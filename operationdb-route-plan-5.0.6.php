<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
include '/home/acedns/public_html/acednsproduct/php-calendar/classes/calendar.php';

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);

$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);
/*$body="<?xml version='1.0' encoding='UTF-8'?>
<root><route_plan><route_plan_details>
<route_plan_trans_id><![CDATA[RP10000316520131112132038]]></route_plan_trans_id><emp_code><![CDATA[100003165]]></emp_code>
<rds_code><![CDATA[4932]]></rds_code><route_code><![CDATA[FDR]]></route_code>
<visit_date><![CDATA[17-11-2013]]></visit_date><create_date><![CDATA[2013-11-12 13:30:13]]></create_date><type><![CDATA[add]]></type></route_plan_details>
</route_plan></root>";*/
/*
$body="<?xml version='1.0' encoding='UTF-8'?><root><route_plan><route_plan_details><route_plan_trans_id><![CDATA[RPE002720140412115826]]></route_plan_trans_id><emp_code><![CDATA[E0027]]></emp_code><route_code><![CDATA[RT/19]]></route_code><visit_date><![CDATA[12-04-2014]]></visit_date><create_date><![CDATA[2014-04-12 11:58:35]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE002720140412120516]]></route_plan_trans_id><emp_code><![CDATA[E0027]]></emp_code><route_code><![CDATA[RT/19]]></route_code><visit_date><![CDATA[13-04-2014]]></visit_date><create_date><![CDATA[2014-04-12 12:05:29]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE002720140412120923]]></route_plan_trans_id><emp_code><![CDATA[E0027]]></emp_code><route_code><![CDATA[RT/19]]></route_code><visit_date><![CDATA[14-04-2014]]></visit_date><create_date><![CDATA[2014-04-12 12:09:48]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE002720140412121507]]></route_plan_trans_id><emp_code><![CDATA[E0027]]></emp_code><route_code><![CDATA[RT/19]]></route_code><visit_date><![CDATA[30-04-2014]]></visit_date><create_date><![CDATA[2014-04-12 12:15:15]]></create_date></route_plan_details></route_plan></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><route_plan><route_plan_details><route_plan_trans_id><![CDATA[RPE023320140812115312]]></route_plan_trans_id><emp_code><![CDATA[E0233]]></emp_code><route_code><![CDATA[NRT/E023320140812115]]></route_code><route_name><![CDATA[null]]></route_name><visit_date><![CDATA[12-08-2014]]></visit_date><create_date><![CDATA[2014-08-12 11:53:24]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE023320140812114610]]></route_plan_trans_id><emp_code><![CDATA[E0233]]></emp_code><route_code><![CDATA[RT/00665]]></route_code><route_name><![CDATA[KOL-Key Accounts -1(F4)]]></route_name><visit_date><![CDATA[12-08-2014]]></visit_date><create_date><![CDATA[2014-08-12 11:46:19]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE023320140812114610]]></route_plan_trans_id><emp_code><![CDATA[E0233]]></emp_code><route_code><![CDATA[RT/00666]]></route_code><route_name><![CDATA[KOL-Key Accounts -2(F4)]]></route_name><visit_date><![CDATA[12-08-2014]]></visit_date><create_date><![CDATA[2014-08-12 11:46:19]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE023320140812114610]]></route_plan_trans_id><emp_code><![CDATA[E0233]]></emp_code><route_code><![CDATA[RT/00667]]></route_code><route_name><![CDATA[KOL-Key Accounts -3(F4)]]></route_name><visit_date><![CDATA[12-08-2014]]></visit_date><create_date><![CDATA[2014-08-12 11:46:19]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE023320140812183844]]></route_plan_trans_id><emp_code><![CDATA[E0233]]></emp_code><route_code><![CDATA[NRT/E023320140812183844]]></route_code><route_name><![CDATA[nnn route]]></route_name><visit_date><![CDATA[12-08-2014]]></visit_date><create_date><![CDATA[2014-08-12 18:39:04]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE023320140812184357]]></route_plan_trans_id><emp_code><![CDATA[E0233]]></emp_code><route_code><![CDATA[NRT/E023320140812184357]]></route_code><route_name><![CDATA[gfgfdd]]></route_name><visit_date><![CDATA[12-08-2014]]></visit_date><create_date><![CDATA[2014-08-12 18:44:09]]></create_date></route_plan_details></route_plan></root>";
$body="<?xml version='1.0' encoding='UTF-8'?><root><route_plan><route_plan_details><route_plan_trans_id><![CDATA[RPE000120150226072856]]></route_plan_trans_id><emp_code><![CDATA[E0001]]></emp_code><route_code><![CDATA[RTBSC3087N00047]]></route_code><route_name><![CDATA[NETAJI NAGAR]]></route_name><visit_date><![CDATA[27-02-2015]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2015-02-26 07:29:07]]></create_date></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE000120150226072856]]></route_plan_trans_id><emp_code><![CDATA[E0001]]></emp_code><route_code><![CDATA[RTBSC6559S00032]]></route_code><route_name><![CDATA[SANTOSHPUR 2]]></route_name><visit_date><![CDATA[27-02-2015]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2015-02-26 07:29:07]]></create_date></route_plan_details></route_plan></root>";*/

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$month = isset($_GET['m']) ? $_GET['m'] : NULL;
$year  = isset($_GET['y']) ? $_GET['y'] : NULL;
$route_plan_trans_id = "*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*ROUTE_PLAN_TRANS_ID";
$route_plan_emp_code = "*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*EMP_CODE";
$route_plan_route_code ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*ROUTE_CODE";
$route_plan_route_name ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*ROUTE_NAME";
$route_plan_visit_date ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*VISIT_DATE";
$route_plan_remarks ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*REMARKS";
$route_plan_create_date ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*CREATE_DATE";

