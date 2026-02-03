<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name']) == 'PRABHUJI' && $_SESSION['admin_login']=="admin" )
	{
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_CRM_CRE.php");
	}
	if($_SESSION['admin_login']=="")  		header("product:index.php");
	$GLOBALS['show']=30;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
		$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	$mode = $_REQUEST['mode'];

	if($mode == 'deactivate')						   deactivate_customer($_REQUEST['row_id']);
	elseif($mode=="activate")		   		  		   activate_customer($_REQUEST['row_id']);
	else    											disphtml("main();");
ob_end_flush();

function main()
{
	?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head><center>
    <?php
	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $contentsdate =$year.'-'.$month.'-'.$date;

	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
	$sql="SELECT EDSA.state_code,EM.emp_name,EM.emp_code FROM emp_datewise_state_allocation EDSA,employee_master EM WHERE 
	 		 EDSA.acedns='yes' AND EDSA.emp_code=EM.emp_code ORDER BY EDSA.state_code ASC";		
	 $rs=mysql_query($sql) or die(mysql_error()." Error in main: ".$sql);
	 $count=mysql_num_rows($rs);
?>
<script language="JavaScript">
function show_all()
{
	document.frmSearch.search_mode.value = "";	
	document.frmSearch.submit();	
}
</script>	

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
function active_customer(val1,val2)
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	document.getElementById('loader').style.display='';
	var url="selectactivatednewcustomer.php?emp_code="+val2+"&state="+val1;
	xmlHttp.onreadystatechange=showActivecustomer;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function showActivecustomer()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val !='')
		{
			document.getElementById('inactivecustomerdisplay').style.display='none';
			document.getElementById('activecustomerdisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		}
		else
		{
			document.getElementById('loader').style.display='';
		}
	}
 }
function inactive_customer(val1,val2)
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	document.getElementById('loader').style.display='';
	var url="selectinactivatednewcustomer.php?emp_code="+val2+"&state="+val1;
	xmlHttp.onreadystatechange=showInActivecustomer;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function showInActivecustomer()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val !='')
		{
			//alert(val);
			document.getElementById('activecustomerdisplay').style.display='none';
			document.getElementById('inactivecustomerdisplay').style.display='';
			document.getElementById('inactivecustomerdisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		}
		else
		{
			document.getElementById('loader').style.display='';
		}
	}
 }
</script>
<div id="display_main" style="max-height: 400px; max-width:1200px; overflow-y: scroll;" align="center">
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0" id="display_table">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> State Allocation Report</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
            
			<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="11">State Allocation Details</td>
				</tr>
			<? 
			if($count == 0)
			{ 
			?>
				<tr> 
					<td align="center" colspan="10">No records found</td>
				</tr>
			<?
			}
			else
			{	
			?>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
                    <td width="35%" align="left" style="padding-left:20px;">State name</td>
                    <td width="35%" align="left" style="padding-left:20px;">Employee name</td>
                    <td width="13%" align="left" style="padding-left:20px;">Active</td>
					<td width="12%" align="left" style="padding-left:20px;">Inactive</td>
				</tr>   
				<?
				$cnt=$GLOBALS[start]+1;
				$cutomer_route_state_array=array();
				while($rec=mysql_fetch_array($rs))
				{
					$state=$rec['state_code'];
					$emp_name=$rec['emp_name'];
					$emp_code=$rec['emp_code'];
					$sqlstatewiseactive="SELECT COUNT(customer_code) AS total_active_customer FROM customer_master 
										WHERE activated='yes' AND activated_by='".$emp_code."' AND state_code='".$state."'";
					$rsstatewiseactive=mysql_query($sqlstatewiseactive);
					$rowstatewiseactive=mysql_fetch_array($rsstatewiseactive);
					$total_active_customer=$rowstatewiseactive['total_active_customer'];
					
					$sqlstatewiseinactive="SELECT COUNT(customer_code) AS total_inactive_customer FROM customer_master_inactive 
										WHERE inactivated_by='".$emp_code."' AND state_code='".$state."'";
					$rsstatewiseinactive=mysql_query($sqlstatewiseinactive);
					$rowstatewiseinactive=mysql_fetch_array($rsstatewiseinactive);
					$total_inactive_customer=$rowstatewiseinactive['total_inactive_customer'];				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" ><?=stripslashes($state);?></td>
                     <td align="left" valign="top" ><?=stripslashes($emp_name);?></td>
                    <td  valign="top" align="right"><a href="javascript:active_customer('<?=$state;?>','<?=$emp_code?>');" title=" Active " style="color: #F00;"><?=$total_active_customer;?></a></td>
                     <td  valign="top" align="right"><a href="javascript:inactive_customer('<?=$state;?>','<?=$emp_code?>');" title=" Inactive " style="color: #F00;"><?=$total_inactive_customer;?></a></td>
				</tr>
			<? 
				} // end of while loop

			} // end of page count
			?>
            </table>
		</td>
	</tr>
</table>
</div>
<div id="activecustomerdisplay" style="display:none">
</div><br />
<div id="inactivecustomerdisplay" style="display:none">
</div><br />
<div id="loader" style="display:none">
                <br/>
               <center><img src="ajax-loader.gif" /></center>
               </div>
</center>
<?
}//End of main()
?>