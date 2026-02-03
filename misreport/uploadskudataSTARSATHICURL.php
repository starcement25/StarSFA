<?php
// 	define("SERVERREMOTE","103.87.174.95");
// 	define("USERREMOTE","starsaat_dnsprod");
// 	define("PASSWORDREMOTE","dnsprod1234#");
// 	define("DBREMOTE","starsaathi_START");
		
// 	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
// 	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	
	
	$type ='remortdb';
	include "saathi_connection.php";



// // Run the SHOW TABLES query
// $sql = "SHOW TABLES";
// $result = mysqli_query($link, $sql);

// if ($result) {
//     echo "Tables in database '" . DBREMOTE . "':<br><ul>";
//     while ($row = mysqli_fetch_row($result)) {
//         echo "<li>" . $row[0] . "</li>";
//     }
//     echo "</ul>";
// } else {
//     echo "Error running query: " . mysqli_error($link);
// }
	
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_prod_code_array=array();
	$branch_code_array=array();
	//echo"<pre>";print_r($_POST);die;

//print_r($_POST);
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_prod_code') $dns_prod_code=$value;
			if(!in_array($dns_prod_code,$dns_prod_code_array))
			{
				array_push($dns_prod_code_array,$dns_prod_code);
			}
			if($key=='branch_code')   	 $branch_code=$value;
			if(!isset(${'branch_code'.$dns_prod_code}))
			{
				${'branch_code'.$dns_prod_code}=array();
			}
			if(!in_array($branch_code,${'branch_code'.$dns_prod_code}))
			{
			  if($branch_code!=''){
				array_push(${'branch_code'.$dns_prod_code},$branch_code);
				}
			}
			if($key=='prod_desc')   	   ${'prod_desc'.$dns_prod_code.$branch_code}=$value;
			if($key=='product_group_code_name')   	${'product_group_code_name'.$dns_prod_code.$branch_code}=$value;
			if($key=='acedns')   	 	 ${'acedns'.$dns_prod_code.$branch_code}=$value;
			if($key=='black_list')   	 ${'black_list'.$dns_prod_code.$branch_code}=$value;
			if($key=='sp_login')   	 	 ${'sp_login'.$dns_prod_code.$branch_code}=$value;
			if($key=='dealer_login')   	 ${'dealer_login'.$dns_prod_code.$branch_code}=$value;
		}
	}
	foreach($dns_prod_code_array as $dns_prod_code_val){
	  foreach(${'branch_code'.$dns_prod_code_val} as $branch_code_val)
		{
		$dns_prod_code=$dns_prod_code_val;
		$branch_code_product=$branch_code_val;
		$prod_desc=${'prod_desc'.$dns_prod_code_val.$branch_code_val};
		$product_group_code_name=${'product_group_code_name'.$dns_prod_code_val.$branch_code_val};
		$acedns=${'acedns'.$dns_prod_code_val.$branch_code_val};
		$black_list=${'black_list'.$dns_prod_code_val.$branch_code_val};
		$sp_login=${'sp_login'.$dns_prod_code_val.$branch_code_val};
		$dealer_login=${'dealer_login'.$dns_prod_code_val.$branch_code_val};  
		
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
		$sqlbranch="SELECT branch_code FROM branch_master where dns_branch_code='".$branch_code_product."'";
		$rsbranch=mysqli_query($link,$sqlbranch);
		$rowbranch=mysqli_fetch_assoc($rsbranch);
		$branch_code=$rowbranch['branch_code'];  
		  
		$branch_code_condition= " AND branch_code='".$branch_code."'";
		$sqlskunamechk="SELECT * FROM product_master WHERE dns_prod_code='".$dns_prod_code."'".$branch_code_condition."";
		$rsskunamechk=mysqli_query($link,$sqlskunamechk);
		$countskunamechk=mysqli_num_rows($rsskunamechk);
		$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
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
			$sql .= " , product_group_code='".mysqli_real_escape_string($link,$product_group_code)."'";
			$sql .= " , acedns='".strtoupper($acedns)."'";
			$sql .= " , black_list='".strtoupper($black_list)."'";
			$sql .= " , dealer_login='".$dealer_login."'";
			$sql .= " , sp_login='".$sp_login."'";
			$sql .= " ,	download_time=CURRENT_TIMESTAMP()";
			echo"<pre>";print_r($sql);
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
			$sp_login_db=$rowskunamechk['sp_login'];
			$dealer_login_db=$rowskunamechk['dealer_login'];
			
			if($acedns_db!='' && $black_list_db!='' && $product_group_code_db!='' && $branch_code_db!='' && $prod_desc_db!='' && $sp_login_db!='' && $dealer_login_db!=''){

			if($acedns_db!=$acedns || $black_list_db!=$black_list 
				|| $product_group_code_db!=$product_group_code || $branch_code_db!=$branch_code 
			   || $prod_desc_db!=$prod_desc || $sp_login_db!=$sp_login || $dealer_login_db!=$dealer_login )
			{
				$sql  = "UPDATE product_master ";
				$sql .= " SET branch_code='".$branch_code."'";
				$sql .= " , prod_desc='".addslashes($prod_desc)."'";
				$sql .= " , product_group_code='".mysqli_real_escape_string($link,$product_group_code)."'";
				$sql .= " , acedns='".strtoupper($acedns)."'";
				$sql .= " , black_list='".strtoupper($black_list)."'";
				$sql .= " , sp_login='".strtoupper($sp_login)."'";
				$sql .= " , dealer_login='".strtoupper($dealer_login)."'";
				$sql .= " , download_time=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";
			echo"<pre>";print_r($sql);
				
				mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
			}
			}
		}
	 }
  }
  $type ='localdb';
		include "saathi_connection.php";
  //mysqli_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='product_master'";
	mysqli_query($link,$sqlupdate);
?>
