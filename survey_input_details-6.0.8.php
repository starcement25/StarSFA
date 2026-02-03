<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];

$type=$_REQUEST['type'];

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlsurveydetails="SELECT row_id,action_id,menu_id,layout_name,display_name,type,display_table_name,mandatory,action,validation,display_order,
				survey_type,survey_sub_menu,acedns,insert_table_detail FROM survey_input WHERE 1 ".$login_condition."";
$rssurveydetails=mysqli_query($link,$sqlsurveydetails);
$count=mysqli_num_rows($rssurveydetails);
$contentsrowcolumn=$count.'¥'.'15';
if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	
		/*$survey_input_value_array=unserialize($survey_input_value);
	
		foreach( $survey_input_value_array as $key => $value ) :
		   $contents  = (($value!='')?$value: ' ')."^";
		   $linecontents  .= $contents;
		   if($i%5==0)
		   {
			$linecontents  .= "\n";
		   }
		   $i++;
		endforeach;
		$contentsrowcolumn=floor($i/5).'¥'.'5';*/
		while($rowsurveydetails = mysqli_fetch_assoc($rssurveydetails))
		{
			$row_id=$rowsurveydetails['row_id'];
			$action_id=$rowsurveydetails['action_id'];
			$menu_id=$rowsurveydetails['menu_id'];
			$layout_name=$rowsurveydetails['layout_name'];
			$display_name=$rowsurveydetails['display_name'];
			$type=$rowsurveydetails['type'];
			$mandatory=$rowsurveydetails['mandatory'];
			$action=$rowsurveydetails['action'];
			$display_table_name=$rowsurveydetails['display_table_name'];
			$validation=$rowsurveydetails['validation'];
			$display_order=$rowsurveydetails['display_order'];
			$survey_type=$rowsurveydetails['survey_type'];
			$survey_sub_menu=$rowsurveydetails['survey_sub_menu'];
			$acedns=$rowsurveydetails['acedns'];
			$insert_table_detail=$rowsurveydetails['insert_table_detail'];
			
			//For tabular form data
			if($type=='checkboxmatrix'){
				$display_table_name_array=explode('#',$display_table_name);
				$sqlx_axis_val="SELECT $display_table_name_array[0] FROM $display_table_name_array[0]";
				$rsx_axis_val=mysqli_query($link,$sqlx_axis_val);
				$x_axis_val='';
				while($rowx_axis_val=mysqli_fetch_assoc($rsx_axis_val))
				{
					$x_axis_val.=$rowx_axis_val[$display_table_name_array[0]].':';
				}
				$final_x_axis_val=substr($x_axis_val,0,-1);
				
				$sqly_axis_val="SELECT $display_table_name_array[1] FROM $display_table_name_array[1]";
				$rsy_axis_val=mysqli_query($link,$sqly_axis_val);
				$y_axis_val='';
				while($rowy_axis_val=mysqli_fetch_assoc($rsy_axis_val))
				{
					$y_axis_val.=$rowy_axis_val[$display_table_name_array[1]].':';
				}
				$final_y_axis_val=substr($y_axis_val,0,-1);
				$action=$final_y_axis_val.'#'.$final_x_axis_val;
				$display_table_name='';
			}
			if($type=='radiomatrix'){
				$sql_table_val="SELECT $display_table_name FROM $display_table_name";
				$rs_table_val=mysqli_query($link,$sql_table_val);
				$table_val='';
				while($row_table_val=mysqli_fetch_assoc($rs_table_val))
				{
					$table_val.=$row_table_val[$display_table_name].':';
				}
				$final_table_val=substr($table_val,0,-1);
				$action=$final_table_val;
				$display_table_name='';
			}
			
			if(empty($_REQUEST['type']) && !isset($_REQUEST['type']))
			{
				$contents  = (($row_id!='')?$row_id: ' ')."^";
				$contents  .= (($action_id!='')?$action_id: ' ')."^";
				$contents  .= (($menu_id!='')?$menu_id: ' ')."^";
				$contents  .= (($layout_name!='')?$layout_name: ' ')."^";
				$contents  .= (($display_name!='')?$display_name: ' ')."^";
				$contents  .= (($type!='')?$type: ' ')."^";
				$contents  .= (($display_table_name!='')?$display_table_name: ' ')."^";
				$contents  .= (($mandatory!='')?$mandatory: ' ')."^";
				$contents  .= (($action!='')?$action: ' ')."^";
				$contents  .= (($validation!='')?$validation: ' ')."^";
				$contents  .= (($display_order!='')?$display_order: ' ')."^";
				$contents  .= (($survey_type!='')?$survey_type: ' ')."^";
				$contents  .= (($survey_sub_menu!='')?$survey_sub_menu: ' ')."^";
				$contents  .= (($acedns!='')?$acedns: ' ')."^";
				$contents  .= (($insert_table_detail!='')?$insert_table_detail: ' ');
				$linecontents  .= $contents."\n";
			}
			else
			{
				$contents .='<data><row_id><![CDATA['.mb_convert_encoding($row_id, 'UTF-8', 'UTF-8').']]></row_id><action_id><![CDATA['.mb_convert_encoding($action_id, 'UTF-8', 'UTF-8').']]></action_id><menu_id><![CDATA['.mb_convert_encoding($menu_id, 'UTF-8', 'UTF-8').']]></menu_id><layout_name><![CDATA['.mb_convert_encoding($layout_name, 'UTF-8', 'UTF-8').']]></layout_name><display_name><![CDATA['.mb_convert_encoding($display_name, 'UTF-8', 'UTF-8').']]></display_name><type><![CDATA['.mb_convert_encoding($type, 'UTF-8', 'UTF-8').']]></type><mandatory><![CDATA['.mb_convert_encoding($mandatory, 'UTF-8', 'UTF-8').']]></mandatory><action><![CDATA['.mb_convert_encoding($action, 'UTF-8', 'UTF-8').']]></action><display_table_name><![CDATA['.mb_convert_encoding($display_table_name, 'UTF-8', 'UTF-8').']]></display_table_name><validation><![CDATA['.mb_convert_encoding($validation, 'UTF-8', 'UTF-8').']]></validation><display_order><![CDATA['.mb_convert_encoding($display_order, 'UTF-8', 'UTF-8').']]></display_order><survey_type><![CDATA['.mb_convert_encoding($survey_type, 'UTF-8', 'UTF-8').']]></survey_type><survey_sub_menu><![CDATA['.mb_convert_encoding($survey_sub_menu, 'UTF-8', 'UTF-8').']]></survey_sub_menu></data>';
			}
		}
		if(empty($_REQUEST['type']) && !isset($_REQUEST['type']))
		{
			$datacontents =$contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
		$datacontents ="<?xml version='1.0' encoding='UTF-8'?><root><curdatetime><![CDATA[".mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8')."]]></curdatetime><recordset>".$contents."</recordset></root>";
		}
    }
	else
	{
		if(empty($_REQUEST['type']) && !isset($_REQUEST['type']))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents ="<?xml version='1.0' encoding='UTF-8'?<curdatetime><![CDATA[".mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8')."]]></curdatetime>0</recordset>";
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$emp_code='';
	$url = "http://salesmpower.acedns.in/survey_input_details-6.0.8.php?nick_name=$nick_name&last_update_time=$last_update_time&type=$type&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);	
	if(empty($_REQUEST['type']) && !isset($_REQUEST['type']))
	{
		header("Content-type: application/text"); 
		header("Content-Disposition: attachment; filename=survey_input_details.txt");
		print "$datacontents";
	}
	else
	{
		echo  $datacontents;
	}
?>