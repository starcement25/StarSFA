<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	disphtml("main();");
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
 if($_REQUEST['mode']=='transferbargain'){
    /*echo '<prev>';
		print_r($_POST);
		echo '</prev>';*/
	//exit();
	$saudano=$_REQUEST['saudano'];
	$bargain_date=date('Y-m-d H:i:s',strtotime(substr($saudano,-14)));
	
	$dns_sauda_no=$_REQUEST['dns_sauda_no'];
	$total_qty=$_REQUEST['total_qty'];
	$customer_code=$_REQUEST['customer_code'];
	//array POST
	$prod_val=$_REQUEST['prod_val'];
	$parent_qty_val=$_REQUEST['parent_qty_val'];
	$prodname_val=$_REQUEST['prodname_val'];
	$countprod=1;
	$validation_error='';
	$commoditiy_array=array();
	for($i=0;$i<=count($prod_val);$i++)
	{
	  $sqlcommoditiy="SELECT PSGM.product_sub_group_name FROM product_master PM,product_sub_group_master PSGM 
	  			WHERE PM.product_sub_group_code=PSGM.product_sub_group_code  AND 
			PM.prod_code='".$prod_val[$i]."'";
	  $rescommoditiy = mysqli_query($link,$sqlcommoditiy);
	  $rowcommoditiy = mysqli_fetch_assoc($rescommoditiy);
	  $commodity_name=$rowcommoditiy['product_sub_group_name'];
	  ${commodity_input_total.$commodity_name}=${commodity_input_total.$commodity_name}+$_REQUEST['saudaqty_'.$prod_val[$i]];
	 ${commodity_actual_total.$commodity_name}=${commodity_actual_total.$commodity_name}+$parent_qty_val[$i];
		if(!in_array($commodity_name,$commoditiy_array))
		{
			array_push($commoditiy_array,$commodity_name);
		}
	}
	//print_r($commoditiy_array);
	//echo ${commodity_input_total.'PALMOLEIN'};
	//echo ${commodity_actual_total.'PALMOLEIN'};
	foreach($commoditiy_array as $commodityval)
	{
	 if(${commodity_input_total.$commodityval}!=${commodity_actual_total.$commodityval})
	 {
	    $validation_error.='Total transfer qty and the Total Bargain qty will be same for 
		'.$commodityval.'<br />';
	 }
	 else
	 {
		 $validation_error.='';
	 }
	}
	if($validation_error!='')
	{
	?>
      <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
        <tr> 
            <td align="center" class="ERR"><?Php echo  $validation_error;?></td>
            <td align="right">&nbsp;</td>
            <td align="right" width="3%">&nbsp;</td>
        </tr>
        </table>
    <?php
	  }
	  else
	  {
		  //echo 'Further Processing';
		  //exit();
		for($i=0;$i<=count($prod_val);$i++)
		{
		  	$sqldnsprodcode="SELECT dns_prod_code,UOM4 FROM product_master WHERE prod_code='".$prod_val[$i]."'";
			$rsdnsprodcode=mysqli_query($link,$sqldnsprodcode);
			$rowdnsprodcode=mysqli_fetch_assoc($rsdnsprodcode);
			$dns_prod_code=$rowdnsprodcode['dns_prod_code'];
			$UOM4parent=$rowdnsprodcode['UOM4'];

			$sqlparentexstchkblank="SELECT qty FROM DO_master WHERE 
			sauda_no='".$saudano."' AND mapped_sku_code='".$dns_prod_code."' AND is_approved='yes' AND qty >0";
			$rsparentexstchkblank=mysqli_query($link,$sqlparentexstchkblank);
			$cntparentexstchkblank=mysqli_num_rows($rsparentexstchkblank);
			if($cntparentexstchkblank >0)
			{
				if($_REQUEST['saudaqty_'.$prod_val[$i]]==0 ||  $_REQUEST['saudaqty_'.$prod_val[$i]]=='')
		  		{
					//$sqldelparent="DELETE FROM DO_master WHERE sauda_no='".$saudano."' AND mapped_sku_code='".$dns_prod_code."'";
					$sqlupdateDOparent="UPDATE DO_master SET qty='0',
								is_transferred_by='".$_SESSION['admin_login']."',
								transferred_date_time=CURRENT_TIMESTAMP()
						 WHERE sauda_no='".$saudano."' AND mapped_sku_code='".$dns_prod_code."' 
						 AND is_approved='yes' AND qty >0";
					mysqli_query($link,$sqlupdateDOparent);
				}
			}
		  if($_REQUEST['saudaqty_'.$prod_val[$i]] >0)
		  {
			$sqlparentexstchk="SELECT qty,sale_rate,sku_code,freight_charge FROM DO_master WHERE 
			sauda_no='".$saudano."' AND mapped_sku_code='".$dns_prod_code."' AND is_approved='yes' AND qty >0";
			$rsparentexstchk=mysqli_query($link,$sqlparentexstchk);
			$cntparentexstchk=mysqli_num_rows($rsparentexstchk);
			if($cntparentexstchk >0)
			{
				$sqlupdateDO="UPDATE DO_master SET qty='".$_REQUEST['saudaqty_'.$prod_val[$i]]."',
								is_transferred_by='".$_SESSION['admin_login']."',
								transferred_date_time=CURRENT_TIMESTAMP()
						 WHERE sauda_no='".$saudano."' AND mapped_sku_code='".$dns_prod_code."' 
						 AND is_approved='yes' AND qty >0";
					mysqli_query($link,$sqlupdateDO);	 
			}
			else
			{
			  $sqlselchildprods="SELECT PM.prod_code,PM.dns_prod_code,PM.pack_size,PM.UOM4,PM.UOM5,PUCM.add_subtract_val 
			  				FROM product_master PM,product_unit_coversion_matrix PUCM WHERE PUCM.prod_code=PM.dns_prod_code AND PUCM.acedns='Y' 
							AND PM.acedns='Y' AND PUCM.mapped_prod_code='".$dns_prod_code."'";
			$rschildprods=mysqli_query($link,$sqlselchildprods);
			while($rowchildprods = mysqli_fetch_assoc($rschildprods))
			{
				$child_prod_code=$rowchildprods['prod_code'];
				$child_dns_prod_code=$rowchildprods['dns_prod_code'];
				$customer_code=$customer_code;
				$sku_code=$child_prod_code;
				if($child_prod_code==$prod_val[$i])
				{
					$qty=$_REQUEST['saudaqty_'.$prod_val[$i]];
					$sqlrate="SELECT sale_rate FROM DO_master WHERE  product_code='".$child_prod_code."' AND 
							sauda_no='".$saudano."' AND is_approved='yes'";
					$rsrate=mysqli_query($link,$sqlrate);
					$recrate=mysqli_fetch_assoc($rsrate);
					$sale_rate_child=$recrate['sale_rate'];
					$sale_rate_parent=$sale_rate_child;
				}
				else
				{
					$qty=0;
					/*$sqlrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$child_prod_code."' 
								AND release_date < '".$bargain_date."' ORDER BY release_date DESC LIMIT 0,1 ";*/
					if($rowchildprods['pack_size']=='BP')
					{
						$sale_rate_child=$sale_rate_parent+$rowchildprods['add_subtract_val'];
						$sale_rate_child=round($sale_rate_child,0);
					}
					if($rowchildprods['pack_size']=='CP')
					{
						$sale_rate_child=((($sale_rate_parent/$UOM4parent)*$rowchildprods['UOM5'])+$rowchildprods['add_subtract_val'])*$rowchildprods['UOM4'];
						$sale_rate_child=round($sale_rate_child,1);
					}			
				}				
				//exit();
				$mapped_prod_code=$dns_prod_code;
				$freight_charge=0;
				$amount=$qty*($sale_rate_child+$freight_charge);
				//End for child freight charge
				$sqlinsertDomaster="INSERT INTO DO_master SET sauda_no='".$saudano."',
									customer_code='".$customer_code."',
									dns_sauda_no='".$dns_sauda_no."',
									branch_code='".$branch_code."',
									sku_code='".$child_prod_code."',
									mapped_sku_code='".$dns_prod_code."',
									qty='".$qty."',
									sale_rate='".$sale_rate_child."',
									freight_charge='".$freight_charge."',
									amount='".$amount."',
									incoterms='".$incoterms."',
									status='no',
									is_approved='yes',
									is_transferred_by='".$_SESSION['admin_login']."',
									transferred_date_time=CURRENT_TIMESTAMP(),
									download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertDomaster);
			  }//end of while
		   }//end of else
		  }
		 }//end of for loop
		 ?>
		 <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
					<tr> 
						<td align="center" class="ERR">Bargain transferred successfully</td>
						<td align="right">&nbsp;</td>
						<td align="right" width="3%">&nbsp;</td>
					</tr>
          </table>
		 <?php
	  }
 }
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
<body>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Opening Bargain Transfer</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        		<table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
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
											WHERE DM.is_approved='yes' AND DM.status='no' AND DM.customer_code=CM.customer_code 
											AND DM.dns_sauda_no LIKE 'B/%' ORDER BY CM.customer_name ASC";
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
								$table_data_bargain .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">Bargain No:</td>";
								$table_data_bargain .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
													<div id=\"bargain_select_div\"></div></td></tr>";
								echo $table_data_bargain;
						?>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" name="submit" onClick="display_result_transfer();">
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                     <br />

                     <center>

       <div id="display" style="max-height: 300px; max-width:1300px; overflow-y: scroll; overflow-x: scroll;display:none;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:800px;display:none;"  align="center" ></div><br />
    <!--div style="width:100%;" align="center" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
