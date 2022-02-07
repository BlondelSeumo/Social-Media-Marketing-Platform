<?php require_once("Home.php"); // including home controller

class Announcement extends Home
{

    public $download_id;
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1) 
        {
            redirect('home/login_page', 'location');
        }
    }


    public function full_list()
    {
        $data['body'] = 'admin/announcement/list';
        $data['page_title'] = $this->lang->line("Announcement");     
        $this->_viewcontroller($data);
    }

    public function list_data()
    {
        $this->ajax_check();
        
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $seen_type = $this->input->post('seen_type');
        $order_by = "id DESC";
     
        $search = $this->input->post('search');

        $where_simple = array();
         
        if($this->session->userdata('user_type') != 'Admin') $where_simple["status"] = 'published';   

        if($seen_type=='1') // seen only
        $where_custom = "((user_id=".$this->user_id." AND is_seen='1') OR (user_id=0 AND FIND_IN_SET('".$this->user_id."', seen_by)))";

        else if($seen_type=='0') // unseeen
        $where_custom = "((user_id=".$this->user_id." AND is_seen='0') OR (user_id=0 AND NOT FIND_IN_SET('".$this->user_id."', seen_by)))";
        
        else // everything
        $where_custom = "((user_id=".$this->user_id.") OR (user_id=0))";

        if($search!='') $where_custom .= " AND (title like '%".$search."%' OR description like '%".$search."%' OR created_at like '%".$search."%')";
                    	     
        $where = array('where' => $where_simple);
        $this->db->where($where_custom);
 
        $table = "announcement";
        $info = $this->basic->get_data($table, $where, $select = '', $join='', $limit, $start, $order_by);
        // $sql =  $this->db->last_query();

        $action_class='hidden';
        if($this->session->userdata('user_type')=='Admin') $action_class='';       

        $html='';
        for($i=0;$i<count($info);$i++)
        {
            $seen_class = '';
            if($info[$i]["user_id"]=="0")
            {
                $seen_by_array = explode(',', $info[$i]["seen_by"]);
                if(in_array($this->user_id, $seen_by_array)) $seen_class = 'hidden';
            }
            else
            {
                if($info[$i]["is_seen"]=='1') $seen_class='hidden';    
            }

            if($info[$i]["status"]=='published') $info[$i]["status_formatted"]='<span class="badge badge-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("Published").'</span>';
            else $info[$i]["status_formatted"]='<span class="badge badge-light"><i class="fa fa-file orange"></i> '.$this->lang->line("Draft").'</span>';

            $view_url=base_url("announcement/details/".$info[$i]['id']);
            $mark_seen_url=base_url("announcement/mark_seen/".$info[$i]['id']);
            $action = "";
            $action .= '<a href="'.base_url("announcement/edit/".$info[$i]['id']).'" class="dropdown-item has-icon"><i class="fas fa-edit"></i> '.$this->lang->line("Edit").'</a>';
        	$action .= '<div class="dropdown-divider"></div><a href="'.base_url("announcement/delete/".$info[$i]['id']).'" class="dropdown-item has-icon text-danger delete_annoucement"><i class="fas fa-trash"></i> '.$this->lang->line("Delete").'</a>';
        	

        	$created_at = $info[$i]['created_at'];
	        $announcement_single = '
	        <div class="activity">
				<div class="activity-icon bg-'.$info[$i]['color_class'].' text-white shadow-'.$info[$i]['color_class'].'">
				  <i class="'.$info[$i]['icon'].'"></i>
				</div>
		        <div class="activity-detail" style="width:100%">
			      <div class="mb-2">
                    <div class="dropdown '.$action_class.'">
                      <a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h" style="font-size:25px"></i></a>
                      <div class="dropdown-menu">
                        <div class="dropdown-title">'.$this->lang->line("Options").'</div>                        
                        '.$action.'  
                      </div>
                    </div>
			        <span class="text-job gray"><i class="far fa-clock"></i> '.date_time_calculator($created_at,true).'</span>
			        <span class="bullet"></span>
			        <a class="text-job mark_seen '.$seen_class.'" href="'.$mark_seen_url.'">'.$this->lang->line("Mark Seen").'</a>
			        
			      </div>
			      <p><i class="far fa-eye"></i> <a href="'.$view_url.'">'.$info[$i]["title"].'</a></p>
			    </div>
			</div>';
		    $html.=$announcement_single;
        }
        echo json_encode(array("html"=>$html,"found"=>count($info)));        
    }

    public function add()
    {
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        $data['body'] = 'admin/announcement/add';
        $data['page_title'] = $this->lang->line("Add Announcement");     
        $this->_viewcontroller($data);
    }

    public function add_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($this->session->userdata('user_type') != 'Admin') exit();
        if(!$_POST) exit();

        $this->form_validation->set_rules('title',   '<b>'.$this->lang->line("Title").'</b>','trim|required');
        $this->form_validation->set_rules('description', '<b>'.$this->lang->line("Description").'</b>','trim|required');

        if ($this->form_validation->run() == false) 
        {
            return $this->add();
        }
        else
        {
            $this->csrf_token_check();
            $title=strip_tags($this->input->post('title',true));
            $description=strip_tags($this->input->post('description',true));
            $status=$this->input->post('status',true);
            if($status=='') $status='draft';
            $created_at=date("Y-m-d H:i:s");

            if($this->basic->insert_data('announcement',array('title'=>$title,'description'=>$description,'status'=>$status,'created_at'=>$created_at)))
            $this->session->set_flashdata('success_message',1);    
            else $this->session->set_flashdata('error_message',1);

            redirect('announcement/full_list', 'location'); 
        }         
    }

    public function edit($id=0)
    {
        if($id==0) exit();
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        $data['body'] = 'admin/announcement/edit';
        $data['page_title'] = $this->lang->line("Edit Announcement");  
        $xdata=$this->basic->get_data("announcement",array('where'=>array('id'=>$id)));   
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $this->_viewcontroller($data);
    }

    public function edit_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($this->session->userdata('user_type') != 'Admin') exit();
        if(!$_POST) exit();

        $this->form_validation->set_rules('title',   '<b>'.$this->lang->line("Title").'</b>','trim|required');
        $this->form_validation->set_rules('description', '<b>'.$this->lang->line("Description").'</b>','trim|required');

        if ($this->form_validation->run() == false) 
        {
            return $this->add();
        }
        else
        {        
            $this->csrf_token_check();
            $id=$this->input->post('hidden_id',true);
            $title=strip_tags($this->input->post('title',true));
            $description=strip_tags($this->input->post('description',true));
            $status=$this->input->post('status',true);
            if($status=='') $status='draft';
            $created_at=date("Y-m-d H:i:s");

            if($this->basic->update_data('announcement',array('id'=>$id),array('title'=>$title,'description'=>$description,'status'=>$status)))
            $this->session->set_flashdata('success_message',1);    
            else $this->session->set_flashdata('error_message',1);

            redirect('announcement/full_list', 'location'); 
        }
    }

    public function delete($id=0)
    {
        $this->ajax_check();
        if($this->is_demo == '1')
        {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("This feature is disabled in this demo."))); 
            exit();
        }
        
        if($id==0) exit();
        if($this->session->userdata('user_type') != 'Admin') exit();
        if($this->basic->delete_data("announcement",array("id"=>$id)))
        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Announcement has been deleted successfully"))); 
        else echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));

    }

    public function details($id=0)
    {
        if($id==0) exit();
        $data['body'] = 'admin/announcement/details';
        $data['page_title'] = $this->lang->line("Notification");  
        $xdata=$this->basic->get_data("announcement",array('where'=>array('id'=>$id)));   
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];

        if($xdata[0]['user_id']!='0' && $xdata[0]['user_id']!=$this->user_id && $this->session->userdata("user_type")!="Admin") exit();

        if ($xdata[0]['user_id'] != '0') 
        {
            $update_data = 
            array
            (
                'is_seen' => '1',
                'last_seen_at' => date('Y-m-d H:i:s')
            );
            $this->basic->update_data('announcement', array('id' => $id) ,$update_data);
        }
        else 
        {
            $update_data = array('last_seen_at' => date('Y-m-d H:i:s'));
            
            $temp = explode(',', $xdata[0]['seen_by']);
            array_push($temp, $this->user_id);
            $temp = array_unique($temp);
            $temp = implode(',', $temp);

            $update_data['seen_by'] = trim($temp,',');

            $this->basic->update_data('announcement', array('id' => $id) ,$update_data);
        }

       
        $this->_viewcontroller($data);
    }


    public function mark_seen($id=0)
    {

        $this->ajax_check();

        $user_id = $this->user_id;

        $info = $this->basic->get_data('announcement', array('where' => array('id' => $id)));
        if(!isset($info[0])) 
        {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("No data found")));
            exit();
        }
        $notification_info = $info[0];

        if ($notification_info['user_id'] != '0' && $notification_info['user_id']!=$user_id)
        {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("Access denied")));
            exit();
        }

        if ($notification_info['user_id'] != '0') 
        {
            $data = 
            array
            (
                'is_seen' => '1',
                'last_seen_at' => date('Y-m-d H:i:s')
            );
            $this->basic->update_data('announcement', array('id' => $id) ,$data);
        }
        else 
        {
            $data = array('last_seen_at' => date('Y-m-d H:i:s'));
            
            $temp = explode(',', $notification_info['seen_by']);
            array_push($temp, $user_id);
            $temp = array_unique($temp);
            $temp = implode(',', $temp);

            $data['seen_by'] = trim($temp,',');

            $this->basic->update_data('announcement', array('id' => $id) ,$data);
        }
        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Announcement has been marked as seen sucessfully.")));

    }



    public function mark_seen_all()
    {
        
        $this->ajax_check();

        $user_id = $this->user_id;

        $where_custom = "(user_id=".$user_id." AND is_seen='0') OR (user_id=0 AND NOT FIND_IN_SET('".$user_id."', seen_by))";
      
        $this->db->where($where_custom);
        $notification_info = $this->basic->get_data("announcement",$where='');
        
        $total=0;
        foreach ($notification_info as $notification) {
            
            $update_data = array();
            if ($notification['user_id'] == '0') {

                $temp = explode(',', $notification['seen_by']);
                array_push($temp, $user_id);
                $temp = array_unique($temp);
                $temp = implode(',', $temp);

                $update_data['seen_by'] = trim($temp,',');
            }
            else $update_data['is_seen'] = '1';

            $update_data['last_seen_at'] = date('Y-m-d H:i:s');

            $this->basic->update_data('announcement', array('id' => $notification['id']), $update_data);
            $total++;
        }
        $this->session->set_flashdata('mark_seen_success',$total." ".$this->lang->line("Unseen announcements have been marked as seen."));
        echo json_encode(array("status"=>"1","message"=>""));

    }



   

   
    
}