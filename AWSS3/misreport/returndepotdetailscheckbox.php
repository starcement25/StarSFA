<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$plant_name=$_REQUEST['plant_name'];
	$plant_name_array=explode(',',$plant_name);
	$plant_name="'".implode("','", $plant_name_array)."'";

	if(in_array('Haldia',$plant_name_array))
	{
		$depot_condition=" AND dns_branch_code <> 'BC35'";	
	}
	if(in_array('Krishnapatnam',$plant_name_array))
	{
		$depot_condition=" AND dns_branch_code <> 'BC36'";	
	}
	 
 	$sqlbranch="SELECT DISTINCT branch_name,branch_code FROM branch_master WHERE 
					plant_name IN(".$plant_name.") ".$depot_condition."  AND acedns='Y' ORDER BY branch_name ASC";
	$rsbranch=mysql_query($sqlbranch);
	while($rowbranch=mysql_fetch_array($rsbranch))
	{	 $branch_code=$rowbranch['branch_code'];
		 $branch_name=$rowbranch['branch_name'];
		  $content.="<tr>";
          $content.="<td align='left'>";
          $content.="<input type='checkbox' name='branch_code[]' value='".$branch_code."' />".$branch_name."";
          $content.="</td>";
          $content.= "</tr>"; 
	}
	echo $content;
	mysql_close($link);
?>