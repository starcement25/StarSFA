<?php
ob_start();
session_start();
require("adminUtils.php");
	define("SERVERSAATHI","103.87.174.95");
	define("USERSAATHI","starsaat_dnsprod");
	define("PASSWORDSAATHI","dnsprod1234#");
	define("DB","starsaat_START");
	
	define("SERVERLOCAL","103.242.119.68");
	define("USERLOCAL","acedns_dnsprod");
	define("PASSWORDLOCAL","dnsprod1234#");
	define("DBLOCAL","acedns_STAR");
		
	$linksaathi=mysql_connect(SERVERSAATHI,USERSAATHI,PASSWORDSAATHI,TRUE) or die("Database Connection Error.");
	mysql_select_db(starsaat_START,$linksaathi) or die("could not connect the database");
	
	$link=mysql_connect(SERVERLOCAL,USERLOCAL,PASSWORDLOCAL,TRUE) or die("Database Connection Error.");
	mysql_select_db(DBLOCAL,$link) or die("could not connect the database");


$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

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

	$count = 1;
	/*$emp_array = array();
	$sql_customer = "SELECT DISTINCT emp_code FROM customer_visit_details WHERE emp_code IN(".$employee.") AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";
	$res_customer = mysql_query($sql_customer);
	$total_customer = mysql_num_rows($res_customer);*/
	$sql_authorize_data = "SELECT TAP.*,DATE_FORMAT(TAP.order_date,'%d-%m-%Y %H:%i:%s') AS order_date,
			DATE_FORMAT(SUBSTRING(TAP.approval_id,-14,14),'%d-%m-%Y %H:%i:%s') AS authorization_date,
			CM.customer_name as dealer_name,BM.branch_name,BM.branch_code,CM.address,EM.emp_name
					FROM T_APPERPDO_APPROVAL TAP,customer_master CM,branch_master BM,employee_master EM
					WHERE TAP.approval_done_by IN(".$employee.") AND TAP.approval_id <> '' AND (SUBSTRING(TAP.approval_id,-14,8) BETWEEN 
				'".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
				AND TAP.customer_code=CM.customer_code AND CM.branch_code=BM.branch_code AND EM.emp_code= TAP.approval_done_by
				ORDER BY SUBSTRING(approval_id,-14,14) DESC,EM.emp_name ASC";
	$res_authorize_data = mysql_query($sql_authorize_data,$link);
	$total_authorize_data = mysql_num_rows($res_authorize_data);
	if($total_authorize_data>0){
		$count = 1;
		?>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="22"><?php echo $header_string;  ?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SL_NO</td>
            <td>APPORDERNO</td>
            <td>Employee name</td>
            <td>Order Date</td>
            <td>Authorization Date</td>
			<td>Branch Name</td>
			<td>Cust Code</td>
            <td>Customer Name</td>
            <td>Consignee Name</td>
			<td>Consignee Address</td>
			<!--td>Visit Time</td-->
            <td>Freight</td>
            <td>Destination</td>
            <td>Product Name</td>
			<td>qty(MT)</td>
            <td>Phone No</td>
            <td>Dump Status</td>
            <td>Dump Name</td>
            <td>Dealer Truck</td>
            <td>Plant name</td>
            <td>STATUS</td>
            <td>Order From</td>
			<td>Remarks</td>
		  </tr>
		<?php
		$res_authorize_data = mysql_query($sql_authorize_data);
		$customer_code_array=array();
		while($row_authorize_data = mysql_fetch_array($res_authorize_data)){
			$emp_name = $row_authorize_data['emp_name'];
			$APPORDERNO= $row_authorize_data['APPORDERNO'];
			$order_date=$row_authorize_data['order_date'];
			$authorization_date=$row_authorize_data['authorization_date'];
			$order_for=$row_authorize_data['order_for'];
			$changed_order_for=$row_authorize_data['changed_order_for'];
			$sub_dealer_code=$row_authorize_data['sub_dealer_code'];
			$changed_sub_dealer_code=$row_authorize_data['changed_sub_dealer_code'];
			$dealer_name=$row_authorize_data['dealer_name'];
			$dealer_address=$row_authorize_data['address'];
			$dns_customer_code=$row_authorize_data['dns_customer_code'];
			$dns_prod_code=$row_authorize_data['dns_prod_code'];
			$changed_dns_prod_code=$row_authorize_data['changed_dns_prod_code'];
			$QTY=$row_authorize_data['QTY'];
			$QTY_CHANGED=$row_authorize_data['QTY_CHANGED'];
			$branch_name = $row_authorize_data['branch_name'];
			$branch_code = $row_authorize_data['branch_code'];
			$freight = $row_authorize_data['freight'];
			$freight_changed = $row_authorize_data['freight_changed'];
			$destination_code = $row_authorize_data['destination_code'];
			$changed_dns_destination_code = $row_authorize_data['changed_dns_destination_code'];
			$phone_no = $row_authorize_data['phone_no'];
			$dump_status = $row_authorize_data['dump_status'];
			$dump_code = $row_authorize_data['dump_code'];
			$dump_name = $row_authorize_data['dump_name'];
			$changed_dump_code = $row_authorize_data['changed_dump_code'];
			$dealer_truck = $row_authorize_data['dealer_truck'];
			$plant_name = $row_authorize_data['plant_name'];
			$approval_status = $row_authorize_data['approval_status'];
			$order_form = $row_authorize_data['order_form'];
			$order_by = $row_authorize_data['order_by'];
			$remark = $row_authorize_data['remarks'];
			
			if($changed_dns_prod_code!='') 
			{
			  $changed_dns_prod_code=$changed_dns_prod_code;	
			}
			else $changed_dns_prod_code=$dns_prod_code;
			$sqlprod = "select prod_code,prod_desc from product_master where `dns_prod_code`='".$changed_dns_prod_code."' 
						AND branch_code='".$branch_code."'";	
			$resprod = mysql_query($sqlprod,$link);
			$rowprod = mysql_fetch_array($resprod);
			$prod_code = $rowprod["prod_code"];
			$prod_desc = $rowprod["prod_desc"];
			
			if($changed_dns_destination_code!='') 
			{
			  $changed_dns_destination_code=$changed_dns_destination_code;	
			}
			else $changed_dns_destination_code=$destination_code;
			
			$sqldestcode="SELECT destination_code,destination_name FROM destination_master WHERE 
								destination_code='".$changed_dns_destination_code."'";
			$rsdestcode=mysql_query($sqldestcode,$link);
			$rowdestcode=mysql_fetch_array($rsdestcode);
			$destination_code = $rowdestcode['destination_code'];
			$destination_name = $rowdestcode['destination_name'];
			if($changed_sub_dealer_code!='')
			{
				$sqlsubdealercode="SELECT dns_customer_code,customer_name,address FROM customer_master 
										WHERE customer_code='".$changed_sub_dealer_code."'";
				$rssubdealercode=mysql_query($sqlsubdealercode,$link);
				$rowsubdealercode=mysql_fetch_array($rssubdealercode);
				$sub_dealer_code = $rowsubdealercode['customer_code'];
				$consignee_name = $rowsubdealercode['customer_name'];
				$consignee_address = $rowsubdealercode['address'];
			}
			if($changed_order_for!='') $changed_order_for=$changed_order_for;
			else $changed_order_for=$order_for;
			if($changed_order_for!='')
			{
				$consignee_name=$changed_order_for;
				$consignee_address=$changed_order_for;
			}
			if($changed_order_for=='' && $changed_sub_dealer_code=='')
			{
				$consignee_name=$dealer_name;
				$consignee_address=$dealer_address;
			}
			
			if($changed_dump_code !='') $changed_dump_code=$changed_dump_code;
			else						$changed_dump_code=$dump_code;
			if($changed_dump_code !=''){
			$sqldumpname="SELECT dump_name FROM branch_dump WHERE branch_code='".$branch_code."' AND dump_code='".$changed_dump_code."'";
			$rsdumpname=mysql_query($sqldumpname,$link);
			$rowdumpname=mysql_fetch_array($rsdumpname);
			$dump_name = $rowdumpname['dump_name'];
		    }
			else $dump_name=$dump_name;
			if($QTY_CHANGED!='') $QTY_CHANGED=$QTY_CHANGED;
			else $QTY_CHANGED=$QTY;
			
			if($order_form=='DEALER') $order_form='DEALER';
			else
			{
				$sqlbrokername="SELECT broker_name FROM broker_master WHERE dns_broker_id='".$order_by."'";
				$rsbrokername=mysql_query($sqlbrokername,$link);
				$rowbrokername=mysql_fetch_array($rsbrokername);
				$order_form = $rowbrokername['broker_name'];	
			}
			if($consignee_name=='null') $consignee_name='';
			if($consignee_address=='null') $consignee_address='';
			if($plant_name=='null') $plant_name='';
			if($remark=='null') $remark='';
			$sqlsaathistatus="SELECT status FROM T_APPERPDO WHERE APPORDERNO='".$APPORDERNO."'";
			$rssaathistatus=mysql_query($sqlsaathistatus,$linksaathi);
			$rowsaathistatus=mysql_fetch_array($rssaathistatus);
			$saathistatus=$rowsaathistatus['status'];	
			echo "<tr>
					<td>".$count."</td>
					<td>".$APPORDERNO."</td>
					<td>".$emp_name."</td>
					<td>".$order_date."</td>
					<td>".$authorization_date."</td>
					<td>".$branch_name."</td>
					<td>".$dns_customer_code."</td>
					<td>".$dealer_name."</td>
					<td>".$consignee_name."</td>
					<td>".$consignee_address."</td>
					<td>".$freight."</td>
					<td>".$destination_name."</td>
					<td>".$prod_desc."</td>
					<td align=\"right\">".number_format($QTY_CHANGED,2)."</td>
					<td>".$phone_no."</td>
					<td>".$dump_status."</td>
					<td>".$dump_name."</td>
					<td>".$dealer_truck."</td>
					<td>".$plant_name."</td>
					<td>".$saathistatus."</td>
					<td>".$order_form."</td>
					<td>".$remark."</td>
				  </tr>";
			
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
