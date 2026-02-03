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
$t = $_REQUEST['survey_type'];

$survey_id="";

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
					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = '".$t."'  AND (row_id='RA821' OR row_id='RA641' OR row_id='RA597')
					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
					//echo $sql_distinct_date;
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

if($total_rows>0){
	?>
        <form action="counter_visit_data.php" method="post">
    <input type="hidden" name="mode" value="PO_no_update" />

    <table border="1" style="border-collapse:collapse;" class="border" width="90%">
 		<tr>
      	  <td colspan="39" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
        
      <tr class="TDHEAD">
          <td width="3%">Date</td>
          
          
        <td width="3%">Employee Code</td>
        <td width="3%">Employee Name</td>
        <td width="3%">Latitude</td>
        <td width="3%">Longitude</td>
        <td width="3%">Zone</td>
        <td width="3%">Region</td>
          <?php
          
          $sql="SELECT * FROM `survey_input_mle` WHERE `survey_sub_menu` = '".$t."' AND acedns='Y' AND type<>'menu' ORDER BY display_order ASC";
          
          $res_head = mysqli_query($link,$sql);
          while($row_head = mysqli_fetch_assoc($res_head)){
		    $display_name = $row_head['display_name'];
          ?>
          
          <td width="3%"><?php echo $display_name; ?></td>
          
          <?php
          }
          ?>
    
       
      </tr>
    <?php
	while($row_survey_ouput = mysqli_fetch_assoc($res_distinct_date)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$survey_time = $row_survey_ouput['survey_time'];
		$value=$row_survey_ouput['value'];
		//echo $value;
		
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
		
		echo "<tr>
				<td>".$survey_date."</td>
				
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				
				<td>".$dns_emp_lat."</td>
				<td>".$dns_emp_lon."</td>
				
				<td>".$zone."</td>
				<td>".$region."</td>
				
				";
		$sql="SELECT * FROM `survey_input_mle` WHERE `survey_sub_menu` = '".$t."' AND acedns='Y' AND type<>'menu' ORDER BY display_order ASC";
          $res_head = mysqli_query($link,$sql);
          while($row_head = mysqli_fetch_assoc($res_head)){
		    $row_id = $row_head['row_id'];
		    
    		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."' AND row_id='".$row_id."'";
    		$res_survey_details = mysqli_query($link,$sql_survey_details);
    		$technical_checked_row='';
    		$total_found = mysqli_num_rows($res_survey_details);
    		$survey_value = "";
    		if($total_found>0){
    		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
    			$row_id = $row_survey_details['row_id'];
    			$survey_value = str_replace('#',':',$row_survey_details['value']);
    			if($row_id=="RA209"){
    			    $commaList = explode(';', $survey_value);
    			    $survey_value = $commaList[0];
    			}
    			if($row_id=="RA209"){
    			    $commaList = explode(';', $survey_value);
    			    $survey_value = $commaList[0];
    			}
    			//$survey_value = str_replace(';','',$survey_value);
    			if($row_id=="RA565" || $row_id=="RA547"){
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
    			
    			
    			if($row_id=="RA832"){
    			    $sql_11 = "SELECT branch_name FROM branch_master WHERE branch_code = '".$survey_value."'";
		            $res_1 = mysqli_query($link,$sql_11);
		            $row_1 = mysqli_fetch_assoc($res_1);
		            $survey_value = $row_1['branch_name'];
		
    			}
    			
    			if($row_id=="RA822" || $row_id=="RA559" || $row_id=="RA561"){
    			    $rssd1 = $survey_value;
				
				$customer_code = $survey_value;
				$sqlcustomer="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
				$rscustomer=mysqli_query($link,$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$dns_customer_code_one=$rowcustomer['dns_customer_code'];
				$survey_value=$rowcustomer['customer_name']."--CODE(".$dns_customer_code_one. ")";
		
    			}
    			
    			
    			
    			
    			
    			
    			
		
    			echo "<td>".$survey_value."</td>";
    		}
          }else{
              echo "<td>".$survey_value."</td>";
          }
          }
	
		
			
		 echo "</tr>";
	 }
}
	else{
		echo "<tr><td colspan='39' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br>
    <br>
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>			
    <?php
mysqli_close($link);
?>
