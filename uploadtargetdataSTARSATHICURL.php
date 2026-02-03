<?php
	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaat_START");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$customer_code_array=array();
	$failure_array=array();	
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='customer_code') $customer_code=$value;
			if(!in_array($customer_code,$customer_code_array))
			{
				array_push($customer_code_array,$customer_code);
			}
			
			if($key=='apr_30_target') 	  ${apr_30_target.$customer_code}=$value;
			if($key=='apr_30_achievement') ${apr_30_achievement.$customer_code}=$value;
			if($key=='may_31_target') 	  ${may_31_target.$customer_code}=$value;
			if($key=='may_31_achievement') ${may_31_achievement.$customer_code}=$value;
			if($key=='jun_30_target') 	  ${jun_30_target.$customer_code}=$value;
			if($key=='jun_30_achievement') ${jun_30_achievement.$customer_code}=$value;
			if($key=='jul_31_target') 	  ${jul_31_target.$customer_code}=$value;
			if($key=='jul_31_achievement') 	 ${jul_31_achievement.$customer_code}=$value;
			if($key=='aug_31_target') 	  ${aug_31_target.$customer_code}=$value;
			if($key=='aug_31_achievement') 	 ${aug_31_achievement.$customer_code}=$value;
			if($key=='sep_30_target') 	  ${sep_30_target.$customer_code}=$value;
			if($key=='sep_30_achievement') ${sep_30_achievement.$customer_code}=$value;
			if($key=='oct_31_target') 	  ${oct_31_target.$customer_code}=$value;
			if($key=='oct_31_achievement') 	 ${oct_31_achievement.$customer_code}=$value;
			if($key=='nov_30_target') 	  ${nov_30_target.$customer_code}=$value;
			if($key=='nov_30_achievement') 	 ${nov_30_achievement.$customer_code}=$value;
			if($key=='dec_31_target') 	  ${dec_31_target.$customer_code}=$value;
			if($key=='dec_31_achievement') 	 ${dec_31_achievement.$customer_code}=$value;
			if($key=='jan_31_target') 	 ${jan_31_target.$customer_code}=$value;
			if($key=='jan_31_achievement') 	 ${jan_31_achievement.$customer_code}=$value;
			if($key=='feb_28_target') 	 ${feb_28_target.$customer_code}=$value;
			if($key=='feb_28_achievement') 	 ${feb_28_achievement.$customer_code}=$value;
			if($key=='mar_31_target') 	 ${mar_31_target.$customer_code}=$value;
			if($key=='mar_31_achievement') 	 ${mar_31_achievement.$customer_code}=$value;
		}
	}
	foreach($customer_code_array as $customer_code_val){
			$customer_code=$customer_code_val;
			$april_target=${apr_30_target.$customer_code};
			$april_achievement=${apr_30_achievement.$customer_code};
			$may_target=${may_31_target.$customer_code};
			$may_achievement=${may_31_achievement.$customer_code};
			$june_target=${jun_30_target.$customer_code};
			$june_achievement=${jun_30_achievement.$customer_code};
			$july_target=${jul_31_target.$customer_code};
			$july_achievement=${jul_31_achievement.$customer_code};
			$august_target=${aug_31_target.$customer_code};
			$august_achievement=${aug_31_achievement.$customer_code};
			$september_target=${sep_30_target.$customer_code};
			$september_achievement=${sep_30_achievement.$customer_code};
			$october_target=${oct_31_target.$customer_code};
			$october_achievement=${oct_31_achievement.$customer_code};
			$november_target=${nov_30_target.$customer_code};
			$november_achievement= ${nov_30_achievement.$customer_code};
			$december_target=${dec_31_target.$customer_code};
			$december_achievement=${dec_31_achievement.$customer_code};
			$january_target=${jan_31_target.$customer_code};
			$january_achievement=${jan_31_achievement.$customer_code};
			$february_target=${feb_28_target.$customer_code};
			$february_achievement=${feb_28_achievement.$customer_code};
			$march_target=${mar_31_target.$customer_code};
			$march_achievement=${mar_31_achievement.$customer_code};
			
			$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_customer_wise WHERE customer_code='".$customer_code."'";
			$rschkcustomercode=mysqli_query($link,$sqlchkcustomercode);
			$countchkcustomercode=mysqli_num_rows($rschkcustomercode);
				if($countchkcustomercode ==0)
				{
					$sqlselfappraisal  = "insert into self_appraisal_customer_wise SET ";
					$sqlselfappraisal .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";
					$sqlselfappraisal .= " , emp_code=''";
					$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";
					$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";
					$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
					$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";
					$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
					$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";
					$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
					$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";
					$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
					$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";
					$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";
					$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";
					$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
					$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";
					$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";
					$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";
					$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";
					$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";
					$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
					$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";
					$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
					$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";
					$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
					$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";
					$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlselfappraisal) or array_push($failure_array,"$sqlselfappraisal");
				}
				else
				{
					$sqlselfappraisalupdate  = "update self_appraisal_customer_wise SET ";
					$sqlselfappraisalupdate .= "  apr_30_target='".mysqli_real_escape_string($april_target)."'";
					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";
					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";
					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";
					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";
					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";
					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";
					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";
					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";
					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";
					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";
					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";
					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";
					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";
					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";
					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";
					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqlselfappraisalupdate) or array_push($failure_array,"$sqlselfappraisalupdate");
				}
				//For product wise table updation
			/*$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$customer_code."'";
			$rsempcode=mysqli_query($link,$sqlempcode);
			$cntempcode=mysqli_num_rows($rsempcode);
			$rowempcode=mysqli_fetch_assoc($rsempcode);
			$emp_code=$rowempcode['emp_code'];*/
				
			$sqlchkemployee="SELECT emp_code FROM self_appraisal_product_wise  WHERE customer_code='".$customer_code."' AND prod_code='12001'";
			$rschkemployee=mysqli_query($link,$sqlchkemployee);
			$counchkemployee=mysqli_num_rows($rschkemployee);
				
				if($counchkemployee ==0)
				{
					$sqlselfappraisalprod  = "insert into self_appraisal_product_wise SET ";
					$sqlselfappraisalprod .= "   emp_code=''";
					$sqlselfappraisalprod .= " , customer_code='".mysqli_real_escape_string($customer_code)."'";
					$sqlselfappraisalprod .= " , prod_code='12001'";
					$sqlselfappraisalprod .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";
					$sqlselfappraisalprod .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";
					$sqlselfappraisalprod .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
					$sqlselfappraisalprod .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";
					$sqlselfappraisalprod .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
					$sqlselfappraisalprod .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";
					$sqlselfappraisalprod .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
					$sqlselfappraisalprod .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";
					$sqlselfappraisalprod .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
					$sqlselfappraisalprod .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";
					$sqlselfappraisalprod .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";
					$sqlselfappraisalprod .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";
					$sqlselfappraisalprod .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
					$sqlselfappraisalprod .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";
					$sqlselfappraisalprod .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";
					$sqlselfappraisalprod .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";
					$sqlselfappraisalprod .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";
					$sqlselfappraisalprod .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";
					$sqlselfappraisalprod .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
					$sqlselfappraisalprod .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";
					$sqlselfappraisalprod .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
					$sqlselfappraisalprod .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";
					$sqlselfappraisalprod .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
					$sqlselfappraisalprod .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";
					$sqlselfappraisalprod .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlselfappraisalprod) or array_push($failure_array,"$sqlselfappraisalprod");
				}
				else
				{
					$sqlselfappraisalupdateprod  = "update self_appraisal_product_wise SET ";
					$sqlselfappraisalupdateprod .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";
					$sqlselfappraisalupdateprod .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";
					$sqlselfappraisalupdateprod .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
					$sqlselfappraisalupdateprod .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";
					$sqlselfappraisalupdateprod .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
					$sqlselfappraisalupdateprod .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";
					$sqlselfappraisalupdateprod .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
					$sqlselfappraisalupdateprod .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";
					$sqlselfappraisalupdateprod .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
					$sqlselfappraisalupdateprod .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";
					$sqlselfappraisalupdateprod .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";
					$sqlselfappraisalupdateprod .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";
					$sqlselfappraisalupdateprod .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
					$sqlselfappraisalupdateprod .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";
					$sqlselfappraisalupdateprod .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";
					$sqlselfappraisalupdateprod .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";
					$sqlselfappraisalupdateprod .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";
					$sqlselfappraisalupdateprod .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";
					$sqlselfappraisalupdateprod .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
					$sqlselfappraisalupdateprod .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";
					$sqlselfappraisalupdateprod .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
					$sqlselfappraisalupdateprod .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";
					$sqlselfappraisalupdateprod .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
					$sqlselfappraisalupdateprod .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";
					$sqlselfappraisalupdateprod .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."' AND prod_code='12001'";
					mysqli_query($link,$sqlselfappraisalupdateprod) or array_push($failure_array,"$sqlselfappraisalupdateprod");
				}
				//End product wise table updation
		}
		print_r($failure_array);
		define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		define("DB","acedns_STAR");
		$link=mysqli_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
		mysqli_select_db(DB,$link) or die("could not connect the database");
		//if(count($failure_array)==0)
		//{
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='self_appraisal_customer_wise'";
		mysqli_query($link,$sqlupdate,$link);
		//}
?>
