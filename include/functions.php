<?php

//Function used for combo population

//define("DB","acedns_$nick_name");
		/*$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");*/
		
$emphierarchystring = "";
		
function PopulateSelectDefault($combo_name, $sql, $value_field, $option_field, $selected_index, $is_multiple='', $style='')
{
	$id_arr = array();	
	if ($is_multiple != "")	
	{
		if ($selected_index == "0,0") $selected=" selected ";	
		else $id_arr = explode(",",$selected_index);
	}
	else
	{
		 $id_arr[] = $selected_index;
	}
	$str_select_start = "<select id=".trim($combo_name)." name=".trim($combo_name)." ".$is_multiple."  class=\"".$style."\"  $extra>";
	$str_option = $str_option;
	$selected="";
	$rs = mysqli_query($link,$sql);
	if (mysqli_num_rows($rs) > 0)
	{
		while($rec = mysqli_fetch_assoc($rs))
		{
			if (in_array($rec[$value_field],$id_arr)) $selected = " selected ";
			else  $selected = " ";
			$str_option = $str_option."<option title=\"".stripslashes($rec[$option_field])."\" value=".$rec[$value_field]." ".$selected." >".stripslashes($rec[$option_field])."</option>";
		}
	}
	$str_select_end = "</select>";
	mysqli_free_result($rs);
	return $str_select_start.$str_option.$str_select_end;
}
function PopulateSelect($combo_name, $sql, $value_field, $option_field, $selected_index, $is_multiple='', $style='')
{

	$id_arr = array();	

	if ($is_multiple != "")	

	{

		if ($selected_index == "0,0") $selected=" selected ";	

		else $id_arr = explode(",",$selected_index);

	}

	else

	{

		 $id_arr[] = $selected_index;

	}

	

	$str_select_start = "<select id=".trim($combo_name)." name=".trim($combo_name)." ".$is_multiple."  class=\"".$style."\"  $extra>";

	$str_option = $str_option."<option value=\"0\"".$selected.">------Select Option------</option>";



	$selected="";

	$rs = mysqli_query($link,$sql);



	if (mysqli_num_rows($rs) > 0)

	{

		while($rec = mysqli_fetch_assoc($rs))

		{

			if (in_array($rec[$value_field],$id_arr)) $selected = " selected ";

			else  $selected = " ";



			$str_option = $str_option."<option title=\"".stripslashes($rec[$option_field])."\" value=".$rec[$value_field]." ".$selected." >".stripslashes($rec[$option_field])."</option>";

		}

	}



	$str_select_end = "</select>";



	mysqli_free_result($rs);

	return $str_select_start.$str_option.$str_select_end;

}
function PopulateSelectMonthYear($combo_name, $sql, $value_field, $option_field, $selected_index, $is_multiple='', $style='')
{
	$id_arr = array();	

	if ($is_multiple != "")	

	{

		if ($selected_index == "0,0") $selected=" selected ";	

		else $id_arr = explode(",",$selected_index);

	}

	else

	{

		 $id_arr[] = $selected_index;

	}

	

	$str_select_start = "<select id=".trim($combo_name)." name=".trim($combo_name)." ".$is_multiple."  class=\"".$style."\"  $extra>";

	$str_option = $str_option."<option value=\"all\"".$selected.">-ALL-</option>";



	$selected="";

	$rs = mysqli_query($link,$sql);



	if (mysqli_num_rows($rs) > 0)

	{

		while($rec = mysqli_fetch_assoc($rs))

		{

			if (in_array($rec[$value_field],$id_arr)) $selected = " selected ";

			else  $selected = " ";



			$str_option = $str_option."<option title=\"".stripslashes($rec[$option_field])."\" value=".$rec[$value_field]." ".$selected." >".stripslashes($rec[$option_field])."</option>";

		}

	}
	$str_select_end = "</select>";
	mysqli_free_result($rs);
	return $str_select_start.$str_option.$str_select_end;
}

// function to fetch a single value against a ID

function getValue($TableName,$ID,$ID_Name,$FieldNames)

{

	$sql_getValue = "SELECT ".$FieldNames." FROM ".$TableName." WHERE ".$ID_Name."='".$ID."'";

	$query_getValue = mysqli_query($link,$sql_getValue);



	$rs_getValue = mysqli_fetch_assoc($query_getValue);



	if (mysqli_num_rows($query_getValue) > 0) return $rs_getValue[0];

	else return "";



	mysqli_free_result($query_getValue);

}


function googleAuthenticate($username, $password, $source="Company-AppName-Version", $service="ac2dm") {    

    session_start();
    if( isset($_SESSION['google_auth_id']) && $_SESSION['google_auth_id'] != null)
        return $_SESSION['google_auth_id'];

    // get an authorization token
    $ch = curl_init();
    if(!ch){
        return false;
    }

    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin");
    $post_fields = "accountType=" . urlencode('HOSTED_OR_GOOGLE')
        . "&Email=" . urlencode($username)
        . "&Passwd=" . urlencode($password)
        . "&source=" . urlencode($source)
        . "&service=" . urlencode($service);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);    
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // for debugging the request
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true); // for debugging the request

    $response = curl_exec($ch);

    //var_dump(curl_getinfo($ch)); //for debugging the request
    //var_dump($response);

    curl_close($ch);

    if (strpos($response, '200 OK') === false) {
        return false;
    }

    // find the auth code
    preg_match("/(Auth=)([\w|-]+)/", $response, $matches);

    if (!$matches[2]) {
        return false;
    }

    $_SESSION['google_auth_id'] = $matches[2];
    return $matches[2];
}

