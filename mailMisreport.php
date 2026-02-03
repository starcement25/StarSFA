<?php
//$nick_name_string='NAPTC,KUNJ';
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
require("include/config-setup.php");
define("DB","acedns_NAPTC");
//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

require("include/config-email-setup.php");
	
	$datetoday=date("Y-m-d");
	$datetoday=date('Y-m-d', strtotime("-1 days,$date "));
	
	$messagestring='<html><body><table width="98%" align="center" border="0" cellpadding="5" cellspacing="2"  
                style="height: 150px;BORDER: #A92A61 1px solid;" >
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #FFFFFF;BACKGROUND-COLOR: #A92A61;"> 
                        <td colspan="4" align="center"><strong>Activity Report</strong></td>
                    </tr>
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;BACKGROUND-COLOR:#c0c8b0;"> 
                        <td width="10%" align="center"></td>
                        <td width="18%" align="left" style="padding-left:20px;">Customer Visited</td>
						<td width="25%" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Orders</td>
                        <td width="" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Collection</td>
                    </tr>';
	for($i=1;$i<=3;$i++)
	{
		if($i==1)
		{
			$date=date('Y-m-d');
			$date=date('Y-m-d', strtotime("-1 days,$date "));
			if($date!='')
			{
				$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			}
			$sl_value='YESTERDAY';
			$val='T';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #C6C8C7;"';
		}
		//For MTD OR Month Today
		if($i==2)
		{
			$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
			$sl_value='MTD';
			$val='MTD';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC674;"';
		}
		//For YTD OR Year Today
		if($i==3)
		{
			//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
			$date_condition=" AND ((MONTH(CURDATE()) >= 4) AND (LO.date BETWEEN date(CONCAT(YEAR(curdate()),'-04-01')) 
								AND date(CONCAT(YEAR(curdate())+1,'-03-31'))))
								OR
							 ((MONTH(CURDATE()) < 4) AND (LO.date BETWEEN date(CONCAT(YEAR(curdate())-1,'-04-01')) 
							 	AND date(CONCAT(YEAR(curdate()),'-03-31'))))";
			$sl_value='YTD';
			$val='YTD';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC6E8;"';
		}
		
	
		
		$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE SUBSTRING(LO.trans_id,1,1) NOT IN
							('A','M')".$date_condition."";
		$rescustomervisit=mysqli_query($link,$sqlcustomervisit) or die(mysqli_error()." Error in select customer visit: ".$sqlcustomervisit);
		$rowcustomervisit=mysqli_fetch_assoc($rescustomervisit);
		$no_customer_visit=$rowcustomervisit['no_visit'];
		
		$sqltotalorder="SELECT COUNT(*) AS total_order_received
							FROM location LO
							WHERE LO.trans_id LIKE 'O%' ".$date_condition."";
		$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
		$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
		$total_order=$rowtotalorder['total_order_received'];
		
		$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
							FROM payment_details PD,location LO
							WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
		$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
		$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
		$collection_received=$rowtotalcollection['total_collection_received'];
		
		$messagestring.='<tr '.$style.'> 
                          <td valign="top" align="center" style="color:#930;font-weight:bold;">'.$sl_value.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.$no_customer_visit.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.$total_order.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.number_format($collection_received,2).'</td>
                         </tr>';

	}
	
	$sqltotalcash="SELECT SUM(PH.amount) AS total_cash
					FROM payment_header PH,location LO
					WHERE  LO.trans_id LIKE 'P%' AND LO.trans_id=PH.receipt_id AND PH.cash_cheque='CASH' AND
					DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$datetoday."%'";
	$rstotalcash=mysqli_query($link,$sqltotalcash) or die(mysqli_error()." Error in total cash collection received: ".$sqltotalcash);
	$rowtotalcash=mysqli_fetch_assoc($rstotalcash);
	$totalcash=number_format($rowtotalcash['total_cash'],2);
	if($totalcash=='0.00')
	{
		$totalcash='No Collection';
	}
	
	$sqltotalcheque="SELECT SUM(PH.amount) AS total_cheque
					FROM payment_header PH,location LO
					WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PH.receipt_id AND PH.cash_cheque='CHEQUE' 
					AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$datetoday."%'";
	$rstotalcheque=mysqli_query($link,$sqltotalcheque) or die(mysqli_error()." Error in total cheque collection received: ".$sqltotalcheque);
	$rowtotalcheque=mysqli_fetch_assoc($rstotalcheque);
	$totalcheque=number_format($rowtotalcheque['total_cheque'],2);
	if($totalcheque=='0.00')
	{
		$totalcheque='No Collection';
	}

	$messagestring.='</table><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td colspan="2"><b>Yesterday\'s Collection:</b></td></tr><tr><td width="10%" align="right">CASH:</td>
					<td width=""><b>'.$totalcash.'</b></td> </tr>
					<tr><td width="10%" align="right">CHEQUE:</td><td width=""><b>'.$totalcheque.'</b></td></tr></table>
					<br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';
	
	$email_to='sales@purbanchal.in';
	//$email_to='';
	$mailsubj="GULF - NAPTC - aceDNS Field Force Activity Report";
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestring, $headers);
	mysqli_close($link);
	//--------------------------------------------End of NAPTC------------------------------------------------------------
	
	/*define("DBONE","acedns_KUNJ");
//require("include/dbcon.php");
$linkone=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DBONE,$linkone) or die("could not connect the database for invalid nick name");

//require("include/config-email-setup.php");
	
	$datetoday=date("Y-m-d");
	$datetoday=date('Y-m-d', strtotime("-1 days,$date "));
	
	$messagestringmail='<html><body><table width="98%" align="center" border="0" cellpadding="5" cellspacing="2"  
                style="height: 150px;BORDER: #A92A61 1px solid;" >
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #FFFFFF;BACKGROUND-COLOR: #A92A61;"> 
                        <td colspan="4" align="center"><strong>Activity Report</strong></td>
                    </tr>
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;BACKGROUND-COLOR:#c0c8b0;"> 
                        <td width="10%" align="center"></td>
                        <td width="18%" align="left" style="padding-left:20px;">Customer Visited</td>
						<td width="25%" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Orders</td>
                        <td width="" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Collection</td>
                    </tr>';
	for($i=1;$i<=3;$i++)
	{
		if($i==1)
		{
			$date=date('Y-m-d');
			$date=date('Y-m-d', strtotime("-1 days,$date "));
			if($date!='')
			{
				$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			}
			$sl_value='YESTERDAY';
			$val='T';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #C6C8C7;"';
		}
		//For MTD OR Month Today
		if($i==2)
		{
			$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
			$sl_value='MTD';
			$val='MTD';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC674;"';
		}
		//For YTD OR Year Today
		if($i==3)
		{
			//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
			$date_condition=" AND ((MONTH(CURDATE()) >= 4) AND (LO.date BETWEEN date(CONCAT(YEAR(curdate()),'-04-01')) 
								AND date(CONCAT(YEAR(curdate())+1,'-03-31'))))
								OR
							 ((MONTH(CURDATE()) < 4) AND (LO.date BETWEEN date(CONCAT(YEAR(curdate())-1,'-04-01')) 
							 	AND date(CONCAT(YEAR(curdate()),'-03-31'))))";
			$sl_value='YTD';
			$val='YTD';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC6E8;"';
		}
		
	
		
		$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE SUBSTRING(LO.trans_id,1,1) NOT IN
							('A','M')".$date_condition."";
		$rescustomervisit=mysqli_query($link,$sqlcustomervisit) or die(mysqli_error()." Error in select customer visit: ".$sqlcustomervisit);
		$rowcustomervisit=mysqli_fetch_assoc($rescustomervisit);
		$no_customer_visit=$rowcustomervisit['no_visit'];
		
		$sqltotalorder="SELECT COUNT(*) AS total_order_received
							FROM location LO
							WHERE LO.trans_id LIKE 'O%' ".$date_condition."";
		$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
		$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
		$total_order=$rowtotalorder['total_order_received'];
		
		$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
							FROM payment_details PD,location LO
							WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
		$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
		$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
		$collection_received=$rowtotalcollection['total_collection_received'];
		
		$messagestringmail.='<tr '.$style.'> 
                          <td valign="top" align="center" style="color:#930;font-weight:bold;">'.$sl_value.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.$no_customer_visit.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.$total_order.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.number_format($collection_received,2).'</td>
                         </tr>';

	}
	
	$sqltotalcash="SELECT SUM(PH.amount) AS total_cash
					FROM payment_header PH,location LO
					WHERE  LO.trans_id LIKE 'P%' AND LO.trans_id=PH.receipt_id AND PH.cash_cheque='CASH' AND
					DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$datetoday."%'";
	$rstotalcash=mysqli_query($link,$sqltotalcash) or die(mysqli_error()." Error in total cash collection received: ".$sqltotalcash);
	$rowtotalcash=mysqli_fetch_assoc($rstotalcash);
	$totalcash=number_format($rowtotalcash['total_cash'],2);
	if($totalcash=='0.00')
	{
		$totalcash='No Collection';
	}
	
	$sqltotalcheque="SELECT SUM(PH.amount) AS total_cheque
					FROM payment_header PH,location LO
					WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PH.receipt_id AND PH.cash_cheque='CHEQUE' 
					AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$datetoday."%'";
	$rstotalcheque=mysqli_query($link,$sqltotalcheque) or die(mysqli_error()." Error in total cheque collection received: ".$sqltotalcheque);
	$rowtotalcheque=mysqli_fetch_assoc($rstotalcheque);
	$totalcheque=number_format($rowtotalcheque['total_cheque'],2);
	if($totalcheque=='0.00')
	{
		$totalcheque='No Collection';
	}

	$messagestringmail.='</table><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td colspan="2"><b>Yesterday\'s Collection:</b></td></tr><tr><td width="10%" align="right">CASH:</td>
					<td width=""><b>'.$totalcash.'</b></td> </tr>
					<tr><td width="10%" align="right">CHEQUE:</td><td width=""><b>'.$totalcheque.'</b></td></tr></table>
					<br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';
	
	$email_to='kunj.rachit@gmail.com';
	//$email_to='';
	$mailsubj="KUNJ - aceDNS Field Force Activity Report";
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestringmail, $headers);
	mysqli_close($linkone);
	//--------------------------------------------------------End of KUNJ-------------------------------------------------------------------------------------

/*define("DBTWO","acedns_SETHS");
//require("include/dbcon.php");
$linktwo=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DBTWO,$linktwo) or die("could not connect the database for invalid nick name");

//require("include/config-email-setup.php");
	
	$datetoday=date("Y-m-d");
	$datetoday=date('Y-m-d', strtotime("-1 days,$date "));
	
	$messagestringmail='<html><body><table width="98%" align="center" border="0" cellpadding="5" cellspacing="2"  
                style="height: 150px;BORDER: #A92A61 1px solid;" >
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #FFFFFF;BACKGROUND-COLOR: #A92A61;"> 
                        <td colspan="4" align="center"><strong>Activity Report</strong></td>
                    </tr>
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;BACKGROUND-COLOR:#c0c8b0;"> 
                        <td width="10%" align="center"></td>
                        <td width="18%" align="left" style="padding-left:20px;">Customer Visited</td>
						<td width="25%" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Orders</td>
                        <td width="" align="left" style="padding-left:20px;"><span style="padding-left:10px;">Collection</td>
                    </tr>';
	for($i=1;$i<=3;$i++)
	{
		if($i==1)
		{
			$date=date('Y-m-d');
			$date=date('Y-m-d', strtotime("-1 days,$date "));
			if($date!='')
			{
				$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			}
			$sl_value='YESTERDAY';
			$val='T';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #C6C8C7;"';
		}
		//For MTD OR Month Today
		if($i==2)
		{
			$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
			$sl_value='MTD';
			$val='MTD';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC674;"';
		}
		//For YTD OR Year Today
		if($i==3)
		{
			//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
			$date_condition=" AND ((MONTH(CURDATE()) >= 4) AND (LO.date BETWEEN date(CONCAT(YEAR(curdate()),'-04-01')) 
								AND date(CONCAT(YEAR(curdate())+1,'-03-31'))))
								OR
							 ((MONTH(CURDATE()) < 4) AND (LO.date BETWEEN date(CONCAT(YEAR(curdate())-1,'-04-01')) 
							 	AND date(CONCAT(YEAR(curdate()),'-03-31'))))";
			$sl_value='YTD';
			$val='YTD';
			$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC6E8;"';
		}
		
	
		
		$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE SUBSTRING(LO.trans_id,1,1) NOT IN
							('A','M')".$date_condition."";
		$rescustomervisit=mysqli_query($link,$sqlcustomervisit) or die(mysqli_error()." Error in select customer visit: ".$sqlcustomervisit);
		$rowcustomervisit=mysqli_fetch_assoc($rescustomervisit);
		$no_customer_visit=$rowcustomervisit['no_visit'];
		
		$sqltotalorder="SELECT COUNT(*) AS total_order_received
							FROM location LO
							WHERE LO.trans_id LIKE 'O%' ".$date_condition."";
		$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
		$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
		$total_order=$rowtotalorder['total_order_received'];
		
		$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
							FROM payment_details PD,location LO
							WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
		$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
		$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
		$collection_received=$rowtotalcollection['total_collection_received'];
		
		$messagestringmail.='<tr '.$style.'> 
                          <td valign="top" align="center" style="color:#930;font-weight:bold;">'.$sl_value.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.$no_customer_visit.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.$total_order.'</td>
							<td align="right" valign="top" style="padding-left:20px;">'.number_format($collection_received,2).'</td>
                         </tr>';

	}
	
	$sqltotalcash="SELECT SUM(PH.amount) AS total_cash
					FROM payment_header PH,location LO
					WHERE  LO.trans_id LIKE 'P%' AND LO.trans_id=PH.receipt_id AND PH.cash_cheque='CASH' AND
					DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$datetoday."%'";
	$rstotalcash=mysqli_query($link,$sqltotalcash) or die(mysqli_error()." Error in total cash collection received: ".$sqltotalcash);
	$rowtotalcash=mysqli_fetch_assoc($rstotalcash);
	$totalcash=number_format($rowtotalcash['total_cash'],2);
	if($totalcash=='0.00')
	{
		$totalcash='No Collection';
	}
	
	$sqltotalcheque="SELECT SUM(PH.amount) AS total_cheque
					FROM payment_header PH,location LO
					WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PH.receipt_id AND PH.cash_cheque='CHEQUE' 
					AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$datetoday."%'";
	$rstotalcheque=mysqli_query($link,$sqltotalcheque) or die(mysqli_error()." Error in total cheque collection received: ".$sqltotalcheque);
	$rowtotalcheque=mysqli_fetch_assoc($rstotalcheque);
	$totalcheque=number_format($rowtotalcheque['total_cheque'],2);
	if($totalcheque=='0.00')
	{
		$totalcheque='No Collection';
	}

	$messagestringmail.='</table><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td colspan="2"><b>Yesterday\'s Collection:</b></td></tr><tr><td width="10%" align="right">CASH:</td>
					<td width=""><b>'.$totalcash.'</b></td> </tr>
					<tr><td width="10%" align="right">CHEQUE:</td><td width=""><b>'.$totalcheque.'</b></td></tr></table>
					<br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';
	
	$email_to='clrathi.unitex@gmail.com,coralak@gmail.com,ashokrathi@universalpoplin.com';
	//$email_to='';
	$mailsubj="SETHS - aceDNS Field Force Activity Report";
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestringmail, $headers);
	mysqli_close($linktwo);*/
?>
