<?php

 //ini_set('display_errors', 1);
 //ini_set('display_startup_errors', 1);
 //error_reporting(E_ALL);
 
ob_start();
session_start();

if(strpos(strtolower($_SESSION['sale_access']),'vendor')!=false && (strtoupper($_SESSION['nick_name'])== 'STAR' || strtoupper($_SESSION['nick_name'])== 'START'))
	{
		require("adminUtils_branding.php");
	}
	else
	{
		require("adminUtils.php");
	}
	
	
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$t = $_REQUEST['survey_type'];

$survey_id="";
$lead_generation_id="";
$new_existing = "New";

if (isset($_POST["submit"])) 
   {
      echo $_f4 = $_POST["iid"];
       exit();
   }

//echo $t;exit();

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
//$survey_type = $_REQUEST['survey_type'];

function getReverseGeoAdd($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc";
		// make the HTTP request
		$data = @file_get_contents($url);
		// parse the json response
		$jsondata = json_decode($data,true);
		
		//print_r($jsondata);
		// if we get a formatted_address array and the status was OK, get the addres
		if(is_array($jsondata )&& $jsondata['status']=='OK')
		{
			  $addr = $jsondata['results']['0']['formatted_address'];
		}		
		return  $addr;	
	}


$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

$sql_table_view = "SELECT value FROM table_view WHERE row_id = 'RA205' and type='checkbox'";
$res_table_view = mysqli_query($link,$sql_table_view);

$rowtableview=mysqli_fetch_assoc($res_table_view);
$value = $rowtableview['value'];
$value_parts=explode("/",$value);

foreach($value_parts as $rowheaderval)
{
	$product_row .= "<td width=\"2%\">$rowheaderval</td>";
}

