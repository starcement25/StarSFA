<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	if($mode == 'add')						   add_record();
	else    									 disphtml("main();");
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Customer Addition</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminCustomerAdd.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="add">			
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Add Customer</td>
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
					<td align="left" valign="top"><input type="text" name="customer_name" class="inplogin" style="width:300px;height:30px;" value="<?php echo $_REQUEST['customer_name'];?>"/></td>
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
                                <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $_REQUEST['emp_name']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Customer Type<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"> 
                    <select name="customer_type" class="inplogin" id="customer_type">
                    <option value=" " >SELECT</option>
                    <option value="D"  <?php if($_REQUEST['customer_type']=='D'){ echo 'selected';}?>>Distributor</option>
                    <option value="R" <?php if($_REQUEST['customer_type']=='R'){ echo 'selected';}?>>Retailer</option>
                    </select>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Add " class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
}//End of main()
function add_record()
{
	$customer_name=$_REQUEST['customer_name'];
	$emp_code=$_REQUEST['emp_name'];
	$customer_type=$_REQUEST['customer_type'];
	
	$sqlempdetails="SELECT EM.branch_code,RM.route_code,RD.rds_code FROM employee_master EM,route_master RM,rds_master RD WHERE 
				   EM.emp_code='".$emp_code."' AND EM.emp_code=RD.emp_code AND RM.emp_code=EM.emp_code";
	$rsempdetails=mysql_query($sqlempdetails);
	$rowempdetails=mysql_fetch_array($rsempdetails);
	$branch_code=$rowempdetails['branch_code'];
	$route_code=$rowempdetails['route_code'];
	$rds_code=$rowempdetails['rds_code'];

	$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
	$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
	$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
	$max_customer_code=$rowmaxcustomercode['max_customer_code'];
	$max_customer_code++;
	
	$sql  = "insert into customer_master ";
	$sql .= " SET customer_code='".$max_customer_code."'";
	$sql .= " , dns_customer_code=''";
	$sql .= " , customer_name='".addslashes($customer_name)."'";
	$sql .= " , branch_code='".$branch_code."'";
	$sql .= " , phone_no=''";
	$sql .= " , route_code='".$route_code."'";
	$sql .= " , emp_code='".$emp_code."'";
	$sql .= " , current_balance	=''";
	$sql .= " , credit_limit=''";
	$sql .= " , acedns='Y'";
	$sql .= " , black_list='N'";
	$sql .= " , TD=''";
	$sql .= " , rds_tag='".$rds_code."'";
	$sql .= " , cust_type='".$customer_type."'";
	$sql .= " , download_time=CURRENT_TIMESTAMP()";
	$sql .= " , added_by='".$_SESSION['admin_login']." ".$_SERVER['REMOTE_ADDR']."'";
	//exit();
	mysql_query($sql) or die(mysql_error()." Error in customer addition.");
	
	$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
	mysql_query($sqlInsertdatarefresh);
	$GLOBALS['err_msg']="Customer information has been added successfully.";
	disphtml("main();");
}
?>