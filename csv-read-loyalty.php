<?php	
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");
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
	if ( !file_exists($folderName)){
		mkdir("csv/$folderName");
		chmod("csv/$folderName", 0777);
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
 
    //For Company Master CSV
	if(similar_file_exists("csv/$folderName/loyalty_card_holder_master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/loyalty_card_holder_master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		$sqldelete="truncate loyalty_card_holder_master";
		$rsdelete=mysqli_query($link,$sqldelete);
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
			  
				$loyalty_card_holder_code=trim($data[0]);
				$loyalty_card_holder_name=trim($data[1]);
				$loyalty_card_no=trim($data[2]);
				$card_type=trim($data[3]);
				$total_purchase_value=trim($data[4]);
				$total_reward_point=trim($data[5]);
				$last_updated_on=trim($data[6]);
				$redeemed=trim($data[7]);
				
				$csv_row_count=$rec_count+1;
				$sqlcardholder  = "insert into loyalty_card_holder_master SET ";
				$sqlcardholder .= "  loyalty_card_holder_code='".mysqli_escape_string($loyalty_card_holder_code)."'";
				$sqlcardholder .= " , loyalty_card_holder_name='".mysqli_escape_string($loyalty_card_holder_name)."'";
				$sqlcardholder .= " , loyalty_card_no='".mysqli_escape_string($loyalty_card_no)."'";
				$sqlcardholder .= " , card_type='".mysqli_escape_string($card_type)."'";
				$sqlcardholder .= " , total_purchase_value='".mysqli_escape_string($total_purchase_value)."'";
				$sqlcardholder .= " , total_reward_point='".mysqli_escape_string($total_reward_point)."'";
				$sqlcardholder .= " , last_update_on='".mysqli_escape_string($last_update_on)."'";
				$sqlcardholder .= " , redeemed='".mysqli_escape_string($redeemed)."'";
				mysqli_query($link,$sqlcardholder) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Loyalty card holder code and Loyalty  card no columns in loyalty_card_holder_master.csv.Please check.");
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Company master.csv is wrong.";
		exit();
	}	*/
	
	//For Branch Master CSV
	if(similar_file_exists("csv/$folderName/outlet_master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/outlet_master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		$sqldelete="truncate outlet_master";
		$rsdelete=mysqli_query($link,$sqldelete);
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
			  
				$outlet_code=trim($data[0]);
				$outlet_name=trim($data[1]);
				$csv_row_count=$rec_count+1;
				
				$sqloutlet  = "insert into outlet_master SET ";
				$sqloutlet .= "  	outlet_code='".mysqli_escape_string($outlet_code)."'";
				$sqloutlet .= " , outlet_name='".mysqli_escape_string($outlet_name)."'";
				
				mysqli_query($link,$sqloutlet) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Outlet code column in outlet_master.csv.Please check.");;
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

	//For Category Master CSV
	if(similar_file_exists("csv/$folderName/outlet_user_master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/outlet_user_master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			$sqldelete="truncate outlet_user_master";
			$rsdelete=mysqli_query($link,$sqldelete);
	
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
				  	$outlet_code=trim($data[0]);
					$outlet_user_code=trim($data[1]);
					$outlet_user_name=trim($data[2]);
					
					$csv_row_count=$rec_count+1;
					$sqloutletuser  = "insert into outlet_user_master SET ";
					$sqloutletuser .= "  outlet_code='".mysqli_escape_string($outlet_code)."'";
					$sqloutletuser .= " , outlet_user_code='".mysqli_escape_string($outlet_user_code)."'";
					$sqloutletuser .= " , outlet_user_name='".mysqli_escape_string($outlet_user_name)."'";
					$sqloutletuser .= " ,  newpassword='1234'";
					$sqloutletuser .= " ,  oldpassword='1234'";
					
					mysqli_query($link,$sqloutletuser) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on outlet_code
 and outlet_user_code columns in outlet_user_master.csv.Please check.");
				}
				 $rec_count++;
			}		
			$successval=1;
		}
			
	if($successval==1)
	{
			$nick_name=$_REQUEST['nick_name'];
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			$mailto='kuntald@coral.in';
		
			if(count($error_array)>0)
			{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' for loyalty with error(s) on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database for loyalty with the following error(s).<br /><br />';
				
				for($i=0;$i<count($error_array);$i++){
					$mailbody.= "<b>$error_array[$i]</b><br /><br />";
				}	
			}
			else{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' for loyalty on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database for loyalty.';	
			}
						
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
				$error_msgArr=explode('#',$GLOBALS['error_msg']);
					if(count($error_msgArr)>0){
						for($i=0;$i<count($error_msgArr);$i++){
							echo "<b>$error_msgArr[$i]</b><br /><br />";
						}
					}
				//main();
			}
			else 
			{
				echo $GLOBALS['msg'] = "Problem in mail sending.";
				//main();
			}
			//echo $err = 'Zip file extracted and data has been uploaded successfully';
		}
		else 
		{
			echo $GLOBALS['msg'] = "Problem with uploading Zip file";
			//main();
		}
	} 

?>