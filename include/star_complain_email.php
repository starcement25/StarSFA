<?php

 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
// $nick_name="STAR";
require("config.php");
require("config-setup.php");
//require("dbcon.php");
//require("functions.php");
//require("config-email-setup.php");
$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
 Star_Complain_Email("SUE055520240611064738",$link);

function return_employee_hierarchy_active_email($emp_code,$link) {
    $emphierarchy = array();
    employee_hierarchy_details_active_email($emp_code, $emphierarchy,$link);
	foreach($emphierarchy as $hierarchyval)
	{
	    if($hierarchyval!=""){
		$emphierarchystring.=$hierarchyval.',';
	    }
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	if(count(explode(',',$emphierarchystring))==1 && $emphierarchystring=="'".$emp_code."'")
	{
		$emphierarchystring=$emphierarchystring;
	}
	else
	{
		$emphierarchystring=$emphierarchystring;
	}
    return $emphierarchystring;
}


function employee_hierarchy_details_active_email($emp_code,&$emphierarchy,$link){
    /* $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");*/
     
   //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
   $sqlemphierarchy="SELECT emp_code,email FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to) AND acedns='Y'";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
		    
			//$emphierarchy[] = "'".$rowemphierarchy['email']."'";
			if($rowemphierarchy['email']!=""){
			    if(!in_array($rowemphierarchy['email'],$emphierarchy))
        		{
        		    array_push($emphierarchy,$rowemphierarchy['email']);
        		}
			    
			}
			employee_hierarchy_details_active_email($rowemphierarchy['emp_code'],$emphierarchy,$link);
		}
	}
	else
	{
		/*if(!in_array("'".$emp_code."'",$emphierarchy))
		{
		    if($emp_code==""){}else{
			$emphierarchy[] ="'".$emp_code."'";
		    }
		}*/
	}
}



function Star_Complain_Email($survey_id,$link)
{
    /*$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");*/
    //$survey_id="SUE082220231026131954";
    $emp_code = substr($survey_id,2,5);
    //echo $emp_code;
    $surveyemailbody="<html><body><table>";
    
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
		        
		        //$value = substr(explode(";", $value), 0);
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
		    $sql="SELECT display_name FROM survey_input WHERE row_id='".$row_id."'";
            $rs_display=mysqli_query($link,$sql);
            
            //display_name
            $dis_row = mysqli_fetch_assoc($rs_display);
            //echo $dis_row['display_name'];
            $surveyemailbody .="<tr><td>".$dis_row['display_name']."</td><td>".$value."</td></tr>";
		}
	}
    
	$spam_filter='@coral.in';
	$flag = 0;
	$emp_email = return_employee_hierarchy_active_email($emp_code,$link);
//	echo $emp_email;
				echo $surveyemailbody;		
	
}



?>