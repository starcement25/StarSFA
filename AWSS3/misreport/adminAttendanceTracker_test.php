<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name']) == 'TECPL')
	{
		require("adminUtils_tecpl.php");
	}
	else
	{
		require("adminUtils.php");
	}

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
	

	if($mode =='add' || $mode =='edit')				disphtml("show_add_edit($_REQUEST[row_id]);");
	elseif($mode == 'save')							save_record($_REQUEST['prod_id']);
	elseif($mode =='change_status')					audit_status($_REQUEST['row_id']);
	elseif($mode =='delete_rec')					   delete_record($_REQUEST['row_id']);
	else    										   disphtml("main();");
ob_end_flush();

function main()
{
	$date = $_REQUEST['date'];
	if($_REQUEST['date']=="")  		$date = date('d-m-Y');
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND LO.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
?>
<script language="javascript">
function search_date()
{
	/*if((document.frmAttendence.from_date.value!="") && (document.frmAttendence.to_date.value!=""))
	{
			var from_yr = document.frmAttendence.from_date.value.substr(0,4);
			var from_mn = document.frmAttendence.from_date.value.substr(5,2);
			var from_dt = document.frmAttendence.from_date.value.substr(8,2);
			var to_yr = document.frmAttendence.to_date.value.substr(0,4);
			var to_mn = document.frmAttendence.to_date.value.substr(5,2);
			var to_dt = document.frmAttendence.to_date.value.substr(8,2);
			if(to_yr<from_yr){ alert("From-date should not be after To-date."); return false; }
			else if(to_yr==from_yr && to_mn<from_mn){ alert("From-date should not be after To-date."); return false; }
			else if(to_yr==from_yr && to_mn==from_mn && to_dt<from_dt){ alert("From-date should not be after To-date."); return false; }
			else { document.frmAttendence.search_mode.value = "SEARCH_DATE";document.frmAttendence.submit(); }
			document.frmAttendence.search_mode.value = "SEARCH_DATE";
			document.frmAttendence.submit();
	}*/
	if((document.frmAttendence.date.value.search(/\S/)==-1))
	{
		alert("Please choose a date.");
		document.frmAttendence.date.focus();
		return false;
	}			
	else
	{
		//alert("Enter From Date or To Date");
		//return false;
		document.frmAttendence.search_mode.value = "SEARCH_DATES";
		document.frmAttendence.submit();	
	}
}
function populate_date()
{
	document.frmAttendence.submit();
}
function search_att(val)
{
		if(val=='yesterday')
		{
			document.getElementById('yesterdayAttDisplay').style.display='';
			document.getElementById('todayAttDisplay').style.display='none';
			document.getElementById('monthlyAttDisplay').style.display='none';
			document.getElementById('employeetabledisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='none';
			if(document.getElementById('employeedate').style.display=='')
			{
				document.getElementById('employeedate').style.display='none';
			}
			if(document.getElementById('datedropdown').style.display=='')
			{
				document.getElementById('datedropdown').style.display='none';
			}
			if(document.getElementById('employeedropdown').style.display=='')
			{
				document.getElementById('employeedropdown').style.display='none';
			}
			if(document.getElementById('employeewiselist'))
			{
				document.getElementById('employeewiselist').style.display='none';
			}
		}
		if(val=='monthly')
		{
			document.getElementById('monthlyAttDisplay').style.display='';	
			document.getElementById('todayAttDisplay').style.display='none';	
			document.getElementById('yesterdayAttDisplay').style.display='none';
			document.getElementById('employeetabledisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='none';
			if(document.getElementById('employeedate').style.display=='')
			{
				document.getElementById('employeedate').style.display='none';
			}
			if(document.getElementById('datedropdown').style.display=='')
			{
				document.getElementById('datedropdown').style.display='none';
			}
			if(document.getElementById('employeedropdown').style.display=='')
			{
				document.getElementById('employeedropdown').style.display='none';
			}
			if(document.getElementById('employeewiselist'))
			{
				document.getElementById('employeewiselist').style.display='none';
			}
		}
		if(val=='today')
		{
			document.getElementById('monthlyAttDisplay').style.display='none';	
			document.getElementById('todayAttDisplay').style.display='';	
			document.getElementById('yesterdayAttDisplay').style.display='none';
			document.getElementById('employeetabledisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='none';
			if(document.getElementById('employeedate').style.display=='')
			{
				document.getElementById('employeedate').style.display='none';
			}
			if(document.getElementById('datedropdown').style.display=='')
			{
				document.getElementById('datedropdown').style.display='none';
			}
			if(document.getElementById('employeedropdown').style.display=='')
			{
				document.getElementById('employeedropdown').style.display='none';
			}
			if(document.getElementById('employeewiselist'))
			{
				document.getElementById('employeewiselist').style.display='none';
			}
		}
		if(val=='choice')
		{
			document.getElementById('employeedate').style.display='';
		}
		if(val=='employeewise')
		{
			document.getElementById('employeedropdown').style.display='';
			document.getElementById('datedropdown').style.display='none';
		}
		if(val=='datewise')
		{
			document.getElementById('datedropdown').style.display='';
			document.getElementById('employeedropdown').style.display='none';
			if(document.getElementById('employeewiselist'))
			{
				document.getElementById('employeewiselist').style.display='none';
			}

		}
		
}
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
function showEmployeeWisedisplay(val)
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var page='attendance';
	var mode='yourchoice';
	var url="selectEmployeeWiseAttendance.php?emp_code="+val+"&page="+page+"&mode="+mode;
	xmlHttp.onreadystatechange=showDetailsEmployee;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }

function showDetailsEmployee()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('yesterdayAttDisplay').style.display='none';
			document.getElementById('todayAttDisplay').style.display='none';
			document.getElementById('monthlyAttDisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('employeetabledisplay').style.display='';
		 	document.getElementById('employeetabledisplay').innerHTML=val;
		 }
	}
 }
function showDateWisedisplay()
{
	//alert(val);
	xmlHttpNext=GetXmlHttpObject()
	if (xmlHttpNext==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var fromDate=document.getElementById('from_date').value;
	
	var toDate=document.getElementById('to_date').value;
	
	var url="selectEmployeeDateWiseAttendance.php?fromDate="+fromDate+"&toDate="+toDate;
	xmlHttpNext.onreadystatechange=showDetailsEmployeeDateWise;
	xmlHttpNext.open("GET",url,true);
	xmlHttpNext.send(null);
  }

function showDetailsEmployeeDateWise()
 {
    if(xmlHttpNext.readyState==4 || xmlHttpNext.readyState=="complete")
	 {
		var val=xmlHttpNext.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('yesterdayAttDisplay').style.display='none';
			document.getElementById('todayAttDisplay').style.display='none';
			document.getElementById('monthlyAttDisplay').style.display='none';
			document.getElementById('employeetabledisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='';
		 	document.getElementById('employeetabledisplaydatewise').innerHTML=val;
		 }
	}
 }
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Attendance Tracker</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
			<!--table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2">Search</td>
				</tr>
				<tr>
					<td colspan="2" align="left">
						<table width="100%" align="left" border="0" cellpadding="0" cellspacing="1">
							<tr>
								<td align="left" width="10%">Date:</td>
								<td align="left" width="20%" style="vertical-align:top;">
									<input type="text" name="date" class="inplogin" readonly value="<?php //echo $_REQUEST['date'];?>" size=10 onClick="javascript:not_permit();";>&nbsp;
									<a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
                                </td>
								<td align="left" width="" style="padding-left:10px;">
									<input type="button" value="Search" class="inplogin" onClick="javascript:search_date();"-->
									<!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
								<!--/td>
							</tr>
                          </table>
                       </td>
                  </tr> 
			</table-->
           <script language="JavaScript" type="text/javascript">
			<!-- // create calendar object(s) just after form tag closed
			 // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
			 // note: you can have as many calendar objects as you need for your application
			/*var cal5 = new calendar3(document.forms['frmAttendence'].elements['date']);
			cal5.year_scroll = true;
			cal5.time_comp = false;*/
			/*var cal6 = new calendar3(document.forms['frmAttendence'].elements['to_date']);
			cal6.year_scroll = true;
			cal6.time_comp = false;*/
			//-->
			</script>
			<br><br>
			</form>
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
                <div id="employeetabledisplay" style="display:none;">
                </div>
                <div id="employeetabledisplaydatewise" style="display:none;">
                </div>
                <!----------------------------------Start Table for first time page loading----------------------------------------------!-->
                <?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']=='yesterday' || $_REQUEST['mode']==''){ 
							$colspan = '8';
					  }
					  else{
						  $colspan = '7';
					  }
				?>
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: <?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']==''){?> ''<?php }else{?>none<?php }?>;height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="<?php echo $colspan; ?>" align="center"><strong>Date: <?php echo date('d-m-Y');?></strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="20%" align="left" style="padding-left:20px;">DNS Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="20%" align="left" style="padding-left:20px;">HQ</td>
                        <td width="20%" align="left" style="padding-left:20px;">Time</td>
                        <td width="" align="left" style="padding-left:20px;">Locate</td>
                        <?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']=='yesterday' || $_REQUEST['mode']==''){ ?>
                        <td width="" align="left" style="padding-left:20px;">Checkout</td>
                        <?php } ?>
                    </tr> 
                    <?php
						$date=date('d-m-Y');
                        if($date!='')
                        {
                            $date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
                        }
                        else
                        {
                            $date_condition='';
                        }
                        
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM 
									location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
									AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." 
									ORDER BY EM.emp_code ASC ";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{ 
					?>
						<tr> 
							<td align="center" colspan="5">No records found.</td>
						</tr>
					<?php }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$time=$rowinformation['time'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$dns_emp_code=$rowinformation['dns_emp_code'];
							$HQ=$rowinformation['HQ'];
							
							echo $sql_checkout = "SELECT DATE_FORMAT(SUBSTRING(LO.trans_id,-14),'%H:%i:%s') AS checkout_time FROM location LO WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'CH%'".$date_condition;die;	
							$res_checkout = mysql_query($sql_checkout);
							$row_checkout = mysql_fetch_array($res_checkout);
							$check_out_time = $row_checkout['checkout_time'];
							if($check_out_time == '')
								$check_out_time = '--';
					?>
							<tr> 
                                    <td valign="top" align="center"><?=$cnt++ ?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $emp_code;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $dns_emp_code;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $emp_name;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $HQ;?></td>
									<td align="left" valign="top" style="padding-left:20px;"><?php echo $time;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><a href="adminAttendanceLocate.php?trans_id=<?php echo $trans_id;?>&emp_code=<?php echo $emp_code;?>&mode=today&page=attendance" style="color:#930;font-weight:bold;">Locate</a></td>
                                    <?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']=='yesterday' || $_REQUEST['mode']==''){ ?>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $check_out_time; ?></td>
                                    <?php } ?>
                              </tr>
                             <?php				
						}
					}
				?>
            </table>
         <!------------------------------------------------End of Table for first time page loading----------------------------------------------!-->
         <!-----------------------------------------------Start of Table for Yesterday Employee Attendence Check------------------------------------>
         	<?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']=='yesterday' || $_REQUEST['mode']==''){ 
							$colspan = '8';
					  }
					  else{
						  $colspan = '7';
					  }
				?>
            <table width="80%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: <?php if($_REQUEST['mode']=='yesterday'){?> ''<?php }else{?>none<?php }?>;height: 150px;overflow-y: scroll;" 
                id="yesterdayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="<?php echo $colspan; ?>" align="center"><strong>Date: <?php $curdate=date('d-m-Y');
						echo $yesterdaydate=date('d-m-Y', strtotime("-1 days,$curdate "));?></strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="20%" align="left" style="padding-left:20px;">DNS Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="20%" align="left" style="padding-left:20px;">HQ</td>
                        <td width="20%" align="left" style="padding-left:20px;">Time</td>
                        <td width="" align="left" style="padding-left:20px;">Locate</td>
                        <?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']=='yesterday' || $_REQUEST['mode']==''){ ?>
                        <td width="" align="left" style="padding-left:20px;">Checkout</td>
                        <?php } ?>
                    </tr> 
                    <?php
						
                        if($yesterdaydate!='')
                        {
                            $date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$yesterdaydate."%'";
                        }
                        else
                        {
                            $date_condition='';
                        }
                        
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM 
									location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND SUBSTRING(EM.emp_code,1,1)!='C' 
									".$emp_hierarchy_condition.$date_condition." ORDER BY EM.emp_code ASC ";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{ 
					?>
						<tr> 
							<td align="center" colspan="5">No records found.</td>
						</tr>
					<?php }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$time=$rowinformation['time'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$dns_emp_code=$rowinformation['dns_emp_code'];
							$HQ=$rowinformation['HQ'];
							
							$sql_checkout = "SELECT DATE_FORMAT(SUBSTRING(LO.date,-14),'%H:%i:%s') AS checkout_time FROM location LO WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'CH%'".$date_condition;
							$res_checkout = mysql_query($sql_checkout);
							$row_checkout = mysql_fetch_array($res_checkout);
							$check_out_time = $row_checkout['checkout_time'];
							if($check_out_time == '')
								$check_out_time = '--';
					?>
							<tr> 
                                    <td valign="top" align="center"><?=$cnt++ ?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $emp_code;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $dns_emp_code;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $emp_name;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $HQ;?></td>
									<td align="left" valign="top" style="padding-left:20px;"><?php echo $time;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><a href="adminAttendanceLocate.php?trans_id=<?php echo $trans_id;?>&emp_code=<?php echo $emp_code;?>&mode=yesterday&page=attendance" style="color:#930;font-weight:bold;">Locate</a></td>
                                    <?php if($_REQUEST['mode']=='today' || $_REQUEST['mode']=='yesterday' || $_REQUEST['mode']==''){ ?>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $check_out_time; ?></td>
                                    <?php } ?>
                              </tr>
                             <?php				
						}
					}
				?>
            </table>
         <!------------------------------------------------End of Table for Yesterday Employee Attendence Check----------------------------------!-->
		 <!-----------------------------------------------Start of Table for monthly Emloyee Attendence Check---------------------------------------->
         
            <table width="80%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: <?php if($_REQUEST['mode']=='monthly'){?> ''<?php }else{?>none<?php }?>;height: 150px;overflow-y: scroll;" id="monthlyAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>For <?php echo date('F');?>,<?php echo date('Y'); ?></strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="20%" align="left" style="padding-left:20px;">DNS Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="20%" align="left" style="padding-left:20px;">HQ</td>
                        <td width="20%" align="left" style="padding-left:20px;">Days on the Field</td>
                        <td width="" align="left" style="padding-left:20px;">Locate</td>
                    </tr> 
                    <?php
                        $date_condition_month=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
                        
						$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time 
										FROM location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
										AND SUBSTRING(EM.emp_code,1,1)!='C'	".$emp_hierarchy_condition.$date_condition_month."
										GROUP BY LO.emp_code  ORDER BY EM.emp_code ASC ";
						$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
						$count=mysql_num_rows($resinformation);
					if($count==0)
					{ 
					?>
						<tr> 
							<td align="center" colspan="5">No records found.</td>
						</tr>
					<?php }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							
							$trans_id=$rowinformation['trans_id'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$dns_emp_code=$rowinformation['dns_emp_code'];
							$HQ=$rowinformation['HQ'];
							$sqlcountatt="SELECT count(emp_code) AS totalattendence FROM location WHERE trans_id LIKE 'A%' 
										AND emp_code='".$emp_code."'  AND YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE())";
							$rescountatt=mysql_query($sqlcountatt) or die(mysql_error()." Error in select count attendence: ".$sqlcountatt);
							$rowcountatt=mysql_fetch_array($rescountatt);
							$totalattendance=$rowcountatt['totalattendence'];
					?>
							<tr> 
                                    <td valign="top" align="center"><?=$cnt++ ?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $emp_code;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $dns_emp_code;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $emp_name;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><?php echo $HQ;?></td>
									<td align="left" valign="top" style="padding-left:20px;"><?php echo $totalattendance;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;"><a href="adminMultiAttendanceLocate.php?emp_code=<?php echo $emp_code;?>&mode=monthly&page=attendance" style="color:#930;font-weight:bold;">Locate</a></td>
                              </tr>
                             <?php				
						}
					}
				?>
            </table>

         
   <!----------------------------------End of Table for monthly Employee Attendence Check------------------------------------------------------------------!-->
   <!----------------------------------------Start of Table for populate employeelist from attendance locate back------------------------------------------!-->
 <?php
 	if($_REQUEST['emp_code']!='' && $_REQUEST['radio_search']=='employee' && $_REQUEST['mode']=='yourchoice')
	{
		$emp_code=$_REQUEST['emp_code'];
		$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsemp=mysql_query($sqlemp) or die(mysql_error()." Error in select employee name and code : ".$sqlemp);
		$rowemp=mysql_fetch_array($rsemp);
		$emp_name=$rowemp['emp_name'];

		
	$tableval='
<table width="47%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 250px;overflow-y: scroll;display:block;" id="employeewiselist">
    <tr class="TDHEAD" > 
        <td colspan="5" align="center"><strong>Emp Name: '.$emp_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Emp Code:'. $emp_code.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
        <td width="30%" align="left" style="padding-left:20px;">Date</td>
        <td width="30%" align="left" style="padding-left:20px;">Time</td>
        <td width="" align="left" style="padding-left:20px;">Locate</td>
    </tr> ';
        
    $sqlinformation="SELECT DATE_FORMAT(date,'%T') AS time,DATE_FORMAT(date,'%b,%e %Y') AS date,trans_id FROM 
                    location LO WHERE LO.trans_id LIKE 'A%' AND LO.emp_code='".$emp_code."' ORDER BY LO.emp_code ASC ";
    $resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
    $count=mysql_num_rows($resinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
            $time=$rowinformation['time'];
            $date=$rowinformation['date'];
            
			$rowval.='<tr> 
                    <td valign="top" align="center">'.$cnt++.'</td>
                    <td align="left" valign="top" style="padding-left:20px;">'.$date.'</td>
                    <td align="left" valign="top" style="padding-left:20px;">'.$time.'</td>
                    <td align="left" valign="top" style="padding-left:20px;"><a href="adminAttendanceLocate.php?trans_id='.$trans_id.'&emp_code='.$emp_code.'&radio_search=employee&mode=yourchoice&page=attendance" style="color:#930;font-weight:bold;">Locate</a></td>
              </tr>';
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval.'<br />'.'<br />';
	}
 ?>
 
 
 <!----------------------------------------End of Table for populate employeelist from attendance locate back--------------------------------------!-->

            <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1">
                <tr> 
                    <td align="center" width="100%">
                    	<input type="radio" value="yesterday" name="radio_type" onClick="javascript:search_att('today');" 
						<?php if($_REQUEST['mode']=='today'){?>checked<?php }?>/>Today
                        &nbsp;&nbsp;&nbsp;
                    	<input type="radio" value="yesterday" name="radio_type" onClick="javascript:search_att('yesterday');" 
						<?php if($_REQUEST['mode']=='yesterday'){?>checked<?php }?>/>Yesterday
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" value="monthly" name="radio_type" onClick="javascript:search_att('monthly');" <?php if($_REQUEST['mode']=="monthly"){?>checked<?php }?>/>Monthly
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" value="yourchoice" name="radio_type" onclick="search_att('choice');" <?php if($_REQUEST['mode']=="yourchoice"){?>checked<?php }?>/>Your Choice
                    
                    </td>
                </tr>
                <tr id="employeedate" style="display:
				<?php if($_REQUEST['radio_search']=='employee' || $_REQUEST['radio_search']=='datewise'){?>''<?php }else{?>none<?php }?>">
                	<td align="center" width="70%">
                    	<input type="radio" value="employee" name="radio_search" onClick="javascript:search_att('employeewise');" 
						<?php if($_REQUEST['radio_search']=='employee'){?>checked<?php }?>/>Employeewise
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" value="datewise" name="radio_search" onClick="javascript:search_att('datewise');" 
						<?php if($_REQUEST['radio_search']=='datewise'){?>checked<?php }?>/>Datewise
                    </td>
                </tr>
             </table>
 <!------------------------------------------------Start of Table for populate employee dropdown------------------------------------------------------!-->
                <table width="40%" align="center" border="0" cellpadding="5" cellspacing="1" 
                style="display:<?php if($_REQUEST['radio_search']=="employee"){?>''<?php }else{?>none<?php }?>;" id="employeedropdown" class="border">
                        <tr>
                            <td width="10%">Employee:</td>
                            <td width="80%">
                                    <?php
										$emp_code=$_REQUEST['emp_code'];
									 echo PopulateSelect('emp_code', "SELECT EM.emp_code,EM.emp_name FROM employee_master EM,location LO WHERE EM.emp_code=LO.emp_code AND LO.trans_id LIKE 'A%' AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ", 'emp_code', 'emp_name', $emp_code,"onChange=javascript:showEmployeeWisedisplay(this.value);",'inplogin');?>					
                                &nbsp;
                            </td>
                        </tr>
                 </table> 
 <!------------------------------------------------End of Table for populate employee dropdown----------------------------------------------------------!-->

 <!------------------------------------------------Start of Table for populate date range dropdown------------------------------------------------------!-->
                 <table width="60%" align="center" border="0" cellpadding="5" cellspacing="1" style="display:<?php if($_REQUEST[search_mode]=="SEARCH_DATE"){?>''<?php }else{?>none<?php }?>;" id="datedropdown" class="border">
                    <tr>
                        <td align="left" width="15%">From Date:</td>
                        <td align="left" width="20%" style="vertical-align:top;">
                             <?php 
							 $from_date=$_REQUEST['from_date'];
							 echo PopulateSelect('from_date', "SELECT DATE_FORMAT(date,'%d-%m-%Y') AS date FROM location WHERE trans_id LIKE 'A%' GROUP BY DATE_FORMAT(date,'%d-%m-%Y') ORDER BY DATE_FORMAT(date,'%Y-%m-%d') DESC ", 'date', 'date', $from_date,'','inplogin');?>					
                        </td>
                        <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                        <td width="20%" style="vertical-align:top;">
                            <?php 
							$to_date=$_REQUEST['to_date'];
							echo PopulateSelect('to_date', "SELECT DATE_FORMAT(date,'%d-%m-%Y') AS date FROM location WHERE trans_id LIKE 'A%' GROUP BY DATE_FORMAT(date,'%d-%m-%Y') ORDER BY DATE_FORMAT(date,'%Y-%m-%d') DESC ", 'date', 'date', $to_date,'','inplogin');?>					
                        </td>
                        <td align="left" width="" style="padding-left:10px;">
                            <input type="button" value="Search" class="inplogin" onClick="javascript:showDateWisedisplay();">
                            <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                        </td>
                    </tr>
                </table>  
 <!------------------------------------------------End  of Table for populate date range dropdown------------------------------------------------------!-->
		</td>
	</tr>
</table>
<?php }//End of main()?>>>