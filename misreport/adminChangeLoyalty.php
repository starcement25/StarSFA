<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("product:index.php");
	$mode = $_REQUEST['mode'];
	
	if($mode =='change_loyalty')					update_record();
	else    										disphtml("main();");
ob_end_flush();
function main()
{
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];
	$rds_name=$_REQUEST['rds_name'];
	$vertical=$_REQUEST['vertical'];
	$loyalty_card_no=$_REQUEST['loyalty_card_no'];

	$sqlcardholder="SELECT loyalty_card_holder_name,phone_no,address FROM loyalty_card_holder_master WHERE loyalty_card_no LIKE '%".$loyalty_card_no."%'";
	$rscardholder=mysqli_query($link,$sqlcardholder) or die(mysqli_error()." Error in select card holder name and code : ".$sqlcardholder);
	$rowcardholder=mysqli_fetch_assoc($rscardholder);
	$loyalty_card_holder_name=$rowcardholder['loyalty_card_holder_name'];
	$phone_no=$rowcardholder['phone_no'];
	$address=$rowcardholder['address'];
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(document.frmedit.loyalty_card_holder_name.value.search(/\S/)==-1)
	{
		alert("Card Holder Name should not be blank");
		document.frmedit.loyalty_card_holder_name.focus();
		return false;
	}
	if(document.frmedit.address.value.search(/\S/)==-1)
	{
		alert("Address should not be blank");
		document.frmedit.address.focus();
		return false;
	}
	if(document.frmedit.phone_no.value.search(/\S/)==-1)
	{
		alert("Phone no should not be blank");
		document.frmedit.phone_no.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Edit Card Holder Details</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminChangeLoyalty.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="change_loyalty">			
            <input type="hidden" name="row_id" value="<?=$loyalty_card_no?>" >
            <input type="hidden" name="month" value="<?=$month?>" >
            <input type="hidden" name="year" value="<?=$year?>" >
            <input type="hidden" name="rds_name" value="<?=$rds_name?>" >
            <input type="hidden" name="vertical" value="<?=$vertical?>" >
            <table width="40%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="right" class="ERR" width="50%"><a href="javascript:void(0);" style="color: #e40000" 
                    onclick="javascript:window.location='adminLoyaltyReport.php?month=<?php echo $month;?>&year=<?php echo $year;?>&rds_name=<?php echo $rds_name;?>&vertical=<?php echo $vertical;?>&search_mode=Loyaltysearch'"><img src="images/back.png" alt="back" /></a></td>
					<td align="left" width="5%"><a href="adminChangeLoyalty.php?month=<?php echo $month;?>&year=<?php echo $year;?>&rds_name=<?php echo $rds_name;?>&vertical=<?php echo $vertical;?>&loyalty_card_no=<?php echo $loyalty_card_no;?>" title=" Refresh the page"><img border="0" src="images/icon_reload.gif"></a></td>
				</tr>
			</table>
            <br />
			<table width="40%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Edit Details of <?php echo $loyalty_card_holder_name;?></td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<? if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<? }?>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Card Holder Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="loyalty_card_holder_name" value="<?=$loyalty_card_holder_name?>" class="inplogin" maxlength="100"></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Address<font colodr="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"><textarea name="address" class="inplogin"><?=$address?></textarea>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Phone no<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="phone_no" value="<?=$phone_no?>" class="inplogin" maxlength="15"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Edit " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminLoyaltyReport.php?month=<?php echo $month;?>&year=<?php echo $year;?>&rds_name=<?php echo $rds_name;?>&vertical=<?php echo $vertical;?>&search_mode=Loyaltysearch';" class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
}
function update_record()
{
	$loyalty_card_no = $_REQUEST['row_id'];
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];
	$rds_name=$_REQUEST['rds_name'];
	$vertical=$_REQUEST['vertical'];
	$upd_sql="UPDATE loyalty_card_holder_master SET loyalty_card_holder_name ='".trim($_POST['loyalty_card_holder_name'])."',
				 phone_no ='".trim($_POST['phone_no'])."',
				 address='".trim($_POST['address'])."' 
				 WHERE loyalty_card_no = '" .$loyalty_card_no."'";
	if(mysqli_query($link,$upd_sql))
	{
	?>
    <script language="JavaScript" type="text/javascript">alert('Loyalty card holder details has been modified Successfully.');window.location.href='adminLoyaltyReport.php?month=<?php echo $month;?>&year=<?php echo $year;?>&rds_name=<?php echo $rds_name;?>&vertical=<?php echo $vertical;?>&search_mode=Loyaltysearch';</script>
    <?php
	}
	//$GLOBALS['err_msg']="Loyalty card holder details has been modified Successfully.";
	//disphtml("main();");
}
?>