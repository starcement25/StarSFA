<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlsurveydetails="SELECT row_id,action_id,menu_id,layout_name,display_name,type,display_table_name,mandatory,action,validation,display_order FROM survey_input WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$rssurveydetails=mysqli_query($link,$sqlsurveydetails);
$count=mysqli_num_rows($rssurveydetails);
$contentsrowcolumn=$count.'¥'.'11';
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
				$contents  .= (($display_order!='')?$display_order: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents =$contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
    }
	else
	{
		$datacontents = '0'.'¥'.'0';
	}	
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=survey_input_details.txt");
	print "$datacontents"; 		
?>
