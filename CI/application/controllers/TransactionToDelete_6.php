<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class TransactionToDelete_6 extends REST_Controller{
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
	}
	function transactiontodelete_get(){
		$emp_code = $this->get('emp_code');
		
		if(employeewise_hierarchy=='yes'){
			$employee_hierarchy = return_employee_hierarchy($emp_code);
			$rowreportinglevel = $this->Userdb_model->empmaster_countreportingto($emp_code);
			$reporting_level = $rowreportinglevel['total_emp_code'];
		}
		else{
			$reporting_level=0;
		}
		
		$rowrds = $this->Userdb_model->rdsmaster_rdscode($emp_code);
		$rds_code = $rowrds['rds_code'];
		
		if($reporting_level>0){
			$rowrdslist = $this->Userdb_model->rdsmaster_rdscode_hierarchy($employee_hierarchy);
			foreach($rowrdslist as $rowrdslistval){
				$rds_list = $rds_list."'".$rowrdslistval."'".',';
			}
			$rds_list = substr($rds_list,0,-1);
			$rowsdelete = $this->Userdb_model->activity_log($rds_code='',$emp_code='',$rds_list,$employee_hierarchy,$val='1');
		}
		else{
			$rowsdelete = $this->Userdb_model->activity_log($rds_code,$emp_code,$rds_list='',$employee_hierarchy='',$val='2');
		}
		
		$count = count($rowsdelete);
		$cnt = 1;
		$contents = "<?xml version='1.0' encoding='UTF-8'?><root>";
		
		if($count>0){
			$date = date('Y-m-d');
			$time = date('H:i:s');
			$contentsdatetime = $date.'€'.$time;
			
			foreach($rowsdelete as $rowsdelete_val){
				if($rowsdelete_val['transaction_id'] != '') 
					$deletion_mode='transactionwise';
				else							
					$deletion_mode='datewise';
					
				$contents.="<deletion_data>";
				$contents .='<order_no><![CDATA['.mb_convert_encoding($rowsdelete_val['transaction_id'], 'UTF-8', 'UTF-8').']]></order_no>
							<start_date><![CDATA['.mb_convert_encoding($rowsdelete_val['start_date'], 'UTF-8', 'UTF-8').']]></start_date>
							<end_date><![CDATA['.mb_convert_encoding($rowsdelete_val['end_date'], 'UTF-8', 'UTF-8').']]></end_date>
							<deletion_mode><![CDATA['.mb_convert_encoding($rowsdelete_val, 'UTF-8', 'UTF-8').']]></deletion_mode>';
				$contents.="</deletion_data>";
			}
		}
		$contents .= "</root>";
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = base_url()."TransactionToDelete_6/transactiontodelete/nick_name/$nick_name/emp_code/$emp_code/X-API-KEY/abcd1234";
		$this->API_log_model->insert_api_log($datetime,$emp_code,$url);
		$this->db->close();
	}
}
?>