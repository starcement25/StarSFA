<?php
ob_start();
session_start();
require("adminUtils.php");

if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
$today = date('Y-m-d');
$condition = " SUBSTRING(PD.datetime,1,10)='".$today."' ";
function plant_sort($a, $b) {
    if($a==$b) return $a;
}
$count = 1;
$plantnamearray = array();
$plant_name_array=array();
$loose_rate_ton_array=array();
$product_group_name_array=array();
$product_group_code_array=array();
$pd_date_array=array();
$pd_time_array=array();
$pd_datetime_array=array();

$sqlproddetails="SELECT prod_code,prod_desc,dns_prod_code FROM product_master WHERE dns_prod_code IN(SELECT DISTINCT prod_code FROM loose_oilrate_formulation WHERE oils like 'MCX%' AND prod_code IN(SELECT DISTINCT mapped_prod_code FROM product_unit_coversion_matrix WHERE acedns='Y')) ORDER BY prod_desc ASC";
$rsproddetails=mysql_query($sqlproddetails);
$total_rows=mysql_num_rows($rsproddetails);
if($total_rows>0){
	?>
    <form name="frm_releaseprice" method="post" action=""/>
    <input type="hidden" name="mode" value="publishrate"/>
    <input type="hidden" name="prod_code" value=""/>
    <table border="1" style="border-collapse:collapse;" class="border" width="80%" cellpadding="4" align="center">
       <tr class="TDHEAD" align="center" id="head_main"><td colspan="8">Release MCX Rate</td></tr>
      <tr class="TDHEAD_SUB" align="center" id="head_main">
      	<td width="8%">SI</td>
        <td width="10%">Product Code</td>
        <td width="">Description</td>
        <td width="10%">Previous Published Rate Open</td>
        <td width="10%">Previous Published Rate Close</td>
        <td width="10%">Current Rate Open</td>
        <td width="10%">Current Rate Close</td>
        <td width="23%">Publish Rate</td>
      </tr>
    <?php
	while($rowproddetails=mysql_fetch_array($rsproddetails))
	{
		$prod_code=$rowproddetails['prod_code'];
		$prod_desc=$rowproddetails['prod_desc'];
		$dns_prod_code=$rowproddetails['dns_prod_code'];
		
		$sqlpreviousrate="SELECT sale_rate_open,sale_rate_close FROM mcx_rate WHERE release_date !='0000-00-00 00:00:00' 
						AND acedns='Y' AND product_code='".$prod_code."'";
		$rspreviousrate=mysql_query($sqlpreviousrate);
		$rowpreviousrate=mysql_fetch_array($rspreviousrate);
		$previous_rate_open=$rowpreviousrate['sale_rate_open'];
		$previous_rate_close=$rowpreviousrate['sale_rate_close'];
		
		$sqlcurrentrate="SELECT sale_rate_open,sale_rate_close FROM mcx_rate WHERE release_date ='0000-00-00 00:00:00' AND 
						acedns='N' AND product_code='".$prod_code."' ORDER BY download_time DESC LIMIT 0,1";
		$rscurrentrate=mysql_query($sqlcurrentrate);
		$rowcurrentrate=mysql_fetch_array($rscurrentrate);
		$current_rate_open=$rowcurrentrate['sale_rate_open'];
		$current_rate_close=$rowcurrentrate['sale_rate_close'];
		
		if($previous_rate_open >0 || $current_rate_open >0 || $previous_rate_close >0 || $current_rate_close >0)
		{
			$radioname="buttonrate_$count";
		echo "<input type=\"hidden\" name=\"prod_val[]\" value=".$prod_code."><input type=\"hidden\" name=\"dns_prod_val[]\" value=".$dns_prod_code."><tr id=\"tab".$count."\">
				<td>".$count."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".number_format($previous_rate_open,2)."</td>
				<td align=\"right\">".number_format($previous_rate_close,2)."</td>
				<td align=\"right\">".number_format($current_rate_open,2)."</td>
				<td align=\"right\">".number_format($current_rate_close,2)."</td>
				<td ><input type=\"radio\" name=\"buttonrate_$count\" value=\"buttonprev_$count\" >PREVIOUS
				<input type=\"radio\" name=\"buttonrate_$count\" value=\"buttoncurr_$count\">CURRENT
				</td>
			  </tr>";
		$count++;
		}
	}
	?>
    <tr><td colspan="7" align="center">&nbsp;</td><td align="center"><input type="submit" name="submit1" value="Publish Rate" /></td></tr>
 <script language="javascript" type="text/javascript">
function priceprev_confirmation(prod_code)
{
	document.frm_releaseprice.mode.value='releaseprevrate';
	document.frm_releaseprice.prod_code.value=prod_code;
	document.frm_releaseprice.submit();
}
function pricecurr_confirmation(prod_code)
{
	document.frm_releaseprice.mode.value='releasecurrrate';
	document.frm_releaseprice.prod_code.value=prod_code;
	document.frm_releaseprice.submit();
}
</script>
<?php  
}
else{
	echo "<tr><td align=\"center\"><strong><font color=\"red\">No records found</font></strong></td></tr>";
}
echo "</table></form>";  

