<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	function main(){
	if($_POST['submit'] == 'Submit'){
		$year = $_POST['year'];
		$month_name = $_POST['month_name'];
		$month = date('m',strtotime(''.$month_name.''));
		
		$list=array();
		for($d=1; $d<=31; $d++)
		{
			$time=mktime(12, 0, 0, $month, $d, $year);          
			if (date('m', $time)==$month)       
				$list[]=date('Y-m-d', $time);
		}
		$tomorrow = date('Y-m-d',strtotime('tomorrow'));
		/*echo "<pre>";
		print_r($list);
		echo "</pre>";*/
		$latt_long_array = array();
	?>
    <center><br />
	<div id="display" style="max-height: 500px; width:80%; overflow-y: scroll;" align="center">
    <?php
	echo "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" cellpadding=\"4px\" class=\"border\">";
		echo "<tr align=\"center\" class=\"TDHEAD_SUB\">
				<td>In Date</td>
				<td>In Time</td>
				<td>In Lat</td>
				<td>In Long</td>
				<td>Out Time</td>
				<td>Out Lat</td>
				<td>Out Long</td>
			  </tr>";
	/*----------------------------> Select Employee <----------------------------*/
	$sql_select_empl = "SELECT emp_code,emp_name,dns_emp_code FROM employee_master ORDER BY emp_name ASC";
	$res_select_empl = mysqli_query($link,$sql_select_empl);
	while($row_select_empl = mysqli_fetch_assoc($res_select_empl)){
		$emp_code = $row_select_empl['emp_code'];
		$emp_name = $row_select_empl['emp_name'];
		$dns_emp_code = $row_select_empl['dns_emp_code'];
		echo "<tr style=\"font-weight:bold;\">
				<td colspan=\"10\" align=\"center\" bgcolor=\"#CCCCCC\">".$emp_name." - ".$dns_emp_code."</td>
			  </tr>";
		foreach($list as $list_value){
			if($list_value == $tomorrow)
				break;
			/*----------------------------> Select login time, latt, long <----------------------------*/
			$sql_login_time = "SELECT SUBSTRING(date,12) as login_time, latt, longi FROM location WHERE emp_code = '".$emp_code."' AND trans_id LIKE 'A%' AND SUBSTRING(date,1,10) = '".$list_value."'";
				$res_login_time = mysqli_query($link,$sql_login_time);
				$total_rows = mysqli_num_rows($res_login_time);
				if($total_rows>0){
					$res_login_time = mysqli_query($link,$sql_login_time);
					$row_login_time = mysqli_fetch_assoc($res_login_time);
					$login_time = $row_login_time['login_time'];
					$latt = $row_login_time['latt'];
					$long = $row_login_time['longi'];
					//@$geocode=file_get_contents('http://open.mapquestapi.com/nominatim/v1/reverse.php?key=b2GmTuzpMDHNrERI3wEBIs0hNuACxAAW&format=json&lat='.$latt.'&lon='.$long.'');
					//@$output= json_decode($geocode,true);
					//$address = $output['display_name'];
					
					/*$new_lat = number_format($latt,3);
					$new_long = number_format($long,3);
					$latt_long = $new_lat."^".$new_lat;
					if(!in_array($latt_long,$latt_long_array)){
						array_push($latt_long_array,$latt_long);
						$geocode=file_get_contents('http://open.mapquestapi.com/nominatim/v1/reverse.php?key=b2GmTuzpMDHNrERI3wEBIs0hNuACxAAW&format=json&lat='.$latt.'&lon='.$long.'');
						$output= json_decode($geocode,true);
						$address = $output['display_name'];
						$location_array[$new_lat."^".$new_long] = $address;
					}*/
					
					/*----------------------------> Select check out time <----------------------------*/									
					$sql_checkout_time = "SELECT MAX(SUBSTRING(date,12)) as checkout_time,latt,longi FROM location WHERE emp_code = '".$emp_code."' AND SUBSTRING(date,1,10) = '".$list_value."' AND trans_id LIKE 'CH%'";
					$res_checkout_time = mysqli_query($link,$sql_checkout_time);
					$row_checkout_time = mysqli_fetch_assoc($res_checkout_time);
					$checkout_time = $row_checkout_time['checkout_time'];
					$checkout_latt = $row_checkout_time['latt'];
					$checkout_longi = $row_checkout_time['longi'];
					
					//$total_working_hours = $checkout_time - $login_time;
					echo "<tr>
							<td>".date('d-m-Y',strtotime(''.$list_value.''))."</td>
							<td>".$login_time."</td>
							<td align=\"right\">".$latt."</td>
							<td align=\"right\">".$long."</td>
							<td>".$checkout_time."</td>
							<td align=\"right\">".$checkout_latt."</td>
							<td align=\"right\">".$checkout_longi."</td>
						  </tr>";
				}
				else{
					echo "<tr>
							<td>".date('d-m-Y',strtotime(''.$list_value.''))."</td>
							<td colspan=\"10\" align=\"center\">A</td>
						  </tr>";
				}
		}
	}
	echo "</table>";
	/*echo "<pre>";
	print_r($latt_long_array);
	print_r($location_array);
	echo "</pre>";*/
	?>
    </div>
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
    </center>
    <script>
function exporttocsv(divid)
{
	    //getting values of current time for generating the file name
        var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var displaydiv = document.getElementById("display").innerHTML;
	    /*document.write('<div id=\'view\'>');
		document.write(displaydiv);
		document.write('<div>');*/
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('displaydiv');
        var table_html = display.innerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Attendance Download' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        //e.preventDefault();
}
</script>
    <?php
	}
	}
?>