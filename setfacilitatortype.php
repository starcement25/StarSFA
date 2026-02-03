<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_DURO");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

	/*$sqlcustomer="SELECT f_code FROM facilitator_master WHERE f_type IS NULL";
	$rscustomer=mysqli_query($link,$sqlcustomer);
	while($rowcustomer=mysqli_fetch_assoc($rscustomer))
	{
		$f_code=$rowcustomer['f_code'];
				$sqlchkexists="SELECT value  FROM survey_output WHERE row_id='RA037' AND survey_id=(SELECT survey_id 
					FROM survey_output WHERE row_id='RA036' AND value LIKE '%".$f_code."%')";
				$rschkexists=mysqli_query($link,$sqlchkexists);
				$emp_exists=mysqli_num_rows($rschkexists);
				if($emp_exists > 0)
				{
					$rowexists=mysqli_fetch_assoc($rschkexists);
				echo $sqlinsert="update facilitator_master SET f_type='".$rowexists['value']."',download_time=CURRENT_TIMESTAMP() WHERE f_code='".$f_code."'";
				mysqli_query($link,$sqlinsert);
				}
	}*/
	
	$sqlcustomer="SELECT f_code FROM facilitator_master WHERE `last_visit_date`='0000-00-00 00:00:00' ORDER BY facilitator_name ASC";
	$rscustomer=mysqli_query($link,$sqlcustomer);
	while($rowcustomer=mysqli_fetch_assoc($rscustomer))
	{
		$f_code=$rowcustomer['f_code'];
				$sqllastvisitfaci="SELECT DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') AS last_visit_date_facilitator FROM 
						survey_output WHERE value like '%".$f_code."%' ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') 
						DESC LIMIT 0,1";				
		$rslastvisitfaci=mysqli_query($link,$sqllastvisitfaci);
		$rowlastvisitfaci=mysqli_fetch_assoc($rslastvisitfaci);
		$last_visit_date_facilitator=$rowlastvisitfaci['last_visit_date_facilitator'];	
				
				echo $sqlinsert="update facilitator_master SET 	last_visit_date='".$last_visit_date_facilitator."' WHERE f_code='".$f_code."'";
				mysqli_query($link,$sqlinsert);
	}
?>	