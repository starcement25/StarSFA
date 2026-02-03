<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_STAR");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM survey_output_test";
$rslocationbkup=mysqli_query($link,$sqllocationbkup);
$countlocationbkup=mysqli_num_rows($rslocationbkup);
while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
{
	$survey_id_bkup=$rowlocationbkup['survey_id'];
	$row_id_bkup=$rowlocationbkup['row_id'];
	$action_id_bkup=$rowlocationbkup['action_id'];
	$value_bkup=$rowlocationbkup['value'];
	$type_bkup=$rowlocationbkup['type'];
	
	$sqllocationchk="SELECT * from survey_output WHERE survey_id='".$survey_id_bkup."' AND row_id='".$row_id_bkup."'";
	$rslocationchk=mysqli_query($link,$sqllocationchk);
	$countlocationchk=mysqli_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into survey_output ";
		$sql .= " SET survey_id='".$survey_id_bkup."'";
		$sql .= " , row_id='".$row_id_bkup."'";
		$sql .= " , action_id='".$action_id_bkup."'";
		$sql .= " , value='".$value_bkup."'";
		echo $sql .= " , type='".$type_bkup."'";
		mysqli_query($link,$sql);
	}
}
	
?>