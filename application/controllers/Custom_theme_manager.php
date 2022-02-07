<?php 
require_once("Home.php"); // including home controller

class Custom_theme_manager extends Home
{
  
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }

        $this->important_feature();
    }


    public function index()
    {
        $this->lists();
    }


    public function lists()
    {
        $data['page_title'] = $this->lang->line("Theme Manager");
        $data['body'] = 'admin/add_ons/list';
        $data['add_on_list'] = $this->add_on_list();
        $this->_viewcontroller($data);    
    }


    protected function add_on_list()
    {
        $myDir = APPPATH.'modules';
        $file_list = $this->_scanAll($myDir);
        $one_list_array=array();
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array[] = explode("/",$one_list[$i]);
        }   
        $final_list_array=array();  
        foreach ($one_list_array as $value) 
        {
            // getting folder name only [ex: bengali], G:/xampp/htdocs/fbinboxer3/application/modules/moduleName/controllers/moduleName.php
            $pos=count($value)-1; // addonController.php
            $pos2=count($value)-2; // controllers folder
            $pos3=count($value)-3;  // modules folder

            if($value[$pos3]=='ultrapost') continue;
            if($value[$pos3]=='simplesupport') continue;

            if($value[$pos2]!="controllers") continue; // only getting controllers

            $lang_folder=$value[$pos3].'/'.$value[$pos2].'/'.$value[$pos];
            $final_list_array[$value[$pos3]] = $lang_folder;
        }
        $final_array = array_unique($final_list_array);
  
        $addon_data=array();
        foreach($final_array as $key => $value) 
        {
            $path=APPPATH.'modules/'.$value;
            $addon_data[$key]=$this->get_addon_data($path); // inside home.php
        }
        return $addon_data;
    }

    public function upload()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        $data['page_title'] = $this->lang->line("Install Add-on");
        $data['body'] = 'admin/add_ons/upload';
        $this->_viewcontroller($data);  
    }


    public function upload_addon_zip()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload/addon";
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0755, true);
        }
        if (isset($_FILES["myfile"])) 
        {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="addon_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;


            $allow=".zip";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;

            $zip = new ZipArchive;
            if ($zip->open($output_dir.'/'.$filename) === TRUE) 
            {
                $addon_path=FCPATH."application/modules/";
                $zip->extractTo($addon_path);
                $zip->close();
                @unlink($output_dir.'/'.$filename);
                $this->session->set_flashdata('addon_uplod_success',$this->lang->line('add-on has been uploaded successfully. you can activate it from here.'));
            } 
            echo json_encode($filename);
        }
    }

  

   
}
