<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$customer_code=$_REQUEST['customer_code'];
$product_details=$_REQUEST['product_details'];
$product_details_parts=explode("$",$product_details);
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$countdata=0;
foreach ($product_details_parts as $product_details_parts_val)
{
	//echo $product_details_parts_val;
	//exit();
	$product_details_parts_semi_val=explode("-",$product_details_parts_val);
	$product_details_sku_code=$product_details_parts_semi_val[0];
	$product_details_DO_qty=$product_details_parts_semi_val[1];
	$product_details_mapped_sku_code=$product_details_parts_semi_val[2];
	
	$sqlmappedprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$product_details_mapped_sku_code."'";
	$rsmappedprodcode=mysqli_query($link,$sqlmappedprodcode);
	$rowmappedprodcode=mysqli_fetch_assoc($rsmappedprodcode);
	$mapped_product_internal_code=$rowmappedprodcode['prod_code'];
    $sqlquery="SELECT sauda_no,qty,sale_rate,freight_charge,sku_code,additional_TD,additional_premium FROM DO_master WHERE status='no' 
				AND is_approved='yes' AND customer_code='".$customer_code."' AND sku_code='".$mapped_product_internal_code."' 
				 ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$Do_qty_distribute=0;
		${allocation.$product_details_sku_code}=0;
		while($rowDOfifo = mysqli_fetch_assoc($result))
		{
			$sauda_no=$rowDOfifo['sauda_no'];
			$addiional_TD=$rowDOfifo['additional_TD'];
			$addiional_premium=$rowDOfifo['additional_premium'];
			$sqlbargainqty="SELECT qty,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$product_details_mapped_sku_code."'";
			$rsbargainqty=mysqli_query($link,$sqlbargainqty);
			$total_DO_qty=0;
			while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))
			{
				$child_sku_code=$rowbargainqty['sku_code'];
				$sqltotalDOqty="SELECT SUM(DO_qty) AS DO_qty FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
				$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);
				$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);
				//echo '<br />';
				
				$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty'];
			}
			$bargain_qty=$rowDOfifo['qty'];
			$pending_qty=$bargain_qty-$total_DO_qty;
			if($pending_qty >0)
			{
			$bargain_qty=$pending_qty;
			$sqlchkDOtemp="SELECT SUM(DO_qty) AS total_DO_qty_temp FROM DO_temp WHERE customer_code='".$customer_code."' AND sauda_no='".$sauda_no."' 
					AND sku_code	='".$product_details_mapped_sku_code."'";
			$rschkDOtemp=mysqli_query($link,$sqlchkDOtemp);
			$rowchkDOtemp=mysqli_fetch_assoc($rschkDOtemp);	
			$total_DO_qty_temp=$rowchkDOtemp['total_DO_qty_temp'];
			$pending_qty_temp=$bargain_qty-$total_DO_qty_temp;
			if($pending_qty_temp >0)
			{
			$bargain_qty=$pending_qty_temp;		
			$bargain_rate=$rowDOfifo['sale_rate'];
			$freight_charge=$rowDOfifo['freight_charge'];
			
			$sqlTD="SELECT TD from sauda_details WHERE sauda_no='".$sauda_no."' AND sku_code='".$rowDOfifo['sku_code']."'";
			$rsTD=mysqli_query($link,$sqlTD);
			$rowTD=mysqli_fetch_assoc($rsTD);
			$TD=$rowTD['TD'];
			if($TD=='') $TD=0;
			$bargain_rate=($bargain_rate+$freight_charge+$TD+$addiional_premium)-$addiional_TD;
			
			if($product_details_sku_code!=$mapped_product_internal_code)
			{
				 $sqlquerychildrate="SELECT sale_rate,freight_charge,additional_TD,additional_premium FROM DO_master WHERE status='no' 
				AND is_approved='yes' AND customer_code='".$customer_code."' AND sku_code='".$product_details_sku_code."' and sauda_no='".$sauda_no."'";
				$rschildrate=mysqli_query($link,$sqlquerychildrate);
				$rowchildrate=mysqli_fetch_assoc($rschildrate);
				$sale_rate_child=($rowchildrate['sale_rate']+$rowchildrate['freight_charge']+$rowchildrate['additional_premium']+$TD)-$rowchildrate['additional_TD'];
				$bargain_rate=$sale_rate_child;
			}
			
			$sqlprodesc="SELECT dns_prod_code,prod_desc,vat,prod_code FROM product_master WHERE prod_code='".$product_details_sku_code."'";
			$rsprodesc=mysqli_query($link,$sqlprodesc);
			$rowprodesc=mysqli_fetch_assoc($rsprodesc);
			$dns_prod_code=$rowprodesc['dns_prod_code'];
			$prod_desc=$rowprodesc['prod_desc'];
			$vat=$rowprodesc['vat'];
			$prod_code_db=$rowprodesc['prod_code'];
			$contents='';
			
			if(${allocation.$product_details_sku_code}!=$product_details_DO_qty)
			{
				/*if($Do_qty_distribute=='0' ){
					$contents  .= (($sauda_no!='')?$sauda_no: ' ')."^";
					$contents  .= (($prod_code_db!='')?$prod_code_db: ' ')."^";
					$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
					$contents  .= (($vat!='')?$vat: ' ')."^";
					$contents  .= (($product_details_DO_qty!='')?$product_details_DO_qty: ' ')."^";
					$contents  .= (($bargain_rate!='')?$bargain_rate: ' ');
					$linecontents  .= $contents."\n";
					$Do_qty_distribute++;
					$countdata++;
				}*/
				if($product_details_DO_qty <=$bargain_qty && ${allocation.$product_details_sku_code}==0)
				{
					${allocation.$product_details_sku_code}=${allocation.$product_details_sku_code}+$product_details_DO_qty;
					$contents  .= (($sauda_no!='')?$sauda_no: ' ')."^";
					$contents  .= (($prod_code_db!='')?$prod_code_db: ' ')."^";
					$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
					$contents  .= (($vat!='')?$vat: ' ')."^";
					$contents  .= (($product_details_DO_qty!='')?$product_details_DO_qty: ' ')."^";
					$contents  .= (($bargain_rate!='')?$bargain_rate: ' ');
					$linecontents  .= $contents."\n";
					$countdata++;
					$sqlinstemp="INSERT INTO DO_temp SET customer_code='".$customer_code."',sauda_no='".$sauda_no."',
								sku_code='".$product_details_mapped_sku_code."',DO_qty='".$product_details_DO_qty."'";
					mysqli_query($link,$sqlinstemp);			
				}
				else
				{
					if(${allocation.$product_details_sku_code}==0){
					$contents  .= (($sauda_no!='')?$sauda_no: ' ')."^";
					$contents  .= (($prod_code_db!='')?$prod_code_db: ' ')."^";
					$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
					$contents  .= (($vat!='')?$vat: ' ')."^";
					$contents  .= (($bargain_qty!='')?$bargain_qty: ' ')."^";
					$contents  .= (($bargain_rate!='')?$bargain_rate: ' ');
					$linecontents  .= $contents."\n";
					${allocation.$product_details_sku_code}=${allocation.$product_details_sku_code}+$bargain_qty;
					$countdata++;
					$sqlinstemp="INSERT INTO DO_temp SET customer_code='".$customer_code."',sauda_no='".$sauda_no."',
								sku_code='".$product_details_mapped_sku_code."',DO_qty='".$bargain_qty."'";
					mysqli_query($link,$sqlinstemp);	
					}
					else
					{
						$contents  .= (($sauda_no!='')?$sauda_no: ' ')."^";
						$contents  .= (($prod_code_db!='')?$prod_code_db: ' ')."^";
						$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
						$contents  .= (($vat!='')?$vat: ' ')."^";
						$contents  .= ((($product_details_DO_qty-${allocation.$product_details_sku_code}) >= $bargain_qty )?$bargain_qty: ($product_details_DO_qty-${allocation.$product_details_sku_code}))."^";
						$contents  .= (($bargain_rate!='')?$bargain_rate: ' ');
						$linecontents  .= $contents."\n";
						if(($product_details_DO_qty-${allocation.$product_details_sku_code}) >= $bargain_qty)
						{
													$qty_temp_insert=$bargain_qty;

						${allocation.$product_details_sku_code}=${allocation.$product_details_sku_code}+$bargain_qty;
						}
						else
						{
							$qty_temp_insert=($product_details_DO_qty-${allocation.$product_details_sku_code});
							${allocation.$product_details_sku_code}=${allocation.$product_details_sku_code}+($product_details_DO_qty-${allocation.$product_details_sku_code});
							
						}
						$countdata++;
						$sqlinstemp="INSERT INTO DO_temp SET customer_code='".$customer_code."',sauda_no='".$sauda_no."',
								sku_code='".$product_details_mapped_sku_code."',DO_qty='".$qty_temp_insert."'";
						mysqli_query($link,$sqlinstemp);	
					}
				}
			}
		  }
		}
	 }
  }
  			//exit();

  foreach ($product_details_parts as $product_details_parts_val)
	{
	//echo $product_details_parts_val;
	//exit();
	$product_details_parts_semi_val=explode("-",$product_details_parts_val);
	$product_details_sku_code=$product_details_parts_semi_val[0];
	$product_details_DO_qty=$product_details_parts_semi_val[1];
	$product_details_mapped_sku_code=$product_details_parts_semi_val[2];
	$sqldeltemp="DELETE FROM DO_temp WHERE  customer_code='".$customer_code."' AND sku_code='".$product_details_mapped_sku_code."'"; //Need to include sauda no later
	mysqli_query($link,$sqldeltemp);
	}
	$contentsrowcolumn=$countdata.'¥'.'6';
	$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	

	if($countdata==0)
	{
		$datacontents = '0'.'¥'.'0';
	}
		
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/DO-transaction-display-fifowise-6.0.0-test.php?nick_name=$nick_name&emp_code=$emp_code&customer_code=$customer_code&product_details=$product_details";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=DO_fifowise.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
