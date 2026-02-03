<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Emplogindeviceid_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
	}
	function emplogindeviceid_get(){
		$deviceid = $this->get('deviceid');
		$emp_code = $this->get('emp_code');
		
		if($deviceid == ''){
			echo "6";
		}
		else{
			$result = $this->Userdb_model->device_id($emp_code);
			$count = count($result);
			$device_id_database = $result['deviceid'];
			
			if($count>0){
				if($deviceid == $device_id_database){
					echo "1";
				}
				else{
					if($device_id_database == ''){
						$emp_name_result = $this->Userdb_model->empname_chngpassword($deviceid);
						$cntchkdeviceid = count($emp_name_result);
						if($cntchkdeviceid>0){
							$emp_name = $emp_name_result['emp_name'];
							echo '4'.'/'.$emp_name;
						}
						else{
							echo "2";
						}
					}
					else{
						echo "0";
					}
				}
			}
			else{
				echo "5";
			}
		}
	}
}
?>