if($_REQUEST['mode']=='publishrate'){
	$prod_code=$_REQUEST['prod_val'];
	$dns_prod_code=$_REQUEST['dns_prod_val'];
	
	for($i=1;$i<=count($prod_code);$i++)
	{
	  $rateval=$_REQUEST["buttonrate_$i"];
	  if(substr($rateval,0,10)=='buttonprev'){
	  $sqlupdatemrpprevious="UPDATE mcx_rate SET release_date=CURRENT_TIMESTAMP()
							WHERE product_code='".$prod_code[$i-1]."' AND acedns='Y'";
	   if(mysql_query($sqlupdatemrpprevious)){
		   $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
						  			 mapped_prod_code='".$dns_prod_code[$i-1]."' AND acedns='Y' AND mapped_prod_code!=prod_code";
		   $rsfetchdependentprod=mysql_query($sqlfetchdependentprod);
		   while($rowfetchdependentprod=mysql_fetch_array($rsfetchdependentprod))
		   {
			  $dependent_prod_val=$rowfetchdependentprod['prod_code'];
			  $sqlconversiondependent="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
			  $rsconversiondependent=mysql_query($sqlconversiondependent);
			  $rowconversiondependent=mysql_fetch_array($rsconversiondependent);
			  ${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
			  ${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];
			  $sqlupdatemrpprevdependent="UPDATE mcx_rate SET release_date=CURRENT_TIMESTAMP()
							WHERE product_code='".${prod_code.$dependent_prod_val}."' AND acedns='Y'";
			  mysql_query($sqlupdatemrpprevdependent);
			  
	      }
	   }
      }
	  if(substr($rateval,0,10)=='buttoncurr'){
		  $sqlupdateprevrate="UPDATE mcx_rate SET acedns='N' WHERE product_code='".$prod_code[$i-1]."'";
		  mysql_query($sqlupdateprevrate);
		  $sqlupdatemrpcurrent="UPDATE mcx_rate SET release_date=CURRENT_TIMESTAMP(),acedns='Y'
								WHERE product_code='".$prod_code[$i-1]."' AND acedns='N' AND 
								release_date='0000-00-00 00:00:00'";
		  if(mysql_query($sqlupdatemrpcurrent))
		  {
		   $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
								 mapped_prod_code='".$dns_prod_code[$i-1]."' AND acedns='Y' AND mapped_prod_code!=prod_code";
		   $rsfetchdependentprod=mysql_query($sqlfetchdependentprod);
		   while($rowfetchdependentprod=mysql_fetch_array($rsfetchdependentprod))
		   {
			  $dependent_prod_val=$rowfetchdependentprod['prod_code'];
			  $sqlconversiondependent="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
			  $rsconversiondependent=mysql_query($sqlconversiondependent);
			  $rowconversiondependent=mysql_fetch_array($rsconversiondependent);
			  ${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
			  ${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];

			  $sqlupdateprevratedependent="UPDATE mcx_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."' AND 
			  								release_date!='0000-00-00 00:00:00'";
		 	  mysql_query($sqlupdateprevratedependent);
			  $sqlupdatemrpcurrdependent="UPDATE mcx_rate SET release_date=CURRENT_TIMESTAMP(),acedns='Y'
								WHERE product_code='".${prod_code.$dependent_prod_val}."' AND acedns='N' AND 
								release_date='0000-00-00 00:00:00'";
			  mysql_query($sqlupdatemrpcurrdependent);
			  
		    }
		  }
	   }
	  $flg=1;
    }
	//exit();
	if($flg==1){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$year.$month.$date.$hour.$minute.$second;
		$notification_type='Broadcast';
		$apiKey='AAAAdCu4Fjw:APA91bHHl7RnWyOj4Pb42NBuPfJZQAkOlmxKCoGL9flYk8xfhsqMY7_YtOtBKXPHNgx5szKyD2T1HriSFZ5NHmcBu874v9uCym0VEQlpNYbuUOjHBUmaVvtIXXYu-FOjhuwUksA4Geob';
		$collapseKey=rand();
		$notification_id='PN'.strtoupper($_SESSION['admin_login']).$location_date;
		$registration_id_array=array();
		$emp_code_array=array();
		$notificatiomessage="Hi,\nMCX Published rate details of $date-$month-$year are \n";
		$sqlemdetails="SELECT registrationid,emp_code FROM changepassword WHERE emp_code='E0040'";
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
		$sqlfetchflashprod="SELECT PM.prod_code,PCM.flash_name,PM.prod_desc FROM product_unit_coversion_matrix PCM,product_master PM 
							WHERE PM.dns_prod_code=PCM.prod_code AND PCM.acedns='Y' AND PCM.is_flash='Y'";
		$rsfetchflashprod=mysql_query($sqlfetchflashprod);
		while($rowfetchflashprod=mysql_fetch_array($rsfetchflashprod))
		 {
			$flash_name=$rowfetchflashprod['flash_name'];
			$prod_desc=$rowfetchflashprod['prod_desc'];
			$prod_code=$rowfetchflashprod['prod_code'];
			$sqlselpublishrate="SELECT sale_rate_open,sale_rate_close FROM mcx_rate WHERE product_code='".$prod_code."' AND acedns='Y'";
			$rsselpublishrate=mysql_query($sqlselpublishrate);
			$rowselpublishrate=mysql_fetch_array($rsselpublishrate);
		    $notificatiomessage.= $flash_name.'-'.$rowselpublishrate['sale_rate_open'].'-'.$rowselpublishrate['sale_rate_close']."\n"; 
		  }
	    //For sending notification
		$title = "";
		$notificatiomessage.=" THANKS,\nASL";
		
		//exit();

		//$message=$notificatiomessage." THANKS,\nVCONNECT";
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $notificatiomessage);
		
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => strtoupper($_SESSION['admin_login']), 'body' => $notificatiomessage); 
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$sqlnotificationmaster  = "INSERT INTO notification_master ";
		$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
		$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
		$sqlnotificationmaster .= " ,sender_id='".strtoupper($_SESSION['admin_login'])."'";
		$sqlnotificationmaster .= " ,message='".addslashes($notificatiomessage)."'";
		$sqlnotificationmaster .= " ,transferred='YES'";

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
		mysql_query($sqlnotificationmaster) or die(mysql_error()." Error in notification insertion.");
	 //End notification 
	?>
	 <script language="JavaScript" type="text/javascript">
		   window.location.href='publiashed_rate_details_MCX.php';</script>
	<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('MCX Rate publish unsuccessful.');window.
			location.href='generated_price_release_MCX.php';</script>
	<?php   }

	}
mysql_close($link);
}?>