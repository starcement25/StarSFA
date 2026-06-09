<?php
ini_set('memory_limit', '1024M');
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("adminUtils.php");

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$branch = $_REQUEST['branch'];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#", ",", $employee);
$employee_arg = str_replace("^", "'", $employee_arg);
// print_r($employee_arg);die;
if ($branch == "all") {
	$branchcondition = '';
} else {
	$branchcondition = " AND branch_code='" . $branch . "'";
}
$competitor_name_array = array();
$sql_competitor_name = "SELECT DISTINCT competitor_name, acedns FROM competitor_group_master WHERE acedns='yes' $branchcondition AND branch_code IS NOT NULL AND branch_code!='' ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,competitor_name ASC";
// echo $sql_competitor_name;die; 
$res_competitor_name = mysqli_query($link, $sql_competitor_name);
$countcompetitor = mysqli_num_rows($res_competitor_name);
$colspanheader = 10 + ($countcompetitor * 4);
?>
<table border="1" style="border-collapse:collapse;" class="border" width="150%">
	<tr class="TDHEAD">
		<td colspan="<?php echo $colspanheader; ?>" align="center">Market Feedback Details</td>
	</tr>
	<tr class="TDHEAD_SUB">
		<td>SI. No</td>
		<td>Date of Visit</td>
		<td>Emp Code</td>
		<td>Emp Name</td>
		<td>Branch</td>
		<td>Cust Category</td>
		<td>Cust Code</td>
		<td>Cust Name</td>
		<td>Route Name</td>
		<td>Contact No</td>
		<?php
		$competitor_name_array = array();
		$acedns_array = array();
		$competitor_string='';
		while ($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)) {
			$competitor_name = $row_competitor_name['competitor_name'];
			$acedns = $row_competitor_name['acedns'];
			echo "<td align=\"center\" colspan=\"5\">" . $competitor_name . "</td>";

			array_push($competitor_name_array, $competitor_name);
			array_push($acedns_array, $acedns);
			$competitor_string .= "'" . $competitor_name . "',";
		}
		// echo "<pre>";
		// print_r($competitor_name_array);
		$_SESSION['competitor_name_passing_array'] = $competitor_name_array;
		$competitor_string = rtrim($competitor_string, ",");
		//print_r($competitor_name_array);
		?>
	</tr>
	<tr class="TDHEAD_SUB">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<?php foreach ($competitor_name_array as  $competitorheaderval) {
			echo "<td>Billing Ex</td>";
			echo "<td>Billing For</td>";
			echo "<td>WSP Ex</td>";
			echo "<td>WSP For</td>";
			echo "<td>RSP</td>";
			//echo "<td>NOD</td>";
		}
		$count = 1;
		// $sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,CM.dns_customer_code,RM.route_name,EM.emp_name,EM.dns_emp_code,
		// 						SUM(MF.PTD) PTD,SUM(MF.PTR) PTR,SUM(MF.PTC) PTC,SUM(MF.PV) PV ,SUM(MF.billing_ex_for) billing_ex_for,SUM(MF.wsp_ex_for) wsp_ex_for,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,BM.branch_name,
		// 						MF.competitor_name 
		// 						FROM market_feedback MF,customer_master CM,employee_master EM,route_master RM,branch_master BM 
		// 						WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND 
		// 						SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND  SUBSTRING(MF.market_feedback_id,3,5) IN (".$employee_arg.") AND 
		// 						(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
		// 						MF.competitor_name IN (".$competitor_string .") AND CM.branch_code=BM.branch_code 
		// 						GROUP BY SUBSTRING(MF.market_feedback_id,3,5),DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
		// 						DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,FIELD(MF.competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,MF.competitor_name ASC";

		$sql_competitor_stock = "
    SELECT
        CM.customer_name,
        CM.phone_no,
        CM.cust_type,
        CM.dns_customer_code,
        RM.route_name,
        EM.emp_name,
        EM.dns_emp_code,
        SUM(MF.PTD) AS PTD,
        SUM(MF.PTR) AS PTR,
        SUM(MF.PTC) AS PTC,
        SUM(MF.PV) AS PV,
        SUM(MF.billing_ex_for) AS billing_ex_for,
        SUM(MF.wsp_ex_for) AS wsp_ex_for,
        DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date,
        BM.branch_name,
        MF.competitor_name,
        SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
        MF.customer_code
    FROM
        market_feedback MF,
        customer_master CM,
        employee_master EM,
        route_master RM,
        branch_master BM
    WHERE
        MF.customer_code = CM.customer_code
        AND CM.route_code = RM.route_code
        AND SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
        AND SUBSTRING(MF.market_feedback_id, 3, 5) IN (" . $employee_arg . ")
        AND (DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "')
        AND MF.competitor_name IN (" . $competitor_string . ")
        AND CM.branch_code = BM.branch_code
    GROUP BY
        CM.customer_name,
        CM.phone_no,
        CM.cust_type,
        CM.dns_customer_code,
        RM.route_name,
        EM.emp_name,
        EM.dns_emp_code,
        DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y'),
        BM.branch_name,
        MF.competitor_name,
        SUBSTRING(MF.market_feedback_id, 3, 5),
        MF.customer_code
    ORDER BY
        visit_date DESC,
        FIELD(MF.competitor_name, 'STAR PSC', 'STAR PPC', 'STAR') DESC,
        MF.competitor_name ASC
";
// echo $sql_competitor_stock;die;

		//  echo $sql_competitor_stock."<br/>";
		// exit;
		$res_competitor_stock = mysqli_query($link, $sql_competitor_stock);
		// echo $res_competitor_stock;
		$count_competitor_stock = mysqli_num_rows($res_competitor_stock);
	// 	if ($count_competitor_stock > 0) {
	// 		$emp_code_array = array();
	// 		$customer_emp_date_array = array();
	// 		$date_array = array();
	// 		while ($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)) {
	// 			$dns_emp_code = $row_competitor_stock['dns_emp_code'];
	// 			$visit_date = $row_competitor_stock['visit_date'];
	// 			$dns_customer_code = $row_competitor_stock['dns_customer_code'];
	// 			$emp_name = $row_competitor_stock['emp_name'];
	// 			${'emp_name' . $dns_emp_code . $dns_customer_code . $visit_date} = $emp_name;
	// 			${'customer_name' . $dns_emp_code . $dns_customer_code . $visit_date} = $row_competitor_stock['customer_name'];
	// 			${'phone_no' . $dns_emp_code . $dns_customer_code . $visit_date} = $row_competitor_stock['phone_no'];
	// 			${'cust_type' . $dns_emp_code . $dns_customer_code . $visit_date} = $row_competitor_stock['cust_type'];
	// 			${'route_name' . $dns_emp_code . $dns_customer_code . $visit_date} = $row_competitor_stock['route_name'];
	// 			${'competitor_name_db' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['competitor_name'];
	// 			${'PTD' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['PTD'];
	// 			${'PTR' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['PTR'];
	// 			${'PTC' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['PTC'];
	// 			${'PV' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['PV'];
	// 			${'billing_ex_for' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['billing_ex_for'];
	// 			${'wsp_ex_for' . $dns_emp_code . $dns_customer_code . $visit_date . $row_competitor_stock['competitor_name']} = $row_competitor_stock['wsp_ex_for'];


	// 			${'branch_name' . $dns_emp_code . $dns_customer_code . $visit_date} = $row_competitor_stock['branch_name'];
	// 			$customer_emp_date_string = $dns_emp_code . '#' . $dns_customer_code . '#' . $visit_date;

	// 			/*$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
	
	// $sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
	// $res_emp_details = mysqli_query($link,$sql_emp_details);
	// $row_emp_details = mysqli_fetch_assoc($res_emp_details);
	// $emp_name = $row_emp_details['emp_name'];
	// $dns_emp_code = $row_emp_details['dns_emp_code'];
	
	// $sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	// $res_customer_details = mysqli_query($link,$sql_customer_details);
	// $row_customer_details = mysqli_fetch_assoc($res_customer_details);
	// $customer_name = $row_customer_details['customer_name'];
	// $dns_customer_code = $row_customer_details['dns_customer_code'];
	// $cust_type = $row_customer_details['cust_type'];*/
	// 			if (!in_array($customer_emp_date_string, $customer_emp_date_array)) {
	// 				array_push($customer_emp_date_array, $customer_emp_date_string);
	// 			}
	// 		}
	// 		for ($i = 0; $i < count($customer_emp_date_array); $i++) {
	// 			$customer_emp_date_string_val = explode("#", $customer_emp_date_array[$i]);
	// 			$dns_emp_code_val = $customer_emp_date_string_val[0];
	// 			$dns_customer_code_val = $customer_emp_date_string_val[1];
	// 			$date_val = $customer_emp_date_string_val[2];

	// 			echo "<tr>
	// 		<td>" . $count . "</td>
	// 		<td>" . $date_val . "</td>
	// 		<td>" . $dns_emp_code_val . "</td>
	// 		<td>" . ${'emp_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "</td>
	// 		<td>" . ${'branch_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "</td>
	// 		<td>" . ${'cust_type' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "</td>
	// 		<td>" . $dns_customer_code_val . "</td>
	// 		<td>" . ${'customer_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "</td>
	// 		<td>" . ${'route_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "</td>
	// 		<td>" . ${'phone_no' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "</td>
	// 		";
	// 			foreach ($competitor_name_array as  $competitorval) {
	// 				if ($competitorval == ${'competitor_name_db' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitorval}) {
	// 					$competitor_name_val = ${'competitor_name_db' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitorval};
	// 					$total_PTD_ex = ${'PTD' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitor_name_val};
	// 					if ($total_PTD_ex == 0) {
	// 						$total_PTD_ex = '-';
	// 					}
	// 					$total_PTD_for = ${'billing_ex_for' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitor_name_val};
	// 					if ($total_PTD_for == 0) {
	// 						$total_PTD_for = '-';
	// 					}
	// 					$total_PTR_ex = ${'PTR' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitor_name_val};
	// 					if ($total_PTR_ex == 0) {
	// 						$total_PTR_ex = '-';
	// 					}
	// 					$total_PTR_for = ${'wsp_ex_for' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitor_name_val};
	// 					if ($total_PTR_for == 0) {
	// 						$total_PTR_for = '-';
	// 					}
	// 					$total_PTC = ${'PTC' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitor_name_val};
	// 					if ($total_PTC == 0) {
	// 						$total_PTC = '-';
	// 					}
	// 					$total_PV = ${'PV' . $dns_emp_code_val . $dns_customer_code_val . $date_val . $competitor_name_val};
	// 				} else {
	// 					$total_PTD_ex = '-';
	// 					$total_PTD_for = '-';
	// 					$total_PTR_ex = '-';
	// 					$total_PTR_for = '-';
	// 					$total_PTC = '-';
	// 					$total_PV = '-';
	// 				}
	// 				echo "<td align=\"right\">" . $total_PTD_ex . "</td>";
	// 				echo "<td align=\"right\">" . $total_PTD_for . "</td>";
	// 				echo "<td align=\"right\">" . $total_PTR_ex . "</td>";
	// 				echo "<td align=\"right\">" . $total_PTR_for . "</td>";
	// 				echo "<td align=\"right\">" . $total_PTC . "</td>";
	// 				//echo "<td align=\"right\">".$total_PV."</td>";		
	// 			}

	// 			echo "</tr>";
	// 			$count++;
	// 		}
	// 	} 
		if($count_competitor_stock > 0){
    $data_array = array(); // Single array to hold all data
    
    while($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)){
        $dns_emp_code = $row_competitor_stock['dns_emp_code'];
        $visit_date = $row_competitor_stock['visit_date'];
        $dns_customer_code = $row_competitor_stock['dns_customer_code'];
        $competitor_name = $row_competitor_stock['competitor_name'];
        
        $key = $dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;
        
        // Initialize array if not exists
        if(!isset($data_array[$key])){
            $data_array[$key] = array(
                'emp_name' => $row_competitor_stock['emp_name'],
                'customer_name' => $row_competitor_stock['customer_name'],
                'phone_no' => $row_competitor_stock['phone_no'],
                'cust_type' => $row_competitor_stock['cust_type'],
                'route_name' => $row_competitor_stock['route_name'],
                'branch_name' => $row_competitor_stock['branch_name'],
                'competitors' => array()
            );
        }
        
        // Store competitor data
        $data_array[$key]['competitors'][$competitor_name] = array(
            'PTD' => $row_competitor_stock['PTD'],
            'PTR' => $row_competitor_stock['PTR'],
            'PTC' => $row_competitor_stock['PTC'],
            'PV' => $row_competitor_stock['PV'],
            'billing_ex_for' => $row_competitor_stock['billing_ex_for'],
            'wsp_ex_for' => $row_competitor_stock['wsp_ex_for']
        );
    }
    
    // Output rows
    foreach($data_array as $key => $row_data){
        list($dns_emp_code_val, $dns_customer_code_val, $date_val) = explode('#', $key);
        
        echo "<tr>
            <td>".$count."</td>
            <td>".$date_val."</td>
            <td>".$dns_emp_code_val."</td>
            <td>".$row_data['emp_name']."</td>
            <td>".$row_data['branch_name']."</td>
            <td>".$row_data['cust_type']."</td>
            <td>".$dns_customer_code_val."</td>
            <td>".$row_data['customer_name']."</td>
            <td>".$row_data['route_name']."</td>
            <td>".$row_data['phone_no']."</td>";
        
        foreach($competitor_name_array as $competitorval){
            if(isset($row_data['competitors'][$competitorval])){
                $comp_data = $row_data['competitors'][$competitorval];
                $total_PTD_ex = $comp_data['PTD'] == 0 ? '-' : $comp_data['PTD'];
                $total_PTD_for = $comp_data['billing_ex_for'] == 0 ? '-' : $comp_data['billing_ex_for'];
                $total_PTR_ex = $comp_data['PTR'] == 0 ? '-' : $comp_data['PTR'];
                $total_PTR_for = $comp_data['wsp_ex_for'] == 0 ? '-' : $comp_data['wsp_ex_for'];
                $total_PTC = $comp_data['PTC'] == 0 ? '-' : $comp_data['PTC'];
            } else {
                $total_PTD_ex = $total_PTD_for = $total_PTR_ex = $total_PTR_for = $total_PTC = '-';
            }
            
            echo "<td align=\"right\">".$total_PTD_ex."</td>";
            echo "<td align=\"right\">".$total_PTD_for."</td>";
            echo "<td align=\"right\">".$total_PTR_ex."</td>";
            echo "<td align=\"right\">".$total_PTR_for."</td>";
            echo "<td align=\"right\">".$total_PTC."</td>";
        }
        
        echo "</tr>";
        $count++;
    }
}
		else {
			echo "<tr><td colspan=" . $colspanheader . ">No Records found</td></tr>";
		}
		mysqli_close($link);
		?>
</table>