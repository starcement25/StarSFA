<?php

ob_start();

session_start();

require("adminUtils.php");

$customer_code = $_REQUEST['customer_code'];

$opttype=$_REQUEST['opttype'];



if($opttype=='bargainapprove')

{

	echo "<select name=\"sauda_no\" id=\"sauda_no\">";

	echo "<option value=\"\">Select</option>";

	$sqlbargaindetails = "SELECT DISTINCT  DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') AS bargain_date_time,dns_sauda_no,sauda_no FROM 

						DO_master 

						WHERE customer_code=".$customer_code." AND is_approved='no' ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%y %H:%i:%s') DESC";

	$resbargaindetails = mysqli_query($link,$sqlbargaindetails);

	while($rowbargaindetails = mysqli_fetch_assoc($resbargaindetails)){

		$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];

		$sauda_no = $rowbargaindetails['sauda_no'];

		echo "<option value=\"'".$sauda_no."'\">".$dns_sauda_no."</option>";

	}

	//$emp_code_string = rtrim($emp_code_string,",");

	//echo "<option value=\"".$emp_code_string."\">All</option>";

	echo "</select>";

}
else if($opttype=='bargaintransfer')
{
	echo "<select name=\"sauda_no\" id=\"sauda_no\">";

	echo "<option value=\"\">Select</option>";

	$sqlbargaindetails = "SELECT DISTINCT  DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') AS bargain_date_time,dns_sauda_no,sauda_no FROM 

						DO_master 

						WHERE customer_code=".$customer_code." AND is_approved='yes' AND status='no' 
						AND dns_sauda_no LIKE 'BR/%' ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%y %H:%i:%s') ASC";

	$resbargaindetails = mysqli_query($link,$sqlbargaindetails);

	while($rowbargaindetails = mysqli_fetch_assoc($resbargaindetails)){

		$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];

		$sauda_no = $rowbargaindetails['sauda_no'];

		echo "<option value=\"'".$sauda_no."'\">".$dns_sauda_no."</option>";

	}

	//$emp_code_string = rtrim($emp_code_string,",");

	//echo "<option value=\"".$emp_code_string."\">All</option>";

	echo "</select>";
}
else if($opttype=='bargaintransferopening')
{
	echo "<select name=\"sauda_no\" id=\"sauda_no\">";

	echo "<option value=\"\">Select</option>";

	$sqlbargaindetails = "SELECT DISTINCT  DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') AS bargain_date_time,dns_sauda_no,sauda_no FROM 

						DO_master 

						WHERE customer_code=".$customer_code." AND is_approved='yes' AND status='no' 
						AND dns_sauda_no LIKE 'B/%' ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%y %H:%i:%s') ASC";

	$resbargaindetails = mysqli_query($link,$sqlbargaindetails);

	while($rowbargaindetails = mysqli_fetch_assoc($resbargaindetails)){

		$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];

		$sauda_no = $rowbargaindetails['sauda_no'];

		echo "<option value=\"'".$sauda_no."'\">".$dns_sauda_no."</option>";

	}

	//$emp_code_string = rtrim($emp_code_string,",");

	//echo "<option value=\"".$emp_code_string."\">All</option>";

	echo "</select>";


}
else
{

	echo "<select name=\"sauda_no\" id=\"sauda_no\">";

	echo "<option value=\"\">Select</option>";

	$sqlbargaindetails = "SELECT DISTINCT  DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') AS bargain_date_time FROM DO_transaction 

						WHERE customer_code=".$customer_code." AND DO_status='' ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%d-%m-%y %H:%i:%s') DESC";

	$resbargaindetails = mysqli_query($link,$sqlbargaindetails);

	while($rowbargaindetails = mysqli_fetch_assoc($resbargaindetails)){

		$bargain_date_time = $rowbargaindetails['bargain_date_time'];

		echo "<option value=\"'".$bargain_date_time."'\">".$bargain_date_time."</option>";

	}

	//$emp_code_string = rtrim($emp_code_string,",");

	//echo "<option value=\"".$emp_code_string."\">All</option>";

	echo "</select>";

}

mysqli_close($link);

?>