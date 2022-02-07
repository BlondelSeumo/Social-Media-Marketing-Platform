<?php
require_once("application/controllers/Home.php");

class Post_planner extends Home
{
    public $addon_data = [];

    /**
     * An array of php file upload errors
     *
     * @var array
     */
    protected $php_file_upload_errors = [
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];    

    public function __construct()
    {
        parent::__construct();
        
        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');

        // path of addon controller
        $addon_path = APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; 

        $this->addon_data = $this->get_addon_data($addon_path);
        
        // Checks member validity
        $this->member_validity();

        // user_id of logged in user, we may need it 
        $this->user_id = $this->session->userdata('user_id'); 
    }

    public function index() 
    {
        $data['body'] = 'post_planner';
        $data['page_title'] = $this->lang->line('Post planner');

        $this->_viewcontroller($data); 
    }

    public function manage_csv_data() 
    {
        $postType = $this->input->post('post_type');

        // Allowed MIME types
        $allowedMimeTypes = [
            // csv
            'text/csv',

            // plain
            'text/plain',

            // failed to determine
            'application/octet-stream',
            'application/csv',
        ];  

        // Starts uploading file
        if (isset($_FILES['post_file'])) {

            $error = $_FILES['post_file']['error'];

            if ($error) {
                $message = isset($this->php_file_upload_errors[$error])
                    ? $this->php_file_upload_errors[$error]
                    : $this->lang->line('Unknown error occurred');
                $data = [
                    'status' => false,
                    'message' => $message,
                ];

                return $this->jsonResponse($data);
            }

            if (is_uploaded_file($_FILES['post_file']['tmp_name'])) {

                $tmpName = $_FILES['post_file']['tmp_name'];
                $postFilename = $_FILES['post_file']['name'];
                $mimeType = mime_content_type($tmpName);

                if(! in_array($mimeType, $allowedMimeTypes)) {
                    $data = [
                        'status' => false,
                        'message' => $this->lang->line("Invalid file uploaded"),
                    ];

                    return $this->jsonResponse($data);
                }

                $filetype = substr($mimeType, 0, 5);
                $extension = mb_substr($postFilename, mb_strrpos($postFilename, '.'));

                // TODO: manipulate csv files

                $tmpArray = [];

                if (false !== ($handle = fopen($tmpName, "r"))) {
                    if (false !== ($data = fgetcsv($handle))) {
                        if(! $this->checkProperHeaderFieldExists($data)) {
                            $message = $this->lang->line("Your CSV file does not contain proper header fields. Required fields are campaign_name, campaign_type, content, and source");
                            $data = [
                                'status' => false,
                                'message' => $message,
                            ];

                            return $this->jsonResponse($data);
                        }

                        $campaign_name_index = array_search('campaign_name', $data);
                        $campaign_type_index = array_search('campaign_type', $data);
                        $message_index = array_search('message', $data);
                        $source_index = array_search('source', $data);

                        $name = $this->lang->line("CSV Post Planner");

                        while(false !== ($data = fgetcsv($handle))) {
                            $campaignType = (isset($data[$campaign_type_index]) && ! empty($data[$campaign_type_index]))
                                ? strtolower($data[$campaign_type_index])
                                : '';
                            $message = (isset($data[$message_index]) && ! empty($data[$message_index]))
                                ? $data[$message_index]
                                : '';

                            if (empty($campaignType) || empty($message)) {
                                continue;
                            } 

                            if (! empty($campaignType) && ! in_array($campaignType, ['text', 'image', 'link'])) {
                                continue;
                            }   

                            $campaignName = (isset($data[$campaign_name_index]) && ! empty($data[$campaign_name_index]))
                                ? $data[$campaign_name_index] 
                                : $name;

                            $rowData = [
                                $campaignName,
                                $campaignType,
                                $message,
                                $data[$source_index],
                            ];

                            array_push($tmpArray, $rowData);
                        }    
                    }
                }

                if (empty($tmpArray)) {
                    $data = [
                        'status' => false,
                        'message' => $this->lang->line('We were not able to extract data from your CSV file'),
                    ];

                    return $this->jsonResponse($data);
                }

                $data = [
                    'status' => true,
                    'message' => $this->lang->line('CSV data has been successfully managed'),
                    'data' => $tmpArray,
                ];

                return $this->jsonResponse($data);

            }
        }
    }

