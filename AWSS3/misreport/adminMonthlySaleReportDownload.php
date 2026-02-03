<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	
	if($_REQUEST['search_mode']=='exceldownloadpurchase' || $_REQUEST['search_mode']=='exceldownloadsales')
	{
		$search_mode=$_REQUEST['search_mode'];
		exceldownloaddata($search_mode);	
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
 function adminPurchaseDownload()
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
	  document.frmSearch.search_mode.value="exceldownloadpurchase";	
	  document.frmSearch.submit();
	}
	function adminSalesDownload()
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
	  document.frmSearch.search_mode.value="exceldownloadsales";	
	  document.frmSearch.submit();
	}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong>Administrator >>Monthly Sale Report Download</strong></td>
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
                                <tr>
                                    <td align="right" width="45%" colspan="2">Branch:</td>
                                    <td align="left" width="" style="vertical-align:top;" colspan="2">
                                       <?php $branch_name=$_REQUEST['branch_name'];
                                        $emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);
                                        ?>
                                        <select name="branch_name" id="branch_name" onChange="javascript:select_depot(this.value);">
                                        <?php if(count($emp_upper_hierarchy_array)==1 || $_SESSION['admin_login']=="admin") {?>
                                        <option value="all">ALL</option>
                                         <?php } 
                                         $sqlquerybranch="SELECT DISTINCT BM.branch_code,BM.branch_name FROM branch_master BM,employee_master EM 
                                                          WHERE BM.branch_code=EM.branch_code ".$emp_hierarchy_condition_one." ORDER BY BM.branch_name ASC";
                                         $resultbranch = mysql_query($sqlquerybranch);
                                         $count=mysql_num_rows($resultbranch);
                                         $cnt=1;
                                         if($count>0){
                                            while($rowbranch = mysql_fetch_array($resultbranch))
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
                            <tr>
                                <td align="left" width="15%">Month:</td>
                                <td align="left" width="20%" style="vertical-align:top;">
                                     <?php 
                                     $month=$_REQUEST['month'];
                                     echo PopulateSelectDefault('month', "SELECT DATE_FORMAT(date,'%M') AS month,DATE_FORMAT(date,'%m') AS month_value FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='O' GROUP BY DATE_FORMAT(date,'%m-%Y') ORDER BY YEAR(date) DESC,MONTH(date) DESC", 'month_value', 'month', $month,'','inplogin');?>					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">Year:</td>
                                <td width="20%" style="vertical-align:top;">
                                    <?php 
                                    $year=$_REQUEST['year'];
                                    echo PopulateSelectDefault('year', "SELECT DATE_FORMAT(date,'%Y') AS year FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='O' GROUP BY DATE_FORMAT(date,'%Y') ORDER BY DATE_FORMAT(date,'%Y') DESC", 'year', 'year', $year,'','inplogin');?>					
                                </td>
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Purchase Download" class="inplogin" onClick="javascript:adminPurchaseDownload();">
                                    <input type="button" value="Sales Download" class="inplogin" onClick="javascript:adminSalesDownload();">
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
	function exceldownloaddata($search_mode)
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
		$branch_code=$_REQUEST['branch_name'];
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
		
		$excelheader="Region". "\t";
		$excelheader.="Employee Name". "\t";
		$excelheader.="Branch". "\t";
		
		for($count_number_days=1;$count_number_days<=$number_days;$count_number_days++)
		{
			$excelheader.=date('F ,d Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		}
		//$excelsubheader="Attendance Details of ".date("F", strtotime($month)).",".date("Y", strtotime($year));
		if($branch_code=='all'){
			$sqlbranchlist="SELECT branch_name,branch_code FROM branch_master ORDER BY branch_name DESC";
		}
		else
		{
			$sqlbranchlist="SELECT  branch_name,branch_code FROM branch_master WHERE branch_code='".$branch_code."'";
		}
		$rsbranchlist=mysql_query($sqlbranchlist);
		while($rowbranchlist=mysql_fetch_array($rsbranchlist))
		{
			$fetched_branch_code=$rowbranchlist['branch_code'];
			$fetched_branch_name=$rowbranchlist['branch_name'];
			$sqlemployeelist="SELECT emp_code,emp_name FROM employee_master WHERE SUBSTRING(emp_code,1,1)!='C' 
							AND branch_code='".$fetched_branch_code."' order BY emp_name ASC";
			$resemployeelist=mysql_query($sqlemployeelist) or die(mysql_error()." Error in select employee list: ".$sqlemployeelist);
			$cnt=1;
			$value="";
			while($rowemployeelist=mysql_fetch_array($resemployeelist))
			{
				$emp_code=$rowemployeelist['emp_code'];
				$emp_name=$rowemployeelist['emp_name'];
				$sqlrds="SELECT rds_name FROM rds_master WHERE emp_code='".$emp_code."'";
				$resrds=mysql_query($sqlrds) or die(mysql_error()." Error in select rds: ".$sqlrds);
				$rowrds=mysql_fetch_array($resrds);
				$rds_name=$rowrds['rds_name'];
				$cnt=1;
				$value="";
				$value.=$fetched_branch_name." \t";
				$value.=$emp_name." \t";
				$value.=$rds_name." \t";			
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
					if($search_mode=='exceldownloadpurchase')
					{
						$trans_type_condition=" AND OH.transaction_type='PB'";
						$excel_name="Purchase_Details";
					}
					if($search_mode=='exceldownloadsales')
					{
						$trans_type_condition=" AND OH.transaction_type='SB'";
						$excel_name="Sales_Details";
					}
					$sqlinformation="SELECT OH.d_instruction FROM order_header OH,location LO WHERE OH.order_no=LO.trans_id 
										AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' 
									 ".$trans_type_condition." AND DATE_FORMAT(LO.date,'%Y-%m-%d')='".$present_date."' ORDER BY 
									 DATE_FORMAT(LO.date,'%Y-%m-%d %H-%i-%s') DESC LIMIT 0,1";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select purchase or sales information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{
						$value.="0"." \t";
					}
					else{
						$rowinformation=mysql_fetch_array($resinformation);
						$d_instructionArray=explode(';',$rowinformation['d_instruction']);
						if($d_instructionArray['1']!='')
						{
							$d_instruction=date('d/m/Y',strtotime($d_instructionArray['1']));
						}
						else
						{
							$d_instruction='0';
						}
						$value.=$d_instruction." \t";
					}
				}
			$line .= $value."\n"; 
			$cnt++;
			}
			$line.="\n";
		}
		//exit();
		$data = str_replace("\r","",$line);
		header("Content-type: application/x-msdownload"); 
		header("Content-Disposition: attachment; filename=".$excel_name."_".date("F", strtotime('01-'.$month.'-'.$year))."_".date("Y", strtotime($year)).".xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		print "$excelheader\n$data";
	}
?>