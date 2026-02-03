<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Phoneno_validation extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
	}
	function validatephoneno_get(){
		$phone_no = $this->get('phone_no');
		$result = $this->Userdb_model->validatephoneno($phone_no);
		$count = count($result);
		if($count>0){
			$prospect_name = $result['prospect_name'];
			$pin = $result['pin'];
			$street_name = $result['street_name'];
			$street_no = $result['street_no'];
			$building_no = $result['building_no'];
			$apartment_no = $result['apartment_no'];
			$email = $result['email'];
			$oil_used = $result['oil_used'];
			
			$contents  = 'Name: '.(($prospect_name!='')?$prospect_name: ' ')."#";
			$contents  .= 'PIN Code: '.(($pin!='')?$pin: ' ')."#";
			$contents  .= 'Street Name: '.(($street_name!='')?$street_name: ' ')."#";
			$contents  .= 'Street No: '.(($street_no!='')?$street_no: ' ')."#";
			$contents  .= 'Building No: '.(($building_no!='')?$building_no: ' ')."#";
			$contents  .= 'Apartment No: '.(($apartment_no!='')?$apartment_no: ' ')."#";
			$contents  .= 'Email Address: '.(($email!='')?$email: ' ')."#";
			$contents  .= 'OIL used: '.(($oil_used!='')?$oil_used: ' ');
			
			echo '1#'.$contents;
		}
		else{
			echo "0";
		}
	}
}
?>