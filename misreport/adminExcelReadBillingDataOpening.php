<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	require 'phpexcel/Classes/PHPExcel/IOFactory.php';
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$GLOBALS['show']=60;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
		$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	disphtml("main();");
	ob_end_flush();

function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
function main()
{
	if($_REQUEST['mode']=="submit_billing_information")
	{
		$current_date=date('Y-m-d');
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		$customer_name=$_POST['customer_name'];
		$customer_code_submitval=$_POST['customer_code'];
 		$invoice_no=$_POST['invoice_no'];
		$invoice_date=$_POST['invoice_date'];
		$prod_desc=$_POST['prod_desc'];
		$IMEI=$_POST['IMEI'];
		//$refference_no=$_POST['refference_no'];
		$customer_code_array=array();
		$customer_name_array=array();
		$registration_id_array=array();
		$emp_code_array=array();
		$prod_code_array=array();
		$prod_desc_array=array();
		//$notificatiomessage="Hi,\nToday's billing details are\n";
		for($i=0;$i<count($prod_desc);$i++)
		{	  
		    $invoice_date_final=date('Y-m-d',strtotime(str_replace('/','-',$invoice_date[$i])));
			
			$sqlselcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_submitval[$i])."'";
			$rsselcustomercode=mysqli_query($link,$sqlselcustomercode);
			$rowcustomercode=mysqli_fetch_assoc($rsselcustomercode);
			$customer_code=$rowcustomercode['customer_code'];
			$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".rtrim(addslashes($prod_desc[$i]))."'";
			$rsprodcode=mysqli_query($link,$sqlprodcode);
			$countprodcode=mysqli_num_rows($rsprodcode);
			$rowprodcode=mysqli_fetch_assoc($rsprodcode);
			$prod_code=$rowprodcode['prod_code'];
			if(!isset(${billcount.$customer_code.$prod_code}))
			{
				${billcount.$customer_code.$prod_code}=0;
			}
			
			//$notificatiomessage.=$prod_desc[$i].'-'.$IMEI[$i]."\n";
			//${notificationmessage.$customer_code}.=$prod_desc[$i].'-'.$IMEI[$i]."\n";
			if(!in_array($customer_code,$customer_code_array))
			{
				array_push($customer_code_array,$customer_code);
				array_push($customer_name_array,$customer_name[$i]); 
			}
		  $sqlinsert="INSERT INTO customer_product_billing SET customer_code='".$customer_code."',
					invoice_no='".$invoice_no[$i]."',invoice_date='".$invoice_date_final."',acedns='Y',download_time=CURRENT_TIMESTAMP(),
					prod_code='".$prod_code."',IMEI='".addslashes($IMEI[$i])."'";
		  mysqli_query($link,$sqlinsert);
		  ${billcount.$customer_code.$prod_code}=${billcount.$customer_code.$prod_code}+1;
		  if(!in_array($prod_code,$prod_code_array))
			{
				array_push($prod_code_array,$prod_code);
				array_push($prod_desc_array,$prod_desc[$i]);
			}
		}
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
	 for ($m=0;$m<count($customer_code_array);$m++)
	   {
		 for($n=0;$n<count($prod_code_array);$n++)
		 {
			 /*$sqlselstockbalancedetails="SELECT allocation_id,allocation_date,allocation_qty,requisition_id,requisition_date,requisition_qty 
										FROM stock_balance_details WHERE customer_code='".$customer_code_array[$m]."' AND 
										prod_code='".$prod_code_array[$n]."' AND active_flag='Y'";
			 $rsselstockbalancedetails=mysqli_query($link,$sqlselstockbalancedetails);
			 $rowselstockbalancedetails=mysqli_fetch_assoc($rsselstockbalancedetails);
			 $allocation_id=$rowselstockbalancedetails['allocation_id'];
			 $allocation_date=$rowselstockbalancedetails['allocation_date'];
			 $allocation_qty=$rowselstockbalancedetails['allocation_qty'];
			 $requisition_id=$rowselstockbalancedetails['requisition_id'];
			 $requisition_qty=$rowselstockbalancedetails['requisition_qty'];
			 $requisition_date=$rowselstockbalancedetails['requisition_date'];*/
			 $allocation_id='CA'.str_replace('/','#',$customer_code_array[$m]).$location_date;
			 $allocation_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			 $allocation_qty='0';
			 $requisition_id='';
			 $requisition_qty='0';
			 $requisition_date='0000-00-00 00:00:00';

			 $sqlinsertstockbalance="INSERT INTO stock_balance_details SET allocation_id='".$allocation_id."',customer_code='".$customer_code_array[$m]."',
									prod_code='".$prod_code_array[$n]."',allocation_qty='".$allocation_qty."',
									allocation_date='".$allocation_date."',requisition_id='".$requisition_id."',
									requisition_qty='".$requisition_qty."',requisition_date='".$requisition_date."',
									billed_qty='".${billcount.$customer_code_array[$m].$prod_code_array[$n]}."',billed_date='',
									download_time=CURRENT_TIMESTAMP()";
			  mysqli_query($link,$sqlinsertstockbalance);
			  ${notificationmessage.$customer_code_array[$m]}.=$prod_desc_array[$n].'-'.${billcount.$customer_code_array[$m].$prod_code_array[$n]}."\n";
		 }
		 $registration_id_array=array();
		 $emp_code_array=array();
		 //echo $registration_id_array[$m];
		//Title of the Notification.
		$notification_id='PN'.$customer_code_array[$m].$location_date;
		$sqlemdetails="SELECT CRR.emp_code,CH.registrationid,EM.reporting_to FROM customer_route_emp_relation CRR,changepassword CH,
						employee_master EM WHERE CRR.emp_code=CH.emp_code AND CH.emp_code=EM.emp_code AND CRR.customer_code='".$customer_code_array[$m]."'";
		$rsempdetails=mysqli_query($link,$sqlemdetails);
		while($rowempdetails=mysqli_fetch_assoc($rsempdetails))
		{
			$registrationid=$rowempdetails['registrationid'];
			$emp_code=$rowempdetails['emp_code'];
			$reporting_to=$rowempdetails['reporting_to'];
			if(!in_array($registrationid,$registration_id_array))
			{
				array_push($registration_id_array,$registrationid);
				array_push($emp_code_array,$emp_code);
			}
			$sqlregdetailsreportingto="SELECT registrationid FROM changepassword WHERE emp_code='".$reporting_to."'";
			$rsregdetailsreportingto=mysqli_query($link,$sqlregdetailsreportingto);
			$rowregdetailsreportingto=mysqli_fetch_assoc($rsregdetailsreportingto);
			$registrationidreportingto=$rowregdetailsreportingto['registrationid'];
			if(!in_array($registrationidreportingto,$registration_id_array))
			{
				array_push($registration_id_array,$registrationidreportingto);
				array_push($emp_code_array,$reporting_to);
			}
		}

		$title = "";
		$message="Hi,\n.".$customer_name_array[$m]." Today's billing are\n".${notificationmessage.$customer_code_array[$m]}." THANKS,\nVCONNECT";

		//$message=$notificatiomessage." THANKS,\nVCONNECT";
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $message);
		
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => strtoupper($_SESSION['admin_login']), 'body' => $message); 
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
			$sqlnotificationmaster  = "INSERT INTO notification_master ";
			$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
			$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
			$sqlnotificationmaster .= " ,sender_id='".strtoupper($_SESSION['admin_login'])."'";
			$sqlnotificationmaster .= " ,message='".addslashes($message)."'";
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
			if($successval==1)
			{
				mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");
			}
		}
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
?>
<script language="JavaScript">
function checkFields()
{
	if(document.form_add_xls.zip_file.value=="")
	{
		alert("Please browse the ZIP file first...");
		document.form_add_xls.zip_file.focus();
		return false;
	}
	
	var fname = document.form_add_xls.zip_file.value.toUpperCase();
	var pos1 = fname.indexOf(".ZIP");
	
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use ZIP only...");
		document.form_add_xls.zip_file.focus();
		return false;	
	}
	return true;	
}
</script>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" >
			<table width="70%" align="center" cellpadding="5" cellspacing="2">
            	 <tr> 
                    <td width="90%" align="center" class="ERR"><font size="+2"><u>Upload Billing Data</u></font></td>
            	</tr>
            </table>
          </td>
    </tr>       
    <tr> 
        <td height="30"  align="left">
        <table width="100%">
            <tr> 
                <td width="90%" align="center" class="ERR"><?=$GLOBALS['msg']?></td>
                <td width="" align="right"></td>
            </tr>
            <tr> 
                <td width="90%" align="center" class="ERR" nowrap="nowrap">
                <?php 
                $errr_msg=$GLOBALS['error_msg'];
                $error_msgArr=explode('#',$errr_msg);
                if(count($error_msgArr)>0){
                    for($i=0;$i<count($error_msgArr);$i++){
                        echo "<b>$error_msgArr[$i]</b><br /><br />";
                    }
                }
                ?>
                </td>
                <td width="" align="right"></td>
            </tr>
        </table></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
