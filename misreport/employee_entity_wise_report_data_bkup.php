<?php
set_time_limit(1000);
//ini_set('memory_limit', '-1');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
session_start();
//ob_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$state = $_REQUEST['state'];
/*$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month_year[0]));
$year = $month_year[1];
$monthabbreviation= date('F',strtotime($month_year[0])).'-'.date('y',strtotime($year));*/

$current_date = date('d-m-Y');
$datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";
$count = 1;
$emp_code=$_REQUEST['emp_code'];
$emp_code_array=explode(",",$emp_code);
$entity=$_REQUEST['entity'];
$entity_array=explode(",",$entity);
$entity_val="'".implode("','", $entity_array)."'";
$last_visit_date_initial=$year.'-'.$monthvalue.'-01';
//exit();
/*----> Total Site Visit <----*/

$sqlallfaciemp="SELECT * FROM (SELECT SUBSTRING(`survey_id`,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(`survey_id`,-14,8),'%d-%m-%Y') AS last_visit_date,SUBSTRING_INDEX(SUBSTRING_INDEX(`value`, ';', 2), ';', -1) AS f_code,DATE_FORMAT(SUBSTRING(`survey_id`,-14,14),'%d-%m-%Y %H:%i:%s') AS last_visit_date_sort FROM survey_output WHERE row_id IN('RA036','RA047') ORDER BY DATE_FORMAT(SUBSTRING(`survey_id`,-14,14),'%Y-%m-%d %H:%i:%s') DESC) AS SAT GROUP BY 1,3 ORDER BY 1";
$rsallfaciemp=mysqli_query($link,$sqlallfaciemp);
while($rowallfaciemp=mysqli_fetch_assoc($rsallfaciemp))
{
	$emp_code_faci=$rowallfaciemp['emp_code'];
	$faci_code=$rowallfaciemp['f_code'];
	$last_visit_date=$rowallfaciemp['last_visit_date'];
	${'f_code_string'.$emp_code_faci}=${'f_code_string'.$emp_code_faci}."'".$faci_code."'".',';
	${'last_visit_date'.$emp_code_faci.$faci_code}=$last_visit_date;
}
$sqlallfaciempcreation="SELECT * FROM (SELECT SUBSTRING(`survey_id`,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(`survey_id`,-14,8),'%d-%m-%Y') AS date_creation,SUBSTRING_INDEX(SUBSTRING_INDEX(`value`, ';', 2), ';', -1) AS f_code,DATE_FORMAT(SUBSTRING(`survey_id`,-14,14),'%d-%m-%Y %H:%i:%s') AS last_visit_date_sort FROM survey_output WHERE row_id IN('RA036','RA047') ORDER BY DATE_FORMAT(SUBSTRING(`survey_id`,-14,14),'%Y-%m-%d %H:%i:%s') ASC) AS SAT GROUP BY 1,3 ORDER BY 1";
$rsallfaciempcreation=mysqli_query($link,$sqlallfaciempcreation);
while($rowallfaciempcreation=mysqli_fetch_assoc($rsallfaciempcreation))
{
	$emp_code_faci=$rowallfaciempcreation['emp_code'];
	$faci_code=$rowallfaciempcreation['f_code'];
	$date_creation=$rowallfaciempcreation['date_creation'];
	${'creation_date'.$emp_code_faci.$faci_code}=$date_creation;
}
?>
<table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
    <tr>
        <td colspan="14" class="TDHEAD" align="center" width="95%"><b>Entity Wise Report Of <?php echo $current_date;?></b></td>
   </tr>
  <tr class="TDHEAD_SUB">
    <td width="4%">SL No</td>
    <td width="9%">Employee Name</td>
    <td width="5%">Designation</td>
    <td width="5%">Branch</td>
    <td width="6%">Entity Type</td>
    <td width="8%">Entity Name</td>
     <td width="7%">Site Status</td>
    <td width="8%">Entity Firm name</td>
    <td width="6%">Entity Mobile</td>
    <td width="7%">Email</td>
    <td width="11%">Area</td>
    <td width="8%">Last Visit Date</td>
    <td width="8%">Next Visit Date</td>
    <td width="8%">Date of creation</td>
  </tr>
