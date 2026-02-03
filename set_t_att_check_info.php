<?php
	// define("SERVER","localhost");
	// define("USER","acedns_dnsprod");
	// define("PASSWORD","dnsprod1234#");
	// //require("include/config-setup.php");
	// define("DB","acedns_STAR");
	// $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	//mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	require_once("sfa_connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$localDB = new sfa_connection();
$link = $localDB->conn;
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$emp_code_array=array();
	$month_array=array();
	
	
	$sqlemplist="SELECT EM.emp_name,EM.dns_emp_code,SUBSTRING(date,12,8) as time,SUBSTRING(date,1,10) as date,LO.trans_id
				FROM location LO,employee_master EM WHERE EM.emp_code=LO.emp_code AND
				 substring(LO.trans_id,1,2) IN('AE','CH') 
				AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')>='2023-12-01'
				ORDER BY LO.emp_code ASC,
				DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') ASC,trans_id ASC";
	$rsemplist=mysqli_query($link,$sqlemplist);
	while($rowemplist=mysqli_fetch_assoc($rsemplist))
	{
		$time=$rowemplist['time'];
		$date=$rowemplist['date'];
		$dns_emp_code=$rowemplist['dns_emp_code'];
		$emp_name=$rowemplist['emp_name'];
		$trans_id=$rowemplist['trans_id'];
		$sqlchkattinfo="SELECT Emp_Id FROM  t_att_checkout_info WHERE Emp_Id='".$dns_emp_code."' AND SUBSTRING(Entry_Date,1,10)='".$date."'";
						$reschkattinfo = mysqli_query($link,$sqlchkattinfo); 
						$countchkattinfo=mysqli_num_rows($reschkattinfo);
		if($countchkattinfo ==0)
		{
			echo $sqlinsertattinfo="INSERT INTO t_att_checkout_info SET Emp_Id='".$dns_emp_code."',
								Emp_Name='".$emp_name."',Entry_Date='".$date."',CheckIN='".$time."'";
			$rsinsertattinfo=mysqli_query($link,$sqlinsertattinfo);					
		}	
	  else
	  {
		  if(substr($trans_id,0,2)=='CH')
		  {
	  echo $sqlupdateloc="UPDATE t_att_checkout_info set CheckOUT='".$time."' WHERE Emp_Id='".$dns_emp_code."'
								AND Entry_Date='".$date."' AND CheckOUT IS NULL";
	  mysqli_query($link,$sqlupdateloc);
	  	}
	  }
 	}
	echo "SUCCESS";
	mysqli_close($link);
?>