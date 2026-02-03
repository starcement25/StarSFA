<?php

//ob_start();


	session_start();

	require("adminUtils.php");

	if($_SESSION['admin_login']=="")  		header("location:index.php");

?>



<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

?>



<body>

<center>



<?php

if($_GET['value']==1)

{

	@$current_date = date('Y-m-d');

	$condition = "substring(LO.date,1,10)="."'".$current_date."'";

	$value = 1;



}

else if($_GET['value']==2)

{

	@$current_date = date('Y-m-d');

	$month = explode("-",$current_date);

	$year = $month[0];

	$month = $month[1];

	$condition = "(YEAR(LO.date) =". $year." AND MONTH(LO.date) =" .$month.")";

	$value = 2;

}

else if($_GET['value']==3)

{

	@$current_date = date('Y-m-d');

	$begin_year = date('Y-m-d', strtotime('first day of January this year'));

	$month_last_date = date('Y-m-d', strtotime('last day of this month this year'));

	$month = explode("-",$current_date);

	$year = $month[0];

	$month = $month[1];

	$condition = "(LO.date BETWEEN '".$begin_year." "."12:00:01' AND '".$month_last_date." "."23:59:59')";

	$value = 3;

}

else if($_GET['value']==4)

{

	$start_date=date('Y-m-d', strtotime($_GET['start_date']));

	$end_date=date('Y-m-d', strtotime($_GET['end_date']));

	$condition = "(LO.date BETWEEN '".$start_date." "."00:00:01' AND '".$end_date." "."23:59:59')";

	$value = 4;

}





$count = 1;

$sql_prospect = "SELECT EM.emp_code, EM.emp_name, count(PCH.customer_name) FROM employee_master EM, prospective_customer_header PCH, location LO WHERE LO.trans_id=PCH.trans_id AND EM.emp_code=substring(LO.trans_id,3,5) AND ".$condition." AND (LO.trans_id LIKE 'DC%' OR LO.trans_id LIKE 'DE%')  GROUP BY EM.emp_name";

$res_prospect = mysqli_query($link,$sql_prospect);

$row_prospect = mysqli_fetch_assoc($res_prospect);

if($row_prospect=='')

{

	echo "<div style=\"border-style:solid; border-color:#A92A61; width:100%;\">";

	echo "No records found"."<br>";

	echo "</div>";

	die;

}





echo "<div style=\"width:50%; overflow-y: scroll;height: 150px;\">";

echo "<table border=\"1\" style=\"border-collapse:collapse; width:100%\" class=\"border\">

		<tr>

			<td class=\"TDHEAD\" colspan=\"3\" align=\"center\">Total Prospects</td>

		</tr>

		<tr class=\"TDHEAD_SUB\">

			<td width=\"8%\">SI</td>

			<td width=\"80%\">Employee Name</td>

			<td align=\"center\" width=\"12%\">Total Prospects</td>

		</tr>";



$res_prospect = mysqli_query($link,$sql_prospect);

while($row_prospect = mysqli_fetch_assoc($res_prospect))

{

	echo "<tr>

			<td>$count</td>";

			if($value==4){

			echo "<td><a href=\"#\" id=\"emp$count\" onClick=\"GenericAjaxFunction('business_prospect_data.php?emp_code=$row_prospect[emp_code]&value=$value&start_date=$start_date&end_date=$end_date','prospect_detail',0); clearprospectdiv(); changecolor(id);\" style=\"color:blue;\">$row_prospect[emp_name]</a></td>";

		  }

			else{

				echo "<td><a href=\"#\" id=\"emp$count\" onClick=\"GenericAjaxFunction('business_prospect_data.php?emp_code=$row_prospect[emp_code]&value=$value','prospect_detail',0); clearprospectdiv(); changecolor(id);\" style=\"color:blue;\">$row_prospect[emp_name]</a></td>";

			}

			echo "<td align=\"center\">".$row_prospect['count(PCH.customer_name)']."</td>

		  </tr>";

	$count++;

}

echo $tab;

echo "</table>";

echo "</div>";

?>

</center>

</body>

<?php

mysqli_close($link);

?>

