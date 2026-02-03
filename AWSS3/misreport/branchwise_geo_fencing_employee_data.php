<?php
ob_start();
session_start();
require("adminUtils.php");

$current_date = date('Y-m-d');
$month_date = date('Y-m');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$previous_year_date = $previous_year."-04-01";
}
else{
	$previous_year_date = date('Y-04-01');
}
$branch = $_REQUEST['branch'];
$month_data = $_REQUEST['month_data'];
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td width="5%">Sl No.</td>
        <td width="15%">Employee Code</td>
        <td width="30%">Employee Name</td>
        <td width="50%">Branch</td>
      </tr>
    <?
	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name,branch_code FROM employee_master 
				WHERE emp_code IN(SELECT DISTINCT emp_code FROM location WHERE SUBSTRING(date,1,7)='".$month_data."' 
				AND trans_id LIKE 'A%' AND emp_code IN(SELECT DISTINCT EM.emp_code FROM employee_master EM,`branch_master` BM  WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND BM.branch_code IN(".$branch.") AND EM.sale_access='primary' )
)";
	$res_emp = mysql_query($sql_emp);
	$count=mysql_num_rows($res_emp);
	if($count >0)
	{
	$countgeo=1;
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$branch_code = $row_emp['branch_code'];
		$branch_code_array=explode(',',$branch_code);
		$branch_name_string='';
		foreach($branch_code_array as $branch_code_val)
		{
			 $sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code_val."'";
			 $rsbranch=mysql_query($sqlbranch);
			 $rowbranch=mysql_fetch_array($rsbranch);
			 $branch_name=$rowbranch['branch_name'];
			 $branch_name_string=$branch_name_string.$branch_name.',';
		}
		$branch_name_string=substr($branch_name_string,0,-1);
		echo "<tr>
				<td>".$countgeo."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$branch_name_string."</td>
			  </tr>";
			  $countgeo++;
	}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "No Records";
}
mysql_close($link);
?>


