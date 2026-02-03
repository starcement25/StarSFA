<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$survey_type = $_REQUEST['survey_type'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$technical_meet_type=$_REQUEST['technical_meet_type'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";


if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysql_query($sql_emp_name);
	$row_emp_name = mysql_fetch_array($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}
//echo $technical_meet_type;
if($technical_meet_type=='RA062')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="7%">Employee Code</td>
        <td width="12%">Employee Name</td>
        <td width="12%">Branch</td>
        <td width="14%">Customer Name</td>
        <td width="8%">Customer Code</td>
        <td width="6">Customer Type</td>
        <td width="5%">No of Particpants</td>
        <td width="10%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Lucky Draw Gift Given</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA140'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA063'){
				$customer = $survey_value;
				$sqlcustomername="SELECT customer_name,dns_customer_code,cust_type FROM customer_master WHERE customer_code='".$customer."'";
				$rscustomername=mysql_query($sqlcustomername);
				$rowcustomername=mysql_fetch_array($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
				$dns_customer_code=$rowcustomername['dns_customer_code'];
				$cust_type=$rowcustomername['cust_type'];
			}
			else if($row_id == 'RA064'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA065'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA066')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA068')
				$lucky_draw_gift_given = $survey_value;	
			else if($row_id == 'RA067'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");
				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$customer_name."</td>
			<td>".$dns_customer_code."</td>
			<td>".$cust_type."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td >".$lucky_draw_gift_given."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
 }
 else{
		echo "<center>No Records Found</center>";
	}
}
else if($technical_meet_type=='RA069')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="7%">Employee Code</td>
        <td width="12%">Employee Name</td>
        <td width="12%">Branch</td>
        <td width="14%">Customer Name</td>
        <td width="8%">Customer Code</td>
        <td width="6">Customer Type</td>
        <td width="5%">No of Particpants</td>
        <td width="12%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Lucky Draw Gift Given</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA141'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA079'){
				$customer = $survey_value;
				$sqlcustomername="SELECT customer_name,dns_customer_code,cust_type FROM customer_master WHERE customer_code='".$customer."'";
				$rscustomername=mysql_query($sqlcustomername);
				$rowcustomername=mysql_fetch_array($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
				$dns_customer_code=$rowcustomername['dns_customer_code'];
				$cust_type=$rowcustomername['cust_type'];
			}
			else if($row_id == 'RA080'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA081'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA082')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA084')
				$lucky_draw_gift_given = $survey_value;		
			else if($row_id == 'RA083'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");
				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$customer_name."</td>
			<td>".$dns_customer_code."</td>
			<td>".$cust_type."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td >".$lucky_draw_gift_given."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
}
else if($technical_meet_type=='RA071')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="10" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="8%">Employee Code</td>
        <td width="16%">Employee Name</td>
        <td width="17%">Branch</td>
        <td width="7%">No of Particpants</td>
        <td width="18%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="8%">Lucky Draw Gift Given</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA085'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA086'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA087'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA145')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA089')
				$lucky_draw_gift_given = $survey_value;		
			else if($row_id == 'RA088'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td >".$lucky_draw_gift_given."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
}
else if($technical_meet_type=='RA073')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="9" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="7%">Employee Code</td>
        <td width="14%">Employee Name</td>
        <td width="13%">Branch</td>
         <td width="12%">Name</td>
        <td width="8%">Mobile No.</td>
        <td width="18%">Organisation</td>
        <td width="16%">Designation</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA103'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA104'){
				$name = $survey_value;
			}
			else if($row_id == 'RA105'){
				$mobile_no = $survey_value;
			}
			else if($row_id == 'RA106')
				$organisation = $survey_value;
			else if($row_id == 'RA107')
				$designation = $survey_value;	
			else if($row_id == 'RA108'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td >".$name."</td>
			<td >".$mobile_no."</td>
			<td >".$organisation."</td>
			<td >".$designation."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
}
else if($technical_meet_type=='RA075')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="11" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="8%">Employee Code</td>
        <td width="14%">Employee Name</td>
        <td width="13%">Area/Branch</td>
        <td width="5%">No of Particpants</td>
        <td width="13%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="5%">Code</td>
        <td width="5%">PC Name</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA115'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA116'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA117'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA118')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA158')
				$code = $survey_value;
			else if($row_id == 'RA159')
				$pc_name = $survey_value;		
			else if($row_id == 'RA119'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td align=\"right\">".$code."</td>
			<td align=\"right\">".$pc_name."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
}
else if($technical_meet_type=='RA072')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="9" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="8%">Employee Code</td>
        <td width="17%">Employee Name</td>
        <td width="18%">Branch</td>
        <td width="5%">No of Particpants</td>
        <td width="15%">Location of Meet</td>
        <td width="18%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA097'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA098'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA099'){
				$location_meet = $survey_value;
			}
			else if($row_id == 'RA100'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA101')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA102'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
										$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$location_meet."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
}
else if($technical_meet_type=='RA074')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="9" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="8%">Employee Code</td>
        <td width="17%">Employee Name</td>
        <td width="18%">Branch</td>
        <td width="5%">No of Particpants</td>
        <td width="15%">Location of Meet</td>
        <td width="18%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA109'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA110'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA111'){
				$location_meet = $survey_value;
			}
			else if($row_id == 'RA112'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA113')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA114'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$location_meet."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
}
if($technical_meet_type=='RA077')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="9" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="8%">Employee Code</td>
        <td width="16%">Employee Name</td>
        <td width="15%">Branch</td>
        <td width="9%">Category</td>
        <td width="18%">Customer Name</td>
        <td width="10%">Purpose of visit</td>
        <td width="18%">Remarks</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA143'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA123'){
				$category = $survey_value;
			}
			else if($row_id == 'RA124'){
				$customer = $survey_value;
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer."'";
				$rscustomername=mysql_query($sqlcustomername);
				$rowcustomername=mysql_fetch_array($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
			}
			else if($row_id == 'RA125'){
				$purpose_visit = $survey_value;
			}
			else if($row_id == 'RA126'){
				$remarks = $survey_value;
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$category."</td>
			<td >".$customer_name."</td>
			<td >".$purpose_visit."</td>
			<td >".$remarks."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
 }
 else{
		echo "<center>No Records Found</center>";
	}
}
if($technical_meet_type=='RA076')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="115%">
      <tr>
      	<td colspan="9" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="10%">Employee Code</td>
        <td width="18%">Employee Name</td>
        <td width="17%">Branch</td>
        <td width="10%">Category</td>
        <td width="6%">No of Particpants</td>
        <td width="14%">Date of Visit</td>
        <td width="18%">Plant Name</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA142'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA120'){
				$category = $survey_value;
			}
			else if($row_id == 'RA121'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA122'){
				$date_of_visit = $survey_value;
			}
			else if($row_id == 'RA135'){
				$plant_name = $survey_value;
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$category."</td>
			<td >".$no_of_participants."</td>
			<td >".$date_of_visit."</td>
			<td >".$plant_name."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
 }
 else{
		echo "<center>No Records Found</center>";
	}
}
if($technical_meet_type=='RA070')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="145%">
      <tr>
      	<td colspan="13" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="7%">Date</td>
        <td width="7%">Employee Code</td>
        <td width="10%">Employee Name</td>
        <td width="7%">Branch</td>
        <td width="9%">Category</td>
        <td width="13%">Name</td>
        <td width="9%">Mobile No</td>
        <td width="8%">Bags Consumed</td>
        <td width="8%">Cover Blocks (Qty Field)</td>
        <td width="7%">Sweets Given</td>
        <td width="7%">Gift</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA139'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA093'){
				$category = $survey_value;
			}
			else if($row_id == 'RA090'){
				$persons_met = $survey_value;
			}
			else if($row_id == 'RA091'){
				$name = $survey_value;
			}
			else if($row_id == 'RA092'){
				$mobile_no = $survey_value;
			}
			else if($row_id == 'RA094'){
				$bags_consumed = $survey_value;
			}
			else if($row_id == 'RA146'){
				$cover_blocked = $survey_value;
			}
			else if($row_id == 'RA095'){
				$sweets_given = $survey_value;
			}
			else if($row_id == 'RA096'){
				$gift = $survey_value;
			}
			else if($row_id == 'RA0926'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$category."</td>
			<td>".$name."</td>
			<td>".$mobile_no."</td>
			<td>".$bags_consumed."</td>
			<td>".$cover_blocked."</td>
			<td >".$sweets_given."</td>
			<td >".$gift."</td>
			<td >".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
 }
 else{
		echo "<center>No Records Found</center>";
	}
}
if($technical_meet_type=='RA078')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysql_query($sqldisplayname);
 $rowdisplayname=mysql_fetch_array($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysql_query($sqlrowid);
 $rowid=mysql_fetch_array($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysql_query($sql_survey_output);   
 $total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="155%">
      <tr>
      	<td colspan="13" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="7%">Employee Code</td>
        <td width="10%">Employee Name</td>
        <td width="7%">Branch</td>
        <td width="7%">Category</td>
        <td width="11%">Name of customer</td>
        <td width="8%">Area</td>
        <td width="7%">Mobile No</td>
        <td width="10%">Site Address</td>
        <td width="9%">Nature of Complaint</td>
        <td width="6%">Solution Provided</td>
        <td width="6%">Status Change</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA144'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA127'){
				$category = $survey_value;
			}
			else if($row_id == 'RA128'){
				$customer_name = $survey_value;
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_name."'";
				$rscustomername=mysql_query($sqlcustomername);
				$rowcustomername=mysql_fetch_array($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
			}
			else if($row_id == 'RA129'){
				$area = $survey_value;
			}
			else if($row_id == 'RA130'){
				$mobile_no = $survey_value;
			}
			else if($row_id == 'RA131'){
				$site_address = $survey_value;
			}
			else if($row_id == 'RA132'){
				$nature_complaint = $survey_value;
			}
			else if($row_id == 'RA133'){
				$solution_provided = $survey_value;
			}
			else if($row_id == 'RA136'){
				$status_change = $survey_value;
			}
			else if($row_id == 'RA134'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
										$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$category."</td>
			<td>".$customer_name."</td>
			<td>".$area."</td>
			<td>".$mobile_no."</td>
			<td>".$site_address."</td>
			<td >".$nature_complaint."</td>
			<td >".$solution_provided."</td>
			<td >".$status_change."</td>
			<td >".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
 }
 else{
		echo "<center>No Records Found</center>";
	}
}

mysql_close($link);
?>