function sendMessageToPhone($authCode, $deviceRegistrationId, $msgType, $messageText) {

        $headers = array('Authorization: GoogleLogin auth=' . $authCode);
        $data = array(
            'registration_id' => $deviceRegistrationId,
            'collapse_key' => $msgType,
            'data.message' => $messageText //TODO Add more params with just simple data instead           
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://android.apis.google.com/c2dm/send");
        if ($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
	
function Exist($value, $field_name, $table_name, $other_condition='')
{
	
	if ($other_condition != "") $sql_other_condition = " AND ".$other_condition." ";
	else  $sql_other_condition = "";
	$sql = "SELECT COUNT(*) FROM ".$table_name." WHERE ".$field_name."='".$value."' ".$sql_other_condition;
	echo"<pre>";print_r($sql);die;
	$rs  = mysqli_query($link,$sql);
	$rec = mysqli_fetch_assoc($rs);
	if ($rec[0] > 0) return true;
	else return false;
}

function generate_userid($name)
{
	$userid = $name.rand();
	if(!Exist($userid,'user_id','faculty',''))
	{
		return $userid;
	}
	else generate_userid();
}


//Function used for pagination

function pagination($count,$frmName)
{
/*
	if($_REQUEST['mode']=='delete')

	{

		$count=$count-1;

		$noOfPages = ceil($count/$GLOBALS['show']);

		$_REQUEST['pageNo']=$noOfPages;

	}

	else

	{

		$noOfPages = ceil($count/$GLOBALS['show']);

	}

//sk230425 add isset
$frmName=(isset($frmName))?$frmName : '';
?>

<script language="JavaScript" type="text/javascript">

<!--

function prevPage(no)

{
	
	document.<?=$frmName?>.action="<?=$_SERVER['PHP_SELF']?>";

	document.<?=$frmName?>.pageNo.value = no-1;

	document.<?=$frmName?>.submit();

}



function nextPage(no)

{

	document.<?=$frmName?>.action="<?=$_SERVER['PHP_SELF']?>";

	document.<?=$frmName?>.pageNo.value = no+1;

	document.<?=$frmName?>.submit();

}



function disPage(no)

{

	document.<?=$frmName?>.action="<?=$_SERVER['PHP_SELF']?>";

	document.<?=$frmName?>.pageNo.value = no;

	document.<?=$frmName?>.submit();

}

//-->

</script>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="4">

	<tr>
	<?php /* ?>
		<!--td width="15%" align="left" style="text-align:left;"><? /*$_REQUEST[pageNo];if($_REQUEST[pageNo]!=1 && $_REQUEST[pageNo]!=''){ ?>

			<a href="javascript:prevPage(<?=$_REQUEST[pageNo] ?>);" onMouseOut="javascript:window.status='Done';" onMouseMove="javascript:window.status='Go to Previous Page';" class="l2">&#171; Previous</a>

			<? }else{ ?>

			<!--<a href="#" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to Previous Page';"><font size="3">&#171;</font> Prev</a>-->

			<? }?>

		<!--/td-->
		
		<td align="center" style="text-align:center;">

			<? ####### script to display no of pages #########

			//condition where no of pages is less than display limit



			$displayPageLmt = $GLOBALS['show']; #holds no of page links to display

			if($noOfPages <= $displayPageLmt)

			{

				for($pgLink = 1; $pgLink <= $noOfPages; $pgLink++)

				{

					if($pgLink==$_REQUEST[pageNo])

					{

						echo "<a href=\"#\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"l2\">[$pgLink]</a>";

					}

					else

					{

						echo "<a href=\"javascript:disPage($pgLink)\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"l2\">$pgLink</a>";

					}	

					if($pgLink<>$noOfPages) echo "&nbsp;|&nbsp;";

				} #end of for loop

			} #end of if



			//condition for no of pages greater than display limit



			if($noOfPages > $displayPageLmt)

			{

				if(($_REQUEST[pageNo]+($displayPageLmt-1)) <= $noOfPages)

				{

					for($pgLink = $_REQUEST[pageNo]; $pgLink <= ($_REQUEST[pageNo]+$displayPageLmt-1); $pgLink++)

					{

						if($pgLink==$_REQUEST[pageNo])

						{

							echo "<a href=\"#\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"l2\">[$pgLink]</a>";

						}

						else

						{

							echo "<a href=\"javascript:disPage($pgLink)\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"l2\">$pgLink</a>";

						}

						if($pgLink<>($_REQUEST[pageNo]+$displayPageLmt-1)) echo "&nbsp;|&nbsp;";

					}#end of for loop						

				}#end of inner if

				else
				{
					for($pgLink = ($noOfPages - ($displayPageLmt-1)); $pgLink <= $noOfPages; $pgLink++)
					{
						if($pgLink==$_REQUEST[pageNo])
						{
							echo "<a href=\"#\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"l2\">[$pgLink]</a>";

						}

						else

						{

							echo "<a href=\"javascript:disPage($pgLink)\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"l2\">$pgLink</a>";

						}



						if($pgLink<>$noOfPages) echo "&nbsp;|&nbsp;";

					}#end of for loop

				}					

			}#end of if noOfPage>displayPageLmt

			?>

		</td>

		<!--td width="15%" align="right" style="text-align:right;">

			<? /*if($_REQUEST[pageNo] != $noOfPages) { ?>

			<a href="javascript:nextPage(<?=$_REQUEST[pageNo] ?>)" onMouseOut="javascript:window.status='Done';" onMouseMove="javascript:window.status='Go to Next Page';" class="l2">Next &#187;</a>

			<? }else{ ?>

			<!--<a href="#" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to Next Page';">Next <font size="3">&#187;</font></a>-->

			

		<!--/td-->

	</tr>

	<? if($noOfPages > 1){ ?>

	<tr>

		<td colspan="3" align="center" class="l2" valign="top" style="text-align:center;"><strong>Page no. :</strong> 

			<select onChange="javascript:disPage(this.value);" style="font-family:verdana; font-size:11px">

			<? for($i=1;$i<=$noOfPages;$i++){?>

				<option value="<?=$i;?>"<?=($_REQUEST[pageNo]==$i)?"selected":"";?>><?=$i;?></option>

			<? }?>

			</select>

		</td>	

	</tr>

  <? } ?>

</table>

<?php
*/
} 
function return_no_days($val1,$val2)
{
	if($val2=='M')
	{
		$countday=0;
		for($i=1;$i<=date('d');$i++)
		{
			$no_of_date=date('Y').'/'.date('m').'/'.$i;
			$week_day=date('l', strtotime($no_of_date));
			if($week_day!='Sunday')
			{
				$countday++;
			}
				
		}
	}
	if($val2=='Y')
	{
		$countday=0;
		for($i=1;$i<=$val1;$i++)
		{
			$start_date = strtotime("2012/06/16");
			$date = strtotime(date("Y/m/d", strtotime($start_date)) . " +$i day");
			$date=date("Y/m/d",$date);
			$week_day=date('l', strtotime($date));
			if($week_day!='Sunday')
			{
				$countday++;
			}
		}
	}
	return $countday;
}
// function return_employee_hierarchy($emp_code) {
//     $emphierarchy = array();
//     $nick_name=$_REQUEST['nick_name'];
// 	// echo $nick_name;
// 	// die;
//     $ddb = "acedns_".$nick_name;

//     define("DBL","$ddb");
    
//     $link=mysqli_connect(SERVER,USER,PASSWORD,DBL) or die("Database Connection Error.");
    
    
//     employee_hierarchy_details($emp_code, $emphierarchy,$link);
    
    
    
//      $emphierarchystring = '';
    
// 	foreach($emphierarchy as $hierarchyval)
// 	{
// 		$emphierarchystring.=$hierarchyval.',';
// 	}
// 	$emphierarchystring=substr($emphierarchystring,0,-1);
// 	if(count(explode(',',$emphierarchystring))==1 && $emphierarchystring=="'".$emp_code."'")
// 	{
// 		$emphierarchystring=$emphierarchystring;
// 	}
// 	else
// 	{
// 		$emphierarchystring=$emphierarchystring.','."'".$emp_code."'";
// 	}
//     return $emphierarchystring;
// }
//new code modify 19-05-2026
function return_employee_hierarchy($emp_code)
{
    static $connections = array();

    $emphierarchy = array();

    $nick_name = $_REQUEST['nick_name'];

    $dbname = "acedns_" . $nick_name;

    if (!isset($connections[$dbname]))
    {
        $connections[$dbname] = mysqli_connect(SERVER, USER, PASSWORD, $dbname);

        if (!$connections[$dbname])
        {
            die("Database Connection Error: " . mysqli_connect_error());
        }
    }

    $link = $connections[$dbname];

    employee_hierarchy_details($emp_code, $emphierarchy, $link);

    $emphierarchy = array_unique($emphierarchy);

    if (!in_array("'" . $emp_code . "'", $emphierarchy))
    {
        $emphierarchy[] = "'" . $emp_code . "'";
    }

    return implode(',', $emphierarchy);
}

// function employee_hierarchy_details($emp_code,&$emphierarchy,$link){
    
    
    
//    //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
//    $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to)";
//   //echo $sqlemphierarchy;exit();
//    $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
//    $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
// 	if($cntemphierarchy>0)
// 	{
// 		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
// 		{
// 			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
// 			// mysqli_free_result($rsemphierarchy);
// 			employee_hierarchy_details($rowemphierarchy['emp_code'],$emphierarchy,$link);
// 		}
// 	}
// 	else
// 	{
// 		if(!in_array("'".$emp_code."'",$emphierarchy))
// 		{
// 			$emphierarchy[] ="'".$emp_code."'";
// 		}
// 	}

// 	 if(isset($rsemphierarchy)) {
//         mysqli_free_result($rsemphierarchy); 
//     }
// }

function employee_hierarchy_details($emp_code, &$emphierarchy, $link, &$visited = []) {

   
    if (isset($visited[$emp_code])) {
        return;
    }
    $visited[$emp_code] = true;

    
    $quoted = "'" . $emp_code . "'";
    if (!in_array($quoted, $emphierarchy)) {
        $emphierarchy[] = $quoted;
    }

    
    $sql = "SELECT emp_code 
            FROM employee_master 
            WHERE FIND_IN_SET('$emp_code', reporting_to)";
    $res = mysqli_query($link, $sql);

    if (!$res) {
        return;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $child = $row['emp_code'];
        $quotedChild = "'" . $child . "'";

        if (!in_array($quotedChild, $emphierarchy)) {
            $emphierarchy[] = $quotedChild;
        }

       
        employee_hierarchy_details($child, $emphierarchy, $link, $visited);
    }

    mysqli_free_result($res);
}

// function employee_hierarchy_details($emp_code, &$emphierarchy, $link) {

//     // Prevent infinite recursion (cycle detection)
//     if (in_array("'".$emp_code."'", $emphierarchy)) {
//         return;
//     }

//     $sqlemphierarchy = "SELECT emp_code FROM employee_master 
//                         WHERE FIND_IN_SET('".$emp_code."', reporting_to)";
    
//     $rsemphierarchy = mysqli_query($link, $sqlemphierarchy);

//     while ($row = mysqli_fetch_assoc($rsemphierarchy)) {

        
//         if (!in_array("'".$row['emp_code']."'", $emphierarchy)) {
//             $emphierarchy[] = "'".$row['emp_code']."'";
//         }

        
//         employee_hierarchy_details($row['emp_code'], $emphierarchy, $link);
//     }

//     mysqli_free_result($rsemphierarchy);
// }
// function return_employee_hierarchy($emp_code) {
//     $emphierarchy = [];

//     require_once("../sfa_connection.php");
//     $localDB = new sfa_connection();
//     $link = $localDB->conn;

//     employee_hierarchy_details($emp_code, $emphierarchy, $link);

    
//     $emphierarchy = array_unique($emphierarchy);

    
//     $emphierarchystring = implode(',', $emphierarchy);

   
//     if (!in_array("'" . $emp_code . "'", $emphierarchy)) {
//         $emphierarchystring .= ($emphierarchystring ? ',' : '') . "'" . $emp_code . "'";
//     }

//     return $emphierarchystring;
// }

// function employee_hierarchy_details($emp_code, &$emphierarchy, $link) {
    
//     $emp_code_escaped = mysqli_real_escape_string($link, $emp_code);

//     $sqlemphierarchy = "SELECT emp_code 
//                         FROM employee_master 
//                         WHERE FIND_IN_SET('$emp_code_escaped', reporting_to)";
    
//     $rsemphierarchy = mysqli_query($link, $sqlemphierarchy);

//     if (!$rsemphierarchy) {
      
//         return;
//     }

//     while ($row = mysqli_fetch_assoc($rsemphierarchy)) {
//         $child_emp = $row['emp_code'];
//         $quoted_child_emp = "'" . $child_emp . "'";

//         if (!in_array($quoted_child_emp, $emphierarchy)) {
//             $emphierarchy[] = $quoted_child_emp;
//             employee_hierarchy_details($child_emp, $emphierarchy, $link); // Recurse
//         }
//     }
// }

function return_employee_hierarchy_active($emp_code) {
    $emphierarchy = array();
    employee_hierarchy_details_active($emp_code, $emphierarchy,$link);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	if(count(explode(',',$emphierarchystring))==1 && $emphierarchystring=="'".$emp_code."'")
	{
		$emphierarchystring=$emphierarchystring;
	}
	else
	{
		$emphierarchystring=$emphierarchystring.','."'".$emp_code."'";
	}
    return $emphierarchystring;
}
function employee_hierarchy_details_active($emp_code,&$emphierarchy){
     $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
   //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
   $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to) AND acedns='Y'";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
			employee_hierarchy_details_active($rowemphierarchy['emp_code'],$emphierarchy);
		}
	}
	else
	{
		if(!in_array("'".$emp_code."'",$emphierarchy))
		{
			$emphierarchy[] ="'".$emp_code."'";
		}
	}
}

