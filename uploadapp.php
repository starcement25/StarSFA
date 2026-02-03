<?php
ini_set('post_max_size', '8M');
ini_set('upload_max_filesize', '8M');
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");

if($_REQUEST['mode']=='app_upload')
{
	app_upload();
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
	if(document.form_add_app.nick_name.value.search(/\S/)==-1){
		alert("Nick name should not be blank.");
		document.form_add_app.nick_name.focus();
		return false;
	}
	if(document.form_add_app.version_code.value.search(/\S/)==-1){
		alert("Version Code should not be blank.");
		document.form_add_app.version_code.focus();
		return false;
	}
	/*if(document.form_add_app.app_file.value=="")
	{
		alert("Please browse the App file first...");
		document.form_add_app.app_file.focus();
		return false;
	}
	var fname = document.form_add_app.app_file.value.toUpperCase();
	var pos1 = fname.indexOf(".APK");
	
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use APK only...");
		document.form_add_app.app_file.focus();
		return false;	
	}*/
	return true;	
}
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
		<tr> 
			<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);$GLOBALS['err_msg']="";?></td>
        </tr>    
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
<table width="70%" align="center" cellpadding="5" cellspacing="2" class="border">
	<form name="form_add_app" action="<?=$_SERVER['PHP_SELF']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="app_upload">
		
		<tr class="TDHEAD" > 
			<td colspan="10">Upload App</td>
		</tr>
        <tr> 
		  <td align="right">Nick Name*</td>
			<td width="2%">:</td>
			<td><input type="text" name="nick_name" class=""  id="nick_name"></td>
		</tr>
		<tr> 
		  <td align="right">App Vesion Code*</td>
			<td width="2%">:</td>
			<td><input type="text" name="version_code" class=""  id="version_code"></td>
		</tr>
        <tr> 
		  <td align="right">Db Vesion Code</td>
			<td width="2%">:</td>
			<td><input type="text" name="db_version_code" class=""  id="db_version_code"></td>
		</tr>	
		<tr> 
		  <td align="right">File*</td>
			<td width="2%">:</td>
			<td><input type="file" name="app_file" class="" ></td>
		</tr>
		<tr>
            <td>&nbsp;</td>
            <td >&nbsp;</td>
            <td>		
                <input type="submit" name="Add" value="Upload" > 
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
<?
}

