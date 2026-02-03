<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
ob_end_flush();
function main(){
	$survey_type = $_GET['survey_type'];
	if($survey_type != '')
		$survey_type_condtion = " AND survey_type = '".$survey_type."' ";
	else
		$survey_type_condtion = "";

?>
<center>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Edit Survey</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
            <!--form method="POST" action="" -->
            <table width="50%" align="center"  cellpadding="5" cellspacing="2">
            	<tr>
                	<td colspan="3" align="right"><a href="survey_output_edit_test.php?show=data" style="color:blue;"><strong>Back</strong></a></td>
                </tr>
            </table>
            <form method="POST" action="">
			<table width="50%" align="center" cellpadding="5" cellspacing="2" border="1" style="border-collapse:collapse;">
                <tr class="TDHEAD">
				  <td colspan="3" align="left">Edit Survey</td>
				</tr>
              <tr style="background:#CD8500;">
                <th>SI</th>
                <th align="center">Name</th>
                <th align="center">Value</th>
              </tr>
			<script language="javascript" type="text/javascript">
            function action_display(row_id,action_id)
            {
                var boolean_action_TR='boolean_action_TR_'+row_id+action_id;
                if(document.getElementById('boolean_action_TR_'+row_id+action_id).style.display=='')
                {
                    document.getElementById('boolean_action_TR_'+row_id+action_id).style.display='none';
                }
                else
                {
                    document.getElementById('boolean_action_TR_'+row_id+action_id).style.display='';
                }
            }
            </script>
		<?php
        $count_layer = 1;
        $survey_layer_name = array();
        $sql_get_layer = "SELECT layout_name FROM survey_input WHERE type = 'layer' ".$survey_type_condtion." ORDER BY display_order ASC";
        $res_get_layer = mysqli_query($link,$sql_get_layer);
        while($row_get_layer = mysqli_fetch_assoc($res_get_layer))
        {
            $survey_layer_name[$count_layer] = $row_get_layer['layout_name'];
            $count_layer++;
        }
        
        $layer_array = array();
        $count = 1;
        $survey_id = $_GET['survey_id'];
        $validation_array=array();
		
        foreach($survey_layer_name as $layer_name)
        {
        $sql_select_survey_details = "SELECT SO.* FROM survey_output SO, survey_input SI WHERE SO.survey_id = '".$survey_id."' 
                                    AND SO.row_id = SI.row_id AND SI.layout_name = '$layer_name' ORDER BY SI.display_order ASC";
        $res_select_survey_details = mysqli_query($link,$sql_select_survey_details);
        while($row_select_survey_details = mysqli_fetch_assoc($res_select_survey_details))
        {
            $sql_select_survey_input = "SELECT * FROM survey_input WHERE row_id = '".$row_select_survey_details['row_id']."'";
            $res_select_survey_input = mysqli_query($link,$sql_select_survey_input);
            $row_select_survey_input = mysqli_fetch_assoc($res_select_survey_input);
            
            $survey_input_type=$row_select_survey_input['type'];
            $survey_input_action=$row_select_survey_input['action'];
            $survey_input_display_name=$row_select_survey_input['display_name'];
            $survey_input_display_table_name=$row_select_survey_input['display_table_name'];
            $mandatory=$row_select_survey_input['mandatory'];
            $validation=$row_select_survey_input['validation'];
            
            $select_data_row_id = $row_select_survey_details['row_id'];
        
            if(!in_array($layer_name,$layer_array))
            {
                array_push($layer_array, $layer_name);
                echo "<tr><td colspan=\"3\" align=\"center\" style=\"background:#FFEC8B;\"><b>".$layer_name."</b></td></tr>";
            }
            
            echo "<tr ><td>$count<input type=\"hidden\" name=\"row_id[]\" value=\"$row_select_survey_details[row_id]\">
                    <input type=\"hidden\" name=\"action_id[]\" value=\"$row_select_survey_details[action_id]\"></td>";
            
            if($survey_input_type == ' ' || $survey_input_type == '' || $survey_input_type == 'double')
            {
                echo "<td><b>".$survey_input_display_name."</b></td>";
                echo "<td><input type=\"text\" name=\"survey_input_value_text".$select_data_row_id."[]\" value=\"$row_select_survey_details[value]\"></td>";
                
                if($survey_input_type == 'double')  $validation_type=$survey_input_type;
                elseif($survey_input_type == ' ')   $validation_type='textbox';
                elseif($survey_input_type == '')     $validation_type='textbox';
                
                if($mandatory=='Y' || $validation!='')
                {
                    $validation_value=$select_data_row_id.'#'.$mandatory.'#'.$validation_type.'#'.$validation;
                    array_push($validation_array,$validation_value);
                }
            }
            else if(strstr($survey_input_type,':',true))
            {
                if($row_select_survey_input['action'] != '' && $row_select_survey_input['action'] != 'Click') //Start of action
                {
                    $survey_input_value_boolean_array=explode(':',$survey_input_type);
                    echo "<td><b>".$survey_input_display_name."</b></td>";
                    echo "<td>";
                    $survey_details_value_boolean=$row_select_survey_details['value'];
					
                    if($survey_details_value_boolean!='' && $survey_details_value_boolean!='No') $survey_details_value_boolean='Y';
                    if($survey_details_value_boolean=='' || $survey_details_value_boolean=='No') $survey_details_value_boolean='N';
					
                    foreach($survey_input_value_boolean_array as $boolean_value)
                    {
                      if ($survey_details_value_boolean==$boolean_value) $checked = " checked='checked' ";
                      else  $checked = " ";
                      echo"<input type=\"radio\" name=\"survey_input_value_boolean_action_".$select_data_row_id."[]\" value=\"$boolean_value\" $checked onchange=\"javascript:action_display('".$select_data_row_id."','".$row_select_survey_input['action_id']."');\">$boolean_value";
                    }
                    echo "</td>";
					
                    if($survey_details_value_boolean!='N') $style_display="display:''";
                    else  $style_display="display:none";
					
									
                    echo "</tr><tr id=\"boolean_action_TR_".$select_data_row_id.$row_select_survey_input['action_id']."\" style=\"$style_display\">";
                    echo "<td></td>";
                    $display_name_action = explode("#", $survey_input_action);
                    echo "<td><b>".$display_name_action[0]."</b></td>";
                    if($display_name_action[1]=='' || $display_name_action[1]==' ' || $display_name_action[1]=='double')
                    {
                        echo "<td><input type=\"text\" id=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\"  name=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\" value=\"$row_select_survey_details[value]\"></td>";
                    }
                    else if($display_name_action[1]=='radio' || $display_name_action[1]=='checkbox')
                    {
                        $column_name = $display_name_action[3];
                        $table_name = $display_name_action[3];
                        $table_value = preg_replace('/[\r\n]+/', '',$row_select_survey_details['value']);
                        $table_value_array=explode(";",$table_value);
                        $table_value_array=array_map('trim',$table_value_array);
                        
                        $sql_table_value_action = "SELECT DISTINCT $column_name FROM ".$table_name." WHERE $column_name!='' ORDER BY $column_name ASC";
                        $res_table_value_action = mysqli_query($link,$sql_table_value_action);
                
                        if(strstr($row_select_survey_details['value'],';',true) || $survey_input_type == 'checkbox')
                        {
                            echo "<td>";
                            while($row_table_value_action = mysqli_fetch_assoc($res_table_value_action))
                            {
                                if (in_array($row_table_value_action[$column_name],$table_value_array)) $checked = " checked='checked' ";
                                else  $checked = " ";
                                echo "<input type=\"checkbox\" id=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\" name=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\"  value=\"$row_table_value_action[$column_name]\" $checked>$row_table_value_action[$column_name]";
                            }
                            echo "</td>";
                        }
                        else
                        { 
                            echo "<td>";
                            while($row_table_value_action = mysqli_fetch_assoc($res_table_value_action))
                            {
                                if (in_array($row_table_value_action[$column_name],$table_value_array)) $checked = " checked='checked' ";
                                else  $checked = " ";
                                echo "<input type=\"radio\" id=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\" name=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\"  value=\"$row_table_value_action[$column_name]\" $checked>$row_table_value_action[$column_name]";
                            }
                            echo "</td>";
                        }
                     }
                    echo "</tr><tr>";
                }//End of action
                else if($row_select_survey_input['action'] != '' && $row_select_survey_input['action'] == 'Click')
                {
                    echo "<td><b>".$survey_input_display_name."</b></td>";
                    echo "<td>".$row_select_survey_details['value']."</td>";
                }
                else
                {
                    $survey_input_value_boolean_array=explode(':',$survey_input_type);
                    echo "<td><b>".$survey_input_display_name."</b></td>";
                    echo "<td>";
                    $survey_details_value_boolean=$row_select_survey_details['value'];
                    if($survey_details_value_boolean=='Yes') $survey_details_value_boolean='Y';
                    if($survey_details_value_boolean=='No') $survey_details_value_boolean='N';
                    foreach($survey_input_value_boolean_array as $boolean_value)
                    {
                      if ($survey_details_value_boolean==$boolean_value) $checked = " checked='checked' ";
                      else  $checked = " ";
                        echo"<input type=\"radio\" name=\"survey_input_value_boolean_".$select_data_row_id."[]\" value=\"$boolean_value\" $checked>$boolean_value";
                    }
                    echo "</td>";
                }
                if($mandatory=='Y' || $validation!='')
                {
                    $validation_value=$select_data_row_id.'#'.$mandatory.'#'.$validation_type.'#'.$validation;
                    array_push($validation_array,$validation_value);
                }
            }
            else if($survey_input_type == 'radio' || $survey_input_type == 'checkbox')
            {
                echo "<td><b>".$survey_input_display_name."</b></td>";
                $column_name = $row_select_survey_input['display_table_name'];
                $table_name = $row_select_survey_input['display_table_name'];
                $table_value = preg_replace('/[\r\n]+/', '',$row_select_survey_details['value']);
                $table_value_array=explode(";",$table_value);
                $table_value_array=array_map('trim',$table_value_array);
                
                if(strstr($column_name,'#',true))
                {
                    $table_columns = explode("#",$column_name);
                    $table_name = $table_columns[0];
                    $column_name = $table_columns[1];
                }
        
                $sql_table_value = "SELECT DISTINCT $column_name FROM ".$table_name." WHERE $column_name!='' ORDER BY $column_name ASC";
                $res_table_value = mysqli_query($link,$sql_table_value);
        
                if(strstr($row_select_survey_details['value'],';',true) || $survey_input_type == 'checkbox')
                {
                    //echo "<td><select name=\"survey_input_value_checkbox_".$select_data_row_id."[]\" multiple=\"multiple\" style=\"width:200px;\">";
                    echo "<td>";
                    while($row_table_value = mysqli_fetch_assoc($res_table_value))
                    {
                        if (in_array($row_table_value[$column_name],$table_value_array)) $checked = " checked='checked' ";
                        else  $checked = " ";
                        //echo "<option value=".$row_table_value[$column_name]." ".$selected.">".$row_table_value[$column_name]."</option>";
                        echo "<input type=\"checkbox\" name=\"survey_input_value_checkbox_".$select_data_row_id."[]\"  value=\"$row_table_value[$column_name]\" $checked>$row_table_value[$column_name]";
                    }
                    //echo "</select><input type=\"text\" name=\"survey_input_value_".$select_data_row_id."[]\" value=\" \" hidden></td>";
                    echo "</td>";
                }
                else
                { 
                    echo "<td>";
                    while($row_table_value = mysqli_fetch_assoc($res_table_value))
                    {
                        if (in_array($row_table_value[$column_name],$table_value_array)) $checked = " checked='checked' ";
                        else  $checked = " ";
                      echo "<input type=\"radio\" name=\"survey_input_value_radio_".$select_data_row_id."[]\"  value=\"$row_table_value[$column_name]\" $checked>$row_table_value[$column_name]";
                    }
                    echo "</td>";
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
			$ip_address = $_SERVER['SERVER_ADDR'];
			$transactionid = $survey_id;
			@$trans_date = date('Y-m-d',strtotime(substr($transactionid,-14,8)));
			$data_array = array();
            $row_id_array = $_POST['row_id'];
            $row_value_array = $_POST['survey_input_value'];
            $action_id_array = $_POST['action_id'];
            
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";*/
			
			
			foreach($row_id_array as $index=>$value)
			{
				$row_id = $value;
				$action_id = $action_id_array[$index];
				$textarray = $_POST["survey_input_value_text".$row_id];
				$radioarray = $_POST["survey_input_value_radio_".$row_id];
				$booleanarray = $_POST["survey_input_value_boolean_".$row_id];
				$booleanactionarray = $_POST["survey_input_value_boolean_action_".$row_id.$action_id];
				$booleanactionarrayone = $_POST["survey_input_value_boolean_action_".$row_id];
				$checkboxarray = $_POST["survey_input_value_checkbox_".$row_id];
				
				if(!empty($textarray))
				{
					foreach($textarray as $value)
					$setvalue = $value;
				}
				
				if(!empty($radioarray))
				{
					foreach($radioarray as $value)
					$setvalue = $value;
				}
				
				if(!empty($booleanarray))
				{
					foreach($booleanarray as $value)
					$setvalue = $value;
					
					if($setvalue == 'Y')
						$setvalue = 'Yes';
					else if($setvalue == 'N')
						$setvalue = 'No';
				}
				
				if(!empty($booleanactionarrayone))
				{
					foreach($booleanactionarrayone as $value)
					$setvalue = $value;
					
					if($setvalue == 'Y')
						$setvalue = 'Yes';
					else if($setvalue == 'N')
						$setvalue = 'No';
				}
				
				if(!empty($booleanactionarray))
				{
					if($setvalue == 'No')
					{
						$setvalue = 'No';
					}
					else
					{
					foreach($booleanactionarray as $value)
					$setvalue = $value;
					
					if($setvalue == 'Y')
						$setvalue = 'Yes';
					else if($setvalue == 'N')
						$setvalue = 'No';
					}
				}
				
				if(!empty($checkboxarray))
				{
					foreach($checkboxarray as $value)
					$setvalue .= $value.";";
				}
				
								
				unset($textarray);
				unset($radioarray);
				unset($booleanarray);
				unset($booleanactionarray);
				unset($checkboxarray);
				
				if($row_id != 'RA067')
				{
					$sql_previous_value = "SELECT value FROM survey_output WHERE row_id = '".$row_id."' AND survey_id = '".$survey_id."'";
					$res_previous_value = mysqli_query($link,$sql_previous_value);
					$row_pevious_value = mysqli_fetch_assoc($res_previous_value);
					$previous_value = $row_pevious_value['value'];
					
					if($previous_value == 'N')
						$previous_value = 'No';
					else if($previous_value == 'Y')
						$previous_value == 'Yes';

					$sql_check_type = "SELECT type FROM survey_input WHERE row_id = '".$row_id."'";
					$res_check_type = mysqli_query($link,$sql_check_type);
					$row_check_type = mysqli_fetch_assoc($res_check_type);
					$row_id_type = $row_check_type['type'];
					
					$flag = 0;
					$previous_value_array = array();
					$current_value_array = array();
					$pos = strpos($previous_value,";");
					
					if($pos>0 || $row_id_type == 'checkbox')
					{
						//$previous_value = str_replace(" ","",$previous_value);
						$previous_value = rtrim($previous_value,";");
						$previous_value_array = explode(";",$previous_value);
						
						//$setvalue = str_replace(" ","",$setvalue);
						$setvalue = rtrim($setvalue,";");
						$current_value_array = explode(";",$setvalue);
						
						$result = array();
						$result = array_diff($current_value_array,$previous_value_array);
						
						if(!empty($result))
							$flag = 1;
					}
					
					if($previous_value != $setvalue && stristr($setvalue,";") == false)
					{
						$affected_field .= $row_id.",";
						$previousvalue .= $previous_value.",";
						$currentvalue .= $setvalue.",";
					}
					if($flag == 1 && stristr($setvalue,";") == true)
					{
						$affected_field .= $row_id.",";
						$previousvalue .= $previous_value.",";
						$currentvalue .= $setvalue.",";
					}
					
					$sql_insert = "UPDATE survey_output SET action_id = '".$action_id."', value = '".addslashes($setvalue)."' WHERE row_id = '".$row_id."' AND survey_id = '".$survey_id."'";
					$res_insert = mysqli_query($link,$sql_insert);
				}
				$setvalue = '';
			}
			
			$sql_survey_log = "INSERT INTO survey_activity_log SET transaction_id = '".$transactionid."', transaction_date = '".$trans_date."', operation_done_by = '".$_SESSION['admin_login']."', operation_type = 'EDIT', affected_field = '".$affected_field."', previous_value = '".$previousvalue."', current_value = '".$currentvalue."', operation_performed_ip = '".$ip_address."', update_datetime = current_timestamp";
			$res_survey_log = mysqli_query($link,$sql_survey_log);
            
			header('location:survey_output_edit_test.php?update=success&show=data');
        }
        ?>
       </td>
	</tr>
</table>  
<?php }?>