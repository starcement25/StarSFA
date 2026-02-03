<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	require 'phpexcel/Classes/PHPExcel/IOFactory.php';
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$GLOBALS['show']=60;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
		$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	disphtml("main();");
	ob_end_flush();

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
function main()
{
	if($_REQUEST['mode']=="submit_sku_information")
	{
		$current_date=date('Y-m-d');
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		
		$dns_prod_code_arr=$_POST['dns_prod_code'];
		$prod_desc_arr=$_POST['prod_desc'];
 		$product_group_code_name_arr=$_POST['product_group_code_name'];
		$product_sub_group_code_name_arr=$_POST['product_sub_group_code_name'];
		$product_brand_code_name_arr=$_POST['product_brand_code_name'];
		$acedns_arr=$_POST['acedns'];
		$black_list_arr=$_POST['black_list'];
		$vertical_value_arr=$_POST['vertical_value'];
		$UOM1_arr=$_POST['UOM1'];
		$UOM2_arr=$_POST['UOM2'];
		$conversion_arr=$_POST['conversion'];
		//$refference_no=$_POST['refference_no'];
		$customer_code_array=array();
		$customer_name_array=array();
		$registration_id_array=array();
		$emp_code_array=array();
		$prod_code_array=array();
		$prod_desc_array=array();
		//$notificatiomessage="Hi,\nToday's billing details are\n";
		for($i=0;$i<count($dns_prod_code_arr);$i++)
		{	  
			$branch_code_name='';
			$dns_prod_code=trim($dns_prod_code_arr[$i]);
			$prod_desc=trim($prod_desc_arr[$i]);
			//$prod_desc=str_replace('~','"',$prod_desc);
			$product_group_code_name=trim($product_group_code_name_arr[$i]);
			$product_sub_group_code_name=trim($product_sub_group_code_name_arr[$i]);
			$product_brand_code_name=trim($product_brand_code_name_arr[$i]);
			$cl_stk='';
			$acedns=trim($acedns_arr[$i]);;
			$black_list=trim($black_list_arr[$i]);
			$vertical_value=trim($vertical_value_arr[$i]);
			$UOM1=trim($UOM1_arr[$i]);
			$UOM2=trim($UOM2_arr[$i]);
			$conversion=trim($conversion_arr[$i]);
			$pack_size='';
			$UOM3='';
			$conversion_factor_two='';
			$TD='';
			$focus='';
			$vat='';
			$pack_unit='';
			$prod_size='';
			
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
					$sqlbrand .= " , vertical_value='".addslashes($vertical_value)."'";
					$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
					$product_group_code=$max_product_group_code;
				}
				else
				{
					$rowprodgroupnamechk=mysqli_fetch_assoc($rsprodgroupnamechk);
					$product_group_code=$rowprodgroupnamechk['product_group_code'];
					$vertical_value_db=$rowprodgroupnamechk['vertical_value'];
					if($vertical_value_db!=$vertical_value)
					{
						$sqlupdatebrand  = "UPDATE product_group_master SET ";
						$sqlupdatebrand .= " vertical_value='".addslashes($vertical_value)."'";
						$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE product_group_name='".addslashes($product_group_code_name)."'";
						mysqli_query($link,$sqlupdatebrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
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
					$sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master";
					$rsmaxproductsubgroupcode=mysqli_query($link,$sqlmaxproductsubgroupcode);
					$rowmaxproductsubgroupcode=mysqli_fetch_assoc($rsmaxproductsubgroupcode);
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
					$sqlbrandform .= "  product_sub_group_code='".mysqli_real_escape_string($max_product_sub_group_code)."'";
					$sqlbrandform .= " , product_sub_group_name='".addslashes($product_sub_group_code_name)."'";
					$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
					$sqlbrandform .= " , vertical_value='".addslashes($vertical_value)."'";
					$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlbrandform);
					$product_sub_group_code=$max_product_sub_group_code;
				}
				else
				{
					$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);
					$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];
					$vertical_value_sub_group=$rowprodsubgroupnamechk['vertical_value'];
					if($vertical_value_sub_group!=$vertical_value)
					{
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
					$sqlmaxproductbrandcode="SELECT MAX( CAST( SUBSTRING( product_brand_code, -(length( product_brand_code ) -2), length( product_brand_code ) -2 ) AS UNSIGNED ) ) AS max_product_brand_code from product_brand_master";
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
					$max_product_brand_code='BS'.$max_product_brand_code;
					$sqlbrandsubform  = "INSERT INTO product_brand_master SET ";
					$sqlbrandsubform .= "  product_brand_code='".mysqli_real_escape_string($max_product_brand_code)."'";
					$sqlbrandsubform .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
					$sqlbrandsubform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
					$sqlbrandsubform .= " , product_brand_name='".addslashes($product_brand_code_name)."'";
					$sqlbrandsubform .= " , vertical_value='".addslashes($vertical_value)."'";
					$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlbrandsubform);
					$product_brand_code=$max_product_brand_code;
				}
				else
				{
					$rowprodbrandnamechk=mysqli_fetch_assoc($rsprodbrandnamechk);
					$product_brand_code=$rowprodbrandnamechk['product_brand_code'];
					$vertical_value_brand=$rowprodbrandnamechk['vertical_value'];
					if($vertical_value_brand!=$vertical_value)
					{
						$sqlupdatebrandsubform  = "UPDATE product_brand_master SET ";
						$sqlupdatebrandsubform .= " vertical_value='".addslashes($vertical_value)."'";
						$sqlupdatebrandsubform .= " , download_time=CURRENT_TIMESTAMP() 
													WHERE product_brand_name='".addslashes($product_brand_code_name)." 
													AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
						mysqli_query($link,$sqlupdatebrandsubform);
					}
				}
				//Product brand code checking end
			}
				if(branch_wise_product=='yes' || $folderName=='SKIPPER')
				{
					$branch_code_condition= " AND branch_code='".$branch_code."'";
				}
				else
				{
					$branch_code_condition= "";
				}
				if(providing_code=='yes' || $folderName=='HALDIRAM'){
					$sqlskunamechk="SELECT * FROM product_master WHERE  dns_prod_code='".$dns_prod_code."' AND acedns='Y'".$branch_code_condition."";
				}
				else
				{
					$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'".$branch_code_condition." 
									AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
									AND product_brand_code='".$product_brand_code."'";
				}
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
					$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
					$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
					$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";
					$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";
					$sql .= " , acedns='".strtoupper($acedns)."'";
					$sql .= " , black_list='".strtoupper($black_list)."'";
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
					$sql .= " , pack_unit='".$pack_unit."'";
					$sql .= " , prod_size='".$prod_size."'";
					$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";
					//exit();
				mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
					$insertflag=1;
					if(branch_wise_cl_stk=='yes' || branch_wise_mrp=='yes')
					{
						$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_code ASC";
						$rsbranch=mysqli_query($link,$sqlbranch);
						while($rowbranch=mysqli_fetch_assoc($rsbranch))
						{
							$branch_code_cl_stk=$rowbranch['branch_code'];
							if(branch_wise_cl_stk=='yes')
							{
								$sqlinsertstk  = "insert into branch_product_wise_stock SET ";
								$sqlinsertstk .= "  	branch_code='".mysqli_real_escape_string($branch_code_cl_stk)."'";
								$sqlinsertstk .= " , product_code='".mysqli_real_escape_string($max_prod_code)."'";
								$sqlinsertstk .= " , closing_stk='0'";
								$sqlinsertstk .= " , download_time=CURRENT_TIMESTAMP()";
								mysqli_query($link,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");
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
					$pack_unit_db=$rowskunamechk['pack_unit'];
					$prod_size_db=$rowskunamechk['prod_size'];
					
					if($acedns_db!=$acedns || $black_list_db!=$black_list || $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 
						|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion 
						|| $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD || $focus_db!=$focus || $weightage_db!=$weightage || $vat_db!=$vat || $addl_vat_db!=$addl_vat || $freight_cost_db!=$freight_cost || $pack_unit_db!=$pack_unit || $prod_size_db!=$prod_size)
					{
						$sql  = "UPDATE product_master ";
						$sql .= " SET branch_code='".$branch_code."'";
						$sql .= " , prod_desc='".addslashes($prod_desc)."'";
						$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
						$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
						$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";
						$sql .= " , acedns='".strtoupper($acedns)."'";
						$sql .= " , black_list='".strtoupper($black_list)."'";
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
						$sql .= " , pack_size='".$pack_size."'";
						$sql .= " , pack_unit='".$pack_unit."'";
						$sql .= " , prod_size='".$prod_size."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						$sql .= " , vertical_value='".addslashes($vertical_value)."' WHERE prod_code='".$prod_code_db."'";
						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
						$updateflag=1;
					}
					else if($cl_stk_db!=$cl_stk)
					{
						$sql  = "UPDATE product_master ";
						$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";
						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");
						$updateflag=1;
					}
				}
				if(branch_wise_product=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log
				{
					if(!in_array($branch_code,$branch_code_array))
					{
						array_push($branch_code_array,$branch_code);
						$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)";
						$rsbranchwiseemp=mysqli_query($link,$sqlbranchwiseemp);
						while($rowbranchwiseemp=mysqli_fetch_assoc($rsbranchwiseemp))
						{
							$emp_code_branchwise=$rowbranchwiseemp['emp_code'];
							modifyempdatadownloadlog($emp_code_branchwise,strtoupper($folderName));
						}
					}
				}//End For emp data download log
			if($successval==1)
			{
				mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");
			}
		}
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	if($_REQUEST['mode']=="submit_mrp_information")
	{
		/*echo '<pre>';
		print_r($_REQUEST);
		echo '</pre>';*/
		//exit();
		$current_date=date('Y-m-d');
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		
		$branch_code_name_arr=$_POST['branch_code_name'];
		$dns_prod_code_arr=$_POST['dns_prod_code'];
 		$mrp_arr=$_POST['mrp'];
		$sale_rate_arr=$_POST['sale_rate'];
		$vertical_value_arr=$_POST['vertical_value'];
		$acedns_arr=$_POST['acedns'];
		$distributor_price_arr=$_POST['distributor_price'];
		$ss_price_arr=$_POST['ss_price'];
		$depot_price_arr=$_POST['depot_price'];
		$UOM_arr=$_POST['UOM'];
		
		for($i=0;$i<count($dns_prod_code_arr);$i++)
		{	  
			$branch_code_name=trim($branch_code_name_arr[$i]);
			$prod_code_name=trim($dns_prod_code_arr[$i]);
			$dns_mrp_code='';
			$mrp=trim($mrp_arr[$i]);
			if(strpos($mrp,',')!=false){
				$mrppos=strpos($mrp,',');
				$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);
			}
			$sale_rate=trim($sale_rate_arr[$i]);
			if(strpos($sale_rate,',')!=false){
				$sale_ratepos=strpos($sale_rate,',');
				$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);
			}
			$vertical_value=trim($vertical_value_arr[$i]);
			$acedns=trim($acedns_arr[$i]);
			$ws_price=0;
			$distributor_price=trim($distributor_price_arr[$i]);
			$ss_price=trim($ss_price_arr[$i]);
			$depot_price=trim($depot_price_arr[$i]);
			$state_code_name='';
			$UOM=trim($UOM_arr[$i]);;
			
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
			if(uom_wise_mrp=='yes' && branch_wise_mrp=='yes')
			{
				$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."' AND branch_code='".$branch_code."'";
			}
			else if(uom_wise_mrp=='yes')
			{
				$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."'";
			}
			else if(branch_wise_mrp=='yes')
			{
				$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."' AND acedns='Y'";
			}
			else if(state_wise_mrp=='yes')
			{
				$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND state_code='".$state_code."'";
			}
			else
			{
				$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."'";
			}
			$rsmrpchk=mysqli_query($link,$sqlmrpchk);
			$countmrpchk=mysqli_num_rows($rsmrpchk);
			$csv_row_count=$rec_count+1;
			$insertflag=0;
			$updateflag=0;
			if($countmrpchk<1){
				$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
				$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
				$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
				$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
				
				if($max_mrp_code=='')
				{
					$max_mrp_code='001';
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
				$sql .= " , acedns='".mysqli_real_escape_string($acedns)."'";
				$sql .= " , state_code='".mysqli_real_escape_string($state_code)."'";
				$sql .= " , ws_rate='".mysqli_real_escape_string($ws_price)."'";
				$sql .= " , distributor_rate='".mysqli_real_escape_string($distributor_price)."'";
				$sql .= " , ss_rate='".mysqli_real_escape_string($ss_price)."'";
				$sql .= " , depot_rate='".mysqli_real_escape_string($depot_price)."'";
				$sql .= " , download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sql)  or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");
				//exit();
				$insertflag=1;
			}
			else
			{
				$rowmrpchk=mysqli_fetch_assoc($rsmrpchk);
				$mrp_db=$rowmrpchk['mrp'];
				$sale_rate_db=$rowmrpchk['sale_rate'];
				$acedns_db=$rowmrpchk['acedns'];
				$ws_price_db=$rowmrpchk['ws_rate'];
				$distributor_price_db=$rowmrpchk['distributor_rate'];
				$ss_price_db=$rowmrpchk['ss_rate'];
				$depot_price_db=$rowmrpchk['depot_rate'];
				$UOM_db=$rowmrpchk['UOM'];	
				if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate || $acedns_db!=$acedns || $ws_price_db!=$ws_price || $distributor_price_db!=$distributor_price || $ss_price_db!=$ss_price || $depot_price_db!=$depot_price ){
					$sqlupdate  = "UPDATE mrp ";
					$sqlupdate .= " SET mrp='".mysqli_real_escape_string($mrp)."'";
					$sqlupdate .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";
					$sqlupdate .= " , acedns='".mysqli_real_escape_string($acedns)."'";
					$sqlupdate .= " , ws_rate='".mysqli_real_escape_string($ws_price)."'";
					$sqlupdate .= " , distributor_rate='".mysqli_real_escape_string($distributor_price)."'";
					$sqlupdate .= " , ss_rate='".mysqli_real_escape_string($ss_price)."'";
					$sqlupdate .= " , depot_rate='".mysqli_real_escape_string($depot_price)."'";
					$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."' 
									AND branch_code='".$branch_code."' AND state_code='".$state_code."' AND UOM='".$UOM."'";
							
					mysqli_query($link,$sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
					$updateflag=1;
				}
			  }
			}
			if($successval==1)
			{
				mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");
			}
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
?>
<script language="JavaScript">
function checkFields()
{
	if(document.form_add_xls.zip_file.value=="")
	{
		alert("Please browse the ZIP file first...");
		document.form_add_xls.zip_file.focus();
		return false;
	}
	
	var fname = document.form_add_xls.zip_file.value.toUpperCase();
	var pos1 = fname.indexOf(".ZIP");
	
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use ZIP only...");
		document.form_add_xls.zip_file.focus();
		return false;	
	}
	return true;	
}
</script>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" >
			<table width="70%" align="center" cellpadding="5" cellspacing="2">
            	 <tr> 
                    <td width="90%" align="center" class="ERR"><font size="+2"><u>Upload Sku and Mrp Data</u></font></td>
            	</tr>
            </table>
          </td>
    </tr>       
    <tr> 
        <td height="30"  align="left">
        <table width="100%">
            <tr> 
                <td width="90%" align="center" class="ERR"><?=$GLOBALS['msg']?></td>
                <td width="" align="right"></td>
            </tr>
            <tr> 
                <td width="90%" align="center" class="ERR" nowrap="nowrap">
                <?php 
                $errr_msg=$GLOBALS['error_msg'];
                $error_msgArr=explode('#',$errr_msg);
                if(count($error_msgArr)>0){
                    for($i=0;$i<count($error_msgArr);$i++){
                        echo "<b>$error_msgArr[$i]</b><br /><br />";
                    }
                }
                ?>
                </td>
                <td width="" align="right"></td>
            </tr>
        </table></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
