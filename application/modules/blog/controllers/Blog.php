<?php
/*
Addon Name: Blog
Unique Name: blog
Module ID: 0
Project ID: 0
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: Front end blog page creation for admin user only
*/

require_once("application/controllers/Home.php"); // loading home controller

class Blog extends Home
{
	public $editor_allowed_tags;
    public function __construct()
    {
        parent::__construct();
        $function_name=$this->uri->segment(2);
        $lv1 = array('category','category_data','category_store','category_update','category_delete','tag','tag_data','tag_store','tag_update','tag_delete','posts','post_data','add_post','add_post_action','edit_post','edit_post_action','delete_post','upload_post_thumbnail','delete_post_thumbnail','comment_delete');
        $lv2 = array('comment_action');
        if(file_exists(APPPATH.'core/licence_type.txt'))$this->license_check_action();        
        if(in_array($function_name, $lv1)){
            if ($this->session->userdata('logged_in') != 1 || $this->session->userdata('user_type') != 'Admin'){ 
                redirect('home/login_page', 'location');exit();
            }if($this->session->userdata('license_type') != 'double'){redirect('home/access_forbidden', 'location');exit;}
        }if(in_array($function_name, $lv2)){
            if ($this->session->userdata('logged_in') != 1){ 
                redirect('home/login_page', 'location');exit();
            }
        }
        $this->editor_allowed_tags = '<h1><h2><h3><h4><h5><h6><a><b><strong><p><i><div><span><ul><li><ol><blockquote><code><table><tr><td><th><img><iframe>';
    }

    function slug($string) {
        $slug = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        // replace non letter or digits by -
        $slug = preg_replace('~[^\\pL\d]+~u', '-', $slug);
        // trim
        $slug = trim($slug, '-');
        return strtolower($slug);
    }

	public function category(){
		$data['body'] = 'blog_category';
        $data['page_title'] = $this->lang->line("Category Manager");
        $this->_viewcontroller($data);
	}

