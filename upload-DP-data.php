<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "dbusername";
$password = "dbpass";
$db_name = "DP";

$conn = mysqli_connect($servername, $username, $password,$db_name);

if (mysqli_connect_errno()){
die("Failed to connect to MySQL: " . mysqli_connect_error());
}
$table_name="DP_list";

/*---------------------------------CODE START FOR UPLOAD--------------------------------------------*/

function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
$submsg = "";
if(@$_POST["upload"]=="Upload"){
$csv_file_name = $_FILES["csv_file"]["name"];
$csv_file_type = $_FILES["csv_file"]["type"];
$csv_file_size = $_FILES["csv_file"]["size"];
$csv_file_tmp = $_FILES["csv_file"]["tmp_name"];

if($csv_file_name!=""){
	$error_array=array();
		// Delete previous file
		  unlink($csv_file_name);
		
	$upload_dir="";
	if(file_exists($_FILES['csv_file']['tmp_name']))
	{
		$file_name = $_FILES['csv_file']['name'];
		$tmp_name=$_FILES['csv_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		 move_uploaded_file($tmp_name,$upload_file);
	 }
	if(similar_file_exists("DP list.csv")!=false)
	{
		$filename=similar_file_exists("DP list.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
			$lines = file($filename);
			//print_r($lines);
			$sqldel="DELETE from $table_name";
			mysqli_query($conn,$sqldel);
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data=array();;
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							//$data[]=$value;
							array_push($data,$value);
							$value = "";
						}
						else 
						{
						$value .=$char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   //$data[]=$value;
					array_push($data,$value);
				  //print_r($data);
					$csv_row_count=$rec_count+1;
					$pincode = trim($data[0]);
					$area	=trim($data[1]);
					$DP_service		=trim($data[2]);
					$weight_0		=str_replace(',','',trim($data[3]));
					$weight_1	  =str_replace(',','',trim($data[4]));
					$weight_2	  =str_replace(',','',trim($data[5]));
					$weight_3	  =str_replace(',','',trim($data[6]));
					$weight_4	  =str_replace(',','',trim($data[7]));
					$weight_5	  =str_replace(',','',trim($data[8]));
					$weight_6	  =str_replace(',','',trim($data[9]));
					$weight_7	  =str_replace(',','',trim($data[10]));
					$weight_8	  =str_replace(',','',trim($data[11]));
					$weight_9	  =str_replace(',','',trim($data[12]));
					$weight_10	  =str_replace(',','',trim($data[13]));
					$weight_11	  =str_replace(',','',trim($data[14]));
					$weight_12	  =str_replace(',','',trim($data[15]));
					$weight_13	  =str_replace(',','',trim($data[16]));
					$weight_14	  =str_replace(',','',trim($data[17]));
					$weight_15	  =str_replace(',','',trim($data[18]));
					$weight_16	  =str_replace(',','',trim($data[19]));
					$weight_17	  =str_replace(',','',trim($data[20]));
					$weight_18	  =str_replace(',','',trim($data[21]));
					$weight_19	  =str_replace(',','',trim($data[22]));
					$weight_20	  =str_replace(',','',trim($data[23]));
					$weight_21	  =str_replace(',','',trim($data[24]));
					$weight_22	  =str_replace(',','',trim($data[25]));
					$weight_23	  =str_replace(',','',trim($data[26]));
					$weight_24	  =str_replace(',','',trim($data[27]));
					$weight_25	  =str_replace(',','',trim($data[28]));
					$weight_26	  =str_replace(',','',trim($data[29]));
					$weight_27	  =str_replace(',','',trim($data[30]));
					$weight_28	  =str_replace(',','',trim($data[31]));
					$weight_29	  =str_replace(',','',trim($data[32]));
					$weight_30	  =str_replace(',','',trim($data[33]));
					$weight_31	  =str_replace(',','',trim($data[34]));
					$weight_32	  =str_replace(',','',trim($data[35]));
					$weight_33	  =str_replace(',','',trim($data[36]));
					$weight_34	  =str_replace(',','',trim($data[37]));
					$weight_35	  =str_replace(',','',trim($data[38]));
					$weight_36	  =str_replace(',','',trim($data[39]));
					$weight_37	  =str_replace(',','',trim($data[40]));
					$weight_38	  =str_replace(',','',trim($data[41]));
					$weight_39	  =str_replace(',','',trim($data[42]));

					$weight_array=array('0.50','1','1.5','2','2.5','3','3.5','4','4.5','5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10','10.5','11','11.5','12','12.5','13','13.5','14','14.5','15','15.5','16','16.5','17','17.5','18','18.5','19','19.5','20');
					
						if($pincode!='' && $area!='' && $DP_service!='')
						{
							for($COUNT=0;$COUNT <40;$COUNT++)
							{
							$rate=${'weight_'.$COUNT};
							$sql  = "insert into $table_name ";
							$sql .= " SET pincode='".addslashes($pincode)."'";
							$sql .= " , area='".addslashes($area)."'";
							$sql .= " , DP_service='".addslashes($DP_service)."'";
							$sql .= " , weight='".$weight_array[$COUNT]."'";
							$sql .= " , rate='".$rate."'";
							//exit();
							$cm = mysqli_query($conn,$sql);
							if(!$cm){
							echo mysqli_error();
						   }
						}
					}
				}//End of IF
				 $rec_count++;
			 }//End of main foreach
			$successval=1;
		}
		else
		{
			$submsg="Naming convention for DP list.csv is wrong.";
		}
		
		if($successval==1)
		{
			$submsg .= 'Data has been uploaded successfully';
		}
		else 
		{
			$submsg .= " Something went wrong.";
			
		}
	}
	else{
		$submsg = "Failed to upload the file. Please try later.";
	}	
}else{
	$submsg = "Please browse the csv file first...";
}
		/*---------------------------------CODE END--------------------------------------------*/

?>
<section align="center">
                        <div >
                          <h2>Upload Data</h2>
                        </div>
                        <div style="height:10px;"></div>
<div >
<form action="" method="POST" name="upld_data_form" id="upld_data_form" enctype="multipart/form-data">
<div  style="margin:0px;">
<div >
    <div style="margin-top:10px;">
        <label for="zip_file">Upload file <strong><font color="#FF0000">[File extension will be .csv]</font></strong></label>
        <div>
<input type="file" class="form-control" id="csv_file" name="csv_file" placeholder="Select CSV file">
        </div>
    </div>
    <div style="height:10px;"></div>
<div >
<input type="submit"  name="upload" value="Upload" />
</div>
<div >
<?php if($submsg!=""){ echo $submsg;}?>
</div>  
</form>
</div>
    </section>
<?php
mysqli_close();
?>