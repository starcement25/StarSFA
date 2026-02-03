<?php
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
$nick_name='SMOTO';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	
	require("include/config-setup.php");
	
	define("DB","acedns_$nick_name");
	
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
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
		/*$zip = new ZipArchive;
		if ($zip->open($filename)) {
			$zip->extractTo("csv/$nick_name/");
			$zip->close();
		}*/
		//For Brand Master CSV
		if(similar_file_exists("csv/$nick_name/Brand Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/Brand Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			/*$sqldelete="truncate product_group_master";
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
				  
					$product_group_name=trim($data[0]);
					$csv_row_count=$rec_count+1;
					
					$sqlprodgroupnamechk="SELECT product_group_name FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";
					$rsprodgroupnamechk=mysqli_query($link,$sqlprodgroupnamechk);
					$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);
					
					if($countprodgroupnamechk<1){
						$sqlbrand  = "insert into product_group_master SET ";
						$sqlbrand .= "  product_group_code='".mysqli_escape_string($product_group_name)."'";
						$sqlbrand .= " , product_group_name='".mysqli_escape_string($product_group_name)."'";
						$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrand) or die(mysqli_error().".Duplicate key @row $csv_row_count on Brand name column in Brand Master.csv.Please check.");
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Brand Master.csv is wrong.";
			exit();
		}*/
		
		//For Brand Form Master CSV
		if(similar_file_exists("csv/$nick_name/Brand Form Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/Brand Form Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			/*$sqldelete="truncate product_sub_group_master";
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
				  	$product_group_name=trim($data[0]);
					$product_sub_group_name=trim($data[1]);
					
					$csv_row_count=$rec_count+1;
					$sqlprodsubgroupnamechk="SELECT product_sub_group_name,product_group_code FROM product_sub_group_master 
											WHERE product_sub_group_name='".addslashes($product_sub_group_name)."' 
											AND product_group_code='".addslashes($product_group_name)."'";
					$rsprodsubgroupnamechk=mysqli_query($link,$sqlprodsubgroupnamechk);
					$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);
					
					if($countprodsubgroupnamechk<1){
						$sqlbrandform  = "insert into product_sub_group_master SET ";
						$sqlbrandform .= "  product_sub_group_code='".mysqli_escape_string($product_sub_group_name)."'";
						$sqlbrandform .= " , product_sub_group_name='".mysqli_escape_string($product_sub_group_name)."'";
						$sqlbrandform .= " , product_group_code='".mysqli_escape_string($product_group_name)."'";
						$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrandform) or die(mysqli_error().".Duplicate key @row $csv_row_count on Brand name and Brand form name columns in Brand Form Master.csv.Please check.");
					}
					else
					{
						$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);
						$product_group_name_existing=$rowprodsubgroupnamechk['product_group_code'];
						if($product_group_name_existing!=$product_group_name)
						{
							$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";
							$sqlupdatebrandform .= " , product_group_code='".mysqli_escape_string($product_group_name)."'";
							$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP()";
							$sqlupdatebrandform .= "  WHERE product_sub_group_name='".addslashes($product_sub_group_name)."' AND product_group_code='".addslashes($product_group_name)."'";
							mysqli_query($link,$sqlupdatebrandform) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Brand Form Master.csv.Please check.");
						}
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
	/*else
		{
			echo $successval="Naming convention for Brand Form Master.csv is wrong.";
			exit();
		}*/

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
					
					$sqlempnamechk="SELECT emp_name FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$countempnamechk=mysqli_num_rows($rsempnamechk);
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

						$csv_row_count=$rec_count+1;
						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						$sql .= " , emp_name='".$employee_name."'";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Employee name column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysqli_query($link,$sqlcp) or die(mysqli_error().'.Internal DATA execution problem on password table.PLease contact aceDNS admin.');
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
		
		//For Route CSV
		if(similar_file_exists("csv/$nick_name/Route Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/Route Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			/*$sqldelete="truncate customer_master";
			$rsdelete=mysqli_query($link,$sqldelete);
			$sqlroutedelete="truncate route_master";
			$rsroutedelete=mysqli_query($link,$sqlroutedelete);*/
			$sqldeletetemp="truncate route_master";
			$rsdeletetemp=mysqli_query($link,$sqldeletetemp);
			
			/*$sqlcustomerchk="SELECT COUNT(*) FROM customer_master";
			$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
			$countcustomerchk=mysqli_num_rows($rscustomerchk);*/
			
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
					
					$route_name	  =trim($data[0]); 
					$emp_name		=trim($data[1]);
					
					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_name)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=mysqli_fetch_assoc($rsempcode);
					$emp_code=$rowempcode['emp_code'];

					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
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
							//$max_route_code++;
						}
						$csv_row_count=$rec_count+1;
						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						
						mysqli_query($link,$sqlroute) or die(mysqli_error().".Duplicate key @row $csv_row_count on Route name and Employee name columns in Route Master.csv.Please check.");
					}
				}
				$rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Route Master.csv is wrong.";
			exit();
		}*/

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
					
					$customer_name	=trim($data[0]);
					$route_name	  =trim($data[1]); 
					$emp_name		=trim($data[2]);
					$acedns		  =trim($data[3]);
					$current_balance =trim($data[4]);
					$credit_limit	=trim($data[5]);
					$black_list	  =trim($data[6]); 
					
					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_name)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=mysqli_fetch_assoc($rsempcode);
					$emp_code=$rowempcode['emp_code'];

					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
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
							//$max_route_code++;
						}
						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlroute) or die(mysqli_error().'.Internal DATA execution problem on route table.PLease contact aceDNS admin.');
					}
					
					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".trim($route_name)."' AND emp_code='".$emp_code."'";
					$rsroutecode=mysqli_query($link,$sqlroutecode);
					$rowroutecode=mysqli_fetch_assoc($rsroutecode);
					$route_code=$rowroutecode['route_code'];
					
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					$csv_row_count=$rec_count+1;
					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE 1";
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
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
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
						
						if(($credit_limit_db==$credit_limit) && ($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , credit_limit='".$credit_limit."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , black_list='".$black_list."',download_time=CURRENT_TIMESTAMP() 
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
		
		
		//For Sku CSV
		if(similar_file_exists("csv/$nick_name/SKU Master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$nick_name/SKU Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			//$lines=str_replace('"','~',$lines);


			$sku_code='12000';
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
					//$prod_desc=str_replace('~','"',$prod_desc);
					
					$product_group_code=trim($data[1]);
					$product_sub_group_code=trim($data[2]);
					$cl_stk=trim($data[4]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					$acedns=trim($data[5]);
					$black_list=trim($data[6]);
					
					/*$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."' AND 
									product_group_code='".addslashes($product_group_code)."' AND product_sub_group_code='".addslashes($product_sub_group_code)."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$countskunamechk=mysqli_num_rows($rsskunamechk);
					$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
					
					$csv_row_count=$rec_count+1;
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
						}*/
						$sql  = "insert into product_master ";
						$sql .= " SET prod_code='".$sku_code."'";
						$sql .= " , prod_desc='".mysqli_escape_string($prod_desc)."'";
						$sql .= " , product_group_code='".mysqli_escape_string($product_group_code)."'";
						$sql .= " , product_sub_group_code='".mysqli_escape_string($product_sub_group_code)."'";
						$sql .= " , cl_stk='".mysqli_escape_string($cl_stk)."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on SKU Name column in sku master.csv.Please check.");
					/*}
					else
					{
						$cl_stk_db=$rowskunamechk['cl_stk'];
						$acedns_db=$rowskunamechk['acedns'];
						$black_list_db=$rowskunamechk['black_list'];
						$prod_code_db=$rowskunamechk['prod_code'];
						$product_group_code_db=$rowskunamechk['product_group_code'];
						$product_sub_group_code_db=$rowskunamechk['product_sub_group_code'];
						
						if(($cl_stk_db==$cl_stk) &&($acedns_db!=$acedns || $black_list_db!=$black_list || $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code))
						{
							$sqlupdatestock="UPDATE product_master SET acedns='".$acedns."',
											product_group_code='".$product_group_code."',
											product_sub_group_code='".$product_sub_group_code."',
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
						/*elseif(($cl_stk_db!=$cl_stk))
						{
							$sqlupdatestock="UPDATE product_master SET cl_stk='".$cl_stk."',
											download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_desc='".addslashes($prod_desc)."'";
							mysqli_query($link,$sqlupdatestock) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on sku master.csv.Please check.");

						}*/
					//}
				}
				$sku_code++;
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
					
					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
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
					$sql .= " SET customer_code='".mysqli_escape_string($customer_code)."'";
					$sql .= " , invoice_id='".mysqli_escape_string($invoice_id)."'";
					$sql .= " , date='".mysqli_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysqli_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysqli_escape_string($due_amount)."'";
					
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
			//$lines=str_replace('"','~',$lines);
			/*$sqldelete="truncate mrp";
			$rsdelete=mysqli_query($link,$sqldelete);*/
			$max_mrp_code='a000';
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
					//$prod_desc=str_replace('~','"',$prod_desc);
					$mrp=trim($data[1]);
					$sale_rate=trim($data[2]);
					if(strpos($sale_rate,',')!=false){
						$salepos=strpos($sale_rate,',');
					$sale_rate = substr($sale_rate,0,$salepos).substr(strstr($sale_rate, ","),1);
					}
					
					/*$sqlskunamechk="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
					$prod_code=$rowskunamechk['prod_code'];*/
					
					/*$sqlmrpchk="SELECT product_code,mrp,sale_rate FROM mrp WHERE product_code='".$prod_code."'";
					$rsmrpchk=mysqli_query($link,$sqlmrpchk);
					$countmrpchk=mysqli_num_rows($rsmrpchk);
					$csv_row_count=$rec_count+1;
					if($countmrpchk<1){
						$sqlmaxmrpcode="SELECT MAX(mrp_code) AS max_mrp_code FROM  mrp";
						$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
						$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
						$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
						
						if($max_mrp_code=='')
						{
							$max_mrp_code='m001';
						}
						else
						{*/
							
							
						//}
						$sql  = "insert into mrp ";
						$sql .= " SET product_code='".$prod_desc."'";
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , mrp='".mysqli_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysqli_escape_string($sale_rate)."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Mrp.csv.Please check.");
					/*}
					else
					{
						$rowmrpchk=mysqli_fetch_assoc($rsmrpchk);
						$mrp_db=$rowmrpchk['mrp'];
						$sale_rate_db=$rowmrpchk['sale_rate'];	
						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate){
							$sqlupdate  = "UPDATE mrp ";
							$sqlupdate .= " SET mrp='".mysqli_escape_string($mrp)."'";
							$sqlupdate .= " , sale_rate='".mysqli_escape_string($sale_rate)."'";
							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."'";
							mysqli_query($link,$sqlupdate) or die(mysqli_error().".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
						}
					}*/
				}
				$max_mrp_code++;
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
			}
		}
	} 
?>