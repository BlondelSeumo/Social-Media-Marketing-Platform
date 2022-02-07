<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('mec_display_price'))
{
  function mec_display_price($original_price=0, $sell_price=0, $currency_icon = '$',$retun_type='1',$currency_position='left',$decimal_point=0,$thousand_comma='0') //$retun_type=1 means price overthrough, $retun_type=2 means purchase price, $retun_type=3 means disount, $retun_type=4 menas discount formatted
  {
    $ci = &get_instance();

    $currency_left = $currency_right = "";
    if($currency_position=='left') $currency_left = $currency_icon;
    if($currency_position=='right') $currency_right = $currency_icon;

    if($retun_type=='1')
    {
      if($sell_price>0 && ($sell_price<$original_price)) 
      {
        $return = "<span class='text-light' style='text-decoration:line-through'>".$currency_left.mec_number_format($original_price,$decimal_point,$thousand_comma).$currency_right."</span> <span class='text-dark'>".$currency_left.mec_number_format($sell_price,$decimal_point,$thousand_comma).$currency_right."</span>";
      }
      else $return = $currency_left.mec_number_format($original_price,$decimal_point,$thousand_comma).$currency_right;
    }
    else if($retun_type=='2')
    {
      if($sell_price>0 && ($sell_price<$original_price)) 
      {
        $return = mec_number_format($sell_price,$decimal_point,$thousand_comma);
      }
      else $return = mec_number_format($original_price,$decimal_point,$thousand_comma);
    }
    else
    {
      $disocunt = 0;
      if($sell_price>0 && ($sell_price<$original_price)) 
      {
        $disocunt = round((($original_price-$sell_price)/$original_price)*100);
        
        if($retun_type==4) $return = '<div class="yith-wcbsl-badge-wrapper yith-wcbsl-mini-badge"> <div class="yith-wcbsl-badge-content">-'.$disocunt.'%</div></div>';
        else $return = $disocunt;
      }
      else
      {
        if($retun_type==4) $return = '';
        else $return = 0;
      }

    }

    return $return;
  }
}

if ( ! function_exists('mec_attribute_map'))
{
  function mec_attribute_map($attribute_array=array(),$attribute_str='',$retun_type='string') // makes comma seperated attributes as name string (1,2 = Color,Size)
  {
    $explode = explode(',', $attribute_str);

    $output = array();
    foreach ($explode as $value) 
    {
      if(isset($attribute_array[$value])) $output[] = $attribute_array[$value];
    }
    if($retun_type=='string') return ucfirst(strtolower(implode(' , ', $output)));
    else return $output;
  }
}


if ( ! function_exists('mec_number_format'))
{
  function mec_number_format($number,$decimal_point=0,$thousand_comma='0')
  {
      $decimal_point_count = strlen(substr(strrchr($number, "."), 1));
      if($decimal_point_count>0 && $decimal_point==0) $decimal_point = $decimal_point_count; // if setup no deciaml place but the number is naturally float, we can not just skip it

      if($decimal_point>2) $decimal_point=2;

      $number = (float)$number;
      $comma = $thousand_comma=='1' ? ',' : '';
      return number_format($number, $decimal_point,'.',$comma);
  }
}

if ( ! function_exists('mec_add_get_param'))
{
  function mec_add_get_param($url="",$param=array())
  {
    if($url=="") return "";
    if(empty($param)) return $url; 
    $final_url = $url;
    foreach ($param as $key => $value)
    {
       if($key=="" || $value=="") continue;
       if(strpos($final_url, '?') !== false) $final_url.="&".$key."=".$value;
       else $final_url.="?".$key."=".$value;
    }
    return $final_url;
  }
}


