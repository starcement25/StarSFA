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
	if($_POST['mode']=="change_mapping")				change_mapping();
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
			if($_REQUEST['emp_name'] == 'all')
				$sql_count .= '';
			else
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
			if($_REQUEST['emp_name'] == 'all')
				$sql_condition = '';
			else{
				if(modified_customer_emp_route=='no'){
				$sql_condition=" AND CM.emp_code='".$_REQUEST['emp_name']."'";
				}
				else
				{
					$sql_condition=" AND CRR.emp_code='".$_REQUEST['emp_name']."'";
				}
			}
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
	if(modified_customer_emp_route=='no'){
		if(strtoupper($_SESSION['nick_name'])=='RKBK')
		{
			$sql="SELECT CM.customer_name,CM.customer_code,CM.acedns,CM.black_list,EM.emp_name FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code ".$emp_hierarchy_condition_one.$sql_condition." ORDER BY EM.emp_name,CM.customer_name ASC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
			/*$row=mysql_fetch_array(mysql_query("SELECT COUNT(CM.customer_code) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code 
				 ".$emp_hierarchy_condition_one.$sql_condition." ORDER BY EM.emp_name,CM.customer_name ASC"));*/
		}
		else
		{
			$sql="SELECT CM.customer_name,CM.customer_code,CM.acedns,CM.black_list,EM.emp_name FROM employee_master EM,customer_master CM 
				 WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007' ".$sql_condition." 
				ORDER BY EM.emp_name,CM.customer_name ASC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
			/*$row=mysql_fetch_array(mysql_query("SELECT COUNT(CM.customer_name) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007' ".$sql_condition."  ORDER BY EM.emp_name,CM.customer_name ASC"));*/
		}
		//$count=$row[0];
	}
	else
	{
		if(strtoupper($_SESSION['nick_name'])=='ASL')
		{
		  $sql="SELECT CM.customer_name,CM.customer_code,CRR.acedns,CM.black_list,EM.emp_name,CM.dns_customer_code,RM.route_name FROM 
			 customer_master CM INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code 
			 INNER JOIN employee_master EM
			ON CRR.emp_code=EM.emp_code INNER JOIN route_master RM ON CRR.route_code=RM.route_code 
			WHERE CRR.acedns='Y' AND CM.cust_type <> 'R'".$sql_condition."  ORDER BY EM.emp_name,CM.customer_name ASC ";
		}
		else
		{
		$sql="SELECT CM.customer_name,CM.customer_code,CRR.acedns,CM.black_list,EM.emp_name,CM.dns_customer_code,RM.route_name FROM 
			 customer_master CM LEFT JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code 
			 LEFT JOIN employee_master EM
			ON CRR.emp_code=EM.emp_code LEFT JOIN route_master RM ON CRR.route_code=RM.route_code WHERE CRR.acedns='Y' AND CM.cust_type <> 'R'".$sql_condition." 
			ORDER BY EM.emp_name,CM.customer_name ASC ";
		}
	}
	$rs=mysql_query($sql) or die(mysql_error()." Error in main: ".$sql);
	$count=mysql_num_rows($rs);
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
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Customer Listing</strong></td>
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
                                    <option value="all">All</option>
                                    <?php 
                                    $sqlqueryemp="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CH 
												WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' ORDER BY EM.emp_name ASC";
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
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><?php echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%"><a href="adminCustomerAddModified.php">ADD CUSTOMER</a></td>
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
                    <td width="15%" align="left" style="padding-left:20px;">Customer Code</td>
					<td width="26%" align="left" style="padding-left:20px;">Customer</td>
                    <td width="" align="left" style="padding-left:20px;">Route</td>
                    <td width="20%" align="left" style="padding-left:20px;">Employee</td>
                    <td align="center" width="8%" ></td>
				</tr>   
				<?php
				$cnt=$GLOBALS[start]+1;
				while($rec=mysql_fetch_array($rs))
				{
					if($rec['acedns']=='Y' && $rec['black_list']=='N')
					{
					$customer_code=str_replace('/','',$rec['customer_code']);
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['dns_customer_code']);?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['customer_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['route_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['emp_name']);?></td>
					<td align="center"><a href="javascript:access_add_edit('<?=$customer_code;?>','<?=$GLOBALS[start]?>');" title=" Edit Customer " style="color: #F00;">EDIT</a></td>
				</tr>
			<?php 
					} // end of if
				} // end of while loop
			} // end of page count
			?>
			</table>
			<?php
				/*if($count>0 && $count > $GLOBALS[show])	
				{
			?>
			<!--table width="70%" align="center" border="0" cellpadding="5" cellspacing="2">
				<tr>
					<td><? pagination($count,"frm_opts");?></td>
				</tr>
			</table-->
			<?php
				}*/
			?>
		</td>
	</tr>
