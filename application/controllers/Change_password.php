<?php 
require_once("Home.php"); // including home controller

class Change_password extends Home
{
  
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }
    }


    public function index()
    {
        $this->reset_password_form();
    }


    public function reset_password_form()
    {
        $data['page_title'] = $this->lang->line("Change Password");
        $data['body'] = 'admin/theme/password_reset_form';
        $this->_subscription_viewcontroller($data);
    }

    public function reset_password_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('defaults/access_forbidden', 'location');
        }

        $this->csrf_token_check();

        $this->form_validation->set_rules('old_password', '<b>'.$this->lang->line("Old Password").'</b>', 'trim|required');
        $this->form_validation->set_rules('new_password', '<b>'.$this->lang->line("New Password").'</b>', 'trim|required');
        $this->form_validation->set_rules('confirm_new_password', '<b>'.$this->lang->line("Confirm Password").'</b>', 'trim|required|matches[new_password]');
        if ($this->form_validation->run() == false) {
            $this->reset_password_form();
        } else {
            $user_id = $this->user_id;
            $password = $this->input->post('old_password', true);
            $new_password = $this->input->post('new_password', true);
            $table = 'users';
            $where['where'] = array(
                'id' => $user_id,
                'password' => md5($password)
                );
            $select = array('');
            if ($this->basic->get_data($table, $where, $select)) {
                $where = array(
                    'id' => $user_id,
                    'password' => md5($password)
                    );
                $data = array('password' => md5($new_password));
                $this->basic->update_data($table, $where, $data);
                $this->session->sess_destroy();
                $this->session->set_flashdata('reset_success', $this->lang->line('Please login with new password'));
                redirect('home/login', 'location');
                // echo $this->session->userdata('reset_success');exit();
            } else {
                $this->session->set_userdata('error', $this->lang->line('The old password you have given is wrong'));
                $this->reset_password_form();
            }
        }
    }
}
