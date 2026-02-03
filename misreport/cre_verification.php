<?php
ob_start();
session_start();
require("adminUtils_CRM_CRE.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

if(isset($_REQUEST['sub'])){
	$customercode=$_POST['customercode'];
	$today=date("Ymd");
	sleep(10);
	$sqltransaction=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM CRM_transaction WHERE customer_code='".$customercode."' 
									AND SUBSTRING(call_id,-14,8)='".$today."' ORDER BY DATE_FORMAT('%Y-%m-%d %h:%i:%s',SUBSTRING(call_id,-14,14)) DESC LIMIT 0,1"));
	if($sqltransaction['call_id']!='')
	{
		$call_id=$sqltransaction['call_id'];
		$qcnt=$_POST['total_ques'];
		for($i=1;$i<=$qcnt;$i++){
			if($_POST['ques'][$i]==8 && $_POST['ans'][$i]==0){
				foreach($_POST['check_list'] as $selected){
				  if($selected=="Others"){ $selected=$_POST['other_reason']; }
				  $remark.=$selected.'#';
				}
				$remarks=substr($remark,0,-1);
			}
			else{
				$remarks=$_POST['remarks'][$i];
			}
			 $sql_ins="INSERT INTO CRM_customer_feedback SET call_id='".$call_id."',customer_code='".$customercode."',question_num='".$_POST['ques'][$i]."',ans='".$_POST['ans'][$i]."',remarks='".$remarks."'";
			mysqli_query($link,$sql_ins);
			
			
		}
		$today=date("Ymd");
		$sqlup="UPDATE CRM_transaction SET remarks='".$_REQUEST['sub_remarks']."',response_type='Responded',respond_date_time=NOW() WHERE customer_code='".$customercode."' AND SUBSTRING(call_id,-14,8)='".$today."'";
		if(mysqli_query($link,$sqlup)){
		 $GLOBALS['msg']="Transaction updated successfully";
		}
		else{
		 $GLOBALS['msg']="Transaction not updated successfully";
		}
	}
	else{
		$GLOBALS['msg']="Call id has not genareted please check your data connectivity";
	}
}

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
		$today=date("Y-m-d");
		$todaystring=date("Ymd");
	$sqlemp="SELECT `route_code` FROM `emp_datewise_route_allocation` WHERE `emp_code`='".$_SESSION['admin_login']."' AND `allocation_date`='".$today."'";
	$resempdetails=mysqli_query($link,$sqlemp);
	$hidden = " ";
	echo "<form action=\"\" name=\"frm_sub\" method=\"post\">";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Retailer CRE</span><br><br>";
	echo "<table  width=\"100%\" align=\"center\">";
	echo "<tr><td width=\"40%\"><table  class=\"border\" width=\"90%\" style=\"border-collapse:collapse;padding:6px;\" border=\"1\" cellpadding=\"4px\">";?>
    <tr class="TDHEAD">
        <td>Route Name</td>
        <td>No of Customer</td>
        <td>Call made</td>
      </tr>
    <?php
	while($rowempdetails=mysqli_fetch_assoc($resempdetails)) { 
	$routname=mysqli_fetch_assoc(mysqli_query($link,"SELECT route_name FROM route_master WHERE route_code='".$rowempdetails['route_code']."'"));
	/*$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code='".$rowempdetails['route_code']."' 
							AND CM.cust_type='R' AND CRR.acedns='Y'";*/
	$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM,
							emp_datewise_route_allocation  EDRA
							WHERE EDRA.route_code=CRR.route_code AND CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$rowempdetails['route_code']."' AND CM.cust_type='R' AND CRR.acedns='Y' AND EDRA.distributor_code=CM.rds_tag AND 
							EDRA.allocation_date='".$today."'";						
	$resultcustomerroutedetails = mysqli_query($link,$sqlquerycustomerroutedetails);
	$countcustomerroutedetails=mysqli_num_rows($resultcustomerroutedetails);
	
	$sqlquerycallmade="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM,CRM_transaction CT
							WHERE CT.customer_code=CM.customer_code AND CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$rowempdetails['route_code']."' AND CM.cust_type='R' AND CRR.acedns='Y'  AND SUBSTRING(CT.call_id,-14,8)='".$todaystring."'";
	$resultcallmade = mysqli_query($link,$sqlquerycallmade);
	$countcallmade=mysqli_num_rows($resultcallmade);
		echo "<tr>
				<td>".$routname['route_name']."</td>
				<td align=\"right\">".$countcustomerroutedetails."</td>
				<td>".$countcallmade."</td>
			  </tr>";
	}
	echo "</table></td><td width=\"60%\">";
	echo "<table id=\"criteria_tab\" class=\"border\" width=\"50%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" cellpadding=\"4px\">";
	?>
    <tr class="TDHEAD">
    	<td colspan="2" align="center"><b>Retailer CRE</b></td>
    </tr>
    <?php
	//attribute_selection_customer_verification($hidden,$create_control);
	$resemp=mysqli_query($link,$sqlemp);
	?>
    <tr><td align="center" colspan="2">Select Route :
    <select name="route" id="route">
     <option value="">Select one Route</option>
     <?php while($rowemp=mysqli_fetch_assoc($resemp)) { 
	 $routname=mysqli_fetch_assoc(mysqli_query($link,"SELECT route_name FROM route_master WHERE route_code='".$rowemp['route_code']."'"));
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
	if(isset($_REQUEST['sub']))
	{
		$route=$_REQUEST['route'];
		if($GLOBALS['msg']!=''){
		echo "<p>".$GLOBALS['msg']."</p>";
		}
	?>
        <table class="border" width="70%" style="border-collapse:collapse;" border="1" height="177px">
      <tr class="TDHEAD">
        <td>Customer Name</td>
        <td >Phone no</td>
        <td >Last Call Date Time</td>
        <td>Responded Date Time</td>
        <td>Status</td>
      </tr>
    <?php
	$sqlquerycustomerroute="SELECT DISTINCT CM.customer_name,CM.phone_no,CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM 
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code='".$route."' AND CM.cust_type='R' AND CRR.acedns='Y'
						   ORDER BY CM.customer_name ASC";
	$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	$today=date("Ymd");
	if($countcustomerroute>0){
		echo "<tr><td colspan = '5' class = 'TDHEAD_SUB' align = 'center'>Retailer CRE</td></tr>";
		while($row_customerroute = mysqli_fetch_assoc($resultcustomerroute)){
			$sqltrans=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM CRM_transaction WHERE customer_code='".$row_customerroute['customer_code']."' AND SUBSTRING(call_id,-14,8)='".$today."'"));
			$customer_name = $row_customerroute['customer_name'];
			$customer_code = $row_customerroute['customer_code'];
			$phone_no = $row_customerroute['phone_no'];
			if($phone_no!=''){
			echo "<tr>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"return update_crm_details('".$customer_code."')\">".$customer_name."</td>
					<td>".$phone_no."</td>
					<td>".(($sqltrans['update_date_time']!="0000-00-00 00:00:00" && $sqltrans['update_date_time']!='')?date('d-m-Y H:i:s',strtotime($sqltrans['update_date_time'])):'')."</td>
					<td>".(($sqltrans['respond_date_time']!="0000-00-00 00:00:00" && $sqltrans['respond_date_time']!='')?date('d-m-Y H:i:s',strtotime($sqltrans['respond_date_time'])):'')."</td>
					<td>".$sqltrans['response_type']."</td>
				  </tr>";
			}
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
		GenericAjaxFunction('cre_verification_data.php?route='+route,'display',0);
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
		
		
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('cre_customer_remarks.php?customer_code='+customer_code,'display_details',0);
		document.getElementById("print_export").hidden = false;
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
	function question_checked()
	{
		alert('kkk');
		if(document.getElementById("ans11").checked==false)
		{
			alert("Please choose at least one option for Question 1");
			return false;
		}
		return true;
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