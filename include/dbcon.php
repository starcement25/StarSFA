<?php
if($remote_db_access=='yes' && $mode!='SETUP')
	{
	/*	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.");
		mysqli_select_db(DBREMOTE,$link) or die("could not connect the database for invalid nick names");*/
		
			$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	}
	else
	{
		//echo "<pre>";echo DB; die;
	    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");

		/*$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");*/
	}


?>