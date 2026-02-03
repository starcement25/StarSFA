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
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND emp_code IN('.$emp_hierarchy.')';
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
function showRdswisesalesDeatails()
{
	//alert(val);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
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
	
	var flag=0;
	var chx = document.getElementsByTagName('input');
 	 for (var i=0; i<chx.length; i++) {
    	if (chx[i].type == 'radio' && chx[i].checked) {
    	flag=1;
    } 
  }
  if(flag==0)
  {
	  alert('Please check at least one radio button.');
	  return false;
  }
  var valrds=document.frmSearch.rds_name.value;
  var valArray=valrds.split('#');
  var val1=valArray[0];
  var val2=valArray[1];
  var val3=document.frmSearch.from_date.value;
  var val4=document.frmSearch.to_date.value;
  var val5=document.frmSearch.radio_type.value;
  var val6=document.frmSearch.brand_code.value;
  var val7=document.frmSearch.sku_code.value;
	var url="selectRdsWiseSales.php?emp_code="+val2+"&rds_code="+val1+"&from_date="+val3+"&to_date="+val4+"&radio_type="+val5+"&brand_code="+val6+"&sku_code="+val7;
	xmlHttp.onreadystatechange=showDetailsRdsWiseSales;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }
function showDetailsRdsWiseSales()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			document.getElementById('productwisesalesdisplay').style.display='none';
			document.getElementById('rdswisesalesdisplay').style.display='';
		 	document.getElementById('rdswisesalesdisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
function showProductwisesalesDeatails(val1,val2,val3,val4,val5,val6)
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
  
	var url="selectProductWiseSales.php?emp_code="+val2+"&rds_code="+val1+"&from_date="+val3+"&to_date="+val4+"&radio_type="+val5+"&prod_code="+val6;
	xmlHttp.onreadystatechange=showDetailsProductWiseSales;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }
