<?php
ob_start();
session_start();
require("adminUtils.php");

$opt_type=$_REQUEST['opt_type'];

if($opt_type=='activateKYC')
{
	$survey_id=$_REQUEST['survey_id'];
	
	$sql_survey_val = "SELECT value FROM survey_output WHERE survey_id = '".$survey_id."' and row_id='RA004'";
	$res_survey_val = mysql_query($sql_survey_val);
	$row_survey_val = mysql_fetch_array($res_survey_val);
	$customer_name = $row_survey_val['value'];
	$dns_customer_code = $_REQUEST['customer_code'];
	$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
	$res_survey_details = mysql_query($sql_survey_details);
	while($row_survey_details = mysql_fetch_array($res_survey_details)){
		$survey_row_id = $row_survey_details['row_id'];
		$survey_value = $row_survey_details['value'];
		$emp_code=substr($survey_id,2,5);
			if($survey_row_id == 'RA004')
				$customer_name = $survey_value;
			else if($survey_row_id == 'RA005')
				$contact = $survey_value;
			else if($survey_row_id == 'RA006')
				$phone_no = $survey_value;
			else if($survey_row_id == 'RA007')
				$address = $survey_value;
			else if($survey_row_id == 'RA008')
				$pin = $survey_value;
			else if($survey_row_id == 'RA011')
				$category = $survey_value;
			else if($survey_row_id == 'RA012'){
				$cus_type = strtolower($survey_value);
				$cus_type=ucfirst($cus_type);
			}
			else if($survey_row_id == 'RA013')
				$route_name = $survey_value;
			else if($survey_row_id == 'RA060')
				$linked_dealer = $survey_value;
			else if($survey_row_id == 'RA138')
				$branch_name = $survey_value;
			else if($survey_row_id == 'RA010')
				$emial = $survey_value;
			else if($survey_row_id == 'RA017')
				$dob = $survey_value;				
	}
	/*$sql_check_cust_phone = "SELECT phone_no FROM customer_master WHERE dns_customer_code <> '".ltrim($dns_customer_code)."' 
							AND phone_no='".$phone_no."' AND phone_no <>''";
	$rs_cust_phone=mysql_query($sql_check_cust_phone);
	$row_check_cust_phone=mysql_num_rows($rs_cust_phone);
	if($row_check_cust_phone  > 0)
	{
		echo "Phone no already exists.Please provide another phone no for customer. ".($dns_customer_code);
		die;
	}*/
	$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."'";
	$rsbranchcode=mysql_query($sqlbranchcode);
	$rowbranchcode=mysql_fetch_array($rsbranchcode);
	$branch_code=$rowbranchcode['branch_code'];
	
	$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
	$rsroutechk=mysql_query($sqlroutechk);
	$rowroutechk=mysql_fetch_array($rsroutechk);
	$route_code=$rowroutechk['route_code'];
	
	$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($linked_dealer)."' AND acedns='Y' 
				AND cust_type IN('Dealer')";
	$rsrdscode=mysql_query($sqlrdscode);
	$rowrdscode=mysql_fetch_array($rsrdscode);
	$rds_code=$rowrdscode['customer_code'];

	$sqlcustomernamechk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
	$rscustomernamechk=mysql_query($sqlcustomernamechk);
	$countcustomernamechk=mysql_num_rows($rscustomernamechk);
	
	if($countcustomernamechk<1)
	{
	$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
	$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
	$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
	$max_customer_code=$rowmaxcustomercode['max_customer_code'];
	
	if($max_customer_code=='')
	{
		$max_customer_code='C/0000001';
	}
	else
	{
		$max_customer_code++;
	}
	$sql  = "insert into customer_master ";
	$sql .= " SET customer_code='".$max_customer_code."'";
	$sql .= " , dns_customer_code='".$dns_customer_code."'";
	$sql .= " , customer_name='".addslashes($customer_name)."'";
	$sql .= " , branch_code='".addslashes($branch_code)."'";
	$sql .= " , phone_no='".$phone_no."'";
	$sql .= " , route_code='".$route_code."'";
	$sql .= " , acedns='Y'";
	$sql .= " , black_list='N'";
	$sql .= " , rds_tag='".$rds_code."'";
	$sql .= " , cust_type='Non Star'";
	$sql .= " , address='".$address."'";
	$sql .= " , pin='".addslashes($pin)."'";
	$sql .= " , download_time=CURRENT_TIMESTAMP()";
	mysql_query($sql);
	$sqlsurveyheaderupdate="UPDATE survey_header SET active='active' WHERE survey_id='".$survey_id."'";
	$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$max_customer_code."',
							 route_code='".$route_code."',
							 emp_code='".$emp_code."',
							 acedns='Y',
							download_time=CURRENT_TIMESTAMP()";
	if(mysql_query($sqlinsertcustomerroute) && mysql_query($sqlsurveyheaderupdate)){?>
    <table  style="border-collapse:collapse;" class="border" width="145%">
      <tr class="TDHEAD_SUB">
      	<td colspan="17" align="center">KYC activated successfully.</td>
      </tr>
     </table> 
     <?php 
	}
	else
	{
	?>
     <table  style="border-collapse:collapse;" class="border" width="145%">
      <tr class="TDHEAD_SUB">
      	<td colspan="17" align="center">KYC activation unsuccessful.</td>
      </tr>
     </table> 
    <?php	
	}
  }
}

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
$survey_type = $_REQUEST['survey_type'];

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$nick_name_val=strtoupper($_SESSION['nick_name']);
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

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

