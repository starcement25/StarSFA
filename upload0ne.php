<?php	
    if(similar_file_exists("../csv/$folderName/gift_master.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/gift_master.csv");
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
				$to_date=trim($data[3]);
				$acedns=trim($data[4]);
				
				$sqlgiftchk="SELECT * FROM gift_master WHERE gift_name='".addslashes($gift_name)."' 
							AND cust_type='".addslashes($cust_type)."' AND acedns='".$acedns."' 
							AND from_date='".$from_date."' AND to_date='".$to_date."'";
				$rsgiftchk=mysqli_query($link,$sqlgiftchk);
				$countgiftchk=mysqli_num_rows($rsgiftchk);
				$csv_row_count=$rec_count+1;
				if($countgiftchk==0)
					{
						$sql  = "insert into gift_master ";
						$sql .= " SET gift_name='".$gift_name."'";
						$sql .= " , cust_type='".addslashes($cust_type)."'";
						$sql .= " , acedns='".addslashes($acedns)."'";
						$sql .= " , from_date='".addslashes($from_date)."'";
						$sql .= " , to_date='".addslashes($to_date)."'";
						$sql .= " , update_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sql) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in gift_master.csv.Please check.");
					}
			}
			$rec_count++;
		}		

		$successval=1;

	}
?>