<?php
/*if($remote_db_access == 'yes'){
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DB) or die("Database Connection Error.".mysql_error());
}
else{*/
	
 $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
//}
//mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
//mysql_select_db(DB,$link) or die("something went wrong.");


