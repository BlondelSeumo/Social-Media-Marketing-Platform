<?php

require_once("application/controllers/Home.php"); // loading home controller
class Integration extends Home
{

	public function __construct()
	{
	    parent::__construct();

	    if ($this->session->userdata('logged_in')!= 1) {
	        redirect('home/login', 'location');
	    }

	    $this->load->helper('form');
	    $this->load->library('upload');

	    $this->important_feature();
	}

	public function index()
	{
		$this->integration_menu_section();
	}

	public function integration_menu_section()
	{
		$data['body'] = 'api_channels';
		$data['page_title'] = $this->lang->line('API Channels');
		$data['has_autoresponder_access'] = false;
		$data['has_json_access'] = false;
		$data['has_sms_access'] = false;
		$data['has_email_access'] = false;

		if($this->session->userdata('user_type') == 'Admin' || in_array(263,$this->module_access)) $data['has_email_access'] = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(264,$this->module_access)) $data['has_sms_access'] = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(265,$this->module_access)) $data['has_autoresponder_access'] = true;

		if($this->basic->is_exist("add_ons",array("project_id"=>31))) {
			if($this->session->userdata('user_type') == 'Admin' || in_array(258,$this->module_access)) {
				$data['has_json_access'] = true;
			}

		}

		$data['payment_gateway_url'] = base_url('payment/accounts');
		$data['email_autoresponder_apis'] = $this->get_autoresponders();
		$data['social_medias'] = $this->get_social_medias();
		$data['payment_apis'] = $this->get_payment_apis();
		$data['sms_email_apis'] = $this->get_sms_email();

		$this->_viewcontroller($data);

	}

	public function get_autoresponders()
	{
		$asset_path_common = base_url('assets/img/api_channel_icon/');
		return [
			'0'=>[
				'title'=>$this->lang->line('MailChimp'),
				'img_path' =>$asset_path_common.'auto_responder/mailchimp.png',
				'action_url'=> base_url('email_auto_responder_integration/mailchimp_list'),
			],
			'1'=>[
				'title'=>$this->lang->line('Sendinblue'),
				'img_path' =>$asset_path_common.'auto_responder/sendinblue.png',
				'action_url'=> base_url('email_auto_responder_integration/sendinblue_list'),
			],
			'2'=>[
				'title'=>$this->lang->line('Mautic'),
				'img_path' =>$asset_path_common.'auto_responder/mautic.png',
				'action_url'=> base_url('email_auto_responder_integration/mautic_list'),
			],
			'3'=>[
				'title'=>$this->lang->line('ActiveCampaign'),
				'img_path' =>$asset_path_common.'auto_responder/activecampaign.png',
				'action_url'=> base_url('email_auto_responder_integration/activecampaign_list'),
			],
			'4'=>[
				'title'=>$this->lang->line('Acelle'),
				'img_path' =>$asset_path_common.'auto_responder/acelle.png',
				'action_url'=> base_url('email_auto_responder_integration/acelle_list'),
			],

		];
		
	}

	public function get_social_medias()
	{
		$has_access = false;
		$has_facebook_access = false;
		$has_twitter_access = false;
		$has_linkedin_access = false;
		$has_medium_access = false;
		$has_google_access = false;
		$has_reddit_access = false;
		$has_blogger_access = false;
		$has_wp_access = false;
		$has_wpSelf_access = false;
		$asset_path_common = base_url('assets/img/api_channel_icon/');

		if($this->session->userdata('user_type') == 'Admin' || in_array(65,$this->module_access)) $has_facebook_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(102,$this->module_access)) $has_twitter_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(103,$this->module_access)) $has_linkedin_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(277,$this->module_access)) $has_medium_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(107,$this->module_access)) $has_google_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(105,$this->module_access)) $has_reddit_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(107,$this->module_access)) $has_blogger_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(108,$this->module_access)) $has_wp_access = true;
		if($this->session->userdata('user_type') == 'Admin' || in_array(109,$this->module_access)) $has_wpSelf_access = true;

		return [
			'0'=>[
				'title'=>$this->lang->line('Facebook'),
				'img_path' =>$asset_path_common.'social_media/facebook.png',
				'action_url'=> base_url('social_apps/facebook_settings'),
				'account_import_url' => base_url('social_accounts/index'),
				'has_access'=> $has_facebook_access,
			],
			'1'=>[
				'title'=>$this->lang->line('Twitter'),
				'img_path' =>$asset_path_common.'social_media/twitter.png',
				'action_url'=> base_url('social_apps/twitter_settings'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_twitter_access,
			],
			'2'=>[
				'title'=>$this->lang->line('LinkedIn'),
				'img_path' =>$asset_path_common.'social_media/linkedin.png',
				'action_url'=> base_url('social_apps/linkedin_settings'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_linkedin_access,
			],
			'3'=>[
				'title'=>$this->lang->line('Medium'),
				'img_path' =>$asset_path_common.'social_media/medium.png',
				'action_url'=> '',
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_medium_access,
			],
			'4'=>[
				'title'=>$this->lang->line('Blogger'),
				'img_path' =>$asset_path_common.'social_media/blogger.png',
				'action_url'=> base_url('social_apps/google_settings'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_blogger_access,
			],
			'5'=>[
				'title'=>$this->lang->line('Reddit'),
				'img_path' =>$asset_path_common.'social_media/reddit.png',
				'action_url'=> base_url('social_apps/reddit_settings'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_reddit_access,
			],
			'6'=>[
				'title'=>$this->lang->line('Google'),
				'img_path' =>$asset_path_common.'social_media/google.png',
				'action_url'=> base_url('social_apps/google_settings'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_google_access,
			],
			'7'=>[
				'title'=>$this->lang->line('WordPress'),
				'img_path' =>$asset_path_common.'social_media/wp.png',
				'action_url'=> base_url('social_apps/wordpress_settings'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_wp_access,
			],
			'8'=>[
				'title'=>$this->lang->line('WordPress (self)'),
				'img_path' =>$asset_path_common.'social_media/wp.png',
				'action_url'=> base_url('social_apps/wordpress_settings_self_hosted'),
				'account_import_url' => base_url('comboposter/social_accounts'),
				'has_access'=> $has_wpSelf_access,
			],

		];
	}

	public function get_payment_apis()
	{
		$asset_path_common = base_url('assets/img/api_channel_icon/');
		return [
			'0'=>[
				'title'=>$this->lang->line('PayPal'),
				'img_path' =>$asset_path_common.'payment/paypl.png',
			],
			'1'=>[
				'title'=>$this->lang->line('Stripe'),
				'img_path' =>$asset_path_common.'payment/stripe.png',
			],
			'2'=>[
				'title'=>$this->lang->line('Mollie'),
				'img_path' =>$asset_path_common.'payment/mollie.png',
			],
			'3'=>[
				'title'=>$this->lang->line('Razorpay'),
				'img_path' =>$asset_path_common.'payment/razorpay.png',
			],
			'4'=>[
				'title'=>$this->lang->line('Paystack'),
				'img_path' =>$asset_path_common.'payment/paystack.png',
			],
			'5'=>[
				'title'=>$this->lang->line('Mercadopago'),
				'img_path' =>$asset_path_common.'payment/mercadopago.png',
			],
			'6'=>[
				'title'=>$this->lang->line('SSLCOMMERZ'),
				'img_path' =>$asset_path_common.'payment/sslcommerz.png',
			],
			'7'=>[
				'title'=>$this->lang->line('Senangpay'),
				'img_path' =>$asset_path_common.'payment/senangpay.png',
			],
			'8'=>[
				'title'=>$this->lang->line('Instamojo'),
				'img_path' =>$asset_path_common.'payment/instamojo.png',
			],
			'9'=>[
				'title'=>$this->lang->line('Toyyibpay'),
				'img_path' =>$asset_path_common.'payment/toyyibpay.png',
			],
			'10'=>[
				'title'=>$this->lang->line('Xendit'),
				'img_path' =>$asset_path_common.'payment/xendit.png',
			],
			'11'=>[
				'title'=>$this->lang->line('Myfatoorah'),
				'img_path' =>$asset_path_common.'payment/myfatoorah.png',
			],
			'12'=>[
				'title'=>$this->lang->line('Paymaya'),
				'img_path' =>$asset_path_common.'payment/paymaya.png',
			],
			'13'=>[
				'title'=>$this->lang->line('Manual'),
				'img_path' =>$asset_path_common.'payment/manualpayment.png',
			],

		];
	}

	public function get_sms_email()
	{
		$asset_path_common = base_url('assets/img/api_channel_icon/');
		return [
			'sms' => [
				'0'=>[
					'title'=>$this->lang->line('Twilio'),
					'img_path' =>$asset_path_common.'sms_email/twilio.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'1'=>[
					'title'=>$this->lang->line('Plivo'),
					'img_path' =>$asset_path_common.'sms_email/plivo.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'2'=>[
					'title'=>$this->lang->line('Clickatell'),
					'img_path' =>$asset_path_common.'sms_email/clickatell.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'3'=>[
					'title'=>$this->lang->line('Clickatell-platform'),
					'img_path' =>$asset_path_common.'sms_email/clickatell.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'4'=>[
					'title'=>$this->lang->line('Planet'),
					'img_path' =>$asset_path_common.'sms_email/planet.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'5'=>[
					'title'=>$this->lang->line('Nexmo'),
					'img_path' =>$asset_path_common.'sms_email/nexmo.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'6'=>[
					'title'=>$this->lang->line('MSG91'),
					'img_path' =>$asset_path_common.'sms_email/msg91.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'7'=>[
					'title'=>$this->lang->line('Africastalking'),
					'img_path' =>$asset_path_common.'sms_email/africastalking.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'8'=>[
					'title'=>$this->lang->line('SemySMS'),
					'img_path' =>$asset_path_common.'sms_email/semysms.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'9'=>[
					'title'=>$this->lang->line('Routesms.com'),
					'img_path' =>$asset_path_common.'sms_email/routesms.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
				'10'=>[
					'title'=>$this->lang->line('HTTP GET/POST'),
					'img_path' =>$asset_path_common.'sms_email/custom.png',
					'action_url'=> base_url('sms_email_manager/sms_api_lists')
				],
			],
			'email'=> [
				'0' => [
					'title'=>$this->lang->line('SMTP'),
					'img_path' =>$asset_path_common.'sms_email/smtp.png',
					'action_url'=> base_url('sms_email_manager/smtp_config')
				],
				'1' => [
					'title'=>$this->lang->line('Sendgrid'),
					'img_path' =>$asset_path_common.'sms_email/sendgrid.png',
					'action_url'=> base_url('sms_email_manager/sendgrid_api_config')
				],
				'2' => [
					'title'=>$this->lang->line('Mailgun'),
					'img_path' =>$asset_path_common.'sms_email/mailgun.png',
					'action_url'=> base_url('sms_email_manager/mailgun_api_config')
				],
				'3' => [
					'title'=>$this->lang->line('Mandrill'),
					'img_path' =>$asset_path_common.'sms_email/mandrill.png',
					'action_url'=> base_url('sms_email_manager/mandrill_api_config')
				],
			]
		];
	}

}