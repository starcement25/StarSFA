<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Logodownload extends REST_Controller{
	function __construct() {
		parent::__construct();
		$this->load->helper('file');
		$setup_db = "acednsproduct";
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($setup_db);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('User_nick_name_model');
	}
	function logo_get(){
		if(!$this->get('nick_name'))
        {
            $this->response(NULL, 400);
        }
		$nick_name=$this->get('nick_name');
		$logo_data = $this->User_nick_name_model->logo_download($nick_name);
		if($logo_data)
		{
			$logo = $logo_data[0]->logo;
			$logourl = "../logo/$logo";
			header("Content-type: image/png"); 
			header("Content-Disposition: attachment; filename=$logo");
			readfile("$logourl");
		}
	}
}
?>