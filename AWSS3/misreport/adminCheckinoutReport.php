<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

disphtml("main();");
ob_end_flush();
function main()
{
	$mode = $_REQUEST['mode'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>

<script>
  webshims.setOptions('waitReady', false);
  webshims.setOptions('forms-ext', {types: 'date'});
  webshims.polyfill('forms forms-ext');
</script>
    <?php
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
function sendDcrMail(val1,val2,val3)
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="adminSendDcrMail.php?emp_code="+val1+"&nick_name="+val2+"&date="+val3;
	xmlHttp.onreadystatechange=sendMailDCR;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function sendMailDCR()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 alert("DCR of "+val+" emailed successfully");
		 }
	}
 }
function showEmployeeWisedisplay(val,val1,val2)
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	document.getElementById('orderDetails').style.display='none';
	document.getElementById('collectionDetails').style.display='none';
	document.getElementById('customertabledisplaydatewise').style.display='none';
	if(document.getElementById('employeewisecutomerlist'))
	{
		document.getElementById('employeewisecutomerlist').style.display='none';
	}
	
	var url="selectEmployeeWiseActivity.php?emp_code="+val+"&mode="+val1+"&page="+val2;
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
			//document.getElementById('yesterdayAttDisplay').style.display='none';
			//document.getElementById('todayAttDisplay').style.display='none';
			//document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('customertabledisplaydatewise').style.display='';
		 	document.getElementById('customertabledisplaydatewise').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
function showAttendaceActivitydisplay(val)
{
	//alert(val);
	xmlHttpNext=GetXmlHttpObject()
	if (xmlHttpNext==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	}
	if(document.getElementById('AttendanceActivity').style.display==''){ 
		document.getElementById('AttendanceActivity').style.display='none';
	}
	
	
	if(val=='T')
	{
	  var url="showAttendanceActivityCheckinout.php?val="+val;
	}
	if(val=='MTD' || val=='YTD')
	{
	  var url="showAttendanceActivityMonthlyCheckinout.php?val="+val;
	}
	if(val == 'custom'){
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			return false;
		}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			return false;
		}
		var url="showAttendanceActivityMonthlyCheckinout.php?val="+val+"&start_date="+start_date+"&end_date="+end_date+"";
	}

	xmlHttpNext.onreadystatechange=showAttendanceActivity;
	xmlHttpNext.open("GET",url,true);
	xmlHttpNext.send(null);
  }

