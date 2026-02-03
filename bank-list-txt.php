<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM bank_master ORDER BY bank_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowbank = mysqli_fetch_assoc($result))
		{
				/*$contents.="<data>";
				$contents .='<bank_id><![CDATA['.mb_convert_encoding($rowbank['bank_id'], 'UTF-8', 'UTF-8').']]></bank_id>
							<bank_name><![CDATA['.mb_convert_encoding($rowbank['bank_name'], 'UTF-8', 'UTF-8').']]></bank_name>';
				$contents.="</data>";*/
				//echo $cnt++;
				$contents  = (($rowbank['bank_id']!='')?$rowbank['bank_id']: ' ')."^";
				$contents  .= (($rowbank['bank_name']!='')?$rowbank['bank_name']: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=bank_master.txt");
	print "$datacontents"; 		
?>
