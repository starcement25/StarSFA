<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

//$sqlsurveydetails="SELECT row_id,type,value,dependent_on,dependent_value,action FROM table_view WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$sqlsurveydetails="SELECT row_id,type,value,dependent_on,dependent_value,action FROM table_view_bkup ";
$rssurveydetails=mysqli_query($link,$sqlsurveydetails);
$count=mysqli_num_rows($rssurveydetails);
$contentsrowcolumn=$count.'¥'.'6';
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
			$type=$rowsurveydetails['type'];
			$value=str_replace("\n","",$rowsurveydetails['value']);
			$dependent_on=$rowsurveydetails['dependent_on'];
			$dependent_value=$rowsurveydetails['dependent_value'];
			$action=$rowsurveydetails['action'];
						
			//For tabular form data
					
			$contents  = (($row_id!='')?$row_id: ' ')."^";
			$contents  .= (($type!='')?$type: ' ')."^";
			$contents  .= (($value!='')?$value: ' ')."^";
			$contents  .= (($dependent_on!='')?$dependent_on: ' ')."^";
			$contents  .= (($dependent_value!='')?$dependent_value: ' ')."^";
			$contents  .= (($action!='')?$action: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents =$contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
    }
	else
	{
		$datacontents = '0'.'¥'.'0';
	}	
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=survey_table_view.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
