<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_EMAMIT");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

function Populate_employee_hierarchy_booking($emp_code)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_booking($emp_code, $emphierarchy);
	return $emphierarchyhtmlstring;
}
function employee_hierarchy_booking($emp_code,&$emphierarchy){
  
   $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE reporting_to='".$emp_code."' 
   					AND acedns!='N' ORDER BY emp_name ASC";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		$count=1;
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
			 $emp_code_immediate=$rowemphierarchy['emp_code'];
			/* $sqlchkaccess="SELECT get_allocation FROM sauda_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
			 $rschkaccess=mysqli_query($link,$sqlchkaccess);
			 $rowchkaccess=mysqli_fetch_assoc($rschkaccess);
			 $get_allocation=$rowchkaccess['get_allocation'];
			 if($get_allocation=='yes' )
			 {
				 $emp_name=$rowemphierarchy['emp_name'];
				 $emphierarchyhtml="<tr>";
				 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"width:27%;padding-left:".$padding."px;\">".$count.'.'.$emp_name."</td>";
				 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master PGM WHERE 1 ".$condition_one." ORDER BY product_group_name ASC ";
				 $res_product = mysqli_query($link,$sql_product);
				  while($row_product = mysqli_fetch_assoc($res_product))
				  {
					   $product_group_code=$row_product['product_group_code'];
					   if($reporting_to_hierarchy=='')
					   {
						   $sql_sauda_details="SELECT SUM(qty) AS qty FROM sauda_allocation WHERE product_filter_code ='".$product_group_code."' 
						   					AND emp_code IN(SELECT emp_code FROM employee_master WHERE reporting_to='".$rowemphierarchy['emp_code']."')";
					   }
					   else
					   {
						   $sql_sauda_details = "SELECT qty FROM sauda_allocation_log WHERE emp_code='".$rowemphierarchy['emp_code']."' AND 
												product_filter_code='".$product_group_code."' AND DATE_FORMAT(SUBSTRING(allocation_date,1,10),'%Y%m%d') 
												LIKE '%$today%' ORDER BY allocation_date DESC LIMIT 0,1";
					   }
					   $res_sauda_details = mysqli_query($link,$sql_sauda_details);
					   $row_sauda_details = mysqli_fetch_assoc($res_sauda_details);
		  			   $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$product_group_code\" align=\"right\" width=\"$td_width%\">$row_sauda_details[qty]</td>";
	 			 }
	  		  $emphierarchyhtml.="<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"GenericAjaxFunction('add_edit_sauda.php?mode=edit&emp_code=$rowemphierarchy[emp_code]','edit_form',0);\" width=\"3%\">Edit</a></td></tr>";
			  echo $emphierarchyhtml.="</tr>";*/
			  employee_hierarchy_booking($rowemphierarchy['emp_code'],$emphierarchy);
			  $count++;
			//}
		}
	}
}
$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
$dateprevioussevendays=date('Y-m-d', strtotime("-7 days,$curdateserver "));

$reporting_to_array=array();
$sauda_allocation_transid_array=array();
$product_group_array=array();
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentstime=$hour.$minute.$second;

$sqlsevendaysbooking="SELECT emp_code,product_group_code,AVG(convert_qty_two) AS AVG_qty FROM  sauda_transaction_log 
WHERE SUBSTRING(sauda_date,-19,10) BETWEEN '".$dateprevioussevendays."' AND '".$curdateserver."' GROUP BY product_group_code,emp_code";

