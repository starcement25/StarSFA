<?php
ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(vertical_fields=='yes'){
	/*$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND PM.vertical_value IN ('.$emp_vertical_value.')';*/
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$condition_one=" AND (";
	$condition_two='';
	foreach($emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PM.vertical_value) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";
}
else
{
	$condition_one="";
}
/*else if(vertical_fields=='yes' && vertical_branch_relation=='yes'){
	$sqlempvertical="SELECT vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_branch_code=$rowempvertical['branch_code'];
	$condition_one=" AND PM.vertical_value IN (".$emp_vertical_value.") AND PM.branch_code='".$emp_branch_code."'";
}*/

if($incremental_download=='no')
{
		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1)
{
  /*$sqlquery="SELECT DISTINCT PM.*
			FROM product_master PM,employee_master EM
			WHERE EM.branch_code = PM.branch_code AND PM.prod_desc <>''  
			AND EM.emp_code ='".$emp_code."' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";*/
	if(branch_wise_product=='yes')
	{
		if(strtoupper($nick_name)=='STAR')
		{
			$employee_hierarchy=return_employee_hierarchy($emp_code);
			$sqlbranchlist="SELECT GROUP_CONCAT(DISTINCT CM.branch_code SEPARATOR ',') AS branch_code_val FROM customer_master CM,
							customer_route_emp_relation CRR WHERE  CM.customer_code=CRR.customer_code AND CRR.acedns='Y' AND CRR.emp_code IN(".$employee_hierarchy.")";
			$rsbranchlist=mysqli_query($link,$sqlbranchlist);
			$rowbranchlist=mysqli_fetch_assoc($rsbranchlist);
			$branch_value=$rowbranchlist['branch_code_val'];				
			$sqlquery="SELECT DISTINCT PM.*
					FROM product_master PM WHERE PM.prod_desc <>'' AND FIND_IN_SET( PM.branch_code,'".$branch_value."') ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
		}
		else
		{
		
		$sqlempbranch="SELECT branch_code,state FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempbranch=mysqli_query($link,$sqlempbranch);
		$rowempbranch=mysqli_fetch_assoc($rsempbranch);
		$branch_value=$rowempbranch['branch_code'];
		$state=$rowempbranch['state'];
		if(state_branch_wise_TD=='yes')
		{
			$sqlstatecode="SELECT state_code FROM state_master WHERE state='".$state."'";
			$rsstatecode=mysqli_query($link,$sqlstatecode);
			$rowstatecode=mysqli_fetch_assoc($rsstatecode);
			$state_code=$rowstatecode['state_code'];
		}
		$branch_value_array=explode(',',$branch_value);
		$branch_value = "'".implode("','", $branch_value_array)."'";
		$condition_branch=' AND PM.branch_code IN ('.$branch_value.')';
		
		$sqlquery="SELECT DISTINCT PM.*
					FROM product_master PM,employee_master EM
					WHERE PM.prod_desc <>''  
					AND EM.emp_code ='".$emp_code."' ".$condition_branch.$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
		}
	}
	else
	{
		$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
	}
}
else
{
	$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
}
			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'28';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			if(state_branch_wise_TD=='yes')
			{
				$sqlselTD="SELECT TD FROM state_branch_product_wise_TD WHERE state_code='".$state_code."' AND 
							branch_code='".$rowproduct['branch_code']."' AND product_code='".$rowproduct['prod_code']."' 
							ORDER BY download_time DESC LIMIT 0,1";
				$rsselTD=mysqli_query($link,$sqlselTD);
				$rowselTD=mysqli_fetch_assoc($rsselTD);
				$TD=$rowselTD['TD'];
			}
			else
			{
				$TD=$rowproduct['TD'];
			}
			
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_code']!='')?$rowproduct['product_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_name']!='')?$rowproduct['product_group_name']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_code']!='')?$rowproduct['product_sub_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_name']!='')?$rowproduct['product_sub_group_name']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_code']!='')?$rowproduct['product_brand_code']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_name']!='')?$rowproduct['product_brand_name']: ' ')."^";
			$contents  .= (($rowproduct['prod_desc']!='')?$rowproduct['prod_desc']: ' ')."^";
			$contents  .= (($rowproduct['black_list']!='')?$rowproduct['black_list']: ' ')."^";
			$contents  .= (($rowproduct['acedns']!='')?$rowproduct['acedns']: ' ')."^";
			$contents  .= (($rowproduct['UOM1']!='')?$rowproduct['UOM1']: ' ')."^";
			$contents  .= (($rowproduct['UOM2']!='')?$rowproduct['UOM2']: ' ')."^";
			$contents  .= (($rowproduct['conversion_factor']!='')?$rowproduct['conversion_factor']: ' ')."^";
			$contents  .= (($rowproduct['pack_size']!='')?$rowproduct['pack_size']: ' ')."^";
			$contents  .= (($rowproduct['UOM3']!='')?$rowproduct['UOM3']: ' ')."^";
			$contents  .= (($rowproduct['conversion_factor_two']!='')?$rowproduct['conversion_factor_two']: ' ')."^";
			$contents  .= (($TD!='')?$TD: ' ')."^";
			$contents  .= (($rowproduct['branch_code']!='')?$rowproduct['branch_code']: ' ')."^";
			$contents  .= (($rowproduct['vertical_value']!='')?$rowproduct['vertical_value']: ' ')."^";
			$contents  .= (($rowproduct['secondary_unit']!='')?$rowproduct['secondary_unit']: ' ')."^";
			$contents  .= (($rowproduct['dns_prod_code']!='')?$rowproduct['dns_prod_code']: ' ')."^";
			$contents  .= (($rowproduct['focus']!='')?$rowproduct['focus']: ' ')."^";
			$contents  .= (($rowproduct['weightage']!='')?$rowproduct['weightage']: ' ')."^";
			$contents  .= (($rowproduct['vat']!='')?$rowproduct['vat']: ' ')."^";
			$contents  .= (($rowproduct['addl_vat']!='')?$rowproduct['addl_vat']: ' ')."^";
			$contents  .= (($rowproduct['freight_cost']!='')?$rowproduct['freight_cost']: ' ')."^";
			$contents  .= (($rowproduct['pack_unit']!='')?$rowproduct['pack_unit']: ' ')."^";
			$contents  .= (($rowproduct['prod_size']!='')?$rowproduct['prod_size']: ' ');

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
			$datacontents = '0'.'¥'.'28';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/product-master-txt-incremental-7.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-master-txt-incremental-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=product_master.txt");
	print "$datacontents";	
	mysqli_close($link);
?>
