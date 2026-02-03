<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	

	disphtml("main();");
ob_end_flush();
function main()
{
	$date = $_REQUEST['date'];
	if($_REQUEST['date']=="")  		$date = date('d-m-Y');
	
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		$emp_upper_hierarchy='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND emp_code IN('.$emp_hierarchy.')';
		$emp_upper_hierarchy=return_employee_upper_hierarchy($_SESSION['admin_login']);
		if(strpos($emp_upper_hierarchy,',')==false){
			$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
		}
	}
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

function showCardWiseTransactiondisplay(val,val1,val2,val3,val4)
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	if(val1=='all' && val2=='all'){
		var url="selectCardWiseAllTransaction.php?card_no="+val+"&month="+val1+"&year="+val2+"&rds_name="+val3+"&vertical="+val4;
	}
	else
	{
		var url="selectCardWiseTransaction.php?card_no="+val+"&month="+val1+"&year="+val2+"&rds_name="+val3+"&vertical="+val4;
	}
	xmlHttp.onreadystatechange=showDetailsCardWiseAllTransaction;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }

function showDetailsCardWiseAllTransaction()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			document.getElementById('cardwisealldisplay').style.display='';
		 	document.getElementById('cardwisealldisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
 
 function showCardWiseSelTransactiondisplay(val,val1,val2,val3,val4)
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="selectCardWiseTransaction.php?card_no="+val+"&month="+val1+"&year="+val2+"&rds_name="+val3+"&vertical="+val4;
	xmlHttp.onreadystatechange=showDetailsCardWiseSelTransaction;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }

