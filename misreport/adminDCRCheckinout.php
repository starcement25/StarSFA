<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	
	if($_REQUEST['mode_type'] == 'mode_type' || $_REQUEST['mode_type'] == 'all_emp' || $_REQUEST['mode'] == 'T')
		disphtml("main();");
	else if($_REQUEST['mode'] == 'custom')
		disphtml("main();");
	else
		disphtml("date_selection();");
ob_end_flush();

function date_selection($start_date,$end_date){
	?>
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
    <?php
	$emp_code=$_REQUEST['emp_code'];
	$mode=$_REQUEST['mode'];
	$page=$_REQUEST['page'];
	?>
    <center>
    <form method="POST" action="#">
    <input type="hidden" name="emp_code" value="<?php echo $emp_code; ?>" />
    <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="mode_type" value="mode_type" />
    <table width="50%" style="background:#CCCCCC;" cellpadding="4">
      <tr>
      	<td>From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" /></td>
         <td>To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" /></td>
      </tr>
      <tr>
      	<td colspan="2" align="center"><input type="submit" name="submit" value="Submit" /></td>
      </tr>
    </table>
    </form>
    <center>
    <?php
}

function main()
{
?>
<script>
function validate(){
	if(document.getElementById("all_emp").value.search(/\S/) == -1){
		alert('Select option'); return false;
	}
}
function PrintElem(elem)
{
	var displaydiv = document.getElementById('display').innerHTML;	
	Popup(displaydiv);
}

function Popup(data) 
{
	var mywindow = window.open('', 'DCR Report', 'height=400,width=600');
	mywindow.document.write('<html><head><title>DCR Report</title>');
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

function exporttocsv(divid)
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	/*document.write('<div id=\'view\'>');
	document.write(view);
	document.write('<div>');*/
	//creating a temporary HTML link element (they support setting file names)*/
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'DCR Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
</script>
<?php
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	
	/*if($_REQUEST['mode'] != 'T')
	date_selection($start_date,$end_date);*/
	
	$emp_code=$_REQUEST['emp_code'];
	$mode=$_REQUEST['mode'];
	$page=$_REQUEST['page'];
	
	
	if($mode=='T')
	{
		$date=date('Y-m-d');
		$date_condition ="  AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
	} 
	if($mode=='MTD')
	{
		/*$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
							AND MONTH(DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";*/
		$date_condition = " AND (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
	}
	if($mode=='YTD')
	{
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		if($month>='04'){
			$fiinancial_year=$year.'-04-01';
		}
		else
		{
			$fiinancial_year=($year-1).'-04-01';
		}

		//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
		/*$date_condition=" AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
					AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";*/
		$date_condition = " AND (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
	}
	if($mode == 'custom') {
		$date_condition = " AND (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
	}
	
	if($_REQUEST['mode_type'] == 'all_emp'){
		$emp_condition = " emp_code IN(SELECT emp_code FROM employee_master) ";
	}
	else{
		$emp_condition = " emp_code='".$emp_code."' ";
	}

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
	
	function getReverseGeo($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
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
	function updateLatlong($emp_code)
	{
		//For check and update in the location table
		$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsselectlatlong=mysqli_query($link,$sqlselectlatlong) or die(mysqli_error()." Error in select zero latt longi: ".$sqlselectlatlong);
		$countselectlatlong=mysqli_num_rows($rsselectlatlong);
		
		if($countselectlatlong>0)
		{
			$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
			$rslastlatlong=mysqli_query($link,$sqllastlatlong) or die(mysqli_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
			$rowlastlatlong=mysqli_fetch_assoc($rslastlatlong);
			$lastlatt=$rowlastlatlong['latt'];
			$lastlongi=$rowlastlatlong['longi'];
			
			$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$rslastlatlong=mysqli_query($link,$sqlupdatelatlong) or die(mysqli_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	//updateLatlong($emp_code);
	$sqlemployee="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsemployee=mysqli_query($link,$sqlemployee);
	$rowemployee=mysqli_fetch_assoc($rsemployee);
	$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
	$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));
	$requiredate=$_REQUEST['requiredate'];
	if($requiredate!='')
	{
		$curdateserver=date('Y-m-d',strtotime($requiredate));
	}
	
	if($_REQUEST['mode_type'] == 'all_emp')
		$emp_name='';
	else
		$emp_name=$rowemployee['emp_name'];
		
		/*$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(DATE,'%Y-%m-%d %H:%i:%s') ASC ";*/
		$sqlcheckinout="SELECT emp_code, latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime,DATE_FORMAT(date,'%d-%m-%Y') as trandate 
						FROM location WHERE ".$emp_condition.$date_condition." AND (SUBSTRING(trans_id,1,1) IN('A') OR SUBSTRING(trans_id,1,2) IN('CI'))  ORDER BY emp_code ASC, SUBSTRING(trans_id,1,1) ASC, SUBSTRING(trans_id,-14,8) ASC, SUBSTRING(trans_id,-6) ASC ";
		$rscheckinout=mysqli_query($link,$sqlcheckinout);
		$countcheckinout=mysqli_num_rows($rscheckinout);
		$pointcoords=array();
		$customer_name_array=array();
		$customer_type_array=array();
		$activity_array=array();
		$trandate_array=array();
		$trantime_array=array();
		$check_in_time_array=array();
		$check_out_time_array=array();
		$remarks_array=array();
		$duration_array=array();
		$emp_code_array = array();
		$emp_code_array_check = array();
		$markersArr[]='';
		$pathpointsArr[]='';
		$pointlabel=1;
		if($countcheckinout>0)
		{
			while($rowcheckinout=mysqli_fetch_assoc($rscheckinout))
			{
				$point=$rowcheckinout['latt'].','.$rowcheckinout['longi'];
				$markersArr[]="markers=color:red|label:$pointlabel|$point";
				$markers=implode('&',$markersArr);
				$pathpointsArr[]=$point;
				$pathpoints=implode('|',$pathpointsArr);
				$pathpoints=substr($pathpoints,1);
				array_push($pointcoords,$point);
				
				$trans_id=$rowcheckinout['trans_id'];
				$operation_type=substr($trans_id,0,1);
				$time=$rowcheckinout['trantime'];
				$trandate=$rowcheckinout['trandate'];
				$emp_code_chk = $rowcheckinout['emp_code'];
				if($operation_type=='A')
				{
					$activity='Attendance';
					$customer_name='----';
					$check_in_time='----';
					$check_out_time='----';
					$time_duration='----';
					$remarks='----';
				}
				if($operation_type=='C')
					{
						$activity='Customer Activity';
						$sqlcustomer="SELECT CM.customer_name, CM.cust_type, CM.customer_code,CIOD.check_in_time,CIOD.check_out_time,CIOD.remarks 
									  FROM customer_master CM,check_in_out_details CIOD
									  WHERE CM.customer_code=CIOD.customer_code AND CIOD.trans_id='".$trans_id."'";
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select check in out details ".$sqlcustomer);
						
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$cust_type=$rowcustomer['cust_type'];
						$check_in_time=date('d-m-Y H:i:s',strtotime($rowcustomer['check_in_time']));
						$check_out_time=date('d-m-Y H:i:s',strtotime($rowcustomer['check_out_time']));
						$time_difference=strtotime($rowcustomer['check_out_time'])-strtotime($rowcustomer['check_in_time']);
						if($time_difference >=3600)
						{
							$hours = floor($time_difference / 3600);
							$minutes = floor(($time_difference / 60) % 60);
							$seconds = $time_difference % 60;
							$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
						}
						else if($time_difference >=60 && $time_difference<3600)
						{
							$minutes = floor(($time_difference / 60) % 60);
							$seconds = $time_difference % 60;
							$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
						}
						else
						{
							$seconds = $time_difference % 60;
							$time_duration=$seconds.' Second(s)';
						}
						$remarks=$rowcustomer['remarks'];
					}
					array_push($emp_code_array,$emp_code_chk);
					array_push($customer_name_array,$customer_name);
					array_push($customer_type_array,$cust_type);
					array_push($trantime_array,$time);
					array_push($trandate_array,$trandate);
					array_push($check_in_time_array,$check_in_time);
					array_push($check_out_time_array,$check_out_time);
					array_push($remarks_array,$remarks);
					array_push($duration_array,$time_duration);
					array_push($activity_array,$activity);
		
				$pointlabel++;
			}
			//echo $markers;
			//print_r($pointcoords);
			$subject='DCR of '.$emp_name.' on '.$datesubject;
			/*$body='<img src="http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.$pathpoints.'&sensor=false&'.$markers.'" alt=""><br /> <br />';*/
			$trandate_display_array=array();
			for($i=0;$i<count($customer_name_array);$i++)
			{
				$sl_no=$i+1;
				$point=($i+1);
				
				$Arrpointcoordsfrom =explode(',',$pointcoords[$i]);
				$latitudeFrom=$Arrpointcoordsfrom[0];
				$longitudeFrom=$Arrpointcoordsfrom[1];
				
				//$address=getReverseGeo($latitudeFrom,$longitudeFrom);
				
				if($i==0)
				{
					$distancetxt='---------';
				}
				else
				{
					$Arrpointcoordsfrom =explode(',',$pointcoords[$i-1]);
					$latitudeFrom=$Arrpointcoordsfrom[0];
					$longitudeFrom=$Arrpointcoordsfrom[1];
					
					$Arrpointcoordsto =explode(',',$pointcoords[$i]);
					$latitudeTo=$Arrpointcoordsto[0];
					$longitudeTo=$Arrpointcoordsto[1];
					$distance=round((haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000))/1000,2);
					$distancetxt=$distance." k.m";
				}
				if(($mode=='YTD' || $mode=='MTD') && !in_array($trandate_array[$i],$trandate_display_array))
				{
					$tranntimedisplayval="<tr><td align='center' colspan='8'><b>".$trandate_array[$i]."</b></td></tr><br />";
			        array_push($trandate_display_array,$trandate_array[$i]);				
				}
				else
				{
					$tranntimedisplayval='';
				}
				
				if(!in_array($emp_code_array[$i],$emp_code_array_check)){
					array_push($emp_code_array_check,$emp_code_array[$i]);
					$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code_array[$i]."'";
					$res_emp_name = mysqli_query($link,$sql_emp_name);
					$row_emp_name = mysqli_fetch_assoc($res_emp_name);
					$emp_name_check = $row_emp_name['emp_name'];
					$bodydetails.= "<tr class=\"TDHEAD\"><td colspan=\"9\" align=\"center\">DCR OF ".$emp_name_check."</td></tr>";
				}
				
				$bodydetails.=$tranntimedisplayval."<tr><td style='width:50px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$sl_no."</span>&nbsp;</td>	
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$activity_array[$i]."</span>&nbsp;</td>
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$customer_name_array[$i]."</span>&nbsp;</td>	
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$customer_type_array[$i]."</span>&nbsp;</td>	
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$check_in_time_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$check_out_time_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$duration_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$remarks_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$trantime_array[$i]."</span>&nbsp;</td>			
							  </tr>";
			}
			
			$body="<table border=1 style=background-color:AliceBlue width='100%' style='height: 500px;
						overflow-y: scroll;display:block;width: 50%;'>
					<tr>
					<th style='width:50px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Sl no.</span></strong></th>
					<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Activity</span></strong></th>
					<th style='width:100px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Dealer/Sub Dealer</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Check in Time</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Check out Time</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Duration</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Time</span></strong></th>
					</tr>
					".$bodydetails."</table>";
		}//End of count if
		$backurl='adminCheckinoutReport.php';
?>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0" >
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Employee Direct Call Report</strong></td>
	</tr>
    <tr>
		<td valign="top" bgcolor="#FFFFFF">
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="right" class="ERR" width="95%"><a href="javascript:void(0);" style="color: #e40000" 
                    onclick="javascript:window.location='<?=$backurl?>?start_date=<?php echo $_REQUEST['start_date']?>&end_date=<?php echo $_REQUEST['end_date']?>&emp_code=<?php echo $_REQUEST['emp_code']?>&mode=<?php echo $_REQUEST['mode']?>&page=<?php echo $_REQUEST['page']?>'"><img src="images/previous.png" alt="back" /></a></td>
					<td align="right" width="5%"><a href="<?=$url;?>" title=" Refresh the page"><img border="0" src="images/icon_reload.gif"></a></td>
				</tr>
			</table>
            <br /> <br /> <br />
            <div align="left">
                <form method="POST" action="#" onsubmit="return validate();">
                <input type="hidden" name="emp_code" value="<?php echo $emp_code; ?>" />
                <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
                <input type="hidden" name="page" value="<?php echo $page; ?>" />
                <input type="hidden" name="start_date" value="<?php echo $start_date; ?>" />
                <input type="hidden" name="end_date" value="<?php echo $end_date; ?>" />
                <input type="hidden" name="mode_type" value="all_emp" />
                <select id="all_emp" name="all_emp"><option value="">Select</option><option value="all">All</option>
                </select>&nbsp;<input type="submit" value="Submit" />
                </form>
            </div><br />
            <table width="100%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				
				<tr> 
					<td align="center"><div id="display"><?php echo $body;?></div></td>
                </tr>
                <tr>
                	<td>
                    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
                    </td>
                </tr>
            </table>
        </td>
     </tr>               
 </table>                   
 <?php }?>                   

