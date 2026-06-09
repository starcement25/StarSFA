<?php
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

$sql_distinct_date = "SELECT DISTINCT survey_id,value, SUBSTRING(survey_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date,
					DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%H:%i:%s') AS survey_time 
						FROM survey_output WHERE 
					(SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = 'Dhalai Services' AND row_id='RA189'  
					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
$res_distinct_date = mysqli_query($link,$sql_distinct_date);

// echo $sql_distinct_date;die;
$total_rows = mysqli_num_rows($res_distinct_date);

if($total_rows>0){
	?>
        <form action="branding_verification_account.php" method="post">
    <input type="hidden" name="mode" value="PO_no_update" />

    <table border="1" style="border-collapse:collapse;" class="border" width="90%">
 		<tr>
      	  <td colspan="39" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
        
      <tr class="TDHEAD">
        <td width="3%">Visit Date</td>
        <td width="3%">Branch</td>
        <td width="3%">Employee Code</td>
        <td width="3%">Employee Name</td>
         <td width="3%">District</td>
        <td width="3%">Customer Name</td>
        <td width="3%">Customer Contact No.</td>
        <td width="3%">Full Address</td>
        <td width="3%">Petty Contractor - Head Mason Name</td>
        <td width="3%">Petty Conttractor- Head Mason Contact No.</td>
        <td width="3%">Site Segment</td>
        <td width="3%">Service Category</td>
        <td width="3%">Dhalai Date</td>
        <td width="3%">Type of Construction</td>
        <td width="3%">Area (Sq. Ft.)</td>
        <td width="3%">Current Stage of Construction</td>
        <td width="3%">Brand/Type of Cement Used</td>
        <td width="3%">Consumed Till Date (No. of Bags)</td>
		 <td width="3%">Bags consumed on the day of Dhalai (No. of Bags)</td>
        <td width="3%">Linked or associated Dealer/ RSSD</td>
        <td width="3%">Cover Block Qty</td>
        <td width="3%">Cover Block Placement Check</td>
		<?php echo $product_row;?>
        <td width="3%">Technical Service Others</td>
		 <td width="3%">Conversion Done</td>
        <td width="3%">Sweet Given</td>
        <td width="3%">Gift Given</td>
        <td width="3%">Photo</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_distinct_date);
	echo "Result Rows: ".mysqli_num_rows($res_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$survey_time = $row_survey_ouput['survey_time'];
		$value=$row_survey_ouput['value'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to,district,HQ FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$emp_name = $row_emp_details['emp_name'];
		$reporting_to = $row_emp_details['reporting_to'];
		$district = $row_emp_details['district'];
		$HQ = $row_emp_details['HQ'];
		
		$sql_branch = "SELECT branch_name FROM branch_master WHERE branch_code = '".$value."'";
		$res_branch = mysqli_query($link,$sql_branch);
		$row_branch = mysqli_fetch_assoc($res_branch);
		$branch_name = $row_branch['branch_name'];
		/*$sql_location = "SELECT latt,longi FROM location WHERE trans_id = '".$survey_id."'";
		$res_location = mysqli_query($link,$sql_location);
		$row_location = mysqli_fetch_assoc($res_location);
		$latt = $row_location['latt'];
		$longi = $row_location['longi'];*/
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$branch_name."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				";
		$bags_consumed="";
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		$technical_checked_row='';
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA190'){
				$customer_name=$survey_value;	
			}
			else if($row_id == 'RA191'){
				$customer_contact_no  = $survey_value;
			}
			else if($row_id == 'RA192'){
				$full_address =$survey_value;
			}
			else if($row_id == 'RA193'){
				$head_mason_name = $survey_value;
			}
			else if($row_id == 'RA194'){
				$head_mason_no = $survey_value;
			}
			else if($row_id == 'RA195'){
				$site_segment = $survey_value;
			}
			else if($row_id == 'RA196'){
				
				if(strpos($survey_value,':')===false){
					$service_category = $survey_value;
					$dhalai_date='';
				}
				else
				{
					$survey_value_parts=explode(':',$survey_value);
					$service_category = $survey_value_parts[0];
					$dhalai_date=$survey_value_parts[1];
				}
			}
			else if($row_id == 'RA197'){
				$type_of_Construction = $survey_value;
			}
			else if($row_id == 'RA198'){
				$Area_sq_ft = $survey_value;
			}
			else if($row_id == 'RA199'){
				$current_stage_construction = $survey_value;
			}
			else if($row_id == 'RA200'){
				$cement_brand_used = $survey_value;
			}
			else if($row_id == 'RA201'){
				$consumed_till_date = $survey_value;
			}
			else if($row_id == 'RA202'){
				$linked_dealer = $survey_value;
			}
			else if($row_id == 'RA203'){
				$cover_block = $survey_value;
			}
			else if($row_id == 'RA204'){
				$cover_block_placement = $survey_value;
			}
			else if($row_id == 'RA205'){
				//$technical_service = $survey_value;
				// $technical_service_value=strtoupper(mb_substr($technical_service_value,0,-1));
				$technical_service_value=str_replace('; ',';',$survey_value);
				$technical_service_value=explode(';',$technical_service_value);
				//$technical_service_value=str_replace(';','',$technical_service_value);
				//print_r($technical_service_value);
				
				foreach($value_parts as $rowvalue)
				{
					//echo strtoupper($rowvalue);
					//echo '<br />';
					
						//echo strtoupper($technical_checked_value);
						if( in_array(strtoupper($rowvalue),$technical_service_value))
						{
							$checked_val="Y";
						}
						else
						{
							$checked_val ="";
						}
					$technical_checked_row .="<td >$checked_val</td>";
				}
				
			}
			else if($row_id == 'RA335'){
				$conversion_done = $survey_value;
			}
			else if($row_id == 'RA206'){
				$sweet_given = $survey_value;
			}
			else if($row_id == 'RA207'){
				$gift_given = $survey_value;
			}
			else if($row_id == 'RA252'){
				$technical_others = $survey_value;
			}
			else if($row_id == 'RA334'){
				$district_survey = $survey_value;
			}
			else if($row_id == 'RA208'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");
				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}else if($row_id == 'SB001'){
				$bags_consumed = $survey_value ?? '';
			}
		}
		if($district_survey=='') $district=$HQ;
		else 					 $district=$district_survey;
		echo "<td>".$district."</td>
			<td>".$customer_name."</td>
			<td >".$customer_contact_no."</td>
			<td >".$full_address."</td>
			<td >".$head_mason_name."</td>
			<td >".$head_mason_no."</td>
			<td >".$site_segment."</td>
			<td >".$service_category."</td>
			<td >".$dhalai_date."</td>
			<td >".$type_of_Construction."</td>
			<td >".$Area_sq_ft."</td>
			<td >".$current_stage_construction."</td>
			<td >".$cement_brand_used."</td>
			<td>".$consumed_till_date."</td>
			<td>".$bags_consumed."</td>
			<td>".$linked_dealer."</td>
			<td>".$cover_block."</td>
			<td>".$cover_block_placement."</td>".$technical_checked_row."
			<td>".$technical_others."</td>
			<td>".$conversion_done."</td>
			<td>".$sweet_given."</td>
			<td>".$gift_given."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	}
	else{
		echo "<tr><td colspan='39' align='center'>No Records</td><tr>";
	}
	?>
    </table>
       <br>
    <br>
	<?php if($total_rows>0){ ?>
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>		
    <?php
	}
mysqli_close($link);
?>
