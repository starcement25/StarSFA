<?php
$datacontents=$_POST['report_data'];

header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=Order_Download_Report.xls"); 
			print "$datacontents";

?>