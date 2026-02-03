<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if($_REQUEST['mode']=="catalogue_upload")	   catalogue_upload();
	else    										disphtml("main();");
ob_end_flush();
function main()
{
	$vertical_name_array=array();
   $sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', -1) as distinct_vertical_value FROM employee_master 
		EM WHERE SUBSTRING_INDEX( EM.vertical_value, ',', -1 ) != ''";
		$res_vertical = mysql_query($sql_vertical);
		$total_rows = mysql_num_rows($res_vertical);
		if($total_rows>0){
			$res_vertical = mysql_query($sql_vertical);
			while($row_vertical = mysql_fetch_array($res_vertical)){
				$dist_vert_value = trim($row_vertical['distinct_vertical_value']);
				if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($dist_vert_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}
				if($dist_vert_value != ''){
					if(!in_array($dist_vert_value,$vertical_name_array))
						array_push($vertical_name_array,$dist_vert_value);
				}
			}
		}
?>
<script language="JavaScript">
function checkFields()
{
	if(document.form_add_PDF.vertical_value)
	{
	if(document.form_add_PDF.vertical_value.value=="")
	{
		alert("Please Select vertical");
		document.form_add_PDF.vertical_value.focus();
		return false;
	}
	}
	if(document.form_add_PDF.pdf_file.value=="")
	{
		alert("Please browse the PDF file");
		document.form_add_PDF.pdf_file.focus();
		return false;
	}
	
	var fname = document.form_add_PDF.pdf_file.value.toUpperCase();
	var pos1 = fname.indexOf(".PDF");
	
	/*if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use PDF only...");
		document.form_add_PDF.pdf_file.focus();
		return false;	
	}*/
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
	<form name="form_add_PDF" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="catalogue_upload">
		<tr class="TDHEAD" > 
			<td colspan="10">Upload Catalogue</td>
		</tr>
        <?php if(vertical_fields=='yes'){?>
		<tr>
            <td align="right">Select Vertical*</td>
            <td width="2%">:</td>
            <td align="left">
            <select id="select_vertical" name="vertical_value">
              <option value="">Select</option>
              <?php
              foreach($vertical_name_array as $vertical_val){
                  echo "<option>$vertical_val</option>";
              }
              ?>
            </select>
        </td>
      </tr>
      <?php }?>	
		<tr> 
		  <td align="right">Catlogue File*</td>
			<td width="2%">:</td>
			<td><input type="file" name="pdf_file" class="" ><br/ ><!--strong><font color="#FF0000">[Extension will be .pdf]</font></strong--></td>
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
function catalogue_upload(){
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$upload_dir="../catalogue/$nick_name/";
	$vertical_value=$_POST['vertical_value'];
	if(file_exists($_FILES['pdf_file']['tmp_name']))
	{
		$file_name = $_FILES['pdf_file']['name'];
		$file_name=str_replace(" ","_",$file_name);
		$tmp_name=$_FILES['pdf_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		move_uploaded_file($tmp_name,$upload_file);
		if(vertical_fields=='yes'){
			if(strpos($file_name,'.pdf')!=false){
		$sqlselcataloguedetails="SELECT file_version FROM catalogue_info WHERE vertical='".$vertical_value."' AND file_name LIKE '%.pdf%'";
			}
			if(strpos($file_name,'.mp4')!=false){
		$sqlselcataloguedetails="SELECT file_version FROM catalogue_info WHERE vertical='".$vertical_value."' AND file_name LIKE '%.mp4%'";
			}
		$rsselcataloguedetails=mysql_query($sqlselcataloguedetails);
		$countselcataloguedetails=mysql_num_rows($rsselcataloguedetails);
		if($countselcataloguedetails > 0){
			if(strpos($file_name,'.pdf')!=false){
			$sqlupdatecatalogueinfo="UPDATE catalogue_info SET file_name='".addslashes($file_name)."',file_version=(file_version+1),
								download_time=CURRENT_TIMESTAMP() WHERE vertical='".$vertical_value."' AND file_name LIKE '%.pdf%'";
			}
			if(strpos($file_name,'.mp4')!=false){
				$sqlupdatecatalogueinfo="UPDATE catalogue_info SET file_name='".addslashes($file_name)."',file_version=(file_version+1),
								download_time=CURRENT_TIMESTAMP() WHERE vertical='".$vertical_value."' AND file_name LIKE '%.mp4%'";
			}
			mysql_query($sqlupdatecatalogueinfo);					
		}
		else
		{
			$sqlinsertcatalogueinfo="INSERT INTO catalogue_info SET file_name='".addslashes($file_name)."',
									vertical='".$vertical_value."',
									file_version=1,
									download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlinsertcatalogueinfo);
			
		}
		$sqlmaxversion="SELECT MAX(file_version) as latest_version FROM catalogue_info WHERE vertical='".$vertical_value."'";
		$rsmaxversion=mysql_query($sqlmaxversion);
		$rowmaxversion=mysql_fetch_array($rsmaxversion);
		$latest_version=$rowmaxversion['latest_version'];
		
		$sqlupdatecatalogueinfoall="UPDATE catalogue_info SET file_version=($latest_version+1),
								download_time=CURRENT_TIMESTAMP() WHERE vertical='".$vertical_value."'";
		mysql_query($sqlupdatecatalogueinfoall);
	  }
	  else
	  {
		$sqlselcataloguedetails="SELECT file_version FROM catalogue_info";
		$rsselcataloguedetails=mysql_query($sqlselcataloguedetails);
		$countselcataloguedetails=mysql_num_rows($rsselcataloguedetails);
		if($countselcataloguedetails > 0){
			$sqlupdatecatalogueinfo="UPDATE catalogue_info SET file_name='".addslashes($file_name)."',file_version=(file_version+1),
								download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlupdatecatalogueinfo);					
		}
		else
		{
			$sqlinsertcatalogueinfo="INSERT INTO catalogue_info SET file_name='".addslashes($file_name)."',
									file_version=1,
									download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlinsertcatalogueinfo);
		}
	  }
		$successval=1;
	 }
	if($successval==1)
	{
		$GLOBALS['msg'] = 'Catalogue uploaded successfully';
		disphtml("main();");
	}
	else 
	{
		echo $GLOBALS['msg'] = "Problem with uploading Catalogue file";
		disphtml("main();");
	}
}
?>