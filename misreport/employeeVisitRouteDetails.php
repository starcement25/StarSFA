<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];

	if($_REQUEST['mode']=='visit_route_display')
	{
		csvexport();
	}
	else
	{
		disphtml("main();");
	}
	
ob_end_flush();

function main()
{
  if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script language="javascript">
function check()
{
	if (document.getElementById("employee").value=='') 
	{
		alert('Please select a employee.');
		document.getElementById("employee").focus();
		return false;
	}
	/*if(document.frmSearch.from_date.value.search(/\S/)==0)
	{
		if(document.frmSearch.to_date.value.search(/\S/)==-1)
		{
			alert('Please input a value for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}*/
	 if (document.frmSearch.month.value==0) 
		{
			alert('Please select a month.');
			document.frmSearch.month.focus();
			return false;
		}
		if (document.frmSearch.year.value==0) 
		{
			alert('Please select a year.');
			document.frmSearch.year.focus();
			return false;
		}
		document.frmSearch.employee_val.value=document.getElementById("employee").value;
	return true;
}
function vertical_emp(vertical){
		var vertical = encodeURIComponent(document.getElementById("vertical").value);
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_vertical_related_data.php?vertical='+vertical+'&type=verticalempall','emp_select_div',0);
	}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Visit Route Details</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="visit_route_display">
                    <input type="hidden" name="employee_val" id="employee_val" value="">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        <?php 
							$onclickvertical = "vertical_emp(this.value);";
							$table_data .= "<tr><td align=\"right\"  width=\"40%\" colspan=\"2\">Vertical:</td>
										<td align=\"left\"  width=\"\" style=\"vertical-align:top;\" colspan=\"2\">";
							 $sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(vertical_value, ',', -1) as distinct_vertical_value 
											FROM employee_master WHERE SUBSTRING_INDEX( vertical_value, ',', -1 ) != '' AND vertical_value NOT LIKE 'M%' 
											".$emp_hierarchy_value_condition." AND acedns='Y'";
							 $res_vertical = mysqli_query($link,$sql_vertical);
							 $vertical_select_control = "<select name=\"vertical\" id=\"vertical\" onchange=\"".$onclickvertical."\">";
							 $vertical_select_control .= "<option value=\"\">Select</option>";
								//$vertical_select_control .= "<option value=\"all\">All</option>";
							 $dist_vertical_val_array=array();
							 while($row_vertical = mysqli_fetch_assoc($res_vertical)){
								 $dist_vert_value = trim($row_vertical['distinct_vertical_value']);
									/*if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
										$pos = substr($dist_vert_value,0,1);
										if($pos == 'M'){
											$dist_vert_value = 'MACROMAN';
										}
									}*/
									if(!in_array($dist_vert_value,$dist_vertical_val_array))
									{
										$vertical_select_control .= "<option value=\"'".$dist_vert_value."'\">".$dist_vert_value."</option>";
										array_push($dist_vertical_val_array,$dist_vert_value);
									}
									$vertical_string .= "'".$dist_vert_value."',";
								}
								$vertical_string = rtrim($vertical_string,",");
								$vertical_select_control .= "</select>";
								$table_data .= $vertical_select_control;
								echo $table_data .= "</td></tr>";
						?>
                         <tr>
                                <td align="right" width="40%" colspan="2">Employee:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2"><div id="emp_select_div"></div>
                                </td>
                           </tr>
                            <tr id="datedropdown" >
                                <td align="left" width="15%">Month:</td>
                                <td align="left" width="30%" style="vertical-align:top;">
                                     <?php 
                                     $month=$_REQUEST['month'];
                                     echo PopulateSelectDefault('month', "SELECT DISTINCT DATE_FORMAT(date,'%M') AS month,DATE_FORMAT(date,'%m') 
									 AS month_value FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='A' AND 
									 DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='2019-09-01'
									 GROUP BY DATE_FORMAT(date,'%m-%Y') ORDER BY YEAR(date) DESC,MONTH(date) DESC", 
									 'month_value', 'month', $month,'','inplogin');?>					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">Year:</td>
                                <td width="" style="vertical-align:top;">
                                    <?php 
                                    $year=$_REQUEST['year'];
                                    echo PopulateSelectDefault('year', "SELECT DATE_FORMAT(date,'%Y') AS year FROM location WHERE emp_code!='C0007' 
									AND SUBSTRING(trans_id,1,1)='A' AND DATE_FORMAT(date,'%Y')!='0000' AND 
									 DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='2019-09-01' GROUP BY DATE_FORMAT(date,'%Y') ORDER BY DATE_FORMAT(date,'%Y') DESC", 'year', 'year', $year,'','inplogin');?>					
                                </td> 
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="submit" value="Download" class="inplogin" name="submit1">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
<?php //End of main()
if($_REQUEST['mode'] =='visit_route_display_mode'){
	
	$emp_name=$_REQUEST['employee'];
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];

	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') >='".$from_date."' AND 
					  				DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') <='".$to_date."'";
	}
	$emp_condition=" AND LO.emp_code IN(".$emp_name.")";
  	$sqlempvisit="SELECT EM.emp_name,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS visit_date, LO.address 
				FROM employee_master EM,location LO WHERE 
				LO.emp_code=EM.emp_code ".$emp_condition.$date_condition." AND LO.trans_id LIKE 'A%' ORDER BY EM.emp_name ASC,
				DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') ASC";
?>
	<div id="display">
	<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" >
                    <tr class="TDHEAD" > 
                        <td colspan="9" align="center"><strong>Visit Details</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="left" style="padding:0px 20px 0px 20px;">Sl</td>
                        <td width="50%" align="left" style="padding:0px 20px 0px 20px;">Emp Name</td>
                        <td width="15%" align="left" style="padding-left:20px;">Visit Date</td>
                        <td width="" align="left" style="padding-left:20px;">Attendance Address</td>
                        <td width="" align="left" style="padding-left:20px;">Attendance Address</td>
                    </tr> 
                    <?php
							$rsempvisit=mysqli_query($link,$sqlempvisit) or die(mysqli_error()." Error in route details: ".$sqlempvisit);
							$countempvisit=mysqli_num_rows($rsempvisit);
							$count=1;
							if($countempvisit <1)
							{
					?>
                     <tr > 
                        <td align="center" colspan="3" >No records found.</td>
                    </tr> 
                    <?php			
							}
							while($rowempvisit=mysqli_fetch_assoc($rsempvisit))
							{
					?>
							<tr> 
                                    <td valign="top" align="right" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $count++;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowempvisit['emp_name'];?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowempvisit['visit_date'];?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php  echo $rowempvisit['address'];;?></td>
                              </tr>
                             <?php				
							}
						?>
            </table>
       </td>
    </tr>
 </table> 
  </div>  
  <center><input name="print" type="button" value="Print" onclick="PrintElem('#display');" />&nbsp;
  		  <input name="export" type="button" value="Export" onclick="exporttocsv();" />
  </center>
<script>
function PrintElem(elem)
    {
		Popup($(elem).html());
    }

function Popup(data) 
    {
        var mywindow = window.open('', 'Customer Frequency Details', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Customer Frequency Details</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;
    }
	
function exporttocsv()
{
	var emp_name = document.getElementById("emp_name").value;
	var start_date = document.frmSearch.from_date.value;
	var end_date = document.frmSearch.to_date.value;
	//alert(emp_name+start_date+end_date);
	//alert(emp_name);
	var stringparts=",";
	if(emp_name==0)
	{
		emp_name='all';
	}
	else  emp_name=emp_name;
	//alert(emp_name);
	window.open('employeeVisitRouteDetails.php?emp_name='+emp_name+'&start_date='+start_date+'&end_date='+end_date,'mywindow');
}
</script>
<?php		
  }
}
?>
<?php
function csvexport()
{
	$setExcelName = 'Customer Frequency';
	//print_r($_REQUEST);
	$emp_name=$_REQUEST['employee_val'];
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];
	$number_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$emp_name_array=array();
	$emp_code_array=array();
	
	$countcsv = 1;
	/*$excelheader = "SI"."\t"."Emp Name"."\t";
	for($count_number_days=1;$count_number_days<=$number_days;$count_number_days++)
		{
			$excelheader.=date('F ,d Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		}
		$excelheader.="\n";*/
	if(strtoupper($_SESSION['nick_name'])=='RUPA')
	{
	 $excelheader = "SI"."\t"."Emp Name"."\t"."Visit Date"."\t"."Attendance Time"."\t"."Checkout Time"."\t"."Attendance Address"."\t"."Checkout Address"."\n";
	}
	else
	{
	 $excelheader = "SI"."\t"."Emp Name"."\t"."Visit Date"."\t"."Attendance Time"."\t"."Attendance Address"."\n";
	}
	if($month!='' && $year!='')
	{
		 $date_condition=" AND DATE_FORMAT(LO.date,'%m')='".$month."' AND DATE_FORMAT(LO.date,'%Y')='".$year."'";
	}
	/*if($emp_name =='all')
	{
		$sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ".$emp_hierarchy_condition." AND acedns!='N' ORDER BY emp_name ASC";
		$resultqueryemp = mysqli_query($link,$sqlqueryemp);
		$count=mysqli_num_rows($resultqueryemp);
		if($count>0){
		while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
		{
			$emp_code=$rowqueryemp['emp_code'];
			$emp_code_string .= "'".$emp_code."',";
		}
		$emp_code_string = rtrim($emp_code_string,",");
	  }
	  $emp_condition=" AND LO.emp_code IN(".$emp_code_string.")";
	}
	else
	{*/
	$emp_condition=" AND LO.emp_code IN(".$emp_name.")";
	//}
	if(strtoupper($_SESSION['nick_name'])=='RUPA')
	{	
	$sqlempvisit="SELECT DISTINCT EM.emp_code,EM.emp_name,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS visit_date,
				CASE
					WHEN LO.trans_id LIKE 'A%' THEN address
					ELSE NULL
				END AS attendance_address,
				CASE
					WHEN LO.trans_id LIKE 'CH%' THEN address
					ELSE NULL
				END AS checkout_address,
				CASE
					WHEN LO.trans_id LIKE 'A%' THEN DATE_FORMAT(LO.date,'%T')
					ELSE NULL
				END AS att_time,
				CASE
					WHEN LO.trans_id LIKE 'CH%' THEN DATE_FORMAT(LO.date,'%T')
					ELSE NULL
				END AS checkout_time FROM employee_master EM,location LO WHERE 
				LO.emp_code=EM.emp_code ".$emp_condition.$date_condition." AND (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'CH%') 
				ORDER BY DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') ASC,
				EM.emp_name ASC";
	}
	else
	{
		$sqlempvisit="SELECT DISTINCT EM.emp_code,EM.emp_name,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS visit_date,
				CASE
					WHEN LO.trans_id LIKE 'A%' THEN address
					ELSE NULL
				END AS attendance_address,
				CASE
					WHEN LO.trans_id LIKE 'A%' THEN DATE_FORMAT(LO.date,'%T')
					ELSE NULL
				END AS att_time,'' as checkout_time,'' as checkout_address FROM employee_master EM,location LO WHERE 
				LO.emp_code=EM.emp_code ".$emp_condition.$date_condition." AND LO.trans_id LIKE 'A%'  
				ORDER BY DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') ASC,
				EM.emp_name ASC";
	}
	$rsempvisit=mysqli_query($link,$sqlempvisit) or die(mysqli_error()." Error in route details: ".$sqlempvisit);	
	while($rowempvisit=mysqli_fetch_assoc($rsempvisit))
	 {
		$emp_code = $rowempvisit['emp_code'];
		$emp_name = $rowempvisit['emp_name'];
		$visit_date = $rowempvisit['visit_date'];
		//$time = $rowempvisit['time'];
		//$address = $rowempvisit['address'];
		$attendance_address=$rowempvisit['attendance_address'];
		$checkout_address=$rowempvisit['checkout_address'];
		$att_time = $rowempvisit['att_time'];
		$checkout_time = $rowempvisit['checkout_time'];
		/*${visit_date.$visit_date.$emp_code}=$visit_date;
		${address.$visit_date.$emp_code}=$address." @ $time Hrs.";
		
		
		if(!in_array($emp_code,$emp_code_array))
		{
			array_push($emp_code_array,$emp_code);
			array_push($emp_name_array,$emp_name);
		}*/
		if(strtoupper($_SESSION['nick_name'])=='RUPA')
		{						
		  $excelcontents .= $countcsv."\t".$emp_name."\t".$visit_date."\t".$att_time."\t".$checkout_time."\t".$attendance_address."\t".$checkout_address."\n";
		}
		else
		{
		  $excelcontents .= $countcsv."\t".$emp_name."\t".$visit_date."\t".$att_time."\t".$attendance_address."\n";
		}
		$countcsv++;
	}
	//print_r($emp_code_array);
	/*for($empcount=0;$empcount < count($emp_code_array);$empcount++)
	{
		$excelcontents .= $countcsv."\t".$emp_name_array[$empcount]."\t";
		for($count_number_days=1;$count_number_days<=$number_days;$count_number_days++)
		 {
			if(strlen($count_number_days)<2)
			{
				$date_value='0'.$count_number_days;
			}
			else
			{
				$date_value=$count_number_days;
			}
			if(strlen($month)<2)
			{
				$month_val='0'.$month;
			}
			else
			{
				$month_val=$month;
			}
		   $present_date=$date_value.'-'.$month_val.'-'.$year;
		   $excelcontents .=${address.$present_date.$emp_code_array[$empcount]}."\t";
		 }
		 $excelcontents .="\n";
		 $countcsv++;
	}*/
	header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"); 
	header("Content-Disposition: attachment; filename=Employee_Visit_Address.xls"); 
	
	echo $excelheader;
	echo $excelcontents;
	}
?>