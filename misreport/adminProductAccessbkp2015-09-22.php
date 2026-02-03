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
	
	
	$sql_count = "SELECT COUNT(PM.prod_code) FROM product_master PM";
	if($_REQUEST['search_mode']=='search')
	{
		if($_REQUEST['brand_code']!="")
		{
			$sql_condition=" AND PM.product_group_code='".$_REQUEST['brand_code']."'";
		}
		if($_REQUEST['product_name']!='')
		{
			$sql_condition.=" AND PM.prod_desc LIKE '%".$_REQUEST['product_name']."%'";
		}
	}
	$res = mysqli_query($link,$sql_count) or die(mysqli_error()." Error in count: ".$sql_count); 
	$row = mysqli_fetch_row($res);
	$count =  $row[0];


	if($_REQUEST[hold_page] > 0)   	$GLOBALS[start] = $_REQUEST[hold_page];
	if($count == $GLOBALS[start])  	$GLOBALS[start] = $GLOBALS[start] - $GLOBALS[show];
	if($GLOBALS[start] < 0)		  $GLOBALS[start] = 0;
	
	if($_REQUEST['search_mode']=='search')
	{
		if($_REQUEST['brand_code']!="")
		{
			$sql_condition=" AND PM.product_group_code='".$_REQUEST['brand_code']."'";
		}
		if($_REQUEST['product_name']!='')
		{
			$sql_condition.=" AND PM.prod_desc LIKE '%".$_REQUEST['product_name']."%'";
		}
	}
	else
	{
		$sql_condition="";
	}

	$sql="SELECT PM.prod_desc,PM.prod_code,PM.acedns,PM.black_list,PGM.product_group_name FROM product_master PM,product_group_master PGM 
		 WHERE PM.product_group_code=PGM.product_group_code  ".$sql_condition." 
		 ORDER BY PGM.product_group_name,PM.prod_desc ASC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
			
	$row=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(PM.prod_desc) FROM product_master PM,product_group_master PGM 
		 WHERE PM.product_group_code=PGM.product_group_code ".$sql_condition."  ORDER BY PGM.product_group_name,PM.prod_desc ASC"));
	
	$count=$row[0];
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
	if (document.frmSearch.brand_code.value=="" && document.frmSearch.product_name.value.search(/\S/)==-1) 
	{
		alert('Please select a <?php echo col1;?> or enter a <?php echo col4;?> to perform the search.');
		document.frmSearch.product_name.focus();
		return false;
	}
	return true;
}

function exporttocsv()
{
	//alert('Hi');
	window.open('productaccesscsvexport.php','mywindow');
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage <?php echo col4;?> Information</strong></td>
	</tr>
    <tr>
		<td valign="top" >
			<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
				<tr class="TDHEAD" > 
					<td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
				</tr>
				<tr > 
					<td width="15%" colspan="7" align="center">
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  >
                        <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
                        <input type="hidden" name="search_mode" value="search">
                        <?php if(no_of_filter >1){?>
                         <tr>
                            <td align="right" width="25%" ><?php echo col1;?>:</td>
                            <td align="left" width="" style="vertical-align:top;">
                             <?php  $brand_code=$_REQUEST['brand_code'];?>
                              	 <select name="brand_code" id="brand_code" onChange="javascript:select_sku(this.value);">
                                    <option value="">SELECT</option>
                                     <?php 
                                     $sqlproductgroup="SELECT product_group_code,product_group_name FROM product_group_master 
									 					ORDER BY product_group_name ASC";
                                     $resultproductgroup = mysqli_query($link,$sqlproductgroup);
                                     $countgroup=mysqli_num_rows($resultproductgroup);
                                        if($countgroup>0){
                                        while($rowproductgroup = mysqli_fetch_assoc($resultproductgroup))
                                        {
                                      ?>
                                         <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($brand_code==$rowproductgroup['product_group_code'] || $_REQUEST['brand_code_val']==$rowproductgroup['product_group_code']){echo  'selected';}?>><?php echo $rowproductgroup['product_group_name'];?></option>
                                       <?php
                                        }
                                      }
                                        ?>	
                                     </select>
                                </td>
                           </tr>
                           <?php }?>
                            <tr>
                                <td align="right" width="25%"><?php echo col4;?>:</td>
                                <td align="left" width="" style="vertical-align:top;">
                               		 <input type="text" value="<?php echo $_REQUEST['product_name'];?>" name="product_name" id="product_name"></input>
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
            <table width="60%" align="center" border="0" cellpadding="5" cellspacing="2">
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
			<table width="60%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="8"><?php echo col4;?> Information</td>
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
					<td width="" align="left" style="padding-left:20px;"><?php echo col4;?></td>
                    <td width="20%" align="left" style="padding-left:20px;"><?php echo col1;?></td>
					<td align="center" width="10%" >Acedns</td>
                    <td align="center" width="10%" >Black list</td>
                    <td align="center" width="10%" ></td>
				</tr>   
				<?php
				$cnt=$GLOBALS[start]+1;
				while($rec=mysqli_fetch_assoc($rs))
				{
					$prod_code=str_replace('/','',$rec['prod_code']);
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['prod_desc']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['product_group_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['acedns']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['black_list']);?></td>
					<td align="center"><a href="javascript:access_add_edit('<?=$prod_code;?>','<?=$GLOBALS[start]?>');" title=" Edit <?php echo col4;?> " style="color: #F00;"><img src="images/edit_icon.gif" alt="" /></a></td>
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
	<form name="frm_opts" action="adminProductAccess.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="search_mode" value="<?=$_REQUEST['search_mode']?>">
        <input type="hidden" name="brand_code" value="<?=$_REQUEST['brand_code']?>">
        <input type="hidden" name="product_name" value="<?=$_REQUEST['product_name']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminProductAccess.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
<?php
}//End of main()

function access_add_edit($row_id)
{
	$prod_code=$row_id;
	$sqlproduct="SELECT prod_desc,acedns,black_list FROM product_master WHERE prod_code = '".$prod_code."'";
	$rsproduct=mysqli_query($link,$sqlproduct) or die(mysqli_error()." Error in show product: ".$sqlproduct);
	$rowproduct=mysqli_fetch_assoc($rsproduct);
	
	$prod_desc	=$rowproduct['prod_desc'];
	$acedns		   =$rowproduct['acedns'];
	$black_list	  =$rowproduct['black_list'];
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
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

<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Change Information</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminProductAccess.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="change_pwd">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Change information of "<?=$prod_desc?>"</td>
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
					<td width="45%" align="right" valign="top" class="tbllogin"><?php echo col4;?><font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?=$prod_desc?></td>
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
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminProductAccess.php';" class="inplogin"></td>
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
	$prod_code=$_REQUEST['row_id'];
	$acedns=$_REQUEST['acedns'];
	$black_list=$_REQUEST['black_list'];
	
	$upd_sql="UPDATE product_master SET acedns ='".trim($acedns)."',
			 black_list='".trim($black_list)."',
			 download_time=CURRENT_TIMESTAMP()
			 WHERE prod_code = '".$prod_code."'";
	mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in product updation.");
	
	$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlInsertdatarefresh);

	$GLOBALS['err_msg']="Product information has been updated successfully.";
	disphtml("main();");
}
?>