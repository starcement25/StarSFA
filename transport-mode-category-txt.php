<?php
/*require("include/config.php");
require("include/dbcon.php");*/
define("SERVER","52.76.13.38");
define("USER","root");
define("PASSWORD","cmcl@123");
define("SOCKET","3306");
//require("include/config-setup.php");
	define("DB","acedns_STAR");
	
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

$sqlquery="SELECT * FROM transport_mode_category ORDER BY transport_mode_cat_name ASC";
$result = mysqli_query($link,$sqlquery,$link);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'2';
	if($count>0){
		while($rowptransportmodecat = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<transport_mode_cat_id><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_cat_id'], 'UTF-8', 'UTF-8').']]></transport_mode_cat_id>
						<transport_mode_cat_name><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_cat_name'], 'UTF-8', 'UTF-8').']]></transport_mode_cat_name>';ho
			$contents.="</data>";*/
			//echo $cnt++;
			$contents  = (($rowptransportmodecat['transport_mode_cat_id']!='')?$rowptransportmodecat['transport_mode_cat_id']: ' ')."^";
			$contents  .= (($rowptransportmodecat['transport_mode_cat_name']!='')?$rowptransportmodecat['transport_mode_cat_name']: ' ');
			$linecontents .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	//$contents .= "</recordset>";			
	//echo $datacontents;	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=transport_mode_category.txt");
	print "$datacontents"; 		
?>
