<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 //$nick_name="STAR";
//require("include/config.php");
//require("include/config-setup.php");
//require("include/dbcon.php");

require("send_email.php");

//require("include/functions.php");
//require("config-email-setup.php");
//define("DB1","acedns_$nick_name");
//$link=mysqli_connect(SERVER,USER,PASSWORD,"acedns_STAR") or die("Database Connection Error.");
// Star_Complain_Email("SUE080320240709151836",$link);
 
// echo get_all_email("E0803");
 
 
 function get_all_email($emp_code,$link){
      
    $emp= return_employee_upper_hierarchy1($emp_code,$link);
     $email="";
     $sql="SELECT * FROM `employee_master` WHERE `emp_code` IN($emp)";
      $rsemail=mysqli_query($link,$sql);
      //$rowemail=mysqli_fetch_assoc($rsemail);
      while($rowemail=mysqli_fetch_assoc($rsemail))
		{
		    if($rowemail['email']!=""){
		        $email= $rowemail['email']."," .$email;
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
			$emphierarchy[] ="'".$emp_code."'";
		}
	}
}
 
 
 
 
 

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
    
	
	//echo "-".$emp_code;
	
	
	
    $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
    //echo $sqlemphierarchy;
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
		$reporting_to=$rowemphierarchy['reporting_to'];
			if(strpos($reporting_to,',')!=false){
				$reporting_to_Arr=explode(',',$reporting_to);
			//print_r($reporting_to_Arr);
				for($cn=0;$cn<count($reporting_to_Arr);$cn++)
				{
					//$emphierarchy[] = "'".$reporting_to_Arr[$cn]."'";
					
					$sqlemail="SELECT emp_code,email FROM employee_master WHERE emp_code='".$reporting_to_Arr[$cn]."'";
					//echo $sqlemail.";";
					   $emailrsemphierarchy=mysqli_query($link,$sqlemail);
                        //$cntemphierarchy=mysqli_num_rows($emailrsemphierarchy);
                        $emailrowemphierarchy=mysqli_fetch_assoc($emailrsemphierarchy);
                       //echo $cn."-". $emailrowemphierarchy['email'];
					if($emailrowemphierarchy['email']!=""){
        			    if(!in_array($emailrowemphierarchy['email'],$emphierarchy))
                		{
                		    array_push($emphierarchy,$emailrowemphierarchy['email']);
                		}
                		  //array_push($emphierarchy,$emailrowemphierarchy['email']);
			    
			        }
					
					
					employee_hierarchy_details_active_email($reporting_to_Arr[$cn],$emphierarchy,$link);
				}
			}
			else
			{
				//$emphierarchy[] = "'".$reporting_to."'";
				$sqlemail="SELECT emp_code,email FROM employee_master WHERE emp_code='".$emp_code."'";
				//echo $sqlemail.";";
					   $emailrsemphierarchy=mysqli_query($link,$sqlemail);
                        //$cntemphierarchy=mysqli_num_rows($emailrsemphierarchy);
                        $emailrowemphierarchy=mysqli_fetch_assoc($emailrsemphierarchy);
                        //echo ">".$emailrowemphierarchy['email'];
					if($emailrowemphierarchy['email']!=""){
        			    if(!in_array($emailrowemphierarchy['email'],$emphierarchy))
                		{
                		    array_push($emphierarchy,$emailrowemphierarchy['email']);
                		}
			    
			        }
				employee_hierarchy_details_active_email($reporting_to,$emphierarchy,$link);
			}
		}
	}
	
	
	
	
	
	
	
	
	
}



function Star_Complain_Email($survey_id,$link)
{
    /*$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");*/
    //$link=mysqli_connect(SERVER,USER,PASSWORD,"acedns_STAR") or die("Database Connection Error.");
    //$survey_id="SUE082220231026131954";
    $emp_code = substr($survey_id,2,5);
    //echo $emp_code;
    
    $new = "New";
    
    $sqlemphierarchy="SELECT row_id,value FROM survey_output WHERE survey_id='".$survey_id."' AND row_id='RA209'";
   $rs1=mysqli_query($link,$sqlemphierarchy);
   $cn1=mysqli_num_rows($rs1);
	if($cn1>0)
	{
	    $new = "New";
	}else{
	    $new = "Existing";
	}
    
    
        	$sqlemail="SELECT emp_code,email,emp_name  FROM employee_master WHERE emp_code='".$emp_code."'";
					   $emailrsemphierarchy=mysqli_query($link,$sqlemail);
                        //$cntemphierarchy=mysqli_num_rows($emailrsemphierarchy);
                        $emailrowemphierarchy=mysqli_fetch_assoc($emailrsemphierarchy);
                        $emp_name=$emailrowemphierarchy['emp_name'];
                        
    
    $surveyemailbody="<html><body>";
    $surveyemailbody.='<a href="https://salesmpower.acedns.in/complain_csv.php?nick_name=STAR&survey_id='.$survey_id.'">Click to Download ('.$new.')</a>';
    $surveyemailbody.="</body></html>";
    
    $surveyemailbody.="<html><body>";
    
    
    /*$surveyemailbody.="https://salesmpower.acedns.in/complain_csv.php?survey_id=".$survey_id;*/
    
    $surveyemailbody.="<table>";
    
    
    $surveyemailbody .="<tr><td>Emp Code</td><td>".$emp_code."</td></tr>";
    
    $surveyemailbody .="<tr><td>Emp Name</td><td>".$emp_name."</td></tr>";
    
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
	$surveyemailbody.="</table>";
    $surveyemailbody.="</body></html>";
	$spam_filter='@coral.in';
	$flag = 0;
	$emp_email = get_all_email($emp_code,$link);
	$subject = "Star SFA - Complaint Report";
	send_the_mail('antarabanerjee@starcement.co.in,anupshaw@starcement.co.in,'.$emp_email, $subject, $surveyemailbody);
//		send_the_mail('satyajitm@coral.in,', $subject, $surveyemailbody);
//	echo $emp_email;
			//	echo $surveyemailbody;		
				
				
	
}



?>