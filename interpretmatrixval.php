<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$matrixval="1:1:0#1:2:1#1:3:0#2:2:1";
$matrixvalarray=explode('#',$matrixval);
$row_id='RA003';
$sql_table_list="SELECT display_table_name,type FROM survey_input WHERE row_id='".$row_id."'";
$rs_table_list=mysqli_query($link,$sql_table_list);
$row_table_list=mysqli_fetch_assoc($rs_table_list);
$display_table_name=$row_table_list['display_table_name'];
$type=$row_table_list['type'];

if($type=='checkboxmatrix')
{
	$display_table_name_array=explode('#',$display_table_name);

	foreach($matrixvalarray as $values)
	{
		$valuesarray=explode(':',$values);
		$sqlx_axis_val="SELECT $display_table_name_array[0] FROM $display_table_name_array[0] LIMIT ".($valuesarray[0]-1).",1";
		$rsx_axis_val=mysqli_query($link,$sqlx_axis_val);
		$rowx_axis_val=mysqli_fetch_assoc($rsx_axis_val);
		$xaxis_val=$rowx_axis_val[$display_table_name_array[0]];
		
		$sqly_axis_val="SELECT $display_table_name_array[1] FROM $display_table_name_array[1] LIMIT ".($valuesarray[1]-1).",1";
		$rsy_axis_val=mysqli_query($link,$sqly_axis_val);
		$rowy_axis_val=mysqli_fetch_assoc($rsy_axis_val);
		$y_axis_val=$rowy_axis_val[$display_table_name_array[1]];

		$final_val.=($xaxis_val.':'.$y_axis_val.':'.$valuesarray[2]).'#';
	}
	$final_val=substr($final_val,0,-1);
}
echo $final_val;

?>