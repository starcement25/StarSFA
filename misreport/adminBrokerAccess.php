<?php
ob_start();
	session_start();
	
		require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("product:index.php");
	$GLOBALS['show']=30;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
		$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	$mode = $_REQUEST['mode'];

	if($mode =='add' || $mode =='edit')				 disphtml("show_add_edit($_REQUEST[row_id]);");
	elseif($mode == 'update')						   update_record($_REQUEST['row_id']);
	if($_POST['mode']=="change_pwd")					change_pwd();
	elseif($_POST['mode']=="clearallocation")		   clear_allocation();
	elseif($mode =='access')							disphtml("access_add_edit($_REQUEST[row_id]);");
	else    											disphtml("main();");
ob_end_flush();

function main()
{
	?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head><center>
    <?php
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}

	 $sql_count = "SELECT COUNT(BM.broker_id) FROM broker_master BM,changepassword CH WHERE CH.emp_code=BM.broker_id AND BM.acedns='Y'";
	$res = mysqli_query($link,$sql_count) or die(mysqli_error()." Error in count: ".$sql_count);
	$row = mysqli_fetch_row($res);
	$count =  $row[0];


	if($_REQUEST[hold_page] > 0)   	$GLOBALS[start] = $_REQUEST[hold_page];
	if($count == $GLOBALS[start])   $GLOBALS[start] = $GLOBALS[start] - $GLOBALS[show];
	if($GLOBALS[start] < 0)		 $GLOBALS[start] = 0;

	 $sql="SELECT BM.* FROM broker_master BM,changepassword CH WHERE CH.emp_code=BM.broker_id AND BM.acedns='Y' 
ORDER BY CH.emp_code ASC";

	/*$row=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(EM.emp_code) FROM employee_master EM,changepassword CH WHERE CH.emp_code=EM.emp_code
		AND (FIND_IN_SET(EM.emp_code,(SELECT GROUP_CONCAT(reporting_to) FROM employee_master WHERE reporting_to <> '')) OR EM.emp_code IN(SELECT emp_code FROM customer_master GROUP BY emp_code)) AND EM.emp_code<>'C0007' ORDER BY EM.emp_code ASC"));*/
	 $row=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(BM.broker_id) FROM broker_master BM,changepassword CH WHERE CH.emp_code=BM.broker_id
		AND BM.acedns='Y' ORDER BY CH.emp_code ASC"));
	$count=$row[0];
	//echo $sql;
	$rs=mysqli_query($link,$sql) or die(mysqli_error()." Error in main: ".$sql);
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

function ChangeStatus(ID,record_no)
{
	document.frm_opts.mode.value='change_status';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}

function access_add_edit(ID,record_no)
{
	document.frm_opts.mode.value='access';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}
