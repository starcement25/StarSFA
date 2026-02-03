<?php
	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaat_START");
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die("could not connect the database");
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
			if($key=='apr_prev_y_target') ${apr_prev_y_target.$customer_code}=$value;
			if($key=='apr_prev_y_achievement') ${apr_prev_y_achievement.$customer_code}=$value;
			if($key=='may_31_target') 	  ${may_31_target.$customer_code}=$value;
			if($key=='may_31_achievement') ${may_31_achievement.$customer_code}=$value;
			if($key=='may_prev_y_target') ${may_prev_y_target.$customer_code}=$value;
			if($key=='may_prev_y_achievement') ${may_prev_y_achievement.$customer_code}=$value;
			if($key=='jun_30_target') 	  ${jun_30_target.$customer_code}=$value;
			if($key=='jun_30_achievement') ${jun_30_achievement.$customer_code}=$value;
			if($key=='jun_prev_y_target')  ${jun_prev_y_target.$customer_code}=$value;
			if($key=='jun_prev_y_achievement') ${jun_prev_y_achievement.$customer_code}=$value;
			if($key=='jul_31_target') 	  ${jul_31_target.$customer_code}=$value;
			if($key=='jul_31_achievement') ${jul_31_achievement.$customer_code}=$value;
			if($key=='jul_prev_y_target') ${jul_prev_y_target.$customer_code}=$value;
			if($key=='jul_prev_y_achievement') ${jul_prev_y_achievement.$customer_code}=$value;
			if($key=='aug_31_target') 	  ${aug_31_target.$customer_code}=$value;
			if($key=='aug_31_achievement') ${aug_31_achievement.$customer_code}=$value;
			if($key=='aug_prev_y_target')  ${aug_prev_y_target.$customer_code}=$value;
			if($key=='aug_prev_y_achievement') ${aug_prev_y_achievement.$customer_code}=$value;
			if($key=='sep_30_target') 	  ${sep_30_target.$customer_code}=$value;
			if($key=='sep_30_achievement') ${sep_30_achievement.$customer_code}=$value;
			if($key=='sep_prev_y_target')  ${sep_prev_y_target.$customer_code}=$value;
			if($key=='sep_prev_y_achievement') ${sep_prev_y_achievement.$customer_code}=$value;
			if($key=='oct_31_target') 	  ${oct_31_target.$customer_code}=$value;
			if($key=='oct_31_achievement') ${oct_31_achievement.$customer_code}=$value;
			if($key=='oct_prev_y_target')  ${oct_prev_y_target.$customer_code}=$value;
			if($key=='oct_prev_y_achievement') ${oct_prev_y_achievement.$customer_code}=$value;
			if($key=='nov_30_target') 	  ${nov_30_target.$customer_code}=$value;
			if($key=='nov_30_achievement') 	 ${nov_30_achievement.$customer_code}=$value;
			if($key=='nov_prev_y_target')  ${nov_prev_y_target.$customer_code}=$value;
			if($key=='nov_prev_y_achievement') ${nov_prev_y_achievement.$customer_code}=$value;
			if($key=='dec_31_target') 	  ${dec_31_target.$customer_code}=$value;
			if($key=='dec_31_achievement') 	 ${dec_31_achievement.$customer_code}=$value;
			if($key=='dec_prev_y_target')  ${dec_prev_y_target.$customer_code}=$value;
			if($key=='dec_prev_y_achievement') ${dec_prev_y_achievement.$customer_code}=$value;
			if($key=='jan_31_target') 	 ${jan_31_target.$customer_code}=$value;
			if($key=='jan_31_achievement') 	 ${jan_31_achievement.$customer_code}=$value;
			if($key=='jan_prev_y_target')  ${jan_prev_y_target.$customer_code}=$value;
			if($key=='jan_prev_y_achievement') ${jan_prev_y_achievement.$customer_code}=$value;
			if($key=='feb_28_target') 	 ${feb_28_target.$customer_code}=$value;
			if($key=='feb_28_achievement') 	 ${feb_28_achievement.$customer_code}=$value;
			if($key=='feb_prev_y_target')  ${feb_prev_y_target.$customer_code}=$value;
			if($key=='feb_prev_y_achievement') ${feb_prev_y_achievement.$customer_code}=$value;
			if($key=='mar_31_target') 	 ${mar_31_target.$customer_code}=$value;
			if($key=='mar_31_achievement') 	 ${mar_31_achievement.$customer_code}=$value;
			if($key=='mar_prev_y_target')  ${mar_prev_y_target.$customer_code}=$value;
			if($key=='mar_prev_y_achievement') ${mar_prev_y_achievement.$customer_code}=$value;
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
			$april_prev_target=${apr_prev_y_target.$customer_code};
			$april_prev_achievement=${apr_prev_y_achievement.$customer_code};
			$may_prev_target=${may_prev_y_target.$customer_code};
			$may_prev_achievement=${may_prev_y_achievement.$customer_code};
			$june_prev_target=${jun_prev_y_target.$customer_code};
			$june_prev_achievement=${jun_prev_y_achievement.$customer_code};;
			$july_prev_target=${jul_prev_y_target.$customer_code};
			$july_prev_achievement=${jul_prev_y_achievement.$customer_code};
			$august_prev_target=${aug_prev_y_target.$customer_code};
			$august_prev_achievement=${aug_prev_y_achievement.$customer_code};
			$september_prev_target=${sep_prev_y_target.$customer_code};
			$september_prev_achievement=${sep_prev_y_achievement.$customer_code};
			$october_prev_target=${oct_prev_y_target.$customer_code};
			$october_prev_achievement=${oct_prev_y_achievement.$customer_code};
			$november_prev_target=${nov_prev_y_target.$customer_code};
			$november_prev_achievement=${nov_prev_y_achievement.$customer_code};
			$december_prev_target=${dec_prev_y_target.$customer_code};
			$december_prev_achievement=${dec_prev_y_achievement.$customer_code};
			$january_prev_target=${jan_prev_y_target.$customer_code};
			$january_prev_achievement=${jan_prev_y_achievement.$customer_code};
			$february_prev_target=${feb_prev_y_target.$customer_code};
			$february_prev_achievement=${feb_prev_y_achievement.$customer_code};
			$march_prev_target=${mar_prev_y_target.$customer_code};
			$march_prev_achievement=${mar_prev_y_achievement.$customer_code};
			

			/*$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_customer_wise WHERE customer_code='".$customer_code."'";

			$rschkcustomercode=mysql_query($sqlchkcustomercode);

			$countchkcustomercode=mysql_num_rows($rschkcustomercode);

				if($countchkcustomercode ==0)

				{

					$sqlselfappraisal  = "insert into self_appraisal_customer_wise SET ";
					$sqlselfappraisal .= "   customer_code='".mysql_real_escape_string($customer_code)."'";
				$sqlselfappraisal .= " , apr_30_target='".mysql_real_escape_string($april_target)."'";
				$sqlselfappraisal .= " , apr_30_achievement='".mysql_real_escape_string($april_achievement)."'";
				$sqlselfappraisal .= " , apr_prev_y_target='".mysql_real_escape_string($april_prev_target)."'";
				$sqlselfappraisal .= " , apr_prev_y_achievement='".mysql_real_escape_string($april_prev_achievement)."'";
				$sqlselfappraisal .= " , may_31_target='".mysql_real_escape_string($may_target)."'";
				$sqlselfappraisal .= " , may_31_achievement='".mysql_real_escape_string($may_achievement)."'";
				$sqlselfappraisal .= " , may_prev_y_target='".mysql_real_escape_string($may_prev_target)."'";
				$sqlselfappraisal .= " , may_prev_y_achievement='".mysql_real_escape_string($may_prev_achievement)."'";
				$sqlselfappraisal .= " , jun_30_target='".mysql_real_escape_string($june_target)."'";
				$sqlselfappraisal .= " , jun_30_achievement='".mysql_real_escape_string($june_achievement)."'";
				$sqlselfappraisal .= " , jun_prev_y_target='".mysql_real_escape_string($june_prev_target)."'";
				$sqlselfappraisal .= " , jun_prev_y_achievement='".mysql_real_escape_string($june_prev_achievement)."'";
				$sqlselfappraisal .= " , jul_31_target='".mysql_real_escape_string($july_target)."'";
				$sqlselfappraisal .= " , jul_31_achievement='".mysql_real_escape_string($july_achievement)."'";
				$sqlselfappraisal .= " , jul_prev_y_target='".mysql_real_escape_string($july_prev_target)."'";
				$sqlselfappraisal .= " , jul_prev_y_achievement='".mysql_real_escape_string($july_prev_achievement)."'";
				$sqlselfappraisal .= " , aug_31_target='".mysql_real_escape_string($august_target)."'";
				$sqlselfappraisal .= " , aug_31_achievement='".mysql_real_escape_string($august_achievement)."'";
				$sqlselfappraisal .= " , aug_prev_y_target='".mysql_real_escape_string($august_prev_target)."'";
				$sqlselfappraisal .= " , aug_prev_y_achievement='".mysql_real_escape_string($august_prev_achievement)."'";
				$sqlselfappraisal .= " , sep_30_target='".mysql_real_escape_string($september_target)."'";
				$sqlselfappraisal .= " , sep_30_achievement='".mysql_real_escape_string($september_achievement)."'";
				$sqlselfappraisal .= " , sep_prev_y_target='".mysql_real_escape_string($september_prev_target)."'";
				$sqlselfappraisal .= " , sep_prev_y_achievement='".mysql_real_escape_string($september_prev_achievement)."'";
				$sqlselfappraisal .= " , oct_31_target='".mysql_real_escape_string($october_target)."'";
				$sqlselfappraisal .= " , oct_31_achievement='".mysql_real_escape_string($october_achievement)."'";
				$sqlselfappraisal .= " , oct_prev_y_target='".mysql_real_escape_string($october_prev_target)."'";
				$sqlselfappraisal .= " , oct_prev_y_achievement='".mysql_real_escape_string($october_prev_achievement)."'";
				$sqlselfappraisal .= " , nov_30_target='".mysql_real_escape_string($november_target)."'";
				$sqlselfappraisal .= " , nov_30_achievement='".mysql_real_escape_string($november_achievement)."'";
				$sqlselfappraisal .= " , nov_prev_y_target='".mysql_real_escape_string($november_prev_target)."'";
				$sqlselfappraisal .= " , nov_prev_y_achievement='".mysql_real_escape_string($november_prev_achievement)."'";
				$sqlselfappraisal .= " , dec_31_target='".mysql_real_escape_string($december_target)."'";
				$sqlselfappraisal .= " , dec_31_achievement='".mysql_real_escape_string($december_achievement)."'";
				$sqlselfappraisal .= " , dec_prev_y_target='".mysql_real_escape_string($december_prev_target)."'";
				$sqlselfappraisal .= " , dec_prev_y_achievement='".mysql_real_escape_string($december_prev_achievement)."'";
				$sqlselfappraisal .= " , jan_31_target='".mysql_real_escape_string($january_target)."'";
				$sqlselfappraisal .= " , jan_31_achievement='".mysql_real_escape_string($january_achievement)."'";
				$sqlselfappraisal .= " , jan_prev_y_target='".mysql_real_escape_string($january_prev_target)."'";
				$sqlselfappraisal .= " , jan_prev_y_achievement='".mysql_real_escape_string($january_prev_achievement)."'";
				$sqlselfappraisal .= " , feb_28_target='".mysql_real_escape_string($february_target)."'";
				$sqlselfappraisal .= " , feb_28_achievement='".mysql_real_escape_string($february_achievement)."'";
				$sqlselfappraisal .= " , feb_prev_y_target='".mysql_real_escape_string($february_prev_target)."'";
				$sqlselfappraisal .= " , feb_prev_y_achievement='".mysql_real_escape_string($february_prev_achievement)."'";

				$sqlselfappraisal .= " , mar_31_target='".mysql_real_escape_string($march_target)."'";
				$sqlselfappraisal .= " , mar_31_achievement='".mysql_real_escape_string($march_achievement)."'";
				$sqlselfappraisal .= " , mar_prev_y_target='".mysql_real_escape_string($march_prev_target)."'";
				$sqlselfappraisal .= " , mar_prev_y_achievement='".mysql_real_escape_string($march_prev_achievement)."'";
					$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

					mysql_query($sqlselfappraisal) or array_push($failure_array,"$sqlselfappraisal");
				}
				else
				{
					$sqlselfappraisalupdate  = "update self_appraisal_customer_wise SET ";
					$sqlselfappraisalupdate .= " apr_30_target='".mysql_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysql_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_target='".mysql_real_escape_string($april_prev_target)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_achievement='".mysql_real_escape_string($april_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysql_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysql_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_target='".mysql_real_escape_string($may_prev_target)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_achievement='".mysql_real_escape_string($may_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysql_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysql_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_prev_y_target='".mysql_real_escape_string($june_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jun_prev_y_achievement='".mysql_real_escape_string($june_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysql_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysql_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_prev_y_target='".mysql_real_escape_string($july_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jul_prev_y_achievement='".mysql_real_escape_string($july_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysql_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysql_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_prev_y_target='".mysql_real_escape_string($august_prev_target)."'";

				    $sqlselfappraisalupdate .= " , aug_prev_y_achievement='".mysql_real_escape_string($august_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysql_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysql_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_prev_y_target='".mysql_real_escape_string($september_prev_target)."'";

				    $sqlselfappraisalupdate .= " , sep_prev_y_achievement='".mysql_real_escape_string($september_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysql_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysql_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_prev_y_target='".mysql_real_escape_string($october_prev_target)."'";

				    $sqlselfappraisalupdate .= " , oct_prev_y_achievement='".mysql_real_escape_string($october_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysql_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysql_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_target='".mysql_real_escape_string($november_prev_target)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_achievement='".mysql_real_escape_string($november_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysql_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysql_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_target='".mysql_real_escape_string($december_prev_target)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_achievement='".mysql_real_escape_string($december_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysql_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysql_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_target='".mysql_real_escape_string($january_prev_target)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_achievement='".mysql_real_escape_string($january_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysql_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysql_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_target='".mysql_real_escape_string($february_prev_target)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_achievement='".mysql_real_escape_string($february_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysql_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysql_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_target='".mysql_real_escape_string($march_prev_target)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_achievement='".mysql_real_escape_string($march_prev_achievement)."'";
					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";

					mysql_query($sqlselfappraisalupdate) or array_push($failure_array,"$sqlselfappraisalupdate");

				}*/

				//For product wise table updation

			/*$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$customer_code."'";

			$rsempcode=mysql_query($sqlempcode);

			$cntempcode=mysql_num_rows($rsempcode);

			$rowempcode=mysql_fetch_array($rsempcode);

			$emp_code=$rowempcode['emp_code'];*/

				

			$sqlchkemployee="SELECT emp_code FROM self_appraisal_product_wise  WHERE customer_code='".$customer_code."' AND prod_code='12001'";

			$rschkemployee=mysql_query($sqlchkemployee);

			$counchkemployee=mysql_num_rows($rschkemployee);

				

				if($counchkemployee ==0)

				{

					$sqlselfappraisalprod  = "insert into self_appraisal_product_wise SET ";

					$sqlselfappraisalprod .= "   emp_code=''";

					$sqlselfappraisalprod .= " , customer_code='".mysql_real_escape_string($customer_code)."'";

					$sqlselfappraisalprod .= " , prod_code='12001'";

				$sqlselfappraisalprod .= " , apr_30_target='".mysql_real_escape_string($april_target)."'";
				$sqlselfappraisalprod .= " , apr_30_achievement='".mysql_real_escape_string($april_achievement)."'";
				$sqlselfappraisalprod .= " , apr_prev_y_target='".mysql_real_escape_string($april_prev_target)."'";
				$sqlselfappraisalprod .= " , apr_prev_y_achievement='".mysql_real_escape_string($april_prev_achievement)."'";
				$sqlselfappraisalprod .= " , may_31_target='".mysql_real_escape_string($may_target)."'";
				$sqlselfappraisalprod .= " , may_31_achievement='".mysql_real_escape_string($may_achievement)."'";
				$sqlselfappraisalprod .= " , may_prev_y_target='".mysql_real_escape_string($may_prev_target)."'";
				$sqlselfappraisalprod .= " , may_prev_y_achievement='".mysql_real_escape_string($may_prev_achievement)."'";
				$sqlselfappraisalprod .= " , jun_30_target='".mysql_real_escape_string($june_target)."'";
				$sqlselfappraisalprod .= " , jun_30_achievement='".mysql_real_escape_string($june_achievement)."'";
				$sqlselfappraisalprod .= " , jun_prev_y_target='".mysql_real_escape_string($june_prev_target)."'";
				$sqlselfappraisalprod .= " , jun_prev_y_achievement='".mysql_real_escape_string($june_prev_achievement)."'";
				$sqlselfappraisalprod .= " , jul_31_target='".mysql_real_escape_string($july_target)."'";
				$sqlselfappraisalprod .= " , jul_31_achievement='".mysql_real_escape_string($july_achievement)."'";
				$sqlselfappraisalprod .= " , jul_prev_y_target='".mysql_real_escape_string($july_prev_target)."'";
				$sqlselfappraisalprod .= " , jul_prev_y_achievement='".mysql_real_escape_string($july_prev_achievement)."'";
				$sqlselfappraisalprod .= " , aug_31_target='".mysql_real_escape_string($august_target)."'";
				$sqlselfappraisalprod .= " , aug_31_achievement='".mysql_real_escape_string($august_achievement)."'";
				$sqlselfappraisalprod .= " , aug_prev_y_target='".mysql_real_escape_string($august_prev_target)."'";
				$sqlselfappraisalprod .= " , aug_prev_y_achievement='".mysql_real_escape_string($august_prev_achievement)."'";
				$sqlselfappraisalprod .= " , sep_30_target='".mysql_real_escape_string($september_target)."'";
				$sqlselfappraisalprod .= " , sep_30_achievement='".mysql_real_escape_string($september_achievement)."'";
				$sqlselfappraisalprod .= " , sep_prev_y_target='".mysql_real_escape_string($september_prev_target)."'";
				$sqlselfappraisalprod .= " , sep_prev_y_achievement='".mysql_real_escape_string($september_prev_achievement)."'";
				$sqlselfappraisalprod .= " , oct_31_target='".mysql_real_escape_string($october_target)."'";
				$sqlselfappraisalprod .= " , oct_31_achievement='".mysql_real_escape_string($october_achievement)."'";
				$sqlselfappraisalprod .= " , oct_prev_y_target='".mysql_real_escape_string($october_prev_target)."'";
				$sqlselfappraisalprod .= " , oct_prev_y_achievement='".mysql_real_escape_string($october_prev_achievement)."'";
				$sqlselfappraisalprod .= " , nov_30_target='".mysql_real_escape_string($november_target)."'";
				$sqlselfappraisalprod .= " , nov_30_achievement='".mysql_real_escape_string($november_achievement)."'";
				$sqlselfappraisalprod .= " , nov_prev_y_target='".mysql_real_escape_string($november_prev_target)."'";
				$sqlselfappraisalprod .= " , nov_prev_y_achievement='".mysql_real_escape_string($november_prev_achievement)."'";
				$sqlselfappraisalprod .= " , dec_31_target='".mysql_real_escape_string($december_target)."'";
				$sqlselfappraisalprod .= " , dec_31_achievement='".mysql_real_escape_string($december_achievement)."'";
				$sqlselfappraisalprod .= " , dec_prev_y_target='".mysql_real_escape_string($december_prev_target)."'";
				$sqlselfappraisalprod .= " , dec_prev_y_achievement='".mysql_real_escape_string($december_prev_achievement)."'";
				$sqlselfappraisalprod .= " , jan_31_target='".mysql_real_escape_string($january_target)."'";
				$sqlselfappraisalprod .= " , jan_31_achievement='".mysql_real_escape_string($january_achievement)."'";
				$sqlselfappraisalprod .= " , jan_prev_y_target='".mysql_real_escape_string($january_prev_target)."'";
				$sqlselfappraisalprod .= " , jan_prev_y_achievement='".mysql_real_escape_string($january_prev_achievement)."'";
				$sqlselfappraisalprod .= " , feb_28_target='".mysql_real_escape_string($february_target)."'";
				$sqlselfappraisalprod .= " , feb_28_achievement='".mysql_real_escape_string($february_achievement)."'";
				$sqlselfappraisalprod .= " , feb_prev_y_target='".mysql_real_escape_string($february_prev_target)."'";
				$sqlselfappraisalprod .= " , feb_prev_y_achievement='".mysql_real_escape_string($february_prev_achievement)."'";

				$sqlselfappraisalprod .= " , mar_31_target='".mysql_real_escape_string($march_target)."'";
				$sqlselfappraisalprod .= " , mar_31_achievement='".mysql_real_escape_string($march_achievement)."'";
				$sqlselfappraisalprod .= " , mar_prev_y_target='".mysql_real_escape_string($march_prev_target)."'";
				$sqlselfappraisalprod .= " , mar_prev_y_achievement='".mysql_real_escape_string($march_prev_achievement)."'";
					mysql_query($sqlselfappraisalprod) or array_push($failure_array,"$sqlselfappraisalprod");

				}
				else
				{
					$sqlselfappraisalupdateprod  = "update self_appraisal_product_wise SET ";

					$sqlselfappraisalupdateprod .= " apr_30_target='".mysql_real_escape_string($april_target)."'";

					$sqlselfappraisalupdateprod .= " , apr_30_achievement='".mysql_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdateprod .= " , apr_prev_y_target='".mysql_real_escape_string($april_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , apr_prev_y_achievement='".mysql_real_escape_string($april_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , may_31_target='".mysql_real_escape_string($may_target)."'";

					$sqlselfappraisalupdateprod .= " , may_31_achievement='".mysql_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdateprod .= " , may_prev_y_target='".mysql_real_escape_string($may_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , may_prev_y_achievement='".mysql_real_escape_string($may_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , jun_30_target='".mysql_real_escape_string($june_target)."'";

					$sqlselfappraisalupdateprod .= " , jun_30_achievement='".mysql_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdateprod .= " , jun_prev_y_target='".mysql_real_escape_string($june_prev_target)."'";

				    $sqlselfappraisalupdateprod .= " , jun_prev_y_achievement='".mysql_real_escape_string($june_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , jul_31_target='".mysql_real_escape_string($july_target)."'";

					$sqlselfappraisalupdateprod .= " , jul_31_achievement='".mysql_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdateprod .= " , jul_prev_y_target='".mysql_real_escape_string($july_prev_target)."'";

				    $sqlselfappraisalupdateprod .= " , jul_prev_y_achievement='".mysql_real_escape_string($july_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , aug_31_target='".mysql_real_escape_string($august_target)."'";

					$sqlselfappraisalupdateprod .= " , aug_31_achievement='".mysql_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdateprod .= " , aug_prev_y_target='".mysql_real_escape_string($august_prev_target)."'";

				    $sqlselfappraisalupdateprod .= " , aug_prev_y_achievement='".mysql_real_escape_string($august_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , sep_30_target='".mysql_real_escape_string($september_target)."'";

					$sqlselfappraisalupdateprod .= " , sep_30_achievement='".mysql_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdateprod .= " , sep_prev_y_target='".mysql_real_escape_string($september_prev_target)."'";

				    $sqlselfappraisalupdateprod .= " , sep_prev_y_achievement='".mysql_real_escape_string($september_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , oct_31_target='".mysql_real_escape_string($october_target)."'";

					$sqlselfappraisalupdateprod .= " , oct_31_achievement='".mysql_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdateprod .= " , oct_prev_y_target='".mysql_real_escape_string($october_prev_target)."'";

				    $sqlselfappraisalupdateprod .= " , oct_prev_y_achievement='".mysql_real_escape_string($october_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , nov_30_target='".mysql_real_escape_string($november_target)."'";

					$sqlselfappraisalupdateprod .= " , nov_30_achievement='".mysql_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdateprod .= " , nov_prev_y_target='".mysql_real_escape_string($november_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , nov_prev_y_achievement='".mysql_real_escape_string($november_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , dec_31_target='".mysql_real_escape_string($december_target)."'";

					$sqlselfappraisalupdateprod .= " , dec_31_achievement='".mysql_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdateprod .= " , dec_prev_y_target='".mysql_real_escape_string($december_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , dec_prev_y_achievement='".mysql_real_escape_string($december_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , jan_31_target='".mysql_real_escape_string($january_target)."'";

					$sqlselfappraisalupdateprod .= " , jan_31_achievement='".mysql_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdateprod .= " , jan_prev_y_target='".mysql_real_escape_string($january_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , jan_prev_y_achievement='".mysql_real_escape_string($january_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , feb_28_target='".mysql_real_escape_string($february_target)."'";

					$sqlselfappraisalupdateprod .= " , feb_28_achievement='".mysql_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdateprod .= " , feb_prev_y_target='".mysql_real_escape_string($february_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , feb_prev_y_achievement='".mysql_real_escape_string($february_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , mar_31_target='".mysql_real_escape_string($march_target)."'";

					$sqlselfappraisalupdateprod .= " , mar_31_achievement='".mysql_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdateprod .= " , mar_prev_y_target='".mysql_real_escape_string($march_prev_target)."'";

					$sqlselfappraisalupdateprod .= " , mar_prev_y_achievement='".mysql_real_escape_string($march_prev_achievement)."'";

					$sqlselfappraisalupdateprod .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."' AND prod_code='12001'";

					mysql_query($sqlselfappraisalupdateprod) or array_push($failure_array,"$sqlselfappraisalupdateprod");

				}

				//End product wise table updation

		}

		//print_r($failure_array);

		define("SERVER","localhost");

		define("USER","acedns_dnsprod");

		define("PASSWORD","dnsprod1234#");

		define("DB","acedns_STAR");

		$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");

		mysql_select_db(DB,$link) or die("could not connect the database");

		//if(count($failure_array)==0)

		//{

		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='self_appraisal_customer_wise'";

		mysql_query($sqlupdate,$link);

		//}

?>

