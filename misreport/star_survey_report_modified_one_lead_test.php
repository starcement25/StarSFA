<?php
//ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

if($mode == 'insertcustomer')		    insert_record($_REQUEST['row_id']);
else {
disphtml("main();");
}
//disphtml("main();");

function main(){
    require("include/dbcon.php");
	?>
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
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
    
    
    <style>



    
  </style>  
    
    
    
    <?php
	$hidden = " hidden";
	
	$create_control = "<tr><td align=\"right\">Type:</td><td align=\"left\"><select name=\"survey_type\" id=\"survey_type\" onchange=\"show_option(this.value);\"><option value=\"\">Select</option>";
	/*$sql_survey_type = "SELECT DISTINCT type FROM survey_output WHERE type 
	NOT IN('Branding Verification','double','masterview','radio','YES:NO')  ORDER BY type ASC";*/
	$sql_survey_type = "SELECT DISTINCT survey_sub_menu FROM survey_input_mle ORDER BY survey_sub_menu ASC";
	$res_survey_type = mysqli_query($link,$sql_survey_type);
	while($row_survey_type = mysqli_fetch_assoc($res_survey_type)){
		$survey_type = $row_survey_type['survey_sub_menu'];
		if($survey_type != '')
		$create_control .= "<option>".$survey_type."</option>";
	}
	$create_control .= "</select></td></tr>";
	$create_control .= "<tr id=\"month_row\" hidden><td align=\"right\">Month:</td><td align=\"left\">
	<select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
	/*$sql_month_selection = "SELECT DISTINCT SUBSTRING(LO.date,1,7) AS distinct_date FROM location LO, survey_output SO WHERE SO.survey_id = LO.trans_id ORDER BY LO.date ASC";
	$res_month_selection = mysqli_query($link,$sql_month_selection);
	while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_date'];
		$year_month_split = explode("-",$distinct_date);
		$monthNum  = $year_month_split[1];
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."</option>";
	}*/
	$create_control .= "</select></td></tr>";
	$create_control .= "<tr id=\"technical_meet_row\" hidden><td align=\"right\" >Technical Meet Type:</td><td align=\"left\"><select name=\"technical_type\" id=\"technical_type\"><option value=\"\">Select</option>";
	$sql_technical_type = "SELECT display_name,row_id FROM survey_input_mle WHERE type='menu' and survey_sub_menu='Technical Meets' ORDER BY display_order ASC";
	$res_technical_type = mysqli_query($link,$sql_technical_type);
	while($row_technical_type = mysqli_fetch_assoc($res_technical_type)){
		$technical_meet_type = $row_technical_type['display_name'];
		$row_id = $row_technical_type['row_id'];
		if($technical_meet_type != '')
		$create_control .= "<option value='".$row_id."'>".$technical_meet_type."</option>";
	}
	$create_control .= "</select></td></tr>";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">OTHER REPORTS MODIFIED</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1500px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
    
</div>
	<input type="hidden" id="report_name" />
    
    <?php
	echo "</center>";
	?>
        <form name="frm_opts" action="star_survey_report_modified.php" method="post" >
        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="row_id" value="">
    </form>
    <script>
	function display_result(){
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		if(document.getElementById("sale_access").value.search(/\S/) == -1){
			alert('Please Select Department');
			return false;
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		
		if(document.getElementById("survey_type").value.search(/\S/) == -1){
			alert("Please Select Type");
			return false;
		}
		
		var survey_type = document.getElementById("survey_type").value;
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		
		
		
		if(survey_type == 'Technical Meets'){
			if(document.getElementById("technical_type").value.search(/\S/) == -1){
			alert("Please Select Technical Meet Type");
			return false;
			}
			var technical_meet_type = document.getElementById("technical_type").value;
		}
		else
		{
			var technical_meet_type='';
		}
		if(survey_type == 'KYC' || survey_type == 'Site Visit' || survey_type == 'Technical Meets' || survey_type == 'Branding' 
		|| survey_type == 'Branding Verification' || survey_type == 'Dhalai Services' || survey_type == 'Site Lead and Conversion Tracking' || survey_type == 'Corporate Branding' || survey_type == 'Lead Generation' || survey_type == 'Complaint Report' || survey_type == 'Influencer' || survey_type == 'Mason Skill Building Program' ||  survey_type == 'Lead Test' || survey_type == 'Counter Visit' || survey_type == 'MLE Site Visit' || survey_type == 'MTL Testing Format' || survey_type == 'Quality Complaint'){
			var start_date = document.getElementById("start_date").value;
			var end_date = document.getElementById("end_date").value;
			if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
				alert("Please provide start date/end date");
				return false;
			}
			if(start_date>end_date){
				alert("Start date cannot be greater than end date");
				return false;
			}
		}
		//
		/*if(survey_type == 'Branding'){
			var month_data = document.getElementById("month_select").value;
			if(document.getElementById("month_select").value.search(/\S/) == -1){
				alert('Please Select Month');
				return false;
			}
		}*/
		var employee = document.getElementById("employee").value;
		
		if(survey_type == 'Branding'){
			var url = 'star_survey_report_branding.php';
		}
		else if(survey_type == 'KYC'){
			var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
			if(nickname=='STAR')
			{
				var url = 'star_survey_report_KYC_modified.php';
			}
			else
			{
				var url = 'star_survey_report_KYC_modified_one.php';	
			}
		}
		else if(survey_type == 'Site Visit'){
				var url = 'star_survey_report_site_visit.php';
		}
		else if(survey_type == 'Technical Meets'){
			var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
			/*if(nickname=='STAR')
			{
			var url = 'star_survey_report_technical_meets_modified.php';
			}
			else
			{*/
				var url = 'star_survey_report_technical_meets_modified_two.php';
			//}
			//alert('Work In Progress');
			//return false;
		}
		else if(survey_type == 'Branding Verification'){
			var url = 'star_survey_report_branding_verification.php';
		}
		else if(survey_type == 'Dhalai Services'){
			var url = 'dhalai_services_data.php';
		}
		else if(survey_type == 'Site Lead and Conversion Tracking'){
			var url = 'site_lead_conversion_data.php';
		}
		else if(survey_type == 'Corporate Branding'){
			var url = 'corporate_branding_data.php';
		}
		else if(survey_type == 'Lead Generation'){
			var url = 'lead_generation_data_new_test.php';
		}
		else if(survey_type == 'Complaint Report'){
			var url = 'complaint_report.php';
		}
		else if(survey_type == 'Influencer'){
			var url = 'Influencer.php';
		}
		else if(survey_type == 'Mason Skill Building Program'){
			var url = 'Influencer.php';
		}else if(survey_type == 'lead Test'){
			var url = 'Influencer.php';
		}
		else if(survey_type == 'Counter Visit'){
			var url = 'counter_visit_data.php';
		}
		else if(survey_type == 'MLE Site Visit'){
			var url = 'counter_visit_data.php';
		}else if(survey_type == 'MTL Testing Format'){
			var url = 'mtl_testing_data.php';
		}
		else if(survey_type == 'Quality Complaint'){
			var url = 'quality_complaint_data.php';
		}
			
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		
		//alert(url);
		GenericAjaxFunction(''+url+'?employee='+employee+'&survey_type='+survey_type+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department+'&technical_meet_type='+technical_meet_type,'display',0);
		
		/*if(survey_type == 'KYC' )
		document.getElementById("print_export").hidden = false;*/
	}
	
	function PrintElem(elem)
	{
		var report_name = document.getElementById("survey_type").value;
		if(report_name == 'Branding' || report_name == 'Site Visit')
			var displaydivval = 'display_details';
		else
			var displaydivval = 'display';
		
		var displaydiv = document.getElementById(displaydivval).innerHTML;	
		Popup(displaydiv);
	   //Popup($(elem).html());
	}
    function UpdateElem(trans_id) {
        var aa = document.getElementById('aa_'+trans_id).value;
        var bb = document.getElementById('bb_'+trans_id).value;
        var cc = document.getElementById('cc_'+trans_id).value;
        var dd = document.getElementById('dd_'+trans_id).value;
        var ee = document.getElementById('ee_'+trans_id).value;
        var ff = document.getElementById('ff_'+trans_id).value;
        
        var gg = document.getElementById('gg_'+trans_id).value;
        var hh = document.getElementById('hh_'+trans_id).value;
        var ii = document.getElementById('ii_'+trans_id).value;
        var jj = document.getElementById('jj_'+trans_id).value;
        var kk = document.getElementById('kk_'+trans_id).value;
        var ll = document.getElementById('ll_'+trans_id).value;
        var mm = document.getElementById('mm_'+trans_id).value;
        
        //alert("."+aa+"."+bb+"."+cc+"."+dd+"."+ee+"."+ff);
       if(aa==""){
           alert("Select Sales ORG");
           return
       }
       if(bb==""){
           alert("Select Sales Division");
           return
       }
       if(cc==""){
           alert("Select Sales Distribution channel");
           return
       }
       if(dd==""){
           alert("Select Sales Document type");
           return
       }
       /*if(ee==""){
           alert("Select Sales Customer reference No");
           return
       }*/
       /*if(ff==""){
           alert("Select Sales Customer reference date");
           return
       }*/
       if(gg==""){
           alert("Select Sales Valid to date");
           return
       }
       if(hh==""){
           alert("Select Sales Material number");
           return
       }
       if(ii==""){
           alert("Select Sales Sold to party code");
           return
       }
       if(jj==""){
           alert("Select Sales ship to party code");
           return
       }
       if(kk==""){
           alert("Select Sales PO method");
           return
       }
        jQuery.ajax({
            url: 'lead_generation_update.php',
    		type:'json',
            method: 'POST',
            data: { trans_id: trans_id, aa: aa, bb:bb, cc:cc, dd:dd, ee:ee,ff:ff,gg:gg,hh:hh,ii:ii,jj:jj,kk:kk, ll:ll, mm:mm },
            success: function(response) {
                /*$("#no_"+"<?php echo $trans_id; ?>").text=response;*/
                alert("success."+response);
            },
            error: function(error) {
                // Handle any errors
                alert("error."+error);
            }
        });
        
       
    
        
        
    }
	function Popup(data) 
	{
		var report_name = document.getElementById("survey_type").value;
		var mywindow = window.open('', ''+report_name+'', 'height=400,width=600');
		mywindow.document.write('<html><head><title>'+report_name+'</title>');
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
		//alert(divid);
			//getting values of current time for generating the file name
			//document.getElementById("customer_id").removeAttribute("text");
			var report_name = document.getElementById("survey_type").value;
			if(report_name == 'Branding' || report_name == 'Site Visit')
				var display_div = 'display_details';
			else
				var display_div = 'display';
			var get_report_name = document.getElementById("report_name").value
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
			var table_div = document.getElementById(''+display_div+'');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			//var table_html = table_div.outerHTML.replace(/Customer Code/gi, '');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = ''+report_name+'' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	
	function show_option(survey_type){
		var survey_type = survey_type;
		//alert(survey_type);
		if(survey_type == 'KYC'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Site Lead and Conversion Tracking'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Lead Generation'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Corporate Branding'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Branding'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Site Visit'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Branding Verification'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Complaint Report'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		if(survey_type == 'Technical Meets'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = false;
		}
		else if(survey_type == 'Influencer'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}else if(survey_type == 'Mason Skill Building Program'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}else if(survey_type == 'Counter Visit'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}else if(survey_type == 'MLE Site Visit'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}else if(survey_type == 'MTL Testing Format'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		else if(survey_type == 'Quality Complaint'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		
	}
	
	function get_visit_details(visit_type,start_date,end_date,employee,survey_type){
		//alert(visit_type+month_data+employee);
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		
		var employee = encodeURIComponent(employee);
		//alert(visit_type);
		//alert(survey_type);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_survey_report_branding_details.php?start_date='+start_date+'&end_date='+end_date+'&visit_type='+visit_type+'&employee='+employee+'&survey_type='+survey_type+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display_details',0);
		//document.getElementById("print_export").hidden = false;
	}
	
	function site_visit_details(visit_status,survey_date,employee,survey_type){
		//alert(visit_status+survey_date+employee);
		var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		/*if(nickname=='STAR')
			{
		GenericAjaxFunction('star_survey_report_site_visit_data.php?visit_status='+visit_status+'&survey_date='+survey_date+'&employee='+employee+'&survey_type='+survey_type+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department+'&start_date='+start_date+'&end_date='+end_date,'display_details',0);
			}
			else
			{*/
			GenericAjaxFunction('star_survey_report_site_visit_data_modified.php?visit_status='+visit_status+'&survey_date='+survey_date+'&employee='+employee+'&survey_type='+survey_type+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department+'&start_date='+start_date+'&end_date='+end_date,'display_details',0);

			//}
		//document.getElementById("print_export").hidden = false;
	}
	function site_visit_details_all(visit_status,start_date,end_date,employee,survey_type){
		var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		/*if(nickname=='STAR')
			{
		GenericAjaxFunction('star_survey_report_site_visit_data.php?visit_status='+visit_status+'&start_date='+start_date+'&end_date='+end_date+'&employee='+employee+'&survey_type='+survey_type,'display_details',0);
			}
			else
			{*/
			GenericAjaxFunction('star_survey_report_site_visit_data_modified.php?visit_status='+visit_status+'&start_date='+start_date+'&end_date='+end_date+'&employee='+employee+'&survey_type='+survey_type,'display_details',0);

			//}
			
			

	}
	function access_add_edit(ID)
	{
		alert(ID);
		document.frm_opts.mode.value='insertcustomer';
		document.frm_opts.row_id.value=ID;
		document.frm_opts.submit();
	}
	function show_input_text(survey_id){
		document.getElementById("showflag_"+survey_id).style.display='none';
		document.getElementById("updateflag_"+survey_id).style.display='';
	}
	function text_blank(survey_id)
	{
		document.getElementById("dns_customer_code_"+survey_id).value='';
	}
   function activate_KYC(survey_id){
	   	var survey_type = document.getElementById("survey_type").value;
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		var customer_code = document.getElementById("dns_customer_code_"+survey_id).value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		var employee = document.getElementById("employee").value;	
		//alert(customer_code);	
		var url = 'star_survey_report_KYC_modified.php';
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction(''+url+'?employee='+employee+'&survey_type='+survey_type+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department+'&survey_id='+survey_id+'&customer_code='+customer_code+'&opt_type=activateKYC','display',0);
		/*if(survey_type == 'KYC' )
		document.getElementById("print_export").hidden = false;*/
	}
	</script>
    <?php
}

function update_record($transid,$aa)
{
    
 //echo $aa;
$sql="update $lead_generation_master set `sales_org`='$aa' where `lead_generation_id`='$transid'";

mysqli_query($conn,$sql);
}
function insert_record($row_id)
{
	$survey_id=$row_id;
	$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
	$res_survey_details = mysqli_query($link,$sql_survey_details);
	while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
		$survey_row_id = $row_survey_details['row_id'];
		$survey_value = $row_survey_details['value'];
		$emp_code=substr($survey_id,2,5);
			if($survey_row_id == 'RA004')
				$customer_name = $survey_value;
			else if($survey_row_id == 'RA005')
				$contact = $survey_value;
			else if($survey_row_id == 'RA006')
				$phone_no = $survey_value;
			else if($survey_row_id == 'RA007')
				$address = $survey_value;
			else if($survey_row_id == 'RA008')
				$pin = $survey_value;
			else if($survey_row_id == 'RA011')
				$category = $survey_value;
			else if($survey_row_id == 'RA012')
				$cus_type = $survey_value;
			else if($survey_row_id == 'RA013')
				$route_name = $survey_value;
			else if($survey_row_id == 'RA060')
				$linked_dealer = $survey_value;
			else if($survey_row_id == 'RA060')
				$branch_name = $survey_value;	
	}
			$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."'";
			$rsbranchcode=mysqli_query($link,$sqlbranchcode);
			$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
			$branch_code=$rowbranchcode['branch_code'];
			
			$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
			$rsroutechk=mysqli_query($link,$sqlroutechk);
			$rowroutechk=mysqli_fetch_assoc($rsroutechk);
			$route_code=$rowroutechk['route_code'];
			
			$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($linked_dealer)."' AND acedns='Y' 
						AND cust_type IN('Dealer')";
			$rsrdscode=mysqli_query($link,$sqlrdscode);
			$rowrdscode=mysqli_fetch_assoc($rsrdscode);
			$rds_code=$rowrdscode['customer_code'];

			$sqlmaxdnscustomercode="SELECT MAX(dns_customer_code) AS max_dns_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'KYC%'";
			$rsmaxdnscustomercode=mysqli_query($link,$sqlmaxdnscustomercode);
			$rowmaxdnscustomercode=mysqli_fetch_assoc($rsmaxdnscustomercode);
			$max_dns_customer_code=$rowmaxdnscustomercode['max_dns_customer_code'];
			
			if($max_dns_customer_code=='')
			{
				$max_dns_customer_code='KYC1';
			}
			else
			{
				$max_dns_customer_code++;
			}
			$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
			$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
			$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
			$max_customer_code=$rowmaxcustomercode['max_customer_code'];
			
			if($max_customer_code=='')
			{
				$max_customer_code='C/0000001';
			}
			else
			{
				$max_customer_code++;
			}
			$sql  = "insert into customer_master ";
			$sql .= " SET customer_code='".$max_customer_code."'";
			$sql .= " , dns_customer_code='".$max_dns_customer_code."'";
			$sql .= " , customer_name='".addslashes($customer_name)."'";
			$sql .= " , branch_code='".addslashes($branch_code)."'";
			$sql .= " , phone_no='".$phone_no."'";
			$sql .= " , route_code='".$route_code."'";
			$sql .= " , acedns='Y'";
			$sql .= " , black_list='N'";
			$sql .= " , rds_tag='".$rds_code."'";
			$sql .= " , cust_type='".$cus_type."'";
			$sql .= " , address='".$address."'";
			$sql .= " , pin='".addslashes($pin)."'";
			$sql .= " , download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sql);
			$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$max_customer_code."',
									 route_code='".$route_code."',
									 emp_code='".$emp_code."',
									 acedns='Y',
									download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlinsertcustomerroute);

	$GLOBALS['err_msg']="KYC activated successfully.";
	disphtml("main();");
}
?>