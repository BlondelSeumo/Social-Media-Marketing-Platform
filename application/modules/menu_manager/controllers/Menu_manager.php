<?php
/*
Addon Name: Menu Manager
Unique Name: menu_manager
Module ID: 0
Project ID: 0
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description:
*/

require_once("application/controllers/Home.php"); // loading home controller

class Menu_manager extends Home
{
    public $editor_allowed_tags;
    public function __construct()
    {
        parent::__construct();
        $function_name=$this->uri->segment(2);
        if ($this->session->userdata('logged_in') != 1) {
            redirect('home/login_page', 'location');
        }
        if($this->session->userdata('user_type') != 'Admin' && $function_name!='custom_page')
        {
            redirect('home/login_page', 'location');
        }
        $this->editor_allowed_tags = '<h1><h2><h3><h4><h5><h6><a><b><strong><p><i><div><span><ul><li><ol><blockquote><code><table><tr><td><th><img><iframe>';
    }

    public function index()
    {
        $data['body'] = 'menu_block';
        $data['page_title'] = $this->lang->line('Menu Manager');
        $this->_viewcontroller($data);
    }

    public function get_page_lists()
    {
        $data['body'] = "custom_page_lists";
        $data['page_title'] = $this->lang->line("Page Manager");
        $this->_viewcontroller($data);
    }

