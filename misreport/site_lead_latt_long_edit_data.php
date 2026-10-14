<?php
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$emp_code = $_REQUEST['employee'];

// Builds the correct WHERE fragment for a filter value, since the different
// dropdowns on this page don't all submit values in the same shape:
//  - "all" / empty  -> no filter on this column
//  - already contains a quote, e.g. 'AP','ASSAM' or 'E2100' -> use IN (...)
//  - plain value(s), e.g. B214 -> use FIND_IN_SET (matches original branch logic)
function build_filter_condition($column, $value)
{
	$value = trim($value);
	if ($value === '' || strtolower($value) === 'all') {
		return "1=1";
	}
	if (strpos($value, "'") !== false) {
		return $column." IN (".$value.")";
	}
	return "FIND_IN_SET(".$column.",'".$value."')";
}

$where = build_filter_condition('zone', $zone)
	." AND ".build_filter_condition('state', $state)
	." AND ".build_filter_condition('branch', $branch)
	." AND ".build_filter_condition('emp_code', $emp_code);

$sqlsitelead = "SELECT id, emp_code, emp_name, cust_name, cust_phn_no, address, longitude, latitude
                FROM new_site_lead_master
                WHERE ".$where."
                ORDER BY cust_name ASC";
//  echo $sqlsitelead;
$ressitelead = mysqli_query($link,$sqlsitelead) or die(mysqli_error($link)." Error in fetching site lead data.");
$totalsitelead = mysqli_num_rows($ressitelead);

if($totalsitelead > 0){
	$count = 1;
	?>
	<table border="1" style="border-collapse:collapse;" class="border" width="90%">
	  <tr class="TDHEAD_SUB">
	  	<td colspan="9" align="center">Site Lead Information</td>
	  </tr>
	  <tr class="TDHEAD" align="center">
		<td width="4%">SI</td>
		<td width="10%">Emp Code</td>
		<td width="14%">Emp Name</td>
		<td width="16%">Customer Name</td>
		<td width="10%">Phone No</td>
		<td width="20%">Address</td>
		<td width="8%">Longitude</td>
		<td width="8%">Latitude</td>
		<td width="10%">EDIT LOCATION</td>
	  </tr>
	<?php
	while($rowsitelead = mysqli_fetch_assoc($ressitelead)){
		$id = $rowsitelead['id'];
		$emp_code_row = $rowsitelead['emp_code'];
		$emp_name = $rowsitelead['emp_name'];
		$cust_name = $rowsitelead['cust_name'];
		$cust_phn_no = $rowsitelead['cust_phn_no'];
		$address = $rowsitelead['address'];
		$longitude = $rowsitelead['longitude'];
		$latitude = $rowsitelead['latitude'];
		echo "<tr>
				<td width=\"4%\">".$count."</td>
				<td width=\"10%\">".htmlspecialchars($emp_code_row)."</td>
				<td width=\"14%\">".htmlspecialchars($emp_name)."</td>
				<td width=\"16%\">".htmlspecialchars($cust_name)."</td>
				<td width=\"10%\">".htmlspecialchars($cust_phn_no)."</td>
				<td width=\"20%\">".htmlspecialchars($address)."</td>
				<td width=\"8%\">".htmlspecialchars($longitude)."</td>
				<td width=\"8%\">".htmlspecialchars($latitude)."</td>
				<td align=\"center\" width=\"10%\"><a href=\"javascript:access_add_edit_sitelead('".$id."');\" title=\"Edit site lead location\" style=\"color: #F00;\"><img src=\"images/edit_icon.gif\" alt=\"\" /></a></td>
			  </tr>";
		$count++;
	}
	?>
	</table>
	<?php
}
else{
	echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
}
?>