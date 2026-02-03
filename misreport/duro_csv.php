<?php 

ob_start();
session_start();
require("adminUtils.php");


  $from_date = date('Y-m-d', strtotime("01-04-2022"));
            $to_date = date('Y-m-d', strtotime("31-03-2023"));
            //$to_date = date('Y-m-d', strtotime("01-05-2023"));
            //$survey_type = $_REQUEST['survey_type'];
            /*if ($from_date != '' && $to_date != '') {
                $date_condition = " AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='" . $to_date . "'";*/
					  	
	//	echo	$date_condition;exit();		  	

  
    

    
  
    $values_all = "";



// Get The Field Name
/*
for ($i = 0; $i < $count_menu; $i++) {
//$heading = mysqli_field_name($sql, $i);
//$heading = mysqli_fetch_field_direct($sql, $i)->name;
//echo $heading;extit();
$output .= '"'.$heading.'",';
}
$output .="\n";*/
// Get Records from the table


$name_type = "Existing";

$Site_name=$Site_owner_name=$Site_owner_Contact_Number=$Check_in_Date=	$Address=$Sub_area=$Location=$Area=$City=$State=$Pin=$Contact_Person=$Contact_Person=$Phone_no=$Stage_Of_Construction=$Project_Type=$Sq_Ft_Area=$Product_Required=$Scope_of_Teak=$Scope_of_NTD=$Site_referred_by=$Next_follow_up_date=	$Escalation_Clause=$Expected_month_of_maturity=$Remarks=$Designation=$Contact_Person_Type=$Email_Id="";

/*$output .="Sr. No,Unique Store ID,	Emp Name,	Survey Date,Lattitude,	Longitude,Site name,Site owner name,Site owner Contact Number,Check in Date,	Address,Sub area,Location,Area,City,State,Pin,Contact Person ,Contact Person Phone no,Stage Of Construction,Project Type,Sq Ft Area,Product Required,Scope of Teak,Scope of NTD,Site referred by,Next follow up date,	Escalation Clause,Expected month of maturity,Remarks,Designation,Contact Person Type,Email Id
";*/

///// fro existing ////
/*$output .="Sr. No,Unique Store ID,	Emp Name,	Survey Date,Lattitude,	Longitude,Site name,Site owner name,Site owner Contact Number,Sub area,Check in Date,	Address,Location,Area,City,State,Pin,Contact Person ,Contact Person Phone no,Stage Of Construction,Project Type,Sq Ft Area,Product Required,Scope of Teak,Scope of NTD,Site referred by,Next follow up date,	Escalation Clause,Expected month of maturity,Remarks,Designation,Contact Person Type,Email Id
";*/



$output .="\n";

$output .="Sr. No,Unique Store ID,	Emp Name,	Survey Date,Lattitude,	Longitude,";

 $sql_get_menu = "SELECT display_name,row_id,action,display_order FROM survey_input WHERE  type!='menu' AND menu_id='RA002'  AND acedns='Y' ORDER BY display_order ASC";
                    $res_get_menu = mysqli_query($link,$sql_get_menu);
                    $count_menu = mysqli_num_rows($res_get_menu);
                    
while ($row_survey_output = mysqli_fetch_assoc($res_get_menu)) {
    
    
                        
                        $display_name = $row_survey_output['display_name'];
                        $display_id = $row_survey_output['row_id'];
                        $output .='"'.$display_name.'",';
                        
                        
                        
                        $row_id_string .= "'" . $display_id . "'" . ',';
                            $row_id_string_SET .= $display_id . ',';
}
$output .="\n";
$row_id_string = substr($row_id_string, 0, -1);
$row_id_string_SET = substr($row_id_string_SET, 0, -1);



$i=1;


$sql_test = "SELECT DISTINCT(SO.survey_id) FROM location SO WHERE DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='" . $to_date . "'";

