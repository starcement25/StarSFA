<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	disphtml("main();");
ob_end_flush();

function main()
{
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}
?>
<script language="javascript">
function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}

	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
function check()
{
	if (document.getElementById('emp_name').value.search(/\S/)==-1) 
	{
		alert('Please choose one employee.');
		document.getElementById('emp_name').focus();
		return false;
	}
	if (document.getElementById('emp_swap').value.search(/\S/)==-1) 
	{
		alert('Please choose one employee for swapping.');
		document.getElementById('emp_swap').focus();
		return false;
	}
	document.getElementById('emp_swap_val').value=document.getElementById('emp_swap').value;
	return true;
}
function swapping_emp()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	if (document.getElementById('emp_name').value.search(/\S/)==-1) 
	{
		alert('Please choose one employee.');
		document.getElementById('emp_name').focus();
		return false;
	}
	var emp_code=document.getElementById('emp_name').value;
	var url="select_swapping_emp.php?emp_code="+emp_code;
	xmlHttp.onreadystatechange=showswappingemp;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function showswappingemp()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("show_swap_emp").innerHTML = val;
		 }
	}
 }
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Employee Swapping</strong></td>
	</tr>
    <tr>
		<td valign="top" >
			<table width="55%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
				<tr class="TDHEAD" > 
					<td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
				</tr>
				<tr > 
					<td width="15%" colspan="7" align="center">
                        <table width="70%" align="center" border="0" cellpadding="5" cellspacing="1"  >
                        <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
                        <input type="hidden" name="mode" value="swapping_emp">
                        <input type="hidden" name="emp_swap_val" id="emp_swap_val" value="">
                        
                        	<tr>
                        		<td align="right" width="40%">Choose Employee:</td>
                        		<td align="left" width="" style="vertical-align:top;" >
                                    <select name="emp_name" id="emp_name" onchange="javascript:swapping_emp();">
                                    <option value="">SELECT</option>
                                    <?php 
                                    $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ".$emp_hierarchy_condition." 
													ORDER BY emp_name ASC";
                                    $resultqueryemp = mysql_query($sqlqueryemp);
                                    $countemp=mysql_num_rows($resultqueryemp);
                                    if($countemp>0){
                                    while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                    {
                                    ?>
                                    <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $_REQUEST['emp_name']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                    <?php
                                    }
                                    }
                                    ?>	
                                    </select>
                                </td>
                        	</tr>
                        	<tr>
                                <td align="right" width="40%">Employee for Swapping:</td>
                                <td align="left" width="" style="vertical-align:top;" id="show_swap_emp">
                                <?php if($_REQUEST['mode']=='swapping_emp'){
                                	echo "<select name=\"emp_swap\" id=\"emp_swap\">";
									echo "<option value=\"\">Select</option>";
									$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y' AND 
												emp_code!='".$_REQUEST['emp_name']."' ".$emp_hierarchy_condition." ORDER BY emp_name ASC";
									$res_emp = mysql_query($sql_emp);
									while($row_emp = mysql_fetch_array($res_emp)){
										$emp_code_swap = $row_emp['emp_code'];
										$emp_name_swap = $row_emp['emp_name'];
										if( $_REQUEST['emp_swap_val']=="'".$row_emp['emp_code']."'"){ $selected='selected';}
										else											  $selected='';
										echo "<option value=\"'".$emp_code_swap."'\" ".$selected.">".$emp_name_swap."</option>";
									}
									echo "</select>";
                               }?>
                                </td>
                             </tr>
                        	<tr>
                            	<td align="right" width="25%">&nbsp;</td>
                                <td align="left" width="" >
                                <input type="submit" value="Submit" class="inplogin">
                                </td>
                        	</tr>
                        	</form>
                        </table> 
					</td>
				</tr>
			</table> 
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><?php echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
if($_REQUEST['mode']=='swapping_emp')
{
	$emp_code=$_POST['emp_name'];
	$emp_code_swap=$_POST['emp_swap_val'];
	
	$customer_code_one_array=array();
	$route_code_one_array=array();
	$acedns_one_array=array();
	$download_time_one_array=array();
	$customer_code_two_array=array();
	$route_code_two_array=array();
	$acedns_two_array=array();
	$download_time_two_array=array();

	$sqlfetchcutomerrouteone="SELECT customer_code,route_code,acedns,download_time  FROM customer_route_emp_relation WHERE emp_code='".$emp_code."'";
	$rsfetchcutomerrouteone=mysql_query($sqlfetchcutomerrouteone);
	while($rowfetchcutomerrouteone=mysql_fetch_array($rsfetchcutomerrouteone))
	{
		$customer_code_one=$rowfetchcutomerrouteone['customer_code'];
		$route_code_one=$rowfetchcutomerrouteone['route_code'];
		$acedns_one=$rowfetchcutomerrouteone['acedns'];
		$download_time_one=$rowfetchcutomerrouteone['download_time'];
		array_push($customer_code_one_array,$customer_code_one);
		array_push($route_code_one_array,$route_code_one);
		array_push($acedns_one_array,$acedns_one);
		array_push($download_time_one_array,$download_time_one);
	}
	$sqlfetchcutomerroutetwo="SELECT customer_code,route_code,acedns,download_time  FROM customer_route_emp_relation WHERE emp_code=".$emp_code_swap."";
	$rsfetchcutomerroutetwo=mysql_query($sqlfetchcutomerroutetwo);
	while($rowfetchcutomerroutetwo=mysql_fetch_array($rsfetchcutomerroutetwo))
	{
		$customer_code_two=$rowfetchcutomerroutetwo['customer_code'];
		$route_code_two=$rowfetchcutomerroutetwo['route_code'];
		$acedns_two=$rowfetchcutomerroutetwo['acedns'];
		$download_time_two=$rowfetchcutomerroutetwo['download_time'];
		array_push($customer_code_two_array,$customer_code_two);
		array_push($route_code_two_array,$route_code_two);
		array_push($acedns_two_array,$acedns_two);
		array_push($download_time_two_array,$download_time_two);
	}
	//print_r($customer_code_one_array);
	//print_r($customer_code_two_array);
	//echo count($customer_code_one_array);
	//echo count($customer_code_two_array);
	//print_r($route_code_one_array);
	//exit();
	for($i=0;$i<count($customer_code_one_array);$i++)
	{
		$sqlupdatecustomerrouteone="UPDATE customer_route_emp_relation SET emp_code=".$emp_code_swap.",download_time=CURRENT_TIMESTAMP() 
									WHERE customer_code='".$customer_code_one_array[$i]."' AND route_code='".$route_code_one_array[$i]."' 
									AND emp_code='".$emp_code."'";
								
		mysql_query($sqlupdatecustomerrouteone);
		
		$sqlinsertswappinglogone="INSERT INTO employee_swapping_log SET 	customer_code='".$customer_code_one_array[$i]."',
								route_code='".$route_code_one_array[$i]."',
								emp_code='".$emp_code."',
								acedns='".$acedns_one_array[$i]."',
								activate_datetime='".$download_time_one_array[$i]."',
								user_ip='".$_SERVER['REMOTE_ADDR']."',
								deactivate_datetime=CURRENT_TIMESTAMP()";
		mysql_query($sqlinsertswappinglogone);
	}
	for($k=0;$k<count($customer_code_two_array);$k++)
	{
		$sqlupdatecustomerroutetwo="UPDATE customer_route_emp_relation SET emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP() 
									WHERE customer_code='".$customer_code_two_array[$k]."' AND route_code='".$route_code_two_array[$k]."' 
									AND emp_code=".$emp_code_swap."";
		mysql_query($sqlupdatecustomerroutetwo);
		
		$sqlinsertswappinglogtwo="INSERT INTO employee_swapping_log SET 	customer_code='".$customer_code_two_array[$k]."',
								route_code='".$route_code_two_array[$k]."',
								emp_code=".$emp_code_swap.",
								acedns='".$acedns_two_array[$k]."',
								activate_datetime='".$download_time_two_array[$k]."',
								user_ip='".$_SERVER['REMOTE_ADDR']."',
								deactivate_datetime=CURRENT_TIMESTAMP()";
		mysql_query($sqlinsertswappinglogtwo);													
	}
	$sqlupddatetablestructure="UPDATE table_structure_master SET need_update='Y' WHERE table_name NOT IN('app_info','data_download_log','employee_master_login','emp_master','emp_menu_access','market_feedback_details','menu_access','menu_details','notification_details','OTP_details',
'product_details','route_plan_details','sauda_form_details','self_appraisal_details','survey_form_details','user_details','order_form_details'
)";
	if(mysql_query($sqlupddatetablestructure))
	{
		$sqldbupdateone="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$emp_code."'";
		mysql_query($sqldbupdateone);
		$sqldbupdatetwo="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$emp_code_swap."'";
		mysql_query($sqldbupdatetwo);
	}
	echo $GLOBALS['err_msg']="Employee swapping done successfully.";
	exit();
  }
}
?>