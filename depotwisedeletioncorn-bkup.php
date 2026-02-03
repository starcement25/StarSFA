<?php
//set_time_limit(1000);
//ini_set('memory_limit', '-1');
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");

require("include/config-setup.php");
define("DB","acedns_RKBK");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

require("include/config-email-setup.php");
	delete_transaction();
 	//For deleting transaction details
	function delete_transaction()
	{
		$rds_name_string='C/0000237,C/0000240,C/0000236,C/0000234,C/0000241,C/0000243,C/0000238';
		$rds_name_array=explode(',',$rds_name_string);
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		foreach($rds_name_array as $rds_name_values){
		//mysqli_query($link,"SET AUTOCOMMIT=1");
		//mysqli_query($link,"START TRANSACTION");
		$grn_no_array=array();
		$receiver_code_array=array();

		$rds_name_query=$rds_name_values;
		$sqlemp="SELECT emp_code,rds_name FROM rds_master WHERE rds_code='".$rds_name_query."'";
		$rsemp=mysqli_query($link,$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);
		$emp_code=$rowemp['emp_code'];
		$rds_name=$rowemp['rds_name'];

		$rds_name_delete=$rds_name;
		$branch_name='B0003';
		$from_date='01-04-2015';
		$from_date=date('Y-m-d',strtotime($from_date));
		$to_date='01-12-2015';
		$to_date=date('Y-m-d',strtotime($to_date));
		$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_name."'";
		$rsbranch=mysqli_query($link,$sqlbranch);
		$rowbranch=mysqli_fetch_assoc($rsbranch);
		$branch_name_mail=$rowbranch['branch_name'];

		$sqlinsertactivitylog="INSERT INTO activity_log SET 
							transaction_id='',
							branch_code='".$branch_name."',
							rds_code='".$rds_name_query."',
							start_date='".$from_date."',
							end_date='".$to_date."',
							request_datetime=CURRENT_TIMESTAMP(),
							updated_flag='0',
							operation_type='deletion c0007 182.75.112.106'";
	 	if(mysqli_query($link,$sqlinsertactivitylog))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}	

		if($from_date!='' && $to_date!='')
		{
			$date_condition=" AND DATE_FORMAT(location.date,'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(location.date,'%Y-%m-%d') <='".$to_date."'";
			$date_condition_ST=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(LO.date,'%Y-%m-%d') <='".$to_date."'";
			$date_condition_mis=" AND DATE_FORMAT(trans_date,'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(trans_date,'%Y-%m-%d') <='".$to_date."'";
		}
		else
		{
			$date_condition='';
		}
		//FOR ST transaction type
		$sqlgrndetails="SELECT DISTINCT GIT.grn_no,GIT.receiver_code  FROM location LO,
						order_header OH,goods_in_transit GIT WHERE 
						LO.trans_id=OH.order_no AND OH.order_no=GIT.grn_no AND OH.transaction_type='ST'
					   AND LO.emp_code='".$emp_code."' ".$date_condition_ST."";
		$rsgrndetails=mysqli_query($link,$sqlgrndetails);
		while($rowgrndetails=mysqli_fetch_assoc($rsgrndetails))
		{
			$receiver_code=$rowgrndetails['receiver_code'];
			$grn_no=$rowgrndetails['grn_no'];
		    $sqlinsertactivitylogST="INSERT INTO activity_log SET 
									transaction_id='".$grn_no."',
									branch_code='',
									rds_code='',
									receiver_code='".$receiver_code."',
									start_date='0000-00-00',
									end_date='0000-00-00',
									request_datetime=CURRENT_TIMESTAMP(),
									updated_flag='0',
									operation_type='deletion c0007 182.75.112.106'";
			if(mysqli_query($link,$sqlinsertactivitylogST))
			{
				$flag=1;
				array_push($grn_no_array,$grn_no);
				array_push($receiver_code_array,$receiver_code);
			}
			else
			{
				$flag=0;
			}
		}
		//End for ST transaction type
		
		//For BT transaction type
		$sqlgrndetailsBT="SELECT GIT.grn_no,GIT.prod_code,GIT.receiver_code  FROM location LO,
						 order_header OH,goods_in_transit GIT WHERE 
						 LO.trans_id=OH.order_no AND OH.order_no=GIT.order_no AND OH.transaction_type='BT'
					     AND LO.emp_code='".$emp_code."' ".$date_condition_ST."";
		$rsgrndetailsBT=mysqli_query($link,$sqlgrndetailsBT);
		while($rowgrndetailsBT=mysqli_fetch_assoc($rsgrndetailsBT))
		{
			$grn_no_BT=$rowgrndetailsBT['grn_no'];
			$receiver_code_BT=$rowgrndetailsBT['receiver_code'];
			$prod_code=$rowgrndetailsBT['prod_code'];
			
			$sqlupdategrnST="UPDATE goods_in_transit SET status='0' WHERE grn_no='".$grn_no_BT."' AND 
							receiver_code='".$receiver_code_BT."' AND  prod_code='".$prod_code."' AND transaction_type='ST'";
			if(mysqli_query($link,$sqlupdategrnST))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}				
			
		}
		//End for BT transaction type
		
		$sqlgrndelete="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
						order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
					  AND order_header.order_no=order_details.order_no AND location.trans_id=goods_in_transit.grn_no  
					  WHERE location.emp_code='".$emp_code."' AND goods_in_transit.transaction_type='ST' ".$date_condition."";
		if(mysqli_query($link,$sqlgrndelete))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		
		$sqlgrnreceiverdelete="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
							order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
					  		AND order_header.order_no=order_details.order_no AND location.trans_id=goods_in_transit.order_no  
					  		WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		if(mysqli_query($link,$sqlgrnreceiverdelete))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		
		$sqlquerydeleteorder="DELETE location,order_header,order_details FROM location INNER JOIN 
							 order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
							 AND order_header.order_no=order_details.order_no WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		$sqlquerydeletepayment="DELETE location,payment_header,payment_details FROM location INNER JOIN 
								payment_header INNER JOIN payment_details ON location.trans_id=payment_header.receipt_id 
								AND payment_header.receipt_id=payment_details.receipt_id 
								WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		$sqlquerydeleteexpense="DELETE location,freight_expenses FROM location INNER JOIN 
								freight_expenses ON location.trans_id=freight_expenses.freight_exp_trans_id 
								WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		$sqlquerydeletemis="DELETE FROM mis_transaction_log WHERE emp_code='".$emp_code."' ".$date_condition_mis."";
						
		if(mysqli_query($link,$sqlquerydeleteorder) && mysqli_query($link,$sqlquerydeletepayment) && mysqli_query($link,$sqlquerydeleteexpense) && mysqli_query($link,$sqlquerydeletemis))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		if($flag==1)
		{
			//mysqli_query($link,"COMMIT");
			$sqlupdateactivitylog="UPDATE activity_log SET 
								   updated_flag='1',
								   update_datetime=CURRENT_TIMESTAMP()
								   WHERE branch_code='".$branch_name."' AND rds_code='".$rds_name_query."' AND start_date='".$from_date."' AND end_date='".$to_date."'";
			mysqli_query($link,$sqlupdateactivitylog);
			
			for($cnt=0;$cnt<count($grn_no_array);$cnt++)
			{
				$sqlupdateactivitylogST="UPDATE activity_log SET 
									   updated_flag='1',
									   update_datetime=CURRENT_TIMESTAMP()
									   WHERE transaction_id='".$grn_no_array[$cnt]."' AND receiver_code='".$receiver_code_array[$cnt]."'";
				mysqli_query($link,$sqlupdateactivitylogST);
			}
			//$GLOBALS['err_msg']="Transaction data of the $rds_name deletion successful";
			$emailsubj="RKBK transaction deletion confirmation on ".$date."-".$month."-".$year." @".$hour."-".$minute."-".$second.' hrs.';
			$emailbody = "<html><head><title>Deletion</title></head>
										<body><br /><table>
										<b>Branch: " .$branch_name_mail. "</b><br /><br />
										<b>Depot Name: " .$rds_name_delete. "</b><br /><br />
										<b>From date: " .$from_date. "</b><br /><br />
										<b>To date: " .$to_date. "</b><br /><br />
										<b>Deletion By: c0007</b><br /><br />
										</table><br /><br />Powered By aceDNS</body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n".
						'X-Mailer: PHP/' . phpversion();
			$email_to='';				
			mail($email_to, $emailsubj, $emailbody, $headers,'-facedns@acedns.in');
		}
	  }
	}
?>