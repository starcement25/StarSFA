<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}

$sqlstate="SELECT state FROM employee_master WHERE emp_code='".$emp_code."'";
$rsstate=mysqli_query($link,$sqlstate);
$rowstate=mysqli_fetch_assoc($rsstate);
$state_name=$rowstate['state'];

	$sql_weightage = "SELECT * FROM state_product_wise_weightage WHERE acedns='yes' and state_name='".$state_name."'";
	$res_weightage = mysqli_query($link,$sql_weightage);
	$count=mysqli_num_rows($res_weightage);
	//print_r($vertical_array);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		while($row_weightage = mysqli_fetch_assoc($res_weightage)){
			$state_name = $row_weightage['state_name'];
			$prod_code=$row_weightage['prod_code'];
			$UOM1=$row_weightage['UOM1'];
			$UOM2=$row_weightage['UOM2'];
			$weightage_conversio1=$row_weightage['weightage_conversio1'];
			$weightage_conversion2=$row_weightage['weightage_conversion2'];
			//$countapproval++;
			$res_data[] = array("state_name"=>$state_name,"prod_code"=>$prod_code,"UOM1"=>$UOM1,"UOM2"=>$UOM2,"weightage_conversio1"=>$weightage_conversio1,"weightage_conversion2"=>$weightage_conversion2);
			
		}
			$countcolumns='6';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something Went Wrong." );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/state-sku-wise-weightage-json.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
