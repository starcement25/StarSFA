<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class UpdatedeviceId_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
	}
	function updatedeviceid_get(){
		$deviceId = $this->get('deviceId');
		$emp_code = $this->get('emp_code');
		
		if($this->Userdb_model->updatedevice_id($emp_code,$deviceId)){
			echo "1";
		}
		else{
			echo "0";
		}
	}
}
?>