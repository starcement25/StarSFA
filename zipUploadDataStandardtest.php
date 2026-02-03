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

	$folderName="$nick_name";
	$error_array=array();
		if ( !file_exists("csv/$folderName")){
		mkdir("csv/$folderName");
		chmod("csv/$folderName", 0777);
	}
	// Get array of all source files
	/*$files = scandir("csv/$folderName");
	// Identify directories
	$source = "csv/$folderName/";
	$destination = "csv/$folderName/filebkup/";
	// Cycle through all source files
	foreach ($files as $file) {
	  if (in_array($file, array(".",".."))) continue;
	  // If we copied this successfully, mark it for deletion
	  if (@copy($source.$file, $destination.$file)) {
		$delete[] = $source.$file;
	  }
	}
	// Delete all successfully-copied files
	foreach ($delete as $file) {
	  unlink($file);
	}*/

	//For MRP CSV
	if(similar_file_exists("csv/$folderName/MRP1.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/MRP1.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			$lines = file($filename);
			/*$sqldelete="truncate mrp";
			$rsdelete=mysqli_query($link,$sqldelete);*/
			$branch_code_array=array();
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
				  	$csv_row_count=$rec_count+1;
				    $branch_code_name=trim($data[0]);
					$prod_code_name=trim($data[1]);
					//$brand_code_name=trim($data[2]);
					//$brand_form_code_name=trim($data[3]);
					$dns_mrp_code=trim($data[2]);
					$mrp=trim($data[3]);
					if(strpos($mrp,',')!=false){
						$mrppos=strpos($mrp,',');
					$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);
					}
					$sale_rate=trim($data[4]);
					if(strpos($sale_rate,',')!=false){
						$sale_ratepos=strpos($sale_rate,',');
						$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);
					}

					$vertical_value=trim($data[5]);
					//$UOM=trim($data[6]);
					$destination_code_name=trim($data[6]);
					$order_type=trim($data[7]);
					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
						//$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' 
								//AND product_group_code='".$brand_code_name."' AND product_sub_group_code='".$brand_form_code_name."'";
					}
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];
					if(providing_code=='yes'){
						if(branch_wise_product=='yes')
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";
						}
						else
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";
						}
					}
					else
					{
						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
					}
					$rsprodcode=mysqli_query($link,$sqlprodcode);
					$rowprodcode=mysqli_fetch_assoc($rsprodcode);
					$prod_code=$rowprodcode['prod_code'];
					
					if($destination_code_name!='')
					{
						if(providing_code=='yes'){
								$sqldestinationcode="SELECT destination_code FROM destination_master WHERE dns_destination_code='".$destination_code_name."'";
						}
						else
						{
							$sqldestinationcode="SELECT destination_code FROM destination_master WHERE destination_name='".addslashes($destination_code_name)."'";
						}
					}
					$rsdestinationcode=mysqli_query($link,$sqldestinationcode);
					$rowdestinationcode=mysqli_fetch_assoc($rsdestinationcode);
					$destination_code=$rowdestinationcode['destination_code'];

					
					if(uom_wise_mrp=='yes' && branch_wise_mrp=='yes')
					{
						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."' AND branch_code='".$branch_code."'";
					}
					else if(destination_ordertype_price_list=='yes')
					{
						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND destination_code='".$destination_code."' 
									AND order_type='".$order_type."'";
					}
					else if(destination_price_list=='yes' && destination_ordertype_price_list=='no')
					{
						echo $sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND destination_code='".$destination_code."'";
					}
					else
					{
						if(providing_code=='yes')
						{
							$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."' 
										AND dns_mrp_code='".$dns_mrp_code."'";
						}
						else
						{
							$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."'";
						}
					}
					$rsmrpchk=mysqli_query($link,$sqlmrpchk);
					$countmrpchk=mysqli_num_rows($rsmrpchk);
					$csv_row_count=$rec_count+1;
					$insertflag=0;
					$updateflag=0;
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
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , dns_mrp_code='".$dns_mrp_code."'";
						$sql .= " , mrp='".mysqli_real_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
						$sql .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
						$sql .= " , UOM='".mysqli_real_escape_string($UOM)."'";
						$sql .= " , destination_code='".mysqli_real_escape_string($destination_code)."'";
						$sql .= " , order_type='".mysqli_real_escape_string($order_type)."'";
						echo $sql .= " , download_time=CURRENT_TIMESTAMP()";
						//mysqli_query($link,$sql)  or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");
						$insertflag=1;
					}
					else
					{
						$rowmrpchk=mysqli_fetch_assoc($rsmrpchk);
						$mrp_db=$rowmrpchk['mrp'];
						$sale_rate_db=$rowmrpchk['sale_rate'];	
						$destination_code_db=$rowmrpchk['destination_code'];
						$order_type_db=	$rowmrpchk['order_type'];
						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate || $destination_code_db!=$destination_code || $order_type_db!=$order_type){
							$sqlupdate  = "UPDATE mrp ";
							$sqlupdate .= " SET mrp='".mysqli_real_escape_string($mrp)."'";
							$sqlupdate .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
							$sqlupdate .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
							$sqlupdate .= " , destination_code	='".mysqli_real_escape_string($destination_code)."'";
							$sqlupdate .= " , order_type='".mysqli_real_escape_string($order_type)."'";
							echo $sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."'";
							//mysqli_query($link,$sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
							$updateflag=1;
						}
					}
					/*if(branch_wise_mrp=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log
					{
						if(!in_array($branch_code,$branch_code_array))
						{
							array_push($branch_code_array,$branch_code);
							$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)";
							$rsbranchwiseemp=mysqli_query($link,$sqlbranchwiseemp);
							while($rowbranchwiseemp=mysqli_fetch_assoc($rsbranchwiseemp))
							{
								$emp_code_branchwise=$rowbranchwiseemp['emp_code'];
								modifyempdatadownloadlog($emp_code_branchwise,strtoupper($folderName));
							}
						}
					}//End For emp data download log*/
				}
				 $rec_count++;
			}
			exit();
		    if(branch_wise_mrp=='no')//Start For emp data download log with no branch tagging
			{
				$emp_code='';
				modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			}//End For emp data download log with no branch tagging

			//Product code checking start
				$sqlprodcodeprice="SELECT product_code FROM mrp WHERE product_code NOT IN
									(SELECT prod_code FROM product_master) GROUP BY product_code";
				$rsprodcodeprice=mysqli_query($link,$sqlprodcodeprice);
				$cntprodcodeprice=mysqli_num_rows($rsprodcodeprice);
				if($cntprodcodeprice>0)
				{
					$prodcodeprice='';
					while($rowprodcodeprice=mysqli_fetch_assoc($rsprodcodeprice))
					{
						$prodcodeprice=$prodcodeprice.$rowprodcodeprice['product_code'].',';
					}
					$prodcodeprice=substr($prodcodeprice,0,-1);
					$errorprodcodeprice=$prodcodeprice.' exists in MRP but not exists in Sku Master.';
					array_push($error_array,$errorprodcodeprice);
				}
			//Product code checking end		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for MRP.csv is wrong.";
			exit();
		}*/
		//For Distributor route csv
		if(similar_file_exists("csv/$folderName/Distributor route.csv")!=false)
		{  
		    $filename=similar_file_exists("csv/$folderName/Distributor route.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
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
						$csv_row_count=$rec_count+1;
						$customer_code_name=trim($data[0]);
						$route_code_name=trim($data[1]);
						$acedns=trim($data[2]);
						
						if(providing_code=='yes'){
							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";
						}
						else
						{
							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_code_name."'";
						}
						$rscustomercode=mysqli_query($link,$sqlcustomercode);
						$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
						$customer_code=$rowcustomercode['customer_code'];
						
						$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_code_name."'";
						$rsroutecode=mysqli_query($link,$sqlroutecode);
						$rowroutecode=mysqli_fetch_assoc($rsroutecode);
						$route_code=$rowroutecode['route_code'];

						if($customer_code!='' && $route_code!='')
						{
							$sqldistributorroute="SELECT distributor_code,acends,route_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."' 
												AND route_code='".$route_code."'";
							$rsdistributorroute=mysqli_query($link,$sqldistributorroute);
							$countdistributorroute=mysqli_num_rows($rsdistributorroute);
							if($countdistributorroute <1)
							{
								$sqlinsertdistributorroute="INSERT INTO distributor_route_relation ";
								$sqlinsertdistributorroute .= " SET distributor_code='".$customer_code."'";
								$sqlinsertdistributorroute .= " ,route_code='".$route_code."'";
								$sqlinsertdistributorroute .= " ,acedns='".$acedns."'";
								$sqlinsertdistributorroute .= " ,download_time=CURRENT_TIMESTAMP()";
								mysqli_query($link,$sqlinsertdistributorroute) or array_push($error_array,".Internal error occurrs @row $csv_row_count in Distributor route.csv.Please check.");
							}
							else
							{
								$rowdistributorroute=mysqli_fetch_assoc($rsdistributorroute);
								$acednsdb=$rowdistributorroute['acends'];
								$distributor_code_db=$rowdistributorroute['distributor_code'];
								$route_code_db=$rowdistributorroute['route_code'];
								
								if($acednsdb!=$acedns)
								{
									$sqlupdatedistributorroute="UPDATE distributor_route_relation SET acedns='".$acedns."',
															  download_time=CURRENT_TIMESTAMP() WHERE distributor_code='".$distributor_code_db."' AND route_code='".$route_code_db."'";
									$rsupdatedistributorroute=mysqli_query($link,$sqlupdatedistributorroute) or array_push($error_array,".Internal error occurrs @row $csv_row_count in Distributor route.csv.Please check.");
								}
							}
						}
					}
					 $rec_count++;
				}
				$successval=1;
			}
			/*else
			{
				echo $successval="Naming convention for Distributor route.csv is wrong.";
				exit();
			}*/
		

?>