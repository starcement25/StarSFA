<?php
	
	$type ='remortdb';
	include "saathi_connection.php";
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");

	//echo"<pre>";print_r($_POST);die;
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_customer_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_customer_code') $dns_customer_code=$value;
			if(!in_array($dns_customer_code,$dns_customer_code_array))
			{
				array_push($dns_customer_code_array,$dns_customer_code);
			}
			
			if($key=='customer_name') 	 ${'customer_name'.$dns_customer_code}=$value;
			if($key=='branch_code') 	   ${'branch_code'.$dns_customer_code}=$value;
			if($key=='route_code') 		${'dns_route_code'.$dns_customer_code}=$value;
			if($key=='route_name') 		${'route_name'.$dns_customer_code}=$value;
			if($key=='phone_no') 		  ${'phone_no'.$dns_customer_code}=$value;
			if($key=='acedns') 			${'acedns'.$dns_customer_code}=$value;
			if($key=='whatsapp_no') 	   ${'whatsapp_no'.$dns_customer_code}=$value;
			if($key=='cust_type') 	  	 ${'cust_type'.$dns_customer_code}=$value;
			if($key=='address') 	  	   ${'address'.$dns_customer_code}=$value;
			if($key=='zone') 	  	   	  ${'zone'.$dns_customer_code}=$value;
			if($key=='rds_tag') 	  	   ${'rds_tag'.$dns_customer_code}=$value;
			if($key=='email') 	  	   	 ${'email'.$dns_customer_code}=$value;
			if($key=='sap_customer_code') ${'sap_customer_code'.$dns_customer_code}=$value;
			if($key=='emp_code') 	  	   ${'emp_code'.$dns_customer_code}=${'emp_code'.$dns_customer_code}.$value.",";
		}
	}
	foreach($dns_customer_code_array as $dns_customer_code_val){
			
			/*echo $dns_customer_code_val;
			echo '<br />';
			echo ${customer_name.$dns_customer_code_val};
			echo '<br />';
			echo  ${branch_code.$dns_customer_code_val};
			echo '<br />';
			echo ${dns_route_code.$dns_customer_code_val};
			echo '<br />';
			echo ${route_name.$dns_customer_code_val};
			echo '<br />';
			echo ${phone_no.$dns_customer_code_val};
			echo '<br />';
			echo ${acedns.$dns_customer_code_val};
			echo '<br />';
			echo ${whatsapp_no.$dns_customer_code_val};
			echo '<br />';
			echo ${cust_type.$dns_customer_code_val};
			echo '<br />';
			echo ${address.$dns_customer_code_val};
			echo '<br />';*/
			
			$dns_customer_code=$dns_customer_code_val;
			$customer_name=${'customer_name'.$dns_customer_code_val};
			$branch_code=${'branch_code'.$dns_customer_code_val};
			$dns_route_code=${'dns_route_code'.$dns_customer_code_val};
			$route_name=${'route_name'.$dns_customer_code_val};
			$phone_no=${'phone_no'.$dns_customer_code_val};
			$acedns=${'acedns'.$dns_customer_code_val};
			$whatsapp_no=${'whatsapp_no'.$dns_customer_code_val};
			$cust_type=${'cust_type'.$dns_customer_code_val};
			$address=${'address'.$dns_customer_code_val};
			$zone=${'zone'.$dns_customer_code_val};
			$rds_tag=${'rds_tag'.$dns_customer_code_val};
			$email=${'email'.$dns_customer_code_val};
			$sap_customer_code=${'sap_customer_code'.$dns_customer_code_val};
			$emp_code_value=${'emp_code'.$dns_customer_code_val};
			$emp_code_value=substr($emp_code_value,0,-1);
			$emp_code_name_array=explode(",",$emp_code_value);
		
			foreach($emp_code_name_array as $emp_code_name_value_next)
					{
						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
				
						$sqlselcustomerroute="SELECT customer_code,emp_code FROM customer_route_emp_relation WHERE 
									   customer_code='".$sap_customer_code."'  AND emp_code='".$emp_code."' AND acedns='Y'";
						//exit();
						$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);
						$countcustomerroute=mysqli_num_rows($rsselcustomerroute);
						if($countcustomerroute==0)
						{
							$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET 		customer_code='".$sap_customer_code."',
													emp_code='".$emp_code."',
													acedns='Y',
													download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlinsertcustomerroute);
						}
						/*else
						{
							$sqlcustomermulticheck="SELECT customer_code FROM customer_route_emp_relation WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."')";
							$rscustomermulticheck=mysqli_query($link,$sqlcustomermulticheck);
							while($rowcustomermulticheck=mysqli_fetch_assoc($rscustomermulticheck))
							{
								$customer_code_multiple=$rowcustomermulticheck['customer_code'];
								$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code='".$route_code."',
														acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 
														customer_code='".$customer_code_multiple."' AND emp_code='".$emp_code."'";
								mysqli_query($link,$sqlupdatecustomerroute);
							}
						}*/
						$mapped_emp_code_string=$mapped_emp_code_string."'".$emp_code."'".',';
					}
					$mapped_emp_code_string_final=substr($mapped_emp_code_string,0,-1);

					$sql_update_customerrouteacedns="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 
					WHERE customer_code='".$sap_customer_code	."' AND emp_code NOT IN(".$mapped_emp_code_string_final.")";

					mysqli_query($link,$sql_update_customerrouteacedns);								 


			
			/*$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code."'";
			$rsbranchcode=mysqli_query($link,$sqlbranchcode);
			$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
			$branch_code_update=$rowbranchcode['branch_code'];
			
			$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";
			$rsrdscode=mysqli_query($link,$sqlrdscode);
			$rowrdscode=mysqli_fetch_assoc($rsrdscode);
			$rds_code=$rowrdscode['customer_code'];
		
			$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";
			$rsroutechk=mysqli_query($link,$sqlroutechk);
			$countroutechk=mysqli_num_rows($rsroutechk);
			if($countroutechk<1 && $route_name!='')
			{
				$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM 
								route_master WHERE route_code NOT LIKE '%N%'";
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
			$sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
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
				$sql .= " , branch_code='".addslashes($branch_code_update)."'";
				$sql .= " , phone_no='".$phone_no."'";
				$sql .= " , route_code='".$route_code."'";
				$sql .= " , acedns='Y'";
				$sql .= " , black_list='N'";
				$sql .= " , cust_type='".$cust_type."'";
				$sql .= " , rds_tag='".$rds_code."'";
				$sql .= " , address='".addslashes($address)."'";
				$sql .= " , zone='".addslashes($zone)."'";
				$sql .= " , whatsapp_no='".addslashes($whatsapp_no)."'";
				$sql .= " , email='".addslashes($email)."'";
				$sql .= " , download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
			   //modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			   $customer_code=$max_customer_code;
			   //$starsathi_operation='INSERT';
			 }
			else
			{
				$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
				$customer_code_db=$rowcustomernamechk['customer_code'];
				$route_code_db=$rowcustomernamechk['route_code'];
				$phone_no_db=$rowcustomernamechk['phone_no'];
				$acedns_db=$rowcustomernamechk['acedns'];
				$black_list_db=$rowcustomernamechk['black_list'];
				$customer_type_db=$rowcustomernamechk['cust_type'];
				$branch_code_db=$rowcustomernamechk['branch_code'];
				$customer_name_db=$rowcustomernamechk['customer_name'];
				$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];
				$address_db=$rowcustomernamechk['address'];
				$zone_db=$rowcustomernamechk['zone'];
				$rds_tag_db=$rowcustomernamechk['rds_tag'];
				$whatsapp_no_db=$rowcustomernamechk['whatsapp_no'];
				$email_db=$rowcustomernamechk['email'];
				$customer_type_db=$rowcustomernamechk['cust_type'];
				$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";
				if($route_code_db!=$route_code || $customer_type_db!=$cust_type 
				|| $branch_code_db!=$branch_code_update || $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no 
				|| $address_db!=$address || $whatsapp_no_db!=$whatsapp_no || $email_db!=$email || $rds_tag_db!=$rds_code || $zone_db!=$zone || $acedns_db!=$acedns)
				{
					$sqlupdated  = "update customer_master ";
					$sqlupdated .= " SET route_code='".$route_code."'";
					$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";
					$sqlupdated .= " , branch_code='".$branch_code_update."'";
					$sqlupdated .= " , cust_type='".$cust_type."'";
					$sqlupdated .= " , phone_no='".$phone_no."'";
					$sqlupdated .= " , rds_tag='".$rds_code."'";
					$sqlupdated .= " , address='".addslashes($address)."',
									 zone='".addslashes($zone)."',
									  whatsapp_no='".$whatsapp_no."',
									  email='".$email."',
									  acedns='".$acedns."',
									  download_time=CURRENT_TIMESTAMP() 
									 WHERE  ".$update_condition."";
					mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
					//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
				}
			}*/
		}

		$type ='localdb';
		include "saathi_connection.php";
		//mysqli_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='customer_master'";
		mysqli_query($link,$sqlupdate);
		mysqli_close($link);
?>
