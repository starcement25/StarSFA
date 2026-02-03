<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
ob_start();
session_start();
require("adminUtils.php");
//if($_SESSION['admin_login']=="")  		header("location:index.php");
	$today = date('Y-m-d');
	$emp_code = $_REQUEST['employee'];
	/*$month_date=$_REQUEST['month_data'];
	$month_date='01'.'-'.$month_date;
	$month_date=date('Y-m',strtotime($month_date));
	$monthparts=explode("-",$month_date);
	$month=$monthparts['1'];
	$year=$monthparts['0'];
	$number_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);*/
	$start_date_captured=$_REQUEST['start_date'];
	$end_date=$_REQUEST['end_date'];
	$datediff = strtotime($end_date) - strtotime($start_date_captured);
	$number_days = round($datediff / (60 * 60 * 24));
	$excel_header_date = strtotime("-1 day", strtotime($start_date_captured));
	$excel_header_date= date("Y-m-d", $excel_header_date);
	$excelheader="Sl NO". "\t";
	$excelheader.="STATE". "\t";
	$excelheader.="ZONE NAME". "\t";
	$excelheader.="BRANCH NAME". "\t";
	$excelheader.="EMP CODE". "\t";
	$excelheader.="SFA CODE". "\t";
	$excelheader.="NAME". "\t";
	$excelheader.="ACEDNS". "\t";
	$excelheader.="DESIGNATION". "\t";
	$excelheader.="HQ". "\t";
	$excelheader.="DEPT.". "\t";
	for($count_number_days=0;$count_number_days<=$number_days;$count_number_days++)
	{
		//$excelheader.=date('d-m-Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		$excel_header_date = strtotime("+1 day", strtotime($excel_header_date));
		$excel_header_date=date("Y-m-d", $excel_header_date);
		$excelheader.= $excel_header_date. "\t";
	}
	$excelheader.="DAYS PRESENT.". "\t";
	$excelheader.="ABSENT.". "\t";
	$excelheader.="SUNDAY.". "\t";
	$excelheader.="HOLIDAY.". "\t";

	    echo $sqlemployeelist="SELECT emp_code, dns_emp_code, emp_name,designation,state,zone,HQ,sale_access,acedns,
					(SELECT GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR ';') 
					FROM branch_master BM WHERE FIND_IN_SET(BM.branch_code,employee_master.branch_code)) 
					as branch_code_name 
					FROM employee_master WHERE emp_code IN(".$emp_code.") order BY state ASC,zone ASC,branch_code_name,emp_name ASC";
		$resemployeelist=mysql_query($sqlemployeelist) or die(mysql_error()." Error in select employee list: ".$sqlemployeelist);
		
		$cntemployeelist=mysql_num_rows($resemployeelist);
		if($cntemployeelist >0)
		{
		$cnt=1;
		$value="";
		while($rowemployeelist=mysql_fetch_array($resemployeelist))
		{
			$emp_code=$rowemployeelist['emp_code'];
			$dns_emp_code=$rowemployeelist['dns_emp_code'];
			$emp_name=$rowemployeelist['emp_name'];
			$designation=$rowemployeelist['designation'];
			$state=$rowemployeelist['state'];
			$zone=$rowemployeelist['zone'];
			$HQ=$rowemployeelist['HQ'];
			$sale_access=$rowemployeelist['sale_access'];
			$acedns=$rowemployeelist['acedns'];
			$branch_code_name=$rowemployeelist['branch_code_name'];
			
			${absent_days.$emp_code}=0;
			${present_days.$emp_code}=0;
			${sunday_present.$emp_code}=0;
					
			$value=$cnt." \t";
			$value.=$state." \t";
			$value.=$zone." \t";
			$value.=$branch_code_name." \t";
			$value.=$dns_emp_code." \t";
			$value.=$emp_code." \t";
			$value.=$emp_name." \t";
			$value.=$acedns." \t";
			$value.=$designation." \t";
			$value.=$HQ." \t";
			$value.=$sale_access." \t";
			$start_date = strtotime("-1 day", strtotime($start_date_captured));
			$start_date= date("Y-m-d", $start_date);
						
			for($count_number_days=0;$count_number_days<=$number_days;$count_number_days++)
			{
				/*if(strlen($count_number_days)<2)
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
				$present_date=$year.'-'.$month_val.'-'.$date_value;*/
				$start_date = strtotime("+1 day", strtotime($start_date));
				$start_date= date("Y-m-d", $start_date);
				if($start_date<=$today)
				{
					$sqlinformation="SELECT DATE_FORMAT(LO.date,'%T') AS time,LO.trans_id FROM 
									 location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'W%' OR LO.trans_id LIKE 'L%') AND 
									 LO.emp_code='".$emp_code."' AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$start_date."'";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select attendance information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{
					   if(date('D',strtotime($start_date))!='Sun')
						{
							$value.="A"." \t";
					   		${absent_days.$emp_code}=${absent_days.$emp_code}+1;
						}
						else
						{
							$value.="Sunday"." \t";
						}
					}
					else{
						$rowinformation=mysql_fetch_array($resinformation);
						$time=$rowinformation['time'];
						$trans_id=$rowinformation['trans_id'];
						$trans_id_parts=substr($rowinformation['trans_id'],0,1);
						if(date('D',strtotime($start_date))=='Sun')
						{
							${sunday_present.$emp_code}=${sunday_present.$emp_code}+1;	
						}
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
						${present_days.$emp_code}=${present_days.$emp_code}+1;
					 }
				  }
				  else
				  {
					 $value.=""." \t"; 								 
				  }
				}
				$value.=${present_days.$emp_code}." \t";
				$value.=${absent_days.$emp_code}." \t";
				$value.=${sunday_present.$emp_code}." \t";
				$value.=''." \t";
				
				 $line .= $value."\n";
				 $cnt++;   
			}
			$datacontents=$excelheader."\n".$line;
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$filename="attendancedata/attendance_".$date.'_'.$month.'_'.$year.'_'.$hour.'_'.$minute.'_'.$second;
			if (file_exists("/home/acedns/public_html/misreport/$filename.xls")){
				unlink("/home/acedns/public_html/misreport/$filename.xls");
			}
			$fp = fopen("/home/acedns/public_html/misreport/$filename.xls","wb");
			fwrite($fp,$datacontents);
			fclose($fp);
			echo $filename;
			//header("Content-type: application/octet-stream"); 
			//header("Content-Disposition: attachment; filename=Order_Download_Report.xls"); 
			//print "$datacontents";
			//$nick_name=strtoupper($_SESSION['nick_name']);
			//echo $datacontents;	
	}
	else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}	
mysql_close($link);
?>