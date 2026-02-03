<?php
require("include/config.php");
require("include/dbcon.php");

$from_date=$_REQUEST['from_date'];
$to_date=$_REQUEST['to_date'];
$emp_code=$_REQUEST['emp_code'];
$rds_code=$_REQUEST['rds_code'];
$radio_type=$_REQUEST['radio_type'];
$sku_code=$_REQUEST['sku_code'];
$brand_code=$_REQUEST['brand_code'];
$from_date=date('Y-m-d',strtotime($from_date));
$to_date=date('Y-m-d',strtotime($to_date));

$sqlrds="SELECT rds_name FROM rds_master WHERE rds_code='".$rds_code."'";
$rsrds=mysql_query($sqlrds);
$rowrds=mysql_fetch_array($rsrds);
$rds_name=$rowrds['rds_name'];
?>
<style type="text/css">
.TDHEAD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	COLOR: #FFFFFF;
	BACKGROUND-COLOR: #A92A61;/*#92C006;*/
}

.TDHEAD_SUB{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	BACKGROUND-COLOR:#c0c8b0;
}

.border{
	BORDER: #A92A61/*#80A537*/ 1px solid;
}
TD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
}

</style>
<?php 
	
	$datetoday=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	if($month>='04'){
		$fiinancial_year=$year.'-04-01';
	}
	else
	{
		$fiinancial_year=($year-1).'-04-01';
	}

	//For  Today
	if($radio_type=='today')
	{
		$date=date('Y-m-d');
		if($date!='')
		{
			$date_condition =" AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d') ='".$date."'";
			$date_condition_opening_stock =" AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d') <'".$date."' 
										AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
		}
		$heading_val='Of '.date('d-m-Y');
	}
	//For MTD OR Month Today
	if($radio_type=='monthly')
	{
		$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
						AND MONTH(DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
		$date_condition_opening_stock =" AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d') < '".$year.'-'.$month.'-01'."'
										AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";				
		$heading_val='Of '.date('M,Y');				
	}
	//For Custom
	if($radio_type=='yourchoice')
	{
	  $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d') <='".$to_date."'";
	  $date_condition_opening_stock =" AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y-%m-%d') <'".$from_date."' 
	  									AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";		
	  $heading_val='From '.date('d-m-Y',strtotime($from_date)).' To '.date('d-m-Y',strtotime($to_date));
	}
	
$tableval='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 320px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="10" align="center"><strong>Product Wise Sales Report of '.$rds_name.' '.$heading_val.' </strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
		<td width="15%" align="left" style="padding-left:20px;">Product</td>
		<td width="15%" align="left" style="padding-left:20px;">Opening Stock</td>
		<td width="10%" align="left" style="padding-left:20px;">Purchase</td>
		<td width="10%" align="left" style="padding-left:20px;">Sale</td>
		<td width="10%" align="left" style="padding-left:20px;">Stock Transfer</td>
		<td width="10%" align="left" style="padding-left:20px;">Stock Received</td>
		<td width="10%" align="left" style="padding-left:20px;">Shortage</td>
		<td width="" align="left" style="padding-left:20px;">Closing Stock</td>
    </tr> ';
	if($sku_code=='all' && $brand_code=='all')
	{
       $sqlproduct="SELECT prod_desc,prod_code FROM product_master ORDER BY prod_desc ASC";
	}
	else if($sku_code=='all' && $brand_code!='all')
	{
		$sqlproduct="SELECT prod_desc,prod_code FROM product_master WHERE product_group_code='".$brand_code."' ORDER BY prod_desc ASC";
	}
	else
	{
		$sqlproduct="SELECT prod_desc,prod_code FROM product_master WHERE prod_code='".$sku_code."'";
	}
	   $resproduct=mysql_query($sqlproduct) or die(mysql_error()." Error in select product group product: ".$sqlproduct);
	   $cnt=$GLOBALS[start]+1;
		while($rowproduct=mysql_fetch_array($resproduct))
		{
			$prod_desc=$rowproduct['prod_desc'];
			$prod_code=$rowproduct['prod_code']; 
			
			$opening_stk=0;
   			$sql_purchase="SELECT SUM(OD.qty) AS total_purchase_qty FROM order_header OH,order_details OD
									WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
									AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='PB'";
			
			$sql_purchase_till_day=$sql_purchase." AND
									DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
									AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
		   $res_purchase_till_day=mysql_query($sql_purchase_till_day) or die(mysql_error()." Error in select purchase till day: ".$sql_purchase_till_day);
		   $row_purchase_till_day=mysql_fetch_array($res_purchase_till_day);
		   $purchase_till_day=$row_purchase_till_day['total_purchase_qty'];
		   
		   $sql_purchase_query=$sql_purchase.$date_condition;
		   $res_purchase_query=mysql_query($sql_purchase_query) or die(mysql_error()." Error in select purchase query: ".$sql_purchase_query);
		   $row_purchase_query=mysql_fetch_array($res_purchase_query);
		   $purchase_value=$row_purchase_query['total_purchase_qty'];
		   
		   $sql_purchase_op_stk_query=$sql_purchase.$date_condition_opening_stock;
		   $res_purchase_op_stk_query=mysql_query($sql_purchase_op_stk_query) or die(mysql_error()." Error in select 
		   						purchase query opening stock: ".$sql_purchase_op_stk_query);
		   $row_purchase_op_stk_query=mysql_fetch_array($res_purchase_op_stk_query);
		   $purchase_value_op_stk=$row_purchase_op_stk_query['total_purchase_qty'];
		   
		   $sql_sale="SELECT SUM(OD.qty) AS total_sale_qty FROM order_header OH,order_details OD
								WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
								AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='SB'";
		   
		   $sql_sale_till_day=$sql_sale." AND
								DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
								AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
		  $res_sale_till_day=mysql_query($sql_sale_till_day) or die(mysql_error()." Error in select sale till day: ".$sql_sale_till_day);
		  $row_sale_till_day=mysql_fetch_array($res_sale_till_day);
		  $sale_till_day=$row_sale_till_day['total_sale_qty'];
		  
		   $sql_sale_query=$sql_sale.$date_condition;
		   $res_sale_query=mysql_query($sql_sale_query) or die(mysql_error()." Error in select sale query: ".$sql_sale_query);
		   $row_sale_query=mysql_fetch_array($res_sale_query);
		   $sale_value=$row_sale_query['total_sale_qty'];
		   
		   $sql_sale_op_stk_query=$sql_sale.$date_condition_opening_stock;
		   $res_sale_op_stk_query=mysql_query($sql_sale_op_stk_query) or die(mysql_error()." Error in select sale query opening stock: ".$sql_sale_op_stk_query);
		   $row_sale_op_stk_query=mysql_fetch_array($res_sale_op_stk_query);
		   $sale_value_op_stk=$row_sale_op_stk_query['total_sale_qty'];

		  $sql_stock_transfer="SELECT SUM(OD.qty) AS total_stocktransfer_qty FROM order_header OH,order_details OD
										WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
										AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='ST'";
										
		  $sql_stock_transfer_till_day=$sql_stock_transfer." AND
										DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
										AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
		$res_stock_transfer_till_day=mysql_query($sql_stock_transfer_till_day) or die(mysql_error()." Error in select stock transfer till day: ".$sql_stock_transfer_till_day);
		$row_stock_transfer_till_day=mysql_fetch_array($res_stock_transfer_till_day);
		$stock_transfer_till_day=$row_stock_transfer_till_day['total_stocktransfer_qty'];
		
		 $sql_stock_transfer_query=$sql_stock_transfer.$date_condition;
		 $res_stock_transfer_query=mysql_query($sql_stock_transfer_query) or die(mysql_error()." Error in select stock_transfer query: ".$sql_stock_transfer_query);
		 $row_stock_transfer_query=mysql_fetch_array($res_stock_transfer_query);
		 $stock_transfer_value=$row_stock_transfer_query['total_stocktransfer_qty'];
		 
		 $sql_stock_transfer_op_stk_query=$sql_stock_transfer.$date_condition_opening_stock;
		 $res_stock_transfer_op_stk_query=mysql_query($sql_stock_transfer_op_stk_query) or die(mysql_error()." Error in select stock_transfer query opening stock: ".$sql_stock_transfer_op_stk_query);
		 $row_stock_transfer_op_stk_query=mysql_fetch_array($res_stock_transfer_op_stk_query);
		 $stock_transfer_value_op_stk=$row_stock_transfer_op_stk_query['total_stocktransfer_qty'];

		$sql_stock_receive="SELECT SUM(OD.qty) AS total_stockreceive_qty FROM order_header OH,order_details OD
							WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
							AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='BT'";
									
		$sql_stock_receive_till_day=$sql_stock_receive." AND
									DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
									AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
		$res_stock_receive_till_day=mysql_query($sql_stock_receive_till_day) or die(mysql_error()." Error in select stock receive till day: ".$sql_stock_receive_till_day);
		$row_stock_receive_till_day=mysql_fetch_array($res_stock_receive_till_day);
		$stock_receive_till_day=$row_stock_receive_till_day['total_stockreceive_qty'];
		
		 $sql_stock_receive_query=$sql_stock_receive.$date_condition;
		 $res_stock_receive_query=mysql_query($sql_stock_receive_query) or die(mysql_error()." Error in select stock_receive query: ".$sql_stock_receive_query);
		 $row_stock_receive_query=mysql_fetch_array($res_stock_receive_query);
		 $stock_receive_value=$row_stock_receive_query['total_stockreceive_qty'];
		 
		 $sql_stock_receive_op_stk_query=$sql_stock_receive.$date_condition_opening_stock;
		 $res_stock_receive_op_stk_query=mysql_query($sql_stock_receive_op_stk_query) or die(mysql_error()." Error in select stock_receive query opening stock: ".$sql_stock_receive_op_stk_query);
		 $row_stock_receive_op_stk_query=mysql_fetch_array($res_stock_receive_op_stk_query);
		 $stock_receive_value_op_stk=$row_stock_receive_op_stk_query['total_stockreceive_qty'];	
	
		$sql_shortage="SELECT SUM(OD.qty) AS total_shortage_qty FROM order_header OH,order_details OD
						WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
						AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type IN ('SA','SH')";
						
		$sql_shortage_till_day=$sql_shortage." AND
									DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
									AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
		$res_shortage_till_day=mysql_query($sql_shortage_till_day) or die(mysql_error()." Error in select shortage till day: ".$sql_shortage_till_day);
		$row_shortage_till_day=mysql_fetch_array($res_shortage_till_day);
		$shortage_till_day=$row_shortage_till_day['total_shortage_qty'];
		
		 $sql_shortage_query=$sql_shortage.$date_condition;
		 $res_shortage_query=mysql_query($sql_shortage_query) or die(mysql_error()." Error in select shortage query: ".$sql_shortage_query);
		 $row_shortage_query=mysql_fetch_array($res_shortage_query);
		 $shortage_value=$row_shortage_query['total_shortage_qty'];
		 
		 $sql_shortage_op_stk_query=$sql_shortage.$date_condition_opening_stock;
		 $res_shortage_op_stk_query=mysql_query($sql_shortage_op_stk_query) or die(mysql_error()." Error in select shortage query opening stock: ".$sql_shortage_op_stk_query);
		 $row_shortage_op_stk_query=mysql_fetch_array($res_shortage_op_stk_query);
		 $shortage_value_op_stk=$row_shortage_op_stk_query['total_shortage_qty'];		

		$opening_stk_final=(($purchase_value_op_stk+$stock_receive_value_op_stk)-($sale_value_op_stk+$shortage_value_op_stk+$stock_transfer_value_op_stk));
		if($radio_type=='today' || $radio_type=='monthly')
		{
			$closing_stock=($opening_stk+$purchase_till_day+$stock_receive_till_day)-($sale_till_day+$shortage_till_day+$stock_transfer_till_day);
		}
		else
		{
			$closing_stock=($opening_stk_final+$purchase_value+$stock_receive_value)-($sale_value+$shortage_value+$stock_transfer_value);
		}
		$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showProductwisesalesDeatails('".$rds_code."','".$emp_code."','".$from_date."','".$to_date."','".$radio_type."','".$prod_code."')\"  style=\"color:#930;font-weight:bold;\">".$prod_desc."</a></td>
                    <td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($opening_stk_final,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($purchase_value,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($sale_value,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($stock_transfer_value,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($stock_receive_value,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($shortage_value,2)."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($closing_stock,2)."</td>
              </tr>";

		}
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
?>