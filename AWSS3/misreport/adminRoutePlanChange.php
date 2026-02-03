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

 function RoutePlandisplay(val)
	{
		//alert(val);
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		if(document.getElementById('employeewiserouteplan'))
		{
			document.getElementById('employeewiserouteplan').style.display='none';
		}
		
		var url="selectEmployeeWiseRoutePlan.php?emp_code="+val;
		xmlHttp.onreadystatechange=showDetailsRoutePlan;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	  }

function showDetailsRoutePlan()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('employeewiserouteplan').style.display='';
		 	document.getElementById('employeewiserouteplan').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }

</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Route Plan Change</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
                <br />
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
              <form name = "frmFJPChange" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                <input type="hidden" name="fjp_change_mode" id="fjp_change_mode" value="">
                <input type="hidden" name="fjp_period_mode" id="fjp_period_mode" value="<?=$_REQUEST['fjp_period_mode']?>">
                <input type="hidden" name="access_submit_type" id="access_submit_type" value="<?=$_REQUEST['access_submit_type']?>">
                <input type="hidden" name="emp_code" id="emp_code" value="<?=$_REQUEST['emp_code']?>">
                <input type="hidden" name="emp_name" id="emp_name" value="<?=$_REQUEST['emp_name']?>">
              <table width="40%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="9" align="center"><strong>Employee information</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="30%" align="left" style="padding:0px 20px 0px 20px;">Name</td>
                        <td width="" align="left" style="padding:0px 20px 0px 20px;">VIEW ROUTE PLAN</td>
                    </tr> 
						<?php
                            $sqlfieldforce="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,route_plan RP 
											WHERE EM.emp_code !='C0007' AND EM.emp_code=RP.emp_code AND RP.visit_date >= CURDATE() GROUP BY RP.emp_code";
                            $resfieldforce=mysql_query($sqlfieldforce) or die(mysql_error()." Error in select field force: ".$sqlfieldforce);
                            while($rowfieldforce=mysql_fetch_array($resfieldforce)){
								
								$emp_code=$rowfieldforce['emp_code'];
								$emp_name=$rowfieldforce['emp_name'];
                        ?>
							<tr> 
                                <td valign="top" align="left" style="BORDER: #A92A61 1px solid;" ><?php echo $emp_name;?></td>
                                <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
					onClick="javascript:RoutePlandisplay('<?php echo $emp_code;?>')"  style="color:#930;font-weight:bold;">VIEW</td>
                              </tr>
                         <?php }?>     
                             
            </table><br /><br />
            	
			 <div id="employeewiserouteplan" style="display:none">
                </div><br />	
				<div id="loader" style="display:none">
                <br/>
               <center><img src="ajax-loader.gif" /></center>
               </div>
<?php }//End of main()?>