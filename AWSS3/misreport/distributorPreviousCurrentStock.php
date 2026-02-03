<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];

	disphtml("main();");
ob_end_flush();


function main()
{
?>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                    <tr height="100"> 
                        <td colspan="7" align="center"><strong>No Primary Sales Found.</strong></td>
                    </tr>
                </table>
        </td>
    </tr>
</table>                    
<?php	
}

function notmain()
{
  if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND CM.emp_code IN('.$emp_hierarchy.')';
	}
?>
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
			alert('Please input a vlaue for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}
	return true;
}
</script>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
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
					<input type="hidden" name="mode" value="stock_details_display">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                                <td align="center" width="30%" colspan="2">Customer:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $customer_code=$_REQUEST['customer_code'];?>
                                    <select name="customer_code" id="customer_code" >
                                    <option value="0">SELECT</option>
                                     <?php 
                                     $sqlquerycustomer="SELECT DISTINCT CM.customer_code,CM.customer_name FROM customer_master CM,stock_audit SA WHERE 
									 					SA.customer_code=CM.customer_code AND CM.customer_code NOT LIKE 'N%' ".$emp_hierarchy_condition." 
														ORDER BY CM.customer_name ASC";
									 $rsquerycustomer=mysql_query($sqlquerycustomer);					
                                     $count=mysql_num_rows($rsquerycustomer);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowquerycustomer = mysql_fetch_array($rsquerycustomer))
                                        {
                                      ?>
                                         <option value="<?php echo $rowquerycustomer['customer_code'];?>" <?php if($customer_code==$rowquerycustomer['customer_code']){echo 'selected';}?>><?php echo $rowquerycustomer['customer_name'];?></option>
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
                                     echo PopulateSelectDefault('month', "SELECT DATE_FORMAT(date,'%M') AS month,DATE_FORMAT(date,'%m') AS month_value FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='S' GROUP BY DATE_FORMAT(date,'%m-%Y') ORDER BY YEAR(date) DESC,MONTH(date) DESC", 'month_value', 'month', $month,'','inplogin');?>					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">Year:</td>
                                <td width="20%" style="vertical-align:top;">
                                    <?php 
                                    $year=$_REQUEST['year'];
                                    echo PopulateSelectDefault('year', "SELECT DATE_FORMAT(date,'%Y') AS year FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='S' GROUP BY DATE_FORMAT(date,'%Y') ORDER BY DATE_FORMAT(date,'%Y') DESC", 'year', 'year', $year,'','inplogin');?>					
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
if($_REQUEST['mode'] =='stock_details_display'){
	$customer_code=$_REQUEST['customer_code'];
	$month=$_REQUEST['month'];
	$monthprevious=$month-1;
	$year=$_REQUEST['year'];
	$month_year_cndition= " AND DATE_FORMAT(LO.date,'%m')='".$month."' AND DATE_FORMAT(LO.date,'%Y')='".$year."'";
	$month_year_cndition_previous= " AND (DATE_FORMAT(LO.date,'%m')='".$monthprevious."' OR DATE_FORMAT(LO.date,'%m')='".$month."' ) AND DATE_FORMAT(LO.date,'%Y')='".$year."'";
		
	$product_name_array=array();
	$previous_stock_array=array();
	$current_stock_array=array();
	$sqlstockauditcurrent="SELECT LO.trans_id,PM.prod_desc,SA.quantity FROM stock_audit SA,location LO,product_master PM
						WHERE SA.transaction_id=LO.trans_id AND SA.product_code=PM.prod_code AND 
						SA.customer_code='".$customer_code."' ".$month_year_cndition." ORDER BY LO.date DESC LIMIT 0,1";
    $rsstockauditcurrent=mysql_query($sqlstockauditcurrent) or die(mysql_error()." Error in stock audit fetch: ".$sqlstockauditcurrent);
	while($rowstockauditcurrent=mysql_fetch_array($rsstockauditcurrent))
	{
		$prod_desc=$rowstockauditcurrent['prod_desc'];
		$quantity=$rowstockauditcurrent['quantity'];
		array_push($product_name_array,$prod_desc);
		${currentstock.$prod_desc}=${currentstock.$prod_desc}+$quantity;
		if(${currentstock.$prod_desc}=='')
		{
			${currentstock.$prod_desc}=0;
		}
	}
	$sqlstockauditprevious="SELECT LO.trans_id,PM.prod_desc,SA.quantity FROM stock_audit SA,location LO,product_master PM
						WHERE SA.transaction_id=LO.trans_id AND SA.product_code=PM.prod_code AND 
						SA.customer_code='".$customer_code."' ".$month_year_cndition." ORDER BY LO.date DESC LIMIT 1,1";
    $rsstockauditprevious=mysql_query($sqlstockauditprevious) or die(mysql_error()." Error in stock audit previous fetch: ".$sqlstockauditprevious);
	while($rowstockauditprevious=mysql_fetch_array($rsstockauditprevious))
	{
		$prod_desc=$rowstockauditprevious['prod_desc'];
		$quantity=$rowstockauditprevious['quantity'];
		if(!in_array($prod_desc,$product_name_array))
		{
			array_push($product_name_array,$prod_desc);
		}
		${previousstock.$prod_desc}=${previousstock.$prod_desc}+$quantity;
		if(${previousstock.$prod_desc}=='')
		{
			${previousstock.$prod_desc}=0;
		}
	}
					
?>						
	<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" >
                    <tr class="TDHEAD" > 
                        <td colspan="8" align="center"><strong>Stock Audit Details</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="left" style="padding:0px 20px 0px 20px;">Sl</td>
                        <td width="" align="left" style="padding:0px 20px 0px 20px;">Product</td>
                        <td width="20%" align="left" style="padding-left:20px;">Previous Stock Qty</td>
                        <td width="15%" align="left" style="padding-left:20px;">Order Supplied</td>
                        <td width="20%" align="left" style="padding-left:20px;">Current Stock Qty</td>
                    </tr> 
                    <?php
							
							$count=1;
							if(count($product_name_array)<1){
					?>
                     <tr > 
                        <td align="center" colspan="3" >No records found.</td>
                    </tr> 
                    <?php }
							for($i=0;$i<count($product_name_array);$i++)
							{
					?>
							<tr> 
                                    <td valign="top" align="right" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $count;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $product_name_array[$i];?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo ${previousstock.$product_name_array[$i]};?></td>
                                     <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo ${currentstock.$product_name_array[$i]};?></td>
                              </tr>
                             <?php
							 	$count++;			
							}
						?>
            </table>
       </td>
    </tr>
 </table>   
<?php		
  }
}
?>	