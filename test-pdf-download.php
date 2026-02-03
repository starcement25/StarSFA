<?php
$generated_file_name = "Activity_report_E0163.pdf";
$generated_file_path ="starpdf/".$generated_file_name;
$cmd = "wkhtmltopdf -T 0 -R 0 -B 0 -L 0 http://salesmpower.acedns.in/STAR_activity_PDF_whatsapp.php?".$generated_file_path;
$output=array();
exec($cmd,$output,$worked);
echo $worked;
if($worked==0){
$pdf_generated = "YES";
$pdf_msg = "";
}else{
$pdf_generated = "NO";
$pdf_msg = "But pdf not generated.";	
}
?>