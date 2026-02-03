<?php
ob_start();
session_start();
require("adminUtils.php");

$prod_group_code=$_REQUEST['prod_group_code'];
$mode=$_REQUEST['mode'];
$prodbranch=$_REQUEST['branch'];

if($prod_group_code !='') $prod_group_condition=" AND  PM.product_group_code IN(".$prod_group_code.")";
else					   $prod_group_condition="";	
if($prodbranch !='')	  $prod_branch_condition=" AND  PM.branch_code IN(".$prodbranch.")";
else					  $prod_branch_condition="";	
				
	

  if($mode=='productsel')
  {
   $sqlproddesc="SELECT PM.prod_code,PM.dns_prod_code,PM.prod_desc,PM.product_group_code,PM.focus FROM product_master PM 
   				WHERE PM.acedns='Y' AND PM.black_list='N' ".$prod_group_condition.$prod_branch_condition." ORDER BY PM.prod_desc ASC";
	$rsproddesc=mysql_query($sqlproddesc);
	$product_group_code_array=array();
	while($rowproddesc=mysql_fetch_array($rsproddesc))
	{
		 $product_group_code=$rowproddesc['product_group_code'];
		 $prod_code=$rowproddesc['prod_code'];
		 $focus=$rowproddesc['focus'];
		 $element_id="prod_id_".$prod_code;
		 if($focus=='Y') {	$checked='checked'; $color='#F00'; $onchange='onclick="defocus_product(this.value);"';}
		 else{			$checked='';			$color=''; $onchange="";}
		  $content.="<tr>";
          $content.="<td align='left'>";
          $content.="<input type='checkbox' name='prod_code[]' id='".$element_id."' value='".$rowproddesc['prod_code']."' ".$checked.' 
		  '.$onchange." /><font color=".$color.">".$rowproddesc['prod_desc']."</font>";
          $content.="</td>";
          $content.= "</tr>"; 
	}
	echo $content;
  }
  if($mode=='productselect')
  {
	  $prod_sub_group_code=$_REQUEST['prod_sub_group_code'];
	  $emp_code=$_REQUEST['emp_code'];
	  $sqlbranches="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
	  $rsbranches=mysql_query($sqlbranches);
	  $rowbranches=mysql_fetch_array($rsbranches);
	  $branch_code=$rowbranches['branch_code'];
   $sqlproddesc="SELECT PM.prod_code,PM.dns_prod_code,PM.prod_desc,PM.product_group_code,PM.focus,MRP.sale_rate,PM.pack_size FROM product_master PM,mrp MRP 
   				WHERE PM.prod_code=MRP.product_code AND PM.acedns='Y' AND PM.product_group_code='".$prod_group_code."' AND PM.product_sub_group_code=".$prod_sub_group_code." AND  MRP.branch_code='".$branch_code."' ORDER BY PM.prod_desc ASC";
	$rsproddesc=mysql_query($sqlproddesc);
	
	$countproddesc=mysql_num_rows($rsproddesc);
	$product_group_code_array=array();
	 $content="<table width=\"80%\" class=\"border\" cellpadding=\"5\" cellspacing=\"2\" align=\"left\" style=\"height: 200px;overflow-y: scroll;display:block;\"><tr class=\"TDHEAD_SUB\"><td align=\"left\" width=\"40%\"><b>SKU</b></td>
	 							<td align=\"left\" width=\"20%\">
                                   <b>Pack</b>
                                </td>
                                <td align=\"left\" width=\"20%\">
                                   <b>Qty</b>
                                </td>
                                <td align=\"left\" width=\"20%\">
                                  <b>Rate</b>
                                </td>
                             </tr>";
	if($countproddesc > 0){						 
	while($rowproddesc=mysql_fetch_array($rsproddesc))
	{
		 $product_group_code=$rowproddesc['product_group_code'];
		 $prod_code=$rowproddesc['prod_code'];
		 $focus=$rowproddesc['focus'];
		 $prod_desc=$rowproddesc['prod_desc'];
		 $pack_size=$rowproddesc['pack_size'];
		 $mrp=$rowproddesc['sale_rate'];
		
		  $content.="<tr>";
          $content.="<td align='left'>".$prod_desc."</td>";
		  $content.="<td align='left'>".$pack_size."</td>";
          $content.="<td align='left'><input type='text' name=\"qty_$prod_code\" id=\"qty_$prod_code\" value=\"\" 
		  onblur=\"javascript:populate_amount('".$prod_code."');\"   /></td>";
          $content.="<td align='left'>".number_format($mrp,2)."</td><input type='hidden' name=\"rate_$prod_code\" id=\"rate_$prod_code\" value=\"$mrp\"  />";
          $content.= "</tr>"; 
	}
	}
	else
	{
		 $content.="<tr>";
          $content.="<td align='center' colspan=\"3\">No Records.</td>";
          $content.= "</tr>"; 
	}
	$content.= "</table>";
	echo $content;
  }
  if($mode=='productsubgroupsel')
  {
	$onclick = "select_product('".$prod_group_code."',this.value);";  
   echo "<select name=\"prod_sub_group\" id=\"prod_sub_group\" onchange=\"".$onclick."\">";
   echo "<option value=\"\">Select</option>";
   $sqlproddesc="SELECT DISTINCT PSGM.product_sub_group_code,PSGM.product_sub_group_name FROM product_master PM,product_sub_group_master PSGM
   				WHERE PM.acedns='Y' AND PM.product_sub_group_code=PSGM.product_sub_group_code AND PM.product_group_code='".$prod_group_code."' 
				ORDER BY PSGM.product_sub_group_name ASC";
	$rsproddesc=mysql_query($sqlproddesc);
	$product_group_code_array=array();
	while($rowproddesc=mysql_fetch_array($rsproddesc))
	{
		 $product_sub_group_code=$rowproddesc['product_sub_group_code'];
		 $product_sub_group_name=$rowproddesc['product_sub_group_name'];
			echo "<option value=\"'".$product_sub_group_code."'\">".$product_sub_group_name."</option>";
			//$product_sub_group_string .= "'".$product_sub_group_code."',";
		$product_sub_group_string = rtrim($product_sub_group_string,",");
		//echo "<option value=\"".$prod_group_string."\">All</option>";
	}
		echo "</select>";
  }
  if($mode=='approvalemp')
  {
		$approval_authority=$_REQUEST['designation']; 
		$sqlqueryemp="SELECT emp_code,emp_name FROM employee_master where 
					designation='".$approval_authority."' AND acedns='Y' ORDER BY emp_name ASC";
	   $resultqueryemp = mysql_query($sqlqueryemp);
		$countqueryemp=mysql_num_rows($resultqueryemp);
		while($rowqueryqueryemp = mysql_fetch_array($resultqueryemp))
		{
		 $emp_code=$rowqueryqueryemp['emp_code'];
		 $emp_name=$rowqueryqueryemp['emp_name'];
		  $content.="<table><tr>";
          $content.="<td align='left'>";
          $content.="<input type='checkbox' name='emp_code[]'  value='".$emp_code."' ".$checked." />".$emp_name."";
          $content.="</td>";
          $content.= "</tr></table>"; 
	}
	echo $content;
  }
  if($mode=='productgroupsel')
  {
	if(no_of_filter > 2)  $onclick = "sel_product_subgroup(this.value);";
	else				  $onclick = "select_product();";	
	 $sql_product_group="SELECT PM.product_group_code,PGM.product_group_name FROM product_master PM,product_group_master PGM 
   				WHERE PM.product_group_code=PGM.product_group_code ".$prod_branch_condition." PM.acedns='Y' AND PM.black_list='N' 
				ORDER BY PGM.product_group_name ASC";
	$rsproddesc=mysql_query($sqlproddesc);
	$product_group_code_array=array();
	$res_product_group = mysql_query($sql_product_group);
	echo "<select name=\"prod_group\" id=\"prod_group\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";

	while($row_product_group = mysql_fetch_array($res_product_group)){
		echo "<option value=\"'".$row_product_group['product_group_code']."'\">".$row_product_group['product_group_name']."</option>";
		$prod_group_string .= "'".$row_product_group['product_group_code']."',";
	}
	$prod_group_string = rtrim($prod_group_string,",");
	echo "<option value=\"".$prod_group_string."\">All</option>";
	echo "</select>";
  }
 if($mode=='approvalsubcat')
  {
	  $approval_category=$_REQUEST['category'];
	 $sqlqueryapprovalsubcat="SELECT approval_sub_category FROM approval_sub_category where 
													approval_category='".$approval_category."' AND is_active='yes' ORDER BY approval_sub_category ASC";
     $resultqueryapprovalsubcat = mysql_query($sqlqueryapprovalsubcat);
	$countqueryapprovalsubcat=mysql_num_rows($resultqueryapprovalsubcat);
			//echo "<select name=\"prod_group\" id=\"prod_group\" onchange=\"".$onclick."\">";
	//echo "<option value=\"\">Select</option>";

	while($rowqueryapprovalsubcat = mysql_fetch_array($resultqueryapprovalsubcat))
	{
		/*$approval_sub_category=strtoupper(str_replace('_','',$rowqueryapprovalsubcat['approval_sub_category']));
		echo "<option value=\"'".$rowqueryapprovalsubcat['approval_sub_category']."'\">".$approval_sub_category."</option>";
		$prod_group_string .= "'".$rowqueryapprovalsubcat['product_group_code']."',";*/
		$approval_sub_category=$rowqueryapprovalsubcat['approval_sub_category'];
		 $content.="<table><tr>";
         $content.="<td align='left'>";
         $content.="<input type='checkbox' name='emp_code[]'  value='".$approval_sub_category."' ".$checked." />".strtoupper(str_replace('_','',$approval_sub_category))."";
         $content.="</td>";
         $content.= "</tr></table>"; 
	}
	//$prod_group_string = rtrim($prod_group_string,",");
	//echo "<option value=\"".$prod_group_string."\">All</option>";
	//echo "</select>";
	echo $content;
  }
  if($mode=='defocusing')
  {
	  $prod_code=$_REQUEST['prod_code'];
	  $sqlupdatefocus="UPDATE product_master SET focus='N',download_time=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code."'";
		if(mysql_query($sqlupdatefocus))
		{
				$sqldnsprodcode="SELECT dns_prod_code FROM product_master WHERE prod_code='".$prod_code."'";
				$rsdnsprodcode=mysql_query($sqldnsprodcode);
				$rowdnsprodcode=mysql_fetch_array($rsdnsprodcode);
				$dns_prod_code=$rowdnsprodcode['dns_prod_code'];
				
				$sqlinsertlog="INSERT INTO  focus_product_log SET prod_code='".$prod_code."',
																	dns_prod_code='".$dns_prod_code."',
																	operation_date=CURDATE(),
																	operated_by='".$_SESSION['admin_login']."',
																	is_focus='N'";
				mysql_query($sqlinsertlog);													
		}
		echo $prod_code;
  }
  //echo $mode;
  if($mode=='mappedproduct')
  {
	$product_group_code=$_REQUEST['product_group_code'];
	$product_sub_group_code=$_REQUEST['product_sub_group_code'];
	/*echo $sqlquerymappedprod="SELECT dns_prod_code,prod_desc FROM product_master WHERE dns_prod_code 
                            IN(SELECT DISTINCT mapped_prod_code FROM product_unit_coversion_matrix WHERE acedns='Y')  AND 
							product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."'
                            ORDER BY prod_desc ASC";*/
?>	 
    <select name="parent_product" id="parent_product" >
    <option value="">SELECT</option>
        <?php 
        $sqlquerymappedprod="SELECT dns_prod_code,prod_desc FROM product_master WHERE dns_prod_code 
                            IN(SELECT DISTINCT mapped_prod_code FROM product_unit_coversion_matrix WHERE acedns='Y')  
                            ORDER BY prod_desc ASC";
        $resultquerymappedprod = mysql_query($sqlquerymappedprod);
        $countquerymappedprod=mysql_num_rows($resultquerymappedprod);
        if($countquerymappedprod>0){
        while($rowquerymappedprod = mysql_fetch_array($resultquerymappedprod))
        {
        ?>
        <option value="<?php echo $rowquerymappedprod['dns_prod_code'];?>" <?php if( $_REQUEST['parent_product']==$rowquerymappedprod['dns_prod_code'])
        {echo 'selected';}?>><?php echo $rowquerymappedprod['prod_desc'];?></option>
        <?php
        }
    }
    ?>	
    </select>
 <?php   
  }
  if($mode=='prodsel')
  {
	
	$prod_group_code=$_REQUEST['prod_group_code'];
    $prod_select_control = "<select name=\"prod_code\" id=\"prod_code\" >";
    $prod_select_control .= "<option value=\"\">SELECT</option>";
        $sqlqueryprod="SELECT dns_prod_code,prod_desc,prod_code FROM product_master WHERE acedns='Y' AND product_group_code IN (".$prod_group_code.") 
		              AND prod_code IN(SELECT DISTINCT sku_code FROM DO_master) ORDER BY prod_desc ASC";
        $resultqueryprod = mysql_query($sqlqueryprod);
        $countqueryprod=mysql_num_rows($resultqueryprod);
        if($countqueryprod>0){
        while($rowqueryprod = mysql_fetch_array($resultqueryprod))
        {
			$prod_code = $rowqueryprod['prod_code'];
			$prod_code_string_val .= "'".$prod_code."',";
           $prod_select_control_options .= "<option value=\"'".$prod_code."'\">".$rowqueryprod['prod_desc']."</option>";
        }
		$prod_code_string_val = rtrim($prod_code_string_val,",");
		$prod_select_control .="<option value=\"".$prod_code_string_val."\">All</option>";
		$prod_select_control .=$prod_select_control_options;
	   echo $prod_select_control .= "</select>";
    }
  }
  if($mode=='prodseldo')
  {
	
	$prod_group_code=$_REQUEST['prod_group_code'];
    $prod_select_control = "<select name=\"prod_code\" id=\"prod_code\" >";
    $prod_select_control .= "<option value=\"\">SELECT</option>";
        $sqlqueryprod="SELECT dns_prod_code,prod_desc,prod_code FROM product_master WHERE acedns='Y' AND product_group_code IN (".$prod_group_code.") 
		              AND prod_code IN(SELECT DISTINCT sku_code FROM DO_transaction) ORDER BY prod_desc ASC";
        $resultqueryprod = mysql_query($sqlqueryprod);
        $countqueryprod=mysql_num_rows($resultqueryprod);
        if($countqueryprod>0){
        while($rowqueryprod = mysql_fetch_array($resultqueryprod))
        {
			$prod_code = $rowqueryprod['prod_code'];
			$prod_code_string_val .= "'".$prod_code."',";
           $prod_select_control_options .= "<option value=\"'".$prod_code."'\">".$rowqueryprod['prod_desc']."</option>";
        }
		$prod_code_string_val = rtrim($prod_code_string_val,",");
		$prod_select_control .="<option value=\"".$prod_code_string_val."\">All</option>";
		$prod_select_control .=$prod_select_control_options;
	   echo $prod_select_control .= "</select>";
    }
  }
  if($mode=='prodsubgroup')
  {
	$product_group_code=$_REQUEST['product_group_code'];
?>	 
    <!--select name="product_sub_group_code" id="product_sub_group_code" >
        <option value="">SELECT</option>
            <?php 
            /*$sqlqueryproductsubgroup="SELECT product_sub_group_code,product_sub_group_name FROM product_sub_group_master 
									WHERE product_group_code='".$product_group_code."' ORDER BY product_sub_group_name ASC";
            $resultqueryproductsubgroup = mysql_query($sqlqueryproductsubgroup);
            $countqueryproductsubgroup=mysql_num_rows($resultqueryproductsubgroup);
            if($countqueryproductsubgroup>0){
            while($rowqueryproductsubgroup = mysql_fetch_array($resultqueryproductsubgroup))
            {
            ?>
            <option value="<?php echo $rowqueryproductsubgroup['product_sub_group_code'];?>" <?php if( $_REQUEST['product_sub_group_code']==$rowqueryproductsubgroup['product_sub_group_code'])
            {echo 'selected';}?>><?php echo $rowqueryproductsubgroup['product_sub_group_name'];?></option>
            <?php
            }
        }*/
        ?>	
    </select-->
    <?php 
   $sqlqueryproductsubgroup="SELECT product_sub_group_code,product_sub_group_name 
										FROM product_sub_group_master where 
										product_group_code='".$product_group_code."' ORDER BY product_sub_group_name ASC";
   $resultqueryproductsubgroup = mysql_query($sqlqueryproductsubgroup);
	$countqueryproductsubgroup=mysql_num_rows($resultqueryproductsubgroup);
	if($countqueryproductsubgroup>0){
	while($rowqueryproductsubgroup = mysql_fetch_array($resultqueryproductsubgroup))
	{
		//print_r($_POST['product_group_code']);
		$sqlitem="SELECT GROUP_CONCAT(prod_desc SEPARATOR ',  ') AS prod_desc FROM product_master  
					WHERE product_sub_group_code='".$rowqueryproductsubgroup['product_sub_group_code']."'";
		$rsitem=mysql_query($sqlitem);
		$rowitem=mysql_fetch_array($rsitem);
		$item_string='';
		if($rowitem['prod_desc']!=''){
			$item_string='['.strtoupper($rowitem['prod_desc']).']'.'</b><br />';
		}
		?>
		<tr>
			<td align="left">
				<input type="radio" name="product_sub_group_code[]" value="<?php echo $rowqueryproductsubgroup['product_sub_group_code'];?>"  onchange="javascript:populate_productsubgroup_new(this.value);"
				<?php if(in_array($rowqueryproductsubgroup['product_sub_group_code'],$_POST['product_sub_group_code'])){?>checked<?php }?>/>
				<?php echo '<b>'.$rowqueryproductsubgroup['product_sub_group_name'].'</b><br />'.$item_string;?>
			</td>
		 </tr>   
	<?php
	}
	?>
    <tr>
        <td align="left">
             <input type="radio" name="product_sub_group_code[]" id="new_checked_sub" value="new" onchange="javascript:populate_productsubgroup_new(this.value);"/><b>NEW</b>
        </td>
    </tr>  
 <?php   
  }
 }
  if($mode=='prodgroup')
  {
	$vertical=$_REQUEST['vertical'];
?>	 
    <!--select name="product_group_code" id="product_group_code" onChange="javascript:sel_prod_sub_group(this.value);">
        <option value="">SELECT</option>
            <?php 
            /*$sqlqueryproductgroup="SELECT product_group_code,product_group_name FROM product_group_master where 
									vertical_value='".$vertical."' ORDER BY product_group_name ASC";
            $resultqueryproductgroup = mysql_query($sqlqueryproductgroup);
            $countqueryproductgroup=mysql_num_rows($resultqueryproductgroup);
            if($countqueryproductgroup>0){
            while($rowqueryproductgroup = mysql_fetch_array($resultqueryproductgroup))
            {
            ?>
            <option value="<?php echo $rowqueryproductgroup['product_group_code'];?>" <?php if( $_REQUEST['product_group_code']==$rowqueryproductgroup['product_group_code'])
            {echo 'selected';}?>><?php echo $rowqueryproductgroup['product_group_name'];?></option>
            <?php
            }
        }*/
        ?>	
    </select-->
    <?php
     $sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master where 
					vertical_value='".$vertical."' ORDER BY product_group_name ASC";
	$rsproductgroup=mysql_query($sqlproductgroup);
	while($rowproductgroup=mysql_fetch_array($rsproductgroup))
	{
		//print_r($_POST['product_group_code']);
		$sqlitem="SELECT GROUP_CONCAT(prod_desc SEPARATOR ',  ') AS prod_desc FROM product_master  
					WHERE product_group_code='".$rowproductgroup['product_group_code']."'";
		$rsitem=mysql_query($sqlitem);
		$rowitem=mysql_fetch_array($rsitem);
		$item_string='';
		if($rowitem['prod_desc']!=''){
			$item_string='['.strtoupper($rowitem['prod_desc']).']'.'</b><br />';
		}
		?>
		<tr>
			<td align="left">
				<input type="radio" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_code'];?>" onChange="javascript:sel_prod_sub_group(this.value);populate_productgroup_new(this.value);"
				<?php if(in_array($rowproductgroup['product_group_name'],$_POST['product_group_code'])){?>checked<?php }?>/>
				<?php echo '<b>'.$rowproductgroup['product_group_name'].'</b><br />'.$item_string;?>
			</td>
		 </tr>   
	<?php
    }
    ?>
    <tr>
        <td align="left">
             <input type="radio" name="product_group_code[]" id="new_checked" value="new" onchange="javascript:populate_productgroup_new(this.value);"/><b>NEW</b>
        </td>
    </tr>  
 <?php   
  }
  if($mode=='produom1')
  {
	$pack_size=$_REQUEST['packsize'];
?>	 
    <select name="UOM1" id="UOM1" onchange="javascript:populate_default_value();">
        <option value="">SELECT</option>
            <?php 
            $sqlqueryproductgroup="SELECT DISTINCT UOM1 FROM product_master WHERE pack_size='".$pack_size."' ORDER BY UOM1 ASC";
            $resultqueryproductgroup = mysql_query($sqlqueryproductgroup);
            $countqueryproductgroup=mysql_num_rows($resultqueryproductgroup);
            if($countqueryproductgroup>0){
            while($rowqueryproductgroup = mysql_fetch_array($resultqueryproductgroup))
            {
            ?>
            <option value="<?php echo $rowqueryproductgroup['UOM1'];?>" <?php if( $_REQUEST['UOM1']==$rowqueryproductgroup['UOM1'])
            {echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM1'];?></option>
            <?php
            }
        }
        ?>
        <!--option value="new" <?php /*if( $_REQUEST['UOM1']=='new'){echo 'selected';}*/?> 
                                >New</option-->
    </select>
 <?php   
  }
  if($mode=='produom2')
  {
	$pack_size=$_REQUEST['packsize'];
?>	 
    <select name="UOM2" id="UOM2" onchange="javascript:populate_uom2_new(this.value);">
        <option value="">SELECT</option>
            <?php 
            $sqlqueryproductgroup="SELECT DISTINCT UOM2 FROM product_master WHERE pack_size='".$pack_size."' ORDER BY UOM2 ASC";
            $resultqueryproductgroup = mysql_query($sqlqueryproductgroup);
            $countqueryproductgroup=mysql_num_rows($resultqueryproductgroup);
            if($countqueryproductgroup>0){
            while($rowqueryproductgroup = mysql_fetch_array($resultqueryproductgroup))
            {
            ?>
            <option value="<?php echo $rowqueryproductgroup['UOM2'];?>" <?php if( $_REQUEST['UOM2']==$rowqueryproductgroup['UOM2'])
            {echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM2'];?></option>
            <?php
            }
        }
        ?>
       <!--option value="new" <?php /*if( $_REQUEST['UOM2']=='new'){echo 'selected';}*/?> >New</option-->	
	
    </select>
 <?php   
  }
mysql_close($link);
exit();







$zone = $_REQUEST['prod_group_code'];
$type = $_REQUEST['type'];

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}

/*--------> Check If Branch Exists <--------*/
$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_condition.$branch_condition." ORDER BY branch_code ASC";
$res_branch = mysql_query($sql_branch);
$branch_total = mysql_num_rows($res_branch);
	
/*--------> Check If Sale Access Exists <--------*/
$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_condition.$sale_access_condition." ORDER BY sale_access ASC";
$res_sale_access = mysql_query($sql_sale_access);
$sale_access_total = mysql_num_rows($res_sale_access);

if(strtoupper($_SESSION['nick_name']) != 'STAR'){
/*--------> Check If Headquarter Exists <--------*/
$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_condition.$hq_condition." ORDER BY hq ASC";
$res_hq = mysql_query($sql_hq);
$hq_total = mysql_num_rows($res_hq);

/*--------> Check If Designation Exists <--------*/
$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_condition.$designation_condition." ORDER BY designation ASC";
$res_designation = mysql_query($sql_designation);
$designation_total = mysql_num_rows($res_designation);
}

/*--------> State Data Populate <--------*/
if($type == 'state'){
	if(strtoupper($_SESSION['nick_name']) == 'SKIPPER'){
		if($designation_total>0)
			$onclick = "state_designation(this.value);";
		else
			$onclick = "state_emp(this.value);";
	}
	else
	{
		if($branch_total>0)
			$onclick = "state_branch(this.value);";
		else if($hq_total>0)
			$onclick = "state_hq(this.value);";
		else if($designation_total>0)
			$onclick = "state_designation(this.value);";
		else
			$onclick = "state_emp(this.value);";
	}
	$select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	
	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master WHERE state != '' ".$zone_condition.$emp_hierarchy_condition_one." ORDER BY state ASC";
	$res_state = mysql_query($sql_state);
	while($row_state = mysql_fetch_array($res_state)){
		$state = $row_state['state'];
		$state_string .= "'".$state."',";
		$select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
	}
	$state_string = rtrim($state_string,",");
	$select_control .= "<option value=\"".$state_string."\">All</option>";
	$select_control .= $select_control_option;
	$select_control .= "</select>";
	echo $select_control;
}
/*--------> Branch Data Populate <--------*/
else if($type == 'branch'){
	if($sale_access_total>0)
		$onclick = "branch_saleaccess(this.value);";
	else if($hq_total>0)
		$onclick = "branch_hq(this.value);";
	else if($designation_total>0)
		$onclick = "branch_designation(this.value);";
	else
		$onclick = "branch_emp(this.value);";
	
	echo "<select name=\"branch\" id=\"branch\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master WHERE zone IN (".$zone.")".$emp_hierarchy_condition_one." AND branch_code != '' ORDER BY branch_code ASC";
	$res_branch = mysql_query($sql_branch);
	while($row_branch = mysql_fetch_array($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."'";
		$res_branch_name = mysql_query($sql_branch_name);
		$row_branch_name = mysql_fetch_array($res_branch_name);
		$branch_name = $row_branch_name['branch_name'];
		$branch_string .= "'".$branch_code."',";
		echo "<option value=\"'".$branch_code."'\">".$branch_name."</option>";
	}
	$branch_string = rtrim($branch_string,",");
	echo "<option value=\"".$branch_string."\">All</option>";
	echo "</select>";
}
/*--------> Sale Access Data Populate <--------*/
else if($type == 'sale_access'){
	if($hq_total>0)
		$onclick = "saleaccess_hq(this.value);";
	else if($designation_total>0)
		$onclick = "saleaccess_designation(this.value);";
	else
		$onclick = "saleaccess_emp(this.value);";
	
	echo "<select name=\"sale_access\" id=\"sale_access\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master WHERE zone IN (".$zone.")".$emp_hierarchy_condition_one." AND sale_access != '' ORDER BY sale_access ASC";
	$res_sale_access = mysql_query($sql_sale_access);
	while($row_sale_access = mysql_fetch_array($res_sale_access)){
		$sale_access = $row_sale_access['sale_access']; 
		$sale_access_string .= "'".$sale_access."',";
		echo "<option value=\"'".$sale_access."'\">".$sale_access."</option>";
	}
	$sale_access_string = rtrim($sale_access_string,",");
	echo "<option value=\"".$sale_access_string."\">All</option>";
	echo "</select>";
}

/*--------> Headquarter Data Populate <--------*/
else if($type == 'hq'){
	if($designation_total>0)
		$onclick = "hq_designation(this.value);";
	else
		$onclick = "hq_emp(this.value);";
				
	echo "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
		
	$sql_hq = "SELECT DISTINCT hq FROM employee_master WHERE zone IN (".$zone.")".$emp_hierarchy_condition_one." AND hq != '' ORDER BY hq ASC";
	$res_hq = mysql_query($sql_hq);
	while($row_hq = mysql_fetch_array($res_hq)){
		$hq = $row_hq['hq'];
		$hq_string .= "'".$hq."',";
		echo "<option value=\"'".$hq."'\">".$hq."</option>";
	}
	$hq_string = rtrim($hq_string,",");
	echo "<option value=\"".$hq_string."\">All</option>";
	echo "</select>";
}
/*--------> Designation Data Populate <--------*/
else if($type == 'designation'){
	$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE zone LIKE '%".$zone."%'".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
	$res_designation = mysql_query($sql_designation);
	while($row_designation = mysql_fetch_array($res_designation)){
		$designation = $row_designation['designation'];
		$designation_string .= "'".$designation."',";
		echo "<option value=\"'".$designation."'\">".$designation."</option>";
	}
	$designation_string = rtrim($designation_string,",");
	echo "<option value=\"".$designation_string."\">All</option>";
	echo "</select>";
}
/*--------> Employee Data Populate <--------*/
else if($type == 'emp'){
	//$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE zone LIKE '%".$zone."%'".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
mysql_close($link);
?>