$rssevendaysbooking=mysqli_query	($sqlsevendaysbooking);		
while($rowsevendaysbooking=mysqli_fetch_assoc($rssevendaysbooking))
	{
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$contentsdate=$year.$month.$date;
		$contentstime++;

		$trans_id='FASYSTEM'.$contentsdate.$contentstime;

		$emp_sevendaysbooking=$rowsevendaysbooking['emp_code'];
		$product_group_code_sevendaysbooking=$rowsevendaysbooking['product_group_code'];
		
		$sqlimmediatelower="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_sevendaysbooking."'";
		$rsimmediatelower=mysqli_query($link,$sqlimmediatelower);
		$countimmediatelower=mysqli_num_rows($rsimmediatelower);

		if(!in_array($emp_sevendaysbooking,$reporting_to_array))
		{
			if($countimmediatelower >0)
			{
				while($rowimmediatelower=mysqli_fetch_assoc($rsimmediatelower))
				{
					${immediatelower.$emp_sevendaysbooking}.=$rowimmediatelower['emp_code'].",";
				}
				array_push($reporting_to_array,$emp_sevendaysbooking);
			}
		}
		array_push($sauda_allocation_transid_array,$trans_id);
		array_push($product_group_array,$product_group_code_sevendaysbooking);
		/*if($countimmediatelower >0)
		{
			$AVG_qty_sevendaysbooking=$rowsevendaysbooking['AVG_qty'];
		}
		else
		{*/
			$AVG_qty_sevendaysbooking=$rowsevendaysbooking['AVG_qty']+(($rowsevendaysbooking['AVG_qty']*10)/100);
		//}

		$sql_insert_sauda_allocation = "INSERT INTO sauda_allocation SET 
							emp_code = '".$emp_sevendaysbooking."', 
							allot_qty ='',
							product_filter_code = '".$product_group_code_sevendaysbooking."', 
							qty = '".round($AVG_qty_sevendaysbooking,3)."',
							BAL = '".round($AVG_qty_sevendaysbooking,3)."'";
		
		if($countimmediatelower ==0)
		{
			$sqlinsertsaudaallocationlog="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id."',
									 allocation_date	   =CURRENT_TIMESTAMP(),
									 emp_code			   ='".$emp_sevendaysbooking."',
									 product_filter_code   ='".$product_group_code_sevendaysbooking."',
									 qty				   ='".round($AVG_qty_sevendaysbooking,3)."'";
			if(mysqli_query($link,$sql_insert_sauda_allocation) && mysqli_query($link,$sqlinsertsaudaallocationlog))
			{
				$successvalinsertion=1;
			}
			else
			{
				$successvalinsertion=0;
			}
		}
		else
		{
			if(mysqli_query($link,$sql_insert_sauda_allocation))
			{
				$successvalinsertion=1;
			}
			else
			{
				$successvalinsertion=0;
			}
		}
	}
	//$emp_val='E0027';
	//echo ${immediatelower.$emp_val};
	for($k=0;$k <count($reporting_to_array);$k++)
	{
		//if($reporting_to_array[$k]=='E0027')
		//{
				${immediatelower.$reporting_to_array[$k]}=${immediatelower.$reporting_to_array[$k]}.$reporting_to_array[$k];
				$sqlimmediatelowertotqty="SELECT SUM(qty) AS total_qty,product_filter_code FROM sauda_allocation WHERE 
									FIND_IN_SET(emp_code,'".${immediatelower.$reporting_to_array[$k]}."') GROUP BY product_filter_code";
				$rsimmediatelowertotqty=mysqli_query($link,$sqlimmediatelowertotqty);
				while($rowimmediatelowertotqty=mysqli_fetch_assoc($rsimmediatelowertotqty))
				{
					$product_filter_code=$rowimmediatelowertotqty['product_filter_code'];
					${final_total_qty.$reporting_to_array[$k].$product_filter_code}=$rowimmediatelowertotqty['total_qty'];
					//exit();
					$sqlsaudaallocationcheck="SELECT product_filter_code,qty FROM sauda_allocation WHERE emp_code='".$reporting_to_array[$k]."' 
											AND product_filter_code='".$product_filter_code."'";
					$rssaudaallocationcheck=mysqli_query($link,$sqlsaudaallocationcheck);
					$cntsaudaallocationcheck=mysqli_num_rows($rssaudaallocationcheck);
					if($cntsaudaallocationcheck > 0)
					{				
						$rowsaudaallocationcheck=mysqli_fetch_assoc($rssaudaallocationcheck);
						$sql_update_sauda_allocation="UPDATE sauda_allocation SET qty = '".round(${final_total_qty.$reporting_to_array[$k].$product_filter_code},3)."',
													BAL = '".round(${final_total_qty.$reporting_to_array[$k].$product_filter_code},3)."' 
													WHERE emp_code='".$reporting_to_array[$k]."' AND product_filter_code='".$product_filter_code."'";
						if(mysqli_query($link,$sql_update_sauda_allocation))
						{
							$successvalupdation=1;
						}
						else
						{
							$successvalupdation=0;
						}
				  	} //End of if for existance of data in sauda allocation
					else
					{
						$sql_insert_sauda_allocation_upper = "INSERT INTO sauda_allocation SET 
													emp_code = '".$reporting_to_array[$k]."', 
													allot_qty ='',
													product_filter_code = '".$product_filter_code."', 
													qty = '".round(${final_total_qty.$reporting_to_array[$k].$product_filter_code},3)."',
													BAL = '".round(${final_total_qty.$reporting_to_array[$k].$product_filter_code},3)."'";
						if(mysqli_query($link,$sql_insert_sauda_allocation_upper))
						{
							$successvalinsertion=1;
						}
						else
						{
							$successvalinsertion=0;
						}
					}//End of else for not existance of data in sauda allocation
					$date=gmdate('d',strtotime('+330 minute'));
					$month=gmdate('m',strtotime('+330 minute'));
					$year=gmdate('Y',strtotime('+330 minute'));
					$contentstime++;
					$trans_id='FASYSTEM'.$contentsdate.$contentstime;
					$sqlinsertsaudaallocationlogupper="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id."',
													 allocation_date	   =CURRENT_TIMESTAMP(),
													 emp_code			   ='".$reporting_to_array[$k]."',
													 product_filter_code   ='".$product_filter_code."',
													 qty				   ='".round(${final_total_qty.$reporting_to_array[$k].$product_filter_code},3)."'";
				  if(mysqli_query($link,$sqlinsertsaudaallocationlogupper))
					{
						$successvalinsertion=1;
					}
					else
					{
						$successvalinsertion=0;
					}									 
			}//End of While loop
		//}
	}//End of for loop
	if($successvalinsertion==1)   echo 'Success';
	else						  echo 'Failure';
?>