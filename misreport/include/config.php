<?php
	//set_magic_quotes_runtime(0);
	//session_start();
	//include("common.php");
	$nick_name ='';
	if(isset($_REQUEST['nick_name']) && $_REQUEST['nick_name']!=''){
		$nick_name=trim(strtoupper($_REQUEST['nick_name']));
	}
	elseif(isset($_SESSION['nick_name']) && $_SESSION['nick_name']!=''){
		$nick_name=trim(strtoupper($_SESSION['nick_name']));
	}

	$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:'';
	
	if(!isset($remote_db_access))
	{
		$linksetupadmin=mysqli_connect("localhost","root","Passw0rd123#$","acedns_acednsproduct") or die("Setup Database Connection Error.");
		//mysql_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");
		
		$sqlnickname="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
		$rsnickname=mysqli_query($linksetupadmin,$sqlnickname);
		$row_nick_name = mysqli_fetch_assoc($rsnickname);
		$cntnickname=mysqli_num_rows($rsnickname);
		$remote_db_access = isset($row_nick_name['remote_db_access']) ?? $row_nick_name['remote_db_access'];
		mysqli_close($linksetupadmin);
	}
	
	if($remote_db_access == 'yes'){
		define("SERVERREMOTE","52.66.101.239");
        define("USERREMOTE","root");
        define("PASSWORDREMOTE","cmcl@123");
        //define("DBREMOTE","acedns_$nick_name");
	}
	else{
		define("SERVER","localhost");
		define("USER","root");
		define("PASSWORD","Passw0rd123#$");
	}

	if($mode=='SETUP')
	{
		define("DB","acedns_acednsproduct");
	}
	else
	{
		if($nick_name=='SCHOOL')
		{
			define("DB","acedns_school");
		}
		else
		{
		define("DB","acedns_$nick_name");
		}
	}

	
	//----------------------- Definition of different attributes----------------------//
	define("MANDATORY","<font face='verdana' size='1' color='#CC0000'></font>");
	define("SCROLL_COLOR","#E1EDC9");
	define("LEFT_COLOR1","#CEDFEC");
	
	define("BLOCKSIZE",3);
	define("SCROLL_COLOR1","#CEDFEC");	
	//----------------------- Database table details ---------------------------------//
	//A
	/*function modifyempdatadownloadlog($emp_code,$nick_name)
	{
		mysql_select_db("acedns_".$nick_name);
		if($emp_code=='')
		{
			$sqlallemp="SELECT emp_code FROM employee_master WHERE acedns <> 'N'";
			$rsallemp=mysql_query($sqlallemp);
			while($rowallemp=mysql_fetch_array($rsallemp))
			{
				$emp_code_all=$rowallemp['emp_code'];
				$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code_all."'";
				$rsemplogchk=mysql_query($sqlemplogchk);
				$countemplogchk=mysql_num_rows($rsemplogchk);
				if($countemplogchk<1)
				{
					$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
					$sqlinsertemplog .= "  	emp_code='".mysql_real_escape_string($emp_code_all)."'";
					$sqlinsertemplog .= " , is_download='yes'";
					$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
					mysql_query($sqlinsertemplog);
				}
				else
				{
					$rowemplogchk=mysql_fetch_array($rsemplogchk);
					$is_download=$rowemplogchk['is_download'];
					if($is_download=='no')
					{
						$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
										is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code_all."'";
						mysql_query($sqlupdateemplog);	
					}
				}
			}
		}
		else
		{
			$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
			$rsemplogchk=mysql_query($sqlemplogchk);
			$countemplogchk=mysql_num_rows($rsemplogchk);
			if($countemplogchk<1)
			{
				$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
				$sqlinsertemplog .= "  	emp_code='".mysql_real_escape_string($emp_code)."'";
				$sqlinsertemplog .= " , is_download='yes'";
				$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
				mysql_query($sqlinsertemplog);
			}
			else
			{
				$rowemplogchk=mysql_fetch_array($rsemplogchk);
				$is_download=$rowemplogchk['is_download'];
				if($is_download=='no')
				{
					$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
									is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
					mysql_query($sqlupdateemplog);	
				}
			}
		}
	}*/
?>