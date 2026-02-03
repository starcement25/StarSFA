<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	//if($mode == 'add')						   add_record();
	if($GLOBALS['mode']!='pricingadd')
	{
		disphtml("main();");
	}
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
	$margincosterror='';
	$mode = $_REQUEST['mode'];
	$error_array=array();

	if($mode == 'add')
	{
		/*$sqlcustomerfreightchk="SELECT * FROM branch_route_freight WHERE branch_code='".addslashes($_REQUEST['branch_name'])."' AND 
								route_code=".$_REQUEST['route']." AND transport_mode='".addslashes($_REQUEST['transport_mode'])."' AND 
								capacity='".addslashes($_REQUEST['loadability_ton'])."' and acedns='Y'";
		$rscustomerfreightchk=mysqli_query($link,$sqlcustomerfreightchk);
		$countcustomerfreightchk=mysqli_num_rows($rscustomerfreightchk);
		if($countcustomerfreightchk< 1)
		{
			$customerfreighterror="Secondary Freight does not exist for this Route.";
			array_push($error_array,$customerfreighterror);
		}*/
		
	if (empty($_REQUEST['dns_prod_code'])) {
		$prodcodeerror = "Product code is required";
		array_push($error_array,$prodcodeerror);
	}
	else
	{
		$sqlprodcodechk="SELECT dns_prod_code FROM product_master WHERE dns_prod_code='".addslashes($_REQUEST['dns_prod_code'])."'";
		$rsprodcodechk=mysqli_query($link,$sqlprodcodechk);
		$countprodcodechk=mysqli_num_rows($rsprodcodechk);
		if($countprodcodechk > 0)
		{
			$prodcodeerror="Product code already exists.";
			array_push($error_array,$prodcodeerror);
		}
	}
	if (empty($_REQUEST['prod_desc']) || ctype_space($_REQUEST['prod_desc'])) {
		$prodnameerror = "Product name is required";
		array_push($error_array,$prodnameerror);
	}
	if (empty($_REQUEST['vertical'])) {
		$verticalerror = "Vertcal is required";
		array_push($error_array,$verticalerror);
	}
	//print_r($_REQUEST['product_group_code']);
	$product_group_code_array=array();
	$product_group_code_array=$_REQUEST['product_group_code'];
	//echo count($product_group_code_array);
	if (count($product_group_code_array)==0) {
		$prodgrouperror = "Product group is required";
		array_push($error_array,$prodgrouperror);
	}
	if(in_array('new',$product_group_code_array))
	{
		if (empty($_REQUEST['product_group_name']) || ctype_space($_REQUEST['product_group_name'])) {
		$prodgroupnameerror = "Product group name is required";
		array_push($error_array,$prodgroupnameerror);
		}
	}
	
	//$product_sub_group_code_array=array();
	//print_r($_POST['product_sub_group_code']);
	$product_sub_group_code_array=$_REQUEST['product_sub_group_code'];
	//echo count($product_sub_group_code_array);
	if (count($product_sub_group_code_array)==0 && !in_array('new',$product_group_code_array)) {
		$prodsubgrouperror = "Product sub group is required";
		array_push($error_array,$prodsubgrouperror);
	}
	if(in_array('new',$product_sub_group_code_array) || in_array('new',$product_group_code_array))
	{
		if (empty($_REQUEST['product_sub_group_name']) || ctype_space($_REQUEST['product_sub_group_name'])) {
		$prodsubgroupnameerror = "Product sub group name is required";
		array_push($error_array,$prodsubgroupnameerror);
		}
	}
	if (empty($_REQUEST['UOM1'])) {
		$uom1error = "UOM1 is required";
		array_push($error_array,$uom1error);
	}
	if($_REQUEST['UOM1']=='new')
	{
	  if(empty($_REQUEST['uom1_new']) || ctype_space($_REQUEST['uom1_new']) ) {
			$uom1newerror = "UOM1 new is required";
			array_push($error_array,$uom1newerror);
		}
	}
	if (empty($_REQUEST['UOM2'])) {
		$uom2error = "UOM2 is required";
		array_push($error_array,$uom2error);
	}
	if($_REQUEST['UOM2']=='new')
	{
	  if(empty($_REQUEST['uom2_new']) || ctype_space($_REQUEST['uom2_new']) ) {
			$uom2newerror = "UOM2 new is required";
			array_push($error_array,$uom2newerror);
		}
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
	if (filter_var($_REQUEST['UOM4'], FILTER_VALIDATE_FLOAT) === false) {
		$uom4error = "UOM4 is required";
		array_push($error_array,$uom4error);
	}
	if (filter_var($_REQUEST['UOM5'], FILTER_VALIDATE_FLOAT) === false) {
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
	/*if (empty($_REQUEST['prod_type'])) {
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
	}*/
	if(filter_var($_REQUEST['margin_cost'], FILTER_VALIDATE_FLOAT) === false ) {
		$margincosterror = "Margin cost have to integer or decimal value";
		array_push($error_array,$margincosterror);
	}
	if(count($error_array)==0)
	{
		add_record();
	}
   }
?>
<style>
.error{
color:red;
font-weight:bold;
}
</style>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Product Addition</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminSKUadd.php" >
			<input type="hidden" name="mode" value="add">			
			<table width="70%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Add Product</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandatory.</td>
				</tr>
				<?php /*if($GLOBALS['err_msg']!=""){?>
				<!--tr>
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr-->
				<?php }*/
				if($_REQUEST['mod']=="succ"){
					?>
				<tr>
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000">Product information has been added successfully.</font></strong></td>
				</tr>
				<?php }
				?>
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Product Code<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="dns_prod_code" id="dns_prod_code" class="inplogin" style="width:100px;height:15px;" value="<?php echo $_REQUEST['dns_prod_code'];?>"/>&nbsp;<span class="error"><?php echo $prodcodeerror;?></span></td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Product Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="prod_desc" id="prod_desc" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $_REQUEST['prod_desc'];?>"/>&nbsp;<span class="error"><?php echo $prodnameerror;?></span></td>
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
                                <option value="<?php echo $rowqueryfgrm['fg_rm'];?>" <?php if( $_REQUEST['fg_rm']==$rowqueryfgrm['fg_rm'])
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
                    value="<?php echo $_REQUEST['hsn_sac'];?>"/>&nbsp;<span class="error"><?php echo $hsnsacerror;?></span></td>
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
                                <option value="<?php echo $rowqueryproductgroup['vertical_value'];?>" <?php if( $_REQUEST['vertical']==$rowqueryproductgroup['vertical_value'])
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
                    <div style="max-height:200px; overflow-y: scroll;display:
					<?php if($_REQUEST['product_group_code']!='' || $_REQUEST['vertical']!=''){?>''<?php }else{?>none<?php }?>" id="product_group_sel_div">
                  <?php $product_group_code=$_REQUEST['product_group_code'];?>
                    <table > 
                    	
                        <!--select name="product_group_code" id="product_group_code" onChange="javascript:sel_prod_sub_group(this.value);">
                            <option value="">SELECT</option>
								<?php 
                                /*$sqlqueryproductgroup="SELECT product_group_code,product_group_name FROM product_group_master where 
													vertical_value='".$_REQUEST['vertical']."' ORDER BY product_group_name ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
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
													vertical_value='".$_REQUEST['vertical']."' ORDER BY product_group_name ASC";
                        $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                        while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                        {
							//print_r($_POST['product_group_code']);
							$sqlitem="SELECT GROUP_CONCAT(prod_desc SEPARATOR ',  ') AS prod_desc FROM product_master  
										WHERE product_group_code='".$rowproductgroup['product_group_code']."'";
							$rsitem=mysqli_query($link,$sqlitem);
							$rowitem=mysqli_fetch_assoc($rsitem);
							$item_string='';
							if($rowitem['prod_desc']!=''){
								$item_string='['.strtoupper($rowitem['prod_desc']).']';
							}
							?>
                            <tr>
                                <td align="left">
                                    <input type="radio" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_code'];?>" onChange="javascript:sel_prod_sub_group(this.value);populate_productgroup_new(this.value);"
									<?php if(in_array($rowproductgroup['product_group_code'],$_POST['product_group_code'])){?>checked<?php }?>/>
									<?php echo '<b>'.$rowproductgroup['product_group_name'].'</b><br />'.$item_string;?>
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                     	 <tr>
                            <td align="left">
                                 <input type="radio" name="product_group_code[]" id="new_checked" value="new" onchange="javascript:populate_productgroup_new(this.value);" <?php if(in_array('new',$_POST['product_group_code'])){?>checked<?php }?>/><b>NEW</b>
                            </td>
                        </tr>  
                    </table></div><div><table><tr>
                                <td align="left">
                                   <span class="error"><?php echo $prodgrouperror;?></span>
                                </td>
                            </tr></table></div>    
                    </td>
				</tr>
                <tr id="prod_group_name_tr" style="display:<?php if($_REQUEST['product_group_name']!='' || in_array('new',$_POST['product_group_code'])){?>''<?php }else{?>none<?php }?>">
                  <td width="30%" align="left" valign="top" class="tbllogin"></td>
                            <td width="3%" align="left" valign="top" class="tbllogin"></td>
                 <td align="left" valign="top"><b>Input New Product Group Name</b><br /><br /><input type="text" name="product_group_name" id="product_group_name" class="inplogin" style="width:300px;height:30px;" 	
                            value="<?php echo $_REQUEST['product_group_name'];?>"/>&nbsp;<span class="error"><?php echo $prodgroupnameerror;?></span></td>
                 </tr>  
                <tr>
					<td align="left" valign="top" class="tbllogin">Product Sub Group<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <div style="max-height:200px; overflow-y: scroll;
                        display:<?php if($_REQUEST['product_sub_group_code']!='' || $_REQUEST['product_group_code']!=''){?>''<?php }else{?>none<?php }?>" id="sub_group_sel_div">
                         <table> 
                            
                        <!--select name="product_sub_group_code" id="product_sub_group_code" >
                            <option value="">SELECT</option>
								<?php 
                                /*$sqlqueryproductsubgroup="SELECT product_sub_group_code,product_sub_group_name 
															FROM product_sub_group_master where 
															product_group_code='".$_REQUEST['product_group_code']."' ORDER BY product_sub_group_name ASC";
                                $resultqueryproductsubgroup = mysqli_query($link,$sqlqueryproductsubgroup);
                                $countqueryproductsubgroup=mysqli_num_rows($resultqueryproductsubgroup);
                                if($countqueryproductsubgroup>0){
                                while($rowqueryproductsubgroup = mysqli_fetch_assoc($resultqueryproductsubgroup))
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
						$product_group_code_arr=$_REQUEST['product_group_code'];
						foreach($product_group_code_arr as $product_group_code_val)
						{
							$product_group_code_val=$product_group_code_val;
						}
                       $sqlqueryproductsubgroup="SELECT product_sub_group_code,product_sub_group_name 
															FROM product_sub_group_master where 
															product_group_code='".$product_group_code_val."' ORDER BY product_sub_group_name ASC";
                       $resultqueryproductsubgroup = mysqli_query($link,$sqlqueryproductsubgroup);
						$countqueryproductsubgroup=mysqli_num_rows($resultqueryproductsubgroup);
						while($rowqueryproductsubgroup = mysqli_fetch_assoc($resultqueryproductsubgroup))
						{
							//print_r($_POST['product_group_code']);
							$sqlitem="SELECT GROUP_CONCAT(prod_desc SEPARATOR ',  ') AS prod_desc FROM product_master  
										WHERE product_sub_group_code='".$rowqueryproductsubgroup['product_sub_group_code']."'";
							$rsitem=mysqli_query($link,$sqlitem);
							$rowitem=mysqli_fetch_assoc($rsitem);
							$item_string='';
							if($rowitem['prod_desc']!=''){
								$item_string='['.strtoupper($rowitem['prod_desc']).']';
							}
							?>
                            <tr>
                                <td align="left">
                                    <input type="radio" name="product_sub_group_code[]" value="<?php echo $rowqueryproductsubgroup['product_sub_group_code'];?>" 
									<?php if(in_array($rowqueryproductsubgroup['product_sub_group_code'],$_POST['product_sub_group_code'])){?>checked<?php }?>/>
									<?php echo '<b>'.$rowqueryproductsubgroup['product_sub_group_name'].'</b><br />'.$item_string;?>
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                     	 <tr>
                            <td align="left">
                                 <input type="radio" name="product_sub_group_code[]" id="new_checked_sub" value="new" 
                                 onchange="javascript:populate_productsubgroup_new(this.value);" <?php if(in_array('new',$_POST['product_sub_group_code']) || in_array('new',$_POST['product_group_code'])){?>checked<?php }?>/><b>NEW</b>
                            </td>
                        </tr></table>
                        </div>
                        <div><table><tr>
                                <td align="left">
                                   <span class="error"><?php echo $prodsubgrouperror;?></span>
                                </td>
                            </tr></table></div>
                    </td>
				</tr>
                 <tr id="prod_sub_group_name_tr" style="display:<?php if($_REQUEST['product_sub_group_name']!='' 
				 || in_array('new',$_POST['product_sub_group_code']) || in_array('new',$_POST['product_group_code'])){?>''<?php }else{?>none<?php }?>">
                  <td width="30%" align="left" valign="top" class="tbllogin"></td>
                            <td width="3%" align="left" valign="top" class="tbllogin"></td>
                 <td align="left" valign="top"><b>Input New Product Sub Group Name</b><br /><br /><input type="text" name="product_sub_group_name" id="product_sub_group_name" class="inplogin" style="width:300px;height:30px;" 	
                            value="<?php echo $_REQUEST['product_sub_group_name'];?>"/>&nbsp;<span class="error"><?php echo $prodsubgroupnameerror;?></span></td>
                 </tr>   
                 <tr>
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
                                <option value="<?php echo $rowqueryproductgroup['pack_size'];?>" <?php if( $_REQUEST['pack_size']==$rowqueryproductgroup['pack_size'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['pack_size'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $packsizeerror;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM 1 (LOOSE/CASE)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <div id="uom1_sel_div"><select name="UOM1" id="UOM1" onchange="javascript:populate_default_value();">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM1 FROM product_master WHERE pack_size='".$_REQUEST['pack_size']."' ORDER BY UOM1 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM1'];?>" <?php if( $_REQUEST['UOM1']==$rowqueryproductgroup['UOM1'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM1'];?></option>
                                <?php
                                }
                            }
                            ?>	
                            	<!--option value="new" <?php /*if( $_REQUEST['UOM1']=='new'){echo 'selected';}*/?> >New</option-->
                        </select>&nbsp;<span class="error"><?php echo $uom1error;?></span></div>
                    </td>
				</tr>
                 <tr id="uom1_tr" style="display:<?php if($_REQUEST['uom1_new']!=''){?>''<?php }else{?>none<?php }?>">
                  <td width="30%" align="left" valign="top" class="tbllogin"></td>
                   <td width="3%" align="left" valign="top" class="tbllogin"></td>
                 <td align="left" valign="top"><b>New UOM1</b><br /><br /><input type="text" name="uom1_new" id="uom1_new" class="inplogin" style="width:50px;height:15px;" 	
                      value="<?php echo $_REQUEST['uom1_new'];?>"/>&nbsp;<span class="error"><?php echo $uom1newerror;?></span></td>
                 </tr>   
                 <tr >
					<td align="left" valign="top" class="tbllogin">UOM 2 (KG/LTR/MT)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <div id="uom2_sel_div"><select name="UOM2" id="UOM2" onchange="javascript:populate_uom2_new(this.value);">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM2 FROM product_master WHERE pack_size='".$_REQUEST['pack_size']."' ORDER BY UOM2 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM2'];?>" <?php if( $_REQUEST['UOM2']==$rowqueryproductgroup['UOM2'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM2'];?></option>
                                <?php
                                }
                            }
                            ?>
                        <!--option value="new" <?php /*if( $_REQUEST['UOM2']=='new'){echo 'selected';}*/?> >New</option-->	
                        </select>&nbsp;<span class="error"><?php echo $uom2error;?></span></div>
                    </td>
				</tr>
                 <tr id="uom2_tr" style="display:<?php if($_REQUEST['uom2_new']!=''){?>''<?php }else{?>none<?php }?>">
                  <td width="30%" align="left" valign="top" class="tbllogin"></td>
                            <td width="3%" align="left" valign="top" class="tbllogin"></td>
                 <td align="left" valign="top"><b>New UOM2</b><br /><br /><input type="text" name="uom2_new" id="uom2_new" class="inplogin" style="width:50px;height:15px;"
                            value="<?php echo $_REQUEST['uom2_new'];?>"/>&nbsp;<span class="error"><?php echo $uom2newerror;?></span></td>
                 </tr>   
               <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">CONVERSION 1 (VOLUME PER CASE AS PER UOM2)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="conversion1" id="conversion1" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['conversion1'];?>"/>&nbsp;<span class="error"><?php echo $conversion1error;?></span></td>
				</tr>
               
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM 3 (WEIGHT PARAMETER IN MT)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="UOM3" id="UOM3" >
                            <!--option value="">SELECT</option-->
								<?php 
                                $sqlqueryproductgroup="SELECT DISTINCT UOM3 FROM product_master WHERE UOM3 <> '' ORDER BY UOM3 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM3'];?>" <?php if( $_REQUEST['UOM3']==$rowqueryproductgroup['UOM3'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM3'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $uom3error;?></span>
                    </td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">CONVERSION 2 (NER OILS WEIGHT PER CASE IN MT)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="conversion2" id="conversion2" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['conversion2'];?>"/>&nbsp;<span class="error"><?php echo $conversion2error;?></span></td>
				</tr>
                <tr>
					<td align="left" valign="top" class="tbllogin">UOM 4 (NO OF PC'S PER CASE)<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <!--select name="UOM4" id="UOM4" >
                            <option value="">SELECT</option>
								<?php 
                                /*$sqlqueryproductgroup="SELECT DISTINCT UOM4 FROM product_master ORDER BY UOM4 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM4'];?>" <?php if( $_REQUEST['UOM4']==$rowqueryproductgroup['UOM4'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM4'];?></option>
                                <?php
                                }
                            }*/
                            ?>	
                        </select--><input type="text" name="UOM4" id="UOM4" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['UOM4'];?>"/>&nbsp;<span class="error"><?php echo $uom4error;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">UOM5 (VOLUME PER PC IN A CASE) <font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <!--select name="UOM5" id="UOM5" >
                            <option value="">SELECT</option>
								<?php 
                                /*$sqlqueryproductgroup="SELECT DISTINCT UOM5 FROM product_master ORDER BY UOM5 ASC";
                                $resultqueryproductgroup = mysqli_query($link,$sqlqueryproductgroup);
                                $countqueryproductgroup=mysqli_num_rows($resultqueryproductgroup);
                                if($countqueryproductgroup>0){
                                while($rowqueryproductgroup = mysqli_fetch_assoc($resultqueryproductgroup))
                                {
                                ?>
                                <option value="<?php echo $rowqueryproductgroup['UOM5'];?>" <?php if( $_REQUEST['UOM5']==$rowqueryproductgroup['UOM5'])
								{echo 'selected';}?>><?php echo $rowqueryproductgroup['UOM5'];?></option>
                                <?php
                                }
                            }*/
                            ?>	
                        </select--><input type="text" name="UOM5" id="UOM5" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['UOM5'];?>"/>&nbsp;<span class="error"><?php echo $uom5error;?></span>
                    </td>
				</tr>
                  <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Cl stk<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="cl_stk" id="cl_stk" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['cl_stk'];?>"/>&nbsp;<span class="error"><?php echo $clstkerror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Gross weight<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="gross_weight" id="gross_weight" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['gross_weight'];?>"/>&nbsp;<span class="error"><?php echo $grossweighterror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">GST(%)<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="gst" id="gst" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['gst'];?>"/>&nbsp;<span class="error"><?php echo $gsterror;?></span></td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Packing Realization<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="packing_realization" id="packing_realization" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['packing_realization'];?>"/>&nbsp;<span class="error"><?php echo $pakingrealizationerror;?></span></td>
				</tr>
                <!--tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Product Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    	<select name="prod_type" id="prod_type" onChange="javascript:populate_formultion_all(this.value);">
                            <option value="">SELECT</option>
                             <option value="parent" <?php /*if($_REQUEST['prod_type']=='parent') { echo 'selected';}?> >Parent</option>
                             <option value="child" <?php if($_REQUEST['prod_type']=='child') { echo 'selected';}?>>Child</option>
                        </select>
                      &nbsp;<span class="error"><?php echo $prodtypeerror;?></span></td>
				</tr>
                <tr style="display:<?php if($_REQUEST['prod_type']=='parent'){echo "";}else{?>none<?php }?>" id="formulation_heading"><td colspan="3" align="center" ><b><u>Oil Formulation & Process Cost</u></b></td></tr>
                <tr id="oil_formulation_tr" style="display:<?php if($_REQUEST['prod_type']=='parent'){echo "";}else{?>none<?php }?>">
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
                          <!--tr>
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
                                    style="width:70px;height:15px;display:<?php if($_REQUEST['prod_type']=='parent' 
									&& $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
                                </td>
                                <td align="left">
                                   <select name="percentile_calc_<?php echo $rowoils['oils'];?>" id="percentile_calc_<?php echo $rowoils['oils'];?>"  style="display:<?php if($_REQUEST['prod_type']=='parent' && $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>" >
                                        <option value="">SELECT</option>
                                         <option value="Y" <?php if($_POST['percentile_calc_'.$oil_val]=='Y'){ echo 'selected';}?> >Y</option>
                                         <option value="N" <?php if($_POST['percentile_calc_'.$oil_val]=='N'){ echo 'selected';}?>>N</option>
                                    </select>
                                </td>
                                <td align="left">
                                 <input type="text" name="process_cost_<?php echo $rowoils['oils'];?>" id="process_cost_<?php echo $rowoils['oils'];?>" value="<?php echo $_POST['process_cost_'.$oil_val];?>" 
                                 style="width:70px;height:15px;display:<?php if($_REQUEST['prod_type']=='parent' && $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                    </table></div>
                </td>
              </tr>
              <tr><td colspan="3" align="center"><b><u>Conversion</u></b></td></tr> 
              <tr id="parent_tr" style="display:<?php if($_REQUEST['prod_type']=='child'){echo "";}else{?>none<?php }?>">
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
                                <option value="<?php echo $rowquerymappedprod['dns_prod_code'];?>" <?php if( $_REQUEST['parent_product']==$rowquerymappedprod['dns_prod_code'])
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
                             <option value="Y" <?php if($_POST['is_flash']=='Y'){ echo 'selected';}?> >Y</option>
                             <option value="N" <?php if($_POST['is_flash']=='N'){ echo 'selected';}?>>N</option>
                        </select>&nbsp;<span class="error"><?php echo $isflasherror;?></span>
                    </td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">Flash name<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top">
                     <input type="text" name="flash_name" id="flash_name" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $_REQUEST['flash_name'];?>"/>&nbsp;<span class="error"><?php echo $flashnameerror;?></span>
                    </td>
				</tr>
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Add Or Subtract Value<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="add_subtract" id="add_subtract" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['add_subtract'];?>"/>&nbsp;<span class="error"><?php echo $addsubtracterror;*/?></span></td>
				</tr-->
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Margin Cost<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="margin_cost" id="margin_cost" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['margin_cost'];?>"/>&nbsp;<span class="error"><?php echo $margincosterror;?></span></td>
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
	<script type="text/javascript" src="ajax1.js"></script>
	<script>
	function populate_default_value()
	{
		if(document.getElementById("UOM1").value=='Loose')
		{
			document.getElementById("conversion1").value=1;
			document.getElementById("conversion2").value=1;
			document.getElementById("UOM4").value=1;
			document.getElementById("UOM5").value=1;
		}
		else
		{
			document.getElementById("conversion1").value='';
			document.getElementById("conversion2").value='';
			document.getElementById("UOM4").value='';
			document.getElementById("UOM5").value='';
		}
	}
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
	function populate_productgroup_new(prod_group_code)
	{
		
		if(document.getElementById("new_checked").checked)
		{
			
			document.getElementById("prod_group_name_tr").style.display='';
			document.getElementById("prod_sub_group_name_tr").style.display='';
		}
		else
		{
			document.getElementById("prod_group_name_tr").style.display='none';
			document.getElementById("prod_sub_group_name_tr").style.display='none';
		}
	}
	function populate_productsubgroup_new(prod_sub_group_code)
	{
		
		if(document.getElementById("new_checked_sub").checked)
		{
			
			document.getElementById("prod_sub_group_name_tr").style.display='';
		}
		else
		{
			document.getElementById("prod_sub_group_name_tr").style.display='none';
		}
	}
	function populate_uom1_new(uom1)
	{
		if(document.getElementById("UOM1").value=='new')
		{
			document.getElementById("uom1_tr").style.display='';
		}
		else
		{
			document.getElementById("uom1_tr").style.display='none';
		}
	}
	
	function populate_uom2_new(uom2)
	{
		if(document.getElementById("UOM2").value=='new')
		{
			document.getElementById("uom2_tr").style.display='';
		}
		else
		{
			document.getElementById("uom2_tr").style.display='none';
		}
	}
	function sel_product_group(vertical){
		document.getElementById("product_group_sel_div").style.display='';
		document.getElementById("prod_group_name_tr").style.display='none';
		document.getElementById("sub_group_sel_div").style.display='none';
		document.getElementById("prod_sub_group_name_tr").style.display='none';
		document.getElementById("product_group_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?vertical='+vertical+'&mode=prodgroup','product_group_sel_div',0);
		
	}
	function sel_prod_sub_group(prod_group_code){
		document.getElementById("sub_group_sel_div").style.display='';
		document.getElementById("prod_sub_group_name_tr").style.display='none';
		//alert(prod_group_code);
		document.getElementById("sub_group_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?product_group_code='+prod_group_code+'&mode=prodsubgroup','sub_group_sel_div',0);
		
	}
	function sel_uom1(packsize){
		document.getElementById("uom1_tr").style.display='none';
		document.getElementById("uom1_new").value='';
		document.getElementById("uom1_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?packsize='+packsize+'&mode=produom1','uom1_sel_div',0);
		
	}
	function sel_uom2(packsize){
		document.getElementById("uom2_tr").style.display='none';
		document.getElementById("uom2_new").value='';
		document.getElementById("uom2_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?packsize='+packsize+'&mode=produom2','uom2_sel_div',0);
		
	}
</script>
	<?php
}//End of main()
function add_record()
{
	/*echo '<pre>';
	print_r($_POST);
	echo '</pre>';*/
	//exit();
	
	$dns_prod_code=$_REQUEST['dns_prod_code'];
	$prod_desc=$_REQUEST['prod_desc'];
	$vertical=$_REQUEST['vertical'];
	//$product_group_code=$_REQUEST['product_group_code'];
	//$product_sub_group_code=$_REQUEST['product_sub_group_code'];
	$UOM1=$_REQUEST['UOM1'];
	$UOM2=$_REQUEST['UOM2'];
	if($UOM1=='new'){
		$UOM1=$_REQUEST['uom1_new'];
	}
	if($UOM2=='new'){
		$UOM2=$_REQUEST['uom2_new'];
	}
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
	/*$prod_type= $_REQUEST['prod_type'];
	$parent_product= $_REQUEST['parent_product'];
	$is_flash= $_REQUEST['is_flash'];
	$flash_name= $_REQUEST['flash_name'];
	$add_subtract= $_REQUEST['add_subtract'];*/
	$margin_cost= $_REQUEST['margin_cost'];
	$product_group_code_array=$_POST['product_group_code'];
	foreach($product_group_code_array as  $product_group_code_val)
	{
		$product_group_code=$product_group_code_val;
	}
	$product_sub_group_code_array=$_POST['product_sub_group_code'];
	foreach($product_sub_group_code_array as  $product_sub_group_code_val)
	{
		$product_sub_group_code=$product_sub_group_code_val;
	}
	if(in_array('new',$product_group_code_array))
	{
	   $product_group_name = $_REQUEST['product_group_name'];
	   $sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master";
		$rsmaxproductgroupcode=mysqli_query($link,$sqlmaxproductgroupcode);
		$rowmaxproductgroupcode=mysqli_fetch_assoc($rsmaxproductgroupcode);
		$max_product_group_code=$rowmaxproductgroupcode['max_product_group_code'];
		
		if($max_product_group_code=='')
		{
			$max_product_group_code='1';
		}
		else
		{
			$max_product_group_code++;
		}
		$max_product_group_code='BR'.$max_product_group_code;
		$sqlbrand  = "INSERT INTO product_group_master SET ";
		$sqlbrand .= "  product_group_code='".$max_product_group_code."'";
		$sqlbrand .= " , product_group_name='".addslashes($product_group_name)."'";
		$sqlbrand .= " , vertical_value='".addslashes($vertical)."'";
		$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs product group master");
		$product_group_code=$max_product_group_code;
	}
	if(in_array('new',$product_sub_group_code_array) || in_array('new',$product_group_code_array))
	{
	   $product_sub_group_name = $_REQUEST['product_sub_group_name'];
	   $sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master";
		$rsmaxproductsubgroupcode=mysqli_query($link,$sqlmaxproductsubgroupcode);
		$rowmaxproductsubgroupcode=mysqli_fetch_assoc($rsmaxproductsubgroupcode);
		$max_product_sub_group_code=$rowmaxproductsubgroupcode['max_product_sub_group_code'];
		
		if($max_product_sub_group_code=='')
		{
			$max_product_sub_group_code='1';
		}
		else
		{
			$max_product_sub_group_code++;
		}
		$max_product_sub_group_code='BF'.$max_product_sub_group_code;
		$sqlbrandform  = "INSERT INTO product_sub_group_master SET ";
		$sqlbrandform .= "  product_sub_group_code='".mysqli_real_escape_string($max_product_sub_group_code)."'";
		$sqlbrandform .= " , product_sub_group_name='".addslashes($product_sub_group_name)."'";
		$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
		$sqlbrandform .= " , vertical_value='".addslashes($vertical)."'";
		$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sqlbrandform);
		$product_sub_group_code=$max_product_sub_group_code;
	}
	
		$sqlmaxskucode="SELECT MAX(prod_code) AS max_prod_code FROM  product_master WHERE 1";
		$rsmaxskucode=mysqli_query($link,$sqlmaxskucode);
		$rowmaxskucode=mysqli_fetch_assoc($rsmaxskucode);
		$max_prod_code=$rowmaxskucode['max_prod_code'];
		
		if($max_prod_code=='')
		{
			$max_prod_code='12001';
		}
		else
		{
			$max_prod_code++;
		}
		$sql  = "insert into product_master ";
		$sql .= " SET prod_code='".$max_prod_code."'";
		$sql .= " , dns_prod_code='".$dns_prod_code."'";
		$sql .= " , branch_code=''";
		$sql .= " , state_code=''";
		$sql .= " , prod_desc='".addslashes($prod_desc)."'";
		$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
		$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
		$sql .= " , product_brand_code=''";
		$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";
		$sql .= " , acedns='N'";
		$sql .= " , black_list='N'";
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
		$sql .= " ,	download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sql);
		
	  /*if($_REQUEST['prod_type']=='parent')
		{
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

		}*/
		$sqlinsertmargincost="INSERT INTO margin_cost 
							SET dns_prod_code='".$dns_prod_code."',
							margin_cost='".$margin_cost."',
							plant_name='Bhiwadi',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							datetime=CURRENT_TIMESTAMP";
		mysqli_query($link,$sqlinsertmargincost);
		//$GLOBALS['err_msg']="Customer information has been added successfully.";
		$GLOBALS['err_msg']='';
		header("location:adminSKUpricingadd.php?dns_prod_code=$dns_prod_code");
		//exit();
		//$GLOBALS['mode']='pricingadd';
		//add_pricing_data($dns_prod_code);
}
?>