function showDetailsProductWiseSales()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			//document.getElementById('rdswisesalesdisplay').style.display='none';
			document.getElementById('productwisesalesdisplay').style.display='';
		 	document.getElementById('productwisesalesdisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
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
function select_sku(brand_code_val)
	{
	  var branch_code=document.frmSearch.branch_name.value;
	 var rds_code=document.frmSearch.rds_name.value;
	  document.frmsku.brand_code_val.value=brand_code_val;
	  document.frmsku.branch_code.value=branch_code;
	  document.frmsku.rds_name.value=rds_code;
	  document.frmsku.submit();
	}
function check(form)
{
	if (document.frmSearch.branch_name.value==0) 
	{
		alert('Please select a branch.');
		document.frmSearch.branch_name.focus();
		return false;
	}
	var flag=0;
	var chx = document.getElementsByTagName('input');
 	 for (var i=0; i<chx.length; i++) {
    	if (chx[i].type == 'radio' && chx[i].checked) {
    	flag=1;
    } 
  }
  if(flag==0)
  {
	  alert('Please check at least one radio button.');
	  return false;
  }
	return true;
}
function display_control(visitdateval)
{
	var visitdate=visitdateval.substring(4, 8)+'-'+visitdateval.substring(2, 4)+'-'+visitdateval.substring(0, 2);
	 var current_date =new Date().toISOString().slice(0,10); 
	 //alert(current_date);
	 
	 if( visitdate > current_date)
	 {
		if(document.getElementById('route_id_display_'+visitdateval).style.display=='block' )
		{
			document.getElementById('route_id_control_'+visitdateval).style.display='block';
			document.getElementById('route_id_display_'+visitdateval).style.display='none';
		}
		else if(document.getElementById('route_id_control_'+visitdateval).style.display=='block' )
		{
			document.getElementById('route_id_control_'+visitdateval).style.display='none';
			document.getElementById('route_id_display_'+visitdateval).style.display='block';
		}
	 }
	 else
	 { 
	   alert(" Visit date must be greater than current date");
	 }
}

</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Route Plan Approval</strong></td>
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
                <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
                <input type="hidden" name="mode" value="route_plan_details">
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                                <td align="right" width="45%" colspan="2">Employee:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $emp_code=$_REQUEST['emp_code'];?>
                                    <select name="emp_code" id="emp_code" onChange="javascript:document.frmSearch.submit();">
                                    <option value="0">SELECT</option>
                                     <?php 
                                     $sqlqueryemployee="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ".$emp_hierarchy_condition_one." 
									 					ORDER BY emp_name ASC";
                                     $resultemployee = mysql_query($sqlqueryemployee);
                                     $count=mysql_num_rows($resultemployee);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowemployee = mysql_fetch_array($resultemployee))
                                        {
                                      ?>
                                         <option value="<?php echo $rowemployee['emp_code'];?>" <?php if($emp_code==$rowemployee['emp_code'] || 
                                         $_REQUEST['emp_code']==$rowemployee['emp_code']){echo 'selected';}?>><?php echo $rowemployee['emp_name'];?></option>
                                       <?php
                                        }
                                      }
                                        ?>	
                                     </select>
                                </td>
                           </tr>
                            <!--tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" onclick="javascript:showRdswisesalesDeatails();">
                                </td>
                            </tr-->
                		</table></td>
                      </tr>
                     </table> 
                     </form>

               		<br />
                <!-----------------------------------------End of Table for first time page loading---------------------------------------------!-->
				 <?php 
				// Displaying the result of search 
				if($_REQUEST['mode']=='route_plan_details'){
					$emp_code=$_REQUEST['emp_code'];
					
					$sqlqueryempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
                    $resultempname = mysql_query($sqlqueryempname);
                    $rowempname=mysql_fetch_array($resultempname);
					$emp_name= $rowempname['emp_name'];
				?>
                <form name ="frmApprove" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
                <input type="hidden" name="mode" value="approve" />
                <input type="hidden" name="emp_code" value="<?php echo $emp_code;?>" />
                <table width="75%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 500px;overflow-y: scroll;display:block;width: 50%;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="4" align="center"><strong>Route plan details of  <?php echo $emp_name;?> for the month 
						<?php echo date('F').','.date('Y');?>  </strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="8%" align="center">Sl no.</td>
                         <td width="8%" align="center"></td>
                        <td width="15%" align="left" style="padding-left:20px;">Visit Date</td>
                        <td width="" align="left" style="padding:0px 20px 0px 20px;">Route</td>
                    </tr> 
                    <?php
						$sqlrouteplan="SELECT RP.route_code,RM.route_name,DATE_FORMAT(RP.visit_date,'%d-%m-%Y') 
										AS visit_date,DATE_FORMAT(RP.visit_date,'%d%m%Y') AS visit_date_codehints,RP.route_plan_trans_id FROM 
									  route_master RM,route_plan RP WHERE RM.route_code=RP.route_code AND RP.emp_code='".$emp_code."' 
									  AND MONTH(RP.visit_date)=MONTH(date_add(curdate(),interval 0 month)) AND YEAR(RP.visit_date)= YEAR(curdate()) 
									  GROUP BY RP.route_code,RP.visit_date ORDER BY RP.visit_date ASC,RM.route_name ASC";
						$rsrouteplan=mysql_query($sqlrouteplan) or die(mysql_error()." Error in route plan: ".$sqlrouteplan);
						$countrouteplan=mysql_num_rows($rsrouteplan);
						if($countrouteplan >0){
						$visit_date_array=array();	
						$visit_date_codehints_array=array();	
						$sl_no_route_plan=0;
						$route_visit_status_prev_array=array();
						while($rowrouteplan=mysql_fetch_array($rsrouteplan))
						{
							$route_code=$rowrouteplan['route_code'];
							$route_name=$rowrouteplan['route_name'];
							$visit_date=$rowrouteplan['visit_date'];
							$visit_date_codehints=$rowrouteplan['visit_date_codehints'];

							$sqllatestroute="SELECT status FROM route_plan WHERE route_code='".$route_code."' 
											AND DATE_FORMAT(visit_date,'%d-%m-%Y')='".$visit_date."' ORDER BY create_date DESC LIMIT 0,1";
							$rslatestroute=mysql_query($sqllatestroute);
							$rowlatestroute=mysql_fetch_array($rslatestroute);
							$status=$rowlatestroute['status'];
							$route_visit_status_prev=$route_code.'-'.$visit_date.'-'.$status;
							/*if(in_array($route_visit_status_prev,$route_visit_status_prev_array))
							{
								continue;
							}
							else
							{*/			
								if($status=='active')
								{
									${route_name.$visit_date_codehints}=${route_name.$visit_date_codehints}.$rowrouteplan['route_name'].',';
									${route_code.$visit_date_codehints}=${route_code.$visit_date_codehints}.$rowrouteplan['route_code'].',';
									${route_plan_trans_id.$visit_date_codehints}=
									${route_plan_trans_id.$visit_date_codehints}.$rowrouteplan['route_plan_trans_id'].',';
									//${route_name.$visit_date_codehints}=substr(${route_name.$visit_date_codehints},0,-1);
									if(!in_array($visit_date_codehints,$visit_date_codehints_array))
									{
										array_push($visit_date_array,$visit_date);
										array_push($visit_date_codehints_array,$visit_date_codehints);
									}
								}
								else
								{
									${inactive_route.$visit_date_codehints}="'".$route_code."'".',';
								}
								//array_push($route_visit_status_prev_array,$route_visit_status_prev);
							//}
						}
						for($i=0;$i <count($visit_date_codehints_array);$i++ )
						{
                            $sl_no_route_plan++;
							if(isset(${inactive_route.$visit_date_codehints_array[$i]}))
							{
								${inactive_route.$visit_date_codehints_array[$i]}=substr(${inactive_route.$visit_date_codehints_array[$i]},0,-1);
							}
							else
							{
								$val=1;
								${inactive_route.$visit_date_codehints_array[$i]}="'".$val."'";
							}
							?>
                              <input type="hidden" name="inactive_route_<?php echo $visit_date_codehints_array[$i];?>" 
              				value="<?php echo ${inactive_route.$visit_date_codehints_array[$i]};?>" />
                            	<tr> 
                                    <td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><?php echo $sl_no_route_plan;?></td>
                                    <!--td> <input type="checkbox" name="display_val[]"  value="RT4732" />CHANGTONGIA
                                    	 <input type="checkbox" name="display_val[]"  value="RT4733" />CHANGTONGIA1
                                    </td-->
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">
                                    <input type="checkbox" name="rp_visit_<?php echo $visit_date_codehints_array[$i];?>" id="rp_visit_<?php echo $visit_date_codehints_array[$i];?>" value="<?php echo $visit_date_codehints_array[$i];?>" onChange="javascript:display_control('<?php echo $visit_date_codehints_array[$i];?>');"/></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><table id="visit_date_display_<?php echo $visit_date_codehints_array[$i];?>" style="display:block;"><tr><td>
									<?php echo $visit_date_array[$i];?></td></tr></table><table id="visit_date_control_<?php echo $visit_date_codehints_array[$i];?>" style="display:none;"><tr><td>
									<input type="text" name="visit_date_<?php echo $visit_date_codehints_array[$i];?>" value="<?php echo $visit_date_array[$i];?>" /> <input type="hidden" name="visit_date_codehints[]" value="<?php echo $visit_date_codehints_array[$i];?>" /></td></tr></table></td>
                                   
									
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">
									<table id="route_id_display_<?php echo $visit_date_codehints_array[$i];?>" style="display:block;"><tr><td><?php echo substr(${route_name.$visit_date_codehints_array[$i]},0,-1);?></td></tr></table><table id="route_id_control_<?php echo $visit_date_codehints_array[$i];?>" 
                                    style="display:none;"><tr><td><?php 
			${route_code.$visit_date_codehints_array[$i]}=substr(${route_code.$visit_date_codehints_array[$i]},0,-1);
			${route_code_array.$visit_date_codehints_array[$i]}=explode(',',${route_code.$visit_date_codehints_array[$i]});
			$emp_hierarchy_choosed=return_employee_hierarchy($emp_code);
			$sqlroute="SELECT DISTINCT RM.route_code,RM.route_name FROM route_master RM,customer_route_emp_relation CRER WHERE CRER.route_code=RM.route_code AND 
						CRER.emp_code IN(".$emp_hierarchy_choosed.") ORDER BY RM.route_name ASC";
			$rsroute=mysql_query($sqlroute);
			$cnt=0;
			while($rowroute=mysql_fetch_array($rsroute))
			{
				$cnt++;
				/*if($visit_date_codehints_array[$i]=='12042016')
				{
					print_r(${route_code_array.$visit_date_codehints_array[$i]});
				}*/
				
					if(in_array($rowroute['route_code'],${route_code_array.$visit_date_codehints_array[$i]})) ${checkval.$visit_date_codehints_array[$i].$rowroute['route_code']}="checked";
					else        ${checkval.$visit_date_codehints_array[$i].$rowroute['route_code']}="";
			?>
                 <input type="checkbox" name="route_code_val_<?php echo $visit_date_codehints_array[$i];?>[]"  
                 value="<?php echo $rowroute['route_code'];?>"  <?php echo ${checkval.$visit_date_codehints_array[$i].$rowroute['route_code']};?>
/><?php echo $rowroute['route_name'];?>
            <?php
			}
			?></td>
              </tr></table></td>
              <?php ${route_plan_trans_id.$visit_date_codehints_array[$i]}=substr(${route_plan_trans_id.$visit_date_codehints_array[$i]},0,-1);?>
              <input type="hidden" name="rp_trans_id_<?php echo $visit_date_codehints_array[$i];?>" 
              value="<?php echo ${route_plan_trans_id.$visit_date_codehints_array[$i]};?>" />
             </tr>
			 <?php	
            }
            ?>
                <tr>
                     <td align="center" width=""  colspan="4">
                        <input type="submit" name="SUBM1" value="Approve" class="inplogin">
                    </td>
                </tr>
            <?php
        }
        else
        {?>
            <tr> 
                <td valign="top" align="center"  colspan="4">No route plan founds for this month</td>
             </tr>
        <?php 
        }
        ?>
            </table></form><br />
            <?php }?>
		</td>
	</tr>
