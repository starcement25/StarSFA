<?php
class Userdb_model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	function db_version(){		
		$query = $this->db->query("SELECT version_code  FROM db_version");
		return $query->result();
	}
	function table_structure_master(){
		$query = $this->db->query("SELECT * FROM table_structure_master ORDER BY t_structure_id");
		return $query->row_array();
	}
	function table_structure_updation_select($device_id,$emp_code){
		$query = $this->db->query("SELECT * FROM table_structure_updation WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'");
		return $query->row_array();
	}
	function table_structure_updation_insert($emp_code,$versionCode,$device_id){
		$sql = "INSERT INTO table_structure_updation SET
				emp_code='".$emp_code."',
				db_version_code='".$versionCode."',
				device_id='".$device_id."',
				is_update='0'";
		//$query = $this->db->query($sql);
	}
	function table_structure_master_need_update(){
		$query = $this->db->query("SELECT * FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id");
		return $query->row_array();
	}
	function emp_changepassword($newpassword,$emp_code){
		$query = $this->db->query("SELECT employee_master.emp_code,employee_master.emp_name,employee_master.sale_access,changepassword.newpassword,
		   changepassword.deviceid FROM employee_master,changepassword WHERE employee_master.emp_code=changepassword.emp_code 
			AND changepassword.newpassword='".$newpassword."' AND changepassword.emp_code='".$emp_code."'");
		return $query->row_array();
	}
	function device_id($emp_code){
		$query = $this->db->query("SELECT CH.deviceid FROM employee_master EM,changepassword CH WHERE EM.emp_code=CH.emp_code	AND CH.emp_code='".$emp_code."'");
		return $query->row_array();
	}
	function empname_chngpassword($deviceid){
		$query = $this->db->query("SELECT EM.emp_name from employee_master EM,changepassword CH where EM.emp_code=CH.emp_code AND CH.deviceid='".$deviceid."'");
		return $query->row_array();
	}
	function updatedevice_id($emp_code,$deviceId){
		$sql = "UPDATE changepassword SET
			deviceid='".$deviceId."' 
			WHERE emp_code='".$emp_code."'";
		return $query = $this->db->query($sql);
	}
	function validatephoneno($phone_no){
		$query = $this->db->query("SELECT prospect_name,pin,street_name,street_no,building_no,apartment_no,phone_no,email,oil_used FROM 
			product_promotion WHERE phone_no='".$phone_no."'");
		return $query->row_array();
	}
	function emp_name($emp_code){
		$query = $this->db->query("SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'");
		return $query->row_array();
	}
	function app_version(){
		$query = $this->db->query("SELECT * FROM app_version");
		return $query->row_array();
	}
	function app_updation($deviceId){
		$query = $this->db->query("SELECT * FROM app_updation WHERE device_id='".$deviceId."'");
		return $query->row_array();
	}
	function app_updation_update($deviceId,$versionCode){
		$sql = "UPDATE app_updation SET
				version_code='".$versionCode."',
				is_update='0'
				WHERE device_id='".$deviceId."'";
		return $query = $this->db->query($sql);
	}
	function app_updation_insert($versionCode,$deviceId){
		$sql = "INSERT INTO app_updation 
				SET	version_code='".$versionCode."',	device_id='".$deviceId."',	is_update='0'";
		return $query = $this->db->query($sql);
	}
	function email_id($column_name){
		$query = $this->db->query("SELECT admin_email_id,account_email_id ".$column_name." FROM company_master WHERE comp_name='".$nick_name."'");
		return $query->row_array();
	}
	function emp_hierarchy_email($employee_upper_hierarchy){
		$query = $this->db->query("SELECT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.")");
		return $query->row_array();
	}
	function reporting_to($emp_code){
		$query = $this->db->query("SELECT reporting_to FROM employee_master WHERE emp_code = '".$emp_code."'");
		return $query->row_array();
	}
	function immediate_email($emp_code){
		$query = $this->db->query("SELECT email FROM employee_master WHERE FIND_IN_SET(emp_code, '".$reporting_to_immediate."')");
		return $query->row_array();
	}
	function dbbackupcheck($deviceId){
		$query = $this->db->query("SELECT * FROM dbbackupcheck  WHERE device_id='".$deviceId."'");
		return $query->row_array();
	}
	function dbbackupcheck_insert($emp_code,$deviceId){
		$sql = "INSERT INTO dbbackupcheck SET emp_code='".$emp_code."', device_id='".$deviceId."', is_checked='0'";
		return $query = $this->db->query($sql);
	}
	function update_dbbackupcheck($deviceId){
		$sql = "UPDATE dbbackupcheck SET is_checked='0' WHERE device_id='".$deviceId."'";
		return $query = $this->db->query($sql);
	}
	function dbbackupcheck_isdelete($emp_code,$deviceId){
		$query = $this->db->query("SELECT is_delete FROM dbbackupcheck  WHERE emp_code='".$emp_code."' and device_id='".$deviceId."'");
		return $query->row_array();
	}
	function dbbackupcheck_update_isdelete($emp_code,$deviceId){
		$sql = "UPDATE dbbackupcheck SET is_delete='0' WHERE emp_code='".$emp_code."' and device_id='".$deviceId."'";
		return $query = $this->db->query($sql);
	}
	function changepassword_update_deviceid_regid($emp_code){
		$sql = "UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '".$emp_code."'";
		return $query = $this->db->query($sql);
	}
	function emp_master_details($emp_code){
		$query = $this->db->query("SELECT * FROM employee_master WHERE emp_code='".$emp_code."'");
		return $query->row_array();
	}
	function dbversion_details(){
		$query = $this->db->query("SELECT * FROM db_version");
		return $query->row_array();
	}
	function tblstruct_updation_updt_dbver_isupd($versionCode,$device_id,$emp_code){
		$sql = "UPDATE table_structure_updation SET
				db_version_code='".$versionCode."',
				is_update='0'
				WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'";
		return $query = $this->db->query($sql);
	}
	function empmaster_countreportingto($emp_code){
		$query = $this->db->query("SELECT COUNT(emp_code) AS total_emp_code FROM employee_master WHERE FIND_IN_SET('".$emp_code."', reporting_to)");
		return $query->row_array();
	}
	function rdsmaster_rdscode($emp_code){
		$query = $this->db->query("SELECT rds_code FROM rds_master WHERE emp_code='".$emp_code."'");
		return $query->row_array();
	}
	function rdsmaster_rdscode_hierarchy($employee_hierarchy){
		$query = $this->db->query("SELECT rds_code FROM rds_master WHERE emp_code IN(".$employee_hierarchy.")");
		return $query->row_array();
	}
	function activity_log($rds_code,$emp_code,$rds_list,$employee_hierarchy,$val){
		if($val == 1){
			$query = $this->db->query("SELECT * FROM activity_log WHERE mis_updated_flag_app='0' AND 
				(rds_code IN(".$rds_list.") OR SUBSTRING(transaction_id,2,5) IN (".$employee_hierarchy."))");
		}
		else if($val == 2){
			$query = $this->db->query("SELECT * FROM activity_log WHERE updated_flag_app='0' AND (rds_code='".$rds_code."' OR SUBSTRING(transaction_id,2,5)='".$emp_code."' 
				OR SUBSTRING(transaction_id,3,5)='".$emp_code."' OR receiver_code='".$emp_code."')");
		}
		return $query->row_array();
	}
	function activitylog_transid($emp_code){
		$query = $this->db->query("SELECT transaction_id FROM activity_log WHERE 	updated_flag_app='0' AND receiver_code='".$emp_code."'");
		return $query->row_array();
	}
	function update_activitylog($emp_code,$rds_code,$val){
		if($val == 1){
			$sql = "UPDATE activity_log SET updated_flag_app='1' WHERE receiver_code='".$emp_code."'";
		}
		else if($val == 2){
			$sql = "UPDATE activity_log SET updated_flag_app='1' WHERE receiver_code IS NULL AND (rds_code='".$rds_code."' OR SUBSTRING(transaction_id,2,5)='".$emp_code."' OR SUBSTRING(transaction_id,3,5)='".$emp_code."')";
		}
		return $query = $this->db->query($sql);
	}
	function update_changepassword_regid($emp_code,$registrationid){
		$sql = "UPDATE changepassword SET
				registrationid='".$registrationid."' 
				WHERE emp_code='".$emp_code."'";
		return $query = $this->db->query($sql);
	}
	function update_changepassword_clearregid($emp_code,$registrationid){
		$sql = "UPDATE changepassword SET
				registrationid='' 
				WHERE emp_code='".$emp_code."'";
		return $query = $this->db->query($sql);
	}
}
?>