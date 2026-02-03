<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name']) == 'EMAMI' && (strtoupper($_SESSION['admin_login'])=="ED01" ||strtoupper($_SESSION['admin_login'])=="E0193")){
		require("adminUtils_HBC_SFATS.php");
	}
	else
	{
	if(strtoupper($_SESSION['nick_name']) == 'TECPL')
	{
		require("adminUtils_tecpl.php");
	}
	else
	{
		if($_SESSION['admin_login']=="E0674" && (strtoupper($_SESSION['nick_name']) == 'STAR')) require("adminUtils_accounts.php");
		else 								  require("adminUtils.php");
	}
	}

	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$GLOBALS['show']=30;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
	$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	$mode = $_REQUEST['mode'];
	

	if($mode =='add' || $mode =='edit')				disphtml("show_add_edit($_REQUEST[row_id]);");
	elseif($mode == 'save')							save_record($_REQUEST['prod_id']);
	elseif($mode =='change_status')					audit_status($_REQUEST['row_id']);
	elseif($mode =='delete_rec')					   delete_record($_REQUEST['row_id']);
	else    										   disphtml("main();");
ob_end_flush();

function main()
{
	$date = $_REQUEST['date'];
	if($_REQUEST['date']=="")  		$date = date('d-m-Y');
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND LO.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script language="javascript">
<?php 

$query = "SELECT * FROM `attendence` WHERE `date` BETWEEN '2023-11-23 00:00:00' AND '2023-11-25 23:59:59'";

$result = mysqli_query($link,$query);

if (mysqli_num_rows($result) > 0) {
    // Set the CSV file name
    $csvFileName = 'exported_data.csv';

    // Open the file for writing
    $csvFile = fopen($csvFileName, 'w');

    // Write the data rows directly
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($csvFile, $row);
    }

    // Close the file
    fclose($csvFile);

    // Provide the file for download
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . $csvFileName . '"');
    readfile($csvFileName);

    // Delete the temporary CSV file
    unlink($csvFileName);
} else {
    echo "No records found";
}

// Close the database connection
mysqli_close($conn);

?>

<?php }//End of main()?>>>>>>>