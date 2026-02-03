<?php
ob_start();
session_start();
require("adminUtils_CRM_CRE.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");
function main(){
	if($_SESSION['admin_login']=="admin" ){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
		$state_condition = " WHERE state != '' ";
		$route_condition = " WHERE route_name != '' ";
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") ";
		$state_condition = " AND state != '' ";
		$route_condition = " AND route_name != '' ";
	}
	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$emp_hierarchy_value_condition.$state_condition." ORDER BY state ASC";
	$res_state = mysql_query($sql_state);
	$state_total = mysql_num_rows($res_state);
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
	$GLOBALS['msg']='';
	if($_REQUEST['flag']=='0')  $GLOBALS['msg']="Transaction not updated successfully";
	if($_REQUEST['flag']=='1')  $GLOBALS['msg']="Transaction updated successfully";
	if($_REQUEST['flag']=='2')  $GLOBALS['msg']="Call id has not genareted please check your data connectivity";
	if($_REQUEST['flag']=='3')  $GLOBALS['msg']="Remarks added sucessfully";
	if($_REQUEST['flag']=='4')  $GLOBALS['msg']="Remarks not added";
	if($_REQUEST['flag']=='5')  $GLOBALS['msg']="No record found";
	$today=date("Y-m-d");
	$todaystring=date("Ymd");
	$sqlemp="SELECT `route_code` FROM `emp_datewise_route_allocation` WHERE `emp_code`='".$_SESSION['admin_login']."' AND `allocation_date`='".$today."'";
	$resempdetails=mysql_query($sqlemp);
	$hidden = " ";
	echo "<form action=\"\" name=\"frm_sub\" method=\"post\">";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Retailer CRE</span><br><br>
    </tr>
</span><br>";
	echo "<table  width=\"100%\" align=\"center\">";
	echo "<tr><td width=\"50%\"><table  class=\"border\" width=\"90%\" style=\"border-collapse:collapse;padding:6px;\" border=\"1\" cellpadding=\"4px\">";?>
    <tr class="TDHEAD">
        <td>Route Name</td>
        <td>No of Customer</td>
        <td>Call made</td>
        <td>CP name</td>
        <td>TSI</td>
      </tr>
    <?php
	while($rowempdetails=mysql_fetch_array($resempdetails)) { 
	$routname=mysql_fetch_array(mysql_query("SELECT route_name FROM route_master WHERE route_code='".$rowempdetails['route_code']."'"));
	/*$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code='".$rowempdetails['route_code']."' 
							AND CM.cust_type='R' AND CRR.acedns='Y'";*/
	$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM,
							emp_datewise_route_allocation  EDRA
							WHERE EDRA.route_code=CRR.route_code AND CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$rowempdetails['route_code']."' AND CM.cust_type='R' AND CRR.acedns='Y' AND EDRA.distributor_code=CM.rds_tag AND 
							EDRA.allocation_date='".$today."'";						
	$resultcustomerroutedetails = mysql_query($sqlquerycustomerroutedetails);
	$countcustomerroutedetails=mysql_num_rows($resultcustomerroutedetails);
	
	$sqlquerycallmade="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM,CRM_transaction CT
							WHERE CT.customer_code=CM.customer_code AND CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$rowempdetails['route_code']."' AND CM.cust_type='R' AND CRR.acedns='Y'  AND SUBSTRING(CT.call_id,-14,8)='".$todaystring."'";
	$resultcallmade = mysql_query($sqlquerycallmade);
	$countcallmade=mysql_num_rows($resultcallmade);
	$sqlcpname="SELECT DISTINCT CM.customer_name,CM.customer_code FROM customer_master CM,emp_datewise_route_allocation  EDRA WHERE EDRA.distributor_code=CM.customer_code AND 
				EDRA.route_code='".$rowempdetails['route_code']."' AND EDRA.allocation_date='".$today."' AND CM.cust_type='D' AND CM.acedns='Y' ORDER BY CM.customer_name ASC";
	$rscpname=mysql_query($sqlcpname);
	$cpname='';
	while($rowcpname=mysql_fetch_array($rscpname))
	{
		$cpname=$cpname.$rowcpname['customer_name'].',';
		$cpcode=$rowcpname['customer_code'];
	}
	$cpname=substr($cpname,0,-1);
	$sqlreportingemp="SELECT emp_name FROM employee_master WHERE emp_code=(SELECT reporting_to FROM employee_master WHERE emp_code=(SELECT emp_code FROM customer_route_emp_relation WHERE customer_code='".$cpcode."' AND acedns='Y'))";
	$rsreportingemp=mysql_query($sqlreportingemp);
	$rowreportingemp=mysql_fetch_array($rsreportingemp);
	$emp_name=$rowreportingemp['emp_name'];
		echo "<tr>
				<td>".$routname['route_name']."</td>
				<td align=\"right\">".$countcustomerroutedetails."</td>
				<td align=\"right\">".$countcallmade."</td>
				<td align=\"left\">".$cpname."</td>
				<td align=\"left\">".$emp_name."</td>
			  </tr>";
	}
	echo "</table></td><td width=\"60%\" valign=\"top\">";
	echo "<table id=\"criteria_tab\" class=\"border\" width=\"50%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" cellpadding=\"4px\">";
	?>
    <tr class="TDHEAD">
    	<td colspan="2" align="center"><b>Retailer CRE</b></td>
    </tr>
    <?php
	//attribute_selection_customer_verification($hidden,$create_control);
	$resemp=mysql_query($sqlemp);
	?>
    <tr><td align="center" colspan="2">Select Route :
    <select name="route" id="route">
     <option value="">Select one Route</option>
     <?php while($rowemp=mysql_fetch_array($resemp)) { 
	 $routname=mysql_fetch_array(mysql_query("SELECT route_name FROM route_master WHERE route_code='".$rowemp['route_code']."'"));
	 ?>
     <option value="<?php echo $rowemp['route_code'];?>" 
     <?php if($_REQUEST['route']!='' && $_REQUEST['route']==$rowemp['route_code']) echo "selected" ?>
     ><?php echo $routname['route_name']; ?></option>
     <?php } ?>
    </select>
    </td></tr>
    <?php
	echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"button\" name=\"submit\" value=\"Submit\" onclick=\"display_result();\"></td></tr></table></td></tr></table>";
	echo "<br><br>";
	?>
    <div id="display" style="max-height: 200px; width:80%; overflow-y: scroll;" align="center">
    <?php
	if($_REQUEST['flag']!='')
	{
		$route=$_REQUEST['route'];
		$emp_code=$_SESSION['admin_login'];
		$current_date=date('Y-m-d');
		if($GLOBALS['msg']!=''){
		echo "<p><b>".$GLOBALS['msg']."</b></p>";
		}
	?>
        <table class="border" width="70%" style="border-collapse:collapse;" border="1" height="177px">
      <tr class="TDHEAD">
        <td>Customer Name</td>
        <td >Phone no</td>
        <td >Last Call Date Time</td>
        <td>Responded Date Time</td>
        <td>Status</td>
        <td>Complain Closer</td>
      </tr>
    <?php
	$sqlquerycustomerroute="SELECT DISTINCT CM.customer_name,CM.phone_no,CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM,
							emp_datewise_route_allocation  EDRA
							WHERE EDRA.route_code=CRR.route_code AND CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$route."' AND CM.cust_type='R' AND CRR.acedns='Y' AND EDRA.distributor_code=CM.rds_tag AND 
							EDRA.allocation_date='".$current_date."' AND EDRA.emp_code='".$emp_code."' ORDER BY CM.customer_name ASC";
	$resultcustomerroute = mysql_query($sqlquerycustomerroute);
	$countcustomerroute=mysql_num_rows($resultcustomerroute);
	$today=date("Ymd");
	if($countcustomerroute>0){
		echo "<tr><td colspan = '6' class = 'TDHEAD_SUB' align = 'center'>Retailer CRE</td></tr>";
		while($row_customerroute = mysql_fetch_array($resultcustomerroute)){

			$sqltrans=mysql_fetch_array(mysql_query("SELECT *,DATE_FORMAT(SUBSTRING(call_id,-14,14),'%d-%m-%Y %H:%i:%s') AS last_call_date_time FROM CRM_transaction WHERE customer_code='".$row_customerroute['customer_code']."' AND response_type='Responded' ORDER BY respond_date_time DESC LIMIT 0,1"));
			$customer_name = $row_customerroute['customer_name'];
			$customer_code = $row_customerroute['customer_code'];
			$phone_no = $row_customerroute['phone_no'];
			
			//if($phone_no!=''){
			echo "<tr>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"return update_crm_details('".$customer_code."')\">".$customer_name."</td>
					<td>".$phone_no."</td>
					<td>".(($sqltrans['last_call_date_time']!='')?$sqltrans['last_call_date_time']:'')."</td>
					<td>".(($sqltrans['respond_date_time']!="0000-00-00 00:00:00" && $sqltrans['respond_date_time']!='')?date('d-m-Y H:i:s',strtotime($sqltrans['respond_date_time'])):'')."</td>
					<td>".$sqltrans['response_type']."</td>
					<td>".$sqltrans['complain_closer']."</td>
				  </tr>";
			//}
		}
	?>
    </table>
    <br />
    <br>
<!--<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>-->
    <?php
		}
		else{
			echo "No Records";
		}
	}
	?>
    </div><br />
    <div id="display_details" style="max-width:1000px;" align="center"></div><br />
    <div id="show_update" style="max-height: 200px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <!--<div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>-->
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	echo "</form>";
	?>
    <script>
	function display_result(){
		if(document.getElementById("route").value.search(/\S/) == -1){
			alert('Please Select Route');
			return false;
		}
		else{
			var route = document.getElementById("route").value;
			document.getElementById("display_details").innerHTML = '';
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('cre_verification_data_modified.php?route='+route,'display',0);
		}
	}
	function show_drop_box(){
		
		document.getElementById("remarks_area").style.display="none";
		document.getElementById("remarks_drop").style.display="block";
	}
	function hide_drop_box(){
		
		document.getElementById("remarks_area").style.display="block";
		document.getElementById("remarks_drop").style.display="none";
	}
	function update_crm_details(customer_code){
		//document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		//GenericAjaxFunction('cre_customer_remarks.php?customer_code='+customer_code,'display_details',0);
		//document.getElementById("print_export").hidden = false;
		var route = document.getElementById("route").value;
		window.location="cre_customer_remarks_modified.php?customer_code="+customer_code+"&route="+route;
	}
	function show1(){
		document.getElementById('ph_responce').style.display = 'block';
		document.getElementById('ph_notresponce').style.display = 'none';
	}
	
	function show2(){
		document.getElementById('ph_notresponce').style.display = 'block';
		document.getElementById('ph_responce').style.display ='none'
	}
	function showhide_other(checkboxElem) {
	  if (checkboxElem.checked) {
		document.getElementById('other_reason').style.display = 'block';
		//document.getElementById('subj').style.display = 'none';
	  } else {
		document.getElementById('other_reason').style.display = 'none';
		//document.getElementById('subj').style.display = 'block';
	  }
	} 
	function ajaxsub1(){
		   var customercode =document.getElementById("customercode").value;
		   var restype=document.querySelector('input[name="restype"]:checked').value;
		   GenericAjaxFunction('crmremarksupdate.php?customercode='+customercode+'&restype='+restype,'show_update',0);
	}
	function ajaxsub(){
	   var customercode =document.getElementById("customercode").value;
	   var remarks=document.getElementById("remarks").value;
	   //GenericAjaxFunction('crmremarksupdate.php?customercode='+customercode+'&remarks='+remarks,'show_update',0);
	   display_result();
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById('display').innerHTML;	
		Popup(displaydiv);
	}
	function Popup(data) 
	{
		var mywindow = window.open('', 'No Activity Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>No Activity Report</title>');
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
	function exporttocsv(divid)
	{
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		/*document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');*/
		//creating a temporary HTML link element (they support setting file names)*/
		var a = document.createElement('a');
		//getting data from our div that contains the HTML table
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		//setting the file name
		a.download = 'No Activity Report' + postfix + '.xls';
		//triggering the function
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		//just in case, prevent default behaviour
		//e.preventDefault();
	}
	
	function show_option(survey_type){
		var survey_type = survey_type;
		if(survey_type == 'KYC'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		else if(survey_type == 'Branding'){
			document.getElementById("date_div").hidden = true;
			document.getElementById("month_row").hidden = false;
		}
		else if(survey_type == 'Site Visit'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		if(survey_type == 'Technical Meets'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
	}
function call_verify(verify_number,ozonetel_username,ozonetel_apikey){
	$.get("http://cloudagent.in/calite/c2capi.html?number="+verify_number+"&username="+ozonetel_username+"&apiKey="+ozonetel_apikey+"", function(data, status){
		var json = data;
		document.getElementById("calling_div").hidden = false;
		document.getElementById("calling_div").innerHTML = 'Please Wait Calling...<br>'+'<img src="ajax-loader_bar.gif" id="ajaxloaderbar">';
		
		var delay=5000; //1 second

		setTimeout(function() {
			if(json['status'] == 'success'){
				document.getElementById("calling_div").innerHTML = 'Call eshtablished';
			}
			else{
				document.getElementById("calling_div").innerHTML = 'Cannot make call';
			}
			$('#calling_div').fadeOut(2000);
		  //your code to be executed after 1 second
		}, delay);
		
		//$('#curtain').fadeIn(800);
		//$('#show_add_info').fadeIn(800);
	});
}

	</script>
    <?php
}
?>