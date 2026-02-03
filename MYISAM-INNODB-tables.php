<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_KUNJT");
	define("DESTDB","acedns_KUNJ");	
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database  source for invalid nick name");

    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'acedns_KUNJT' 
        AND ENGINE = 'InnoDB'";
    $rs = mysqli_query($link,$sql);
	$tbl_array=array();
    while($row = mysqli_fetch_assoc($rs))
    {
        //$tbl = $row[0];
		array_push($tbl_array,$row[0]);
    }
	mysqli_select_db(DESTDB,$link) or die("could not connect the database destination for invalid nick name");
	
	foreach($tbl_array as $tbl)
	{
	  $sql = "ALTER TABLE `$tbl` ENGINE=InnoDB";
      mysqli_query($link,$sql);
	}
?>