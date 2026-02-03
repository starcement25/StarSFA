<?php
class User_nick_name_model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	function get_nick_name(){
		$query = $this->db->query("SELECT nick_name FROM user_details");
		return $query->result();
	}
	function logo_download($nick_name){
		$query = $this->db->query("SELECT logo FROM user_details WHERE nick_name='$nick_name'");
		return $query->result();
	}
	function setupuserdetails_download($nick_name,$emp_code,$last_update_time,$incremental_download){
		if($incremental_download=='no')
			$login_condition="";
		else
			$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		
		$query = $this->db->query("SELECT * FROM user_details WHERE nick_name='$nick_name'");
		return $query->row_array();
	}
	function app_url($nick_name){
		$query = $this->db->query("SELECT previous_baseurl_app,current_baseurl_app FROM user_details WHERE nick_name='$nick_name'");
		return $query->row_array();
	}
	function app_db_update_execution($user_db_version_code,$versionCode){
		$query = $this->db->query("SELECT * FROM app_db_update_execution WHERE db_version > ".$user_db_version_code."  						AND db_version <= ".$versionCode."  ORDER BY app_db_u_exe_id ASC");
		return $query->row_array();
	}
}
?>