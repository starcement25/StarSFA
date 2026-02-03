<?php	
set_time_limit(1000);

error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
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
}
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
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
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
	$folderName = $_REQUEST['nick_name'];
	$error_array=array();
	if ( !file_exists("csv/$folderName")){
		mkdir("csv/$folderName");
		chmod("csv/$folderName", 0777);
	}
	// Get array of all source files
	$files = scandir("csv/$folderName");
	// Identify directories
	$source = "csv/$folderName/";
	$destination = "csv/$folderName/filebkup/";
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

	$upload_dir="csv/$folderName/";
	if(file_exists($_FILES['zip_file']['tmp_name']))
	{
		$file_name = $_FILES['zip_file']['name'];
		$tmp_name=$_FILES['zip_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		
		 move_uploaded_file($tmp_name,$upload_file);
		$zip = new ZipArchive;
		if ($zip->open($upload_file)) {
			
			$zip->extractTo("csv/$folderName/");
			$zip->close();
		} 
	 }
		//For Retail stores csv
	foreach ($files as $file)
	{
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if($ext=='csv')
		{
		 $filename=similar_file_exists("csv/$folderName/$file");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$row_id_array=array();
		$survey_id_array=array();
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
					$reporting_to_val='';
					$branch_code='';
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
					if($rec_count==1)
					{
						foreach($data as $datarowid)
						{
						  if($datarowid!='')
						  {
						  	array_push($row_id_array,$datarowid);
						  }
						}
						$sql_menu_id = "SELECT menu_id,survey_type FROM survey_input WHERE row_id = '".$data[1]."'";
						$res_menu_id = mysqli_query($link,$sql_menu_id);
						$row_menu_id = mysqli_fetch_assoc($res_menu_id);
						$menu_id = $row_menu_id['menu_id'];
						$survey_type = $row_menu_id['survey_type'];
					}
					else
					{
						for($datacnt=0;$datacnt<count($row_id_array);$datacnt++)
						{
						   $sqlchksurveypublish="SELECT survey_id FROM survey_publish WHERE survey_id='".$data[0]."' AND 
						   						row_id='".preg_replace('/[\r\n]+/', '',$row_id_array[$datacnt])."'";
						   $rschksurveypublish=mysqli_query($link,$sqlchksurveypublish);
						   $cntchksurveypublish=mysqli_num_rows($rschksurveypublish);
						   if($cntchksurveypublish==0){				
							  $sqlinsertsurveypublish="INSERT INTO survey_publish SET survey_id='".$data[0]."',
														row_id='".preg_replace('/[\r\n]+/', '',$row_id_array[$datacnt])."',
														value='".addslashes(preg_replace('/[\r\n]+/', '',$data[$datacnt+1]))."'";
							   mysqli_query($link,$sqlinsertsurveypublish);
						   }
						  if($row_id_array[$datacnt]=='RA055' || $row_id_array[$datacnt]=='RA183'){
						 	  //echo $data[0];
							  if($data[0]!=''){
							   array_push($survey_id_array,$data[0]);
							  }
							}
						}
					}
					// Start For survey header data creation	
					$sql_survey_menu = "SELECT layout_name FROM survey_input WHERE menu_id = '".$menu_id."' AND type = 'menu'";
					$res_survey_menu = mysqli_query($link,$sql_survey_menu);
					$row_survey_menu = mysqli_fetch_assoc($res_survey_menu);
					$survey_menu = $row_survey_menu['layout_name'];
					
					$sql_question_answered = "SELECT COUNT(survey_id) FROM survey_publish WHERE survey_id = '".$data[0]."' AND value != ''";
					$res_question_answered = mysqli_query($link,$sql_question_answered);
					$row_question_answered = mysqli_fetch_assoc($res_question_answered);
					$total_answered = $row_question_answered['COUNT(survey_id)'];
					
				    if($survey_type == 'mall'){
					$column_name_value = 'Mall Name';
					
					if($survey_menu == 'Retail Stores'){
						$display_name = 'Business Name';
						$contact_info_first_name = 'First Name';
						$contact_info_last_name = 'Last Name';
						$phone_no = 'Mobile Number#OTP';
						
						/*--------> Get Row id Of Contact First Name <--------*/
						$sql_contact_row_id = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$contact_info_first_name."%' AND survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
						$res_contact_row_id = mysqli_query($link,$sql_contact_row_id);
						$row_contact_row_id = mysqli_fetch_assoc($res_contact_row_id);
						$disp_contact_row_id = $row_contact_row_id['row_id'];
						
						$sql_contact_name = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_contact_row_id."'";
						$res_contact_name = mysqli_query($link,$sql_contact_name);
						$row_contact_name = mysqli_fetch_assoc($res_contact_name);
						$contact_first_name = $row_contact_name['value'];
						
						/*--------> Get Row id Of Contact Last Name <--------*/
						$sql_contact_row_id_last = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$contact_info_last_name."%' AND survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
						$res_contact_row_id_last = mysqli_query($link,$sql_contact_row_id_last);
						$row_contact_row_id_last = mysqli_fetch_assoc($res_contact_row_id_last);
						$disp_contact_row_id_last = $row_contact_row_id_last['row_id'];
						
						$sql_contact_name_last = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_contact_row_id_last."'";
						$res_contact_name_last = mysqli_query($link,$sql_contact_name_last);
						$row_contact_name_last = mysqli_fetch_assoc($res_contact_name_last);
						$contact_last_name = $row_contact_name_last['value'];
						
						$contact_name = $contact_first_name." ".$contact_last_name;
						
						/*--------> Get Row id Of Mobile <--------*/
						$sql_phone_row_id = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$phone_no."%' AND survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
						$res_phone_row_id = mysqli_query($link,$sql_phone_row_id);
						$row_phone_row_id = mysqli_fetch_assoc($res_phone_row_id);
						$disp_phone_row_id = $row_phone_row_id['row_id'];
						
						$sql_phone = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_phone_row_id."'";
						$res_phone = mysqli_query($link,$sql_phone);
						$row_phone = mysqli_fetch_assoc($res_phone);
						$phone = $row_phone['value'];
					}
					else if($survey_menu == 'Food Joints'){
						$display_name = 'Restaurant Name';
					}
					else if($survey_menu == 'Spa & Salons'){
						$display_name = 'Name';
					}
					else if($survey_menu == 'Movie Theatre'){
						$display_name = 'Name';
					}
					else if($survey_menu == 'Games & Other Entertainment'){
						$display_name = 'Name';
					}
					else if($survey_menu == 'ATM service'){
						$display_name = 'Bank Name';
					}
					else if($survey_menu == 'Others'){
						$display_name = 'Name';
					}
				}
				else if($survey_type == 'hi-street'){
					$column_name_value = 'Area';
					
					if($survey_menu == 'Retail Stores'){
						$display_name = 'Business Name';
						$contact_info_first_name = 'First Name';
						$contact_info_last_name = 'Last Name';
						$phone_no = 'Mobile Number#OTP';
						
						/*--------> Get Row id Of Contact First Name <--------*/
						$sql_contact_row_id = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$contact_info_first_name."%' AND survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
						$res_contact_row_id = mysqli_query($link,$sql_contact_row_id);
						$row_contact_row_id = mysqli_fetch_assoc($res_contact_row_id);
						$disp_contact_row_id = $row_contact_row_id['row_id'];
						
						$sql_contact_name = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_contact_row_id."'";
						$res_contact_name = mysqli_query($link,$sql_contact_name);
						$row_contact_name = mysqli_fetch_assoc($res_contact_name);
						$contact_first_name = $row_contact_name['value'];
						
						/*--------> Get Row id Of Contact Last Name <--------*/
						$sql_contact_row_id_last = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$contact_info_last_name."%' AND survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
						$res_contact_row_id_last = mysqli_query($link,$sql_contact_row_id_last);
						$row_contact_row_id_last = mysqli_fetch_assoc($res_contact_row_id_last);
						$disp_contact_row_id_last = $row_contact_row_id_last['row_id'];
						
						$sql_contact_name_last = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_contact_row_id_last."'";
						$res_contact_name_last = mysqli_query($link,$sql_contact_name_last);
						$row_contact_name_last = mysqli_fetch_assoc($res_contact_name_last);
						$contact_last_name = $row_contact_name_last['value'];
						
						$contact_name = $contact_first_name." ".$contact_last_name;
						
						/*--------> Get Row id Of Mobile <--------*/
						$sql_phone_row_id = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$phone_no."%' AND survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
						$res_phone_row_id = mysqli_query($link,$sql_phone_row_id);
						$row_phone_row_id = mysqli_fetch_assoc($res_phone_row_id);
						$disp_phone_row_id = $row_phone_row_id['row_id'];
						
						$sql_phone = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_phone_row_id."'";
						$res_phone = mysqli_query($link,$sql_phone);
						$row_phone = mysqli_fetch_assoc($res_phone);
						$phone = $row_phone['value'];
					}
					else if($survey_menu == 'Spa & Salons'){
						$display_name = 'Name';
					}
				}
			 $sql_name_row_id = "SELECT row_id FROM survey_input WHERE display_name LIKE '%".$display_name."%' AND 
			  					survey_type = '".$survey_type."' AND menu_id = '".$menu_id."'";
			$res_name_row_id = mysqli_query($link,$sql_name_row_id);
			$row_name_row_id = mysqli_fetch_assoc($res_name_row_id);
			$disp_name_row_id = $row_name_row_id['row_id'];
			
			$sql_business_name = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$disp_name_row_id."'";
			$res_business_name = mysqli_query($link,$sql_business_name);
			$row_business_name = mysqli_fetch_assoc($res_business_name);
			$business_name = $row_business_name['value'];
			
			$sql_mall_hs_id = "SELECT row_id FROM survey_input WHERE menu_id = '".$menu_id."' AND display_name LIKE '%".$column_name_value."%'";
			$res_mall_hs_id = mysqli_query($link,$sql_mall_hs_id);
			$row_mall_hs_id = mysqli_fetch_assoc($res_mall_hs_id);
			$mall_hs_id = $row_mall_hs_id['row_id'];
				
			$sql_get_mall_hs_name = "SELECT value FROM survey_publish WHERE survey_id = '".$data[0]."' AND row_id = '".$mall_hs_id."'";
			$res_get_mall_hs_name = mysqli_query($link,$sql_get_mall_hs_name);
			$row_get_mall_hs_name = mysqli_fetch_assoc($res_get_mall_hs_name);
			$mall_hs_name = $row_get_mall_hs_name['value'];
	
			for($timmevariable=1;$timmevariable<=4;$timmevariable++){
				if($timmevariable == 1){
					$time_condition = " (SUBSTRING(survey_id,-6) BETWEEN 090000 AND 120000) ";
					$sql_time_slot = "SELECT row_id FROM survey_publish WHERE ".$time_condition." AND survey_id = '".$data[0]."' LIMIT 0,1";
					$res_time_slot = mysqli_query($link,$sql_time_slot);
					$time_slot_row = mysqli_num_rows($res_time_slot);
					if($time_slot_row>0){
						$flag = 1;
					}
				}
				else if($timmevariable == 2){
					$time_condition = " (SUBSTRING(survey_id,-6) BETWEEN 120100 AND 150000) ";
					$sql_time_slot = "SELECT row_id FROM survey_publish WHERE ".$time_condition." AND survey_id = '".$data[0]."' LIMIT 0,1";
					$res_time_slot = mysqli_query($link,$sql_time_slot);
					$time_slot_row = mysqli_num_rows($res_time_slot);
					if($time_slot_row>0){
						$flag = 2;
					}
				}
				else if($timmevariable == 3){
					$time_condition = " (SUBSTRING(survey_id,-6) BETWEEN 150100 AND 180000) ";
					$sql_time_slot = "SELECT row_id FROM survey_publish WHERE ".$time_condition." AND survey_id = '".$data[0]."' LIMIT 0,1";
					$res_time_slot = mysqli_query($link,$sql_time_slot);
					$time_slot_row = mysqli_num_rows($res_time_slot);
					if($time_slot_row>0){
						$flag = 3;
					}
				}
				else if($timmevariable == 4){
					$time_condition = " (SUBSTRING(survey_id,-6) BETWEEN 180100 AND 210000) ";
					$sql_time_slot = "SELECT row_id FROM survey_publish WHERE ".$time_condition." AND survey_id = '".$data[0]."' LIMIT 0,1";
					$res_time_slot = mysqli_query($link,$sql_time_slot);
					$time_slot_row = mysqli_num_rows($res_time_slot);
					if($time_slot_row>0){
						$flag = 4;
					}
				}
				
					if($flag == 1)
						$column_name = "time_9_to_12";
					if($flag == 2)
						$column_name = "time_12_to_3";
					if($flag == 3)
						$column_name = "time_3_to_6";
					if($flag == 4)
						$column_name = "time_6_to_9";
					
					$slot_count = 1;
				
			}
			  $sqlchk="SELECT survey_id,status FROM survey_header WHERE survey_id='".$data[0]."'";
			  /*if($rec_count==4)
			  {
				  exit();
			  }*/
			   $rschk=mysqli_query($link,$sqlchk);
			   $cntchk=mysqli_num_rows($rschk);
			   if($cntchk==0)
			   {
				   $sql_insert_survey_header = "INSERT INTO survey_header SET 
												 survey_id = '".$data[0]."', 
												 survey_type = '".$survey_type."',
													 menu_name = '".$survey_menu."', 
												  mall_hs_name = '".addslashes($mall_hs_name)."',
												 business_name = '".addslashes($business_name)."',
												  contact_name = '".addslashes($contact_name)."',
													  phone_no = '".$phone."', 
												  $column_name = '".$slot_count."', 
											questions_answered = '".$total_answered."',
													status = 'ready to publish',
												download_time=CURRENT_TIMESTAMP()";
				   $res_insert_survey_header = mysqli_query($link,$sql_insert_survey_header);
			   }
			   else
			   {
				    $rowchk=mysqli_fetch_assoc($rschk);
					$status=$rowchk['status'];
					if($status!='DCA')
					{
						$sql_update_survey_header = "UPDATE survey_header SET 
													 business_name='".addslashes($business_name)."',
													 mall_hs_name = '".addslashes($mall_hs_name)."',
													status = 'ready to publish', download_time=CURRENT_TIMESTAMP()
													WHERE survey_id = '".$data[0]."'";
					   $res_update_survey_header = mysqli_query($link,$sql_update_survey_header);
					}
			   }
			   //print_r($row_id_array);
			   // End For survey header data creation	
			 }
				 $rec_count++;
			}
			$successval=1;
			if(count($survey_id_array) >0){
				$socialwebarrayall=array('Facebook','Twitter','Instagram','Pinterest','Youtube','Google+','Linkedin');
				foreach($survey_id_array as $survey_id_val)
				{
					$sqlselectsocialweb="SELECT value from survey_publish WHERE (row_id='RA055' OR row_id='RA183') and survey_id='".$survey_id_val."'";
					$rsselectsocialweb=mysqli_query($link,$sqlselectsocialweb);
					$rowselectsocialweb=mysqli_fetch_assoc($rsselectsocialweb);
					$socialweb=$rowselectsocialweb['value'];
					$socialwebarray=explode(';',$socialweb);
					$socilwebstring='';
					foreach($socialwebarray as $socialwebval)
					{
						$sqlselectsocialwebpartval="SELECT value from survey_publish WHERE row_id='".trim($socialwebval)."' and 
													survey_id='".$survey_id_val."' ";
						$rsselectsocialwebpartval=mysqli_query($link,$sqlselectsocialwebpartval);
						$rowselectsocialwebpartval=mysqli_fetch_assoc($rsselectsocialwebpartval);
						$socialwebpartval=$rowselectsocialwebpartval['value'];
						$socilwebstring.=trim($socialwebval).'#'.trim($socialwebpartval).';';
					}
					foreach($socialwebarrayall as $socialwebvalall)
					{
						$sqldel="DELETE FROM survey_publish WHERE row_id='".trim($socialwebvalall)."' and survey_id='".$survey_id_val."'";
						mysqli_query($link,$sqldel);
					}
					//$socilwebstring=substr($socilwebstring,0,-1);
					
					$sqlupdatesurveypublish="UPDATE survey_publish SET value='".$socilwebstring."' WHERE (row_id='RA055' OR row_id='RA183') 
											and survey_id='".$survey_id_val."'";
					mysqli_query($link,$sqlupdatesurveypublish);
				}
			}
		  }
		}
		/*else
		{
			echo $successval="Naming convention for Employee Master.csv is wrong.";
			exit();
		}*/

	if($successval==1)
	{
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
		 main();
	}
	else 
	{
		echo $GLOBALS['msg'] = "Problem with uploading Zip file";
		main();
	}
}
?>