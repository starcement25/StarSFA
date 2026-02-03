<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name']) == 'STAR' && strtoupper($_SESSION['admin_login']) == 'E0674')
	{
		require("adminUtils_accounts.php");
	}
	else
	{
		require("adminUtils.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	

	disphtml("main();");
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
			document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('datedropdown').style.display='none';
			if(document.getElementById('datewiseemployeelist'))
			{
				document.getElementById('datewiseemployeelist').style.display='none';
			}
			if(document.getElementById('customertabledisplaydatewise').style.display=='')
			{
				document.getElementById('customertabledisplaydatewise').style.display='none';
			}
			if(document.getElementById('orderDetails'))
			{
				document.getElementById('orderDetails').style.display='none';
			}
			if(document.getElementById('collectionDetails'))
			{
				document.getElementById('collectionDetails').style.display='none';
			}
			if(document.getElementById('employeewisecutomerlist').style.display='')
			{			
				document.getElementById('employeewisecutomerlist').style.display='none';
			}			
			
		}
		if(val=='today')
		{
			document.getElementById('todayAttDisplay').style.display='';	
			document.getElementById('yesterdayAttDisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='none';
			document.getElementById('datedropdown').style.display='none';
			if(document.getElementById('datewiseemployeelist'))
			{
				document.getElementById('datewiseemployeelist').style.display='none';
			}
			if(document.getElementById('customertabledisplaydatewise').style.display=='')
			{
				document.getElementById('customertabledisplaydatewise').style.display='none';
			}
			if(document.getElementById('orderDetails'))
			{
				document.getElementById('orderDetails').style.display='none';
			}
			if(document.getElementById('collectionDetails'))
			{
				document.getElementById('collectionDetails').style.display='none';
			}
			if(document.getElementById('employeewisecutomerlist').style.display='')
			{			
				document.getElementById('employeewisecutomerlist').style.display='none';
			}
					
		}
		if(val=='choice')
		{
			document.getElementById('datedropdown').style.display='';
			if(document.getElementById('datewiseemployeelist'))
			{
				document.getElementById('datewiseemployeelist').style.display='none';
			}
			if(document.getElementById('customertabledisplaydatewise').style.display=='')
			{
				document.getElementById('customertabledisplaydatewise').style.display='none';
			}				
			if(document.getElementById('orderDetails'))
			{
				document.getElementById('orderDetails').style.display='none';
			}
			if(document.getElementById('collectionDetails'))
			{
				document.getElementById('collectionDetails').style.display='none';
			}			
			if(document.getElementById('employeewisecutomerlist').style.display='')
			{			
				document.getElementById('employeewisecutomerlist').style.display='none';
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
function showEmployeeWisedisplay(val,val1,val2)
{
	//alert(val2);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	document.getElementById('orderDetails').style.display='none';
	document.getElementById('customertabledisplaydatewise').style.display='none';
	if(val1=='YC')
	{
		var fromDate=document.getElementById('from_date').value;
		var toDate=document.getElementById('to_date').value;

		var url="selectEmployeeWiseActivity.php?emp_code="+val+"&mode="+val1+"&from_date="+fromDate+"&to_date="+toDate+"&page="+val2;
	}
	else{
		var url="selectEmployeeWiseActivity.php?emp_code="+val+"&mode="+val1+"&page="+val2;
	}
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
			document.getElementById('employeewisecutomerlist').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
function showDateWisedisplay(val)
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
	
	var url="selectEmployeeDateWiseActivity.php?fromDate="+fromDate+"&toDate="+toDate;
	xmlHttpNext.onreadystatechange=showActivityEmployeeDateWise;
	xmlHttpNext.open("GET",url,true);
	xmlHttpNext.send(null);
  }

function showActivityEmployeeDateWise()
 {
    if(xmlHttpNext.readyState==4 || xmlHttpNext.readyState=="complete")
	 {
		var val=xmlHttpNext.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('yesterdayAttDisplay').style.display='none';
			document.getElementById('todayAttDisplay').style.display='none';
			document.getElementById('employeetabledisplaydatewise').style.display='';
			document.getElementById('employeetabledisplaydatewise').innerHTML=val;
		 	document.getElementById('customertabledisplaydatewise').style.display='none';
			document.getElementById('loader').style.display='none';
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
			document.getElementById('collectionDetails').style.display='none';
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
 function populateCollectionDetails(val,val1)
	{
	//alert(val);
	xmlHttpCollection=GetXmlHttpObject()
	if (xmlHttpCollection==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	
	var url="populateCollectionDetails.php?trans_id="+val+"&customer_code="+val1;
	
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


</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Activity</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
			
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
              
               <?php
			   if(sauda_allocation=='yes')
				{
					$order_sauda_text='Sauda';
					$order_sauda_text_one='Booked';
				}
				else
				{
					$order_sauda_text='Order';
					$order_sauda_text_one='Received';
				}
			   ?>
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
                <table width="95%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: <?php if($_REQUEST['radio_type']=='today' || $_REQUEST['mode']=='' || $_REQUEST['mode']=='T'){?> ''<?php }else{?>none<?php }?>;height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="8" align="center"><strong>Date: <?php echo date('d-m-Y');?></strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="10%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="25%" align="left" style="padding-left:20px;">Name</td>
                        <td width="12%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>
                        <td width="12%" align="left" style="padding-left:20px;">No of <?php echo $order_sauda_text.' '.$order_sauda_text_one?><br /><span style="padding-left:24px;"></span></td>
                        <td width="12%" align="left" style="padding-left:20px;">Order Amount</td>
                        <?php if(collection == 'yes'){ ?>
                        <td width="12%" align="left" style="padding-left:20px;">Total Collection<br /><span style="padding-left:20px;">(Rs/-)</span></td>
                        <?php } ?>
                        <td width="" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No</span><br /> Transaction</td>
                         <?php if(stk_audit == 'yes' || retailer_care=='yes'){ ?>
                        <td width="12%" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No of</span><br /> Stock Audit</td>
                        <?php } ?>
                    </tr> 
                    <?php
						$date=date('d-m-Y');
                        if($date!='')
                        {
                            if(sale=='no' && instruction=='yes')
							{
								$date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
							}
							else
							{
								$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') LIKE '%".$date."%'";
							}
                        }
                        else
                        {
                            $date_condition='';
                        }
                        
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
									location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND (SUBSTRING(trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) AND SUBSTRING(trans_id,1,2) NOT IN('PA')  
						AND SUBSTRING(trans_id,1,2) NOT IN('SU') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{ 
					?>
						<tr> 
							<td align="center" colspan="5">No records found.</td>
						</tr>
					<?php 
					}else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysqli_fetch_assoc($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$operation_type=substr($trans_id,0,1);
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$customer_code_array=array();
							
							$sql_order_header_customer = "SELECT SUBSTRING(order_no,-19,5) AS emp_code, customer_code, SUBSTRING(order_no,-14,8) AS oh_date 
												FROM order_header WHERE ( SUBSTRING(order_no,2,5)='".$emp_code."' OR  SUBSTRING(order_no,3,5)='".$emp_code."')
												AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') LIKE '%".$date."%' AND customer_code IN(SELECT customer_code FROM customer_master)
												GROUP BY SUBSTRING(order_no,-19,5), customer_code, SUBSTRING(order_no,-14,8) 
												ORDER BY SUBSTRING(order_no,-14,8) DESC,SUBSTRING(customer_code,-14,8) DESC";
							$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
							//$yttdcount=1;
							while($row_order_header_customer = mysqli_fetch_assoc($res_order_header_customer)){
								$emp_code = $row_order_header_customer['emp_code'];
								$order_header_customer_code = $row_order_header_customer['customer_code'];
								$location_date = $row_order_header_customer['oh_date'];
								$customer_concat_date = $emp_code."^".$order_header_customer_code."^".$location_date;
				
								$customer_code_array[$customer_concat_date] = '1';
							}
							$sql_payment_header_customer = "SELECT SUBSTRING(receipt_id,-19,5) AS emp_code, customer_code, SUBSTRING(receipt_id,-14,8) 
															AS ph_date FROM payment_header WHERE 
															( SUBSTRING(receipt_id,2,5)='".$emp_code."' OR  SUBSTRING(receipt_id,3,5)='".$emp_code."')
															AND DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%d-%m-%Y') LIKE '%".$date."%' AND customer_code IN(SELECT customer_code FROM customer_master)
															GROUP BY SUBSTRING(receipt_id,-19,5), customer_code, SUBSTRING(receipt_id,-14,8)";
							$res_payment_header_customer = mysqli_query($link,$sql_payment_header_customer);
							while($row_payment_header_customer = mysqli_fetch_assoc($res_payment_header_customer)){
								$emp_code = $row_payment_header_customer['emp_code'];
								$payment_header_customer_code = $row_payment_header_customer['customer_code'];
								$location_date = $row_payment_header_customer['ph_date'];
								$customer_concat_date = $emp_code."^".$payment_header_customer_code."^".$location_date;
								$customer_code_array[$customer_concat_date] = '1';
							}
							if(check_in_out=='yes')
							{
								$sql_checkin_out_customer = "SELECT SUBSTRING(trans_id,-19,5) AS emp_code, customer_code, SUBSTRING(trans_id,-14,8) 
																AS ci_date FROM check_in_out_details WHERE SUBSTRING(trans_id,-19,5)='".$emp_code."'
																AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') LIKE '%".$date."%' 
																AND customer_code IN(SELECT customer_code FROM customer_master)
															GROUP BY SUBSTRING(trans_id,-19,5), customer_code, SUBSTRING(trans_id,-14,8)";
								$res_checkin_out_customer = mysqli_query($link,$sql_checkin_out_customer);
								while($row_checkin_out_customer = mysqli_fetch_assoc($res_checkin_out_customer)){
									$emp_code = $row_checkin_out_customer['emp_code'];
									$check_in_out_customer_code = $row_checkin_out_customer['customer_code'];
									$location_date = $row_checkin_out_customer['ci_date'];
									$customer_concat_date = $emp_code."^".$check_in_out_customer_code."^".$location_date;
									$customer_code_array[$customer_concat_date] = '1';
								}
							}
							if(sale_rate=='yes')
							{
							$sqltotalorder="SELECT ROUND(SUM(OD.qty),2) AS total_order_received,SUM(OD.qty*OD.sale_rate) AS total_amount FROM order_details OD, location LO,order_header OH,customer_master CM WHERE OD.order_no = LO.trans_id AND LO.trans_id LIKE 'O%' AND LO.emp_code = '".$emp_code."' 
							AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition;
							}
							if(mrp=='yes')
							{
							$sqltotalorder="SELECT ROUND(SUM(OD.qty),2) AS total_order_received,SUM(OD.qty*MRP.mrp) AS total_amount FROM order_details OD, location LO,order_header OH,customer_master CM,mrp MRP WHERE OD.order_no = LO.trans_id AND LO.trans_id LIKE 'O%' 
							AND LO.emp_code = '".$emp_code."' AND OD.sku_code=MRP.product_code AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition;
							}
							$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
							$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
							$total_amount_received=$rowtotalorder['total_amount'];
							
							$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
												FROM payment_details PD,location LO
												WHERE  LO.emp_code='".$emp_code."' 
												AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
							$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
							$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
							
							$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
												FROM location LO,order_header OH,customer_master CM WHERE  LO.emp_code='".$emp_code."' 
												AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') AND OH.order_no=LO.trans_id AND OH.customer_code=CM.customer_code AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
							$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
							$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
							$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit
												FROM location LO WHERE  LO.emp_code='".$emp_code."' 
												AND (LO.trans_id LIKE 'SE%')  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
							$rsstockaudit=mysqli_query($link,$sqlstockaudit) or die(mysqli_error()." Error in total no transaction: ".$sqlstockaudit);
							$rowstockaudit=mysqli_fetch_assoc($rsstockaudit);
					?>
							<tr> 
                                    <td valign="top" align="center" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?=$cnt++ ?></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
					onClick="javascript:showEmployeeWisedisplay('<?=$emp_code?>','T','activity')"  style="color:#930;font-weight:bold;"><?php echo $emp_code;?></a></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $emp_name;?></td>
									<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php /*echo $rowinformation['no_of_visit'];*/echo count($customer_code_array);?></td>
                                   	<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowtotalorder['total_order_received'];?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format(round($total_amount_received,2),2);?></td>
                                    <?php if(collection == 'yes'){ ?>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format($rowtotalcollection['total_collection_received'],2);?></td>
                                    <?php } ?>
                              		<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rownotransaction['total_no_transaction'];?></td>
                                     <?php if(stk_audit == 'yes' || retailer_care=='yes'){ ?>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowstockaudit['total_stk_audit'];?></td>
                                    <?php }?>
                              </tr>
                             <?php				
						}
					}
				?>
            </table><br />
         <!------------------------------------------------End of Table for first time page loading----------------------------------------------!-->
         <!-----------------------------------------------Start of Table for Yesterday Employee Activity Check------------------------------------>
         
            <table width="80%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: <?php if($_REQUEST['radio_type']=='yesterday' || $_REQUEST['mode']=='Y'){?> ''<?php }else{?>none<?php }?>;height: 150px;overflow-y: scroll;" id="yesterdayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="8" align="center"><strong>Date: <?php $curdate=date('d-m-Y');
						echo $yesterdaydate=date('d-m-Y', strtotime("-1 days,$curdate "));?></strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                         <td width="10%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="25%" align="left" style="padding-left:20px;">Name</td>
                        <td width="12%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>
                        <td width="12%" align="left" style="padding-left:20px;">No of <?php echo $order_sauda_text.' '.$order_sauda_text_one?><br /><span style="padding-left:24px;"></span></td>
                        <td width="12%" align="left" style="padding-left:20px;">Order Amount</td>
                        <?php if(collection == 'yes'){ ?>
                        <td width="12%" align="left" style="padding-left:20px;">Total Collection<br /><span style="padding-left:20px;">(Rs/-)</span></td>
                        <?php } ?>
                        <td width="" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No</span><br /> Transaction</td>
                         <?php if(stk_audit == 'yes' || retailer_care=='yes'){ ?>
                        <td width="12%" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No of</span><br /> Stock Audit</td>
                        <?php } ?>
                    </tr> 
                    <?php
						if($yesterdaydate!='')
						{
						  if(sale=='no' && instruction=='yes')
							{
								$date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$yesterdaydate."%'";
							}
							else
							{
								$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') LIKE '%".$yesterdaydate."%'";
							}
						}
						else
                        {
                            $date_condition='';
                        }
                        
					$sqlinfoyesterday="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
									location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,1) NOT IN
									('A','M','C') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
					$resinfoyesterday=mysqli_query($link,$sqlinfoyesterday) or die(mysqli_error()." Error in select transaction information yesterday: ".$sqlinfoyesterday);
					$countyesterday=mysqli_num_rows($resinfoyesterday);
					if($countyesterday==0)
					{ 
					?>
						<tr> 
							<td align="center" colspan="5">No records found.</td>
						</tr>
					<?php }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinfoyesterday=mysqli_fetch_assoc($resinfoyesterday))
						{
							$trans_id=$rowinfoyesterday['trans_id'];
							$operation_type=substr($trans_id,0,1);
							$emp_name=$rowinfoyesterday['emp_name'];
							$emp_code=$rowinfoyesterday['emp_code'];
							$customer_code_array=array();
		
							$sql_order_header_customer = "SELECT SUBSTRING(order_no,-19,5) AS emp_code, customer_code, SUBSTRING(order_no,-14,8) AS oh_date 
							FROM order_header WHERE ( SUBSTRING(order_no,2,5)='".$emp_code."' OR  SUBSTRING(order_no,3,5)='".$emp_code."')
							AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') LIKE '%".$yesterdaydate."%' AND customer_code IN(SELECT customer_code FROM customer_master)
							GROUP BY SUBSTRING(order_no,-19,5), customer_code, SUBSTRING(order_no,-14,8) 
							ORDER BY SUBSTRING(order_no,-14,8) DESC,SUBSTRING(customer_code,-14,8) DESC";
							$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
							//$yttdcount=1;
							while($row_order_header_customer = mysqli_fetch_assoc($res_order_header_customer)){
								$emp_code = $row_order_header_customer['emp_code'];
								$order_header_customer_code = $row_order_header_customer['customer_code'];
								$location_date = $row_order_header_customer['oh_date'];
								$customer_concat_date = $emp_code."^".$order_header_customer_code."^".$location_date;
				
								$customer_code_array[$customer_concat_date] = '1';
							}
							$sql_payment_header_customer = "SELECT SUBSTRING(receipt_id,-19,5) AS emp_code, customer_code, SUBSTRING(receipt_id,-14,8) 
															AS ph_date FROM payment_header WHERE 
															( SUBSTRING(receipt_id,2,5)='".$emp_code."' OR  SUBSTRING(receipt_id,3,5)='".$emp_code."')
															AND DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%d-%m-%Y') LIKE '%".$yesterdaydate."%' AND customer_code IN(SELECT customer_code FROM customer_master)
															GROUP BY SUBSTRING(receipt_id,-19,5), customer_code, SUBSTRING(receipt_id,-14,8)";
							$res_payment_header_customer = mysqli_query($link,$sql_payment_header_customer);
							while($row_payment_header_customer = mysqli_fetch_assoc($res_payment_header_customer)){
								$emp_code = $row_payment_header_customer['emp_code'];
								$payment_header_customer_code = $row_payment_header_customer['customer_code'];
								$location_date = $row_payment_header_customer['ph_date'];
								$customer_concat_date = $emp_code."^".$payment_header_customer_code."^".$location_date;
								$customer_code_array[$customer_concat_date] = '1';
							}
							if(check_in_out=='yes')
							{
								$sql_checkin_out_customer = "SELECT SUBSTRING(trans_id,-19,5) AS emp_code, customer_code, SUBSTRING(trans_id,-14,8) 
																AS ci_date FROM check_in_out_details WHERE SUBSTRING(trans_id,-19,5)='".$emp_code."'
																AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') LIKE '%".$yesterdaydate."%' 
																AND customer_code IN(SELECT customer_code FROM customer_master)
															GROUP BY SUBSTRING(trans_id,-19,5), customer_code, SUBSTRING(trans_id,-14,8)";
								$res_checkin_out_customer = mysqli_query($link,$sql_checkin_out_customer);
								while($row_checkin_out_customer = mysqli_fetch_assoc($res_checkin_out_customer)){
									$emp_code = $row_checkin_out_customer['emp_code'];
									$check_in_out_customer_code = $row_checkin_out_customer['customer_code'];
									$location_date = $row_checkin_out_customer['ci_date'];
									$customer_concat_date = $emp_code."^".$check_in_out_customer_code."^".$location_date;
									$customer_code_array[$customer_concat_date] = '1';
								}
							}
							if(sale_rate=='yes')
							{
							$sqltotalorderyesterday="SELECT ROUND(SUM(OD.amount),2) AS total_order_received,SUM(OD.qty*OD.sale_rate) AS total_amount FROM order_details OD, location LO,order_header OH,customer_master CM WHERE OD.order_no = LO.trans_id AND LO.trans_id LIKE 'O%' AND LO.emp_code = '".$emp_code."' 
							AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition;
							}
							if(mrp=='yes')
							{
							$sqltotalorderyesterday="SELECT ROUND(SUM(OD.qty),2) AS total_order_received,SUM(OD.qty*MRP.mrp) AS total_amount FROM order_details OD, location LO,order_header OH,customer_master CM,mrp MRP WHERE OD.order_no = LO.trans_id AND LO.trans_id LIKE 'O%' 
							AND LO.emp_code = '".$emp_code."' AND OD.sku_code=MRP.product_code AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition;
							}
							$rstotalorderyesterday=mysqli_query($link,$sqltotalorderyesterday) or die(mysqli_error()." Error in total order received yesterday: ".$sqltotalorderyesterday);
							$rowtotalorderyesterday=mysqli_fetch_assoc($rstotalorderyesterday);
							$total_amount_received=$rowtotalorderyesterday['total_amount'];
							
							$sqltotalcollectionyesterday="SELECT ROUND(SUM(PD.amount),2) AS total_collection_received
														FROM payment_details PD,location LO
														WHERE  LO.emp_code='".$emp_code."' 
														AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
							$rstotalcollectionyesterday=mysqli_query($link,$sqltotalcollectionyesterday) or die(mysqli_error()." Error in total collection received yesterday: ".$sqltotalcollectionyesterday);
							$rowtotalcollectionyesterday=mysqli_fetch_assoc($rstotalcollectionyesterday);
							
							$sqlnotransactionyesterday="SELECT COUNT(LO.trans_id)AS total_no_transaction
												FROM location LO,order_header OH,customer_master CM WHERE  LO.emp_code='".$emp_code."' 
												AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') AND OH.order_no=LO.trans_id AND OH.customer_code=CM.customer_code ".$date_condition."";
							$rsnotransactionyesterday=mysqli_query($link,$sqlnotransactionyesterday) or die(mysqli_error()." Error in total no transaction yesterday: ".$sqlnotransactionyesterday);
							$rownotransactionyesterday=mysqli_fetch_assoc($rsnotransactionyesterday);
							$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit
												FROM location LO WHERE  LO.emp_code='".$emp_code."' 
												AND (LO.trans_id LIKE 'SE%')  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
							$rsstockaudit=mysqli_query($link,$sqlstockaudit) or die(mysqli_error()." Error in total no transaction: ".$sqlstockaudit);
							$rowstockaudit=mysqli_fetch_assoc($rsnotransaction);
					?>
							<tr> 
                                    <td valign="top" align="center" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?=$cnt++ ?></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
					onClick="javascript:showEmployeeWisedisplay('<?=$emp_code?>','Y','activity')"  style="color:#930;font-weight:bold;"><?php echo $emp_code;?></a></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $emp_name;?></td>
									<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php  /*echo $rowinformation['no_of_visit'];*/echo count($customer_code_array);?></td>
                                   	<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format(round($total_amount_received,2),2);?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowtotalcollectionyesterday['total_collection_received'];?></td>
                              		<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rownotransactionyesterday['total_no_transaction'];?></td>
                                <?php if(stk_audit == 'yes' || retailer_care=='yes'){ ?>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">
										<?php echo $rowstockaudit['total_stk_audit'];?></td>
                                 <?php } ?>       
                              </tr>
                             <?php				
						}
					}
				?>
            </table><br />
  <!------------------------------------------------End of Table for Yesterday Employee Activity Check-----------------------------------------------------!-->
  <!----------------------------------------Start of Table for populate employee date wise locate back-----------------------------------------------------!-->
 <?php
 	//For date wise Employee activity listing
	
	if($_REQUEST['mode']=='YC')
	{
		
		$from_date_pre=$_REQUEST['from_date'];
		$from_date=date('Y-m-d',strtotime($from_date_pre));
		$to_date_pre=$_REQUEST['to_date'];	
		$to_date=date('Y-m-d',strtotime($to_date_pre));
		
		if(stk_audit=='yes' || retailer_care=='yes'){
			$stk_audit_customer_TR='<td width="12%" align="left" style="padding-left:20px;">No of Stock Audit</td>';
		}
		else
		{
			$stk_audit_customer_TR='';
			$stk_audit_customer_TD='';
		}

		
$tableval='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;" id="datewiseemployeelist">
    <tr class="TDHEAD" > 
        <td colspan="7" align="center"><strong>From: '.$from_date_pre.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: '. $to_date_pre.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="15%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="20%" align="left" style="padding-left:20px;">Name</td>
		<td width="15%" align="left" style="padding-left:20px;">No. of Customer Visit</td>
		<td width="18%" align="left" style="padding-left:20px;">NO of '.$order_sauda_text.' '.$order_sauda_text_one.'</td>
		<td width="18%" align="left" style="padding-left:20px;">Total Collection(Rs/-)</td>
		<td width="" align="left" style="padding-left:20px;">No Transaction</td>'.$stk_audit_customer_TR.'
    </tr> ';
        
         if(sale=='no' && instruction=='yes')
		 {
			$date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
		 }
		 else
		 {
			$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
		 }               
		     
		$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,1) NOT IN
						('A','M','C') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysqli_fetch_assoc($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			$customer_code_array=array();
			$sql_order_header_customer = "SELECT SUBSTRING(order_no,-19,5) AS emp_code, customer_code, SUBSTRING(order_no,-14,8) AS oh_date 
		FROM order_header WHERE ( SUBSTRING(order_no,2,5)='".$emp_code."' OR  SUBSTRING(order_no,3,5)='".$emp_code."')
		AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' AND customer_code IN(SELECT customer_code FROM customer_master)
		GROUP BY SUBSTRING(order_no,-19,5), customer_code, SUBSTRING(order_no,-14,8) 
		ORDER BY SUBSTRING(order_no,-14,8) DESC,SUBSTRING(customer_code,-14,8) DESC";
		$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
		//$yttdcount=1;
		while($row_order_header_customer = mysqli_fetch_assoc($res_order_header_customer)){
			$emp_code = $row_order_header_customer['emp_code'];
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			$location_date = $row_order_header_customer['oh_date'];
			$customer_concat_date = $emp_code."^".$order_header_customer_code."^".$location_date;

			$customer_code_array[$customer_concat_date] = '1';
		}
		$sql_payment_header_customer = "SELECT SUBSTRING(receipt_id,-19,5) AS emp_code, customer_code, SUBSTRING(receipt_id,-14,8) 
										AS ph_date FROM payment_header WHERE 
										( SUBSTRING(receipt_id,2,5)='".$emp_code."' OR  SUBSTRING(receipt_id,3,5)='".$emp_code."')
										AND DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' 
										GROUP BY SUBSTRING(receipt_id,-19,5), customer_code, SUBSTRING(receipt_id,-14,8)";
		$res_payment_header_customer = mysqli_query($link,$sql_payment_header_customer);
		while($row_payment_header_customer = mysqli_fetch_assoc($res_payment_header_customer)){
			$emp_code = $row_payment_header_customer['emp_code'];
			$payment_header_customer_code = $row_payment_header_customer['customer_code'];
			$location_date = $row_payment_header_customer['ph_date'];
			$customer_concat_date = $emp_code."^".$payment_header_customer_code."^".$location_date;
			$customer_code_array[$customer_concat_date] = '1';
		}

			if(sale_rate=='yes')
			{
				$sqltotalorderyesterday="SELECT ROUND(SUM(OD.amount),2) AS total_order_received,SUM(OD.qty*OD.sale_rate) AS total_amount FROM 
									order_details OD, location LO,order_header OH,customer_master CM WHERE OD.order_no = LO.trans_id AND 
									LO.trans_id LIKE 'O%' AND LO.emp_code = '".$emp_code."' 
									AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition;
			}
			if(mrp=='yes')
			{
			$sqltotalorderyesterday="SELECT ROUND(SUM(OD.amount),2) AS total_order_received,SUM(OD.qty*MRP.mrp) AS total_amount FROM order_details OD, 
									location LO,order_header OH,customer_master CM,mrp MRP WHERE OD.order_no = LO.trans_id AND LO.trans_id LIKE 'O%' 
			AND LO.emp_code = '".$emp_code."' AND OD.sku_code=MRP.product_code AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition;
			}
			$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received,SUM(OD.sale_rate) AS total_rate
							FROM order_details OD,location LO,order_header OH,customer_master CM
							WHERE LO.emp_code='".$emp_code."' 
							AND LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition."";
			$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
			$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
			$total_amount_received=$rowtotalorder['total_rate']*$rowtotalorder['total_order_received'];
			
			$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
								FROM payment_details PD,location LO
								WHERE  LO.emp_code='".$emp_code."' 
								AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
			$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
			$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
			
			$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
								FROM location LO,order_header OH,customer_master CM WHERE  LO.emp_code='".$emp_code."' 
								AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') AND OH.order_no=LO.trans_id AND OH.customer_code=CM.customer_code AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
			$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
			$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
			
			$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit
								FROM location LO WHERE  LO.emp_code='".$emp_code."' 
								AND (LO.trans_id LIKE 'SE%')  AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
			$rsstockaudit=mysqli_query($link,$sqlstockaudit) or die(mysqli_error()." Error in total no transaction: ".$sqlstockaudit);
			$rowstockaudit=mysqli_fetch_assoc($rsstockaudit);
			if(stk_audit=='yes' || retailer_care=='yes'){
			$stk_audit_customer_TD="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
			".$rowstockaudit['total_stk_audit']."</td>";
			}

            
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','YC','activity')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".count($customer_code_array)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowtotalorder['total_order_received']."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format(round($total_amount_received,2),2)."</td>";
					if(collection == 'yes'){
					$rowval.= "<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowtotalcollection['total_collection_received']."</td>";
					}
					$rowval.= "<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rownotransaction['total_no_transaction']."</td>".$stk_audit_customer_TD."
              </tr>";
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;
echo $finalval.'<br />'.'<br />';
	}
	
	//For Listing the customer Of Datewise Employee Activity
	
	if($_REQUEST['emp_code']!='')
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
			if($date!='' && sale=='no' && instruction=='yes')
			{
				$date_condition ="  AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			}
			else
			{
				$date_condition ="  AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
			}
		}
		if($mode=='Y')
		{
			$date=date('Y-m-d', strtotime("-1 days,$curdate "));
			if(sale=='no' && instruction=='yes')
			{
				$date_condition ="  AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			}
			else
			{
				$date_condition ="  AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
			}
		}
		if($mode=='YC')
		{
			$from_date=date('Y-m-d',strtotime($_REQUEST['from_date']));
			$to_date=date('Y-m-d',strtotime($_REQUEST['to_date']));
			if(sale=='no' && instruction=='yes')
			{
				$date_condition=" AND DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
			}
			else
			{
				$date_condition=" AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
			}
		}
		$date_array=array();
		if(DCR_map=='yes')
		{
			$locatestring='<td width="" align="left" style="padding-left:20px; color:#930;">Locate</td>';
		}
		else
		{
			$locatestring='';
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
				<td width="18%" align="left" style="padding-left:20px;">'.$order_sauda_text.' '.$order_sauda_text_one.' Quantity</td>
				<td width="18%" align="left" style="padding-left:20px;">Collection Received(Rs/-)</td>'.$locatestring.'
			</tr> ';
				 $sqltrans="SELECT *,DATE_FORMAT(date,'%d-%m-%Y') AS date FROM location WHERE emp_code='".$emp_code."'".$date_condition." ORDER BY date DESC ";
				 $restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id: ".$sqltrans);
				 $cnt=$GLOBALS[start]+1;
				 $rowvallist="";
				while($rowtrans=mysqli_fetch_assoc($restrans))
				{
				  $trans_id=$rowtrans['trans_id'];
				  $latt=$rowtrans['latt'];
				  $longi=$rowtrans['longi'];
				  $operation_type=substr($trans_id,0,1);
				  if($mode!='T' && !in_array($rowtrans['date'],$date_array))
					{
						array_push($date_array,$rowtrans['date']);
						$rowvallist.="<tr> <td valign=\"top\" align=\"center\" colspan=\"6\"><strong>".$rowtrans['date']."</strong></td></tr>";
					}
				  if($operation_type=='O')
					{
						$order_no=$trans_id;
						/*$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
									  GROUP BY OD.order_no";*/
						if(sale=='no'){
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
									   GROUP BY OD.order_no";
						}
						else
						{
							$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received FROM 
										  order_header OH,customer_master CM,order_details OD
										  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
										  GROUP BY OD.order_no UNION SELECT VM.vendor_name 
										  AS customer_name,VM.vendor_code AS customer_code,SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received FROM 
										  order_header OH,vendor_master VM,order_details OD
										  WHERE VM.vendor_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
										  GROUP BY OD.order_no";
						}			  
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						$customer_code=$rowcustomer['customer_code'];
						$customer_name=$rowcustomer['customer_name'];
						if(DCR_map=='yes')
						{
						  if($latt==0 && $longi==0)
						  {
							  $locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
						  }
						  else
						  {
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
									<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
									emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>";				}
						}
						else $locateval='';
						$rowvallist.="<tr> 
									<td valign=\"top\" align=\"center\">".$cnt++."</td>
									<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
									<a href=\"javascript:void(0)\" onClick=\"javascript:populateOrderDetails('".$trans_id."','".$customer_code."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
									<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
									<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowcustomer['total_order_received']."</td>
									<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>".$locateval."
								</tr>";
					}
				if($operation_type=='P')
				{
					$receipt_id=$trans_id;
					/*$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
										FROM payment_header PH,customer_master CM,payment_details PD
										WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
										AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";*/
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
					$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
					$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
					$customer_code=$rowcustomerpayment['customer_code'];
					$customer_name=$rowcustomerpayment['customer_name'];
		
					if(DCR_map=='yes')
					{
						if($latt==0 && $longi==0)
						  {
							  $locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
						  }
						  else
						  {
						$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
									<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
									emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date\" 	style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
						  }
					}
					else $locateval='';
					$rowvallist.="<tr> 
									<td valign=\"top\" align=\"center\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
									<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" onClick=\"javascript:populateCollectionDetails('".$trans_id."','".$customer_code."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
									<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
									<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
									<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowcustomerpayment['total_collection_received']."</td>".$locateval."
								</tr>";					
				}            
			  }
		$tablevalendlist='</table>';				
		$finalvallist=$tablevallist.$rowvallist.$tablevalendlist;

		echo $finalvallist;
	}
 ?>
 <!----------------------------------------End of Table for populate populate employee date wise locate back----------------------------------------------!-->
			 <div id="employeetabledisplaydatewise" style="display:none;">
                	 
                </div><br /><br />
                  <div id="customertabledisplaydatewise" style="display:none">
                </div><br />
                <div id="orderDetails" style="display:none">
                </div><br />
                <div id="collectionDetails" style="display:none">
                </div>
                <div id="auditDetails" style="display:none">
                </div>
               <div id="loader" style="display:none">
                      <br/>
               <center><img src="ajax-loader.gif" /></center></div>
           
            <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1">
                <tr> 
                    <td align="center" width="100%">
                    	<input type="radio" value="yesterday" name="radio_type" onClick="javascript:search_att('today');" 
						<?php if($_REQUEST['radio_type']=='today'){?>checked<?php }?>/>Today
                        &nbsp;&nbsp;&nbsp;
                    	<input type="radio" value="yesterday" name="radio_type" onClick="javascript:search_att('yesterday');" 
						<?php if($_REQUEST['radio_type']=='yesterday'){?>checked<?php }?>/>Yesterday
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" value="yourchoice" name="radio_type" onclick="search_att('choice');" <?php if($_REQUEST['radio_type']=="yourchoice"){?>checked<?php }?>/>Your Choice
                    </td>
                </tr>
                <!--tr id="employeedate" style="display:
				<?php /*if($_REQUEST['radio_search']=='employee' || $_REQUEST['radio_search']=='datewise'){?>''<?php }else{?>none<?php }?>">
                	<td align="center" width="70%">
                    	<input type="radio" value="employee" name="radio_search" onClick="javascript:search_att('employeewise');" 
						<?php if($_REQUEST['radio_search']=='employee'){?>checked<?php }?>/>Employeewise
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" value="datewise" name="radio_search" onClick="javascript:search_att('datewise');" 
						<?php if($_REQUEST['radio_search']=='datewise'){?>checked<?php }*/?>/>Datewise
                    </td>
                </tr-->
             </table>
 <!------------------------------------------------Start of Table for populate employee dropdown--------------------------------------------------!-->
                <!--table width="40%" align="center" border="0" cellpadding="5" cellspacing="1" 
                style="display:<?php /*if($_REQUEST['radio_search']=="employee"){?>''<?php }else{?>none<?php }?>;" id="employeedropdown" class="border">
                        <tr>
                            <td width="10%">Employee:</td>
                            <td width="80%">
                                    <?php
										$emp_code=$_REQUEST['emp_code'];
									 echo PopulateSelect('emp_code', "SELECT EM.emp_code,EM.emp_name FROM employee_master EM,location LO WHERE EM.emp_code=LO.emp_code AND LO.trans_id LIKE 'A%' GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ", 'emp_code', 'emp_name', $emp_code,"onChange=javascript:showEmployeeWisedisplay(this.value);",'inplogin');*/?>					
                                &nbsp;
                            </td>
                        </tr>
                 </table--> 
 <!------------------------------------------------End of Table for populate employee dropdown------------------------------------------------------!-->

 <!------------------------------------------------Start of Table for populate date range dropdown------------------------------------------------------!-->
                 <table width="60%" align="center" border="0" cellpadding="5" cellspacing="1" style="display:<?php if($_REQUEST['mode']=="YC"){?>''<?php }else{?>none<?php }?>;" id="datedropdown" class="border">
                    <tr>
                        <td align="left" width="15%">From Date:</td>
                        <td align="left" width="20%" style="vertical-align:top;">
                             <?php 
							 $from_date=date('d-m-Y',strtotime($_REQUEST['from_date']));
							 echo PopulateSelect('from_date', "SELECT DATE_FORMAT(date,'%d-%m-%Y') AS date FROM location WHERE trans_id NOT LIKE 'A%' GROUP BY DATE_FORMAT(date,'%d-%m-%Y') ORDER BY DATE_FORMAT(date,'%Y-%m-%d') DESC ", 'date', 'date', $from_date,'','inplogin');?>					
                        </td>
                        <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                        <td width="20%" style="vertical-align:top;">
                            <?php 
							$to_date=date('d-m-Y',strtotime($_REQUEST['to_date']));
							echo PopulateSelect('to_date', "SELECT DATE_FORMAT(date,'%d-%m-%Y') AS date FROM location WHERE trans_id NOT LIKE 'A%' GROUP BY DATE_FORMAT(date,'%d-%m-%Y') ORDER BY DATE_FORMAT(date,'%Y-%m-%d') DESC ", 'date', 'date', $to_date,'','inplogin');?>					
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
<?php }//End of main()?>
>>>>