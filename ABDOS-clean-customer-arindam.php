<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ABDOS");
$link=mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB,$link);
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
	if(similar_file_exists("customer master new one arindam.csv")!=false)
	{
		$filename=similar_file_exists("customer master new one arindam.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		/*$sqldelete="truncate branch_master";
		$rsdelete=mysqli_query($link,$sqldelete);*/
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
			  //print_r($data);
			  
				$new_customer_code=trim($data[0]);
				$customer_name=trim($data[1]);
				$route_name=trim($data[4]);
				$check_type=trim($data[5]);
				$emp_code='E0017';
				echo $sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_name."'";
				$rsroutecode=mysqli_query($link,$sqlroutecode);
				$rowroutecode=mysqli_fetch_assoc($rsroutecode);
				$route_code=$rowroutecode['route_code'];
				echo $sqlreplacecustomercode="SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRER WHERE 
										CM.customer_name='".addslashes($customer_name)."' AND CRER.route_code='".$route_code."' AND 
									CM.customer_code=CRER.customer_code AND CRER.emp_code='E0017' AND CM.acedns='Y'";
				$rsreplacecustomercode=mysqli_query($link,$sqlreplacecustomercode);
				$rowreplacecustomercode=mysqli_fetch_assoc($rsreplacecustomercode);
				$replacing_customer_code=$rowreplacecustomercode['customer_code'];
				echo $sqlcustomercode="SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRER WHERE 
										CM.customer_name='".addslashes($customer_name)."' AND CRER.route_code='".$route_code."' AND 
									CM.customer_code=CRER.customer_code AND CRER.emp_code='E0017' AND CM.acedns='N'";
				$rscustomercode=mysqli_query($link,$sqlcustomercode);
				$cntchkcustomercode=mysqli_num_rows($rscustomercode);
				while($rowcustomercode=mysqli_fetch_assoc($rscustomercode)){
				
				$customer_code=$rowcustomercode['customer_code'];
				$customer_acedns=$rowcustomercode['acedns'];
				
				/*echo $sqlchkcustomerroute="SELECT customer_code FROM customer_route_emp_relation WHERE customer_code='".$customer_code."' AND 
									route_code='".$route_code."' AND emp_code='E0017'";
				$rschkcustomerroute=mysqli_query($link,$sqlchkcustomerroute);	
				$cntchkcustomerroute=mysqli_num_rows($rschkcustomerroute);*/	
				if($customer_acedns=='N')
				{
					echo $sqldeletecustomer="DELETE FROM customer_master WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqldeletecustomer);
					
					echo $sqldeletecustomerroute="DELETE FROM customer_route_emp_rerlation WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqldeletecustomerroute);
					
					echo $sqlprevorder  = "UPDATE prev_order_counting_master SET customer_code='".$replacing_customer_code."' WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqlprevorder);
					
					echo $sqlorder  = "UPDATE order_header SET customer_code='".$replacing_customer_code."' WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqlorder);

					echo $sqlaudit  = "UPDATE stock_audit SET customer_code='".$replacing_customer_code."' WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqlaudit);
					//exit();
				}
				}
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Branch master.csv is wrong.";
		exit();
	}*/

?>