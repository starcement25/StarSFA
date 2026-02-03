<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class TransactionDeletionConfirmation_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		require APPPATH . '/controllers/config_setup.php';
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
		$this->load->model('API_log_model');
		$this->load->library('function_required');
		require APPPATH . '/controllers/config_email_setup.php';
	}
	function transactiondeletionconfirmation_get(){
		$emp_code = $this->get('emp_code');
		
		if(employeewise_hierarchy == 'yes'){
			$employee_hierarchy = return_employee_hierarchy($emp_code);
			$rowreportinglevel = $this->Userdb_model->empmaster_countreportingto($emp_code);
			$reporting_level = $rowreportinglevel['total_emp_code'];
			
		}
		else{
			$reporting_level = 0;
		}
		
		if($reporting_level > 0){
			$rowrdslist = $this->Userdb_model->rdsmaster_rdscode_hierarchy($employee_hierarchy);
			foreach($rowrdslist as $rowrdslistval){
				$rds_list = $rds_list."'".$rowrdslistval."'".',';
			}
			$rds_list = substr($rds_list,0,-1);
			if($this->Userdb_model->activity_log($rds_code='',$emp_code='',$rds_list,$employee_hierarchy,$val='1')){
				echo "1";
			}
			else{
				echo "0";
			}
		}
		else{
			$rowrds = $this->Userdb_model->rdsmaster_rdscode($emp_code);
			$rds_code = $rowrds['rds_code'];
			
			$rowactivitylog = $this->Userdb_model->activitylog_transid($emp_code);
			$cntactivitylog = count($rowactivitylog);
			
			if($cntactivitylog > 0){
				$result = $this->Userdb_model->update_activitylog($emp_code,$rds_code='',$val='1');
			}
			else{
				$result = $this->Userdb_model->update_activitylog($emp_code,$rds_code,$val='2');
			}
			
			if($result){
				echo "1";
			}
			else{
				echo "0";
			}
		}
		
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = base_url()."TransactionDeletionConfirmation_6/transactiondeletionconfirmation/nick_name/$nick_name/emp_code/$emp_code/X-API-KEY/abcd1234";
		$this->API_log_model->insert_api_log($datetime,$emp_code,$url);
		$this->db->close();
	}
}

?>