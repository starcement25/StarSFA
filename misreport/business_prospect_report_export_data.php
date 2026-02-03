<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	$emp_condition = '';
}
else{
	$emp_condition = " AND SUBSTRING(PCH.trans_id,3,5) IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(PCH.trans_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(PCH.trans_id,-14,8) <='".str_replace("-","",$end_date)."'";
}
else
{
	$date_condition='';
}

	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '15' align = 'center' class = 'TDHEAD_SUB'>Business Prospect report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Create Date</td>
        <td>Emp Name</td>
        <td>Prospect Name</td>
        <?php
		if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
		{
		?>
            <td>In Time</td>
            <td>Out Time</td>
        <?php }?>
        <td>Address</td>
        <td>Pin</td>
        <td>Area</td>
	  	<td>Phone No</td>
        <td>Cust type</td>
        <td>Remarks</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='ILS'){?>
        <td>Category of Stores</td>
        <?php }?>
        <td>Discussed Product</td>
		<?php
		if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
		{
		?>
            <td>Lattitude</td>
            <td>Longitude</td>
            <td >Locate</td>
        <?php }?>
      </tr>
    <?
	if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
	{
	  $sql_prospect = "SELECT EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%d-%m-%Y %H:%i:%s') AS create_date,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%H:%i:%s') AS out_time,SUBSTRING(PCH.check_in_time,-8,8) AS in_time,PCH.trans_id,PCH.customer_name, PCH.phone_no, PCH.customer_code,PCH.cust_type,PCH.remarks, PCH.area,PCH.pin,PCH.address,PCH.category_of_store,LO.latt,LO.longi FROM employee_master EM, prospective_customer_header PCH,location LO WHERE  EM.emp_code=substring(PCH.trans_id,3,5) AND LO.trans_id=PCH.trans_id 
				 ".$emp_condition.$date_condition."  ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	}
	else
	{
	  $sql_prospect = "SELECT EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%d-%m-%Y %H:%i:%s') AS create_date,PCH.trans_id,PCH.customer_name, PCH.phone_no, PCH.customer_code,PCH.cust_type,PCH.remarks, PCH.area,PCH.pin,PCH.address,PCH.category_of_store FROM employee_master EM, prospective_customer_header PCH WHERE  EM.emp_code=substring(PCH.trans_id,3,5) 
				 ".$emp_condition.$date_condition." ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	}
	$res_prospect = mysqli_query($link,$sql_prospect);
	$cnt_prospect=mysqli_num_rows($res_prospect);
	if($cnt_prospect >0){
	while($row_prospect = mysqli_fetch_assoc($res_prospect))
	{	
		$route_code = $row_prospect['area'];
		$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
		$res_route_name = mysqli_query($link,$sql_route_name);
		$row_route_name = mysqli_fetch_assoc($res_route_name);
		$route_name = $row_route_name['route_name'];
		if($route_name=='') $route_name=$row_prospect['area'];
		$pin = $row_prospect['pin'];
		$emp_name = $row_prospect['emp_name'];
		$emp_code = $row_prospect['emp_code'];
		$create_date = $row_prospect['create_date'];
		$customer_code = $row_prospect['customer_code'];
		$customer_name = $row_prospect['customer_name'];
		$phone_no = $row_prospect['phone_no'];
		$address = $row_prospect['address'];
		$cust_type = $row_prospect['cust_type'];
		$remarks = $row_prospect['remarks'];
		$trans_id=$row_prospect['trans_id'];
		$category_of_store=$row_prospect['category_of_store'];
		$latt=$row_prospect['latt'];
		$longi=$row_prospect['longi'];
	  $SQLproddiscussed="SELECT GROUP_CONCAT(PM.prod_desc SEPARATOR ',') as prod_discussed FROM prospective_customer_details PCD,product_master PM WHERE 
						PM.prod_code=PCD.product_code AND PCD.trans_id='".$trans_id."' ORDER BY PM.prod_desc ASC";
	  $rsproddiscussed=mysqli_query($link,$SQLproddiscussed);
	  $rowproddiscussed=mysqli_fetch_assoc($rsproddiscussed);
	  $prod_discussed=$rowproddiscussed['prod_discussed'];
		echo "<tr>
			<td>".$create_date."</td>
			<td>".$emp_name."</td>
			<td>".$customer_name."</td>";
			if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
			{
				$out_time=$row_prospect['out_time'];
				$in_time=$row_prospect['in_time'];
				if($in_time=='') $out_time='';
				echo "<td>".$in_time."</td>
				  	<td>".$out_time."</td>";
			}
			echo "<td>".$address."</td>
			<td>".$pin."</td>
			<td>".$route_name."</td>
			<td>".$phone_no."</td>
			<td>".$cust_type."</td>
			<td>".$remarks."</td>";
			if(strtoupper($_SESSION['nick_name'])=='ILS'){
				echo "<td>".$category_of_store."</td>";
			}
			echo "<td>".$prod_discussed."</td>";
		if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
		{
            echo "<td>".$latt."</td>";
			echo "<td>".$longi."</td>";
			echo "<td><a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$create_date&page=prospectdetails\" style=\"color:red;font-weight:bold;\" target=\"_blank\">Locate</a>
							</td>";
        }
		  echo "</tr>";
	}
	}
	else{
	echo "<tr><td colspan='10' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
mysqli_close($link);
?>