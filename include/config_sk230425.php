<?php
	$nick_name=strtoupper($_REQUEST['nick_name']);
	if($_REQUEST['nick_name']=='DIGIVIVE')
	{
		$nick_name=strtoupper($_REQUEST['nick_name']);
	}
	
	//For PRABHUJI
	/*if(strtoupper($nick_name)=='HALDIRAM')
	{
		$nick_name='PRABHUJI';
	}*/
	//For DNV
	if(strtoupper($nick_name)=='DNV')
	{
		$nick_name='DNV';
	}
	$mode=$_REQUEST['mode'];
	define("SERVER","localhost");
	define("USER","root");
	define("PASSWORD","Passw0rd123#$");
	define("APICALLLOGURL","https://devsfa.starcement.co.in");
	/*$linkdbaccess=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_acednsproduct",$linkdbaccess) or die("could not connect the setup database");*/
	    $linkdbaccess=mysqli_connect("localhost","root","Passw0rd123#$","acedns_acednsproduct") or die("Setup Database Connection Error.");
		
    

	$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsdbaccessdetails=mysqli_query($linkdbaccess,$sqldbaccessdetails);
	$rowdbaccessdetails=mysqli_fetch_assoc($rsdbaccessdetails);
	$remote_db_access=$rowdbaccessdetails['remote_db_access'];
	mysqli_close($linkdbaccess);
	if($remote_db_access=='yes' && $mode!='SETUP')
	{
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_$nick_name");
	}
	else
	{
		if($mode=='SETUP')
		{
			define("DB","acedns_acednsproduct");
			define("DBN","acedns_$nick_name");
		}
		else
		{
			define("DB","acedns_$nick_name");
			define("DBN","acedns_$nick_name");
		}
		define("URL","https://devsfa.starcement.co.in");

		//define("CSS","http://localhost/shopnshop/css/style.css");	
	}
	
	function insertapilog($datetime,$emp_code,$url,$nick_name)
	{
		//mysqli_select_db("acedns_".$nick_name);
		//define("DB","acedns_$nick_name");
		$link=mysqli_connect(SERVER,USER,PASSWORD,DBN) or die("Database Connection Error.");
		$sqlinsertapilog="INSERT INTO apicalllog SET date_time=CURRENT_TIMESTAMP,
						  emp_code='".$emp_code."',
						  url='".$url."'";
		mysqli_query($link,$sqlinsertapilog);				  
	}
	function modifyempdatadownloadlog($emp_code,$nick_name)
	{
	//	mysqli_select_db("acedns_".$nick_name);
		//define("DB","acedns_$nick_name");
		$link=mysqli_connect(SERVER,USER,PASSWORD,DBN) or die("Database Connection Error.");
		
		if($emp_code=='')
		{
			$sqlallemp="SELECT emp_code FROM employee_master WHERE acedns <> 'N'";
			$rsallemp=mysqli_query($link,$sqlallemp);
			while($rowallemp=mysqli_fetch_assoc($rsallemp))
			{
				$emp_code_all=$rowallemp['emp_code'];
				$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code_all."'";
				$rsemplogchk=mysqli_query($link,$sqlemplogchk);
				$countemplogchk=mysqli_num_rows($rsemplogchk);
				if($countemplogchk<1)
				{
					$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
					$sqlinsertemplog .= "  	emp_code='".mysqli_real_escape_string($emp_code_all)."'";
					$sqlinsertemplog .= " , is_download='yes'";
					$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertemplog);
				}
				else
				{
					$rowemplogchk=mysqli_fetch_assoc($rsemplogchk);
					$is_download=$rowemplogchk['is_download'];
					if($is_download=='no')
					{
						$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
										is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code_all."'";
						mysqli_query($link,$sqlupdateemplog);	
					}
				}
			}
		}
		else
		{
			$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
			$rsemplogchk=mysqli_query($link,$sqlemplogchk);
			$countemplogchk=mysqli_num_rows($rsemplogchk);
			if($countemplogchk<1)
			{
				$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
				$sqlinsertemplog .= "  	emp_code='".mysqli_real_escape_string($emp_code)."'";
				$sqlinsertemplog .= " , is_download='yes'";
				$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertemplog);
			}
			else
			{
				$rowemplogchk=mysqli_fetch_assoc($rsemplogchk);
				$is_download=$rowemplogchk['is_download'];
				if($is_download=='no')
				{
					$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
									is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
					mysqli_query($link,$sqlupdateemplog);	
				}
			}
		}
	}
	function returndatarefresh($emp_code)
	{
		if($emp_code!='')
		{
			$sqlempdownloadchk="SELECT is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
			$rsempdownloadchk=mysqli_query($link,$sqlempdownloadchk);
			$rowempdownloadchk=mysqli_fetch_assoc($rsempdownloadchk);
			$is_download=$rowempdownloadchk['is_download'];
			$countempdownloadchk=mysqli_num_rows($rsempdownloadchk);
			if($is_download=='yes')
			{
				$datarefresh=2;
			}
			else
			{
				$datarefresh=0;
			}
		}
		else
		{
			$datarefresh=0;
		}
		return $datarefresh;
   }
?>
