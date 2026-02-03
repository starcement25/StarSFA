<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	disphtml("main();");
ob_end_flush();
function select_fjp_date($combo_name, $selected_index, $style='')
{
	$curdate=date('d');
	$curmonth=date('m');
	$curyear=date('Y');
	$num = cal_days_in_month(CAL_GREGORIAN, $curmonth, $curyear); 
	$str_select_start = "<select name=".trim($combo_name)." id=".trim($combo_name)." class=\"".$style."\">";
	//$str_option = "<option value=\"\">DD</option>";
	$str_option='';
	$selected="";
	for($i = $curdate; $i <= $num; $i++)
	{
		if(strlen($i)==1)
		{
			$i='0'.$i;
		}
		$day_mon_year=$i.'-'.$curmonth.'-'.$curyear;
		if($day_mon_year == $selected_index) $selected = " selected ";
		else  $selected = " ";
		$str_option = $str_option."<option value=\"".$day_mon_year."\" ".$selected." >".stripslashes($day_mon_year)."</option>";
	}
	$str_select_end = "</select>";
	return $str_select_start.$str_option.$str_select_end;
	
}
function main()
{
?>
<script language="javascript">

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}

	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
 function showFJPPERIODdisplay()
 {
	document.getElementById('fjp_period_mode').value='showFJPperiod';
	document.frmFJP.submit();	
 }
 function changeFJPPERIOD()
 {
	document.getElementById('fjp_change_mode').value='changeFJPperiod';
	document.frmFJPChange.submit();	
 }
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Route Plan Access Period</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name = "frmFJP" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="fjp_period_mode" id="fjp_period_mode" value="">
            <input type="hidden" name="emp_code" id="emp_code" value="">
            <input type="hidden" name="emp_name" id="emp_name" value="">
			
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
                <br />
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" >
                     <tr>
                     	<td valign="top" align="left" width="50%"><strong>Route plan access period submit type</strong></td>
                        <td valign="top" align="left" width="5%">:</td>
                        <td valign="top" align="left" >
                        <select name="access_submit_type" onchange="javascript:showFJPPERIODdisplay();" >
                        	 <option value="" >SELECT</option>
                            <option value="indiv" <?php if($_REQUEST['access_submit_type']=='indiv'){ echo 'selected';}?>>INDIVIDUAL</option>
                             <option value="all" <?php if($_REQUEST['access_submit_type']=='all'){ echo 'selected';}?>>ALL</option>
                        </select> 
                        </td>
                    </tr>
                 </table>  
                 </form> 
                 <br />
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
              <?php if($_REQUEST['fjp_period_mode']=='showFJPperiod'){?>
              <form name = "frmFJPChange" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                <input type="hidden" name="fjp_change_mode" id="fjp_change_mode" value="">
                <input type="hidden" name="fjp_period_mode" id="fjp_period_mode" value="<?=$_REQUEST['fjp_period_mode']?>">
                <input type="hidden" name="access_submit_type" id="access_submit_type" value="<?=$_REQUEST['access_submit_type']?>">
                <input type="hidden" name="emp_code" id="emp_code" value="<?=$_REQUEST['emp_code']?>">
                <input type="hidden" name="emp_name" id="emp_name" value="<?=$_REQUEST['emp_name']?>">
              <table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="9" align="center"><strong>Route plan access period information</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="20%" align="left" style="padding:0px 20px 0px 20px;">Name</td>
                        <td width="20%" align="left" style="padding:0px 20px 0px 20px;">Start Date</td>
                        <td width="20%" align="left" style="padding:0px 20px 0px 20px;">End Date</td>
                        <td width="" align="left" style="padding-left:20px;"></td>
                        <!--td width="" align="left" style="padding-left:20px;"></td-->
                    </tr> 
						<?php
                            $sqlfieldforce="SELECT emp_code,emp_name FROM employee_master WHERE emp_code !='C0007'";
                            $resfieldforce=mysql_query($sqlfieldforce) or die(mysql_error()." Error in select field force: ".$sqlfieldforce);
                            while($rowfieldforce=mysql_fetch_array($resfieldforce)){
								
								$emp_code=$rowfieldforce['emp_code'];
								$sqlaccessperiod="SELECT DATE_FORMAT(access_start_date,'%d-%m-%Y') AS access_start_date,
											DATE_FORMAT(access_end_date,'%d-%m-%Y') AS access_end_date FROM route_plan_access_period WHERE emp_code= '".$emp_code."'";
								$resaccessperiod=mysql_query($sqlaccessperiod) or die(mysql_error()." Error in select access period: ".$sqlaccessperiod);
								$rowaccessperiod=mysql_fetch_array($resaccessperiod);
								$access_start_date=$rowaccessperiod['access_start_date'];
								$access_end_date=$rowaccessperiod['access_end_date'];
								if($access_start_date=='' || $access_end_date=='')
								{
									$access_start_date='N/A';
									$access_end_date='N/A';
								}
                        ?>
							<tr> 
                                <td valign="top" align="left" style="BORDER: #A92A61 1px solid;" ><?php echo $rowfieldforce['emp_code']?></td>
                                <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $rowfieldforce['emp_name']?></td>
                                <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $access_start_date;?></td>
								 <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $access_end_date;?></td>
                                <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><!--a href="javascript:void(0)" 
                onClick="javascript:showFJPPERIODdisplay('<?//$rowfieldforce['emp_code']?>','<?//$rowfieldforce['emp_name']?>')"  style="color:#930;font-weight:bold;">CHANGE FJP PERIOD</a-->
                				<input type="checkbox" name="chk_fjp_access[]" value="<?php echo $rowfieldforce['emp_code']?>" 
								<?php if(($_REQUEST['fjp_period_mode']=='showFJPperiod') && $_REQUEST['access_submit_type']=='all'){ echo 'checked';}?>/>
                                </td>
                                <!--td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"></td-->
                              </tr>
                             <?php				
							}
						?>
            </table><br /><br />
            	<?php  //if($_REQUEST['fjp_period_mode']=='showFJPperiod'){
				$access_start_date=$_REQUEST['start_date'];
				$access_end_date=$_REQUEST['end_date'];
				?>
            	 <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="9" align="center"><strong>Change Route plan access period </strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="30%" align="center">Start Date</td>
                        <td width="30%" align="center">End Date</td>
                    </tr> 
                    
						
                    <tr> 
                            <td valign="top" align="center" style="BORDER: #A92A61 1px solid;" >
							<?php echo select_fjp_date('start_date', $access_start_date,'');?></td>
                            <td align="center" valign="top" style="BORDER: #A92A61 1px solid;"><?php echo select_fjp_date('end_date', $access_end_date,'');?></td>
                            <!--td align="center" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
            onClick="javascript:changeFJPPERIOD('<?//$_REQUEST['emp_code']?>')"  style="color:#930;font-weight:bold;">CHANGE</a></td-->
                      </tr>	
                            
            </table>
            <br />
            <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" >
                 <tr>
                    <td valign="top" align="center" >
                    <input type="button" name="ok" value=" OK " onClick="javascript:changeFJPPERIOD()" />
                    </td>
                </tr>
             </table>   
            </form>
            <?php }
			if($_REQUEST['fjp_change_mode']=='changeFJPperiod'){
				
				$chk_fjp_access=$_REQUEST['chk_fjp_access'];
				
				foreach($chk_fjp_access as $value){
				//exit();
			 	$emp_code=$value;
				/*$sqlaccessperiod="SELECT access_start_date FROM route_plan_access_period WHERE emp_code= '".$emp_code."'";
				$resaccessperiod=mysql_query($sqlaccessperiod) or die(mysql_error()." Error in select access period: ".$sqlaccessperiod);
				$cntaccessperiod=mysql_num_rows($resaccessperiod);*/
				
				$sqldelfjp="DELETE FROM route_plan_access_period WHERE emp_code='".$emp_code."'";
				mysql_query($sqldelfjp);
				
				$access_start_date=$_REQUEST['start_date'];
				$access_start_date_ARR=explode('-',$access_start_date);
				$access_start_date_final=$access_start_date_ARR[2].'-'.$access_start_date_ARR[1].'-'.$access_start_date_ARR[0];
				$access_end_date=$_REQUEST['end_date'];
				$access_end_date_ARR=explode('-',$access_end_date);
				$access_end_date_final=$access_end_date_ARR[2].'-'.$access_end_date_ARR[1].'-'.$access_end_date_ARR[0];
				
					$sqlfjpperiodchange="INSERT INTO route_plan_access_period SET
										emp_code='".$emp_code."',
										access_start_date='".$access_start_date_final."',
										access_end_date='".$access_end_date_final."',
										modified_by='".$_SESSION['admin_login']."',
										modified_date=CURRENT_TIMESTAMP()";
					if(mysql_query($sqlfjpperiodchange)){
						$successval='1';
					}
					else
					{
						$successval='0';
					}
				}
				if($successval=='1')
				{
				?>
					<script language="JavaScript" type="text/javascript">alert('Route plan access period has been changed successfully.');window.location.href="adminRoutePlanAccess.php?fjp_period_mode=showFJPperiod&emp_code=<?php echo $emp_code;?>&access_submit_type=<?php echo $_REQUEST['access_submit_type'];?>";</script>
					<?php }else{
					?><script language="JavaScript" type="text/javascript">alert('Failure in changing of Roue plan access period information.');window.location.href="adminRoutePlanAccess.php?fjp_period_mode=showFJPperiod&emp_code=<?php echo $emp_code;?>&access_submit_type=<?php echo $_REQUEST['access_submit_type'];?>";</script>
				<?php
				}
			}
			?>
<?php }//End of main()?>