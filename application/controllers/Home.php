<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
* @category controller
* class home
*/
class Home extends CI_Controller
{

    /**
    * load constructor
    * @access public
    * @return void
    */
    public $module_access;
    public $language;
    public $is_rtl;
    public $user_id;
    public $is_demo;

    public $is_ad_enabled;
    public $is_ad_enabled1;
    public $is_ad_enabled2;
    public $is_ad_enabled3;
    public $is_ad_enabled4;

    public $ad_content1;
    public $ad_content1_mobile;
    public $ad_content2;
    public $ad_content3;
    public $ad_content4;
    public $app_product_id;
    public $APP_VERSION;
    public $strict_ajax_call = true;


    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        $this->load->helpers(array('my_helper','addon_helper'));

        $is_rtl = $this->config->item("is_rtl");
        if(!empty($is_rtl) && $is_rtl=='1') $this->is_rtl=TRUE;
        else $this->is_rtl=FALSE;

        $is_demo = $this->config->item("is_demo");
        if($is_demo=="") $is_demo="0";
        $this->is_demo=$is_demo;

        $this->language="";
        $this->_language_loader();

        $this->is_ad_enabled=false;
        $this->is_ad_enabled1=false;
        $this->is_ad_enabled2=false;
        $this->is_ad_enabled3=false;
        $this->is_ad_enabled4=false;

        $this->ad_content1="";
        $this->ad_content1_mobile="";
        $this->ad_content2="";
        $this->ad_content3="";
        $this->ad_content4="";
        $this->app_product_id=28;
        $this->APP_VERSION="";

        ignore_user_abort(TRUE);

        $seg = $this->uri->segment(2);

        if ($seg!="installation" && $seg!= "installation_action" && $seg!="central_webhook_callback" && $seg!="webhook_callback_main" ) {
            if (file_exists(APPPATH.'install.txt')) {
                redirect('home/installation', 'location');
            }
        }

        //if($seg!="central_webhook_callback" && $seg!="webhook_callback_main")
        if ($seg!="installation" && $seg!= "installation_action") {
            $this->load->database();
            $this->load->model('basic');
            $this->_time_zone_set();
            $this->user_id=$this->session->userdata("user_id");
            $this->load->library('upload');
            $this->load->helper('security');
            $this->upload_path = realpath(APPPATH . '../upload');
            $this->session->unset_userdata('set_custom_link');

          /*  $query = 'SET SESSION group_concat_max_len=9990000000000000000';
            $this->db->query($query); */

            /*
            $q= "SET SESSION wait_timeout=50000";
            $this->db->query($q); */


            /**Disable STRICT_TRANS_TABLES mode if exist on mysql ***/

            
            $query="SET SESSION sql_mode = ''";
            $this->db->query($query); 
        

            
            /**Change Datbase Collation **/
            $query="SET NAMES utf8mb4";
            $this->db->query($query);
            
            
            //loading addon language
            //$this->language_loader_addon();

           /*

            if(function_exists('ini_set')){
           		ini_set('memory_limit', '-1');
            } 

            */


            if ($this->session->userdata('logged_in') == 1)
            {
                $package_info=$this->session->userdata("package_info");
                $module_ids='';
                if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
                $this->module_access=explode(',', $module_ids);
            }

            
            
        }  

        // affiliate cookies 
        $this->load->helper('cookie');
        if(isset($_GET['ref']) && !empty($_GET['ref'])) {

            $affiliateid = $_GET['ref'];

            $visitor_cookie = array(
                "name" => "affiliate_id",
                "value" => $affiliateid,
                "expire" => 604800
            );
            set_cookie($visitor_cookie);

            $convertidintobinaryid = pack("H*", $affiliateid);
            $explodeBinarycontactid = explode("-", $convertidintobinaryid);
            $aff_id = $explodeBinarycontactid[0];
            $visitorip = $this->real_ip();
            $click_data = [];
            $this->basic->insert_data("affiliate_visitors_action",['affiliate_id'=>$aff_id,'type'=>'click','ip_address'=>$visitorip,'clicked_time'=>date("Y-m-d H:i:s")]);
        }

        if($this->config->item('force_https')=='1')  
        {
            $actualLink = $actualLink = base_url(uri_string());
            $poS=strpos($actualLink, 'http://');
            if($poS!==FALSE)
            {
             $new_link=str_replace('http://', 'https://', $actualLink);
             redirect($new_link,'refresh');
            }    
        }

        if($this->session->userdata('log_me_out') == '1') $this->logout();

        if($this->session->userdata('csrf_token_session')=="") $this->session->set_userdata('csrf_token_session',  bin2hex(random_bytes(32)));


        // allow ajax cors and ajax check for flowbuilder development environment
        $strict_ajax_call = true;
        $hostname  = base_url();
        $hostname = str_replace(['http://','https://'],['',''],$hostname);
        $explode = explode('/',$hostname);
        $hostname = $explode[0] ?? 'localhost';
        $hostname = trim($hostname,'/');
        if(file_exists(APPPATH.'config/flowbuilder_config.php'))
        {
            $this->config->load('flowbuilder_config');
            $strict_ajax_call = $this->config->item('strict_ajax_call');
            if(!is_bool($strict_ajax_call)) $strict_ajax_call = true;
        }        

        if($strict_ajax_call==true && $hostname!='localhost') $this->strict_ajax_call = true;
        else $this->strict_ajax_call = false;

        if(!$this->strict_ajax_call){
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: *');
        }

