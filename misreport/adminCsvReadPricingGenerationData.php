<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	require("adminUtils.php");
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
	else    										   disphtml("main();");
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
function csv_upload(){
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
	//For Basic freight CSV
	if(similar_file_exists("../csv/$folderName/Basic freight.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Basic freight.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
					
					$dns_branch_code=trim($data[0]);
					$truck_load=trim($data[1]);
					$plant_name=trim($data[2]);
					$hire_cost=trim($data[3]);
					
					$sqlbranchcode="SELECT branch_code,plant_name FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];
					$plant_name=$rowbranchcode['plant_name'];
					
				   /* $sqlcheckvalue="SELECT hire_cost FROM basic_freight WHERE branch_code='".$branch_code."' AND hire_cost='".$hire_cost."'";
					$rscheckvalue=mysqli_query($link,$sqlcheckvalue);
					$countcheckvalue=mysqli_num_rows($rscheckvalue);
					if($countcheckvalue <1)
					{*/				
						$sqlinsertbasicfreight="INSERT INTO basic_freight 
											  SET branch_code='".$branch_code."',
											  plant_name='".$plant_name."',
											  hire_cost='".$hire_cost."',
											  truck_load='".$truck_load."',
											  datetime=CURRENT_TIMESTAMP";
						mysqli_query($link,$sqlinsertbasicfreight) or array_push($error_array,"mysqli_error().Error Found @row $csv_row_count in Basic freight.csv.Please check.");
;
					/*}
					else
					{
						$sqlupdatebasicfreight="UPDATE basic_freight 
												SET datetime=CURRENT_TIMESTAMP WHERE branch_code='".$branch_code."' AND hire_cost='".$hire_cost."'";
						mysqli_query($link,$sqlupdatebasicfreight)or array_push($error_array,"mysqli_error().Error Found @row $csv_row_count in Basic freight.csv.Please check.");
					}*/
				}
				 $rec_count++;
		}
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Basic freight.csv is wrong.";
		exit();
	}*/
    //For Depot Cost CSV
	if(similar_file_exists("../csv/$folderName/Depot_Freight cost.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Depot_Freight cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
					
					$dns_branch_code=trim($data[0]);
					$dns_prod_code=trim($data[1]);
					$depot_cost=trim($data[2]);
					$freight=trim($data[3]);
					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsbranchcode=mysqli_query($link,$sqlbranchcode);
					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];
								
					/*$sqldepotcostchk="SELECT depot_cost FROM depot_freight_cost WHERE dns_prod_code='".$dns_prod_code."' AND branch_code='".$branch_code."' 
									AND depot_cost='".$depot_cost."' AND freight='".$freight."'";
					$rsdepotcostchk=mysqli_query($link,$sqldepotcostchk);
					$countdepotcostchk=mysqli_num_rows($rsdepotcostchk);
					if($countdepotcostchk <1)
					{*/
					 $sqlinsertdepotcost="INSERT INTO depot_freight_cost
											  SET dns_prod_code='".$dns_prod_code."',
											  branch_code='".$branch_code."',
											  depot_cost='".$depot_cost."',
											  freight='".$freight."',
											  datetime=CURRENT_TIMESTAMP";
					   mysqli_query($link,$sqlinsertdepotcost) or array_push($error_array,"mysqli_error().Error Found @row $csv_row_count in Depot Cost.csv.Please check.");
					/*}
					else
					{
						$sqlupdatedepotcost="UPDATE depot_freight_cost
											  SET datetime=CURRENT_TIMESTAMP WHERE dns_prod_code='".$dns_prod_code."' AND depot_cost='".$depot_cost."' 
											  AND branch_code='".$branch_code."' AND freight='".$freight."'";
						mysqli_query($link,$sqlupdatedepotcost) or array_push($error_array,"mysqli_error().Error Found @row $csv_row_count in Depot Cost.csv.Please check.");
					}*/
				}
					$rec_count++;
			 }
					$successval=1;
		 }
		/*else
		{
			echo $successval="Naming convention for Depot_Freight cost.csv is wrong.";
			exit();
		}*/
		//For Load distribution csv
	   if(similar_file_exists("../csv/$folderName/Load distribution.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/Load distribution.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
				
				$dns_prod_code=trim($data[0]);
				$truck_load=trim($data[1]);
				$qty_truck_load=trim($data[2]);
								
				/*$sqlcheckvalue="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$dns_prod_code."'  AND qty_truck_load='".$qty_truck_load."'";
				$rscheckvalue=mysqli_query($link,$sqlcheckvalue);
				$countcheckvalue=mysqli_num_rows($rscheckvalue);
				if($countcheckvalue <1)
				{*/	
					$sqlinsertloaddistribution="INSERT INTO load_distribution SET 
												prod_code='".$dns_prod_code."',
												truck_load='".$truck_load."',
											    qty_truck_load='".$qty_truck_load."',
											    datetime=CURRENT_TIMESTAMP";
					mysqli_query($link,$sqlinsertloaddistribution) or array_push($error_array,"mysqli_error().Error Found @row $csv_row_count in Truck load.csv.Please check.");
				/*}
				else
				{
					$sqlupdateloaddistribution="UPDATE load_distribution 
											  SET datetime=CURRENT_TIMESTAMP WHERE prod_code='".$dns_prod_code."' AND qty_truck_load='".$qty_truck_load."'";
					mysqli_query($link,$sqlupdateloaddistribution) or array_push($error_array,"mysqli_error().Error Found @row $csv_row_count in Truck load.csv.Please check.");
				 }*/
			  }
					$rec_count++;
			}
					$successval=1;
		 }
		/*else
		{
			echo $successval="Naming convention for Load distribution.csv is wrong.";
			exit();
		}*/
		//For Packing master csv
		if(similar_file_exists("../csv/$folderName/Packing master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Packing master.csv");
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
				  
					$dns_prod_code=trim($data[0]);
					$prod_desc=trim($data[1]);
					$packing_cost=trim($data[2]);
					$no_pc_one_case=trim($data[3]);
					$plant_name=trim($data[4]);
					$packing_realization=trim($data[5]);
					$csv_row_count=$rec_count+1;
					$current_date=date('Y-m-d');

					/*$sqlpackchk="SELECT dns_prod_code,prod_desc FROM packing_master WHERE dns_prod_code='".addslashes($dns_prod_code)."' AND prod_desc='".addslashes($prod_desc)."'";
					$rspackchk=mysqli_query($link,$sqlpackchk);
					$countpackchk=mysqli_num_rows($rspackchk);
					$rowpackchk=mysqli_fetch_assoc($rspackchk);
					
					if($countpackchk<1)
					{*/
						$sqlpacking  = "insert into packing_master SET ";
						$sqlpacking .= "  dns_prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
						$sqlpacking .= "  ,prod_desc='".mysqli_real_escape_string($prod_desc)."'";
						$sqlpacking .= " , packing_cost='".mysqli_real_escape_string($packing_cost)."'";
						$sqlpacking .= " , no_pc_one_case='".mysqli_real_escape_string($no_pc_one_case)."'";
						$sqlpacking .= " , plant_name='".mysqli_real_escape_string($plant_name)."'";
						$sqlpacking .= " , packing_realization='".mysqli_real_escape_string($packing_realization)."'";
						$sqlpacking .= " , datetime=CURRENT_TIMESTAMP";
						mysqli_query($link,$sqlpacking) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Packing master.csv.Please check.");
					/*}
					else
					{
						$dns_prod_code_db=$rowpackchk['dns_prod_code'];
						$prod_desc_db=$rowpackchk['prod_desc'];
						$sqlupdatepacking  = "UPDATE packing_master SET ";
						$sqlupdatepacking .= " packing_cost='".mysqli_real_escape_string($packing_cost)."'";
						$sqlupdatepacking .= " , datetime=CURRENT_TIMESTAMP";
						$sqlupdatepacking .= " WHERE dns_prod_code='".addslashes($dns_prod_code_db)."' AND prod_desc='".addslashes($prod_desc_db)."'";
						mysqli_query($link,$sqlupdatepacking) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Packing master.csv.Please check.");
					}*/
				    $sqlselectdistinctbranchcode="SELECT branch_code,prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND prod_desc 
												NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
					$rsselectdistinctbranchcode=mysqli_query($link,$sqlselectdistinctbranchcode);
					while($rowselectdistinctbranchcode=mysqli_fetch_assoc($rsselectdistinctbranchcode))
					{
						$branch_code=$rowselectdistinctbranchcode['branch_code'];
						$prod_code=$rowselectdistinctbranchcode['prod_code'];
						//generate_price_details($prod_code,$branch_code);
						
						$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$branch_code."'";
						$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
						$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
						$distinct_plant_name=$rowdistinctplant['plant_name'];
						
						$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$dns_prod_code."'";
						$rschkformulation=mysqli_query($link,$sqlchkformulation);
						$rowchkformulation=mysqli_fetch_assoc($rschkformulation);
						$is_formulation=$rowchkformulation['formulation'];
						${product_group_code.$dns_prod_code}=$rowchkformulation['product_group_code'];
						
						if($is_formulation=='yes')
						{
							$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
													SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
						}
						else
						{
							$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
													SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
						}
						if($cntchkpricegeneration > 0)
						{
							generate_price_details($dns_prod_code,$branch_code);
						}
					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Packing master.csv is wrong.";
			exit();	
		}*/
		

		//For Process cost csv
		if(similar_file_exists("../csv/$folderName/Process cost.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Process cost.csv");
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
				  
					$dns_prod_code=trim($data[0]);
					$process_cost=trim($data[1]);	
					$plant_name=trim($data[2]);				
					$csv_row_count=$rec_count+1;

						$sqlprocess  = "insert into process_cost SET ";
						$sqlprocess .= "  dns_prod_code='".mysqli_real_escape_string($dns_prod_code)."'";
						$sqlprocess .= " , process_cost='".mysqli_real_escape_string($process_cost)."'";
						$sqlprocess .= " , plant_name='".mysqli_real_escape_string($plant_name)."'";
						$sqlprocess .= " , datetime=CURRENT_TIMESTAMP";
						mysqli_query($link,$sqlprocess) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Process cost.csv.Please check.");
				    /*$sqlselectdistinctbranchcode="SELECT branch_code,prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND prod_desc 
												NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
					$rsselectdistinctbranchcode=mysqli_query($link,$sqlselectdistinctbranchcode);
					while($rowselectdistinctbranchcode=mysqli_fetch_assoc($rsselectdistinctbranchcode))
					{
						$branch_code=$rowselectdistinctbranchcode['branch_code'];
						$prod_code=$rowselectdistinctbranchcode['prod_code'];
						generate_price_details($prod_code,$branch_code);
					}*/
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Process cost.csv is wrong.";
			exit();	
		}*/
		
	 	//For Oilrate formulation csv
		if(similar_file_exists("../csv/$folderName/Oilrate formulation.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Oilrate formulation.csv");
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
				  
					$plant=trim($data[0]);
					$product_group=trim($data[1]);
					$prod_code=trim($data[2]);
					$oils=trim($data[3]);
					$formulation=trim($data[4]);					
					$csv_row_count=$rec_count+1;
					
					$sqlprodgroup="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$product_group."'";
					$rsprodgroup=mysqli_query($link,$sqlprodgroup);
					$rowprodgroup=mysqli_fetch_assoc($rsprodgroup);
					$product_group_code=$rowprodgroup['product_group_code'];
					
					$sqloilformulation  = "insert into loose_oilrate_formulation SET ";
					$sqloilformulation .= "  plant_name='".mysqli_real_escape_string($plant)."'";
					$sqloilformulation .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";
					$sqloilformulation .= " , prod_code='".mysqli_real_escape_string($prod_code)."'";
					$sqloilformulation .= " , oils='".mysqli_real_escape_string($oils)."'";
					$sqloilformulation .= " , formulation='".mysqli_real_escape_string($formulation)."'";
					$sqloilformulation .= " , datetime=CURRENT_TIMESTAMP";
					mysqli_query($link,$sqloilformulation) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in oilrate formulation.csv.Please check.");
				    /*$sqlselectdistinctbranchcode="SELECT branch_code,prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND prod_desc 
												NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
					$rsselectdistinctbranchcode=mysqli_query($link,$sqlselectdistinctbranchcode);
					while($rowselectdistinctbranchcode=mysqli_fetch_assoc($rsselectdistinctbranchcode))
					{
						$branch_code=$rowselectdistinctbranchcode['branch_code'];
						$prod_code=$rowselectdistinctbranchcode['prod_code'];
						generate_price_details($prod_code,$branch_code);
					}*/
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Process cost.csv is wrong.";
			exit();	
		}*/
		//For Depot Cost csv
	   if(similar_file_exists("../csv/$folderName/Depot cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/Depot cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$current_date=date('Y-m-d');
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
				
				$dns_branch_code=trim($data[0]);
				$product_group_name=trim($data[1]);
				$depot_cost=trim($data[2]);
				$vertical_value=trim($data[3]);
								
					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
					$countdistinctplant=mysqli_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						//echo "Please provide proper value for Depot code column in Depot cost.csv at row ".$csv_row_count;
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Depot code column in Depot cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 
											product_group_name='".$product_group_name."'";
					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);
					$countproductgroupcode=mysqli_num_rows($rsproductgroupcode);
					if($countproductgroupcode==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Oil group column in Depot cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];
					
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);
					$countverticalvalue=mysqli_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Vertical value column in Depot cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
												product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."'";
					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
						$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						
						$depot_cost_case_prodwise=$depot_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$depot_cost_case_prodwise=round(($depot_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
				
						$sqlinsertdeptcost="INSERT INTO depot_cost 
											SET dns_prod_code='".$distinct_dnsprod_code."',
											branch_code='".$distinct_branch_code."',
											depot_cost='".$depot_cost_case_prodwise."',
											vertical_value='".$vertical_value."',
											ip_address='".$_SERVER['REMOTE_ADDR']."',
											datetime=CURRENT_TIMESTAMP";
						if(mysqli_query($link,$sqlinsertdeptcost))
						{
							$sqlinsertdeptcostlog="INSERT INTO depot_cost_log 
												SET dns_prod_code='".$distinct_dnsprod_code."',
												branch_code='".$distinct_branch_code."',
												depot_cost='".$depot_cost."',
											 	vertical_value='".$vertical_value."',
											 	ip_address='".$_SERVER['REMOTE_ADDR']."',
											 	operation_type='UPLOAD',
											 	datetime=CURRENT_TIMESTAMP";
						    mysqli_query($link,$sqlinsertdeptcostlog);
						}
						if($is_formulation=='yes')
						{
							$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
						}
						else
						{
							$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
						}
						if($cntchkpricegeneration > 0)
						{
							generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
						}
					}
				}			  
			$rec_count++;
		   }
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Depot cost.csv is wrong.";
			exit();
		}*/
		//For Margin Cost csv
	   if(similar_file_exists("../csv/$folderName/margin cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/margin cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$current_date=date('Y-m-d');
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
				
				$plant_name=trim($data[0]);
				$dns_branch_code=trim($data[1]);
				$product_group_name=trim($data[2]);
				$pack_size=trim($data[3]);
				$margin_cost=trim($data[4]);
				$vertical_value=trim($data[5]);
								
					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
					$countdistinctplant=mysqli_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Depot code column in margin cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					if($distinct_plant_name==$plant_name){
					$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 
										product_group_name='".$product_group_name."'";
					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);
					$countproductgroupcode=mysqli_num_rows($rsproductgroupcode);
					if($countproductgroupcode==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Oil group column in margin cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);
					$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];
					
					$sqlpacksizechk="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	pack_size='".$pack_size."'";
					$rspacksizechk=mysqli_query($link,$sqlpacksizechk);
					$countpacksizechk=mysqli_num_rows($rspacksizechk);
					if($countpacksizechk==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Oil type column in margin cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);
					$countverticalvalue=mysqli_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Vertical value column in margin cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}

					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
												product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."' AND pack_size='".$pack_size."'";
					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
						$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						
						$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						$sqlinsertmargincost="INSERT INTO margin_cost 
											SET dns_prod_code='".$distinct_dnsprod_code."',
											branch_code='".$distinct_branch_code."',
											margin_cost='".$margin_cost_case_prodwise."',
											auth_one_limit='0',
											vertical_value='".$vertical_value."',
											ip_address='".$_SERVER['REMOTE_ADDR']."',
											datetime=CURRENT_TIMESTAMP";
						if(mysqli_query($link,$sqlinsertmargincost))
						{
							$sqlinsertmargincostlog="INSERT INTO margin_cost_log
											SET dns_prod_code='".$distinct_dnsprod_code."',
											branch_code='".$distinct_branch_code."',
											margin_cost='".$margin_cost."',
											auth_one_limit='0',
											vertical_value='".$vertical_value."',
											ip_address='".$_SERVER['REMOTE_ADDR']."',
											operation_type='UPLOAD',
											datetime=CURRENT_TIMESTAMP";
						    mysqli_query($link,$sqlinsertmargincostlog);
						}
						if($is_formulation=='yes')
						{
							$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
						}
						else
						{
							$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
						}
						if($cntchkpricegeneration > 0)
						{
							generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
						}
					}
				  }
				  else{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Plant name column in magin cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
				  }
				}			  
			$rec_count++;
		   }
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for margin cost.csv is wrong.";
			exit();
		}*/
	   //For Honey comb Cost csv
	   if(similar_file_exists("../csv/$folderName/honeycomb cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/honeycomb cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$current_date=date('Y-m-d');
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
				
				$plant_name=trim($data[0]);
				$dns_branch_code=trim($data[1]);
				$transport_mode=trim($data[2]);
				$product_group_name=trim($data[3]);
				$honeycomb_cost=trim($data[4]);
				$vertical_value=trim($data[5]);
								
					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
					$countdistinctplant=mysqli_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Depot code column in Honeycomb cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					
					$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";
					$rstransportmode=mysqli_query($link,$sqltransportmode);
					$counttransportmode=mysqli_num_rows($rstransportmode);
					if($counttransportmode==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Transport mode column in Honeycomb cost.csv at row ".
						$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					
					if($distinct_plant_name==$plant_name){
					$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE product_group_name='".$product_group_name."'";
					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);
					$countproductgroupcode=mysqli_num_rows($rsproductgroupcode);
					if($countproductgroupcode==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Oil group column in Honeycomb cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);
					$countverticalvalue=mysqli_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Vertical value column in Honeycomb cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}

					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
												product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."'";
					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
						$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						
						$honeycomb_cost_case_prodwise=$honeycomb_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$honeycomb_cost_case_prodwise=round(($honeycomb_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						$sqlinserthoneycombcost="INSERT INTO honeycomb_cost 
											  SET plant_name='".$plant_name."',
											  branch_code='".$distinct_branch_code."',
											  transport_mode='".$transport_mode."',
											  prod_code='".$distinct_dnsprod_code."',
											  honeycomb_cost='".$honeycomb_cost_case_prodwise."',
											  ip_address='".$_SERVER['REMOTE_ADDR']."',
											  user_id='".$_SESSION['admin_login']."',
											   vertical_value='".$vertical_value."',
											  datetime=CURRENT_TIMESTAMP";
						if(mysqli_query($link,$sqlinserthoneycombcost))
						{
							$sqlinserthoneycombcostlog="INSERT INTO honeycomb_cost_log 
											  SET plant_name='".$plant_name."',
											  branch_code='".$distinct_branch_code."',
											  transport_mode='".$transport_mode."',
											  prod_code='".$distinct_dnsprod_code."',
											  honeycomb_cost='".$honeycomb_cost."',
											  ip_address='".$_SERVER['REMOTE_ADDR']."',
											  user_id='".$_SESSION['admin_login']."',
											   vertical_value='".$vertical_value."',
											   operation_type='UPLOAD',
											  datetime=CURRENT_TIMESTAMP";
							mysqli_query($link,$sqlinserthoneycombcostlog);				  
						}
						if($is_formulation=='yes')
						{
							$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
						}
						else
						{
							$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
						}
						if($cntchkpricegeneration > 0)
						{
							generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
						}
					}
				  }
				  else{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Plant name column in Honeycomb cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
				  }
				}			  
			$rec_count++;
		   }
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for honeycomb cost.csv is wrong.";
			exit();
		}*/
		//For Detention Cost csv
	   if(similar_file_exists("../csv/$folderName/detention cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/detention cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$current_date=date('Y-m-d');
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
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
				
				$plant_name=trim($data[0]);
				$dns_branch_code=trim($data[1]);
				$detention_cost=trim($data[2]);
				$vertical_value=trim($data[3]);

					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
					if($countdistinctplant==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Depot code column in Detention cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);
					$countverticalvalue=mysqli_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Vertical value column in Detention cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
					}
					if($distinct_plant_name==$plant_name){
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."'";
					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
						$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];
						
						$sqlchkformulation="SELECT formulation FROM product_group_master WHERE 
										product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
						$rschkformulation=mysqli_query($link,$sqlchkformulation);
						$rowchkformulation=mysqli_fetch_assoc($rschkformulation);
						$is_formulation=$rowchkformulation['formulation'];
		
						$detention_cost_case_prodwise=$detention_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$detention_cost_case_prodwise=round(($detention_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						$sqlinsertdetentioncost="INSERT INTO detention_cost SET
												plant_name='".$distinct_plant_name."', 
											    prod_code='".$distinct_dnsprod_code."',
											    branch_code='".$distinct_branch_code."',
											   detention_cost	='".$detention_cost_case_prodwise."',
											   vertical_value='".$vertical_value."',
											   ip_address='".$_SERVER['REMOTE_ADDR']."',
											   user_id='".$_SESSION['admin_login']."',
											   datetime=CURRENT_TIMESTAMP";
						if(mysqli_query($link,$sqlinsertdetentioncost))
						{
							$sqlinsertdetentioncostlog="INSERT INTO detention_cost_log SET
														plant_name='".$distinct_plant_name."', 
														prod_code='".$distinct_dnsprod_code."',
														branch_code='".$distinct_branch_code."',
													   detention_cost	='".$detention_cost."',
													   vertical_value='".$vertical_value."',
													   ip_address='".$_SERVER['REMOTE_ADDR']."',
													   user_id='".$_SESSION['admin_login']."',
													   operation_type='UPLOAD',
													   datetime=CURRENT_TIMESTAM";
							mysqli_query($link,$sqlinsertdetentioncostlog);				  
						}
						if($is_formulation=='yes')
						{
							$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".${product_group_code.$distinct_dnsprod_code}."' 
													AND SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
						}
						else
						{
							$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".${product_group_code.$distinct_dnsprod_code}."' 
													AND SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
							$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
						}
						if($cntchkpricegeneration > 0)
						{
							generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
						}
					}
				  }
				  else{
						$GLOBALS['msg'] = "<font size=\"+2\">Please provide proper value for Plant name column in Detention cost.csv at row ".$csv_row_count."</font>";
						disphtml("main();");
						die;
				  }
				}			  
			$rec_count++;
		   }
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for detention cost.csv is wrong.";
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
					$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
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
?>