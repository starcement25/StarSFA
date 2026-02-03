<?php

$msg_out = "";

$csv_mimetypes = array(

    'text/csv',

    'application/csv',

    'text/comma-separated-values',

    'application/excel',

	'application/vnd.ms-excel'

);

if(@$_POST["gensql"]=="Generate"){

	$my_table_name = $_POST["my_table_name"];

	$my_csv_file_name = $_FILES["my_csv_file"]["name"];

	$my_csv_file_tmp_name = $_FILES["my_csv_file"]["tmp_name"];

	$my_csv_file_type = $_FILES["my_csv_file"]["type"];

	

	if($my_table_name==""){

		$msg_out = "Please enter table name.";

	}else if(strpos($my_table_name, ' ') > 0) {

		$msg_out = "Please remove space from table name.";

	}else if(preg_match('/[A-Z]/',$my_table_name)){

		$msg_out = "Table name should be small letter.";

	}else if($my_csv_file_name==""){

		$msg_out = "Please choose a csv file.";

	}else if(!in_array($my_csv_file_type, $csv_mimetypes)){

		$msg_out = "Please select a csv file.";

	}else{

		generate_sql($my_csv_file_tmp_name,$my_table_name);

		$msg_out = "Successfully generated.";

	}

	





}



function generate_sql($file,$table){

	

if(($handle = fopen($file , "r")) !== FALSE) 

	{

		$data1 = fgetcsv($handle, 1000, ",");

		

	foreach($data1 as $aatass){

		$csv_colmn_arr[] = trim($aatass);

	}
	
	$no_of_columns = count($csv_colmn_arr);

	$clmstrn = implode("`,`",$csv_colmn_arr);

	

		$counts = 0;

	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 

	{

	if($counts!=0){

	foreach($data as $dataval){
		
		$dataval = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $dataval);
		$dataval =  preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $dataval);

		$csv_value_arr[] = trim($dataval);

	}
	
	//print_r($csv_value_arr);

	$no_of_value_columns = count($csv_value_arr);

	if($no_of_columns==$no_of_value_columns){

	$csv_value_arr_final[] = $csv_value_arr;

	}

	unset($csv_value_arr);

	}

		

	$counts++;

	}

	fclose($handle);

	}

	

$final_query='';	

foreach($csv_value_arr_final as $csv_value_arr_finalval){

	$vlustrn = implode("','",$csv_value_arr_finalval);

	$final_query .= "INSERT INTO ".$table." (`".$clmstrn."`) VALUES('".$vlustrn."');\n";

	}	





header('Content-type: application/sql');

header('Content-Disposition: attachment; filename='.$table.'.sql');

header('Pragma: no-cache');    

header('Expires: 0');

echo $final_query;

exit;	

}

?>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>CSV FILE TO SQL FILE CONVERSION</title>

<style>

.my_upload_table{

	width:60%;

	margin:10px auto;

}

.my_upload_table tr td{

	height:40px;

	padding-left:5px;

}

.my_upload_table tr td .my_table_name{

	height:25px;

}

.my_upload_table tr td .gensql{

	padding:3px;

	font-size:14px;

}

.msgtxt{

	width:60%;

	height:20px;

	margin:20px auto 10px auto;

	text-align:center;

}

</style>

</head>



<body>

<div class="msgtxt">

<?php

if($msg_out!=''){

	echo $msg_out;

}

?>

</div>

<form action="" method="POST" enctype="multipart/form-data">

<table class="my_upload_table" border="1" cellspacing="0" cellpadding="0">

  <tr>

    <td>Enter&nbsp;table&nbsp;name</td>

    <td><input type="text" class="my_table_name" name="my_table_name"></td>

  </tr>

  <tr>

    <td>Choose&nbsp;CSV&nbsp;file</td>

    <td> <input type="file" name="my_csv_file" ></td>

  </tr>

  <tr>

    <td align="center" colspan="2"><input type="submit" class="gensql" name="gensql" value="Generate"></td>

  </tr>

</table>

</form>

</body>

</html>