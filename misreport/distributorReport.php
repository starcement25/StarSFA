<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];

	if(!$_GET)
	{
		disphtml("main();");
	}
	else
	{
		csvexport();
	}
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
<!--link href="Freeze.css" rel="stylesheet" type="text/css" /-->
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Distributor Report</strong></td>
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
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="visit_frequency_display">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        <?php  
							$onclickvertical = "vertical_state(this.value);";
							$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Vertical:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
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
								$table_data_state .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\"><div id=\"state_select_div\"></div></td></tr>";
								echo $table_data_state;
 	  	
						/*$onclick = "state_distributor(this.value);";
						$sql_state = "SELECT DISTINCT state FROM employee_master WHERE state!='' ".$emp_hierarchy_condition." ORDER BY state ASC";
						$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">State:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
						$state_select_control = "<select name=\"state\" id=\"state\" >";
						$state_select_control .= "<option value=\"\">Choose State</option>";
						$res_state = mysqli_query($link,$sql_state);
						while($row_state = mysqli_fetch_assoc($res_state)){
							$state = $row_state['state'];
							$dns_state_code = $row_state['dns_state_code'];
							$state_string .= "'".$dns_state_code."',";
							$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
						}
						$state_string = rtrim($state_string,",");
						//$state_select_control .= "<option value=\"".$state_string."\">All</option>";
						$state_select_control .= $state_select_control_option;
						$state_select_control .= "</select>";
						$table_data .= $state_select_control;
					    echo $table_data .= "</td></tr>";*/
						?>
                        <tr>
                            <td align="center" width="" colspan="4">
                              <div id="date_div" style="width:60%;" >
                                From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
                                To:<input type="date" name="end_date" id="end_date" style="height:20px;" onchange="datewise_distributor();"/>
                                <!--input type="submit" name="submit" value="Submit" onClick="show_emp();" /-->
                                </div>
                                </td>
                          </tr> 
                          <?php     
					   $table_data_dist .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">Distributor:</td>";
					   $table_data_dist .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\"><div id=\"distributor_select_div\"></div></td></tr>";
						echo $table_data_dist .= "<td>";
						$table_data_emp .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">SO:</td>";
						$table_data_emp .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\"><div id=\"emp_select_div\"></div></td></tr>";
						echo $table_data_emp .= "<td>";

							/*$state_select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
							$state_select_control .= "<option value=\"\">Select</option>";
							
							$res_state = mysqli_query($link,$sql_state);
							while($row_state = mysqli_fetch_assoc($res_state)){
								$state = $row_state['state'];
								$state_string .= "'".$state."',";
								$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
							}
							$state_string = rtrim($state_string,",");
							$state_select_control .= "<option value=\"".$state_string."\">All</option>";
							$state_select_control .= $state_select_control_option;
							$state_select_control .= "</select>";
							$table_data .= $state_select_control;
							$table_data .= "</td></tr>";*/
						?>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" name="submit" onclick="display_result();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                     <br />
                     <center>
       <div id="display"  align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <!--div style="width:100%;" align="center" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
</center>
			   <script language="javascript" type="text/javascript">
			   function vertical_state(vertical){
                    if(document.getElementById("vertical").value.search(/\S/) == -1)
                        return false;
                    var vertical = encodeURIComponent(vertical);
                    document.getElementById("state_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?vertical='+vertical+'&type=statehistorical','state_select_div',0);
                }
                function state_distributor(state){
                    if(document.getElementById("state").value.search(/\S/) == -1)
                        return false;
                    var state = encodeURIComponent(state);
					
                    document.getElementById("distributor_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?state='+state+'&type=statedistributor','distributor_select_div',0);
                }
				function datewise_distributor(){
                    if(document.getElementById("state").value.search(/\S/) == -1)
                        return false;
                    var state =document.getElementById("state").value;
					//alert(state);
					var start_date=document.getElementById("start_date").value;
					var end_date=document.getElementById("end_date").value;
					var vertical=document.getElementById("vertical").value;
                    document.getElementById("distributor_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?state='+state+'&type=datedistributor&start_date='+start_date+'&end_date='+end_date+'&vertical='+vertical,'distributor_select_div',0);
                }
                function distributor_emp(distributor){
                    if(document.getElementById("state").value.search(/\S/) == -1)
                        return false;
					if(document.getElementById("distributor").value.search(/\S/) == -1)
                        return false;
	
                    var distributor = encodeURIComponent(distributor);
					var state = encodeURIComponent(document.getElementById("state").value);
					var vertical=document.getElementById("vertical").value;
                    document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?state='+state+'&type=distemp&distributor='+distributor+'&vertical='+vertical,'emp_select_div',0);
                }
                function display_result(){
                if(document.getElementById("state").value.search(/\S/) == -1){
                    alert('Please Select State');
                    return false;
                }
				if(document.getElementById("distributor").value.search(/\S/) == -1){
                    alert('Please Select Distributor');
                    return false;
                }
                if(document.getElementById("employee").value.search(/\S/) == -1){
                    alert('Please Select SO');
                    return false;
                }
				if(document.getElementById("start_date").value.search(/\S/) == -1){
					alert('Provide start date');
				return false;
				}
				if(document.getElementById("end_date").value.search(/\S/) == -1){
					alert('Provide end date');
					return false;
				}
				if(start_date>end_date){
					alert("Start date cannot be greater than end date");
					return false;
				}
                
                var state = document.getElementById("state").value;
			    var employee = document.getElementById("employee").value;
                var distributor = document.getElementById("distributor").value;
				var start_date=document.getElementById("start_date").value;
				var end_date=document.getElementById("end_date").value;

                document.getElementById("display_details").innerHTML = '';
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                GenericAjaxFunction('distributorReportData.php?employee='+employee+'&distributor='+distributor+'&state='+state+'&start_date='+start_date+'&end_date='+end_date,'display',0);
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
		mywindow.document.write('<html><head><title>Distributor Report</title>');
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
	/*var dt = new Date();
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
	a.download = 'Distributor data' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();*/
	var state = document.getElementById("state").value;
	var employee = document.getElementById("employee").value;
	var distributor = document.getElementById("distributor").value;
	var start_date=document.getElementById("start_date").value;
	var end_date=document.getElementById("end_date").value;
	window.open('distributorReportDataExport.php?employee='+employee+'&distributor='+distributor+'&state='+state+'&start_date='+start_date+'&end_date='+end_date,'mywindow');
}
        </script>
        <br />
 <?php
}
?>