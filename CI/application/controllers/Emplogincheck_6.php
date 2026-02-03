<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Emplogincheck_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
		$this->load->model('API_log_model');
	}
	function emplogincheck_get(){
		$emp_code = $this->get('emp_code');
		$newpassword = $this->get('newpassword');
		$deviceid = $this->get('deviceid');
		
		$result = $this->Userdb_model->emp_changepassword($newpassword,$emp_code);
		$count = count($result);
		
		$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
		if($count>0){
			$is_licensed_array = $this->Userdb_model->is_licensed_changepassword($emp_code);
			$is_licensed = $is_licensed_array['is_licensed'];
			$device_id_database = $result['deviceid'];
			
			if($is_licensed == 1){
				if($deviceid == ''){
					echo '6';
				}
				else{
					/*----> Checking the posted deviceid and the database existed deviceid  is same or not <----*/
					if($deviceid != $device_id_database){
						/*---->Checking that the database existed deviceid is blank or no <----*/
						if($device_id_database == ''){
							$cntchkdeviceid_array = $this->Userdb_model->emp_deviceid_check($deviceid);
							$cntchkdeviceid = count($cntchkdeviceid_array);
							/* ----> Checking that the POST data deviceid is already existed on the database for different employee code or not <---- */
							if($cntchkdeviceid>0){
								$emp_name = $cntchkdeviceid_array['emp_name'];
							   	echo '4'.'/'.$emp_name;
							}
							else{
								if($this->Userdb_model->update_changepassword($deviceid,$emp_code)){
									$this->Userdb_model->update_table_structure_updation($deviceid,$emp_code);
									$i = 0;
									$contents.="<data>";
									foreach($result as $key=>$val){
										$contents .='<'.$key.'><![CDATA['.mb_convert_encoding($val, 'UTF-8', 'UTF-8').']]></'.$key.'>';
									}
									$contents.="</data>";
									$contents .= "</recordset>";			
									echo $contents;
								}
								else{
									echo "0";
								}
							}
						}
						else{
							echo "0";
						}
					}
					else{
						$i = 0;
						$this->Userdb_model->update_table_structure_updation($deviceid,$emp_code);
						$contents.="<data>";
						foreach($result as $key=>$val){
							$contents .='<'.$key.'><![CDATA['.mb_convert_encoding($val, 'UTF-8', 'UTF-8').']]></'.$key.'>';
						}
						$contents.="</data>";
						$contents .= "</recordset>";			
						echo $contents;
					}
				}
			}
			else{
				echo 'NOT LICENSED USER';
			}
		}
		else{
			echo 'NOT VALID USER';
		}
		
		$this->db->close();
		$config = $this->dbconn->create_db_conn($nick_name);
		$this->load->database($config);
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		
		$url = base_url()."Emplogincheck_6/emplogincheck/nick_name/$nick_name/emp_code/$emp_code/device_id/$deviceid/newpassword/$newpassword/X-API-KEY/abcd1234";
		insertapilog($datetime,$emp_code,$url,$nick_name);
		$this->API_log_model->insert_api_log($datetime,$emp_code,$url);
		$this->db->close();
	}
}
?>