<?php

require_once('Google_My_Business/autoload.php');
require_once('Google_My_Business/Client.php');
require_once('Google_My_Business/Service/MyBusiness.php');
require_once('Google_My_Business/Service/Oauth2.php');

class Google_my_business
{
    /**
     * {@code "offline"} to request offline access from the user.
     */
    const ACCESS_TYPE_OFFLINE = 'offline';

    /**
     * {@code "online"} to request online access from the user.
     */
    const ACCESS_TYPE_ONLINE = 'online';

    /**
     * Force approval. Can get refresh token
     */
    const APPROVAL_PROMPT_FORCE = 'force';

    /**
     * Auto login, Can't get refresh token after first login.
     */
    const APPROVAL_PROMPT_AUTO = 'auto';

    /**
     * OAuth2 endpoint
     */
    const GOOGLE_OAUTH2_ENDPOINT = 'https://accounts.google.com/o/oauth2/auth';

    /**
     * @var string
     */
    public $clientId;

    /**
     * @var string
     */
    public $clientSecret;

    /**
     * @var string
     */
    public $redirectUri;

    /**
     * @var string|json
     */
    public $accessToken;

    /**
     * @var array
     */
    public $scopes = [
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/business.manage',
    ];

    /**
     * Holds instance of Google Client
     *
     * @var \Google\Google_Client
     */
    public $client;

    /**
     * Holds instance of Google_Service_Oauth2
     *
     * @var \Google\Google_Service_Oauth2
     */
    public $oauth;

    /**
     * Holds instance of Google_Service_MyBusiness
     *
     * @var \Google_Service_MyBusiness
     */
    public $myBusiness;

    /**
     * Holds user ID
     *
     * @var string
     */
    public $userId;

    public $google_mybusiness_user_table_id;

    /**
     * CTA action type
     *
     * @var array
     */
    public $actionType = [
        'BOOK',
        'ORDER',
        'SHOP',
        'LEARN_MORE',
        'SIGN_UP',
        'CALL'
    ];

    /**
     * Google_my_business constructor.
     */
    public function __construct($params=[])
    {
        // Gets instance of Codeigniter
        $this->CI =& get_instance();

        // Loads database
        $this->CI->load->database();

        // Loads BASIC model
        $this->CI->load->model('basic');

        // Loads URL helper
        $this->CI->load->helper('url_helper');

        // Loads session
        $this->CI->load->library('session');


        $this->initializeGclient($params);
    }

    public function initializeGclient($params = []) 
    {
        // Sets user ID
        // $this->userId = $this->CI->session->userdata('user_id');
        $this->google_mybusiness_user_table_id = '';
        $this->userId = '';

        $this->google_mybusiness_user_table_id = $this->CI->session->userdata('google_mybusiness_user_table_id');

        if(empty($this->google_mybusiness_user_table_id) && isset($params['gmb_user_table_id']))
            $this->google_mybusiness_user_table_id = $params['gmb_user_table_id'];

        if(!empty($this->google_mybusiness_user_table_id))
        {
            $user_info = $this->CI->basic->get_data('google_user_account',['where'=>['id'=>$this->google_mybusiness_user_table_id]],['user_id']);
            $this->userId = $user_info[0]['user_id'];
        }

        // Gets OAuth2 parameters  
        $data = $this->getOAuth2Params($this->google_mybusiness_user_table_id);

        if (! count($data)) {
            throw new \Exception('Client ID and Client secret are not found.');
        }

        $this->clientId = isset($data['google_client_id']) ? trim($data['google_client_id']) : null;
        $this->clientSecret = isset($data['google_client_secret']) ? trim($data['google_client_secret']) : null;

        if (! empty($params['redirectUri'])) {
            $this->redirectUri = $params['redirectUri'];
        } else {
            // $this->redirectUri = 'http://localhost/google_login/gmb/gmb.php';
            $this->redirectUri = base_url('social_accounts/import_gmb_account_callback');
        }

        $this->accessToken = isset($data['access_token']) ? trim($data['access_token']) : null;

        // Creates google my business object
        $this->client = $this->bootGoogleClient();

        $this->myBusiness = new Google_Service_MyBusiness($this->client);

        // Creates Google_Service_Oauth2 object
        $this->oauth = new Google_Service_Oauth2($this->client);

    }

