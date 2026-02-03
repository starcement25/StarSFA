<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
require("include/functions.php");
$sqldelete="truncate sauda_allocation_temp";
$rsdelete=mysqli_query($link,$sqldelete);
$emp_code='';
 echo Populate_employee_hierarchy_allocation($emp_code);
function Populate_employee_hierarchy_allocation($emp_code)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_allocation($emp_code, $emphierarchy);
	//return $emphierarchyhtmlstring;
	//return 'SUCCESS';
}
function employee_hierarchy_allocation($emp_code,&$emphierarchy){
	$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
	$dateprevioussevendays=date('Y-m-d', strtotime("-7 days,$curdateserver "));
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$milisecond=gmdate('u',strtotime('+330 minute'));
		
	$contentsdate=$year.$month.$date;
	$contentstime=$hour.$minute.$second;
	
    $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE reporting_to='".$emp_code."' 
   					AND acedns!='N' ORDER BY emp_name ASC";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
			 $emp_code_immediate=$rowemphierarchy['emp_code'];

			 //$emp_hierarchy_condition=return_employee_hierarchy($emp_code_immediate);
			 //$emp_hierarchy_condition_lower=substr($emp_hierarchy_condition,0,-8);
			 //exit();
			 $sqlchkaccess="SELECT get_allocation FROM sauda_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
			 $rschkaccess=mysqli_query($link,$sqlchkaccess);
			 $rowchkaccess=mysqli_fetch_assoc($rschkaccess);
			 $get_allocation=$rowchkaccess['get_allocation'];
			 if($get_allocation=='yes' )
			 {
				//$contentstime=$hour.$minute.$second;
				 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master WHERE acedns='Y' ORDER BY product_group_name ASC";
				 $res_product = mysqli_query($link,$sql_product);
				 $count=1;
				  while($row_product = mysqli_fetch_assoc($res_product))
				  {
					$product_group_code_list=$row_product['product_group_code'];
					/*$sqlsevendaysbooking="SELECT SUM(SAT.AVG_qty) AS AVG_qty FROM (SELECT product_group_code,emp_code,AVG(convert_qty_two)  AS AVG_qty 
											FROM sauda_transaction_log WHERE SUBSTRING(sauda_date,-19,10) BETWEEN '".$dateprevioussevendays."' 
										 	AND '".$curdateserver."' AND emp_code IN(".$emp_hierarchy_condition.") AND 
										 	product_group_code='".$product_group_code_list."' GROUP BY product_group_code,emp_code) SAT ";*/
					$sqlsevendaysbooking="SELECT product_group_code,emp_code,AVG(convert_qty_two)  AS AVG_qty 
											FROM sauda_transaction_log WHERE SUBSTRING(sauda_date,-19,10) BETWEEN '".$dateprevioussevendays."' 
										 	AND '".$curdateserver."' AND emp_code='".$emp_code_immediate."' AND 
										 	product_group_code='".$product_group_code_list."'";	
					$rssevendaysbooking=mysqli_query($link,$sqlsevendaysbooking);	
					$cntsevendaysbooking=mysqli_num_rows($rssevendaysbooking);	
					$rowsevendaysbooking=mysqli_fetch_assoc($rssevendaysbooking);
					
					/*$sqlsevendaysbookinglower="SELECT SUM(SAT.AVG_qty) AS AVG_qty FROM (SELECT AVG(convert_qty_two)  AS AVG_qty FROM 
										 sauda_transaction_log WHERE SUBSTRING(sauda_date,-19,10) BETWEEN '".$dateprevioussevendays."' 
											 AND '".$curdateserver."' AND emp_code IN(".$emp_hierarchy_condition_lower.") AND 
										 product_group_code='".$product_group_code_list."' GROUP BY product_group_code,emp_code) SAT ";
					$rssevendaysbookinglower=mysqli_query($link,$sqlsevendaysbookinglower);	
					$rowsevendaysbookinglower=mysqli_fetch_assoc($rssevendaysbookinglower);
					//$contentstime++;
					$AVG_qty_sevendaysbookinglower=$rowsevendaysbookinglower['AVG_qty']+(($rowsevendaysbookinglower['AVG_qty']*10)/100);
					$AVG_qty_sevendaysbookinglower=round($AVG_qty_sevendaysbookinglower,3);*/
			
					//$trans_id='FASYS'.$contentsdate.$emp_code_immediate.$product_group_code_list;
					//$trans_id='FASYSTEM'.generate_transid($trans_id_initial);
					$AVG_qty_sevendaysbooking=$rowsevendaysbooking['AVG_qty']+(($rowsevendaysbooking['AVG_qty']*40)/100);
					//$AVG_qty_sevendaysbooking=round($AVG_qty_sevendaysbooking,3);
					
					//$balance=$AVG_qty_sevendaysbooking-$AVG_qty_sevendaysbookinglower;

					if($AVG_qty_sevendaysbooking >0)
					{
						$AVG_qty_sevendaysbooking=ceil($AVG_qty_sevendaysbooking)+1;
						/*$sql_insert_sauda_allocation = "INSERT INTO sauda_allocation SET 
														emp_code = '".$emp_code_immediate."', 
														allot_qty ='',
														product_filter_code = '".$product_group_code_list."', 
														qty = '".$AVG_qty_sevendaysbooking."',
														BAL = '".$AVG_qty_sevendaysbooking."'";
						 $sqlinsertsaudaallocationlog="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='".$emp_code_immediate."',
													   product_filter_code   ='".$product_group_code_list."',
													   qty				   ='".$AVG_qty_sevendaysbooking."'";
						if(mysqli_query($link,$sql_insert_sauda_allocation) && mysqli_query($link,$sqlinsertsaudaallocationlog))
						{
							$successvalinsertion=1;
						}
						else
						{
							$successvalinsertion=0;
						}*/
						$sql_insert_sauda_allocation_temp = "INSERT INTO sauda_allocation_temp SET 
														emp_code = '".$emp_code_immediate."', 
														product_filter_code = '".$product_group_code_list."', 
														qty = '".$AVG_qty_sevendaysbooking."'";
						mysqli_query($link,$sql_insert_sauda_allocation_temp);								
					}
					 $count++;
	 			 }
			  	 employee_hierarchy_allocation($rowemphierarchy['emp_code'],$emphierarchy);
			}
		}
	}
}
echo Populate_employee_hierarchy_allocation_final($emp_code);
function Populate_employee_hierarchy_allocation_final($emp_code)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_allocation_final($emp_code, $emphierarchy);
	return 'SUCCESS';
}
function employee_hierarchy_allocation_final($emp_code,&$emphierarchy){
	$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
	$dateprevioussevendays=date('Y-m-d', strtotime("-7 days,$curdateserver "));
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
		
	$contentsdate=$year.$month.$date;
	$contentstime=$hour.$minute.$second;
	
    $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE reporting_to='".$emp_code."' 
   					AND acedns!='N' ORDER BY emp_name ASC";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
			 $emp_code_immediate=$rowemphierarchy['emp_code'];

			 $emp_hierarchy_condition=return_employee_hierarchy($emp_code_immediate);
			 $sqlchkaccess="SELECT get_allocation FROM sauda_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
			 $rschkaccess=mysqli_query($link,$sqlchkaccess);
			 $rowchkaccess=mysqli_fetch_assoc($rschkaccess);
			 $get_allocation=$rowchkaccess['get_allocation'];
			 if($get_allocation=='yes' )
			 {
				 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master WHERE acedns='Y' ORDER BY product_group_name ASC";
				 $res_product = mysqli_query($link,$sql_product);
				 $count=1;
				  while($row_product = mysqli_fetch_assoc($res_product))
				  {
					$product_group_code_list=$row_product['product_group_code'];
					$sqlsevendaysbookingfinal="SELECT SUM(qty) AS total_qty FROM sauda_allocation_temp WHERE emp_code IN(".$emp_hierarchy_condition.") AND 
										 	product_filter_code='".$product_group_code_list."'";
					//exit();						
					$rssevendaysbookingfinal=mysqli_query($link,$sqlsevendaysbookingfinal);	
					$rowsevendaysbookingfinal=mysqli_fetch_assoc($rssevendaysbookingfinal);
					
					$trans_id='FASYS'.$contentsdate.$emp_code_immediate.$product_group_code_list;
					$AVG_qty_sevendaysbooking_final=$rowsevendaysbookingfinal['total_qty'];
					if($AVG_qty_sevendaysbooking_final >0)
					{
						$sql_insert_sauda_allocation = "INSERT INTO sauda_allocation SET 
														emp_code = '".$emp_code_immediate."', 
														allot_qty ='',
														product_filter_code = '".$product_group_code_list."', 
														qty = '".$AVG_qty_sevendaysbooking_final."',
														BAL = '".$AVG_qty_sevendaysbooking_final."'";
						 $sqlinsertsaudaallocationlog="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='".$emp_code_immediate."',
													   product_filter_code   ='".$product_group_code_list."',
													   qty				   ='".$AVG_qty_sevendaysbooking_final."'";
						if(mysqli_query($link,$sql_insert_sauda_allocation) && mysqli_query($link,$sqlinsertsaudaallocationlog))
						{
							$successvalinsertion=1;
						}
						else
						{
							$successvalinsertion=0;
						}
					}
					 $count++;
	 			 }
			  	 employee_hierarchy_allocation_final($rowemphierarchy['emp_code'],$emphierarchy);
			}
		}
	}
}

?>