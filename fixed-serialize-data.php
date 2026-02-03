<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlsurveydetails="SELECT survey_input_id,survey_input_value FROM survey_input WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') 
					ORDER BY DATE_FORMAT(download_time,'%Y-%m-%d %h%i%s') DESC LIMIT 1,1";
$rssurveydetails=mysqli_query($link,$sqlsurveydetails);
$rowsurveydetails=mysqli_fetch_assoc($rssurveydetails);
$survey_input_value=$rowsurveydetails['survey_input_value'];


function fix_corrupted_serialized_string($string) {
    $tmp = explode(':"', $string);
    $length = count($tmp);
    for($i = 1; $i < $length; $i++) {    
        list($string) = explode('"', $tmp[$i]);
        $str_length = strlen($string);    
        $tmp2 = explode(':', $tmp[$i-1]);
        $last = count($tmp2) - 1;    
        $tmp2[$last] = $str_length;         
        $tmp[$i-1] = join(':', $tmp2);
    }
    return join(':"', $tmp);
}

if( !empty( $survey_input_value ) )
    {
		echo $fix_data=fix_corrupted_serialized_string($survey_input_value);
		
		/*foreach( unserialize($fix_data) as $key => $value ) :
		   $contents  = (($value!='')?$value: ' ')."^";
		   $linecontents  .= $contents;
		   if($i%5==0)
		   {
		   	$linecontents  .= "\n";
		   }
		   $i++;
        endforeach;
		$contentsrowcolumn=floor($i/5).'¥'.'5';
		$datacontents =$contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		print "$datacontents";*/
	}
?>