    /**
     * Bootstraps Google_Client
     *
     * @return Google_Client
     */
    protected function bootGoogleClient()
    {
        // Bootstraps Google_Client
        $client = new Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->addScope($this->scopes);
        $client->setAccessType(self::ACCESS_TYPE_OFFLINE);
        $client->setRedirectUri($this->redirectUri);

        if (null != $this->accessToken) {
            $client->setAccessToken($this->accessToken);
        }

        // Sets new access token if the previous one is expired
        if ($this->accessToken && $client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();

            $client->refreshToken($refreshToken);
            $accessToken = $client->getAccessToken();

            // Updates access token
            try {
                $this->updateAccessToken($accessToken);
            } catch (\Exception $e) {
                log_message('error', 'Could not update access token.');
            }
        }

        return $client;
    }    

    /**
     * Updates access token
     *
     * @param string $accessToken
     * @throws Exception
     */
    protected function updateAccessToken($accessToken)
    {
        $accessTokenValidity = json_decode($accessToken, true);
        if (! is_array($accessTokenValidity) || null == $accessTokenValidity) {
            throw new Exception('Invalid access token provided to be saved in database.');
        }

        $data = [
            'access_token' => $accessToken
        ];

        $where = [
            'id' =>  $this->google_mybusiness_user_table_id,
            'user_id' => $this->userId
        ];

        $this->CI->basic->update_data('google_user_account', $where, $data);
    }

    /**
     * Responsible for getting OAuth2 params
     *
     * @return array|null
     */
    protected function getOAuth2Params($google_mybusiness_user_table_id)
    {
        if($google_mybusiness_user_table_id)
        {
            $table_name = 'google_user_account';
            $select = [
                'login_config.id',
                'login_config.google_client_id',
                'login_config.google_client_secret',
                'google_user_account.access_token'
            ];

            $where = [
                'where' => [
                    'google_user_account.id' => $google_mybusiness_user_table_id,
                ]
            ];

            $join = [
                'login_config' => 'google_user_account.app_config_id=login_config.id,left'
            ];

        }
        else
        {
            $table_name = 'login_config';
            $select = [
                'login_config.id',
                'login_config.google_client_id',
                'login_config.google_client_secret'
            ];

            $where = [
                'where' => [
                    'login_config.status' => '1',
                ]
            ];

            $join = [];
        }

        $result = $this->CI->basic->get_data($table_name, $where, $select, $join, 1);

        $this->CI->session->set_userdata('gmb_config_table_id', $result[0]['id']);

        return isset($result[0]) ? $result[0] : null;
    }

