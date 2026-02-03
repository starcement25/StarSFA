<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Dbbackupcheck_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
	}
	function dbbackupcheck_get(){
		$emp_code = $this->get('emp_code');
		$deviceId = $this->get('deviceId');
		
		$rowselect = $this->Userdb_model->dbbackupcheck($deviceId);
		$count = count($rowselect);
		
		if($count == 0){
			if($this->Userdb_model->dbbackupcheck_insert($emp_code,$deviceId)){
				echo "0";
			}
			else{
				echo "3";
			}
		}
		else{
			echo $rowselect['is_checked'];
		}
	}
}
?>