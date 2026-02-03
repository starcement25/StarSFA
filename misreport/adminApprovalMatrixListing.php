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
	$sql_count = "SELECT COUNT(approval_authority_id) FROM approval_matrix ";
	/*if($_REQUEST['search_mode']=='search')
	{
		if($_REQUEST['product_name']!='')
		{
			$sql_count.="  AND PM.prod_desc LIKE '%".$_REQUEST['product_name']."%'";
		}
	}*/
	$res = mysqli_query($link,$sql_count) or die(mysqli_error()." Error in count: ".$sql_count); 
	$row = mysqli_fetch_row($res);
	$count =  $row[0];

	if($_REQUEST[hold_page] > 0)   	$GLOBALS[start] = $_REQUEST[hold_page];
	if($count == $GLOBALS[start])  	$GLOBALS[start] = $GLOBALS[start] - $GLOBALS[show];
	if($GLOBALS[start] < 0)		  $GLOBALS[start] = 0;
	
	/*if($_REQUEST['search_mode']=='search')
	{
		if($_REQUEST['product_name']!='')
		{
			$sql_condition.=" AND PM.prod_desc LIKE '%".$_REQUEST['product_name']."%'";
		}
	}
	else
	{
		$sql_condition="";
	}*/
   $sql="SELECT EM.emp_name,AM.* FROM 
			 employee_master EM INNER JOIN approval_matrix AM ON AM.approval_authority_id=EM.emp_code 
			 ORDER BY AM.approval_authority ASC,EM.emp_name ASC,AM.approval_sub_category ASC ";
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Approval Matrix Listing</strong></td>
	</tr>
    <!--tr>
		<td valign="top" >
			<table width="55%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
				<tr class="TDHEAD" > 
					<td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
				</tr>
				<tr > 
					<td width="15%" colspan="7" align="center">
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  >
                        <form name ="frmSearch" method="post" action="<?php //echo $_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
                        <input type="hidden" name="search_mode" value="search">
                        	<tr>
                                <td align="right" width="25%">Product Name:</td>
                                <td align="left" width="" style="vertical-align:top;">
                               		 <input type="text" value="<?php //echo $_REQUEST['product_name'];?>" name="product_name" id="product_name"></input>
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
	</tr-->
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="1">
            
				<tr> 
					<td align="center" class="ERR"><?php if($_REQUEST['mod']=="succ"){ echo 'Product Information edited successfully.';}?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%"><a href="adminApprovalMatrixCreation.php" title=" Create Approval Matrix " style="color: #F00;"><img src="images/plus_icon.gif" alt=""></a></td>
				</tr>
			</table>
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="8">Approval Matrix Information</td>
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
                    <td width="9%" align="left" style="padding-left:20px;">Designation</td>
					<td width="20%" align="left" style="padding-left:20px;">Employee</td>
                    <td width="16%" align="left" style="padding-left:20px;">Approval Category</td>
                    <td width="15%%" align="left" style="padding-left:20px;">Field Name</td>
                    <td width="11%" align="left" style="padding-left:20px;">Operation</td>
                     <td width="13%" align="left" style="padding-left:20px;">Approval Level</td>
                     <td width="10%" align="left" style="padding-left:20px;">Status</td>
				</tr>   
				<?php
				$cnt=$GLOBALS[start]+1;
				while($rec=mysqli_fetch_assoc($rs))
				{
					if($rec['is_active']=='yes') $status='ACTIVE';
					if($rec['is_active']=='no') $status='INACTIVE';
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes(strtoupper($rec['approval_authority']));?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['emp_name']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes(strtoupper($rec['approval_category']));?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes(strtoupper($rec['approval_sub_category']));?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes(strtoupper($rec['operation_type']));?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['approval_level']).'TIER';?></td>
                     <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($status);?></td>
				</tr>
			<?php 
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
	<form name="frm_opts" action="adminApprovalMatrixListing.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="search_mode" value="<?=$_REQUEST['search_mode']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminApprovalMatrixListing.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
<?php
}//End of main()

