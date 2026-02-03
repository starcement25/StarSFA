<?php
	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	//print_r($_POST);
	$dns_prod_code_array=array();
	$branch_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_prod_code') $dns_prod_code=$value;
			if(!in_array($dns_prod_code,$dns_prod_code_array))
			{
				array_push($dns_prod_code_array,$dns_prod_code);
			}
			if($key=='branch_code')   	 $branch_code=$value;
			if(!isset(${branch_code.$dns_prod_code}))
			{
				${branch_code.$dns_prod_code}=array();
			}
			if(!in_array($branch_code,${branch_code.$dns_prod_code}))
			{
			  if($branch_code!=''){
				array_push(${branch_code.$dns_prod_code},$branch_code);
				}
			}
			if($key=='prod_desc')   	   ${prod_desc.$dns_prod_code.$branch_code}=$value;
			if($key=='product_group_code_name')   	${product_group_code_name.$dns_prod_code.$branch_code}=$value;
			if($key=='acedns')   	 	 ${acedns.$dns_prod_code.$branch_code}=$value;
			if($key=='black_list')   	 ${black_list.$dns_prod_code.$branch_code}=$value;
		}
	}
	foreach($dns_prod_code_array as $dns_prod_code_val){
	  foreach(${branch_code.$dns_prod_code_val} as $branch_code_val)
		{
		$dns_prod_code=$dns_prod_code_val;
		$branch_code=$branch_code_val;
		$prod_desc=${prod_desc.$dns_prod_code_val.$branch_code_val};
		$product_group_code_name=${product_group_code_name.$dns_prod_code_val.$branch_code_val};
		$acedns=${acedns.$dns_prod_code_val.$branch_code_val};
		$black_list=${black_list.$dns_prod_code_val.$branch_code_val};
		
		$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code."'";
		$rsbranchcode=mysqli_query($link,$sqlbranchcode);
		$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
		$branch_code=$rowbranchcode['branch_code'];
		
		$sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."'";
		$rsprodgroupnamechk=mysqli_query($link,$sqlprodgroupnamechk);
		$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);
		if($countprodgroupnamechk<1){
			$sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master";
			$rsmaxproductgroupcode=mysqli_query($link,$sqlmaxproductgroupcode);
			$rowmaxproductgroupcode=mysqli_fetch_assoc($rsmaxproductgroupcode);
			$max_product_group_code=$rowmaxproductgroupcode['max_product_group_code'];
			
			if($max_product_group_code=='')
			{
				$max_product_group_code='1';
			}
			else
			{
				$max_product_group_code++;
			}
			$max_product_group_code='BR'.$max_product_group_code;
			$sqlbrand  = "INSERT INTO product_group_master SET ";
			$sqlbrand .= "  product_group_code='".$max_product_group_code."'";
			$sqlbrand .= " , product_group_name='".addslashes($product_group_code_name)."'";
			$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
			$product_group_code=$max_product_group_code;
		}
		else
		{
			$rowprodgroupnamechk=mysqli_fetch_assoc($rsprodgroupnamechk);
			$product_group_code=$rowprodgroupnamechk['product_group_code'];
		}
		$branch_code_condition= " AND branch_code='".$branch_code."'";
		echo $sqlskunamechk="SELECT * FROM product_master WHERE dns_prod_code='".$dns_prod_code."'".$branch_code_condition."";
		$rsskunamechk=mysqli_query($link,$sqlskunamechk);
		$countskunamechk=@mysqli_num_rows($rsskunamechk);
		$rowskunamechk=@mysqli_fetch_assoc($rsskunamechk);
		$updateflag=0;
		$insertflag=0;
		if($countskunamechk<1)
		{
			$sqlmaxskucode="SELECT MAX(prod_code) AS max_prod_code FROM  product_master WHERE 1";
			$rsmaxskucode=mysqli_query($link,$sqlmaxskucode);
			$rowmaxskucode=mysqli_fetch_assoc($rsmaxskucode);
			$max_prod_code=$rowmaxskucode['max_prod_code'];
			
			if($max_prod_code=='')
			{
				$max_prod_code='12001';
			}
			else
			{
				$max_prod_code++;
			}
			$sql  = "insert into product_master ";
			$sql .= " SET prod_code='".$max_prod_code."'";
			$sql .= " , dns_prod_code='".$dns_prod_code."'";
			$sql .= " , branch_code='".$branch_code."'";
			$sql .= " , prod_desc='".addslashes($prod_desc)."'";
			$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
			$sql .= " , acedns='".strtoupper($acedns)."'";
			$sql .= " , black_list='".strtoupper($black_list)."'";
			$sql .= " ,	download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sql);
		 }
		else
		{
			$branch_code_db=$rowskunamechk['branch_code'];
			$acedns_db=$rowskunamechk['acedns'];
			$black_list_db=$rowskunamechk['black_list'];
			$prod_code_db=$rowskunamechk['prod_code'];
			$prod_desc_db=$rowskunamechk['prod_desc'];
			$product_group_code_db=$rowskunamechk['product_group_code'];

			if($acedns_db!=$acedns || $black_list_db!=$black_list 
				|| $product_group_code_db!=$product_group_code || $branch_code_db!=$branch_code || $prod_desc_db!=$prod_desc)
			{
				$sql  = "UPDATE product_master ";
				$sql .= " SET branch_code='".$branch_code."'";
				$sql .= " , prod_desc='".addslashes($prod_desc)."'";
				$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
				$sql .= " , acedns='".strtoupper($acedns)."'";
				$sql .= " , black_list='".strtoupper($black_list)."'";
				$sql .= " , download_time=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";
				mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
			}
		}
	 }
  }
	/*define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_STAR");
	$link=mysqli_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='product_master'";*/
	mysqli_query($link,$sqlupdate,$link);
?>
