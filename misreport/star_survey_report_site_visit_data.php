<?php
ob_start();

session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$visit_status = $_REQUEST['visit_status'];
$survey_date = $_REQUEST['survey_date'];
$survey_type = $_REQUEST['survey_type'];

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";


if(strpos($employee,"#") == FALSE){
	$new_emp_code = str_replace("^","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

?>
<table border="1" style="border-collapse:collapse;" class="border" width="180%">
  <tr class="TDHEAD">
  	<td colspan="80"><?php echo $header_string; ?></td>
  </tr>
  <tr class="TDHEAD_SUB" align="center">
  	<td width="5%">Date</td>
    <td>Emp Code</td>
    <td>Emp Name</td>
    <td>Recommendation ID</td>
    <td>Star Lifting (In Bags) </td>
  	<td>Branch</td>
    <td>Place</td>
    <td>Name</td>
    <td>Category</td>
    <td>Contact no</td>
    <td>Address</td>
    <td>Brand in Use</td>
    <?php
	/*$competitorname_array=array(); 
	$sqlcompetitor="SELECT DISTINCT competitor_name FROM competitor_group_master ORDER BY competitor_name ASC ";
	$rscompetitor=mysqli_query($link,$sqlcompetitor);
	while($rowcompetitor=mysqli_fetch_assoc($rscompetitor))
	{
		echo '<td>'.strtoupper($rowcompetitor['competitor_name']).'</td>';
		if(!in_array($rowcompetitor['competitor_name'],$competitorname_array))
		{
			array_push($competitorname_array,$rowcompetitor['competitor_name']);
		}
	}*/
	?>
    <td>Reason of Buying</td>
    <td>Source of Purchase</td>
    <td>Rate/Bag</td>
  	<td>Segment</td>
    <td>Type of Structure</td>
    <td>Current Stage</td>
    <td>Total Consumption (Bag)</td>
    <td>Balance Consumption (Bag)</td>
    <td>Visit Status</td>
    <td>Order Placed to</td>
    <td>Remarks</td>
    <td>Photo Link</td>
  </tr>
<?php
$visit_status_arg_list = str_replace("^","'",$visit_status);
$visit_status_arg_list = str_replace("-",",",$visit_status_arg_list);

if(stristr($visit_status_arg_list,"'") == true){
	$condition = " value IN(".$visit_status_arg_list.") ";
}
else{
	$condition = " value = '".$visit_status."' ";
}

if($start_date != '' && $end_date != ''){
	$date_condition = " (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
}
else{
	$date_condition = " SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$survey_date)."' ";
}

$sql_site_details = "SELECT DISTINCT survey_id FROM survey_output WHERE ".$date_condition." AND SUBSTRING(survey_id,3,5) IN(".$employee_arg.") AND ".$condition." AND type = '".$survey_type."' ORDER BY SUBSTRING(survey_id,-14,8) DESC";
$res_site_details = mysqli_query($link,$sql_site_details);
while($row_site_details = mysqli_fetch_assoc($res_site_details)){
	$survey_id = $row_site_details['survey_id'];
	$emp_code = substr($survey_id,2,5);
	
	$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysqli_query($link,$sql_emp_details);
	$row_emp_details = mysqli_fetch_assoc($res_emp_details);
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	$emp_name = $row_emp_details['emp_name'];
	
	$get_date = substr($survey_id,-14,8);
	
	echo "<tr>
			<td>".date('d-m-Y',strtotime($get_date))."</td>
			<td>".$dns_emp_code."</td>
			<td>".$emp_name."</td>";
	
	$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
	$res_survey_details = mysqli_query($link,$sql_survey_details);
	while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
		$row_id = $row_survey_details['row_id'];
		$value = $row_survey_details['value'];
		
		if($row_id == 'RA020')
			$branch = $value;
		else if($row_id == 'RA055')
			$place = $value;
		else if($row_id == 'RA021')
			$name = $value;
		else if($row_id == 'RA022')
			$cat = $value;
		else if($row_id == 'RA023')
			$contact = $value;
		else if($row_id == 'RA024')
			$address = $value;
		else if($row_id == 'RA026')
			$reason_of_buying = $value;
		else if($row_id == 'RA027')
			$source_of_purchase = $value;
		else if($row_id == 'RA028')
			$rate_bag = $value;
		else if($row_id == 'RA029')
			$segment = $value;
		else if($row_id == 'RA030')
			$type_of_structure = $value;
		else if($row_id == 'RA031')
			$current_stage = $value;
		else if($row_id == 'RA032')
			$total_consumption = $value;
		else if($row_id == 'RA036')
			$balance_consumption = $value;
		else if($row_id == 'RA033')
			$visit_status = $value;
		else if($row_id == 'RA034')
			$order_placed_to = $value;
		else if($row_id == 'RA035')
			$remarks = $value;
		else if($row_id == 'RA038'){
			$site_image = $value;
			$site_image = ltrim($site_image," ");
			$site_image = rtrim($site_image," ");
			$site_image = rtrim($site_image,";");
			$site_image=str_replace('.JPEG','.jpeg',$site_image);
			$site_image_array = explode(";",$site_image);
			
			$image_string = '';
			foreach($site_image_array as $image){
				$image = ltrim($image," ");
				if($image != '')
				//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
			}
		}
		else if($row_id == 'RA058'){
			$recommendation_id = $value;
		}
		else if($row_id == 'RA059'){
			$star_lifting = $value;
		}
		else if($row_id == 'RA025'){
			$brand_in_use = str_replace('#',':',$value);
			/*$brand_in_use_count=explode('@',$brand_in_use);
			/*$brand_html="<td><table border='1'><tr>";
			foreach($brand_in_use_count  as $brand_in_name_val)
			{
				$brand_html.="<td><b>Brand Name</b></td>"."<td><b>Qty</b></td>";
			}
			$brand_html.='</tr><tr>';
			foreach($brand_in_use_count  as $brand_in_name_val)
			{
				$brand_name=str_replace('YES:','',$brand_in_name_val);
				$brand_name_qty=explode(':',$brand_name);
				$brand_name=$brand_name_qty[0];
				$brand_qty=$brand_name_qty[1];
				
				$brand_html.="<td>".$brand_name."</td>"."<td>".$brand_qty."</td>";
			}
			$brand_html.='</tr></table></td>';*/
			/*$brand_html='';
			foreach($competitorname_array as $competitorname_val)
			{
				foreach($brand_in_use_count  as $brand_in_name_val)
				{
					$brand_name=str_replace('YES:','',$brand_in_name_val);
					$brand_name_qty=explode(':',$brand_name);
					$brand_name=$brand_name_qty[0];
					$brand_qty=$brand_name_qty[1];
					if(strtolower($brand_name)==strtolower($competitorname_val))
					{
					
					$brand_html.="<td align=\"right\">".$brand_qty."</td>";
					}
					else
					{
						$brand_html.="<td align=\"right\"></td>";
					}
				}
			}*/
		}
	}
	echo "<td>".$recommendation_id."</td>
			<td>".$star_lifting."</td>
			<td>".$branch."</td>
			<td>".$place."</td>
			<td>".$name."</td>
			<td>".$cat."</td>
			<td>".$contact."</td>
			<td>".$address."</td>
			<td>".$brand_in_use."</td>
			<td>".$reason_of_buying."</td>
			<td>".$source_of_purchase."</td>
			<td>".$rate_bag."</td>
			<td>".$segment."</td>
			<td>".$type_of_structure."</td>
			<td>".$current_stage."</td>
			<td>".$total_consumption."</td>
			<td>".$balance_consumption."</td>
			<td>".$visit_status."</td>
			<td>".$order_placed_to."</td>
			<td>".$remarks."</td>
			<td>".$image_string."</td>
		  </tr>";
}
?>
</table>
<br />
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>