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
	else    										disphtml("main();");
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
function return_auto_code($code_prefix,$code_type,$code){
	
	if($code_type=='employee')
	{
		if(strlen($code)=='1')
		{
			$build_code=$code_prefix.'000'.$code;
		}
		if(strlen($code)=='2')
		{
			$build_code=$code_prefix.'00'.$code;
		}
		if(strlen($code)=='3')
		{
			$build_code=$code_prefix.'0'.$code;
		}
	}
	return $build_code;
}

function csv_upload(){
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	$error_array=array();
	if ( !file_exists("../csv/$folderName")){
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
	 $date=gmdate('d',strtotime('+330 minute'));
	 $month=gmdate('m',strtotime('+330 minute'));
	 $year=gmdate('Y',strtotime('+330 minute'));
	 $currentdate =$year.'-'.$month.'-'.$date;
	//For Emp wise customer lat long csv
	if(similar_file_exists("../csv/$folderName/empwise-customer-latlong.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/empwise-customer-latlong.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		/*$sqldelete="truncate branch_master";
		$rsdelete=mysql_query($sqldelete);*/
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
				$dns_emp_code=trim($data[0]);
				$startingdate=trim($data[1]);
				$startingdate = date('Y-m-d', strtotime($startingdate));
				$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$dns_emp_code."'";
				$rsempcode=mysql_query($sqlempcode);
				$rowempcode=mysql_fetch_array($rsempcode);
				$emp_code=$rowempcode['emp_code'];
				
				$csv_row_count=$rec_count+1;
				$sqlcustomer="SELECT customer_code,trans_id FROM customer_visit_details WHERE  emp_code='".$emp_code."' 
							AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$startingdate."' AND '".$currentdate."'
					 		ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') ASC";	 
				$rscustomer=mysql_query($sqlcustomer);
				while($rowcustomer=mysql_fetch_array($rscustomer))
				{
					$customer_code=$rowcustomer['customer_code'];
					$trans_id=$rowcustomer['trans_id'];
					$sqlselecttranslatlong="SELECT latt,longi FROM location WHERE trans_id='".$trans_id."'";
					$rsselecttranslatlong=mysql_query($sqlselecttranslatlong);
					$rowselecttranslatlong=mysql_fetch_array($rsselecttranslatlong);
					$latt_taken=$rowselecttranslatlong['latt'];
					$longi_taken=$rowselecttranslatlong['longi'];
					$sqlupdatecustomerlatlong="UPDATE customer_master SET base_latt='".$latt_taken."',base_longi='".$longi_taken."' 
												WHERE customer_code='".$customer_code."'";
					mysql_query($sqlupdatecustomerlatlong);							
				    //$trans_date=$rowselecttranslatlong['trans_date'];
				}
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Branch master.csv is wrong.";
		exit();
	}*/
	if($successval==1)
	{
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
		disphtml("main();");	 
	}
	else
	{
	   $GLOBALS['msg'] = 'Data uploading error. Please contact ADMIN.';
		disphtml("main();");	 
	}
}
?>