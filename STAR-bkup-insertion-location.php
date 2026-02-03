<?php
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_STAR");
		$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.");
		mysqli_select_db(DBREMOTE,$link) or die("could not connect the database for invalid nick name");


$sqllocationbkup="SELECT * FROM location_bkup";
$rslocationbkup=mysqli_query($link,$sqllocationbkup);
$countlocationbkup=mysqli_num_rows($rslocationbkup);
while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
{
	$trans_id_bkup=$rowlocationbkup['trans_id'];
	$emp_code_bkup=$rowlocationbkup['emp_code'];
	$date_bkup=$rowlocationbkup['date'];
	$updatetime_bkup=$rowlocationbkup['updatetime'];
	$latt_bkup=$rowlocationbkup['latt'];
	$longi_bkup=$rowlocationbkup['longi'];
	$transferred_bkup=$rowlocationbkup['transferred'];
	
	$sqllocationchk="SELECT * from location WHERE trans_id='".$trans_id_bkup."'";
	$rslocationchk=mysqli_query($link,$sqllocationchk);
	$countlocationchk=mysqli_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into location ";
		$sql .= " SET emp_code='".$emp_code_bkup."'";
		$sql .= " , trans_id='".$trans_id_bkup."'";
		$sql .= " , date='".$date_bkup."'";
		$sql .= " , updatetime='".$updatetime_bkup."'";
		$sql .= " , latt='".$latt_bkup."'";
		$sql .= " , longi='".$longi_bkup."'";
		$sql .= " , transferred='".$transferred_bkup."'";
		mysqli_query($link,$sql);
	}
}
	
?>