<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if(strtolower($_SESSION['admin_login'])=='admin' && strtoupper($_SESSION['nick_name'])=='RUPA')
	{
		$employee_code='E0602';
	}
	else if(strtolower($_SESSION['admin_login'])=='admin' && strtoupper($_SESSION['nick_name'])=='ARCHITA')
	{
		$employee_code='E0004';
	}
	else $employee_code=$_SESSION['admin_login'];
	$sqlempphone="SELECT phone_no FROM employee_master WHERE emp_code='".$employee_code."'";
	$rsempphone=mysql_query($sqlempphone);
	$rowempphone=mysql_fetch_array($rsempphone);
	$phone_no=$rowempphone['phone_no'];
	$password="@$phone_no#";
	require_once 'protect.php';
    Protect\with('form.php', $password);
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	//echo $mode = $_REQUEST['mode'];
	if($mode =='editorder')
	{
		disphtml("order_information_edit($_REQUEST[row_id],$_REQUEST[row_id_one]);");
	}
	elseif($mode == 'edit')						   edit_record($_REQUEST['row_id'],$_REQUEST['row_id_one']);
	elseif($mode == 'exitpage')     exit_page();
	else  disphtml("main();");

ob_end_flush();
?>
<?php
function main()
{
  if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}
	$current_date = date('Y-m-d');
	$month_date = date('Y-m');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		//$previous_year = date('Y', strtotime('-1 year'));
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		//$previous_year_date = date('Y-04-01');
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
?>
 <style>
.datatable{
  width:98%;
  table-layout: fixed;
  }
.tbl-header{
  background-color: rgba(255,255,255,0.3);
 }
.tbl-content{
  height:400px;
  overflow-x:auto;
  margin-top: 0px;
  border: 1px solid rgba(255,255,255,0.3);
}
.datatable th{
  padding: 20px 15px;
  text-align: left;
  font-weight: 500;
  font-size: 12px;
  color: #fff;
  text-transform: uppercase;
}
.datatable td{
  padding: 15px;
  text-align: left;
  vertical-align:middle;
  font-weight: 300;
  font-size: 12px;
  color: #000000;
  border-bottom: solid 1px rgba(255,255,255,0.1);
}
/* demo styles */
/* for custom scrollbar for webkit browser*/
::-webkit-scrollbar {
    width: 6px;
} 
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
} 
::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script language="javascript">

function check()
{
	if (document.frmSearch.emp_name.value==0) 
	{
		alert('Please select a employee.');
		document.frmSearch.emp_name.focus();
		return false;
	}
	/*if(document.frmSearch.from_date.value.search(/\S/)==0)
	{
		if(document.frmSearch.to_date.value.search(/\S/)==-1)
		{
			alert('Please input a value for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}*/
	return true;
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Order Edit</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="40%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
					<input type="hidden" name="mode" value="">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="40%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        <?php 
							$onclickvertical = "vertical_state(this.value);";
							$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Cost Center:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
								$sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(vertical_value, ',', -1) as distinct_vertical_value 
								FROM employee_master WHERE acedns='Y' AND SUBSTRING_INDEX( vertical_value, ',', -1 ) != '' ".$emp_hierarchy_condition." 
								ORDER BY SUBSTRING_INDEX(vertical_value, ',', -1) ASC";
								$res_vertical = mysql_query($sql_vertical);

								$vertical_select_control = "<select name=\"vertical\" id=\"vertical\" onchange=\"".$onclickvertical."\">";
								$vertical_select_control .= "<option value=\"\">Select</option>";
								//$vertical_select_control .= "<option value=\"all\">All</option>";
								$dist_vertical_val_array=array();
								while($row_vertical = mysql_fetch_array($res_vertical)){
									$dist_vert_value = trim($row_vertical['distinct_vertical_value']);
									if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
										$pos = substr($dist_vert_value,0,1);
										if($pos == 'M'){
											$dist_vert_value = 'MACROMAN';
										}
									}
									if(!in_array($dist_vert_value,$dist_vertical_val_array))
									{
										$vertical_select_control .= "<option value=\"'".$dist_vert_value."'\">".$dist_vert_value."</option>";
										array_push($dist_vertical_val_array,$dist_vert_value);
									}
									$vertical_string .= "'".$dist_vert_value."',";
								}
								$vertical_string = rtrim($vertical_string,",");
								$vertical_select_control .= "</select>";
								$table_data .= $vertical_select_control;
								echo $table_data .= "</td></tr>";
								$table_data_state .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">State:</td>";
								$table_data_state .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
													<div id=\"state_select_div\"></div></td></tr>";
								echo $table_data_state .= "<td>";
								$table_data_emp .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">Employee:</td>";
								echo $table_data_emp .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
													<div id=\"emp_select_div\"></div></td></tr>";
													
								$create_control = "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Month:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\"><select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
								$sql_month_selection = "SELECT DISTINCT DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')AS distinct_datetime 
														FROM order_header WHERE 
													DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') >='".$previous_year_date."' AND 
													DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')  <='".$current_date."' 
													GROUP BY SUBSTRING(order_no,-14,6) ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') ASC ";
								$res_month_selection = mysql_query($sql_month_selection);
								while($row_month_selection = mysql_fetch_array($res_month_selection)){
									$distinct_date = $row_month_selection['distinct_datetime'];
									$year_month_split = explode("-",$distinct_date);
									$monthNum  = $year_month_split[1];
									$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
									$create_control .= "<option value=\"".$distinct_date."\">".$monthName."-".$year_month_split[0]."</option>";
								}
								echo $create_control .= "</select></td></tr>";
						?>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" name="submit" onClick="display_result();">
                                    <input name="btnexit" type="button" class="inplogin" value="Exit" onClick="javascript:exit_page();"> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                      <form name="frm_opts" action="adminOrderEdit.php" method="post" >
                        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
                        <input type="hidden" name="row_id" value="">
                        <input type="hidden" name="row_id_one" value="">
                    </form>
                     </table> 
                     <br />
                     <center>
       <div id="display" style="max-height: 300px; max-width:1300px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:800px;"  align="center" style="display:''"></div><br />
    <!--div style="width:100%;" align="center" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
