<?php 
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$survey_id=$_REQUEST['survey_id'];
$emp_code = substr($survey_id,2,5);
$output="Type,survey_id,emp_code,emp_name,";

    $new = "New";
    
    	$sqlemail="SELECT emp_code,email,emp_name  FROM employee_master WHERE emp_code='".$emp_code."'";
					   $emailrsemphierarchy=mysqli_query($link,$sqlemail);
                        //$cntemphierarchy=mysqli_num_rows($emailrsemphierarchy);
                        $emailrowemphierarchy=mysqli_fetch_assoc($emailrsemphierarchy);
                        $emp_name=$emailrowemphierarchy['emp_name'];
    
    $sqlemphierarchy="SELECT row_id,value FROM survey_output WHERE survey_id='".$survey_id."' AND row_id='RA209'";
   $rs1=mysqli_query($link,$sqlemphierarchy);
   $cn1=mysqli_num_rows($rs1);
	if($cn1>0)
	{
	    $new = "New";
	}else{
	    $new = "Existing";
	}

    $sqlemphierarchy="SELECT row_id,value FROM survey_output WHERE survey_id='".$survey_id."'";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
		    $row_id = $rowemphierarchy['row_id'];
		    $sql="SELECT display_name FROM survey_input WHERE row_id='".$row_id."'";
            $rs_display=mysqli_query($link,$sql);
       
            $dis_row = mysqli_fetch_assoc($rs_display);
            $output .='"'.$dis_row['display_name'].'",';
		}
	}
            $output .="\n";

$output.=$new.','.$survey_id.','.$emp_code.','.$emp_name.',';

    $sqlemphierarchy="SELECT row_id,value FROM survey_output WHERE survey_id='".$survey_id."'";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
		    $value="";
		    $row_id = $rowemphierarchy['row_id'];
		    $value = $rowemphierarchy['value'];
		    if($row_id=="RA209" || $row_id=="RA283"){
		        
		        $array = explode(";", $value);
		        $value=$array[0];
		        //print_r($array);
		    }else if($row_id=="RA210"){
		        $sql="SELECT branch_name FROM branch_master WHERE branch_code='".$value."'";
		    
            $rs_b=mysqli_query($link,$sql);
            
            $b_row = mysqli_fetch_assoc($rs_b);
            $value = $b_row['branch_name']."(".$value.")";
            
		    }
		    else{
		        //$value = $rowemphierarchy['value'];
		    }
		    
		    if(strpos($value,'.JPEG')!=false){
		        $string = str_replace('JPEG;', 'jpeg', $value);
		        $output .='"https://starcement.s3.ap-south-1.amazonaws.com/'.$string.'",';
		    }else{
		        $output .='"'.$value.'",';
		    }
            
		}
	}


//echo $output; exit();


$curr_date = date("Y_M_D_m_s_A");
// Download the file
$the_file_name = $new."_Complain Data_".$curr_date.".csv";
$filename = $the_file_name;//Complain Close Data.csv";//$the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;



?>