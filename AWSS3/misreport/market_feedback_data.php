<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require("datefunction.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$product_group = $_REQUEST['product_group'];
	$route_code = $_REQUEST['route_code'];
	
	$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
	$res_route_name = mysql_query($sql_route_name);
	$row_route_name = mysql_fetch_array($res_route_name);
	$roue_name = $row_route_name['route_name'];
	
	$sql_market_feedback = "SELECT DATE_FORMAT(SUBSTRING(market_feedback_id,-14,8),'%d-%m-%Y') as date_selected, competitor_name, SUM(PTD), SUM(PTC), SUM(PTR) FROM market_feedback WHERE DATE_FORMAT(SUBSTRING(market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' AND route_code LIKE '%".$route_code."%' AND product_group LIKE '%".$product_group."%' GROUP BY competitor_name ORDER BY DATE_FORMAT(SUBSTRING(market_feedback_id,-14,8),'%Y-%m-%d')";
	$res_market_feedback = mysql_query($sql_market_feedback);
	$total_rows = mysql_num_rows($res_market_feedback);
	
	if($total_rows>0)
	{
		if($_GET['export'] == 'true')
		{
			$header_data = "\t\t".$product_group."-".$roue_name."\t\t\n";
			$header_data .= "SI"."\t"."Competitor Name"."\t"."PTD"."\t"."PTR"."\t"."PTC";
			$count = 1;
			$date_array = array();
			$res_market_feedback = mysql_query($sql_market_feedback);
			while($row_market_feedback = mysql_fetch_array($res_market_feedback))
			{
				$date_selected = $row_market_feedback['date_selected'];
				$competitor_name = $row_market_feedback['competitor_name'];
				$ptd = number_format($row_market_feedback['SUM(PTD)'],2);
				$ptc = number_format($row_market_feedback['SUM(PTC)'],2);
				$ptr = number_format($row_market_feedback['SUM(PTR)'],2);
				
				if(!in_array($date_selected,$date_array))
				{
					array_push($date_array, $date_selected);
					$contents .= "\t\t".$date_selected."\t\t\n";
				}
				
				$contents .= $count."\t".$competitor_name."\t".$ptd."\t".$ptc."\t".$ptr."\n";
				$count++;
			}
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=Market_Feedback_Report.xls"); 
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo ucwords($header_data)."\n".$contents;
		}
		else
		{
			$count = 1;
			$date_array = array();
			echo "<table width=\"100%\" border=\"1\" class=\"border\" style=\"border-collapse:collapse;\">
				  <tr class=\"TDHEAD\">
					<td colspan=\"5\" align=\"center\">$product_group - $roue_name</td>
				  </tr>
				  <tr class=\"TDHEAD_SUB\" align=\"center\">
					<td>SI</td>
					<td>Competitor Name</td>
					<td>PTD</td>
					<td>PTR</td>
					<td>PTC</td>
				  </tr>";
			$res_market_feedback = mysql_query($sql_market_feedback);
			while($row_market_feedback = mysql_fetch_array($res_market_feedback))
			{
				$date_selected = $row_market_feedback['date_selected'];
				$competitor_name = $row_market_feedback['competitor_name'];
				$ptd = number_format($row_market_feedback['SUM(PTD)'],2);
				$ptc = number_format($row_market_feedback['SUM(PTC)'],2);
				$ptr = number_format($row_market_feedback['SUM(PTR)'],2);
				if(!in_array($date_selected,$date_array))
				{
					array_push($date_array, $date_selected);
					echo "<tr>
							<td align=\"center\" colspan=\"5\" style=\"background:#CCCCCC; font-weight:bold;\">".$date_selected."</td>
						  </tr>";
				}
				echo "<tr>
						<td>".$count."</td>
						<td>".$competitor_name."</td>
						<td align=\"right\">".$ptd."</td>
						<td align=\"right\">".$ptc."</td>
						<td align=\"right\">".$ptr."</td>
					  </tr>";
				$count++;
				//$product_group[$route_code]
			}
			echo "</table>";
		}
	}
	else
	{
		echo "<font color='red'><strong>No records found</strong></font>";
	}
mysql_close($link);	
?>