<?php
	// define("SERVERREMOTE","103.87.174.95");
	// define("USERREMOTE","starsaat_dnsprod");
	// define("PASSWORDREMOTE","dnsprod1234#");
	// define("DBREMOTE","starsaathi_STARS");
		
	// $link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	$type ='remortdb';
	include "saathi_connection.php";
	//echo"<pre>";print_r($_POST);
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_prod_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_prod_code') $dns_prod_code=$value;
			if(!in_array($dns_prod_code,$dns_prod_code_array))
			{
				array_push($dns_prod_code_array,$dns_prod_code);
			}
			if($key=='prod_desc')   	 ${'prod_desc'.$dns_prod_code}=$value;
			if($key=='prod_image')  ${'prod_image'.$dns_prod_code}=$value;
			if($key=='min_order_qty')  		 ${'min_order_qty'.$dns_prod_code}=$value;
			if($key=='price_per_piece')  		${'price_per_piece'.$dns_prod_code}=$value;
			if($key=='GST_rate')  		  ${'GST_rate'.$dns_prod_code}=$value;
			if($key=='status')  		  ${'status'.$dns_prod_code}=$value;
			if($key=='payment_gateway')   ${'payment_gateway'.$dns_prod_code}=$value;
			if($key=='dealer_login')  	  ${'dealer_login'.$dns_prod_code}=$value;
			if($key=='sp_login')  		  ${'sp_login'.$dns_prod_code}=$value;
		}
	}
	foreach($dns_prod_code_array as $dns_prod_code_val){
		$dns_prod_code=$dns_prod_code_val;
		$prod_desc=${'prod_desc'.$dns_prod_code_val};
		$prod_image=${'prod_image'.$dns_prod_code_val};
		$min_order_qty=${'min_order_qty'.$dns_prod_code_val};
		$price_per_piece=${'price_per_piece'.$dns_prod_code_val};
		$GST_rate=${'GST_rate'.$dns_prod_code_val};
		$status=${'status'.$dns_prod_code_val};
		$payment_gateway=${'payment_gateway'.$dns_prod_code_val};
		$dealer_login=${'dealer_login'.$dns_prod_code_val};
		$sp_login=${'sp_login'.$dns_prod_code_val};
		
		 $sqlpopprod="SELECT dns_prod_code FROM  pop_product_master WHERE dns_prod_code='".addslashes($dns_prod_code)."'";
		$rspopprod=mysqli_query($link,$sqlpopprod);
		$countpopprod=mysqli_num_rows($rspopprod);

				if($countpopprod=='0')
					{
						$sqlinsertpoprod  = "insert into  pop_product_master ";
						$sqlinsertpoprod .= " SET dns_prod_code='".$dns_prod_code."'";
						$sqlinsertpoprod .= " ,prod_desc='".$prod_desc."'";
						$sqlinsertpoprod .= " ,prod_image='".$prod_image."'";
						$sqlinsertpoprod .= " ,min_order_qty='".$min_order_qty."'";
						$sqlinsertpoprod .= " ,price_per_piece='".$price_per_piece."'";
						$sqlinsertpoprod .= " ,GST_rate='".$GST_rate."'";
						$sqlinsertpoprod .= " ,status='".$status."'";
						$sqlinsertpoprod .= " ,payment_gateway='".$payment_gateway."'";
						$sqlinsertpoprod .= " ,dealer_login='".$dealer_login."'";
						$sqlinsertpoprod .= " ,sp_login='".$sp_login."'";
						$sqlinsertpoprod .= " , upload_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertpoprod) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on pop product table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlupdateprod  = "update pop_product_master ";
						$sqlupdateprod .= " SET prod_desc='".$prod_desc."'";
						$sqlupdateprod .= " ,min_order_qty='".$min_order_qty."'";
						$sqlupdateprod .= " ,price_per_piece='".$price_per_piece."'";
						$sqlupdateprod .= " ,status='".$status."'";
						$sqlupdateprod .= " ,payment_gateway='".$payment_gateway."'";
						$sqlupdateprod .= " ,dealer_login='".$dealer_login."'";
						$sqlupdateprod .= " ,sp_login='".$sp_login."'";
						$sqlupdateprod .= " ,upload_date_time=CURRENT_TIMESTAMP() WHERE dns_prod_code='".addslashes($dns_prod_code)."'";
						mysqli_query($link,$sqlupdateprod);
						//echo"<pre>";print_r($sqlupdateprod);
					}
	}
	mysqli_close($link);
		// define("SERVER","localhost");
		// define("USER","acedns_dnsprod");
		// define("PASSWORD","dnsprod1234#");
		// define("DB","acedns_STAR");
		// $linksource=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
		$type ='localdb';
		include "saathi_connection.php";
		//mysqli_select_db(DB,$linksource) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='pop_product'";
		mysqli_query($link,$sqlupdate);
mysqli_close($link);
?>
