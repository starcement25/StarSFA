<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$emp_code=substr($emp_code,0,5);

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(allocation_date) > UNIX_TIMESTAMP('".$last_update_time."')";
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
}else{
	//sk add else condition 08052025
	$fillter_table_value = 'product_group_master';
	$fillter_code= 'product_group_code';
	$fillter_name = 'product_group_name';
	$fillteracronym = "PGM";

}


$sqlfilterwiseconversion="SELECT conversion_factor_two,UOM3,$fillter_code FROM product_master GROUP BY ".$fillter_code;
//echo"<pre>";print_r($sqlfilterwiseconversion);die;
$rsfilterwiseconversion=mysqli_query($link,$sqlfilterwiseconversion);
while($rowfilterwiseconversion=mysqli_fetch_assoc($rsfilterwiseconversion))
{
	$val_fillter_code=$rowfilterwiseconversion["$fillter_code"];
	${conversion_two.$val_fillter_code}=$rowfilterwiseconversion["conversion_factor_two"];
}
//---------------------------------------End of standardization of conversion-------------------------------------------------------------
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$current_date=$year.'-'.$month.'-'.$date;

if($emp_code!='C0007'){
 	$sqlquery="SELECT * FROM sauda_allocation_log WHERE ".$emp_hierarchy_condition.$login_condition." AND 
				SUBSTRING(allocation_date,1,10)='".$current_date."'  AND allocation_id<>''";
 }
 else
 {
	$sqlquery="SELECT * FROM sauda_allocation_log WHERE 1 ".$login_condition." AND SUBSTRING(allocation_date,1,10)='".$current_date."' AND allocation_id<>''";
 }
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		//$contentsrowcolumn=$count.'¥'.'6';
		$product_group_code_array=array();
		$countcolumn=0;
		while($rowsaudalog = mysqli_fetch_assoc($result))
		{
			$emp_code_db=$rowsaudalog['emp_code'];
			$product_filter_code	=$rowsaudalog['product_filter_code'];
			
			$sqlempbranch="SELECT branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code_db."'";
			$rsempbranch=mysqli_query($link,$sqlempbranch);
			$rowempbranch=mysqli_fetch_assoc($rsempbranch);
			$branch_value=$rowempbranch['branch_code'];
			$branch_value_array=explode(',',$branch_value);
			$branch_value = "'".implode("','", $branch_value_array)."'";
			$condition_branch=' AND PM.branch_code IN ('.$branch_value.')';
			
			$emp_vertical_value=$rowempbranch['vertical_value'];
			$emp_vertical_value_array=explode(',',$emp_vertical_value);
			//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
			$condition_one=" AND (";
			$condition_two='';
			foreach($emp_vertical_value_array as $emp_vertical_values)
			{
				$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PGM.vertical_value) OR";
			}
			$condition_two=substr($condition_two,0,-2);
			$condition_one.=$condition_two.")";

			$sqlquery="SELECT DISTINCT PGM.product_group_code FROM product_group_master PGM,employee_master EM,product_master PM WHERE 
					PM.product_group_code=PGM.product_group_code AND PGM.acedns='Y' AND PM.acedns='Y'  
					AND EM.emp_code='".$emp_code_db."' ".$condition_branch.$condition_one." ORDER BY PGM.product_group_name ASC";
			$rsquery=mysqli_query($link,$sqlquery);
			while($rowquery=mysqli_fetch_assoc($rsquery))
			{
				array_push($product_group_code_array,$rowquery['product_group_code']);
			}

			if(in_array($product_filter_code,$product_group_code_array))
			{
				$qty=round(($rowsaudalog['qty']*${conversion_two.$product_filter_code}),2);
				$qty_ton=round($rowsaudalog['qty'],2);
				$allocation_id=$rowsaudalog['allocation_id'];
				$allocation_date=$rowsaudalog['allocation_date'];
				
					$contents  = (($allocation_id!='')?preg_replace('/[\r\n]+/', '',$allocation_id): ' ')."^";
					$contents  .= (($allocation_date!='')?$allocation_date: ' ')."^";
					$contents  .= (($emp_code_db!='')?$emp_code_db: ' ')."^";
					$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
					$contents  .= (($qty!='')?$qty: 0)."^";
					$contents  .= (($qty_ton!='')?$qty_ton: 0);
					$linecontents  .= $contents."\n";
					
					$countcolumn++;
			}
		}
		$contentsrowcolumn=$countcolumn.'¥'.'6';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sauda-allocation-log-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
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
	header("Content-Disposition: attachment; filename=sauda_allocation_log.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