function return_employee_upper_hierarchy($emp_code) {
    $emphierarchy = array();
    $emphierarchystring = "";
    employee_upper_hierarchy_details($emp_code, $emphierarchy);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
    return $emphierarchystring;
}
function employee_upper_hierarchy_details($emp_code,&$emphierarchy){
     //$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");

	 static $connections = array();

    //$emphierarchy = array();

    $nick_name = $_REQUEST['nick_name'];

    $dbname = "acedns_" . $nick_name;

	if (!isset($connections[$dbname]))
    {
        $connections[$dbname] = mysqli_connect(SERVER, USER, PASSWORD, $dbname);

        if (!$connections[$dbname])
        {
            die("Database Connection Error: " . mysqli_connect_error());
        }
    }

    $link = $connections[$dbname];

  $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			$reporting_to=$rowemphierarchy['reporting_to'];
			if(strpos($reporting_to,',')!=false){
				$reporting_to_Arr=explode(',',$reporting_to);
			
				for($cn=0;$cn<count($reporting_to_Arr);$cn++)
				{
					$emphierarchy[] = "'".$reporting_to_Arr[$cn]."'";
					employee_upper_hierarchy_details($reporting_to_Arr[$cn],$emphierarchy);
				}
			}
			else
			{
				$emphierarchy[] = "'".$reporting_to."'";
				employee_upper_hierarchy_details($reporting_to,$emphierarchy);
			}
		}
	}
	else
	{
		if(!in_array("'".$emp_code."'",$emphierarchy))
		{
			$emphierarchy[] ="'".$emp_code."'";
		}
	}
}
function fetch_corresponding_emails($operation_type,$area,$branch_code)
{
	$correspondingemails='';
	
	$sqlfetchemails="SELECT mail_id FROM mail_access WHERE FIND_IN_SET( '".$branch_code."', branch_code ) >0 AND 
					FIND_IN_SET( '".$operation_type."', attributes ) >0 AND FIND_IN_SET( '".$area."', area ) >0";
	$rsfetchemails=mysqli_query($link,$sqlfetchemails) or die(mysqli_error().'Error in mail id fetch.');
	while($rowfetchemails=mysqli_fetch_assoc($rsfetchemails))
	{
		$correspondingemails=$correspondingemails.$rowfetchemails['mail_id'].',';
	}
	$correspondingemails=substr($correspondingemails,0,-1);
	return $correspondingemails;
}
function  title_case_emp($emp_name)
{
	$lower_case_emp=strtolower($emp_name);
	$emp_array=explode(' ',$lower_case_emp);
	$emp_val='';
	foreach($emp_array as $values)
	{
		$emp_val=$emp_val.ucfirst($values).' ';
	}
	$emp_val=rtrim($emp_val);
	return $emp_val;
}
//$emp_name='Vijay shankar';
//echo title_case_emp($emp_name);
function generate_OTP()
{
	$digits = 4;
    return $random_no=rand(pow(10, $digits-1), pow(10, $digits)-1);
	/*if(!Exist($random_no,'OTP','OTP_details',''))
	{
		$OTP=$random_no;
	}
	else 
	{
		return generate_OTP();
	}
	return $OTP;*/
}
function generate_transid($transid)
{
	$transid=$transid+1;
	if(!Exist($transid,'allocation_id','sauda_allocation_log',''))
	{
		return $transid;
	}
	else generate_transid($transid);
}
/*function customer_visit_details($emp_code,$trans_id,$customer_code)
{
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$curdate=$year.'-'.$month.'-'.$date;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-13,8)));
	$sqlcustomerdetails="SELECT today,mtd,ytd FROM cutomer_visit_details  WHERE emp_code='".$emp_code."'";
	$rscustomerdetails=mysqli_query($link,$sqlcustomerdetails);
	$rowcustomerdetails=mysqli_fetch_assoc($rowcustomerdetails);
	$today=$rowcustomerdetails['today'];
	$mtd==$rowcustomerdetails['mtd'];
	$ytd==$rowcustomerdetails['ytd'];
	
	if((strtotime($curdate)==strtotime($transdate)) && $today < 1)
	{
		$sqlupdatemisdata="UPDATE cutomer_visit_details SET customer_visited_tdy=customer_visited_tdy+1,
							customer_visited_mtd=customer_visited_mtd+1,customer_visited_ytd=customer_visited_ytd+1 
							WHERE emp_code='".$emp_code."'";
	}
	else if(strtotime($month)==strtotime(substr($trans_id,-9,2)) && strtotime($date)!= strtotime(substr($trans_id,-7,2)))
	{
		$sqlupdatemisdata="UPDATE cutomer_visit_details SET customer_visited_mtd=customer_visited_mtd+1,
							customer_visited_ytd=customer_visited_ytd+1 
							WHERE emp_code='".$emp_code."'";
	}
	else if(strtotime($month)!=strtotime(substr($trans_id,-9,2)) && strtotime($date)!= strtotime(substr($trans_id,-7,2)))
	{
		$sqlupdatemisdata="UPDATE cutomer_visit_details SET customer_visited_ytd=customer_visited_ytd+1 
							WHERE emp_code='".$emp_code."'";
	}
}*/
function update_transaction_STAR($emp_code,$trans_id,$qty,$trans_type,$trans_sub_type,$link)
{
    
    //$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$curdate=$year.'-'.$month.'-'.$date;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-14,8)));
	if($month < 4)
	{
		$financial_year_start=($year-1).'-04-01';
		$financial_year_end=$year.'-03-31';
	}
	else
	{
		$financial_year_start=$year.'-04-01';
		$financial_year_end=($year+1).'-03-31';
	}
	
	$sqlchkempdatewise="SELECT emp_code FROM mis_details_emp_datewise WHERE emp_code='".$emp_code."' AND operation_date='".$transdate."'";
	$rschkempdatewise=mysqli_query($link,$sqlchkempdatewise);
	$cntchkempdatewise=mysqli_num_rows($rschkempdatewise);
	if($trans_type=='A')
	{
		$coumn_name_tdy="present_tdy=(present_tdy+1)";
		$coumn_name_mtd="present_mtd=(present_mtd+1)";
		$coumn_name_ytd="present_ytd=(present_ytd+1)";
		if($cntchkempdatewise==0)
		{
			$sqlinsertempdatewisepresent="INSERT INTO mis_details_emp_datewise SET 
										operation_date='".$transdate."',emp_code='".$emp_code."',present=1";
			mysqli_query($link,$sqlinsertempdatewisepresent);							
		}
		else
		{
			$sqlupdateempdatewisepresent="UPDATE mis_details_emp_datewise SET present=(present+1) WHERE 
										operation_date='".$transdate."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlupdateempdatewisepresent);	
		}
	}
if($trans_type=='O')
	{
	    
	    
		$coumn_name_tdy="order_received_tdy=(order_received_tdy+".$qty.")";
		$coumn_name_mtd="order_received_mtd=(order_received_mtd+".$qty.")";
		$coumn_name_ytd="order_received_ytd=(order_received_ytd+".$qty.")";
		
		$sqlcustomercode="SELECT customer_code FROM order_header WHERE order_no='".$trans_id."'";
		$rscustomercode=mysqli_query($link,$sqlcustomercode);
		$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
		$customer_code=$rowcustomercode['customer_code'];
		if($cntchkempdatewise==0)
		{
			$sqlinsertempdatewiseorder="INSERT INTO mis_details_emp_datewise SET 
										operation_date='".$transdate."',emp_code='".$emp_code."',order_received='".$qty."',present=1,
										customer_visited='".$customer_code."'";
			mysqli_query($link,$sqlinsertempdatewiseorder);							
		}
		else
		{
			$sqlupdateempdatewiseorder="UPDATE mis_details_emp_datewise SET order_received=(order_received+".$qty."),
										customer_visited=CASE WHEN customer_visited IS NULL OR customer_visited =  '' THEN  '".$customer_code."' 
										ELSE concat(customer_visited,',".$customer_code."') END WHERE operation_date='".$transdate."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlupdateempdatewiseorder);	
		}
	}
	if($trans_type=='NO' || $trans_type=='NS' || $trans_type=='NSU')
	{
		$coumn_name_tdy="no_transaction_tdy=(no_transaction_tdy+1)";
		$coumn_name_mtd="no_transaction_mtd=(no_transaction_mtd+1)";
		$coumn_name_ytd="no_transaction_ytd=(no_transaction_ytd+1)";
		if($trans_type=='NO')
		{
		  $sqlcustomercodenotrans="SELECT customer_code FROM order_header WHERE order_no='".$trans_id."'";
		}
		else if($trans_type=='NS')
		{
			$sqlcustomercodenotrans="SELECT customer_code FROM stock_audit WHERE  transaction_id='".$trans_id."'";
		}
		$rscustomercodenotrans=mysqli_query($link,$sqlcustomercodenotrans);
		$rowcustomercodenotrans=mysqli_fetch_assoc($rscustomercodenotrans);
		$customer_codenotrans=$rowcustomercodenotrans['customer_code'];
		if($trans_type=='NO' || $trans_type=='NS')
		{
			$customer_code_cond_insert=",customer_visited='".$customer_codenotrans."'";
			$customer_code_cond_update=",customer_visited=CASE WHEN customer_visited IS NULL OR customer_visited =  '' THEN  '".$customer_codenotrans."' 
										ELSE concat(customer_visited,',".$customer_codenotrans."') END ";
		}
		else if($trans_type=='NSU')
		{
			$customer_code_cond_insert="";
			$customer_code_cond_update="";
		}
		
		if($cntchkempdatewise==0)
		{
			$sqlinsertempdatewisenotrans="INSERT INTO mis_details_emp_datewise SET 
										operation_date='".$transdate."',emp_code='".$emp_code."',no_transaction=1".$customer_code_cond_insert.",present=1";
			mysqli_query($link,$sqlinsertempdatewisenotrans);							
		}
		else
		{
			$sqlupdateempdatewisenotrans="UPDATE mis_details_emp_datewise SET no_transaction=(no_transaction+1)".$customer_code_cond_update." WHERE 
										operation_date='".$transdate."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlupdateempdatewisenotrans);	
		}
	}
	if($trans_type=='S')
	{
		/*$coumn_name_tdy="stock_audit_tdy=(stock_audit_tdy+".$qty.")";
		$coumn_name_mtd="stock_audit_mtd=(stock_audit_mtd+".$qty.")";
		$coumn_name_ytd="stock_audit_ytd=(stock_audit_ytd+".$qty.")";*/
		$coumn_name_tdy="stock_audit_tdy=(stock_audit_tdy+1)";
		$coumn_name_mtd="stock_audit_mtd=(stock_audit_mtd+1)";
		$coumn_name_ytd="stock_audit_ytd=(stock_audit_ytd+1)";
		
		$sqlcustomercodestk="SELECT customer_code FROM stock_audit WHERE transaction_id='".$trans_id."'";
		$rscustomercodestk=mysqli_query($link,$sqlcustomercodestk);
		$countcustomercodestk=mysqli_num_rows($rscustomercodestk);
		$rowcustomercodestk=mysqli_fetch_assoc($rscustomercodestk);
		$customer_codestk=$rowcustomercodestk['customer_code'];

		if($cntchkempdatewise==0)
		{
			$sqlinsertempdatewisestock="INSERT INTO mis_details_emp_datewise SET 
										operation_date='".$transdate."',emp_code='".$emp_code."',stock_audit=1,present=1
										,customer_visited='".$customer_codestk."'";
			mysqli_query($link,$sqlinsertempdatewisestock);							
		}
		else
		{
			if($countcustomercodestk==1)
			{
				$sqlupdateempdatewisestock="UPDATE mis_details_emp_datewise SET stock_audit=(stock_audit+1),
											customer_visited=CASE WHEN customer_visited IS NULL OR customer_visited =  '' THEN  '".$customer_codestk."' 
											ELSE concat(customer_visited,',".$customer_codestk."') END WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdateempdatewisestock);
			}
		}
	}
	if($trans_type=='MS')
	{
		$coumn_name_tdy="market_feedback_tdy=(market_feedback_tdy+1)";
		$coumn_name_mtd="market_feedback_mtd=(market_feedback_mtd+1)";
		$coumn_name_ytd="market_feedback_ytd=(market_feedback_ytd+1)";
		$sqlcustomercodemarketfeed="SELECT customer_code FROM mf_stk_audit_header WHERE mf_stk_audit_id='".$trans_id."'";
		$rscustomercodemarketfeed=mysqli_query($link,$sqlcustomercodemarketfeed);
		$rowcustomercodemarketfeed=mysqli_fetch_assoc($rscustomercodemarketfeed);
		$customer_codemarketfeed=$rowcustomercodemarketfeed['customer_code'];

		if($cntchkempdatewise==0)
		{
			$sqlinsertempdatewisemarketfeed="INSERT INTO mis_details_emp_datewise SET 
										operation_date='".$transdate."',emp_code='".$emp_code."',market_feedback=1,present=1,
										customer_visited='".$customer_codemarketfeed."'";
			mysqli_query($link,$sqlinsertempdatewisemarketfeed);							
		}
		else
		{
			$sqlupdateempdatewisemarketfeed="UPDATE mis_details_emp_datewise SET market_feedback=(market_feedback+1),
										customer_visited=CASE WHEN customer_visited IS NULL OR customer_visited =  '' THEN  '".$customer_codemarketfeed."' 
										ELSE concat(customer_visited,',".$customer_codemarketfeed."') END WHERE 
										operation_date='".$transdate."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlupdateempdatewisemarketfeed);	
		}
	}
	if($trans_type=='SU')
	{
		if($trans_sub_type=='KYC')
		{
			$coumn_name_tdy="kyc_tdy=(kyc_tdy+1)";
			$coumn_name_mtd="kyc_mtd=(kyc_mtd+1)";
			$coumn_name_ytd="kyc_ytd=(kyc_ytd+1)";
			if($cntchkempdatewise==0)
			{
				$sqlinsertempdatewisekyc="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."',kyc=1,present=1";
				mysqli_query($link,$sqlinsertempdatewisekyc);							
			}
			else
			{
				$sqlupdateempdatewisekyc="UPDATE mis_details_emp_datewise SET kyc=(kyc+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdateempdatewisekyc);	
			}
		}
		else if($trans_sub_type=='Site Visit')
		{
			$coumn_name_tdy="site_visit_tdy=(site_visit_tdy+1)";
			$coumn_name_mtd="site_visit_mtd=(site_visit_mtd+1)";
			$coumn_name_ytd="site_visit_ytd=(site_visit_ytd+1)";
			if($cntchkempdatewise==0)
			{
				$sqlinsertempdatewisesitevisit="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."',site_visit=1,present=1";
				mysqli_query($link,$sqlinsertempdatewisesitevisit);							
			}
			else
			{
				$sqlupdateempdatewisesitevisit="UPDATE mis_details_emp_datewise SET site_visit=(site_visit+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdateempdatewisesitevisit);	
			}

		}
		else if($trans_sub_type=='Site Lead and Conversion Tracking')
		{
			$coumn_name_tdy="site_lead_and_conversion_tracking_tdy=(site_lead_and_conversion_tracking_tdy+1)";
			$coumn_name_mtd="site_lead_and_conversion_tracking_mtd=(site_lead_and_conversion_tracking_mtd+1)";
			$coumn_name_ytd="site_lead_and_conversion_tracking_ytd=(site_lead_and_conversion_tracking_ytd+1)";
			if($cntchkempdatewise==0)
			{
				$sqlinsertempdatewisesitevisit="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."',site_tracking_conversion=1,present=1";
				mysqli_query($link,$sqlinsertempdatewisesitevisit);							
			}
			else
			{
				$sqlupdateempdatewisesitevisit="UPDATE mis_details_emp_datewise SET site_tracking_conversion=(site_tracking_conversion+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdateempdatewisesitevisit);	
			}

		}
		else if($trans_sub_type=='Technical Meets')
		{
			$coumn_name_tdy="technical_meet_tdy=(technical_meet_tdy+1)";
			$coumn_name_mtd="technical_meet_mtd=(technical_meet_mtd+1)";
			$coumn_name_ytd="technical_meet_ytd=(technical_meet_ytd+1)";
			if($cntchkempdatewise==0)
			{
				$sqlinsertempdatewisemeets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."',technical_meet=1,present=1";
				mysqli_query($link,$sqlinsertempdatewisemeets);							
			}
			else
			{
				$sqlupdateempdatewisemeets="UPDATE mis_details_emp_datewise SET technical_meet=(technical_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdateempdatewisemeets);	
			}
		}
		else if($trans_sub_type=='Counter Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertcountermeets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."',counter_meet =1,present=1";
				mysqli_query($link,$sqlinsertcountermeets);							
			}
			else
			{
				$sqlupdatecountermeets="UPDATE mis_details_emp_datewise SET counter_meet =(counter_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatecountermeets);	
			}
		}
		else if($trans_sub_type=='Mega Mason Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	mega_mason_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET mega_mason_meet=(mega_mason_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Professional Visit')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	professional_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET professional_meet=(professional_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Engineers Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	engineers_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET engineers_meet=(engineers_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='IHB Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	IHB_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET IHB_meet=(IHB_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		
		else if($trans_sub_type=='Contractor Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	contractor_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET contractor_meet=(contractor_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Catch Them Young')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	catch_them_young=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET catch_them_young=(catch_them_young+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		
		else if($trans_sub_type=='PC One Day Training Programme')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	pc_traning_programme=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET pc_traning_programme=(pc_traning_programme+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Customer Guidance Camp')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	customer_guidance_camp=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET customer_guidance_camp=(customer_guidance_camp+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Big Contractor Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	big_contractor_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET big_contractor_meet=(big_contractor_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Small Engineers Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	small_engineers_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET small_engineers_meet=(small_engineers_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Mason Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	mason_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET mason_meet=(mason_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Complain')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	complaint=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET complaint=(complaint+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Dealer/Subdealer Visit')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	dealer_subdealer_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET dealer_subdealer_meet=(dealer_subdealer_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}
		else if($trans_sub_type=='Contractor Meet')
		{
			
			if($cntchkempdatewise==0)
			{
				$sqlinsertmegameets="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."', 	contractor_meet=1,present=1";
				mysqli_query($link,$sqlinsertmegameets);							
			}
			else
			{
				$sqlupdatemegameets="UPDATE mis_details_emp_datewise SET contractor_meet=(contractor_meet+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdatemegameets);	
			}
		}

		else if($trans_sub_type=='Branding')
		{
			$coumn_name_tdy="brand_activity_tdy=(brand_activity_tdy+1)";
			$coumn_name_mtd="brand_activity_mtd=(brand_activity_mtd+1)";
			$coumn_name_ytd="brand_activity_ytd=(brand_activity_ytd+1)";
			if($cntchkempdatewise==0)
			{
				$sqlinsertempdatewisebrandactivity="INSERT INTO mis_details_emp_datewise SET 
											operation_date='".$transdate."',emp_code='".$emp_code."',brand_activity=1,present=1";
				mysqli_query($link,$sqlinsertempdatewisebrandactivity);							
			}
			else
			{
				$sqlupdateempdatewisebrandactivity="UPDATE mis_details_emp_datewise SET brand_activity=(brand_activity+1) WHERE 
											operation_date='".$transdate."' AND emp_code='".$emp_code."'";
				mysqli_query($link,$sqlupdateempdatewisebrandactivity);	
			}
		}
	}
	//echo $curdate;
	//echo $transdate;
	if(strtotime($curdate)==strtotime($transdate))
	{
		$sqlupdatemisdata="UPDATE mis_data_details SET $coumn_name_tdy,$coumn_name_mtd,$coumn_name_ytd WHERE emp_code='".$emp_code."'";
	}
	else
	{
		if($month==substr($trans_id,-10,2) && strtotime($transdate)>=strtotime($financial_year_start) && strtotime($transdate)<=strtotime($financial_year_end))
		{
			$sqlupdatemisdata="UPDATE mis_data_details SET $coumn_name_mtd,$coumn_name_ytd WHERE emp_code='".$emp_code."'";
		}
		else if($month!=substr($trans_id,-10,2) && strtotime($transdate)>=strtotime($financial_year_start) && strtotime($transdate)<=strtotime($financial_year_end)){
			$sqlupdatemisdata="UPDATE mis_data_details SET $coumn_name_ytd WHERE emp_code='".$emp_code."'";
		}
	}
	
	//echo 'fgfgfgfgfg'.$sqlupdatemisdata;
//	if($sqlupdatemisdata!="")
//	mysqli_query($link,$sqlupdatemisdata);
            /*try{
				mysqli_query($link,$sqlupdatemisdata);
				}catch(Exception $e) {
				    
				}*/
	
	//if($nick_name == 'START'){
		if($trans_type == "O" || $trans_type=='MS' || $trans_type=='S' || $trans_type=='NO' || $trans_type=='NS'){
			$hint_remarks = '';
			if($trans_type == "O" || $trans_type=='NO'){
				$customer_code = get_record('order_no','order_header',$trans_id,$link);
				$hint_remarks = get_hint_remarks('order_no','order_header',$trans_id,$link);
			}
			else if($trans_type=='S' || $trans_type=='NS'){
				$customer_code = get_record('transaction_id','stock_audit',$trans_id,$link);
				$hint_remarks = get_hint_remarks('transaction_id','stock_audit',$trans_id,$link);
			}
			else if($trans_type=='MS'){
				$customer_code = get_record('mf_stk_audit_id','mf_stk_audit_header',$trans_id,$link);
				$hint_remarks = '';
			}
			$sql_cust_details = "SELECT customer_name, cust_type, rds_tag, route_code FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_cust_details = mysqli_query($link,$sql_cust_details);
			$row_cust_details = mysqli_fetch_assoc($res_cust_details);
			$customer_name = $row_cust_details['customer_name'] ?? '';
			$cust_type = $row_cust_details['cust_type'] ?? '';
			$route_code = $row_cust_details['route_code'] ?? '';
			$rds_tag = $row_cust_details['rds_tag'] ?? '';
			
			$sql_route = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
			$res_route = mysqli_query($link,$sql_route);
			$row_route = mysqli_fetch_assoc($res_route);
			$route_name = $row_route['route_name'] ?? '';
			
			$sql_customer_visit_details = "INSERT INTO customer_visit_details SET 
													 `emp_code` = '".$emp_code."', 
													 `trans_id` = '".$trans_id."', 
												`customer_code` = '".$customer_code."', 
												`customer_name` = '".addslashes($customer_name)."', 
													`cust_type` = '".$cust_type."', 
												   `route_code` = '".$route_code."', 
												   `route_name` = '".addslashes($route_name)."', 
													  `rds_tag` = '".$rds_tag."', 
											     `hint_remarks` = '".addslashes($hint_remarks)."'";
			mysqli_query($link,$sql_customer_visit_details);
		}
	//}
	if($trans_type == "O" || $trans_type=='NO' || ($trans_type=='SU' && $trans_sub_type=='KYC')){
	     $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
		$sqlemplevel="SELECT level FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsemplevel=mysqli_query($link,$sqlemplevel);
		$rowemplevel=mysqli_fetch_assoc($rsemplevel);
		$emp_level=$rowemplevel['level'];
		$transmonth=substr($trans_id,-14,6);
		$year=substr($transmonth,0,4);
		$monthval=substr($transmonth,4,2);
		if($monthval=='01') $columname='jan_visit_achievement';
		if($monthval=='02') $columname='feb_visit_achievement';
		if($monthval=='03') $columname='mar_visit_achievement';
		if($monthval=='04') $columname='apr_visit_achievement';
		if($monthval=='05') $columname='may_visit_achievement';
		if($monthval=='06') $columname='jun_visit_achievement';
		if($monthval=='07') $columname='jul_visit_achievement';
		if($monthval=='08') $columname='aug_visit_achievement';
		if($monthval=='09') $columname='sep_visit_achievement';
		if($monthval=='10') $columname='oct_visit_achievement';
		if($monthval=='11') $columname='nov_visit_achievement';
		if($monthval=='12') $columname='dec_visit_achievement';
		if($trans_type == "O") $customer_code_update=$customer_code;
		if($trans_type == "NO") $customer_code_update=$customer_codenotrans;
		if($trans_type == "SU") $customer_code_update=$qty;
		
			if($emp_level < 3) $emp_level=$emp_level;
			if($emp_level >=3) $emp_level='L3';
			$sqlupdatelevelvisit="UPDATE sis_level_wise_unique_visit_target_ach SET 
										$columname=CASE WHEN $columname IS NULL OR $columname =  '' THEN  '".$customer_code_update."' 
										ELSE concat($columname,',".$customer_code_update."') END WHERE level='".$emp_level."' AND year='".$year."'";
			mysqli_query($link,$sqlupdatelevelvisit);	
	}
}
function update_achievement_STAR($emp_code,$trans_id,$qty,$trans_type,$trans_sub_type)
{
    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$curdate=$year.'-'.$month.'-'.$date;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-14,8)));
	if($month < 4)
	{
		$financial_year_start=($year-1).'-04-01';
		$financial_year_end=$year.'-03-31';
	}
	else
	{
		$financial_year_start=$year.'-04-01';
		$financial_year_end=($year+1).'-03-31';
	}
	
