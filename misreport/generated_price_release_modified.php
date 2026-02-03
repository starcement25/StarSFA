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
$dateprevious=date('Y-m-d', strtotime("-15 days,$curdateserver "));	

$sqlproddetails="SELECT PM.prod_code,PM.prod_desc,PM.dns_prod_code,PM.product_sub_group_code,
				PSGM.product_sub_group_name 
				FROM product_master PM,product_sub_group_master PSGM WHERE 
				PM.product_sub_group_code=PSGM.product_sub_group_code AND PM.dns_prod_code IN(SELECT DISTINCT prod_code 
				FROM loose_oilrate_formulation ) AND PM.dns_prod_code IN(SELECT DISTINCT mapped_prod_code
				FROM product_unit_coversion_matrix WHERE is_flash='Y'  ) ORDER BY PSGM.product_sub_group_name ASC,PM.prod_desc ASC";
$rsproddetails=mysqli_query($link,$sqlproddetails);
$total_rows=mysqli_num_rows($rsproddetails);
$product_sub_group_name_array=array();
if($total_rows>0){
	?>
    <form name="frm_releaseprice" method="post" action=""/>
    <input type="hidden" name="mode" value=""/>
    <input type="hidden" name="prod_code" value=""/>
    <table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center">
       <tr class="TDHEAD" align="center" id="head_main"><td colspan="6">Release Rate</td></tr>
      <tr class="TDHEAD_SUB" align="center" id="head_main">
      	<td>SI</td>
        <td>Product Code</td>
        <td>Description</td>
        <td>Previous Published Rate</td>
        <td>Current Rate</td>
		<td>Difference</td>
      </tr>
    <?php
	while($rowproddetails=mysqli_fetch_assoc($rsproddetails))
	{
		$prod_code=$rowproddetails['prod_code'];
		$prod_desc=$rowproddetails['prod_desc'];
		$dns_prod_code=$rowproddetails['dns_prod_code'];
		$prod_subgroup_nospace=str_replace(" ","",$rowproddetails['product_sub_group_name']);
		if($_REQUEST['mode']=='displayprevrate'){
			//print_r($_REQUEST);
			//echo 'dfdfdffdfdfdf'.$_REQUEST["release_date_$prod_subgroup_nospace"];
			if($_REQUEST["release_date_$prod_subgroup_nospace"]!='')
			{
				$sqlpreviousrate="SELECT sale_rate FROM sauda_mrp WHERE release_date !='0000-00-00 00:00:00' 
									AND product_code='".$prod_code."' AND 
									release_date='".$_REQUEST["release_date_$prod_subgroup_nospace"]."'";
			}
			else
			{
				$sqlpreviousrate="SELECT sale_rate FROM sauda_mrp WHERE release_date !='0000-00-00 00:00:00' AND acedns='Y' AND product_code='".$prod_code."'";
			}
			$rspreviousrate=mysqli_query($link,$sqlpreviousrate);
			$rowpreviousrate=mysqli_fetch_assoc($rspreviousrate);
			$previous_rate=$rowpreviousrate['sale_rate'];
		}
		else
		{
		$sqlpreviousrate="SELECT sale_rate FROM sauda_mrp WHERE release_date !='0000-00-00 00:00:00' AND acedns='Y' AND product_code='".$prod_code."'";
		$rspreviousrate=mysqli_query($link,$sqlpreviousrate);
		$rowpreviousrate=mysqli_fetch_assoc($rspreviousrate);
		$previous_rate=$rowpreviousrate['sale_rate'];
		}
		
		$sqlcurrentrate="SELECT sale_rate FROM sauda_mrp WHERE release_date ='0000-00-00 00:00:00' AND 
						acedns='N' AND product_code='".$prod_code."' ORDER BY download_time DESC LIMIT 0,1";
		$rscurrentrate=mysqli_query($link,$sqlcurrentrate);
		$rowcurrentrate=mysqli_fetch_assoc($rscurrentrate);
		$current_rate=$rowcurrentrate['sale_rate'];
		
		if($previous_rate >0 || $current_rate >0)
		{
			if(!in_array($rowproddetails['product_sub_group_name'],$product_sub_group_name_array))
			{
				if($_REQUEST["release_date_$prod_subgroup_nospace"]!='')
					{
						$display_none='';
					}
					else
					{
						$display_none='none';
					}
				$dropdownval="<select name=\"release_date_$prod_subgroup_nospace\" id=\"release_date_$rowproddetails[product_sub_group_name]\" style=\"display:$display_none\" onchange=\"javascript:display_previous_rate()\">";
				$dropdownval.="<option value=''>SELECT RATE</option>";
				$sqlquery="SELECT DISTINCT SM.release_date FROM sauda_mrp SM,product_master PM,product_sub_group_master PSGM 
							WHERE SM.product_code=PM.prod_code AND 
							PM.product_sub_group_code=PSGM.product_sub_group_code AND SM.release_date!='0000-00-00 00:00:00' 
							AND SUBSTRING(SM.release_date,1,10) >='".$dateprevious."' GROUP BY PSGM.product_sub_group_name,SM.release_date 
							ORDER BY SUBSTRING(SM.release_date,1,19) DESC";
				$result = mysqli_query($link,$sqlquery);
				while($rowprice = mysqli_fetch_assoc($result))
				{
					if($_REQUEST["release_date_$prod_subgroup_nospace"]==$rowprice['release_date'])
					{
						$selected='selected';
					}
					else
					{
						$selected='';
					}
					$dropdownval.="<option value='".$rowprice['release_date']."'".$selected.">".$rowprice['release_date']."</option>";
				}
				$dropdownval.='</select>';
				if($_REQUEST["release_date_$prod_subgroup_nospace"]!='')
					{
						$prev_checked='checked';
						$curr_checked='';
					}
					else
					{
						$prev_checked='';
						$rateval=$_REQUEST["buttonrate_$prod_subgroup_nospace"];
					    //exit();
					    if(substr($rateval,0,10)=='buttoncurr')
						{
							$curr_checked='checked';
						}
					}
				
				echo "<tr id=\"productsubgrouptab".$count."\">
				<td>&nbsp;</td>
				<td colspan=\"2\" style=\"border-left-style:hidden\"><b>".$rowproddetails['product_sub_group_name']."</b</td>
				<td colspan=\"3\" style=\"padding-left:15px;border-left-style:hidden;\" >
				<b><input type=\"radio\" name=\"buttonrate_$prod_subgroup_nospace\" value=\"buttonprev_$rowproddetails[product_sub_group_name]\" onchange=\"javascript:previous_date('".$rowproddetails['product_sub_group_name']."');\" ".$prev_checked.">PREVIOUS &nbsp;".$dropdownval."
				<input type=\"radio\" name=\"buttonrate_$prod_subgroup_nospace\" value=\"buttoncurr_$rowproddetails[product_sub_group_name]\" onchange=\"javascript:current_date('".$rowproddetails['product_sub_group_name']."');\" ".$curr_checked.">CURRENT</b></td>
			  </tr>";
			  array_push($product_sub_group_name_array,$rowproddetails['product_sub_group_name']);
			}
			$radioname="buttonrate_$count";
		echo "<input type=\"hidden\" name=\"prod_val[]\" value='".$prod_code."'>
		<input type=\"hidden\" name=\"dns_prod_val[]\" value='".$dns_prod_code."'><tr id=\"tab".$count."\"><input type=\"hidden\" name=\"product_sub_group_name[]\" value='".$rowproddetails['product_sub_group_name']."'>
				<td>".$count."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".number_format($previous_rate,2)."</td>
				<td align=\"right\">".number_format($current_rate,2)."</td>
				<td align=\"right\">".number_format(($current_rate-$previous_rate),2)."</td>
			  </tr>";
		$count++;
		}
	}
	?>
    <tr><td style="border-left-style:hidden;">&nbsp;</td>
				<td style="border-left-style:hidden;">&nbsp;</td>
				<td style="border-left-style:hidden;">&nbsp;</td><td colspan="3" align="left" style="border-left-style:hidden;padding-left:15px;"><input type="button" name="submit1" value="Publish Rate" onclick="submit_rate_release();"/></td></tr>
 </table></form>             
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
function previous_date(product_sub_group_name)
{
	document.getElementById('release_date_'+product_sub_group_name).style.display='';
}
function current_date(product_sub_group_name)
{
	document.getElementById('release_date_'+product_sub_group_name).style.display='none';
}
function submit_rate_release()
{
	document.frm_releaseprice.mode.value='publishrate';
	document.frm_releaseprice.submit();
}
function display_previous_rate()
{
	document.frm_releaseprice.mode.value='displayprevrate';
	document.frm_releaseprice.submit();
}
</script>
<?php  
}
else{
	echo "<tr><td align=\"center\"><strong><font color=\"red\">No records found</font></strong></td></tr>";
}
//echo "</table></form>";  

