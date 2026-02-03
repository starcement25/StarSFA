<?php	
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");
require("include/config-email-setup.php");
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
$folderName=$_REQUEST['nick_name'];
	//For Branch Master CSV
	if(similar_file_exists("csv/$folderName/Branch master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Branch master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		$sqldelete="truncate branch_master";
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
			  
				$branch_code=trim($data[0]);
				$branch_name=trim($data[1]);
				$branch_location=trim($data[2]);
				$comp_code=trim($data[3]);
				$branch_email_id=trim($data[4]);
				$alternative_email_id=trim($data[5]);
				
				$sqlbranch  = "insert into branch_master SET ";
				$sqlbranch .= "  	branch_code='".mysqli_escape_string($branch_code)."'";
				$sqlbranch .= " , branch_name='".mysqli_escape_string($branch_name)."'";
				$sqlbranch .= " , branch_location='".mysqli_escape_string($branch_location)."'";
				$sqlbranch .= " , comp_code='".mysqli_escape_string($comp_code)."'";
				$sqlbranch .= " , branch_email_id='".mysqli_escape_string($branch_email_id)."'";
				echo $sqlbranch .= " , alternative_email_id='".mysqli_escape_string($alternative_email_id)."'";
				
				mysqli_query($link,$sqlbranch) or die(mysqli_error());
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

	//For Customer CSV
	if(similar_file_exists("csv/$folderName/Branch Customer Master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Branch Customer Master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			/*$sqldelete="truncate customer_master";
			$rsdelete=mysqli_query($link,$sqldelete);
			$sqlroutedelete="truncate route_master";
			$rsroutedelete=mysqli_query($link,$sqlroutedelete);
			$sqldeletetemp="truncate customer_master_temp";
			$rsdeletetemp=mysqli_query($link,$sqldeletetemp);*/
			
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
					$branch_code	=trim($data[1]);
					$sqlcustomernamechk="UPDATE customer_master SET branch_code='".$branch_code."' 
										WHERE customer_name='".addslashes($customer_name)."'";
					$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
				}
				 $rec_count++;
				 $countroute++;
			}
		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}*/
	//For Outstanding CSV
?>