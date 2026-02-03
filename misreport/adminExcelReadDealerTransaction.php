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
	if($_REQUEST['mode']=="submit_dealer_transaction")
	{
		$current_date=date('Y-m-d');
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		$customer_code=$_POST['customer_code'];
		$YTD_sales=$_POST['YTD_sales'];
 		$Dues=$_POST['Dues'];
		$legend_earned_point=$_POST['legend_earned_point'];
		$legend_tier=$_POST['legend_tier'];
		$legend_total_point=$_POST['legend_total_point'];
		$acedns=$_POST['acedns'];
		
		for($i=0;$i<count($customer_code);$i++)
		{	  
			$sqlselcustomercode="SELECT CM.customer_code,CM.customer_name,CRR.emp_code FROM customer_master CM,customer_route_emp_relation  CRR
							WHERE CM.customer_code=CRR.customer_code AND CM.dns_customer_code='".addslashes($customer_code[$i])."'";
			$rsselcustomercode=mysqli_query($link,$sqlselcustomercode);
			$rowcustomercode=mysqli_fetch_assoc($rsselcustomercode);
			$customer_code_db=$rowcustomercode['customer_code'];
			$customer_name=$rowcustomercode['customer_name'];
			$emp_code=$rowcustomercode['emp_code'];
		  
		  $sqlchkdealertrans="SELECT customer_code FROM dealer_transaction WHERE customer_code='".addslashes($customer_code_db)."'";
		  $rschkdealertrans=mysqli_query($link,$sqlchkdealertrans);
		  $cntchkdealertrans=mysqli_num_rows($rschkdealertrans);
		  if($cntchkdealertrans >0){
			 $sqlupdate="UPDATE dealer_transaction SET acedns='N' WHERE customer_code='".addslashes($customer_code_db)."'";
		  	mysqli_query($link,$sqlupdate);

		  }
		  $sqlinsert="INSERT INTO dealer_transaction SET customer_code='".$customer_code_db."',customer_name='".$customer_name."',emp_code='".$emp_code."',
					YTD_sales='".$YTD_sales[$i]."',dues='".$Dues[$i]."',legends_earned_points='".$legend_earned_point[$i]."',
					legends_tier='".$legend_tier[$i]."',legends_total_points='".$legend_total_point[$i]."',acedns='".$acedns[$i]."',
					download_time=CURRENT_TIMESTAMP()";
		  mysqli_query($link,$sqlinsert);
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
                    <td width="90%" align="center" class="ERR"><font size="+2"><u>Upload Dealer Transaction</u></font></td>
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
	   if(similar_file_exists("../csv/$folderName/dealer transaction.xlsx")!=false || similar_file_exists("../csv/$folderName/dealer transaction.xls")!=false)
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
		if(similar_file_exists("../csv/$folderName/dealer transaction.xlsx")!=false)
		{
			$inputfilename = "../csv/$folderName/dealer transaction.xlsx";
		}
		if(similar_file_exists("../csv/$folderName/dealer transaction.xls")!=false)
		{
			$inputfilename = "../csv/$folderName/dealer transaction.xls";
		}
		$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
		$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
		$objPHPExcel = $objReader->load($inputfilename);
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
		  <tr class="TDHEAD" align="center" id="head_main">
			<td colspan="9" class="TDHEAD" align="center">Dealer Transaction</td>
		  </tr>
		  <tr class="TDHEAD_SUB" align="center" id="head_main">
		   <td>SI</td><td>Customer Code</td><td>YTD sales</td><td>Dues</td><td>Legends earned points
</td><td>Legends tier</td><td>Legends total points</td><td>Acedns</td></tr>';
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
						$customer_code = trim($rowData[0][0]);
						$YTD_sales=$sheet->getCellByColumnAndRow(1,$row)->getFormattedValue();
						$Dues=$sheet->getCellByColumnAndRow(2,$row)->getFormattedValue();
						$legend_earned_point=$sheet->getCellByColumnAndRow(3,$row)->getFormattedValue();
						$legend_tier=$sheet->getCellByColumnAndRow(4,$row)->getFormattedValue();
						$legend_total_point=$sheet->getCellByColumnAndRow(5,$row)->getFormattedValue();
						$acedns = trim($rowData[0][6]);
						
					    $sqlselcustomercode="SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRR  WHERE 
				  					CRR.customer_code=CM.customer_code AND CM.dns_customer_code='".addslashes($customer_code)."' and CRR.acedns='Y'";
						$rsselcustomercode=mysqli_query($link,$sqlselcustomercode);
						$countselcustomercode=mysqli_num_rows($rsselcustomercode); 
						if($countselcustomercode==0)
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Customer Code)");
						}
						else
						{
							$rowselcustomercode=mysqli_fetch_assoc($rsselcustomercode);
							$customer_code_db=$rowselcustomercode['customer_code'];
						}
					  $tabledatacsv.="<input type=\"hidden\" name=\"customer_code[]\" value=\"$customer_code\">
									<input type=\"hidden\" name=\"YTD_sales[]\" value=\"$YTD_sales\">
									<input type=\"hidden\" name=\"Dues[]\" value=\"$Dues\">
									<input type=\"hidden\" name=\"legend_earned_point[]\" value=\"$legend_earned_point\">
									<input type=\"hidden\" name=\"legend_tier[]\" value=\"$legend_tier\">
									<input type=\"hidden\" name=\"legend_total_point[]\" value=\"$legend_total_point\">
									<input type=\"hidden\" name=\"acedns[]\" value=\"$acedns\">
									<tr id=\"tab\">
									<td>".$count."</td>
									<td>".$customer_code."</td>
									<td align=\"right\">".$YTD_sales."</td>
									<td align=\"right\">".$Dues."</td><td align=\"right\">".$legend_earned_point."</td>
									<td align=\"right\">".$legend_tier."</td><td align=\"right\">".$legend_total_point."</td><td align=\"right\">".$acedns."</td></tr>";	
					}
					$rec_count++;
					$count++;
				}
		if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"9\"><font size=\"+2\"><u>Dealer Transaction</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"9\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
			echo $tabledatafinal=$tabledata.$tabledatacsv."<tr><td colspan='9' align='center'><input type='hidden' name='mode' value='submit_dealer_transaction' /><input type='submit' name='submit1' value='Final Upload' /><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminExcelReadDealerTransaction.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
	}
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