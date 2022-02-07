<?php
require_once("Home.php"); // including home controller
/**
* class config
* @category controller
*/


class Search_tools extends Home
{
    public $user_id;
    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');
        if($this->session->userdata('user_type') != 'Admin' && !in_array(267,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->user_id=$this->session->userdata('user_id');
    
        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();     
    }


    public function index()
    {
      $data['body'] = 'utility/menu_block';
      $data['page_title'] = $this->lang->line('Search Tools');
      $this->load->library('Fb_rx_login');
      $this->_viewcontroller($data);
    }

    public function comparision()
    {

        $data['body'] = 'utility/comparison_checker';
        $data['page_title'] = $this->lang->line('Website Comparison');
        $this->_viewcontroller($data);

    }


    public function comparison_action()
    {
        $this->ajax_check();
        $this->load->library('Fb_rx_login');
        $status=$this->_check_usage($module_id=267,$request=1);
        if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded. you can no longer use this feature.")));
            exit();
        }
        $crnt_access_token_id = $this->session->userdata('facebook_rx_fb_user_info');
        $access_token_data = $this->basic->get_data('facebook_rx_fb_user_info',array("where"=>array("id"=>$crnt_access_token_id)));
        $access_token ='';
        if (isset($access_token_data[0])) {
           
           if (isset($access_token_data[0]['access_token'])) {
                
                $access_token = $access_token_data[0]['access_token'];
                
           }
        }
       
       $url1 = $this->input->post('url1', true);
       $url2 = $this->input->post('url2', true);
       $output = array();
       if ($url1 != '') 
       {
           
           
           $existency_data = $this->fb_rx_login->fb_like_comment_share($url1,$access_token);
           
           //echo "<pre>";print_r($existency_data);exit;
           $date_time = isset($existency_data['updated_time']) ? $existency_data['updated_time'] : "";
           $date_time = strtotime($date_time);
           if ($date_time !='') 
              $update_time = date('F j, Y, g:i a', $date_time);
           else
              $update_time = '';
           $description = isset($existency_data['description']) ? $existency_data['description'] : "";
           // if(strlen($description)>30)
           //    $des = '...';
           // else
           //    $des = "";
        
          if(isset($existency_data['errormessage']))
          {
            $output["output1"] = '<div class="card card-statistic-2 red">
                <div class="card-stats">
                  <div class="card-stats-title"> '.$existency_data['errormessage'].' 
                  </div>

                </div>


              </div>';
          }
          else
          {
            $total_share = isset($existency_data['total_share']) ? $existency_data['total_share'] : '0';
            $total_reaction = isset($existency_data['total_reaction']) ? $existency_data['total_reaction'] :'0';
            $total_comment = isset($existency_data['total_comment']) ? $existency_data['total_comment'] : '0';
            $title = isset($existency_data['title']) ? $existency_data['title'] : "";
            $type = isset($existency_data['type']) ? $existency_data['type'] : "";
            $output["output1"] = '<div class="card card-statistic-2">
                     <div class="card-stats">
                       <div class="card-stats-title"> '.$this->lang->line("Website Report").' 
                       </div>
                       <div class="card-stats-items">
                        <div class="card-stats-item">
                           <div class="card-stats-item-count">'.custom_number_format($total_share).'</div>
                           <div class="card-stats-item-label">'.$this->lang->line('Share').'</div>
                         </div>
                         <div class="card-stats-item">
                           <div class="card-stats-item-count">'.custom_number_format($total_reaction).'</div>
                           <div class="card-stats-item-label">'.$this->lang->line('Reaction').'</div>
                         </div>
                         <div class="card-stats-item">
                           <div class="card-stats-item-count">'.custom_number_format($total_comment).'</div>
                           <div class="card-stats-item-label">'.$this->lang->line('Comment').'</div>
                         </div>
                       </div>
                     </div>
                     <div class="card-icon shadow-primary bg-primary">
                       <i class="fas fa fa-link"></i>
                     </div>
                     <div class="card-wrap">
                       <div class="card-header">
                         <h4>'.$title.'</h4>
                       </div>
                       <div class="card-body">
                         <p style="font-size:15px;"> '.$type.' </p>
                       </div>
                     </div>
                   </div>

                   <li class="media">
                     <div class="media-body">
                       <div class="card card-statistic-1">
                         <div class="card-icon bg-primary">
                           <i class="fas fa fa-clock-o"></i>
                         </div>
                         <div class="card-wrap">
                           <div class="card-header">
                             <h4>'.$this->lang->line('Updated Time').'</h4>
                           </div>
                           <div class="card-body"><p style="font-size:13px;">'.$update_time.'</p></div>
                         </div>
                       </div>
                     </div>
                   </li>


                   <li class="media">
                     <div class="media-body">
                       <div class="card card-statistic-1">
                         <div class="card-icon bg-primary">
                           <i class="fas fa-align-justify"></i>
                         </div>
                         <div class="card-wrap">
                           <div class="card-header">
                             <h4>'.$this->lang->line('Description').'</h4>
                           </div>
                           <div class="card-body"><p class="description_tooltip" style="font-size:13px;overflow:auto;" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$description.'">'.$description.'</p></div>
                         </div>
                       </div>
                     </div>
                   </li>
               
                 ';
            
          }



            
       }
       else
       {

           $output["empty"] = 'empty';
          

       }

       

       if($url2 != '')
       {
           
            $existency_data2 = $this->fb_rx_login->fb_like_comment_share($url2,$access_token);

            $date_time2 =  isset($existency_data2['updated_time']) ? $existency_data2['updated_time'] : "";
            $date_time2 = strtotime($date_time2);
            if ($date_time2 !='') 
               $update_time2 = date('F j, Y, g:i a',$date_time2);
            else
               $update_time2 = '';
            $description2 = isset($existency_data2['description']) ? $existency_data2['description'] : "";
            // if(strlen($description2)>30)
            //    $des2 = '...';
            // else
            //    $des2 = "";

             if(isset($existency_data2['errormessage']))
             {
                $output["output2"] = '<div class="card card-statistic-2 red">
                    <div class="card-stats">
                      <div class="card-stats-title"> '.$existency_data2['errormessage'].' 
                      </div>

                    </div>


                  </div>';
             }
             else
             {
              $total_share2 = isset($existency_data2['total_share']) ? $existency_data2['total_share'] : '0';
              $total_reaction2 = isset($existency_data2['total_reaction']) ? $existency_data2['total_reaction'] : '0';
              $total_comment2 =  isset($existency_data2['total_comment']) ? $existency_data2['total_comment'] : '0';
              $title2 = isset($existency_data2['title']) ? $existency_data2['title'] : '';
              $type2 = isset($existency_data2['type']) ? $existency_data2['type'] : '';

              $output["output2"] = '<div class="card card-statistic-2">
                                      <div class="card-stats">
                                        <div class="card-stats-title"> '.$this->lang->line("Competitor Website Report").' 
                                        </div>
                                        <div class="card-stats-items">
                                          <div class="card-stats-item">
                                            <div class="card-stats-item-count">'.custom_number_format($total_share2).'</div>
                                            <div class="card-stats-item-label">'.$this->lang->line('Share').'</div>
                                          </div>
                                          <div class="card-stats-item">
                                            <div class="card-stats-item-count">'.custom_number_format($total_reaction2).'</div>
                                            <div class="card-stats-item-label">'.$this->lang->line('Reaction').'</div>
                                          </div>
                                          <div class="card-stats-item">
                                            <div class="card-stats-item-count">'.custom_number_format($total_comment2).'</div>
                                            <div class="card-stats-item-label">'.$this->lang->line('Comment').'</div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="card-icon shadow-primary bg-primary">
                                        <i class="fas fa fa-link"></i>
                                      </div>
                                      <div class="card-wrap">
                                        <div class="card-header">
                                          <h4>'.$title2.'</h4>
                                        </div>
                                        <div class="card-body">
                                          <p style="font-size:15px;">'.$type2.' </p>
                                        </div>
                                      </div>
                                    </div>
                                    <li class="media">
                                      <div class="media-body">
                                        <div class="card card-statistic-1">
                                          <div class="card-icon bg-primary">
                                            <i class="fas fa fa-clock-o"></i>
                                          </div>
                                          <div class="card-wrap">
                                            <div class="card-header">
                                              <h4>'.$this->lang->line('Updated Time').'</h4>
                                            </div>
                                            <div class="card-body"><p style="font-size:13px;">'.$update_time2.'</p></div>
                                          </div>
                                        </div>
                                      </div>
                                    </li>

                                    <li class="media">
                                      <div class="media-body">
                                        <div class="card card-statistic-1">
                                          <div class="card-icon bg-primary">
                                            <i class="fas fa-align-justify"></i>
                                          </div>
                                          <div class="card-wrap">
                                            <div class="card-header">
                                              <h4>'.$this->lang->line('Description').'</h4>
                                            </div>
                                            <div class="card-body"><p style="font-size:13px;overflow:auto;" class="description_tooltipp" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$description2.'">'.$description2.'</p></div>
                                          </div>
                                        </div>
                                      </div>
                                    </li>
                                 
                                    ';
              
             }



             
       }
       else
       {
            
           $output["empty1"] = 'empty1';
           
       }
       $this->_insert_usage_log($module_id=267,$request=1);
       echo json_encode($output);


    }