</center>
			   <script language="javascript" type="text/javascript">
			   function exit_page()
				{
					//alert("ok");
					document.frm_opts.mode.value="exitpage";
					document.frm_opts.submit();
				}

			   function update_result(prod_code,order_no)
				{
					if(document.getElementById("qty").value.search(/\S/)==-1)
					{
						alert("Please input Qty value");
						form.qty.focus();
						return false;
					}
					if(document.getElementById("sale_rate").value.search(/\S/)==-1)
					{
						alert("Please input Rate value");
						form.sale_rate.focus();
						return false;
					}
					var vertical = document.getElementById("vertical").value;
					var state = document.getElementById("state").value;
					var employee = document.getElementById("employee").value;
					//var frequency = document.getElementById("frequency").value;
					
					//document.getElementById("display_details").innerHTML = '';
					var month = document.getElementById("month_select").value;
					var qty = document.getElementById("qty").value;
					var sale_rate = document.getElementById("sale_rate").value;
					document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
					GenericAjaxFunction('order_update.php?employee='+employee+'&vertical='+vertical+'&state='+state+'&month='+month+'&qty='+qty+'&sale_rate='+sale_rate+'&prod_code='+prod_code+'&order_no='+order_no,'display',0);
				}

			   function access_add_edit(prod_code,order_no)
				{
					//alert();
					//document.frm_opts.mode.value='editorder';
					document.getElementById("display_details").style.display = '';
					document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
					GenericAjaxFunction('order_edit_html.php?prod_code='+prod_code+'&order_no='+order_no,'display_details',0);
                	//document.getElementById("print_export").hidden = false;
					document.getElementById("display_details").focus();
					//document.getElementById("display_details").scrollTop=document.getElementById("display").scrollHeight;
				}
                function vertical_state(vertical){
                    if(document.getElementById("vertical").value.search(/\S/) == -1)
                        return false;
                    var vertical = encodeURIComponent(vertical);
                    document.getElementById("state_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?vertical='+vertical+'&type=state','state_select_div',0);
                }
                function state_emp(state){
					if(document.getElementById("vertical").value.search(/\S/) == -1)
                        return false;
                    if(document.getElementById("state").value.search(/\S/) == -1)
                        return false;
                    var state = encodeURIComponent(state);
					var vertical = encodeURIComponent(document.getElementById("vertical").value);
                    document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?state='+state+'&type=emp&vertical='+vertical,'emp_select_div',0);
                }
                function display_result(){
                if(document.getElementById("vertical").value.search(/\S/) == -1){
                    alert('Please Select Vertical');
                    return false;
                }
                if(document.getElementById("state").value.search(/\S/) == -1){
                    alert('Please Select State');
                    return false;
                }
                if(document.getElementById("employee").value.search(/\S/) == -1){
                    alert('Please Select Employee');
                    return false;
                }
				if(document.getElementById("month_select").value.search(/\S/) == -1){
                    alert('Please Select Month');
                    return false;
                }
                var vertical = document.getElementById("vertical").value;
                var state = document.getElementById("state").value;
                var employee = document.getElementById("employee").value;
                //var frequency = document.getElementById("frequency").value;
                
                document.getElementById("display_details").innerHTML = '';
				document.getElementById("display_details").style.display = 'none';
				var month = document.getElementById("month_select").value;
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
				GenericAjaxFunction('order_edit_data.php?employee='+employee+'&vertical='+vertical+'&state='+state+'&month='+month,'display',0);
                document.getElementById("print_export").hidden = false;
            }
	function PrintElem(elem)
	   {
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	   }

	function Popup(data) 
	{
		var mywindow = window.open('', 'Customer Visit Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Customer Visit Report</title>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		mywindow.document.write('</head><body >');
		mywindow.document.write(data);
		mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
	}
	
	/*function exporttocsv(divid)
	{
		var get_report_name = document.getElementById("report_name").value
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var a = document.createElement('a');
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		a.download = 'Customer Visit Report' + postfix + '.xls';
		a.click();
	}*/
function exporttocsv()
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Order data' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
}
        </script>
        <br />
 <?php
}// end main
function order_information_edit($row_id,$row_id_one)
{
   	 if(modified_customer_emp_route=='yes'){
		echo $sqlorderdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no,PM.prod_desc,
		OD.qty,OD.sale_rate,OD.amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.order_no,PM.prod_code
		FROM customer_master CM,customer_route_emp_relation CRR,route_master RM,employee_master EM,product_master PM,order_details OD,
		order_header OH WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CRR.emp_code=EM.emp_code AND
		OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND PM.prod_code=OD.sku_code  AND OD.order_no='".$row_id_one."' AND OD.sku_code='".$row_id."'";	 }
	 else
	 {
		$sqlorderdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no,PM.prod_desc,
		OD.qty,OD.sale_rate,OD.amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.order_no,PM.prod_code
		FROM customer_master CM,route_master RM,employee_master EM,product_master PM,order_details OD,
		order_header OH WHERE CM.route_code=RM.route_code AND CM.emp_code=EM.emp_code AND
		OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND PM.prod_code=OD.sku_code  
		AND OD.order_no='".$row_id_one."' AND OD.sku_code='".$row_id."'";
	 }
	 $resorderdetails = mysql_query($sqlorderdetails);
	 $roworderdetails = mysql_fetch_array($resorderdetails);
?>	
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.qty.value.search(/\S/)==-1)
	{
		alert("Please input Qty value");
		form.qty.focus();
		return false;
	}
	if(form.sale_rate.value.search(/\S/)==-1)
	{
		alert("Please input Rate value");
		form.sale_rate.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Order Edit</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminOrderEdit.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="edit">	
             <input type="hidden" name="row_id" value="<?=$row_id?>" >	
             <input type="hidden" name="row_id_one" value="<?=$row_id_one?>" >		
	
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Edit Order of "<?php echo $roworderdetails['customer_name'];?>" for product 
                  "<?php echo $roworderdetails['prod_desc'];?>"</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }?>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Qty<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="qty" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $roworderdetails['qty'];?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Rate<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="sale_rate" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $roworderdetails['sale_rate'];?>"/></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Edit " class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
}//
function edit_record($row_id,$row_id_one)
{
	$qty = $_REQUEST['qty'];
	$sale_rate=$_REQUEST['sale_rate'];
	
	$upd_sql="UPDATE order_details SET qty ='".addslashes($qty)."',
			 sale_rate='".addslashes($sale_rate)."',
			 amount='".($qty*$sale_rate)."'
			 WHERE order_no = '" .$row_id_one."' AND sku_code='".$row_id."'";
	mysql_query($upd_sql) or die(mysql_error()." Error in customer updation.");

	$GLOBALS['err_msg']="Order information has been edited successfully.";
	disphtml("main();");
}
function exit_page()
{
	$scope = current_url();
	$session_key = 'password_protect_'.preg_replace('/\W+/', '_', $scope);
    $_SESSION[$session_key] = '';
	header("location:adminMain.php");
}
function current_url($script_only=false) {
  $protocol = 'http';
  $port = ':'.$_SERVER["SERVER_PORT"];
  if($_SERVER["HTTPS"] == 'on') $protocol .= 's';
  if($protocol == 'http' && $port == ':80') $port = '';
  if($protocol == 'https' && $port == ':443') $port = '';
  $path = $script_only ? $_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_URI'];
  return "$protocol://$_SERVER[SERVER_NAME]$port$path";
}
?>
