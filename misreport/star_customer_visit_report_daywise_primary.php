<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
    require("include/dbcon.php");
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
	$hidden = "";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">CUSTOMER VISIT REPORT DAYWISE - PRIMARY</span><br><br>";
	attribute_selection($hidden,$create_control='');
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
<div id="loader" style="display:none"><br/><center><img src="ajax-loader.gif" /></center></div>
    <div id="display" style="max-height: 450px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
	// ------------------------------------------------------------------
	// An "Employee Type" row is added just below Department, inside the
	// existing Select Criteria table.  Department is NOT auto selected -
	// the row is shown only when the user picks Department = Primary and
	// stays hidden for every other department (BD, Branding, BRANDING
	// VERIFICATION, Branding verification vendor, Logistics,
	// PROJECT LOTUS, SURVEY, All, ...).
	// ------------------------------------------------------------------
	var user_type_html =
		'<select name="user_type" id="user_type" style="width:150px;" onchange="ut_user_type_changed();">'
		+ '<option value="">All</option>'
		+ '<option value="Trade Sales">Trade Sales</option>'
		+ '<option value="IS">IS</option>'
		+ '<option value="SBS">SBS</option>'
		+ '<option value="SP">SP</option>'
		+ '<option value="Survey">Survey</option>'
		+ '</select>';

	var ut_row_node = null;   // the Employee Type row (or fallback span)

	function ut_parent_tag(el, tag){
		while(el && el.nodeName != tag) el = el.parentNode;
		return el;
	}

	function ut_find_department(){
		var dept = document.getElementById("sale_access");
		if(dept) return dept;
		var sels = document.getElementsByTagName("select");
		for(var i = 0; i < sels.length; i++){
			var nm = ((sels[i].id || '') + ' ' + (sels[i].name || '')).toLowerCase();
			if(nm.indexOf("sale_access") != -1 || nm.indexOf("department") != -1) return sels[i];
		}
		return null;
	}

	// Employee Type belongs to Primary only. Any other department
	// (BD, Branding, BRANDING VERIFICATION, Branding verification vendor,
	//  Logistics, PROJECT LOTUS, SURVEY, All, nothing selected) hides it.
	function ut_is_primary_selected(){
		var dept = ut_find_department();
		if(!dept || dept.selectedIndex < 0) return false;
		var val = (dept.value || '').replace(/'/g, "").replace(/^\s+|\s+$/g, "");
		if(val == '' || val.indexOf(",") != -1) return false;   // Select / All
		if(val.toUpperCase() == "PRIMARY") return true;
		var txt = dept.options[dept.selectedIndex].text.replace(/^\s+|\s+$/g, "");
		return txt.toUpperCase() == "PRIMARY";
	}

	// Always re-locate the row from the live #user_type element, so a stale
	// or lost reference can never leave the row stuck on screen.
	function ut_get_row_node(){
		if(ut_row_node && ut_row_node.nodeName == "SPAN") return ut_row_node;   // fallback layout
		var sel = document.getElementById("user_type");
		if(sel){
			var tr = ut_parent_tag(sel, "TR");
			if(tr) return tr;
		}
		return ut_row_node;
	}

	function ut_sync_visibility(){
		var node = ut_get_row_node();
		if(!node) return;
		var show = ut_is_primary_selected();
		var want = show ? "" : "none";
		if(node.style.display != want) node.style.display = want;
		if(!show){
			// Hidden -> drop the filter so the Employee list stays complete
			var sel = document.getElementById("user_type");
			if(sel && sel.selectedIndex != 0) sel.selectedIndex = 0;
			if(ut_emp_codes_map != null){
				ut_emp_codes_map = null;
				ut_apply_emp_filter();
			}
		}
	}

	// Listen on the Department select without disturbing its own inline
	// onchange (which reloads the Employee list).
	function ut_hook_department(dept){
		if(dept.getAttribute("data-ut-hooked")) return;
		dept.setAttribute("data-ut-hooked", "1");
		if(dept.addEventListener)
			dept.addEventListener("change", function(){ setTimeout(ut_sync_visibility, 0); }, false);
		else if(dept.attachEvent)
			dept.attachEvent("onchange", function(){ setTimeout(ut_sync_visibility, 0); });
	}

	function add_user_type_filter(){
		var dept = ut_find_department();
		if(!dept || dept.options.length <= 1) return;
		ut_hook_department(dept);
		if(document.getElementById("user_type")){
			ut_sync_visibility();
			return;
		}

		var cell = ut_parent_tag(dept, "TD");
		var row  = ut_parent_tag(dept, "TR");

		if(row && row.parentNode && row.cells.length > 1){
			// Same layout as the other criteria rows: label cell + field cell
			var new_row    = document.createElement("TR");
			var label_cell = document.createElement("TD");
			var field_cell = document.createElement("TD");
			var src_label  = row.cells[0];

			label_cell.className = src_label.className;
			label_cell.setAttribute("align", src_label.getAttribute("align") || "right");
			label_cell.setAttribute("style", src_label.getAttribute("style") || "");
			label_cell.innerHTML = "Employee Type:";

			if(cell){
				field_cell.className = cell.className;
				field_cell.setAttribute("align", cell.getAttribute("align") || "left");
				field_cell.setAttribute("style", cell.getAttribute("style") || "");
			}
			field_cell.innerHTML = user_type_html;

			new_row.appendChild(label_cell);
			new_row.appendChild(field_cell);
			for(var c = 2; c < row.cells.length; c++) new_row.appendChild(document.createElement("TD"));
			row.parentNode.insertBefore(new_row, row.nextSibling);
			ut_row_node = new_row;
		}

		if(!document.getElementById("user_type")){
			// Fallback: criteria block is not a normal table row — show it next to Department
			var holder = cell || dept.parentNode;
			if(holder){
				var span = document.createElement("span");
				span.innerHTML = '<br><b>Employee Type:</b>&nbsp;' + user_type_html;
				holder.appendChild(span);
				ut_row_node = span;
			}
		}
		ut_sync_visibility();
	}
	// ------------------------------------------------------------------
	// User Type filters the Employee dropdown on employee_master.level
	//   Trade Sales -> L1..L8, MIS      IS  -> NT, NT_TO
	//   SBS         -> SBS, BM          SP  -> SP        Survey -> PS
	// The level -> code list comes from get_saleaccess_related_data_primary.php
	// ------------------------------------------------------------------
	var ut_emp_codes_map = null;   // null = All (no filtering)
	var ut_emp_full_html = '';
	var ut_last_emp_node = null;

	function ut_apply_emp_filter(){
		var sel = document.getElementById("employee");
		if(!sel) return;
		sel.innerHTML = ut_emp_full_html;      // always start from the full list
		if(ut_emp_codes_map == null) return;

		var kept = [];
		for(var i = sel.options.length - 1; i >= 0; i--){
			var opt = sel.options[i];
			if(opt.value == '') continue;                  // "Select"
			if(opt.text == 'All') continue;                // handled below
			var code = opt.value.replace(/'/g, "");
			if(ut_emp_codes_map[code]) kept.push("'" + code + "'");
			else sel.remove(i);
		}
		kept.reverse();
		for(var j = 0; j < sel.options.length; j++){
			if(sel.options[j].text == 'All'){
				sel.options[j].value = kept.join(",");
				break;
			}
		}
		sel.selectedIndex = 0;
	}

	// The employee dropdown is rebuilt by the zone/branch/department ajax.
	// When that happens the node is replaced, so re-take the full list and
	// re-apply whatever User Type is currently selected.
	function ut_watch_employee(){
		var sel = document.getElementById("employee");
		if(!sel) return;
		if(sel !== ut_last_emp_node){
			ut_last_emp_node = sel;
			ut_emp_full_html = sel.innerHTML;
			if(ut_emp_codes_map != null) ut_apply_emp_filter();
		}
	}

	function ut_user_type_changed(){
		var sel = document.getElementById("user_type");
		var val = sel ? sel.value : '';
		if(val == ''){                                     // All
			ut_emp_codes_map = null;
			ut_apply_emp_filter();
			return;
		}
		document.getElementById('loader').style.display = '';
		var xhr = GetXmlHttpObject();
		if(xhr == null){
			alert("Browser does not support HTTP Request");
			return;
		}
		xhr.open("POST", "get_saleaccess_related_data_primary.php", true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				document.getElementById('loader').style.display = 'none';
				var codes = xhr.responseText.replace(/^\s+|\s+$/g, "").split(",");
				ut_emp_codes_map = {};
				for(var i = 0; i < codes.length; i++){
					if(codes[i] != '') ut_emp_codes_map[codes[i]] = true;
				}
				ut_apply_emp_filter();
			}
		};
		xhr.send("type=user_type_emp&user_type=" + encodeURIComponent(val));
	}

	add_user_type_filter();
	ut_watch_employee();
	// Re-add / re-apply if the criteria block is rebuilt by the zone/state/branch ajax
	setInterval(function(){
		add_user_type_filter();
		ut_watch_employee();
	}, 800);

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
		if(document.getElementById("sale_access"))
		{
			if(document.getElementById("sale_access").value.search(/\S/) == -1){
				alert('Please Select Department');
				return false;
			}
			var department = document.getElementById("sale_access").value;

		}
		else var department='';
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		document.getElementById('loader').style.display='';
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		
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
		
		var employee = document.getElementById("employee").value;
		var partsstring = ",";
		//alert(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1);
		if(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1 )
		{
			alert("Please select one department then All employee");
			return false;
		}
		var user_type = document.getElementById("user_type") ? document.getElementById("user_type").value : '';

		document.getElementById("display_details").innerHTML = '';
		var url="star_customer_visit_report_daywise_data.php";
		var params = 'employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department+'&user_type='+encodeURIComponent(user_type);
		xmlHttpdisplay=GetXmlHttpObject()
		if (xmlHttpdisplay==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		xmlHttpdisplay.open("POST", url, true);
	
		//Send the proper header information along with the request
		xmlHttpdisplay.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		xmlHttpdisplay.onreadystatechange = function() {//Call a function when the state changes.
			if(xmlHttpdisplay.readyState == 4 && xmlHttpdisplay.status == 200) {
				var val=xmlHttpdisplay.responseText;
				if(val!="")
				 {
					 //alert("Success Download Started");
					 document.getElementById('loader').style.display='none';
					 document.getElementById('display').innerHTML=val;
				 }
			}
		}
		xmlHttpdisplay.send(params);
		document.getElementById("print_export").hidden = false;
		/*document.getElementById("print_export").hidden = false;
		//string.indexOf(substring) !== -1;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_customer_visit_report_daywise_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
		document.getElementById("print_export").hidden = false;*/
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'Actinable Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Actinable Report</title>');
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
	
	function exporttocsv(){
		var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	var a = document.createElement('a');
	
	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Customer Visit Report' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
		/*if(document.getElementById("zone").value.search(/\S/) == -1){
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
		if(document.getElementById("sale_access"))
		{
			if(document.getElementById("sale_access").value.search(/\S/) == -1){
				alert('Please Select Department');
				return false;
			}
			var department = document.getElementById("sale_access").value;

		}
		else var department='';
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		var employee = document.getElementById("employee").value;
		window.open('star_customer_visit_report_daywise_data_export.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'mywindow');*/
}
	</script>
    <?php
}
?>