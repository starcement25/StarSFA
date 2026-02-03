<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ob_start();
    session_start();
    require("adminUtils.php");
    require("include/config.php");
    require("include/config-setup.php");
    require("include/dbcon.php");

    if (isset($_SESSION['competitor_name_passing_array'])) 
    {
        $storedArray = $_SESSION['competitor_name_passing_array'];
    }
    set_time_limit(0); // no time limit
    $start_date = "2025-10-01";
    $end_date = "2025-10-07";
    // $employee = $_REQUEST['employee'];

    // echo "<pre>";

    // print_r($storedArray);

    $competitor_name_array = array();

    $sql_competitor_name = "SELECT DISTINCT competitor_name FROM competitor_group_master WHERE acedns='yes' ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,competitor_name ASC";
    $res_competitor_name = mysqli_query($link,$sql_competitor_name);
    $countcompetitor=mysqli_num_rows($res_competitor_name);
    //$colspanheader=10+($countcompetitor*4);

    $header_not_star = "SI. No"."\t"."Date of Visit"."\t"."Emp Code"."\t"."Emp Name"."\t"."Branch"."\t"."Cust Category"."\t"."Cust Code"."\t"."Cust Name"."\t"."Route Name"."\t"."Contact No"."\t";

    // $table_data_star='';
    $table_data_not_star='';
    // echo "<pre>";
    // print_r($storedArray);

    $competitor_name_array=array();
    // while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){
    // 	$competitor_name1 = $row_competitor_name['competitor_name'];
    foreach($storedArray as $competitor_name1)
    {
        /*if(in_array($competitor_name1,$competitor_name_array)){
            // $competitor_name='';
            // //$header.= $competitor_name."\t".""."\t".""."\t".""."\t";
            // $header.= $competitor_name."\t".""."\t".""."\t";
            // //$header.= $competitor_name."\t";
            $header.= $competitor_name1."\t".""."\t".""."\t";
            array_push($competitor_name_array,$competitor_name1);
            $competitor_string .= "'".$competitor_name1."',";
            
        }else{
        //$header.= $competitor_name."\t".""."\t".""."\t".""."\t";
        // $header.= $competitor_name1."\t".""."\t".""."\t";
        //$header.= $competitor_name."\t";
            continue;
        // array_push($competitor_name_array,$competitor_name1);
        // $competitor_string .= "'".$competitor_name1."',";
        }*/
        $competitor_string .= "'".$competitor_name1."',";
        $header_star.= $competitor_name1."\t".""."\t".""."\t"."\t"."\t";
        $header_not_star.= $competitor_name1."\t".""."\t".""."\t"."\t"."\t";
        array_push($competitor_name_array,$competitor_name1);
    }
    // echo "<pre>";
    // print_r($storedArray);
    $competitor_string = rtrim($competitor_string,",");

    $header_star.="\n";
    $header_star.=""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t";

    $header_not_star.="\n";
    $header_not_star.=""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t";

    foreach($storedArray as  $competitorheaderval)
    {
            
        // echo $competitorheaderval."<br/>";

        // echo $_SESSION['nick_name'];

        // if($_SESSION['nick_name']=='STAR'){
            // $header_star.="Billing"."\t";
            // $header_star.="WSP"."\t";
            // $header_star.="RSP"."\t";
            // $header_star.="NOD"."\t";
            
            
            //$header.="NOD"."\t";
        // }
        // else{
            
            $header_not_star.="Billing EX"."\t";
            $header_not_star.="Billing For"."\t";
            $header_not_star.="WSP EX"."\t";
            $header_not_star.="WSP For"."\t";
            $header_not_star.="RSP"."\t";
            
            // $header.="Billing EX"."\t";
            // $header.="Billing For"."\t";
            // $header.="WSP EX"."\t";
            // $header.="WSP For"."\t";
            // $header.="RSP"."\t";
        // }
        
    }

    //$header.="\n";
    $count=1;
    $queryFrom = 0;
    $queryTo = 1000;
    $filename = "large_data.csv";
    $filePath = __DIR__ . "/" . $filename;
    $fileHandle = fopen($filePath, 'w');

    while(true)
    {
        $dataChunk = [];
        $sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,CM.dns_customer_code,RM.route_name,EM.emp_name,EM.dns_emp_code,
                                SUM(MF.PTD) PTD,SUM(MF.PTR) PTR,SUM(MF.PTC) PTC,SUM(MF.PV) PV ,SUM(MF.billing_ex_for) billing_ex_for,SUM(MF.wsp_ex_for) wsp_ex_for,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,BM.branch_name,
                                MF.competitor_name 
                                FROM market_feedback MF,customer_master CM,employee_master EM,route_master RM,branch_master BM 
                                WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND 
                                SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND 
                                (DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
                                MF.competitor_name IN (".$competitor_string .") AND CM.branch_code=BM.branch_code 
                                GROUP BY SUBSTRING(MF.market_feedback_id,3,5),DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
                                DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,FIELD(MF.competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,MF.competitor_name ASC LIMIT ".$queryFrom.", ".$queryTo;

        //echo $sql_competitor_stock;exit();
        $res_competitor_stock = mysqli_query($link,$sql_competitor_stock);
        $count_competitor_stock=mysqli_num_rows($res_competitor_stock);
        if($count_competitor_stock >0)
        {
            $customer_emp_date_array=array();
            
            while($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock))
            {
                
                $dns_emp_code = $row_competitor_stock['dns_emp_code'];
                $visit_date = $row_competitor_stock['visit_date'];
                $dns_customer_code = $row_competitor_stock['dns_customer_code'];
                $emp_name = $row_competitor_stock['emp_name'];
                ${'emp_name'.$dns_emp_code.$dns_customer_code.$visit_date}=$emp_name;
                ${'customer_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['customer_name'];
                ${'phone_no'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['phone_no'];
                ${'cust_type'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['cust_type'];
                ${'route_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['route_name'];
                ${'competitor_name_db'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['competitor_name'];

                ${'PTD'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTD'];
                ${'PTR'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTR'];
                ${'PTC'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTC'];
                ${'PV'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PV'];
                ${'billing_ex_for'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['billing_ex_for'];
                ${'wsp_ex_for'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['wsp_ex_for'];

                ${'branch_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['branch_name'];
                $customer_emp_date_string=$dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;
                
                /*$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
                
                $sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
                $res_emp_details = mysqli_query($link,$sql_emp_details);
                $row_emp_details = mysqli_fetch_assoc($res_emp_details);
                $emp_name = $row_emp_details['emp_name'];
                $dns_emp_code = $row_emp_details['dns_emp_code'];
                
                $sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
                $res_customer_details = mysqli_query($link,$sql_customer_details);
                $row_customer_details = mysqli_fetch_assoc($res_customer_details);
                $customer_name = $row_customer_details['customer_name'];
                $dns_customer_code = $row_customer_details['dns_customer_code'];
                $cust_type = $row_customer_details['cust_type'];*/
                if(!in_array($customer_emp_date_string,$customer_emp_date_array))
                {
                    array_push($customer_emp_date_array,$customer_emp_date_string);
                }
            }

            // echo "<pre>";
            // print_r($row_competitor_stock['competitor_name']);
            for($i=0;$i < count($customer_emp_date_array);$i++)
            {
                $customer_emp_date_string_val=explode("#",$customer_emp_date_array[$i]);
                $dns_emp_code_val=$customer_emp_date_string_val[0];
                $dns_customer_code_val=$customer_emp_date_string_val[1];
                $date_val=$customer_emp_date_string_val[2];


                // $table_data_not_star .= $count."\t".$date_val."\t".$dns_emp_code_val."\t".${'emp_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'branch_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'cust_type'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".$dns_customer_code_val."\t".${'customer_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'route_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'phone_no'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t";
                $table_data_not_star = [$count, $date_val, $dns_emp_code_val, ${'emp_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}, ${'branch_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}, ${'cust_type'.$dns_emp_code_val.$dns_customer_code_val.$date_val}, $dns_customer_code_val, ${'customer_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}, ${'route_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}, ${'phone_no'.$dns_emp_code_val.$dns_customer_code_val.$date_val}];

                foreach($storedArray as  $competitorval)
                {

                    // if($competitorval == ${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval}){

                        $competitor_name_val=${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                        
                        // if($_SESSION['nick_name']!='STAR'){
                            
                            // $total_PTD=${'PTD'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                            // $total_PTR=${'PTR'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                            // $total_PTC=${'PTC'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                            // $total_PV=${'PV'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};

                            $total_PTD_ex=${'PTD'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                            $total_PTD_for=${'billing_ex_for'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
                            $total_PTR_ex=${'PTR'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                            $total_PTR_for=${'wsp_ex_for'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
                            $total_PTC=${'PTC'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};

                            // if($total_PTD==0){
                            // 	$total_PTD='-';
                            // }
                            // if($total_PTR==0){
                            // 	$total_PTR='-';
                            // }
                            if($total_PTC==0)
                            {
                                $total_PTC='-';
                            }
                            if($total_PTD_ex==0){
                                $total_PTD_ex='-';
                            }
                            if($total_PTD_for==0){
                                $total_PTD_for='-';
                            }
                            if($total_PTR_ex==0){
                                $total_PTR_ex='-';
                            }
                            if($total_PTR_for==0){
                                $total_PTR_for='-';
                            }

                            // $table_data_not_star .=$total_PTD_ex."\t".$total_PTD_for."\t".$total_PTR_ex."\t".$total_PTR_for."\t".$total_PTC."\t";
                            array_push($table_data_not_star, $total_PTD_ex);
                            array_push($table_data_not_star, $total_PTD_for);
                            array_push($table_data_not_star, $total_PTR_ex);
                            array_push($table_data_not_star, $total_PTR_for);
                            array_push($table_data_not_star, $total_PTC);
                            
                            // echo "table_data_not_star= ".$table_data_not_star."<br/>";
                            // $total_PTD_for='-';
                            // $total_PTR_for='-';
                        // }else{
                            
                            // $total_PTC=${'PTC'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
                            // $total_PV=${'PV'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};

                            // $table_data_star .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV."\t";

                            // echo "table_data_star= ".$table_data_star."<br/>";
                        // }
                        
                    // }

                    

                    // else
                    // {
                    // 	// $total_PTD='-';
                    // 	// $total_PTR='-';	
                    // 	$total_PTC='-';	
                    // 	// $total_PV='-';
                    // 	$total_PTD_ex='-';
                    // 	$total_PTD_for='-';
                    // 	$total_PTR_ex='-';
                    // 	$total_PTR_for='-';
                        
                    // }
                    
                    //$table_data .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV."\t";
                    // if($_SESSION['nick_name']=='STAR'){
                        // $table_data .=$total_PTD_ex."\t".$total_PTD_for."\t".$total_PTR_ex."\t".$total_PTR_for."\t".$total_PTC."\t";
                    
                    // }else{
                        // $table_data .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV;
                        
                    // }
                    
                    //$table_data .=$total_PTD."\t";
                    
                    $table_data_not_star .="\n";
                    $count++;
                }	
            
                // $table_data_star .="\n";
                // $table_data_not_star .="\n";
                // $count++;
                fputcsv($fileHandle, $table_data_not_star);
            }
        }
        else
        {
            break;
        }
        $queryFrom += $queryTo;
    }

    fclose($fileHandle);

    if(file_exists($filePath)) 
    {
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Clear output buffer
        ob_clean();
        flush();

        // Step 7: Stream file to browser
        readfile($filePath);

        // Step 8: Delete file after sending
        unlink($filePath);
        exit;
    } else {
         echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
    }

    // $table_data_star="test";

    // print_r($table_data);
    // $table_data_star.="test9";
    // if($table_data_not_star!='')
    // {	
        
    //     header("Content-type: application/octet-stream"); 
    //     header("Content-Disposition: attachment; filename=Market feedback price.xls"); 
    //     header("Pragma: no-cache"); 
    //     header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
    //     echo ucwords($header_not_star)."\n".$table_data_not_star;
    
    // }
    // else
    // {
    //     echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
    // }
        
    // if($table_data_star!=''){
        // header("Content-type: application/octet-stream");
        // header("Content-Disposition: attachment; filename=Market feedback price1.xls"); 
        // header("Pragma: no-cache"); 
        // header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
        // echo ucwords($header_star)."\n".$table_data_star;
    // }else{
    // 	echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
    // }

    // if($table_data_star!='' || $table_data_not_star!=''){
    // 	header("Content-type: application/octet-stream"); 
    // 	header("Content-Disposition: attachment; filename=Market feedback price.xls"); 
    // 	header("Pragma: no-cache"); 
    // 	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
    // 	echo ucwords($header_not_star)."\n".$table_data_not_star;
    // 	// echo ucwords($header_star)."\n".$table_data_star;
    // }else
    // 	{
    // 		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
    // 	}

    mysqli_close($link);

?>

