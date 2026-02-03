<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_EMAMI");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$product_group_array=array('BR11','BR12','BR13','BR14','BR16','BR19');
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
		
	$contentsdate=$year.$month.$date;
	
	foreach($product_group_array as $prodgroupval)
	{
		$sqlsel="SELECT qty FROM sauda_allocation WHERE emp_code='E0060' AND product_filter_code='".$prodgroupval."'";
		$rssel=mysqli_query($link,$sqlsel);
		$countsel=mysqli_num_rows($rssel);
		if($countsel > 0)
		{
			$trans_id1='FASYS'.$contentsdate.'E0060'.$prodgroupval;
			$trans_id2='FASYS'.$contentsdate.'E0076'.$prodgroupval;

			if($prodgroupval=='BR11'){
				$sqlupdateallocation="UPDATE sauda_allocation SET qty='65',BAL='65' WHERE emp_code IN('E0060','E0076') AND product_filter_code='BR11'";
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";									   
			}
			if($prodgroupval=='BR12'){
				$sqlupdateallocation="UPDATE sauda_allocation SET qty='65',BAL='65' WHERE emp_code IN('E0060','E0076') AND product_filter_code='BR12'";
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";									   
			}
			if($prodgroupval=='BR13'){
				$sqlupdateallocation="UPDATE sauda_allocation SET qty='75',BAL='75' WHERE emp_code IN('E0060','E0076') AND product_filter_code='BR13'";
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='75'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='75'";
			}
			if($prodgroupval=='BR14'){
				$sqlupdateallocation="UPDATE sauda_allocation SET qty='60',BAL='60' WHERE emp_code IN('E0060','E0076') AND product_filter_code='BR14'";
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='60'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='60'";
			}
			if($prodgroupval=='BR16'){
				$sqlupdateallocation="UPDATE sauda_allocation SET qty='20',BAL='20' WHERE emp_code IN('E0060','E0076') AND product_filter_code='BR16'";
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='20'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='20'";
			}
			if($prodgroupval=='BR19'){
				$sqlupdateallocation="UPDATE sauda_allocation SET qty='10',BAL='10' WHERE emp_code IN('E0060','E0076') AND product_filter_code='BR19'";
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='10'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='10'";
			}
			mysqli_query($link,$sqlinsertsaudaallocationlog1);
			mysqli_query($link,$sqlinsertsaudaallocationlog2);
			mysqli_query($link,$sqlupdateallocation);
			echo 'success';
		}
		else{
			$trans_id1='FASYS'.$contentsdate.'E0060'.$prodgroupval;
			$trans_id2='FASYS'.$contentsdate.'E0076'.$prodgroupval;

			if($prodgroupval=='BR11'){
				$sqlinsertallocation1="INSERT INTO sauda_allocation SET 
										qty='65',
										BAL='65',
										emp_code='E0060',
										product_filter_code='BR11'";
				$sqlinsertallocation2="INSERT INTO sauda_allocation SET 
										qty='65',
										BAL='65',
										emp_code='E0076',
										product_filter_code='BR11'";						
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";									   
			}
			if($prodgroupval=='BR12'){
				$sqlinsertallocation1="INSERT INTO sauda_allocation SET 
										qty='65',
										BAL='65',
										emp_code='E0060',
										product_filter_code='BR12'";
				$sqlinsertallocation2="INSERT INTO sauda_allocation SET 
										qty='65',
										BAL='65',
										emp_code='E0076',
										product_filter_code='BR12'";						
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='65'";									   
			}
			if($prodgroupval=='BR13'){
				$sqlinsertallocation1="INSERT INTO sauda_allocation SET 
										qty='75',
										BAL='75',
										emp_code='E0060',
										product_filter_code='BR13'";
				$sqlinsertallocation2="INSERT INTO sauda_allocation SET 
										qty='75',
										BAL='75',
										emp_code='E0076',
										product_filter_code='BR13'";						

				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='75'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='75'";
			}
			if($prodgroupval=='BR14'){
				$sqlinsertallocation1="INSERT INTO sauda_allocation SET 
										qty='60',
										BAL='60',
										emp_code='E0060',
										product_filter_code='BR14'";
				$sqlinsertallocation2="INSERT INTO sauda_allocation SET 
										qty='60',
										BAL='60',
										emp_code='E0076',
										product_filter_code='BR14'";						
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='60'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='60'";
			}
			if($prodgroupval=='BR16'){
				$sqlinsertallocation1="INSERT INTO sauda_allocation SET 
										qty='20',
										BAL='20',
										emp_code='E0060',
										product_filter_code='BR16'";
				$sqlinsertallocation2="INSERT INTO sauda_allocation SET 
										qty='20',
										BAL='20',
										emp_code='E0076',
										product_filter_code='BR16'";						
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='20'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='20'";
			}
			if($prodgroupval=='BR19'){
				$sqlinsertallocation1="INSERT INTO sauda_allocation SET 
										qty='10',
										BAL='10',
										emp_code='E0060',
										product_filter_code='BR19'";
				$sqlinsertallocation2="INSERT INTO sauda_allocation SET 
										qty='10',
										BAL='10',
										emp_code='E0076',
										product_filter_code='BR19'";						
				$sqlinsertsaudaallocationlog1="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id1."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0060',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='10'";
				$sqlinsertsaudaallocationlog2="INSERT INTO sauda_allocation_log SET allocation_id	='".$trans_id2."',
													   allocation_date	   =CURRENT_TIMESTAMP(),
														emp_code			   ='E0076',
													   product_filter_code   ='".$prodgroupval."',
													   qty				   ='10'";
			}
			mysqli_query($link,$sqlinsertsaudaallocationlog1);
			mysqli_query($link,$sqlinsertsaudaallocationlog2);
			mysqli_query($link,$sqlinsertallocation1);
			mysqli_query($link,$sqlinsertallocation2);
			echo 'success';
		}
	}
	mysqli_close($link);
?>