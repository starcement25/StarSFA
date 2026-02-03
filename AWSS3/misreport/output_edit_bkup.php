<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<center>
<form method="POST" action="">
<table border="1" width="600px" style="border-collapse:collapse; border-color:#996600;" cellpadding="8px">
  <tr style="background:#CD8500;">
  	<th>SI</th>
  	<th align="center">Name</th>
    <th align="center">Value</th>
  </tr>

<?php
$count_layer = 1;
$survey_layer_name = array();
$sql_get_layer = "SELECT layout_name FROM survey_input WHERE type = 'layer' ORDER BY display_order ASC";
$res_get_layer = mysql_query($sql_get_layer);
while($row_get_layer = mysql_fetch_array($res_get_layer))
{
	$survey_layer_name[$count_layer] = $row_get_layer['layout_name'];
	$count_layer++;
}

$layer_array = array();
$count = 1;
$survey_id = $_GET['survey_id'];

foreach($survey_layer_name as $layer_name)
{
$sql_select_survey_details = "SELECT * FROM survey_output SO, survey_input SI WHERE SO.survey_id = '".$survey_id."' AND SO.row_id = SI.row_id AND SI.layout_name = '$layer_name' ORDER BY SI.display_order ASC";
$res_select_survey_details = mysql_query($sql_select_survey_details);
while($row_select_survey_details = mysql_fetch_array($res_select_survey_details))
{
	
	$sql_select_survey_input = "SELECT * FROM survey_input WHERE row_id = '".$row_select_survey_details['row_id']."'";
	$res_select_survey_input = mysql_query($sql_select_survey_input);
	$row_select_survey_input = mysql_fetch_array($res_select_survey_input);
	
	$select_data_row_id = $row_select_survey_details['row_id'];
	
	if(!in_array($layer_name,$layer_array))
	{
		array_push($layer_array, $layer_name);
		echo "<tr><td colspan=\"3\" align=\"center\" style=\"background:#FFEC8B;\"><b>".$layer_name."</b></td></tr>";
	}
	
	echo "<tr style=\"background:#FFF8DC;\"><td>$count<input type=\"hidden\" name=\"row_id[]\" value=\"$row_select_survey_details[row_id]\"><input type=\"hidden\" name=\"action_id[]\" value=\"$row_select_survey_details[action_id]\"></td>";
	
	if($row_select_survey_input['type'] == ' ' || $row_select_survey_input['type'] == '' || $row_select_survey_input['type'] == 'double')
	{
		echo "<td><b>".$row_select_survey_input['display_name']."</b></td>";
		echo "<td><input type=\"text\" name=\"survey_input_value[]\" value=\"$row_select_survey_details[value]\"></td>";
		
	}
	else if(strstr($row_select_survey_input['type'],':',true))
	{
		if($row_select_survey_input['action'] != '')
		{
			$display_name = explode("#", $row_select_survey_input['action']);
			echo "<td><b>".$display_name[0]."</b></td>";
			echo "<td><input type=\"text\" name=\"survey_input_value[]\" value=\"$row_select_survey_details[value]\"></td>";
		}
		else
		{
			echo "<td><b>".$row_select_survey_input['display_name']."</b></td>";
			echo "<td><input type=\"text\" name=\"survey_input_value[]\" value=\"$row_select_survey_details[value]\"></td>";
		}
	}
	else if($row_select_survey_input['type'] == 'radio' || $row_select_survey_input['type'] == 'checkbox')
	{
		echo "<td><b>".$row_select_survey_input['display_name']."</b></td>";
		if(strstr($row_select_survey_details['value'],';',true))
		{
			$queryval = '';
			
			echo "<td><select name=\"".$select_data_row_id."[]"."\" multiple=\"multiple\" style=\"width:200px;\">";
			$column_name = $row_select_survey_input['display_table_name'];
			$table_name = $row_select_survey_input['display_table_name'];
			
			if(strstr($column_name,'#',true))
			{
				$table_columns = explode("#",$column_name);
				$table_name = $table_columns[0];
				$column_name = $table_columns[1];
			}
			
			$category_value = $row_select_survey_details['value'];
			
			/** Code to remove new line, ;, space **/
			$category_value = str_replace("\n","",$category_value);
			$category_value = str_replace(" ","",$category_value);
			$category_value_one = str_replace("'","\'",$category_value);
			$category_value_one = rtrim($category_value_one, ";");
			$category_value = rtrim($category_value, ";");
			/*End*/
			
			$category_value = explode(";",$category_value);
			$category_value_one = explode(";",$category_value_one);
			
			/** Code To Form Query Condition **/
			foreach($category_value_one as $val1)
				$queryval.="'".trim($val1)."',";
			$queryval = rtrim($queryval, ",");
			/*End*/
			
			$sql_category_table = "SELECT * FROM ".$table_name." WHERE $column_name NOT IN(".$queryval.")";
			$res_category_table = mysql_query($sql_category_table);
			while($row_category_table = mysql_fetch_array($res_category_table))
			{
				
				echo "<option>".$row_category_table[$column_name]."</option>";
					
			}
			foreach($category_value as $catval)
			{
				echo "<option selected>".trim($catval)."</option>";
			}
			echo "</select><input type=\"text\" name=\"survey_input_value[]\" value=\" \" hidden></td>";
			
		}
		else
		{
			
			echo "<td><input type=\"text\" name=\"survey_input_value[]\" value=\"$row_select_survey_details[value]\"></td>";
		}
	}
	$count++;
	echo "</tr>";
}
}
?>
<tr><td colspan="3" align="center"><input type="submit" name="submit" value="Submit" style="width:80px; height:30px; font-weight:bold; background:#CC9966; border-radius:5px; cursor:pointer;" /></td></tr>
</table>
</form>
</center>
<?php
if($_POST['submit'] == 'Submit')
{
	
	$data_array = array();
	$row_id_array = $_POST['row_id'];
	$row_value_array = $_POST['survey_input_value'];
	$action_id_array = $_POST['action_id'];
	
		
	for($count = 0; $count<count($row_id_array); $count++)
	{
		$selected_new_value = '';
		array_push($data_array, $row_id_array[$count]);
		
		$select_array_variable = $row_id_array[$count];
		$selected_data = $_POST[$select_array_variable];
		if(is_array($selected_data))
		{
			foreach($selected_data as $select_value)
			{
				$selected_new_value .= $select_value."; ";
			}
			$row_value_array[$count] = $selected_new_value;
			array_push($data_array, $selected_new_value);
		}
		else
			array_push($data_array, $row_value_array[$count]);
		
		if($action_id_array[$count] == '')
		{
			$action_id_array[$count] = '';
		}
		array_push($data_array, $action_id_array[$count]);
	}
		
	$survey_output_chunk = array_chunk($data_array,3);
	
	
		
	foreach($survey_output_chunk as $survey_chunk_index=>$survey_chunk_value)
	{
		$survey_chunk_value[1] = str_replace("'", "\'",$survey_chunk_value[1]);
		$sql_record_exist_check = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."' AND row_id = '".$survey_chunk_value[0]."' AND action_id = '".$survey_chunk_value[2]."' AND value = '".$survey_chunk_value[1]."'";
		$res_record_exist_check = mysql_query($sql_record_exist_check);
		$total_record_exist_check = mysql_num_rows($res_record_exist_check);
		
		if($total_record_exist_check==0)
		{
			$sql_update_survey_output = "UPDATE survey_output SET action_id = '".$survey_chunk_value[2]."', value = '".$survey_chunk_value[1]."' WHERE row_id = '".$survey_chunk_value[0]."' AND survey_id = '".$survey_id."'";
			$res_update_survey_output = mysql_query($sql_update_survey_output);
		}
	}
	echo "Data updated successfully";
}
?>