$sql_distinct_date = "SELECT DISTINCT survey_id,value, SUBSTRING(survey_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date,
					DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%H:%i:%s') AS survey_time 
						FROM survey_output WHERE 
					(SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = '".$t."'  AND (row_id='RA595' OR row_id='RA594' OR row_id='RA597' OR row_id='RA724')
					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
					//echo $sql_distinct_date;
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

$res_distinct_date11 = mysqli_query($link,$sql_distinct_date);
$row_survey_ouput1 = mysqli_fetch_assoc($res_distinct_date11);
$survey_id = $row_survey_ouput1['survey_id'];
$menu_id = "";
$sql_survey_output_chk = "SELECT survey_id,row_id,value FROM survey_output where survey_id = '".$survey_id."' AND row_id = 'RA486'";

$res_survey_output_hk = mysqli_query($link,$sql_survey_output_chk);
$count_chk=mysqli_num_rows($res_survey_output_hk);
if($count_chk > 0){
    $menu_id="RA514";
    $new_existing = "New";
}else{
    $menu_id="RA515";
    $new_existing = "Existing";
}

if($total_rows>0){
	?>
	

<style>
  .table {
    width: fit-content;
    block-size: fit-content;
  }
  
  .view {
  margin: auto;
  width: 600px;
}

.wrapper {
  position: relative;
  overflow: auto;
  border: 1px solid black;
  white-space: nowrap;
}

.sticky-col {
  position: -webkit-sticky;
  position: sticky;
  background-color: #A92A61;
}
.sticky-cols {
  position: -webkit-sticky;
  position: sticky;
  background-color: white;
}

.first-col {
  width: 100px;
  min-width: 100px;
  max-width: 100px;
  left: 0px;
}

.second-col {
  width: 150px;
  min-width: 150px;
  max-width: 150px;
  left: 100px;
}
.id-col {
  width: 150px;
  min-width: 150px;
  max-width: 150px;
  left: 2px;
}
.max-col {
  width: 200px;
  min-width: 200px;
  max-width: 200px;
  left: 2px;
}
</style>
    
<!--<form action="star_survey_report_modified_one_lead_test.php" method="get">
            <input type="hidden" name="mode" value="PO_no_update" />-->
    <table border="1" style="border-collapse:collapse;" class="border" width="100%">
        
 		<tr>
      	  <td colspan="57" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
        
      <tr class="TDHEAD">
          <td class="sticky-col id-col">ID</td>
          <td >New/Existing</td>
          <td class="id-col">Date</td>
          <td width="3%">Month</td>
        <td class="id-col">Employee Code</td>
        <td class="id-col">Employee Name</td>
        <td width="3%">Latitude</td>
        <td width="3%">Longitude</td>
        <td width="3%">Zone</td>
        <td width="3%">Region</td>
          <?php
          
          $sql="SELECT * FROM `survey_input_lead` WHERE `menu_id` = '".$menu_id."' AND acedns='Y' AND type<>'menu' ORDER BY display_order ASC";
          
          $res_head = mysqli_query($link,$sql);
		  
          while($row_head = mysqli_fetch_assoc($res_head)){
		    $display_name = $row_head['display_name'];
		    if($display_name=="Assigned to" || $display_name=="RA531"){
		        ?>
		        <td class="id-col"><?php echo $display_name; ?></td>
		        <td class="id-col">Action Taken</td>
		        <td class="id-col">Approved Price</td>
		        <?php
		        
		    }else{
          ?>
          
          <td class="id-col"><?php echo $display_name; ?></td>
          
          <?php
		    }
          }
          ?>
        <!-- <td class="id-col">Sales org</td>
        <td class="id-col">Division</td>
        <td class="id-col">Distribution channel</td>
        <td class="id-col">Document type</td>
        <td class="id-col">Customer reference No</td>
        
        <td class="max-col">Customer reference date</td>
        <td class="id-col">Valid to date</td>
        <td class="id-col">Material number</td>
        <td class="id-col">Sold to party code</td>
        <td class="id-col">Ship to party code</td>
        <td class="id-col">PO method</td>
        <td class="id-col">Quotation provided </td>
        <td class="id-col">Date of Quotation provided</td> -->

		<!-- New field added on 10.04.2025 for spreadsheet -->
		<td class="id-col">Destination</td>
		<td class="id-col">Company Constraint</td>
		<td class="id-col">Reason</td>
		<td class="id-col">NOV</td>
		<td class="id-col">Incoterms</td>
		<td class="id-col">Serving Location for EXW</td>
		<td class="id-col">Quoted Price (₹/MT)</td>
		<td class="id-col">TPC, etc (₹/MT)</td>
		<td class="id-col">Payment</td>
		<td class="id-col">Last Price (₹/MT)</td>
		<td class="id-col">Prev. Last Price (₹/MT)</td>
        
        <td>Submit</td>
       
      </tr>
    <?php
	
	while($row_survey_ouput = mysqli_fetch_assoc($res_distinct_date)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$survey_time = $row_survey_ouput['survey_time'];
		//$value=$row_survey_ouput['value'];
		
		$sql_survey_output_chk = "SELECT survey_id,row_id,value FROM survey_output where survey_id = '".$survey_id."' AND row_id = 'RA486'";

        $res_survey_output_hk1 = mysqli_query($link,$sql_survey_output_chk);
        $count_chk1=mysqli_num_rows($res_survey_output_hk1);
        if($count_chk1 > 0){
            
            $new_existing = "New";
        }else{
            
            $new_existing = "Existing";
        }
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to,district,HQ,region,zone FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$emp_name = $row_emp_details['emp_name'];
		$reporting_to = $row_emp_details['reporting_to'];
		$district = $row_emp_details['district'];
		$HQ = $row_emp_details['HQ'];
		$region = $row_emp_details['region'];
		$zone = $row_emp_details['zone'];
		
		$sql_emp_lat_long = "SELECT latt, longi FROM location WHERE trans_id = '".$survey_id."'";
		$res_emp_lat_long = mysqli_query($link,$sql_emp_lat_long);
		$row_emp_lat_lomg = mysqli_fetch_assoc($res_emp_lat_long);
		$dns_emp_lat = $row_emp_lat_lomg['latt'];
		$dns_emp_lon = $row_emp_lat_lomg['longi'];
				
		//}
		$lg_date1 = date("F", strtotime($survey_date));
		$cc2 = "sticky-cols max-col";
		$cc = "sticky-cols first-col";

		
		echo "<tr>";
		 echo       '<td class="'.$cc.'">'.$survey_id.'</td>';
		        
		echo '<td >'.$new_existing.'</td>';
		        
		echo	"<td>".$survey_date."</td>
				<td>".$lg_date1."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				
				<td>".$dns_emp_lat."</td>
				<td>".$dns_emp_lon."</td>
				
				<td>".$zone."</td>
				<td>".$region."</td>
				
				";
		$sql="SELECT * FROM `survey_input_lead` WHERE `survey_sub_menu` = '".$t."' AND acedns='Y' AND type<>'menu' ORDER BY display_order ASC";
          $res_head = mysqli_query($link,$sql);
          while($row_head = mysqli_fetch_assoc($res_head)){
		    $row_id = $row_head['row_id'];
		    
    		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."' AND row_id='".$row_id."'";
    		$res_survey_details = mysqli_query($link,$sql_survey_details);
    		$technical_checked_row='';
    		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
    			$row_id = $row_survey_details['row_id'];
    			$survey_value = str_replace('#',':',$row_survey_details['value']);
    			if($row_id=="RA486" || $row_id=="RA516"){
    			    $commaList = explode(';', $survey_value);
    			    $survey_value = $commaList[0];
    			    $lead_generation_id = $commaList[1];
    			}
    			//$survey_value = str_replace(';','',$survey_value);
    			if($row_id=="RA585" || $row_id=="RA592"){
    			    if($survey_value!=""){
    			        
    			        $site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode("; ;",$site_image);
				//$survey_value=$site_image;
				//exit();
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					
										$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
    			        
    			        $image_string = str_replace(';','',$image_string);
    			        $survey_value=$image_string;
    			        
    			        
    			        
    			        /*$site_image=str_replace('.JPEG','.jpeg',$survey_value);
    			        $survey_value = "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$site_image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";*/
    			    }
    			}
    			
    			
    			if($row_id=="RA550"){
    			    $sql_11 = "SELECT branch_name FROM branch_master WHERE branch_code = '".$survey_value."'";
		            $res_1 = mysqli_query($link,$sql_11);
		            $row_1 = mysqli_fetch_assoc($res_1);
		            $survey_value = $row_1['branch_name'];
		
    			}
    			
    			if($row_id=="RA557" || $row_id=="RA559" || $row_id=="RA561"){
    			    $rssd1 = $survey_value;
				
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$survey_value=$rowcustomer['customer_name']."-".$dns_customer_code_one;
		
    			}
    			
    			if($row_id=="RA501" || $row_id=="RA531"){
    			    $sql_11 = "SELECT dns_emp_code, emp_name ,reporting_to,district,HQ,region,zone FROM employee_master WHERE emp_code = '".$survey_value."'";
		            $res_1 = mysqli_query($link,$sql_11);
		            $row_1 = mysqli_fetch_assoc($res_1);
		            
		            $survey_value = $row_1['emp_name'];
		            
		            $sqlcustomer="SELECT * FROM lead_generation_master WHERE lead_generation_id='".$lead_generation_id."'";
				$rsL=mysqli_query($link,$sqlcustomer);
				$rowL=mysqli_fetch_assoc($rsL);
				$action_on_lead=$rowL['action_on_lead'];
		            $approved_price=$rowL['approved_price'];
		            
		            echo "<td>".$survey_value."</td>";
		            echo "<td>".$action_on_lead."</td>";
		            echo "<td>".$approved_price."</td>";
		
    			}else{
		
    			echo "<td>".$survey_value."</td>";
    			}
    		}
		    
          }
		//$lead_generation_id="2";
		
		$sqlcustomer="SELECT * FROM lead_generation_master WHERE lead_generation_id='".$lead_generation_id."'";
				$rsL=mysqli_query($link,$sqlcustomer);
				$rowL=mysqli_fetch_assoc($rsL);
				$PO_method=$rowL['PO_method'];
				$ship_to_party=$rowL['ship_to_party'];
				$sold_to_party=$rowL['sold_to_party'];
				$material_number=$rowL['material_number'];
				$valid_to_date=$rowL['valid_to_date'];
				$customer_reference_date=$rowL['customer_reference_date'];
				$customer_reference_no=$rowL['customer_reference_no'];
				$sales_org = $rowL['sales_org'];
				$document_type = $rowL['document_type'];
				$quotation_provided = $rowL['quotation_provided'];
				$quotation_provided_date = $rowL['quotation_provided_date'];

				//This new field value for lead_input_sheet
				$product_packaging 		= $rowL['product_packaging'];
				$qty_req  				= $rowL['qty_req'];
				$lead_emp_code 			= $rowL['emp_code'];
				$lead_party_name 		= $rowL['party_name'];
				$lead_date 				= date('d-m-Y');
				
				
				
		  //comment out for new requirement 14/04/2025
		
		// echo '<td style="width:200px;"><select id="aa_'.$lead_generation_id.'" name="'.$lead_generation_id.'_1">
		// <option value="'.$sales_org.'" selected>'.$sales_org.'</option>
		// <option value="1010">1010</option>
		// <option value="1017">1017</option>
		// </select></td>';
		
		// echo '<td style="width:200px;"><select id="bb_'.$lead_generation_id.'" name="'.$lead_generation_id.'_2">
		// <option value="CE">CE</option>
		// </select></td>';
		
		// echo '<td style="width:200px;"><select id="cc_'.$lead_generation_id.'" name="'.$lead_generation_id.'_3">
		// <option value="NT">NT</option>
		// </select></td>';
		
		
		// echo '<td style="width:200px;"><select id="dd_'.$lead_generation_id.'" name="4">
		// <option value="'.$document_type.'" selected>'.$document_type.'</option>
		// <option value="ZQTN">ZQTN</option>
		// <option value="ZCCQ">ZCCQ</option>
		// <option value="ZNTD">ZNTD</option>
		// </select></td>';
	


		
		// echo '<td style="width:200px;"><input type="text" id="ee_'.$lead_generation_id.'" name="5" value="'.$customer_reference_no.'"></td>';
		
 		// echo '<td style="width:200px;"><input type="date" id="ff_'.$lead_generation_id.'" name="'.$lead_generation_id.'_6" value="'.$customer_reference_date.'"></td>';
		// echo '<td><input type="date" id="gg_'.$lead_generation_id.'" name="'.$lead_generation_id.'_7" value="'.$valid_to_date.'"></td>';
		
		// echo '<td><input type="text" id="hh_'.$lead_generation_id.'" name="'.$lead_generation_id.'_8" value="'.$material_number.'"></td>';
		
		// echo '<td><input type="text" id="ii_'.$lead_generation_id.'" name="'.$lead_generation_id.'_9" value="'.$sold_to_party.'"></td>';
		
		// echo '<td><input type="text" id="jj_'.$lead_generation_id.'" name="'.$lead_generation_id.'_10" value="'.$ship_to_party.'"></td>';
		
		// echo '<td><input type="text" id="kk_'.$lead_generation_id.'" name="'.$lead_generation_id.'_11" value="'.$PO_method.'"></td>';
		
		// echo '<td style="width:200px;"><select id="ll_'.$lead_generation_id.'" name="'.$lead_generation_id.'_12">
		// <option value="'.$quotation_provided.'" selected>'.$quotation_provided.'</option>
		// <option value="yes">Yes</option>
		// <option value="no">No</option>
		// </select></td>';
		
		
		// echo '<td><input type="date" id="mm_'.$lead_generation_id.'" name="'.$lead_generation_id.'_13" value="'.$quotation_provided_date.'">
		
		// </td>';
		
		/*echo '<td><button onclick="updateL('.$lead_generation_id.')" class="btn bg-red waves-effe">Update</button></td>';
		echo '<td><input name="update" type="button" value="Update1" id="btnUpdate" onClick="updateL('.$lead_generation_id.')"></td>';*/
		// ======== New field added on 10.04.2025 for spreadsheet ============
		
		// $sql_destination = "SELECT DISTINCT destination_name FROM destination_master order by destination_name ASC";
		// $res_destination = mysqli_query($link,$sql_destination);
		// $destination_option='';
		// while($row_destination_list = mysqli_fetch_assoc($res_destination)){
		// 	$row_destination_name = $row_destination_list['destination_name'];
		// 	$destination_option .= '<option value="'.$row_destination_name.'">'.$row_destination_name.'</option>';
		// }
		// echo $destination_option;die;

		echo '<td style="width:200px;"><select id="destination_'.$lead_generation_id.'" name="'.$lead_generation_id.'_destination">
		<option value="'.$rowL['destination'].'" selected>'.$rowL['destination'].'</option>
		<option value="AGARTALA">AGARTALA</option>
		<option value="MAIBONG">MAIBONG</option>
		<option value="DIBANG">DIBANG</option>
		<option value="BONGAIGAON">BONGAIGAON</option>
		<option value="DHUBRI">DHUBRI</option>
		
		</select></td>';

		echo '<td><input type="text" id="company_constraint_'.$lead_generation_id.'" name="'.$lead_generation_id.'_company_constraint" value="'.$rowL['company_constraint'].'"></td>';

		echo '<td><input type="text" id="reason_'.$lead_generation_id.'" name="'.$lead_generation_id.'_reason" value="'.$rowL['reason'].'"></td>';

		echo '<td><input type="text" id="nov_'.$lead_generation_id.'" name="'.$lead_generation_id.'_nov" value="'.$rowL['nov'].'"></td>';

		echo '<td><input type="text" id="incoterms_'.$lead_generation_id.'" name="'.$lead_generation_id.'_incoterms" value="'.$rowL['incoterms'].'"></td>';

		echo '<td><input type="text" id="serving_location_'.$lead_generation_id.'" name="'.$lead_generation_id.'_serving_location" value="'.$rowL['serving_location'].'"></td>';

		echo '<td><input type="number" id="quoted_price_'.$lead_generation_id.'" name="'.$lead_generation_id.'_quoted_price" value="'.$rowL['quoted_price'].'"></td>';

		echo '<td><input type="number" id="tpc_'.$lead_generation_id.'" name="'.$lead_generation_id.'_tpc" value="'.$rowL['tpc'].'"></td>';

		echo '<td><input type="text" id="payment_'.$lead_generation_id.'" name="'.$lead_generation_id.'_payment" value="'.$rowL['payment'].'"></td>';

		echo '<td><input type="text" id="last_price_'.$lead_generation_id.'" name="'.$lead_generation_id.'_last_price" value="'.$rowL['last_price'].'"></td>';

		echo '<td><input type="text" id="prev_last_price_'.$lead_generation_id.'" name="'.$lead_generation_id.'_prev_last_price" value="'.$rowL['prev_last_price'].'"></td>';
		
		
		?>
		
		
		<td>
		    <button onclick="UpdateElem('<?php echo $lead_generation_id; ?>')" class="btn bg-red waves-effe">Update</button>
			
			<input type="hidden" id="product_packaging_<?php echo $lead_generation_id; ?>" value="<?php echo $product_packaging; ?>">
			<input type="hidden" id="qty_req_<?php echo $lead_generation_id; ?>" value="<?php echo $qty_req; ?>">
			<input type="hidden" id="lead_emp_code_<?php echo $lead_generation_id; ?>" value="<?php echo $lead_emp_code; ?>">
			<input type="hidden" id="lead_party_name_<?php echo $lead_generation_id; ?>" value="<?php echo $lead_party_name; ?>">
			<input type="hidden" id="lead_date_<?php echo $lead_generation_id; ?>" value="<?php echo $lead_date; ?>">
			<input type="hidden" id="lead_exist_<?php echo $lead_generation_id; ?>" value="<?php echo $new_existing; ?>">
		</td>
		
		<?php
		
		/*echo '<td><div style="width:100%;" align="right" id="print_export" ><input name="update" type="button" value="Update" id="update" onClick="UpdateElem('.$lead_generation_id.');">
    
            </div></td>';*/
			
		 echo "</tr>";
		 
	 }
}
	else{
		echo "<tr><td colspan='52' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br>
    <br>
	


    <?php
    
mysqli_close($link);
?>