</table>
	<br>
	<form name="frm_opts" action="adminCustomerListing.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="search_mode" value="<?=$_REQUEST['search_mode']?>">
        <input type="hidden" name="emp_name" value="<?=$_REQUEST['emp_name']?>">
        <input type="hidden" name="customer_name" value="<?=$_REQUEST['customer_name']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminCustomerListing.php">
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
	if(modified_customer_emp_route=='no'){
	$sqlcustomer="SELECT CM.customer_name,CM.emp_code,CM.branch_code,EM.emp_name FROM customer_master CM,employee_master EM 
				WHERE CM.customer_code = '".$customer_code."' AND CM.emp_code=EM.emp_code";
	}
	else
	{
		$sqlcustomer="SELECT CM.dns_customer_code,CM.customer_name,CM.route_code,CM.branch_code,CRR.emp_code,CM.state_code,
					CM.credit_limit,CM.credit_days,CM.address,CM.TIN,CM.PAN,CM.pin,CM.incoterms,CM.phone_no,
					CM.transport_mode,CM.sauda_limit,CM.loadability_ton,CM.cust_type,CM.rds_tag
					FROM customer_master CM 
					INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code 
					WHERE CM.customer_code = '".$customer_code."' ";	
	}
	$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in show customer: ".$sqlcustomer);
	$rowcustomer=mysql_fetch_array($rscustomer);
	$dns_customer_code=$rowcustomer['dns_customer_code'];
	$customer_name=$rowcustomer['customer_name'];
	$phone_no=$rowcustomer['phone_no'];
	$state_code=$rowcustomer['state_code'];
	$route=$rowcustomer['route_code'];
	$emp_code=$rowcustomer['emp_code'];
	$credit_limit=$rowcustomer['credit_limit'];
	$credit_days=$rowcustomer['credit_days'];
	$branch_name=$rowcustomer['branch_code'];
	$address=$rowcustomer['address'];
	$TIN=$rowcustomer['TIN'];
	$PAN=$rowcustomer['PAN'];
	$pin=$rowcustomer['pin'];
	$incoterms=$rowcustomer['incoterms'];
	$sauda_limit=$rowcustomer['sauda_limit'];
	$transport_mode=$rowcustomer['transport_mode'];
	$loadability_ton=$rowcustomer['loadability_ton'];
	$cust_type=$rowcustomer['cust_type'];
	$rds_tag=$rowcustomer['rds_tag'];
