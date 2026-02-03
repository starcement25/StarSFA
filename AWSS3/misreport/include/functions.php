<?php
//Function used for combo population
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
	$rs = mysql_query($sql);
	if (mysql_num_rows($rs) > 0)
	{
		while($rec = mysql_fetch_array($rs))
		{
			if (in_array($rec[$value_field],$id_arr)) $selected = " selected ";
			else  $selected = " ";
			$str_option = $str_option."<option title=\"".stripslashes($rec[$option_field])."\" value=".$rec[$value_field]." ".$selected." >".stripslashes($rec[$option_field])."</option>";
		}
	}
	$str_select_end = "</select>";
	mysql_free_result($rs);
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

	$rs = mysql_query($sql);



	if (mysql_num_rows($rs) > 0)

	{

		while($rec = mysql_fetch_array($rs))

		{

			if (in_array($rec[$value_field],$id_arr)) $selected = " selected ";

			else  $selected = " ";



			$str_option = $str_option."<option title=\"".stripslashes($rec[$option_field])."\" value=".$rec[$value_field]." ".$selected." >".stripslashes($rec[$option_field])."</option>";

		}

	}



	$str_select_end = "</select>";



	mysql_free_result($rs);

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

	$rs = mysql_query($sql);



	if (mysql_num_rows($rs) > 0)

	{

		while($rec = mysql_fetch_array($rs))

		{

			if (in_array($rec[$value_field],$id_arr)) $selected = " selected ";

			else  $selected = " ";



			$str_option = $str_option."<option title=\"".stripslashes($rec[$option_field])."\" value=".$rec[$value_field]." ".$selected." >".stripslashes($rec[$option_field])."</option>";

		}

	}
	$str_select_end = "</select>";
	mysql_free_result($rs);
	return $str_select_start.$str_option.$str_select_end;
}

// function to fetch a single value against a ID

function getValue($TableName,$ID,$ID_Name,$FieldNames)

{

	$sql_getValue = "SELECT ".$FieldNames." FROM ".$TableName." WHERE ".$ID_Name."='".$ID."'";

	$query_getValue = mysql_query($sql_getValue);



	$rs_getValue = mysql_fetch_array($query_getValue);



	if (mysql_num_rows($query_getValue) > 0) return $rs_getValue[0];

	else return "";



	mysql_free_result($query_getValue);

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
	$rs  = mysql_query($sql);
	$rec = mysql_fetch_array($rs);
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

		<!--td width="15%" align="left" style="text-align:left;"><? /*$_REQUEST[pageNo];if($_REQUEST[pageNo]!=1 && $_REQUEST[pageNo]!=''){ ?>

			<a href="javascript:prevPage(<?=$_REQUEST[pageNo] ?>);" onMouseOut="javascript:window.status='Done';" onMouseMove="javascript:window.status='Go to Previous Page';" class="l2">&#171; Previous</a>

			<? }else{ ?>

			<!--<a href="#" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to Previous Page';"><font size="3">&#171;</font> Prev</a>-->

			<? }*/?>

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

			<? }*/?>

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

