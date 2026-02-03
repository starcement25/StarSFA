<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ABDOS");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
$sqlquery="SELECT DISTINCT customer_code,order_no FROM prev_order_counting_master order by visit_date ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		while($rowcustomer = mysqli_fetch_assoc($result))
		{
			$order_no=$rowcustomer['order_no'];
			$customer_code=$rowcustomer['customer_code'];
			if(substr($order_no,0,1)=='O'){
				$emp_code=substr($order_no,1,5);
			}
			if(substr($order_no,0,1)=='N'){
				$emp_code=substr($order_no,2,5);
			}
			
			$sqlcustomernamecode="SELECT customer_code,customer_name FROM customer_master WHERE acedns='Y' AND customer_name=
							(SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."') AND route_code=(SELECT route_code FROM customer_master WHERE customer_code='".$customer_code."')";
			$rscustomernamecode=mysqli_query($link,$sqlcustomernamecode);
			$cntcustomernamecode=mysqli_num_rows($rscustomernamecode);
			if($cntcustomernamecode >0)
			{
				$customer_code_name_str='';
				while($rowcustomernamecode = mysqli_fetch_assoc($rscustomernamecode))
				{
					$customer_code_name_str=$customer_code_name_str."'".$rowcustomernamecode['customer_code']."'".',';
					$customer_name=$rowcustomernamecode['customer_name'];
				}
				
				$customer_code_name_str=substr($customer_code_name_str,0,-1);
				$sqlcheckcustomerroute="SELECT customer_code,route_code FROM customer_route_emp_relation WHERE emp_code='".$emp_code."' 
										AND acedns='Y' AND customer_code IN(".$customer_code_name_str.")";
				$rscheckcustomerroute=mysqli_query($link,$sqlcheckcustomerroute);
				$rowcheckcustomerroute=mysqli_fetch_assoc($rscheckcustomerroute);	
				$customer_code_present=$rowcheckcustomerroute['customer_code'];
				$route_code_present=$rowcheckcustomerroute['route_code'];
				if($customer_code_present !=''){
					
					$sqlroute_name="SELECT route_name FROM route_master WHERE route_code='".$route_code_present."'";
					$rsroute_name=mysqli_query($link,$sqlroute_name);
					$rowroute_name=mysqli_fetch_assoc($rsroute_name);
					$route_name_present=$rowroute_name['route_name'];
					
					$sqlupdateprevorder="UPDATE prev_order_counting_master SET 	customer_code	='".$customer_code_present."',
											download_time=CURRENT_TIMESTAMP(),delete_flag='not_deleted',
											customer_code_replaced='".$customer_code."',	route_code_replaced='".$route_code_present."',
											route_name_replaced='".$route_name_present."',customer_name_replaced='".$customer_name."' 
											WHERE order_no='".$order_no."'";
				}
				else
				{
					$sqlupdateprevorder="UPDATE prev_order_counting_master SET download_time=CURRENT_TIMESTAMP(),delete_flag='deleted' 
											WHERE order_no='".$order_no."'";
				}
				mysqli_query($link,$sqlupdateprevorder);						
			}
			//exit();
		}
	  }
	echo 'SUCCESS';
?>