if($trans_type=='O')
	{
		$sqlcustomercode="SELECT customer_code,branch_code,SUBSTRING(order_no,-14,6) As month FROM order_header WHERE order_no='".$trans_id."'";
		$rscustomercode=mysqli_query($link,$sqlcustomercode);
		$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
		$customer_code=$rowcustomercode['customer_code'];
		$branch_code=$rowcustomercode['branch_code'];
		$month=$rowcustomercode['month'];
		$year=substr($month,0,4);
		$monthval=substr($month,4,2);
		if($monthval=='01') $columname='jan_vol_achievement';
		if($monthval=='02') $columname='feb_vol_achievement';
		if($monthval=='03') $columname='mar_vol_achievement';
		if($monthval=='04') $columname='apr_vol_achievement';
		if($monthval=='05') $columname='may_vol_achievement';
		if($monthval=='06') $columname='jun_vol_achievement';
		if($monthval=='07') $columname='jul_vol_achievement';
		if($monthval=='08') $columname='aug_vol_achievement';
		if($monthval=='09') $columname='sep_vol_achievement';
		if($monthval=='10') $columname='oct_vol_achievement';
		if($monthval=='11') $columname='nov_vol_achievement';
		if($monthval=='12') $columname='dec_vol_achievement';
		$sqlchkbranchvol="SELECT branch_code FROM sis_branch_volume_target_ach WHERE branch_code='".$branch_code."' AND year='".$year."'";
		$rschkbranchvol=mysqli_query($link,$sqlchkbranchvol);
		$cntchkbranchvol=mysqli_num_rows($rschkbranchvol);

		if($cntchkbranchvol==0)
		{
			$sqlinsertbranchvolach="INSERT INTO sis_branch_volume_target_ach SET ";
			$sqlinsertbranchvolach .= " branch_code='".addslashes($branch_code)."'";
			$sqlinsertbranchvolach .= " ,year='".$year."'";
			$sqlinsertbranchvolach .= " ,$columname='".addslashes($qty)."'";
			$sqlinsertbranchvolach .= " , download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlinsertbranchvolach);
		}
		else
		{
			$sqlupdatebranchvolach="UPDATE sis_branch_volume_target_ach SET $columname=($columname+".$qty."),download_time=CURRENT_TIMESTAMP()
										WHERE branch_code='".$branch_code."' AND year='".$year."'";
			mysqli_query($link,$sqlupdatebranchvolach);	
		}
		
		$sqlemplevel="SELECT level FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsemplevel=mysqli_query($link,$sqlemplevel);
		$rowemplevel=mysqli_fetch_assoc($rsemplevel);
		$emp_level=$rowemplevel['level'];
		if($emp_level!='')
		{
			$sqlchklevelvol="SELECT emp_level FROM sis_emp_level_wise_target_ach WHERE emp_level='".$emp_level."' AND year='".$year."'";
			$rschklevelvol=mysqli_query($link,$sqlchklevelvol);
			$cntchklevelvol=mysqli_num_rows($rschklevelvol);
			if($cntchklevelvol==0)
			{
				$sqlinsertlevelvolach="INSERT INTO sis_emp_level_wise_target_ach SET ";
				$sqlinsertlevelvolach .= " emp_level='".addslashes($emp_level)."'";
				$sqlinsertlevelvolach .= " ,year='".$year."'";
				$sqlinsertlevelvolach .= " ,$columname='".addslashes($qty)."'";
				$sqlinsertlevelvolach .= " , download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertlevelvolach);
			}
			else
			{
				$sqlupdatelevelvolach="UPDATE sis_emp_level_wise_target_ach SET $columname=($columname+".$qty."),download_time=CURRENT_TIMESTAMP()
											WHERE emp_level='".$emp_level."' AND year='".$year."'";
				mysqli_query($link,$sqlupdatelevelvolach);	
			}
		}
	}
}
function get_record($column,$table,$trans_id,$link){
    //$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    
    
	$sql_transaction_data = "SELECT customer_code FROM $table WHERE $column = '$trans_id'";
	$res_transaction_data = mysqli_query($link,$sql_transaction_data);
	$row_transaction_data = mysqli_fetch_assoc($res_transaction_data);
	$customer_code = $row_transaction_data['customer_code'];
	return $customer_code;
}
function get_hint_remarks($column,$table,$trans_id,$link){
	//$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	$sql_hint_remarks = "SELECT hint_remarks FROM $table WHERE $column = '".$trans_id."'";
	$res_hint_remarks = mysqli_query($link,$sql_hint_remarks);
	$row_hint_remarks = mysqli_fetch_assoc($res_hint_remarks);
	$hint_remarks = $row_hint_remarks['hint_remarks'];
	return $hint_remarks;
}
function send_hint_remarks_email_sms($orderdataheader_hint_remarks,$reporting_to,$visit_date,$emp_name,$orderdataheader_customer_code,$orderdataheader_d_instruction,$nick_name,$trans_id)
{
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	$sqlbossdetails="SELECT emp_name,email,phone_no FROM employee_master WHERE emp_code='".$reporting_to."'";
	$rsbossdetails=mysqli_query($link,$sqlbossdetails);
	$rowbossdetails=mysqli_fetch_assoc($rsbossdetails);
	$boss_email=$rowbossdetails['email'];
	$boss_phone_no=$rowbossdetails['phone_no'];
	
	$sqlcustomerdetails="SELECT dns_customer_code,customer_name,phone_no,branch_code,address,cust_type FROM customer_master WHERE 
					customer_code='".$orderdataheader_customer_code."'";
	$rscustomerdetails=mysqli_query($link,$sqlcustomerdetails);
	$rowcustomerdetails=mysqli_fetch_assoc($rscustomerdetails);
	$customer_name=$rowcustomerdetails['customer_name'];
	$phone_no_dealer=$rowcustomerdetails['phone_no'];
	$address_dealer=$rowcustomerdetails['address'];
	$type_dealer=$rowcustomerdetails['cust_type'];
	$branch_code=$rowcustomerdetails['branch_code'];
	$branch_code_array=explode(',',$branch_code);
	foreach($branch_code_array as $branch_code_val)
	{
		$sqlselbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code_val."'";
		$rsselbranchname=mysqli_query($link,$sqlselbranchname);
		$rowselbranchname=mysqli_fetch_assoc($rsselbranchname);
		$branch_name_dealer=$branch_name_dealer.$rowselbranchname['branch_name'].',';
	}
	$branch_name_dealer=substr($branch_name_dealer,0,-1);
	
	if($orderdataheader_hint_remarks=='Branding Requirement')
	{
		$email_subject_parts="Branding Requirement";
	}
	if($orderdataheader_hint_remarks=='Technical Requirement')
	{
		$email_subject_parts="Technical Requirement";
	}
	$email_subject=$nick_name.' - '.$email_subject_parts.' by '.$emp_name.' on '.$visit_date.' hrs.';
	$visit_date_parts=date('d-m-Y',strtotime($visit_date));
	
	/*$email_body = "PFA attached the technical requirement for the day :  ( ".$visit_date_parts." ) <br /><br /><br />Powered By aceDNS<br />";

	${excelbodystatic.$trans_id}="Dealer Name/Sub Dealer Name"."\t"."Dealer Name/Sub Dealer Contact Number"."\t"."Branch of Dealer"."\t".
	"Date of Visit"."\t"."Visited By"."\t"."Remarks";
	${excelbody.$trans_id}=${excelbodystatic.$trans_id}."\n".preg_replace('/[\r\n]+/', '',$customer_name)."\t".$phone_no_dealer."\t".$branch_name_dealer."\t".$visit_date."\t".$emp_name."\t".preg_replace('/[\r\n]+/', '',$orderdataheader_d_instruction)."\t"."\n";
	
	$strSid = md5(uniqid(time()));
	$headers='';
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Bcc: ".BCCEMAIL." \r\n" .
				'X-Mailer: PHP/' . phpversion();
	$headers .= "MIME-Version: 1.0\r\n";			
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n";
	$headers .= "This is a multi-part message in MIME format.\n";
	$headers .= "--".$strSid."\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n"; // or UTF-8 //
	$headers .= "Content-Transfer-Encoding: 7bit\n";
	$headers .= $email_body."\n";
	$strContent1 = base64_encode(${excelbody.$trans_id});
	$headers .= "--".$strSid."\n";
	$headers .= "Content-Type: application/octet-stream; name=\"".$email_subject_parts."_".$visit_date.".xls\"\n";
	$headers .= "Content-Transfer-Encoding: base64\n";
	$headers .= "Content-Disposition: attachment; filename=\"".$email_subject_parts."_".$visit_date.".xls\"\n";
	$headers .= $strContent1."\n";		
	
	$spam_filter='-facedns@coral.in';
	if(mail($boss_email, $email_subject, $email_body, $headers,$spam_filter))
	{
		$val='SUCCESS';
	}
	else
	{
		$val='FAILURE';
	}
	
	//For SMS
	${sms_text.$trans_id}="Recommendation ID: "." "."Engineer Name: ".$emp_name." "."Name: ".$customer_name." "."Contact No: ".$phone_no_dealer." "."Site Address: ".$address_dealer." "."Category:  ".$type_dealer."";
	
	$url="http://www.myvaluefirst.com/smpp/sendsms?username=cmclhttp&password=cmcl1234&to=".$boss_phone_no."&udh=&from=STARCM&text=".urlencode(${sms_text.$trans_id})."";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_exec($ch);*/
	//return $val;
}
function update_ach_RUPA($emp_code,$trans_id,$order_UOM,$qty,$UOM1,$UOM2,$conversion_factor)
{
    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$curdate=$year.'-'.$month.'-'.$date;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-14,8)));
	$transmonth=substr($trans_id,-10,2);
	
	if($order_UOM==$UOM1) $qty=$qty;
	if($order_UOM==$UOM2) $qty=$qty/$conversion_factor;
	
	if($transmonth=='01') {
		$sql_update_clause=" jan_31_achievement=(jan_31_achievement+".$qty.")";
		$sql_in_clause=" jan_31_achievement='".$qty."'"; 
	}
	if($transmonth=='02'){ $sql_update_clause=" feb_28_achievement=(feb_28_achievement+".$qty.")";$sql_in_clause=" feb_28_achievement='".$qty."'"; }
	if($transmonth=='03'){ $sql_update_clause=" mar_31_achievement=(mar_31_achievement+".$qty.")";$sql_in_clause=" mar_31_achievement='".$qty."'"; }
	if($transmonth=='04'){ $sql_update_clause=" apr_30_achievement=(apr_30_achievement+".$qty.")";$sql_in_clause=" apr_30_achievement='".$qty."'"; }
	if($transmonth=='05'){ $sql_update_clause=" may_31_achievement=(may_31_achievement+".$qty.")";$sql_in_clause=" may_31_achievement='".$qty."'"; }
	if($transmonth=='06'){ $sql_update_clause=" jun_30_achievement=(jun_30_achievement+".$qty.")";$sql_in_clause=" jun_30_achievement='".$qty."'"; }
	if($transmonth=='07'){ $sql_update_clause=" jul_31_achievement=(jul_31_achievement+".$qty.")";$sql_in_clause=" jul_31_achievement='".$qty."'"; }
	if($transmonth=='08'){ $sql_update_clause=" aug_31_achievement=(aug_31_achievement+".$qty.")";$sql_in_clause=" aug_31_achievement='".$qty."'"; }
	if($transmonth=='09'){ $sql_update_clause=" sep_30_achievement=(sep_30_achievement+".$qty.")";$sql_in_clause=" sep_30_achievement='".$qty."'"; }
	if($transmonth=='10'){ $sql_update_clause=" oct_31_achievement=(oct_31_achievement+".$qty.")";$sql_in_clause=" oct_31_achievement='".$qty."'"; }
	if($transmonth=='11'){ $sql_update_clause=" nov_30_achievement=(nov_30_achievement+".$qty.")";$sql_in_clause=" nov_30_achievement='".$qty."'"; }
	if($transmonth=='12'){ $sql_update_clause=" dec_31_achievement=(dec_31_achievement+".$qty.")";$sql_in_clause=" dec_31_achievement='".$qty."'"; }
	
	$sqlchkempproductgroup="SELECT emp_code,product_group_code FROM self_appraisal_productgroup_wise 
									WHERE emp_code='".$emp_code."'";
	$rschkempproductgroup=mysqli_query($link,$sqlchkempproductgroup);
	$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);
	if($countchkempproductgroup==0){
		$sqlselfappraisal  = "insert into self_appraisal_productgroup_wise SET ";
		$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";
		$sqlselfappraisal .= " , product_group_code=''";
		$sqlselfappraisal .= " , ".$sql_in_clause."";
		$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sqlselfappraisal);
	 }
	else
	{
		$sqlselfappraisalupdate  = "update self_appraisal_productgroup_wise SET ";
		$sqlselfappraisalupdate .= "  ".$sql_update_clause."";
		$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
		mysqli_query($link,$sqlselfappraisalupdate);
	}
}
function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}
function update_ach_DNV($emp_code,$trans_id,$customer_code,$customer_name,$qty,$cust_type,$trans_type,$dns_customer_code)
{
    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	//$curdate=$year.'-'.$month.'-'.$date;
	$curdate=$date.'-'.$month.'-'.$year;
	$curmonth='01-'.$month.'-'.$year;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-14,8)));
	if($month < 4)
	{
		$financial_year_start=($year-1).'-04-01';
		$financial_year_end=$year.'-03-31';
	}
	else
	{
		$financial_year_start=$year.'-04-01';
		$financial_year_end=($year+1).'-03-31';
	}
	$transmonth=substr($trans_id,-10,2);
	
