<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_STORE");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
$sqlsurveydetails="SELECT survey_input_value FROM survey_input_backup";
$rssurveydetails=mysqli_query($link,$sqlsurveydetails);
$rowsurveydetails=mysqli_fetch_assoc($rssurveydetails);
$survey_input_value=$rowsurveydetails['survey_input_value'];

$survey_input_value_array=unserialize($survey_input_value);
	$j=0;
	$i=1;
	$k=1;
	$m=2;
	$n=3;
	$p=4;
	foreach( $survey_input_value_array as $key => $value ) :
	   
	   //$survey_input_value_array[1];
	   
			if($i==1)
			{
				$k=$i;
				$row_count ='RA00'.$i;
				//$row_id = $row_count;
			}
		  if($i%5==0)
		   {
			   $k=$k+5;
			   $j=$j+5;
			   $m=$m+5;
			   $p=$p+5;
			   $n=$n+5;
		   }
		   if($survey_input_value_array[$k]=='layer')
		   {
			   $layout_name=$survey_input_value_array[$j];
		   }
		   $display_name=$survey_input_value_array[$j];
		   
		   if($survey_input_value_array[$k]=='layer')
		   {
		   	$type=$survey_input_value_array[$k];
		   }
		   elseif($survey_input_value_array[$k]=='text')
		   {
			   $type='';
		   }
		   elseif($survey_input_value_array[$k]=='boolean')
		   {
			   $type='Y;N';
		   }
		   elseif($survey_input_value_array[$k]=='listview')
		   {
			   $type=$survey_input_value_array[$p];
		   }
		   $mandatory=$survey_input_value_array[$n];
		   
	   		
		   if($i%5==0)
		   {
				//$row_count=$row_count+1;
			    //$row_id = $row_count;
				$row_count++;
				if($mandatory=='mandatory')
				{
					$mandatory='Y';
				}
				else
				{
					$mandatory='N';
				}
			  	$sql_insert_survey_input = "INSERT INTO survey_input_backup_one SET row_id='".$row_count."', action_id='', 
											layout_name='". $layout_name."', 
											display_name='".$display_name."', 
											type='".$type."', 
											display_table_name='', 
											mandatory='".$mandatory."', 
											`action`='', 
											download_time=current_timestamp";
				$res_insert_survey_input = mysqli_query($link,$sql_insert_survey_input);
 
		   }
	   $i++;
	endforeach;
	
	echo "Data Inserted";
	exit();
?>
