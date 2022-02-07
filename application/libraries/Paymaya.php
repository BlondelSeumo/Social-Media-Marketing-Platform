      <?php

      class Paymaya{

      public $paymaya_public_key;
      public $paymaya_secret_key;
      public $purpose;
      public $amount;
      public $currency;
      public $phone;
      public $buyer_name;
      public $email;
      public $success_url;
      public $failure_url;
      public $cancel_url;
      public $paymaya_mode;
      public $button_lang;
      public $paymaya_api_url;
      public $payment_id;
      public $payment_request_id;
      public $base_64;
      public $checkoutid;
      public $secret_key_base64;
      public $redirect_url;


      function __construct(){

      $this->CI =& get_instance();

      $query = $this->CI->db->get('payment_config');
      $config_data = $query->result_array();

      $this->paymaya_public_key =isset($config_data[0]['paymaya_public_key'])?$config_data[0]['paymaya_public_key']:"";
      $this->paymaya_secret_key =isset($config_data[0]['paymaya_secret_key'])?$config_data[0]['paymaya_secret_key']:"";
      $this->paymaya_mode =isset($config_data[0]['paymaya_mode'])?$config_data[0]['paymaya_mode']:"live";
      $this->base_64 = base64_encode($this->paymaya_public_key.":".$this->paymaya_secret_key);
      $this->secret_key_base64 = base64_encode($this->paymaya_secret_key);

      if($this->paymaya_mode=='sandbox') $this->paymaya_api_url="https://pg-sandbox.paymaya.com/checkout/v1/checkouts";
      else $this->paymaya_api_url="https://pg.paymaya.com/checkout/v1/checkouts";
      }

      function set_button(){

      $button_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay with Paymaya");

      $button = "
      <a href='".$this->redirect_url."' class='list-group-item list-group-item-action flex-column align-items-start'>
      <div class='d-flex w-100 align-items-center'>
      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/paymaya.png')."'></small>
      <h6 class='mb-1'>".$button_lang."</h6>
      </div>
      </a>";

      return $button;

      }


      public function checkout_url(){

        $post_data = [
          'totalAmount' => [
            'value' => $this->amount,
            'currency' => 'PHP',
            'details' => [
              'subtotal' => $this->amount,
            ],
          ],
          'items' => [
            0 => [
              'name' => $this->purpose,
              'totalAmount' => [
                'value' => $this->amount,
              ],
            ],
          ],
          'redirectUrl' => [
            'success' => $this->success_url,
            'failure' => $this->failure_url,
            'cancel' => $this->cancel_url,
          ],
          'requestReferenceNumber' => ' ',
        ];

      $json_en = json_encode($post_data);

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_URL, $this->paymaya_api_url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $json_en);
      $headers = array(
      'Authorization: Basic '. $this->base_64,
      'Content-Type:application/json');
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($curl);

      $info = curl_getinfo($curl);
      curl_close($curl);
      return $response = json_decode($result,true);
    
      }


      public function get_checkoutid($checkout_id){

      $url = 'https://pg-sandbox.paymaya.com/checkout/v1/checkouts/'.$checkout_id;
      // echo $this->secret_key_base64;
      // exit();
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $headers = array(
      'Content-Type:application/json',
      'Authorization: Basic '.$this->secret_key_base64);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($curl);

      $response = json_decode($result, true);
       return $response;

      }

      }

      ?>