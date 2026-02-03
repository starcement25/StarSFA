<?php
ob_start();
	session_start();
	require("adminUtils.php");
/*	require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");*/
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	function main(){
	    require("include/dbcon.php");
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
				<td>Date</td>
				<td>Location</td>
				<td>Login Time</td>
				<td>Chkout Time</td>
				<td>Working Hrs</td>
				<td>Retails Entry</td>
				<td>&gt;10:45 A.M</td>
				<td>&lt;15</td>
				<td>&gt;15 but &lt;25</td>
				<td>&gt;25</td>
			  </tr>";
	/*----------------------------> Select Employee <----------------------------*/
	$sql_select_empl = "SELECT emp_code, emp_name FROM employee_master ORDER BY emp_name ASC";
	$res_select_empl = mysqli_query($link,$sql_select_empl);
	while($row_select_empl = mysqli_fetch_assoc($res_select_empl)){
		$emp_code = $row_select_empl['emp_code'];
		$emp_name = $row_select_empl['emp_name'];
		echo "<tr style=\"font-weight:bold;\">
				<td colspan=\"10\" align=\"center\" bgcolor=\"#CCCCCC\">".$emp_name."</td>
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
					@$geocode=file_get_contents('http://open.mapquestapi.com/nominatim/v1/reverse.php?key=b2GmTuzpMDHNrERI3wEBIs0hNuACxAAW&format=json&lat='.$latt.'&lon='.$long.'');
					@$output= json_decode($geocode,true);
					$address = $output['display_name'];
					
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
					$sql_checkout_time = "SELECT MAX(SUBSTRING(date,12)) as checkout_time FROM location WHERE emp_code = '".$emp_code."' AND SUBSTRING(date,1,10) = '".$list_value."'";
					$res_checkout_time = mysqli_query($link,$sql_checkout_time);
					$row_checkout_time = mysqli_fetch_assoc($res_checkout_time);
					$checkout_time = $row_checkout_time['checkout_time'];
					
					//$total_working_hours = $checkout_time - $login_time;
					/*----------------------------> Select total call made by employee ----------------------------*/
					$sql_retail_entries = "SELECT COUNT(trans_id) as total_retail_entry FROM location WHERE emp_code = '".$emp_code."' AND SUBSTRING(date,1,10) = '".$list_value."' AND trans_id LIKE 'SU%'";
					$res_retail_entries = mysqli_query($link,$sql_retail_entries);
					$row_retail_entries = mysqli_fetch_assoc($res_retail_entries);
					$total_retail_entries = $row_retail_entries['total_retail_entry'];
					
					if($total_retail_entries<15)
						$retail_color_one = "#FF0000";
					else
						$retail_color_one = "";
					
					if($total_retail_entries>=15 && $total_retail_entries<25)
						$retail_color_two = "#FFFF00";
					else
						$retail_color_two = "";
					
					if($total_retail_entries>=25)
						$retail_color_three = "#00EE00";
					else
						$retail_color_three = "";
					
					if($login_time>'10:45:00'){
						$atd_color = "#FF0000";
					}
					else{
						$atd_color = "#00EE00";
					}
					
					$datetime1 = new DateTime($login_time);
					$datetime2 = new DateTime($checkout_time);
					$interval = $datetime1->diff($datetime2);
					$elapsed = $interval->format('%H');
										
					echo "<tr>
							<td>".date('d-m-Y',strtotime(''.$list_value.''))."</td>
							<td>".$address."</td>
							<td>".$login_time."</td>
							<td>".$checkout_time."</td>
							<td align=\"right\">".$elapsed."</td>
							<td align=\"right\">".$total_retail_entries."</td>
							<td bgcolor=\"$atd_color\"></td>
							<td bgcolor=\"$retail_color_one\"></td>
							<td bgcolor=\"$retail_color_two\"></td>
							<td bgcolor=\"$retail_color_three\"></td>
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