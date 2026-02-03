<?php
ob_start();
	session_start();
	//ini_set('max_execution_time', 300);
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	
	
	$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
	$rsbranches=mysqli_query($link,$sqlbranches);
	$countbranches=mysqli_num_rows($rsbranches);

	if($_REQUEST['search_mode']=='exceldownload')
	{
		exceldownloaddata($countbranches);	
	}
	else
	{
		disphtml("main($countbranches);");
	}
ob_end_flush();
function main($countbranches)
{
	?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
  webshims.setOptions('waitReady', false);
  webshims.setOptions('forms-ext', {types: 'date'});
  webshims.polyfill('forms forms-ext');
</script>
</head>
    <?php
	if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="emovesfa_hr"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		$emp_upper_hierarchy='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
		$emp_upper_hierarchy=return_employee_upper_hierarchy($_SESSION['admin_login']);
		if(strpos($emp_upper_hierarchy,',')==false){
			$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
		}
	}
?>
<script language="javascript">
function adminAttendancePrint()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	if(start_date>end_date)
	{
		alert("Start date cannot be greater than end date");
		return false;
	}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
	{
		alert("Start date/End date cannot be empty");
		return false;
	}
	
	document.frmSearch.search_mode.value="exceldownload";	
	document.frmSearch.submit();
}
</script>

    <table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong>Administrator >>Monthly Attendance Print</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
			<br><br>
			
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                 id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>SELECT ATTRIBUTES</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td  align="center" >
                        <table width="100%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        	 <?php if($countbranches > 0){?>
                                <tr>
                                    <td align="right" width="45%" colspan="2">Branch:</td>
                                    <td align="left" width="" style="vertical-align:top;" colspan="2">
                                       <?php $branch_name=$_REQUEST['branch_name'];
                                        $emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);

                                        ?>
                                        <select name="branch_name" id="branch_name" onChange="javascript:select_depot(this.value);">
                                        <?php if(count($emp_upper_hierarchy_array)==1 || $_SESSION['admin_login']=="admin") {?>
                                         <?php //if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="emovesfa_hr"){?>
                                        <option value="all">ALL</option>
                                         <?php 
										   //}
										 }
				 /*------------> Selection of branch, comma separated branch string creation<----------------*/
										 $branch_array = array();
									 	$sql_get_branch_list = "SELECT EM.branch_code FROM employee_master EM WHERE 1".$emp_hierarchy_condition_one;
										$res_get_branch_list = mysqli_query($link,$sql_get_branch_list);
										 while($row_get_branch_list = mysqli_fetch_assoc($res_get_branch_list)){
											$get_branch_code = $row_get_branch_list['branch_code'];
											if(strpos($get_branch_code,',') == true){
											$branch_code_explode = explode(',',$get_branch_code);
											foreach($branch_code_explode as $value){
												if(!in_array($value,$branch_array)){
													array_push($branch_array,$value);
													$branch_string .= "'".$value."',";
												}
											}
											}
											else{
											if(!in_array($get_branch_code,$branch_array)){
												array_push($branch_array,$get_branch_code);
												$branch_string .= "'".$get_branch_code."',";
											}
											}
										 }
										 $branch_string = rtrim($branch_string, ",");
										 
                                         /*$sqlquerybranch="SELECT DISTINCT BM.branch_code,BM.branch_name FROM branch_master BM,employee_master EM 
                                                          WHERE BM.branch_code=EM.branch_code ".$emp_hierarchy_condition_one." ORDER BY BM.branch_name ASC";*/
										 $sqlquerybranch = "SELECT BM.branch_code, BM.branch_name FROM branch_master BM WHERE BM.branch_code IN(".$branch_string.") ORDER BY BM.branch_name ASC";			  
                                         $resultbranch = mysqli_query($link,$sqlquerybranch);
                                         $count=mysqli_num_rows($resultbranch);
                                         $cnt=1;
                                         if($count>0){
											 $resultbranch = mysqli_query($link,$sqlquerybranch);
                                            while($rowbranch = mysqli_fetch_assoc($resultbranch))
                                            {
                                          ?>
                                             <option value="<?php echo $rowbranch['branch_code'];?>" <?php if($branch_name==$rowbranch['branch_name'] || 
                                             $_REQUEST['branch_code']==$rowbranch['branch_code']){echo 'selected';}?>><?php echo $rowbranch['branch_name'];?></option>
                                           <?php
                                            }
                                          }
                                          ?>	
                                         </select>
                                    </td>
                               </tr>
                               <?php }?>
                            <tr>
                                <td align="left" width="15%">FROM:</td>
                                <td align="left" width="20%" style="vertical-align:top;">
                                	<input type="date" name="start_date" id="start_date" value="" style="height:20px;" />					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">TO:</td>
                                <td width="20%" style="vertical-align:top;">
                                	<input type="date" name="end_date" id="end_date" value="" style="height:20px;" />
                                </td>
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Download" class="inplogin" onClick="javascript:adminAttendancePrint();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
               		</td>
              	</tr> 
            </table>
            </form>
           <br />
         <!------------------------------------------------End of Table for first time page loading----------------------------------------------!-->
  			<div id="AttendanceActivity" style="display:none;">
                </div><br />
  <!----------------------------------------Start of Table for populate employee date wise locate back-----------------------------------------------------!-->
 <?php
}//End of main()

