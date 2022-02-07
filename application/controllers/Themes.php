<?php 
require_once("Home.php"); // including home controller

class Themes extends Home
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
        $data['body'] = 'admin/theme_manager/list';
        $data['theme_list'] = $this->theme_list();
        $this->_viewcontroller($data);    
    }


    public function theme_list()
    {
        $myDir = APPPATH.'views/site';
        $file_list = $this->_scanFolder($myDir);
        $one_list_array=array();

        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array[] = explode("/",$one_list[$i]);
        }   
        $final_list_array=array();  

        $i=0;
        foreach ($one_list_array as $value) 
        {
            $pos=count($value)-1; // addonController.php

            $folder_name = $value[$pos];
            $path=APPPATH.'views/site/'.$folder_name;
            $addon_data[$i]=$this->get_theme_data($path."/index.php"); // inside home.php
            $thumb_path = 'application/views/site/'.$folder_name.'/thumb.png';
            if(file_exists($thumb_path))
                $addon_data[$i]['thumb']='application/views/site/'.$folder_name.'/thumb.png';
            else
                $addon_data[$i]['thumb']='';
            $addon_data[$i]['folder_name'] = $folder_name;
            $i++;
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

        $data['page_title'] = $this->lang->line("Install Theme");
        $data['body'] = 'admin/theme_manager/upload';
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
        $output_dir = FCPATH."upload/themes";
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
                $addon_path=FCPATH."application/views/site/";
                $zip->extractTo($addon_path);
                $zip->close();
                @unlink($output_dir.'/'.$filename);
                $this->session->set_flashdata('theme_upload_success',$this->lang->line('Theme has been uploaded successfully. you can activate it from here.'));
            } 
            echo json_encode($filename);
        }
    }

    public function _scanFolder($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new IteratorIterator($di) as $filename) {
            if ($filename->isDir()) 
            {
                $dir = str_replace($myDir, '', dirname($filename));
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
        }
        return $dirTree;
    }

    public function active_deactive_theme()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' || $this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }
        $response = [];
        $folder_name = $this->input->post('folder_name',true);
        $active_or_deactive = $this->input->post('active_or_deactive',true);
        $response['status'] = '1';

        include('application/config/my_config.php');
        if($active_or_deactive == 'active')
        {
            $config['current_theme'] = $folder_name;
            $response['message'] = $this->lang->line('Theme has been activated successfully.');
        }
        else
        {
            $config['current_theme'] = 'modern';
            $response['message'] = $this->lang->line('Theme has been deactivated successfully.');
        }

        file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');

        echo json_encode($response);
    }

    public function delete_theme()
    {
        $this->ajax_check();
        $response = [];
        $folder_name = $this->input->post('folder_name',true);
        if($folder_name == 'new_default')
        {
            $response['status'] = '0';
            $response['message'] = $this->lang->line('You can not delete the default theme.');
            echo json_encode($response);
            exit();            
        }
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        $path = "application/views/site/".$folder_name;
        $this->delete_directory($path);
        $response['status'] = '1';
        $response['message'] = $this->lang->line('Theme has been deleted successfully.');

        if($folder_name == $this->config->item('current_theme'))
        {
            include('application/config/my_config.php');
            $config['current_theme'] = 'modern';
            file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
        }


        echo json_encode($response);

    }

  

   
}
