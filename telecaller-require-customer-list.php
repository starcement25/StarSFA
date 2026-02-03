<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

	$employee_hierarchy=return_employee_hierarchy($emp_code);
	//$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';

	$sql_customer_list = "SELECT customer_name,mobile_no,demo_tentative_date_time,'TENT' as type,SUBSTRING(tent_form_id,3,5) as emp_code,DATE_FORMAT(SUBSTRING(tent_form_id,-14,14),'%d-%m-%Y %H:%i:%s') as date_time 
						FROM tent_form_details WHERE interested_for_demo='no' AND mobile_no NOT IN(SELECT DISTINCT mobile FROM telecaller_form_details WHERE demo_achieved='yes') AND  SUBSTRING(tent_form_id,3,5) IN(".$employee_hierarchy.") 
						UNION
						SELECT customer_name,mobile_no,demo_tentative_date_time,'KNOCKING' as type,SUBSTRING(knocking_form_id,3,5) as emp_code,DATE_FORMAT(SUBSTRING(knocking_form_id,-14,14),'%d-%m-%Y %H:%i:%s') as date_time 
						FROM knocking_form_details WHERE interested_for_demo='no' AND mobile_no NOT IN(SELECT DISTINCT mobile FROM telecaller_form_details WHERE demo_achieved='yes') AND SUBSTRING(knocking_form_id,3,5) IN(".$employee_hierarchy.") ORDER BY demo_tentative_date_time ASC";
	$res_customer_list = mysqli_query($link,$sql_customer_list);
	$count=mysqli_num_rows($res_customer_list);
	//print_r($vertical_array);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		while($row_customer_list = mysqli_fetch_assoc($res_customer_list)){
			$customer_name = $row_customer_list['customer_name'];
			$mobile_no=$row_customer_list['mobile_no'];
			$type=$row_customer_list['type'];
			$emp_code=$row_customer_list['emp_code'];
			$date_time=$row_customer_list['date_time'];
			
			$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];
						
			$sqlproduct="SELECT DISTINCT product,brand,life_of_product FROM  tent_form_product_details WHERE mobile_no='".$mobile_no."'  
							UNION
						SELECT DISTINCT product,brand,life_of_product FROM  knocking_form_product_details WHERE mobile_no='".$mobile_no."'
						ORDER BY product ASC";
			$rsproduct=mysqli_query($link,$sqlproduct);
			$countprod=mysqli_num_rows($rsproduct);
			$res_prod_data=array();
				while($rowprod=mysqli_fetch_assoc($rsproduct))
				{
					 $product=$rowprod['product'];
					 $brand=$rowprod['brand'];
					 $life_of_product=$rowprod['life_of_product'];
					 $res_prod_data[] = array("product"=>$product,"brand"=>$brand,"life_of_product"=>$life_of_product);
				}
				$res_data[] = array("customer_name"=>$customer_name,"mobile_no"=>$mobile_no,"emp_name"=>$emp_name,"date_time"=>$date_time,"source_type"=>$type,"prod_data"=>$res_prod_data);

			}

			$countcolumns='6';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Some thing went wrong");
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/telecaller-require-customer-list.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
