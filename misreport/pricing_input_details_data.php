<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$condition = " SUBSTRING(PD.datetime,1,10)='".$today."' ";
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = " SUBSTRING(PD.datetime,1,4)='".$year."' AND SUBSTRING(PD.datetime,6,2)='".$month."' ";
}
else if($_GET['type'] == 'custom'){
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$condition = " (SUBSTRING(PD.datetime,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
}
function plant_sort($a, $b) {
    if($a==$b) return $a;
}
$count = 1;
$plantnamearray = array();
$plant_name_array=array();
$loose_rate_ton_array=array();
$product_group_name_array=array();
$pd_date_array=array();
$pd_time_array=array();
$pd_datetime_array=array();
?>
<table border="1" style="border-collapse:collapse;" class="border" width="100%" cellpadding="4" >
  <tr class="TDHEAD" align="center" id="head_main">
    <td>SI</td>
    <td>Date</td>
    <td>Time</td>
    <td>Plant name</td>
    <td>Loose Rate</td>
  </tr>
<?php
//if(strtoupper($_SESSION['vertical_value'])=='SPECIALTY FATS'){
$sql_check_formulation_record="SELECT PD.plant_name,group_concat(concat(PD.oils,':',PD.oils_rate) separator '#') AS loose_rate_ton,DATE_FORMAT(SUBSTRING(PD.datetime,1,10),'%d-%m-%Y') as pd_date, SUBSTRING(PD.datetime,12) as pd_time FROM pricing_detials_formulation PD 
WHERE ".$condition."  AND PD.vertical_value='".$_SESSION['vertical_value']."' GROUP BY PD.plant_name,PD.price_generation_id ORDER BY PD.plant_name ASC,SUBSTRING(PD.datetime,1,19) DESC";
/*}
else{
	$sql_check_formulation_record="SELECT PD.plant_name,group_concat(concat(PD.oils,':',PD.oils_rate) separator '#') AS loose_rate_ton,PGM.product_group_name,PGM.product_group_code,PGM.formulation,DATE_FORMAT(SUBSTRING(PD.datetime,1,10),'%d-%m-%Y') as pd_date, SUBSTRING(PD.datetime,12) as pd_time FROM pricing_detials_formulation PD, product_group_master PGM WHERE ".$condition." AND PD.product_group_code=PGM.product_group_code  GROUP BY PD.plant_name,PD.datetime ORDER BY PD.plant_name ASC,SUBSTRING(PD.datetime,1,19) DESC,PGM.product_group_name ASC";
}*/
$res_check_record_formulation = mysqli_query($link,$sql_check_formulation_record);
$total_rows_formulation = mysqli_num_rows($res_check_record_formulation);
while($row_check_record_formulation = mysqli_fetch_assoc($res_check_record_formulation)){
		$plant_name = $row_check_record_formulation['plant_name'];
		//$oils=$row_check_record_formulation['oils'];
		//$loose_rate_ton = $oils.' : '.$oils_rate.'#';
		$loose_rate_ton = $row_check_record_formulation['loose_rate_ton'];
		$product_group_name = $row_check_record_formulation['product_group_name'];
		$pd_date = $row_check_record_formulation['pd_date'];
		$pd_time = $row_check_record_formulation['pd_time'];
		$pd_datetime_combined=$pd_date.' '.$pd_time;
		
		array_push($plant_name_array,$plant_name);
		array_push($loose_rate_ton_array,$loose_rate_ton);
		array_push($product_group_name_array,$product_group_name);
		array_push($pd_date_array,$pd_date);
		array_push($pd_time_array,$pd_time);
		array_push($pd_datetime_array,$pd_datetime_combined);
	}
	//asort($plant_name_array);
	//print_r($plant_name_array);
	for($i=0;$i<count($plant_name_array);$i++)
	{
		if(strpos($loose_rate_ton_array[$i],'#')!=false){
			$loose_rate_ton_split=explode('#',$loose_rate_ton_array[$i]);
			$loose_rate_ton_final='';
			foreach($loose_rate_ton_split as $loose_rate_ton_val)
			{
				$loose_rate_ton_final=$loose_rate_ton_final.$loose_rate_ton_val.'<br />';
			}
		}
		else $loose_rate_ton_final=$loose_rate_ton_array[$i];
		
		/*if(!in_array($plant_name_array[$i],$plantnamearray)){
			array_push($plantnamearray,$plant_name_array[$i]);
			
			echo "<tr class=\"TDHEAD_SUB\"><td align=\"center\" colspan=\"5\">".$plant_name_array[$i]."</td></tr>";
		}*/
		echo "<tr id=\"tab".$count."\">
				<td>".$count."</td>
				<td>".$pd_date_array[$i]."</td>
				<td>".$pd_time_array[$i]."</td>
				<td>".$plant_name_array[$i]."</td>
				<td align=\"right\">".$loose_rate_ton_final."</td>
			  </tr>";
		$count++;
	}
	echo "</table>";
mysqli_close($link);
?>