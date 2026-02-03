<?php
date_default_timezone_set("Asia/Kolkata");
class User_setup_model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	function update_api_key($nick_name){
		$rest_api_key = $nick_name.date('YmdHis').rand(100000,999999);
		$query = $this->db->query("TRUNCATE rest_api_key");
		$query = $this->db->query("INSERT INTO rest_api_key SET `key`='$rest_api_key'");
		return;
	}
}
?>