function access_add_edit($row_id)
{
	$prod_code=$row_id;
	$prodcodeerror='';
	$prodnameerror='';
	$verticalerror='';
	$prodgrouperror='';
	$prodsubgrouperror='';
	$uom1error='';
	$uom2error='';
	$conversion1error='';
	$packsizeerror='';
	$conversion2error='';
	$uom3error='';
	$uom4error='';
	$uom5error='';
	$clstkerror='';
	$grossweighterror='';
	$fgrmerror='';
	$hsnsacerror='';
	$gsterror='';
	$pakingrealizationerror='';
	$prodtypeerror='';
	$oilformulationerror='';
	$parentproducterror='';
	$addsubtracterror='';
	$isflasherror='';
	$flashnameerror='';
	$mod = $_REQUEST['mod'];
	$error_array=array();

	if($mod == 'change_mapping')
	{
	
	if (empty($_REQUEST['prod_desc']) || ctype_space($_REQUEST['prod_desc'])) {
		$prodnameerror = "Product name is required";
		array_push($error_array,$prodnameerror);
	}
	if (empty($_REQUEST['vertical'])) {
		$verticalerror = "Vertcal is required";
		array_push($error_array,$verticalerror);
	}
	if (empty($_REQUEST['product_group_code'])) {
		$prodgrouperror = "Product group is required";
		array_push($error_array,$prodgrouperror);
	}
	if (empty($_REQUEST['product_sub_group_code'])) {
		$prodsubgrouperror = "Product sub group is required";
		array_push($error_array,$prodsubgrouperror);
	}
	if (empty($_REQUEST['UOM1'])) {
		$uom1error = "UOM1 is required";
		array_push($error_array,$uom1error);
	}
	if (empty($_REQUEST['UOM2'])) {
		$uom2error = "UOM2 is required";
		array_push($error_array,$uom2error);
	}
	if (empty($_REQUEST['conversion1'])) {
		$conversion1error = "Conversion1 is required";
		array_push($error_array,$conversion1error);
	}
	else if(filter_var($_REQUEST['conversion1'], FILTER_VALIDATE_FLOAT) === false ) {
		$conversion1error = "Conversion1 have to integer or decimal value";
		array_push($error_array,$conversion1error);
	}
	if (empty($_REQUEST['pack_size'])) {
		$packsizeerror = "Pack size is required";
		array_push($error_array,$packsizeerror);
	}
	if (empty($_REQUEST['UOM3'])) {
		$uom3error = "UOM3 days is required";
		array_push($error_array,$uom3error);
	}
	if (empty($_REQUEST['conversion2'])) {
		$conversion2error = "Conversion2 is required";
		array_push($error_array,$conversion2error);
	}
	else if(filter_var($_REQUEST['conversion2'], FILTER_VALIDATE_FLOAT) === false ) {
		$conversion2error = "Conversion2 have to integer or decimal value";
		array_push($error_array,$conversion2error);
	}
	if (empty($_REQUEST['UOM4'])) {
		$uom4error = "UOM4 is required";
		array_push($error_array,$uom4error);
	}
	if (empty($_REQUEST['UOM5'])) {
		$uom5error = "UOM5 is required";
		array_push($error_array,$uom5error);
	}
	if(filter_var($_REQUEST['cl_stk'], FILTER_VALIDATE_FLOAT) === false ) {
		$clstkerror = "Cl stk have to integer or decimal value";
		array_push($error_array,$clstkerror);
	}
	if (empty($_REQUEST['gross_weight'])) {
		$grossweighterror = "Gross weight is required";
		array_push($error_array,$grossweighterror);
	}
	else if(filter_var($_REQUEST['gross_weight'], FILTER_VALIDATE_FLOAT) === false ) {
		$grossweighterror = "Gross weight have to integer or decimal value";
		array_push($error_array,$grossweighterror);
	}
	if (empty($_REQUEST['fg_rm'])) {
		$fgrmerror = "FG/RM is required";
		array_push($error_array,$fgrmerror);
	}
	if (empty($_REQUEST['hsn_sac'])) {
		$hsnsacerror = "HSN/SAC is required";
		array_push($error_array,$hsnsacerror);
	}
	else if(filter_var($_REQUEST['hsn_sac'], FILTER_VALIDATE_FLOAT) === false ) {
		$hsnsacerror = "HSN/SAC have to integer or decimal value";
		array_push($error_array,$hsnsacerror);
	}
	if(filter_var($_REQUEST['gst'], FILTER_VALIDATE_FLOAT) === false ) {
		$gsterror = "Gst have to integer or decimal value";
		array_push($error_array,$gsterror);
	}
	if (empty($_REQUEST['packing_realization'])) {
		$pakingrealizationerror = "Packing realization is required";
		array_push($error_array,$pakingrealizationerror);
	}
	else if(filter_var($_REQUEST['packing_realization'], FILTER_VALIDATE_FLOAT) === false ) {
		$pakingrealizationerror = "Packing realization have to integer or decimal value";
		array_push($error_array,$pakingrealizationerror);
	}
	if (empty($_REQUEST['prod_type'])) {
		$prodtypeerror = "Prod type is required";
		array_push($error_array,$prodtypeerror);
	}
	if($_REQUEST['prod_type']=='parent')
	{
		$oil_formulation_array=$_POST[oils];
		$cntoilsval=0;
		foreach($oil_formulation_array as $oil_val)
		{
			$oil_val_post=str_replace(" ","_",$oil_val);
			if($_POST['oils_'.$oil_val_post]==$oil_val)
			{
				$cntoilsval++;
			}
		}
		if($cntoilsval==0)
		{
			$oilformulationerror="Please choose at least one oil.";
			array_push($error_array,$oilformulationerror);
		}
	}
	if($_REQUEST['prod_type']=='child')
	{
	if (empty($_REQUEST['parent_product'])) {
		$parentproducterror = "Parent product is required";
		array_push($error_array,$parentproducterror);
	}
	}
	if (empty($_REQUEST['is_flash'])) {
		$isflasherror = "Is flash is required";
		array_push($error_array,$isflasherror);
	}
	if (empty($_REQUEST['flash_name'])) {
		$flashnameerror = "Flash name is required";
		array_push($error_array,$flashnameerror);
	}
	if(filter_var($_REQUEST['add_subtract'], FILTER_VALIDATE_FLOAT) === false ) {
		$addsubtracterror = "Add subtract have to integer or decimal value";
		array_push($error_array,$addsubtracterror);
	}
	if(count($error_array)==0)
	{
		change_mapping();
	}
	$dns_prod_code=$_REQUEST['dns_prod_code'];
	$prod_desc=$_REQUEST['prod_desc'];
	$vertical=$_REQUEST['vertical'];
	$product_group_code=$_REQUEST['product_group_code'];
	$product_sub_group_code=$_REQUEST['product_sub_group_code'];
	$UOM1=$_REQUEST['UOM1'];
	$UOM2=$_REQUEST['UOM2'];
	$conversion1=$_REQUEST['conversion1'];
	$pack_size=$_REQUEST['pack_size'];
	$UOM3=$_REQUEST['UOM3'];
	$conversion2=$_REQUEST['conversion2'];
	$UOM4=$_REQUEST['UOM4'];
	$UOM5=$_REQUEST['UOM5'];
	$cl_stk=$_REQUEST['cl_stk'];
	$hsn_sac=$_REQUEST['hsn_sac'];
	$vat=$_REQUEST['gst'];
	$gross_weight=$_REQUEST['gross_weight'];
	$fg_rm=$_REQUEST['fg_rm'];
	$packing_realization=$_REQUEST['packing_realization'];
	$prod_type= $_REQUEST['prod_type'];;
	$parent_product= $_REQUEST['parent_product'];
	$is_flash= $_REQUEST['is_flash'];
	$flash_name= $_REQUEST['flash_name'];
	$add_subtract= $_REQUEST['add_subtract'];
   }
   else{
	
	$sqlproduct="SELECT PM.*,PGM.product_group_name,PSGM.product_sub_group_name,PGM.product_group_code,PSGM.product_sub_group_code
					FROM product_master PM INNER JOIN product_group_master PGM ON PM.product_group_code=PGM.product_group_code
			 		INNER JOIN product_sub_group_master PSGM ON PM.product_sub_group_code=PSGM.product_sub_group_code 
					WHERE PM.prod_code = '".$prod_code."' ";	
	$rsproduct=mysqli_query($link,$sqlproduct) or die(mysqli_error()." Error in show customer: ".$sqlproduct);
	$rowproduct=mysqli_fetch_assoc($rsproduct);
	$dns_prod_code=$rowproduct['dns_prod_code'];
	$prod_desc=$rowproduct['prod_desc'];
	$vertical=$rowproduct['vertical_value'];
	$product_group_code=$rowproduct['product_group_code'];
	$product_group_name=$rowproduct['product_group_name'];
	$product_sub_group_code=$rowproduct['product_sub_group_code'];
	$product_sub_group_name=$rowproduct['product_sub_group_name'];
	$UOM1=$rowproduct['UOM1'];
	$UOM2=$rowproduct['UOM2'];
	$conversion1=$rowproduct['conversion_factor'];
	$pack_size=$rowproduct['pack_size'];
	$UOM3=$rowproduct['UOM3'];
	$conversion2=$rowproduct['conversion_factor_two'];
	$UOM4=$rowproduct['UOM4'];
	$UOM5=$rowproduct['UOM5'];
	$cl_stk=$rowproduct['cl_stk'];
	$hsn_sac=$rowproduct['hsn_sac'];
	$vat=$rowproduct['vat'];
	$gross_weight=$rowproduct['gross_weight'];
	$fg_rm=$rowproduct['fg_rm'];
	$packing_realization=$rowproduct['packing_realization'];
	
	$sqlprodtype="SELECT mapped_prod_code FROM product_unit_coversion_matrix 
					WHERE mapped_prod_code='".$dns_prod_code."'  ORDER BY download_time DESC LIMIT 0,1";
	$rsprodtype=mysqli_query($link,$sqlprodtype);
	$countprodtype=mysqli_num_rows($rsprodtype);
	if($countprodtype > 0)
	{
		$sqlparentproduct="SELECT is_flash,flash_name,add_subtract_val FROM product_unit_coversion_matrix 
	 					WHERE prod_code='".$dns_prod_code."' ORDER BY download_time DESC LIMIT 0,1";
		$rsparentproduct=mysqli_query($link,$sqlparentproduct);
		$rowparentproduct=mysqli_fetch_assoc($rsparentproduct);
		$prod_type= 'parent';
		$is_flash= $rowparentproduct['is_flash'];
		$flash_name= $rowparentproduct['flash_name'];
		$add_subtract= $rowparentproduct['add_subtract_val'];
	}
	else
	{
	 $prod_type= 'child';
	 $sqlchildproduct="SELECT mapped_prod_code,is_flash,flash_name,add_subtract_val FROM product_unit_coversion_matrix 
	 					WHERE prod_code='".$dns_prod_code."' ORDER BY download_time DESC LIMIT 0,1";
	 $rschildproduct=mysqli_query($link,$sqlchildproduct);
	 $rowchildproduct=mysqli_fetch_assoc($rschildproduct);
	 $parent_product= $rowchildproduct['mapped_prod_code'];
	 $is_flash= $rowchildproduct['is_flash'];
	 $flash_name= $rowchildproduct['flash_name'];
	 $add_subtract= $rowchildproduct['add_subtract_val'];
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
	function populate_formulation(oils)
	{
	  if(document.getElementById("oils_"+oils).checked==true)
	  {
		document.getElementById("formulation_"+oils).style.display ='';
		document.getElementById("percentile_calc_"+oils).style.display ='';
		document.getElementById("process_cost_"+oils).style.display ='';
	  }
	  else
	  {
		 document.getElementById("formulation_"+oils).style.display ='none';
		document.getElementById("percentile_calc_"+oils).style.display ='none';
		document.getElementById("process_cost_"+oils).style.display ='none';
	  }
	}
	function populate_formultion_all(prodtype)
	{
	  if(document.getElementById("prod_type").value=='parent')
	  {
		document.getElementById("oil_formulation_tr").style.display ='';
		document.getElementById("formulation_heading").style.display ='';
		document.getElementById("parent_tr").style.display ='none';
	  }
	  else
	  {
		 document.getElementById("oil_formulation_tr").style.display ='none';
		 document.getElementById("parent_tr").style.display ='';
		 document.getElementById("formulation_heading").style.display ='none';
		 
		 if(document.getElementById("product_group_code").value.search(/\S/) == -1)
			return false;
		 if(document.getElementById("product_sub_group_code").value.search(/\S/) == -1)
			return false;	
		var product_group_code = document.getElementById("product_group_code").value;
		var product_sub_group_code = document.getElementById("product_sub_group_code").value;
		document.getElementById("parent_prod_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?product_group_code='+product_group_code+'&product_sub_group_code='+product_sub_group_code+'&mode=mappedproduct','parent_prod_div',0);
	  }
	}
	function sel_product_group(vertical){
		document.getElementById("product_group_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?vertical='+vertical+'&mode=prodgroup','product_group_sel_div',0);
		
	}
	function sel_prod_sub_group(prod_group_code){
		document.getElementById("sub_group_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?product_group_code='+prod_group_code+'&mode=prodsubgroup','sub_group_sel_div',0);
		
	}
	function sel_uom1(packsize){
		document.getElementById("uom1_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?packsize='+packsize+'&mode=produom1','uom1_sel_div',0);
		
	}
	function sel_uom2(packsize){
		document.getElementById("uom2_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?packsize='+packsize+'&mode=produom2','uom2_sel_div',0);
		
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
</script>
<style>
.error{
color:red;
font-weight:bold;
}
</style>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Edit Product</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminProductListing.php" >
			<input type="hidden" name="mod" value="change_mapping">
            <input type="hidden" name="mode" value="access">			
            <input type="hidden" name="row_id" value="<?=$row_id?>" >
			<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
			<table width="70%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Edit Information of "<?=$prod_desc?>"</td>
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
				?>
 				<tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Product Code<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="dns_prod_code" id="dns_prod_code" class="inplogin" style="width:100px;height:15px;" value="<?php echo $dns_prod_code;?>" readonly/>&nbsp;<span class="error"><?php echo $prodcodeerror;?></span></td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Product Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="prod_desc" id="prod_desc" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $prod_desc;?>"/>&nbsp;<span class="error"><?php echo $prodnameerror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">FG/RM<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="fg_rm" id="fg_rm" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryfgrm="SELECT DISTINCT fg_rm FROM product_master ORDER BY fg_rm ASC";
                                $resultqueryfgrm = mysqli_query($link,$sqlqueryfgrm);
                                $countqueryfgrm=mysqli_num_rows($resultqueryfgrm);
                                if($countqueryfgrm>0){
                                while($rowqueryfgrm = mysqli_fetch_assoc($resultqueryfgrm))
                                {
                                ?>
                                <option value="<?php echo $rowqueryfgrm['fg_rm'];?>" <?php if( $fg_rm==$rowqueryfgrm['fg_rm'])
								{echo 'selected';}?>><?php echo $rowqueryfgrm['fg_rm'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $fgrmerror;?></span></td>
				</tr>
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">HSN/SAC<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="hsn_sac" id="hsn_sac" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $hsn_sac;?>"/>&nbsp;<span class="error"><?php echo $hsnsacerror;?></span></td>
				</tr>
                 <tr >
					<td align="left" valign="top" class="tbllogin">VERTICLE (DEVISION)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="vertical" id="vertical" onChange="javascript:sel_product_group(this.value)">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT vertical_value FROM product_master ORDER BY vertical_value ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['vertical_value'];?>" <?php if( $vertical==$rowqueryproductgroup['vertical_value'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['vertical_value'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $verticalerror;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">Product Group<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="product_group_code" id="product_group_code" onChange="javascript:sel_prod_sub_group(this.value);">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT product_group_code,product_group_name FROM product_group_master where 
												vertical_value='".$vertical."' ORDER BY product_group_name ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['product_group_code'];?>" <?php if( $product_group_code==$rowqueryproductgroup['product_group_code'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['product_group_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $prodgrouperror;?></span>
                    </td>
				</tr>
                <tr>
					<td align="left" valign="top" class="tbllogin">Product Sub Group<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <div id="sub_group_sel_div"><select name="product_sub_group_code" id="product_sub_group_code" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductsubgroup="SELECT product_sub_group_code,product_sub_group_name FROM product_sub_group_master 
												WHERE product_group_code='".$product_group_code."' ORDER BY product_sub_group_name ASC";
                                $resultqueryproductsubgroup = mysqli_query($link,$sqlqueryproductsubgroup);
                                $countqueryproductsubgroup=mysqli_num_rows($resultqueryproductsubgroup);
                                if($countqueryproductsubgroup>0){
                                while($rowqueryproductsubgroup = mysqli_fetch_assoc($resultqueryproductsubgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductsubgroup['product_sub_group_code'];?>" <?php if( $product_sub_group_code==$rowqueryproductsubgroup['product_sub_group_code'])
								{echo 'selected';}?>><?php echo $rowqueryproductsubgroup['product_sub_group_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $prodsubgrouperror;?></span></div>
                    </td>
				</tr> 
                <tr >
					<td align="left" valign="top" class="tbllogin">Pack Size<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="pack_size" id="pack_size" onChange="javascript:sel_uom1(this.value);sel_uom2(this.value);">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT pack_size FROM product_master ORDER BY UOM2 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['pack_size'];?>" <?php if( $pack_size==$rowqueryproductgroup['pack_size'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['pack_size'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $packsizeerror;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM 1 (PACKING IN)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="UOM1" id="UOM1" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM1 FROM product_master WHERE pack_size='".$pack_size."' ORDER BY UOM1 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM1'];?>" <?php if( $UOM1==$rowqueryproductgroup['UOM1'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM1'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $uom1error;?></span>
                    </td>
				</tr>
                 <tr >
					<td align="left" valign="top" class="tbllogin">UOM 2 (WEIGHT PARAMETER LTR/KG)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="UOM2" id="UOM2" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM2 FROM product_master WHERE pack_size='".$pack_size."' ORDER BY UOM2 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM2'];?>" <?php if( $UOM2==$rowqueryproductgroup['UOM2'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM2'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $uom2error;?></span>
                    </td>
				</tr>
               <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">CONVERSION 1 (NO OF PC'S PER SKU CASE)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="conversion1" id="conversion1" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $conversion1;?>"/>&nbsp;<span class="error"><?php echo $conversion1error;?></span></td>
				</tr>
                
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM 3 (WEIGHT PARAMETER IN MT)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="UOM3" id="UOM3" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM3 FROM product_master ORDER BY UOM3 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM3'];?>" <?php if( $UOM3==$rowqueryproductgroup['UOM3'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM3'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $uom3error;?></span>
                    </td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">CONVERSION 2 (OIL WEIGHT PER CASE IN MT)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="conversion2" id="conversion2" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $conversion2;?>"/>&nbsp;<span class="error"><?php echo $conversion2error;?></span></td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM 4 (NO OF PC'S PER CASE)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="UOM4" id="UOM4" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM4 FROM product_master ORDER BY UOM4 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM4'];?>" <?php if( $UOM4==$rowqueryproductgroup['UOM4'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM4'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $uom4error;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM5<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="UOM5" id="UOM5" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM5 FROM product_master ORDER BY UOM5 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM5'];?>" <?php if( $UOM5==$rowqueryproductgroup['UOM5'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM5'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $uom5error;?></span>
                    </td>
				</tr>
                  <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Cl stk<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="cl_stk" id="cl_stk" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $cl_stk;?>"/>&nbsp;<span class="error"><?php echo $clstkerror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Gross weight<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="gross_weight" id="gross_weight" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $gross_weight;?>"/>&nbsp;<span class="error"><?php echo $grossweighterror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">GST(%)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="gst" id="gst" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $vat;?>"/>&nbsp;<span class="error"><?php echo $gsterror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Packing Realization<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="packing_realization" id="packing_realization" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $packing_realization;?>"/>&nbsp;<span class="error"><?php echo $pakingrealizationerror;?></span></td>
				</tr>
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Product Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    	<select name="prod_type" id="prod_type" onChange="javascript:populate_formultion_all(this.value);">
                            <option value="">SELECT</option>
                             <option value="parent" <?php if($prod_type=='parent') { echo 'selected';}?> >Parent</option>
                             <option value="child" <?php if($prod_type=='child') { echo 'selected';}?>>Child</option>
                        </select>
                      &nbsp;<span class="error"><?php echo $prodtypeerror;?></span></td>
				</tr>
                <tr style="display:<?php if($prod_type=='parent'){echo "";}else{?>none<?php }?>" id="formulation_heading"><td colspan="3" align="center" ><b><u>Oil Formulation & Process Cost</u></b></td></tr>
                <tr id="oil_formulation_tr" style="display:<?php if($prod_type=='parent'){echo "";}else{?>none<?php }?>">
                <td align="left" width="20%"  valign="top" class="tbllogin">Oil Formulation<font color="#FF0000"><strong>*</strong></font>&nbsp;&nbsp;</td>
                  <td width="3%" align="left" valign="top" class="tbllogin">:</td>
                  <td align="left" valign="top"><div style="max-height:200px; overflow-y: scroll;">
                    <table cellpadding="5" cellspacing="2">
                        <!--tr>
                            <td align="left">
                                 <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="error"><?php //echo $customerproductgrouperror;?></span>
                            </td>
                         </tr--> 
                          <tr>
                                <td align="left"><b>Oils</b></td>
                                <td align="left">
                                    <b>Formulation(%)</b>
                                </td>
                                <td align="left">
                                   <b>Percentile Calc</b>
                                </td>
                                <td align="left">
                                  <b>Process cost</b>
                                </td>
                             </tr>
                             <tr><td colspan="4" align="center"><span class="error"><?php echo $oilformulationerror;?></span></td></tr>
    
                        <?php 
						$mod = $_REQUEST['mod'];
						if($mod == 'change_mapping')
						{
							$sqloils="SELECT DISTINCT oils FROM loose_oilrate_formulation ORDER BY oils ASC ";
							$rsoils=mysqli_query($link,$sqloils);
							while($rowoils=mysqli_fetch_assoc($rsoils))
							{
								//print_r($_POST['product_group_code']);
								$oil_val=str_replace(" ","_",$rowoils['oils']);
								?>
								<tr>
									<td align="left">
										<input type="checkbox" name="oils_<?php echo $rowoils['oils'];?>" id="oils_<?php echo $rowoils['oils'];?>" value="<?php echo $rowoils['oils'];?>" onChange="javascript:populate_formulation(this.value);" 
										<?php if($_POST['oils_'.$oil_val]==$rowoils['oils']){?>checked<?php }?>/><?php echo $rowoils['oils'];?>
										<input type="hidden" name="oils[]" value="<?php echo $rowoils['oils'];?>" />
									</td>
									<td align="left">
									   <input type="text" name="formulation_<?php echo $rowoils['oils'];?>" id="formulation_<?php echo $rowoils['oils'];?>" 
									   value="<?php echo $_POST['formulation_'.$oil_val];?>" 
										style="width:70px;height:15px;display:<?php if($prod_type=='parent' 
										&& $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
									</td>
									<td align="left">
									   <select name="percentile_calc_<?php echo $rowoils['oils'];?>" id="percentile_calc_<?php echo $rowoils['oils'];?>"  style="display:<?php if($prod_type=='parent' && $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>" >
											<option value="">SELECT</option>
											 <option value="Y" <?php if($_POST['percentile_calc_'.$oil_val]=='Y'){ echo 'selected';}?> >Y</option>
											 <option value="N" <?php if($_POST['percentile_calc_'.$oil_val]=='N'){ echo 'selected';}?>>N</option>
										</select>
									</td>
									<td align="left">
									 <input type="text" name="process_cost_<?php echo $rowoils['oils'];?>" id="process_cost_<?php echo $rowoils['oils'];?>" value="<?php echo $_POST['process_cost_'.$oil_val];?>" 
									 style="width:70px;height:15px;display:<?php if($prod_type=='parent' && $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
									</td>
								 </tr>   
							<?php
							}
						}
						else
						{
						$sqloils="SELECT DISTINCT oils FROM loose_oilrate_formulation ORDER BY oils ASC";
                        $rsoils=mysqli_query($link,$sqloils);
                        while($rowoils=mysqli_fetch_assoc($rsoils))
                        {
							$sqlprodoils="SELECT DISTINCT oils,formulation,percentile_calc from loose_oilrate_formulation WHERE 
										prod_code='".$dns_prod_code."' AND oils='".$rowoils['oils']."' ";
							$rsprodoils=mysqli_query($link,$sqlprodoils);
							$countprodoils=mysqli_num_rows($rsprodoils);
							if($countprodoils >0)
							{
								$rowprodoils=mysqli_fetch_assoc($rsprodoils);
								if($rowprodoils['percentile_calc']=='N')
								{
								$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$rowprodoils['oils']."' ";
								$rsprocesscost=mysqli_query($link,$sqlprocesscost);
								$rowprocesscost=mysqli_fetch_assoc($rsprocesscost);
								$process_cost=$rowprocesscost['process_cost'];
								}
								else
								{
									$process_cost='';
								}
							}
						?>	
                                <td align="left">
                                    <input type="checkbox" name="oils_<?php echo $rowoils['oils'];?>" id="oils_<?php echo $rowoils['oils'];?>" value="<?php echo $rowoils['oils'];?>" onChange="javascript:populate_formulation(this.value);" 
									<?php if($rowprodoils['oils']==$rowoils['oils']){?>checked<?php }?>/><?php echo $rowoils['oils'];?>
                                    <input type="hidden" name="oils[]" value="<?php echo $rowoils['oils'];?>" />
                                </td>
                                <td align="left">
                                   <input type="text" name="formulation_<?php echo $rowoils['oils'];?>" id="formulation_<?php echo $rowoils['oils'];?>" 
                                   value="<?php echo $rowprodoils['formulation'];?>" 
                                    style="width:70px;height:15px;display:<?php if($prod_type=='parent' 
									&& $rowprodoils['oils']==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
                                </td>
                                <td align="left">
                                   <select name="percentile_calc_<?php echo $rowoils['oils'];?>" id="percentile_calc_<?php echo $rowoils['oils'];?>"  style="display:<?php if($prod_type=='parent' && $rowprodoils['oils']==$rowoils['oils']){echo "";}else{?>none<?php }?>" >
                                        <option value="">SELECT</option>
                                         <option value="Y" <?php if($rowprodoils['percentile_calc']=='Y'){ echo 'selected';}?> >Y</option>
                                         <option value="N" <?php if($rowprodoils['percentile_calc']=='N'){ echo 'selected';}?>>N</option>
                                    </select>
                                </td>
                                <td align="left">
                                 <input type="text" name="process_cost_<?php echo $rowoils['oils'];?>" id="process_cost_<?php echo $rowoils['oils'];?>" value="<?php echo $process_cost;?>" 
                                 style="width:70px;height:15px;display:<?php if($prod_type=='parent' &&  $rowprodoils['oils']==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
                                </td>
                             </tr>   
                        <?php
                    	}
					}
					?>
                    </table></div>
                </td>
              </tr>
              <tr><td colspan="3" align="center"><b><u>Conversion</u></b></td></tr> 
              <tr id="parent_tr" style="display:<?php if($prod_type=='child'){echo "";}else{?>none<?php }?>">
					<td align="left" valign="top" class="tbllogin">Parent Product<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top">
                    <div id="parent_prod_div"> 
                        <select name="parent_product" id="parent_product" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlquerymappedprod="SELECT dns_prod_code,prod_desc FROM product_master WHERE dns_prod_code 
													IN(SELECT DISTINCT mapped_prod_code FROM product_unit_coversion_matrix WHERE acedns='Y') 
													ORDER BY prod_desc ASC";
                                $resultquerymappedprod = mysqli_query($link,$sqlquerymappedprod);
                                $countquerymappedprod=mysqli_num_rows($resultquerymappedprod);
                                if($countquerymappedprod>0){
                                while($rowquerymappedprod = mysqli_fetch_assoc($resultquerymappedprod))
                                {
                                ?>
                                <option value="<?php echo $rowquerymappedprod['dns_prod_code'];?>" <?php if( $parent_product==$rowquerymappedprod['dns_prod_code'])
								{echo 'selected';}?>><?php echo $rowquerymappedprod['prod_desc'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $parentproducterror;?></span></div>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">Is Flash<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top">
                      <select name="is_flash" id="is_flash" >
                            <option value="">SELECT</option>
                             <option value="Y" <?php if($is_flash=='Y'){ echo 'selected';}?> >Y</option>
                             <option value="N" <?php if($is_flash=='N'){ echo 'selected';}?>>N</option>
                        </select>&nbsp;<span class="error"><?php echo $isflasherror;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">Flash name<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top">
                     <input type="text" name="flash_name" id="flash_name" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $flash_name;?>"/>&nbsp;<span class="error"><?php echo $flashnameerror;?></span>
                    </td>
				</tr>
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Add Or Subtract Value<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="add_subtract" id="add_subtract" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $add_subtract;?>"/>&nbsp;<span class="error"><?php echo $addsubtracterror;?></span></td>
				</tr>				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="javascript:window.location='adminProductListing.php';" class="inplogin"></td>
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
	$prod_code=$_REQUEST['row_id'];
		/*echo '<pre>';
	print_r($_POST);
	echo '</pre>';*/
	//exit();*/
	$dns_prod_code=$_REQUEST['dns_prod_code'];
	$prod_desc=$_REQUEST['prod_desc'];
	$vertical=$_REQUEST['vertical'];
	$product_group_code=$_REQUEST['product_group_code'];
	$product_sub_group_code=$_REQUEST['product_sub_group_code'];
	$UOM1=$_REQUEST['UOM1'];
	$UOM2=$_REQUEST['UOM2'];
	$conversion1=$_REQUEST['conversion1'];
	$pack_size=$_REQUEST['pack_size'];
	$UOM3=$_REQUEST['UOM3'];
	$conversion2=$_REQUEST['conversion2'];
	$UOM4=$_REQUEST['UOM4'];
	$UOM5=$_REQUEST['UOM5'];
	$cl_stk=$_REQUEST['cl_stk'];
	$hsn_sac=$_REQUEST['hsn_sac'];
	$gst=$_REQUEST['gst'];
	$gross_weight=$_REQUEST['gross_weight'];
	$fg_rm=$_REQUEST['fg_rm'];
	$packing_realization=$_REQUEST['packing_realization'];
	$prod_type= $_REQUEST['prod_type'];
	$parent_product= $_REQUEST['parent_product'];
	$is_flash= $_REQUEST['is_flash'];
	$flash_name= $_REQUEST['flash_name'];
	$add_subtract= $_REQUEST['add_subtract'];
	
		$sql  = "update product_master ";
		$sql .= " SET branch_code=''";
		$sql .= " , state_code=''";
		$sql .= " , prod_desc='".addslashes($prod_desc)."'";
		$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
		$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
		$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";
		$sql .= " , vertical_value='".addslashes($vertical)."'";
		$sql .= " , UOM1='".$UOM1."'";
		$sql .= " , UOM2='".$UOM2."'";
		$sql .= " , pack_size='".$pack_size."'";
		$sql .= " , UOM3='".$UOM3."'";
		$sql .= " , conversion_factor_two='".$conversion2."'";
		$sql .= " , TD=''";
		$sql .= " , conversion_factor='".$conversion1."'";
		$sql .= " , vat='".$gst."'";
		$sql .= " , addl_vat=''";
		$sql .= " , UOM4='".addslashes($UOM4)."'";
		$sql .= " , fg_rm='".$fg_rm."'";
		$sql .= " , gross_weight='".$gross_weight."'";
		$sql .= " , oil_category=''";
		$sql .= " , alias=''";
		$sql .= " , hsn_sac='".addslashes($hsn_sac)."'";
		$sql .= " , packing_realization='".addslashes($packing_realization)."'";
		$sql .= " , UOM5='".addslashes($UOM5)."'";
		$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";
		$sql .= " ,	download_time=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code."'";
		mysqli_query($link,$sql);
		
	  if($_REQUEST['prod_type']=='parent')
		{
		$sqldnsprodcode="SELECT dns_prod_code FROM product_master WHERE prod_code='".$prod_code."'";
		$rsdnsprodcode=mysqli_query($link,$sqldnsprodcode);
		$rowdnsprodcode=mysqli_fetch_assoc($rsdnsprodcode);
		$dns_prod_code=$rowdnsprodcode['dns_prod_code'];
		
		$sqlupdateformulation="UPDATE loose_oilrate_formulation SET acedns='N' WHERE prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
		$rsupdateformulation=mysqli_query($link,$sqlupdateformulation);
		
		$oil_formulation_array=$_POST[oils];
		//print_r($oil_formulation_array);
		$cntoilsval=0;
			foreach($oil_formulation_array as $oil_val)
			{
				$oil_val_post=str_replace(" ","_",$oil_val);
				//echo $_POST['oils_'.$oil_val_post];
				if($_POST['oils_'.$oil_val_post]==$oil_val)
				{
					$formulation=$_POST['formulation_'.$oil_val_post];
					$percentile_calc=$_POST['percentile_calc_'.$oil_val_post];
					$sqloilformulation  = "insert into loose_oilrate_formulation SET ";
					$sqloilformulation .= "  plant_name='Bhiwadi'";
					$sqloilformulation .= " , prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
					$sqloilformulation .= " , oils='".mysqli_real_escape_string($oil_val)."'";
					$sqloilformulation .= " , formulation='".mysqli_real_escape_string($formulation)."'";
					$sqloilformulation .= " , base_oil=''";
					$sqloilformulation .= " , acedns='Y'";
					$sqloilformulation .= " , percentile_calc='".mysqli_real_escape_string($percentile_calc)."'";

					$sqloilformulation .= " , user_id='".$_SESSION['admin_login']."'";
					$sqloilformulation .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
					$sqloilformulation .= " , datetime=CURRENT_TIMESTAMP";
					mysqli_query($link,$sqloilformulation) or die(mysqli_error().".Internal error occur	in oilrate formulation.Please check.");
					
					if($percentile_calc=='N')
					{
						$process_cost=$_POST['process_cost_'.$oil_val_post];
						$sqlprocess  = "insert into process_cost SET ";
						$sqlprocess .= "  oil_type='".mysqli_real_escape_string($oil_val)."'";
						$sqlprocess .= " , oil_category=''";
						$sqlprocess .= " , process_cost='".mysqli_real_escape_string($process_cost)."'";
						$sqlprocess .= " , plant_name='Bhiwadi'";
						$sqlprocess .= " , user_id='".$_SESSION['admin_login']."'";
						$sqlprocess .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
						$sqlprocess .= " , datetime=CURRENT_TIMESTAMP";
						mysqli_query($link,$sqlprocess) or die(mysqli_error().".Internal error occurrs in Process cost.Please check.");
					}
				}
			}
			$sqlupdateconversion="UPDATE product_unit_coversion_matrix SET acedns='N' WHERE prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
			$rsupdateconversion=mysqli_query($link,$sqlupdateconversion);
			$sqlconversion  = "insert into product_unit_coversion_matrix SET ";
			$sqlconversion .= "  	prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
			$sqlconversion .= " , mapped_prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
			$sqlconversion .= " , is_flash='".mysqli_real_escape_string($is_flash)."'";
			$sqlconversion .= " , flash_name='".mysqli_real_escape_string($flash_name)."'";
			$sqlconversion .= " , add_subtract_val='".mysqli_real_escape_string($add_subtract)."'";
			$sqlconversion .= " , user_id='".$_SESSION['admin_login']."'";
			$sqlconversion .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
			$sqlconversion .= " , download_time=CURRENT_TIMESTAMP";
			mysqli_query($link,$sqlconversion) or die(mysqli_error().".Internal error occurrs in conversion.Please check.");
		}
		else
		{
			$sqlupdateconversion="UPDATE product_unit_coversion_matrix SET acedns='N' WHERE prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
			$rsupdateconversion=mysqli_query($link,$sqlupdateconversion);
			$sqlconversion  = "insert into product_unit_coversion_matrix SET ";
			$sqlconversion .= "  	prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
			$sqlconversion .= " , mapped_prod_code='".mysqli_real_escape_string($parent_product)."'";
			$sqlconversion .= " , is_flash='".mysqli_real_escape_string($is_flash)."'";
			$sqlconversion .= " , flash_name='".mysqli_real_escape_string($flash_name)."'";
			$sqlconversion .= " , add_subtract_val='".mysqli_real_escape_string($add_subtract)."'";
			$sqlconversion .= " , user_id='".$_SESSION['admin_login']."'";
			$sqlconversion .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
			$sqlconversion .= " , download_time=CURRENT_TIMESTAMP";
			mysqli_query($link,$sqlconversion) or die(mysqli_error().".Internal error occurrs in conversion.Please check.");

		}
	$GLOBALS['err_msg']="Product Information edited successfully.";
	header("location:adminProductListing.php?mod=succ");
		//exit();
	disphtml("main();");
}
?>