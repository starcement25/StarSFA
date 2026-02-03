<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100002157';

$sqlbranches="SELECT * FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1)
{
	$sqlquery="SELECT DISTINCT PGM.* FROM product_group_master PGM,state_product_group_master SPGM,employee_master EM,product_master PM WHERE 
				EM.branch_code=SPGM.branch_code AND SPGM.product_group_code=PGM.product_group_code AND PM.product_group_code=PGM.product_group_code
				AND PM.acedns='Y' AND PM.black_list='N'
				AND EM.emp_code='".$emp_code."' ORDER BY PGM.product_group_name ASC";
}
else
{
	$sqlquery="SELECT DISTINCT PGM.* FROM product_group_master PGM,product_master PM WHERE PM.product_group_code=PGM.product_group_code 
				AND PM.acedns='Y' AND PM.black_list='N' ORDER BY PGM.product_group_name ASC";

}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowproductgroup = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<product_group_code><![CDATA['.mb_convert_encoding($rowproductgroup['product_group_code'], 'UTF-8', 'UTF-8').']]></product_group_code>
						<product_group_name><![CDATA['.mb_convert_encoding($rowproductgroup['product_group_name'], 'UTF-8', 'UTF-8').']]></product_group_name>';
			$contents.="</data>";*/
			//echo $cnt++;
				
			$contents  = (($rowproductgroup['product_group_code']!='')?$rowproductgroup['product_group_code']: ' ')."^";
			$contents  .= (($rowproductgroup['product_group_name']!='')?$rowproductgroup['product_group_name']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/	
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=product_group_master.txt");
	print "$datacontents"; 		
?>
