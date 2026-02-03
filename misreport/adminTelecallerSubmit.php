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
	/*$customercodeerror='';
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
	$error_array=array();*/
$mode = $_REQUEST['mode'];
	if($mode == 'add')
	{
		add_record();
		
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong>Tele caller Details</strong></td>
	</tr>
    <?php if($_REQUEST['mod']=="succ"){?>
        <tr>
            <td align="center"  class="ERR"><strong><font color="#FF0000">Tele caller details submitted successfully.</font></strong></td>
        </tr>
        <?php }
		if($_REQUEST['mod']=="fail"){?>
        <tr>
            <td align="center"  class="ERR"><strong><font color="#FF0000">Please choose proper customer</font></strong></td>
        </tr>
        <?php }
        ?>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminTelecallerSubmit.php" >
			<input type="hidden" name="mode" value="add">			
			<table width="65%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="center">Telecaller Form</td>
				</tr>
				<!--tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandatory.</td>
				</tr-->
                <tr id="remarks_tr" >
                 <td width="25%" align="right" valign="top" class="tbllogin">Customer</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width="">
                 <select name="customer_info" id="customer_info" >
                 <option value="">SELECT</option>
				 <?php 
				 	$sqlcustomer="SELECT customer_name,mobile_no FROM  tent_form_details WHERE interested_for_demo='no' 
								UNION  
								SELECT customer_name,mobile_no FROM  knocking_form_details WHERE interested_for_demo='no' 
								ORDER BY customer_name ASC
								";
					$rscustomer=mysqli_query($link,$sqlcustomer);
					while($rowcustomer=mysqli_fetch_assoc($rscustomer))
					{
				 ?>
                 	<option value="<?php echo $rowcustomer['mobile_no']?>"><?php echo $rowcustomer['customer_name']?> - <?php echo $rowcustomer['mobile_no']?></option>
                 <?php }?>
                  </select>&nbsp;<span class="error"><?php echo $customererror;?></span>
                 </td>
                 </tr>
                 
                 <tr id="show_connected" hidden>
                  <td width="25%" align="right" valign="top" class="tbllogin">Connected</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="is_connected" id="is_connected" class="inplogin" 
                    value="yes" onChange="javascript:sel_connected('yes');"/>Yes
                    <input type="radio" name="is_connected" id="is_connected" class="inplogin" 
                    value="no" onChange="javascript:sel_connected('no');"/>No
                    </td>
                 </tr>
                  <tr><td colspan="3">
                 <table id="show_reason" style="display: none; padding-left: 50px;">
                 <tr ><td width="25%" align="right" valign="top" class="tbllogin">Reason</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="not_connected_reason" id="not_connected_reason" class="inplogin" 
                    value="Switch off" />Switch off
                    <input type="radio" name="not_connected_reason" id="not_connected_reason" class="inplogin" 
                    value="Ringing" />Ringing
                    <input type="radio" name="not_connected_reason" id="not_connected_reason" class="inplogin" 
                    value="Wrong number" />Wrong number
                    <input type="radio" name="not_connected_reason" id="not_connected_reason" class="inplogin" 
                    value="Not reachable" />Not reachable
                     <input type="radio" name="not_connected_reason" id="not_connected_reason" class="inplogin" 
                    value="Disconnected" />Disconnected
                    </td>
                 </tr>
                 </table></td></tr>
                 <tr><td colspan="3">
                 <table id="connected_details" style="display: none;padding-left: 5px;" cellpadding="5" cellspacing="2">
                
                 <tr><td width="25%" align="right" valign="top" class="tbllogin">Existing Product</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
				<?php
                /* $sqlquery="SELECT PM.prod_desc,PM.prod_code,PM.pack_size
                FROM product_master PM WHERE focus='Y' ORDER BY PM.prod_desc ASC";
                $result = mysqli_query($link,$sqlquery);
                $count=mysqli_num_rows($result);*/
                $select_control='<table cellpadding="5" cellspacing="2"  align="left" class="border" style="height: 200px;overflow-y: scroll;display:block;"><tr class="TDHEAD_SUB">
                        <td align="left" width="30%"><b>Product</b></td>
                        <td align="left" width="25%"><b>Brand</b></td>
                        <td align="left" width="45%"><b>Life of Product</b></td>
                     </tr >';
                /*if($count>0){
                while($rowsorderquery = mysqli_fetch_assoc($result))
                {
                $prod_code=$rowsorderquery['prod_code'];
                $prod_desc=$rowsorderquery['prod_desc'];
                $pack_size=$rowsorderquery['pack_size'];
                $sqlmrp="SELECT sale_rate FROM mrp where product_code='".$prod_code."' AND branch_code='".$branch_code."'";
                $rsmrp=mysqli_query($link,$sqlmrp);
                $rowmrp=mysqli_fetch_assoc($rsmrp);
                $mrp=$rowmrp['sale_rate'];*/
                
                  $select_control.='
                     <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
                     onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                        <td align="left">Chimney</td>
                        <td align="left">Test brand</td>
                        <td align="left">Test life</td>
                     </tr>   
                    ';
                /*}
                }
                else
                {
                $select_control.='<tr>
                        <td align="left" colspan="3"><b>No records.</b>
                        </td>
                     </tr>';
                }*/
                echo $select_control.='</table>';
                ?>
    </td></tr>
                  <tr><td width="25%" align="right" valign="top" class="tbllogin">Demo appointment taken</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="demo_appointment_taken" id="demo_appointment_taken" class="inplogin" 
                    value="yes" />Yes
                    <input type="radio" name="demo_appointment_taken" id="demo_appointment_taken" class="inplogin" 
                    value="no" />No
                    </td>
                 </tr>
                  <tr><td width="25%" align="right" valign="top" class="tbllogin">Demo appointment Date/Time</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="datetime-local" name="demo_appointment_datetime" id="demo_appointment_datetime" class="inplogin" 
                    value="" />
                    </td>
                 </tr>
                  <tr ><td width="25%" align="right" valign="top" class="tbllogin">Allocation To</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="allocated_user" id="allocated_user" class="inplogin" 
                    value="GL" />GL
                    <input type="radio" name="allocated_user" id="allocated_user" class="inplogin" 
                    value="Executive" />Executive
                    <input type="radio" name="allocated_user" id="allocated_user" class="inplogin" 
                    value="Business owner" />Business owner 
                    <input type="radio" name="allocated_user" id="allocated_user" class="inplogin" 
                    value="Area head" />Area head
                    </td>
                 </tr>
                 <tr><td width="25%" align="right" valign="top" class="tbllogin">Interested for Service</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="service_interest" id="service_interest" class="inplogin" 
                    value="yes" onChange="javascript:sel_service('yes');"/>Yes
                    <input type="radio" name="service_interest" id="service_interest" class="inplogin" 
                    value="no" onChange="javascript:sel_service('no');"/>No
                    </td>
                 </tr>
                 <tr><td colspan="3">
                 <?php $cur_date=date('Y-m-d');
				 		$cur_time=date('H:i');
				 		$next_calling_date=date('Y-m-d',strtotime($cur_date. '+1months'));
						$next_calling_date_time=$next_calling_date.'T'.$cur_time;
				?>
                 <table id="show_no_interest" style="display: none;padding-left: 35px;" cellpadding="5" cellspacing="2">
                 <tr ><td width="25%" align="right" valign="top" class="tbllogin">Next calling date / time</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="datetime-local" name="next_calling_datetime" id="next_calling_datetime" class="inplogin" 
                    value="<?php echo $next_calling_date_time;?>" />
                    </td>
                 </tr>
                 </table></td></tr>
                 <tr><td colspan="3">
                 <table id="show_interest" style="display: none;padding-left: 35px;" cellpadding="5" cellspacing="2">
                 <tr ><td width="25%" align="right" valign="top" class="tbllogin">Capture Type of Service</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="text" name="service_type" id="service_type" class="inplogin"  value="" />
                    </td>
                 </tr>
                 <tr ><td width="25%" align="right" valign="top" class="tbllogin">Amount to be collected</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="text" name="collected_amount" id="collected_amount" class="inplogin"  value="" />
                    </td>
                 </tr>
                  <?php $cur_date=date('Y-m-d');
				 		$next_due_date=date('Y-m-d',strtotime($cur_date. '+3months'));
				?>
                 <tr ><td width="25%" align="right" valign="top" class="tbllogin">Next due date</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="date" name="next_due_date" id="next_due_date" class="inplogin" 
                    value="<?php echo $next_due_date;?>" />
                    </td>
                 </tr>
                 </table></td></tr>
                  <tr id="remarks_tr" >
                 <td width="25%" align="right" valign="top" class="tbllogin">Remarks</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width=""><input type="text" name="other_remarks" id="other_remarks" class="inplogin" style="width:350px;height:30px;" 	
                            value="<?php echo $_REQUEST['other_remarks'];?>"/>&nbsp;<span class="error"><?php echo $otherremarkserror;?></span></td>
                 </tr>
                 
                 </table><td></td>
                   
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="button" value=" Add " class="inplogin" onclick="document.frmadd.submit();"></td>
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
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
	<script type="text/javascript" src="ajax1.js"></script>
	<script type="text/javascript">
	//onblur="javascript:show_retailer_info(this.value);"
	jQuery(function(){
		
		jQuery("#customer_info").change(function(){	
		jQuery("#show_connected").show();
		});
	});
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
			var emp_code=document.getElementById("emp_code").value;
			//alert(emp_code);
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code+'&emp_code='+emp_code,'customer_info_div',0);
		}
		if(customer_info_val=='proposed_sku')
		{
			var fetchval='10';
			var customer_code=document.getElementById("customer_code").value;
			var distributor=document.getElementById("distributor").value;
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code+'&distributor='+distributor,'customer_info_div',0);
		}
		if(customer_info_val=='last_3_months')
		{
			var fetchval='90';
			var customer_code=document.getElementById("customer_code").value;
			var distributor=document.getElementById("distributor").value;
			document.getElementById("customer_info_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('get_customer_info_data.php?customer_info_val='+customer_info_val+'&fetchval='+fetchval+'&customer_code='+customer_code+'&distributor='+distributor,'customer_info_div',0);
		}
		if(customer_info_val=='target_ach')
		{
			var fetchval='';
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
				//var textpara = val + "\n\n" + "not purchased last 2 weeks";
				var textpara = val;
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

	function sel_prod_sub_group(prod_group_code){
		document.getElementById("sub_group_sel_div").style.display='';
		document.getElementById("sub_group_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?prod_group_code='+prod_group_code+'&mode=productsubgroupsel','sub_group_sel_div',0);
		
	}
	function select_product(prod_group_code,prod_sub_group_code){
		var emp_code=document.getElementById("emp_code").value;
		document.getElementById("product_sel_div").style.display='';
		document.getElementById("product_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?prod_group_code='+prod_group_code+'&prod_sub_group_code='+prod_sub_group_code+'&emp_code='+emp_code+'&mode=productselect','product_sel_div',0);
		//document.getElementById('orderamount').innerHTML=0;
		//document.getElementById('total_amount').value=0;
	}
	function show_retailer_info(phoneval)
	{
		//alert(phoneval);
		document.getElementById("retailer_info_sel_div").style.display='';
		document.getElementById("retailer_info_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_customer_info_data.php?phone='+phoneval+'&customer_info_val=selectretailerinfo','retailer_info_sel_div',0);
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
	function populate_amount(prod_code)
	{
		/*if(event.charCode >= 48 && event.charCode <= 57)
		{*/
		var rate=document.getElementById("rate_"+prod_code).value;
		//alert(rate);
		//alert(event.key);
		//alert(document.getElementById("qty_"+prod_code).value)
		//var value=event.key;
		if(document.getElementById("qty_"+prod_code).value!=''){
		var final_value=document.getElementById("qty_"+prod_code).value;
		}
		else
		{
			var final_value=0;
		}
		var amount=rate*final_value;
		var total_amount=document.getElementById('total_amount').value;
		if(total_amount=='') total_amount=0;
		var final_amount=parseFloat(total_amount)+parseFloat(amount);
		
		document.getElementById('orderamount').innerHTML=final_amount;
		document.getElementById('total_amount').value=final_amount;
		//}
		
	}
	function open_file()
	{
		var urlmenu = document.getElementById('branch_scheme').value;
		urlmenu='http://salesmpower.acedns.in/schemes/'+urlmenu;
      	window.open(urlmenu, '_blank' );
	}
	function sel_connected(is_connected)
	{
		if(is_connected=='no')
		{
			document.getElementById("show_reason").style.display='block';
			document.getElementById("connected_details").style.display='none';
		}
		else
		{
			document.getElementById("show_reason").style.display='none';
			document.getElementById("connected_details").style.display='block';
		}
	}
	function sel_service(is_interested)
	{
		if(is_interested=='no')
		{
			document.getElementById("show_no_interest").style.display='block';
			document.getElementById("show_interest").style.display='none';
		}
		else
		{
			document.getElementById("show_no_interest").style.display='none';
			document.getElementById("show_interest").style.display='block';
		}
	}
</script>
	<?php
}//End of main()
function add_record()
{
	echo '<pre>';
		print_r($_REQUEST);
	echo '</pre>';
	//exit();
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		$telecaller_form_id='TF'.$_SESSION['admin_login'].$year.$month.$date.$hour.$minute.$second;
		$tent_knocking_form_id='';
		$mobile=$_REQUEST['customer_info'];
		$sqlcustomer="SELECT customer_name FROM knocking_form_details WHERE mobile_no='".addslashes($mobile)."'";
		$rscustomer=mysqli_query($link,$sqlcustomer);
		$countcustomer=mysqli_num_rows($rscustomer);
		$rowcustomer=mysqli_fetch_assoc($rscustomer);
		$customer_name=$rowcustomer['customer_name'];
		$is_connected=$_REQUEST['is_connected'];
		$not_connected_reason=$_REQUEST['not_connected_reason'];
		//$demo_existing_product=$input['demo_existing_product'];
		$demo_appointment_taken=$_REQUEST['demo_appointment_taken'];
		$demo_appointment_datetime=$_REQUEST['demo_appointment_datetime'];
		$allocated_user=$_REQUEST['allocated_user'];
		$service_interest=$_REQUEST['service_interest'];
		$next_call_date_time=$_REQUEST['next_calling_date_time'];
		$service_type=$_REQUEST['service_type'];
		$collected_amount=$_REQUEST['collected_amount'];
		$next_due_date=$_REQUEST['next_due_date'];
		//$last_call_date_time=$input['last_call_date_time'];
		//$appointment_date_time=$input['appointment_date_time'];
		$remarks=$_REQUEST['other_remarks'];
		$update_by=$_SESSION['admin_login'];
		
		$sqlchktelecallerform="SELECT telecaller_form_id FROM telecaller_form_details WHERE telecaller_form_id='".addslashes($telecaller_form_id)."'";
		$rschktelecallerform=mysqli_query($link,$sqlchktelecallerform);
		$countchktelecallerform=mysqli_num_rows($rschktelecallerform);
		if($countchktelecallerform==0){
		$sqlinserttelecallerdetails="INSERT INTO telecaller_form_details SET telecaller_form_id='".addslashes($telecaller_form_id)."',
							tent_knocking_form_id='".addslashes($tent_knocking_form_id)."',
							customer_name='".addslashes($customer_name)."',
							mobile='".addslashes($mobile)."',
							is_connected='".addslashes($is_connected)."',
							not_connected_reason='".addslashes($not_connected_reason)."',
							demo_existing_product='".addslashes($demo_existing_product)."',
							 demo_appointment_taken='".addslashes($demo_appointment_taken)."',
							demo_appointment_datetime='".addslashes($demo_appointment_datetime)."',
							allocated_user='".addslashes($allocated_user)."',
							service_interest='".addslashes($service_interest)."',
							next_call_date_time='".addslashes($next_call_date_time)."',
							service_type='".addslashes($service_type)."',
							collected_amount='".addslashes($collected_amount)."',
							next_due_date='".addslashes($next_due_date)."',
							last_call_date_time='".addslashes($last_call_date_time)."',
							appointment_date_time='".addslashes($appointment_date_time)."',
							remarks='".addslashes($remarks)."',
							update_by='".addslashes($update_by)."',
							update_date_time=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlinserttelecallerdetails))
			{
				header("location:adminTelecallerSubmit.php?mod=succ");
			}
			else
			{
				header("location:adminTelecallerSubmit.php?mod=fail");
			}
		}
		exit();
		disphtml("main();");
}
?>