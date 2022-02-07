<?php 
require_once FCPATH."system/core/Controller.php";
require_once APPPATH.'controllers/Test.php';


// var_dump($rc->hasMethod('publicFoo')); // bool(false)
// var_dump($rc->hasMethod('index')); // bool(true)



// return 'Test/index'; // return your routing

/**
* @category controller
* class Admin
*/

class Method_check extends Home
{

    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('form');
        $this->load->library('upload');
        $this->upload_path = realpath(APPPATH . '../upload');

    }

    public function index()
    {
    	$rc = new ReflectionClass('Test');
    	
    	if($rc->hasMethod('index'))
    	{

    	}
    	else
    		unlink(APPPATH.'controllers/test.php');
    }

}