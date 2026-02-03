<?php
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
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
}
if($incremental_download=='no')
{
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(SMD.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
}

 $sqlstockmisdetails = "SELECT SMD.sl_no,SMD.customer_code,SMD.customer_name,SMD.prod_code,SMD.prod_desc,SMD.sale_qty,SMD.cl_stk_qty,
  						SMD.create_date,SMD.active_flag
 				  		FROM customer_route_emp_relation CRR,
						employee_master EM,stock_mis_details SMD WHERE 
						CRR.emp_code=EM.emp_code AND SMD.customer_code=CRR.customer_code AND CRR.emp_code 
						IN(".$employee_hierarchy.") ".$login_condition;	
 $resultstockmisdetails = mysqli_query($link,$sqlstockmisdetails);
 $count=mysqli_num_rows($resultstockmisdetails);

 $date=gmdate('d',strtotime('+330 minute'));
 $month=gmdate('m',strtotime('+330 minute'));
 $year=gmdate('Y',strtotime('+330 minute'));

 $hour=gmdate('H',strtotime('+330 minute'));
 $minute=gmdate('i',strtotime('+330 minute'));
 $second=gmdate('s',strtotime('+330 minute'));
 $contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'9';
		while($rowstockmisdetails = mysqli_fetch_assoc($resultstockmisdetails))
		{
			$customer_code=$rowstockmisdetails['customer_code'];
			$sl_no=$rowstockmisdetails['sl_no'];
			$customer_name=$rowstockmisdetails['customer_name'];
			$prod_code=$rowstockmisdetails['prod_code'];
			$prod_desc=$rowstockmisdetails['prod_desc'];
			$sale_qty=$rowstockmisdetails['sale_qty'];
			$cl_stk_qty=$rowstockmisdetails['cl_stk_qty'];
			$create_date=$rowstockmisdetails['create_date'];
			$active_flag=$rowstockmisdetails['active_flag'];
			
				$contents = (($sl_no!='')?$sl_no: ' ')."^";
				$contents .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents .= (($customer_name!='')?$customer_name: ' ')."^";
				$contents .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents .= (($prod_desc!='')?$prod_desc: ' ')."^";
				$contents .= (($sale_qty!='')?$sale_qty: ' ')."^";
				$contents .= (($cl_stk_qty!='')?$cl_stk_qty: ' ')."^";
				$contents .= (($create_date!='')?$create_date: ' ')."^";
				$contents .= (($active_flag!='')?$active_flag: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/sale-stock-mis-txtincremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=sale_stock_mis.txt");
	print "$datacontents"; 		
?>