if ( ! function_exists('mec_sticky_footer'))
{
  function mec_sticky_footer($store_data=array(),$subscriber_id='',$current_cart=array(),$ecommerce_config=array())
  {
    $ci = &get_instance();
    $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
    $current_cart_id = isset($current_cart['cart_id']) ? $current_cart['cart_id'] : 0;
    $cart_count = isset($current_cart['cart_count']) ? $current_cart['cart_count'] : 0;
    $current_cart_url = base_url("ecommerce/cart/".$current_cart_id);
    $current_cart_url = mec_add_get_param($current_cart_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
    $provider_mapping = base_url("ecommerce/store/".$store_data['store_unique_id']);
    $provider_mapping = mec_add_get_param($provider_mapping,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));  
    $href=$terms=$refund='';    
    if($subscriber_id!="" && $current_cart_id!=0) $href = 'href="'.$current_cart_url.'"';
    $store_id = isset($store_data['store_id']) ? $store_data['store_id'] : $store_data['id'];
    $hide_add_to_cart = isset($ecommerce_config['hide_add_to_cart']) ? $ecommerce_config['hide_add_to_cart'] : '0';
    $hide_buy_now = isset($ecommerce_config['hide_buy_now']) ? $ecommerce_config['hide_buy_now'] : '0';

    $purchase_off = $hide_add_to_cart=='1' && $hide_buy_now =='1' ? true : false;
    
    $my_orders_link = base_url("ecommerce/my_orders/".$store_id);
    $my_orders_link = mec_add_get_param($my_orders_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
    
    $store_link = base_url("ecommerce/store/".$store_data['store_unique_id']);
    $store_link = mec_add_get_param($store_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
    // $store_name_logo = ($store_data['store_favicon']!='') ? '<img alt="'.$store_data['store_name'].'" class="rounded-circle" style="width:29px;height:29px;margin-top:1px;" src="'.base_url("upload/ecommerce/".$store_data['store_favicon']).'">' : '';
    // if(empty($store_name_logo)) 
    $store_name_logo = '<i class="lnr lnr-home"></i>';
    $first_menu = !empty($store_name_logo) ? '<li class="breadcrumb-item"><a href="'.$store_link.'">'.$store_name_logo.' <div>'.$ci->lang->line("Home").'</div></a></li>' : '';

    $my_orders_menu = $more_menu = $login_menu = '';

    if(!$purchase_off) $login_menu  = '<li class="breadcrumb-item"><a href="" class="pointer" id="login_form"><i class="lnr lnr-enter"></i> <div>'.$ci->lang->line("Login").'</div></a></li>';

    if($subscriber_id!="")
    {
      if(!$purchase_off)
      {
        $my_orders_menu = '<li class="breadcrumb-item"><a href="'.$my_orders_link.'" class=""><i class="lnr lnr-printer"></i> <div>'.$ci->lang->line("Orders").'</div></a></li>';
        $more_menu = '<li class="breadcrumb-item"><a class="text-primary pointer" id="sidebarCollapse"><i class="lnr lnr-menu"></i> <div>'.$ci->lang->line("More").'</div></a></li>';
      }
      $login_menu = '';
    }

    $hide_cart = $purchase_off ? 'd-none' : '';

    $ret =  '
    <div style="height: 90px"></div>
    <nav aria-label="breadcrumb" class="m-0 w-100 d-print-none" id="sticky-footer">
      <ol class="breadcrumb m-0 justify-content-center bg-white">
        <li class="breadcrumb-item" ><a href="javascript:history.back(1);" class="text-center"><i class="lnr lnr-chevron-left-circle"></i> <div>'.$ci->lang->line("Back").'</div></a></li>
       '.$first_menu.'
        <li class="breadcrumb-item '.$hide_cart.'"><a '.$href.' class=""><i class="lnr lnr-cart"></i> <div>'.$ci->lang->line("Cart").' <span id="cart_count_display" class="badge badge-primary rounded" style="padding:2px 4px;">'.$cart_count.'</span></div></a></li>
        '.$my_orders_menu.'
        '.$more_menu.'
        '.$login_menu.'
      </ol>
    </nav>';

    return $ret;
      
  }
}

if ( ! function_exists('mec_sidebar'))
{
  function mec_sidebar($store_data=array(),$subscriber_id='',$current_cart=array())
  {
    $ci = &get_instance();
    $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
    $current_cart_id = isset($current_cart['cart_id']) ? $current_cart['cart_id'] : 0;
    $cart_count = isset($current_cart['cart_count']) ? $current_cart['cart_count'] : 0;
    $current_cart_url = base_url("ecommerce/cart/".$current_cart_id);
    $current_cart_url = mec_add_get_param($current_cart_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
    $provider_mapping = base_url("ecommerce/store/".$store_data['store_unique_id']);
    $provider_mapping = mec_add_get_param($provider_mapping,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
    $footer_copyright = "<a href='".$provider_mapping."'>".$store_data['store_name']."</a>";
    $footer_terms_use_link = $store_data['terms_use_link'];
    $footer_refund_link = $store_data['refund_policy_link'];
    $href=$terms=$refund='';    
    if($subscriber_id!="" && $current_cart_id!=0) $href = 'href="'.$current_cart_url.'"';
    $store_id = isset($store_data['store_id']) ? $store_data['store_id'] : $store_data['id'];
    
    $my_orders_link = base_url("ecommerce/my_orders/".$store_id);
    $my_orders_link = mec_add_get_param($my_orders_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

    $contact = '<a href="#" data-toggle="modal" data-target="#contactModal"><i class="fas fa-paper-plane"></i> '.$ci->lang->line("Contact").'</a>';
    
    if(isset($footer_terms_use_link) && !empty($footer_terms_use_link)) $terms = '<a href="#" class="text-dark" data-toggle="modal" data-target="#TermsModal"><i class="lnr lnr-license"></i>'.$ci->lang->line("Terms of Use").'</a>';

    if(isset($footer_refund_link) && !empty($footer_refund_link)) $refund = '<a href="#" class="text-dark" data-toggle="modal" data-target="#RefundModal"><i class="lnr lnr-book"></i>'.$ci->lang->line("Refund Policy").'</a>';
    
    $store_link = base_url("ecommerce/store/".$store_data['store_unique_id']);
    $store_link = mec_add_get_param($store_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
    $store_name_logo = ($store_data['store_logo']!='') ? '<img alt="'.$store_data['store_name'].'" class="img-fluid" style="width:120px;" src="'.base_url("upload/ecommerce/".$store_data['store_logo']).'">' : '';
    $first_menu = !empty($store_name_logo) ? '<a class="p-0" href="'.$store_link.'">'.$store_name_logo.'</a>' : $store_data['store_name'];

    $login_menu = $logout_menu = '';
    if($subscriber_id=="")
    $login_menu  = '<li class="list-group-item d-flex justify-content-between align-items-center"><a href="" class="pointer" id="login_form"><i class="fas fa-sign-in-alt"></i> '.$ci->lang->line("Login").'</a></li>';

    if($ci->session->userdata($store_id."ecom_session_subscriber_id")!='')
    $logout_menu  = '<li class="list-group-item d-flex justify-content-between align-items-center"><a href="" class="pointer" id="logout"><i class="lnr lnr-exit"></i> '.$ci->lang->line("Logout").'</a></li>';

    $ret =  '

    <nav id="sidebar" class="nicescroll bg-white">
        <div class="sidebar-header bg-white">
            <h6 class="text-primary m-0" style="line-height:0">'.$first_menu.'</h6>            
        </div>

        <ul class="list-group list-group-flush m-0 mt-4 p-0">
            <li id="showProfile" class="list-group-item d-flex justify-content-between align-items-center"><a href="" class=""><i class="lnr lnr-user"></i>'.$ci->lang->line("Profile").'</a></li>
            <li id="showAddress" data-close="0" class="list-group-item d-flex justify-content-between align-items-center"><a href="" class=""><i class="lnr lnr-map-marker"></i>'.$ci->lang->line("Delivery Address").'</a></li>
            <li class="list-group-item d-flex justify-content-between align-items-center"><a href="'.$my_orders_link.'" class=""><i class="lnr lnr-printer"></i>'.$ci->lang->line("My Orders").'</a></li>
            '.$login_menu.'
            '.$logout_menu.'
            <li data-close="0" class="list-group-item d-flex justify-content-between align-items-center">'.$terms.'</a></li>
            <li data-close="0" class="list-group-item d-flex justify-content-between align-items-center">'.$refund.'</a></li>
        </ul>            
    </nav>';

    return $ret;
      
  }
}


if ( ! function_exists('mec_display_rating_starts'))
{
  function mec_display_rating_starts($rating_point=0,$class='')
  {
    if($rating_point<1) return "";
    $ret="";
    $loop=0;
    for($i=1;$i<=$rating_point;$i++)
    {
      $loop++;
      $ret.='<i class="fa fa-star orange '.$class.'"></i>';
    }
    $start_bank = 5-$loop;
    if($start_bank>0)
    for($i=1;$i<=$start_bank;$i++)
    {
      $ret.='<i class="fa fa-star text-muted '.$class.'"></i>';
    }
    return $ret;
  }
}

if ( ! function_exists('mec_display_rating'))
{
  function mec_display_rating($rating_point=0,$review_count=0)
  {     
     if($rating_point==0 || $review_count==0) return "";
     if($review_count<3) return "";
     $value=$rating_point/$review_count;

     if($value>5) $value=5;

     $return="";
     if(is_integer($value))
     {
       for ($i=1; $i <=$value ; $i++) 
       { 
         $return.= "<i class='fa fa-star orange'></i>";
       }
     }
     else
     {
        $exp=explode('.', $value);
        $before=$exp[0];
        $after='.'.round($exp[1],1); //.35 => .4, .34=>.3

        for ($i=1; $i <=$before ; $i++) 
        { 
         $return.= "<i class='fa fa-star orange'></i>";
        }

        if($after>.20 && $after<=.7) $return.= "<i class='fa fa-star-half-alt orange'></i>";
        else if($after>.7 && $after<=1) $return.= "<i class='fa fa-star orange'></i>";

     }
     return $return;
     
  }
}

if ( ! function_exists('mec_average_rating'))
{
  function mec_average_rating($rating_point=0,$review_count=0)
  {     
     if($rating_point==0 || $review_count==0) return "";
     if($review_count<3) return "";
     $value=$rating_point/$review_count;     
     return round($value,2);
     
  }
}
