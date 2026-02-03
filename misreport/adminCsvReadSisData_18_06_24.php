<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ob_start();
	session_start();
	require("adminUtils.php");
	require("include/config.php");
    require("include/config-setup.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if($_REQUEST['mode']=="csv_upload")				csv_upload();
	else    										disphtml("main();");
ob_end_flush();
function main()
{
    require("include/dbcon.php");
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
			<td colspan="10">Upload SIS Data</td>
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
    require("include/dbcon.php");
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	$error_array=array();
	$upload_master_table_array=array();
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
	if(similar_file_exists("../csv/$folderName/branch base volume achv.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/branch base volume achv.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
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
				$dns_branch_code=trim($data[0]);
				$april_target=trim($data[1]);
				$april_achv=trim($data[2]);
				$may_target=trim($data[3]);
				$may_achv=trim($data[4]);
				$june_target=trim($data[5]);
				$june_achv=trim($data[6]);
				$july_target=trim($data[7]);
				$july_achv=trim($data[8]);
				$august_target=trim($data[9]);
				$august_achv=trim($data[10]);
				$september_target=trim($data[11]);
				$september_achv=trim($data[12]);
				$october_target=trim($data[13]);
				$october_achv=trim($data[14]);
				$november_target=trim($data[15]);
				$november_achv=trim($data[16]);
				$december_target=trim($data[17]);
				$december_achv=trim($data[18]);
				$january_target=trim($data[19]);
				$january_achv=trim($data[20]);
				$february_target=trim($data[21]);
				$february_achv=trim($data[22]);
				$march_target=trim($data[23]);
				$march_achv=trim($data[24]);
				
				$sqlchkbranchcode="SELECT branch_code FROM sis_branch_volume_target_ach WHERE branch_code='".addslashes($dns_branch_code)."' AND year='2021'";
				$branch_code=$dns_branch_code;
				$rschkbranchcode=mysqli_query($link,$sqlchkbranchcode);
				$countchkbranchcode=mysqli_num_rows($rschkbranchcode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchkbranchcode ==0)
				{
					$sqlbranchvolume  = "insert into sis_branch_volume_target_ach SET ";
					$sqlbranchvolume .= "   branch_code='".mysqli_real_escape_string($dns_branch_code)."'";
					$sqlbranchvolume .= "   year='2021'";
					$sqlbranchvolume .= " , jan_vol_target='".mysqli_real_escape_string($january_target)."'";
					$sqlbranchvolume .= " , jan_vol_achievement='".mysqli_real_escape_string($january_achv)."'";
					$sqlbranchvolume .= " , feb_vol_target='".mysqli_real_escape_string($february_target)."'";
					$sqlbranchvolume .= " , feb_vol_achievement='".mysqli_real_escape_string($february_achv)."'";
					$sqlbranchvolume .= " , mar_vol_target='".mysqli_real_escape_string($march_target)."'";
					$sqlbranchvolume .= " , mar_vol_achievement='".mysqli_real_escape_string($march_achv)."'";
					$sqlbranchvolume .= " , apr_vol_target='".mysqli_real_escape_string($april_target)."'";
					$sqlbranchvolume .= " , apr_vol_achievement='".mysqli_real_escape_string($april_achv)."'";
					$sqlbranchvolume .= " , may_vol_target='".mysqli_real_escape_string($may_target)."'";
					$sqlbranchvolume .= " , may_vol_achievement='".mysqli_real_escape_string($may_achv)."'";
					$sqlbranchvolume .= " , jun_vol_target='".mysqli_real_escape_string($june_target)."'";
					$sqlbranchvolume .= " , jun_vol_achievement='".mysqli_real_escape_string($june_achv)."'";
					$sqlbranchvolume .= " , jul_vol_target='".mysqli_real_escape_string($july_target)."'";
					$sqlbranchvolume .= " , jul_vol_achievement='".mysqli_real_escape_string($july_achv)."'";
					$sqlbranchvolume .= " , aug_vol_target='".mysqli_real_escape_string($august_target)."'";
					$sqlbranchvolume .= " , aug_vol_achievement='".mysqli_real_escape_string($august_achv)."'";
					$sqlbranchvolume .= " , sep_vol_target='".mysqli_real_escape_string($september_target)."'";
					$sqlbranchvolume .= " , sep_vol_achievement='".mysqli_real_escape_string($september_achv)."'";
					$sqlbranchvolume .= " , oct_vol_target='".mysqli_real_escape_string($october_target)."'";
					$sqlbranchvolume .= " , oct_vol_achievement='".mysqli_real_escape_string($october_achv)."'";
					$sqlbranchvolume .= " , nov_vol_target='".mysqli_real_escape_string($november_target)."'";
					$sqlbranchvolume .= " , nov_vol_achievement='".mysqli_real_escape_string($november_achv)."'";
					$sqlbranchvolume .= " , dec_vol_target='".mysqli_real_escape_string($december_target)."'";
					$sqlbranchvolume .= " , dec_vol_achievement='".mysqli_real_escape_string($december_achv)."'";
					$sqlbranchvolume .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlbranchvolume) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in branch base volume achv.csv.Please check.");
				}
				else
				{
					$sqlbranchvolumeupdate  = "update sis_branch_volume_target_ach SET ";
					$sqlbranchvolumeupdate .= " , jan_vol_target='".mysqli_real_escape_string($january_target)."'";
					$sqlbranchvolumeupdate .= " , jan_vol_achievement='".mysqli_real_escape_string($january_achv)."'";
					$sqlbranchvolumeupdate .= " , feb_vol_target='".mysqli_real_escape_string($february_target)."'";
					$sqlbranchvolumeupdate .= " , feb_vol_achievement='".mysqli_real_escape_string($february_achv)."'";
					$sqlbranchvolumeupdate .= " , mar_vol_target='".mysqli_real_escape_string($march_target)."'";
					$sqlbranchvolumeupdate .= " , mar_vol_achievement='".mysqli_real_escape_string($march_achv)."'";
					$sqlbranchvolumeupdate .= " , apr_vol_target='".mysqli_real_escape_string($april_target)."'";
					$sqlbranchvolumeupdate .= " , apr_vol_achievement='".mysqli_real_escape_string($april_achv)."'";
					$sqlbranchvolumeupdate .= " , may_vol_target='".mysqli_real_escape_string($may_target)."'";
					$sqlbranchvolumeupdate .= " , may_vol_achievement='".mysqli_real_escape_string($may_achv)."'";
					$sqlbranchvolumeupdate .= " , jun_vol_target='".mysqli_real_escape_string($june_target)."'";
					$sqlbranchvolumeupdate .= " , jun_vol_achievement='".mysqli_real_escape_string($june_achv)."'";
					$sqlbranchvolumeupdate .= " , jul_vol_target='".mysqli_real_escape_string($july_target)."'";
					$sqlbranchvolumeupdate .= " , jul_vol_achievement='".mysqli_real_escape_string($july_achv)."'";
					$sqlbranchvolumeupdate .= " , aug_vol_target='".mysqli_real_escape_string($august_target)."'";
					$sqlbranchvolumeupdate .= " , aug_vol_achievement='".mysqli_real_escape_string($august_achv)."'";
					$sqlbranchvolumeupdate .= " , sep_vol_target='".mysqli_real_escape_string($september_target)."'";
					$sqlbranchvolumeupdate .= " , sep_vol_achievement='".mysqli_real_escape_string($september_achv)."'";
					$sqlbranchvolumeupdate .= " , oct_vol_target='".mysqli_real_escape_string($october_target)."'";
					$sqlbranchvolumeupdate .= " , oct_vol_achievement='".mysqli_real_escape_string($october_achv)."'";
					$sqlbranchvolumeupdate .= " , nov_vol_target='".mysqli_real_escape_string($november_target)."'";
					$sqlbranchvolumeupdate .= " , nov_vol_achievement='".mysqli_real_escape_string($november_achv)."'";
					$sqlbranchvolumeupdate .= " , dec_vol_target='".mysqli_real_escape_string($december_target)."'";
					$sqlbranchvolumeupdate .= " , dec_vol_achievement='".mysqli_real_escape_string($december_achv)."'";
					$sqlbranchvolumeupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$dns_branch_code."' AND year='2021'";
					mysqli_query($link,$sqlbranchvolumeupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in branch base volume achv.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/branch dealer target achv.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/branch dealer target achv.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
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
				$dns_branch_code=trim($data[0]);
				$april_target=trim($data[1]);
				$april_achv=trim($data[2]);
				$may_target=trim($data[3]);
				$may_achv=trim($data[4]);
				$june_target=trim($data[5]);
				$june_achv=trim($data[6]);
				$july_target=trim($data[7]);
				$july_achv=trim($data[8]);
				$august_target=trim($data[9]);
				$august_achv=trim($data[10]);
				$september_target=trim($data[11]);
				$september_achv=trim($data[12]);
				$october_target=trim($data[13]);
				$october_achv=trim($data[14]);
				$november_target=trim($data[15]);
				$november_achv=trim($data[16]);
				$december_target=trim($data[17]);
				$december_achv=trim($data[18]);
				$january_target=trim($data[19]);
				$january_achv=trim($data[20]);
				$february_target=trim($data[21]);
				$february_achv=trim($data[22]);
				$march_target=trim($data[23]);
				$march_achv=trim($data[24]);
				
				$sqlchkbranchcode="SELECT branch_code FROM sis_branch_dealer_volume_target_ach WHERE branch_code='".addslashes($dns_branch_code)."' AND year='2021'";
				$branch_code=$dns_branch_code;
				$rschkbranchcode=mysqli_query($link,$sqlchkbranchcode);
				$countchkbranchcode=mysqli_num_rows($rschkbranchcode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchkbranchcode ==0)
				{
					$sqlbranchdealer  = "insert into sis_branch_dealer_volume_target_ach SET ";
					$sqlbranchdealer .= "   branch_code='".mysqli_real_escape_string($dns_branch_code)."'";
					$sqlbranchdealer .= "   year='2021'";
					$sqlbranchdealer .= " , jan_dealer_target='".mysqli_real_escape_string($january_target)."'";
					$sqlbranchdealer .= " , jan_dealer_achievement='".mysqli_real_escape_string($january_achv)."'";
					$sqlbranchdealer .= " , feb_dealer_target='".mysqli_real_escape_string($february_target)."'";
					$sqlbranchdealer .= " , feb_dealer_achievement='".mysqli_real_escape_string($february_achv)."'";
					$sqlbranchdealer .= " , mar_dealer_target='".mysqli_real_escape_string($march_target)."'";
					$sqlbranchdealer .= " , mar_dealer_achievement='".mysqli_real_escape_string($march_achv)."'";
					$sqlbranchdealer .= " , apr_dealer_target='".mysqli_real_escape_string($april_target)."'";
					$sqlbranchdealer .= " , apr_dealer_achievement='".mysqli_real_escape_string($april_achv)."'";
					$sqlbranchdealer .= " , may_dealer_target='".mysqli_real_escape_string($may_target)."'";
					$sqlbranchdealer .= " , may_dealer_achievement='".mysqli_real_escape_string($may_achv)."'";
					$sqlbranchdealer .= " , jun_dealer_target='".mysqli_real_escape_string($june_target)."'";
					$sqlbranchdealer .= " , jun_dealer_achievement='".mysqli_real_escape_string($june_achv)."'";
					$sqlbranchdealer .= " , jul_dealer_target='".mysqli_real_escape_string($july_target)."'";
					$sqlbranchdealer .= " , jul_dealer_achievement='".mysqli_real_escape_string($july_achv)."'";
					$sqlbranchdealer .= " , aug_dealer_target='".mysqli_real_escape_string($august_target)."'";
					$sqlbranchdealer .= " , aug_dealer_achievement='".mysqli_real_escape_string($august_achv)."'";
					$sqlbranchdealer .= " , sep_dealer_target='".mysqli_real_escape_string($september_target)."'";
					$sqlbranchdealer .= " , sep_dealer_achievement='".mysqli_real_escape_string($september_achv)."'";
					$sqlbranchdealer .= " , oct_dealer_target='".mysqli_real_escape_string($october_target)."'";
					$sqlbranchdealer .= " , oct_dealer_achievement='".mysqli_real_escape_string($october_achv)."'";
					$sqlbranchdealer .= " , nov_dealer_target='".mysqli_real_escape_string($november_target)."'";
					$sqlbranchdealer .= " , nov_dealer_achievement='".mysqli_real_escape_string($november_achv)."'";
					$sqlbranchdealer .= " , dec_dealer_target='".mysqli_real_escape_string($december_target)."'";
					$sqlbranchdealer .= " , dec_dealer_achievement='".mysqli_real_escape_string($december_achv)."'";
					$sqlbranchdealer .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlbranchdealer) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in branch dealer target achv.csv.Please check.");
				}
				else
				{
					$sqlbranchdealerupdate  = "update sis_branch_dealer_volume_target_ach SET ";
					$sqlbranchdealerupdate .= " , jan_dealer_target='".mysqli_real_escape_string($january_target)."'";
					$sqlbranchdealerupdate .= " , jan_dealer_achievement='".mysqli_real_escape_string($january_achv)."'";
					$sqlbranchdealerupdate .= " , feb_dealer_target='".mysqli_real_escape_string($february_target)."'";
					$sqlbranchdealerupdate .= " , feb_dealer_achievement='".mysqli_real_escape_string($february_achv)."'";
					$sqlbranchdealerupdate .= " , mar_dealer_target='".mysqli_real_escape_string($march_target)."'";
					$sqlbranchdealerupdate .= " , mar_dealer_achievement='".mysqli_real_escape_string($march_achv)."'";
					$sqlbranchdealerupdate .= " , apr_dealer_target='".mysqli_real_escape_string($april_target)."'";
					$sqlbranchdealerupdate .= " , apr_dealer_achievement='".mysqli_real_escape_string($april_achv)."'";
					$sqlbranchdealerupdate .= " , may_dealer_target='".mysqli_real_escape_string($may_target)."'";
					$sqlbranchdealerupdate .= " , may_dealer_achievement='".mysqli_real_escape_string($may_achv)."'";
					$sqlbranchdealerupdate .= " , jun_dealer_target='".mysqli_real_escape_string($june_target)."'";
					$sqlbranchdealerupdate .= " , jun_dealer_achievement='".mysqli_real_escape_string($june_achv)."'";
					$sqlbranchdealerupdate .= " , jul_dealer_target='".mysqli_real_escape_string($july_target)."'";
					$sqlbranchdealerupdate .= " , jul_dealer_achievement='".mysqli_real_escape_string($july_achv)."'";
					$sqlbranchdealerupdate .= " , aug_dealer_target='".mysqli_real_escape_string($august_target)."'";
					$sqlbranchdealerupdate .= " , aug_dealer_achievement='".mysqli_real_escape_string($august_achv)."'";
					$sqlbranchdealerupdate .= " , sep_dealer_target='".mysqli_real_escape_string($september_target)."'";
					$sqlbranchdealerupdate .= " , sep_dealer_achievement='".mysqli_real_escape_string($september_achv)."'";
					$sqlbranchdealerupdate .= " , oct_dealer_target='".mysqli_real_escape_string($october_target)."'";
					$sqlbranchdealerupdate .= " , oct_dealer_achievement='".mysqli_real_escape_string($october_achv)."'";
					$sqlbranchdealerupdate .= " , nov_dealer_target='".mysqli_real_escape_string($november_target)."'";
					$sqlbranchdealerupdate .= " , nov_dealer_achievement='".mysqli_real_escape_string($november_achv)."'";
					$sqlbranchdealerupdate .= " , dec_dealer_target='".mysqli_real_escape_string($december_target)."'";
					$sqlbranchdealerupdate .= " , dec_dealer_achievement='".mysqli_real_escape_string($december_achv)."'";
					$sqlbranchdealerupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$dns_branch_code."' AND year='2021'";
					mysqli_query($link,$sqlbranchdealerupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in branch dealer target achv.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/weightage conversion.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/weightage conversion.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
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
				$state_name=trim($data[0]);
				$prod_code_name=trim($data[1]);
				$uom1=trim($data[2]);
				$uom2=trim($data[3]);
				$weightage_conversion1=trim($data[4]);
				$weightage_conversion2=trim($data[5]);
				if(providing_code=='yes'){
					$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";
				}
				else
				{
					$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
				} 
				$rsproductcode=mysqli_query($link,$sqlproductcode);
				$rowproductcode=mysqli_fetch_assoc($rsproductcode);
				$product_code=$rowproductcode['prod_code'];
				$csv_row_count=$rec_count+1;
				
				$sqlweightagechk="SELECT prod_code FROM state_product_wise_weightage WHERE prod_code='".$product_code."' 
							AND state_name='".$state_name."'";
				$rsweightagechk=mysqli_query($link,$sqlweightagechk);
				$countweightagechk=mysqli_num_rows($rsweightagechk);
				$rowweightagechk=mysqli_fetch_assoc($rsweightagechk);

				if($countweightagechk<1)
				{
					$sqlinsertweightage  = "insert into state_product_wise_weightage SET ";
					$sqlinsertweightage .= "  	state_name='".addslashes($state_name)."'";
					$sqlinsertweightage .= " , prod_code='".addslashes($product_code)."'";
					$sqlinsertweightage .= " , UOM1='".addslashes($uom1)."'";
					$sqlinsertweightage .= " , UOM2='".addslashes($uom2)."'";
					$sqlinsertweightage .= " , weightage_conversio1='".addslashes($weightage_conversion1)."'";
					$sqlinsertweightage .= " , weightage_conversion2='".addslashes($weightage_conversion2)."'";
					$sqlinsertweightage .= " , acedns='yes'";
					$sqlinsertweightage .= " , upload_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertweightage) or array_push($error_array,"mysqli_error().Internal error occurs on weightage conversion table.Please contact ADMIN.");
				}

				else
				{
					$sqlupdateweightage  = "UPDATE state_product_wise_weightage SET ";
					$sqlupdateweightage .= "  	UOM1='".addslashes($uom1)."'";
					$sqlupdateweightage .= "  	UOM1='".addslashes($uom2)."'";
					$sqlupdateweightage .= "  	weightage_conversio1='".addslashes($weightage_conversio1)."'";
					$sqlupdateweightage .= "  	weightage_conversion2='".addslashes($weightage_conversion2)."'";
					$sqlupdateweightage .= ",  upload_time=CURRENT_TIMESTAMP()";
					$sqlupdateweightage .= "  WHERE prod_code='".addslashes($product_code)."' AND state_name='".addslashes($state_name)."'";
					mysqli_query($link,$sqlupdateweightage) or array_push($error_array,"mysqli_error().Internal error occurs on  weightage conversion table.Please contact ADMIN");
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
	if(similar_file_exists("../csv/$folderName/branch level penalty.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/branch level penalty.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
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
				$dns_branch_code=trim($data[0]);
				$april_l2_penalty=trim($data[1]);
				$april_l3_above_penalty=trim($data[2]);
				$may_l2_penalty=trim($data[3]);
				$may_l3_above_penalty=trim($data[4]);
				$june_l2_penalty=trim($data[5]);
				$june_l3_above_penalty=trim($data[6]);
				$july_l2_penalty=trim($data[7]);
				$july_l3_above_penalty=trim($data[8]);
				$august_l2_penalty=trim($data[9]);
				$august_l3_above_penalty=trim($data[10]);
				$september_l2_penalty=trim($data[11]);
				$september_l3_above_penalty=trim($data[12]);
				$october_l2_penalty=trim($data[13]);
				$october_l3_above_penalty=trim($data[14]);
				$november_l2_penalty=trim($data[15]);
				$november_l3_above_penalty=trim($data[16]);
				$december_l2_penalty=trim($data[17]);
				$december_l3_above_penalty=trim($data[18]);
				$january_l2_penalty=trim($data[19]);
				$january_l3_above_penalty=trim($data[20]);
				$february_l2_penalty=trim($data[21]);
				$february_l3_above_penalty=trim($data[22]);
				$march_l2_penalty=trim($data[23]);
				$march_l3_above_penalty=trim($data[24]);
				
				$sqlchkbranchcode="SELECT branch_code FROM sis_branch_level_wise_penalty WHERE branch_code='".addslashes($dns_branch_code)."' AND year='2021'";
				$branch_code=$dns_branch_code;
				$rschkbranchcode=mysqli_query($link,$sqlchkbranchcode);
				$countchkbranchcode=mysqli_num_rows($rschkbranchcode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchkbranchcode ==0)
				{
					$sqlbranchdealer  = "insert into sis_branch_level_wise_penalty SET ";
					$sqlbranchdealer .= "   branch_code='".mysqli_real_escape_string($dns_branch_code)."'";
					$sqlbranchdealer .= "   year='2021'";
					$sqlbranchdealer .= " , jan_l2_penalty='".mysqli_real_escape_string($january_l2_penalty)."'";
					$sqlbranchdealer .= " , jan_l3_above_penalty='".mysqli_real_escape_string($january_l3_above_penalty)."'";
					$sqlbranchdealer .= " , feb_l2_penalty='".mysqli_real_escape_string($february_l2_penalty)."'";
					$sqlbranchdealer .= " , feb_l3_above_penalty='".mysqli_real_escape_string($february_l3_above_penalty)."'";
					$sqlbranchdealer .= " , mar_l2_penalty='".mysqli_real_escape_string($march_l2_penalty)."'";
					$sqlbranchdealer .= " , mar_l3_above_penalty='".mysqli_real_escape_string($march_l3_above_penalty)."'";
					$sqlbranchdealer .= " , apr_l2_penalty='".mysqli_real_escape_string($april_l2_penalty)."'";
					$sqlbranchdealer .= " , apr_l3_above_penalty='".mysqli_real_escape_string($april_l3_above_penalty)."'";
					$sqlbranchdealer .= " , may_l2_penalty='".mysqli_real_escape_string($may_l2_penalty)."'";
					$sqlbranchdealer .= " , may_l3_above_penalty='".mysqli_real_escape_string($may_l3_above_penalty)."'";
					$sqlbranchdealer .= " , jun_l2_penalty='".mysqli_real_escape_string($june_l2_penalty)."'";
					$sqlbranchdealer .= " , jun_l3_above_penalty='".mysqli_real_escape_string($june_l3_above_penalty)."'";
					$sqlbranchdealer .= " , jul_l2_penalty='".mysqli_real_escape_string($july_l2_penalty)."'";
					$sqlbranchdealer .= " , jul_l3_above_penalty='".mysqli_real_escape_string($july_l3_above_penalty)."'";
					$sqlbranchdealer .= " , aug_l2_penalty='".mysqli_real_escape_string($august_l2_penalty)."'";
					$sqlbranchdealer .= " , aug_l3_above_penalty='".mysqli_real_escape_string($august_l3_above_penalty)."'";
					$sqlbranchdealer .= " , sep_l2_penalty='".mysqli_real_escape_string($september_l2_penalty)."'";
					$sqlbranchdealer .= " , sep_l3_above_penalty='".mysqli_real_escape_string($september_l3_above_penalty)."'";
					$sqlbranchdealer .= " , oct_l2_penalty='".mysqli_real_escape_string($october_l2_penalty)."'";
					$sqlbranchdealer .= " , oct_l3_above_penalty='".mysqli_real_escape_string($october_l3_above_penalty)."'";
					$sqlbranchdealer .= " , nov_l2_penalty='".mysqli_real_escape_string($november_l2_penalty)."'";
					$sqlbranchdealer .= " , nov_l3_above_penalty='".mysqli_real_escape_string($november_l3_above_penalty)."'";
					$sqlbranchdealer .= " , dec_l2_penalty='".mysqli_real_escape_string($december_l2_penalty)."'";
					$sqlbranchdealer .= " , dec_l3_above_penalty='".mysqli_real_escape_string($december_l3_above_penalty)."'";
					$sqlbranchdealer .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlbranchdealer) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in branch level penalty.csv.Please check.");
				}
				else
				{
					$sqlbranchdealerupdate  = "update sis_branch_level_wise_penalty SET ";
					$sqlbranchdealerupdate .= " , jan_l2_penalty='".mysqli_real_escape_string($january_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , jan_l3_above_penalty='".mysqli_real_escape_string($january_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , feb_l2_penalty='".mysqli_real_escape_string($february_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , feb_l3_above_penalty='".mysqli_real_escape_string($february_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , mar_l2_penalty='".mysqli_real_escape_string($march_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , mar_l3_above_penalty='".mysqli_real_escape_string($march_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , apr_l2_penalty='".mysqli_real_escape_string($april_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , apr_l3_above_penalty='".mysqli_real_escape_string($april_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , may_l2_penalty='".mysqli_real_escape_string($may_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , may_l3_above_penalty='".mysqli_real_escape_string($may_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , jun_l2_penalty='".mysqli_real_escape_string($june_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , jun_l3_above_penalty='".mysqli_real_escape_string($june_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , jul_l2_penalty='".mysqli_real_escape_string($july_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , jul_l3_above_penalty='".mysqli_real_escape_string($july_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , aug_l2_penalty='".mysqli_real_escape_string($august_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , aug_l3_above_penalty='".mysqli_real_escape_string($august_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , sep_l2_penalty='".mysqli_real_escape_string($september_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , sep_l3_above_penalty='".mysqli_real_escape_string($september_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , oct_l2_penalty='".mysqli_real_escape_string($october_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , oct_l3_above_penalty='".mysqli_real_escape_string($october_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , nov_l2_penalty='".mysqli_real_escape_string($november_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , nov_l3_above_penalty='".mysqli_real_escape_string($november_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , dec_l2_penalty='".mysqli_real_escape_string($december_l2_penalty)."'";
					$sqlbranchdealerupdate .= " , dec_l3_above_penalty='".mysqli_real_escape_string($december_l3_above_penalty)."'";
					$sqlbranchdealerupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$dns_branch_code."' AND year='2021'";
					mysqli_query($link,$sqlbranchdealerupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in branch level penalty.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/SIS summary.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/SIS summary.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			//$data="";
			$data = array();
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
						//$data[]=$value;
						array_push($data,$value);
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   //$data[]=$value;
			   array_push($data,$value);
			  //print_r($data);
				$emp_code=trim($data[0]);
				$name=trim($data[1]);
				$month_year=trim($data[2]);
				$sales_volume_MT=trim($data[3]);
				$sales_volume_TGT=trim($data[4]);
				$sales_volume_ACH=trim($data[5]);
				$sales_volume_ACH_percent=trim($data[6]);
				$sales_volume_WGT_percent=trim($data[7]);
				$sales_volume_SCORE_percent=trim($data[8]);
				$monthly_unique_visit=trim($data[9]);
				$monthly_unique_visit_TGT=trim($data[10]);
				$monthly_unique_visit_ACH=trim($data[11]);
				$monthly_unique_visit_ACH_percent=trim($data[12]);
				$monthly_unique_visit_WGT_percent=trim($data[13]);
				$monthly_unique_visit_SCORE_percent=trim($data[14]);
				$dealer_appointment=trim($data[15]);
				$dealer_appointment_TGT=trim($data[16]);
				$dealer_appointment_ACH=trim($data[17]);
				$dealer_appointment_ACH_percent=trim($data[18]);
				$dealer_appointmen_WGT_percent=trim($data[19]);
				$dealer_appointment_SCORE_percent=trim($data[20]);
				$active_dealer_count=trim($data[21]);
				$active_dealer_count_TGT=trim($data[22]);
				$active_dealer_count_ACH=trim($data[23]);
				$active_dealer_count_ACH_percent=trim($data[24]);
				$active_dealer_count_WGT_percent=trim($data[25]);
				$active_dealer_count_SCORE_percent=trim($data[26]);
				
				
					$paramiter_five=trim($data[27]);
				$five_TGT=trim($data[28]);
				$five_ACH=trim($data[29]);
				$five_ACH_percent=trim($data[30]);
				$five_WGT_percent=trim($data[31]);
				$five_SCORE_percent=trim($data[32]);
				
				
					$paramiter_six=trim($data[33]);
				$six_TGT=trim($data[34]);
				$six_ACH=trim($data[35]);
				$six_ACH_percent=trim($data[36]);
				$six_WGT_percent=trim($data[37]);
				$six_SCORE_percent=trim($data[38]);
				
				
				
				$earning_score_percent=trim($data[39]);
				$penalty_percent=trim($data[40]);
				$final_score_percent=trim($data[41]);
				$OTSI=trim($data[42]);
				$SIS_earning_month=trim($data[43]);
				$remarks=trim($data[44]);
				
				$sqlchkempcode="SELECT emp_code FROM SIS_summary WHERE emp_code='".addslashes($emp_code)."' AND month_year='".addslashes($month_year)."'";
				$rschkempcode=mysqli_query($link,$sqlchkempcode);
				$countchkempcode=mysqli_num_rows($rschkempcode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchkempcode ==0)
				{
					$sqlinsis  = "insert into SIS_summary SET ";
					$sqlinsis .= "   emp_code='".mysqli_real_escape_string($link,$emp_code)."'";
					$sqlinsis .= "  ,name='".mysqli_real_escape_string($link,$name)."'";
					$sqlinsis .= "  , month_year='".addslashes($month_year)."'";
					$sqlinsis .= " , sales_volume_MT='".mysqli_real_escape_string($link,$sales_volume_MT)."'";
					$sqlinsis .= " , sales_volume_TGT='".mysqli_real_escape_string($link,$sales_volume_TGT)."'";
					$sqlinsis .= " , sales_volume_ACH='".mysqli_real_escape_string($link,$sales_volume_ACH)."'";
					$sqlinsis .= " , sales_volume_ACH_percent='".mysqli_real_escape_string($link,$sales_volume_ACH_percent)."'";
					$sqlinsis .= " , sales_volume_WGT_percent='".mysqli_real_escape_string($link,$sales_volume_WGT_percent)."'";
					$sqlinsis .= " , sales_volume_SCORE_percent='".mysqli_real_escape_string($link,$sales_volume_SCORE_percent)."'";
					$sqlinsis .= " , monthly_unique_visit='".mysqli_real_escape_string($link,$monthly_unique_visit)."'";
					$sqlinsis .= " , monthly_unique_visit_TGT='".mysqli_real_escape_string($link,$monthly_unique_visit_TGT)."'";
					$sqlinsis .= " , monthly_unique_visit_ACH='".mysqli_real_escape_string($link,$monthly_unique_visit_ACH)."'";
					$sqlinsis .= " , monthly_unique_visit_ACH_percent='".mysqli_real_escape_string($link,$monthly_unique_visit_ACH_percent)."'";
					$sqlinsis .= " , monthly_unique_visit_WGT_percent='".mysqli_real_escape_string($link,$monthly_unique_visit_WGT_percent)."'";
					$sqlinsis .= " , monthly_unique_visit_SCORE_percent='".mysqli_real_escape_string($link,$monthly_unique_visit_SCORE_percent)."'";
					$sqlinsis .= " , dealer_appointment='".mysqli_real_escape_string($link,$dealer_appointment)."'";
					$sqlinsis .= " , dealer_appointment_TGT='".mysqli_real_escape_string($link,$dealer_appointment_TGT)."'";
					$sqlinsis .= " , dealer_appointment_ACH='".mysqli_real_escape_string($link,$dealer_appointment_ACH)."'";
					$sqlinsis .= " , dealer_appointment_ACH_percent='".mysqli_real_escape_string($link,$dealer_appointment_ACH_percent)."'";
					$sqlinsis .= " , dealer_appointmen_WGT_percent='".mysqli_real_escape_string($link,$dealer_appointmen_WGT_percent)."'";
					$sqlinsis .= " , dealer_appointment_SCORE_percent='".mysqli_real_escape_string($link,$dealer_appointment_SCORE_percent)."'";
					$sqlinsis .= " , active_dealer_count='".mysqli_real_escape_string($link,$active_dealer_count)."'";
					$sqlinsis .= " , active_dealer_count_TGT='".mysqli_real_escape_string($link,$active_dealer_count_TGT)."'";
					$sqlinsis .= " , active_dealer_count_ACH='".mysqli_real_escape_string($link,$active_dealer_count_ACH)."'";
					$sqlinsis .= " , active_dealer_count_ACH_percent='".mysqli_real_escape_string($link,$active_dealer_count_ACH_percent)."'";
					$sqlinsis .= " , active_dealer_count_WGT_percent='".mysqli_real_escape_string($link,$active_dealer_count_WGT_percent)."'";
					$sqlinsis .= " , active_dealer_count_SCORE_percent='".mysqli_real_escape_string($link,$active_dealer_count_SCORE_percent)."'";
					
					
					$sqlinsis .= " , paramiter_five='".mysqli_real_escape_string($link,$paramiter_five)."'";
					$sqlinsis .= " , five_TGT='".mysqli_real_escape_string($link,$five_TGT)."'";
					$sqlinsis .= " , five_ACH='".mysqli_real_escape_string($link,$five_ACH)."'";
					$sqlinsis .= " , five_ACH_percent='".mysqli_real_escape_string($link,$five_ACH_percent)."'";
					$sqlinsis .= " , five_WGT_percent='".mysqli_real_escape_string($link,$five_WGT_percent)."'";
					$sqlinsis .= " , five_SCORE_percent='".mysqli_real_escape_string($link,$five_SCORE_percent)."'";
					
					$sqlinsis .= " , paramiter_six='".mysqli_real_escape_string($link,$paramiter_six)."'";
					$sqlinsis .= " , six_TGT='".mysqli_real_escape_string($link,$six_TGT)."'";
					$sqlinsis .= " , six_ACH='".mysqli_real_escape_string($link,$six_ACH)."'";
					$sqlinsis .= " , six_ACH_percent='".mysqli_real_escape_string($link,$six_ACH_percent)."'";
					$sqlinsis .= " , six_WGT_percent='".mysqli_real_escape_string($link,$six_WGT_percent)."'";
					$sqlinsis .= " , six_SCORE_percent='".mysqli_real_escape_string($link,$six_SCORE_percent)."'";
					
					
					
					$sqlinsis .= " , earning_score_percent='".mysqli_real_escape_string($link,$earning_score_percent)."'";
					$sqlinsis .= " , penalty_percent='".mysqli_real_escape_string($link,$penalty_percent)."'";
					$sqlinsis .= " , final_score_percent='".mysqli_real_escape_string($link,$final_score_percent)."'";
					$sqlinsis .= " , OTSI='".mysqli_real_escape_string($link,$OTSI)."'";
					$sqlinsis .= " , SIS_earning_month='".mysqli_real_escape_string($link,$SIS_earning_month)."'";
					$sqlinsis .= " , remarks='".mysqli_real_escape_string($link,$remarks)."'";
					$sqlinsis .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsis) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in sis summary.csv.Please check.");
				}
				else
				{
					$sqlupdate  = "UPDATE SIS_summary SET ";
					$sqlupdate .= " sales_volume_MT='".mysqli_real_escape_string($link,$sales_volume_MT)."'";
					$sqlupdate .= " , name='".mysqli_real_escape_string($link,$name)."'";
					$sqlupdate .= " , sales_volume_TGT='".mysqli_real_escape_string($link,$sales_volume_TGT)."'";
					$sqlupdate .= " , sales_volume_ACH='".mysqli_real_escape_string($link,$sales_volume_ACH)."'";
					$sqlupdate .= " , sales_volume_ACH_percent='".mysqli_real_escape_string($link,$sales_volume_ACH_percent)."'";
					$sqlupdate .= " , sales_volume_WGT_percent='".mysqli_real_escape_string($link,$sales_volume_WGT_percent)."'";
					$sqlupdate .= " , sales_volume_SCORE_percent='".mysqli_real_escape_string($link,$sales_volume_SCORE_percent)."'";
					$sqlupdate .= " , monthly_unique_visit='".mysqli_real_escape_string($link,$monthly_unique_visit)."'";
					$sqlupdate .= " , monthly_unique_visit_TGT='".mysqli_real_escape_string($link,$monthly_unique_visit_TGT)."'";
					$sqlupdate .= " , monthly_unique_visit_ACH='".mysqli_real_escape_string($link,$monthly_unique_visit_ACH)."'";
					$sqlupdate .= " , monthly_unique_visit_ACH_percent='".mysqli_real_escape_string($link,$monthly_unique_visit_ACH_percent)."'";
					$sqlupdate .= " , monthly_unique_visit_WGT_percent='".mysqli_real_escape_string($link,$monthly_unique_visit_WGT_percent)."'";
					$sqlupdate .= " , monthly_unique_visit_SCORE_percent='".mysqli_real_escape_string($link,$monthly_unique_visit_SCORE_percent)."'";
					$sqlupdate .= " , dealer_appointment='".mysqli_real_escape_string($link,$dealer_appointment)."'";
					$sqlupdate .= " , dealer_appointment_TGT='".mysqli_real_escape_string($link,$dealer_appointment_TGT)."'";
					$sqlupdate .= " , dealer_appointment_ACH='".mysqli_real_escape_string($link,$dealer_appointment_ACH)."'";
					$sqlupdate .= " , dealer_appointment_ACH_percent='".mysqli_real_escape_string($link,$dealer_appointment_ACH_percent)."'";
					$sqlupdate .= " , dealer_appointmen_WGT_percent='".mysqli_real_escape_string($link,$dealer_appointmen_WGT_percent)."'";
					$sqlupdate .= " , dealer_appointment_SCORE_percent='".mysqli_real_escape_string($link,$dealer_appointment_SCORE_percent)."'";
					$sqlupdate .= " , active_dealer_count='".mysqli_real_escape_string($link,$active_dealer_count)."'";
					$sqlupdate .= " , active_dealer_count_TGT='".mysqli_real_escape_string($link,$active_dealer_count_TGT)."'";
					$sqlupdate .= " , active_dealer_count_ACH='".mysqli_real_escape_string($link,$active_dealer_count_ACH)."'";
					$sqlupdate .= " , active_dealer_count_ACH_percent='".mysqli_real_escape_string($link,$active_dealer_count_ACH_percent)."'";
					$sqlinsis .= " , active_dealer_count_WGT_percent='".mysqli_real_escape_string($link,$active_dealer_count_WGT_percent)."'";
					$sqlupdate .= " , active_dealer_count_SCORE_percent='".mysqli_real_escape_string($link,$active_dealer_count_SCORE_percent)."'";
					
					
					
						
					$sqlupdate .= " , paramiter_five='".mysqli_real_escape_string($link,$paramiter_five)."'";
					$sqlupdate .= " , five_TGT='".mysqli_real_escape_string($link,$five_TGT)."'";
					$sqlupdate .= " , five_ACH='".mysqli_real_escape_string($link,$five_ACH)."'";
					$sqlupdate .= " , five_ACH_percent='".mysqli_real_escape_string($link,$five_ACH_percent)."'";
					$sqlupdate .= " , five_WGT_percent='".mysqli_real_escape_string($link,$five_WGT_percent)."'";
					$sqlupdate .= " , five_SCORE_percent='".mysqli_real_escape_string($link,$five_SCORE_percent)."'";
					
					$sqlupdate .= " , paramiter_six='".mysqli_real_escape_string($link,$paramiter_six)."'";
					$sqlupdate .= " , six_TGT='".mysqli_real_escape_string($link,$six_TGT)."'";
					$sqlupdate .= " , six_ACH='".mysqli_real_escape_string($link,$six_ACH)."'";
					$sqlupdate .= " , six_ACH_percent='".mysqli_real_escape_string($link,$six_ACH_percent)."'";
					$sqlupdate .= " , six_WGT_percent='".mysqli_real_escape_string($link,$six_WGT_percent)."'";
					$sqlupdate .= " , six_SCORE_percent='".mysqli_real_escape_string($link,$six_SCORE_percent)."'";
					
					
					
					
					
					$sqlupdate .= " , earning_score_percent='".mysqli_real_escape_string($link,$earning_score_percent)."'";
					$sqlupdate .= " , penalty_percent='".mysqli_real_escape_string($link,$penalty_percent)."'";
					$sqlupdate .= " , final_score_percent='".mysqli_real_escape_string($link,$final_score_percent)."'";
					$sqlupdate .= " , OTSI='".mysqli_real_escape_string($link,$OTSI)."'";
					$sqlupdate .= " , SIS_earning_month='".mysqli_real_escape_string($link,$SIS_earning_month)."'";
					$sqlupdate .= " , remarks='".mysqli_real_escape_string($link,$remarks)."'";
					$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".addslashes($emp_code)."' AND month_year='".addslashes($month_year)."'";
					mysqli_query($link,$sqlupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in sis summary.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/BRANCH WISE COMPETITOR REPLACE.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/BRANCH WISE COMPETITOR REPLACE.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
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

				$branch_code_name=trim($data[0]);
				$brand_name=trim($data[1]);
				$new_brand_name=trim($data[2]);
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranchdestinationfreight="SELECT branch_code FROM competitor_group_master WHERE branch_code='".addslashes($branch_code)."' 
											AND competitor_name	='".addslashes($brand_name)."' ";
				$rsbranchdestinationfreight=mysqli_query($link,$sqlbranchdestinationfreight);
				$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);
				if($countbranchdestinationfreight > 0)
					{
						$sqlbranchdestinationfreightupd  = "update competitor_group_master ";
						$sqlbranchdestinationfreightupd .= " SET display_name='".$new_brand_name."'";
						$sqlbranchdestinationfreightupd .= "  WHERE branch_code='".addslashes($branch_code)."' AND competitor_name='".addslashes($brand_name)."'";
						mysqli_query($link,$sqlbranchdestinationfreightupd);
					}
			   }
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Company master.csv is wrong.";
		exit();
	}*/
	if($successval==1)
	{
	/*	$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();*/
		if($nick_name =='STAR')
		{
			//$mailto='sumansaha@cmcl.co.in';
			  $mailto='warroom@starcement.co.in,emovesfa@starcement.co.in,kishukeshav@starcement.co.in';
		}
		else
		{
			$mailto='';
		}
		if(count($error_array)>0)
		{
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$date.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':'.$second;
			$mailsub='Data has been successfully uploaded to '.$nick_name.' with error(s) on '.$contentsdatetime;
			$mailbody='Data has been successfully uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
			for($i=0;$i<count($error_array);$i++){
				$mailbody.= "<b>$error_array[$i]</b><br /><br />";
			}	
		}
		else{
			$mailsub='Data has been successfully uploaded to '.$nick_name.' on '.date('d-m-Y H:i:s');
			$mailbody='Data has been successfully uploaded to '.$nick_name.' database.';	
		}
		//if(mail($mailto, $mailsub, $mailbody, $headers,'-facedns@coral.in'))
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
			disphtml("main();");
		}
		/*else
		{
			echo $GLOBALS['msg'] = "Error in mail sending.";
			disphtml("main();");
		}*/
		//echo $err = 'Zip file extracted and data has been uploaded successfully';
	}
	else 
	{
		echo $GLOBALS['msg'] = "Problem with uploading Zip file";
		disphtml("main();");
	}
 }
?>