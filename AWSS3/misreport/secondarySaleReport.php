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
	   $sql_emp_designation=mysql_fetch_array(mysql_query("SELECT designation FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
		$designation=$sql_emp_designation['designation'];

		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}
?>
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Secondary Sales Report</strong></td>
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
                          	$onclickvertical = "vertical_emp(this.value);";
							$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Vertical:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
								$sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(vertical_value, ',', -1) as distinct_vertical_value 
								FROM employee_master WHERE SUBSTRING_INDEX( vertical_value, ',', -1 ) != '' AND vertical_value NOT LIKE 'M%' 
								".$emp_hierarchy_condition." AND vertical_value IN(SELECT DISTINCT vertical_value FROM employee_master 
								WHERE acedns='Y' AND designation IN('RSM','ASM','SO','SS')) ORDER BY SUBSTRING_INDEX(vertical_value, ',', -1) ASC";
								$res_vertical = mysql_query($sql_vertical);
								$vertical_select_control = "<select name=\"vertical\" id=\"vertical\" onchange=\"".$onclickvertical."\">";
								$vertical_select_control .= "<option value=\"\">Select</option>";
								//$vertical_select_control .= "<option value=\"all\">All</option>";
								$dist_vertical_val_array=array();
								while($row_vertical = mysql_fetch_array($res_vertical)){
									$dist_vert_value = trim($row_vertical['distinct_vertical_value']);
										/*if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
											$pos = substr($dist_vert_value,0,1);
											if($pos == 'M'){
												$dist_vert_value = 'MACROMAN';
											}
										}*/
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
								?>
                              <!--tr>
                                <td align="right" width="45%" colspan="2">RSM:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php /*$rsm_name=$_REQUEST['rsm_name'];?>
                                    <select name="rsm_name" id="rsm_name" onChange="javascript:emp_select_asmso(this.value);">
                                    <option value="">SELECT</option>
                                     <?php 
                                     $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ".$emp_hierarchy_condition." AND acedns='Y' AND 
									 				UPPER(designation)='RSM' ORDER BY emp_name ASC";
                                     $resultqueryemp = mysql_query($sqlqueryemp);
                                     $count=mysql_num_rows($resultqueryemp);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                        {
                                      ?>
                                         <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if($emp_name==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                       <?php
									   		$emp_code_string .= "'".$rowqueryemp['emp_code']."',";
                                        }
										$emp_code_string = rtrim($emp_code_string,",");
                                      }*/
                                        ?>	
                                     </select>
                                </td>
                           </tr-->
                        <?php 
							if($designation!='RSM' && $designation!='ASM' && $designation!='SO' && $designation!='SS' )
							{
							$table_data_emp_upper = "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">RSM:</td>";
							echo $table_data_emp_upper .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
												<div id=\"empupperh_select_div\"></div></td></tr>";
							}
							$table_data_emp .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">ASM/SO/SS:</td>";
							echo $table_data_emp .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
												<div id=\"emp_select_div\"></div></td></tr>";
							/*$table_data .= "<tr><td align=\"right\"  colspan=\"2\" width=\"45%\">Vertical:</td>";
							echo $table_data .= "<td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">
												<div id=\"vertical_select_div\"></div></td></tr>";					
							$table_data .= "<tr><td align=\"right\" colspan=\"2\" width=\"45%\">Vertical:</td><td align=\"left\" width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
								$sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(vertical_value, ',', -1) as distinct_vertical_value 
								FROM employee_master WHERE SUBSTRING_INDEX( vertical_value, ',', -1 ) != '' ".$emp_hierarchy_condition." ORDER BY 
								SUBSTRING_INDEX(vertical_value, ',', -1) ASC";
								$res_vertical = mysql_query($sql_vertical);

								$vertical_select_control = "<select name=\"vertical\" id=\"vertical\">";
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
								echo $table_data .= "</td></tr>";*/
						?>
                        <tr>
                            <td align="center" width="" colspan="4">
                              <div id="date_div" style="width:60%;" >
                                From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
                                To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
                                <!--input type="submit" name="submit" value="Submit" onClick="show_emp();" /-->
                                </div>
                                </td>
                          </tr>      
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
       <div id="display" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="center" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
</center>
			   <script language="javascript" type="text/javascript">
                function emp_select_asmso(rsmcode){
                    if(document.getElementById("rsm_name").value.search(/\S/) == -1)
                        return false;
                    var rsmcode = encodeURIComponent(rsmcode);
                    document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?rsmcode='+rsmcode+'&type=empasmso','emp_select_div',0);
                }
                function emp_vertical(asmsocode){
                    //var state = encodeURIComponent(state);
					var amssoval = encodeURIComponent(document.getElementById("asm_so").value);
                    document.getElementById("vertical_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                    GenericAjaxFunction('get_vertical_related_data.php?asmsocode='+amssoval+'&type=empvertical','vertical_select_div',0);
                }
				function vertical_emp(vertical){
					var vertical = encodeURIComponent(document.getElementById("vertical").value);
					var designation="<?=$designation?>";
					if(designation=='RSM' || designation=='ASM' || designation=='SO' || designation=='SS')
					{
					 document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                 GenericAjaxFunction('get_vertical_related_data.php?designation='+designation+'&vertical='+vertical+'&type=verticalemp','emp_select_div',0);

					}
					else
					{
                    document.getElementById("empupperh_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                 GenericAjaxFunction('get_vertical_related_data.php?vertical='+vertical+'&type=verticalemp','empupperh_select_div',0);
					}
                }
                function display_result(){
					var designation="<?=$designation?>";
					if(designation!='RSM' && designation!='ASM' && designation!='SO' && designation!='SS')
					{
						if(document.getElementById("rsm_name").value.search(/\S/) == -1){
							alert('Please Select RSM');
							return false;
						}
					}
                if(document.getElementById("asm_so").value.search(/\S/) == -1){
                    alert('Please Select ASM/SO/SS');
                    return false;
                }
                if(document.getElementById("vertical").value.search(/\S/) == -1){
                    alert('Please Select Vertical');
                    return false;
                }
                
                var vertical = document.getElementById("vertical").value;
                var asm_so = document.getElementById("asm_so").value;
				//alert(document.getElementById("asm_so").value);
				var start_date=document.getElementById("start_date").value;
				var end_date=document.getElementById("end_date").value;
                
                document.getElementById("display_details").innerHTML = '';
                document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
                GenericAjaxFunction('secondarySaleReportData.php?asm_so='+asm_so+'&vertical='+vertical+'&start_date='+start_date+'&end_date='+end_date,'display',0);
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
	a.download = 'Visit Frequency data' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
}
        </script>
        <br />
 <?php
}
?>