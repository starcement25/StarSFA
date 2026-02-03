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
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Customer Addition</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminCustomerAddModified.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="add">			
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Add Customer</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }
				if($_REQUEST['mod']=="succ"){?>
				<tr>
					<td align="center" colspan="6" class="ERR"><strong><font color="#FF0000">Customer information has been added successfully.</font></strong></td>
				</tr>
				<?php }
				?>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Code<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="dns_customer_code" id="dns_customer_code" class="inplogin" style="width:300px;height:30px;" value="<?php echo $_REQUEST['dns_customer_code'];?>"/></td>
				</tr>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="customer_name" id="customer_name" class="inplogin" style="width:300px;height:30px;" value="<?php echo $_REQUEST['customer_name'];?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    	<select name="cust_type" id="cust_type"  >
                            <option value="">SELECT</option>
                             <!--option value="D">D</option>
                             <option value="SS">SS</option-->
                             <?php 
                                $sqlquerycusttype="SELECT DISTINCT cust_type FROM customer_master where customer_code 
											IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation  WHERE acedns='Y') ORDER BY cust_type ASC";
                                $resultquerycusttype = mysql_query($sqlquerycusttype);
                                $countquerycusttype=mysql_num_rows($resultquerycusttype);
                                if($countquerycusttype>0){
                                while($rowquerycusttype = mysql_fetch_array($resultquerycusttype))
                                {
                                echo "<option value='".$rowquerycusttype['cust_type']."'>".$rowquerycusttype['cust_type']."</option>";
                                }
                              }
                            ?>	
                        </select>
                      </td>
				</tr>
                 <tr id="tagged_ss_tr">
					<td width="45%" align="right" valign="top" class="tbllogin">Tagged SS</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
                    <select name="tagged_ss" id="tagged_ss">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryss="SELECT dns_customer_code,customer_code,customer_name FROM customer_master where cust_type='SS' AND acedns='Y'";
                                $resultqueryss = mysql_query($sqlqueryss);
                                $countqueryss=mysql_num_rows($resultqueryss);
                                if($countqueryss>0){
                                while($rowqueryss = mysql_fetch_array($resultqueryss))
                                {
                                echo "<option value=\"'".$rowqueryss['customer_code']."'\">".$rowqueryss['customer_name']."</option>";
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Phone no<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="phone_no" id="phone_no" class="inplogin" style="width:100px;height:20px;" value="<?php echo $_REQUEST['phone_no'];?>"/></td>
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
                                echo "<option value=\"'".$rowquerystate['state']."'\">".$rowquerystate['state']."</option>";
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Route Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><div id="route_select_div"></div>
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
                                <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $_REQUEST['emp_name']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Credit Limit<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="credit_limit" id="credit_limit"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $_REQUEST['credit_limit'];?>" maxlength="13"/></td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Credit Days<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="credit_days" id="credit_days" class="inplogin" style="width:100px;height:20px;" value="<?php echo $_REQUEST['credit_days'];?>"/></td>
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
                                <option value="<?php echo $rowquerybranch['branch_code'];?>" <?php if( $_REQUEST['branch_name']==$rowquerybranch['branch_code']){echo 'selected';}?>><?php echo $rowquerybranch['branch_name'];?></option>
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
					<td align="left" valign="top" ><input type="text" name="address" id="address" class="inplogin" style="width:300px;height:30px;" value="<?php echo $_REQUEST['address'];?>"/></td>
				</tr>
                 <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">TIN/GST<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top" ><input type="text" name="TIN"  id="TIN" class="inplogin" style="width:120px;height:20px;" value="<?php echo $_REQUEST['TIN'];?>"/>
                   &nbsp;&nbsp;&nbsp;&nbsp;<b>PAN</b><font color="#FF0000"><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;<input type="text" name="PAN" id="PAN" class="inplogin" style="width:120px;height:20px;" value="<?php echo $_REQUEST['PAN'];?>"/>
                    </td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">PIN<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="pin" id="pin"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $_REQUEST['pin'];?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Incoterms<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="incoterms" id="incoterms" >
                            <option value="">SELECT</option><option value="EX PLANT">EX PLANT</option><option value="FOR PLANT">FOR PLANT</option></select></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Sauda Limit<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="sauda_limit" id="sauda_limit"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $_REQUEST['sauda_limit'];?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Transport Mode<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><select name="transport_mode" id="transport_mode" >
                            <option value="">SELECT</option><option value="TRUCK">TRUCK</option><option value="TANKER">TANKER</option></select></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Capacity<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="loadability_ton" id="loadability_ton"  class="inplogin" style="width:100px;height:20px;" value="<?php echo $_REQUEST['loadability_ton'];?>"/></td>
				</tr>
               <tr>
                <td align="right" width="45%"  valign="top" class="tbllogin">Select Product Category<font color="#FF0000"><strong>*</strong></font>&nbsp;&nbsp;</td>
                  <td width="5%" align="center" valign="top" class="tbllogin">:</td>
                  <td align="left" valign="top"><div style="max-height:200px; overflow-y: scroll;">
                  <?php $product_group_code=$_REQUEST['product_group_code'];?>
                    <table >
                        <tr>
                            <td align="left">
                                 <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                            </td>
                         </tr>  
                        <?php 
                        $sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
                        $rsproductgroup=mysql_query($sqlproductgroup);
                        while($rowproductgroup=mysql_fetch_array($rsproductgroup))
                        {?>
                            <tr>
                                <td align="left">
                                    <input type="checkbox" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>checked<?php }?>/><?php echo $rowproductgroup['product_group_name'];?>
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
					<td><input type="submit" value=" Add " class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
	<script type="text/javascript" src="ajax1.js"></script>
	<script>
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
	function tag_ss(tagval)
	{
		//alert(tagval);
		if(tagval=='D')
		{
			//alert(tagval);
			document.getElementById("tagged_ss_tr").style.display='';
		}
		else
		{
			document.getElementById("tagged_ss_tr").style.display='none';
		}
	}
	function check(form)
	{
		if(form.dns_customer_code.value.search(/\S/)==-1)
		{
			alert("Please enter customer code");
			form.dns_customer_code.focus();
			return false;
		}
		if(form.customer_name.value.search(/\S/)==-1)
		{
			alert("Please enter customer name");
			form.customer_name.focus();
			return false;
		}
		if(form.cust_type.value.search(/\S/)==-1)
		{
			alert("Please select customer type");
			form.cust_type.focus();
			return false;
		}
		if(form.phone_no.value.search(/\S/)==-1)
		{
			alert("Please enter phone no");
			form.phone_no.focus();
			return false;
		}
		else
		{
			var phone_no=form.phone_no.value;
			if(isNaN(phone_no) || phone_no.length !=10)
			{
				alert("Please enter Phone no of 10 digit");
				form.phone_no.focus();
				return false;
			}
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
			//alert(TIN.length);
			//var letters = new RegExp("/^[a-zA-Z]+[a-zA-Z0-9._]+$/");
      //if(inputtxt.value.match(letters))
	  		//var numbers = new RegExp("[^0-9]");
			
     // if(inputtxt.value.match(numbers))
	 		//alert(letters.test(TIN));
	 		/*if(letters.test(TIN)==true)
			{
				alert("TIN should be alphanumeric");
				form.TIN.focus();
				return false;
			}*/
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
		else
		{
			if(isNaN(form.pin.value))
			{
				alert("Pin should be numeric");
				form.pin.focus();
				return false;
			}
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
		else
		{
			if(isNaN(form.sauda_limit.value))
			{
				alert("Sauda limit should be numeric");
				form.sauda_limit.focus();
				return false;
			}
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
		else
		{
			if(isNaN(form.sauda_limit.value))
			{
				alert("Capacity should be numeric");
				form.sauda_limit.focus();
				return false;
			}
		}
		var is_checked=false;
		for(i=0; i<document.frmadd.elements.length; i++){
			if(document.frmadd.elements[i].type=="checkbox" && document.frmadd.elements[i].checked==true 
					&& document.frmadd.elements[i].name=='product_group_code[]'){
				is_checked=true;
				break;
			}
		}
		if(!is_checked){
			alert("Please check at least one Product Category");
			return false;
		}
		/*if(form.acedns.value==' ')
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
		}*/
		return true;
	}
</script>
	<?php
}//End of main()
function add_record()
{
	$dns_customer_code=$_REQUEST['dns_customer_code'];
	$customer_name=$_REQUEST['customer_name'];
	$cust_type=$_REQUEST['cust_type'];
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
	$product_group_code_array=$_POST['product_group_code'];
	$product_group_code="'".implode("','", $product_group_code)."'";
	
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
		if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $TIN) && preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $PAN)) {
		$sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
		$rscustomernamechk=mysql_query($sqlcustomernamechk);
		$countcustomernamechk=mysql_num_rows($rscustomernamechk);
		
		$sqlcustomerphonechk="SELECT * FROM customer_master WHERE phone_no='".addslashes($phone_no)."'";
		$rscustomerphonechk=mysql_query($sqlcustomerphonechk);
		$countcustomerphonechk=mysql_num_rows($rscustomerphonechk);
		
		$sqlcustomerTINchk="SELECT * FROM customer_master WHERE TIN='".addslashes($TIN)."'";
		$rscustomerTINchk=mysql_query($sqlcustomerTINchk);
		$countcustomerTINchk=mysql_num_rows($rscustomerTINchk);
		
		$sqlcustomerPANchk="SELECT * FROM customer_master WHERE PAN='".addslashes($PAN)."'";
		$rscustomerPANchk=mysql_query($sqlcustomerPANchk);
		$countcustomerPANchk=mysql_num_rows($rscustomerPANchk);
		
		$sqlcustomerfreightchk="SELECT * FROM branch_route_freight WHERE branch_code='".addslashes($branch_name)."' AND 
								route_code='".addslashes($route)."' AND transport_mode='".addslashes($transport_mode)."' AND 
								capacity='".addslashes($loadability_ton)."' and acedns='Y'";
		$rscustomerfreightchk=mysql_query($sqlcustomerfreightchk);
		$countcustomerfreightchk=mysql_num_rows($rscustomerfreightchk);
		
		if($countcustomernamechk > 0)
		{
			$GLOBALS['err_msg']="Customer code already exists.";
		}
		else if($countcustomerphonechk > 0)
		{
			$GLOBALS['err_msg']="Phone no already exists.";
		}
		else if($countcustomerTINchk > 0)
		{
			$GLOBALS['err_msg']="TIN already exists.";
		}
		else if($countcustomerPANchk > 0)
		{
			$GLOBALS['err_msg']="PAN already exists.";
		}
		else if($countcustomerfreightchk< 1)
		{
			$GLOBALS['err_msg']="Freight does not exist.";
		}
		else
		{
		$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
		$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
		$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
		$max_customer_code=$rowmaxcustomercode['max_customer_code'];
		
		if($max_customer_code=='')
		{
			$max_customer_code='C/0000001';
		}
		else
		{
			$max_customer_code++;
		}
		$sql  = "insert into customer_master ";
		$sql .= " SET customer_code='".$max_customer_code."'";
		$sql .= " , dns_customer_code='".$dns_customer_code."'";
		$sql .= " , customer_name='".addslashes($customer_name)."'";
		$sql .= " , branch_code='".addslashes($branch_name)."'";
		$sql .= " , phone_no='".$phone_no."'";
		$sql .= " , route_code=".$route."";
		$sql .= " , credit_limit='".$credit_limit."'";
		$sql .= " , credit_days='".$credit_days."'";
		$sql .= " , acedns='Y'";
		$sql .= " , black_list='N'";
		$sql .= " , cust_type='".addslashes($cust_type)."'".$sqltagged_ss;
		$sql .= " , address='".$address."'";
		$sql .= " , TIN='".$TIN."'";
		$sql .= " , PAN='".$PAN."'";
		$sql .= " , state_code=".$state_code."";
		$sql .= " , incoterms='".addslashes($incoterms)."'";
		$sql .= " , loadability_ton='".addslashes($loadability_ton)."'";
		$sql .= " , transport_mode='".addslashes($transport_mode)."'";
		$sql .= " , pin='".addslashes($pin)."'";
		$sql .= " , retailer_app='yes'";
		$sql .= " , download_time=CURRENT_TIMESTAMP()";
		mysql_query($sql);
		$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$max_customer_code."',
								 route_code=".$route.",
								emp_code='".$emp_name."',
								acedns='Y',
								download_time=CURRENT_TIMESTAMP()";
		mysql_query($sqlinsertcustomerroute);
		
		$sqlinsertcustomersdaudalimit="INSERT INTO customer_sauda_limit ";
		$sqlinsertcustomersdaudalimit .= " SET customer_code='".$dns_customer_code."'";
		$sqlinsertcustomersdaudalimit .= " , sauda_limit='".$sauda_limit."'";
		$sqlinsertcustomersdaudalimit .= " 	,download_time=CURRENT_TIMESTAMP()";
		mysql_query($sqlinsertcustomersdaudalimit);
		if($cust_type!='R')
		{
		$sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$max_customer_code."',
										route_code=".$route.",emp_code='".$emp_name."',download_time=CURRENT_TIMESTAMP()";
		$rsinsertdistributorroute=mysql_query($sqlinsertdistributorroute);
		}
		$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation SET customer_code='".$max_customer_code."',
								  branch_code='".$branch_name."',
								  acedns='Y',
								 download_time=CURRENT_TIMESTAMP()";
		mysql_query($sqlinsertcustomerbranch);
		foreach($product_group_code_array as $product_group_code_val)
		{
			$sqlinsertproductrelation="INSERT INTO customer_product_relation SET 
										customer_code='".$max_customer_code."',
										oil_category='".$product_group_code_val."',
										acedns='Y',
										download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlinsertproductrelation);							
		}
		
		//$GLOBALS['err_msg']="Customer information has been added successfully.";
		$GLOBALS['err_msg']='';
		header("location:adminCustomerAddModified.php?mod=succ");
		//exit();
		}
		}
		else
		{
			$GLOBALS['err_msg']="TIN and PAN should be alphanumeric.";
		}
		disphtml("main();");
}
?>