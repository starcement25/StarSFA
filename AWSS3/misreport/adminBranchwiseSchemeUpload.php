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
    $branch_name_array=array();
	$branch_code_array=array();
    $sql_branch = "SELECT DISTINCT BM.branch_code,BM.branch_name FROM employee_master EM,branch_master BM 
   				  WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) ORDER BY BM.branch_name ASC";
	$res_branch = mysql_query($sql_branch);
	$total_rows = mysql_num_rows($res_branch);
	if($total_rows>0){
		$res_branch = mysql_query($sql_branch);
		while($row_branch = mysql_fetch_array($res_branch)){
			$branch_code = trim($row_branch['branch_code']);
			$branch_name = trim($row_branch['branch_name']);
			if($branch_name != ''){
				if(!in_array($branch_code,$branch_code_array))
					array_push($branch_code_array,$branch_code);
					array_push($branch_name_array,$branch_name);
			}
		}
	}
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
	var is_checked_depot=false;
	for(i=0; i<document.form_add_PDF.elements.length; i++){
		if(document.form_add_PDF.elements[i].type=="checkbox" && document.form_add_PDF.elements[i].checked==true 
				&& document.form_add_PDF.elements[i].name=='select_branch[]'){
			is_checked_depot=true;
			break;
		}
	}
	if(!is_checked_depot){
		alert("Please check at least one Branch");
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
           <td align="right">Select Branch*</td>
           <td width="2%">:</td>
           <td align="left"><div style="max-height:200px; overflow-y: scroll;">
      		<table>
            <!--tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_plant" id="all_checked_plant" value="allplant" onchange="javascript:checked_all_plant();select_depot_details();"/>ALL
                </td>
             </tr-->  
        <?php
        	for($i=0;$i<count($branch_code_array);$i++){
                 // echo "<option value='".$branch_code_array[$i]."'>".$branch_name_array[$i]."</option>";
			?>
          		<tr>
                    <td align="left">
                        <input type="checkbox" name="select_branch[]" value="<?php echo $branch_code_array[$i];?>" /><?php echo $branch_name_array[$i];?>
                    </td>
                 </tr>   
            <?php
			$cnt++;
			}
		?>	
    </table>
    </div>
    </td>
    </tr>	
		<tr> 
		  <td align="right">Pdf File*</td>
			<td width="2%">:</td>
			<td><input type="file" name="pdf_file" class="" ><br/ ><strong><font color="#FF0000">[Extension will be .pdf]</font></strong></td>
		</tr>
        <tr><td colspan="3" align="center"><div id="date_div" $hidden >
From:<input type="date" name="start_date" id="start_date" style="height:15px;" />
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
	$branch_code=$_POST['select_branch'];
	$branch_code_array=$_POST['select_branch'];
	//$branch_code="'".implode("','", $branch_code)."'";
	$start_date=$_POST['start_date'];
	$end_date=$_POST['end_date'];
	if(file_exists($_FILES['pdf_file']['tmp_name']))
	{
		$file_name = $_FILES['pdf_file']['name'];
		$file_name=str_replace(" ","_",$file_name);
		$tmp_name=$_FILES['pdf_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		move_uploaded_file($tmp_name,$upload_file);
		foreach($branch_code as $branch_code_val)
		{
			$sqlinsertbranchscheme="INSERT INTO branch_schemes_PDF SET branch_code='".$branch_code_val."',
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