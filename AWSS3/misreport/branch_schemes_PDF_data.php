<?php
ob_start();
session_start();
require("adminUtils.php");

$state=$_REQUEST['state'];
$zone=$_REQUEST['zone'];
$branch=$_REQUEST['branch'];
$opt_type=$_REQUEST['opt_type'];
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
			$state_condition = " state IN(".$state.") ";
}
if($opt_type=='updateschemedate')
{
	$sl_no=$_REQUEST['sl_no'];
	$start_date=$_REQUEST['start_date'];
	$end_date=$_REQUEST['end_date'];
	
	$sql_scheme_PDF_val = "SELECT branch_code,PDF_file_name FROM branch_schemes_PDF WHERE sl_no = '".$sl_no."'";
	$res_scheme_PDF_val = mysql_query($sql_scheme_PDF_val);
	$row_scheme_PDF_val = mysql_fetch_array($res_scheme_PDF_val);
	$branch_code_exists = $row_scheme_PDF_val['branch_code'];
	$PDF_file_name_exists = $row_scheme_PDF_val['PDF_file_name'];

	$sql  = "insert into branch_schemes_PDF ";
	$sql .= " SET branch_code='".$branch_code_exists."'";
	$sql .= " , PDF_file_name='".$PDF_file_name_exists."'";
	$sql .= " , start_date='".$start_date."'";
	$sql .= " , end_date='".$end_date."'";
	$sql .= " , acedns='Y'";
	$sql .= " , download_time=CURRENT_TIMESTAMP()";
	if(mysql_query($sql)){?>
    <table  style="border-collapse:collapse;" class="border" width="60%">
      <tr class="TDHEAD_SUB">
      	<td colspan="7" align="center">Scheme Activation Date Change Successfull.</td>
      </tr>
     </table><br /><br />  
     <?php 
	}
	else
	{
	?>
     <table  style="border-collapse:collapse;" class="border" width="60%">
      <tr class="TDHEAD_SUB">
      	<td colspan="7" align="center">Scheme Activation Date Change Unsuccessfull.</td>
      </tr>
     </table><br /><br />  
    <?php	
	}
}

$curdate=date('Y-m-d');
	$sql_branch_PDF = "SELECT * FROM (SELECT PDF_file_name,branch_code,acedns,start_date,end_date,sl_no FROM branch_schemes_PDF WHERE 
			FIND_IN_SET(branch_code,'".$branch."') ORDER BY download_time DESC) AS SAT GROUP BY 2,1 ORDER BY 2 ASC";
	$res_branch_PDF = mysql_query($sql_branch_PDF);
	$countbranchPDF=mysql_num_rows($res_branch_PDF);
	if($countbranchPDF >0){
		$count = 1;
		?>
		<table border="1" style="border-collapse:collapse;" class="border" width="60%" align="center">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Branch Schemes PDF Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="8%">SI</td>
            <td width="20%">Branch Name</td>
			<td width="15%">PDF File Name</td>
            <td width="15%">PDF File View</td>
            <td width="15%">Start Date</td>
            <td width="15%">End Date</td>
            <td width="12%">Action</td>
		  </tr>
		<?php
		while($rowbranchPDF = mysql_fetch_array($res_branch_PDF)){
			$branch_code = $rowbranchPDF['branch_code'];
			$PDF_file_name = $rowbranchPDF['PDF_file_name'];
			$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
			$rsbranchname=mysql_query($sqlbranchname);
			$rowbranchname=mysql_fetch_array($rsbranchname);
			$branch_name = $rowbranchname['branch_name'];
			$start_date = $rowbranchPDF['start_date'];
			$end_date = $rowbranchPDF['end_date'];
			$sl_no = $rowbranchPDF['sl_no'];
			if($geo_fencing == 'YES') $activate_flag='ACTIVATED';
			else					  $activate_flag="<a href=\"javascript:access_add_edit('".$branch_code."');\" title=\" Edit branch geo fencing\" style=\"color: #F00;\">ACTIVATE</a>";
			$PDF_file_view="<a href=\"http://salesmpower.acedns.in/schemes/$PDF_file_name\" style=\"color: #F00;\" target=\"_blank\">View</a>";
			echo "<tr>
					<td width=\"8%\">".$count."</td>
					<td width=\"20%\">".$branch_name."</td>
					<td width=\"15%\">".$PDF_file_name."</td>
					<td width=\"15%\">".$PDF_file_view."</td>
					<td width=\"15%\"><input type=\"date\" name=\"start_date_$sl_no\" id=\"start_date_$sl_no\" value=\"".$start_date."\"></td>
					<td width=\"15%\"><input type=\"date\" name=\"end_date_$sl_no\" id=\"end_date_$sl_no\" value=\"".$end_date."\"></td>
					<td width=\"12%\"><input type=\"button\" name=\"submit\" value=\"Update\" onclick=\"javascript:update_date_range('".$sl_no."');\"/></td>
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
