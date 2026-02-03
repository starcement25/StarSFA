<?php
//require("include/config.php");
define("SERVER","216.237.114.58");
define("USER","coralweb");
define("PASSWORD","coral5071");
$nick_name='TYS';
$nick_name_one='VIPL';
define("DB","$nick_name");
define("DBONE","$nick_name_one");
require("include/config-setup.php");
//require("include/dbcon.php");

function connecttodb($servername,$dbname,$dbuser,$dbpassword)
{
    $link=mysqli_connect($servername,$dbuser,$dbpassword,TRUE) or die("Database Connection Error.");
	mysqli_select_db($dbname,$link) or die("could not connect the database for invalid nick name");
    return $link;
}
${link1} = connecttodb(SERVER,DB,USER,PASSWORD);
${link2} = connecttodb(SERVER,DBONE,USER,PASSWORD);
require("include/config-email-setup.php");
$date=date('Y-m-d');
$serverdate=date('Ymd', strtotime("$date"));
$countloop=1;
$group_array=array();
$subgroup_array=array();
$product_array=array();

for($i=1;$i<=2;$i++){
$messagestring='<html><body>
				<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" >
				<tr> 
					<td colspan="4" align="left"><strong>ITEM WISE REPORT: '.date('d/m/Y',strtotime("$date")).'</strong></td>
				</tr><br />
				<tr>
					<td>
						<table width="98%" align="center" cellpadding="5" cellspacing="2">';
						
/*$sqlorderdetails="SELECT OD.sku_code,OD.qty,OD.mrp_code,OD.TD,OD.sale_rate,PGM.product_group_name,PGM.product_group_code,
				PSGM.product_sub_group_name,PSGM.product_sub_group_code,PM.prod_desc
				FROM order_details OD,product_group_master PGM,product_sub_group_master PSGM,product_master PM
				WHERE OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code 
				AND PM.product_sub_group_code=PSGM.product_sub_group_code AND SUBSTRING(OD.order_no,-14,8)='".$serverdate."'";*/
				/*if($i==1){
				$link="$link".$i;
				}
				if($i==2){
				$link="$link".$i;
				}*/
$sqltotproduct="SELECT COUNT(*) AS tot_prod FROM product_master";
$rstotproduct=mysqli_query($link,$sqltotproduct,${link.$i});
$rowtotproduct=mysqli_fetch_assoc($rstotproduct);
echo 'tot_product'.$tot_prod=$rowtotproduct['tot_prod'];

mysqli_close(${link.$i});
//exit();
$sqlorderdetails="SELECT OD.sku_code,SUM(OD.qty) AS qty,OD.mrp_code,MRP.mrp,OD.TD,OD.sale_rate,PGM.product_group_name,PGM.product_group_code,
				PSGM.product_sub_group_name,PSGM.product_sub_group_code,PM.prod_desc,SUM((OD.qty*MRP.mrp)-(((OD.qty*MRP.mrp)*OD.TD)/100)) AS total_mrp
				FROM order_details OD,product_group_master PGM,product_sub_group_master PSGM,product_master PM,mrp MRP
				WHERE OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code 
				AND PM.product_sub_group_code=PSGM.product_sub_group_code AND OD.mrp_code=MRP.mrp_code 
				AND SUBSTRING(OD.order_no,-14,8)='".$serverdate."' GROUP BY OD.sku_code ORDER BY PGM.product_group_code ASC";				
$rsorderdetails=mysqli_query($link,$sqlorderdetails,"$link_$i");
$count=mysqli_num_rows($rsorderdetails);
if($count>0)
{
	while($roworderdetails=mysqli_fetch_assoc($rsorderdetails))
	{
		$prod_desc=$roworderdetails['prod_desc'];
		$qty=$roworderdetails['qty'];
		$mrp_code=$roworderdetails['mrp_code'];
		$TD=$roworderdetails['TD'];
		$sale_rate=$roworderdetails['sale_rate'];
		$prod_code=$roworderdetails['sku_code'];
		$product_group_code=$roworderdetails['product_group_code'];
		$product_sub_group_code=$roworderdetails['product_sub_group_code'];
		
		
		if(!in_array($roworderdetails['product_group_name'],$group_array))
		{
			array_push($group_array,$roworderdetails['product_group_name']);
			$sqlproductgrouptotal="SELECT PM.prod_code
									FROM order_details OD,product_group_master PGM,product_sub_group_master PSGM,product_master PM,mrp MRP
									WHERE OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code 
									AND PM.product_sub_group_code=PSGM.product_sub_group_code AND OD.mrp_code=MRP.mrp_code 
									AND SUBSTRING(OD.order_no,-14,8)='".$serverdate."' AND PGM.product_group_code='".$product_group_code."'
									GROUP BY OD.sku_code";
			$rsproductgrouptotal=mysqli_query($link,$sqlproductgrouptotal,"$link_$i");
			//$rowproductgrouptotal=mysqli_fetch_assoc($rsproductgrouptotal);
			//$total_product_group=$rowproductgrouptotal['total_product_group'];
			$total_product_group=mysqli_num_rows($rsproductgrouptotal);
			
			$group_total=$group_total+$total_product_group;
			
			$messagestring.='<tr>
								<td align="left" colspan="2" style="BORDER: #000000 1px solid;">
								<strong>Product Group: '.$roworderdetails['product_group_name'].'</strong></td>';
			if(!in_array($roworderdetails['product_sub_group_name'],$subgroup_array))
			{
				array_push($subgroup_array,$roworderdetails['product_sub_group_name']);
				/*$sqlproductsubgrouptotal="SELECT COUNT(PM.prod_code) AS total_product_sub_group
								FROM order_details OD,product_group_master PGM,product_sub_group_master PSGM,product_master PM
								WHERE OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code 
								AND PM.product_sub_group_code=PSGM.product_sub_group_code AND SUBSTRING(OD.order_no,-14,8)='".$serverdate."'
								AND PSGM.product_sub_group_code='".$product_sub_group_code."'";
				$rsproductsubgrouptotal=mysqli_query($link,$sqlproductsubgrouptotal);
				$rowproductsubgrouptotal=mysqli_fetch_assoc($rsproductsubgrouptotal);
				$total_product_sub_group=$rowproductsubgrouptotal['total_product_sub_group'];*/

				$messagestring.='<td align="left" colspan="2" style="BORDER: #000000 1px solid;">
								<strong>Product Sub Group: '.$roworderdetails['product_sub_group_name'].'</strong></td>
							</tr>';
			}					
		}
		// For repeated product group the product group name column will be blank only show the product sub group name
		if(!in_array($roworderdetails['product_sub_group_name'],$subgroup_array))
			{
				array_push($subgroup_array,$roworderdetails['product_sub_group_name']);
				$messagestring.='<tr>
									<td align="left" colspan="2" style="BORDER: #000000 1px solid;">&nbsp;</td>
									<td align="left" colspan="2" style="BORDER: #000000 1px solid;">
									<strong>Product Sub Group: '.$roworderdetails['product_sub_group_name'].'</strong></td>
								</tr>';
			}
			if($countloop==1){
				$messagestring.='<tr>
						<td align="left" width=""  style="BORDER: #000000 1px solid;">Product</td>
						<td align="left" width="15%" style="BORDER: #000000 1px solid;">Qty</td>
						<td align="left" width="12%" style="BORDER: #000000 1px solid;">Rate</td>
						<td align="left" width="16%" style="BORDER: #000000 1px solid;">Amount</td>
					 </tr>';
			}
			
			/*$sqlmrpdetails="SELECT mrp FROM mrp WHERE product_code='".$prod_code."' AND mrp_code='".$mrp_code."'";
			$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
			$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
			$mrp=$recmrpdetails['mrp'];
			
			$totalmrp=($qty*$mrp)-((($qty*$mrp)*$TD)/100);
			${subqty.$product_sub_group_code}=${subqty.$product_sub_group_code}+$qty;
			${subrate.$product_sub_group_code}=${subrate.$product_sub_group_code}+$mrp;
			${subtotal.$product_sub_group_code}=${subtotal.$product_sub_group_code}+$totalmrp;*/
			
			$mrp=$roworderdetails['mrp'];
			$totalmrp=$roworderdetails['total_mrp'];
			$messagestring.='<tr>
								<td align="left" style="BORDER: #000000 1px solid;">'.$prod_desc.'</td>
								<td align="right" style="BORDER: #000000 1px solid;">'.$qty.'</td>
								<td align="right" style="BORDER: #000000 1px solid;">'.number_format($mrp,2).'</td>
								<td align="right" style="BORDER: #000000 1px solid;">'.number_format($totalmrp,2).'</td>
							 </tr>';
							 
			${groupqty.$product_group_code}=${groupqty.$product_group_code}+$qty;
			${grouptotal.$product_group_code}=${grouptotal.$product_group_code}+$totalmrp;
			
			${grandqty.$serverdate}=${grandqty.$serverdate}+$qty;
			${grandtotal.$serverdate}=${grandtotal.$serverdate}+$totalmrp;	
			if($group_total==$countloop )
			{
				$messagestring.= '<tr>
										<td align="left" style="BORDER: #000000 1px solid;" colspan="1"><strong>Total</strong></td>
										<td align="right" style="BORDER: #000000 1px solid;">'.${groupqty.$product_group_code}.'</td>
										<td align="right" style="BORDER: #000000 1px solid;">&nbsp;</td>
										<td align="right" style="BORDER: #000000 1px solid;">'.number_format(${grouptotal.$product_group_code},2).'</td>
								</tr><br />';
			}
			if($count==$countloop)
			{
				$messagestring.= '<tr>
										<td align="left" style="BORDER: #000000 1px solid;" colspan="1"><strong>Grand Total</strong></td>
										<td align="right" style="BORDER: #000000 1px solid;">'.${grandqty.$serverdate}.'</td>
										<td align="right" style="BORDER: #000000 1px solid;">&nbsp;</td>
										<td align="right" style="BORDER: #000000 1px solid;">'.number_format(${grandtotal.$serverdate},2).'</td>
								</tr><br />';
			}
		$countloop++;
	}
}
else
{
	$messagestring.='<tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>No operations performed</strong></td>
					</tr></table></td></tr></table>';
}
echo $messagestring.= '</table></td></tr></table><br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';

//************************ Send Email ****************************************************//

	//echo $messagestring;
	/*$email_to='pk.kankaria@gmail.com,vikram.solutions@gmail.com';
	$mailsubj="TYS item wise report of ".date('d/m/Y',strtotime("$date"));
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestring, $headers,'-facedns@coral.in');*/
	
}
?>