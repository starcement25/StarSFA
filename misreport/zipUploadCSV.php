<?php
error_reporting(E_ALL ^ E_NOTICE);

require("../include/config.php");
require("../include/dbcon.php");

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
if(similar_file_exists('../csv/aceDNS_csv.zip')!=false)
{
	$filename=similar_file_exists("../csv/aceDNS_csv.zip");
	 $zip = new ZipArchive;
	if ($zip->open($filename)) {
		
		$zip->extractTo('../csv/');
		$zip->close();
		
		//For Brand Master CSV
		if(similar_file_exists("../csv/Brand Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/Brand Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$sqldelete="truncate brand_master";
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
				  
					$brand_name=trim($data[0]);
					
					$sqlbrand  = "insert into brand_master SET ";
					$sqlbrand .= "  brand_code='".mysqli_escape_string($brand_name)."'";
					$sqlbrand .= " , brand_name='".mysqli_escape_string($brand_name)."'";
					
					mysqli_query($link,$sqlbrand) or die(mysqli_error());
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		else
		{
			$successval=0;
		}
		
		//For Employee CSV
		if(similar_file_exists("../csv/Employee Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/Employee Master.csv");
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
					
					$sqlempnamechk="SELECT * FROM employee_master WHERE emp_name='".$employee_name."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$countempnamechk=mysqli_num_rows($rsempnamechk);
					
					if($countempnamechk<1)
					{
						$sqlmaxempcode="SELECT MAX(emp_code) AS max_emp_code FROM  employee_master ";
						$rsmaxempcode=mysqli_query($link,$sqlmaxempcode);
						$rowmaxempcode=mysqli_fetch_assoc($rsmaxempcode);
						$max_emp_code=$rowmaxempcode['max_emp_code'];
						$max_emp_code++;

						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						echo $sql .= " , emp_name='".$employee_name."'";
						mysqli_query($link,$sql) or die(mysqli_error());
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'"; 
						mysqli_query($link,$sqlcp) or die(mysqli_error());
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		else
		{
			$successval=0;
		}
		
		//For Customer CSV
		if(similar_file_exists("../csv/Customer Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/Customer Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			/*$sqldelete="truncate customer_master";
			$rsdelete=mysqli_query($link,$sqldelete);
			$sqlroutedelete="truncate route_master";
			$rsroutedelete=mysqli_query($link,$sqlroutedelete);*/
			
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
					
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					
					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE 1";
						$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
						$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
						$max_customer_code=$rowmaxcustomercode['max_customer_code'];
						$max_customer_code++;
						
						$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".$emp_name."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						
						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
						$rsroutechk=mysqli_query($link,$sqlroutechk);
						$countroutechk=mysqli_num_rows($rsroutechk);
						if($countroutechk<1)
						{
							$routcode='RT/'.$countroute;
							$sqlroute  = "insert into route_master ";
							$sqlroute .= " SET route_code='".$routcode."'";
							$sqlroute .= " ,route_name='".$route_name."'";
							$sqlroute .= " , emp_code='".$emp_code."'";
							
							mysqli_query($link,$sqlroute);
						}
						
						$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
						$rsroutecode=mysqli_query($link,$sqlroutecode);
						$rowroutecode=mysqli_fetch_assoc($rsroutecode);
						$route_code=$rowroutecode['route_code'];
						
						$sql  = "insert into customer_master ";
						$sql .= " SET customer_code='".$max_customer_code."'";
						$sql .= " , customer_name='".mysqli_real_escape_string(addslashes($customer_name))."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						
						mysqli_query($link,$sql);
					}
				}
				 $rec_count++;
				 $countroute++;
			}		
			$successval=1;
		}
		else
		{
			$successval=0;
		}
		
		//For Sku CSV
		if(similar_file_exists("../csv/SKU Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/SKU Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			/*$sqldelete="truncate sku_master";
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
				  
					$sku_name=trim($data[0]);
					$brand_code=trim($data[1]);
					$cl_stk=trim($data[4]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					
					$sqlskunamechk="SELECT * FROM sku_master WHERE sku_name='".$sku_name."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$countskunamechk=mysqli_num_rows($rsskunamechk);
					
					if($countskunamechk<1)
					{
						$sqlmaxskucode="SELECT MAX(sku_code) AS max_sku_code FROM  sku_master WHERE 1";
						$rsmaxskucode=mysqli_query($link,$sqlmaxskucode);
						$rowmaxskucode=mysqli_fetch_assoc($rsmaxskucode);
						$max_sku_code=$rowmaxskucode['max_sku_code'];
						$max_sku_code++;
					
						$sql  = "insert into sku_master ";
						$sql .= " SET sku_code='".$max_sku_code."'";
						$sql .= " , sku_name='".mysqli_escape_string($sku_name)."'";
						$sql .= " , brand_code='".mysqli_escape_string($brand_code)."'";
						$sql .= " , cl_stk='".mysqli_escape_string($cl_stk)."'";
						mysqli_query($link,$sql);
					}
					$sqlupdatestock="UPDATE sku_master SET cl_stk='".$cl_stk."' WHERE sku_name='".$sku_name."'";
					mysqli_query($link,$sqlupdatestock);
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		else
		{
			$successval=0;
		}	
		
		//For Outstanding CSV
		
		if(similar_file_exists("../csv/Outstanding.csv")!=false)
		{
			$filename=similar_file_exists("../csv/Outstanding.csv");
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
					
					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_name."'";
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
					
					mysqli_query($link,$sql);
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		else
		{
			$successval=0;
		}
		if($successval==1)
		{
			echo $err = 'Zip file extracted and data has been uploaded successfully';
		}
		else 
		{
			echo $err = "Problem with uploading Zip file";
		}
	} else {
		echo $err = "Problem in zip file extraction.";
	}
}
mysqli_close($link);
?>