<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	//echo $mode = $_REQUEST['mode'];

	if($mode =='editcustomer')
	{
		disphtml("customer_information_edit($_REQUEST[row_id]);");
	}
	elseif($mode == 'edit')						   edit_record($_REQUEST['row_id']);
	else  disphtml("main();");

ob_end_flush();

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

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
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
	if(document.frmSearch.from_date.value.search(/\S/)==0)
	{
		if(document.frmSearch.to_date.value.search(/\S/)==-1)
		{
			alert('Please input a value for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}
	return true;
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Customer Edit</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="40%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="visit_frequency_display">
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
								FROM employee_master WHERE SUBSTRING_INDEX( vertical_value, ',', -1 ) != '' ".$emp_hierarchy_condition." ORDER BY 
								SUBSTRING_INDEX(vertical_value, ',', -1) ASC";
								$res_vertical = mysqli_query($link,$sql_vertical);

								$vertical_select_control = "<select name=\"vertical\" id=\"vertical\" onchange=\"".$onclickvertical."\">";
								$vertical_select_control .= "<option value=\"\">Select</option>";
								//$vertical_select_control .= "<option value=\"all\">All</option>";
								$dist_vertical_val_array=array();
								while($row_vertical = mysqli_fetch_assoc($res_vertical)){
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
						?>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" name="submit" onClick="display_result();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                      <form name="frm_opts" action="adminCustomerEdit.php" method="post" >
                        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
                        <input type="hidden" name="row_id" value="">
                    </form>
                     </table> 
                     <br />
                     <center>
       <div id="display" style="max-height: 350px; max-width:1300px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <!--div id="display_details" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center"></div><br /-->
    <!--div style="width:100%;" align="center" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
</center>
			   <script language="javascript" type="text/javascript">
			   function access_add_edit(ID)
				{
					//alert();
					document.frm_opts.mode.value='editcustomer';
					document.frm_opts.row_id.value=ID;
					document.frm_opts.submit();
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
                var vertical = document.getElementById("vertical").value;
                var state = document.getElementById("state").value;
                var employee = document.getElementById("employee").value;
                //var frequency = document.getElementById("frequency").value;
                
                //document.getElementById("display_details").innerHTML = '';
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
				GenericAjaxFunction('customer_edit_data.php?employee='+employee+'&vertical='+vertical+'&state='+state,'display',0);
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
	a.download = 'Customer data' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
}
        </script>
        <br />
 <?php
}// end main
function customer_information_edit($row_id)
{
   $sqlcustomerdetails="SELECT customer_name,rds_tag,route_code,phone_no,emp_code
						FROM customer_master WHERE customer_code='".$row_id."'";
	$rescustomerdetails = mysqli_query($link,$sqlcustomerdetails);
	$rowcustomerdetails=mysqli_fetch_assoc($rescustomerdetails);
	$emp_code=$rowcustomerdetails['emp_code'];
?>	
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.customer_name.value.search(/\S/)==-1)
	{
		alert("Please enter Customer Name");
		form.customer_name.focus();
		return false;
	}
	if(form.phone_no.value.search(/\S/)==-1)
	{
		alert("Please enter Customer Phone");
		form.phone_no.focus();
		return false;
	}
	if(form.rds_name.value.search(/\S/)==-1)
	{
		alert("Please Choose Distributor");
		form.rds_name.focus();
		return false;
	}
	if(form.route_name.value.search(/\S/)==-1)
	{
		alert("Please Choose Route");
		form.route_name.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Customer Edit</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminCustomerEdit.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="edit">	
             <input type="hidden" name="row_id" value="<?=$row_id?>" >		
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Edit info of "<?php echo $rowcustomerdetails['customer_name'];?>"</td>
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
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Name<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="customer_name" class="inplogin" style="width:300px;height:30px;" value="<?php echo $rowcustomerdetails['customer_name'];?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Customer Phone<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="phone_no" class="inplogin" style="width:300px;height:30px;" 
                    value="<?php echo $rowcustomerdetails['phone_no'];?>"/></td>
				</tr>
                <tr>
					<td align="right" valign="top" class="tbllogin">Distributor<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="rds_name" id="rds_name" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlquerydistributor="SELECT DISTINCT customer_code,customer_name FROM customer_master WHERE 
													customer_code IN(SELECT DISTINCT rds_tag FROM customer_master WHERE acedns='Y' 
													AND emp_code='".$emp_code."') ORDER BY customer_name ASC";
                                $resultquerydistributor = mysqli_query($link,$sqlquerydistributor);
                                $countquerydistributor=mysqli_num_rows($resultquerydistributor);
                                if($countquerydistributor>0){
                                while($rowquerydistributor = mysqli_fetch_assoc($resultquerydistributor))
                                {
                                ?>
                                <option value="<?php echo $rowquerydistributor['customer_code'];?>" <?php if( $rowcustomerdetails['rds_tag']==$rowquerydistributor['customer_code']){echo 'selected';}?>><?php echo $rowquerydistributor['customer_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>
                    </td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Route<font color="#FF0000"><strong>*</strong></font></td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td  align="left" valign="top"> 
                        <select name="route_name" id="route_name" >
                            <option value="">SELECT</option>
								<?php 
                                $sqlqueryroute="SELECT route_name,route_code FROM route_master WHERE 
													route_code IN(SELECT DISTINCT route_code FROM customer_master WHERE acedns='Y' 
													AND emp_code='".$emp_code."') ORDER BY route_name ASC";
                                $resultqueryroute = mysqli_query($link,$sqlqueryroute);
                                $countqueryroute=mysqli_num_rows($resultqueryroute);
                                if($countqueryroute>0){
                                while($rowqueryroute = mysqli_fetch_assoc($resultqueryroute))
                                {
                                ?>
                                <option value="<?php echo $rowqueryroute['route_code'];?>" <?php if( $rowcustomerdetails['route_code']==$rowqueryroute['route_code']){echo 'selected';}?>><?php echo $rowqueryroute['route_name'];?></option>
                                <?php
                                }
                            }
                            ?>	
                        </select>
                    </td>
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
function edit_record($row_id)
{
	$customer_code=$row_id;
	$customer_name = $_REQUEST['customer_name'];
	$phone_no=$_REQUEST['phone_no'];
	$rds_name=$_REQUEST['rds_name'];
	$route_name=$_REQUEST['route_name'];
	
	$upd_sql="UPDATE customer_master SET customer_name ='".addslashes($customer_name)."',
			 phone_no='".addslashes($phone_no)."',
			 route_code='".addslashes($route_name)."',
			 rds_tag='".addslashes($rds_name)."',
			 download_time=CURRENT_TIMESTAMP()
			 WHERE customer_code = '" .$customer_code."'";
	mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in customer updation.");

	$GLOBALS['err_msg']="Customer information has been edited successfully.";
	disphtml("main();");
}

?>