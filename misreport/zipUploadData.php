<?php
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	
ob_end_flush();
if($_REQUEST['mode']=='csv_upload')
{
	csv_upload();
}
else
{
	disphtml("main();");
}
function main()
{
	//echo $_SESSION['nick_name'];
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
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
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
	<form name="form_add_CSV" action="" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
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
	
	//For Unzip a zip file
	$folderName = $_SESSION['nick_name'];
	$error_array=array();
	if ( !file_exists($folderName)){
		mkdir("../csv/$folderName");
		chmod("../csv/$folderName", 0777);
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

		//For Brand Master CSV
		if(similar_file_exists("../csv/$folderName/Brand Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Brand Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$sqldelete="truncate product_group_master";
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
				  
					$product_group_name=trim($data[0]);
					
					$csv_row_count=$rec_count+1;
					$sqlbrand  = "insert into product_group_master SET ";
					$sqlbrand .= "  product_group_code='".mysqli_escape_string($product_group_name)."'";
					$sqlbrand .= " , product_group_name='".mysqli_escape_string($product_group_name)."'";
					
					mysqli_query($link,$sqlbrand) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand name column in Brand Master.csv.Please check.");
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		else
		{
			echo $successval="Naming convention for Brand Master.csv is wrong.";
			exit();
		}
		
		//For Brand Form Master CSV
		if(similar_file_exists("../csv/$folderName/Brand Form Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Brand Form Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$sqldelete="truncate product_sub_group_master";
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
				  	$product_group_name=trim($data[0]);
					$product_sub_group_name=trim($data[1]);
					
					$csv_row_count=$rec_count+1;
					$sqlbrandform  = "insert into product_sub_group_master SET ";
					$sqlbrandform .= "  product_sub_group_code='".mysqli_escape_string($product_sub_group_name)."'";
					$sqlbrandform .= " , product_sub_group_name='".mysqli_escape_string($product_sub_group_name)."'";
					$sqlbrandform .= " , product_group_code='".mysqli_escape_string($product_group_name)."'";
					
					mysqli_query($link,$sqlbrandform) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand name and Brand form name columns in Brand Form Master.csv.Please check.");
				}
				 $rec_count++;
			}
			//Product group code checking start
				$sqlgroupcodesub_group="SELECT product_group_code FROM product_sub_group_master WHERE product_group_code NOT IN(SELECT product_group_code FROM product_group_master)";
				$rsgroupcodesub_group=mysqli_query($link,$sqlgroupcodesub_group);
				$cntgroupcodesub_group=mysqli_num_rows($rsgroupcodesub_group);
				if($cntgroupcodesub_group>0)
				{
					$groupcodesub_group='';
					while($rowgroupcodesub_group=mysqli_fetch_assoc($rsgroupcodesub_group))
					{
						$groupcodesub_group=$groupcodesub_group.$rowgroupcodesub_group['product_group_code'].',';
					}
					$groupcodesub_group=substr($groupcodesub_group,0,-1);
					$errorgroupcodesub_group=$groupcodesub_group.' exists in Brand Form Master but not exists in Brand Master.';
					array_push($error_array,$errorgroupcodesub_group);
				}
			//Product group code checking end		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Brand Form Master.csv is wrong.";
			exit();
		}*/
		
		//For Brand Form Master CSV
		if(similar_file_exists("../csv/$folderName/Brand sub form master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Brand sub form master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$sqldelete="truncate product_brand_master";
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
					$product_sub_group_name=trim($data[0]);
					$product_brand_name=trim($data[1]);
					
					$csv_row_count=$rec_count+1;
					$sqlbrandsubform  = "insert into product_brand_master SET ";
					$sqlbrandsubform .= "  product_brand_code='".mysqli_escape_string($product_brand_name)."'";
					$sqlbrandsubform .= " , product_sub_group_code='".mysqli_escape_string($product_sub_group_name)."'";
					$sqlbrandsubform .= " , product_brand_name='".mysqli_escape_string($product_brand_name)."'";
					
					mysqli_query($link,$sqlbrandsubform) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand form name and Brand sub form name columns in Brand Sub Form Master.csv.Please check.");
				}
				 $rec_count++;
			}
			//Product sub group code checking start
				$sqlsubgroupcodebrand="SELECT product_sub_group_code FROM product_brand_master WHERE product_sub_group_code NOT IN(SELECT product_sub_group_code FROM product_sub_group_master)";
				$rssubgroupcodebrand=mysqli_query($link,$sqlsubgroupcodebrand);
				$cntsubgroupcodebrand=mysqli_num_rows($rssubgroupcodebrand);
				if($cntsubgroupcodebrand>0)
				{
					$subgroupcodebrand='';
					while($rowsubgroupcodebrand=mysqli_fetch_assoc($rssubgroupcodebrand))
					{
						$subgroupcodebrand=$subgroupcodebrand.$rowsubgroupcodebrand['product_sub_group_code'].',';
					}
					$subgroupcodebrand=substr($subgroupcodebrand,0,-1);
					$errorsubgroupcodebrand=$subgroupcodebrand.' exists in Brand Sub Form Master but not exists in Brand Form Master.';
					array_push($error_array,$errorsubgroupcodebrand); 
				}
			//Product sub group code checking end		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Brand Sub Form Master.csv is wrong.";
			exit();
		}*/
		
		//For Sku CSV
		if(similar_file_exists("../csv/$folderName/SKU Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/SKU Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$lines=str_replace('"','~',$lines);
			$sqldelete="truncate product_master_temp";
			$rsdelete=mysqli_query($link,$sqldelete);
			/*$sqlskuchk="SELECT * FROM product_master";
			$rsskuchk=mysqli_query($link,$sqlskuchk);
			$countskuchk=mysqli_num_rows($rsskuchk);*/

			/*$sku_code='12001';*/
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
				  
					$prod_desc=trim($data[0]);
					$prod_desc=str_replace('~','"',$prod_desc);
					$product_group_code=trim($data[1]);
					$product_sub_group_code=trim($data[2]);
					$product_brand_code=trim($data[3]);
					$cl_stk=trim($data[4]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					$acedns=trim($data[5]);
					$black_list=trim($data[6]);
					
					$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."' 
									AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
									AND product_brand_code='".$product_brand_code."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$countskunamechk=mysqli_num_rows($rsskunamechk);
					$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
					
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
						$sql .= " , prod_desc='".addslashes($prod_desc)."'";
						$sql .= " , product_group_code='".mysqli_escape_string($product_group_code)."'";
						$sql .= " , product_sub_group_code='".mysqli_escape_string($product_sub_group_code)."'";
						$sql .= " , product_brand_code='".mysqli_escape_string($product_brand_code)."'";
						$sql .= " , cl_stk='".mysqli_escape_string($cl_stk)."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						if(mysqli_query($link,$sql))
						{
							$sqltemp  = "insert into product_master_temp ";
							$sqltemp .= " SET prod_code='".$max_prod_code."'";
							$sqltemp .= " , prod_desc='".addslashes($prod_desc)."'";
							$sqltemp .= " , product_group_code='".mysqli_escape_string($product_group_code)."'";
							$sqltemp .= " , product_sub_group_code='".mysqli_escape_string($product_sub_group_code)."'";
							$sqltemp .= " , product_brand_code='".mysqli_escape_string($product_brand_code)."'";
							$sqltemp .= " , cl_stk='".mysqli_escape_string($cl_stk)."'";
							$sqltemp .= " , acedns='".$acedns."'";
							$sqltemp .= " , black_list='".$black_list."'";
							mysqli_query($link,$sqltemp) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on SKU Name column in sku master.csv.Please check.");
						}
					}
					else
					{
						$cl_stk_db=$rowskunamechk['cl_stk'];
						$acedns_db=$rowskunamechk['acedns'];
						$black_list_db=$rowskunamechk['black_list'];
						$prod_code_db=$rowskunamechk['prod_code_db'];
						
						if($cl_stk_db!=$cl_stk || $acedns_db!=$acedns || $black_list_db!=$black_list)
						{
							$sqltemp  = "insert into product_master_temp ";
							$sqltemp .= " SET prod_code='".$max_prod_code."'";
							$sqltemp .= " , prod_desc='".addslashes($prod_desc)."'";
							$sqltemp .= " , product_group_code='".mysqli_escape_string($product_group_code)."'";
							$sqltemp .= " , product_sub_group_code='".mysqli_escape_string($product_sub_group_code)."'";
							$sqltemp .= " , product_brand_code='".mysqli_escape_string($product_brand_code)."'";
							$sqltemp .= " , cl_stk='".mysqli_escape_string($cl_stk)."'";
							$sqltemp .= " , acedns='".$acedns."'";
							$sqltemp .= " , black_list='".$black_list."'";
							mysqli_query($link,$sqltemp) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on SKU Name column in sku master.csv.Please check.");
						}
						$sqlupdatestock="UPDATE product_master SET cl_stk='".$cl_stk."',
										acedns='".$acedns."',
										black_list='".$black_list."' WHERE prod_desc='".addslashes($prod_desc)."'";
						mysqli_query($link,$sqlupdatestock) or die(mysqli_error());
					}
				//}
					//$sku_code++;
				}
				 $rec_count++;
			}
			//Product group code checking start
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
					$errorgroupcodeproduct=$groupcodeproduct.' exists in SKU Master but not exists in Brand Master.';
					array_push($error_array,$errorgroupcodeproduct);
				}
			//Product group code checking end
			//Product sub group code checking start
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
					$errorsubgroupcodeproduct=$subgroupcodeproduct.' exists in SKU Master but not exists in Brand Form Master.';
					array_push($error_array,$errorsubgroupcodeproduct);
				}
			//Product sub group code checking end
			$successval=1;
		}
		else
		{
			echo $successval="Naming convention for SKU Master.csv is wrong.";
			exit();
		}	
		
		//For Mrp CSV
		if(similar_file_exists("../csv/$folderName/MRP.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/MRP.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			$lines=str_replace('"','~',$lines);
			$sqldelete="truncate mrp";
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
				  
					$prod_desc=trim($data[0]);
					$prod_desc=str_replace('~','"',$prod_desc);
					$mrp=trim($data[1]);
					$sale_rate=trim($data[2]);
					if(strpos($sale_rate,',')!=false){
						$salepos=strpos($sale_rate,',');
					$sale_rate = substr($sale_rate,0,$salepos).substr(strstr($sale_rate, ","),1);
					}
					
					$sqlskunamechk="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'";
					$rsskunamechk=mysqli_query($link,$sqlskunamechk);
					$countskunamechk=mysqli_num_rows($rsskunamechk);
					$rowskunamechk=mysqli_fetch_assoc($rsskunamechk);
					$prod_code=$rowskunamechk['prod_code'];
					
						$sqlmaxmrpcode="SELECT MAX(mrp_code) AS max_mrp_code FROM  mrp";
						$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
						$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
						$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
						
						if($max_mrp_code=='')
						{
							$max_mrp_code='m001';
						}
						else
						{
							$max_mrp_code++;
						}
					
						$sql  = "insert into mrp ";
						$sql .= " SET product_code='".$prod_code."'";
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , mrp='".mysqli_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysqli_escape_string($sale_rate)."'";
						mysqli_query($link,$sql);
				}
				 $rec_count++;
			}
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
					$errorprodcodeprice=$prodcodeprice.' exists in mrp but not exists in SKU Master.';
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

		//For Employee CSV
		if(similar_file_exists("../csv/$folderName/Employee Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Employee Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
			$lines = file($filename);
			/*$sqldelete="truncate employee_master";
			$rsdelete=mysqli_query($link,$sqldelete);
			$sqldeletepassword="truncate changepassword";
			$rsdeletepassword=mysqli_query($link,$sqldeletepassword);*/
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
					
					$employee_name=trim($data[0]);
					
					$sqlempnamechk="SELECT emp_name FROM employee_master WHERE emp_name='".trim($employee_name)."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$countempnamechk=mysqli_num_rows($rsempnamechk);
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

						$csv_row_count=$rec_count+1;
						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						$sql .= " , emp_name='".$employee_name."'";
						mysqli_query($link,$sql) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Employee name column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysqli_query($link,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		else
		{
			echo $successval="Naming convention for Employee Master.csv is wrong.";
			exit();
		}
		
		//For Customer CSV
		if(similar_file_exists("../csv/$folderName/Customer Master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Customer Master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			/*$sqldelete="truncate customer_master";
			$rsdelete=mysqli_query($link,$sqldelete);
			$sqlroutedelete="truncate route_master";
			$rsroutedelete=mysqli_query($link,$sqlroutedelete);*/
			$sqldeletetemp="truncate customer_master_temp";
			$rsdeletetemp=mysqli_query($link,$sqldeletetemp);
			
			/*$sqlcustomerchk="SELECT COUNT(*) FROM customer_master";
			$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
			$countcustomerchk=mysqli_num_rows($rscustomerchk);*/
			
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
					
					$customer_name	=trim($data[0]);
					$route_name	  =trim($data[1]); 
					$emp_name		=trim($data[2]);
					$acedns		  =trim($data[3]);
					$current_balance =trim($data[4]);
					$credit_limit	=trim($data[5]);
					$black_list	  =trim($data[6]); 
					
					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_name)."'";
					$rsempcode=mysqli_query($link,$sqlempcode);
					$rowempcode=mysqli_fetch_assoc($rsempcode);
					$emp_code=$rowempcode['emp_code'];

					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
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
						
						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");
					}
					
					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".trim($route_name)."' AND emp_code='".$emp_code."'";
					$rsroutecode=mysqli_query($link,$sqlroutecode);
					$rowroutecode=mysqli_fetch_assoc($rsroutecode);
					$route_code=$rowroutecode['route_code'];
					
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					
					$csv_row_count=$rec_count+1;
					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE 1";
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
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						if(mysqli_query($link,$sql))
						{
							$sqltemp  = "insert into customer_master_temp ";
							$sqltemp .= " SET customer_code='".$max_customer_code."'";
							$sqltemp .= " , customer_name='".addslashes($customer_name)."'";
							$sqltemp .= " , route_code='".$route_code."'";
							$sqltemp .= " , emp_code='".$emp_code."'";
							$sqltemp .= " , current_balance	='".$current_balance."'";
							$sqltemp .= " , credit_limit='".$credit_limit."'";
							$sqltemp .= " , acedns='".$acedns."'";
							$sqltemp .= " , black_list='".$black_list."'";
							mysqli_query($link,$sqltemp) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee name columns in customer master.csv.Please check.");
						}
					}
					else
					{
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$emp_code_db=$rowcustomernamechk['emp_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						
						if($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $credit_limit_db!=$credit_limit || $acedns_db!=$acedns || $black_list_db!=$black_list)
						{
							$sqltemp  = "insert into customer_master_temp ";
							$sqltemp .= " SET customer_code='".$customer_code_db."'";
							$sqltemp .= " , customer_name='".addslashes($customer_name)."'";
							$sqltemp .= " , route_code='".$route_code."'";
							$sqltemp .= " , emp_code='".$emp_code."'";
							$sqltemp .= " , current_balance	='".$current_balance."'";
							$sqltemp .= " , credit_limit='".$credit_limit."'";
							$sqltemp .= " , acedns='".$acedns."'";
							$sqltemp .= " , black_list='".$black_list."'";
							mysqli_query($link,$sqltemp) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee name columns in customer master.csv.Please check.");
						}
						
						$sqlupdated  = "update customer_master ";
						$sqlupdated .= " SET route_code='".$route_code."'";
						$sqlupdated .= " , current_balance	='".$current_balance."'";
						$sqlupdated .= " , credit_limit='".$credit_limit."'";
						$sqlupdated .= " , acedns='".$acedns."'";
						$sqlupdated .= " , black_list='".$black_list."' WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
						mysqli_query($link,$sqlupdated) or die(mysqli_error());
					}
					//}
				}
				 $rec_count++;
				 $countroute++;
			}
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
					$errorempcoderetail=$empcoderetail.' exists in customer_master but not exists in employee_master.';
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
					$errorroutecoderetail=$routecoderetail.' exists in customer_master but not exists in route_master.';
					array_push($error_array,$errorroutecoderetail);
				}
			//Route code checking end
		
			$successval=1;
		}
		else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}
		
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
					
					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
	
					$invoice_id=trim($data[1]);
					$date=trim($data[2]);
					$dateArr=explode('/',$date);
					$finaldate=$dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
					$invoice_amount=trim($data[3]);
					if(strpos($invoice_amount,',')!=false){
						$invoicepos=strpos($invoice_amount,',');
					$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);
					}
					$due_amount=trim($data[4]);
					if(strpos($due_amount,',')!=false){
					$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);
					}
	
					$sql  = "insert into outstanding ";
					$sql .= " SET customer_code='".mysqli_escape_string($customer_code)."'";
					$sql .= " , invoice_id='".mysqli_escape_string($invoice_id)."'";
					$sql .= " , date='".mysqli_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysqli_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysqli_escape_string($due_amount)."'";
					
					mysqli_query($link,$sql) or die(mysqli_error());
				}
				 $rec_count++;
			}
			//Customer code checking start
				$sqlcustomercodeoutstanding="SELECT customer_code FROM outstanding WHERE customer_code NOT IN(SELECT customer_code FROM customer_master)";
				$rscustomercodeoutstanding=mysqli_query($link,$sqlcustomercodeoutstanding);
				$cntcustomercodeoutstanding=mysqli_num_rows($rscustomercodeoutstanding);
				if($cntcustomercodeoutstanding>0)
				{
					$customercodeoutstanding='';
					while($rowcustomercodeoutstanding=mysqli_fetch_assoc($rscustomercodeoutstanding))
					{
						$customercodeoutstanding=$customercodeoutstanding.$rowcustomercodeoutstanding['customer_code'].',';
					}
					$customercodeoutstanding=substr($customercodeoutstanding,0,-1);
					$errorcustomercodeoutstanding=$customercodeoutstanding.' exists in outstanding but not exists in customer_master.';
					array_push($error_array,$errorcustomercodeoutstanding);
				}
			//Customer code checking end		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Outstanding.csv is wrong.";
			exit();
		}*/
		
		if($successval==1)
		{
			$nick_name=$_SESSION['nick_name'];
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
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
				disphtml("main();");
			}
			else 
			{
				$GLOBALS['msg'] = "Problem in mail sending.";
				disphtml("main();");
			}
			//echo $err = 'Zip file extracted and data has been uploaded successfully';
		}
		else 
		{
			$GLOBALS['msg'] = "Problem with uploading Zip file";
			disphtml("main();");
		}
	} 
?>