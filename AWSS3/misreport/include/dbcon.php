<?php
if($remote_db_access == 'yes'){
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.".mysql_error());
}
else{
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
}
//mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
mysql_select_db(DB,$link) or die("something went wrong.");


