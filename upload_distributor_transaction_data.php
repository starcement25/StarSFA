<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_MAGIK");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database");

//$folder_path = "masterdata"; 
   
// List of name of files inside 
// specified folder 
//$files = glob($folder_path.'/*');  
   
// Deleting all the files in the list 
$connection = ssh2_connect('115.240.2.102', 22);
ssh2_auth_password($connection, 'sftp-09', 'Magik#09');

$sftp = ssh2_sftp($connection);
$sftp_fd = intval($sftp);

$handle = opendir("ssh2.sftp://$sftp_fd/sftp-09/");
//echo "Directory handle: $handle\n";
//echo "Entries:\n";
while (false != ($entry = readdir($handle))){
    echo $entry.'<br />';
	//exit();
	//$file_parts=explode("/",$file);
	$rec_count = 0;
	$ins_count = 0;
	$err = "";
	//if($entry=='Distributor Transactions_2021_04_28.csv'){
	$lines = file("ssh2.sftp://$sftp_fd/sftp-09/$entry");
	//print_r($lines);
	/*$sqldelete="DELETE FROM ledger_balance_details WHERE UPPER(cust_class)='SALT'";
	$rsdelete=mysqli_query($link,$sqldelete);*/
	$line='';
	 $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $initial_start_date='2021-04-01';
	  $initial_end_date='2021-06-30';
	foreach($lines as $line)
	{
		$i = 0;
		$char = substr($line, $i, 1);
		$value ="";
		$data=array();
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
	
					//$data[]=$value;
					array_push($data,$value);
	
					$value = "";
				}
				else 
				{
				$value .= $char;
	
				}
				$i++;
				$char = substr($line, $i, 1);
			} //end of while
	
			//$data[]=$value;
			array_push($data,$value);
		  //print_r($data);
			$csv_row_count=$rec_count+1;
			  	$trans_type=trim($data[0]);
				$distributor_name=trim($data[1]);
				$customer_name=trim($data[2]);
				$product_code=trim($data[3]);
				$qty=trim($data[4]);
				$year=trim($data[5]);
				$month=trim($data[6]);
				if(strlen($month)=='1') $month='0'.$month;
				$day=trim($data[7]);
				if(strlen($day)=='1') $day='0'.$day;
				$product_name=trim($data[8]);
				$rate=trim($data[9]);
				$value=trim($data[10]);
				
				$sqlcustomercode="SELECT customer_code,cust_type FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
				$rscustomercode=mysqli_query($link,$sqlcustomercode);
				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
				$customer_code=$rowcustomercode['customer_code'];
				$cust_type=$rowcustomercode['cust_type'];
				
				$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($product_code)."'";
				$rsprodcode=mysqli_query($link,$sqlprodcode);
				$rowprodcode=mysqli_fetch_assoc($rsprodcode);
				$prod_code=$rowprodcode['prod_code'];
				
				/*$sqlchkprimarysales="SELECT customer_code FROM primary_sales WHERE 
								customer_code='".addslashes($dns_customer_code)."' AND division_text ='".addslashes($division_text)."' AND 
								lob='".addslashes($lob)."' AND inv_no='".addslashes($inv_no)."' AND inv_date='".addslashes($inv_date)."' AND BILLQTY='".addslashes($billqty)."' AND BILLQTYNA='".addslashes($billqtyna)."'";*/
						
				/*$sqlchkprimarysales="SELECT customer_code FROM primary_sales WHERE 
								 inv_no='".addslashes($inv_no)."' AND customer_code='".addslashes($dns_customer_code)."' AND line_item='".addslashes($line_item)."'";				
				$rschkprimarysales=mysqli_query($conn,$sqlchkprimarysales);
				$countchkprimarysales=mysqli_num_rows($rschkprimarysales);
				$csv_row_count=$rec_count+1;
				if($countchkprimarysales ==0)
				{*/
					
				/*echo $sqlcustomerchk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
				$rscustomerchk=mysqli_query($conn,$sqlcustomerchk);
				$countcustomerchk=mysqli_num_rows($rscustomerchk);
					if($countcustomerchk > 0)
					{	*/
					if(strtoupper($trans_type)=='OUT'){
						 
						  	$sqlempcode="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND 
										customer_code='".addslashes($customer_code)."' LIMIT 0,1";
							$rsempcode=mysqli_query($link,$sqlempcode);
							$rowempcode=mysqli_fetch_assoc($rsempcode);
							$empcode=$rowempcode['emp_code'];
							
							$order_no='O'.$empcode.$year.$month.$day.$hour.$minute.$second;
							$visit_date_day="$year-$month-$day";
							$visit_date="$year-$month-$day $hour:$minute:$second";
							$sqlchkorder="SELECT customer_code FROM prev_order_counting_master WHERE 
								 product_code='".addslashes($prod_code)."' AND customer_code='".addslashes($customer_code)."' 
								 AND SUBSTRING(visit_date,1,10)='".addslashes($visit_date_day)."'";
							$rschkorder=mysqli_query($link,$sqlchkorder);
							$cntchkorder=mysqli_num_rows($rschkorder);	 
							if($cntchkorder==0){
								if($customer_code!='' && $prod_code!=''){
									$second=$second+1;
									if($second >59)
									{
										$second='10';
										$minute=$minute+1;
									}
									if($minute > 59)
									{
										$second='10';
										$minute='10';
										$hour=$hour+1;
									}	 				
							$sqlinsorder  = "insert into prev_order_counting_master SET ";
							$sqlinsorder .= "   customer_code='".addslashes($customer_code)."'";
							$sqlinsorder .= "  ,  	product_code='".addslashes($prod_code)."'";
							$sqlinsorder .= "  ,  	cust_type='".addslashes($cust_type)."'";
							$sqlinsorder .= "  , visit_qty='".addslashes($qty)."'";
							$sqlinsorder .= "  , visit_date='".addslashes($visit_date)."'";
							$sqlinsorder .= "  , 	order_no='".addslashes($order_no)."'";
							$sqlinsorder .= " , rate='".addslashes($rate)."'";
							$sqlinsorder .= " , amount='".addslashes($value)."'";
							$sqlinsorder .= " , creation_type='upload'";
							echo $sqlinsorder .= " , download_time=CURRENT_TIMESTAMP()";
							
								mysqli_query($link,$sqlinsorder);
								//target Ach update START
								if(strtotime(substr($visit_date,0,10)) >=strtotime($initial_start_date) && strtotime(substr($visit_date,0,10)) <=strtotime( $initial_end_date))
								{
									$final_start_date= $initial_start_date;
									$final_end_date=$initial_end_date;
								}
								$sqltargetach="SELECT * FROM  retailer_wise_target_ach WHERE customer_code='".addslashes($customer_code)."' AND acedns='Y' 
												AND start_date='".addslashes($final_start_date)."' AND end_date='".addslashes($final_end_date)."'";
								$rstargetach=mysqli_query($link,$sqltargetach);
								$counttargetach=mysqli_num_rows($rstargetach);
								if($counttargetach==0)
									{
										if(substr($visit_date,5,2)=='04')
										{
											$apr_freq_ach=$qty;
										}
										else
										{
											$apr_freq_ach=0;
										}
										if(substr($visit_date,5,2)=='05')
										{
											$may_freq_ach=$qty;
										}
										else
										{
											$may_freq_ach=0;
										}
										if(substr($visit_date,5,2)=='06')
										{
											$jun_freq_ach=$qty;
										}
										else
										{
											$jun_freq_ach=0;
										}
										
										$sql  = "insert into retailer_wise_target_ach ";
										$sql .= " SET customer_code='".$customer_code."'";
										$sql .= " , customer_name='".addslashes($customer_name)."'";
										$sql .= " , start_date='".addslashes($final_start_date)."'";
										$sql .= " , end_date='".addslashes($final_end_date)."'";
										$sql .= " , value_slab_target='0'";
										$sql .= " , value_slab_ach='".$value."'";
										$sql .= " , sku_count_target='0'";
										$sql .= " , sku_count_ach='1'";
										$sql .= " , apr_freq_target='0'";
										$sql .= " , apr_freq_ach='".$apr_freq_ach."'";
										$sql .= " , may_freq_target='0'";
										$sql .= " , may_freq_ach='".$may_freq_ach."'";
										$sql .= " , jun_freq_target='0'";
										$sql .= " , jun_freq_ach='".$jun_freq_ach."'";
										$sql .= " , acedns='Y'";
										echo $sql .= " , upload_time=CURRENT_TIMESTAMP()";
									   mysqli_query($link,$sql);
									}
									else
									{
										if(substr($visit_date,5,2)=='04')
										{
											$apr_freq_ach=$qty;
										}
										else
										{
											$apr_freq_ach=0;
										}
										if(substr($visit_date,5,2)=='05')
										{
											$may_freq_ach=$qty;
										}
										else
										{
											$may_freq_ach=0;
										}
										if(substr($visit_date,5,2)=='06')
										{
											$jun_freq_ach=$qty;
										}
										else
										{
											$jun_freq_ach=0;
										}
										$sqlchkskucount="SELECT product_code FROM prev_order_counting_master WHERE 
											 product_code='".addslashes($prod_code)."' AND customer_code='".addslashes($customer_code)."' 
											 AND SUBSTRING(visit_date,1,10) >='".$initial_start_date."' AND SUBSTRING(visit_date,1,10) <='".$initial_end_date."'";
										$rschkskucount=mysqli_query($link,$sqlchkskucount);
										$cntchkskucount=mysqli_num_rows($rschkskucount);	 
										if($cntchkskucount==1){
											$sku_count_add='1';
										}
										else
										{
											$sku_count_add='0';
										}
										
										$sqlupdate  = "update retailer_wise_target_ach SET";
										$sqlupdate .= " value_slab_ach=(value_slab_ach+'".$value."')";
										$sqlupdate .= " , sku_count_ach=(sku_count_ach+'".$sku_count_add."')";
										$sqlupdate .= " , apr_freq_ach=(apr_freq_ach+'".$apr_freq_ach."')";
										$sqlupdate .= " , may_freq_ach=(may_freq_ach+'".$may_freq_ach."')";
										$sqlupdate .= " , jun_freq_ach=(jun_freq_ach+'".$jun_freq_ach."')";
										echo $sqlupdate .= " , upload_time=CURRENT_TIMESTAMP() WHERE customer_code='".addslashes($customer_code)."' AND acedns='Y' 
												AND start_date='".addslashes($final_start_date)."' AND end_date='".addslashes($final_end_date)."'";
									   	mysqli_query($link,$sqlupdate);
									}
									//target Ach update end
								}
							}
							else
							{
								$sqlinsorderdup  = "insert into prev_order_counting_master_backup SET ";
								$sqlinsorderdup .= "   customer_code='".addslashes($customer_code)."'";
								$sqlinsorderdup .= "  ,  	product_code='".addslashes($prod_code)."'";
								$sqlinsorderdup .= "  , visit_qty='".addslashes($qty)."'";
								$sqlinsorderdup .= "  , visit_date='".addslashes($visit_date)."'";
								$sqlinsorderdup .= "  , 	order_no='".addslashes($order_no)."'";
								$sqlinsorderdup .= " , download_time=CURRENT_TIMESTAMP()";
								//mysqli_query($link,$sqlinsorderdup);
							}
					//}
				}
				/*else
				{
				//}*/
			}
	   $rec_count++;
	}
	//}
} 
?>