<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

//$emp_code = $_REQUEST['emp_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);

function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);
	
	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;
	
	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}
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

$sql_emp_code = "SELECT DISTINCT emp_code FROM location WHERE emp_code IN(".$employee.")
				AND (SUBSTRING(`date`,1,10) >= '".$start_date."' AND SUBSTRING(`date`,1,10) <='".$end_date."')";
$res_emp_code = mysqli_query($link,$sql_emp_code);
$total_row_check = mysqli_num_rows($res_emp_code);
if($total_row_check>0){
	?>
    <div id="display_data">
    <table class="border" style="border-collapse:collapse;" width="100%" border="1" cellpadding="4px">
    	<tr class="TDHEAD_SUB">
        	<td align="center" colspan="15">Travel Bill</td>
        </tr>
    	<tr class="TDHEAD">
        	<td width="6%">Date</td>
            <td width="6%">Place From</td>
            <td width="6%">Place TO</td>
            <td width="6%">Travel Mode</td>
            <td width="6%">KM(UP & DOWN)</td>
            <td width="6%">KM Fare(Amount in RS)</td>
            <td width="6%">DA(Amount in RS)</td>
            <td width="6%">Fare<br />(Amount in RS)</td>
            <td width="6%">Food<br />(Amount in RS)</td>
            <td width="6%">Lodging<br />(Amount in RS)</td>
            <td width="6%">LOCAL Conveyance <br />(Amount in RS)</td>
            <td width="6%">Others <br />(Amount in RS)</td>
            <td width="6%">Total <br />(Amount in RS)</td>
            <td width="">Remarks</td>
            <td width="">Miscellaneous</td>
            <?php if(strtoupper($_SESSION['nick_name'])=='ILS'){?>
              <td width="">Daily assistance</td>
              <td width="">Km travelled (one way)</td>
            <?php }?>
            <td width="7%">photo</td>
        </tr>
    <?php
	$res_emp_code = mysqli_query($link,$sql_emp_code);
	while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
		
		$sql_emp_details = "SELECT emp_name, designation, sale_access FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$designation = $row_emp_details['designation'];
		$sale_access = $row_emp_details['sale_access'];
		
		$sqlTADA="SELECT TM.per_km_TA,TM.DA_HQ,TM.DA_EX_HQ,TM.DA_outstation FROM TA_DA_master TM
				WHERE  TM.emp_code='".$emp_code."'";
		$resTADA = mysqli_query($link,$sqlTADA);
		$rowTADA=mysqli_fetch_assoc($resTADA);
		$per_km_TA=$rowTADA['per_km_TA'];
		$DA_HQ=$rowTADA['DA_HQ'];	
		$DA_EX_HQ=$rowTADA['DA_EX_HQ'];	
		$DA_outstation=$rowTADA['DA_outstation'];
		//$att_activities=$rowTADA['att_activities'];

		$countrow=1;
		/*$sql_max_min_date = "SELECT MAX(DATE_FORMAT(tour_date,'%d-%m-%Y')) AS end_date, MIN(DATE_FORMAT(tour_date,'%d-%m-%Y')) AS start_date FROM tour_fooding_lodging_expenses WHERE emp_code = '".$emp_code."' AND (tour_date BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_max_min_date = mysqli_query($link,$sql_max_min_date);
		$row_max_min_date = mysqli_fetch_assoc($res_max_min_date);
		
		$max_date = $row_max_min_date['end_date'];
		$min_date = $row_max_min_date['start_date'];*/
		
		?>
        <tr>
        	<td colspan="15" align="center" style="padding:8px;">
            	<table border="1" style="border-collapse:collapse;" width="60%">
                	<tr>
                    	<td style="font-weight:bold;">Name</td>
                        <td><?php echo $emp_name; ?></td>
                        <td style="font-weight:bold;">Designation</td>
                        <td><?php echo $designation; ?></td>
                        <td style="font-weight:bold;">Department</td>
                        <td><?php echo $sale_access; ?></td>
                    </tr>
                    <tr>
                    	<td colspan="6" align="center" style="font-weight:bold;">Details Of Expenses - (<?php echo date('d-m-Y', strtotime($start_date)). " to ".date('d-m-Y', strtotime($end_date)); ?>)</td>
                    </tr>
                </table>            	
            </td>
        </tr>
        <?php
		
		$sql_tour_details_latest = "SELECT SUBSTRING(LO.date,1,10) AS tour_date FROM location LO 
							WHERE  LO.emp_code = '".$emp_code."' AND 
							(SUBSTRING(LO.date,1,10) >= '".$start_date."' AND SUBSTRING(LO.date,1,10) <='".$end_date."') 
							AND (LO.TA_DA_mode='OWN'  OR LO.trans_id LIKE 'A%')
							ORDER BY LO.date DESC LIMIT 0,1";				
		$res_tour_details_latest = mysqli_query($link,$sql_tour_details_latest);
		$row_tour_details_latest=mysqli_fetch_assoc($res_tour_details_latest);
		echo $latest_tour_date=date('d-m-Y',strtotime($row_tour_details_latest['tour_date']));
		
		echo $sql_tour_details = "SELECT LO.latt,LO.longi,LO.trans_id,LO.TA_DA_mode,SUBSTRING(LO.date,1,10) AS tour_date FROM location LO 
							WHERE  LO.emp_code = '".$emp_code."' AND 
							(SUBSTRING(LO.date,1,10) >= '".$start_date."' AND SUBSTRING(LO.date,1,10) <='".$end_date."') 
							AND (LO.TA_DA_mode='OWN'  OR LO.trans_id LIKE 'A%')
							ORDER BY LO.date ASC,LO.TA_DA_mode ASC";				
		$res_tour_details = mysqli_query($link,$sql_tour_details);
		$cnt_tour_details=mysqli_num_rows($res_tour_details);				

		$countrow=1;
		$countkm=1;
		${tour_date.$emp_code}=array();
		${count_emp_rows.$emp_code}=1;
		$place_to=array();
		while($row_tour_details = mysqli_fetch_assoc($res_tour_details)){
			//$tour_date = date('d-m-Y',strtotime($row_tour_details['tour_date_from']));
			$tour_date = date('d-m-Y',strtotime($row_tour_details['tour_date']));
			$tour_date_location = date('Y-m-d',strtotime($row_tour_details['tour_date']));
			/*$tour_place_from = $row_tour_details['tour_place_from'];
			$tour_place_to = $row_tour_details['tour_place_to'];
			$transport_mode_type = $row_tour_details['transport_mode_type'];
			$transport_other_charges = $row_tour_details['transport_other_charges'];
			$fooding_charges = $row_tour_details['fooding_charges'];
			$hotel_charge = $row_tour_details['hotel_charge'];
			$local_conveyance = $row_tour_details['local_conveyance'];
			$other_expenses = $row_tour_details['other_expenses'];
			$remarks = $row_tour_details['remarks'];
			$transport_attachment = $row_tour_details['other_attachment_file'];
			$misc = $row_tour_details['misc'];
			if(strtoupper($_SESSION['nick_name'])=='ILS'){
				$daily_assistance = $row_tour_details['daily_assistance'];
				$km_traveled = $row_tour_details['km_traveled'];
			}*/
			
			if(${countkm.$emp_code.$tour_date}=='')
			{
				${countkm.$emp_code.$tour_date}=1;
			}
			 ${tour_date.$emp_code}=$tour_date;
			/*$sqlempkmvisited="SELECT latt,longi,trans_id FROM location WHERE emp_code = '".$emp_code."'  AND SUBSTRING(`date`,1,10)='".$tour_date_location."' 
							AND  TA_DA_mode='OWN'  ORDER BY `date` ASC";
			$resempkmvisited = mysqli_query($link,$sqlempkmvisited);
			while($rowempkmvisited = mysqli_fetch_assoc($resempkmvisited)){*/
			//For KM  calculation
			/*$sqltourown="SELECT latt,longi,trans_id FROM location WHERE emp_code = '".$emp_code."'  AND SUBSTRING(`date`,1,10)='".$tour_date_location."' 
							AND  TA_DA_mode='OWN'  ORDER BY `date` ASC";
			$restourown = mysqli_query($link,$sqltourown);
			$cnt_tour_own=mysqli_num_rows($restourown);*/
				$trans_id=$row_tour_details['trans_id'];
				$TA_DA_mode=$row_tour_details['TA_DA_mode'];
				if(count(${place_to.$emp_code.$tour_date})==0)
				{
				${place_to.$emp_code.$tour_date}=array();
				}
				if(substr($trans_id,0,1)=='O' || substr($trans_id,0,2)=='NO' || substr($trans_id,0,2)=='NE' )
				{
					
					if(substr($trans_id,0,1)=='O' || substr($trans_id,0,2)=='NO')
					{
					$sqlroute="SELECT RM.route_name FROM route_master RM,customer_master CM,order_header OH WHERE 
							CM.route_code=RM.route_code AND OH.customer_code=CM.customer_code AND OH.order_no='".$trans_id."'";
					$resroute= mysqli_query($link,$sqlroute);
					$rowroute=mysqli_fetch_assoc($resroute);
					}
					if(substr($trans_id,0,1)=='NE')
					{
					$sqlroute="SELECT RM.route_name FROM route_master RM,customer_master CM WHERE CM.route_code=RM.route_code  AND 
								CM.customer_code='".$trans_id."'";
					$resroute= mysqli_query($link,$sqlroute);
					$rowroute=mysqli_fetch_assoc($resroute);
					}
					//echo '<br />';
					$route_name=$rowroute['route_name'];
					 if(!in_array($route_name,${place_to.$emp_code.$tour_date}))
					 {
						${place_to_string.$emp_code.$tour_date}=${place_to_string.$emp_code.$tour_date}.$route_name.',';
					 }
					 array_push(${place_to.$emp_code.$tour_date},$route_name);
				}
				if(substr($trans_id,0,2)=='CH' || substr($trans_id,0,2)=='DC')
				{
					$latitudeFromCH=$row_tour_details['latt'];	
					$longitudeFromCH=$row_tour_details['longi'];
					$addressFromCH=getReverseGeoAdd($latitudeFromCH,$longitudeFromCH);
					$place_to_CH=$addressFromCH;
					if($addressFromCH!='')
					{
					if(${place_to_string.$emp_code.$tour_date}!='')
						{
							${place_to_string.$emp_code.$tour_date}=${place_to_string.$emp_code.$tour_date}.','.$addressFromCH;
						}
						else
						{
							${place_to_string.$emp_code.$tour_date}=$addressFromCH;
						}
					}
				}
				if(substr($trans_id,0,1)=='A')
				{
					$latitudeFromA=$row_tour_details['latt'];	
					$longitudeFromA=$row_tour_details['longi'];
					$addressFromA=getReverseGeoAdd($latitudeFromA,$longitudeFromA);
					${place_from.$emp_code.$tour_date}=$addressFromA;
				}
				
				//echo ${'place_to_stringE00882022-11-01'};
			if(${tour_date.$emp_code}!=${prev_tour_date.$emp_code} && ${prev_tour_date.$emp_code}!='' && 
			${place_to_string.$emp_code.${prev_tour_date.$emp_code}}!='')
			{
				//s$att_activities=substr($rowTADA['att_activities'],0,-1);
				$sqlDA="SELECT att_activities FROM att_checkout_journey_info 
				WHERE  emp_code='".$emp_code."' AND SUBSTRING(create_date,1,10)='".date('Y-m-d',strtotime(${prev_tour_date.$emp_code}))."'";
				$resDA = mysqli_query($link,$sqlDA);
				$rowDA=mysqli_fetch_assoc($resDA);
				${att_activities.${prev_tour_date.$emp_code}}=$rowDA['att_activities'];
				
			
			$TA_tot=${distancefinal.$emp_code.${prev_tour_date.$emp_code}}*$per_km_TA;
			if(rtrim(${att_activities.${prev_tour_date.$emp_code}})=='Outstation,') 	${DA_tot.${prev_tour_date.$emp_code}}=$DA_outstation;
			if(rtrim(${att_activities.${prev_tour_date.$emp_code}})=='HQ,') 			echo ${DA_tot.${prev_tour_date.$emp_code}}=$DA_HQ;
			if(rtrim(${att_activities.${prev_tour_date.$emp_code}})=='EX HQ,') 	   ${DA_tot.${prev_tour_date.$emp_code}}=$DA_EX_HQ;
			
			echo "<tr>
					<td>".${prev_tour_date.$emp_code}."</td>
					<td>".strtoupper(${place_from.$emp_code.${prev_tour_date.$emp_code}})."</td>
					<td>".substr(${place_to_string.$emp_code.${prev_tour_date.$emp_code}},0,-1)."</td>
					<td>OWN</td>
					<td>".${distancefinal.$emp_code.${prev_tour_date.$emp_code}}."</td>
					<td>".$TA_tot."</td>
					<td>".${DA_tot.${prev_tour_date.$emp_code}}."</td>
					<td align=\"right\">".$transport_other_charges."</td>
					<td align=\"right\">".$fooding_charges."</td>
					<td align=\"right\">".$hotel_charge."</td>
					<td align=\"right\">".$local_conveyance."</td>
					<td align=\"right\">".$other_expenses."</td>
					<td align=\"right\">".$total_amount."</td>
					<td>".strtoupper($remarks)."</td>
					<td>".$misc."</td>";
					if(strtoupper($_SESSION['nick_name'])=='ILS'){
				echo"<td>".$daily_assistance."</td>
					<td>".$km_traveled."</td>";
					}
					echo"<td>".$image_string."</td>
				</tr>";
			}
					

					if(${countkm.$emp_code.$tour_date}==1 &&  ${latitudeToprev.$emp_code.$tour_date}=='' && ${longitudeToprev.$emp_code.$tour_date}=='')
					{
						${latitudeFrom.$emp_code.$tour_date}=$row_tour_details['latt'];	
						${longitudeFrom.$emp_code.$tour_date}=$row_tour_details['longi'];
						
					}
					else if(${countkm.$emp_code.$tour_date} > 1  && ${latitudeToprev.$emp_code.$tour_date}!='' && ${longitudeToprev.$emp_code.$tour_date}!='')
					{
						${latitudeFrom.$emp_code.$tour_date}=${latitudeToprev.$emp_code.$tour_date};	
						${longitudeFrom.$emp_code.$tour_date}=${longitudeToprev.$emp_code.$tour_date};
					}
					if(${countkm.$emp_code.$tour_date} > 1)
					{
						echo '<br />';
						echo $tour_date;
						echo '<br />';
						echo '---------------------------------------------------';
						echo ${latitudeFrom.$emp_code.$tour_date};
						echo '<br />';
						echo ${longitudeFrom.$emp_code.$tour_date};
						echo '<br />';
						
						echo ${latitudeTo.$emp_code.$tour_date}=$row_tour_details['latt'];
						echo '<br />';
						echo ${longitudeTo.$emp_code.$tour_date}=$row_tour_details['longi'];
						echo '<br />';
						echo 'S----'.$distance=round((haversineGreatCircleDistance(${latitudeFrom.$emp_code.$tour_date}, ${longitudeFrom.$emp_code.$tour_date}, ${latitudeTo.$emp_code.$tour_date}, ${longitudeTo.$emp_code.$tour_date}, $earthRadius = 6371000))/1000,2);
						echo 'E----'.${distancefinal.$emp_code.$tour_date}=${distancefinal.$emp_code.$tour_date}+$distance;
						
						/*if($cnt_tour_own==${countkm.$emp_code.$tour_date_location})
						{
							$addressTo=getReverseGeoAdd($latitudeTo,$longitudeTo);
							$tour_place_to=$addressTo;
						}*/

						${latitudeToprev.$emp_code.$tour_date}=${latitudeTo.$emp_code.$tour_date};
						${longitudeToprev.$emp_code.$tour_date}=${longitudeTo.$emp_code.$tour_date};
					}
			
				//}
			
				//$countrow++;
			   ${prev_tour_date.$emp_code}=$tour_date;
			   	${countkm.$emp_code.$tour_date}++;
				${count_emp_rows.$emp_code}++;

			}
			$sqlDA="SELECT att_activities FROM att_checkout_journey_info 
				WHERE  emp_code='".$emp_code."' AND SUBSTRING(create_date,1,10)='".date('Y-m-d',strtotime($latest_tour_date))."'";
				$resDA = mysqli_query($link,$sqlDA);
				$rowDA=mysqli_fetch_assoc($resDA);
				${att_activities.$latest_tour_date}=$rowDA['att_activities'];
				
			
			$TA_tot=${distancefinal.$emp_code.$latest_tour_date}*$per_km_TA;
			if(rtrim(${att_activities.$latest_tour_date})=='Outstation,') 	${DA_tot.$latest_tour_date}=$DA_outstation;
			if(rtrim(${att_activities.$latest_tour_date})=='HQ,') 			echo ${DA_tot.$latest_tour_date}=$DA_HQ;
			if(rtrim(${att_activities.$latest_tour_date})=='EX HQ,') 	   ${DA_tot.$latest_tour_date}=$DA_EX_HQ;
			
			echo "<tr>
					<td>".$latest_tour_date."</td>
					<td>".strtoupper(${place_from.$emp_code.$latest_tour_date})."</td>
					<td>".substr(${place_to_string.$emp_code.$latest_tour_date},0,-1)."</td>
					<td>OWN</td>
					<td>".${distancefinal.$emp_code.$latest_tour_date}."</td>
					<td>".$TA_tot."</td>
					<td>".${DA_tot.$latest_tour_date}."</td>
					<td align=\"right\">".$transport_other_charges."</td>
					<td align=\"right\">".$fooding_charges."</td>
					<td align=\"right\">".$hotel_charge."</td>
					<td align=\"right\">".$local_conveyance."</td>
					<td align=\"right\">".$other_expenses."</td>
					<td align=\"right\">".$total_amount."</td>
					<td>".strtoupper($remarks)."</td>
					<td>".$misc."</td>";
					if(strtoupper($_SESSION['nick_name'])=='ILS'){
				echo"<td>".$daily_assistance."</td>
					<td>".$km_traveled."</td>";
					}
					echo"<td>".$image_string."</td>
				</tr>";
				
		/*echo "<tr>
				<td colspan=\"4\" align=\"right\" style=\"font-weight:bold;\">Total</td>
				<td align=\"right\">".$sum_local_conveyance."</td>
				<td align=\"right\">".$sum_transport_fair."</td>
				<td align=\"right\">".$sum_fooding_allowance."</td>
				<td align=\"right\">".$sum_hotel_charge."</td>
				<td align=\"right\">".$sum_other_expenses."</td>
				<td></td>
			</tr>";
		$sum_total_expenses = ($sum_local_conveyance+$sum_transport_fair+$sum_fooding_allowance+$sum_hotel_charge+$sum_other_expenses);
		echo "<tr>
				<th colspan=\"4\" align=\"right\" style=\"font-weight:bold;\">Total</th>
				<td align=\"left\" colspan=\"6\"><b>".$sum_total_expenses."</b></td>
			</tr>";
		
		$sum_local_conveyance = '';
		$sum_transport_fair = '';
		$sum_fooding_allowance = '';
		$sum_hotel_charge = '';
		$sum_other_expenses = '';*/
	}
	?>
    </table>
    </div>
    <?php
	}
else{
	echo "<font color=\"red\"><strong>No records</strong></font>";
}
?>
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display_data');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv('#display_data');">