?>
<script type="text/javascript" src="ajax1.js"></script>
<script language="JavaScript" type="text/javascript">
function state_route(state){
	if(document.getElementById("state_code").value.search(/\S/) == -1)
		return false;
	var state = encodeURIComponent(state);
	document.getElementById('showroute').style.display='none';
	document.getElementById('route_select_div').style.display='';
	document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=stateroute','route_select_div',0);
}
function tag_ss(tagval)
{
	if(tagval=='D')
	{
		document.getElementById("tagged_ss").style.display='';
	}
	else
	{
		document.getElementById("tagged_ss").style.display='none';
	}
}
function checkedit(form)
{
	/*if(form.dns_customer_code.value.search(/\S/)==-1)
	{
		alert("Please enter customer code");
		form.dns_customer_code.focus();
		return false;
	}*/
	if(form.customer_name.value.search(/\S/)==-1)
	{
		alert("Please enter customer name");
		form.customer_name.focus();
		return false;
	}
	if(form.phone_no.value.search(/\S/)==-1)
	{
		alert("Please enter phone no");
		form.phone_no.focus();
		return false;
	}
	if(form.state_code.value.search(/\S/)==-1)
	{
		alert("Please choose state");
		form.state_code.focus();
		return false;
	}
	if(form.route.value.search(/\S/)==-1)
	{
		alert("Please choose route");
		form.route.focus();
		return false;
	}
	if(form.emp_name.value.search(/\S/)==-1)
	{
		alert("Please choose employee");
		form.emp_name.focus();
		return false;
	}
	if(form.credit_limit.value.search(/\S/)==-1)
	{
		alert("Please enter credit limit");
		form.credit_limit.focus();
		return false;
	}
	/*else
	{
		var creditlimit=form.credit_limit.value;
		if(form.credit_limit.value.length > 13)
		{
			alert('please provide proper credit limit');
			form.credit_limit.focus();
			return false;
		}
		var creditlimitparts=creditlimit.split('.');
		if(creditlimitparts[1].length > 2)
		{
			alert('please provide proper credit limit');
			form.credit_limit.focus();
			return false;
		}
	}*/
	if(form.credit_days.value.search(/\S/)==-1)
	{
		alert("Please enter credit days");
		form.credit_days.focus();
		return false;
	}
	else
	{
		var creditdays=form.credit_days.value;
		
		if(isNaN(creditdays) || creditdays.length > 3)
		{
			alert("Please enter proper credit days");
			form.credit_days.focus();
			return false;
		}
	}
	if(form.branch_name.value.search(/\S/)==-1)
	{
		alert("Please choose branch");
		form.branch_name.focus();
		return false;
	}
	if(form.address.value.search(/\S/)==-1)
	{
		alert("Please enter address");
		form.address.focus();
		return false;
	}
	if(form.TIN.value.search(/\S/)==-1)
	{
		alert("Please enter TIN");
		form.TIN.focus();
		return false;
	}
	else
	{
		var TIN=form.TIN.value;
		if(TIN.length !=15)
		{
			alert("Please enter TIN of 15 digit");
			form.TIN.focus();
			return false;
		}
	}
	if(form.PAN.value.search(/\S/)==-1)
	{
		alert("Please enter PAN");
		form.PAN.focus();
		return false;
	}
	else
	{
		var PAN=form.PAN.value;
		if(PAN.length !=10)
		{
			alert("Please enter PAN of 10 digit");
			form.PAN.focus();
			return false;
		}
	}
	if(form.pin.value.search(/\S/)==-1)
	{
		alert("Please enter pin");
		form.pin.focus();
		return false;
	}
	if(form.incoterms.value.search(/\S/)==-1)
	{
		alert("Please choose incoterms");
		form.incoterms.focus();
		return false;
	}
	if(form.sauda_limit.value.search(/\S/)==-1)
	{
		alert("Please enter sauda limit");
		form.sauda_limit.focus();
		return false;
	}
	if(form.transport_mode.value.search(/\S/)==-1)
	{
		alert("Please choose transport mode");
		form.transport_mode.focus();
		return false;
	}
	if(form.loadability_ton.value.search(/\S/)==-1)
	{
		alert("Please enter capacity");
		form.loadability_ton.focus();
		return false;
	}
	return true;
}
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Edit Customer</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminCustomerListing.php" onSubmit="return checkedit(this);">
			<input type="hidden" name="mode" value="change_mapping">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Edit Information of "<?=$customer_name?>"</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){
					?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }?>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Code<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="dns_customer_code" id="dns_customer_code" class="inplogin" style="width:300px;height:30px;" value="<?php echo $dns_customer_code;?>"/ readonly></td>
				</tr>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="customer_name" id="customer_name" class="inplogin" style="width:300px;height:30px;" value="<?php echo $customer_name;?>"/></td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    	<select name="cust_type" id="cust_type" onchange="javascript:tag_ss(this.value);" >
                            <option value="">SELECT</option>
                             <option value="D" <?php if($cust_type=='D') echo 'selected'?>>D</option>
                             <option value="SS" <?php if($cust_type=='SS') echo 'selected'?>>SS</option>
                        </select>
                      </td>
				</tr>
                <?php if($cust_type=='D'){?>
                 <tr id="tagged_ss" style="display:<?php if($cust_type=='D'){?>''<?php }else{?> none<?php }?>">
					<td width="45%" align="right" valign="top" class="tbllogin">Tagged SS</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="tagged_ss" id="tagged_ss">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryss="SELECT dns_customer_code,customer_code,customer_name FROM customer_master where cust_type='SS' 
											AND acedns='Y'";
                                $resultqueryss = mysql_query($sqlqueryss);
                                $countqueryss=mysql_num_rows($resultqueryss);
                                if($countqueryss>0){
                                while($rowqueryss = mysql_fetch_array($resultqueryss))
                                {
									if($rds_tag==$rowqueryss['customer_code']) $selected='selected';
									else  $selected='';
                                echo "<option value=\"'".$rowqueryss['customer_code']."'\" $selected>".$rowqueryss['customer_name']."</option>";
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                <?php }?>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Phone no<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="phone_no" id="phone_no" class="inplogin" style="width:100px;height:20px;" value="<?php echo $phone_no;?>"/></td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">State<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="state_code" id="state_code" onchange="state_route(this.value);" >
                            <option value="">SELECT</option>
                                <!--<option value=<?php /*echo $rowquerystate['state'];?> <?php if( $_REQUEST['state_code']==$rowquerystate['state']){echo 'selected';}?>><?php echo $rowquerystate['state'];*/?></option>!-->
								<?php 
                                $sqlquerystate="SELECT state FROM state_master ORDER BY state ASC";
                                $resultquerystate = mysql_query($sqlquerystate);
                                $countquerystate=mysql_num_rows($resultquerystate);
                                if($countquerystate>0){
                                while($rowquerystate = mysql_fetch_array($resultquerystate))
                                {
									if(strtoupper($rowquerystate['state'])==strtoupper($state_code)) $selected='selected';
									else									$selected='';
                                echo "<option value=\"'".$rowquerystate['state']."'\" $selected>".$rowquerystate['state']."</option>";
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                <tr >
					<td width="45%" align="right" valign="top" class="tbllogin">Route Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><div id="route_select_div" style="display:none"></div>
                    <div id="showroute" style="display:''">
                    <?php
						echo "<select name=\"route\" id=\"route\" >";
						echo "<option value=\"\">Choose Route</option>";
						$sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_master CM,route_master RM 
												WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND 
													CM.state_code IN('".$state_code."') AND CM.cust_type='D'  ORDER BY RM.route_name ASC";
						$resultcustomerroute = mysql_query($sqlquerycustomerroute);
						$countcustomerroute=mysql_num_rows($resultcustomerroute);
						if($countcustomerroute>0){
							while($rowscustomerroute = mysql_fetch_array($resultcustomerroute))
							{
								$route_name=$rowscustomerroute['route_name'];
								$route_code=$rowscustomerroute['route_code'];
								if($route==$route_code) $selected='selected';
								else					$selected='';
								$option_value_string.="<option value=\"'".$route_code."'\" $selected>".strtoupper($route_name)."</option>";
							}
						}
						echo $option_value_string;
						echo "</select>";
					?>	
                    </div>
                    </td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Employee<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="emp_name" id="emp_name" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' ORDER BY emp_name ASC";
                                $resultqueryemp = mysql_query($sqlqueryemp);
                                $countemp=mysql_num_rows($resultqueryemp);
                                if($countemp>0){
                                while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                {
                                ?>
                                <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $emp_code==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Credit Limit</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="credit_limit" id="credit_limit"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $credit_limit;?>" maxlength="13"/></td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Credit Days</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="credit_days" id="credit_days" class="inplogin" style="width:100px;height:20px;" value="<?php echo $credit_days;?>"/></td>
				</tr>
                 <!--tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Current Balance</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="current_balance"  id="current_balance"  class="inplogin" style="width:300px;height:30px;" value="<?php echo $_REQUEST['current_balance'];?>"/></td>
				</tr-->
                <tr>
					<td align="right" valign="top" class="tbllogin">Branch<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="branch_name" id="branch_name" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlquerybranch="SELECT branch_code,branch_name FROM branch_master WHERE acedns='Y' ORDER BY branch_name ASC";
                                $resultquerybranch = mysql_query($sqlquerybranch);
                                $countquerybranch=mysql_num_rows($resultquerybranch);
                                if($countquerybranch>0){
                                while($rowquerybranch = mysql_fetch_array($resultquerybranch))
                                {
                                ?>
                                <option value="<?php echo $rowquerybranch['branch_code'];?>" <?php if( $branch_name==$rowquerybranch['branch_code']){echo 'selected';}?>><?php echo $rowquerybranch['branch_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin" >Address<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" ><input type="text" name="address" id="address" class="inplogin" style="width:300px;height:30px;" value="<?php echo $address;?>"/></td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">TIN/GST<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" ><input type="text" name="TIN"  id="TIN" class="inplogin" style="width:120px;height:20px;" value="<?php echo $TIN;?>"/>
                   &nbsp;&nbsp;&nbsp;&nbsp;<b>PAN</b><font color="#FF0000"><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;<input type="text" name="PAN" id="PAN" class="inplogin" style="width:120px;height:30px;" value="<?php echo $PAN;?>"/>
                    </td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">PIN<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="pin" id="pin"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $pin;?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Incoterms<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="incoterms" id="incoterms" >
                            <option value="">SELECT</option><option value="EX PLANT" <?php if( strtoupper($incoterms)=='EX PLANT'){echo 'selected';}?>>EX PLANT</option><option value="FOR PLANT" <?php if( strtoupper($incoterms)=='FOR PLANT'){echo 'selected';}?>>FOR PLANT</option></select></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Sauda Limit<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="sauda_limit" id="sauda_limit"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $sauda_limit;?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Transport Mode<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="transport_mode" id="transport_mode" >
                            <option value="">SELECT</option><option value="TRUCK" <?php if( strtoupper($transport_mode)=='TRUCK'){echo 'selected';}?>>TRUCK</option><option value="TANKER" <?php if( strtoupper($transport_mode)=='TANKER'){echo 'selected';}?>>TANKER</option></select></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Capacity<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="loadability_ton" id="loadability_ton"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $loadability_ton;?>"/></td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminCustomerListing.php';" class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?
}
function change_mapping()
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
	$customer_name=$_REQUEST['customer_name'];
	$phone_no=$_REQUEST['phone_no'];
	$state_code=$_REQUEST['state_code'];
	$route=$_REQUEST['route'];
	$emp_name=$_REQUEST['emp_name'];
	$credit_limit=$_REQUEST['credit_limit'];
	$credit_days=$_REQUEST['credit_days'];
	$branch_name=$_REQUEST['branch_name'];
	$address=$_REQUEST['address'];
	$TIN=$_REQUEST['TIN'];
	$PAN=$_REQUEST['PAN'];
	$pin=$_REQUEST['pin'];
	$incoterms=$_REQUEST['incoterms'];
	$sauda_limit=$_REQUEST['sauda_limit'];
	$transport_mode=$_REQUEST['transport_mode'];
	$loadability_ton=$_REQUEST['loadability_ton'];
	$cust_type=$_REQUEST['cust_type'];
	$tagged_ss=$_REQUEST['tagged_ss'];
	if($cust_type =='D')
	{
		if($tagged_ss!='')
		{
			$sqltagged_ss=",rds_tag=".$tagged_ss."";
		}
		else
		{
			$sqltagged_ss=",rds_tag=''";
		}
	}
	else $sqltagged_ss=",rds_tag=''";
	
	/*$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
	$rsroutechk=mysql_query($sqlroutechk);
	$countroutechk=mysql_num_rows($rsroutechk);
	/*if(strtoupper($folderName)=='ASL')
	{
		$sqltownnamechk="SELECT town_name FROM town_master WHERE town_name='".addslashes($route_name)."'";
		$rstownnamechk=mysql_query($sqltownnamechk);
		$cnttownmamechk=mysql_num_rows($rstownnamechk);
		if($cnttownmamechk==0)
		{
			echo "Route name not exists in town list.Please provide another route name at row ".($csv_row_count+1);
			die;
		}
	}*/
		/*if($countroutechk<1 && $route_name!='')
		{
			$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
			$rsmaxroutecode=mysql_query($sqlmaxroutecode);
			$rowmaxroutecode=mysql_fetch_array($rsmaxroutecode);
			$new_route_code=$rowmaxroutecode['new_route_code'];
			
			if($new_route_code=='')
			{
				$max_route_code='RT/1';
			}
			else
			{
				$max_route_code='RT/'.($new_route_code+1);
			}
			$sqlroute  = "insert into route_master ";
			$sqlroute .= " SET route_code='".$max_route_code."'";
			$sqlroute .= " ,dns_route_code='".$dns_route_code."'";
			$sqlroute .= " ,route_name='".$route_name."'";
			$sqlroute .= " ,branch_code='".$branch_code."'";
			$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlroute) or  array_push($error_array,"mysql_error().
							Internal DATA execution problem on route table.PLease contact aceDNS admin.");				
			//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			$route_code=$max_route_code;
		}
		else
		{
			$rowroutechk=mysql_fetch_array($rsroutechk);
			$route_code=$rowroutechk['route_code'];
			$route_name_db=$rowroutechk['route_name'];
			if($route_name_db !=$route_name)
			{
				$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',branch_code='".$branch_code."',download_time=CURRENT_TIMESTAMP() 
								WHERE route_code='".$route_code."'";
				mysql_query($sqlupdateroue) or  array_push($error_array,"mysql_error().
							Internal DATA execution problem on route table.PLease contact aceDNS admin.");
			}
		}*/
		//For customer
		$sql  = "UPDATE customer_master ";
		$sql .= " SET  customer_name='".addslashes($customer_name)."'";
		$sql .= " , branch_code='".addslashes($branch_name)."'";
		$sql .= " , phone_no='".$phone_no."'";
		$sql .= " , route_code=".$route."";
		$sql .= " , credit_limit='".$credit_limit."'";
		$sql .= " , credit_days='".$credit_days."'";
		$sql .= " , address='".$address."'";
		$sql .= " , TIN='".$TIN."'";
		$sql .= " , PAN='".$PAN."'";
		$sql .= " , state_code=".$state_code."";
		$sql .= " , cust_type='".addslashes($cust_type)."'".$sqltagged_ss;
		$sql .= " , incoterms='".addslashes($incoterms)."'";
		$sql .= " , loadability_ton='".addslashes($loadability_ton)."'";
		$sql .= " , transport_mode='".addslashes($transport_mode)."'";
		$sql .= " , pin='".addslashes($pin)."'";
		$sql .= " , retailer_app='yes'";
		$sql .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
		mysql_query($sql);
		$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code=".$route.",
								emp_code='".$emp_name."',
								acedns='Y',
								download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
		mysql_query($sqlupdatecustomerroute);
		
		$sqlupdatecustomersdaudalimit="UPDATE customer_sauda_limit ";
		$sqlupdatecustomersdaudalimit .= " SET sauda_limit='".$sauda_limit."'";
		$sqlupdatecustomersdaudalimit .= " 	,download_time=CURRENT_TIMESTAMP()  WHERE customer_code='".$customer_code."'";
		mysql_query($sqlupdatecustomersdaudalimit);
		$sqlupdatedistributorroute="UPDATE distributor_route_relation SET 
									route_code=".$route.",emp_code='".$emp_name."',download_time=CURRENT_TIMESTAMP() WHERE distributor_code='".$customer_code."'";
		$rsupdatedistributorroute=mysql_query($sqlupdatedistributorroute);
		$GLOBALS['err_msg']="Customer information has been added successfully.";

	$GLOBALS['err_msg']="Customer Information edited successfully.";
	disphtml("main();");
}
?>