<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_MAGIK");
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
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc";
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
	/*$sqlemplist="SELECT EM.emp_code,LO.trans_id, LO.latt,LO.longi,LO.address 
				FROM employee_master EM,location LO WHERE 
				LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%m-%Y')='09-2019' AND LO.address!=''  
					ORDER BY EM.emp_name ASC,
				DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') ASC";*/
	$sqlemplist="SELECT emp_code,trans_id,latt,longi,address 
				FROM location  WHERE (trans_id LIKE 'O%' OR  trans_id LIKE 'NO%') AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%m-%Y')='09-2021' AND address=''   ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') ASC";			
	$rsemplist=mysqli_query($link,$sqlemplist);
	while($rowemplist=mysqli_fetch_assoc($rsemplist))
	{
	  $emp_code = $rowemplist['emp_code'];
	  $trans_id = $rowemplist['trans_id'];
	  echo $latt = $rowemplist['latt'];
	  echo $longi = $rowemplist['longi'];
	  $address=getReverseGeo($latt,$longi);
	  
	  echo $sqlupdateloc="UPDATE location set address='".$address."' WHERE trans_id='".$trans_id."'";
	 // exit();
	  mysqli_query($link,$sqlupdateloc);
 	}
	echo "SUCCESS";
	mysqli_close($link);
?>