    public function manage_submitted_data() 
    {
        $settingsType = (string) $this->input->post('settingsType');
        $postTimeZone = (string) $this->input->post('postTimeZone', true);
        $csvData = (string) $this->input->post('csvData', true);

        $facebookSelectBox = (string) $this->input->post('facebookSelectBox', true);
        $twitterSelectBox = (string) $this->input->post('twitterSelectBox', true);
        $linkedinSelectBox = (string) $this->input->post('linkedinSelectBox', true);
        $redditSelectBox = (string) $this->input->post('redditSelectBox', true);
        $subredditSelectBox = (string) $this->input->post('subredditSelectBox', true);        

        $facebookSelectedAccouunts = explode(',', $facebookSelectBox);
        $twitterSelectedAccouunts = explode(',', $twitterSelectBox);
        $linkedinSelectedAccouunts = explode(',', $linkedinSelectBox);
        $redditSelectedAccouunts = explode(',', $redditSelectBox);

        $postingMedium = array_merge(
            $facebookSelectedAccouunts,
            $twitterSelectedAccouunts,
            $linkedinSelectedAccouunts,
            $redditSelectedAccouunts
        );

        $postingMedium = array_filter($postingMedium);

        $tableName = 'comboposter_campaigns';
        $csvData = json_decode($csvData, true);

        if (null == $csvData || ! is_array($csvData)) {
            $data = [
                'status' => false,
                'message' => $this->lang->line("You CSV data is not well-formatted"),
            ];

            return $this->jsonResponse($data);
        }

        if (! in_array($postTimeZone, array_keys($this->_time_zone_list()))) {
            $data = [
                'status' => false,
                'message' => $this->lang->line("Invalid timezone provided"),
            ];

            return $this->jsonResponse($data);
        }

        if (empty($postingMedium)) {
            $data = [
                'status' => false,
                'message' => $this->lang->line("Please select at least one social medium"),
            ];

            return $this->jsonResponse($data);
        }

        if ('manual' == $settingsType) {
            $campaignIds = (string) $this->input->post('campaignIds', true);
            $manauSchduleData = (string) $this->input->post('manauSchduleData', true);

            $rowNumbers = explode(',', $campaignIds);
            $selectedDates = explode(',', $manauSchduleData);

            if (count($rowNumbers) != count($selectedDates)) {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("Number of campaign IDs and selected Dates are not equal"),
                ];

                return $this->jsonResponse($data);
            }

            if (count($csvData) != count($rowNumbers)) {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("The number of fields that you have filled in manually and the rows you have in the CSV file are not equal"),
                ];

                return $this->jsonResponse($data);
            }

            $preparedCsvData = $this->prepareManualInsertionData(
                $csvData, 
                [ 
                    'rowNumbers' => $rowNumbers, 
                    'selectedDates' => $selectedDates,
                ]
            );           

            $insertionOptions = [];
            $insertionOptions['timeZone'] = $postTimeZone;
            $insertionOptions['postingMedium'] = $postingMedium;
            $insertionOptions['subreddits'] = $subredditSelectBox;

            $resultOfTextData = 0;
            $resultOfImageData = 0;
            $resultOfLinkData = 0;

            if (! empty($preparedCsvData['text'])) {
                // Runs batch-insert 
                $resultOfTextData = $this->insertData($tableName, $preparedCsvData['text'], $insertionOptions);
            }

            if (! empty($preparedCsvData['link'])) {
                // Runs batch-insert 
                $resultOfLinkData = $this->insertData($tableName, $preparedCsvData['link'], $insertionOptions);
            }

