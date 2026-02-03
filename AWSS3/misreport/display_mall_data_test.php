<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$condition = " WHERE mall_id LIKE 'M%' ";
	if($_GET['searchtype'])
	{
		$mall_name = $_GET['mall_name'];
		$searchtype = $_GET['searchtype'];
		if($searchtype == 'mall')
			$condition = " WHERE mall_name LIKE '%".$mall_name."%' AND mall_id LIKE 'M%' ";
		else if($searchtype == 'city')
			$condition = " WHERE city LIKE '%".$mall_name."%' AND mall_id LIKE 'M%' ";
	}
	if(strtoupper($_SESSION['admin_login']) == 'ADMIN')
		$colspan = 17;
	else
		$colspan = 16;
?>
<table border="1" style="border-collapse:collapse;" class="border" width="100%" cellpadding="4">
  <tr class="TDHEAD">
  	<td colspan="<?php echo $colspan; ?>" align="center">Mall Details</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>SI</td>
  	<td>Mall Name</td>
    <td>Street Number</td>
    <td>Address</td>
    <td>Landmark</td>
    <td>Area</td>
    <td>City</td>
    <td>Pincode</td>
    <td>State</td>
    <td>Country</td>
    <td>STD Code</td>
    <td>Closed On</td>
    <td>Upcoming Event</td>
    <td>Facility</td>
    <td>Assigned To</td>
    <td>Status</td>
    <?php if(strtoupper($_SESSION['admin_login']) == 'ADMIN'){ ?>
    <td>Action</td>
    <?php } ?>
  </tr>
<?php
	$count = 1;
	$sql_select_mall = "SELECT * FROM mall_master".$condition." ORDER BY mall_name ASC";
	$res_select_mall = mysql_query($sql_select_mall);
	while($row_select_mall = mysql_fetch_array($res_select_mall))
	{
		$assigned_emp = '';
		$sql_select_assigned_emp = "SELECT EM.emp_name FROM mall_emp_relation MER, employee_master EM WHERE mall_id = '".$row_select_mall['mall_id']."' AND MER.emp_code = EM.emp_code";
		$res_select_assigned_emp = mysql_query($sql_select_assigned_emp);
		while($row_select_assigned_emp = mysql_fetch_array($res_select_assigned_emp))
		{
			$assigned_emp .= $row_select_assigned_emp['emp_name'].",";
		}
		$assigned_emp = rtrim($assigned_emp,",");
		if($row_select_mall['status'] == 'enable'){
			$color = "#00EE00";
			$set_status = 'disable';
		}
		else{
			$color = "#FF3030";
			$set_status = 'enable';
		}
		echo "<tr>
				<td>".$count."</td>
				<td>".$row_select_mall['mall_name']."</td>
				<td>".$row_select_mall['street_number']."</td>
				<td>".$row_select_mall['address']."</td>
				<td>".$row_select_mall['landmark']."</td>
				<td>".$row_select_mall['area']."</td>
				<td>".$row_select_mall['city']."</td>
				<td>".$row_select_mall['pincode']."</td>
				<td>".$row_select_mall['state']."</td>
				<td>".$row_select_mall['country']."</td>
				<td>".$row_select_mall['std_code']."</td>
				<td>".$row_select_mall['closed_on']."</td>
				<td>".$row_select_mall['upcoming_event']."</td>
				<td>".str_replace(",","<br>",$row_select_mall['general_facility'])."</td>
				<td>".$assigned_emp."</td>";
				if(strtoupper($_SESSION['admin_login']) == 'ADMIN')
					echo "<td style=\"color:$color; font-weight:bold;cursor:pointer;\" onclick=\"change_status('$row_select_mall[mall_id]','$set_status');\">".strtoupper($row_select_mall['status'])."D</td>";
				else
					echo "<td style=\"color:$color; font-weight:bold;\" >".strtoupper($row_select_mall['status'])."D</td>";
				if(strtoupper($_SESSION['admin_login']) == 'ADMIN')
				echo "<td><a href=\"#\" style=\"color:blue;\" onclick=show_mall_data('$row_select_mall[mall_id]');>Edit</a></td>
			  </tr>";
		$count++;
	}
?>
</table>