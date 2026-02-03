<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_UCLINDIA");	
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'acedns_UCLINDIA' 
        AND ENGINE = 'InnoDB'";

    $rs = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($rs))
    {
        $tbl = $row[0];
        $sql = "ALTER TABLE `$tbl` ENGINE=MyISAM";
        mysqli_query($link,$sql);
    }
?>