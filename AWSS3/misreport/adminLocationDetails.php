<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$GLOBALS['show']=30;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
		$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	$mode = $_REQUEST['mode'];
	

	if($mode =='add' || $mode =='edit')				 disphtml("show_add_edit($_REQUEST[row_id]);");
	else    											disphtml("main();");
ob_end_flush();

function main()
{
	$sqltrans="SELECT *,DATE_FORMAT(date,'%b %e ,%y') AS date,DATE_FORMAT(date,'%T') AS time FROM location WHERE trans_id='".$_REQUEST['trans_id']."'";
	$rstrans=mysql_query($sqltrans) or die(mysql_error()." Error in select transaction : ".$sqltrans);	
	$rowtrans=mysql_fetch_array($rstrans);
	
	$trans_id=$rowtrans['trans_id'];
	$emp_code=$rowtrans['emp_code'];
	$time=$rowtrans['time'];
	$operation_type=substr($trans_id,0,1);
	$operation_type_no=substr($trans_id,1,1);
	
	$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsemp=mysql_query($sqlemp) or die(mysql_error()." Error in select employee : ".$sqlemp);
	$rowemp=mysql_fetch_array($rsemp);
	$emp_name=$rowemp['emp_name'];
	
	$sqlcustomer="SELECT CM.customer_name,RM.route_name FROM customer_master CM,route_master RM WHERE 
					CM.route_code=RM.route_code AND CM.customer_code='".$_REQUEST['customer_code']."'";
	$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer : ".$sqlcustomer);	
	$rowcustomer=mysql_fetch_array($rscustomer);
	$customer=$rowcustomer['customer_name'];
	$route=$rowcustomer['route_name'];
	
	if($_REQUEST['store_code']!=''){
		
		$sqlstore="SELECT store_name FROM store_code_master WHERE store_code='".$_REQUEST['store_code']."'";
		$rsstore=mysql_query($sqlstore) or die(mysql_error()." Error in select store : ".$sqlstore);	
		$rowstore=mysql_fetch_array($rsstore);
		$store=$rowstore['store_name'];
		
	}
	//echo $_SERVER['PHP_SELF'];
	
	if($_REQUEST['store_code']!='')
	{
		$url="adminLocationDetails.php?trans_id=$_REQUEST[trans_id]&store_code=$_REQUEST[store_code]";
	}
	else
	{
		$url="adminLocationDetails.php?trans_id=$_REQUEST[trans_id]&customer_code=$_REQUEST[customer_code]";
	}
	if($operation_type=='O')
	{
		$order_no=substr($trans_id,1,19);
		$sqlorderdetails="SELECT SM.sku_name,SM.brand_code,SM.brand_form_code,OD.qty FROM order_details OD,sku_master SM WHERE SM.sku_code=OD.sku_code
				AND OD.order_no='".$order_no."'";
		$rsorderdetails=mysql_query($sqlorderdetails) or die(mysql_error()." Error in select order details: ".$sqlorderdetails);
	}
	if($operation_type=='P')
	{
		$receipt_id=substr($trans_id,1,19);
		$sqlpaymentdetails="SELECT PD.amount,OU.invoice_amount,OU.due_amount,PD.invoice_id FROM payment_details PD,outstanding OU WHERE PD.invoice_id=OU.invoice_id
				AND PD.receipt_id='".$receipt_id."'";
		$rspaymentdetails=mysql_query($sqlpaymentdetails) or die(mysql_error()." Error in select payment details: ".$sqlpaymentdetails);

	}
	if($operation_type=='C')
	{
		$stockist_receipt_id=substr($trans_id,1,19);
		$sqlstockdetails="SELECT SM.sku_name,CSCS.qty FROM customer_stock_counting_stockist CSCS,sku_master SM WHERE 
							SM.sku_code=CSCS.sku_code AND CSCS.stockist_receipt_id='".$stockist_receipt_id."'";
		$rsstockdetails=mysql_query($sqlstockdetails) or die(mysql_error()." Error in select stock details: ".$sqlstockdetails);

	}
	if($operation_type=='M')
	{
		$mt_receipt_id=substr($trans_id,1,19);
		$sqlmtdetails="SELECT SM.sku_name,SCMT.qty FROM stock_cousting_mt SCMT,sku_master SM WHERE 
						SM.sku_code=SCMT.sku_code AND SCMT.mt_receipt_id='".$mt_receipt_id."'";
		$rsmtdetails=mysql_query($sqlmtdetails) or die(mysql_error()." Error in select mt details: ".$sqlmtdetails);

	}
	
?>


<script language="JavaScript">
var geocoder = new GClientGeocoder();
var cnt;
var globalLat;
var globalLon;
var emp_name="<?=stripslashes($emp_name);?>";
<?php if($_REQUEST['store_code']!=''){?>
var customer="<?=stripslashes($store);?>";
<?php }else{?>
var customer="<?=stripslashes($customer);?>";
<?php }?>
var route="<?=stripslashes($route);?>";
var time="<?=$time?>";
var Lat="<?=$rowtrans['latt']?>";
var Lon="<?=$rowtrans['longi']?>";
var Place='';
</script>
<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Employee Visit Details</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="right" class="ERR" width="95%"><a href="javascript:void(0);" style="color: #e40000" onclick="javascript:window.location='adminCustomerListing.php?from_date=<?php echo $_REQUEST['from_date']?>&to_date=<?php echo $_REQUEST['to_date']?>&emp_code=<?php echo $_REQUEST['emp_code']?>&search_mode=SEARCH_DATE'"> <img src="images/back.png" alt="back" /> </a></td>
					<td align="right" width="5%"><a href="<?=$url;?>" title=" Refresh the page"><img border="0" src="images/icon_reload.gif"></a></td>
				</tr>
			</table>
            <?php if($operation_type=='O'){?>
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="display: run-in;height: 200px;overflow-y: scroll">
				<tr class="TDHEAD" > 
					<td colspan="5">Order Details</td>
				</tr>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
					<td width="20%" align="left" style="padding-left:20px;">Brand</td>
                    <td width="25%" align="left" style="padding-left:20px;">Brand Form</td>
                    <td width="40%" align="left" style="padding-left:20px;">Sku</td>
                    <td width="" align="left" style="padding-left:20px;">Quantity</td>
				</tr> 
                <?php $cnt=$GLOBALS[start]+1;
					while($roworderdetails=mysql_fetch_array($rsorderdetails))
					{
				?>
                		<tr> 
                            <td valign="top" align="center"><?=$cnt++ ?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=$roworderdetails['brand_code'];?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=$roworderdetails['brand_form_code'];?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=$roworderdetails['sku_name'];?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=number_format($roworderdetails['qty'],2);?></td>
                         </tr>
                
                <?php }?>
            </table>    
            <?php }
			 if($operation_type=='P'){?>
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="display: run-in;height: 200px;overflow-y: scroll">
				<tr class="TDHEAD" > 
					<td colspan="5">Payment Details</td>
				</tr>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
					<td width="22%" align="left" style="padding-left:20px;">Invoice</td>
                    <td width="22%" align="left" style="padding-left:20px;">Invoice Amount</td>
                    <td width="22%" align="left" style="padding-left:20px;">Due Amount</td>
                    <td width="" align="left" style="padding-left:20px;">Collected Amount</td>
				</tr> 
                <?php $cnt=$GLOBALS[start]+1;
					while($rowpaymentdetails=mysql_fetch_array($rspaymentdetails))
					{
				?>
                		<tr> 
                            <td valign="top" align="center"><?=$cnt++ ?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=$rowpaymentdetails['invoice_id'];?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=number_format($rowpaymentdetails['invoice_amount'],2);?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=number_format($rowpaymentdetails['due_amount'],2);?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=number_format($rowpaymentdetails['amount'],2);?></td>
                         </tr>
                
                <?php }?>
            </table>    
            <?php }
            if($operation_type=='C'){?>
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="display: run-in;height: 200px;overflow-y: scroll">
				<tr class="TDHEAD" > 
					<td colspan="5"><?php echo stripslashes($rowcustomer['customer_name']);?> Stock Upgradation Details</td>
				</tr>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
                    <td width="22%" align="left" style="padding-left:20px;">Sku</td>
                    <td width="22%" align="left" style="padding-left:20px;">Quantity</td>
				</tr> 
                <?php $cnt=$GLOBALS[start]+1;
					while($rowstockdetails=mysql_fetch_array($rsstockdetails))
					{
				?>
                		<tr> 
                            <td valign="top" align="center"><?=$cnt++ ?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=$rowstockdetails['sku_name'];?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=number_format($rowstockdetails['qty'],2);?></td>
                         </tr>
                
                <?php }?>
            </table>    
			<?php }
            if($operation_type=='M'){?>
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="display: run-in;height: 200px;overflow-y: scroll">
				<tr class="TDHEAD" > 
					<td colspan="5"><?php echo stripslashes($rowcustomer['store_name']);?> Counter Stock Upgradation Details</td>
				</tr>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
                    <td width="22%" align="left" style="padding-left:20px;">Sku</td>
                    <td width="22%" align="left" style="padding-left:20px;">Quantity</td>
				</tr> 
                <?php $cnt=$GLOBALS[start]+1;
					while($rowmtdetails=mysql_fetch_array($rsmtdetails))
					{
				?>
                		<tr> 
                            <td valign="top" align="center"><?=$cnt++ ?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=$rowmtdetails['sku_name'];?></td>
                            <td align="left" valign="top" style="padding-left:20px;"><?=number_format($rowmtdetails['qty'],2);?></td>
                         </tr>
                
                <?php }?>
            </table>    
			<?php }?>

            <br /> <br /> <br />
            
            
			<table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2">Employee Visit Details</td>
				</tr>
				<tr> 
					<td  colspan="2" align="center"><strong>
                    <?php if($_REQUEST['customer_code']!=''){?>
                    Customer Name:  <font color="#FF0000"><?php echo stripslashes($rowcustomer['customer_name']);?></font>
                    <?php }
					if($_REQUEST['store_code']!=''){
					?>
                    Store Name:  <font color="#FF0000"><?php echo stripslashes($rowstore['store_name']);?></font>
					<?php }?>
                    
                    </strong></td>
				</tr>
                <tr> 
					<td  width="25%" align="left"><strong>Visit Date:  <font color="#FF0000"><?php echo $rowtrans['date'];?></font></strong></td>
                    <td  width="75%" align="right"><strong><?php if($_REQUEST['operation']==''){?>Visit Purpose: <?php }?>
                    <font color="#FF0000">
					<?php 
						if($operation_type=='O'){ echo 'Order';}
						if($operation_type=='P'){ echo 'Payment';}
						if($operation_type=='C'){ echo 'Stockist Stock Upgradation';}
						if($operation_type=='M'){ echo 'MT Counter Stock Upgradation';}
						if($_REQUEST['operation']=='noorder'){echo 'No Order';}
						if($_REQUEST['operation']=='nocollection'){echo 'No Collection';}
					?>
                    </font></strong></td>
                    
				</tr>
                <tr> 
					<td  colspan="2" align="center"><strong>Location:  </strong></td>
				</tr>
                <tr>
                	<td colspan="2">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
                          <tr> 
                            <td  align="center">
                            <div id="map" style="width: 600px; height: 400px"></div>
                            </td>
                          </tr>
                          <tr> 
                            <td >&nbsp;</td>
                          </tr>
                        </table>
                    </td>
                 </tr>   
                
			</table>
			<br><br>
			</form>
		</td>
	</tr>
</table>
<?
}//End of main()

?>