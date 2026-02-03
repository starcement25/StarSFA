<?php
//$nick_name_string='NAPTC,KUNJ';
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");

require("include/config-setup.php");
define("DB","acedns_AMPL");
//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

require("include/config-email-setup.php");
	
	$datetoday=date("Y-m-d");
	$dateprev=date('d-m-Y', strtotime("-1 days,$datetoday "));
	
	$messagestring='<html><body><table width="98%" align="center" border="0" cellpadding="5" cellspacing="2"  
                style="height: 150px;BORDER: #A92A61 1px solid;" >
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;"> 
                        <td colspan="6" align="center"><strong>ACEDNS DAILY CALL REPORT SUMMARY - AMPL</strong></td>
                    </tr>
					 <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;"> 
                        <td colspan="6" align="center"><strong>DATE : '.$dateprev.'</strong></td>
                    </tr>
                    <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;BACKGROUND-COLOR:#c0c8b0;"> 
                        <td width="" align="left">Sno</td>
						<td width="18%" align="left" style="padding-left:20px;">Executive name</td>
                        <td width="18%" align="left" style="padding-left:20px;">Number of calls</td>
						<td width="18%" align="left" style="padding-left:20px;">Order collected</td>
                        <td width="18%" align="left" style="padding-left:20px;">O/S collected</td>
						<td width="18%" align="left" style="padding-left:20px;">Remarks</td>
                    </tr>';
	
		$date=date('Y-m-d', strtotime("-1 days,$datetoday "));
		if($date!='')
		{
			$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
		}
		$style='style="COLOR: #000000;BACKGROUND-COLOR: #C6C8C7;"';
		$styleone='style="COLOR: #000000;BACKGROUND-COLOR: #8BC674;"';
		
		$sqltrans="SELECT EM.emp_name,EM.emp_code FROM 
					location LO,employee_master EM 
					WHERE EM.emp_code=LO.emp_code AND SUBSTRING(LO.trans_id,1,1) NOT IN
							('A','D') ".$date_condition." GROUP BY LO.emp_code
					ORDER BY EM.emp_name ASC ";
		$restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id: ".$sqltrans);
		$count=mysqli_num_rows($restrans);
		if($count>0)
		{
			$sl_value=1;
			while($rectrans = mysqli_fetch_assoc($restrans)) 
			{
					$emp_code=$rectrans['emp_code'];
					$emp_name=$rectrans['emp_name'];
					$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE SUBSTRING(LO.trans_id,1,1) NOT IN
							('A','D') AND LO.emp_code='".$emp_code."' ".$date_condition."";
					$rescustomervisit=mysqli_query($link,$sqlcustomervisit) or die(mysqli_error()." Error in select customer visit: ".$sqlcustomervisit);
					$rowcustomervisit=mysqli_fetch_assoc($rescustomervisit);
					$no_customer_visit=$rowcustomervisit['no_visit'];
					
					$sqltotalorder="SELECT COUNT(*) AS total_order_received
										FROM location LO
										WHERE LO.trans_id LIKE 'O%' AND LO.emp_code='".$emp_code."' ".$date_condition."";
					$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
					$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
					$total_order=$rowtotalorder['total_order_received'];
					
					$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
										FROM payment_details PD,location LO
										WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id AND LO.emp_code='".$emp_code."' ".$date_condition."";
					$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
					$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
					$collection_received=$rowtotalcollection['total_collection_received'];
					
					if($sl_value%2=='0')
					{
						$style='style="COLOR: #000000;BACKGROUND-COLOR: #C6C8C7;"';
					}
					else
					{
						$style='style="COLOR: #000000;BACKGROUND-COLOR: #8BC674;"';
					}
					$messagestring.='<tr '.$style.'> 
									  <td valign="top" align="center" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.$sl_value.'</td>
									  <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.$emp_name.'</td>
										<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.$no_customer_visit.'</td>
										<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.$total_order.'</td>
										<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.number_format($collection_received,2).'</td>
										<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"></td>
									 </tr>';
				$sl_value++;					 
			}
		}
		else
		{
			$messagestring.='<tr>
							<td align="left" style="BORDER: #000000 1px solid;" colspan="6"><strong>No operations performed</strong></td>
						</tr>';
		}
		$messagestring.='</table><br /><br /><table  width="98%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td align="center">Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';
		echo $messagestring;
	mysqli_close($link);
?>
