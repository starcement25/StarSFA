<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if($incremental_download=='no'){
	$login_condition='';
}
else
{
	$login_condition="AND UNIX_TIMESTAMP(LO.date) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlquery="SELECT OD.sku_code,SUM(OD.qty)
			FROM order_details OD,order_header OH,location LO
			WHERE OD.order_no=OH.order_no AND LO.trans_id=OH.order_no AND OH.transaction_type='CN' 
			AND OH.customer_code='".$emp_code."' ".$login_condition." GROUP BY OD.sku_code";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			//$sku_code_array=array();
			//if(!in_array($rowproduct['sku_code'],$sku_code_array))
			//{
			$contents  = (($rowproduct['sku_code']!='')?$rowproduct['sku_code']: ' ')."^";
			$contents  .= (($rowproduct['cl_stk']!='')?$rowproduct['cl_stk']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'2';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/product-cl-stk-sales-txt-incremental-3.1.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-cl-stk-sales-txt-incremental-3.1.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=cl_stk_sales.txt");
	print "$datacontents";		
	
?>
