<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlquery="SELECT * FROM product_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowsproductdetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<product_id><![CDATA['.mb_convert_encoding($rowsproductdetails['product_id'], 'UTF-8', 'UTF-8').']]></product_id>
							<user_id><![CDATA['.mb_convert_encoding($rowsproductdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<no_of_filter><![CDATA['.mb_convert_encoding($rowsproductdetails['no_of_filter'], 'UTF-8', 'UTF-8').']]></no_of_filter>
							<col1><![CDATA['.mb_convert_encoding($rowsproductdetails['col1'], 'UTF-8', 'UTF-8').']]></col1>
							<col2><![CDATA['.mb_convert_encoding($rowsproductdetails['col2'], 'UTF-8', 'UTF-8').']]></col2>
							<col3><![CDATA['.mb_convert_encoding($rowsproductdetails['col3'], 'UTF-8', 'UTF-8').']]></col3>
							<col4><![CDATA['.mb_convert_encoding($rowsproductdetails['col4'], 'UTF-8', 'UTF-8').']]></col4>
							<uom_wise_mrp><![CDATA['.mb_convert_encoding($rowsproductdetails['uom_wise_mrp'], 'UTF-8', 'UTF-8').']]></uom_wise_mrp>
							<branch_wise_mrp><![CDATA['.mb_convert_encoding($rowsproductdetails['branch_wise_mrp'], 'UTF-8', 'UTF-8').']]></branch_wise_mrp>
							<sauda_allocation_basedon_filter><![CDATA['.mb_convert_encoding($rowsproductdetails['sauda_allocation_basedon_filter'], 'UTF-8', 'UTF-8').']]></sauda_allocation_basedon_filter>
							<product_in_business_prospect><![CDATA['.mb_convert_encoding($rowsproductdetails['product_in_business_prospect'], 'UTF-8', 'UTF-8').']]></product_in_business_prospect>
							<branch_wise_product><![CDATA['.mb_convert_encoding($rowsproductdetails['branch_wise_product'], 'UTF-8', 'UTF-8').']]></branch_wise_product>
							<secondary_unit><![CDATA['.mb_convert_encoding($rowsproductdetails['secondary_unit'], 'UTF-8', 'UTF-8').']]></secondary_unit>
							<destination_price_list><![CDATA['.mb_convert_encoding($rowsproductdetails['destination_price_list'], 'UTF-8', 'UTF-8').']]></destination_price_list>
							<destination_ordertype_price_list><![CDATA['.mb_convert_encoding($rowsproductdetails['destination_ordertype_price_list'], 'UTF-8', 'UTF-8').']]></destination_ordertype_price_list>
							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/setup-product-details-incremental-6.0.4.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/setup-product-details-incremental-6.0.0.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving //Users position
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
			
	echo $contents;	
	mysqli_close($link);	
?>
