<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

class Employee_master_rest extends REST_Controller {
	function __construct() {
        parent::__construct();
        // path to simple_html_dom
		$nick_name=$this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Employee_model');
    }
	/*public function index_get($nick_name,$id) {
			if($nick_name != '')
				$another_db=$this->load->database($nick_name);
			else
				$this->load->database(); // load database
			$this->load->model('Employee_model'); // load model
			$emp_data = $this->Employee_model->retrieve_data($id);
			echo "<pre>";
			print_r($emp_data);
			echo "</pre>";
    }*/
	function emplist_get()
    {
		if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }
		//$nick_name=$this->input->post('nick_name');
		$id=$this->get('id');
		$emp_data = $this->Employee_model->retrieve_data($id);
		if($emp_data)
		{
			 $this->response($emp_data, 200);
		}
    }
}
?>