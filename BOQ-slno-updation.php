<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
define("DB","acedns_NIMBUS");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlBOQ="SELECT mi_type FROM BOQ_master GROUP BY mi_type ORDER BY mi_type ASC";
$rsBOQ=mysqli_query($link,$sqlBOQ);
$count=mysqli_num_rows($rsBOQ);
if($count>0){
		while($rowBOQ = mysqli_fetch_assoc($rsBOQ))
		{
			$mi_type_db=$rowBOQ['mi_type'];
			$sqlBOQdetails="SELECT BOQ_id FROM BOQ_master WHERE mi_type='".$mi_type_db."'";
			$rsBOQdetails=mysqli_query($link,$sqlBOQdetails);
			$countdetails=1;
			while($rowBOQdetails = mysqli_fetch_assoc($rsBOQdetails))
			{
				$BOQ_id=$rowBOQdetails['BOQ_id'];
				$sqlupdate="UPDATE BOQ_master SET sl_no='".$countdetails."' WHERE BOQ_id='".$BOQ_id."'";
				mysqli_query($link,$sqlupdate);
				$countdetails++;
			}
		}
    }
	mysqli_close($link);	
?>
