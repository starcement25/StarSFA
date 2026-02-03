<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
//$body=str_replace("'",'"',$body);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><DO_TRANSACTION><location><emp_code><![CDATA[E0052]]></emp_code><trans_id><![CDATA[NE005220190520162035]]></trans_id><latt><![CDATA[22.5643463]]></latt><longi><![CDATA[88.3568892]]></longi><date><![CDATA[2019-05-20 16:20:35]]></date></location><DO_DETAILS><SAUDA_NO><![CDATA[FTE004120190802214637]]></SAUDA_NO><CUSTOMER_CODE><![CDATA[C/0000064]]></CUSTOMER_CODE><DESTINATION><![CDATA[RT/28]]></DESTINATION><DO_NO><![CDATA[DOE004120190808100405]]></DO_NO><SKU_CODE><![CDATA[12003]]></SKU_CODE><DO_QTY><![CDATA[5]]></DO_QTY><DO_RATE><![CDATA[1120.95]]></DO_RATE><DO_AMOUNT><![CDATA[1120.95]]></DO_AMOUNT><DO_DATE><![CDATA[2019-08-29 18:10:18]]></ DO_DATE ><PO_NO><![CDATA[]]></ PO_NO ></DO_DETAILS></DO_TRANSACTION></root>";*/

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><DO_TRANSACTION><location><emp_code><![CDATA[E0016]]></emp_code><trans_id><![CDATA[DOE001620191011174625]]></trans_id><latt><![CDATA[22.727305]]></latt><longi><![CDATA[88.4904832]]></longi><date><![CDATA[2019-10-11 17:46:25]]></date></location><DO_DETAILS><SAUDA_NO><![CDATA[FTE001620191011124419]]></SAUDA_NO><customer_code><![CDATA[C/0000119]]></customer_code><DESTINATION><![CDATA[RT/48]]></DESTINATION><DO_NO><![CDATA[DOE001620191011174625]]></DO_NO><SKU_CODE><![CDATA[12100]]></SKU_CODE><DO_QTY><![CDATA[10]]></DO_QTY><DO_RATE><![CDATA[854.25]]></DO_RATE><DO_AMOUNT><![CDATA[8969.62]]></DO_AMOUNT><DO_DATE><![CDATA[2019-10-11 17:46:25]]></DO_DATE><PO_NO><![CDATA[]]></PO_NO></DO_DETAILS></DO_TRANSACTION></root>";*/

$location_emp_code="*ROOT*DO_TRANSACTION*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*DO_TRANSACTION*LOCATION*TRANS_ID";
$location_latt = "*ROOT*DO_TRANSACTION*LOCATION*LATT";
$location_longi = "*ROOT*DO_TRANSACTION*LOCATION*LONGI";
$location_date="*ROOT*DO_TRANSACTION*LOCATION*DATE";

$sauda_no = "*ROOT*DO_TRANSACTION*DO_DETAILS*SAUDA_NO";
$customer_code = "*ROOT*DO_TRANSACTION*DO_DETAILS*CUSTOMER_CODE";
$destination = "*ROOT*DO_TRANSACTION*DO_DETAILS*DESTINATION";
$DO_no ="*ROOT*DO_TRANSACTION*DO_DETAILS*DO_NO";
$sku_code ="*ROOT*DO_TRANSACTION*DO_DETAILS*SKU_CODE";
$DO_qty ="*ROOT*DO_TRANSACTION*DO_DETAILS*DO_QTY";
$DO_rate ="*ROOT*DO_TRANSACTION*DO_DETAILS*DO_RATE";
$DO_amount ="*ROOT*DO_TRANSACTION*DO_DETAILS*DO_AMOUNT";
$DO_date ="*ROOT*DO_TRANSACTION*DO_DETAILS*DO_DATE";
$PO_no ="*ROOT*DO_TRANSACTION*DO_DETAILS*PO_NO";
$delivery_date ="*ROOT*DO_TRANSACTION*DO_DETAILS*DELIVERY_DATE";

$DO_array=array();
$DO_details_array=array();

$counterDO=0;
$counterDOdetails=0;

class xml_DO{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;	
}
class xml_DO_details{
	var $sauda_no,$customer_code,$destination,$DO_no,$sku_code,$DO_qty,$DO_rate,$DO_amount,$DO_date,$PO_no,$delivery_date;
}
function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}
function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}