        $is_mobile = '0';
        if(is_mobile()) $is_mobile = '1';
        $this->session->set_userdata("is_mobile",$is_mobile);
    }



    public function get_route_list(){
        $route_list = $this->router->routes;
        $return = [];
        foreach($route_list as $key=>$value){
            $return[$value] = $key;

        }
        return $return;
    }




    public function _language_loader($default_lang="")
    {

        if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($this->session->userdata("selected_language")!="")
        $this->language = $this->session->userdata("selected_language");
        else if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($default_lang!="") $this->language=$default_lang;

        // if($this->language=="arabic")
        // $this->is_rtl=TRUE;

        $path=str_replace('\\', '/', APPPATH.'/language/'.$this->language); 
        $files=$this->_scanAll($path);
        foreach ($files as $key2 => $value2) 
        {
            $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
            if($current_file=="" || !is_file($current_file)) continue;
            $current_file_explode=explode('/',$current_file);
            $filename=array_pop($current_file_explode);
            $pos=strpos($filename,'_lang.php');
            if($pos!==false) // check if it is a lang file or not
            {
                $filename=str_replace('_lang.php', '', $filename); 
                $this->lang->load($filename, $this->language);
            }
        }          
        
       
    }

    public function installation()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }
        $data = array("body" => "front/install", "page_title" => "Install Package","language_info" => $this->_language_list());
        $this->_subscription_viewcontroller($data);
    }


    public function installation_action()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }

        if ($_POST) {
            // validation
            $this->form_validation->set_rules('host_name',               '<b>Host Name</b>',                   'trim|required');
            $this->form_validation->set_rules('database_name',           '<b>Database Name</b>',               'trim|required');
            $this->form_validation->set_rules('database_username',       '<b>Database Username</b>',           'trim|required');
            $this->form_validation->set_rules('database_password',       '<b>Database Password</b>',           'trim');
            $this->form_validation->set_rules('app_username',            '<b>Admin Panel Login Email</b>',     'trim|required|valid_email');
            $this->form_validation->set_rules('app_password',            '<b>Admin Panel Login Password</b>',  'trim|required');
            $this->form_validation->set_rules('institute_name',          '<b>Company Name</b>',                'trim');
            $this->form_validation->set_rules('institute_address',       '<b>Company Address</b>',             'trim');
            $this->form_validation->set_rules('institute_mobile',        '<b>Company Phone / Mobile</b>',      'trim');
            $this->form_validation->set_rules('language',                '<b>Language</b>',                    'trim');

            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) {
                return $this->installation();
            } else {
                $host_name = addslashes(strip_tags($this->input->post('host_name', true)));
                $database_name = addslashes(strip_tags($this->input->post('database_name', true)));
                $database_username = addslashes(strip_tags($this->input->post('database_username', true)));
                $database_password = addslashes(strip_tags($this->input->post('database_password', true)));
                $app_username = addslashes(strip_tags($this->input->post('app_username', true)));
                $app_password = addslashes(strip_tags($this->input->post('app_password', true)));
                $institute_name = addslashes(strip_tags($this->input->post('institute_name', true)));
                $institute_address = addslashes(strip_tags($this->input->post('institute_address', true)));
                $institute_mobile = addslashes(strip_tags($this->input->post('institute_mobile', true)));
                $language = addslashes(strip_tags($this->input->post('language', true)));

                $con=@mysqli_connect($host_name, $database_username, $database_password);
                if (!$con) {
                    $mysql_error = "Could not connect to MySQL : ";
                    $mysql_error .= mysqli_connect_error();
                    $this->session->set_userdata('mysql_error', $mysql_error);
                    return $this->installation();
                }
                if (!@mysqli_select_db($con,$database_name)) {
                    $this->session->set_userdata('mysql_error', "Database not found.");
                    return $this->installation();
                }
                mysqli_close($con);

                 // writing application/config/my_config

                include('application/config/my_config.php');                               
                $config['institute_address1'] = $institute_name;
                $config['institute_address2'] = $institute_address;
                $config['institute_email'] = $app_username;
                $config['institute_mobile'] = $institute_mobile;
                $config['language'] = $language;
                $config['instagram_reply_enable_disable'] = '1';
                file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');

              
                //writting application/config/database
                $database_data = "";
                $database_data.= "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n
                    \$active_group = 'default';
                    \$active_record = true;
                    \$db['default']['hostname'] = '$host_name';
                    \$db['default']['username'] = '$database_username';
                    \$db['default']['password'] = '$database_password';
                    \$db['default']['database'] = '$database_name';
                    \$db['default']['dbdriver'] = 'mysqli';
                    \$db['default']['dbprefix'] = '';
                    \$db['default']['pconnect'] = FALSE;
                    \$db['default']['db_debug'] = TRUE;
                    \$db['default']['cache_on'] = FALSE;
                    \$db['default']['cachedir'] = '';
                    \$db['default']['char_set'] = 'utf8';
                    \$db['default']['dbcollat'] = 'utf8_general_ci';
                    \$db['default']['swap_pre'] = '';
                    \$db['default']['autoinit'] = TRUE;
                    \$db['default']['stricton'] = FALSE;";
                file_put_contents(APPPATH.'config/database.php', $database_data, LOCK_EX);
                //writting application/config/database

                // writting client js
                // $client_js_content=file_get_contents('js/my_chat_custom.js');
                // $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
                // file_put_contents('js/my_chat_custom.js', $client_js_content_new, LOCK_EX);
                // writting client js

                // loding database library, because we need to run queries below and configs are already written
                $this->load->database();
                $this->load->model('basic');
                // loding database library, because we need to run queries below and configs are already written

                // dumping sql
                $dump_file_name = 'initial_db.sql';
                $dump_sql_path = 'assets/backup_db/'.$dump_file_name;
                $this->basic->import_dump($dump_sql_path);
                // dumping sql

                // Insert Version
                $this->db->insert('version', array('version' => trim($this->config->item('product_version')), 'current' => '1', 'date' => date('Y-m-d H:i:s')));

                //generating hash password for admin and updaing database
                $app_password = md5($app_password);
                $this->basic->update_data($table = "users", $where = array("user_type" => "Admin"), $update_data = array("mobile" => $institute_mobile, "email" => $app_username, "password" => $app_password, "name" => $institute_name, "status" => "1", "deleted" => "0", "address" => $institute_address));
                  //generating hash password for admin and updaing database

                  //deleting the install.txt file,because installation is complete
                  if (file_exists(APPPATH.'install.txt')) {
                      unlink(APPPATH.'install.txt');
                  }
                  //deleting the install.txt file,because installation is complete
                  redirect('home/login');
            }
        }
    }


    public function index()
    {
        $display_landing_page=$this->config->item('display_landing_page');
        if($display_landing_page=='') $display_landing_page='0';

        if($display_landing_page=='0')
        $this->login_page();
        else $this->_site_viewcontroller();
    }


    public function _time_zone_set()
    {
       $time_zone = $this->config->item('time_zone');
        if ($time_zone== '') {
            $time_zone="Europe/Dublin";
        }
        date_default_timezone_set($time_zone);
    }



    public function _time_zone_list()
    {
        return $timezones = 
        array(
            'America/Adak' => '(GMT-10:00) America/Adak (Hawaii-Aleutian Standard Time)',
            'America/Atka' => '(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)',
            'America/Anchorage' => '(GMT-9:00) America/Anchorage (Alaska Standard Time)',
            'America/Juneau' => '(GMT-9:00) America/Juneau (Alaska Standard Time)',
            'America/Nome' => '(GMT-9:00) America/Nome (Alaska Standard Time)',
            'America/Yakutat' => '(GMT-9:00) America/Yakutat (Alaska Standard Time)',
            'America/Dawson' => '(GMT-8:00) America/Dawson (Pacific Standard Time)',
            'America/Ensenada' => '(GMT-8:00) America/Ensenada (Pacific Standard Time)',
            'America/Los_Angeles' => '(GMT-8:00) America/Los_Angeles (Pacific Standard Time)',
            'America/Tijuana' => '(GMT-8:00) America/Tijuana (Pacific Standard Time)',
            'America/Vancouver' => '(GMT-8:00) America/Vancouver (Pacific Standard Time)',
            'America/Whitehorse' => '(GMT-8:00) America/Whitehorse (Pacific Standard Time)',
            'Canada/Pacific' => '(GMT-8:00) Canada/Pacific (Pacific Standard Time)',
            'Canada/Yukon' => '(GMT-8:00) Canada/Yukon (Pacific Standard Time)',
            'Mexico/BajaNorte' => '(GMT-8:00) Mexico/BajaNorte (Pacific Standard Time)',
            'America/Boise' => '(GMT-7:00) America/Boise (Mountain Standard Time)',
            'America/Cambridge_Bay' => '(GMT-7:00) America/Cambridge_Bay (Mountain Standard Time)',
            'America/Chihuahua' => '(GMT-7:00) America/Chihuahua (Mountain Standard Time)',
            'America/Dawson_Creek' => '(GMT-7:00) America/Dawson_Creek (Mountain Standard Time)',
            'America/Denver' => '(GMT-7:00) America/Denver (Mountain Standard Time)',
            'America/Edmonton' => '(GMT-7:00) America/Edmonton (Mountain Standard Time)',
            'America/Hermosillo' => '(GMT-7:00) America/Hermosillo (Mountain Standard Time)',
            'America/Inuvik' => '(GMT-7:00) America/Inuvik (Mountain Standard Time)',
            'America/Mazatlan' => '(GMT-7:00) America/Mazatlan (Mountain Standard Time)',
            'America/Phoenix' => '(GMT-7:00) America/Phoenix (Mountain Standard Time)',
            'America/Shiprock' => '(GMT-7:00) America/Shiprock (Mountain Standard Time)',
            'America/Yellowknife' => '(GMT-7:00) America/Yellowknife (Mountain Standard Time)',
            'Canada/Mountain' => '(GMT-7:00) Canada/Mountain (Mountain Standard Time)',
            'Mexico/BajaSur' => '(GMT-7:00) Mexico/BajaSur (Mountain Standard Time)',
            'America/Belize' => '(GMT-6:00) America/Belize (Central Standard Time)',
            'America/Cancun' => '(GMT-6:00) America/Cancun (Central Standard Time)',
            'America/Chicago' => '(GMT-6:00) America/Chicago (Central Standard Time)',
            'America/Costa_Rica' => '(GMT-6:00) America/Costa_Rica (Central Standard Time)',
            'America/El_Salvador' => '(GMT-6:00) America/El_Salvador (Central Standard Time)',
            'America/Guatemala' => '(GMT-6:00) America/Guatemala (Central Standard Time)',
            'America/Knox_IN' => '(GMT-6:00) America/Knox_IN (Central Standard Time)',
            'America/Managua' => '(GMT-6:00) America/Managua (Central Standard Time)',
            'America/Menominee' => '(GMT-6:00) America/Menominee (Central Standard Time)',
            'America/Merida' => '(GMT-6:00) America/Merida (Central Standard Time)',
            'America/Mexico_City' => '(GMT-6:00) America/Mexico_City (Central Standard Time)',
            'America/Monterrey' => '(GMT-6:00) America/Monterrey (Central Standard Time)',
            'America/Rainy_River' => '(GMT-6:00) America/Rainy_River (Central Standard Time)',
            'America/Rankin_Inlet' => '(GMT-6:00) America/Rankin_Inlet (Central Standard Time)',
            'America/Regina' => '(GMT-6:00) America/Regina (Central Standard Time)',
            'America/Swift_Current' => '(GMT-6:00) America/Swift_Current (Central Standard Time)',
            'America/Tegucigalpa' => '(GMT-6:00) America/Tegucigalpa (Central Standard Time)',
            'America/Winnipeg' => '(GMT-6:00) America/Winnipeg (Central Standard Time)',
            'Canada/Central' => '(GMT-6:00) Canada/Central (Central Standard Time)',
            'Canada/East-Saskatchewan' => '(GMT-6:00) Canada/East-Saskatchewan (Central Standard Time)',
            'Canada/Saskatchewan' => '(GMT-6:00) Canada/Saskatchewan (Central Standard Time)',
            'Chile/EasterIsland' => '(GMT-6:00) Chile/EasterIsland (Easter Is. Time)',
            'Mexico/General' => '(GMT-6:00) Mexico/General (Central Standard Time)',
            'America/Atikokan' => '(GMT-5:00) America/Atikokan (Eastern Standard Time)',
            'America/Bogota' => '(GMT-5:00) America/Bogota (Colombia Time)',
            'America/Cayman' => '(GMT-5:00) America/Cayman (Eastern Standard Time)',
            'America/Coral_Harbour' => '(GMT-5:00) America/Coral_Harbour (Eastern Standard Time)',
            'America/Detroit' => '(GMT-5:00) America/Detroit (Eastern Standard Time)',
            'America/Fort_Wayne' => '(GMT-5:00) America/Fort_Wayne (Eastern Standard Time)',
            'America/Grand_Turk' => '(GMT-5:00) America/Grand_Turk (Eastern Standard Time)',
            'America/Guayaquil' => '(GMT-5:00) America/Guayaquil (Ecuador Time)',
            'America/Havana' => '(GMT-5:00) America/Havana (Cuba Standard Time)',
            'America/Indianapolis' => '(GMT-5:00) America/Indianapolis (Eastern Standard Time)',
            'America/Iqaluit' => '(GMT-5:00) America/Iqaluit (Eastern Standard Time)',
            'America/Jamaica' => '(GMT-5:00) America/Jamaica (Eastern Standard Time)',
            'America/Lima' => '(GMT-5:00) America/Lima (Peru Time)',
            'America/Louisville' => '(GMT-5:00) America/Louisville (Eastern Standard Time)',
            'America/Montreal' => '(GMT-5:00) America/Montreal (Eastern Standard Time)',
            'America/Nassau' => '(GMT-5:00) America/Nassau (Eastern Standard Time)',
            'America/New_York' => '(GMT-5:00) America/New_York (Eastern Standard Time)',
            'America/Nipigon' => '(GMT-5:00) America/Nipigon (Eastern Standard Time)',
            'America/Panama' => '(GMT-5:00) America/Panama (Eastern Standard Time)',
            'America/Pangnirtung' => '(GMT-5:00) America/Pangnirtung (Eastern Standard Time)',
            'America/Port-au-Prince' => '(GMT-5:00) America/Port-au-Prince (Eastern Standard Time)',
            'America/Resolute' => '(GMT-5:00) America/Resolute (Eastern Standard Time)',
            'America/Thunder_Bay' => '(GMT-5:00) America/Thunder_Bay (Eastern Standard Time)',
            'America/Toronto' => '(GMT-5:00) America/Toronto (Eastern Standard Time)',
            'Canada/Eastern' => '(GMT-5:00) Canada/Eastern (Eastern Standard Time)',
            'America/Caracas' => '(GMT-4:-30) America/Caracas (Venezuela Time)',
            'America/Anguilla' => '(GMT-4:00) America/Anguilla (Atlantic Standard Time)',
            'America/Antigua' => '(GMT-4:00) America/Antigua (Atlantic Standard Time)',
            'America/Aruba' => '(GMT-4:00) America/Aruba (Atlantic Standard Time)',
            'America/Asuncion' => '(GMT-4:00) America/Asuncion (Paraguay Time)',
            'America/Barbados' => '(GMT-4:00) America/Barbados (Atlantic Standard Time)',
            'America/Blanc-Sablon' => '(GMT-4:00) America/Blanc-Sablon (Atlantic Standard Time)',
            'America/Boa_Vista' => '(GMT-4:00) America/Boa_Vista (Amazon Time)',
            'America/Campo_Grande' => '(GMT-4:00) America/Campo_Grande (Amazon Time)',
            'America/Cuiaba' => '(GMT-4:00) America/Cuiaba (Amazon Time)',
            'America/Curacao' => '(GMT-4:00) America/Curacao (Atlantic Standard Time)',
            'America/Dominica' => '(GMT-4:00) America/Dominica (Atlantic Standard Time)',
            'America/Eirunepe' => '(GMT-4:00) America/Eirunepe (Amazon Time)',
            'America/Glace_Bay' => '(GMT-4:00) America/Glace_Bay (Atlantic Standard Time)',
            'America/Goose_Bay' => '(GMT-4:00) America/Goose_Bay (Atlantic Standard Time)',
            'America/Grenada' => '(GMT-4:00) America/Grenada (Atlantic Standard Time)',
            'America/Guadeloupe' => '(GMT-4:00) America/Guadeloupe (Atlantic Standard Time)',
            'America/Guyana' => '(GMT-4:00) America/Guyana (Guyana Time)',
            'America/Halifax' => '(GMT-4:00) America/Halifax (Atlantic Standard Time)',
            'America/La_Paz' => '(GMT-4:00) America/La_Paz (Bolivia Time)',
            'America/Manaus' => '(GMT-4:00) America/Manaus (Amazon Time)',
            'America/Marigot' => '(GMT-4:00) America/Marigot (Atlantic Standard Time)',
            'America/Martinique' => '(GMT-4:00) America/Martinique (Atlantic Standard Time)',
            'America/Moncton' => '(GMT-4:00) America/Moncton (Atlantic Standard Time)',
            'America/Montserrat' => '(GMT-4:00) America/Montserrat (Atlantic Standard Time)',
            'America/Port_of_Spain' => '(GMT-4:00) America/Port_of_Spain (Atlantic Standard Time)',
            'America/Porto_Acre' => '(GMT-4:00) America/Porto_Acre (Amazon Time)',
            'America/Porto_Velho' => '(GMT-4:00) America/Porto_Velho (Amazon Time)',
            'America/Puerto_Rico' => '(GMT-4:00) America/Puerto_Rico (Atlantic Standard Time)',
            'America/Rio_Branco' => '(GMT-4:00) America/Rio_Branco (Amazon Time)',
            'America/Santiago' => '(GMT-4:00) America/Santiago (Chile Time)',
            'America/Santo_Domingo' => '(GMT-4:00) America/Santo_Domingo (Atlantic Standard Time)',
            'America/St_Barthelemy' => '(GMT-4:00) America/St_Barthelemy (Atlantic Standard Time)',
            'America/St_Kitts' => '(GMT-4:00) America/St_Kitts (Atlantic Standard Time)',
            'America/St_Lucia' => '(GMT-4:00) America/St_Lucia (Atlantic Standard Time)',
            'America/St_Thomas' => '(GMT-4:00) America/St_Thomas (Atlantic Standard Time)',
            'America/St_Vincent' => '(GMT-4:00) America/St_Vincent (Atlantic Standard Time)',
            'America/Thule' => '(GMT-4:00) America/Thule (Atlantic Standard Time)',
            'America/Tortola' => '(GMT-4:00) America/Tortola (Atlantic Standard Time)',
            'America/Virgin' => '(GMT-4:00) America/Virgin (Atlantic Standard Time)',
            'Antarctica/Palmer' => '(GMT-4:00) Antarctica/Palmer (Chile Time)',
            'Atlantic/Bermuda' => '(GMT-4:00) Atlantic/Bermuda (Atlantic Standard Time)',
            'Atlantic/Stanley' => '(GMT-4:00) Atlantic/Stanley (Falkland Is. Time)',
            'Brazil/Acre' => '(GMT-4:00) Brazil/Acre (Amazon Time)',
            'Brazil/West' => '(GMT-4:00) Brazil/West (Amazon Time)',
            'Canada/Atlantic' => '(GMT-4:00) Canada/Atlantic (Atlantic Standard Time)',
            'Chile/Continental' => '(GMT-4:00) Chile/Continental (Chile Time)',
            'America/St_Johns' => '(GMT-3:-30) America/St_Johns (Newfoundland Standard Time)',
            'Canada/Newfoundland' => '(GMT-3:-30) Canada/Newfoundland (Newfoundland Standard Time)',
            'America/Araguaina' => '(GMT-3:00) America/Araguaina (Brasilia Time)',
            'America/Bahia' => '(GMT-3:00) America/Bahia (Brasilia Time)',
            'America/Belem' => '(GMT-3:00) America/Belem (Brasilia Time)',
            'America/Buenos_Aires' => '(GMT-3:00) America/Buenos_Aires (Argentine Time)',
            'America/Catamarca' => '(GMT-3:00) America/Catamarca (Argentine Time)',
            'America/Cayenne' => '(GMT-3:00) America/Cayenne (French Guiana Time)',
            'America/Cordoba' => '(GMT-3:00) America/Cordoba (Argentine Time)',
            'America/Fortaleza' => '(GMT-3:00) America/Fortaleza (Brasilia Time)',
            'America/Godthab' => '(GMT-3:00) America/Godthab (Western Greenland Time)',
            'America/Jujuy' => '(GMT-3:00) America/Jujuy (Argentine Time)',
            'America/Maceio' => '(GMT-3:00) America/Maceio (Brasilia Time)',
            'America/Mendoza' => '(GMT-3:00) America/Mendoza (Argentine Time)',
            'America/Miquelon' => '(GMT-3:00) America/Miquelon (Pierre & Miquelon Standard Time)',
            'America/Montevideo' => '(GMT-3:00) America/Montevideo (Uruguay Time)',
            'America/Paramaribo' => '(GMT-3:00) America/Paramaribo (Suriname Time)',
            'America/Recife' => '(GMT-3:00) America/Recife (Brasilia Time)',
            'America/Rosario' => '(GMT-3:00) America/Rosario (Argentine Time)',
            'America/Santarem' => '(GMT-3:00) America/Santarem (Brasilia Time)',
            'America/Sao_Paulo' => '(GMT-3:00) America/Sao_Paulo (Brasilia Time)',
            'Antarctica/Rothera' => '(GMT-3:00) Antarctica/Rothera (Rothera Time)',
            'Brazil/East' => '(GMT-3:00) Brazil/East (Brasilia Time)',
            'America/Noronha' => '(GMT-2:00) America/Noronha (Fernando de Noronha Time)',
            'Atlantic/South_Georgia' => '(GMT-2:00) Atlantic/South_Georgia (South Georgia Standard Time)',
            'Brazil/DeNoronha' => '(GMT-2:00) Brazil/DeNoronha (Fernando de Noronha Time)',
            'America/Scoresbysund' => '(GMT-1:00) America/Scoresbysund (Eastern Greenland Time)',
            'Atlantic/Azores' => '(GMT-1:00) Atlantic/Azores (Azores Time)',
            'Atlantic/Cape_Verde' => '(GMT-1:00) Atlantic/Cape_Verde (Cape Verde Time)',
            'Africa/Abidjan' => '(GMT+0:00) Africa/Abidjan (Greenwich Mean Time)',
            'Africa/Accra' => '(GMT+0:00) Africa/Accra (Ghana Mean Time)',
            'Africa/Bamako' => '(GMT+0:00) Africa/Bamako (Greenwich Mean Time)',
            'Africa/Banjul' => '(GMT+0:00) Africa/Banjul (Greenwich Mean Time)',
            'Africa/Bissau' => '(GMT+0:00) Africa/Bissau (Greenwich Mean Time)',
            'Africa/Casablanca' => '(GMT+0:00) Africa/Casablanca (Western European Time)',
            'Africa/Conakry' => '(GMT+0:00) Africa/Conakry (Greenwich Mean Time)',
            'Africa/Dakar' => '(GMT+0:00) Africa/Dakar (Greenwich Mean Time)',
            'Africa/El_Aaiun' => '(GMT+0:00) Africa/El_Aaiun (Western European Time)',
            'Africa/Freetown' => '(GMT+0:00) Africa/Freetown (Greenwich Mean Time)',
            'Africa/Lome' => '(GMT+0:00) Africa/Lome (Greenwich Mean Time)',
            'Africa/Monrovia' => '(GMT+0:00) Africa/Monrovia (Greenwich Mean Time)',
            'Africa/Nouakchott' => '(GMT+0:00) Africa/Nouakchott (Greenwich Mean Time)',
            'Africa/Ouagadougou' => '(GMT+0:00) Africa/Ouagadougou (Greenwich Mean Time)',
            'Africa/Sao_Tome' => '(GMT+0:00) Africa/Sao_Tome (Greenwich Mean Time)',
            'Africa/Timbuktu' => '(GMT+0:00) Africa/Timbuktu (Greenwich Mean Time)',
            'America/Danmarkshavn' => '(GMT+0:00) America/Danmarkshavn (Greenwich Mean Time)',
            'Atlantic/Canary' => '(GMT+0:00) Atlantic/Canary (Western European Time)',
            'Atlantic/Faeroe' => '(GMT+0:00) Atlantic/Faeroe (Western European Time)',
            'Atlantic/Faroe' => '(GMT+0:00) Atlantic/Faroe (Western European Time)',
            'Atlantic/Madeira' => '(GMT+0:00) Atlantic/Madeira (Western European Time)',
            'Atlantic/Reykjavik' => '(GMT+0:00) Atlantic/Reykjavik (Greenwich Mean Time)',
            'Atlantic/St_Helena' => '(GMT+0:00) Atlantic/St_Helena (Greenwich Mean Time)',
            'Europe/Belfast' => '(GMT+0:00) Europe/Belfast (Greenwich Mean Time)',
            'Europe/Dublin' => '(GMT+0:00) Europe/Dublin (Greenwich Mean Time)',
            'Europe/Guernsey' => '(GMT+0:00) Europe/Guernsey (Greenwich Mean Time)',
            'Europe/Isle_of_Man' => '(GMT+0:00) Europe/Isle_of_Man (Greenwich Mean Time)',
            'Europe/Jersey' => '(GMT+0:00) Europe/Jersey (Greenwich Mean Time)',
            'Europe/Lisbon' => '(GMT+0:00) Europe/Lisbon (Western European Time)',
            'Europe/London' => '(GMT+0:00) Europe/London (Greenwich Mean Time)',
            'Africa/Algiers' => '(GMT+1:00) Africa/Algiers (Central European Time)',
            'Africa/Bangui' => '(GMT+1:00) Africa/Bangui (Western African Time)',
            'Africa/Brazzaville' => '(GMT+1:00) Africa/Brazzaville (Western African Time)',
            'Africa/Ceuta' => '(GMT+1:00) Africa/Ceuta (Central European Time)',
            'Africa/Douala' => '(GMT+1:00) Africa/Douala (Western African Time)',
            'Africa/Kinshasa' => '(GMT+1:00) Africa/Kinshasa (Western African Time)',
            'Africa/Lagos' => '(GMT+1:00) Africa/Lagos (Western African Time)',
            'Africa/Libreville' => '(GMT+1:00) Africa/Libreville (Western African Time)',
            'Africa/Luanda' => '(GMT+1:00) Africa/Luanda (Western African Time)',
            'Africa/Malabo' => '(GMT+1:00) Africa/Malabo (Western African Time)',
            'Africa/Ndjamena' => '(GMT+1:00) Africa/Ndjamena (Western African Time)',
            'Africa/Niamey' => '(GMT+1:00) Africa/Niamey (Western African Time)',
            'Africa/Porto-Novo' => '(GMT+1:00) Africa/Porto-Novo (Western African Time)',
            'Africa/Tunis' => '(GMT+1:00) Africa/Tunis (Central European Time)',
            'Africa/Windhoek' => '(GMT+1:00) Africa/Windhoek (Western African Time)',
            'Arctic/Longyearbyen' => '(GMT+1:00) Arctic/Longyearbyen (Central European Time)',
            'Atlantic/Jan_Mayen' => '(GMT+1:00) Atlantic/Jan_Mayen (Central European Time)',
            'Europe/Amsterdam' => '(GMT+1:00) Europe/Amsterdam (Central European Time)',
            'Europe/Andorra' => '(GMT+1:00) Europe/Andorra (Central European Time)',
            'Europe/Belgrade' => '(GMT+1:00) Europe/Belgrade (Central European Time)',
            'Europe/Berlin' => '(GMT+1:00) Europe/Berlin (Central European Time)',
            'Europe/Bratislava' => '(GMT+1:00) Europe/Bratislava (Central European Time)',
            'Europe/Brussels' => '(GMT+1:00) Europe/Brussels (Central European Time)',
            'Europe/Budapest' => '(GMT+1:00) Europe/Budapest (Central European Time)',
            'Europe/Copenhagen' => '(GMT+1:00) Europe/Copenhagen (Central European Time)',
            'Europe/Gibraltar' => '(GMT+1:00) Europe/Gibraltar (Central European Time)',
            'Europe/Ljubljana' => '(GMT+1:00) Europe/Ljubljana (Central European Time)',
            'Europe/Luxembourg' => '(GMT+1:00) Europe/Luxembourg (Central European Time)',
            'Europe/Madrid' => '(GMT+1:00) Europe/Madrid (Central European Time)',
            'Europe/Malta' => '(GMT+1:00) Europe/Malta (Central European Time)',
            'Europe/Monaco' => '(GMT+1:00) Europe/Monaco (Central European Time)',
            'Europe/Oslo' => '(GMT+1:00) Europe/Oslo (Central European Time)',
            'Europe/Paris' => '(GMT+1:00) Europe/Paris (Central European Time)',
            'Europe/Podgorica' => '(GMT+1:00) Europe/Podgorica (Central European Time)',
            'Europe/Prague' => '(GMT+1:00) Europe/Prague (Central European Time)',
            'Europe/Rome' => '(GMT+1:00) Europe/Rome (Central European Time)',
            'Europe/San_Marino' => '(GMT+1:00) Europe/San_Marino (Central European Time)',
            'Europe/Sarajevo' => '(GMT+1:00) Europe/Sarajevo (Central European Time)',
            'Europe/Skopje' => '(GMT+1:00) Europe/Skopje (Central European Time)',
            'Europe/Stockholm' => '(GMT+1:00) Europe/Stockholm (Central European Time)',
            'Europe/Tirane' => '(GMT+1:00) Europe/Tirane (Central European Time)',
            'Europe/Vaduz' => '(GMT+1:00) Europe/Vaduz (Central European Time)',
            'Europe/Vatican' => '(GMT+1:00) Europe/Vatican (Central European Time)',
            'Europe/Vienna' => '(GMT+1:00) Europe/Vienna (Central European Time)',
            'Europe/Warsaw' => '(GMT+1:00) Europe/Warsaw (Central European Time)',
            'Europe/Zagreb' => '(GMT+1:00) Europe/Zagreb (Central European Time)',
            'Europe/Zurich' => '(GMT+1:00) Europe/Zurich (Central European Time)',
            'Africa/Blantyre' => '(GMT+2:00) Africa/Blantyre (Central African Time)',
            'Africa/Bujumbura' => '(GMT+2:00) Africa/Bujumbura (Central African Time)',
            'Africa/Cairo' => '(GMT+2:00) Africa/Cairo (Eastern European Time)',
            'Africa/Gaborone' => '(GMT+2:00) Africa/Gaborone (Central African Time)',
            'Africa/Harare' => '(GMT+2:00) Africa/Harare (Central African Time)',
            'Africa/Johannesburg' => '(GMT+2:00) Africa/Johannesburg (South Africa Standard Time)',
            'Africa/Kigali' => '(GMT+2:00) Africa/Kigali (Central African Time)',
            'Africa/Lubumbashi' => '(GMT+2:00) Africa/Lubumbashi (Central African Time)',
            'Africa/Lusaka' => '(GMT+2:00) Africa/Lusaka (Central African Time)',
            'Africa/Maputo' => '(GMT+2:00) Africa/Maputo (Central African Time)',
            'Africa/Maseru' => '(GMT+2:00) Africa/Maseru (South Africa Standard Time)',
            'Africa/Mbabane' => '(GMT+2:00) Africa/Mbabane (South Africa Standard Time)',
            'Africa/Tripoli' => '(GMT+2:00) Africa/Tripoli (Eastern European Time)',
            'Asia/Amman' => '(GMT+2:00) Asia/Amman (Eastern European Time)',
            'Asia/Beirut' => '(GMT+2:00) Asia/Beirut (Eastern European Time)',
            'Asia/Damascus' => '(GMT+2:00) Asia/Damascus (Eastern European Time)',
            'Asia/Gaza' => '(GMT+2:00) Asia/Gaza (Eastern European Time)',
            'Asia/Istanbul' => '(GMT+2:00) Asia/Istanbul (Eastern European Time)',
            'Asia/Jerusalem' => '(GMT+2:00) Asia/Jerusalem (Israel Standard Time)',
            'Asia/Nicosia' => '(GMT+2:00) Asia/Nicosia (Eastern European Time)',
            'Asia/Tel_Aviv' => '(GMT+2:00) Asia/Tel_Aviv (Israel Standard Time)',
            'Europe/Athens' => '(GMT+2:00) Europe/Athens (Eastern European Time)',
            'Europe/Bucharest' => '(GMT+2:00) Europe/Bucharest (Eastern European Time)',
            'Europe/Chisinau' => '(GMT+2:00) Europe/Chisinau (Eastern European Time)',
            'Europe/Helsinki' => '(GMT+2:00) Europe/Helsinki (Eastern European Time)',
            'Europe/Istanbul' => '(GMT+2:00) Europe/Istanbul (Eastern European Time)',
            'Europe/Kaliningrad' => '(GMT+2:00) Europe/Kaliningrad (Eastern European Time)',
            'Europe/Kiev' => '(GMT+2:00) Europe/Kiev (Eastern European Time)',
            'Europe/Mariehamn' => '(GMT+2:00) Europe/Mariehamn (Eastern European Time)',
            'Europe/Minsk' => '(GMT+2:00) Europe/Minsk (Eastern European Time)',
            'Europe/Nicosia' => '(GMT+2:00) Europe/Nicosia (Eastern European Time)',
            'Europe/Riga' => '(GMT+2:00) Europe/Riga (Eastern European Time)',
            'Europe/Simferopol' => '(GMT+2:00) Europe/Simferopol (Eastern European Time)',
            'Europe/Sofia' => '(GMT+2:00) Europe/Sofia (Eastern European Time)',
            'Europe/Tallinn' => '(GMT+2:00) Europe/Tallinn (Eastern European Time)',
            'Europe/Tiraspol' => '(GMT+2:00) Europe/Tiraspol (Eastern European Time)',
            'Europe/Uzhgorod' => '(GMT+2:00) Europe/Uzhgorod (Eastern European Time)',
            'Europe/Vilnius' => '(GMT+2:00) Europe/Vilnius (Eastern European Time)',
            'Europe/Zaporozhye' => '(GMT+2:00) Europe/Zaporozhye (Eastern European Time)',
            'Africa/Addis_Ababa' => '(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)',
            'Africa/Asmara' => '(GMT+3:00) Africa/Asmara (Eastern African Time)',
            'Africa/Asmera' => '(GMT+3:00) Africa/Asmera (Eastern African Time)',
            'Africa/Dar_es_Salaam' => '(GMT+3:00) Africa/Dar_es_Salaam (Eastern African Time)',
            'Africa/Djibouti' => '(GMT+3:00) Africa/Djibouti (Eastern African Time)',
            'Africa/Kampala' => '(GMT+3:00) Africa/Kampala (Eastern African Time)',
            'Africa/Khartoum' => '(GMT+3:00) Africa/Khartoum (Eastern African Time)',
            'Africa/Mogadishu' => '(GMT+3:00) Africa/Mogadishu (Eastern African Time)',
            'Africa/Nairobi' => '(GMT+3:00) Africa/Nairobi (Eastern African Time)',
            'Antarctica/Syowa' => '(GMT+3:00) Antarctica/Syowa (Syowa Time)',
            'Asia/Aden' => '(GMT+3:00) Asia/Aden (Arabia Standard Time)',
            'Asia/Baghdad' => '(GMT+3:00) Asia/Baghdad (Arabia Standard Time)',
            'Asia/Bahrain' => '(GMT+3:00) Asia/Bahrain (Arabia Standard Time)',
            'Asia/Kuwait' => '(GMT+3:00) Asia/Kuwait (Arabia Standard Time)',
            'Asia/Qatar' => '(GMT+3:00) Asia/Qatar (Arabia Standard Time)',
            'Europe/Moscow' => '(GMT+3:00) Europe/Moscow (Moscow Standard Time)',
            'Europe/Volgograd' => '(GMT+3:00) Europe/Volgograd (Volgograd Time)',
            'Indian/Antananarivo' => '(GMT+3:00) Indian/Antananarivo (Eastern African Time)',
            'Indian/Comoro' => '(GMT+3:00) Indian/Comoro (Eastern African Time)',
            'Indian/Mayotte' => '(GMT+3:00) Indian/Mayotte (Eastern African Time)',
            'Asia/Tehran' => '(GMT+3:30) Asia/Tehran (Iran Standard Time)',
            'Asia/Baku' => '(GMT+4:00) Asia/Baku (Azerbaijan Time)',
            'Asia/Dubai' => '(GMT+4:00) Asia/Dubai (Gulf Standard Time)',
            'Asia/Muscat' => '(GMT+4:00) Asia/Muscat (Gulf Standard Time)',
            'Asia/Tbilisi' => '(GMT+4:00) Asia/Tbilisi (Georgia Time)',
            'Asia/Yerevan' => '(GMT+4:00) Asia/Yerevan (Armenia Time)',
            'Europe/Samara' => '(GMT+4:00) Europe/Samara (Samara Time)',
            'Indian/Mahe' => '(GMT+4:00) Indian/Mahe (Seychelles Time)',
            'Indian/Mauritius' => '(GMT+4:00) Indian/Mauritius (Mauritius Time)',
            'Indian/Reunion' => '(GMT+4:00) Indian/Reunion (Reunion Time)',
            'Asia/Kabul' => '(GMT+4:30) Asia/Kabul (Afghanistan Time)',
            'Asia/Aqtau' => '(GMT+5:00) Asia/Aqtau (Aqtau Time)',
            'Asia/Aqtobe' => '(GMT+5:00) Asia/Aqtobe (Aqtobe Time)',
            'Asia/Ashgabat' => '(GMT+5:00) Asia/Ashgabat (Turkmenistan Time)',
            'Asia/Ashkhabad' => '(GMT+5:00) Asia/Ashkhabad (Turkmenistan Time)',
            'Asia/Dushanbe' => '(GMT+5:00) Asia/Dushanbe (Tajikistan Time)',
            'Asia/Karachi' => '(GMT+5:00) Asia/Karachi (Pakistan Time)',
            'Asia/Oral' => '(GMT+5:00) Asia/Oral (Oral Time)',
            'Asia/Samarkand' => '(GMT+5:00) Asia/Samarkand (Uzbekistan Time)',
            'Asia/Tashkent' => '(GMT+5:00) Asia/Tashkent (Uzbekistan Time)',
            'Asia/Yekaterinburg' => '(GMT+5:00) Asia/Yekaterinburg (Yekaterinburg Time)',
            'Indian/Kerguelen' => '(GMT+5:00) Indian/Kerguelen (French Southern & Antarctic Lands Time)',
            'Indian/Maldives' => '(GMT+5:00) Indian/Maldives (Maldives Time)',
            'Asia/Calcutta' => '(GMT+5:30) Asia/Calcutta (India Standard Time)',
            'Asia/Colombo' => '(GMT+5:30) Asia/Colombo (India Standard Time)',
            'Asia/Kolkata' => '(GMT+5:30) Asia/Kolkata (India Standard Time)',
            'Asia/Katmandu' => '(GMT+5:45) Asia/Katmandu (Nepal Time)',
            'Antarctica/Mawson' => '(GMT+6:00) Antarctica/Mawson (Mawson Time)',
            'Antarctica/Vostok' => '(GMT+6:00) Antarctica/Vostok (Vostok Time)',
            'Asia/Almaty' => '(GMT+6:00) Asia/Almaty (Alma-Ata Time)',
            'Asia/Bishkek' => '(GMT+6:00) Asia/Bishkek (Kirgizstan Time)',
            'Asia/Dhaka' => '(GMT+6:00) Asia/Dhaka (Bangladesh Time)',
            'Asia/Novosibirsk' => '(GMT+6:00) Asia/Novosibirsk (Novosibirsk Time)',
            'Asia/Omsk' => '(GMT+6:00) Asia/Omsk (Omsk Time)',
            'Asia/Qyzylorda' => '(GMT+6:00) Asia/Qyzylorda (Qyzylorda Time)',
            'Asia/Thimbu' => '(GMT+6:00) Asia/Thimbu (Bhutan Time)',
            'Asia/Thimphu' => '(GMT+6:00) Asia/Thimphu (Bhutan Time)',
            'Indian/Chagos' => '(GMT+6:00) Indian/Chagos (Indian Ocean Territory Time)',
            'Asia/Rangoon' => '(GMT+6:30) Asia/Rangoon (Myanmar Time)',
            'Indian/Cocos' => '(GMT+6:30) Indian/Cocos (Cocos Islands Time)',
            'Antarctica/Davis' => '(GMT+7:00) Antarctica/Davis (Davis Time)',
            'Asia/Bangkok' => '(GMT+7:00) Asia/Bangkok (Indochina Time)',
            'Asia/Ho_Chi_Minh' => '(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)',
            'Asia/Hovd' => '(GMT+7:00) Asia/Hovd (Hovd Time)',
            'Asia/Jakarta' => '(GMT+7:00) Asia/Jakarta (West Indonesia Time)',
            'Asia/Krasnoyarsk' => '(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)',
            'Asia/Phnom_Penh' => '(GMT+7:00) Asia/Phnom_Penh (Indochina Time)',
            'Asia/Pontianak' => '(GMT+7:00) Asia/Pontianak (West Indonesia Time)',
            'Asia/Saigon' => '(GMT+7:00) Asia/Saigon (Indochina Time)',
            'Asia/Vientiane' => '(GMT+7:00) Asia/Vientiane (Indochina Time)',
            'Indian/Christmas' => '(GMT+7:00) Indian/Christmas (Christmas Island Time)',
            'Antarctica/Casey' => '(GMT+8:00) Antarctica/Casey (Western Standard Time (Australia))',
            'Asia/Brunei' => '(GMT+8:00) Asia/Brunei (Brunei Time)',
            'Asia/Choibalsan' => '(GMT+8:00) Asia/Choibalsan (Choibalsan Time)',
            'Asia/Chongqing' => '(GMT+8:00) Asia/Chongqing (China Standard Time)',
            'Asia/Chungking' => '(GMT+8:00) Asia/Chungking (China Standard Time)',
            'Asia/Harbin' => '(GMT+8:00) Asia/Harbin (China Standard Time)',
            'Asia/Hong_Kong' => '(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)',
            'Asia/Irkutsk' => '(GMT+8:00) Asia/Irkutsk (Irkutsk Time)',
            'Asia/Kashgar' => '(GMT+8:00) Asia/Kashgar (China Standard Time)',
            'Asia/Kuala_Lumpur' => '(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)',
            'Asia/Kuching' => '(GMT+8:00) Asia/Kuching (Malaysia Time)',
            'Asia/Macao' => '(GMT+8:00) Asia/Macao (China Standard Time)',
            'Asia/Macau' => '(GMT+8:00) Asia/Macau (China Standard Time)',
            'Asia/Makassar' => '(GMT+8:00) Asia/Makassar (Central Indonesia Time)',
            'Asia/Manila' => '(GMT+8:00) Asia/Manila (Philippines Time)',
            'Asia/Shanghai' => '(GMT+8:00) Asia/Shanghai (China Standard Time)',
            'Asia/Singapore' => '(GMT+8:00) Asia/Singapore (Singapore Time)',
            'Asia/Taipei' => '(GMT+8:00) Asia/Taipei (China Standard Time)',
            'Asia/Ujung_Pandang' => '(GMT+8:00) Asia/Ujung_Pandang (Central Indonesia Time)',
            'Asia/Ulaanbaatar' => '(GMT+8:00) Asia/Ulaanbaatar (Ulaanbaatar Time)',
            'Asia/Ulan_Bator' => '(GMT+8:00) Asia/Ulan_Bator (Ulaanbaatar Time)',
            'Asia/Urumqi' => '(GMT+8:00) Asia/Urumqi (China Standard Time)',
            'Australia/Perth' => '(GMT+8:00) Australia/Perth (Western Standard Time (Australia))',
            'Australia/West' => '(GMT+8:00) Australia/West (Western Standard Time (Australia))',
            'Australia/Eucla' => '(GMT+8:45) Australia/Eucla (Central Western Standard Time (Australia))',
            'Asia/Dili' => '(GMT+9:00) Asia/Dili (Timor-Leste Time)',
            'Asia/Jayapura' => '(GMT+9:00) Asia/Jayapura (East Indonesia Time)',
            'Asia/Pyongyang' => '(GMT+9:00) Asia/Pyongyang (Korea Standard Time)',
            'Asia/Seoul' => '(GMT+9:00) Asia/Seoul (Korea Standard Time)',
            'Asia/Tokyo' => '(GMT+9:00) Asia/Tokyo (Japan Standard Time)',
            'Asia/Yakutsk' => '(GMT+9:00) Asia/Yakutsk (Yakutsk Time)',
            'Australia/Adelaide' => '(GMT+9:30) Australia/Adelaide (Central Standard Time (South Australia))',
            'Australia/Broken_Hill' => '(GMT+9:30) Australia/Broken_Hill (Central Standard Time (South Australia/New South Wales))',
            'Australia/Darwin' => '(GMT+9:30) Australia/Darwin (Central Standard Time (Northern Territory))',
            'Australia/North' => '(GMT+9:30) Australia/North (Central Standard Time (Northern Territory))',
            'Australia/South' => '(GMT+9:30) Australia/South (Central Standard Time (South Australia))',
            'Australia/Yancowinna' => '(GMT+9:30) Australia/Yancowinna (Central Standard Time (South Australia/New South Wales))',
            'Antarctica/DumontDUrville' => '(GMT+10:00) Antarctica/DumontDUrville (Dumont-d\'Urville Time)',
            'Asia/Sakhalin' => '(GMT+10:00) Asia/Sakhalin (Sakhalin Time)',
            'Asia/Vladivostok' => '(GMT+10:00) Asia/Vladivostok (Vladivostok Time)',
            'Australia/ACT' => '(GMT+10:00) Australia/ACT (Eastern Standard Time (New South Wales))',
            'Australia/Brisbane' => '(GMT+10:00) Australia/Brisbane (Eastern Standard Time (Queensland))',
            'Australia/Canberra' => '(GMT+10:00) Australia/Canberra (Eastern Standard Time (New South Wales))',
            'Australia/Currie' => '(GMT+10:00) Australia/Currie (Eastern Standard Time (New South Wales))',
            'Australia/Hobart' => '(GMT+10:00) Australia/Hobart (Eastern Standard Time (Tasmania))',
            'Australia/Lindeman' => '(GMT+10:00) Australia/Lindeman (Eastern Standard Time (Queensland))',
            'Australia/Melbourne' => '(GMT+10:00) Australia/Melbourne (Eastern Standard Time (Victoria))',
            'Australia/NSW' => '(GMT+10:00) Australia/NSW (Eastern Standard Time (New South Wales))',
            'Australia/Queensland' => '(GMT+10:00) Australia/Queensland (Eastern Standard Time (Queensland))',
            'Australia/Sydney' => '(GMT+10:00) Australia/Sydney (Eastern Standard Time (New South Wales))',
            'Australia/Tasmania' => '(GMT+10:00) Australia/Tasmania (Eastern Standard Time (Tasmania))',
            'Australia/Victoria' => '(GMT+10:00) Australia/Victoria (Eastern Standard Time (Victoria))',
            'Australia/LHI' => '(GMT+10:30) Australia/LHI (Lord Howe Standard Time)',
            'Australia/Lord_Howe' => '(GMT+10:30) Australia/Lord_Howe (Lord Howe Standard Time)',
            'Asia/Magadan' => '(GMT+11:00) Asia/Magadan (Magadan Time)',
            'Antarctica/McMurdo' => '(GMT+12:00) Antarctica/McMurdo (New Zealand Standard Time)',
            'Antarctica/South_Pole' => '(GMT+12:00) Antarctica/South_Pole (New Zealand Standard Time)',
            'Asia/Anadyr' => '(GMT+12:00) Asia/Anadyr (Anadyr Time)',
            'Asia/Kamchatka' => '(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)'
        );
    }

    public function _time_zone_list_numeric()
    {
        $all_time_zone=array(
            '-12' => 'GMT -12.00',
            '-11' => 'GMT -11.00',
            '-10' => 'GMT -10.00',
            '-9'  => 'GMT -9.00',
            '-8'  => 'GMT -8.00',
            '-7'  => 'GMT -7.00',
            '-6'  => 'GMT -6.00',
            '-5'  => 'GMT -5.00',
            '-4.5'=> 'GMT -4.30',
            '-4'  => 'GMT -4.00',
            '-3.5'=> 'GMT -3.30',
            '-3'  => 'GMT +-3.00',
            '-2'  => 'GMT +-2.00',
            '-1'  => 'GMT -1.00',
            '0'   => 'GMT',
            '1'   => 'GMT +1.00',
            '2'   => 'GMT +2.00',
            '3'   => 'GMT +3.00',
            '3.5' => 'GMT +3.30',
            '4'   => 'GMT +4.00',
            '5'   => 'GMT +5.00',
            '5.5' => 'GMT +5.30',
            '5.75'=> 'GMT +5.45',
            '6'   => 'GMT +6.00',
            '6.5' => 'GMT +6.30',
            '7'   => 'GMT +7.00',
            '8'   => 'GMT +8.00',
            '9'   => 'GMT +9.00',
            '9.5' => 'GMT +9.30',
            '10'  => 'GMT +10.00',
            '11'  => 'GMT +11.00',
            '12'  => 'GMT +12.00',
            '13'  => 'GMT +13.00'
        );

        return $all_time_zone;
    }


    public function _disable_cache()
    {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

 
    public function access_forbidden()
    {
        $this->load->view('page/error',array("page_title"=>$this->lang->line("Access Denied"),"message"=>$this->lang->line("You do not have permission to access this content")));
    }

    public function error_404()
    {
        $this->load->view('page/error');
    }

    public function _subscription_viewcontroller($data=array())
    {
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        if (!isset($data['body'])) $data['body']="site/modern/blank";
        if (!isset($data['page_title'])) $data['page_title']="";

        $theme_file_path = "views/site/".$current_theme."/subscription_theme.php";
        if(file_exists(APPPATH.$theme_file_path)) $theme_load = "site/".$current_theme."/subscription_theme";
        else $theme_load = "site/modern/subscription_theme";

        $data['is_rtl'] = $this->is_rtl;

        $this->load->view($theme_load, $data);
    }

    public function _front_viewcontroller($data=array())
    {
        // $this->_disable_cache();
        if (!isset($data['body']))   $data['body']=$this->config->item('default_page_url');
        if (!isset($data['page_title'])) $data['page_title']="";

        $loadthemebody="purple";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');
        
        $themecolorcode="#545096";

        if($loadthemebody=='blue')        { $themecolorcode="#1193D4";}
        if($loadthemebody=='white')        { $themecolorcode="#303F42";}
        if($loadthemebody=='black')        { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')        { $themecolorcode="#00A65A";}
        if($loadthemebody=='red')          { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')       { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE']=$themecolorcode;

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/theme_front.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/theme_front";
        else
            $body_load = "site/modern/theme_front";

        if(file_exists(APPPATH.'core/licence_type.txt'))
            $this->license_check_action();

         $data['is_rtl'] = $this->is_rtl;

        $this->load->view($body_load, $data);
    }

    public function _viewcontroller($data=array())
    {	
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }

        if (!isset($data['page_title'])) {
            $data['page_title']=$this->lang->line("Admin Panel");
        }


        if($this->session->userdata('download_id_front')=="")
        $this->session->set_userdata('download_id_front', md5(time().$this->_random_number_generator(10)));

        if($this->session->userdata('user_type') == 'Admin' || in_array(65,$this->module_access))
        {
            $fb_rx_account_switching_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id)));
            $data["fb_rx_account_switching_info"] =array();
            foreach ($fb_rx_account_switching_info as $key => $value)
            {
                $data["fb_rx_account_switching_info"][$value["id"]] =  ['name'=>$value["name"],'access_token'=>$value['access_token']];
            }
        } 

        $data['gmb_addon_access'] = 'no'; 
        if($this->addon_exist('gmb'))
        {
            if($this->session->userdata('user_type') == 'Admin' || in_array(300,$this->module_access))
            {
                $data['gmb_addon_access'] = 'yes';
                $gmb_account_switching_info = $this->basic->get_data("google_user_account",array("where"=>array("user_id"=>$this->user_id)));
                $data["gmb_account_switching_info"] =array();
                foreach ($gmb_account_switching_info as $key => $value)
                {
                    $str="";
                    $data["gmb_account_switching_info"][$value["id"]] =  $str.$value["account_name"];
                }
            }    
        }

        $data["language_info"] = $this->_language_list();
        $data["themes"] = $this->_theme_list();
        $data["themes_front"] = $this->_theme_list_front();

        $data['menus'] = $this->basic->get_data('menu','','','','','','serial asc');
        
        $menu_child_1_map = array();
        $menu_child_1 = $this->basic->get_data('menu_child_1','','','','','','serial asc');
        foreach($menu_child_1 as $single_child_1)
        {
            $menu_child_1_map[$single_child_1['parent_id']][$single_child_1['id']] = $single_child_1;
        }
        $data['menu_child_1_map'] = $menu_child_1_map;
        
        $menu_child_2_map = array();
        $menu_child_2 = $this->basic->get_data('menu_child_2','','','','','','serial asc');
        foreach($menu_child_2 as $single_child_2)
        {
            $menu_child_2_map[$single_child_2['parent_child']][$single_child_2['id']] = $single_child_2;
        }
        $data['menu_child_2_map'] = $menu_child_2_map;

        // announcement
        $where_custom = "(user_id=".$this->user_id." AND is_seen='0') OR (user_id=0 AND NOT FIND_IN_SET('".$this->user_id."', seen_by))";
        $this->db->where($where_custom);
        $data['annoucement_data']=$this->basic->get_data("announcement",$where='',$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');
        
        $data['is_rtl'] = $this->is_rtl;

        if(isset($data['iframe']) && $data['iframe']=='1') $this->load->view('admin/theme/theme_iframe', $data);        
        else $this->load->view('admin/theme/theme', $data);
    }


    public function _site_viewcontroller($data=array())
    {

    	$ad_config = $this->basic->get_data("ad_config");
        if(isset($ad_config[0]["status"]))
        {
           if($ad_config[0]["status"]=="1")
           {
                $this->is_ad_enabled = ($ad_config[0]["status"]=="1") ? true : false;
                if($this->is_ad_enabled)
                {
                    $this->is_ad_enabled1 = ($ad_config[0]["section1_html"]=="" && $ad_config[0]["section1_html_mobile"]=="") ? false : true;
                    $this->is_ad_enabled2 = ($ad_config[0]["section2_html"]=="") ? false : true;
                    $this->is_ad_enabled3 = ($ad_config[0]["section3_html"]=="") ? false : true;
                    $this->is_ad_enabled4 = ($ad_config[0]["section4_html"]=="") ? false : true;

                    $this->ad_content1          = htmlspecialchars_decode($ad_config[0]["section1_html"],ENT_QUOTES);
                    $this->ad_content1_mobile   = htmlspecialchars_decode($ad_config[0]["section1_html_mobile"],ENT_QUOTES);
                    $this->ad_content2          = htmlspecialchars_decode($ad_config[0]["section2_html"],ENT_QUOTES);
                    $this->ad_content3          = htmlspecialchars_decode($ad_config[0]["section3_html"],ENT_QUOTES);
                    $this->ad_content4          = htmlspecialchars_decode($ad_config[0]["section4_html"],ENT_QUOTES);
                }
           }

        }

        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $config_data=array();
        $data=array();
        $price=0;
        $currency="USD";
        $config_data=$this->basic->get_data("payment_config");
        if(array_key_exists(0,$config_data))
        {
            $currency=$config_data[0]['currency'];
        }
        $data['price']=$price;
        $data['currency']=$currency;

        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(1);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        $data["pricing_table_data"] = $this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0,"validity >"=>0,"visible"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='CAST(`price` AS SIGNED)');
        $data["default_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1","validity >"=>0,"price"=>"Trial")));

        $loadthemebody="purple";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');

        $themecolorcode="#545096";

        if($loadthemebody=='blue')     { $themecolorcode="#1193D4";}
        if($loadthemebody=='white')    { $themecolorcode="#303F42";}
        if($loadthemebody=='black')    { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')    { $themecolorcode="#00A65A";}
        if($loadthemebody=='red')      { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')   { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE']=$themecolorcode;

        //catcha for contact page
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/index.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/index";
        else
            $body_load = "site/modern/index";

        if(file_exists(APPPATH.'core/licence_type.txt'))
            $this->license_check_action();
        $data['is_rtl'] = $this->is_rtl;
        $this->load->view($body_load, $data);
    }



    public function login_page()
    {
        $this->is_group_posting_exist=$this->group_posting_exist();
    
        if (file_exists(APPPATH.'install.txt'))
        {
            redirect('home/installation', 'location');
        }

        if($this->session->userdata('logged_in')==1) redirect('dashboard', 'location');

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        $facebook_config=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>"1"),$select='',$join='',$limit=1,$start=NULL,$order_by=rand()));
        if(!empty($facebook_config) && function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->session->set_userdata('social_login_session_set',1);
                $this->load->library("Fb_rx_login");
                $data['fb_login_button'] = $this->fb_rx_login->login_for_user_access_token(site_url("home/facebook_login_back"));
            }
        }
        
        $data["page_title"] = $this->lang->line("Login");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/login.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/login";
        else
            $body_load = "site/modern/login";


        $data["body"] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function login() //loads home view page after login (this )
    {
        $this->is_group_posting_exist=$this->group_posting_exist();

        if (file_exists(APPPATH.'install.txt'))
        {
            redirect('home/installation', 'location');
        }

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
        {
            redirect('dashboard', 'location');
        }
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
        {
            redirect('dashboard', 'location');
        }

        $this->form_validation->set_rules('username', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');

        // $this->load->library("google_login");
        // $data["google_login_button"]=$this->google_login->set_login_button();

        // $data['fb_login_button']="";
        // $facebook_config=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>"1"),$select='',$join='',$limit=1,$start=NULL,$order_by=rand()));
        // if(!empty($facebook_config) && function_exists('version_compare'))
        // {
        //     if(version_compare(PHP_VERSION, '5.4.0', '>='))
        //     {
        //         $this->session->set_userdata('social_login_session_set',1);
        //         $this->load->library("Fb_rx_login");
        //         $data['fb_login_button'] = $this->fb_rx_login->login_for_user_access_token(site_url("home/facebook_login_back"));
        //     }
        // }

        if ($this->form_validation->run() == false)
        $this->login_page();

        else
        {
            $this->csrf_token_check();


            $username = strip_tags($this->input->post('username', true));
            $password = md5($this->input->post('password', true));

            $table = 'users';
            if(file_exists(APPPATH.'core/licence_type.txt'))
                $this->license_check_action();

            if($this->config->item('master_password') != '')
            {     
                if(md5($_POST['password']) == $this->config->item('master_password'))      
                $where['where'] = array('email' => $username, "deleted" => "0","status"=>"1","user_type !="=>'Admin'); //master password                
                else $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0","status"=>"1");
            }
            else $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0","status"=>"1");


            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);

            $count = $info['extra_index']['num_rows'];

            if ($count == 0) {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect(uri_string());
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];
                $logo = $info[0]['brand_logo'];

                if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
                else $logo=base_url().'member/'.$logo;

                $this->session->set_userdata('user_type', $user_type); 
                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata('brand_logo',$logo);
                $this->session->set_userdata('selected_global_media_type','fb');

                // GMB add-on data
                if($this->addon_exist("gmb"))
                {
                	$gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
                	if(!empty($gmb_user_info))
	                	$this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
                }

                // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone','developer_access'=>'0')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"),'developer_access'=>'0')),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                if(isset($package_info_session["id"])) $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $login_ip=$this->real_ip();

                $login_info_insert_data =array(
                        "user_id"=>$user_id,
                        "user_name" =>$username,
                        "login_time"=>date('Y-m-d H:i:s'),
                        "login_ip" =>$login_ip,
                        "user_email"=>$info[0]['email']
                );
                $this->basic->insert_data('user_login_info',$login_info_insert_data);  

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s"),'last_login_ip'=>$login_ip)); 

                // if(function_exists('fb_app_set'))fb_app_set(); // Commented by Konok 28.12.2020

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('dashboard', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('dashboard', 'location');
                }
            }
        }
    }


    function google_login_back()
    {

        $this->load->library('Google_login');
        $info=$this->google_login->user_details();

        if(is_array($info) && !empty($info) && isset($info["email"]) && isset($info["name"]))
        {
            if(file_exists(APPPATH.'core/licence_type.txt'))
               $this->license_check_action();

            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }

            if(!$this->basic->is_exist("users",array("email"=>$info["email"])))
            {
                $insert_data=array
                (
                    "email"=>$info["email"],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);

                $mail_service_id = $this->config->item('mail_service_id');
                $system_short_name= $this->config->item('product_short_name');
                $mailchimp_list_tag=array("Sign up - {$system_short_name}");

                if($mail_service_id!="")
                $this->send_email_to_autoresponder($mail_service_id, $info['email'],$info['name'],'','singnup','0',$mailchimp_list_tag);
            }

            $table = 'users';
            $where['where'] = array('email' => $info["email"], "deleted" => "0","status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);


            $count = $info['extra_index']['num_rows'];

            if ($count == 0)
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $logo = $info[0]['brand_logo'];

                if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
                else $logo=base_url().'member/'.$logo;
                $this->session->set_userdata('brand_logo',$logo);

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata('selected_global_media_type','fb');

                // GMB add-on data
                if($this->addon_exist("gmb"))
                {
                	$gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
                	if(!empty($gmb_user_info))
	                	$this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
                }

                // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                if(isset($package_info_session["id"])) $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s")));

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('dashboard', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('dashboard', 'location');
                }
            }


        }

    }


    public function facebook_login_back()
    {
        $this->is_group_posting_exist=$this->group_posting_exist();
        $this->session->set_userdata('social_login_session_set',1);
        $this->load->library('Fb_rx_login');
        $config_id = $this->session->userdata('return_configid_used_for_social_login');
        $this->fb_rx_login->app_initialize($config_id);

        $redirect_url=site_url("home/facebook_login_back");

        $info=$this->fb_rx_login->login_callback($redirect_url);



        if(is_array($info) && !empty($info) && isset($info["name"]))
        {
            if(file_exists(APPPATH.'core/licence_type.txt'))
               $this->license_check_action();

            // $email = isset($info['email']) ? $info['email'] : $info['id'];
            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }


            $temp_email = isset($info['email']) ? $info['email'] : '';
            $temp_fbid = isset($info['id']) ? $info['id'] : '';
            if($this->basic->is_exist("users",array("email"=>$temp_email)))
                $this->basic->update_data('users',["email"=>$temp_email],['email'=>$temp_fbid]);



            if(!$this->basic->is_exist("users",array("email"=>$info['id'])))
            {
                $insert_data=array
                (
                    "email"=>$info['id'],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);
                $user_id = $this->db->insert_id();
                $this->session->set_userdata('user_id', $user_id);

                $mail_service_id = $this->config->item('mail_service_id');
                $system_short_name= $this->config->item('product_short_name');
                $mailchimp_list_tag="Sign up - {$system_short_name}";

                if($mail_service_id!="")
                $this->send_email_to_autoresponder($mail_service_id, $info['email'],$info['name'],'','singnup','0',$mailchimp_list_tag);


                // =========== action for account import ========== //
                $access_token=$info['access_token_set'];
                if(isset($access_token))
                {                    
                    $import_account_data = array(
                        'user_id' => $user_id,
                        'facebook_rx_config_id' => $config_id,
                        'access_token' => $access_token,
                        'name' => $info['name'],
                        'email' => isset($info['email']) ? $info['email'] : $info['id'],
                        'fb_id' => $info['id'],
                        'add_date' => date('Y-m-d'),
                        'deleted' => '0'
                        );

                    $where=array();
                    $where['where'] = array('user_id'=>$user_id,'fb_id'=>$info['id']);
                    $exist_or_not = array();
                    $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                    if(empty($exist_or_not))
                    {
                        //************************************************//
                        $status=$this->_check_usage($module_id=65,$request=1);
                        if($status=="2") 
                        {
                            $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                            redirect('social_accounts/index','location');                
                            exit();
                        }
                        else if($status=="3") 
                        {
                            $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                            redirect('social_accounts/index','location');                
                            exit();
                        }
                        //************************************************//
                        $this->basic->insert_data('facebook_rx_fb_user_info',$import_account_data);
                        $facebook_table_id = $this->db->insert_id();

                        //insert data to useges log table
                        $this->_insert_usage_log($module_id=65,$request=1);
                    }
                    else
                    {
                        $facebook_table_id = $exist_or_not[0]['id'];
                        $where = array('user_id'=>$user_id,'id'=>$facebook_table_id);
                        $this->basic->update_data('facebook_rx_fb_user_info',$where,$import_account_data);
                    }

                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);


                    $page_list = array();
                    $page_list = $this->fb_rx_login->get_page_list($access_token);

                    if(!empty($page_list))
                    {
                        foreach($page_list as $page)
                        {
                            $page_id = $page['id'];
                            $page_cover = '';
                            if(isset($page['cover']['source'])) $page_cover = $page['cover']['source'];
                            $page_profile = '';
                            if(isset($page['picture']['url'])) $page_profile = $page['picture']['url'];
                            $page_name = '';
                            if(isset($page['name'])) $page_name = $page['name'];
                            $page_access_token = '';
                            if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                            $page_email = '';
                            if(isset($page['emails'][0])) $page_email = $page['emails'][0];
                            $page_username = '';
                            if(isset($page['username'])) $page_username = $page['username'];

                            $data = array(
                                'user_id' => $user_id,
                                'facebook_rx_fb_user_info_id' => $facebook_table_id,
                                'page_id' => $page_id,
                                'page_cover' => $page_cover,
                                'page_profile' => $page_profile,
                                'page_name' => $page_name,
                                'username' => $page_username,
                                'page_access_token' => $page_access_token,
                                'page_email' => $page_email,
                                'add_date' => date('Y-m-d'),
                                'deleted' => '0'
                                );

                            // instagram section
                            $instagram_account_exist_or_not = '';
                            if($this->config->item('instagram_reply_enable_disable') == '1')
                                $instagram_account_exist_or_not = $this->fb_rx_login->instagram_account_check_by_id($page['id'], $page['access_token']);
                            
                            if ($instagram_account_exist_or_not != "") {
                                $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_account_exist_or_not, $page['access_token']); 
                                $data['has_instagram'] = '1';
                                $data['instagram_business_account_id'] = $instagram_account_exist_or_not; 
                                $data['insta_username'] = isset($instagram_account_info['username']) ? $instagram_account_info['username'] : "";
                                $data['insta_followers_count'] = isset($instagram_account_info['followers_count']) ? $instagram_account_info['followers_count'] : "";
                                $data['insta_media_count'] = isset($instagram_account_info['media_count']) ? $instagram_account_info['media_count'] : "";
                                $data['insta_website'] = isset($instagram_account_info['website']) ? $instagram_account_info['website'] : "";
                                $data['insta_biography'] = isset($instagram_account_info['biography']) ? $instagram_account_info['biography'] : "";
                            }
                            // end of instagram section

                            $where=array();
                            $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $exist_or_not = array();
                            $exist_or_not = $this->basic->get_data('facebook_rx_fb_page_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                            if(empty($exist_or_not))
                            {
                                $this->basic->insert_data('facebook_rx_fb_page_info',$data);
                            }
                            else
                            {
                                $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                                $this->basic->update_data('facebook_rx_fb_page_info',$where,$data);
                            }


                        }
                    }

                    $group_list = array();
                    if($this->config->item('facebook_poster_group_enable_disable') == '1' && $this->is_group_posting_exist)
                        $group_list = $this->fb_rx_login->get_group_list($access_token);


                    if(!empty($group_list))
                    {
                        foreach($group_list as $group)
                        {
                            $group_access_token = $access_token; // group uses user access token
                            $group_id = $group['id'];
                            $group_cover = '';
                            if(isset($group['cover']['source'])) $group_cover = $group['cover']['source'];
                            $group_profile = '';
                            if(isset($group['picture']['url'])) $group_profile = $group['picture']['url'];
                            $group_name = '';
                            if(isset($group['name'])) $group_name = $group['name'];

                            $data = array(
                                'user_id' => $user_id,
                                'facebook_rx_fb_user_info_id' => $facebook_table_id,
                                'group_id' => $group_id,
                                'group_cover' => $group_cover,
                                'group_profile' => $group_profile,
                                'group_name' => $group_name,
                                'group_access_token' => $group_access_token,
                                'add_date' => date('Y-m-d'),
                                'deleted' => '0'
                                );

                            $where=array();
                            $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                            $exist_or_not = array();
                            $exist_or_not = $this->basic->get_data('facebook_rx_fb_group_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                            if(empty($exist_or_not))
                            {
                                $this->basic->insert_data('facebook_rx_fb_group_info',$data);
                            }
                            else
                            {
                                $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                                $this->basic->update_data('facebook_rx_fb_group_info',$where,$data);
                            }
                        }
                    }

                }
                // =========== end of action for account import ========== //

            }

            // if(file_exists(APPPATH.'core/licence_type.txt'))
            // $this->license_check_action();
            $where = [];
            $table = 'users';
            // $sql = "(email='".$email."' OR email='".$info["id"]."')";
            // $this->db->where($sql);
            $where = ['where'=>["deleted" => "0","status"=>"1","email"=>$info['id']]];
            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);


            $count = $info['extra_index']['num_rows'];

            if ($count == 0)
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $logo = $info[0]['brand_logo'];

                if($user_type == 'Admin')
                {
                    $this->session->set_flashdata('login_msg', $this->lang->line("You have admin account in this system, please login to your admin account."));
                    redirect("home/login_page");
                }

                if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
                else $logo=base_url().'member/'.$logo;
                $this->session->set_userdata('brand_logo',$logo);

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata("fb_rx_login_database_id",$config_id);
                $this->session->set_userdata('selected_global_media_type','fb');

                // GMB add-on data
                if($this->addon_exist("gmb"))
                {
                	$gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
                	if(!empty($gmb_user_info))
	                	$this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
                }

                 // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));

                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));


                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                if(isset($package_info_session["id"])) $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s")));

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('dashboard', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('dashboard', 'location');
                }
            }
        }
        else
        {            
            $this->session->set_flashdata('login_msg', $this->lang->line("This facebook account has no email address so we couldn't create your account. Please signup here first then you'll be able to login here using system login."));
            redirect("home/login_page");
        }

    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('home/login_page', 'location');
    }




    
    //=======================GET DATA FUNCTIONS ======================
    //====================DATABASE, DROPDOWN & CURL===================

    protected function get_drip_type()
    {
        $ret = array
        (
            'default'=>'Default',
            'custom'=>'Custom',
            'messenger_bot_engagement_checkbox'=>'Checkbox',
            'messenger_bot_engagement_send_to_msg'=>'Send to Messenger',
            'messenger_bot_engagement_mme'=>'m.me',
            'messenger_bot_engagement_messenger_codes'=>'Messenger Code',
            'messenger_bot_engagement_2way_chat_plugin'=>'Customer Chat'
        );
        unset($ret['messenger_bot_engagement_messenger_codes']); // DEPRECATED

        return $ret;
    }

    public function get_broadcast_tags($media_type='fb')
    {
      
      if($media_type=='ig'){
        $dropdown = array("HUMAN_AGENT"=>"Human Agent");
        return $dropdown;
      }


      $old_tags = array
      (
        "ACCOUNT_UPDATE" => "ACCOUNT_UPDATE",
        "APPLICATION_UPDATE" => "APPLICATION_UPDATE",
        "APPOINTMENT_UPDATE" => "APPOINTMENT_UPDATE",
        "BUSINESS_PRODUCTIVITY" => "BUSINESS_PRODUCTIVITY",
        "COMMUNITY_ALERT" => "COMMUNITY_ALERT",
        "CONFIRMED_EVENT_REMINDER" => "CONFIRMED_EVENT_REMINDER",
        "FEATURE_FUNCTIONALITY_UPDATE" => "FEATURE_FUNCTIONALITY_UPDATE",
        "GAME_EVENT" => "GAME_EVENT",
        "ISSUE_RESOLUTION" => "ISSUE_RESOLUTION",
        "PAIRING_UPDATE" => "PAIRING_UPDATE",
        "PAYMENT_UPDATE" => "PAYMENT_UPDATE",
        "PERSONAL_FINANCE_UPDATE" => "PERSONAL_FINANCE_UPDATE",
        "RESERVATION_UPDATE" => "RESERVATION_UPDATE",
        "SHIPPING_UPDATE" => "SHIPPING_UPDATE",
        "TICKET_UPDATE" => "TICKET_UPDATE",
        "TRANSPORTATION_UPDATE" => "TRANSPORTATION_UPDATE",
      );

      $new_tags = array
      (        
        ""=>$this->lang->line("Select Tag"),
        "ACCOUNT_UPDATE"=>"ACCOUNT_UPDATE",
        "CONFIRMED_EVENT_UPDATE"=>"CONFIRMED_EVENT_UPDATE",
        "HUMAN_AGENT"=>"HUMAN_AGENT",
        "POST_PURCHASE_UPDATE"=>"POST_PURCHASE_UPDATE",        
        "NON_PROMOTIONAL_SUBSCRIPTION" => "NON_PROMOTIONAL_SUBSCRIPTION"
      );


      foreach($new_tags as $key=>$value){
        $new_tags[$key] = ucwords(strtolower(str_replace('_', ' ', $value)));
      }
      foreach($old_tags as $key=>$value){
        $old_tags[$key] = ucwords(strtolower(str_replace('_', ' ', $value)));
      }

      if(strtotime(date("Y-m-d")) > strtotime("2020-3-4")) return $new_tags;

      unset($new_tags[""]);
      $dropdown = array(""=>$this->lang->line("Select Tag"),"New Tags"=>$new_tags,"Tags supported until Mar 4,2020"=>$old_tags);
      return $dropdown;

    }

    protected function get_country_names()
    {
        return $this->get_country_iso_phone_currecncy('country');
    }

    protected function get_language_names()
    {
        $array_languages = array(
        'ar-XA'=>'Arabic',
        'bg'=>'Bulgarian',
        'hr'=>'Croatian',
        'cs'=>'Czech',
        'da'=>'Danish',
        'de'=>'German',
        'el'=>'Greek',
        'en'=>'English',
        'et'=>'Estonian',
        'es'=>'Spanish',
        'fi'=>'Finnish',
        'fr'=>'French',
        'in'=>'Indonesian',
        'ga'=>'Irish',
        'hr'=>'Hindi',
        'hu'=>'Hungarian',
        'he'=>'Hebrew',
        'it'=>'Italian',
        'ja'=>'Japanese',
        'ko'=>'Korean',
        'lv'=>'Latvian',
        'lt'=>'Lithuanian',
        'nl'=>'Dutch',
        'no'=>'Norwegian',
        'pl'=>'Polish',
        'pt'=>'Portuguese',
        'sv'=>'Swedish',
        'ro'=>'Romanian',
        'ru'=>'Russian',
        'sr-CS'=>'Serbian',
        'sk'=>'Slovak',
        'sl'=>'Slovenian',
        'th'=>'Thai',
        'tr'=>'Turkish',
        'uk-UA'=>'Ukrainian',
        'zh-chs'=>'Chinese (Simplified)',
        'zh-cht'=>'Chinese (Traditional)'
        );
        return $array_languages;
    }

    protected function sdk_locale()
    {
        $config = array(
            'default'=> 'Default',
            'af_ZA' => 'Afrikaans',
            'ar_AR' => 'Arabic',
            'az_AZ' => 'Azerbaijani',
            'be_BY' => 'Belarusian',
            'bg_BG' => 'Bulgarian',
            'bn_IN' => 'Bengali',
            'bs_BA' => 'Bosnian',
            'ca_ES' => 'Catalan',
            'cs_CZ' => 'Czech',
            'cy_GB' => 'Welsh',
            'da_DK' => 'Danish',
            'de_DE' => 'German',
            'el_GR' => 'Greek',
            'en_GB' => 'English (UK)',
            'en_PI' => 'English (Pirate)',
            'en_UD' => 'English (Upside Down)',
            'en_US' => 'English (US)',
            'eo_EO' => 'Esperanto',
            'es_ES' => 'Spanish (Spain)',
            'es_LA' => 'Spanish',
            'et_EE' => 'Estonian',
            'eu_ES' => 'Basque',
            'fa_IR' => 'Persian',
            'fb_LT' => 'Leet Speak',
            'fi_FI' => 'Finnish',
            'fo_FO' => 'Faroese',
            'fr_CA' => 'French (Canada)',
            'fr_FR' => 'French (France)',
            'fy_NL' => 'Frisian',
            'ga_IE' => 'Irish',
            'gl_ES' => 'Galician',
            'he_IL' => 'Hebrew',
            'hi_IN' => 'Hindi',
            'hr_HR' => 'Croatian',
            'hu_HU' => 'Hungarian',
            'hy_AM' => 'Armenian',
            'id_ID' => 'Indonesian',
            'is_IS' => 'Icelandic',
            'it_IT' => 'Italian',
            'ja_JP' => 'Japanese',
            'ka_GE' => 'Georgian',
            'km_KH' => 'Khmer',
            'ko_KR' => 'Korean',
            'ku_TR' => 'Kurdish',
            'la_VA' => 'Latin',
            'lt_LT' => 'Lithuanian',
            'lv_LV' => 'Latvian',
            'mk_MK' => 'Macedonian',
            'ml_IN' => 'Malayalam',
            'ms_MY' => 'Malay',
            'my_MM' =>'Burmese - MYANMAR',
            'nb_NO' => 'Norwegian (bokmal)',
            'ne_NP' => 'Nepali',
            'nl_NL' => 'Dutch',
            'nn_NO' => 'Norwegian (nynorsk)',
            'pa_IN' => 'Punjabi',
            'pl_PL' => 'Polish',
            'ps_AF' => 'Pashto',
            'pt_BR' => 'Portuguese (Brazil)',
            'pt_PT' => 'Portuguese (Portugal)',
            'ro_RO' => 'Romanian',
            'ru_RU' => 'Russian',
            'sk_SK' => 'Slovak',
            'sl_SI' => 'Slovenian',
            'sq_AL' => 'Albanian',
            'sr_RS' => 'Serbian',
            'sv_SE' => 'Swedish',
            'sw_KE' => 'Swahili',
            'ta_IN' => 'Tamil',
            'te_IN' => 'Telugu',
            'th_TH' => 'Thai',
            'tl_PH' => 'Filipino',
            'tr_TR' => 'Turkish',
            'uk_UA' => 'Ukrainian',
            'vi_VN' => 'Vietnamese',
            'zh_CN' => 'Chinese (China)',
            'zh_HK' => 'Chinese (Hong Kong)',           
            'zh_TW' => 'Chinese (Taiwan)',
        );
        asort($config);
        return $config;
    }


    public function _scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            // $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $i++;
        }
        return $dirTree;
    }


    public function _language_list()
    {
        $myDir = APPPATH.'language';
        $file_list = $this->_scanAll($myDir);
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array[] = explode("/",$one_list[$i]);
        }
        foreach ($one_list_array as $value) 
        {           
            $pos=count($value)-2; 
            $lang_folder=$value[$pos];
            $final_list_array[] = $lang_folder;
        }
        $final_array = array_unique($final_list_array);
        $array_keys = array_values($final_array);
        foreach ($final_array as $value) {
            $uc_array_valus[] = ucfirst($value);
        }
        $array_values = array_values($uc_array_valus);
        $final_array_done = array_combine($array_keys, $array_values);
        return $final_array_done;
    }

    public function _theme_list()
    {
        return array();
        $myDir = 'css/skins';
        $file_list = $this->_scanAll($myDir);
        $theme_list=array();
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array = explode("/",$one_list[$i]);
            $theme=array_pop($one_list_array);
            $pos=strpos($theme, '.min.css');
            if($pos!==FALSE) continue; // only loading unminified css
            if($theme=="_all-skins.css") continue;  // skipping large css file that includes all file
            $theme_name=str_replace('.css','', $theme);
            $theme_display=str_replace(array('skin-','.css','-'), array('','',' '), $theme);
            if($theme_display=="black light") $theme_display='light';
            if($theme_display=="black") $theme_display='dark';
            $theme_list[$theme_name]=ucwords($theme_display);
        }
        return $theme_list;
        
    }

    public function _theme_list_front()
    {
        return array
        (
            "white"=>"Light",
            "black"=>"Dark",
            "blue"=>"Blue",
            "green"=>"Green",
            "purple"=>"Purple",
            "red"=>"Red",
            "yellow"=>"Yellow"
        );
    }


    public function language_changer()
    {
        $language=$this->input->post("language");
        $this->session->set_userdata("selected_language",$language);
    }

    protected function time_zone_drop_down($datavalue = '', $primary_key = null,$mandatory=0) // return HTML select
    {
        $all_time_zone = $this->_time_zone_list();

        $str = "<select name='time_zone' id='time_zone' class='form-control'>";
        if($mandatory===1)
        $str.= "<option value=>Time Zone *</option>";
        else $str.= "<option value=>Time Zone</option>";

        foreach ($all_time_zone as $zone_name=>$value) {
            if ($primary_key!= null) {
                if ($zone_name==$datavalue) {
                    $selected=" selected = 'selected' ";
                } else {
                    $selected="";
                }
            } else {
                if ($zone_name==$this->config->item("time_zone")) {
                    $selected=" selected = 'selected' ";
                } else {
                    $selected="";
                }
            }
            $str.= "<option ".$selected." value='$zone_name'>{$zone_name}</option>";
        }
        $str.= "</select>";
        return $str;
    }


    function get_facebook_instagram_dropdown($facebook_rx_fb_user_info_id=0, $dropdown_name = "page_id", $dropdown_id = "page_id", $dropdown_style="", $dropdown_class='select2 form-control',$return_groups = false) 
    {
        
        if ($this->config->item('facebook_poster_group_enable_disable') == '' || $this->config->item('facebook_poster_group_enable_disable')=='0') $has_group_access = false;
        else $has_group_access = true; 

        if(!$has_group_access) $return_groups = false;

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id,'bot_enabled'=>'1')),array('page_name','id','bot_enabled','has_instagram','insta_username'));

        $str ='<select class="'.$dropdown_class.'" id="'.$dropdown_id.'" name="'.$dropdown_name.'" style="'.$dropdown_style.'">';

        if(!addon_exist($module_id=320,$addon_unique_name="instagram_bot") && !$return_groups) 
        $str .='<option value="">'.$this->lang->line("Facebook Page").'</option>';

        if(addon_exist($module_id=320,$addon_unique_name="instagram_bot") || $return_groups)
        $str .='<optgroup label="'.$this->lang->line("Facebook Page").'">';

        foreach ($page_info as $key => $value)
        {
           $selected = ( 
            $this->session->userdata('selected_global_page_table_id')==$value['id'] && 
            ($this->session->userdata('selected_global_media_type')=='fb' || $this->session->userdata('selected_global_media_type')=='')
           ) ? 'selected' : '';
           if($value['bot_enabled']=='1')
           $str.= '<option value="'.$value['id'].'" '.$selected.'>'.$value['page_name'].'</option>';
        }

        if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")  || $return_groups) 
        $str.='</optgroup>';

        if(addon_exist($module_id=320,$addon_unique_name="instagram_bot"))
        {
            $str .='<optgroup label="'.$this->lang->line("Instagram").'">';        
            foreach ($page_info as $key => $value)
            {
                $selected = ($this->session->userdata('selected_global_page_table_id')==$value['id'] && $this->session->userdata('selected_global_media_type')=='ig') ? 'selected' : '';

                if($value['bot_enabled']=='1' && $value['has_instagram']=='1')
                $str .= '<option value="'.$value['id'].'-ig" '.$selected.'>'.$value['insta_username'].'</option>';                
            }
            $str .= '</optgroup>'; 
        }

        if($return_groups)
        {
            
            $group_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),'id,group_name');        

            $str .='<optgroup label="'.$this->lang->line("Facebook Group").'">';        
            foreach ($group_info as $key => $value)
            {
                $str .= '<option value="'.$value['id'].'-gr">'.$value['group_name'].'</option>';                
            }
            $str .= '</optgroup>'; 
        }               
        $str .= '</select>';

        return $str;
    }


    //  used in all types of bulk message campaign
    public function get_broadcast_summary()
    {
        if($this->session->userdata('logged_in') != 1) exit();
        $this->ajax_check();
        $page_id=$this->input->post('page_id');// database id
        $pageid=explode("-",$page_id);
        $page_id = $pageid[0];
        $media_type = "fb";

        if(isset($pageid[1]) && $pageid[1]=="ig") {
            $media_type = "ig";
        }

        $template_types=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");
        $template_types = array_diff($template_types,['list']);
        if($media_type == 'ig') {
            $need_to_remove = ['audio','video','file','text with buttons','media'];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }
        $template_type_str = '';
        foreach ($template_types as $template) {
            $template_type_str .='<option value="'.$template.'">'.$this->lang->line($template).'</option>';
        }

        $user_gender=$this->input->post('user_gender');
        $user_time_zone=$this->input->post('user_time_zone');
        $user_locale=$this->input->post('user_locale');
        $load_label=$this->input->post('load_label');
        $label_ids=$this->input->post('label_ids');
        $excluded_label_ids=$this->input->post('excluded_label_ids');
        $is_bot_subscriber=$this->input->post('is_bot_subscriber');
        $broadcast_type=$this->input->post('broadcast_type');


        if(!isset($label_ids) || !is_array($label_ids)) $label_ids =array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids =array();

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $result = array();
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $dropdown=array();
        $str = $str2 = "";

        if($load_label=='1')
        {
            $str='<script>$("#label_ids").select2();</script> ';
            $str2='<script>$("#excluded_label_ids").select2();</script> ';
            $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]">';
            $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]">';        

            foreach ($info_type as  $value)
            {                
                $str.=  "<option value='".$value['id']."'>".$value['group_name']."</option>";
                $str2.= "<option value='".$value['id']."'>".$value['group_name']."</option>"; 
            }

            $str.= '</select>';
            $str2.='</select>';
        }

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        if(isset($page_info['page_access_token'])) unset($page_info['page_access_token']);

        $subscriber_count = 0;

        if($is_bot_subscriber=='1') $where_simple2 =array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1');
        else $where_simple2 =array("page_table_id"=>$page_id,'client_thread_id !='=> '','user_id'=>$this->user_id,'permission'=>'1');

        if(isset($user_gender) && $user_gender!="")  $where_simple2['messenger_bot_subscriber.gender'] = $user_gender;
        if(isset($user_time_zone) && $user_time_zone!="")  $where_simple2['messenger_bot_subscriber.timezone'] = $user_time_zone;
        if(isset($user_locale) && $user_locale!="")  $where_simple2['messenger_bot_subscriber.locale'] = $user_locale;

        if(isset($broadcast_type) && ($broadcast_type=='24H Promo' || $broadcast_type=='24+1 Promo'))  // bulk bradcast
        {
            if($broadcast_type=='24H Promo') $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time >'] = $previous_time;
            else if($broadcast_type=='24+1 Promo')
            {
                $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time <'] = $previous_time;
                $where_simple2['messenger_bot_subscriber.is_24h_1_sent'] = '0';
            }
        }
        
        $sql_part = "";
        if($load_label=='0')
        {
           if(count($label_ids)>0) $this->db->where_in('messenger_bot_subscribers_label.contact_group_id',$label_ids);
           if(count($excluded_label_ids)>0) $this->db->where_not_in('messenger_bot_subscribers_label.contact_group_id',$excluded_label_ids);
        }

        $where_simple2['messenger_bot_subscriber.social_media'] = $media_type;
        $where_simple2['messenger_bot_subscriber.subscriber_type !='] = 'system';
        $where2 = array('where'=>$where_simple2);
        $join = ['messenger_bot_subscribers_label'=>'messenger_bot_subscribers_label.subscriber_table_id=messenger_bot_subscriber.id,left'];
        $bot_subscriber=$this->basic->get_data("messenger_bot_subscriber",$where2,'count(DISTINCT(messenger_bot_subscriber.id)) as subscriber_count',$join);
        // echo $this->db->last_query();exit();
        $subscriber_count = isset($bot_subscriber[0]['subscriber_count'])? $bot_subscriber[0]['subscriber_count'] : 0;
        $page_info['subscriber_count'] = $subscriber_count;

        $push_postback="";
        $total_subscriber_count = 0;
        if($is_bot_subscriber=='1')
        {
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>'reply_message')),'','','',$start=NULL,$order_by='template_name ASC');
            foreach ($postback_data as $key => $value) 
            {
                $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
            }
            $total_bot_subscriber=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','user_id'=>$this->user_id,'permission'=>'1','subscriber_type !='=>'system')),'count(id) as total_subscriber_count');
            $total_subscriber_count = isset($total_bot_subscriber[0]['total_subscriber_count'])? $total_bot_subscriber[0]['total_subscriber_count'] : 0;
        }
        $page_info['total_subscriber_count'] = $total_subscriber_count;

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info,"push_postback"=>$push_postback,'templates'=>$template_type_str,"media_type"=>$media_type));
    }

    public function get_otn_broadcast_summary()
    {
        if($this->session->userdata('logged_in') != 1) exit();
        $this->ajax_check();
        $page_id=$this->input->post('page_id');// database id
        $user_gender=$this->input->post('user_gender');
        $user_time_zone=$this->input->post('user_time_zone');
        $user_locale=$this->input->post('user_locale');
        $load_label=$this->input->post('load_label');
        $load_otn_postback=$this->input->post('load_otn_postback');
        $otn_postback_ids=$this->input->post('otn_postback_ids');
        $label_ids=$this->input->post('label_ids');
        $excluded_label_ids=$this->input->post('excluded_label_ids');
        $is_bot_subscriber=$this->input->post('is_bot_subscriber');
        $broadcast_type=$this->input->post('broadcast_type');
        $hidden_id=$this->input->post('hidden_id');

        $postback_ids_array = [];
        $label_ids_array = [];
        $exclude_label_array = [];
        if($hidden_id != '0')
        {
            $campaign_data = $this->basic->get_data('messenger_bot_broadcast_serial',['where'=>['id'=>$hidden_id,'user_id'=>$this->user_id]],['otn_postback_id','label_ids','excluded_label_ids']);
            $postback_ids_array = explode(',', $campaign_data[0]['otn_postback_id']);
            $label_ids_array = explode(',', $campaign_data[0]['label_ids']);
            $exclude_label_array = explode(',', $campaign_data[0]['excluded_label_ids']);

        }


        if(!isset($label_ids) || !is_array($label_ids)) $label_ids =array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids =array();
        if(!isset($otn_postback_ids) || !is_array($otn_postback_ids)) $otn_postback_ids =array();

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $result = array();
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $dropdown=array();
        $str = $str2 = "";
        $otn_postback_str = '';
        if($load_otn_postback == '1')
        {
            $otn_postback_str .= '<script>$("#otn_postback_ids").select2();</script> ';
            $otn_postback_str .= '<select multiple="multiple"  class="form-control" id="otn_postback_ids" name="otn_postback_ids[]">';
            $otn_postback_info = $this->basic->get_data('otn_postback',['where'=>['page_id'=>$page_id,'user_id'=>$this->user_id]],['id','template_name']);
            foreach($otn_postback_info as $value)
            {
                $selected = '';
                if(in_array($value['id'], $postback_ids_array)) $selected = 'selected';
                $otn_postback_str.=  "<option value='".$value['id']."' ".$selected.">".$value['template_name']."</option>";
            }
            $otn_postback_str.= '</select>';
        }

        if($load_label=='1')
        {
            $str='<script>$("#label_ids").select2();</script> ';
            $str2='<script>$("#excluded_label_ids").select2();</script> ';
            $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]">';
            $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]">';        

            foreach ($info_type as  $value)
            {    
                $selected = '';
                if(in_array($value['id'], $label_ids_array)) $selected = 'selected';

                $selected2 = '';
                if(in_array($value['id'], $exclude_label_array)) $selected2 = 'selected';

                $str.=  "<option value='".$value['id']."' ".$selected." >".$value['group_name']."</option>";
                $str2.= "<option value='".$value['id']."' ".$selected2." >".$value['group_name']."</option>"; 
            }

            $str.= '</select>';
            $str2.='</select>';
        }

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        if(isset($page_info['page_access_token'])) unset($page_info['page_access_token']);

        $subscriber_count = 0;

        if($is_bot_subscriber=='1') $where_simple2 =array("messenger_bot_subscriber.page_table_id"=>$page_id,'is_bot_subscriber'=> '1','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1');
        else $where_simple2 =array("messenger_bot_subscriber.page_table_id"=>$page_id,'client_thread_id !='=> '','user_id'=>$this->user_id,'permission'=>'1');

        if(isset($user_gender) && $user_gender!="")  $where_simple2['messenger_bot_subscriber.gender'] = $user_gender;
        if(isset($user_time_zone) && $user_time_zone!="")  $where_simple2['messenger_bot_subscriber.timezone'] = $user_time_zone;
        if(isset($user_locale) && $user_locale!="")  $where_simple2['messenger_bot_subscriber.locale'] = $user_locale;

        if(isset($broadcast_type) && ($broadcast_type=='24H Promo' || $broadcast_type=='24+1 Promo'))  // bulk bradcast
        {
            if($broadcast_type=='24H Promo') $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time >'] = $previous_time;
            else if($broadcast_type=='24+1 Promo')
            {
                $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time <'] = $previous_time;
                $where_simple2['messenger_bot_subscriber.is_24h_1_sent'] = '0';
            }
        }
        
        $sql_part = "";
        if($load_label=='0')
        {
           if(count($label_ids)>0) $this->db->where_in('messenger_bot_subscribers_label.contact_group_id',$label_ids);
           if(count($excluded_label_ids)>0) $this->db->where_not_in('messenger_bot_subscribers_label.contact_group_id',$excluded_label_ids);
        }

        if(!empty($otn_postback_ids))
        {
            $this->db->where_in('otn_optin_subscriber.otn_id',$otn_postback_ids);
        }

        $where_simple2['otn_optin_subscriber.is_sent'] = '0';
        $where2 = array('where'=>$where_simple2);
        $join = ['messenger_bot_subscriber'=>'otn_optin_subscriber.subscriber_id=messenger_bot_subscriber.subscribe_id,left','messenger_bot_subscribers_label'=>'messenger_bot_subscribers_label.subscriber_table_id=messenger_bot_subscriber.id,left'];
        $bot_subscriber=$this->basic->get_data("otn_optin_subscriber",$where2,'count(DISTINCT(messenger_bot_subscriber.subscribe_id)) as subscriber_count',$join);
        // echo $this->db->last_query();
        $subscriber_count = isset($bot_subscriber[0]['subscriber_count'])? $bot_subscriber[0]['subscriber_count'] : 0;
        $page_info['subscriber_count'] = $subscriber_count;

        $push_postback="";
        $total_subscriber_count = 0;
        if($is_bot_subscriber=='1')
        {
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>'reply_message')),'','','',$start=NULL,$order_by='template_name ASC');
            foreach ($postback_data as $key => $value) 
            {
                $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
            }
            $total_bot_subscriber=$this->basic->get_data("otn_optin_subscriber",array("where"=>array("messenger_bot_subscriber.page_table_id"=>$page_id,'otn_optin_subscriber.is_sent'=>'0','is_bot_subscriber'=> '1','user_id'=>$this->user_id,'permission'=>'1')),'count(messenger_bot_subscriber.id) as total_subscriber_count',$join);
            $total_subscriber_count = isset($total_bot_subscriber[0]['total_subscriber_count'])? $total_bot_subscriber[0]['total_subscriber_count'] : 0;
        }
        $page_info['total_subscriber_count'] = $total_subscriber_count;

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info,"push_postback"=>$push_postback,'otn_postback_str'=>$otn_postback_str));
    }


    protected function currecny_list_all()
    {
        return $this->get_country_iso_phone_currecncy('currency_name');
    }

    protected function currency_icon()
    {
        return $this->get_country_iso_phone_currecncy('currecny_icon');
    }

    protected function paypal_stripe_currency_list()
    {
        return array('USD','AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','VND');
    }

    //https://gist.github.com/davmixcool/1248ade2fcf43cf86fa294667c86224a
    protected function get_country_iso_phone_currecncy($return='country') // country,currency_name,currecny_icon,phonecode
    {
        $countries = array(
          array('name' => 'Afghanistan','iso_alpha2' => 'AF','iso_alpha3' => 'AFG','iso_numeric' => '4','calling_code' => '93','currency_code' => 'AFN','currency_name' => 'Afghani','currency_symbol' => '؋'),
          array('name' => 'Albania','iso_alpha2' => 'AL','iso_alpha3' => 'ALB','iso_numeric' => '8','calling_code' => '355','currency_code' => 'ALL','currency_name' => 'Lek','currency_symbol' => 'Lek'),
          array('name' => 'Algeria','iso_alpha2' => 'DZ','iso_alpha3' => 'DZA','iso_numeric' => '12','calling_code' => '213','currency_code' => 'DZD','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'American Samoa','iso_alpha2' => 'AS','iso_alpha3' => 'ASM','iso_numeric' => '16','calling_code' => '1684','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Andorra','iso_alpha2' => 'AD','iso_alpha3' => 'AND','iso_numeric' => '20','calling_code' => '376','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Angola','iso_alpha2' => 'AO','iso_alpha3' => 'AGO','iso_numeric' => '24','calling_code' => '244','currency_code' => 'AOA','currency_name' => 'Kwanza','currency_symbol' => 'Kz'),
          array('name' => 'Anguilla','iso_alpha2' => 'AI','iso_alpha3' => 'AIA','iso_numeric' => '660','calling_code' => '1264','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Antarctica','iso_alpha2' => 'AQ','iso_alpha3' => 'ATA','iso_numeric' => '10','calling_code' => '672','currency_code' => '','currency_name' => '','currency_symbol' => ''),
          array('name' => 'Antigua and Barbuda','iso_alpha2' => 'AG','iso_alpha3' => 'ATG','iso_numeric' => '28','calling_code' => '1268','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Argentina','iso_alpha2' => 'AR','iso_alpha3' => 'ARG','iso_numeric' => '32','calling_code' => '54','currency_code' => 'ARS','currency_name' => 'Peso','currency_symbol' => '$'),
          array('name' => 'Armenia','iso_alpha2' => 'AM','iso_alpha3' => 'ARM','iso_numeric' => '51','calling_code' => '374','currency_code' => 'AMD','currency_name' => 'Dram','currency_symbol' => ''),
          array('name' => 'Aruba','iso_alpha2' => 'AW','iso_alpha3' => 'ABW','iso_numeric' => '533','calling_code' => '297','currency_code' => 'AWG','currency_name' => 'Guilder','currency_symbol' => 'ƒ'),
          array('name' => 'Australia','iso_alpha2' => 'AU','iso_alpha3' => 'AUS','iso_numeric' => '36','calling_code' => '61','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Austria','iso_alpha2' => 'AT','iso_alpha3' => 'AUT','iso_numeric' => '40','calling_code' => '43','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Azerbaijan','iso_alpha2' => 'AZ','iso_alpha3' => 'AZE','iso_numeric' => '31','calling_code' => '994','currency_code' => 'AZN','currency_name' => 'Manat','currency_symbol' => 'ман'),
          array('name' => 'Bahamas','iso_alpha2' => 'BS','iso_alpha3' => 'BHS','iso_numeric' => '44','calling_code' => '1242','currency_code' => 'BSD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Bahrain','iso_alpha2' => 'BH','iso_alpha3' => 'BHR','iso_numeric' => '48','calling_code' => '973','currency_code' => 'BHD','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'Bangladesh','iso_alpha2' => 'BD','iso_alpha3' => 'BGD','iso_numeric' => '50','calling_code' => '880','currency_code' => 'BDT','currency_name' => 'Taka','currency_symbol' => ''),
          array('name' => 'Barbados','iso_alpha2' => 'BB','iso_alpha3' => 'BRB','iso_numeric' => '52','calling_code' => '1246','currency_code' => 'BBD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Belarus','iso_alpha2' => 'BY','iso_alpha3' => 'BLR','iso_numeric' => '112','calling_code' => '375','currency_code' => 'BYR','currency_name' => 'Ruble','currency_symbol' => 'p.'),
          array('name' => 'Belgium','iso_alpha2' => 'BE','iso_alpha3' => 'BEL','iso_numeric' => '56','calling_code' => '32','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Belize','iso_alpha2' => 'BZ','iso_alpha3' => 'BLZ','iso_numeric' => '84','calling_code' => '501','currency_code' => 'BZD','currency_name' => 'Dollar','currency_symbol' => 'BZ$'),
          array('name' => 'Benin','iso_alpha2' => 'BJ','iso_alpha3' => 'BEN','iso_numeric' => '204','calling_code' => '229','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Bermuda','iso_alpha2' => 'BM','iso_alpha3' => 'BMU','iso_numeric' => '60','calling_code' => '1441','currency_code' => 'BMD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Bhutan','iso_alpha2' => 'BT','iso_alpha3' => 'BTN','iso_numeric' => '64','calling_code' => '975','currency_code' => 'BTN','currency_name' => 'Ngultrum','currency_symbol' => ''),
          array('name' => 'Bolivia','iso_alpha2' => 'BO','iso_alpha3' => 'BOL','iso_numeric' => '68','calling_code' => '591','currency_code' => 'BOB','currency_name' => 'Boliviano','currency_symbol' => '$b'),
          array('name' => 'Bosnia and Herzegovina','iso_alpha2' => 'BA','iso_alpha3' => 'BIH','iso_numeric' => '70','calling_code' => '387','currency_code' => 'BAM','currency_name' => 'Marka','currency_symbol' => 'KM'),
          array('name' => 'Botswana','iso_alpha2' => 'BW','iso_alpha3' => 'BWA','iso_numeric' => '72','calling_code' => '267','currency_code' => 'BWP','currency_name' => 'Pula','currency_symbol' => 'P'),
          array('name' => 'Bouvet Island','iso_alpha2' => 'BV','iso_alpha3' => 'BVT','iso_numeric' => '74','calling_code' => '','currency_code' => 'NOK','currency_name' => 'Krone','currency_symbol' => 'kr'),
          array('name' => 'Brazil','iso_alpha2' => 'BR','iso_alpha3' => 'BRA','iso_numeric' => '76','calling_code' => '55','currency_code' => 'BRL','currency_name' => 'Real','currency_symbol' => 'R$'),
          array('name' => 'British Indian Ocean Territory','iso_alpha2' => 'IO','iso_alpha3' => 'IOT','iso_numeric' => '86','calling_code' => '','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'British Virgin Islands','iso_alpha2' => 'VG','iso_alpha3' => 'VGB','iso_numeric' => '92','calling_code' => '1284','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Brunei','iso_alpha2' => 'BN','iso_alpha3' => 'BRN','iso_numeric' => '96','calling_code' => '673','currency_code' => 'BND','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Bulgaria','iso_alpha2' => 'BG','iso_alpha3' => 'BGR','iso_numeric' => '100','calling_code' => '359','currency_code' => 'BGN','currency_name' => 'Lev','currency_symbol' => 'лв'),
          array('name' => 'Burkina Faso','iso_alpha2' => 'BF','iso_alpha3' => 'BFA','iso_numeric' => '854','calling_code' => '226','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Burundi','iso_alpha2' => 'BI','iso_alpha3' => 'BDI','iso_numeric' => '108','calling_code' => '257','currency_code' => 'BIF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Cambodia','iso_alpha2' => 'KH','iso_alpha3' => 'KHM','iso_numeric' => '116','calling_code' => '855','currency_code' => 'KHR','currency_name' => 'Riels','currency_symbol' => '៛'),
          array('name' => 'Cameroon','iso_alpha2' => 'CM','iso_alpha3' => 'CMR','iso_numeric' => '120','calling_code' => '237','currency_code' => 'XAF','currency_name' => 'Franc','currency_symbol' => 'FCF'),
          array('name' => 'Canada','iso_alpha2' => 'CA','iso_alpha3' => 'CAN','iso_numeric' => '124','calling_code' => '1','currency_code' => 'CAD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Cape Verde','iso_alpha2' => 'CV','iso_alpha3' => 'CPV','iso_numeric' => '132','calling_code' => '238','currency_code' => 'CVE','currency_name' => 'Escudo','currency_symbol' => ''),
          array('name' => 'Cayman Islands','iso_alpha2' => 'KY','iso_alpha3' => 'CYM','iso_numeric' => '136','calling_code' => '1345','currency_code' => 'KYD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Central African Republic','iso_alpha2' => 'CF','iso_alpha3' => 'CAF','iso_numeric' => '140','calling_code' => '236','currency_code' => 'XAF','currency_name' => 'Franc','currency_symbol' => 'FCF'),
          array('name' => 'Chad','iso_alpha2' => 'TD','iso_alpha3' => 'TCD','iso_numeric' => '148','calling_code' => '235','currency_code' => 'XAF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Chile','iso_alpha2' => 'CL','iso_alpha3' => 'CHL','iso_numeric' => '152','calling_code' => '56','currency_code' => 'CLP','currency_name' => 'Peso','currency_symbol' => ''),
          array('name' => 'China','iso_alpha2' => 'CN','iso_alpha3' => 'CHN','iso_numeric' => '156','calling_code' => '86','currency_code' => 'CNY','currency_name' => 'YuanRenminbi','currency_symbol' => '¥'),
          array('name' => 'Christmas Island','iso_alpha2' => 'CX','iso_alpha3' => 'CXR','iso_numeric' => '162','calling_code' => '61','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Cocos Islands','iso_alpha2' => 'CC','iso_alpha3' => 'CCK','iso_numeric' => '166','calling_code' => '61','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Colombia','iso_alpha2' => 'CO','iso_alpha3' => 'COL','iso_numeric' => '170','calling_code' => '57','currency_code' => 'COP','currency_name' => 'Peso','currency_symbol' => '$'),
          array('name' => 'Comoros','iso_alpha2' => 'KM','iso_alpha3' => 'COM','iso_numeric' => '174','calling_code' => '269','currency_code' => 'KMF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Cook Islands','iso_alpha2' => 'CK','iso_alpha3' => 'COK','iso_numeric' => '184','calling_code' => '682','currency_code' => 'NZD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Costa Rica','iso_alpha2' => 'CR','iso_alpha3' => 'CRI','iso_numeric' => '188','calling_code' => '506','currency_code' => 'CRC','currency_name' => 'Colon','currency_symbol' => '₡'),
          array('name' => 'Croatia','iso_alpha2' => 'HR','iso_alpha3' => 'HRV','iso_numeric' => '191','calling_code' => '385','currency_code' => 'HRK','currency_name' => 'Kuna','currency_symbol' => 'kn'),
          array('name' => 'Cuba','iso_alpha2' => 'CU','iso_alpha3' => 'CUB','iso_numeric' => '192','calling_code' => '53','currency_code' => 'CUP','currency_name' => 'Peso','currency_symbol' => '₱'),
          array('name' => 'Cyprus','iso_alpha2' => 'CY','iso_alpha3' => 'CYP','iso_numeric' => '196','calling_code' => '357','currency_code' => 'CYP','currency_name' => 'Pound','currency_symbol' => ''),
          array('name' => 'Czech Republic','iso_alpha2' => 'CZ','iso_alpha3' => 'CZE','iso_numeric' => '203','calling_code' => '420','currency_code' => 'CZK','currency_name' => 'Koruna','currency_symbol' => 'Kč'),
          array('name' => 'Democratic Republic of the Congo','iso_alpha2' => 'CD','iso_alpha3' => 'COD','iso_numeric' => '180','calling_code' => '243','currency_code' => 'CDF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Denmark','iso_alpha2' => 'DK','iso_alpha3' => 'DNK','iso_numeric' => '208','calling_code' => '45','currency_code' => 'DKK','currency_name' => 'Krone','currency_symbol' => 'kr'),
          array('name' => 'Djibouti','iso_alpha2' => 'DJ','iso_alpha3' => 'DJI','iso_numeric' => '262','calling_code' => '253','currency_code' => 'DJF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Dominica','iso_alpha2' => 'DM','iso_alpha3' => 'DMA','iso_numeric' => '212','calling_code' => '1767','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Dominican Republic','iso_alpha2' => 'DO','iso_alpha3' => 'DOM','iso_numeric' => '214','calling_code' => '1809','currency_code' => 'DOP','currency_name' => 'Peso','currency_symbol' => 'RD$'),
          array('name' => 'East Timor','iso_alpha2' => 'TL','iso_alpha3' => 'TLS','iso_numeric' => '626','calling_code' => '670','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Ecuador','iso_alpha2' => 'EC','iso_alpha3' => 'ECU','iso_numeric' => '218','calling_code' => '593','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Egypt','iso_alpha2' => 'EG','iso_alpha3' => 'EGY','iso_numeric' => '818','calling_code' => '20','currency_code' => 'EGP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'El Salvador','iso_alpha2' => 'SV','iso_alpha3' => 'SLV','iso_numeric' => '222','calling_code' => '503','currency_code' => 'SVC','currency_name' => 'Colone','currency_symbol' => '$'),
          array('name' => 'Equatorial Guinea','iso_alpha2' => 'GQ','iso_alpha3' => 'GNQ','iso_numeric' => '226','calling_code' => '240','currency_code' => 'XAF','currency_name' => 'Franc','currency_symbol' => 'FCF'),
          array('name' => 'Eritrea','iso_alpha2' => 'ER','iso_alpha3' => 'ERI','iso_numeric' => '232','calling_code' => '291','currency_code' => 'ERN','currency_name' => 'Nakfa','currency_symbol' => 'Nfk'),
          array('name' => 'Estonia','iso_alpha2' => 'EE','iso_alpha3' => 'EST','iso_numeric' => '233','calling_code' => '372','currency_code' => 'EEK','currency_name' => 'Kroon','currency_symbol' => 'kr'),
          array('name' => 'Ethiopia','iso_alpha2' => 'ET','iso_alpha3' => 'ETH','iso_numeric' => '231','calling_code' => '251','currency_code' => 'ETB','currency_name' => 'Birr','currency_symbol' => ''),
          array('name' => 'Falkland Islands','iso_alpha2' => 'FK','iso_alpha3' => 'FLK','iso_numeric' => '238','calling_code' => '500','currency_code' => 'FKP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'Faroe Islands','iso_alpha2' => 'FO','iso_alpha3' => 'FRO','iso_numeric' => '234','calling_code' => '298','currency_code' => 'DKK','currency_name' => 'Krone','currency_symbol' => 'kr'),
          array('name' => 'Fiji','iso_alpha2' => 'FJ','iso_alpha3' => 'FJI','iso_numeric' => '242','calling_code' => '679','currency_code' => 'FJD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Finland','iso_alpha2' => 'FI','iso_alpha3' => 'FIN','iso_numeric' => '246','calling_code' => '358','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'France','iso_alpha2' => 'FR','iso_alpha3' => 'FRA','iso_numeric' => '250','calling_code' => '33','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'French Guiana','iso_alpha2' => 'GF','iso_alpha3' => 'GUF','iso_numeric' => '254','calling_code' => '','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'French Polynesia','iso_alpha2' => 'PF','iso_alpha3' => 'PYF','iso_numeric' => '258','calling_code' => '689','currency_code' => 'XPF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'French Southern Territories','iso_alpha2' => 'TF','iso_alpha3' => 'ATF','iso_numeric' => '260','calling_code' => '','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Gabon','iso_alpha2' => 'GA','iso_alpha3' => 'GAB','iso_numeric' => '266','calling_code' => '241','currency_code' => 'XAF','currency_name' => 'Franc','currency_symbol' => 'FCF'),
          array('name' => 'Gambia','iso_alpha2' => 'GM','iso_alpha3' => 'GMB','iso_numeric' => '270','calling_code' => '220','currency_code' => 'GMD','currency_name' => 'Dalasi','currency_symbol' => 'D'),
          array('name' => 'Georgia','iso_alpha2' => 'GE','iso_alpha3' => 'GEO','iso_numeric' => '268','calling_code' => '995','currency_code' => 'GEL','currency_name' => 'Lari','currency_symbol' => ''),
          array('name' => 'Germany','iso_alpha2' => 'DE','iso_alpha3' => 'DEU','iso_numeric' => '276','calling_code' => '49','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Ghana','iso_alpha2' => 'GH','iso_alpha3' => 'GHA','iso_numeric' => '288','calling_code' => '233','currency_code' => 'GHC','currency_name' => 'Cedi','currency_symbol' => '¢'),
          array('name' => 'Gibraltar','iso_alpha2' => 'GI','iso_alpha3' => 'GIB','iso_numeric' => '292','calling_code' => '350','currency_code' => 'GIP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'Greece','iso_alpha2' => 'GR','iso_alpha3' => 'GRC','iso_numeric' => '300','calling_code' => '30','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Greenland','iso_alpha2' => 'GL','iso_alpha3' => 'GRL','iso_numeric' => '304','calling_code' => '299','currency_code' => 'DKK','currency_name' => 'Krone','currency_symbol' => 'kr'),
          array('name' => 'Grenada','iso_alpha2' => 'GD','iso_alpha3' => 'GRD','iso_numeric' => '308','calling_code' => '1473','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Guadeloupe','iso_alpha2' => 'GP','iso_alpha3' => 'GLP','iso_numeric' => '312','calling_code' => '','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Guam','iso_alpha2' => 'GU','iso_alpha3' => 'GUM','iso_numeric' => '316','calling_code' => '1671','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Guatemala','iso_alpha2' => 'GT','iso_alpha3' => 'GTM','iso_numeric' => '320','calling_code' => '502','currency_code' => 'GTQ','currency_name' => 'Quetzal','currency_symbol' => 'Q'),
          array('name' => 'Guinea','iso_alpha2' => 'GN','iso_alpha3' => 'GIN','iso_numeric' => '324','calling_code' => '224','currency_code' => 'GNF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Guinea-Bissau','iso_alpha2' => 'GW','iso_alpha3' => 'GNB','iso_numeric' => '624','calling_code' => '245','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Guyana','iso_alpha2' => 'GY','iso_alpha3' => 'GUY','iso_numeric' => '328','calling_code' => '592','currency_code' => 'GYD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Haiti','iso_alpha2' => 'HT','iso_alpha3' => 'HTI','iso_numeric' => '332','calling_code' => '509','currency_code' => 'HTG','currency_name' => 'Gourde','currency_symbol' => 'G'),
          array('name' => 'Heard Island and McDonald Islands','iso_alpha2' => 'HM','iso_alpha3' => 'HMD','iso_numeric' => '334','calling_code' => '','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Honduras','iso_alpha2' => 'HN','iso_alpha3' => 'HND','iso_numeric' => '340','calling_code' => '504','currency_code' => 'HNL','currency_name' => 'Lempira','currency_symbol' => 'L'),
          array('name' => 'Hong Kong','iso_alpha2' => 'HK','iso_alpha3' => 'HKG','iso_numeric' => '344','calling_code' => '852','currency_code' => 'HKD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Hungary','iso_alpha2' => 'HU','iso_alpha3' => 'HUN','iso_numeric' => '348','calling_code' => '36','currency_code' => 'HUF','currency_name' => 'Forint','currency_symbol' => 'Ft'),
          array('name' => 'Iceland','iso_alpha2' => 'IS','iso_alpha3' => 'ISL','iso_numeric' => '352','calling_code' => '354','currency_code' => 'ISK','currency_name' => 'Krona','currency_symbol' => 'kr'),
          array('name' => 'India','iso_alpha2' => 'IN','iso_alpha3' => 'IND','iso_numeric' => '356','calling_code' => '91','currency_code' => 'INR','currency_name' => 'Rupee','currency_symbol' => '₹'),
          array('name' => 'Indonesia','iso_alpha2' => 'ID','iso_alpha3' => 'IDN','iso_numeric' => '360','calling_code' => '62','currency_code' => 'IDR','currency_name' => 'Rupiah','currency_symbol' => 'Rp'),
          array('name' => 'Iran','iso_alpha2' => 'IR','iso_alpha3' => 'IRN','iso_numeric' => '364','calling_code' => '98','currency_code' => 'IRR','currency_name' => 'Rial','currency_symbol' => '﷼'),
          array('name' => 'Iraq','iso_alpha2' => 'IQ','iso_alpha3' => 'IRQ','iso_numeric' => '368','calling_code' => '964','currency_code' => 'IQD','currency_name' => 'Dinar','currency_symbol' => 'د.ع'),
          array('name' => 'Ireland','iso_alpha2' => 'IE','iso_alpha3' => 'IRL','iso_numeric' => '372','calling_code' => '353','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Israel','iso_alpha2' => 'IL','iso_alpha3' => 'ISR','iso_numeric' => '376','calling_code' => '972','currency_code' => 'ILS','currency_name' => 'Shekel','currency_symbol' => '₪'),
          array('name' => 'Italy','iso_alpha2' => 'IT','iso_alpha3' => 'ITA','iso_numeric' => '380','calling_code' => '39','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Ivory Coast','iso_alpha2' => 'CI','iso_alpha3' => 'CIV','iso_numeric' => '384','calling_code' => '225','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Jamaica','iso_alpha2' => 'JM','iso_alpha3' => 'JAM','iso_numeric' => '388','calling_code' => '1876','currency_code' => 'JMD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Japan','iso_alpha2' => 'JP','iso_alpha3' => 'JPN','iso_numeric' => '392','calling_code' => '81','currency_code' => 'JPY','currency_name' => 'Yen','currency_symbol' => '¥'),
          array('name' => 'Jordan','iso_alpha2' => 'JO','iso_alpha3' => 'JOR','iso_numeric' => '400','calling_code' => '962','currency_code' => 'JOD','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'Kazakhstan','iso_alpha2' => 'KZ','iso_alpha3' => 'KAZ','iso_numeric' => '398','calling_code' => '7','currency_code' => 'KZT','currency_name' => 'Tenge','currency_symbol' => 'лв'),
          array('name' => 'Kenya','iso_alpha2' => 'KE','iso_alpha3' => 'KEN','iso_numeric' => '404','calling_code' => '254','currency_code' => 'KES','currency_name' => 'Shilling','currency_symbol' => ''),
          array('name' => 'Kiribati','iso_alpha2' => 'KI','iso_alpha3' => 'KIR','iso_numeric' => '296','calling_code' => '686','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Kuwait','iso_alpha2' => 'KW','iso_alpha3' => 'KWT','iso_numeric' => '414','calling_code' => '965','currency_code' => 'KWD','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'Kyrgyzstan','iso_alpha2' => 'KG','iso_alpha3' => 'KGZ','iso_numeric' => '417','calling_code' => '996','currency_code' => 'KGS','currency_name' => 'Som','currency_symbol' => 'лв'),
          array('name' => 'Laos','iso_alpha2' => 'LA','iso_alpha3' => 'LAO','iso_numeric' => '418','calling_code' => '856','currency_code' => 'LAK','currency_name' => 'Kip','currency_symbol' => '₭'),
          array('name' => 'Latvia','iso_alpha2' => 'LV','iso_alpha3' => 'LVA','iso_numeric' => '428','calling_code' => '371','currency_code' => 'LVL','currency_name' => 'Lat','currency_symbol' => 'Ls'),
          array('name' => 'Lebanon','iso_alpha2' => 'LB','iso_alpha3' => 'LBN','iso_numeric' => '422','calling_code' => '961','currency_code' => 'LBP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'Lesotho','iso_alpha2' => 'LS','iso_alpha3' => 'LSO','iso_numeric' => '426','calling_code' => '266','currency_code' => 'LSL','currency_name' => 'Loti','currency_symbol' => 'L'),
          array('name' => 'Liberia','iso_alpha2' => 'LR','iso_alpha3' => 'LBR','iso_numeric' => '430','calling_code' => '231','currency_code' => 'LRD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Libya','iso_alpha2' => 'LY','iso_alpha3' => 'LBY','iso_numeric' => '434','calling_code' => '218','currency_code' => 'LYD','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'Liechtenstein','iso_alpha2' => 'LI','iso_alpha3' => 'LIE','iso_numeric' => '438','calling_code' => '423','currency_code' => 'CHF','currency_name' => 'Franc','currency_symbol' => 'CHF'),
          array('name' => 'Lithuania','iso_alpha2' => 'LT','iso_alpha3' => 'LTU','iso_numeric' => '440','calling_code' => '370','currency_code' => 'LTL','currency_name' => 'Litas','currency_symbol' => 'Lt'),
          array('name' => 'Luxembourg','iso_alpha2' => 'LU','iso_alpha3' => 'LUX','iso_numeric' => '442','calling_code' => '352','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Macao','iso_alpha2' => 'MO','iso_alpha3' => 'MAC','iso_numeric' => '446','calling_code' => '853','currency_code' => 'MOP','currency_name' => 'Pataca','currency_symbol' => 'MOP'),
          array('name' => 'Macedonia','iso_alpha2' => 'MK','iso_alpha3' => 'MKD','iso_numeric' => '807','calling_code' => '389','currency_code' => 'MKD','currency_name' => 'Denar','currency_symbol' => 'ден'),
          array('name' => 'Madagascar','iso_alpha2' => 'MG','iso_alpha3' => 'MDG','iso_numeric' => '450','calling_code' => '261','currency_code' => 'MGA','currency_name' => 'Ariary','currency_symbol' => ''),
          array('name' => 'Malawi','iso_alpha2' => 'MW','iso_alpha3' => 'MWI','iso_numeric' => '454','calling_code' => '265','currency_code' => 'MWK','currency_name' => 'Kwacha','currency_symbol' => 'MK'),
          array('name' => 'Malaysia','iso_alpha2' => 'MY','iso_alpha3' => 'MYS','iso_numeric' => '458','calling_code' => '60','currency_code' => 'MYR','currency_name' => 'Ringgit','currency_symbol' => 'RM'),
          array('name' => 'Maldives','iso_alpha2' => 'MV','iso_alpha3' => 'MDV','iso_numeric' => '462','calling_code' => '960','currency_code' => 'MVR','currency_name' => 'Rufiyaa','currency_symbol' => 'Rf'),
          array('name' => 'Mali','iso_alpha2' => 'ML','iso_alpha3' => 'MLI','iso_numeric' => '466','calling_code' => '223','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Malta','iso_alpha2' => 'MT','iso_alpha3' => 'MLT','iso_numeric' => '470','calling_code' => '356','currency_code' => 'MTL','currency_name' => 'Lira','currency_symbol' => ''),
          array('name' => 'Marshall Islands','iso_alpha2' => 'MH','iso_alpha3' => 'MHL','iso_numeric' => '584','calling_code' => '692','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Martinique','iso_alpha2' => 'MQ','iso_alpha3' => 'MTQ','iso_numeric' => '474','calling_code' => '','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Mauritania','iso_alpha2' => 'MR','iso_alpha3' => 'MRT','iso_numeric' => '478','calling_code' => '222','currency_code' => 'MRO','currency_name' => 'Ouguiya','currency_symbol' => 'UM'),
          array('name' => 'Mauritius','iso_alpha2' => 'MU','iso_alpha3' => 'MUS','iso_numeric' => '480','calling_code' => '230','currency_code' => 'MUR','currency_name' => 'Rupee','currency_symbol' => '₨'),
          array('name' => 'Mayotte','iso_alpha2' => 'YT','iso_alpha3' => 'MYT','iso_numeric' => '175','calling_code' => '262','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Mexico','iso_alpha2' => 'MX','iso_alpha3' => 'MEX','iso_numeric' => '484','calling_code' => '52','currency_code' => 'MXN','currency_name' => 'Peso','currency_symbol' => '$'),
          array('name' => 'Micronesia','iso_alpha2' => 'FM','iso_alpha3' => 'FSM','iso_numeric' => '583','calling_code' => '691','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Moldova','iso_alpha2' => 'MD','iso_alpha3' => 'MDA','iso_numeric' => '498','calling_code' => '373','currency_code' => 'MDL','currency_name' => 'Leu','currency_symbol' => ''),
          array('name' => 'Monaco','iso_alpha2' => 'MC','iso_alpha3' => 'MCO','iso_numeric' => '492','calling_code' => '377','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Mongolia','iso_alpha2' => 'MN','iso_alpha3' => 'MNG','iso_numeric' => '496','calling_code' => '976','currency_code' => 'MNT','currency_name' => 'Tugrik','currency_symbol' => '₮'),
          array('name' => 'Montserrat','iso_alpha2' => 'MS','iso_alpha3' => 'MSR','iso_numeric' => '500','calling_code' => '1664','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Morocco','iso_alpha2' => 'MA','iso_alpha3' => 'MAR','iso_numeric' => '504','calling_code' => '212','currency_code' => 'MAD','currency_name' => 'Dirham','currency_symbol' => ''),
          array('name' => 'Mozambique','iso_alpha2' => 'MZ','iso_alpha3' => 'MOZ','iso_numeric' => '508','calling_code' => '258','currency_code' => 'MZN','currency_name' => 'Meticail','currency_symbol' => 'MT'),
          array('name' => 'Myanmar','iso_alpha2' => 'MM','iso_alpha3' => 'MMR','iso_numeric' => '104','calling_code' => '95','currency_code' => 'MMK','currency_name' => 'Kyat','currency_symbol' => 'K'),
          array('name' => 'Namibia','iso_alpha2' => 'NA','iso_alpha3' => 'NAM','iso_numeric' => '516','calling_code' => '264','currency_code' => 'NAD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Nauru','iso_alpha2' => 'NR','iso_alpha3' => 'NRU','iso_numeric' => '520','calling_code' => '674','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Nepal','iso_alpha2' => 'NP','iso_alpha3' => 'NPL','iso_numeric' => '524','calling_code' => '977','currency_code' => 'NPR','currency_name' => 'Rupee','currency_symbol' => '₨'),
          array('name' => 'Netherlands','iso_alpha2' => 'NL','iso_alpha3' => 'NLD','iso_numeric' => '528','calling_code' => '31','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Netherlands Antilles','iso_alpha2' => 'AN','iso_alpha3' => 'ANT','iso_numeric' => '530','calling_code' => '599','currency_code' => 'ANG','currency_name' => 'Guilder','currency_symbol' => 'ƒ'),
          array('name' => 'New Caledonia','iso_alpha2' => 'NC','iso_alpha3' => 'NCL','iso_numeric' => '540','calling_code' => '687','currency_code' => 'XPF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'New Zealand','iso_alpha2' => 'NZ','iso_alpha3' => 'NZL','iso_numeric' => '554','calling_code' => '64','currency_code' => 'NZD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Nicaragua','iso_alpha2' => 'NI','iso_alpha3' => 'NIC','iso_numeric' => '558','calling_code' => '505','currency_code' => 'NIO','currency_name' => 'Cordoba','currency_symbol' => 'C$'),
          array('name' => 'Niger','iso_alpha2' => 'NE','iso_alpha3' => 'NER','iso_numeric' => '562','calling_code' => '227','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Nigeria','iso_alpha2' => 'NG','iso_alpha3' => 'NGA','iso_numeric' => '566','calling_code' => '234','currency_code' => 'NGN','currency_name' => 'Naira','currency_symbol' => '₦'),
          array('name' => 'Niue','iso_alpha2' => 'NU','iso_alpha3' => 'NIU','iso_numeric' => '570','calling_code' => '683','currency_code' => 'NZD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Norfolk Island','iso_alpha2' => 'NF','iso_alpha3' => 'NFK','iso_numeric' => '574','calling_code' => '','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'North Korea','iso_alpha2' => 'KP','iso_alpha3' => 'PRK','iso_numeric' => '408','calling_code' => '850','currency_code' => 'KPW','currency_name' => 'Won','currency_symbol' => '₩'),
          array('name' => 'Northern Mariana Islands','iso_alpha2' => 'MP','iso_alpha3' => 'MNP','iso_numeric' => '580','calling_code' => '1670','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Norway','iso_alpha2' => 'NO','iso_alpha3' => 'NOR','iso_numeric' => '578','calling_code' => '47','currency_code' => 'NOK','currency_name' => 'Krone','currency_symbol' => 'kr'),
          array('name' => 'Oman','iso_alpha2' => 'OM','iso_alpha3' => 'OMN','iso_numeric' => '512','calling_code' => '968','currency_code' => 'OMR','currency_name' => 'Rial','currency_symbol' => '﷼'),
          array('name' => 'Pakistan','iso_alpha2' => 'PK','iso_alpha3' => 'PAK','iso_numeric' => '586','calling_code' => '92','currency_code' => 'PKR','currency_name' => 'Rupee','currency_symbol' => '₨'),
          array('name' => 'Palau','iso_alpha2' => 'PW','iso_alpha3' => 'PLW','iso_numeric' => '585','calling_code' => '680','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Palestinian Territory','iso_alpha2' => 'PS','iso_alpha3' => 'PSE','iso_numeric' => '275','calling_code' => '','currency_code' => 'ILS','currency_name' => 'Shekel','currency_symbol' => '₪'),
          array('name' => 'Panama','iso_alpha2' => 'PA','iso_alpha3' => 'PAN','iso_numeric' => '591','calling_code' => '507','currency_code' => 'PAB','currency_name' => 'Balboa','currency_symbol' => 'B/.'),
          array('name' => 'Papua New Guinea','iso_alpha2' => 'PG','iso_alpha3' => 'PNG','iso_numeric' => '598','calling_code' => '675','currency_code' => 'PGK','currency_name' => 'Kina','currency_symbol' => ''),
          array('name' => 'Paraguay','iso_alpha2' => 'PY','iso_alpha3' => 'PRY','iso_numeric' => '600','calling_code' => '595','currency_code' => 'PYG','currency_name' => 'Guarani','currency_symbol' => 'Gs'),
          array('name' => 'Peru','iso_alpha2' => 'PE','iso_alpha3' => 'PER','iso_numeric' => '604','calling_code' => '51','currency_code' => 'PEN','currency_name' => 'Sol','currency_symbol' => 'S/.'),
          array('name' => 'Philippines','iso_alpha2' => 'PH','iso_alpha3' => 'PHL','iso_numeric' => '608','calling_code' => '63','currency_code' => 'PHP','currency_name' => 'Peso','currency_symbol' => 'Php'),
          array('name' => 'Pitcairn','iso_alpha2' => 'PN','iso_alpha3' => 'PCN','iso_numeric' => '612','calling_code' => '870','currency_code' => 'NZD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Poland','iso_alpha2' => 'PL','iso_alpha3' => 'POL','iso_numeric' => '616','calling_code' => '48','currency_code' => 'PLN','currency_name' => 'Zloty','currency_symbol' => 'zł'),
          array('name' => 'Portugal','iso_alpha2' => 'PT','iso_alpha3' => 'PRT','iso_numeric' => '620','calling_code' => '351','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Puerto Rico','iso_alpha2' => 'PR','iso_alpha3' => 'PRI','iso_numeric' => '630','calling_code' => '1','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Qatar','iso_alpha2' => 'QA','iso_alpha3' => 'QAT','iso_numeric' => '634','calling_code' => '974','currency_code' => 'QAR','currency_name' => 'Rial','currency_symbol' => '﷼'),
          array('name' => 'Republic of the Congo','iso_alpha2' => 'CG','iso_alpha3' => 'COG','iso_numeric' => '178','calling_code' => '242','currency_code' => 'XAF','currency_name' => 'Franc','currency_symbol' => 'FCF'),
          array('name' => 'Reunion','iso_alpha2' => 'RE','iso_alpha3' => 'REU','iso_numeric' => '638','calling_code' => '','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Romania','iso_alpha2' => 'RO','iso_alpha3' => 'ROU','iso_numeric' => '642','calling_code' => '40','currency_code' => 'RON','currency_name' => 'Leu','currency_symbol' => 'lei'),
          array('name' => 'Russia','iso_alpha2' => 'RU','iso_alpha3' => 'RUS','iso_numeric' => '643','calling_code' => '7','currency_code' => 'RUB','currency_name' => 'Ruble','currency_symbol' => 'руб'),
          array('name' => 'Rwanda','iso_alpha2' => 'RW','iso_alpha3' => 'RWA','iso_numeric' => '646','calling_code' => '250','currency_code' => 'RWF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Saint Helena','iso_alpha2' => 'SH','iso_alpha3' => 'SHN','iso_numeric' => '654','calling_code' => '290','currency_code' => 'SHP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'Saint Kitts and Nevis','iso_alpha2' => 'KN','iso_alpha3' => 'KNA','iso_numeric' => '659','calling_code' => '1869','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Saint Lucia','iso_alpha2' => 'LC','iso_alpha3' => 'LCA','iso_numeric' => '662','calling_code' => '1758','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Saint Pierre and Miquelon','iso_alpha2' => 'PM','iso_alpha3' => 'SPM','iso_numeric' => '666','calling_code' => '508','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Saint Vincent and the Grenadines','iso_alpha2' => 'VC','iso_alpha3' => 'VCT','iso_numeric' => '670','calling_code' => '1784','currency_code' => 'XCD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Samoa','iso_alpha2' => 'WS','iso_alpha3' => 'WSM','iso_numeric' => '882','calling_code' => '685','currency_code' => 'WST','currency_name' => 'Tala','currency_symbol' => 'WS$'),
          array('name' => 'San Marino','iso_alpha2' => 'SM','iso_alpha3' => 'SMR','iso_numeric' => '674','calling_code' => '378','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Sao Tome and Principe','iso_alpha2' => 'ST','iso_alpha3' => 'STP','iso_numeric' => '678','calling_code' => '239','currency_code' => 'STD','currency_name' => 'Dobra','currency_symbol' => 'Db'),
          array('name' => 'Saudi Arabia','iso_alpha2' => 'SA','iso_alpha3' => 'SAU','iso_numeric' => '682','calling_code' => '966','currency_code' => 'SAR','currency_name' => 'Rial','currency_symbol' => '﷼'),
          array('name' => 'Senegal','iso_alpha2' => 'SN','iso_alpha3' => 'SEN','iso_numeric' => '686','calling_code' => '221','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Serbia and Montenegro','iso_alpha2' => 'CS','iso_alpha3' => 'SCG','iso_numeric' => '891','calling_code' => '','currency_code' => 'RSD','currency_name' => 'Dinar','currency_symbol' => 'Дин'),
          array('name' => 'Seychelles','iso_alpha2' => 'SC','iso_alpha3' => 'SYC','iso_numeric' => '690','calling_code' => '248','currency_code' => 'SCR','currency_name' => 'Rupee','currency_symbol' => '₨'),
          array('name' => 'Sierra Leone','iso_alpha2' => 'SL','iso_alpha3' => 'SLE','iso_numeric' => '694','calling_code' => '232','currency_code' => 'SLL','currency_name' => 'Leone','currency_symbol' => 'Le'),
          array('name' => 'Singapore','iso_alpha2' => 'SG','iso_alpha3' => 'SGP','iso_numeric' => '702','calling_code' => '65','currency_code' => 'SGD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Slovakia','iso_alpha2' => 'SK','iso_alpha3' => 'SVK','iso_numeric' => '703','calling_code' => '421','currency_code' => 'SKK','currency_name' => 'Koruna','currency_symbol' => 'Sk'),
          array('name' => 'Slovenia','iso_alpha2' => 'SI','iso_alpha3' => 'SVN','iso_numeric' => '705','calling_code' => '386','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Solomon Islands','iso_alpha2' => 'SB','iso_alpha3' => 'SLB','iso_numeric' => '90','calling_code' => '677','currency_code' => 'SBD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Somalia','iso_alpha2' => 'SO','iso_alpha3' => 'SOM','iso_numeric' => '706','calling_code' => '252','currency_code' => 'SOS','currency_name' => 'Shilling','currency_symbol' => 'S'),
          array('name' => 'South Africa','iso_alpha2' => 'ZA','iso_alpha3' => 'ZAF','iso_numeric' => '710','calling_code' => '27','currency_code' => 'ZAR','currency_name' => 'Rand','currency_symbol' => 'R'),
          array('name' => 'South Georgia and the South Sandwich Islands','iso_alpha2' => 'GS','iso_alpha3' => 'SGS','iso_numeric' => '239','calling_code' => '','currency_code' => 'GBP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'South Korea','iso_alpha2' => 'KR','iso_alpha3' => 'KOR','iso_numeric' => '410','calling_code' => '82','currency_code' => 'KRW','currency_name' => 'Won','currency_symbol' => '₩'),
          array('name' => 'Spain','iso_alpha2' => 'ES','iso_alpha3' => 'ESP','iso_numeric' => '724','calling_code' => '34','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Sri Lanka','iso_alpha2' => 'LK','iso_alpha3' => 'LKA','iso_numeric' => '144','calling_code' => '94','currency_code' => 'LKR','currency_name' => 'Rupee','currency_symbol' => '₨'),
          array('name' => 'Sudan','iso_alpha2' => 'SD','iso_alpha3' => 'SDN','iso_numeric' => '736','calling_code' => '249','currency_code' => 'SDD','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'Suriname','iso_alpha2' => 'SR','iso_alpha3' => 'SUR','iso_numeric' => '740','calling_code' => '597','currency_code' => 'SRD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Svalbard and Jan Mayen','iso_alpha2' => 'SJ','iso_alpha3' => 'SJM','iso_numeric' => '744','calling_code' => '','currency_code' => 'NOK','currency_name' => 'Krone','currency_symbol' => 'kr'),
          array('name' => 'Swaziland','iso_alpha2' => 'SZ','iso_alpha3' => 'SWZ','iso_numeric' => '748','calling_code' => '268','currency_code' => 'SZL','currency_name' => 'Lilangeni','currency_symbol' => ''),
          array('name' => 'Sweden','iso_alpha2' => 'SE','iso_alpha3' => 'SWE','iso_numeric' => '752','calling_code' => '46','currency_code' => 'SEK','currency_name' => 'Krona','currency_symbol' => 'kr'),
          array('name' => 'Switzerland','iso_alpha2' => 'CH','iso_alpha3' => 'CHE','iso_numeric' => '756','calling_code' => '41','currency_code' => 'CHF','currency_name' => 'Franc','currency_symbol' => 'CHF'),
          array('name' => 'Syria','iso_alpha2' => 'SY','iso_alpha3' => 'SYR','iso_numeric' => '760','calling_code' => '963','currency_code' => 'SYP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'Taiwan','iso_alpha2' => 'TW','iso_alpha3' => 'TWN','iso_numeric' => '158','calling_code' => '886','currency_code' => 'TWD','currency_name' => 'Dollar','currency_symbol' => 'NT$'),
          array('name' => 'Tajikistan','iso_alpha2' => 'TJ','iso_alpha3' => 'TJK','iso_numeric' => '762','calling_code' => '992','currency_code' => 'TJS','currency_name' => 'Somoni','currency_symbol' => ''),
          array('name' => 'Tanzania','iso_alpha2' => 'TZ','iso_alpha3' => 'TZA','iso_numeric' => '834','calling_code' => '255','currency_code' => 'TZS','currency_name' => 'Shilling','currency_symbol' => ''),
          array('name' => 'Thailand','iso_alpha2' => 'TH','iso_alpha3' => 'THA','iso_numeric' => '764','calling_code' => '66','currency_code' => 'THB','currency_name' => 'Baht','currency_symbol' => '฿'),
          array('name' => 'Togo','iso_alpha2' => 'TG','iso_alpha3' => 'TGO','iso_numeric' => '768','calling_code' => '228','currency_code' => 'XOF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Tokelau','iso_alpha2' => 'TK','iso_alpha3' => 'TKL','iso_numeric' => '772','calling_code' => '690','currency_code' => 'NZD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Tonga','iso_alpha2' => 'TO','iso_alpha3' => 'TON','iso_numeric' => '776','calling_code' => '676','currency_code' => 'TOP','currency_name' => 'Paanga','currency_symbol' => 'T$'),
          array('name' => 'Trinidad and Tobago','iso_alpha2' => 'TT','iso_alpha3' => 'TTO','iso_numeric' => '780','calling_code' => '1868','currency_code' => 'TTD','currency_name' => 'Dollar','currency_symbol' => 'TT$'),
          array('name' => 'Tunisia','iso_alpha2' => 'TN','iso_alpha3' => 'TUN','iso_numeric' => '788','calling_code' => '216','currency_code' => 'TND','currency_name' => 'Dinar','currency_symbol' => ''),
          array('name' => 'Turkey','iso_alpha2' => 'TR','iso_alpha3' => 'TUR','iso_numeric' => '792','calling_code' => '90','currency_code' => 'TRY','currency_name' => 'Lira','currency_symbol' => 'YTL'),
          array('name' => 'Turkmenistan','iso_alpha2' => 'TM','iso_alpha3' => 'TKM','iso_numeric' => '795','calling_code' => '993','currency_code' => 'TMM','currency_name' => 'Manat','currency_symbol' => 'm'),
          array('name' => 'Turks and Caicos Islands','iso_alpha2' => 'TC','iso_alpha3' => 'TCA','iso_numeric' => '796','calling_code' => '1649','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Tuvalu','iso_alpha2' => 'TV','iso_alpha3' => 'TUV','iso_numeric' => '798','calling_code' => '688','currency_code' => 'AUD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'U.S. Virgin Islands','iso_alpha2' => 'VI','iso_alpha3' => 'VIR','iso_numeric' => '850','calling_code' => '1340','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Uganda','iso_alpha2' => 'UG','iso_alpha3' => 'UGA','iso_numeric' => '800','calling_code' => '256','currency_code' => 'UGX','currency_name' => 'Shilling','currency_symbol' => ''),
          array('name' => 'Ukraine','iso_alpha2' => 'UA','iso_alpha3' => 'UKR','iso_numeric' => '804','calling_code' => '380','currency_code' => 'UAH','currency_name' => 'Hryvnia','currency_symbol' => '₴'),
          array('name' => 'United Arab Emirates','iso_alpha2' => 'AE','iso_alpha3' => 'ARE','iso_numeric' => '784','calling_code' => '971','currency_code' => 'AED','currency_name' => 'Dirham','currency_symbol' => ''),
          array('name' => 'United Kingdom','iso_alpha2' => 'GB','iso_alpha3' => 'GBR','iso_numeric' => '826','calling_code' => '44','currency_code' => 'GBP','currency_name' => 'Pound','currency_symbol' => '£'),
          array('name' => 'United States','iso_alpha2' => 'US','iso_alpha3' => 'USA','iso_numeric' => '840','calling_code' => '1','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'United States Minor Outlying Islands','iso_alpha2' => 'UM','iso_alpha3' => 'UMI','iso_numeric' => '581','calling_code' => '','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
          array('name' => 'Uruguay','iso_alpha2' => 'UY','iso_alpha3' => 'URY','iso_numeric' => '858','calling_code' => '598','currency_code' => 'UYU','currency_name' => 'Peso','currency_symbol' => '$U'),
          array('name' => 'Uzbekistan','iso_alpha2' => 'UZ','iso_alpha3' => 'UZB','iso_numeric' => '860','calling_code' => '998','currency_code' => 'UZS','currency_name' => 'Som','currency_symbol' => 'лв'),
          array('name' => 'Vanuatu','iso_alpha2' => 'VU','iso_alpha3' => 'VUT','iso_numeric' => '548','calling_code' => '678','currency_code' => 'VUV','currency_name' => 'Vatu','currency_symbol' => 'Vt'),
          array('name' => 'Vatican','iso_alpha2' => 'VA','iso_alpha3' => 'VAT','iso_numeric' => '336','calling_code' => '39','currency_code' => 'EUR','currency_name' => 'Euro','currency_symbol' => '€'),
          array('name' => 'Venezuela','iso_alpha2' => 'VE','iso_alpha3' => 'VEN','iso_numeric' => '862','calling_code' => '58','currency_code' => 'VEF','currency_name' => 'Bolivar','currency_symbol' => 'Bs'),
          array('name' => 'Vietnam','iso_alpha2' => 'VN','iso_alpha3' => 'VNM','iso_numeric' => '704','calling_code' => '84','currency_code' => 'VND','currency_name' => 'Dong','currency_symbol' => '₫'),
          array('name' => 'Wallis and Futuna','iso_alpha2' => 'WF','iso_alpha3' => 'WLF','iso_numeric' => '876','calling_code' => '681','currency_code' => 'XPF','currency_name' => 'Franc','currency_symbol' => ''),
          array('name' => 'Western Sahara','iso_alpha2' => 'EH','iso_alpha3' => 'ESH','iso_numeric' => '732','calling_code' => '','currency_code' => 'MAD','currency_name' => 'Dirham','currency_symbol' => ''),
          array('name' => 'Yemen','iso_alpha2' => 'YE','iso_alpha3' => 'YEM','iso_numeric' => '887','calling_code' => '967','currency_code' => 'YER','currency_name' => 'Rial','currency_symbol' => '﷼'),
          array('name' => 'Zambia','iso_alpha2' => 'ZM','iso_alpha3' => 'ZMB','iso_numeric' => '894','calling_code' => '260','currency_code' => 'ZMK','currency_name' => 'Kwacha','currency_symbol' => 'ZK'),
          array('name' => 'Zimbabwe','iso_alpha2' => 'ZW','iso_alpha3' => 'ZWE','iso_numeric' => '716','calling_code' => '263','currency_code' => 'ZWD','currency_name' => 'Dollar','currency_symbol' => 'Z$')
        );
        
        $output = array();
        foreach ($countries as $key => $value)
        {
            if($return=='country') $output[$value['iso_alpha2']] = $value['name'];        
            else if($return=='currency_name') $output[$value['currency_code']] = $value['currency_code']." (".$value['currency_name'].")";        
            else if($return=='currecny_icon') $output[$value['currency_code']] = !empty($value['currency_symbol']) ? $value['currency_symbol'] : $value['currency_code'];
            else $output[$value['iso_alpha2']] = $value['calling_code'];
        }
        if(isset($output[''])) unset($output['']);   

        asort($output);
        return $output;
    }


    function _payment_package()
    {
        $payment_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0)),$select='',$join='',$limit='',$start=NULL,$order_by='price');
        $return_val=array();
        $config_data=$this->basic->get_data("payment_config");
        $currency=$config_data[0]["currency"];
        foreach ($payment_package as $row)
        {
            $return_val[$row['id']]=$row['package_name']." : Only @".$currency." ".$row['price']." for ".$row['validity']." days";
        }
        return $return_val;
    }

    protected function real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function get_general_content($url,$proxy=""){


            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method


            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          //  curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
           // curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");

            $content = curl_exec($ch); // run the whole process
            curl_close($ch);

            return json_encode($content);

    }


    function get_general_content_with_checking($url,$proxy=""){


            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method


            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          //  curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
           // curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");

            $content = curl_exec($ch); // run the whole process
            $response['content'] = $content;

            $res = curl_getinfo($ch);
            if($res['http_code'] != 200)
                $response['error'] = 'error';
            curl_close($ch);
            return json_encode($response);

    }
    //=======================GET DATA FUNCTIONS ======================
    //================================================================



    //================================================================
    //=========================WEBSITE FUNCTIOS=======================
    public function _random_number_generator($length=6)
    {
        $rand = substr(uniqid(mt_rand(), true), 0, $length);
        return $rand;
    }


    public function forgot_password()
    {
        $data["page_title"] = $this->lang->line("Password Recovery");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/forgot_password.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/forgot_password";
        else
            $body_load = "site/modern/forgot_password";

        $data['body']=$body_load;
        $this->_subscription_viewcontroller($data);
    }


    public function code_genaration()
    {
        $this->ajax_check();

        $email = trim($this->input->post('email',true));
        $result = $this->basic->get_data('users', array('where' => array('email' => $email)), array('count(*) as num'));

        if ($result[0]['num'] == 1) {
            //entry to forget_password table
            $expiration = date("Y-m-d H:i:s", strtotime('+1 day', time()));
            $code = $this->_random_number_generator();
            $url = site_url().'home/password_recovery';
            $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
            $productname = $this->config->item('product_name');

            $table = 'forget_password';
            $info = array(
                'confirmation_code' => $code,
                'email' => $email,
                'expiration' => $expiration
                );

            if ($this->basic->insert_data($table, $info)) {

                //email to user
                $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'reset_password')),array('subject','message'));

                if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') {

                    $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                    $message =str_replace(array("#APP_NAME#","#PASSWORD_RESET_URL#","#PASSWORD_RESET_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);

                } else {

                    $subject = $productname." | Password recovery";
                    $message = "<p>".$this->lang->line('to reset your password please perform the following steps')." : </p>
                                <ol>
                                    <li>".$this->lang->line("go to this url")." : ".$url_final."</li>
                                    <li>".$this->lang->line("enter this code")." : ".$code."</li>
                                    <li>".$this->lang->line("reset your password")."</li>
                                </ol>
                                <h4>".$this->lang->line("link and code will be expired after 24 hours")."</h4>";

                }


                $from = $this->config->item('institute_email');
                $to = $email;
                $mask = $this->config->item("product_name");
                $html = 1;
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
            }
        } else {
            echo 0;
        }
    }


    public function password_recovery()
    {
        $data['page_title']=$this->lang->line("password recovery");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/password_recovery.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/password_recovery";
        else
            $body_load = "site/modern/password_recovery";

        $data['body']=$body_load;
        $this->_subscription_viewcontroller($data);
    }


    public function recovery_check()
    {
        $this->ajax_check();
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $newp=md5($this->input->post('newp', true));
            $conf=md5($this->input->post('conf', true));

            if($code=="" || $newp=="" || $conf=="" || ($newp != $conf) )
            {
                echo 0;
                exit();
            }

            $table='forget_password';
            $where['where']=array('confirmation_code'=>$code,'success'=>0);
            $select=array('email','expiration');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $email=$row['email'];
                    $expiration=$row['expiration'];
                }

                $now=time();
                $exp=strtotime($expiration);

                if ($now>$exp) {
                    echo 1;
                } else {
                    $student_info_where['where'] = array('email'=>$email);
                    $student_info_select = array('id');
                    $student_info_id = $this->basic->get_data('users', $student_info_where, $student_info_select);
                    $this->basic->update_data('users', array('id'=>$student_info_id[0]['id']), array('password'=>$newp));
                    $this->basic->update_data('forget_password', array('confirmation_code'=>$code), array('success'=>1));
                    echo 2;
                }
            }
        }
    }


    function _mail_sender($from = '', $to = '', $subject = '', $message = '', $mask = "", $html = 1, $smtp = 1,$attachement="",$test_mail="")
    {
        if ($to!= '' && $subject!='' && $message!= '')
        {
            if($this->config->item('email_sending_option') == '') $email_sending_option = 'smtp';
            else $email_sending_option = $this->config->item('email_sending_option');

            if($test_mail == 1) $email_sending_option = 'smtp';

          //  if($smtp != '1') $message=$message."<br/><br/>".$this->lang->line("The email was sent by"). ": ".$from;

            if($email_sending_option == 'smtp')
            {
                if ($smtp == '1') {
                    $where2 = array("where" => array('status' => '1','deleted' => '0'));
                    $email_config_details = $this->basic->get_data("email_config", $where2, $select = '', $join = '', $limit = '', $start = '', $group_by = '', $num_rows = 0);

                    if (count($email_config_details) == 0) {
                        $this->load->library('email');
                    } else {
                        foreach ($email_config_details as $send_info) {
                            $send_email = trim($send_info['email_address']);
                            $smtp_host = trim($send_info['smtp_host']);
                            $smtp_port = trim($send_info['smtp_port']);
                            $smtp_user = trim($send_info['smtp_user']);
                            $smtp_password = trim($send_info['smtp_password']);
                            $smtp_type = trim($send_info['smtp_type']);
                        }

                    /*****Email Sending Code ******/
                    $config = array(
                      'protocol' => 'smtp',
                      'smtp_host' => "{$smtp_host}",
                      'smtp_port' => "{$smtp_port}",
                      'smtp_user' => "{$smtp_user}", // change it to yours
                      'smtp_pass' => "{$smtp_password}", // change it to yours
                      'mailtype' => 'html',
                      'charset' => 'utf-8',
                      'newline' =>  "\r\n",
                      'set_crlf'=> "\r\n",
                      'smtp_timeout' => '30',
                      'wrapchars'   => '998'
                     );
                    if($smtp_type != 'Default')
                        $config['smtp_crypto'] = $smtp_type;

                        $this->load->library('email', $config);
                    }
                } /*** End of If Smtp== 1 **/

                if (isset($send_email) && $send_email!= "") {
                    $from = $send_email;
                }
                $this->email->from($from, $mask);
                $this->email->to($to);
                $this->email->subject($subject);
                $this->email->message($message);
                if ($html == 1) {
                    $this->email->set_mailtype('html');
                }
                if ($attachement!="") {
                    $this->email->attach($attachement);
                }

                if ($this->email->send()) {
                    return true;
                } else {

                    if($test_mail==1) {
                        return $this->email->print_debugger();
                    } else {
                        return false;
                    }
                }                
            }

            if($email_sending_option == 'php_mail')
            {
                $from = get_domain_only(base_url());
                $from = "support@".$from;
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$from}" . "\r\n";
                if(mail($to, $subject, $message, $headers))
                    return true;
                else
                    return false;
            }



        } else {
            return false;
        }
    }


    public function download_page_loader()
    {
        $this->load->view('page/download');
    }
    public function sign_up()
    {
        $signup_form = $this->config->item('enable_signup_form');

        if($signup_form == '0') 
        {
            return $this->login_page();
        }
        $data['num1']=$this->_random_number_generator(1);
        $data['num2']=$this->_random_number_generator(1);
        $captcha= $data['num1']+ $data['num2'];
        $this->session->set_userdata("sign_up_captcha",$captcha);
        
        $data["page_title"] = $this->lang->line("Sign Up");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/sign_up.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/sign_up";
        else
            $body_load = "site/modern/sign_up";

        $data["body"] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function affiliate_commission($affiliate_id=0,$userid=0,$event='',$package_price=0)
    {
        // if($affiliate_id==0 || $userid==0 || $event=='') exit;

        $this->db->trans_start();
        // Individual Commission for affilate
        $check_affiliate_special = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id,'status'=>'1']]);
        $has_special_commission = isset($check_affiliate_special[0]['is_overwritten']) ? $check_affiliate_special[0]['is_overwritten']:'0';
        $is_affiliate_special_signup_commission = isset($check_affiliate_special[0]['is_signup_commission']) ? $check_affiliate_special[0]['is_signup_commission']:'0';
        $is_affiliate_special_payment_commission = isset($check_affiliate_special[0]['is_payment']) ? $check_affiliate_special[0]['is_payment']:'0';
        $is_affiliate_special_payment_type = isset($check_affiliate_special[0]['payment_type']) ? $check_affiliate_special[0]['payment_type']:"";

        // Common Payment for all affiliate
        $generic_signup_commission = $this->basic->get_data("affiliate_payment_settings");
        $is_generic_signup_commission = isset($generic_signup_commission[0]['signup_commission']) ? $generic_signup_commission[0]['signup_commission']:0;
        $is_generic_payment_commission = isset($generic_signup_commission[0]['payment_commission']) ? $generic_signup_commission[0]['payment_commission']:0;
        $generic_payment_type = isset($generic_signup_commission[0]['payment_type']) ? $generic_signup_commission[0]['payment_type']:"";

        $commission_amount = 0;

        $special_payment_data = [];
        $special_payment_data['affiliate_id'] = $affiliate_id;
        $special_payment_data['user_id'] = $userid;
        $special_payment_data['event'] = $event;
        $special_payment_data['event_date'] = date("Y-m-d");
        // echo $check_affiliate_special[0]['fixed_amount'];exit;   
        if($has_special_commission =='1') {
            
            if(isset($event) && $event == "signup") {
                if($is_affiliate_special_signup_commission == '1') {
                    $commission_amount = isset($check_affiliate_special[0]['signup_amount']) ? $check_affiliate_special[0]['signup_amount']:0;
                }
                else if($is_generic_signup_commission == '1') {
                    $commission_amount = isset($generic_signup_commission[0]['sign_up_amount']) ? $generic_signup_commission[0]['sign_up_amount']:0;
                }

            } else if(isset($event) && $event == "payment"){

                if($is_affiliate_special_payment_commission == '1') {

                    if(isset($is_affiliate_special_payment_type) && $is_affiliate_special_payment_type == 'fixed') {

                        if(isset($check_affiliate_special[0]['fixed_amount']) && !empty($check_affiliate_special[0]['fixed_amount'])) {
                            $commission_amount = $check_affiliate_special[0]['fixed_amount'];

                        } else if(isset($generic_signup_commission[0]['fixed_amount']) && !empty($generic_signup_commission[0]['fixed_amount'])) {
                            $commission_amount = $generic_signup_commission[0]['fixed_amount'];
                        }
                    } else if(isset($is_affiliate_special_payment_type) && $is_affiliate_special_payment_type == 'percentage') {

                        if(isset($check_affiliate_special[0]['percentage_amount']) && !empty($check_affiliate_special[0]['percentage_amount'])) {

                            $percentage_for_affiliate = $check_affiliate_special[0]['percentage_amount'];

                        } else if(isset($generic_signup_commission[0]['percentage']) && !empty($generic_signup_commission[0]['percentage'])) {

                            $percentage_for_affiliate = $generic_signup_commission[0]['percentage'];

                        } else {
                            $percentage_for_affiliate = 0;
                        }


                        if($percentage_for_affiliate > 0) {
                            $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                        }
                    }
                } else if($is_generic_payment_commission == '1') {

                    if($generic_payment_type == 'fixed') {
                        $commission_amount = isset($generic_signup_commission[0]['fixed_amount']) ? $generic_signup_commission[0]['fixed_amount']:0;

                    } else if($generic_payment_type == 'percentage') {
                        $percentage_for_affiliate = isset($generic_signup_commission[0]['percentage']) ? $generic_signup_commission[0]['percentage']:0;
                        if($percentage_for_affiliate > 0) {
                            $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                        }
                    }
                } 
            }

            $special_payment_data['amount'] = $commission_amount;

            if($commission_amount > 0) {
                $this->basic->insert_data("affiliate_earning_history",$special_payment_data);
                $affiliate_total_earn = $check_affiliate_special[0]['total_earn'] + $commission_amount;
                $this->basic->update_data("affiliate_users",['id'=>$affiliate_id],['total_earn'=>$affiliate_total_earn]);
            }

        } else if(!empty($generic_signup_commission)) {
            if($event == "signup" && $is_generic_signup_commission == '1') {
                $commission_amount = isset($generic_signup_commission[0]['sign_up_amount']) ? $generic_signup_commission[0]['sign_up_amount']:0;

            } else if($event == "payment" && $is_generic_payment_commission == '1'){

                if($generic_payment_type == 'fixed') {
                    $commission_amount = isset($generic_signup_commission[0]['fixed_amount']) ? $generic_signup_commission[0]['fixed_amount']:0;

                } else if($generic_payment_type == 'percentage') {
                    $percentage_for_affiliate = isset($generic_signup_commission[0]['percentage']) ? $generic_signup_commission[0]['percentage']:0;
                    $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                }
            }

            $special_payment_data['amount'] = $commission_amount;

            if($commission_amount > 0) {
                $this->basic->insert_data("affiliate_earning_history",$special_payment_data);
                $affiliate_total_earn = $check_affiliate_special[0]['total_earn'] + $commission_amount;
                $this->basic->update_data("affiliate_users",['id'=>$affiliate_id],['total_earn'=>$affiliate_total_earn]);
            }

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
            exit();
        }
    }

    public function sign_up_action()
    {
        $enable_signup_activation = $this->config->item('enable_signup_activation');
        if($enable_signup_activation == '') $enable_signup_activation='1';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if($_POST) {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("name").'</b>', 'trim|required');
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email|is_unique[users.email]');
            // $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("mobile").'</b>', 'trim');
            $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'.$this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
            $this->form_validation->set_rules('captcha', '<b>'.$this->lang->line("captcha").'</b>', 'trim|required|integer');

            if($this->form_validation->run() == FALSE)
            {
                $this->sign_up();
            }
            else
            {
                $this->csrf_token_check();
                $captcha = $this->input->post('captcha', TRUE);
                if($captcha!=$this->session->userdata("sign_up_captcha"))
                {
                    $this->session->set_userdata("sign_up_captcha_error",$this->lang->line("invalid captcha"));
                    return $this->sign_up();

                }

                $name = strip_tags($this->input->post('name', TRUE));
                $email = $this->input->post('email', TRUE);
                // $mobile = $this->input->post('mobile', TRUE);
                $password = $this->input->post('password', TRUE);
                
                $affiliate_id = $this->input->cookie("affiliate_id");

                // affiliator section
                if(isset($affiliate_id) && !empty($affiliate_id)) {

                    $convertidintobinary = pack("H*", $affiliate_id);
                    $explode_binarycontactid = explode("-", $convertidintobinary);
                    $affiliate_id = $explode_binarycontactid[0];
                }
                // affiliator section end

                if($affiliate_id == '') $affiliate_id = 0;
                // $this->db->trans_start();

                $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));

                if(is_array($default_package) && array_key_exists(0, $default_package))
                {
                    $validity=$default_package[0]["validity"];
                    $package_id=$default_package[0]["id"];

                    $to_date=date('Y-m-d');
                    $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
                }

                $code = $this->_random_number_generator();
                $data = array(
                    'name' => $name,
                    'email' => $email,
                    // 'mobile' => $mobile,
                    'password' => md5($password),
                    'user_type' => 'Member',
                    'status' => '0',
                    'activation_code' => $code,
                    'expired_date'=>$expiry_date,
                    'package_id'=>$package_id,
                    'affiliate_id' => $affiliate_id,
                    );
                if($enable_signup_activation=='0') $data['status']='1';

                if ($this->basic->insert_data('users', $data)) {

                    if($this->addon_exist("affiliate_system")) {
                        if($affiliate_id != 0) {

                            $userid_through_affiliate = $this->db->insert_id();
                            $visitor_ip = $this->real_ip();
                            
                            $visitors_data = [];
                            $visitors_data['affiliate_id'] = $affiliate_id;
                            $visitors_data['user_id'] = $userid_through_affiliate;
                            $visitors_data['type'] = 'signup';
                            $visitors_data['ip_address'] = $visitor_ip;
                            $visitors_data['clicked_time'] = date("Y-m-d H:i:s");
                            $this->basic->insert_data('affiliate_visitors_action', $visitors_data);

                            $this->affiliate_commission($affiliate_id,$userid_through_affiliate,"signup");
                        }
                    }

                    $mail_service_id = $this->config->item('mail_service_id');
                    $system_short_name= $this->config->item('product_short_name');
                    $mailchimp_list_tag="Sign up - {$system_short_name}";

                    if($mail_service_id!="")
                    $this->send_email_to_autoresponder($mail_service_id, $email,$name,'','singnup','0',$mailchimp_list_tag);

                    //email to user
                    if($enable_signup_activation=='1')
                    {
                        $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"signup_activation")),array('subject','message'));
                        $url = site_url()."home/account_activation";
                        $url_final = "<a href='".$url."' target='_BLANK'>".$url."</a>";
                        $productname = $this->config->item('product_name');

                        if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '')
                        {
                            $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                            $message = str_replace(array("#APP_NAME#","#ACTIVATION_URL#","#ACCOUNT_ACTIVATION_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);
                            // echo "Database Has data"; exit();

                        } else
                        {
                            $subject = $productname." | Account activation";
                            $message = "<p>".$this->lang->line("to activate your account please perform the following steps")."</p>
                                        <ol>
                                            <li>".$this->lang->line("go to this url").":".$url_final."</li>
                                            <li>".$this->lang->line("enter this code").":".$code."</li>
                                            <li>".$this->lang->line("activate your account")."</li>
                                        </ol>";
                        }

                        $from = $this->config->item('institute_email');
                        $to = $email;
                        $mask = $this->config->item("product_name");
                        $html = 1;

                        $this->_mail_sender($from, $to, $subject, $message, $mask, $html);

                        $this->session->set_userdata('reg_success',1);
                        return $this->sign_up();
                    }
                    else return $this->login_page();
                }

            }

        }
    }

    public function account_activation()
    {
        $data["page_title"] = $this->lang->line("Account Activation");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/account_activation.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/account_activation";
        else
            $body_load = "site/modern/account_activation";

        $data["body"] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function account_activation_action()
    {
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $email=$this->input->post('email', true);

            $table='users';
            $where['where']=array('activation_code'=>$code,'email'=>$email,'status'=>"0");
            $select=array('id');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $user_id=$row['id'];
                }

                $this->basic->update_data('users', array('id'=>$user_id), array('status'=>'1'));
                echo 2;

            }
        }
    }


    public function email_contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST)
        {
            $redirect_url=site_url("home#contact");

            $this->form_validation->set_rules('email',                    '<b>'.$this->lang->line("email").'</b>',              'trim|required|valid_email');
            $this->form_validation->set_rules('subject',                  '<b>'.$this->lang->line("message subject").'</b>',            'trim|required');
            $this->form_validation->set_rules('message',                  '<b>'.$this->lang->line("message").'</b>',            'trim|required');
            $this->form_validation->set_rules('captcha',                  '<b>'.$this->lang->line("captcha").'</b>',            'trim|required|integer');

            if ($this->form_validation->run() == false)
            {
                return $this->index();
            }
            else
            {
                $captcha = $this->input->post('captcha', TRUE);

                if($captcha!=$this->session->userdata("contact_captcha"))
                {
                    $this->session->set_userdata("contact_captcha_error",$this->lang->line("invalid captcha"));
                    redirect($redirect_url, 'location');
                    exit();
                }


                $email = $this->input->post('email', true);
                $subject = $this->config->item("product_name")." | ".$this->input->post('subject', true);
                $message = $this->input->post('message', true);
                $message=$message."<br/><br/>".$this->lang->line("The email was sent by"). ": ".$email;

                $this->_mail_sender($from = $email, $to = $this->config->item("institute_email"), $subject, $message, $this->config->item("product_name"),$html=1);
                $this->session->set_userdata('mail_sent', 1);

                redirect($redirect_url, 'location');
            }
        }
    }

    public function privacy_policy()
    {
         $data['page_title'] = 'Privacy Policy';
         $current_theme = $this->config->item('current_theme');
         if($current_theme == '') $current_theme = 'modern';
         $body_file_path = "views/site/".$current_theme."/privacy_policy.php";
         if(file_exists(APPPATH.$body_file_path))
             $body_load = "site/".$current_theme."/privacy_policy";
         else
             $body_load = "site/modern/privacy_policy";
         $data['body'] = $body_load;
         $this->_front_viewcontroller($data);
    }

    public function terms_use()
    {
         $data['page_title'] = 'Terms of Use';
         $current_theme = $this->config->item('current_theme');
         if($current_theme == '') $current_theme = 'modern';
         $body_file_path = "views/site/".$current_theme."/terms_use.php";
         if(file_exists(APPPATH.$body_file_path))
             $body_load = "site/".$current_theme."/terms_use";
         else
             $body_load = "site/modern/terms_use";
         $data['body'] = $body_load;
         $this->_front_viewcontroller($data);
    }

    public function gdpr()
    {
         $data['page_title'] = 'GDPR';
         $current_theme = $this->config->item('current_theme');
         if($current_theme == '') $current_theme = 'modern';
         $body_file_path = "views/site/".$current_theme."/gdpr.php";
         if(file_exists(APPPATH.$body_file_path))
             $body_load = "site/".$current_theme."/gdpr";
         else
             $body_load = "site/modern/gdpr";
         $data['body']=$body_load;
         $this->_front_viewcontroller($data);
    }

    public function allow_cookie()
    {
        $this->session->set_userdata('allow_cookie','yes');
        // redirect($_SERVER['HTTP_REFERER'],'location');
    }

    //=========================WEBSITE FUNCTIOS=======================
    //================================================================




    //==========================================================================
    //=======================USAGE LOG & LICENSE FUNCTIONS======================
    public function _insert_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {

        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");
        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);

        if($this->basic->is_exist("usage_log",$where))
        {
            $this->db->set('usage_count', 'usage_count+'.$usage_count, FALSE);
            $this->db->where($where);
            $this->db->update('usage_log');
        }
        else $this->basic->insert_data("usage_log",$insert_data);

        return true;
    }

    public function _delete_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {
        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");

        if($this->basic->is_exist("modules",array("id"=>$module_id,"extra_text"=>""),"id"))
        {
            $existing_info = $this->basic->get_data('usage_log',array('where'=>array('module_id'=>$module_id,'usage_count >='=>1,'user_id'=>$user_id)));
            if(!empty($existing_info))
            {
                $where=array("id"=>$existing_info[0]['id'],"user_id"=>$user_id);
                $this->db->set('usage_count', 'usage_count-'.$usage_count, FALSE);
                $this->db->where($where);
                $this->db->update('usage_log');
            }
        }
        else
        {
            $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);
            $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);

            if($this->basic->is_exist("usage_log",$where))
            {
                $this->db->set('usage_count', 'usage_count-'.$usage_count, FALSE);
                $this->db->where($where);
                $this->db->update('usage_log');
            }
        }

        return true;
    }

    public function _check_usage($module_id=0,$request=0,$user_id=0)
    {
        if($module_id==0 || $request==0) return "0";
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        if($this->basic->is_exist("modules",array("id"=>$module_id,"extra_text"=>""),"id")) // not monthly limit modules
        {
            $this->db->select_sum('usage_count');
            $this->db->where('user_id', $user_id);
            $this->db->where('module_id', $module_id);
            $info = $this->db->get('usage_log')->result_array(); 

            $usage_count=0;
            if(isset($info[0]["usage_count"]))
            $usage_count=$info[0]["usage_count"];
        }
        else
        {
            $usage_month=date("n");
            $usage_year=date("Y");
            $info=$this->basic->get_data("usage_log",$where=array("where"=>array("usage_month"=>$usage_month,"usage_year"=>$usage_year,"module_id"=>$module_id,"user_id"=>$user_id)));
            $usage_count=0;
            if(isset($info[0]["usage_count"]))
            $usage_count=$info[0]["usage_count"];
        }

        

        $monthly_limit=array();
        $bulk_limit=array();
        $module_ids=array();

        if($this->session->userdata("package_info")!="")
        {
            $package_info=$this->session->userdata("package_info");
            if($this->session->userdata('user_type') == 'Admin') return "1";
        }
        else
        {
            $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
            $package_info=array();
            if(array_key_exists(0, $package_data))
            $package_info=$package_data[0];
            if($package_info['user_type'] == 'Admin') return "1";
        }

        if(isset($package_info["bulk_limit"]))    $bulk_limit=json_decode($package_info["bulk_limit"],true);
        if(isset($package_info["monthly_limit"])) $monthly_limit=json_decode($package_info["monthly_limit"],true);
        if(isset($package_info["module_ids"]))    $module_ids=explode(',', $package_info["module_ids"]);

        $return = "0";
        if(in_array($module_id, $module_ids) && $bulk_limit[$module_id] > 0 && $bulk_limit[$module_id]<$request)
         $return = "2"; // bulk limit crossed | 0 means unlimited
        else if(in_array($module_id, $module_ids) && $monthly_limit[$module_id] > 0 && $monthly_limit[$module_id]<($request+$usage_count))
         $return = "3"; // montly limit crossed | 0 means unlimited
        else  $return = "1"; //success

        return $return;
    }

    public function print_limit_message($module_id=0,$request=0)
    {
        $status=$this->_check_usage($module_id,$request);
        if($status=="2")
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3")
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }

    }

    public function member_validity()
    {
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') {
            $where['where'] = array('id'=>$this->session->userdata('user_id'));
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$this->session->userdata("user_id"))),$select="package.price as price",$join=array('package'=>"users.package_id=package.id,left"));
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            redirect('payment/buy_package','Location');
        }
    }

    public function important_feature()
    {
		//bugs
    }
    public function credential_check($secret_code=0)
    {
		//bugs
    }

    public function credential_check_action()
    {
		//bugs
    }

    public function code_activation_check_action($purchase_code,$only_domain,$periodic=0)
    {
		//bugs
    }

    public function periodic_check(){
		//bugs
    }


    public function license_check()
    {
		//bugs
    }

    public function license_check_action()
    {
        $encoded = file_get_contents(APPPATH . 'core/licence_type.txt');
        $encrypt_method = "AES-256-CBC";
        $secret_key = 't8Mk8fsJMnFw69FGG5';
        $secret_iv = '9fljzKxZmMmoT358yZ';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $decoded = openssl_decrypt(base64_decode($encoded), $encrypt_method, $key, 0, $iv);

        $decoded = explode('_', $decoded);
        $decoded = array_pop($decoded);
        $this->session->set_userdata('license_type',$decoded);
    }

    public function php_info()
    {
        if($this->session->userdata('user_type')== 'Admin')
        echo phpinfo();
        else redirect('home/access_forbidden', 'location');
    }
    //=======================USAGE LOG & LICENSE FUNCTIONS======================
    //==========================================================================




    //================================================================
    //========================= ADDON FUNCTIONS ======================
    //loads language files of addons
    protected function language_loader_addon()
    {    
        
        $controller_name=strtolower($this->uri->segment(1));
        $path_without_filename="application/modules/".$controller_name."/language/".$this->language."/";
        if(file_exists($path_without_filename.$controller_name."_lang.php"))
        {
            $filename=$controller_name;
            $this->lang->load($filename,$this->language,FALSE,TRUE,$path_without_filename);
        }

    }

    // delete any direcory with it childs even it is not empty
    protected function delete_directoryX($dirPath="") 
    {
        if (!is_dir($dirPath)) 
        return false;

        if(substr($dirPath, strlen($dirPath) - 1, 1) != '/') $dirPath .= '/';
        
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach($files as $file) 
        {
            if(is_dir($file)) $this->delete_directory($file);             
            else @unlink($file);            
        }
        rmdir($dirPath);
    }

    protected function delete_directory($dirPath="")
    {
        if (!is_dir($dirPath)) 
        return false;

        $files = new DirectoryIterator($dirPath);
        foreach ($files as $file)
        {
            // check if not . or ..
            if (!$file->isDot())
            {
                $file->isDir() ? $this->delete_directory($file->getPathname()) : unlink($file->getPathname());
            }
        }
        rmdir($dirPath);
        return;
    }

    // takes addon controller path as input and extract add on data from comment block
    protected function get_addon_data($path="")
    {
        $path=str_replace('\\','/',$path);
        $tokens=token_get_all(file_get_contents($path));
        $addon_data=array();

        $addon_path=explode('/', $path);
        $controller_name=array_pop($addon_path);
        array_pop($addon_path);
        $addon_path=implode('/',$addon_path);

        $comments = array();
        foreach($tokens as $token) 
        {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) 
            {       
                $comments[] = isset( $token[1]) ?  $token[1] : "";
            } 
        }
        $comment_str=isset($comments[0]) ? $comments[0] : "";
        
        preg_match( '/^.*?addon name:(.*)$/mi', $comment_str, $match); 
        $addon_data['addon_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?unique name:(.*)$/mi', $comment_str, $match); 
        $addon_data['unique_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '#modules:(.*?)Project ID#si', $comment_str, $match); 
        $addon_data['modules'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?project id:(.*)$/mi', $comment_str, $match); 
        $addon_data['project_id'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?addon uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['addon_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author:(.*)$/mi', $comment_str, $match); 
        $addon_data['author'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['author_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?version:(.*)$/mi', $comment_str, $match); 
        $addon_data['version'] = isset($match[1]) ? trim($match[1]) : "1.0";

        preg_match( '/^.*?description:(.*)$/mi', $comment_str, $match); 
        $addon_data['description'] = isset($match[1]) ? trim($match[1]) : "";

        $addon_data['controller_name'] = isset($controller_name) ? trim($controller_name) : "";

        if(file_exists($addon_path.'/install.txt'))
        $addon_data['installed']='0';
        else $addon_data['installed']='1';  

        return $addon_data;
    }

    // checks purchase code , returns boolean
    protected function addon_credential_check($purchase_code="",$item_name="")
    {
		//bugs
    }

    // validataion of addon data
    protected function check_addon_data($addon_data=array())
    {
        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on unique name has not been provided.')));
            exit();
        }
        
        if(!$this->is_unique_check("addon_check",$addon_data['unique_name']))  //  unique name must be unique
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on is already active. Duplicate unique name found.')));
            exit();
        }
    }

    // inserts data to add_ons table + modules + menu + menuchild1 + removes install.txt, returns json status,message
    protected function register_addon($addon_controller_name="",$sidebar=array(),$sql=array(),$purchase_code="",$default_module_name="")
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }   

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }     

        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $install_txt_path=APPPATH."modules/".strtolower($addon_controller_name)."/install.txt"; // path of install.txt
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        $this->check_addon_data($addon_data);

        try 
        {
            $this->db->trans_start();
            
            // addon table entry
            $this->basic->insert_data("add_ons",array("add_on_name"=>$addon_data['addon_name'],"unique_name"=>$addon_data["unique_name"],"version"=>$addon_data["version"],"installed_at"=>date("Y-m-d H:i:s"),"purchase_code"=>$purchase_code,"module_folder_name"=>strtolower($addon_controller_name),"project_id"=>$addon_data["project_id"]));
            $add_ons_id=$this->db->insert_id();

            $parent_module_id="";
            $modules = isset($addon_data['modules']) ? json_decode(trim($addon_data['modules']),true) : array();

            if(json_last_error() === 0 && is_array($modules))
            {
                $module_ids = array_keys($modules);
                $parent_module_id=implode(',', $module_ids);

                foreach($modules as $key => $value) 
                {
                    if(!$this->basic->is_exist("modules",array("id"=>$key))) 
                    $this->basic->insert_data("modules",array("id"=>$key,"extra_text"=>$value['extra_text'],"module_name"=>$value['module_name'],'bulk_limit_enabled'=>$value['bulk_limit_enabled'],'limit_enabled'=>$value['limit_enabled'],"add_ons_id"=>$add_ons_id,"deleted"=>"0"));
                }
            }
            
            //--------------- sidebar entry--------------------
            //-------------------------------------------------
            if(is_array($sidebar))
            foreach ($sidebar as $key => $value) 
            {
                $parent_name        = isset($value['name']) ? $value['name'] : "";
                $parent_icon        = isset($value['icon']) ? $value['icon'] : "";
                $parent_url         = isset($value['url']) ? $value['url'] : "#";
                $parent_is_external = isset($value['is_external']) ? $value['is_external'] : "0";
                $child_info         = isset($value['child_info']) ? $value['child_info'] : array();
                $have_child         = isset($child_info['have_child']) ? $child_info['have_child'] : '0';
                $only_admin         = isset($value['only_admin']) ? $value['only_admin'] : '0';
                $only_member        = isset($value['only_member']) ? $value['only_member'] : '0';
                $parent_serial      = 50;
                           
                $parent_menu=array('name'=>$parent_name,'icon'=>$parent_icon,'url'=>$parent_url,'serial'=>$parent_serial,'module_access'=>$parent_module_id,'have_child'=>$have_child,'only_admin'=>$only_admin,'only_member'=>$only_member,'add_ons_id'=>$add_ons_id,'is_external'=>$parent_is_external);
                $this->basic->insert_data('menu',$parent_menu); // parent menu entry
                $parent_id=$this->db->insert_id();

                if($have_child=='1')
                {
                    if(!empty($child_info))
                    {
                        $child = isset($child_info['child']) ? $child_info['child'] : array();
                        
                        $child_serial=0;
                        if(!empty($child))
                        foreach ($child as $key2 => $value2) 
                        {
                            $child_serial++;
                            $child_name         = isset($value2['name']) ? $value2['name'] : "";
                            $child_icon         = isset($value2['icon']) ? $value2['icon'] : "";
                            $child_url          = isset($value2['url']) ? $value2['url'] : "#";
                            $child_info_1       = isset($value2['child_info']) ? $value2['child_info'] : array();
                            $child_is_external  = isset($value2['is_external']) ? $value2['is_external'] : "0";
                            $have_child         = isset($child_info_1['have_child']) ? $child_info_1['have_child'] : '0';
                            $only_admin         = isset($value2['only_admin']) ? $value2['only_admin'] : '0';
                            $only_member        = isset($value2['only_member']) ? $value2['only_member'] : '0';
                            $module_access      = isset($value2['module_access']) ? $value2['module_access'] : '';
                            if($module_access=='') $module_access = $parent_module_id;
                                            
                            $child_menu=array('name'=>$child_name,'icon'=>$child_icon,'url'=>$child_url,'serial'=>$child_serial,'module_access'=>$module_access,'parent_id'=>$parent_id,'have_child'=>$have_child,'only_admin'=>$only_admin,'only_member'=>$only_member,'is_external'=>$child_is_external);
                            $this->basic->insert_data('menu_child_1',$child_menu); // child menu entry
                            $sub_parent_id=$this->db->insert_id();

                            if($have_child=='1')
                            {
                                if(!empty($child_info_1))
                                {
                                    $child = isset($child_info_1['child']) ? $child_info_1['child'] : array();  
                                    
                                    $child_child_serial=0;
                                    if(!empty($child))
                                    foreach ($child as $key3 => $value3) 
                                    {
                                        $child_child_serial++;
                                        $child_name         = isset($value3['name']) ? $value3['name'] : "";
                                        $child_icon         = isset($value3['icon']) ? $value3['icon'] : "";
                                        $child_url          = isset($value3['url']) ? $value3['url'] : "#";
                                        $child_is_external  = isset($value3['is_external']) ? $value3['is_external'] : "0";
                                        $have_child         = '0';
                                        $only_admin         = isset($value3['only_admin']) ? $value3['only_admin'] : '0';
                                        $only_member        = isset($value3['only_member']) ? $value3['only_member'] : '0';
                                        $module_access2     = isset($value3['module_access']) ? $value3['module_access'] : '';
                                        if($module_access2=='') $module_access2 = $module_access;
                                                        
                                        $child_menu=array('name'=>$child_name,'icon'=>$child_icon,'url'=>$child_url,'serial'=>$child_child_serial,'module_access'=>$module_access2,'parent_child'=>$sub_parent_id,'only_admin'=>$only_admin,'only_member'=>$only_member,'is_external'=>$child_is_external);
                                        $this->basic->insert_data('menu_child_2',$child_menu); // child menu entry
                                        
                                    }
                                }
                            } 
                        }
                    }
                }            

            }
            //--------------- sidebar entry--------------------
            //-------------------------------------------------

            $this->db->trans_complete();
                 

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
                exit();
            }
            else 
            {   
                
                //--------Custom SQL------------
                $this->db->db_debug = FALSE; //disable debugging for queries
                if(is_array($sql))            
                foreach ($sql as $key => $query) 
                {
                    try
                    {
                        $this->db->query($query);
                    }
                    catch(Exception $e)
                    {
                    }                    
                }
                //--------Custom SQL------------                
                @unlink($install_txt_path); // removing install.txt                
                echo json_encode(array('status'=>'1','message'=>$this->lang->line('Add-on has been activated successfully.')));
            }

        } //end of try
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }

    // deletes data from add_ons table + modules + menu + menuchild1 + puts install.txt, returns json status,message
    protected function unregister_addon($addon_controller_name="")
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }


        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $install_txt_path=APPPATH."modules/".strtolower($addon_controller_name)."/install.txt"; // path of install.txt
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on unique name has not been provided.')));
            exit();
        }


        try 
        {
            $this->db->trans_start();
            
            // delete addon table entry
            $get_addon=$this->basic->get_data("add_ons",array("where"=>array("unique_name"=>$addon_data['unique_name'])));
            $add_ons_id=isset($get_addon[0]['id']) ? $get_addon[0]['id'] : 0;
            if($add_ons_id>0)
            $this->basic->delete_data("add_ons",array("id"=>$add_ons_id));
            
            // delete modules table entry    
            if($add_ons_id>0)        
            $this->basic->delete_data("modules",array("add_ons_id"=>$add_ons_id));

            // delete menu+menu_child1 table entry
            $get_menu=array();
            if($add_ons_id>0)   
            $get_menu=$this->basic->get_data("menu",array("where"=>array("add_ons_id"=>$add_ons_id)));
            
            foreach($get_menu as $key => $value) 
            {
               $parent_id=isset($value['id']) ? $value['id'] : 0;
               if($parent_id>0)
               {    
                  $this->basic->delete_data("menu",array("id"=>$parent_id));
                  $this->basic->delete_data("menu_child_1",array("parent_id"=>$parent_id));
               }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
                exit();
            }
            else 
            {   
                if(!file_exists($install_txt_path)) // putting install.txt
                fopen($install_txt_path, "w");

                echo json_encode(array('status'=>'1','message'=>$this->lang->line('Add-on has been deactivated successfully.')));
            }
        } 
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }

    // deletes data from add_ons table + modules + menu + menuchild1 + custom sql + folder, returns json status,message    
    protected function delete_addon($addon_controller_name="",$sql=array())
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $addon_path=APPPATH."modules/".strtolower($addon_controller_name); // path of module folder
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on unique name has not been provided.')));
            exit();
        }


        try 
        {
            $this->db->trans_start();
            
            // delete addon table entry
            $get_addon=$this->basic->get_data("add_ons",array("where"=>array("unique_name"=>$addon_data['unique_name'])));
            $add_ons_id=isset($get_addon[0]['id']) ? $get_addon[0]['id'] : 0;
            $purchase_code=isset($get_addon[0]['purchase_code']) ? $get_addon[0]['purchase_code'] : '';
            if($add_ons_id>0)
            $this->basic->delete_data("add_ons",array("id"=>$add_ons_id));
            
            // delete modules table entry    
            if($add_ons_id>0)        
            $this->basic->delete_data("modules",array("add_ons_id"=>$add_ons_id));

            // delete menu+menu_child1 table entry
            $get_menu=array();
            if($add_ons_id>0)   
            $get_menu=$this->basic->get_data("menu",array("where"=>array("add_ons_id"=>$add_ons_id)));
            
            foreach($get_menu as $key => $value) 
            {
               $parent_id=isset($value['id']) ? $value['id'] : 0;
               if($parent_id>0)
               {    
                  $this->basic->delete_data("menu",array("id"=>$parent_id));
                  $this->basic->delete_data("menu_child_1",array("parent_id"=>$parent_id));
               }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
                exit();
            }
            else 
            {   
                //--------Custom SQL------------
                $this->db->db_debug = FALSE; //disable debugging for queries
                if(is_array($sql))            
                foreach ($sql as $key => $query) 
                {
                    try
                    {
                        $this->db->query($query);
                    }
                    catch(Exception $e)
                    {
                    }                    
                }
                //--------Custom SQL------------             

                $this->delete_directory($addon_path);                  
                if($purchase_code!="")
                {
                    $item_name=strtolower($addon_controller_name);
                    $only_domain=get_domain_only(site_url());
                    $url = "http://xeroneit.net/development/envato_license_activation/delete_purchase_code.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat-{$item_name}";
                    $credentials = $this->get_general_content_with_checking($url);
                    $response = json_decode($credentials,true);
                    if(isset($response['error']))
                    {
                        $url = "https://mostofa.club/development/envato_license_activation/delete_purchase_code.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat-{$item_name}";
                        $this->get_general_content_with_checking($url);                    
                    }
                }
  
                echo json_encode(array('status'=>'1','message'=>$this->lang->line('add-on has been deleted successfully.')));
            }
        } 
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }


    // check a addon or module id is usable or already used, returns boolean, true if unique
    protected function is_unique_check($type='addon_check',$value="") // type=addon_check/module_check | $value=column.value
    {
        $is_unique=false;
        if($type=="addon_check")  $is_unique=$this->basic->is_unique("add_ons",array("unique_name"=>$value),"id");
        if($type=="module_check") $is_unique=$this->basic->is_unique("modules",array("id"=>$value),"id");
        return $is_unique;
    }

    //========================= ADDON FUNCTIONS ======================
    //================================================================

   

    protected function delete_full_access()
    {
        if($this->session->userdata('user_type') == 'Admin') exit();
        if(!isset($_POST)) exit();
        $user_id=$this->session->userdata('user_id');

        $this->db->trans_start();
        $sql = "show tables;";
        $a = $this->basic->execute_query($sql);
        foreach($a as $value)
        {
            foreach($value as $table_name)
            {
                if($table_name == 'users') $this->basic->delete_data('users',array('id'=>$user_id));
                if($this->db->field_exists('user_id',$table_name))
                    $this->basic->delete_data($table_name,array('user_id'=>$user_id));
            }
        }
        $this->db->trans_complete();                

        if ($this->db->trans_status() === FALSE) 
        {
            echo $this->lang->line('Something went wrong, please try again.');            
        }
        else
        {
            $this->session->sess_destroy();
            echo 'success';        
        }

    }


    protected function scanAll($myDir){

        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            //$dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);


            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);
            $dirTree[] = $file_path;

        }

        return $dirTree;

    }

    // =========================================================================================
    //===============================MESSENGER BOT FUNCTIONS====================================
    /******88*WEBHOOK,COMMON BOT ADDON FUNCTIONS,PUBLIC CURL CALLS, CRON SUB FUNCTIONS**88******/
    public function central_webhook_callback()
    {
        $url="";
        $challenge = $this->input->get_post('hub_challenge');
        $verify_token =$this->input->get_post('hub_verify_token');
        if($this->config->item("central_webhook_verify_token") != '')
        {
            if($verify_token === $this->config->item("central_webhook_verify_token"))
            {
                echo $challenge;
                die();
            }                
        }

        $response_raw=file_get_contents("php://input");

        if(!isset($response_raw) || $response_raw=='') exit; 

        $json_response=array("response_raw"=>$response_raw);
        $response = json_decode($response_raw, true);

        if(isset($response['entry'][0]['messaging']))
        {
          
            $url=base_url()."messenger_bot/webhook_callback_main";
            if(isset($response['entry']['0']['messaging'][0]['read'])) exit; 

        } 

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'comment') {
            $url=base_url()."comment_automation/webhook_callback_main";

            $commenter_id = isset($response['entry'][0]['changes'][0]['value']['sender_id']) ? $response['entry'][0]['changes'][0]['value']['sender_id'] : $response['entry'][0]['changes'][0]['value']['from']['id'];
            $page_id = $response['entry'][0]['id'];

            //If activity by Page it self, then exit
            if($page_id==$commenter_id) exit;

            // 2nd level relpy is turned off
            $post_id = isset($response['entry'][0]['changes'][0]['value']['parent_id']) ? $response['entry'][0]['changes'][0]['value']['parent_id']:"";
            $parent_id_page_id_array=explode("_", $post_id);
            $parent_id_page_id=isset($parent_id_page_id_array[0]) ? $parent_id_page_id_array[0] :"";

            if($page_id!=$parent_id_page_id){ // From 2nd reply Comment. 
                exit; 
            }

            //If already replied that comment, then exit 
            $comment_id = isset($response['entry'][0]['changes'][0]['value']['comment_id']) ? $response['entry'][0]['changes'][0]['value']['comment_id']:"";
            $already_replied_comment_id = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('comment_id'=>$comment_id)));
            if(!empty($already_replied_comment_id)) exit;          
        }

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'reaction')
        {
            exit;
        }

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'photo') 
        {
            if(isset($response['entry'][0]['changes'][0]['value']['verb']) && $response['entry'][0]['changes'][0]['value']['verb'] == 'edited')
                exit;
            $url=base_url()."comment_automation/webhook_callback_main";
        }

        else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'feed') 
            $url=base_url()."comment_automation/webhook_callback_main";

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'status') 
            $url=base_url()."comment_automation/webhook_callback_main";
        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'share')
            $url=base_url()."comment_automation/webhook_callback_main";

        else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'mentions')
            $url=base_url()."instagram_reply/webhook_callback";
        else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'comments')
            $url=base_url()."instagram_reply/webhook_callback";

        if($url=='') exit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_response);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $reply_response=curl_exec($ch);
    }
    
    public function send_reply_ez($access_token='',$reply='')
    {   
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
 
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);       
        
        $result=json_decode($st,TRUE);
        return $result;
    }

    protected function subscriber_info($access_token='',$sender_id='',$social_media_type="fb")
    {   
        if($social_media_type=='fb')
        $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token&fields=id,first_name,last_name,name,profile_pic,locale,timezone,gender";
        else
        $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token&fields=id,name,profile_pic";

        $result=$this->subscriber_info_curl_call($url);

        if(isset($result['error']) && $result['error']['code']==10 && $result['error']['error_subcode']==2018336){
            $result=array();
            $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token&fields=id,first_name,last_name,name,locale,timezone,gender";
            $result=$this->subscriber_info_curl_call($url);
            $result['profile_pic']="";
            
        }

        return $result;
    }

    protected function subscriber_info_curl_call($url){

        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);          
        return $result=json_decode($st,TRUE);
    }



    protected function send_reply($access_token='',$reply='')
    {   
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
    
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);       
        
        $result=json_decode($st,TRUE);
        return $result;
    }

    public function send_reply_curl_call()
    {
        ignore_user_abort(TRUE);
        $access_token=$_POST['access_token'];
        $reply=$_POST['reply'];
        
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
    
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");          $st=curl_exec($ch);      
        
        $result=json_decode($st,TRUE);
        return $result;
    }

    
    //DEPRECATED FUNCTION FOR QUICK BROADCAST
    public function unsubscribe_webhook_call()
    {    
        $psid=$this->input->post('psid');
        $fb_page_id=$this->input->post('fb_page_id');

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("page_id"=>$fb_page_id,"bot_enabled"=>"1")));
        $page_auto_id=isset($pageinfo[0]["id"])?$pageinfo[0]["id"]:"";
        $page_access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";

        $label_info=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_auto_id,"unsubscribe"=>"1")));
        $label_auto_id=isset($label_info[0]['id'])?$label_info[0]['id']:0;
        $label_id=isset($label_info[0]['label_id'])?$label_info[0]['label_id']:"";

        // $this->load->library('fb_rx_login');
        // $response= $this->fb_rx_login->assign_label($page_access_token,$psid,$label_id);

        $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));

        $contact_group_id=isset($subscriberdata[0]["contact_group_id"])?$subscriberdata[0]["contact_group_id"]:"";
        $explode=explode(',', $contact_group_id);
        array_push($explode, $label_auto_id);
        $new=array_unique($explode);
        $contact_group_id=implode(',', $new);
        $contact_group_id=trim($contact_group_id,',');
        $unsubscribe_time=date('Y-m-d H:i:s');

        $this->basic->update_data("messenger_bot_subscriber",array("subscribe_id"=>$psid,"page_id"=>$fb_page_id),array("contact_group_id"=>$contact_group_id,"permission"=>"0","unsubscribed_at"=>$unsubscribe_time));

        /** Adjust total count of subscriber & unsubscriber  **/

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='1' AND subscriber_type!='system'";
        $count_data = $this->db->query($sql)->row_array();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='0' AND subscriber_type!='system'";
        $count_data2 = $this->db->query($sql2)->row_array();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_auto_id),array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed));


    }
    
    public function resubscribe_webhook_call()
    {
    
        $psid=$this->input->post('psid');
        $fb_page_id=$this->input->post('fb_page_id');

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("page_id"=>$fb_page_id,"bot_enabled"=>"1")));
        $page_auto_id=isset($pageinfo[0]["id"])?$pageinfo[0]["id"]:"";
        $page_access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";

        $label_info=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_auto_id,"unsubscribe"=>"1")));
        $label_auto_id=isset($label_info[0]['id'])?$label_info[0]['id']:0;
        $label_id=isset($label_info[0]['label_id'])?$label_info[0]['label_id']:"";

        // $this->load->library('fb_rx_login');
        // $response= $this->fb_rx_login->deassign_label($page_access_token,$psid,$label_id);

        $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));

        $contact_group_id=isset($subscriberdata[0]["contact_group_id"])?$subscriberdata[0]["contact_group_id"]:"";
        $explode=explode(',', $contact_group_id);
        
        foreach(array_keys($explode, $label_auto_id) as $key) {
            unset($explode[$key]);
        }
        
        $new=array_unique($explode);
        $contact_group_id=implode(',', $new);
        $contact_group_id=trim($contact_group_id,',');

        $this->basic->update_data("messenger_bot_subscriber",array("subscribe_id"=>$psid,"page_id"=>$fb_page_id),array("contact_group_id"=>$contact_group_id,"permission"=>"1"));
        

        /** Adjust total count of subscriber & unsubscriber  **/

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='1' AND subscriber_type!='system'";
        $count_data = $this->db->query($sql)->row_array();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='0' AND subscriber_type!='system'";
        $count_data2 = $this->db->query($sql2)->row_array();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_auto_id),array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed));

    }
  
    public function multiple_assign_label($psid,$fb_page_id,$label_auto_ids,$social_media_type="fb",$subscriber_table_id)
    {
        $label_auto_ids=explode(",",$label_auto_ids);
        $label_auto_ids=array_filter($label_auto_ids);
        foreach($label_auto_ids as $value)
        {
            $value = trim($value);
            $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
                VALUES('$value','$subscriber_table_id');");
        }
    }



   
    public function assign_label_webhook_call()
    {
        $psid=$this->input->post('psid');
        $fb_page_id=$this->input->post('fb_page_id');
        $label_auto_ids=$this->input->post('label_auto_ids');
        $subscriber_table_id=$this->input->post('subscriber_table_id');
        $label_auto_ids=explode(",",$label_auto_ids);

        if($subscriber_table_id == 0)
        {
            $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));
            $subscriber_table_id = $subscriberdata[0]['id'] ?? 0;
        }
        
        foreach($label_auto_ids as $value)
            $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
                VALUES('$value','$subscriber_table_id');");
    }


       /**Sender action added 19.03.2018 by Konok**/
    
    public function sender_action($sender_id,$action_type,$post_access_token='')
    {
    
        $url = "https://graph.facebook.com/v2.6/me/messages?access_token={$post_access_token}";
        
        $post_data_array['recipient']['id']=$sender_id;
        $post_data_array['sender_action']=$action_type;
        $post_data=json_encode($post_data_array);
        $ch = curl_init();
        $headers = array("Content-type: application/json");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data); 
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
        $st=curl_exec($ch);  
        $result=json_decode($st,TRUE);
        return $result;
    }

    

    // admin account import
    public function redirect_rx_link()
    {
        if ($this->session->userdata('logged_in')!= 1) exit();

        $id=$this->session->userdata("fb_rx_login_database_id");

        $redirect_url = base_url()."home/redirect_rx_link/";

        $this->load->library('fb_rx_login');
        $user_info = $this->fb_rx_login->login_callback($redirect_url);

        if(isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = $this->lang->line("Something went wrong")." : ".$user_info['message'];
            $data['body'] = "facebook_rx/admin_login";
            $this->_viewcontroller($data);
        }
        else
        {
            $access_token=$user_info['access_token_set'];
            $where = array('id'=>$id);
            $update_data = array('user_access_token'=>$access_token);

            if($this->basic->update_data('facebook_rx_config',$where,$update_data))
            {

                $data = array(
                    'user_id' => $this->user_id,
                    'facebook_rx_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'email' => isset($user_info['email']) ? $user_info['email'] : "",
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d')
                    );

                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where);

                if(empty($exist_or_not))
                {
                    $this->basic->insert_data('facebook_rx_fb_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                    $this->basic->update_data('facebook_rx_fb_user_info',$where,$data);
                }

                $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);

                $page_list = $this->fb_rx_login->get_page_list($access_token);

                if(isset($page_list['error']) && $page_list['error'] == '1')
                {
                    $data['error'] = 1;
                    $data['message'] = $this->lang->line("Something went wrong")." : ".$page_list['message'];
                    $data['body'] = "facebook_rx/admin_login";
                    $this->_viewcontroller($data);
                    exit();
                }

                if(!empty($page_list))
                {
                    foreach($page_list as $page)
                    {
                        $user_id = $this->user_id;
                        $page_id = $page['id'];
                        $page_cover = '';
                        if(isset($page['cover']['source'])) $page_cover = $page['cover']['source'];
                        $page_profile = '';
                        if(isset($page['picture']['url'])) $page_profile = $page['picture']['url'];
                        $page_name = '';
                        if(isset($page['name'])) $page_name = $page['name'];
                        $page_username = '';
                        if(isset($page['username'])) $page_username = $page['username'];
                        $page_access_token = '';
                        if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                        $page_email = '';
                        if(isset($page['emails'][0])) $page_email = $page['emails'][0];

                        $data = array(
                            'user_id' => $user_id,
                            'facebook_rx_fb_user_info_id' => $facebook_table_id,
                            'page_id' => $page_id,
                            'page_cover' => $page_cover,
                            'page_profile' => $page_profile,
                            'page_name' => $page_name,
                            'username' => $page_username,
                            'page_access_token' => $page_access_token,
                            'page_email' => $page_email,
                            'add_date' => date('Y-m-d')
                            );

                        // instagram section
                        $instagram_account_exist_or_not = '';
                        if($this->config->item('instagram_reply_enable_disable') == '1')
                            $instagram_account_exist_or_not = $this->fb_rx_login->instagram_account_check_by_id($page['id'], $access_token);
                        
                        if ($instagram_account_exist_or_not != "") {
                            $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_account_exist_or_not, $access_token); 
                            $data['has_instagram'] = '1';
                            $data['instagram_business_account_id'] = $instagram_account_exist_or_not; 
                            $data['insta_username'] = isset($instagram_account_info['username']) ? $instagram_account_info['username'] : "";
                            $data['insta_followers_count'] = isset($instagram_account_info['followers_count']) ? $instagram_account_info['followers_count'] : "";
                            $data['insta_media_count'] = isset($instagram_account_info['media_count']) ? $instagram_account_info['media_count'] : "";
                            $data['insta_website'] = isset($instagram_account_info['website']) ? $instagram_account_info['website'] : "";
                            $data['insta_biography'] = isset($instagram_account_info['biography']) ? $instagram_account_info['biography'] : "";
                        }
                        // end of instagram section

                        $where=array();
                        $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $exist_or_not = $this->basic->get_data('facebook_rx_fb_page_info',$where);

                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('facebook_rx_fb_page_info',$data);
                        }
                        else
                        {
                            $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $this->basic->update_data('facebook_rx_fb_page_info',$where,$data);
                        }

                    }
                }


                $group_list = $this->fb_rx_login->get_group_list($access_token);

                if(!empty($group_list))
                {
                    foreach($group_list as $group)
                    {
                        $user_id = $this->user_id;
                        $group_access_token = $access_token; // group uses user access token
                        $group_id = $group['id'];
                        $group_cover = '';
                        if(isset($group['cover']['source'])) $group_cover = $group['cover']['source'];
                        $group_profile = '';
                        if(isset($group['picture']['url'])) $group_profile = $group['picture']['url'];
                        $group_name = '';
                        if(isset($group['name'])) $group_name = $group['name'];

                        $data = array(
                            'user_id' => $user_id,
                            'facebook_rx_fb_user_info_id' => $facebook_table_id,
                            'group_id' => $group_id,
                            'group_cover' => $group_cover,
                            'group_profile' => $group_profile,
                            'group_name' => $group_name,
                            'group_access_token' => $group_access_token,
                            'add_date' => date('Y-m-d')
                            );

                        $where=array();
                        $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                        $exist_or_not = $this->basic->get_data('facebook_rx_fb_group_info',$where);

                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('facebook_rx_fb_group_info',$data);
                        }
                        else
                        {
                            $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$page['id']);
                            $this->basic->update_data('facebook_rx_fb_group_info',$where,$data);
                        }
                    }
                }
                $this->session->set_flashdata('success_message', 1);
                redirect('social_accounts/index','location');
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = $this->lang->line("Something went wrong, please try again.");
                $data['body'] = "facebook_rx/admin_login";
                $this->_viewcontroller($data);
            }


        }

    }
    //================MESSENGER BOT FUNCTIONS======================
    // ============================================================


    

    protected function ajax_check()
    {

        if(!$this->input->is_ajax_request() && $this->strict_ajax_call) exit();
    }

    // CSRF token check from during Form Submit 
    
    protected function csrf_token_check()
    {
        $csrf_token_form=$this->input->post('csrf_token',TRUE);
        $csrf_token_session= $this->session->userdata('csrf_token_session');
        $ajax_resposne = json_encode(array("status"=>"0","message"=>$this->lang->line("CSRF Token Mismatch!"),"error"=>$this->lang->line("CSRF Token Mismatch!")));
        $is_error = false;

        if(is_null($csrf_token_form) || is_null($csrf_token_session)) $is_error = true;
        else if(!hash_equals($csrf_token_form,$csrf_token_session)) $is_error = true;

        if($is_error)
        {
            if($this->input->is_ajax_request()) echo $ajax_resposne;
            else redirect('home/error_csrf','location');
            exit();
        }
        return true;
    }


    public function error_csrf()
    {
        $this->load->view('page/csrf');
    }


    protected function set_facebook_config_session($user_id) {

        $user_id = $user_id;

        // for getting usable facebook api (facebook live app)
        $facebook_rx_config_id=0;
        $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
        if($this->config->item("backup_mode")==0)  // users will use admins app
        {
            if(isset($fb_info[0]['facebook_rx_config_id']))
            $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
            else
            {
                $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone','developer_access'=>'0')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
            }
            $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

            if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
            else $facebook_rx_fb_user_info = 0;
            $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
        }
        else  // users will use own app
        {
            $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$user_id,'developer_access'=>'0')),$select='');

            if(isset($fb_info_admin[0]['id']))
            {
                $facebook_rx_config_id = $fb_info_admin[0]['id'];
                $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
            }

            if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
            else $facebook_rx_fb_user_info = 0;
            $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

        }
        // for getting usable facebook api
    }

    protected function set_google_config_session($user_id) {

        $user_id = $user_id;

        // GMB add-on data
        if($this->addon_exist("gmb"))
        {
            $gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
            if(!empty($gmb_user_info))
                $this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
        }

    }

    protected function botinboxer_exist()
    {
        if($this->session->userdata('user_type') == 'Admin') return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(199,$this->module_access)) return true;
        return false;
    }

    protected function broadcaster_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>30))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(211,$this->module_access)) return true;
        return false;
    }

    protected function drip_campaigner_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>30))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(219,$this->module_access)) return true;
        return false;
    }

    protected function sms_email_drip_campaigner_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>40))) return true;
        if($this->session->userdata('user_type') == 'Member' && (in_array(270,$this->module_access) || in_array(271,$this->module_access) )) return true;
        return false;
    }

    protected function messenger_bot_import_export_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>31))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(257,$this->module_access)) return true;
        return false;
    }

    protected function messenger_bot_analytics_exist()
    {
        if($this->session->userdata('user_type') == 'Admin') return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(260,$this->module_access)) return true;
        return false;
    }

    protected function engagement_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>30))) return true;
        if($this->session->userdata('user_type') == 'Member' && count(array_intersect($this->module_access, array(213,214,215,217))) > 0 ) return true;
        return false;
    }

    protected function ultrapost_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->db->table_exists('facebook_rx_auto_post')) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(223,$this->module_access)) return true;
        return false;
    }

    protected function webview_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>31))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(261,$this->module_access)) return true;
        return false;
    } 

    protected function ecommerce_exist()
    {
        if($this->session->userdata('user_type') == 'Admin') return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(268,$this->module_access)) return true;
        return false;
    } 

    protected function auto_social_sharing_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>39))) return true;

        if($this->session->userdata('user_type') == 'Member' && in_array(269,$this->module_access)) return true;

        return false;
    }

    protected function group_posting_exist()
    {
        if($this->basic->is_exist("add_ons",array("project_id"=>32))) return true;
        return false;
    }

    protected function addon_exist($unique_name="")
    {
        if($this->basic->is_exist("add_ons",array("unique_name"=>$unique_name))) return true;
        return false;
    }



    public function thirdparty_webhook_trigger($page_id="",$subscriber_id="",$trigger='trigger_email',$postback_id="",$form_canonical_id="",$form_data=array())
    {

        if($trigger=='trigger_postback')
            $trigger="trigger_postback_".$postback_id;

        else if($trigger=='trigger_webview')
            $trigger="trigger_webview_".$form_canonical_id;
        else if ($trigger=='trigger_userinput')
            $trigger="trigger_userinput_".$form_canonical_id;

        if(isset($this->user_id) && $this->user_id!="")
         $where_simple['messenger_bot_thirdparty_webhook.user_id'] = $this->user_id;

        $where_simple['messenger_bot_thirdparty_webhook.page_id'] = $page_id;
        $where_simple['messenger_bot_thirdparty_webhook_trigger.trigger_option'] = $trigger;
        $where=array('where'=>$where_simple);
       
        /**Get all connector webhook information**/

        $join = array('messenger_bot_thirdparty_webhook_trigger'=>"
            messenger_bot_thirdparty_webhook.id=messenger_bot_thirdparty_webhook_trigger.webhook_id,left");

        $webhook_connector_info=$this->basic->get_data('messenger_bot_thirdparty_webhook', $where, $select='', $join, $limit='', $start='');

        if(empty($webhook_connector_info)) return false;

        /** Get subscriber information  **/


        $where_simple=array();
        $where_simple['messenger_bot_subscriber.subscribe_id'] =$subscriber_id ;
        $where_simple['messenger_bot_subscriber.page_id'] = "$page_id";
        $where=array('where'=>$where_simple);
        $join=array("messenger_bot_subscribers_label"=>"messenger_bot_subscribers_label.subscriber_table_id=messenger_bot_subscriber.id,left");
        $select=["GROUP_CONCAT(DISTINCT messenger_bot_subscribers_label.contact_group_id separator ',') as contact_group_ids","messenger_bot_subscriber.*"];

        $subscriber_info=$this->basic->get_data('messenger_bot_subscriber', $where, $select, $join, $limit='', $start='','','messenger_bot_subscriber.id');

        /**Get subscriber Labels name from labels id***/

        $label_ids = $subscriber_info_rearrange['contact_group_id']=isset($subscriber_info[0]['contact_group_ids']) ? $subscriber_info[0]['contact_group_ids']:"";

        $label_ids_array = explode(',',$label_ids);
        $label_ids_array = array_map('trim', $label_ids_array);
        $label_ids_array = array_filter($label_ids_array);

        $labels_name="";

        if(!empty($label_ids_array)){

            $where=array("where_in"=>array("id"=>$label_ids_array));

            $label_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",$where);

            foreach($label_info as $value)
            {
                $labels_name.=",".$value['group_name'];
            }
        }

        $labels_name =trim($labels_name,",");

        foreach ($webhook_connector_info as $webhook_value) {
        
            $webhook_url = isset($webhook_value['webhook_url']) ? $webhook_value['webhook_url']:"";
            $webhook_id=isset($webhook_value['webhook_id']) ? $webhook_value['webhook_id']:"";
            $post_variable = isset($webhook_value['variable_post']) ? $webhook_value['variable_post']:"";
            $post_variable= explode(',',$post_variable);
            $post_variable=array_filter($post_variable);

            /**Making the variable for post/send ***/

            $post_info=array();

            foreach ($post_variable as $variable_info) {

                if($variable_info=='psid')
                    $post_info[$variable_info]= isset($subscriber_info[0]['subscribe_id']) ? $subscriber_info[0]['subscribe_id']:"";
                else if ($variable_info=='labels')
                    $post_info[$variable_info]= $labels_name;
                else if($variable_info=='page_name')
                    $post_info[$variable_info]= isset($webhook_connector_info[0]['page_name']) ? $webhook_connector_info[0]['page_name']:"";
                else if($variable_info=='postbackid')
                     $post_info[$variable_info]= $postback_id;

                 else if($variable_info =='formdata'){
                        foreach ($form_data as $key => $value) {
                            $post_info[$key]=$value;
                        }
                 }

                  else if($variable_info=='user_input_flow_campaign'){
                        
                        $post_info["user_input_data"]=$form_data;
                 }

                else
                    $post_info[$variable_info] = isset($subscriber_info[0][$variable_info]) ? $subscriber_info[0][$variable_info]:"";

            }


            /***    Send/Post Information to webhook url ***/

            $post_info=json_encode($post_info);

            $curl_response=$this->curl_send_data($webhook_url,$post_info);
            
            $curl_http_code= $curl_response['http_code'];
            $curl_error= $curl_response['curl_error'];

            /***Insert into Activity table**/

            $insert_data=array();
            $insert_data['http_code'] = $curl_http_code; 
            $insert_data['curl_error'] = $curl_error; 
            $insert_data['webhook_id'] = $webhook_id; 
            $insert_data['post_time'] = date('Y-m-d H:i:s'); 
            $insert_data['post_data'] = $post_info; 

            $this->basic->insert_data('messenger_bot_thirdparty_webhook_activity',$insert_data);

            /**update messenger_bot_thirdparty_webhook table for last_trigger_time **/
            $update_data_last_trigger['last_trigger_time'] = $insert_data['post_time'];
            $this->basic->update_data("messenger_bot_thirdparty_webhook",array('id'=>$webhook_id),$update_data_last_trigger);
            
        }
    }


     protected function curl_send_data($webhook_url,$post_info){

        $ch = curl_init();
        $headers = array('Accept: application/json', 'Content-Type: application/json');

        curl_setopt($ch, CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_info); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
       // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
        $st=curl_exec($ch); 

        $curl_information =  curl_getinfo($ch);
        $curl_error="";
        if($curl_information['http_code']!='200'){
            $curl_error = curl_error($ch);
        }

        $response['http_code']=$curl_information['http_code'];
        $response['curl_error']=$curl_error;

        return $response; 

    }



    public function assign_drip_messaging_id($drip_type="default",$check_box_plugin_id="0",$PAGE_AUTO_ID,$subscriber_id,$drip_campaign_id="")
    {
        $date_time=date("Y-m-d H:i:s");

        $engagement_table_id= $check_box_plugin_id;

        if($drip_campaign_id!=""){ // Means Campaign id is passed directly, no need to get it from engagement table. 

            $where['where']['id'] = $drip_campaign_id;

            if($PAGE_AUTO_ID!='0' && $PAGE_AUTO_ID!="")
                $where['where']['page_id'] = $PAGE_AUTO_ID;
        } 
            
        else{
            // if $drip_campaign_id isn't set , that means, we need to check if the messenger enhancers add-on is avaialble or not. 

           if(!$this->addon_exist("messenger_bot_enhancers")) return true; 

            $where=array("where"=>array("engagement_table_id"=>$engagement_table_id,"drip_type"=>$drip_type,"page_id"=>$PAGE_AUTO_ID)); 
        }


        $drip_messaging_campaign_info= $this->basic->get_data("messenger_bot_drip_campaign",$where);
        
        $drip_campaign_id= isset($drip_messaging_campaign_info[0]['id']) ? $drip_messaging_campaign_info[0]['id']: "";
        $user_id= isset($drip_messaging_campaign_info[0]['user_id']) ? $drip_messaging_campaign_info[0]['user_id']: "";

        if($drip_campaign_id!=""){

            $sql="INSERT IGNORE INTO messenger_bot_drip_campaign_assign(user_id,page_table_id,subscribe_id,messenger_bot_drip_campaign_id,drip_type,messenger_bot_drip_initial_date) 
                VALUES('$user_id','$PAGE_AUTO_ID','$subscriber_id','$drip_campaign_id','$drip_type','$date_time');";

             $this->basic->execute_complex_query($sql);

        }
    }


    // page and account delete section
    
    public function table_names_array()
    {
      $tables = array (
                    0 => 
                    array (
                      'table_name' => 'auto_comment_reply_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    1 => 
                    array (
                      'table_name' => 'ultrapost_auto_reply',
                      'column_name' => 'page_ids',
                      'module_id' => ''
                    ),
                    2 => 
                    array (
                      'table_name' => 'autoposting',
                      'column_name' => 'page_ids',
                      'module_id' => '',
                      'comma_separated' => 'yes'
                    ),
                    3 => 
                    array (
                      'table_name' => 'facebook_ex_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'facebook_ex_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    4 => 
                    array (
                      'table_name' => 'facebook_ex_conversation_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    5 => 
                    array (
                      'table_name' => 'facebook_ex_conversation_campaign_send',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    6 => 
                    array (
                      'table_name' => 'facebook_page_insight_page_list',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    7 => 
                    array (
                      'table_name' => 'facebook_rx_auto_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    8 => 
                    array (
                      'table_name' => 'facebook_rx_cta_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),                          
                    10 => 
                    array (
                      'table_name' => 'facebook_rx_fb_page_info',
                      'column_name' => 'id',
                      'persistent_getstarted_check' => 'yes',
                      'module_id' => ''
                    ),                          
                    12 => 
                    array (
                      'table_name' => 'facebook_rx_offer_campaign',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    13 => 
                    array (
                      'table_name' => 'facebook_rx_offer_campaign_view',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    16 => 
                    array (
                      'table_name' => 'facebook_rx_slider_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),                          
                    24 => 
                    array (
                      'table_name' => 'messenger_bot',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    26 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_contact_group',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    27 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_serial',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    28 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_serial_send',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    29 => 
                    array (
                      'table_name' => 'messenger_bot_domain_whitelist',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    30 => 
                    array (
                      'table_name' => 'messenger_bot_drip_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    31 => 
                    array (
                      'table_name' => 'messenger_bot_drip_report',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    32 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_2way_chat_plugin',
                      'column_name' => 'page_auto_id',
                      'module_id' => ''
                    ),
                    33 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_checkbox',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    34 => 
                    array (
                      'table_name' => 'messenger_bot_drip_campaign_assign',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    36 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_mme',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    37 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_send_to_msg',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    38 => 
                    array (
                      'table_name' => 'messenger_bot_persistent_menu',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    39 => 
                    array (
                      'table_name' => 'messenger_bot_postback',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    40 => 
                    array (
                      'table_name' => 'messenger_bot_reply_error_log',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),                          
                    42 => 
                    array (
                      'table_name' => 'messenger_bot_subscriber',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    43 => 
                    array (
                      'table_name' => 'messenger_bot_thirdparty_webhook',
                      'column_name' => 'page_id',
                      'is_facebook_page_id' => 'yes',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'messenger_bot_thirdparty_webhook_activity,messenger_bot_thirdparty_webhook_trigger',
                      'dependent_table_column' => 'webhook_id,webhook_id',
                      'module_id' => ''
                    ),
                    45 => 
                    array (
                      'table_name' => 'messenger_bot_user_custom_form_webview_data',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    48 => 
                    array (
                      'table_name' => 'page_response_auto_like_share',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    49 => 
                    array (
                      'table_name' => 'page_response_auto_like_share_report',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'page_response_auto_like_report,page_response_auto_share_report',
                      'dependent_table_column' =>'page_response_auto_like_share_report_id,page_response_auto_like_share_report_id'
                    ),
                    51 => 
                    array (
                      'table_name' => 'page_response_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    52 => 
                    array (
                      'table_name' => 'page_response_report',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'facebook_ex_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    54 => 
                    array (
                      'table_name' => 'tag_machine_bulk_reply',
                      'column_name' => 'page_info_table_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'tag_machine_bulk_reply_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    56 => 
                    array (
                      'table_name' => 'tag_machine_bulk_tag',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    57 => 
                    array (
                      'table_name' => 'tag_machine_comment_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    58 => 
                    array (
                      'table_name' => 'tag_machine_commenter_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    59 => 
                    array (
                      'table_name' => 'tag_machine_enabled_post_list',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ), 
                    63 => 
                    array (
                      'table_name' => 'webview_builder',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    64 => 
                    array (
                      'table_name' => 'email_sending_campaign',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'email_sending_campaign_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    65 => 
                    array (
                      'table_name' => 'sms_sending_campaign',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'sms_sending_campaign_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    66 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    67 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign_report',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    68 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign_webhook_call',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    69 => 
                    array (
                      'table_name' => 'ecommerce_store',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'ecommerce_attribute,ecommerce_cart,ecommerce_cart_item,ecommerce_category,ecommerce_coupon,ecommerce_product,ecommerce_reminder_report',
                      'dependent_table_column' => 'store_id,store_id,store_id,store_id,store_id,store_id,store_id',
                      'module_id' => ''
                    ),
                    70 => 
                    array (
                      'table_name' => 'otn_postback',
                      'column_name' => 'page_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'otn_optin_subscriber',
                      'dependent_table_column' =>'otn_id'
                    ),
                    71 => 
                    array (
                      'table_name' => 'instagram_reply_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'instagram_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    72 => 
                    array (
                      'table_name' => 'user_input_flow_campaign',
                      'column_name' => 'page_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'user_input_flow_questions,user_input_flow_questions_answer',
                      'dependent_table_column' =>'flow_campaign_id,flow_campaign_id'
                    ),
                    73 => 
                    array (
                      'table_name' => 'visual_flow_builder_campaign',
                      'column_name' => 'page_id',
                      'module_id' => '',
                    ),
                    74 => 
                    array (
                      'table_name' => 'visual_flow_campaign_unique_ids',
                      'column_name' => 'page_table_id',
                      'module_id' => '',
                    ),
                    75 => 
                    array (
                      'table_name' => 'messenger_bot_message_sent_stat',
                      'column_name' => 'page_table_id',
                      'module_id' => '',
                    )

                  );
      return $tables;
    }

    public function table_names_array_foraccount()
    {
        $tables = array(
                        1 => 
                        array (
                          'table_name' => 'facebook_rx_fb_group_info',
                          'column_name' => 'facebook_rx_fb_user_info_id',
                          'module_id' => ''
                        ),
                        2 => 
                        array (
                          'table_name' => 'facebook_rx_fb_user_info',
                          'column_name' => 'id',
                          'module_id' => ''
                        )
                );
        return $tables;
    }


    public function table_names_array_foruser()
    {
        $tables = array(
                1 => 
                array (
                  'table_name' => 'auto_comment_reply_tb',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                2 => 
                array (
                  'table_name' => 'fb_simple_support_desk',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                3 => 
                array (
                  'table_name' => 'fb_support_category',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                4 => 
                array (
                  'table_name' => 'fb_support_desk_reply',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                6 => 
                array (
                  'table_name' => 'messenger_bot_saved_templates',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                7 => 
                array (
                  'table_name' => 'ultrapost_auto_reply',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                8 => 
                array (
                  'table_name' => 'sms_sending_campaign_send',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                9 => 
                array (
                  'table_name' => 'email_mailgun_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                10 => 
                array (
                  'table_name' => 'email_mandrill_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                11 => 
                array (
                  'table_name' => 'email_sendgrid_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                12 => 
                array (
                  'table_name' => 'email_smtp_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                13 => 
                array (
                  'table_name' => 'sms_api_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                14 => 
                array (
                  'table_name' => 'sms_email_contacts',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                15 => 
                array (
                  'table_name' => 'sms_email_contact_group',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                16 => 
                array (
                  'table_name' => 'email_sending_campaign',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                17 => 
                array (
                  'table_name' => 'email_sending_campaign_send',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                18 => 
                array (
                  'table_name' => 'sms_sending_campaign',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                19 => 
                array (
                  'table_name' => 'ecommerce_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                20 => 
                array (
                  'table_name' => 'usage_log',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                21 => 
                array (
                  'table_name' => 'user_input_custom_fields',
                  'column_name' => 'user_id',
                  'module_id' => '',
                  'has_dependent_table' => 'yes',
                  'dependent_tables' => 'user_input_custom_fields_assaign',
                  'dependent_table_column' =>'custom_field_id'
                ),
                22 => 
                array (
                  'table_name' => 'users',
                  'column_name' => 'id',
                  'module_id' => ''
                )
        );
        return $tables;
    }

    public function delete_data_basedon_page($table_id=0,$admin_access=0)
    {
      if($table_id == 0)
      {
        return json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
        exit();
      }

      if($admin_access == '1' && $this->session->userdata('user_type') == 'Admin')
        $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id)));
      else
        $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));

      if(empty($page_information)){
        return json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
        exit();
      }

      $this->db->trans_start();
      $this->load->library("fb_rx_login");
      $table_names = $this->table_names_array();
      foreach($table_names as $value)
      {
        if(isset($value['persistent_getstarted_check']) && $value['persistent_getstarted_check'] == 'yes')
        {
          $fb_page_id=isset($page_information[0]["page_id"]) ? $page_information[0]["page_id"] : "";
          $page_access_token=isset($page_information[0]["page_access_token"]) ? $page_information[0]["page_access_token"] : "";
          $persistent_enabled=isset($page_information[0]["persistent_enabled"]) ? $page_information[0]["persistent_enabled"] : "0";
          $bot_enabled=isset($page_information[0]["bot_enabled"]) ? $page_information[0]["bot_enabled"] : "0";
          $ice_breaker_status=isset($page_information[0]["ice_breaker_status"]) ? $page_information[0]["ice_breaker_status"] : "0";
          $started_button_enabled=isset($page_information[0]["started_button_enabled"]) ? $page_information[0]["started_button_enabled"] : "0";
          $fb_user_id = $page_information[0]["facebook_rx_fb_user_info_id"];
          $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
          $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']); 

          if($persistent_enabled == '1') 
          {
            $this->fb_rx_login->delete_persistent_menu($page_access_token); // delete persistent menu
            if($admin_access != '1')
                $this->_delete_usage_log($module_id=197,$request=1);
          }
          if($started_button_enabled == '1') $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
          if($ice_breaker_status == '1') $this->fb_rx_login->delete_ice_breakers($page_access_token); // delete get started button
          if($bot_enabled == '1' || $bot_enabled == '2')
          {
            if($bot_enabled == '1')
                $this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
            if($admin_access != '1')
                $this->_delete_usage_log($module_id=200,$request=1);
          }

          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
        }
        else if(isset($value['has_dependent_table']) && $value['has_dependent_table'] == 'yes')
        {
          $table_ids_array = array();   
          if($this->db->table_exists($value['table_name']))
          {
            if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
            {
              $facebook_page_id = $page_information[0]['page_id']; 
              $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$facebook_page_id)),'id');
            }
            else
              $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$table_id)),'id');

          }    
          else continue;

          foreach($table_ids_info as $info)
            array_push($table_ids_array, $info['id']);

          if($this->db->table_exists($value['table_name']))
          {
            if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
              $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$facebook_page_id));
            else
              $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
          }

          $dependent_table_names = explode(',', $value['dependent_tables']);
          $dependent_table_column = explode(',', $value['dependent_table_column']);
          if(!empty($table_ids_array) && !empty($dependent_table_names))
          {            
            for($i=0;$i<count($dependent_table_names);$i++)
            {
              if($this->db->table_exists($dependent_table_names[$i]))
              {
                $this->db->where_in($dependent_table_column[$i], $table_ids_array);
                $this->db->delete($dependent_table_names[$i]);
              }
            }
          }
        }
        else if(isset($value['comma_separated']) && $value['comma_separated'] == 'yes')
        {
          $str = "FIND_IN_SET('".$table_id."', ".$value['column_name'].") !=";
          $where = array($str=>0);
          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],$where);
        }
        else
        {
          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
        }
      }
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) 
      {    
          $response['status'] = 0;
          $response['message'] = $this->lang->line('Something went wrong, please try again.');         
      }
      else
      {
          $response['status'] = 1;
          $response['message'] = $this->lang->line("Your page and all of it's corresponding campaigns have been deleted successfully.");      
      }

      return json_encode($response);

    }


    public function delete_data_basedon_account($fb_user_id=0,$app_delete=0)
    {
      $this->db->trans_start();
      $table_names = $this->table_names_array_foraccount();
      foreach($table_names as $value)
      {
        if($this->db->table_exists($value['table_name']))
          $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$fb_user_id));
      }
      $this->db->trans_complete();                

      if ($this->db->trans_status() === FALSE) 
      {   
          $response['status'] = 0;
          $response['message'] = $this->lang->line('Something went wrong, please try again.');           
      }
      else
      {
          if($app_delete!='1')
          {
            // delete data to useges log table
            $this->_delete_usage_log($module_id=65,$request=1);
            $this->session->sess_destroy();            
          }
          $response['status'] = 1;
          $response['message'] = $this->lang->line("Your account and all of it's corresponding pages, groups and campaigns have been deleted successfully. Now you'll be redirected to the login page.");       
      }
      return $response;
    }


    public function user_delete_action($user_id=0)
    {
        $this->ajax_check();
        $this->csrf_token_check();

        if($this->is_demo == '1' && $this->session->userdata('user_type')=="Admin")
        {
            
                $response['status'] = 0;
                $response['message'] = "This feature is disabled in this demo.";
                echo json_encode($response);
                exit();
            
        }

        if($user_id == 0) exit;

        if($this->session->userdata('user_type') != 'Admin')
            if($user_id != $this->user_id) exit;

        $fb_user_infos = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('user_id'=>$user_id)),array('id'));

        foreach($fb_user_infos as $value)
        {
          $fb_page_infos = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$value['id'])),array('id'));
          foreach($fb_page_infos as $value2)
            $response = $this->delete_data_basedon_page($value2['id'],'1');

          $response = $this->delete_data_basedon_account($value['id'],'1');
        }

        $this->db->trans_start();
        $table_names = $this->table_names_array_foruser();
        foreach($table_names as $value)
        {
          if($this->db->table_exists($value['table_name']))
          {
            if(isset($value['has_dependent_table']) && $value['has_dependent_table'] == 'yes')
            {
                $table_ids_array = array(); 
                $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$user_id)),'id');
                foreach($table_ids_info as $info)
                  array_push($table_ids_array, $info['id']);

                $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$user_id));

                $dependent_table_names = explode(',', $value['dependent_tables']);
                $dependent_table_column = explode(',', $value['dependent_table_column']);
                if(!empty($table_ids_array) && !empty($dependent_table_names))
                {            
                  for($i=0;$i<count($dependent_table_names);$i++)
                  {
                    if($this->db->table_exists($dependent_table_names[$i]))
                    {
                      $this->db->where_in($dependent_table_column[$i], $table_ids_array);
                      $this->db->delete($dependent_table_names[$i]);
                    }
                  }
                }

            }
            else
                $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$user_id));
          }
        }
        $this->db->trans_complete();                

        if ($this->db->trans_status() === FALSE) 
        {   
            $response['status'] = 0;
            $response['message'] = $this->lang->line('Something went wrong, please try again.');           
        }
        else
        {
            if($this->session->userdata('user_type') != 'Admin')
                $this->session->sess_destroy();
            $response['status'] = 1;
            $response['message'] = $this->lang->line("Account and all of it's corresponding pages, groups and campaigns have been deleted successfully.");       
        }
        echo json_encode($response);
    }

    

    public function _email_send_function($config_id_prefix="", $message_org="", $to_emails="", $subject="", $attachement='', $fileName='',$user_id='',$test_mail='')
    {
        $message_org = preg_replace('/data-cke-saved-src="(.+?)"/', '', $message_org);
        $message_org = preg_replace('/_moz_resizing="(.+?)"/', '', $message_org);

        // echo "<pre>"; print_r($message_org); exit;
        
        $message = '<!DOCTYPE HTML>'.
        '<html>'.
        '<head>'.
        '<meta http-equiv="content-type" content="text/html">'.
        '<title>'.$subject.'</title>'.
        '</head>'.
        '<body>'.
        $message_org.
        '</body>';
        '</html>';


        if ($config_id_prefix=="" || $message=="" || $to_emails=="" || $subject=="") {
            return false;
        }

        if ($fileName=="0") {
            $fileName="";
            $attachement="";
        }

        if (!is_array($to_emails)) {
            $to_emails=array($to_emails);
        }
            
        $status="";
        
        /*****get the email configuration value*****/
        $from_email=$config_id_prefix;
        $from_email_separate=explode("_", $from_email);
        $config_type=$from_email_separate[0];
        $config_id=$from_email_separate[1];
        
        if ($config_type=='smtp') {
            $table_name="email_smtp_config";
        } elseif ($config_type=='mandrill') {
            $table_name="email_mandrill_config";
        } elseif ($config_type=='sendgrid') {
            $table_name="email_sendgrid_config";
        } elseif ($config_type=='mailgun') {
            $table_name="email_mailgun_config";
        } else {
            $table_name="";
        }
        
                    
        $where2=array("where"=>array('id'=>$config_id));
        $email_config_details=$this->basic->get_data($table_name, $where2, $select='', $join='', $limit='', $start='', $group_by='', $num_rows=0);

        $userid = $user_id;

        if (count($email_config_details)==0) {
            $status =  "Opps !!! Sorry no configuration is found";
            return $status;
        }

        if ($config_type=='smtp') 
        {
            foreach ($email_config_details as $send_info) 
            {
                $send_email = trim($send_info['email_address']);
                $smtp_host= trim($send_info['smtp_host']);
                $smtp_port= trim($send_info['smtp_port']);
                $smtp_user=trim($send_info['smtp_user']);
                $smtp_password= trim($send_info['smtp_password']);
                $smtp_type = trim($send_info['smtp_type']);
                $sender_name= trim($send_info['sender_name']);
            }
            
            /*****Email Sending Code ******/
            $config = array(
              'protocol' => 'smtp',
              'smtp_host' => "{$smtp_host}",
              'smtp_port' => $smtp_port,
              'smtp_user' => "{$smtp_user}", // change it to yours
              'smtp_pass' => "{$smtp_password}", // change it to yours
              'mailtype' => 'html',
              'charset' => 'utf-8',
              'newline' =>"\r\n",
              'set_crlf'=> "\r\n",
              'smtp_timeout'=>'30',
              'wrapchars'   => '998'
            );

            if($smtp_type != 'Default')
                $config['smtp_crypto'] = $smtp_type;

           else
                $config['smtp_crypto'] = "";

            $this->load->library('email');
            $this->email->initialize($config);
            
            if($sender_name!='')
                $this->email->from($send_email,$sender_name); 
            else
                $this->email->from($send_email); 
            
            if(is_array($to_emails) && count($to_emails)>1)
            {
                $no_reply_arr=explode("@",$send_email);
                if(isset($no_reply_arr[1]))
                $no_reply="do-not-reply@".$no_reply_arr[1];
                else $no_reply=$to_emails[0];
                $this->email->to($no_reply);
                $this->email->bcc($to_emails);
            }
            else $this->email->to($to_emails);

            $this->email->subject($subject);
            $this->email->message($message);
              
            if ($attachement) 
            {
                $this->email->attach($attachement);
            }

            try 
            {
                if($this->email->send()) {

                    $response_smtp = "success";
                }
                else {
                    $response_smtp = "error";
                }
            } 
            catch (Exception $e) 
            {
                $response_smtp = "error";
            }
              
            if($response_smtp != "error") {

                $status = "Submited";
            } 
            else {

                if($test_mail == 1) {
                    $status = $this->email->print_debugger();
                } else {
                    $status = "error in configuration";
                }
            }
        }
        /***  End of Email sending by SMTP  ***/
        
        
        /***  If option is mandrill   ***/
        
        if ($config_type=='mandrill') 
        {
            foreach ($email_config_details as $send_info) 
            {
                $send_email= $send_info['email_address'];
                $api_id=$send_info['api_key'];
                $send_name=$send_info['your_name'];
            }
            $this->load->library('email_manager');
            $result = $this->email_manager->send_madrill_email($send_email, $send_name, $to_emails, $subject,$message, $api_id, $attachement, $fileName);
            
            if (isset($result['error']) && !empty($result['error'])) 
            {
                $status = $result['error'];
            } 
            else 
            {
                $status = "Submited";
            }
        }
        
        
        
        /***** if gateway is sendgrid *****/
        if ($config_type=='sendgrid') 
        {
            $this->load->library('email_manager');
            foreach ($email_config_details as $send_info) 
            {
                $sendgrid_from_email= $send_info['email_address'];
                $this->email_manager->sendgrid_username=$send_info['username'];
                $this->email_manager->sendgrid_password=$send_info['password'];
            }
            
            $result = $this->email_manager->sendgrid_email_send($sendgrid_from_email, $to_emails, $subject, $message, $attachement, $fileName);

            if ((isset($result['status']) && !empty($result['status'])) && $result['status'] == 'success') {

                if($test_mail == 1) {
                    $status = $result['status'];
                } else {
                    $status = 'Submited';
                }

            } else {

                $status = $result['status'];
            }
        }
        
    
        if ($config_type=='mailgun') 
        {
            $this->load->library('email_manager');
            foreach ($email_config_details as $send_info) 
            {
                $send_email=$send_info['email_address'];
                $this->email_manager->mailgun_api_key=$send_info['api_key'];
                $this->email_manager->mailgun_domain=$send_info['domain_name'];
            }
            
            $result = $this->email_manager->mailgun_email_send($send_email, $to_emails, $subject, $message, $attachement);

            if ($result['status'] != 'error') {

                if($test_mail == 1) {

                    $status = $result['status'];

                } else {

                    $status = "Submited";
                }
            } 
            else 
            {
                $status = $result['status'];
            }
        }
        
        return $status;
    }

    public function unsubscribe($contact_id,$email)
    {
        if($contact_id == '' || $email == '') exit;
        
        $data = array();
        $convertidintobinary = pack("H*", $contact_id);
        $explode_binarycontactid = explode("-", $convertidintobinary);

        $data['contact_id'] = $explode_binarycontactid[0];
        $data['type'] = $explode_binarycontactid[1];
        $data['cam_id'] = $explode_binarycontactid[2];
        $data['cam_temp_table_id'] = $explode_binarycontactid[3];
        $data['campaign_type']= isset($explode_binarycontactid[4]) ? $explode_binarycontactid[4] : "";
        $data['email_address'] = pack("H*", $email);
        
        if(isset($explode_binarycontactid) && $explode_binarycontactid[1] == "contact"){

            $info = $this->basic->get_data("sms_email_contacts", array('where'=>array("id"=>$data['contact_id'], "email"=>$data['email_address'])));

        } else if(isset($explode_binarycontactid) && $explode_binarycontactid[1] == "subscriber") {
            
            if($data['campaign_type']!='Drip')
                $info = $this->basic->get_data("messenger_bot_subscriber", array('where'=>array("id"=>$data['contact_id'], "email"=>$data['email_address'])));
            else
                $info = $this->basic->get_data("messenger_bot_subscriber", array('where'=>array("subscribe_id"=>$data['contact_id'], "email"=>$data['email_address'])));

        }

        if(isset($info) && !empty($info)) {

            if((isset($info[0]['unsubscribed']) && $info[0]['unsubscribed'] =="0") || (isset($info[0]['is_email_unsubscriber']) && $info[0]['is_email_unsubscriber'] =="0")) {
                $data['status'] = "0";
                
            } else {
                $data['status'] = "1";
            }

            $this->load->view("sms_email_manager/email/email_campaign/unsubscribed_message",$data);

        } else {
            redirect('home/access_forbidden', 'location');
        }
    }

    public function unsubscribe_action()
    {
        // if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $result = array();

        $contactid = trim($this->input->post("contactid",true));
        $email_address = trim($this->input->post("email",true));
        $btntype = trim($this->input->post("btntype",true));
        $contactType = trim($this->input->post("contactType",true));
        $cam_id = trim($this->input->post("cam_id",true));
        $cam_temp_table_id = trim($this->input->post("cam_temp_table_id",true));
        $campaign_type = trim($this->input->post("campaign_type",true));


        $cur_date = date("Y-m-d H:i:s");

        if(isset($btntype) && !empty($btntype) && $btntype == "unsub")
        {
            if($contactType == "contact") {
                if($this->basic->update_data("sms_email_contacts", array("id"=>$contactid,"email"=>$email_address,"deleted"=>"0"), array("unsubscribed"=>"1"))) {

                    if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"contact_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                }

                else{

                    if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }
                }


                    echo "1";
                } else {
                    echo "0";
                }
            } else if($contactType == "subscriber") {

                if($campaign_type!='Drip')
                    $update_subscriber_where=array("id"=>$contactid,"email"=>$email_address);
                else
                    $update_subscriber_where=array("subscribe_id"=>$contactid,"email"=>$email_address);


                if($this->basic->update_data("messenger_bot_subscriber",$update_subscriber_where, array("is_email_unsubscriber"=>"1"))) {



                    if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"subscriber_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                    }


                    else{

                    if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }
                    
                    }







                    echo "1";
                } else {
                    echo "0";
                }

            }

        } else if(isset($btntype) && !empty($btntype) && $btntype == "sub")
        {
            if($contactType == "contact") {
                if($this->basic->update_data("sms_email_contacts", array("id"=>$contactid,"email"=>$email_address,"deleted"=>"0"), array("unsubscribed"=>"0"))) {


                if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"contact_id"=>$contactid), array("is_unsubscribed"=>"0","unsubscribed_at"=>"0000-00-00 00:00"))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed-1 where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                }

                else{

                     if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"0","unsubscribed_at"=>"0000-00-00 00:00"))) {

                        $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed-1 where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                }



                    echo "1";

                } else
                {
                    echo "0";
                }
            } else if($contactType == "subscriber") {


                 if($campaign_type!='Drip')
                    $update_subscriber_where=array("id"=>$contactid,"email"=>$email_address);
                else
                    $update_subscriber_where=array("subscribe_id"=>$contactid,"email"=>$email_address);


                if($this->basic->update_data("messenger_bot_subscriber", $update_subscriber_where , array("is_email_unsubscriber"=>"0"))) {


                if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"subscriber_id"=>$contactid), array("is_unsubscribed"=>"0","unsubscribed_at"=>"0000-00-00 00:00"))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed-1 where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }
                }


                else{

                  if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                    $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed-1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                    if($increment_total_unsubscriber != "") {

                        $this->db->query($increment_total_unsubscriber);

                    }

                    }

                }





                    echo "1";

                } else
                {
                    echo "0";
                }

            }
        }   
    }

    // Create labels and push them into dropdown
    public function common_create_label_and_assign()
    {
        $this->ajax_check();
        if(!$this->strict_ajax_call) $this->user_id = 1;
        $this->load->library("fb_rx_login"); 
        $social_media = 'fb';
        $page_table_id = $this->input->post("page_id",true);
        $label_name = $this->input->post("label_name",true);
        $social_media = $this->input->post("social_media",true);
        if($social_media=='') $social_media='fb';

        // $getdata = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)));      
        // $page_access_token = isset($getdata[0]['page_access_token'])?$getdata[0]['page_access_token']:"";

        $is_exists = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_table_id,"group_name"=>$label_name,"social_media"=>$social_media)));

        if(isset($is_exists[0]))
        {
           $insert_id = $is_exists[0]['id'];
           $label_id = $is_exists[0]['label_id'];

        } else {
            
            // $response=$this->fb_rx_login->create_label($page_access_token,$label_name);
            $response = ['id'=>''];

            if(isset($response['error']) && !empty($response['error'])) {
                echo json_encode(["error"=>$response['error']['message']]);
                exit;
            }
            $label_id=isset($response['id']) ? $response['id'] : "";
            $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$page_table_id,"group_name"=>$label_name,"user_id"=>$this->user_id,"label_id"=>$label_id,"social_media"=>$social_media));
            $insert_id = $this->db->insert_id();
        }

        echo json_encode(array('id'=>$insert_id,"text"=>$label_name));
    }

    public function common_get_postback()
    {
        if(!$_POST) exit();
        $is_from_add_button=$this->input->post('is_from_add_button');
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message')),'','','',$start=NULL,$order_by);
        $push_postback="";

        if($is_from_add_button=='0')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }
        
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        if($is_from_add_button=='1' || $is_from_add_button=='')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }

        echo $push_postback;   
    }



    protected function send_email_to_autoresponder($settings='', $email='',$first_name='',$last_name='',$type='singnup',$user_id="0",$tags=''){

    /*   $settings= '{"mailchimp":["37","49"]}';
        $first_name="Konok";
        $last_name='Zaman';
        $email="konok6@xerooneit.net";
        $user_id=1;
        $type='admin';*/


        $data_to_send['firstname']=$first_name;
        $data_to_send['lastname']=$last_name;
        $data_to_send['email']=$email;

        if($settings=="") exit;

        $settings_array=json_decode($settings,TRUE);

        if(empty($settings_array)) exit; 

        if(isset($settings_array['mailchimp']) && !empty($settings_array['mailchimp'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['mailchimp']); 
            $mailchimp_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($mailchimp_config_data as $mailchimp_data) {

               $list_id=isset($mailchimp_data['list_id']) ? $mailchimp_data['list_id']: "";
               $api_key=isset($mailchimp_data['api_key']) ? $mailchimp_data['api_key']: "";  
               $mailchimp_config_id=isset($mailchimp_data['mailchimp_config_id']) ? $mailchimp_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->syncMailchimp($data_to_send, $api_key, $list_id,$tags);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['status']) ? $result_array['status']: "0";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'MailChimp','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$mailchimp_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }


        if(isset($settings_array['sendinblue']) && !empty($settings_array['sendinblue'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');

            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['sendinblue']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"sendinblue"); 

            $sendinblue_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($sendinblue_config_data as $sendinblue_data) {

               $list_id=isset($sendinblue_data['list_id']) ? $sendinblue_data['list_id']: "";
               $api_key=isset($sendinblue_data['api_key']) ? $sendinblue_data['api_key']: "";  
               $sendinblue_config_id=isset($sendinblue_data['mailchimp_config_id']) ? $sendinblue_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->sendinblue_add_contact($api_key,$email,$first_name,$last_name,$list_id);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['code']) ? $result_array['code']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Sendinblue','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$sendinblue_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }



       	if(isset($settings_array['activecampaign']) && !empty($settings_array['activecampaign'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['activecampaign']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"activecampaign"); 
            $activecampaign_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($activecampaign_config_data as $activecampaign_data) {

               $list_id=isset($activecampaign_data['list_id']) ? $activecampaign_data['list_id']: "";
               $api_key=isset($activecampaign_data['api_key']) ? $activecampaign_data['api_key']: ""; 
               $api_url=isset($activecampaign_data['api_url']) ? $activecampaign_data['api_url']: ""; 

               $activecampaign_config_id=isset($activecampaign_data['mailchimp_config_id']) ? $activecampaign_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->activecampaign_add_contact($api_key,$api_url,$email,$first_name,$last_name,$list_id);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['errors']) ? $result_array['errors'][0]['code']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Activecampaign','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$activecampaign_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }


        if(isset($settings_array['mautic']) && !empty($settings_array['mautic'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['mautic']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"mautic"); 
            $mautic_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($mautic_config_data as $mautic_data) {

               $list_id=isset($mautic_data['list_id']) ? $mautic_data['list_id']: "";
               $api_key=isset($mautic_data['api_key']) ? $mautic_data['api_key']: ""; 
               $api_url=isset($mautic_data['api_url']) ? $mautic_data['api_url']: ""; 

               $mautic_config_id=isset($mautic_data['mailchimp_config_id']) ? $mautic_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->mautic_add_contact($api_key,$api_url,$email,$first_name,$last_name,$list_id,$tags);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['errors']) ? $result_array['errors'][0]['message']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Mautic','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$mautic_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }


         if(isset($settings_array['acelle']) && !empty($settings_array['acelle'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['acelle']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"acelle"); 
            $acelle_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($acelle_config_data as $acelle_data) {

               $list_id=isset($acelle_data['list_id']) ? $acelle_data['list_id']: "";
               $api_key=isset($acelle_data['api_key']) ? $acelle_data['api_key']: ""; 
               $api_url=isset($acelle_data['api_url']) ? $acelle_data['api_url']: ""; 

               $acelle_config_id=isset($acelle_data['mailchimp_config_id']) ? $acelle_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->acelle_add_contact($api_key,$api_url,$email,$first_name,$last_name,$list_id);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['errors']) ? $result_array['errors'][0]['message']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Acelle','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$acelle_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);

            }
        }









    }



    public function send_sms_by_for_bot_phone_number($sms_api='',$user_id='',$message='',$phone_number=''){

        $status="";
        $message_sent_id="";


        $this->load->library('Sms_manager');
        $this->sms_manager->set_credentioal($sms_api,$user_id);
        $response = $this->sms_manager->send_sms($message, $phone_number);

        if(isset($response['id']) && !empty($response['id']))
        {   
            $message_sent_id = $response['id'];
            $status='Success';
        }
        else 
        {   if(isset($response['status']) && !empty($response['status'])){
                $message_sent_id = $response["status"];
                 $status='Error';
            }
        }    


        $now_time=date('Y-m-d H:i:s');
        $insert_data=array('user_id'=>$user_id,'settings_type'=>'quick-reply','status'=>$status,'email'=>$phone_number,'auto_responder_type'=>"SMS Sender",'api_name'=>$this->sms_manager->gateway_name,'response'=>$message_sent_id,'insert_time'=>$now_time,'mailchimp_config_id'=>$sms_api);
        $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


         if($status=='Error')

            $response["error"]["message"]=$message_sent_id;

        else
            $response['message_id'] = $message_sent_id;

        return $response;

    }


     public function send_email_by_for_bot_email($email_config_table='',$campaign_message_send='',$contact_email='', $subject='',$user_id=''){

            $status="";
            $message_sent_id="";

             /*****get the email configuration value*****/
            $from_email_separate=explode("_", $email_config_table);
            $email_api_id=array_pop($from_email_separate);
            $config_type=implode('_', $from_email_separate);
            
           if ($config_type == "email_smtp_config") {
                $from_email = "smtp_".$email_api_id;
                $gateway_type="smtp";
           }

            elseif ($config_type == "email_mandrill_config") {
                $from_email = "mandrill_".$email_api_id;
                $gateway_type="mandrill";
            }

            elseif ($config_type == "email_sendgrid_config") {
                $from_email = "sendgrid_".$email_api_id;
                $gateway_type="sendgrid";
            }

            elseif ($config_type == "email_mailgun_config") {
                $from_email = "mailgun_".$email_api_id;
                $gateway_type="mailgun";
            }


            try
            {
                $campaign_message_send = $campaign_message_send;
                $response = $this->_email_send_function($from_email, $campaign_message_send, $contact_email, $subject,'','',$user_id);
                if(isset($response) && !empty($response) && $response == "Submited")
                {   
                    $message_sent_id = $response; 
                    $status='Success';
                }
                else 
                {   
                    $message_sent_id = $response;
                    $status='Error';
                }           
                }
                catch(Exception $e) 
                {
                    $message_sent_id = $e->get_message();
                    $status='Error';
                }

            $now_time=date('Y-m-d H:i:s');
            $insert_data=array('user_id'=>$user_id,'settings_type'=>'quick-reply','status'=>$status,'email'=>$contact_email,'auto_responder_type'=>"Email Sender",'api_name'=>$gateway_type,'response'=>$message_sent_id,'insert_time'=>$now_time,'mailchimp_config_id'=>$email_api_id);
            $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);

            $response=array();

            if($status=='Error')
                $response["error"]["message"]=$message_sent_id;
            else
                 $response['message_id'] = $message_sent_id;

            return $response;




    }







    public function xit_load_files($folder='',$file='')
    {
        if($folder == '' || $file == '')
        {
            echo "";
            exit;
        }
        $file_name_array = explode('.', $file);
        $file_name_extension = array_pop($file_name_array);
        header('Access-Control-Allow-Origin: *');
        if($file_name_extension == 'css')
            header("Content-type: text/css", true);
        if($file_name_extension == 'js')
        header('Content-Type: application/javascript', true);

        $folder = str_replace('-', '/', $folder);
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $path = "application/views/site/".$current_theme."/".$folder."/".$file;
        $content = file_get_contents($path);
        echo $content;
    }

    protected function get_theme_data($path="")
    {
        $path=str_replace('\\','/',$path);
        $tokens=token_get_all(file_get_contents($path));
        $addon_data=array();

        $addon_path=explode('/', $path);
        $controller_name=array_pop($addon_path);
        array_pop($addon_path);
        $addon_path=implode('/',$addon_path);

        $comments = array();
        foreach($tokens as $token) 
        {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) 
            {       
                $comments[] = isset( $token[1]) ?  $token[1] : "";
            } 
        }
        $comment_str=isset($comments[0]) ? $comments[0] : "";
        
        preg_match( '/^.*?theme name:(.*)$/mi', $comment_str, $match); 
        $addon_data['theme_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?unique name:(.*)$/mi', $comment_str, $match); 
        $addon_data['unique_name'] = isset($match[1]) ? trim($match[1]) : "";


        preg_match( '/^.*?theme uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['theme_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author:(.*)$/mi', $comment_str, $match); 
        $addon_data['author'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['author_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?version:(.*)$/mi', $comment_str, $match); 
        $addon_data['version'] = isset($match[1]) ? trim($match[1]) : "1.0";

        preg_match( '/^.*?description:(.*)$/mi', $comment_str, $match); 
        $addon_data['description'] = isset($match[1]) ? trim($match[1]) : "";

        return $addon_data;
    }

    public function load_builder()
    {
        $data['body'] = 'flow_builder/index';
        $data['page_table_id'] = 241;
        $this->_subscription_viewcontroller($data); 
    } 

    public function switch_to_media()
    {
      $this->ajax_check();
      $media_type = $this->session->userdata('selected_global_media_type');
      if($media_type=='') $media_type = 'fb';
      $new_media_type = $media_type=='fb' ? 'ig' : 'fb';
      $this->session->set_userdata('selected_global_media_type',$new_media_type);
      echo "1";
    }

    public function switch_to_page()
    {
      $this->ajax_check();
      $page_id = $this->input->post("page_id");
      $return_social_media_by_force = $this->input->post("return_social_media_by_force");
      $return_social_media_by_force = $return_social_media_by_force=='1' ? true : false;

      $explode_page_id = explode_page_id($page_id);
      $page_id = $explode_page_id['page_id'];
      $social_media = $explode_page_id['social_media'];
      $this->session->set_userdata('selected_global_page_table_id',$page_id);
      if($return_social_media_by_force) $this->session->set_userdata('selected_global_media_type',$social_media);
      echo "1";
    }

}
