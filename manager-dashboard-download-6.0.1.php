<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
/*$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if($incremental_download=='no')
{
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ";
}*/
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$currentdate=$year.'-'.$month.'-'.$date;
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$currentdate=$_REQUEST['dateval'];
$current_time=$_REQUEST['timeval'];
$currentdatetime =$year.'-'.$month.'-'.$date.' '.$current_time;



if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_val_rds=' AND (LO.emp_code IN('.$employee_hierarchy.'))';
}
else
{
	$emp_val_rds=" AND emp_code='".$emp_code."'";
} 
$sqllocation = "SELECT EM.emp_code,EM.emp_name,SUBSTRING(LO.date,1,10) as att_date,DATE_FORMAT(LO.date,'%T') as att_time FROM employee_master EM INNER  JOIN location LO 
			    ON LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%'  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') >= '2024-07-21'
				".$emp_val_rds." ORDER BY EM.emp_name ASC";
				
				//echo $sqllocation;
$resultlocation = mysqli_query($link,$sqllocation);
$count=mysqli_num_rows($resultlocation);
	if($count>0){
		$contentsrowcolumn=$count.'¥'.'23';
		while($rowlocation = mysqli_fetch_assoc($resultlocation))
		{
		   $emp_code_lower=$rowlocation['emp_code'];
		   $emp_name=$rowlocation['emp_name'];
			${'att_date'.$emp_code_lower}=$rowlocation['att_date'];	
		   ${'att_time'.$emp_code_lower}=$rowlocation['att_time'];

		   if(strtoupper($nick_name)=='STAR' || strtoupper($nick_name)=='START')
		   {

			 $sql_checkout = "SELECT SUBSTRING(`date`,12) AS checkout_time,trans_id FROM location  WHERE emp_code = '".$emp_code_lower."' 
							AND trans_id LIKE 'CH%' AND DATE_FORMAT(`date`,'%Y-%m-%d')='".${'att_date'.$emp_code_lower}."'";
							//echo $sql_checkout;
			$res_checkout = mysqli_query($link,$sql_checkout);
			$row_checkout = mysqli_fetch_assoc($res_checkout);
			$check_out_time = $row_checkout['checkout_time'];
			//${'check_out_time'.$emp_code.$att_date}=$check_out_time;
			${'check_out_time'.$emp_code_lower}=$check_out_time;
 
			  $sqlmarketoverview="SELECT kyc,technical_meet,counter_meet,mega_mason_meet,engineers_meet,professional_meet,contractor_meet,dealer_subdealer_meet,complaint,mason_meet,IHB_meet,small_engineers_meet,big_contractor_meet,catch_them_young,pc_traning_programme,customer_guidance_camp,site_visit,complaint_report,dhalai_service,site_tracking_conversion FROM  mis_details_emp_datewise  WHERE emp_code='".$emp_code_lower."' AND DATE_FORMAT(operation_date,'%Y-%m-%d')='".${'att_date'.$emp_code_lower}."'";
			   
			 $rsmarketoverview=mysqli_query($link,$sqlmarketoverview);
		   	 $rowmarketoverview=mysqli_fetch_assoc($rsmarketoverview);
		   	 $total_kyc=$rowmarketoverview['kyc'];
			 $total_technical_meet=$rowmarketoverview['technical_meet'];  
			 $total_counter_meet=$rowmarketoverview['counter_meet'];
			 $total_mega_mason_meet=$rowmarketoverview['mega_mason_meet'];
			 $total_engineers_meet=$rowmarketoverview['engineers_meet'];
			 $total_professional_meet=$rowmarketoverview['professional_meet'];
			 $total_contractor_meet=$rowmarketoverview['contractor_meet'];
			 $total_dealer_subdealer_meet=$rowmarketoverview['dealer_subdealer_meet'];
			   $total_complaint=$rowmarketoverview['complaint'];
			   $total_mason_meet=$rowmarketoverview['mason_meet'];
			   $total_IHB_meet=$rowmarketoverview['IHB_meet'];
			   $total_small_engineers_meet=$rowmarketoverview['small_engineers_meet'];
			   $total_big_contractor_meet=$rowmarketoverview['big_contractor_meet'];
			   $total_catch_them_young=$rowmarketoverview['catch_them_young'];
			   $total_pc_traning_programme=$rowmarketoverview['pc_traning_programme'];
			   $total_customer_guidance_camp=$rowmarketoverview['customer_guidance_camp'];
			   $total_site_visit=$rowmarketoverview['site_visit'];
			   $total_complaint_report=$rowmarketoverview['complaint_report'];
			   $total_dhalai_service=$rowmarketoverview['dhalai_service'];
			   $total_site_tracking_conversion=$rowmarketoverview['site_tracking_conversion'];
			   
			   
		   }
		   else
		   {
			  $sqlcustomervisit="SELECT COUNT(DISTINCT customer_code) AS tot_customer_visit FROM prev_order_counting_master WHERE 
			  					SUBSTRING(order_no,-19,5)='".$emp_code_lower."' 
							AND DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') <='".$currentdatetime."' AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')='".$currentdate."'";
			    $rscustomervisit=mysqli_query($link,$sqlcustomervisit);
		   $rowcustomervisit=mysqli_fetch_assoc($rscustomervisit);
		   $total_customer_visit=$rowcustomervisit['tot_customer_visit'];
		   }
		   
			
			$contents = (($emp_code_lower!='')?$emp_code_lower: ' ')."^";
			$contents .= (($emp_name!='')?$emp_name: ' ')."^";
			$contents .= ((${'att_date'.$emp_code_lower}!='')?${'att_date'.$emp_code_lower}: ' ')."^";
			$contents .= ((${'att_time'.$emp_code_lower}!='')?${'att_time'.$emp_code_lower}: 'ABSENT')."^";
			$contents .= ((${'check_out_time'.$emp_code_lower}!='')?${'check_out_time'.$emp_code_lower}: '')."^";
			$contents .= (($total_counter_meet!='')?$total_counter_meet: 0)."^";
			$contents .= (($total_mega_mason_meet!='')?$total_mega_mason_meet: 0)."^";
			$contents .= (($total_engineers_meet!='')?$total_engineers_meet: 0)."^";
			$contents .= (($total_professional_meet!='')?$total_professional_meet: 0)."^";
			$contents .= (($total_contractor_meet!='')?$total_contractor_meet: 0)."^";
			$contents .= (($total_dealer_subdealer_meet!='')?$total_dealer_subdealer_meet: 0)."^";
			$contents .= (($total_complaint!='')?$total_complaint: 0)."^";
			$contents .= (($total_mason_meet!='')?$total_mason_meet: 0)."^";
			$contents .= (($total_IHB_meet!='')?$total_IHB_meet: 0)."^";
			$contents .= (($total_small_engineers_meet!='')?$total_small_engineers_meet: 0)."^";
			$contents .= (($total_big_contractor_meet!='')?$total_big_contractor_meet: 0)."^";
			$contents .= (($total_catch_them_young!='')?$total_catch_them_young: 0)."^";
			$contents .= (($total_pc_traning_programme!='')?$total_pc_traning_programme: 0)."^";
			$contents .= (($total_customer_guidance_camp!='')?$total_customer_guidance_camp: 0)."^";
			
			$contents .= (($total_site_visit!='')?$total_site_visit: 0)."^";
			$contents .= (($total_complaint_report!='')?$total_complaint_report: 0)."^";
			$contents .= (($total_dhalai_service!='')?$total_dhalai_service: 0)."^";

            $contents .= (($total_site_tracking_conversion!='')?$total_site_tracking_conversion: 0);

			
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/manager-dashboard-download-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&dateval=$currentdate&timeval=$current_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=manager-dashboard.txt");
	print "$datacontents"; 		
?>
