<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$condition = " WHERE type = 'mall' ";
	if($_GET['searchtype'])
	{
		$mall_name = $_GET['mall_name'];
		$searchtype = $_GET['searchtype'];
		if($searchtype == 'mall')
			$condition = " WHERE mall_name LIKE '%".$mall_name."%' AND type = 'mall' ";
		else if($searchtype == 'city')
			$condition = " WHERE city LIKE '%".$mall_name."%' AND type = 'mall' ";
	}
	if(strtoupper($_SESSION['admin_login']) == 'ADMIN' || strtoupper($_SESSION['admin_login']) == 'E0002')
		$colspan = 9;
	else
		$colspan = 8;
?>
<table border="1" style="border-collapse:collapse;" class="border" width="100%" cellpadding="4">
  <tr class="TDHEAD">
  	<td colspan="<?php echo $colspan; ?>" align="center">Mall Details</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>SI</td>
  	<td>Mall Name</td>
    <td>Area</td>
    <td>City</td>
    <td>Pincode</td>
    <td>Assigned To</td>
    <td>Status</td>
    <?php if(strtoupper($_SESSION['admin_login']) == 'ADMIN' || strtoupper($_SESSION['admin_login']) == 'E0002'){ ?>
    <td>Action</td>
    <?php } ?>
  </tr>
<?php
	$count = 1;
	$sql_select_mall = "SELECT * FROM mall_master".$condition." ORDER BY mall_id DESC";
	$res_select_mall = mysqli_query($link,$sql_select_mall);
	while($row_select_mall = mysqli_fetch_assoc($res_select_mall))
	{
		$assigned_emp = '';
		$sql_select_assigned_emp = "SELECT EM.emp_name FROM mall_emp_relation MER, employee_master EM WHERE mall_id = '".$row_select_mall['mall_id']."' AND MER.emp_code = EM.emp_code";
		$res_select_assigned_emp = mysqli_query($link,$sql_select_assigned_emp);
		while($row_select_assigned_emp = mysqli_fetch_assoc($res_select_assigned_emp))
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
				<td>".$row_select_mall['area']."</td>
				<td>".$row_select_mall['city']."</td>
				<td align=\"right\">".$row_select_mall['pincode']."</td>
				<td>".$assigned_emp."</td>";
				if(strtoupper($_SESSION['admin_login']) == 'ADMIN')
					echo "<td style=\"color:$color; font-weight:bold;cursor:pointer;\" onclick=\"change_status('$row_select_mall[mall_id]','$set_status');\">".strtoupper($row_select_mall['status'])."</td>";
				else
					echo "<td style=\"color:$color; font-weight:bold;\" >".strtoupper($row_select_mall['status'])."</td>";
				if(strtoupper($_SESSION['admin_login']) == 'ADMIN' || strtoupper($_SESSION['admin_login']) == 'E0002')
				echo "<td><a href=\"#\" style=\"color:blue;\" onclick=show_mall_data('$row_select_mall[mall_id]');>Edit</a></td>
			  </tr>";
		$count++;
	}
?>
</table>