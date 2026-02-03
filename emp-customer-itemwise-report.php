<?php
	$nick_name='VIPL';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	require("include/config-setup.php");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	require("include/config-email-setup.php");
	
		$date=date('d',strtotime('+330 minute'));
		$month=date('m',strtotime('+330 minute'));
		$year=date('Y',strtotime('+330 minute'));
		
		$hour=date('H',strtotime('+330 minute'));
		$minute=date('i',strtotime('+330 minute'));
		$second=date('s',strtotime('+330 minute'));
		
		/*$date=gmdate('d',strtotime('-900 minute'));
		$month=gmdate('m',strtotime('-900 minute'));
		$year=gmdate('Y',strtotime('-900 minute'));
		
		$hour=gmdate('H',strtotime('-900 minute'));
		$minute=gmdate('i',strtotime('-900 minute'));
		$second=gmdate('s',strtotime('-900 minute'));*/
		
		$val=$date.'-'.$month.'-'.$year.' @'.$hour.':'.$minute.':'.$second;
		
		$showing_date=$date.'/'.$month.'/'.$year;
		$val_database=$year.'-'.$month.'-'.$date;
		$order_no_like=$year.$month.$date;
		$ordervalue='0';
		
		$sqlemployee="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CH WHERE CH.deviceid!='' AND EM.emp_code !='C0007' 
						AND CH.emp_code=EM.emp_code ORDER BY EM.emp_name ASC";
		$resemployee=mysqli_query($link,$sqlemployee) or die(mysqli_error()." Error in select employee: ".$sqlemployee);
        while($rowemployee=mysqli_fetch_assoc($resemployee))
        {
			$emp_name=$rowemployee['emp_name'];
			$emp_code=$rowemployee['emp_code'];								
		
		$sqltrans="SELECT CM.customer_name,OH.customer_code FROM location LO,order_header OH,customer_master CM  
					WHERE LO.emp_code='".$emp_code."' AND LO.trans_id LIKE 'O%' AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$val_database."%'
		 			AND LO.trans_id=OH.order_no AND OH.customer_code=CM.customer_code GROUP BY OH.customer_code  
					UNION
					SELECT CM.customer_name,OH.customer_code FROM location LO,order_header OH,prospective_customer_master CM  
					WHERE LO.emp_code='".$emp_code."' AND LO.trans_id LIKE 'O%' AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$val_database."%'
		 			AND LO.trans_id=OH.order_no AND OH.customer_code=CM.customer_code GROUP BY OH.customer_code  
					";
		 $restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id for employee wise activity: ".$sqltrans);
		 $counttrans=mysqli_num_rows($restrans);
		 if($counttrans > 0){
			 $ordervalue='1';
			 $mailbody.="<strong>Employee Name: ".$emp_name."</strong><br /><br />";				

		 $rowval=""; 
        while($rowtrans=mysqli_fetch_assoc($restrans))
        {				
			$customer_name=$rowtrans['customer_name'];
			$customer_code=$rowtrans['customer_code'];
			/*$sqlroutename="SELECT RM.route_name FROM route_master RM,route_plan RP WHERE 
							RM.route_code=RP.route_code AND RP.visit_date='".$val_database."' AND RP.emp_code='".$emp_code."'";
			$rsroutename=mysqli_query($link,$sqlroutename);
			$rowroutename=mysqli_fetch_assoc($rsroutename);
			$route_name=$rowroutename['route_name'];
			$mailbody.="<strong>Area:<strong> ".$route_name."<br /><br />";*/	
			$mailbody.="<strong>Customer Name: ".$customer_name."</strong><br /><br />";
			
			$mailbody.='
			<table width="60%" align="center" border="1" cellpadding="5" cellspacing="2" >
				<tr> 
					<td width="" align="left" style="padding-left:20px;">SKU</td>
					<td width="14%" align="left" style="padding-left:20px;">Quantity</td>
					<td width="14%" align="left" style="padding-left:20px;">Sale Rate</td>
					<td width="14%" align="left" style="padding-left:20px;">Amount</td>
				</tr> ';
			 $sqlorderdetails="SELECT PM.prod_desc,PM.prod_code,OD.qty,OD.sale_rate
								FROM  product_master PM,order_details OD,order_header OH
								WHERE OD.order_no=OH.order_no AND OH.customer_code='".$customer_code."' AND 
								OD.sku_code=PM.prod_code AND OH.order_no LIKE '%O".$emp_code.$order_no_like."%' ORDER BY OH.order_no DESC";
								
			$rsorderdetails=mysqli_query($link,$sqlorderdetails) or die(mysqli_error()." Error in select order product details: ".$sqlorderdetails);
			$cnt=1;
			while($roworderdetails=mysqli_fetch_assoc($rsorderdetails))
			{
				//$product_group_name=$roworderdetails['product_group_name'];
				//$product_sub_group_name=$roworderdetails['product_sub_group_name'];
				$prod_desc=$roworderdetails['prod_desc'];
				$qty=$roworderdetails['qty'];
				$prod_code=$roworderdetails['prod_code'];
				$prod_mrp=$roworderdetails['sale_rate'];
				
				$amount=($prod_mrp*$qty);
					
				$mailbody.="<tr> 
							<td align=\"left\" valign=\"top\" style=\"padding-left:5px;BORDER: #A92A61 1px solid;\">".$prod_desc."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$qty."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($prod_mrp,2)."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($amount,2)."</td>
						</tr>";
		  }
			$mailbody.='</table><br />';				
		}
			  $mailbody.='<hr style="border-top: dotted 1px;" /><br />';
	  }
	}
	if($ordervalue=='0'){
		$mailbody.='No Order.';
	}
	$mailbody.="<br /><br />Powered By Forcepower<br /></body></html>";
	
	$mailsubj="VIPL BI-HOURLY Sales Order ".$val;
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
	$email='suraj@vibrantinfocom.net,sundeep@vibrantinfocom.net';				
	if(@mail($email, $mailsubj, $mailbody, $headers,'-facedns@acedns.in'))
		{
			echo 'SUCCESSFULL';
		}
?>		