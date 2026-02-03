<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	//if($mode == 'add')						   add_record();
	disphtml("main();");
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
	$mode = $_REQUEST['mode'];
	$state_code_GST_array=array();
	$state_name_array=array();
	$error_array=array();

	if($mode == 'add')
	{
		add_record();
		
	}
   $remarks="<b>TEST<b/>\n <b>not purchased last 2 weeks</b>";
?>
<style>
.error{
color:red;
font-weight:bold;
}
</style>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong>Order Submit</strong></td>
	</tr>
    <?php if($_REQUEST['mod']=="succ"){?>
        <tr>
            <td align="center"  class="ERR"><strong><font color="#FF0000">Order submitted successfully.</font></strong></td>
        </tr>
        <?php }
        ?>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminOrderSubmit.php" >
			<input type="hidden" name="mode" value="add">			
			<table width="70%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Order Submit</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandatory.</td>
				</tr>
				
				
				
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Choose Employee<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"> <select name="emp_code" id="emp_code">
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryemp="SELECT dns_emp_code,emp_code,emp_name FROM employee_master where acedns='Y'";
                                $resultqueryemp = mysql_query($sqlqueryemp);
                                $countqueryemp=mysql_num_rows($resultqueryemp);
                                if($countqueryemp>0){
                                while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                {
									if( $_REQUEST['emp_code']=="'".$rowqueryemp['emp_code']."'") { $selected='selected';}
									else{ $selected='';}
                                echo "<option value=".$rowqueryemp['emp_code']." ".$selected.">".$rowqueryemp['emp_name']."</option>";
                                }
                            }
                            ?>	
                        </select><?php echo $employeeerror;?></span></td>
				</tr>
                <tr>
                	<td width="20%" align="left" valign="top" class="tbllogin">Choose Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="customer_type" id="customer_type" class="inplogin" value="recommended" onChange="javascript:sel_distributor('recommended');"/>RECOMMENDED COUNTER<input type="radio" name="customer_type" id="customer_type" class="inplogin" 
                    value="additional" onChange="javascript:sel_distributor('additional');"/>ADDITIONAL COUNTER</td>
				</tr>
				<tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Distributor Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><div id="dist_select_div">&nbsp;<span class="error"><?php echo $distributorerror;?></span></div></td>
				</tr>
                <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Route Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><div id="route_select_div">
                    &nbsp;<span class="error"><?php echo $routeerror;?></span>
                    </div>
                    </td>
				</tr>
                  <tr>
					<td width="20%" align="left" valign="top" class="tbllogin">Customer Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="3%" align="left" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><div id="customer_select_div">
                    &nbsp;&nbsp;<span class="error"><?php echo $customererror;?></span>
                    </div>
                    </td>
				</tr>
                 <tr><td width="20%" align="left" valign="top" class="tbllogin">Customer Info Type<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" value="last_days_1" onChange="javascript:sel_cust_info('last_days_1');"/>Last 10 Days<input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="last_days_2" onChange="javascript:sel_cust_info('last_days_2');"/>Last 2 Weeks
                    <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="proposed_sku" onChange="javascript:sel_cust_info('proposed_sku');"/>Top 10 Proposed SKU
                     <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="focused_product" onChange="javascript:sel_cust_info('focused_product');"/>Focus Product
                    </td>
                 </tr>
                 <tr >
                <td align="left" width="60%"  valign="top" class="tbllogin" colspan="3">
                    <div id="customer_info_div"></div>
                 </td>
                 </tr>   
                 <tr><td colspan="3" align="center" width="95%"><b><u>Choose SKU</u></b></td></tr>
                <tr id="oil_formulation_tr" >
                <td align="left" width="60%"  valign="top" class="tbllogin" colspan="3">
                    <table cellpadding="5" cellspacing="2" width="60%" style="height: 250px;overflow-y: scroll;display:block;" align="center">
                          <tr>
                                <td align="left"><b>SKU</b></td>
                                <td align="left">
                                   <b>Qty</b>
                                </td>
                                <td align="left">
                                  <b>Rate</b>
                                </td>
                             </tr>
                             <tr>
    
                        <?php 
                        $sqlproduct="SELECT PM.prod_desc,MRP.mrp,PM.prod_code FROM product_master PM,mrp MRP  WHERE PM.prod_code=MRP.product_code 
											ORDER BY PM.prod_desc ASC";
                        $rsproduct=mysql_query($sqlproduct);
                        while($rowproduct=mysql_fetch_array($rsproduct))
                        {
							//print_r($_POST['product_group_code']);
							$prod_desc=$rowproduct['prod_desc'];
							$prod_code=$rowproduct['prod_code'];
							$mrp=$rowproduct['mrp'];
							?>
                            <tr>
                                <td align="left"><?php echo $prod_desc;?></td>
                                 <td align="left">
                                 <input type="text" name="qty_<?php echo $prod_code;?>" id="qty_<?php echo $prod_code;?>" 
                                 value="<?php if($_POST['qty_'.$prod_code]>0){echo $_POST['qty_'.$prod_code];}?>"  />
                                </td>
                                <td align="left">
                                  <?php echo number_format($mrp,2);?>
                                </td>
                             </tr>   
                        <?php
                        }
                    ?>
                     
                    </table>
                </td>
              </tr>
               <tr><td width="20%" align="left" valign="top" class="tbllogin">Remarks</td>
					<td width="1%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left" width="">
                    <input type="radio" name="remarks" id="remarks" class="inplogin" value="Stocks Available" onChange="javascript:remarks_other('Stocks Available');"/>Stocks Available<input type="radio" name="remarks" id="remarks" class="inplogin" 
                    value="Visit Next day" onChange="javascript:remarks_other('Visit Next day');"/>Visit Next day
                    <input type="radio" name="remarks" id="remarks" class="inplogin" 
                    value="Previous Order Not delivered" onChange="javascript:remarks_other('Previous Order Not delivered');"/>Previous Order Not delivered
                     <input type="radio" name="remarks" id="remarks" class="inplogin" 
                    value="Shop Closed" onChange="javascript:remarks_other('Shop Closed');"/>Shop Closed
                     <input type="radio" name="remarks" id="remarks" class="inplogin" 
                    value="other" onChange="javascript:remarks_other('other');"/>Other
                    </td>
                 </tr>
                 <tr id="remarks_tr" style="display:none">
                  <td width="20%" align="left" valign="top" class="tbllogin"></td>
                            <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width=""><b>Other Reamrks</b><br /><br /><input type="text" name="other_remarks" id="other_remarks" class="inplogin" style="width:300px;height:30px;" 	
                            value="<?php echo $_REQUEST['other_remarks'];?>"/>&nbsp;<span class="error"><?php echo $otherremarkserror;?></span></td>
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script language="JavaScript" src="calendar3.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script type="text/javascript" src="jquery.highlight.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
	<script type="text/javascript" src="ajax1.js"></script>
	<script>
	function GetXmlHttpObject()
	{
		var xmlHttp=null;
		try
		{
			// Firefox, Opera 8.0+, Safari
			xmlHttp=new XMLHttpRequest();
		}
	
		catch (e)
		{
			// Internet Explorer
			try
			{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		return xmlHttp;
	}
	function sel_distributor(cust_type){
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		if(document.getElementById("customer_type").value.search(/\S/) == -1)
			return false;
		var emp_code=document.getElementById("emp_code").value;
		document.getElementById("dist_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		//GenericAjaxFunction('get_distributor_related_data.php?cust_type='+cust_type+'&type=fetchdist+&emp_code='+emp_code,'dist_select_div',0);
		var url='get_distributor_related_data.php?cust_type='+cust_type+'&type=fetchdist+&emp_code='+emp_code;
		xmlHttp.onreadystatechange=showDetailsDist;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	}
	function showDetailsDist()
	 {
		if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		 {
			var val=xmlHttp.responseText;
			//alert(val);
			if(val!="")
			 {
				document.getElementById("dist_select_div").innerHTML = val;
			 }
		}
	 }
	function distributor_route(cust_type,distributor_code){
		if(document.getElementById("distributor").value.search(/\S/) == -1)
			return false;
			var emp_code=document.getElementById("emp_code").value;
			var customer_type=document.getElementById("customer_type").value;
		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_distributor_related_data.php?cust_type='+cust_type+'&type=fetchroute+&emp_code='+emp_code+'&distributor_code='+distributor_code,'route_select_div',0);
	}
	function route_customer(cust_type,distributor_code,route_code){
		if(document.getElementById("distributor").value.search(/\S/) == -1)
			return false;
			var emp_code=document.getElementById("emp_code").value;
			//var customer_type=document.getElementById("customer_type").value;
		document.getElementById("customer_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_distributor_related_data.php?cust_type='+cust_type+'&type=fetchroutecust+&emp_code='+emp_code+'&distributor_code='+distributor_code+'&route_code='+route_code,'customer_select_div',0);
	}
	function sel_cust_info(customer_info_val)
	{
		if(customer_info_val=='last_days_1')
		{
			var fetchval='10';
			var customer_code=document.getElementById("customer_code").value;
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code,'customer_info_div',0);
		}
		if(customer_info_val=='last_days_2')
		{
			var fetchval='14';
			var customer_code=document.getElementById("customer_code").value;
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code,'customer_info_div',0);
		}
		if(customer_info_val=='focused_product')
		{
			var fetchval='';
			var customer_code=document.getElementById("customer_code").value;
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code,'customer_info_div',0);
		}
		if(customer_info_val=='proposed_sku')
		{
			var fetchval='10';
			var customer_code=document.getElementById("customer_code").value;
			var distributor=document.getElementById("distributor").value;
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code+'&distributor='+distributor,'customer_info_div',0);
		}
	}
	function show_remarks()
	{
		var customer_code=document.getElementById("customer_code").value;
		if(customer_code=='')
		{
			alert('Please choose a customer');
		}
		else
		{
		//var textpara = "TEST" + "\n\n" + "not purchased last 2 weeks";
		xmlHttpone=GetXmlHttpObject()
		if (xmlHttpone==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		var url='get_distributor_related_data.php?type=fetchremarks+&customer_code='+customer_code;
		xmlHttpone.onreadystatechange=showRemarks;
		xmlHttpone.open("GET",url,true);
		xmlHttpone.send(null);
		}
	}
	function showRemarks()
	 {
		if(xmlHttpone.readyState==4 || xmlHttpone.readyState=="complete")
		 {
			var val=xmlHttpone.responseText;
			//alert(val);
			if(val!="")
			 {
				var textpara = val + "\n\n" + "not purchased last 2 weeks";
				alert(textpara);	
			 }
		}
	 }
	function remarks_other(remarksval)
	{
		if(remarksval=='other')
		{
				document.getElementById("remarks_tr").style.display='';
		}
		else
		{
			document.getElementById("remarks_tr").style.display='none';
		}
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
		$customer_code=$_REQUEST['customer_code'];
		$remarks=$_REQUEST['remarks'];
		if($remarks=='other')
		{
			$d_instruction=$_REQUEST['other_remarks'];
		}
		else $d_instruction=$_REQUEST['remarks'];
		$sqlproduct="SELECT PM.prod_desc,MRP.mrp,PM.prod_code FROM product_master PM,mrp MRP  WHERE PM.prod_code=MRP.product_code 
											ORDER BY PM.prod_desc ASC";
		$rsproduct=mysql_query($sqlproduct);
		while($rowproduct=mysql_fetch_array($rsproduct))
		{
		    $prod_code=$rowproduct['prod_code'];
			
			if($_REQUEST['qty_'.$prod_code] >0)
			{
			$sqlorder  = "insert into prev_order_counting_master ";
			$sqlorder .= " SET product_code='".$prod_code."'";
			$sqlorder .= " ,visit_qty='".$_REQUEST['qty_'.$prod_code]."'";
			$sqlorder .= " ,visit_date='".date('Y-m-d H:i:s')."'";
			$sqlorder .= " ,customer_code='".$customer_code."'";
			$sqlorder .= " , cust_type='R'";
			$sqlorder .= " , d_instruction='".$d_instruction."'";
			$sqlorder .= " , download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlorder); 
			}
		}
		header("location:adminOrderSubmit.php?mod=succ");
		exit();
		disphtml("main();");
}
?>