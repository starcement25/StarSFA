<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		define("DB","acedns_STAR");
		$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	$sqllead="SELECT * FROM lead_generation_master order BY lead_generation_id DESC LIMIT 0,20";
	$rsL=mysqli_query($link,$sqllead);
	$lead_generation_id_array=array();
	while($rowL=mysqli_fetch_assoc($rsL)){
		$lead_generation_id =$rowL['lead_generation_id'];
		${'emp_code'.$lead_generation_id}=$rowL['emp_code'];
		${'lead_type'.$lead_generation_id}=$rowL['lead_type'];
		${'self_other'.$lead_generation_id}=$rowL['self_other'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to,district,HQ,region,zone FROM employee_master WHERE emp_code = '".${'emp_code'.$lead_generation_id}."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		${'emp_name'.$lead_generation_id}= $row_emp_details['emp_name'];
		
		echo $sql_surveyid = "SELECT survey_id,row_id,value,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') AS survey_date,SUBSTRING(survey_id,-10,2) AS survey_month FROM survey_output where `value` LIKE '%".$lead_generation_id."%' ORDER BY survey_id DESC LIMIT 0,1";

        $res_surveyid = mysqli_query($link,$sql_surveyid);
        $row_surveyid = mysqli_fetch_assoc($res_surveyid);
		${'survey_id'.$lead_generation_id} = $row_surveyid['survey_id'];
		${'survey_date'.$lead_generation_id} = $row_surveyid['survey_date'];
		${'survey_month'.$lead_generation_id} = $row_surveyid['survey_month'];
		
		$sql_emp_lat_long = "SELECT latt, longi FROM location WHERE trans_id = '".${'survey_id'.$lead_generation_id}."'";
		$res_emp_lat_long = mysqli_query($link,$sql_emp_lat_long);
		$row_emp_lat_lomg = mysqli_fetch_assoc($res_emp_lat_long);
		${'lat'.$lead_generation_id} = $row_emp_lat_lomg['latt'];
		${'longi'.$lead_generation_id} = $row_emp_lat_lomg['longi'];
		
		$PO_method=$rowL['PO_method'];
		$ship_to_party=$rowL['ship_to_party'];
		$sold_to_party=$rowL['sold_to_party'];
		$material_number=$rowL['material_number'];
		$valid_to_date=$rowL['valid_to_date'];
		$customer_reference_date=$rowL['customer_reference_date'];
		$customer_reference_no=$rowL['customer_reference_no'];
		$sales_org = $rowL['sales_org'];
		$document_type = $rowL['document_type'];
		if(!in_array($lead_generation_id,$lead_generation_id_array))
			{
				array_push($lead_generation_id_array,$lead_generation_id);
			}
			
	}
	mysqli_close($link);
print_r($lead_generation_id_array);
//exit();

	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$linkdest=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");

	
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
$sl_no=1;
	
	foreach($lead_generation_id_array as $lead_generation_id_val){
			$lead_type=${'lead_type'.$lead_generation_id_val};
			$emp_name=${'emp_name'.$lead_generation_id_val};
			$survey_date=${'survey_date'.$lead_generation_id_val};
			$survey_month=${'survey_month'.$lead_generation_id_val};
			$lat=${'lat'.$lead_generation_id_val};
			$longi=${'longi'.$lead_generation_id_val};
			$self_other=${'self_other'.$lead_generation_id_val};
			
			
			echo $sqlinsert="INSERT INTO ZSS_Quot_Ord SET Sr_No='".$sl_no."',
									Unique_Store_ID='".$lead_generation_id_val."',
									Emp_Name='".$emp_name."',
									Survey_Date='".$survey_date."',
									Survey_Month='".$survey_month."',
									Lattitude='".$lat."',
									Longitude='".$longi."',
									Lead_Type='".$lead_type."',
									Self_Others='".$self_other."'";
			mysqli_query($linkdest,$sqlinsert);
		$sl_no++;
	}
		mysqli_close($linkdest);
?>
