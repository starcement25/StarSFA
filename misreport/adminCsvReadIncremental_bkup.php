<?php	
set_time_limit(1000);

ini_set('memory_limit', '-1');

error_reporting(E_ALL ^ E_NOTICE);

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

function main()

{
    require("include/dbcon.php");

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
	
    
	<?php if(strtoupper($_SESSION['nick_name'])=='SYLVAN'){ ?>
		
		<form action="download.php" method="POST">
    <td align="right">Download Files in zip*</td>
    
    <td width="2%">:</td>
    
    <td><input type="submit" value="Download"></button></td>
</form>
	
	<?php } ?>


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



	//For Company Master CSV

	/*if(similar_file_exists("csv/$folderName/Company master.csv")!=false)

	{

		$filename=similar_file_exists("csv/$folderName/Company master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		$sqldelete="truncate company_master";

		$rsdelete=mysqli_query($link,$sqldelete);

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

			  

				$comp_code=$nick_name;

				$dns_comp_code=trim($data[0]);

				$comp_name=trim($data[1]);

				$admin_email_id=trim($data[2]);

				$account_email_id=trim($data[3]);

				

				$sqlcompany  = "insert into company_master SET ";

				$sqlcompany .= "  comp_code='".mysqli_real_escape_string($comp_code)."'";

				$sqlcompany .= "  dns_comp_code='".mysqli_real_escape_string($dns_comp_code)."'";

				$sqlcompany .= " , comp_name='".mysqli_real_escape_string($comp_name)."'";

				$sqlcompany .= " , admin_email_id='".mysqli_real_escape_string($admin_email_id)."'";

				$sqlcompany .= " , account_email_id='".mysqli_real_escape_string($account_email_id)."'";

				mysqli_query($link,$sqlcompany) or array_push($error_array,"mysqli_error().Internal error in Company master.csv.Please check.");;

			}

			 $rec_count++;

		}		

		$successval=1;

	}*/

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/

	//For Branch Master CSV

	if(similar_file_exists("../csv/$folderName/Branch master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Branch master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		{

			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='branch_master'";

			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

			$need_update=$rowchkupdateinfo['need_update'];

			if($need_update=='yes')

			{

				echo "Previous update process is going on branch master. Please try some time later.";

				die;

			}

		}

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

				if($providing_code=='yes'){

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

				}

				else

				{

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_name."'";

				}

				/*$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."' 

									AND branch_location='".$branch_location."'";*/

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

					$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";

				}

				else

				{

					$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);

					$branch_code=$rowbranchnamechk['branch_code'];



					$sqlbranch  = "UPDATE branch_master SET ";

					$sqlbranch .= "  	dns_branch_code='".mysqli_real_escape_string($dns_branch_code)."'";

					$sqlbranch .= " , branch_location='".mysqli_real_escape_string($branch_location)."'";

					$sqlbranch .= " , branch_name='".mysqli_real_escape_string($branch_name)."'";

					$sqlbranch .= " , branch_state='".mysqli_real_escape_string($branch_state)."'";

					$sqlbranch .= " , comp_code='".mysqli_real_escape_string($comp_code)."'";

					$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($branch_email_id)."'";

					$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";

					$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($alternative_email_id)."' 

									WHERE branch_code='".$branch_code."'";

				}

				mysqli_query($link,$sqlbranch) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Branch master.csv.Please check.");

			}

			   if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

			   {

				  $branch_ins_fields = array(

							'dns_branch_code' => $dns_branch_code,

							'branch_location' => $branch_location,

							'branch_name' => $branch_name,

							'branch_state' => $branch_state,

							'comp_code' => $comp_code,

							'branch_email_id' => $branch_email_id,

							'alternative_email_id' => $alternative_email_id,

							'acedns' => $acedns,

						);

				  $branch_in_final_array[]=$branch_ins_fields;		

				  $branch_ins_fields_string = http_build_query($branch_in_final_array); 

				}

			 $rec_count++;

		}

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		  {

			array_push($upload_master_table_array,'branch_master');

		  }

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Branch master.csv is wrong.";

		exit();

	}*/


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
					$category=trim($data[34]);

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

					if($providing_code=='yes'){

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

							mysqli_query($link,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");

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

							mysqli_query($link,$sqlbrandform);

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

						if($providing_code=='yes' || $folderName=='HALDIRAM'){

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
							$sql .= " , category='".addslashes($category)."'";
							$sql .= " , packing_realization='".addslashes($packing_realization)."'";
							$sql .= " , UOM5='".addslashes($UOM5)."'".$sqlinsertupdategweight;
							$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";

							//echo $sql."<br>";

							//exit();

						$res2 = mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");

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

										mysqli_query($link,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");

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
							$category_db=$rowskunamechk['category'];						

							if($acedns_db!=$acedns || $black_list_db!=$black_list 

								|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 

								|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion 

								|| $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD || $focus_db!=$focus || $weightage_db!=$weightage || $vat_db!=$vat || $addl_vat_db!=$addl_vat || $freight_cost_db!=$freight_cost || $pack_unit_db!=$pack_unit 

								|| $prod_size_db!=$prod_size || $lead_time_db!=$lead_time || $buffer_level_db!=$buffer_level 

								|| $max_level_marketing_db!=$max_level_marketing || $UOM4_db!=$UOM4 || $UOM5_db!=$UOM5 

								|| $gross_weight_db!=$gross_weight || $fg_rm_db!=$fg_rm || $oil_category_db!=$oil_category || $alias_db!=$alias || $hsn_sac_db!=$hsn_sac || $packing_realization_db!=$packing_realization || $prod_full_name_db!=$prod_full_name || $state_code_db!=$state_code || $category_db!=$category)

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
								$sql .= " , category='".addslashes($category)."'";
								$sql .= " , packing_realization='".addslashes($packing_realization)."'";

								$sql .= " , download_time=CURRENT_TIMESTAMP()";

								$sql .= " , vertical_value='".addslashes($vertical_value)."' WHERE prod_code='".$prod_code_db."'";

								//echo $sql."<br>";

								$res2 = mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");

								

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

								mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");

								$updateflag=1;
							}

						}

						if(strtoupper($folderName)=='ASL')

						{

							$sqlupd="UPDATE customer_product_relation SET download_time=CURRENT_TIMESTAMP() 

									WHERE oil_category='".$product_group_code_name."'";

							mysqli_query($link,$sqlupd);		

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

						'branch_code' => $branch_code,

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



				if($providing_code=='yes'){

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


	//For Employee CSV

	if(similar_file_exists("../csv/$folderName/Employee master.csv")!=false)

	{
require("include/config.php");

require("include/config-setup.php");

require("include/dbcon.php");
		$filename=similar_file_exists("../csv/$folderName/Employee master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

			$lines = file($filename);

			//print_r($lines);

			$reporting_to_array=array();

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
					

				  //$data[]=$value;

				 // print_r($data);

					$dns_employee_code=trim($data[0]);

					$employee_name=trim($data[1]);

					$branch_code_name=trim($data[2]);
					if(strpos($branch_code_name,';')!=false)
					 {
						$branch_code_name=str_replace(';',',',$branch_code_name);
					 }

					$vertical_value=trim($data[3]);

					$reporting_to=trim($data[4]);

					if(strpos($reporting_to,';')!=false)
					 {
						$reporting_to=str_replace(';',',',$reporting_to);
					 }
					if($reporting_to!='')
					{

						$reporting_to=str_replace(', ',',',$reporting_to);

						$reporting_val_array=explode(',',$reporting_to);

						foreach($reporting_val_array as $reporting_val)

						{

							if($providing_code == 'yes')
							{

								$sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE dns_emp_code = '".ltrim($reporting_val)."'";

									/*if($total_rows_dns_emp_code == 0)

									{

										echo "Please provide dns employee code in reporting to at row ".($csv_row_count+1);

										die;

									}*/

							}
							else
							{
							   $sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE emp_name = '".ltrim($reporting_val)."'";
							}

							$res_check_emp_code = mysqli_query($link,$sql_check_emp_code);

							$total_rows_emp_code = mysqli_num_rows($res_check_emp_code);

							$reporting_not_exists='';

							if($total_rows_emp_code == 0)
							{

								if($providing_code == 'yes')

								{

									$reporting_not_exists=$dns_employee_code.'#'.$reporting_val;

									array_push($reporting_to_array,$reporting_not_exists);

								}

								else

								{

									$reporting_not_exists=$employee_name.'#'.$reporting_val;

									array_push($reporting_to_array,$reporting_not_exists);

								}

							}

						}

				    }

					$email=trim($data[5]);

					$phone_no=trim($data[6]);

					$sale_access=trim($data[7]);

					$designation=trim($data[8]);

					$HQ=trim($data[9]);

					$state=trim($data[10]);

					$zone=trim($data[11]);

					$acedns=trim($data[12]);

					$district=trim($data[13]);

					$functionality=trim($data[14]);

					$functionality_rel_val=trim($data[15]);

					$DOJ=trim($data[16]);
					

					if(strpos($DOJ,'/')!=false){

					 $DOJArr=explode('/',$DOJ);

					}

					if(strpos($DOJ,'-')!=false){

					 $DOJArr=explode('-',$DOJ);

					}

					if(strlen($DOJArr[2])==2)

					{

					//	$year='20'.$DOJArr[2];
					$year=''.$DOJArr[2];

					}

					else

					{

						$year=$DOJArr[2];

					}

					$DOJ=$year.'-'.$DOJArr[1].'-'.$DOJArr[0];

					$level=trim($data[17]);
					$region=trim($data[18]);

					if($acedns =='N')

					{

						$app_access='N';

					}

					else

					{

						$app_access='Y';

					}

					if($folderName=='KHMER')

					{

						if(strtoupper($functionality)=='DOS')

						{

							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($employee_name)."' AND cust_type='D'";

							$rscustomercode=mysqli_query($link,$sqlcustomercode);

							$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

							$functionality_rel_val=$rowcustomercode['customer_code'];

						}

						if(strtoupper($functionality)=='ROS')

						{

							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($employee_name)."' AND cust_type='R'";

							$rscustomercode=mysqli_query($link,$sqlcustomercode);

							$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

							$functionality_rel_val=$rowcustomercode['customer_code'];

						}

						$sqlinsertcond=",functionality='".$functionality."',functionality_rel_val='".$functionality_rel_val."'";

					}

					else if(strtoupper($folderName)=='VCONNECT'){

						if(strtoupper($functionality)=='ROS')

						{

							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($functionality_rel_val)."' 

											AND cust_type='R'";

							$rscustomercode=mysqli_query($link,$sqlcustomercode);

							$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

							$functionality_rel_val=$rowcustomercode['customer_code'];

						}

						$sqlinsertcond=",functionality='".$functionality."',functionality_rel_val='".$functionality_rel_val."'";

					}

					else $sqlinsertcond='';



					if($providing_code=='yes'){

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



					$rsreportingto=mysqli_query($link,$sqlreportingto);

					while($rowreportingto=mysqli_fetch_assoc($rsreportingto))

					{

						$reporting_to_val=$reporting_to_val.$rowreportingto['emp_code'].',';

					}

					$reporting_to_val=substr($reporting_to_val,0,-1);

					if($providing_code=='yes'){

						$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE dns_emp_code='".$dns_employee_code."'";

					}

					else{
						if($folderName=='ELEGANT'){
							$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE phone_no='".addslashes($phone_no)."'";
						}
						else
						{
						$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";
						}

					}

					$rsempnamechk=mysqli_query($link,$sqlempnamechk);

					$countempnamechk=mysqli_num_rows($rsempnamechk);

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

						if($folderName=='PARLE')

						{

							if(strtoupper($designation)=='ASM') $level='2';

							if(strtoupper($designation)=='DSM') $level='3';

							if(strtoupper($designation)=='ZSM') $level='4';

							$sqllevel = " , level='".$level."'";

						}

						else	$sqllevel='';
						$sql  = "insert into employee_master ";

						$sql .= " SET emp_code='".$max_emp_code."'";

						$sql .= " , dns_emp_code='".$dns_employee_code."'";

						$sql .= " , emp_name='".ltrim(addslashes($employee_name))."'";

						$sql .= " , branch_code='".$branch_code."'";

						$sql .= " , vertical_value='".$vertical_value."'";

						$sql .= " , reporting_to='".$reporting_to_val."'";

						$sql .= " , email='".addslashes($email)."'";

						$sql .= " , phone_no='".$phone_no."'";

						$sql .= " , sale_access='".$sale_access."'";

						$sql .= " , HQ='".$HQ."'";

						$sql .= " , designation='".$designation."'";

						$sql .= " , acedns='".$acedns."'";

						$sql .= " , app_access='".$app_access."'";

						$sql .= " , state='".$state."'";

						$sql .= " , zone='".$zone."'";
						/*if($folderName!='CORAL')
						{
						    $sql .= " , DOJ='".$DOJ."'";
						}*/
						if($DOJ=='00-00-0000')
						{
						    $DOJ = "1971-01-10";
						    $sql .= " , DOJ='".$DOJ."'";
						}
						$sql .= " , level='".$level."'";
						$sql .= " , region='".$region."'";

						$sql .= " , District='".$district."'".$sqllevel.$sqlinsertcond;

						$sql .= " , acedns_changed_date=CURRENT_TIMESTAMP()";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";
//echo $sql;
						mysqli_query($link,$sql) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");

						

						$sqlchkchangepassword="SELECT emp_code FROM changepassword WHERE emp_code='".$max_emp_code."'";

						$rschkchangepassword=mysqli_query($link,$sqlchkchangepassword);

						$countchkchangepassword=mysqli_num_rows($rschkchangepassword);

						if($countchkchangepassword==0)

						{

							$sqlcp  = "insert into changepassword ";

							$sqlcp .= " SET emp_code='".$max_emp_code."'";

							$sqlcp .= " , newpassword='1234'";

							$sqlcp .= " , oldpassword='1234'"; 

							$sqlcp .= " , status='true'";

							$sqlcp .= " , is_licensed='1'"; 
							
							$sqlcp .= " , deviceid=''"; 
							
							
							$sqlcp .= " , last_operation_datetime=CURRENT_TIMESTAMP()"; 
							
							

							mysqli_query($link,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");

						}

						//modifyempdatadownloadlog($max_emp_code,strtoupper($folderName));

						if($folderName=='STAR')

						{

							$sqlmis  = "insert into mis_data_details ";

							$sqlmis .= " SET emp_code='".$max_emp_code."'";

							$sqlmis .= " , sale_access='".$sale_access."'";

							$sqlmis .= " , reporting_to='".$reporting_to_val."'"; 

							mysqli_query($link,$sqlmis) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on mis data details table.

							PLease contact aceDNS admin.");

						}

					}

					else

					{

						$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

						$emp_code_db=$rowempnamechk['emp_code'];

						$acedns_db=$rowempnamechk['acedns'];

						$sqlupdate  = "UPDATE employee_master ";

						$sqlupdate .= " SET branch_code='".$branch_code."'";

						$sqlupdate .= " , emp_name='".ltrim(addslashes($employee_name))."'";
						$sqlupdate .= " , dns_emp_code='".ltrim(addslashes($dns_employee_code))."'";

						$sqlupdate .= " , vertical_value='".$vertical_value."'";

						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";

						$sqlupdate .= " , email='".$email."'";

						$sqlupdate .= " , sale_access='".$sale_access."'";

						$sqlupdate .= " , HQ='".$HQ."'";

						$sqlupdate .= " , designation='".$designation."'";

						$sqlupdate .= " , acedns='".$acedns."'";

						$sqlupdate .= " , app_access='".$app_access."'";

						$sqlupdate .= " , state='".$state."'";

						$sqlupdate .= " , zone='".$zone."'";

						//$sqlupdate .= " , DOJ='".$DOJ."'";
						if($DOJ=='00-00-0000')
						{
						    $DOJ = "1971-01-10";
						    $sql .= " , DOJ='".$DOJ."'";
						}

						$sqlupdate .= " , level='".$level."'";
						$sqlupdate .= " , region='".$region."'";

						$sqlupdate .= " , District='".$district."'".$sqlinsertcond;

						$sqlupdate .= " , download_time=CURRENT_TIMESTAMP()";

						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_code='".addslashes($emp_code_db)."'";

						mysqli_query($link,$sqlupdate) or  array_push($error_array,"mysqli_error().Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");

						if($acedns_db !=$acedns)

						{

							$sql_update_emp_acedns = "UPDATE employee_master SET acedns_changed_date=CURRENT_TIMESTAMP() 

										WHERE emp_code = '".$emp_code_db."'";

							mysqli_query($link,$sql_update_emp_acedns);

						}

						//modifyempdatadownloadlog($emp_code_db,strtoupper($folderName));

						

						$sqlupdatebranchtime="UPDATE branch_master SET download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$branch_code."'";

						mysqli_query($link,$sqlupdatebranchtime);

					}

					//Customer Employee mapping checking

					if(customer_employee_mapping=='no' && $countempnamechk<1 && $nick_name!='LIPL')

					{

						$sqlcustomeremployee="SELECT emp_code FROM customer_master WHERE emp_code <>'' AND customer_code NOT LIKE 'N%' ORDER BY emp_code ASC LIMIT 0,1";

						$rscustomeremployee=mysqli_query($link,$sqlcustomeremployee);

						$rowcustomeremployee=mysqli_fetch_assoc($rscustomeremployee);

						$customeremployeecode=$rowcustomeremployee['emp_code'];

						

						$sqlupdateemployee="UPDATE employee_master set reporting_to=CONCAT(reporting_to,',','".$max_emp_code."') 

											WHERE emp_code='".$customeremployeecode."'";

						$rsupdateemployee=mysqli_query($link,$sqlupdateemployee);					

					}

					if($nick_name == 'LIPL' && $countempnamechk<1){

						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

						$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);

						$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);

						$new_route_code=$rowmaxroutecode['new_route_code'];

						

						$max_route_code='RT/'.($new_route_code+1);

												

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code=''";

						$sqlroute .= " ,route_name='KOLKATA'";

						$sqlroute .= " , emp_code='".$max_emp_code."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlroute);

						$route_code=$max_route_code;

						

						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";

						$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);

						$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);

						$max_customer_code=$rowmaxcustomercode['max_customer_code'];

						

						$max_customer_code++;

						

						$sql  = "insert into customer_master ";

						$sql .= " SET customer_code='".$max_customer_code."'";

						$sql .= " , dns_customer_code=''";

						$sql .= " , customer_name='STOREHANDLE'";

						$sql .= " , branch_code=''";

						$sql .= " , phone_no=''";

						$sql .= " , route_code='".$route_code."'";

						$sql .= " , emp_code='".$max_emp_code."'";

						$sql .= " , current_balance	=''";

						$sql .= " , credit_limit=''";

						$sql .= " , credit_days=''";

						$sql .= " , acedns='Y'";

						$sql .= " , black_list='N'";

						$sql .= " , TD=''";

						$sql .= " , rds_tag=''";

						$sql .= " , cust_type=''";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						//exit();

						mysqli_query($link,$sql);

					}

				}

				 $rec_count++;

			}
			foreach($reporting_to_array as $reporting_to_concat_values)

			{

				$reporting_to_splitval=explode('#',$reporting_to_concat_values);

				if($providing_code=='yes'){

					$sqlempowncode="SELECT emp_code,reporting_to FROM employee_master WHERE dns_emp_code='".$reporting_to_splitval[0]."'";

					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$reporting_to_splitval[1]."'";

				}

				else{

					$sqlempowncode="SELECT emp_code,reporting_to FROM employee_master WHERE emp_name='".$reporting_to_splitval[0]."'";

					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE emp_name='".$reporting_to_splitval[1]."'";

				}

				$rsempowncode=mysqli_query($link,$sqlempowncode);

				$rsempbosscode=mysqli_query($link,$sqlempbosscode);

				$countempowncode=mysqli_num_rows($rsempowncode);

				$countempbosscode=mysqli_num_rows($rsempbosscode);

				

				if($countempowncode == 0 || $countempbosscode==0)

				{

					echo "Please provide the details for the reporting to '".$reporting_to_splitval[1]."'";

					die;

				}

				if($countempbosscode >0)

				{

					$rowempowncode=mysqli_fetch_assoc($rsempowncode);

					$emp_own_reportingto=$rowempowncode['reporting_to'];

					$rowempbosscode=mysqli_fetch_assoc($rsempbosscode);

					if($emp_own_reportingto!='')

					{

					 echo $sqlupdatereportingto="UPDATE employee_master SET reporting_to=CONCAT(reporting_to,',".$rowempbosscode['emp_code']."') 

						                   WHERE emp_code='".$rowempowncode['emp_code']."'";

					}

					else

					{

						$sqlupdatereportingto="UPDATE employee_master SET reporting_to='".$rowempbosscode['emp_code']."' 

										WHERE emp_code='".$rowempowncode['emp_code']."'";

					}

					mysqli_query($link,$sqlupdatereportingto);					

				}

			}

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Employee Master.csv is wrong.";

			exit();

		}*/

		

	//For Route CSV

	if(similar_file_exists("../csv/$folderName/ROUTE MASTER.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/ROUTE MASTER.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		/*$sqlroutedelete="truncate route_master";

		$rsroutedelete=mysqli_query($link,$sqlroutedelete);*/

		

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

				//$routcode=trim($data[0]);

				$route_name	  =trim($data[0]); 

				$emp_code_name   =trim($data[1]); 



				

				/*if(strlen($emp_code)>4){

					$emp_code=substr($emp_code, -4);

					if(substr($emp_code, 0,1)=='0')

					{

						$emp_code=substr($emp_code,-3);

					}

				}*/

				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				$rsempcode=mysqli_query($link,$sqlempcode);

				$rowempcode=mysqli_fetch_assoc($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";

				$rsroutechk=mysqli_query($link,$sqlroutechk);

				$countroutechk=mysqli_num_rows($rsroutechk);

				if($countroutechk<1)

				{

					$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master";

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

					$sqlroute .= " ,route_name='".$route_name."'";

					$sqlroute .= " , emp_code='".$emp_code."'";

					

					mysqli_query($link,$sqlroute);

				}

			}

			 $rec_count++;

		}		

		$successval=1;

	}

		//For RDS CSV

	if(similar_file_exists("../csv/$folderName/RDS MASTER.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/RDS MASTER.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqlroutedelete="truncate route_master";

		$rsroutedelete=mysqli_query($link,$sqlroutedelete);*/

		

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

				$rdscode=trim($data[0]);

				$rdsname	  =trim($data[1]); 

				$emp_code_name =trim($data[2]);

				$rds_type =trim($data[3]);

				

				if($providing_code=='yes'){

					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

					$rsempcode=mysqli_query($link,$sqlempcode);

					$rowempcode=mysqli_fetch_assoc($rsempcode);

					$emp_code=$rowempcode['emp_code'];

				}

				else

				{

					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

					$rsempcode=mysqli_query($link,$sqlempcode);

					$rowempcode=mysqli_fetch_assoc($rsempcode);

					$emp_code=$rowempcode['emp_code'];

				}

				$sqlrdsnamechk="SELECT * FROM rds_master WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";

				$rsrdsnamechk=mysqli_query($link,$sqlrdsnamechk);

				$countrdsnamechk=mysqli_num_rows($rsrdsnamechk);

					

				$csv_row_count=$rec_count+1;

				if($countrdsnamechk<1)

				{

					$sqlmaxrdscode="SELECT MAX(rds_code) AS max_rds_code FROM  rds_master WHERE 1";

					$rsmaxrdscode=mysqli_query($link,$sqlmaxrdscode);

					$rowmaxrdscode=mysqli_fetch_assoc($rsmaxrdscode);

					$max_rds_code=$rowmaxrdscode['max_rds_code'];

					

					if($max_rds_code=='')

					{

						$max_rds_code='C/00001';

					}

					else

					{

						$max_rds_code++;

					}



				

					$sqlrds  = "insert into rds_master ";

					$sqlrds .= " SET rds_code='".$max_rds_code."'";

					$sqlrds .= " ,dns_rds_code='".$rdscode."'";

					$sqlrds .= " ,rds_name='".$rdsname."'";

					$sqlrds .= " , emp_code='".$emp_code."'";

					$sqlrds .= " , rds_type='".$rds_type."'";

					$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";

				

					mysqli_query($link,$sqlrds) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee name columns in rds master.csv.Please check.");

				}

				else

				{

					$sqlupdated  = "update rds_master ";

					$sqlupdated .= " SET rds_type='".$rds_type."'";

					$sqlupdated .= " WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";

					mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on rds master.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	//For Customer CSV

	if(similar_file_exists("../csv/$folderName/Customer Master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Customer Master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

			if(strtoupper($folderName)=='STAR')
			{

				$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='customer_master'";

				$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

				$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

				$need_update=$rowchkupdateinfo['need_update'];

				if($need_update=='yes')

				{

					echo "Previous update process is going on customer master. Please try some time later.";

					die;

				}

			}

			$lines = file($filename);
			//print_r($lines);

			$countroute=0;

			if(modified_customer_emp_route=='no' && $folderName!='ABDOS' && $folderName!='DNV' && $folderName!='HALDIRAM' && $folderName!='SHAKERS' && $folderName!='SKIPPER' && $folderName!='SMPDEMO' && $folderName!='SAVERA' && $folderName!='GWAAL' && $folderName!='KARMA' && $folderName!='STAR')

			{

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

						$value .=$char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

				  //print_r($data);

					

					$csv_row_count=$rec_count+1;

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					if($folderName=='MAITHAN')

					{

						$sqlemparray="SELECT emp_name FROM employee_master WHERE HQ='".$route_name."'";

						$rsemparray=mysqli_query($link,$sqlemparray);

						$emp_code_name_array=array();

						while($rowemparray=mysqli_fetch_assoc($rsemparray))

						{

							array_push($emp_code_name_array,$rowemparray['emp_name']);

						}

					}

					else

					{

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

					}

					

					//print_r($emp_code_name_array);



					if($providing_code == 'yes')

					{

						foreach($emp_code_name_array as $emp_code_name_values)

						{

							$sql_check_dns_code = "SELECT emp_code FROM employee_master WHERE dns_emp_code = '".$emp_code_name_values."'";

							$res_check_dns_code = mysqli_query($link,$sql_check_dns_code);

							$total_rows = mysqli_num_rows($res_check_dns_code);

							if($total_rows == 0)

							{

								echo "Please provide proper DNS Employee Code at row ".($csv_row_count+1);

								die;

							}

						}

						if($route_name != '' && $dns_route_code == '')

						{

							echo "Please provide dns route code at row ".($csv_row_count+1);

						}

					}

					else

					{

						foreach($emp_code_name_array as $emp_code_name_values)

						{

							$sql_check_emp = "SELECT emp_code FROM employee_master WHERE emp_name = '".$emp_code_name_values."'";

							$res_check_emp = mysqli_query($link,$sql_check_emp);

							$total_rows = mysqli_num_rows($res_check_emp);

							if($total_rows == 0)

							{

								echo "Please provide proper employee at row ".($csv_row_count+1);

								die;

							}

						}

					}

					//For VIPL employee only

					/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_code)."'";

					$rsempnamechk=mysqli_query($link,$sqlempnamechk);

					$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

					$emp_code=$rowempnamechk['emp_code'];*/

					$acedns		  =trim($data[6]);

					if($acedns =='')

					{

						echo "Please provide value for Acedns at row ".($csv_row_count+1);

						die;

					}

					

					$credit_limit	=trim($data[7]);

					$credit_days	 =trim($data[8]);

					$current_balance =trim($data[9]);

					$black_list	  =trim($data[10]); 

					if($black_list=='')

					{

						echo "Please provide value for Blacklist at row ".($csv_row_count+1);

						die;

					}

					

					$TD	  		  =trim($data[11]);

					$branch_code_name =trim($data[12]);

					$customer_type   =trim($data[13]);

					$rds_tag   =trim($data[14]);

					if(retailer_app=='yes'){

					 if(strpos($rds_tag,';')!=false)

					 {

						$rds_tag=str_replace(';',',',$rds_tag);

					 }

					 $emp_code_name_array=explode(',',$rds_tag);

					}

					$sauda_validity_period  =trim($data[15]);

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim(preg_replace('/[\r\n]+/', '',$data[27]));

					$bank_account_number  =trim(preg_replace('/[\r\n]+/', '',$data[28]));

					$email  =trim(preg_replace('/[\r\n]+/', '',$data[29]));

					$visit_day  =trim($data[30]);

					$state  =trim(preg_replace('/[\r\n]+/', '',$data[31]));

					$monthly_potential  =trim($data[32]);

					$sauda_limit  =trim($data[33]);

					$incoterms  =trim($data[34]);

					$loadability_ton  =trim($data[35]);

					$transport_mode  =trim($data[36]);

					$sauda_type  =trim($data[37]);

					$zone  =trim(preg_replace('/[\r\n]+/', '',$data[38]));

					$visit_sequence=trim($data[39]);

					$appointment_date=trim($data[40]);

					

					if($customer_name =='')

					{

						echo "Please provide proper Customer Name at row ".($csv_row_count+1);

						die;

					}

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

						if($providing_code=='yes'){

							$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

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

							$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";

							$rsempcode=mysqli_query($link,$sqlempcode);

							$rowempcode=mysqli_fetch_assoc($rsempcode);

							$emp_code=$rowempcode['emp_code'];

							

							//exit();

							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";

							$rsbranchcode=mysqli_query($link,$sqlbranchcode);

							$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

							$branch_code=$rowbranchcode['branch_code'];

						}

					 //$emp_code=$emp_code_name;

					 	if($providing_code=='yes'){

							$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";

						}

						else

						{

							$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' 

										and emp_code='".$emp_code."'";

						}

						$rsrdscode=mysqli_query($link,$sqlrdscode);

						$rowrdscode=mysqli_fetch_assoc($rsrdscode);

						$rds_code=$rowrdscode['customer_code'];



						if($providing_code=='yes'){

							$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."' AND emp_code='".$emp_code."'";

						}

						else

						{

							$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";

						}

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

							$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

							mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");

							modifyempdatadownloadlog($emp_code,strtoupper($folderName));

							$route_code=$max_route_code;

						}

						else

						{

							$rowroutechk=mysqli_fetch_assoc($rsroutechk);

							$route_code=$rowroutechk['route_code'];

						}



						if($providing_code=='yes'){

						   $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND 

											emp_code='".$emp_code."' AND route_code='".$route_code."'";

						}

						else

						{

						$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' 

										AND emp_code='".$emp_code."' AND route_code='".$route_code."'";

						}

					/*$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";*/

					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);

					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);

					

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

						$sql .= " , address='".$address."'";

						$sql .= " , owner_name='".$owner_name."'";

						$sql .= " , owner_phone='".$owner_phone."'";

						$sql .= " , cust_class='".$cust_class."'";

						$sql .= " , weekly_closing_day='".$weekly_closing_day."'";

						$sql .= " , TIN='".$TIN."'";

						$sql .= " , PAN='".$PAN."'";

						$sql .= " , district='".$district."'";

						$sql .= " , landline_no='".$landline_no."'";

						$sql .= " , minimum_stock='".$minimum_stock."'";

						$sql .= " , bank_name='".$bank_name."'";

						$sql .= " , bank_account_number='".$bank_account_number."'";

						$sql .= " , email='".addslashes($email)."'";

						$sql .= " , visit_day='".addslashes($visit_day)."'";

						$sql .= " , state_code='".addslashes($state)."'";

						$sql .= " , monthly_potential='".addslashes($monthly_potential)."'";

						$sql .= " , coverage_type='".addslashes($coverage_type)."'";
						$sql .= " , zone='".$zone."'";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						//exit();

						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().".$sql." Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");

						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$customer_code=$max_customer_code;

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

						$customer_name_db=$rowcustomernamechk['customer_name'];

						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];

						$address_db=$rowcustomernamechk['address'];

						$owner_name_db=$rowcustomernamechk['owner_name'];

						$owner_phone_db=$rowcustomernamechk['owner_phone'];

						$cust_class_db=$rowcustomernamechk['cust_class'];

						$weekly_closing_day_db=$rowcustomernamechk['weekly_closing_day'];

						$TIN_db=$rowcustomernamechk['TIN'];

						$PAN_db=$rowcustomernamechk['PAN'];

						$district_db=$rowcustomernamechk['district'];

						$landline_no_db=$rowcustomernamechk['landline_no'];

						$minimum_stock_db=$rowcustomernamechk['minimum_stock'];

						$bank_name_db=$rowcustomernamechk['bank_name'];

						$bank_account_number_db=$rowcustomernamechk['bank_account_number'];

						$email_db=$rowcustomernamechk['email'];

						$visit_day_db=$rowcustomernamechk['visit_day'];

						$coverage_type_db  =$rowcustomernamechk['coverage_type'];

						$state_code_db=$rowcustomernamechk['state_code'];

						$monthly_potential_db  =$rowcustomernamechk['monthly_potential'];

						if($providing_code=='yes'){

							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";

						}

						else

						{

							$update_condition=" customer_name='".addslashes($customer_name)."'";

						}

						if($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD || $customer_type_db!=$customer_type || $rds_tag_db!=$rds_code 

						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 

						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no || $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class || $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district || $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name || $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day 

						|| $coverage_type_db!=$coverage_type || $monthly_potential_db!=$monthly_potential)

						{

							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";

							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";

							$sqlupdated .= " , current_balance	='".$current_balance."'";

							$sqlupdated .= " , acedns='".$acedns."'";

							$sqlupdated .= " , branch_code='".$branch_code."'";

							$sqlupdated .= " , TD='".$TD."'";

							$sqlupdated .= " , cust_type='".$customer_type."'";

						    $sqlupdated .= " , credit_days='".$credit_days."'";

							$sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";

							$sqlupdated .= " , address='".$address."'";

							$sqlupdated .= " , owner_name='".$owner_name."'";

							$sqlupdated .= " , owner_phone='".$owner_phone."'";

							$sqlupdated .= " , cust_class='".$cust_class."'";

							$sqlupdated .= " , weekly_closing_day='".$weekly_closing_day."'";

							$sqlupdated .= " , TIN='".$TIN."'";

							$sqlupdated .= " , PAN='".$PAN."'";

							$sqlupdated .= " , district='".$district."'";

							$sqlupdated .= " , landline_no='".$landline_no."'";

							$sqlupdated .= " , minimum_stock='".$minimum_stock."'";

							$sqlupdated .= " , bank_name='".$bank_name."'";

							$sqlupdated .= " , bank_account_number='".$bank_account_number."'";

							$sqlupdated .= " , email='".$email."'";

							$sqlupdated .= " , rds_tag='".$rds_code."',visit_day='".addslashes($visit_day)."',

												coverage_type='".addslashes($coverage_type)."',

												state_code='".addslashes($state)."',

												monthly_potential='".addslashes($monthly_potential)."',

												download_time=CURRENT_TIMESTAMP() 

											 WHERE  ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."' ";

							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

							modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						}

						if(($credit_limit_db!=$credit_limit))

						{

							$sqlupdatedcredit  = "update customer_master ";

							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";

							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 

											 WHERE ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."'";

							mysqli_query($link,$sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

							modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						}

						$customer_code=$customer_code_db;

					}//End of else

					//For Distributor route creation

					  if(distributor_route_planning=='yes')

					   {

						   $sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."',

						   							route_code='".$route_code."',emp_code='".$emp_code."'";

						   $rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);

						   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

						   if($countchkdistributorroute==0)

						   {						

							   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

														route_code='".$route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";

							   $rsinsertdistributorroute=mysqli_query($link,$sqlinsertdistributorroute);

						   }

					   }

					//End of Distributor route creation

				}//End of emp code name foreach

				//exit();

			 }//End of IF

				 $rec_count++;

		}//End of main foreach

			//exit();

			//Emp code checking start

				$sqlempcoderetail="SELECT emp_code FROM customer_master WHERE emp_code NOT IN(SELECT emp_code FROM employee_master)";

				$rsempcoderetail=mysqli_query($link,$sqlempcoderetail);

				$cntempcoderetail=mysqli_num_rows($rsempcoderetail);

				if($cntempcoderetail>0)

				{

					$empcoderetail='';

					while($rowempcoderetail=mysqli_fetch_assoc($rsempcoderetail))

					{

						$empcoderetail=$empcoderetail.$rowempcoderetail['emp_code'].',';

					}

					$empcoderetail=substr($empcoderetail,0,-1);

					if($folderName=='RUPA')

					{

						$errorempcoderetail='There are many employee exists in customer_master but not exists in employee_master.';

					}

					else

					{

						$errorempcoderetail=$empcoderetail.' exists in customer_master but not exists in employee_master.';

					}

					array_push($error_array,$errorempcoderetail);

				}

			//Emp code checking end

			//Route code checking start

				$sqlroutecoderetail="SELECT route_code FROM customer_master WHERE route_code NOT IN(SELECT route_code FROM route_master)";

				$rsroutecoderetail=mysqli_query($link,$sqlroutecoderetail);

				$cntroutecoderetail=mysqli_num_rows($rsroutecoderetail);

				if($cntroutecoderetail>0)

				{

					$routecoderetail='';

					while($rowroutecoderetail=mysqli_fetch_assoc($rsroutecoderetail))

					{

						$routecoderetail=$routecoderetail.$rowroutecoderetail['route_code'].',';

					}

					$routecoderetail=substr($routecoderetail,0,-1);

					if($folderName=='RUPA')

					{

						$errorempcoderetail='There are many route exists in customer_master but not exists in route_master.';

					}

					else

					{

						$errorroutecoderetail=$routecoderetail.' exists in customer_master but not exists in route_master.';

					}

					array_push($error_array,$errorroutecoderetail);

				}

			//Route code checking end

			}
			else
			{

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

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim(preg_replace('/[\r\n]+/', '',$data[1]));

					$phone_no		=trim(preg_replace('/[\r\n]+/', '',$data[2]));

					$dns_route_code  =trim(preg_replace('/[\r\n]+/', '',$data[3]));

					$route_name	  =trim(preg_replace('/[\r\n]+/', '',$data[4]));  

					$emp_code_name		=trim(preg_replace('/[\r\n]+/', '',$data[5]));

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					$emp_code_name_array=explode(',',$emp_code_name);

					$acedns		  =trim($data[6]);

					$credit_limit	=trim($data[7]);

					$credit_days	 =trim($data[8]);

					$current_balance =trim($data[9]);

					$black_list	  =trim($data[10]); 

					$TD	  		  =trim($data[11]);

					$branch_code_name =trim($data[12]);

					$customer_type   =trim(preg_replace('/[\r\n]+/', '',$data[13]));

					$rds_tag   =trim(preg_replace('/[\r\n]+/', '',$data[14]));

					//echo retailer_app;

					/*if(strtoupper($folderName)=='START'){

					 if(strpos($rds_tag,';')!=false)

					 {

						$rds_tag=str_replace(';',',',$rds_tag);

					 }

					 $emp_code_name_array=explode(',',$rds_tag);

					}*/

					$sauda_validity_period  =trim(preg_replace('/[\r\n]+/', '',$data[15]));

					$address  =trim(preg_replace('/[\r\n]+/', '',$data[16]));

					$landline_no  =trim(preg_replace('/[\r\n]+/', '',$data[17]));

					$owner_name  =trim(preg_replace('/[\r\n]+/', '',$data[18]));

					$owner_phone  =trim(preg_replace('/[\r\n]+/', '',$data[19]));

					$cust_class  =trim(preg_replace('/[\r\n]+/', '',$data[20]));

					$weekly_closing_day  =trim(preg_replace('/[\r\n]+/', '',$data[21]));

					$coverage_type  =trim(preg_replace('/[\r\n]+/', '',$data[22]));

					$TIN  =trim(preg_replace('/[\r\n]+/', '',$data[23]));

					$PAN  =trim(preg_replace('/[\r\n]+/', '',$data[24]));

					$district  =trim(preg_replace('/[\r\n]+/', '',$data[25]));

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim(preg_replace('/[\r\n]+/', '',$data[27]));

					$bank_account_number  =trim(preg_replace('/[\r\n]+/', '',$data[28]));

					$email  =trim(preg_replace('/[\r\n]+/', '',$data[29]));

					$visit_day  =trim($data[30]);

					$state  =trim(preg_replace('/[\r\n]+/', '',$data[31]));

					$monthly_potential  =trim($data[32]);

					$sauda_limit  =trim($data[33]);

					$incoterms  =trim($data[34]);

					$loadability_ton  =trim($data[35]);

					$transport_mode  =trim($data[36]);

					$sauda_type  =trim($data[37]);

					$zone  =trim(preg_replace('/[\r\n]+/', '',$data[38]));

					$visit_sequence=trim($data[39]);

					$appointment_date=trim($data[40]);

					if($appointment_date!=''){

					if(strpos($appointment_date,'/')!=false){

					 $appointment_dateArr=explode('/',$appointment_date);

					}

					if(strpos($appointment_date,'-')!=false){

					 $appointment_dateArr=explode('-',$appointment_date);

					}

					//echo $dns_customer_code.'##'.count($appointment_dateArr).'##'.$appointment_date;

						if(strlen($appointment_dateArr[2])==2)

						{

							$year='20'.$appointment_dateArr[2];

						}

						else

						{

							$year=$appointment_dateArr[2];

						}

						$appointment_date=$year.'-'.$appointment_dateArr[1].'-'.$appointment_dateArr[0];

					}

					else

					{

						$appointment_date='';

					}

					$date_of_birth=trim($data[41]);

					if($date_of_birth !=''){

					if(strpos($date_of_birth,'/')!=false){

					 $date_of_birthArr=explode('/',$date_of_birth);

					}

					if(strpos($date_of_birth,'-')!=false){

					 $date_of_birthArr=explode('-',$date_of_birth);

					}

						if(strlen($date_of_birthArr[2])==2)

						{

							$year='20'.$date_of_birthArr[2];

						}

						else

						{

							$year=$date_of_birthArr[2];

						}

						$date_of_birth=$year.'-'.$date_of_birthArr[1].'-'.$date_of_birthArr[0];

					}

					else

					{

						$date_of_birth='';

					}



					$date_of_anniversary=trim($data[42]);

					if($date_of_anniversary !=''){

					if(strpos($date_of_anniversary,'/')!=false){

					 $date_of_anniversaryArr=explode('/',$date_of_anniversary);

					}

					if(strpos($date_of_anniversary,'-')!=false){

					 $date_of_anniversaryArr=explode('-',$date_of_anniversary);

					}

						if(strlen($date_of_anniversaryArr[2])==2)

						{

							$year='20'.$date_of_anniversaryArr[2];

						}

						else

						{

							$year=$date_of_anniversaryArr[2];

						}

						$date_of_anniversary=$year.'-'.$date_of_anniversaryArr[1].'-'.$date_of_anniversaryArr[0];

					}

					else

					{

						$date_of_anniversary='';

					}



					$whatsapp_no=trim(preg_replace('/[\r\n]+/', '',$data[43]));

					$beneficiary_name=trim(preg_replace('/[\r\n]+/', '',$data[44]));

					$IFS_code=trim($data[45]);

					$pin=trim(preg_replace('/[\r\n]+/', '',$data[46]));

					$base_latt=trim($data[47]);

					$base_longi=trim($data[48]);

					$retailer_app=trim($data[49]);
					$is_new_customer=trim($data[50]);
					$category_of_store=trim($data[51]);
					$nlp=trim($data[52]);
					$area=trim($data[53]);
					$route_no=trim($data[54]);
					$cluster=trim($data[55]);

					$visit_day=strtolower($visit_day);

					$visit_day=ucfirst($visit_day);

					//echo $dns_customer_code.$customer_name.'<br />';

					/*if($customer_name =='')

					{

						echo "Please provide proper Customer Name at row ".($csv_row_count+1);

						die;

					}*/

					if(strtoupper($folderName)=='SYLVAN')
					{
						
						if(strlen($phone_no)!=10 || filter_var($phone_no, FILTER_VALIDATE_INT) === false )
						{
							echo "Phone no should be 10 digits number at row ".($csv_row_count+1);
							die;
						}
						if(strlen($pin)!=6 || filter_var($pin, FILTER_VALIDATE_INT) === false )
						{
							$GLOBALS['msg'] = "PIN should be 6 digits number at row ".($csv_row_count+1);
							disphtml("main();");
							die;
						}
						/*$sql_check_cust_phone = "SELECT phone_no FROM customer_master WHERE dns_customer_code <> '".ltrim($dns_customer_code)."' AND phone_no='".$phone_no."' AND phone_no <>''";
						$rs_cust_phone=mysqli_query($link,$sql_check_cust_phone);
						$row_check_cust_phone=mysqli_num_rows($rs_cust_phone);
						if($row_check_cust_phone  > 0)
						{
							echo "Phone no already exists.Please provide another phone no at row ".($csv_row_count+1);
							die;
						}*/
						/*$sql_check_cust_whatsapp = "SELECT whatsapp_no FROM customer_master WHERE dns_customer_code <> '".ltrim($dns_customer_code)."' 

												AND whatsapp_no='".$whatsapp_no."' AND whatsapp_no <>''";

						$rs_check_cust_whatsapp=mysqli_query($link,$sql_check_cust_whatsapp);
						$row_check_cust_whatsapp=mysqli_num_rows($rs_check_cust_whatsapp);
						if($row_check_cust_whatsapp  > 0)
						{
							echo "Whatsapp no already exists.Please provide another Whatsapp no at row ".($csv_row_count+1);
							die;
						}*/
					}

					$mapped_emp_code_string='';

					//For employee code and branch code

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

					if($providing_code=='yes'){

						if(strtoupper($folderName)=='ASL'){

							$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";

							$rsempcode=mysqli_query($link,$sqlempcode);

							$rowempcode=mysqli_fetch_assoc($rsempcode);

							$emp_code=$rowempcode['emp_code'];

						}

						else

						{

						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

						$rsempcode=mysqli_query($link,$sqlempcode);

						$rowempcode=mysqli_fetch_assoc($rsempcode);

						$emp_code=$rowempcode['emp_code'];

						}
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

						$rsbranchcode=mysqli_query($link,$sqlbranchcode);

						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

						$branch_code=$rowbranchcode['branch_code'];

					}
					else
					{
						if(strtoupper($folderName)=='ELEGANT'){
							$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";
						}
						else
						{
						  $sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";
						}
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
						$rsbranchcode=mysqli_query($link,$sqlbranchcode);
						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
						$branch_code=$rowbranchcode['branch_code'];
					}

					//For distributor tagged

					if($providing_code=='yes'){
						$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";
					}
					else
					{
						if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
						{
							$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."' AND 
										dns_customer_code!=''";
							/*$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y' 
										AND cust_type!='R'AND customer_name!=''";*/			
						}
						else
						{
							$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y' 
															 AND cust_type!='R'";
						}

					}

					$rsrdscode=mysqli_query($link,$sqlrdscode);

					$rowrdscode=mysqli_fetch_assoc($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					//For route

					if($providing_code=='yes'){

						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";

					}

					else

					{

						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";

					}

					$rsroutechk=mysqli_query($link,$sqlroutechk);

					$countroutechk=mysqli_num_rows($rsroutechk);

					/*if(strtoupper($folderName)=='ASL')

					{

						$sqltownnamechk="SELECT town_name FROM town_master WHERE town_name='".addslashes($route_name)."'";

						$rstownnamechk=mysqli_query($link,$sqltownnamechk);

						$cnttownmamechk=mysqli_num_rows($rstownnamechk);

						if($cnttownmamechk==0)

						{

							echo "Route name not exists in town list.Please provide another route name at row ".($csv_row_count+1);

							die;

						}

					}*/

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

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";

						$sqlroute .= " ,route_name='".$route_name."'";

						$sqlroute .= " ,branch_code='".$branch_code."'";
						$sqlroute .= " ,cluster='".$cluster."'";
						$sqlroute .= " ,area='".$area."'";
						$sqlroute .= " ,route_no='".$route_no."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

					else

					{

						$rowroutechk=mysqli_fetch_assoc($rsroutechk);

						$route_code=$rowroutechk['route_code'];
						$branch_code_db=$rowroutechk['branch_code'];
						$route_name_db=$rowroutechk['route_name'];
						$area_db=$rowroutechk['area'];
						$route_no_db=$rowroutechk['route_no'];
						$cluster_db=$rowroutechk['cluster'];

						if($route_name_db !=$route_name || $branch_code_db !=$branch_code || $cluster_db !=$cluster || $area_db!=$area || $route_no_db!=$route_no)

						{
							//$branch_code=$branch_code_db.','.$branch_code;
							$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',branch_code='".$branch_code."'
											,cluster='".$cluster."',
											area='".$area."',route_no='".$route_no."',download_time=CURRENT_TIMESTAMP() 
											WHERE route_code='".$route_code."'";

							mysqli_query($link,$sqlupdateroue) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");

						}

					}

					//For customer

					if($providing_code=='yes'){

					  // $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND route_code='".$route_code."'";

					    $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

					}

					else

					{

					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."' AND acedns='Y'";

					 //$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

					}

					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);

					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);

					

					$csv_row_count=$rec_count+1;

					if($countcustomernamechk<1)

					{

						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";

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

						$sql .= " , current_balance	='".$current_balance."'";

						$sql .= " , credit_limit='".$credit_limit."'";

						$sql .= " , credit_days='".$credit_days."'";

						$sql .= " , acedns='Y'";

						$sql .= " , black_list='N'";

						$sql .= " , TD='".$TD."'";

						$sql .= " , rds_tag='".$rds_code."'";

						$sql .= " , cust_type='".$customer_type."'";

						$sql .= " , sauda_validity_period='".$sauda_validity_period."'";

						$sql .= " , address='".addslashes($address)."'";

						$sql .= " , owner_name='".addslashes($owner_name)."'";

						$sql .= " , owner_phone='".$owner_phone."'";

						$sql .= " , cust_class='".$cust_class."'";

						$sql .= " , weekly_closing_day='".$weekly_closing_day."'";

						$sql .= " , TIN='".$TIN."'";

						$sql .= " , PAN='".$PAN."'";

						$sql .= " , district='".$district."'";

						$sql .= " , landline_no='".$landline_no."'";

						$sql .= " , minimum_stock='".$minimum_stock."'";

						$sql .= " , bank_name='".$bank_name."'";

						$sql .= " , bank_account_number='".$bank_account_number."'";

						$sql .= " , email='".$email."'";

						$sql .= " , visit_day='".addslashes($visit_day)."'";

						$sql .= " , state_code='".addslashes($state)."'";

						$sql .= " , monthly_potential='".addslashes($monthly_potential)."'";

						$sql .= " , coverage_type='".addslashes($coverage_type)."'";

						$sql .= " , incoterms='".addslashes($incoterms)."'";

						$sql .= " , loadability_ton='".addslashes($loadability_ton)."'";

						$sql .= " , transport_mode='".addslashes($transport_mode)."'";

						$sql .= " , sauda_type='".addslashes($sauda_type)."'";

						$sql .= " , zone='".addslashes($zone)."'";

						$sql .= " , visit_sequence='".addslashes($visit_sequence)."'";

						$sql .= " , appointment_date='".addslashes($appointment_date)."'";

						$sql .= " , date_of_birth='".addslashes($date_of_birth)."'";

						$sql .= " , date_of_anniversary='".addslashes($date_of_anniversary)."'";

						$sql .= " , whatsapp_no='".addslashes($whatsapp_no)."'";

						$sql .= " , beneficiary_name='".addslashes($beneficiary_name)."'";

						$sql .= " , IFS_code='".addslashes($IFS_code)."'";

						$sql .= " , pin='".addslashes($pin)."'";

						$sql .= " , base_latt='".addslashes($base_latt)."'";

						$sql .= " , base_longi='".addslashes($base_longi)."'";

						$sql .= " , retailer_app='".addslashes($retailer_app)."'";
						$sql .= " , is_new_customer='".addslashes($is_new_customer)."'";
						$sql .= " ,	category_of_store='".addslashes($category_of_store)."'";
						$sql .= " ,	is_nlp='".addslashes($nlp)."'";
						$sql .= " ,	cluster='".addslashes($cluster)."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");

					   //modifyempdatadownloadlog($emp_code,strtoupper($folderName));

					   $customer_code=$max_customer_code;

					   //$starsathi_operation='INSERT';

					}

					else

					{

						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);

						$customer_code_db=$rowcustomernamechk['customer_code'];

						$route_code_db=$rowcustomernamechk['route_code'];

						$current_balance_db=$rowcustomernamechk['current_balance'];

						$phone_no_db=$rowcustomernamechk['phone_no'];

						$credit_limit_db=$rowcustomernamechk['credit_limit'];

						$credit_days_db=$rowcustomernamechk['credit_days'];

						$acedns_db=$rowcustomernamechk['acedns'];

						$black_list_db=$rowcustomernamechk['black_list'];

						$TD_db=$rowcustomernamechk['TD'];

						$customer_type_db=$rowcustomernamechk['cust_type'];

						$rds_tag_db=$rowcustomernamechk['rds_tag'];

						$branch_code_db=$rowcustomernamechk['branch_code'];

						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];

						$customer_name_db=$rowcustomernamechk['customer_name'];

						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];

						$address_db=$rowcustomernamechk['address'];

						$owner_name_db=$rowcustomernamechk['owner_name'];

						$owner_phone_db=$rowcustomernamechk['owner_phone'];

						$cust_class_db=$rowcustomernamechk['cust_class'];

						$weekly_closing_day_db=$rowcustomernamechk['weekly_closing_day'];

						$TIN_db=$rowcustomernamechk['TIN'];

						$PAN_db=$rowcustomernamechk['PAN'];

						$district_db=$rowcustomernamechk['district'];

						$zone_db=$rowcustomernamechk['zone'];

						$landline_no_db=$rowcustomernamechk['landline_no'];

						$minimum_stock_db=$rowcustomernamechk['minimum_stock'];

						$bank_name_db=$rowcustomernamechk['bank_name'];

						$bank_account_number_db=$rowcustomernamechk['bank_account_number'];

						$email_db=$rowcustomernamechk['email'];

						$visit_day_db=$rowcustomernamechk['visit_day'];

						$coverage_type_db=$rowcustomernamechk['coverage_type'];

						$state_code_db=$rowcustomernamechk['state_code'];

						$monthly_potential_db  =$rowcustomernamechk['monthly_potential'];

						$sauda_limit_db=$rowcustomernamechk['sauda_limit'];

						$incoterms_db=$rowcustomernamechk['incoterms'];

						$loadability_ton_db=$rowcustomernamechk['loadability_ton'];

						$transport_mode_db=$rowcustomernamechk['transport_mode'];

						$sauda_type_db=$rowcustomernamechk['sauda_type'];

						$zone_db=$rowcustomernamechk['zone'];

						$visit_sequence_db=$rowcustomernamechk['visit_sequence'];

						$appointment_date_db=$rowcustomernamechk['appointment_date'];

						$date_of_birth_db=$rowcustomernamechk['date_of_birth'];

						$date_of_anniversary_db=$rowcustomernamechk['date_of_anniversary'];

						$whatsapp_no_db=$rowcustomernamechk['whatsapp_no'];

						$beneficiary_name_db=$rowcustomernamechk['beneficiary_name'];

						$IFS_code_db=$rowcustomernamechk['IFS_code'];

						$pin_db=$rowcustomernamechk['pin'];

						$base_latt_db=$rowcustomernamechk['base_latt'];

						$base_longi_db=$rowcustomernamechk['base_longi'];

						$retailer_app_db=$rowcustomernamechk['retailer_app'];
						$is_new_customer_db=$rowcustomernamechk['is_new_customer'];
						$category_of_store_db=$rowcustomernamechk['category_of_store'];
						$is_nlp_db=$rowcustomernamechk['is_nlp'];
						$cluster_db=$rowcustomernamechk['cluster'];
						if($providing_code=='yes'){

							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";

						}

						else

						{

							$update_condition=" customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";

						}



						if($route_code_db!=$route_code || $current_balance_db!=$current_balance || $TD_db!=$TD || $customer_type_db!=$customer_type 

						|| $rds_tag_db!=$rds_code 

						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 

						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no 

						|| $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class 

						|| $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district 

						|| $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name 

						|| $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day 

						|| $coverage_type_db!=$coverage_type || $monthly_potential_db!=$monthly_potential || $sauda_limit_db!=$sauda_limit 

						|| $incoterms_db!=$incoterms || $loadability_ton_db!=$loadability_ton || $transport_mode_db!=$transport_mode || $sauda_type_db!=$sauda_type || $zone_db!=$zone || $visit_sequence_db!=$visit_sequence || $appointment_date_db!=$appointment_date || $date_of_birth_db!=$date_of_birth || $date_of_anniversary_db!=$date_of_anniversary || $whatsapp_no_db!=$whatsapp_no || $beneficiary_name_db!=$beneficiary_name 

						|| $IFS_code_db!=$IFS_code || $pin_db!=$pin ||  $base_latt_db!=$base_latt || $base_longi_db!=$base_longi || $retailer_app_db!=$retailer_app || $is_new_customer_db!=$is_new_customer || $category_of_store_db!=$category_of_store || $is_nlp_db!=$nlp || $cluster_db!=$cluster)

						{
							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";

							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";

							$sqlupdated .= " , current_balance	='".$current_balance."'";

							$sqlupdated .= " , branch_code='".$branch_code."'";

							$sqlupdated .= " , TD='".$TD."'";

							$sqlupdated .= " , cust_type='".$customer_type."'";

							$sqlupdated .= " , phone_no='".$phone_no."'";

						    $sqlupdated .= " , credit_days='".$credit_days."'";

							$sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";

							$sqlupdated .= " , address='".addslashes($address)."'";

							$sqlupdated .= " , owner_name='".addslashes($owner_name)."'";

							$sqlupdated .= " , owner_phone='".$owner_phone."'";

							$sqlupdated .= " , cust_class='".$cust_class."'";

							$sqlupdated .= " , weekly_closing_day='".$weekly_closing_day."'";

							$sqlupdated .= " , TIN='".$TIN."'";

							$sqlupdated .= " , PAN='".$PAN."'";

							$sqlupdated .= " , district='".$district."'";

							$sqlupdated .= " , landline_no='".$landline_no."'";

							$sqlupdated .= " , minimum_stock='".$minimum_stock."'";

							$sqlupdated .= " , bank_name='".$bank_name."'";

							$sqlupdated .= " , bank_account_number='".$bank_account_number."'";

							$sqlupdated .= " , email='".$email."'";
							$sqlupdated .= " , base_latt='".addslashes($base_latt)."'";

							$sqlupdated .= " , base_longi='".addslashes($base_longi)."'";

							$sqlupdated .= " , rds_tag='".$rds_code."',visit_day='".addslashes($visit_day)."',

												coverage_type='".addslashes($coverage_type)."',

												state_code='".addslashes($state)."',

												monthly_potential='".addslashes($monthly_potential)."',

												incoterms='".addslashes($incoterms)."',

												loadability_ton='".addslashes($loadability_ton)."',

												transport_mode='".$transport_mode."',sauda_type='".$sauda_type."',

												zone='".$zone."',visit_sequence='".$visit_sequence."',

												appointment_date='".addslashes($appointment_date)."',

												date_of_birth='".addslashes($date_of_birth)."',

												date_of_anniversary='".addslashes($date_of_anniversary)."',

												whatsapp_no='".$whatsapp_no."',

												beneficiary_name='".addslashes($beneficiary_name)."',

												pin='".addslashes($pin)."',

												IFS_code='".$IFS_code."',

												retailer_app='".$retailer_app."',
												is_new_customer='".$is_new_customer."',
												category_of_store='".$category_of_store."',
												is_nlp='".addslashes($nlp)."',
												cluster='".addslashes($cluster)."',
												download_time=CURRENT_TIMESTAMP() 

											 WHERE  ".$update_condition."";

							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

							//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

							//STOP base latt and base longi update on 15-11-2019 for new customer image and base latt longi updation if geo_fencing_menu is on.

						}

						if(($credit_limit_db!=$credit_limit))

						{

							$sqlupdatedcredit  = "update customer_master ";

							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";

							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 

											 WHERE ".$update_condition."";

							mysqli_query($link,$sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

						 // modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						}

						$customer_code=$customer_code_db;

						//$starsathi_operation='UPDATE';

					}

					//For customer sauda limit in ASL

					if(strtoupper($folderName)=='ASL'){

						if($customer_type!='R'){

							$sqlcustomersaudalimit="SELECT customer_code FROM customer_sauda_limit WHERE customer_code='".$dns_customer_code."'";

							$rscustomersaudalimit=mysqli_query($link,$sqlcustomersaudalimit);

							$countcustomersaudalimit=mysqli_num_rows($rscustomersaudalimit);

							if($countcustomersaudalimit <1)

							{

								$sqlinsertcustomersdaudalimit="INSERT INTO customer_sauda_limit ";

								$sqlinsertcustomersdaudalimit .= " SET customer_code='".$dns_customer_code."'";

								$sqlinsertcustomersdaudalimit .= " , sauda_limit='".$sauda_limit."'";

								$sqlinsertcustomersdaudalimit .= " 	,download_time=CURRENT_TIMESTAMP()";

								mysqli_query($link,$sqlinsertcustomersdaudalimit);

							}

							else

							{

								$sqlupdatesaudalimit="UPDATE customer_sauda_limit SET sauda_limit='".$sauda_limit."',download_time=CURRENT_TIMESTAMP() WHERE 

													customer_code='".$dns_customer_code."'";

								$rsupdatesaudalimit=mysqli_query($link,$sqlupdatesaudalimit);

							}

							$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE 

													customer_code='".$customer_code."'";

							mysqli_query($link,$sqlupdatecustomerroute);

						}

					}

					//For Distributor route creation

					  if($folderName=='HALDIRAM' || $folderName=='OSHEA' || $folderName=='AJANTA' || strtoupper($folderName)=='ASL')

					   {

						   if($customer_type!='R')

						   {

							   $sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE 

							   							distributor_code='".$customer_code."' AND emp_code='".$emp_code."'";

								$rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);

								$countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

							   if($countchkdistributorroute==0)

							   {	

							   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

														route_code='".$route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";

							   $rsinsertdistributorroute=mysqli_query($link,$sqlinsertdistributorroute);

							   }

							   else

							   {

								   $sqlupdatedistributorroute="UPDATE distributor_route_relation SET route_code='".$route_code."',

												acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 

												distributor_code='".$customer_code."' AND emp_code='".$emp_code."'";

									mysqli_query($link,$sqlupdatedistributorroute);	

							   }

						   }

					   }

					//End of Distributor route creation

					//For customer route relation

					if(strtoupper($folderName)=='DURO')
					{

						$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 

									   customer_code='".$customer_code."'  AND  route_code='".$route_code."' AND emp_code='".$emp_code."'";

						$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);

						$countcustomerroute=mysqli_num_rows($rsselcustomerroute);

						if($countcustomerroute==0)

						{

							$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',

													 route_code='".$route_code."',

													emp_code='".$emp_code."',

													acedns='".$acedns."',

													download_time=CURRENT_TIMESTAMP()";

							mysqli_query($link,$sqlinsertcustomerroute);

						}

						else

						{

							$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 

													customer_code='".$customer_code."' AND route_code='".$route_code."' AND emp_code='".$emp_code."'";

							mysqli_query($link,$sqlupdatecustomerroute);						

						}

					}
					else
					{

						$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 
									   customer_code='".$customer_code."'  AND emp_code='".$emp_code."'";
						//exit();
						$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);
						$countcustomerroute=mysqli_num_rows($rsselcustomerroute);
						if($countcustomerroute==0)
						{
							$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',
													 route_code='".$route_code."',
													emp_code='".$emp_code."',
													acedns='".$acedns."',
													download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlinsertcustomerroute);
						}
						else
						{
							$sqlcustomermulticheck="SELECT customer_code FROM customer_route_emp_relation WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."')";
							$rscustomermulticheck=mysqli_query($link,$sqlcustomermulticheck);
							while($rowcustomermulticheck=mysqli_fetch_assoc($rscustomermulticheck))
							{
								$customer_code_multiple=$rowcustomermulticheck['customer_code'];
								$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code='".$route_code."',
														acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 
														customer_code='".$customer_code_multiple."' AND emp_code='".$emp_code."'";
								mysqli_query($link,$sqlupdatecustomerroute);
							}
						}
					}

					if($folderName=='STAR')

					{

					   $mapped_emp_code_string=$mapped_emp_code_string."'".$emp_code."'".',';

					}

					//For array preperation of STAR SATHI server data insertion

				   if(strtoupper($folderName)=='STAR')
				   {
					  if(strpos($dns_customer_code,',')===false){
					  $cust_ins_fields = array(
								'dns_customer_code' => $dns_customer_code,
								'customer_name' => $customer_name,
								'branch_code' => $branch_code_name,
								'phone_no' => $phone_no,
								'route_code' => $dns_route_code,
								'route_name' => $route_name,
								'acedns' => $acedns,
								'cust_type' => $customer_type,
								'address' => $address,
								'zone' => $zone,
								'whatsapp_no' => $whatsapp_no,
								'rds_tag' => $rds_tag,
								'email' => $email,
							);

					  $cust_in_final_array[]=$cust_ins_fields;		
					  $cust_ins_fields_string = http_build_query($cust_in_final_array); 
					  }
					}
				   if(strtoupper($folderName)=='SUPERSHAKTI' && $customer_type!='' &&  $customer_type!='R')
				   {
					  if(strpos($dns_customer_code,',')===false){
					  $cust_ins_fields = array(
					  			'customer_code' => $customer_code,
								'dns_customer_code' => $dns_customer_code,
								'customer_name' => $customer_name,
								'branch_code' => $branch_code_name,
								'phone_no' => $phone_no,
								'route_code' => $dns_route_code,
								'route_name' => $route_name,
								'acedns' => $acedns,
								'cust_type' => $customer_type,
								'address' => $address,
								'whatsapp_no' => $whatsapp_no,
								'rds_tag' => $rds_tag,
							);

					  $cust_in_final_array[]=$cust_ins_fields;		
					  $cust_ins_fields_string = http_build_query($cust_in_final_array); 
					  }
					}
					if(strtoupper($folderName)=='GOLDSTONET' && $customer_type!='')
				   {
					  if(strpos($dns_customer_code,',')===false){
					  $cust_ins_fields = array(
					  			'customer_code' => $customer_code,
								'dns_customer_code' => $dns_customer_code,
								'customer_name' => $customer_name,
								'branch_code' => $branch_code_name,
								'phone_no' => $phone_no,
								'route_code' => $dns_route_code,
								'route_name' => $route_name,
								'acedns' => $acedns,
								'cust_type' => $customer_type,
								'address' => $address,
								'whatsapp_no' => $whatsapp_no,
								'rds_tag' => $rds_tag,
							);

					  $cust_in_final_array[]=$cust_ins_fields;		
					  $cust_ins_fields_string = http_build_query($cust_in_final_array); 
					  }
					}
					 //End of array preperation for STAR SATHI server

					//exit();

					//End for customer route relation

					//For customer branch relation

					/*$branch_code_array=array();

					if($providing_code=='yes'){

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";

					$rsbranchcode=mysqli_query($link,$sqlbranchcode);

					while($rowbranchcode=mysqli_fetch_assoc($rsbranchcode))

					{

						$branch_code=$rowbranchcode['branch_code'];

						$sqlcustomerbranch="SELECT customer_code,acedns FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

						$rscustomerbranch=mysqli_query($link,$sqlcustomerbranch);

						$countcustomerbranch=mysqli_num_rows($rscustomerbranch);

						if($countcustomerbranch <1)

						{

							$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";

							$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";

							$sqlinsertcustomerbranch .= " , branch_code='".$branch_code."'";

							$sqlinsertcustomerbranch .= " ,acedns='Y'";

							$sqlinsertcustomerbranch .= " ,download_time=CURRENT_TIMESTAMP()";

							mysqli_query($link,$sqlinsertcustomerbranch);

							modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						}

						else

						{

							//For acedns  Y

							$rowcustomerbranch=mysqli_fetch_assoc($rscustomerbranch);

							$acedns_db=$rowcustomerbranch['acedns'];

							if($acedns_db=='N')

							{

								$sqlupdateacednsstatus="UPDATE customer_branch_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE 

													customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

								$rsupdateacednsstatus=mysqli_query($link,$sqlupdateacednsstatus);

								modifyempdatadownloadlog($emp_code,strtoupper($folderName));

							}

						}

						array_push($branch_code_array,$branch_code);

					}

				}

				else

				{

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')";

					$rsbranchcode=mysqli_query($link,$sqlbranchcode);

					while($rowbranchcode=mysqli_fetch_assoc($rsbranchcode))

					{

						$branch_code=$rowbranchcode['branch_code'];

						$sqlcustomerbranch="SELECT customer_code,acedns FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

						$rscustomerbranch=mysqli_query($link,$sqlcustomerbranch);

						$countcustomerbranch=mysqli_num_rows($rscustomerbranch);

						if($countcustomerbranch <1)

						{

							$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";

							$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";

							$sqlinsertcustomerbranch .= " ,branch_code='".$branch_code."'";

							$sqlinsertcustomerbranch .= " ,acedns='Y'";

							$sqlinsertcustomerbranch .= " ,download_time=CURRENT_TIMESTAMP()";

							mysqli_query($link,$sqlinsertcustomerbranch);

						}

						else

						{

							//For acedns  Y

							$rowcustomerbranch=mysqli_fetch_assoc($rscustomerbranch);

							$acedns_db=$rowcustomerbranch['acedns'];

							if($acedns_db=='N')

							{

								$sqlupdateacednsstatus="UPDATE customer_branch_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE 

														customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

								$rsupdateacednsstatus=mysqli_query($link,$sqlupdateacednsstatus);

							}

						}

						array_push($branch_code_array,$branch_code);

					}

				}

				//For acedns  N

				$sqlcutomerallbranches="SELECT branch_code FROM customer_branch_relation WHERE customer_code='".$customer_code."'";

				$rscustomerallbranches=mysqli_query($link,$sqlcutomerallbranches);

				while($rowcustomerallbranches=mysqli_fetch_assoc($rscustomerallbranches))

				{

					$branch_code_active=$rowcustomerallbranches['branch_code'];

					if(!in_array($branch_code_active,$branch_code_array))

					{

						$sqlupdatecustomerbranch="UPDATE customer_branch_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() WHERE 

												customer_code='".$customer_code."' AND branch_code='".$branch_code_active."'";

						$rsupdatecustomerbranch=mysqli_query($link,$sqlupdatecustomerbranch);

					}

				}*/

			//End of acedns  N

			//End For customer branch relation	

					}//End of employee for each

					if($folderName=='STAR')

					{

						$mapped_emp_code_string_final=substr($mapped_emp_code_string,0,-1);

						$sql_update_customerrouteacedns="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 

															WHERE customer_code='".$customer_code."' AND emp_code NOT IN(".$mapped_emp_code_string_final.")";

						mysqli_query($link,$sql_update_customerrouteacedns);								 

					}

					if($folderName=='ARCHITA')

					{

						if($customer_type=='D' && $acedns=='N')

						   {

							   $sqlselcustomerrouteacedns="SELECT customer_code FROM customer_route_emp_relation WHERE customer_code='".$customer_code."' AND acedns='Y'";

							   $rsselcustomerrouteacedns=mysqli_query($link,$sqlselcustomerrouteacedns);

							   $countcustomerrouteacedns=mysqli_num_rows($rsselcustomerrouteacedns);

							   if($countcustomerrouteacedns==0)

							   {

							   $sqlupdatedistributorrouteacedns="UPDATE distributor_route_relation SET acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() 

							   								WHERE distributor_code='".$customer_code."'";

							   mysqli_query($link,$sqlupdatedistributorrouteacedns);

							   }

						   }

						//For customer branch relation

							$branch_code_array=array();

							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$state."')";

							$rsbranchcode=mysqli_query($link,$sqlbranchcode);

							while($rowbranchcode=mysqli_fetch_assoc($rsbranchcode))

							{

								$branch_code=$rowbranchcode['branch_code'];

								$sqlcustomerbranch="SELECT customer_code,acedns FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

								$rscustomerbranch=mysqli_query($link,$sqlcustomerbranch);

								$countcustomerbranch=mysqli_num_rows($rscustomerbranch);

								if($countcustomerbranch <1)

								{

									$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";

									$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";

									$sqlinsertcustomerbranch .= " , branch_code='".$branch_code."'";

									$sqlinsertcustomerbranch .= " ,acedns='Y'";

									$sqlinsertcustomerbranch .= " ,download_time=CURRENT_TIMESTAMP()";

									mysqli_query($link,$sqlinsertcustomerbranch);

								}

								else

								{

									//For acedns  Y

									$rowcustomerbranch=mysqli_fetch_assoc($rscustomerbranch);

									$acedns_db=$rowcustomerbranch['acedns'];

									if($acedns_db=='N')

									{

										$sqlupdateacednsstatus="UPDATE customer_branch_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE 

															customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

										$rsupdateacednsstatus=mysqli_query($link,$sqlupdateacednsstatus);

									}

								}

								array_push($branch_code_array,$branch_code);

						   }

							//For acedns  N

							$sqlcutomerallbranches="SELECT branch_code FROM customer_branch_relation WHERE customer_code='".$customer_code."'";

							$rscustomerallbranches=mysqli_query($link,$sqlcutomerallbranches);

							while($rowcustomerallbranches=mysqli_fetch_assoc($rscustomerallbranches))

							{

								$branch_code_active=$rowcustomerallbranches['branch_code'];

								if(!in_array($branch_code_active,$branch_code_array))

								{

									$sqlupdatecustomerbranch="UPDATE customer_branch_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() WHERE 

															customer_code='".$customer_code."' AND branch_code='".$branch_code_active."'";

									$rsupdatecustomerbranch=mysqli_query($link,$sqlupdatecustomerbranch);

								}

							}

						//End of acedns  N

					}//End ARCHITA

				  }

					$rec_count++;

				}//End of for loop

				if(strtoupper($folderName)=='STAR')

				  {

					array_push($upload_master_table_array,'customer_master');

				  }

			}//End of else

			if(route_plan=='yes')

			{

				$sqlrouteextra="SELECT  route_code,route_name FROM route_master WHERE route_name IN('Office Visit','Leave Request')";

				$rsrouteextra=mysqli_query($link,$sqlrouteextra);

				$cntrouteextra=mysqli_num_rows($rsrouteextra);

				if($cntrouteextra==0)

				{

					$new_route_array=array('Office Visit','Leave Request');

					foreach($new_route_array as $routeval)

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

						}

						if($providing_code=='yes'){

							$dns_route_code=$routeval;

						}

						else $dns_route_code='';

						$sqlrouteextra  = "insert into route_master ";

						$sqlrouteextra .= " SET route_code='".$max_route_code."'";

						$sqlrouteextra .= " ,dns_route_code='".$dns_route_code."'";

						$sqlrouteextra .= " ,route_name='".$routeval."'";

						$sqlrouteextra .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlrouteextra) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin for extra route.");

					}

				}

			}

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

		

