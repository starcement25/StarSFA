<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Dbdeletecheck_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
	}
	function dbdeletecheck_get(){
		$emp_code = $this->get('emp_code');
		$deviceId = $this->get('deviceId');
		
		$rowselect = $this->Userdb_model->dbbackupcheck_isdelete($emp_code,$deviceId);
		$is_delete = $rowselect['is_delete'];
		
		$this->Userdb_model->dbbackupcheck_update_isdelete($emp_code,$deviceId);
		
		$this->Userdb_model->changepassword_update_deviceid_regid($emp_code);
		
		echo $is_delete;
	}
}
?>