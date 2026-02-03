<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	//if($mode == 'add')						   add_record();
	$dns_prod_code=$_REQUEST['dns_prod_code'];
		disphtml("add_pricing_data($dns_prod_code);");
ob_end_flush();
function add_pricing_data($dns_prod_code)
{

	$prodtypeerror='';
	$oilformulationerror='';
	$parentproducterror='';
	$addsubtracterror='';
	$isflasherror='';
	$flashnameerror='';
	$margincosterror='';
	$mode = $_REQUEST['mode'];
	$modval = $_REQUEST['modval'];
	$error_array=array();
		$sqlproddetails="SELECT product_group_code,product_sub_group_code FROM product_master WHERE dns_prod_code='".addslashes($dns_prod_code)."'";
		$rsproddetails=mysql_query($sqlproddetails);
		$rowproddetails=mysql_num_rows($rsproddetails);
		$product_group_code=$rowproddetails['product_group_code'];
		$product_sub_group_code=$rowproddetails['product_sub_group_code'];


	if($mode == 'addpricing' && $modval=='')
	{
	if (empty($_REQUEST['prod_type'])) {
		$prodtypeerror = "Prod type is required";
		array_push($error_array,$prodtypeerror);
	}
	if($_REQUEST['prod_type']=='parent')
	{
		$oil_formulation_array=$_POST[oils];
		$baseoil_formulation_array=$_POST[baseoils];
		$cntoilsval=0;
		$totalformulation=0;
		foreach($oil_formulation_array as $oil_val)
		{
			/*$oil_val_post=str_replace(" ","_",$oil_val);
			if($_POST['oils_'.$oil_val_post]==$oil_val)
			{
				if($_POST['formulation_'.$oil_val_post]!='')
				{
					$totalformulation=$totalformulation+$_POST['formulation_'.$oil_val_post];
				}
				
			}*/
			$oil_val_post=str_replace(" ","_",$oil_val);
			if($_POST['oils_'.$oil_val_post]==$oil_val)
			{
			$cntoilsval++;
			}
		}
		foreach($baseoil_formulation_array as $baseoil_val)
		{
			$baseoil_val_post=str_replace(" ","_",$baseoil_val);
			if($_POST['baseoils_'.$baseoil_val_post]==$baseoil_val)
			{
				if($_POST['baseformulation_'.$baseoil_val_post]!='')
				{
					$totalformulation=$totalformulation+$_POST['baseformulation_'.$baseoil_val_post];
				}
			}
		}
		if($_POST['baseformulation_new']!='')
		{
			$totalformulation=$totalformulation+$_POST['baseformulation_new'];
		}
		if($totalformulation!='100')
		{
			$baseoilformulationerror="Formulation should be 100.";
			array_push($error_array,$baseoilformulationerror);
		}
		if($cntoilsval==0 && $_POST['processoils_new']=='')
		{
			$oilformulationerror="Please choose at least one oil for process cost.";
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
	if($_REQUEST['is_flash']=='Y'){
		if (empty($_REQUEST['flash_name'])) {
			$flashnameerror = "Flash name is required";
			array_push($error_array,$flashnameerror);
		}
	}
	if(filter_var($_REQUEST['add_subtract'], FILTER_VALIDATE_FLOAT) === false ) {
		$addsubtracterror = "Add subtract have to integer or decimal value";
		array_push($error_array,$addsubtracterror);
	}
	if(count($error_array)==0)
	{
		insert_pricing_data();
	}
   }
   else
   {
	if($_REQUEST['prod_type']=='parent')
	{
		$oil_formulation_array=$_POST[oils];
		$baseoil_formulation_array=$_POST[baseoils];
		$cntoilsval=0;
		$totalformulation=0;
		foreach($oil_formulation_array as $oil_val)
		{
			/*$oil_val_post=str_replace(" ","_",$oil_val);
			if($_POST['oils_'.$oil_val_post]==$oil_val)
			{
				if($_POST['formulation_'.$oil_val_post]!='')
				{
					$totalformulation=$totalformulation+$_POST['formulation_'.$oil_val_post];
				}
				
			}*/
			if($_POST['oils_'.$oil_val_post]==$oil_val)
			{
			$cntoilsval++;
			}
		}
		foreach($baseoil_formulation_array as $baseoil_val)
		{
			$baseoil_val_post=str_replace(" ","_",$baseoil_val);
			if($_POST['baseoils_'.$baseoil_val_post]==$baseoil_val)
			{
				if($_POST['baseformulation_'.$baseoil_val_post]!='')
				{
					$totalformulation=$totalformulation+$_POST['baseformulation_'.$baseoil_val_post];
				}
			}
		}
		if($_POST['baseformulation_new']!='')
		{
			$totalformulation=$totalformulation+$_POST['baseformulation_new'];
		}
		if($totalformulation!='100')
		{
			$baseoilformulationerror="Formulation should be 100.";
			array_push($error_array,$baseoilformulationerror);
		}
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Product Pricing Data Addition</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminSKUpricingadd.php" >
			<input type="hidden" name="mode" value="addpricing">
            <input type="hidden" name="modval"  value="">
            <input type="hidden" name="product_group_code" id="product_group_code" value="<?php echo $product_group_code;?>">
            <input type="hidden" name="product_sub_group_code" id="product_sub_group_code" value="<?php echo $product_sub_group_code;?>">
            <input type="hidden" name="dns_prod_code" id="dns_prod_code" value="<?php echo $dns_prod_code;?>">			
			<table width="70%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Add Product Pricing Data</td>
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
					<td width="20%" align="left" valign="top" class="tbllogin">Product Code<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo $dns_prod_code;?></td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Product Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    	<select name="prod_type" id="prod_type" onChange="javascript:populate_baseformultion_all(this.value);">
                            <option value="">SELECT</option>
                             <option value="parent" <?php if($_REQUEST['prod_type']=='parent') { echo 'selected';}?> >Parent</option>
                             <option value="child" <?php if($_REQUEST['prod_type']=='child') { echo 'selected';}?>>Child</option>
                        </select>
                      &nbsp;<span class="error"><?php echo $prodtypeerror;?></span></td>
				</tr>
                 <tr style="display:<?php if($_REQUEST['prod_type']=='parent'){echo "";}else{?>none<?php }?>" id="base_formulation_heading"><td colspan="3" align="center" ><b><u>Base Oil & Formulation</u></b></td></tr>
                <tr id="base_oil_formulation_tr" style="display:<?php if($_REQUEST['prod_type']=='parent'){echo "";}else{?>none<?php }?>">
                <td align="left" width="20%"  valign="top" class="tbllogin">Base Oil Formulation<font color="#FF0000"><strong>*</strong></font>&nbsp;&nbsp;</td>
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
                                <td align="left"><b>Base Oils</b></td>
                                <td align="left">
                                    <b>Formulation(%)</b>
                                </td>
                             </tr>
                             <tr><td colspan="2" align="center"><span class="error"><?php echo $baseoilformulationerror;?></span></td></tr>
    
                        <?php 
                        $sqlbaseoils="SELECT DISTINCT base_oil FROM base_oil_master ORDER BY base_oil ASC";
                        $rsbaseoils=mysql_query($sqlbaseoils);
                        while($rowbaseoils=mysql_fetch_array($rsbaseoils))
                        {
							//print_r($_POST['product_group_code']);
							$base_oil_val=str_replace(" ","_",$rowbaseoils['base_oil']);
							?>
                            <tr>
                                <td align="left">
                                    <input type="checkbox" name="baseoils_<?php echo $rowbaseoils['base_oil'];?>" id="baseoils_<?php echo $rowbaseoils['base_oil'];?>" value="<?php echo $rowbaseoils['base_oil'];?>" onChange="javascript:populate_base_formulation(this.value);" 
									<?php if($_POST['baseoils_'.$base_oil_val]==$rowbaseoils['base_oil']){?>checked<?php }?>/><?php echo $rowbaseoils['base_oil'];?>
                                    <input type="hidden" name="baseoils[]" value="<?php echo $rowbaseoils['base_oil'];?>" />
                                </td>
                                <td align="left">
                                   <input type="text" name="baseformulation_<?php echo $rowbaseoils['base_oil'];?>" id="baseformulation_<?php echo $rowbaseoils['base_oil'];?>" 
                                   value="<?php echo $_POST['baseformulation_'.$base_oil_val];?>" 
                                    style="width:70px;height:15px;display:<?php if($_REQUEST['prod_type']=='parent' 
									&& $_POST['baseoils_'.$base_oil_val]==$rowbaseoils['base_oil']){echo "";}else{?>none<?php }?>"  />
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                     <tr>
                         <td align="left"><b>NEW:</b><input type="text" name="baseoils_new" id="baseoils_new" value="<?php echo $_POST['baseoils_new'];?>"  
                         style="width:100px;height:15px;"/></td>
                            <td align="left">
                               <input type="text" name="baseformulation_new" id="baseformulation_new" 
                               value="<?php echo $_POST['baseformulation_new'];?>" style="width:70px;height:15px;" />
                            </td>
                     </tr>       
                    <tr>
                        <td colspan="2" align="center"><input type="button" value=" OK " class="inplogin" onclick="javascript:populate_formultion_all();"></td>
                    </tr>
                    </table></div>
                </td>
              </tr>

                <tr style="display:<?php if($_REQUEST['prod_type']=='parent' || ($_REQUEST['modval']!=''&& $baseoilformulationerror=='')){echo "";}else{?>none<?php }?>" id="formulation_heading"><td colspan="3" align="center" ><b><u>Oil Formulation & Process Cost</u></b></td></tr>
                <tr id="oil_formulation_tr" style="display:<?php if($_REQUEST['prod_type']=='parent' || 
				($_REQUEST['modval']!=''&& $baseoilformulationerror=='')){echo "";}else{?>none<?php }?>">
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
                                <!--td align="left">
                                    <b>Formulation(%)</b>
                                </td-->
                                <td align="left">
                                   <b>Percentile Calc</b>
                                </td>
                                <td align="left">
                                  <b>Process cost</b>
                                </td>
                             </tr>
                             <tr><td colspan="4" align="center"><span class="error"><?php echo $oilformulationerror;?></span></td></tr>
    
                        <?php 
                        $sqloils="SELECT DISTINCT oils FROM loose_oilrate_formulation WHERE oils NOT IN(SELECT base_oil FROM base_oil_master) 
								ORDER BY oils ASC";
                        $rsoils=mysql_query($sqloils);
                        while($rowoils=mysql_fetch_array($rsoils))
                        {
							//print_r($_POST['product_group_code']);
							$oil_val=str_replace(" ","_",$rowoils['oils']);
							$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$rowoils['oils']."' ";
							$rsprocesscost=mysql_query($sqlprocesscost);
							$rowprocesscost=mysql_fetch_array($rsprocesscost);
							$process_cost=$rowprocesscost['process_cost'];
							?>
                            <tr>
                                <td align="left">
                                    <input type="checkbox" name="oils_<?php echo $rowoils['oils'];?>" id="oils_<?php echo $rowoils['oils'];?>" value="<?php echo $rowoils['oils'];?>" onChange="javascript:populate_formulation(this.value);" 
									<?php if($_POST['oils_'.$oil_val]==$rowoils['oils']){?>checked<?php }?>/><?php echo $rowoils['oils'];?>
                                    <input type="hidden" name="oils[]" value="<?php echo $rowoils['oils'];?>" />
                                </td>
                                <!--td align="left">
                                   <input type="text" name="formulation_<?php /*echo $rowoils['oils'];?>" id="formulation_<?php echo $rowoils['oils'];?>" 
                                   value="<?php echo $_POST['formulation_'.$oil_val];?>" 
                                    style="width:70px;height:15px;display:<?php if($_REQUEST['prod_type']=='parent' 
									&& $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }*/?>"  />
                                </td-->
                                <td align="left">
                                   <select name="percentile_calc_<?php echo $rowoils['oils'];?>" id="percentile_calc_<?php echo $rowoils['oils'];?>"  style="display:<?php if(($_REQUEST['prod_type']=='parent' || ($_REQUEST['modval']!=''&& $baseoilformulationerror=='')) && $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>" >
                                        <!--option value="">SELECT</option>
                                         <option value="Y" <?php //if($_POST['percentile_calc_'.$oil_val]=='Y'){ echo 'selected';}?> >Y</option-->
                                         <option value="N" <?php if($_POST['percentile_calc_'.$oil_val]=='N'){ echo 'selected';}?>>N</option>
                                    </select>
                                </td>
                                <td align="left">
                                 <input type="text" name="process_cost_<?php echo $rowoils['oils'];?>" id="process_cost_<?php echo $rowoils['oils'];?>" 
                                 value="<?php if($_POST['process_cost_'.$oil_val]!=''){echo $_POST['process_cost_'.$oil_val];}else { echo $process_cost;}?>" 
                                 style="width:70px;height:15px;display:<?php if(($_REQUEST['prod_type']=='parent' || ($_REQUEST['modval']!=''&& $baseoilformulationerror=='')) && $_POST['oils_'.$oil_val]==$rowoils['oils']){echo "";}else{?>none<?php }?>"  />
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                     <tr>
                         <td align="left"><b>NEW:</b><input type="text" name="processoils_new" id="processoils_new" value="<?php echo $_POST['processoils_new'];?>"  
                         style="width:100px;height:15px;"/></td>
                          <td align="left">
                                   <select name="percentile_calc_new" id="percentile_calc_new"  />
                                        <!--option value="">SELECT</option>
                                         <option value="Y" <?php //if($_POST['percentile_calc_'.$oil_val]=='Y'){ echo 'selected';}?> >Y</option-->
                                         <option value="N" <?php if($_POST['percentile_calc_new']=='N'){ echo 'selected';}?>>N</option>
                                    </select>
                                </td>
                            <td align="left">
                               <input type="text" name="process_cost_new" id="process_cost__new" 
                               value="<?php echo $_POST['process_cost_new'];?>" style="width:70px;height:15px;" />
                            </td>
                     </tr>
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
					<td width="20%" align="left" valign="top" class="tbllogin">Add Or Subtract Value<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="add_subtract" id="add_subtract" class="inplogin" style="width:50px;height:15px;" 
                    value="<?php echo $_REQUEST['add_subtract'];?>"/>&nbsp;<span class="error"><?php echo $addsubtracterror;?></span></td>
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
	function populate_formulation(oils)
	{
	  
	  if(document.getElementById("oils_"+oils).checked==true)
	  {
		//document.getElementById("formulation_"+oils).style.display ='';
		document.getElementById("percentile_calc_"+oils).style.display ='';
		document.getElementById("process_cost_"+oils).style.display ='';
	  }
	  else
	  {
		 //document.getElementById("formulation_"+oils).style.display ='none';
		document.getElementById("percentile_calc_"+oils).style.display ='none';
		document.getElementById("process_cost_"+oils).style.display ='none';
	  }
	}
	function populate_base_formulation(oils)
	{
	  //alert(oils);
	  if(document.getElementById("baseoils_"+oils).checked==true)
	  {
		document.getElementById("baseformulation_"+oils).style.display ='';
	  }
	  else
	  {
		 document.getElementById("baseformulation_"+oils).style.display ='none';
	  }
	}
	function populate_formultion_all()
	{
	  if(document.getElementById("prod_type").value=='parent')
	  {
		document.getElementById("oil_formulation_tr").style.display ='';
		document.getElementById("formulation_heading").style.display ='';
		document.getElementById("parent_tr").style.display ='none';
		document.frmadd.modval.value="chkbaseoil";
	  	document.frmadd.submit();
	  }
	  else
	  {
		 document.getElementById("oil_formulation_tr").style.display ='none';
		 document.getElementById("parent_tr").style.display ='';
		 document.getElementById("formulation_heading").style.display ='none';
		 
		 alert(document.getElementById("product_group_code").value);
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
	function populate_baseformultion_all(prodtype)
	{
	  if(document.getElementById("prod_type").value=='parent')
	  {
		document.getElementById("base_oil_formulation_tr").style.display ='';
		document.getElementById("base_formulation_heading").style.display ='';
		document.getElementById("parent_tr").style.display ='none';
	  }
	  else
	  {
		 document.getElementById("base_oil_formulation_tr").style.display ='none';
		 document.getElementById("oil_formulation_tr").style.display ='none';
		 document.getElementById("parent_tr").style.display ='';
		 document.getElementById("base_formulation_heading").style.display ='none';
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
</script>
	<?php
}
function insert_pricing_data()
{
	$prod_type= $_REQUEST['prod_type'];
	$parent_product= $_REQUEST['parent_product'];
	$is_flash= $_REQUEST['is_flash'];
	$flash_name= $_REQUEST['flash_name'];
	$add_subtract= $_REQUEST['add_subtract'];
	$dns_prod_code= $_REQUEST['dns_prod_code'];
		if($_REQUEST['prod_type']=='parent')
		{
			if($_POST['baseoils_new']!='')
			{
				$sqlinsertbaseoil="INSERT INTO base_oil_master SET base_oil='".$_POST['baseoils_new']."',mandatory='N',lower_limit='10000',
								upper_limit='99000',download_time=CURRENT_TIMESTAMP,user_id='".$_SESSION['admin_login']."',
								ip_address='".$_SERVER['REMOTE_ADDR']."'";
				if(mysql_query($sqlinsertbaseoil))
				{
					$sqloilformulationins  = "insert into loose_oilrate_formulation SET ";
					$sqloilformulationins .= "  plant_name='Bhiwadi'";
					$sqloilformulationins .= " , prod_code='".mysql_real_escape_string($dns_prod_code)."'";
					$sqloilformulationins .= " , oils='".mysql_real_escape_string($_POST['baseoils_new'])."'";
					$sqloilformulationins .= " , formulation='".mysql_real_escape_string($_POST['baseformulation_new'])."'";
					$sqloilformulationins .= " , base_oil=''";
					$sqloilformulationins .= " , acedns='Y'";
					$sqloilformulationins .= " , percentile_calc=''";
					$sqloilformulationins .= " , user_id='".$_SESSION['admin_login']."'";
					$sqloilformulationins .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
					$sqloilformulationins .= " , datetime=CURRENT_TIMESTAMP";
					mysql_query($sqloilformulationins) or die(mysql_error().".Internal error occur	in oilrate formulation base oil.Please check.");
					
				}
			}
		$sqlupdateformulation="UPDATE loose_oilrate_formulation SET acedns='N' WHERE prod_code='".mysql_real_escape_string($dns_prod_code)."'";
		$rsupdateformulation=mysql_query($sqlupdateformulation);
		
		$oil_formulation_array=$_POST[oils];
		$baseoil_formulation_array=$_POST[baseoils];
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
					$sqloilformulation .= " , prod_code='".mysql_real_escape_string($dns_prod_code)."'";
					$sqloilformulation .= " , oils='".mysql_real_escape_string($oil_val)."'";
					$sqloilformulation .= " , formulation='".mysql_real_escape_string($formulation)."'";
					$sqloilformulation .= " , base_oil=''";
					$sqloilformulation .= " , acedns='Y'";
					$sqloilformulation .= " , percentile_calc='".mysql_real_escape_string($percentile_calc)."'";
					$sqloilformulation .= " , user_id='".$_SESSION['admin_login']."'";
					$sqloilformulation .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
					$sqloilformulation .= " , datetime=CURRENT_TIMESTAMP";
					mysql_query($sqloilformulation) or die(mysql_error().".Internal error occur	in oilrate formulation.Please check.");
					
					if($percentile_calc=='N')
					{
						$process_cost=$_POST['process_cost_'.$oil_val_post];
						$sqlprocess  = "insert into process_cost SET ";
						$sqlprocess .= "  oil_type='".mysql_real_escape_string($oil_val)."'";
						$sqlprocess .= " , oil_category=''";
						$sqlprocess .= " , process_cost='".mysql_real_escape_string($process_cost)."'";
						$sqlprocess .= " , plant_name='Bhiwadi'";
						$sqlprocess .= " , user_id='".$_SESSION['admin_login']."'";
						$sqlprocess .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
						$sqlprocess .= " , datetime=CURRENT_TIMESTAMP";
						mysql_query($sqlprocess) or die(mysql_error().".Internal error occurrs in Process cost.Please check.");
					}
				}
			}
			foreach($baseoil_formulation_array as $baseoil_val)
			{
				$baseoil_val_post=str_replace(" ","_",$baseoil_val);
				if($_POST['baseoils_'.$baseoil_val_post]==$baseoil_val)
				{
					$baseformulation=$_POST['baseformulation_'.$baseoil_val_post];
						$sqlbaseoilformulation  = "insert into loose_oilrate_formulation SET ";
						$sqlbaseoilformulation .= "  plant_name='Bhiwadi'";
						$sqlbaseoilformulation .= " , prod_code='".mysql_real_escape_string($dns_prod_code)."'";
						$sqlbaseoilformulation .= " , oils='".mysql_real_escape_string($baseoil_val)."'";
						$sqlbaseoilformulation .= " , formulation='".mysql_real_escape_string($baseformulation)."'";
						$sqlbaseoilformulation .= " , base_oil='Y'";
						$sqlbaseoilformulation .= " , acedns='Y'";
						$sqlbaseoilformulation .= " , percentile_calc=''";
						$sqlbaseoilformulation .= " , user_id='".$_SESSION['admin_login']."'";
						$sqlbaseoilformulation .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
						$sqlbaseoilformulation .= " , datetime=CURRENT_TIMESTAMP";
						mysql_query($sqlbaseoilformulation) or die(mysql_error().".Internal error occur	in base oilrate formulation.Please check.");
				}
			}
			if($_POST['processoils_new']!='')
			{
				$sqloilformulationporcess  = "insert into loose_oilrate_formulation SET ";
				$sqloilformulationporcess .= "  plant_name='Bhiwadi'";
				$sqloilformulationporcess .= " , prod_code='".mysql_real_escape_string($dns_prod_code)."'";
				$sqloilformulationporcess .= " , oils='".mysql_real_escape_string($_POST['processoils_new'])."'";
				$sqloilformulationporcess .= " , base_oil=''";
				$sqloilformulationporcess .= " , acedns='Y'";
				$sqloilformulationporcess .= " , percentile_calc='N'";
				$sqloilformulationporcess .= " , user_id='".$_SESSION['admin_login']."'";
				$sqloilformulationporcess .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
				$sqloilformulationporcess .= " , datetime=CURRENT_TIMESTAMP";
				mysql_query($sqloilformulationporcess) or die(mysql_error().".Internal error occur	in oilrate formulation new process cost.Please check.");
				
				if($_POST['process_cost_new']!='')
				{
					$sqlprocessnew  = "insert into process_cost SET ";
					$sqlprocessnew .= "  oil_type='".mysql_real_escape_string($_POST['processoils_new'])."'";
					$sqlprocessnew .= " , oil_category=''";
					$sqlprocessnew .= " , process_cost='".mysql_real_escape_string($_POST['process_cost_new'])."'";
					$sqlprocessnew .= " , plant_name='Bhiwadi'";
					$sqlprocessnew .= " , user_id='".$_SESSION['admin_login']."'";
					$sqlprocessnew .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
					$sqlprocessnew .= " , datetime=CURRENT_TIMESTAMP";
					mysql_query($sqlprocessnew) or die(mysql_error().".Internal error occurrs in Process cost new.Please check.");
				}
				
			}
			
			$sqlupdateconversion="UPDATE product_unit_coversion_matrix SET acedns='N' WHERE prod_code='".mysql_real_escape_string($dns_prod_code)."'";
			$rsupdateconversion=mysql_query($sqlupdateconversion);
			$sqlconversion  = "insert into product_unit_coversion_matrix SET ";
			$sqlconversion .= "  	prod_code='".mysql_real_escape_string($dns_prod_code)."'";
			$sqlconversion .= " , mapped_prod_code='".mysql_real_escape_string($dns_prod_code)."'";
			$sqlconversion .= " , is_flash='".mysql_real_escape_string($is_flash)."'";
			$sqlconversion .= " , flash_name='".mysql_real_escape_string($flash_name)."'";
			$sqlconversion .= " , add_subtract_val='".mysql_real_escape_string($add_subtract)."'";
			$sqlconversion .= " , user_id='".$_SESSION['admin_login']."'";
			$sqlconversion .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
			$sqlconversion .= " , download_time=CURRENT_TIMESTAMP";
			mysql_query($sqlconversion) or die(mysql_error().".Internal error occurrs in conversion.Please check.");
		}
		else
		{
			$sqlupdateconversion="UPDATE product_unit_coversion_matrix SET acedns='N' WHERE prod_code='".mysql_real_escape_string($dns_prod_code)."'";
			$rsupdateconversion=mysql_query($sqlupdateconversion);
			$sqlconversion  = "insert into product_unit_coversion_matrix SET ";
			$sqlconversion .= "  	prod_code='".mysql_real_escape_string($dns_prod_code)."'";
			$sqlconversion .= " , mapped_prod_code='".mysql_real_escape_string($parent_product)."'";
			$sqlconversion .= " , is_flash='".mysql_real_escape_string($is_flash)."'";
			$sqlconversion .= " , flash_name='".mysql_real_escape_string($flash_name)."'";
			$sqlconversion .= " , add_subtract_val='".mysql_real_escape_string($add_subtract)."'";
			$sqlconversion .= " , user_id='".$_SESSION['admin_login']."'";
			$sqlconversion .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";
			$sqlconversion .= " , download_time=CURRENT_TIMESTAMP";
			mysql_query($sqlconversion) or die(mysql_error().".Internal error occurrs in conversion.Please check.");

		}
		$sqlprodupdate="UPDATE product_master SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE dns_prod_code='".$dns_prod_code."'";
		mysql_query($sqlprodupdate);
		header("location:adminSKUadd.php?mod=succ");
}
?>