if($trans_type=='O')
	{
	if($transmonth=='01') { $target_ach_month_sel="jan_31_target";}
	if($transmonth=='02') { $target_ach_month_sel="feb_28_target";}
	if($transmonth=='03') { $target_ach_month_sel=" mar_31_target"; }
	if($transmonth=='04'){ $target_ach_month_sel=" apr_30_target"; }
	if($transmonth=='05'){ $target_ach_month_sel=" may_31_target"; }
	if($transmonth=='06'){ $target_ach_month_sel=" jun_30_target";}
	if($transmonth=='07'){ $target_ach_month_sel=" jul_31_target"; }
	if($transmonth=='08'){ $target_ach_month_sel=" aug_31_target"; }
	if($transmonth=='09'){ $target_ach_month_sel=" sep_30_target"; }
	if($transmonth=='10'){ $target_ach_month_sel=" oct_31_target"; }
	if($transmonth=='11'){ $target_ach_month_sel=" nov_30_target"; }
	if($transmonth=='12'){ $target_ach_month_sel=" dec_31_target"; }
	
	$sqlselvolumetarget="SELECT $target_ach_month_sel FROM self_appraisal_summary WHERE customer_code='".$dns_customer_code."'";
	$rsselvolumetarget=mysqli_query($link,$sqlselvolumetarget);
	$rowselvolumetarget=mysqli_fetch_assoc($rsselvolumetarget);
	$volume_target=$rowselvolumetarget[$target_ach_month_sel];
		
		$sqlcustomercodechk="SELECT customer_code FROM emp_target_achievement WHERE emp_code='".$emp_code."' AND 
							customer_code='".$customer_code."' AND month='".$curdate."'";
		$rscustomercodechk=mysqli_query($link,$sqlcustomercodechk);
		$customercodechk=mysqli_num_rows($rscustomercodechk);
		if($customercodechk==0)
		{
			$sqlinsertemptargetach="INSERT INTO emp_target_achievement SET 
									emp_code='".$emp_code."',
									month='".$curdate."',
									customer_code='".$customer_code."',
									customer_name='".$customer_name."',
									cust_type='".$cust_type."',
									volume_target='".$volume_target."',
									volume_achievement='".$qty."'";
			mysqli_query($link,$sqlinsertemptargetach);							
		}
		else
		{
			$sqlupdateemptargetach="UPDATE emp_target_achievement SET volume_target='".$volume_target."', 
									volume_achievement=(volume_achievement+".$qty.") WHERE customer_code='".$customer_code."' AND 
									emp_code='".$emp_code."' AND month='".$curdate."'";
			mysqli_query($link,$sqlupdateemptargetach);	
		}
	}
	if($trans_type=='P')
	{
	/*if($transmonth=='01') { $target_ach_month_sel="jan_31_target";}
	if($transmonth=='02') { $target_ach_month_sel="feb_28_target";}
	if($transmonth=='03') { $target_ach_month_sel=" mar_31_target"; }
	if($transmonth=='04'){ $target_ach_month_sel=" apr_30_target"; }
	if($transmonth=='05'){ $target_ach_month_sel=" may_31_target"; }
	if($transmonth=='06'){ $target_ach_month_sel=" jun_30_target";}
	if($transmonth=='07'){ $target_ach_month_sel=" jul_31_target"; }
	if($transmonth=='08'){ $target_ach_month_sel=" aug_31_target"; }
	if($transmonth=='09'){ $target_ach_month_sel=" sep_30_target"; }
	if($transmonth=='10'){ $target_ach_month_sel=" oct_31_target"; }
	if($transmonth=='11'){ $target_ach_month_sel=" nov_30_target"; }
	if($transmonth=='12'){ $target_ach_month_sel=" dec_31_target"; }
	
	$sqlselvolumetarget="SELECT $target_ach_month_sel FROM self_appraisal_summary WHERE customer_code='".$customer_code."'";
	$rsselvolumetarget=mysqli_query($link,$sqlselvolumetarget);
	$rowselvolumetarget=mysqli_fetch_assoc($rsselvolumetarget);
	$volume_target=$rowselvolumetarget[$target_ach_month_sel];*/
		
		$sqlcustomercodechk="SELECT customer_code FROM emp_target_achievement WHERE emp_code='".$emp_code."' AND 
							customer_code='".$customer_code."' AND month='".$curdate."'";
		$rscustomercodechk=mysqli_query($link,$sqlcustomercodechk);
		$customercodechk=mysqli_num_rows($rscustomercodechk);
		if($customercodechk==0){
			$sqlinsertemptargetach="INSERT INTO emp_target_achievement SET 
									emp_code='".$emp_code."',
									month='".$curdate."',
									customer_code='".$customer_code."',
									customer_name='".$customer_name."',
									cust_type='".$cust_type."',
									collection_target='0',
									collection_achievement='".$qty."'";
			mysqli_query($link,$sqlinsertemptargetach);							
		}
		else
		{
			$sqlupdateemptargetach="UPDATE emp_target_achievement SET  collection_achievement=(collection_achievement+".$qty.") 
									WHERE customer_code='".$customer_code."' AND emp_code='".$emp_code."' AND month='".$curdate."'";
			mysqli_query($link,$sqlupdateemptargetach);	
		}
	}
}
function update_ach_ABDOS($emp_code,$trans_id,$customer_code,$route_code,$qty,$weightage,$trans_type,$product_category)
{
    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$curdate=$year.'-'.$month.'-'.$date;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-14,8)));
	$transmonth=substr($trans_id,-10,2);
	
	if($transmonth=='01') {
		$sql_update_clause=" jan_31_achievement=(jan_31_achievement+".$weightage.")";
		$sql_in_clause=" jan_31_achievement='".$weightage."'"; 
	}
	if($transmonth=='02'){ $sql_update_clause=" feb_28_achievement=(feb_28_achievement+".$weightage.")";$sql_in_clause=" feb_28_achievement='".$weightage."'"; }
	if($transmonth=='03'){ $sql_update_clause=" mar_31_achievement=(mar_31_achievement+".$weightage.")";$sql_in_clause=" mar_31_achievement='".$weightage."'"; }
	if($transmonth=='04'){ $sql_update_clause=" apr_30_achievement=(apr_30_achievement+".$weightage.")";$sql_in_clause=" apr_30_achievement='".$weightage."'"; }
	if($transmonth=='05'){ $sql_update_clause=" may_31_achievement=(may_31_achievement+".$weightage.")";$sql_in_clause=" may_31_achievement='".$weightage."'"; }
	if($transmonth=='06'){ $sql_update_clause=" jun_30_achievement=(jun_30_achievement+".$weightage.")";$sql_in_clause=" jun_30_achievement='".$weightage."'"; }
	if($transmonth=='07'){ $sql_update_clause=" jul_31_achievement=(jul_31_achievement+".$weightage.")";$sql_in_clause=" jul_31_achievement='".$weightage."'"; }
	if($transmonth=='08'){ $sql_update_clause=" aug_31_achievement=(aug_31_achievement+".$weightage.")";$sql_in_clause=" aug_31_achievement='".$weightage."'"; }
	if($transmonth=='09'){ $sql_update_clause=" sep_30_achievement=(sep_30_achievement+".$weightage.")";$sql_in_clause=" sep_30_achievement='".$weightage."'"; }
	if($transmonth=='10'){ $sql_update_clause=" oct_31_achievement=(oct_31_achievement+".$weightage.")";$sql_in_clause=" oct_31_achievement='".$weightage."'"; }
	if($transmonth=='11'){ $sql_update_clause=" nov_30_achievement=(nov_30_achievement+".$weightage.")";$sql_in_clause=" nov_30_achievement='".$weightage."'"; }
	if($transmonth=='12'){ $sql_update_clause=" dec_31_achievement=(dec_31_achievement+".$weightage.")";$sql_in_clause=" dec_31_achievement='".$weightage."'"; }
	
	$sqlchkempproductgroup="SELECT emp_code,product_category,route_code FROM self_appraisal_route_product_group_wise 
									WHERE emp_code='".$emp_code."' AND product_category='".$product_category."' AND route_code='".$route_code."'";
	$rschkempproductgroup=mysqli_query($link,$sqlchkempproductgroup);
	$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);
	/*if($countchkempproductgroup==0){
		$sqlselfappraisal  = "insert into self_appraisal_route_product_group_wise SET ";
		$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";
		$sqlselfappraisal .= " , product_group_code=''";
		$sqlselfappraisal .= " , ".$sql_in_clause."";
		$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sqlselfappraisal);
	 }
	else
	{*/
	if($countchkempproductgroup >0){
		$sqlselfappraisalupdate  = "update self_appraisal_route_product_group_wise SET ";
		$sqlselfappraisalupdate .= "  ".$sql_update_clause."";
		$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' AND product_category='".$product_category."' AND route_code='".$route_code."'";
		mysqli_query($link,$sqlselfappraisalupdate);
	}
	
}

?>