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
	$date = $_REQUEST['date'];
	if($_REQUEST['date']=="")  		$date = date('d-m-Y');
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
	  var url="showAttendanceActivity.php?val="+val;
	}
	if(val=='MTD' || val=='YTD')
	{
	  var url="showAttendanceActivityMonthly.php?val="+val;
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

function showEmployeeWiseAttendacedisplay(val,val1,val2)
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="selectEmployeeWiseAttendance.php?emp_code="+val+"&mode="+val1+"&page="+val2;
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>MIS REPORT</strong></td>
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
                <table width="98%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="8" align="center"><strong>MIS Report</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <td width="15%" align="left" style="padding-left:20px;">Available Field <br /><span style="padding-left:20px;">Force</span></td>
                        <td width="9%" align="left" style="padding:0px 20px 0px 20px;">Present</td>
                        <td width="15%" align="left" style="padding-left:20px;">Customer Visited</td>
                        <td width="21%" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Collection</span><br /> Received(Rs/-)</td>
                        <td width="" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No</span><br /> Transaction</td>
                        <?php if(stk_audit=='yes'){?>
                        <td width="11%" align="left" style="padding-left:20px;">Stock Audit<br /><span style="padding-left:10px;">(Qty)</span></td>
                        <?php }?>
                        <?php /*if(route_plan=='yes'){?>
                         <td width="" align="left" style="padding-left:20px;"></td>
                         <?php }*/?>
                    </tr> 
                    <?php
						$sqlfieldforce="SELECT COUNT(EM.emp_code) AS available_field_force FROM employee_master EM,changepassword CH 
										WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND SUBSTRING(EM.emp_code,1,1)!='C'";
						$resfieldforce=mysqli_query($link,$sqlfieldforce) or die(mysqli_error()." Error in select field force: ".$sqlfieldforce);
						$rowfieldforce=mysqli_fetch_assoc($resfieldforce);
						$av_field_force=$rowfieldforce['available_field_force'];
						
						for($i=1;$i<=3;$i++)
						{
							if($i==1)
							{
								$date=date('Y-m-d');
								if($date!='')
								{
									$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
								}
								$sl_value='Today';
								$val='T';
							}
							//For MTD OR Month Today
							if($i==2)
							{
								$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
								$sl_value='MTD';
								$val='MTD';
							}
							//For YTD OR Year Today
							if($i==3)
							{
								$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
								$sl_value='YTD';
								$val='YTD';
							}
							
                        
							$sqlpresent="SELECT COUNT(LO.trans_id) AS no_present  FROM location LO WHERE LO.trans_id LIKE 'A%' 
										AND SUBSTRING(LO.emp_code,1,1)!='C' ".$date_condition."";
							$respresent=mysqli_query($link,$sqlpresent) or die(mysqli_error()." Error in select present information: ".$sqlpresent);
							$rowpresent=mysqli_fetch_assoc($respresent);
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
							
							$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE SUBSTRING(LO.trans_id,1,1) NOT IN
										 		('A','M') AND SUBSTRING(LO.emp_code,1,1)!='C' ".$date_condition."";
							$rescustomervisit=mysqli_query($link,$sqlcustomervisit) or die(mysqli_error()." Error in select customer visit: ".$sqlcustomervisit);
							$rowcustomervisit=mysqli_fetch_assoc($rescustomervisit);
							$no_customer_visit=$rowcustomervisit['no_visit'];
							
							$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
												FROM payment_details PD,location LO
												WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
												AND SUBSTRING(LO.emp_code,1,1)!='C' ".$date_condition."";
							$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
							$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
							$collection_received=$rowtotalcollection['total_collection_received'];

							
							$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
												FROM location LO WHERE (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') 
												AND SUBSTRING(LO.emp_code,1,1)!='C' ".$date_condition."";
							$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
							$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
							$no_transaction=$rownotransaction['total_no_transaction'];
					?>
							<tr> 
                                    <td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
					onClick="javascript:showAttendaceActivitydisplay('<?=$val?>')"  style="color:#930;font-weight:bold;"><?=$sl_value?></a></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $av_field_force;?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $present;?></td>
									<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $no_customer_visit;?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format($collection_received,2);?></td>
                              		<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $no_transaction;?></td>
                                    <?php if(stk_audit=='yes'){
									$sqlnostkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
													WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') 
													AND SUBSTRING(LO.emp_code,1,1)!='C' ".$date_condition."";
									$rsnostkaudit=mysqli_query($link,$sqlnostkaudit) or die(mysqli_error()." Error in total no stk audit: ".$sqlnostkaudit);
									$rownostkaudit=mysqli_fetch_assoc($rsnostkaudit);
									$no_stk_audit=$rownostkaudit['total_stk_audit'];
									if($no_stk_audit=='')  $no_stk_audit=0;
									?>
                                   <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $no_stk_audit;?></td>
                                    <?php }/*if(route_plan=='yes'){
									?>
                                     <td valign="top" align="center" style="BORDER: #A92A61 1px solid;" ><a href="javascript:void(0)" 
					onClick="javascript:showRoutePlandisplay('<?=$val?>')"  style="color:#930;font-weight:bold;">ROUTE PLAN</a></td>
                    				<?php }*/?>
                              </tr>
                             <?php				
							}
						?>
            </table><br />
         <!------------------------------------------------End of Table for first time page loading----------------------------------------------!-->
  			<div id="AttendanceActivity" style="display:none;">
                </div><br />
  
  
  <!----------------------------------------Start of Table for populate employee date wise locate back-----------------------------------------------------!-->
 <?php
 	//For date wise Employee activity listing
	
	if($_REQUEST['page']=='misreport')
	{	
		$mode=$_REQUEST['mode'];
		if($mode=='T')
		{
			$date=date('Y-m-d');
			$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$headerval=date('d-m-Y',strtotime($date));
		} 
		if($mode=='MTD')
		{
			$date_condition =" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE())";
			$headerval=date('F').' ,'.date('Y');
		}
		if($mode=='YTD')
		{
			$date_condition =" AND YEAR(LO.date) = YEAR(CURDATE())";
			$headerval=date('Y');
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
										AND SUBSTRING(EM.emp_code,1,1)!='C' ".$date_condition." ORDER BY EM.emp_code ASC ";
						$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
						$count=mysqli_num_rows($resinformation);
						if($count==0)
						{
							$tablevalattendance.='<tr><td align="center" colspan="5">No records found.</td></tr>';
							
						 }
					else{
							$cnt=$GLOBALS[start]+1;
							while($rowinformation=mysqli_fetch_assoc($resinformation))
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
												&mode=$mode&page=misreport\" style=\"color:#930;font-weight:bold;\">Locate</a></td>
										  </tr>";
							}
						}
				$tablevalattendanceend.='</table><br />';
			}
			
			if($mode=='MTD' || $mode=='YTD')
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
									 AND SUBSTRING(EM.emp_code,1,1)!='C' ".$date_condition."
									 GROUP BY LO.emp_code  ORDER BY EM.emp_code ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select attendance information monthly: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{
						$tablevalattendance.='<tr><td align="center" colspan="5">No records found.</td></tr>';
						
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysqli_fetch_assoc($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$time=$rowinformation['time'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$sqlcountatt="SELECT count(emp_code) AS totalattendence FROM location LO WHERE LO.trans_id LIKE 'A%' 
										  AND LO.emp_code='".$emp_code."' ".$date_condition."";
							$rescountatt=mysqli_query($link,$sqlcountatt) or die(mysqli_error()." Error in select count attendence: ".$sqlcountatt);
							$rowcountatt=mysqli_fetch_assoc($rescountatt);
							$totalattendance=$rowcountatt['totalattendence'];

							$rowvalattendance.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"javascript:void(0)\" 
											onClick=\"javascript:showEmployeeWiseAttendacedisplay('".$emp_code."','".$mode."','misreport')\"  
											style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$totalattendance."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"adminMultiAttendanceLocate.php?emp_code=$emp_code&mode=$mode
												&page=misreport\" style=\"color:#930;font-weight:bold;\">Locate</a></td>
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
			if((strtoupper($_SESSION['nick_name'])=='VIPL' || strtoupper($_SESSION['nick_name'])=='AMW' || strtoupper($_SESSION['nick_name'])=='RUPA' 
			|| strtoupper($_SESSION['nick_name'])=='CDNS' || strtoupper($_SESSION['nick_name'])=='AMPL')  
			&& $mode=='T'){
				$DCR_TH='<td width="5%" align="left" style="padding-left:20px;">DCR</td>';
			}
$tablevalactivity='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="8" align="center"><strong>Activity ON '.$headerval.'</strong></td>
    </tr>
     <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="12%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="16%" align="left" style="padding-left:20px;">Name</td>
		<td width="15%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>
		<td width="20%" align="left" style="padding-left:20px;">No of Order Received<br /><span style="padding-left:24px;"></span></td>
		<td width="17%" align="left" style="padding-left:20px;">Total Collection<br /><span style="padding-left:20px;">(Rs/-)</span></td>
		<td width="" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No</span><br /> Transaction</td>'.$stk_audit_TH.$DCR_TH.'
    </tr> ';
        
		$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,1) NOT IN
						('A','M') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysqli_fetch_assoc($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			
			if((strtoupper($_SESSION['nick_name'])=='VIPL' || strtoupper($_SESSION['nick_name'])=='AMW' || strtoupper($_SESSION['nick_name'])=='RUPA' 
			|| strtoupper($_SESSION['nick_name'])=='CDNS' || strtoupper($_SESSION['nick_name'])=='AMPL') 
			&& $mode=='T'){
			$DCR_TD="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
					<a href=\"adminDCR.php?emp_code=$emp_code&mode=$mode&page=misreport\"style=\"color:#930;font-weight:bold;\">DCR</a></td>";
			}
			
			$sqltotalorder="SELECT COUNT(LO.trans_id)AS total_no_order
							FROM location LO WHERE  LO.emp_code='".$emp_code."' 
							AND (LO.trans_id LIKE 'O%') ".$date_condition."";
			$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
			$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
			
			$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
								FROM payment_details PD,location LO
								WHERE  LO.emp_code='".$emp_code."' 
								AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
			$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
			$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
			
			$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
								FROM location LO WHERE  LO.emp_code='".$emp_code."' 
								AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$date_condition."";
			$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
			$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
			if(stk_audit=='yes'){
				$sqlstkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
						WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') AND LO.emp_code='".$emp_code."'".$date_condition."";
				$rsstkaudit=mysqli_query($link,$sqlstkaudit) or die(mysqli_error()." Error in total stk audit: ".$sqlstkaudit);
				$rowstkaudit=mysqli_fetch_assoc($rsstkaudit);
				$stk_audit=$rowstkaudit['total_stk_audit'];
				if($stk_audit=='')  $stk_audit=0;
				$stk_audit_TD="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$stk_audit."</td>
";
			}
            
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','".$mode."','misreport')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['no_of_visit']."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowtotalorder['total_no_order']."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($rowtotalcollection['total_collection_received'],2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
						".$rownotransaction['total_no_transaction']."</td>".$stk_audit_TD.$DCR_TD."
              </tr>";
        }
$tablevalend='</table></div>';				
$finalval=$tablevalattendance.$rowvalattendance.$tablevalattendanceend.$tablevalactivity.$rowval.$tablevalend;

echo $finalval.'<br />';
	}
		
	//For Listing the customer of Employee Activity
	
	if($_REQUEST['page']=='misreport' && ($_REQUEST['mode']=='T' || $_REQUEST['mode']=='MTD' || $_REQUEST['mode']=='YTD') && $_REQUEST['customer_code']!='')
	{
		$emp_code=$_REQUEST['emp_code'];

		$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in select employee name and code : ".$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);
		$emp_name=$rowemp['emp_name'];
		
		$mode=$_REQUEST['mode'];
		if($mode=='T')
		{
			$date=date('Y-m-d');
			$date_condition=" AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
		}
		if($mode=='MTD')
		{
			$date_condition =" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE())";
		}
		if($mode=='YTD')
		{
			$date_condition =" AND YEAR(LO.date) = YEAR(CURDATE())";
		}
		$page=$_REQUEST['page'];
		$date_array=array();
		if(stk_audit=='yes'){
			$stk_audit_customer_TR='<td width="11%" align="left" style="padding-left:20px;">Stock Audit</td>';
			$stk_audit_customer_TD='<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>';
		}
		else
		{
			$stk_audit_customer_TR='';
			$stk_audit_customer_TD='';
		}
		$tablevallist='
		<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;" id="employeewisecutomerlist">
			<tr class="TDHEAD" > 
				<td colspan="7" align="center"><strong>Employee Name: '.$emp_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Employee Code: '. $emp_code.'</strong></td>
			</tr>
			 <tr class="TDHEAD_SUB"> 
				 <td width="5%" align="center">Sl</td>
				<td width="15%" align="left" style="padding-left:20px;">Customer code</td>
				<td width="20%" align="left" style="padding-left:20px;">Name</td>
				<td width="18%" align="left" style="padding-left:20px;">Order Received Quantity<br /><span style="padding-left:20px;"></span></td>
				<td width="18%" align="left" style="padding-left:20px;">Collection Received<br /><span style="padding-left:20px;">(Rs/-)</span></td>'.
				$stk_audit_customer_TR.'
				<td width="" align="left" style="padding-left:20px;">Locate</td>
			  </tr>';
				 $sqltrans="SELECT LO.*,DATE_FORMAT(LO.date,'%d-%m-%Y') AS date FROM location LO 
				 	WHERE LO.trans_id NOT LIKE 'A%' AND LO.emp_code='".$emp_code."'".$date_condition." ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d %h:%i:%s') DESC ";
				 $restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id: ".$sqltrans);
				 $cnt=$GLOBALS[start]+1;
				 $rowvallist=""; 
				while($rowtrans=mysqli_fetch_assoc($restrans))
				{
				  $trans_id=$rowtrans['trans_id'];
				  $operation_type=substr($trans_id,0,1);
				  if($mode!='T' && !in_array($rowtrans['date'],$date_array))
					{
						array_push($date_array,$rowtrans['date']);
						if(strtoupper($_SESSION['nick_name'])=='VIPL' || strtoupper($_SESSION['nick_name'])=='AMW' 
						|| strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='CDNS' || 
						strtoupper($_SESSION['nick_name'])=='AMPL')
						{
							$rowval_DCR="-----"."<a href=\"adminDCR.php?emp_code=$emp_code&mode=$mode&page=misreport&requiredate=$rowtrans[date]\"style=\"color:#930;font-weight:bold;\">DCR</a>"."";
						}
						else
						{
							$rowval_DCR='';
						}
						$rowvallist.="<tr> <td valign=\"top\" align=\"center\" colspan=\"6\"><strong>".$rowtrans['date'].$rowval_DCR."</strong></td></tr>";
					}
				  if($operation_type=='O')
					{
						$order_no=$trans_id;
						$sqlorderheader="SELECT customer_code FROM order_header WHERE order_no='".$order_no."'";
						$rsorderheader=mysqli_query($link,$sqlorderheader) or die(mysqli_error()." Error in select order: ".$sqlorderheader);
						$roworderheader=mysqli_fetch_assoc($rsorderheader);
						$customer_code=$roworderheader['customer_code'];
				
						/*if(substr($customer_code,0,1)=='N')
						{
							$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
										  order_header OH,prospective_customer_master CM,order_details OD
										  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
										   GROUP BY OD.order_no";
						}
						else
						{*/
							if(sale=='no'){
								$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
											  order_header OH,customer_master CM,order_details OD
											  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
											   GROUP BY OD.order_no";
							}
							else
							{
								$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
											  order_header OH,customer_master CM,order_details OD
											  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
											   GROUP BY OD.order_no UNION SELECT VM.vendor_name 
											   AS customer_name,VM.vendor_code AS customer_code,SUM(OD.qty) AS total_order_received FROM 
											  order_header OH,vendor_master VM,order_details OD
											  WHERE VM.vendor_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
											   GROUP BY OD.order_no";
							}
						//}
					$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
					$rowcustomer=mysqli_fetch_assoc($rscustomer);
					//$customer_code=$rowcustomer['customer_code'];
					$customer_name=$rowcustomer['customer_name'];

					
						$rowvallist.="<tr> 
							<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"javascript:void(0)\" onClick=\"javascript:populateOrderDetails('".$trans_id."','".$customer_code."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowcustomer['total_order_received']."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
							".$stk_audit_customer_TD."
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>
				  		</tr>";
					}
				if($operation_type=='P')
				{
					$receipt_id=$trans_id;
					$sqlpaymentheader="SELECT customer_code,sale_type FROM payment_header WHERE receipt_id='".$receipt_id."'";
					$rspaymentheader=mysqli_query($link,$sqlpaymentheader) or die(mysqli_error()." Error in select payment header: ".$sqlpaymentheader);
					$rowpaymentheader=mysqli_fetch_assoc($rspaymentheader);
					$customer_code=$rowpaymentheader['customer_code'];
					$sale_type=$rowpaymentheader['sale_type'];
					
					/*if(substr($customer_code,0,1)=='N')
					{
						$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
									FROM payment_header PH,prospective_customer_master CM,payment_details PD
									WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
									AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
					}
					else
					{*/
						if(sale=='no'){
							$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
												FROM payment_header PH,customer_master CM,payment_details PD
												WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
												AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
						}
						else
						{
							$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
										FROM payment_header PH,customer_master CM,payment_details PD
										WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
										AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id UNION 
										SELECT VM.vendor_name AS customer_name,VM.vendor_code AS customer_code,SUM(PD.amount) AS total_collection_received 
										FROM payment_header PH,customer_master CM,payment_details PD
										WHERE VM.vendor_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
										AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
										";
						}
					//}
					$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
					$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
					$customer_code=$rowcustomerpayment['customer_code'];
					$customer_name=$rowcustomerpayment['customer_name'];
			
					$rowvallist.="<tr> 
							<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" onClick=\"javascript:populateCollectionDetails('".$trans_id."','".$customer_code."','".$sale_type."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($rowcustomerpayment['total_collection_received'],2)."</td>
							".$stk_audit_customer_TD."
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>
						</tr>";				
				} 
				if($operation_type=='S')
				{
						$stk_counting_trans_id=$trans_id;
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code  FROM 
									  stock_audit SA,customer_master CM
									  WHERE CM.customer_code=SA.customer_code AND SA.transaction_id='".$stk_counting_trans_id."' 
									  GROUP BY SA.transaction_id";
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						$customer_code=$rowcustomer['customer_code'];
						$customer_name=$rowcustomer['customer_name'];
						
						$sqlstkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM stock_audit SA 
									WHERE SA.transaction_id='".$stk_counting_trans_id."' GROUP BY SA.transaction_id";
						$rsstkaudit=mysqli_query($link,$sqlstkaudit) or die(mysqli_error()." Error in total stk audit: ".$sqlstkaudit);
						$rowstkaudit=mysqli_fetch_assoc($rsstkaudit);
						$stk_audit=$rowstkaudit['total_stk_audit'];
				
					$rowvallist.="<tr> 
								<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
								<a href=\"javascript:void(0)\" onClick=\"javascript:populateAuditDetails('".$trans_id."','".$customer_code."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
								<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
								<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
								<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$stk_audit."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
								<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
								emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>
							</tr>";
				}
				if($operation_type=='N')
				{
					$operation_type_no=substr($trans_id,1,1);
					if($operation_type_no=='O')
						{
							$order_no=$trans_id;
							$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code FROM order_header OH,customer_master CM 
												WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
							$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
							$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
					
							$customer_code=$rowcustomernoorder['customer_code'];
							$customer_name=$rowcustomernoorder['customer_name'];
							$rowvallist.="<tr> 
										<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_code."</td>
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
										<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
										".$stk_audit_customer_TD."
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
										<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
										emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>
									</tr>";
						}
						if($operation_type_no=='C')
						{
							$receipt_id=$trans_id;
							$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code FROM payment_header PH,customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
							$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
					
							$customer_code=$rowcustomernocollection['customer_code'];
							$customer_name=$rowcustomernocollection['customer_name'];
							$rowvallist.="<tr> 
										<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_code."</td>
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
										<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
										".$stk_audit_customer_TD."
										<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
										<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
										emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>
									</tr>";
						}
				 }           
			  }
		$tablevalendlist='</table>';				
		$finalvallist=$tablevallist.$rowvallist.$tablevalendlist;

		echo $finalvallist;
	}
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