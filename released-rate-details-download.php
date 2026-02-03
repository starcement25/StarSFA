<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
/*$sqlquery="SELECT * FROM sauda_mrp WHERE acedns='Y' AND product_code IN(SELECT prod_code FROM product_master WHERE dns_prod_code IN(SELECT DISTINCT prod_code 
				FROM loose_oilrate_formulation ))";*/
$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
$dateprevious=date('Y-m-d', strtotime("-15 days,$curdateserver "));				
$sqlquery="SELECT SM.release_date,PM.prod_desc,PSGM.product_sub_group_name,PM.prod_code,PM.dns_prod_code,SM.sale_rate FROM sauda_mrp SM,product_master PM,product_sub_group_master PSGM 
			WHERE SM.product_code=PM.prod_code AND 
			PM.product_sub_group_code=PSGM.product_sub_group_code AND SM.release_date!='0000-00-00 00:00:00' 
			AND SUBSTRING(SM.release_date,1,10) >='".$dateprevious."' ORDER BY SUBSTRING(SM.release_date,1,19) DESC,PSGM.product_sub_group_name ASC,PM.prod_desc ASC,SM.parent_child ASC";				
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$countrelease=0;
	$contentsrowcolumn  =$count.'¥'.'5';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$productreleasedatearr=array();
		while($rowprice = mysqli_fetch_assoc($result))
		{
			 $sqlflashname="SELECT flash_name FROM product_unit_coversion_matrix WHERE prod_code='".$rowprice['dns_prod_code']."' AND acedns='Y'";
			 $rsflashname=mysqli_query($link,$sqlflashname);
			 $rowflashname=mysqli_fetch_assoc($rsflashname);
			 $flashname=$rowflashname['flash_name'];

			$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$rowprice['product_code']."' AND 
									released_date='".$rowprice['releaed_date']."'";
			$rsselindustrialrate=mysqli_query($link,$sqlselindustrialrate);
			$rowselindustrialrate=mysqli_fetch_assoc($rsselindustrialrate);
			$cntselindustrialrate=mysqli_num_rows($rsselindustrialrate);
			if($cntselindustrialrate >0)
			{
				$released_rate=$rowselindustrialrate['sale_rate'];
			}
			else $sale_rate=$rowprice['sale_rate'];
			
			/*$sqlprodsubgroupname="SELECT PM.prod_desc,PSGM.product_sub_group_name FROM product_sub_group_master PSGM,product_master PM 
								WHERE  PSGM.product_sub_group_code=PM.product_sub_group_code AND PM.prod_code='".$rowprice['product_code']."'";
			$rsprodsubgroupname=mysqli_query($link,$sqlprodsubgroupname);
			$rowprodsubgroupname=mysqli_fetch_assoc($rsprodsubgroupname);*/
			$product_sub_group_name=$rowprice['product_sub_group_name'];
			$prod_desc=$rowprice['prod_desc'];
			$prod_code=$rowprice['prod_code'];
			$release_date=$rowprice['release_date'];
			$prodreleasedatestr=$prod_code.$release_date;
			
			if(!in_array($prodreleasedatestr,$productreleasedatearr))
			{					
				$contents  = (($product_sub_group_name!='')?$product_sub_group_name: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($flashname!='')?$flashname: ' ')."^";
				$contents  .= (($release_date!='')?$release_date: ' ')."^";
				$contents  .= (($sale_rate!='')?$sale_rate: ' ');
				$linecontents  .= $contents."\n";
				array_push($productreleasedatearr,$prodreleasedatestr);
				$countrelease++;
			}
		}
		$contentsrowcolumn  =$countrelease.'¥'.'5';
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
			$datacontents = '0'.'¥'.'5';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/released-rate-details-download.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/mrp-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=released_rate_details.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