$route_plan_array = array();
$route_plan_trans_id_array=array();
$route_plan_emp_code_array=array();
$route_plan_visit_date_array=array();
$route_plan_create_date_array=array();
$route_plan_visit_date_event_array=array();
$route_plan_del_emp_code_array=array();
$route_plan_del_visit_date_array=array();
$route_plan_route_code_array=array();
$counter = 0;

class xml_route_plan{
	var $route_plan_trans_id,$route_plan_emp_code,$route_plan_route_code,$route_plan_route_name,$route_plan_visit_date,$route_plan_remarks,$route_plan_create_date;
}

function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}

function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}

function contents($parser, $data){
    global $current_tag,$route_plan_trans_id,$route_plan_emp_code,$route_plan_route_code,$route_plan_route_name,$route_plan_visit_date,$route_plan_remarks,
			$route_plan_create_date,$route_plan_array,$counter;
	if(substr($current_tag,0,16)=='*ROOT*ROUTE_PLAN')
	{
		/*echo $current_tag.'<br />';
		echo $data.'<br />';*/
		switch($current_tag){
			case $route_plan_trans_id:
				$route_plan_array[$counter] = new xml_route_plan();
				$route_plan_array[$counter]->route_plan_trans_id = $data;
				break;
			case $route_plan_emp_code:
				$route_plan_array[$counter]->route_plan_emp_code = $data;
				break;
			case $route_plan_route_code:
				$route_plan_array[$counter]->route_plan_route_code = $data;
				break;
			case $route_plan_route_name:
				$route_plan_array[$counter]->route_plan_route_name = $data;
				break;	
			case $route_plan_visit_date:
				$route_plan_array[$counter]->route_plan_visit_date = $data;
				break;
			case $route_plan_remarks:
				$route_plan_array[$counter]->route_plan_remarks = $data;
				break;	
			case $route_plan_create_date:
				$route_plan_array[$counter]->route_plan_create_date = $data;
				$counter++;
				break;	
		}
	}
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
//print_r($attendance_array);
//print_r($audit_array);
//print_r($stock_audit_array);
//echo count($route_plan_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -------------------------------------------------------START QUERY FOR ROUTEPLAN-----------------------------------------------------------------------*/
if(count($route_plan_array)>0)
{
	//$count=1;
	$blank_val='  ';
	for($x=0;$x<count($route_plan_array);$x++){
		$route_plan_trans_id=$route_plan_array[$x]->route_plan_trans_id;
		$route_plan_emp_code=$route_plan_array[$x]->route_plan_emp_code;
		$route_plan_route_code=$route_plan_array[$x]->route_plan_route_code;
		$route_plan_route_name=$route_plan_array[$x]->route_plan_route_name;
		$route_plan_visit_date=$route_plan_array[$x]->route_plan_visit_date;
		$route_plan_remarks=$route_plan_array[$x]->route_plan_remarks;
		$route_plan_create_date=$route_plan_array[$x]->route_plan_create_date;
		$route_plan_visit_date_database=date('Y-m-d',strtotime($route_plan_visit_date));
		
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$update_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
				
		//For checking that trans id exist or not
		$sqlselectrouteplan="SELECT route_plan_trans_id,route_code FROM route_plan 
							  WHERE emp_code ='".$route_plan_emp_code."' AND visit_date	='".$route_plan_visit_date_database."'";
		$resselectrouteplan = mysqli_query($link,$sqlselectrouteplan) or die(mysqli_error()." Error in chk trans id for route plan: ".$sqlselectrouteplan); 
		$countselectrouteplan=mysqli_num_rows($resselectrouteplan);
					
		if($countselectrouteplan<1)
		{
			$sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$route_plan_trans_id."',
								  emp_code 			='".$route_plan_emp_code."',
								  route_code 		='".$route_plan_route_code."',
								  visit_date 		='".$route_plan_visit_date_database."',
								  remarks			='".$route_plan_remarks."',
								  create_date 		='".$route_plan_create_date."',
								  update_date		='".$update_date."'";
			if(mysqli_query($link,$sqlinsertrouteplan))
			{
				$flag=5;
				if(!in_array($route_plan_emp_code,$route_plan_del_emp_code_array) && !in_array($route_plan_visit_date_database,$route_plan_del_visit_date_array))
				{
					array_push($route_plan_del_emp_code_array,$route_plan_emp_code);
					array_push($route_plan_del_visit_date_array,$route_plan_visit_date_database);
				}
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		}
		else
		{
			//For deleting the previous values of same visit date
			if(!in_array($route_plan_emp_code,$route_plan_del_emp_code_array) && !in_array($route_plan_visit_date_database,$route_plan_del_visit_date_array))
			{
				$sqldeleterouteplan="DELETE FROM route_plan WHERE  emp_code ='".$route_plan_emp_code."' AND visit_date	='".$route_plan_visit_date_database."'";
				if(mysqli_query($link,$sqldeleterouteplan)){
					array_push($route_plan_del_emp_code_array,$route_plan_emp_code);
					array_push($route_plan_del_visit_date_array,$route_plan_visit_date_database);
				}
			}
			 $sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$route_plan_trans_id."',
								  emp_code 			='".$route_plan_emp_code."',
								  route_code 		='".$route_plan_route_code."',
								  visit_date 		='".$route_plan_visit_date_database."',
								  create_date 		='".$route_plan_create_date."',
								  remarks			='".$route_plan_remarks."',
								  update_date		='".$update_date."'";
			if(mysqli_query($link,$sqlinsertrouteplan))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		}
		if($flag==5){
			
			$sqlselectrouteplanedit="SELECT route_plan_trans_id,current_route_code FROM route_plan_log 
									WHERE emp_code ='".$route_plan_emp_code."' AND current_route_code='".$route_plan_route_code."' AND
									visit_date	='".$route_plan_visit_date_database."'";
			$resselectrouteplanedit = mysqli_query($link,$sqlselectrouteplanedit) or die(mysqli_error()." Error in chk  route plan log: ".$sqlselectrouteplan); 
			$countselectrouteplanedit=mysqli_num_rows($resselectrouteplanedit);
	
			if($countselectrouteplanedit<1)
			{
				$sqlinsertrouteplanelog="INSERT INTO route_plan_log SET route_plan_trans_id ='".$route_plan_trans_id."',
									  emp_code 			='".$route_plan_emp_code."',
									  prev_route_code 	='',
									  current_route_code ='".$route_plan_route_code."',
									  visit_date 		='".$route_plan_visit_date_database."',
									  remarks			='".$route_plan_remarks."',
									  created_by 		='USER',
									  create_date		='".$route_plan_create_date."'";					  
				if(mysqli_query($link,$sqlinsertrouteplanelog))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			}
		}
		if(!in_array($route_plan_trans_id,$route_plan_trans_id_array))
		{
			array_push($route_plan_trans_id_array,$route_plan_trans_id);
			array_push($route_plan_emp_code_array,$route_plan_emp_code);
			array_push($route_plan_visit_date_array,$route_plan_visit_date);
			array_push($route_plan_create_date_array,$route_plan_create_date);
		}
		array_push($route_plan_route_code_array,$route_plan_route_code);
   }// End of for loop
   //print_r($route_plan_visit_date_event_array);	
	if($flag==5){
		mysqli_query($link,"COMMIT");

		for($y=0;$y<count($route_plan_trans_id_array);$y++){  // strat of for loop of sending mail
		
		$calendar = Calendar::factory($month, $year);
		$route_plan_visit_date_event_month=substr($route_plan_visit_date_array[$y],3,2);
		$route_plan_visit_date_event_year=substr($route_plan_visit_date_array[$y],6,4);
		
		$today = date("Y-m-d");
		$today_time = strtotime($today);
		$sqlrouteplan="SELECT route_code,visit_date,route_plan_trans_id,create_date  FROM route_plan WHERE visit_date LIKE '%".$route_plan_visit_date_event_year.'-'.$route_plan_visit_date_event_month."%' AND emp_code='".$route_plan_emp_code_array[$y]."'";
		$rsrouteplan=mysqli_query($link,$sqlrouteplan);
		while($rowrouteplan=mysqli_fetch_assoc($rsrouteplan))
		{
			$route_plan_visit_date_existing=$rowrouteplan['visit_date'];
			$route_plan_visit_date_existing_time=strtotime($route_plan_visit_date_existing);
			$route_plan_visit_date_existing=date('d-m-Y',strtotime($route_plan_visit_date_existing));
			$route_plan_visit_date_event_existing=substr($route_plan_visit_date_existing,0,2);
			$route_code_existing=$rowrouteplan['route_code'];
			$route_plan_create_date_existing=$rowrouteplan['create_date'];
			
			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code_existing."'";
			$rsroutename=mysqli_query($link,$sqlroutename);
			$rowroutename=mysqli_fetch_assoc($rsroutename);
			$route_name_existing=$rowroutename['route_name'];
			
			if(route_plan_flow=='yes')
			{
				$sqlrdsname="SELECT DISTINCT RM.rds_name from customer_master CM,customer_route_emp_relation CRER,rds_master RM 
							WHERE CRER.customer_code=CM.customer_code AND CRER.route_code IN ('".$route_code_existing."') AND CRER.rds_tag=RM.rds_code ORDER BY RM.rds_name ASC ";
				$rsrdsname=mysqli_query($link,$sqlrdsname);
				$rowrdsname=mysqli_fetch_assoc($rsrdsname);
				$rds_name_existing=$rowrdsname['rds_name'];
			}
			else
			{
				$rds_name_existing='';
			}
		
			if($route_plan_visit_date_existing_time>$today_time)
			{
				$font_color='#0000FF';
			}
			elseif($route_plan_visit_date_existing_time<$today_time)
			{
				$font_color='#990000';
			}
			else
			{
				$font_color='#00CC00';
			}
			$route_code_existing=str_replace('/','-',$route_code_existing);
			${event.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_existing.$route_code_existing} = $calendar->event()
			->condition('timestamp', strtotime(date('F',strtotime($route_plan_visit_date_existing))." $route_plan_visit_date_event_existing, ".date('Y',strtotime($route_plan_visit_date_existing))))
			->title('Hello All')
			->output('<font color="'.$font_color.'">'.$rds_name_existing."<br /><br />".$route_name_existing.'</font>');
			//echo '<font color="'.$font_color.'">'.$route_name_existing.'</font>';
			
			$calendar->attach(${event.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_existing.$route_code_existing});	
			//array_push($route_plan_visit_date_event_array,$route_plan_visit_date_event_existing);
		}
		
		/*for($m=0;$m<count($route_plan_visit_date_event_array);$m++){
			$route_code_existing=str_replace('/','-',$route_plan_route_code_array[$m]);
			echo 'event'.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_array[$m].$route_code_existing;
			$calendar->attach(${event.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_array[$m].$route_code_existing});	
		}*/
		$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$route_plan_emp_code_array[$y]."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];
		
		if(branch_vertical_operation_wise_email=='yes'){
				$operation_type='Routeplan';
				$route_plan_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
		}
		else
		{
			$route_plan_email=ROUTEPLANMAILRECIPENTS;
		}
		
		//$route_plan_email=fetch_corresponding_emails($operation_type,$admin_Email_ID,$rds_Email_ID,$emp_Email_ID,$hierarchical_Email_ID);			
		$routeplanmailsubj="Route Plan of ".$emp_name." for ".date('F,Y',strtotime($route_plan_visit_date_array[$y])).' Posted On '.date('d-m-Y H:i:s').' hrs';
		
		$routeplanmailbody = "<html><head><title>Route Plan</title></head>
					<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
					.$emp_name. "</b><br /><br />
<table style='width:100%; border-collapse:collapse;'>
	<thead>
		<tr style='padding-bottom:20px;'>
			<th colspan='5' style='text-align:center; font-size:1.5em;'>".$calendar->month().$calendar->year."</th>
		</tr>
	<tr style='text-align:left;'>";
			 foreach ($calendar->days() as $day): 
				$routeplanmailbody.="<th>".$day."</th>";
			endforeach ;
		$html_code.="</tr>
	</thead>
	<tbody>";
	foreach ($calendar->weeks() as $week): 
			$routeplanmailbody.="<tr>";
				foreach ($week as $day):
					
					list($number, $current, $data) = $day;
					
					$classes = array();
					$output  = '';
					
					if (is_array($data))
					{
						$classes = $data['classes'];
						$title   = $data['title'];
						$output  = empty($data['output']) ? '' : '<ul style="margin:0; padding:0 4px; list-style:none;"><li style="margin:0; padding:5px 0; line-height:1em;">'.implode('</li><li style="margin:0; padding:5px 0; line-height:1em;">', $data['output']).'</li></ul>';
					}
					$routeplanmailbody.="
					<td style='width:14%; height:100px; vertical-align:top; border:1px solid #CCC;'>
						<span style='display:block; padding:4px; line-height:12px; background:#EEE;' title=".implode(' / ', $title).">".$number."</span>
						<div style=''>
							".$output."
						</div>
					</td>";
				endforeach;
				$routeplanmailbody.="
				</tr>";
		 endforeach;
		 $routeplanmailbody.="
	</tbody>
</table><br /><br />Powered By aceDNS<br /></body></html>";
//echo $routeplanmailbody;
		/*$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		//echo 	$routeplanmailbody;		
		if(mail(ROUTEPLANMAILRECIPENTS, $routeplanmailsubj, $routeplanmailbody, $headers,'-facedns@coral.in'))
		{
			$flag=6;
		}
		else
		{
			//mysqli_query($link,"ROLLBACK");
			$flag=0;
			//return;
		}*/
	  }
	  $headers  = "MIME-Version: 1.0\r\n";
	  $headers .= "Content-type: text/html; charset=UTF-8\n";
	  $headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		//echo 	$routeplanmailbody;		
		if(mail($route_plan_email, $routeplanmailsubj, $routeplanmailbody, $headers,$spam_filter))
		{
			$flag=6;
		}
		else
		{
			//mysqli_query($link,"ROLLBACK");
			$flag=0;
			//return;
		}
    }
}
 /* --------------------END QUERY FOR ROUTEPLAN------------------------------------------------------------------------------------------------------------*/
if($flag==6)
{
	if($countdatarefresh >0)
	 {
		 echo 2;
	 }
	 else
	 {
	 	echo 1;
	 }
}
else
{
	echo 0;
}

?>