    public function page_lists_data()
    {
        $this->ajax_check();

        $search_value = $_POST['searching'];
        $page_date_range = $this->input->post("page_date_range");
        $display_columns = array("#",'CHECKBOX','id','page_name','slug','url','created_at','actions');
        $search_columns = array('page_name','slug','url');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom="user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) {
                $temp[] = $value." LIKE "."'%$search_value%'";
            }
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        if($page_date_range !="")
        {
            $exp = explode('|', $page_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            $where_custom .= " AND created_at >= '{$from_date}' AND created_at <='{$to_date}'";
        }

        $table = "custom_page_builder";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for($i = 0; $i < count($info); $i++)
        {
            if($info[$i]['created_at'] != "0000-00-00 00:00:00") {
                $info[$i]['created_at'] = date("M j, Y H:i:s A",strtotime($info[$i]['created_at']));
            }

            $info[$i]['url'] = base_url("menu_manager/custom_page/").$info[$i]['url'];

            $info[$i]['actions'] = '
            <div style="min-width:130px;">
                <a target="_BLANK" href="'.base_url('menu_manager/custom_page/').$info[$i]['id'].'" data-toggle="tooltip" title="'.$this->lang->line('View Page').'" class="btn btn-outline-primary view_page btn-circle"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;
                <a href="'.base_url('menu_manager/edit_page/').$info[$i]['id'].'" data-toggle="tooltip" title="'.$this->lang->line('Edit').'" class="btn btn-outline-warning edit_page btn-circle"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;
                <a href="#" data-toggle="tooltip" title="'.$this->lang->line('Delete').'" table_id="'.$info[$i]['id'].'" class="btn btn-outline-danger delete_page btn-circle"><i class="fas fa-trash-alt"></i></a>
            <div>
            <script>$("[data-toggle=\'tooltip\']").tooltip();</script>';            
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function create_page()
    {
        $data['body'] = 'create_page';
        $data['page_title'] = $this->lang->line('Create Page');
        $this->_viewcontroller($data);
    }

    public function create_page_action() 
    {
        $this->ajax_check();

        if(!$_POST) exit;
        $this->load->helper('security');

        $this->form_validation->set_rules('page_name','<b>'.$this->lang->line("Page name").'<b>','trim|required');

        $this->form_validation->set_rules('page_description','<b>'.$this->lang->line("Description").'</b>','trim|required');

        if (false === $this->form_validation->run()) {
            $message = '';
            if ($this->form_validation->error('page_name')) {
                $message = $this->form_validation->error('page_name');
            } elseif ($this->form_validation->error('page_description')) {
                $message = $this->form_validation->error('page_description');
            }

            $message = strip_tags($message);
            echo json_encode(['error'=>$message]); exit;
        }

        $page_name   = strip_tags(trim($this->input->post('page_name',true)));
        $search      = array(' ',"'",'"', '_','/','*','$','&');
        $replace     = '-';
        $slug        = str_replace( $search, $replace, strtolower($page_name));
        $description = strip_tags($this->input->post('page_description'),$this->editor_allowed_tags);

        $data = array(
            'page_name'  => $page_name,
            'slug'       => $slug,
            'url'        => '',
            'page_description'=> $description,
            'user_id'    => $this->user_id,
            'created_at' => date("Y-m-d H:i:s")
        );

        if($this->basic->insert_data('custom_page_builder', $data)) {
            $insertedId = $this->db->insert_id();
            $this->basic->update_data("custom_page_builder",['id'=>$insertedId],['url'=>$insertedId]);
            echo json_encode(['status'=>"1"]);
        } else {
            echo json_encode(['status'=>"0"]);
        }
    }

    public function delete_single_page()
    {
        $this->ajax_check();
        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == null) exit;

        if($this->basic->delete_data("custom_page_builder", array("id"=>$table_id,'user_id'=>$this->user_id)))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }

    public function ajax_delete_all_selected_pages()
    {
        $this->ajax_check();

        $selected_page_ids = $this->input->post('info', true);

        if(!is_array($selected_page_ids)) {
            $selected_page_ids = array();
        }

        $implode_ids = implode(",",$selected_page_ids);

        if(!empty($selected_page_ids)) {

            $final_sql = "DELETE FROM custom_page_builder WHERE id IN({$implode_ids})";

            $this->db->query($final_sql);

            if($this->db->affected_rows() > 0) {
                echo "1";
            } else {
                echo "0";
            }
        }
    }

    public function edit_page($id=0)
    {
        if($id == "" || $id == 0) {
            redirect("home/error_404","location");
        }

        $data['body'] = "update_page";
        $data['page_title'] = $this->lang->line("Edit Page");
        $data['page_data'] = $this->basic->get_data("custom_page_builder",['where'=>['id'=>$id,'user_id'=>$this->user_id]]);

        $this->_viewcontroller($data);
    }

    public function edit_page_action()
    {
        $this->ajax_check();
        $this->load->helper('security');

        $table_id = $this->input->post('page_table_id',true);

        $this->form_validation->set_rules('page_name','<b>'.$this->lang->line("Page name").'<b>',"trim|required");

        $this->form_validation->set_rules('page_description','<b>'.$this->lang->line("Description").'</b>','trim|required');

        if (false === $this->form_validation->run()) {
            $message = '';
            if ($this->form_validation->error('page_name')) {
                $message = $this->form_validation->error('page_name');
            } elseif ($this->form_validation->error('page_description')) {
                $message = $this->form_validation->error('page_description');
            }

            $message = strip_tags($message);
            echo json_encode(['error'=>$message]); exit;
        }

        $page_name   = strip_tags(trim($this->input->post('page_name',true)));
        $search      = array(' ',"'",'"', '_','/','*','$','&');
        $replace     = '-';
        $slug        = str_replace( $search, $replace, strtolower($page_name));
        $description = strip_tags($this->input->post('page_description'),$this->editor_allowed_tags);

        $data = array(
            'page_name'  => $page_name,
            'slug'       => $slug,
            'url'        => $table_id,
            'page_description'=> $description,
            'user_id'    => $this->user_id
        );

        if($this->basic->update_data('custom_page_builder', ['id'=>$table_id], $data)) {
            echo json_encode(['status'=>"1"]);
        } else {
            echo json_encode(['status'=>"0"]);
        }
    }

    public function custom_page($id=0)
    {
        if($id=="" || $id==0) {
            redirect("home/error_404","location");
        }

        $data['body'] = 'view_single_page';
        $pagedata = $this->basic->get_data("custom_page_builder",['where'=>['id'=>$id]]);
        if(!isset($pagedata[0])) {
            redirect("home/error_404","location");
        }
        $data['page_title'] = $pagedata[0]['page_name'];
        $data['page_data'] = $pagedata[0];

        $this->_viewcontroller($data);
    }

    public function get_menu_lists()
    {   
        $menus         = $this->basic->get_data('menu','','','','','','serial asc');
        $data['menus'] = $menus;
        
        $menu_child_1_map         = array();
        $menu_child_1_map         = $this->basic->get_data('menu_child_1','','','','','','serial asc'); 
        $data['menu_child_1_map'] = $menu_child_1_map;

        $admin_double_level2=array('admin/activity_log','payment/accounts','payment/earning_summary','payment/transaction_log','blog/posts');

        $data['page_title'] = $this->lang->line("Link Manager");

        $all_menu = array();

        $i=0;
        foreach ($menus as $key => $value) 
        {
            $all_menu[$i]["text"]            = $value["name"];
            $all_menu[$i]["href"]            = $value["url"];
            $all_menu[$i]["icon"]            = $value["icon"];
            // if($value["color"] !='') {
            //     $color_css = $value['url']!='social_accounts/index' ? "background: -webkit-linear-gradient(270deg,".$value["color"].",".adjustBrightness($value["color"],-0.65).");-webkit-background-clip: text;-webkit-text-fill-color: transparent;" : "color:".$value["color"];

            //     $all_menu[$i]["color"]       = $color_css;
            // } else {
            //     $all_menu[$i]["color"]           = '';
            // }
            $all_menu[$i]["color"]           = $value['color'];
            $all_menu[$i]["target"]          = $value["is_external"];
            $all_menu[$i]["module_access"]   = $value["module_access"];
            $all_menu[$i]["only_admin"]      = $value["only_admin"];
            $all_menu[$i]["only_member"]     = $value["only_member"];
            $all_menu[$i]["add_ons_id"]      = $value["add_ons_id"];
            $all_menu[$i]["header_text"]      = $value["header_text"];
            $all_menu[$i]["is_menu_manager"] = $value["is_menu_manager"];
            $all_menu[$i]["page_list"]       = $value["custom_page_id"];
            $all_menu[$i]["is_extended"]     = "0";
            $all_menu[$i]["license_type"]     = $this->session->userdata('license_type');
            if(in_array($value['url'], $admin_double_level2)) {
                $all_menu[$i]["is_extended"]     = "1";
            }

            $parent_id = $value["id"];

            if($value['have_child'] == '1')
            {
                $j=0;
                foreach ($menu_child_1_map as $key1 => $value1)
                {
                    if($value1["parent_id"] == $parent_id)
                    {
                        $all_menu[$i]["children"][$j]["text"]          = $value1["name"];
                        $all_menu[$i]["children"][$j]["href"]          = $value1["url"];
                        $all_menu[$i]["children"][$j]["icon"]          = $value1["icon"];
                        $all_menu[$i]["children"][$j]["is_menu_manager"] = $value1["is_menu_manager"];
                        $all_menu[$i]["children"][$j]["page_list"]       = $value1["custom_page_id"];
                        $all_menu[$i]["children"][$j]["target"]        = $value1["is_external"];
                        $all_menu[$i]["children"][$j]["module_access"] = $value1["module_access"];
                        $all_menu[$i]["children"][$j]["only_admin"]    = $value1["only_admin"];
                        $all_menu[$i]["children"][$j]["only_member"]   = $value1["only_member"];
                        $all_menu[$i]["children"][$j]["is_extended"]   = '0';
                        $all_menu[$i]["children"][$j]["license_type"]   = $this->session->userdata('license_type');
                        if(in_array($value1['url'], $admin_double_level2)) {
                            $all_menu[$i]["children"][$j]["is_extended"] = "1";
                        }

                        $j++;
                    }

                } 
            }

            $i++;
        }

        $data['page_value'] = $this->basic->get_data('custom_page_builder',['where'=>['user_id'=>$this->user_id]]);
        $data['all_menu']   = addslashes(json_encode($all_menu));
        $data['body']       = 'menu_manager';
        $this->_viewcontroller($data);
    }

    public function insert_menu_data()
    {
        $menus_value = $_POST['values'];
        $datas       = json_decode($menus_value,true);
        // echo "<pre>"; print_r($datas); exit;
        $this->db->trans_begin();

        $this->basic->delete_data('menu', ['id >'=>0]);
        $this->basic->delete_data('menu_child_1', ['id >'=>0]);

        $i = 1;
        foreach ($datas as $menu) {

            $data                  = array();
            $data['name']          = trim($menu['text']);
            $data['icon']          = $menu['icon'];
            $data['color']         = $menu['color'];
            
            if($i > 1) $i = 3 + $i;

            $data['serial']        = $i;
            $data['module_access'] = $menu['module_access'];

            if(isset($menu['children']))
                $data['have_child'] = "1";
            else
                $data['have_child'] = "0";

            $data['only_admin']      = $menu['only_admin'];
            $data['only_member']     = $menu['only_member'];

            if(isset($menu['add_ons_id']))
                $data['add_ons_id']      = $menu['add_ons_id'];
            if(isset($menu['header_text']))
                $data['header_text']     = trim($menu['header_text']);
            $data['is_external']     = $menu['target'];
            $data['is_menu_manager'] = $menu['is_menu_manager'];

            if($menu['page_list'] == '') $menu['page_list'] = '0';

            if($menu['target'] == "0" && $menu['is_menu_manager'] == "0") { // system menu

                $data['custom_page_id'] = "0";
                $data['url']           = trim($menu['href']);

            } else if($menu['target'] == '0' && $menu['is_menu_manager'] == '1') { // internal menu

                if($menu['page_list'] != '0') {
                    $data['custom_page_id'] = $menu['page_list'];
                    $data['url']     = "menu_manager/custom_page/".$menu['page_list'];

                } else {

                    $data['custom_page_id'] = '0';
                    $data['url']     = "#";
                }

            } else if($menu['target'] == '1' && $menu['is_menu_manager'] == '1') { // external menu

                $data['custom_page_id'] = '0';
                $data['url'] = trim($menu['href']);

            }

            $this->basic->insert_data('menu',$data);
            $parent_id = $this->db->insert_id();
            
            if (isset($menu['children'])) {

                $j = 1;               
                foreach ($menu['children'] as $child_1) {

                    if(isset($child_1['color'])) {
                        unset($child_1['color']);
                    }
                    
                    $data                  = array();
                    $data['name']          = $child_1['text'];
                    $data['icon']          = $child_1['icon'];

                    if($j > 1) $j = 3 + $j;

                    $data['serial']        = $j;
                    $data['module_access'] = $child_1['module_access'];

                    if(isset($child_1['children']))
                        $data['have_child'] = "1";
                    else
                        $data['have_child'] = "0";

                    $data['only_admin']  = $child_1['only_admin'];
                    $data['only_member'] = $child_1['only_member'];
                    $data['icon']        = $child_1['icon'];
                    $data['parent_id']   = $parent_id;
                    $data['is_external'] = $child_1['target'];
                    $data['is_menu_manager'] = $child_1['is_menu_manager'];

                    if($child_1['page_list'] == '') $child_1['page_list'] = '0';

                    if($child_1['target'] == "0" && $child_1['is_menu_manager'] == "0") { // system menu

                        $data['custom_page_id'] = "0";
                        $data['url']           = trim($child_1['href']);

                    } else if($child_1['target'] == '0' && $child_1['is_menu_manager'] == '1') { // internal menu

                        if($child_1['page_list'] != '0') {
                            
                            $data['custom_page_id'] = $child_1['page_list'];
                            $data['url']     = "menu_manager/custom_page/".$child_1['page_list'];

                        } else {
                            
                            $data['custom_page_id'] = '0';
                            $data['url']     = "#";
                        }

                    } else if($child_1['target'] == '1' && $child_1['is_menu_manager'] == '1') { // external menu

                        $data['custom_page_id'] = '0';
                        $data['url'] = trim($child_1['href']);

                    }

                    $this->basic->insert_data('menu_child_1',$data);
                    $j++;
                }
            }

            $i++;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
        }
        else
        {
            $this->db->trans_commit();
        }

        echo true;

    }

    public function reset_to_default()
    {
        $this->ajax_check();

        $sql=array
        (
            1=> "DROP TABLE IF EXISTS `menu`;",

            2=>"
            CREATE TABLE IF NOT EXISTS `menu` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `icon` varchar(255) NOT NULL,
              `color` varchar(20) NOT NULL,
              `url` varchar(255) NOT NULL,
              `serial` int(11) NOT NULL,
              `module_access` varchar(255) NOT NULL,
              `have_child` enum('1','0') NOT NULL DEFAULT '0',
              `only_admin` enum('1','0') NOT NULL DEFAULT '1',
              `only_member` enum('1','0') NOT NULL DEFAULT '0',
              `add_ons_id` int(11) NOT NULL,
              `is_external` enum('0','1') NOT NULL DEFAULT '0',
              `header_text` varchar(255) NOT NULL,
              `is_menu_manager` enum('0','1') NOT NULL DEFAULT '0',
              `custom_page_id` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            3=>"
            INSERT INTO `menu` (`id`, `name`, `icon`,`color`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) 
            VALUES
            (1, 'Dashboard', 'fa fa-fire', '#ff7979', 'dashboard', 1, '', '0', '0', '0', 0, '0', '', '0', 0),
            (2, 'System', 'fas fa-cog','#d35400', '', 50, '', '1', '1', '0', 0, '0', 'Administration', '0', 0),
            (3, 'Subscription', 'fas fa-coins','#ffa801', '', 50, '', '1', '1', '0', 0, '0', '', '0', 0),
            (4, 'Facebook & Instagram', 'fab fa-facebook','#0D8BF1', 'social_accounts/index', 5, '65', '0', '0', '0', 0, '0', 'Integrations', '0', 0),
            (5, 'Comment Growth Tools', 'fas fa-comments','#575fcf', 'comment_automation/comment_growth_tools', 14, '80,201,202,204,206,220,222,223,251,256,278,279', '0', '0', '0', 0, '0', 'Comment Feature', '0', 0),
            (6, 'Subscriber Manager', 'fas fa-user-circle','#D980FA', 'subscriber_manager/bot_subscribers', 21, '', '0', '0', '0', 0, '0', 'Messenger Tools', '0', 0),
            (7, 'Live Chat', 'fas fa-comment-alt','#32ff7e', 'subscriber_manager/livechat', 21, '', '0', '0', '0', 0, '0', '', '0', 0),
            (8, 'Broadcasting', 'fas fa-paper-plane','#0D8BF1', 'messenger_bot_broadcast', 29, '79,210,211,262,263,264', '0', '0', '0', 0, '0', '', '0', 0),
            (9, 'Bot Manager', 'fas fa-project-diagram','#B33771', 'messenger_bot/bot_list', 25, '197,198,199,211,213,214,215,217,218,219,257,258,260,261,262,265,266', '0', '0', '0', 0, '0', '', '0', 0),
            (10, 'Ecommerce Store', 'fas fa-shopping-cart','#FC427B', 'ecommerce', 30, '268', '0', '0', '0', 0, '0', 'Ecommerce', '0', 0),
            (11, 'Social Posting', 'fa fa-share-square','#a55eea', 'ultrapost', 33, '220,222,223,256,100', '0', '0', '0', 0, '0', 'Posting Feature', '0', 0),
            (12, 'Search Tools', 'fas fa-search', '#218c74','search_tools', 37, '267', '0', '0', '0', 0, '0', 'Utility Tools', '0', 0),
            (13, 'API Channels', 'fas fa-wifi', '#0D8BF1','integration', 5, '', '0', '0', '0', 0, '0', '', '0', 0);",
            4=>"DROP TABLE IF EXISTS `menu_child_1`;",
            5=>"
            CREATE TABLE IF NOT EXISTS `menu_child_1` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `url` varchar(255) NOT NULL,
              `serial` int(11) NOT NULL,
              `icon` varchar(255) NOT NULL,
              `module_access` varchar(255) NOT NULL,
              `parent_id` int(11) NOT NULL,
              `have_child` enum('1','0') NOT NULL DEFAULT '0',
              `only_admin` enum('1','0') NOT NULL DEFAULT '1',
              `only_member` enum('1','0') NOT NULL DEFAULT '0',
              `is_external` enum('0','1') NOT NULL DEFAULT '0',
              `is_menu_manager` enum('0','1') NOT NULL DEFAULT '0',
              `custom_page_id` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            6=>"
            INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES
            (1, 'Settings', 'admin/settings', 1, 'fas fa-sliders-h', '', 2, '0', '1', '0', '0', '0', 0),
            (3, 'Cron Job', 'cron_job/index', 9, 'fas fa-clipboard-list', '', 2, '0', '1', '0', '0', '0', 0),
            (4, 'Language Editor', 'multi_language/index', 13, 'fas fa-language', '', 2, '0', '1', '0', '0', '0', 0),
            (5, 'Add-on Manager', 'addons/lists', 17, 'fas fa-plug', '', 2, '0', '1', '0', '0', '0', 0),
            (7, 'Check Update', 'update_system/update_list_v2', 21, 'fas fa-leaf', '', 2, '0', '1', '0', '0', '0', 0),
            (8, 'Package Manager', 'payment/package_manager', 1, 'fas fa-shopping-bag', '', 3, '0', '1', '0', '0', '0', 0),
            (9, 'User Manager', 'admin/user_manager', 5, 'fas fa-users', '', 3, '0', '1', '0', '0', '0', 0),
            (10, 'Announcement', 'announcement/full_list', 9, 'far fa-bell', '', 3, '0', '1', '0', '0', '0', 0),
            (12, 'Earning Summary', 'payment/earning_summary', 17, 'fas fa-tachometer-alt', '', 3, '0', '1', '0', '0', '0', 0),
            (13, 'Transaction Log', 'payment/transaction_log', 27, 'fas fa-history', '', 3, '0', '1', '0', '0', '0', 0),
            (46, 'Theme Manager', 'themes/lists', 19, 'fas fa-palette', '', 2, '0', '1', '0', '0', '0', 0),
            (47, 'Blog Manager', 'blog/posts', 20, 'fas fa-newspaper', '', 2, '0', '1', '0', '0', '0', 0),
            (48, 'Menu Manager', 'menu_manager/index', 20, 'fas fa-bars', '', 2, '0', '1', '0', '0', '0', 0);"
        );
        
        // if($this->basic->is_exist("add_ons",array("project_id"=>"50"),"id"))
        // {
        //    $sql_woocommerce = "INSERT INTO `menu` (`name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES('WC Integration', 'fab fa-wordpress', 'woocommerce_integration', (SELECT serial FROM menu as menu2 WHERE url='ecommerce'), '293', '0', '0', '0', (SELECT id FROM add_ons WHERE project_id='50'), '0', '', '0', 0);";
        //    array_push($sql, $sql_woocommerce); 
        // }

        if($this->basic->is_exist("add_ons",array("project_id"=>"55"),"id"))
        {
            $sqls_array = array(
                8=>"INSERT INTO `menu` (`id`, `name`, `icon`, `color`,`url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'GMB Manager', 'fas fa-store-alt','#0D8BF1', '', (SELECT serial FROM menu as menu2 WHERE url='ultrapost'), '300,301,302,303,304,305', '1', '0', '0', (SELECT id FROM add_ons WHERE project_id='55'), '0', '', '0', '0');",
                9=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Location Manager', 'gmb/location_list', '5', 'fas fa-map-marked-alt', '301,302', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);",
                10=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Campaigns', 'gmb/campaigns', '10', 'fas fa-arrows-alt', '303,304', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);",
                11=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Review Replies', 'gmb/review_report', '15', 'fas fa-reply-all', '302', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);",
                12=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Account Import', 'gmb/business_accounts', '1', 'fa fa-cloud-download-alt', '300', (SELECT id FROM menu WHERE module_access='300,301,302,303,304,305'), '0', '0', '0', '0', '0', 0);"
            );
            
            foreach ($sqls_array as $single_sql) {
                array_push($sql, $single_sql); 
            }
        }

        if($this->basic->is_exist("add_ons",array("project_id"=>"57"),"id"))
        {
            $sqls_array2 = array(
                13=>"INSERT INTO `menu` (`id`, `name`, `icon`, `color`,`url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Affiliate System', 'fas fa-people-carry', '#34e7e4','', 50, '0', '1', '1', '0', 0, '0', '', '0', 0);",
                14=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Affiliate Users', 'affiliate_system/affiliate_users', 1, 'fas fa-users', '', (SELECT id FROM menu WHERE module_access='0'), '0', '1', '0', '0', '0', 0);",
                15=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Commission Settings', 'affiliate_system/affiliate_payment_settings', 5, 'fas fa-money-check-alt', '', (SELECT id FROM menu WHERE module_access='0'), '0', '1', '0', '0', '0', 0);",
                16=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Withdrawal Requests', 'affiliate_system/all_withdrawal_requests', 9, 'fas fa-hands-helping', '', (SELECT id FROM menu WHERE module_access='0'), '0', '1', '0', '0', '0', 0);"
            );
            
            foreach ($sqls_array2 as $single_sql2) {
                array_push($sql, $single_sql2); 
            }
        }

        if($this->basic->is_exist("add_ons",array("project_id"=>"65"),"id"))
        {
            $sqls_array3 = array(
                1=>"INSERT INTO `menu` (`id`, `name`, `icon`, `color`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Team Member', 'fas fa-user-friends', '#32ff7e','', (SELECT serial FROM menu as menu2 WHERE have_child='1' ORDER BY id DESC LIMIT 1), '325', '1', '0', '0', (SELECT id FROM add_ons WHERE project_id='65'), '0', 'Team', '0', '0');",

                2=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Team Roles', 'team_member/role_list', '1', 'fas fa-user-lock', '325', (SELECT id FROM menu WHERE module_access='325'), '0', '0', '0', '0', '0', 0);",

                3=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Team Members', 'team_member/member_list', '1', 'fas fa-user-ninja', '325', (SELECT id FROM menu WHERE module_access='325'), '0', '0', '0', '0', '0', 0);"
            );
            
            foreach ($sqls_array3 as $single_sql3) {
                array_push($sql, $single_sql3); 
            }
        }
        
        $this->db->trans_start();

        foreach ($sql as $key => $query) 
        {
            try
            {
                $this->db->query($query);
            }
            catch(Exception $e)
            {
            }                    
        }

        if(!$this->addon_exist("comment_reply_enhancers")) 
        {
            $this->basic->delete_data('menu_child_1',['url'=>'comment_reply_enhancers/post_list']);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
            exit();
        }
        else
        {
            echo json_encode(array('status'=>'1','message'=>$this->lang->line('You have successfully revert back to the default menus.')));
        }



    }


}