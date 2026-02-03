<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
//require("include/functions.php");
require("include/config-email-setup.php");

	function similar_file_exists($filename) {
	  if (file_exists($filename)) {
		return $filename;
	  }
	  $dir = dirname($filename);
	  $files = glob($dir . '/*');
	  $lcaseFilename = strtolower($filename);
	  foreach($files as $file) {
		if (strtolower($file) == $lcaseFilename) {
		  return $file;
		}
	  }
	  return false;
	}
	function return_auto_code($code_prefix,$code_type,$code){
		
		if($code_type=='employee')
		{
			if(strlen($code)=='1')
			{
				$build_code=$code_prefix.'000'.$code;
			}
			if(strlen($code)=='2')
			{
				$build_code=$code_prefix.'00'.$code;
			}
			if(strlen($code)=='3')
			{
				$build_code=$code_prefix.'0'.$code;
			}
		}
		return $build_code;
	}
	
	$upload_dir="csv/$nick_name/";
	$folderName = $_REQUEST['nick_name'];
	//$error_array=array();
	if(similar_file_exists("csv/$nick_name/aceDNS_csv.zip")!=false)
	{
		$filename=similar_file_exists("csv/$nick_name/aceDNS_csv.zip");
		$zip = new ZipArchive;
		if ($zip->open($filename)) {
			$zip->extractTo("csv/$nick_name/");
			$zip->close();
		}
	//For Branch Master CSV
	if(similar_file_exists("csv/$folderName/Branch master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Branch master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		/*$sqldelete="truncate branch_master";
		$rsdelete=mysqli_query($link,$sqldelete);*/
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
			
					if(!$double_coute_found && $char=="\"")
					{  
					
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
				
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;
			  //print_r($data);
			  
				$dns_branch_code=trim($data[0]);
				$branch_name=trim($data[1]);
				$branch_location=trim($data[2]);
				$comp_code=trim($data[3]);
				$branch_state=trim($data[4]);
				$branch_email_id=trim($data[5]);
				$branch_accounts_email_id=trim($data[6]);
				$alternative_email_id=trim($data[7]);
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."'";				
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$countbranchnamechk=mysqli_num_rows($rsbranchnamechk);
				
				$csv_row_count=$rec_count+1;
				if($countbranchnamechk<1)
				{
					$sqlmaxbranchcode="SELECT MAX(branch_code) AS max_branch_code FROM  branch_master WHERE 1";
					$rsmaxbranchcode=mysqli_query($link,$sqlmaxbranchcode);
					$rowmaxbranchcode=mysqli_fetch_assoc($rsmaxbranchcode);
					$max_branch_code=$rowmaxbranchcode['max_branch_code'];
					
					if($max_branch_code=='')
					{
						$max_branch_code='B0001';
					}
					else
					{
						$max_branch_code++;
					}
					$sqlbranch  = "insert into branch_master SET ";
					$sqlbranch .= "  	branch_code='".mysqli_real_escape_string($max_branch_code)."'";
					$sqlbranch .= " , dns_branch_code='".mysqli_real_escape_string($dns_branch_code)."'";
					$sqlbranch .= " , branch_name='".mysqli_real_escape_string($branch_name)."'";
					$sqlbranch .= " , branch_state='".mysqli_real_escape_string($branch_state)."'";
					$sqlbranch .= " , branch_location='".mysqli_real_escape_string($branch_location)."'";
					$sqlbranch .= " , comp_code='".mysqli_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($alternative_email_id)."'";
				}
				else
				{
					$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);

					$sqlbranch  = "UPDATE branch_master SET ";
					$sqlbranch .= "  	dns_branch_code='".mysqli_real_escape_string($dns_branch_code)."'";
					$sqlbranch .= " , branch_location='".mysqli_real_escape_string($branch_location)."'";
					$sqlbranch .= " , branch_state='".mysqli_real_escape_string($branch_state)."'";
					$sqlbranch .= " , comp_code='".mysqli_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($alternative_email_id)."' WHERE branch_name='".addslashes($branch_name)."'";
				}
				mysqli_query($link,$sqlbranch) or die(mysqli_error().".Duplicate key @row $csv_row_count in Branch master.csv.Please check.");
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Branch master.csv is wrong.";
		exit();
	}*/
		if(similar_file_exists("csv/$folderName/Sku master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$folderName/Sku master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			$lines = file($filename);
			$duplicate_product=array();
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				//print_r($data);
				  
					$branch_code_name=trim($data[0]);
					$dns_prod_code=trim($data[1]);
					$prod_desc=trim($data[2]);
					//$prod_desc=str_replace('~','"',$prod_desc);
					$product_group_code_name=trim($data[3]);
					$product_sub_group_code_name=trim($data[4]);
					$product_brand_code_name=trim($data[5]);
					//For SELVEL
					/*if($product_brand_code_name!=''){
					$prod_desc=$prod_desc.'-'.$product_brand_code_name;
					}
					$product_brand_code_name='';*/
					//End For SELVEL
					$cl_stk=trim($data[6]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					$acedns=trim($data[7]);
					$black_list=trim($data[8]);
					$vertical_value=trim($data[9]);
					$UOM1=trim($data[10]);
					$UOM2=trim($data[11]);
					$conversion=trim($data[12]);
					$UOM3=trim($data[13]);
					$conversion_factor_two=trim($data[14]);
					$TD=trim($data[15]);
					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
					}
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

					if(no_of_filter > 1){
						//Product group code checking start
						$sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."'";
						$rsprodgroupnamechk=mysqli_query($link,$sqlprodgroupnamechk);
						$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);
						if($countprodgroupnamechk<1){
							$sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master WHERE product_group_code LIKE 'BR%'";
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
							$sqlbrand .= "  product_group_code='".addslashes($max_product_group_code)."'";
							$sqlbrand .= " , product_group_name='".addslashes($product_group_code_name)."'";
							$sqlbrand .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlbrand) or die(mysqli_error().".Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
							$product_group_code=$max_product_group_code;
						}
						else
						{
							$rowprodgroupnamechk=mysqli_fetch_assoc($rsprodgroupnamechk);
							$product_group_code=$rowprodgroupnamechk['product_group_code'];
							$vertical_value_db=$rowprodgroupnamechk['vertical_value'];
							if($vertical_value_db!=$vertical_value)
							{
								/*if(substr($product_group_code,0,1)!='BR')
								{
									$sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master WHERE product_group_code LIKE 'BR%'";
									$rsmaxproductgroupcode=mysqli_query($link,$sqlmaxproductgroupcode);
									$rowmaxproductgroupcode=mysqli_fetch_assoc($rsmaxproductgroupcode);
									$max_product_group_code=$rowmaxproductgroupcode['max_product_group_code'];
									
									echo strlen($max_product_group_code);
									if($max_product_group_code=='' || strlen($max_product_group_code) >5)
									{
										$max_product_group_code='1';
									}
									else
									{
										$max_product_group_code++;
									}
									$product_group_code='BR'.$max_product_group_code;
									$sqlupdatebrandcondition=" ,product_group_code='".$product_group_code."'"; 
								}
								else
								{
									$sqlupdatebrandcondition='';
								}*/
								$sqlupdatebrand  = "UPDATE product_group_master SET ";
								$sqlupdatebrand .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE product_group_name='".addslashes($product_group_code_name)."'";
								mysqli_query($link,$sqlupdatebrand) or die(mysqli_error().".Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
							}
						}
						//Product group code checking end
					 }
					if(no_of_filter > 2){
						//Product sub group code checking start
						$sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($product_sub_group_code_name)."' 
												AND product_group_code='".$product_group_code."'";
						$rsprodsubgroupnamechk=mysqli_query($link,$sqlprodsubgroupnamechk);
						$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);
						if($countprodsubgroupnamechk<1){
							/*$sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master WHERE product_sub_group_code LIKE 'BF%'";
							$rsmaxproductsubgroupcode=mysqli_query($link,$sqlmaxproductsubgroupcode);
							$rowmaxproductsubgroupcode=mysqli_fetch_assoc($rsmaxproductsubgroupcode);
							$max_product_sub_group_code=$rowmaxproductsubgroupcode['max_product_sub_group_code'];
							
							if($max_product_sub_group_code=='' )
							{
								$max_product_sub_group_code='1';
							}
							else
							{
								$max_product_sub_group_code++;
							}
							$max_product_sub_group_code='BF'.$max_product_sub_group_code;*/
							$sqlbrandform  = "INSERT INTO product_sub_group_master SET ";
							$sqlbrandform .= "  product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code_name)."'";
							$sqlbrandform .= " , product_sub_group_name='".addslashes($product_sub_group_code_name)."'";
							$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sqlbrandform .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlbrandform);
							$product_sub_group_code=$product_sub_group_code_name;
						}
						else
						{
							$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);
							$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];
							$vertical_value_sub_group=$rowprodsubgroupnamechk['vertical_value'];
							if($vertical_value_sub_group!=$vertical_value)
							{
								/*if(substr($product_sub_group_code,0,1)!='BF')
								{
									$sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master WHERE product_sub_group_code LIKE 'BF%'";
									$rsmaxproductsubgroupcode=mysqli_query($link,$sqlmaxproductsubgroupcode);
									$rowmaxproductsubgroupcode=mysqli_fetch_assoc($rsmaxproductsubgroupcode);
									$max_product_sub_group_code=$rowmaxproductsubgroupcode['max_product_sub_group_code'];
									
									if($max_product_sub_group_code=='' || strlen($max_product_sub_group_code) >5)
									{
										$max_product_sub_group_code='1';
									}
									else
									{
										$max_product_sub_group_code++;
									}
									$product_sub_group_code='BF'.$max_product_sub_group_code;
									$sqlupdatebrandformcondition=" ,product_sub_group_code='".$product_sub_group_code."'"; 
								}
								else
								{
									$sqlupdatebrandformcondition='';
								}*/

								$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";
								$sqlupdatebrandform .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP() WHERE 
														product_sub_group_name='".addslashes($product_sub_group_code_name)." AND product_group_code='".$product_group_code."'";
								mysqli_query($link,$sqlupdatebrandform);
							}
						}
						//Product sub group code checking end
					}
					if(no_of_filter > 3){
						//Product brand code checking start
						$sqlprodbrandnamechk="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".addslashes($product_brand_code_name)."'
												AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
						$rsprodbrandnamechk=mysqli_query($link,$sqlprodbrandnamechk);
						$countprodbrandnamechk=mysqli_num_rows($rsprodbrandnamechk);
						if($countprodbrandnamechk<1){
							/*$sqlmaxproductbrandcode="SELECT MAX( CAST( SUBSTRING( product_brand_code, -(length( product_brand_code ) -2), length( product_brand_code ) -2 ) AS UNSIGNED ) ) AS max_product_brand_code from product_brand_master WHERE product_brand_code LIKE 'BS%'";
							$rsmaxproductbrandcode=mysqli_query($link,$sqlmaxproductbrandcode);
							$rowmaxproductbrandcode=mysqli_fetch_assoc($rsmaxproductbrandcode);
							$max_product_brand_code=$rowmaxproductbrandcode['max_product_brand_code'];
							
							if($max_product_brand_code=='')
							{
								$max_product_brand_code='1';
							}
							else
							{
								$max_product_brand_code++;
							}
							$max_product_brand_code='BS'.$max_product_brand_code;*/
							$sqlbrandsubform  = "INSERT INTO product_brand_master SET ";
							$sqlbrandsubform .= "  product_brand_code='".mysqli_real_escape_string($product_brand_code_name)."'";
							$sqlbrandsubform .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
							$sqlbrandsubform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sqlbrandsubform .= " , product_brand_name='".addslashes($product_brand_code_name)."'";
							$sqlbrandsubform .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlbrandsubform);
							$product_brand_code=$product_brand_code_name;
						}
						else
						{
							$rowprodbrandnamechk=mysqli_fetch_assoc($rsprodbrandnamechk);
							$product_brand_code=$rowprodbrandnamechk['product_brand_code'];
							$vertical_value_brand=$rowprodbrandnamechk['vertical_value'];
							if($vertical_value_brand!=$vertical_value)
							{
								/*if(substr($product_brand_code,0,1)!='BS')
								{
								   $sqlmaxproductbrandcode="SELECT MAX( CAST( SUBSTRING( product_brand_code, -(length( product_brand_code ) -2), length( product_brand_code ) -2 ) AS UNSIGNED ) ) AS max_product_brand_code from product_brand_master WHERE product_brand_code LIKE 'BS%'";
									$rsmaxproductbrandcode=mysqli_query($link,$sqlmaxproductbrandcode);
									$rowmaxproductbrandcode=mysqli_fetch_assoc($rsmaxproductbrandcode);
									$max_product_brand_code=$rowmaxproductbrandcode['max_product_brand_code'];
									
									
									if($max_product_brand_code=='' || strlen($max_product_brand_code) >5)
									{
										$max_product_brand_code='1';
									}
									else
									{
										$max_product_brand_code++;
									}
									$product_brand_code='BS'.$max_product_brand_code;
									$sqlupdatebrandsubformcondition=" ,product_brand_code='".$product_brand_code."'"; 
								}
								else
								{
									$sqlupdatebrandsubformcondition='';
								}*/

								$sqlupdatebrandsubform  = "UPDATE product_brand_master SET ";
								$sqlupdatebrandsubform .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrandsubform .= " , download_time=CURRENT_TIMESTAMP() 
															WHERE product_brand_name='".addslashes($product_brand_code_name)." 
															AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
								mysqli_query($link,$sqlupdatebrandsubform);
							}
						}
					}
					//Product brand code checking end
					/*if(branch_wise_product=='yes')
					{
						$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".$prod_desc."' AND branch_code='".$branch_code."' AND  
										dns_prod_code='".$dns_prod_code."' AND product_group_code='".$product_group_code."' 
										AND product_sub_group_code='".$product_sub_group_code."' AND product_brand_code='".$product_brand_code."'";
					}
					else
					{*/
					$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."' AND branch_code='".$branch_code."' AND dns_prod_code='".$dns_prod_code."' 
									AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
									AND product_brand_code='".$product_brand_code."'";
					//}
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$countskunamechk=@mysqli_num_rows($rsskunamechk);
					$rowskunamechk=@mysqli_fetch_assoc($rsskunamechk);
					
					$csv_row_count=$rec_count+1;
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
						$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
						$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";
						$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , vertical_value='".$vertical_value."'";
						$sql .= " , UOM1='".$UOM1."'";
						$sql .= " , UOM2='".$UOM2."'";
						$sql .= " , conversion_factor='".$conversion."'";
						$sql .= " , pack_size='".$pack_size."'";
						$sql .= " , UOM3='".$UOM3."'";
						$sql .= " , conversion_factor_two='".$conversion_factor_two."'";
						$sql .= " , TD='".$TD."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						$sql .= " , download_time_cl_stk=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
					}
					else
					{
						$cl_stk_db=$rowskunamechk['cl_stk'];
						$branch_code_db=$rowskunamechk['branch_code'];
						$acedns_db=$rowskunamechk['acedns'];
						$black_list_db=$rowskunamechk['black_list'];
						$prod_code_db=$rowskunamechk['prod_code'];
						$product_group_code_db=$rowskunamechk['product_group_code'];
						$product_sub_group_code_db=$rowskunamechk['product_sub_group_code'];
						$product_brand_code_db=$rowskunamechk['product_brand_code'];
						$UOM1_db=$rowskunamechk['UOM1'];
						$UOM2_db=$rowskunamechk['UOM2'];
						$conversion_db=$rowskunamechk['conversion_factor'];
						$conversion_factor_two_db=$rowskunamechk['conversion_factor_two'];
						$pack_size_db=$rowskunamechk['pack_size'];
						$UOM3_db=$rowskunamechk['UOM3'];
						$TD_db=$rowskunamechk['TD'];
						
						if(($cl_stk_db==$cl_stk) && ($acedns_db!=$acedns || $black_list_db!=$black_list 
							|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 
							|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion || 
							$conversion_factor_two_db!=$conversion_factor || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD))
						{
							$sql  = "UPDATE product_master ";
							$sql .= " SET branch_code='".$branch_code."'";
							$sql .= " , prod_desc='".addslashes($prod_desc)."'";
							$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
							$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";
							$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";
							$sql .= " , acedns='".$acedns."'";
							$sql .= " , black_list='".$black_list."'";
							$sql .= " , UOM1	 ='".$UOM1."'";
							$sql .= " , UOM2  ='".$UOM2."'";
							$sql .= "  ,conversion_factor='".$conversion."'";
							$sql .= " , pack_size='".$pack_size."'";
							$sql .= " , UOM3='".$UOM3."'";
							$sql .= " , conversion_factor_two='".$conversion_factor_two."'";
							$sql .= " , TD='".$TD."'";							
							$sql .= " , download_time=CURRENT_TIMESTAMP()";
							$sql .= " , vertical_value='".$vertical_value."' WHERE prod_code='".$prod_code_db."'";
							mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
						}
						else if($cl_stk_db!=$cl_stk)
						{
							$sql  = "UPDATE product_master ";
							$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";
							mysqli_query($link,$sql) or die(mysqli_error().".Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");
						}
						//For TT
						//array_push($duplicate_product,$dns_prod_code." \t".$prod_desc." \t".$product_group_code." \t".$product_sub_group_code);
				}
			}
		$rec_count++;
	  }
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for SKU Master.csv is wrong.";
		exit();
	}*/
	//exit();
	//For Employee CSV
	if(similar_file_exists("csv/$folderName/Employee master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Employee master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
			$lines = file($filename);
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				
				if($rec_count>=1)
				{ 
					$reporting_to_val='';
					$branch_code='';
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				    $data[]=$value;
				  
					$dns_employee_code=trim($data[0]);
					$employee_name=trim($data[1]);
					
					$branch_code_name=trim($data[2]);
					$vertical_value=trim($data[3]);
					$reporting_to=trim($data[4]);
					$email=trim($data[5]);
					$phone_no=trim($data[6]);
					$sale_access=trim($data[7]);
					$designation=trim($data[8]);
					$HQ=trim($data[9]);
					$state=trim($data[10]);
					$zone=trim($data[11]);
					$acedns=trim($data[12]);
					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";
						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code,'".$reporting_to."')";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')";
						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')";
					}
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					while($rowbranchcode=mysqli_fetch_assoc($rsbranchcode))
					{
						$branch_code=$branch_code.$rowbranchcode['branch_code'].',';
					}
					$branch_code=substr($branch_code,0,-1);

					if(vertical_fields=='yes'){
						$vertical_condition=" AND vertical_value='".$vertical_value."'";
					}
					else
					{
						$vertical_condition="";
					}
					
					if(providing_code=='yes'){
					 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($dns_employee_code)."' ".$vertical_condition."";
					}
					else
					{
					  $sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";
					}
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$countempnamechk=mysqli_num_rows($rsempnamechk);
					
					$rsreportingto=mysqli_query($link,$sqlreportingto);
					
					while($rowreportingto=mysqli_fetch_assoc($rsreportingto))
					{
						$reporting_to_val=$reporting_to_val.$rowreportingto['emp_code'].',';
					}
					$reporting_to_val=substr($reporting_to_val,0,-1);
					
					//exit();
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
						
						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						$sql .= " , dns_emp_code='".$dns_employee_code."'";
						$sql .= " , emp_name='".$employee_name."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , vertical_value='".$vertical_value."'";
						$sql .= " , reporting_to='".$reporting_to_val."'";
						$sql .= " , email='".addslashes($email)."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , sale_access='secondary'";
						$sql .= " , HQ='".$HQ."'";
						$sql .= " , designation='".$designation."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , state='".$state."'";
						$sql .= " , zone='".$zone."'";
						mysqli_query($link,$sql) or die(mysqli_error().".mysqli_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysqli_query($link,$sqlcp) or die(mysqli_error().".mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");
					}
					else
					{
						$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);
						$emp_code_db=$rowempnamechk['emp_code'];
						$sqlupdate  = "UPDATE employee_master ";
						$sqlupdate .= " SET branch_code='".$branch_code."'";
						$sqlupdate .= " , vertical_value='".$vertical_value."'";
						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";
						$sqlupdate .= " , email='".$email."'";
						$sqlupdate .= " , sale_access='secondary'";
						$sqlupdate .= " , HQ='".$HQ."'";
						$sqlupdate .= " , designation='".$designation."'";
						$sqlupdate .= " , acedns='".$acedns."'";
						$sqlupdate .= " , state='".$state."'";
						$sqlupdate .= " , zone='".$zone."'";
						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_code='".addslashes($emp_code_db)."'";
						mysqli_query($link,$sqlupdate) or die(mysqli_error().".Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");
					}
				}
				 $rec_count++;
			}
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Employee Master.csv is wrong.";
			exit();
		}*/
		
	//For Customer CSV
	if(similar_file_exists("csv/$folderName/Customer Master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Customer Master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			$lines = file($filename);
			$countroute=0;
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   $data[]=$value;
				  //print_r($data);
					
					$dns_customer_code =trim($data[0]);
					$customer_name	=trim($data[1]);
					$phone_no		=trim($data[2]);
					$dns_route_code	  =trim($data[3]);
					$route_name	  =trim($data[4]);  
					$emp_code_name		=trim($data[5]);
					//For VIPL employee only
					/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_code)."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);
					$emp_code=$rowempnamechk['emp_code'];*/
					$acedns		  =trim($data[6]);
					$credit_limit	=trim($data[7]);
					$credit_days	 =trim($data[8]);
					$current_balance =trim($data[9]);
					$black_list	  =trim($data[10]); 
					$TD	  		  =trim($data[11]);
					$branch_code_name =trim($data[12]);
					$customer_type   =trim($data[13]);
					$rds_tag   =trim($data[14]);
					$sauda_validity_period  =trim($data[15]);
					
					if(providing_code=='yes'){
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						$branch_code=$rowempcode['branch_code'];
					}
					else
					{
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						//exit();
						$branch_code=$rowempcode['branch_code'];
					}
					//$emp_code=$emp_code_name;
					$sqlrdscode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_tag)."' AND emp_code='".$emp_code."'";
					$rsrdscode=mysqli_query($link,$sqlrdscode);
					$rowrdscode=mysqli_fetch_assoc($rsrdscode);
					$rds_code=$rowrdscode['rds_code'];
					
					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";
					//$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
					$rsroutechk=mysqli_query($link,$sqlroutechk);
					$countroutechk=mysqli_num_rows($rsroutechk);
					if($countroutechk<1 && $route_name!='')
					{
						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
						$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
						$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
						$new_route_code=$rowmaxroutecode['new_route_code'];
						
						if($new_route_code=='')
						{
							$max_route_code='RT/1';
						}
						else
						{
							$max_route_code='RT/'.($new_route_code+1);
							//$max_route_code++;
						}
						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						//$sqlroute .= " , vertical_value='".$vertical_value."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlroute) or die(mysqli_error().".Internal DATA execution problem on route table.PLease contact aceDNS admin.");
						$route_code=$max_route_code;
					}
					else
					{
						$rowroutechk=mysqli_fetch_assoc($rsroutechk);
						$route_code=$rowroutechk['route_code'];
					}
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."' 
										AND route_code='".$route_code."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					$csv_row_count=$rec_count+1;
					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
						$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
						$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
						$max_customer_code=$rowmaxcustomercode['max_customer_code'];
						
						if($max_customer_code=='')
						{
							$max_customer_code='C/0000001';
						}
						else
						{
							$max_customer_code++;
						}
						$sql  = "insert into customer_master ";
						$sql .= " SET customer_code='".$max_customer_code."'";
						$sql .= " , dns_customer_code='".$dns_customer_code."'";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , branch_code='".addslashes($branch_code)."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , credit_days='".$credit_days."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , TD='".$TD."'";
						$sql .= " , rds_tag='".$rds_code."'";
						$sql .= " , cust_type='".$customer_type."'";
						$sql .= " , sauda_validity_period='".$sauda_validity_period."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						//exit();
						mysqli_query($link,$sql) or die(mysqli_error().".Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
						$customer_code=$max_customer_code;
						
						$sqlupdatecustomerstatus="UPDATE customer_master SET acedns='N',download_time=CURRENT_TIMESTAMP() 
													WHERE customer_name='".addslashes($customer_name)."' AND emp_code <> '".$emp_code."'";
						mysqli_query($link,$sqlupdatecustomerstatus);
					}
					else
					{
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$emp_code_db=$rowcustomernamechk['emp_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$credit_days_db=$rowcustomernamechk['credit_days'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$TD_db=$rowcustomernamechk['TD'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						$rds_tag_db=$rowcustomernamechk['rds_tag'];
						$branch_code_db=$rowcustomernamechk['branch_code'];
						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];
						
						if(($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD || $customer_type_db!=$customer_type 
						|| $rds_tag_db!=$rds_tag || $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!=$credit_days))
						{ 
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , black_list='".$black_list."'";
							$sqlupdated .= " , branch_code='".$branch_code."'";
							$sqlupdated .= " , TD='".$TD."'";
							$sqlupdated .= " , cust_type='".$customer_type."'";
							$sqlupdated .= " , credit_days='".$credit_days."'";
							$sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";
							$sqlupdated .= " , rds_tag='".$rds_code."',download_time=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."' AND route_code='".$route_code."'";
							mysqli_query($link,$sqlupdated) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
						if(($credit_limit_db!=$credit_limit))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET credit_limit='".$credit_limit."'";
							$sqlupdated .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."' AND route_code='".$route_code."'";
							mysqli_query($link,$sqlupdated) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
					}
				}
				 $rec_count++;
			}
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}*/
		
	//For Vendor Master CSV
	if(similar_file_exists("csv/$folderName/Vendor master.csv")!=false && strtoupper($folderName)=='RKBK')
	{
		$filename=similar_file_exists("csv/$folderName/Vendor master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
			
					if(!$double_coute_found && $char=="\"")
					{  
					
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
				
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;
			  //print_r($data);
			  
				$vendor_code=trim($data[0]);
				$vendor_name=trim($data[1]);
				$emp_code_name=trim($data[2]);
				$branch_code_name=trim($data[3]);
				$rds_code_name=trim($data[4]);
				
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];
				
				$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";
				$rsempnamechk=mysqli_query($link,$sqlempnamechk);
				$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);
				$emp_code=$rowempnamechk['emp_code'];

				$sqlrdscode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_code_name)."' AND emp_code='".$emp_code."'";
				$rsrdscode=mysqli_query($link,$sqlrdscode);
				$rowrdscode=mysqli_fetch_assoc($rsrdscode);
				$rds_code=$rowrdscode['rds_code'];
				
				$sqlvendornamechk="SELECT vendor_code FROM vendor_master WHERE vendor_name='".addslashes($vendor_name)."' 
									AND rds_code='".$rds_code."'";
				$rsvendornamechk=mysqli_query($link,$sqlvendornamechk);
				$countvendornamechk=mysqli_num_rows($rsvendornamechk);
				
				$csv_row_count=$rec_count+1;
				if($countvendornamechk<1)
				{
					$sqlmaxvendorcode="SELECT MAX(vendor_code) AS max_vendor_code FROM  vendor_master WHERE 1";
					$rsmaxvendorcode=mysqli_query($link,$sqlmaxvendorcode);
					$rowmaxvendorcode=mysqli_fetch_assoc($rsmaxvendorcode);
					$max_vendor_code=$rowmaxvendorcode['max_vendor_code'];
					
					if($max_vendor_code=='')
					{
						$max_vendor_code='V0001';
					}
					else
					{
						$max_vendor_code++;
					}

				$sqlvendor  = "insert into vendor_master SET ";
				$sqlvendor .= "  vendor_code='".mysqli_real_escape_string($max_vendor_code)."'";
				$sqlvendor .= "  ,dns_vendor_code='".mysqli_real_escape_string($vendor_code)."'";
				$sqlvendor .= "  ,vendor_name='".mysqli_real_escape_string($vendor_name)."'";
				$sqlvendor .= " , branch_code='".mysqli_real_escape_string($branch_code)."'";
				$sqlvendor .= " , rds_code='".mysqli_real_escape_string($rds_code)."'";
				$sqlvendor .= " , emp_code='".mysqli_real_escape_string($emp_code)."'";
				mysqli_query($link,$sqlvendor) or die(mysqli_error().".Internal error in Vendor master.csv.Please check.");
				}
				else
				{
					$sqlupvendor  = "update vendor_master SET ";
					$sqlupvendor .= "  dns_vendor_code='".mysqli_real_escape_string($vendor_code)."' WHERE vendor_name='".addslashes($vendor_name)."' 
									AND rds_code='".$rds_code."'";
					mysqli_query($link,$sqlupvendor) or die(mysqli_error().".Internal error in Vendor master.csv.Please check.");
				}
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Company master.csv is wrong.";
		exit();
	}	*/

	//For Outstanding CSV
	if(similar_file_exists("csv/$folderName/Outstanding.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Outstanding.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			$sqldelete="truncate outstanding";
			$rsdelete=mysqli_query($link,$sqldelete);
			$customeroutstandingmissmatchArr=array();
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
					$csv_row_count=$rec_count+1;
					$customer_code_name=trim($data[0]);
					if(providing_code=='yes')
					{
						$emp_code_name=trim($data[1]);
						$invoice_id=trim($data[2]);
						$date=trim($data[3]);
						$dateArr=explode('/',$date);
						if(strlen($dateArr[2])==2)
						{
							$year='20'.$dateArr[2];
						}
						else
						{
							$year=$dateArr[2];
						}
						$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];
						$invoice_amount=trim($data[4]);
						if(strpos($invoice_amount,',')!=false){
							//$invoicepos=strpos($invoice_amount,',');
						//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);
							$invoice_amount =str_replace(',','',$invoice_amount);
						}
						if(strpos($invoice_amount,' Cr')!=false){
							$invoice_amount='-'.$invoice_amount;
						}
						$due_amount=trim($data[5]);
						if(strpos($due_amount,',')!=false){
						//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);
						$due_amount =str_replace(',','',$due_amount);
						}
						if(strpos($due_amount,' Cr')!=false){
							$due_amount ='-'.$due_amount;
						}
						$vertical_value	  =trim($data[6]); 
					}
					else
					{
						//$route_code_name=trim($data[1]);
						$invoice_id=trim($data[1]);
						$date=trim($data[2]);
						$dateArr=explode('/',$date);
						if(strlen($dateArr[2])==2)
						{
							$year='20'.$dateArr[2];
						}
						else
						{
							$year=$dateArr[2];
						}
						$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];
						$invoice_amount=trim($data[3]);
						if(strpos($invoice_amount,',')!=false){
							//$invoicepos=strpos($invoice_amount,',');
						//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);
							$invoice_amount =str_replace(',','',$invoice_amount);
						}
						if(strpos($invoice_amount,' Cr')!=false){
							$invoice_amount='-'.$invoice_amount;
						}
						$due_amount=trim($data[4]);
						if(strpos($due_amount,',')!=false){
						//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);
						$due_amount =str_replace(',','',$due_amount);
						}
						if(strpos($due_amount,' Cr')!=false){
							$due_amount ='-'.$due_amount;
						}
						$vertical_value	  =trim($data[5]); 
					}
	
				   $sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' AND acedns='Y'";
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$countcustomercode=mysqli_num_rows($rscustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					
					/*if($countcustomercode <1 && !in_array($customer_code_name,$customeroutstandingmissmatchArr))
					{
						$customeroutstandingmissmatch='';
						$customeroutstandingmissmatch.=$customer_code_name.',';
						array_push($customeroutstandingmissmatchArr,$customer_code_name);
						//$lineexcel .= $customeroutstandingmissmatch."\n";
					}*/

					$sql  = "insert into outstanding ";
					$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";
					$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";
					$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";
					$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";
					
					mysqli_query($link,$sql) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");;
				}
				 $rec_count++;
			}
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Outstanding.csv is wrong.";
			exit();
		}*/
	//For MRP CSV
	if(similar_file_exists("csv/$folderName/MRP.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/MRP.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			$lines = file($filename);
			/*$sqldelete="truncate mrp";
			$rsdelete=mysqli_query($link,$sqldelete);*/
			
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				//print_r($data);
				  	$csv_row_count=$rec_count+1;
				    $branch_code_name=trim($data[0]);
					$prod_code_name=trim($data[1]);
					//$brand_code_name=trim($data[2]);
					//$brand_form_code_name=trim($data[3]);
					$dns_mrp_code=trim($data[2]);
					$mrp=trim($data[3]);
					if(strpos($mrp,',')!=false){
						$mrppos=strpos($mrp,',');
					$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);
					}
					$sale_rate=trim($data[4]);
					if(strpos($sale_rate,',')!=false){
						$sale_ratepos=strpos($sale_rate,',');
						$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);
					}

					$vertical_value=trim($data[5]);
					$UOM=trim($data[6]);
					if($mrp=='') $mrp=0;
					if($sale_rate=='') $sale_rate=0;
					
					/*if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
						//$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' 
								//AND product_group_code='".$brand_code_name."' AND product_sub_group_code='".$brand_form_code_name."'";
					}
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];*/
					$branch_code='';
					if(providing_code=='yes'){
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";
					}
					else
					{
						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
					}
					$rsprodcode=mysqli_query($link,$sqlprodcode);
					$rowprodcode=mysqli_fetch_assoc($rsprodcode);
					$prod_code=$rowprodcode['prod_code'];
					
					$sqlmrpchk="SELECT mrp,sale_rate FROM mrp WHERE product_code='".$prod_code."'";
					$rsmrpchk=mysqli_query($link,$sqlmrpchk);
					$countmrpchk=mysqli_num_rows($rsmrpchk);
					$csv_row_count=$rec_count+1;
					if($countmrpchk<1){
						$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
						$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
						$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
						$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
						
						if($max_mrp_code=='')
						{
							$max_mrp_code='1';
						}
						else
						{
							$max_mrp_code++;
						}
						$max_mrp_code='z'.$max_mrp_code;

						$sql  = "insert into mrp ";
						$sql .= " SET product_code='".$prod_code."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , dns_mrp_code='".$dns_mrp_code."'";
						$sql .= " , mrp='".mysqli_real_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
						$sql .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
						$sql .= " , UOM='".mysqli_real_escape_string($UOM)."'";
						$sql .= " , acedns='Y'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql)  or die(mysqli_error().".Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");
					}
					else
					{
						$rowmrpchk=mysqli_fetch_assoc($rsmrpchk);
						$mrp_db=$rowmrpchk['mrp'];
						$sale_rate_db=$rowmrpchk['sale_rate'];	
						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate){
							$sqlupdate  = "UPDATE mrp ";
							$sqlupdate .= " SET mrp='".mysqli_real_escape_string($mrp)."'";
							$sqlupdate .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."'";
							mysqli_query($link,$sqlupdate) or die(mysqli_error().".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
						}
					}
				}
				 $rec_count++;
			}
			
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for MRP.csv is wrong.";
			exit();
		}*/
		if($successval==1)
		{
			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlInsert))
			{
				echo $err = 'Zip file extracted and data has been uploaded successfully';
				$curdateserver=gmdate('Y-m-d H:i:s',strtotime('+330 minute'));
			
				/*$url="http://salempower.acedns.in/mailDatabaseDetails.php?nick_name=$nick_name";
				$url = str_replace(" ", '%20', $url);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
				$response = json_decode(curl_exec($ch));*/
			}
	  }
	}
?>