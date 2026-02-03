<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/


$nick_name="STAR";
//require("include/config.php");
//require("include/config-setup.php");


require("send_email_lead.php");
$survey_id=$_REQUEST['su_id'];


 Star_Lead_Email($survey_id,"$link");


function Star_Lead_Email($survey_id,$link1)
{
    $e_emp_code="";
    $link=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234#","acedns_STAR") or die("Database Connection Error.");
   
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
    
    $surveyemailbody.="</body></html>";
    
    $surveyemailbody.="<html><body>";
    
    
    /*$surveyemailbody.="https://salesmpower.acedns.in/complain_csv.php?survey_id=".$survey_id;*/
    
    $surveyemailbody.="<table>";
    
    $surveyemailbody .="<tr><td>ID </td><td>".$survey_id."</td></tr>";
    
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
		    if($row_id=="RA486" || $row_id=="RA516"){
		        
		        //$value = substr(explode(";", $value), 0);
		        $array = explode(";", $value);
		        $value=$array[0];
		        //print_r($array);
		    }else if($row_id=="RA210"){
		        $sql="SELECT branch_name FROM branch_master WHERE branch_code='".$value."'";
		    
            $rs_b=mysqli_query($link,$sql);
            
            $b_row = mysqli_fetch_assoc($rs_b);
            $value = $b_row['branch_name']."(".$value.")";
            
		    }else if($row_id=="RA501" || $row_id=="RA531"){
		        $e_emp_code = $value;
		    }
		    else{
		        //$value = $rowemphierarchy['value'];
		    }
		    
		    $value = $value;
		    
		    $sql="SELECT display_name FROM survey_input_lead WHERE row_id='".$row_id."'";
            $rs_display=mysqli_query($link,$sql);
            
            
            
            
            if($row_id=="RA592" || $row_id=="RA585"){
    			    if($value!=""){
    			        $survey_value = $value;
    			        
    			        $site_image = $survey_value;
				$site_image = ltrim($site_image," ");

				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode("; ;",$site_image);
				//$survey_value=$site_image;
				//exit();
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					
										$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";


					}
    			        
    			        $image_string = str_replace(';','',$image_string);
    			        $survey_value=$image_string;
    			       
    			    }
    			    
    			    $value = $survey_value;
    			}
            
            
            
            
            
            
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
	//$emp_email = get_all_email($emp_code,$link);
	
	//if(){
	
	$sql="SELECT email FROM employee_master WHERE emp_code='".$e_emp_code."'";
		  echo $sql;  
            $rs_e=mysqli_query($link,$sql);
            
            $e_row = mysqli_fetch_assoc($rs_e);
            $emp_email = $e_row['email'];
	    echo $emp_email;
	//}
	//echo $surveyemailbody;
	
	$subject = "Star SFA - Lead Report";
		send_the_mail_lead('satyajitm@coral.in,'.$emp_email, $subject, $surveyemailbody);
/*	send_the_mail_lead('satyajitm@coral.in,abhishekd@forcepower.in,amitb@forcepower.in,'.$emp_email, $subject, $surveyemailbody);*/

	
}



?>