<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_RUPA");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$emp_code_array=array();
	$month_array=array();
	
	function getReverseGeo($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo";
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
	$sqlemplist="SELECT EM.emp_code,LO.trans_id, LO.latt,LO.longi,LO.address 
				FROM employee_master EM,location LO WHERE 
				LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
				AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y')='14-07-2021' AND LO.address=''
				ORDER BY EM.emp_name ASC,
				DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') ASC";
	$rsemplist=mysqli_query($link,$sqlemplist);
	while($rowemplist=mysqli_fetch_assoc($rsemplist))
	{
	  $emp_code = $rowemplist['emp_code'];
	  $trans_id = $rowemplist['trans_id'];
	  $latt = $rowemplist['latt'];
	  $longi = $rowemplist['longi'];
	  $address=getReverseGeo($latt,$longi);
	  $sqlupdateloc="UPDATE location set address='".addslashes($address)."' WHERE trans_id='".$trans_id."'";
	  mysqli_query($link,$sqlupdateloc);
 	}
	echo "SUCCESS";
	mysqli_close($link);
?>