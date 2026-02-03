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
		if (empty($_REQUEST['approval_authority'])) {
		$authorityerror = "Designation is required";
		array_push($error_array,$authorityerror);
		}
		$emp_code_array=array();
		$emp_code_array=$_REQUEST['emp_code'];
		//echo count($product_group_code_array);
		if (count($emp_code_array)==0) {
			$employeeerror = "Employee is required";
			array_push($error_array,$employeeerror);
		}
	if (empty($_REQUEST['approval_category'])) {
		$approvalcaterror = "Category is required";
		array_push($error_array,$approvalcaterror);
		}
	$approval_sub_category_array=$_REQUEST['approval_sub_category'];
	//echo count($product_sub_group_code_array);
		if (count($approval_sub_category_array)==0) {
			$approvalsubcaterror = "Field name is required";
			array_push($error_array,$approvalsubcaterror);
		}
		
	$operation_type_array=$_REQUEST['operation_type'];
	//echo count($product_sub_group_code_array);
		if (count($operation_type_array)==0) {
			$operationtypeerror = "Operation type is required";
			array_push($error_array,$operationtypeerror);
		}	
	if (empty($_REQUEST['approval_level'])) {
		$approvallevelerror = "Approval level is required";
		array_push($error_array,$approvallevelerror);
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Approval Matrix Creation</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminApprovalMatrixCreation.php" >
			<input type="hidden" name="mode" value="add">			
			<table width="55%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Create Approval Matrix</td>
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
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000">Approval matrix created successfully.</font></strong></td>
				</tr>
				<?php }
				?>
                                <tr>
					<td align="left" valign="top" class="tbllogin">Designation<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="approval_authority" id="approval_authority" onChange="javascript:sel_emp(this.value);">
                            <option value="">SELECT</option>
								<?php 
                                $sqlquerydesignation="SELECT DISTINCT designation FROM employee_master WHERE acedns='Y' AND designation <> '' ORDER BY designation ASC";
                                $resultquerydesignation = mysqli_query($link,$sqlquerydesignation);
                                $countquerydesignation=mysqli_num_rows($resultquerydesignation);
                                if($countquerydesignation >0){
                                while($rowquerydesignation= mysqli_fetch_assoc($resultquerydesignation))
                                {
                                ?>
                                <option value="<?php echo $rowquerydesignation['designation'];?>" <?php if( $_REQUEST['approval_authority']==$rowquerydesignation['designation'])
								{echo 'selected';}?>><?php echo $rowquerydesignation['designation'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $authorityerror;?></span>
                    </td>
				</tr>
                <tr>
					<td align="left" valign="top" class="tbllogin">Employee<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <div style="max-height:200px; overflow-y: scroll;
                        display:<?php if($_REQUEST['approval_authority']!=''){?>''<?php }else{?>none<?php }?>" id="emp_sel_div">
                         <table> 
                        <?php 
						$approval_authority=$_REQUEST['approval_authority'];
						foreach($product_group_code_arr as $product_group_code_val)
						{
							$product_group_code_val=$product_group_code_val;
						}
                       $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master where 
									designation='".$approval_authority."' ORDER BY emp_name ASC";
                       $resultqueryemp = mysqli_query($link,$sqlqueryemp);
						$countqueryemp=mysqli_num_rows($resultqueryemp);
						while($rowqueryqueryemp = mysqli_fetch_assoc($resultqueryemp))
						{
							?>
                            <tr>
                                <td align="left">
                                    <input type="checkbox" name="emp_code[]" value="<?php echo $rowqueryqueryemp['emp_code'];?>" 
									<?php if(in_array($rowqueryqueryemp['emp_code'],$_POST['emp_code'])){?>checked<?php }?>/>
									<?php echo $rowqueryqueryemp['emp_name'];?>
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                        </table>
                        </div>
                        <div><table><tr>
                                <td align="left">
                                   <span class="error"><?php echo $employeeerror;?></span>
                                </td>
                            </tr></table></div>
                    </td>
				</tr>

                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Category<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="approval_category" id="approval_category" 
                    onChange="javascript:sel_approval_sub_category(this.value);">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryapprovalcat="SELECT DISTINCT approval_category FROM approval_sub_category ORDER BY approval_category ASC";
                                $resultqueryapprovalcat = mysqli_query($link,$sqlqueryapprovalcat);
                                $countqueryapprovalcat=mysqli_num_rows($resultqueryapprovalcat);
                                if($countqueryapprovalcat>0){
                                while($rowqueryapprovalcat = mysqli_fetch_assoc($resultqueryapprovalcat))
                                {
                                ?>
                                <option value="<?php echo $rowqueryapprovalcat['approval_category'];?>" <?php if( $_REQUEST['approval_category']==$rowqueryapprovalcat['approval_category'])
								{echo 'selected';}?>><?php echo strtoupper($rowqueryapprovalcat['approval_category']);?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $approvalcaterror;?></span></td>
				</tr>
                <tr >
					<td align="left" valign="top" class="tbllogin">Field name<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top">
                    <div style="max-height:200px; overflow-y: scroll;display:
					<?php if($_REQUEST['approval_category']!=''){?>''<?php }else{?>none<?php }?>" id="sub_category_sel_div">
                  <?php $approval_category=$_REQUEST['approval_category'];?>
                    <table> 	
                        <!--select name="approval_sub_category" id="approval_sub_category" >
                            <option value="">SELECT</option-->
								<?php 
                                $sqlqueryapprovalsubcat="SELECT approval_sub_category FROM approval_sub_category where 
													approval_category='".$_REQUEST['approval_category']."' AND is_active='yes' ORDER BY approval_sub_category ASC";
                                $resultqueryapprovalsubcat = mysqli_query($link,$sqlqueryapprovalsubcat);
                                $countqueryapprovalsubcat=mysqli_num_rows($resultqueryapprovalsubcat);
                                if($countqueryapprovalsubcat>0){
                                while($rowqueryapprovalsubcat = mysqli_fetch_assoc($resultqueryapprovalsubcat))
                                {
                                ?>
                                <!--option value="<?php /*echo strtoupper(str_replace('_','',$rowqueryapprovalsubcat['approval_sub_category']));?>" <?php if( $_REQUEST['approval_sub_category']==$rowqueryproductgroup['product_group_code'])
								{echo 'selected';}?>><?php echo $rowqueryapprovalsubcat['approval_sub_category'];*/?></option-->
                                <?php
								?>
                            <tr>
                                <td align="left">
                                    <input type="checkbox" name="approval_sub_category[]" value="<?php echo $rowqueryapprovalsubcat['approval_sub_category'];?>" 
									<?php if(in_array($rowqueryapprovalsubcat['approval_sub_category'],$_POST['approval_sub_category'])){?>checked<?php }?>/>
									<?php echo $rowqueryapprovalsubcat['approval_sub_category'];?>
                                </td>
                             </tr>   
                        <?php
                                }
                            }
                            ?>	
                        <!--/select-->
                        </table>
                        </div>
                        <div><table><tr>
                                <td align="left">
                                   <span class="error"><?php echo $approvalsubcaterror;?></span>
                                </td>
                            </tr></table></div>
                    </td>
				</tr>
                <?php $operation_arr=array('ADD','EDIT','APPROVE','REJECT');?>
                 <tr>
					<td align="left" valign="top" class="tbllogin">Choose Operation<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <!--select name="operation_type" id="operation_type">
                            <option value="">SELECT</option>
							<option value="ADD">ADD</option>
                            <option value="EDIT">EDIT</option>
                            <option value="EDIT">APPROVE</option>
                            <option value="EDIT">REJECT</option>	
                        </select>&nbsp;<span class="error"><?php //echo $operationtypeerror;?></span-->
                         <?php foreach($operation_arr as $operation_val){
							?> 
							  <input type="checkbox" name="operation_type[]" value="<?php echo $operation_val;?>" 
									<?php if(in_array($operation_val,$_POST['operation_type'])){?>checked<?php }?>/>
									<?php echo $operation_val;?>
                      <?php
						 }
						?> &nbsp;<span class="error"><?php echo $operationtypeerror;?></span>
                    </td>
				</tr>
                 <tr>
					<td align="left" valign="top" class="tbllogin">Approval level<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="approval_level" id="approval_level" >
                            <option value="">SELECT</option>
							<option value="1 tier" <?php if($_POST['approval_level']=='1 tier'){ echo 'selected';}?>>1 tier</option>
                            <option value="2 tier" <?php if($_POST['approval_level']=='2 tier'){ echo 'selected';}?>>2 tier</option>
                            <option value="3 tier" <?php if($_POST['approval_level']=='3 tier'){ echo 'selected';}?>>3 tier</option>
                            <option value="4 tier" <?php if($_POST['approval_level']=='4 tier'){ echo 'selected';}?>>4 tier</option>	
                        </select>&nbsp;<span class="error"><?php echo $approvallevelerror;?></span>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Add " class="inplogin">&nbsp;&nbsp;<input type="button" value=" Cancel " class="inplogin" onclick="window.location='adminApprovalMatrixListing.php'"></td>
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
	function sel_approval_sub_category(category){
		document.getElementById("sub_category_sel_div").style.display='';
		document.getElementById("sub_category_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?category='+category+'&mode=approvalsubcat','sub_category_sel_div',0);
	
	}
	function sel_emp(designation){
		//alert(designation);
		document.getElementById("emp_sel_div").style.display='';
		document.getElementById("emp_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?designation='+designation+'&mode=approvalemp','emp_sel_div',0);
		
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
	$approval_authority=$_REQUEST['approval_authority'];
	$emp_code=$_REQUEST['emp_code'];
	$approval_category=$_REQUEST['approval_category'];
	$approval_sub_category=$_REQUEST['approval_sub_category'];
	$operation_type=$_REQUEST['operation_type'];
	$approval_level=$_REQUEST['approval_level'];
		
		foreach($emp_code as $emp_code_val)
		{
			foreach($approval_sub_category as $approval_sub_category_val)
			{
				foreach($operation_type as $operation_type_val)
				{
					
					
					$sqlinsertappmatrix="INSERT INTO approval_matrix ";
					$sqlinsertappmatrix .= " SET approval_category='".$approval_category."'";
					$sqlinsertappmatrix .= " , approval_sub_category='".$approval_sub_category_val."'";
					$sqlinsertappmatrix .= " , operation_type='".$operation_type_val."'";
					$sqlinsertappmatrix .= " , approval_authority='".$approval_authority."'";
					$sqlinsertappmatrix .= " , approval_authority_id='".$emp_code_val."'";
					$sqlinsertappmatrix .= " , approval_level='".$approval_level."'";
					$sqlinsertappmatrix .= " , is_active='yes'";
					mysqli_query($link,$sqlinsertappmatrix);
				}
			}
		}
		//$GLOBALS['err_msg']="Customer information has been added successfully.";
		$GLOBALS['err_msg']='';
		header("location:adminApprovalMatrixCreation.php?mod=succ");
		//exit();
		//$GLOBALS['mode']='pricingadd';
		//add_pricing_data($dns_prod_code);
}
?>