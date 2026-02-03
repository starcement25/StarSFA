<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if($_REQUEST['mode']=="csv_upload")				csv_upload();
	else    										disphtml("main();");
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
	if(similar_file_exists("../csv/$folderName/warehouse master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/warehouse master.csv");
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
				$emp_code=trim($data[0]);
				if(strpos($emp_code,';')!=false)
				 {
					$emp_code=str_replace(';',',',$emp_code);
				 }
				 $emp_code_array=explode(',',$emp_code);
				 $emp_code_string='';
				 foreach($emp_code_array as $emp_code_value)
				 {
					 /*$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_value)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=mysqli_fetch_assoc($rsempcode);
					$emp_code_val=$rowempcode['emp_code'];*/
					$emp_code_string=$emp_code_string."'".$emp_code_value."'".',';
				 }
				 $emp_code_string=substr($emp_code_string,0,-1);
					$warehouse_code=trim($data[1]);
					$warehouse=trim($data[2]);
					$diversion=trim($data[3]);
					$coordinator=trim($data[4]);
					$incharge=trim($data[5]);
					$zone=trim($data[6]);
					$godown_open_time=trim($data[7]);
					$godown_close_time=trim($data[8]);
					$hours_format=trim($data[9]);
					$status=trim($data[10]);
				
				
				
				$sqlchkcode="SELECT emp_code FROM warehouse_master WHERE warehouse_code='".addslashes($warehouse_code)."'";
				$rschkcode=mysqli_query($link,$sqlchkcode);
				$countchkcode=mysqli_num_rows($rschkcode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
					if($countchkcode ==0)
					{
						$sqlinwarehouse  = "insert into warehouse_master SET ";
						$sqlinwarehouse .= "   emp_code='".mysqli_real_escape_string($emp_code_string)."'";
						$sqlinwarehouse .= "  ,warehouse='".mysqli_real_escape_string($warehouse)."'";
						$sqlinwarehouse .= "  ,warehouse_code='".mysqli_real_escape_string($warehouse_code)."'";
						$sqlinwarehouse .= "  , diversion='".addslashes($diversion)."'";
						$sqlinwarehouse .= " , coordinator='".mysqli_real_escape_string($coordinator)."'";
						$sqlinwarehouse .= " , incharge='".mysqli_real_escape_string($incharge)."'";
						$sqlinwarehouse .= " , zone='".mysqli_real_escape_string($zone)."'";
						$sqlinwarehouse .= " , godown_open_time='".mysqli_real_escape_string($godown_open_time)."'";
						$sqlinwarehouse .= " , godown_close_time='".mysqli_real_escape_string($godown_close_time)."'";
						$sqlinwarehouse .= " , hours_format='".mysqli_real_escape_string($hours_format)."'";
						$sqlinwarehouse .= " , is_active='".mysqli_real_escape_string($status)."'";
						$sqlinwarehouse .= " , upload_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinwarehouse) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in warehuse master.csv.Please check.");
					}
					else
					{
						$sqlupdate  = "UPDATE warehouse_master ";
						$sqlupdate .= "  SET warehouse='".mysqli_real_escape_string($warehouse)."'";
						$sqlupdate .= "  , emp_code='".addslashes($emp_code_string)."'";
						$sqlupdate .= "  , diversion='".addslashes($diversion)."'";
						$sqlupdate .= " , coordinator='".mysqli_real_escape_string($coordinator)."'";
						$sqlupdate .= " , incharge='".mysqli_real_escape_string($incharge)."'";
						$sqlupdate .= " , zone='".mysqli_real_escape_string($zone)."'";
						$sqlupdate .= " , godown_open_time='".mysqli_real_escape_string($godown_open_time)."'";
						$sqlupdate .= " , godown_close_time='".mysqli_real_escape_string($godown_close_time)."'";
						$sqlupdate .= " , hours_format='".mysqli_real_escape_string($hours_format)."'";
						$sqlupdate .= " , is_active='".mysqli_real_escape_string($status)."'";
						$sqlupdate .= " , upload_date_time=CURRENT_TIMESTAMP() WHERE warehouse_code='".addslashes($warehouse_code)."'";
						mysqli_query($link,$sqlupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in warehuse master.csv.Please check.");
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
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
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