<?php
	set_time_limit(1000);	
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");
	
	$folderName=$nick_name;
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
	
	$error_array=array();
	
	//For Company Master CSV
	if(similar_file_exists("csv/$folderName/company master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/company master.csv");
		$linescompany = file($filename);
		$csvcompanycount=(count($linescompany)-1);
	}
	//For Branch Master CSV
	if(similar_file_exists("csv/$folderName/branch master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/branch master.csv");
		$linesbranch = file($filename);
		$csvbranchcount=(count($linesbranch)-1);
	}
	//For Employee CSV
	if(similar_file_exists("csv/$folderName/employee_master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/employee_master.csv");
		$linesemployee = file($filename);
		$csvemployeecount=(count($linesemployee)-1);
	}
	//For Customer CSV
	if(similar_file_exists("csv/$folderName/CustomerMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/CustomerMaster.csv");
		$linescustomer = file($filename);
		$csvcustomercount=(count($linescustomer)-1);
	}
	//For Sku CSV
	if(similar_file_exists("csv/$folderName/ProductMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/ProductMaster.csv");
		$linessku = file($filename);
		$csvskucount=(count($linessku)-1);
	}
	//For Outstanding CSV
	if(similar_file_exists("csv/$folderName/OutstandingMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/OutstandingMaster.csv");
		$linesoutstanding = file($filename);
		$csvoutstandingcount=(count($linesoutstanding)-1);
	}
	//For MRP CSV
	if(similar_file_exists("csv/$folderName/MRPMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/MRPMaster.csv");
		$linesmrp = file($filename);
		$csvmrpcount=(count($linesmrp)-1);
	}
	//Company
   $sqlcompanycount="select * from company_master";
   $rscompanycount=mysqli_query($link,$sqlcompanycount) or die(mysqli_error());
   $companycount=mysqli_num_rows($rscompanycount);

   //Branch
   $sqlbranchcount="select * from branch_master";
   $rsbranchcount=mysqli_query($link,$sqlbranchcount) or die(mysqli_error());
   $branchcount=mysqli_num_rows($rsbranchcount);

   //Category
  /* $sqlcatcount="select * from category_master";
   $rscatcount=mysqli_query($link,$sqlcatcount) or die(mysqli_error());
   $catcount=mysqli_num_rows($rscatcount);

   //Sub Category
   $sqlsubcatcount="select * from sub_category_master";
   $rssubcatcount=mysqli_query($link,$sqlsubcatcount) or die(mysqli_error());
   $subcatcount=mysqli_num_rows($rssubcatcount);*/
   //Employee
   $sqlemployeecount="select * from employee_master";
   $rsemployeecount=mysqli_query($link,$sqlemployeecount) or die(mysqli_error());
   $employeecount=mysqli_num_rows($rsemployeecount);
   if(sale=='yes')
   {
		//Vendor
	   $sqlvendorcount="select * from vendor_master";
	   $rsvendorcount=mysqli_query($link,$sqlvendorcount) or die(mysqli_error());
	   $vendorcount=mysqli_num_rows($rsvendorcount);
	   
	   $vendorTR='<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">vendor_master</td>
					<td align=\"right\">'.$vendorcount.'</td>
					<td align=\"right\">'.$csvvendorcount.'</td>
				</tr>';
   }
   else
   {
	   $vendorTR='';
   }
	//customer
	$sqlcustomercount="select * from customer_master";
	$rscustomercount=mysqli_query($link,$sqlcustomercount) or die(mysqli_error());
	$customercount=mysqli_num_rows($rscustomercount);
	
   //SKU
   $sqlskucount="select * from product_master";
   $rsskucount=mysqli_query($link,$sqlskucount) or die(mysqli_error());
   $skucount=mysqli_num_rows($rsskucount);
   
   //Outstanding
   $sqloutstandingcount="select * from outstanding";
   $rsoutstandingcount=mysqli_query($link,$sqloutstandingcount) or die(mysqli_error());
   $outstandingcount=mysqli_num_rows($rsoutstandingcount);

   //Mrp
   $sqlmrpcount="select * from mrp";
   $rsmrpcount=mysqli_query($link,$sqlmrpcount) or die(mysqli_error());
   $mrpcount=mysqli_num_rows($rsmrpcount);
  	

   	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$val=$date.'-'.$month.'-'.$year.' @'.$hour.':'.$minute.':'.$second;
	
   	//$email   ='rajuyk@automotiveml.com'; 
	$email=$corresponding_emails;
	if($nick_name=='RKBK' || $nick_name=='CGEW')
	{
		$email=$corresponding_emails.',ak@coral.in';
	}
	if($nick_name=='NAPTC' || $nick_name=='PBCL')
	{
		$email='arupratanacharjee@gmail.com';
	}
	$subject =$nick_name.' batch running details on '.$val;
	$message='<html><body>
			<table width=\"99%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"5\"  style=\"BORDER: #80A537 1px solid;\">
				<tr style=\"FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #FFFFFF;BACKGROUND-COLOR:#80a537;\" > 
					<td colspan=\"8\">Comparison between the MYSQL database and CSV row count are mentioned below</td>
				</tr>
				<tr style=\"FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;BACKGROUND-COLOR:#c0c8b0;\"> 
					<td width=\"\" align=\"center\"><strong>Table Name</strong></td>
					<td width=\"30%\" align=\"left\"><strong>MYSQL Row Count</strong></td>
					<td width=\"30%\" align=\"left\"><strong>CSV Row Count</strong></td>
				</tr>
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">company_master</td>
					<td align=\"right\">'.$companycount.'</td>
					<td align=\"right\">'.$csvcompanycount.'</td>
				</tr>
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">branch_master</td>
					<td align=\"right\">'.$branchcount.'</td>
					<td align=\"right\">'.$csvbranchcount.'</td>
				</tr>
				
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">employee_master</td>
					<td align=\"right\">'.$employeecount.'</td>
					<td align=\"right\">'.$csvemployeecount.'</td>
				</tr>'.$vendorTR.'	
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">customer_master</td>
					<td align=\"right\">'.$customercount.'</td>
					<td align=\"right\">'.$csvcustomercount.'</td>
				</tr>	
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">sku_master</td>
					<td align=\"right\">'.$skucount.'</td>
					<td align=\"right\">'.$csvskucount.'</td>
				</tr>	
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">outstanding</td>
					<td align=\"right\">'.$outstandingcount.'</td>
					<td align=\"right\">'.$csvoutstandingcount.'</td>
				</tr>
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">mrp</td>
					<td align=\"right\">'.$mrpcount.'</td>
					<td align=\"right\">'.$csvmrpcount.'</td>
				</tr>	
				</table></body></html>';   

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n".
						'X-Mailer: PHP/' . phpversion();
	//echo $message;

	$flgSend=mail($email, $subject, $message, $headers,'-facedns@coral.in');
	/*if($flgSend)
		{
			echo "Csv Generated & Email Sending.";
		}
		else
		{
			echo "Email Can Not Send.";
		} */
?>