</center>
			   <script language="javascript" type="text/javascript">
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
                    GenericAjaxFunction('get_bargain_related_data.php?customer_code='+customer_code+'&opttype=bargaintransferopening','bargain_select_div',0);
                }

			 function display_result_transfer(){
                if(document.getElementById("customer_code").value.search(/\S/) == -1){
                    alert('Please Select Customer');
                    return false;
                }
                if(document.getElementById("sauda_no").value.search(/\S/) == -1){

                    alert('Please Select Bargain Date and time');
                    return false;
                }
                var customer_code = document.getElementById("customer_code").value;
                var sauda_no = document.getElementById("sauda_no").value;
                document.getElementById("display_details").innerHTML = '';
				document.getElementById("display_details").style.display = 'none';
				document.getElementById("display").style.display = '';
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
				GenericAjaxFunction('Bargain_transfer_data_opening.php?customer_code='+customer_code+'&sauda_no='+sauda_no,'display',0);
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
}// end main

function updateDOstatus($row_id,$row_id_one,$row_id_two)

{

	$DO_no = $row_id;

	$sku_code=$row_id_one;

	$status=$row_id_two;

	

	$upd_sql="UPDATE DO_transaction SET DO_status ='".$status."'

			 WHERE DO_no = '" .$DO_no."' AND sku_code='".$sku_code."'";

	mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in DO status updation.");

	

	//For push notification

	if($status=='approved')

	{

		$sqlcustomerroute="SELECT CM.customer_name,RM.route_name FROM customer_master CM,route_master RM,DO_transaction `DO` WHERE 

							CM.route_code=RM.route_code AND `DO`.customer_code=CM.customer_code AND `DO`.destination=RM.route_code 

							AND `DO`.DO_no='".$DO_no."'";

		$rscustomerroute=mysqli_query($link,$sqlcustomerroute);

		$rowcustomerroute=mysqli_fetch_assoc($rscustomerroute);

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

					mysqli_query($link,$sqlnotification) or die(mysqli_error()." Error in notification insertion.");

				}

			}

			if($successval==1)

			{

				mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");

			}



	}

	$GLOBALS['err_msg']="DO ".strtoupper($status)." SUCCESSFUL.";

	disphtml("main();");

}

?>