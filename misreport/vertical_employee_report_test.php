<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	
disphtml("main();");
function main()
{
		
?>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
  webshims.setOptions('waitReady', false);
  webshims.setOptions('forms-ext', {types: 'date'});
  webshims.polyfill('forms forms-ext');
</script>
</head>
<body>
<center>
<?php
/*---------------------------------> Display vertical for ADMIN <--------------------------------*/
	if($_SESSION['admin_login'] == 'admin'){
		$vertical_name_array = array();
		$current_date = date('Y-m-d');
		/*$sql_attendance_verticalwise = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', 1) as distinct_vertical_value FROM employee_master EM, location LO WHERE EM.emp_code = LO.emp_code AND SUBSTRING(LO.date,1,10)='".$current_date."' UNION DISTINCT SELECT DISTINCT OH.vertical_value FROM order_header OH, location LO WHERE LO.trans_id=OH.order_no AND SUBSTRING(LO.date,1,10)='".$current_date."'";*/
		$sql_attendance_verticalwise = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', -1) as distinct_vertical_value FROM employee_master 
		EM WHERE SUBSTRING_INDEX( EM.vertical_value, ',', -1 ) != ''";
		$res_attendance_verticalwise = mysqli_query($link,$sql_attendance_verticalwise);
		$total_rows = mysqli_num_rows($res_attendance_verticalwise);
		if($total_rows>0){
			$res_attendance_verticalwise = mysqli_query($link,$sql_attendance_verticalwise);
			while($row_attendance_verticalwise = mysqli_fetch_assoc($res_attendance_verticalwise)){
				$dist_vert_value = trim($row_attendance_verticalwise['distinct_vertical_value']);
				if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($dist_vert_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}
				
				if($dist_vert_value != ''){
					if(!in_array($dist_vert_value,$vertical_name_array))
						array_push($vertical_name_array,$dist_vert_value);
				}
			}
			echo "<table width='60%' style='border-collapse:collapse;' cellpadding='6px'><tr>";
					
			foreach($vertical_name_array as $value){
					$image = $value.".jpg";
					echo "<td><img src='images/".$image."' style=\"cursor:pointer\" onclick=\"show_data('$value');\"></td>";
			}
					echo "<tr></table>";
		}
		else{
			echo "No vertical to select";
		}
	}  /*-------------------------------> Display vertical for specific employee -------------------------------*/
	else{
		$vertical_name_array=array();
		$sql_get_vertical = "SELECT vertical_value FROM employee_master WHERE emp_code = '".$_SESSION['admin_login']."'";
		$res_get_vertical = mysqli_query($link,$sql_get_vertical);
		$row_get_vertical = mysqli_fetch_assoc($res_get_vertical);
		$get_vertical = $row_get_vertical['vertical_value'];
		if(strpos($get_vertical,',') != false){
			$get_vertical_array = explode(",",$get_vertical);
			//print_r($get_vertical_array);
			foreach($get_vertical_array as $get_vertical_value){
				if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($get_vertical_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}
				else{
					$dist_vert_value = $get_vertical_value;
				}
				
				if(!in_array($dist_vert_value,$vertical_name_array))
				array_push($vertical_name_array,$dist_vert_value);
			}
			//print_r($vertical_name_array);
			echo "<table width='60%' style='border-collapse:collapse;' cellpadding='6px'><tr>";
			foreach($vertical_name_array as $value){
					$image = $value.".jpg";
					echo "<td><img src='images/".$image."' style=\"cursor:pointer\" onclick=\"show_data('$value');\"></td>";
			}
			echo "<tr></table>";
		}
		else{
			$get_vertical_array = $get_vertical;
			if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
				$pos = substr($get_vertical_array,0,1);
				if($pos == 'M'){
					$get_vertical_array = 'MACROMAN';
				}
			}
			echo "<table width='60%' style='border-collapse:collapse;' cellpadding='6px'><tr>";
			$image = $get_vertical_array.".jpg";
			echo "<td><img src='images/".$image."' style=\"cursor:pointer\" onclick=\"show_data('$get_vertical_array');\"></td>";
			echo "<tr></table>";
		}
	}
	
?>
<br />
<div id="display" style="max-height: 480px; width:70%; overflow-y: scroll; margin-left:10px;" align="center">
</div>
<br>
<div id="date_div" style="width:60%;" >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<!--input type="submit" name="submit" value="Submit" onClick="show_emp();" /-->
</div>
<br>
</center>
</body>

<script>
/*---------------------------------> Function called when onclick vertical image <--------------------------------*/
function show_data(vertical_name)
{
	//alert(veritcal_name);
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	if(start_date != ''){
		if(document.getElementById("end_date").value.search(/\S/) == -1){
			alert('Provide end date');
			return false;
		}
	}
	if(end_date != ''){
		if(document.getElementById("start_date").value.search(/\S/) == -1){
			alert('Provide start date');
			return false;
		}
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('vertical_report_employee_data_test.php?vertical_name='+vertical_name+'&start_date='+start_date+'&end_date='+end_date,'display',0);
}

/*---------------------------------> Function called when clicked on SUBMIT <--------------------------------*/
function show_emp()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Provide start date');
		return false;
	}
	if(document.getElementById("end_date").value.search(/\S/) == -1){
		alert('Provide end date');
		return false;
	}
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('vertical_report_employee_data.php?start_date='+start_date+'&end_date='+end_date,'display',0);
	
}

function PrintElem(elem)
{
   Popup($(elem).html());
}
/*---------------------------------> Print data <--------------------------------*/
function Popup(data) 
{
	var mywindow = window.open('', 'Daily Activity Analysis', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Daily Activity Analysis</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}
/*---------------------------------> Export to excel <--------------------------------*/
function exporttocsv(divid)
{
	//alert(divid);
        //getting values of current time for generating the file name
        var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('display');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Daily Activity Analysis' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}
</script>
<?php
	}
?>