function clear_allocation(ID,record_no)
{
	document.frm_opts.mode.value='clearallocation';
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
	a.download = 'Employee Access' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
</script>
<div id="display_main" style="max-height: 400px; max-width:1000px; overflow-y: scroll;" align="center">
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0" id="display_table">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Broker Access</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr>
					<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>

			<table width="85%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" >
					<td colspan="14">Broker Information</td>
				</tr>
			<?
			if($count == 0)
			{
			?>
				<tr>
					<td align="center" colspan="9">No records found</td>
				</tr>
			<?
			}
			else
			{
			?>
				<tr class="TDHEAD_SUB">
					<td width="5%" align="center">Sl</td>
                    <td width="15%" align="left" style="padding-left:20px;">DNS Broker code</td>
                    <td width="15%" align="left" style="padding-left:20px;">Broker code</td>
					<td width="" align="left" style="padding-left:20px;">Broker</td>
                    <td width="" align="left" style="padding-left:20px;">Phone</td>
					<td width="" align="left" style="padding-left:20px;">State</td>
                    <td width="" align="left" style="padding-left:20px;">App Version</td>
                    <td width="" align="left" style="padding-left:20px;">DB Version</td>
                    <td align="center" width="20%" ></td>
				</tr>
				<?
				$cnt=$GLOBALS[start]+1;
				while($rec=mysqli_fetch_assoc($rs))
				{

					$deviceid_sql="SELECT deviceid FROM changepassword WHERE  emp_code = '".$rec[broker_id]."' AND deviceid <>''";
					$deviceid_res=mysqli_query($link,$deviceid_sql) or die(mysqli_error()." Error in fetch deviceid");
					$countdeviceid=mysqli_num_rows($deviceid_res);
					$row_device_id = mysqli_fetch_assoc($deviceid_res);
					$device_id = $row_device_id['deviceid'];

					$sql_app_version = "SELECT version_code FROM app_updation WHERE device_id = '".$device_id."'";
					$res_app_version = mysqli_query($link,$sql_app_version);
					$row_app_version = mysqli_fetch_assoc($res_app_version);
					$app_version = $row_app_version['version_code'];

					$sql_db_version = "SELECT db_version_code FROM table_structure_updation WHERE device_id = '".$device_id."' AND 
					emp_code = '".$rec[broker_id]."'";
					$res_db_version = mysqli_query($link,$sql_db_version);
					$row_db_version = mysqli_fetch_assoc($res_db_version);
					$db_version = $row_db_version['db_version_code'];
					
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body">
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec[dns_broker_id]);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec[broker_id]);?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['broker_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec[phone_no]);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec[state_code]);?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=$app_version; ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=$db_version;?></td>
                    <td align="center"><?php if($countdeviceid>0){?><a href="javascript:clear_allocation('<?=$rec[broker_id];?>','<?=$GLOBALS[start]?>');" title=" Clear Allocation "
                    style="color: #030 ;">Clear Allocation</a><?php }else{ echo '--'; }?></td>
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
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2">

                <tr>
                <td align="right"><div style="width:95%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
            <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
        </div></td>
            </tr>
			</table>
			<?
				}
			?>
		</td>
	</tr>

</table>
	<br>
	<form name="frm_opts" action="adminBrokerAccess.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminBrokerAccess.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
</div>
</center>
<?
}//End of main()

