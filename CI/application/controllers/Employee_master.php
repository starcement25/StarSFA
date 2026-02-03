<?php
class Employee_master extends CI_Controller {
	function __construct() {
        parent::__construct();
        // path to simple_html_dom
		//$this->load->helper('url');
		//echo 'a'.$this->input->get('nick_name',TRUE);
		
    }
	
	function employee_master_data($nick_name) {
		echo "Hello ".$nick_name."<br>";
        //$this->load->view('page_php');
		//$db = "acedns_".$nick_name;
		/*if($nick_name == 'ABDOS' || $nick_name == 'RUPA')
			$this->load->database($nick_name);
		else
			$this->load->database(); // load database*/
			
		/* Library Dbconn created for dynamic database connectivity */
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
		
		/* Load database by passing database settings as argument */
		$this->load->database($config);
   		$this->load->model('Employee_model'); // load model
		$emp_data = $this->Employee_model->retrieve_data('E0001');//calls model function with argument
		
		/* Dumps Data */
		echo "<pre>";
		print_r($emp_data);
		echo "</pre>";
    }
}
?>