<?php
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
require("adminUtils.php");
	if($_SESSION['admin_login']=="" || $_SESSION['admin_login']=="admin")  		header("location:index.php");


if($_REQUEST['mode']=='csv_upload')
{
	csv_upload();
}
else
{
	disphtml("main();");
}
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
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr> 
			<td height="30"  align="left">
            <table width="100%">
				<tr> 
					<td width="95%" align="center" class="ERR"><?=$GLOBALS['msg']?></td>
					<td width="5%" align="right"></td>
				</tr>
			</table></td>
		</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
<table width="70%" align="center" cellpadding="5" cellspacing="2" class="border">
	<form name="form_add_CSV" action="<?=$_SERVER['PHP_SELF']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
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
                <input type="button" name="back" value=" Back " onClick="javascript:document.location='adminMain.php'">
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
<?
}

function csv_upload(){
	
	//For Unzip a zip file
	if(file_exists($_FILES['zip_file']['tmp_name']))
	{
		 if(file_exists("../csv/Brand Master.csv"))
		 {
			 unlink("../csv/Brand Master.csv");
		 }
		 if(file_exists("../csv/Employee Master.csv"))
		 {
			 unlink("../csv/Employee Master.csv");
		 }
		 if(file_exists("../csv/Customer Master.csv"))
		 {
			 unlink("../csv/Customer Master.csv");
		 }
		 if(file_exists("../csv/SKU Master.csv"))
		 {
			 unlink("../csv/SKU Master.csv");
		 }
		 if(file_exists("../csv/Outstanding.csv"))
		 {
			 unlink("../csv/Outstanding.csv");
		 }
		 
		 $zip = new ZipArchive;
		if ($zip->open($_FILES['zip_file']['tmp_name']) === TRUE) {
			
			$zip->extractTo('../csv/');
			$zip->close();
			$GLOBALS['msg']='ok';
		} else {
			$GLOBALS['msg']='failed';
		}
	}
	//For Brand Master JK
	if(file_exists("../csv/Brand Master JK.csv"))
	{
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file("../csv/Brand Master JK.csv");
		$sqldelete="truncate brand_master";
		$rsdelete=mysqli_query($link,$sqldelete);
		
		$sqldeletebf="truncate brand_form_master";
		$rsdeletebf=mysqli_query($link,$sqldeletebf);
		
		$sqldeletesku="truncate sku_master";
		$rsdeletesku=mysqli_query($link,$sqldeletesku);
		$skucount='12000';
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
			  
				$brand_name=trim($data[0]);
				$sku_name=trim($data[1]);
				$brand_form_name=trim($data[2]);
				$cl_stk=trim($data[3]);
				
				$sqlbrandchk="SELECT * FROM brand_master WHERE brand_name='".$brand_name."'";
				$rsbrandchk=mysqli_query($link,$sqlbrandchk);
				$countbrandchk=mysqli_num_rows($rsbrandchk);
				if($countbrandchk<1)
				{
					$sqlbrand  = "insert into brand_master SET ";
					$sqlbrand .= "  brand_code='".mysqli_escape_string($brand_name)."'";
					$sqlbrand .= " , brand_name='".mysqli_escape_string($brand_name)."'";
					
					mysqli_query($link,$sqlbrand) or die(mysqli_error());
				}
				
				$sqlbrandformchk="SELECT * FROM brand_form_master WHERE brand_form_name='".$brand_form_name."'";
				$rsbrandformchk=mysqli_query($link,$sqlbrandformchk);
				$countbrandformchk=mysqli_num_rows($rsbrandformchk);
				if($countbrandformchk<1)
				{
					$sqlbrandform  = "insert into brand_form_master SET ";
					$sqlbrandform .= "  brand_form_code='".mysqli_escape_string($brand_form_name)."'";
					$sqlbrandform .= " , brand_form_name='".mysqli_escape_string($brand_form_name)."'";
					$sqlbrandform .= " , brand_code='".mysqli_escape_string($brand_name)."'";
					
					mysqli_query($link,$sqlbrandform) or die(mysqli_error());
				}
				
				$sql  = "insert into sku_master ";
				$sql .= " SET sku_code='".$skucount."'";
				$sql .= " , sku_name='".mysqli_escape_string($sku_name)."'";
				$sql .= " , brand_code='".mysqli_escape_string($brand_name)."'";
				$sql .= " , brand_form_code='".mysqli_escape_string($brand_form_name)."'";
				$sql .= " , cl_stk='".mysqli_escape_string($cl_stk)."'";
				mysqli_query($link,$sql);
				
			}
			 $rec_count++;
			 $skucount++;
		}		
		$successval=1;
	}
	else
	{
		$successval=0;
	}

	//For Brand Master CSV
	if(file_exists("../csv/Brand Master.csv"))
	{
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file("../csv/Brand Master.csv");
		$sqldelete="truncate brand_master";
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
			  
				$brand_name=trim($data[0]);
				
				$sqlbrand  = "insert into brand_master SET ";
				$sqlbrand .= "  brand_code='".mysqli_escape_string($brand_name)."'";
				$sqlbrand .= " , brand_name='".mysqli_escape_string($brand_name)."'";
				
				mysqli_query($link,$sqlbrand) or die(mysqli_error());
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	else
	{
		$successval=0;
	}
	
	//For Employee CSV
	if(file_exists("../csv/Employee Master.csv"))
	{
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file("../csv/Employee Master.csv");
		$sqldelete="truncate employee_master";
		$rsdelete=mysqli_query($link,$sqldelete);
		$sqldeletepassword="truncate changepassword";
		$rsdeletepassword=mysqli_query($link,$sqldeletepassword);
		$employeecount='E0000';
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
				
				$sql  = "insert into employee_master ";
				$sql .= " SET emp_code='".$employeecount."'";
				$sql .= " , emp_name='".$employee_name."'";
				mysqli_query($link,$sql) or die(mysqli_error());
				
				$sqlcp  = "insert into changepassword ";
				$sqlcp .= " SET emp_code='".$employeecount."'";
				$sqlcp .= " , newpassword='1234'";
				$sqlcp .= " , oldpassword='1234'"; 
				$sqlcp .= " , status='true'"; 
				mysqli_query($link,$sqlcp) or die(mysqli_error());
			}
			 $rec_count++;
			 $employeecount++;
		}		
		$successval=1;
	}
	else
	{
		$successval=0;
	}
	
	//For Customer CSV
	if(file_exists("../csv/Customer Master.csv"))
	{
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file("../csv/Customer Master.csv");
		$sqldelete="truncate customer_master";
		$rsdelete=mysqli_query($link,$sqldelete);
		$sqlroutedelete="truncate route_master";
		$rsroutedelete=mysqli_query($link,$sqlroutedelete);
		
		$customercount='C/0000000';
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
				
				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".$emp_name."'";
				$rsempcode=mysqli_query($link,$sqlempcode);
				$rowempcode=mysqli_fetch_assoc($rsempcode);
				$emp_code=$rowempcode['emp_code'];
				
				$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
				$rsroutechk=mysqli_query($link,$sqlroutechk);
				$countroutechk=mysqli_num_rows($rsroutechk);
				if($countroutechk<1)
				{
					$routcode='RT/'.$countroute;
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$routcode."'";
					$sqlroute .= " ,route_name='".$route_name."'";
					$sqlroute .= " , emp_code='".$emp_code."'";
					
					mysqli_query($link,$sqlroute);
				}
				
				$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
				$rsroutecode=mysqli_query($link,$sqlroutecode);
				$rowroutecode=mysqli_fetch_assoc($rsroutecode);
				$route_code=$rowroutecode['route_code'];
				
				$sql  = "insert into customer_master ";
				$sql .= " SET customer_code='".$customercount."'";
				$sql .= " , customer_name='".mysqli_escape_string($customer_name)."'";
				$sql .= " , route_code='".$route_code."'";
				$sql .= " , emp_code='".$emp_code."'";
				
				mysqli_query($link,$sql);
			}
			 $rec_count++;
			 $customercount++;
			 $countroute++;
		}		
		$successval=1;
	}
	else
	{
		$successval=0;
	}
	
	//For Sku CSV
	if(file_exists("../csv/SKU Master.csv"))
	{
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file("../csv/SKU Master.csv");
		$sqldelete="truncate sku_master";
		$rsdelete=mysqli_query($link,$sqldelete);
		$skucount='12000';
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
			  
				$sku_name=trim($data[0]);
				$brand_code=trim($data[1]);
				
				$sql  = "insert into sku_master ";
				$sql .= " SET sku_code='".$skucount."'";
				$sql .= " , sku_name='".mysqli_escape_string($sku_name)."'";
				$sql .= " , brand_code='".mysqli_escape_string($brand_code)."'";
				mysqli_query($link,$sql);
			}
			 $rec_count++;
			 $skucount++;
		}		
		$successval=1;
	}
	else
	{
		$successval=0;
	}	
	
	//For Outstanding CSV
	
	if(file_exists("../csv/Outstanding.csv"))
	{
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file("../csv/Outstanding.csv");
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
				
				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_name."'";
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
				
				mysqli_query($link,$sql);
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	else
	{
		$successval=0;
	}
	if($successval==1)
	{
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
		disphtml("main();");
	}
	else 
	{
		$err .= "Problem with uploading Zip file";
		$GLOBALS['msg'] = $err;
		disphtml("main();");
	}
}
?>