<table width="70%" align="center" cellpadding="5" cellspacing="2" class="border">
	<form name="form_add_xls" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="xls_upload">
		
		<tr class="TDHEAD" > 
			<td colspan="10">Upload Zip File</td>
		</tr>
			
		<tr> 
		  <td align="right">Zip File*</td>
			<td width="2%">:</td>
			<td><input type="file" name="zip_file" class="" ><br/ ><strong><font color="#FF0000">[Extension will be .zip]</font></strong></td>
		</tr>
		<tr>
            <td>&nbsp;</td>
            <td >&nbsp;</td>
            <td>		
                <input type="submit" name="Add" value="Add" > 
                <!--input type="button" name="back" value=" Back " onClick="javascript:document.location='adminMain.php'"-->
            </td>
		</tr>
		<tr class="TDHEAD_SUB"> 
			<td colspan="10">&nbsp;</td>
		</tr>
	</form>
</table>
</td>
</tr>
</table><br /><br /><br />
<?php
if($_REQUEST['mode']=="xls_upload"){
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	$error_array=array();
	if (!file_exists("../csv/$folderName")){
		mkdir("../csv/$folderName");
		chmod("../csv/$folderName", 0777);
	}
	// Get array of all source files
	$files = scandir("../csv/$folderName");
	// Identify directories
	$source = "../csv/$folderName/";
	$destination = "../csv/$folderName/filebkup/";
	// Cycle through all source files
	foreach ($files as $file) {
	  if (in_array($file, array(".",".."))) continue;
	  // If we copied this successfully, mark it for deletion
	  if (@copy($source.$file, $destination.$file)) {
		$delete[] = $source.$file;
	  }
	}
	// Delete all successfully-copied files
	foreach ($delete as $file) {
	  unlink($file);
	}
	$upload_dir="../csv/$folderName/";
	if(file_exists($_FILES['zip_file']['tmp_name']))
	{
		$file_name = $_FILES['zip_file']['name'];
		$tmp_name=$_FILES['zip_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
	    move_uploaded_file($tmp_name,$upload_file);
		$zip = new ZipArchive;
		if ($zip->open($upload_file)) {
			$zip->extractTo("../csv/$folderName/");
			$zip->close();
		} 
	 }
	  //For sku master xls
	   if(similar_file_exists("../csv/$folderName/sku master.xlsx")!=false || similar_file_exists("../csv/$folderName/sku master.xls")!=false)
	    {
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		//$lines = file($filename);
		if(similar_file_exists("../csv/$folderName/sku master.xlsx")!=false)
		{
			$inputfilename = "../csv/$folderName/sku master.xlsx";
		}
		if(similar_file_exists("../csv/$folderName/sku master.xls")!=false)
		{
			$inputfilename = "../csv/$folderName/sku master.xls";
		}
		$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
		$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
		$objPHPExcel = $objReader->load($inputfilename);
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
		  <tr class="TDHEAD" align="center" id="head_main">
			<td colspan="12" class="TDHEAD" align="center">Sku master</td>
		  </tr>
		  <tr class="TDHEAD_SUB" align="center" id="head_main">
		   <td>SI</td><td>prod_code</td><td>prod_desc</td><td>brand_code</td><td>brand_form_code</td><td>brand_sub_form_code</td><td>acedns</td><td>black_list</td><td>vertical_value</td><td>UOM1</td><td>UOM2</td><td>Conversion</td></tr>';
				//  Get worksheet dimensions
				$sheet = $objPHPExcel->getSheet(0); 
				$highestRow = $sheet->getHighestRow(); 
				$highestColumn = $sheet->getHighestColumn();
				//  Loop through each row of the worksheet in turn
				for ($row = 1; $row <= $highestRow; $row++)
				{ 
					//  Read a row of data into an array
					$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
					//  Insert row data array into your database of choice here
					if($rec_count>=1)
					{ 
						$csv_row_count=$rec_count+1;
						$branch_code_name = trim($rowData[0][0]);
						$dns_prod_code=$sheet->getCellByColumnAndRow(1,$row)->getFormattedValue();
						$prod_desc=trim($rowData[0][2]);
						$product_group_code_name=trim($rowData[0][3]);
						$product_sub_group_code_name=trim($rowData[0][4]);
						$product_brand_code_name=trim($rowData[0][5]);
						$cl_stk=trim($rowData[0][6]);
						if(strpos($cl_stk,',')!=false){
							$stkpos=strpos($cl_stk,',');
							$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
						}
						$acedns=trim($rowData[0][7]);
						$black_list=trim($rowData[0][8]);
						$vertical_value=trim($rowData[0][9]);
						$UOM1=trim($rowData[0][10]);
						$UOM2=trim($rowData[0][11]);
						$conversion=trim($rowData[0][12]);
						$pack_size=trim($rowData[0][13]);
						$UOM3=trim($rowData[0][14]);
						$conversion_factor_two=trim($rowData[0][15]);
						$conversion_factor_two=str_replace(',','',$conversion_factor_two);
						$TD=trim($rowData[0][16]);
						$focus=trim($rowData[0][17]);
						$vat=trim($rowData[0][18]);
						$pack_unit=trim($rowData[0][19]);
						$prod_size=trim($rowData[0][20]);
						if(providing_code=='yes')
						{
							if($dns_prod_code=='')
							{
								array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Prod_Code)");
							}
						}
						if($acedns =='')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (acedns)");
						}
					  if($black_list=='')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (black_list)");
						}
						/*if($vertical_value=='')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (vertical_value)");
						}*/
					  $tabledatacsv.="<input type=\"hidden\" name=\"dns_prod_code[]\" value=\"$dns_prod_code\">
									<input type=\"hidden\" name=\"prod_desc[]\" value=\"$prod_desc\">
									<input type=\"hidden\" name=\"product_group_code_name[]\" value=\"$product_group_code_name\">
									<input type=\"hidden\" name=\"product_sub_group_code_name[]\" value=\"$product_sub_group_code_name\">
									<input type=\"hidden\" name=\"product_brand_code_name[]\" value=\"$product_brand_code_name\">
									<input type=\"hidden\" name=\"acedns[]\" value=\"$acedns\">
									<input type=\"hidden\" name=\"black_list[]\" value=\"$black_list\">
									<input type=\"hidden\" name=\"vertical_value[]\" value=\"$vertical_value\">
									<input type=\"hidden\" name=\"UOM1[]\" value=\"$UOM1\">
									<input type=\"hidden\" name=\"UOM2[]\" value=\"$UOM2\">
									<input type=\"hidden\" name=\"conversion[]\" value=\"$conversion\">
									<tr id=\"tab\">
									<td>".$count."</td>
									<td>".$dns_prod_code."</td>
									<td>".$prod_desc."</td>
									<td>".$product_group_code_name."</td>
									<td>".$product_sub_group_code_name."</td>
									<td>".$product_brand_code_name."</td>
									<td>".$acedns."</td>
									<td>".$black_list."</td><td>".$vertical_value."</td><td>".$UOM1."</td><td>".$UOM2."</td><td>".$conversion."</td>
									</tr>";	
					}
					$rec_count++;
					$count++;
				}
		if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"12\"><font size=\"+2\"><u>Sku master</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"12\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
			echo $tabledatafinal=$tabledata.$tabledatacsv."<tr><td colspan='12' align='center'><input type='hidden' name='mode' value='submit_sku_information' /><input type='submit' name='submit1' value='Final Upload' /><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminExcelReadProductData.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
	}
	
	if(similar_file_exists("../csv/$folderName/mrp.xlsx")!=false || similar_file_exists("../csv/$folderName/mrp.xls")!=false || similar_file_exists("../csv/$folderName/MRP.xlsx")!=false || similar_file_exists("../csv/$folderName/MRP.xls")!=false)
	    {
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		//$lines = file($filename);
		if(file_exists("../csv/$folderName/mrp.xlsx"))
		{
			$inputfilename = "../csv/$folderName/mrp.xlsx";
		}
		if(file_exists("../csv/$folderName/mrp.xls"))
		{
			$inputfilename = "../csv/$folderName/mrp.xls";
		}
		if(file_exists("../csv/$folderName/MRP.xlsx"))
		{
			$inputfilename = "../csv/$folderName/MRP.xlsx";
		}
		if(file_exists("../csv/$folderName/MRP.xls"))
		{
			$inputfilename = "../csv/$folderName/MRP.xls";
		}
		$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
		$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
		$objPHPExcel = $objReader->load($inputfilename);
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
		  <tr class="TDHEAD" align="center" id="head_main">
			<td colspan="10" class="TDHEAD" align="center">Mrp</td>
		  </tr>
		  <tr class="TDHEAD_SUB" align="center" id="head_main">
		   <td>SI</td><td>Branch code</td><td>prod_desc/prod_code</td><td>mrp</td><td>Sale Rate</td><td>vertical_value</td><td>acedns</td><td>distributor price</td><td>SS price</td><td>Depot price</td></tr>';
				//  Get worksheet dimensions
				$sheet = $objPHPExcel->getSheet(0); 
				$highestRow = $sheet->getHighestRow(); 
				$highestColumn = $sheet->getHighestColumn();
				//  Loop through each row of the worksheet in turn
				for ($row = 1; $row <= $highestRow; $row++)
				{ 
					//  Read a row of data into an array
					$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
					//  Insert row data array into your database of choice here
					if($rec_count>=1)
					{ 
						$csv_row_count=$rec_count+1;
						$branch_code_name = trim($rowData[0][0]);
						$dns_prod_code=$sheet->getCellByColumnAndRow(1,$row)->getFormattedValue();
						$dns_mrp_code=trim($rowData[0][2]);
						$mrp=trim($rowData[0][3]);
						if(strpos($mrp,',')!=false){
							$mrppos=strpos($mrp,',');
							$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);
						}
						$sale_rate=trim($rowData[0][4]);
						if(strpos($sale_rate,',')!=false){
							$sale_ratepos=strpos($sale_rate,',');
							$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);
						}
						$vertical_value=trim($rowData[0][5]);
						$acedns=trim($rowData[0][8]);
						$ws_price=trim($rowData[0][9]);
						$distributor_price=trim($rowData[0][10]);
						$ss_price=trim($rowData[0][11]);
						$depot_price=trim($rowData[0][12]);
						$state_code_name=trim($rowData[0][13]);
						$UOM=trim($rowData[0][14]);
						
						if(providing_code=='yes'){
							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
							$sqlstatecode="SELECT state_code FROM state_master WHERE dns_state_code='".$state_code_name."'";
						}
						else
						{
							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
							$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_code_name."%'";
						}
						$rsbranchcode=mysqli_query($link,$sqlbranchcode);
						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
						$branch_code=$rowbranchcode['branch_code'];
						
						$rsstatecode=mysqli_query($link,$sqlstatecode);
						$rowstatecode=mysqli_fetch_assoc($rsstatecode);
						$state_code=$rowstatecode['state_code'];
						if($branch_code=='' && branch_wise_mrp == 'yes')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Branch code)");
						}
						if(providing_code=='yes'){
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."'";
						}
						else
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($dns_prod_code)."'";
						}
						$rsprodcode=mysqli_query($link,$sqlprodcode);
						$rowprodcode=mysqli_fetch_assoc($rsprodcode);
						$prod_code=$rowprodcode['prod_code'];
						if($prod_code =='')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (prod_code)");
						}
					  if($acedns=='')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (acedns)");
						}
						/*if($vertical_value=='')
						{
							array_push($error_array,"Error @Row (".$csv_row_count.") Column : (vertical_value)");
						}*/
					  $tabledatacsv.="<input type=\"hidden\" name=\"branch_code_name[]\" value=\"$branch_code_name\">
									<input type=\"hidden\" name=\"dns_prod_code[]\" value=\"$dns_prod_code\">
									<input type=\"hidden\" name=\"mrp[]\" value=\"$mrp\">
									<input type=\"hidden\" name=\"sale_rate[]\" value=\"$sale_rate\">
									<input type=\"hidden\" name=\"vertical_value[]\" value=\"$vertical_value\">
									<input type=\"hidden\" name=\"acedns[]\" value=\"$acedns\">
									<input type=\"hidden\" name=\"distributor_price[]\" value=\"$distributor_price\">
									<input type=\"hidden\" name=\"ss_price[]\" value=\"$ss_price\">
									<input type=\"hidden\" name=\"depot_price[]\" value=\"$depot_price\">
									<input type=\"hidden\" name=\"UOM[]\" value=\"$UOM\">
									<tr id=\"tab\">
										<td>".$count."</td>
										<td>".$branch_code_name."</td>
										<td>".$dns_prod_code."</td>
										<td>".$mrp."</td>
										<td>".$sale_rate."</td>
										<td>".$vertical_value."</td>
										<td>".$acedns."</td>
										<td>".$distributor_price."</td>
										<td>".$ss_price."</td>
										<td>".$depot_price."</td>
									</tr>";	
					}
					$rec_count++;
					$count++;
				}
		if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"10\"><font size=\"+2\"><u>Mrp</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"10\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
			echo $tabledatafinal=$tabledata.$tabledatacsv."<tr><td colspan='10' align='center'><input type='hidden' name='mode' value='submit_mrp_information' /><input type='submit' name='submit1' value='Final Upload' /><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminExcelReadProductData.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
	}
	/*else
	{
		echo $successval="Naming convention for billing information.xls is wrong.";
		exit();
	}*/ 
	if($successval==1)
	{
		$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlInsert))

		{
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			//$mailto='kuntald@coral.in';
			$mailto='';
		
			if(count($error_array)>0)
			{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' with error(s) on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
				
				for($i=0;$i<count($error_array);$i++){
					$mailbody.= "<b>$error_array[$i]</b><br /><br />";
				}	
			}
			else{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database.';	
			}
			if($dupliacateproductval!=''){
				$mailbody.=$dupliacateproductval;
			}
			//$mailto='';			
			if(mail($mailto, $mailsub, $mailbody, $headers,'-facedns@coral.in'))
			{
				if(count($error_array)>0)
				{
					$error_string=implode('#',$error_array);
					$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully with the following error(s).';
				}
				else{
					$GLOBALS['msg'] = '<b>Zip file extracted and data has been uploaded successfully</b>';
				}
				$GLOBALS['error_msg']=$error_string;
				/*$error_msgArr=explode('#',$GLOBALS['error_msg']);
					if(count($error_msgArr)>0){
						for($i=0;$i<count($error_msgArr);$i++){
							echo "<b>$error_msgArr[$i]</b><br /><br />";
						}
					}*/
				disphtml("main();");
			}
			else
			{
				echo $GLOBALS['msg'] = "Error in mail sending.";
				disphtml("main();");
			}
			//echo $err = 'Zip file extracted and data has been uploaded successfully';
		}
		else 
		{
			echo $GLOBALS['msg'] = "Problem with uploading Zip file";
			disphtml("main();");
		}
	}
  }
}
?>