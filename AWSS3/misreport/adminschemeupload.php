<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if($_REQUEST['mode']=="schemes_upload")	  scheme_pdf_upload();
	else    									 disphtml("main();");
ob_end_flush();
function main()
{
   
?>
<script language="JavaScript">
function checkFields()
{
	/*if(document.form_add_PDF.select_branch.value=="")
	{
		alert("Please Select Branch");
		document.form_add_PDF.select_branch.focus();
		return false;
	}*/
	if(document.form_add_PDF.scheme_name.value.search(/\S/) == -1)
	{
		alert("Please Enter Scheme Name");
		document.form_add_PDF.scheme_name.focus();
		return false;
	}
	if(document.form_add_PDF.pdf_file.value=="")
	{
		alert("Please browse the PDF file");
		document.form_add_PDF.pdf_file.focus();
		return false;
	}
	
	var fname = document.form_add_PDF.pdf_file.value.toUpperCase();
	var pos1 = fname.indexOf(".PDF");
	
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use PDF only...");
		document.form_add_PDF.pdf_file.focus();
		return false;	
	}
    if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
		alert("Please provide From/To date");
		return false;
	}
	if(document.getElementById("start_date").value>document.getElementById("end_date").value){
		alert("From date cannot be greater than To date");
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
	<form name="form_add_PDF" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="schemes_upload">
		<tr class="TDHEAD" > 
			<td colspan="10">Upload Scheme PDF</td>
		</tr>
		<!--tr>
            <td align="right">Select Branch*</td>
            <td width="2%">:</td>
            <td align="left">
            <select id="select_branch" name="select_branch">
              <option value="">Select</option>
              <?php
              /*for($i=0;$i<count($branch_code_array);$i++){
                  echo "<option value='".$branch_code_array[$i]."'>".$branch_name_array[$i]."</option>";
              }*/
              ?>
            </select>
        </td>
      </tr-->
     <tr>
            <td align="right">Scheme Name</td>
            <td width="2%">:</td>
            <td align="left">
           <input type="text" name="scheme_name" value="" />
        </td>
      </tr>
		<tr> 
		  <td align="right">Pdf File</td>
			<td width="2%">:</td>
			<td><input type="file" name="pdf_file" class="" ><br/ ><strong><font color="#FF0000">[Extension will be .pdf]</font></strong></td>
		</tr>
        <tr><td align="right">From</td>
        <td width="2%">:</td>
        <td><div id="date_div" $hidden >
<input type="date" name="start_date" id="start_date" style="height:15px;" />
To:<input type="date" name="end_date" id="end_date" style="height:15px;" />
</div></td></tr>
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
function scheme_pdf_upload(){
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$upload_dir="../schemes/";
	$start_date=$_POST['start_date'];
	$end_date=$_POST['end_date'];
	$scheme_name=$_POST['scheme_name'];
	if(file_exists($_FILES['pdf_file']['tmp_name']))
	{
		$file_name = $_FILES['pdf_file']['name'];
		$file_name=str_replace(" ","_",$file_name);
		$tmp_name=$_FILES['pdf_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		move_uploaded_file($tmp_name,$upload_file);
		$sqlcheckscheme="SELECT scheme_name FROM branch_schemes_PDF WHERE scheme_name='".addslashes($scheme_name)."'";
		$rscheckscheme=mysql_query($sqlcheckscheme);
		$countcheckscheme=mysql_num_rows($rscheckscheme);
		if($countcheckscheme==0)
		{	
			$sqlinsertbranchscheme="INSERT INTO branch_schemes_PDF SET branch_code='',
									scheme_name='".addslashes($scheme_name)."',
									PDF_file_name='".addslashes($file_name)."',
									acedns='Y',
									start_date='".$start_date."',
									end_date='".$end_date."',
									download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlinsertbranchscheme);
		}
		else
		{
			$sqlupdate="UPDATE branch_schemes_PDF SET 
						acedns='N',download_time=CURRENT_TIMESTAMP() WHERE scheme_name='".addslashes($scheme_name)."'";
			mysql_query($sqlupdate);
			$sqlinsertbranchscheme="INSERT INTO branch_schemes_PDF SET branch_code='',
									scheme_name='".addslashes($scheme_name)."',
									PDF_file_name='".addslashes($file_name)."',
									acedns='Y',
									start_date='".$start_date."',
									end_date='".$end_date."',
									download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlinsertbranchscheme);			
		}
		$successval=1;
	 }

	if($successval==1)
	{
		$GLOBALS['msg'] = 'Schemes PDF uploaded successfully';
		disphtml("main();");
	}
	else 
	{
		echo $GLOBALS['msg'] = "Problem with uploading Schemes file";
		disphtml("main();");
	}
}
?>