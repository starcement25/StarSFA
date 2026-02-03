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
		$sql="SELECT CM.customer_name,CM.customer_code,CRR.acedns,CM.black_list,EM.emp_name,CM.dns_customer_code FROM 
			 customer_master CM LEFT JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code 
			 LEFT JOIN employee_master EM
			ON CRR.emp_code=EM.emp_code WHERE CRR.acedns='Y' ".$sql_condition." 
			ORDER BY EM.emp_name,CM.customer_name ASC ";
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Customer Mapping</strong></td>
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
                    <td width="15%" align="left" style="padding-left:20px;">Customer Code</td>
					<td width="" align="left" style="padding-left:20px;">Customer</td>
                    <td width="30%" align="left" style="padding-left:20px;">Employee</td>
                    <td align="center" width="20%" ></td>
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
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['emp_name']);?></td>
					<td align="center"><a href="javascript:access_add_edit('<?=$customer_code;?>','<?=$GLOBALS[start]?>');" title=" Change Mapping " style="color: #F00;">CHANGE MAPPING</a></td>
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
	<form name="frm_opts" action="adminCustomerMapping.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="search_mode" value="<?=$_REQUEST['search_mode']?>">
        <input type="hidden" name="emp_name" value="<?=$_REQUEST['emp_name']?>">
        <input type="hidden" name="customer_name" value="<?=$_REQUEST['customer_name']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminCustomerMapping.php">
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
		$sqlcustomer="SELECT CM.customer_name,CM.emp_code,CM.branch_code,EM.emp_name FROM customer_master CM 
					LEFT JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code 
			 		LEFT JOIN employee_master EM ON CRR.emp_code=EM.emp_code
				WHERE CM.customer_code = '".$customer_code."' ";	
	}
	$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in show customer: ".$sqlcustomer);
	$rowcustomer=mysql_fetch_array($rscustomer);
	
	$customer_name =$rowcustomer['customer_name'];
	$emp_code	  =$rowcustomer['emp_code'];
	$branch_code   =$rowcustomer['branch_code'];
	$emp_name   =$rowcustomer['emp_name'];
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.emp_code.value=='')
	{
		alert("Please choose a employee to be mapped");
		form.emp_code.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Change Mapping</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminCustomerMapping.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="change_mapping">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Change mapping of "<?=$customer_name?>"</td>
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
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Name</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?=$customer_name?></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Currently mapped</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?=$emp_name?></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">To be changed<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
           				<select name="emp_code" id="emp_code" >
                        <option value="">SELECT</option>
                        <?php 
						if(strtoupper($_SESSION['nick_name'])=='VCONNECTT' || strtoupper($_SESSION['nick_name'])=='VCONNECT')
						{
							 $sqlqueryemp="SELECT EM.emp_code,EM.emp_name FROM employee_master EM 
												WHERE EM.acedns='Y'  AND 
												EM.emp_code!='".$emp_code."' AND EM.designation='ISP' ORDER BY EM.emp_name ASC";
						}
						else
						{
							if($branch_code!='')
							{
								 $sqlqueryemp="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CH 
												WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND EM.branch_code='".$branch_code."' AND EM.emp_code!='".$emp_code."' ORDER BY EM.emp_name ASC";
							}
							else
							{
								 $sqlqueryemp="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CH 
											WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND EM.emp_code!='".$emp_code."' ORDER BY EM.emp_name ASC";
							}
						}
                        $resultqueryemp = mysql_query($sqlqueryemp);
                        $countemp=mysql_num_rows($resultqueryemp);
                        if($countemp>0){
							while($rowqueryemp = mysql_fetch_array($resultqueryemp))
							{
							?>
							<option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $_REQUEST['emp_code']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
							<?php
							}
                        }
                        ?>	
                    </select>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminCustomerMapping.php';" class="inplogin"></td>
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
	$new_emp_code=$_REQUEST['emp_code'];
	if(modified_customer_emp_route=='no'){
	$sqlexistingcustomer="SELECT customer_name,branch_code,current_balance,credit_limit,route_code,cust_type,rds_tag 
							FROM customer_master WHERE customer_code = '".$customer_code."'";
	$rsexistingcustomer=mysql_query($sqlexistingcustomer) or die(mysql_error()." Error in select existing customer name: ".$sqlexistingcustomer);
	$rowexistingcustomer=mysql_fetch_array($rsexistingcustomer);

	$new_customer_name = $rowexistingcustomer['customer_name'];
	$new_branch_code=$rowexistingcustomer['branch_code'];
	$new_current_balance=$rowexistingcustomer['current_balance'];
	$new_credit_limit=$rowexistingcustomer['credit_limit'];
	$new_cust_type=$rowexistingcustomer['cust_type'];
	$existing_route_code=$rowexistingcustomer['route_code'];
	$existing_rds_tag=$rowexistingcustomer['rds_tag'];
	
	//New employee data fetching
	$sqlroutename="SELECT route_name FROM route_master WHERE route_code = '".$existing_route_code."'";
	$rsroutename=mysql_query($sqlroutename) or die(mysql_error()." Error in select route name: ".$sqlroutename);
	$rowroutename=mysql_fetch_array($rsroutename);
	$route_name=$rowroutename['route_name'];
	if($existing_rds_tag!='')
	{
		$sqlrdsname="SELECT rds_name FROM rds_master WHERE rds_code = '".$existing_rds_tag."'";
		$rsrdsname=mysql_query($sqlrdsname) or die(mysql_error()." Error in select rds name: ".$sqlrdsname);
		$rowrdsname=mysql_fetch_array($rsrdsname);
		$rds_name=$rowrdsname['rds_name'];
	}
	//End of new employee data fetching
	
	//ADD new route
	$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$new_emp_code."'";
	$rsroutechk=mysql_query($sqlroutechk);
	$countroutechk=mysql_num_rows($rsroutechk);
	if($countroutechk<1 && $route_name!='')
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
			//$max_route_code++;
		}
	
		$sqlroute  = "insert into route_master ";
		$sqlroute .= " SET route_code='".$max_route_code."'";
		$sqlroute .= " ,dns_route_code=''";
		$sqlroute .= " ,route_name='".$route_name."'";
		$sqlroute .= " , emp_code='".$new_emp_code."'";
		$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
		mysql_query($sqlroute) or die(mysql_error()." Error in route insertion.");
		$route_code=$max_route_code;
	}
	else
	{
		$rowroutechk=mysql_fetch_array($rsroutechk);
		$route_code=$rowroutechk['route_code'];
	}
	//End of new route addition
	
	//ADD new rds
	if($existing_rds_tag!='')
	{
		$sqlrdsnamechk="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_name)."' AND emp_code='".$new_emp_code."'";
		$rsrdsnamechk=mysql_query($sqlrdsnamechk);
		$countrdsnamechk=mysql_num_rows($rsrdsnamechk);
		if($countrdsnamechk<1)
		{
			$sqlmaxrdscode="SELECT MAX(rds_code) AS max_rds_code FROM  rds_master WHERE 1";
			$rsmaxrdscode=mysql_query($sqlmaxrdscode);
			$rowmaxrdscode=mysql_fetch_array($rsmaxrdscode);
			$max_rds_code=$rowmaxrdscode['max_rds_code'];
			
			if($max_rds_code=='')
			{
				$max_rds_code='C/0000001';
			}
			else
			{
				$max_rds_code++;
			}
	
		
			$sqlrds  = "insert into rds_master ";
			$sqlrds .= " SET rds_code='".$max_rds_code."'";
			$sqlrds .= " ,rds_name='".$rds_name."'";
			$sqlrds .= " , emp_code='".$new_emp_code."'";
			$sqlrds .= " , rds_type='D'";
			$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
		
			mysql_query($sqlrds) or die(mysql_error()." Error in rds insertion.");
			$rds_code=$max_rds_code;
		}
		else
		{
			$rowrdsnamechk=mysql_fetch_array($rsrdsnamechk);
			$rds_code=$rowrdsnamechk['rds_code'];
		}
	}
	//End of new rds addition
	
	//New customer addition
	$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($new_customer_name)."' AND emp_code='".$new_emp_code."' 
						AND route_code='".$route_code."'";
	$rscustomernamechk=mysql_query($sqlcustomernamechk);
	$countcustomernamechk=mysql_num_rows($rscustomernamechk);
	if($countcustomernamechk<1)
	{
		$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
		$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
		$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
		$max_customer_code=$rowmaxcustomercode['max_customer_code'];
		$max_customer_code++;
		
		$sql  = "insert into customer_master ";
		$sql .= " SET customer_code='".$max_customer_code."'";
		$sql .= " , dns_customer_code=''";
		$sql .= " , customer_name='".addslashes($new_customer_name)."'";
		$sql .= " , branch_code='".$new_branch_code."'";
		$sql .= " , phone_no=''";
		$sql .= " , route_code='".$route_code."'";
		$sql .= " , emp_code='".$new_emp_code."'";
		$sql .= " , current_balance	='".$new_current_balance."'";
		$sql .= " , credit_limit='".$new_credit_limit."'";
		$sql .= " , acedns='Y'";
		$sql .= " , black_list='N'";
		$sql .= " , TD=''";
		$sql .= " , rds_tag='".$rds_code."'";
		$sql .= " , cust_type='".$new_cust_type."'";
		$sql .= " , download_time=CURRENT_TIMESTAMP()";
		//exit();
		mysql_query($sql) or die(mysql_error()." Error in customer addition.");
	}
	//End of new customer addition
	
	$upd_sql="UPDATE customer_master SET acedns ='N',
			 black_list='Y',
			 download_time=CURRENT_TIMESTAMP()
			 WHERE customer_code = '" .$customer_code."'";
	mysql_query($upd_sql) or die(mysql_error()." Error in customer updation.");
	
	$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
	mysql_query($sqlInsertdatarefresh);
	}
	else
	{
		$sqlselpreviousemp="SELECT emp_code FROM customer_route_emp_relation WHERE customer_code = '".$customer_code."' AND acedns='Y'";
		$rsselpreviousemp=mysql_query($sqlselpreviousemp);
		$rowselpreviousemp=mysql_fetch_array($rsselpreviousemp);
		$previous_emp_code=$rowselpreviousemp['emp_code'];
		
		$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET emp_code='".$new_emp_code."',download_time=CURRENT_TIMESTAMP() 
								WHERE customer_code = '" .$customer_code."'";
		if(mysql_query($sqlupdatecustomerroute))
		{
			$sqlupddatetablestructure="UPDATE table_structure_master SET need_update='Y' 
						WHERE table_name  IN('customer_master','route_master','distributor_route_relation')";
		   if(mysql_query($sqlupddatetablestructure))
			{
				$sqldbupdateone="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$new_emp_code."'";
				mysql_query($sqldbupdateone);
				$sqldbupdatetwo="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$previous_emp_code."'";
				mysql_query($sqldbupdatetwo);
			}
		}
	}

	$GLOBALS['err_msg']="Customer mapping has been changed successfully.";
	disphtml("main();");
}
?>