function app_upload(){
	//echo 	$_FILES['app_file']['name'];
	$db_version_code=$_REQUEST['db_version_code'];
	$version_code=$_REQUEST['version_code'];
	$nick_name=$_REQUEST['nick_name'];
	if ( !file_exists("app_update/$version_code")){
		mkdir("app_update/$version_code");
		chmod("app_update/$version_code", 0777);
	}
	$upload_dir = "app_update/$version_code/";
	$linksetup=connecttodb(SERVER,"acednsproduct",USER,PASSWORD);
	$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsdbaccessdetails=mysqli_query($link,$sqldbaccessdetails,$linksetup);
	$rowdbaccessdetails=mysqli_fetch_assoc($rsdbaccessdetails);
	$remote_db_access=$rowdbaccessdetails['remote_db_access'];

	if($remote_db_access=='yes')
	{
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_$nick_name");
		$link=connecttodb(SERVERREMOTE,"$nick_name",USERREMOTE,PASSWORDREMOTE);
	}
	else
	{
		$link=connecttodb(SERVER,"$nick_name",USER,PASSWORD);
	}
	
		if($db_version_code!='')
		{
			$sqlselectversion="SELECT version_code FROM db_version ";
			$rsselectversion=mysqli_query($link,$sqlselectversion,$link);
			$rowselectversion=mysqli_fetch_assoc($rsselectversion);
			$versionCode=$rowselectversion['version_code'];
			
			$sqlquery="SELECT * FROM app_db_update_execution WHERE db_version > '".$versionCode."'  
						AND db_version <= '".$db_version_code."'  ORDER BY app_db_u_exe_id ASC ";
			$result = mysqli_query($link,$sqlquery,$linksetup) or die(mysqli_error());
			$countdbupdate=mysqli_num_rows($result);
			if($countdbupdate>0)
			{
				$sqltablestructuresetN="UPDATE table_structure_master SET need_update='N',is_transaction='N'";
				mysqli_query($link,$sqltablestructuresetN,$link) or die(mysqli_error(). "Error in update table structure master to set need  update N: ".$sqltablestructuresetN);
			}
			while($rowdblog=mysqli_fetch_assoc($result))
			{
				$table_name=$rowdblog['table_name'];
				$table_structure=$rowdblog['table_structure'];
				$need_update=$rowdblog['need_update'];
				$is_transaction=$rowdblog['is_transaction'];
				
				$sqlquerydetailschk="SELECT * FROM table_structure_master WHERE table_name='".$table_name."'";
				$rsquerydetailschk=mysqli_query($link,$sqlquerydetailschk,$link);
				$countquerydetailschk=mysqli_num_rows($rsquerydetailschk);
				
				if($countquerydetailschk > 0)
				{
				$sqlupdatetablestructure="UPDATE table_structure_master SET 
										table_structure='".addslashes($table_structure)."',
										need_update='".$need_update."',
										is_transaction='".$is_transaction."' WHERE table_name='".$table_name."'";
				mysqli_query($link,$sqlupdatetablestructure,$link) or die(mysqli_error(). "Error in update table structure master: ".$sqlupdatetablestructure);
				}
				else
				{
					$sqlinserttablestructure="INSERT INTO table_structure_master SET 
											table_name='".$table_name."',
											table_structure='".addslashes($table_structure)."',
											need_update='".$need_update."',
											is_transaction='".$is_transaction."'";
					mysqli_query($link,$sqlinserttablestructure,$link) or die(mysqli_error(). "Error in insert table structure master: ".$sqlinserttablestructure);
				}
			}
			$sqlUpdatedbver="UPDATE db_version SET version_code='".$db_version_code."'";
			mysqli_query($link,$sqlUpdatedbver,$link) or die(mysqli_error(). "Error in update db version: ".$sqlUpdatedbver);
			
			$sqlUpdate="UPDATE table_structure_updation SET is_update='1'";
			mysqli_query($link,$sqlUpdate,$link) or die(mysqli_error(). "Error in db update: ".$sqlUpdate);
		}
		
		if($_FILES['app_file']['name']!="")			
		{       					 			
			$file_name = $_FILES['app_file']['name'];
			if(file_exists($upload_dir.$file_name))
			{
				unlink($upload_dir.$file_name);
			}
			
			$tmp_name=$_FILES['app_file']['tmp_name'];
			
			$upload_file = $upload_dir.$file_name;
			move_uploaded_file($tmp_name,$upload_file);
			
			$sqlUpdateappver="UPDATE app_version SET version_code='".$version_code."'";
			mysqli_query($link,$sqlUpdateappver,$link) or die(mysqli_error(). "Error in update app version: ".$sqlUpdateappver);
			
			$sqlUpdate="UPDATE app_updation SET is_update='1'";
			mysqli_query($link,$sqlUpdate,$link) or die(mysqli_error(). "Error in app upload: ".$sqlUpdate);
			
			$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlInsertdatarefresh);
			
			$GLOBALS['err_msg'] = "App has been uploaded successfully.";
			main();
		}
		mysqli_close($linksetup);
		mysqli_close($link);	
	}
	function connecttodb($servername,$dbname,$dbuser,$dbpassword)
	{
		$dbnamefinal="acedns_".$dbname;
		$link=mysqli_connect($servername,$dbuser,$dbpassword,TRUE) or die("Database Connection Error.");
		mysqli_select_db($dbnamefinal,$link) or die("could not connect the database for invalid nick name");
		return $link;
	}
?>