function contents($parser, $data){
    global $current_tag, $location_emp_code, $location_trans_id,$location_latt,$location_longi,$location_date,$sauda_no,$customer_code,$destination,$DO_no,$sku_code,$DO_qty,$DO_rate,$DO_amount,$DO_date,$PO_no,$delivery_date,$counterDO,$counterDOdetails,$DO_array,$DO_details_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,20)=='*ROOT*DO_TRANSACTION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$DO_array[$counterDO] = new xml_DO();
				$DO_array[$counterDO]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$DO_array[$counterDO]->location_trans_id = $data;
				break;
			case $location_latt:
				$DO_array[$counterDO]->location_latt = $data;
				break;
			case $location_longi:
				$DO_array[$counterDO]->location_longi = $data;
				break;
			case $location_date:
				$DO_array[$counterDO]->location_date = $data;
				$counterDO++;
				break;		
		}
	}
	if(substr($current_tag,0,31)=='*ROOT*DO_TRANSACTION*DO_DETAILS')
		{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';	
			switch($current_tag){
				case $sauda_no:
					$DO_details_array[$counterDOdetails] = new xml_DO_details();
					$DO_details_array[$counterDOdetails]->sauda_no = $data;
					break;
				case $customer_code:
					$DO_details_array[$counterDOdetails]->customer_code = $data;
					break;
				case $destination:
					$DO_details_array[$counterDOdetails]->destination = $data;
					break;
				case $DO_no:
					$DO_details_array[$counterDOdetails]->DO_no = $data;
					break;
				case $sku_code:
					$DO_details_array[$counterDOdetails]->sku_code = $data;
					break;						
				case $DO_qty:
					$DO_details_array[$counterDOdetails]->DO_qty = $data;
					break;
				case $DO_rate:
					$DO_details_array[$counterDOdetails]->DO_rate = $data;
					break;
				case $DO_amount:
					$DO_details_array[$counterDOdetails]->DO_amount = $data;
					break;
				case $DO_date:
					$DO_details_array[$counterDOdetails]->DO_date = $data;
					break;
				case $PO_no:
					$DO_details_array[$counterDOdetails]->PO_no = $data;
					break;
				case $delivery_date:
					$DO_details_array[$counterDOdetails]->delivery_date = $data;
					$counterDOdetails++;
					break;						
		}
	}
}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);

mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -------------------------------------------------------START QUERY FOR DO details-----------------------------------------------------------------------*/
$DO_array_trans_id=array();
$DO_no_array=array();
$sauda_array_trans_id=array();
if(count($DO_array)>0)
{
	for($x=0;$x<count($DO_array);$x++){
		$location_emp_code=$DO_array[$x]->location_emp_code;
		$location_trans_id=$DO_array[$x]->location_trans_id;
		$location_latt=$DO_array[$x]->location_latt;
		$location_longi=$DO_array[$x]->location_longi;
		$location_date=$DO_array[$x]->location_date;
		
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check DO location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for DO
		if($countchkorlocation>0)
		{
			//$sauda_trans_id_chk=substr($sauda_trans_id,1,19);
			if(!in_array($DO_trans_id,$DO_array_trans_id))
			{
				array_push($DO_array_trans_id,$DO_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update sauda location: ".$sqlupdateorlocation);
			if($rsupdateorlocation)
			{
				$flag=6;
			}
			else
			{
				echo $flag=0;
			}
		}
		else
		{
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_server=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding sauda
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
									trans_id='".$location_trans_id."',
									latt='".$location_latt."',
									longi='".$location_longi."',
									date='".$location_date."',
									updatetime='".$location_date_server."'"; 
			if(mysqli_query($link,$sqlinsertorlocation))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}	
		}
		  $sqlmaxchronologicalno="SELECT dns_DO_no FROM DO_transaction ORDER BY  DATE_FORMAT(SUBSTRING(DO_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
		  $rsmaxchronologicalno=mysqli_query($link,$sqlmaxchronologicalno);
		  $rowmaxchronologicalno=mysqli_fetch_assoc($rsmaxchronologicalno);
		  $maxchronologicalno=$rowmaxchronologicalno['dns_DO_no'];
		  $maxchronologicalnoparts=explode("/",$maxchronologicalno);
		  $maxchronological=$maxchronologicalnoparts[2]+1;

	  }//End of for loop	
	if(count($DO_details_array)>0)
	{
		for($i=0;$i<count($DO_details_array);$i++){
			$sauda_no=$DO_details_array[$i]->sauda_no;
			$customer_code=$DO_details_array[$i]->customer_code;
			$destination=$DO_details_array[$i]->destination;
			$DO_no=$DO_details_array[$i]->DO_no;
			$sku_code=$DO_details_array[$i]->sku_code;
			$DO_qty=$DO_details_array[$i]->DO_qty;
			$DO_rate=$DO_details_array[$i]->DO_rate;
			$DO_amount=$DO_details_array[$i]->DO_amount;
			$DO_date=$DO_details_array[$i]->DO_date;
			$PO_no=$DO_details_array[$i]->PO_no;
			$delivery_date=$DO_details_array[$i]->delivery_date;
			
			if(no_of_filter==1){
				$sqlproductdetails="SELECT prod_code,dns_prod_code,prod_desc,UOM1,UOM2,UOM3,conversion_factor,conversion_factor_two,branch_code 
									FROM product_master WHERE prod_code='".$sku_code."'";
			}
			if(no_of_filter==2){
				$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PM.prod_desc,PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,PM.dns_prod_code,PM.branch_code FROM product_master PM,product_group_master PGM 
									WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$sku_code."'";
			}
			if(no_of_filter==3){
				$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PSGM.product_sub_group_name,PSGM.product_sub_group_code,PM.prod_desc,PM.UOM1,
									PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,PM.dns_prod_code,PM.branch_code FROM 
									product_master PM,product_group_master PGM,product_sub_group_master PSGM
									WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
									AND PM.prod_code='".$sku_code."'";
			}
			if(no_of_filter==4){
				$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PSGM.product_sub_group_name,PSGM.product_sub_group_code,PBM.product_brand_name,PBM.product_brand_code,PM.prod_desc,
									PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,PM.dns_prod_code,PM.branch_code 
									FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
									WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
									AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$sku_code."'";
			}
			$rsproductdetails=mysqli_query($link,$sqlproductdetails);
			$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
			$prod_code=$rowproductdetails['prod_code'];
			$prod_desc=$rowproductdetails['prod_desc'];
			$product_group_code=$rowproductdetails['product_group_code'];
			$product_group_name=$rowproductdetails['product_group_name'];
			$product_sub_group_code=$rowproductdetails['product_sub_group_code'];
			$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
			$product_brand_code=$rowproductdetails['product_brand_code'];
			$product_brand_name=$rowproductdetails['product_brand_name'];
			$UOM1=$rowproductdetails['UOM1'];
			$UOM2=$rowproductdetails['UOM2'];
			$UOM3=$rowproductdetails['UOM3'];
			$conversion_factor=$rowproductdetails['conversion_factor'];
			$conversion_factor_two=$rowproductdetails['conversion_factor_two'];
			$dns_prod_code=$rowproductdetails['dns_prod_code'];
			if(strtoupper($UOM1)=='LOOSE')
			{
				$qty_MT=round($DO_qty,3);
			}
			if(strtoupper($UOM1)=='CASE')
			{
				$qty_MT=round(($DO_qty*$conversion_factor_two),3);
			}
			if(!in_array($DO_no,$DO_array_trans_id))
			 {
				 $sqldnsbranch="SELECT DISTINCT BM.dns_branch_code FROM branch_master BM,sauda_header SH WHERE 
				 				SH.branch_code=BM.branch_code AND SH.sauda_no='".$sauda_no."'";
				$rsdnsbranch=mysqli_query($link,$sqldnsbranch);
				$rowdnsbranch=mysqli_fetch_assoc($rsdnsbranch);
				$dns_branch_code=$rowdnsbranch['dns_branch_code'];
				$DO_no_date_time=date('dmY',strtotime(substr($DO_no,7,8)));
					
					$dns_DO_no='DO/'.$dns_branch_code."/".$maxchronological.'/'.$DO_no_date_time;
				$sqlinsertDOdetails="INSERT INTO DO_transaction SET sauda_no='".$sauda_no."',
										customer_code ='".$customer_code."',
										destination	='".$destination."',
										DO_no		='".$DO_no."',
										sku_code	='".$sku_code."',
										DO_qty		='".$DO_qty."',
										DO_qty_MT	='".$qty_MT."',
										DO_rate		='".$DO_rate."',
										DO_amount	='".$DO_amount."',
										DO_date		='".$DO_date."',
										PO_no 		='".$PO_no."',
										delivery_date ='".$delivery_date."',
										DO_status	='',
										dns_DO_no	='".$dns_DO_no."',
										download_time='".$location_date_server."'";
				if(mysqli_query($link,$sqlinsertDOdetails))
				{
					$flag=5;
					if(!in_array($sauda_no,$sauda_array_trans_id))
					{
						array_push($sauda_array_trans_id,$sauda_no);
					}
					$sqlselmappedsku="SELECT mapped_sku_code FROM DO_master WHERE sauda_no='".$sauda_no."' AND sku_code	='".$sku_code."'";
					$rsselmappedsku=mysqli_query($link,$sqlselmappedsku);
					$rowselmappedsku=mysqli_fetch_assoc($rsselmappedsku);
					$mapped_sku_code=$rowselmappedsku['mapped_sku_code'];
					
					$sqlbargainqty="SELECT qty,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";
					$rsbargainqty=mysqli_query($link,$sqlbargainqty);
					$bargain_qty_total=0;
					$total_DO_qty=0;
					while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))
					{
						$child_sku_code=$rowbargainqty['sku_code'];
						$bargain_qty_total=$bargain_qty_total+$rowbargainqty['qty'];
						$sqltotalDOqty="SELECT SUM(DO_qty) AS  DO_qty FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
						$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);
						$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);
						//echo '<br />';
						$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty'];
					}
					//echo $bargain_qty_total;
					//echo '<br />';
					//echo $total_DO_qty;
					//echo '<br />';
					//echo $DO_qty;
					if($bargain_qty_total==$total_DO_qty)
					{
						$sqlupdatesaudastat="UPDATE DO_master SET status='yes' WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";
						if(mysqli_query($link,$sqlupdatesaudastat))
						{
							$flag=5;
						}					
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
					}
					if(strtoupper($UOM1)=='LOOSE')
					{
						$convert_qty_MT=$DO_qty;
					}
					else
					{
						$convert_qty_MT=round(($DO_qty*$conversion_factor_two),3);
					}
					${pending_bargain_qty.$DO_no}=${pending_bargain_qty.$DO_no}+$convert_qty_MT;
					${customer_code.$DO_no}=$customer_code;
					if(!in_array($DO_no,$DO_no_array))
					{
						array_push($DO_no_array,$DO_no);
					}
				}					
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			}
		}
	}
	/*foreach($sauda_array_trans_id as $saudaval)
	{
		$sqlupdatesaudastat="UPDATE DO_master SET status='yes' WHERE  sauda_no='".$saudaval."'";
		if(mysqli_query($link,$sqlupdatesaudastat))
		{
			$flag=5;
		}					
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	}*/
	foreach($DO_no_array as $DO_no_val)
	{
		$sqldnscustomercode="SELECT dns_customer_code,rds_tag FROM customer_master WHERE customer_code='".${customer_code.$DO_no_val}."'";
		$rsdnscustomercode=mysqli_query($link,$sqldnscustomercode);
		$rowdnscustomercode=mysqli_fetch_assoc($rsdnscustomercode);
		$dns_customer_code=$rowdnscustomercode['dns_customer_code'];
		$rds_tag=$rowdnscustomercode['rds_tag'];
		if($rds_tag!='')
		{
			$sqldnscustomercoderds="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$rds_tag."'";
			$rsdnscustomercoderds=mysqli_query($link,$sqldnscustomercoderds);
			$rowdnscustomercoderds=mysqli_fetch_assoc($rsdnscustomercoderds);
			$dns_customer_code=$rowdnscustomercoderds['dns_customer_code'];
			${customer_code.$DO_no_val}=$rds_tag;
		}
		$sqlupdatelimit="UPDATE customer_sauda_limit SET 
						pending_qty=(pending_qty-${pending_bargain_qty.$DO_no_val}) 
						WHERE customer_code='".$dns_customer_code."'";
		$sqlupdatecustomerdatetime="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() 
									WHERE customer_code='".${customer_code.$DO_no_val}."'";				
		if(mysqli_query($link,$sqlupdatelimit) && mysqli_query($link,$sqlupdatecustomerdatetime))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}				
	}
}
 /* --------------------END QUERY FOR DO details--------------------------------------------------------------------------------------------------------*/
	if($flag==5)
	{
	   mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
mysqli_close($link);
?>