function showDetailsCardWiseSelTransaction()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
	//alert(val);
		if(val!="")
		 {
			document.getElementById('cardwiseseldisplay').style.display='';
		 	document.getElementById('cardwiseseldisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
 function showLoyaltydisplay()
	{
	  document.frmSearch.search_mode.value='Loyaltysearch';
	  document.frmSearch.submit();
	}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>LOYALTY REPORT</strong></td>
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
                        <td colspan="7" align="center"><strong>LOYALTY Report</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                            <td align="right" width="15%" colspan="2">Vertical:</td>
                            <td align="left" width="" style="vertical-align:top;" colspan="2">
                                 <?php 
                                 $verical=$_REQUEST['vertical'];
								 //echo exp(-5);
                                 ?>
                                 <select name="vertical">
                                 	 <option value="all" <?php if($verical=='all'){echo 'selected';}?>>ALL</option>
                                     <option value="FMCG" <?php if($verical=='FMCG'){echo 'selected';}?>>FMCG</option>
                                     <option value="FUEL" <?php if($verical=='FUEL'){echo 'selected';}?>>FUEL</option>
                                     <option value="HSD" <?php if($verical=='HSD'){echo 'selected';}?>>HSD</option>
                                     <option value="LUBE" <?php if($verical=='LUBE'){echo 'selected';}?>>LUBE</option>
                                     <option value="MS" <?php if($verical=='MS'){echo 'selected';}?>>MS</option>
                                 </select>
                            </td>
                           </tr>
                            <tr>
                                <td align="right" width="15%" colspan="2">Depot:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                     <?php 
                                     $rds_name=$_REQUEST['rds_name'];
                                     ?>
                                     <!--select name="outlet">
                                         <option value="all" <?php /*if($outlet=='all'){echo 'selected';}?>>ALL</option>
                                         <?php
                                            $sqloutlet="SELECT * from outlet_master order by outlet_name ASC";
                                            $rsoutlet=mysqli_query($link,$sqloutlet);
                                            while($recoutlet=mysqli_fetch_assoc($rsoutlet)){
                                         ?>
                                         <option value=<?php echo $recoutlet['outlet_code']; ?> <?php if($outlet==$recoutlet['outlet_code']){echo 'selected';}?>>
                                         <?php echo $recoutlet['outlet_name'];?>
                                         </option>
                                         <?php }*/?>
                                     </select-->
                                     <select name="rds_name" id="rds_name">
                                     <?php if(strtolower($emp_upper_hierarchy) ==strtolower($_SESSION['admin_login']) || $emp_upper_hierarchy==''){?>
                                    <option value="all" <?php if($rds_name=='all'){echo 'selected';}?>>ALL</option>
                                     <?php }
									 $sqlqueryrds="SELECT rds_code,rds_name,emp_code FROM rds_master WHERE 1 ".$emp_hierarchy_condition_one." ORDER BY rds_name ASC";
									 $resultrds = mysqli_query($link,$sqlqueryrds);
									 $countrds=mysqli_num_rows($resultrds);
									 if($countrds>0){
										while($rowrds = mysqli_fetch_assoc($resultrds))
										{
										 ?>
											 <option value=<?php echo $rowrds['rds_code']; ?> <?php if( $rds_name==$rowrds['rds_code']){echo 'selected';}?>>
											 <?php echo $rowrds['rds_name']; ?>
											 </option>
										<?php }
                                   		}?>
                                     </select>
                                </td>
                           </tr>
                            <tr>
                                <td align="left" width="15%">Month:</td>
                                <td align="left" width="20%" style="vertical-align:top;">
                                     <?php 
                                     $month=$_REQUEST['month'];
                                     echo PopulateSelectMonthYear('month', "SELECT DATE_FORMAT(date,'%M') AS month,DATE_FORMAT(date,'%m') AS month_value FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='L' GROUP BY DATE_FORMAT(date,'%m-%Y') ORDER BY DATE_FORMAT(date,'%m-%Y') ASC", 'month_value', 'month', $month,'','inplogin');?>					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">Year:</td>
                                <td width="20%" style="vertical-align:top;">
                                    <?php 
                                    $year=$_REQUEST['year'];
                                    echo PopulateSelectMonthYear('year', "SELECT DATE_FORMAT(date,'%Y') AS year FROM location WHERE emp_code!='C0007' AND SUBSTRING(trans_id,1,1)='L' GROUP BY DATE_FORMAT(date,'%Y') ORDER BY DATE_FORMAT(date,'%Y') ASC", 'year', 'year', $year,'','inplogin');?>					
                                </td>
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" onClick="javascript:showLoyaltydisplay();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                        </form>
               		</td>
              	</tr> 
            </table><br />
         <!------------------------------------------------End of Table for first time page loading----------------------------------------------!-->
  			<div id="AttendanceActivity" style="display:none;">
                </div><br />
  <!----------------------------------------Start of Table for populate employee date wise locate back-----------------------------------------------------!-->
 <?php
 	//For date wise Employee activity listing
	if($_REQUEST['search_mode']=='Loyaltysearch')
	{
		$vertical_value=$_REQUEST['vertical'];
		$month=$_REQUEST['month'];
		$year=$_REQUEST['year'];
		$rds_name=$_REQUEST['rds_name'];
		if($rds_name!='all'){
			$rds_condition=" AND CT.rds_code='".$rds_name."'";
		}
		else
		{
			$rds_condition="";
		}
		if($month!=="all" && $year!=="all")
		{
			$month_year_cndition= " AND DATE_FORMAT(LLT.date,'%m')='".$month."' AND DATE_FORMAT(LLT.date,'%Y')='".$year."'";
		}
		else
		{
			$month_year_cndition="";
		}
		if($vertical_value!='all')
		{
			$vertical_condition=" AND CT.trans_type='".$vertical_value."'";
		}
		else
		{
			$vertical_condition="";
		}
		$tablevalloyalty='<div id="LoyaltyActivityBack" style="display:""">
			<table width="75%" align="center" border="0"y cellpadding="5" cellspacing="2" class="border" 
						style="height: 330px;overflow-y: scroll;display:block;" id="todayAttDisplay">
						<tr class="TDHEAD" > 
							<td colspan="8" align="center"><strong>Loyalty Transaction Details</strong></td>
						</tr>
						<tr class="TDHEAD_SUB"> 
							<td width="5%" align="center">Sl</td>
							<td width="" align="left" style="padding-left:20px;">Loyalty Card Holder</td>
							<td width="15%" align="left" style="padding-left:20px;">Count of <br />transaction</td>
							<td width="20%" align="left" style="padding-left:20px;">Card No.</td>
							<td width="10%" align="left" style="padding-left:20px;">Phone No.</td>
							<td width="10%" align="left" style="padding-left:20px;">Amount</td>
							<td width="6%" align="left" style="padding-left:20px;">Point</td>
							<td width="6%" align="left" style="padding-left:20px;"></td>
						</tr>'; 
						
						$sqlinformation="SELECT LCHM.loyalty_card_holder_name,LCHM.loyalty_card_no,LCHM.phone_no,
										SUM(CT.purchase_value) AS amount,COUNT(LLT.trans_id) AS no_transaction FROM 
										location LLT,card_transaction CT,loyalty_card_holder_master LCHM
										 WHERE LLT.trans_id=CT.transaction_id AND CT.loyalty_card_no=LCHM.loyalty_card_no 
										  AND LLT.emp_code!='C0007' ".$vertical_condition.$rds_condition.$month_year_cndition." 
										GROUP BY CT.loyalty_card_no ORDER BY LCHM.loyalty_card_holder_name ASC ";
						$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
						$count=mysqli_num_rows($resinformation);
						if($count==0)
						{
							$tablevalloyalty.='<tr><td align="center" colspan="7">No records found.</td></tr>';
							
						 }
					else{
							$cnt=$GLOBALS[start]+1;
							while($rowinformation=mysqli_fetch_assoc($resinformation))
							{
								$no_transaction=$rowinformation['no_transaction'];
								$loyalty_card_holder_name=$rowinformation['loyalty_card_holder_name'];
								$loyalty_card_no=trim(preg_replace('/\s+/', '',$rowinformation['loyalty_card_no']));
								$phone_no=$rowinformation['phone_no'];
								$amount=$rowinformation['amount'];
								$point=floor($amount/100);
								$rowvalvalloyalty.="<tr> 
												<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" onClick=\"javascript:showCardWiseTransactiondisplay('".$loyalty_card_no."','".$month."','".$year."','".$rds_name."','".$vertical_value."')\"  
											style=\"color:#930;font-weight:bold;\">".$loyalty_card_holder_name."</a></td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$no_transaction."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$loyalty_card_no."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$phone_no."</td>
												<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
												".number_format($amount,2)."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$point."</td>
												<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
												<a href=\"adminChangeLoyalty.php?month=$month&year=$year&rds_name=$rds_name&vertical=$vertical_value&loyalty_card_no=$loyalty_card_no\"   style=\"color:#930;font-weight:bold;\">EDIT</a></td>
										  </tr>";
							}
						}
		$tablevalloyaltyend='</table><br />';
		echo $tablevalloyalty.$rowvalvalloyalty.$tablevalloyaltyend;
	}
	?>
 <!----------------------------------------End of Table for populate populate employee date wise locate back----------------------------------------------!-->
                <div id="cardwisealldisplay" style="display:none">
                </div><br />
                <div id="cardwiseseldisplay" style="display:none">
                </div><br />
               <div id="loader" style="display:none">
                <br/>
               <center><img src="ajax-loader.gif" /></center>
               </div>
		</td>
	</tr>
</table>
<?php }//End of main()?>