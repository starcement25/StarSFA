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
			<form name="frmadd" method="post" action="adminTelecallerSubmitmod.php" onsubmit="return check();" >
			<input type="hidden" name="mode" value="add">			
			<table width="65%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="center">Telecaller Form</td>
				</tr>
				<!--tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandatory.</td>
				</tr-->
                <tr id="remarks_tr" >
                 <td width="35%" align="right" valign="top" class="tbllogin">Customer</td>
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
                  <td width="35%" align="right" valign="top" class="tbllogin">Connected</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="is_connected" id="is_connected" class="inplogin" 
                    value="yes" onChange="javascript:sel_connected('yes');"/>Yes
                    <input type="radio" name="is_connected" id="is_connected" class="inplogin" 
                    value="no" onChange="javascript:sel_connected('no');"/>No
                    </td>
                 </tr>
                  <tr><td colspan="3">
                 <table id="show_reason" style="display: none; padding-left: 30px;">
                 <tr ><td width="35%" align="right" valign="top" class="tbllogin">Reason</td>
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
                 <table id="connected_details" style="display: none;padding-left: 75px;" cellpadding="5" cellspacing="2">
                
                 <tr><td width="35%" align="right" valign="top" class="tbllogin">Existing Product</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left"><div id="product_sel_div">
				<?php
               /* $sqlproduct="SELECT product,brand,life_of_product FROM  tent_form_product_details WHERE interested_for_demo='no' 
								UNION  
								SELECT customer_name,mobile_no FROM  knocking_form_details WHERE interested_for_demo='no' 
								ORDER BY customer_name ASC
								";
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
                
                  /*$select_control.='
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
                //echo $select_control.='</table>';
                ?></div>
    </td></tr>
                  <tr><td width="35%" align="right" valign="top" class="tbllogin">Demo Achieved</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="radio" name="demo_appointment_taken" id="demo_appointment_taken" class="inplogin" 
                    value="yes" onChange="javascript:sel_demo_ach('yes');"/>Yes
                    <input type="radio" name="demo_appointment_taken" id="demo_appointment_taken" class="inplogin" 
                    value="no" onChange="javascript:sel_demo_ach('no');"/>No
                    </td>
                 </tr>
                 
                 <tr><td colspan="3">
                 <table id="show_demo_yes" style="display: none;" cellpadding="5" cellspacing="2">
                  <tr><td width="35%" align="right" valign="top" class="tbllogin">Demo appointment Date/Time</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <input type="datetime-local" name="demo_appointment_datetime" id="demo_appointment_datetime" class="inplogin" 
                    value="" />
                    </td>
                 </tr>
                  <tr ><td width="35%" align="right" valign="top" class="tbllogin">Allocation To</td>
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
                 </table></td></tr>
                 <tr><td colspan="3">
                 <table id="show_demo_no" style="display: none;" cellpadding="5" cellspacing="2">
                 <tr ><td width="35%" align="right" valign="top" class="tbllogin">Next Appointment date / time</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="datetime-local" name="next_appointment_date_time" id="next_appointment_date_time" class="inplogin" 
                    value="<?php echo $next_appointment_date_time;?>" />
                    </td>
                 </tr>
                 </table></td></tr>
                 
                 <tr><td width="35%" align="right" valign="top" class="tbllogin">Interested for Service</td>
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
                 <tr ><td width="35%" align="right" valign="top" class="tbllogin">Next calling date / time</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="datetime-local" name="next_calling_datetime" id="next_calling_datetime" class="inplogin" 
                    value="<?php echo $next_calling_date_time;?>" />
                    </td>
                 </tr>
                 </table></td></tr>
                 <tr><td colspan="3">
                 <table id="show_interest" style="display: none;padding-left: 35px;" cellpadding="5" cellspacing="2">
                 <tr ><td width="35%" align="right" valign="top" class="tbllogin">Capture Type of Service</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="text" name="service_type" id="service_type" class="inplogin"  value="" />
                    </td>
                 </tr>
                 <tr ><td width="35%" align="right" valign="top" class="tbllogin">Amount to be collected</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="text" name="collected_amount" id="collected_amount" class="inplogin"  value="" />
                    </td>
                 </tr>
                  <?php //$cur_date=date('Y-m-d');
				 		//$next_due_date=date('Y-m-d',strtotime($cur_date. '+3months'));
				?>
                 <tr ><td width="35%" align="right" valign="top" class="tbllogin">Service date</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                     <input type="date" name="service_date" id="service_date" class="inplogin" 
                    value="" />
                    </td>
                 </tr>
                 </table></td></tr>
                  <tr id="remarks_tr" >
                 <td width="35%" align="right" valign="top" class="tbllogin">Remarks</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width=""><input type="text" name="other_remarks" id="other_remarks" class="inplogin" style="width:350px;height:30px;" 	
                            value="<?php echo $_REQUEST['other_remarks'];?>"/>&nbsp;<span class="error"><?php echo $otherremarkserror;?></span></td>
                 </tr>
                 
                 </table><td></td>
                   
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Add " class="inplogin" ></td>
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
	function select_product(){
		var cust_mobile=document.getElementById("customer_info").value;
		document.getElementById("product_sel_div").style.display='';
		document.getElementById("product_sel_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_product_related_data.php?cust_mobile='+cust_mobile+'&mode=producttent','product_sel_div',0);
		//document.getElementById('orderamount').innerHTML=0;
		//document.getElementById('total_amount').value=0;
	}
	function check()
	{
		if(document.getElementById("customer_info").value=="")
		{
			alert("Please Select customer");
			document.getElementById("customer_info").focus();
			return false;
		}
		return true;
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
			select_product();
		}
	}
	function sel_demo_ach(is_achieved)
	{
		if(is_achieved=='no')
		{
			document.getElementById("show_demo_no").style.display='block';
			document.getElementById("show_demo_yes").style.display='none';
		}
		else
		{
			document.getElementById("show_demo_no").style.display='none';
			document.getElementById("show_demo_yes").style.display='block';
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
		
		$telecaller_form_id='TC'.strtoupper($_SESSION['admin_login']).$year.$month.$date.$hour.$minute.$second;
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
		$next_appointment_date_time=$_REQUEST['next_appointment_date_time'];
		$service_type=$_REQUEST['service_type'];
		$collected_amount=$_REQUEST['collected_amount'];
		$service_date=$_REQUEST['service_date'];
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
							 demo_achieved='".addslashes($demo_appointment_taken)."',
							demo_appointment_datetime='".addslashes($demo_appointment_datetime)."',
							allocated_user='".addslashes($allocated_user)."',
							service_interest='".addslashes($service_interest)."',
							next_appointment_date_time='".addslashes($next_appointment_date_time)."',
							service_type='".addslashes($service_type)."',
							collected_amount='".addslashes($collected_amount)."',
							service_date='".addslashes($service_date)."',
							remarks='".addslashes($remarks)."',
							update_by='".addslashes($update_by)."',
							update_date_time=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlinserttelecallerdetails))
			{
				header("location:adminTelecallerSubmitmod.php?mod=succ");
			}
			else
			{
				header("location:adminTelecallerSubmitmod.php?mod=fail");
			}
		}
		exit();
		disphtml("main();");
}
?>