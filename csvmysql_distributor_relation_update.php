<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
//define("DB","acedns_PARLET");
define("DB","acedns_UCLINDIA");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
$folderName='UCLINDIA';

if(similar_file_exists("csv/$folderName/store_N.csv")!=false)
{
	$filename=similar_file_exists("csv/$folderName/store_N.csv");
	$rec_count = 0;
	$ins_count = 0;
	$err = "";
		$lines = file($filename);
		//print_r($lines);
		$countroute=0;
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
			
					if(!$double_coute_found && $char=="\"")
					{  
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;
		
		$dns_customer_code =trim($data[0]);
		$customer_name	=trim($data[1]);
				
		$sqlupdated  = "update customer_master ";
		$sqlupdated .= " SET acedns='N'";
		echo $sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() WHERE  dns_customer_code='".$dns_customer_code."' 
							AND customer_name='".$customer_name."'";
		mysqli_query($link,$sqlupdated);
	}
	$rec_count++;
  }
}
exit();
	if(similar_file_exists("csv/$folderName/distributor_correction.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/distributor_correction.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
			$lines = file($filename);
			//print_r($lines);
			$countroute=0;
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   $data[]=$value;
		
		$dns_customer_code =trim($data[0]);
		$customer_name	=trim($data[1]);
		$route_name	  =trim($data[2]); 
		$address	  =trim($data[3]); 
		$phone_no	  =trim($data[4]); 
		$email=  trim($data[5]);
		$emp_code_name		=trim($data[7]);
		$emp_code_name_array=explode(',',$emp_code_name);
		
		
		$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
        $rsroutechk=mysqli_query($link,$sqlroutechk);
        $countroutechk=mysqli_num_rows($rsroutechk);
        if($countroutechk<1)
         {
			$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
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
			$sqlroute .= " ,dns_route_code='".$route_name."'";
			$sqlroute .= " ,route_name='".$route_name."'";
			echo $sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlroute); 
			$route_code=$max_route_code;              
		 }
		 else
		 {
			$rowroutechk=mysqli_fetch_assoc($rsroutechk); 
			$route_code=$rowroutechk['route_code'];
		 }
		 /*$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."'";
			$rsrdscode=mysqli_query($link,$sqlrdscode);
			$rowrdscode=mysqli_fetch_assoc($rsrdscode);
			$rds_code=$rowrdscode['customer_code'];*/
		
		if($dns_customer_code!='')
		{
			$sql_customer_name_exist = "SELECT customer_code FROM customer_master WHERE dns_customer_code = '".$dns_customer_code."'";
			$res_customer_name_exist = mysqli_query($link,$sql_customer_name_exist);
			$total_row_exist_check = mysqli_num_rows($res_customer_name_exist);
			
			if($total_row_exist_check>0){
				$res_customer_name_exist = mysqli_query($link,$sql_customer_name_exist);
				$row_customer_name_exist = mysqli_fetch_assoc($res_customer_name_exist);
				$customer_code = $row_customer_name_exist['customer_code'];
				
					$sqlupdated  = "update customer_master ";
					$sqlupdated .= " SET route_code='".$route_code."'";
					$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";
					$sqlupdated .= " , phone_no='".$phone_no."'";
					$sqlupdated .= " , address='".$address."'";
					echo $sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() WHERE  customer_code='".$customer_code."' ";
					mysqli_query($link,$sqlupdated);
					
					echo $sqlupdatecustomerroute="DELETE FROM customer_route_emp_relation WHERE 
												customer_code='".$customer_code."'";
					if(mysqli_query($link,$sqlupdatecustomerroute))
					{	
					foreach($emp_code_name_array as $emp_code_name_value_next)
					{
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						//exit();
						$branch_code=$rowempcode['branch_code'];
						echo $sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',
												route_code='".$route_code."',
												emp_code='".$emp_code."',
												acedns='Y',
												download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertcustomerroute);	
					}
				}
			}
		/*else
		{
					$sql  = "insert into customer_master ";
					$sql .= " SET customer_code='".$dns_customer_code."'";
					$sql .= " , dns_customer_code=''";
					$sql .= " , customer_name='".addslashes($customer_name)."'";
					$sql .= " , branch_code='".addslashes($branch_code)."'";
					$sql .= " , phone_no='".$phone_no."'";
					$sql .= " , route_code='".$route_code."'";
					$sql .= " , emp_code='".$emp_code."'";
					$sql .= " , current_balance	='".$current_balance."'";
					$sql .= " , credit_limit='".$credit_limit."'";
					$sql .= " , credit_days='".$credit_days."'";
					$sql .= " , acedns='".$acedns."'";
					$sql .= " , black_list='".$black_list."'";
					$sql .= " , TD='".$TD."'";
					$sql .= " , rds_tag='".$rds_code."'";
					$sql .= " , cust_type='".$customer_type."'";
					$sql .= " , sauda_validity_period='".$sauda_validity_period."'";
					$sql .= " , address='".$address."'";
					$sql .= " , owner_name='".$owner_name."'";
					$sql .= " , owner_phone='".$owner_phone."'";
					$sql .= " , cust_class='".$cust_class."'";
					$sql .= " , weekly_closing_day='".$weekly_closing_day."'";
					$sql .= " , TIN='".$TIN."'";
					$sql .= " , PAN='".$PAN."'";
					$sql .= " , district='".$district."'";
					$sql .= " , download_time=CURRENT_TIMESTAMP()";
					//exit();
					mysqli_query($link,$sql);
					$customer_code=$dns_customer_code;

		}*/
		
		/*$sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$rds_code."',
								route_code='".$route_code."',emp_code='".$emp_code."'";
	   $rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);
	   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);
	   if($countchkdistributorroute==0)
	   {						
		   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$rds_code."',
									route_code='".$route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";
		   $rsinsertdistributorroute=mysqli_query($link,$sqlinsertdistributorroute);
	   }*/
		}
	}
	$rec_count++;
  }
}
echo "Updated Successfully";
?>