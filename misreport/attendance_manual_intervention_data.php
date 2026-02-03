<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$opt_type=$_REQUEST['opt_type'];
if($opt_type=='atttimeupdate')
{
	$emp_code=$_REQUEST['emp_code'];
	$newdate=$_REQUEST['newdate'];
	$attendance_time=$_REQUEST['attendance_time'];
	$attendance_date=$newdate.' '.$attendance_time;
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
	mysqli_query($link,"SET AUTOCOMMIT=0");
	mysqli_query($link,"START TRANSACTION");
	
	$trans_id='A'.$emp_code.str_replace('-','',$newdate).str_replace(':','',$attendance_time);
	$sqlinsertattlocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='1',
									longi='1',
									date='".$attendance_date."',
									updatetime='".$location_date."'";
	//For Insert into the attendance table for new trans id
	$sqlinsertattendance="INSERT INTO attendence SET emp_code='".$emp_code."',
						 trans_id='".$trans_id."',
						 date='".$attendance_date."'";	
	if(mysqli_query($link,$sqlinsertattlocation) && mysqli_query($link,$sqlinsertattendance))
	{
		mysqli_query($link,"COMMIT");
	?>	
    <table  style="border-collapse:collapse;" class="border" width="60%">
      <tr class="TDHEAD_SUB">
      	<td colspan="5" align="center">Attendance successfull.</td>
      </tr>
     </table> 
     <?php 
	}
	else
	{
	?>
     <table  style="border-collapse:collapse;" class="border" width="60%">
      <tr class="TDHEAD_SUB">
      	<td colspan="17" align="center">Attendance unsuccessful.</td>
      </tr>
     </table> 
    <?php	
	}
  }
if($opt_type=='checkouttimeupdate')
{
	$emp_code=$_REQUEST['emp_code'];
	$newdate=$_REQUEST['newdate'];
	$checkout_time=$_REQUEST['checkout_time'];
	$checkout_date=$newdate.' '.$checkout_time;
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
	$trans_id='CH'.$emp_code.str_replace('-','',$newdate).str_replace(':','',$checkout_time);
	$sqlchkattendance="SELECT trans_id FROM location WHERE trans_id LIKE 'A%' AND SUBSTRING(date,1,10)='".$newdate."' AND emp_code='".$emp_code."'";
	$rschkattendance=mysqli_query($link,$sqlchkattendance);
	$cntchkattendance=mysqli_num_rows($rschkattendance);
	if($cntchkattendance >0)
	{
		$sqlinsertattlocation="INSERT INTO location SET emp_code='".$emp_code."',
								trans_id='".$trans_id."',
								latt='1',
								longi='1',
								date='".$checkout_date."',
								updatetime='".$location_date."'";
		//For Insert into the attendance table for new trans id
		$sqlinsertcheckout="INSERT INTO attendence SET emp_code='".$emp_code."',
							trans_id='".$trans_id."',
							date='".$checkout_date."'";	
		if(mysqli_query($link,$sqlinsertattlocation) && mysqli_query($link,$sqlinsertcheckout))
		{
		?>	
		<table  style="border-collapse:collapse;" class="border" width="60%">
		  <tr class="TDHEAD_SUB">
			<td colspan="5" align="center">Checkout successfull.</td>
		  </tr>
		 </table> 
		 <?php 
		}
		else
		{
		?>
		 <table  style="border-collapse:collapse;" class="border" width="60%">
		  <tr class="TDHEAD_SUB">
			<td colspan="17" align="center">Checkout unsuccessful.</td>
		  </tr>
		 </table> 
		<?php	
		}
	}
	else
	{
	?>
     <table  style="border-collapse:collapse;" class="border" width="60%">
		  <tr class="TDHEAD_SUB">
			<td colspan="17" align="center">Checkout unsuccessful for In time missing of the day.</td>
		  </tr>
	  </table> 
    <?php	
	}
  }
