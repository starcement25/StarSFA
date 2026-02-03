<?php
ob_start();
session_start();
require("adminUtils.php");

$state=$_REQUEST['state'];
$zone=$_REQUEST['zone'];
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || strtoupper($_SESSION['admin_login'])=='ACCOUNTS'){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$designation_condition = " WHERE designation != '' ";
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND EM.emp_code IN(".$emp_hierarchy.") ";
	$designation_condition = " AND designation != '' ";
}

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND EM.zone IN(".$zone.") ";
}
if($state != ''){
	if($state == 'all')
		$state_condition = " state!=''";
	else
		if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM'){
			$state_condition = " FIND_IN_SET(".$state.", state)";
		}
		else
		{
			$state_condition = " state IN(".$state.") ";
		}
}
$curdate=date('Y-m-d');
	$sql_branch = "SELECT DISTINCT BM.branch_code,BM.dns_branch_code,BM.branch_name FROM branch_master BM,employee_master EM 
				WHERE  FIND_IN_SET(BM.branch_code,EM.branch_code) AND EM.state IN (".$state.")
				".$emp_hierarchy_condition_one."  ".$zone_condition." ORDER BY BM.branch_name ASC";
	$res_branch = mysql_query($sql_branch);
	$countbranch=mysql_num_rows($res_branch);
	if($countbranch >0){
		$count = 1;
		?>
		<table border="1" style="border-collapse:collapse;" class="border" width="50%" align="center">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Branch Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="8%">SI</td>
            <td width="22%">Branch Code</td>
			<td width="">Branch Name</td>
            <td width="20%">Activate</td>
		  </tr>
		<?php
		while($rowbranchdetails = mysql_fetch_array($res_branch)){
			$branch_code = $rowbranchdetails['branch_code'];
			$dns_branch_code = $rowbranchdetails['dns_branch_code'];
			$branch_name = $rowbranchdetails['branch_name'];
			$sqlselgeofencing="SELECT geo_fencing FROM branchwise_geo_fencing WHERE branch_code='".$branch_code."'";
			$rsselgeofencing=mysql_query($sqlselgeofencing);
			$rowselgeofencing=mysql_fetch_array($rsselgeofencing);
			$geo_fencing = $rowselgeofencing['geo_fencing'];
			if($geo_fencing == 'YES') $activate_flag='ACTIVATED';
			else					  $activate_flag="<a href=\"javascript:access_add_edit('".$branch_code."');\" title=\" Edit branch geo fencing\" style=\"color: #F00;\">ACTIVATE</a>";
			echo "<tr>
					<td width=\"8%\">".$count."</td>
					<td width=\"22%\">".$dns_branch_code."</td>
					<td width=\"\">".$branch_name."</td>
					<td width=\"20%\">".$activate_flag."</td>
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
