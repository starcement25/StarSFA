<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

if(strtoupper($_SESSION['nick_name']) == 'CASHLESS')
	$store_name = 'Company Name';
else 
	$store_name = 'Business Name';


$emp_code=$_REQUEST['emp_code'];

$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
$rsemp=mysql_query($sqlemp) or die(mysql_error()." Error in select employee name and code : ".$sqlemp);
$rowemp=mysql_fetch_array($rsemp);
$emp_name=$rowemp['emp_name'];

$mode=$_REQUEST['mode'];
$page=$_REQUEST['page'];
if($mode=='T')
{
	$date=date('Y-m-d');
	$date_condition ="  AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
}
if($mode=='Y')
{
	$date=date('Y-m-d', strtotime("yesterday"));
	$date_condition ="  AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
}
if($mode=='MTD')
{
	$date_condition=" AND YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE()) ";
}
if($mode=='YC')
{
	$from_date=date('Y-m-d',strtotime($_REQUEST['from_date']));
	$to_date=date('Y-m-d',strtotime($_REQUEST['to_date']));
	$date_condition=" AND DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
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

		//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
		$date_condition=" AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
					AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
}
?>
<style type="text/css">
.TDHEAD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	COLOR: #FFFFFF;
	BACKGROUND-COLOR: #A92A61;/*#92C006;*/
}

.TDHEAD_SUB{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	BACKGROUND-COLOR:#c0c8b0;
}

.border{
	BORDER: #A92A61/*#80A537*/ 1px solid;
}
TD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
}

</style>
<?php 
$date_array=array();
echo '
<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 350px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="3" align="center"><strong>Employee Name: '.$emp_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Employee Code: '. $emp_code.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB" style=\"position:fixed;\"> 
         <td align="center">Sl</td>
		<th align="left" style="padding-left:20px;">Store Name</td>
		<th align="left" style="padding-left:20px;">Locate</td>
    </tr> ';
		 $sqltrans="SELECT DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') AS date, trans_id, latt, longi, SUBSTRING(date,1,10) as locate_date, SUBSTRING(date,12) as locate_time  FROM location WHERE trans_id LIKE 'SU%' AND emp_code='".$emp_code."'".$date_condition." ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %h:%i:%s') DESC ";
		 $restrans=mysql_query($sqltrans) or die(mysql_error()." Error in select transaction id for employee wise activity: ".$sqltrans);
         $cnt=$GLOBALS[start]+1;
		$rowval=""; 
	while($rowtrans=mysql_fetch_array($restrans))
	{
		$get_date = $rowtrans['date'];
		$trans_id = $rowtrans['trans_id'];
		$latt = $rowtrans['latt'];
		$longi = $rowtrans['longi'];
		$locate_date = $rowtrans['locate_date'];
		$locate_time = $rowtrans['locate_time'];
		
		$sql_business_name = "SELECT SO.value FROM survey_output SO, survey_input SI WHERE SO.survey_id = '".$trans_id."' AND SO.row_id = SI.row_id AND SI.display_name LIKE '%".$store_name."%'";
		$res_business_name = mysql_query($sql_business_name);
		$row_business_name = mysql_fetch_array($res_business_name);
		$business_name = $row_business_name['value'];
		
		if(!in_array($get_date,$date_array)){
			array_push($date_array,$get_date);
			echo "<tr><td colspan='3' align='center' style='font-weight:bold;'>$get_date</td></tr>";
		}
		
		echo "<tr> 
					<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"show_employee_survey_details.php?survey_id=$trans_id\" target=\"_BLANK\" style=\"color:#930;\" >".$business_name."</a></td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
					<a href=\"#\" style=\"color:blue;font-weight:bold;\" target=\"_target\" onclick=\"window.open('http://acedns.in/acednsproduct/misreport/misreport_survey_locate.php?get_latt=$latt&get_longi=$longi&emp_name=$emp_name&locate_date=$locate_date&locate_time=$locate_time','mywin','width=500,height=500');\">Locate</a></td>
				</tr>";
		
	}
echo '</table>';	
mysql_close($link);			
?>