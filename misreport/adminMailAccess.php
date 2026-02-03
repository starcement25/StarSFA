<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
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
	elseif($mode == 'add_edit_mail')					update_record($_REQUEST['row_id']);
	else    											disphtml("main();");
ob_end_flush();

function main()
{
	$sql_count = "SELECT count(*) FROM mail_access";
	$res = mysqli_query($link,$sql_count) or die(mysqli_error()." Error in count: ".$sql_count); 
	$row = mysqli_fetch_row($res);
	$count =  $row[0];


	if($_REQUEST[hold_page] > 0)   	$GLOBALS[start] = $_REQUEST[hold_page];
	if($count == $GLOBALS[start])   $GLOBALS[start] = $GLOBALS[start] - $GLOBALS[show];
	if($GLOBALS[start] < 0)		 $GLOBALS[start] = 0;


	$sql="SELECT * FROM mail_access  WHERE 1 ORDER BY id DESC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
	$row=mysqli_fetch_assoc(mysqli_query($link,"SELECT count(id) FROM mail_access ORDER BY id DESC"));
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

function Delete(ID,record_no)
{
	var UserResp = window.confirm("Are you sure to remove this Branch?");
	if( UserResp == true )
	{
		document.frm_opts.mode.value='delete_rec';
		document.frm_opts.row_id.value=ID;
		document.frm_opts.hold_page.value = record_no*1;
		document.frm_opts.submit();
	}
}
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Email Information</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">
                    	<?php if($_SESSION['admin_login']=="admin"){?>
                    	<a href="javascript:Add();" title=" Add Email " ><font color="#000000"><strong>Add Email</strong></font></a>
                        <?php }?>
                    </td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="8">Email Information</td>
				</tr>
			<?php 
			if($count == 0){ ?>
				<tr> 
					<td align="center" colspan="8">No records found</td>
				</tr>
			<?php }else{?>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
                    <td width="15%" align="left" style="padding-left:20px;">Email</td>
					<td width="10%" align="left" style="padding-left:20px;">Branch</td>
					<td  width="" align="left" style="padding-left:20px;">Attributes</td>
                    <td align="center" width="10%" >Edit</td>
				</tr>   
				<?php
				$cnts=$GLOBALS[start]+1;
				while($rec=mysqli_fetch_assoc($rs))
				{
					$branch_code=$rec['branch_code'];
					$branch_code_arr=explode(',',$branch_code);
					$branch_location='';
					for($cnt=0;$cnt<count($branch_code_arr);$cnt++){
						$sqlbranch="SELECT branch_location FROM branch_master WHERE branch_code='".$branch_code_arr[$cnt]."'";
						$rsbranch=mysqli_query($link,$sqlbranch);
						$rowbranch=mysqli_fetch_assoc($rsbranch);
						$branch_location=$branch_location.$rowbranch['branch_location'].',';
					}
					$branch_location=substr($branch_location,0,-1);
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnts++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['mail_id']);?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=$branch_location;?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['attributes']);?></td>
                     <td align="center">
					<?php if($_SESSION['admin_login']=="admin"){?>
                    <a href="javascript:Edit('<?=$rec[id];?>','<?=$GLOBALS[start]?>');" title=" Edit Email "><img src="images/edit_icon.gif" border="0">
                    </a><?php }else{?>---<?php }?>
                    </td>
				</tr>
			<?php 
				} // end of while loop
			} // end of page count
			?>
			</table>
			<?php if($count>0 && $count > $GLOBALS[show]){?>
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2">
				<tr>
					<td><?php pagination($count,"frm_opts");?></td>
				</tr>
			</table>
			<?php
				}
			?>
		</td>
	</tr>
</table>
	<br>
	<form name="frm_opts" action="adminMailAccess.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminMailAccess.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
