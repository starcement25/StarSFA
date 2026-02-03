<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "stradtes_tm";
$password = "X(YEr@qYsZ%b";
$db_name = "stradtes_OTM";

$conn = mysqli_connect($servername, $username, $password,$db_name);
if (mysqli_connect_errno()){
die("Failed to connect to MySQL: " . mysqli_connect_error().mysqli_connect_errno());
}

				$sqlchkpsr="SELECT TD.Net_amt,TD.dTransactionDate,CM.`Customer Code`,CM.psr_code FROM trans_data TD,customer_master CM WHERE TD.Customer_Code=CM.`Customer Code`";				
				$rschkchkpsr=mysqli_query($conn,$sqlchkpsr);
				
				$chkpsr=mysqli_num_rows($rschkchkpsr);
				$csv_row_count=$rec_count+1;
				if($chkpsr >0)
				{
					while($rowchkchkpsr=mysqli_fetch_assoc($rschkchkpsr)){
					$Net_amt=$rowchkchkpsr['Net_amt'];
					$dTransactionDate=$rowchkchkpsr['dTransactionDate'];
					$Customer_Code=$rowchkchkpsr['Customer Code'];
					$psr_code=$rowchkchkpsr['psr_code'];
					
						if(substr($dTransactionDate,3,3)=='Jan'){
						$sqlupdate="UPDATE psr_monthly_sales  SET jan=(jan+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Feb'){
						$sqlupdate="UPDATE psr_monthly_sales  SET feb=(feb+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Mar'){
						$sqlupdate="UPDATE psr_monthly_sales  SET mar=(mar+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Apr'){
						$sqlupdate="UPDATE psr_monthly_sales  SET apr=(apr+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='May'){
						$sqlupdate="UPDATE psr_monthly_sales  SET may=(may+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Jun'){
						$sqlupdate="UPDATE psr_monthly_sales  SET jun=(jun+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Jul'){
						$sqlupdate="UPDATE psr_monthly_sales  SET jul=(jul+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Aug'){
						$sqlupdate="UPDATE psr_monthly_sales  SET aug=(aug+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Sep'){
						$sqlupdate="UPDATE psr_monthly_sales  SET sep=(sep+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Oct'){
						$sqlupdate="UPDATE psr_monthly_sales  SET oct=(oct+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Nov'){
						$sqlupdate="UPDATE psr_monthly_sales  SET nov=(nov+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
						if(substr($dTransactionDate,3,3)=='Dec'){
						$sqlupdate="UPDATE psr_monthly_sales  SET `dec`=(`dec`+$Net_amt) WHERE FIND_IN_SET(psr_code,'".$psr_code."')";
						mysqli_query($conn,$sqlupdate);
						}
					}
					$sqlround="UPDATE psr_monthly_sales SET `jan`=ROUND(`jan`,2),`feb`=ROUND(`feb`,2),`mar`=ROUND(`mar`,2),`apr`=ROUND(`apr`,2),`may`=ROUND(`may`,2),`jun`=ROUND(`jun`,2),`jul`=ROUND(`jul`,2), `aug`=ROUND(`aug`,2),`sep`=ROUND(`sep`,2),`oct`=ROUND(`oct`,2),`nov`=ROUND(`nov`,2),`dec`=ROUND(`dec`,2)";
					mysqli_query($conn,$sqlround);
				}
?>