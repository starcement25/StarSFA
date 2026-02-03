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
	$batchnoerror='';
	$batchdateerror='';
	$inqtyerror='';
	$outqtyerror='';
	$mode = $_REQUEST['mode'];
	$error_array=array();

	if($mode == 'add')
	{
		if (empty($_REQUEST['prod_code'])) {
		$prodcodeerror = "Product is required";
		array_push($error_array,$prodcodeerror);
		}
	if (empty($_REQUEST['batch_no'])) {
		$batchnoerror = "Batch no is required";
		array_push($error_array,$batchnoerror);
		}
		$sqlprodchk="SELECT prod_code,dns_prod_code,batch_no FROM batch_wise_stock WHERE dns_prod_code='".$_REQUEST['prod_code']."' 
					AND batch_no='".$_REQUEST['batch_no']."'";
		$rsprodchk=mysqli_query($link,$sqlprodchk);
		$countprodchk=mysqli_num_rows($rsprodchk);
		$csv_row_count=$rec_count+1;
		if($countprodchk>0)
		{
			$prodcodeerror = "Data already exists for that product and Batch no Please choose another";
			array_push($error_array,$prodcodeerror);
		}
	
	if (empty($_REQUEST['batch_date'])) {
		$batchdateerror = "Batch date is required";
		array_push($error_array,$batchdateerror);
		}
	if (empty($_REQUEST['in_qty'])) {
		$inqtyerror = "In qty is required";
		array_push($error_array,$inqtyerror);
		}	
	else if(filter_var($_REQUEST['in_qty'], FILTER_VALIDATE_FLOAT) === false ) {
		$inqtyerror = "In qty to be integer or decimal value";
		array_push($error_array,$inqtyerror);
	}
	if (empty($_REQUEST['out_qty'])) {
		$outqtyerror = "Out qty is required";
		array_push($error_array,$outqtyerror);
		}	
	else if(filter_var($_REQUEST['out_qty'], FILTER_VALIDATE_FLOAT) === false ) {
		$outqtyerror = "Out qty have to integer or decimal value";
		array_push($error_array,$outqtyerror);
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
[type="date"]::-webkit-calendar-picker-indicator {
  display: none;
}
</style>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Batch wise Product Stock</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminBatchWiseStockPreperation.php" >
			<input type="hidden" name="mode" value="add">			
			<table width="55%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Create Batch Wise Product Stock</td>
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
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000">Batch wise Product Stock created successfully.</font></strong></td>
				</tr>
				<?php }
				?>
                                <tr>
					<td align="left" valign="top" class="tbllogin">Product<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="prod_code" id="prod_code" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryprod="SELECT DISTINCT prod_code,prod_desc,dns_prod_code FROM product_master WHERE acedns='Y' ORDER BY prod_desc ASC";
                                $resultqueryprod = mysqli_query($link,$sqlqueryprod);
                                $countqueryprod=mysqli_num_rows($resultqueryprod);
                                if($countqueryprod >0){
                                while($rowqueryprod= mysqli_fetch_assoc($resultqueryprod))
                                {
                                ?>
                                <option value="<?php echo $rowqueryprod['dns_prod_code'];?>" <?php 
								if( $_REQUEST['prod_code']==$rowqueryprod['dns_prod_code'])
								{echo 'selected';}?>><?php echo $rowqueryprod['prod_desc'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>&nbsp;<span class="error"><?php echo $prodcodeerror;?></span>
                    </td>
				</tr>
                <tr>
					<td align="left" valign="top" class="tbllogin">Batch No<font color="#FF0000"><strong>*</strong></font></td>
					<td align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                      <input type="text" name="batch_no" value="<?php echo $_REQUEST['batch_no'];?>" />&nbsp;<span class="error"><?php echo $batchnoerror;?></span>
                    </td>
				</tr>

                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Batch Date<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                      <input type="date" name="batch_date" value="<?php echo $_REQUEST['batch_date'];?>" />&nbsp;<span class="error"><?php echo $batchdateerror;?></span>
                    </td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">In Qty<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                      <input type="text" name="in_qty" id="in_qty" value="<?php echo $_REQUEST['in_qty'];?>" style="width:100px;"  onblur="javascript:prep_stock();"/>&nbsp;<span class="error"><?php echo $inqtyerror;?></span>
                    </td>
				</tr>
                 <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Out Qty<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                      <input type="text" name="out_qty"  id="out_qty" value="<?php echo $_REQUEST['out_qty'];?>"  style="width:100px;"  onblur="javascript:prep_stock();" />&nbsp;<span class="error"><?php echo $outqtyerror;?></span>
                    </td>
				</tr>
                <tr>
					<td width="30%" align="left" valign="top" class="tbllogin">Closing Stock</td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                      <input type="text" name="cl_stk" id="cl_stk" value="" style="width:100px;" readonly="readonly"/>
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
	function prep_stock(){
		
		var in_qty=document.getElementById("in_qty").value;
		var out_qty=document.getElementById("out_qty").value;
		var cl_stk=parseFloat(in_qty)-parseFloat(out_qty);
		if(cl_stk > 0)
		{
		document.getElementById("cl_stk").value=cl_stk;
		}
		
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
	$prod_code=$_REQUEST['prod_code'];
	$batch_no=$_REQUEST['batch_no'];
	$batch_date=$_REQUEST['batch_date'];
	$in_qty=$_REQUEST['in_qty'];
	$out_qty=$_REQUEST['out_qty'];
	$cl_stk=$in_qty-$out_qty;
	
	$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code."'";
	$rsprodcode=mysqli_query($link,$sqlprodcode);
	$rowprodcode=mysqli_fetch_assoc($rsprodcode);
	$prodcode=$rowprodcode['prod_code'];
		
	$sqlstk  = "insert into batch_wise_stock SET ";
	$sqlstk .= "  	prod_code='".$prodcode."'";
	$sqlstk .= " , dns_prod_code='".mysqli_real_escape_string($prod_code)."'";
	$sqlstk .= " , batch_no='".mysqli_real_escape_string($batch_no)."'";
	$sqlstk .= " , batch_date='".mysqli_real_escape_string($batch_date)."'";
	$sqlstk .= " , in_qty='".mysqli_real_escape_string($in_qty)."'";
	$sqlstk .= " , out_qty='".mysqli_real_escape_string($out_qty)."'";
	$sqlstk .= " , cl_stock='".mysqli_real_escape_string($cl_stk)."'";
	$sqlstk .= " , download_time=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlstk);
		//$GLOBALS['err_msg']="Customer information has been added successfully.";
		$GLOBALS['err_msg']='';
		header("location:adminBatchWiseStockPreperation.php?mod=succ");
		//exit();
		//$GLOBALS['mode']='pricingadd';
		//add_pricing_data($dns_prod_code);
}
?>