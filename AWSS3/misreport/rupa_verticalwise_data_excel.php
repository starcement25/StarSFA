<?php
	/*define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	mysql_connect(SERVER,USER,PASSWORD);
	mysql_select_db("acedns_RUPA");*/
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$type = $_REQUEST['type'];
	$vertical = $_REQUEST['vertical'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	
	@$current_date = date('Y-m-d');
	
	if($type == 'today')
	{
		$condition = "VBED.operation_date LIKE '%".$current_date."%'";
		$report_details = 'Report: Today';
		$filename = 'Vertical Value Today';
	}
	else if($type == 'mtd')
	{
		$month = explode("-",$current_date);
		$condition = "(YEAR(VBED.operation_date)=2015 AND MONTH(VBED.operation_date) = 08)";
		$report_details = 'Report: MTD';
		$filename = 'Vertical Value MTD';
	}
	else
	{
		$condition = "(VBED.operation_date BETWEEN '".$start_date."' AND '".$end_date."')";
		$startdate = date('d-m-Y',strtotime($start_date));
		$enddate = date('d-m-Y',strtotime($end_date));
		$report_details = "Report: From ".$startdate." To ".$enddate;
		$filename = 'Vertical Value From '.$startdate.' To '.$enddate;
	}
	
		
	/*$sql_vertical_wise_data = "SELECT EM.emp_code, EM.emp_name, VBED.calls_made, VBED.productive, sum(VBED.qty) as quantity, round(((VBED.productive/VBED.calls_made)*100),2) as conversion_percentage FROM employee_master EM, vertical_branch_employeewise_details VBED WHERE VBED.emp_code=EM.emp_code AND VBED.vertical_value='".$vertical."' AND ".$condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";
	$res_vertical_wise_data = mysql_query($sql_vertical_wise_data);
	$total_vertical_rows = mysql_num_rows($res_vertical_wise_data);
	
	if($total_vertical_rows>0)
	{
		$setExcelName = $filename; 
					
		$contents = "Emp Name"."\t"."Calls Made"."\t"."Productive Calls"."\t"."Quantity PCP"."\t";
				
		$count = 1;
		$res_vertical_wise_data = mysql_query($sql_vertical_wise_data);
		while($row_vertical_wise_data = mysql_fetch_array($res_vertical_wise_data))
		{
			$total_calls_made += $row_vertical_wise_data['calls_made'];
			$total_productive_calls += $row_vertical_wise_data['productive'];
			$total_quantity += $row_vertical_wise_data['quantity'];
			
			$contents .= strip_tags(str_replace('"', '""', $row_vertical_wise_data['emp_name']))."\t";
			$contents .= strip_tags(str_replace('"', '""', $row_vertical_wise_data['calls_made']))."\t";
			$contents .= strip_tags(str_replace('"', '""', $row_vertical_wise_data['productive']))."\t";
			$contents .= strip_tags(str_replace('"', '""', $row_vertical_wise_data['quantity']))."\t";
			$contents .= strip_tags(str_replace('"', '""', $row_vertical_wise_data['conversion_percentage']))."\t";
			
			$rowLine .= $contents;
			$setData .= trim($rowLine)."\n";
			$count++;
		}
			$setData .= "Total"."\t"."\t".$total_calls_made."\t".$total_productive_calls."\t".$total_quantity."\t";
			$setData = str_replace("\r", "", $setData); 
	if ($setData == "") { $setData = "nno matching records foundn"; } 
	$setCounter = mysql_num_fields($setRec); //This Header is used to make data download instead of display the data 
	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=".$setExcelName."_Report.xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
	echo ucwords($setMainHeader)."\n".$setData."\n"; //- See more at: http://www.discussdesk.com/download-mysql-data-into-excel-file-in-php.htm#sthash.5bPI72JI.dpuf
		
		//echo "</table>";
	}
	else
	{
		echo "No records found";
	}*/
	
	
	$setCounter = 0; 
	$setExcelName = $filename; 
	$setSql = "SELECT EM.emp_name, VBED.calls_made, VBED.productive, sum(VBED.qty) as quantity, round(((VBED.productive/VBED.calls_made)*100),2) as conversion_percentage FROM employee_master EM, vertical_branch_employeewise_details VBED WHERE VBED.emp_code=EM.emp_code AND VBED.vertical_value='".$vertical."' AND ".$condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC"; 
	$setRec = mysql_query($setSql); 
	$setCounter = mysql_num_fields($setRec); 
	for ($i = 0; $i < $setCounter; $i++) 
	{ 
		$setMainHeader .= mysql_field_name($setRec, $i)."\t"; 
	} 
	while($rec = mysql_fetch_row($setRec)) 
	{
		 $rowLine = ''; 
		 foreach($rec as $value) 
		 { 
		 	if(!isset($value) || $value == "") 
			{ $value = "\t"; } 
			else 
			{ 
				//It escape all the special charactor, quotes from the data. 
				$value = strip_tags(str_replace('"', '""', $value)); 
				$value = '"' . $value . '"' . "\t"; 
			} 
			$rowLine .= $value; 
		 } 
			$setData .= trim($rowLine)."\n"; 
	} 
	$setData = str_replace("\r", "", $setData); 
	if ($setData == "") { $setData = "nno matching records foundn"; } 
	$setCounter = mysql_num_fields($setRec); //This Header is used to make data download instead of display the data 
	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=".$setExcelName."_Report.xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
	echo ucwords($setMainHeader)."\n".$setData."\n"; //- See more at: http://www.discussdesk.com/download-mysql-data-into-excel-file-in-php.htm#sthash.5bPI72JI.dpuf
?>