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
	if (document.getElementById('route_swap').value=='') 
	{
		alert('Please choose one route.');
		document.getElementById('route_swap').focus();
		return false;
	}
	if (document.getElementById('emp_swap').value.search(/\S/)==-1) 
	{
		alert('Please choose one employee for swapping.');
		document.getElementById('emp_swap').focus();
		return false;
	}
	document.getElementById('emp_swap_val').value=document.getElementById('emp_swap').value;
	var optionVal = new Array();
	for (var i = 0; i < document.getElementById('route_swap').options.length; i++) {
		if(document.getElementById('route_swap').options[i].selected){
		  //alert(document.getElementById('route_swap').options[i].value);
		  optionVal.push(document.getElementById('route_swap').options[i].value);
		}
	}
	document.getElementById('route_swap_val').value=optionVal;
	//alert(document.getElementById('route_swap').value);
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
 
 function swapping_route()
{
	xmlHttproute=GetXmlHttpObject()
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
	var url="select_swapping_route.php?emp_code="+emp_code;
	xmlHttproute.onreadystatechange=showswappingroute;
	xmlHttproute.open("GET",url,true);
	xmlHttproute.send(null);
}
function showswappingroute()
 {
    if(xmlHttproute.readyState==4 || xmlHttproute.readyState=="complete")
	 {
		var val=xmlHttproute.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("show_swap_route").innerHTML = val;
		 }
	}
 }
 
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Employee Route Swapping</strong></td>
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
                        <input type="hidden" name="mode" value="swapping_route">
                        <input type="hidden" name="emp_swap_val" id="emp_swap_val" value="">
                        <input type="hidden" name="route_swap_val" id="route_swap_val" value="">
                        
                        	<tr>
                        		<td align="right" width="40%">Choose Employee:</td>
                        		<td align="left" width="" style="vertical-align:top;" >
                                    <select name="emp_name" id="emp_name" onchange="javascript:swapping_emp();swapping_route();">
                                    <option value="">SELECT</option>
                                    <?php 
                                    $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ".$emp_hierarchy_condition." 
													ORDER BY emp_name ASC";
                                    $resultqueryemp = mysqli_query($link,$sqlqueryemp);
                                    $countemp=mysqli_num_rows($resultqueryemp);
                                    if($countemp>0){
                                    while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
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
                        		<td align="right" width="40%">Choose Route:</td>
                        		<td align="left" width="" style="vertical-align:top;" id="show_swap_route">
                                <?php if($_REQUEST['mode']=='swapping_route'){
									$sqlquerycustomerroute="SELECT DISTINCT route_code FROM customer_route_emp_relation  WHERE 
														route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
														AND emp_code='".$_REQUEST['emp_name']."'";
									$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
                                	echo "<select name=\"route_swap[]\" id=\"route_swap\" multiple=\"multiple\" style=\"height: 150px;width: 200px;\">";
									echo "<option value=\"\">Select</option>";
									while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute)){
										$route_code=$rowscustomerroute['route_code'];
										$sqlroute="SELECT  route_name FROM route_master WHERE route_code='".$route_code."'";
										$rsroute=mysqli_query($link,$sqlroute);
										$rowroute=mysqli_fetch_assoc($rsroute);
										$route_name=preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
										if( $_REQUEST['route_swap_val']=="'".$route_code."'"){ $selected='selected';}
										else											  $selected='';
										echo "<option value=\"'".$route_code."'\" ".$selected.">".$route_name."</option>";
									}
									echo "</select>";
                               }?>
                                </td>
                        	</tr>
                        	<tr>
                                <td align="right" width="40%">Employee for Swapping:</td>
                                <td align="left" width="" style="vertical-align:top;" id="show_swap_emp">
                                <?php if($_REQUEST['mode']=='swapping_route'){
                                	echo "<select name=\"emp_swap\" id=\"emp_swap\">";
									echo "<option value=\"\">Select</option>";
									$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y' AND 
												emp_code!='".$_REQUEST['emp_name']."' ".$emp_hierarchy_condition." ORDER BY emp_name ASC";
									$res_emp = mysqli_query($link,$sql_emp);
									while($row_emp = mysqli_fetch_assoc($res_emp)){
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
if($_REQUEST['mode']=='swapping_route')
{
	$emp_code=$_POST['emp_name'];
	$emp_code_swap=$_POST['emp_swap_val'];
	$route_code_swap=$_POST['route_swap_val'];
	$route_code_swap=str_replace("'","",$route_code_swap);
	$route_code_swap=explode(",",$route_code_swap);
	
	//print_r($route_code_swap);
	//exit();
	/*//set acedns=N for swapping employee existing customer
	$sqlupdatecustomerroutestatus="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 
									WHERE emp_code=".$emp_code_swap."";
	mysqli_query($link,$sqlupdatecustomerroutestatus); //--2019-07-03
	//End of set acedns=N for swapping employee existing customer
	//set acedns=N for swapping employee existing distributor
	$sqlupdatedistributorroutestatus="UPDATE distributor_route_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 
									WHERE emp_code=".$emp_code_swap."";
	mysqli_query($link,$sqlupdatedistributorroutestatus);
	//End of set acedns=N for swapping employee existing distributor*/

foreach($route_code_swap as $route_code_val)
	{
		$customer_code_one_array=array();
		$route_code_one_array=array();
		$acedns_one_array=array();
		$download_time_one_array=array();
		
		$distributor_code_one_array=array();
		$distributor_route_code_one_array=array();
		$distributor_download_time_one_array=array();
		//For Customer route emp relation
		$sqlfetchcutomerrouteone="SELECT customer_code,route_code,acedns,download_time  FROM 
								customer_route_emp_relation WHERE emp_code='".$emp_code."' AND route_code='".$route_code_val."'";
		$rsfetchcutomerrouteone=mysqli_query($link,$sqlfetchcutomerrouteone);
		while($rowfetchcutomerrouteone=mysqli_fetch_assoc($rsfetchcutomerrouteone))
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
		for($i=0;$i<count($customer_code_one_array);$i++)
		{
			$sqlchkexistingcustomer="SELECT customer_code,route_code,acedns  FROM customer_route_emp_relation WHERE 
								customer_code='".$customer_code_one_array[$i]."' AND route_code='".$route_code_val."' AND emp_code=".$emp_code_swap."";
			$rschkexistingcustomer=mysqli_query($link,$sqlchkexistingcustomer);
			$countchkexistingcustomer=mysqli_num_rows($rschkexistingcustomer);
			if($countchkexistingcustomer >0)//2019-10-17
			{					
				$sqldelfirst="DELETE FROM customer_route_emp_relation WHERE customer_code='".$customer_code_one_array[$i]."' 
								AND route_code='".$route_code_val."' AND emp_code=".$emp_code."";
				mysqli_query($link,$sqldelfirst);
			}
			else
			{
				$sqlupdatecustomerrouteone="UPDATE customer_route_emp_relation SET emp_code=".$emp_code_swap.",download_time=CURRENT_TIMESTAMP() 
											WHERE customer_code='".$customer_code_one_array[$i]."' AND route_code='".$route_code_val."' 
											AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatecustomerrouteone); //----2019-07-03
			}
			$sqlinsertswappinglogone="INSERT INTO employee_route_swapping_log SET customer_code='".$customer_code_one_array[$i]."',
									route_code='".$route_code_val."',
									emp_code='".$emp_code."',
									acedns='N',
									activate_datetime='".$download_time_one_array[$i]."',
									swapping_type='customerroute',
									user_ip='".$_SERVER['REMOTE_ADDR']."',
									deactivate_datetime=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlinsertswappinglogone);
			$sqlinsertswappinglogtwo="INSERT INTO employee_route_swapping_log SET customer_code='".$customer_code_one_array[$i]."',
									route_code='".$route_code_val."',
									emp_code=".$emp_code_swap.",
									acedns='Y',
									activate_datetime='".$download_time_one_array[$i]."',
									swapping_type='customerroute',
									user_ip='".$_SERVER['REMOTE_ADDR']."'";
			mysqli_query($link,$sqlinsertswappinglogtwo);
		}
		//END For Customer route emp relation
		//For Distributor route emp relation
		if(strtoupper($_SESSION['nick_name'])!='GOLDSTONE')
		{
			$sqlfetchdistributorrouteone="SELECT distributor_code,route_code,download_time  FROM 
									distributor_route_relation WHERE emp_code='".$emp_code."' AND route_code='".$route_code_val."'";
			$rsfetchdistributorrouteone=mysqli_query($link,$sqlfetchdistributorrouteone);
			while($rowfetchdistributorrouteone=mysqli_fetch_assoc($rsfetchdistributorrouteone))
			{
				$distributor_code_one=$rowfetchdistributorrouteone['distributor_code'];
				$distributor_route_code_one=$rowfetchdistributorrouteone['route_code'];
				$distributor_download_time_one=$rowfetchdistributorrouteone['download_time'];
				array_push($distributor_code_one_array,$distributor_code_one);
				array_push($distributor_route_code_one_array,$distributor_route_code_one);
				array_push($distributor_download_time_one_array,$distributor_download_time_one);
			}
			for($i=0;$i<count($distributor_code_one_array);$i++)
			{
				$sqlupdatedistributorrouteone="UPDATE distributor_route_relation SET emp_code=".$emp_code_swap.",download_time=CURRENT_TIMESTAMP() 
											WHERE distributor_code='".$distributor_code_one_array[$i]."' AND route_code='".$route_code_val."' 
											AND emp_code='".$emp_code."'";
										
				mysqli_query($link,$sqlupdatedistributorrouteone);
				$sqlinsertswappinglogonedist="INSERT INTO employee_route_swapping_log SET customer_code='".$distributor_code_one_array[$i]."',
										route_code='".$route_code_val."',
										emp_code='".$emp_code."',
										acedns='N',
										activate_datetime='".$distributor_download_time_one_array[$i]."',
										swapping_type='distributorroute',
										user_ip='".$_SERVER['REMOTE_ADDR']."',
										deactivate_datetime=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertswappinglogonedist);
				$sqlinsertswappinglogtwodist="INSERT INTO employee_route_swapping_log SET customer_code='".$distributor_code_one_array[$i]."',
										route_code='".$route_code_val."',
										emp_code=".$emp_code_swap.",
										acedns='Y',
										activate_datetime='".$distributor_download_time_one_array[$i]."',
										swapping_type='distributorroute',
										user_ip='".$_SERVER['REMOTE_ADDR']."'";
				mysqli_query($link,$sqlinsertswappinglogtwodist);
				//UPDATE Distributor Physical route
				$sqlupdatedistributorphysroute="UPDATE customer_route_emp_relation SET emp_code=".$emp_code_swap.",download_time=CURRENT_TIMESTAMP() 
											WHERE customer_code='".$distributor_code_one_array[$i]."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatedistributorphysroute);
			}
	   }
	}
	/*$sqlupddatetablestructure="UPDATE table_structure_master SET need_update='Y' WHERE table_name NOT IN('app_info','data_download_log','employee_master_login','emp_master','emp_menu_access','market_feedback_details','menu_access','menu_details','notification_details','OTP_details','product_group_master','product_sub_group_master','product_master','order_details'
'product_details','route_plan_details','sauda_form_details','self_appraisal_details','survey_form_details','user_details','order_form_details'
)";*/
$sqlupddatetablestructure="UPDATE table_structure_master SET need_update='Y' 
						WHERE table_name  IN('customer_master','route_master','distributor_route_relation')";
	if(mysqli_query($link,$sqlupddatetablestructure))
	{
		$sqldbupdateone="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$emp_code."'";
		mysqli_query($link,$sqldbupdateone);//--2019-07-03
		$sqldbupdatetwo="UPDATE table_structure_updation SET is_update='1' WHERE emp_code=".$emp_code_swap."";
		mysqli_query($link,$sqldbupdatetwo);//--2019-07-03
	}
	exit();
	?>
    <script language="JavaScript" type="text/javascript">alert('Route swapping done successfully.');window.location.href='adminEmployeeRouteSwapping.php';</script>
    <?php
	//echo $GLOBALS['err_msg']="Employee route swapping done successfully.";
	//exit();
  }
}
?>