//echo $sql_test;exit();

  $sql_survey_existing_row_id = "SELECT DISTINCT(SO.survey_id),DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(" . $row_id_string . ") AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='" . $to_date . "' AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code" . $emp_condition . " 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "') ";
									  
									   //echo $sql_survey_existing_row_id;exit();
									  
                            $rs_survey_existing_row_id = mysqli_query($link,$sql_survey_existing_row_id);
                            while ($row_survey_existing_row_id = mysqli_fetch_assoc($rs_survey_existing_row_id)) {
                                
                                $survey_id_existing = $row_survey_existing_row_id['survey_id'];
                                
                                //echo $survey_id_existing ."--";
                               $emp_code =  substr($survey_id_existing,2,-14);
                               
                               //echo $emp_code;exit();
                                
                                $output .='"'.$i.'",';
                                
                                $i=1+$i;
                                $output .='"'.$survey_id_existing.'",';
                                
                             
                                
                                
                                ///////////////////Emp Name/////////
                                
                                
                                
                                        $sql_survey_existing_emp = "SELECT emp_name FROM employee_master SO WHERE SO.emp_code='".$emp_code."' ";
                                  
                                  //echo $sql_survey_existing_row_id ; exit();
                                $rs_survey_existing_row_id_value_emp = mysqli_query($link,$sql_survey_existing_emp);
                 while ($row_emp = mysqli_fetch_assoc($rs_survey_existing_row_id_value_emp)) {
    
                            $emp_name = $row_emp['emp_name'];
                         
                        }
                                
                                
                                
                                   $output .='"'.$emp_name.'",';
                                
                                $output .='"'.$row_survey_existing_row_id['survey_date'].'",';
                                
                                
                                
                                
                                
                                $sql_survey_existing_location = "SELECT latt,longi FROM location SO WHERE SO.trans_id='".$row_survey_existing_row_id['survey_id']."' ";
                                  
                                  //echo $sql_survey_existing_row_id ; exit();
                                $rs_survey_existing_row_id_value_location = mysqli_query($link,$sql_survey_existing_location);
                 while ($row_location = mysqli_fetch_assoc($rs_survey_existing_row_id_value_location)) {
    
    
                            $latt = $row_location['latt'];
                            
                            $output .='"'.$latt.'",';
    
                            $longi = $row_location['longi'];
                            
                            $output .='"'.$longi.'",';
                            
                            
                            
    
    
                 }
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                  $sql_survey_existing_row_id = "SELECT value,row_id FROM survey_output SO WHERE SO.survey_id='".$row_survey_existing_row_id['survey_id']."' ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "')";
                                  
                                  //echo $sql_survey_existing_row_id ; exit();
                                $rs_survey_existing_row_id_value = mysqli_query($link,$sql_survey_existing_row_id);
                 while ($row = mysqli_fetch_assoc($rs_survey_existing_row_id_value)) {
    
    
    $value = $row['value'];
     if ($row['row_id'] == 'RA003'){
        $valueparts = explode(";", $value);
        
        $Site_name = $valueparts[0];
        $output .='"'.$Site_name.'",';
    }else
    if ($row['row_id'] == 'RA024'){
        $valueparts = explode(";", $value);
        
        $Site_name = $valueparts[0];
        $output .='"'.$Site_name.'",';
    }else if ($row['row_id'] == 'RA004'){
        $valueparts = explode(";", $value);
        
        $Address = $valueparts[0];
        $output .='"'.$Address.'",';
    }
    else if ($row['row_id'] == 'RA005'){
        $valueparts = explode(";", $value);
        
        $Sub_area = $valueparts[0];
        $output .='"'.$Sub_area.'",';
    }else if ($row['row_id'] == 'RA006'){
        $valueparts = explode(";", $value);
        
        $Location = $valueparts[0];
        $output .='"'.$Location.'",';
    }
    else if ($row['row_id'] == 'RA007'){
        $valueparts = explode(";", $value);
        
        $City = $valueparts[0];
        $output .='"'.$City.'",';
    }
    else if ($row['row_id'] == 'RA108'){
        $valueparts = explode(";", $value);
        
        $Area = $valueparts[0];
        $output .='"'.$Area.'",';
    }
    else if ($row['row_id'] == 'RA031' || $row['row_id'] == 'RA032' || $row['row_id'] == 'RA103'){
        $valueparts = explode(";", $value);
        
        $sqlfacilitatorname = "SELECT facilitator_name FROM facilitator_master WHERE f_code='" . $valueparts[0] . "'";
                                        $rsfacilitatorname = mysqli_query($link,$sqlfacilitatorname);
                                        $rowfacilitatorname = mysqli_fetch_assoc($rsfacilitatorname);
                                        $facilitator_name = $rowfacilitatorname['facilitator_name'].'",';
                                        
                                        
                                        
        $sqlfacilitatorname = "SELECT facilitator_name FROM facilitator_master WHERE f_code='" . $valueparts[1] . "'";
                                        $rsfacilitatorname1 = mysqli_query($link,$sqlfacilitatorname);
                                        $rowfacilitatorname1 = mysqli_fetch_assoc($rsfacilitatorname1);
                                        $facilitator_name .= $rowfacilitatorname1['facilitator_name'];
        
        
        
        $output .='"'.$facilitator_name.'",';
    }
    
        else if ($row['row_id'] == 'RA102'){
        $valueparts = explode(";", $value);
        
        $sql_survey_existing_emp = "SELECT emp_name FROM employee_master SO WHERE SO.emp_code='".$valueparts[0]."' ";
                                        $rsfacilitatorname = mysqli_query($link,$sql_survey_existing_emp);
                                        $rowfacilitatorname = mysqli_fetch_assoc($rsfacilitatorname);
                                        $e_name = $rowfacilitatorname['emp_name'].'",';
                                        
                                        
                                        
        $sql_survey_existing_emp = "SELECT emp_name FROM employee_master SO WHERE SO.emp_code='".$valueparts[1]."' ";
                                        $rsfacilitatorname1 = mysqli_query($link,$sql_survey_existing_emp);
                                        $rowfacilitatorname1 = mysqli_fetch_assoc($rsfacilitatorname1);
                                        $e_name .= $rowfacilitatorname1['emp_name'];
        
        
        
        $output .='"'.$e_name.'",';
    }
    
    else if ($row['row_id'] == 'RA087' || $row['row_id'] == 'RA088'){
        $valueparts = explode(";", $value);
        
        $sql_survey_existing_emp = "SELECT customer_name FROM customer_master SO WHERE SO.customer_code='".$valueparts[0]."' ";
                                        $rsfacilitatorname = mysqli_query($link,$sql_survey_existing_emp);
                                        $rowfacilitatorname = mysqli_fetch_assoc($rsfacilitatorname);
                                        $c_name = $rowfacilitatorname['emp_name'].'",';
                                        
                                        
                                        
        $sql_survey_existing_emp = "SELECT customer_name FROM customer_master SO WHERE SO.customer_code='".$valueparts[1]."' ";
                                        $rsfacilitatorname1 = mysqli_query($link,$sql_survey_existing_emp);
                                        $rowfacilitatorname1 = mysqli_fetch_assoc($rsfacilitatorname1);
                                        $c_name .= $rowfacilitatorname1['emp_name'];
        
        
        
        $output .='"'.$c_name.'",';
    }
    
    else{
        $output .='"'.$row['value'].'",';
    }
    
    
    
  //  ====$State=$Pin=$Contact_Person=$Contact_Person
    
     

//$output .="\n";
}
                                
   $output .="\n";               //echo $output;  exit();              
                              
                            
                                
                            
                                
                                
                            }
                            
                            //$output.= $survey_id_existing.",". $values_all;
                  
//echo $output;     
                        
//$output .="\n";

//exit();


$curr_date = date("jS_M_Y_h_m_s_A");
// Download the file
$the_file_name = "location_list_".$curr_date.".csv";
$filename = "Site Visit_".$name_type."-".$from_date."-".$to_date.".csv";//$the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;




?>