<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_SUPERSHAKTIDMS");
	$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database");
	
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$customer_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='customer_code') $customer_code=$value;
			if(!in_array($customer_code,$customer_code_array))
			{
				array_push($customer_code_array,$customer_code);
			}
			if($key=='dns_customer_code') ${dns_customer_code.$customer_code}=$value;
			if($key=='customer_name') 	 ${customer_name.$customer_code}=$value;
			if($key=='branch_code') 	   ${branch_code.$customer_code}=$value;
			if($key=='route_code') 		${dns_route_code.$customer_code}=$value;
			if($key=='route_name') 		${route_name.$customer_code}=$value;
			if($key=='phone_no') 		  ${phone_no.$customer_code}=$value;
			if($key=='acedns') 			${acedns.$customer_code}=$value;
			if($key=='whatsapp_no') 	   ${whatsapp_no.$customer_code}=$value;
			if($key=='cust_type') 	  	 ${cust_type.$customer_code}=$value;
			if($key=='address') 	  	   ${address.$customer_code}=$value;
			if($key=='rds_tag') 	  	   ${rds_tag.$customer_code}=$value;
		}
	}
	foreach($customer_code_array as $dns_customer_code_val){
			
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
			
			$customer_code=$dns_customer_code_val;
			$dns_customer_code=${dns_customer_code.$dns_customer_code_val};
			$customer_name=${customer_name.$dns_customer_code_val};
			$branch_code=${branch_code.$dns_customer_code_val};
			$dns_route_code=${dns_route_code.$dns_customer_code_val};
			$route_name=${route_name.$dns_customer_code_val};
			$phone_no=${phone_no.$dns_customer_code_val};
			$acedns=${acedns.$dns_customer_code_val};
			$whatsapp_no=${whatsapp_no.$dns_customer_code_val};
			$cust_type=${cust_type.$dns_customer_code_val};
			$address=${address.$dns_customer_code_val};
			$rds_tag=${rds_tag.$dns_customer_code_val};
			
			$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code."'";
			$rsbranchcode=mysql_query($sqlbranchcode);
			$rowbranchcode=mysql_fetch_array($rsbranchcode);
			$branch_code_update=$rowbranchcode['branch_code'];
			
			$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";
			$rsrdscode=mysql_query($sqlrdscode);
			$rowrdscode=mysql_fetch_array($rsrdscode);
			$rds_code=$rowrdscode['customer_code'];
		
			$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
			$rsroutechk=mysql_query($sqlroutechk);
			$countroutechk=mysql_num_rows($rsroutechk);
			if($countroutechk<1 && $route_name!='')
			{
				$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM 
								route_master WHERE route_code NOT LIKE '%N%'";
				$rsmaxroutecode=mysql_query($sqlmaxroutecode);
				$rowmaxroutecode=mysql_fetch_array($rsmaxroutecode);
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
				mysql_query($sqlroute) or  array_push($error_array,"mysql_error().
								Internal DATA execution problem on route table.PLease contact aceDNS admin.");				
				//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
				$route_code=$max_route_code;
			}
			else
			{
				$rowroutechk=mysql_fetch_array($rsroutechk);
				$route_code=$rowroutechk['route_code'];
				$route_name_db=$rowroutechk['route_name'];
				if($route_name_db !=$route_name)
				{
					$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',download_time=CURRENT_TIMESTAMP() 
									WHERE route_code='".$route_code."'";
					mysql_query($sqlupdateroue) or  array_push($error_array,"mysql_error().
								Internal DATA execution problem on route table.PLease contact aceDNS admin.");
				}
			}
			$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";
			$rscustomernamechk=mysql_query($sqlcustomernamechk);
			$countcustomernamechk=mysql_num_rows($rscustomernamechk);
			$csv_row_count=$rec_count+1;
			if($countcustomernamechk<1)
			{
				/*$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
				$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
				$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
				$max_customer_code=$rowmaxcustomercode['max_customer_code'];
				
				if($max_customer_code=='')
				{
					$max_customer_code='C/0000001';
				}
				else
				{
					$max_customer_code++;
				}*/
				if(strtoupper($cust_type)=='DEALER') $cust_type='sub dealer';
				if(strtoupper($cust_type)=='DISTRIBUTOR') $cust_type='Dealer';
				$sql  = "insert into customer_master ";
				$sql .= " SET customer_code='".$customer_code."'";
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
				$sql .= " , whatsapp_no='".addslashes($whatsapp_no)."'";
				$sql .= " , download_time=CURRENT_TIMESTAMP()";
				mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
			   //modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			   $customer_code=$max_customer_code;
			   //$starsathi_operation='INSERT';
			 }
			else
			{
				$rowcustomernamechk=mysql_fetch_array($rscustomernamechk);
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
				$rds_tag_db=$rowcustomernamechk['rds_tag'];
				$whatsapp_no_db=$rowcustomernamechk['whatsapp_no'];
				$customer_type_db=$rowcustomernamechk['cust_type'];
				$update_condition=" customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";
				if(strtoupper($cust_type)=='DEALER') $cust_type='sub dealer';
				if(strtoupper($cust_type)=='DISTRIBUTOR') $cust_type='Dealer';

				if($route_code_db!=$route_code || $customer_type_db!=$cust_type 
				|| $branch_code_db!=$branch_code_update || $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no 
				|| $address_db!=$address || $whatsapp_no_db!=$whatsapp_no || $rds_tag_db!=$rds_code || $acedns_db!=$acedns)
				{
					$sqlupdated  = "update customer_master ";
					$sqlupdated .= " SET dns_customer_code='".addslashes($dns_customer_code)."'";
					$sqlupdated .= " , branch_code='".$branch_code_update."'";
					$sqlupdated .= " , cust_type='".$cust_type."'";
					$sqlupdated .= " , phone_no='".$phone_no."'";
					$sqlupdated .= " , rds_tag='".$rds_code."'";
					$sqlupdated .= " , address='".addslashes($address)."',
									  whatsapp_no='".$whatsapp_no."',
									  acedns='".$acedns."',
									  download_time=CURRENT_TIMESTAMP() 
									 WHERE  ".$update_condition."";
					mysql_query($sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
					//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
				}
			}
		}
		/*define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
		mysql_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='customer_master'";
		mysql_query($sqlupdate,$link);*/
?>
