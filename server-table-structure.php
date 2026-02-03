<?php
function duplicateTables($sourceDB=NULL, $targetDB=NULL) {
	
    $link = mysqli_connect('localhost', 'acedns_dnsprod', 'dnsprod1234#') or die(mysqli_error()); // connect to database
	echo 'a';
	//$create_database=mysqli_query($link,"CREATE DATABASE acedns_".$targetDB."") or die(mysqli_error());
	echo 'b';
    $result = mysqli_query($link,'SHOW TABLES FROM acedns_'.$sourceDB) or die(mysqli_error());
	$insert_table_array=array('admin_master','app_version','bank_master','db_version','table_structure_master','transport_mode_category','transport_mode_sub_category');
    while($row = mysqli_fetch_row($result)) {
		
        //mysqli_query($link,'DROP TABLE IF EXISTS `' . $targetDB . '`.`' . $row[0] . '`') or die(mysqli_error());
        mysqli_query($link,'CREATE TABLE `' . $targetDB . '`.`' . $row[0] . '` LIKE `' . $sourceDB . '`.`' . $row[0] . '`') or die(mysqli_error());
        if(in_array($row[0],$insert_table_array)){
			mysqli_query($link,'INSERT INTO `' . $targetDB . '`.`' . $row[0] . '` SELECT * FROM `' . $sourceDB . '`.`' . $row[0] . '`') or die(mysqli_error());
		}
        mysqli_query($link,'OPTIMIZE TABLE `' . $targetDB . '`.`' . $row[0] . '`') or die(mysqli_error());
    }
    mysqli_free_result($result);
    mysqli_close($link);
} // end duplicateTables()
duplicateTables('NILESH', 'DEMO');
?>