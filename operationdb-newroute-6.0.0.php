<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

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

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@coral.in';
}
$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_route><route_code><![CDATA[NRT/E000120160729145127]]></route_code><route_name><![CDATA[YEST C]]></route_name><distributor_code><![CDATA[C/0000005]]></distributor_code></new_route><new_route><route_code><![CDATA[NRT/E000120160729145726]]></route_code><route_name><![CDATA[Sports World]]></route_name><distributor_code><![CDATA[C/0000005]]></distributor_code></new_route></root>";*/

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_customer><location><emp_code><![CDATA[E0010]]></emp_code><trans_id><![CDATA[NE001020160425102210]]></trans_id><latt><![CDATA[22.720395]]></latt><longi><![CDATA[87.68254]]></longi><date><![CDATA[2016-04-25 10:22:10]]></date></location><new_customer_details><customer_code><![CDATA[NE001020160425102210]]></customer_code><customer_name><![CDATA[Netta Pati]]></customer_name><Phone_no><![CDATA[9999999999]]></Phone_no><pin_code><![CDATA[999999]]></pin_code><area><![CDATA[NRT/E001220160404092845]]></area><area_name><![CDATA[KHARAR]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[KHARAR]]></address></new_customer_details></new_customer></root>";*/

$new_route_code = "*ROOT*NEW_ROUTE*ROUTE_CODE";
$new_route_name = "*ROOT*NEW_ROUTE*ROUTE_NAME";
$distributor_code = "*ROOT*NEW_ROUTE*DISTRIBUTOR_CODE";

$new_route_array=array();
$counter = 0;
class xml_new_route{
	var $new_route_code,$new_route_name,$distributor_code;	
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
    global $current_tag,$counter,$new_route_code,$new_route_name,$distributor_code,$new_route_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,15)=='*ROOT*NEW_ROUTE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $new_route_code:
				$new_route_array[$counter] = new xml_new_route();
				$new_route_array[$counter]->new_route_code = $data;
				break;
			case $new_route_name:
				$new_route_array[$counter]->new_route_name = $data;
				break;
			case $distributor_code:
				$new_route_array[$counter]->distributor_code = $data;
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
//print_r($new_route_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------START QUERY FOR New route------------------------------------------------------------------------------------------------*/
if(count($new_route_array)>0)
{
	$refreshflag=0;
	for($x=0;$x<count($new_route_array);$x++){
		$new_route_code=$new_route_array[$x]->new_route_code;
		$new_route_name=$new_route_array[$x]->new_route_name;
		$new_route_name= preg_replace('/[\r\n]+/', '',$new_route_name);
		$distributor_code=$new_route_array[$x]->distributor_code;
		
			$sqlroute="select route_name from route_master WHERE route_name='".addslashes($new_route_name)."' AND emp_code='".$emp_code."'";
			$rsroute=mysqli_query($link,$sqlroute);
			$countroute=mysqli_num_rows($rsroute);
			if($countroute<1)
			{
				$sqlroute  = "insert into route_master ";
				$sqlroute .= " SET route_code='".$new_route_code."'";
				$sqlroute .= " ,route_name='".addslashes($new_route_name)."'";
				$sqlroute .= " ,emp_code='".$emp_code."'";
				$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlroute))
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
			$sqldistributorroute="select route_code from distributor_route_relation WHERE distributor_code='".$distributor_code."' AND route_code='".$new_route_code."' AND emp_code='".$emp_code."'";
			$rsdistributorroute=mysqli_query($link,$sqldistributorroute);
			$countdistributorroute=mysqli_num_rows($rsdistributorroute);
			if($countdistributorroute<1)
			{
				$sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$distributor_code."',
								route_code='".$new_route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertdistributorroute))
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

	}//End of FOR
}//END of IF
 /* --------------------END QUERY FOR New route--------------------------------------------------------------------------------------------------------*/
 echo $flag;
$countdatarefresh=returndatarefresh($emp_code);
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
if($flag==6)
{
 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-newroute-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
