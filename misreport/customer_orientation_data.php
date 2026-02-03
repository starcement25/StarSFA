<?php


ob_start();
session_start();

if(strpos(strtolower($_SESSION['sale_access']),'vendor')!=false && (strtoupper($_SESSION['nick_name'])== 'STAR' || strtoupper($_SESSION['nick_name'])== 'START'))
	{
		require("adminUtils_branding.php");
	}
	else
	{
		require("adminUtils.php");
	}
	
	
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
//$survey_type = $_REQUEST['survey_type'];
//echo $employee;
function getReverseGeoAdd($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc";
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


$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

//echo $start_date;
$sql_distinct_date = "SELECT * FROM `customer_orientation` WHERE emp_code IN(".$employee.") AND Date_Format(`date_time`,'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'
					ORDER BY `date_time` DESC";
					//echo $sql_distinct_date;
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

if($total_rows>0){
	?>
        <form action="complaint_report.php" method="post">
    <input type="hidden" name="mode" value="PO_no_update" />

    <table border="1" style="border-collapse:collapse;" class="border" width="90%">
 		<tr>
      	  <td colspan="39" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
        
      <tr class="TDHEAD">
        <td width="3%">Employee Code</td>
        <td width="3%">Employee Name</td>
        <td width="3%">Date Time</td>
 
      </tr>
    <?php
	while($row_survey_ouput = mysqli_fetch_assoc($res_distinct_date)){
	
		$emp_code = $row_survey_ouput['emp_code'];
		$date_time = $row_survey_ouput['date_time'];
		
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to,district,HQ FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$emp_name = $row_emp_details['emp_name'];
		$reporting_to = $row_emp_details['reporting_to'];
		$district = $row_emp_details['district'];
		$HQ = $row_emp_details['HQ'];
		

		
		echo "<tr>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$date_time."</td>
				
				";
	
		 echo "</tr>";
	 }
}
	else{
		echo "<tr><td colspan='39' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br>
    <br>
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>			
    <?php
mysqli_close($link);
?>
