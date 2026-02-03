<?php
session_start();

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code = $_REQUEST['emp_code'];
//$emp_code='E0924';
$mode = $_REQUEST['mode'];
$page = $_REQUEST['page'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
if ($mode == 'MTD') {
	$date_condition = " AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE())  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
} else if ($mode == 'YTD') {
	$date_condition = " AND YEAR(LO.date) = YEAR(CURDATE())";
} else if ($mode == 'yourchoice') {
	$date_condition = " AND (DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "') ";
} else {
	$date_condition = "";
}

$sqlemp = "SELECT emp_name FROM employee_master WHERE emp_code='" . $emp_code . "'";
$rsemp = mysqli_query($link, $sqlemp) or die(mysqli_error() . " Error in select employee name and code : " . $sqlemp);
$rowemp = mysqli_fetch_assoc($rsemp);
$emp_name = $rowemp['emp_name'];
?>
<style type="text/css">
	.TDHEAD {
		FONT-FAMILY: Verdana;
		FONT-SIZE: 11px;
		FONT-WEIGHT: bold;
		COLOR: #FFFFFF;
		BACKGROUND-COLOR: #A92A61;
		/*#92C006;*/
	}

	.TDHEAD_SUB {
		FONT-FAMILY: Verdana;
		FONT-SIZE: 11px;
		FONT-WEIGHT: bold;
		BACKGROUND-COLOR: #c0c8b0;
	}

	.border {
		BORDER: #A92A61
			/*#80A537*/
			1px solid;
	}

	TD {
		FONT-FAMILY: Verdana;
		FONT-SIZE: 11px;
	}
</style>
<?php
if (DCR_map == 'yes') {
	$locatestring = '<td width="20%" align="left" style="padding-left:20px;">Locate</td>';
	$locatestringcheckout = '';
	if (checkout == 'yes') {
		$locatestringcheckout = '<td width="20%" align="left" style="padding-left:20px;">Checkout Locate</td>';
	}
} else {
	$locatestring = '';
	$locatestringcheckout = '';
}
$tableval = '
<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 250px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="6" align="center"><strong>Emp Name: ' . $emp_name . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Emp Code:' . $emp_code . '</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
        <td width="30%" align="left" style="padding-left:20px;">Date</td>
        <td width="30%" align="left" style="padding-left:20px;">Check-In Time</td>
		 <td width="" align="left" style="padding-left:20px;">Check-Out Time</td><td width="" align="left" style="padding-left:20px;">Duration</td>' . $locatestring . $locatestringcheckout . '
    </tr> ';
$sqlinformation = "SELECT DATE_FORMAT(date,'%T') AS time,DATE_FORMAT(date,'%b,%e %Y') AS date,trans_id,DATE_FORMAT(date,'%Y-%m-%d') AS dateformat,LO.latt,LO.longi FROM 
                    location LO WHERE LO.trans_id LIKE 'A%' AND LO.emp_code='" . $emp_code . "' " . $date_condition . " ORDER BY DATE_FORMAT(date,'%Y-%m-%d') DESC ";
$resinformation = mysqli_query($link, $sqlinformation) or die(mysqli_error() . " Error in select transaction information: " . $sqlinformation);
$count = mysqli_num_rows($resinformation);
$cnt = $GLOBALS[$start] + 1;
while ($rowinformation = mysqli_fetch_assoc($resinformation)) {
	$trans_id = $rowinformation['trans_id'];
	$latt = $rowinformation['latt'];
	$longi = $rowinformation['longi'];
	$time = $rowinformation['time'];
	$date = $rowinformation['date'];
	$dateformat = $rowinformation['dateformat'];
	$sql_checkout = "SELECT SUBSTRING(LO.date,12) AS checkout_time,LO.trans_id,LO.latt,LO.longi FROM location LO WHERE 
							LO.emp_code = '" . $emp_code . "' AND LO.trans_id LIKE 'CH%' AND SUBSTRING(LO.date,1,10)='" . $dateformat . "'";
	$res_checkout = mysqli_query($link, $sql_checkout);
	$row_checkout = mysqli_fetch_assoc($res_checkout);
	$check_out_time = $row_checkout['checkout_time'];
	$chk_out_trans_id = $row_checkout['trans_id'];
	$chk_out_latt = $row_checkout['latt'];
	$chk_out_longi = $row_checkout['longi'];
	$today = date("Y-m-d");
	if ($check_out_time == '' && strtoupper($_SESSION['nick_name']) == 'STAR') {
		if ($dateformat == $today) {
        $check_out_time = '--';
    } else {
        $check_out_time = '--';
    }
	} else if ($check_out_time == '' && strtoupper($_SESSION['nick_name']) != 'STAR') {

		$check_out_time = '--';
	}
	if (DCR_map == 'yes') {
		if ($latt == 0 && $longi == 0) {
			$locateval = "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
		} else {
			$locateval = '<td align="left" valign="top" style="padding-left:20px;"><a href="adminAttendanceLocate.php?trans_id=' . $trans_id . '&emp_code=' . $emp_code . '&radio_search=employee&mode=' . $mode . '&page=' . $page . '&start_date=' . $start_date . '&end_date=' . $end_date . '" style="color:#930;font-weight:bold;">Locate</a></td>';
		}
		$locatevalchkout = '';
		if (checkout == 'yes') {
			if ($chk_out_latt == 0 && $chk_out_longi == 0 && $check_out_time != '--') {
				$locatevalchkout = "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
			} else if ($chk_out_latt == 0 && $chk_out_longi == 0 && $check_out_time == '--') {
				$locatevalchkout = "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">--</td>";
			} else {
				$locatevalchkout = '<td align="left" valign="top" style="padding-left:20px;"><a href="adminAttendanceLocate.php?trans_id=' . $chk_out_trans_id . '&emp_code=' . $emp_code . '&radio_search=employee&mode=' . $mode . '&page=' . $page . '&start_date=' . $start_date . '&end_date=' . $end_date . '" style="color:#930;font-weight:bold;">Locate</a></td>';
			}
		}
	} else {
		$locateval = '';
		$locatevalchkout = '';
	}
	if ($check_out_time != '--') {
		$time_difference = strtotime($check_out_time) - strtotime($time);
		$time_difference_final = $time_difference;
		if ($time_difference_final >= 3600) {
			$hours = floor($time_difference_final / 3600);
			$minutes = floor(($time_difference_final / 60) % 60);
			$seconds = $time_difference_final % 60;
			$time_duration = $hours . ' Hour(s) ' . $minutes . ' Minute(s) ' . $seconds . ' Second(s)';
		} else if ($time_difference_final >= 60 && $time_difference_final < 3600) {
			$minutes = floor(($time_difference_final / 60) % 60);
			$seconds = $time_difference_final % 60;
			$time_duration = $minutes . ' Minute(s) ' . $seconds . ' Second(s)';
		} else {
			$seconds = $time_difference_final % 60;
			$time_duration = $seconds . ' Second(s)';
		}
	} else {
		$time_duration = '--';
	}
	$rowval .= '<tr> 
                    <td valign="top" align="center">' . $cnt++ . '</td>
                    <td align="left" valign="top" style="padding-left:20px;">' . $date . '</td>
                    <td align="left" valign="top" style="padding-left:20px;">' . $time . '</td>
					<td align="left" valign="top" style="padding-left:20px;">' . $check_out_time . '</td>
					<td align="left" valign="top" style="padding-left:20px;">' . $time_duration . '</td>' . $locateval . $locatevalchkout . '
              </tr>';
}
$tablevalend = '</table>';
$finalval = $tableval . $rowval . $tablevalend;

echo $finalval;

mysqli_close($link);
?>