<?php

/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/

ob_start();
session_start();
//echo"<pre>";print_r($_SESSION);die;
if(strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' && strtoupper($_SESSION['nick_name']) == 'STAR')
{
	require("adminUtils_accounts.php");
}
else
{
	require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	 require("include/dbcon.php");

   
	date_default_timezone_set("Asia/Kolkata");
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
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
	$hidden = " hidden";
	echo "<center>";
        // API endpoint
        $sathi_url="https://dev.starsaathi.com/";
        $apiUrl = $sathi_url."sfa_allocation_report_api.php?emp_code=".$_SESSION['admin_login'];

        // Parameters
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $sl_branch = $_GET['sl_branch'] ?? '';
        $srch_linked_dealer = $_GET['srch_linked_dealer'] ?? '';
        $srch_sub_dealer = $_GET['srch_sub_dealer'] ?? '';
        $month = $_GET['month'] ?? '';

        // Build query
        $query = http_build_query([
            
            'start_from' => $offset,
            'limit' => $limit,
            'sl_branch' => $sl_branch,
            'srch_linked_dealer' => $srch_linked_dealer,
            'srch_sub_dealer' => $srch_sub_dealer,
            'month' => $month
        ]);

        // Fetch data
        $response = file_get_contents("$apiUrl,$query");
        //echo"<pre>";print_r($response);die;
        $data = json_decode($response, true);
        $records = $data['data'] ?? [];
        $totalRecords = $data['total_pages'] ?? 0;
        $totalPages = ceil($totalRecords / $limit);
	?>
    <h2>Dealer Allocation Report</h2>

        <form method="GET">
                <?php
                // URL of the API providing branch data
                $api_url = $sathi_url.'sfa_allocation_branch_details_api.php';

                // Fetch API response
                $response = file_get_contents($api_url);

                // Decode JSON response
                $data = json_decode($response, true);
                ?>
            <!--  <input type="text" name="sl_branch" value="<?= htmlspecialchars($sl_branch) ?>"> -->
            Branch:
            <select name="sl_branch" id="sl_branch">
                <option value="">Select Branch</option>
                <?php
                if ($data && $data['status'] === 'success') {
                    foreach ($data['data'] as $branch) {
                        $branch_code = htmlspecialchars($branch['code']);
                        $branch_name = htmlspecialchars($branch['name']);
                        $selected = ($branch_code === $sl_branch) ? 'selected' : '';
                        echo "<option value=\"$branch_code\" $selected>$branch_name</option>";
                    }
                } else {
                    echo "<option value=\"\">Unable to load branches</option>";
                }
                ?>
            </select>
            Linked Dealer: <input type="text" name="srch_linked_dealer" value="<?= htmlspecialchars($srch_linked_dealer) ?>">
            Sub Dealer: <input type="text" name="srch_sub_dealer" value="<?= htmlspecialchars($srch_sub_dealer) ?>">
            Month (YYYY-MM):
           <?php $eash_year_month_arr = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
                $the_year_month_sl_arr = array();
                $from_sel_year = 2023;
                $to_sel_year = date("Y");
                for($i=$from_sel_year;$i<=$to_sel_year;$i++){

                foreach($eash_year_month_arr as $eyma_key=>$eash_year_month_arr_val){
                $the_sl_ym_key = $i."-".$eyma_key;
                $the_sl_ym_val_text = $eash_year_month_arr_val." ".$i;
                $the_year_month_sl_arr[$the_sl_ym_key] = $the_sl_ym_val_text;
                    
                }
                    
                }?>
            <!--  <input type="text" name="month" value="<?= htmlspecialchars($month) ?>"> -->
            <select name="month" id="month" class="form-control">
                <option value="">Select Month</option>
                <?php
                // Loop through the array to generate options
                foreach ($the_year_month_sl_arr as $tymsa_key=>$tymsa_val) { ?>
                    <option value="<?php echo $tymsa_key; ?>" <?php if ($tymsa_key == $month) { ?> selected="selected" <?php } ?>><?php echo $tymsa_val; ?></option>
                <?php } ?>
            </select>
            <button type="submit">Search</button>
        </form>
        <br><br><br><br>
        <div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="center">	

        <?php if (!empty($records)): ?>
        <table border="1" style="border-collapse:collapse;" class="border" width="100%">
            <thead>
                <tr class="TDHEAD">
                    <th class="id-col">Allocation Date Time</th>
                    <th>APPORDERNO</th>
                    <th>Inv Date</th>
                    <th>Linked Dealer Code</th>
                    <th>Linked Dealer SAP Code</th>
                    <th>Linked Dealer Name</th>
                    <th>Sub Dealer / RSSD Code</th>
                    <th>Sub Dealer / RSSD SAP Code</th>
                    <th>Sub Dealer / RSSD Name</th>
                    <th>Branch</th>
                    <th>Month</th>
                    <th>Product Name</th>
                    <th>Total Inv qty</th>
                    <th>Allocated qty.</th>
                    <th>Remaining Allocation qty.</th>
                    <th>Inv No.</th>
                    <th>Inv Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="17">No records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date_and_time']) ?></td>
                            <td><?= htmlspecialchars($row['APPORDERNO']) ?></td>
                            <td><?= htmlspecialchars($row['inv_date']) ?></td>
                            <td><?= htmlspecialchars($row['linked_dealer_code']) ?></td>
                            <td><?= htmlspecialchars($row['linked_dealer_sap_code']) ?></td>
                            <td><?= htmlspecialchars($row['linked_dealer_name']) ?></td>
                            <td><?= htmlspecialchars($row['sub_dealer_rssd_code']) ?></td>
                            <td><?= htmlspecialchars($row['sub_dealer_rssd_sap_code']) ?></td>
                            <td><?= htmlspecialchars($row['sub_dealer_rssd_name']) ?></td>
                            <td><?= htmlspecialchars($row['branch']) ?></td>
                            <td><?= htmlspecialchars($row['month']) ?></td>
                            <td><?= htmlspecialchars($row['prod_display_name']) ?></td>
                            <td><?= htmlspecialchars($row['total_inv_qty']) ?></td>
                            <td><?= htmlspecialchars($row['total_allocation_qty']) ?></td>
                            <td><?= htmlspecialchars($row['remaining_allocation_qty']) ?></td>
                            <td><?= htmlspecialchars($row['inv_no']) ?></td>
                            <td><?= htmlspecialchars($row['inv_cancl']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php endif; ?>
    <div class="pagination">
    <?php /*for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; */ ?>
    </div>
    <?php
	echo "</center>";
	?>
    
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
		if(document.getElementById("sale_access").value.search(/\S/) == -1){
			alert('Please Select Department');
			return false;
		}
		if(document.getElementById("emp_access").value.search(/\S/) == -1){
			alert('Please Select Employee Access');
			return false;
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		var employee = document.getElementById("employee").value;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_star.php?employee='+employee,'display',0);
		document.getElementById("print_export").hidden = true;
	}
	
	function show_mis_details(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_details_star.php?employee='+employee+'&val='+val,'display_details',0);
		//document.getElementById("print_export").hidden = false;
	}
	
	function show_date_range_control(){
		document.getElementById("custom_date_div").hidden = false;
	}
	
	function show_datewisedata(employee){
		
		var mode = 'datewise';
		var start_date = document.getElementById("start_date_val").value;
		var end_date = document.getElementById("end_date_val").value;
				
		var today = new Date();
		var today_date = today.toISOString().substring(5, 10);
		var current_month = today.toISOString().substring(5, 7);
		var current_year = today.toISOString().substring(0, 4);
		//alert(start_date.substring(0, 4));
		
		/*----> FINANCIAL YEAR CHECKING <----*/
		var financial_year_limit=(current_year-1)+'-04'+'-01';
		
		if(start_date < financial_year_limit)
		{
			alert('Please Select Current or Previous Financial Year');
			return false;
		}
		if(start_date.substring(5, 7) <'04' &&  start_date.substring(0, 4)== current_year && end_date.substring(5, 7) >'03')
		{
			alert('Please Select Financial Year');
			return false;
		}
		if(start_date.substring(5, 7) >='04' &&  (start_date.substring(0, 4)== (current_year-1)) && end_date.substring(5, 7) >'03' && 
		(end_date.substring(0, 4)== current_year))
		{
			alert('Please Select Financial Year');
			return false;
		}
		/*if(current_month >= '04' && (start_date.substring(5, 7) <= '03' || end_date.substring(5, 7) <= '03')){
			alert('Please Select Financial Year');
			return false;
		}*/
				
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			return false;
		}
	
	if(document.getElementById("start_date_val").value.search(/\S/)==-1 && document.getElementById("end_date_val").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			return false;
		}
		
		document.getElementById("display").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_star.php?employee='+encodeURIComponent(employee)+'&mode=datewise&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
	function show_emp_data_details(mode,employee,start_date,end_date){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_details_star.php?employee='+employee+'&mode='+mode+'&start_date='+start_date+'&end_date='+end_date,'display_details',0);
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display_details").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'MIS Report Details', 'height=400,width=600');
		mywindow.document.write('<html><head><title>MIS Report Details</title>');
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
		var table_div = document.getElementById('display_details');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		//setting the file name
		a.download = 'MIS Report Details' + postfix + '.xls';
		//triggering the function
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		//just in case, prevent default behaviour
		//e.preventDefault();
	}
	</script>
    <style>
        .table {
            width: fit-content;
            block-size: fit-content;
        }

        .view {
        margin: auto;
        width: 600px;
        }

        .wrapper {
        position: relative;
        overflow: auto;
        border: 1px solid black;
        white-space: nowrap;
        }

        .sticky-col {
        position: -webkit-sticky;
        position: sticky;
        background-color: #A92A61;
        }
        .sticky-cols {
        position: -webkit-sticky;
        position: sticky;
        background-color: white;
        }

        .first-col {
        width: 100px;
        min-width: 100px;
        max-width: 100px;
        left: 0px;
        }

        .second-col {
        width: 150px;
        min-width: 150px;
        max-width: 150px;
        left: 100px;
        }
        .id-col {
        width: 150px;
        min-width: 150px;
        max-width: 150px;
        left: 2px;
        }
        .max-col {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
        left: 2px;
        }
        </style>
    <?php
}
?>