function exceldownloaddata($countbranches){
		
	$branch_code=$_REQUEST['branch_name'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	
	$year_month = $year."-".$month;
	
	if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="emovesfa_hr"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		
		if($branch_code == 'all')
			$branch_condition = " branch_code != ''";
		else
			$branch_condition = " FIND_IN_SET('".$branch_code."',branch_code) ";
		}
	else{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' emp_code IN('.$emp_hierarchy.')';
		
		if($branch_code == 'all')
			$branch_condition = " AND branch_code != ''";
		else
			$branch_condition = " AND FIND_IN_SET('".$branch_code."',branch_code) ";
	}
	
	$excel_header = "Punch Number"."\t"."Punch Date & Time"."\n";
	
	$emp_dns_array = array();
	$sql_emp_code = "SELECT dns_emp_code, emp_code FROM employee_master WHERE ".$emp_hierarchy_condition.$branch_condition;
	$res_emp_code = mysqli_query($link,$sql_emp_code);
	while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
		$dns_emp_code  =$row_emp_code['dns_emp_code'];
		$emp_code = $row_emp_code['emp_code'];
		$emp_dns_array[$emp_code] = $dns_emp_code;
		$emp_string .= "'".$emp_code."',";
	}
	$emp_string = rtrim($emp_string,",");
	
	$sql_location_data = "SELECT DATE_FORMAT(SUBSTRING(LO.date,1,10),'%d-%m-%Y') AS date_selected, LO.emp_code, SUBSTRING(LO.date,12,16) AS atd_time FROM location LO WHERE LO.emp_code IN (".$emp_string.") AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') AND (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'CH%') ORDER BY SUBSTRING(LO.date,1,10) DESC, LO.emp_code ASC";
	$res_location_data = mysqli_query($link,$sql_location_data);
	$total_rows = mysqli_num_rows($res_location_data);
	if($total_rows>0){
		$res_location_data = mysqli_query($link,$sql_location_data);
		while($row_location_data = mysqli_fetch_assoc($res_location_data)){
			$date_selected = $row_location_data['date_selected'];
			$emp_code = $row_location_data['emp_code'];
			$atd_time = $row_location_data['atd_time'];
			$atd_date_time = $date_selected." ".$atd_time;
			$excel_data .= $emp_dns_array[$emp_code]."\t".$atd_date_time."\n";
		}
	}
	
	$getdate_timestamp = date('d-m-Y H:i:s');
	$excel_data = str_replace("\r","",$excel_data);
	header("Content-type: application/x-msdownload"); 
	header("Content-Disposition: attachment; filename=Attendace_Details_".$getdate_timestamp.".xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	print "$excel_header\n$excel_data";
}
?>