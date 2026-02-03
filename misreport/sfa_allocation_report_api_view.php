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
//echo"<pre>";print_r('ss3');die;
if($_SESSION['admin_login']=="")  		header("product:index.php");
//echo"<pre>";print_r($_SESSION);die;

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
    if($_SESSION['level']=="L3"){
        // API endpoint
        $sathi_url="https://starsaathi.com/SAP/";
        $apiUrl = $sathi_url."sfa_allocation_report_api.php?";
        //echo"<pre>";print_r($apiUrl);die;
        // Parameters
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 100;
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
            'month' => $month,
            'emp_code' => $_SESSION['admin_login']
        ]);

        // Fetch data
        $response = file_get_contents("$apiUrl,$query");
        //echo"<pre>";print_r($query);die;
        //echo"<pre>";print_r($apiUrl);die;
        //echo"<pre>";print_r($response);die;
        $data = json_decode($response, true);
        //echo"<pre>";print_r($data);die;

        $records = $data['data'] ?? [];
        $totalRecords = $data['total_records'] ?? 0;
        $totalPages = ceil($totalRecords / $limit);
        //echo"<pre>";print_r($totalRecords);die;

	?>
        <h2>Allocation report invoice wise</h2>

            <form method="GET">
                    <?php
                    // URL of the API providing branch data
                    $api_url = $sathi_url.'sfa_allocation_branch_details_api.php?emp_code='.$_SESSION['admin_login'];

                    // Fetch API response
                    $response = file_get_contents($api_url);

                    // Decode JSON response
                    $data = json_decode($response, true);
                    //echo"<pre>";print_r($data);die;
                    ?>
                <!--  <input type="text" name="sl_branch" value="<?= htmlspecialchars($sl_branch) ?>"> -->
                Branch:
                <select name="sl_branch" id="sl_branch">
                    <option value="">Select Branch</option>
                    <?php
                    if ($data && $data['status'] === 'success') {
                        foreach ($data['data'] as $branch) {
                            $branch_code = htmlspecialchars($branch['branch_code']);
                            $branch_name = htmlspecialchars($branch['branch_name']);
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
            <?php if (!empty($records)): ?>
            <div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="right">
                <a href="sfa_allocation_report_api_export.php?sl_branch=<?php echo $_GET['sl_branch'] ?>&srch_linked_dealer=<?php echo $_GET['srch_linked_dealer'] ?>&srch_sub_dealer=<?php echo $_GET['srch_sub_dealer'] ?>&month=<?php echo $_GET['month'] ?>" class="btn btn-default" value="Export" style="align='center'" >
                    <button >Export</button>
                </a>
                <a href="sfa_allocation_report_api_view.php" class="btn btn-default" value="Export" style="align='center'" >
                    <button >Reset</button>
                </a>
            </div>
            <?php endif ?>
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
            <?php else:
                if(isset($_GET['sl_branch']) || isset($_GET['srch_linked_dealer']) ||isset($_GET['srch_sub_dealer']) ||isset($_GET['month'])){
                echo "Data Not Found";} endif; ?>
            <div class="pagination">
                <?php
                /// Get current page and total pages dynamically
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $totalPages = $totalPages; // You must define this earlier from your data

        // Previous button
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) . '">&laquo; Prev</a>';
        }

        // Always show first page if page > 1
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="' . ($page == 1 ? 'active' : '') . '">1</a>';
        }

        // Determine start and end of visible range
        $start = max(2, $page - 2);
        $end = min($totalPages - 1, $page + 2);

        // Ellipsis before the range
        if ($start > 2) {
            echo '<span class="ellipsis">...</span>';
        }

        // Visible page numbers
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="' . $active . '">' . $i . '</a>';
        }

        // Ellipsis after the range
        if ($end < $totalPages - 1) {
            echo '<span class="ellipsis">...</span>';
        }

        // Always show last page
        if ($totalPages > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '" class="' . ($page == $totalPages ? 'active' : '') . '">' . $totalPages . '</a>';
        }

        // Next button
        if ($page < $totalPages) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page + 1])) . '">Next &raquo;</a>';
        }
                ?>
            </div>
        <?php
    }else{
        echo "Please contact admin";
    }
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
        <style>
        .pagination {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    padding: 20px 0;
    gap: 6px;
}

.pagination a,
.pagination span.ellipsis {
    padding: 8px 14px;
    text-decoration: none;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #007bff;
    transition: background-color 0.3s ease;
}

.pagination a:hover {
    background-color: #f0f0f0;
}

.pagination a.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
    font-weight: bold;
}

.pagination .ellipsis {
    color: #777;
    pointer-events: none;
}

        </style>
    <?php
}
?>
