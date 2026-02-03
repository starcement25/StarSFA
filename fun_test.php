<?php

 $nick_name="STAR";
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 
 
 $emp_code=strtoupper($_REQUEST['emp_code']);
 $type=strtoupper($_REQUEST['type']);
 
require("include/config.php");
//require("include/config-setup.php");
//require("include/dbcon.php");
//require("include/config-email-setup.php");
require("include/functions.php");


//require("config-email-setup.php");
//define("DB1","acedns_$nick_name");
//$link=mysqli_connect(SERVER,USER,PASSWORD,"acedns_STAR") or die("Database Connection Error.");
// Star_Complain_Email("SUE080320240709151836",$link);
 //echo $type;
 //echo get_all_emp("E1271",$link);
 
 
/* if($type=="UPPER"){
     echo return_employee_upper_hierarchy($emp_code);
 }else if($type=="ACT"){
     echo return_employee_hierarchy_active($emp_code);
 }else{
     echo return_employee_hierarchy($emp_code);
 }*/


if($type=="APP"){
     $nick_name=$_REQUEST['nick_name'];
    $ddb = "acedns_".$nick_name;

    define("DBL","$ddb");
    
    $link=mysqli_connect(SERVER,USER,PASSWORD,DBL) or die("Database Connection Error.");
     $sqlemphierarchy="SELECT COUNT(version_code) as version_code FROM `app_updation` WHERE `version_code` LIKE '5.6.3.0'";
  //echo $sqlemphierarchy;exit();
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy);
   echo "App-". $rowemphierarchy['version_code'];
   
   
   $sqlemphierarchy="SELECT COUNT(is_update) as is_update FROM `table_structure_updation` WHERE `db_version_code` LIKE '18.9'";
  //echo $sqlemphierarchy;exit();
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy);

            echo "Db-". $rowemphierarchy['is_update'];
            
            
            
               $sqlemphierarchy="SELECT COUNT(date) as is_update FROM `attendence` WHERE `date` LIKE '2024-08-26%'";
 
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy);

            echo "Att-". $rowemphierarchy['is_update'];
            
            
}else if($type=="DB"){
     $nick_name=$_REQUEST['nick_name'];
    $ddb = "acedns_".$nick_name;

    define("DBL","$ddb");
    
    $link=mysqli_connect(SERVER,USER,PASSWORD,DBL) or die("Database Connection Error.");
     $sqlemphierarchy="SELECT COUNT(is_update) as is_update FROM `table_structure_updation` WHERE `is_update` LIKE '0'";
  //echo $sqlemphierarchy;exit();
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy);
   //while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
	//	{
            echo "Db-". $rowemphierarchy['is_update'];
	//	}
}
 //return_employee_hierarchy_test($emp_code);
 
 function return_employee_hierarchy_test($emp_code) {
    $emphierarchy = array();
    $nick_name=$_REQUEST['nick_name'];
    $ddb = "acedns_".$nick_name;

    define("DBL","$ddb");
    
    $link=mysqli_connect(SERVER,USER,PASSWORD,DBL) or die("Database Connection Error.");
    
    
    employee_hierarchy_details_test($emp_code, $emphierarchy,$link);
    
    
    
    
    
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	if(count(explode(',',$emphierarchystring))==1 && $emphierarchystring=="'".$emp_code."'")
	{
		$emphierarchystring=$emphierarchystring;
	}
	else
	{
		$emphierarchystring=$emphierarchystring.','."'".$emp_code."'";
	}
    return $emphierarchystring;
}

function employee_hierarchy_details_test($emp_code,&$emphierarchy,$link){
    
    //echo $emp_code.">";
    print_r($emphierarchy);
   //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
   $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to)";
  //echo $sqlemphierarchy;exit();
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
			//print_r($emphierarchy[]);
			employee_hierarchy_details_test($rowemphierarchy['emp_code'],$emphierarchy,$link);
			
			
		}
	}
	else
	{
		if(!in_array("'".$emp_code."'",$emphierarchy))
		{
			$emphierarchy[] ="'".$emp_code."'";
		}
	}
	
	
}
 
 
/* function get_all_emp($emp_code,$link){
      
    $emp= return_employee_upper_hierarchy1($emp_code,$link);
     $email="";
     $sql="SELECT * FROM `employee_master` WHERE `emp_code` IN($emp)";
      $rsemail=mysqli_query($link,$sql);
      //$rowemail=mysqli_fetch_assoc($rsemail);
      while($rowemail=mysqli_fetch_assoc($rsemail))
		{
		    if($rowemail['emp_code']!=""){
		        $email= $rowemail['emp_code']."," .$email;
		    }
		    
		}
		$email=substr($email,0,-1);
		return $email;
 }
 
 function return_employee_upper_hierarchy1($emp_code,$link) {
    $emphierarchy = array();
    $emphierarchystring = "";
    $emphierarchystring.="'$emp_code',";
    employee_upper_hierarchy_details1($emp_code, $emphierarchy,$link);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
    return $emphierarchystring;
}
function employee_upper_hierarchy_details1($emp_code,&$emphierarchy,$link){
     
  $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			$reporting_to=$rowemphierarchy['reporting_to'];
			if(strpos($reporting_to,',')!=false){
				$reporting_to_Arr=explode(',',$reporting_to);
			
				for($cn=0;$cn<count($reporting_to_Arr);$cn++)
				{
					$emphierarchy[] = "'".$reporting_to_Arr[$cn]."'";
					employee_upper_hierarchy_details1($reporting_to_Arr[$cn],$emphierarchy,$link);
				}
			}
			else
			{
				$emphierarchy[] = "'".$reporting_to."'";
				employee_upper_hierarchy_details1($reporting_to,$emphierarchy,$link);
			}
		}
	}
	else
	{
		if(!in_array("'".$emp_code."'",$emphierarchy))
		{
			$emphierarchy[] ="'".x."'";
		}
	}
}
 
 
*/

?>