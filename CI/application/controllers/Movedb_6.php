<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Movedb_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		require APPPATH . '/controllers/config_setup.php';
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
		$this->load->library('function_required');
	}
	function movedb_get(){
		$deviceId = $this->get('deviceId');
		$folderName = $nick_name;
		
		if (!file_exists($folderName)){
			mkdir("dbbackup/$folderName");
			chmod("dbbackup/$folderName", 0777);
		}
		
		$upload_dir="dbbackup/".$folderName.'/';
		$file_name = $_FILES['file']['name'];
		$tmp_name=$_FILES['file']['tmp_name'];
		$file_size=$_FILES['file']['size'];
		$file_type 	= 'general';
		
		if($file_name != "")// && $file_size < 2097152
		{
			$upload_file = $upload_dir.$file_name;
			if(move_uploaded_file($tmp_name,$upload_file))
			{
				if($this->Userdb_model->update_dbbackupcheck($deviceId)){
					echo "1";
				}
				else{
					echo "0";
				}
			}
		}//end of size and file checking
	}
}

?>