<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$sqlquerychk="SELECT product_code FROM sauda_mrp WHERE acedns='Y' AND product_code IN(SELECT prod_code FROM product_master WHERE dns_prod_code IN(SELECT DISTINCT prod_code FROM loose_oilrate_formulation ))";
$resultchk = mysqli_query($link,$sqlquerychk);
$countchk=mysqli_num_rows($resultchk);

if($countchk > 0)
{
$sqlquery="SELECT SM.*,PM.prod_desc,PSGM.product_sub_group_name,PM.dns_prod_code FROM sauda_mrp SM,product_master PM,product_sub_group_master PSGM 
			WHERE SM.product_code=PM.prod_code AND 
			PM.product_sub_group_code=PSGM.product_sub_group_code AND SM.acedns='Y' AND  PM.dns_prod_code IN(SELECT DISTINCT prod_code 
				FROM loose_oilrate_formulation ) ORDER BY PSGM.product_sub_group_name ASC,PM.prod_desc ASC";				
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'6';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowprice = mysqli_fetch_assoc($result))
		{
			 $sqlflashname="SELECT flash_name FROM product_unit_coversion_matrix WHERE prod_code='".$rowprice['dns_prod_code']."' AND acedns='Y'";
			 $rsflashname=mysqli_query($link,$sqlflashname);
			 $rowflashname=mysqli_fetch_assoc($rsflashname);
			 $flashname=$rowflashname['flash_name'];

			$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$rowprice['product_code']."' AND acedns='Y'";
			$rsselindustrialrate=mysqli_query($link,$sqlselindustrialrate);
			$rowselindustrialrate=mysqli_fetch_assoc($rsselindustrialrate);
			$cntselindustrialrate=mysqli_num_rows($rsselindustrialrate);
			if($cntselindustrialrate >0)
			{
				$sale_rate=$rowselindustrialrate['sale_rate'];
			}
			else $sale_rate=$rowprice['sale_rate'];
			
			$sqlselprevrate="SELECT sale_rate FROM sauda_mrp WHERE product_code='".$rowprice['product_code']."' AND acedns='N' 
							ORDER BY create_date DESC LIMIT 0,1";
			$rsselprevrate=mysqli_query($link,$sqlselprevrate);
			$rowselprevrate=mysqli_fetch_assoc($rsselprevrate);
			$current_sale_rate=$rowselprevrate['sale_rate'];
			
			$parent_child=$rowprice['parent_child'];
			
			if($parent_child=='parent')
			{
				$prospected_publish_rate=$current_sale_rate;
			}
			else
			{
				$sqlselindustrialrateprev="SELECT sale_rate FROM industrial_rate WHERE product_code='".$rowprice['product_code']."' AND acedns='N' 
											ORDER BY create_date DESC LIMIT 0,1";
				$rsselindustrialrateprev=mysqli_query($link,$sqlselindustrialrateprev);
				$rowselindustrialrateprev=mysqli_fetch_assoc($rsselindustrialrateprev);
				$prospected_publish_rate=$rowselindustrialrateprev['sale_rate'];
			}
			
			/*$sqlprodsubgroupname="SELECT PM.prod_desc,PSGM.product_sub_group_name FROM product_sub_group_master PSGM,product_master PM 
								WHERE  PSGM.product_sub_group_code=PM.product_sub_group_code AND PM.prod_code='".$rowprice['product_code']."'";
			$rsprodsubgroupname=mysqli_query($link,$sqlprodsubgroupname);
			$rowprodsubgroupname=mysqli_fetch_assoc($rsprodsubgroupname);*/
			$product_sub_group_name=$rowprice['product_sub_group_name'];
			$prod_desc=$rowprice['prod_desc'];					
			
			$contents  = (($rowprice['product_code']!='')?$rowprice['product_code']: ' ')."^";
			$contents  .= (($flashname!='')?$flashname: ' ')."^";
			$contents  .= (($product_sub_group_name!='')?$product_sub_group_name: ' ')."^";
			$contents  .= (($rowprice['mrp_code']!='')?$rowprice['mrp_code']: ' ')."^";
			$contents  .= (($prospected_publish_rate!='')?$prospected_publish_rate: ' ')."^";
			$contents  .= (($sale_rate!='')?$sale_rate: ' ');
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
			$datacontents = '0'.'¥'.'6';
		}
	}
}
else
{
		$sqlquery="SELECT * FROM (SELECT SM.product_code AS group_prod_code,SM.*,PM.prod_desc,PSGM.product_sub_group_name,PM.dns_prod_code FROM sauda_mrp SM,product_master PM,product_sub_group_master PSGM 
			WHERE SM.product_code=PM.prod_code AND 
			PM.product_sub_group_code=PSGM.product_sub_group_code AND  SM.release_date ='0000-00-00 00:00:00' AND 
						PM.dns_prod_code IN(SELECT DISTINCT prod_code 
				FROM loose_oilrate_formulation )  ORDER BY create_date DESC ,PSGM.product_sub_group_name ASC,PM.prod_desc ASC) AS LAT GROUP BY 1";
				
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'6';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowprice = mysqli_fetch_assoc($result))
		{
			 $sqlflashname="SELECT flash_name FROM product_unit_coversion_matrix WHERE prod_code='".$rowprice['dns_prod_code']."' AND acedns='Y'";
			 $rsflashname=mysqli_query($link,$sqlflashname);
			 $rowflashname=mysqli_fetch_assoc($rsflashname);
			 $flashname=$rowflashname['flash_name'];

			//$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$rowprice['product_code']."' AND acedns='Y'";
			$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE release_date ='0000-00-00 00:00:00' AND 
						acedns='N' AND product_code='".$rowprice['product_code']."' ORDER BY download_time DESC LIMIT 0,1";
			$rsselindustrialrate=mysqli_query($link,$sqlselindustrialrate);
			$rowselindustrialrate=mysqli_fetch_assoc($rsselindustrialrate);
			$cntselindustrialrate=mysqli_num_rows($rsselindustrialrate);
			if($cntselindustrialrate >0)
			{
				$sale_rate=$rowselindustrialrate['sale_rate'];
			}
			else $sale_rate=$rowprice['sale_rate'];
			
			$sqlselprevrate="SELECT sale_rate FROM sauda_mrp WHERE product_code='".$rowprice['product_code']."' AND acedns='N' 
							ORDER BY create_date DESC LIMIT 0,1";
			$rsselprevrate=mysqli_query($link,$sqlselprevrate);
			$rowselprevrate=mysqli_fetch_assoc($rsselprevrate);
			$current_sale_rate=$rowselprevrate['sale_rate'];
			
			$parent_child=$rowprice['parent_child'];
			
			if($parent_child=='parent')
			{
				$prospected_publish_rate=$current_sale_rate;
			}
			else
			{
				$sqlselindustrialrateprev="SELECT sale_rate FROM industrial_rate WHERE product_code='".$rowprice['product_code']."' AND acedns='N' 
											ORDER BY create_date DESC LIMIT 0,1";
				$rsselindustrialrateprev=mysqli_query($link,$sqlselindustrialrateprev);
				$rowselindustrialrateprev=mysqli_fetch_assoc($rsselindustrialrateprev);
				$prospected_publish_rate=$rowselindustrialrateprev['sale_rate'];
			}
			
			/*$sqlprodsubgroupname="SELECT PM.prod_desc,PSGM.product_sub_group_name FROM product_sub_group_master PSGM,product_master PM 
								WHERE  PSGM.product_sub_group_code=PM.product_sub_group_code AND PM.prod_code='".$rowprice['product_code']."'";
			$rsprodsubgroupname=mysqli_query($link,$sqlprodsubgroupname);
			$rowprodsubgroupname=mysqli_fetch_assoc($rsprodsubgroupname);*/
			$product_sub_group_name=$rowprice['product_sub_group_name'];
			$prod_desc=$rowprice['prod_desc'];					
			
			$contents  = (($rowprice['product_code']!='')?$rowprice['product_code']: ' ')."^";
			$contents  .= (($flashname!='')?$flashname: ' ')."^";
			$contents  .= (($product_sub_group_name!='')?$product_sub_group_name: ' ')."^";
			$contents  .= (($rowprice['mrp_code']!='')?$rowprice['mrp_code']: ' ')."^";
			$contents  .= (($prospected_publish_rate!='')?$prospected_publish_rate: ' ')."^";
			$contents  .= (($sale_rate!='')?$sale_rate: ' ');
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
			$datacontents = '0'.'¥'.'6';
		}
	}
				
}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/generate-publish-rate-download.php?nick_name=$nick_name&emp_code=$emp_code";
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
	header("Content-Disposition: attachment; filename=generate_publish.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
