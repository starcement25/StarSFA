<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
	?>
    <center>
    <div id="display" align="center" style="width:98%; max-height:340px; overflow-y:scroll;">
    <?php
	/*$current_date = date('Y-m-d');
	if($_SESSION['admin_login']=="admin")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 AND EM.acedns!='N' ";
		$customer_condition=" 1 ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
		$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
	}*/

$sql_menu_id = "SELECT row_id FROM `survey_input` WHERE  layout_name = 'New Sales' AND type = 'menu'";
$res_menu_id = mysql_query($sql_menu_id);
$row_menu_id = mysql_fetch_array($res_menu_id);
$menu_id = $row_menu_id['row_id'];

$sql_row_id = "SELECT row_id FROM `survey_input` WHERE  menu_id = '".$menu_id."' AND type != 'menu' LIMIT 0,1";
$res_row_id = mysql_query($sql_row_id);
$row_row_id = mysql_fetch_array($res_row_id);
$row_id = $row_row_id['row_id'];

$count = 1;
$sql_survey_id = "SELECT DISTINCT survey_id FROM survey_output WHERE row_id = '".$row_id."' AND type = 'NEW'";
$res_survey_id = mysql_query($sql_survey_id);
$total_row_check = mysql_num_rows($res_survey_id);
if($total_row_check>0){
	?>
    <table class="border" border="1" style="border-collapse:collapse;" border="100%">
      <tr class="TDHEAD_SUB" align="center">
      	<td colspan="4"></td>
        <td colspan="3">Lead Generation</td>
        <td colspan="2">Lead Conversion</td>
        <td>Yes Selection in Lead Conversion</td>
        <td>No Selection</td>
      </tr>
      <tr class="TDHEAD" align="center">
      	<td>SI</td>
        <td>IHB Name</td>
        <td>Contact Number</td>
        <td>Address</td>
        <td>Dealer</td>
        <td>Sub Dealer</td>
        <td>Self</td>
        <td>Yes</td>
        <td>No</td>
        <td>Qty</td>
        <td>Remarks</td>
      </tr>
    <?php
	$res_survey_id = mysql_query($sql_survey_id);
	while($row_survey_id = mysql_fetch_array($res_survey_id)){
		$survey_id = $row_survey_id['survey_id'];
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$value = $row_survey_details['value'];
			
			if($row_id == 'RA006')
				$ihb_name = $row_survey_details['value'];
			if($row_id == 'RA007')
				$ihb_cntct_numbr = $row_survey_details['value'];
			if($row_id == 'RA008')
				$ihb_address = $row_survey_details['value'];
			if($row_id == 'RA009'){
				$lead_conversn_yes = '';
				$lead_conversn_no = '';
				$qty = '';
				$remarks = '';
				
				$lead_conversn_sales = $row_survey_details['value'];
				$lead_conversn_array = explode(":",$lead_conversn_sales);
				if($lead_conversn_array[0] == 'YES'){
					$lead_conversn_yes = 'YES';
					$qty = $lead_conversn_array[1];
				}
				else if($lead_conversn_array[0] == 'NO'){
					$lead_conversn_no = 'NO';
					$remarks = $lead_conversn_array[1];
				}
			}
			if($row_id == 'RA028'){
				$sub_dealer = '';
				$dealer = '';
				$self = '';
				$lead_genrtd = $row_survey_details['value'];
				$lead_genrtd_array = explode(":",$lead_genrtd);
				if($lead_genrtd_array[0] == 'EXISTING'){
					$sql_cust_details = "SELECT customer_name, cust_type FROM customer_master WHERE customer_code = '".$lead_genrtd_array[1]."'";
					$res_cust_details = mysql_query($sql_cust_details);
					$row_cust_details = mysql_fetch_array($res_cust_details);
					$cust_name = $row_cust_details['customer_name'];
					$cust_type = $row_cust_details['cust_type'];
					
					if($cust_type == 'R')
						$sub_dealer = $cust_name;
					else if($cust_type == 'D')
						$dealer = $cust_name;
				}
				else{
					$sub_dealer = '';
					$dealer = '';
					$self = 'SELF';
				}
			}
		}
		echo "<tr>
				<td>".$count."</td>
				<td>".$ihb_name."</td>
				<td>".$ihb_cntct_numbr."</td>
				<td>".$ihb_address."</td>
				<td>".$dealer."</td>
				<td>".$sub_dealer."</td>
				<td>".$self."</td>
				<td>".$lead_conversn_yes."</td>
				<td>".$lead_conversn_no."</td>
				<td align=\"right\">".$qty."</td>
				<td>".$remarks."</td>
			  </tr>";
		$count++;
	}
	?>
    </table>
    <?php
}
else{
	echo "No Records Found";
}
?>
</div>
</center>
<?php
}
?>

