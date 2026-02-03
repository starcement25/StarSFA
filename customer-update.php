<?php		
 set_time_limit(1000);
 ini_set('memory_limit', '-1');  
error_reporting(E_ALL ^ E_NOTICE);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
require("include/config-setup.php");
define("DB","acedns_STAR");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require("include/config-email-setup.php");
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
			$customer_code_del_prev_array=array();
			$route_code_del_prev_array=array();
			$emp_code_del_prev_array=array();
			$dns_customer_code_array=array();
	
			//------------------------------------------For customer route creation------------------------------------------------------------------
			$sqlcustomersel="SELECT DISTINCT dns_customer_code FROM customer_master ";
			$rscustomersel=mysqli_query($link,$sqlcustomersel);
			while($rowcustomersel=mysqli_fetch_assoc($rscustomersel))
			{
				$dns_customer_code_duplicate=$rowcustomersel['dns_customer_code'];
				$sqlcustomerchk="SELECT customer_code,route_code FROM customer_master WHERE dns_customer_code='".$dns_customer_code_duplicate."' ORDER BY customer_code ASC";
				$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
				${countcustomer.$dns_customer_code_duplicate}=1;
				//${customer_code_del_prev_array.$dns_customer_code_duplicate}=array();
				while($rowcustomerchk=mysqli_fetch_assoc($rscustomerchk))
				{
					if(${countcustomer.$dns_customer_code_duplicate}==1)
					{
						${route_code_present.$dns_customer_code_duplicate}=$rowcustomerchk['route_code'];
						${customer_code_present.$dns_customer_code_duplicate}=$rowcustomerchk['customer_code'];
					}
					if(${countcustomer.$dns_customer_code_duplicate}>1)
					{
						${customer_code_del_prev.$dns_customer_code_duplicate}.="'".$rowcustomerchk['customer_code']."'".',';
					}
					$sqlbacklogemp="SELECT emp_code FROM customer_master_backlog WHERE customer_code='".$rowcustomerchk['customer_code']."' AND dns_customer_code='".$dns_customer_code_duplicate."'";
					$rsbacklogemp=mysqli_query($link,$sqlbacklogemp);
					$rowbacklogemp=mysqli_fetch_assoc($rsbacklogemp);
					${emp_code_present.$dns_customer_code_duplicate}=$rowbacklogemp['emp_code'];
					
					$selcustomerroute="SELECT customer_code FROM customer_route_emp_relation WHERE 
							customer_code='".${customer_code_present.$dns_customer_code_duplicate}."' AND route_code='".${route_code_present.$dns_customer_code_duplicate}."' AND emp_code='".${emp_code_present.$dns_customer_code_duplicate}."'";
					$rscustomerroute=mysqli_query($link,$selcustomerroute);
					$cntcustomerroute=mysqli_num_rows($rscustomerroute);
					if($cntcustomerroute ==0){
					
						$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET 
												customer_code='".${customer_code_present.$dns_customer_code_duplicate}."',
												route_code='".${route_code_present.$dns_customer_code_duplicate}."',
												emp_code='".${emp_code_present.$dns_customer_code_duplicate}."',
												acedns='Y',
												download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertcustomerroute);
					}
					
					//$customer_code_prev_string.="'".$rowcustomerchk['customer_code']."'".',';
					${countcustomer.$dns_customer_code_duplicate}++;
				}
				if(!in_array($dns_customer_code_duplicate,$dns_customer_code_array))
				{
					array_push($dns_customer_code_array,$dns_customer_code_duplicate);
				}
			}
					//print_r($route_code_del_prev_array);
			for($i=0;$i<count($dns_customer_code_array);$i++)
			{
					${customer_code_del_prev.$dns_customer_code_array[$i]}=substr(${customer_code_del_prev.$dns_customer_code_array[$i]},0,-1);
					$sqlupdateorder="UPDATE order_header SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdateorder);
					$sqlupdateprevordercounting="UPDATE prev_order_counting_master SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdateprevordercounting);					
					$sqlupdatepaymentheader="UPDATE payment_header SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdatepaymentheader);
					
					$sqlupdatestockaudit="UPDATE stock_audit SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdatestockaudit);
					
					$sqlupdateroutecustomerplan="UPDATE route_customer_plan SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."'  
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdateroutecustomerplan);
					
					$sqlupdatemarketfeedback="UPDATE mf_stk_audit_header SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."'
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdatemarketfeedback);
					
					$sqlupdatemarketfeedbackone="UPDATE market_feedback SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdatemarketfeedbackone);
					
					$sqlupdateyellowcard="UPDATE yellow_card_details SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdateyellowcard);
					
					$sqlupdatecompetitorpricing="UPDATE competitor_pricing SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."' 
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdatecompetitorpricing);
					
					$sqlupdatecompetitorstock="UPDATE competitor_stock SET customer_code='".${customer_code_present.$dns_customer_code_array[$i]}."'
										WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqlupdatecompetitorstock);
					
					$sqldelpevcustomer="DELETE FROM customer_master WHERE customer_code IN(".${customer_code_del_prev.$dns_customer_code_array[$i]}.")";
					mysqli_query($link,$sqldelpevcustomer);
					$replaced_customer_code_prev_string=str_replace("'","",${customer_code_del_prev.$dns_customer_code_array[$i]});
					$sqlcustomerrefference="INSERT INTO customer_refference SET customer_replaced='".$replaced_customer_code_prev_string."',
												customer_replaced_by='".${customer_code_present.$dns_customer_code_array[$i]}."'";
					mysqli_query($link,$sqlcustomerrefference);					
					}
			echo $successval=1;
			exit();
?>