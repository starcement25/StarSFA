<?php
ob_start();
session_start();


header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=stock_audit_Report.xls"); 
header("Pragma: no-cache"); 
header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
echo ucwords($_SESSION['excel_data']); //- See more at: http://www.discussdesk.com/download-mysql-data-into-excel-file-in-php.htm#sthash.5bPI72JI.dpuf

?>