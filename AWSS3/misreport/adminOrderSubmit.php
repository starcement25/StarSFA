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
		if($_REQUEST['mod']=="fail"){?>
        <tr>
            <td align="center"  class="ERR"><strong><font color="#FF0000">Please choose proper customer and product.</font></strong></td>
        </tr>
        <?php }
        ?>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminOrderSubmit.php" >
			<input type="hidden" name="mode" value="add">			
			<table width="65%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="6" align="left">Order Submit</td>
				</tr>
				<!--tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandatory.</td>
				</tr-->
                 <tr >
                 <td width="15%" align="right" valign="top" class="tbllogin">Phone No</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width="">
                 <input type="text" name="phone_no" id="phone_no" class="inplogin phone_no" style="width:200px;height:20px;" 	
                            value="<?php echo $_REQUEST['phone_no'];?>"  />&nbsp;<span class="error"><?php echo $phoneerror;?></span></td>
                 </tr> 
                  <tr>
                  		<td width="15%" align="right" valign="top" class="tbllogin"></td>
                         <td width="1%" align="left" valign="top" class="tbllogin"></td>
                        <td align="left"   valign="top"  width=""><div id="retailer_info_sel_div">
                        </div>
                     </td>
                   </tr>   
                 <tr><td width="15%" align="right" valign="top" class="tbllogin">Customer Info Type</td>
					<td width="5%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left">
                    <!--input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" value="last_days_1" onChange="javascript:sel_cust_info('last_days_1');"/>Last 10 Days!-->
                    <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="target_ach" onChange="javascript:sel_cust_info('target_ach');"/>Target Vs Achievement
                    <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="last_days_2" onChange="javascript:sel_cust_info('last_days_2');"/>Last 2 Weeks
                    <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="last_3_months" onChange="javascript:sel_cust_info('last_3_months');"/>Last 3 Months
                    <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="proposed_sku" onChange="javascript:sel_cust_info('proposed_sku');"/>Top 10 Proposed SKU
                     <input type="radio" name="customer_info_type" id="customer_info_type" class="inplogin" 
                    value="focused_product" onChange="javascript:sel_cust_info('focused_product');"/>Focus Product
                    
                    </td>
                 </tr>
                 <tr >
               	<td width="15%" align="right" valign="top" class="tbllogin"></td>
                 <td width="1%" align="left" valign="top" class="tbllogin"></td>
                <td align="left"   valign="top"  width="">
                    <div id="customer_info_div" width="70%" ></div>
                 </td>
                 </tr>
                 <tr>
                 	<td width="15%" align="right" valign="top" class="tbllogin">Brand</td>
                    <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 	<td align="left" valign="top" width="">
                        <select name="product_group_code" id="product_group_code" onChange="javascript:sel_prod_sub_group(this.value);">
                            <option value="">SELECT</option>
                            <?php 
                            $sqlqueryproductgroup="SELECT product_group_code,product_group_name FROM product_group_master where 1 ORDER BY product_group_name ASC";
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
                        }
                        ?>	
                       </select>
                       </td>
                 </tr>
                   <tr>
                 	<td width="15%" align="right" valign="top" class="tbllogin">Brand Form</td>
                    <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 	<td align="left" valign="top" width="">
                       <div id="sub_group_sel_div"></div>
                       </td>
                 </tr>        
                 
                <tr >
                <td width="15%" align="right" valign="top" class="tbllogin"></td>
                 <td width="1%" align="left" valign="top" class="tbllogin"></td>
                <td align="left"   valign="top"  width="">
                    	  <!--tr>
                                <td align="left" width="50%"><b>Brand</b>
                                <br />
                                <select name="product_group_code" id="product_group_code" onChange="javascript:sel_prod_sub_group(this.value);">
                            	<option value="">SELECT</option>
								<?php 
                                /*$sqlqueryproductgroup="SELECT product_group_code,product_group_name FROM product_group_master where 1 ORDER BY product_group_name ASC";
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
                        	</select>
                                </td>
                                <td align="left" colspan="2" width="50%"><b>Brand Form</b><div id="sub_group_sel_div">
                                </div></td>
                             </tr>
                          <tr!-->
                          <div id="product_sel_div"></div>
                            
                        <?php 
                        /*$sqlproduct="SELECT PM.prod_desc,MRP.mrp,PM.prod_code FROM product_master PM,mrp MRP  WHERE PM.prod_code=MRP.product_code 
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
                        }*/
                    ?>
                </td>
              </tr>
              <tr  >
                 <td width="15%" align="right" valign="top" class="tbllogin">Order Amount</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width="" id="orderamount" style="font-weight: bold;"></td><input type="hidden" id="total_amount" value="" />
               </tr>
              <?php
			  	$sqlcatalogue="SELECT vertical,file_name,file_version FROM catalogue_info WHERE 1 ORDER BY download_time DESC";
				$resultcatalogue = mysql_query($sqlcatalogue);
				$countcatalogue=mysql_num_rows($resultcatalogue);
				if($countcatalogue>0){
					$rowcatalogue = mysql_fetch_array($resultcatalogue);
					$rowcatalogue['file_name'];
					$catalogue="<a href=\"http://salesmpower.acedns.in/catalogue/".strtoupper($_SESSION['nick_name'])."/$rowcatalogue[file_name]\" style=\"color: red;font-weight:bold;\" target=\"_blank\">Click Here</a>";
				}
				else
				{
					$catalogue='N/A';
				}
				$sqlscheme="SELECT PDF_file_name FROM branch_schemes_PDF WHERE 1 ORDER BY download_time DESC";
				$resultscheme = mysql_query($sqlscheme);
				$countscheme=mysql_num_rows($resultscheme);
				if($countscheme>0){
					$rowscheme = mysql_fetch_array($resultscheme);
					$scheme="<a href=\"http://salesmpower.acedns.in/schemes/$rowscheme[PDF_file_name]\" style=\"color:green;font-weight:bold;\" target=\"_blank\">Click Here</a>";
				}
				else
				{
					$scheme='N/A';
				}
			  ?>
               <tr id="remarks_tr" >
                 <td width="15%" align="right" valign="top" class="tbllogin">Catalogue</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width=""><?php echo $catalogue;?></td>
                 </tr>
                <tr id="remarks_tr" >
                 <td width="15%" align="right" valign="top" class="tbllogin">Scheme</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width="">
                 <select name="branch_scheme" id="branch_scheme" onchange="javascript:open_file();">
                 <option value="">SELECT</option>
				 <?php //echo $scheme;
				 	$sqlcheckscheme="SELECT scheme_name,PDF_file_name FROM branch_schemes_PDF WHERE acedns='Y'";
					$rscheckscheme=mysql_query($sqlcheckscheme);
					while($rowcheckscheme=mysql_fetch_array($rscheckscheme))
					{
				 ?>
                 	<option value="<?php echo $rowcheckscheme['PDF_file_name']?>"><?php echo $rowcheckscheme['scheme_name']?></option>
                 <?php }?>
                  </select>
                 </td>
                 </tr>
               <tr><td width="15%" align="right" valign="top" class="tbllogin">Disposition</td>
					<td width="1%" align="left" valign="top" class="tbllogin">:</td>
					<td  valign="top"  align="left" width="">
                    <?php
					
					$disposition_array=array('Order Taken','Last Order Delivery Pending','Recently Received Material','Order Expected Next Week',
					'Replacement/Service Issue','Not Interested Due To Brand','Shop Closed/Owner Not Available','End Customer','Not connected');
					$select_control="<select name=\"d_instruction\" id=\"d_instruction\" class=\"inplogin\">";
					$select_control .= "<option value=\"\">Select</option>";
					//echo "<option value=\"all\">All</option>";
					/*$sql_branch = "SELECT customer_name,customer_code FROM customer_master WHERE customer_code IN (".$distributor_string_logic.") ORDER BY customer_name ASC";
					$res_branch = mysql_query($sql_branch);
					while($row_branch = mysql_fetch_array($res_branch)){
						$customer_code = $row_branch['customer_code'];
						$customer_name = $row_branch['customer_name'];
						$select_control_option .="<option value=\"".$customer_code."\">".$customer_name."</option>";
					}*/
					foreach($disposition_array as $disposition_val)
					{
						$select_control_option .="<option value=\"".$disposition_val."\">".$disposition_val."</option>";
					}
						$select_control .= $select_control_option;
						$select_control .="</select>";
					echo $select_control;
					?>
                    </td>
                 </tr>
                 <tr id="remarks_tr" >
                 <td width="15%" align="right" valign="top" class="tbllogin">Remarks</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width=""><input type="text" name="other_remarks" id="other_remarks" class="inplogin" style="width:350px;height:30px;" 	
                            value="<?php echo $_REQUEST['other_remarks'];?>"/>&nbsp;<span class="error"><?php echo $otherremarkserror;?></span></td>
                 </tr>
                 <tr >
                 <td width="15%" align="right" valign="top" class="tbllogin">Issue</td>
                       <td width="1%" align="left" valign="top" class="tbllogin">:</td>
                 <td align="left" valign="top" width=""><input type="text" name="remarks_issue" id="remarks_issue" class="inplogin" style="width:350px;height:30px;" 	
                            value="<?php echo $_REQUEST['remarks_issue'];?>"/>&nbsp;<span class="error"><?php echo $remarksissueerror;?></span></td>
                 </tr>      
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
	//onblur="javascript:show_retailer_info(this.value);"
	$(function(){
		/*$(".phone_no").on("keyup",function (event) {
		$(this).val($(this).val().replace(/[^0-9\.]/g,''));
			if (event.which == 13 && $(this).val().indexOf('.') != -1) {
			//event.preventDefault();
				show_retailer_info(this.value);
			}
			})*/
		 /*var toSubmit = function() {
			var text = $(".phone_no").val();
			alert(text);
			show_retailer_info(text);
		  };*/
		
		  var enterPressed = function() {
			//$('.phone_no').keyup(function(event) {
			$('.phone_no').keypress(function(event) {	
			  //if(event.which == 13) {
				//toSubmit();
				var text = $(".phone_no").val();
					text=text+(String.fromCharCode(event.keyCode));
				show_retailer_info(text);
			  //}
			});
		  };
		enterPressed();	
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
</script>
	<?php
}//End of main()
function add_record()
{
	/*echo '<pre>';
		print_r($_REQUEST);
	echo '</pre>';*/
		$customer_code=$_REQUEST['customer_code'];
		if($customer_code !='')
		{
			$emp_code=$_REQUEST['emp_code'];
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$order_no='O'.$emp_code.$year.$month.$date.$hour.$minute.$second;
		  $sqlbranches="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
		  $rsbranches=mysql_query($sqlbranches);
		  $rowbranches=mysql_fetch_array($rsbranches);
		  $branch_code=$rowbranches['branch_code'];
		$d_instruction=$_REQUEST['d_instruction'];
		$other_remarks=$_REQUEST['other_remarks'];
		$remarks_issue=$_REQUEST['remarks_issue'];
		$sqlproduct="SELECT PM.prod_desc,MRP.sale_rate,PM.prod_code FROM product_master PM,mrp MRP  WHERE PM.prod_code=MRP.product_code 
											AND MRP.branch_code='".$branch_code."' ORDER BY PM.prod_desc ASC";
											//exit();
		$rsproduct=mysql_query($sqlproduct);
		while($rowproduct=mysql_fetch_array($rsproduct))
		{
		    $prod_code=$rowproduct['prod_code'];
			
			if($_REQUEST['qty_'.$prod_code] >0)
			{
				$rate=$_REQUEST['rate_'.$prod_code];
				$amount=$_REQUEST['qty_'.$prod_code]*$rate;
			$sqlorder  = "insert into prev_order_counting_master ";
			$sqlorder .= " SET product_code='".$prod_code."'";
			$sqlorder .= "  ,order_no='".$order_no."'";
			$sqlorder .= " ,visit_qty='".$_REQUEST['qty_'.$prod_code]."'";
			$sqlorder .= " ,visit_date='".date('Y-m-d H:i:s')."'";
			$sqlorder .= " ,customer_code='".$customer_code."'";
			$sqlorder .= " , cust_type='R'";
			$sqlorder .= " , d_instruction='".$d_instruction."'";
			$sqlorder .= " , remarks='".$other_remarks."'";
			$sqlorder .= " , hint_remarks='".$remarks_issue."'";
			$sqlorder .= " , rate='".$_REQUEST['rate_'.$prod_code]."'";
			$sqlorder .= " , amount='".$amount."'";
			$sqlorder .= " , creation_type='callcenter'";
			$sqlorder .= " , download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlorder); 
			}
		}
		header("location:adminOrderSubmit.php?mod=succ");
		}
		else
		{
			header("location:adminOrderSubmit.php?mod=fail");
		}
		exit();
		disphtml("main();");
}
?>