<?php
		
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","103.241.144.155");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234");
	
	//$db_namearray=array('RUPA','PARLE','ABDOS');
	$db_namearray=array('EMAMI','UCLINDIA');
	foreach($db_namearray as $dbval)
	{
		$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		$linkremote=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error remote.");

		//define("DB","acedns_$dbval");	
		//define("DBREMOTE","acedns_$dbval");

		mysqli_select_db("acedns_$dbval",$link) or die("could not connect the database for invalid nick name");
		mysqli_select_db("acedns_$dbval",$linkremote) or die("could not connect the database for invalid nick name remote");

		/*if($dbval=='RUPA')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-17 23:59:59'";
		}
		if($dbval=='PARLE')
		{
			//echo $dbval;
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-20 23:59:59'";
		}
		if($dbval=='ABDOS')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-22 23:59:59'";
		}*/
		if($dbval=='EMAMI')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-19 23:59:59'";
		}
		if($dbval=='UCLINDIA')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-02-02 23:59:59'";
		}
		$sqllocationbkup="SELECT * FROM location ".$date_condition;
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
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
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
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
				mysqli_query($link,$sql,$link);
			}
		}
		mysqli_close($link);
		mysqli_close($linkremote);
	}
?>