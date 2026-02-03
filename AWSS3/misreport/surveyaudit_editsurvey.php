<?php
session_start();
ob_start();
include "surveyaudit_check.php";
include "surveyaudit_connection.php";
include "surveyaudit_header.php";

$survey_id = $_REQUEST['survey_id'];
$row_id = $_REQUEST['row_id'];
$mall_name = $_REQUEST['mall_name'];

/*------------------> Get row id to check menu associated with it <-----------------------*/
$sql_survey_row_id = "SELECT row_id FROM survey_output WHERE survey_id = '".$survey_id."' ORDER BY row_id ASC LIMIT 1";
$res_survey_row_id = mysql_query($sql_survey_row_id);
$row_survey_row_id = mysql_fetch_array($res_survey_row_id);
$survey_rowid = $row_survey_row_id['row_id'];

$sql_menu_id = "SELECT menu_id FROM survey_input WHERE row_id = '".$survey_rowid."'";
$res_menu_id = mysql_query($sql_menu_id);
$row_menu_id = mysql_fetch_array($res_menu_id);
$menu_id = $row_menu_id['menu_id'];

/*------------------> Get layer names according to menu <-----------------------*/
$survey_layer_name = array();
$sql_get_layer = "SELECT DISTINCT layout_name FROM survey_input WHERE type = 'layer' AND menu_id = '".$menu_id."'ORDER BY display_order ASC";
$res_get_layer = mysql_query($sql_get_layer);
while($row_get_layer = mysql_fetch_array($res_get_layer))
{
	$survey_layer_name[$count_layer] = $row_get_layer['layout_name'];
	$count_layer++;
}
$layer_array = array();
$count = 1;

