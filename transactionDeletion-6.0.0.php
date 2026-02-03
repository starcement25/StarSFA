<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/dbcon.php");
$body=file_get_contents('php://input');
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><deletion_data><order_no><![CDATA[OE004120150101121724]]></order_no><branch_code><![CDATA[]]></branch_code><rds_code><![CDATA[]]></rds_code><start_date><![CDATA[]]></start_date><end_date><![CDATA[]]></end_date><deletion_mode><![CDATA[transactionwise]]></deletion_mode></deletion_data></root>";*/

$deletion_order_no = "*ROOT*DELETION_DATA*ORDER_NO";
$deletion_branch_code = "*ROOT*DELETION_DATA*BRANCH_CODE";
$deletion_rds_code = "*ROOT*DELETION_DATA*RDS_CODE";
$deletion_start_date = "*ROOT*DELETION_DATA*START_DATE";
$deletion_end_date = "*ROOT*DELETION_DATA*END_DATE";
$deletion_mode = "*ROOT*DELETION_DATA*DELETION_MODE";
$emp_code=$_REQUEST['emp_code'];

$deletion_array = array();
$counter = 0;

class xml_deletion{
    var $deletion_order_no, $deletion_branch_code,$deletion_rds_code,$deletion_start_date,$deletion_end_date,$deletion_mode;
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
    global $current_tag, $deletion_order_no, $deletion_branch_code,$deletion_rds_code,$deletion_start_date,$deletion_end_date,$deletion_mode, $counter, $deletion_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,19)=='*ROOT*DELETION_DATA')
	{
		switch($current_tag){
			case $deletion_order_no:
				$deletion_array[$counter] = new xml_deletion();
				$deletion_array[$counter]->deletion_order_no = $data;
				break;
			case $deletion_branch_code:
				$deletion_array[$counter]->deletion_branch_code = $data;
				break;
			case $deletion_rds_code:
				$deletion_array[$counter]->deletion_rds_code = $data;
				break;
			case $deletion_start_date:
				$deletion_array[$counter]->deletion_start_date = $data;
				break;
			case $deletion_end_date:
				$deletion_array[$counter]->deletion_end_date = $data;
				break;
			case $deletion_mode:
				$deletion_array[$counter]->deletion_mode = $data;
				$counter++;
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
//print_r($deletion_array);

mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

if(count($deletion_array)>0)
{
	for($x=0;$x<count($deletion_array);$x++){
		$order_no=$deletion_array[$x]->deletion_order_no;
		$branch_code=$deletion_array[$x]->deletion_branch_code;
		$rds_code=$deletion_array[$x]->deletion_rds_code;
		$start_date=$deletion_array[$x]->deletion_start_date;
		$end_date=$deletion_array[$x]->deletion_end_date;
		$deletion_mode=$deletion_array[$x]->deletion_mode;
		
		$sqlinsertactivitylog="INSERT INTO activity_log SET 
								transaction_id='".$order_no."',
								branch_code='".$branch_code."',
								rds_code='".$rds_code."',
								start_date='".$start_date."',
								end_date='".$end_date."',
								request_datetime=CURRENT_TIMESTAMP(),
								updated_flag='0',
								operation_type='deletion ".$emp_code."'";
		 mysqli_query($link,$sqlinsertactivitylog);	
		
		if($deletion_mode=='transactionwise')
		{
			$order_type=substr($order_no,0,1);
			if($order_type=='O')
			{
				$sqlsaletype="SELECT sale_type FROM order_header WHERE order_no='".$order_no."'";
				$rssaletype=mysqli_query($link,$sqlsaletype);
				$cntsaletype=mysqli_num_rows($rssaletype);
				if($cntsaletype >0)
				{
					$rowsaletype=mysqli_fetch_assoc($rssaletype);
					$sale_type=$rowsaletype['sale_type'];
				}
				$sqlgrndelete="DELETE FROM goods_in_transit WHERE grn_no='".$order_no."'";
				if(mysqli_query($link,$sqlgrndelete))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
				$sqlgrnordervaluedelete="DELETE FROM goods_in_transit WHERE order_no='".$order_no."'";
				if(mysqli_query($link,$sqlgrnordervaluedelete))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
			
			$sqlquerydeleteorder="DELETE location,order_header,order_details FROM location INNER JOIN 
								 order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
								AND order_header.order_no=order_details.order_no WHERE location.trans_id ='".$order_no."'";
			if(mysqli_query($link,$sqlquerydeleteorder))
			{
				if(mysqli_affected_rows() <1)
				{
					$flag=2;
				}
				else
				{
					$flag=1;
				}
			}
			else
			{
				$flag=0;
			}
			$sqlquerydeletemis="DELETE FROM mis_transaction_log WHERE trans_id ='".$order_no."'";
			if(mysqli_query($link,$sqlquerydeletemis))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}		
			if($sale_type=='CASH')
			{
				$receipt_id=str_replace('O','P',$order_no);
				
				$sqlquerydeletepayment="DELETE location,payment_header,payment_details FROM location INNER JOIN 
									payment_header INNER JOIN payment_details ON location.trans_id=payment_header.receipt_id 
									AND payment_header.receipt_id=payment_details.receipt_id 
									WHERE location.trans_id='".$receipt_id."'";
				if(mysqli_query($link,$sqlquerydeletepayment))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
			  }
			}
			else
			{
				$sqlquerydeleteexpense="DELETE location,freight_expenses FROM location INNER JOIN 
									freight_expenses ON location.trans_id=freight_expenses.freight_exp_trans_id 
									WHERE location.trans_id ='".$order_no."'";
				if(mysqli_query($link,$sqlquerydeleteexpense))
				{
					if(mysqli_affected_rows() <1)
					{
						$flag=2;
					}
					else
					{
						$flag=1;
					}
				}
				else
				{
					$flag=0;
				}
			}
			if($flag==0)
			{
				mysqli_query($link,"ROLLBACK");
				echo '0';
			}
			else if($flag==1)
			{
				mysqli_query($link,"COMMIT");
				$sqlupdateactivitylog="UPDATE activity_log SET 
									   updated_flag='1',
									   update_datetime=CURRENT_TIMESTAMP()
									   WHERE transaction_id='".$order_no."'";
				mysqli_query($link,$sqlupdateactivitylog);	
				echo '1';
			}
			else
			{
				echo '2';
			}
		}
		if($deletion_mode=='datewise')
		{
			$sqlemp="SELECT emp_code,rds_name FROM rds_master WHERE rds_code='".$rds_code."'";
			$rsemp=mysqli_query($link,$sqlemp);
			$rowemp=mysqli_fetch_assoc($rsemp);
			$emp_code=$rowemp['emp_code'];
			$rds_name=$rowemp['rds_name'];
		
			if($start_date!='' && $end_date!='')
			{
				$date_condition=" AND DATE_FORMAT(location.date,'%Y-%m-%d') >='".$start_date."' AND DATE_FORMAT(location.date,'%Y-%m-%d') <='".$end_date."'";
			}
			else
			{
				$date_condition='';
			}
			
			$sqlgrndelete="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
							order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
						  AND order_header.order_no=order_details.order_no AND location.trans_id=goods_in_transit.grn_no  
						  WHERE location.emp_code='".$emp_code."' ".$date_condition."";
			if(mysqli_query($link,$sqlgrndelete))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
			$sqlgrnreceiverdelete="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
							order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
						  AND order_header.order_no=order_details.order_no AND location.trans_id=goods_in_transit.order_no  
						  WHERE location.emp_code='".$emp_code."' ".$date_condition."";
			if(mysqli_query($link,$sqlgrnreceiverdelete))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
			
			$sqlquerydeleteorder="DELETE location,order_header,order_details FROM location INNER JOIN 
								 order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
								 AND order_header.order_no=order_details.order_no WHERE location.emp_code='".$emp_code."' ".$date_condition."";
			$sqlquerydeletepayment="DELETE location,payment_header,payment_details FROM location INNER JOIN 
									payment_header INNER JOIN payment_details ON location.trans_id=payment_header.receipt_id 
									AND payment_header.receipt_id=payment_details.receipt_id 
									WHERE location.emp_code='".$emp_code."' ".$date_condition."";
			$sqlquerydeleteexpense="DELETE location,freight_expenses FROM location INNER JOIN 
									freight_expenses ON location.trans_id=freight_expenses.freight_exp_trans_id 
									WHERE location.emp_code='".$emp_code."' ".$date_condition."";
					
			if(mysqli_query($link,$sqlquerydeleteorder) && mysqli_query($link,$sqlquerydeletepayment) && mysqli_query($link,$sqlquerydeleteexpense))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
			if($flag==1)
			{
				mysqli_query($link,"COMMIT");
				$sqlupdateactivitylog="UPDATE activity_log SET 
									   updated_flag='1',
									   update_datetime=CURRENT_TIMESTAMP()
									   WHERE branch_code='".$branch_code."' AND rds_code='".$rds_code."' AND start_date='".$start_date."' AND end_date='".$end_date."'";
				mysqli_query($link,$sqlupdateactivitylog);	
				echo '1';
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo '0';
			}
		}
	}
}
mysqli_close($link);
?>
