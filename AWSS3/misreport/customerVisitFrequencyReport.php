<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];

	if(!$_GET)
	{
	disphtml("main();");
	}
	else
	{
		csvexport();
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
	if (document.frmSearch.emp_name.value==0) 
	{
		alert('Please select a employee.');
		document.frmSearch.emp_name.focus();
		return false;
	}
	if(document.frmSearch.from_date.value.search(/\S/)==0)
	{
		if(document.frmSearch.to_date.value.search(/\S/)==-1)
		{
			alert('Please input a value for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}
	return true;
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Visit Frequency Report</strong></td>
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
                
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="visit_frequency_display">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                                <td align="right" width="45%" colspan="2">Employee:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $emp_name=$_REQUEST['emp_name'];?>
                                    <select name="emp_name" id="emp_name" >
                                    <option value="0">SELECT</option>
                                     <?php 
                                     $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ".$emp_hierarchy_condition." AND acedns!='N' ORDER BY emp_name ASC";
                                     $resultqueryemp = mysql_query($sqlqueryemp);
                                     $count=mysql_num_rows($resultqueryemp);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                        {
                                      ?>
                                         <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if($emp_name==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                       <?php
                                        }
                                      }
                                        ?>	
                                     </select>
                                </td>
                           </tr>
                            <tr id="datedropdown" >
                                <td align="left" width="15%">From Date:</td>
                                <td align="left" width="30%" style="vertical-align:top;">
                                		<?php $from_date=$_REQUEST['from_date'];?>
                                      <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date"></input>&nbsp;
                                        <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
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
                                <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                                <td width="" style="vertical-align:top;">
                                    <?php $to_date=$_REQUEST['to_date'];?>
                                     <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="to_date"></input>&nbsp;
                                        <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
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
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="submit" value="Submit" class="inplogin">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
<?php //End of main()
if($_REQUEST['mode'] =='visit_frequency_display'){
	$emp_name=$_REQUEST['emp_name'];
	$from_date=$_REQUEST['from_date'];
	$to_date=$_REQUEST['to_date'];
	$from_date=date('Y-m-d',strtotime($from_date));
	$to_date=date('Y-m-d',strtotime($to_date));
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$from_date."' AND 
					  				DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$to_date."'";
	}
	$emp_condition=" AND (SUBSTRING(OH.order_no,2,5)='".$emp_name."' OR SUBSTRING(OH.order_no,3,5)='".$emp_name."')";
		
	$sqlcustomervisit="SELECT CM.customer_code, CM.customer_name, COUNT(OH.order_no) AS no_of_visit FROM customer_master CM,order_header OH WHERE OH.customer_code=CM.customer_code ".$emp_condition.$date_condition." GROUP BY OH.customer_code ORDER BY CM.customer_name ASC";
?>
	<div id="display">
	<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="60%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" >
                    <tr class="TDHEAD" > 
                        <td colspan="9" align="center"><strong>Visit Details</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="left" style="padding:0px 20px 0px 20px;">Sl</td>
                        <td width="70%" align="left" style="padding:0px 20px 0px 20px;">Customer Name</td>
                        <td width="" align="left" style="padding-left:20px;">No of Visit</td>
                        <td width="" align="left" style="padding-left:20px;">Quantity</td>
                    </tr> 
                    <?php
							$rscustomervisit=mysql_query($sqlcustomervisit) or die(mysql_error()." Error in total visit: ".$sqlcustomervisit);
							$countcustomervisit=mysql_num_rows($rscustomervisit);
							$count=1;
							if($countcustomervisit <1)
							{
					?>
                     <tr > 
                        <td align="center" colspan="3" >No records found.</td>
                    </tr> 
                    <?php			
							}
							while($rowcustomervisit=mysql_fetch_array($rscustomervisit))
							{
								$sql_total_quantity = "SELECT SUM(OD.qty) FROM order_header OH, order_details OD WHERE OH.customer_code='".$rowcustomervisit['customer_code']."' AND OH.order_no=OD.order_no ".$date_condition;
								$res_total_quantity = mysql_query($sql_total_quantity);
								$row_total_quantity = mysql_fetch_array($res_total_quantity);
								$total_quantity = $row_total_quantity['SUM(OD.qty)'];
								$sum_total_visit += $rowcustomervisit['no_of_visit'];
								$sum_total_quantity += $total_quantity;
					?>
							<tr> 
                                    <td valign="top" align="right" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $count++;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowcustomervisit['customer_name'];?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowcustomervisit['no_of_visit'];?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php if($total_quantity != '') echo $total_quantity; else echo "0";?></td>
                              </tr>
                             <?php				
							}
						?>
                        <tr>
                        	<td style="padding-left:20px;BORDER: #A92A61 1px solid;"><b>Total</b></td>
                            <td style="padding-left:20px;BORDER: #A92A61 1px solid;"></td>
                            <td style="padding-left:20px;BORDER: #A92A61 1px solid;" align="right"><?php echo $sum_total_visit;?></td>
                            <td style="padding-left:20px;BORDER: #A92A61 1px solid;" align="right"><?php echo $sum_total_quantity;?></td>
                        </tr>
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
	window.open('customerVisitFrequencyReport.php?emp_name='+emp_name+'&start_date='+start_date+'&end_date='+end_date,'mywindow');
}
</script>
<?php		
  }
    
}
?>
<?php
function csvexport()
{
	if($_GET['emp_name'])
	{
	$setExcelName = 'Customer Frequency';
	$emp_name = $_REQUEST['emp_name'];
	$from_date = $_REQUEST['start_date'];
	$to_date = $_REQUEST['end_date'];
	
	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));
	
	$count = 1;
	$excelheader = "SI"."\t"."Customer Name"."\t"."No of Visit"."\t"."Quantity"."\t"."\n";
	$sqlcustomervisit="SELECT CM.customer_code, CM.customer_name,COUNT(OH.order_no) AS no_of_visit FROM customer_master CM,order_header OH WHERE OH.customer_code=CM.customer_code AND (SUBSTRING(OH.order_no,2,5)='".$emp_name."' OR SUBSTRING(OH.order_no,3,5)='".$emp_name."') AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$to_date."' GROUP BY OH.customer_code ORDER BY CM.customer_name ASC";
	$rescustomervisit = mysql_query($sqlcustomervisit);
	while($rowcustomervisit = mysql_fetch_array($rescustomervisit))
	{
		$sql_total_quantity = "SELECT SUM(OD.qty) as total_quantity FROM order_header OH, order_details OD WHERE OH.customer_code='".$rowcustomervisit['customer_code']."' AND OH.order_no=OD.order_no AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$to_date."'";
		$res_total_quantity = mysql_query($sql_total_quantity);
		$row_total_quantity = mysql_fetch_array($res_total_quantity);
		$total_quantity = $row_total_quantity['total_quantity'];
		if($total_quantity == '')
			$total_quantity = 0;
		
		$customer_name = $rowcustomervisit['customer_name'];
		$no_of_visit = $rowcustomervisit['no_of_visit'];
		
		$sum_total_visit += $rowcustomervisit['no_of_visit'];
		$sum_total_quantity += $total_quantity;
								
		$excelcontents .= $count."\t".$customer_name."\t".$no_of_visit."\t".$total_quantity."\n";
		$count++;
	}
	
	$excelcontents .= "Total"."\t"."\t".$sum_total_visit."\t".$sum_total_quantity."\n";
	header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"); 
	header("Content-Disposition: attachment; filename=Customer_Frequency_Report.xls"); 
	
	echo $excelheader;
	echo $excelcontents;
	}
}
?>