<?php
ob_start();
session_start();

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

//$emp_code = $_REQUEST['emp_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);

$sql_emp_code = "SELECT DISTINCT emp_code FROM tour_expenses_details WHERE emp_code IN(".$employee.")
				AND (tour_date_from >= '".$start_date."' AND tour_date_from <='".$end_date."')";
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
        	<td width="7%">Date</td>
            <td width="10%">Place From</td>
            <td width="10%">Place TO</td>
            <td width="10%">Travel Mode1</td>
            <td width="10%">Travel Mode2</td>
            <td width="10%">Travel Mode3</td>
            <td width="6%">Fare1<br />(Amount in RS)</td>
            <td width="6%">Fare2<br />(Amount in RS)</td>
            <td width="6%">Fare3<br />(Amount in RS)</td>
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
				
		$sql_tour_details = "SELECT * FROM tour_expenses_details WHERE emp_code = '".$emp_code."' AND 
						(tour_date_from >= '".$start_date."' AND tour_date_from <='".$end_date."') ORDER BY tour_date_from ASC";
		$res_tour_details = mysqli_query($link,$sql_tour_details);
		$cnt_tour_details=mysqli_num_rows($res_tour_details);				

		$countrow=1;
		while($row_tour_details = mysqli_fetch_assoc($res_tour_details)){
			$tour_date = date('d-m-Y',strtotime($row_tour_details['tour_date_from']));
			$tour_place_from = $row_tour_details['tour_place_from'];
			$tour_place_to = $row_tour_details['tour_place_to'];
			$transport_mode_type = $row_tour_details['transport_mode_type'];
			$transport_other_charges = $row_tour_details['transport_other_charges'];
			
			
			$transport_mode_type2 = $row_tour_details['transport_mode_type2'];
			$transport_other_charges2 = $row_tour_details['transport_fair2'];
			$transport_mode_type3 = $row_tour_details['transport_mode_type3'];
			$transport_other_charges3 = $row_tour_details['transport_fair3'];
			
			
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
			}
			
			/*$sum_local_conveyance += $local_conveyance;
			$sum_transport_fair += $transport_fair;
			$sum_fooding_allowance += $fooding_allowance;
			$sum_hotel_charge += $hotel_charge;
			$sum_other_expenses += $other_expenses;*/
			${'transport_other_charges'.$emp_code}=${'transport_other_charges'.$emp_code}+$transport_other_charges;
			${'transport_other_charges2'.$emp_code}=${'transport_other_charges2'.$emp_code}+$transport_other_charges2;
			${'transport_other_charges3'.$emp_code}=${'transport_other_charges3'.$emp_code}+$transport_other_charges3;
			${'fooding_charges'.$emp_code}=${'fooding_charges'.$emp_code}+$fooding_charges;
			${'hotel_charge'.$emp_code}=${'hotel_charge'.$emp_code}+$hotel_charge;
			${'local_conveyance'.$emp_code}=${'local_conveyance'.$emp_code}+$local_conveyance;
			${'other_expenses'.$emp_code}=${'other_expenses'.$emp_code}+$other_expenses;
			$total_amount=($transport_other_charges+$fooding_charges+$hotel_charge+$local_conveyance+$other_expenses+$transport_other_charges2+$transport_other_charges3);
			${'total_amount'.$emp_code}=${'transport_other_charges'.$emp_code}+${'fooding_charges'.$emp_code}+${'hotel_charge'.$emp_code}+${'local_conveyance'.$emp_code}+${'other_expenses'.$emp_code}+${'transport_other_charges3'.$emp_code}+${'transport_other_charges2'.$emp_code};

			$tour_photo = ltrim($transport_attachment," ");
			$tour_photo = rtrim($tour_photo," ");
			$tour_photo = rtrim($tour_photo,";");
			$tour_photo=str_replace('.JPEG','.jpeg',$tour_photo);
			$tour_photo_array = explode(";",$tour_photo);
			
			$image_string = '';
			foreach($tour_photo_array as $image){
				$image = ltrim($image," ");
				if($image != '')
				$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
			}
			if($transport_other_charges==0) { $transport_other_charges='-'; }
			else{ $transport_other_charges=number_format($transport_other_charges,2);}
			if($fooding_charges==0) { $fooding_charges='-';}
			else{ $fooding_charges=number_format($fooding_charges,2);}
			if($hotel_charge==0){ $hotel_charge='-';}
			else{$hotel_charge=number_format($hotel_charge,2);}
			if($local_conveyance==0){ $local_conveyance='-';}
			else{$local_conveyance=number_format($local_conveyance,2);}
			if($other_expenses==0){ $other_expenses='-';}
			else{$other_expenses=number_format($other_expenses,2);}
			if($total_amount==0){ $total_amount='-';}
			else{$total_amount=number_format($total_amount,2);}
			echo "<tr>
					<td>".$tour_date."</td>
					<td>".strtoupper($tour_place_from)."</td>
					<td>".strtoupper($tour_place_to)."</td>
					<td>".strtoupper($transport_mode_type)."</td>
					<td>".strtoupper($transport_mode_type2)."</td>
					<td>".strtoupper($transport_mode_type3)."</td>
					<td align=\"right\">".$transport_other_charges."</td>
					
					
					<td align=\"right\">".$transport_other_charges2."</td>
					
					<td align=\"right\">".$transport_other_charges3."</td>
					
					<td align=\"right\">".$fooding_charges."</td>
					<td align=\"right\">".$hotel_charge."</td>
					<td align=\"right\">".$local_conveyance."</td>
					<td align=\"right\">".$other_expenses."</td>
					<td align=\"right\">".$total_amount."</td>
					<td>".strtoupper(str_replace('#','',$remarks))."</td>
					<td>".$misc."</td>";
					if(strtoupper($_SESSION['nick_name'])=='ILS'){
				echo"<td>".$daily_assistance."</td>
					<td>".$km_traveled."</td>";
			}
					echo"<td>".$image_string."</td>
				</tr>";
				
				if($countrow==$cnt_tour_details)
				{
					echo "<tr>
					<td colspan=\"6\" align=\"center\"><b>TOTAL</b></td>					
					<td align=\"right\"><b>".number_format(${'transport_other_charges'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'transport_other_charges2'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'transport_other_charges3'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'fooding_charges'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'hotel_charge'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'local_conveyance'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'other_expenses'.$emp_code},2)."</b></td>
					<td align=\"right\"><b>".number_format(${'total_amount'.$emp_code},2)."</b></td>
					<td></td>
					<td></td>";
					if(strtoupper($_SESSION['nick_name'])=='ILS'){
					echo "<td></td><td></td>";
					}
					echo "<td></td>
				</tr>";
				}
				$countrow++;
				
		}
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