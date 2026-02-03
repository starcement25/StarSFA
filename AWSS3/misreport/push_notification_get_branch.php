<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$branch_array = array();
$vertical_value = $_REQUEST['vertical_value'];
$sql_branch = "SELECT branch_code FROM employee_master WHERE FIND_IN_SET('$vertical_value',vertical_value)";
$res_branch = mysql_query($sql_branch);
while($row_branch = mysql_fetch_array($res_branch)){
	$branch_code = $row_branch['branch_code'];
	if(strpos($branch_code,',') == true){
		$branch_code_explode = explode(',',$branch_code);
		foreach($branch_code_explode as $value){
			if(!in_array($value,$branch_array)){
				array_push($branch_array,$value);
				$branch_string .= "'".$value."',";
			}
		}
	}
	else{
		if(!in_array($branch_code,$branch_array)){
			array_push($branch_array,$branch_code);
			$branch_string .= "'".$branch_code."',";
		}
	}
}
$branch_string = rtrim($branch_string, ",");
/*echo "<pre>";
print_r($branch_array);
echo "</pre>";*/
?>
<!--<option value="">Select</option>-->
<!--<table>
<tr>-->
<?php
$count = 1;
//foreach($branch_array as $branch_code_val){
	$sql_branch_name = "SELECT branch_code, branch_name FROM branch_master WHERE branch_code IN(".$branch_string.") ORDER BY branch_name ASC";
	$res_branch_name = mysql_query($sql_branch_name);
	while($row_branch_name = mysql_fetch_array($res_branch_name)){
		$branch_code = $row_branch_name['branch_code'];
		$branch_name = $row_branch_name['branch_name'];
		//echo "<option value=\"".$branch_code."\">".$branch_name."</option>";
		echo $branch_name.":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"".$branch_code."\" class=\"input_chk\" /><br>";
		/*if($count%5 == 0){
			echo "</tr><tr>";
		}*/
		$count++;
	}
//}
mysql_close($link);
?>
<!--</table>-->