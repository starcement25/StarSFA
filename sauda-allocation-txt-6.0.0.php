<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$emp_code=substr($emp_code,0,5);

$sqlempname="SELECT reporting_to,designation FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempname=mysqli_query($link,$sqlempname);
$rowempname=mysqli_fetch_assoc($rsempname);
$reporting_to=$rowempname['reporting_to'];
$designation=$rowempname['designation'];

$sqlchkallocationaccess="SELECT flag FROM sauda_allocation_access WHERE designation='".$designation."'";
$rschkallocationaccess=mysqli_query($link,$sqlchkallocationaccess);
$rowchkallocationaccess=mysqli_fetch_assoc($rschkallocationaccess);
$allocation_flag=$rowchkallocationaccess['flag'];
if($allocation_flag=='yes' && $reporting_to!='')
{
	$allocation_fetch_emp_code=$emp_code;
	$emp_hierarchy_condition="emp_code='".$allocation_fetch_emp_code."'";
}
else if($allocation_flag=='no' && $reporting_to!='')
{
	$allocation_fetch_emp_code=$reporting_to;
	$emp_hierarchy_condition="emp_code='".$allocation_fetch_emp_code."'";
}
else if($allocation_flag=='yes' && $reporting_to=='')
{
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}

//---------------------------------------standardization of conversion-------------------------------------------------------------
if(sauda_allocation_basedon_filter == 1)
{
	$fillter_table_value = 'product_group_master';
	$fillter_code= 'product_group_code';
	$fillter_name = 'product_group_name';
	$fillteracronym = "PGM";
}
else if(sauda_allocation_basedon_filter == 2)
{
	$fillter_table_value = 'product_sub_group_master';
	$fillter_code = 'product_sub_group_code';
	$fillter_name = 'product_sub_group_name';
	$fillteracronym = "PSGM";
}
else if(sauda_allocation_basedon_filter == 3)
{
	$fillter_table_value = 'product_brand_master';
	$fillter_code = 'product_brand_code';
	$fillter_name = 'product_brand_name';
	$fillteracronym = "PBM";
}
else if(sauda_allocation_basedon_filter == 4)
{
	$fillter_table_value = 'product_master';
	$fillter_code = 'product_code';
	$fillter_name = 'product_name';
	$fillteracronym = "PM";
}
$sqlfilterwiseconversion="SELECT conversion_factor_two,UOM3,$fillter_code FROM product_master GROUP BY ".$fillter_code;
$rsfilterwiseconversion=mysqli_query($link,$sqlfilterwiseconversion);
while($rowfilterwiseconversion=mysqli_fetch_assoc($rsfilterwiseconversion))
{
	$val_fillter_code=$rowfilterwiseconversion["$fillter_code"];
	${conversion_two.$val_fillter_code}=$rowfilterwiseconversion["conversion_factor_two"];
}
//---------------------------------------End of standardization of conversion-------------------------------------------------------------
if($emp_code!='C0007'){
 	$sqlquery="SELECT *,BAL FROM sauda_allocation WHERE ".$emp_hierarchy_condition." GROUP BY product_filter_code";
 }
 else
 {
	$sqlquery="SELECT *,BAL FROM sauda_allocation WHERE 1 GROUP BY product_filter_code";
 }
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'4';
		while($rowsauda = mysqli_fetch_assoc($result))
		{
			//$emp_code=$rowsauda['emp_code'];
			$product_filter_code	=$rowsauda['product_filter_code'];
			$qty=round(($rowsauda['BAL']*${conversion_two.$product_filter_code}),2);
			$allot_qty=round(($rowsauda['allot_qty']*${conversion_two.$product_filter_code}),2);
			
				$contents  = (($emp_code!='')?$emp_code: ' ')."^";
				$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
				$contents  .= (($qty!='')?$qty: ' ')."^";
				$contents  .= (($allot_qty!='')?$allot_qty: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		//$datacontents = '0'.'¥'.'0';
		$sqlqueryproductfilter="SELECT $fillter_code FROM $fillter_table_value";
		$resultproductfilter = mysqli_query($link,$sqlqueryproductfilter);
		$countproductfilter=mysqli_num_rows($resultproductfilter);
		$contentsrowcolumn=$countproductfilter.'¥'.'4';

		while($rowproductfilter = mysqli_fetch_assoc($resultproductfilter))
		{
			$product_filter_code	=$rowproductfilter[$fillter_code];
			$qty=0;
			$allot_qty=0;
				$contents  = (($emp_code!='')?$emp_code: ' ')."^";
				$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
				$contents  .= $qty."^";
				$contents  .= $allot_qty;
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/sauda-allocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/sauda-allocation-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=sauda_allocation.txt");
	print "$datacontents"; 		
?>
