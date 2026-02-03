<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	if($mode == 'updateDOstatus')  updateDOstatus($_REQUEST['row_id'],$_REQUEST['row_id_one'],$_REQUEST['row_id_two']);
	else  disphtml("main();");

ob_end_flush();
?>
<?php
function main()
{
	$current_date = date('Y-m-d');
	$month_date = date('Y-m');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		//$previous_year = date('Y', strtotime('-1 year'));
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		//$previous_year_date = date('Y-04-01');
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	$sqlexpirybargaindetails="SELECT sauda_no FROM DO_master
				WHERE is_approved='yes' AND qty >0 AND  sauda_no NOT IN(SELECT DISTINCT sauda_no FROM DO_transaction) AND  
				CURRENT_DATE > `valid_upto`
				ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC ";	
//exit();					
	$resexpirybargaindetails = mysql_query($sqlexpirybargaindetails);
	$totalexpirybargaindetails = mysql_num_rows($resexpirybargaindetails);
?>
<html>

 <style>
.datatable{
  width:98%;
  table-layout: fixed;
  }
.tbl-header{
  background-color: rgba(255,255,255,0.3);
 }
.tbl-content{
  height:400px;
  overflow-x:auto;
  margin-top: 0px;
  border: 1px solid rgba(255,255,255,0.3);
}
.datatable th{
  padding: 20px 15px;
  text-align: left;
  font-weight: 500;
  font-size: 12px;
  color: #fff;
  text-transform: uppercase;
}
.datatable td{
  padding: 15px;
  text-align: left;
  vertical-align:middle;
  font-weight: 300;
  font-size: 12px;
  color: #000000;
  border-bottom: solid 1px rgba(255,255,255,0.1);
}
/* demo styles */
/* for custom scrollbar for webkit browser*/
::-webkit-scrollbar {
    width: 6px;
} 
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
} 
::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>

<body onLoad="display_result();">
<!--table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Bargain Approval</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        		<table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><? /*echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
                
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
					<input type="hidden" name="mode" value="">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        <?php
							$onclickcustomer = "customer_bargain(this.value);"; 
							$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Customer:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
							$sql_customer = "SELECT DISTINCT CM.customer_code,CM.customer_name FROM customer_master CM,DO_master DM 
											WHERE DM.is_approved='no' AND DM.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
							$res_customer = mysql_query($sql_customer);
							$customer_select_control = "<select name=\"customer_code\" id=\"customer_code\" onchange=\"".$onclickcustomer."\">";
							$customer_select_control .= "<option value=\"\">Select</option>";
							//$vertical_select_control .= "<option value=\"all\">All</option>";
							while($row_customer = mysql_fetch_array($res_customer)){
									$customer_string .= "'".$row_customer['customer_code']."',";
									$customer_select_control .= "<option value=\"'".$row_customer['customer_code']."'\">".$row_customer['customer_name']."</option>";
								}
								$customer_string = rtrim($customer_string,",");
								$customer_select_control .= "</select>";
								$table_data .= $customer_select_control;
								echo $table_data .= "</td></tr>";
								$table_data_bargain .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">Bargain Date Time:</td>";
								$table_data_bargain .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
													<div id=\"bargain_select_div\"></div></td></tr>";
								echo $table_data_bargain;
						?>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" name="submit" onClick="display_result();">
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                      <form name="frm_opts" action="adminBargainApproved.php" method="post" >
                        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']*/?>">
                        <input type="hidden" name="row_id" value="">
                        <input type="hidden" name="row_id_one" value="">
                         <input type="hidden" name="row_id_two" value="">
                    </form>
                     </table> 
                     <br /-->
                     <center>
       <div id="display" style="max-height: 300px; max-width:1500px; overflow-y: scroll; overflow-x: scroll;display:none;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:800px;display:none;"  align="center" ></div><br />
    <!--div style="width:100%;" align="center" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
