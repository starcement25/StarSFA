<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_HALDIRAM");
$link=mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB,$link);
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
	if(similar_file_exists("HALDIRAM clean master.csv")!=false)
	{
		$filename=similar_file_exists("HALDIRAM clean master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		/*$sqldelete="truncate branch_master";
		$rsdelete=mysqli_query($link,$sqldelete);*/
		foreach($lines as $line)
		{

			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
			
					if(!$double_coute_found && $char=="\"")
					{  
					
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
				
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;

				$customer_code=trim($data[0]);
				
				//Delete order data
				$sqlselorder="SELECT order_no FROM order_header WHERE customer_code='".$customer_code."'";
				$rsselorder=mysqli_query($link,$sqlselorder);
				$cntselorder=mysqli_num_rows($rsselorder);
				if($cntselorder>0){
					while($rowselorder=mysqli_fetch_assoc($rsselorder))
					{
						$sqldeletelocation="DELETE from location WHERE trans_id='".$rowselorder['order_no']."'";
						mysqli_query($link,$sqldeletelocation);
						echo $sqldeleteorderh="DELETE from order_header WHERE order_no='".$rowselorder['order_no']."'";
						mysqli_query($link,$sqldeleteorderh);
						$sqldeleteorderp="DELETE from order_details WHERE order_no='".$rowselorder['order_no']."'";
						mysqli_query($link,$sqldeleteorderp);
						$sqldeleteprevorder="DELETE from prev_order_counting_master WHERE order_no='".$rowselorder['order_no']."'";
						mysqli_query($link,$sqldeleteprevorder);
					}
				}
				//Delete payment data
				$sqlselpayment="SELECT receipt_id FROM payment_header WHERE customer_code='".$customer_code."'";
				$rsselpayment=mysqli_query($link,$sqlselpayment);
				$cntselpayment=mysqli_num_rows($rsselpayment);
				if($cntselpayment>0){
					while($rowselpayment=mysqli_fetch_assoc($rsselpayment))
					{
						$sqldeletelocationp="DELETE from location WHERE trans_id='".$rowselpayment['receipt_id']."'";
						mysqli_query($link,$sqldeletelocationp);
						echo $sqldeletepaymenth="DELETE from payment_header WHERE receipt_id='".$rowselpayment['receipt_id']."'";
						mysqli_query($link,$sqldeletepaymenth);
						$sqldeletepaymentp="DELETE from payment_details WHERE receipt_id='".$rowselpayment['receipt_id']."'";
						mysqli_query($link,$sqldeletepaymentp);
					}
				}
				//delete self_appraisal
				echo $sqldeleteself="DELETE from self_appraisal_customer_wise WHERE customer_code='".$customer_code."'";
			 	mysqli_query($link,$sqldeleteself);
				//delete customer and distributor route
				echo $sqldeletecustomerroute="delete from customer_route_emp_relation where customer_code='".$customer_code."'";
				mysqli_query($link,$sqldeletecustomerroute);
				echo $sqldeletedistributorroute="delete from distributor_route_relation where distributor_code='".$customer_code."'";
				mysqli_query($link,$sqldeletedistributorroute);	
				//delete customer master
				echo $sqldelcustomer="DELETE FROM customer_master WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqldelcustomer);
				
				$sqlinsertlog="INSERT INTO deleted_customer_log SET customer_code='".$customer_code."',update_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertlog);
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Branch master.csv is wrong.";
		exit();
	}*/
	
	echo 'SUCCESS';

?>