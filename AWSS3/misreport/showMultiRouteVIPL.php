<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

	if($mode =='add' || $mode =='edit')				disphtml("show_add_edit($_REQUEST[row_id]);");
	else    										   disphtml("main();");
ob_end_flush();

function main()
{
		$sqlorder="SELECT OH.order_no, OH.customer_code FROM `order_header` OH, location LO
					WHERE LO.trans_id = OH.order_no GROUP BY OH.customer_code";
		$rsorder=mysql_query($sqlorder);
		$order_no_string='';
		$customer_code_string='';			
		while($roworder=mysql_fetch_array($rsorder))
			{
				$order_no_string=$order_no_string.$roworder['order_no'].',';
				$customer_code_string=$customer_code_string.$roworder['customer_code'].',';
			}
		$order_no_string_final=substr($order_no_string,0,-1);
		$customer_code_string_final=substr($customer_code_string,0,-1);
		$customer_code_string_array=explode(',',$customer_code_string_final);
		$customer_code_string = "'".implode("','", $customer_code_string_array)."'";
		
		$sqlpayment="SELECT PH.receipt_id, PH.customer_code FROM `payment_header` PH, location LO
					WHERE LO.trans_id = PH.receipt_id AND PH.customer_code NOT IN (".$customer_code_string.")";
		$rspayment=mysql_query($sqlpayment);	
		while($rowpayment=mysql_fetch_array($rspayment))
			{
				$order_no_string_final=$order_no_string_final.$rowpayment['receipt_id'].',';
				$customer_code_string_final=$customer_code_string_final.$rowpayment['customer_code'].',';
			}
		$order_no_string_final=substr($order_no_string,0,-1);
		$customer_code_string_final=substr($customer_code_string,0,-1);	
		$order_no_string_array=explode(',',$order_no_string_final);
		$order_no_string = "'".implode("','", $order_no_string_array)."'";
		
		
		$sqlpoint="SELECT latt,longi,trans_id FROM location WHERE trans_id IN (".$order_no_string.")";			
		$rspoint=mysql_query($sqlpoint);
		$countpoint=mysql_num_rows($rspoint);
		if($countpoint>0)
		{
			$routeDetails='[';
			while($rowpoint=mysql_fetch_array($rspoint))
			{
				$lat=$rowpoint['latt'];
				$lon=$rowpoint['longi'];
				$trans_id=$rowpoint['trans_id'];
				$sqlorderheader="SELECT customer_code FROM order_header WHERE order_no='".$trans_id."'";
				$rsorderheader=mysql_query($sqlorderheader) or die(mysql_error()." Error in select order: ".$sqlorderheader);
				$roworderheader=mysql_fetch_array($rsorderheader);
				$customer_code=$roworderheader['customer_code'];
				
				if(substr($customer_code,0,1)=='N')
				{
					$sqlcustomer="SELECT CM.customer_name FROM 
								  order_header OH,prospective_customer_master CM
								  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$trans_id."'";
				}
				else
				{
					$sqlcustomer="SELECT CM.customer_name FROM 
								  order_header OH,customer_master CM
								  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$trans_id."'";
				}
				$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysql_fetch_array($rscustomer);
				$customer_name=addslashes($rowcustomer['customer_name']);
				//$point=round($rowpoint['latt'],2).','.round($rowpoint['longi'],2);
				$date='';
				$time='';
				$emp_name='';
			
				$routeDetails.="['$emp_name', $lat, $lon,'$customer_name','$date'],";
			}
		}
	$routeDetails.=']';
?>
<html>
<head>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE
&amp;sensor=true" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="misreport/mapfilemultipleAttendance.js"></script>
<script language="JavaScript" type="text/javascript" src="misreport/prototype.js"></script>
</head>
<script language="JavaScript">
var geocoder = new GClientGeocoder();
var cnt;
var globalLat;
var globalLon;
var routes=<?=$routeDetails?>;
</script>
<body onLoad="forload(routes);" onUnload="GUnload()">
<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Customer Location</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="right" class="ERR" width="99%"></td>
					<td align="right" width="1%"></td>
				</tr>
			</table>
			<table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2"></td>
				</tr>
              
                <tr>
                	<td colspan="2">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
                          <tr> 
                            <td  align="center">
                            <div id="map" style="width: 1000px; height: 800px"></div>
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
		</td>
	</tr>
</table>
</body>
</html>
<?php }?>