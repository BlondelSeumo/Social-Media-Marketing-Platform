<?php

require_once('Google_My_Business/autoload.php');
require_once('Google_My_Business/Client.php');
require_once('Google_My_Business/Service/MyBusiness.php');

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

    /**
     * Google_my_business constructor.
     */
    public function __construct()
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

        // Sets user ID
        $this->userId = $this->CI->session->userdata('user_id');

        // Gets OAuth2 parameters
        $data = $this->getOAuth2Params();


        if (! count($data)) {
            throw new \Exception('Client ID and Client secret are not found.');
        }

        $this->clientId = $data['client_id'] ? $data['client_id'] : null;
        $this->clientSecret = $data['client_secret'] ? $data['client_secret'] : null;
        $this->redirectUri = $data['redirect_uri'] ? $data['redirect_uri'] : null;
        $this->accessToken = $data['access_token'] ? $data['access_token'] : null;

        // Creates google my business object
        $this->client = $this->bootGoogleClient();
        $this->myBusiness = new Google_Service_MyBusiness($this->client);
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
        if ($client->isAccessTokenExpired()) {
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
    protected function updateAccessToken(string $accessToken)
    {
        $accessTokenValidity = json_decode($accessToken, true);
        if (! is_array($accessTokenValidity) || null == $accessTokenValidity) {
            throw new Exception('Invalid access token provided to be saved in database.');
        }

        $data = [
            'access_token' => $accessToken
        ];

        $where = [
            'user_id' => $this->userId
        ];



        $this->CI->basic->update_data('google_mybusiness_oauth', $where, $data);
    }

    /**
     * Responsible for getting OAuth2 params
     *
     * @return array|null
     */
    protected function getOAuth2Params()
    {
        // $select = [
        //     'google_app_config.client_id',
        //     'google_app_config.client_secret',
        //     'google_user_acccount.access_token',
        // ];

        $where = [
            'where' => [
                'user_id' => $this->userId,
                'status' => '1',
            ]
        ];

        // $join = [
        //     'google_user_acccount' => 'google_app_config.id=google_user_account.app_config_id'
        // ];

        $result = $this->CI->basic->get_data('google_mybusiness_oauth', $where);
        return $result[0] ? $result[0] : null;
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
        return "<a href='{$login_url}' class='btn btn-outline-primary login_button' social_account='google'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
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
     * @param string $url The URL where the user will be redirected
     * @param string $summery Description for the post
     * @param array $media An array media items
     * For example: [
     *      [
     *          'mediaFormat' => 'PHOTO|VIDEO'
     *          'sourceUrl' => 'URL for the media'
     *      ],
     * ]
     * @param string $language
     * @return Google_Service_MyBusiness_LocalPost
     */
    public function callToActionPost(
        string $parent,
        string $actionType,
        string $url,
        string $summery,
        array $media,
        string $language = 'en_EN'
    ) {
        $callToAction = new Google_Service_MyBusiness_CallToAction;
        $callToAction->actionType($actionType);
        $callToAction->setUrl($url);

        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->getLanguageCode($language);
        $localPost->setCallToAction($callToAction);
        $localPost->setSummary($summery);
        $localPost->setMedia($media);

        $callToActionPost = $this->myBusiness->accounts_locations_localPosts;
        return $callToActionPost->create($parent, $localPost);
    }

    /**
     * Creates an event post
     *
     * @param $parent Account name and location name
     * For example: accounts/{numbers}/locations/{numbers}
     *
     * @param $title Event name (max 140 chars)
     * @param array $media An array media items
     * For example: [
     *      [
     *          'mediaFormat' => 'PHOTO|VIDEO'
     *          'sourceUrl' => 'URL for the media'
     *      ],
     * ]
     *
     * @param DateTime $startDateTime
     * @param DateTime $endDateTime
     * @param string $language
     * @return Google_Service_MyBusiness_LocalPost
     * @throws Exception
     */
    public function eventPost(
        $parent,
        $title,
        array $media,
        \DateTime $startDateTime,
        \DateTime $endDateTime,
        $language = 'en_US'
    ) {
        $sdt = new \DateTime($startDateTime);
        $startYear = $sdt->format('Y');
        $startMonth = $sdt->format('m');
        $startDay = $sdt->format('d');
        $startHours = $sdt->format('H');
        $startMinutes = $sdt->format('i');
        $startSeconds = $sdt->format('s');

        $edt = new \DateTime($endDateTime);
        $endYear = $edt->format('Y');
        $endMonth = $edt->format('m');
        $endDay = $edt->format('d');
        $endHours = $edt->format('H');
        $endMinutes = $edt->format('i');
        $endSeconds = $edt->format('s');

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
        $localPost->setMedia($media);

        // Creates event post
        $eventPost = $this->myBusiness->accounts_locations_localPosts;
        return $eventPost->create($parent, $localPost);
    }

    /**
     * Creates an offer post
     *
     * @param $parent Account name and location name
     * For example: accounts/{numbers}/locations/{numbers}
     *
     * @param $title Offer title (max 140 chars)
     * @param $couponCode
     * @param $refundUrl
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
     */
    public function offerPost(
        $parent,
        $title,
        $couponCode,
        $refundUrl,
        array $media,
        $language = 'en_EN'
    ) {
        // Prepares offer post
        $localPostOffer = new Google_Service_MyBusiness_LocalPostOffer;
        $localPostOffer->setCouponCode($couponCode);
        $localPostOffer->setRedeemOnlineUrl($refundUrl);

        // Prepares local post
        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->getLanguageCode($language);
        $localPost->setOffer($localPostOffer);
        $localPost->setSummary($title);
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
     * @param string $answer The answer to the questions (max 4096 chars)
     * @return Google_Service_MyBusiness_Answer
     */
    public function answerQuestion($parent, $answer)
    {
        // Prepares answer
        $answer = new Google_Service_MyBusiness_Answer;
        $answer->setText($answer);

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
     * @param array $locationName Account name and location name
     * For Example: [
     *      'accounts/{numbers}locations/{numbers}'
     * ]
     *
     * @param string $numberOfDays Insights would be returned based on the number of days
     * For Example: 'NINETY|SEVENTY|FIFTY|THIRTY'
     * 
     * @return Google_Service_MyBusiness_ReportLocationInsightsResponse
     */
    public function locationInsightsDrivingDirectionsMetric(
        string $name,
        array $locationName,
        string $numberOfDays
    ) {
        // Prepares driving directions metric request
        $drivingDirectionMetricRequest = new Google_Service_MyBusiness_DrivingDirectionMetricsRequest;
        $drivingDirectionMetricRequest->setNumDays($numberOfDays);

        $reportLocationInsights = new Google_Service_MyBusiness_ReportLocationInsightsRequest;
        $reportLocationInsights->setDrivingDirectionsRequest($drivingDirectionMetricRequest);
        $reportLocationInsights->setLocationNames($locationName);

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
        $basicMetricRequest->setMetricRequests($metricRequest);
        $basicMetricRequest->setTimeRange($timeRange);

        // Prepare report posts insights request
        $reportLocalPostInsightsRequest = new Google_Service_MyBusiness_ReportLocalPostInsightsRequest;
        $reportLocalPostInsightsRequest->setBasicRequest($basicMetricRequest);
        $reportLocalPostInsightsRequest->setLocalPostNames($localPostNames);

        $accountsLocations = $this->myBusiness->accounts_locations_localPosts;
        return $accountsLocations->reportInsights($name, $reportLocalPostInsightsRequest);
    }

    public function tryOfferPost()
    {
        $startDateTime = new \DateTime('2020-02-10');
        $endDateTime = new \DateTime('2020-02-20');

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
        $localPostEvent->setTitle('Checking Offer Post');
        $localPostEvent->setSchedule($schedule);

        // Prepares offer post
        $localPostOffer = new Google_Service_MyBusiness_LocalPostOffer;
        $localPostOffer->setCouponCode('676655765');
        $localPostOffer->setRedeemOnlineUrl('https://example.com/redeem/');

        // Prepares local post
        $localPost = new Google_Service_MyBusiness_LocalPost;
        $localPost->getLanguageCode('en');
        $localPost->setEvent($localPostEvent);
        $localPost->setOffer($localPostOffer);
        $localPost->setSummary('Some test summary for the offer post and so on.');
        $localPost->setMedia([
            'mediaFormat' => 'PHOTO',
            'sourceUrl' => 'https://www.xeroneit.net/upload/portfolio/32/cover.jpg'
        ]);

        // Creates event post
        $eventPost = $this->myBusiness->accounts_locations_localPosts;
        return $eventPost->create('accounts/107745512734031207626/locations/14692206365244175995', $localPost);
    }
}