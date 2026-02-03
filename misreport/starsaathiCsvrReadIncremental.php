<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
//error_reporting(E_ALL ^ E_NOTICE);

/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 
 
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name'])=='SCHOOL')
	{
		require("adminUtils_school.php");
	}
	else{
	require("adminUtils.php");
	}
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
	if($_REQUEST['mode']=="csv_upload")				csv_upload();
	else    										disphtml("main();");
ob_end_flush();
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
//require("include/functions.php");
require("include/config-email-setup.php");
if($_REQUEST['mode']=='csv_upload')
{
	csv_upload();
}
else
{
	main();
}*/

//require("include/config.php");
//require("include/config-setup.php");


function main()
{
?>
<script language="JavaScript">
function checkFields()
{
	if(document.form_add_CSV.zip_file.value=="")
	{
		alert("Please browse the ZIP file first...");
		document.form_add_CSV.zip_file.focus();
		return false;
	}	
	var fname = document.form_add_CSV.zip_file.value.toUpperCase();
	var pos1 = fname.indexOf(".ZIP");
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use ZIP only...");
		document.form_add_CSV.zip_file.focus();
		return false;	
	}
	return true;	
}
</script>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
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
	<form name="form_add_CSV" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="csv_upload">
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
                <input type="submit" name="Add" value="Add" onClick="return check();"> 
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
</table>
<?php
}
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
function csv_upload(){
	require("include/config.php");
require("include/config-setup.php");
    require("include/dbcon.php");
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	$error_array=array();
	$upload_master_table_array=array();
	if ( !file_exists("../csv/$folderName")){
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


		if(similar_file_exists("../csv/$folderName/Sku master.csv")!=false)

		{

			if($folderName=='RUPA')

			{

				$filesize=(filesize("$_SERVER[DOCUMENT_ROOT]/csv/$folderName/sku master.csv") * .0009765625) * .0009765625;// MB

				if($filesize >1)

				{

					header("location:uploadskuRUPA.php");

					exit();

				}

			}

			if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

			{

				$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='product_master'";

				$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

				$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

				$need_update=$rowchkupdateinfo['need_update'];

				if($need_update=='yes')

				{

					echo "Previous update process is going on sku master. Please try some time later.";

					die;

				}

			}

			$filename=similar_file_exists("../csv/$folderName/Sku master.csv");

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

				//$data="";
				$data = array();

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

							//$data[]=$value;
							array_push($data,$value);

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

					// $data[]=$value;
					 array_push($data,$value);

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

					$vat=trim($data[18]);

					$pack_unit=trim($data[19]);

					$prod_size=trim($data[20]);

					$lead_time=trim($data[21]);

					$buffer_level=trim($data[22]);

					$max_level_marketing=trim($data[23]);

					$UOM4=trim($data[24]);

					$UOM5=trim($data[25]);

					$gross_weight=trim($data[26]);

					$fg_rm=trim($data[27]);

					$oil_category=trim($data[28]);

					$alias=trim($data[29]);

					$hsn_sac=trim($data[30]);

					$packing_realization=trim($data[31]);

					$state_name=trim($data[32]);
					$prod_full_name=trim($data[33]);

				   //For Gross weight calculation

				   if(strtoupper($folderName)=='ASL')

					{

						$sql_BOM= "SELECT* FROM(SELECT prod_code,material_name,usage_qty,multiple_single FROM packing_BOM WHERE 

									prod_code='".$dns_prod_code."'  

									ORDER BY datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 1";

						$res_BOM = mysqli_query($link,$sql_BOM);

						while($row_BOM = mysqli_fetch_assoc($res_BOM)){

							$prod_code = $row_BOM['prod_code'];

							$material_name = $row_BOM['material_name'];

							$usage_qty = $row_BOM['usage_qty'];

							$multiple_single = $row_BOM['multiple_single'];

							

							$sqlselpackmatprice="SELECT weight FROM packing_material_master WHERE material_name='".addslashes($material_name)."' 

										ORDER BY datetime DESC LIMIT 0,1";

							$resselpackmatprice = mysqli_query($link,$sqlselpackmatprice);

							$rowselpackmatprice = mysqli_fetch_assoc($resselpackmatprice);

							if(strtoupper($multiple_single)=='M')

							{

								$weight = ($rowselpackmatprice['weight']*$UOM4);

							}

							else

							{

								$weight = $rowselpackmatprice['weight'];

							}

							${total_weight.$dns_prod_code}=${total_weight.$dns_prod_code}+$weight;

						}

						$gross_weight=${total_weight.$dns_prod_code}+($conversion_factor_two*1000);						

						$sqlinsertupdategweight=" , gross_weight='".$gross_weight."'";	

					}

					else

					{

						$gross_weight='';

						$sqlinsertupdategweight="";	

					}

					//echo no_of_filter;

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

					

					$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_name."%'";

					$rsstatecode=mysqli_query($link,$sqlstatecode);

					$rowstatecode=mysqli_fetch_assoc($rsstatecode);

					$state_code=$rowstatecode['state_code'];



						if(no_of_filter > 1){

						//Product group code checking start

						if(vertical_fields=='yes')

						{

						   $sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."' AND 	vertical_value='".$vertical_value."'";

						}

						else

						{

						  $sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."'";

						}

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

							//mysqli_query($link,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");

							$product_group_code=$max_product_group_code;

						}

						else

						{

							$rowprodgroupnamechk=mysqli_fetch_assoc($rsprodgroupnamechk);

							$product_group_code=$rowprodgroupnamechk['product_group_code'];

						}

						/*else

						{

							$vertical_value_db=$rowprodgroupnamechk['vertical_value'];

							if($vertical_value_db!=$vertical_value)

							{

								$sqlupdatebrand  = "UPDATE product_group_master SET ";

								$sqlupdatebrand .= " vertical_value='".addslashes($vertical_value)."'";

								$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE product_group_name='".addslashes($product_group_code_name)."'";

								mysqli_query($link,$sqlupdatebrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");

							}

						}*/

						//Product group code checking end

					 }

					 //echo no_of_filter;

					if(no_of_filter > 2){

						//Product sub group code checking start

						if(vertical_fields=='yes')

						{

						   $sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($product_sub_group_code_name)."' 

												AND product_group_code='".$product_group_code."' AND 	vertical_value='".$vertical_value."'";

						}

						else

						{

							$sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($product_sub_group_code_name)."' 

												AND product_group_code='".$product_group_code."'";

						}

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

							//mysqli_query($link,$sqlbrandform);

							$product_sub_group_code=$max_product_sub_group_code;

							

							

						}

						else

						{

							$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);

							$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];

						}

						/*else

						{

							$vertical_value_sub_group=$rowprodsubgroupnamechk['vertical_value'];

							if($vertical_value_sub_group!=$vertical_value)

							{

								$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";

								$sqlupdatebrandform .= " vertical_value='".addslashes($vertical_value)."'";

								$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP() WHERE 

														product_sub_group_name='".addslashes($product_sub_group_code_name)." AND product_group_code='".$product_group_code."'";

								mysqli_query($link,$sqlupdatebrandform);

							}

						}*/

						//Product sub group code checking end

					}

											//exit();



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

							//mysqli_query($link,$sqlbrandsubform);

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

								//mysqli_query($link,$sqlupdatebrandsubform);

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

						if(state_wise_product=='yes' || strtoupper($folderName)=='NIMBUS')

						{

							$state_code_condition= " AND state_code='".$state_code."'";

						}

						else

						{

							$state_code_condition= "";

						}

						if(providing_code=='yes' || $folderName=='HALDIRAM'){

							$sqlskunamechk="SELECT * FROM product_master WHERE  dns_prod_code='".$dns_prod_code."'".$branch_code_condition.$state_code_condition."";

						}

						else

						{

							$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'".$branch_code_condition.$state_code_condition." 

											AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 

											AND product_brand_code='".$product_brand_code."'";

						}

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

							$sql .= " , state_code='".$state_code."'";

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

							$sql .= " , lead_time='".$lead_time."'";

							$sql .= " , buffer_level='".$buffer_level."'";					

							$sql .= " , UOM4='".addslashes($UOM4)."'";

							$sql .= " , fg_rm='".addslashes($fg_rm)."'";

							$sql .= " , oil_category='".addslashes($oil_category)."'";

							$sql .= " , alias='".addslashes($alias)."'";

							$sql .= " , hsn_sac='".addslashes($hsn_sac)."'";
							$sql .= " , prod_full_name='".addslashes($prod_full_name)."'";
							$sql .= " , packing_realization='".addslashes($packing_realization)."'";
							$sql .= " , UOM5='".addslashes($UOM5)."'".$sqlinsertupdategweight;
							$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";

							//echo $sql."<br>";

							//exit();

						//$res2 = mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");

						/*if(!$res2){

									$new_error = "added ".mysqli_error();

									array_push($error_array,$new_error);

								}*/

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

										//mysqli_query($link,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");

									}

									/*if(branch_wise_mrp=='yes')

									{

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

				

										$sqlinsertmrp  = "insert into mrp ";

										$sqlinsertmrp .= " SET product_code='".mysqli_real_escape_string($max_prod_code)."'";

										$sqlinsertmrp .= " , branch_code='".mysqli_real_escape_string($branch_code_cl_stk)."'";

										$sqlinsertmrp .= " , mrp_code='".$max_mrp_code."'";

										$sqlinsertmrp .= " , dns_mrp_code=''";

										$sqlinsertmrp .= " , mrp='0'";

										$sqlinsertmrp .= " , sale_rate='0'";

										$sqlinsertmrp .= " , vertical_value='".addslashes($vertical_value)."'";

										$sqlinsertmrp .= " , UOM=''";

										$sqlinsertmrp .= " , download_time=CURRENT_TIMESTAMP()";

										mysqli_query($link,$sqlinsertmrp)  or  array_push($error_array,"mysqli_error().Internal error occurs in addition of mrp.Please check.");

									}*/

								}

							}

							/*if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && branch_wise_mrp=='no')

							{

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

								$sqlinsertmrp  = "insert into mrp ";

								$sqlinsertmrp .= " SET product_code='".mysqli_real_escape_string($max_prod_code)."'";

								$sqlinsertmrp .= " , branch_code=''";

								$sqlinsertmrp .= " , mrp_code='".$max_mrp_code."'";

								$sqlinsertmrp .= " , dns_mrp_code=''";

								$sqlinsertmrp .= " , mrp='0'";

								$sqlinsertmrp .= " , sale_rate='0'";

								$sqlinsertmrp .= " , vertical_value='".addslashes($vertical_value)."'";

								$sqlinsertmrp .= " , UOM=''";

								$sqlinsertmrp .= " , download_time=CURRENT_TIMESTAMP()";

								mysqli_query($link,$sqlinsertmrp)  or  array_push($error_array,"mysqli_error().Internal error occurs in addition of mrp.Please check.");

							}*/

						}

						else

						{

							$cl_stk_db=$rowskunamechk['cl_stk'];

							$branch_code_db=$rowskunamechk['branch_code'];

							$state_code_db=$rowskunamechk['state_code'];

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

							$lead_time_db=$rowskunamechk['lead_time'];

							$buffer_level_db=$rowskunamechk['buffer_level'];

							$max_level_marketing_db=$rowskunamechk['max_level_marketing'];

							$UOM4_db=$rowskunamechk['UOM4'];

							$UOM5_db=$rowskunamechk['UOM5'];

							$gross_weight_db=$rowskunamechk['gross_weight'];

							$fg_rm_db=$rowskunamechk['fg_rm'];

							$oil_category_db=$rowskunamechk['oil_category'];

							$alias_db=$rowskunamechk['alias'];

							$hsn_sac_db=$rowskunamechk['hsn_sac'];

							$packing_realization_db=$rowskunamechk['packing_realization'];
							$prod_full_name_db=$rowskunamechk['prod_full_name'];							

							if($acedns_db!=$acedns || $black_list_db!=$black_list 

								|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 

								|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion 

								|| $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD || $focus_db!=$focus || $weightage_db!=$weightage || $vat_db!=$vat || $addl_vat_db!=$addl_vat || $freight_cost_db!=$freight_cost || $pack_unit_db!=$pack_unit 

								|| $prod_size_db!=$prod_size || $lead_time_db!=$lead_time || $buffer_level_db!=$buffer_level 

								|| $max_level_marketing_db!=$max_level_marketing || $UOM4_db!=$UOM4 || $UOM5_db!=$UOM5 

								|| $gross_weight_db!=$gross_weight || $fg_rm_db!=$fg_rm || $oil_category_db!=$oil_category || $alias_db!=$alias || $hsn_sac_db!=$hsn_sac || $packing_realization_db!=$packing_realization || $prod_full_name_db!=$prod_full_name || $state_code_db!=$state_code)

							{

								$sql  = "UPDATE product_master ";

								$sql .= " SET branch_code='".$branch_code."'";

								$sql .= " , prod_desc='".addslashes($prod_desc)."'";

								$sql .= " , state_code='".addslashes($state_code)."'";

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

								$sql .= " , lead_time='".$lead_time."'";

								$sql .= " , buffer_level='".$buffer_level."'";

								$sql .= " , max_level_marketing='".$max_level_marketing."'";

								$sql .= " , UOM4='".$UOM4."'";

								$sql .= " , UOM5='".$UOM5."'";

								$sql .= " , gross_weight='".$gross_weight."'";

								$sql .= " , fg_rm='".addslashes($fg_rm)."'";

								$sql .= " , oil_category='".addslashes($oil_category)."'";

								$sql .= " , alias='".addslashes($alias)."'";

								$sql .= " , hsn_sac='".addslashes($hsn_sac)."'";
								$sql .= " , prod_full_name='".addslashes($prod_full_name)."'";
								$sql .= " , packing_realization='".addslashes($packing_realization)."'";

								$sql .= " , download_time=CURRENT_TIMESTAMP()";

								$sql .= " , vertical_value='".addslashes($vertical_value)."' WHERE prod_code='".$prod_code_db."'";

								//echo $sql."<br>";

								//$res2 = mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");

								

								/*if(!$res2){

									$new_error = "added ".mysqli_error();

									array_push($error_array,$new_error);

								}*/

								$updateflag=1;

							}

							else if($cl_stk_db!=$cl_stk)

							{

								$sql  = "UPDATE product_master ";

								$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";

								//mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");

								$updateflag=1;

							}

						}

						if(strtoupper($folderName)=='ASL')

						{

							$sqlupd="UPDATE customer_product_relation SET download_time=CURRENT_TIMESTAMP() 

									WHERE oil_category='".$product_group_code_name."'";

							//mysqli_query($link,$sqlupd);		

						}

						//exit();   

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

					//For TT

					//array_push($duplicate_product,$dns_prod_code." \t".$prod_desc." \t".$product_group_code." \t".$product_sub_group_code);



				}

			   if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

			   {

				  $product_ins_fields = array(

						'dns_prod_code' => $dns_prod_code,

						'branch_code' => $branch_code_name,

						'prod_desc' => $prod_desc,

						'product_group_code_name' => $product_group_code_name,

						'acedns' => $acedns,

						'black_list' => $black_list,

					);

				  $product_in_final_array[]=$product_ins_fields;		

				  $product_ins_fields_string = http_build_query($product_in_final_array); 

				}

			$rec_count++;

		}

		//exit();

		if(branch_wise_product=='no')//Start For emp data download log with no branch tagging

		{

			$emp_code='';

			modifyempdatadownloadlog($emp_code,strtoupper($folderName));

		}//End For emp data download log with no branch tagging



		//Product group code checking start

			if(no_of_filter==2 || no_of_filter==3){

				$sqlgroupcodeproduct="SELECT product_group_code FROM product_master WHERE product_group_code NOT IN

									(SELECT product_group_code FROM product_group_master) GROUP BY product_group_code";

				$rsgroupcodeproduct=mysqli_query($link,$sqlgroupcodeproduct);

				$cntgroupcodeproduct=mysqli_num_rows($rsgroupcodeproduct);

				if($cntgroupcodeproduct>0)

				{

					$groupcodeproduct='';

					while($rowgroupcodeproduct=mysqli_fetch_assoc($rsgroupcodeproduct))

					{

						$groupcodeproduct=$groupcodeproduct.$rowgroupcodeproduct['product_group_code'].',';

					}

					$groupcodeproduct=substr($groupcodeproduct,0,-1);

					if($folderName=='RUPA')

					{

						$errorgroupcodeproduct='There are many brand exists in Sku master but not exists in Brand Master.';

					}

					else

					{

						$errorgroupcodeproduct=$groupcodeproduct.' exists in Sku master but not exists in Brand Master.';

					}

					array_push($error_array,$errorgroupcodeproduct);

				}

			}

		//Product group code checking end

		//Product sub group code checking start

			if(no_of_filter==3){

			$sqlsubgroupcodeproduct="SELECT product_sub_group_code FROM product_master WHERE product_sub_group_code NOT IN

			(SELECT product_sub_group_code FROM product_sub_group_master) GROUP BY product_sub_group_code";

			$rssubgroupcodeproduct=mysqli_query($link,$sqlsubgroupcodeproduct);

			$cntsubgroupcodeproduct=mysqli_num_rows($rssubgroupcodeproduct);

			if($cntsubgroupcodeproduct>0)

			{

				$subgroupcodeproduct='';

				while($rowsubgroupcodeproduct=mysqli_fetch_assoc($rssubgroupcodeproduct))

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

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		  {

			array_push($upload_master_table_array,'product_master');

		  }

	   $successval=1;

	}

		

	/*else

	{

		echo $successval="Naming convention for SKU Master.csv is wrong.";

		exit();

	}*/

	//exit();	

	//For Branch wise closing stock CSV

	if(similar_file_exists("../csv/$folderName/Branch closing stock.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Branch closing stock.csv");

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

			//$data="";
			$data = array();

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

						//$data[]=$value;
						array_push($data,$value);

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   //$data[]=$value;
			   array_push($data,$value);

			  //print_r($data);

			  

				$dns_branch_code_name=trim($data[0]);

				$prod_code_name=trim($data[1]);

				$brand_code_name=trim($data[2]);

				$brand_form_code_name=trim($data[3]);

				$brand_sub_form_code_name=trim($data[4]);

				$cl_stk=trim($data[5]);

				if($cl_stk=='') $cl_stk=0;

				if(strpos($cl_stk,',')!=false){

				   $cl_stk =str_replace(',','',$cl_stk);

				}

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$brand_code_name."'";

				$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];



				$sqlproductsubgroupcode="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".$brand_form_code_name."' 

										AND product_group_code='".$product_group_code."'";

				$rsproductsubgroupcode=mysqli_query($link,$sqlproductsubgroupcode);

				$rowproductsubgroupcode=mysqli_fetch_assoc($rsproductsubgroupcode);

				$product_sub_group_code=$rowproductsubgroupcode['product_sub_group_code'];



				$sqlproductbrandcode="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".$brand_sub_form_code_name."' 

										AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."'";

				$rsproductbrandcode=mysqli_query($link,$sqlproductbrandcode);

				$rowproductbrandcode=mysqli_fetch_assoc($rsproductbrandcode);

				$product_brand_code=$rowproductbrandcode['product_brand_code'];



				if(providing_code=='yes'){

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code_name."'";

					$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND 

									product_group_code='".addslashes($product_group_code)."' AND product_sub_group_code='".addslashes($product_sub_group_code)."' 

									AND product_brand_code='".addslashes($product_brand_code)."'";

				}

				else

				{

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($dns_branch_code_name)."'";

					$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' AND 

									product_group_code='".addslashes($product_group_code)."' AND product_sub_group_code='".addslashes($product_sub_group_code)."' 

									AND product_brand_code='".addslashes($product_brand_code)."'";

				} 

				$rsbranchcode=mysqli_query($link,$sqlbranchcode);

				$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				$branch_code=$rowbranchcode['branch_code'];

				$rsproductcode=mysqli_query($link,$sqlproductcode);

				$rowproductcode=mysqli_fetch_assoc($rsproductcode);

				$product_code=$rowproductcode['prod_code'];



				$sqlclstkchk="SELECT branch_code,closing_stk FROM branch_product_wise_stock WHERE branch_code='".$branch_code."' 

							AND product_code='".$product_code."'";

				$rsclstkchk=mysqli_query($link,$sqlclstkchk);

				$countstkchk=mysqli_num_rows($rsclstkchk);

				$rowclstkchk=mysqli_fetch_assoc($rsclstkchk);

				

				$csv_row_count=$rec_count+1;

				if($countstkchk<1)

				{

					$sqlinsertstk  = "insert into branch_product_wise_stock SET ";

					$sqlinsertstk .= "  	branch_code='".mysqli_real_escape_string($branch_code)."'";

					$sqlinsertstk .= " , product_code='".mysqli_real_escape_string($product_code)."'";

					$sqlinsertstk .= " , closing_stk='".mysqli_real_escape_string($cl_stk)."'";

					$sqlinsertstk .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($link,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");

				}

				else

				{

					$cl_stk_db=$rowclstkchk['closing_stk'];

					if($cl_stk!=$cl_stk_db)

					{

						$sqlupdatestk  = "UPDATE branch_product_wise_stock SET ";

						$sqlupdatestk .= "  	closing_stk='".mysqli_real_escape_string($cl_stk)."'";

						$sqlupdatestk .= ",  download_time=CURRENT_TIMESTAMP()";

						$sqlupdatestk .= "  WHERE branch_code='".mysqli_real_escape_string($branch_code)."' AND product_code='".mysqli_real_escape_string($product_code)."'";

						mysqli_query($link,$sqlupdatestk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN");

					}

				}

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


	if(similar_file_exists("../csv/$folderName/broker destination.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/broker destination.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		{
			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='broker_destination'";
			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);
			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);
			$need_update=$rowchkupdateinfo['need_update'];
			if($need_update=='yes')
			{
				echo "Previous update process is going on broker destination. Please try some time later.";
				die;
			}
		}
//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			//$data="";
			$data = array();
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
						//$data[]=$value;
						array_push($data,$value);
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   //$data[]=$value;
			   array_push($data,$value);
			  //print_r($data);
				$broker_code=trim($data[0]);
				$destination_code_name=trim($data[1]);
				$acedns=trim($data[2]);
				$sqlbrokernamechk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($broker_code)."'";
				$rsbrokernamechk=mysqli_query($link,$sqlbrokernamechk);
				$rowbrokernamechk=mysqli_fetch_assoc($rsbrokernamechk);
				$broker_id=$rowbrokernamechk['broker_id'];
				
				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";
				$rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);
				$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
				
				$sqlbrokerdestination="SELECT broker_id FROM sp_destination WHERE broker_id='".addslashes($broker_id)."' AND destination_code='".$destination_code."'";
				$rsbrokerdestination=mysqli_query($link,$sqlbrokerdestination);
				$countbrokerdestination=mysqli_num_rows($rsbrokerdestination);
				if($countbrokerdestination<1 )
					{
						$sqlbrokerdestination  = "insert into sp_destination ";
						$sqlbrokerdestination .= " SET broker_id='".$broker_id."'";
						$sqlbrokerdestination .= " ,destination_code='".$destination_code."'";
						$sqlbrokerdestination .= " ,acedns='".$acedns."'";
						$sqlbrokerdestination .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrokerdestination) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on broker destination table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlbrokerdestinationupd = "update sp_destination ";
						$sqlbrokerdestinationupd .= " SET acedns='".$acedns."'";
						$sqlbrokerdestinationupd .= " , download_time=CURRENT_TIMESTAMP() WHERE broker_id='".addslashes($broker_id)."' AND destination_code='".$destination_code."'";
						mysqli_query($link,$sqlbrokerdestinationupd);
					}
			   }
			   if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			   {
				  $broker_destination_ins_fields = array(
							'broker_code' => $broker_code,
							'destination_code_name' => $destination_code_name,
							'acedns' => $acedns,
						);
				  $broker_destination_ins_final_array[]=$broker_destination_ins_fields;		
				  $broker_destination_ins_fields_string = http_build_query($broker_destination_ins_final_array); 
				}
			 $rec_count++;
		}
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		  {
			array_push($upload_master_table_array,'broker_destination');
		  }		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Company master.csv is wrong.";
		exit();
	}	*/




	//For FACILITATOR MASTER CSV

	if(similar_file_exists("../csv/$folderName/Facilitator master.csv")!=false)
	{

		$filename=similar_file_exists("../csv/$folderName/Facilitator master.csv");

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

			//$data="";
			$data = array();

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

						//$data[]=$value;
						array_push($data,$value);

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   //$data[]=$value;
			   array_push($data,$value);

			  //print_r($data);



				$facilitator_name=trim($data[0]);

				$emp_code_name =trim($data[1]);

				$f_type =trim($data[2]);

				$firm_name =trim($data[3]);

				$f_address =trim($data[4]);

				$f_pin =trim($data[5]);

				$f_sub_area =trim($data[6]);

				$f_area =trim($data[7]);

				$mobile_no =trim($data[8]);

				$email_id =trim($data[9]);

				$dob =trim($data[10]);

				$annniversary =trim($data[11]);

				$acedns =trim($data[12]);

				$branch_code_name =trim($data[13]);

				

				if(providing_code=='yes'){

					$emp_code_name=str_replace(")","",$emp_code_name);

					$emp_code_name=substr($emp_code_name,-6,6);

					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

					$rsempcode=mysqli_query($link,$sqlempcode);

					$rowempcode=mysqli_fetch_assoc($rsempcode);

					$emp_code=$rowempcode['emp_code'];

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

					$rsbranchcode=mysqli_query($link,$sqlbranchcode);

					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

					$branch_code=$rowbranchcode['branch_code'];

				}

				else

				{

					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

					$rsempcode=mysqli_query($link,$sqlempcode);

					$rowempcode=mysqli_fetch_assoc($rsempcode);

					$emp_code=$rowempcode['emp_code'];

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";

					$rsbranchcode=mysqli_query($link,$sqlbranchcode);

					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

					$branch_code=$rowbranchcode['branch_code'];

				}

				//$sqlfnamechk="SELECT * FROM facilitator_master WHERE facilitator_name='".addslashes($facilitator_name)."' AND emp_code='".$emp_code."'";
				$sqlfnamechk="SELECT * FROM facilitator_master WHERE facilitator_name='".addslashes($facilitator_name)."'";

				$rsfnamechk=mysqli_query($link,$sqlfnamechk);

				$countfnamechk=mysqli_num_rows($rsfnamechk);

					

				$csv_row_count=$rec_count+1;

				if($countfnamechk<1)

				{

					$sqlmaxfcode="SELECT MAX(f_code) AS max_f_code FROM  facilitator_master WHERE f_code like 'F%'";

					$rsmaxfcode=mysqli_query($link,$sqlmaxfcode);

					$rowmaxfcode=mysqli_fetch_assoc($rsmaxfcode);

					$max_f_code=$rowmaxfcode['max_f_code'];

					

					if($max_f_code=='')

					{

						$max_f_code='F00001';

					}

					else

					{

						$max_f_code++;

					}

					$sqlfacilitator  = "insert into facilitator_master ";

					$sqlfacilitator .= " SET f_code='".$max_f_code."'";

					$sqlfacilitator .= " ,facilitator_name='".addslashes($facilitator_name)."'";

					$sqlfacilitator .= " , emp_code='".$emp_code."'";

					$sqlfacilitator .= " ,f_type='".$f_type."'";

					$sqlfacilitator .= " ,firm_name='".addslashes($firm_name)."'";

					$sqlfacilitator .= " ,f_address='".addslashes($f_address)."'";

					$sqlfacilitator .= " ,	f_pin='".$f_pin."'";

					$sqlfacilitator .= " ,f_sub_area='".$f_sub_area."'";

					$sqlfacilitator .= " ,f_area='".$f_area."'";

					$sqlfacilitator .= " ,mobile_no='".$mobile_no."'";

					$sqlfacilitator .= " , email_id='".$email_id."'";

					$sqlfacilitator .= " , dob='".$dob."'";

					$sqlfacilitator .= " , annniversary='".$annniversary."'";

					$sqlfacilitator .= " ,branch_code='".$branch_code."'";

					$sqlfacilitator .= " , acedns='".$acedns."'";

					$sqlfacilitator .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($link,$sqlfacilitator) or array_push($error_array,"mysqli_error().Internel error occurrs @row $csv_row_count on facilitator master.csv.Please check.");

				}

				else

				{

					$rowfnamechk=mysqli_fetch_assoc($rsfnamechk);

					$f_code_db=$rowfnamechk['f_code'];

					$sqlupdated  = "update facilitator_master ";

					$sqlupdated .= "  SET facilitator_name='".addslashes($facilitator_name)."'";

					$sqlupdated .= " , emp_code='".$emp_code."'";

					$sqlupdated .= " ,f_type='".$f_type."'";

					$sqlupdated .= " ,firm_name='".addslashes($firm_name)."'";

					$sqlupdated .= " ,f_address='".addslashes($f_address)."'";

					$sqlupdated .= " ,f_sub_area='".$f_sub_area."'";

					$sqlupdated .= " ,f_area='".$f_area."'";

					$sqlupdated .= " ,	f_pin='".$f_pin."'";

					$sqlupdated .= " ,mobile_no='".$mobile_no."'";

					$sqlupdated .= " , email_id='".$email_id."'";

					$sqlupdated .= " , dob='".$dob."'";

					$sqlupdated .= " , annniversary='".$annniversary."'";

					$sqlupdated .= " , branch_code='".$branch_code."'";

					$sqlupdated .= " , acedns='".$acedns."'";

					$sqlupdated .= " , download_time=CURRENT_TIMESTAMP()";

					$sqlupdated .= " WHERE f_code='".$f_code_db."'";

					mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on facilitator master.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}
	//For FACILITATOR MASTER STAR CSV
	if(similar_file_exists("../csv/$folderName/fecilitator master.csv")!=false)
	{

		$filename=similar_file_exists("../csv/$folderName/fecilitator master.csv");

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

			//$data="";
			$data = array();

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

						//$data[]=$value;
						array_push($data,$value);

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   //$data[]=$value;
			   array_push($data,$value);

			  //print_r($data);

				$dns_f_code=trim($data[0]);
				$facilitator_name =trim($data[1]);
				$emp_code =trim($data[2]);
				$emp_name =trim($data[3]);
				$f_type =trim($data[4]);
				$designation =trim($data[5]);
				$firm_name =trim($data[6]);
				$f_address =trim($data[7]);
				$f_pin =trim($data[8]);
				$f_district =trim($data[9]);
				$f_area =trim($data[10]);
				$mobile_no =trim($data[11]);
				$whatsapp_no =trim($data[12]);
				$email_id =trim($data[13]);
				$dob =trim($data[14]);
				$annniversary =trim($data[15]);
				$acedns =trim($data[16]);
				$branch =trim($data[17]);

				$sqlfnamechk="SELECT * FROM facilitator_master WHERE dns_f_code='".addslashes($dns_f_code)."' ";
				$rsfnamechk=mysqli_query($link,$sqlfnamechk);
				$countfnamechk=mysqli_num_rows($rsfnamechk);
				$csv_row_count=$rec_count+1;

				if($countfnamechk<1)
				{
					$sqlmaxfcode="SELECT MAX(f_code) AS max_f_code FROM  facilitator_master WHERE f_code like 'F%'";
					$rsmaxfcode=mysqli_query($link,$sqlmaxfcode);
					$rowmaxfcode=mysqli_fetch_assoc($rsmaxfcode);
					$max_f_code=$rowmaxfcode['max_f_code'];
					if($max_f_code=='')
					{
						$max_f_code='F00001';
					}
					else
					{
						$max_f_code++;
					}
					$sqlfacilitator  = "insert into facilitator_master ";
					$sqlfacilitator .= " SET f_code='".$max_f_code."'";
					$sqlfacilitator .= " ,dns_f_code='".$dns_f_code."'";
					$sqlfacilitator .= " ,facilitator_name='".addslashes($facilitator_name)."'";
					$sqlfacilitator .= " , emp_code='".$emp_code."'";
					$sqlfacilitator .= " , emp_name='".$emp_name."'";
					$sqlfacilitator .= " ,f_type='".$f_type."'";
					$sqlfacilitator .= " ,designation='".$designation."'";
					$sqlfacilitator .= " ,firm_name='".addslashes($firm_name)."'";
					$sqlfacilitator .= " ,f_address='".addslashes($f_address)."'";
					$sqlfacilitator .= " ,	f_pin='".$f_pin."'";
					$sqlfacilitator .= " ,	f_district='".$f_district."'";
					$sqlfacilitator .= " ,f_area='".$f_area."'";
					$sqlfacilitator .= " ,mobile_no='".$mobile_no."'";
					$sqlfacilitator .= " ,whatsapp_no='".$whatsapp_no."'";
					$sqlfacilitator .= " , email_id='".$email_id."'";
					$sqlfacilitator .= " , dob='".$dob."'";
					$sqlfacilitator .= " , annniversary='".$annniversary."'";
					$sqlfacilitator .= " , acedns='".$acedns."'";
					$sqlfacilitator .= " ,branch='".$branch."'";
					$sqlfacilitator .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlfacilitator) or array_push($error_array,"mysqli_error().Internel error occurrs @row $csv_row_count on fecilitator master.csv.Please check.");
				}
				else
				{
					$rowfnamechk=mysqli_fetch_assoc($rsfnamechk);
					$f_code_db=$rowfnamechk['f_code'];
					$sqlupdated  = "update facilitator_master ";
					$sqlupdated .= "  SET facilitator_name='".addslashes($facilitator_name)."'";
					$sqlupdated .= " , emp_code='".$emp_code."'";
					$sqlupdated .= " , emp_name='".$emp_name."'";
					$sqlupdated .= " ,f_type='".$f_type."'";
					$sqlupdated .= " ,designation='".$designation."'";
					$sqlupdated .= " ,firm_name='".addslashes($firm_name)."'";
					$sqlupdated .= " ,f_address='".addslashes($f_address)."'";
					$sqlupdated .= " ,	f_pin='".$f_pin."'";
					$sqlupdated .= " ,	f_district='".$f_district."'";
					$sqlupdated .= " ,f_area='".$f_area."'";
					$sqlupdated .= " ,mobile_no='".$mobile_no."'";
					$sqlupdated .= " ,whatsapp_no='".$whatsapp_no."'";
					$sqlupdated .= " , email_id='".$email_id."'";
					$sqlupdated .= " , dob='".$dob."'";
					$sqlupdated .= " , annniversary='".$annniversary."'";
					$sqlupdated .= " , acedns='".$acedns."'";
					$sqlupdated .= " ,branch='".$branch."'";
					$sqlupdated .= " , download_time=CURRENT_TIMESTAMP()";
					$sqlupdated .= " WHERE f_code='".$f_code_db."'";
					mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on fecilitator master.csv.Please check.");
				}
			}

			 $rec_count++;

		}

		$successval=1;

	}


	  //For Broker Master CSV

		if(similar_file_exists("../csv/$folderName/Broker master.csv")!=false)
		{

			$filename=similar_file_exists("../csv/$folderName/Broker master.csv");
			$rec_count = 0;
			$ins_count = 0;

			$err = "";

			$updatecount=0;

			/*if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			{

				$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='broker_master'";

				$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

				$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

				$need_update=$rowchkupdateinfo['need_update'];

				if($need_update=='yes')

				{

					echo "Previous update process is going on broker master. Please try some time later.";

					die;

				}

			}*/

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

			$lines = file($filename);

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				//$data="";
				$data = array();

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

							//$data[]=$value;
							array_push($data,$value);

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   //$data[]=$value;
				   array_push($data,$value);

				  //print_r($data);

					$dns_broker_id=trim($data[0]);

					$broker_name=trim($data[1]);

					$contact_person=trim($data[2]);

					$mail_id=trim($data[3]);

					$phone_no=trim($data[4]);

					$acedns=trim($data[5]);

					if($providing_code=='yes')
					{

						$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($dns_broker_id)."'";

					}

					else

					{

						$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE broker_name='".addslashes($broker_name)."'";

					}

					$rsbrokerchk=mysqli_query($link,$sqlbrokerchk);

					$countbrokerchk=mysqli_num_rows($rsbrokerchk);

					$rowbrokerchk=mysqli_fetch_assoc($rsbrokerchk);

					$csv_row_count=$rec_count+1;

					if($countbrokerchk<1)

					{

						$sqlmaxbrokercode="SELECT MAX(broker_id) AS max_broker_id FROM  broker_master WHERE 1";

						$rsmaxbrokercode=mysqli_query($link,$sqlmaxbrokercode);

						$rowmaxbrokercode=mysqli_fetch_assoc($rsmaxbrokercode);

						$max_broker_id=$rowmaxbrokercode['max_broker_id'];

						

						if($max_broker_id=='')

						{

							$max_broker_id='BR0001';

						}

						else

						{

							$max_broker_id++;

						}

	

						$sqlbroker  = "insert into broker_master SET ";

						$sqlbroker .= "  broker_id='".mysqli_real_escape_string($max_broker_id)."'";

						$sqlbroker .= "  ,broker_name='".mysqli_real_escape_string($broker_name)."'";

						$sqlbroker .= " , dns_broker_id='".mysqli_real_escape_string($dns_broker_id)."'";

						$sqlbroker .= " , contact_person='".mysqli_real_escape_string($contact_person)."'";

						$sqlbroker .= " , mail_id='".mysqli_real_escape_string($mail_id)."'";

						$sqlbroker .= " , phone_no='".mysqli_real_escape_string($phone_no)."'";

						$sqlbroker .= " , acedns='".mysqli_real_escape_string($acedns)."'";

						$sqlbroker .= " , download_time=CURRENT_TIMESTAMP()";

						//mysqli_query($link,$sqlbroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");
					}

					else
					{

						$broker_id_db=$rowbrokerchk['broker_id'];

						$sqlupdatebroker  = "UPDATE broker_master SET ";

						$sqlupdatebroker .= "  broker_name='".mysqli_real_escape_string($link,$broker_name)."'";

						$sqlupdatebroker .= " , contact_person='".mysqli_real_escape_string($link,$contact_person)."'";

						$sqlupdatebroker .= " , mail_id='".mysqli_real_escape_string($link,$mail_id)."'";

						$sqlupdatebroker .= " , phone_no='".mysqli_real_escape_string($link,$phone_no)."'";

						$sqlupdatebroker .= " , dns_broker_id='".mysqli_real_escape_string($link,$dns_broker_id)."'";

						$sqlupdatebroker .= " , acedns='".mysqli_real_escape_string($link,$acedns)."',download_time=CURRENT_TIMESTAMP()";

						$sqlupdatebroker .= " WHERE broker_id='".mysqli_real_escape_string($link,$broker_id_db)."'";

						//mysqli_query($link,$sqlupdatebroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");

					}

				}

				if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			   {

				  $broker_ins_fields = array(

							'dns_broker_id' => $dns_broker_id,

							'broker_name' => $broker_name,

							'contact_person' => $contact_person,

							'mail_id' => $mail_id,

							'phone_no' => $phone_no,

							'acedns' => $acedns,

						);

				  $broker_final_array[]=$broker_ins_fields;		

				  $broker_ins_fields_string = http_build_query($broker_final_array); 

				}

				 $rec_count++;

			}

			if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

			  {

				array_push($upload_master_table_array,'broker_master');

			  }

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Broker master.csv is wrong.";

			exit();

		}	*/

	//For customer broker mapping CSV

	if(similar_file_exists("../csv/$folderName/Customer broker mapping.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Customer broker mapping.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		/*if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			{
				$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='customer_broker_relation'";

				$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

				$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

				$need_update=$rowchkupdateinfo['need_update'];

				if($need_update=='yes')

				{

					echo "Previous update process is going on customer broker mapping. Please try some time later.";

					die;

				}

			}*/

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			//$data="";
			$data = array();

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

						//$data[]=$value;
						array_push($data,$value);

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   //$data[]=$value;
			   array_push($data,$value);

			  //print_r($data);

			  

				$customer_code=trim($data[0]);

				$customer_name=trim($data[1]);

				$broker_code=trim($data[2]);

				$broker_name=trim($data[3]);

				$acedns=trim($data[4]);

				$mapped_broker=strtolower(trim($data[5]));

				

				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code)."'";

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code_db=$rowcustomercode['customer_code'];

				

				$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($broker_code)."'";

				$rsbrokerchk=mysqli_query($link,$sqlbrokerchk);

				$rowbrokerchk=mysqli_fetch_assoc($rsbrokerchk);

				$broker_id=$rowbrokerchk['broker_id'];

				

				$sqlcustomerbroker="SELECT customer_code,acedns FROM customer_broker_relation WHERE customer_code='".$customer_code_db."' 

									AND broker_code='".$broker_id."'";

				$rscustomerbroker=mysqli_query($link,$sqlcustomerbroker);

				$countcustomerbroker=mysqli_num_rows($rscustomerbroker);

				if($countcustomerbroker <1)

				{

					$sqlinsertcustomerbroker="INSERT INTO customer_broker_relation ";

					$sqlinsertcustomerbroker .= " SET customer_code='".$customer_code_db."'";

					$sqlinsertcustomerbroker .= " ,broker_code='".$broker_id."'";

					$sqlinsertcustomerbroker .= " ,acedns='Y'";

					$sqlinsertcustomerbroker .= " ,	mapped_broker='".$mapped_broker."'";

					$sqlinsertcustomerbroker .= " ,download_time=CURRENT_TIMESTAMP()";

					//mysqli_query($link,$sqlinsertcustomerbroker);

				}

				else

				{

					//For acedns  Y

					$sqlupdatecustomerbroker="UPDATE customer_broker_relation SET acedns='".$acedns."',	
										mapped_broker='".$mapped_broker."',download_time=CURRENT_TIMESTAMP() WHERE 

										customer_code='".$customer_code_db."' AND broker_code='".$broker_id."'";

					//$rsupdatecustomerbroker=mysqli_query($link,$sqlupdatecustomerbroker);

				}

			}

			if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

			   {

				  $customer_broker_ins_fields = array(

							'customer_code' => $customer_code,

							'customer_name' => $customer_name,

							'broker_code' => $broker_code,

							'broker_name' => $broker_name,

							'acedns' => $acedns,

							'mapped_broker' => $mapped_broker,

						);

				  $customer_broker_final_array[]=$customer_broker_ins_fields;		

				  $customer_broker_ins_fields_string = http_build_query($customer_broker_final_array); 

				}

			 $rec_count++;

		}

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		  {

			array_push($upload_master_table_array,'customer_broker_relation');

		  }		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/

	//For Depot Destination Freight

	if(similar_file_exists("../csv/$folderName/branch dump.csv")!=false)
	{

		$filename=similar_file_exists("../csv/$folderName/branch dump.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		{

			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='branch_dump'";

			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

			$need_update=$rowchkupdateinfo['need_update'];

			if($need_update=='yes')

			{

				echo "Previous update process is going on branch dump. Please try some time later.";

				die;

			}

		}

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		//print_r($lines);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			//$data="";
			$data = array();

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

						//$data[]=$value;
						array_push($data,$value);

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			  // $data[]=$value;
			   array_push($data,$value);

			  //print_r($data);

			  

				$branch_code_name=trim($data[0]);

				$dump_code_name=trim($data[1]);

				$dump_name=trim($data[2]);

				$acedns=trim($data[3]);

				$is_plant=trim($data[4]);

				$branch_code_name=str_replace(';',',',$branch_code_name);

				$branch_code_name_array=explode(',',$branch_code_name);

				

				foreach($branch_code_name_array as $branch_code_val)

				{

				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_val)."'";

				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqlbranchdump="SELECT branch_code FROM  branch_dump WHERE branch_code='".addslashes($branch_code)."' 

											AND dump_code='".$dump_code_name."'";

				$rsbranchdump=mysqli_query($link,$sqlbranchdump);

				$countbranchdump=mysqli_num_rows($rsbranchdump);

				if($countbranchdump<1 )

					{

						$sqlinsertbranchdump  = "insert into  branch_dump ";

						$sqlinsertbranchdump .= " SET branch_code='".$branch_code."'";

						$sqlinsertbranchdump .= " ,dump_code='".$dump_code_name."'";

						$sqlinsertbranchdump .= " ,dump_name='".$dump_name."'";

						$sqlinsertbranchdump .= " ,acedns='".$acedns."'";

						$sqlinsertbranchdump .= " ,is_plant='".$is_plant."'";

						$sqlinsertbranchdump .= " , download_time=CURRENT_TIMESTAMP()";

						/*mysqli_query($link,$sqlinsertbranchdump) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on branch dump table.PLease contact aceDNS admin.");*/				

					}

					else

					{

						$sqlbranchdumpupd  = "update branch_dump ";

						$sqlbranchdumpupd .= " SET acedns='".$acedns."'";

						$sqlbranchdumpupd .= " ,is_plant='".$is_plant."'";

						$sqlbranchdumpupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' 

															AND dump_code='".$dump_code_name."'";

						//mysqli_query($link,$sqlbranchdumpupd);

						

					}

					if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

				    {

					  $branch_dump_ins_fields = array(

								'branch_code_name' => $branch_code_val,

								'dump_code' => $dump_code_name,

								'dump_name' => $dump_name,

								'acedns' => $acedns,

								'is_plant' => $is_plant,

							);

					  $branch_dump_in_final_array[]=$branch_dump_ins_fields;		

					  $branch_dump_ins_fields_string = http_build_query($branch_dump_in_final_array); 

					}

			   }

			   

		}

		$rec_count++;

	}
	
	if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

	  {

		array_push($upload_master_table_array,'branch_dump');

	  }	

	$successval=1;

  }

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/
	if(similar_file_exists("../csv/$folderName/branch pgstatus.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/branch pgstatus.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		{
			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='branch_pgstatus'";
			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);
			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);
			$need_update=$rowchkupdateinfo['need_update'];
			if($need_update=='yes')
			{
				echo "Previous update process is going on branch pgstatus. Please try some time later.";
				die;
			}
		}
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		//print_r($lines);
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			//$data="";
			$data = array();
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
						//$data[]=$value;
						array_push($data,$value);
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   //$data[]=$value;
			   array_push($data,$value);
			  //print_r($data);
				$branch_code_name=trim($data[0]);
				$status=trim($data[1]);
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];
				$sqlbranchpg="SELECT branch_code FROM  branch_PGstatus WHERE branch_code='".addslashes($branch_code)."'";
				$rsbranchpg=mysqli_query($link,$sqlbranchpg);
				$countbranchpg=mysqli_num_rows($rsbranchpg);
				if($countbranchpg<1 )
					{
						$sqlinsertbranchpg  = "insert into  branch_PGstatus ";
						$sqlinsertbranchpg .= " SET branch_code='".$branch_code."'";
						$sqlinsertbranchpg .= " ,pg_status='".$status."'";
						$sqlinsertbranchpg .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertbranchpg) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on branch pgstatus table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlbranchpgupd  = "update branch_PGstatus ";
						$sqlbranchpgupd .= " SET pg_status='".$status."'";
						$sqlbranchpgupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."'";
						mysqli_query($link,$sqlbranchpgupd);
					}
					if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
				    {
					  $branch_pgstatus_ins_fields = array(
								'branch_code_name' => $branch_code_name,
								'pg_status' => $status,
							);
					  $branch_pgstatus_in_final_array[]=$branch_pgstatus_ins_fields;		
					  $branch_pgstatus_ins_fields_string = http_build_query($branch_pgstatus_in_final_array); 
					}
				}
			$rec_count++;
		}	
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		  {
			array_push($upload_master_table_array,'branch_pgstatus');
		  }	
		$successval=1;
  	}

	if($successval==1)

	{

			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP(),uploading_ip='".$_SERVER['REMOTE_ADDR']."'";

			if(mysqli_query($link,$sqlInsert))

			{

				$headers  = "MIME-Version: 1.0\r\n";

				$headers .= "Content-type: text/html; charset=UTF-8\n";

				$headers .= "From: "."FROMTAG"."<"."FROMEMAIL"."> \r\n" .

							"Reply-To:"."FROMEMAIL"." \r\n" .

							"Bcc: "."BCCEMAIL"." \r\n" .

							'X-Mailer: PHP/' . phpversion();

				

				if($nick_name =='STAR')

				{

					//$mailto='sumansaha@cmcl.co.in';

					  $mailto='warroom@starcement.co.in,emovesfa@starcement.co.in,kishukeshav@starcement.co.in';

				}

				else if($nick_name =='RUPA')

				{

					$mailto='salesmis@rupa.co.in,exe.asst@rupa.co.in';

				}

				else

				{

					$mailto='';

				}

			

				if(count($error_array)>0)

				{

					$date=gmdate('d',strtotime('+330 minute'));

					$month=gmdate('m',strtotime('+330 minute'));

					$year=gmdate('Y',strtotime('+330 minute'));

					

					$hour=gmdate('H',strtotime('+330 minute'));

					$minute=gmdate('i',strtotime('+330 minute'));

					$second=gmdate('s',strtotime('+330 minute'));

					//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

					$contentsdatetime =$date.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':'.$second;



					$mailsub='Data has been successfully uploaded to '.$nick_name.' with error(s) on '.$contentsdatetime;

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

						$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

					}

					$GLOBALS['error_msg']=$error_string;

					/*$error_msgArr=explode('#',$GLOBALS['error_msg']);

						if(count($error_msgArr)>0){

							for($i=0;$i<count($error_msgArr);$i++){

								echo "<b>$error_msgArr[$i]</b><br /><br />";

							}

						}*/

					//STAR CURL

					if(strtoupper($folderName)=='STAR')

					{

						if(in_array('branch_master',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='branch_master'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadbranchdataSTARSATHIDEVCURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$branch_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('product_master',$upload_master_table_array))

						{
							//echo $product_ins_fields_string;
						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 


						   				where table_name='product_master'";

						    //mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadskudataSTARSATHIDEVCURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$product_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('customer_master',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='customer_master'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploaddataSTARSATHICURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$cust_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('destination_master',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='destination_master'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploaddestinationdataSTARSATHICURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$destination_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('self_appraisal_customer_wise',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='self_appraisal_customer_wise'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadtargetdataSTARSATHICURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$target_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//$GLOBALS['error_msg']=$result;

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('broker_master',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='broker_master'";

						   // mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadbrokerdataSTARSATHIDEVCURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$broker_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//$GLOBALS['error_msg']=$result;

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('customer_broker_relation',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='customer_broker_relation'";

						    //mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadcustomerbrokerdataSTARSATHIDEVCURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$customer_broker_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//$GLOBALS['error_msg']=$result;

							//echo $result;

							//close connection

							curl_close($ch);

						}

						if(in_array('branch_destination_freight',$upload_master_table_array))
						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='branch_destination_freight'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadbranchdestinationdataSTARSATHICURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$branch_destination_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//$GLOBALS['error_msg']=$result;

							//echo $result;

							//close connection

							curl_close($ch);

						}
						if(in_array('customer_destination',$upload_master_table_array))
						{
						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() where table_name='customer_destination'";
						    mysqli_query($link,$sqlupdatetableinfo);
							//$postData = json_encode($cust_in_final_array);
							$url="http://salesmpower.acedns.in/misreport/uploadcustomerdestinationdataSTARSATHICURL.php";
							$ch = curl_init();
							//set the url, number of POST vars, POST data
							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_POST, 1);
							curl_setopt($ch,CURLOPT_POSTFIELDS, "$cust_destination_ins_fields_string");
							//execute post
							$result = curl_exec($ch);
							print_r($result);
							//$GLOBALS['error_msg']=$result;
							//echo $result;
							//close connection
							curl_close($ch);
						}
						if(in_array('broker_destination',$upload_master_table_array))
						{
						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() where table_name='broker_destination'";
						    mysqli_query($link,$sqlupdatetableinfo);
							//$postData = json_encode($cust_in_final_array);
							$url="http://salesmpower.acedns.in/misreport/uploadbrokerdestinationdataSTARSATHICURL.php";
							$ch = curl_init();
							//set the url, number of POST vars, POST data
							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_POST, 1);
							curl_setopt($ch,CURLOPT_POSTFIELDS, "$broker_destination_ins_fields_string");
							//execute post
							$result = curl_exec($ch);
							print_r($result);
							//$GLOBALS['error_msg']=$result;
							//echo $result;
							//close connection
							curl_close($ch);
						}
						if(in_array('branch_dump',$upload_master_table_array))
						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='branch_dump'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="https://salesmpower.acedns.in/misreport/uploadbranchdumpdataSTARSATHIDEVCURL.php";

							$ch = curl_init();

							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);

							curl_setopt($ch,CURLOPT_POST, 1);

							curl_setopt($ch,CURLOPT_POSTFIELDS, "$branch_dump_ins_fields_string");

							//execute post

							$result = curl_exec($ch);

							print_r($result);

							//$GLOBALS['error_msg']=$result;

							//echo $result;

							//close connection

							curl_close($ch);

						}
						if(in_array('branch_pgstatus',$upload_master_table_array))
						{
						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 
										where table_name='branch_pgstatus'";
							mysqli_query($link,$sqlupdatetableinfo);
							//$postData = json_encode($cust_in_final_array);
							$url="http://salesmpower.acedns.in/misreport/uploadbranchpgstatusSTARSATHICURL.php";
							$ch = curl_init();
							//set the url, number of POST vars, POST data
							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_POST, 1);
							curl_setopt($ch,CURLOPT_POSTFIELDS, "$branch_pgstatus_ins_fields_string");
							//execute post
							$result = curl_exec($ch);
							print_r($result);
							//$GLOBALS['error_msg']=$result;
							//echo $result;
							//close connection
							curl_close($ch);
						}
					}
					//STAR CURL
					if(strtoupper($folderName)=='SUPERSHAKTI')
					{
							$url="http://salesmpower.acedns.in/misreport/uploaddataSUPERSHAKTICURL.php";
							$ch = curl_init();
							//set the url, number of POST vars, POST data
							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_POST, 1);
							curl_setopt($ch,CURLOPT_POSTFIELDS, "$cust_ins_fields_string");
							//execute post
							$result = curl_exec($ch);
							print_r($result);
							//echo $result;
							//close connection
							curl_close($ch);
					}
					disphtml("main();");

				}

				/*else

				{

					echo $GLOBALS['msg'] = "Error in mail sending.";

					disphtml("main();");

				}*/

				

				//echo $err = 'Zip file extracted and data has been uploaded successfully';

			}

			else 

			{
				if(strtoupper($_SESSION['nick_name'])=='SCHOOL')
				{
					$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
				}
				else
				{
				echo $GLOBALS['msg'] = "Problem with uploading Zip file";
				}

				disphtml("main();");

			}

	}

}
?>