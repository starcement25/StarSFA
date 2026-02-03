<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
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
	if($_REQUEST['mode']=="csv_upload")				csv_upload();
	else    										   disphtml("main();");
ob_end_flush();
function main()
{
?>
<script language="JavaScript">
function checkFields()
{
	if(document.form_add_CSV.zip_file.value=="")
	{
		alert("Please browse the ZIP file first...");
		document.form_add_CSV.zip_file.focus();
		return false;
	}
	
	var fname = document.form_add_CSV.zip_file.value.toUpperCase();
	var pos1 = fname.indexOf(".ZIP");
	
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use ZIP only...");
		document.form_add_CSV.zip_file.focus();
		return false;	
	}
	return true;	
}
</script>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
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
	<form name="form_add_CSV" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="csv_upload">
		
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
                <input type="submit" name="Add" value="Add" onClick="return check();"> 
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
</table>
<?php
}
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
function csv_upload(){
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
	//For Outstanding CSV
	if(similar_file_exists("../csv/$folderName/Outstanding.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Outstanding.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$sqldelete="truncate outstanding_ageing";
			$rsdelete=mysqli_query($link,$sqldelete);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
					$csv_row_count=$rec_count+1;
					
					$plant_code=trim($data[0]);
					$dns_customer_code=trim($data[1]);
					$customer_code_name=trim($data[2]);
					$outstanding_amount=trim($data[8]);
					if(strpos($outstanding_amount,',')!=false){
						$outstanding_amount =str_replace(',','',$outstanding_amount);
					}
					$amount_0_15_days=trim($data[9]);
					if(strpos($amount_0_15_days,',')!=false){
						$amount_0_15_days =str_replace(',','',$amount_0_15_days);
					}
					$amount_16_30_days=trim($data[10]);
					if(strpos($amount_16_30_days,',')!=false){
						$amount_16_30_days =str_replace(',','',$amount_16_30_days);
					}
					$amount_31_45_days=trim($data[11]);
					if(strpos($amount_31_45_days,',')!=false){
						$amount_31_45_days =str_replace(',','',$amount_31_45_days);
					}
					$amount_46_90_days=trim($data[12]);
					if(strpos($amount_46_90_days,',')!=false){
						$amount_46_90_days =str_replace(',','',$amount_46_90_days);
					}
					$amount_greater_90_days=trim($data[13]);
					if(strpos($amount_greater_90_days,',')!=false){
						$amount_greater_90_days =str_replace(',','',$amount_greater_90_days);
					}
					$greater_90_days=trim($data[14]);
					$sqlcustomercode="SELECT customer_code,customer_name,emp_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$countcustomercode=mysqli_num_rows($rscustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					$customer_code=str_replace('/','-',$rowcustomercode['customer_code']);
					$customer_name=$rowcustomercode['customer_name'];
					$emp_code=$rowcustomercode['emp_code'];
					
					if(!in_array($customer_code,$customerarray))
					{
						array_push($customerarray,$customer_code);
						array_push($customernamearray,$customer_name);
					}
					${outstanding_amount.$customer_code}=${outstanding_amount.$customer_code}+$outstanding_amount;
					${amount_0_15_days.$customer_code}=${amount_0_15_days.$customer_code}+$amount_0_15_days;
					${amount_16_30_days.$customer_code}=${amount_16_30_days.$customer_code}+$amount_16_30_days;
					${amount_31_45_days.$customer_code}=${amount_31_45_days.$customer_code}+$amount_31_45_days;
					${amount_46_90_days.$customer_code}=${amount_46_90_days.$customer_code}+$amount_46_90_days;
					${amount_greater_90_days.$customer_code}=${amount_greater_90_days.$customer_code}+$amount_greater_90_days;
					
				}
				 $rec_count++;
			}
			for($countcustomer=0;$countcustomer<count($customerarray);$countcustomer++){
				$customer_code=$customerarray[$countcustomer];
				$customer_name=$customernamearray[$countcustomer];
				//echo ${outstanding_amount.$customer_code};
				//exit();
				if(${outstanding_amount.$customer_code} >0 || ${amount_0_15_days.$customer_code} >0)
				{
					if(${amount_16_30_days.$customer_code} <0)
					{
						${amount_0_15_days.$customer_code}=${amount_0_15_days.$customer_code}+${amount_16_30_days.$customer_code};
					}
					if(${amount_31_45_days.$customer_code} <0)
					{
						${amount_0_15_days.$customer_code}=${amount_0_15_days.$customer_code}+${amount_31_45_days.$customer_code};
					}
					if(${amount_46_90_days.$customer_code} <0)
					{
						${amount_0_15_days.$customer_code}=${amount_0_15_days.$customer_code}+${amount_46_90_days.$customer_code};
					}
					if(${amount_greater_90_days.$customer_code} <0)
					{
						${amount_0_15_days.$customer_code}=${amount_0_15_days.$customer_code}+${amount_greater_90_days.$customer_code};
					}
					
					$customer_code_replace=str_replace('-','/',$customer_code);
					$sql  = "insert into outstanding_ageing ";
					$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code_replace)."'";
					$sql .= " ,customer_name='".mysqli_real_escape_string($customer_name)."'";
					$sql .= " ,outstanding_amount='".mysqli_real_escape_string(${outstanding_amount.$customer_code})."'";
					$sql .= " ,amount_0_15_days='".mysqli_real_escape_string(${amount_0_15_days.$customer_code})."'";
					$sql .= " ,amount_16_30_days='".mysqli_real_escape_string(${amount_16_30_days.$customer_code})."'";
					$sql .= " ,amount_31_45_days='".mysqli_real_escape_string(${amount_31_45_days.$customer_code})."'";
					$sql .= " ,amount_46_90_days='".mysqli_real_escape_string(${amount_46_90_days.$customer_code})."'";
					$sql .= " ,amount_greater_90_days='".mysqli_real_escape_string(${amount_greater_90_days.$customer_code})."'";
					$sql .= " , download_time=CURRENT_TIMESTAMP()";
					//exit();
				    mysqli_query($link,$sql) or array_push($error_array,".Internal error occurrs @row $csv_row_count on Outstanding.csv.Please check.");
					if($emp_code!='')
					 {
					 	modifyempdatadownloadlog($emp_code,strtoupper($folderName));
					 }
				}
			}
			//exit();
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Contract.csv is wrong.";
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
						$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
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
?>