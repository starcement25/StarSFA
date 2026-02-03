<?php	
set_time_limit(1000);

error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
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
}
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
	//For Unzip a zip file
	$folderName = $_REQUEST['nick_name'];
	$error_array=array();
	if ( !file_exists("csv/$folderName")){
		mkdir("csv/$folderName");
		chmod("csv/$folderName", 0777);
	}
	$upload_dir="csv/$folderName/";
	if(file_exists($_FILES['zip_file']['tmp_name']))
	{
		$file_name = $_FILES['zip_file']['name'];
		$tmp_name=$_FILES['zip_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		
		 move_uploaded_file($tmp_name,$upload_file);
		$zip = new ZipArchive;
		if ($zip->open($upload_file)) {
			
			
			$zip->extractTo("csv/$folderName/");
			$zip->close();
		} 
	 }
 	//For order details CSV in RKBK
	/*if(similar_file_exists("csv/$folderName/order_details.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/order_details.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		$not_exists_array=array();
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
			  
				$order_no=trim($data[0]);
				$sku_name=trim($data[1]);
				$qty=trim($data[2]);
				$rate=trim($data[3]);
				
				$sqlbranchnamechk="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($sku_name)."'";
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$countprodcode=mysqli_num_rows($rsbranchnamechk);
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$prod_code=$rowbranchnamechk['prod_code'];
				
				$sqlvendor  = "insert into order_details SET ";
				$sqlvendor .= "  order_no='".mysqli_real_escape_string($order_no)."'";
				$sqlvendor .= "  ,sku_code='".mysqli_real_escape_string($prod_code)."'";
				$sqlvendor .= " , qty='".mysqli_real_escape_string($qty)."'";
				$sqlvendor .= " , sale_rate='".mysqli_real_escape_string($rate)."'";
				mysqli_query($link,$sqlvendor) or array_push($error_array,"mysqli_error().Internal error in order_details.csv.Please check.");
				
				if($countprodcode<1)
				{
					array_push($not_exists_array,$sku_name);
				}
			}
			 $rec_count++;
		}		
		$successval=1;
		print_r($not_exists_array);
	}

	//For Company Master CSV
	if(similar_file_exists("csv/$folderName/Company master.csv")!=false)
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
				$branch_email_id=trim($data[5]);
				$branch_accounts_email_id=trim($data[6]);
				$alternative_email_id=trim($data[7]);
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."' 
									AND branch_location='".$branch_location."'";
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
					$sqlbranch .= " , comp_code='".mysqli_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($alternative_email_id)."' WHERE branch_name='".addslashes($branch_name)."'";
				}
				mysqli_query($link,$sqlbranch) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Branch master.csv.Please check.");
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

	//For Category Master CSV
	if(similar_file_exists("csv/$folderName/Brand Master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Brand Master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			/*$sqldelete="truncate product_group_master";
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
				  	$dns_product_group_code=trim($data[0]);
					$product_group_code=trim($data[1]);
					$product_group_name=trim($data[1]);
					$vertical_value=trim($data[2]);
					
					$csv_row_count=$rec_count+1;
					$sqlprodgroupnamechk="SELECT product_group_name FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";
					$rsprodgroupnamechk=mysqli_query($link,$sqlprodgroupnamechk);
					$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);
					if($countprodgroupnamechk<1){
						$sqlbrand  = "insert into product_group_master SET ";
						$sqlbrand .= "  product_group_code='".mysqli_real_escape_string($product_group_code)."'";
						$sqlbrand .= " ,dns_product_group_code='".mysqli_real_escape_string($dns_product_group_code)."'";
						$sqlbrand .= " , product_group_name='".mysqli_real_escape_string($product_group_name)."'";
						$sqlbrand .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
						$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrand) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand name column in Brand Master.csv.Please check.");
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		
	//For Sub Category Master CSV
	if(similar_file_exists("csv/$folderName/Brand Form Master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Brand Form Master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			/*$sqldelete="truncate product_sub_group_master";
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
				  	$dns_product_sub_group_code=trim($data[0]);
					$product_sub_group_code=trim($data[1]);
					$product_sub_group_name=trim($data[1]);
					$product_group_code=trim($data[2]);
					$vertical_value=trim($data[3]);
					
					$csv_row_count=$rec_count+1;
					$sqlprodsubgroupnamechk="SELECT product_sub_group_name,product_group_code FROM product_sub_group_master 
											WHERE product_sub_group_name='".addslashes($product_sub_group_name)."' AND product_group_code='".$product_group_code."'";
					$rsprodsubgroupnamechk=mysqli_query($link,$sqlprodsubgroupnamechk);
					$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);
					
					if($countprodsubgroupnamechk<1){
						$sqlbrandform  = "insert into product_sub_group_master SET ";
						$sqlbrandform .= "  product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
						$sqlbrandform .= " ,dns_product_sub_group_code='".mysqli_real_escape_string($dns_product_sub_group_code)."'";
						$sqlbrandform .= " , product_sub_group_name='".mysqli_real_escape_string($product_sub_group_name)."'";
						$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
						$sqlbrandform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
						$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrandform) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand code and Brand form code columns in Brand Form Master.csv.Please check.");
					}
					else
					{
						$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);
						$product_group_name_existing=$rowprodsubgroupnamechk['product_group_code'];
						$vertical_value_existing=$rowprodsubgroupnamechk['vertical_value'];
						if($product_group_name_existing!=$product_group_name || $vertical_value_existing!=$vertical_value)
						{
							$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";
							$sqlupdatebrandform .= " product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sqlupdatebrandform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
							$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP()";
							$sqlupdatebrandform .= "  WHERE product_sub_group_name='".addslashes($product_sub_group_name)."'";
							mysqli_query($link,$sqlupdatebrandform) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Brand Form Master.csv.Please check.");
						}
					}
				}
				 $rec_count++;
			}
			//Product group code checking start
				$sqlgroupcodesub_group="SELECT product_group_code FROM product_sub_group_master WHERE product_group_code NOT IN(SELECT product_group_code FROM product_group_master)";
				$rsgroupcodesub_group=mysqli_query($link,$sqlgroupcodesub_group) or die(mysqli_error());
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
		
	//For Sub Category Master CSV
	if(similar_file_exists("csv/$folderName/Brand Sub Form Master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Brand Sub Form Master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			/*$sqldelete="truncate product_sub_group_master";
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
				  	$dns_brand_code=trim($data[0]);
					$product_brand_code=trim($data[1]);
					$product_sub_group_code=trim($data[2]);
					$vertical_value=trim($data[3]);
					
					$csv_row_count=$rec_count+1;
					/*$sqlprodsubgroupnamechk="SELECT product_sub_group_name,product_group_code FROM product_sub_group_master 
											WHERE product_sub_group_name='".addslashes($product_sub_group_name)."'";
					$rsprodsubgroupnamechk=mysqli_query($link,$sqlprodsubgroupnamechk);
					$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);
					
					if($countprodsubgroupnamechk<1){*/
						$sqlbrandsubform  = "insert into product_brand_master SET ";
						$sqlbrandsubform .= "  product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";
						$sqlbrandsubform .= " ,	dns_product_brand_code='".mysqli_real_escape_string($dns_brand_code)."'";
						$sqlbrandsubform .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";
						$sqlbrandsubform .= " , product_brand_name='".mysqli_real_escape_string($product_brand_code)."'";
						$sqlbrandsubform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
						$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrandsubform) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand code and Brand form code columns in Brand Sub Form Master.csv.Please check.");
						//exit();
					/*}
					else
					{
						$rowprodsubgroupnamechk=mysqli_fetch_assoc($rsprodsubgroupnamechk);
						$product_group_name_existing=$rowprodsubgroupnamechk['product_group_code'];
						$vertical_value_existing=$rowprodsubgroupnamechk['vertical_value'];
						if($product_group_name_existing!=$product_group_name || $vertical_value_existing!=$vertical_value)
						{
							$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";
							$sqlupdatebrandform .= " product_group_code='".mysqli_real_escape_string($product_group_code)."'";
							$sqlupdatebrandform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";
							$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP()";
							$sqlupdatebrandform .= "  WHERE product_sub_group_name='".addslashes($product_sub_group_name)."'";
							mysqli_query($link,$sqlupdatebrandform) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Brand Form Master.csv.Please check.");
						}
					}*/
				}
				 $rec_count++;
			}
			$successval=1;
		}

		if(similar_file_exists("csv/$folderName/Sku master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$folderName/Sku master.csv");
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

					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
						if(no_of_filter > 1){
							$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE dns_product_group_code='".$product_group_code_name."'";
							$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);
							$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);
							$product_group_code=$rowproductgroupcode['product_group_code'];
						}
						if(no_of_filter > 2){
							$sqlproductsubgroupcode="SELECT product_sub_group_code FROM product_sub_group_master WHERE dns_product_sub_group_code='".$product_sub_group_code_name."'";
							$rsproductsubgroupcode=mysqli_query($link,$sqlproductsubgroupcode);
							$rowproductsubgroupcode=mysqli_fetch_assoc($rsproductsubgroupcode);
							$product_sub_group_code=$rowproductsubgroupcode['product_sub_group_code'];
						}
						if(no_of_filter > 3){
							$sqlproductbrandcode="SELECT product_brand_code FROM product_brand_master WHERE dns_product_brand_code='".$product_brand_code_name."'";
							$rsproductbrandcode=mysqli_query($link,$sqlproductbrandcode);
							$rowproductbrandcode=mysqli_fetch_assoc($rsproductbrandcode);
							$product_brand_code=$rowproductbrandcode['product_brand_code'];
						}
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
					}
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];
					$product_group_code=$product_group_code_name;
					$product_sub_group_code=$product_sub_group_code_name;
					$product_brand_code=$product_brand_code_name;
					
					if(branch_wise_product=='yes')
					{
						$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".$prod_desc."' AND branch_code='".$branch_code."' AND  
										dns_prod_code='".$dns_prod_code."'AND product_group_code='".$product_group_code."' 
										AND product_sub_group_code='".$product_sub_group_code."' AND product_brand_code='".$product_brand_code."'";
					}
					else
					{
						$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".$prod_desc."' 
										AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
										AND product_brand_code='".$product_brand_code."'";
					}
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
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
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
						
						if(($cl_stk_db==$cl_stk) && ($acedns_db!=$acedns || $black_list_db!=$black_list 
							|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 
							|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code))
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
							$sql .= " , download_time=CURRENT_TIMESTAMP()";
							$sql .= " , vertical_value='".$vertical_value."' WHERE prod_code='".$prod_code_db."'";
							mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
						}
						else if($cl_stk_db!=$cl_stk)
						{
							$sql  = "UPDATE product_master ";
							$sql .= " SET cl_stk='".$cl_stk."' WHERE prod_code='".$prod_code_db."'";
							mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");

						}
				}
			}
		$rec_count++;
	}
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
						$errorgroupcodeproduct=$groupcodeproduct.' exists in Sku master but not exists in Brand Master.';
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
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for SKU Master.csv is wrong.";
			exit();
		}*/
		
	
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
					$HQ=trim($data[8]);
					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
					}
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

					$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";
					$rsempnamechk=mysqli_query($link,$sqlempnamechk);
					$countempnamechk=mysqli_num_rows($rsempnamechk);
					
					$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')";
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
						$sql .= " , email='".$email."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , sale_access='".$sale_access."'";
						$sql .= " , HQ='".$HQ."'";
						mysqli_query($link,$sql) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysqli_query($link,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");
					}
					else
					{
						$sqlupdate  = "UPDATE employee_master ";
						$sqlupdate .= " SET branch_code='".$branch_code."'";
						$sqlupdate .= " , vertical_value='".$vertical_value."'";
						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";
						$sqlupdate .= " , email='".$email."'";
						$sqlupdate .= " , sale_access='".$sale_access."'";
						$sqlupdate .= " , HQ='".$HQ."'";
						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_name='".addslashes($employee_name)."'";
						mysqli_query($link,$sqlupdate) or  array_push($error_array,"mysqli_error().Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");
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
		//For RDS CSV
	if(similar_file_exists("csv/$folderName/RDS MASTER.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/RDS MASTER.csv");
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
				
				if(providing_code=='yes'){
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
						$max_rds_code='C/0005718';
					}
					else
					{
						$max_rds_code++;
					}

				
					$sqlrds  = "insert into rds_master ";
					$sqlrds .= " SET rds_code='".$max_rds_code."'";
					$sqlrds .= " ,rds_name='".$rdsname."'";
					$sqlrds .= " , emp_code='".$emp_code."'";
					$sqlrds .= " , rds_type='".$rds_type."'";
					$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
				
					mysqli_query($link,$sqlrds) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee name columns in rds master.csv.Please check.");;
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
					$acedns		  =trim($data[6]);
					$credit_limit	=trim($data[7]);
					$credit_days	 =trim($data[8]);
					$current_balance =trim($data[9]);
					$black_list	  =trim($data[10]); 
					$TD	  		  =trim($data[11]);
					$branch_code_name =trim($data[12]);
					$customer_type   =trim($data[13]);
					$rds_tag   =trim($data[14]);
					
					$csv_row_count=$rec_count+1;
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
						$rsbranchcode=mysqli_query($link,$sqlbranchcode);
						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
						$branch_code=$rowbranchcode['branch_code'];
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
						$rsbranchcode=mysqli_query($link,$sqlbranchcode);
						$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
						$branch_code=$rowbranchcode['branch_code'];
					}
					//$emp_code=$emp_code_name;
					//Creation of code random no parameter
					$random_no_length=7-strlen($folderName);//7 is the maximum length of the company nick name
					$foldernamerand=$folderName.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);

					//------------------------------------------For route creation---------------------------------------------------------------------
					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
					$rsroutechk=mysqli_query($link,$sqlroutechk);
					$countroutechk=mysqli_num_rows($rsroutechk);
					if($countroutechk<1 && $route_name!='')
					{
						//Creation of route code parameter
						$route_prefix="RT";
						$firt_character_route=strtoupper(substr($route_name,0,1));
						$route_code_left_part=$route_prefix.$foldernamerand.$branch_code.$firt_character_route;
						
						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, (length('".$route_code_left_part."')+1), (length( route_code ) -length('".$route_code_left_part."'))) AS UNSIGNED )) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
						$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
						$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
						$new_route_code=$rowmaxroutecode['new_route_code'];
						
						if($new_route_code=='')
						{
							$max_route_code=$route_code_left_part.'00001';
						}
						else
						{
							$max_route_code=$route_code_left_part.str_pad($new_route_code+1, 5, 0, STR_PAD_LEFT);
							//$max_route_code++;
						}

						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , vertical_value='".$vertical_value."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlroute) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");
						$route_code=$max_route_code;
					}
					else
					{
						$rowroutechk=mysqli_fetch_assoc($rsroutechk);
						$route_code=$rowroutechk['route_code'];
					}
					//-----------------------------------------------------------End of route creation--------------------------------------------------
					
					//-----------------------------------------------------------For rds creation-------------------------------------------------------
					if($customer_type=='D')
					{
						$sqlrdscode="SELECT * FROM rds_master WHERE rds_name='".addslashes($customer_name)."'";
						$rsrdscode=mysqli_query($link,$sqlrdscode);
						$countrdscode=mysqli_num_rows($rsrdscode);
						if($countrdsnamechk<1)
						{
							//Creation of rds code parameter
							$rds_prefix="RD";
							$firt_character_rds=strtoupper(substr($customer_name,0,1));
							$rds_code_left_part=$rds_prefix.$foldernamerand.$branch_code.$firt_character_rds;

							
							$sqlmaxrdscode="SELECT MAX( CAST( SUBSTRING( customer_code, (length('".$rds_code_left_part."')+1), (length( customer_code ) -length('".$rds_code_left_part."'))) AS UNSIGNED )) AS new_rds_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
							$rsmaxrdscode=mysqli_query($link,$sqlmaxrdscode);
							$rowmaxrdscode=mysqli_fetch_assoc($rsmaxrdscode);
							$new_rds_code=$rowmaxrdscode['new_rds_code'];
							
							if($new_rds_code=='')
							{
								$max_rds_code=$rds_code_left_part.'00001';
							}
							else
							{
								$max_rds_code=$rds_code_left_part.str_pad($new_rds_code+1, 5, 0, STR_PAD_LEFT);
							}
			
							$sqlrds  = "insert into rds_master ";
							$sqlrds .= " SET rds_code='".$max_rds_code."'";
							$sqlrds .= " ,rds_name='".$customer_name."'";
							$sqlrds .= " , rds_type='D'";
							$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
						
							mysqli_query($link,$sqlrds) or array_push($error_array,"mysqli_error().Internal DATA execution problem on distributor table.PLease contact aceDNS admin.");
						}
					}
					//-----------------------------------------------------------End of rds creation----------------------------------------------------
					
					//---------------------------------------------------------For customer creation and updation---------------------------------------
					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
					if($countcustomernamechk<1)
					{
						if($customer_type=='D')
						{
							$preifix_string='RD';
						}
						else
						{
							$preifix_string='CM';
						}
						//Creation of customer code parameter
						$customer_prefix=$preifix_string;
						$firt_character_customer=strtoupper(substr($customer_name,0,1));
						$customer_code_left_part=$customer_prefix.$foldernamerand.$branch_code.$firt_character_customer;

						$sqlmaxcustomercode="SELECT MAX( CAST( SUBSTRING( customer_code, (length('".$customer_code_left_part."')+1), (length( customer_code ) -length('".$customer_code_left_part."'))) AS UNSIGNED )) AS new_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
						$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
						$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
						$new_customer_code=$rowmaxcustomercode['new_customer_code'];
						
						if($new_customer_code=='')
						{
							$max_customer_code=$customer_code_left_part.'00001';
						}
						else
						{
							$max_customer_code=$customer_code_left_part.str_pad($new_customer_code+1, 5, 0, STR_PAD_LEFT);
						}
						$sql  = "insert into customer_master ";
						$sql .= " SET customer_code='".$max_customer_code."'";
						$sql .= " , dns_customer_code='".$dns_customer_code."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , TD='".$TD."'";
						$sql .= " , cust_type='".$customer_type."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						//exit();
						mysqli_query($link,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
						$customer_code=$max_customer_code;
					}
					else
					{
						$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$TD_db=$rowcustomernamechk['TD'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$branch_code_db=$rowcustomernamechk['branch_code'];
						
						if(($credit_limit_db==$credit_limit) && ($current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD  || $route_code_db!=$route_code || $branch_code_db!=$branch_code))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET current_balance	='".$current_balance."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , branch_code='".$branch_code."'";
							$sqlupdated .= " , TD='".$TD."'";
							$sqlupdated .= " , cust_type='".$customer_type."'";
							$sqlupdated .= " , route_code='".$route_code."'";
							$sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."'";
							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
						elseif(($credit_limit_db!=$credit_limit))
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET credit_limit='".$credit_limit."'";
							$sqlupdated .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."'";
							mysqli_query($link,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
						/*elseif($credit_limit_db!=$credit_limit && $route_code_db==$route_code && $emp_code_db==$emp_code && $current_balance_db==$current_balance && $acedns_db==$acedns && $black_list_db==$black_list || $vertical_value_db!=$vertical_value || $TD_db!=$TD)
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , credit_limit='".$credit_limit."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , black_list='".$black_list."' WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."'";
							mysqli_query($link,$sqlupdatestock) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}*/
						$customer_code=$customer_code_db;
					}
					//---------------------------------------------------------End of customer creation and updation---------------------------------------
					
					//-----------------------------------------------------For customer route emp relation creation-----------------------------------------
					$sqlrdstagcode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_tag)."'";
					$rsrdstagcode=mysqli_query($link,$sqlrdstagcode);
					$rowrdstagcode=mysqli_fetch_assoc($rsrdstagcode);
					$rds_tag_code=$rowrdstagcode['rds_code'];

					$emp_code_name_array=explode(';',$emp_code_name);
					if(providing_code=='yes'){
						$query_condition="dns_emp_code";
					}
					else
					{
						$query_condition="emp_name";
					}
					for($cntemp=0;$cntemp< count($emp_code_name_array);$cntemp++)
					{
						$sqlempcode="SELECT emp_code FROM employee_master WHERE ".$query_condition."='".addslashes($emp_code_name_array[$cntemp])."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];

						$sqlcustomeremprealtion="SELECT * FROM customer_route_emp_relation WHERE customer_code='".$customer_code."' AND 
												route_code='".$route_code."' AND emp_code='".$emp_code."'";
						$rscustomeremprealtion=mysqli_query($link,$sqlcustomeremprealtion);
						$cntcustomeremprealtion=mysqli_num_rows($rscustomeremprealtion);
						
						if($cntcustomeremprealtion <1)
						{
							$sqlinsertcustomerrelation = "insert into customer_route_emp_relation ";
							$sqlinsertcustomerrelation .= " SET customer_code='".$customer_code."'";
							$sqlinsertcustomerrelation .= " ,route_code='".$route_code."'";
							$sqlinsertcustomerrelation .= " , emp_code='".$emp_code."'";
							$sqlinsertcustomerrelation .= " , rds_tag='".$rds_tag_code."'";
							mysqli_query($link,$sqlinsertcustomerrelation) or array_push($error_array,"mysqli_error().Internal DATA execution problem on customer relational table.PLease contact aceDNS admin.");

						}
					}
					
				   //-----------------------------------------------------End of customer route emp relation creation-----------------------------------------

				}
				 $rec_count++;
			}
			//exit();
			//Emp code checking start
				$sqlempcoderetail="SELECT emp_code FROM customer_route_emp_relation WHERE emp_code NOT IN(SELECT emp_code FROM employee_master)";
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
				$sqlroutecoderetail="SELECT route_code FROM customer_route_emp_relation WHERE route_code NOT IN(SELECT route_code FROM route_master)";
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
		/*else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}*/
		
	//For Vendor Master CSV
	if(similar_file_exists("csv/$folderName/Vendor master.csv")!=false)
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
	
					if(providing_code=='yes'){
						/*$sqlroutecode="SELECT route_code FROM route_master WHERE dns_route_code='".$route_code_name."'";
						$rsroutecode=mysqli_query($link,$sqlroutecode);
						$rowroutecode=mysqli_fetch_assoc($rsroutecode);
						$route_code=$rowroutecode['route_code'];
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."' AND route_code='".$route_code."'";*/
						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";
						$rsempcode=mysqli_query($link,$sqlempcode);
						$rowempcode=mysqli_fetch_assoc($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."' AND emp_code='".$emp_code."' ";
					}
					else
					{
						/*$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_code_name."'";
						$rsroutecode=mysqli_query($link,$sqlroutecode);
						$rowroutecode=mysqli_fetch_assoc($rsroutecode);
						$route_code=$rowroutecode['route_code'];
						//$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' AND route_code='".$route_code."'";
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' AND route_code='".$route_code."'";*/
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";
					}
					$rscustomercode=mysqli_query($link,$sqlcustomercode);
					$countcustomercode=mysqli_num_rows($rscustomercode);
					$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					
					if($countcustomercode <1 && !in_array($customer_code_name,$customeroutstandingmissmatchArr))
					{
						$customeroutstandingmissmatch='';
						$customeroutstandingmissmatch.=$customer_code_name.',';
						array_push($customeroutstandingmissmatchArr,$customer_code_name);
						//$lineexcel .= $customeroutstandingmissmatch."\n";
					}

					$sql  = "insert into outstanding ";
					$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";
					$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";
					$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";
					$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";
					
					mysqli_query($link,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");;
				}
				 $rec_count++;
			}
			//exit();
			//Customer code checking start
				//print_r($customeroutstandingmissmatchArr);
				$customeroutstandingmissmatch=substr($customeroutstandingmissmatch,0,-1);
				$errorcustomeroutstanding=$customeroutstandingmissmatch.' exists in outstanding but not exists in customer_master.';
				array_push($error_array,$errorcustomeroutstanding);
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
					$vertical_value=trim($data[5]);
					
					if(providing_code=='yes'){
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
					$branch_code=$rowbranchcode['branch_code'];
					if(providing_code=='yes'){
						if(branch_wise_product=='yes')
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";
						}
						else
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";
						}
					}
					else
					{
						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";

					}
					$rsprodcode=mysqli_query($link,$sqlprodcode);
					$rowprodcode=mysqli_fetch_assoc($rsprodcode);
					$prod_code=$rowprodcode['prod_code'];
					
					$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."'";
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
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sql)  or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");
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
							mysqli_query($link,$sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
						}
					}
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
			
	if($successval==1)
	{
			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlInsert))
			{
				$nick_name=$_REQUEST['nick_name'];
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				$mailto='kuntald@coral.in';
			
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
					/*$error_msgArr=explode('#',$GLOBALS['error_msg']);
						if(count($error_msgArr)>0){
							for($i=0;$i<count($error_msgArr);$i++){
								echo "<b>$error_msgArr[$i]</b><br /><br />";
							}
						}*/
					main();
				}
				else
				{
					echo $GLOBALS['msg'] = "Error in mail sending.";
					main();
				}
				
				//echo $err = 'Zip file extracted and data has been uploaded successfully';
			}
			else 
			{
				echo $GLOBALS['msg'] = "Problem with uploading Zip file";
				main();
			}
	}
}
?>