    public function place_search()
    {

      $data['body'] = 'utility/place_search';
      $data['page_title'] = $this->lang->line('Place Search');
      $this->_viewcontroller($data);


    }

    public function place_search_action()
    {

      $this->ajax_check();
      $status=$this->_check_usage($module_id=267,$request=1);
      if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
      { 
          echo "<div class='col-12 col-sm-6 col-md-6 col-lg-12' id='nodata'>
                         <div class='empty-state'>
                              <h2 class='mt-0 text-center red'> ".$this->lang->line("limit has been exceeded. you can no longer use this feature.")."</h2>

                              
                            </div>
                         
                        </div>";
            exit;
      }
      $keyword = $this->input->post('keyword', true);
      if($keyword == ""){
        echo "Empty keyword"; exit;
      }
      $search_limit = $this->input->post('limit', true);
      $distance = $this->input->post('distance', true);
      if($distance == ""){
        $distance = '1000';
      }
      $latitude = $this->input->post('latitude', true);
      if ($latitude == ""){
        echo "Empty latitude"; exit;
      }
      $longitude = $this->input->post('longitude', true);
      if($longitude == ""){
        echo "Empty longitude"; exit;
      }

      $output = array();
      $this->load->library('Fb_rx_login');

      $crnt_access_token_id = $this->session->userdata('facebook_rx_fb_user_info');
      $access_token_data = $this->basic->get_data('facebook_rx_fb_user_info',array("where"=>array("id"=>$crnt_access_token_id)));
      $access_token ='';
      if (isset($access_token_data[0])) {
         
         if (isset($access_token_data[0]['access_token'])) {
              
              $access_token = $access_token_data[0]['access_token'];
              
         }
      }
      

      $search_results = $this->fb_rx_login->location_search($access_token,$keyword,$latitude,$longitude,$distance,$search_limit);
 
     
      if(isset($search_results['error_message']))
      { 
        $output= "<div class='col-12 col-sm-6 col-md-6 col-lg-12' id='nodata'>
                       <div class='empty-state'>
                            <img class='img-fluid' style='height: 200px' src='".base_url('assets/img/drawkit/drawkit-nature-man-colour.svg')."' alt='image'>
                            <h2 class='mt-0 text-center red'> ".$search_results['error_message']."</h2>
                            
                          </div>
                       
                      </div>";
      }
      else
      {
        $search_result = isset($search_results['data']['0']) ? $search_results['data']['0'] : 0 ;

        $total = $search_results['total_found'];
        if (count($search_result)>0) {


           
           $output = '

              <script>
                  $(document).ready(function() {

                      $(".about_tool").tooltip();
                      $(".see_details_modal").tooltip();
                      

                  });
              </script>

           ';

           $output .="<div class='card'>
                    <div class='card-header'>
                      <h4> <i class='fas fa-search-location'></i> ".$this->lang->line("Search Results")."</h4>
                      <div class='card-header-action'>
                        <div class='badges'>
                          <span class='badge badge-primary'>".$total."</span>
                        </div>                    
                      </div>
                    </div>
                  </div>
                  <div class='row' id='video_lists' style='margin-left: 10px; margin-right: 10px;margin-top: 20px'>";
              $i=0;
            foreach ($search_result as $value) {
                 
                $name = isset($value['name']) ? $value['name'] : '';
                $star_rating = isset($value['overall_star_rating']) ? $value['overall_star_rating'] : 0; 
                $website = isset($value['website']) ? "<a href=".addhttp($value["website"])." style='display:block;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;max-width: 180px;' target='_blank' >".addhttp($value["website"])."</a>" : 'N/A';
                $about = isset($value['about']) ? $value['about'] : '';
                $checkins = isset($value['checkins']) ? $value['checkins'] : 0;
                $cover_photo = isset($value['cover']['source']) ? $value['cover']['source'] : base_url('assets/img/news/img06.jpg');
                $likes = isset($value['engagement']['count']) ? $value['engagement']['count'] : 0;
                $is_always_open = isset($value['is_always_open']) ? $value['is_always_open'] : 0;
                $is_permanently_closed = isset($value['is_permanently_closed']) ? $value['is_permanently_closed'] : 0;
                $payment_options_amex = isset($value['payment_options']['amex']) ? $value['payment_options']['amex'] : 0;
                $payment_options_cash_only = isset($value['payment_options']['cash_only']) ? $value['payment_options']['cash_only'] : 0;
                $payment_options_discover = isset($value['payment_options']['discover']) ? $value['payment_options']['discover'] : 0;
                $payment_options_mastercard = isset($value['payment_options']['mastercard']) ? $value['payment_options']['mastercard'] : 0;
                $payment_options_visa = isset($value['payment_options']['visa']) ? $value['payment_options']['visa'] : 0;
                $price = '';
                $price_range = isset($value['price_range']);
                if (strlen($price_range) == 1) {
                  $price = "menu are inexpensive";
                }
                if(strlen($price_range) == 2){
                  $price = "menu are moderately priced";
                }
                if(strlen($price_range == 3)){
                  $price = "menu are expensive";
                }
                $rating_count = isset($value['rating_count']) ? $value['rating_count'] : 0;
                $restaurant_services_delivery = isset($value['restaurant_services']['delivery']) ? $value['restaurant_services']['delivery'] : 0;
                $restaurant_services_catering = isset($value['restaurant_services']['catering']) ? $value['restaurant_services']['catering'] : 0;
                $restaurant_services_groups = isset($value['restaurant_services']['groups']) ? $value['restaurant_services']['groups'] : 0;
                $restaurant_services_kids = isset($value['restaurant_services']['kids']) ? $value['restaurant_services']['kids'] : 0;
                $restaurant_services_outdoor = isset($value['restaurant_services']['outdoor']) ? $value['restaurant_services']['outdoor'] : 0;
                $restaurant_services_reserve = isset($value['restaurant_services']['reserve']) ? $value['restaurant_services']['reserve'] : 0;
                $restaurant_services_takeout = isset($value['restaurant_services']['takeout']) ? $value['restaurant_services']['takeout'] : 0;
                $restaurant_services_waiter = isset($value['restaurant_services']['waiter']) ? $value['restaurant_services']['waiter'] : 0;
                $restaurant_services_walkins = isset($value['restaurant_services']['walkins']) ? $value['restaurant_services']['walkins'] : 0;
                $is_verified = isset($value['is_verified']) ? $value['is_verified'] : 0;
                $location_city = isset($value['location']['city']) ? $value['location']['city'] : '';
                $location_country = isset($value['location']['country']) ? $value['location']['country'] : '';
                $location_latitude = isset($value['location']['latitude']) ? $value['location']['latitude'] : '';
                $location_longitude = isset($value['location']['longitude']) ? $value['location']['longitude'] : '';
                $location_street = isset($value['location']['street']) ? $value['location']['street'] : '';
                $location_zip = isset($value['location']['zip']) ? $value['location']['zip'] : 'N/A';
                $location_state = isset($value['location']['state']) ? $value['location']['state'] : '';
                $link = isset($value['link']) ? $value['link'] : '';
                $phone_no = isset($value['phone']) ? $value['phone'] : 'N/A';

                $category_list = $value['category_list'];
                $category_name = array();
                if(count($category_list)>0){

                  foreach ($category_list as $catagory_value) {
                    
                    $category_name[] = isset($catagory_value['name']) ? $catagory_value['name'] : '';

                  }
                }
               $cate_name = implode(', ', $category_name);

               $hours_open_close = isset($value['hours']) ? $value['hours'] : 0;
               $hours_open = array();
               $hours_close = array();
               if($hours_open_close>0){
                foreach ($hours_open_close as  $open_close_value) {
                  
                   $hours_open[] = isset($open_close_value['key']) ? $open_close_value['key'] : '';
                   $hours_close[] = isset($open_close_value['value']) ? $open_close_value['value'] : '';
                }

               }


               if(strlen($name)>20) 
                $dot= '...'; 
               else 
                $dot= "";

               if(strlen($about)>25)
                $des = '...';
               else
                $des = "";



                  $output .= "
                       
                       <div class='col-12 col-sm-6 col-md-6 col-lg-4 samsu".$i." '>
                           <article class='article profile-widget'>
                             <div class='article-header details see_details_modal' style='cursor:pointer' data-toggle='tooltip' data-placement='top' data-original-title='".$this->lang->line("See details about this place")."'>

                               <div class='article-image' data-background='{$cover_photo}' style='background-image: url({$cover_photo});'>
                                  <a href='{$link}'></a>
                                  <textarea style='visibility:hidden;'>".json_encode($value)."</textarea>
                               </div>
                               <div class='check_box_background text-center'>
                                 <div class='profile-widget-item'>

                                 </div>

                              </div>


                  ";

                  $output .="
                                <div class='video_option_background'>
                                  
                                  </div>

                                  <div class='article-title'>
                                    <h2 title='".$name."' style='color: white;'><a target='_blank' href='{$link}'>".substr($name, 0, 20).$dot."</a></h2>
                                    <div class='check_box_background text-center' style='top:8px;'>
                                      <div class='profile-widget-item' style='color: white;'>

                                      </div>

                                    </div>
                                  </div>
                                </div>
                            ";


                            $output .="
                                    <div class='article-details' style='padding: 0;'>
                                      <p style='cursor:pointer;' data-toggle='tooltip' data-placement='top' data-original-title='".$about."' class='description_info about_tool'>".substr($about, 0, 20).$des."</p>
                                       
                                       
                                        <ul class='list-unstyled' style='padding-left:6px;'>
                                          <li class='media'>
                                           
                                              <img class='mr-2 rounded' title=".$this->lang->line('location')." width='20' src=".base_url('assets/img/products/product-1-60.png')." alt='product'>
                             
                                            <div class='media-body'>
                                              <div class='text-muted text-small'>{$location_country} <div class='bullet'></div> {$location_city}</div>
                                              <div class='media-title' style='display: inline-grid;overflow: hidden;padding-right: 15px;'>Zip Code: {$location_zip}</div>
                                            </div>
                                          </li>
                                        <li class='media'>
                                         
                                            <img class='mr-2 rounded' title=".$this->lang->line('Phone')." width='20' src=".base_url('assets/img/products/product-3-50.png')." alt='product'>
                                      
                                          <div class='media-body'>
                                           
                                            <div class='media-title'>{$phone_no} </div>
                                            <div class='text-muted text-small' title='Categories'>{$cate_name}
                                          </div>
                                        </div>
                                        </li>
                                        <li class='media'>
                                            <img class='mr-2 rounded' title=".$this->lang->line('Website')." width='20' src=".base_url('assets/img/products/product-4-50.png')." alt='product'>
                                            <div class='media-body'>
                                             
                                              <div  class='media-title'>{$website}</div>
                                              
                                            </div>
                                            
                                        </li>
                                        </ul>
                                      <div class='profile-widget-items'>

                                        <div class='profile-widget-item'>
                                          <div class='profile-widget-item-label'> <i style='color:var(--blue);' title=".$this->lang->line('Likes')." class='fa fa-thumbs-up'></i> </div>
                                          <div class='profile-widget-item-value'>".custom_number_format($likes)."</div>
                                        </div>
                                        <div class='profile-widget-item'>
                                          <div class='profile-widget-item-label'> <i style='color:#e7711b;' title=".$this->lang->line('Star')." class='fa fa-star-o'></i> </div>
                                          <div class='profile-widget-item-value'>".custom_number_format($star_rating)."</div>
                                        </div>
                                        <div class='profile-widget-item'>
                                          <div class='profile-widget-item-label'><i style='color:#f71905;' title=".$this->lang->line('Checkins')." class='fa fa-map-marker'></i></div>
                                          <div class='profile-widget-item-value'>".custom_number_format($checkins)."</div>
                                        </div>
                                      </div>

                                    </div>
                                  </article>
                                </div>";
              $output .= "<script>
                            ScrollReveal().reveal('.samsu".$i."',{ delay: 300});
                          </script>"; 
              $i++;
            }
             $output .="</div>";
             $this->_insert_usage_log($module_id=267,$request=1);

        }
        else{
           $output= "<div class='col-12 col-sm-6 col-md-6 col-lg-12' id='nodata'>
                       <div class='empty-state'>
                            <img class='img-fluid' style='height: 200px' src='".base_url('assets/img/drawkit/drawkit-nature-man-colour.svg')."' alt='image'>
                            <h2 class='mt-0'> ".$this->lang->line('We could not find any data.')."</h2>
                            <a href='' class='btn btn-outline-primary mt-4'><i class='fa fa-search'></i> ".$this->lang->line('Try Once Again')."</a>
                          </div>
                       
                      </div>";
        }
      }
      

      $page_encoding =  mb_detect_encoding($output);
      if(isset($page_encoding)){
        $output = @iconv( $page_encoding, "utf-8//IGNORE", $output );
      } 
      echo $output;

    }


}