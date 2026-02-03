<?php
class API_log_model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	function insert_api_log($datetime,$emp_code,$url){		
		$url = urldecode($url);
		$sql = "INSERT INTO apicalllog SET date_time = '$datetime', emp_code = '$emp_code', url = '".addslashes($url)."'";
		$query = $this->db->query($sql);
		
		//$query = $this->db->query("SELECT * FROM apicalllog WHERE emp_code = '$emp_code' LIMIT 0,1");
		//return $query->result();
	}
}
?>