/*------------------> Display clickable layers <-----------------------*/
/*echo "<table class=\"surveylayertable\"><tr>";
$divcount = 1;
foreach($survey_layer_name as $layer_name){
	$divlayerid = explode(" ",$layer_name);
	$divlayeridnames .= $divlayerid[0]."-";
	//echo "<span style=\"background:#87CEFF; font-weight:bold;\"  align=\"center\" ><a href=\"#\" style=\"color:white; text-decoration:none;\" onclick=\"divinfo('".$divlayerid[0]."');\">".strtoupper($layer_name)."</a></span>&nbsp;";
	//$divcount++;
	echo "<td onclick=\"divinfo('".$divlayerid[0]."');\">".strtoupper($layer_name)."</td>";
	if($divcount == 5){
		echo "</tr><tr>";
	}
	$divcount++;
	
}
$divlayeridnames = rtrim($divlayeridnames,"-");
echo "</tr></table>";
/*------------------> Operation performed on submit <-----------------------*/
if($_POST['submit'] == 'Save')
{
	$ip_address = $_SERVER['SERVER_ADDR'];
	$transactionid = $survey_id;
	@$trans_date = date('Y-m-d',strtotime(substr($transactionid,-14,8)));
	$data_array = array();
	$row_id_array = $_POST['row_id'];
	$row_value_array = $_POST['survey_input_value'];
	$action_id_array = $_POST['action_id'];
	$layerfirstpart = $_POST['layerfirstpart'];
	
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
				$setvalue = '';
				foreach($booleanactionarray as $value){
				$setvalue .= $value.";";
				}
				$setvalue .= " ";
				
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
		
		$sql_previous_value = "SELECT value FROM survey_output WHERE row_id = '".$row_id."' AND survey_id = '".$survey_id."'";
		$res_previous_value = mysql_query($sql_previous_value);
		$row_pevious_value = mysql_fetch_array($res_previous_value);
		$previous_value = $row_pevious_value['value'];
		
		if($previous_value == 'N')
			$previous_value = 'No';
		else if($previous_value == 'Y')
			$previous_value == 'Yes';

		$sql_check_type = "SELECT type FROM survey_input WHERE row_id = '".$row_id."'";
		$res_check_type = mysql_query($sql_check_type);
		$row_check_type = mysql_fetch_array($res_check_type);
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
		
		/*-----> Check if survey already edited, existing in survey audit temp table on the basis of which either INSERT or UPDATE operation performed <----*/
		$sql_check = "SELECT row_id FROM survey_audit_temp WHERE row_id = '".$row_id."' AND survey_id = '".$survey_id."'";
		$res_check = mysql_query($sql_check);
		$total_row = mysql_num_rows($res_check);
		
		if($total_row>0){
			$sql_insert_update = "UPDATE survey_audit_temp SET action_id = '".$action_id."', value = '".addslashes($setvalue)."' WHERE row_id = '".$row_id."' AND survey_id = '".$survey_id."'";
		}
		else{
			$sql_insert_update = "INSERT INTO survey_audit_temp SET action_id = '".$action_id."', value = '".addslashes($setvalue)."', row_id = '".$row_id."', survey_id = '".$survey_id."'";
		}
			$res_insert_update = mysql_query($sql_insert_update);
			
		$setvalue = '';
	}
		
	header('location:surveyaudit_editsurvey.php?survey_id='.$survey_id.'&mall_name='.$mall_name.'&layerfirstpart='.$layerfirstpart.'');
}
?>
<!---- Display survey data -->
<center>
<table width="100%" align="center" cellspacing="2" border="0" id="maintable" style="font-family:'Courier New', Courier, monospace;"  >
	<tr>
		<td width="100%" align="center" style="font-weight:bold; color:#000000;">Edit Survey</td>
	</tr>
    <tr>
    	<td align="center">
        <?php
        $count_layer = 1;
        $validation_array=array();
		
		$sql_survey_exist_check_surveyaudit = "SELECT MAX(SUBSTRING(survey_audit_id,-14,8)) as max_date, MAX(SUBSTRING(survey_audit_id,-6)) as max_time FROM survey_audit WHERE survey_id = '".$survey_id."'";
		$res_survey_exist_check_surveyaudit = mysql_query($sql_survey_exist_check_surveyaudit);
		$row_survey_exist_check_surveyaudit = mysql_fetch_array($res_survey_exist_check_surveyaudit);
		$max_date = $row_survey_exist_check_surveyaudit['max_date'];
		$max_time = $row_survey_exist_check_surveyaudit['max_time'];
		
		if($max_date != ''){
			$survey_output_table = 'survey_audit';
			$condition = " AND SUBSTRING(SO.survey_audit_id,-14,8) = '".$max_date."' AND SUBSTRING(SO.survey_audit_id,-6) = '".$max_time."' ";
		}
		else
			$survey_output_table = 'survey_output';
					
        foreach($survey_layer_name as $layer_name)
        {
			$sql_select_survey_details = "SELECT SAT.* FROM survey_audit_temp SAT, survey_input SI WHERE SAT.survey_id = '".$survey_id."' 
                                    AND SAT.row_id = SI.row_id AND SI.layout_name = '".$layer_name."' ORDER BY SI.display_order ASC";
        	$res_select_survey_details = mysql_query($sql_select_survey_details);
			$layer_check_total_rows = mysql_num_rows($res_select_survey_details);
			if($layer_check_total_rows<=0){
				$sql_select_survey_details = "SELECT SO.* FROM ".$survey_output_table." SO, survey_input SI WHERE SO.survey_id = '".$survey_id."'".$condition." 
                                    AND SO.row_id = SI.row_id AND SI.layout_name = '".$layer_name."' ORDER BY SI.display_order ASC";
				$res_select_survey_details = mysql_query($sql_select_survey_details);
				$layer_check_total_rows = mysql_num_rows($res_select_survey_details);
			}
		
		
		if($layer_check_total_rows>0){
        $res_select_survey_details = mysql_query($sql_select_survey_details);
        while($row_select_survey_details = mysql_fetch_array($res_select_survey_details))
        {
            $sql_select_survey_input = "SELECT * FROM survey_input WHERE row_id = '".$row_select_survey_details['row_id']."'";
            $res_select_survey_input = mysql_query($sql_select_survey_input);
            $row_select_survey_input = mysql_fetch_array($res_select_survey_input);
            
            $survey_input_type=$row_select_survey_input['type'];
            $survey_input_action=$row_select_survey_input['action'];
            $survey_input_display_name=$row_select_survey_input['display_name'];
            $survey_input_display_table_name=$row_select_survey_input['display_table_name'];
            $mandatory=$row_select_survey_input['mandatory'];
            $validation=$row_select_survey_input['validation'];
            
            $select_data_row_id = $row_select_survey_details['row_id'];
        
            if(!in_array($layer_name,$layer_array))
            {
				$showcount_serial = 1;
				$divlayerid = explode(" ",$layer_name);
				
				$get_next_layer_name = $survey_layer_name[$i++];
				$get_next_layer_explode = explode(" ",$get_next_layer_name);
				$get_next_layer_partone = $get_next_layer_explode[0];
				
				if($count != 1){
					echo "<tr hidden><input type='hidden' name='layerfirstpart' value='$get_next_layer_partone'><td></td></tr></table><div align=\"center\" ><input type=\"submit\" name=\"submit\" value=\"Save\" style=\"width:100px; height:40px; font-weight:bold; background:#CC9966; border-radius:5px; cursor:pointer; font-size:18px; margin-top:4px;\" /></div></form></div><br>";
				}
				
				array_push($layer_array, $layer_name);
				
				if($_GET['layerfirstpart'] == ''){
					if($divlayerid[0] == 'Business'){
						$hidden = "";
						$expand_collapse_symbol = "-";
					}
					else{
						$hidden = "hidden";
						$expand_collapse_symbol = "+";
					}
				}
				
				if($_GET['layerfirstpart'] != ''){
					if($_GET['layerfirstpart'] == $divlayerid[0]){
						$hidden = "";
						$expand_collapse_symbol = "-";
					}
					else{
						$hidden = "hidden";
						$expand_collapse_symbol = "+";
					}
				}
                
				$divlayer_string .= $divlayerid[0]."-";
				
				echo "<div class=\"layertab\"><div class=\"expand_collapse\" id=\"expand_collapse_symbol_".$divlayerid[0]."\">".$expand_collapse_symbol."</div><div class=\"spanlayertab\" onclick=\"showlayerinfo('".$divlayerid[0]."')\">".strtoupper($layer_name)."</div></div><div id=\"".$divlayerid[0]."\"  $hidden><form method='POST' action=''><br><table class='layertableinfo'>";
				//echo "<tr><td colspan=\"3\" align=\"center\" style=\"background:#FFE4B5;\"><b>".$layer_name."</b></td></tr>";
				echo "<tr>
						<th>SI</th>
						<th align=\"center\">Name</th>
						<th align=\"center\">Value</th>
					  </tr>";
			  
                //$i=$i+1;
			}
            
            echo "<tr ><td>$showcount_serial<input type=\"hidden\" name=\"row_id[]\" value=\"$row_select_survey_details[row_id]\">
                    <input type=\"hidden\" name=\"action_id[]\" value=\"$row_select_survey_details[action_id]\"></td>";
            
            if($survey_input_type == ' ' || $survey_input_type == '' || $survey_input_type == 'double')
            {
                /*echo "<td><b>".$survey_input_display_name."</b></td>";
                echo "<td><input type=\"text\" name=\"survey_input_value_text".$select_data_row_id."[]\" value=\"$row_select_survey_details[value]\"></td>";*/
				if(strpos($survey_input_display_name,"#") == true){
					$displaynamearray = explode("#",$survey_input_display_name);
					$survey_input_display_name = $displaynamearray[0];
					$survey_details_value_array = explode("#",$row_select_survey_details['value']);
					$surveydetailsvalue = $survey_details_value_array[0];
				}
				else{
					$surveydetailsvalue = $row_select_survey_details['value'];
				}
                echo "<td><b>".$survey_input_display_name."</b></td>";
                echo "<td><input type=\"text\" name=\"survey_input_value_text".$select_data_row_id."[]\" value=\"$surveydetailsvalue\"></td>";
				
                
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
                      echo "<input type=\"radio\" name=\"survey_input_value_boolean_action_".$select_data_row_id."[]\" value=\"$boolean_value\" $checked onchange=\"javascript:action_display('".$select_data_row_id."','".$row_select_survey_input['action_id']."');\">&nbsp;&nbsp;$boolean_value"."&nbsp;&nbsp;&nbsp;";
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
                        
                        $sql_table_value_action = "SELECT DISTINCT $column_name FROM ".$table_name." WHERE $column_name!=''";
                        $res_table_value_action = mysql_query($sql_table_value_action);
                
                        if(strstr($row_select_survey_details['value'],';',true) || $survey_input_type == 'checkbox')
                        {
                            echo "<td>";
                            while($row_table_value_action = mysql_fetch_array($res_table_value_action))
                            {
                                if (in_array($row_table_value_action[$column_name],$table_value_array)) $checked = " checked='checked' ";
                                else  $checked = " ";
                                echo "<input type=\"checkbox\" id=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\" name=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\"  value=\"$row_table_value_action[$column_name]\" $checked>$row_table_value_action[$column_name]";
                            }
                            echo "</td>";
                        }
                        else
                        { 
                            echo "<td><div id=\"multiple_radio_".$select_data_row_id."\" style=\"max-height:30px; overflow-y:scroll;\" onclick=\"multiple_radio_height_increase('".$select_data_row_id."');\"  onmouseout=\"multiple_radio_height_decrease('".$select_data_row_id."');\">";
							$hidden_multiple_radio_list_view = "";
                            while($row_table_value_action = mysql_fetch_array($res_table_value_action))
                            {
                                if (in_array($row_table_value_action[$column_name],$table_value_array)){
									$checked = " checked='checked' ";
									echo "$row_table_value_action[$column_name]";
								}
                                else{
									$checked = " ";
								}
								$hidden_multiple_radio_list_view .= "<input type=\"radio\" id=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\" name=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\"  value=\"$row_table_value_action[$column_name]\" $checked>$row_table_value_action[$column_name]"."<br>";
                                //echo "<input type=\"radio\" id=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\" name=\"survey_input_value_boolean_action_".$select_data_row_id.$row_select_survey_input['action_id']."[]\"  value=\"$row_table_value_action[$column_name]\" $checked>$row_table_value_action[$column_name]"."<br>";
                            }
							echo "<div id=\"hidden_multiple_radio_list_view_".$select_data_row_id."\" hidden>".$hidden_multiple_radio_list_view."</div>";
                            echo "</div></td>";
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
                        echo"<input type=\"radio\" name=\"survey_input_value_boolean_".$select_data_row_id."[]\" value=\"$boolean_value\" $checked>&nbsp;&nbsp;$boolean_value&nbsp;&nbsp;&nbsp;";
                    }
                    echo "</td>";
                }
                if($mandatory=='Y' || $validation!='')
                {
                    $validation_value=$select_data_row_id.'#'.$mandatory.'#'.$validation_type.'#'.$validation;
                    array_push($validation_array,$validation_value);
                }
            }
            else if(($survey_input_type == 'radio' || $survey_input_type == 'checkbox') && $row_select_survey_input['action'] != 'Click')
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
					if($validation != '')
						$table_name = $column_name;
				}
				
        
                $sql_table_value = "SELECT DISTINCT $column_name FROM ".$table_name." WHERE $column_name!=''";
                $res_table_value = mysql_query($sql_table_value);
        
                if(strstr($row_select_survey_details['value'],';',true) || $survey_input_type == 'checkbox')
                {
					//echo "<td>";
/*----------------> Two span created to form survey category string one hidden and another shown<------------------*/
					if($column_name == 'cat_name'){
						$span_cat_nothidden = "<span id=\"cat_span_nothidden_".$select_data_row_id."\" >";
						$hidden_span_category = "<span id=\"cat_span_".$select_data_row_id."\" hidden>";
						$subcat_spanid = $select_data_row_id;
					}
/*----------------> One span created to form survey sub category string <------------------*/					
					if($column_name == 'sub_cat_name')
						$span_subcategory = "<span id=\"sub_cat_span_".$subcat_spanid."\">";
						
					echo "<td><div id=\"multiple_radio_".$select_data_row_id."\" style=\"max-height:30px; overflow-y:scroll;\" onclick=\"multiple_radio_height_increase('".$select_data_row_id."');\" >";
                    while($row_table_value = mysql_fetch_array($res_table_value))
                    {
                        if (in_array($row_table_value[$column_name],$table_value_array)){
							$checked = " checked='checked' ";
							$span_cat_nothidden .= "<input type=\"checkbox\" name=\"survey_input_value_checkbox_".$select_data_row_id."[]\" class=\"category_chk\"  value=\"$row_table_value[$column_name]\" onclick=\"get_sub_category('".$select_data_row_id."')\" $checked>&nbsp;&nbsp;$row_table_value[$column_name]"."<br>";
							$span_subcategory .= "<input type=\"checkbox\" name=\"survey_input_value_checkbox_".$select_data_row_id."[]\"  value=\"$row_table_value[$column_name]\" $checked>&nbsp;&nbsp;$row_table_value[$column_name]"."<br>";
						}
                        else{
							$checked = " ";
							$hidden_span_category .= "<input type=\"checkbox\" name=\"survey_input_value_checkbox_".$select_data_row_id."[]\" class=\"category_chk\"  value=\"$row_table_value[$column_name]\" onclick=\"get_sub_category('".$select_data_row_id."')\" $checked>&nbsp;&nbsp;$row_table_value[$column_name]"."<br>";
						}
						
                        if($column_name != 'cat_name' && $column_name != 'sub_cat_name')
							echo "<input type=\"checkbox\" name=\"survey_input_value_checkbox_".$select_data_row_id."[]\"  value=\"$row_table_value[$column_name]\" $checked>&nbsp;&nbsp;$row_table_value[$column_name]"."<br>";
						
                    }
					if($column_name == 'cat_name'){
						$span_cat_nothidden .= "</span><br>";
						$hidden_span_category .= "</span><br>";
						echo $span_cat_nothidden."<input name=\"category\" type=\"button\" value=\"Add More\" style=\"margin-bottom:2px;\" id=\"cat_button_".$select_data_row_id."\" onclick=\"show_hidden_cat_span('".$select_data_row_id."');\" />".$hidden_span_category;
					}
					
					if($column_name == 'sub_cat_name'){
						$span_subcategory .= "</span><br>";
						echo $span_subcategory;
					}    
					echo "</div></td>";
                }
                else
                { 
                    echo "<td><div id=\"multiple_radio_".$select_data_row_id."\" style=\"max-height:30px; overflow-y:scroll;\" onclick=\"multiple_radio_height_increase('".$select_data_row_id."');\"  onmouseout=\"multiple_radio_height_decrease('".$select_data_row_id."');\">";
					$hidden_multiple_radio_list = "";
                    while($row_table_value = mysql_fetch_array($res_table_value))
                    {
                        if (in_array($row_table_value[$column_name],$table_value_array)){
							$checked = " checked";
							echo "$row_table_value[$column_name]";
						}
						else{
							$checked = " ";
						}
						$hidden_multiple_radio_list .= "<input type=\"radio\" name=\"survey_input_value_radio_".$select_data_row_id."[]\"  value=\"$row_table_value[$column_name]\" $checked>&nbsp;&nbsp;$row_table_value[$column_name]<br>";
                      //echo "<input type=\"radio\" name=\"survey_input_value_radio_".$select_data_row_id."[]\"  value=\"$row_table_value[$column_name]\" $checked>$row_table_value[$column_name]"."<br>";
                    }
					echo "<div id=\"multiple_radio_option_".$select_data_row_id."\" hidden>".$hidden_multiple_radio_list."</div>";
                    echo "</div></td>";
                }
            }
			else if($row_select_survey_input['action'] != '' && $row_select_survey_input['action'] == 'Click')
			{
				echo "<td><b>".$survey_input_display_name."</b></td>";
				echo "<td><input type=\"hidden\" name=\"survey_input_value_text".$select_data_row_id."[]\" value=\"".$row_select_survey_details['value']."\" id=\"image_box_".$select_data_row_id."\"><br>";
				
				if(strpos($row_select_survey_details['value'],";") == true){
					$image_string = $row_select_survey_details['value'];
					$image_string_array = explode(";",$image_string);
					array_pop($image_string_array);
					foreach($image_string_array as $imagejpeg){
						$imagejpeg_array = explode(".",$imagejpeg);
						echo "<img src=\"http://acedns.in/acednsproduct/upload/LIPL/".$imagejpeg."\" width=\"100\" height=\"80\" id=\"".$imagejpeg_array[0]."\">&nbsp;<input name=\"discard\" type=\"button\" value=\"Discard\" id=\"discard_button_".$imagejpeg_array[0]."\" onclick=\"discard_image('".$imagejpeg_array[0]."','".$select_data_row_id."')\" /><br><br>";
					}
				}
				else{
					echo "No images found";
				}
				//echo "<td><textarea name=\"survey_input_value_text".$select_data_row_id."[]\" rows=\"8\" cols=\"40\" readonly>".$row_select_survey_details['value']."</textarea></td>";
			}
			else if($row_select_survey_input['action'] == '' && $survey_input_type == 'rating'){
				echo "<td><b>".str_replace("#"," ",$survey_input_display_name)."</b></td>";
				echo "<td>";
				for($i=1;$i<=4;$i++){
					if($i == $row_select_survey_details['value'])
						$checked = 'checked';
					else
						$checked = '';
					echo $i."<input type=\"radio\" name=\"survey_input_value_radio_".$select_data_row_id."[]\" value=\"$i\" $checked>&nbsp;";
				}
				echo "</td>";
			}
            $count++;
			$showcount_serial++;
            echo "</tr>";
        }
		}
        }
		echo "</table><div align=\"center\" ><input type=\"submit\" name=\"submit\" value=\"Save\" style=\"width:80px; height:30px; font-weight:bold; background:#CC9966; border-radius:5px; cursor:pointer;\" /></div></div>";
        ?>
        </td>
    </tr>
    <tr>
    	<td align="center">
        <div id="final_submit_id" ><input type="button" name="final_submit" value="Send For Approval" id="final_submit" style="height:40px; font-weight:bold; background:#CC9966; border-radius:5px; cursor:pointer; width:200px; font-size:18px;" onClick="final_submit_check();" /></div>
        </td>
    </tr>
