<?php
	// define("SERVERREMOTE","103.87.174.95");
	// define("USERREMOTE","starsaat_dnsprod");
	// define("PASSWORDREMOTE","dnsprod1234#");
	// define("DBREMOTE","starsaathi_STARS");
// 		error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
	// $link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	$type ='remortdb';
	include "saathi_connection.php";
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	
	//echo"<pre>123";print_r($_POST);die;

	$dns_prod_code_branch_array=array();
$branch_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
			if($key=='dns_branch_code')  $branch_code=$value;
			if(!in_array($branch_code,$branch_code_array))
			{
				array_push($branch_code_array,$branch_code);
			}
			//$branch_prod_string=$dns_prod_code.'#'.$branch_code;
			if($key=='branch_name')  		 ${'branch_name'.$branch_code}=$value;
			if($key=='dns_prod_code')   	 $dns_prod_code=$value;
			if(!isset(${'dns_prod_code'.$branch_code}))
			{
				${'dns_prod_code'.$branch_code}=array();
			}
			if(!in_array($dns_prod_code,${'dns_prod_code'.$branch_code}))
			{
			  if($dns_prod_code!=''){
				array_push(${'dns_prod_code'.$branch_code},$dns_prod_code);
				}
			}
			if($key=='prod_desc')   	 ${'prod_desc'.$branch_code.$dns_prod_code}=$value;
			if($key=='status')   	 ${'status'.$branch_code.$dns_prod_code}=$value;
			
		}
	}
	foreach($branch_code_array as $branch_code_val){
		$branch_name=${'branch_name'.$branch_code_val};
	  foreach(${'dns_prod_code'.$branch_code_val} as $dns_prod_code_val)
		{
		$branch_code=$branch_code_val;
		$dns_prod_code=$dns_prod_code_val;
		$prod_desc=${'prod_desc'.$branch_code.$dns_prod_code};
		$status=${'status'.$branch_code.$dns_prod_code}; 
		
		
		$sqlpopprod="SELECT dns_prod_code FROM  branch_pop_product WHERE 
					dns_prod_code='".addslashes($dns_prod_code)."' AND branch_code='".$branch_code."'";
		//echo"<pre>";print_r($sqlpopprod);

		$rspopprod=mysqli_query($link,$sqlpopprod);
		$countpopprod=mysqli_num_rows($rspopprod);
			if($dns_prod_code !='' && $prod_desc !='' && $branch_code !='' && $branch_name!='' && $status!='')
			{
				if($countpopprod=='0')
					{
						$sqlinsertbranchpoprod  = "insert into  branch_pop_product  ";
						$sqlinsertbranchpoprod .= " SET dns_prod_code='".$dns_prod_code."'";
						$sqlinsertbranchpoprod .= " ,prod_desc='".$prod_desc."'";
						$sqlinsertbranchpoprod .= " ,branch_code='".$branch_code."'";
						$sqlinsertbranchpoprod .= " ,branch_name='".$branch_name."'";
						$sqlinsertbranchpoprod .= " ,status='".$status."'";
						$sqlinsertbranchpoprod .= " , upload_date_time=CURRENT_TIMESTAMP()";
						//echo $sqlinsertbranchpoprod;
						mysqli_query($link,$sqlinsertbranchpoprod) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on branch pop product table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlupdatebrprod  = "update branch_pop_product ";
						$sqlupdatebrprod .= " SET prod_desc='".$prod_desc."'";
						$sqlupdatebrprod .= " ,branch_name='".$branch_name."'";
						$sqlupdatebrprod .= " ,status='".$status."'";
						$sqlupdatebrprod .= " , upload_date_time=CURRENT_TIMESTAMP() WHERE dns_prod_code='".addslashes($dns_prod_code)."' AND branch_code='".$branch_code."'";
						//echo $sqlupdatebrprod;
						mysqli_query($link,$sqlupdatebrprod);
					}
				}
	}
	}
		// define("SERVER","localhost");
		// define("USER","acedns_dnsprod");
		// define("PASSWORD","dnsprod1234#");
		// define("DB","acedns_STAR");
		// $linksource=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
		$type ='localdb';
		include "saathi_connection.php";
		//mysqli_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_pop_product'";
		mysqli_query($link,$sqlupdate);
mysqli_close($link);
?>
