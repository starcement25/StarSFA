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
	//if($mode =='add' || $mode =='edit')				 disphtml("show_add_edit($_REQUEST[row_id]);");
	//if($_POST['mode']=="change_mapping")				change_mapping();
	if($mode =='access')							disphtml("access_add_edit($_REQUEST[row_id]);");
	else 											disphtml("main();");
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
	$res = mysqli_query($link,$sql_count) or die(mysqli_error()." Error in count: ".$sql_count); 
	$row = mysqli_fetch_row($res);
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
			/*$row=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(CM.customer_code) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code 
				 ".$emp_hierarchy_condition_one.$sql_condition." ORDER BY EM.emp_name,CM.customer_name ASC"));*/
		}
		else
		{
			$sql="SELECT CM.customer_name,CM.customer_code,CM.acedns,CM.black_list,EM.emp_name FROM employee_master EM,customer_master CM 
				 WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007' ".$sql_condition." 
				ORDER BY EM.emp_name,CM.customer_name ASC LIMIT ".$GLOBALS[start].",".$GLOBALS[show];
			/*$row=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(CM.customer_name) FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code AND EM.emp_code<>'C0007' ".$sql_condition."  ORDER BY EM.emp_name,CM.customer_name ASC"));*/
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
	$rs=mysqli_query($link,$sql) or die(mysqli_error()." Error in main: ".$sql);
	$count=mysqli_num_rows($rs);
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
                                    $resultqueryemp = mysqli_query($link,$sqlqueryemp);
                                    $countemp=mysqli_num_rows($resultqueryemp);
                                    if($countemp>0){
                                    while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
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
					<td align="center" class="ERR"><?php if($_REQUEST['mod']=="succ"){ echo 'Customer Information edited successfully.';}?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%"></td>
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
				while($rec=mysqli_fetch_assoc($rs))
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
	<form name="frm_opts" action="adminCustomerListingModified.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="search_mode" value="<?=$_REQUEST['search_mode']?>">
        <input type="hidden" name="emp_name" value="<?=$_REQUEST['emp_name']?>">
        <input type="hidden" name="customer_name" value="<?=$_REQUEST['customer_name']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminCustomerListingModified.php">
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
	$customercodeerror='';
	$customernameerror='';
	$customertypeerror='';
	$customerphoneerror='';
	$customeremailerror='';
	$customerstateerror='';
	$customerrouteerror='';
	$customeremperror='';
	$customercrediterror='';
	$customercreditdayserror='';
	$customerbrancherror='';
	$customeraddresserror='';
	$customerpanerror='';
	$customertinerror='';
	$customerpinerror='';
	$customerincotermserror='';
	$customersaudalimiterror='';
	$customertransporterror='';
	$customercapacityerror='';
	$customerfreighterror='';
	$customerfreightinputerror='';
	$customerproductgrouperror='';
	$mod = $_REQUEST['mod'];
	$state_code_GST_array=array();
	$state_name_array=array();
	$error_array=array();

	if($mod == 'change_mapping')
	{
		if($_REQUEST['sec_freight']!='')
		{
		  if (empty($_REQUEST['route'])) {
				$customerfreightinputerror = "Route required";
			}
			if (empty($_REQUEST['branch_name'])) {
				$customerfreightinputerror = " Branch required";
			}
			if (empty($_REQUEST['transport_mode'])) {
				$customerfreightinputerror = " Transport mode required";
			}
			if (empty($_REQUEST['loadability_ton'])) {
				$customerfreightinputerror = " Capacity required";
			}
			if(filter_var($_REQUEST['loadability_ton'], FILTER_VALIDATE_FLOAT) === false ) {
				$customerfreightinputerror = " Capacity value improper";
			}
		  if(filter_var($_REQUEST['sec_freight'], FILTER_VALIDATE_INT) === false ) {
			$customerfreightinputerror = " Input freight have to integer";
			array_push($error_array,$customerfreightinputerror);
		   }
		   else
		   {
			if($customerfreightinputerror=='')
			{
			$sqlbranchdestinationfreight="SELECT branch_code FROM branch_route_freight WHERE branch_code='".addslashes($_REQUEST['branch_name'])."' AND 
								route_code=".$_REQUEST['route']." AND transport_mode='".addslashes($_REQUEST['transport_mode'])."' AND 
								capacity='".addslashes($_REQUEST['loadability_ton'])."' and acedns='Y'";
			$rsbranchdestinationfreight=mysqli_query($link,$sqlbranchdestinationfreight);
			$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);
			if($countbranchdestinationfreight < 1){
				$sqlbranchdestinationfreight  = "insert into branch_route_freight ";
				$sqlbranchdestinationfreight .= " SET branch_code='".$_REQUEST['branch_name']."'";
				$sqlbranchdestinationfreight .= " ,route_code=".$_REQUEST['route']."";
				$sqlbranchdestinationfreight .= " ,acedns='Y'";
				$sqlbranchdestinationfreight .= " ,freight='".$_REQUEST['sec_freight']."'";
				$sqlbranchdestinationfreight .= " , `date`=CURDATE()";
				$sqlbranchdestinationfreight .= " , transport_mode='".$_REQUEST['transport_mode']."'";
				$sqlbranchdestinationfreight .= " , capacity='".$_REQUEST['loadability_ton']."'";
				$sqlbranchdestinationfreight .= " , state_code=''";
				$sqlbranchdestinationfreight .= " , vertical_value=''";
				$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlbranchdestinationfreight);
		   		}
			}
		   }
		}
		if ($_REQUEST['incoterms']=='FOR PLANT' || $_REQUEST['incoterms']=='FOR DEPOT') {
		$sqlcustomerfreightchk="SELECT * FROM branch_route_freight WHERE branch_code='".addslashes($_REQUEST['branch_name'])."' AND 
								route_code=".$_REQUEST['route']." AND transport_mode='".addslashes($_REQUEST['transport_mode'])."' AND 
								capacity='".addslashes($_REQUEST['loadability_ton'])."' and acedns='Y'";
		$rscustomerfreightchk=mysqli_query($link,$sqlcustomerfreightchk);
		$countcustomerfreightchk=mysqli_num_rows($rscustomerfreightchk);
		if($countcustomerfreightchk< 1)
		{
			$customerfreighterror="Secondary Freight does not exist for this Route.";
			array_push($error_array,$customerfreighterror);
		}
		}
		
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
		$sqlcustomercodechk="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomercodechk=mysqli_query($link,$sqlcustomercodechk);
		$rowcustomercodechk=mysqli_fetch_assoc($rscustomercodechk);
		$dns_customer_code=$rowcustomercodechk['dns_customer_code'];
	if (empty($_REQUEST['customer_name']) || ctype_space($_REQUEST['customer_name'])) {
		$customernameerror = "Customer name is required";
		array_push($error_array,$customernameerror);
	}
	if (empty($_REQUEST['cust_type'])) {
		$customertypeerror = "Customer type is required";
		array_push($error_array,$customertypeerror);
	}
	if (empty($_REQUEST['phone_no'])) {
		$customerphoneerror = "Phone no is required and have to 10 digit numeric";
		array_push($error_array,$customerphoneerror);
	}
	else if(!preg_match('/^[0-9]{10}+$/', $_REQUEST['phone_no']))
	{
		$customerphoneerror = "Phone no have to 10 digit numeric";
		array_push($error_array,$customerphoneerror);
	}
	else 
	{
		if(($_REQUEST['cust_type']=='D' || $_REQUEST['cust_type']=='CORPORATE') && $_REQUEST['tagged_ss']==''){
		$sqlcustomerphonechk="SELECT phone_no FROM customer_master WHERE phone_no='".addslashes($_REQUEST['phone_no'])."' AND 
							customer_code!='".$customer_code."' AND acedns='Y'";
		$rscustomerphonechk=mysqli_query($link,$sqlcustomerphonechk);
		$countcustomerphonechk=mysqli_num_rows($rscustomerphonechk);
		if($countcustomerphonechk > 0)
		{
			$customerphoneerror="Phone no already exists.";
			array_push($error_array,$customerphoneerror);
		}
		}
		$sqlphonechkemp="SELECT phone_no FROM employee_master WHERE phone_no='".addslashes($_REQUEST['phone_no'])."'";
		$rsphonechkemp=mysqli_query($link,$sqlphonechkemp);
		$countphonechkemp=mysqli_num_rows($rsphonechkemp);
		if($countphonechkemp > 0)
		{
			$customerphoneerror="Phone no already exists for employee.";
			array_push($error_array,$customerphoneerror);
		}
	}
	if (empty($_REQUEST['email'])) {
		$customeremailerror = "Email is required";
		array_push($error_array,$customeremailerror);
	}
	else if(filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) === false)
	{
		$customeremailerror = "Invalid Email";
		array_push($error_array,$customeremailerror);
	}
	else 
	{
		if(($_REQUEST['cust_type']=='D' || $_REQUEST['cust_type']=='CORPORATE') && $_REQUEST['tagged_ss']==''){
		$sqlcustomeremailchk="SELECT email FROM customer_master WHERE email='".addslashes($_REQUEST['email'])."' AND 
							customer_code!='".$customer_code."' AND acedns='Y'";
		$rscustomeremailchk=mysqli_query($link,$sqlcustomeremailchk);
		$countcustomeremailchk=mysqli_num_rows($rscustomeremailchk);
		if($countcustomeremailchk > 0)
		{
			$customeremailerror="Email already exists.";
			array_push($error_array,$customeremailerror);
		}
		}
	}
	if (empty($_REQUEST['state_code'])) {
		$customerstateerror = "State is required";
		array_push($error_array,$customerstateerror);
	}
	if (empty($_REQUEST['route'])) {
		$customerrouteerror = "Route is required";
		array_push($error_array,$customerrouteerror);
	}
	if (empty($_REQUEST['emp_name'])) {
		$customeremperror = "Employee is required";
		array_push($error_array,$customeremperror);
	}
	if($_REQUEST['credit_limit']!='ADVANCE')
	{
	if (filter_var($_REQUEST['credit_limit'], FILTER_VALIDATE_INT) === false ) {
		$customercrediterror = "Credit limit is required";
		array_push($error_array,$customercrediterror);
	}
	}
	if (filter_var($_REQUEST['credit_days'], FILTER_VALIDATE_INT) === false) {
		$customercreditdayserror = "Credit days is required";
		array_push($error_array,$customercreditdayserror);
	}
	else if($_REQUEST['credit_days'] > 365)
	{
		$customercreditdayserror = "Credit days have to <= 365 days";
		array_push($error_array,$customercreditdayserror);
	}
	if (empty($_REQUEST['branch_name'])) {
		$customerbrancherror = "Branch is required";
		array_push($error_array,$customerbrancherror);
	}
	if (empty($_REQUEST['address']) || ctype_space($_REQUEST['address'])) {
		$customeraddresserror = "Address is required";
		array_push($error_array,$customeraddresserror);
	}
	if (empty($_REQUEST['PAN'])) {
		$customerpanerror = "PAN is required and have to 10 diigit Alphanumeric";
		array_push($error_array,$customerpanerror);
	}
	else if (strlen($_REQUEST['PAN'])!='10') {
		$customerpanerror = "PAN have to 10 diigit Alphanumeric";
		array_push($error_array,$customerpanerror);
	}
	else if(!preg_match("/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/", $_REQUEST['PAN'])) {
		$customerpanerror = "Invalid PAN";
		array_push($error_array,$customerpanerror);
	}
	else
	{
		if(($_REQUEST['cust_type']=='D' || $_REQUEST['cust_type']=='CORPORATE') && $_REQUEST['tagged_ss']==''){
		$sqlcustomerPANchk="SELECT  PAN FROM customer_master WHERE PAN='".addslashes($_REQUEST['PAN'])."' AND 
							customer_code!='".$customer_code."' AND acedns='Y'";
		$rscustomerPANchk=mysqli_query($link,$sqlcustomerPANchk);
		$countcustomerPANchk=mysqli_num_rows($rscustomerPANchk);
		if($countcustomerPANchk > 0)
		{
			$customerpanerror="PAN already exists.";
			array_push($error_array,$customerpanerror);
		}
		}
	}
	if (empty($_REQUEST['TIN'])) {
		$customertinerror = "TIN is required have to 15 diigit Alphanumeric";
		array_push($error_array,$customertinerror);
	}
	else if (strlen($_REQUEST['TIN'])!='3') {
		$customertinerror = "Maximum input 3 digit Alphanumeric";
		array_push($error_array,$customertinerror);
	}
	else if(!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $_REQUEST['TIN']))
	{
		$customertinerror = "TIN have to Alphanumeric";
		array_push($error_array,$customertinerror);
	}
	else{
		if(($_REQUEST['cust_type']=='D' || $_REQUEST['cust_type']=='CORPORATE') && $_REQUEST['tagged_ss']==''){
		$sqlcustomerTINchk="SELECT TIN FROM customer_master WHERE TIN='".addslashes($_REQUEST['TIN'])."' AND 
							customer_code!='".$customer_code."' AND acedns='Y'";
		$rscustomerTINchk=mysqli_query($link,$sqlcustomerTINchk);
		$countcustomerTINchk=mysqli_num_rows($rscustomerTINchk);
		if($countcustomerTINchk > 0)
		{
			$customertinerror="TIN already exists.";
			array_push($error_array,$customertinerror);
		}
		}
	  }
	if (empty($_REQUEST['pin'])) {
		$customerpinerror = "PIN is required";
		array_push($error_array,$customerpinerror);
	}
	else if(filter_var($_REQUEST['pin'], FILTER_VALIDATE_INT) === false ) {
		$customerpinerror = "PIN have to integer";
		array_push($error_array,$customerpinerror);
	}
	if (empty($_REQUEST['incoterms'])) {
		$customerincotermserror = "Incoterms is required";
		array_push($error_array,$customerincotermserror);
	}
	if (empty($_REQUEST['sauda_limit'])) {
		$customersaudalimiterror = "Sauda limit is required";
		array_push($error_array,$customersaudalimiterror);
	}
	else if(filter_var($_REQUEST['sauda_limit'], FILTER_VALIDATE_INT) === false ) {
		$customersaudalimiterror = "Sauda limit have to integer";
		array_push($error_array,$customersaudalimiterror);
	}
	if (empty($_REQUEST['transport_mode'])) {
		$customertransporterror = "Transport mode is required";
		array_push($error_array,$customertransporterror);
	}
	if ($_REQUEST['incoterms']=='FOR PLANT' || $_REQUEST['incoterms']=='FOR DEPOT' || $_REQUEST['incoterms']=='EX DEPOT') {
	if (empty($_REQUEST['loadability_ton'])) {
		$customercapacityerror = "Capacity is required";
		array_push($error_array,$customercapacityerror);
	}
	else if(filter_var($_REQUEST['loadability_ton'], FILTER_VALIDATE_FLOAT) === false ) {
		$customercapacityerror = "Capacity have to integer or decimal value";
		array_push($error_array,$customercapacityerror);
	}
	else if($_REQUEST['loadability_ton'] > 35)
		{
			$customercapacityerror = "Capacity have to <=35";
			array_push($error_array,$customercapacityerror);
		}
	}
	$product_group_code_array=$_POST['product_group_code'];
	if(count($product_group_code_array)==0)
	{
		$customerproductgrouperror = "Please check at least one Product Category";
		array_push($error_array,$customerproductgrouperror);
	}
	$sqlquerystate="SELECT state_code_GST FROM state_master where state =".$_REQUEST['state_code']."";
    $resultquerystate = mysqli_query($link,$sqlquerystate);
	$rowquerystate = mysqli_fetch_assoc($resultquerystate);
	$state_code_GST=$rowquerystate['state_code_GST'];
	if(strlen($state_code_GST)==1)
	{
		$state_code_GST='0'.$state_code_GST;
	}
	$staticTIN=$state_code_GST.$_REQUEST['PAN'];
	if(count($error_array)==0)
	{
		change_mapping();
	}
	$customer_name=$_REQUEST['customer_name'];
	$phone_no=$_REQUEST['phone_no'];
	$email=$_REQUEST['email'];
	$state_code=$_REQUEST['state_code'];
	$route=$_REQUEST['route'];
	$emp_code=$_REQUEST['emp_name'];
	$credit_limit=$_REQUEST['credit_limit'];
	$credit_days=$_REQUEST['credit_days'];
	$branch_name=$_REQUEST['branch_name'];
	$address=$_REQUEST['address'];
	$TIN=$_REQUEST['TIN'];
	$PAN=$_REQUEST['PAN'];
	$pin=$_REQUEST['pin'];
	$incoterms=$_REQUEST['incoterms'];
	$transport_mode=$_REQUEST['transport_mode'];
	$loadability_ton=$_REQUEST['loadability_ton'];
	$cust_type=$_REQUEST['cust_type'];
	$rds_tag=$_REQUEST['tagged_ss'];
	$sauda_limit=$_REQUEST['sauda_limit'];
	$oil_category_array=$_POST['product_group_code'];
   }
   else{
	if(modified_customer_emp_route=='no'){
	$sqlcustomer="SELECT CM.customer_name,CM.emp_code,CM.branch_code,EM.emp_name FROM customer_master CM,employee_master EM 
				WHERE CM.customer_code = '".$customer_code."' AND CM.emp_code=EM.emp_code";
	}
	else
	{
		$sqlcustomer="SELECT CM.dns_customer_code,CM.customer_name,CM.route_code,CM.branch_code,CRR.emp_code,CM.state_code,
					CM.credit_limit,CM.credit_days,CM.address,CM.TIN,CM.PAN,CM.pin,CM.incoterms,CM.phone_no,CM.email,
					CM.transport_mode,CM.sauda_limit,CM.loadability_ton,CM.cust_type,CM.rds_tag
					FROM customer_master CM 
					INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code 
					WHERE CM.customer_code = '".$customer_code."' ";	
	}
	$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in show customer: ".$sqlcustomer);
	$rowcustomer=mysqli_fetch_assoc($rscustomer);
	$dns_customer_code=$rowcustomer['dns_customer_code'];
	$customer_name=$rowcustomer['customer_name'];
	$email=$rowcustomer['email'];
	$phone_no=$rowcustomer['phone_no'];
	$state_code="'".$rowcustomer['state_code']."'";
	$route="'".$rowcustomer['route_code']."'";
	//$emp_code=$rowcustomer['emp_code'];
	$credit_limit=$rowcustomer['credit_limit'];
	$credit_days=$rowcustomer['credit_days'];
	$branch_name=$rowcustomer['branch_code'];
	$address=$rowcustomer['address'];
	$TIN=substr($rowcustomer['TIN'],12,3);
	$PAN=$rowcustomer['PAN'];
	$pin=$rowcustomer['pin'];
	$incoterms=$rowcustomer['incoterms'];
	$transport_mode=$rowcustomer['transport_mode'];
	$loadability_ton=$rowcustomer['loadability_ton'];
	$cust_type=$rowcustomer['cust_type'];
	$rds_tag="'".$rowcustomer['rds_tag']."'";
	$sqlempcode="SELECT emp_code FROM customer_route_emp_relation WHERE customer_code='".$customer_code."' AND emp_code 
				NOT IN(SELECT DISTINCT reporting_to FROM employee_master) AND acedns='Y'";
	$rsempcode=mysqli_query($link,$sqlempcode);
	$rowempcode=mysqli_fetch_assoc($rsempcode);
	$emp_code=$rowcustomer['emp_code'];			
	
	$sqlsaudalimit="SELECT sauda_limit FROM customer_sauda_limit where customer_code='".$dns_customer_code."'";
	$rssaudalimit=mysqli_query($link,$sqlsaudalimit);
	$rowsaudalimit=mysqli_fetch_assoc($rssaudalimit);
	$sauda_limit=$rowsaudalimit['sauda_limit'];
	
	$sqlbranch="SELECT branch_code FROM customer_branch_relation where customer_code='".$customer_code."'";
	$rsbranch=mysqli_query($link,$sqlbranch);
	$rowbranch=mysqli_fetch_assoc($rsbranch);
	$branch_name=$rowbranch['branch_code'];
	
	$sqlquerystate="SELECT state_code_GST FROM state_master where state =".$state_code."";
    $resultquerystate = mysqli_query($link,$sqlquerystate);
	$rowquerystate = mysqli_fetch_assoc($resultquerystate);
	$state_code_GST=$rowquerystate['state_code_GST'];
	if(strlen($state_code_GST)==1)
	{
		$state_code_GST='0'.$state_code_GST;
	}
	$staticTIN=$state_code_GST.$PAN;
	$oil_category_array=array();
	$sqlcustomerproductmapping="SELECT oil_category FROM customer_product_relation WHERE acedns='Y' AND customer_code = '".$customer_code."'";
	$rscustomerproductmapping=mysqli_query($link,$sqlcustomerproductmapping);
	while($rowcustomerproductmapping=mysqli_fetch_assoc($rscustomerproductmapping))
	{
		if(!in_array($rowcustomerproductmapping['oil_category'],$oil_category_array))
		{
			array_push($oil_category_array,$rowcustomerproductmapping['oil_category']);
		}
	}
  }
