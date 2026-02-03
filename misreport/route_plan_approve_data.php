<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);

$sqlqueryempname="SELECT emp_name FROM employee_master WHERE emp_code IN (".$employee.")";
$resultempname = mysqli_query($link,$sqlqueryempname);
$rowempname=mysqli_fetch_assoc($resultempname);
$emp_name= $rowempname['emp_name'];
$emp_code = str_replace("'","",$employee);


				?>
                <form name ="frmApprove" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
                <input type="hidden" name="mode" value="approve" />
                <input type="hidden" name="emp_code" value="<?php echo $emp_code;?>" />
                <table width="100%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
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
						$sqlrouteplan="SELECT RM.route_code,RM.route_name,DATE_FORMAT(RP.visit_date,'%d-%m-%Y') 
										AS visit_date,DATE_FORMAT(RP.visit_date,'%d%m%Y') AS visit_date_codehints,RP.route_plan_trans_id FROM 
									  route_master RM,route_plan RP WHERE RM.route_code=RP.route_code AND RP.emp_code='".$emp_code."' 
									  AND MONTH(RP.visit_date)=MONTH(date_add(curdate(),interval 0 month)) AND YEAR(RP.visit_date)= YEAR(curdate()) 
									  ORDER BY RP.visit_date ASC,RM.route_name ASC";
						$rsrouteplan=mysqli_query($link,$sqlrouteplan) or die(mysqli_error()." Error in route plan: ".$sqlrouteplan);
						$countrouteplan=mysqli_num_rows($rsrouteplan);
						if($countrouteplan >0){
						$visit_date_array=array();	
						$visit_date_codehints_array=array();	
						$sl_no_route_plan=0;
						$route_visit_status_prev_array=array();
						while($rowrouteplan=mysqli_fetch_assoc($rsrouteplan))
						{
							$route_code=$rowrouteplan['route_code'];
							$route_name=$rowrouteplan['route_name'];
							$visit_date=$rowrouteplan['visit_date'];
							$visit_date_codehints=$rowrouteplan['visit_date_codehints'];

							$sqllatestroute="SELECT status FROM route_plan WHERE route_code='".$route_code."' 
											AND DATE_FORMAT(visit_date,'%d-%m-%Y')='".$visit_date."' ORDER BY create_date DESC LIMIT 0,1";
							$rslatestroute=mysqli_query($link,$sqllatestroute);
							$rowlatestroute=mysqli_fetch_assoc($rslatestroute);
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
									<table id="route_id_display_<?php echo $visit_date_codehints_array[$i];?>" style="display:block;"><tr><td><?php echo substr(${route_name.$visit_date_codehints_array[$i]},0,-1);?></td></tr></table><table id="route_id_control_<?php echo $visit_date_codehints_array[$i];?>" style="display:none;"><tr><td><?php 
			${route_code.$visit_date_codehints_array[$i]}=substr(${route_code.$visit_date_codehints_array[$i]},0,-1);
			${route_code_array.$visit_date_codehints_array[$i]}=explode(',',${route_code.$visit_date_codehints_array[$i]});
			$emp_hierarchy_choosed=return_employee_hierarchy($emp_code);
			$sqlroute="SELECT route_code,route_name FROM route_master WHERE emp_code IN(".$emp_hierarchy_choosed.") ORDER BY route_name ASC";
			$rsroute=mysqli_query($link,$sqlroute);
			$cnt=0;
			while($rowroute=mysqli_fetch_assoc($rsroute))
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
			mysqli_query($link,$sqlupdate);			
		}
		//exit();			
		$sql_delete="DELETE from route_plan WHERE emp_code='".$emp_code."' AND visit_date='".$visit_date."' 
					AND route_code NOT IN(".$_POST[$inactive_route].") and status='active'";
		mysqli_query($link,$sql_delete);
		
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
			  if(mysqli_query($link,$sqlinsertrouteplan)){
				$sqlinsertrouteplanelog="INSERT INTO route_plan_log SET route_plan_trans_id ='".$trans_id_val."',
										  emp_code 			='".$emp_code."',
										  prev_route_code 	='',
										  current_route_code ='".$_POST[$route_code_variable][$k]."',
										  visit_date 		='".$visit_date."',
										  created_by 		='".$_SESSION['admin_login']."',
										  create_date		=CURRENT_TIMESTAMP()";	
					if(mysqli_query($link,$sqlinsertrouteplanelog))
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
	 if($success==1) {?><script language="JavaScript" type="text/javascript">alert('Route plan approved successfully');window.location.href='route_plan_approve.php';</script><?php }else{?><script language="JavaScript" type="text/javascript">alert('Route plan approval failure');window.location.href='route_plan_approve.php';</script><?php }
   }
   
mysqli_close($link);
?>