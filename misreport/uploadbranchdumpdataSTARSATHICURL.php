<?php
	// define("SERVERREMOTE","103.87.174.95");
	// define("USERREMOTE","starsaat_dnsprod");
	// define("PASSWORDREMOTE","dnsprod1234#");
	// define("DBREMOTE","starsaat_START");
		error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
	// $link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	// mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	$type ='remortdb';
	include "saathi_connection.php";
	//echo"<pre>";print_r($_POST);
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	/*$branch_code_array=array();
	$destination_code_array=array();
	foreach($_POST as $array_value) {
		
		foreach($array_value as $key=>$value) { echo"<pre>";print_r($array_value);

 	 		if($key=='branch_code_name') $branch_code_name=$value;
			if(!in_array($branch_code_name,$branch_code_array))
			{
				array_push($branch_code_array,$branch_code_name);
			}
			if($key=='dump_code')   	 $dump_code=$value;
			if(!isset(${dump_code.$branch_code_name}))
			{
				${dump_code.$branch_code_name}=array();
			}
			if(!in_array($dump_code,${dump_code.$branch_code_name}))
			{
			  if($dump_code!=''){
				array_push(${dump_code.$branch_code_name},$dump_code);
				}
			}
			if($key=='dump_name')  	 ${dump_name.$branch_code_name.$dump_code}=$value;
			if($key=='acedns')  		${acedns.$branch_code_name.$dump_code}=$value;
			if($key=='is_plant')  	  ${is_plant.$branch_code_name.$dump_code}=$value;
		}
	}
	echo"<pre>";print_r('ss');die;
	foreach($branch_code_array as $branch_code_val){
	  foreach(${dump_code.$branch_code_val} as $dump_code_val)
		{
		$branch_code_name=$branch_code_val;
		$dum_code=$dump_code_val;
		$dump_name=${dump_name.$branch_code_name.$dum_code};
		$acedns=${acedns.$branch_code_val.$dum_code};
		$is_plant=${is_plant.$branch_code_val.$dum_code};
		
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranchdump="SELECT branch_code FROM  branch_dump WHERE branch_code='".addslashes($branch_code)."' 
											AND dump_code='".$dum_code."'";
				$rsbranchdump=mysqli_query($link,$sqlbranchdump);
				$countbranchdump=mysqli_num_rows($rsbranchdump);
				if($countbranchdump<1 )
					{
						$sqlinsertbranchdump  = "insert into  branch_dump ";
						$sqlinsertbranchdump .= " SET branch_code='".$branch_code."'";
						$sqlinsertbranchdump .= " ,dump_code='".$dum_code."'";
						$sqlinsertbranchdump .= " ,dump_name='".$dump_name."'";
						$sqlinsertbranchdump .= " ,acedns='".$acedns."'";
						$sqlinsertbranchdump .= " ,is_plant='".$is_plant."'";
						$sqlinsertbranchdump .= " , download_time=CURRENT_TIMESTAMP()";
						echo $sqlinsertbranchdump;
						mysqli_query($link,$sqlinsertbranchdump);				
					}
					else
					{
						$sqlbranchdumpupd  = "update branch_dump ";
						$sqlbranchdumpupd .= " SET acedns='".$acedns."'";
						$sqlbranchdumpupd .= " ,is_plant='".$is_plant."'";
						$sqlbranchdumpupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' 
															AND dump_code='".$dum_code."'";
								echo $sqlbranchdumpupd;							
						mysqli_query($link,$sqlbranchdumpupd);
						
					}
			}
	 }*/

			$branch_data = [];
			//sk add line 02-09-25
			
			// Extract only dump_code values
			$dumpCodes = array_column($_POST, 'dump_code');

			// Remove duplicates
			$uniqueDumpCodes = array_values(array_unique($dumpCodes));
			//print_r($uniqueDumpCodes);
			if (!empty($uniqueDumpCodes)) {
					// escape values for safety
					$escaped = array_map(function($code) use ($link) {
						return "'" . mysqli_real_escape_string($link, $code) . "'";
					}, $uniqueDumpCodes);

					// join into IN clause
					$inClause = implode(",", $escaped);

					 $sql = "UPDATE branch_dump 
						SET acedns = 'N' 
						WHERE dump_code IN ($inClause)";
					mysqli_query($link, $sql) ;
				}
			//sk add end line 02-09-25

        foreach ($_POST as $row) {
            $branch_code_name = $row['branch_code_name'] ?? '';
            $dump_code = $row['dump_code'] ?? '';
            $dump_name = $row['dump_name'] ?? '';
            $acedns = $row['acedns'] ?? '';
            $is_plant = $row['is_plant'] ?? '';
        
            if ($branch_code_name === '' || $dump_code === '') {
                continue; // skip invalid rows
            }
        
            // Initialize if not set
            if (!isset($branch_data[$branch_code_name])) {
                $branch_data[$branch_code_name] = [];
            }
        
            // Avoid duplicates
            if (!isset($branch_data[$branch_code_name][$dump_code])) {
                $branch_data[$branch_code_name][$dump_code] = [
                    'dump_name' => $dump_name,
                    'acedns'    => $acedns,
                    'is_plant'  => $is_plant
                ];
            }
        }
		 foreach ($branch_data as $dns_branch_code => $dumps) {
        // Get actual branch_code
        $sql = "SELECT branch_code FROM branch_master WHERE dns_branch_code = '".addslashes($dns_branch_code)."'";
        $res = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($res);
        if (!$row) continue;
    
        $branch_code = $row['branch_code'];
    
        foreach ($dumps as $dump_code => $info) {
            $dump_name = $info['dump_name'];
            $acedns    = $info['acedns'];
            $is_plant  = $info['is_plant'];
    
            // Check if already exists
            $sqlchk = "SELECT branch_code FROM branch_dump 
                       WHERE branch_code = '".addslashes($branch_code)."' 
                         AND dump_code = '".addslashes($dump_code)."'";
            $reschk = mysqli_query($link, $sqlchk);
    
            if (mysqli_num_rows($reschk) < 1) {
                $sqlins = "INSERT INTO branch_dump 
                            SET branch_code = '$branch_code',
                                dump_code = '$dump_code',
                                dump_name = '$dump_name',
                                acedns = '$acedns',
                                is_plant = '$is_plant',
                                download_time = CURRENT_TIMESTAMP()";
                //echo "<pre>Insert: $sqlins</pre>";
                mysqli_query($link, $sqlins);
            } else {
                $sqlupd = "UPDATE branch_dump 
                            SET acedns = '$acedns',
                                is_plant = '$is_plant',
                                download_time = CURRENT_TIMESTAMP()
                            WHERE branch_code = '$branch_code' AND dump_code = '$dump_code'";
                //echo "<pre>Update: $sqlupd</pre>";
                mysqli_query($link, $sqlupd);
            }
        }
    }
	// define("SERVER","localhost");
	// define("USER","acedns_dnsprod");
	// define("PASSWORD","dnsprod1234#");
	// define("DB","acedns_STAR");
	// $link=mysqli_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	// mysqli_select_db(DB,$link) or die("could not connect the database");
	$type ='localdb';
	include "saathi_connection.php";
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_dump'";
	mysqli_query($link,$sqlupdate);
?>