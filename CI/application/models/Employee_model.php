<?php
class Employee_model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	function retrieve_data($emp_code){
		//$this->db->select("post_id,post_title,post_content"); 
		//$this->db->from('post_tbl');
		$query = $this->db->query("SELECT emp_code, emp_name FROM employee_master WHERE emp_code='$emp_code'");
		return $query->result();
		/*foreach ($query->result() as $row)
		{
				echo $row->title;
				echo $row->name;
				echo $row->email;
		}*/
		
		//echo 'Total Results: ' . $query->num_rows();
	}
}
?>