    public function login_button()
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $this->scopes),
            'access_type' => self::ACCESS_TYPE_OFFLINE,
            'approval_prompt' => self::APPROVAL_PROMPT_FORCE,
        ];

        $login_url = self::GOOGLE_OAUTH2_ENDPOINT . '?' . http_build_query($params);
        return 
        '<a id="gSignInWrapper" href="'.$login_url.'"><div id="customBtn" class="customGPlusSignIn"><span class="icon"></span><span class="buttonText">'.$this->CI->lang->line("Import Google My Business Account").'</span></div></a>';
    }

    /**
     * Returns account information
     *
     * @return Google_Service_MyBusiness_ListAccountsResponse
     */
    public function accountInformation()
    {
        $accounts = $this->myBusiness->accounts;
        return $accounts->listAccounts();
    }

    /**
     * Lists media based on location
     *
     * @param string $name
     * For Example: accounts/{numbers}/locations/{numbers}
     *
     * @return Google_Service_MyBusiness_ListMediaItemsResponse
     */
    public function list_media($name)
    {
        $media = $this->myBusiness->accounts_locations_media;
        return $media->listAccountsLocationsMedia($name);
    }

    /**
     * Retrieves accounts locations list
     *
     * @param $name Account name. For example: accounts/numbers
     * @return Google_Service_MyBusiness_ListLocationsResponse
     */
    public function listAccountsLocations($name)
    {
        $locations = $this->myBusiness->accounts_locations;
        return $locations->listAccountsLocations($name);
    }

    /**
     * Retrieves posts list
     *
     * @param $parent Account name and location name.
     * For example: accounts/{numbers}/locations/{numbers}
     * @return Google_Service_MyBusiness_ListLocalPostsResponse
     */
    public function postsList($parent)
    {
        $postsList = $this->myBusiness->accounts_locations_localPosts;
        return $postsList->listAccountsLocationsLocalPosts($parent);
    }

    /**
     * Retrieves reviews list
     *
     * @param $parent Account name and location name.
     * For example: accounts/{numbers}/locations/{numbers}
     * @return Google_Service_MyBusiness_ListReviewsResponse
     */
    public function reviewsList($parent)
    {
        $reviewsList = $this->myBusiness->accounts_locations_reviews;
        return $reviewsList->listAccountsLocationsReviews($parent);
    }

    /**
     * Retrieves questions list
     *
     * @param $parent Account name and location name.
     * For example: accounts/{numbers}/locations/{numbers}
     * @return Google_Service_MyBusiness_ListQuestionsResponse
     */
    public function questionsList($parent)
    {
        $questionsList = $this->myBusiness->accounts_locations_questions;
        return $questionsList->listAccountsLocationsQuestions($parent);
    }

    /**
     * Retrieve questions' answers list
     *
     * @param $parent Account name, location name and question key.
     * For example: accounts/{numbers}/locations/{numbers}/questions/{key}
     * @return Google_Service_MyBusiness_ListAnswersResponse
     */
    public function questionsAnswersList($parent)
    {
        $questionsAnswersList = $this->myBusiness->accounts_locations_questions_answers;
        return $questionsAnswersList->listAccountsLocationsQuestionsAnswers($parent);
    }

    /**
     * Creates a call-to-action post
     *
     * @param string $parent accounts/{numbers}/locations/{numbers}
     * @param string $actionType The post type
     * For Example: "BOOK|ORDER|SHOP|LEARN_MORE|SIGN_UP|CALL"
     *
     * @param string $url The URL where the user will be redirected
     * @param string $summary Title for the CTA post
     * @param array $media An array of media items
     * For example: [
     *      [
     *          'mediaFormat' => 'PHOTO|VIDEO'
     *          'sourceUrl' => 'URL for the media'
     *      ],
     * ]
     * @param string $language
     * @return Google_Service_MyBusiness_LocalPost
     * @throws Exception
     */
    public function callToActionPost(
        string $parent,
        string $actionType,
        string $summary,
        array $media = null,
        string $url = null,
        string $language = 'en-US'
    ) {

        if (! preg_match('~accounts/[\d]+/locations/[\d]+~', $parent, $matches)) {
            throw new \Exception('Invalid location id provided');
        }

        if (! in_array($actionType, $this->actionType)) {
            throw new \Exception('Invalid CTA action type provided');
        }

        if (! empty($url) && ! filter_var($url, FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)) {
            throw new \Exception('CTA URL must be a valid URL');
        }

        if (! filter_var($summary, FILTER_SANITIZE_STRING)) {
            throw new \Exception('Summary contains illegal chars');
        }

        if (null != $media) {
            if (! isset($media[0]['mediaFormat']) && ! isset($media[0]['sourceUrl'])) {
                throw new \Exception('mediaFormat and sourceUrl keys are necessary');
            }

            if ('PHOTO' != $media[0]['mediaFormat']
                && ! filter_var($media[0]['sourceUrl'], FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)
            ) {
                throw new \Exception('Provide a valid media type or url');
            }

            $extension = mb_substr($media[0]['sourceUrl'], mb_strrpos($media[0]['sourceUrl'], '.'));
            if (! in_array($extension, ['.jpeg', '.jpg', '.png', '.gif'])) {
                throw new \Exception('Invalid media provided. Supported media is jpeg, jpg, png, gif');
            }
        }

        if (! preg_match('~[a-z]{2}\-[A-Z]{2}~', $language, $matches)) {
            throw new \Exception('Provide a valid language');
        }

        $callToAction = new Google_Service_MyBusiness_CallToAction;
        $callToAction->setActionType($actionType);
        if ('call' != strtolower($actionType)) {
            $callToAction->setUrl($url);
        }

        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->getLanguageCode($language);
        $localPost->setCallToAction($callToAction);
        $localPost->setSummary($summary);

        if (null != $media) {
            $localPost->setMedia($media);
        }

        $callToActionPost = $this->myBusiness->accounts_locations_localPosts;
        return $callToActionPost->create($parent, $localPost);
    }

    /**
     * Creates an event post
     *
     * @param string $parent Account name and location name
     * For example: accounts/{numbers}/locations/{numbers}
     *
     * @param string $title Event name (max 140 chars)
     * @param string $summary Description for the event
     * @param DateTime $startDateTime
     * @param DateTime $endDateTime
     * @param array $media An array media items
     * For example: [
     *      [
     *          'mediaFormat' => 'PHOTO|VIDEO'
     *          'sourceUrl' => 'URL for the media'
     *      ],
     * ]
     *
     * @param string $language
     * @return Google_Service_MyBusiness_LocalPost
     * @throws Exception
     */
    public function eventPost(
        string $parent,
        string $title,
        string $summary,
        \DateTime $startDateTime,
        \DateTime $endDateTime,
        array $media,
        $language = 'en-US'
    ) {

        if (! preg_match('~accounts/[\d]+/locations/[\d]+~', $parent, $matches)) {
            throw new \Exception('Invalid location id provided');
        }

        if (! filter_var($title, FILTER_SANITIZE_STRING)) {
            throw new \Exception('Title contains illegal chars');
        }

        if (! filter_var($summary, FILTER_SANITIZE_STRING)) {
            throw new \Exception('Summary contains illegal chars');
        }

        if (! isset($media[0]['mediaFormat']) && ! isset($media[0]['sourceUrl'])) {
            throw new \Exception('mediaFormat and sourceUrl keys are necessary');
        }

        if ('PHOTO' != $media[0]['mediaFormat']
            && ! filter_var($media[0]['sourceUrl'], FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)
        ) {
            throw new \Exception('Provide a valid media type or URL');
        }

        $extension = substr($media[0]['sourceUrl'], strrpos($media[0]['sourceUrl'], '.'));
        if (! in_array($extension, ['.jpeg', '.jpg', '.png', '.gif'])) {
            throw new \Exception('Invalid media provided. Supported media is jpeg, jpg, png, gif');
        }

        if (! preg_match('~[a-z]{2}\-[A-Z]{2}~', $language, $matches)) {
            throw new \Exception('Provide a valid language');
        }

        $startYear = $startDateTime->format('Y');
        $startMonth = $startDateTime->format('m');
        $startDay = $startDateTime->format('d');
        $startHours = $startDateTime->format('H');
        $startMinutes = $startDateTime->format('i');
        $startSeconds = $startDateTime->format('s');

        $endYear = $endDateTime->format('Y');
        $endMonth = $endDateTime->format('m');
        $endDay = $endDateTime->format('d');
        $endHours = $endDateTime->format('H');
        $endMinutes = $endDateTime->format('i');
        $endSeconds = $endDateTime->format('s');

        // Prepares data and time
        $startDate = new Google_Service_MyBusiness_Date;
        $startDate->setYear($startYear);
        $startDate->setMonth($startMonth);
        $startDate->setDay($startDay);

        $endDate = new Google_Service_MyBusiness_Date;
        $endDate->setYear($endYear);
        $endDate->setMonth($endMonth);
        $endDate->setDay($endDay);

        $startTime = new Google_Service_MyBusiness_TimeOfDay;
        $startTime->setHours($startHours);
        $startTime->setMinutes($startMinutes);
        $startTime->setSeconds($startSeconds);

        $endTime = new Google_Service_MyBusiness_TimeOfDay;
        $endTime->setHours($endHours);
        $endTime->setMinutes($endMinutes);
        $endTime->setSeconds($endSeconds);

        // Prepares schedule
        $schedule = new Google_Service_MyBusiness_TimeInterval;
        $schedule->setStartDate($startDate);
        $schedule->setEndDate($endDate);
        $schedule->setStartTime($startTime);
        $schedule->setEndTime($endTime);

        // Prepares local-post event
        $localPostEvent = new Google_Service_MyBusiness_LocalPostEvent;
        $localPostEvent->setTitle($title);
        $localPostEvent->setSchedule($schedule);

        // Prepares local post
        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->getLanguageCode($language);
        $localPost->setEvent($localPostEvent);
        $localPost->setSummary($summary);
        $localPost->setMedia($media);

        // Creates event post
        $eventPost = $this->myBusiness->accounts_locations_localPosts;
        return $eventPost->create($parent, $localPost);
    }

    /**
     * Creates an offer post
     *
     * @param string $parent Account name and location name
     * For example: accounts/{numbers}/locations/{numbers}
     *
     * @param string $couponCode
     * @param string $redeemUrl
     * @param string $summary
     * @param array $media An array media items
     * For example: [
     *      [
     *          'mediaFormat' => 'PHOTO|VIDEO'
     *          'sourceUrl' => 'URL for the media'
     *      ],
     * ]
     *
     * @param string $language
     * @return Google_Service_MyBusiness_LocalPost
     * @throws Exception
     */
    public function offerPost(
        string $parent,
        string $couponCode,
        string $redeemUrl,
        string $summary,
        array $media,
        $language = 'en-US'
    ) {
        if (! preg_match('~accounts/[\d]+/locations/[\d]+~', $parent, $matches)) {
            throw new \Exception('Invalid location id provided');
        }

        if (! preg_match('~[a-zA-Z0-9]+~', $couponCode, $matches)) {
            throw new \Exception('Coupon code allows only alphanumeric chars.');
        }

        if (! filter_var($redeemUrl, FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)) {
            throw new \Exception('Provide a valid redeem URL');
        }

        if (! filter_var($summary, FILTER_SANITIZE_STRING)) {
            throw new \Exception('Summary contains illegal chars');
        }

        if (! isset($media[0]['mediaFormat']) && ! isset($media[0]['sourceUrl'])) {
            throw new \Exception('mediaFormat and sourceUrl keys are necessary');
        }

        if ('PHOTO' != $media[0]['mediaFormat']
            && ! filter_var($media[0]['sourceUrl'], FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)
        ) {
            throw new \Exception('Provide a valid media type or url');
        }

        $extension = substr($media[0]['sourceUrl'], strrpos($media[0]['sourceUrl'], '.'));
        if (! in_array($extension, ['.jpeg', '.jpg', '.png', '.gif'])) {
            throw new \Exception('Invalid media provided. Supported media is jpeg, jpg, png, gif');
        }

        if (! preg_match('~[a-z]{2}\-[A-Z]{2}~', $language, $matches)) {
            throw new \Exception('Provide a valid language');
        }

        // Prepares offer post
        $localPostOffer = new Google_Service_MyBusiness_LocalPostOffer;
        $localPostOffer->setCouponCode($couponCode);
        $localPostOffer->setRedeemOnlineUrl($redeemUrl);

        // Prepares local post
        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->getLanguageCode($language);
        $localPost->setOffer($localPostOffer);
        $localPost->setSummary($summary);
        $localPost->setMedia($media);

        // Creates offer post
        $localPosts = $this->myBusiness->accounts_locations_localPosts;
        return $localPosts->create($parent, $localPost);
    }

    /**
     * Answers to a question
     *
     * @param string $parent Account name, location name, and question name
     * For Example: accounts/{numbers}/locations/{numbers}/questions/{key}
     *
     * @param string $text The answer to the questions (max 4096 chars)
     * @return Google_Service_MyBusiness_Answer
     */
    public function answerQuestion($parent, $text)
    {
        // Prepares answer
        $answer = new Google_Service_MyBusiness_Answer;
        $answer->setText($text);

        // Prepares upsert answer request
        $upsertAnswerRequest = new Google_Service_MyBusiness_UpsertAnswerRequest;
        $upsertAnswerRequest->setAnswer($answer);

        // Answers a question
        $answerQuestion = $this->myBusiness->accounts_locations_questions_answers;
        return $answerQuestion->upsert($parent, $upsertAnswerRequest);
    }

    /**
     * Makes a reply to a review
     *
     * @param string $parent
     * For Example: accounts/{numbers}/locations/{numbers}/reviews/{key}
     *
     * @param string $comment
     * @return Google_Service_MyBusiness_ReviewReply
     */
    public function replyReview(string $parent, string $comment)
    {
        // Prepares reply for review
        $reviewReply = new Google_Service_MyBusiness_ReviewReply;
        $reviewReply->setComment($comment);

        // Creates reply for review
        $createReviewReply = $this->myBusiness->accounts_locations_reviews;
        return $createReviewReply->updateReply($parent, $reviewReply);
    }

    /**
     * Deletes the response to the specified review. This operation is only valid if
     * the specified location is verified. (reviews.deleteReply)
     *
     * @param string $name The name of the review reply to delete.
     * For Example: accounts/{numbers}/locations/{numbers}/reviews/{key}
     *
     * @param array $optParams Optional parameters.
     * @return Google_Service_MyBusiness_MybusinessEmpty
     */
    public function deleteReply($name, $optParams = array())
    {
        // Deletes a reply
        $deleteReply = $this->myBusiness->accounts_locations_reviews;
        return $deleteReply->deleteReply($name, $optParams);
    }

    /**
     * Deletes the answer written by the current user to a question.
     * (answers.delete)
     *
     * @param string $parent The name of the question to delete an answer for.
     * @param array $optParams Optional parameters.
     * @return Google_Service_MyBusiness_MybusinessEmpty
     */
    public function deleteQuestionAnswer($parent, $optParams = array())
    {
        // Delete question's answer
        $deleteQuestionAnswer = $this->myBusiness->accounts_locations_questions_answers;
        return $deleteQuestionAnswer->delete($parent, $optParams);
    }

    public function addMenuOrServices()
    {
        // $priceLists = [
        //     [
        //         'priceListId' => '',
        //         'labels' => [
        //             'displayName' => '',
        //             'description' => '',
        //             'languageCode' => ''
        //         ],
        //         'sections' => [
        //             [
        //                 'sectionId' => '',
        //                 'sectionType' => '',
        //                 'labels' => [
        //                     'displayName' => '',
        //                     'description' => '',
        //                     'languageCode' => ''
        //                 ],
        //                 'items' => [
        //                     [
        //                         'itemId' => '',
        //                         'labels' => [
        //                             'displayName' => '',
        //                             'description' => '',
        //                             'languageCode' => ''
        //                         ],
        //                         'price' => [
        //                             'currencyCode' => '',
        //                             'amount' => '',
        //                         ]
        //
        //                     ],
        //                 ]
        //             ],
        //         ]
        //     ]
        // ];

        // $priceList = new Google_Service_MyBusiness_PriceList;
        // $priceList->setPriceListId();
        // $priceList->setSourceUrl();
        // $priceList->setLabels();
        // $priceList->setSections();
        //
        // $location = new Google_Service_MyBusiness_Location;
        // $location->setPriceLists([
        //
        // ]);
        //
        // $priceLabel = new Google_Service_MyBusiness_Label;
        // $sectionItem = new Google_Service_MyBusiness_Item;
        //
        // $priceSection = new Google_Service_MyBusiness_Section;
        // $itemMoney = new Google_Service_MyBusiness_Money;
        //
        // $patchPriceList = $this->myBusiness->accounts_locations;
        // return $patchPriceList->patch($name, $location);
    }

    /**
     * Creates a product
     *
     * @param string $parent Account name and location name
     * For Example: accounts/{numbers}/locations/{numbers}
     *
     * @param string $title Product title (max 140 chars)
     * @param int $highPrice Product price
     * @param array $media An array media items
     * For example: [
     *      [
     *          'mediaFormat' => 'PHOTO|VIDEO'
     *          'sourceUrl' => 'URL for the media'
     *      ],
     * ]
     *
     * @param string $currency The currency type i.e 'USD'
     * @param int|null $lowPrice Lower price for the product
     * @param string|null $summary Description for the product (max 240 chars)
     * @return Google_Service_MyBusiness_LocalPost
     */
    public function addProduct(
        string $parent,
        string $title,
        int $lowPrice,
        int $highPrice,
        string $summary,
        array $media,
        string $currency = 'USD'
    ) {
        // Prepares product price
        $lowerPrice = new Google_Service_MyBusiness_Money;
        $lowerPrice->setCurrencyCode($currency);
        $lowerPrice->setUnits($lowPrice);

        $upperPrice = new Google_Service_MyBusiness_Money;
        $upperPrice->setCurrencyCode($currency);
        $upperPrice->setUnits($highPrice);

        // Prepares local product post
        $localPostProduct = new Google_Service_MyBusiness_LocalPostProduct;
        $localPostProduct->setLowerPrice($lowerPrice);
        $localPostProduct->setUpperPrice($upperPrice);
        $localPostProduct->setProductName($title);

        // Prepares local post
        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->setProduct($localPostProduct);
        $localPost->setSummary($summary);
        $localPost->setMedia($media);

        $localPosts = $this->myBusiness->accounts_locations_localPosts;
        return $localPosts->create($parent, $localPost);
    }

    /**
     * Fetches location insights based on metric type
     *
     * @param string $name Account name
     * For Example: accounts/{numbers}
     *
     * @param array $locationName
     * For Example: [
     *      'accounts/{numbers}/locations/{numbers}'
     * ]
     *
     * @param string $metric Type of metric
     * For Example: "ALL|QUERIES_DIRECT|QUERIES_INDIRECT|QUERIES_CHAIN|VIEWS_MAPS|
     * VIEWS_SEARCH|ACTIONS_WEBSITE|ACTIONS_PHONE|ACTIONS_DRIVING_DIRECTIONS|PHOTOS_VIEWS_MERCHANT
     * |PHOTOS_VIEWS_CUSTOMERS|PHOTOS_COUNT_MERCHANT|PHOTOS_COUNT_CUSTOMERS|LOCAL_POST_VIEWS_SEARCH
     * |LOCAL_POST_ACTIONS_CALL_TO_ACTION"
     *
     * @param array $setOptions Array of options
     * For Example: [
     *      'AGGREGATED_TOTAL',
     *      'AGGREGATED_DAILY',
     *      'BREAKDOWN_DAY_OF_WEEK' - Only valid for ACTIONS_PHONE.
     *      'BREAKDOWN_HOUR_OF_DAY' - Only valid for ACTIONS_PHONE.
     * ]
     *
     * @param DateTime $startDateTime
     * @param DateTime $endDateTime
     * @return Google_Service_MyBusiness_ReportLocationInsightsResponse
     * @throws Exception
     */
    public function locationInsightsBasicMetric(
        string $name,
        array $locationName,
        string $metric,
        array $setOptions,
        \DateTime $startDateTime,
        \DateTime $endDateTime
    ) {
        // Prepares metric request
        $metricRequest = new Google_Service_MyBusiness_MetricRequest;
        $metricRequest->setMetric($metric);
        $metricRequest->setOptions($setOptions);

        // Prepares time range
        $timeRange = new Google_Service_MyBusiness_TimeRange;
        $timeRange->setStartTime($startDateTime->format('Y-m-d\TH:i:s\Z'));
        $timeRange->setEndTime($endDateTime->format('Y-m-d\TH:i:s\Z'));

        // Prepares basic metric request
        $basicMetricRequest = new Google_Service_MyBusiness_BasicMetricsRequest;
        $basicMetricRequest->setMetricRequests($metricRequest);
        $basicMetricRequest->setTimeRange($timeRange);

        // Prepare report location insights request
        $reportLocationInsights = new Google_Service_MyBusiness_ReportLocationInsightsRequest;
        $reportLocationInsights->setBasicRequest($basicMetricRequest);
        $reportLocationInsights->setLocationNames($locationName);

        $accountsLocations = $this->myBusiness->accounts_locations;
        return $accountsLocations->reportInsights($name, $reportLocationInsights);
    }

    /**
     * Fetches location insights based on driving direction
     *
     * @param string $name Account name
     * For Example: accounts/{numbers}
     *
     * @param array $locationNames Account name and location name
     * For Example: [
     *      'accounts/{numbers}locations/{numbers}'
     * ]
     *
     * @param string $numberOfDays Insights would be returned based on the number of days
     * For Example: 'NINETY|THIRTY|SEVEN'
     * 
     * @return Google_Service_MyBusiness_ReportLocationInsightsResponse
     */
    public function locationInsightsDrivingDirectionsMetric(
        string $name,
        array $locationNames,
        string $numberOfDays
    ) {
        // Prepares driving directions metric request
        $drivingDirectionMetricRequest = new Google_Service_MyBusiness_DrivingDirectionMetricsRequest;
        $drivingDirectionMetricRequest->setNumDays($numberOfDays);

        $reportLocationInsights = new Google_Service_MyBusiness_ReportLocationInsightsRequest;
        $reportLocationInsights->setDrivingDirectionsRequest($drivingDirectionMetricRequest);
        $reportLocationInsights->setLocationNames($locationNames);

        $accountsLocations = $this->myBusiness->accounts_locations;
        return $accountsLocations->reportInsights($name, $reportLocationInsights);
    }

    /**
     * Fetches post insights based on metric type
     *
     * @param string $name Account name and location name
     * For Example: accounts/{numbers}/locations/{numbers}
     *
     * @param array $localPostNames
     * For Example: [
     *      'accounts/{numbers}/locations/{numbers}/localPosts/{numbers}
     * ]
     *
     * @param string $metric Type of metric
     * For Example: "ALL|QUERIES_DIRECT|QUERIES_INDIRECT|QUERIES_CHAIN|VIEWS_MAPS|
     * VIEWS_SEARCH|ACTIONS_WEBSITE|ACTIONS_PHONE|ACTIONS_DRIVING_DIRECTIONS|PHOTOS_VIEWS_MERCHANT
     * |PHOTOS_VIEWS_CUSTOMERS|PHOTOS_COUNT_MERCHANT|PHOTOS_COUNT_CUSTOMERS|LOCAL_POST_VIEWS_SEARCH
     * |LOCAL_POST_ACTIONS_CALL_TO_ACTION"
     *
     * @param array $setOptions Array of options
     * For Example: [
     *      'AGGREGATED_TOTAL',
     *      'AGGREGATED_DAILY',
     *      'BREAKDOWN_DAY_OF_WEEK' - Only valid for ACTIONS_PHONE.
     *      'BREAKDOWN_HOUR_OF_DAY' - Only valid for ACTIONS_PHONE.
     * ]
     *
     * @param DateTime $startDateTime
     * @param DateTime $endDateTime
     * @return Google_Service_MyBusiness_ReportLocationInsightsResponse
     * @throws Exception
     */
    public function postsInsightsBasicMetric(
        string $name,
        array $localPostNames,
        string $metric,
        array $setOptions,
        \DateTime $startDateTime,
        \DateTime $endDateTime
    ) {
        // Prepares metric request
        $metricRequest = new Google_Service_MyBusiness_MetricRequest;
        $metricRequest->setMetric($metric);
        $metricRequest->setOptions($setOptions);

        // Prepares time range
        $timeRange = new Google_Service_MyBusiness_TimeRange;
        $timeRange->setStartTime($startDateTime->format('Y-m-d\TH:i:s\Z'));
        $timeRange->setEndTime($endDateTime->format('Y-m-d\TH:i:s\Z'));

        // Prepares basic metric request
        $basicMetricRequest = new Google_Service_MyBusiness_BasicMetricsRequest;
        $basicMetricRequest->setMetricRequests([
            $metricRequest,
        ]);
        $basicMetricRequest->setTimeRange($timeRange);

        // Prepare report posts insights request
        $reportLocalPostInsightsRequest = new Google_Service_MyBusiness_ReportLocalPostInsightsRequest;
        $reportLocalPostInsightsRequest->setBasicRequest($basicMetricRequest);
        $reportLocalPostInsightsRequest->setLocalPostNames($localPostNames);

        $accountsLocations = $this->myBusiness->accounts_locations_localPosts;
        return $accountsLocations->reportInsights($name, $reportLocalPostInsightsRequest);
    }

    /**
     * Creates a new media item for the location.
     *
     * @param string $parent The resource name of the location where this media item
     * will be created.
     * For Example: accounts/{numbers}/locations/{numbers}/media
     *
     * @param string $mediaFormat The format of the media
     * For Example: PHOTO|VIDEO
     *
     * @param string $category The type of the media
     * For Example: COVER|PROFILE|LOGO|EXTERIOR|PRODUCT|FOOD_AND_DRINK|MENU|COMMON_AREA|ROOMS|TEAMS|ADDITIONAL
     *
     * @param string $description The description for the media item
     * @return Google_Service_MyBusiness_MediaItem
     * @throws Exception
     */
    public function mediaUpload(
        string $parent,
        string $mediaFormat,
        string $category,
        string $description,
        string $sourceUrl
    ) {
        if (! in_array($mediaFormat, ['PHOTO', 'VIDEO'])) {
            throw new \Exception('Media format must be one of PHOTO and VIDEO');
        }

        $categories = [
            'COVER',
            'PROFILE',
            'LOGO',
            'EXTERIOR',
            'INTERIOR',
            'PRODUCT',
            'AT_WORK',
            'FOOD_AND_DRINK',
            'MENU',
            'COMMON_AREA',
            'ROOMS',
            'TEAMS',
            'ADDITIONAL',
        ];

        if (! in_array($category, $categories)) {
            throw new \Exception('The category is not supported');
        }

        $locationAssociation = new Google_Service_MyBusiness_LocationAssociation;
        $locationAssociation->setCategory($category);

        $mediaItem = new Google_Service_MyBusiness_MediaItem;
        $mediaItem->setSourceUrl($sourceUrl);
        $mediaItem->setMediaFormat($mediaFormat);
        $mediaItem->setDescription($description);
        $mediaItem->setLocationAssociation($locationAssociation);


        $locationsMedia = $this->myBusiness->accounts_locations_media;
        return $locationsMedia->create($parent, $mediaItem);
    }
}