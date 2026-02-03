<?php	
    if(similar_file_exists("../csv/$folderName/ABDOS Customer Master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/ABDOS Customer Master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
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
					$dns_customer_code =trim($data[0]);
					$customer_name	=trim($data[1]);
					$phone_no		=trim($data[2]);
					$dns_route_code	  =trim($data[3]);
					$route_name	  =trim($data[4]);  
					$emp_code_name		=trim($data[5]);
					 if(strpos($emp_code_name,';')!=false)
					 {
						$emp_code_name=str_replace(';',',',$emp_code_name);
					 }
					 $emp_code_name_array=explode(',',$emp_code_name);
					$acedns		  =trim($data[6]);
					$credit_limit	=trim($data[7]);
					$credit_days	 =trim($data[8]);
					$current_balance =trim($data[9]);
					$black_list	  =trim($data[10]); 
					$TD	  		  =trim($data[11]);
					$branch_code_name =trim($data[12]);
					$customer_type   =trim($data[13]);
					$rds_tag   =trim($data[14]);
					$sauda_validity_period  =trim($data[15]);
					$address  =trim($data[16]);
					$landline_no  =trim($data[17]);
					$owner_name  =trim($data[18]);
					$owner_phone  =trim($data[19]);
					$cust_class  =trim($data[20]);
					$weekly_closing_day  =trim($data[21]);
					$coverage_type  =trim($data[22]);
					$TIN  =trim($data[23]);
					$PAN  =trim($data[24]);
					$district  =trim($data[25]);
					$minimum_stock  =trim($data[26]);
					$bank_name  =trim($data[27]);
					$bank_account_number  =trim($data[28]);
					$email  =trim($data[29]);
					$visit_day  =trim($data[30]);
					
					$mapped_emp_code_string='';
					//For employee code and branch code
					if($acedns =='Y'){
					foreach($emp_code_name_array as $emp_code_name_value_next)
					{
					if(providing_code=='yes'){
						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
						$rsbranchcode=mysqli_query($link,$sqlbranchcode);
						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
						$branch_code=$rowbranchcode['branch_code'];
					}
					else
					{
						$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
						$rsbranchcode=mysqli_query($link,$sqlbranchcode);
						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
						$branch_code=$rowbranchcode['branch_code'];
					}
					//For distributor tagged
					if(providing_code=='yes'){
						$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";
					}
					else
					{
						$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y'";
					}
					$rsrdscode=mysqli_query($link,$sqlrdscode);
					$rowrdscode=mysqli_fetch_assoc($rsrdscode);
					$rds_code=$rowrdscode['customer_code'];
					
					//For route
					if(providing_code=='yes'){
						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";
					}
					else
					{
						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
					}
					$rsroutechk=mysqli_query($link,$sqlroutechk);
					$countroutechk=mysqli_num_rows($rsroutechk);
					if($countroutechk<1 && $route_name!='')
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
						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				
						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						$route_code=$max_route_code;
					}
					else
					{
						$rowroutechk=mysqli_fetch_assoc($rsroutechk);
						$route_code=$rowroutechk['route_code'];
						$route_name_db=$rowroutechk['route_name'];
						if($route_name_db !=$route_name)
						{
							$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',download_time=CURRENT_TIMESTAMP() 
											WHERE route_code='".$route_code."'";
							mysqli_query($link,$sqlupdateroue) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on route table.PLease contact aceDNS admin.");
						}
					}
					//For customer
					if(providing_code=='yes'){
					  // $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND route_code='".$route_code."'";
					    $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
					}
					else
					{
					 $sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";
					 //$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
					}
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					
					$csv_row_count=$rec_count+1;
					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
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
						$sql .= " , dns_customer_code='".$dns_customer_code."'";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , branch_code='".addslashes($branch_code)."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , credit_days='".$credit_days."'";
						$sql .= " , acedns='Y'";
						$sql .= " , black_list='N'";
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
						$sql .= " , landline_no='".$landline_no."'";
						$sql .= " , minimum_stock='".$minimum_stock."'";
						$sql .= " , bank_name='".$bank_name."'";
						$sql .= " , bank_account_number='".$bank_account_number."'";
						$sql .= " , email='".$email."'";
						$sql .= " , visit_day='".addslashes($visit_day)."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
					   //modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						$customer_code=$max_customer_code;
					}
					else
					{
						
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$phone_no_db=$rowcustomernamechk['phone_no'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$credit_days_db=$rowcustomernamechk['credit_days'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$TD_db=$rowcustomernamechk['TD'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						$rds_tag_db=$rowcustomernamechk['rds_tag'];
						$branch_code_db=$rowcustomernamechk['branch_code'];
						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];
						$customer_name_db=$rowcustomernamechk['customer_name'];
						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];
						$address_db=$rowcustomernamechk['address'];
						$owner_name_db=$rowcustomernamechk['owner_name'];
						$owner_phone_db=$rowcustomernamechk['owner_phone'];
						$cust_class_db=$rowcustomernamechk['cust_class'];
						$weekly_closing_day_db=$rowcustomernamechk['weekly_closing_day'];
						$TIN_db=$rowcustomernamechk['TIN'];
						$PAN_db=$rowcustomernamechk['PAN'];
						$district_db=$rowcustomernamechk['district'];
						$zone_db=$rowcustomernamechk['zone'];
						$landline_no_db=$rowcustomernamechk['landline_no'];
						$minimum_stock_db=$rowcustomernamechk['minimum_stock'];
						$bank_name_db=$rowcustomernamechk['bank_name'];
						$bank_account_number_db=$rowcustomernamechk['bank_account_number'];
						$email_db=$rowcustomernamechk['email'];
						$visit_day_db=$rowcustomernamechk['visit_day'];


						if(providing_code=='yes'){
							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";
						}
						else
						{
							$update_condition=" customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";
						}

						if($route_code_db!=$route_code || $current_balance_db!=$current_balance || $TD_db!=$TD || $customer_type_db!=$customer_type 
						|| $rds_tag_db!=$rds_code 
						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 
						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no || $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class || $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district || $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name || $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day)
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";
							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , branch_code='".$branch_code."'";
							$sqlupdated .= " , TD='".$TD."'";
							$sqlupdated .= " , cust_type='".$customer_type."'";
							$sqlupdated .= " , phone_no='".$phone_no."'";
						    $sqlupdated .= " , credit_days='".$credit_days."'";
							$sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";
							$sqlupdated .= " , address='".$address."'";
							$sqlupdated .= " , owner_name='".$owner_name."'";
							$sqlupdated .= " , owner_phone='".$owner_phone."'";
							$sqlupdated .= " , cust_class='".$cust_class."'";
							$sqlupdated .= " , weekly_closing_day='".$weekly_closing_day."'";
							$sqlupdated .= " , TIN='".$TIN."'";
							$sqlupdated .= " , PAN='".$PAN."'";
							$sqlupdated .= " , district='".$district."'";
							$sqlupdated .= " , landline_no='".$landline_no."'";
							$sqlupdated .= " , minimum_stock='".$minimum_stock."'";
							$sqlupdated .= " , bank_name='".$bank_name."'";
							$sqlupdated .= " , bank_account_number='".$bank_account_number."'";
							$sqlupdated .= " , email='".$email."'";
							$sqlupdated .= " , rds_tag='".$rds_code."',visit_day='".addslashes($visit_day)."',download_time=CURRENT_TIMESTAMP() 
											 WHERE  ".$update_condition."";
							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
							//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						}
						if(($credit_limit_db!=$credit_limit))
						{
							$sqlupdatedcredit  = "update customer_master ";
							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";
							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 
											 WHERE ".$update_condition."";
							mysqli_query($link,$sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						 // modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						}
						$customer_code=$customer_code_db;
					}
					//For customer route relation
					$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 
						           customer_code='".$customer_code."'  AND emp_code='".$emp_code."'";
					//exit();
					$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);
					$countcustomerroute=mysqli_num_rows($rsselcustomerroute);
					if($countcustomerroute==0)
					{
					    $sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',
												 route_code='".$route_code."',
												emp_code='".$emp_code."',
												acedns='".$acedns."',
												download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertcustomerroute);
					}
					else
					{
						$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code='".$route_code."',
												acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 
												customer_code='".$customer_code."' AND emp_code='".$emp_code."'";
						mysqli_query($link,$sqlupdatecustomerroute);						
					}
				 }
				}
			  }
					$rec_count++;
		   }//End of for loop
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}*/
?>