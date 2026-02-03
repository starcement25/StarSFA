<?php
/*define("SERVER","localhost");
define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	//require("include/config-setup.php");
	
	$linksetupDCR=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_CSPL",$linksetupDCR) or die("could not connect the setup database");
	
	echo $_REQUEST['approval'];
	
	echo "ORDER APPROVED.";*/
	
	
ob_start();
	session_start();
	require("userUtils.php");
	$mode = $_REQUEST['mode'];
	if($mode == 'save')						  add_record();
	else    									 disphtml("main();");
ob_end_flush();

function main()
{
	$order_no=$_REQUEST['order_no'];
	$approval=$_REQUEST['approval'];
	$nick_name=$_REQUEST['nick_name'];
	if($approval=='approved')
	{
		$sqlupdate="UPDATE order_header SET is_approved='YES' WHERE order_no='".$order_no."'";
		if(mysqli_query($link,$sqlupdate))
		{
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
			
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
				$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;  
				//$email   ='export@tttextiles.com'; 
				//$subject ='TT mail checking again on'.$location_date;
			$email   =ORDEREMAILRECIPENTS; 
			$subject =$nick_name.' - '. 'Order Approved On '.date('d-m-Y',strtotime($location_date)).' @'.date('H:i:s',strtotime($location_date)).' hrs.' ;
			
			$message="<html><head><title>Order approval</title></head>
					<body>
					<table>
						<b>Refference No: " .$order_no. "</b>
					</table>
					<br /><br><br>Powered By aceDNS<br></body></html>";   

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
		$flgSend=mail($email, $subject, $message, $headers,'-facedns@coral.in');
	}
?>
	<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Order Approval</td>
				</tr>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000">Order has been approved</font></strong></td>
				</tr>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Refference No</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo $order_no;?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php		
	}
	else
	{
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.not_approved_reason.value.search(/\S/)==-1)
	{
		alert("Please provide a reason.");
		form.not_approved_reason.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="update-order-approval.php?nick_name=<?php echo $nick_name;?>" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="save">
            <input type="hidden" name="order_no" value="<?php echo $order_no;?>">		
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Provide not approval reason</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }?>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Refference No</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo $order_no;?></td>
				</tr>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Reason<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="not_approved_reason" class="not_approved_reason" style="width:300px;height:30px;" value="<?php echo $_REQUEST['not_approved_reason'];?>"/></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Save " class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
	}
}//End of main()
function add_record()
{
	$order_no=$_REQUEST['order_no'];
	$not_approved_reason=$_REQUEST['not_approved_reason'];
	$nick_name=$_REQUEST['nick_name'];
	
	$sqlupdate="UPDATE order_header SET is_approved='NO',not_approved_reason='".$not_approved_reason."' WHERE order_no='".$order_no."'";
	if(mysqli_query($link,$sqlupdate))
	{
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
		
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;  
			//$email   ='export@tttextiles.com'; 
			//$subject ='TT mail checking again on'.$location_date;
		$email   =ORDEREMAILRECIPENTS; 
		$subject =$nick_name.' - '. 'Order Not Approved On '.date('d-m-Y',strtotime($location_date)).' @'.date('H:i:s',strtotime($location_date)).' hrs.' ;
		
		$message="<html><head><title>Order not approval</title></head>
				<body>
				<table>
					<b>Refference No: " .$order_no. "</b><br />
					<b>Reason of not approval: " .$not_approved_reason. "</b>
				</table>
				<br /><br><br>Powered By aceDNS<br></body></html>";   

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		$flgSend=mail($email, $subject, $message, $headers,'-facedns@coral.in');
	}
	?>
     <script language="JavaScript" type="text/javascript">alert('Order not approval reason has saved successfully.');window.close();</script>
    <?php
	//$GLOBALS['err_msg']="Order not approval reason has saved successfully.";
	//disphtml("main();");
}
?>
