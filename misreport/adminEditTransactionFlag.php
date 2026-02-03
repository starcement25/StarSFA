<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	
	if($_REQUEST['mode']=='edit_transaction_flag')   edit_transaction_flag();
	else                                            disphtml("main();");
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
function search_att(val)
{
	if(val=='yourchoice')
	{
		document.getElementById('datedropdown').style.display='';
	}
	else
	{
		document.getElementById('datedropdown').style.display='none';
	}
}
function select_depot(branch_code_val)
	{
	  document.frmdepot.branch_code.value=branch_code_val;
	  document.frmdepot.submit();
	}
	function check()
	{
		if (document.frmSearch.branch_name.value==0) 
		{
			alert('Please select a branch.');
			document.frmSearch.branch_name.focus();
			return false;
		}
		if (document.frmSearch.rds_name.value==0) 
		{
			alert('Please select a depot.');
			document.frmSearch.rds_name.focus();
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
        	<form name ="frmdepot" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="branch_code" value="">
            </form>
			<br><br>
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
                                <td align="right" width="45%" colspan="2">Branch:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $branch_name=$_REQUEST['branch_name'];?>
                                    <select name="branch_name" id="branch_name" onChange="javascript:select_depot(this.value);">
                                    <option value="0">SELECT</option>
                                     <?php 
                                     $sqlquerybranch="SELECT DISTINCT BM.branch_code,BM.branch_name FROM branch_master BM,employee_master EM 
									 					WHERE BM.branch_code=EM.branch_code ".$emp_hierarchy_condition_one." ORDER BY BM.branch_name ASC";
                                     $resultbranch = mysqli_query($link,$sqlquerybranch);
                                     $count=mysqli_num_rows($resultbranch);
                                     $cnt=1;
                                        if($count>0){
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
                            <tr>
                                <td align="right" width="45%" colspan="2">Depot:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php 
										$rds_name=$_REQUEST['rds_name'];
										$branch_code=$_REQUEST['branch_code'];
								?>	 
                                	<select name="rds_name" id="rds_name">
                                    <option value="0">SELECT</option>
                                     <?php 
									 $sqlqueryrds="SELECT RM.rds_code,RM.rds_name,RM.emp_code FROM rds_master RM,employee_master EM WHERE 
									 			RM.emp_code=EM.emp_code AND EM.branch_code='".$branch_code."' ".$emp_hierarchy_condition_one."";
									 $resultrds = mysqli_query($link,$sqlqueryrds);
									 $countrds=mysqli_num_rows($resultrds);
									 if($countrds>0){
									while($rowrds = mysqli_fetch_assoc($resultrds))
									{
										//echo $emp_code=$rowrds['emp_code'];
                                     ?>
                                         <option value=<?php echo $rowrds['rds_code']; ?> <?php if( $rds_name==$rowrds['rds_code']){echo 'selected';}?>>
                                         <?php echo $rowrds['rds_name']; ?>
                                         </option>
                                       <?php }?>
                                   <?php }?>   
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
                                    <input type="submit" value="Change" class="inplogin" onclick="javascript:RdswiseDataDownload();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
				 <?php 
	 }//End of main()
	 function edit_transaction_flag()
	 {
		 $branch_name=$_REQUEST['branch_name'];
		$rds_name=$_REQUEST['rds_name'];
		$from_date=$_REQUEST['from_date'];
		$from_date=date('Y-m-d',strtotime($from_date));
		$to_date=$_REQUEST['to_date'];
		$to_date=date('Y-m-d',strtotime($to_date));
		
		$sqlemp="SELECT emp_code FROM rds_master WHERE rds_code='".$rds_name."'";
		$rsemp=mysqli_query($link,$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);
		$emp_code=$rowemp['emp_code'];
		
		$sqlupdate="update order_header set downld_transferred='NO' WHERE SUBSTRING(order_no,2,5)='".$emp_code."' 
					AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')>='".$from_date."' 
					AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')<='".$to_date."' AND SUBSTRING(order_no,1,1)='O'";
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