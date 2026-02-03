<?php
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

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
function return_auto_code($code_prefix,$code_type,$code){
	
	if($code_type=='employee')
	{
		if(strlen($code)=='1')
		{
			$build_code=$code_prefix.'000'.$code;
		}
		if(strlen($code)=='2')
		{
			$build_code=$code_prefix.'00'.$code;
		}
		if(strlen($code)=='3')
		{
			$build_code=$code_prefix.'0'.$code;
		}
	}
	return $build_code;
}

	$upload_dir="csv/$nick_name/";
	if(similar_file_exists("csv/$nick_name/aceDNS_csv.zip")!=false)
	{
		$filename=similar_file_exists("csv/$nick_name/aceDNS_csv.zip");
		$zip = new ZipArchive;
		if ($zip->open($filename)) {
			$zip->extractTo("csv/$nick_name/");
			$zip->close();
		}

		//For Employee CSV
		if(similar_file_exists("csv/$nick_name/Employee Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/Employee Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			/*$sqldelete="truncate employee_master";
			$rsdelete=mysqli_query($link,$sqldelete);
			$sqldeletepassword="truncate changepassword";
			$rsdeletepassword=mysqli_query($link,$sqldeletepassword);*/
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
					
					$employee_name=trim($data[0]);
					$branch_code_name=trim($data[1]);
					$vertical_value=trim($data[2]);
					$reporting_to=trim($data[3]);
					$email=trim($data[4]);
					$phone_no=trim($data[5]);
					$sale_access=trim($data[6]);
					$sale_access='primary';
					$HQ=trim($data[7]);
					
					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

					$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$countempnamechk=mysqli_num_rows($rsempnamechk);
					
					$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')";
					$rsreportingto=mysqli_query($link,$sqlreportingto);
					
					while($rowreportingto=mysqli_fetch_assoc($rsreportingto))
					{
						$reporting_to_val=$rowreportingto['emp_code'];
					}
					//exit();
					
					$csv_row_count=$rec_count+1;
					if($countempnamechk<1)
					{
						$sqlmaxempcode="SELECT MAX(emp_code) AS max_emp_code FROM  employee_master ";
						$rsmaxempcode=mysqli_query($link,$sqlmaxempcode);
						$rowmaxempcode=mysqli_fetch_assoc($rsmaxempcode);
						$max_emp_code=$rowmaxempcode['max_emp_code'];
						if($max_emp_code=='')
						{
							$max_emp_code='E0001';
						}
						else
						{
							$max_emp_code++;
						}
						
						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						$sql .= " , emp_name='".$employee_name."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , vertical_value='".$vertical_value."'";
						$sql .= " , reporting_to='".$reporting_to_val."'";
						$sql .= " , email='".$email."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , sale_access='".$sale_access."'";
						$sql .= " , HQ='".$HQ."'";
     					mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Employee name column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysqli_query($link,$sqlcp) or die(mysqli_error().'.Internal DATA execution problem on password table.PLease contact aceDNS admin.');
					}
					else
					{
						$sqlupdate  = "UPDATE employee_master ";
						$sqlupdate .= " SET branch_code='".$branch_code."'";
						$sqlupdate .= " , vertical_value='".$vertical_value."'";
						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";
						$sqlupdate .= " , email='".$email."'";
						$sqlupdate .= " , sale_access='".$sale_access."'";
						$sqlupdate .= " , HQ='".$HQ."'";
						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_name='".addslashes($employee_name)."'";
     					mysqli_query($link,$sqlupdate) or die(mysqli_error().".Duplicate key @row $csv_row_count on Employee name column in Employee Master.csv.Please check.");
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Employee Master.csv is wrong.";
			exit();
		}*/
		
		//For RDS CSV
		if(similar_file_exists("csv/$nick_name/RDS MASTER.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/RDS MASTER.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			/*$sqlroutedelete="truncate route_master";
			$rsroutedelete=mysqli_query($link,$sqlroutedelete);*/
			
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
					$rdsname	  =trim($data[0]); 
					$emp_code_name =trim($data[1]);
					$rds_type =trim($data[2]);
					
					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=mysqli_fetch_assoc($rsempcode);
					$emp_code=$rowempcode['emp_code'];
					
					$sqlrdsnamechk="SELECT * FROM rds_master WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";
					$rsrdsnamechk=mysqli_query($link,$sqlrdsnamechk);
					$countrdsnamechk=mysqli_num_rows($rsrdsnamechk);
						
					$csv_row_count=$rec_count+1;
					if($countrdsnamechk<1)
					{
						$sqlmaxrdscode="SELECT MAX(rds_code) AS max_rds_code FROM  rds_master WHERE 1";
						$rsmaxrdscode=mysqli_query($link,$sqlmaxrdscode);
						$rowmaxrdscode=mysqli_fetch_assoc($rsmaxrdscode);
						$max_rds_code=$rowmaxrdscode['max_rds_code'];
						
						if($max_rds_code=='')
						{
							$max_rds_code='C/0000001';
						}
						else
						{
							$max_rds_code++;
						}
	
					
						$sqlrds  = "insert into rds_master ";
						$sqlrds .= " SET rds_code='".$max_rds_code."'";
						$sqlrds .= " ,rds_name='".$rdsname."'";
						$sqlrds .= " , emp_code='".$emp_code."'";
						$sqlrds .= " , rds_type='".$rds_type."'";
						$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
					
						mysqli_query($link,$sqlrds) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in rds master.csv.Please check.");;
					}
					else
					{
						$sqlupdated  = "update rds_master ";
						$sqlupdated .= " SET rds_type='".$rds_type."'";
						$sqlupdated .= " WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";
						mysqli_query($link,$sqlupdated) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on rds master.csv.Please check.");
					}
				}
				 $rec_count++;
			}
			$successval=1;
		}
	
		//For Customer CSV
		if(similar_file_exists("csv/$nick_name/Customer Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/Customer Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			
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
				  //print_r($data);
					
					$branch_name	 =trim($data[0]);
					$customer_name   =trim($data[1]);
					$address   		 =trim($data[2]);
					$phone_no   		 =trim($data[3]);
					$route_name	  =trim($data[4]); 
					$emp_name		=trim($data[5]);
					$current_balance =trim($data[6]);
					$credit_limit	=trim($data[7]);
					$acedns		  =trim($data[8]);
					$black_list	  =trim($data[9]); 
					$TD	  		  =trim($data[10]); 
					$cust_type	  	=trim($data[11]);
					$rds_name	  	=trim($data[12]);
					
					if($acedns=='') 
					{
					  $acedns='Y';
					}
					if($black_list=='')
					{
						$black_list='N';
					}
					
					$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE emp_name='".trim($emp_name)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=@mysqli_fetch_assoc($rsempcode);
					$emp_code=$rowempcode['emp_code'];
					$branch_code=$rowempcode['branch_code'];
					
					$sqlrdscode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_name)."' AND emp_code='".$emp_code."'";
					$rsrdscode=mysqli_query($link,$sqlrdscode);
					$rowrdscode=mysqli_fetch_assoc($rsrdscode);
					$rds_code=$rowrdscode['rds_code'];

					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
					$rsroutechk=mysqli_query($link,$sqlroutechk);
					$countroutechk=@mysqli_num_rows($rsroutechk);
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
							//$max_route_code++;
						}
						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,route_name='".addslashes($route_name)."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlroute) or die(mysqli_error().'.Internal DATA execution problem on route table.PLease contact aceDNS admin.');
					}

					
					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".trim($route_name)."' AND emp_code='".$emp_code."'";
					$rsroutecode=mysqli_query($link,$sqlroutecode);
					$rowroutecode=@mysqli_fetch_assoc($rsroutecode);
					$route_code=$rowroutecode['route_code'];
					
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					$csv_row_count=$rec_count+1;
					if($countcustomernamechk<1)
					{
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
						
						$sql  = "insert into customer_master ";
						$sql .= " SET customer_code='".$max_customer_code."'";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , TD='".$TD."'";
						$sql .= " , rds_tag='".$rds_code."'";
						$sql .= " , cust_type='".$cust_type."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Customer name and Employee name columns in customer master.csv.Please check.");
					}
					else
					{
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$emp_code_db=$rowcustomernamechk['emp_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$TD_db=$rowcustomernamechk['TD'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						$rds_tag_db=$rowcustomernamechk['rds_tag'];
						$branch_code_db=$rowcustomernamechk['branch_code'];
						
						if(($credit_limit_db==$credit_limit) && ($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD || $customer_type_db!=$cust_type || $rds_tag_db!=$rds_tag || $branch_code_db!=$branch_code))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , branch_code='".$branch_code."'";
							$sqlupdated .= " , TD='".$TD."'";
							$sqlupdated .= " , cust_type='".$cust_type."'";
							$sqlupdated .= " , rds_tag='".$rds_code."',download_time=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
							mysqli_query($link,$sqlupdated) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
						/*elseif(($credit_limit_db==$credit_limit)&&($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , black_list='".$black_list."',download_time=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
							mysqli_query($link,$sqlupdated) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}*/
						elseif(($credit_limit_db!=$credit_limit))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET credit_limit='".$credit_limit."',download_time_credit_limit=CURRENT_TIMESTAMP()";
							$sqlupdated .= " WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
							mysqli_query($link,$sqlupdated) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
					}
				}
				 $rec_count++;
				 $countroute++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}*/
		
		//For Vendor Master CSV
		if(similar_file_exists("csv/$nick_name/Vendor master.csv")!=false && sale=='yes')
		{
			$filename=similar_file_exists("csv/$nick_name/Vendor master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
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
				  
					$vendor_name=trim($data[0]);
					$emp_code_name=trim($data[1]);
					$rds_code_name=trim($data[2]);
					$branch_code_name=trim($data[3]);
					
					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
					$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
					$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
					$branch_code=$rowbranchnamechk['branch_code'];
					
					$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);
					$emp_code=$rowempnamechk['emp_code'];
	
					$sqlrdscode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_code_name)."' AND emp_code='".$emp_code."'";
					$rsrdscode=mysqli_query($link,$sqlrdscode);
					$rowrdscode=mysqli_fetch_assoc($rsrdscode);
					$rds_code=$rowrdscode['rds_code'];
					
					$sqlvendornamechk="SELECT vendor_code FROM vendor_master WHERE vendor_name='".addslashes($vendor_name)."' 
										AND rds_code='".$rds_code."'";
					$rsvendornamechk=mysqli_query($link,$sqlvendornamechk);
					$countvendornamechk=mysqli_num_rows($rsvendornamechk);
					
					$csv_row_count=$rec_count+1;
					if($countvendornamechk<1)
					{
						$sqlmaxvendorcode="SELECT MAX(vendor_code) AS max_vendor_code FROM  vendor_master WHERE 1";
						$rsmaxvendorcode=mysqli_query($link,$sqlmaxvendorcode);
						$rowmaxvendorcode=mysqli_fetch_assoc($rsmaxvendorcode);
						$max_vendor_code=$rowmaxvendorcode['max_vendor_code'];
						
						if($max_vendor_code=='')
						{
							$max_vendor_code='V0001';
						}
						else
						{
							$max_vendor_code++;
						}
	
						$sqlvendor  = "insert into vendor_master SET ";
						$sqlvendor .= "  vendor_code='".mysqli_real_escape_string($max_vendor_code)."'";
						$sqlvendor .= "  ,vendor_name='".mysqli_real_escape_string($vendor_name)."'";
						$sqlvendor .= " , branch_code='".mysqli_real_escape_string($branch_code)."'";
						$sqlvendor .= " , rds_code='".mysqli_real_escape_string($rds_code)."'";
						$sqlvendor .= " , emp_code='".mysqli_real_escape_string($emp_code)."'";
						mysqli_query($link,$sqlvendor) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Vendor master.csv.Please check.");
					}
					else
					{
						$sqlupdatevendor  = "UPDATE vendor_master SET ";
						$sqlupdatevendor .= "  branch_code='".mysqli_real_escape_string($branch_code)."'";
						$sqlupdatevendor .= " , emp_code='".mysqli_real_escape_string($emp_code)."'";
						$sqlupdatevendor .= " WHERE vendor_name='".mysqli_real_escape_string($vendor_name)."' AND 
											rds_code='".mysqli_real_escape_string($rds_code)."'";
						mysqli_query($link,$sqlupdatevendor) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Vendor master.csv.Please check.");
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Vendor master.csv is wrong.";
			exit();
		}	*/

		//For Sku CSV
		if(similar_file_exists("csv/$nick_name/SKU Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/SKU Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$lines=str_replace('"','~',$lines);


			/*$sku_code='12001';*/
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
				  
					$branch_name=trim($data[0]);
					$product_group_name=trim($data[1]);
					$prod_desc=trim($data[2]);
					$prod_desc=str_replace('~','"',$prod_desc);
					$cl_stk=trim($data[3]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					$UOM1=trim($data[4]);
					$UOM2=trim($data[5]);
					$acedns=trim($data[6]);
					$black_list=trim($data[7]);
					$vertical_value=trim($data[8]);
					
					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."'";
					$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
					$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
					$branch_code=$rowbranchnamechk['branch_code'];
					$csv_row_count=$rec_count+1;
					
					$sqlprodgroupnamechk="SELECT product_group_name FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";
					$rsprodgroupnamechk=mysqli_query($link,$sqlprodgroupnamechk);
					$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);
					
					if($countprodgroupnamechk<1){
						$sqlbrand  = "insert into product_group_master SET ";
						$sqlbrand .= "  product_group_code='".mysqli_real_escape_string($product_group_name)."'";
						$sqlbrand .= " , product_group_name='".mysqli_real_escape_string($product_group_name)."'";
						$sqlbrand .= " , vertical_value='".$vertical_value."'";
						$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrand) or die(mysqli_error().".Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
					}
					
					$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."' AND 
									product_group_code='".addslashes($product_group_name)."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$countskunamechk=mysqli_num_rows($rsskunamechk);
					$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
					
					if($countskunamechk<1)
					{
						$sqlmaxskucode="SELECT MAX(prod_code) AS max_prod_code FROM  product_master WHERE 1";
						$rsmaxskucode=mysqli_query($link,$sqlmaxskucode);
						$rowmaxskucode=mysqli_fetch_assoc($rsmaxskucode);
						$max_prod_code=$rowmaxskucode['max_prod_code'];
						
						if($max_prod_code=='')
						{
							$max_prod_code='12001';
						}
						else
						{
							$max_prod_code++;
						}
						$sql  = "insert into product_master ";
						$sql .= " SET prod_code='".$max_prod_code."'";
						$sql .= " , prod_desc='".addslashes($prod_desc)."'";
						$sql .= " , branch_code='".mysqli_real_escape_string($branch_code)."'";
						$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_name)."'";
						$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , UOM1='".addslashes($UOM1)."'";
						$sql .= " , UOM2='".addslashes($UOM2)."'";
						$sql .= " , vertical_value='".$vertical_value."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						$sql .= " , download_time_cl_stk=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on SKU Name column in sku master.csv.Please check.");
					}
					else
					{
						$cl_stk_db=$rowskunamechk['cl_stk'];
						$acedns_db=$rowskunamechk['acedns'];
						$black_list_db=$rowskunamechk['black_list'];
						$prod_code_db=$rowskunamechk['prod_code'];
						$product_group_code_db=$rowskunamechk['product_group_code'];
						$UOM1_db=$rowskunamechk['UOM1'];
						$UOM2_db=$rowskunamechk['UOM2'];
						$branch_code_db=$rowskunamechk['branch_code'];
						$vertical_value_db=$rowskunamechk['vertical_value'];
						
						if(($cl_stk_db==$cl_stk) &&($acedns_db!=$acedns || $black_list_db!=$black_list || $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code || $UOM1_db!=$UOM1 || $UOM2_db!=$UOM2 || $branch_code_db!=$branch_code || $vertical_value_db!=$vertical_value))
						{
							$sqlupdatestock="UPDATE product_master SET acedns='".$acedns."',
											product_group_code='".$product_group_code."',
											UOM1				  ='".addslashes($UOM1)."',
											UOM2				  ='".addslashes($UOM2)."',
											branch_code			  ='".$branch_code."',
											vertical_value		  ='".$vertical_value."',
											download_time=CURRENT_TIMESTAMP(),
											black_list='".$black_list."' WHERE prod_desc='".addslashes($prod_desc)."'";
							mysqli_query($link,$sqlupdatestock) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on sku master.csv.Please check.");
						}
						/*elseif(($cl_stk_db==$cl_stk)&&($acedns_db!=$acedns || $black_list_db!=$black_list || $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code))
						{
							$sqlupdatestock="UPDATE product_master SET acedns='".$acedns."',
											product_group_code='".$product_group_code."',
											product_sub_group_code='".$product_sub_group_code."',
											download_time=CURRENT_TIMESTAMP(),
											black_list='".$black_list."' WHERE prod_desc='".addslashes($prod_desc)."'";
							mysqli_query($link,$sqlupdatestock) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on sku master.csv.Please check.");

						}*/
						elseif(($cl_stk_db!=$cl_stk))
						{
							$sqlupdatestock="UPDATE product_master SET cl_stk='".$cl_stk."',
											download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_desc='".addslashes($prod_desc)."'";
							mysqli_query($link,$sqlupdatestock) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on sku master.csv.Please check.");

						}
					}
				}
				 $rec_count++;
			}
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for SKU Master.csv is wrong.";
			exit();
		}*/
		
		//For Outstanding CSV
		if(similar_file_exists("csv/$nick_name/Outstanding.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/Outstanding.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$sqldelete="truncate outstanding";
			$rsdelete=mysqli_query($link,$sqldelete);
	
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
				  
					$customer_name=trim($data[0]);
					
					//$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."' 
									and download_time=(SELECT MAX(download_time) FROM customer_master WHERE customer_name='".addslashes($customer_name)."')";
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
	
					$invoice_id=trim($data[1]);

					$date=trim($data[2]);
					$dateArr=explode('/',$date);
					$finaldate=$dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
					$invoice_amount=trim($data[3]);
					if(strpos($invoice_amount,',')!=false){
						$invoicepos=strpos($invoice_amount,',');
					$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);
					}
					$due_amount=trim($data[4]);
					if(strpos($due_amount,',')!=false){
					$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);
					}
					$sql  = "insert into outstanding ";
					$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";
					$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";
					$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";
					
					mysqli_query($link,$sql) or die(mysqli_error());
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Outstanding.csv is wrong.";
			exit();
		}*/
		
		//For Mrp CSV
		if(similar_file_exists("csv/$nick_name/MRP.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/MRP.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$lines=str_replace('"','~',$lines);
			/*$sqldelete="truncate mrp";
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
				  
					$prod_desc=trim($data[0]);
					$prod_desc=str_replace('~','"',$prod_desc);
					$mrp=trim($data[1]);
					$sale_rate=trim($data[2]);
					if(strpos($sale_rate,',')!=false){
						$salepos=strpos($sale_rate,',');
					$sale_rate = substr($sale_rate,0,$salepos).substr(strstr($sale_rate, ","),1);
					}
					
					$sqlskunamechk="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
					$prod_code=$rowskunamechk['prod_code'];
					
					$sqlmrpchk="SELECT product_code,mrp,sale_rate FROM mrp WHERE product_code='".$prod_code."'";
					$rsmrpchk=mysqli_query($link,$sqlmrpchk);
					$countmrpchk=mysqli_num_rows($rsmrpchk);
					$csv_row_count=$rec_count+1;
					if($countmrpchk<1){
						$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
						$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
						$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
						$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
						
						if($max_mrp_code=='')
						{
							$max_mrp_code='001';
						}
						else
						{
							$max_mrp_code++;
						}
						$max_mrp_code='z'.$max_mrp_code;
						$sql  = "insert into mrp ";
						$sql .= " SET product_code='".$prod_code."'";
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , mrp='".mysqli_real_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Mrp.csv.Please check.");
					}
					else
					{
						$rowmrpchk=mysqli_fetch_assoc($rsmrpchk);
						$mrp_db=$rowmrpchk['mrp'];
						$sale_rate_db=$rowmrpchk['sale_rate'];	
						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate){
							$sqlupdate  = "UPDATE mrp ";
							$sqlupdate .= " SET mrp='".mysqli_real_escape_string($mrp)."'";
							$sqlupdate .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."'";
							mysqli_query($link,$sqlupdate) or die(mysqli_error().".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
						}
					}

				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for MRP.csv is wrong.";
			exit();
		}*/	
		if($successval==1)
		{
			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlInsert))
			{
				echo $err = 'Zip file extracted and data has been uploaded successfully';
				$curdateserver=gmdate('Y-m-d H:i:s',strtotime('+330 minute'));
			
				$url="http://www.acedns.in/acednsproduct/mailDatabaseDetails.php?nick_name=$nick_name";
				$url = str_replace(" ", '%20', $url);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 20);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
				$response = json_decode(curl_exec($ch));
			}
		}
	} 
?>
