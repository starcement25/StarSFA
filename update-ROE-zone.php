<?php		
 set_time_limit(1000);
 /*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
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
			$sqlcustomersel="SELECT dns_customer_code,acedns FROM zone_correction_ROE  WHERE 1";
			$rscustomersel=mysqli_query($link,$sqlcustomersel);
			$updatecount=0;
			while($rowcustomersel=mysqli_fetch_assoc($rscustomersel))
			{
				$route_code_prev_string='';
				$customer_code_prev_array=array();
				$customer_name_prev_array=array();
				$dns_customer_code_prev_array=array();
				$emp_code_prev_array=array();
				$acedns_new=$rowcustomersel['acedns'];
				$dns_customer_code_new=$rowcustomersel['dns_customer_code'];
				$sqlcustomerchk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$dns_customer_code_new."'";
				$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
				$rowcustomerchk=mysqli_fetch_assoc($rscustomerchk);
				$customer_code_new=$rowcustomerchk['customer_code'];
				
				if($customer_code_new!=''){
				
					echo $sqlupdateprevcustomerroute="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 
											WHERE customer_code='".$customer_code_new."'";
					mysqli_query($link,$sqlupdateprevcustomerroute);
					$updatecount++;
					}
			   }
				
			echo $updatecount;
			exit();

?>