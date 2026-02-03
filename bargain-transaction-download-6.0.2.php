<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");

require("include/config-setup.php");

require("include/dbcon.php");

require("include/functions.php");



$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];

$last_update_time=str_replace('€',' ',$last_update_time);

$incremental_download=$_REQUEST['incremental_download'];

$data_download_time=$_REQUEST['data_download_time'];

$data_download_time=str_replace('€',' ',$data_download_time);



$employee_hierarchy=return_employee_hierarchy($emp_code);

$emp_hierarchy_condition=' AND CRR.emp_code IN('.$employee_hierarchy.')';



if($incremental_download=='no')

{

	$login_condition="";

	$login_condition_one="";

}

else

{

	$login_condition=" AND UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";

}
if(strtoupper(substr($emp_code,0,1))=='E')
{
    $sqlquery="SELECT DISTINCT DM.sauda_no,DM.customer_code,DM.branch_code,DM.sku_code,DM.mapped_sku_code,SUM(DM.qty) as qty,

				(DM.sale_rate+DM.freight_charge) AS sale_rate,SUM(DM.amount) as amount,DM.status,

				DM.incoterms,DM.dns_sauda_no,DM.freight_charge FROM DO_master DM,product_master PM,

				product_sub_group_master PSM

				WHERE DM.status='no' AND DM.is_approved='yes' AND DM.sku_code=PM.prod_code 

				AND PM.product_sub_group_code=PSM.product_sub_group_code

				AND DM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.")) 

				GROUP BY DM.customer_code,DM.sku_code ORDER BY PSM.product_sub_group_name DESC,DM.mapped_sku_code ASC,DM.qty DESC";
}
if(strtoupper(substr($emp_code,0,1))=='C')
{
    $sqlquery="SELECT DISTINCT DM.sauda_no,DM.customer_code,DM.branch_code,DM.sku_code,DM.mapped_sku_code,SUM(DM.qty) as qty,

				(DM.sale_rate+DM.freight_charge) AS sale_rate,SUM(DM.amount) as amount,DM.status,

				DM.incoterms,DM.dns_sauda_no,DM.freight_charge FROM DO_master DM,product_master PM,

				product_sub_group_master PSM

				WHERE DM.status='no' AND DM.is_approved='yes' AND DM.sku_code=PM.prod_code 

				AND PM.product_sub_group_code=PSM.product_sub_group_code

				AND DM.customer_code='".$emp_code."'

				GROUP BY DM.customer_code,DM.sku_code ORDER BY PSM.product_sub_group_name DESC,DM.mapped_sku_code ASC,DM.qty DESC";
}
if(strtoupper(substr($emp_code,0,1))=='B')
{
    $sqlquery="SELECT DISTINCT DM.sauda_no,DM.customer_code,DM.branch_code,DM.sku_code,DM.mapped_sku_code,SUM(DM.qty) as qty,

				(DM.sale_rate+DM.freight_charge) AS sale_rate,SUM(DM.amount) as amount,DM.status,

				DM.incoterms,DM.dns_sauda_no,DM.freight_charge FROM DO_master DM,product_master PM,

				product_sub_group_master PSM

				WHERE DM.status='no' AND DM.is_approved='yes' AND DM.sku_code=PM.prod_code 

				AND PM.product_sub_group_code=PSM.product_sub_group_code

				AND DM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.")) 

				GROUP BY DM.customer_code,DM.sku_code ORDER BY PSM.product_sub_group_name DESC,DM.mapped_sku_code ASC,DM.qty DESC";
}

	$result = mysqli_query($link,$sqlquery);

	$count=mysqli_num_rows($result);

	if($count>0){

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowDOtrans = mysqli_fetch_assoc($result))

		{

			$sauda_no=$rowDOtrans['sauda_no'];

			$sku_code=$rowDOtrans['sku_code'];

			$mapped_sku_code=$rowDOtrans['mapped_sku_code'];

			$customer_code=$rowDOtrans['customer_code'];

			

			$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$mapped_sku_code."'";

			$rsrodcode=mysqli_query($link,$sqlprodcode);

			$rowprodcode=mysqli_fetch_assoc($rsrodcode);

			$prod_code_mapped=$rowprodcode['prod_code'];

			if($prod_code_mapped==$sku_code)

			{

				/*$sqlbargainqty="SELECT SUM(qty,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";*/

				$sqlbargainqty="SELECT sku_code,sauda_no FROM DO_master WHERE  customer_code='".$customer_code."' AND 

								mapped_sku_code	='".$mapped_sku_code."' AND status='no' AND is_approved='yes'";

				$rsbargainqty=mysqli_query($link,$sqlbargainqty);

				$bargain_qty_total=$rowDOtrans['qty'];

				$total_DO_qty=0;

				while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))

				{

					$child_sku_code=$rowbargainqty['sku_code'];

					$DO_fetched_bargain_no=$rowbargainqty['sauda_no'];

					//$bargain_qty_total=$bargain_qty_total+$rowbargainqty['qty'];

					$sqltotalDOqty="SELECT SUM(DO_qty) As  DO_qty FROM DO_transaction WHERE sauda_no='".$DO_fetched_bargain_no."' 

									AND sku_code='".$child_sku_code."'";

					$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);

					$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);

					$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty'];

				}

				$available_DO_qty=$bargain_qty_total-$total_DO_qty;

			}

			else

			{

				$available_DO_qty=0;

			}

			$contents  = (($rowDOtrans['sauda_no']!='')?$rowDOtrans['sauda_no']: ' ')."^";

			$contents  .= (($rowDOtrans['customer_code']!='')?$rowDOtrans['customer_code']: ' ')."^";


			$contents  .= (($rowDOtrans['branch_code']!='')?$rowDOtrans['branch_code']: ' ')."^";

			$contents  .= (($rowDOtrans['sku_code']!='')?$rowDOtrans['sku_code']: ' ')."^";

			$contents  .= (($available_DO_qty!='')?$available_DO_qty: ' ')."^";

			$contents  .= (($rowDOtrans['sale_rate']!='')?round($rowDOtrans['sale_rate'],2): ' ')."^";

			$contents  .= (($rowDOtrans['amount']!='')?round($rowDOtrans['amount'],2): ' ')."^";

			$contents  .= (($rowDOtrans['status']!='')?$rowDOtrans['status']: ' ')."^";

			$contents  .= (($rowDOtrans['incoterms']!='')?$rowDOtrans['incoterms']: ' ')."^";

			$contents  .= (($rowDOtrans['mapped_sku_code']!='')?$rowDOtrans['mapped_sku_code']: ' ')."^";

			$contents  .= (($rowDOtrans['dns_sauda_no']!='')?$rowDOtrans['dns_sauda_no']: ' ')."^";

			$contents  .= (($rowDOtrans['freight_charge']!='')?$rowDOtrans['freight_charge']: ' ');

			$linecontents  .= $contents."\n";

		}

		$contentsrowcolumn=$count.'¥'.'12';

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

			$datacontents = '0'.'¥'.'12';

		}

	}

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));

	$url = APICALLLOGURL."/bargain-transaction-download-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";

	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 

	header("Content-Disposition: attachment; filename=bargain_transaction.txt");

	print "$datacontents"; 

	mysqli_close($link);		

?>

