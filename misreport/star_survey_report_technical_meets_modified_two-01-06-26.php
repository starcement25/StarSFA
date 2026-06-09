<?php
//ob_start();
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

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
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}
//echo $technical_meet_type;
if($technical_meet_type=='RA062_')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
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
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA140'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA063'){
				$customer = $survey_value;
				$sqlcustomername="SELECT customer_name,dns_customer_code,cust_type FROM customer_master WHERE customer_code='".$customer."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
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
else if($technical_meet_type=='RA062'){
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
	$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="5%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="5%">Location</td>
		 <td width="5%">Category</td>
		<td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>
		 <td width="6%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="5%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
		  <td width="5%">Lucky Draw Gift Given</td>
        <td width="4%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
				$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA140'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA472'){
				$location = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA473'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA474'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA475'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA476'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA477'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA478'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
	    echo "<td>".$branch_name."</td>
			<td>".$location."</td>
			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$name_of_gift."</td>
			<td align=\"right\">".$no_of_gift."</td>
			<td>".$lucky_draw_gift_given."</td>
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
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
	$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="5%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <!--<td width="6%">Region</td>
		  <td width="6%">zone</td>-->
        <td width="6%">Branch</td>
        <!--<td width="5%">Contractor Category</td>-->
		<td width="5%">Location</td>
		 <!--<td width="5%">Category</td>
		 <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>-->
        <td width="5%">No of Particpants</td>
        <td width="5%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
		  <td width="5%">Lucky Draw Gift Given</td>
        <td width="4%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
				$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		$contractor_category = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA141'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA445'){
				$location_meet = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA446'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA504'){
				$contractor_category = $survey_value;
			}
			
			else if($row_id == 'RA447'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA448'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA449'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA450'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA451'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
		echo "<td>".$branch_name."</td>
		
		
			<td >".$location_meet."</td>

			
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
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="17" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="6%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="6%">Location</td>
		 <td width="5%">Category</td>
			<td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="6%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
				$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA085'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA465'){
				$location = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA466'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA467'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA468'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA469'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA470'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA471'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$location."</td>
			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
else if($technical_meet_type=='RA073')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
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
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA103'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

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
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="17" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
         <td width="5%">Date</td>
        <td width="6%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="6%">Location</td>
		 <td width="5%">Category</td>
		 <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="6%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,zone,region FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$zone = $row_emp_details['zone'];
		$region = $row_emp_details['region'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>
				";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
				$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA115'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA479'){
				$location = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA480'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA481'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA482'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA483'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA484'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA485'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
			}

			else if($row_id == 'RA116'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA117'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA118')
				$no_of_gift = $survey_value;
			/*else if($row_id == 'RA158')
				$code = $survey_value;
			else if($row_id == 'RA159')
				$pc_name = $survey_value;*/		
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$location."</td>
			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
else if($technical_meet_type=='RA072')
{
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="18" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
       <td width="5%">Date</td>
        <td width="6%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="6%">Location</td>
		 <td width="5%">Category</td>
		 <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>
		 <td width="7%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="6%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
				$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA097'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA098'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA099'){
				$location_meet = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA419'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA420'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA421'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA422'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA423'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA424'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td >".$location_meet."</td>

			<td>".$category1."</td>
			
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
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
        
        <td width="18%">Zone</td>
        <td width="18%">Region</td>
        <td width="18%">Branch</td>
        <td width="18%">Location of Meet</td>
        
        <td width="18%">Influencer Segment</td>
        
        
        
        <td width="5%">Category</td>
			<td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>
        
        
        <td width="5%">No of Particpants</td>
        
        <td width="18%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA109'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
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
			
			else if($row_id == 'RA505'){
				$zone = $survey_value;
			}else if($row_id == 'RA507'){
				$region = $survey_value;
			}else if($row_id == 'RA506'){
				$influencer = $survey_value;
			}
			
			else if($row_id == 'RA508'){
				$cat1 = $survey_value;
			}else if($row_id == 'RA509'){
				$rssd1 = $survey_value;
				
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'];
				/*$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;*/
				
			}
			
			else if($row_id == 'RA512'){
				$cat3 = $survey_value;
			}else if($row_id == 'RA513'){
				$rssd3 = $survey_value;
				
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'];
				/*$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;*/
				
			}
			
			else if($row_id == 'RA510'){
				$cat2 = $survey_value;
			}else if($row_id == 'RA511'){
				$rssd2 = $survey_value;
				
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'];
				/*$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;*/
				
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
					
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
		echo "
		
		<td>".$branch_name."</td>
		<td >".$location_meet."</td>
		<td >".$influencer."</td>
		
		<td >".$cat1."</td>
		<td >".$dns_customer_code_one."</td>
		<td >".$customer_name_one."</td>
		
		<td >".$cat2."</td>
		<td >".$dns_customer_code_two."</td>
		<td >".$customer_name_two."</td>
		
		<td >".$cat3."</td>
		<td >".$dns_customer_code_three."</td>
		<td >".$customer_name_three."</td>
		
		
			<td align=\"right\">".$no_of_participants."</td>
			
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
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
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
        <td width="9%">Customer Code</td>
        <td width="18%">Customer Name</td>
        <td width="10%">Purpose of visit</td>
        <td width="18%">Remarks</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA143'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA123'){
				$category = $survey_value;
			}
			else if($row_id == 'RA124'){
				$customer = $survey_value;
				$sqlcustomername="SELECT customer_name,dns_customer_code FROM customer_master WHERE customer_code='".$customer."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
				$customer_code=$rowcustomername['dns_customer_code'];
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
			<td >".$customer_code."</td>
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
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="115%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="10%">Employee Code</td>
        <td width="12%">Employee Name</td>
		  <td width="6%">Region</td>
		<td width="6%">zone</td>
        <td width="12%">Branch</td>
		  <td width="12%">Name of Plant</td>
        <td width="8%">Category</td>
        <td width="6%">No of Particpants</td>
        <td width="8%">Name of Gift</td>
		<td width="8%">No. of Gift</td>  
        <td width="6%">Photo link</td>
        
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,zone,region FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>
				";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA142'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA135'){
				$plant_name = $survey_value;
			}
			else if($row_id == 'RA120'){
				$category = $survey_value;
			}
			else if($row_id == 'RA121'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA452'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA453')
				$no_of_gift = $survey_value;
			
			else if($row_id == 'RA454'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
			
		}
		echo "<td>".$branch_name."</td>
		<td >".$plant_name."</td>
			<td>".$category."</td>
			<td >".$no_of_participants."</td>
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
if($technical_meet_type=='RA070')
{
 $sqldisplayname="SELECT display_name FROM survey_input WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
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
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA139'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
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
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

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
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

 $sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
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
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA144'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA127'){
				$category = $survey_value;
			}
			else if($row_id == 'RA128'){
				$customer_name = $survey_value;
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_name."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

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
if($technical_meet_type=='RA355')
{
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="6%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="6%">Location</td>
		 <td width="5%">Category</td>
			<td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="6%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,zone,region FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
			$zone = $row_emp_details['zone'];
			$region = $row_emp_details['region'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
				$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA362'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA444'){
				$location = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA438'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA439'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA440'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA441'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA442'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA443'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
			}			
			else if($row_id == 'RA357'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA358'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA359')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA360'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$location."</td>
			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
if($technical_meet_type=='RA364')
{
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="20" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="4%">Employee Code</td>
        <td width="6%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="6%">Location</td>
		 <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>
		 <td width="6%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="6%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA365'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA431'){
				$location = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA432'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA433'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				//$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
				$customer_name_one=$rowcustomer['customer_name'];
			}
			else if($row_id == 'RA434'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA435'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				//$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
				$customer_name_two=$rowcustomer['customer_name'];
			}
			else if($row_id == 'RA436'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA437'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				//$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
				$customer_name_three=$rowcustomer['customer_name'];
			}
			else if($row_id == 'RA369'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA370'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA371')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA372'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$location."</td>
			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
if($technical_meet_type=='RA373')
{
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="18" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="6%">Employee Code</td>
        <td width="7%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="6%">Branch</td>
		<td width="6%">Location</td>
		 <td width="5%">Category</td>
		 <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="6%">Name of Gift</td>
        <td width="5%">No. of Gift</td>
		  <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name, region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA374'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA375'){
				$location_of_meet = $survey_value;
			}
			
			else if($row_id == 'RA425'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA426'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA427'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA428'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA429'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA430'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
			}
			
			else if($row_id == 'RA376'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA377'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA378')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA379'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td >".$location_of_meet."</td>
			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
if($technical_meet_type=='RA380')
{
 $sqldisplayname="SELECT display_name FROM survey_input_bkup WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_bkup WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="10%">Employee Code</td>
        <td width="20%">Employee Name</td>
        <td width="20%">Branch</td>
        
            <td width="5%">Contractor Category</td>
		
		 <td width="5%">Category</td>
		 <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="7%"> Dealer/ RSSD Name-3</td>
        
        
        
		<td width="8%">No of Particpants</td>
        <td width="20%">Name of Gift</td>
        <td width="8%">No. of Gift</td>
        <td width="8%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		 $contractor_category="";
		
		$category1="";
		$dns_customer_code_one="";
		$customer_name_one="";
		
		$category2="";
		$dns_customer_code_two="";
		$customer_name_two="";
		
		$category3="";
		$dns_customer_code_three="";
		$customer_name_three="";
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
		    
		    
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA381'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA382'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA383'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA384')
				$no_of_gift = $survey_value;
			else if($row_id == 'RA385'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
			else if($row_id == 'RA446'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA504'){
				$contractor_category = $survey_value;
			}
			
			else if($row_id == 'RA447'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'];
			}
			else if($row_id == 'RA448'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA449'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'];
			}
			else if($row_id == 'RA450'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA451'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'];
			}
		}
		echo "<td>".$branch_name."</td>
		
		<td >".$contractor_category."</td>
		
		<td >".$category1."</td>
		<td >".$dns_customer_code_one."</td>
		<td >".$customer_name_one."</td>
		
		<td >".$category2."</td>
		<td >".$dns_customer_code_two."</td>
		<td >".$customer_name_two."</td>
		
		<td >".$category3."</td>
		<td >".$dns_customer_code_three."</td>
		<td >".$customer_name_three."</td>
		
		
		
		
			<td align=\"right\">".$no_of_participants."</td>
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
if($technical_meet_type=='RA386')
{
 $sqldisplayname="SELECT display_name FROM survey_input_bkup WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_bkup WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="10%">Employee Code</td>
        <td width="10%">Employee Name</td>
        <td width="10%">Branch</td>
        <td width="6%">No of Particpants</td>
        <td width="15%">TYPE OF INSTITUTION</td>
        <td width="15%">ENGINEERING COLLEGE NAME</td>
		 <td width="20%">ADDRESS</td> 
        <td width="7%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA387'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA389'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA388'){
				$type_of_institution = $survey_value;
			}
			else if($row_id == 'RA390')
				$college_name = $survey_value;
			else if($row_id == 'RA391'){
				$address = $survey_value;
			}
			else if($row_id == 'RA392'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$type_of_institution."</td>
			<td >".$college_name."</td>
			<td >".$address."</td>
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
if($technical_meet_type=='RA393')
{
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="14" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="8%">Employee Code</td>
        <td width="10%">Employee Name</td>
		<td width="6%">Region</td>
		<td width="6%">zone</td>
        <td width="8%">Branch</td>
		<td width="6%">Location</td>
		 <td width="6%">Type Of Institution</td> 
		  <td width="7%">Name of the Institution</td>
		  <td width="10%">Address</td>
		<td width="5%">No of Particpants</td>
		<td width="8%">Name of Gift</td>
		<td width="8%">No. of Gift</td>  
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>
				";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA395'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA463'){
				$location = $survey_value;
			}
			else if($row_id == 'RA396'){
				$type_of_institution = $survey_value;
			}
			else if($row_id == 'RA397'){
				$name_institution = $survey_value;
			}
			else if($row_id == 'RA398'){
				$address = $survey_value;
			}
			else if($row_id == 'RA399'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA400'){
				$name_of_gift = $survey_value;
			}
			else if($row_id == 'RA401')
				$no_of_gift = $survey_value;
			
			else if($row_id == 'RA402'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td>".$location."</td>
			<td>".$type_of_institution."</td>
			<td>".$name_institution."</td>
			<td>".$address."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
if($technical_meet_type=='RA394')
{
 $sqldisplayname="SELECT display_name FROM survey_input_tm WHERE row_id='".$technical_meet_type."'";
 $rsdisplayname=mysqli_query($link,$sqldisplayname);
 $rowdisplayname=mysqli_fetch_assoc($rsdisplayname);
 $display_name=$rowdisplayname['display_name'];
 
 $sqlrowid="SELECT row_id FROM survey_input_tm WHERE menu_id='".$technical_meet_type."' AND acedns='Y' ORDER BY row_id LIMIT 0,1";
 $rsrowid=mysqli_query($link,$sqlrowid);
 $rowid=mysqli_fetch_assoc($rsrowid);
 $row_id_single=$rowid['row_id'];
 
 $header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Technical Meet Type:".$display_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));
$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                   FROM survey_output WHERE row_id='".$row_id_single."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND (SUBSTRING(survey_id,-14,8) 
				   BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
 $res_survey_output = mysqli_query($link,$sql_survey_output);   
 $total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="125%">
      <tr>
      	<td colspan="12" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="6%">Employee Code</td>
        <td width="10%">Employee Name</td>
		  <td width="6%">Region</td>
		  <td width="6%">zone</td>
        <td width="7%">Branch</td>
		<td width="6%">Location</td>
		 <td width="5%">Category</td>
		 <td width="3%">Dealer/ RSSD Code-1</td>
		 <td width="6%"> Dealer/ RSSD Name-1</td>
		 <td width="4%">Category</td>
		   <td width="3%">Dealer/ RSSD Code-2</td>
		 <td width="6%"> Dealer/ RSSD Name-2</td>
		  <td width="4%">Category</td>
		  <td width="3%">Dealer/ RSSD Code-3</td>

		 <td width="10%"> Dealer/ RSSD Name-3</td>
        <td width="5%">No of Particpants</td>
        <td width="4%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$region."</td>
				<td>".$zone."</td>
				";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$dns_customer_code_one = "";
		$customer_name_one = "";
		$dns_customer_code_three = "";
		$customer_name_three = "";
		$customer_name_two = "";
		$dns_customer_code_two = "";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			//$dns_customer_code_one = "";
			if($row_id == 'RA403'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA455'){
				$location_meet = $survey_value;
				/*$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];*/
			}
			else if($row_id == 'RA456'){
				$category1 = $survey_value;
			}
			else if($row_id == 'RA457'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$customer_name_one=$rowcustomer['customer_name'].'-'.$dns_customer_code_one;
			}
			else if($row_id == 'RA458'){
				$category2 = $survey_value;
			}
			else if($row_id == 'RA459'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_two=$rowcustomer['dns_customer_code'];
				$customer_name_two=$rowcustomer['customer_name'].'-'.$dns_customer_code_two;
			}
			else if($row_id == 'RA460'){
				$category3 = $survey_value;
			}
			else if($row_id == 'RA461'){
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_three=$rowcustomer['dns_customer_code'];
				$customer_name_three=$rowcustomer['customer_name'].'-'.$dns_customer_code_three;
			}

			else if($row_id == 'RA407'){
				$no_of_participants = $survey_value;
			}
			else if($row_id == 'RA408'){
				$address = $survey_value;
			}
			else if($row_id == 'RA410'){
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
										$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
			}
		}
		echo "<td>".$branch_name."</td>
			<td >".$location_meet."</td>

			<td>".$category1."</td>
			<td>".$dns_customer_code_one."</td>
			<td>".$customer_name_one."</td>
			<td>".$category2."</td>
			<td>".$dns_customer_code_two."</td>
			<td>".$customer_name_two."</td>
			<td>".$category3."</td>
			<td>".$dns_customer_code_three."</td>
			<td>".$customer_name_three."</td>
			<td align=\"right\">".$no_of_participants."</td>
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
mysqli_close($link);
?>