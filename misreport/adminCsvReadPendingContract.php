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
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
//require("include/functions.php");
require("include/config-email-setup.php");

if($_REQUEST['mode']=='csv_upload')
{
	csv_upload();
}
else
{
	main();
}*/
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
	//For Contract CSV
	if(similar_file_exists("../csv/$folderName/Contract.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Contract.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			$sqldelete="truncate pending_contract";
			$rsdelete=mysqli_query($link,$sqldelete);
			$customeroutstandingmissmatchArr=array();
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
					
					$branch_code_name=trim($data[0]);
					$prod_code_name=trim($data[1]);
					$customer_code_name=trim($data[2]);
					$broker_code_name=trim($data[3]);
					$contract_qty=trim($data[4]);
					if(strpos($contract_qty,',')!=false){
						$contract_qty =str_replace(',','',$contract_qty);
					}
					$despatch_qty=trim($data[5]);
					if(strpos($despatch_qty,',')!=false){
						$despatch_qty =str_replace(',','',$despatch_qty);
					}
					$pending_qty=trim($data[6]);
					if(strpos($pending_qty,',')!=false){
						$pending_qty =str_replace(',','',$pending_qty);
					}
					$sauda_date=trim($data[7]);
					$sauda_date =str_replace('.','-',$sauda_date);
					$finaldate=date('Y-m-d',strtotime($sauda_date));
					//$dateArr=explode('.',$sauda_date);
					//$finaldate=$dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
	
					if(providing_code=='yes'){
						$sqlbranchchk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
						$rsbranchchk=mysqli_query($link,$sqlbranchchk);
						$rowbranchchk=mysqli_fetch_assoc($rsbranchchk);
						$branch_code=$rowbranchchk['branch_code'];
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";
						$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";
						$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($broker_code_name)."'";
					}
					else
					{
						$sqlbranchchk="SELECT branch_code FROM branch_master WHERE banch_name='".addslashes($branch_code_name)."'";
						$rsbranchchk=mysqli_query($link,$sqlbranchchk);
						$rowbranchchk=mysqli_fetch_assoc($rsbranchchk);
						$branch_code=$rowbranchchk['branch_code'];
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";
						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' AND branch_code='".$branch_code."'";
						$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE broker_name='".addslashes($broker_code_name)."'";
					}
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$countcustomercode=mysqli_num_rows($rscustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					
					$rsprodcode=mysqli_query($link,$sqlprodcode);
					$rowprodcode=mysqli_fetch_assoc($rsprodcode);
					$prod_code=$rowprodcode['prod_code'];
					
				    $rsbrokerchk=mysqli_query($link,$sqlbrokerchk);
					$rowbrokerchk=mysqli_fetch_assoc($rsbrokerchk);
					$broker_id=$rowbrokerchk['broker_id'];

					$sql  = "insert into pending_contract ";
					$sql .= " SET branch_code='".mysqli_real_escape_string($branch_code)."'";
					$sql .= " ,prod_code='".mysqli_real_escape_string($prod_code)."'";
					$sql .= " ,customer_code='".mysqli_real_escape_string($customer_code)."'";
					$sql .= " ,broker_id='".mysqli_real_escape_string($broker_id)."'";
					$sql .= " , contract_qty='".mysqli_real_escape_string($contract_qty)."'";
					$sql .= " , despatch_qty='".mysqli_real_escape_string($despatch_qty)."'";
					$sql .= " , pending_qty='".mysqli_real_escape_string($pending_qty)."'";
					$sql .= " , sauda_date='".$finaldate."'";
					
					mysqli_query($link,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Contract.csv.Please check.");
					
				}
				 $rec_count++;
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
				$mailto='kuntald@coral.in';
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