<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$GLOBALS['show']=60;
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
	
	
	if(strtoupper($_SESSION['nick_name'])=='RKBK')
	{
		$sql_count ="SELECT COUNT(CM.customer_code) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code ".$emp_hierarchy_condition_one."";
	}
	else
	{
		$sql_count = "SELECT COUNT(CM.customer_code) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007'";
	}
	if($_REQUEST['search_mode']=='search')
	{
		if($_REQUEST['emp_name']!="")
		{
			$sql_count.=" AND CM.emp_code='".$_REQUEST['emp_name']."'";
		}
		if($_REQUEST['customer_name']!='')
		{
			$sql_count.=" AND CM.customer_name LIKE '%".$_REQUEST['customer_name']."%'";
		}
	}
	$res = mysql_query($sql_count) or die(mysql_error()." Error in count: ".$sql_count); 
	$row = mysql_fetch_row($res);
	$count =  $row[0];


	if($_REQUEST[hold_page] > 0)   	$GLOBALS[start] = $_REQUEST[hold_page];
	if($count == $GLOBALS[start])  	$GLOBALS[start] = $GLOBALS[start] - $GLOBALS[show];
	if($GLOBALS[start] < 0)		  $GLOBALS[start] = 0;
	
	if($_REQUEST['search_mode']=='search')
	{
		if($_REQUEST['emp_name']!="")
		{
			$sql_condition=" AND CM.emp_code='".$_REQUEST['emp_name']."'";
		}
		if($_REQUEST['customer_name']!='')
		{
			$sql_condition.=" AND CM.customer_name LIKE '%".$_REQUEST['customer_name']."%'";
		}
	}
	else
	{
		$sql_condition="";
	}

	if(strtoupper($_SESSION['nick_name'])=='RKBK')
	{
		$sql="SELECT CM.customer_name,CM.customer_code,CM.acedns,CM.black_list,EM.emp_name FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code ".$emp_hierarchy_condition_one.$sql_condition." ORDER BY EM.emp_name,CM.customer_name ASC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
			
		$row=mysql_fetch_array(mysql_query("SELECT COUNT(CM.customer_code) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code 
			 ".$emp_hierarchy_condition_one.$sql_condition." ORDER BY EM.emp_name,CM.customer_name ASC"));
	}
	else
	{
		$sql="SELECT CM.customer_name,CM.customer_code,CM.acedns,CM.black_list,EM.emp_name FROM employee_master EM,customer_master CM 
			 WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007' ".$sql_condition." 
			ORDER BY EM.emp_name,CM.customer_name ASC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
				
		$row=mysql_fetch_array(mysql_query("SELECT COUNT(CM.customer_name) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007' ".$sql_condition."  ORDER BY EM.emp_name,CM.customer_name ASC"));
	}
	
	$count=$row[0];
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
function access_add_edit(ID,record_no)
{
	document.frm_opts.mode.value='access';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}
</script>
<script language="javascript">
function check()
{
	if (document.frmSearch.emp_name.value=="" && document.frmSearch.customer_name.value.search(/\S/)==-1) 
	{
		alert('Please select a employee or enter a customer name to perform the search.');
		document.frmSearch.emp_name.focus();
		return false;
	}
	return true;
}

function exporttocsv()
{
	//alert('Hi');
	window.open('customeraccesscsvexport.php','mywindow');
}
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Customer Information</strong></td>
	</tr>
    <tr>
		<td valign="top" >
			<table width="55%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
				<tr class="TDHEAD" > 
					<td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
				</tr>
				<tr > 
					<td width="15%" colspan="7" align="center">
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  >
                        <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
                        <input type="hidden" name="search_mode" value="search">
                        	<tr>
                        		<td align="right" width="25%">Employee:</td>
                        		<td align="left" width="" style="vertical-align:top;" >
                                    <select name="emp_name" id="emp_name" >
                                    <option value="">SELECT</option>
                                    <?php 
                                    $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ORDER BY emp_name ASC";
                                    $resultqueryemp = mysql_query($sqlqueryemp);
                                    $countemp=mysql_num_rows($resultqueryemp);
                                    if($countemp>0){
                                    while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                    {
                                    ?>
                                    <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $_REQUEST['emp_name']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                    <?php
                                    }
                                    }
                                    ?>	
                                    </select>
                                </td>
                        	</tr>
                        	<tr>
                                <td align="right" width="25%">Customer Name:</td>
                                <td align="left" width="" style="vertical-align:top;">
                               		 <input type="text" value="<?php echo $_REQUEST['customer_name'];?>" name="customer_name" id="customer_name"></input>
                                </td>
                             </tr>
                        	<tr>
                            	<td align="right" width="25%">&nbsp;</td>
                                <td align="left" width="" >
                                <input type="submit" value="Submit" class="inplogin">
                                </td>
                        	</tr>
                        	</form>
                        </table>
					</td>
				</tr>
			</table>
            <table width="70%" align="center" border="0" cellpadding="5" cellspacing="2">
            	<tr>
            		<td align="right"><input name="export" type="button" value="Export" onclick="exporttocsv();"/></td>
                </tr>
            </table>
 		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><?php echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="8">Customer Information</td>
				</tr>
			<?php 
			if($count == 0)
			{ 
			?>
				<tr> 
					<td align="center" colspan="8">No records found</td>
				</tr>
			<?php
			}
			else
			{	
			?>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
					<td width="" align="left" style="padding-left:20px;">Customer</td>
                    <td width="20%" align="left" style="padding-left:20px;">Employee</td>
					<td align="center" width="10%" >Acedns</td>
                    <td align="center" width="10%" >Black list</td>
                    <td align="center" width="10%" ></td>
				</tr>   
				<?php
				$cnt=$GLOBALS[start]+1;
				while($rec=mysql_fetch_array($rs))
				{
					$customer_code=str_replace('/','',$rec['customer_code']);
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['customer_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['emp_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['acedns']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['black_list']);?></td>
					<td align="center"><a href="javascript:access_add_edit('<?=$customer_code;?>','<?=$GLOBALS[start]?>');" title=" Edit customer " style="color: #F00;"><img src="images/edit_icon.gif" alt="" /></a></td>
				</tr>
			<?php 
				} // end of while loop
			} // end of page count
			?>
			</table>
			<?php
				if($count>0 && $count > $GLOBALS[show])	
				{
			?>
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2">
				<tr>
					<td><? pagination($count,"frm_opts");?></td>
				</tr>
			</table>
			<?php
				}
			?>
		</td>
	</tr>
</table>
	<br>
	<form name="frm_opts" action="adminCustomerAccess.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="search_mode" value="<?=$_REQUEST['search_mode']?>">
        <input type="hidden" name="emp_name" value="<?=$_REQUEST['emp_name']?>">
        <input type="hidden" name="customer_name" value="<?=$_REQUEST['customer_name']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminCustomerAccess.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
<?php
}//End of main()

function access_add_edit($row_id)
{
	$customer_code_prefix=substr($row_id,0,1);
	if($customer_code_prefix=='C')
	{
		$customer_code_parts=substr($row_id,1,(strlen($row_id)-1));
		$customer_code=$customer_code_prefix.'/'.$customer_code_parts;
	}
	else
	{
		$customer_code=$row_id;
	}
	$sqlcustomer="SELECT customer_name,acedns,black_list FROM customer_master WHERE customer_code = '".$customer_code."'";
	$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in show customer: ".$sqlcustomer);
	$rowcustomer=mysql_fetch_array($rscustomer);
	
	$customer_name	=$rowcustomer['customer_name'];
	$acedns		   =$rowcustomer['acedns'];
	$black_list	  =$rowcustomer['black_list'];
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.customer_name.value.search(/\S/)==-1)
	{
		alert("Please enter Customer name");
		form.customer_name.focus();
		return false;
	}
	if(form.acedns.value==' ')
	{
		alert("Please choose a acedns value");
		form.acedns.focus();
		return false;
	}

	if (form.black_list.value==' ') 
	{
		alert('Please choose a balck list value');
		form.black_list.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Change Information</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminCustomerAccess.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="change_pwd">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Change information of "<?=$customer_name?>"</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }?>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?=$customer_name?></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Acedns<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                    <select name="acedns" class="inplogin" id="acedns">
                     <option value=" " >SELECT</option>
                    <option value="Y"  <?php if($acedns=='Y'){ echo 'selected';}?>>YES</option>
                    <option value="N" <?php if($acedns=='N'){ echo 'selected';}?>>NO</option>
                    </select>
                    </td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Balck list<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"> 
                    <select name="black_list" class="inplogin" id="black_list">
                    <option value=" " >SELECT</option>
                    <option value="Y"  <?php if($black_list=='Y'){ echo 'selected';}?>>YES</option>
                    <option value="N" <?php if($black_list=='N'){ echo 'selected';}?>>NO</option>
                    </select>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminCustomerAccess.php';" class="inplogin"></td>
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
	$customer_code_prefix=substr($_REQUEST['row_id'],0,1);
	if($customer_code_prefix=='C')
	{
		$customer_code_parts=substr($_REQUEST['row_id'],1,(strlen($_REQUEST['row_id'])-1));
		$customer_code=$customer_code_prefix.'/'.$customer_code_parts;
	}
	else
	{
		$customer_code=$_REQUEST['row_id'];
	}
	$customer_name = $_REQUEST['customer_name'];
	$acedns=$_REQUEST['acedns'];
	$black_list=$_REQUEST['black_list'];
	
	$upd_sql="UPDATE customer_master SET acedns ='".trim($acedns)."',
			 black_list='".trim($black_list)."',
			 download_time=CURRENT_TIMESTAMP()
			 WHERE customer_code = '" .$customer_code."'";
	mysql_query($upd_sql) or die(mysql_error()." Error in customer updation.");

	$GLOBALS['err_msg']="Customer information has been updated successfully.";
	disphtml("main();");
}
?>