function access_add_edit($row_id = '')
{
		$sqlemp="SELECT emp_name,acedns FROM employee_master WHERE emp_code = '".$row_id."'";
		$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in show shop edit: ".$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);

		$emp_name	=$rowemp['emp_name'] ;
		$acedns	=$rowemp['acedns'] ;

		$sqlempPassword="SELECT * FROM changepassword WHERE emp_code = '".$row_id."'";
		$rsempPassword=mysqli_query($link,$sqlempPassword) or die(mysqli_error()." Error in Fetch employee change password details: ".$sqlempPassword);
		$countempPassword=mysqli_num_rows($rsempPassword);
		$rowempPassword=mysqli_fetch_assoc($rsempPassword);

		if($countempPassword>0)
		{
			$newpassword=$rowempPassword['newpassword'];
			$oldpassword=$rowempPassword['oldpassword'];
			$status=$rowempPassword['status'];
		}
		else
		{
			$newpassword='';
			$oldpassword='';
		}
		$status=$rowempPassword['status'];
		$is_licensed=$rowempPassword['is_licensed'];
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{

	var minsize = 4;			//minlength for password

	var maxsize = 20;			//maxlength for password

	var upassID=form.newpassword;

	var upass_string = upassID.value;





	var x1 = /^[a-z\d]{6,14}$/i // only alphanumerics, and length 6-10

	var x2 = /[a-z]/i           // a letter present

	var x3 = /\d/               // a digit present



	if(form.oldpassword.value.search(/\S/)==-1)
	{

		alert("Please enter Old Password");

		form.oldpassword.focus();

		return false;

	}

	if(upassID.value.search(/\S/)==-1)
	{

		alert("Please enter New Password");

		upassID.focus();

		return false;

	}

	if (upassID.value.length > maxsize)
	{

		alert('Your password is too long');

		upassID.focus();

		return false;

	}

	if (upassID.value.length < minsize)
	{

		alert('Your password is too short');

		upassID.focus();

		return false;

	}



	//**************************** End ***********************************\\

	if(form.conf_newpassword.value.search(/\S/)==-1)
	{

		alert("Please Confirm New Password");

		form.conf_newpassword.focus();

		return false;

	}

	if(form.newpassword.value!=form.conf_newpassword.value)

	{

		alert("Password mismatch");

		form.newpassword.value="";

		form.conf_newpassword.value="";

		form.newpassword.focus();

		return false;

	}

	return true;

}

</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Provide Access</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminEmployeeAccess.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="change_pwd">
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD">
				  <td colspan="3" align="left">Provide Access To "<?=$emp_name?>"</td>
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
					<td width="45%" align="right" valign="top" class="tbllogin">Old Password<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="oldpassword" value="<?=$oldpassword?>" class="inplogin" maxlength="20"></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">New Password<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"><input type="text" name="newpassword" value="<?=$newpassword?>" class="inplogin" maxlength="20"></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Confirm New Password<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="conf_newpassword" value="" class="inplogin" maxlength="20"></td>
				</tr>
                <tr>
					<td align="right" valign="top" class="tbllogin">Status</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="status" class="inplogin" id="status">
                    <option value="Y"  <?php if($acedns=='Y'){ echo 'selected';}?>>unblocked</option>
                    <option value="N" <?php if($acedns=='N'){ echo 'selected';}?>>blocked</option>
                    </select>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminEmployeeAccess.php';" class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?
}
function change_pwd()
{
	$emp_code = $_REQUEST['row_id'];
	$pwd_sql="SELECT * FROM changepassword WHERE  emp_code = '".$emp_code."'";
	$pwd_res=mysqli_query($link,$pwd_sql) or die(mysqli_error()." Error in fetch details");

	$pwd_sql_admin="SELECT * FROM admin_master WHERE  admin_login = '".$emp_code."'";
	$pwd_res_admin=mysqli_query($link,$pwd_sql_admin) or die(mysqli_error()." Error in fetch admin details");


	$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
	$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in show emp name: ".$sqlemp);
	$rowemp=mysqli_fetch_assoc($rsemp);
	$emp_name	=$rowemp['emp_name'] ;

	$sqlemail="SELECT admin_email FROM admin_master WHERE admin_id = 1";
	$rsemail=mysqli_query($link,$sqlemail) or die(mysqli_error()." Error in fetch admin email: ".$sqlemail);
	$rowemail=mysqli_fetch_assoc($rsemail);
	$admin_email=$rowemail['admin_email'] ;
	if(mysqli_num_rows($pwd_res)>0)
	{
		$upd_sql="UPDATE changepassword SET newpassword ='".trim($_POST[newpassword])."',
				 oldpassword ='".trim($_POST[oldpassword])."',
				 is_licensed='1'
				 WHERE emp_code = '" .$emp_code."'";
		mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in pasword updation.");

		$sql_update_emp_acedns = "UPDATE employee_master SET acedns='".$_POST['status']."',acedns_changed_date=CURRENT_TIMESTAMP()
										WHERE emp_code = '".$emp_code."'";
		mysqli_query($link,$sql_update_emp_acedns);

		$GLOBALS['err_msg']="Employee Access has been Provided Successfully.";
	}
	else
	{
		$ins_sql="INSERT INTO  changepassword SET
				  newpassword ='".trim($_POST[newpassword])."',
				  oldpassword ='".trim($_POST[oldpassword])."',
				  emp_code = '" .$emp_code."'";
		mysqli_query($link,$ins_sql) or die(mysqli_error()." Error in pasword insertion.");

		$GLOBALS['err_msg']="Employee Access has been Provided Successfully.";
	}
	if(mysqli_num_rows($pwd_res_admin)>0)
	{
		$upd_sql_admin="UPDATE admin_master SET admin_pwd ='".trim($_POST[newpassword])."' WHERE admin_login = '" .$emp_code."'";
		mysqli_query($link,$upd_sql_admin) or die(mysqli_error()." Error in pasword updation admin.");
	}

		/*$old_pwd = $_POST['oldpassword'];
		$new_pwd = $_POST['newpassword'];

		$subject = "Access has been provided to the employee ".$emp_name;
		$message  = "Hi,";
		$message .= "<br><br>Access for the employee <strong>".$emp_name."</strong>:<br>";
		$message .= "<strong>Old Password :</strong> ".$old_pwd."<br>";
		$message .= "<strong>New Password :</strong> ".$new_pwd."<br>";
		$message .= "Thanks you very much. <br><br>";
		$message .= "Sincerely, <br>";
		$message .= "(Forcepower)<br>\n\n";


		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: acedns@coral.in <acedns@coral.in>\r\n";
		mail($admin_email, $subject, $message, $headers);*/

	disphtml("main();");
}
function clear_allocation()
{
	$emp_code = $_REQUEST['row_id'];
	$upd_sql="UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '" .$emp_code."'";
    mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in device allocation updation.");

	$GLOBALS['err_msg']="Broker device allocation has been cleared Successfully.";
	disphtml("main();");
}
?>
