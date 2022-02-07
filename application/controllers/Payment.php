<?php require_once("Home.php"); // including home controller

class Payment extends Home
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1) redirect('home/login_page', 'location');
        $this->load->library('paypal_class');
        $this->load->library('stripe_class');
        $this->important_feature();
        $this->periodic_check();
    }

 
    public function accounts()
    {     
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');
        $data['body'] = "admin/payment/accounts";
        $data['page_title'] = $this->lang->line('Payment Accounts');
        $get_data = $this->basic->get_data("payment_config");
        $data['xvalue'] = isset($get_data[0])?$get_data[0]:array();
        if($this->is_demo == '1')
            $data["xvalue"]["stripe_secret_key"]=$data["xvalue"]["stripe_publishable_key"]=$data["xvalue"]["paypal_email"]=$data["xvalue"]["paystack_secret_key"]=$data["xvalue"]["paystack_public_key"]=$data["xvalue"]["razorpay_key_id"]=$data["xvalue"]["razorpay_key_secret"]=$data["xvalue"]["mollie_api_key"]=$data["xvalue"]["mercadopago_public_key"]=$data["xvalue"]["mercadopago_public_key"]=$data["xvalue"]["sslcommerz_store_id"]=$data["xvalue"]["sslcommerz_store_password"]=$data["xvalue"]["senangpay_merchent_id"]=$data["xvalue"]["senangpay_secret_key"]=$data["xvalue"]["instamojo_api_key"]=$data["xvalue"]["instamojo_auth_token"]=$data["xvalue"]["toyyibpay_secret_key"]=$data["xvalue"]["toyyibpay_category_code"]=$data["xvalue"]["xendit_secret_api_key"]="XXXXXXXXXX";
        $paypal_stripe_currency_list = $this->paypal_stripe_currency_list();
        $marcadopago_country = array('ar'=>'Argentina','br'=>'Brazil','co'=>'Colombia','mx'=>'Mexico','cl'=>'Chile','pe' => 'Peru','uy' => 'Uruguay');
        asort($paypal_stripe_currency_list);
        $data['currency_list'] = $paypal_stripe_currency_list;
        $data['marcadopago_country'] = $marcadopago_country;
        $data['currecny_list_all'] = $this->currecny_list_all();
        $this->_viewcontroller($data);
    }

    public function accounts_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') redirect('home/access_forbidden', 'location');
        if ($_POST) 
        {
            // validation
            $this->form_validation->set_rules('paypal_email','<b>'.$this->lang->line("Paypal Email").'</b>','trim');
            $this->form_validation->set_rules('paypal_payment_type','<b>'.$this->lang->line("Paypal Recurring Payment").'</b>','trim');
            $this->form_validation->set_rules('paypal_mode','<b>'.$this->lang->line("Paypal Sandbox Mode").'</b>','trim');
            $this->form_validation->set_rules('stripe_secret_key','<b>'.$this->lang->line("Stripe Secret Key").'</b>','trim');
            $this->form_validation->set_rules('stripe_publishable_key','<b>'.$this->lang->line("Stripe Publishable Key").'</b>','trim');
            $this->form_validation->set_rules('currency','<b>'.$this->lang->line("Currency").'</b>',  'trim');
            $this->form_validation->set_rules('manual_payment','<b>'.$this->lang->line("Manual Payment").'</b>',  'trim');
            $this->form_validation->set_rules('manual_payment_instruction','<b>'.$this->lang->line("Manual Payment Instruction").'</b>',  'trim');

            $this->form_validation->set_rules('razorpay_key_id','<b>'.$this->lang->line("Razorpay Key ID").'</b>','trim');
            $this->form_validation->set_rules('razorpay_key_secret','<b>'.$this->lang->line("Razorpay Key Secret").'</b>','trim');
            $this->form_validation->set_rules('paystack_secret_key','<b>'.$this->lang->line("Paystack Secret Key").'</b>','trim');
            $this->form_validation->set_rules('paystack_public_key','<b>'.$this->lang->line("Paystack Public Key").'</b>','trim');

            $this->form_validation->set_rules('mercadopago_public_key','<b>'.$this->lang->line("Mercadopago Public Key").'</b>','trim');
            $this->form_validation->set_rules('mercadopago_access_token','<b>'.$this->lang->line("Mercadopago Access Token").'</b>','trim');
            $this->form_validation->set_rules('marcado_country','<b>'.$this->lang->line("Mercadopago Supported Country").'</b>','trim');

            $this->form_validation->set_rules('sslcommerz_store_id','<b>'.$this->lang->line("SSLCommerz Store Id").'</b>','trim');
            $this->form_validation->set_rules('sslcommerz_store_password','<b>'.$this->lang->line("SSLCommerz Store Password(API/Secret Key)").'</b>','trim');
            $this->form_validation->set_rules('sslcommers_mode','<b>'.$this->lang->line("SSLCommerz Sandbox Mode").'</b>','trim');

            $this->form_validation->set_rules('mollie_api_key','<b>'.$this->lang->line("mollie_api_key").'</b>','trim');


             $this->form_validation->set_rules('senangpay_merchent_id','<b>'.$this->lang->line("SenangPay Merchant Id").'</b>','trim');
            $this->form_validation->set_rules('senangpay_secret_key','<b>'.$this->lang->line("SenangPay Secret Key").'</b>','trim');
            $this->form_validation->set_rules('senangpay_mode','<b>'.$this->lang->line("Senangpay Sandbox Mode").'</b>','trim');

             $this->form_validation->set_rules('instamojo_api_key','<b>'.$this->lang->line("Instamojo Private Api Key").'</b>','trim');
            $this->form_validation->set_rules('instamojo_auth_token','<b>'.$this->lang->line("Instamojo Private Auth Token").'</b>','trim');
            $this->form_validation->set_rules('instamojo_mode','<b>'.$this->lang->line("Instamojo Sandbox Mode").'</b>','trim');

            $this->form_validation->set_rules('toyyibpay_secret_key','<b>'.$this->lang->line("Toyyibpay Secret Key").'</b>','trim');
            $this->form_validation->set_rules('toyyibpay_category_code','<b>'.$this->lang->line("Toyyibpay Category Code").'</b>','trim');
            $this->form_validation->set_rules('toyyibpay_mode','<b>'.$this->lang->line("Toyyibpay Sandbox Mode").'</b>','trim');

            $this->form_validation->set_rules('paymaya_public_key','<b>'.$this->lang->line("Paymaya Public Key").'</b>','trim');
            $this->form_validation->set_rules('paymaya_secret_key','<b>'.$this->lang->line("Paymaya Secret Key").'</b>','trim');
            $this->form_validation->set_rules('paymaya_mode','<b>'.$this->lang->line("Paymaya Sandbox Mode").'</b>','trim');

             $this->form_validation->set_rules('myfatoorah_api_key','<b>'.$this->lang->line("Myfatoorah Api Key").'</b>','trim');
            $this->form_validation->set_rules('myfatoorah_mode','<b>'.$this->lang->line("Myfatoorah Sandbox Mode").'</b>','trim');


             $this->form_validation->set_rules('xendit_secret_api_key','<b>'.$this->lang->line("Xendit Secret Api Key").'</b>','trim');
            


            

            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->accounts();
            } 
            else 
            {
                // assign
                $this->csrf_token_check();
                $paypal_email=strip_tags($this->input->post('paypal_email',true));
                $paypal_payment_type=strip_tags($this->input->post('paypal_payment_type',true));
                $paypal_mode=$this->input->post('paypal_mode',true);
                $stripe_secret_key=strip_tags($this->input->post('stripe_secret_key',true));
                $stripe_publishable_key=strip_tags($this->input->post('stripe_publishable_key',true));
                $currency=strip_tags($this->input->post('currency',true));
                $manual_payment=strip_tags($this->input->post('manual_payment',true));
                $manual_payment_instruction = $this->input->post('manual_payment_instruction', true);

                $razorpay_key_id=strip_tags($this->input->post('razorpay_key_id',true));
                $razorpay_key_secret=strip_tags($this->input->post('razorpay_key_secret',true));
                $paystack_secret_key=strip_tags($this->input->post('paystack_secret_key',true));
                $paystack_public_key=strip_tags($this->input->post('paystack_public_key',true));

                $mercadopago_public_key=strip_tags($this->input->post('mercadopago_public_key',true));
                $mercadopago_access_token=strip_tags($this->input->post('mercadopago_access_token',true));
                $marcado_country=strip_tags($this->input->post('marcado_country',true));

                $sslcommerz_store_id=strip_tags($this->input->post('sslcommerz_store_id',true));
                $sslcommerz_store_password=strip_tags($this->input->post('sslcommerz_store_password',true));
                $sslcommers_mode=$this->input->post('sslcommers_mode',true);

                $senangpay_merchent_id=strip_tags($this->input->post('senangpay_merchent_id',true));
                $senangpay_secret_key=strip_tags($this->input->post('senangpay_secret_key',true));
                $senangpay_mode=$this->input->post('senangpay_mode',true);

                $instamojo_api_key=strip_tags($this->input->post('instamojo_api_key',true));
                $instamojo_auth_token=strip_tags($this->input->post('instamojo_auth_token',true));
                $instamojo_mode=$this->input->post('instamojo_mode',true);

                $toyyibpay_secret_key=strip_tags($this->input->post('toyyibpay_secret_key',true));
                $toyyibpay_category_code=strip_tags($this->input->post('toyyibpay_category_code',true));
                $toyyibpay_mode=$this->input->post('toyyibpay_mode',true); 

                $paymaya_public_key=strip_tags($this->input->post('paymaya_public_key',true));
                $paymaya_secret_key=strip_tags($this->input->post('paymaya_secret_key',true));
                $paymaya_mode=$this->input->post('paymaya_mode',true); 


                $myfatoorah_api_key=strip_tags($this->input->post('myfatoorah_api_key',true));
                $myfatoorah_mode=strip_tags($this->input->post('myfatoorah_mode',true));
               
                $xendit_secret_api_key=$this->input->post('xendit_secret_api_key',true);

                $mollie_api_key=strip_tags($this->input->post('mollie_api_key',true));

                if($paypal_payment_type=="") $paypal_payment_type="manual";
                if($paypal_mode=="") $paypal_mode="live";

                if($sslcommers_mode=="") $sslcommers_mode="live";
                if($senangpay_mode=="") $senangpay_mode="live";
                if($instamojo_mode=="") $instamojo_mode="live";
                if($toyyibpay_mode=="") $toyyibpay_mode="live";
                if($paymaya_mode=="")   $paymaya_mode="live";
                $update_data = 
                array
                (
                    'paypal_email'=>$paypal_email,
                    'paypal_payment_type'=>$paypal_payment_type,
                    'paypal_mode'=>$paypal_mode,
                    'stripe_secret_key'=>$stripe_secret_key,
                    'stripe_publishable_key'=>$stripe_publishable_key,
                    'razorpay_key_id'=>$razorpay_key_id,
                    'razorpay_key_secret'=>$razorpay_key_secret,
                    'paystack_secret_key'=>$paystack_secret_key,
                    'paystack_public_key'=>$paystack_public_key,
                    'mercadopago_public_key'=>$mercadopago_public_key,
                    'mercadopago_access_token'=>$mercadopago_access_token,
                    'marcadopago_country'=>$marcado_country,
                    'sslcommerz_store_id'=>$sslcommerz_store_id,
                    'sslcommerz_store_password'=>$sslcommerz_store_password,
                    'sslcommers_mode'=>$sslcommers_mode,
                    'senangpay_merchent_id'=>$senangpay_merchent_id,
                    'senangpay_secret_key'=>$senangpay_secret_key,
                    'senangpay_mode'=>$senangpay_mode,
                    'instamojo_api_key'=>$instamojo_api_key,
                    'instamojo_auth_token'=>$instamojo_auth_token,
                    'instamojo_mode'=>$instamojo_mode,
                    'toyyibpay_secret_key'=>$toyyibpay_secret_key,
                    'toyyibpay_category_code'=>$toyyibpay_category_code,
                    'toyyibpay_mode'=>$toyyibpay_mode,

                    'paymaya_public_key'=>$paymaya_public_key,
                    'paymaya_secret_key'=>$paymaya_secret_key,
                    'paymaya_mode'=>$paymaya_mode,
                    'myfatoorah_api_key'=>$myfatoorah_api_key,
                    'myfatoorah_mode'=>$myfatoorah_mode,
                    'xendit_secret_api_key'=>$xendit_secret_api_key,
                    'mollie_api_key'=>$mollie_api_key,
                    'currency'=>$currency,
                    'manual_payment'=> ('' == $manual_payment) ? 'no' : 'yes',
                    'manual_payment_instruction'=>$manual_payment_instruction,
                    'deleted' => '0'
                );

                $get_data = $this->basic->get_data("payment_config");
                if(isset($get_data[0]))
                $this->basic->update_data("payment_config",array("id >"=>0),$update_data);
                else $this->basic->insert_data("payment_config",$update_data);      
                                         
                $this->session->set_flashdata('success_message', 1);
                redirect('payment/accounts', 'location');
            }
        }
    }

    public function earning_summary()
    {
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');

        $user_data = $this->basic->get_data('users',$where='',$select=array('count(id) as total_user'));

        $year = date("Y");
        $lastyear = $year-1;
        $month = date("m");
        $date = date("Y-m-d");

        $payment_result = $this->db->query("SELECT * FROM transaction_history WHERE  DATE_FORMAT(payment_date,'%Y')='{$year}' OR DATE_FORMAT(payment_date,'%Y')='{$lastyear}' ORDER BY payment_date DESC");
        $payment_data = $payment_result->result_array();

        $payment_today=$payment_month=$payment_year=$payment_life=0;
        $array_month = array();
        $array_year = array();
        $this_year_earning=array();
        $last_year_earning=array();
        $this_year_top= array();
        $last_year_top= array();

        $month_names = array();
        for($m=1; $m<=$month; ++$m)
        {
            $name=date('M', mktime(0, 0, 0, $m, 1));
            $month_names[]=$this->lang->line($name);
            $this_year_earning[]=0;
            $last_year_earning[]=0;
        }

        foreach ($payment_data as $key => $value) 
        {
           $mon = date("F",strtotime($value['payment_date']));
           $mon2 = date("m",strtotime($value['payment_date']));

           if(strtotime($value['payment_date']) == strtotime($date)) $payment_today += $value["paid_amount"];

           if(date("m",strtotime($value['payment_date'])) == $month && date("Y",strtotime($value['payment_date'])) == $year) 
           {
                $payment_month += $value["paid_amount"];
                $payment_date = date("jS M y",strtotime($value['payment_date']));

                if(!isset($array_month[$payment_date])) $array_month[$payment_date] = 0;
                $array_month[$payment_date] += $value["paid_amount"];
           }

           if(date("Y",strtotime($value['payment_date'])) == $year) 
           {
                $payment_year += $value["paid_amount"];
                $payment_life += $value["paid_amount"];
                if(!isset($array_year[$mon])) $array_year[$mon] = 0;
                $array_year[$mon] += $value["paid_amount"];

                if(isset($this_year_earning[$mon2-1])) $this_year_earning[$mon2-1] += $value["paid_amount"];

                if(!isset($this_year_top[$value['country']])) $this_year_top[$value['country']] = 0;
                $this_year_top[$value['country']] += $value["paid_amount"];
           }

           if(date("Y",strtotime($value['payment_date'])) == $lastyear) 
           {
                 if(isset($last_year_earning[$mon2-1])) $last_year_earning[$mon2-1] += $value["paid_amount"];

                if(!isset($last_year_top[$value['country']])) $last_year_top[$value['country']] = 0;
                $last_year_top[$value['country']] += $value["paid_amount"];
           }
        }
        arsort($this_year_top);
        arsort($last_year_top);

        $data['payment_today'] = $payment_today;
        $data['payment_month'] = $payment_month;
        $data['payment_year'] = $payment_year;
        $data['payment_life'] = $payment_life;
        $data['array_month'] = $array_month;
        $data['array_year'] = $array_year;
        $data['month_names'] = $month_names;
        $data['this_year_earning'] = $this_year_earning;
        $data['last_year_earning'] = $last_year_earning;
        $data['year'] = $year;
        $data['lastyear'] = $lastyear;
        $data['this_year_top'] = $this_year_top;
        $data['last_year_top'] = $last_year_top;
        $data['country_names'] = $this->get_country_names();

        $data['user_data'] = $user_data[0]['total_user'];

        $data['body'] = 'admin/payment/earning_summary';
        $data['page_title'] = $this->lang->line("Earning Summary");

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";
        $data["currency"]= $currency;
        $this->_viewcontroller($data);
    }

    public function transaction_log() // works for both admin and member
    {

        if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');

        $action = isset($_GET['action']) ? $_GET['action'] : ""; // if redirect after purchase
        if($action!="")
        {
            if($action=="cancel") $this->session->set_userdata('payment_cancel',1);
            else if($action=="success") $this->session->set_userdata('payment_success',1);
            redirect('payment/transaction_log','refresh');
        }

        $data['body']='admin/payment/transaction_log';
        $data['page_title']=$this->lang->line("Transaction Log");
        
        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";
        $this->_viewcontroller($data);  
    }

    public function transaction_log_data()
    { 
        $this->ajax_check();
        if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');

        $payment_date_range = $this->input->post("payment_date_range");
        $search_value = $_POST['search']['value'];
        $display_columns = array("#","CHECKBOX",'id','receiver_email','first_name', 'last_name', 'payment_type', 'cycle_start_date','cycle_expired_date', 'payment_date','paid_amount');
        $search_columns = array('receiver_email','first_name', 'last_name','paid_amount', 'payment_type','transaction_id');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';

        if($this->session->userdata('user_type')=='Admin')
        $where_custom="user_id > 0 ";
        else $where_custom="user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
        if($payment_date_range!="")
        {
            $exp = explode('|', $payment_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            $where_custom .= " AND payment_date >= '{$from_date}' AND payment_date <='{$to_date}'";
        }
          
        $table="transaction_history";
        $this->db->where($where_custom);
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {
            $info[$i]["cycle_start_date"] = date("jS M y",strtotime($info[$i]["cycle_start_date"]));
            $info[$i]["cycle_expired_date"] = date("jS M y",strtotime($info[$i]["cycle_expired_date"]));
            $info[$i]["payment_date"] = date("jS M y H:i:s",strtotime($info[$i]["payment_date"]));

            if($this->session->userdata('user_type')=="Admin") {
                if($info[$i]['payment_type'] == 'PAYPAL' || $info[$i]['payment_type'] == "PAYPAL-Instant")
                    $info[$i]["receiver_email"] = "<a href='".base_url("admin/edit_user/".$info[$i]["user_id"])."'>".$info[$i]["paypal_email"]."</a>";
                else
                    $info[$i]["receiver_email"] = "<a href='".base_url("admin/edit_user/".$info[$i]["user_id"])."'>".$info[$i]["receiver_email"]."</a>";
            } else {

                if($info[$i]['payment_type'] == 'PAYPAL' || $info[$i]['payment_type'] == "PAYPAL-Instant")
                    $info[$i]["receiver_email"] = $info[$i]["paypal_email"];
                else
                    $info[$i]["receiver_email"] = $info[$i]["receiver_email"];

            }

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }   

    public function member_payment_history() // kept because not sure if it is called from somewhere
    {
        if ($this->session->userdata('license_type') == 'double' && $this->session->userdata('user_type') == 'Member') 
        redirect('payment/transaction_log', 'location');
        else redirect('home/access_forbidden', 'location');
    }

    public function buy_package()
    {
        if($this->session->userdata('license_type') == 'double' && $this->session->userdata('user_type') == 'Member')
        {
           $data['body'] = "member/buy_package";
           $data['page_title'] = $this->lang->line('Buy Package');

           $config_data=$this->basic->get_data("payment_config");
           $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
           $currency_icons = $this->currency_icon();
           $data["currency"]=$currency;
           $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";
           // $data['currency_list'] = $this->basic->get_enum_values_assoc("payment_config","currency");
           $data['currency_list'] = $this->currecny_list_all();

           $data['payment_type'] = isset($config_data[0]['paypal_payment_type'])?$config_data[0]['paypal_payment_type']:"manual";
           $data['manual_payment'] = isset($config_data[0]['manual_payment'])?$config_data[0]['manual_payment']:"no";
           $data['manual_payment_instruction'] = isset($config_data[0]['manual_payment_instruction'])?$config_data[0]['manual_payment_instruction']:"";
           $payment_method = $this->basic->get_data('transaction_history', array('where' => array('user_id' => $this->user_id), array('payment_type'), '', '', '', 'payment_date,dsc'));
           $data['payment_method'] = isset($payment_method[0]['payment_type']) ? $payment_method[0]['payment_type'] : 'Paypal';
           $data["payment_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0,"validity >"=>0,"visible"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='CAST(`price` AS SIGNED)');

           $user_info = $this->basic->get_data('users', array('where' => array('id' => $this->user_id)), array('paypal_subscription_enabled', 'last_payment_method'));
           if(!isset($user_info[0])) exit();
           if($user_info[0]['paypal_subscription_enabled'] == '1' ) $data['has_reccuring'] = 'true';
           else $data['has_reccuring'] = 'false';
           $data['last_payment_method'] = $user_info[0]['last_payment_method'];
           $this->_viewcontroller($data);
        }
        else redirect('home/access_forbidden', 'location');
    }

    public function manual_payment_upload_file() 
    {
        // Kicks out if not a ajax request
        $this->ajax_check();

        if ('get' == strtolower($_SERVER['REQUEST_METHOD'])) {
            exit();
        }

        $upload_dir = APPPATH . '../upload/manual_payment';

        // Makes upload directory
        if( ! file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

       if (isset($_FILES['file'])) {

            $file_size = $_FILES['file']['size'];
            if ($file_size > 5242880) {
                $message = $this->lang->line('The file size exceeds the limit. Allowed size is 5MB. Please remove the file and upload again.');
                echo json_encode(['error' => $message]);
                exit;
            }
            
            // Holds tmp file
            $tmp_file = $_FILES['file']['tmp_name'];

            if (is_uploaded_file($tmp_file)) {

                $post_fileName = $_FILES['file']['name'];
                $post_fileName_array = explode('.', $post_fileName);
                $ext = array_pop($post_fileName_array);

                $allow_ext = ['pdf', 'doc', 'txt', 'png', 'jpg', 'jpeg', 'zip'];
                if(! in_array(strtolower($ext), $allow_ext)) {
                    $message = $this->lang->line('Are you kidding???');
                    echo json_encode(['error' => $message]);
                    exit;
                }

                $filename = implode('.', $post_fileName_array);
                $filename = strtolower(strip_tags(str_replace(' ', '-', $filename)));
                $filename = $filename . '_' . $this->user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) . '.' . $ext;

                // Moves file to the upload dir
                $dest_file = $upload_dir . DIRECTORY_SEPARATOR . $filename;
                if (! @move_uploaded_file($tmp_file, $dest_file)) {
                    $message = $this->lang->line('That was not a valid upload file.');
                    echo json_encode(['error' => $message]);
                    exit;
                }

                // Sets filename to session
                $this->session->set_userdata('manual_payment_uploaded_file', $filename);

                // Returns response
                echo json_encode([ 'filename' => $filename]);
            }
       }        
    }

    public function manual_payment_delete_file() 
    {
        // Kicks out if not a ajax request
        $this->ajax_check();

        if ('get' == strtolower($_SERVER['REQUEST_METHOD'])) {
            exit();
        }

        // Upload dir path
        $upload_dir = APPPATH . '../upload/manual_payment';

        // Grabs filename
        $filename = (string) $this->input->post('filename');
        $session_filename = $this->session->userdata('manual_payment_uploaded_file');
        if ($filename !== $session_filename) {
            exit;
        }

        // Prepares file path
        $filepath = $upload_dir . DIRECTORY_SEPARATOR . $filename;
        
        // Tries to remove file
        if (file_exists($filepath)) {
            // Deletes file from disk
            unlink($filepath);

            // Clears the file from cache 
            clearstatcache();

            // Deletes file from session
            $this->session->unset_userdata('manual_payment_uploaded_file');
            
            echo json_encode(['deleted' => 'yes']);
            exit();
        }

        echo json_encode(['deleted' => 'no']);
    }

    public function manual_payment() 
    {
        // Kicks out if not a ajax request
        $this->ajax_check();

        if ('get' == strtolower($_SERVER['REQUEST_METHOD'])) {
            exit();
        }

        // Sets validation rules
        $this->form_validation->set_rules('paid_amount', $this->lang->line('Paid amount'), 'required|numeric');
        $this->form_validation->set_rules('paid_currency', $this->lang->line('Currency type'), 'required');
        $this->form_validation->set_rules('additional_info', $this->lang->line('Additional info'), 'trim');
        $this->form_validation->set_rules('package_id', $this->lang->line('Package ID'), 'required|numeric');
        $this->form_validation->set_rules('mp_resubmitted_id', $this->lang->line('Package ID'), 'trim');

        // Shows errors if user data is invalid
        if (false === $this->form_validation->run()) {
            if ($this->form_validation->error('paid_amount')) {
                $message = $this->form_validation->error('paid_amount');
            } elseif ($this->form_validation->error('paid_currency')) {
                $message = $this->form_validation->error('paid_currency');
            } elseif ($this->form_validation->error('additional_info')) {
                $message = $this->form_validation->error('additional_info');
            } elseif ($this->form_validation->error('package_id')) {
                $message = $this->form_validation->error('package_id');
            } elseif ($this->form_validation->error('mp_resubmitted_id')) {
                $message = $this->form_validation->error('mp_resubmitted_id');
            } else {
                $message = $this->lang->line('Something went wrong. Please try again later!');
            }

            echo json_encode(['error' => strip_tags($message)]);
            exit;
        }

        // Grabs some vars
        $paid_amount = $this->input->post('paid_amount',true);
        $paid_currency = $this->input->post('paid_currency',true);
        $additional_info = strip_tags($this->input->post('additional_info'));
        $package_id = (int) $this->input->post('package_id',true);
        $filename = $this->session->userdata('manual_payment_uploaded_file');
        $mp_resubmitted_id = (int) $this->input->post('mp_resubmitted_id',true);

        if (! empty($mp_resubmitted_id)) {
            $mp_resubmitted_data = $this->basic->get_data('transaction_history_manual', ['where' => ['id' => $mp_resubmitted_id]], ['id', 'user_id', 'filename'], [], 1);

            if (1 != sizeof($mp_resubmitted_data)) {
                $message = $this->lang->line('Bad request.');
                echo json_encode(['error' => $message]);
                return;
            }

            $mp_resubmitted_data = $mp_resubmitted_data[0];
            if ($mp_resubmitted_data['user_id'] != $this->user_id) {
                $message = $this->lang->line('Bad request.');
                echo json_encode(['error' => $message]);
                return;
            }

            $updated_at = date('Y-m-d H:i:s');
            $update_where = ['id' => $mp_resubmitted_id];
            $update_data = [
                'status' => '0',
                'paid_amount' => $paid_amount,
                'paid_currency' => $paid_currency,
                'additional_info' => $additional_info,
                'updated_at' => $updated_at,
            ];

            // Deletes previous attachement if new one exists
            if (! empty($filename)) {
                // Updates filename in the db
                $update_data['filename'] = $filename;

                // Upload dir path
                $upload_dir = APPPATH . '../upload/manual_payment';

                // Prepares file path
                $filepath = $upload_dir . DIRECTORY_SEPARATOR . $mp_resubmitted_data['filename'];
                
                // Tries to remove previously uploaded file
                if (file_exists($filepath)) {
                    // Deletes file from disk
                    unlink($filepath);
                }
            }

            if ($this->basic->update_data('transaction_history_manual', $update_where, $update_data)
            ) {

                // Deletes file from session
                $this->session->unset_userdata('manual_payment_uploaded_file');

                $message = $this->lang->line('Your manual transaction has been successfully re-submitted and is now being reviewed. We would let you know once it has been approved.');

                echo json_encode(['success' => $message]);
                exit;
            }

            $message = $this->lang->line('Something went wrong while re-submitting your information. Please try again later or contact the administrator!');
            echo json_encode(['error' => $message]);
            exit;                        
        }        

        // Checks whether the attachment is attached
        $filename = $this->session->userdata('manual_payment_uploaded_file');
        if (empty($filename)) {
            $message = $this->lang->line('The attachment must be provided.');
            echo json_encode(['error' => $message]);
            exit;
        }

        $transaction_id = 'man_' . hash_pbkdf2('sha512', $paid_amount, mt_rand(19999999, 99999999), 1000, 24);
        $data = [
            'paid_amount' => $paid_amount, 
            'paid_currency' => $paid_currency, 
            'additional_info' => $additional_info,
            'package_id' => $package_id,
            'user_id' => $this->user_id,
            'transaction_id' => $transaction_id,
            'filename' => $filename,
            'created_at' => date('Y-m-d H:i:s'), 
        ];

        if($this->basic->insert_data('transaction_history_manual', $data)) {
            $message = $this->lang->line('Your manual transaction has been successfully submitted and is now being reviewed. We would let you konw once it has been approved.');

            // Deletes file from session
            $this->session->unset_userdata('manual_payment_uploaded_file');

            echo json_encode(['success' => $message]);
            exit;
        }

        $message = $this->lang->line('Something went wrong while saving your information. Please try again later or contact the administrator!');
        echo json_encode(['error' => $message]);
        exit;
    }

    public function transaction_log_manual() 
    {
        $config_data = $this->basic->get_data("payment_config");
        $currency = isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data['currency_icon'] = isset($currency_icons[$currency]) ? $currency_icons[$currency] : '$';
        $data['currency_list'] = $this->basic->get_enum_values_assoc("payment_config","currency");        
        $data['body'] = 'admin/payment/transaction_log_manual';
        $data['page_title'] = $this->lang->line('Manual Transaction Log');
        $this->_viewcontroller($data);
    }

    public function transaction_log_manual_data() 
    {
        // Kicks out if not a ajax request
        $this->ajax_check();

        if ('get' == strtolower($_SERVER['REQUEST_METHOD'])) {
            exit();
        }

        $payment_date_range = $this->input->post("payment_date_range");
        
        $search_value = $_POST['search']['value'];
        $display_columns = array('id', 'name', 'email', 'paid_amount', 'status','created_at');
        $search_columns = array('name', 'email', 'paid_amount', 'additional_info');

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $curency_icon= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 5;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'transaction_history_manual.created_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by = $sort . " " . $order;

        $where_custom = '';
        if('Admin' == $this->session->userdata('user_type')) {
            $where_custom = "user_id > 0 ";
        } else {
            $where_custom = "user_id = " . $this->user_id;
        }

        if ('' != $search_value) {
            foreach ($search_columns as $key => $value) {
                $temp[] = $value . " LIKE " . "'%$search_value%'";
            }
            $imp = implode(" OR ", $temp);
            $where_custom .= " AND (" . $imp . ") ";
        }

        if('' != $payment_date_range) {
            $exp = explode('|', $payment_date_range);
            $from_date = isset($exp[0]) ? $exp[0] : '';
            $to_date = isset($exp[1]) ? $exp[1] : '';

            if('Invalid date' != $from_date && 'Invalid date' != $to_date) {
                $where_custom .= " AND created_at >= '{$from_date}' AND created_at <='{$to_date}'";
            }
        }
          
        $table="transaction_history_manual";
        $select = [
            'transaction_history_manual.id',
            'transaction_history_manual.package_id',
            'transaction_history_manual.user_id',
            'transaction_history_manual.paid_amount',
            'transaction_history_manual.paid_currency',
            'transaction_history_manual.additional_info',
            'transaction_history_manual.filename',
            'transaction_history_manual.status',
            'transaction_history_manual.created_at',
            'users.id as user_id',
            'users.name',
            'users.email',
            'package.package_name',
            'package.price',
            'package.validity',
        ];
        $join = [
            'users' => 'transaction_history_manual.user_id=users.id,left',
            'package' => 'transaction_history_manual.package_id=package.id,left'
        ];

        $this->db->where($where_custom);
        $info = $this->basic->get_data($table, $where='', $select, $join, $limit, $start, $order_by, $group_by='');
        // echo "<pre>"; print_r($info); exit;
        
        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table, $where='', $count=$table.".id", $join, $group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        $i = 0;
        $base_url = base_url();
        foreach ($info as $key => $value) {
            // Modifies transaction_history_manual.status
            $status = isset($info[$i]['status']) ? $info[$i]['status'] : '2';
            if ('0' == $status) {
                $info[$i]['status'] = '<span class="text-warning"><i class="fa fa-spinner"></i> ' . $this->lang->line('Pending') . '</span>';
            } elseif ('1' == $status) {
                $info[$i]['status'] = '<span class="text-success"><i class="far fa-check-circle"></i> ' . $this->lang->line('Approved') . '</span>';
            } elseif ('2' == $status) {
                $info[$i]['status'] = '<span class="text-danger"><i class="far fa-check-circle"></i> ' . $this->lang->line('Rejected') . '</span>';
            }
            
            // Modifies transaction_history_manual.attachment column
            $file = base_url('upload/manual_payment/' . $info[$i]['filename']);
            $info[$i]['attachment'] = $this->handle_attachment($info[$i]['id'], $file);

            // Modifies users.name column
            if ('Admin' == $this->session->userdata('user_type')) {
                $info[$i]['name'] = '<a href="' . base_url('admin/edit_user/' . $info[$i]['user_id']) . '" target="_blank">' . $info[$i]['name'] . '</a>';
            }

            // Adds actions column for admin
            if (! isset($info[$i]['actions'])) {
                $action_width = (2*47)+20;
                $is_disabled = ('1' == $status || '2' == $status) ? 'disabled' : '';
                
                if ('Admin' == $this->session->userdata('user_type')) {
                    $approve_btn = '<a href="#" id="mp-approve-btn" class="btn btn-circle btn-outline-success ' . $is_disabled . '" data-id="' . $info[$i]['id'] . '"><i class="fas fa-check-circle"></i></a>';
                    $reject_btn = '<a href="#" id="mp-reject-btn" class="btn btn-circle btn-outline-danger ' . $is_disabled . '" data-id="' . $info[$i]['id'] . '"><i class="fas fa-times-circle"></i></a>';

                    $output = '<div class="dropdown d-inline dropright">';
                    $output .= '<button  class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    $output .= '<i class="fa fa-briefcase"></i>';
                    $output .= '</button>';

                    $output .= '<div class="dropdown-menu mini_dropdown text-center" style="width:' . $action_width . 'px !important">';
                    $output .= $approve_btn;
                    $output .= $reject_btn;
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '<script>$("[data-toggle=\'tooltip\']").tooltip();</script>';

                    $info[$i]['actions'] = $output;
                } elseif ('Member' == $this->session->userdata('user_type')) {
                    if ('0' == $status) {
                        $info[$i]['actions'] = '<i class="fas fa-spinner text-warning" data-toggle="tooltip" title="' . $this->lang->line('In progress') . '"></i>';
                    } elseif ('1' == $status) {
                        $info[$i]['actions'] = '<i class="fas fa-check-circle text-success" data-toggle="tooltip" title="' . $this->lang->line('No action required') . '"></i>';
                    } elseif ('2' == $status) {
                        $info[$i]['actions'] = '<a href="#" id="manual-payment-resubmit" data-id="' . $info[$i]['id'] . '" data-toggle="tooltip" title="' . $this->lang->line('You can re-submit this payment.') . '">'. $this->lang->line('Re-submit') .'</a>';
                    }

                    $info[$i]['actions'] .= '<script>$("[data-toggle=\'tooltip\']").tooltip();</script>';
                }
            }

            $info[$i]['package'] = '<a target="_blank" href="'.base_url('payment/edit_package/'.$info[$i]['package_id']).'">'.$info[$i]['package_name'].'</a>';
            $info[$i]['price'] = $curency_icon.$info[$i]['price'];
            $info[$i]['validity'] = $info[$i]['validity'].' '.$this->lang->line('Days');

            // Modifies transaction_history_manual.created_at column
            $info[$i]["created_at"] = date("jS M y H:i:s",strtotime($info[$i]["created_at"]));

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = $info;

        echo json_encode($data);
    }

    public function transaction_log_manual_resubmit() 
    {
        if (! $this->input->is_ajax_request()) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['msg' => $message]);
            return;
        }

        $resubmit_data = isset($resubmit_data[0]) ? $resubmit_data[0] : '';
        $payment_instructions = isset($payment_instructions[0]) ? $payment_instructions[0] : '';

        // Prepares vars
        $user_id = $this->session->userdata('tlm_resubmit_user_id');
        $filename = $this->session->userdata('tlm_resubmit_filename');
        $package_price = $this->session->userdata('tlm_resubmit_package_price');
        $package_validity = $this->session->userdata('tlm_resubmit_package_validity');

        if ($user_id != $this->user_id) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['error' => $message]);
            return;
        }
    }

    public function transaction_log_manual_resubmit_data() 
    {
        if (! $this->input->is_ajax_request()) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['msg' => $message]);
            return;
        }

        $this->form_validation->set_rules('id', $this->lang->line('Transaction ID'), 'trim|required');
        if (false === $this->form_validation->run()) {
            if ($this->form_validation->error('id')) {
                $message = $this->form_validation->error('id');
            }

            echo json_encode(['error' => strip_tags($message)]);
            return;
        }

        // Gets transaction ID
        $id = (int) $this->input->post('id');

        $where = [
            'where' => [
                'transaction_history_manual.id' => $id,
                'transaction_history_manual.status' => '2'
            ],
        ];

        $select = [
            'transaction_history_manual.id as thm_id',
            'transaction_history_manual.filename',
            'transaction_history_manual.paid_amount',
            'transaction_history_manual.paid_currency',
            'transaction_history_manual.additional_info',
            'users.id as user_id',
            'package.id as package_id',
            'package.price as package_price',
            'package.validity as package_validity',            
        ];

        $join = [
            'users' => 'transaction_history_manual.user_id = users.id,left',
            'package' => 'transaction_history_manual.package_id = package.id,left',
        ];

        $resubmit_data = $this->basic->get_data('transaction_history_manual', $where, $select, $join, 1);
        $payment_instructions = $this->basic->get_data('payment_config', [], ['manual_payment', 'manual_payment_instruction'], [], 1);

        if (1 != sizeof($resubmit_data)) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['error' => $message]);
            return;
        }

        $resubmit_data = isset($resubmit_data[0]) ? $resubmit_data[0] : '';
        $payment_instructions = isset($payment_instructions[0]) ? $payment_instructions[0] : '';

        // Prepares vars
        $user_id = $resubmit_data['user_id'];
        $package_id = $resubmit_data['package_id'];
        $package_price = $resubmit_data['package_price'];
        $package_validity = $resubmit_data['package_validity'];

        $filename = $resubmit_data['filename'];
        $paid_amount = $resubmit_data['paid_amount'];
        $paid_currency = $resubmit_data['paid_currency'];
        $additional_info = $resubmit_data['additional_info'];
        $manual_payment_status = isset($payment_instructions['manual_payment']) ? $payment_instructions['manual_payment'] : '';
        $manual_payment_instruction = isset($payment_instructions['manual_payment_instruction']) ? $payment_instructions['manual_payment_instruction'] : '';

        if ($user_id != $this->user_id) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['error' => $message]);
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'package_id' => $package_id, 
            'paid_amount' => $paid_amount,
            'paid_currency' => $paid_currency,
            'additional_info' => $additional_info,
            'manual_payment_status' => $manual_payment_status,
            'manual_payment_instruction' => $manual_payment_instruction,
        ]);

        // $this->session->set_userdata('tlm_resubmit_user_id', $user_id);
        // $this->session->set_userdata('tlm_resubmit_filename', $filename);
        // $this->session->set_userdata('tlm_resubmit_package_price', $price);
        // $this->session->set_userdata('tlm_resubmit_package_validity', $validity);
    }

    private function handle_attachment($id, $file) 
    {
        $info = pathinfo($file);
        if (isset($info['extension']) && ! empty($info['extension'])) {
            switch (strtolower($info['extension'])) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return $this->manual_payment_display_attachment($file);
                case 'zip':
                case 'pdf':
                case 'txt':
                    return '<div data-id="' . $id . '" id="mp-download-file" class="btn btn-outline-info"><i class="fa fa-download"></i></div>';
            }
        }
    }

    public function manual_payment_download_file() 
    {
        // Prevents out-of-memory issue
        if (ob_get_level()) {
            ob_end_clean();
        }

        // If it is GET request let it download file
        $method = $this->input->method();
        if ('get' == $method) {
            $filename = $this->session->userdata('manual_payment_download_file');

            if (! $filename) {
                $message = $this->lang->line('No file to download.');
                echo json_encode(['msg' => $message]);
            } else {
                $file = APPPATH . '../upload/manual_payment/' . $filename;

                header('Expires: 0');
                header('Pragma: public');
                header('Cache-Control: must-revalidate');
                header('Content-Length: ' . filesize($file));
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                readfile($file);
                $this->session->unset_userdata('manual_payment_download_file');
                exit;
            }

        // If it is POST request, grabs the file
        } elseif ('post' === $method) {
            if (! $this->input->is_ajax_request()) {
                $message = $this->lang->line('Bad Request.');
                echo json_encode(['msg' => $message]);
                exit;
            }

            // Grabs transaction ID
            $id = (int) $this->input->post('file');

            // Checks file owner
            $select = ['id', 'user_id', 'filename'];
            $where = [];
            if ('Admin' == $this->session->userdata('user_type')) {
                $where = [
                    'where' => [
                        'id' => $id,
                    ],
                ];
            } else {
                $where = [
                    'where' => [
                        'id' => $id,
                        'user_id' => $this->user_id,
                    ],
                ];
            }

            $result = $this->basic->get_data('transaction_history_manual', $where, $select, [], 1);
            if (1 != count($result)) {
                $message = $this->lang->line('You do not have permission to download this file.');
                echo json_encode(['error' => $message]);
                exit;
            }

            $filename = $result[0]['filename'];
            $this->session->set_userdata('manual_payment_download_file', $filename);

            echo json_encode(['status' => 'ok']);
        }
    }

    private function manual_payment_display_attachment($file) 
    {
        $output = '<div class="mp-display-img">';
        $output .= '<div class="mp-img-item btn btn-outline-info" data-image="' . $file . '" href="' . $file . '">';
        $output .= '<i class="fa fa-image"></i>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<script>$(".mp-display-img").Chocolat({className: "mp-display-img", imageSelector: ".mp-img-item"});</script>';

        return $output;
    }

    public function manual_payment_handle_actions() 
    {
        if (! $this->input->is_ajax_request()) {
            $message = $this->lang->line('Bad Request.');
            echo json_encode(['msg' => $message]);
            exit;
        }

        $this->form_validation->set_rules('id', $this->lang->line('Transaction ID'), 'required|numeric');
        $this->form_validation->set_rules('action_type', $this->lang->line('Action type'), 'trim|required|in_list[mp-approve-btn,mp-reject-btn]');
        $this->form_validation->set_rules('rejected_reason', $this->lang->line('Rejection reason'), 'trim');

        if (false === $this->form_validation->run()) {
            if ($this->form_validation->error('id')) {
                $message = $this->form_validation->error('id');
            } elseif ($this->form_validation->error('action_type')) {
                $message = $this->form_validation->error('action_type');
            } elseif ($this->form_validation->error('rejected_reason')) {
                $message = $this->form_validation->error('rejected_reason');
            }

            echo json_encode(['error' => strip_tags($message)]);
            exit;
        }

        $id = $this->input->post('id');
        $action_type = $this->input->post('action_type');
        $rejected_reason = $this->input->post('rejected_reason');

        switch ($action_type) {
            case 'mp-approve-btn':
                $this->manual_payment_approve($id);
                return;

            case 'mp-reject-btn':
                $this->manual_payment_reject($id, $rejected_reason);
                return;

            default:
                $message = $this->lang->line('The action type was not valid.');
                echo json_encode(['error' => $message]);
                return;
        }
    }

    public function manual_payment_approve($transaction_id) 
    {
        if (! $this->input->is_ajax_request() 
            || 'Admin' != $this->session->userdata('user_type')
        ) {
            $message = $this->lang->line('Bad Request.');
            echo json_encode(['msg' => $message]);
            exit;
        }

        $man_select = [
            'transaction_history_manual.id as thm_id',
            'transaction_history_manual.user_id',
            'transaction_history_manual.package_id',
            'transaction_history_manual.transaction_id',
            'transaction_history_manual.paid_amount',
            'transaction_history_manual.status',
            'transaction_history_manual.created_at',
            'users.name',
            'users.email',
            'package.price',
            'package.validity',
        ];

        $man_where = [
            'where' => [
                'transaction_history_manual.id' => $transaction_id,
                // 'transaction_history_manual.status' => '0',
            ],
        ];

        $man_join = [
            'users' => 'transaction_history_manual.user_id = users.id,left',
            'package' => 'transaction_history_manual.package_id = package.id,left',
        ];

        $manual_transaction = $this->basic->get_data('transaction_history_manual', $man_where, $man_select, $man_join, 1);

        if (1 != sizeof($manual_transaction)) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['error' => $message]);
            return;
        }

        // Manual transaction info
        $manual_transaction = $manual_transaction[0];

        // Payment status
        $status = $manual_transaction['status'];
        if ('1' == $status) {
            $message = $this->lang->line('The transaction had already been approved.');
            echo json_encode(['error' => $message]);
            return;
        } elseif ('2' == $status) {
            $message = $this->lang->line('The transaction had been rejected and you can not approve it.');
            echo json_encode(['error' => $message]);
            return;
        }
        
        // Prepares some vars
        $name = explode(' ', $manual_transaction['name']);
        $first_name = isset($name[0]) ? $name[0] : '';
        $last_name = isset($name[1]) ? $name[1] : '';
        $name = $first_name . ' ' . $last_name;
        $email = $manual_transaction['email'];
        $user_id = $manual_transaction['user_id'];
        $package_id = $manual_transaction['package_id'];
        $paid_amount = $manual_transaction['paid_amount'];
        $transaction_id = $manual_transaction['transaction_id'];

        // Prepares sql for 'transaction_history' table
        $prev_where['where'] = ['user_id' => $user_id];
        $prev_select = ['cycle_start_date', 'cycle_expired_date'];
        
        $prev_payment_info = $this->basic->get_data('transaction_history', $prev_where, $prev_select, $join = '', $limit = '1', $start = 0, $order_by = 'ID DESC', $group_by = '');

        // Previous payment info
        $prev_payment = isset($prev_payment_info[0]) ? $prev_payment_info[0] : [];

        // Prepares cycle start and end date
        $prev_cycle_expired_date = '';
        if (1 == sizeof($prev_payment_info)) {
            $prev_cycle_expired_date = $prev_payment['cycle_expired_date'];
        }

        $validity_str = '+' . $manual_transaction['validity'] . ' day';
        if ('' == $prev_cycle_expired_date || strtotime($prev_cycle_expired_date) == strtotime(date('Y-m-d'))) {
            $cycle_start_date = date('Y-m-d');
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        } elseif (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))) {
            $cycle_start_date = date('Y-m-d');
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        } elseif (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))) {
            $cycle_start_date = date("Y-m-d",strtotime('+1 day', strtotime($prev_cycle_expired_date)));
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        }

        // Data for 'transaction_history' table
        $transaction_history_data = [
            'verify_status'     => '',
            'first_name'        => $first_name,
            'last_name'         => $last_name,
            'paypal_email'      => $email,
            'receiver_email'    => $email,
            'country'           => '',
            'payment_date'      => date('Y-m-d H:i:s', strtotime($manual_transaction['created_at'])),
            'payment_type'      => 'manual',
            'transaction_id'    => $transaction_id,
            'user_id'           => $user_id,
            'package_id'        => $package_id,
            'cycle_start_date'  => $cycle_start_date,
            'cycle_expired_date'=> $cycle_expired_date,
            'paid_amount'       => $paid_amount,
        ];

        // Data form 'users' table
        $user_where = ['id' => $user_id];
        $user_data = [
            'expired_date' => $cycle_expired_date, 
            'package_id' => $package_id, 
            'bot_status' => '1'
        ];

        // Begins db transaction
        $this->db->trans_begin();

        // Inserts into 'transaction_history' table
        $this->basic->insert_data('transaction_history', $transaction_history_data);

        // Updates 'users' table
        $this->basic->update_data('users', $user_where, $user_data);

        // Updates 'transaction_history_manual' table
        $this->basic->update_data('transaction_history_manual', 
            ['id' => $manual_transaction['thm_id']], 
            [   
                'status' => '1',
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        // Commits transaction, rollbacks and sends error message otherwise
        if (false === $this->db->trans_status()) {
            $this->db->trans_rollback();
            $message = $this->lang->line('Could not approve the transaction.');
            echo json_encode(['error' => $message]);
            return;            
        } else {
            $this->db->trans_commit();
        }

        // affiliate Section
        if($this->addon_exist('affiliate_system')) {
            $get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
            $affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
            if($affiliate_id != 0) {
                $this->affiliate_commission($affiliate_id,$user_id,'payment',$paid_amount);
            }
        }

        // At this point, payment is approved
        $message = $this->lang->line('Your transaction approved successfully.');
        echo json_encode([
            'status' => 'ok',
            'message' => $message
        ]);
        
        // Prepares vars for sending emails to payer and payee
        $product_short_name = $this->config->item('product_short_name');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');

        $payment_confirmation_email_template = $this->basic->get_data('email_template_management',
            [
                'where' => [
                    'template_type' => 'paypal_payment',
                ],
                'or_where' => [
                    'template_type' => 'paypal_new_payment_made',
                ]
            ],
            [
                'subject',
                'message',
            ],$join='',$limit='',$start=NULL,$order_by='id asc'
        );

        // Sends email to payer
        if (isset($payment_confirmation_email_template[0]) 
            && '' != $payment_confirmation_email_template[0]['subject'] 
            && '' != $payment_confirmation_email_template[0]['message']
        ) {
            $to = $email;
            $url = base_url();
            $subject = $payment_confirmation_email_template[0]['subject'];
            $message = str_replace(
                [
                    '#PRODUCT_SHORT_NAME#',
                    '#APP_SHORT_NAME#',
                    '#CYCLE_EXPIRED_DATE#',
                    '#SITE_URL#',
                    '#APP_NAME#',
                ], 
                [
                    $product_short_name,
                    $cycle_expired_date,
                    $url,
                    $mask,
                ],
                $payment_confirmation_email_template[0]['message']
            );

            // Sends mail to payer
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
        } else {
            $to = $email;
            $subject = 'Payment Confirmation';
            $message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href=\"" . base_url() . "\">{$mask}</a> team";

            // Sends mail to payer
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
        }

        // New payment email to payee (admin)
        if(isset($payment_confirmation_email_template[1]) 
            && '' != $payment_confirmation_email_template[1]['subject'] 
            && '' != $payment_confirmation_email_template[1]['message']
        ) {
            $to = $from;
            $subject = $payment_confirmation_email_template[1]['subject'];
            $message = str_replace('#PAID_USER_NAME#', $name, $payment_confirmation_email_template[1]['message']);

            // Sends mail to payee (admin)
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
        } else {
            $to = $from;
            $subject = 'New Payment Made';
            $message = "New payment has been made by {$name}";

            // Sends email to payee (admin)
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
        }        
    }

    public function manual_payment_reject($id, $rejected_reason) 
    {
        if (! $this->input->is_ajax_request()
            || 'Admin' != $this->session->userdata('user_type')
        ) {
            $message = $this->lang->line('Bad Request.');
            echo json_encode(['msg' => $message]);
            exit;
        }

        $man_select = [
            'transaction_history_manual.id as thm_id',
            'transaction_history_manual.user_id',
            'transaction_history_manual.package_id',
            'transaction_history_manual.transaction_id',
            'transaction_history_manual.status',
            'users.name',
            'users.email',
        ];

        $man_where = [
            'where' => [
                'transaction_history_manual.id' => $id,
                // 'transaction_history_manual.status' => '0',
            ],
        ];

        $man_join = [
            'users' => 'transaction_history_manual.user_id = users.id,left',
        ];

        $manual_transaction = $this->basic->get_data('transaction_history_manual', $man_where, $man_select, $man_join, 1);

        if (1 != sizeof($manual_transaction)) {
            $message = $this->lang->line('Bad request.');
            echo json_encode(['error' => $message]);
            return;
        }

        // Manual transaction info
        $manual_transaction = $manual_transaction[0];
        
        // Holds transaction status
        $status = $manual_transaction['status'];
        $transaction_id = $manual_transaction['transaction_id'];

        if ('1' == $status) {
            $message = $this->lang->line('The transaction had already been approved.');
            echo json_encode(['error' => $message]);
            return;
        } elseif ('2' == $status) {
            $message = $this->lang->line('The transaction had already been rejected.');
            echo json_encode(['error' => $message]);
            return;
        }

        if (empty($rejected_reason)) {
            $message = $this->lang->line('Please describe the reason of the rejection of this payment.');
            echo json_encode(['error' => $message]);
            exit;
        }

        // Prepares some vars
        $thm_id = $manual_transaction['thm_id'];
        $email = $manual_transaction['email'];

        $where = ['id' => $thm_id];
        $data = [
            'status' => '2',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->basic->update_data('transaction_history_manual', $where, $data)) {
            $message = $this->lang->line('The transaction has been rejected.');
            echo json_encode(['status' => 'ok', 'message' => $message]);
        } else {
            $message = $this->lang->line('Something went wrong! Please try again later.');
            echo json_encode(['error' => $message]);
        }

        // Prepares vars for sending emails to payer and payee
        $product_short_name = $this->config->item('product_short_name');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');

        // Sends email to payer
        $to = $email;
        $subject = 'Manual payment rejection';
        $message = "Transaction ID: {$transaction_id} has been rejected. Please check out the following reason:<br/><br/>{$rejected_reason}<br/><br/>If you are still want to use this {$product_short_name} system, please resubmit the payment again in accordance with the description above.<br/><br/>Thank you,<br/><a href=\"" . base_url() . "\">{$mask}</a> team";

        // Sends mail to payer
        $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
    
        $to = $from;
        // Sends email to payee (admin)
        $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
    }

    public function payment_button($package_id=0)
    {      
        if($package_id == 0) exit;

        if ($this->session->userdata('license_type') == 'double' && $this->session->userdata('user_type') == 'Member') 
        {
            $config_data=$this->basic->get_data("payment_config");
            if(!isset($config_data[0])) 
            {
                $buttons_html = '<div class="alert alert-warning alert-has-icon">
                                  <div class="alert-icon"><i class="far fa-credit-card"></i></div>
                                  <div class="alert-body">
                                    <div class="alert-title">'.$this->lang->line("Warning").'</div>
                                    '.$this->lang->line("No payment method found").'
                                  </div>
                                </div>';
            }
            $config_data = $config_data[0];
            
            
            $cancel_url=base_url()."payment/transaction_log?action=cancel";
            $success_url=base_url()."payment/transaction_log?action=success";
            
            $payment_amount=0;
            $package_name="";
            $package_validity="";
            $package_id=$package_id;
            $package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
            if(is_array($package_data) && array_key_exists(0, $package_data))
            {
                $payment_amount=$package_data[0]["price"];
                $package_name=$package_data[0]["package_name"];
                $package_validity=$package_data[0]["validity"];
                $validity_extra_info=$package_data[0]["validity_extra_info"];
                $validity_extra_info = explode(',', $validity_extra_info);
            }
            else 
            {
                // echo $this->lang->line("something went wrong, please try again.");
                exit();
            }

            $where['where'] = array('deleted'=>'0');
            $payment_config = $this->basic->get_data('payment_config',$where,$select='');
            if(!empty($payment_config)) 
            {
                $paypal_email = $payment_config[0]['paypal_email'];
                $currency = $payment_config[0]["currency"];
                $stripe_secret = $payment_config[0]["stripe_secret_key"];

                $razorpay_key_id = isset($payment_config[0]['razorpay_key_id']) ? $payment_config[0]['razorpay_key_id'] : '';
                $razorpay_key_secret = isset($payment_config[0]['razorpay_key_secret']) ? $payment_config[0]['razorpay_key_secret'] : '';
                $paystack_secret_key = isset($payment_config[0]['paystack_secret_key']) ? $payment_config[0]['paystack_secret_key'] : '';
                $paystack_public_key = isset($payment_config[0]['paystack_public_key']) ? $payment_config[0]['paystack_public_key'] : '';
                $mollie_api_key = isset($payment_config[0]['mollie_api_key']) ? $payment_config[0]['mollie_api_key'] : '';

                $mercadopago_public_key = isset($payment_config[0]['mercadopago_public_key']) ? $payment_config[0]['mercadopago_public_key'] : '';
                $mercadopago_access_token = isset($payment_config[0]['mercadopago_access_token']) ? $payment_config[0]['mercadopago_access_token'] : '';
                 $marcadopago_country = isset($payment_config[0]['marcadopago_country']) ? $payment_config[0]['marcadopago_country'] : '';

                $sslcommerz_store_id = isset($payment_config[0]['sslcommerz_store_id']) ? $payment_config[0]['sslcommerz_store_id'] : '';
                $sslcommerz_store_password = isset($payment_config[0]['sslcommerz_store_password']) ? $payment_config[0]['sslcommerz_store_password'] : '';
                $sslcommers_mode = isset($payment_config[0]['sslcommers_mode']) ? $payment_config[0]['sslcommers_mode'] : 'live';

                $senangpay_merchent_id = isset($payment_config[0]['senangpay_merchent_id']) ? $payment_config[0]['senangpay_merchent_id'] : '';
                $senangpay_secret_key = isset($payment_config[0]['senangpay_secret_key']) ? $payment_config[0]['senangpay_secret_key'] : '';
                $senangpay_mode = isset($payment_config[0]['senangpay_mode']) ? $payment_config[0]['senangpay_mode'] : 'live';

                $instamojo_api_key = isset($payment_config[0]['instamojo_api_key']) ? $payment_config[0]['instamojo_api_key'] : '';
                $instamojo_auth_token = isset($payment_config[0]['instamojo_auth_token']) ? $payment_config[0]['instamojo_auth_token'] : '';
                $instamojo_mode = isset($payment_config[0]['instamojo_mode']) ? $payment_config[0]['instamojo_mode'] : 'live';

                $toyyibpay_secret_key = isset($payment_config[0]['toyyibpay_secret_key']) ? $payment_config[0]['toyyibpay_secret_key'] : '';
                $toyyibpay_category_code = isset($payment_config[0]['toyyibpay_category_code']) ? $payment_config[0]['toyyibpay_category_code'] : '';
                $toyyibpay_mode = isset($payment_config[0]['toyyibpay_mode']) ? $payment_config[0]['toyyibpay_mode'] : 'live';
                $xendit_secret_api_key = isset($payment_config[0]['xendit_secret_api_key']) ? $payment_config[0]['xendit_secret_api_key'] : '';

                $paymaya_public_key = isset($payment_config[0]['paymaya_public_key']) ? $payment_config[0]['paymaya_public_key'] : '';
                $paymaya_secret_key = isset($payment_config[0]['paymaya_secret_key']) ? $payment_config[0]['paymaya_secret_key'] : '';
                $paymaya_mode = isset($payment_config[0]['paymaya_mode']) ? $payment_config[0]['paymaya_mode'] : 'live';

                $myfatoorah_api_key = isset($payment_config[0]['myfatoorah_api_key']) ? $payment_config[0]['myfatoorah_api_key'] : '';
                $myfatoorah_mode = isset($payment_config[0]['myfatoorah_mode']) ? $payment_config[0]['myfatoorah_mode'] : '';


            } 
            else 
            {
                $paypal_email = "";
                $currency = "USD";
                $sslcommers_mode = 'live';
            }

            $user_info = $this->basic->get_data('users',['where'=>['id'=>$this->user_id]]);
            $user_first_name = isset($user_info[0]['first_name']) ? $user_info[0]['first_name'] : '';
            $user_last_name = isset($user_info[0]['last_name']) ? $user_info[0]['last_name'] : '';
            $user_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
            $user_name = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
            $user_mobile = isset($user_info[0]['mobile']) ? $user_info[0]['mobile'] : '';
            
            $this->paypal_class->cancel_url=$cancel_url;
            $this->paypal_class->success_url=$success_url;
            $this->paypal_class->notify_url=site_url()."paypal_ipn/ipn_notify";

            if ($this->session->userdata('license_type') == 'double' && $config_data['paypal_payment_type'] == 'recurring') {

                $this->paypal_class->a3=$payment_amount;
                $this->paypal_class->p3=$validity_extra_info[0];
                $this->paypal_class->t3=$validity_extra_info[1];
                $this->paypal_class->src='1';
                $this->paypal_class->sra='1';
                $this->paypal_class->is_recurring=true;
            }
            else
                $this->paypal_class->amount=$payment_amount;

            $this->paypal_class->user_id=$this->user_id;
            $this->paypal_class->business_email=$paypal_email;
            $this->paypal_class->currency=$currency;
            $this->paypal_class->secondary_button=true;
            $this->paypal_class->button_lang=$this->lang->line("Pay with PayPal");
            $this->paypal_class->package_id=$package_id;
            $this->paypal_class->product_name=$this->config->item("product_name")." : ".$package_name." (".$package_validity." days)";
            

            // all buttons initialized to empty
            $pp_button = $st_button = $razorpay_button = $paystack_button = $mollie_button = $mercadopago = $sslcommerz = $senangpay = $instamojo = $xendit = $toyyibpay= $myfatoorah = $paymaya_button = "" ;

            
            $this->session->set_userdata('stripe_payment_package_id',$package_id);
            $this->session->set_userdata('stripe_payment_amount',$payment_amount);
            
            if($paypal_email!="")
            $pp_button = $this->paypal_class->set_button(); 

            /*****  Stripe Button ******/
            if($stripe_secret!=""){
            $this->stripe_class->description=$this->config->item("product_name")." : ".$package_name." (".$package_validity." days)";
            $this->stripe_class->amount=$payment_amount;
            $this->stripe_class->secondary_button=true;
            $this->stripe_class->stripe_lang=$this->lang->line("Pay with Stripe");
            $this->stripe_class->action_url=site_url()."stripe_action/index/".$this->user_id.'/'.$package_id;
            $st_button = $this->stripe_class->set_button();
            }    

            if($razorpay_key_id!="" && $razorpay_key_secret!="")
            { 
              $this->load->library("razorpay_class_ecommerce");

              $this->razorpay_class_ecommerce->key_id=$razorpay_key_id;
              $this->razorpay_class_ecommerce->key_secret=$razorpay_key_secret; 
              $this->razorpay_class_ecommerce->title=$package_name;
              $this->razorpay_class_ecommerce->description=$this->config->item("product_name")." : ".$package_name." (".$package_validity." days)";
              $this->razorpay_class_ecommerce->amount=$payment_amount;
              $this->razorpay_class_ecommerce->action_url=base_url("stripe_action/razorpay_action/".$this->user_id.'/'.$package_id); 
              $this->razorpay_class_ecommerce->currency=$currency;
              $store_favicon = base_url("assets/img/logo.png");
              $this->razorpay_class_ecommerce->img_url=$store_favicon;
              $this->razorpay_class_ecommerce->customer_name=$user_first_name." ".$user_last_name;
              $this->razorpay_class_ecommerce->customer_email=$user_email;
              $this->razorpay_class_ecommerce->secondary_button=true;
              $this->razorpay_class_ecommerce->button_lang=$this->lang->line("Pay with Razorpay");

              // for action function, because it's not web hook based, it's js based
              $this->session->set_userdata('razorpay_payment_package_id',$package_id);
              $this->session->set_userdata('razorpay_payment_amount',$payment_amount);

              $razorpay_button =  $this->razorpay_class_ecommerce->set_button();
            }   

            if($paystack_secret_key!="" && $paystack_public_key!="")
            { 
              $this->load->library("paystack_class_ecommerce");

              $this->paystack_class_ecommerce->secret_key=$paystack_secret_key;
              $this->paystack_class_ecommerce->public_key=$paystack_public_key; 
              $this->paystack_class_ecommerce->title=$package_name;
              $this->paystack_class_ecommerce->description=$this->config->item("product_name")." : ".$package_name." (".$package_validity." days)";
              $this->paystack_class_ecommerce->amount=$payment_amount;
              $this->paystack_class_ecommerce->action_url=base_url("stripe_action/paystack_action/".$this->user_id.'/'.$package_id); 
              $this->paystack_class_ecommerce->currency=$currency;
              $this->paystack_class_ecommerce->img_url=base_url("assets/img/logo.png");
              $this->paystack_class_ecommerce->customer_first_name=$user_first_name;
              $this->paystack_class_ecommerce->customer_last_name=$user_last_name;
              $this->paystack_class_ecommerce->customer_email=$user_email;
              $this->paystack_class_ecommerce->secondary_button=true;
              $this->paystack_class_ecommerce->button_lang=$this->lang->line("Pay with Paystack");

              // for action function, because it's not web hook based, it's js based
              $this->session->set_userdata('paystack_payment_package_id',$package_id);
              $this->session->set_userdata('paystack_payment_amount',$payment_amount);

              $paystack_button =  $this->paystack_class_ecommerce->set_button();
            }  

            if($mercadopago_public_key!='' && $mercadopago_access_token!='')
            {
                $this->load->library("mercadopago");

                $this->mercadopago->public_key=$mercadopago_public_key;
                $this->mercadopago->marcadopago_url = 'https://www.mercadopago.com.'.$marcadopago_country;
                $this->mercadopago->redirect_url=base_url("stripe_action/mercadopago_action/".$this->user_id.'/'.$package_id);
                $this->mercadopago->transaction_amount=$payment_amount;
                $this->mercadopago->secondary_button=true;
                $this->mercadopago->button_lang=$this->lang->line('Pay with Mercadopago');

                $this->session->set_userdata('mercadopago_payment_package_id',$package_id);
                $this->session->set_userdata('mercadopago_payment_amount',$payment_amount);
                $this->session->set_userdata('mercadopago_accesstoken',$mercadopago_access_token);

                $mercadopago =  $this->mercadopago->set_button();
            }

            if($mollie_api_key!="")
            { 
              $this->load->library("mollie_class_ecommerce");
              $unique_id = $this->user_id.time();
              $this->mollie_class_ecommerce->api_key=$mollie_api_key; 
              $this->mollie_class_ecommerce->title=$package_name;
              $this->mollie_class_ecommerce->description=$this->config->item("product_name")." : ".$package_name." (".$package_validity." days)";
              $this->mollie_class_ecommerce->amount=$payment_amount;
              $this->mollie_class_ecommerce->action_url=base_url("stripe_action/mollie_action/".$this->user_id.'/'.$package_id); 
              $this->mollie_class_ecommerce->currency=$currency;
              $this->mollie_class_ecommerce->img_url=base_url("assets/img/logo.png");
              $this->mollie_class_ecommerce->customer_name=$user_first_name." ".$user_last_name;
              $this->mollie_class_ecommerce->customer_email=$user_email;
              $this->mollie_class_ecommerce->ec_order_id=$unique_id;
              $this->mollie_class_ecommerce->secondary_button=true;
              $this->mollie_class_ecommerce->button_lang=$this->lang->line("Pay with Mollie");

              // for action function, because it's not web hook based, it's js based
              $this->session->set_userdata('mollie_payment_package_id',$package_id);
              $this->session->set_userdata('mollie_payment_amount',$payment_amount);
              $this->session->set_userdata('mollie_unique_id',$unique_id);

              $mollie_button =  $this->mollie_class_ecommerce->set_button();
            }    

            $postdata_array = [];
            if($sslcommerz_store_id != '' && $sslcommerz_store_password != '')
            {
                $postdata_array = [
                                    'total_amount' => $payment_amount,
                                    'currency' => $currency,
                                    'product_name' => $this->config->item("product_name")." : ".$package_name." (".$package_validity." days)",
                                    'product_category' => $package_name,
                                    'cus_name' => $user_first_name.' '.$user_last_name,
                                    'cus_email' => $user_email,
                                    'package_id' => $package_id,
                                    'user_id' => $this->user_id,
                                ];
                $endpoint_url = base_url('stripe_action/sslcommerz_action');
                $sslcommerz = '<button style="display : none;" class="your-button-class" id="sslczPayBtn"
                                     token="if you have any token validation"
                                     postdata=""
                                     order="If you already have the transaction generated for current order"
                                     endpoint="'.$endpoint_url.'"> Pay With SSLCOMMERZ
                               </button>';

                $sslcommerz .= "
                                <a href='#' class='list-group-item list-group-item-action flex-column align-items-start' onclick=\"document.getElementById('sslczPayBtn').click();\">
                                    <div class='d-flex w-100 align-items-center'>
                                      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/sslcommerz.png')."'></small>
                                      <h6 class='mb-1'>".$this->lang->line('Pay With SSLCOMMERZ')."</h6>
                                    </div>
                                </a>";
            } 

            // SenangPay
            if($senangpay_merchent_id != '' && $senangpay_secret_key != ''){

            $senangpay_order_id = $package_id.'_'.$this->user_id;
            // $hashed_string = md5($senangpay_secret_key.urldecode($this->config->item("product_name")).urldecode($payment_amount).urldecode($senangpay_order_id));
             $hashed_string = hash_hmac('sha256', $senangpay_secret_key.urldecode($this->config->item("product_name")).urldecode($payment_amount).urldecode($senangpay_order_id), $senangpay_secret_key);
            $this->load->library('senangpay');
            $this->senangpay->merchant_id = $senangpay_merchent_id;
            $this->senangpay->secretkey = $senangpay_secret_key;
            $this->senangpay->detail =$this->config->item("product_name");
            $this->senangpay->amount = $payment_amount;
            $this->senangpay->order_id = $senangpay_order_id;
            $this->senangpay->name = $user_name;
            $this->senangpay->email = $user_email;
            $this->senangpay->phone = $user_mobile;
            $this->senangpay->senangpay_mode = $senangpay_mode;
            $this->senangpay->hashed_string = $hashed_string;
            $this->senangpay->secondary_button = true;
            $this->senangpay->button_lang = $this->lang->line('Pay With Senangpay');
            $senangpay = $this->senangpay->set_button();

        }

        // Instamojo

        if($instamojo_api_key != '' && $instamojo_auth_token != '')
        {
            $redirect_url_instamojo = base_url('stripe_action/instamojo_action/').$package_id.'/'.$this->user_id;
            $this->load->library('instamojo');
            $this->instamojo->redirect_url = $redirect_url_instamojo;
            $this->instamojo->button_lang = $this->lang->line('Pay With Instamojo');
            $instamojo = $this->instamojo->set_button();
        }

        // toyibpay

        if($toyyibpay_secret_key != '' && $toyyibpay_category_code != '')
        {
            $redirect_url_toyyibpay = base_url('stripe_action/toyyibpay_action/').$package_id.'/'.$this->user_id;
            $this->load->library('toyyibpay');
            $this->toyyibpay->redirect_url = $redirect_url_toyyibpay;
            $this->toyyibpay->button_lang = $this->lang->line('Pay with toyyibpay');
            $toyyibpay = $this->toyyibpay->set_button();
        }

         if($paymaya_public_key != '' && $paymaya_secret_key != '')
        {
            $redirect_url_paymaya = base_url('stripe_action/paymaya_action/').$package_id.'/'.$this->user_id;
            $this->load->library('paymaya');
            $this->paymaya->redirect_url = $redirect_url_paymaya;
            $this->paymaya->button_lang = $this->lang->line('Pay with paymaya');
            $paymaya_button = $this->paymaya->set_button();
        }


         if($xendit_secret_api_key != '')
        {
            $xendit_redirect_url = base_url('stripe_action/xendit_action/').$package_id.'/'.$this->user_id;
            $xendit_success_redirect_url = base_url('stripe_action/xendit_success/');
            $xendit_failure_redirect_url = base_url('stripe_action/xendit_fail/');
            $this->load->library('xendit');
            $this->xendit->xendit_redirect_url = $xendit_redirect_url;
            $this->xendit->xendit_success_redirect_url = $xendit_success_redirect_url;
            $this->xendit->xendit_failure_redirect_url = $xendit_failure_redirect_url;
            $this->xendit->button_lang = $this->lang->line('Pay With Xendit');
            $xendit = $this->xendit->set_button();
        }



        if($myfatoorah_api_key != '')
        {
            // This is test . The redirect url must be in live server . So when i  set the redirect url to a live server for test purpose
            $redirect_url_myfatoorah = base_url('stripe_action/myfatoorah_action/').$package_id.'/'.$this->user_id;
            $this->load->library('myfatoorah');
            $this->myfatoorah->redirect_url = $redirect_url_myfatoorah;
            $this->myfatoorah->button_lang = $this->lang->line('Pay With myfatoorah');
            $myfatoorah = $this->myfatoorah->set_button();
        }




            $buttons_html = '<br><div class="row" id="payment_options">';
            if($pp_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$pp_button.'</div>';
            if($st_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$st_button.'</div>';
            if($razorpay_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$razorpay_button.'</div>';
            if($paystack_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$paystack_button.'</div>';
            if($mollie_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$mollie_button.'</div>';
            if($mercadopago != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$mercadopago.'</div>';
            if($sslcommerz != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$sslcommerz.'</div>';
            if($senangpay != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$senangpay.'</div>';
            if($instamojo != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$instamojo.'</div>';
            if($toyyibpay != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$toyyibpay.'</div>';
            if($myfatoorah != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$myfatoorah.'</div>';
            if($paymaya_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$paymaya_button.'</div>';
            if($xendit != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$xendit.'</div>';
            $buttons_html .= '</div>';
                                    

            $data['body'] = "member/buy_buttons";
            $data['postdata_array'] = json_encode($postdata_array);
            $data['sslcommers_mode'] = $sslcommers_mode;
            $data['buttons_html'] = $buttons_html;
            $data['page_title'] = $this->lang->line('Make Payment');
            $data['package_id'] = $package_id;

            $config_data=$this->basic->get_data("payment_config");
            $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
            $currency_icons = $this->currency_icon();
            $data["currency"]=$currency;
            $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";
            $data['currency_list'] = $this->currecny_list_all();

            $data['payment_type'] = isset($config_data[0]['paypal_payment_type'])?$config_data[0]['paypal_payment_type']:"manual";
            $data['manual_payment'] = isset($config_data[0]['manual_payment'])?$config_data[0]['manual_payment']:"no";
            $data['manual_payment_instruction'] = isset($config_data[0]['manual_payment_instruction'])?$config_data[0]['manual_payment_instruction']:"";
            $payment_method = $this->basic->get_data('transaction_history', array('where' => array('user_id' => $this->user_id), array('payment_type'), '', '', '', 'payment_date,dsc'));
            $data['payment_method'] = isset($payment_method[0]['payment_type']) ? $payment_method[0]['payment_type'] : 'Paypal';
            $data["payment_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0,"validity >"=>0,"visible"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='CAST(`price` AS SIGNED)');

            $user_info = $this->basic->get_data('users', array('where' => array('id' => $this->user_id)), array('paypal_subscription_enabled', 'last_payment_method'));
            if(!isset($user_info[0])) exit();
            if($user_info[0]['paypal_subscription_enabled'] == '1' ) $data['has_reccuring'] = 'true';
            else $data['has_reccuring'] = 'false';
            $data['last_payment_method'] = $user_info[0]['last_payment_method'];
            $this->_viewcontroller($data);

        }

    }


    public function package_manager()
    {
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') 
        redirect('home/login_page', 'location');
        
        $data['body']='admin/payment/package_list';
        $data['page_title']=$this->lang->line("Package Manager");
        $data['payment_config']=$this->basic->get_data('payment_config');
        $this->_viewcontroller($data);  
    }

    public function package_manager_data()
    { 
        $this->ajax_check();
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') exit();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id', 'package_name','price','validity','is_default');
        $search_columns = array( 'package_name','price','validity');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where = array();
        if ($search_value != '') 
        {
            $or_where = array();
            foreach ($search_columns as $key => $value) 
            $or_where[$value.' LIKE '] = "%$search_value%";
            $where = array('or_where' => $or_where);
        }
            
        $table="package";
        $info=$this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start);

        echo json_encode($data);
    }


    public function add_package()
    {       
        $data['body']='admin/payment/add_package';     
        $data['page_title']=$this->lang->line('Add Package');     
        $data['modules']=$this->basic->get_data('modules',$where='',$select='',$join='',$limit='',$start='',$order_by='module_name asc',$group_by='',$num_rows=0);
        $data['payment_config']=$this->basic->get_data('payment_config');
        $data['validity_type'] = array('D' => $this->lang->line('Day'), 'W' => $this->lang->line('Week'), 'M' => $this->lang->line('Month'), 'Y' => $this->lang->line('Year'));
        $this->_viewcontroller($data);
    }


    public function add_package_action() 
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') 
        redirect('home/login_page', 'location');

        if($_SERVER['REQUEST_METHOD'] === 'GET') 
        redirect('home/access_forbidden','location');

        if($_POST)
        {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Package Name").'</b>', 'trim|required');   
            $this->form_validation->set_rules('price', '<b>'.$this->lang->line("Price").'</b>', 'trim|required');
            $this->form_validation->set_rules('validity_amount', '<b>'.$this->lang->line("Validity").'</b>', 'trim|required|integer');   
            $this->form_validation->set_rules('visible', '<b>'.$this->lang->line("Available to Purchase").'</b>', 'trim');
            $this->form_validation->set_rules('highlight', '<b>'.$this->lang->line("Highlighted Package").'</b>', 'trim');
            $this->form_validation->set_rules('modules[]','<b>'.$this->lang->line("Modules").'</b>','trim|required');       
                
            if ($this->form_validation->run() == FALSE)
            {
                $this->add_package(); 
            }
            else
            {
                $this->csrf_token_check();

                $validity_type_arr['D'] = 1;
                $validity_type_arr['W'] = 7;
                $validity_type_arr['M'] = 30;
                $validity_type_arr['Y'] = 365;

                $package_name=strip_tags($this->input->post('name',true));
                $price=strip_tags($this->input->post('price',true));
                $visible=$this->input->post('visible',true);
                $highlight=$this->input->post('highlight',true);

                if($visible=='') $visible='0';
                if($highlight=='') $highlight='0';

                $validity_amount=$this->input->post('validity_amount',true);
                $validity_type=$this->input->post('validity_type',true);
                $validity = $validity_amount * $validity_type_arr[$validity_type];
                $validity_extra_info = implode(',', array($validity_amount, $validity_type));
                
                $modules=array();
                if(count($this->input->post('modules'))>0)  
                {
                   $modules=$this->input->post('modules');                            
                }

                $bulk_limit=array();
                $monthly_limit=array();

                foreach ($modules as $value) 
                {
                    $monthly_field="monthly_".$value;
                   
                    $val=$this->input->post($monthly_field);
                    if($val=="") $val=0;
                    $monthly_limit[$value]=$val;
               

                    $bulk_field="bulk_".$value;
                    
                    $val=$this->input->post($bulk_field);
                    if($val=="") $val=0;
                    $bulk_limit[$value]=$val;                    
                }



                $modules_str=implode(',',$modules);                        
                               
                $data=array
                (
                    'package_name'=>$package_name,
                    'price'=>$price,
                    'validity'=>$validity,
                    'visible'=>$visible,
                    'highlight'=>$highlight,
                    'validity_extra_info'=>$validity_extra_info,
                    'module_ids'=>$modules_str,
                    'monthly_limit'=>json_encode($monthly_limit),
                    'bulk_limit'=>json_encode($bulk_limit)
                );
                
                if($this->basic->insert_data('package',$data))                                      
                $this->session->set_flashdata('success_message',1);   
                else    
                $this->session->set_flashdata('error_message',1);     
                
                redirect('payment/package_manager','location');                 
                
            }
        }   
    }


    public function details_package($id=0)
    {        
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') 
        redirect('home/login_page', 'location');

        if($id==0)
        redirect('home/access_forbidden','location');

        $data['body']='admin/payment/details_package';        
        $data['page_title']=$this->lang->line("Package Details");        
        $data['modules']=$this->basic->get_data('modules',$where='',$select='',$join='',$limit='',$start='',$order_by='module_name asc',$group_by='',$num_rows=0);
        $data['value']=$this->basic->get_data('package',$where=array("where"=>array("id"=>$id)));
        $data['payment_config']=$this->basic->get_data('payment_config');
        $data['validity_type'] = array('D' => $this->lang->line('Days'), 'W' => $this->lang->line('Weeks'), 'M' => $this->lang->line('Months'), 'Y' => $this->lang->line('Years'));

        $validity_days = $data['value'][0]['validity'];

        if ($validity_days % 365 == 0) {

            $data['validity_type_info'] = 'Y';
            $data['validity_amount'] = $validity_days / 365;
        }
        else if ($validity_days % 30 == 0) {

            $data['validity_type_info'] = 'M';
            $data['validity_amount'] = $validity_days / 30;
        }
        else if ($validity_days % 7 == 0) {

            $data['validity_type_info'] = 'W';
            $data['validity_amount'] = $validity_days / 7;
        }
        else {

            $data['validity_type_info'] = 'D';
            $data['validity_amount'] = $validity_days;
        }

        $this->_viewcontroller($data);  
    }


    public function edit_package($id=0)
    {       
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') 
        redirect('home/login_page', 'location');

        if($id==0) 
        redirect('home/access_forbidden','location');

        $data['body']='admin/payment/edit_package';     
        $data['page_title']=$this->lang->line('Edit Package');     
        $data['modules']=$this->basic->get_data('modules',$where='',$select='',$join='',$limit='',$start='',$order_by='module_name asc',$group_by='',$num_rows=0);
        $data['value']=$this->basic->get_data('package',$where=array("where"=>array("id"=>$id)));
        $data['payment_config']=$this->basic->get_data('payment_config');
        $data['validity_type'] = array('D' => $this->lang->line('Days'), 'W' => $this->lang->line('Weeks'), 'M' => $this->lang->line('Months'), 'Y' => $this->lang->line('Years'));

        $validity_days = $data['value'][0]['validity'];

        if ($validity_days % 365 == 0) {

            $data['validity_type_info'] = 'Y';
            $data['validity_amount'] = $validity_days / 365;
        }
        else if ($validity_days % 30 == 0) {

            $data['validity_type_info'] = 'M';
            $data['validity_amount'] = $validity_days / 30;
        }
        else if ($validity_days % 7 == 0) {

            $data['validity_type_info'] = 'W';
            $data['validity_amount'] = $validity_days / 7;
        }
        else {

            $data['validity_type_info'] = 'D';
            $data['validity_amount'] = $validity_days;
        }

        $this->_viewcontroller($data);
    }


    public function edit_package_action() 
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') 
        redirect('home/login_page', 'location');

        if($_SERVER['REQUEST_METHOD'] === 'GET') 
        redirect('home/access_forbidden','location');

        if($_POST)
        {
            $validity_type_arr['D'] = 1;
            $validity_type_arr['W'] = 7;
            $validity_type_arr['M'] = 30;
            $validity_type_arr['Y'] = 365;

            $id=$this->input->post("id");
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Package Name").'</b>', 'trim|required');
            $this->form_validation->set_rules('visible', '<b>'.$this->lang->line("Available to Purchase").'</b>', 'trim');
            $this->form_validation->set_rules('highlight', '<b>'.$this->lang->line("Highlighted Package").'</b>', 'trim');
            $this->form_validation->set_rules('modules[]','<b>'.$this->lang->line("modules").'</b>','trim');   
            $this->form_validation->set_rules('price', '<b>'.$this->lang->line("price").'</b>', 'trim|required');    
            
            if(($this->input->post("is_default")=="1" && $this->input->post("price")=="Trial") || $this->input->post("is_default")=="0")  
            $this->form_validation->set_rules('validity_amount', '<b>'.$this->lang->line("Validity").'</b>', 'trim|required|integer');   
            
            if ($this->form_validation->run() == FALSE)
            {
                $this->edit_package($id); 
            }
            else
            {
                $this->csrf_token_check();

                $package_name=strip_tags($this->input->post('name',true));
                $price=strip_tags($this->input->post('price',true));
                $visible=$this->input->post('visible',true);
                $highlight=$this->input->post('highlight',true);

                if($visible=='') $visible='0';
                if($highlight=='') $highlight='0';

                // $validity=$this->input->post('validity');
                $validity_amount=$this->input->post('validity_amount',true);
                $validity_type=$this->input->post('validity_type',true);
                $validity = $validity_amount * $validity_type_arr[$validity_type];
                $validity_extra_info = implode(',', array($validity_amount, $validity_type));
                
                $modules=array();
                if(count($this->input->post('modules'))>0)  
                {
                   $modules=$this->input->post('modules');                            
                }

                $bulk_limit=array();
                $monthly_limit=array();

                foreach ($modules as $value) 
                {
                    $monthly_field="monthly_".$value;
                   
                    $val=$this->input->post($monthly_field);
                    if($val=="") $val=0;
                    $monthly_limit[$value]=$val;
               

                    $bulk_field="bulk_".$value;
                    
                    $val=$this->input->post($bulk_field);
                    if($val=="") $val=0;
                    $bulk_limit[$value]=$val;                    
                }


                $modules_str=implode(',',$modules);                        
                               
                if($this->input->post("is_default")=="1" && $this->input->post("price")=="0") 
                $validity="0"; 
                $data=array
                (
                    'package_name'=>$package_name,
                    'validity'=>$validity,
                    'visible'=>$visible,
                    'highlight'=>$highlight,
                    'validity_extra_info'=>$validity_extra_info,
                    'module_ids'=>$modules_str,
                    'price'=>$price,
                    'monthly_limit'=>json_encode($monthly_limit),
                    'bulk_limit'=>json_encode($bulk_limit)
                );
                
                if($this->basic->update_data('package',array("id"=>$id),$data))                                      
                $this->session->set_flashdata('success_message',1);   
                else    
                $this->session->set_flashdata('error_message',1);   


                // print_r($data); exit();
                
                redirect('payment/package_manager','location');                 
                
            }
        }   
    }

    public function delete_package($id=0)
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->is_demo == '1')
        {
            echo json_encode(array("status"=>"0","message"=>"This feature is disabled in this demo.")); 
            exit();
        }        
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') exit();
        if($id==0) exit();

        if($this->basic->update_data('package',array("id"=>$id),array("deleted"=>"1")))                                      
        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Package has been deleted successfully"))); 
        else echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));
    } 


    public function usage_history()
    {        
        if($this->session->userdata('user_type') != 'Member') 
        redirect('home/login_page', 'location');

        $current_month = date("n");
        $current_year = date("Y");

        $info = $this->basic->get_data($table="modules", $where="", $select = "usage_log.*,modules.module_name,modules.id as module_id,limit_enabled,extra_text,bulk_limit_enabled",$join=array('usage_log'=>"usage_log.module_id=modules.id AND user_id =".$this->session->userdata("user_id")." AND usage_month=".$current_month." AND usage_year=".$current_year.",left"),$limit='',$start=NULL,$order_by='module_name asc');  

        $package_info=$this->session->userdata("package_info");

        // module count of not monthly
        $this->db->select('sum(usage_count) as usage_count,module_id');
        $this->db->where('user_id', $this->user_id);
        $this->db->group_by('module_id');
        $not_monthy_info = $this->db->get('usage_log')->result_array();
        $not_monthy_module_info=array(); 
        foreach ($not_monthy_info as $key => $value) 
        {
            $not_monthy_module_info[$value['module_id']]=$value['usage_count'];
        }
        $data['not_monthy_module_info']=$not_monthy_module_info;

        $monthly_limit='';

        if(isset($package_info["monthly_limit"]))  $monthly_limit=$package_info["monthly_limit"];
        $bulk_limit=array();
        if(isset($package_info["bulk_limit"]))  $bulk_limit=$package_info["bulk_limit"];
        $package_name="No Package";
        if(isset($package_info["package_name"]))  $package_name=$package_info["package_name"];
        $validity="0";
        if(isset($package_info["validity"]))  $validity=$package_info["validity"];
        $price="0";
        if(isset($package_info["price"]))  $price=$package_info["price"];

        $data['info']=$info;
        $data['monthly_limit']=json_decode($monthly_limit,true);
        $data['bulk_limit']=json_decode($bulk_limit,true);
        $data['package_name']=$package_name;
        $data['validity']=$validity;
        $data['price']=$price;

        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        $currency_icons = $this->currency_icon();
        $data["currency"]=$currency;
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";
        
        $data['body'] = 'member/usage_log';
        $data['page_title'] = $this->lang->line("Usage Log");

        $this->_viewcontroller($data);
    }

   

   
    
}