$newdate=$_REQUEST['newdate'];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
$nick_name_val=strtoupper($_SESSION['nick_name']);
if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

$header_string = "Employee:".$new_emp_name."&nbsp;&nbsp;Date:".date('d-m-Y',strtotime($newdate));

$sql_emp_list = "SELECT dns_emp_code,emp_name,emp_code
				FROM employee_master WHERE acedns='Y' AND emp_code IN(".$employee_arg.") AND 
				emp_code NOT IN(SELECT emp_code FROM location where trans_id LIKE 'CH%'
AND (SUBSTRING(trans_id,-14,8) ='".str_replace("-","",$newdate)."')) ORDER BY emp_name ASC";
$res_emp_list = mysqli_query($link,$sql_emp_list);
$total_rows = mysqli_num_rows($res_emp_list);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="70%">
      <tr class="TDHEAD_SUB">
      	<td colspan="4"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="15%">Employee code</td>
        <td width="45%">Employee name</td>
        <td width="20%">Set In Time</td>
        <td width="">Set Out Time</td>
      </tr>
    <?php
	$res_emp_list = mysqli_query($link,$sql_emp_list);
	while($row_emp_list = mysqli_fetch_assoc($res_emp_list)){
		$dns_emp_code = $row_emp_list['dns_emp_code'];
		$emp_code= $row_emp_list['emp_code'];
		$emp_name = $row_emp_list['emp_name'];
		$sqlatttime="SELECT SUBSTRING(date,12,8) as att_time FROM location WHERE trans_id LIKE 'A%' 
							AND (SUBSTRING(trans_id,-14,8) ='".str_replace("-","",$newdate)."') AND emp_code='".$emp_code."'";
		$rsatttime=mysqli_query($link,$sqlatttime);
		$countatttime=mysqli_num_rows($rsatttime);
		$rowatttime=mysqli_fetch_assoc($rsatttime);
		if($countatttime==0)
		{
			$attendance_update_flag="<a href=\"javascript:void(0);\" title=\" Set att time\" style=\"color: #F00;\" onclick=\"javascript:show_input_text('".$emp_code."');\">SET IN TIME</a>";
		}
		else
		{
			$attendance_update_flag=$rowatttime['att_time'];
		}
		$checkout_update_flag="<a href=\"javascript:void(0);\" title=\" Set checkout time\" style=\"color: #F00;\" onclick=\"javascript:show_input_text_checkout('".$emp_code."');\">SET OUT TIME</a>";
		echo "<tr>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td id=\"showflag_$emp_code\" style=\"display:''\">".$attendance_update_flag."</td>
				<td id=\"updateflag_$emp_code\" style=\"display:none\"><input type=\"time\" step='1' min=\"00:00:00\" max=\"23:59:59\" name=\"attendance_time_$emp_code\" id=\"attendance_time_$emp_code\" value=\"Time(hh:mm:ss)\"/ size=\"12\" onclick=\"javascript:text_blank('".$emp_code."')\">&nbsp;&nbsp;<input type=\"button\" name=\"submit\" value=\"Submit\" onclick=\"javascript:update_att_time('".$emp_code."');\"/></td>
				<td id=\"showcheckoutflag_$emp_code\" style=\"display:''\">".$checkout_update_flag."</td>
				<td id=\"updatecheckoutflag_$emp_code\" style=\"display:none\"><input type=\"time\" step='1' min=\"00:00:00\" max=\"23:59:59\" name=\"checkout_time_$emp_code\" id=\"checkout_time_$emp_code\" value=\"Time(hh:mm:ss)\"/ size=\"12\" onclick=\"javascript:text_blank_checkout('".$emp_code."')\">&nbsp;&nbsp;<input type=\"button\" name=\"submit\" value=\"Submit\" onclick=\"javascript:update_checkout_time('".$emp_code."');\"/></td>
			  </tr>";
	}
	?>
    </table>
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
	
}
else{
	echo "<center>No records found</center>";
}
mysqli_close($link);
?>
		