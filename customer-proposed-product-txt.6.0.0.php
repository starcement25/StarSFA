<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('�',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('�',' ',$data_download_time);

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	//$emp_hierarchy_condition='(SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
}
else
{
	$employee_hierarchy="'".$emp_code."'";
}

if($incremental_download=='no')
{
		$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

/*$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);*/
 
					   
$sqlquery="SELECT POCM.customer_code,CM.rds_tag,GROUP_CONCAT(DISTINCT product_code SEPARATOR ',') AS concated_product FROM prev_order_counting_master POCM,customer_master CM WHERE CM.customer_code=POCM.customer_code AND (SUBSTRING(POCM.order_no,2,5) IN(".$employee_hierarchy."))
			  AND POCM.status='pending' AND POCM.order_no LIKE 'O%' GROUP BY POCM.customer_code ORDER BY POCM.customer_code ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
$countdis=0;
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'�'.$hour.':'.$minute.':'.$second."\n";
		
		$todaydate =$year.'-'.$month.'-'.$date;
		$dayscount='90';
		$numericprevdate=date('Y-m-d', strtotime("-$dayscount days,$todaydate "));
		$date_condition=" AND SUBSTRING(visit_date,1,10) >='".$numericprevdate."' 
					   AND SUBSTRING(visit_date,1,10) <='".$todaydate."'";

		while($rowquery = mysqli_fetch_assoc($result))
		{
			$customer_code=$rowquery['customer_code'];
			$rds_tag=$rowquery['rds_tag'];
			$concated_product=$rowquery['concated_product'];
			$concated_product_array=explode(",",$concated_product);
			$concated_product_string='';
			foreach($concated_product_array as  $concated_product_val)
			{
				$concated_product_string=$concated_product_string."'".$concated_product_val."'".',';
			}
			$concated_product_string=substr($concated_product_string,0,-1);
			
			$sqlproposedsku="SELECT * FROM(SELECT product_code,SUM(visit_qty) As total_qty FROM prev_order_counting_master WHERE 
							customer_code!='".$customer_code."' AND customer_code 
							IN(SELECT customer_code FROM customer_master WHERE acedns='Y' AND rds_tag='".$rds_tag."') AND 
							product_code NOT IN(".$concated_product_string.") AND order_no LIKE 'O%' $date_condition GROUP BY product_code) AS SAT ORDER BY 2 DESC";
			$rsproposedsku=mysqli_query($link,$sqlproposedsku);
			while($rowproposedsku=mysqli_fetch_assoc($rsproposedsku))
			{				
				${product_code.$customer_code}=$rowproposedsku['product_code'];
				${total_qty.$customer_code}=$rowproposedsku['total_qty'];
			//$d_instruction=$rowsorderstatus['d_instruction'];
			//$rate=$rowsorderstatus['rate'];
			//$amount=$rowsorderstatus['amount'];
			//$remarks='';
			$flag='1';
			
			$contents  = (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= ((${product_code.$customer_code}!='')?${product_code.$customer_code}: ' ')."^";
			$contents  .= ((${total_qty.$customer_code}!='')?${total_qty.$customer_code}: ' ');
			$linecontents  .= $contents."\n";
			$countdis++;
			}
		}
		$contentsrowcolumn=$countdis.'�'.'3';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'�'.'0';
		}
		else
		{
			$datacontents = '0'.'�'.'3';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-proposed-product-txt.6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/prev-stock-counting-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_proposed_product.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
