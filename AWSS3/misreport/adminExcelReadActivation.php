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
	if($_REQUEST['mode']=="submit_imei_activation")
	{
		$current_date=date('Y-m-d');
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		
		$activation_date=$_POST['activation_date'];
		$IMEI=$_POST['IMEI'];
		for($i=0;$i<count($IMEI);$i++)
		{	  
			$sqlupdatebilling="UPDATE customer_product_billing SET activation_date='".date('Y-m-d H:i:s',strtotime($activation_date[$i]))."',
								active_IMEI_upload_time=CURRENT_TIMESTAMP(),download_time=CURRENT_TIMESTAMP() 
								WHERE  IMEI='".$IMEI[$i]."'";
			$rsupdatebilling=mysql_query($sqlupdatebilling);					
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
                    <td width="90%" align="center" class="ERR"><font size="+2"><u>IMEI Activation</u></font></td>
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
	  //For IMEI activation xls
	   if(similar_file_exists("../csv/$folderName/IMEI activation.xlsx")!=false || similar_file_exists("../csv/$folderName/IMEI activation.xls")!=false)
	    {
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=1;
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
		if(similar_file_exists("../csv/$folderName/IMEI activation.xlsx")!=false)
		{
			$inputfilename = "../csv/$folderName/IMEI activation.xlsx";
		}
		if(similar_file_exists("../csv/$folderName/IMEI activation.xls")!=false)
		{
			$inputfilename = "../csv/$folderName/IMEI activation.xls";
		}
		$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
		$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
		$objPHPExcel = $objReader->load($inputfilename);
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="50%" cellpadding="4" align="center" >
		  <tr class="TDHEAD" align="center" id="head_main">
			<td colspan="3" class="TDHEAD" align="center">IMEI activation</td>
		  </tr>
		  <tr class="TDHEAD_SUB" align="center" id="head_main">
		   <td>SI</td><td>IMEI</td><td>Activate Date time</td></tr>';
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
						$IMEI=$sheet->getCellByColumnAndRow(0,$row)->getFormattedValue();
						//$dns_customer_code_from = trim($rowData[0][1]);
						$activation_date = date('d-m-Y H:i:s',PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row)->getValue()));
						
					  /*$sqlchkIMEI="SELECT IMEI FROM customer_product_billing WHERE IMEI='".rtrim(addslashes($IMEI))."'";
					  $rschkIMEI=mysql_query($sqlchkIMEI);
					  $countchkIMEI=mysql_num_rows($rschkIMEI);
					  if($countchkIMEI ==0)
						{
							array_push($error_array,"Invalid IMEI @Row (".$csv_row_count.") Column : (IMEI)");
						}*/
					  $tabledatacsv.="<input type=\"hidden\" name=\"activation_date[]\" value=\"$activation_date\">
									<input type=\"hidden\" name=\"IMEI[]\" value=\"$IMEI\">
									<tr id=\"tab\">
									<td>".$count."</td>
									<td>".$IMEI."</td>
									<td>".$activation_date."</td>
									</tr>";
						$count++;				
					}
					$rec_count++;
					
				}
		if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"7\"><font size=\"+2\"><u>IMEI Activation</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"7\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
			echo $tabledatafinal=$tabledata.$tabledatacsv."<tr><td colspan='7' align='center'><input type='hidden' name='mode' value='submit_imei_activation' /><input type='submit' name='submit1' value='Final Upload' /><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminExcelReadActivation.php'\"/></td></tr></table></form>";
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
		if(mysql_query($sqlInsert))

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