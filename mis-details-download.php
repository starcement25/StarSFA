<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$sqlquery="SELECT *,DATE_FORMAT(`date`,'%d-%m-%Y') AS date FROM mis_details ORDER BY nick_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$contentsrowcolumn=$count.'¥'.'4';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowmis = mysqli_fetch_assoc($result))
		{
			$nick_name=$rowmis['nick_name'];
			$total_force = $rowmis['no_of_total_force'];
			$date=$rowmis['date'];
			$attendance=$rowmis['attendance'];
			$transactions=$rowmis['transactions'];
				
				$contents  = (($nick_name!='')?$nick_name: ' ')."^";
				$contents  .= (($date!='')?$date: ' ')."^";
				$contents  .= (($attendance!='')?$attendance: ' ')."^";
				$contents  .= (($transactions!='')?$transactions: ' ')."^";
				$contents  .= (($total_force!='')?$total_force: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=mis_details.txt");
	print "$datacontents"; 		
?>
