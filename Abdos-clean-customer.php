<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ABDOS");
$link=mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB,$link);
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
	if(similar_file_exists("ABDOS clean master.csv")!=false)
	{
		$filename=similar_file_exists("ABDOS clean master.csv");
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
			  
				$old_customer_code=trim($data[0]);
				$new_customer_code=trim($data[1]);
				if($old_customer_code !='' && $new_customer_code!='#N/A' && $new_customer_code!='')
				{
					$sqlprevorder  = "UPDATE prev_order_counting_master SET customer_code='".$new_customer_code."' WHERE customer_code='".$old_customer_code."'";
					mysqli_query($link,$sqlprevorder);
					/*$sqlorder  = "UPDATE order_header SET customer_code='".$new_customer_code."' WHERE customer_code='".$old_customer_code."'";
					mysqli_query($link,$sqlorder);

					$sqlaudit  = "UPDATE stock_audit SET customer_code='".$new_customer_code."' WHERE customer_code='".$old_customer_code."'";
					mysqli_query($link,$sqlaudit);*/
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

?>