</table>
<?php $divlayer_string = rtrim($divlayer_string,"-"); ?>
<!--<form method="POST" action="">
<div id="display"></div><br>
<div align="center" id="submit_id" hidden><input type="submit" name="submit" value="Submit" style="width:80px; height:30px; font-weight:bold; background:#CC9966; border-radius:5px; cursor:pointer;" /></div>
<br><br>
</form>-->


</center>
<script>
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
	
	function divinfo(layer_name){
		var divlayeridnames = '<?php echo $divlayeridnames; ?>';
		var divlayersplit = divlayeridnames.split("-");
		for(i=0;i<divlayersplit.length;i++){
			var element_check = document.getElementById(''+divlayersplit[i]+'');
			if(element_check!= null){
				if(divlayersplit[i] == layer_name)
					continue;
				document.getElementById(''+divlayersplit[i]+'').hidden = true;
			}
		}
		
		var element_check = document.getElementById(''+layer_name+'');
		if(element_check === null){
			//document.getElementById("display").innerHTML = "<center><strong><font color='red'>No records for this layer</font></strong></center>";
			alert('No records found for this layer');
			//document.getElementById("submit_id").hidden = true;
			document.getElementById("final_submit_id").hidden = true;
		}
		else{
			//alert('Hii');
			document.getElementById(''+layer_name+'').hidden = false;
			//document.getElementById("submit_id").hidden = false;
			document.getElementById("final_submit_id").hidden = false;
		}
			
		$.post("surveyaudit_session_setlayer.php",
		{
			layer_name: layer_name
		},
		function(data, status){
			//alert(data);
		});
	}
	
	function final_submit_check(){
		$.post("surveyaudit_finalsubmit_check.php",
		{
			survey_id: '<?php echo $survey_id; ?>'
		},
		function(data, status){
			alert(data);
		});
	}
	
	<?php
		if($_POST['submit'] == 'Submit'){
			echo "alert('Data updated');";
			echo "document.getElementById(\"display\").innerHTML = document.getElementById('".$_SESSION['layer_name']."').innerHTML;";
			//echo "document.getElementById(\"submit_id\").hidden = false;";
			echo "document.getElementById(\"final_submit_id\").hidden = false;";
		}
	?>
	
	function show_hidden_cat_span(span_id){
		document.getElementById('cat_span_'+span_id).hidden = false;
		document.getElementById('cat_button_'+span_id).hidden = true;
	}
	
	function get_sub_category(span_id){
		//alert(span_id);
		var survey_id = '<?php echo $survey_id; ?>';
		var category_chk = new Array;
		$('.category_chk:checked').each(function() {
			category_chk.push($(this).val()+';'); //';' added to separate each checked value
		});
				
		//alert(JSON.stringify(category_chk));
		var encode_category = escape(category_chk);
		document.getElementById('sub_cat_span_'+span_id+'').innerHTML = '<img src="small-ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('surveyaudit_getsubcateogry.php?span_id='+span_id+'&survey_id='+survey_id+'&category='+encode_category,
		'sub_cat_span_'+span_id,0);
		
	}
	
	function multiple_radio_height_increase(row_id){
		document.getElementById('multiple_radio_'+row_id).style.maxHeight = '150px';
		
		var multiple_radio_option = document.getElementById('multiple_radio_option_'+row_id);
		if(multiple_radio_option != null)
			document.getElementById('multiple_radio_'+row_id).innerHTML = document.getElementById('multiple_radio_option_'+row_id).innerHTML;
		
		var hidden_multiple_radio_list_view = document.getElementById('hidden_multiple_radio_list_view_'+row_id);
		if(hidden_multiple_radio_list_view != null)
			document.getElementById('multiple_radio_'+row_id).innerHTML = document.getElementById('hidden_multiple_radio_list_view_'+row_id).innerHTML;
		//alert(test);
		
	}
	
	function multiple_radio_height_decrease(row_id){
		//alert(row_id);
		document.getElementById('multiple_radio_'+row_id).style.maxHeight = '30px';
	}
	
	function showlayerinfo(layer_name){
		var divlayeridnames = '<?php echo $divlayer_string; ?>';
		var divlayersplit = divlayeridnames.split("-");
		for(i=0;i<divlayersplit.length;i++){
			var element_check = document.getElementById(''+divlayersplit[i]+'');
			if(element_check!= null){
				if(divlayersplit[i] == layer_name){
					if(document.getElementById(''+divlayersplit[i]+'').hidden == true){
						document.getElementById(''+divlayersplit[i]+'').hidden = false;
						document.getElementById('expand_collapse_symbol_'+divlayersplit[i]+'').innerHTML = '-';
					}
					else{
						document.getElementById(''+divlayersplit[i]+'').hidden = true;
						document.getElementById('expand_collapse_symbol_'+divlayersplit[i]+'').innerHTML = '+';
					}
				}
					//$('#'+divlayersplit[i]+'').fadeIn(800);
				else{
					document.getElementById(''+divlayersplit[i]+'').hidden = true;
					document.getElementById('expand_collapse_symbol_'+divlayersplit[i]+'').innerHTML = '+';
				}
			}
		}
	}
	
	function discard_image(image_id,row_id){
		$('#'+image_id+'').fadeOut(500);
		$('#discard_button_'+image_id+'').fadeOut(500);
		var image_box = document.getElementById('image_box_'+row_id).value;
		//alert(image_box);
		var image_name = image_id+".jpeg;";
		//alert(image_name);
		image_box = image_box.replace(""+image_name+"","");
		//alert(image_box);
		document.getElementById('image_box_'+row_id).value = image_box+' ';
	}
	
</script>
<?php
	include "surveyaudit_footer.php";
?>