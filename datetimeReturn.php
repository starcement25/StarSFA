<?php
echo date_default_timezone_set("Asia/Calcutta");

$date=gmdate('d');
$month=gmdate('m');
$year=gmdate('Y');

$hour=gmdate('H');
$minute=gmdate('i');
$second=gmdate('s');

echo $val=$year.$month.$date.$hour.$minute.$second;

$jsonval=array(    
        'stamp' => $val,    
        'date' => $date,    
        'month' => $month,
		'year' => $year);
		
echo $encoded_value=json_encode($jsonval);

/*$a = 2.20032324;
echo $f = sprintf ("%.2f", $a);*/

?>		