function showAttendanceActivity()
 {
    if(xmlHttpNext.readyState==4 || xmlHttpNext.readyState=="complete")
	 {
		var val=xmlHttpNext.responseText;
		//alert(val);
		if(val!="")
		 {
		 	document.getElementById('AttendanceActivity').style.display='';
		 	document.getElementById('AttendanceActivity').innerHTML=val;
			if(document.getElementById('orderDetails').style.display=='')
			{
				document.getElementById('orderDetails').style.display='none';
			}
			if(document.getElementById('collectionDetails').style.display=='')
			{
				document.getElementById('collectionDetails').style.display='none';
			}
			document.getElementById('loader').style.display='none';
			document.getElementById('customertabledisplaydatewise').style.display='none';
			if(document.getElementById('AttendanceActivityBack')){ 
				document.getElementById('AttendanceActivityBack').style.display='none';
			}
			document.getElementById('employeewisecutomerlist').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
 function populateOrderDetails(val,val1)
	{
	//alert(val);
	xmlHttpOrder=GetXmlHttpObject()
	if (xmlHttpOrder==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	
	var url="populateOrderDetails.php?trans_id="+val+"&customer_code="+val1;
	
	xmlHttpOrder.onreadystatechange=showOrderDetails;
	xmlHttpOrder.open("GET",url,true);
	xmlHttpOrder.send(null);
  }

function showOrderDetails()
 {
    if(xmlHttpOrder.readyState==4 || xmlHttpOrder.readyState=="complete")
	 {
		var val=xmlHttpOrder.responseText;
		//alert(val);
		if(val!="")
		 {
			//document.getElementById('yesterdayAttDisplay').style.display='none';
			//document.getElementById('todayAttDisplay').style.display='none';
			//document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('orderDetails').style.display='';
		 	document.getElementById('orderDetails').innerHTML=val;
			document.getElementById('loader').style.display='none';
			document.getElementById('employeewisecutomerlist').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
 function populateCollectionDetails(val,val1,val2)
	{
	//alert(val);
	xmlHttpCollection=GetXmlHttpObject()
	if (xmlHttpCollection==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	
	var url="populateCollectionDetails.php?trans_id="+val+"&customer_code="+val1+"&sale_type="+val2;
	
	xmlHttpCollection.onreadystatechange=showCollectionDetails;
	xmlHttpCollection.open("GET",url,true);
	xmlHttpCollection.send(null);
  }

function showCollectionDetails()
 {
    if(xmlHttpCollection.readyState==4 || xmlHttpCollection.readyState=="complete")
	 {
		var val=xmlHttpCollection.responseText;
		//alert(val);
		if(val!="")
		 {
			//document.getElementById('yesterdayAttDisplay').style.display='none';
			//document.getElementById('todayAttDisplay').style.display='none';
			//document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('collectionDetails').style.display='';
		 	document.getElementById('collectionDetails').innerHTML=val;
			document.getElementById('orderDetails').style.display='none';
			document.getElementById('loader').style.display='none';
			document.getElementById('employeewisecutomerlist').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
  function populateAuditDetails(val,val1)
	{
	//alert(val);
	xmlHttpaudit=GetXmlHttpObject()
	if (xmlHttpaudit==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	
	var url="populateAuditDetails.php?trans_id="+val+"&customer_code="+val1;
	
	xmlHttpaudit.onreadystatechange=showAuditDetails;
	xmlHttpaudit.open("GET",url,true);
	xmlHttpaudit.send(null);
  }

function showAuditDetails()
 {
    if(xmlHttpaudit.readyState==4 || xmlHttpaudit.readyState=="complete")
	 {
		var val=xmlHttpaudit.responseText;
		//alert(val);
		if(val!="")
		 {
			//document.getElementById('yesterdayAttDisplay').style.display='none';
			//document.getElementById('todayAttDisplay').style.display='none';
			//document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('auditDetails').style.display='';
		 	document.getElementById('auditDetails').innerHTML=val;
			document.getElementById('orderDetails').style.display='none';
			document.getElementById('loader').style.display='none';
			document.getElementById('employeewisecutomerlist').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }

function showEmployeeWiseAttendacedisplay(val,val1,start_date,end_date,val2)
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="selectEmployeeWiseAttendance.php?emp_code="+val+"&mode="+val1+"&page="+val2+"&start_date="+start_date+"&end_date="+end_date;
	xmlHttp.onreadystatechange=showDetailsEmployeeAttendance;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }

function showDetailsEmployeeAttendance()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('employeetabledisplay').style.display='';
		 	document.getElementById('employeetabledisplay').innerHTML=val;
		 }
	}
 }
  function showRoutePlandisplay(val)
 {
	xmlHttpNext=GetXmlHttpObject()
	if (xmlHttpNext==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	}
	if(document.getElementById('AttendanceActivity').style.display==''){ 
		document.getElementById('AttendanceActivity').style.display='none';
	}
	if(document.getElementById('RoutePlan').style.display==''){ 
		document.getElementById('RoutePlan').style.display='none';
	}
	
	if(val=='T')
	{
	  var url="showRoutePlan.php?val="+val;
	}
	if(val=='MTD' || val=='YTD')
	{
	  var url="showRoutePlanMonthly.php?val="+val;
	}

	xmlHttpNext.onreadystatechange=showRoutePlan;
	xmlHttpNext.open("GET",url,true);
	xmlHttpNext.send(null);
  }

function showRoutePlan()
 {
    if(xmlHttpNext.readyState==4 || xmlHttpNext.readyState=="complete")
	 {
		var val=xmlHttpNext.responseText;
		//alert(val);
		if(val!="")
		 {
		 	if(document.getElementById('AttendanceActivity').style.display==''){
				document.getElementById('AttendanceActivity').style.display='none';
			}
			document.getElementById('RoutePlan').style.display='';
		 	document.getElementById('RoutePlan').innerHTML=val;
			if(document.getElementById('RoutePlanDetails').style.display=='')
			{
				document.getElementById('RoutePlanDetails').style.display='none';
			}
			if(document.getElementById('orderDetails').style.display=='')
			{
				document.getElementById('orderDetails').style.display='none';
			}
			if(document.getElementById('auditDetails').style.display=='')
			{
				document.getElementById('auditDetails').style.display='none';
			}
			if(document.getElementById('loader').style.display=='')
			{
				document.getElementById('loader').style.display='none';
			}
			if(document.getElementById('customertabledisplaydatewise').style.display=='')
			{
				document.getElementById('customertabledisplaydatewise').style.display='none';
			}
			if(document.getElementById('AttendanceActivityBack')){ 
				document.getElementById('AttendanceActivityBack').style.display='none';
			}
			if(document.getElementById('employeewisecutomerlist')){
				document.getElementById('employeewisecutomerlist').style.display='none';
			}
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
 function showEmployeeWiseRouteplandisplay(val,val1,val2)
 {
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="selectEmployeeWiseRoutePlan.php?emp_code="+val+"&mode="+val1+"&page="+val2;
	xmlHttp.onreadystatechange=showDetailsEmployeeRoutePlan;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }

function showDetailsEmployeeRoutePlan()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('RoutePlanDetails').style.display='';
		 	document.getElementById('RoutePlanDetails').innerHTML=val;
		 }
	}
 }
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>CHECK IN CHECK OUT REPORT</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
			
			<br><br>
			</form>
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
                <table width="60%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="8" align="center"><strong>CHECK IN CHECK OUT REPORT</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <td width="30%" align="left" style="padding-left:20px;">Available Field <br /><span style="padding-left:20px;">Force</span></td>
                        <td width="15%" align="left" style="padding:0px 20px 0px 20px;">Present</td>
                        <td width="" align="left" style="padding-left:20px;">Customer Visited</td>
                    </tr> 
                    <?php
						$sqlfieldforce="SELECT COUNT(EM.emp_code) AS available_field_force FROM employee_master EM,changepassword CH 
										WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND SUBSTRING(EM.emp_code,1,1)!='C' 
										".$emp_hierarchy_condition_one."";
						$resfieldforce=mysql_query($sqlfieldforce) or die(mysql_error()." Error in select field force: ".$sqlfieldforce);
						$rowfieldforce=mysql_fetch_array($resfieldforce);
						$av_field_force=$rowfieldforce['available_field_force'];
						
						for($i=1;$i<=3;$i++)
						{
							if($i==1)
							{
								$date=date('Y-m-d');
								$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
								$sl_value='Today';
								$val='T';
							}
							//For MTD OR Month Today
							if($i==2)
							{
								$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) AND MONTH(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
								$sl_value='MTD';
								$val='MTD';
							}
							//For YTD OR Year Today
							if($i==3)
							{
								$date=gmdate('d',strtotime('+330 minute'));
								$month=gmdate('m',strtotime('+330 minute'));
								$year=gmdate('Y',strtotime('+330 minute'));
								
								$hour=gmdate('H',strtotime('+330 minute'));
								$minute=gmdate('i',strtotime('+330 minute'));
								$second=gmdate('s',strtotime('+330 minute'));
								
								if($month>='04'){
									$fiinancial_year=$year.'-04-01';
								}
								else
								{
									$fiinancial_year=($year-1).'-04-01';
								}

								//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
								$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
											AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
								$sl_value='YTD';
								$val='YTD';
							}
							
							$sqlpresent="SELECT COUNT(LO.trans_id) AS no_present  FROM location LO WHERE LO.trans_id LIKE 'A%' 
										AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition."";
							$respresent=mysql_query($sqlpresent) or die(mysql_error()." Error in select present information: ".$sqlpresent);
							$rowpresent=mysql_fetch_array($respresent);
							$present=$rowpresent['no_present'];
							//For MTD OR Month Today
							if($i==2)
							{
								$days=return_no_days('','M');
								$present=ceil($present/$days);
							}
							//For YTD OR Year Today
							if($i==3)
							{
								 
								 $now = strtotime(date('Y-m-d')); // or your date as well
								 $start_date = strtotime("2012-06-16");
								 $datediff = $now - $start_date;
								 $nodays=floor($datediff/(60*60*24));
								 $days=return_no_days($nodays,'Y');
								 $present=ceil($present/$days);
							}
							
							$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE SUBSTRING(LO.trans_id,1,2) IN
										 		('CI') AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition."";
							$rescustomervisit=mysql_query($sqlcustomervisit) or die(mysql_error()." Error in select customer visit: ".$sqlcustomervisit);
							$rowcustomervisit=mysql_fetch_array($rescustomervisit);
							$no_customer_visit=$rowcustomervisit['no_visit'];
							
					?>
							<tr> 
                                    <td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
					onClick="javascript:showAttendaceActivitydisplay('<?=$val?>')"  style="color:#930;font-weight:bold;"><?=$sl_value?></a></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $av_field_force;?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $present;?></td>
									<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $no_customer_visit;?></td>
                              </tr>
                             <?php				
							}
						?>
                        <tr>
                                <td colspan="4">
                                <div id="date_div" align="center" ><b>Custom</b><br />
From:<input type="date" name="start_date" id="start_date" value="<?php echo $_REQUEST['start_date']; ?>" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="<?php echo $_REQUEST['end_date']; ?>" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="showAttendaceActivitydisplay('custom');" />
                                </td>
                              </tr>
            </table><br />
         <!------------------------------------------------End of Table for first time page loading----------------------------------------------!-->
  			<div id="AttendanceActivity" style="display:none;">
                </div><br />
  
  
  <!----------------------------------------Start of Table for populate employee date wise locate back-----------------------------------------------------!-->
 <?php
 	//For date wise Employee activity listing
	
	if($_REQUEST['page']=='checkinoutreport')
	{	
		$mode=$_REQUEST['mode'];
		
		if($mode=='T')
		{
			$date=date('Y-m-d');
			$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
			$headerval=date('d-m-Y',strtotime($date));
		} 
		if($mode=='MTD')
		{
			$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
								AND MONTH(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
			$headerval=date('F').' ,'.date('Y');
		}
		if($mode=='YTD')
		{
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			
			if($month>='04'){
				$fiinancial_year=$year.'-04-01';
			}
			else
			{
				$fiinancial_year=($year-1).'-04-01';
			}

			//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
			$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
						AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
			$headerval=date('Y');
		}
		if($mode == 'custom'){
			$date_condition ="  AND (DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."')";
		}
		if($mode=='T')
		{
			$tablevalattendance='<div id="AttendanceActivityBack" style="display:""">
			<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
						style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
						<tr class="TDHEAD" > 
							<td colspan="5" align="center"><strong>Attendance ON '.date('d-m-Y',strtotime($date)).'</strong></td>
						</tr>
						<tr class="TDHEAD_SUB"> 
							<td width="5%" align="center">Sl</td>
							<td width="20%" align="left" style="padding-left:20px;">Emp code</td>
							<td width="30%" align="left" style="padding-left:20px;">Name</td>
							<td width="20%" align="left" style="padding-left:20px;">Time</td>
							<td width="" align="left" style="padding-left:20px;">Locate</td>
						</tr>'; 
						$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM 
										location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
										AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." ORDER BY EM.emp_code ASC ";
						$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
						$count=mysql_num_rows($resinformation);
						if($count==0)
						{
							$tablevalattendance.='<tr><td align="center" colspan="5">No records found.</td></tr>';
							
						 }
					else{
							$cnt=$GLOBALS[start]+1;
							while($rowinformation=mysql_fetch_array($resinformation))
							{
								$trans_id=$rowinformation['trans_id'];
								$time=$rowinformation['time'];
								$emp_name=$rowinformation['emp_name'];
								$emp_code=$rowinformation['emp_code'];
								$rowvalattendance.="<tr> 
												<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$time."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
												<a href=\"adminAttendanceLocate.php?trans_id=$trans_id&emp_code=$emp_code
												&mode=$mode&page=checkinoutreport\" style=\"color:#930;font-weight:bold;\">Locate</a></td>
										  </tr>";
							}
						}
				$tablevalattendanceend.='</table><br />';
			}
			
			if($mode=='MTD' || $mode=='YTD' || $mode=='custom')
			{
				$tablevalattendance='<div id="AttendanceActivityBack" style="display:"""><table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="5" align="center"><strong>Attendance On '.$headerval.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="20%" align="left" style="padding-left:20px;">Days on the Field</td>
                        <td width="" align="left" style="padding-left:20px;">Locate</td>
                    </tr>'; 
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time 
									 FROM location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%'  
									 AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition."
									 GROUP BY LO.emp_code  ORDER BY EM.emp_code ASC ";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select attendance information monthly: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{
						$tablevalattendance.='<tr><td align="center" colspan="5">No records found.</td></tr>';
						
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$time=$rowinformation['time'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$sqlcountatt="SELECT count(emp_code) AS totalattendence FROM location LO WHERE LO.trans_id LIKE 'A%' 
										  AND LO.emp_code='".$emp_code."' ".$date_condition."";
							$rescountatt=mysql_query($sqlcountatt) or die(mysql_error()." Error in select count attendence: ".$sqlcountatt);
							$rowcountatt=mysql_fetch_array($rescountatt);
							$totalattendance=$rowcountatt['totalattendence'];

							$rowvalattendance.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"javascript:void(0)\" 
											onClick=\"javascript:showEmployeeWiseAttendacedisplay('".$emp_code."','".$mode."','checkinoutreport')\"  
											style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$totalattendance."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"adminMultiAttendanceLocate.php?emp_code=$emp_code&mode=$mode
												&page=checkinoutreport\" style=\"color:#930;font-weight:bold;\">Locate</a></td>
									  </tr>";
						}
					}
  				$tablevalattendanceend.='</table><br /><div id="employeetabledisplay" style="display:none;">
                </div><br />';
			}
			if(stk_audit=='yes'){
				$stk_audit_TH='<td width="11%" align="left" style="padding-left:20px;">Stock Audit<br /><span style="padding-left:2px;">(Qty)</span></td>';
			}
			else
			{
				$stk_audit_TH='';
			}
			//echo 'fdgfdfd'.$nick_name;
			if(need_DCR == 'yes'){
				$DCR_TH='<td width="5%" align="left" style="padding-left:20px;">DCR</td>';
			}
$tablevalactivity='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="9" align="center"><strong>Customer Activity ON '.$headerval.'</strong></td>
    </tr>
     <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="12%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="16%" align="left" style="padding-left:20px;">Name</td>
		<td width="15%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>'.$DCR_TH.'
    </tr> ';
		$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,2) IN
						('CI') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." 
						GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			
			$DCR_TD="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
					<a href=\"adminDCRCheckinout.php?emp_code=".$emp_code."&mode=".$mode."&start_date=".$_REQUEST['start_date']."&end_date=".$_REQUEST['end_date']."&page=checkinoutreport\"style=\"color:#930;font-weight:bold;\">DCR</a></td>";
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['no_of_visit']."</td>
					".$stk_audit_TD.$DCR_TD."
              </tr>";
        }
$tablevalend='</table></div>';				
$finalval=$tablevalattendance.$rowvalattendance.$tablevalattendanceend.$tablevalactivity.$rowval.$tablevalend;

echo $finalval.'<br />';
	}
		
	//For Listing the customer of Employee Activity
	
 ?>
 <!----------------------------------------End of Table for populate populate employee date wise locate back----------------------------------------------!-->
                <div id="customertabledisplaydatewise" style="display:none">
                </div><br />
                <div id="RoutePlan" style="display:none">
                </div><br />
                <div id="RoutePlanDetails" style="display:none">
                </div><br />
                <div id="orderDetails" style="display:none">
                </div><br />
                <div id="auditDetails" style="display:none">
                </div>
                <div id="collectionDetails" style="display:none">
                </div>
               <div id="loader" style="display:none">
                <br/>
               <center><img src="ajax-loader.gif" /></center>
               </div>
		</td>
	</tr>
</table>
<?php }//End of main()?>