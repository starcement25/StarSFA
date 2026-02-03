<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM loyalty_card_holder_master ORDER BY loyalty_card_holder_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$string = trim(preg_replace('/\s+/', ' ', $string));
	$contentsrowcolumn  =$count.'¥'.'10';
	if($count>0){
		while($rowloyalty = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowloyalty['loyalty_card_holder_code']!='')?$rowloyalty['loyalty_card_holder_code']: ' ')."^";
				$contents  .= (($rowloyalty['loyalty_card_holder_name']!='')?$rowloyalty['loyalty_card_holder_name']: ' ')."^";
				$contents  .= (($rowloyalty['loyalty_card_no']!='')?trim(preg_replace('/\s+/', '',$rowloyalty['loyalty_card_no'])): ' ')."^";
				$contents  .= (($rowloyalty['card_type']!='')?$rowloyalty['card_type']: ' ')."^";
				$contents  .= (($rowloyalty['total_purchase_value']!='')?$rowloyalty['total_purchase_value']: ' ')."^";
				$contents  .= (($rowloyalty['total_reward_point']!='')?$rowloyalty['total_reward_point']: ' ')."^";
				$contents  .= (($rowloyalty['last_update_on']!='')?$rowloyalty['last_update_on']: ' ')."^";
				$contents  .= (($rowloyalty['redeemed']!='')?$rowloyalty['redeemed']: ' ')."^";
				$contents  .= (($rowloyalty['phone_no']!='')?$rowloyalty['phone_no']: ' ')."^";
				$contents  .= (($rowloyalty['address']!='')?$rowloyalty['address']: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=loyalty_card_holder_master.txt");
	print "$datacontents"; 		
?>
