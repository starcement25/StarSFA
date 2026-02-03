<?php
ob_start();
session_start();
require("adminUtils.php");

$emp_code = $_REQUEST['emp_code'];
$customer_Array = $_REQUEST['customer_Array'];

if($emp_code != ''){
	$sqlchkemp="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$emp_code."' and acedns='Y'";
	$rschkemp=mysqli_query($link,$sqlchkemp);
	$coutchkemp=mysqli_num_rows($rschkemp);
	if($coutchkemp==0)
	{
		echo 'Tagged employee not exists';
	}
	else
	{
		$rowchkemp=mysqli_fetch_assoc($rschkemp);
		$emp_code_internal=$rowchkemp['emp_code'];
		$customer_string="'".implode ( "', '", $customer_Array )."'";

		$customer_name_string='';
		$sqltaggingexistall="SELECT customer_name FROM customer_master WHERE customer_code IN(SELECT customer_code FROM market_survey_tagging 
						WHERE emp_code = '".$emp_code_internal."' AND customer_code IN(".$customer_string."))";
		$rstaggingexistall=mysqli_query($link,$sqltaggingexistall);
		$couttaggingexistall=mysqli_num_rows($rstaggingexistall);
		if($couttaggingexistall==0)
		{							
			foreach($customer_Array as $customer_id)
			{
			/*$sqltaggingexist="SELECT customer_name FROM customer_master WHERE customer_code IN(SELECT customer_code FROM market_survey_tagging 
							WHERE emp_code = '".$emp_code_internal."' AND customer_code IN(".$customer_string."))";*/
			$sqltaggingexist="SELECT customer_name FROM customer_master WHERE customer_code IN(SELECT customer_code FROM market_survey_tagging WHERE emp_code = '".$emp_code_internal."' AND customer_code='".$customer_id."')";	
			$rstaggingexist=mysqli_query($link,$sqltaggingexist);
			$couttaggingexist=mysqli_num_rows($rstaggingexist);
				if($couttaggingexist==0)
				{			
				/*$sql_del_prev_tagging = "DELETE  FROM market_survey_tagging WHERE emp_code = '".$emp_code_internal."'";
				$res_del_prev_tagging = mysqli_query($link,$sql_del_prev_tagging);*/			
					$sql_insert_new_tagging = "INSERT INTO market_survey_tagging SET customer_code = '".$customer_id."', emp_code = '".$emp_code_internal."', tagged_date_time = current_timestamp";
					$res_insert_new_tagging = mysqli_query($link,$sql_insert_new_tagging);
				}
			}
				echo "<br /><br /><b>Employee tagging done for the selected customers<b>";	

		}
		else
		{
			while($rowtaggingexistall = mysqli_fetch_assoc($rstaggingexistall)){
			  $customer_name_string .= $rowtaggingexistall['customer_name'].',';
			}
			$customer_name_string=substr($customer_name_string,0,-1);
			echo "<b>Tagging already exists for the customer ".$customer_name_string."<b><br />";
		}
	}
}
?>