if($_REQUEST['mode']=='publishrate'){
	$product_sub_group_name=$_REQUEST['product_sub_group_name'];
	//print_r($product_sub_group_name);
	//exit();
	$prod_code=$_REQUEST['prod_val'];
	$dns_prod_code=$_REQUEST['dns_prod_val'];
	//$prod_sub_group_code=$_REQUEST['prod_sub_group_code'];
	$sqllatestcreatedate="select MAX(create_date) AS max_create_date FROM sauda_mrp";
	$rslatestcreatedate=mysqli_query($link,$sqllatestcreatedate);
	$rowlatestcreatedate=mysqli_fetch_assoc($rslatestcreatedate);
	$latest_create_date=$rowlatestcreatedate['max_create_date'];
	$latest_create_date_day=substr($latest_create_date,0,10);
	$latest_create_date_final=substr($latest_create_date,0,16);
	
	$sqlprevcreatedate="select MAX(create_date) AS max_create_date_prev FROM sauda_mrp where create_date < '".$latest_create_date."'";
	$rsprevcreatedate=mysqli_query($link,$sqlprevcreatedate);
	$rowprevcreatedate=mysqli_fetch_assoc($rsprevcreatedate);
	$prev_create_date=$rowprevcreatedate['max_create_date_prev'];
	//$latest_create_date_day=substr($latest_create_date,0,10);
	$prev_create_date_final=substr($prev_create_date,0,16);
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$price_release_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
	//print_r($_POST);
	//exit();
	for($i=0;$i< count($prod_code);$i++)
	{
	   $prod_subgroup_nospace=str_replace(" ","",$product_sub_group_name[$i]);
	   $rateval=$_REQUEST["buttonrate_$prod_subgroup_nospace"];
	   $rateval_array=explode('_',$rateval);
	   $release_date_val=$_REQUEST["release_date_$prod_subgroup_nospace"];
	  //exit();
	  if(substr($rateval,0,10)=='buttonprev' && $release_date_val!=''){
		 $sqlupdateprevrate="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".$prod_code[$i]."'";
		  mysqli_query($link,$sqlupdateprevrate);
		/*$sqlupdatemrpprev="UPDATE sauda_mrp SET release_date='".$price_release_date."',acedns='Y'
								WHERE product_code='".$prod_code[$i-1]."' AND acedns='N' AND 
								create_date='".$prev_create_date."'"; 
	  	   $sqlupdatemrpprevious="UPDATE sauda_mrp SET release_date=CURRENT_TIMESTAMP()
							WHERE product_code='".$prod_code[$i-1]."' AND acedns='Y'";*/
			$sqlupdatemrpprev="UPDATE sauda_mrp SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$_SESSION['admin_login']."' 
							WHERE product_code='".$prod_code[$i]."' AND release_date='".$release_date_val."'"; 				
	   		if(mysqli_query($link,$sqlupdatemrpprev)){
		   $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
						  			 mapped_prod_code='".$dns_prod_code[$i]."' AND acedns='Y' AND mapped_prod_code!=prod_code";
		   $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
		   while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
		   {
			  $dependent_prod_val=$rowfetchdependentprod['prod_code'];
			  $sqlconversiondependent="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
			  $rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
			  $rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
			  ${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
			  ${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];
			   $sqlupdateprevratedependent="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	  mysqli_query($link,$sqlupdateprevratedependent);
			  $sqlupdatemrpprevdependent="UPDATE sauda_mrp SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$_SESSION['admin_login']."'
							WHERE product_code='".${prod_code.$dependent_prod_val}."' AND release_date='".$release_date_val."'";
			  mysqli_query($link,$sqlupdatemrpprevdependent);
			   $sqlupdateindustrialratedependent="UPDATE industrial_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	  mysqli_query($link,$sqlupdateindustrialratedependent);
			  
			  $sqlupdateindustrialprevdependent="UPDATE industrial_rate SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),
			  									published_by='".$_SESSION['admin_login']."'
												WHERE product_code='".${prod_code.$dependent_prod_val}."' AND release_date='".$release_date_val."'";
			  mysqli_query($link,$sqlupdateindustrialprevdependent); 
	      }
	   }
      }
	  if(substr($rateval,0,10)=='buttoncurr'){
		  $sqlupdateprevrate="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".$prod_code[$i]."'";
		  mysqli_query($link,$sqlupdateprevrate);
		  /*$sqlupdatemrpcurrent="UPDATE sauda_mrp SET release_date=CURRENT_TIMESTAMP(),acedns='Y'
								WHERE product_code='".$prod_code[$i-1]."' AND acedns='N' AND 
								release_date='0000-00-00 00:00:00'";*/
		   $sqlupdatemrpcurrent="UPDATE sauda_mrp SET release_date='".$price_release_date."',acedns='Y',
		   						publish_date=CURRENT_TIMESTAMP(),published_by='".$_SESSION['admin_login']."' 
								WHERE product_code='".$prod_code[$i]."' AND acedns='N' AND 
								SUBSTRING(create_date,1,16)='".$latest_create_date_final."'";
			//exit();											
		  if(mysqli_query($link,$sqlupdatemrpcurrent))
		  {
		   $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
								 mapped_prod_code='".$dns_prod_code[$i]."' AND acedns='Y' AND mapped_prod_code!=prod_code";
		   $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
		   while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
		   {
			  $dependent_prod_val=$rowfetchdependentprod['prod_code'];
			  $sqlconversiondependent="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
			  $rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
			  $rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
			  ${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
			  ${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];

			  $sqlupdateprevratedependent="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	  mysqli_query($link,$sqlupdateprevratedependent);
			  $sqlupdateindustrialratedependent="UPDATE industrial_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	  mysqli_query($link,$sqlupdateindustrialratedependent);
			 $sqlupdatemrpcurrdependent="UPDATE sauda_mrp SET release_date='".$price_release_date."',acedns='Y',
			 							publish_date=CURRENT_TIMESTAMP(),published_by='".$_SESSION['admin_login']."' 
										WHERE product_code='".${prod_code.$dependent_prod_val}."' AND acedns='N' AND 
								SUBSTRING(create_date,1,16)='".$latest_create_date_final."'";
			  mysqli_query($link,$sqlupdatemrpcurrdependent);
			$sqlupdateindustrialcurrdependent="UPDATE industrial_rate SET release_date='".$price_release_date."',acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$_SESSION['admin_login']."' 
						WHERE product_code='".${prod_code.$dependent_prod_val}."' AND acedns='N' AND 
								SUBSTRING(create_date,1,16)='".$latest_create_date_final."'";
			  mysqli_query($link,$sqlupdateindustrialcurrdependent); 
		    }
		  }
	   }
	  $flg=1;
    }
	$sqlselduplicate="SELECT product_code FROM sauda_mrp where acedns='Y' AND 
					release_date='".$price_release_date."' GROUP BY product_code HAVING count(product_code) > 1 ";
	$rsselduplicate=mysqli_query($link,$sqlselduplicate);
	while($rowselduplicate=mysqli_fetch_assoc($rsselduplicate))
	{
		$prod_code_duplicate=$rowselduplicate['product_code'];
		$sqlupdateparentrate="UPDATE sauda_mrp SET acedns='N'
								WHERE product_code='".$prod_code_duplicate."' AND parent_child='parent' 
								AND release_date='".$price_release_date."'";
		mysqli_query($link,$sqlupdateparentrate);
		$sqlupdatechildrate="UPDATE sauda_mrp SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$_SESSION['admin_login']."' 
								WHERE product_code='".$prod_code_duplicate."' AND parent_child='child' 
								AND release_date='".$price_release_date."'";
		mysqli_query($link,$sqlupdatechildrate);						
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
		$notificatiomessage="Hi,\nPublished rate details of $date-$month-$year are \n";
		$sqlemdetails="SELECT registrationid,emp_code FROM changepassword WHERE emp_code='E0040'";
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
		$sqlfetchflashprod="SELECT PM.prod_code,PCM.flash_name,PM.prod_desc FROM product_unit_coversion_matrix PCM,product_master PM 
							WHERE PM.dns_prod_code=PCM.prod_code AND PCM.acedns='Y' AND PCM.is_flash='Y'";
		$rsfetchflashprod=mysqli_query($link,$sqlfetchflashprod);
		while($rowfetchflashprod=mysqli_fetch_assoc($rsfetchflashprod))
		 {
			$flash_name=$rowfetchflashprod['flash_name'];
			$prod_desc=$rowfetchflashprod['prod_desc'];
			$prod_code=$rowfetchflashprod['prod_code'];
			$sqlselpublishrate="SELECT sale_rate FROM sauda_mrp WHERE product_code='".$prod_code."' AND acedns='Y'";
			$rsselpublishrate=mysqli_query($link,$sqlselpublishrate);
			$rowselpublishrate=mysqli_fetch_assoc($rsselpublishrate);
		    $notificatiomessage.= $flash_name.'-'.$rowselpublishrate['sale_rate']."\n"; 
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
				mysqli_query($link,$sqlnotification) or die(mysqli_error()." Error in notification insertion.");
			}
		}
		mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");
	 //End notification 
	?>
	 <script language="JavaScript" type="text/javascript">
		   window.location.href='published_rate_details.php';</script>
	<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Previous rate release unsuccessful.');window.
			location.href='generated_price_release.php';</script>
	<?php   }

	}
if($_REQUEST['mode']=='generateprice'){
	$product_group_code=$_REQUEST['product_group_code'];
	$plant_name=$_REQUEST['plant_name'];
	$loose_rate_ton=$_REQUEST['loose_rate_ton'];
	$current_date=date('Y-m-d');
	//exit();
	//For formulation=yes
	if(strpos($loose_rate_ton,'#')!=false){
		$loose_rate_ton_split=explode('#',$loose_rate_ton);
		$formulation_prod_array=array();
		foreach($loose_rate_ton_split as $oils_val)
		{
			//$loose_rate_ton_final=$loose_rate_ton_final.$loose_rate_ton_val.'<br />';
			$oils_val_split=explode(":",$oils_val);
			$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
										WHERE product_group_code='".$product_group_code."' AND plant_name='".$plant_name."' 
										AND oils='".$oils_val_split[0]."' ORDER BY datetime DESC) AS SAT GROUP BY 1 ";
			$rsprodwiseformulation=mysqli_query($link,$sqlprodwiseformulation);
			while($rowprodwiseformulation=mysqli_fetch_assoc($rsprodwiseformulation))
			{	/*echo 	$rowprodwiseformulation['prod_code'];
				echo '<br />';
				echo 'loose rate input-'.$oils_val_split[1];
				echo '<br />';
				echo 'formulation-'.$rowprodwiseformulation['formulation'];
				echo '<br />';*/			
				 ${loosrate_calc_val.$rowprodwiseformulation['prod_code']}=(substr($rowprodwiseformulation['formulation'],0,-1)
				*$oils_val_split[1])/100;
				
				${loosrate_final_val.$rowprodwiseformulation['prod_code']}=${loosrate_final_val.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val.$rowprodwiseformulation['prod_code']};
				if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
				{
					array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
				}
			}
		}
		foreach($formulation_prod_array as $formulation_prod_val)
		{
			//echo $formulation_prod_val.'<br />';
			$sqlprocesscost="SELECT process_cost FROM process_cost WHERE dns_prod_code='".$formulation_prod_val."' 
							AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
			$rsprocesscost=mysqli_query($link,$sqlprocesscost);
			$rowprocesscost=mysqli_fetch_assoc($rsprocesscost);
			${process_cost.$formulation_prod_val}=$rowprocesscost['process_cost'];
			if(${process_cost.$formulation_prod_val}=='') ${process_cost.$formulation_prod_val}=0;
			${loosrate_final_val.$formulation_prod_val};
			//echo 'final loose rate  - <br />';
			${loosrate_total.$formulation_prod_val}=${loosrate_final_val.$formulation_prod_val}+${process_cost.$formulation_prod_val};
			//${loosrate_total.$product_group_code}=${loosrate_total.$product_group_code}+${loosrate_total.$formulation_prod_val};
		}
		//For product group wise all product data mrp updation on the basis of Loose rate
		$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."' 
									AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master 
									WHERE plant_name='".$plant_name."' AND acedns='Y')";
		$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
		while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
		{
			$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
			$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
			$rsbranchname=mysqli_query($link,$sqlbranchname);
			$rowbranchname=mysqli_fetch_assoc($rsbranchname);
			$branch_name=$rowbranchname['branch_name'];
			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
										product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master 
										WHERE plant_name='".$plant_name."' AND acedns='Y')";
			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
			{
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,branch_code FROM product_master WHERE 
									dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."' 
									AND product_group_code='".$product_group_code."' AND acedns='Y'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
				${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
				${prod_desc.$distinct_dnsprod_code}=$rowconversionfactor['prod_desc'];
				${distinct_branch_code.$distinct_dnsprod_code}=$rowconversionfactor['branch_code'];
				$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' 
									AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
				$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
				${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
				${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
				$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
											AND branch_code='".$distinct_branch_code."' AND 
											vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
				$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
				$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
				${depot_cost.$distinct_dnsprod_code}=$rowdepotcostprodwise['depot_cost'];
				if(${depot_cost.$distinct_dnsprod_code}=='')
				{
					${depot_cost.$distinct_dnsprod_code}=0;
				}
				/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
											AND branch_code='".$distinct_branch_code."' AND 
											vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
				$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
				$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
				${margin_cost.$distinct_dnsprod_code}=$rowmargincostprodwise['margin_cost'];
				if(${margin_cost.$distinct_dnsprod_code}=='')
				{
					${margin_cost.$distinct_dnsprod_code}=0;
				}*/
				${margin_cost.$distinct_dnsprod_code}=0;
				$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
											AND branch_code='".$distinct_branch_code."' AND 
											vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
				$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
				$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
				${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
				if(${freight_cost.$distinct_dnsprod_code}=='')
				{
					${freight_cost.$distinct_dnsprod_code}=0;
				}
				/*$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$distinct_dnsprod_code."' 
											AND branch_code='".$distinct_branch_code."' AND 
											vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
				$rshoneycombcostprodwise=mysqli_query($link,$sqlhoneycombcostprodwise);
				$rowhoneycombcostprodwise=mysqli_fetch_assoc($rshoneycombcostprodwise);
				${honeycomb_cost.$distinct_dnsprod_code}=$rowhoneycombcostprodwise['honeycomb_cost'];
				if(${honeycomb_cost.$distinct_dnsprod_code}=='')
				{
					${honeycomb_cost.$distinct_dnsprod_code}=0;
				}*/
				${honeycomb_cost.$distinct_dnsprod_code}=0;
				$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$distinct_dnsprod_code."' 
											AND branch_code='".$distinct_branch_code."' AND 
											vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
				$rsdetentioncostprodwise=mysqli_query($link,$sqldetentioncostprodwise);
				$rowdetentioncostprodwise=mysqli_fetch_assoc($rsdetentioncostprodwise);
				${detention_cost.$distinct_dnsprod_code}=$rowdetentioncostprodwise['detention_cost'];
				if(${detention_cost.$distinct_dnsprod_code}=='')
				{
					${detention_cost.$distinct_dnsprod_code}=0;
				}
				
				//echo ${loosrate_total.$distinct_dnsprod_code};
				${loose_rate_case_prodwise.$distinct_dnsprod_code}=round((${loosrate_total.$distinct_dnsprod_code}/${conversion_factor_two.$distinct_dnsprod_code}),2);
				${loose_rate_case_prodwise.$distinct_dnsprod_code}=round((${loose_rate_case_prodwise.$distinct_dnsprod_code}*${conversion_factor.$distinct_dnsprod_code}),2);
				//exit();
				//${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${honeycomb_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${mrp_prodwise.$distinct_dnsprod_code}=round(${mrp_prodwise.$distinct_dnsprod_code},2);
				
				//${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${basic_rate_prodwise.$distinct_dnsprod_code}=round(${basic_rate_prodwise.$distinct_dnsprod_code},2);
				$primary_freight=round(${freight_cost.$distinct_dnsprod_code},2);
				$depot_cost=${depot_cost.$distinct_dnsprod_code};
				
				$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code."' AND 
								dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";
				$rs_prod_code=mysqli_query($link,$sql_prod_code);
				$cntprod_code=mysqli_num_rows($rs_prod_code);
				$row_prod_code=mysqli_fetch_assoc($rs_prod_code);
				$prod_code_master=$row_prod_code['prod_code'];
				$vertical_value_master=$row_prod_code['vertical_value'];
				if($cntprod_code >0)
				{
				  $sqlbranchprodchk="SELECT product_code FROM sauda_mrp WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
				  $rsbranchprodchk=mysqli_query($link,$sqlbranchprodchk);
				  $cntbranchprodchk=mysqli_num_rows($rsbranchprodchk);
				   if($cntbranchprodchk >0)						{
						$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='".${mrp_prodwise.$distinct_dnsprod_code}."',
											sale_rate='".${mrp_prodwise.$distinct_dnsprod_code}."',
											basic_rate='".${basic_rate_prodwise.$distinct_dnsprod_code}."',primary_freight	='".$primary_freight."',
											depot_cost='".$depot_cost."',download_time=CURRENT_TIMESTAMP()
										WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
						mysqli_query($link,$sqlupdatemrpprodwise);
					}
					else
						{
							$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
											AS max_mrp_code from sauda_mrp";
							$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
							$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
							$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
							$max_mrp_code++;
							$max_mrp_code='z'.$max_mrp_code;

						   $sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',
						   						mrp='".${mrp_prodwise.$distinct_dnsprod_code}."',sale_rate='".${mrp_prodwise.$distinct_dnsprod_code}."',
						   						branch_code='".$distinct_branch_code."',product_code='".$prod_code_master."',
												vertical_value='".$vertical_value_master."',
												basic_rate='".${basic_rate_prodwise.$distinct_dnsprod_code}."',
												primary_freight	='".$primary_freight."',depot_cost='".$depot_cost."',download_time=CURRENT_TIMESTAMP()";
						   mysqli_query($link,$sqlinsertmrpprodwise);
						}
					}
					$flag=1;
			}
		}
		if($flag==1){
				$sqlupdatepricegenflag="UPDATE pricing_detials_formulation SET price_generated='yes' WHERE product_group_code='".$product_group_code."' 
									AND plant_name='".$plant_name."' AND SUBSTRING(datetime,1,10)='".$current_date."'";
				mysqli_query($link,$sqlupdatepricegenflag);						
			?>
			   <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='product_groupwise_pricelist_new.php?product_group_code=<?php echo $product_group_code;?>&plant_name=<?php echo $plant_name;?>&formulation=yes';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Price generation unsuccessful.');window.location.href='generate_pricing_issue_to_released.php';</script>
            <?php
            }
	}// END of formulation=yes
	else // Fomulation=no
	{
		$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."' 
									AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master 
									WHERE plant_name='".$plant_name."' AND acedns='Y')";
		$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
		while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
		{
		$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
		//$tabledataval.="<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">";
		$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
		$rsbranchname=mysqli_query($link,$sqlbranchname);
		$rowbranchname=mysqli_fetch_assoc($rsbranchname);
		$branch_name=$rowbranchname['branch_name'];

		$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
									product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
									AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
		/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
									product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
									AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."'";	*/						
		$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
		while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
		{
			$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
			$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,branch_code FROM product_master WHERE 
								dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."' 
								AND product_group_code='".$product_group_code."' AND acedns='Y'";
			$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
			$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
			${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
			${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
			${prod_desc.$distinct_dnsprod_code}=$rowconversionfactor['prod_desc'];
			${branch_code.$distinct_dnsprod_code}=$rowconversionfactor['branch_code'];

			$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' 
								AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
			$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
			$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
			${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
			${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
			
			$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' AND 
										vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
			$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
			$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
			${depot_cost.$distinct_dnsprod_code}=$rowdepotcostprodwise['depot_cost'];
			if(${depot_cost.$distinct_dnsprod_code}=='')
			{
				${depot_cost.$distinct_dnsprod_code}=0;
			}
			/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' AND 
										vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
			$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
			$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);

			${margin_cost.$distinct_dnsprod_code}=$rowmargincostprodwise['margin_cost'];
			if(${margin_cost.$distinct_dnsprod_code}=='')
			{
				${margin_cost.$distinct_dnsprod_code}=0;
			}*/
			${margin_cost.$distinct_dnsprod_code}=0;
			$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' AND 
										vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
			$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
			$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
			${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
			if(${freight_cost.$distinct_dnsprod_code}=='')
			{
				${freight_cost.$distinct_dnsprod_code}=0;
			}
			
			/*$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
			$rshoneycombcostprodwise=mysqli_query($link,$sqlhoneycombcostprodwise);
			$rowhoneycombcostprodwise=mysqli_fetch_assoc($rshoneycombcostprodwise);
			${honeycomb_cost.$distinct_dnsprod_code}=$rowhoneycombcostprodwise['honeycomb_cost'];
			if(${honeycomb_cost.$distinct_dnsprod_code}=='')
			{
				${honeycomb_cost.$distinct_dnsprod_code}=0;
			}*/
			${honeycomb_cost.$distinct_dnsprod_code}=0;
			$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
			$rsdetentioncostprodwise=mysqli_query($link,$sqldetentioncostprodwise);
			$rowdetentioncostprodwise=mysqli_fetch_assoc($rsdetentioncostprodwise);
			${detention_cost.$distinct_dnsprod_code}=$rowdetentioncostprodwise['detention_cost'];
			if(${detention_cost.$distinct_dnsprod_code}=='')
			{
				${detention_cost.$distinct_dnsprod_code}=0;
			}
			${loose_rate_case_prodwise.$distinct_dnsprod_code}=round(($loose_rate_ton/${conversion_factor_two.$distinct_dnsprod_code}),2);
			${loose_rate_case_prodwise.$distinct_dnsprod_code}=round((${loose_rate_case_prodwise.$distinct_dnsprod_code}*${conversion_factor.$distinct_dnsprod_code}),2);
			//${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${honeycomb_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
			${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
			${mrp_prodwise.$distinct_dnsprod_code}=round(${mrp_prodwise.$distinct_dnsprod_code},2);
			//${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
			${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
			${basic_rate_prodwise.$distinct_dnsprod_code}=round(${basic_rate_prodwise.$distinct_dnsprod_code},2);
			$primary_freight=round(${freight_cost.$distinct_dnsprod_code},2);
			$depot_cost=${depot_cost.$distinct_dnsprod_code};
			
			$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code."' AND 
								dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";
			$rs_prod_code=mysqli_query($link,$sql_prod_code);
			$cntprod_code=mysqli_num_rows($rs_prod_code);
			$row_prod_code=mysqli_fetch_assoc($rs_prod_code);
			$prod_code_master=$row_prod_code['prod_code'];
			$vertical_value_master=$row_prod_code['vertical_value'];
			if($cntprod_code >0)
			{
			  $sqlbranchprodchk="SELECT product_code FROM sauda_mrp WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
			  $rsbranchprodchk=mysqli_query($link,$sqlbranchprodchk);
			  $cntbranchprodchk=mysqli_num_rows($rsbranchprodchk);
			   if($cntbranchprodchk >0){
					$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='".${mrp_prodwise.$distinct_dnsprod_code}."',
										sale_rate='".${mrp_prodwise.$distinct_dnsprod_code}."',
										basic_rate='".${basic_rate_prodwise.$distinct_dnsprod_code}."',primary_freight	='".$primary_freight."',
										depot_cost='".$depot_cost."',download_time=CURRENT_TIMESTAMP()
									WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
					mysqli_query($link,$sqlupdatemrpprodwise);
				}
				else
					{
						$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
										AS max_mrp_code from sauda_mrp";
						$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
						$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
						$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
						$max_mrp_code++;
						$max_mrp_code='z'.$max_mrp_code;

					   $sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',
											mrp='".${mrp_prodwise.$distinct_dnsprod_code}."',sale_rate='".${mrp_prodwise.$distinct_dnsprod_code}."',
											branch_code='".$distinct_branch_code."',product_code='".$prod_code_master."',
											vertical_value='".$vertical_value_master."',
											basic_rate='".${basic_rate_prodwise.$distinct_dnsprod_code}."',
											primary_freight	='".$primary_freight."',depot_cost='".$depot_cost."',download_time=CURRENT_TIMESTAMP()";
					   mysqli_query($link,$sqlinsertmrpprodwise);
					}
				}
			$flag=1;
		}
	  }
	  if($flag==1){
		  	$sqlupdatepricegenflag="UPDATE pricing_detials SET price_generated='yes' WHERE product_group_code='".$product_group_code."' 
									AND plant_name='".$plant_name."' AND SUBSTRING(datetime,1,10)='".$current_date."'";
			mysqli_query($link,$sqlupdatepricegenflag);						
		  ?>
			   <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='product_groupwise_pricelist_new.php?product_group_code=<?php echo $product_group_code;?>&plant_name=<?php echo $plant_name;?>';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Price generation unsuccessful.');window.
				location.href='generate_pricing_issue_to_released.php';</script>
            <?php
         }
	}// End of Formulation=no
}
mysqli_close($link);
}?>