            if (! empty($preparedCsvData['image'])) {
                // Runs batch-insert 
                $resultOfImageData = $this->insertData($tableName, $preparedCsvData['image'], $insertionOptions);
            }

            if ($resultOfTextData || $resultOfImageData || $resultOfLinkData) {
                $data = [
                    'status' => true,
                    'message' => $this->lang->line("We have created following campaign(s) from the CSV file upload"),
                    'data' => [
                        'text' => $resultOfTextData,
                        'link' => $resultOfLinkData,
                        'image' => $resultOfImageData,
                    ],
                ];

                return $this->jsonResponse($data);
            } else {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("We are unable to prepare and save the CSV data that you uploaded"),
                ];

                return $this->jsonResponse($data);
            }

        } elseif ('automatic' == $settingsType) {
            $postStartTime = $this->input->post('postStartTime', true);
            $postEndTime = $this->input->post('postEndTime', true);
            $postInterval = $this->input->post('postInterval', true);
            $postStartDate = $this->input->post('postStartDate', true);
            $postDayOff = $this->input->post('postDayOff', true);
            $recyclePost = $this->input->post('recyclePost', true);

            $postDayOffArray = explode(',', $postDayOff);
            $date = new DateTime($postStartDate);
            $day = $date->format('l');

            if (! $postStartDate) {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("Post start date can not be empty"),
                ];

                return $this->jsonResponse($data);
            }

            // if ($postStartTime >= $postEndTime) {
            //     $data = [
            //         'status' => false,
            //         'message' => $this->lang->line("The start time should be less than the end time"),
            //     ];

            //     return $this->jsonResponse($data);
            // }

            if ($postInterval < 0 || $postInterval > 259200) {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("Post interval must be greater than 0 and less than or equal to 259200"),
                ];

                return $this->jsonResponse($data);
            }

            if (in_array($day, $postDayOffArray)) {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("The start-date is similar to the day(s) that are off"),
                ];

                return $this->jsonResponse($data);
            }

            $preparedCsvData = $this->prepareAutomaticFormSubmittedCsvData($csvData, [
                'postTimeZone' => $postTimeZone,
                'postStartDate' => $postStartDate,
                'postStartTime' => $postStartTime,
                'postEndTime' => $postEndTime,
                'postInterval' => $postInterval,
                'postDayOff' => $postDayOffArray,
                'recyclePost' => $recyclePost,
            ]);

            $insertionOptions = [];
            $insertionOptions['timeZone'] = $postTimeZone;
            $insertionOptions['postingMedium'] = $postingMedium;
            $insertionOptions['subreddits'] = $subredditSelectBox;

            $data = $this->prepareAutomaticInsertionData($preparedCsvData, $insertionOptions);

            $resultOfTextData = 0;
            $resultOfImageData = 0;
            $resultOfLinkData = 0;

            if (! empty($data['text'])) {
                $resultOfTextData = $this->insertBatchData($tableName, $data['text']);
            }

            if (! empty($data['link'])) {
                $resultOfLinkData = $this->insertBatchData($tableName, $data['link']);
            }

            if (! empty($data['image'])) {
                $resultOfImageData = $this->insertBatchData($tableName, $data['image']);
            }            

            if ($resultOfTextData || $resultOfImageData || $resultOfLinkData) {
                $data = [
                    'status' => true,
                    'message' => $this->lang->line("We have created following campaign(s) from the CSV file upload"),
                    'data' => [
                        'text' => $resultOfTextData,
                        'link' => $resultOfLinkData,
                        'image' => $resultOfImageData,
                    ],
                ];

                return $this->jsonResponse($data);
            } else {
                $data = [
                    'status' => false,
                    'message' => $this->lang->line("We are unable to prepare and save the CSV data that you uploaded"),
                ];

                return $this->jsonResponse($data);
            }
        }
    }

    /**
     * Checks whether any property header field exists or not
     *
     * @param array $data First array data from a CSV file
     * @return bool
     */
    private function checkProperHeaderFieldExists($data) 
    {
        $campaign_name_index = array_search('campaign_name', $data);
        $campaign_type_index = array_search('campaign_type', $data);
        $message_index = array_search('message', $data);
        $source_index = array_search('source', $data);

        return (false === $campaign_name_index
            || false === $campaign_type_index
            || false === $message_index
            || false === $source_index
        ) ? false : true;
    }

    /**
     * Prepares form data submitted manually
     *
     * @param $csvData array
     * @param $options array ['rowNumbers' => [], 'selectedDates' => []]
     * @return array
     */
    private function prepareManualInsertionData($csvData, $options) {
        $campaignsContainer = [
            'text' => [],
            'link' => [],
            'image' => [],
        ];

        foreach ($options['rowNumbers'] as $key => $val) {
            if (isset($options['selectedDates'][$key]) && isset($csvData[$val])) {
                $campaign = $csvData[$val];
                $campaignType = isset($campaign[1]) ? $campaign[1] : null;

                array_push($campaignsContainer[$campaignType], [
                    $options['selectedDates'][$key] => $campaign,
                ]);
            }
        }

        return $campaignsContainer;
    }

    /**
     * Prepares form data submitted with "automatic" type
     *
     * @param $csvData array
     * @param $options array ['timeZone'=> 'string', postStartDate' => 'string', 'postStartTime' => 'string', 'postEndTime' => 'string', 'postInterval' => 'int', 'postDayOff' => []]
     * @return array
     */
    private function prepareAutomaticFormSubmittedCsvData($csvData, $options) {
        $postTimeZone = (isset($options['postTimeZone']) && ! empty($options['postTimeZone'])) 
            ? trim($options['postTimeZone']) 
            : '';
        $postStartDate = (isset($options['postStartDate']) && strtotime($options['postStartDate'])) 
            ? trim($options['postStartDate']) 
            : date('Y-m-d');
        $postStartTime = (isset($options['postStartTime']) && ! empty($options['postStartTime'])) 
            ? trim($options['postStartTime']) 
            : '00:00';
        $postEndTime = (isset($options['postEndTime']) && ! empty($options['postEndTime'])) 
            ? trim($options['postEndTime']) 
            : '23:00';
        $postInterval = (isset($options['postInterval']) 
            && $options['postInterval'] > 0 
            && $options['postInterval'] < 259200) 
                ? (int) $options['postInterval']
                : 10;
        $postDayOff = (isset($options['postDayOff']) && is_array($options['postDayOff'])) 
            ? $options['postDayOff']
            : [];
        $recyclePost = (isset($options['recyclePost']) && $options['recyclePost'] > 0) 
            ? (int) $options['recyclePost']
            : 1;

        // Ensures valid date formats from string
        $startDateTime = "$postStartDate $postStartTime:00";

        if (! strtotime($startDateTime)) {
            $startDateTime = "$postStartDate $postStartTime:00";
        }

        $endDateTime = "$postStartDate $postEndTime:00";

        if (! strtotime($endDateTime)) {
            $endDateTime = "$postStartDate $postEndTime:00";
        }
    
        // Fixes timezone
        if (empty($postTimeZone)) {
            $defaultTimeZone = $this->config->item('time_zone');
            
            if (empty($defaultTimeZone)) {
                $postTimeZone = 'Europe/Dublin';
            } else {
                $postTimeZone = $defaultTimeZone;
            }
        }

        // Modifies server timezone
        date_default_timezone_set($postTimeZone);

        $totalItem = count($csvData) * $recyclePost;
        $periodDesignator = "PT{$postInterval}M";

        $date_planner_time_list = $this->get_between_date_planner($totalItem,$periodDesignator,$postStartDate,$postStartTime,$postEndTime,$postDayOff);
     
        // Holds formatted data
        $no_data = 0;
        $previousDate = '';
        $preparedCsvData = [];

        for ($i = 0; $i < $recyclePost; $i++) {
            // if needed, on some reason, sets the array pointer back to first element
            reset($csvData);

            foreach ($csvData as $val) {
                if (! isset($date_planner_time_list[$no_data])) {
                    break;
                }

                $dateFormat = $date_planner_time_list[$no_data];
                $preparedCsvData[$dateFormat] = $val;
                $no_data++;
            }
        }

        return $preparedCsvData;
    }

    private function prepareAutomaticInsertionData($preparedCsvData, $options) 
    {
        $campaignsContainer = [
            'text' => [],
            'link' => [],
            'image' => [],
        ];

        foreach ($preparedCsvData as $dateTime => $campaign) {
            $options['dateTime'] = $dateTime;
            $campaignType = isset($campaign[1]) ? $campaign[1] : '';

            $data = $this->prepareData($campaign, $options);
            array_push($campaignsContainer[$campaignType], $data);
        }

        return $campaignsContainer;
    }

    private function insertData($tableName, $data, $options) 
    {  
        $insertId = 0;
        $firstInsertion = 0;
        $insertionTimes = 0;

        foreach ($data as $index) {
            foreach ($index as $dateTime => $campaign) {
                $options['dateTime'] = $dateTime;
                $data = $this->prepareData($campaign, $options);

                if ($insertId > 0) {
                    $data['is_child'] = '1';
                    $data['parent_campaign_id'] = $insertId;
                }

                $this->basic->insert_data($tableName, $data);

                if ($this->db->affected_rows()) {
                    if (0 == $firstInsertion) {
                        $insertId = $this->db->insert_id();
                        $firstInsertion++;
                    }

                    $insertionTimes++;
                }
            }
        }      

        return $insertionTimes;
    }

    private function insertBatchData($tableName, $data) 
    {
        $this->basic->insert_data($tableName, $data[0]);

        if ($this->db->affected_rows()) {
            $insertId = $this->db->insert_id();
            unset($data[0]);

            if (empty($data)) {
                return 1;
            }

            array_walk($data, function(&$key) use($insertId) {
                $key['is_child'] = '1';
                $key['parent_campaign_id'] = $insertId;
            });
        }

        // Runs batch-insert 
        $affectedRows = $this->db->insert_batch($tableName, $data);

        if (is_int($affectedRows)) {
            $affectedRows += 1;
        }
        
        return $affectedRows;
    }

    private function prepareData($campaign, $options) 
    {
        $campaignName = $campaign[0];
        $campaignType = $campaign[1];

        $data = [
            'user_id' => $this->user_id,
            'campaign_type' => $campaignType,
            'campaign_name' => $campaignName,
            'subreddits' => isset($options['subreddits']) ? $options['subreddits'] : '',
            'message' => isset($campaign[2]) ? $campaign[2] : '',
        
            'schedule_type' => 'now',
            'schedule_time' => isset($options['dateTime']) ? $options['dateTime'] : '',
            'schedule_timezone' => isset($options['timeZone']) ? $options['timeZone'] : '',

            'posting_medium' => isset($options['postingMedium']) ? json_encode($options['postingMedium']) : '',
            'image_url' => ('image' == $campaignType && isset($campaign[3])) ? $campaign[3] : '',
            'link' => ('link' == $campaignType && isset($campaign[3])) ? $campaign[3] : '',

            'is_child' => '0',
            'parent_campaign_id' => 'null',
        ];

        return $data;
    }

    public function campaign_settings()
    {
        $this->ajax_check();

        $this->is_ultrapost_exist = $this->ultrapost_exist();

        if(! $this->is_ultrapost_exist) {
            $data = [
                'status' => false,
                'message' => $this->lang->line("Access forbidden : you do not have access to publish module. Please contact application admin to get access."),
            ];

            return $this->jsonResponse($data); 
        }

        $fb_page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where" => array("facebook_rx_fb_page_info.user_id" => $this->user_id)),array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name"),array('facebook_rx_fb_user_info' => "facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left"));

        $twitter_accounts = $this->basic->get_data('twitter_users_info', array('where' => array('user_id' => $this->user_id)), array('id', 'name'));
        $linkedin_accounts = $this->basic->get_data('linkedin_users_info', array('where' => array('user_id' => $this->user_id)), array('id', 'name'));
        $reddit_accounts = $this->basic->get_data('reddit_users_info', array('where' => array('user_id' => $this->user_id)), array('id', 'username'));
        
        $subreddits = $this->subRedditList();
        $timezones = $this->_time_zone_list();
        
        $data = [
            'status' => true,
            'facebook_accounts' => $fb_page_info,
            'twitter_accounts' => $twitter_accounts,
            'linkedin_accounts' => $linkedin_accounts,
            'reddit_accounts' => $reddit_accounts,
            'subreddits' => $subreddits,
            'timezones' => $timezones,
            'defaultTimeZone' => $this->config->item('time_zone'),
        ];

        return $this->jsonResponse($data);
    }

    public function subRedditList()
    {
        $subRedditListArr = array(
            "1200isplenty" => "1200isplenty",
            "AdventureTime" => "AdventureTime",
            "Android" => "Android",
            "Android" => "Android",
            "Animated" => "Animated",
            "Anxiety" => "Anxiety",
            "ArcherFX" => "ArcherFX",
            "ArtPorn" => "ArtPorn",
            "AskReddit" => "AskReddit",
            "AskScience" => "AskScience",
            "AskScience" => "AskScience",
            "AskSocialScience" => "AskSocialScience",
            "Baseball" => "Baseball",
            "BetterCallSaul" => "BetterCallSaul",
            "Bitcoin" => "Bitcoin",
            "Bitcoin" => "Bitcoin",
            "BobsBurgers" => "BobsBurgers",
            "Books" => "Books",
            "BreakingBad" => "BreakingBad",
            "Cheap_Meals" => "Cheap_Meals",
            "Classic4chan" => "Classic4chan",
            "DeepIntoYouTube" => "DeepIntoYouTube",
            "Discussion" => "Discussion",
            "DnD" => "DnD",
            "Doctor Who" => "Doctor Who",
            "DoesAnybodyElse" => "DoesAnybodyElse",
            "DunderMifflin" => "DunderMifflin",
            "EDC" => "EDC",
            "Economics" => "Economics",
            "Entrepreneur" => "Entrepreneur",
            "FlashTV" => "FlashTV",
            "FoodPorn" => "FoodPorn",
            "General" => "General",
            "General" => "General",
            "General" => "General",
            "General" => "General",
            "General" => "General",
            "HistoryPorn" => "HistoryPorn",
            "Hockey" => "Hockey",
            "HybridAnimals" => "HybridAnimals",
            "IASIP" => "IASIP",
            "Jokes" => "Jokes",
            "LetsNotMeet" => "LetsNotMeet",
            "LifeProTips" => "LifeProTips",
            "Lifestyle" => "Lifestyle",
            "Linux" => "Linux",
            "LiverpoolFC" => "LiverpoolFC",
            "Netflix" => "Netflix",
            "Occupation" => "Occupation",
            "Offensive_Wallpapers" => "Offensive_Wallpapers",
            "OutOfTheLoop" => "OutOfTheLoop",
            "PandR" => "PandR",
            "Pokemon" => "Pokemon",
            "Seinfeld" => "Seinfeld",
            "Sherlock" => "Sherlock",
            "Soccer" => "Soccer",
            "Sound" => "Sound",
            "SpacePorn" => "SpacePorn",
            "Television" => "Television",
            "TheSimpsons" => "TheSimpsons",
            "Tinder" => "Tinder",
            "TrueDetective" => "TrueDetective",
            "UniversityofReddit" => "UniversityofReddit",
            "YouShouldKnow" => "YouShouldKnow",
            "advice" => "advice",
            "amiugly" => "amiugly",
            "anime" => "anime",
            "apple" => "apple",
            "aquariums" => "aquariums",
            "askculinary" => "askculinary",
            "askengineers" => "askengineers",
            "askengineers" => "askengineers",
            "askhistorians" => "askhistorians",
            "askmen" => "askmen",
            "askphilosophy" => "askphilosophy",
            "askwomen" => "askwomen",
            "bannedfromclubpenguin" => "bannedfromclubpenguin",
            "batman" => "batman",
            "battlestations" => "battlestations",
            "bicycling" => "bicycling",
            "bigbrother" => "bigbrother",
            "biology" => "biology",
            "blackmirror" => "blackmirror",
            "blackpeoplegifs" => "blackpeoplegifs",
            "budgetfood" => "budgetfood",
            "business" => "business",
            "casualiama" => "casualiama",
            "celebs" => "celebs",
            "changemyview" => "changemyview",
            "changemyview" => "changemyview",
            "chelseafc" => "chelseafc",
            "chemicalreactiongifs" => "chemicalreactiongifs",
            "chemicalreactiongifs" => "chemicalreactiongifs",
            "chemistry" => "chemistry",
            "coding" => "coding",
            "college" => "college",
            "comics" => "comics",
            "community" => "community",
            "confession" => "confession",
            "cooking" => "cooking",
            "cosplay" => "cosplay",
            "cosplay" => "cosplay",
            "crazyideas" => "crazyideas",
            "cyberpunk" => "cyberpunk",
            "dbz" => "dbz",
            "depression" => "depression",
            "doctorwho" => "doctorwho",
            "education" => "education",
            "educationalgifs" => "educationalgifs",
            "engineering" => "engineering",
            "entertainment" => "entertainment",
            "environment" => "environment",
            "everymanshouldknow" => "everymanshouldknow",
            "facebookwins" => "facebookwins",
            "facepalm" => "facepalm",
            "facepalm" => "facepalm",
            "fantasy" => "fantasy",
            "fantasyfootball" => "fantasyfootball",
            "firefly" => "firefly",
            "fitmeals" => "fitmeals",
            "flexibility" => "flexibility",
            "food" => "food",
            "funny" => "funny",
            "futurama" => "futurama",
            "gadgets" => "gadgets",
            "gallifrey" => "gallifrey",
            "gamedev" => "gamedev",
            "gameofthrones" => "gameofthrones",
            "geek" => "geek",
            "gentlemanboners" => "gentlemanboners",
            "gifs" => "gifs",
            "girlsmirin" => "girlsmirin",
            "google" => "google",
            "gravityfalls" => "gravityfalls",
            "gunners" => "gunners",
            "hardbodies" => "hardbodies",
            "hardware" => "hardware",
            "harrypotter" => "harrypotter",
            "history" => "history",
            "hockey" => "hockey",
            "houseofcards" => "houseofcards",
            "howto" => "howto",
            "humor" => "humor",
            "investing" => "investing",
            "japanesegameshows" => "japanesegameshows",
            "keto" => "keto",
            "ketorecipes" => "ketorecipes",
            "languagelearning" => "languagelearning",
            "law" => "law",
            "learnprogramming" => "learnprogramming",
            "lectures" => "lectures",
            "lego" => "lego",
            "lifehacks" => "lifehacks",
            "linguistics" => "linguistics",
            "literature" => "literature",
            "loseit" => "loseit",
            "magicTCG" => "magicTCG",
            "marvelstudios" => "marvelstudios",
            "math" => "math",
            "mrrobot" => "mrrobot",
            "mylittlepony" => "mylittlepony",
            "naruto" => "naruto",
            "nasa" => "nasa",
            "nbastreams" => "nbastreams",
            "nosleep" => "nosleep",
            "nutrition" => "nutrition",
            "olympics" => "olympics",
            "onepunchman" => "onepunchman",
            "paleo" => "paleo",
            "patriots" => "patriots",
            "pettyrevenge" => "pettyrevenge",
            "photoshop" => "photoshop",
            "physics" => "physics",
            "pics" => "pics",
            "pizza" => "pizza",
            "podcasts" => "podcasts",
            "poetry" => "poetry",
            "pokemon" => "pokemon",
            "preppers" => "preppers",
            "prettygirls" => "prettygirls",
            "psychology" => "psychology",
            "python" => "python",
            "quotes" => "quotes",
            "rainmeter" => "rainmeter",
            "rateme" => "rateme",
            "reactiongifs" => "reactiongifs",
            "recipes" => "recipes",
            "reddevils" => "reddevils",
            "relationship_advice" => "relationship_advice",
            "rickandmorty" => "rickandmorty",
            "running" => "running",
            "samplesize" => "samplesize",
            "scifi" => "scifi",
            "screenwriting" => "screenwriting",
            "seinfeld" => "seinfeld",
            "self" => "self",
            "sex" => "sex",
            "shield" => "shield",
            "simpleliving" => "simpleliving",
            "soccer" => "soccer",
            "southpark" => "southpark",
            "stockmarket" => "stockmarket",
            "stocks" => "stocks",
            "talesfromtechsupport" => "talesfromtechsupport",
            "tattoos" => "tattoos",
            "teachers" => "teachers",
            "thewalkingdead" => "thewalkingdead",
            "thinspo" => "thinspo",
            "tinyhouses" => "tinyhouses",
            "todayilearned" => "todayilearned",
            "topgear" => "topgear",
            "tumblr" => "tumblr",
            "twinpeaks" => "twinpeaks",
            "twitch" => "twitch",
            "vandwellers" => "vandwellers",
            "vegan" => "vegan",
            "videos" => "videos",
            "wallpaper" => "wallpaper",
            "weathergifs" => "weathergifs",
            "westworld" => "westworld",
            "whitepeoplegifs" => "whitepeoplegifs",
            "wikileaks" => "wikileaks",
            "wikipedia" => "wikipedia",
            "woodworking" => "woodworking",
            "writing" => "writing",
            "wwe" => "wwe",
            "youtube" => "youtube",
            "youtubehaiku" => "youtubehaiku",
            "zombies" => "zombies"
        );

        $subRedditListArr = array_unique($subRedditListArr);

        return $subRedditListArr;
    }

    private function jsonResponse($data)
    {
        if (! headers_sent()) {
            header('Content-Type: application/json');
        }

        echo json_encode($data);

        exit;
    }    

    private function get_between_date_planner($n,$interval,$start_date,$start_time,$end_time,$off_day){

        $response=array();
        $start_date_time= $start_date_time =new dateTime($start_date.' '.$start_time); 

        for($i=0; $i<=$n; $i++){
            $start_hour = $start_date_time->format('H:i');
            if(strtotime($start_hour)<strtotime($start_time)){  
                $start_date_time_temp   =   $start_date_time->format('Y-m-d');
                $start_date_time=$this->assign_begining_time($start_date_time_temp,$start_time);
            }
            
            else if(strtotime($start_hour) > strtotime($end_time)){

                $start_date_time=$start_date_time->add(new DateInterval("P1D"));
                $start_date_time_temp   =   $start_date_time->format('Y-m-d');
                $start_date_time=$this->assign_begining_time($start_date_time_temp,$start_time);
            }

            $day = $start_date_time->format('l');

            for($k=0;$k<6;$k++){
                if(in_array($day,$off_day)){
                    $start_date_time=$start_date_time->add(new DateInterval("P1D"));
                    $start_date_time_temp   =   $start_date_time->format('Y-m-d');
                    $start_date_time=$this->assign_begining_time($start_date_time_temp,$start_time);
                    $day = $start_date_time->format('l'); 
                }
                else {
                    $response [] = $start_date_time->format('Y-m-d H:i:s');
                    break;
                }
            }

            $start_date_time=$start_date_time->add(new DateInterval($interval));
        }

        return $response;
    }

    private function assign_begining_time($start_date_time_temp,$start_time){
        $start_date_time_temp= $start_date_time_temp. " ". $start_time;
        $start_date_time=new dateTime($start_date_time_temp); 
        $start_date_time->format('Y-m-d H:i:s.u'); 
        return $start_date_time;
    }
}