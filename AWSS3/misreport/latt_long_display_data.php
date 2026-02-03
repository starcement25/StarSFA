<?php
ob_start();
session_start();
require("adminUtils_school.php");
$curdate=date('Y-m-d');
	 
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.district,CM.phone_no,CM.zone,CM.base_latt,
							CM.base_longi FROM customer_master CM
							WHERE CM.customer_name <> '' AND CM.base_latt >0 AND  CM.base_longi >0 ORDER BY CM.customer_name ASC";
	$rescustomerdetails = mysql_query($sqlcustomerdetails);
	$totalcustomerdetails = mysql_num_rows($rescustomerdetails);
	if($totalcustomerdetails >0){
		$count = 1;
		?>
         <form name="frm_opts_multiple" action="customer_latt_long_edit.php" method="post" >
        <input type="hidden" name="mode" value="editcustomer_multiple">
		<table border="1" style="border-collapse:collapse;" class="border" width="90%">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Lattitude Longitude List</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="5%">SI</td>
            <td width="15%">District</td>
			<td width="15%">Block</td>
            <td width="10%">Phone No</td>
            <td width="15%">School Code</td>
            <td width="20%">School Name</td>
            <td width="10%">Lattitude</td>
            <td width="10%">Longitude</td>
		  </tr>
		<?php
		
		while($rowcustomerdetails = mysql_fetch_array($rescustomerdetails)){
			//$emp_name = $rowcustomerdetails['emp_name'];
			$customer_code = $rowcustomerdetails['customer_code'];
			$dns_customer_code = $rowcustomerdetails['dns_customer_code'];
			$customer_name = $rowcustomerdetails['customer_name'];
			$phone_no=$rowcustomerdetails['phone_no'];
			$district=$rowcustomerdetails['district'];
			$zone=$rowcustomerdetails['zone'];
			$base_latt=$rowcustomerdetails['base_latt'];
			$base_longi=$rowcustomerdetails['base_longi'];
			echo "<tr>
					<td width=\"5%\">".$count."</td>
					<td width=\"15%\">".$district."</td>
					<td width=\"15%\">".$zone."</td>
					<td width=\"10%\">".$phone_no."</td>
					<td width=\"15%\">".$dns_customer_code."</td>
					<td width=\"20%\">".$customer_name."</td>
					<td width=\"10%\">".$base_latt."</td>
					<td width=\"10%\">".$base_longi."</td>
				  </tr>";
				  
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