?>
<script type="text/javascript" src="ajax1.js"></script>
<script language="JavaScript" type="text/javascript">
function state_route(state){
		if(document.getElementById("state_code").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=stateroutecust','route_select_div',0);
	}
	function checked_all()
	{
	  checkboxes = document.getElementsByName('product_group_code[]');
	  if(document.getElementById("all_checked").checked==true)
	  {
		  for(var i in checkboxes)
		  checkboxes[i].checked = true;
	  }
	  else
	  {
		   for(var i in checkboxes)
		  checkboxes[i].checked = false;
	  }
	}
	function populate_TIN()
	{
		var state_val=document.getElementById("state_code").value;
        //var state_val_final = state_val.replace("'", "");
		//alert(state_val);
		var pan_val=document.getElementById("PAN").value;
		//alert(pan_val);
		var GST_state_code=document.getElementById("stateval_"+state_val).value;
		if(GST_state_code.length==1)
		{
			 GST_state_code='0'+GST_state_code;
		}
		//alert(GST_state_code);
		document.getElementById("showtin").innerHTML =GST_state_code+pan_val.toUpperCase();
	}
	function propulate_credit_limit(creditdays)
	{
		if(creditdays==0)
		{
			document.getElementById("credit_limit").value='ADVANCE';
		}
		else
		{
			document.getElementById("credit_limit").value='';
		}
	}
	function populate_capacity(incoterms)
	{
		if(incoterms=='FOR PLANT' || incoterms=='FOR DEPOT' || incoterms=='EX DEPOT')
		{
			document.getElementById("capacity_tr").style.display='';
		}
		else
		{
			document.getElementById("capacity_tr").style.display='none';
		}
	}
function tag_ss(tagval)
	{
		//alert(tagval);
		if(document.getElementById("checkbox_ss").checked==true)
	  	{
			//alert(tagval);
			document.getElementById("tagged_ss_tr").style.display='';
		}
		else
		{
			document.getElementById("tagged_ss_tr").style.display='none';
			document.getElementById("tagged_ss").value='';
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
<style>
.error{
color:red;
font-weight:bold;
}
</style>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Edit Customer</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminCustomerListingModified.php" >
			<input type="hidden" name="mod" value="change_mapping">
            <input type="hidden" name="mode" value="access">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="70%" align="center" class="border" cellpadding="5" cellspacing="2">
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
				<?php }
                if($mod == 'change_mapping' && $customerfreighterror!=''){?>
				<tr>
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000"><?php echo $customerfreighterror;?></font></strong><br /><br /><br />
                    	Input Freight:<input type="text" name="sec_freight" id="sec_freight" class="inplogin" style="width:80px;height:15px;" value="<?php echo $_REQUEST['sec_freight'];?>"/>&nbsp;<input type="submit" value=" SAVE " class="inplogin">&nbsp;<span class="error"><?php echo $customerfreightinputerror;?></span>
                    </td>
				</tr>
				<?php }?>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Customer Code<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="dns_customer_code" id="dns_customer_code" class="inplogin" style="width:100px;height:15px;" value="<?php echo $dns_customer_code;?>"/ readonly></td>
				</tr>
				<tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Customer Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="customer_name" id="customer_name" class="inplogin" style="width:300px;height:30px;" value="<?php echo $customer_name;?>"/>&nbsp;<span class="error"><?php echo $customernameerror;?></span></td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Customer Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    	<select name="cust_type" id="cust_type"  >
                            <option value="">SELECT</option>
                             <!--option value="D">D</option>
                             <option value="SS">SS</option-->
                             <?php 
                                $sqlquerycusttype="SELECT DISTINCT cust_type FROM customer_master where customer_code 
											IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation  WHERE acedns='Y') ORDER BY cust_type ASC";
                                $resultquerycusttype = mysqli_query($link,$sqlquerycusttype);
                                $countquerycusttype=mysqli_num_rows($resultquerycusttype);
                                if($countquerycusttype>0){
                                while($rowquerycusttype = mysqli_fetch_assoc($resultquerycusttype))
                                {
									if( $cust_type==$rowquerycusttype['cust_type']) { $selected='selected';}
									else{ $selected='';}
									if($rowquerycusttype['cust_type']=='CORPORATE'){ $cust_type_disp='CORPORATE';}
									if($rowquerycusttype['cust_type']=='D'){ $cust_type_disp='DEALER';}
									if($rowquerycusttype['cust_type']=='R'){ $cust_type_disp='RETAILER';}
									if($rowquerycusttype['cust_type']=='SS'){ $cust_type_disp='SUPER STOCKIST';}
                                echo "<option value='".$rowquerycusttype['cust_type']."' ".$selected.">".$cust_type_disp."</option>";
                                }
                              }
                            ?>	
                        </select>
                      &nbsp;<span class="error"><?php echo $customertypeerror;?></span></td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">&nbsp;</td>
					<td width="3%" align="left" valign="top" class="tbllogin">&nbsp;</td>
					<td align="left" valign="top">If Tagged SS is yes then please tick the checkbox&nbsp;<input type="checkbox" name="checkbox_ss" id="checkbox_ss"  class="inplogin" value="1" <?php if($_POST['checkbox_ss']=='1' || $rds_tag!=''){?>checked<?php }?> onchange="javascript:tag_ss();"/></td>
				</tr>
                 <tr id="tagged_ss_tr" style="display: <?php if($_POST['checkbox_ss']=='1' || $rds_tag!=''){?>''<?php }else{?>none<?php }?>">
					<td width="20%" align="left" valign="top" class="tbllogin">Tagged SS</td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="tagged_ss" id="tagged_ss">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryss="SELECT dns_customer_code,customer_code,customer_name FROM customer_master where cust_type IN('SS','CORPORATE') 
								AND acedns='Y'";
                                $resultqueryss = mysqli_query($link,$sqlqueryss);
                                $countqueryss=mysqli_num_rows($resultqueryss);
                                if($countqueryss>0){
                                while($rowqueryss = mysqli_fetch_assoc($resultqueryss))
                                {
									if( $rds_tag=="'".$rowqueryss['customer_code']."'") { $selected='selected';}
									else{ $selected='';}
                                echo "<option value=\"'".$rowqueryss['customer_code']."'\" ".$selected.">".$rowqueryss['customer_name']."</option>";
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Phone no<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="phone_no" id="phone_no" maxlength="10" class="inplogin" style="width:100px;height:20px;" value="<?php echo $phone_no;?>"/>&nbsp;<span class="error"><?php echo $customerphoneerror;?></span></td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Email<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="email" id="email"  class="inplogin" style="width:150px;height:20px;" 
                    value="<?php echo $email;?>"/>&nbsp;<span class="error"><?php echo $customeremailerror;?></span></td>
				</tr>
				<tr>
					<td align="left" valign="top" class="tbllogin">Employee<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="emp_name" id="emp_name" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' AND emp_code NOT IN(SELECT DISTINCT reporting_to FROM employee_master) ORDER BY emp_name ASC";
                                $resultqueryemp = mysqli_query($link,$sqlqueryemp);
                                $countemp=mysqli_num_rows($resultqueryemp);
                                if($countemp>0){
                                while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
                                {
                                ?>
                                <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $emp_code==$rowqueryemp['emp_code'])
								{echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $customeremperror;?></span>
                    </td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Credit Days<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="credit_days" id="credit_days" class="inplogin" style="width:100px;height:20px;" value="<?php echo $credit_days;?>" onblur="javascript:propulate_credit_limit(this.value);"/>&nbsp;<span class="error"><?php echo $customercreditdayserror;?></span></td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Credit Limit<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="credit_limit" id="credit_limit"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $credit_limit;?>" maxlength="13"/>&nbsp;<span class="error"><?php echo $customercrediterror;?></span></td>
				</tr>
                 <tr>
					<td align="left" valign="top" class="tbllogin">Branch<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="branch_name" id="branch_name" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlquerybranch="SELECT branch_code,branch_name FROM branch_master WHERE acedns='Y' ORDER BY branch_name ASC";
                                $resultquerybranch = mysqli_query($link,$sqlquerybranch);
                                $countquerybranch=mysqli_num_rows($resultquerybranch);
                                if($countquerybranch>0){
                                while($rowquerybranch = mysqli_fetch_assoc($resultquerybranch))
                                {
                                ?>
                                <option value="<?php echo $rowquerybranch['branch_code'];?>" <?php if( $branch_name==$rowquerybranch['branch_code']){echo 'selected';}?>><?php echo $rowquerybranch['branch_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $customerbrancherror;?></span>
                    </td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin" >Address<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" ><input type="text" name="address" id="address" class="inplogin" style="width:300px;height:30px;" value="<?php echo $address;?>"/>&nbsp;<span class="error"><?php echo $customeraddresserror;?></span></td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">PIN<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="pin" id="pin"  class="inplogin" style="width:100px;height:20px;" 
                    value="<?php echo $pin;?>"/>&nbsp;<span class="error"><?php echo $customerpinerror;?></span></td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">State<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="state_code" id="state_code" onchange="state_route(this.value);" >
                            <option value="">SELECT</option>
                                <!--<option value=<?php /*echo $rowquerystate['state'];?> <?php if( $_REQUEST['state_code']==$rowquerystate['state']){echo 'selected';}?>><?php echo $rowquerystate['state'];*/?></option>!-->
								<?php 
                                $sqlquerystate="SELECT state,state_code_GST FROM state_master where state IN(SELECT CM.state_code FROM customer_master CM INNER JOIN customer_route_emp_relation CRR ON CM.customer_code=CRR.customer_code AND CRR.acedns='Y') ORDER BY state ASC";
                                $resultquerystate = mysqli_query($link,$sqlquerystate);
                                $countquerystate=mysqli_num_rows($resultquerystate);
                                if($countquerystate>0){
                                while($rowquerystate = mysqli_fetch_assoc($resultquerystate))
                                {
									array_push($state_code_GST_array,$rowquerystate['state_code_GST']);
									array_push($state_name_array,$rowquerystate['state']);
									if( strtoupper($state_code)=="'".strtoupper($rowquerystate['state'])."'") { $selected='selected';}
									else{ $selected='';}
                                	echo "<option value=\"'".$rowquerystate['state']."'\" ".$selected.">".$rowquerystate['state']."</option>";
                                }
                            }
                            ?>	
                        </select>
                        <?php 
						for($i=0;$i< count($state_name_array);$i++){
							?>
                        <input type='hidden' name="stateval_<?php echo $state_name_array[$i];?>" id="stateval_<?php echo "'".$state_name_array[$i]."'";?>" 
                        value="<?php echo $state_code_GST_array[$i];?>" />
                        <?Php }?>
                        &nbsp;<span class="error"><?php echo $customerstateerror;?></span>
                    </td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Route Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><div id="route_select_div">
                    <?php 
					/*echo $sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_route_emp_relation CM,route_master RM 
											WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND 
												CM.state_code IN(".$state_code.") AND CM.cust_type <> 'R'  ORDER BY RM.route_name ASC";*/
					echo "<select name=\"route\" id=\"route\">";
					echo "<option value=\"\">Choose Route</option>";
					$sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_route_emp_relation CM,route_master RM,customer_master CMA
											WHERE CM.route_code=RM.route_code AND CM.customer_code=CMA.customer_code AND RM.route_name!='' AND CM.acedns='Y' AND 
												CMA.state_code IN(".$state_code.") AND CMA.cust_type <> 'R'  ORDER BY RM.route_name ASC";
					$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
					$countcustomerroute=mysqli_num_rows($resultcustomerroute);
					if($countcustomerroute>0){
						while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
						{
							$route_name=$rowscustomerroute['route_name'];
							$route_code=$rowscustomerroute['route_code'];
							if( $route=="'".$route_code."'") { $selected='selected';}
							else{ $selected='';}
							$option_value_string.="<option value=\"'".$route_code."'\" ".$selected.">".strtoupper($route_name)."</option>";
							$route_code_string .= "'".$route_code."',";
						}
					}
					$route_code_string = rtrim($route_code_string,",");
					//echo "<option value=\"".$route_code_string."\">All</option>";
					echo $option_value_string;
					echo "</select>";
				?>&nbsp;<span class="error"><?php echo $customerrouteerror;?></span>
                    </div>
                    </td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Incoterms<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="incoterms" id="incoterms" onchange="javascript:populate_capacity(this.value);">
                     <option value="">SELECT</option><option value="EX PLANT" <?php if( strtoupper($incoterms)=='EX PLANT'){echo 'selected';}?>>EX PLANT</option><option value="FOR PLANT" <?php if( strtoupper($incoterms)=='FOR PLANT'){echo 'selected';}?>>FOR PLANT</option><option value="EX DEPOT" <?php if( strtoupper($incoterms)=='EX DEPOT'){echo 'selected';}?>>EX DEPOT</option><option value="FOR DEPOT" <?php if( strtoupper($incoterms)=='FOR DEPOT'){echo 'selected';}?>>
                     FOR DEPOT</option></select>&nbsp;<span class="error"><?php echo $customerincotermserror;?></span></td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin" >PAN<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" ><input type="text" name="PAN" id="PAN" class="inplogin" 
                    style="width:120px;height:20px;text-transform: uppercase;" value="<?php echo strtoupper($PAN);?>" maxlength="10" onBlur="javascript:populate_TIN();"/>&nbsp;<span class="error"><?php echo $customerpanerror;?></span></td>
				</tr>
                 <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">TIN/GST<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" ><span id="showtin"><?php echo strtoupper($staticTIN);?></span><input type="text" name="TIN"  id="TIN" class="inplogin" 
                    style="width:30px;height:20px;text-transform: uppercase;" value="<?php echo strtoupper($TIN);?>" maxlength="3" onClick="javascript:populate_TIN();" />&nbsp;<span class="error"><?php echo $customertinerror;?></span>
                    </td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Sauda Limit<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="sauda_limit" id="sauda_limit"  class="inplogin" style="width:100px;height:20px;" value="<?php if($sauda_limit!=''){echo $sauda_limit;}else{ echo '100';}?>"/>&nbsp;<span class="error"><?php echo $customersaudalimiterror;?></span></td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Transport Mode<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="transport_mode" id="transport_mode" >
                            <option value="">SELECT</option><option value="TRUCK" <?php if( strtoupper($transport_mode)=='TRUCK'){echo 'selected';}?>>TRUCK</option><option value="TANKER" <?php if( strtoupper($transport_mode)=='TANKER'){echo 'selected';}?>>TANKER</option></select>&nbsp;<span class="error"><?php echo $customertransporterror;?></span></td>
				</tr>
                <tr style="display:<?php if( strtoupper($incoterms)=='FOR PLANT' || strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='EX DEPOT'){ echo '';}else{?>none<?php }?>" id='capacity_tr'>
					<td width="20%" align="left" valign="top" class="tbllogin">Capacity(MT)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="loadability_ton" id="loadability_ton"  class="inplogin" style="width:50px;height:20px;" value="<?php echo $loadability_ton;?>"/>&nbsp;<span class="error"><?php echo $customercapacityerror;?></span></td>
				</tr>
				<tr>
                <td align="left" width="20%"  valign="top" class="tbllogin">Category<font color="#FF0000"><strong>*</strong></font>&nbsp;&nbsp;</td>
                  <td width="3%" align="left" valign="top" class="tbllogin">:</td>
                  <td align="left" valign="top"><div style="max-height:200px; overflow-y: scroll;">
                  <?php $product_group_code=$_REQUEST['product_group_code'];?>
                    <table >
                        <tr>
                            <td align="left">
                                 <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="error"><?php echo $customerproductgrouperror;?></span>
                            </td>
                         </tr>  
                        <?php 
                        $sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
                        $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                        while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                        {
							//print_r($_POST['product_group_code']);
							$sqloilcat="SELECT GROUP_CONCAT(oil_type SEPARATOR ',  ') AS oil_type FROM process_cost  
										WHERE oil_category='".$rowproductgroup['product_group_name']."'";
							$rsoilcat=mysqli_query($link,$sqloilcat);
							$rowoilcat=mysqli_fetch_assoc($rsoilcat);
							$oil_type_string='';
							if($rowoilcat['oil_type']!=''){
								$oil_type_string='['.strtoupper($rowoilcat['oil_type']).']';
							}
							?>
                            <tr>
                                <td align="left">
                                    <input type="checkbox" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_name'];?>" 
									<?php if(in_array($rowproductgroup['product_group_name'],$oil_category_array)){?>checked<?php }?>/><?php echo '<b>'.$rowproductgroup['product_group_name'].'</b><br />'.$oil_type_string;?>
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                    </table></div>
                </td>
              </tr>  
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminCustomerListingModified.php';" class="inplogin"></td>
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
	$email=$_REQUEST['email'];
	$state_code=$_REQUEST['state_code'];
	$route=$_REQUEST['route'];
	$emp_name=$_REQUEST['emp_name'];
	$credit_limit=$_REQUEST['credit_limit'];
	$credit_days=$_REQUEST['credit_days'];
	$branch_name=$_REQUEST['branch_name'];
	$address=$_REQUEST['address'];
	$PAN=$_REQUEST['PAN'];
	$sqlquerystate="SELECT state_code_GST FROM state_master where state=".$state_code."";
    $resultquerystate = mysqli_query($link,$sqlquerystate);
	$rowquerystate = mysqli_fetch_assoc($resultquerystate);
	$state_code_GST=$rowquerystate['state_code_GST'];
	if(strlen($state_code_GST)==1)
	{
		$state_code_GST='0'.$state_code_GST;
	}
	$TIN=$state_code_GST.$PAN.$_REQUEST['TIN'];
	$pin=$_REQUEST['pin'];
	$incoterms=$_REQUEST['incoterms'];
	$sauda_limit=$_REQUEST['sauda_limit'];
	$transport_mode=$_REQUEST['transport_mode'];
	$loadability_ton=$_REQUEST['loadability_ton'];
	$cust_type=$_REQUEST['cust_type'];
	$tagged_ss=$_REQUEST['tagged_ss'];
		if($tagged_ss!='')
		{
			$sqltagged_ss=",rds_tag=".$tagged_ss."";
		}
		else
		{
			$sqltagged_ss=",rds_tag=''";
		}
	$product_group_code_array=$_POST['product_group_code'];
	$product_group_code="'".implode("','", $product_group_code)."'";
	/*$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
	$rsroutechk=mysqli_query($link,$sqlroutechk);
	$countroutechk=mysqli_num_rows($rsroutechk);
	/*if(strtoupper($folderName)=='ASL')
	{
		$sqltownnamechk="SELECT town_name FROM town_master WHERE town_name='".addslashes($route_name)."'";
		$rstownnamechk=mysqli_query($link,$sqltownnamechk);
		$cnttownmamechk=mysqli_num_rows($rstownnamechk);
		if($cnttownmamechk==0)
		{
			echo "Route name not exists in town list.Please provide another route name at row ".($csv_row_count+1);
			die;
		}
	}*/
		/*if($countroutechk<1 && $route_name!='')
		{
			$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
			$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
			$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
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
			mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().
							Internal DATA execution problem on route table.PLease contact aceDNS admin.");				
			//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			$route_code=$max_route_code;
		}
		else
		{
			$rowroutechk=mysqli_fetch_assoc($rsroutechk);
			$route_code=$rowroutechk['route_code'];
			$route_name_db=$rowroutechk['route_name'];
			if($route_name_db !=$route_name)
			{
				$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',branch_code='".$branch_code."',download_time=CURRENT_TIMESTAMP() 
								WHERE route_code='".$route_code."'";
				mysqli_query($link,$sqlupdateroue) or  array_push($error_array,"mysqli_error().
							Internal DATA execution problem on route table.PLease contact aceDNS admin.");
			}
		}*/
		//For customer
		$sql  = "UPDATE customer_master ";
		$sql .= " SET  customer_name='".addslashes($customer_name)."'";
		$sql .= " , branch_code='".addslashes($branch_name)."'";
		$sql .= " , phone_no='".$phone_no."'";
		$sql .= " , email='".$email."'";
		$sql .= " , route_code=".$route."";
		$sql .= " , credit_limit='".$credit_limit."'";
		$sql .= " , credit_days='".$credit_days."'";
		$sql .= " , address='".$address."'";
		$sql .= " , TIN='".strtoupper($TIN)."'";
		$sql .= " , PAN='".strtoupper($PAN)."'";
		$sql .= " , state_code=".$state_code."";
		$sql .= " , cust_type='".addslashes($cust_type)."'".$sqltagged_ss;
		$sql .= " , incoterms='".addslashes($incoterms)."'";
		$sql .= " , loadability_ton='".addslashes($loadability_ton)."'";
		$sql .= " , transport_mode='".addslashes($transport_mode)."'";
		$sql .= " , pin='".addslashes($pin)."'";
		$sql .= " , retailer_app='yes'";
		$sql .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
		//exit();
		mysqli_query($link,$sql);
		$sqlchkchangepassword="SELECT emp_code FROM changepassword WHERE emp_code='".$customer_code."'";
		$rschkchangepassword=mysqli_query($link,$sqlchkchangepassword);
		$countchkchangepassword=mysqli_num_rows($rschkchangepassword);
		if($countchkchangepassword==0)
		{
			$sqlcp  = "insert into changepassword ";
			$sqlcp .= " SET emp_code='".$customer_code."'";
			$sqlcp .= " , newpassword='1234'";
			$sqlcp .= " , oldpassword='1234'"; 
			$sqlcp .= " , status='true'";
			$sqlcp .= " , is_licensed='1'"; 
			mysqli_query($link,$sqlcp);
		}
		$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 
									   customer_code='".$customer_code."'  AND emp_code='".$emp_name."' AND route_code=".$route."";
		$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);
		$countcustomerroute=mysqli_num_rows($rsselcustomerroute);
		if($countcustomerroute==0)
		{
			$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',
									 route_code=".$route.",
									emp_code='".$emp_name."',
									acedns='Y',
									download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlinsertcustomerroute);
			if($cust_type!='R')
			{
			$sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',
											route_code=".$route.",emp_code='".$emp_name."',download_time=CURRENT_TIMESTAMP()";
			$rsinsertdistributorroute=mysqli_query($link,$sqlinsertdistributorroute);
			}
		}
		else
		{
			$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE 
									customer_code='".$customer_code."' AND emp_code='".$emp_name."' AND route_code=".$route."";
			mysqli_query($link,$sqlupdatecustomerroute);
			$sqlupdatedistributorroute="UPDATE distributor_route_relation SET download_time=CURRENT_TIMESTAMP() 
									WHERE distributor_code='".$customer_code."' AND emp_code='".$emp_name."' AND route_code=".$route."";
			$rsupdatedistributorroute=mysqli_query($link,$sqlupdatedistributorroute);
		}
		/*$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code=".$route.",
								emp_code='".$emp_name."',
								acedns='Y',
								download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
		mysqli_query($link,$sqlupdatecustomerroute);*/
		
		$sqldnscustomercode="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$customer_code."'";
		$rsdnscustomercode=mysqli_query($link,$sqldnscustomercode);
		$rowdnscustomercode=mysqli_fetch_assoc($rsdnscustomercode);
		$dns_customer_code=$rowdnscustomercode['dns_customer_code'];
		
		$sqlupdatecustomersdaudalimit="UPDATE customer_sauda_limit ";
		$sqlupdatecustomersdaudalimit .= " SET sauda_limit='".$sauda_limit."'";
		$sqlupdatecustomersdaudalimit .= " 	,download_time=CURRENT_TIMESTAMP()  WHERE customer_code='".$dns_customer_code."'";
		mysqli_query($link,$sqlupdatecustomersdaudalimit);
		
		$sqlselcustomerbranch="SELECT customer_code FROM customer_branch_relation WHERE 
							customer_code='".$customer_code."'  AND branch_code='".addslashes($branch_name)."'";
		$rsselcustomerbranch=mysqli_query($link,$sqlselcustomerbranch);
		$countcustomerbranch=mysqli_num_rows($rsselcustomerbranch);
		if($countcustomerbranch==0)
		{
			$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation SET customer_code='".$customer_code."',
									branch_code='".addslashes($branch_name)."',acedns='Y',
								  download_time=CURRENT_TIMESTAMP()";
		    mysqli_query($link,$sqlinsertcustomerbranch);
		}
		else
		{
		$sqlupdatecustomerbranch="UPDATE customer_branch_relation SET branch_code='".addslashes($branch_name)."',acedns='Y',
								 download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
		mysqli_query($link,$sqlupdatecustomerbranch);
		}
		
		if(count($product_group_code_array) > 0)
		{
			$sqldelcustomerproduct="DELETE from customer_product_relation WHERE customer_code='".$customer_code."'";
			mysqli_query($link,$sqldelcustomerproduct);
			foreach($product_group_code_array as $product_group_code_val)
			{
				$sqlinsertproductrelation="INSERT INTO customer_product_relation SET 
											customer_code='".$customer_code."',
											oil_category='".$product_group_code_val."',
											acedns='Y',
											download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertproductrelation);							
			}
		}
//exit();
	$GLOBALS['err_msg']="Customer Information edited successfully.";
	header("location:adminCustomerListingModified.php?mod=succ");
		//exit();
	disphtml("main();");
}
?>