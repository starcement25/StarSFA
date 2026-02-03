<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];
require("include/config.php");
require("include/config-setup.php");
	if($mode =='excel_download')		excelDownload();
	else  disphtml("main();");
//ob_end_flush();

function main()
{
?>
<script language="javascript">
function check()
{
	var is_checked=false;
	var start_date = document.getElementById("from_date").value;
	var end_date = document.getElementById("to_date").value;
	var date1parts=start_date.split('-');
	var startdateval=date1parts[2]+'-'+date1parts[1]+'-'+date1parts[0];
	var date2parts=end_date.split('-');
	var enddateval=date2parts[2]+'-'+date2parts[1]+'-'+date2parts[0];
	if(document.getElementById("from_date").value.search(/\S/) == -1){
		alert('Provide From date');
		return false;
	}
	if(document.getElementById("to_date").value.search(/\S/) == -1){
		alert('Provide To date');
		return false;
	}
	if(startdateval>enddateval){
		alert("From date cannot be greater than To date");
		return false;
	}
	
	 var date1 = new Date(startdateval);
		 var date2 = new Date(enddateval);
  		 var Difference_In_Time = date2.getTime() - date1.getTime();
		  //alert(Difference_In_Time);
		// To calculate the no. of days between two dates
		 var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
		 //alert(Difference_In_Days);
		//alert(Difference_In_Days);
		if((Difference_In_Days+1) >181)
		{
			alert("You can check maximam 180 days report.");
			return false;
		}
	return true;
}
</script>

<table cellpadding="4px" width="50%" class="border" align="center">
    <tr class="TDHEAD_SUB">
        <td align="center">Site Data Download</td>
    </tr>
    <tr><td align="center">
 <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
<input type="hidden" name="mode" value="excel_download">
<table cellpadding="4px">
        <tr >
        <td align="right" width="25%">From Date:</td>
        <td align="left">
       <?php $from_date=$_REQUEST['from_date'];?>
      <input id="from_date" type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date"></input>&nbsp;
        <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18" ></a>
    </label>
    <script language="JavaScript" type="text/javascript">
        <!-- // create calendar object(s) just after form tag closed
         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
         // note: you can have as many calendar objects as you need for your application
        var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
        cal5.year_scroll = true;
        cal5.time_comp = false;
        //-->
    </script>
    </td>
  </tr>
  <tr>
        <td align="right" width="25%">To Date:</td>
        <td align="left">
        <?php $to_date=$_REQUEST['to_date'];?>
         <input id="to_date" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="to_date"></input>&nbsp;
            <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18" ></a>
        </label>
        <script language="JavaScript" type="text/javascript">
            <!-- // create calendar object(s) just after form tag closed
             // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
             // note: you can have as many calendar objects as you need for your application
            var cal6 = new calendar3(document.forms['frmSearch'].elements['to_date']);
            cal6.year_scroll = true;
            cal6.time_comp = false;
            //-->
        </script>
    </td>
  </tr>
  <tr >
    <td></td>
    <td align="left"><input name="submit" type="submit" value="Submit" id="submitdata" ></td>
  </tr>
    </table>
    </form>
    </td>
    </tr>
</table>
  <br />
<?php 
}
function excelDownload()
{
		require("include/dbcon.php");

	$from_date=date('Y-m-d',strtotime($_REQUEST['from_date']));
	$to_date=date('Y-m-d',strtotime($_REQUEST['to_date']));
	$survey_type = $_REQUEST['survey_type'];
	if($from_date!='' && $to_date!='')
	{
	  $date_condition=" AND DATE_FORMAT(SUBSTRING(SM.site_id,-14,8),'%Y-%m-%d') >='".$from_date."' AND 
					  	DATE_FORMAT(SUBSTRING(SM.site_id,-14,8),'%Y-%m-%d') <='".$to_date."'";
	}
	
$excelheader='Sr. No'."\t".'Unique Store ID'."\t".'Emp Name'."\t".'Survey Date'."\t".'Lattitude'."\t".'Longitude'."\t".'Site Name'."\t".'Contact Person'."\t".'Contact Person Phone no'."\t".'Designation'."\t".'Contact Person Type'."\t".'Email Id'."\t".'Check in Date'."\t".'Address'."\t".'Sub Area'."\t".'Location'."\t".'Area'."\t".'City'."\t".'State'."\t".'Pin'."\t".'Phone no'."\t".'Scope of Teak'."\t".'Scope of NTD'."\t".'Site referred by'."\t".'Name'."\t".'Status'."\t".'Next follow up date'."\t".'Escalation Clause'."\t".'Architect involved'."\t".'Contractor involved'."\t".'Dealer involved'."\t".'Sub Dealer involved'."\t".'Auth Retailer involved'."\t".'Stage Of Construction'."\t".'Expected month of maturity'."\t".'Category'."\t".'Qty'."\t".'Value'."\t".'Desc'."\t".'Spices'."\t".'Remarks';
				
				  //echo ${row_id_string.'SUE016320201014100700'};
$sql_survey_output="SELECT SM.*,DATE_FORMAT(SUBSTRING(SM.site_id,-14,8),'%d-%m-%Y') AS survey_date,EM.emp_name,LO.latt,LO.longi,
					(SELECT GROUP_CONCAT(emp_name SEPARATOR ';')  FROM employee_master WHERE  FIND_IN_SET(emp_code,REPLACE(SM.escalation_clause,';',','))) AS escalation_clause_name,
				(SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') FROM facilitator_master WHERE  FIND_IN_SET(f_code,REPLACE(SM.architect_involved,';',','))) AS architect_involved_name,
				(SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') FROM facilitator_master WHERE  FIND_IN_SET(f_code,REPLACE(SM.contractor_involved,';',','))) AS contractor_involved_name,
				(SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') FROM facilitator_master WHERE  FIND_IN_SET(f_code,REPLACE(SM.auth_retailer_involved,';',','))) AS auth_retailer_involved_name,
				(SELECT GROUP_CONCAT(customer_name SEPARATOR ';') FROM customer_master WHERE  FIND_IN_SET(customer_code,REPLACE(SM.dealer_involved,';',',')))  AS dealer_involved_name,
				(SELECT GROUP_CONCAT(customer_name SEPARATOR ';') FROM customer_master WHERE  FIND_IN_SET(customer_code,REPLACE(SM.sub_dealer_involved,';',','))) AS sub_dealer_involved_name
				 FROM site_master SM, employee_master EM,location LO WHERE 
				 SUBSTRING(SM.site_id,2,5)=EM.emp_code AND REPLACE(SM.site_id,'S','SU')=LO.trans_id ".$date_condition." ORDER BY DATE_FORMAT(SUBSTRING(SM.site_id,-14,8),'%Y-%m-%d') ASC ";
				 
				  $rs_survey_output=mysqli_query($link,$sql_survey_output);
				  $output_no=1; 
				  while($row_survey_output=mysqli_fetch_assoc($rs_survey_output))
				  {
					$site_id= $row_survey_output['site_id'];
					$lattitude=$row_survey_output['latt'];
					$longitude=$row_survey_output['longi'];
					$emp_name=$row_survey_output['emp_name'];
					$survey_date=$row_survey_output['survey_date'];
					$site_name=preg_replace('/[\r\n]+/', '',$row_survey_output['site_name']);
					$contact_person=preg_replace('/[\r\n]+/', '',$row_survey_output['contact_person']);
					$phone_no=$row_survey_output['phone_no'];
					$designation=$row_survey_output['designation'];
					$contact_person_type=$row_survey_output['contact_person_type'];
					$email=$row_survey_output['email'];
					$check_in_date=$row_survey_output['check_in_date'];
					$address=preg_replace('/[\r\n]+/', '',$row_survey_output['address']);
					$sub_area=preg_replace('/[\r\n]+/', '',$row_survey_output['sub_area']);
					$location=preg_replace('/[\r\n]+/', '',$row_survey_output['location']);
					$area=preg_replace('/[\r\n]+/', '',$row_survey_output['area']);
					$city=preg_replace('/[\r\n]+/', '',$row_survey_output['city']);
					$state=$row_survey_output['state'];
					$pin=$row_survey_output['pin'];
					$scope_of_teak=$row_survey_output['scope_of_teak'];
					$scope_of_NTD=$row_survey_output['scope_of_NTD'];
					$site_reffered_by=$row_survey_output['site_reffered_by'];
					$current_status=$row_survey_output['current_status'];
					$follow_up_date=$row_survey_output['follow_up_date'];
					$escalation_clause=$row_survey_output['escalation_clause'];
					/*$escalation_clause_value=str_replace(";",",",$escalation_clause);
					$sqlempname="SELECT GROUP_CONCAT(emp_name SEPARATOR ';') AS escalation_clause_name FROM employee_master WHERE  
							FIND_IN_SET(emp_code,'".$escalation_clause_value."')";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);*/
					$escalation_clause_name=$row_survey_output['escalation_clause_name'];
					
					$architect_involved=$row_survey_output['architect_involved'];
					/*$architect_involved_value=str_replace(";",",",$architect_involved);
					$sqlarchitectname="SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') AS architect_involved_name FROM facilitator_master WHERE  
							FIND_IN_SET(f_code,'".$architect_involved_value."')";
					$rsarchitectname=mysqli_query($link,$sqlarchitectname);
					$rowarchitectname=mysqli_fetch_assoc($rsarchitectname);*/
					$architect_involved_name=$row_survey_output['architect_involved_name'];

					$contractor_involved=$row_survey_output['contractor_involved'];
					/*$contractor_involved_value=str_replace(";",",",$contractor_involved);
					$sqlcontractorname="SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') AS contractor_involved_name FROM facilitator_master WHERE  
							FIND_IN_SET(f_code,'".$contractor_involved_value."')";
					$rscontractorname=mysqli_query($link,$sqlcontractorname);
					$rowcontractorname=mysqli_fetch_assoc($rscontractorname);*/
					$contractor_involved_name=$row_survey_output['contractor_involved_name'];
					
					$dealer_involved=$row_survey_output['dealer_involved'];
					/*$dealer_involved_value=str_replace(";",",",$dealer_involved);
					$sqldealername="SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') AS dealer_involved_name FROM facilitator_master WHERE  
							FIND_IN_SET(f_code,'".$dealer_involved_value."')";
					$rsdealername=mysqli_query($link,$sqldealername);
					$rowdealername=mysqli_fetch_assoc($rsdealername);*/
					$dealer_involved_name=$row_survey_output['dealer_involved_name'];
					
					$sub_dealer_involved=$row_survey_output['sub_dealer_involved'];
					/*$sub_dealer_involved_value=str_replace(";",",",$sub_dealer_involved);
					$sqlsubdealername="SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') AS sub_dealer_involved_name FROM facilitator_master WHERE  
							FIND_IN_SET(f_code,'".$sub_dealer_involved_value."')";
					$rssubdealername=mysqli_query($link,$sqlsubdealername);
					$rowsubdealername=mysqli_fetch_assoc($rssubdealername);*/
					$sub_dealer_involved_name=$row_survey_output['sub_dealer_involved_name'];
					
					$auth_retailer_involved=$row_survey_output['auth_retailer_involved'];
					/*$auth_retailer_involved_value=str_replace(";",",",$auth_retailer_involved);
					$sqlauthretailername="SELECT GROUP_CONCAT(facilitator_name SEPARATOR ';') AS auth_retailer_involved_name FROM facilitator_master WHERE  
							FIND_IN_SET(f_code,'".$auth_retailer_involved_value."')";
					$rsauthretailername=mysqli_query($link,$sqlauthretailername);
					$rowauthretailername=mysqli_fetch_assoc($rsauthretailername);*/
					$auth_retailer_involved_name=$row_survey_output['auth_retailer_involved_name'];
					
					$stage_of_construction=$row_survey_output['stage_of_construction'];
					$expected_month_maturity=$row_survey_output['expected_month_maturity'];
					$countfirstpart=1;
					$product_cat_first_part=explode("$",$row_survey_output['product_category']);
							foreach($product_cat_first_part as $product_cat_first_part_val)
							{
								$TD_count=1;
								
								$product_cat_second_part=explode("#",$product_cat_first_part_val);
								//print_r($product_cat_second_part);
								$product_cat_second_part_sub_val=explode(":",$product_cat_second_part[0]);
								${'category'.$site_id}=$product_cat_second_part_sub_val[0];
								${'qty'.$site_id}=$product_cat_second_part_sub_val[1];
								${'amtvalue'.$site_id}=$product_cat_second_part[1];
								${'desc'.$site_id}=preg_replace('/[\r\n]+/', '',$product_cat_second_part[2]);
								${'species'.$site_id}=preg_replace('/[\r\n]+/', '',$product_cat_second_part[3]);
							}
					$remarks=preg_replace('/[\r\n]+/', '',$row_survey_output['remarks']);
					
$valuedata .=$output_no."\t".$site_id."\t".$emp_name."\t".$survey_date."\t".$lattitude."\t".$longitude."\t".$site_name."\t".$contact_person."\t".$phone_no."\t".$designation."\t".$contact_person_type."\t".$email."\t".$check_in_date."\t".$address."\t".$sub_area."\t".$location."\t".$area."\t".$city."\t".$state."\t".$pin."\t".$phone_no."\t".$scope_of_teak."\t".$scope_of_NTD."\t".$site_reffered_by."\t".$site_reffered_by."\t".$current_status."\t".$follow_up_date."\t".$escalation_clause_name."\t".$architect_involved_name."\t".$contractor_involved_name."\t".$dealer_involved_name."\t".$sub_dealer_involved_name."\t".$auth_retailer_involved_name."\t".$stage_of_construction."\t".$expected_month_maturity."\t".${'category'.$site_id}."\t".${'qty'.$site_id}."\t".${'amtvalue'.$site_id}."\t".${'desc'.$site_id}."\t".${'species'.$site_id}."\t".$remarks."\n";
			 $output_no++;
			 }
	$output=$excelheader."\n".$valuedata;
	$file_name="site_data_".date('d_m_Y_H_i_s').'.xls';
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$file_name");
			header('Pragma: no-cache');    
			header('Expires: 0');
			echo $output;
			exit();
}
?>