<table width="70%" align="center" cellpadding="5" cellspacing="2" class="border">
	<form name="form_add_xls" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="xls_upload">
		
		<tr class="TDHEAD" > 
			<td colspan="10">Upload Zip File</td>
		</tr>
			
		<tr> 
		  <td align="right">Zip File*</td>
			<td width="2%">:</td>
			<td><input type="file" name="zip_file" class="" ><br/ ><strong><font color="#FF0000">[Extension will be .zip]</font></strong></td>
		</tr>
		<tr>
            <td>&nbsp;</td>
            <td >&nbsp;</td>
            <td>		
                <input type="submit" name="Add" value="Add" > 
                <!--input type="button" name="back" value=" Back " onClick="javascript:document.location='adminMain.php'"-->
            </td>
		</tr>
		<tr class="TDHEAD_SUB"> 
			<td colspan="10">&nbsp;</td>
		</tr>
	</form>
</table>
</td>
</tr>
</table><br /><br /><br />
<?php
if($_REQUEST['mode']=="xls_upload"){
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	$error_array=array();
	if (!file_exists("../csv/$folderName")){
		mkdir("../csv/$folderName");
		chmod("../csv/$folderName", 0777);
	}
	// Get array of all source files
	$files = scandir("../csv/$folderName");
	// Identify directories
	$source = "../csv/$folderName/";
	$destination = "../csv/$folderName/filebkup/";
	// Cycle through all source files
	foreach ($files as $file) {
	  if (in_array($file, array(".",".."))) continue;
	  // If we copied this successfully, mark it for deletion
	  if (@copy($source.$file, $destination.$file)) {
		$delete[] = $source.$file;
	  }
	}
	// Delete all successfully-copied files
	foreach ($delete as $file) {
	  unlink($file);
	}
	$upload_dir="../csv/$folderName/";
	if(file_exists($_FILES['zip_file']['tmp_name']))
	{
		$file_name = $_FILES['zip_file']['name'];
		$tmp_name=$_FILES['zip_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
	    move_uploaded_file($tmp_name,$upload_file);
		$zip = new ZipArchive;
		if ($zip->open($upload_file)) {
			$zip->extractTo("../csv/$folderName/");
			$zip->close();
		} 
	 }
	  //For billing information xls
	   if(similar_file_exists("../csv/$folderName/billing information opening.xlsx")!=false || similar_file_exists("../csv/$folderName/billing information opening.xls")!=false)
	    {
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		//$lines = file($filename);
		if(similar_file_exists("../csv/$folderName/billing information opening.xlsx")!=false)
		{
			$inputfilename = "../csv/$folderName/billing information opening.xlsx";
		}
		if(similar_file_exists("../csv/$folderName/billing information opening.xls")!=false)
		{
			$inputfilename = "../csv/$folderName/billing information opening.xls";
		}
		$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
		$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
		$objPHPExcel = $objReader->load($inputfilename);
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
		  <tr class="TDHEAD" align="center" id="head_main">
			<td colspan="7" class="TDHEAD" align="center">Billing Information</td>
		  </tr>
		  <tr class="TDHEAD_SUB" align="center" id="head_main">
		   <td>SI</td><td>PARTY NAME</td><td>PARTY CODE</td><td>INVOICE NO</td><td>INVOICE DATE</td><td>MODEL NAME</td><td>IMEI</td></tr>';
				//  Get worksheet dimensions
				$sheet = $objPHPExcel->getSheet(0); 
				$highestRow = $sheet->getHighestRow(); 
				$highestColumn = $sheet->getHighestColumn();
				//  Loop through each row of the worksheet in turn
				for ($row = 1; $row <= $highestRow; $row++)
				{ 
					//  Read a row of data into an array
					$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
					//  Insert row data array into your database of choice here
					if($rec_count>=1)
					{ 
						$csv_row_count=$rec_count+1;
						$customer_name = trim($rowData[0][0]);
						$dns_customer_code = trim($rowData[0][1]);
						$invoice_no = trim($rowData[0][2]);
						//echo $invoice_date = trim($rowData[0][3]);
						$invoice_date_final = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(3, $row)->getValue()));
						//$invoice_date_final=date('Y-m-d',strtotime(str_replace('/','-',$invoice_date)));
						$prod_desc = trim($rowData[0][4]);
						$IMEI=$sheet->getCellByColumnAndRow(5,$row)->getFormattedValue();
						//$IMEI = trim($rowData[0][5]);
						
					    $sqlselcustomercode="SELECT CRR.customer_code FROM customer_master CM,customer_route_emp_relation CRR WHERE 
				  					CRR.customer_code=CM.customer_code AND CM.dns_customer_code='".addslashes($dns_customer_code)."' and CRR.acedns='Y'";
						$rsselcustomercode=mysqli_query($link,$sqlselcustomercode);
						$countselcustomercode=mysqli_num_rows($rsselcustomercode); 
						if($countselcustomercode==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (PARTY CODE)");
						}
						else
						{
							$rowselcustomercode=mysqli_fetch_assoc($rsselcustomercode);
							$customer_code=$rowselcustomercode['customer_code'];
						}
					  /*$sqlselcustomerdnscode="SELECT CRR.customer_code FROM customer_master CM,customer_route_emp_relation CRR WHERE 
										CRR.customer_code=CM.customer_code AND CM.dns_customer_code='".addslashes($customer_code)."' 
										AND CM.customer_name='".addslashes($customer_name)."' and CRR.acedns='Y'";
					  $rsselcustomerdnscode=mysqli_query($link,$sqlselcustomerdnscode);
					  $countselcustomerdnscode=mysqli_num_rows($rsselcustomerdnscode);
					  if($countselcustomerdnscode==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (PARTY CODE)");
						}*/
					  $sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".rtrim(addslashes($prod_desc))."' and acedns='Y'";
					  $rsprodcode=mysqli_query($link,$sqlprodcode);
					  $countprodcode=mysqli_num_rows($rsprodcode);
					  if($countprodcode==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (MODEL NAME)");
						}
						else
						{
							$rowprodcode=mysqli_fetch_assoc($rsprodcode);
							$prod_code=$rowprodcode['prod_code'];
						}
						if(!isset(${billcount.$customer_code.$prod_code}))
						{
							${billcount.$customer_code.$prod_code}=0;
						}
						/*$sqlchkactiveallocation="SELECT allocation_id,SUBSTRING(allocation_date,1,10) AS allocation_date FROM 
												stock_balance_details WHERE customer_code='".$customer_code."' 
												AND prod_code='".$prod_code."' AND active_flag='Y'";
						$rschkactiveallocation=mysqli_query($link,$sqlchkactiveallocation);
						$countchkactiveallocation=mysqli_num_rows($rschkactiveallocation);
						if($countchkactiveallocation==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (PARTY NAME AND MODEL NAME Have no active allocation)");
						}
						else
						{
							$rowchkactiveallocation=mysqli_fetch_assoc($rschkactiveallocation);
							$allocation_date=$rowchkactiveallocation['allocation_date'];
						}
						if($invoice_date_final < $allocation_date)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Bill date cannot be after Allocation date)");
						}
						$sqlchkactiveallocation="SELECT allocation_id FROM stock_balance_details WHERE customer_code='".$customer_code."' 
												AND prod_code='".$prod_code."' AND active_flag='Y'";
						$rschkactiveallocation=mysqli_query($link,$sqlchkactiveallocation);
						$countchkactiveallocation=mysqli_num_rows($rschkactiveallocation);
						if($countchkactiveallocation==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (PARTY NAME AND MODEL NAME Have no active allocation)");
						}
						$sqlchkrequisitionqty="SELECT SUM(requisition_qty) AS total_requisition FROM stock_balance_details WHERE 
												customer_code='".$customer_code."' AND prod_code='".$prod_code."' AND active_flag='Y' AND billed_qty =0";
						$rschkrequisitionqty=mysqli_query($link,$sqlchkrequisitionqty);
						$rowchkrequisitionqty=mysqli_fetch_assoc($rschkrequisitionqty);
						$total_requisition=$rowchkrequisitionqty['total_requisition'];
						
						${billcount.$customer_code.$prod_code}=${billcount.$customer_code.$prod_code}+1;
						$sqlchkbilledqty="SELECT SUM(billed_qty) AS total_billed FROM stock_balance_details WHERE customer_code='".$customer_code."' 
										 AND prod_code='".$prod_code."' AND active_flag='Y'";
						$rschkbilledqty=mysqli_query($link,$sqlchkbilledqty);
						$rowchkbilledqty=mysqli_fetch_assoc($rschkbilledqty);
						$total_billed=$rowchkbilledqty['total_billed']+${billcount.$customer_code.$prod_code};
	
						if($total_billed > $total_requisition)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Billing Qty cannot be greater than Requisition Qty)");
						}*/
					  /*$sqlrequisition="SELECT requisition_id FROM requisition_details WHERE requisition_id='".rtrim(addslashes($refference_no))."'";
					  $rsrequisition=mysqli_query($link,$sqlrequisition);
					  $countrequisition=mysqli_num_rows($rsrequisition);
					  if($countrequisition==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Refference No)");
						}*/
					  $sqlchkIMEI="SELECT IMEI FROM customer_product_billing WHERE IMEI='".rtrim(addslashes($IMEI))."'";
					  $rschkIMEI=mysqli_query($link,$sqlchkIMEI);
					  $countchkIMEI=mysqli_num_rows($rschkIMEI);
					  if($countchkIMEI > 0)
						{
							array_push($error_array,"Duplicate data @Row (".$csv_row_count.") Column : (IMEI)");
						}
						$sqlchkopeningbillingqty="SELECT billed_qty FROM stock_balance_details WHERE customer_code='".$customer_code."'  AND 
												prod_code='".$prod_code."'";
						$rschkopeningbillingqty=mysqli_query($link,$sqlchkopeningbillingqty);
						$openingbillingqty=mysqli_num_rows($rschkopeningbillingqty);
						/* if($openingbillingqty > 0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Opening Billing has been already uploaded for this PARTY)");
						}*/
					  $tabledatacsv.="<input type=\"hidden\" name=\"customer_name[]\" value=\"$customer_name\">
									<input type=\"hidden\" name=\"customer_code[]\" value=\"$dns_customer_code\">
									<input type=\"hidden\" name=\"invoice_no[]\" value=\"$invoice_no\">
									<input type=\"hidden\" name=\"invoice_date[]\" value=\"$invoice_date_final\">
									<input type=\"hidden\" name=\"prod_desc[]\" value=\"$prod_desc\">
									<input type=\"hidden\" name=\"IMEI[]\" value=\"$IMEI\">
									<tr id=\"tab\">
									<td>".$count."</td>
									<td>".$customer_name."</td>
									<td>".$dns_customer_code."</td>
									<td>".$invoice_no."</td>
									<td>".$invoice_date_final."</td><td>".$prod_desc."</td>
									<td>".$IMEI."</td></tr>";	
					}
					$rec_count++;
					$count++;
				}
		if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"7\"><font size=\"+2\"><u>Billing Information</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"7\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
			echo $tabledatafinal=$tabledata.$tabledatacsv."<tr><td colspan='7' align='center'><input type='hidden' name='mode' value='submit_billing_information' /><input type='submit' name='submit1' value='Final Upload' /><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminExcelReadBillingData.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
	}
	/*else
	{
		echo $successval="Naming convention for billing information.xls is wrong.";
		exit();
	}*/ 
	if($successval==1)
	{
		$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlInsert))

		{
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			//$mailto='kuntald@coral.in';
			$mailto='';
		
			if(count($error_array)>0)
			{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' with error(s) on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
				
				for($i=0;$i<count($error_array);$i++){
					$mailbody.= "<b>$error_array[$i]</b><br /><br />";
				}	
			}
			else{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database.';	
			}
			if($dupliacateproductval!=''){
				$mailbody.=$dupliacateproductval;
			}
			//$mailto='';			
			if(mail($mailto, $mailsub, $mailbody, $headers,'-facedns@coral.in'))
			{
				if(count($error_array)>0)
				{
					$error_string=implode('#',$error_array);
					$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully with the following error(s).';
				}
				else{
					$GLOBALS['msg'] = '<b>Zip file extracted and data has been uploaded successfully</b>';
				}
				$GLOBALS['error_msg']=$error_string;
				/*$error_msgArr=explode('#',$GLOBALS['error_msg']);
					if(count($error_msgArr)>0){
						for($i=0;$i<count($error_msgArr);$i++){
							echo "<b>$error_msgArr[$i]</b><br /><br />";
						}
					}*/
				disphtml("main();");
			}
			else
			{
				echo $GLOBALS['msg'] = "Error in mail sending.";
				disphtml("main();");
			}
			//echo $err = 'Zip file extracted and data has been uploaded successfully';
		}
		else 
		{
			echo $GLOBALS['msg'] = "Problem with uploading Zip file";
			disphtml("main();");
		}
	}
  }
}
?>