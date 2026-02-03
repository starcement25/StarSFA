<?php
ob_start();
session_start();
require("adminUtils_CRM_CRE.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	if($_SESSION['admin_login']=="admin" ){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = " WHERE acedns='Y'";
		$state_condition = " WHERE state != '' ";
		$route_condition = " WHERE route_name != '' ";
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") AND acedns='Y'";
		$state_condition = " WHERE state != '' ";
		$route_condition = " AND route_name != '' ";
	}
	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$state_condition." ORDER BY state ASC";
	$res_state = mysqli_query($link,$sql_state);
	$state_total = mysqli_num_rows($res_state);
	?>
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
    <?php
	$hidden = " ";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">CRM Report</span>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>
	<table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" cellpadding=\"4px\">
					<tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Report</td></tr>";
	
	$table_data="<tr><td align=\"right\">Date:</td><td><input type=\"date\" name=\"allocation_date\" id=\"allocation_date\" style=\"height:15px;\" /></td></tr><br />";
	echo $table_data.="<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Submit\" onclick=\"display_result();\"></td></tr></table>";
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	echo "<div id=\"display\">"; 
	?>
	<table class="border" width="30%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
        <td>Employee Name</td>
        <td >Number Of Call</td>
        <td >Total Call Duration</td>
      </tr>
      <?php
	$today=date('Ymd');
	$sqlqueryemp="SELECT EM.emp_code,EM.emp_name,COUNT(CT.customer_code) as tot_cnt,SEC_TO_TIME(SUM(TIME_TO_SEC(CT.call_duration))) as tot_duration FROM `CRM_transaction` CT,employee_master EM WHERE SUBSTRING(call_id,4,5)=EM.emp_code AND SUBSTRING(call_id,-14,8)='".$today."'  GROUP BY SUBSTRING(call_id,4,5)";
	$resultemp = mysqli_query($link,$sqlqueryemp);
	$countemp=mysqli_num_rows($resultemp);
	if($countemp>0){
		while($row_emp = mysqli_fetch_assoc($resultemp)){
			$emp_name = $row_emp['emp_name'];
			$emp_code = $row_emp['emp_code'];
			$callcnt = $row_emp['tot_cnt'];
			$tot_duration= $row_emp['tot_duration'];
			
			echo "<tr>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"return show_report_details('".$emp_code."')\">".$emp_name."</td>
					<td align=\"right\">".$callcnt."</td>
					<td align=\"right\">".$tot_duration."</td>
				  </tr>";
			}
		}
	?>
    </table>
    
    <input type="hidden" name="serdate" id="serdate" value="<?php echo $today?>" />
	<?php
	echo "</div><br><br>";
	?>
    <div id="display_details" style="max-width:1300px;" align="center"></div><br />
    <script>
	function display_result(){
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		
		if(dd<10) {
			dd = '0'+dd
		} 
		
		if(mm<10) {
			mm = '0'+mm
		} 
		var CUR_DATE = yyyy + '-' + mm + '-' + dd;	
		if(document.getElementById("allocation_date").value.search(/\S/) == -1){
			alert("Please choose date");
			return false;
		}
		else
			var allocation_date = document.getElementById("allocation_date").value;
		
		
		
		//document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('customer_report_CRM_data.php?allocation_date='+allocation_date,'display',0);
	}
	function distributor_route(distributor){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = document.getElementById("state").value;
		if(document.getElementById("distributor").value.search(/\S/) == -1)
			return false;
		var distributor = encodeURIComponent(distributor);
		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?distributor='+distributor+'&type=route','route_select_div',0);
	}
	/*function state_emp_lev_one(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("emp_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=stateemplevone','emp_div',0);
	}*/
	function show_report_details(empcode){
		var sdate = document.getElementById("serdate").value;
		GenericAjaxFunction('get_reportdetails_data.php?empcode='+empcode+'&sdate='+sdate,'display_details',0);
		
	}
	function exporttocsv(empcode){
		//var emp_code = document.getElementById("emp_code").value;
		var sdate = document.getElementById("serdate").value;
		window.open('get_reportdetails_data_export.php?empcode='+empcode+'&sdate='+sdate);
   }
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
	function save_complain(call_id)
	{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		var complainval = document.getElementById("complainval_"+call_id).value;
		var url="savecomplaincloser.php?complainval="+complainval+"&call_id="+call_id;
		xmlHttp.onreadystatechange=savecomplaincloser;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	}
	function savecomplaincloser()
	 {
		if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		 {
			var val=xmlHttp.responseText;
			var valarray=val.split('#');
			if(valarray[0]==1)
			{
				document.getElementById("complain_"+valarray[1]).innerHTML=valarray[2];
			}
		 }
	 }
	</script>
    <?php
}
?>