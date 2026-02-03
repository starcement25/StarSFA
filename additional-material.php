<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

$sqladditional="SELECT * FROM additional_material ORDER BY  mi_type ASC,prod_desc ASC ";
$rsadditional=mysqli_query($link,$sqladditional);
$count=mysqli_num_rows($rsadditional);
$contentsrowcolumn=$count.'¥'.'5';
if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowadditional = mysqli_fetch_assoc($rsadditional))
		{
			$prod_code=$rowadditional['prod_code'];
			$prod_desc=$rowadditional['prod_desc'];
			$uom=$rowadditional['uom'];
			$rate=$rowadditional['rate'];
			$mi_type=$rowadditional['mi_type'];

			$contents  = (($prod_code!='')?$prod_code: ' ')."^";
			$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
			$contents  .= (($uom!='')?$uom: ' ')."^";
			$contents  .= (($rate!='')?$rate: ' ')."^";
			$contents  .= (($mi_type!='')?$mi_type: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents =$contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
    }
	else
	{
		$datacontents = '0'.'¥'.'0';
	}	
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=additional_mat.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
