<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if($_REQUEST['mode']=="csv_upload")				csv_upload();
	else    										disphtml("main();");
ob_end_flush();
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
			<td colspan="10">Upload Product Data</td>
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
	if(similar_file_exists("../csv/$folderName/thickness.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/thickness.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
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
				$dns_thickness_code=trim($data[0]);
				$thickness=trim($data[1]);
				
				$sqlchkthicknesscode="SELECT thickness_code FROM thickness_master WHERE dns_thickness_code='".addslashes($dns_thickness_code)."'";
				$rschkthicknesscode=mysqli_query($link,$sqlchkthicknesscode);
				$countchkthicknesscode=mysqli_num_rows($rschkthicknesscode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchkthicknesscode ==0)
				{
					$sqlmaxthicknesscode="SELECT MAX(thickness_code) AS max_thickness_code FROM  thickness_master WHERE 1";
					$rsmaxthicknesscode=mysqli_query($link,$sqlmaxthicknesscode);
					$rowmaxthicknesscode=mysqli_fetch_assoc($rsmaxthicknesscode);

					$max_thickness_code=$rowmaxthicknesscode['max_thickness_code'];					
					if($max_thickness_code=='')
					{
						$max_thickness_code='T0001';
					}
					else
					{
						$max_thickness_code++;
					}
					$sqlthickness  = "insert into thickness_master SET ";
					$sqlthickness .= "   thickness_code='".mysqli_real_escape_string($max_thickness_code)."'";
					$sqlthickness .= " , dns_thickness_code='".mysqli_real_escape_string($dns_thickness_code)."'";
					$sqlthickness .= " , thickness='".mysqli_real_escape_string($thickness)."'";
					$sqlthickness .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlthickness) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in thickness.csv.Please check.");
				}
				else
				{
					$sqlupdatethickness  = "update thickness_master SET ";
					$sqlupdatethickness .= " thickness='".mysqli_real_escape_string($thickness)."'";
					$sqlupdatethickness .= " , download_time=CURRENT_TIMESTAMP() WHERE dns_thickness_code='".$dns_thickness_code."'";
					mysqli_query($link,$sqlupdatethickness) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in thickness.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/size.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/size.csv");
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
				$dns_size_code=trim($data[0]);
				$size=trim($data[1]);
				
				$sqlchkSizecode="SELECT size_code FROM size_master WHERE dns_size_code='".addslashes($dns_size_code)."'";
				$rschksizecode=mysqli_query($link,$sqlchksizecode);
				$countchksizecode=mysqli_num_rows($rschksizecode);
				//$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);
				//$customer_code=$rowcustomercode['customer_code'];
				$csv_row_count=$rec_count+1;
				if($countchksizecode ==0)
				{
					$sqlmaxsizecode="SELECT MAX(size_code) AS max_size_code FROM  size_master WHERE 1";
					$rsmaxsizecode=mysqli_query($link,$sqlmaxsizecode);
					$rowmaxsizecode=mysqli_fetch_assoc($rsmaxsizecode);

					$max_size_code=$rowmaxsizecode['max_size_code'];					
					if($max_size_code=='')
					{
						$max_size_code='S0001';
					}
					else
					{
						$max_size_code++;
					}
					$sqlsize  = "insert into size_master SET ";
					$sqlsize .= "   size_code='".mysqli_real_escape_string($max_size_code)."'";
					$sqlsize .= " , dns_size_code='".mysqli_real_escape_string($dns_size_code)."'";
					$sqlsize .= " , size='".mysqli_real_escape_string($size)."'";
					$sqlsize .= " , download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlsize) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in size.csv.Please check.");
				}
				else
				{
					$sqlupdatesize  = "update size_master SET ";
					$sqlupdatesize .= " size='".mysqli_real_escape_string($size)."'";
					$sqlupdatesize .= " , download_time=CURRENT_TIMESTAMP() WHERE dns_size_code='".$dns_size_code."'";
					mysqli_query($link,$sqlupdatesize) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in size.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	if(similar_file_exists("../csv/$folderName/category.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/category.csv");
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
			  $dns_product_group_code=trim($data[0]);
			  $product_group_name=trim($data[1]);
				 $sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE dns_product_group_code='".addslashes($dns_product_group_code)."'";
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
							$sqlbrand .= " , product_group_name='".addslashes($product_group_name)."'";
							$sqlbrand .= " , dns_product_group_code='".addslashes($dns_product_group_code)."'";
							$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in category.csv.Please check.");

						}
						else
						{
							$sqlupdatebrand  = "UPDATE product_group_master SET ";
							$sqlupdatebrand .= " product_group_name='".addslashes($product_group_code_name)."'";
							$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE dns_product_group_code='".addslashes($dns_product_group_code)."'";
							mysqli_query($link,$sqlupdatebrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in category.csv.Please check.");
						}
					}
			 $rec_count++;
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
				if(providing_code=='yes'){
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
					$sqlupdateweightage .= "  	UOM1='".addslashes($uom2)."'";
					$sqlupdateweightage .= "  	weightage_conversio1='".addslashes($weightage_conversio1)."'";
					$sqlupdateweightage .= "  	weightage_conversion2='".addslashes($weightage_conversion2)."'";
					$sqlupdateweightage .= ",  upload_time=CURRENT_TIMESTAMP()";
					$sqlupdateweightage .= "  WHERE prod_code='".addslashes($product_code)."' AND state_name='".addslashes($state_name)."'";
					mysqli_query($link,$sqlupdateweightage) or array_push($error_array,"mysqli_error().Internal error occurs on  weightage conversion table.Please contact ADMIN");
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
	if(similar_file_exists("../csv/$folderName/SKU master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/SKU master.csv");
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
					 print_r($data);
					$csv_row_count=$rec_count+1;
					$branch_code_name=trim($data[0]);
					$dns_prod_code=trim($data[1]);
					$prod_desc=trim($data[2]);
					//$prod_desc=str_replace('~','"',$prod_desc);
					$product_group_code_name=trim($data[3]);
					$product_sub_group_code_name=trim($data[4]);
					$product_brand_code_name=trim($data[5]);
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

					$sqlthickness="SELECT thickness_code,thickness,dns_thickness_code FROM thickness_master";
					$rsthickness=mysqli_query($link,$sqlthickness);
					$countthickness=mysqli_num_rows($rsthickness);
					while($rowthickness=mysqli_fetch_assoc($rsthickness))
					{
						$thickness_code=$rowthickness['thickness_code'];
						$thickness=$rowthickness['thickness'];
						$dns_thickness_code=$rowthickness['dns_thickness_code'];
						
						$sqlsize="SELECT size_code,size,dns_size_code FROM size_master";
						$rssize=mysqli_query($link,$sqlsize);
						$countsize=mysqli_num_rows($rssize);
						while($rowsize=mysqli_fetch_assoc($rssize))
						{
							$size_code=$rowsize['size_code'];
							$size=$rowsize['size'];
							$dns_size_code=$rowsize['dns_size_code'];
						
						//Product Group
						$sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE dns_product_group_code='".addslashes($product_group_code_name)."'";
						$rsprodgroupnamechk=mysqli_query($link,$sqlprodgroupnamechk);
						$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);
						$rowprodgroupnamechk=mysqli_fetch_assoc($rsprodgroupnamechk);
						$product_group_code=$rowprodgroupnamechk['product_group_code'];
						
						//Product Sub Group
						
						$sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($thickness)."' AND product_group_code='".$product_group_code."'";
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
							$sqlbrandform .= " , product_sub_group_name='".addslashes($thickness)."'";
							$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlbrandform);
							$product_sub_group_code=$max_product_sub_group_code;
						}
						else
						{
							$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);
							$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];
						}

						//Product Brand
						
						$sqlprodbrandnamechk="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".addslashes($size)."'
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
							$sqlbrandsubform .= " , product_brand_name='".addslashes($size)."'";
							$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlbrandsubform);
							$product_brand_code=$max_product_brand_code;
						}
						else
						{
							$rowprodbrandnamechk=mysqli_fetch_assoc($rsprodbrandnamechk);
							$product_brand_code=$rowprodbrandnamechk['product_brand_code'];
						}
							
							//$sqlskunamechk="SELECT * FROM product_master WHERE  dns_prod_code='".$dns_prod_code."'";
						$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'
											AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
											AND product_brand_code='".$product_brand_code."'";
						$rsskunamechk=mysqli_query($link,$sqlskunamechk);
						$countskunamechk=@mysqli_num_rows($rsskunamechk);
						$rowskunamechk=@mysqli_fetch_assoc($rsskunamechk);
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
							$sql .= " , branch_code=''";
							$sql .= " , state_code=''";
							$sql .= " , prod_desc='".addslashes($prod_desc)."'";
							$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
							$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";
							$sql .= " , cl_stk=''";
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
						$res2 = mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
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
								$updateflag=1;
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
	if($successval==1)
	{
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		if($nick_name =='STAR')
		{
			//$mailto='sumansaha@cmcl.co.in';
			  $mailto='warroom@starcement.co.in,emovesfa@starcement.co.in,kishukeshav@starcement.co.in';
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
		//if(mail($mailto, $mailsub, $mailbody, $headers,'-facedns@coral.in'))
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
		echo $GLOBALS['msg'] = "Problem with uploading Zip file";
		disphtml("main();");
	}
 }
?>s