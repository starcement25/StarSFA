<?php	
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_RUPA");
	$link=mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB,$link);

	//print_r($_REQUEST);
	if($_REQUEST['mode']=='download_KYC')
	{
		$emp_code = $_REQUEST['emp_name'];
		$start_date = $_REQUEST['start_date'];
		$end_date = $_REQUEST['end_date'];
		
		if($emp_code=='all') $emp_condition='';
		else				 $emp_condition=" SUBSTRING(survey_id,3,5)='".$emp_code."' AND ";
		
		$header = "Customer Name"."\t"."Contact Person"."\t"."Mobile No."."\t"."Address"."\t"."Email Id"."\t"."Date Of Birth"."\t"."Spouse Name"."\t"."Date Of Anniversary"."\t"."Employee name";
		$sqlsurvey_id="SELECT DISTINCT survey_id FROM survey_output 
						WHERE ".$emp_condition." DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') 
						BETWEEN '".$start_date."' AND '".$end_date."' ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %h:%i:%s') DESC";
		$rssurvey_id=mysqli_query($link,$sqlsurvey_id);
		$row_id_array=array();
		$survey_id_array=array();
		while($rowsurvey_id=mysqli_fetch_assoc($rssurvey_id))
		{
			$survey_id=$rowsurvey_id['survey_id'];
			$sqlvalue="SELECT SO.value,SO.row_id FROM survey_output SO,survey_input SI WHERE SO.row_id=SI.row_id AND SO.survey_id='".$survey_id."' 
					ORDER BY SI.display_order ASC";
			$rsvalue=mysqli_query($link,$sqlvalue);
			while($rowvalue=mysqli_fetch_assoc($rsvalue)){
				$row_id=$rowvalue['row_id'];
				if(!in_array($row_id,$row_id_array))
				{
					array_push($row_id_array,$row_id);
				}
				
				if($row_id=='RA014')	${customer_name.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA013')	${anniversary.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA012')	${spouse.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA011')	${birth.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA010')	${email.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA007')	${address.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA006')	${mobile.$row_id.$survey_id}=$rowvalue['value'];
				if($row_id=='RA004')	${contact.$row_id.$survey_id}=$rowvalue['value'];
			}
			array_push($survey_id_array,$survey_id);
		}
		//print_r($row_id_array);
		foreach($survey_id_array as $survey_id_val)
		{
			$emp_code_survey=substr($survey_id_val,2,5);

			$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code_survey."'";
			$rsemp=mysqli_query($link,$sqlemp);
			$rowemp=mysqli_fetch_assoc($rsemp);
			$emp_name=$rowemp['emp_name'];
			foreach($row_id_array as $row_id_val)
			{
				if($row_id_val=='RA014'){
					$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".${customer_name.$row_id_val.$survey_id_val}."'";
					$rscustomer=mysqli_query($link,$sqlcustomer);	
					$rowcustomer=mysqli_fetch_assoc($rscustomer);
					$customer_name=$rowcustomer['customer_name'];
					$content .= $customer_name."\t";
				}
				
				if($row_id_val=='RA004')	$content .= ${contact.$row_id_val.$survey_id_val}."\t";
				if($row_id_val=='RA006')	$content .= ${mobile.$row_id_val.$survey_id_val}."\t";
				if($row_id_val=='RA007')	$content .= ${address.$row_id_val.$survey_id_val}."\t";
				if($row_id_val=='RA010')	$content .= ${email.$row_id_val.$survey_id_val}."\t";
				if($row_id_val=='RA011')	$content .= ${birth.$row_id_val.$survey_id_val}."\t";
				if($row_id_val=='RA012')	$content .= ${spouse.$row_id_val.$survey_id_val}."\t";
				if($row_id_val=='RA013')	$content .= ${anniversary.$row_id_val.$survey_id_val}."\t";
			}
			$content .= $emp_name."\t";
			$content .= "\n";
		}

		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=\"KYC_Report.xls\""); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$content;
	}
?>