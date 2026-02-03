<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND PM.vertical_value IN ('.$emp_vertical_value.')';
}
else
{
	$condition_one="";
}

if($incremental_download=='no')
{
	if($nick_name=='SMOTO')
	{
		$login_condition='';
	}
	else
	{
		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
	}
}
else
{
	$login_condition="AND UNIX_TIMESTAMP(PM.download_time_cl_stk) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlbranches="SELECT * FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

$sqlmrp="SELECT COUNT(mrp_code) AS no_of_mrp FROM mrp WHERE 1";
$rsmrp=mysqli_query($link,$sqlmrp);
$rowmrp=mysqli_fetch_assoc($rsmrp);
$no_of_mrp=$rowmrp['no_of_mrp'];

if($countbranches>1)
{
	if($no_of_mrp>0){
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk
					FROM product_master PM,employee_master EM,mrp MRP
					WHERE EM.branch_code = PM.branch_code AND PM.prod_desc <>'' 
					AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' ".$condition_one." ".$login_condition." 
					ORDER BY PM.prod_desc ASC";
	}
	else
	{
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk
					FROM product_master PM,employee_master EM
					WHERE EM.branch_code = PM.branch_code AND PM.prod_desc <>''  
					AND EM.emp_code ='".$emp_code."' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
	}
}
else
{
	if($no_of_mrp>0){
			
			if($nick_name=='SMOTO')
			{
				$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk FROM product_master PM 
							WHERE PM.is_download='yes' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
			}
			else
			{
				$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk 
					FROM product_master PM,mrp MRP WHERE PM.prod_code=MRP.product_code AND PM.prod_desc <>'' 
					".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
			}
		}	
		else
		{
			$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk FROM product_master PM WHERE PM.prod_desc <>'' 
						 ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
		}	
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<prod_code><![CDATA['.mb_convert_encoding($rowproduct['prod_code'], 'UTF-8', 'UTF-8').']]></prod_code>
						<prod_desc><![CDATA['.mb_convert_encoding($rowproduct['prod_desc'], 'UTF-8', 'UTF-8').']]></prod_desc>
						<cl_stk><![CDATA['.mb_convert_encoding($rowproduct['cl_stk'], 'UTF-8', 'UTF-8').']]></cl_stk>';
			$contents.="</data>";*/
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['cl_stk']!='')?$rowproduct['cl_stk']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/product-cl-stk-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-cl-stk-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=product_cl_stk.txt");
	print "$datacontents";		
	
?>
