<?php
	$nick_name='RKBK';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_name ASC";
		$resbranch=mysqli_query($link,$sqlbranch) or die(mysqli_error()." Error in select branch: ".$sqlbranch);
        while($rowbranch=mysqli_fetch_assoc($resbranch))
        {
			$branch_code=$rowbranch['branch_code'];
			$sqlbranchemployee="SELECT emp_code FROM employee_master WHERE branch_code='".$branch_code."' AND SUBSTRING(emp_code,1,1)!='C'";
			$resbranchemployee=mysqli_query($link,$sqlbranchemployee) or die(mysqli_error()." Error in select branch employee: ".$sqlbranchemployee);
			$branch_employee='';
			while($rowbranchemployee=mysqli_fetch_assoc($resbranchemployee))
        	{
				$emp_code=$rowbranchemployee['emp_code'];
				$branch_employee=$branch_employee."'".$emp_code."'".',';
			}
			$branch_employee=substr($branch_employee,0,-1);
			//exit();
			if($branch_employee!=''){
				$sqlrdsemployee="SELECT rds_code,emp_code FROM rds_master WHERE emp_code IN($branch_employee)";
				//$sqlrdsemployee="SELECT rds_code,emp_code FROM rds_master WHERE emp_code IN('E0033')";
				$resrdsemployee=mysqli_query($link,$sqlrdsemployee) or die(mysqli_error()." Error in select rds employee: ".$sqlrdsemployee);
				while($rowrdsemployee=mysqli_fetch_assoc($resrdsemployee))
        		{
					$rds_code=$rowrdsemployee['rds_code'];
					$rds_employee="'".$rowrdsemployee['emp_code']."'";
					
					$sqlproduct="SELECT prod_code FROM product_master WHERE prod_code IN('12332','12333') ORDER BY prod_desc ASC";	
					$resproduct=mysqli_query($link,$sqlproduct) or die(mysqli_error()." Error in select product: ".$sqlproduct);
					while($rowproduct=mysqli_fetch_assoc($resproduct))
					{
						$prod_code=$rowproduct['prod_code'];					
						$sql_branch_rds_product_stk="SELECT closing_stk FROM branch_rds_product_wise_stock WHERE branch_code='".$branch_code."' 
												AND rds_code='".$rds_code."' AND product_code='".$prod_code."'";
						$res_branch_rds_product_stk=mysqli_query($link,$sql_branch_rds_product_stk) or die(mysqli_error()." 
												Error in select branch rds product stk: ".$sql_branch_rds_product_stk);	
						$cnt_branch_rds_product_stk=mysqli_num_rows($res_branch_rds_product_stk);
						if($cnt_branch_rds_product_stk ==0)
						{											
							echo $sql_insert_branch_rds_product_opening_stk="INSERT INTO branch_rds_product_wise_stock SET 
															branch_code='".$branch_code."',
															rds_code='".$rds_code."',
															product_code='".$prod_code."',
															closing_stk='0'";
						$res_insert_branch_rds_product_opening_stk=mysqli_query($link,$sql_insert_branch_rds_product_opening_stk) or die(mysqli_error()." error
												Insert in branch rds product opening stk: ".$sql_insert_branch_rds_product_opening_stk);	
						}
					}
				}
			}
		}
	mysqli_close($link);
	echo 'SUCCESS';	
?>		