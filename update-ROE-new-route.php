<?php		
 set_time_limit(1000);
 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db($link,DB) or die("could not connect the database for invalid nick name");
			$customer_code_del_prev_array=array();
			$route_code_del_prev_array=array();
			$emp_code_del_prev_array=array();
	
			//------------------------------------------For customer route creation------------------------------------------------------------------
			echo $sqlcustomersel="SELECT customer_code,route_code,route_name FROM ROE_route_update  WHERE 1  order by sl_no ASC";
			$rscustomersel=mysqli_query($link,$sqlcustomersel);
			$updatecount=1;
			while($rowcustomersel=mysqli_fetch_assoc($rscustomersel))
			{
				$route_code_prev_string='';
				$customer_code_prev_array=array();
				$customer_name_prev_array=array();
				$dns_customer_code_prev_array=array();
				$emp_code_prev_array=array();
				$dns_route_code_new=$rowcustomersel['route_code'];
				$dns_customer_code_new=$rowcustomersel['customer_code'];
				$route_name_new=$rowcustomersel['route_name'];
				$sqlcustomerchk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$dns_customer_code_new."'";
				$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
				$rowcustomerchk=mysqli_fetch_assoc($rscustomerchk);
				$customer_code_new=$rowcustomerchk['customer_code'];
				echo $sqlroutechk="SELECT route_code FROM route_master WHERE dns_route_code='".$dns_route_code_new."'";
				$rsroutechk=mysqli_query($link,$sqlroutechk);
				$countroutechk=mysqli_num_rows($rsroutechk);
				if($countroutechk<1)
						{
							$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
							$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
							$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
							$new_route_code=$rowmaxroutecode['new_route_code'];
							if($new_route_code=='')
							{
								$max_route_code='RT/1';
							}
							else
							{
								$max_route_code='RT/'.($new_route_code+1);
								//$max_route_code++;
							}
							$sqlroute  = "insert into route_master ";
							$sqlroute .= " SET route_code='".$max_route_code."'";
							$sqlroute .= " ,dns_route_code='".$dns_route_code_new."'";
							$sqlroute .= " ,route_name='".$route_name_new."'";
							$sqlroute .= " , emp_code=''";
							$sqlroute .= " ,branch_code=''";
							$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

							mysqli_query($link,$sqlroute);
				//$rowroutechk=mysqli_fetch_assoc($rsroutechk);
				//$route_code_new=$rowroutechk['route_code'];
					$route_code_new=$max_route_code;	

				}
				else{
					$rowroutechk=mysqli_fetch_assoc($rsroutechk);
				   $route_code_new=$rowroutechk['route_code'];
				}
				if($customer_code_new!='' && $route_code_new!=''){
				$sqlselcustomerprevroute="SELECT customer_code,emp_code,route_code FROM customer_route_emp_relation WHERE 
											acedns='Y' AND customer_code='".$customer_code_new."'";
							//exit();
				$rsselcustomerprevroute=mysqli_query($link,$sqlselcustomerprevroute);
				while($rowselcustomerprevroute=mysqli_fetch_assoc($rsselcustomerprevroute))
				{
					$emp_code_previous=$rowselcustomerprevroute['emp_code'];
					$sqlchkprevcustroute="SELECT customer_code FROM customer_route_emp_relation WHERE 
											acedns='Y' AND customer_code='".$customer_code_new."' AND emp_code='".$emp_code_previous."' AND route_code='".$route_code_new."'";
					$rschkprevcustroute=mysqli_query($link,$sqlchkprevcustroute);
					$countchkprevcustroute=mysqli_num_rows($rschkprevcustroute);
					if($countchkprevcustroute==0){
					echo $sqlupdateprevcustomerroute="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 
											WHERE customer_code='".$customer_code_new."' and emp_code='".$emp_code_previous."'";
					mysqli_query($link,$sqlupdateprevcustomerroute);
					echo $sqlinsertcustomerroute="INSERT INTO 
											customer_route_emp_relation SET 		    					customer_code='".$customer_code_new."',
											route_code='".$route_code_new."',
											emp_code='".$emp_code_previous."',
											acedns='Y',
											download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertcustomerroute);
					}
			   }
				$updatecount++;
				}
			}
			echo $updatecount;
			exit();

?>