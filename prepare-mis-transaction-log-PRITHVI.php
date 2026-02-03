<?php
	$nick_name='PRITHVI';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	require("include/config-setup.php");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$date_val=$year.'-'.$month.'-'.$date;
	$date_val=date('Y-m-d', strtotime("-1 days,$date_val"));
	//$date_val='';
	
	/*$sql_transaction_download = "select BM.branch_code,RM.rds_code,EM.emp_code,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,
									OD.order_no,CM.customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type,OH.d_instruction,RM.rds_name from order_details OD,
									customer_master CM,product_master PM, order_header OH,rds_master RM,branch_master BM,employee_master EM,
									location LO where OH.customer_code = CM.customer_code and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND SUBSTRING(OH.order_no,2,1)!='C' AND CM.rds_tag=RM.rds_code AND CM.branch_code=BM.branch_code 
									AND CM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '".$date_val."'  
																	UNION
									select BM.branch_code,RM.rds_code,EM.emp_code,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,OD.order_no, 
									VM.vendor_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type, OH.d_instruction,RM.rds_name from order_details OD,
									vendor_master VM,product_master PM,order_header OH,rds_master RM,branch_master BM,employee_master EM,
									location LO where OH.customer_code = VM.vendor_code and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND SUBSTRING(OH.order_no,2,1)!='C' AND VM.rds_code=RM.rds_code AND VM.branch_code=BM.branch_code 
									AND VM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '".$date_val."'
                                    								UNION
                                    select BM.branch_code,RM.rds_code,EM.emp_code,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,
									OD.order_no,RM.rds_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type, OH.d_instruction,RM.rds_name from order_details OD,
									product_master PM,order_header OH,rds_master RM,branch_master BM,employee_master EM,location LO 
									where SUBSTRING(OH.order_no,2,5) = RM.emp_code AND OH.transaction_type='ST' AND OD.sku_code = PM.prod_code 
									and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND EM.branch_code=BM.branch_code 
									AND RM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '".$date_val."' 
                                 							ORDER BY rds_name,transaction_type";*/

		$sql_transaction_download = "select BM.branch_code,RM.rds_code,EM.emp_code,LO.date AS transaction_date,
									OD.order_no,CM.customer_code,CM.customer_name,PM.prod_code,PM.product_group_code,OD.qty,
									OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type,OH.d_instruction,RM.rds_name from order_details OD,
									customer_master CM,product_master PM, order_header OH,rds_master RM,branch_master BM,employee_master EM,
									location LO where OH.customer_code = CM.customer_code and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND SUBSTRING(OH.order_no,2,1)!='C' AND CM.rds_tag=RM.rds_code AND CM.branch_code=BM.branch_code 
									AND CM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date_val."%'
																	UNION ALL
									select BM.branch_code,RM.rds_code,EM.emp_code,LO.date AS transaction_date,OD.order_no,VM.vendor_code AS customer_code,
									VM.vendor_name AS customer_name,PM.prod_code,PM.product_group_code,OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type, OH.d_instruction,RM.rds_name from order_details OD,
									vendor_master VM,product_master PM,order_header OH,rds_master RM,branch_master BM,employee_master EM,
									location LO where OH.customer_code = VM.vendor_code and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND SUBSTRING(OH.order_no,2,1)!='C' AND VM.rds_code=RM.rds_code AND VM.branch_code=BM.branch_code 
									AND VM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date_val."%' 
                                    								UNION ALL
                  					select BM.branch_code,RM.rds_code,EM.emp_code,LO.date AS transaction_date,
									OD.order_no,RM.rds_code AS customer_code,RM.rds_name AS customer_name,PM.prod_code,PM.product_group_code,
									OD.qty,OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type, OH.d_instruction,RM.rds_name from order_details OD,
									product_master PM,order_header OH,rds_master RM,branch_master BM,employee_master EM,location LO 
									where SUBSTRING(OH.order_no,2,5) = RM.emp_code AND OH.transaction_type='ST' AND OD.sku_code = PM.prod_code 
									and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND EM.branch_code=BM.branch_code 
									AND RM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date_val."%' 
                                 							ORDER BY rds_name,transaction_type";														
															
		$rs_transaction_download = mysqli_query($link,$sql_transaction_download) or die(mysqli_error()." Error in transaction download: ".$sql_transaction_download);
		while($rec_transaction_download = mysqli_fetch_assoc($rs_transaction_download)) 
		{ 
			$branch_code=$rec_transaction_download['branch_code'];
			$rds_code=$rec_transaction_download['rds_code'];
			$emp_code=$rec_transaction_download['emp_code'];
			$transaction_date=$rec_transaction_download['transaction_date'];
			$order_no=$rec_transaction_download['order_no'];
			$sale_rate=$rec_transaction_download['sale_rate'];
			$customer_code=$rec_transaction_download['customer_code'];
			$customer_name=$rec_transaction_download['customer_name'];
			$prod_code=$rec_transaction_download['prod_code'];
			$product_group_code=$rec_transaction_download['product_group_code'];
			$qty=$rec_transaction_download['qty'];
			$Amount=$rec_transaction_download['Amount'];
			$TD='';
			$input_amount=$rec_transaction_download['input_amount'];
			if($input_amount >0)
			{
				$Amount=$input_amount;
				$sale_rate=0;
			}
			$VAT=$rec_transaction_download['VAT'];
			$transaction_type=$rec_transaction_download['transaction_type'];
			$d_instruction=$rec_transaction_download['d_instruction'];
			
			if($transaction_type=='ST' || $transaction_type=='BT')
			{
				$sqlrdsname="SELECT RM.rds_name,RM.rds_code FROM rds_master RM,order_header OH WHERE OH.customer_code=RM.rds_code 
							AND OH.order_no='".$order_no."'";
				$rsrdsname=mysqli_query($link,$sqlrdsname) or die(mysqli_error()." Error in select rds name: ".$sqlrdsname);
				$rowrdsname=mysqli_fetch_assoc($rsrdsname);
				$customer_name=$rowrdsname['rds_name'];
				$customer_code=$rowrdsname['rds_code'];		
			}
			
			$sqlinsertmistransactionlog="INSERT INTO mis_transaction_log SET branch_code='".$branch_code."',
											rds_code 		='".$rds_code."',
											emp_code		='".$emp_code."',
											trans_date		='".$transaction_date."',
											trans_id		='".$order_no."',
											customer_code	='".$customer_code."',
											customer_name	='".$customer_name."',
											sku_code		='".$prod_code."',
											group_code		='".$product_group_code."',
											qty				='".$qty."',
											sale_rate		='".$sale_rate."',
											amount			='".$Amount."',
											VAT				='".$VAT."',
											TD				='".$TD."',
											d_instruction	='".$d_instruction."',
											trans_type 		='".$transaction_type."',
											download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertmistransactionlog))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
		}
		$sql_query_order_header_SA="SELECT BM.branch_name,RM.rds_name,EM.emp_name,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,
									OD.order_no,PM.prod_code,PM.product_group_code, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,OH.transaction_type, OH.d_instruction,RM.rds_code from order_details OD,
									product_master PM,order_header OH,rds_master RM,branch_master BM,employee_master EM,location LO 
									where SUBSTRING(OH.order_no,2,5)=EM.emp_code AND OH.transaction_type IN ('SA','SH')
									AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code 
									AND LO.trans_id=OH.order_no AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '".$date_val."'";
		$rs_order_header_SA = mysqli_query($link,$sql_query_order_header_SA) or die(mysqli_error()." Error in order header SA download: ".$sql_order_header_download);
						
		while($rec_order_header_SA = mysqli_fetch_assoc($rs_order_header_SA)) 
			{ 
				$branch_code_SA=$rec_order_header_SA['branch_code'];
				$rds_code_SA=$rec_order_header_SA['rds_code'];
				$emp_code_SA=$rec_order_header_SA['emp_code'];
				$transaction_date_SA=$rec_order_header_SA['transaction_date'];
				$order_no_SA=$rec_order_header_SA['order_no'];
				$sale_rate_SA=$rec_order_header_SA['sale_rate'];
				$customer_code_SA='';
				$customer_name_SA='';
				$prod_code_SA=$rec_order_header_SA['prod_code'];
				$product_group_code_SA=$rec_order_header_SA['product_group_code'];
				$qty_SA=$rec_order_header_SA['qty'];
				$Amount_SA=$rec_order_header_SA['Amount'];
				$input_amount_SA=$rec_order_header_SA['input_amount'];
				if($input_amount_SA >0)
				{
					$Amount_SA=$input_amount_SA;
					$sale_rate_SA=0;
				}
				$VAT_SA=$rec_order_header_SA['VAT'];
				$TD_SA='';
				$transaction_type_SA=$rec_order_header_SA['transaction_type'];
				$d_instruction_SA=$rec_order_header_SA['d_instruction'];
				
				$sqlinsertmistransactionlogSA="INSERT INTO mis_transaction_log SET branch_code='".$branch_code_SA."',
											rds_code 		='".$rds_code_SA."',
											emp_code		='".$emp_code_SA."',
											trans_date		='".$transaction_date_SA."',
											trans_id		='".$order_no_SA."',
											customer_code	='".$customer_code_SA."',
											customer_name	='".$customer_name_SA."',
											sku_code		='".$prod_code_SA."',
											group_code		='".$product_group_code_SA."',
											qty				='".$qty_SA."',
											sale_rate		='".$sale_rate_SA."',
											amount			='".$Amount_SA."',
											VAT				='".$VAT_SA."',
											TD				='".$TD_SA."',
											d_instruction	='".$d_instruction_SA."',
											trans_type 		='".$transaction_type_SA."',
											download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertmistransactionlogSA))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
			}
		if($flag==1)
		{
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILURE';
		}
?>	