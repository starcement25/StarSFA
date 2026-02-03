<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlBOQ="SELECT BM.sl_no,BM.BOQ_id,BM.product_group_code,BM.mi_type,BM.prod_code,BM.area,BM.spacing,BM.qty,BM.amount,BM.acedns,PM.prod_desc 
			FROM BOQ_master BM INNER JOIN product_master PM WHERE BM.prod_code=PM.dns_prod_code ORDER BY PM.prod_desc ASC ";
$rsBOQ=mysqli_query($link,$sqlBOQ);
$count=mysqli_num_rows($rsBOQ);
$contentsrowcolumn=$count.'¥'.'10';
if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowBOQ = mysqli_fetch_assoc($rsBOQ))
		{
			$BOQ_id=$rowBOQ['BOQ_id'];
			$product_group_code=$rowBOQ['product_group_code'];
			$mi_type=$rowBOQ['mi_type'];
			$prod_code=$rowBOQ['prod_code'];
			$prod_desc=$rowBOQ['prod_desc'];
			$area=$rowBOQ['area'];
			$spacing=$rowBOQ['spacing'];
			$qty=$rowBOQ['qty'];
			$amount=$rowBOQ['amount'];
			$acedns=$rowBOQ['acedns'];
			$sl_no=$rowBOQ['sl_no'];
			//For tabular form data
			$contents  = (($BOQ_id!='')?$BOQ_id: ' ')."^";
			$contents  .= (($product_group_code!='')?$product_group_code: ' ')."^";
			$contents  .= (($mi_type!='')?$mi_type: ' ')."^";
			$contents  .= (($sl_no!='')?$sl_no: ' ')."^";
			$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
			$contents  .= (($area!='')?$area: ' ')."^";
			$contents  .= (($spacing!='')?$spacing: ' ')."^";
			$contents  .= (($qty!='')?$qty: ' ')."^";
			$contents  .= (($amount!='')?$amount: ' ')."^";
			$contents  .= (($acedns!='')?$acedns: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents =$contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
    }
	else
	{
		$datacontents = '0'.'¥'.'0';
	}	
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=BOQ_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