<?php
}//End of main()
function show_add_edit($row_id = '')
{
	if ($row_id == '') 
	{
		$current_mode = "Add";
		
		$mail_id=$_REQUEST['mail_id'];
		$name=$_REQUEST['name'];
		$branch_name=$_REQUEST['branch_name'];
		$attributes=$_REQUEST['attributes'];
		$area=$_REQUEST['area'];
		$attributes_array=array();
		$area_array_edit=array();
	}
	else 
	{
		$sqlmail="SELECT * FROM mail_access WHERE id = '".$row_id."'";
		$rsmail=mysqli_query($link,$sqlmail) or die(mysqli_error()." Error in show mail: ".$sqlmail);
		$rowmail=mysqli_fetch_assoc($rsmail);
		
		$current_mode = "Edit";
		$mail_id=$rowmail['mail_id'];
		$name=$rowmail['name'];
		$area=$rowmail['area'];
		$area_array_edit=explode(',',$area);
		$branch_code=$rowmail['branch_code'];
		$id_arr = array();	
		if ($branch_code == "0,0") $selected=" selected ";	
		else $id_arr = explode(",",$branch_code);
		
		$attributes=$rowmail['attributes'];
		$attributes_array=explode(',',$attributes);
	}
	$area_array=explode(',',vertical_fields_value);
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.mail_id.value.search(/\S/)==-1)
	{
		alert("Please enter Email Id.");
		form.mail_id.focus();
		return false;
	}
	if(form.mail_id.value.search(/\S/)==0)
	{
		var x = form.mail_id.value;
		var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (filter.test(x)==false)
		{	
			alert("Enter valid Email Id.");
			form.mail_id.value="";
			form.mail_id.focus();
			return false;
		}
	}
	if(form.name.value.search(/\S/)==-1)
	{
		alert("Please enter Name.");
		form.name.focus();
		return false;
	}
	if(document.frmedit.elements['branch_name[]'].value != 0)
	{
		var i;
		for(i=0;i<document.frmedit.elements['branch_name[]'].length;i++)
		{
			if(document.frmedit.elements['branch_name[]'][i].value == 0)
			{
				document.frmedit.elements['branch_name[]'][i].selected = false;
			}
		}
	}
	if(document.frmedit.elements['branch_name[]'].value == 0)
	{
		alert('Please Select Atleast one branch.');
		document.frmedit.elements['branch_name[]'].focus();
		return false;
	}
	
	var areaflag='0';
	 
	for(i=0;i<document.frmedit.elements.length;i++)
	{	
		var elm = document.frmedit.elements[i];
		if((elm.type=="checkbox") && (elm.name=='area[]'))
		{
			if(elm.checked){
				areaflag = '1';
				break;
			}
			else
			{
				areaflag ='0';
			}	
		}
	}
	if(areaflag == '0')
	{
		alert('Please Check Atleast one area.');
		return false;
	}
	var flag = '0';
	for(i=0;i<document.frmedit.elements.length;i++)
	{	
		var elm = document.frmedit.elements[i];
		if((elm.type=="checkbox") && (elm.name=='attributes[]'))
		{
			if(elm.checked){
				flag = '1';
				break;
			}
			else
			{
				flag ='0';
			}	
		}
	}
	if(flag == '0')
	{
		alert('Please Check Atleast one attribute.');
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Email Information</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminMailAccess.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="add_edit_mail">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="60%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left"><?=$current_mode=='Add'?' Add ':' Update '?> Email</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<? }?>
				<tr>
					<td width="28%" align="right" valign="top" class="tbllogin">Email Id<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="mail_id" value="<?=$mail_id?>" class="inplogin" maxlength="100" size="45"></td>
				</tr>
                <tr>
					<td width="28%" align="right" valign="top" class="tbllogin">Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="name" value="<?=$name?>" class="inplogin" maxlength="100" size="45"></td>
				</tr>
                <tr>
					<td width="28%" align="right" valign="top" class="tbllogin">Branch<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" class="tbllogin">
                    <select name="branch_name[]"  multiple size="9" class="inplogin">
                     	<option value="0">--------------------SELECT---------------------</option>
						<?php
                        $sqlbranch="SELECT branch_code,branch_name,branch_location  FROM branch_master ORDER BY branch_location ASC";
                        $rsbranch=mysqli_query($link,$sqlbranch) or die(mysqli_error()." Error in show branch: ".$sqlbranch);
                        while($rowbranch=mysqli_fetch_assoc($rsbranch))
                        {
							if (in_array($rowbranch['branch_code'],$id_arr)) $selected = " selected ";
							else  $selected = " ";
                        ?>
                        <option value="<?php echo $rowbranch['branch_code'];?>" <?=$selected;?> style="padding-bottom:6px;"><?php echo $rowbranch['branch_name'];?> -- <?php echo $rowbranch['branch_location'];?></option>
                        <?php }?>
                    </select>
                    <br/ ><strong><font color="#FF0000"><?php if ($row_id == ''){?>[For Multiple Branch Selection hold the <font color="#0000FF">Ctrl Key</font> and then select one by one.]<?php }
					else{?>[For Multiple Branch Selection and to keep the previous branch selected hold the <font color="#0000FF">Ctrl Key</font> and then select one by one.]<?php }?></font></strong>
                    </td>
				</tr>
                <tr>
					<td align="right" valign="top" class="tbllogin">Area</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                        <!--input type="checkbox" name="area[]" value="L" class="inplogin" <?php /*if (in_array('L',$area_array)){ echo 'checked';}?>> LUBES
                       	<input type="checkbox" name="area[]" value="S" class="inplogin" <?php if (in_array('S',$area_array)) { echo 'checked';}*/?>> SPARES-->
                    	<?php for($cntarea=0;$cntarea<count($area_array);$cntarea++)
							{
						?>
                           <input type="checkbox" name="area[]" value="<?php echo $area_array[$cntarea]?>" class="inplogin" <?php if (in_array("$area_array[$cntarea]",$area_array_edit)) { echo 'checked';}?>> <?php echo $area_array[$cntarea];?>
                        <?php		
							}
						?>                    
                    </td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Attributes<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top">
                        <input type="checkbox" name="attributes[]" value="Attendance" class="inplogin" <?php if (in_array('Attendance',$attributes_array)){ echo 'checked';}?>> Attendance
                         <input type="checkbox" name="attributes[]" value="Routeplan" class="inplogin" <?php if (in_array('Routeplan',$attributes_array)){ echo 'checked';}?>> Route plan
                       	<input type="checkbox" name="attributes[]" value="Order" class="inplogin" <?php if (in_array('Order',$attributes_array)) { echo 'checked';}?>> Order
                        <input type="checkbox" name="attributes[]" value="Payment" class="inplogin" <?php if (in_array('Payment',$attributes_array)) { echo 'checked';}?>> Collection
                        <input type="checkbox" name="attributes[]" value="Prospectivecustomeradd" class="inplogin" <?php if (in_array('Prospectivecustomeradd',$attributes_array)) { echo 'checked';}?>> Business Prospect
                        <input type="checkbox" name="attributes[]" value="DCR" class="inplogin" <?php if (in_array('DCR',$attributes_array)) { echo 'checked';}?>> Daily Call Report
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" <?=$current_mode=='Add'?' Add ':' Update '?> " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminMailAccess.php';" class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
}
function update_record($row_id='')
{
	$mail_id=$_POST['mail_id'];
	$name=$_POST['name'];
	$branch_name=implode(',',$_POST['branch_name']);
	$area=implode(',',$_POST['area']);
	$attributes=implode(',',$_POST['attributes']);
	
	$sqlmailidchk="SELECT mail_id FROM mail_access WHERE mail_id='".$mail_id."' ";
	$rsmailidchk=mysqli_query($link,$sqlmailidchk) or die(mysqli_error()." Error in mail id check.");
	$countmailidchk=mysqli_num_rows($rsmailidchk);
	
	if($row_id == '')
	{
		//if($countmailidchk==0)
		//{
			$sql="INSERT INTO  mail_access SET 
				  mail_id ='".trim($mail_id)."',
				  name	  ='".trim($name)."',
				  area	  ='".$area."',
				  branch_code ='".trim($branch_name)."',
				  attributes = '" .$attributes."'";
			mysqli_query($link,$sql) or die(mysqli_error()." Error in mail insertion.");
			$last_insert_id=mysqli_insert_id();
			
			//For Insertion to the log table
			$access_id='AM'.$last_insert_id.date('YmdHis');
			$access_type='Add Email';
			$added_data='mail_id_'.$mail_id.','.'name_'.$name.','.'branch_code_'.$branch_name.','.'area_'.$area.','.'attributes_'.$attributes;
			
			$sqlinsertlog="INSERT INTO  access_log SET 
						  access_id ='".trim($access_id)."',
						  access_type ='".trim($access_type)."',
						  access_date =CURRENT_TIMESTAMP(),
						  modified_data ='',
						  added_data = '" .$added_data."'";
			mysqli_query($link,$sqlinsertlog) or die(mysqli_error()." Error in email insertion log.");
			//End of insertion to log table

			$GLOBALS['err_msg']="Email information  has been added successfully.";
			disphtml("main();");
		/*}
		else
		{
			$row_id='';
			$GLOBALS['err_msg']="Email id already exist.";
			disphtml("show_add_edit($row_id);");
		}*/
	}
	else
	{
		$sqlfetch="SELECT mail_id,name,branch_code,attributes,area FROM mail_access WHERE id='".$row_id."'";
		$rsfetch=mysqli_query($link,$sqlfetch) or die(mysqli_error()." Error in mail id fetch.");
		$rowfetch=mysqli_fetch_assoc($rsfetch);
		$fetched_mail_id=$rowfetch['mail_id'];
		$previous_name=$rowfetch['name'];
		$previous_branch_code=$rowfetch['branch_code'];
		$previous_attributes=$rowfetch['attributes'];
		$previous_area=$rowfetch['area'];

		if($mail_id!=$fetched_mail_id){
			/*if($countmailidchk==0)
			{*/	
				$sqlupdate="UPDATE mail_access SET 
							mail_id ='".trim($mail_id)."',
							name	  ='".trim($name)."',
							area	  ='".$area."',
							branch_code ='".trim($branch_name)."',
							attributes = '" .$attributes."' WHERE id='".$row_id."'";
				mysqli_query($link,$sqlupdate) or die(mysqli_error()." Error in mail updation.");
				
				//For Insertion to the log table
				$access_id='EM'.$row_id.date('YmdHis');
				$access_type='Edit Email';
				$modified_data='';
				if($fetched_mail_id!=$mail_id){
					$modified_data.='mail_id_'.$fetched_mail_id.'_'.$mail_id.',';
				}
				if($previous_name!=$name){
					$modified_data.='name_'.$previous_name.'_'.$name.',';
				}
				if($previous_branch_code!=$branch_name){
					$modified_data.='branch_code_'.$previous_branch_code.'_'.$branch_name.',';
				}
				if($previous_attributes!=$attributes){
					$modified_data.='attributes_'.$previous_attributes.'_'.$attributes.',';
				}
				if($previous_area!=$area){
					$modified_data.='area_'.$previous_area.'_'.$area.',';
				}
				$modified_data=substr($modified_data,0,-1);
				$sqlinsertlog="INSERT INTO  access_log SET 
							  access_id ='".trim($access_id)."',
							  access_type ='".trim($access_type)."',
							  access_date =CURRENT_TIMESTAMP(),
							  added_data='',
							  modified_data = '" .$modified_data."'";
				mysqli_query($link,$sqlinsertlog) or die(mysqli_error()." Error in email updation log.");
				//End of insertion to log table

				$GLOBALS['err_msg']="Email information  has been modified successfully.";
				disphtml("main();");
			/*}
			else
			{
				$row_id=$row_id;
				$GLOBALS['err_msg']="Email id already exist.";
				disphtml("show_add_edit($row_id);");
			}*/
		}
		else{
			$sqlupdate="UPDATE mail_access SET 
						mail_id ='".trim($mail_id)."',
						name	  ='".trim($name)."',
						area	  ='".$area."',
						branch_code ='".trim($branch_name)."',
						attributes = '" .$attributes."' WHERE id='".$row_id."'";
			mysqli_query($link,$sqlupdate) or die(mysqli_error()." Error in mail updation.");
			
			//For Insertion to the log table
			$access_id='EM'.$row_id.date('YmdHis');
			$access_type='Edit Email';
			$modified_data='';
			if($fetched_mail_id!=$mail_id){
				$modified_data.='mail_id_'.$fetched_mail_id.'_'.$mail_id.',';
			}
			if($previous_name!=$name){
				$modified_data.='name_'.$previous_name.'_'.$name.',';
			}
			if($previous_branch_code!=$branch_name){
				$modified_data.='branch_code_'.$previous_branch_code.'_'.$branch_name.',';
			}
			if($previous_attributes!=$attributes){
				$modified_data.='attributes_'.$previous_attributes.'_'.$attributes.',';
			}
			if($previous_area!=$area){
				$modified_data.='area_'.$previous_area.'_'.$area.',';
			}
			$modified_data=substr($modified_data,0,-1);
			
			$sqlinsertlog="INSERT INTO  access_log SET 
						  access_id ='".trim($access_id)."',
						  access_type ='".trim($access_type)."',
						  access_date =CURRENT_TIMESTAMP(),
						  added_data='',
						  modified_data = '" .$modified_data."'";
			mysqli_query($link,$sqlinsertlog) or die(mysqli_error()." Error in email updation log.");
			//End of insertion to log table

			$GLOBALS['err_msg']="Email information  has been modified successfully.";
			disphtml("main();");
		}
	}
}
?>