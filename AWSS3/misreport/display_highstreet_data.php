<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$condition = " WHERE type = 'hi-street' ";
	if($_GET['searchtype'])
	{
		$mall_name = $_GET['mall_name'];
		$searchtype = $_GET['searchtype'];
		if($searchtype == 'pincode')
			$condition = " WHERE pincode LIKE '%".$mall_name."%' AND type = 'hi-street' ";
		else if($searchtype == 'city')
			$condition = " WHERE city LIKE '%".$mall_name."%' AND type = 'hi-street' ";
	}
	if($_SESSION['admin_login'] == 'admin' || $_SESSION['admin_login'] == 'E0002')
		$colspan = 8;
	else
		$colspan = 7;
?>
<table border="1" style="border-collapse:collapse;" class="border" cellpadding="4" width="100%">
  <tr class="TDHEAD">
  	<td colspan="<?php echo $colspan; ?>" align="center">High Street Details</td>
  </tr>
  <tr class="TDHEAD_SUB" align="center">
  	<td>SI</td>
  	<!--td>High Street Name</td-->
    <td>Area</td>
    <td>City</td>
    <td>Pincode</td>
    <td>STD Code</td>
    <td>Assigned To</td>
    <td>Status</td>
    <?php if(strtoupper($_SESSION['admin_login']) == 'ADMIN' || strtoupper($_SESSION['admin_login']) == 'E0002'){ ?>
    <td>Action</td>
    <?php } ?>
  </tr>
<?php
	$count = 1;
	$sql_select_mall = "SELECT * FROM mall_master".$condition." ORDER BY mall_id DESC";
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
				<td>".$row_select_mall['area']."</td>
				<td>".$row_select_mall['city']."</td>
				<td align=\"right\">".$row_select_mall['pincode']."</td>
				<td align=\"right\">".$row_select_mall['std_code']."</td>
				<td>".str_replace(",","<br>",$assigned_emp)."</td>";
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