</center>
			   <script language="javascript" type="text/javascript">
			   function update_status(DO_no,sku_code,status)
				{
					document.frm_opts.mode.value="updateDOstatus";
					document.frm_opts.row_id.value=DO_no;
					document.frm_opts.row_id_one.value=sku_code;
					document.frm_opts.row_id_two.value=status;
					document.frm_opts.submit();
				}

			   function access_add_edit(prod_code,order_no)
				{
					document.getElementById("display_details").style.display = '';
					document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
					GenericAjaxFunction('order_edit_html.php?prod_code='+prod_code+'&order_no='+order_no,'display_details',0);
					document.getElementById("display_details").focus();
				}
                function customer_bargain(customer_code){
                    if(document.getElementById("customer_code").value.search(/\S/) == -1)
                        return false;
                    var customer_code = encodeURIComponent(customer_code);
                    document.getElementById("bargain_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_bargain_related_data.php?customer_code='+customer_code+'&opttype=bargainapprove','bargain_select_div',0);
                }
			 function display_result(){
                /*if(document.getElementById("customer_code").value.search(/\S/) == -1){
                    alert('Please Select Customer');
                    return false;
                }
                if(document.getElementById("sauda_no").value.search(/\S/) == -1){
                    alert('Please Select Bargain Date and time');
                    return false;
                }
                var customer_code = document.getElementById("customer_code").value;
                var sauda_no = document.getElementById("sauda_no").value;*/
                document.getElementById("display_details").innerHTML = '';
				document.getElementById("display_details").style.display = 'none';
				document.getElementById("display").style.display = '';
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
				GenericAjaxFunction('Bargain_approved_data.php','display',0);
                document.getElementById("print_export").hidden = false;
            }
	function PrintElem(elem)
	   {
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	   }

	function Popup(data) 
	{
		var mywindow = window.open('', 'Customer DO Details', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Customer DO Details</title>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		mywindow.document.write('</head><body >');
		mywindow.document.write(data);
		mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
	}
	
	/*function exporttocsv(divid)
	{
		var get_report_name = document.getElementById("report_name").value
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var a = document.createElement('a');
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		a.download = 'Customer Visit Report' + postfix + '.xls';
		a.click();
	}*/
function exporttocsv()
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Customer DO Data' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
}
        </script>
        <br />
 <?php
 if($_REQUEST['mode']=='approvebargain'){
    /*echo '<prev>';
		print_r($_POST);
		echo '</prev>';
	exit();	*/

	$saudano=$_REQUEST['saudano'];
	$prod_val=$_REQUEST['prod_val'];
	$qty_val=$_REQUEST['qty_val'];
	$corporate_val=$_REQUEST['corporate_val'];
	$customername_val=$_REQUEST['customername_val'];
	$prodname_val=$_REQUEST['prodname_val'];
	$customer_code_val=$_REQUEST['customer_code_val'];
	$countprod=1;
	$corporate_error='';
	for($i=0;$i<=count($prod_val);$i++)
	{
	  if(strtoupper($corporate_val[$i])=='CORPORATE' && !file_exists($_FILES["po_file_$saudano[$i]_$prod_val[$i]"]['tmp_name']) && $_REQUEST["bargain_status_$saudano[$i]_$prod_val[$i]"]=='approved')
	  {
		  $corporate_error='1';
	?>
    	     <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR">Bargain approval not possible for the customer - 
					<?php echo str_replace('_',' ',$customername_val[$i]);?> and the product <?php echo str_replace('_',' ',$prodname_val[$i]);?> . As Corporate customer shoul have PO.</td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
    <?php
	  }
	  else
	  {
	  
	  $bargain_status_val=$_REQUEST["bargain_status_$saudano[$i]_$prod_val[$i]"];
	  $current_qty=$_REQUEST["saudaqty_$saudano[$i]_$prod_val[$i]"];
	  if($bargain_status_val=='approved'){
		$sqlmappedsku="SELECT mapped_sku_code FROM DO_master WHERE sku_code='".$prod_val[$i]."' AND sauda_no='".$saudano[$i]."'";
		$rsmappedsku=mysql_query($sqlmappedsku);
		$rowmappedsku=mysql_fetch_array($rsmappedsku);
		$mapped_sku_code=$rowmappedsku['mapped_sku_code'];
 	    $upload_dir="../upload/ASL/";
		if(file_exists($_FILES["po_file_$saudano[$i]_$prod_val[$i]"]['tmp_name']))
		{
		$file_name = $_FILES["po_file_$saudano[$i]_$prod_val[$i]"]['name'];
		$tmp_name=$_FILES["po_file_$saudano[$i]_$prod_val[$i]"]['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		 move_uploaded_file($tmp_name,$upload_file);
		}
		else
		{
			$file_name='';
		}
		$sqlupdatebargainapprove="UPDATE DO_master SET is_approved='yes',qty='".$current_qty."',prev_qty='".$qty_val[$i]."',
		  							is_approved_date_time=CURRENT_TIMESTAMP(),
									is_approved_by='".$_SESSION['admin_login']."',
									po_file='".$file_name."'
									WHERE sku_code='".$prod_val[$i]."' AND sauda_no='".$saudano[$i]."'";
		  mysql_query($sqlupdatebargainapprove);
		  $sqlupdatechildbargainapprove="UPDATE DO_master SET is_approved='yes',is_approved_date_time=CURRENT_TIMESTAMP(),
									is_approved_by='".$_SESSION['admin_login']."'
									WHERE mapped_sku_code='".$mapped_sku_code."' AND sauda_no='".$saudano[$i]."'";
		  mysql_query($sqlupdatechildbargainapprove);
		  
							
	   }
	   if($bargain_status_val=='reject'){
		   	$sqlmappedsku="SELECT mapped_sku_code FROM DO_master WHERE sku_code='".$prod_val[$i]."' AND sauda_no='".$saudano[$i]."'";
			$rsmappedsku=mysql_query($sqlmappedsku);
			$rowmappedsku=mysql_fetch_array($rsmappedsku);
			$mapped_sku_code=$rowmappedsku['mapped_sku_code'];

		  $sqlupdatebargainreject="UPDATE DO_master SET is_approved='reject',
		  							is_approved_date_time=CURRENT_TIMESTAMP(),
									is_approved_by='".$_SESSION['admin_login']."'
									WHERE sku_code='".$prod_val[$i]."' AND sauda_no='".$saudano[$i]."'";
		  mysql_query($sqlupdatebargainreject);
		  $sqlupdatechildbargainreject="UPDATE DO_master SET is_approved='reject',
		  							is_approved_date_time=CURRENT_TIMESTAMP(),
									is_approved_by='".$_SESSION['admin_login']."'
									WHERE mapped_sku_code='".$mapped_sku_code."' AND sauda_no='".$saudano[$i]."'";
		  mysql_query($sqlupdatechildbargainreject);
	   }
	  }
	 }
	 if($corporate_error==''){
	 ?>
     <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR">Bargain approved successfully</td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
	 <?php
	 }
  }
}// end main
function updateDOstatus($row_id,$row_id_one,$row_id_two)
{
	$DO_no = $row_id;
	$sku_code=$row_id_one;
	$status=$row_id_two;
	
	$upd_sql="UPDATE DO_transaction SET DO_status ='".$status."'
			 WHERE DO_no = '" .$DO_no."' AND sku_code='".$sku_code."'";
	mysql_query($upd_sql) or die(mysql_error()." Error in DO status updation.");
	
	//For push notification
	if($status=='approved')
	{
		$sqlcustomerroute="SELECT CM.customer_name,RM.route_name FROM customer_master CM,route_master RM,DO_transaction `DO` WHERE 
							CM.route_code=RM.route_code AND `DO`.customer_code=CM.customer_code AND `DO`.destination=RM.route_code 
							AND `DO`.DO_no='".$DO_no."'";
		$rscustomerroute=mysql_query($sqlcustomerroute);
		$rowcustomerroute=mysql_fetch_array($rscustomerroute);
		$customer_name=$rowcustomerroute['customer_name'];
		$route_name=$rowcustomerroute['route_name'];				
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$year.$month.$date.$hour.$minute.$second;
    	$notification_type='Broadcast OTP';
		$apiKey='AAAA1Zogo-E:APA91bGp4CvpqyREkzZRyOd2_6ExuXWxR8AQpMkftS0gk2wgMD_MrJlkFzKGh4FsMxEugyx1YER6IXFMcLJJrcAf5xNbRcoafWLp70uqApMatOEm9L0J7T8ugutbND1pEBYPF7Lm0980';
		$collapseKey=rand();
		$notification_id='PN'.strtoupper($_SESSION['admin_login']).$location_date;
		$registration_id_array=array();
		$emp_code_array=array();
		//Title of the Notification.
		$sqlemdetails="SELECT OMA.emp_code,CH.registrationid FROM OTP_menu_access OMA,changepassword CH
						WHERE OMA.emp_code=CH.emp_code AND OMA.accessible_menu='transporter'";
		$rsempdetails=mysql_query($sqlemdetails);
		while($rowempdetails=mysql_fetch_array($rsempdetails))
		{
			$registrationid=$rowempdetails['registrationid'];
			$emp_code=$rowempdetails['emp_code'];
			if(!in_array($registrationid,$registration_id_array))
			{
				array_push($registration_id_array,$registrationid);
				array_push($emp_code_array,$emp_code);
			}
		}
		$title = "";
		$message="Hi,<br /> ".$DO_no." has been approved of ".$customer_name." of ".$route_name."<br /> THANKS,<br />ASL";
		$messageFCM="Hi,\n".$DO_no." has been approved of ".$customer_name." of ".$route_name."\nTHANKS,\nASL";
		//$message=$notificatiomessage." THANKS,\nVCONNECT";
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $messageFCM);
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => strtoupper($_SESSION['admin_login']), 'body' => $messageFCM); 
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
			$sqlnotificationmaster  = "INSERT INTO notification_master ";
			$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
			$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
			$sqlnotificationmaster .= " ,sender_id='".strtoupper($_SESSION['admin_login'])."'";
			$sqlnotificationmaster .= " ,message='".addslashes($message)."'";
			$sqlnotificationmaster .= " ,transferred='YES'";

			//print_r($registration_id_array);
			for($k=0;$k< count($registration_id_array);$k++)
			{
				$arrayToSend = array('to' => $registration_id_array[$k], 'data'=>$data);
				// Set POST variables
				$url = 'https://fcm.googleapis.com/fcm/send';
				$headers = array(
					'Authorization: key='.$apiKey,
					'Content-Type: application/json'
				);
				// Open connection
				$ch = curl_init();
		 
				//Set the url, number of POST vars, POST data
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
				// Disabling SSL Certificate support temporarly
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
		 
				// Execute post
				$result = curl_exec($ch);
				//print_r($result);
				/*if ($result === FALSE) {
					die('Curl failed: ' . curl_error($ch));
				}*/
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($httpCode != 200) {    
					//request failed    
					$successval=0; 
				} 
				else
				{
					$successval=1;	
				}
				// Close connection
				curl_close($ch);
				if($successval==1)
				{
					$sqlnotification  = "INSERT INTO notification_ack_relation ";
					$sqlnotification .= " SET notification_id='".$notification_id."'";
					$sqlnotification .= " ,receiver_id='".$emp_code_array[$k]."'";
					mysql_query($sqlnotification) or die(mysql_error()." Error in notification insertion.");
				}
			}
			if($successval==1)
			{
				mysql_query($sqlnotificationmaster) or die(mysql_error()." Error in notification insertion.");
			}

	}
	$GLOBALS['err_msg']="DO ".strtoupper($status)." SUCCESSFUL.";
	disphtml("main();");
}
?>
