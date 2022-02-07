<?php

require_once("Home.php"); // including home controller

/**
* class admin_config
* @category controller
*/
class Myprofile extends Home
{
    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login_page', 'location');
        }
   }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->edit_profile();
    }

 
    public function edit_profile()
    {      
        $data['body'] = "member/edit_profile";
        $data['page_title'] = $this->lang->line('Profile');
        $join = array('package'=>"users.package_id=package.id,left");
        $data["profile_info"]=$this->basic->get_data("users",array("where"=>array("users.id"=>$this->session->userdata("user_id"))),"users.*,package_name",$join);
        $data["time_zone_list"] = $this->_time_zone_list();
        $this->_viewcontroller($data);
    }

    public function edit_profile_action()
    {
        if($this->is_demo == '1' && $this->session->userdata('user_type') == 'Admin')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>Permission denied</h2>"; 
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
            // validation
            $this->form_validation->set_rules('name',                '<b>'.$this->lang->line("name").'</b>',             'trim|required');
            $this->form_validation->set_rules('email',               '<b>'.$this->lang->line("email").'</b>',            'trim|required|valid_email|callback_unique_email_check['.$this->session->userdata('user_id').']');
            $this->form_validation->set_rules('address',             '<b>'.$this->lang->line("address").'</b>',          'trim');
            $this->form_validation->set_rules('time_zone',             '<b>'.$this->lang->line("Time Zone").'</b>',          'trim');
            
            if ($this->form_validation->run() == false) 
            {
                return $this->edit_profile();
            } 
            else 
            {
                // assign
                $this->csrf_token_check();
                $name=addslashes(strip_tags($this->input->post('name', true)));
                $email=addslashes(strip_tags($this->input->post('email', true)));
                $address=addslashes(strip_tags($this->input->post('address', true)));
                $time_zone=addslashes(strip_tags($this->input->post('time_zone', true)));
                $base_path=FCPATH . 'member';
                if(!file_exists($base_path)) mkdir($base_path,0755);

                $this->load->library('upload');

                $photo="";
                if ($_FILES['logo']['size'] != 0) {
                    $photo = $this->session->userdata("user_id").".png";
                    $config = array(
                        "allowed_types" => "png",
                        "upload_path" => $base_path,
                        "overwrite" => true,
                        "file_name" => $photo,
                        'max_size' => '200',
                        'max_width' => '500',
                        'max_height' => '500'
                        );
                    $this->upload->initialize($config);
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('logo')) {
                        $this->session->set_userdata('logo_error', $this->upload->display_errors());
                        return $this->edit_profile();
                    }
                }

                $update_data=array
                (
                    "name"=>$name,
                    "email"=>$email,
                    "address"=>$address,
                    "time_zone"=>$time_zone
                );

                if($photo!="") $update_data["brand_logo"] = $photo;
 
                $this->basic->update_data("users",array("id"=>$this->session->userdata("user_id")),$update_data);
                     
                $this->session->set_flashdata('success_message', 1);
                redirect('myprofile/edit_profile', 'location');
            }
        }
    }

    function unique_email_check($str, $edited_id)
    {
        $email= strip_tags(trim($this->input->post('email',TRUE)));
        if($email==""){
            $s= $this->lang->line("required");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
        }
        
        if(!isset($edited_id) || !$edited_id)
            $where=array("email"=>$email);
        else        
            $where=array("email"=>$email,"id !="=>$edited_id);
        
        
        $is_unique=$this->basic->is_unique("users",$where,$select='');
        
        if (!$is_unique) {
            $s = $this->lang->line("is_unique");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
            }
                
        return TRUE;
    }

}
