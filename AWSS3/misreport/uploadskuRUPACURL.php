<?php	
	require("adminUtils.php");
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
     if(similar_file_exists("../csv/RUPA/Sku master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/RUPA/Sku master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			$lines = file($filename);
			$duplicate_product=array();
			$branch_code_array=array();
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
					
					if(branch_wise_product == 'yes')
					{
						if($branch_code_name == '')
						{
							echo "Please provide valid branch code at row ".($csv_row_count+1);
							die;
						}
					}
					
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
					if($acedns =='')
					{
						echo "Please provide proper value for Acedns column at row ".($csv_row_count+1);
						die;
					}
					
					$black_list=trim($data[8]);
					if($black_list=='')
					{
						echo "Please provide proper value for Blacklist column at row ".($csv_row_count+1);
						die;
					}
					
					$vertical_value=trim($data[9]);
					$UOM1=trim($data[10]);
					$UOM2=trim($data[11]);
					$conversion=trim($data[12]);
					$pack_size=trim($data[13]);
					$UOM3=trim($data[14]);
					$conversion_factor_two=trim($data[15]);
					$conversion_factor_two=str_replace(',','',$conversion_factor_two);
					$TD=trim($data[16]);
					$focus=trim($data[17]);
					$weightage=trim($data[18]);
					$vat=trim($data[19]);
					$addl_vat=trim($data[20]);
					$freight_cost=trim($data[21]);
					
					//echo no_of_filter;
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
					}
					$rsbranchcode=mysql_query($sqlbranchcode);
					$rowbranchcode=mysql_fetch_array($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

						if(no_of_filter > 1){
						//Product group code checking start
						$sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."'";
						$rsprodgroupnamechk=mysql_query($sqlprodgroupnamechk);
						$countprodgroupnamechk=mysql_num_rows($rsprodgroupnamechk);
						if($countprodgroupnamechk<1){
							$sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master";
							$rsmaxproductgroupcode=mysql_query($sqlmaxproductgroupcode);
							$rowmaxproductgroupcode=mysql_fetch_array($rsmaxproductgroupcode);
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
							$sqlbrand .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlbrand) or array_push($error_array,"mysql_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
							$product_group_code=$max_product_group_code;
						}
						else
						{
							$rowprodgroupnamechk=mysql_fetch_array($rsprodgroupnamechk);
							$product_group_code=$rowprodgroupnamechk['product_group_code'];
							$vertical_value_db=$rowprodgroupnamechk['vertical_value'];
							if($vertical_value_db!=$vertical_value)
							{
								$sqlupdatebrand  = "UPDATE product_group_master SET ";
								$sqlupdatebrand .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE product_group_name='".addslashes($product_group_code_name)."'";
								mysql_query($sqlupdatebrand) or array_push($error_array,"mysql_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
							}
						}
						//Product group code checking end
					 }
					if(no_of_filter > 2){
						//Product sub group code checking start
						$sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($product_sub_group_code_name)."' 
												AND product_group_code='".$product_group_code."'";
						$rsprodsubgroupnamechk=mysql_query($sqlprodsubgroupnamechk);
						$countprodsubgroupnamechk=mysql_num_rows($rsprodsubgroupnamechk);
						if($countprodsubgroupnamechk<1){
							$sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master";
							$rsmaxproductsubgroupcode=mysql_query($sqlmaxproductsubgroupcode);
							$rowmaxproductsubgroupcode=mysql_fetch_array($rsmaxproductsubgroupcode);
							$max_product_sub_group_code=$rowmaxproductsubgroupcode['max_product_sub_group_code'];
							
							if($max_product_sub_group_code=='')
							{
								$max_product_sub_group_code='1';
							}
							else
							{
								$max_product_sub_group_code++;
							}
							$max_product_sub_group_code='BF'.$max_product_sub_group_code;
							$sqlbrandform  = "INSERT INTO product_sub_group_master SET ";
							$sqlbrandform .= "  product_sub_group_code='".mysql_real_escape_string($max_product_sub_group_code)."'";
							$sqlbrandform .= " , product_sub_group_name='".addslashes($product_sub_group_code_name)."'";
							$sqlbrandform .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sqlbrandform .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlbrandform);
							$product_sub_group_code=$max_product_sub_group_code;
						}
						else
						{
							$rowprodsubgroupnamechk=mysql_fetch_array($rsprodsubgroupnamechk);
							$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];
							$vertical_value_sub_group=$rowprodsubgroupnamechk['vertical_value'];
							if($vertical_value_sub_group!=$vertical_value)
							{
								$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";
								$sqlupdatebrandform .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP() WHERE 
														product_sub_group_name='".addslashes($product_sub_group_code_name)." AND product_group_code='".$product_group_code."'";
								mysql_query($sqlupdatebrandform);
							}
						}
						//Product sub group code checking end
					}
					if(no_of_filter > 3){
						//Product brand code checking start
						$sqlprodbrandnamechk="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".addslashes($product_brand_code_name)."'
												AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
						$rsprodbrandnamechk=mysql_query($sqlprodbrandnamechk);
						$countprodbrandnamechk=mysql_num_rows($rsprodbrandnamechk);
						if($countprodbrandnamechk<1){
							$sqlmaxproductbrandcode="SELECT MAX( CAST( SUBSTRING( product_brand_code, -(length( product_brand_code ) -2), length( product_brand_code ) -2 ) AS UNSIGNED ) ) AS max_product_brand_code from product_brand_master";
							$rsmaxproductbrandcode=mysql_query($sqlmaxproductbrandcode);
							$rowmaxproductbrandcode=mysql_fetch_array($rsmaxproductbrandcode);
							$max_product_brand_code=$rowmaxproductbrandcode['max_product_brand_code'];
							
							if($max_product_brand_code=='')
							{
								$max_product_brand_code='1';
							}
							else
							{
								$max_product_brand_code++;
							}
							$max_product_brand_code='BS'.$max_product_brand_code;
							$sqlbrandsubform  = "INSERT INTO product_brand_master SET ";
							$sqlbrandsubform .= "  product_brand_code='".mysql_real_escape_string($max_product_brand_code)."'";
							$sqlbrandsubform .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
							$sqlbrandsubform .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sqlbrandsubform .= " , product_brand_name='".addslashes($product_brand_code_name)."'";
							$sqlbrandsubform .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlbrandsubform);
							$product_brand_code=$max_product_brand_code;
						}
						else
						{
							$rowprodbrandnamechk=mysql_fetch_array($rsprodbrandnamechk);
							$product_brand_code=$rowprodbrandnamechk['product_brand_code'];
							$vertical_value_brand=$rowprodbrandnamechk['vertical_value'];
							if($vertical_value_brand!=$vertical_value)
							{
								$sqlupdatebrandsubform  = "UPDATE product_brand_master SET ";
								$sqlupdatebrandsubform .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrandsubform .= " , download_time=CURRENT_TIMESTAMP() 
															WHERE product_brand_name='".addslashes($product_brand_code_name)." 
															AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
								mysql_query($sqlupdatebrandsubform);
							}
						}
						//Product brand code checking end
					}
						if(branch_wise_product=='yes' || $folderName=='DNV' || $folderName=='SKIPPER')
						{
							$branch_code_condition= " AND branch_code='".$branch_code."'";
						}
						else
						{
							$branch_code_condition= "";
						}
						if(providing_code=='yes'){
							$sqlskunamechk="SELECT * FROM product_master WHERE  dns_prod_code='".$dns_prod_code."'".$branch_code_condition."";
						}
						else
						{
							$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'".$branch_code_condition." 
											AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
											AND product_brand_code='".$product_brand_code."'";
						}
						$rsskunamechk=mysql_query($sqlskunamechk);
						$countskunamechk=@mysql_num_rows($rsskunamechk);
						$rowskunamechk=@mysql_fetch_array($rsskunamechk);
						$updateflag=0;
						$insertflag=0;
						if($countskunamechk<1)
						{
							$sqlmaxskucode="SELECT MAX(prod_code) AS max_prod_code FROM  product_master WHERE 1";
							$rsmaxskucode=mysql_query($sqlmaxskucode);
							$rowmaxskucode=mysql_fetch_array($rsmaxskucode);
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
							$sql .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sql .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
							$sql .= " , product_brand_code='".mysql_real_escape_string($product_brand_code)."'";
							$sql .= " , cl_stk='".mysql_real_escape_string($cl_stk)."'";
							$sql .= " , acedns='".$acedns."'";
							$sql .= " , black_list='".$black_list."'";
							$sql .= " , vertical_value='".addslashes($vertical_value)."'";
							$sql .= " , UOM1='".$UOM1."'";
							$sql .= " , UOM2='".$UOM2."'";
							$sql .= " , pack_size='".$pack_size."'";
							$sql .= " , UOM3='".$UOM3."'";
							$sql .= " , conversion_factor_two='".$conversion_factor_two."'";
							$sql .= " , TD='".$TD."'";
							$sql .= " , conversion_factor='".$conversion."'";
							$sql .= " , focus='".$focus."'";
							$sql .= " , weightage='".$weightage."'";
							$sql .= " , vat='".$vat."'";
							$sql .= " , addl_vat='".$addl_vat."'";
							$sql .= " , freight_cost='".$freight_cost."'";
							$sql .= " , download_time=CURRENT_TIMESTAMP()";
							$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";
							//exit();
						mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
							$insertflag=1;
							if(branch_wise_cl_stk=='yes' || branch_wise_mrp=='yes')
							{
								$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_code ASC";
								$rsbranch=mysql_query($sqlbranch);
								while($rowbranch=mysql_fetch_array($rsbranch))
								{
									$branch_code_cl_stk=$rowbranch['branch_code'];
									if(branch_wise_cl_stk=='yes')
									{
										$sqlinsertstk  = "insert into branch_product_wise_stock SET ";
										$sqlinsertstk .= "  	branch_code='".mysql_real_escape_string($branch_code_cl_stk)."'";
										$sqlinsertstk .= " , product_code='".mysql_real_escape_string($max_prod_code)."'";
										$sqlinsertstk .= " , closing_stk='0'";
										$sqlinsertstk .= " , download_time=CURRENT_TIMESTAMP()";
										mysql_query($sqlinsertstk) or array_push($error_array,"mysql_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");
									}
								}
							}
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
							$focus_db=$rowskunamechk['focus'];
							$weightage_db=$rowskunamechk['weightage'];
							$vat_db=$rowskunamechk['vat'];
							$addl_vat_db=$rowskunamechk['addl_vat'];
							$freight_cost_db=$rowskunamechk['freight_cost'];
							$vertical_value_db=$rowskunamechk['vertical_value'];
							
							if(($cl_stk_db==$cl_stk) && ($acedns_db!=$acedns || $black_list_db!=$black_list 
								|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 
								|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion 
								|| $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD || $focus_db!=$focus || $weightage_db!=$weightage || $vat_db!=$vat || $addl_vat_db!=$addl_vat || $freight_cost_db!=$freight_cost))
							{
								$sql  = "UPDATE product_master ";
								$sql .= " SET branch_code='".$branch_code."'";
								$sql .= " , prod_desc='".addslashes($prod_desc)."'";
								$sql .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
								$sql .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
								$sql .= " , product_brand_code='".mysql_real_escape_string($product_brand_code)."'";
								$sql .= " , acedns='".$acedns."'";
								$sql .= " , black_list='".$black_list."'";
								$sql .= " , UOM1	 ='".$UOM1."'";
								$sql .= " , UOM2  ='".$UOM2."'";
								$sql .= "  ,conversion_factor='".$conversion."'";
								$sql .= " , UOM3='".$UOM3."'";
								$sql .= " , conversion_factor_two='".$conversion_factor_two."'";
								$sql .= " , TD='".$TD."'";
								$sql .= " , focus='".$focus."'";
								$sql .= " , weightage='".$weightage."'";
								$sql .= " , vat='".$vat."'";
								$sql .= " , addl_vat='".$addl_vat."'";
								$sql .= " , freight_cost='".$freight_cost."'";
								$sql .= " , download_time=CURRENT_TIMESTAMP()";
								$sql .= " , vertical_value='".addslashes($vertical_value)."' WHERE prod_code='".$prod_code_db."'";
								mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
								$updateflag=1;
							}
							else if($cl_stk_db!=$cl_stk)
							{
								$sql  = "UPDATE product_master ";
								$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";
								mysql_query($sql) or array_push($error_array,"mysql_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");
								$updateflag=1;
							}
						}
				}
		$rec_count++;
		}

		//Product group code checking start
			if(no_of_filter==2 || no_of_filter==3){
				$sqlgroupcodeproduct="SELECT product_group_code FROM product_master WHERE product_group_code NOT IN
									(SELECT product_group_code FROM product_group_master) GROUP BY product_group_code";
				$rsgroupcodeproduct=mysql_query($sqlgroupcodeproduct);
				$cntgroupcodeproduct=mysql_num_rows($rsgroupcodeproduct);
				if($cntgroupcodeproduct>0)
				{
					$groupcodeproduct='';
					while($rowgroupcodeproduct=mysql_fetch_array($rsgroupcodeproduct))
					{
						$groupcodeproduct=$groupcodeproduct.$rowgroupcodeproduct['product_group_code'].',';
					}
					$groupcodeproduct=substr($groupcodeproduct,0,-1);
					$errorgroupcodeproduct=$groupcodeproduct.' exists in Sku master but not exists in Brand Master.';
					array_push($error_array,$errorgroupcodeproduct);
				}
			}
		//Product group code checking end
		//Product sub group code checking start
			if(no_of_filter==3){
			$sqlsubgroupcodeproduct="SELECT product_sub_group_code FROM product_master WHERE product_sub_group_code NOT IN
			(SELECT product_sub_group_code FROM product_sub_group_master) GROUP BY product_sub_group_code";
			$rssubgroupcodeproduct=mysql_query($sqlsubgroupcodeproduct);
			$cntsubgroupcodeproduct=mysql_num_rows($rssubgroupcodeproduct);
			if($cntsubgroupcodeproduct>0)
			{
				$subgroupcodeproduct='';
				while($rowsubgroupcodeproduct=mysql_fetch_array($rssubgroupcodeproduct))
				{
					$subgroupcodeproduct=$subgroupcodeproduct.$rowsubgroupcodeproduct['product_sub_group_code'].',';
				}
				$subgroupcodeproduct=substr($subgroupcodeproduct,0,-1);
				$errorsubgroupcodeproduct=$subgroupcodeproduct.' exists in Sku master but not exists in Brand Form Master.';
				array_push($error_array,$errorsubgroupcodeproduct);
			}
		}
		//Product sub group code checking end
		/*foreach($duplicate_product as $duplicate_product_val)
		{
			$dupliacateproductval=$dupliacateproductval.$duplicate_product_val."\n";
		}
		//print_r($customeroutstandingmissmatchArr);
				$data = str_replace("\r","",$dupliacateproductval);
				
				header("Content-type: application/x-msdownload"); 
				header("Content-Disposition: attachment; filename=duplicateproduct.xls"); 
				header("Pragma: no-cache"); 
				header("Expires: 0"); 
				print "$data";*/
			$successval=1;
	}
	if($successval==1)
	{
		$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
		if(mysql_query($sqlInsert))
		{
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			
			
			$mailto='salesmis@rupa.co.in,exe.asst@rupa.co.in';
			//$mailto='';
		
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$date.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':'.$second;
			
			if(count($error_array)>0)
			{
				$mailsub='Sku data has been successfully uploaded to '.$nick_name.' with error(s) on '.$contentsdatetime;
				$mailbody='Sku data has been successfully uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
				
				for($i=0;$i<count($error_array);$i++){
					$mailbody.= "<b>$error_array[$i]</b><br /><br />";
				}	
			}
			else{
				$mailsub='Sku data has been successfully uploaded to '.$nick_name.' on '.$contentsdatetime;
				$mailbody='Sku data has been successfully uploaded to '.$nick_name.' database.';	
			}
			if($dupliacateproductval!=''){
				$mailbody.=$dupliacateproductval;
			}
			//$mailto='';			
			mail($mailto, $mailsub, $mailbody, $headers,'-facedns@coral.in');
		}
	}
?>