//start customer master employee replacement MAGIK
if(similar_file_exists("../csv/$folderName/Customer Master MAGIK.csv")!=false)
{
	$filename=similar_file_exists("../csv/$folderName/Customer Master MAGIK.csv");

	$rec_count = 0;

	$ins_count = 0;

	$err = "";
	$customer_code_array=array();
		$lines = file($filename);

		//print_r($lines);
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

				$customer_code	=trim($data[0]);
				$customer_name	=trim(preg_replace('/[\r\n]+/', '',$data[1]));
				$route_name  =trim(preg_replace('/[\r\n]+/', '',$data[4]));
				$old_emp_name  =trim(preg_replace('/[\r\n]+/', '',$data[5]));
				$new_emp_name  =trim(preg_replace('/[\r\n]+/', '',$data[6]));

				$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
				$rsroutecode=mysqli_query($link,$sqlroutecode);
				$rowroutecode=mysqli_fetch_assoc($rsroutecode);
				$route_code=$rowroutecode['route_code'];
				
				$sqlempnamechkold="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($old_emp_name)."'";
				$rsempnamechkold=mysqli_query($link,$sqlempnamechkold);
				$rowempnamechkold=mysqli_fetch_assoc($rsempnamechkold);
				$emp_code_old=$rowempnamechkold['emp_code'];
				
				$sqlempnamechknew="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($new_emp_name)."'";
				$rsempnamechknew=mysqli_query($link,$sqlempnamechknew);
				$rowempnamechknew=mysqli_fetch_assoc($rsempnamechknew);
				$emp_code_new=$rowempnamechknew['emp_code'];
				
				$sqlcustomernamechk="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND 
									route_code='".$route_code."'";
				$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
				$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
				$csv_row_count=$rec_count+1;
					if($countcustomernamechk >0)
					{
						$rowcustomercode=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code=$rowcustomercode['customer_code'];
						if(!in_array($customer_code,$customer_code_array))
						{
							$sqlupdatecustomerrouteN="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 
													WHERE customer_code='".$customer_code."'";
							$rsupdatecustomerrouteN=mysqli_query($link,$sqlupdatecustomerrouteN);
							$sqlupdatecustomerrouteY="UPDATE customer_route_emp_relation SET acedns='Y',emp_code='".addslashes($emp_code_new)."',download_time=CURRENT_TIMESTAMP() 
													WHERE customer_code='".$customer_code."' AND emp_code='".$emp_code_old."'";
							$rsupdatecustomerrouteY=mysqli_query($link,$sqlupdatecustomerrouteY);
								array_push($customer_code_array,$customer_code);
						}
					}
				}
			   $rec_count++;
			}
		$successval=1;
	}
	//End customer master MAGIK

		

	//For Employee app access checking

	/*if(similar_file_exists("../csv/$folderName/Employee master.csv")!=false)

	{

		$sqlempcodefetch="SELECT emp_code,app_access FROM employee_master WHERE acedns='Y'";

		$rsempcodefetch=mysqli_query($link,$sqlempcodefetch);

		while($rowempcodefetch=mysqli_fetch_assoc($rsempcodefetch))

		{

			$emp_code_fetched=$rowempcodefetch['emp_code'];

			$app_access=$rowempcodefetch['app_access'];

			$sqlchkchild="SELECT COUNT(emp_code) AS total_child FROM employee_master WHERE FIND_IN_SET('".$emp_code_fetched."', reporting_to)";

			$rschkchild=mysqli_query($link,$sqlchkchild);

			$rowchkchild=mysqli_fetch_assoc($rschkchild);

			$total_child=$rowchkchild['total_child'];

			

			$sqlchkcustomer="SELECT COUNT(customer_code) AS total_customer FROM customer_master WHERE emp_code='".$emp_code_fetched."'";

			$rschkcustomer=mysqli_query($link,$sqlchkcustomer);

			$rowchkcustomer=mysqli_fetch_assoc($rschkcustomer);

			$total_customer=$rowchkcustomer['total_customer'];

		

		if($total_child==0  && $total_customer==0)

		{

			if($app_access=='Y')

			{

				$sqlupdateempappaccess="UPDATE employee_master SET app_access='N' WHERE emp_code='".$emp_code_fetched."'";

				mysqli_query($link,$sqlupdateempappaccess);

			}

		}

		else

		{

			if($app_access=='N')

			{

				$sqlupdateempappaccess="UPDATE employee_master SET app_access='Y' WHERE emp_code='".$emp_code_fetched."'";

				mysqli_query($link,$sqlupdateempappaccess);

			}

		}

	  }

	}*/

	//For Vendor Master CSV

	if(similar_file_exists("../csv/$folderName/Vendor master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Vendor master.csv");

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

			  

				$dns_vendor_code=trim($data[0]);

				$vendor_name=trim($data[1]);

				$emp_code_name=trim($data[2]);

				$rds_code_name=trim($data[3]);

				$branch_code_name=trim($data[4]);

				

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

				$sqlvendor .= "  ,dns_vendor_code='".mysqli_real_escape_string($dns_vendor_code)."'";

				$sqlvendor .= "  ,vendor_name='".mysqli_real_escape_string($vendor_name)."'";

				$sqlvendor .= " , branch_code='".mysqli_real_escape_string($branch_code)."'";

				$sqlvendor .= " , rds_code='".mysqli_real_escape_string($rds_code)."'";

				$sqlvendor .= " , emp_code='".mysqli_real_escape_string($emp_code)."'";

				mysqli_query($link,$sqlvendor) or array_push($error_array,"mysqli_error().Internal error in Vendor master.csv.Please check.");

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

	if(similar_file_exists("../csv/$folderName/Outstanding.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Outstanding.csv");

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

					if($providing_code=='yes')

					{

						if($folderName=='STAR')

						{

							$emp_code_name=trim($data[1]);

							if(strpos($emp_code_name,';')!=false)

							 {

								$emp_code_name=str_replace(';',',',$emp_code_name);

							 }

							$invoice_id=trim($data[2]);

							$date=trim($data[3]);

							if(strpos($date,'/')!=false){

							 $dateArr=explode('/',$date);

							}

							if(strpos($date,'-')!=false){

							 $dateArr=explode('-',$date);

							}

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

							//$vertical_value	  =trim($data[6]); 

							$achievement_qty=trim($data[6]);

						}

						else

						{

							//$emp_code_name=trim($data[1]);

							$invoice_id=trim($data[1]);

							$date=trim($data[2]);

							if(strpos($date,'/')!=false){

							 $dateArr=explode('/',$date);

							}

							if(strpos($date,'-')!=false){

							 $dateArr=explode('-',$date);

							}

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

					}

					else

					{

						if($folderName=='ROUNAK')

						{

							$emp_code_name=trim($data[1]);

							$invoice_id=trim($data[2]);

							$date=trim($data[3]);

							if(strpos($date,'/')!=false){

							 $dateArr=explode('/',$date);

							}

							if(strpos($date,'-')!=false){

							 $dateArr=explode('-',$date);

							}

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

						   if(strpos($date,'/')!=false){

							 $dateArr=explode('/',$date);

							}

							if(strpos($date,'-')!=false){

							 $dateArr=explode('-',$date);

							}



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

					}

	

					if($folderName=='STAR')

					 {

						$sqlempcode="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code, '".$emp_code_name."')";

						$rsempcode=mysqli_query($link,$sqlempcode);

						while($rowempcode=mysqli_fetch_assoc($rsempcode))

						{

						   $emp_code=$rowempcode['emp_code'];

						   $sqlcustomercode="SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRR

						   					WHERE CM.customer_code=CRR.customer_code AND CM.dns_customer_code='".$customer_code_name."' 

											AND CRR.emp_code='".$emp_code."'";

						   $rscustomercode=mysqli_query($link,$sqlcustomercode);

						   $rowcustomercode=mysqli_fetch_assoc($rscustomercode);

						   $customer_code=$rowcustomercode['customer_code'];



							$sql  = "insert into outstanding ";

							$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";

							$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";

							$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";

							$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";

							$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";

							$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";

							mysqli_query($link,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");

						}

					 }

					 else if($folderName=='ROUNAK')

					 {

						$sqlempcode="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name, '".$emp_code_name."')";

						$rsempcode=mysqli_query($link,$sqlempcode);

						while($rowempcode=mysqli_fetch_assoc($rsempcode))

						{

						   $emp_code=$rowempcode['emp_code'];

						   $sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_code_name."' 

											AND emp_code='".$emp_code."'";

						   $rscustomercode=mysqli_query($link,$sqlcustomercode);

						   $rowcustomercode=mysqli_fetch_assoc($rscustomercode);

						   $customer_code=$rowcustomercode['customer_code'];



							$sql  = "insert into outstanding ";

							$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";

							$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";

							$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";

							$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";

							$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";

							$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";

							mysqli_query($link,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");

						}

					 }

					else

					{

						if($providing_code=='yes'){

							$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

							$rsempcode=mysqli_query($link,$sqlempcode);

							$rowempcode=mysqli_fetch_assoc($rsempcode);

							$emp_code=$rowempcode['emp_code'];

						   $sqlcustomercode="SELECT customer_code,emp_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

						}

						else

						{

							$sqlcustomercode="SELECT customer_code,emp_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";

						}

						$rscustomercode=mysqli_query($link,$sqlcustomercode);

						$countcustomercode=mysqli_num_rows($rscustomercode);

						

						if($countcustomercode <1 && !in_array($customer_code_name,$customeroutstandingmissmatchArr))

						{

							$customeroutstandingmissmatch='';

							$customeroutstandingmissmatch.=$customer_code_name.',';

							array_push($customeroutstandingmissmatchArr,$customer_code_name);

							//$lineexcel .= $customeroutstandingmissmatch."\n";

						}

						$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

						$customer_code=$rowcustomercode['customer_code'];

						$emp_code=$rowcustomercode['emp_code'];

	

						$sql  = "insert into outstanding ";

						$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";

						$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";

						$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";

						$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";

						$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";

						$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";

					

						mysqli_query($link,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");

					}

					/*if($emp_code!='')

					 {

						modifyempdatadownloadlog($emp_code,strtoupper($folderName));

					 }*/

				 }

				 $rec_count++;

			}

			//exit();

			//Customer code checking start

				//print_r($customeroutstandingmissmatchArr);

				/*$customeroutstandingmissmatch=substr($customeroutstandingmissmatch,0,-1);

				$errorcustomeroutstanding=$customeroutstandingmissmatch.' exists in outstanding but not exists in customer_master.';

				array_push($error_array,$errorcustomeroutstanding);*/

				/*$data = str_replace("\r","",$lineexcel);

				

				header("Content-type: application/x-msdownload"); 

				header("Content-Disposition: attachment; filename=customermissmatch.xls"); 

				header("Pragma: no-cache"); 

				header("Expires: 0"); 

				print "$data";*/

			//Customer code checking end		

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Outstanding.csv is wrong.";

			exit();

		}*/

		

	

	//For MRP CSV

	if(similar_file_exists("../csv/$folderName/MRP.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/MRP.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

			$lines = file($filename);

			/*$sqldelete="truncate mrp";

			$rsdelete=mysqli_query($link,$sqldelete);*/

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

					

					if(branch_wise_mrp == 'yes')

					{

						if($branch_code_name == '')

						{

							echo "Please provide valid branch code at row ".$csv_row_count;

							die;

						}

					}

					

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

					$acedns=trim($data[8]);

					$ws_price=trim($data[9]);

					$distributor_price=trim($data[10]);

					$ss_price=trim($data[11]);

					$depot_price=trim($data[12]);

					$state_code_name=trim($data[13]);

					$UOM=trim($data[14]);

					

					if($folderName=='SHYAM'){

						if($distributor_price==0){

							$distributor_price=$sale_rate;

						}

						if($sale_rate==0){

							$sale_rate=$distributor_price;

						}

					}

				

					if($providing_code=='yes'){

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";

						$sqlstatecode="SELECT state_code FROM state_master WHERE dns_state_code='".$state_code_name."'";

					}

					else

					{

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";

						$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_code_name."%'";

						//$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' 

								//AND product_group_code='".$brand_code_name."' AND product_sub_group_code='".$brand_form_code_name."'";

					}

					$rsbranchcode=mysqli_query($link,$sqlbranchcode);

					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

					$branch_code=$rowbranchcode['branch_code'];

					

					$rsstatecode=mysqli_query($link,$sqlstatecode);

					$rowstatecode=mysqli_fetch_assoc($rsstatecode);

					$state_code=$rowstatecode['state_code'];

					if($providing_code=='yes'){

						if(branch_wise_product=='yes' || $folderName=='DNV')

						{

							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";

						}

						else

						{

							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

						}

					}

					else if($folderName=='HALDIRAM' || $folderName=='SKIPPER')

					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

					}

					else if($folderName=='NIMBUS')

					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' AND 

									state_code='".$state_code."'";

					}
					else if(strtoupper($folderName)=='CORAL')
					{

					$prod_code_name_parts=explode('/',$prod_code_name);
					$sqlprodgroupname="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($prod_code_name_parts[0])."'";
					$rsprodgroupname=mysqli_query($link,$sqlprodgroupname);
					$countprodgroupname=mysqli_num_rows($rsprodgroupname);
					$rowprodgroupname=mysqli_fetch_assoc($rsprodgroupname);	
					$product_group_code=$rowprodgroupname['product_group_code'];
						
					$sqlprodsubgroupname="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($prod_code_name_parts[1])."' AND product_group_code='".$product_group_code."'";
					$rsprodsubgroupname=mysqli_query($link,$sqlprodsubgroupname);
					$countprodsubgroupname=mysqli_num_rows($rsprodsubgroupname);
					$rowprodsubgroupname=mysqli_fetch_assoc($rsprodsubgroupname);	
					$product_sub_group_code=$rowprodsubgroupname['product_sub_group_code'];
						
				$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name_parts[2])."' AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";

					}
					else
					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' AND acedns='Y'";
					}

					$rsprodcode=mysqli_query($link,$sqlprodcode);

					$rowprodcode=mysqli_fetch_assoc($rsprodcode);

					$prod_code=$rowprodcode['prod_code'];

					

					/*if($prod_code=='' && !in_array($prod_code_name,$prod_code_array))

					{

						echo $prod_code_name.'<br />';

						array_push($prod_code_array,$prod_code_name);

					}*/
					if(uom_wise_mrp=='yes' && branch_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."' AND branch_code='".$branch_code."'";

					}
					else if(uom_wise_mrp=='yes' && state_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."' AND state_code='".$state_code."'";

					}
					else if(uom_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."'";

					}

					else if(branch_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."'";

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

						$vertical_value_db=$rowmrpchk['vertical_value'];	

						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate || $acedns_db!=$acedns || $ws_price_db!=$ws_price || $distributor_price_db!=$distributor_price || $ss_price_db!=$ss_price || $depot_price_db!=$depot_price || $vertical_value_db!=$vertical_value){

							$sqlupdate  = "UPDATE mrp ";

							$sqlupdate .= " SET mrp='".mysqli_real_escape_string($mrp)."'";

							$sqlupdate .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";

							$sqlupdate .= " , acedns='".mysqli_real_escape_string($acedns)."'";

							$sqlupdate .= " , ws_rate='".mysqli_real_escape_string($ws_price)."'";

							$sqlupdate .= " , distributor_rate='".mysqli_real_escape_string($distributor_price)."'";

							$sqlupdate .= " , ss_rate='".mysqli_real_escape_string($ss_price)."'";

							$sqlupdate .= " , depot_rate='".mysqli_real_escape_string($depot_price)."'";

							$sqlupdate .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."' 

											AND branch_code='".$branch_code."' AND state_code='".$state_code."' AND UOM='".$UOM."'";

									

							mysqli_query($link,$sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");

							$updateflag=1;

						}

					}

					/*if(branch_wise_mrp=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log

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

					}*///End For emp data download log

				}

				 $rec_count++;

			}

			 if(branch_wise_mrp=='no')//Start For emp data download log with no branch tagging

			{

				$emp_code='';

				modifyempdatadownloadlog($emp_code,strtoupper($folderName));

			}//End For emp data download log with no branch tagging



			//Product code checking start

				$sqlprodcodeprice="SELECT product_code FROM mrp WHERE product_code NOT IN

									(SELECT prod_code FROM product_master) GROUP BY product_code";

				$rsprodcodeprice=mysqli_query($link,$sqlprodcodeprice);

				$cntprodcodeprice=mysqli_num_rows($rsprodcodeprice);

				if($cntprodcodeprice>0)

				{

					$prodcodeprice='';

					while($rowprodcodeprice=mysqli_fetch_assoc($rsprodcodeprice))

					{

						$prodcodeprice=$prodcodeprice.$rowprodcodeprice['product_code'].',';

					}

					$prodcodeprice=substr($prodcodeprice,0,-1);

					$errorprodcodeprice=$prodcodeprice.' exists in MRP but not exists in Sku Master.';

					array_push($error_array,$errorprodcodeprice);

				}

			//Product code checking end		

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for MRP.csv is wrong.";

			exit();

		}*/

	//For Destination Master CSV

	if(similar_file_exists("../csv/$folderName/Destination master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Destination master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		{

			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='destination_master'";

			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

			$need_update=$rowchkupdateinfo['need_update'];

			if($need_update=='yes')

			{

				echo "Previous update process is going on destination master. Please try some time later.";

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

			  

				$dns_destination_code=trim($data[0]);

				$destination_name=trim($data[1]);

				$ex_for_type=trim($data[2]);

				if($providing_code=='yes'){

					$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($dns_destination_code)."'";

				}

				else

				{

					$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE destination_name='".addslashes($destination_name)."'";

				}

				

				$rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);

				$countdestinationnamechk=mysqli_num_rows($rsdestinationnamechk);

				

				$csv_row_count=$rec_count+1;

				if($countdestinationnamechk<1)

				{

					$sqlmaxdestinationcode="SELECT MAX(destination_code) AS max_destination_code FROM  destination_master WHERE 1";

					$rsmaxdestinationcode=mysqli_query($link,$sqlmaxdestinationcode);

					$rowmaxdestinationcode=mysqli_fetch_assoc($rsmaxdestinationcode);

					$max_destination_code=$rowmaxdestinationcode['max_destination_code'];

					

					if($max_destination_code=='')

					{

						$max_destination_code='D0001';

					}

					else

					{

						$max_destination_code++;

					}

				

					$sqldestination  = "insert into destination_master SET ";

					$sqldestination .= "   destination_code='".mysqli_real_escape_string($max_destination_code)."'";

					$sqldestination .= " , dns_destination_code='".mysqli_real_escape_string($dns_destination_code)."'";

					$sqldestination .= " , destination_name='".mysqli_real_escape_string($destination_name)."'";

					$sqldestination .= " , ex_for_type='".mysqli_real_escape_string($ex_for_type)."'";

					$sqldestination .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($link,$sqldestination) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");

					$emp_code='';

					//modifyempdatadownloadlog($emp_code,strtoupper($folderName));		

				}

				else

				{

					$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);

					$destination_code=$rowdestinationnamechk['destination_code'];



					$sqldestination  = "UPDATE destination_master SET ";

					$sqldestination .= "  dns_destination_code='".mysqli_real_escape_string($dns_destination_code)."',

										  destination_name='".addslashes($destination_name)."',

										  ex_for_type='".addslashes($ex_for_type)."',

										download_time=CURRENT_TIMESTAMP() WHERE destination_code='".mysqli_real_escape_string($destination_code)."'";

					mysqli_query($link,$sqldestination) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");

					$emp_code='';

					//modifyempdatadownloadlog($emp_code,strtoupper($folderName));		

				}

			}

		   if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		   {

			  $destination_ins_fields = array(

						'dns_destination_code' => $dns_destination_code,

						'destination_name' => $destination_name,

						'ex_for_type' => $ex_for_type,

					);

			  $destination_in_final_array[]=$destination_ins_fields;		

			  $destination_ins_fields_string = http_build_query($destination_in_final_array); 

			}

			 $rec_count++;

		}

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		  {

			array_push($upload_master_table_array,'destination_master');

		  }

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Destination master.csv is wrong.";

		exit();

	}*/

	//For Star Dealer Target CSV

	if(similar_file_exists("../csv/$folderName/Star Dealer Target.csv")!=false || similar_file_exists("../csv/$folderName/Dealer Target.csv")!=false)

	{

		if(similar_file_exists("../csv/$folderName/Star Dealer Target.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Star Dealer Target.csv");

		}

		if(similar_file_exists("../csv/$folderName/Dealer Target.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Dealer Target.csv");

		}

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

		{

			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='self_appraisal_customer_wise'";

			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);

			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);

			$need_update=$rowchkupdateinfo['need_update'];

			if($need_update=='yes')

			{

				echo "Previous update process is going on dealer target. Please try some time later.";

				die;

			}

		}

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_customer_wise";

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
				$dns_customer_code=trim($data[0]);
				$customer_name=trim($data[1]);
				/*$april_target=str_replace(',','',trim($data[2]));
				$april_achievement=str_replace(',','',trim($data[3]));

				$may_target=str_replace(',','',trim($data[4]));

				$may_achievement=str_replace(',','',trim($data[5]));

				$june_target=str_replace(',','',trim($data[6]));

				$june_achievement=str_replace(',','',trim($data[7]));

				$july_target=str_replace(',','',trim($data[8]));

				$july_achievement=str_replace(',','',trim($data[9]));

				$august_target=str_replace(',','',trim($data[10]));

				$august_achievement=str_replace(',','',trim($data[11]));

				$september_target=str_replace(',','',trim($data[12]));

				$september_achievement=str_replace(',','',trim($data[13]));

				$october_target=str_replace(',','',trim($data[14]));

				$october_achievement=str_replace(',','',trim($data[15]));

				$november_target=str_replace(',','',trim($data[16]));

				$november_achievement=str_replace(',','',trim($data[17]));

				$december_target=str_replace(',','',trim($data[18]));

				$december_achievement=str_replace(',','',trim($data[19]));

				$january_target=str_replace(',','',trim($data[20]));

				$january_achievement=str_replace(',','',trim($data[21]));

				$february_target=str_replace(',','',trim($data[22]));

				$february_achievement=str_replace(',','',trim($data[23]));

				$march_target=str_replace(',','',trim($data[24]));

				$march_achievement=str_replace(',','',trim($data[25]));*/
				$april_target=trim($data[2]);
				$april_achievement=trim($data[3]);
				$april_prev_target=trim($data[4]);
				$april_prev_achievement=trim($data[5]);
				$may_target=trim($data[6]);
				$may_achievement=trim($data[7]);
				$may_prev_target=trim($data[8]);
				$may_prev_achievement=trim($data[9]);
				$june_target=trim($data[10]);
				$june_achievement=trim($data[11]);
				$june_prev_target=trim($data[12]);
				$june_prev_achievement=trim($data[13]);
				$july_target=trim($data[14]);
				$july_achievement=trim($data[15]);
				$july_prev_target=trim($data[16]);
				$july_prev_achievement=trim($data[17]);
				$august_target=trim($data[18]);
				$august_achievement=trim($data[19]);
				$august_prev_target=trim($data[20]);
				$august_prev_achievement=trim($data[21]);
				$september_target=trim($data[22]);
				$september_achievement=trim($data[23]);
				$september_prev_target=trim($data[24]);
				$september_prev_achievement=trim($data[25]);
				$october_target=trim($data[26]);
				$october_achievement=trim($data[27]);
				$october_prev_target=trim($data[28]);
				$october_prev_achievement=trim($data[29]);
				$november_target=trim($data[30]);
				$november_achievement=trim($data[31]);
				$november_prev_target=trim($data[32]);
				$november_prev_achievement=trim($data[33]);
				$december_target=trim($data[34]);
				$december_achievement=trim($data[35]);
				$december_prev_target=trim($data[36]);
				$december_prev_achievement=trim($data[37]);
				$january_target=trim($data[38]);
				$january_achievement=trim($data[39]);
				$january_prev_target=trim($data[40]);
				$january_prev_achievement=trim($data[41]);
				$february_target=trim($data[42]);
				$february_achievement=trim($data[43]);
				$february_prev_target=trim($data[44]);
				$february_prev_achievement=trim($data[45]);
				$march_target=trim($data[46]);
				$march_achievement=trim($data[47]);
				$march_prev_target=trim($data[48]);
				$march_prev_achievement=trim($data[49]);
				if($providing_code=='yes'){
				$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_customer_wise WHERE customer_code='".addslashes($dns_customer_code)."'";
				$customer_code=$dns_customer_code;
				}
				else
				{
					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_customer_wise WHERE customer_code='".$customer_code."'";
				}
				$rschkcustomercode=mysqli_query($link,$sqlchkcustomercode);
				$countchkcustomercode=mysqli_num_rows($rschkcustomercode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchkcustomercode ==0)
				{
					$sqlselfappraisal  = "insert into self_appraisal_customer_wise SET ";
					$sqlselfappraisal .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";
				$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";
				$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";
				$sqlselfappraisal .= " , apr_prev_y_target='".mysqli_real_escape_string($april_prev_target)."'";
				$sqlselfappraisal .= " , apr_prev_y_achievement='".mysqli_real_escape_string($april_prev_achievement)."'";
				$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
				$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";
				$sqlselfappraisal .= " , may_prev_y_target='".mysqli_real_escape_string($may_prev_target)."'";
				$sqlselfappraisal .= " , may_prev_y_achievement='".mysqli_real_escape_string($may_prev_achievement)."'";
				$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
				$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";
				$sqlselfappraisal .= " , jun_prev_y_target='".mysqli_real_escape_string($june_prev_target)."'";
				$sqlselfappraisal .= " , jun_prev_y_achievement='".mysqli_real_escape_string($june_prev_achievement)."'";
				$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
				$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";
				$sqlselfappraisal .= " , jul_prev_y_target='".mysqli_real_escape_string($july_prev_target)."'";
				$sqlselfappraisal .= " , jul_prev_y_achievement='".mysqli_real_escape_string($july_prev_achievement)."'";
				$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
				$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";
				$sqlselfappraisal .= " , aug_prev_y_target='".mysqli_real_escape_string($august_prev_target)."'";
				$sqlselfappraisal .= " , aug_prev_y_achievement='".mysqli_real_escape_string($august_prev_achievement)."'";
				$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";
				$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";
				$sqlselfappraisal .= " , sep_prev_y_target='".mysqli_real_escape_string($september_prev_target)."'";
				$sqlselfappraisal .= " , sep_prev_y_achievement='".mysqli_real_escape_string($september_prev_achievement)."'";
				$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
				$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";
				$sqlselfappraisal .= " , oct_prev_y_target='".mysqli_real_escape_string($october_prev_target)."'";
				$sqlselfappraisal .= " , oct_prev_y_achievement='".mysqli_real_escape_string($october_prev_achievement)."'";
				$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";
				$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";
				$sqlselfappraisal .= " , nov_prev_y_target='".mysqli_real_escape_string($november_prev_target)."'";
				$sqlselfappraisal .= " , nov_prev_y_achievement='".mysqli_real_escape_string($november_prev_achievement)."'";
				$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";
				$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";
				$sqlselfappraisal .= " , dec_prev_y_target='".mysqli_real_escape_string($december_prev_target)."'";
				$sqlselfappraisal .= " , dec_prev_y_achievement='".mysqli_real_escape_string($december_prev_achievement)."'";
				$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
				$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";
				$sqlselfappraisal .= " , jan_prev_y_target='".mysqli_real_escape_string($january_prev_target)."'";
				$sqlselfappraisal .= " , jan_prev_y_achievement='".mysqli_real_escape_string($january_prev_achievement)."'";
				$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
				$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";
				$sqlselfappraisal .= " , feb_prev_y_target='".mysqli_real_escape_string($february_prev_target)."'";
				$sqlselfappraisal .= " , feb_prev_y_achievement='".mysqli_real_escape_string($february_prev_achievement)."'";

				$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
				$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";
				$sqlselfappraisal .= " , mar_prev_y_target='".mysqli_real_escape_string($march_prev_target)."'";
				$sqlselfappraisal .= " , mar_prev_y_achievement='".mysqli_real_escape_string($march_prev_achievement)."'";
				$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Star Dealer Target.csv.Please check.");
				}
				else
				{
					$sqlselfappraisalupdate  = "update self_appraisal_customer_wise SET ";
					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_target='".mysqli_real_escape_string($april_prev_target)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_achievement='".mysqli_real_escape_string($april_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_target='".mysqli_real_escape_string($may_prev_target)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_achievement='".mysqli_real_escape_string($may_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_prev_y_target='".mysqli_real_escape_string($june_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jun_prev_y_achievement='".mysqli_real_escape_string($june_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_prev_y_target='".mysqli_real_escape_string($july_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jul_prev_y_achievement='".mysqli_real_escape_string($july_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_prev_y_target='".mysqli_real_escape_string($august_prev_target)."'";

				    $sqlselfappraisalupdate .= " , aug_prev_y_achievement='".mysqli_real_escape_string($august_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_prev_y_target='".mysqli_real_escape_string($september_prev_target)."'";

				    $sqlselfappraisalupdate .= " , sep_prev_y_achievement='".mysqli_real_escape_string($september_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_prev_y_target='".mysqli_real_escape_string($october_prev_target)."'";

				    $sqlselfappraisalupdate .= " , oct_prev_y_achievement='".mysqli_real_escape_string($october_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_target='".mysqli_real_escape_string($november_prev_target)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_achievement='".mysqli_real_escape_string($november_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_target='".mysqli_real_escape_string($december_prev_target)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_achievement='".mysqli_real_escape_string($december_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_target='".mysqli_real_escape_string($january_prev_target)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_achievement='".mysqli_real_escape_string($january_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_target='".mysqli_real_escape_string($february_prev_target)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_achievement='".mysqli_real_escape_string($february_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_target='".mysqli_real_escape_string($march_prev_target)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_achievement='".mysqli_real_escape_string($march_prev_achievement)."'";
					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Star Dealer Target.csv.Please check.");
				}
			}
			if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			   {
				  $target_ins_fields = array(
							'customer_code' => $customer_code,
							'apr_30_target' => $april_target,									                            'apr_30_achievement' => $april_achievement,
							'apr_prev_y_target' => $april_prev_target,
							'apr_prev_y_achievement' => $april_prev_achievement,
							'may_31_target' => $may_target,
							'may_31_achievement' => $may_achievement,
							'may_prev_y_target' => $may_prev_target,
							'may_prev_y_achievement' => $may_prev_achievement,
							'jun_30_target' => $june_target,
							'jun_30_achievement' => $june_achievement,
							'jun_prev_y_target' => $june_prev_target,
							'jun_prev_y_achievement' => $june_prev_achievement,
							'jul_31_target' => $july_target,
							'jul_31_achievement' => $july_achievement,
							'jul_prev_y_target' => $july_prev_target,
							'jul_prev_y_achievement' => $july_prev_achievement,
							'aug_31_target' => $august_target,
							'aug_31_achievement' => $august_achievement,
							'aug_prev_y_target' => $august_prev_target,
							'aug_prev_y_achievement' => $august_prev_achievement,
							'sep_30_target' => $september_target,
							'sep_30_achievement' => $september_achievement,
							'sep_prev_y_target' => $september_prev_target,
							'sep_prev_y_achievement' => $september_prev_achievement,
							'oct_31_target' => $october_target,
							'oct_31_achievement' => $october_achievement,
							'oct_prev_y_target' => $october_prev_target,
							'oct_prev_y_achievement' => $october_prev_achievement,
							'nov_30_target' => $november_target,
							'nov_30_achievement' => $november_achievement,
							'nov_prev_y_target' => $november_prev_target,
							'nov_prev_y_achievement' => $november_prev_achievement,
							'dec_31_target' => $december_target,
							'dec_31_achievement' => $december_achievement,
							'dec_prev_y_target' => $december_prev_target,
							'dec_prev_y_achievement' => $december_prev_achievement,
							'jan_31_target' => $january_target,
							'jan_31_achievement' => $january_achievement,
							'jan_prev_y_target' => $january_prev_target,
							'jan_prev_y_achievement' => $january_prev_achievement,
							'feb_28_target' => $february_target,
							'feb_28_achievement' => $february_achievement,
							'feb_prev_y_target' => $february_prev_target,
							'feb_prev_y_achievement' => $february_prev_achievement,	
							'mar_31_target' => $march_target,
							'mar_31_achievement' => $march_achievement,
							'mar_prev_y_target' => $march_prev_target,
							'mar_prev_y_achievement' => $march_prev_achievement,	
						);
				  $target_in_final_array[]=$target_ins_fields;		
				  $target_ins_fields_string = http_build_query($target_in_final_array); 
				}
			 $rec_count++;
		}
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		  {
			array_push($upload_master_table_array,'self_appraisal_customer_wise');
		  }
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Star Dealer Target.csv is wrong.";
		exit();
	}*/
	//For Star Target CSV
	if(similar_file_exists("../csv/$folderName/Star Branch target.csv")!=false)
	{

		$filename=similar_file_exists("../csv/$folderName/Star Branch target.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

				$april_target=trim($data[2]);

				$april_achievement=trim($data[3]);

				$may_target=trim($data[4]);

				$may_achievement=trim($data[5]);

				$june_target=trim($data[6]);

				$june_achievement=trim($data[7]);

				$july_target=trim($data[8]);

				$july_achievement=trim($data[9]);

				$august_target=trim($data[10]);

				$august_achievement=trim($data[11]);

				$september_target=trim($data[12]);

				$september_achievement=trim($data[13]);

				$october_target=trim($data[14]);

				$october_achievement=trim($data[15]);

				$november_target=trim($data[16]);

				$november_achievement=trim($data[17]);

				$december_target=trim($data[18]);

				$december_achievement=trim($data[19]);

				$january_target=trim($data[20]);

				$january_achievement=trim($data[21]);

				$february_target=trim($data[22]);

				$february_achievement=trim($data[23]);

				$march_target=trim($data[24]);

				$march_achievement=trim($data[25]);

				

				$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($dns_branch_code)."'";

				$rsbranchcode=mysqli_query($link,$sqlbranchcode);

				$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				$branch_code=$rowbranchcode['branch_code'];

				

				$sqlchkbranchcode="SELECT branch_code FROM self_appraisal_branch_wise WHERE branch_code='".addslashes($branch_code)."'";

				$rschkbranchcode=mysqli_query($link,$sqlchkbranchcode);

				$countchkbranchcode=mysqli_num_rows($rschkbranchcode);

				//$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkbranchcode==0){

				$sqlselfappraisal  = "insert into self_appraisal_branch_wise SET ";

				$sqlselfappraisal .= "   branch_code='".mysqli_real_escape_string($branch_code)."'";

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

					mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Star Target.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_branch_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

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

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$branch_code."'";

			mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Star Target.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star Target.csv is wrong.";

		exit();

	}*/

	//For Product Group Target ACh CSV

	if(similar_file_exists("../csv/$folderName/Product Group Target ACh.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Product Group Target ACh.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

			  

				$dns_emp_code=trim($data[0]);

				$emp_name=trim($data[1]);

				$product_group_name=trim($data[2]);

				$april_target=trim($data[3]);

				$april_achievement=trim($data[4]);

				$may_target=trim($data[5]);

				$may_achievement=trim($data[6]);

				$june_target=trim($data[7]);

				$june_achievement=trim($data[8]);

				$july_target=trim($data[9]);

				$july_achievement=trim($data[10]);

				$august_target=trim($data[11]);

				$august_achievement=trim($data[12]);

				$september_target=trim($data[13]);

				$september_achievement=trim($data[14]);

				$october_target=trim($data[15]);

				$october_achievement=trim($data[16]);

				$november_target=trim($data[17]);

				$november_achievement=trim($data[18]);

				$december_target=trim($data[19]);

				$december_achievement=trim($data[20]);

				$january_target=trim($data[21]);

				$january_achievement=trim($data[22]);

				$february_target=trim($data[23]);

				$february_achievement=trim($data[24]);

				$march_target=trim($data[25]);

				$march_achievement=trim($data[26]);

				

				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_name)."'";

				$rsempcode=mysqli_query($link,$sqlempcode);

				$rowempcode=mysqli_fetch_assoc($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";

				$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];

								

				$sqlchkempproductgroup="SELECT emp_code,product_group_code FROM self_appraisal_productgroup_wise 

									WHERE emp_code='".$emp_code."' AND product_group_code='".$product_group_code."'";

				$rschkempproductgroup=mysqli_query($link,$sqlchkempproductgroup);

				$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);

				//$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempproductgroup==0){

				$sqlselfappraisal  = "insert into self_appraisal_productgroup_wise SET ";

				$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";

				$sqlselfappraisal .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

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

				mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Product Group Target ACh.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_productgroup_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

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

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' 

												AND product_group_code='".$product_group_code."'";

					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Product Group Target ACh.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star  Target.csv is wrong.";

		exit();

	}*/

   //For Product Wise Target Ach

	if(similar_file_exists("../csv/$folderName/Product Wise Target Ach.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Product Wise Target Ach.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

			  

				$emp_code_name=trim($data[0]);

				$prod_code_desc_name=trim($data[1]);

				$april_target=trim($data[2]);

				$april_achievement=trim($data[3]);

				$april_prev_target=trim($data[4]);

				$april_prev_achievement=trim($data[5]);

				$may_target=trim($data[6]);

				$may_achievement=trim($data[7]);

				$may_prev_target=trim($data[8]);

				$may_prev_achievement=trim($data[9]);

				$june_target=trim($data[10]);

				$june_achievement=trim($data[11]);

				$june_prev_target=trim($data[12]);

				$june_prev_achievement=trim($data[13]);

				$july_target=trim($data[14]);

				$july_achievement=trim($data[15]);

				$july_prev_target=trim($data[16]);

				$july_prev_achievement=trim($data[17]);

				$august_target=trim($data[18]);

				$august_achievement=trim($data[19]);

				$august_prev_target=trim($data[20]);

				$august_prev_achievement=trim($data[21]);

				$september_target=trim($data[22]);

				$september_achievement=trim($data[23]);

				$september_prev_target=trim($data[24]);

				$september_prev_achievement=trim($data[25]);

				$october_target=trim($data[26]);

				$october_achievement=trim($data[27]);

				$october_prev_target=trim($data[28]);

				$october_prev_achievement=trim($data[29]);

				$november_target=trim($data[30]);

				$november_achievement=trim($data[31]);

				$november_prev_target=trim($data[32]);

				$november_prev_achievement=trim($data[33]);

				$december_target=trim($data[34]);

				$december_achievement=trim($data[35]);

				$december_prev_target=trim($data[36]);

				$december_prev_achievement=trim($data[37]);

				$january_target=trim($data[38]);

				$january_achievement=trim($data[39]);

				$january_prev_target=trim($data[40]);

				$january_prev_achievement=trim($data[41]);

				$february_target=trim($data[42]);

				$february_achievement=trim($data[43]);

				$february_prev_target=trim($data[44]);

				$february_prev_achievement=trim($data[45]);

				$march_target=trim($data[46]);

				$march_achievement=trim($data[47]);

				$march_prev_target=trim($data[48]);

				$march_prev_achievement=trim($data[49]);

				

				if($providing_code=='yes'){

					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

				}

				else{

					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				}

				$rsempcode=mysqli_query($link,$sqlempcode);

				$rowempcode=mysqli_fetch_assoc($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				if($providing_code=='yes'){

					$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($prod_code_desc_name)."'";

				}

				else{

					$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_desc_name)."'";

				}

				$rsproductcode=mysqli_query($link,$sqlproductcode);

				$rowproductcode=mysqli_fetch_assoc($rsproductcode);

				$prod_code=$rowproductcode['prod_code'];

								

				$sqlchkempproduct="SELECT emp_code,prod_code FROM self_appraisal_product_wise 

									WHERE emp_code='".$emp_code."' AND prod_code='".$prod_code."'";

				$rschkempproduct=mysqli_query($link,$sqlchkempproduct);

				$countchkempproduct=mysqli_num_rows($rschkempproduct);

				//$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempproduct==0){

				$sqlselfappraisal  = "insert into self_appraisal_product_wise SET ";

				$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";

				$sqlselfappraisal .= " , prod_code='".mysqli_real_escape_string($prod_code)."'";

				$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";

				$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

				$sqlselfappraisal .= " , apr_prev_y_target='".mysqli_real_escape_string($april_prev_target)."'";

				$sqlselfappraisal .= " , apr_prev_y_achievement='".mysqli_real_escape_string($april_prev_achievement)."'";

				$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

				$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

				$sqlselfappraisal .= " , may_prev_y_target='".mysqli_real_escape_string($may_prev_target)."'";

				$sqlselfappraisal .= " , may_prev_y_achievement='".mysqli_real_escape_string($may_prev_achievement)."'";

				$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

				$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

				$sqlselfappraisal .= " , jun_prev_y_target='".mysqli_real_escape_string($june_prev_target)."'";

				$sqlselfappraisal .= " , jun_prev_y_achievement='".mysqli_real_escape_string($june_prev_achievement)."'";

				$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

				$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

				$sqlselfappraisal .= " , jul_prev_y_target='".mysqli_real_escape_string($july_prev_target)."'";

				$sqlselfappraisal .= " , jul_prev_y_achievement='".mysqli_real_escape_string($july_prev_achievement)."'";

				$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

				$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

				$sqlselfappraisal .= " , aug_prev_y_target='".mysqli_real_escape_string($august_prev_target)."'";

				$sqlselfappraisal .= " , aug_prev_y_achievement='".mysqli_real_escape_string($august_prev_achievement)."'";

				$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

				$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

				$sqlselfappraisal .= " , sep_prev_y_target='".mysqli_real_escape_string($september_prev_target)."'";

				$sqlselfappraisal .= " , sep_prev_y_achievement='".mysqli_real_escape_string($september_prev_achievement)."'";

				$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

				$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

				$sqlselfappraisal .= " , oct_prev_y_target='".mysqli_real_escape_string($october_prev_target)."'";

				$sqlselfappraisal .= " , oct_prev_y_achievement='".mysqli_real_escape_string($october_prev_achievement)."'";

				$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

				$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

				$sqlselfappraisal .= " , nov_prev_y_target='".mysqli_real_escape_string($november_prev_target)."'";

				$sqlselfappraisal .= " , nov_prev_y_achievement='".mysqli_real_escape_string($november_prev_achievement)."'";

				$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

				$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

				$sqlselfappraisal .= " , dec_prev_y_target='".mysqli_real_escape_string($december_prev_target)."'";

				$sqlselfappraisal .= " , dec_prev_y_achievement='".mysqli_real_escape_string($december_prev_achievement)."'";

				$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

				$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

				$sqlselfappraisal .= " , jan_prev_y_target='".mysqli_real_escape_string($january_prev_target)."'";

				$sqlselfappraisal .= " , jan_prev_y_achievement='".mysqli_real_escape_string($january_prev_achievement)."'";

				$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

				$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

				$sqlselfappraisal .= " , feb_prev_y_target='".mysqli_real_escape_string($february_prev_target)."'";

				$sqlselfappraisal .= " , feb_prev_y_achievement='".mysqli_real_escape_string($february_prev_achievement)."'";

				$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

				$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

				$sqlselfappraisal .= " , mar_prev_y_target='".mysqli_real_escape_string($march_prev_target)."'";

				$sqlselfappraisal .= " , mar_prev_y_achievement='".mysqli_real_escape_string($march_prev_achievement)."'";

				$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Product Wise Target Ach.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_product_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_target='".mysqli_real_escape_string($april_prev_target)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_achievement='".mysqli_real_escape_string($april_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_target='".mysqli_real_escape_string($may_prev_target)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_achievement='".mysqli_real_escape_string($may_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_prev_y_target='".mysqli_real_escape_string($june_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jun_prev_y_achievement='".mysqli_real_escape_string($june_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_prev_y_target='".mysqli_real_escape_string($july_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jul_prev_y_achievement='".mysqli_real_escape_string($july_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_prev_y_target='".mysqli_real_escape_string($august_prev_target)."'";

				    $sqlselfappraisalupdate .= " , aug_prev_y_achievement='".mysqli_real_escape_string($august_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_prev_y_target='".mysqli_real_escape_string($september_prev_target)."'";

				    $sqlselfappraisalupdate .= " , sep_prev_y_achievement='".mysqli_real_escape_string($september_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_prev_y_target='".mysqli_real_escape_string($october_prev_target)."'";

				    $sqlselfappraisalupdate .= " , oct_prev_y_achievement='".mysqli_real_escape_string($october_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_target='".mysqli_real_escape_string($november_prev_target)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_achievement='".mysqli_real_escape_string($november_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_target='".mysqli_real_escape_string($december_prev_target)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_achievement='".mysqli_real_escape_string($december_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_target='".mysqli_real_escape_string($january_prev_target)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_achievement='".mysqli_real_escape_string($january_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_target='".mysqli_real_escape_string($february_prev_target)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_achievement='".mysqli_real_escape_string($february_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_target='".mysqli_real_escape_string($march_prev_target)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_achievement='".mysqli_real_escape_string($march_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' 

												AND prod_code='".$prod_code."'";

					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Product Wise Target Ach.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Product Wise Target Ach.csv is wrong.";

		exit();

	}*/

    //For Weekly Target ACh CSV

	if(similar_file_exists("../csv/$folderName/Weekly Target ACh.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Weekly Target ACh.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

			  

				$monthyear=trim($data[0]);

				$emp_code=trim($data[1]);

				$emp_name=trim($data[2]);

				$week1_target=trim($data[3]);

				$week1_ach=trim($data[4]);

				$week2_target=trim($data[5]);

				$week2_ach=trim($data[6]);

				$week3_target=trim($data[7]);

				$week3_ach=trim($data[8]);

				$week4_target=trim($data[9]);

				$week4_ach=trim($data[10]);

				$week5_target=trim($data[11]);

				$week5_ach=trim($data[12]);

				$month_target=trim($data[13]);

				$month_ach=trim($data[14]);

				

				$monthyearparts=explode("'",$monthyear);

				$monthyearpartsyear='20'.$monthyearparts[1];

				$intial_date=$monthyearparts[0].'01 '.$monthyearpartsyear;

				$originalmonth=date('m',strtotime($intial_date));

				

				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_code='".addslashes($emp_code)."'";

				$rsempcode=mysqli_query($link,$sqlempcode);

				$rowempcode=mysqli_fetch_assoc($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				$sqlchktargetach="SELECT emp_code FROM self_appraisal_emp_week_wise WHERE emp_code='".$emp_code."' 

								AND month='".$originalmonth."' AND year='".$monthyearpartsyear."'";

				$rschktargetach=mysqli_query($link,$sqlchktargetach);

				$countchktargetach=mysqli_num_rows($rschktargetach);

				//$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchktargetach==0){

					$week1_target=trim($data[3]);

				$week1_ach=trim($data[4]);

				$week2_target=trim($data[5]);

				$week2_ach=trim($data[6]);

				$week3_target=trim($data[7]);

				$week3_ach=trim($data[8]);

				$week4_target=trim($data[9]);

				$week4_ach=trim($data[10]);

				$week5_target=trim($data[11]);

				$week5_ach=trim($data[12]);

				$month_target=trim($data[13]);

				$month_ach=trim($data[14]);

				

				$sqlselfappraisal  = "insert into self_appraisal_emp_week_wise SET ";

				$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";

				$sqlselfappraisal .= " , month='".mysqli_real_escape_string($originalmonth)."'";

				$sqlselfappraisal .= " , year='".mysqli_real_escape_string($monthyearpartsyear)."'";

				$sqlselfappraisal .= " , week1_target='".mysqli_real_escape_string($week1_target)."'";

				$sqlselfappraisal .= " , week1_ach='".mysqli_real_escape_string($week1_ach)."'";

				$sqlselfappraisal .= " , week2_target='".mysqli_real_escape_string($week2_target)."'";

				$sqlselfappraisal .= " , week2_ach='".mysqli_real_escape_string($week2_ach)."'";

				$sqlselfappraisal .= " , week3_target='".mysqli_real_escape_string($week3_target)."'";

				$sqlselfappraisal .= " , week3_ach='".mysqli_real_escape_string($week3_ach)."'";

				$sqlselfappraisal .= " , week4_target='".mysqli_real_escape_string($week4_target)."'";

				$sqlselfappraisal .= " , week4_ach='".mysqli_real_escape_string($august_target)."'";

				$sqlselfappraisal .= " , week5_target='".mysqli_real_escape_string($week5_target)."'";

				$sqlselfappraisal .= " , week5_ach='".mysqli_real_escape_string($week5_ach)."'";

				$sqlselfappraisal .= " , month_target='".mysqli_real_escape_string($month_target)."'";

				$sqlselfappraisal .= " , month_ach='".mysqli_real_escape_string($october_target)."'";

				$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Weekly Target ACh.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_emp_week_wise SET ";

					$sqlselfappraisalupdate .= "  week1_target='".mysqli_real_escape_string($week1_target)."'";

					$sqlselfappraisalupdate .= " , week2_target='".mysqli_real_escape_string($week2_target)."'";

					$sqlselfappraisalupdate .= " , week3_target='".mysqli_real_escape_string($week3_target)."'";

					$sqlselfappraisalupdate .= " , week4_target='".mysqli_real_escape_string($week4_target)."'";

					$sqlselfappraisalupdate .= " , week5_target='".mysqli_real_escape_string($week5_target)."'";

					$sqlselfappraisalupdate .= " , month_target='".mysqli_real_escape_string($month_target)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' 

												AND month='".$originalmonth."' AND year='".$monthyearpartsyear."'";

					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Weekly Target ACh.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star  Target.csv is wrong.";

		exit();

	}*/



	//For Product Group Customer Target ACh CSV

	if(similar_file_exists("../csv/$folderName/Product Group Customer Target ACh.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Product Group Customer Target ACh.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

			  

				$dns_customer_code=trim($data[0]);

				$product_group_name=trim($data[1]);

				$april_target=trim($data[2]);

				$april_achievement=trim($data[3]);

				$may_target=trim($data[4]);

				$may_achievement=trim($data[5]);

				$june_target=trim($data[6]);

				$june_achievement=trim($data[7]);

				$july_target=trim($data[8]);

				$july_achievement=trim($data[9]);

				$august_target=trim($data[10]);

				$august_achievement=trim($data[11]);

				$september_target=trim($data[12]);

				$september_achievement=trim($data[13]);

				$october_target=trim($data[14]);

				$october_achievement=trim($data[15]);

				$november_target=trim($data[16]);

				$november_achievement=trim($data[17]);

				$december_target=trim($data[18]);

				$december_achievement=trim($data[19]);

				$january_target=trim($data[20]);

				$january_achievement=trim($data[21]);

				$february_target=trim($data[22]);

				$february_achievement=trim($data[23]);

				$march_target=trim($data[24]);

				$march_achievement=trim($data[25]);

				

				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";

				$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];

								

				$sqlchkempproductgroup="SELECT product_group_code FROM self_appraisal_cust_productgroup_wise 

									WHERE customer_code='".$customer_code."' AND product_group_code='".$product_group_code."'";

				$rschkempproductgroup=mysqli_query($link,$sqlchkempproductgroup);

				$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);

				//$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempproductgroup==0){

				$sqlselfappraisal  = "insert into self_appraisal_cust_productgroup_wise SET ";

				$sqlselfappraisal .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";

				$sqlselfappraisal .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

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

				mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Product Group Customer Target ACh.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_cust_productgroup_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

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

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."' 

												AND product_group_code='".$product_group_code."'";

					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Product Group Customer Target ACh.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Product Group Customer Target ACh.csv is wrong.";

		exit();

	}*/

	//For Target Achievement CSV

	if(similar_file_exists("../csv/$folderName/Target achievement.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Target achievement.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

			  

				$dns_customer_code=trim($data[0]);

				$vertical=trim($data[1]);

				$product_group_code=trim($data[2]);

				$prod_code=trim($data[3]);

				$april_target=trim($data[4]);

				$april_achievement=trim($data[5]);

				$may_target=trim($data[6]);

				$may_achievement=trim($data[7]);

				$june_target=trim($data[8]);

				$june_achievement=trim($data[9]);

				$july_target=trim($data[10]);

				$july_achievement=trim($data[11]);

				$august_target=trim($data[12]);

				$august_achievement=trim($data[13]);

				$september_target=trim($data[14]);

				$september_achievement=trim($data[15]);

				$october_target=trim($data[16]);

				$october_achievement=trim($data[17]);

				$november_target=trim($data[18]);

				$november_achievement=trim($data[19]);

				$december_target=trim($data[20]);

				$december_achievement=trim($data[21]);

				$january_target=trim($data[22]);

				$january_achievement=trim($data[23]);

				$february_target=trim($data[24]);

				$february_achievement=trim($data[25]);

				$march_target=trim($data[26]);

				$march_achievement=trim($data[27]);

				

				$sqlcustomercode="SELECT customer_code,customer_name FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				$customer_name=$rowcustomercode['customer_name'];

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";

				$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];

				

				$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_summary WHERE customer_code='".$dns_customer_code."' AND vertical='".$vertical."'";

				$rschkcustomercode=mysqli_query($link,$sqlchkcustomercode);

				$countchkcustomercode=mysqli_num_rows($rschkcustomercode);

				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);

				//$customer_code=$rowcustomercode['customer_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkcustomercode ==0)

				{

					$sqlselfappraisal  = "insert into self_appraisal_summary SET ";

					$sqlselfappraisal .= "   customer_code='".mysqli_real_escape_string($dns_customer_code)."'";

					$sqlselfappraisal .= "   ,customer_name='".mysqli_real_escape_string($customer_name)."'";

					$sqlselfappraisal .= "   ,vertical='".mysqli_real_escape_string($vertical)."'";

					$sqlselfappraisal .= "   ,product_group_code=''";

					$sqlselfappraisal .= "   ,prod_code=''";

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

					mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Target achievement.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_summary SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

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

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$dns_customer_code."' AND vertical='".$vertical."'";

			mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Target achievement.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star Target.csv is wrong.";

		exit();

	}*/

		//For Target Achievement CSV

	if(similar_file_exists("../csv/$folderName/Employee Target achievement.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Employee Target achievement.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

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

			  

				$dns_emp_code=trim($data[0]);

				$vertical=trim($data[1]);

				$product_group_code=trim($data[2]);

				$prod_code=trim($data[3]);

				$april_target=trim($data[4]);

				$april_achievement=trim($data[5]);

				$may_target=trim($data[6]);

				$may_achievement=trim($data[7]);

				$june_target=trim($data[8]);

				$june_achievement=trim($data[9]);

				$july_target=trim($data[10]);

				$july_achievement=trim($data[11]);

				$august_target=trim($data[12]);

				$august_achievement=trim($data[13]);

				$september_target=trim($data[14]);

				$september_achievement=trim($data[15]);

				$october_target=trim($data[16]);

				$october_achievement=trim($data[17]);

				$november_target=trim($data[18]);

				$november_achievement=trim($data[19]);

				$december_target=trim($data[20]);

				$december_achievement=trim($data[21]);

				$january_target=trim($data[22]);

				$january_achievement=trim($data[23]);

				$february_target=trim($data[24]);

				$february_achievement=trim($data[25]);

				$march_target=trim($data[26]);

				$march_achievement=trim($data[27]);

				

				$sqlchkempcode="SELECT emp_code FROM FROM self_appraisal_emp_wise WHERE emp_code='".$dns_emp_code."' AND vertical='".$vertical."'";

				$rschkempcode=mysqli_query($link,$sqlchkempcode);

				$countchkempcode=mysqli_num_rows($rschkempcode);

				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);

				//$customer_code=$rowcustomercode['customer_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempcode ==0)

				{

					$sqlselfappraisal  = "insert into self_appraisal_emp_wise SET ";

					$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($dns_emp_code)."'";

					$sqlselfappraisal .= "   ,emp_name=''";

					$sqlselfappraisal .= "   ,vertical='".mysqli_real_escape_string($vertical)."'";

					$sqlselfappraisal .= "   ,product_group_code=''";

					$sqlselfappraisal .= "   ,prod_code=''";

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

					mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Employee Target achievement.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_emp_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

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

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$dns_emp_code."' AND vertical='".$vertical."'";

					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Employee Target achievement.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star Target.csv is wrong.";

		exit();

	}*/

	//For Customer product wise order plan.csv

	if(similar_file_exists("../csv/$folderName/Customer product wise order plan.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Customer product wise order plan.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_customer_wise";

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

			  

				$dns_customer_code=trim($data[0]);

				$dns_prod_code=trim($data[1]);

				$apr_purchase=trim($data[2]);

				$apr_plan=trim($data[3]);

				$may_purchase=trim($data[4]);

				$may_plan=trim($data[5]);

				$jun_purchase=trim($data[6]);

				$jun_plan=trim($data[7]);

				$jul_purchase=trim($data[8]);

				$jul_plan=trim($data[9]);

				$aug_purchase=trim($data[10]);

				$aug_plan=trim($data[11]);

				$sep_purchase=trim($data[12]);

				$sep_plan=trim($data[13]);

				$oct_purchase=trim($data[14]);

				$oct_plan=trim($data[15]);

				$nov_purchase=trim($data[16]);

				$nov_plan=trim($data[17]);

				$dec_purchase=trim($data[18]);

				$dec_plan=trim($data[19]);

				$jan_purchase=trim($data[20]);

				$jan_plan=trim($data[21]);

				$feb_purchase=trim($data[22]);

				$feb_plan=trim($data[23]);

				$mar_purchase=trim($data[24]);

				$mar_plan=trim($data[25]);

				

				

				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				

				$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($dns_prod_code)."'";

				$rsproductcode=mysqli_query($link,$sqlproductcode);

				$rowproductcode=mysqli_fetch_assoc($rsproductcode);

				$prod_code=$rowproductcode['prod_code'];



				$sqlchkcustomercode="SELECT customer_code FROM customer_product_wise_orderplan_details WHERE 

									customer_code='".$customer_code."' AND 	prod_code='".$prod_code."'";

				$rschkcustomercode=mysqli_query($link,$sqlchkcustomercode);

				$countchkcustomercode=mysqli_num_rows($rschkcustomercode);

				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);

				//$customer_code=$rowcustomercode['customer_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkcustomercode ==0)

				{

					$sqlorderplan  = "insert into customer_product_wise_orderplan_details SET ";

					$sqlorderplan .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";

					$sqlorderplan .= " ,  prod_code='".mysqli_real_escape_string($prod_code)."'";

					$sqlorderplan .= " , apr_plan='".mysqli_real_escape_string($apr_plan)."'";

					$sqlorderplan .= " , apr_purchase='".mysqli_real_escape_string($apr_purchase)."'";

					$sqlorderplan .= " , may_plan='".mysqli_real_escape_string($may_plan)."'";

					$sqlorderplan .= " , may_purchase='".mysqli_real_escape_string($may_purchase)."'";

					$sqlorderplan .= " , jun_plan='".mysqli_real_escape_string($jun_plan)."'";

					$sqlorderplan .= " , jun_purchase='".mysqli_real_escape_string($jun_purchase)."'";

					$sqlorderplan .= " , jul_plan='".mysqli_real_escape_string($jul_plan)."'";

					$sqlorderplan .= " , jul_purchase='".mysqli_real_escape_string($jul_purchase)."'";

					$sqlorderplan .= " , aug_plan='".mysqli_real_escape_string($aug_plan)."'";

					$sqlorderplan .= " , aug_purchase='".mysqli_real_escape_string($aug_purchase)."'";

					$sqlorderplan .= " , sep_plan='".mysqli_real_escape_string($sep_plan)."'";

					$sqlorderplan .= " , sep_purchase='".mysqli_real_escape_string($sep_purchase)."'";

					$sqlorderplan .= " , oct_plan='".mysqli_real_escape_string($oct_plan)."'";

					$sqlorderplan .= " , oct_purchase='".mysqli_real_escape_string($oct_purchase)."'";

					$sqlorderplan .= " , nov_plan='".mysqli_real_escape_string($nov_plan)."'";

					$sqlorderplan .= " , nov_purchase='".mysqli_real_escape_string($nov_purchase)."'";

					$sqlorderplan .= " , dec_plan='".mysqli_real_escape_string($dec_plan)."'";

					$sqlorderplan .= " , dec_purchase='".mysqli_real_escape_string($dec_purchase)."'";

					$sqlorderplan .= " , jan_plan='".mysqli_real_escape_string($jan_plan)."'";

					$sqlorderplan .= " , jan_purchase='".mysqli_real_escape_string($jan_purchase)."'";

					$sqlorderplan .= " , feb_plan='".mysqli_real_escape_string($feb_plan)."'";

					$sqlorderplan .= " , feb_purchase='".mysqli_real_escape_string($feb_purchase)."'";

					$sqlorderplan .= " , mar_plan='".mysqli_real_escape_string($mar_plan)."'";

					$sqlorderplan .= " , mar_purchase='".mysqli_real_escape_string($mar_purchase)."'";

					$sqlorderplan .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($link,$sqlorderplan) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Customer product wise order plan.csv .Please check.");

				}

				else

				{

						$prev_month=strtolower(date('M', mktime(0, 0, 0, date('m')-1, 1, date('Y'))));

						$prev_month_column=$prev_month.'_purchase';

						$sqlupdatecondition=" ,$prev_month_column='".${$prev_month.'_purchase'}."'";



					

					$sqlupdateorderplan  = "update customer_product_wise_orderplan_details SET ";

					$sqlupdateorderplan .= "  apr_plan='".mysqli_real_escape_string($apr_plan)."'";

					$sqlupdateorderplan .= " , may_plan='".mysqli_real_escape_string($may_plan)."'";

					$sqlupdateorderplan .= " , jun_plan='".mysqli_real_escape_string($jun_plan)."'";

					$sqlupdateorderplan .= " , jul_plan='".mysqli_real_escape_string($jul_plan)."'";

					$sqlupdateorderplan .= " , aug_plan='".mysqli_real_escape_string($aug_plan)."'";

					$sqlupdateorderplan .= " , sep_plan='".mysqli_real_escape_string($sep_plan)."'";

					$sqlupdateorderplan .= " , oct_plan='".mysqli_real_escape_string($oct_plan)."'";

					$sqlupdateorderplan .= " , nov_plan='".mysqli_real_escape_string($nov_plan)."'";

					$sqlupdateorderplan .= " , dec_plan='".mysqli_real_escape_string($dec_plan)."'";

					$sqlupdateorderplan .= " , jan_plan='".mysqli_real_escape_string($jan_plan)."'";

					$sqlupdateorderplan .= " , feb_plan='".mysqli_real_escape_string($feb_plan)."'";

					$sqlupdateorderplan .= " , mar_plan='".mysqli_real_escape_string($mar_plan)."'";

					$sqlupdateorderplan .= " , download_time=CURRENT_TIMESTAMP() ".$sqlupdatecondition;

					$sqlupdateorderplan .= "  WHERE customer_code='".$customer_code."' AND prod_code='".$prod_code."'";

					mysqli_query($link,$sqlupdateorderplan) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Customer product wise order plan.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Product Group Customer Target ACh.csv is wrong.";

		exit();

	}*/



	//For Distributor route CSV

	if(similar_file_exists("../csv/$folderName/Distributor_emp_route_relation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Distributor_emp_route_relation.csv");

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

			  

				$customer_code_name=trim($data[0]);

				$emp_code_name=trim($data[1]);

				$route_code_name=trim($data[2]);

				//$actual_route=trim($data[4]);

				$state=trim($data[3]);

				$visit_day=trim($data[4]);

				$acedns=trim($data[5]);

				$route_class=trim($data[6]);

				$DA=trim($data[7]);

				if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

				$emp_code_name_array=explode(',',$emp_code_name);

				

				/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				$rsempnamechk=mysqli_query($link,$sqlempnamechk);

				$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

				$emp_code=$rowempnamechk['emp_code'];*/

				if($providing_code=='yes'){

					$sqlroutecode="SELECT route_code FROM route_master WHERE dns_route_code='".addslashes($route_code_name)."'";

				}

				else

				{

					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_code_name)."'";

				}

				$rsroutecode=mysqli_query($link,$sqlroutecode);

				//$actual_route_code=$rowactualroutecode['route_code'];

				$countroutecode=mysqli_num_rows($rsroutecode);

				if($countroutecode<1 )

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

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$route_code_name."'";

						$sqlroute .= " ,route_name='".$route_code_name."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

				else

				{	

					$rowroutecode=mysqli_fetch_assoc($rsroutecode);

					$route_code=$rowroutecode['route_code'];

				}



				if($providing_code=='yes'){

					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_name)."' 

										AND cust_type!='R' AND acedns='Y'";

				}

				else{

					/*if($folderName=='HALDIRAM')

					{

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' 

											AND route_code='".$actual_route_code."'";

					}

					else

					{*/

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' 

										AND cust_type!='R'";

					//}

				}

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				foreach($emp_code_name_array as $emp_code_name_value_next)

					{

						if($providing_code=='yes'){

						 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

						}

						else{

						 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";	

						}

						$rsempnamechk=mysqli_query($link,$sqlempnamechk);

						$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

						$emp_code=$rowempnamechk['emp_code'];

				   $sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."' AND 

											route_code='".$route_code."' AND emp_code='".$emp_code."'";

				   $rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);

				   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

					   if($countchkdistributorroute==0)

					   {						

						   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

													route_code='".$route_code."',emp_code='".$emp_code."',state='".addslashes($state)."',

													visit_day='".addslashes($visit_day)."',acedns='".$acedns."',route_class='".$route_class."',

													DA='".$DA."',download_time=CURRENT_TIMESTAMP()";

						   $rsinsertdistributorroute=mysqli_query($link,$sqlinsertdistributorroute);

					   }

					   else

					   {

						    $sqlupdatedistributorroute="UPDATE distributor_route_relation SET state='".addslashes($state)."',

													visit_day='".addslashes($visit_day)."',acedns='".$acedns."',route_class='".$route_class."',

													DA='".$DA."',download_time=CURRENT_TIMESTAMP() WHERE 

													distributor_code='".$customer_code."' AND route_code='".$route_code."' AND 

													emp_code='".$emp_code."'";

						   $rsupdatedistributorroute=mysqli_query($link,$sqlupdatedistributorroute);

					   }
					  $sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE 
													customer_code='".$customer_code."'"; 
					  $rsupdatecustomerroute=mysqli_query($link,$sqlupdatecustomerroute);								

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

	}*/

	//For Distributor route advanced CSV

	if(similar_file_exists("../csv/$folderName/Distributor_emp_route_relation_advanced.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Distributor_emp_route_relation_advanced.csv");

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

			  

				$customer_code_name=trim($data[0]);

				$emp_code_name=trim($data[1]);

				$route_code_name=trim($data[2]);

				//$actual_route=trim($data[4]);

				$state=trim($data[3]);

				$visit_day=trim($data[4]);

				$combined_route=trim($data[5]);

				$combined_route_array=explode(',',$combined_route);

				$emp_code_name_array=explode(',',$emp_code_name);

				

				/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				$rsempnamechk=mysqli_query($link,$sqlempnamechk);

				$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

				$emp_code=$rowempnamechk['emp_code'];*/

			 foreach($combined_route_array as $combined_route_val)

			  {

				if( $combined_route_val!=''){ 

				if($providing_code=='yes'){

					$sqlroutecode="SELECT route_code FROM route_master WHERE dns_route_code='".addslashes($combined_route_val)."'";

				}

				else

				{

					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".addslashes(ltrim($combined_route_val))."'";

				}

				$rsroutecode=mysqli_query($link,$sqlroutecode);

				//$actual_route_code=$rowactualroutecode['route_code'];

				$countroutecode=mysqli_num_rows($rsroutecode);

				if($countroutecode<1 )

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

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$combined_route_val."'";

						$sqlroute .= " ,route_name='".addslashes(ltrim($combined_route_val))."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

				else

				{	

					$rowroutecode=mysqli_fetch_assoc($rsroutecode);

					$route_code=$rowroutecode['route_code'];

				}



				if($providing_code=='yes'){

					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_name)."'";

				}

				else{

					/*if($folderName=='HALDIRAM')

					{

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' 

											AND route_code='".$actual_route_code."'";

					}

					else

					{*/

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";

					//}

				}

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				foreach($emp_code_name_array as $emp_code_name_value_next)

					{

						if($providing_code=='yes'){

						 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

						}

						else{

						 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";	

						}

						$rsempnamechk=mysqli_query($link,$sqlempnamechk);

						$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

						$emp_code=$rowempnamechk['emp_code'];

					$sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."',

											route_code='".$route_code."',emp_code='".$emp_code."'";

				   $rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);

				   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

					   if($countchkdistributorroute==0)

					   {						

						   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

													route_code='".$route_code."',emp_code='".$emp_code."',state='".addslashes($state)."',

													visit_day='".addslashes($visit_day)."',download_time=CURRENT_TIMESTAMP()";

						   $rsinsertdistributorroute=mysqli_query($link,$sqlinsertdistributorroute);

					   }

					}

			   }

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

	}*/

	//For modified Distributor route CSV for AJANTA

	if(similar_file_exists("../csv/$folderName/modified Distributor_emp_route_relation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/modified Distributor_emp_route_relation.csv");

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

			  

				$customer_code_name=trim($data[0]);

				$emp_code_name_old=trim($data[1]);

				$emp_code_name_new=trim($data[2]);

				if($providing_code=='yes'){

					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_name)."'";

				}

				else{

				   $sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";

				}

				$rscustomercode=mysqli_query($link,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				if($providing_code=='yes'){

					 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_new)."'";

					}

					else{

					 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_new)."'";	

					}

					$rsempnamechk=mysqli_query($link,$sqlempnamechk);

					$rowempnamechk=mysqli_fetch_assoc($rsempnamechk);

					$emp_code_new=$rowempnamechk['emp_code'];

				   $sqlempnamechkold="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_old)."'";	

				   $rsempnamechkold=mysqli_query($link,$sqlempnamechkold);

				   $rowempnamechkold=mysqli_fetch_assoc($rsempnamechkold);

				   $emp_code_old=$rowempnamechkold['emp_code'];

				   

				   $sqlchkcustomerroute="SELECT customer_code FROM customer_route_emp_relation WHERE customer_code='".$customer_code."' AND 

				   						emp_code='".$emp_code_old."'";

				   $rschkcustomerroute=mysqli_query($link,$sqlchkcustomerroute);

				   $countchkcustomerroute=mysqli_num_rows($rschkcustomerroute);

				   if($countchkcustomerroute > 0)

					{	

					   $sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET 

												emp_code='".$emp_code_new."',download_time=CURRENT_TIMESTAMP() 

												WHERE customer_code='".$customer_code."' AND emp_code='".$emp_code_old."'";

					   $rsupdatecustomerroute=mysqli_query($link,$sqlupdatecustomerroute);

					}

				   

				  $sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."' AND 

				   						emp_code='".$emp_code_old."'";

				   $rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);

				   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

				   if($countchkdistributorroute > 0)

					{	

					   $sqlupdatedistributorroute="UPDATE distributor_route_relation SET 

												emp_code='".$emp_code_new."',download_time=CURRENT_TIMESTAMP() 

												WHERE distributor_code='".$customer_code."' AND emp_code='".$emp_code_old."'";

					   $rsinsertdistributorroute=mysqli_query($link,$sqlupdatedistributorroute);

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

	}*/

	//For Depot Destination Freight

	if(similar_file_exists("../csv/$folderName/Depot Destination Freight.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Depot Destination Freight.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		{
			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='branch_destination_freight'";
			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);
			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);
			$need_update=$rowchkupdateinfo['need_update'];
			if($need_update=='yes')
			{
				echo "Previous update process is going on branch destination freight. Please try some time later.";
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

				$destination_code_name=trim($data[1]);

				$freight=trim($data[2]);

				$acedns=trim($data[3]);

				$date=trim($data[4]);
				$dateArr=explode('-',$date);
				if(strlen($dateArr[2])==2)
				{
					$year='20'.$dateArr[2];
				}
				else
				{
					$year=$dateArr[2];
				}
				$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];
				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";

				$rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);

				$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
				$sqlbranchdestinationfreight="SELECT branch_code FROM branch_destination_freight WHERE branch_code='".addslashes($branch_code)."' 

											AND destination_code='".$destination_code."'";

				$rsbranchdestinationfreight=mysqli_query($link,$sqlbranchdestinationfreight);

				$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);
				if($countbranchdestinationfreight<1 )
					{

						$sqlbranchdestinationfreight  = "insert into branch_destination_freight ";

						$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";

						$sqlbranchdestinationfreight .= " ,destination_code='".$destination_code."'";

						$sqlbranchdestinationfreight .= " ,acedns='".$acedns."'";

						$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlbranchdestinationfreight) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on depot destination freight table.PLease contact aceDNS admin.");				

					}
					else
					{

						$sqlbranchdestinationfreightupd  = "update branch_destination_freight ";

						$sqlbranchdestinationfreightupd .= " SET acedns='".$acedns."'";

						$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' 

															AND destination_code='".$destination_code."'";

						mysqli_query($link,$sqlbranchdestinationfreightupd);
					}
			   }
			   if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			   {

				  $branch_destination_ins_fields = array(

							'branch_code_name' => $branch_code_name,

							'destination_code_name' => $destination_code_name,

							'acedns' => $acedns,

						);

				  $branch_destination_in_final_array[]=$branch_destination_ins_fields;		

				  $branch_destination_ins_fields_string = http_build_query($branch_destination_in_final_array); 
				}
			 $rec_count++;
		}
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		  {
			array_push($upload_master_table_array,'branch_destination_freight');
		  }		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Company master.csv is wrong.";
		exit();
	}	*/
	if(similar_file_exists("../csv/$folderName/customer destination.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/customer destination.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		{
			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='customer_destination'";
			$rschkupdateinfo=mysqli_query($link,$sqlchkupdateinfo);
			$rowchkupdateinfo=mysqli_fetch_assoc($rschkupdateinfo);
			$need_update=$rowchkupdateinfo['need_update'];
			if($need_update=='yes')
			{
				echo "Previous update process is going on customer destination. Please try some time later.";
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
				$customer_code_name=trim($data[0]);
				$destination_code_name=trim($data[1]);
				$acedns=trim($data[2]);
				$sqlcustnamechk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_name)."'";
				$rscustnamechk=mysqli_query($link,$sqlcustnamechk);
				$rowcustnamechk=mysqli_fetch_assoc($rscustnamechk);
				$customer_code=$rowcustnamechk['customer_code'];
				
				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";
				$rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);
				$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
				
				$sqlcustomerdestination="SELECT customer_code FROM customer_destination WHERE customer_code='".addslashes($customer_code)."' AND destination_code='".$destination_code."'";
				$rscustomerdestination=mysqli_query($link,$sqlcustomerdestination);
				$countcustomerdestination=mysqli_num_rows($rscustomerdestination);
				if($countcustomerdestination<1 )
					{
						$sqlcustomerdestination  = "insert into customer_destination ";
						$sqlcustomerdestination .= " SET customer_code='".$customer_code."'";
						$sqlcustomerdestination .= " ,destination_code='".$destination_code."'";
						$sqlcustomerdestination .= " ,acedns='".$acedns."'";
						$sqlcustomerdestination .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlcustomerdestination) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on customer destination table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlcustomerdestinationupd = "update customer_destination ";
						$sqlcustomerdestinationupd .= " SET acedns='".$acedns."'";
						$sqlcustomerdestinationupd .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".addslashes($customer_code)."' AND destination_code='".$destination_code."'";
						mysqli_query($link,$sqlcustomerdestinationupd);
					}
			   }
			   if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
			   {
				  $cust_destination_ins_fields = array(
							'customer_code_name' => $customer_code_name,
							'destination_code_name' => $destination_code_name,
							'acedns' => $acedns,
						);
				  $cust_destination_ins_final_array[]=$cust_destination_ins_fields;		
				  $cust_destination_ins_fields_string = http_build_query($cust_destination_ins_final_array); 
				}
			 $rec_count++;
		}
		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		  {
			array_push($upload_master_table_array,'customer_destination');
		  }		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Company master.csv is wrong.";
		exit();
	}	*/
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

	//For BRANCH WISE COMPETITOR

	if(similar_file_exists("../csv/$folderName/BRANCH WISE COMPETITOR.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/BRANCH WISE COMPETITOR.csv");

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

			  

				$brand_name=trim($data[1]);
				$branch_code_name=trim($data[2]);
				$acedns=trim($data[4]);
				$product_type=trim($data[5]);



				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqlbranchdestinationfreight="SELECT branch_code FROM competitor_group_master WHERE branch_code='".addslashes($branch_code)."' 

											AND competitor_name	='".addslashes($brand_name)."' ";

				$rsbranchdestinationfreight=mysqli_query($link,$sqlbranchdestinationfreight);

				$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);

				if($countbranchdestinationfreight<1)

					{

						$sqlbranchdestinationfreight  = "insert into competitor_group_master ";

						$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";

						$sqlbranchdestinationfreight .= " ,competitor_name	='".$brand_name."'";
						$sqlbranchdestinationfreight .= " ,product_type	='".$product_type."'";
						$sqlbranchdestinationfreight .= " ,acedns	='yes'";

						$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlbranchdestinationfreight) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on competitor_group_master table.PLease contact aceDNS admin.");				

					}

					else
					{

						$sqlbranchdestinationfreightupd  = "update competitor_group_master ";

						$sqlbranchdestinationfreightupd .= " SET acedns='".$acedns."'";
						$sqlbranchdestinationfreightupd .= " ,product_type	='".$product_type."'";

						$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' AND competitor_name='".addslashes($brand_name)."'";

						mysqli_query($link,$sqlbranchdestinationfreightupd);

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

	}*/

	//For discount csv

	if(similar_file_exists("../csv/$folderName/Discount.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Discount.csv");

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

			  

				$branch_code_name=trim($data[0]);

				$prod_code_name=trim($data[1]);

				$qty_slab=trim($data[2]);

				$TD_percent=trim($data[3]);

				$cust_class=trim($data[4]);

				$discount_code=trim($data[5]);

				$tax_code=trim($data[6]);

				$acedns=trim($data[7]);

				$SGST=trim($data[8]);

				$CGST=trim($data[9]);

				$IGST=trim($data[10]);

			

				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqlprodnamechk="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($prod_code_name)."'";

				$rsprodnamechk=mysqli_query($link,$sqlprodnamechk);

				$rowprodnamechk=mysqli_fetch_assoc($rsprodnamechk);

				$prod_code=$rowprodnamechk['prod_code'];



				$sqldiscountchk="SELECT TD_percent FROM prodqty_custclass_wise_TD WHERE branch_code='".$branch_code."' 

								AND prod_code='".$prod_code."' AND qty_slab='".$qty_slab."' AND cust_class='".$cust_class."'";

				$rsdiscountchk=mysqli_query($link,$sqldiscountchk);

				$countdiscountchk=mysqli_num_rows($rsdiscountchk);

				$rowdiscountchk=mysqli_fetch_assoc($rsdiscountchk);



				if($countdiscountchk<1 )

					{

						$sqlinsertdiscount  = "insert into prodqty_custclass_wise_TD ";

						$sqlinsertdiscount .= " SET branch_code='".$branch_code."'";

						$sqlinsertdiscount .= " ,	prod_code='".$prod_code."'";

						$sqlinsertdiscount .= " ,	qty_slab='".$qty_slab."'";

						$sqlinsertdiscount .= " ,	TD_percent='".$TD_percent."'";

						$sqlinsertdiscount .= " ,	cust_class='".$cust_class."'";

						$sqlinsertdiscount .= " , acedns='".$acedns."'";

						$sqlinsertdiscount .= " , discount_code='".$discount_code."'";

						$sqlinsertdiscount .= " , tax_code='".$tax_code."'";

						$sqlinsertdiscount .= " , SGST='".$SGST."'";

						$sqlinsertdiscount .= " , CGST='".$CGST."'";

						$sqlinsertdiscount .= " , IGST='".$IGST."'";

						$sqlinsertdiscount .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlinsertdiscount) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on prodqty_custclass_wise_TD table.PLease contact aceDNS admin.");				

					}

					else

					{

						$sqlupdatediscount  = "update prodqty_custclass_wise_TD ";

						$sqlupdatediscount .= " SET TD_percent='".$TD_percent."'";

						$sqlupdatediscount .= ", discount_code='".$TD_percent."'";

						$sqlupdatediscount .= ", tax_code='".$tax_code."'";

						$sqlupdatediscount .= ", SGST='".$SGST."'";

						$sqlupdatediscount .= ", CGST='".$CGST."'";

						$sqlupdatediscount .= ", IGST='".$IGST."'";

						$sqlupdatediscount .= " ,acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$branch_code."' 

						AND prod_code='".$prod_code."' AND qty_slab='".$qty_slab."' AND cust_class='".$cust_class."'";

						

						mysqli_query($link,$sqlupdatediscount);

						

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

	}*/

	//For Branch costcenter CSV

	if(similar_file_exists("../csv/$folderName/branch costcenter.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/branch costcenter.csv");

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

				$cost_center=trim($data[3]);

				if($providing_code=='yes'){

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

				}

				else

				{

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_name."'";

				}

				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);

				$countbranchnamechk=mysqli_num_rows($rsbranchnamechk);

				$rowbranchnamecheck=mysqli_fetch_assoc($rsbranchnamechk);

				$branch_code=$rowbranchnamecheck['branch_code'];

				

				$sqlbranch  = "UPDATE branch_master SET ";

				$sqlbranch .= "  	costcenter='".mysqli_real_escape_string($cost_center)."'";

				$sqlbranch .= "  WHERE branch_code='".$branch_code."'";

				mysqli_query($link,$sqlbranch) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Branch master.csv.Please check.");

			}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Branch costcenter.csv is wrong.";

		exit();

	}*/

	/*if(similar_file_exists("../csv/$folderName/ABDOS Customer Master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/ABDOS Customer Master.csv");

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

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

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

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					

					$mapped_emp_code_string='';

					//For employee code and branch code

					if($acedns =='Y'){

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

					if($providing_code=='yes'){

						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

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

						$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";

						$rsempcode=mysqli_query($link,$sqlempcode);

						$rowempcode=mysqli_fetch_assoc($rsempcode);

						$emp_code=$rowempcode['emp_code'];

						

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";

						$rsbranchcode=mysqli_query($link,$sqlbranchcode);

						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

						$branch_code=$rowbranchcode['branch_code'];

					}

					//For distributor tagged

					if($providing_code=='yes'){

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";

					}

					else

					{

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y'";

					}

					$rsrdscode=mysqli_query($link,$sqlrdscode);

					$rowrdscode=mysqli_fetch_assoc($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					//For route

					if($providing_code=='yes'){

						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";

					}

					else

					{

						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";

					}

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

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";

						$sqlroute .= " ,route_name='".$route_name."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

					else

					{

						$rowroutechk=mysqli_fetch_assoc($rsroutechk);

						$route_code=$rowroutechk['route_code'];

						$route_name_db=$rowroutechk['route_name'];

						if($route_name_db !=$route_name)

						{

							$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',download_time=CURRENT_TIMESTAMP() 

											WHERE route_code='".$route_code."'";

							mysqli_query($link,$sqlupdateroue) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");

						}

					}

					//For customer

					if($providing_code=='yes'){

					  // $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND route_code='".$route_code."'";

					    $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

					}

					else

					{

					 $sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' 

					 		AND route_code='".$route_code."' AND acedns='Y'";

					 //$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

					}

					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);

					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);

					

					$csv_row_count=$rec_count+1;

					if($countcustomernamechk<1)

					{

						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";

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

						$sql .= " , current_balance	='".$current_balance."'";

						$sql .= " , credit_limit='".$credit_limit."'";

						$sql .= " , credit_days='".$credit_days."'";

						$sql .= " , acedns='Y'";

						$sql .= " , black_list='N'";

						$sql .= " , TD='".$TD."'";

						$sql .= " , rds_tag='".$rds_code."'";

						$sql .= " , cust_type='".$customer_type."'";

						$sql .= " , sauda_validity_period='".$sauda_validity_period."'";

						$sql .= " , address='".$address."'";

						$sql .= " , owner_name='".$owner_name."'";

						$sql .= " , owner_phone='".$owner_phone."'";

						$sql .= " , cust_class='".$cust_class."'";

						$sql .= " , weekly_closing_day='".$weekly_closing_day."'";

						$sql .= " , TIN='".$TIN."'";

						$sql .= " , PAN='".$PAN."'";

						$sql .= " , district='".$district."'";

						$sql .= " , landline_no='".$landline_no."'";

						$sql .= " , minimum_stock='".$minimum_stock."'";

						$sql .= " , bank_name='".$bank_name."'";

						$sql .= " , bank_account_number='".$bank_account_number."'";

						$sql .= " , email='".$email."'";

						$sql .= " , visit_day='".addslashes($visit_day)."'";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");

					   //modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$customer_code=$max_customer_code;

					}

					else

					{

						

						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);

						$customer_code_db=$rowcustomernamechk['customer_code'];

						$route_code_db=$rowcustomernamechk['route_code'];

						$current_balance_db=$rowcustomernamechk['current_balance'];

						$phone_no_db=$rowcustomernamechk['phone_no'];

						$credit_limit_db=$rowcustomernamechk['credit_limit'];

						$credit_days_db=$rowcustomernamechk['credit_days'];

						$acedns_db=$rowcustomernamechk['acedns'];

						$black_list_db=$rowcustomernamechk['black_list'];

						$TD_db=$rowcustomernamechk['TD'];

						$customer_type_db=$rowcustomernamechk['cust_type'];

						$rds_tag_db=$rowcustomernamechk['rds_tag'];

						$branch_code_db=$rowcustomernamechk['branch_code'];

						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];

						$customer_name_db=$rowcustomernamechk['customer_name'];

						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];

						$address_db=$rowcustomernamechk['address'];

						$owner_name_db=$rowcustomernamechk['owner_name'];

						$owner_phone_db=$rowcustomernamechk['owner_phone'];

						$cust_class_db=$rowcustomernamechk['cust_class'];

						$weekly_closing_day_db=$rowcustomernamechk['weekly_closing_day'];

						$TIN_db=$rowcustomernamechk['TIN'];

						$PAN_db=$rowcustomernamechk['PAN'];

						$district_db=$rowcustomernamechk['district'];

						$zone_db=$rowcustomernamechk['zone'];

						$landline_no_db=$rowcustomernamechk['landline_no'];

						$minimum_stock_db=$rowcustomernamechk['minimum_stock'];

						$bank_name_db=$rowcustomernamechk['bank_name'];

						$bank_account_number_db=$rowcustomernamechk['bank_account_number'];

						$email_db=$rowcustomernamechk['email'];

						$visit_day_db=$rowcustomernamechk['visit_day'];





						if($providing_code=='yes'){

							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";

						}

						else

						{

							$update_condition=" customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."' AND acedns='Y'";

						}



						if($route_code_db!=$route_code || $current_balance_db!=$current_balance || $TD_db!=$TD || $customer_type_db!=$customer_type 

						|| $rds_tag_db!=$rds_code 

						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 

						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no || $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class || $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district || $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name || $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day)

						{

							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";

							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";

							$sqlupdated .= " , current_balance	='".$current_balance."'";

							$sqlupdated .= " , branch_code='".$branch_code."'";

							$sqlupdated .= " , TD='".$TD."'";

							$sqlupdated .= " , cust_type='".$customer_type."'";

							$sqlupdated .= " , phone_no='".$phone_no."'";

						    $sqlupdated .= " , credit_days='".$credit_days."'";

							$sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";

							$sqlupdated .= " , address='".$address."'";

							$sqlupdated .= " , owner_name='".$owner_name."'";

							$sqlupdated .= " , owner_phone='".$owner_phone."'";

							$sqlupdated .= " , cust_class='".$cust_class."'";

							$sqlupdated .= " , weekly_closing_day='".$weekly_closing_day."'";

							$sqlupdated .= " , TIN='".$TIN."'";

							$sqlupdated .= " , PAN='".$PAN."'";

							$sqlupdated .= " , district='".$district."'";

							$sqlupdated .= " , landline_no='".$landline_no."'";

							$sqlupdated .= " , minimum_stock='".$minimum_stock."'";

							$sqlupdated .= " , bank_name='".$bank_name."'";

							$sqlupdated .= " , bank_account_number='".$bank_account_number."'";

							$sqlupdated .= " , email='".$email."'";

							$sqlupdated .= " , rds_tag='".$rds_code."',visit_day='".addslashes($visit_day)."',download_time=CURRENT_TIMESTAMP() 

											 WHERE  ".$update_condition."";

							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

							//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						}

						if(($credit_limit_db!=$credit_limit))

						{

							$sqlupdatedcredit  = "update customer_master ";

							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";

							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 

											 WHERE ".$update_condition."";

							mysqli_query($link,$sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

						 // modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						}

						$customer_code=$customer_code_db;

					}

					//For customer route relation

					$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 

						           customer_code='".$customer_code."'  AND emp_code='".$emp_code."'";

					//exit();

					$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);

					$countcustomerroute=mysqli_num_rows($rsselcustomerroute);

					if($countcustomerroute==0)

					{

					    $sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',

												 route_code='".$route_code."',

												emp_code='".$emp_code."',

												acedns='".$acedns."',

												download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlinsertcustomerroute);

					}

					else

					{

						$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code='".$route_code."',

												acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 

												customer_code='".$customer_code."' AND emp_code='".$emp_code."'";

						mysqli_query($link,$sqlupdatecustomerroute);						

					}

				 }

				}

			  }

					$rec_count++;

		   }//End of for loop

			$successval=1;

		}*/

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

		if(similar_file_exists("../csv/$folderName/ABDOS Customer Master New.csv")!=false)

		{

		$filename=similar_file_exists("../csv/$folderName/ABDOS Customer Master New.csv");

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

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

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

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					

					$mapped_emp_code_string='';

					//For employee code and branch code

					if($acedns =='Y'){

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

					//For distributor tagged

					if($providing_code=='yes'){

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";

					}

					else

					{

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y'";

					}

					$rsrdscode=mysqli_query($link,$sqlrdscode);

					$rowrdscode=mysqli_fetch_assoc($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					//For route

					if($providing_code=='yes'){

						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";

					}

					else

					{

						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";

					}

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

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";

						$sqlroute .= " ,route_name='".$route_name."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

					else

					{

						$rowroutechk=mysqli_fetch_assoc($rsroutechk);

						$route_code=$rowroutechk['route_code'];

					}

							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							echo $sqlupdated .= " , rds_tag='".$rds_code."',download_time=CURRENT_TIMESTAMP() 

											 WHERE  customer_code='".$dns_customer_code."'";

							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

							//modifyempdatadownloadlog($emp_code,strtoupper($folderName));

							//exit();

				 	}

				 }

			  }

					$rec_count++;

		   }//End of for loop

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

		if(similar_file_exists("../csv/$folderName/HALDIRAM Customer Master.csv")!=false)

		{

		$filename=similar_file_exists("../csv/$folderName/HALDIRAM Customer Master.csv");

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

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

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

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					

					$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y' 

									AND cust_type='D'";

					$rsrdscode=mysqli_query($link,$sqlrdscode);

					$rowrdscode=mysqli_fetch_assoc($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					$sqlupdated  = "update customer_master ";

					$sqlupdated .= " SET rds_tag='".$rds_code."'";

					$sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() WHERE  customer_code='".$dns_customer_code."'";

					mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

			  }

					$rec_count++;

		   }//End of for loop

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

	//For scheme master csv

	if(similar_file_exists("../csv/$folderName/scheme master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/scheme master.csv");

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

			  

				$scheme_id=trim($data[0]);

				$start_date=trim($data[1]);

				if(strpos($start_date,'/')!=false){

				 $dateArr=explode('/',$start_date);

				}

				if(strpos($start_date,'-')!=false){

				 $dateArr=explode('-',$start_date);

				}

				if(strlen($dateArr[2])==2)

				{

					$year='20'.$dateArr[2];

				}

				else

				{

					$year=$dateArr[2];

				}

				$finalstartdate=$year.'-'.$dateArr[1].'-'.$dateArr[0];



				$end_date=trim($data[2]);

				if(strpos($end_date,'/')!=false){

				 $dateArr=explode('/',$end_date);

				}

				if(strpos($end_date,'-')!=false){

				 $dateArr=explode('-',$end_date);

				}

				if(strlen($dateArr[2])==2)

				{

					$year='20'.$dateArr[2];

				}

				else

				{

					$year=$dateArr[2];

				}

				$finalenddate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

				$scheme_prod_code_name=trim($data[3]);

				$scheme_qty=trim($data[4]);

				$scheme_amount=trim($data[5]);

				$scheme_type=trim($data[6]);

				$freebies_prod_code_name=trim($data[7]);

				$freebies_qty=trim($data[8]);

				$freebies_val_percent=trim($data[9]);

				$freebies_val_amount=trim($data[10]);

				$scheme_filter=trim($data[11]);

				$scheme_UOM =trim($data[12]);

				$freebies_UOM=trim($data[13]);

				

				 if($providing_code=='yes'){

						$sqlschemeprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$scheme_prod_code_name."'";

						$sqlfreebiesprodcode="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$freebies_prod_code_name."'";

					}

					else

					{

						$sqlschemeprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($scheme_prod_code_name)."'";

						$sqlfreebiesprodcode="SELECT prod_code,prod_desc FROM product_master WHERE prod_desc='".addslashes($freebies_prod_code_name)."'";

					}

				 $rsschemeprodcode=mysqli_query($link,$sqlschemeprodcode);

				 $countschemeprodcode=mysqli_num_rows($rsschemeprodcode);

				 if($countschemeprodcode >0)

				 {

					 $rowschemeprodcode=mysqli_fetch_assoc($rsschemeprodcode);

					 $scheme_prod_code=$rowschemeprodcode['prod_code'];

				 }

				 else

				 {

					 $scheme_prod_code=$scheme_prod_code_name;

				 }

				 $rsfreebiesprodcode=mysqli_query($link,$sqlfreebiesprodcode);

				 $countfreebiesprodcode=mysqli_num_rows($rsfreebiesprodcode);

				 if($countfreebiesprodcode > 0)

				 {

					$rowfreebiesprodcode=mysqli_fetch_assoc($rsfreebiesprodcode);

				 	$freebies_prod_code=$rowfreebiesprodcode['prod_code'];

					$freebies_prod_desc=$rowfreebiesprodcode['prod_desc'];

				 }

				 else{

					 $freebies_prod_code='';

					 $freebies_prod_desc=$freebies_prod_code_name;

				 }

				/*$sqlschemechk="SELECT freebies_prod_code,freebies_prod_desc FROM freebies_master WHERE scheme_id=(SELECT scheme_id FROM scheme_master WHERE prod_code='".$scheme_prod_code."' AND start_date='".$finalstartdate."' AND end_date='".$finalenddate."')";*/

				$sqlschemechk="SELECT scheme_id FROM scheme_master WHERE prod_code='".$scheme_prod_code_name."' AND start_date='".$finalstartdate."' AND end_date='".$finalenddate."'";

				$rsschemechk=mysqli_query($link,$sqlschemechk);

				$countschemechk=mysqli_num_rows($rsschemechk);

				/*if($countschemechk >0){

					echo "Scheme on this product is ongoing please choose another product";

					die;

				}

				$freebies_prod_code_array=array();

				$freebies_prod_desc_array=array();

				if($countschemechk >0){

					while($rowschemechk=mysqli_fetch_assoc($rsschemechk))

					{

						array_push($freebies_prod_code_array,$rowschemechk['freebies_prod_code']);

						array_push($freebies_prod_desc_array,$rowschemechk['freebies_prod_desc']);

					}

				}*/

				if($countschemechk ==0){

				$sqlmaxscheme="SELECT MAX(scheme_id) AS max_scheme_id FROM  scheme_master WHERE 1";

				$rsmaxscheme=mysqli_query($link,$sqlmaxscheme);

				$rowmaxscheme=mysqli_fetch_assoc($rsmaxscheme);

				$max_scheme_id=$rowmaxscheme['max_scheme_id'];

				

				if($max_scheme_id=='')

				{

					$max_scheme_id='S00001';

				}

				else

				{

					$max_scheme_id++;

				}



				$sqlinsertscheme  = "insert into scheme_master ";

				$sqlinsertscheme .= " SET scheme_id='".$max_scheme_id."'";

				$sqlinsertscheme .= " ,	dns_scheme_id='".$scheme_id."'";

				$sqlinsertscheme .= " ,	start_date='".$finalstartdate."'";

				$sqlinsertscheme .= " ,	end_date='".$finalenddate."'";

				$sqlinsertscheme .= " ,	prod_code='".$scheme_prod_code_name."'";

				$sqlinsertscheme .= " , qty='>=".$scheme_qty."'";

				$sqlinsertscheme .= " , amount='".$scheme_amount."'";

				$sqlinsertscheme .= " , scheme_type='single'";

				$sqlinsertscheme .= " , scheme_filter='".$scheme_filter."'";

				$sqlinsertscheme .= " , UOM='".$scheme_UOM."'";

				$sqlinsertscheme .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($link,$sqlinsertscheme) or  array_push($error_array,"mysqli_error().

								Internal DATA execution problem on scheme master table.PLease contact aceDNS admin.");

				$scheme_id=$max_scheme_id;				

				

				}

				else

				{

					$rowschemechk=mysqli_fetch_assoc($rsschemechk);

					$scheme_id=$rowschemechk['scheme_id'];

				}

				$sqlinsertfreebies  = "insert into freebies_master ";

				$sqlinsertfreebies .= " SET scheme_id='".$scheme_id."'";

				$sqlinsertfreebies .= " ,	freebies_prod_code='".$freebies_prod_code_name."'";

				$sqlinsertfreebies .= " ,	freebies_prod_desc='".$freebies_prod_desc."'";

				$sqlinsertfreebies .= " ,	qty='".$freebies_qty."'";

				$sqlinsertfreebies .= " ,	value_percent='".$freebies_val_percent."'";

				$sqlinsertfreebies .= " , value_amount='".$freebies_val_amount."'";

				$sqlinsertfreebies .= " , UOM='".$freebies_UOM."'";

				mysqli_query($link,$sqlinsertfreebies) or  array_push($error_array,"mysqli_error().

								Internal DATA execution problem on freebies master table.PLease contact aceDNS admin.");

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for scheme master.csv is wrong.";

		exit();

	}*/

	//For customer sku msl csv

	if(similar_file_exists("../csv/$folderName/customer sku msl.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/customer sku msl.csv");

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

			  

				$customer_code_name=trim($data[0]);

				$prod_code_name=trim($data[1]);

				$msl=trim($data[2]);

				

				 if($providing_code=='yes'){

						$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

					}

					else

					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".trim(addslashes($prod_code_name))."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".trim(addslashes($customer_code_name))."'";

					}

				 $rsprodcode=mysqli_query($link,$sqlprodcode);

				 $rowprodcode=mysqli_fetch_assoc($rsprodcode);

				 $prod_code=$rowprodcode['prod_code'];

				 $rscustomercode=mysqli_query($link,$sqlcustomercode);

				 $rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				 $customer_code=$rowcustomercode['customer_code'];

				 

				 $sqlselectcustomerprodmsl="SELECT msl FROM customer_product_wise_msl WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

				 $rsselectcustomerprodmsl=mysqli_query($link,$sqlselectcustomerprodmsl);

				 $countselectcustomerprodmsl=mysqli_num_rows($rsselectcustomerprodmsl);	

				 

				 if($countselectcustomerprodmsl >0)

				 {

					 $sqlupdate="UPDATE customer_product_wise_msl SET acedns='N' WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

					 $rsupdate=mysqli_query($link,$sqlupdate);					

				 }



				$sqlinsertmsl  = "insert into customer_product_wise_msl ";

				$sqlinsertmsl .= " SET customer_code='".$customer_code."'";

				$sqlinsertmsl .= " ,	prod_code='".$prod_code."'";

				$sqlinsertmsl .= " ,	msl='".$msl."'";

				$sqlinsertmsl .= " ,	acedns='Y'";

				$sqlinsertmsl .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($link,$sqlinsertmsl) or  array_push($error_array,"mysqli_error().

								Internal DATA execution problem on customer_product_wise_msl table.PLease contact aceDNS admin.");

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for customer sku msl.csv is wrong.";

		exit();

	}*/



	/*if(similar_file_exists("../csv/$folderName/stock allocation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/stock allocation.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$prodarray="";

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

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

				//echo $value;

			   $data[]=$value;

				//echo count($data);

			   if($rec_count==0)

				{

					foreach($data as $key=>$value)

					{

						//if($key >1){

							$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".rtrim(addslashes($value))."'";

							$rsprodcode=mysqli_query($link,$sqlprodcode);

							$countprodcode=mysqli_num_rows($rsprodcode);

							if($countprodcode >0){

								$rowprodcode=mysqli_fetch_assoc($rsprodcode);

								$prod_code=$rowprodcode['prod_code'];

								$prodarray[]=$prod_code;

							}

						//}

					}

					//print_r($prodarray);

				}

				else

				{

				  //print_r($data);

				  //print_r($prodarray);

				  $customer_name=trim($data[0]);

				  $from_date=trim($data[1]);

				  $to_date=trim($data[2]);

				  $from_date=date('Y-m-d',strtotime(str_replace('/','-',$from_date)));

				  $to_date=date('Y-m-d',strtotime(str_replace('/','-',$to_date)));

				  $sqlselcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

				  $rsselcustomercode=mysqli_query($link,$sqlselcustomercode);

				  $rowcustomercode=mysqli_fetch_assoc($rsselcustomercode);

				  $customer_code=$rowcustomercode['customer_code'];

				  $allocation_id='CA'.str_replace('/','#',$customer_code).$contentsdatetime;

				  $sqldelete="DELETE FROM customer_product_allocation WHERE customer_code='".$customer_code."'";

				  mysqli_query($link,$sqldelete);

				  for($k=0;$k <count($prodarray);$k++)

				  {

					  $sqlinsert="INSERT INTO customer_product_allocation SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',

					  			from_date='".$from_date."',to_date='".$to_date."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

					  			prod_code='".$prodarray[$k]."',qty='".$data[$k+3]."'";

					  mysqli_query($link,$sqlinsert);

					  

					  $sqlinsertlog="INSERT INTO customer_product_allocation_log SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',

					  			from_date='".$from_date."',to_date='".$to_date."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

					  			prod_code='".$prodarray[$k]."',qty='".$data[$k+3]."'";

					  mysqli_query($link,$sqlinsertlog);				

				  }

				}

			 $rec_count++;

		}		

		$successval=1;

	}*/

	/*else

	{

		echo $successval="Naming convention for stock allocation.csv is wrong.";

		exit();

	}*/

	if(similar_file_exists("../csv/$folderName/billing information.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/billing information.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$prodarray="";

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

		

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

				//echo $value;

			     $data[]=$value;

				  //print_r($data);

				  //print_r($prodarray);

				  $customer_name=trim($data[0]);

				  $invoice_no=trim($data[1]);

				  $invoice_date=trim($data[2]);

				  $invoice_date_final=date('Y-m-d',strtotime(str_replace('/','-',$invoice_date)));

				  $prod_desc=trim($data[3]);

				  $IMEI=trim($data[4]);

				  

				   $sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".rtrim(addslashes($prod_desc))."'";

				   $rsprodcode=mysqli_query($link,$sqlprodcode);

				   $countprodcode=mysqli_num_rows($rsprodcode);

				   $rowprodcode=mysqli_fetch_assoc($rsprodcode);

				   $prod_code=$rowprodcode['prod_code'];



				  $sqlselcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

				  $rsselcustomercode=mysqli_query($link,$sqlselcustomercode);

				  $rowcustomercode=mysqli_fetch_assoc($rsselcustomercode);

				  $customer_code=$rowcustomercode['customer_code'];

				  //$allocation_id='CA'.str_replace('/','#',$customer_code).$contentsdatetime;

				 // $sqldelete="DELETE FROM customer_product_allocation WHERE customer_code='".$customer_code."'";

				 // mysqli_query($link,$sqldelete);

				  $sqlinsert="INSERT INTO customer_product_billing SET customer_code='".$customer_code."',

							invoice_no='".$invoice_no."',invoice_date='".$invoice_date_final."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

							prod_code='".$prod_code."',IMEI='".$IMEI."'";

				  mysqli_query($link,$sqlinsert);

					  

					  /*$sqlinsertlog="INSERT INTO customer_product_allocation_log SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',

					  			from_date='".$from_date."',to_date='".$to_date."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

					  			prod_code='".$prodarray[$k]."',qty='".$data[$k+3]."'";

					  mysqli_query($link,$sqlinsertlog);*/				

				}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for billing information.csv is wrong.";

		exit();

	}*/

	if(similar_file_exists("../csv/$folderName/activation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/activation.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$prodarray="";

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

		

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

				//echo $value;

			     $data[]=$value;

				  //print_r($data);

				  //print_r($prodarray);

				  $IMEI_one="'".trim($data[0])."'";

				  $IMEI_two="'".trim($data[1])."'";

				  

				  $sqlIMEI="SELECT IMEI FROM customer_product_billing WHERE IMEI IN($IMEI_one,$IMEI_two)";

				  $rsIMEI=mysqli_query($link,$sqlIMEI);

				  $countIMEI=mysqli_num_rows($rsIMEI);

				  if($countIMEI >0)

				  {

					  $rowIMEI=mysqli_fetch_assoc($rsIMEI);

					  $active_IMEI=$rowIMEI['IMEI'];

					  $sqlupdate="UPDATE  customer_product_billing SET active_IMEI='".$active_IMEI."',

							,active_IMEI_upload_time=CURRENT_TIMESTAMP() WHERE IMEI='".$active_IMEI."'";

				  	mysqli_query($link,$sqlupdate);

				  }

				}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for activation.csv is wrong.";

		exit();

	}*/

	//For purchase details csv

	if(similar_file_exists("../csv/$folderName/purchase details.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/purchase details.csv");

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

			  

				$customer_code_name=trim($data[0]);

				$invoice_no=trim($data[1]);

				$invoice_date=trim($data[2]);

				$invoice_date_final=date('Y-m-d',strtotime(str_replace('/','-',$invoice_date)));

				$prod_code_name=trim($data[3]);

				$qty=trim($data[4]);

				$rate=trim($data[5]);

				$amount=trim($data[6]);

				

				 if($providing_code=='yes'){

						$sqlprodcode="SELECT prod_code,vertical_value,product_group_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

					}

					else

					{

						$sqlprodcode="SELECT prod_code,vertical_value,product_group_code FROM product_master WHERE prod_desc='".trim(addslashes($prod_code_name))."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".trim(addslashes($customer_code_name))."'";

					}

				 $rsprodcode=mysqli_query($link,$sqlprodcode);

				 $rowprodcode=mysqli_fetch_assoc($rsprodcode);

				 $prod_code=$rowprodcode['prod_code'];

				 $vertical_value=$rowprodcode['vertical_value'];

				 $rscustomercode=mysqli_query($link,$sqlcustomercode);

				 $rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				 $customer_code=$rowcustomercode['customer_code'];

				 $product_group_code=$rowprodcode['product_group_code'];

				 

				 $sqlselectcustomerprod="SELECT qty FROM purchase_details WHERE distributor_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."' and invoice_date='".$invoice_date_final."' and invoice_no='".$invoice_no."'";

				 $rsselectcustomerprod=mysqli_query($link,$sqlselectcustomerprod);

				 $countselectcustomerprod=mysqli_num_rows($rsselectcustomerprod);	

				 

				 /*if($countselectcustomerprod >0)

				 {

					 $sqlupdate="UPDATE customer_product_wise_msl SET acedns='N' WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

					 $rsupdate=mysqli_query($link,$sqlupdate);					

				 }*/

				if($countselectcustomerprod==0){

					$sqlinsertpurchasedetails = "insert into purchase_details ";

					$sqlinsertpurchasedetails .= " SET distributor_code='".$customer_code."'";

					$sqlinsertpurchasedetails .= " ,	prod_code='".$prod_code."'";

					$sqlinsertpurchasedetails .= " ,	invoice_no='".$invoice_no."'";

					$sqlinsertpurchasedetails .= " ,	invoice_date='".$invoice_date_final."'";

					$sqlinsertpurchasedetails .= " ,	qty='".$qty."'";

					$sqlinsertpurchasedetails .= " ,	rate='".$rate."'";

					$sqlinsertpurchasedetails .= " ,	amount='".$amount."'";

					$sqlinsertpurchasedetails .= " ,	vertical_value='".$vertical_value."'";

					$sqlinsertpurchasedetails .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($link,$sqlinsertpurchasedetails) or  array_push($error_array,"mysqli_error().

									Internal DATA execution problem on purchase details table.PLease contact aceDNS admin.");

				}

				else

				{

					$sqlupdatepurchasedetails = "update purchase_details ";

					$sqlupdatepurchasedetails .= "  SET ";

					$sqlupdatepurchasedetails .= "  qty='".$qty."'";

					$sqlupdatepurchasedetails .= " ,	rate='".$rate."'";

					$sqlupdatepurchasedetails .= " ,	amount='".$amount."'";

					$sqlupdatepurchasedetails .= " ,	vertical_value='".$vertical_value."'";

					$sqlupdatepurchasedetails .= " , download_time=CURRENT_TIMESTAMP() where distributor_code='".$customer_code."' and 	

													prod_code='".$prod_code."' and invoice_date='".$invoice_date_final."' and invoice_no='".$invoice_no."'";

					mysqli_query($link,$sqlupdatepurchasedetails) or  array_push($error_array,"mysqli_error().

									Internal DATA execution problem on purchase details table.PLease contact aceDNS admin.");

				

				

				

				}

				//FOR ARCHITA closing stock update

				$sqlchkcustomerstk="SELECT last_stk_audit_qty,last_stk_audit_date FROM customer_productwise_stock 

									WHERE customer_code='".$customer_code."' AND 	prod_code='".$prod_code."'";

				$rschkcustomerstk=mysqli_query($link,$sqlchkcustomerstk);

				$countchkcustomerstk=mysqli_num_rows($rschkcustomerstk);

				$rowchkcustomerstk=mysqli_fetch_assoc($rschkcustomerstk);

				if($countchkcustomerstk>0)

				{

					$last_stk_audit_date=substr($rowchkcustomerstk['last_stk_audit_date'],1,10);

					 if(strtotime($invoice_date_final) > strtotime($last_stk_audit_date))

					 {

						$sqlupdateclstk="UPDATE customer_productwise_stock SET 

										purchase_after_audit =(purchase_after_audit+$qty),

										purchase_date='".$invoice_date_final."',

										 cl_stk=((last_stk_audit_qty+purchase_after_audit)-sale_after_audit),

										download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."' 

										AND prod_code='".$prod_code."'";

						mysqli_query($link,$sqlupdateclstk);

					 }

				}

				else

				{

					$sqlinsertclstk="INSERT INTO customer_productwise_stock SET

									 customer_code='".$customer_code."',

									 prod_code='".$prod_code."',

									 product_group_code='".$product_group_code."',

									 last_stk_audit_qty =0,

									 last_stk_audit_date='0000-00-00 00:00:00', 

									 purchase_after_audit ='".$qty."',

									 purchase_date='".$invoice_date_final."',

									 sale_after_audit ='0',

									 sale_date='0000-00-00 00:00:00',

									 cl_stk=((last_stk_audit_qty+purchase_after_audit)-sale_after_audit),

									  download_time=CURRENT_TIMESTAMP() ";

					mysqli_query($link,$sqlinsertclstk);

				}

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for customer sku msl.csv is wrong.";

		exit();

	}*/

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

				

				if($providing_code=='yes'){

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

	//For Customer wise lat long CSV

	if(similar_file_exists("../csv/$folderName/customer wise latlong.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/customer wise latlong.csv");

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

			  

				$customer_code=trim($data[0]);

				$customer_name=trim($data[1]);

				$lattitude_base=trim($data[2]);

				$longitude_base=trim($data[3]);

				$sqlchkcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code."'";

				$rschkcustomercode=mysqli_query($link,$sqlchkcustomercode);

				while($rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode))

				{

					$customer_code_db=$rowchkcustomercode['customer_code'];

					$sqlcustomer  = "UPDATE customer_master SET ";

					$sqlcustomer .= "  	base_latt='".mysqli_real_escape_string($lattitude_base)."'";

					$sqlcustomer .= "  ,base_longi='".mysqli_real_escape_string($longitude_base)."'";

					$sqlcustomer .= "  ,download_time=CURRENT_TIMESTAMP()";

					$sqlcustomer .= "  WHERE dns_customer_code='".$customer_code."'";

					mysqli_query($link,$sqlcustomer) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in customerwise latlong.csv.Please check.");

				

					$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code_db."'";

					mysqli_query($link,$sqlupdatecustomerroute);

				}

			}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Branch costcenter.csv is wrong.";

		exit();

	}*/

	//For customer product Mapping csv

	if(similar_file_exists("../csv/$folderName/Customer Product Mapping.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Customer Product Mapping.csv");

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

			  $csv_row_count=$rec_count+1;

				$customer_code_name=trim($data[0]);

				$oil_category=trim($data[1]);

				$oil_category_array=explode(',',$oil_category);

				$premium=trim($data[2]);
				$premium_array=explode(',',$premium);
				$TD=trim($data[3]);
				$TD_array=explode(',',$TD);

				$mcx_rate_parameter=trim($data[4]);

				$acedns=trim($data[5]);

				$acedns='Y';

				

				 if($providing_code=='yes'){

						//$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

					}

					else

					{

						//$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".trim(addslashes($prod_code_name))."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".trim(addslashes($customer_code_name))."'";

					}

				 //$rsprodcode=mysqli_query($link,$sqlprodcode);

				/* $cntprodcode=mysqli_num_rows($rsprodcode);

				 if($cntprodcode==0)

				 {

					 echo "Please provide Proper Product code at row ".($csv_row_count+1);

					 die;

				 }

				 $rowprodcode=mysqli_fetch_assoc($rsprodcode);

				 $prod_code=$rowprodcode['prod_code'];*/

				 $rscustomercode=mysqli_query($link,$sqlcustomercode);

				 $cntcustomercode=mysqli_num_rows($rscustomercode);

				 if($cntcustomercode==0)

				 {

					 echo "Please provide Proper Customer code at row ".($csv_row_count+1);

					 die;

				 }

				 $rowcustomercode=mysqli_fetch_assoc($rscustomercode);

				 $customer_code=$rowcustomercode['customer_code'];

				 

				 /*$sqlselectcustomerprodrel="SELECT acedns FROM customer_product_relation WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

				 $rsselectcustomerprodrel=mysqli_query($link,$sqlselectcustomerprodrel);

				 $countselectcustomerprodrel=mysqli_num_rows($rsselectcustomerprodrel);	

				 

				 if($countselectcustomerprodrel >0)

				 {

					 $sqlupdate="UPDATE customer_product_relation SET acedns='".$acedns."' WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

					 $rsupdate=mysqli_query($link,$sqlupdate);					

				 }*/

				for($i=0;$i < count($oil_category_array);$i++)
				{
				  $sqlselectcustomerprodrel="SELECT acedns FROM customer_product_relation WHERE customer_code='".$customer_code."' 

				  							AND oil_category='".ltrim($oil_category_array[$i])."'";

				  $rsselectcustomerprodrel=mysqli_query($link,$sqlselectcustomerprodrel);

				  $countselectcustomerprodrel=mysqli_num_rows($rsselectcustomerprodrel);	

					 if($countselectcustomerprodrel ==0)
					 {	

						$sqlinsertrelation  = "insert into customer_product_relation ";

						$sqlinsertrelation .= " SET customer_code='".$customer_code."'";

						$sqlinsertrelation .= " ,	prod_code='".$prod_code."'";

						$sqlinsertrelation .= " ,	oil_category='".ltrim($oil_category_array[$i])."'";

						$sqlinsertrelation .= " ,	premium='".$premium_array[$i]."'";

						$sqlinsertrelation .= " ,	TD='".$TD_array[$i]."'";

						$sqlinsertrelation .= " ,	mcx_rate_parameter='".$mcx_rate_parameter."'";

						$sqlinsertrelation .= " ,	acedns='".$acedns."'";

						$sqlinsertrelation .= " , download_time=CURRENT_TIMESTAMP()";

						$sqlinsertrelation .= " , user_id='".$_SESSION['admin_login']."'";

						mysqli_query($link,$sqlinsertrelation) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on customer_product_relation table.PLease contact aceDNS admin.");

					 }

					 else

					 {

						$sqlupdaterelation  = "UPDATE customer_product_relation ";

						$sqlupdaterelation .= " SET premium='".$premium_array[$i]."'";

						$sqlupdaterelation .= " ,	TD='".$TD_array[$i]."'";

						$sqlupdaterelation .= " ,	mcx_rate_parameter='".$mcx_rate_parameter."'";

						$sqlupdaterelation .= " ,	acedns='".$acedns."'";

						$sqlupdaterelation .= " , download_time=CURRENT_TIMESTAMP()";

						$sqlupdaterelation .= " , user_id='".$_SESSION['admin_login']."' WHERE customer_code='".$customer_code."' 

												AND oil_category='".ltrim($oil_category_array[$i])."'";

						mysqli_query($link,$sqlupdaterelation) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on customer_product_relation table.PLease contact aceDNS admin.");

					 }

				 }

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for customer product relation.csv is wrong.";

		exit();

	}*/

	//For Town Master CSV

	if(similar_file_exists("../csv/$folderName/Town Master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Town Master.csv");

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

			  

				$dns_town_code=trim($data[0]);

				$town_name=trim($data[1]);

				$town_type=trim($data[2]);

				$district=trim($data[3]);

				$state=trim($data[4]);

				$distance_from_plant=trim($data[5]);

				if($providing_code=='yes'){

					$sqltownnamechk="SELECT town_code FROM town_master WHERE dns_town_code='".$dns_town_code."'";

				}

				else

				{

					$sqltownnamechk="SELECT town_code FROM town_master WHERE town_name='".$branch_name."'";

				}

				/*$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."' 

									AND branch_location='".$branch_location."'";*/

				$rstownnamechk=mysqli_query($link,$sqltownnamechk);

				$counttownnamechk=mysqli_num_rows($rstownnamechk);

				

				$csv_row_count=$rec_count+1;

				if($counttownnamechk<1)

				{

					$sqlmaxtowncode="SELECT MAX(town_code) AS max_town_code FROM  town_master WHERE 1";

					$rsmaxtowncode=mysqli_query($link,$sqlmaxtowncode);

					$rowmaxtowncode=mysqli_fetch_assoc($rsmaxtowncode);

					$max_town_code=$rowmaxtowncode['max_town_code'];

					

					if($max_town_code=='')

					{

						$max_town_code='T0001';

					}

					else

					{

						$max_town_code++;

					}

				

					$sqltown  = "insert into town_master SET ";

					$sqltown .= "  	town_code='".mysqli_real_escape_string($max_town_code)."'";

					$sqltown .= " , dns_town_code='".mysqli_real_escape_string($dns_town_code)."'";

					$sqltown .= " , town_name='".mysqli_real_escape_string($town_name)."'";

					$sqltown .= " , town_type='".mysqli_real_escape_string($town_type)."'";

					$sqltown .= " , district='".mysqli_real_escape_string($district)."'";

					$sqltown .= " , state='".mysqli_real_escape_string($state)."'";

					$sqltown .= " , distance_from_plant='".mysqli_real_escape_string($distance_from_plant)."'";

					$sqltown .= " , download_time=CURRENT_TIMESTAMP()";

				}

				else

				{

					$rowtownnamechk=mysqli_fetch_assoc($rstownnamechk);

					$town_code=$rowtownnamechk['town_code'];



					$sqltown  = "UPDATE town_master SET ";

					$sqltown .= "  	dns_town_code='".mysqli_real_escape_string($dns_town_code)."'";

					$sqltown .= " , town_name='".mysqli_real_escape_string($town_name)."'";

					$sqltown .= " , town_type='".mysqli_real_escape_string($town_type)."'";

					$sqltown .= " , district='".mysqli_real_escape_string($district)."'";

					$sqltown .= " , state='".mysqli_real_escape_string($state)."'";

					$sqltown .= " , distance_from_plant='".mysqli_real_escape_string($distance_from_plant)."'";

					$sqltown .= " , download_time=CURRENT_TIMESTAMP() WHERE town_code='".$town_code."'";

				}

				mysqli_query($link,$sqltown) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Town master.csv.Please check.");

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

	  //For Broker Master CSV

		if(similar_file_exists("../csv/$folderName/Broker master.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Broker master.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			$updatecount=0;

			if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

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

			}

			

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

						mysqli_query($link,$sqlbroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");
						if(strtoupper($folderName)=='ASL'){
						$sqlchkchangepassword="SELECT emp_code FROM changepassword WHERE emp_code='".$max_broker_id."'";
						$rschkchangepassword=mysqli_query($link,$sqlchkchangepassword);
						$countchkchangepassword=mysqli_num_rows($rschkchangepassword);
						if($countchkchangepassword==0)
						{
							$sqlcp  = "insert into changepassword ";
							$sqlcp .= " SET emp_code='".$max_broker_id."'";
							$sqlcp .= " , newpassword='1234'";
							$sqlcp .= " , oldpassword='1234'"; 
							$sqlcp .= " , status='true'";
							$sqlcp .= " , is_licensed='1'"; 
							mysqli_query($link,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");

						}
						}
					}

					else

					{

						$broker_id_db=$rowbrokerchk['broker_id'];

						$sqlupdatebroker  = "UPDATE broker_master SET ";

						$sqlupdatebroker .= "  broker_name='".mysqli_real_escape_string($broker_name)."'";

						$sqlupdatebroker .= " , contact_person='".mysqli_real_escape_string($contact_person)."'";

						$sqlupdatebroker .= " , mail_id='".mysqli_real_escape_string($mail_id)."'";

						$sqlupdatebroker .= " , phone_no='".mysqli_real_escape_string($phone_no)."'";

						$sqlupdatebroker .= " , dns_broker_id='".mysqli_real_escape_string($dns_broker_id)."'";

						$sqlupdatebroker .= " , acedns='".mysqli_real_escape_string($acedns)."',download_time=CURRENT_TIMESTAMP()";

						$sqlupdatebroker .= " WHERE broker_id='".mysqli_real_escape_string($broker_id_db)."'";

						mysqli_query($link,$sqlupdatebroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");

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
	if(similar_file_exists("../csv/$folderName/Batch wise Cl Stock.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Batch wise Cl Stock.csv");
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
				$dns_prod_code=trim($data[0]);
				$batch_no=trim($data[1]);
				$batch_date=date('Y-m-d',strtotime(trim($data[2])));
				$in_qty=trim($data[3]);
				$out_qty=trim($data[4]);
				$cl_stk=trim($data[5]);

				$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."'";
				$rsprodcode=mysqli_query($link,$sqlprodcode);
				$rowprodcode=mysqli_fetch_assoc($rsprodcode);
				$prodcode=$rowprodcode['prod_code'];
				$sqlprodchk="SELECT prod_code,dns_prod_code,batch_no FROM batch_wise_stock WHERE dns_prod_code='".$dns_prod_code."' AND batch_no='".$batch_no."'";
				$rsprodchk=mysqli_query($link,$sqlprodchk);
				$countprodchk=mysqli_num_rows($rsprodchk);
				$csv_row_count=$rec_count+1;
				if($countprodchk<1)
				{
					$sqlstk  = "insert into batch_wise_stock SET ";
					$sqlstk .= "  	prod_code='".$prodcode."'";
					$sqlstk .= " , dns_prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
					$sqlstk .= " , batch_no='".mysqli_real_escape_string($batch_no)."'";
					$sqlstk .= " , batch_date='".mysqli_real_escape_string($batch_date)."'";
					$sqlstk .= " , in_qty='".mysqli_real_escape_string($in_qty)."'";
					$sqlstk .= " , out_qty='".mysqli_real_escape_string($out_qty)."'";
					$sqlstk .= " , cl_stock='".mysqli_real_escape_string($cl_stk)."'";
					$sqlstk .= " , download_time=CURRENT_TIMESTAMP()";
				}
				else
				{
					$rowprodchk=mysqli_fetch_assoc($rsprodchk);
					$dns_prod_code=$rowprodchk['dns_prod_code'];
					$batch_no=$rowprodchk['batch_no'];
					$sqlstk  = "UPDATE batch_wise_stock SET ";
					$sqlstk .= "  	batch_date='".mysqli_real_escape_string($batch_date)."'";
					$sqlstk .= " , in_qty='".mysqli_real_escape_string($in_qty)."'";
					$sqlstk .= " , out_qty='".mysqli_real_escape_string($out_qty)."'";
					$sqlstk .= " , cl_stock='".mysqli_real_escape_string($cl_stock)."'";
					$sqlstk .= " , download_time=CURRENT_TIMESTAMP() WHERE dns_prod_code='".mysqli_real_escape_string($dns_prod_code)."' AND batch_no='".mysqli_real_escape_string($batch_no)."'";
				}
				mysqli_query($link,$sqlstk) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Batch wise Cl Stock.csv.Please check.");
			}
			 $rec_count++;
		}
		$successval=1;
	}

	//For customer broker mapping CSV

	if(similar_file_exists("../csv/$folderName/Customer broker mapping.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Customer broker mapping.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')

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

			}

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

					mysqli_query($link,$sqlinsertcustomerbroker);

				}

				else

				{

					//For acedns  Y

					$sqlupdatecustomerbroker="UPDATE customer_broker_relation SET acedns='".$acedns."',	
										mapped_broker='".$mapped_broker."',download_time=CURRENT_TIMESTAMP() WHERE 

										customer_code='".$customer_code_db."' AND broker_code='".$broker_id."'";

					$rsupdatecustomerbroker=mysqli_query($link,$sqlupdatecustomerbroker);

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

	//For BOQ master CSV

	if(similar_file_exists("../csv/$folderName/BOQ master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/BOQ master.csv");

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

			  

				$prod_code=trim($data[0]);

				$product_group_code=trim($data[1]);

				$area=trim($data[2]);

				$spacing=trim($data[3]);

				$qty=trim($data[4]);

				$amount=trim($data[5]);

				$mi_type=trim($data[6]);

				

				//$sqlselpoddetails="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code."'";

				//$rsselproddetils=

			

					$sqlmaxBOQ="SELECT MAX(BOQ_id) AS max_BOQ_id FROM  BOQ_master WHERE 1";

					$rsmaxBOQ=mysqli_query($link,$sqlmaxBOQ);

					$rowmaxBOQ=mysqli_fetch_assoc($rsmaxBOQ);

					$max_BOQ_id=$rowmaxBOQ['max_BOQ_id'];

					

					if($max_BOQ_id=='')

					{

						$max_BOQ_id='BO0001';

					}

					else

					{

						$max_BOQ_id++;

					}



					$sqlBOQ  = "insert into BOQ_master SET ";

					$sqlBOQ .= "  BOQ_id='".mysqli_real_escape_string($max_BOQ_id)."'";

					$sqlBOQ .= "  ,prod_code='".mysqli_real_escape_string($prod_code)."'";

					$sqlBOQ .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

					$sqlBOQ .= " , area='".mysqli_real_escape_string($area)."'";

					$sqlBOQ .= " , spacing='".mysqli_real_escape_string($spacing)."'";

					$sqlBOQ .= " , qty='".mysqli_real_escape_string($qty)."'";

					$sqlBOQ .= " , amount='".mysqli_real_escape_string($amount)."'";

					$sqlBOQ .= " , mi_type='".mysqli_real_escape_string($mi_type)."'";

					mysqli_query($link,$sqlBOQ) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in BOQ master.csv.Please check.");

			}

			$rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for BOQ master.csv is wrong.";

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

						mysqli_query($link,$sqlinsertbranchdump) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on branch dump table.PLease contact aceDNS admin.");				

					}

					else

					{

						$sqlbranchdumpupd  = "update branch_dump ";

						$sqlbranchdumpupd .= " SET acedns='".$acedns."'";

						$sqlbranchdumpupd .= " ,is_plant='".$is_plant."'";

						$sqlbranchdumpupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' 

															AND dump_code='".$dump_code_name."'";

						mysqli_query($link,$sqlbranchdumpupd);

						

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
if(similar_file_exists("../csv/$folderName/pop product master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/pop product master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		{
			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='pop_product'";
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

				$dns_prod_code=trim($data[0]);
				$prod_desc=trim($data[1]);
				$prod_image=trim($data[2]);
				$min_order_qty=trim($data[3]);
				$price_per_piece=trim($data[4]);
				$GST_rate=trim($data[5]);
				$status=trim($data[6]);

				
		$sqlpopprod="SELECT dns_prod_code FROM  pop_product_master WHERE dns_prod_code='".addslashes($dns_prod_code)."'";
		$rspopprod=mysqli_query($link,$sqlpopprod);
		$countpopprod=mysqli_num_rows($rspopprod);

				if($countpopprod=='0' )
					{
						$sqlinsertpoprod  = "insert into  pop_product_master ";
						$sqlinsertpoprod .= " SET dns_prod_code='".$dns_prod_code."'";
						$sqlinsertpoprod .= " ,prod_desc='".$prod_desc."'";
						$sqlinsertpoprod .= " ,prod_image='".$prod_image."'";
						$sqlinsertpoprod .= " ,min_order_qty='".$min_order_qty."'";
						$sqlinsertpoprod .= " ,price_per_piece='".$price_per_piece."'";
						$sqlinsertpoprod .= " ,GST_rate='".$GST_rate."'";
						$sqlinsertpoprod .= " ,status='".$status."'";
						$sqlinsertpoprod .= " , upload_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertpoprod) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on pop product table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlupdateprod  = "update pop_product_master ";
						$sqlupdateprod .= " SET prod_desc='".$prod_desc."'";
						$sqlupdateprod .= " ,min_order_qty='".$min_order_qty."'";
						$sqlupdateprod .= " ,status='".$status."'";
						$sqlupdateprod .= " , upload_date_time=CURRENT_TIMESTAMP() WHERE dns_prod_code='".addslashes($dns_prod_code)."'";
						mysqli_query($link,$sqlupdateprod);
					}

					if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
				    {
					  $pop_prod_ins_fields = array(
								'dns_prod_code' => $dns_prod_code,
								'prod_desc' => $prod_desc,
								'prod_image' => $prod_image,
								'min_order_qty' => $min_order_qty,
								'price_per_piece' => $price_per_piece,
						  		'GST_rate' => $GST_rate,
						  		'status' => $status,
							);

					  $pop_product_final_array[]=$pop_prod_ins_fields;		

					  $pop_prod_ins_fields_string = http_build_query($pop_product_final_array); 
					}

			   }

		$rec_count++;
	}
	
	if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
	  {
		array_push($upload_master_table_array,'pop_product');
	  }	

	$successval=1;

  }	

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/
		//For BOQ master CSV
	
	if(similar_file_exists("../csv/$folderName/branch pop product.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/branch pop product.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";

		if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
		{
			$sqlchkupdateinfo="SELECT need_update FROM master_tables_update_info where table_name='branch_pop_product'";
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
				$dns_prod_code=trim($data[2]);
				$prod_desc=trim($data[3]);
				$status=trim($data[4]);
				
		$sqlpopprod="SELECT dns_prod_code FROM  branch_pop_product WHERE 
					dns_prod_code='".addslashes($dns_prod_code)."' AND branch_code='".$dns_branch_code."'";
		$rspopprod=mysqli_query($link,$sqlpopprod);
		$countpopprod=mysqli_num_rows($rspopprod);

				if($countpopprod<1 )
					{
						$sqlinsertbranchpoprod  = "insert into  branch_pop_product  ";
						$sqlinsertbranchpoprod .= " SET dns_prod_code='".$dns_prod_code."'";
						$sqlinsertbranchpoprod .= " ,prod_desc='".$prod_desc."'";
						$sqlinsertbranchpoprod .= " ,branch_code='".$dns_branch_code."'";
						$sqlinsertbranchpoprod .= " ,branch_name='".$branch_name."'";
						$sqlinsertbranchpoprod .= " ,status='".$status."'";
						$sqlinsertbranchpoprod .= " , upload_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertbranchpoprod) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on pop product table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlupdatebrprod  = "update branch_pop_product ";
						$sqlupdatebrprod .= " SET prod_desc='".$prod_desc."'";
						$sqlupdatebrprod .= " ,branch_name='".$branch_name."'";
						$sqlupdatebrprod .= " ,status='".$status."'";
						$sqlupdatebrprod .= " , upload_date_time=CURRENT_TIMESTAMP() WHERE dns_prod_code='".addslashes($dns_prod_code)."' AND branch_code='".$dns_branch_code."'";
						mysqli_query($link,$sqlupdatebrprod);
					}

					if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
				    {
					  $pop_branch_prod_ins_fields = array(
								'dns_branch_code' => $dns_branch_code,
						  		'dns_prod_code' => $dns_prod_code,
								'prod_desc' => $prod_desc,
						  		'branch_name' => $branch_name,
						  		'status' => $status,
							);

					  $pop_branch_product_final_array[]=$pop_branch_prod_ins_fields;		

					  $pop_branch_prod_ins_fields_string = http_build_query($pop_branch_product_final_array); 
					}

			   }

		$rec_count++;
	}
	
	if(strtoupper($folderName)=='START' || strtoupper($folderName)=='STAR')
	  {
		array_push($upload_master_table_array,'branch_pop_product');
	  }	

	$successval=1;

  }	

	if(similar_file_exists("../csv/$folderName/customer_wise_target.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/customer_wise_target.csv");

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

			if($rec_count>=2)

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
				$customer_name=trim($data[0]);
				$start_date=trim($data[1]);
				$end_date=trim($data[2]);
				$value_slab=trim($data[3]);
				$sku_count=trim($data[4]);
				$apr_freq=trim($data[5]);
				$may_freq=trim($data[6]);
				$jun_freq=trim($data[7]);
				
				$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
				$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
				$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
				$csv_row_count=$rec_count+1;
				if($countcustomernamechk > 0)
					{
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code=$rowcustomernamechk['customer_code'];
						$start_date_parts=explode(".",$start_date);
						if(strlen($start_date_parts[0])==1)
						{
							$final_start_date=$start_date_parts[2].'-'.$start_date_parts[1].'-0'.$start_date_parts[0];
						}
						else
						{
							$final_start_date=$start_date_parts[2].'-'.$start_date_parts[1].'-'.$start_date_parts[0];
						}
						$end_date_parts=explode(".",$end_date);
						if(strlen($end_date_parts[0])==1)
						{
						$final_end_date=$end_date_parts[2].'-'.$end_date_parts[1].'-0'.$end_date_parts[0];
						}
						else
						{
							$final_end_date=$end_date_parts[2].'-'.$end_date_parts[1].'-'.$end_date_parts[0];
						}
						$sql  = "insert into retailer_wise_target_ach ";
						$sql .= " SET customer_code='".$customer_code."'";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , start_date='".addslashes($final_start_date)."'";
						$sql .= " , end_date='".addslashes($final_end_date)."'";
						$sql .= " , value_slab_target='".addslashes($value_slab)."'";
						$sql .= " , sku_count_target='".addslashes($sku_count)."'";
						$sql .= " , apr_freq_target='".addslashes($apr_freq)."'";
						$sql .= " , may_freq_target='".addslashes($may_freq)."'";
						$sql .= " , jun_freq_target='".addslashes($jun_freq)."'";
						$sql .= " , acedns='Y'";
						$sql .= " , upload_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in BOQ master.csv.Please check.");
					}
			}
			$rec_count++;
		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for BOQ master.csv is wrong.";

		exit();

	}	*/
	if(similar_file_exists("../csv/$folderName/beatewise_TA_DA.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/beatewise_TA_DA.csv");

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
				$sr_name=trim($data[0]);
				$so_name=trim($data[1]);
				$route_name=trim($data[2]);
				$HQ_EX_SO=trim($data[3]);
				$DA=trim($data[4]);
				$distance=trim($data[5]);
				$TA=trim($data[6]);
				
					$sqlempowncode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($sr_name)."'";
					$rsempowncode=mysqli_query($link,$sqlempowncode);
					$rowempowncode=mysqli_fetch_assoc($rsempowncode);
					$sr_code=$rowempowncode['emp_code'];

					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($so_name)."'";
					$rsempbosscode=mysqli_query($link,$sqlempbosscode);
					$rowempbosscode=mysqli_fetch_assoc($rsempbosscode);
					$so_code=$rowempbosscode['emp_code'];

					$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
					$rsroutechk=mysqli_query($link,$sqlroutechk);
					$rowroutechk=mysqli_fetch_assoc($rsroutechk);
					$route_code=$rowroutechk['route_code'];

				
				$sqlempnamechk="SELECT * FROM beatwise_TA_DA WHERE emp_code='".addslashes($sr_code)."' AND route_code='".addslashes($route_code)."'";
				$rsempnamechk=mysqli_query($link,$sqlempnamechk);
				$countempnamechk=mysqli_num_rows($rsempnamechk);
				$csv_row_count=$rec_count+1;
				if($countempnamechk==0)
					{
						$sql  = "insert into beatwise_TA_DA ";
						$sql .= " SET emp_code='".$sr_code."'";
						$sql .= " , emp_name='".addslashes($sr_name)."'";
						$sql .= " , reporting_to='".addslashes($so_code)."'";
						$sql .= " , reporting_to_name='".addslashes($so_name)."'";
						$sql .= " , route_code='".addslashes($route_code)."'";
						$sql .= " , route_name='".addslashes($route_name)."'";
						$sql .= " , HQ_EX_OS='".addslashes($HQ_EX_SO)."'";
						$sql .= " , DA='".addslashes($DA)."'";
						$sql .= " , distance='".addslashes($distance)."'";
						$sql .= " , TA='".addslashes($TA)."'";
						$sql .= " , acedns='Y'";
						$sql .= " , upload_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in beatewise_TA_DA.csv.Please check.");
					}
					else
					{
						$sqlupdate  = "UPDATE beatwise_TA_DA ";
						$sqlupdate .= " SET emp_name='".addslashes($sr_name)."'";
						$sqlupdate .= " , reporting_to='".addslashes($so_code)."'";
						$sqlupdate .= " , reporting_to_name='".addslashes($so_name)."'";
						$sqlupdate .= " , route_name='".addslashes($route_name)."'";
						$sqlupdate .= " , HQ_EX_OS='".addslashes($HQ_EX_SO)."'";
						$sqlupdate .= " , DA='".addslashes($DA)."'";
						$sqlupdate .= " , distance='".addslashes($distance)."'";
						$sqlupdate .= " , TA='".addslashes($TA)."'";
						$sqlupdate .= " , acedns='Y'";
						$sqlupdate .= " , upload_time=CURRENT_TIMESTAMP() WHERE emp_code='".addslashes($sr_code)."' AND route_code='".addslashes($route_code)."'";
					mysqli_query($link,$sqlupdate) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in beatewise_TA_DA.csv.Please check.");
					}
			}
			$rec_count++;
		}		

		$successval=1;

	}
	if(similar_file_exists("../csv/$folderName/Facilitator master other.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Facilitator master other.csv");
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
				
                $facilitator_code=trim($data[0]);
				$facilitator_name=trim($data[1]);
				$emp_code_name =trim($data[2]);
				$f_type =trim($data[3]);
				$firm_name =trim($data[4]);
				$f_address =trim($data[5]);
				$f_pin =trim($data[6]);
				$f_sub_area =trim($data[7]);
				$f_area =trim($data[8]);
				$mobile_no =trim($data[9]);
				$email_id =trim($data[10]);
				$dob =trim($data[11]);
				$annniversary =trim($data[12]);
				$acedns =trim($data[13]);
				$branch_code_name =trim($data[14]);

					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=mysqli_fetch_assoc($rsempcode);
					$emp_code=$rowempcode['emp_code'];
					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

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
					$sqlupdated .= " WHERE f_code='".$facilitator_code."'";
					mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on facilitator master other.csv.Please check.");
			}
			 $rec_count++;
		}
		$successval=1;

	}
	if(similar_file_exists("../csv/$folderName/gift master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/gift master.csv");
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
				$gift_name=trim($data[0]);
				$cust_type=trim($data[1]);
				$from_date=trim($data[2]);
				$from_date_final=date('Y-m-d',strtotime(str_replace('/','-',$from_date)));
				$to_date=trim($data[3]);
				$to_date_final=date('Y-m-d',strtotime(str_replace('/','-',$to_date)));
				$acedns=trim($data[4]);
				
				$sqlgiftchk="SELECT * FROM gift_master WHERE gift_name='".addslashes($gift_name)."' 
							AND cust_type='".addslashes($cust_type)."' AND acedns='".$acedns."' 
							AND from_date='".$from_date_final."' AND to_date='".$to_date_final."'";
				$rsgiftchk=mysqli_query($link,$sqlgiftchk);
				$countgiftchk=mysqli_num_rows($rsgiftchk);
				$csv_row_count=$rec_count+1;
				if($countgiftchk==0)
					{
						$sql  = "insert into gift_master ";
						$sql .= " SET gift_name='".$gift_name."'";
						$sql .= " , cust_type='".addslashes($cust_type)."'";
						$sql .= " , acedns='".addslashes($acedns)."'";
						$sql .= " , from_date='".addslashes($from_date_final)."'";
						$sql .= " , to_date='".addslashes($to_date_final)."'";
						$sql .= " , update_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in gift_master.csv.Please check.");
					}
			}
			$rec_count++;
		}		

		$successval=1;

	}
	if(similar_file_exists("../csv/$folderName/manager list.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/manager list.csv");
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
				$emp_code=trim($data[0]);
				$emp_name=trim($data[1]);
				$designation=trim($data[2]);
				$phone_no=trim($data[3]);
				
				$sqlmanagerchk="SELECT emp_code FROM whats_app_phone_list WHERE emp_code='".addslashes($emp_code)."'";
				$rsmanagerchk=mysqli_query($link,$sqlmanagerchk);
				$countmanagerchk=mysqli_num_rows($rsmanagerchk);
				$csv_row_count=$rec_count+1;
				if($countmanagerchk==0)
					{
						$sql  = "insert into whats_app_phone_list ";
						$sql .= " SET emp_code='".$emp_code."'";
						$sql .= " , emp_name='".addslashes($emp_name)."'";
						$sql .= " , designation='".addslashes($designation)."'";
						$sql .= " , phone_no='".addslashes($phone_no)."'";
						$sql .= " , update_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in manager list.csv.Please check.");
					}
					else
					{
						$sql  = "UPDATE whats_app_phone_list ";
						$sql .= " SET emp_name='".addslashes($emp_name)."'";
						$sql .= " , designation='".addslashes($designation)."'";
						$sql .= " , phone_no='".addslashes($phone_no)."'";
						$sql .= " , update_time=CURRENT_TIMESTAMP() where emp_code='".$emp_code."'";
						mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in manager list.csv.Please check.");
					}
			}
			$rec_count++;
		}		

		$successval=1;

	}
	if(similar_file_exists("../csv/$folderName/Mason Master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Mason Master.csv");
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
					$customer_name	=trim(preg_replace('/[\r\n]+/', '',$data[0]));
					$rds_tag   =trim(preg_replace('/[\r\n]+/', '',$data[1]));
					$district	  =trim(preg_replace('/[\r\n]+/', '',$data[2]));  
					$phone_no	  =trim(preg_replace('/[\r\n]+/', '',$data[3]));  
					$acedns		  ='Y';
					$customer_type   ='Engineer';
				//For distributor tagged
					$sqlrdscode="SELECT customer_code,route_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y' 
														 AND cust_type!='R'";
					$rsrdscode=mysqli_query($link,$sqlrdscode);
					$rowrdscode=mysqli_fetch_assoc($rsrdscode);
					$rds_code=$rowrdscode['customer_code'];
					$rds_route_code=$rowrdscode['route_code'];
				
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' 
									AND route_code='".$rds_route_code."' AND acedns='Y'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					$csv_row_count=$rec_count+1;

					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
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
						$sql .= " , dns_customer_code=''";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , route_code='".$rds_route_code."'";
						$sql .= " , acedns='Y'";
						$sql .= " , black_list='N'";
						$sql .= " , rds_tag='".$rds_code."'";
						$sql .= " , cust_type='".$customer_type."'";
						$sql .= " , district='".$district."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in Mason master.csv.Please check.");
					   $customer_code=$max_customer_code;
					}
					else
					{
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$phone_no_db=$rowcustomernamechk['phone_no'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						$rds_tag_db=$rowcustomernamechk['rds_tag'];
						$customer_name_db=$rowcustomernamechk['customer_name'];
						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];
						$district_db=$rowcustomernamechk['district'];
							$update_condition=" customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";
							
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , dns_customer_code=''";
							$sqlupdated .= " , cust_type='".$customer_type."'";
							$sqlupdated .= " , phone_no='".$phone_no."'";
							$sqlupdated .= " , district='".$district."'";
							$sqlupdated .= " , rds_tag='".$rds_code."',download_time=CURRENT_TIMESTAMP() 
											 WHERE  ".$update_condition."";
							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Mason Master.csv.Please check.");
							$customer_code=$customer_code_db;
						}
						
						$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 
											customer_code='".$rds_code."' AND acedns='Y'";
						$rsselcustomerroute=mysqli_query($link,$sqlselcustomerroute);
						$countcustomerroute=mysqli_num_rows($rsselcustomerroute);
						if($countcustomerroute > 0)
						{
							while($rowselcustomerroute=mysqli_fetch_assoc($rsselcustomerroute))
							{
								$route_code=$rowselcustomerroute['route_code'];
								$emp_code=$rowselcustomerroute['emp_code'];
								$sqlselmasonroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 
											customer_code='".$customer_code."' AND route_code='".$route_code."' AND emp_code='".$emp_code."' AND acedns='Y'";
								$rsselmasonroute=mysqli_query($link,$sqlselmasonroute);
								$countmasonroute=mysqli_num_rows($rsselmasonroute);
								if($countmasonroute==0)
								{
								$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',
														 route_code='".$route_code."',
														emp_code='".$emp_code."',
														acedns='Y',
														download_time=CURRENT_TIMESTAMP()";
								mysqli_query($link,$sqlinsertcustomerroute);
								}
								/*else
								{
									$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() 
															WHERE customer_code='".$customer_code."' AND route_code='".$route_code."' 
															AND emp_code='".$emp_code."'";
									mysqli_query($link,$sqlupdatecustomerroute);						
								}*/
							}
						}
						else
						{
								$sqlselmasonroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 
											customer_code='".$customer_code."'";
								$rsselmasonroute=mysqli_query($link,$sqlselmasonroute);
								$countmasonroute=mysqli_num_rows($rsselmasonroute);
								if($countmasonroute==0)
								{
								$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',
														 route_code='".$route_code."',
														emp_code='".$emp_code."',
														acedns='Y',
														download_time=CURRENT_TIMESTAMP()";
								mysqli_query($link,$sqlinsertcustomerroute);
								}
						}
					}
					$rec_count++;
				}//End of for loop
			$successval=1;
		}		
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
	if(similar_file_exists("../csv/$folderName/weightage conversion.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/weightage conversion.csv");
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
				$state_name=trim($data[0]);
				$prod_code_name=trim($data[1]);
				$uom1=trim($data[2]);
				$uom2=trim($data[3]);
				$weightage_conversion1=trim($data[4]);
				$weightage_conversion2=trim($data[5]);
				if($providing_code=='yes'){
					$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";
				}
				else
				{
					$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
				} 
				$rsproductcode=mysqli_query($link,$sqlproductcode);
				$rowproductcode=mysqli_fetch_assoc($rsproductcode);
				$product_code=$rowproductcode['prod_code'];
				$csv_row_count=$rec_count+1;
				
				$sqlweightagechk="SELECT prod_code FROM state_product_wise_weightage WHERE prod_code='".$product_code."' 
							AND state_name='".$state_name."'";
				$rsweightagechk=mysqli_query($link,$sqlweightagechk);
				$countweightagechk=mysqli_num_rows($rsweightagechk);
				$rowweightagechk=mysqli_fetch_assoc($rsweightagechk);

				if($countweightagechk<1)
				{
					 $sqlinsertweightage  = "insert into state_product_wise_weightage SET ";
					$sqlinsertweightage .= "  	state_name='".addslashes($state_name)."'";
					$sqlinsertweightage .= " , prod_code='".addslashes($product_code)."'";
					$sqlinsertweightage .= " , UOM1='".addslashes($uom1)."'";
					$sqlinsertweightage .= " , UOM2='".addslashes($uom2)."'";
					$sqlinsertweightage .= " , weightage_conversio1='".addslashes($weightage_conversion1)."'";
					$sqlinsertweightage .= " , weightage_conversion2='".addslashes($weightage_conversion2)."'";
					$sqlinsertweightage .= " , acedns='yes'";
					$sqlinsertweightage .= " , upload_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertweightage) or array_push($error_array,"mysqli_error().Internal error occurs on weightage conversion table.Please contact ADMIN.");
				}

				else
				{
					 $sqlupdateweightage  = "UPDATE state_product_wise_weightage SET ";
					$sqlupdateweightage .= "  	UOM1='".addslashes($uom1)."'";
					$sqlupdateweightage .= "  	,UOM2='".addslashes($uom2)."'";
					$sqlupdateweightage .= "  	,weightage_conversio1='".addslashes($weightage_conversion1)."'";
					$sqlupdateweightage .= "  	,weightage_conversion2='".addslashes($weightage_conversion2)."'";
					$sqlupdateweightage .= ",  upload_time=CURRENT_TIMESTAMP()";
					$sqlupdateweightage .= "  WHERE prod_code='".addslashes($product_code)."' AND state_name='".addslashes($state_name)."'";
					mysqli_query($link,$sqlupdateweightage) or array_push($error_array,"mysqli_error().Internal error occurs on  weightage conversion table.Please contact ADMIN");
				}
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/customer product stock.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/customer product stock.csv");
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
				$customer_name=trim($data[0]);
				$prod_code_name=trim($data[1]);
				$stock=trim($data[2]);
				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_name."'";
				$rscustomercode=mysqli_query($link,$sqlcustomercode);
				$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
				$customer_code=$rowcustomercode['customer_code'];
					
				$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
				$rsproductcode=mysqli_query($link,$sqlproductcode);
				$rowproductcode=mysqli_fetch_assoc($rsproductcode);
				$product_code=$rowproductcode['prod_code'];
				$csv_row_count=$rec_count+1;
				
				$sqlcustomerproductstk="SELECT prod_code FROM customer_product_stock WHERE prod_code='".$product_code."' 
							AND customer_code='".$customer_code."'";
				$rscustomerproductstk=mysqli_query($link,$sqlcustomerproductstk);
				$countcustomerproductstk=mysqli_num_rows($rscustomerproductstk);
				$rowweightagechk=mysqli_fetch_assoc($rsweightagechk);

				if($countcustomerproductstk<1)
				{
					$sqlinsertstk  = "insert into customer_product_stock SET ";
					$sqlinsertstk .= "  	prod_code='".addslashes($product_code)."'";
					$sqlinsertstk .= " , customer_code='".addslashes($customer_code)."'";
					$sqlinsertstk .= " , uploaded_stock='".addslashes($stock)."'";
					$sqlinsertstk .= " , stock='".addslashes($stock)."'";
					$sqlinsertstk .= " , update_date_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on customer product stock table.Please contact ADMIN.");
				}

				else
				{
					$sqlupdatestk  = "UPDATE customer_product_stock SET ";
					$sqlupdatestk .= "  	stock='".addslashes($stock)."'";
					$sqlupdatestk .= "  	uploaded_stock='".addslashes($stock)."'";
					$sqlupdatestk .= ",  update_date_time=CURRENT_TIMESTAMP()";
					$sqlupdatestk .= "  WHERE prod_code='".addslashes($product_code)."' AND customer_code='".addslashes($customer_code)."'";
					mysqli_query($link,$sqlupdatestk) or array_push($error_array,"mysqli_error().Internal error occurs on   customer product stock table.Please contact ADMIN");
				}
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/OOH Site Master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/OOH Site Master.csv");
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
				$ooh_site_code=trim($data[0]);
				$state=trim($data[1]);
				$area=trim($data[2]);
				$city=trim($data[3]);
				$location=trim($data[4]);
				$facing=trim($data[5]);
				$media=trim($data[6]);
				$type=trim($data[7]);
				$l_r=trim($data[8]);
				$t_b=trim($data[9]);
				$qty=trim($data[10]);
				$fascia=trim($data[11]);
				$sq_ft=trim($data[12]);
				$vendor=trim($data[13]);
				$vendor_code=trim($data[14]);
				$location_facing=trim($data[15]);
				$acedns=trim($data[16]);
				
				$csv_row_count=$rec_count+1;
				
				$sqlvendordetailschk="SELECT vendor_code FROM vendor_details WHERE ooh_site_code='".addslashes($ooh_site_code)."'";
				$rsvendordetailschk=mysqli_query($link,$sqlvendordetailschk);
				$countvendordetailschk=mysqli_num_rows($rsvendordetailschk);
				$rowvendordetailschk=mysqli_fetch_assoc($rsvendordetailschk);

				if($countvendordetailschk<1)
				{
					$sqlinsertvendordetails  = "insert into vendor_details SET ";
					$sqlinsertvendordetails .= " ooh_site_code='".addslashes($ooh_site_code)."'";
					$sqlinsertvendordetails .= " , state='".addslashes($state)."'";
					$sqlinsertvendordetails .= " , area='".addslashes($area)."'";
					$sqlinsertvendordetails .= " , city='".addslashes($city)."'";
					$sqlinsertvendordetails .= " , location='".addslashes($location)."'";
					$sqlinsertvendordetails .= " , facing='".addslashes($facing)."'";
					$sqlinsertvendordetails .= " , media='".addslashes($media)."'";
					$sqlinsertvendordetails .= " , `type`='".addslashes($type)."'";
					$sqlinsertvendordetails .= " , L_R='".addslashes($l_r)."'";
					$sqlinsertvendordetails .= " , T_B='".addslashes($t_b)."'";
					$sqlinsertvendordetails .= " , qty='".addslashes($qty)."'";
					$sqlinsertvendordetails .= " , fascia='".addslashes($fascia)."'";
					$sqlinsertvendordetails .= " , sq_ft='".addslashes($sq_ft)."'";
					$sqlinsertvendordetails .= " , vendor='".addslashes($vendor)."'";
					$sqlinsertvendordetails .= " , vendor_code='".addslashes($vendor_code)."'";
					$sqlinsertvendordetails .= " , location_facing='".addslashes($location_facing)."'";
					$sqlinsertvendordetails .= " , acedns='".addslashes($acedns)."'";
					$sqlinsertvendordetails .= " , upload_date_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertvendordetails) or array_push($error_array,"mysqli_error().Internal error occurs on OOH Site Master.Please contact ADMIN.");
				}
				else
				{
					$sqlupdatevendordetails  = "UPDATE vendor_details SET ";
					$sqlupdatevendordetails .= " 	state='".addslashes($state)."'";
					$sqlupdatevendordetails .= " , area='".addslashes($area)."'";
					$sqlupdatevendordetails .= " , city='".addslashes($city)."'";
					$sqlupdatevendordetails .= " , location='".addslashes($location)."'";
					$sqlupdatevendordetails .= " , facing='".addslashes($facing)."'";
					$sqlupdatevendordetails .= " , media='".addslashes($media)."'";
					$sqlupdatevendordetails .= " , `type`='".addslashes($type)."'";
					$sqlupdatevendordetails .= " , L_R='".addslashes($l_r)."'";
					$sqlupdatevendordetails .= " , T_B='".addslashes($t_b)."'";
					$sqlupdatevendordetails .= " , qty='".addslashes($qty)."'";
					$sqlupdatevendordetails .= " , fascia='".addslashes($fascia)."'";
					$sqlupdatevendordetails .= " , sq_ft='".addslashes($sq_ft)."'";
					$sqlupdatevendordetails .= " , vendor='".addslashes($vendor)."'";
					$sqlupdatevendordetails .= " , vendor_code='".addslashes($vendor_code)."'";
					$sqlupdatevendordetails .= " , location_facing='".addslashes($location_facing)."'";
					$sqlupdatevendordetails .= " , acedns='".addslashes($acedns)."'";
					$sqlupdatevendordetails .= " , upload_date_time=CURRENT_TIMESTAMP() WHERE  ooh_site_code='".addslashes($ooh_site_code)."'";
					mysqli_query($link,$sqlupdatevendordetails) or array_push($error_array,"mysqli_error().Internal error occurs on OOH Site Master.Please contact ADMIN.");
				}
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	 if(similar_file_exists("../csv/$folderName/TA_DA.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/TA_DA.csv");
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
				$emp_code=trim($data[0]);
				$emp_name=trim($data[1]);
				$per_km_TA=trim($data[2]);
				$DA_HQ=trim($data[3]);
				$DA_EX_HQ=trim($data[4]);
				$DA_outstation=trim($data[5]);
				
				$sqlempnamechk="SELECT * FROM TA_DA_master WHERE emp_code='".addslashes($emp_code)."'";
				$rsempnamechk=mysqli_query($link,$sqlempnamechk);
				$countempnamechk=mysqli_num_rows($rsempnamechk);
				$csv_row_count=$rec_count+1;
				if($countempnamechk==0)
					{
						$sql  = "insert into TA_DA_master ";
						$sql .= " SET emp_code='".$emp_code."'";
						$sql .= " , emp_name='".addslashes($emp_name)."'";
						$sql .= " , per_km_TA='".addslashes($per_km_TA)."'";
						$sql .= " , DA_HQ='".addslashes($DA_HQ)."'";
						$sql .= " , DA_EX_HQ='".addslashes($DA_EX_HQ)."'";
						$sql .= " , DA_outstation='".addslashes($DA_outstation)."'";
						$sql .= " , upload_date_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in TA_DA.csv.Please check.");
					}
					else
					{
						$sqlupdate  = "UPDATE TA_DA_master ";
						$sqlupdate .= " SET emp_name='".addslashes($emp_name)."'";
						$sqlupdate .= " , per_km_TA='".addslashes($per_km_TA)."'";
						$sqlupdate .= " , DA_HQ='".addslashes($DA_HQ)."'";
						$sqlupdate .= " , DA_EX_HQ='".addslashes($DA_EX_HQ)."'";
						$sqlupdate .= " , DA_outstation='".addslashes($DA_outstation)."'";
						$sqlupdate .= " , upload_date_time=CURRENT_TIMESTAMP() WHERE emp_code='".addslashes($emp_code)."'";
					mysqli_query($link,$sqlupdate) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in TA_DA.csv.Please check.");
					}
			}
			$rec_count++;
		}		

		$successval=1;

	}
	if(similar_file_exists("../csv/$folderName/PJP UPLOAD.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/PJP UPLOAD.csv");
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
				$state=trim($data[0]);
				$date=trim($data[1]);
				$date=str_replace('/','-',$date);
				$day=trim($data[2]);
				$employee=trim($data[3]);
				$area=trim($data[4]);
				$route_name=trim($data[5]);
				$route_no=trim($data[6]);
				$objective=trim($data[7]);
				$datearray=explode('-',$date);
				$visit_date=$datearray[2].'-'.$datearray[1].'-'.$datearray[0];
				
				$sqlroutename="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_code=$rowroutename['route_code'];
				
				$sqlemp="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($employee)."'";
				$rsemp=mysqli_query($link,$sqlemp);
				$rowemp=mysqli_fetch_assoc($rsemp);
				$emp_code=$rowemp['emp_code'];
				$trans_id_val='RP'.$emp_code.date('YmdHis');
								
				$sql_delete="DELETE from route_plan WHERE emp_code='".$emp_code."' AND visit_date='".$visit_date."' 
					AND route_code='".$route_code."' and status='active'";
				mysqli_query($link,$sql_delete);
		
			  $sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$trans_id_val."',
								  emp_code 		='".$emp_code."',
								  route_code 		='".$route_code."',
								  visit_date 		='".$visit_date."',
								  route_no 			='".$route_no."',
								  area 				='".$area."',
								  objective 		='".$objective."',
								  status			='active',
								  is_approved		='yes',
								  create_date		=CURRENT_TIMESTAMP(),
								  update_date		=CURRENT_TIMESTAMP()";
			 mysqli_query($link,$sqlinsertrouteplan);
				
			}
			$rec_count++;
		}		

		$successval=1;

	}
	 if(similar_file_exists("../csv/$folderName/Route Category Target ACh.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Route Category Target ACh.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		/*$sqldelete="truncate self_appraisal_branch_wise";
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

				$emp_name=trim($data[0]);
				$area=trim($data[1]);
				$route_name=trim($data[2]);
				$route_no=trim($data[3]);
				$DTS_target=trim($data[4]);
				$DW_target=trim($data[5]);
				$other_target=trim($data[6]);

				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_name)."'";
				$rsempcode=mysqli_query($link,$sqlempcode);
				$rowempcode=mysqli_fetch_assoc($rsempcode);
				$emp_code=$rowempcode['emp_code'];

				$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
				$rsroutecode=mysqli_query($link,$sqlroutecode);
				$rowroutecode=mysqli_fetch_assoc($rsroutecode);
				$route_code=$rowroutecode['route_code'];

				$sqlchkemproute="SELECT emp_code,product_category FROM self_appraisal_route_product_group_wise 
									WHERE emp_code='".$emp_code."' AND route_code='".$route_code."'";
				$rschkemproute=mysqli_query($link,$sqlchkemproute);
				$countchkemproute=mysqli_num_rows($rschkemproute);

				//$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				$csv_row_count=$rec_count+1;
				$curdate=date('d-m-Y');
				$month=substr($curdate,3,2);
				if($countchkemproute==0){
					for($i=0;$i<3;$i++)
					{
						if($i==0)
						{
						 $product_category='DTS';
						 if($month=='12')
						 {  
						 $target_cond=" ,dec_31_target='".mysqli_real_escape_string($DTS_target)."'";
						 }
						}
						if($i==1)
						{
							$product_category='DW';
							if($month=='12')
						 	{ 
							$target_cond=" ,dec_31_target='".mysqli_real_escape_string($DW_target)."'";
							}
						}
						if($i==2)
						{
							$product_category='Other';
							if($month=='12')
						 	{ 
							$target_cond=" ,dec_31_target='".mysqli_real_escape_string($other_target)."'";
							}
						}
						
						
						$sqlselfappraisal  = "insert into self_appraisal_route_product_group_wise SET ";
						$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";
						$sqlselfappraisal .= "  , emp_name='".mysqli_real_escape_string($emp_name)."'";
						$sqlselfappraisal .= " , route_code='".mysqli_real_escape_string($route_code)."'";
						$sqlselfappraisal .= " , route_name='".mysqli_real_escape_string($route_name)."'";
						$sqlselfappraisal .= " , `area`='".mysqli_real_escape_string($area)."'";
						$sqlselfappraisal .= " , route_no='".mysqli_real_escape_string($route_no)."'";
						$sqlselfappraisal .= " , product_category='".mysqli_real_escape_string($product_category)."'";
						$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";
						$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
						$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
						$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
						$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
						$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($sep_target)."'";
						$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
						$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'".$target_cond;
						$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
						$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
						$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
						$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Route Category Target ACh.csv.Please check.");
					}
				}
				else
				{
					for($i=0;$i<3;$i++)
					{
					if($i==0)
						{
						 $product_category='DTS';
						 if($month=='12')
						 {  
						 $target_cond=" ,dec_31_target='".mysqli_real_escape_string($DTS_target)."'";
						 }
						}
						if($i==1)
						{
							$product_category='DW';
							if($month=='12')
						 	{ 
							$target_cond=" ,dec_31_target='".mysqli_real_escape_string($DW_target)."'";
							}
						}
						if($i==2)
						{
							$product_category='Other';
							if($month=='12')
						 	{ 
							$target_cond=" ,dec_31_target='".mysqli_real_escape_string($other_target)."'";
							}
						}

					$sqlselfappraisalupdate  = "update self_appraisal_route_product_group_wise SET ";
					$sqlselfappraisalupdate .= "   apr_30_target='".mysqli_real_escape_string($april_target)."'";
					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";
					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";
					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";
					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";
					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";
					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'".$target_cond;
					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";
					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";
					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";
					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";
					$sqlselfappraisalupdate .= " , `area`='".mysqli_real_escape_string($area)."'";
					$sqlselfappraisalupdate .= " , route_no='".mysqli_real_escape_string($route_no)."'";
					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' 
												AND product_category='".$product_category."' AND route_code='".$route_code."'";
					mysqli_query($link,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Route Category Target ACh.csv.Please check.");
					}
				}
			}
			 $rec_count++;
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
/**/
			/*	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .

							"Reply-To:".FROMEMAIL." \r\n" .

							"Bcc: ".BCCEMAIL." \r\n" .

							'X-Mailer: PHP/' . phpversion();

				*/

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

							$url="http://salesmpower.acedns.in/misreport/uploadbranchdataSTARSATHICURL.php";

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

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='product_master'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadskudataSTARSATHICURL.php";

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

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadbrokerdataSTARSATHICURL.php";

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
						if(in_array('pop_product',$upload_master_table_array))
						{
						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 
						   				where table_name='pop_product'";
						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/upload_pop_product_data_curl.php";

							$ch = curl_init();
							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_POST, 1);
							curl_setopt($ch,CURLOPT_POSTFIELDS, "$pop_prod_ins_fields_string");
							//execute post
							$result = curl_exec($ch);
							print_r($result);
							curl_close($ch);

						}
						if(in_array('branch_pop_product',$upload_master_table_array))
						{
						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 
						   				where table_name='branch_pop_product'";
						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/upload_pop_branch_product_data_curl.php";

							$ch = curl_init();
							//set the url, number of POST vars, POST data

							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_POST, 1);
							curl_setopt($ch,CURLOPT_POSTFIELDS, "$pop_branch_prod_ins_fields_string");
							//execute post
							$result = curl_exec($ch);
							print_r($result);
							curl_close($ch);

						}
						if(in_array('customer_broker_relation',$upload_master_table_array))

						{

						   $sqlupdatetableinfo="UPDATE master_tables_update_info set need_update='yes',download_time_yes=CURRENT_TIMESTAMP() 

						   				where table_name='customer_broker_relation'";

						    mysqli_query($link,$sqlupdatetableinfo);

							//$postData = json_encode($cust_in_final_array);

							$url="http://salesmpower.acedns.in/misreport/uploadcustomerbrokerdataSTARSATHICURL.php";

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

							$url="http://salesmpower.acedns.in/misreport/uploadbranchdumpdataSTARSATHICURL.php";

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
					if(strtoupper($folderName)=='GOLDSTONET')
					{
							$url="http://salesmpower.acedns.in/misreport/uploaddataGOLDSTONECURL.php";
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

				else

				{

					echo $GLOBALS['msg'] = "Error in mail sending.";

					disphtml("main();");

				}

				

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