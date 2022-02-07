<?php
/*
Addon Name: Simple support desk
Unique Name: SimplesupportDesk
Module ID: 0
Project ID: 0
Addon URI: http://getfbinboxer.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1.0
Description: Helpdesk service for extended users
*/

require_once("application/controllers/Home.php"); // loading home controller

class Simplesupport extends Home
{
    public $addon_data=array();
    /**
     * initialize addon 
     */
    public function __construct()
    {
        parent::__construct();

        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path); 

        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        $this->load->helper('text');
        $function_name=$this->uri->segment(2);
        if($function_name!="open_ticket") 
        {          
            if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
        }if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');

    }


    public function tickets()
    {
        if($this->session->userdata('license_type') != 'double') redirect('home/access_forbidden', 'location');
        $data['body'] = 'tickets';
        $data['page_title'] = $this->lang->line("Tickets");    
        $this->_viewcontroller($data);
    }

    public function ticket_data()
    {
        $this->ajax_check();
        
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $ticket_status = $this->input->post("ticket_status",true);
        $order_by = "id DESC";
     
        $search = $this->input->post('search');

        $users = $this->basic->get_data("users");

        $user_assoc = array();
        $admin_users = array();
        foreach ($users as $key => $value) {
            $user_assoc[$value['id']] = $value;
            if($value['user_type']=="Admin") array_push($admin_users, $value['id']);
        }
        $where_simple = array();
        $where_custom = "";         

        if($this->session->userdata('user_type') == 'Member') $where_simple["fb_simple_support_desk.user_id"] = $this->user_id;   
        else $where_simple["fb_simple_support_desk.user_id >"] = 0;

        if($this->session->userdata("user_type")=="Admin")
        {
            if($ticket_status=="hidden")
            {
                $where_simple["display"] = "0";
            }
            else 
            {
                if($ticket_status!="")
                $where_simple["display"] = "1";
            }
        }
        
        if($ticket_status!='' && $ticket_status!='hidden') $where_simple["ticket_status"] = $ticket_status;


        if($search!='') $where_custom .= " (fb_simple_support_desk.id like '%".$search."%' OR ticket_title like '%".$search."%' OR category_name like '%".$search."%')";
                             
        $where = array('where' => $where_simple);
        if($where_custom!="") $this->db->where($where_custom);

        $select= array('fb_simple_support_desk.*','fb_support_category.category_name');
        $join = array('fb_support_category' => 'fb_simple_support_desk.support_category=fb_support_category.id,left');
 
        $table = "fb_simple_support_desk";
        $info = $this->basic->get_data($table, $where, $select, $join, $limit, $start, $order_by);
        // echo $sql =  $this->db->last_query();  

        $html='';
        for($i=0;$i<count($info);$i++)
        {  
            $id = $info[$i]['id'];
            $view_url= base_url()."simplesupport/reply/".$id;
            $resolve_url=base_url("simplesupport/action/resolve/".$id);
            $close_url=base_url("simplesupport/action/close/".$id);

            $logo = isset($user_assoc[$info[$i]['user_id']]['brand_logo'])?$user_assoc[$info[$i]['user_id']]['brand_logo']:"";
            if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
            else $logo=base_url().'member/'.$logo;

            $ticket_owner_name = isset($user_assoc[$info[$i]['user_id']]['name'])?$user_assoc[$info[$i]['user_id']]['name']:"";
            if($this->session->userdata("user_type")=="Admin") $ticket_owner_name = "<a href='".base_url('admin/edit_user/'.$info[$i]['user_id'])."'>".$ticket_owner_name."</a>";

            $action = ""; 

            if($info[$i]['ticket_status'] != '1')
            $action .= '<a  table_id="'.$id.'" href="" class="dropdown-item has-icon ticket_action"  data-type="open"><i class="far fa-comment"></i> '.$this->lang->line("Re-open").'</a>';

            if($info[$i]['ticket_status'] != '3')
            $action .= '<a  table_id="'.$id.'" href="" class="dropdown-item has-icon ticket_action"  data-type="resolve"><i class="fas fa-paper-plane"></i> '.$this->lang->line("Resolve").'</a>';

            if($info[$i]['ticket_status'] != '2')
            $action .= '<a  table_id="'.$id.'" href="" class="dropdown-item has-icon ticket_action"  data-type="close"><i class="fas fa-ban"></i> '.$this->lang->line("Close").'</a>';          

            if($info[$i]['display'] == '1' && $this->session->userdata("user_type")=="Admin")
            $action .= '<a  table_id="'.$id.'" href="" class="dropdown-item has-icon ticket_action"  data-type="hide"><i class="fas fa-eye-slash"></i> '.$this->lang->line("Hide").'</a>';

            $action .= '<div class="dropdown-divider"></div>';
            $action .= '<a  table_id="'.$id.'" class="dropdown-item has-icon text-danger delete_ticket"><i class="fas fa-trash"></i> '.$this->lang->line("Delete").'</a>';
           
    
            $icon = "";
            if($info[$i]['display']=="0")  $icon = "fas fa-eye-slash";
            else if($info[$i]['ticket_status']=='2') $icon = "text-danger fas fa-ban";
            else if($info[$i]['ticket_status']=='3')  $icon = "text-primary far fa-paper-plane";
            else $icon = "text-warning fas fa-ticket-alt";
            
            if($info[$i]['last_replied_by']==0)
            $reply_details = '<span class="badge badge-danger float-md-right"><i class="far fa-clock"></i> '.$this->lang->line("Reply Pending").'</span>';
            else if(in_array($info[$i]['last_replied_by'],$admin_users))
            $reply_details = '<span class="badge badge-light float-md-right"><i class="fas fa-headset"></i> '.$this->lang->line("Agent Replied").' '.date_time_calculator($info[$i]['last_replied_at'],true).'</span>';
            else 
            $reply_details = '<span class="badge badge-warning float-md-right"><i class="fas fa-user"></i> '.$this->lang->line("Client Replied").' '.date_time_calculator($info[$i]['last_replied_at'],true).'</span>';
            
            $ticket_open_time = $info[$i]["ticket_open_time"];
            $ticket_single = '
            <div class="activity">
                <div class="activity-icon bg-light" text-white shadow-light">
                  <i class="'.$icon.'"></i>
                </div>
                <div class="activity-detail h-100" style="width:100%">            
                  <div class="row align-items-center h-100">
                    <div class="col-3 col-md-1 mx-auto">
                        <img src="'.$logo.'" class="rounded-circle" style="max-width:50px">
                    </div>
                    <div class="col-9 col-md-11">
                        <div class="mb-2">
                          <div class="dropdown">
                            <a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h" style="font-size:25px"></i></a>
                            <div class="dropdown-menu">
                              <div class="dropdown-title">'.$this->lang->line("Options").'</div>                        
                              '.$action.'  
                            </div>
                          </div>
                          <span class="text-job"><i class="far fa-clock"></i> '.date_time_calculator($ticket_open_time,true).'</span>
                          <span class="bullet"></span>
                          '.$ticket_owner_name.'
                          '.$reply_details.'
                        </div>
                        <p class="text-justify"><a href="'.$view_url.'"><b>#'.$info[$i]['id'].' : </b>'.$info[$i]["ticket_title"].'</a></p>
                    </div>

                  </div>
                </div>
            </div>';
            $html.=$ticket_single;
        }
        echo json_encode(array("html"=>$html,"found"=>count($info)));        
    }



    public function open_ticket()
    {
       
        $data['body'] = 'open_ticket';
        $data['page_title'] = $this->lang->line('Open Ticket');
        $data["support_category"]=$this->basic->get_data("fb_support_category");
        $this->_viewcontroller($data);
    }


    public function open_ticket_action()
    {
        if($_POST)
        {
            
            $this->csrf_token_check();
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$this->input->post($key,true);
            }
        }if($this->session->userdata('license_type') != 'double') exit;

        $data['ticket_title'] = strip_tags($ticket_title);
        $data['ticket_text'] = $ticket_text;
        $data['user_id'] = $this->user_id;
        $data['support_category']= $support_category;
        $data['ticket_open_time']= date("Y-m-d H:i:s");
        if($this->basic->insert_data('fb_simple_support_desk',$data))
        {
    		$ticket_id = $this->db->insert_id();
    		$user_email = $this->session->userdata("user_login_email");
    		$ticket_url = base_url().'simplesupport/reply/'.$ticket_id;
    		$subject = $this->config->item('product_name')." | "."support ticket";
    		$message = "<p>Hi Admin. <br><br> 
    		<p>
    		The customer open a new ticket. <br>
    		".word_limiter($ticket_text,30)."
    		<br><br>If you want to reply this ticket, (go to ticket ID <a href='{$ticket_url}'>{$ticket_id})</a>. <br>
    		</p> 

    		<br> <br> Thanks<br><a href='".base_url()."'>".$this->session->userdata("username")."</a>";
    		$from = $user_email;
    	    $to = $this->config->item('institute_email');
    		$subject = $subject;
    		$mask = $subject;
    		$html = 1;
    		$this->_mail_sender($from, $to, $subject, $message, $mask, $html);
    		
    		if($this->session->userdata("user_type")=="Member")
    		{
    			$message = "<p>Hi ".$this->session->userdata('username').". <br><br> 
    			<p>
    			Thanks for contacting us. We have received your request (ticket ID <a href='{$ticket_url}'>{$ticket_id})</a>. <br>
    			A support representative will be reviewing your request and will send you a personal response.(usually within 24 hours). </p>

    			<br> <br> Thanks<br><a href='".base_url()."'>".$this->config->item("company")."</a> Team";
    			$from = $this->config->item('institute_email');
    			$to   = $user_email;
    			$subject = $subject;
    			$mask = $subject;
    			$html = 1;
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html);
    		}
    		$this->session->set_flashdata('success_message', 1);
    		redirect('simplesupport/tickets', 'location');
        }
       

        
      

    }

    public function delete_ticket()
    {
      $this->ajax_check();
      if($this->is_demo == '1')
      {
          echo json_encode(array("status"=>"0","message"=>"This feature is disabled in this demo.")); 
          exit();
      } 
	    $id = $this->input->post('id');

        if($this->session->userdata("user_type")=="Admin")
        $this->basic->delete_data('fb_simple_support_desk',array('id'=>$id));
	    else $this->basic->delete_data('fb_simple_support_desk',array('id'=>$id,"user_id"=>$this->user_id));
	
	    $this->basic->delete_data('fb_support_desk_reply',array('reply_id'=>$id));

        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Ticket has been deleted successfully")));
    	
    }    

    public function ticket_action()
    {
        $this->ajax_check();

        $id = $this->input->post('id');
        $action = $this->input->post('action');

        $update_data=array();
        $message = "Operation successful";

        if($action=="open") 
        {
            $update_data=array("ticket_status"=>"1","display"=>"1","last_action_at"=>date("Y-m-d H:i:s"));
            $message = "Ticket has been re-opened successfully";
        }
        if($action=="resolve") 
        {
            $update_data=array("ticket_status"=>"3","display"=>"1","last_action_at"=>date("Y-m-d H:i:s"));
            $message = "Ticket has been resolved successfully";
        }
        if($action=="close")
        {
            $update_data=array("ticket_status"=>"2","display"=>"1","last_action_at"=>date("Y-m-d H:i:s"));
            $message = "Ticket has been closed successfully";
        }
        if($action=="hide" && $this->session->userdata("user_type")=="Admin") 
        {
            $update_data=array("display"=>"0","last_action_at"=>date("Y-m-d H:i:s"));
            $message = "Ticket has been hidden successfully";
        }

        if($this->session->userdata("user_type")=="Admin")
        $this->basic->update_data('fb_simple_support_desk',array('id'=>$id),$update_data);
        else $this->basic->update_data('fb_simple_support_desk',array('id'=>$id,"user_id"=>$this->user_id),$update_data);
        
        echo json_encode(array("status"=>"1","message"=>$this->lang->line($message)));
    }

   
    public function reply($id=0)
    {
   
        if($id==0) exit();
        if($this->session->userdata('license_type') != 'double') exit;
        $data['body'] = 'ticket_reply';
        $join = array(
            'fb_support_category' => 'fb_simple_support_desk.support_category=fb_support_category.id,left'
        );
        if($this->session->userdata("user_type")=="Admin")
        $where=array('where' => array('fb_simple_support_desk.id' => $id));
        else $where=array('where' => array('fb_simple_support_desk.id' => $id,"fb_simple_support_desk.user_id"=>$this->user_id));

        $table = "fb_simple_support_desk";
        $info = $this->basic->get_data($table, $where, $select='fb_simple_support_desk.*,fb_support_category.category_name', $join);
        if(!isset($info[0])) exit();

        $data['ticket_info']=$info;

        $user=$info[0]['user_id'];
        $user_info = $this->basic->get_data('users',array('where'=>array('id'=>$user)));
        $data['user_info']=$user_info;

        $join = array('users' => 'fb_support_desk_reply.user_id=users.id,left');

        $table = "fb_support_desk_reply";
        $ticket_replied = $this->basic->get_data($table,array("where"=>array("reply_id"=>$id)),$select='',$join);
        $data['ticket_replied'] = $ticket_replied;
        $data['page_title'] = "#".$id." : ".$info[0]["ticket_title"];
        
        $this->_viewcontroller($data);
    }

    public function reply_action()
    {
       if($_POST)
       {
           
           $this->csrf_token_check();
           $post=$_POST;
           foreach ($post as $key => $value) 
           {
               $$key=$this->input->post($key,true);
           }
       }
       
       $data['ticket_reply_text']  = $ticket_reply_text;
       $data['user_id'] = $this->user_id;
       $data['reply_id'] = $id; // ticket id
       $data['ticket_reply_time'] = date("Y-m-d H:i:s");
       
       if($this->basic->insert_data('fb_support_desk_reply',$data))
       {
       		if($this->session->userdata("user_type")=="Member")
       		{
       			$id= $id; 
       			$url = site_url()."simplesupport/reply/".$id;
       			$url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
       			$message = "<p>"."The customer has responded to the ticket"."</p>
       			            </br>
       			            </br>
       			            <p>".'Hi'." ".'Admin'.", </p>
       			            </br>
       			            </br>
       			            <p>".word_limiter($data['ticket_reply_text'],50)." </p>
       			            </br>
       			            </br>
       			            <p>"."Go to this url".":".$url_final."</p>";


       			$from = $this->session->userdata("user_login_email");
       			$to = $this->config->item('institute_email');
       			$subject = $this->config->item('product_name')." | "."support ticket";
       			$mask = $subject;
       			$html = 1;
       			$this->_mail_sender($from, $to, $subject, $message, $mask, $html);
       			$update_ticket = array("last_replied_at"=>date("Y-m-d H:i:s"),"last_replied_by"=>$this->user_id,"ticket_status"=>"1","display"=>"1");
       			$this->basic->update_data('fb_simple_support_desk',array("id"=>$id),$update_ticket);
       			$this->session->set_flashdata('success_message', 1);
       			redirect('simplesupport/reply/'.$id.'', 'location'); 
       		}
       		else
       		{
       			$id= $id; 
       			$url = site_url()."simplesupport/reply/".$id;
       			$url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
       			$message = "<p>"."Admin has responded to your ticket"."</p>
       			            </br>
       			            </br>
       			            <p>".'Hi'." ".'Customer'.", </p>
       			            </br>
       			            </br>
       			            <p>".word_limiter($data['ticket_reply_text'],50)." </p>
       			            </br>
       			            </br>
       			            <p>"."Go to this url".":".$url_final."</p>";

				$where=array('where' => array('fb_simple_support_desk.id' => $id));
				$data_support_desk= $this->basic->get_data('fb_simple_support_desk',$where);
				
				$userid=$data_support_desk[0]['user_id'];
				$where=array('where' => array('users.id' => $userid ));
				$table = "users";
                $from = $this->config->item('institute_email'); 
				$select =array("users.email");
				$tomail = $this->basic->get_data($table,$where,$select);
				if(isset($tomail[0]['email']))
				$to = $tomail[0]['email']; 
				$subject = $this->config->item('product_name')." | "."Support ticket";
				$mask = $subject;
				$html = 1;
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html);
				$update_ticket = array("last_replied_at"=>date("Y-m-d H:i:s"),"last_replied_by"=>$this->user_id);
				$this->basic->update_data('fb_simple_support_desk',array("id"=>$id),$update_ticket);
				$this->session->set_flashdata('success_message', 1);
				redirect('simplesupport/reply/'.$id.'', 'location');              
       		}
       }

    }


    public function support_category_manager()
    {
     if ($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
     $data['body'] = 'support_category';
     $data['page_title'] = $this->lang->line('Support Category');
     $data['category_data'] = $this->basic->get_data("fb_support_category");
     $this->_viewcontroller($data);

    }

    public function add_category()
    {       
        if ($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        $data['body'] = 'add_category';
        $data['page_title'] = $this->lang->line('Add Category');
        $this->_viewcontroller($data);
    }
    
    public function add_category_action()
    {
       if ($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
       if($this->is_demo == '1')
       {
           echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
           exit();
       }

       if($_SERVER['REQUEST_METHOD'] === 'GET') 
       redirect('home/access_forbidden','location');

       if($_POST)
       {
           $this->form_validation->set_rules('category_name', '<b>'.$this->lang->line("Category Name").'</b>', 'trim|required');      
               
           if ($this->form_validation->run() == FALSE)
           {
               $this->add_category(); 
           }
           else
           {               
               $category_name=$this->input->post('category_name');               
                                                      
               $data=array
               (
                   'category_name'=>$category_name,
                   'deleted'=>'0'
               );
               
               if($this->basic->insert_data('fb_support_category',$data)) $this->session->set_flashdata('success_message',1);   
               else $this->session->set_flashdata('error_message',1);     
               
               redirect('simplesupport/support_category_manager','location');                 
               
           }
       }  

    }

    public function edit_category($id=0)
    {       
        if ($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
   
        $data['body']='edit_category';     
        $data['page_title']=$this->lang->line('Edit Category');     
        $xdata=$this->basic->get_data('fb_support_category',array("where"=>array("id"=>$id)));
        if(!isset($xdata[0])) exit();
        $data['xdata'] = $xdata[0];
        $this->_viewcontroller($data);
    }


    public function edit_category_action() 
    {
        if ($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'GET') 
        redirect('home/access_forbidden','location');

        if($_POST)
        {
            $id = $this->input->post('id');
            $this->form_validation->set_rules('category_name', '<b>'.$this->lang->line("Category Name").'</b>', 'trim|required');      
                
            if ($this->form_validation->run() == FALSE)
            {
                $this->edit_category($id); 
            }
            else
            {               
                $category_name=$this->input->post('category_name');               
                                                       
                $data=array
                (
                    'category_name'=>$category_name
                );
                
                if($this->basic->update_data('fb_support_category',array("id"=>$id),$data)) $this->session->set_flashdata('success_message',1);   
                else $this->session->set_flashdata('error_message',1);     
                
                redirect('simplesupport/support_category_manager','location');                 
                
            }
        }   
    }   


    public function delete_category($id=0)
    {
        $this->ajax_check();
        if($this->is_demo == '1')
        {
            echo json_encode(array("status"=>"0","message"=>"This feature is disabled in this demo.")); 
            exit();
        }
        if ($this->session->userdata('user_type') != 'Admin') exit(); 
        if($this->basic->update_data('fb_support_category',array('id'=>$id),array("deleted"=>"1")))
        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Category has been deleted successfully"))); 
        else echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));
      
    }




}