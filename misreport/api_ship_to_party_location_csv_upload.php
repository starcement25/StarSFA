<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
session_start();
require("adminUtils.php");
require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

if($_SESSION['admin_login']=="")  		header("location:index.php");
if($_REQUEST['mode']=="csv_upload")	csv_upload();
else    								disphtml("main();");
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
			<td colspan="10">Upload Ship To Party Location Data</td>
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
	global $conn;

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
		$delete = array();
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

	$successval = 0;

	/*
	|----------------------------------------------------------------------
	| Expected CSV filename: "ship to party location.csv"
	| Expected column order (change the $data[n] mapping below if your
	| actual CSV has a different column order):
	|   0  dns_ship_to_party_code   (required, cannot be blank)
	|   1  ship_to_party_name
	|   2  customer_type
	|   3  branch_code
	|   4  branch_name
	|   5  dealer_code              (required, cannot be blank)
	|   6  dealer_name
	|   7  latitude
	|   8  longitude
	|   9  destination_name
	|   10 status                   (blank -> defaults to 'pending')
	|   11 approve_by
	|----------------------------------------------------------------------
	*/
	if(similar_file_exists("../csv/$folderName/ship to party location.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/ship to party location.csv");
		$rec_count = 0;
		$lines = file($filename);
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data=array();
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

				$dns_ship_to_party_code = isset($data[0]) ? trim($data[0]) : '';
				$ship_to_party_name     = isset($data[1]) ? trim($data[1]) : '';
				$customer_type          = isset($data[2]) ? trim($data[2]) : '';
				$branch_code            = isset($data[3]) ? trim($data[3]) : '';
				$branch_name            = isset($data[4]) ? trim($data[4]) : '';
				$dealer_code            = isset($data[5]) ? trim($data[5]) : '';
				$dealer_name            = isset($data[6]) ? trim($data[6]) : '';
				$latitude               = isset($data[7]) ? trim($data[7]) : '';
				$longitude              = isset($data[8]) ? trim($data[8]) : '';
				$destination_name       = isset($data[9]) ? trim($data[9]) : '';
				$status                 = isset($data[10]) ? trim($data[10]) : '';
				$approve_by             = isset($data[11]) ? trim($data[11]) : '';

				if ($status === '') {
					$status = 'pending';
				}

				$csv_row_count = $rec_count + 1;

				/*
				|------------------------------------------------------------
				| Required-field check: dns_ship_to_party_code and
				| dealer_code cannot be blank. Skip the row (with a logged
				| error) rather than inserting a NULL into either.
				|------------------------------------------------------------
				*/
				if ($dns_ship_to_party_code === '' || $dealer_code === '') {
					array_push($error_array, "Row $csv_row_count skipped: dns_ship_to_party_code and dealer_code are both required.");
					$rec_count++;
					continue;
				}

				/*
				|------------------------------------------------------------
				| Uniqueness check on (dns_ship_to_party_code, dealer_code):
				| if the pair already exists, the row is rejected with an
				| error (not updated, not silently skipped).
				|------------------------------------------------------------
				*/
				$sqlchk = mysqli_prepare($conn, "
					SELECT id FROM ship_to_party_location
					WHERE dns_ship_to_party_code = ? AND dealer_code = ?
				");
				if (!$sqlchk) {
					array_push($error_array, "Row $csv_row_count: prepare (lookup) failed: " . mysqli_error($conn));
					$rec_count++;
					continue;
				}
				mysqli_stmt_bind_param($sqlchk, "ss", $dns_ship_to_party_code, $dealer_code);
				mysqli_stmt_execute($sqlchk);
				$chkResult = mysqli_stmt_get_result($sqlchk);
				$existingRow = mysqli_fetch_assoc($chkResult);
				mysqli_stmt_close($sqlchk);

				if (!$existingRow)
				{
					$sqlins = mysqli_prepare($conn, "
						INSERT INTO ship_to_party_location
							(dns_ship_to_party_code, ship_to_party_name, customer_type,
							 branch_code, branch_name, dealer_code, dealer_name,
							 latitude, longitude, destination_name, status, approve_by,
							 created_at, updated_at)
						VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
					");
					if (!$sqlins) {
						array_push($error_array, "Row $csv_row_count: prepare (insert) failed: " . mysqli_error($conn));
						$rec_count++;
						continue;
					}
					mysqli_stmt_bind_param(
						$sqlins, "ssssssssssss",
						$dns_ship_to_party_code, $ship_to_party_name, $customer_type,
						$branch_code, $branch_name, $dealer_code, $dealer_name,
						$latitude, $longitude, $destination_name, $status, $approve_by
					);
					if (!mysqli_stmt_execute($sqlins)) {
						array_push($error_array, "Row $csv_row_count insert failed: " . mysqli_stmt_error($sqlins));
					}
					mysqli_stmt_close($sqlins);
				}
				else
				{
					array_push($error_array, "Row $csv_row_count skipped: dns_ship_to_party_code '$dns_ship_to_party_code' + dealer_code '$dealer_code' already exists.");
				}
			}
			$rec_count++;
		}
		$successval=1;
	}

	if($successval==1)
	{
		// $headers  = "MIME-Version: 1.0\r\n";
		// $headers .= "Content-type: text/html; charset=UTF-8\n";
		// $headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
		// 			"Reply-To:".FROMEMAIL." \r\n" .
		// 			"Bcc: ".BCCEMAIL." \r\n" .
		// 			'X-Mailer: PHP/' . phpversion();
		// if($nick_name =='STAR')
		// {
		// 	$mailto='warroom@starcement.co.in,emovesfa@starcement.co.in,kishukeshav@starcement.co.in';
		// }
		// else
		// {
		// 	$mailto='';
		// }
		// if(count($error_array)>0)
		// {
		// 	$date=gmdate('d',strtotime('+330 minute'));
		// 	$month=gmdate('m',strtotime('+330 minute'));
		// 	$year=gmdate('Y',strtotime('+330 minute'));
		// 	$hour=gmdate('H',strtotime('+330 minute'));
		// 	$minute=gmdate('i',strtotime('+330 minute'));
		// 	$second=gmdate('s',strtotime('+330 minute'));
		// 	$contentsdatetime =$date.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':'.$second;
		// 	$mailsub='Ship To Party Location data has been uploaded to '.$nick_name.' with error(s) on '.$contentsdatetime;
		// 	$mailbody='Ship To Party Location data has been uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
		// 	for($i=0;$i<count($error_array);$i++){
		// 		$mailbody.= "<b>$error_array[$i]</b><br /><br />";
		// 	}
		// }
		// else{
		// 	$mailsub='Ship To Party Location data has been uploaded to '.$nick_name.' on '.date('d-m-Y H:i:s');
		// 	$mailbody='Ship To Party Location data has been uploaded to '.$nick_name.' database.';
		// }
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
	}
	else
	{
		echo $GLOBALS['msg'] = "Problem with uploading Zip file. Make sure it contains \"ship to party location.csv\".";
		disphtml("main();");
	}
 }
?>