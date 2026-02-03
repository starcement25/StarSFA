<?php
/*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
ob_start();


	session_start();
	require("adminUtils.php");
	require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");


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
//require("include/config.php");
//require("include/config-setup.php");

function main($countbranches)
{
    require("include/dbcon.php");
	if($_SESSION['admin_login']=="admin"){
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
	  document.frmSearch.search_mode.value="exceldownload";	
	  document.frmSearch.submit();
	 /* var monthvalue=document.frmSearch.month.value;
	  var yearvalue=document.frmSearch.year.value;
	  var width  = 1200;
	  var height = 600;
	  var xc = ((screen.availWidth - 10) - width) / 2;
	  var yc = ((screen.availHeight- 30) - height) / 2;
	new_window = window.open('printAttendance.php?monthvalue='+monthvalue+'&yearvalue='+yearvalue, '', 'toolbar=0,menubar=0,resizable=no,dependent=0,status=0,scrollbars=yes,width=' + width + ',height=' + height + ',left=' + xc + ',top=' + yc);*/
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
                                         <?php if(strtolower($_SESSION['admin_login'])=="admin" 
										 ||(strtolower($_SESSION['admin_login'])!="admin" && strtoupper($_SESSION['nick_name'])=='DURO') ){?>
                                        <option value="all">ALL</option>
                                         <?php 
										   }
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
                                <td align="left" width="15%">Month:</td>
                                <td align="left" width="20%" style="vertical-align:top;">
                                     <?php 
                                     $month=$_REQUEST['month'];
                                     echo PopulateSelectDefault('month', "SELECT DATE_FORMAT(date,'%M') AS month,DATE_FORMAT(date,'%m') AS month_value FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='A' GROUP BY DATE_FORMAT(date,'%m-%Y') ORDER BY YEAR(date) DESC,MONTH(date) DESC", 'month_value', 'month', $month,'','inplogin');?>					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">Year:</td>
                                <td width="20%" style="vertical-align:top;">
                                    <?php 
                                    $year=$_REQUEST['year'];
                                    echo PopulateSelectDefault('year', "SELECT DATE_FORMAT(date,'%Y') AS year FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='A' GROUP BY DATE_FORMAT(date,'%Y') ORDER BY DATE_FORMAT(date,'%Y') DESC", 'year', 'year', $year,'','inplogin');?>					
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
//For date wise Employee attendance download
	function exceldownloaddata($countbranches)
	{
	    require("include/dbcon.php");
	    require("include/functions.php");
		//$today = date('d');
		$today = date('Y-m-d');
		if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		}
		else
		{
			$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
			$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
		}
	
		$branch_code=$_REQUEST['branch_name'];
		if($branch_code=='all')
		{
			$branch_condition=" AND branch_code!=''";
		}
		else
		{
			$branch_condition="AND FIND_IN_SET('".$branch_code."',branch_code)";
		}
		$month=$_REQUEST['month'];
		$year=$_REQUEST['year'];
		if($month!=="all" && $year!=="all")
		{
			$month_year_cndition= " AND DATE_FORMAT(LO.date,'%m')='".$month."' AND DATE_FORMAT(LO.date,'%Y')='".$year."'";
		}
		else
		{
			$month_year_cndition="";
		}
		$number_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		if($countbranches > 0 && strtoupper($_SESSION['nick_name'])!='PALSONS'){
			$excelheader="Emp Code". "\t";
			$excelheader.="DNS Emp Code". "\t";
			$excelheader.="Employee Name". "\t";
			$excelheader.="Branch". "\t";
			if(retailer_app=='yes')
			{
			  $excelheader.="Customer Name". "\t";
			}
			$excelheader.="State". "\t";
			$excelheader.="Designation". "\t";
		}
		else
		{
			$excelheader="Sl No". "\t";
			$excelheader.="Emp Code". "\t";
			$excelheader.="DNS Emp Code". "\t";
			$excelheader.="Employee Name". "\t";
			if(retailer_app=='yes')
			{
			  $excelheader.="Customer Name". "\t";
			}
			$excelheader.="State". "\t";
			$excelheader.="Designation". "\t";
			$excelheader.="Active/Inactive". "\t";
		}
		for($count_number_days=1;$count_number_days<=$number_days;$count_number_days++)
		{
			$excelheader.=date('F ,d Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		}
		//$excelsubheader="Attendance Details of ".date("F", strtotime($month)).",".date("Y", strtotime($year));
		if($countbranches > 0 && strtoupper($_SESSION['nick_name'])!='PALSONS'){ //For the user of multiple branches
			$emp_code_chk_array=array();
				/*$sqlemployeelist="SELECT emp_code,emp_name FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' 
								AND branch_code='".$fetched_branch_code."' ".$emp_hierarchy_condition." order BY emp_name ASC";*/
				/*$sqlemployeelist="SELECT emp_code, dns_emp_code, emp_name,designation,state,(SELECT GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR ';') 
				FROM branch_master BM WHERE FIND_IN_SET(BM.branch_code,branch_code)) as branch_code_name 
				FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' AND 
				FIND_IN_SET('".$fetched_branch_code."',branch_code)".$emp_hierarchy_condition." order BY emp_name ASC";*/
				$sqlemployeelist="SELECT emp_code, dns_emp_code, emp_name,designation,state,(SELECT GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR ';') 
				FROM branch_master BM WHERE FIND_IN_SET(BM.branch_code,employee_master.branch_code)) as branch_code_name 
				FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' AND acedns='Y' ".$emp_hierarchy_condition.$branch_condition."  order BY emp_name ASC";
				
				$resemployeelist=mysqli_query($link,$sqlemployeelist) or die(mysqli_error()." Error in select employee list: ".$sqlemployeelist);
				$cnt=1;
				$value="";
				while($rowemployeelist=mysqli_fetch_assoc($resemployeelist))
				{
					$emp_code=$rowemployeelist['emp_code'];
					if(strlen($month)<2)
					{
						$month_val='0'.$month;
					}
					else
					{
						$month_val=$month;
					}
					$month_year_val=$year.'-'.$month_val;
					$sqlcustname="SELECT CM.customer_name FROM customer_master CM,customer_route_emp_relation CRR WHERE 
									CM.customer_code=CRR.customer_code AND CRR.emp_code='".$emp_code."'";
					$rscustname=mysqli_query($link,$sqlcustname);
					$rowcustname=mysqli_fetch_assoc($rscustname);
					$customer_name=$rowcustname['customer_name'];				
					if(!in_array($emp_code,$emp_code_chk_array))
					{
					/*$sqlinformationmonth="SELECT LO.trans_id FROM 
									 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND 
									 LO.emp_code='".$emp_code."' AND DATE_FORMAT(LO.date,'%Y-%m')='".$month_year_val."'";
					$rsinformationmonth=mysqli_query($link,$sqlinformationmonth);
					$countinformationmonth=mysqli_num_rows($rsinformationmonth);
					if($countinformationmonth > 0){	*/				 

					$dns_emp_code=$rowemployeelist['dns_emp_code'];
					$emp_name=$rowemployeelist['emp_name'];
					$designation=$rowemployeelist['designation'];
					$state=$rowemployeelist['state'];
					$branch_code_name=$rowemployeelist['branch_code_name'];
					
					/*$sqlrds="SELECT rds_name FROM rds_master WHERE emp_code='".$emp_code."'";
					$resrds=mysqli_query($link,$sqlrds) or die(mysqli_error()." Error in select rds: ".$sqlrds);
					$rowrds=mysqli_fetch_assoc($resrds);
					if(sale=='yes') $rds_name=$rowrds['rds_name'];
					else 			$rds_name='';*/
					$cnt=1;
					$value="";
					$value.=$emp_code." \t";
					$value.=$dns_emp_code." \t";
					$value.=$emp_name." \t";
					$value.=$branch_code_name." \t";

					if(retailer_app=='yes')
					{
					$value.=$customer_name." \t";
					}
					$value.=$state." \t";
					$value.=$designation." \t";			
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
						$present_date=$year.'-'.$month_val.'-'.$date_value;
						if(strtoupper($_SESSION['nick_name'])=='DURO' || strtoupper($_SESSION['nick_name'])=='NIMBUS')
						{
						$sqlinformation="SELECT DATE_FORMAT(LO.date,'%T') AS time,LO.trans_id,LO.address FROM 
										 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND 
										 LO.emp_code='".$emp_code."' AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$present_date."'";
						}
						else
						{
							$sqlinformation="SELECT DATE_FORMAT(LO.date,'%T') AS time,LO.trans_id FROM 
										 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND 
										 LO.emp_code='".$emp_code."' AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$present_date."'";
						}
						$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select attendance information: ".$sqlinformation);
						$count=mysqli_num_rows($resinformation);
						
						if($count==0)
						{
							if($present_date>$today)
								$value.="X"." \t";
							else
								$value.="A"." \t";
						}
						else{
							$rowinformation=mysqli_fetch_assoc($resinformation);
							$time=$rowinformation['time'];
							if(strtoupper($_SESSION['nick_name'])=='DURO' || strtoupper($_SESSION['nick_name'])=='NIMBUS')
							{
								$address=$rowinformation['address'];
							}
							$trans_id=$rowinformation['trans_id'];
							$trans_id_parts=substr($rowinformation['trans_id'],0,1);
							if($trans_id_parts=='W')
							{
								$value.="WO"." \t";
							}
							else if($trans_id_parts=='L')
							{
								$value.="LR"." \t";
							}
							else
							{
								if(strtoupper($_SESSION['nick_name'])=='DURO' || strtoupper($_SESSION['nick_name'])=='NIMBUS')
								{
								$value.=$time.'-'.$address." \t";
								}
								else if(strtoupper($_SESSION['nick_name'])=='MAGIK')
								{
									$sqlattactivity="SELECT att_activities FROM att_checkout_journey_info WHERE attendance_id='".$trans_id."'";
									$resattactivity=mysqli_query($link,$sqlattactivity);
									$rowattactivity=mysqli_fetch_assoc($resattactivity);
									$att_activities=$rowattactivity['att_activities'];
									$att_activities=substr($att_activities,0,-2);
									$value.=$time.'-'.$att_activities." \t";
								}
								else if(strtoupper($_SESSION['nick_name'])=='SYLVAN')
								{	
								 	$sql_checkout = "SELECT SUBSTRING(LO.date,12) AS checkout_time,trans_id,latt,longi FROM location LO WHERE LO.emp_code = '".$emp_code."' 
											AND LO.trans_id LIKE 'CH%' AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$present_date."'";
									$res_checkout = mysqli_query($link,$sql_checkout);
									$row_checkout = mysqli_fetch_assoc($res_checkout);
									$check_out_time = $row_checkout['checkout_time'];
									
									if($check_out_time != '')
										{
											$time_difference=strtotime($check_out_time)-strtotime($time);
											$time_difference_final=$time_difference;
											if($time_difference_final >=3600)
											{
												$hours = floor($time_difference_final / 3600);
												$minutes = floor(($time_difference_final / 60) % 60);
												$seconds = $time_difference_final % 60;
												$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
											}
											else if($time_difference_final >=60 && $time_difference_final<3600)
											{
												$minutes = floor(($time_difference_final / 60) % 60);
												$seconds = $time_difference_final % 60;
												$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
											}
											else
											{
												$seconds = $time_difference_final % 60;
												$time_duration=$seconds.' Second(s)';
											}
										$value.=$time.' - '.$check_out_time.' - '.$time_duration." \t";

										}
										else
										{
											$value.=$time." \t";
										}
									
								}
								else if(strtoupper($_SESSION['nick_name'])=='ABDOS')
								{
									$sqlattdetails="SELECT att_status,att_remarks FROM location WHERE trans_id='".$trans_id."'";
									$resattdetails=mysqli_query($link,$sqlattdetails);
									$rowattdetails=mysqli_fetch_assoc($resattdetails);
									$att_status=$rowattdetails['att_status'];
									$att_remarks=$rowattdetails['att_remarks'];
									if($att_status!='' || $att_remarks!=''){
									if($att_status!=''){
										$valueatt=' - '.$att_status;
									}
									if($att_remarks!=''){
										$valueatt=$valueatt.' - '.$att_remarks;
									}
									}
									else $valueatt='';
									$value.=$time.$valueatt." \t";
								}
								else
								{
									$value.=$time." \t";
								}
							}
						}
					}
				array_push($emp_code_chk_array,$emp_code);
								  $line .= $value."\n"; 

				$cnt++;
					}
				  //}
				}
				//$line.="\n";
		}
		else //For the user of no branches
		{
			$sqlemployeelist="SELECT emp_code, dns_emp_code, emp_name,designation,state,acedns FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' 
							".$emp_hierarchy_condition."  order BY emp_name ASC";
							
			$resemployeelist=mysqli_query($link,$sqlemployeelist) or die(mysqli_error()." Error in select employee list: ".$sqlemployeelist);
			$cnt=1;
			$value="";
			while($rowemployeelist=mysqli_fetch_assoc($resemployeelist))
			{
				$emp_code=$rowemployeelist['emp_code'];
				$dns_emp_code=$rowemployeelist['dns_emp_code'];
				$emp_name=$rowemployeelist['emp_name'];
				$designation=$rowemployeelist['designation'];
				$state=$rowemployeelist['state'];
				$acedns=$rowemployeelist['acedns'];
				
				$value="";
				$value.=$cnt." \t";
				$value.=$emp_code." \t";
				$value.=$dns_emp_code." \t";
				$value.=$emp_name." \t";
				$value.=$state." \t";
				$value.=$designation." \t";	
				$value.=$acedns." \t";
				if(strlen($month)<2)
					{
						$month_val='0'.$month;
					}
					else
					{
						$month_val=$month;
					}
				$month_year_val=$year.'-'.$month_val;
				if(strtoupper($_SESSION['nick_name'])=='PALSONS'){
				$sqlinformationmonth="SELECT LO.trans_id FROM 
									 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND 
									 LO.emp_code='".$emp_code."' AND DATE_FORMAT(LO.date,'%Y-%m')='".$month_year_val."'";
					$rsinformationmonth=mysqli_query($link,$sqlinformationmonth);
					$countinformationmonth=mysqli_num_rows($rsinformationmonth);
				 if($countinformationmonth > 0){				
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
					$present_date=$year.'-'.$month_val.'-'.$date_value;
					//echo $date_value;
					$selcountleaveoffice="SELECT RM.route_code,RM.route_name FROM route_master RM,route_plan RP WHERE RP.route_code=RM.route_code 
											AND RP.emp_code='".$emp_code."' AND RP.visit_date='".$present_date."' AND 
											RM.route_name IN('Office Visit','Leave Request','Meeting')";
					$rscountleaveoffice=mysqli_query($link,$selcountleaveoffice);
					$cntleaveoffice=mysqli_num_rows($rscountleaveoffice);

					$sqlinformation="SELECT DATE_FORMAT(LO.date,'%T') AS time,LO.trans_id FROM 
									 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND LO.emp_code='".$emp_code."' 
									 AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$present_date."'";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select attendance information: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{
						if($cntleaveoffice > 0)
						{
							$rowcountleaveoffice=mysqli_fetch_assoc($rscountleaveoffice);
							if($rowcountleaveoffice['route_name']=='Office Visit')   $value.="O"." \t";
							if($rowcountleaveoffice['route_name']=='Leave Request')   $value.="L"." \t";
							if($rowcountleaveoffice['route_name']=='Meeting')   		  $value.="Meeting"." \t";
						}
						else
						{
							if($present_date>$today)
								$value.="X"." \t";
							else
								$value.="A"." \t";
						}
					}
					else{
						if($cntleaveoffice > 0)
						{
							$rowcountleaveoffice=mysqli_fetch_assoc($rscountleaveoffice);
							if($rowcountleaveoffice['route_name']=='Office Visit')   $value.="O"." \t";
							if($rowcountleaveoffice['route_name']=='Leave Request')   $value.="L"." \t";
							if($rowcountleaveoffice['route_name']=='Meeting')          $value.="Meeting"." \t";
						}
						else
						{
							$rowinformation=mysqli_fetch_assoc($resinformation);
							$time=$rowinformation['time'];
							$trans_id=$rowinformation['trans_id'];
							$trans_id_parts=substr($rowinformation['trans_id'],0,1);
							if($trans_id_parts=='W')
							{
								$value.="WO"." \t";
							}
							else if($trans_id_parts=='L')
							{
								$value.="LR"." \t";
							}
							else
							{
								$value.=$time." \t";
							}
						}
					}
				}
				$line .= $value."\n"; 
				$cnt++;
				 }
				}
				else
				{
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
					$present_date=$year.'-'.$month_val.'-'.$date_value;
					//echo $date_value;
					$selcountleaveoffice="SELECT RM.route_code,RM.route_name FROM route_master RM,route_plan RP WHERE RP.route_code=RM.route_code 
											AND RP.emp_code='".$emp_code."' AND RP.visit_date='".$present_date."' AND 
											RM.route_name IN('Office Visit','Leave Request','Meeting')";
					$rscountleaveoffice=mysqli_query($link,$selcountleaveoffice);
					$cntleaveoffice=mysqli_num_rows($rscountleaveoffice);

					$sqlinformation="SELECT DATE_FORMAT(LO.date,'%T') AS time,LO.trans_id FROM 
									 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND LO.emp_code='".$emp_code."' 
									 AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$present_date."'";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select attendance information: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{
						if($cntleaveoffice > 0)
						{
							$rowcountleaveoffice=mysqli_fetch_assoc($rscountleaveoffice);
							if($rowcountleaveoffice['route_name']=='Office Visit')   $value.="O"." \t";
							if($rowcountleaveoffice['route_name']=='Leave Request')   $value.="L"." \t";
							if($rowcountleaveoffice['route_name']=='Meeting')   		  $value.="Meeting"." \t";
						}
						else
						{
							if($present_date>$today)
								$value.="X"." \t";
							else
								$value.="A"." \t";
						}
					}
					else{
						if($cntleaveoffice > 0)
						{
							$rowcountleaveoffice=mysqli_fetch_assoc($rscountleaveoffice);
							if($rowcountleaveoffice['route_name']=='Office Visit')   $value.="O"." \t";
							if($rowcountleaveoffice['route_name']=='Leave Request')   $value.="L"." \t";
							if($rowcountleaveoffice['route_name']=='Meeting')   		  $value.="Meeting"." \t";
						}
						else
						{
							$rowinformation=mysqli_fetch_assoc($resinformation);
							$time=$rowinformation['time'];
							$trans_id=$rowinformation['trans_id'];
							$trans_id_parts=substr($rowinformation['trans_id'],0,1);
							if($trans_id_parts=='W')
							{
								$value.="WO"." \t";
							}
							else if($trans_id_parts=='L')
							{
								$value.="LR"." \t";
							}
							else
							{
								$value.=$time." \t";
							}
						}
					}
				}
				$line .= $value."\n"; 
				$cnt++;
				}
			}
		}
		$data = str_replace("\r","",$line);
		header("Content-type: application/x-msdownload"); 
		header("Content-Disposition: attachment; filename=Attendace_Details_".date("F", strtotime('01-'.$month.'-'.$year))."_".date("Y", strtotime($year)).".xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		print "$excelheader\n$data";
	}
?>