	public function category_data()
    {           
        if(!$_POST) exit();

        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','name','created_at','updated_at','actions');
        $search_columns = array('name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
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
            
        $table="blog_post_categories";
        $join = array();
        $select= array();
        $info=$this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {
            $info[$i]["created_at"] = date("jS M y",strtotime($info[$i]["created_at"]));

            if (!empty($info[$i]["updated_at"]))
             	$info[$i]["updated_at"] = date("jS M y",strtotime($info[$i]["updated_at"]));
            else
            	$info[$i]["updated_at"] = '';

            $str="";   
            
            $str=$str."<a class='btn btn-circle btn-outline-warning edit_category' data-toggle='tooltip' data-id='{$info[$i]["id"]}' data-name='{$info[$i]["name"]}' title='".$this->lang->line('Edit')."' href='javascript:void(0)'>".'<i class="fas fa-edit"></i>'."</a>";
            $str=$str."&nbsp;<a href='javascript:void(0)' class='delete_category btn btn-circle btn-outline-danger' data-id='{$info[$i]["id"]}' data-toggle='tooltip' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
             
            $info[$i]["actions"] = "<div style='min-width:95px'>".$str."</div>";

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function category_lists(){
        $data = [''=>'--'.$this->lang->line('Select Category').'--'];
        $categories = $this->basic->get_data('blog_post_categories');
        foreach ($categories as $category) {
            $data[$category['id']] = $category['name'];
        }
        return $data;
    }

    public function category_store(){
        if(!$_POST) exit();

        $this->ajax_check();

        $this->form_validation->set_rules('name', 'name', 'required|trim|is_unique[blog_post_categories.name]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=>$this->form_validation->error_array()
            ]);
            exit;
        }

        $data = array(
          'name' => strip_tags($this->input->post('name', true)),
          'slug' => $this->slug(strip_tags($this->input->post('name', true))),
          'created_at' => date('Y-m-d H:i:s'),
        );

        if($this->basic->insert_data('blog_post_categories', $data)) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Category has been created successfully."),
          ]);
          exit;
        } else {
          echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function category_update(){
        if(!$_POST) exit();

        $this->ajax_check();
        $id = $this->input->post('category_id', true);

        $unique_name = "blog_post_categories.name.".$id;
        $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Name").'</b>', "trim|required|is_unique[$unique_name]");


        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=>$this->form_validation->error_array()
            ]);
            exit;
        }

        $data = array(
          'name' => $this->input->post('name', true),
          'slug' => $this->slug(strip_tags($this->input->post('name', true))),
          'updated_at' => date('Y-m-d H:i:s'),
        );

        if($this->basic->update_data('blog_post_categories', array('id' => $id), $data)) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Category has been updated successfully."),
          ]);
          exit;
        } else {
          echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function category_delete(){
        if(!$_POST) exit();

        $this->ajax_check();
        $id = $this->input->post('category_id', true);
        if ($this->basic->delete_data('blog_post_categories', array("id" => $id))) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Category has been deleted successfully."),
          ]);
          exit;       
        }else{
          echo json_encode([
            'status'=> 0,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function tag(){
        $data['body'] = 'blog_tag';
        $data['page_title'] = $this->lang->line("Tag Manager");
        $this->_viewcontroller($data);
    }

    public function tag_data()
    {   
        if(!$_POST) exit();

        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','name','created_at','updated_at','actions');
        $search_columns = array('name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
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
            
        $table="blog_post_tags";
        $join = array();
        $select= array();
        $info=$this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {
            $info[$i]["created_at"] = date("jS M y",strtotime($info[$i]["created_at"]));

            if (!empty($info[$i]["updated_at"]))
                $info[$i]["updated_at"] = date("jS M y",strtotime($info[$i]["updated_at"]));
            else
                $info[$i]["updated_at"] = '';

            $str="";   
            
            $str=$str."<a class='btn btn-circle btn-outline-warning edit_tag' data-toggle='tooltip' data-id='{$info[$i]["id"]}' data-name='{$info[$i]["name"]}' title='".$this->lang->line('Edit')."' href='javascript:void(0)'>".'<i class="fas fa-edit"></i>'."</a>";
            $str=$str."&nbsp;<a href='javascript:void(0)' class='delete_tag btn btn-circle btn-outline-danger' data-id='{$info[$i]["id"]}' data-toggle='tooltip' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
             
            $info[$i]["actions"] = "<div style='min-width:95px'>".$str."</div>";

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function tag_lists(){
        // $data = [''=>'--'.$this->lang->line('Select Tags').'--'];
        $data = [];
        $tags = $this->basic->get_data('blog_post_tags');
        foreach ($tags as $tag) {
            $data[$tag['name']] = $tag['name'];
        }
        return $data;
    }

    public function tag_store(){
        if(!$_POST) exit();

        $this->ajax_check();
        $this->form_validation->set_rules('name', 'name', 'required|trim|is_unique[blog_post_tags.name]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=>$this->form_validation->error_array()
            ]);
            exit;
        }

        $data = array(
          'name' => strip_tags($this->input->post('name', true)),
          'created_at' => date('Y-m-d H:i:s'),
        );

        if($this->basic->insert_data('blog_post_tags', $data)) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Tag has been created successfully."),
          ]);
          exit;
        } else {
          echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function tag_update(){
        if(!$_POST) exit();

        $this->ajax_check();
        $id = $this->input->post('tag_id', true);
        
        $unique_name = "blog_post_tags.name.".$id;
        $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Name").'</b>', "trim|required|is_unique[$unique_name]");

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=>$this->form_validation->error_array()
            ]);
            exit;
        }

        $data = array(
          'name' => strip_tags($this->input->post('name', true)),
          'updated_at' => date('Y-m-d H:i:s'),
        );

        if($this->basic->update_data('blog_post_tags', array('id' => $id), $data)) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Tag has been updated successfully."),
          ]);
          exit;
        } else {
          echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function tag_delete(){
        if(!$_POST) exit();

        $this->ajax_check();
        $id = $this->input->post('tag_id', true);
        if ($this->basic->delete_data('blog_post_tags', array("id" => $id))) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Tag has been deleted successfully."),
          ]);
          exit;
        }else{
          echo json_encode([
            'status'=> 0,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    // Post List Page
    public function posts(){
        $data['body'] = 'blog_post';
        $data['page_title'] = $this->lang->line("Blog Manager");
        $this->_viewcontroller($data);
    }

    public function post_data(){
        if(!$_POST) exit();

        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','title','category','author','status','created_at','published_at','actions');
        $search_columns = array('title');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
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
            
        $table="blog_posts";

        $join = array(
            'users'=>"blog_posts.user_id=users.id,left",
            'blog_post_categories'=>"blog_posts.category_id=blog_post_categories.id,left",
        );
        $select= array(
            "blog_posts.id",
            "blog_posts.slug",
            "blog_posts.title",
            "blog_posts.status",
            "blog_posts.created_at",
            "blog_posts.published_at",
            "users.name as author",
            "blog_post_categories.name as category"
        );

        $info=$this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {

            $info[$i]["title"] = mb_substr($info[$i]["title"], 0, 50).'.........';
            $info[$i]["category"] = $info[$i]["category"];
            $info[$i]["author"] = $info[$i]["author"];
            $info[$i]["created_at"] = date("jS M y",strtotime($info[$i]["created_at"]));

            if (!empty($info[$i]["updated_at"]))
                $info[$i]["published_at"] = date("jS M y",strtotime($info[$i]["updated_at"]));
            else
                $info[$i]["published_at"] = '';

            if ($info[$i]["status"] == '1')
                $info[$i]["status"] = '<div class="badge badge-success">'.$this->lang->line('Published').'</div>';
            elseif($info[$i]["status"] == '0')
                $info[$i]["status"] = '<div class="badge badge-danger">'.$this->lang->line('Draft').'</div>';
            else
                $info[$i]["status"] = '<div class="badge badge-warning">'.$this->lang->line('Pending').'</div>';

            $single_url  = base_url("blog/post_details/".$info[$i]["slug"].'/'.$info[$i]["id"]);

            $str="";   
            
            $str=$str."<a class='btn btn-circle btn-outline-warning edit_tag' data-toggle='tooltip' data-id='{$info[$i]["id"]}' data-title='{$info[$i]["title"]}' title='".$this->lang->line('Edit')."' href='".$base_url.'blog/edit_post/'.$info[$i]["id"]."'>".'<i class="fas fa-edit"></i>'."</a>";
            $str=$str."&nbsp;<a href='javascript:void(0)' class='delete_post btn btn-circle btn-outline-danger' data-id='{$info[$i]["id"]}' data-toggle='tooltip' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $str=$str."&nbsp;<a href='".$single_url."' target='_BLANK' class='btn btn-circle btn-outline-info' data-toggle='tooltip' title='".$this->lang->line('Visit')."'>".'<i class="fa fa-eye"></i>'."</a>";
             
            $info[$i]["actions"] = "<div style='min-width:95px'>".$str."</div>";

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function add_post(){
        if ($this->session->userdata('logged_in') != 1 || $this->session->userdata('user_type') != 'Admin' || $this->session->userdata('license_type') != 'double') 
        { 
            redirect('home/login_page', 'location');exit();
        }
        $data['body'] = 'blog_add_post';
        $data['page_title'] = $this->lang->line("Add Post");
        $data['category_lists'] = $this->category_lists();
        $data['tag_lists'] = $this->tag_lists();
        $this->_viewcontroller($data);
    }

    public function add_post_action(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST') 
            redirect('home/access_forbidden','location');

        $this->form_validation->set_rules('title', $this->lang->line("Title"), 'required|trim');
        $this->form_validation->set_rules('category_id', $this->lang->line("Category"), 'required|trim');
        $this->form_validation->set_rules('body', $this->lang->line("Body"), 'required|trim');
        $this->form_validation->set_rules('tags[]', $this->lang->line("Tags"), 'required|trim');
        $this->form_validation->set_rules('status', $this->lang->line("Status"), 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=>$this->form_validation->error_array()
            ]);
            exit;
        }

        if ($this->input->post('status', true) == '1')
            $published_at = date('Y-m-d H:i:s');
        else
            $published_at = null;

        $data = array(
            'title' => strip_tags($this->input->post('title', true)),
            'keywords' => strip_tags($this->input->post('keywords', true)),
            'slug' => strip_tags($this->slug($this->input->post('title', true))),
            'category_id' => strip_tags($this->input->post('category_id', true)),
            'body' => strip_tags($this->input->post('body'),$this->editor_allowed_tags),
            'thumbnail' => strip_tags($this->input->post('thumbnail', true)),
            'tags' => strip_tags(implode(',', $this->input->post('tags', true))),
            'status' => strip_tags($this->input->post('status', true)),
            'published_at' => $published_at,
            'user_id' => $this->user_id,
            'created_at' => date('Y-m-d H:i:s'),
        );

        if($this->basic->insert_data('blog_posts', $data)) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Post has been created successfully."),
          ]);
          exit;
        } else {
          echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function edit_post($id){
        if ($this->session->userdata('logged_in') != 1 || $this->session->userdata('user_type') != 'Admin' || $this->session->userdata('license_type') != 'double') 
        { 
            redirect('home/login_page', 'location');exit();
        }
        $data['body'] = 'blog_edit_post';
        $data['page_title'] = $this->lang->line("Edit Post");
        $data['category_lists'] = $this->category_lists();
        $data['tag_lists'] = $this->tag_lists();
        $data['post'] = $this->basic->get_data('blog_posts',array('where' => array("id" => $id)));
        $this->_viewcontroller($data);
    }

    public function edit_post_action($id){

        if($_SERVER['REQUEST_METHOD'] !== 'POST') 
            redirect('home/access_forbidden','location');

        $this->form_validation->set_rules('title', $this->lang->line("Title"), 'required|trim');
        $this->form_validation->set_rules('category_id', $this->lang->line("Category"), 'required|trim');
        $this->form_validation->set_rules('body', $this->lang->line("Body"), 'required|trim');
        $this->form_validation->set_rules('tags[]', $this->lang->line("Tags"), 'required|trim');
        $this->form_validation->set_rules('status', $this->lang->line("Status"), 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=>$this->form_validation->error_array()
            ]);
            exit;
        }

        $post = $this->basic->get_data('blog_posts',array('where' => array("id" => $id)));

        if ($this->input->post('status', true) == '1' && $post[0]['status'] != '1')
            $published_at = date('Y-m-d H:i:s');
        else
            $published_at = $post[0]['published_at'];

        $data = array(
            'title' => strip_tags($this->input->post('title', true)),
            'keywords' => strip_tags($this->input->post('keywords', true)),
            'slug' => strip_tags($this->slug($this->input->post('title', true))),
            'category_id' => strip_tags($this->input->post('category_id', true)),
            'body' => strip_tags($this->input->post('body'),$this->editor_allowed_tags),
            'thumbnail' => strip_tags($this->input->post('thumbnail', true)),
            'tags' => strip_tags(implode(',', $this->input->post('tags', true))),
            'status' => strip_tags($this->input->post('status', true)),
            'published_at' => $published_at,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if($this->basic->update_data('blog_posts', array('id' => $post[0]['id']), $data)) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Post has been updated successfully."),
          ]);
          exit;
        } else {
          echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function delete_post(){
        if(!$_POST) exit();

        $this->ajax_check();
        $id = $this->input->post('post_id', true);

        // Delete Thumbnail
        $post = $this->basic->get_data('blog_posts',array('where' => array("id" => $id)));
        if (!empty($post[0]['thumbnail'])) {
            $output_dir = FCPATH."upload/blog/";
            $filePath = $output_dir.$post[0]['thumbnail'];
            if (file_exists($filePath))
            {
                unlink($filePath);
            }
        }
        // Delete Thumbnail

        if ($this->basic->delete_data('blog_posts', array("id" => $id))) {
            echo json_encode([
                'status'=> 1,
                'message'=> $this->lang->line("Post has been deleted successfully."),
            ]);
            exit;
        }else{
            echo json_encode([
                'status'=> 0,
                'message'=> $this->lang->line("Something Went Wrong, please try once again."),
            ]);
            exit;
        }
    }

    public function upload_post_thumbnail()
    {
        $this->ajax_check();

        $ret = array();
        $output_dir = FCPATH."upload/blog";

        if(!file_exists($output_dir))
        {
            mkdir($output_dir,0777);
        }

        if (isset($_FILES["file"])) {

            $error = $_FILES["file"]["error"];

            $post_fileName = $_FILES["file"]["name"];
            $post_fileName_array = explode(".", $post_fileName);
            $ext = array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename=$this->user_id."_"."blog"."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            $allow = ".jpeg,.jpg,.png,.gif";
            $allow = str_replace('.', '', $allow);
            $allow = explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Bad request");
                exit;
            }

            move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function delete_post_thumbnail()
    {
        $this->ajax_check();

        $output_dir = FCPATH."upload/blog/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
            $fileName = $_POST['name'];
            $fileName = str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
            $filePath = $output_dir. $fileName;
            if (file_exists($filePath))
            {
                unlink($filePath);
            }
        } else {
            $fileName = $this->input->post("fileName",true);
            if(file_exists($output_dir.$fileName)){
                unlink($output_dir.$fileName);
            }
        }

        if (isset($_POST['post_id']) && $_POST['post_id'] > 0) {
            $this->basic->update_data('blog_posts', array('id' => $_POST['post_id']), [
                'thumbnail' => NULL,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function _sidebars_data(){
        $data["categories"] = $this->basic->get_data("blog_post_categories",$where = array(),[
            "blog_post_categories.id",
            "blog_post_categories.name",
            "blog_post_categories.slug",
            "(select count(blog_posts.id) from blog_posts where blog_posts.status='1' AND blog_posts.category_id = blog_post_categories.id) as total_posts",
        ]);

        $data["tags"] = $this->basic->get_data("blog_post_tags",$where = array(),[
            "blog_post_tags.id",
            "blog_post_tags.name",
        ]);

        return $data;
    }

    // Front View 
    public function _blog_viewcontroller($data=array())
    {
        if (!isset($data['body']))   $data['body'] = $this->config->item('default_page_url');
        if (!isset($data['page_title'])) $data['page_title']="";

        $loadthemebody="purple";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');
        
        $themecolorcode="#545096";

        if($loadthemebody=='blue')         { $themecolorcode="#1193D4";}
        if($loadthemebody=='white')        { $themecolorcode="#303F42";}
        if($loadthemebody=='black')        { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')        { $themecolorcode="#00A65A";}
        if($loadthemebody=='red')          { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')       { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE'] = $themecolorcode;

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_load = "site/modern/theme_front";

        $data['is_rtl'] = $this->is_rtl;
        $data['sidebar'] = $this->_sidebars_data();
        $this->load->view($body_load, $data);
    }

    public function index(){
        $data['page_title'] = 'Blog';
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_load = "blog";
        $data['body'] = $body_load;

        // Blog Post Data
        // init params
        $table = "blog_posts";
        $where_sample['blog_posts.status ='] = "1";
        $where = array('where' => $where_sample);
        
        $join = array(
            'users'=>"blog_posts.user_id=users.id,left",
            'blog_post_categories'=>"blog_posts.category_id=blog_post_categories.id,left",
        );

        $select= array(
            "blog_posts.id",
            "blog_posts.title",
            "blog_posts.slug",
            "blog_posts.thumbnail",
            "blog_posts.body",
            "blog_posts.published_at",
            "users.name as author",
            "blog_post_categories.id as category_id",
            "blog_post_categories.name as category_name",
            "blog_post_categories.slug as category_slug",
            "(select count(blog_post_comments.id) from blog_post_comments where blog_post_comments.post_id = blog_posts.id) as total_comments",
        );

        $limit = 10;
        $start = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $totalRecords = $this->basic->count_row($table,$where,$count=$table.".id")[0]['total_rows'];

        // get current page records
        $data["posts"] = $this->basic->get_data($table,$where,$select,$join,$limit,$start, $order_by = 'id desc');
         
        $this->load->library('pagination');
        $config['base_url'] = base_url() . 'blog/index';
        $config['total_rows'] = $totalRecords;
        $config['per_page'] = $limit;
        $config["uri_segment"] = 3;

        $config['full_tag_open'] = '<div class="col-sm-12 text-center"><ul class="pagination">';
        $config['full_tag_close'] = '</ul></div>';

        $config['first_link'] = '<<';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
         
        $config['last_link'] = '>>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = '<span aria-hidden="true">&raquo;</span>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '<span aria-hidden="true">&laquo;</span>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
         
        $this->pagination->initialize($config);
         
        // build paging links
        $data["paiging_links"] = $this->pagination->create_links();

        // Blog Post Data
        $this->_blog_viewcontroller($data);
    }

    public function posts_filter(){
        $data['page_title'] = 'Blog Filter';
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_load = "blog_posts_filter";
        $data['body'] = $body_load;

        // Category wise filter
        //$_GET paramitter received (type='category', slug, id)

        // Tag wise filter
        //$_GET paramitter received (type='tags', slug)

        // Search wise filter
        //$_GET paramitter received (type='search', keywords)

        // Set data on sessions
        if (isset( $_GET['type'])) {
            // Remove sessions previous data
            $this->session->set_userdata('filter_type', '');
            $this->session->set_userdata('filter_slug', '');
            $this->session->set_userdata('filter_id', '');
            $this->session->set_userdata('filter_keywords', '');
            // Remove sessions previous data

            $this->session->set_userdata('filter_type', $_GET['type']);
            if ($_GET['type'] == 'category') {
                $this->session->set_userdata('filter_slug', $_GET['slug']);
                $this->session->set_userdata('filter_id', $_GET['id']);
            }

            if ($_GET['type'] == 'tags') {
                $this->session->set_userdata('filter_slug', $_GET['slug']);
            }

            if ($_GET['type'] == 'search') {
                $this->session->set_userdata('filter_keywords', $_GET['keywords']);
            }
        }
        // Set data on sessions


        // Get sessions data
        $filter_type = $this->session->userdata('filter_type');
        $filter_slug = $this->session->userdata('filter_slug');
        $filter_id = $this->session->userdata('filter_id');
        $filter_keywords = $this->session->userdata('filter_keywords');

        // Blog Post Data
            // init params
            $table = "blog_posts";
            $where_sample['blog_posts.status ='] = "1";

            // filter wise conditions
            if ($filter_type == 'category') {
                $where_sample['blog_posts.category_id ='] = $filter_id;
            }

            if ($filter_type == 'tags') {
                $where_sample['blog_posts.tags LIKE'] = "%{$filter_slug}%";
            }
            
            if ($filter_type == 'search') {
                $where_sample['blog_posts.title LIKE'] = "%{$filter_keywords}%";
            }
            // filter wise conditions

            $where = array('where' => $where_sample);
            
            $join = array(
                'users'=>"blog_posts.user_id=users.id,left",
                'blog_post_categories'=>"blog_posts.category_id=blog_post_categories.id,left",
            );

            $select= array(
                "blog_posts.id",
                "blog_posts.title",
                "blog_posts.slug",
                "blog_posts.thumbnail",
                "blog_posts.body",
                "blog_posts.published_at",
                "users.name as author",
                "blog_post_categories.id as category_id",
                "blog_post_categories.name as category_name",
                "blog_post_categories.slug as category_slug",
                "(select count(blog_post_comments.id) from blog_post_comments where blog_post_comments.post_id = blog_posts.id) as total_comments",
            );

            $limit = 5;
            $start = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $totalRecords = $this->basic->count_row($table,$where,$count=$table.".id")[0]['total_rows'];

            // get current page records
            $data["posts"] = $this->basic->get_data($table,$where,$select,$join,$limit,$start, $order_by = 'id desc');
             
            $this->load->library('pagination');
            $config['base_url'] = base_url() . 'blog/posts_filter';
            $config['total_rows'] = $totalRecords;
            $config['per_page'] = $limit;
            $config["uri_segment"] = 3;

            $config['full_tag_open'] = '<div class="col-sm-12 text-center"><ul class="pagination">';
            $config['full_tag_close'] = '</ul></div>';

            $config['first_link'] = '<<';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
             
            $config['last_link'] = '>>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';

            $config['next_link'] = '<span aria-hidden="true">&raquo;</span>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_link'] = '<span aria-hidden="true">&laquo;</span>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';
            $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
             
            $this->pagination->initialize($config);
             
            // build paging links
            $data["totalRecords"] = $totalRecords;
            $data["paiging_links"] = $this->pagination->create_links();

        // Blog Post Data
        $this->_blog_viewcontroller($data);
    }

    public function post_details($slug, $id){
        $data['page_title'] = 'Post Details';
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_load = "blog_post_details";
        $data['body'] = $body_load;

        // Post data
        $table = "blog_posts";
        $where_sample['blog_posts.id ='] = $id;
        if($this->session->userdata('user_type') != 'Admin')  $where_sample['blog_posts.status'] = '1';
        $where = array('where' => $where_sample);
        
        $join = array(
            'users'=>"blog_posts.user_id=users.id,left",
            'blog_post_categories'=>"blog_posts.category_id=blog_post_categories.id,left",
        );

        $select= array(
            "blog_posts.id",
            "blog_posts.title",
            "blog_posts.slug",
            "blog_posts.thumbnail",
            "blog_posts.body",
            "blog_posts.views",
            "blog_posts.tags",
            "blog_posts.published_at",
            "users.name as author",
            "blog_post_categories.id as category_id",
            "blog_post_categories.name as category_name",
            "blog_post_categories.slug as category_slug",
            "(select count(blog_post_comments.id) from blog_post_comments where blog_post_comments.post_id = blog_posts.id) as total_comments",
        );

        $data["post"] = $this->basic->get_data($table,$where,$select,$join);
        if(empty($data["post"])){
        	show_404();
        }
        // Post data end

        // visitor count
        $this->load->helper('cookie');
        $visited = 'blog_post_details_'.$id;
        $check_visitor = $this->input->cookie($visited, FALSE);
        $ip = $this->input->ip_address();
        // $expire = time() + 7200;
        $expire = 7200;

        if ($check_visitor == false) {
            $cookie = array(
                "name"   => $visited,
                "value"  => "$ip",
                "expire" =>  (int)$expire,
                "secure" => false
            );
            $this->input->set_cookie($cookie);
            $this->basic->update_data('blog_posts', array('id' => $id), [
                'views'=>$data["post"][0]['views'] + 1,
            ]);
        }
        // visitor count end

        $this->_blog_viewcontroller($data);
    }

    public function comment_action(){
        if(!$_POST) exit();
        $this->ajax_check();

        $this->form_validation->set_rules('comment', $this->lang->line('comment'), 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'=> 0,
                'errors'=> $this->form_validation->error_array()['comment']
            ]);
            exit;
        }

        $data['comment'] = $this->input->post('comment', true);
        
        if(isset($_GET['type']) && $_GET['type'] == 'edit'){
            $id = $this->input->post('comment_id', true);
            $data['updated_at'] = date('Y-m-d H:i:s');
            if($this->basic->update_data('blog_post_comments', array('id' => $id), $data)) {
              echo json_encode([
                'status'=> 1,
                'message'=> $this->lang->line("Comment has been updated successfully."),
              ]);
              exit;
            }
        }else{
            if (!empty($this->input->post('post_id', true)) && $this->input->post('post_id', true) > 0){
                $data['post_id'] = $this->input->post('post_id', true);
            }

            if (!empty($this->input->post('parent_id', true)) && $this->input->post('parent_id', true) > 0){
                $data['parent_id'] = $this->input->post('parent_id', true);
            }else{
                $data['parent_id'] = NULL;
            }

            $data['status'] = 1;
            $data['user_id'] = $this->user_id;
            $data['created_at'] = date('Y-m-d H:i:s');

            if($this->basic->insert_data('blog_post_comments', $data)) {
              echo json_encode([
                'status'=> 1,
                'message'=> $this->lang->line("Comment has been created successfully."),
              ]);
              exit;
            }
        }

        echo json_encode([
            'status'=> 2,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
        ]);
        exit;
    }

    public function comment_delete(){
        if(!$_POST) exit();
        $this->ajax_check();

        $id = $this->input->post('id', true);

        if ($this->basic->delete_data('blog_post_comments', array("id" => $id))) {
          echo json_encode([
            'status'=> 1,
            'message'=> $this->lang->line("Comment has been deleted successfully."),
          ]);
          exit;
        }else{
          echo json_encode([
            'status'=> 0,
            'message'=> $this->lang->line("Something Went Wrong, please try once again."),
          ]);
          exit;
        }
    }

    public function load_comments($post_id){
        $table = "blog_post_comments";
        $limit = $this->input->post('limit', true) ? $this->input->post('limit', true) : '10';
        $start = $this->input->post('start', true) ? $this->input->post('start', true) : NULL;
        $where = array('where'=>array('post_id ='=> $post_id, 'blog_post_comments.status ='=> 1, 'parent_id ='=> NULL));
    
        $join = array(
            'users'=>"blog_post_comments.user_id=users.id,left",
        );

        $select= array(
            "blog_post_comments.*",
            "users.name as comment_author",
            "users.brand_logo as comment_author_image",
        );

        $comments = $this->basic->get_data($table,$where,$select,$join,$limit,$start,'blog_post_comments.id desc');

        $str = '';
        foreach ($comments as $comment) {
            $hasParent = $this->basic->get_data($table,array('where'=>array('blog_post_comments.status ='=> 1, 'parent_id ='=> $comment['id'])),$select,$join,'1');
            $str .= '<div class="comment">';
            $str .= '<div class="comment-box">
                <div class="comment-author">';
                    $comment_author_image = $comment['comment_author_image'];
                    if($comment_author_image=="") $comment_author_image=base_url("assets/img/avatar/avatar-1.png");
                    else $comment_author_image=base_url().'member/'.$comment_author_image;
                    $str .= '<img src="'.$comment_author_image.'">';
            $str .= '</div>
                <div class="comment-body">
                    <div class="comment-heading">
                        <h4>'.$comment['comment_author'].' <small><i class="fa fa-clock-o"></i> '.date("jS M y H:i",strtotime($comment["created_at"])).'</small></h4>
                    </div><!--/.comment-heading-->
                    <div class="comment-content">
                        <div class="comment-text" id="comment-text-'.$comment['id'].'">'.$comment['comment'].'</div>
                        <div class="comment-action">';
                            if ($this->session->userdata('logged_in') == 1){
                                if (('Admin' == $this->session->userdata('user_type'))) {
                                    $editbtn = ($this->user_id==$comment['user_id']) ? ' <a href="javascript:void(0)" data-comment-id="'.$comment['id'].'" class="comment-edit btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>' : '';
                                    $str .= '<a href="javascript:void(0)" data-comment-id="'.$comment['id'].'" class="comment-replay btn btn-xs btn-primary"><i class="fa fa-reply"></i></a>'.$editbtn.' <a href="javascript:void(0)" data-comment-id="'.$comment['id'].'" class="comment-delete btn btn-xs btn-danger"><i class="fa fa-trash"></i></a> ';
                                }
                            }
                        $str .= '</div><!--/.comment-action-->
                    </div><!--/.comment-content-->
                </div><!--/.comment-body-->
            </div><!--/.comment-box-->';
            if (count($hasParent) > 0) {
                $str .= $this->_reply_comments($comment['id']);
            }
            $str .= '</div><!--/.comment-->';
            if (end($comments)['id'] == $comment['id']) {
                $start = $limit + $start;
                $str .= '<button type="button" data-start="'.$start.'" class="load-more-comments btn-default">'.$this->lang->line("Load older comments").'...</button>';
            }
        }

        echo $str;
    }

    public function _reply_comments($parent_id){
        $table = "blog_post_comments";
        $where = array('where'=>array('blog_post_comments.status ='=> 1, 'parent_id ='=> $parent_id));
    
        $join = array(
            'users'=>"blog_post_comments.user_id=users.id,left",
        );

        $select= array(
            "blog_post_comments.*",
            "users.name as comment_author",
            "users.brand_logo as comment_author_image",
        );

        $comments = $this->basic->get_data($table,$where,$select,$join);
        $str = '';
        foreach ($comments as $comment) {
            $hasParent = $this->basic->get_data($table,array('where'=>array('blog_post_comments.status ='=> 1, 'parent_id ='=> $comment['id'])),$select,$join,$limit=1);
            $str .= '<div class="comment">';
            $str .= '<div class="comment-box">
                <div class="comment-author">';
                    $comment_author_image = $comment['comment_author_image'];
                    if($comment_author_image=="") $comment_author_image=base_url("assets/img/avatar/avatar-1.png");
                    else $comment_author_image=base_url().'member/'.$comment_author_image;
                    $str .= '<img src="'.$comment_author_image.'" alt="Generic placeholder image">';
            $str .= '</div>
                <div class="comment-body">
                    <div class="comment-heading">
                        <h4>'.$comment['comment_author'].' <small>'.date("jS M y H:i",strtotime($comment["created_at"])).'</small></h4>
                    </div><!--/.comment-heading-->
                    <div class="comment-content">
                        <div class="comment-text" id="comment-text-'.$comment['id'].'">'.$comment['comment'].'</div>
                        <div class="comment-action">';
                            if ($this->session->userdata('logged_in') == 1){
                                if (('Admin' == $this->session->userdata('user_type')) || ($this->user_id == $comment['user_id'])) {
                                    $editbtn = ($this->user_id==$comment['user_id']) ? ' <a href="javascript:void(0)" data-comment-id="'.$comment['id'].'" class="comment-edit btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>' : '';
                                    $str .= '<a href="javascript:void(0)" data-comment-id="'.$comment['id'].'" class="comment-replay btn btn-xs btn-primary"><i class="fa fa-reply"></i></a>'.$editbtn.' <a href="javascript:void(0)" data-comment-id="'.$comment['id'].'" class="comment-delete btn btn-xs btn-danger"><i class="fa fa-trash"></i></a> ';
                                }
                            }
                        $str .= '</div><!--/.comment-action-->
                    </div><!--/.comment-content-->
                </div><!--/.comment-body-->
            </div><!--/.comment-box-->';
            if (count($hasParent) > 0) {
                $str .= $this->_reply_comments($comment['id']);
            }
            $str .= '</div><!--/.comment-->';
        }

        return $str;
    }
}