<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("product:index.php");
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

	if($mode == 'change_route')					update_record();
	else    										disphtml("main();");
ob_end_flush();
function select_fjp_date($combo_name, $selected_index, $style='')
{
	$curdate=date('d');
	$curmonth=date('m');
	$curyear=date('Y');
	$num = cal_days_in_month(CAL_GREGORIAN, $curmonth, $curyear); 
	$str_select_start = "<select name=".trim($combo_name)." id=".trim($combo_name)." class=\"".$style."\">";
	//$str_option = "<option value=\"\">DD</option>";
	$str_option='';
	$selected="";
	for($i = $curdate; $i <= $num; $i++)
	{
		if(strlen($i)==1)
		{
			$i='0'.$i;
		}
		$day_mon_year=$i.'-'.$curmonth.'-'.$curyear;
		if($day_mon_year == $selected_index) $selected = " selected ";
		else  $selected = " ";
		$str_option = $str_option."<option value=\"".$day_mon_year."\" ".$selected." >".stripslashes($day_mon_year)."</option>";
	}
	$str_select_end = "</select>";
	return $str_select_start.$str_option.$str_select_end;
	
}

function main()
{
		$emp_code=$_REQUEST['emp_code'];
		$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in show shop edit: ".$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);
		
		$emp_name	=$rowemp['emp_name'] ;
		$route_code_plan	=$_REQUEST['route_code'];
		$visit_date_plan	=date('Y-m-d',strtotime($_REQUEST['visit_date']));
		$visit_date_plan_selected=$_REQUEST['visit_date'];
		$trans_id	=$_REQUEST['trans_id'] ;
		
		$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code_plan."'";
		$rsroutename=mysqli_query($link,$sqlroutename) or die(mysqli_error()." Error in select route name previous : ".$sqlroutename);
		$rowroutename=mysqli_fetch_assoc($rsroutename);
		$route_name_previous=$rowroutename['route_name'];

?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.oldpassword.value.search(/\S/)==-1)
	{

		alert("Please enter Old Password");

		form.oldpassword.focus();

		return false;

	}

	if(upassID.value.search(/\S/)==-1)
	{

		alert("Please enter New Password");

		upassID.focus();

		return false;

	}

	if (upassID.value.length > maxsize) 
	{

		alert('Your password is too long');

		upassID.focus();

		return false;

	}

	if (upassID.value.length < minsize) 
	{

		alert('Your password is too short');

		upassID.focus();

		return false;

	}

	

	//**************************** End ***********************************\\

	if(form.conf_newpassword.value.search(/\S/)==-1)
	{

		alert("Please Confirm New Password");

		form.conf_newpassword.focus();

		return false;

	}

	if(form.newpassword.value!=form.conf_newpassword.value)

	{

		alert("Password mismatch");

		form.newpassword.value="";

		form.conf_newpassword.value="";

		form.newpassword.focus();

		return false;

	}

	return true;

}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Modify Route Plan</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="" >
			<input type="hidden" name="mode" value="change_route">			
            <input type="hidden" name="route_code_plan" value="<?=$route_code_plan;?>" >
            <input type="hidden" name="visit_date_plan" value="<?=$visit_date_plan;?>" >
            <input type="hidden" name="emp_code" value="<?=$emp_code;?>" >
            <input type="hidden" name="trans_id" value="<?=$trans_id;?>" >
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Changing route plan of "<?=$emp_name?>"</td>
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
					<td align="right" valign="top" class="tbllogin">Route present</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo $route_name_previous;?></td>
				</tr>
                <tr>
					<td align="right" valign="top" class="tbllogin">Route to change</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <?php
						$sqlroute="SELECT route_code,route_name FROM route_master WHERE emp_code='".$emp_code."' 
						AND route_code NOT IN(SELECT DISTINCT route_code FROM route_plan WHERE emp_code='".$emp_code."' AND visit_date='".$visit_date_plan."')";
						$rsroute=mysqli_query($link,$sqlroute) or die(mysqli_error()." Error in fetch current route to choose: ".$sqlroute);
						$countroute=mysqli_num_rows($rsroute);
						if($countroute >0){
					?>
                    <select name="route" class="inplogin" id="route" style="width:200px;">
                    <?php while($rowroute=mysqli_fetch_assoc($rsroute)){
						$route_code=$rowroute['route_code'] ;
						$route_name=$rowroute['route_name'] ;
						?>
                    <option value="<?php echo $route_code;?>"  <?php if($route_code==$route_code_plan){ echo 'selected';}?>><?php echo $route_name;?></option>
                    <?php }?>
                    </select>
                    <?php }
					else
					{
						echo "No route remains to change";
					}
					?>
                    </td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Visit date<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo select_fjp_date('visit_date', $visit_date_plan_selected,'');?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin" <?php if($countroute <1){?>disabled<?php }?>>&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminRoutePlanChange.php';" class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?
}
function update_record()
{
	$emp_code = $_REQUEST['emp_code'];
	$route_code_plan= $_REQUEST['route_code_plan'];
	$visit_date_plan= $_REQUEST['visit_date_plan'];
	$trans_id = $_REQUEST['trans_id'];
	$route_code_current= $_REQUEST['route'];
	$visit_date_current= $_REQUEST['visit_date'];
	$visit_date_current=date('Y-m-d',strtotime($visit_date_current));
	
	$sqlupdaterouteplan="UPDATE route_plan SET route_plan_trans_id ='".$trans_id."',
						  route_code 		='".$route_code_current."',
						  visit_date 		='".$visit_date_current."',
						  update_date		=CURRENT_TIMESTAMP() WHERE route_plan_trans_id ='".$trans_id."' 
						  AND visit_date ='".$visit_date_plan."' AND route_code='".$route_code_plan."'";
	if(mysqli_query($link,$sqlupdaterouteplan)){
		$sqlinsertrouteplanelog="INSERT INTO route_plan_log SET route_plan_trans_id ='".$trans_id."',
								  emp_code 			='".$emp_code."',
								  prev_route_code 	='".$route_code_plan."',
								  current_route_code ='".$route_code_current."',
								  visit_date 		='".$visit_date_current."',
								  created_by 		='".$_SESSION['admin_login']."',
								  create_date		=CURRENT_TIMESTAMP()";	
		if(mysqli_query($link,$sqlinsertrouteplanelog))
		{
			$GLOBALS['err_msg']="Route plan modified Successfully.";
		}
		else
		{
			$GLOBALS['err_msg']="Route plan modification failure.";
		}
	}
	else
	{
		$GLOBALS['err_msg']="Route plan modification failure.";
	}
	disphtml("main();");
}
?>