$sql_survey_output = "SELECT DISTINCT SO.survey_id, SUBSTRING(SO.survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date,SH.active
				FROM survey_output SO,survey_header SH WHERE SH.survey_id=SO.survey_id AND SO.type = '".$survey_type."' AND SUBSTRING(SO.survey_id,3,5) IN(".$employee_arg.") 
AND (SUBSTRING(SO.survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."')  ORDER BY DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') DESC";
$res_survey_output = mysql_query($sql_survey_output);
$total_rows = mysql_num_rows($res_survey_output);
if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="145%">
      <tr class="TDHEAD_SUB">
      	<td colspan="24"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td>Firm</td>
        <td>Contact Person</td>
        <td>Mobile</td>
        <td>WhatsApp No</td>
         <td>Branch</td>
        <td>Area</td>
        <td>Address</td>
        <td>Pincode</td>
        <td>Category</td>
        <td>Sub-Category</td>
        <td>Brand Used</td>
        <td>Potential</td>
        <td>Emp Code</td>
        <td>Created By</td>
        <td>Department</td>
        <td>Linked dealer</td>
        <td>DOB</td>
        <td>Email Id</td>
        <td>Code</td>
        <td>No of Sites (Nos)</td>
        <td>No of Labour</td>
        <td>Photo link</td>
        <td>Activate</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_survey_output);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$active = $row_survey_ouput['active'];
		
			$code='';
			$no_sites='';
			$no_labour='';

		
		$sql_survey_details = "SELECT SO.row_id, SO.value FROM survey_output SO, survey_input SI WHERE SO.survey_id = '".$survey_id."' 
		AND SO.row_id = SI.row_id AND SI.row_id IN ('RA004','RA005','RA006','RA013','RA007','RA008','RA011','RA012','RA014','RA015','RA060','RA137','RA061','RA138','RA010','RA017','RA155','RA156','RA157')";
		$res_survey_details = mysql_query($sql_survey_details);
		
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$survey_row_id = $row_survey_details['row_id'];
			$survey_value = $row_survey_details['value'];
			
			if($survey_row_id == 'RA004')
				$firm_name = $survey_value;
			else if($survey_row_id == 'RA005')
				$contact = $survey_value;
			else if($survey_row_id == 'RA006')
				$mobile = $survey_value;
			else if($survey_row_id == 'RA006')
				$mobile = $survey_value;		
			else if($survey_row_id == 'RA007')
				$address = $survey_value;
			else if($survey_row_id == 'RA008')
				$pincode = $survey_value;
			else if($survey_row_id == 'RA011')
				$category = $survey_value;
			else if($survey_row_id == 'RA012')
				$sub_category = $survey_value;
			else if($survey_row_id == 'RA013')
				$area = $survey_value;
			else if($survey_row_id == 'RA014')
				$brand_most_sold = $survey_value;
			else if($survey_row_id == 'RA015')
				$potential = $survey_value;
			else if($survey_row_id == 'RA010')
				$email = $survey_value;
			else if($survey_row_id == 'RA017')
				$dob = $survey_value;	
			else if($survey_row_id == 'RA155')
				$code = $survey_value;
			else if($survey_row_id == 'RA156')
				$no_sites = $survey_value;
			else if($survey_row_id == 'RA157')
				$no_labour = $survey_value;			
			else if($survey_row_id == 'RA060'){
				$linked_dealer = $survey_value;
				$sqldealer="SELECT customer_name FROM customer_master WHERE customer_code='".$linked_dealer."'";
				$rsdealer=mysql_query($sqldealer);
				$rowdealer=mysql_fetch_array($rsdealer);
				$linked_dealer=$rowdealer['customer_name'];	
			}
			else if($survey_row_id == 'RA137'){
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
					/*${image_string.$survey_id} .= "<a href=\"http://salesmpower.acedns.in/upload/$nick_name_val/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";*/
					${image_string.$survey_id} .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
			else if($survey_row_id == 'RA061')
				$whatsapp_no = $survey_value;
			else if($survey_row_id == 'RA138'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
		}
		$sql_emp_details = "SELECT dns_emp_code, emp_name, sale_access FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$emp_name = $row_emp_details['emp_name'];
		$sale_access = $row_emp_details['sale_access'];
		//$activate_flag="<a href=\"javascript:access_add_edit('".$survey_id."');\" title=\" Activate KYC\" style=\"color: #F00;\">ACTIVATE</a>";
		if((strtoupper($sub_category)=='DEALER' || strtoupper($sub_category)=='SUB DEALER') && $active=='inactive' && strtoupper($_SESSION['admin_login'])=='ADMIN'){
			//$activate_flag="<a href=\"activate_kyc_customer.php?survey_id=$survey_id\" title=\" Activate KYC\" style=\"color: #F00;\">ACTIVATE</a>";
			$activate_flag="<a href=\"javascript:void(0);\" title=\" Activate KYC\" style=\"color: red;\" onclick=\"javascript:show_input_text('".$survey_id."');\">ACTIVATE</a>";
		}
		else if((strtoupper($sub_category)=='DEALER' || strtoupper($sub_category)=='SUB DEALER') && $active=='active'){
			$sqldnscode="SELECT dns_customer_code FROM customer_master WHERE customer_name='".$firm_name."' AND cust_type='Non Star' AND phone_no='".$mobile."'";
			$rsdnscode=mysql_query($sqldnscode);
			$rowdnscode=mysql_fetch_array($rsdnscode);
			$dns_customer_code=$rowdnscode['dns_customer_code'];
			$activate_flag=$dns_customer_code;
		}
		else
		{
			$activate_flag="";
		}
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$firm_name."</td>
				<td>".$contact."</td>
				<td>".$mobile."</td>
				<td>".$whatsapp_no."</td>
				<td>".$branch_name."</td>
				<td>".$area."</td>
				<td>".$address."</td>
				<td>".$pincode."</td>
				<td>".$category."</td>
				<td>".$sub_category."</td>
				<td>".$brand_most_sold."</td>
				<td align=\"right\">".$potential."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$sale_access."</td>
				<td>".$linked_dealer."</td>
				<td>".$dob."</td>
				<td>".$email."</td>
				<td>".$code."</td>
				<td>".$no_sites."</td>
				<td>".$no_labour."</td>
				<td>".${image_string.$survey_id}."</td>
				<td id=\"showflag_$survey_id\" style=\"display:''\">".$activate_flag."</td>
				<td id=\"updateflag_$survey_id\" style=\"display:none\"><input type=\"text\" name=\"dns_customer_code_$survey_id\" id=\"dns_customer_code_$survey_id\" value=\"Customer Code\"/ size=\"12\" onclick=\"javascript:text_blank('".$survey_id."')\">&nbsp;&nbsp;<input type=\"button\" name=\"submit\" value=\"Submit\" onclick=\"javascript:activate_KYC('".$survey_id."');\"/></td>
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
mysql_close($link);
?>
		