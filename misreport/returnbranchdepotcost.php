<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$plant_name=$_REQUEST['plant_name'];
	if(strtolower($plant_name)=='haldia')
	{
		$depot_condition=" AND dns_branch_code <> 'BC35'";	
	}
	if(strtolower($plant_name)=='krishnapatnam')
	{
		$depot_condition=" AND dns_branch_code <> 'BC36'";	
	}
	$content='<select name="branch_code" id="branch_code" >';
	$content.='<option value="">SELECT</option>';
    $sqlbranch="SELECT DISTINCT branch_name,branch_code FROM branch_master WHERE plant_name='".$plant_name."' ".$depot_condition." ORDER BY branch_name ASC";
	$rsbranch=mysqli_query($link,$sqlbranch);
	while($rowbranch=mysqli_fetch_assoc($rsbranch))
	{		
		$content.="<option value='".$rowbranch['branch_code']."'>".$rowbranch['branch_name']."</option>";
	}
	$content.='</select>';

	echo $content;
	mysqli_close($link);
?>