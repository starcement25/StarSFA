<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class Updateapp_6 extends REST_Controller{
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
	function updateapp_get(){
		$deviceId = $this->get('deviceId');
		$versionCode = $this->get('versionCode');
		$emp_code = $this->get('emp_code');
		
		$get_value = $this->function_required->return_no_days($val1='15',$val2='M');
		echo "<pre>";
		print_r($get_value);
		echo "</pre>";
		die;
		
		$rowempname = $this->Userdb_model->emp_name($emp_code);
		$emp_name = $rowempname['emp_name'];
		
		$rowselectappversion = $this->Userdb_model->app_version();
		$app_version_latest = $rowselectappversion['version_code'];
		$release_date = date('d/m/Y',strtotime($rowselectappversion['date']));
		
		$rowselect = $this->Userdb_model->app_updation($deviceId);
		$count = count($rowselect);
		
		if($count>0){
			if($versionCode > $rowselect['version_code']){
				if($this->Userdb_model->app_updation_update($deviceId,$versionCode)){
					echo "2";
				}
				else{
					echo "3";
				}
				
				$date=gmdate('d',strtotime('+329 minute'));
				$month=gmdate('m',strtotime('+329 minute'));
				$year=gmdate('Y',strtotime('+329 minute'));
				
				$hour=gmdate('H',strtotime('+329 minute'));
				$minute=gmdate('i',strtotime('+329 minute'));
				$second=gmdate('s',strtotime('+329 minute'));
				$update_date=$date.'/'.$month.'/'.$year;
				$update_time=$hour.':'.$minute.':'.$second;
				
				if($nick_name == 'RUPA'){
					$app_update_mail = '';
				}
				else{
					if(email_hierarchywise == 'yes'){
						if(email_hierarchy_level == 1){
							$app_update_mail='';
						}
						else{
							$app_update_mail=ORDEREMAILRECIPENTS;
						}
					}
					else{
						$app_update_mail=ORDEREMAILRECIPENTS;
					}
				}
				$mailsubj="ACEdns - ".strtoupper($nick_name)." APP Version $versionCode released on $release_date has been successfully updated to $emp_name";
				$emailbody="<html><head><title>App Updation</title></head>
							<body>ACEdns - ".strtoupper($nick_name)." APP Version $versionCode released on $release_date has been successfully updated to $emp_name - $emp_code on $update_date and @ $update_time.</table><br /><br />Regards<br />
	TEAM - ACEdns</body></html>";
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
				$sendmail=@mail($app_update_mail, $mailsubj, $emailbody, $headers,'-facedns@acedns.in');
			}
			
			if($rowselect['version_code'] == $versionCode && $rowselect['is_update'] == 1){
				echo "1-".$app_version_latest;
			}
			else if($versionCode < $rowselect['version_code']){
				echo "1-".$app_version_latest;
			}
			else{
				echo "0";
			}
		}
		else{
			if(mysql_query($this->Userdb_model->app_updation_insert($versionCode,$deviceId))){
				echo "4".'/'.$app_version_latest;
			}
			else{
				echo "3";
			}
		}
	}
}