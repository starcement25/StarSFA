<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_EMAMIT");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

require("include/functions.php");
$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
$dateprevioussevendays=date('Y-m-d', strtotime("-7 days,$curdateserver "));

$reporting_to_array=array();
$sauda_allocation_transid_array=array();
$product_group_array=array();

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdate=$year.$month.$date;
$contentstime=$hour.$minute.$second;


	$sqlemplist="SELECT emp_code FROM employee_master WHERE acedns='Y' AND SUBSTRING(emp_code,1,1)!='C' ORDER BY emp_code DESC";
	$rsemplist=mysqli_query($link,$sqlemplist);
	while($rowemplist=mysqli_fetch_assoc($rsemplist))
	{
		$emp_code_list=$rowemplist['emp_code'];
		$sqlmenuaccess="SELECT not_accessible_menu FROM menu_access WHERE emp_code='".$emp_code_list."'";
		$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
		$countmenuaccess=mysqli_num_rows($rsmenuaccess);
		$menu_access_array=array();
		if($countmenuaccess >0)
		{
			while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
			{
				array_push($menu_access_array,$rowmenuaccess['not_accessible_menu']);
			}
		}
	
		if(!in_array('sauda',$menu_access_array))
		{
			$sqlimmediatelower="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code_list."' AND emp_code NOT IN(SELECT emp_code FROM menu_access WHERE not_accessible_menu='sauda')";
			$rsimmediatelower=mysqli_query($link,$sqlimmediatelower);
			$countimmediatelower=mysqli_num_rows($rsimmediatelower);
			
			if(!in_array($emp_code_list,$reporting_to_array))
			{
				if($countimmediatelower >0)
				{
					while($rowimmediatelower=mysqli_fetch_assoc($rsimmediatelower))
					{
						${immediatelower.$emp_code_list}.=$rowimmediatelower['emp_code'].",";
					}
					array_push($reporting_to_array,$emp_code_list);
				}
			}
			$sqlproductgrouplist="SELECT product_group_code FROM product_group_master ORDER BY product_group_code ASC";
			$rsproductgrouplist=mysqli_query($link,$sqlproductgrouplist);
			while($rowproductgrouplist=mysqli_fetch_assoc($rsproductgrouplist))
			{
				$product_group_code_list=$rowproductgrouplist['product_group_code'];
				$sqlsevendaysbooking="SELECT emp_code,product_group_code,AVG(convert_qty_two) AS AVG_qty FROM 
									 sauda_transaction_log WHERE SUBSTRING(sauda_date,-19,10) BETWEEN '".$dateprevioussevendays."' 
									 AND '".$curdateserver."' AND emp_code='".$emp_code_list."' AND product_group_code='".$product_group_code_list."' 
									 GROUP BY product_group_code,emp_code";
				$rssevendaysbooking=mysqli_query	($sqlsevendaysbooking);	
				$cntsevendaysbooking=mysqli_num_rows($rssevendaysbooking);	
				$rowsevendaysbooking=mysqli_fetch_assoc($rssevendaysbooking);
				$contentstime++;
		
				$trans_id='FASYSTEM'.$contentsdate.$contentstime;
				
				array_push($sauda_allocation_transid_array,$trans_id);
				array_push($product_group_array,$product_group_code_sevendaysbooking);
				if($cntsevendaysbooking >0)
				{
					$AVG_qty_sevendaysbooking=$rowsevendaysbooking['AVG_qty']+(($rowsevendaysbooking['AVG_qty']*10)/100);
				}
				else
				{
					$AVG_qty_sevendaysbooking=0;
				}
		
				$sql_insert_sauda_allocation = "INSERT INTO sauda_allocation SET 
											emp_code = '".$emp_code_list."', 
											allot_qty ='',
											product_filter_code = '".$product_group_code_list."', 
											qty = '".round($AVG_qty_sevendaysbooking,3)."',
											BAL = '".round($AVG_qty_sevendaysbooking,3)."'";
				 $sqlinsertsaudaallocationlog="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id."',
											 allocation_date	   =CURRENT_TIMESTAMP(),
											 emp_code			   ='".$emp_code_list."',
											 product_filter_code   ='".$product_group_code_list."',
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
		}
	}
	$emp_val='E0042';
	echo ${immediatelower.$emp_val};
	//exit();
	print_r($reporting_to_array);
	for($k=0;$k <count($reporting_to_array);$k++)
	{
		//if($reporting_to_array[$k]=='E0042')
		//{
				//${immediatelower.$reporting_to_array[$k]}=${immediatelower.$reporting_to_array[$k]}.$reporting_to_array[$k];
				$employee_hierarchy=return_employee_hierarchy($reporting_to_array[$k]);
				/*echo $sqlimmediatelowertotqty="SELECT SUM(qty) AS total_qty,product_filter_code FROM sauda_allocation WHERE 
									FIND_IN_SET(emp_code,'".${immediatelower.$reporting_to_array[$k]}."') GROUP BY product_filter_code";*/
				echo $sqlimmediatelowertotqty="SELECT SUM(qty) AS total_qty,product_filter_code FROM sauda_allocation_log WHERE 
									emp_code IN(".$employee_hierarchy.") AND SUBSTRING(allocation_date,1,10)='".$curdateserver."' 
									GROUP BY product_filter_code";
				//exit();										
				$rsimmediatelowertotqty=mysqli_query($link,$sqlimmediatelowertotqty);
				while($rowimmediatelowertotqty=mysqli_fetch_assoc($rsimmediatelowertotqty))
				{
					$product_filter_code=$rowimmediatelowertotqty['product_filter_code'];
					${final_total_qty.$reporting_to_array[$k].$product_filter_code}=$rowimmediatelowertotqty['total_qty'];
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
						$date=gmdate('d',strtotime('+330 minute'));
						$month=gmdate('m',strtotime('+330 minute'));
						$year=gmdate('Y',strtotime('+330 minute'));
						
						$contentstime++;
						
						$trans_id='FASYSTEM'.$contentsdate.$contentstime;
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