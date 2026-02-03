<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	//if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];

	if($_REQUEST['mode']=='edit_transaction_flag')   edit_transaction_flag();
	else                                            disphtml("main();");

ob_end_flush();

function main()
{
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
	if(document.frmSearch.from_date.value.search(/\S/)==-1)
	{
		alert('Please input a vlaue for From Date.');
		document.frmSearch.from_date.focus();
		return false;
	}
	if(document.frmSearch.to_date.value.search(/\S/)==-1)
	{
		alert('Please input a vlaue for To Date.');
		document.frmSearch.to_date.focus();
		return false;
	}
	return true;
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>EDIT TRANSACTION FLAG</strong></td>
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
					<input type="hidden" name="mode" value="edit_transaction_flag">
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
                                     $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ORDER BY emp_name ASC";
                                     $resultqueryemp = mysqli_query($link,$sqlqueryemp);
                                     $count=mysqli_num_rows($resultqueryemp);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
                                        {
                                      ?>
                                         <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if($emp_name==$rowqueryemp['emp_name'] || 
                                         $_REQUEST['emp_code']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
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
                                    <input type="submit" value="Change" class="inplogin" >
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
<?php }//End of main()
	 function edit_transaction_flag()
	 {
		$emp_code=$_REQUEST['emp_name'];
		$from_date=$_REQUEST['from_date'];
		$from_date=date('Y-m-d',strtotime($from_date));
		$to_date=$_REQUEST['to_date'];
		$to_date=date('Y-m-d',strtotime($to_date));
		
		$sqlupdate="update order_header set dwnld_transferred='NO' WHERE SUBSTRING(order_no,2,5)='".$emp_code."' 
					AND DATE_FORMAT(SUBSTRING(order_no,7,8),'%Y-%m-%d')>='".$from_date."' 
					AND DATE_FORMAT(SUBSTRING(order_no,7,8),'%Y-%m-%d')<='".$to_date."' AND SUBSTRING(order_no,1,1)='O'";
		if(mysqli_query($link,$sqlupdate)) 
		{
			$GLOBALS['err_msg']="Transaction flag updated successfully.";
			disphtml("main();");
		}
		else
		{
			$GLOBALS['err_msg']="Transaction flag updation unsuccessfull.";
			disphtml("main();");
		}
	 }
?>	