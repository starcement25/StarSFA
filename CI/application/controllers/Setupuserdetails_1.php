<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Setupuserdetails_1 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$setup_db = "acednsproduct";
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($setup_db);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('User_nick_name_model');
		$this->load->model('API_log_model');
	}
	
	function setupuserdetails_get(){
		$nick_name = $this->get('nick_name');
		$emp_code = $this->get('emp_code');
		$last_update_time = $this->get('last_update_time');
		$incremental_download = $this->get('incremental_download');
		$setupuserdetails_data = $this->User_nick_name_model->setupuserdetails_download($nick_name,$emp_code,$last_update_time,$incremental_download);
		if(count($setupuserdetails_data)>0){
			$date=date('Y-m-d');
			$time=date('H:i:s');
			$contentsdatetime = $date.'€'.$time;
			$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
			
			$contents.="<data>";
			$contents .='<user_id><![CDATA['.mb_convert_encoding($setupuserdetails_data['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
						<name><![CDATA['.mb_convert_encoding($setupuserdetails_data['name'], 'UTF-8', 'UTF-8').']]></name>
						<address><![CDATA['.mb_convert_encoding($setupuserdetails_data['address'], 'UTF-8', 'UTF-8').']]></address>
						<phone_no><![CDATA['.mb_convert_encoding($setupuserdetails_data['phone_no'], 'UTF-8', 'UTF-8').']]></phone_no>
						<email><![CDATA['.mb_convert_encoding($setupuserdetails_data['email'], 'UTF-8', 'UTF-8').']]></email>
						<license_key><![CDATA['.mb_convert_encoding($setupuserdetails_data['license_key'], 'UTF-8', 'UTF-8').']]></license_key>
						<no_users><![CDATA['.mb_convert_encoding($setupuserdetails_data['no_users'], 'UTF-8', 'UTF-8').']]></no_users>
						<nick_name><![CDATA['.mb_convert_encoding($setupuserdetails_data['nick_name'], 'UTF-8', 'UTF-8').']]></nick_name>
						<no_of_branches><![CDATA['.mb_convert_encoding($setupuserdetails_data['no_of_branches'], 'UTF-8', 'UTF-8').']]></no_of_branches>
						<email_hierarchywise><![CDATA['.mb_convert_encoding($setupuserdetails_data['email_hierarchywise'], 'UTF-8', 'UTF-8').']]></email_hierarchywise>
						<vertical_fields><![CDATA['.mb_convert_encoding($setupuserdetails_data['vertical_fields'], 'UTF-8', 'UTF-8').']]></vertical_fields>
						<vertical_fields_value><![CDATA['.mb_convert_encoding($setupuserdetails_data['vertical_fields_value'], 'UTF-8', 'UTF-8').']]></vertical_fields_value>
						<previous_stock><![CDATA['.mb_convert_encoding($setupuserdetails_data['previous_stock'], 'UTF-8', 'UTF-8').']]></previous_stock>
						<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
						<multiple_prospect><![CDATA['.mb_convert_encoding($setupuserdetails_data['multiple_prospect'], 'UTF-8', 'UTF-8').']]></multiple_prospect>
						<multiple_prospect_value><![CDATA['.mb_convert_encoding($setupuserdetails_data['multiple_prospect_value'], 'UTF-8', 'UTF-8').']]></multiple_prospect_value>
						<stock_audit_scan><![CDATA['.mb_convert_encoding($setupuserdetails_data['stock_audit_scan'], 'UTF-8', 'UTF-8').']]></stock_audit_scan>';
			$contents.="</data>";
			$contents .= "</recordset>";
			$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
			
			echo $contents;
		}
		else{
			echo '0';
		}
		$this->db->close();
		
		$config = $this->dbconn->create_db_conn($nick_name);
		$this->load->database($config);
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = base_url()."Setupuserdetails_1/setupuserdetails/nick_name/RUPA/emp_code/$emp_code/last_update_time/$last_update_time/incremental_download/$incremental_download/X-API-KEY/abcd1234";
		$this->API_log_model->insert_api_log($datetime,$emp_code,$url);
		$this->db->close();
	}
}
?>