<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
$sqlquery="SELECT branch_code,route_code FROM branch_route_freight_test";
$result = mysqli_query($link,$sqlquery);
		while($rowcustomer = mysqli_fetch_assoc($result))
		{
			$branch_code_new=$rowcustomer['branch_code'];
			$route_code_new=$rowcustomer['route_code'];
			$sqlbranch="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_new."'";
			$rsbranch=mysqli_query($link,$sqlbranch);
			$rowbranch=mysqli_fetch_assoc($rsbranch);
			$branch_code=$rowbranch['branch_code'];
			
			$sqlroute="SELECT route_code FROM route_master WHERE route_name='".$route_code_new."'";
			$rsroute=mysqli_query($link,$sqlroute);
			$rowroute=mysqli_fetch_assoc($rsroute);
			$route_code=$rowroute['route_code'];
			
			$sqlchk="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' AND route_code='".$route_code."' AND acedns='Y'"; 
			$rschk=mysqli_query($link,$sqlchk);
			$countchk=mysqli_num_rows($rschk);
			if($countchk==0)
			{
				$sqlbranchdestinationfreight  = "insert into branch_route_freight ";
				$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";
				$sqlbranchdestinationfreight .= " ,route_code='".$route_code."'";
				$sqlbranchdestinationfreight .= " ,acedns='Y'";
				$sqlbranchdestinationfreight .= " ,freight='0'";
				$sqlbranchdestinationfreight .= " , `date`='2017-08-07'";
				echo $sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlbranchdestinationfreight);
			}
		}
			//exit();
	echo 'SUCCESS';
?>
