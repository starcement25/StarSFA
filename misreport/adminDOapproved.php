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
	/*$sqlexpirybargaindetails="SELECT sauda_no FROM DO_master
				WHERE is_approved='yes' AND qty >0 AND  sauda_no NOT IN(SELECT DISTINCT sauda_no FROM DO_transaction) AND  
				CURRENT_DATE > `valid_upto`
				ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC ";	
//exit();					
	$resexpirybargaindetails = mysqli_query($link,$sqlexpirybargaindetails);
	$totalexpirybargaindetails = mysqli_num_rows($resexpirybargaindetails);
	//$totalexpirybargaindetails=0;*/
?>
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
<?php //if($totalexpirybargaindetails >0){?>
<!--script type="text/javascript">
	window.location="adminExpiryBargainApproved.php?page=2";
</script-->
<?php
//}else{
	?>	
<body onLoad="display_result();">
<?php //}?>
<!--table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>DO Approval</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        		<table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><?php //echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
                
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?php //echo $_SERVER['PHP_SELF']?>" >
					<input type="hidden" name="mode" value="">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        <?php
							/*$onclickcustomer = "customer_bargain(this.value);"; 
							$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Customer:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
							$sql_customer = "SELECT DISTINCT CM.customer_code,CM.customer_name FROM customer_master CM,DO_transaction DT 
											WHERE DT.DO_status='' AND DT.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
							$res_customer = mysqli_query($link,$sql_customer);
							$customer_select_control = "<select name=\"customer_code\" id=\"customer_code\" onchange=\"".$onclickcustomer."\">";
							$customer_select_control .= "<option value=\"\">Select</option>";
							//$vertical_select_control .= "<option value=\"all\">All</option>";
							while($row_customer = mysqli_fetch_assoc($res_customer)){
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
								echo $table_data_bargain;*/
						?>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" name="submit" onClick="display_result();">
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                      <form name="frm_opts" action="adminDOapproved.php" method="post" >
                        <input type="hidden" name="mode" value="<?php //echo $_REQUEST['mode']?>">
                        <input type="hidden" name="row_id" value="">
                        <input type="hidden" name="row_id_one" value="">
                         <input type="hidden" name="row_id_two" value="">
                    </form>
                     </table> 
                     <br /-->
                     <center>
       <div id="display" style="max-height: 300px; max-width:1350px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:800px;"  align="center" style="display:''"></div><br />
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
                    GenericAjaxFunction('get_bargain_related_data.php?customer_code='+customer_code,'bargain_select_div',0);
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
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
				//GenericAjaxFunction('DO_approved_data.php?customer_code='+customer_code+'&sauda_no='+sauda_no,'display',0);
				GenericAjaxFunction('DO_approved_data.php','display',0);
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
//function updateDOstatus($row_id,$row_id_one,$row_id_two)
if($_REQUEST['mode']=='approvedo')
{
	//$DO_no = $row_id;
	//$sku_code=$row_id_one;
	//$status=$row_id_two;
	
	$dono=$_REQUEST['dono'];
	$sauda_no=$_REQUEST['sauda_no'];
	$prod_val=$_REQUEST['prod_val'];
	$customer_code_val=$_REQUEST['customer_code_val'];
	//print_r($dono);
	for($i=0;$i< count($dono);$i++)
	{
	  //$do_status_val=$_REQUEST["do_status_".$dono[$i]."_".$sauda_no[$i]."_".$prod_val[$i]];
	  $do_status_val=$_REQUEST["do_status_".$dono[$i]];
	  //echo $prod_val[$i];
	  //exit();
	  if($do_status_val=='approved'){
		/*$upd_sql="UPDATE DO_transaction SET DO_status ='approved' 
		WHERE DO_no = '" .$dono[$i]."' AND sku_code='".$prod_val[$i]."' AND sauda_no='".$sauda_no[$i]."'";*/
		$upd_sql="UPDATE DO_transaction SET DO_status ='approved' WHERE DO_no = '" .$dono[$i]."'";
		mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in DO status approve updation.");
	  }
	  if($do_status_val=='reject'){
		/*$upd_sql="UPDATE DO_transaction SET DO_status ='reject'
				 WHERE DO_no = '" .$dono[$i]."' AND sku_code='".$prod_val[$i]."' AND sauda_no='".$sauda_no[$i]."'";*/
	    $upd_sql="UPDATE DO_transaction SET DO_status ='approved' WHERE DO_no = '" .$dono[$i]."'";
		mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in DO status reject updation.");
	  }
	  
	//For push notification
	if($do_status_val=='approved')
	{
		$sqlcustomerroute="SELECT CM.customer_name,RM.route_name,SUM(DO.DO_qty_MT) AS total_qty FROM customer_master CM,route_master RM,DO_transaction DO WHERE 
							CM.route_code=RM.route_code AND DO.customer_code=CM.customer_code AND DO.destination=RM.route_code 
							AND DO.DO_no='".$DO_no."' GROUP BY DO.DO_no";
		$rscustomerroute=mysqli_query($link,$sqlcustomerroute);
		$rowcustomerroute=mysqli_fetch_assoc($rscustomerroute);
		$customer_name=$rowcustomerroute['customer_name'];
		$route_name=$rowcustomerroute['route_name'];
		$total_qty=$rowcustomerroute['total_qty'];					
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$year.$month.$date.$hour.$minute.$second;
    	$notification_type='Broadcast to Transporter';
		/*$apiKey='AAAA1Zogo-E:APA91bGp4CvpqyREkzZRyOd2_6ExuXWxR8AQpMkftS0gk2wgMD_MrJlkFzKGh4FsMxEugyx1YER6IXFMcLJJrcAf5xNbRcoafWLp70uqApMatOEm9L0J7T8ugutbND1pEBYPF7Lm0980';*/
		$apiKey='AAAAdCu4Fjw:APA91bHHl7RnWyOj4Pb42NBuPfJZQAkOlmxKCoGL9flYk8xfhsqMY7_YtOtBKXPHNgx5szKyD2T1HriSFZ5NHmcBu874v9uCym0VEQlpNYbuUOjHBUmaVvtIXXYu-FOjhuwUksA4Geob';
		$collapseKey=rand();
		$notification_id='PN'.strtoupper($_SESSION['admin_login']).$location_date;
		$registration_id_array=array();
		$emp_code_array=array();
		//Title of the Notification.
		$sqlemdetails="SELECT OMA.emp_code,CH.registrationid FROM OTP_menu_access OMA,changepassword CH
						WHERE OMA.emp_code=CH.emp_code AND OMA.accessible_menu='transporter'";
		$rsempdetails=mysqli_query($link,$sqlemdetails);
		while($rowempdetails=mysqli_fetch_assoc($rsempdetails))
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
		$messageFCM="Hi,<br /> Order has been received by client for station ".$route_name." for total load of ".$total_qty." MT. Please click here to sign vehicle no., Driver name and expected time of vehicle arrival at factory<br /> THANKS,<br />ASL";
		//$messageFCM="Hi,\n".$DO_no." has been approved of ".$customer_name." of ".$route_name."\nTHANKS,\nASL";
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
			$sqlnotificationmaster .= " ,message='".addslashes($messageFCM)."'";
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
					//@mysqli_query($link,$sqlnotification) or die(mysqli_error()." Error in notification insertion.");
				}
			}
			if($successval==1)
			{
				//@mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");
			}

		}
		
	//$GLOBALS['err_msg']="DO ".strtoupper($status)." SUCCESSFUL.";
	//disphtml("main();");
  }// end main
  ?>
         <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR">Do approved successfully</td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
        <?php
}
}
?>