</table>
<?php 
   if($_REQUEST['mode']=='approve')
   {
	  //$count=$_POST['count'];
	  $visit_date_codehints=$_POST['visit_date_codehints'];
	  $emp_code=$_POST['emp_code'];

	 for($i=0;$i<count($visit_date_codehints);$i++)
	 {
		$visit_date=date('Y-m-d',strtotime($_POST['visit_date_'.$visit_date_codehints[$i]]));
		$trans_id=$_POST['rp_trans_id_'.$visit_date_codehints[$i]];
		$trans_id_array=explode(',',$trans_id);
		$trans_id_val=$trans_id_array['0'];
		
		$route_code_variable='route_code_val_'.$visit_date_codehints[$i];
		$inactive_route='inactive_route_'.$visit_date_codehints[$i];
		if(isset($_POST['visit_date_'.$visit_date_codehints[$i]]))
		{
			for($m=0;$m<count($_POST[$route_code_variable]);$m++)
			{
				${active_roue.$visit_date_codehints[$i]}.="'".$_POST[$route_code_variable][$m]."'".',';
			}
			${active_roue.$visit_date_codehints[$i]}=substr(${active_roue.$visit_date_codehints[$i]},0,-1);
			
								
			$sqlupdate="UPDATE route_plan SET status='inactive',create_date=CURRENT_TIMESTAMP() WHERE 
						emp_code='".$emp_code."' AND visit_date='".$visit_date."' and route_code NOT IN(".${active_roue.$visit_date_codehints[$i]}.")";
			mysql_query($sqlupdate);			
		}
		//exit();			
		$sql_delete="DELETE from route_plan WHERE emp_code='".$emp_code."' AND visit_date='".$visit_date."' 
					AND route_code NOT IN(".$_POST[$inactive_route].") and status='active'";
		mysql_query($sql_delete);
		
		for($k=0;$k<count($_POST[$route_code_variable]);$k++)
		{
		  if(isset($_POST[$route_code_variable][$k]))
		  {
			  $sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$trans_id_val."',
								  emp_code 		='".$emp_code."',
								  route_code 		='".$_POST[$route_code_variable][$k]."',
								  visit_date 		='".$visit_date."',
								  create_date		=CURRENT_TIMESTAMP(),
								  update_date		=CURRENT_TIMESTAMP()";
			  if(mysql_query($sqlinsertrouteplan)){
				$sqlinsertrouteplanelog="INSERT INTO route_plan_log SET route_plan_trans_id ='".$trans_id_val."',
										  emp_code 			='".$emp_code."',
										  prev_route_code 	='',
										  current_route_code ='".$_POST[$route_code_variable][$k]."',
										  visit_date 		='".$visit_date."',
										  created_by 		='".$_SESSION['admin_login']."',
										  create_date		=CURRENT_TIMESTAMP()";	
					if(mysql_query($sqlinsertrouteplanelog))
					{
						$success=1;
					}
					else
					{
						$success=0;
					}
				}
				else
				{
					$success=0;
				}
		  }
				//echo $POST['route_code_'.$visit_date_codehints[$i]][$k];
		}
		//exit();
		//print_r($POST['route_code_'.$visit_date_codehints[$i]]);
	 }
	 //exit();
	 if($success==1) {?><script language="JavaScript" type="text/javascript">alert('Route plan approved successfully');window.location.href='route_plan_approval_all.php';</script><?php }else{?><script language="JavaScript" type="text/javascript">alert('Route plan approval failure');window.location.href='route_plan_approval_all.php';</script><?php }
   }
}//End of main()?>