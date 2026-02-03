<?php
class Update_api_key extends CI_Controller {
	function __construct() {
        parent::__construct();
        // path to simple_html_dom
		
    }
	function key_update() {
		
		$this->load->database();
		$this->load->model('User_nick_name_model');
		$user_nick_name = $this->User_nick_name_model->get_nick_name();
		$this->db->close();
		
		foreach($user_nick_name as $nick_name_obj){
			$nick_name = $nick_name_obj->nick_name;
			$this->load->library('dbconn');
			$config = $this->dbconn->create_db_conn($nick_name,TRUE);
			$this->load->database($config);
			$this->load->model('User_setup_model'); // load model
			$update_key_data = $this->User_setup_model->update_api_key($nick_name);//calls model function
			$this->db->close();
		}
		/*$this->load->database(); 
   		$this->load->model('User_setup_model'); // load model
		$update_key_data = $this->User_setup_model->update_api_key();//calls model function 
		//$this->response($update_key_data, 200);
		if($this->db->affected_rows() >0)
		{
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILURE';
		}*/
		echo "Key Generated Successfully";
    }
}
?>