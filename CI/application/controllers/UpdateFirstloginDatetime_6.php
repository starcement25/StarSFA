<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require  APPPATH . '/config.php';
//require  APPPATH . '/dbcon.php';

require APPPATH . '/libraries/REST_Controller.php';

class UpdateFirstloginDatetime_6 extends REST_Controller{
	function __construct() {
		parent::__construct();
		$nick_name = $this->get('nick_name');
		require APPPATH . '/controllers/config_setup.php';
		$this->load->library('dbconn');
		$config = $this->dbconn->create_db_conn($nick_name);//calls library function with argument
 		$this->load->database($config);
		$this->load->model('Userdb_model');
		$this->load->model('API_log_model');
		$this->load->library('function_required');
		require APPPATH . '/controllers/config_email_setup.php';
	}
	function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);
	
	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;
	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}
	function getReverseGeo($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
		// make the HTTP request
		$data = @file_get_contents($url);
		// parse the json response
		$jsondata = json_decode($data,true);
		
		//print_r($jsondata);
		// if we get a formatted_address array and the status was OK, get the addres
		if(is_array($jsondata )&& $jsondata['status']=='OK')
		{
			  $addr = $jsondata['results']['0']['formatted_address'];
		}		
		return  $addr;	
	}
	function updateLatlong($emp_code)
	{
		//For check and update in the location table
		$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsselectlatlong=mysql_query($sqlselectlatlong) or die(mysql_error()." Error in select zero latt longi: ".$sqlselectlatlong);
		$countselectlatlong=mysql_num_rows($rsselectlatlong);
		
		if($countselectlatlong>0)
		{
			$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
			$rslastlatlong=mysql_query($sqllastlatlong) or die(mysql_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
			$rowlastlatlong=mysql_fetch_array($rslastlatlong);
			$lastlatt=$rowlastlatlong['latt'];
			$lastlongi=$rowlastlatlong['longi'];
			
			$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$rsupdatelatlong=mysql_query($sqlupdatelatlong) or die(mysql_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	function updateFirstloginDatetime_get(){
		$emp_code = $this->get('emp_code');
		$date_time = $this->get('downloaddate');
		$date_time = str_replace('_',' ',$date_time);
		$device_id = $this->get('device_id');
		$days_first_login = $this->get('days_first_login');
		$db_version = $this->get('db_version');
		
		$rowempname = $this->Userdb_model->emp_master_details($emp_code);
		$emp_name = $rowempname['emp_name'];
		$branch_code = $rowempname['branch_code'];
		$vertical_value = $rowempname['vertical_value'];
		$reporting_to_immediate = $rowempname['reporting_to'];
		
		$rowselectversion = $this->Userdb_model->dbversion_details();
		$versionCode = $rowselectversion['version_code'];
		$release_date = date('d/m/Y',strtotime($rowselectversion['date']));
		
		$rowselect = $this->Userdb_model->table_structure_updation_select($device_id,$emp_code);
		$count = count($rowselect);
		if($count>0){
			$is_update = $rowselect['is_update'];
			$existed_db_version_code = $rowselect['db_version_code'];
			
			if($is_update==1){
				if($versionCode == $db_version){
					$this->Userdb_model->tblstruct_updation_updt_dbver_isupd($versionCode,$device_id,$emp_code);
				}
				
				$date=gmdate('d',strtotime('+329 minute'));
				$month=gmdate('m',strtotime('+329 minute'));
				$year=gmdate('Y',strtotime('+329 minute'));
				
				$hour=gmdate('H',strtotime('+329 minute'));
				$minute=gmdate('i',strtotime('+329 minute'));
				$second=gmdate('s',strtotime('+329 minute'));
				$update_date=$date.'/'.$month.'/'.$year;
				$update_time=$hour.':'.$minute.':'.$second;
				
				if(email_hierarchywise=='yes'){
					if(email_hierarchy_level == 1){
						$db_update_mail='';
					}
					else{
					 	$db_update_mail=ORDEREMAILRECIPENTS;
					}
				}
				else{
					$db_update_mail=ORDEREMAILRECIPENTS;
				}
				
				$mailsubj="ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name";
				$emailbody="<html><head><title>Db Updation</title></head>
							<body>ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name - $emp_code on $update_date @ $update_time.</table><br /><br />Regards<br />
	TEAM - ACEdns</body></html>";
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
				$sendmail = @mail($db_update_mail, $mailsubj, $emailbody, $headers,'-facedns@acedns.in');
			}
		}
		else{
			$this->Userdb_model->table_structure_updation_insert($emp_code,$versionCode,$device_id);
		}
		
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = base_url()."http://salesmpower.acedns.in/CI/UpdateFirstloginDatetime_6/updateFirstloginDatetime/nick_name/$nick_name/emp_code/$emp_code/deviceId/$device_id/downloaddate/$date_time/days_first_login/$days_first_login/db_version/$db_version/X-API-KEY/abcd1234";
		$this->API_log_model->insert_api_log($datetime,$emp_code,$url);
		$this->db->close();		
	}
}
?>