<?php
$count=1;
$no_records_count=0;
//print_r($emp_code_array);exit();
foreach($emp_code_array as $emp_code_val){
	/*$sqlfacilitatorlist="SELECT EM.emp_name,FM.f_code,FM.f_type,FM.facilitator_name,FM.firm_name,FM.mobile_no,FM.email_id,FM.f_area,
						FM.next_follow_up_date,DATE_FORMAT(SUBSTRING(FM.f_code,-14,8),'%d/%m/%Y') AS facilitator_entry_date
						FROM employee_master EM,facilitator_master FM WHERE FM.emp_code=EM.emp_code AND FM.emp_code='".$emp_code_val."' AND 
						FM.f_type IN(".$entity_val.") AND SUBSTRING(FM.f_code,-14,4)='".$year."' AND 
						SUBSTRING(FM.f_code,-10,2)='".$monthvalue."' ORDER BY FM.f_type ASC";*/
	/*$sqlfacilitatorlist="SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,FM.f_code,FM.f_type,FM.facilitator_name,FM.firm_name,FM.mobile_no,FM.email_id,FM.f_area,
						FM.next_follow_up_date,DATE_FORMAT(SUBSTRING(FM.f_code,-14,8),'%d/%m/%Y') AS facilitator_entry_date,DATE_FORMAT(SUBSTRING(FM.last_visit_date,1,10),'%d/%m/%Y') AS last_visit_date,BM.branch_name
						FROM employee_master EM,facilitator_master FM,branch_master BM WHERE FM.emp_code=EM.emp_code AND FM.branch_code=BM.branch_code AND FM.emp_code='".$emp_code_val."' AND 
						FM.f_type IN(".$entity_val.") ORDER BY FM.f_type ASC";*/
						
						${'f_code_string'.$emp_code_val}=substr(${'f_code_string'.$emp_code_val},0,-1);
						//echo ${'f_code_string'.$emp_code_val};
	$sqlfacilitatorlist="SELECT FM.f_code,FM.f_type,FM.facilitator_name,FM.firm_name,FM.mobile_no,FM.email_id,FM.f_area,
						FM.next_follow_up_date,DATE_FORMAT(SUBSTRING(FM.f_code,-14,8),'%d/%m/%Y') AS facilitator_entry_date,DATE_FORMAT(SUBSTRING(FM.last_visit_date,1,10),'%d/%m/%Y') AS last_visit_date,BM.branch_name
						FROM  facilitator_master FM,branch_master BM WHERE FM.branch_code=BM.branch_code AND FM.f_code IN ('".$emp_code_val."') AND 
						FM.f_type IN(".$entity_val.") ORDER BY FM.f_type ASC";			//echo $sqlfacilitatorlist;							
	$resfacilitatorlist = mysqli_query($link,$sqlfacilitatorlist);
	$cntfacilitatorlist=mysqli_num_rows($resfacilitatorlist);					
	while($rowfacilitatorlist = mysqli_fetch_assoc($resfacilitatorlist)){
		$emp_name = $rowfacilitatorlist['emp_name'];
		$designation = $rowfacilitatorlist['designation'];
		$emp_code = $rowfacilitatorlist['emp_code'];
		$branch_code = $rowfacilitatorlist['branch_code'];
		$f_code = $rowfacilitatorlist['f_code'];
		$f_type = $rowfacilitatorlist['f_type'];
		$facilitator_name=$rowfacilitatorlist['facilitator_name'];
		$firm_name=$rowfacilitatorlist['firm_name'];
		$mobile_no=$rowfacilitatorlist['mobile_no'];
		$email_id=$rowfacilitatorlist['email_id'];
		$f_area=$rowfacilitatorlist['f_area'];
		$f_area=str_replace('#','',$f_area);
		$next_follow_up_date=$rowfacilitatorlist['next_follow_up_date'];
		$facilitator_entry_date=$rowfacilitatorlist['facilitator_entry_date'];
		$last_visit_date=$rowfacilitatorlist['last_visit_date'];
		$last_visit_date=$rowfacilitatorlist['last_visit_date'];
		
		$sqlemp="SELECT emp_name,designation,emp_code,branch_code FROM employee_master WHERE emp_code='".$emp_code_val."'";
		$rsemp=mysqli_query($link,$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);
		$emp_name=$rowemp['emp_name'];
		$designation=$rowemp['designation'];
		$branch_code_emp=$rowemp['branch_code'];
		/*$sqllastvisitfaci="SELECT DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d/%m/%Y') AS last_visit_date_facilitator FROM 
						survey_output WHERE value like '%".$f_code."%' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') 
						< '".$last_visit_date_initial."' ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";*/
		/*$sqllastvisitfaci="SELECT DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d/%m/%Y') AS last_visit_date_facilitator FROM 
						survey_output WHERE value like '%".$f_code."%' ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') 
						DESC LIMIT 0,1";				
		$rslastvisitfaci=mysqli_query($link,$sqllastvisitfaci);
		$rowlastvisitfaci=mysqli_fetch_assoc($rslastvisitfaci);
		$last_visit_date_facilitator=$rowlastvisitfaci['last_visit_date_facilitator'];	
		if($last_visit_date_facilitator =='')
		{
			$last_visit_date_facilitator=$facilitator_entry_date;
		}*/
		$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code_emp."'";
		$rsbranch=mysqli_query($link,$sqlbranch);
		$rowbranch=mysqli_fetch_assoc($rsbranch);
		$branch_name_emp=$rowbranch['branch_name'];
		if($last_visit_date=='00/00/0000')
		{
			$last_visit_date_facilitator=$facilitator_entry_date;
		}
		else
		{
		$last_visit_date_facilitator=$last_visit_date;
		}
		$branch_name=$rowfacilitatorlist['branch_name'];
			
		echo "<tr>
					<td width=\"4%\">".$count."</td>
					<td width=\"10%\">".$emp_name."</td>
					<td width=\"5%\">".$designation."</td>
					<td width=\"5%\">".$branch_name_emp."</td>
					<td width=\"6%\">".$f_type."</td>
					<td width=\"8%\">".$facilitator_name."</td>
					<td width=\"7%\"></td>
					<td width=\"9%\">".$firm_name."</td>
					<td width=\"7%\">".$mobile_no."</td>
					<td width=\"7%\">".$email_id."</td>
					<td width=\"12%\">".$f_area."</td>
					<td width=\"9%\">".${'last_visit_date'.$emp_code_val.$f_code}."</td>
					<td width=\"9%\">".$next_follow_up_date."</td>
					<td width=\"9%\">".${'creation_date'.$emp_code_val.$f_code}."</td>
				  </tr>";
			$count++;
			$no_records_count++;
	}
       if(in_array('Sites',$entity_array))
	   {
	   /*$sql_site_visit = "SELECT EM.emp_name,'SITES' AS entity_type,SM.site_name,SM.phone_no,SM.site_id,SM.address,
	   					SM.follow_up_date,DATE_FORMAT(SUBSTRING(SM.site_id,-14,8),'%d/%m/%Y') AS site_entry_date
		 				FROM site_master SM INNER JOIN employee_master EM
						ON SM.emp_code=EM.emp_code AND SM.emp_code='".$emp_code_val."' AND SUBSTRING(SM.site_id,-14,4)='".$year."' AND 
						SUBSTRING(SM.site_id,-10,2)='".$monthvalue."' ";*/
		$sql_site_visit = "SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,'SITES' AS entity_type,SM.site_name,SM.phone_no,SM.site_id,SM.address,
	   					SM.follow_up_date,DATE_FORMAT(SUBSTRING(SM.site_id,-14,8),'%d/%m/%Y') AS site_entry_date,DATE_FORMAT(SUBSTRING(SM.download_time,1,10),'%d/%m/%Y') AS last_visit_date,SM.current_status
		 				FROM site_master SM INNER JOIN employee_master EM
						ON SM.emp_code=EM.emp_code AND SM.emp_code='".$emp_code_val."'";				
		$res_site_visit = mysqli_query($link,$sql_site_visit);
		$total_rows = mysqli_num_rows($res_site_visit);
			while($row_site_visit = mysqli_fetch_assoc($res_site_visit)){
				$emp_name = $row_site_visit['emp_name'];
				$designation = $row_site_visit['designation'];
				$emp_code = $row_site_visit['emp_code'];
				$branch_code = $row_site_visit['branch_code'];
				$site_id = $row_site_visit['site_id'];
				$creation_date=date('d-m-Y',strtotime(substr($site_id,6,8)));
				$entity_type = $row_site_visit['entity_type'];
				$site_name=$row_site_visit['site_name'];
				$phone_no=$row_site_visit['phone_no'];
				$address=$row_site_visit['address'];
				$address=str_replace('#','',$address);
				$follow_up_date=$row_site_visit['follow_up_date'];
				$site_entry_date=$row_site_visit['site_entry_date'];
				$last_visit_date=$row_site_visit['last_visit_date'];
				$current_status=$row_site_visit['current_status'];
				/*$sqllastvisitsite="SELECT DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d/%m/%Y') AS last_visit_date_site FROM 
						survey_output WHERE value like '%".$site_id."%' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') 
						< '".$last_visit_date_initial."' ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";*/
				$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
				$rsbranch=mysqli_query($link,$sqlbranch);
				$rowbranch=mysqli_fetch_assoc($rsbranch);
				$branch_name=$rowbranch['branch_name'];
		
				/*$sqllastvisitsite="SELECT DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d/%m/%Y') AS last_visit_date_site FROM 
						survey_output WHERE value like '%".$site_id."%' ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";		
				$rslastvisitsite=mysqli_query($link,$sqllastvisitsite);
				$rowlastvisitsite=mysqli_fetch_assoc($rslastvisitsite);
				$last_visit_date_site=$rowlastvisitsite['last_visit_date_site'];
				if($last_visit_date_site =='')
				{
					$last_visit_date_site=$site_entry_date;
				}*/
				echo "<tr>
						<td width=\"4%\">".$count."</td>
						<td width=\"10%\">".$emp_name."</td>
						<td width=\"5%\">".$designation."</td>
						<td width=\"5%\">".$branch_name."</td>
						<td width=\"6%\">".$entity_type."</td>
						<td width=\"8%\">".$site_name."</td>
						<td width=\"7%\">".$current_status."</td>
						<td width=\"9%\"></td>
						<td width=\"7%\">".$phone_no."</td>
						<td width=\"7%\"></td>
						<td width=\"12%\">".$address."</td>
						<td width=\"9%\">".$last_visit_date."</td>
						<td width=\"9%\">".$follow_up_date."</td>
						<td width=\"9%\">".$creation_date."</td>
					  </tr>";
				$count++;
				$no_records_count++;
			}
	   }
}
	?>
	</table>
<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
       <?php		
		if($no_records_count==0){
			echo "<div style=\"font-weight:bold; color:red;\">No Records Found</div>";
		}
		mysqli_close($link);
?>
