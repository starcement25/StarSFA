<?php
$type ='remortdb';
	include "saathi_connection.php";
//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_emp_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_employee_code') $dns_employee_code=$value;
			if(!in_array($dns_employee_code,$dns_emp_code_array))
			{
				array_push($dns_emp_code_array,$dns_employee_code);
			}
			if($key=='employee_name')   	 ${'employee_name'.$dns_employee_code}=$value;
			if($key=='branch_code_name')  ${'branch_code_name'.$dns_employee_code}=$value;
			if($key=='reporting_to')  		 ${'reporting_to'.$dns_employee_code}=$value;
			if($key=='email')  		${'email'.$dns_employee_code}=$value;
			if($key=='phone_no')  		  ${'phone_no'.$dns_employee_code}=$value;
			if($key=='sale_access')  		  ${'sale_access'.$dns_employee_code}=$value;
			if($key=='designation')  		  ${'designation'.$dns_employee_code}=$value;
			if($key=='state')  		  ${'state'.$dns_employee_code}=$value;
			if($key=='zone')  		  ${'zone'.$dns_employee_code}=$value;
			if($key=='acedns')  		  ${'acedns'.$dns_employee_code}=$value;
			if($key=='district')  		  ${'district'.$dns_employee_code}=$value;
			if($key=='level')  		  ${'level'.$dns_employee_code}=$value;
			if($key=='region')  		  ${'region'.$dns_employee_code}=$value;
		}
	}
	foreach($dns_emp_code_array as $dns_emp_code_val){
		$dns_employee_code=$dns_emp_code_val;
		$employee_name=${'employee_name'.$dns_emp_code_val};
		$branch_code_name=${'branch_code_name'.$dns_emp_code_val};
		$reporting_to=${'reporting_to'.$dns_emp_code_val};
		$email=${'email'.$dns_emp_code_val};
		$phone_no=${'phone_no'.$dns_emp_code_val};
		$sale_access=${'sale_access'.$dns_emp_code_val};
		$designation=${'designation'.$dns_emp_code_val};
		$state=${'state'.$dns_emp_code_val};
		$zone=${'zone'.$dns_emp_code_val};
		$acedns=${'acedns'.$dns_emp_code_val};
		$district=${'district'.$dns_emp_code_val};
		$level=${'level'.$dns_emp_code_val};
		$region=${'region'.$dns_emp_code_val};
		
		$reporting_to_val='';
		
		if(strpos($reporting_to,';')!=false)
		 {
			$reporting_to=str_replace(';',',',$reporting_to);
		 }
		if($reporting_to!='')
		{
			$reporting_to=str_replace(', ',',',$reporting_to);

			$reporting_val_array=explode(',',$reporting_to);

			foreach($reporting_val_array as $reporting_val)
			{
				$sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE dns_emp_code = '".ltrim($reporting_val)."'";
					$res_check_emp_code = mysqli_query($link,$sql_check_emp_code);
				$total_rows_emp_code = mysqli_num_rows($res_check_emp_code);
				$reporting_not_exists='';
				if($total_rows_emp_code == 0)
				{
						$reporting_not_exists=$dns_employee_code.'#'.$reporting_val;
						array_push($reporting_to_array,$reporting_not_exists);
				}
			}
		}
		
		$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";	
		$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code,'".$reporting_to."')";
		$rsbranchcode=mysqli_query($link,$sqlbranchcode);
		while($rowbranchcode=mysqli_fetch_assoc($rsbranchcode))
		{
			$branch_code=$branch_code.$rowbranchcode['branch_code'].',';
		}
		$branch_code=substr($branch_code,0,-1);
		$rsreportingto=mysqli_query($link,$sqlreportingto);
		while($rowreportingto=mysqli_fetch_assoc($rsreportingto))
		{
			$reporting_to_val=$reporting_to_val.$rowreportingto['emp_code'].',';
		}
		$reporting_to_val=substr($reporting_to_val,0,-1);
		$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE dns_emp_code='".$dns_employee_code."'";
		$rsempnamechk=mysqli_query($link,$sqlempnamechk);
		$countempnamechk=mysqli_num_rows($rsempnamechk);
		$csv_row_count=$rec_count+1;
		if($countempnamechk<1)
		{
			$sqlmaxempcode="SELECT MAX(emp_code) AS max_emp_code FROM  employee_master ";
			$rsmaxempcode=mysqli_query($link,$sqlmaxempcode);
			$rowmaxempcode=mysqli_fetch_assoc($rsmaxempcode);
			$max_emp_code=$rowmaxempcode['max_emp_code'];
						if($max_emp_code=='')
						{
							$max_emp_code='E0001';
						}
						else
						{
							$max_emp_code++;
						}
						if($level==""){
						    $level="0";
						}
						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						$sql .= " , dns_emp_code='".$dns_employee_code."'";
						$sql .= " , emp_name='".ltrim(addslashes($employee_name))."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , reporting_to='".$reporting_to_val."'";
						$sql .= " , email='".addslashes($email)."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , sale_access='".$sale_access."'";
						$sql .= " , designation='".$designation."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , state='".$state."'";
						$sql .= " , zone='".$zone."'";
						$sql .= " , level='".$level."'";
						$sql .= " , region='".$region."'";
						$sql .= " , District='".$district."'".$sqllevel.$sqlinsertcond;
						$sql .= " , acedns_changed_date=CURRENT_TIMESTAMP()";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
//echo $sql;
						mysqli_query($link,$sql);
		}
					else

					{
						$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);
						$emp_code_db=$rowempnamechk['emp_code'];
						$acedns_db=$rowempnamechk['acedns'];

						$sqlupdate  = "UPDATE employee_master ";
						$sqlupdate .= " SET branch_code='".$branch_code."'";
						$sqlupdate .= " , emp_name='".ltrim(addslashes($employee_name))."'";
						$sqlupdate .= " , dns_emp_code='".ltrim(addslashes($dns_employee_code))."'";
						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";
						$sqlupdate .= " , email='".$email."'";
						$sqlupdate .= " , sale_access='".$sale_access."'";
						$sqlupdate .= " , designation='".$designation."'";
						$sqlupdate .= " , acedns='".$acedns."'";
						$sqlupdate .= " , state='".$state."'";
						$sqlupdate .= " , zone='".$zone."'";
						$sqlupdate .= " , level='".$level."'";
						$sqlupdate .= " , region='".$region."'";
						$sqlupdate .= " , District='".$district."'".$sqlinsertcond;
						$sqlupdate .= " , download_time=CURRENT_TIMESTAMP()";
						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_code='".addslashes($emp_code_db)."'";

						mysqli_query($link,$sqlupdate);

	
	}
	}
mysqli_close($link);
		$type ='localdb';
		include "saathi_connection.php";
		//mysqli_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='employee_master'";
		mysqli_query($linksource,$sqlupdate);
mysqli_close($linksource);
?>
