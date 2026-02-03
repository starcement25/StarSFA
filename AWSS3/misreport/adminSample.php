<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("product:index.php");
	$mode = $_REQUEST['mode'];
	if($mode =='add')				 				  disphtml("show_add_edit($_REQUEST[row_id]);");
	elseif($mode == 'addrecord')					   add_record($_REQUEST['row_id']);
	//if($_POST['mode']=="change_pwd")					change_pwd();
	elseif($_POST['mode']=="change_status")		   	 change_status();
	else    											disphtml("main();");
ob_end_flush();

function main()
{
?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head><center>
<?php
 $sql="SELECT * FROM sample_master ORDER BY creation_date DESC";
 $rs=mysql_query($sql) or die(mysql_error()." Error in main: ".$sql);
?>
<script language="JavaScript">
function show_all()
{
	document.frmSearch.search_mode.value = "";
	document.frmSearch.submit();
}
</script>

<script language="javascript">
function Add()
{
	//alert("ok");
	document.frm_opts.mode.value="add";
	document.frm_opts.row_id.value="";
	document.frm_opts.submit();
}

function Edit(ID,record_no)
{
	document.frm_opts.mode.value='edit';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}

function ChangeStatus(ID)
{
	document.frm_opts.mode.value='change_status';
	document.frm_opts.row_id.value=ID;
	//document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}

function access_add_edit(ID,record_no)
{
	document.frm_opts.mode.value='access';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}
function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data)
{
	var mywindow = window.open('', 'Employee Access', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Employee Access</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv(){
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;

	var tab_text="<table border='2px' id='export_table'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++)
    {
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<a[^>]*>|<\/a>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

	//$('#export_table').find('a').contents().unwrap(); //converts hyperlinks to plain text

	var a = document.createElement('a');

	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Sample info' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
</script>
<div id="display_main" style="max-height: 400px; max-width:1000px; overflow-y: scroll;" align="center">
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0" id="display_table">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Sample</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr>
					<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%"><a href="javascript:Add();" title=" Add Sample"><img src="images/plus_icon.gif" border="0"></a></td>
				</tr>
			</table>
			<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" >
					<td colspan="14">Sample Information</td>
				</tr>
			<?php
			$count=mysql_num_rows($rs);
			if($count == 0)
			{
			?>
				<tr>
					<td align="center" colspan="9">No records found</td>
				</tr>
			<?php
			}
			else
			{
			?>
				<tr class="TDHEAD_SUB">
					<td width="5%" align="center">Sl</td>
                    <td width="30%" align="left" style="padding-left:20px;">Reference No</td>
                    <td width="" align="left" style="padding-left:20px;">Image</td>
					<td align="center" width="20%" >Active/Inactive</td>
				</tr>
				<?php
				$cnt=$GLOBALS[start]+1;
				while($rec=mysql_fetch_array($rs))
				{
					$reference_no=$rec['reference_no'];
					$sample_photo=$rec['sample_photo'];
					
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body">
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($reference_no);?></td>
                    <td align="left" valign="top" style="padding-left:20px;">
                    <?php
                    	$file_name="../sample/thumbnail/thumb_". $sample_photo;
						if(file_exists($file_name))	{
					?>
					   <img src="<? echo $file_name;?>" border="0">
					<? }else{?>			 
						<img src="../sample/thumbnail/thumb_no_image.jpg" border="0">
						<? }?>
                    </td>
                    <td align="center">
                    	<a href="javascript:ChangeStatus('<?=$rec[reference_no];?>')" title="<?=($rec['acedns']=='Y')?'Turn off':'Turn on'?>"> 
						<? 
						if($rec[acedns]=="N")  		echo "<font color=\"#FF0000\"><b>Inactive</b></font>";
						else  							echo "<font color=\"green\"><b>Active</b></font>"; 
						?>
						</a></td>
				</tr>
			<?
				} // end of while loop
			} // end of page count
			?>
            </table>
			<?
				if($count>0 && $count > $GLOBALS[show])
				{
			?>
			<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2">

                <tr>
                    <td align="right"><div style="width:95%;" align="right">
                    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
                    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
            		</div>
                    </td>
            	</tr>
			</table>
			<?
				}
			?>
		</td>
	</tr>
</table>
	<br>
	<form name="frm_opts" action="adminSample.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
		<input type="hidden" name="url" value="adminSample.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
</div>
</center>
<?
}//End of main()

function show_add_edit($row_id = '')
{
	$sqlprem="SELECT * FROM sample_master WHERE reference_no='".$row_id ."'";
	$rsprem=mysql_query($sqlprem);
	$rowprem=mysql_fetch_array($rsprem);
	$acedns=$rowprem['acedns'];
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.reference_no.value.search(/\S/)==-1)
	{
		alert("Please enter Reference No");
		form.reference_no.focus();
		return false;
	}
	if(form.sample_photo.value!="")
	{
		var imagepath	=	form.sample_photo.value.toUpperCase();
		var path1		=	imagepath.lastIndexOf(".JPG");
		var path2		=	imagepath.lastIndexOf(".JPEG");
		var path3		=	imagepath.lastIndexOf(".GIF");
		var path4		=	imagepath.lastIndexOf(".PNG");
		if(path1==-1 && path2==-1 && path3==-1 && path4==-1)
		{
			alert("Please Upload jpg,jpeg,gif,png file");
			form.sample_photo.value = "";
			form.sample_photo.focus();
			return false;
		}
	}
	return true;
}
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Add Sample</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminSample.php" onSubmit="return check(this);" enctype="multipart/form-data">
			<input type="hidden" name="mode" value="addrecord">
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD">
				  <td colspan="3" align="left">Add Sample</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<? if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<? }?>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Reference No<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="reference_no" value="<?=$oldpassword?>" class="inplogin" maxlength="20"></td>
				</tr>
				<tr> 
					<td align="right" valign="top" class="tbllogin" width="45%">Upload Image</td>
					<td align="center" valign="top" class="tbllogin" width="5%">:</td>
					<td class="tbllogin"><input name="sample_photo" type="file" class="inplogin" /></td>
				</tr>                
                <!--tr>
					<td align="right" valign="top" class="tbllogin">Status</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="status" class="inplogin" id="status">
                    <option value="Y"  <?php /*if($acedns=='Y'){ echo 'selected';}?>>unblocked</option>
                    <option value="N" <?php if($acedns=='N'){ echo 'selected';}*/?>>blocked</option>
                    </select>
                    </td>
				</tr-->
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Add " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminSample.php';" class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?
}
function clear_allocation()
{
	$emp_code = $_REQUEST['row_id'];
	$upd_sql="UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '" .$emp_code."'";
    mysql_query($upd_sql) or die(mysql_error()." Error in device allocation updation.");

	$GLOBALS['err_msg']="Employee device allocation has been cleared Successfully.";
	disphtml("main();");
}
function change_status()
{
	$reference_no = $_REQUEST['row_id'];
	$sqlupdate="UPDATE sample_master SET acedns = if(acedns = 'N','Y','N') WHERE reference_no = '".addslashes($reference_no)."'";
	mysql_query($sqlupdate) or die(mysql_error()." Error in status changed.");
	$GLOBALS[err_msg] = "Sample status has been changed successfully.";
	$GLOBALS['mode'] = "";
	$_REQUEST['mode'] = "";
	disphtml("main();");
}
function add_record($row_id='')
{	
	$arr1 = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=');
	$arr2 = array('_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_');	
	$reference_no= $_POST['reference_no'];
	 $sqlselectrefferenceno="SELECT reference_no FROM sample_master WHERE reference_no='".addslashes($reference_no)."'";
	 $rsselectrefferenceno=mysql_query($sqlselectrefferenceno);
	 $cntselectrefferenceno=mysql_num_rows($rsselectrefferenceno);
	 if($cntselectrefferenceno ==0){
	 $sql = "INSERT INTO sample_master   
			SET       
			reference_no 						= '".trim(mysql_escape_string($reference_no))."',					
			acedns								= 'Y',
			creation_date						=CURRENT_TIMESTAMP()";
	$rs  = mysql_query($sql) or die(mysql_error()." Error in sample insert: ".$sql);
	$new_id = mysql_insert_id();
	$upload_dir = "../sample/";	
	if($_FILES['sample_photo']['name']!="")			
	{       					 
		$_FILES['sample_photo']['name'] = str_replace($arr1,$arr2,$_FILES['sample_photo']['name']);
		$file_name 	= "";
		$file_name  = "sample".'_'.$reference_no.'_'.$_FILES['sample_photo']['name'];
		$tmp_name 	= $_FILES['sample_photo']['tmp_name'];
		$file_size 	= $_FILES['sample_photo']['size'];
		$file_type 	= 'image';
		$sql = '';
		$check1 = fileUpload($upload_dir,$file_name,$tmp_name,$file_size,$file_type,$sql="");
		if($check1 != 0)//check1 returns 0 on success
		{
			$file_name='';  
			$GLOBALS['err_msg'] = "Your file is too big to upload!!!";
			//disphtml("show_add_edit();");
			//NA--->not available
		}
		//MakeThumbnail($upload_dir, $file_name ,100,'');
		MakeThumbnail($upload_dir, $file_name ,100,'','thumbnail/thumb_');
		//MakeThumbnail($upload_dir, $file_name ,500,'','bigthumbnail/bigthumb_');
		$sql_update = "UPDATE sample_master SET  sample_photo = '".$file_name."' WHERE reference_no ='".$reference_no."'";
		mysql_query($sql_update) or die(mysql_error(). "Error in sample image insert: ".$sql_update);
	}	
	$GLOBALS['err_msg'] = "Sample added successfully.";
	$GLOBALS['mode'] = "";
	$_REQUEST['mode'] = "";
	disphtml("main();");
   }
   else
   {
	 $GLOBALS['err_msg'] = "Reference no already exisits.";
	 $GLOBALS['mode'] = "";
	 $_REQUEST['mode'] = "";
	 disphtml("main();");  
   }
}
?>
