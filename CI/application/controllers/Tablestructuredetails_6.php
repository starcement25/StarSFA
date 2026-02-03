<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Tablestructuredetails_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		require APPPATH . '/controllers/config_setup.php';
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
		//$this->load->model('API_log_model');
	}
	
	function tablestructuredetails_get(){
		$nick_name = $this->get('nick_name');
		$emp_code = $this->get('emp_code');
		$device_id = $this->get('device_id');
		$mode = $this->get('mode');
		$db_version = $this->Userdb_model->db_version();
		$versionCode = $db_version[0]->version_code;
		
		if($mode=='INSTALL'){
			$table_structure_master_data = $this->Userdb_model->table_structure_master();
			$counttable = count($table_structure_master_data);
			
			if($emp_code!='')
				$count_array = $this->Userdb_model->table_structure_updation_select($device_id,$emp_code);
			else
				$count_array = $this->Userdb_model->table_structure_updation_select($device_id,$emp_code='');
			
			$count = count($count_array);
						
			if($count<1)
				$this->Userdb_model->table_structure_updation_insert($emp_code,$versionCode,$device_id);
		}
		else{
			if($emp_code != '')
				$count_array = $this->Userdb_model->table_structure_updation_select($device_id,$emp_code);
			else
				$count_array = $this->Userdb_model->table_structure_updation_select($device_id,$emp_code='');
				
			$count = count($count_array);
			
			if($count>0){
				$is_update = $count_array['is_update'];
				$user_db_version_code = $count_array['db_version_code'];
				
				$version_chk = intval(($versionCode-$user_db_version_code)*10);
				$chkval=1;
				if(($version_chk > intval($chkval)) && $is_update==1)
				{
					$this->db->close();
					$setup_db = "acednsproduct";
					$this->load->library('dbconn');
					$config = $this->dbconn->create_db_conn($setup_db);//calls library function with argument
					$this->load->database($config);
					$this->load->model('User_nick_name_model');
			
					$counttable_array = $this->User_nick_name_model->app_db_update_execution($user_db_version_code,$versionCode);
					$counttable = count($counttable_array);
				}
				else if($is_update == 1){
					$counttable_array = $this->Userdb_model->table_structure_master_need_update();
					$counttable = count($counttable_array);
					
					$result = $this->Userdb_model->table_structure_updation_updated_users();
					$no_of_updated_users = $result['no_of_updated_users'];
					
					if($no_of_updated_users == no_of_licensed_users){
						$this->Userdb_model->table_structure_master_update();
					}
				}
				else{
					$counttable=0;
				}
			}
		}
		
		if($counttable>0){
			$this->db->close();
			$setup_db = "acednsproduct";
			$this->load->library('dbconn');
			$config = $this->dbconn->create_db_conn($setup_db);//calls library function with argument
			$this->load->database($config);
			$this->load->model('User_nick_name_model');
			$url_data = $this->User_nick_name_model->app_url($nick_name);
			$previous_baseurl_app = $url_data['previous_baseurl_app'];
			$current_baseurl_app = $url_data['current_baseurl_app'];
			if($previous_baseurl_app!=$current_baseurl_app || $mode=='INSTALL')
				$baseurlchanged='Y';
			else
				$baseurlchanged='N';
			$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
						
			$contents.="<data>";
			$contents .='<table_name><![CDATA['.mb_convert_encoding($counttable_array['table_name'], 'UTF-8', 'UTF-8').']]></table_name>
						<table_structure><![CDATA['.mb_convert_encoding($counttable_array['table_structure'], 'UTF-8', 'UTF-8').']]></table_structure>
						<transaction><![CDATA['.mb_convert_encoding($counttable_array['is_transaction'], 'UTF-8', 'UTF-8').']]></transaction>
						<master><![CDATA['.mb_convert_encoding($counttable_array['is_master'], 'UTF-8', 'UTF-8').']]></master>
						<db_version><![CDATA['.mb_convert_encoding($versionCode, 'UTF-8', 'UTF-8').']]></db_version>
						<base_url_changed><![CDATA['.mb_convert_encoding($baseurlchanged, 'UTF-8', 'UTF-8').']]></base_url_changed>
						<current_baseurl_app><![CDATA['.mb_convert_encoding($current_baseurl_app, 'UTF-8', 'UTF-8').']]></current_baseurl_app>
						';
			$contents.="</data>";
			$contents .= "</recordset>";
			echo $contents;		
		}
		else{
			echo "0";
		}
	}
}
?>