<?
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
function return_employee_hierarchy($emp_code) {
    $emphierarchy = array();
    employee_hierarchy_details($emp_code, $emphierarchy);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1).','."'".$emp_code."'";
    return $emphierarchystring;
}
function employee_hierarchy_details($emp_code,&$emphierarchy){
   //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
   $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to)";
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
			employee_hierarchy_details($rowemphierarchy['emp_code'],$emphierarchy);
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
function return_employee_hierarchy_commaseperated($emp_code) {
    $emphierarchy = array();
	$emp_code=str_replace("'","",$emp_code);
	$emp_code_array=explode(',',$emp_code);
	foreach($emp_code_array as $emp_code_commaseperated)
	{
    	employee_hierarchy_details_commaseperated($emp_code_commaseperated, $emphierarchy);
	}
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	foreach($emp_code_array as $emp_code_commaseperated)
	{
		$emphierarchystring=$emphierarchystring.','."'".$emp_code_commaseperated."'";
	}
    return $emphierarchystring;
}
function employee_hierarchy_details_commaseperated($emp_code,&$emphierarchy){
   //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
   $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to) AND acedns='Y'";
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
			employee_hierarchy_details_commaseperated($rowemphierarchy['emp_code'],$emphierarchy);
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
function return_employee_hierarchy_sate_commaseperated($emp_code,$state) {
    $emphierarchy = array();
	$emp_code=str_replace("'","",$emp_code);
	$emp_code_array=explode(',',$emp_code);
	foreach($emp_code_array as $emp_code_commaseperated)
	{
    	employee_hierarchy_details_state_commaseperated($emp_code_commaseperated, $emphierarchy,$state);
	}
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	foreach($emp_code_array as $emp_code_commaseperated)
	{
		$emphierarchystring=$emphierarchystring.','."'".$emp_code_commaseperated."'";
	}
    return $emphierarchystring;
}
function employee_hierarchy_details_state_commaseperated($emp_code,&$emphierarchy,$state){
   //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
   if($state == 'all')
		$state_condition = " state!=''";
	else
		$state_condition = " FIND_IN_SET(".$state.", state)";
	
   $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to) AND acedns='Y' AND ".$state_condition;
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
			employee_hierarchy_details_state_commaseperated($rowemphierarchy['emp_code'],$emphierarchy,$state);
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
    employee_upper_hierarchy_details($emp_code, $emphierarchy);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
    return $emphierarchystring;
}
function employee_upper_hierarchy_details($emp_code,&$emphierarchy){
   $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
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
	$rsfetchemails=mysql_query($sqlfetchemails) or die(mysql_error().'Error in mail id fetch.');
	while($rowfetchemails=mysql_fetch_array($rsfetchemails))
	{
		$correspondingemails=$correspondingemails.$rowfetchemails['mail_id'].',';
	}
	$correspondingemails=substr($correspondingemails,0,-1);
	return $correspondingemails;
}
function generate_price_details($distinct_dnsprod_code,$distinct_branch_code)
{
	//For live
	$sqlplant="SELECT plant_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
	$rsplant=mysql_query($sqlplant);
	$rowplant=mysql_fetch_array($rsplant);
	$plant_name=$rowplant['plant_name'];
	
	$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
						dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
	$rsconversionfactor=mysql_query($sqlconversionfactor);
	$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
	${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
	${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
	${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];
	
	$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
	$rschkformulation=mysql_query($sqlchkformulation);
	$rowchkformulation=mysql_fetch_array($rschkformulation);
	$is_formulation=$rowchkformulation['formulation'];
	
		if($is_formulation=='yes')
		 {
				$sqlprocesscost="SELECT process_cost FROM process_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
								  AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rsprocesscost=mysql_query($sqlprocesscost);
				$rowprocesscost=mysql_fetch_array($rsprocesscost);
				${process_cost.$distinct_dnsprod_code}=$rowprocesscost['process_cost'];
							
				//loose oilrate fetching
				$sqlprodwiseformulation="SELECT * FROM(SELECT prod_code,formulation,oils FROM loose_oilrate_formulation 
								WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND plant_name='".$plant_name."' 
								AND prod_code='".$distinct_dnsprod_code."' ORDER BY datetime DESC) AS SAT GROUP BY 3 ";
				$rsprodwiseformulation=mysql_query($sqlprodwiseformulation);
				while($rowprodwiseformulation=mysql_fetch_array($rsprodwiseformulation))
				{					
					$sqllooserate="SELECT oils_rate FROM pricing_detials_formulation WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 
							plant_name='".$plant_name."' AND oils='".$rowprodwiseformulation['oils']."' ORDER BY datetime DESC LIMIT 0,1";
					$rslooserate=mysql_query($sqllooserate);
					$rowlooserate=mysql_fetch_array($rslooserate);
					$oils_rate_ton=$rowlooserate['oils_rate'];
					
				 ${loosrate_calc_val.$distinct_dnsprod_code}=(substr($rowprodwiseformulation['formulation'],0,-1)*$oils_rate_ton)/100;
				 ${loosrate_final_val.$distinct_dnsprod_code}=${loosrate_final_val.$distinct_dnsprod_code}+${loosrate_calc_val.$distinct_dnsprod_code};
				}
				${loosrate_final_val.$distinct_dnsprod_code}=${loosrate_final_val.$distinct_dnsprod_code}+${process_cost.$distinct_dnsprod_code};	  
			}
		  else
			{
				$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 
									plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rslooserate=mysql_query($sqllooserate);
				$rowlooserate=mysql_fetch_array($rslooserate);
				${loosrate_final_val.$distinct_dnsprod_code}=$rowlooserate['loose_rate_ton'];					
			}
	
			$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' 
								AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
			$rspackingprodwise=mysql_query($sqlpackingprodwise);
			$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
			${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
			${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
		
			/*$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
									AND branch_code='".$distinct_branch_code."' AND 
									vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
			$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
			$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
			${depot_cost.$distinct_dnsprod_code}=$rowdepotcostprodwise['depot_cost'];
			if(${depot_cost.$distinct_dnsprod_code}=='')
			{
				${depot_cost.$distinct_dnsprod_code}=0;
			}
			
			$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' AND vertical_value='".$_SESSION['vertical_value']."' 
										ORDER BY datetime DESC LIMIT 0,1";
			$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
			$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
			${margin_cost.$distinct_dnsprod_code}=$rowmargincostprodwise['margin_cost'];
			if(${margin_cost.$distinct_dnsprod_code}=='')
			{
				${margin_cost.$distinct_dnsprod_code}=0;
			}
			
			$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' AND 
										vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
			$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
			$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
			${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
			if(${freight_cost.$distinct_dnsprod_code}=='')
			{
				${freight_cost.$distinct_dnsprod_code}=0;
			}
			
			$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
			$rshoneycombcostprodwise=mysql_query($sqlhoneycombcostprodwise);
			$rowhoneycombcostprodwise=mysql_fetch_array($rshoneycombcostprodwise);
			${honeycomb_cost.$distinct_dnsprod_code}=$rowhoneycombcostprodwise['honeycomb_cost'];
			if(${honeycomb_cost.$distinct_dnsprod_code}=='')
			{
				${honeycomb_cost.$distinct_dnsprod_code}=0;
			}*/
			
			$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$distinct_dnsprod_code."' 
										AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
			$rsdetentioncostprodwise=mysql_query($sqldetentioncostprodwise);
			$rowdetentioncostprodwise=mysql_fetch_array($rsdetentioncostprodwise);
			${detention_cost.$distinct_dnsprod_code}=$rowdetentioncostprodwise['detention_cost'];
			if(${detention_cost.$distinct_dnsprod_code}=='')
			{
				${detention_cost.$distinct_dnsprod_code}=0;
			}

		$loose_rate_case_prodwise=round((${loosrate_final_val.$distinct_dnsprod_code}/${conversion_factor_two.$distinct_dnsprod_code}),2);
		$loose_rate_case_prodwise=round(($loose_rate_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
		
		$mrp_prodwise=$loose_rate_case_prodwise+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${honeycomb_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
		$mrp_prodwise=round($mrp_prodwise,2);
		
		$basic_rate_prodwise=$loose_rate_case_prodwise+${packing_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
		$basic_rate_prodwise=round($basic_rate_prodwise,2);
		//$primary_freight=round(${freight_cost.$distinct_dnsprod_code},2);
		//$depot_cost=${depot_cost.$distinct_dnsprod_code};

	//exit();
	
	$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code."' AND dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";
	$rs_prod_code=mysql_query($sql_prod_code);
	$cntprod_code=mysql_num_rows($rs_prod_code);
	$row_prod_code=mysql_fetch_array($rs_prod_code);
	$prod_code_master=$row_prod_code['prod_code'];
	$vertical_value_master=$row_prod_code['vertical_value'];
	
	if($cntprod_code >0)
	{
		$sqlbranchprodchk="SELECT product_code FROM sauda_mrp WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
		$rsbranchprodchk=mysql_query($sqlbranchprodchk);
		$cntbranchprodchk=mysql_num_rows($rsbranchprodchk);
		if($cntbranchprodchk >0)						{
			$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='".$mrp_prodwise."',sale_rate='".$mrp_prodwise."',
								basic_rate='".$basic_rate_prodwise."',download_time=CURRENT_TIMESTAMP() 
								WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
			mysql_query($sqlupdatemrpprodwise);
		}
		else
		{
			$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from sauda_mrp";
			$rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
			$rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
			$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
			$max_mrp_code++;
			$max_mrp_code='z'.$max_mrp_code;

		   $sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',mrp='".$mrp_prodwise."',sale_rate='".$mrp_prodwise."',
									branch_code='".$distinct_branch_code."',product_code='".$prod_code_master."',
									vertical_value='".$vertical_value_master."',basic_rate='".$basic_rate_prodwise."',download_time=CURRENT_TIMESTAMP()";
		   mysql_query($sqlinsertmrpprodwise);
		}
	}
}
function fileUpload($upload_dir, $file_name, $tmp_name, $file_size, $file_type,$sql = "")

{

	$flag = 0;

	if($file_name != "")// && $file_size < 2097152

	{

		$upload_file = $upload_dir.$file_name;

		if($sql != "")//for update image

		{

			$sql_rs = mysql_query($sql) or die(mysql_error());

			$sql_data = mysql_fetch_row($sql_rs);

			$image_to_unlink = $sql_data[0];



			//echo($image_to_unlink);										

			if(file_exists($upload_dir.$image_to_unlink) && $upload_dir.$image_to_unlink != $upload_dir)

			{		

				copy($upload_dir.$image_to_unlink, $upload_dir.$image_to_unlink.".bak"); //create the backup file

				unlink($upload_dir.$image_to_unlink); //delete the specified file

				switch($file_type)

				{

					case "image":

						$extn = strstr($file_name,".");

						//echo($extn);

						$new_extn = strtoupper($extn);

						if($new_extn == ".JPG" || $new_extn == ".GIF" ||  $new_extn == ".JPEG" ||  $new_extn == ".PNG" || $new_extn == ".BMP")

						{			

							if(move_uploaded_file($tmp_name,$upload_file))

							{										

								unlink($upload_dir.$image_to_unlink.".bak"); //delete the backup file

								$GLOBALS['err'] = "Your file have uploaded successfully!!!";

							}

							else

							{

								copy($upload_dir.$image_to_unlink.".bak",$upload_dir.$image_to_unlink);

								unlink($upload_dir.$image_to_unlink.".bak");



								$GLOBALS['err'] = "A error occoured during uploading your file!!!";

								$flag = 1;

								return $flag;

							}

						}

						else

						{

							copy($upload_dir.$image_to_unlink.".bak",$upload_dir.$image_to_unlink);

							unlink($upload_dir.$image_to_unlink.".bak");



							$GLOBALS['err'] = "File should be an Image!!!";



							$flag = 1;

							return $flag;

						}

					break;

					case "general":

						if(move_uploaded_file($tmp_name,$upload_file))

						{

							unlink($upload_dir.$image_to_unlink.".bak"); //delete the backup file								

							$GLOBALS['err'] = "Your file have uploaded successfully!!!";

						}

						else

						{

							copy($upload_dir.$image_to_unlink.".bak",$upload_dir.$image_to_unlink);

							unlink($upload_dir.$image_to_unlink.".bak");



							$GLOBALS['err'] = "A error occoured during uploading your file!!!";



							$flag = 1;

							return $flag;

						}

					break;

				}																	

			}		

			else

			{

				switch($file_type)

				{

					case "image":

						$extn = strstr($file_name,".");

						$new_extn = strtoupper($extn);



						if($new_extn == ".JPG" || $new_extn == ".GIF" ||  $new_extn == ".JPEG" ||  $new_extn == ".PNG" || $new_extn == ".BMP")

						{

							if(move_uploaded_file($tmp_name,$upload_file))

							{

								$GLOBALS['err']= "Your file have uploaded successfully!!!";

							}

							else

							{

								$GLOBALS['err']= "A error occoured during uploading your file!!!";



								$flag=1;

								return $flag;

							}

						}

						else

						{

							$GLOBALS['err']= "File should be an Image!!!";



							$flag=1;

							return $flag;

						}

					break;

					case "general":

						if(move_uploaded_file($tmp_name,$upload_file))

						{

							$GLOBALS['err'] = "Your file have uploaded successfully!!!";

						}

						else

						{

							$GLOBALS['err'] = "An error occoured during uploading your file!!!";



							$flag=1;

							return $flag;

						}

					break;

				}//end of switch

			}//end of unlink checking

		}//

		else//for insert image

		{

			switch($file_type)

			{

				case "image":

					$extn=strstr($file_name,".");

					$new_extn = strtoupper($extn);



					if($new_extn==".JPG" || $new_extn==".GIF" ||  $new_extn==".JPEG" ||  $new_extn==".PNG")

					{

						if(move_uploaded_file($tmp_name,$upload_file))

						{

							$GLOBALS['err'] = "Your file have uploaded successfully!!!";

						}

						else

						{

							$GLOBALS['err'] = "A error occoured during uploading your file!!!";



							$flag=1;

							return $flag;

						}

					}

					else

					{

						$GLOBALS['err'] = "File should be an Image!!!";



						$flag=1;

						return $flag;

					}

				break;

				case "general":

					if(move_uploaded_file($tmp_name,$upload_file))

					{

						$GLOBALS['err'] = "Your file have uploaded successfully!!!";

					}

					else

					{

						$GLOBALS['err'] = "A error occoured during uploading your file!!!";



						$flag=1;

						return $flag;

					}

				break;

			}//end of switch

		}//end of sql checking

	}//end of size and file checking

	else

	{

		$GLOBALS['err'] = "Your file is too big to upload!!!";

		$flag=1;

		return;

	}		

	return $flag;

}
function MakeThumbnail($Image_path, $flname, $width, $height,$orginal="")

{

	$image_name = $flname;  //Image path retrived 

	$image_path = $Image_path;



	list($temp_width, $temp_height, $temp_type, $temp_attr) = getimagesize($Image_path.$flname);

	

	if($temp_width > $width)

	{

		$height = ceil(($temp_height*$width)/$temp_width);

	}

    if($temp_width <= $width)

	{

		$width = $temp_width;

		$height = $temp_height;

	}

	//Identifying Image type 

    $len = strlen($image_name); 

    $pos =strpos($image_name,"."); 

    $type = substr($image_name,$pos + 1,$len); 

	$type_new = strtolower($type);



    if ($type_new=="jpeg" || $type_new=="jpg") 

    { 

        thumb_jpeg ($image_name, $image_path, $width, $height,$orginal); //Call to jpeg function 

    } 

    else if($type_new=="png" || $type_new=="PNG") 

    { 

        thumb_png ($image_name, $image_path, $width, $height,$orginal);  //Call to PNG function 

    } 

	else if($type_new=="gif" || $type_new=="GIF") 

    { 

        thumb_gif ($image_name, $image_path, $width, $height,$orginal);  //Call to PNG function 

    } 

}



//JPEG function 

function thumb_jpeg($image_name, $image_path, $width, $height,$orginal="") 

{ 

    $source_path = $image_path; 

	if($orginal=="")

    $destination_path = $image_path."thumbnail/thumb_";

	else 

    $destination_path = $image_path.$orginal;

	

    $new_width=$width; 

    $new_height=$height; 



    $destimg=imagecreatetruecolor($new_width,$new_height) or die("Problem In Creating image"); 

    $srcimg=ImageCreateFromJPEG($source_path.$image_name) or die("Problem In opening Source Image"); 



    ImageCopyResized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg)) or die("Problem In resizing"); 

    ImageJPEG($destimg,$destination_path.$image_name) or die("Problem In saving");



	chmod($destination_path.$image_name,0777);

} 



//PNG function 



function thumb_png($image_name, $image_path, $width, $height,$orginal="") 

{ 

    $source_path = $image_path; 

	if($orginal=="")

    	$destination_path = $image_path."thumbnail/thumb_";

	else 

    	$destination_path = $image_path.$orginal;



    $new_width=$width; 

    $new_height=$height; 



	$destimg=imagecreatetruecolor($new_width,$new_height) or die("Problem In Creating image"); 

    $srcimg=ImageCreateFromPNG($source_path.$image_name) or die("Problem In opening Source Image"); 





	ImageCopyResized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg)) or die("Problem In resizing"); 

    ImagePNG($destimg,$destination_path.$image_name) or die("Problem In saving"); 



	chmod($destination_path.$image_name,0777);

} 



//GIF function 



function thumb_gif($image_name, $image_path, $width, $height,$orginal="") 

{ 

    $source_path = $image_path; 

	if($orginal=="")

    	$destination_path = $image_path."thumbnail/thumb_";

	else 

    	$destination_path = $image_path.$orginal;



    $new_width=$width; 

    $new_height=$height; 



    $destimg=imagecreatetruecolor($new_width,$new_height) or die("Problem In Creating image"); 

    $srcimg=ImageCreateFromGIF($source_path.$image_name) or die("Problem In opening Source Image"); 



	ImageCopyResized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg)) or die("Problem In resizing"); 

    ImageGIF($destimg,$destination_path.$image_name) or die("Problem In saving");



	chmod($destination_path.$image_name,0777);

} 
?>