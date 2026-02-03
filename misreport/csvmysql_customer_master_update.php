<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_PARLET");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$count = 1;
$record = 1;
$array = array();
$filename = 'Customer Master-parle.csv';
$file = fopen($filename,"r");
while(! feof($file))
{
	$file1=fgetcsv($file);
	
	if($count != 1){
		/*echo "<pre>";
		print_r($file1);
		echo "</pre>";
		die;*/
		
		$customer_code = $file1[0];
		$customer_name = $file1[1];
		$phone_no = $file1[2];
		$route_code = $file1[3];
		$route_name = $file1[4];
		$employee_code = $file1[5];
		$AceDNS = $file1[6];
		$Credit_Limit = $file1[7];
		$Credit_days = $file1[8];
		$Current_Balance = $file1[9];
		$Black_List = $file1[10];
		$TD = $file1[11];
		$Branch_code = $file1[12];
		$Customer_Type = $file1[13];
		$rds_code = $file1[14];
		$sauda_validity_period = $file1[15];
		$Cust_address = $file1[16];
		$owner_name = $file1[17];
		$owner_phone = $file1[18];
		$cust_class = $file1[19];
		$weekly_closing_date = $file1[20];
		$coverage_type = $file1[21];
		$TIN = $file1[22];
		$PAN = $file1[23];
		
		if($customer_name == '')
		break;
		
		$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
        $rsroutechk=mysqli_query($link,$sqlroutechk);
        $countroutechk=mysqli_num_rows($rsroutechk);
        if($countroutechk<1)
         {
			$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master";
			$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
			$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
			$new_route_code=$rowmaxroutecode['new_route_code'];
			
			if($new_route_code=='')
			{
				$max_route_code='RT/1';
			}
			else
			{
				$max_route_code='RT/'.($new_route_code+1);
			}
			$sqlroute  = "insert into route_master ";
			$sqlroute .= " SET route_code='".$max_route_code."'";
			$sqlroute .= " ,dns_route_code=''";
			$sqlroute .= " ,route_name='".$route_name."'";
			$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlroute); 
			$route_code=$max_route_code;              
		 }
		 else
		 {
			$rowroutechk=mysqli_fetch_assoc($rsroutechk); 
			$route_code=$rowroutechk['route_code'];
		 }
		
		
		$sql_customer_name_exist = "SELECT customer_code FROM customer_master WHERE customer_name = '".$customer_name."'";
		$res_customer_name_exist = mysqli_query($link,$sql_customer_name_exist);
		$total_row_exist_check = mysqli_num_rows($res_customer_name_exist);
		
		if($total_row_exist_check>0){
			$res_customer_name_exist = mysqli_query($link,$sql_customer_name_exist);
			$row_customer_name_exist = mysqli_fetch_assoc($res_customer_name_exist);
			$customer_code = $row_customer_name_exist['customer_code'];
			
			$sql = "UPDATE customer_master SET 
					`address` = '".$Cust_address."', 
					`route_code` = '".$route_code."', 
					`emp_code` = '".$employee_code."', 
					`acedns` = '".$AceDNS."', 
					`black_list` = '".$Black_List."', 
					`TD` = '".$TD."', 
					`cust_type` = '".$Customer_Type."', 
					`rds_tag` = '".$rds_code."', 
					`download_time` = current_timestamp
			   WHERE customer_code = '".$customer_code."'";
			   
		}
		else{
			$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
                        $rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
                        $rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
                        $max_customer_code=$rowmaxcustomercode['max_customer_code'];
                        
                        if($max_customer_code=='')
                        {
                            $max_customer_code='C/0000001';
                        }
                        else
                        {
                            $max_customer_code++;
                        }
						
			$sql = "INSERT INTO customer_master SET 
					`customer_code` = '".$max_customer_code."', 
					`dns_customer_code` = '', 
					`customer_name` = '".$customer_name."', 
					`address` = '".$Cust_address."', 
					`route_code` = '".$route_code."', 
					`emp_code` = '".$employee_code."', 
					`acedns` = '".$AceDNS."', 
					`black_list` = '".$Black_List."', 
					`TD` = '".$TD."', 
					`cust_type` = '".$Customer_Type."', 
					`rds_tag` = '".$rds_code."', 
					`download_time` = current_timestamp";
		}
		mysqli_query($link,$sql); 
}
	$count++;
}
echo "Updated Successfully";
mysqli_close($link);
?>