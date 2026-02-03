<?php
ob_start();
session_start();
require("adminUtils.php");

$date = date('d-m-Y');
$employee = $_REQUEST['employee'];

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND LO.emp_code IN('.$employee.')';
}

$emp_hierarchy_condition_one = ' AND LO.emp_code IN('.$employee.') ';



?>
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="1" class="border" 
                style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="13" align="center"><strong>MIS Report</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB" style="text-align:left;"> 
                        <td align="center"></td>
                        <td align="left">Available Field <br /><span>Force</span></td>
                        <td align="left">Present</td>
                        <td align="left">Customer Visited</td>
                        <?php if(order == 'yes'){?>
                        <td align="left"><span>Order</span><br /> Received<br />(qty)</td>
                        <?php } ?>
                        <?php if(collection == 'yes'){?>
                        <td align="left"><span>Collection</span><br /> Received(Rs/-)</td>
                        <?php } ?>
                        <td align="left"><span>No</span><br /> Transaction</td>
                        <?php if(stk_audit=='yes'){?>
                        <td align="left">Stock Audit<br /><span>(Qty)</span></td>
                        <?php }?>
                        <td>KYC</td>
                        <td>Brand Activity</td>
                        <td>Technical Meet</td>
                        <td>Site Visit</td>
                        <td>Market Feedback</td>
                        <td>Total Activity</td>
                    </tr> 
                    <?php
						$sqlfieldforce="SELECT COUNT(EM.emp_code) AS available_field_force FROM employee_master EM,changepassword CH 
										WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND SUBSTRING(EM.emp_code,1,1)!='C' AND EM.emp_code IN(".$employee.")";
						$resfieldforce=mysqli_query($link,$sqlfieldforce) or die(mysqli_error()." Error in select field force: ".$sqlfieldforce);
						$rowfieldforce=mysqli_fetch_assoc($resfieldforce);
						$av_field_force=$rowfieldforce['available_field_force'];
						
						for($i=1;$i<=3;$i++)
						{
							if($i==1)
							{
								$date=date('Y-m-d');
								if($date!='' && sale=='no' && instruction=='yes')
								{
									$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
								}
								else
								{
									$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
								}
								
								$survey_output_date_condition = " AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$date)."' ";
								$market_feedback_condition = " AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$date)."' ";
								$sl_value='Today';
								$val='T';
							}
							//For MTD OR Month Today
							if($i==2)
							{
								$current_month = date('Ym');
								if(sale=='no' && instruction=='yes')
								{
									$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
								}
								else
								{
									$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) AND MONTH(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
								}
								
								$survey_output_date_condition = " AND SUBSTRING(survey_id,-14,6) = '".$current_month."' ";
								$market_feedback_condition = " AND SUBSTRING(market_feedback_id,-14,6) = '".$current_month."' ";
								$sl_value='MTD';
								$val='MTD';
							}
							//For YTD OR Year Today
							if($i==3)
							{
								$end_date = date('Y-m-d');
								
								$date=gmdate('d',strtotime('+330 minute'));
								$month=gmdate('m',strtotime('+330 minute'));
								$year=gmdate('Y',strtotime('+330 minute'));
								
								$hour=gmdate('H',strtotime('+330 minute'));
								$minute=gmdate('i',strtotime('+330 minute'));
								$second=gmdate('s',strtotime('+330 minute'));
								
								if($month>='04'){
									$fiinancial_year=$year.'-04-01';
								}
								else
								{
									$fiinancial_year=($year-1).'-04-01';
								}

								if(sale=='no' && instruction=='yes')
								{
									//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
									$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
												AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
								}
								else
								{
									//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
									$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
												AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
								}
								
								$survey_output_date_condition = "  AND (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$fiinancial_year)."' AND '".str_replace("-","",$end_date)."') ";
								
								$market_feedback_condition = "  AND (SUBSTRING(market_feedback_id,-14,8) BETWEEN '".str_replace("-","",$fiinancial_year)."' AND '".str_replace("-","",$end_date)."') ";
								
								
								$sl_value='YTD';
								$val='YTD';
							}
							
							$sqlpresent="SELECT COUNT(LO.trans_id) AS no_present  FROM location LO WHERE LO.trans_id LIKE 'A%' 
										AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition_one.$date_condition;
							$respresent=mysqli_query($link,$sqlpresent) or die(mysqli_error()." Error in select present information: ".$sqlpresent);
							$rowpresent=mysqli_fetch_assoc($respresent);
							$present=$rowpresent['no_present'];
							//For MTD OR Month Today
							if($i==2)
							{
								$days=return_no_days('','M');
								$present=ceil($present/$days);
							}
							//For YTD OR Year Today
							if($i==3)
							{
								 
								 $now = strtotime(date('Y-m-d')); // or your date as well
								 $start_date = strtotime("2012-06-16");
								 $datediff = $now - $start_date;
								 $nodays=floor($datediff/(60*60*24));
								 $days=return_no_days($nodays,'Y');
								 $present=ceil($present/$days);
							}
							
							/*----------> Total Customer Visit <----------*/
							$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE (SUBSTRING(LO.trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(LO.trans_id,1,2) IN('NO','NC')) AND SUBSTRING(LO.trans_id,1,2) NOT IN('PA')  
						AND SUBSTRING(LO.trans_id,1,2) NOT IN('SU') AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition_one.$date_condition;
							$rescustomervisit=mysqli_query($link,$sqlcustomervisit) or die(mysqli_error()." Error in select customer visit: ".$sqlcustomervisit);
							$rowcustomervisit=mysqli_fetch_assoc($rescustomervisit);
							$no_customer_visit=$rowcustomervisit['no_visit'];
							
							
							/*----------> Total Collection <----------*/
							$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
												FROM payment_details PD,location LO
												WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
												AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition_one.$date_condition;
							$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
							$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
							$collection_received=$rowtotalcollection['total_collection_received'];
							
							
							/*----------> Total Order <----------*/
							$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received
											FROM order_details OD,location LO
											WHERE  LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no AND SUBSTRING(LO.emp_code,1,1)!='C' 
											".$emp_hierarchy_condition_one.$date_condition;
							$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
							$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
							$total_order_qty=$rowtotalorder['total_order_received'];


							/*----------> Total No Transaction <----------*/
							$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
												FROM location LO WHERE (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') 
												AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition_one.$date_condition;
							$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
							$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
							$no_transaction=$rownotransaction['total_no_transaction'];
							
							$employee_arg = str_replace(",","#",$employee);
							$employee_arg = str_replace("'","^",$employee_arg);
							
							
							/*----------> Total Stock Audit <----------*/
							$sqlnostkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
													WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') 
													AND SUBSTRING(LO.emp_code,1,1)!='C' ".$date_condition.$emp_hierarchy_condition_one;
							$rsnostkaudit=mysqli_query($link,$sqlnostkaudit) or die(mysqli_error()." Error in total no stk audit: ".$sqlnostkaudit);
							$rownostkaudit=mysqli_fetch_assoc($rsnostkaudit);
							$no_stk_audit=$rownostkaudit['total_stk_audit'];
							
							
							/*----------> Total KYC <----------*/
							$sql_KYC_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'KYC' AND SUBSTRING(survey_id,3,5) IN (".$employee.") ".$survey_output_date_condition;
							$res_KYC_count = mysqli_query($link,$sql_KYC_count);
							$row_KYC_count = mysqli_fetch_assoc($res_KYC_count);
							$KYC_count = $row_KYC_count['COUNT(DISTINCT survey_id)'];
							
							
							/*----------> Total Branding <----------*/
							$sql_brand_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Branding' AND SUBSTRING(survey_id,3,5) IN (".$employee.") ".$survey_output_date_condition;
							$res_brand_count = mysqli_query($link,$sql_brand_count);
							$row_brand_count = mysqli_fetch_assoc($res_brand_count);
							$brand_count = $row_brand_count['COUNT(DISTINCT survey_id)'];
							
							
							/*----------> Total Technical Meets <----------*/
							$sql_technical_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Technical Meets' AND SUBSTRING(survey_id,3,5) IN (".$employee.") ".$survey_output_date_condition;
							$res_technical_count = mysqli_query($link,$sql_technical_count);
							$row_technical_count = mysqli_fetch_assoc($res_technical_count);
							$technical_count = $row_technical_count['COUNT(DISTINCT survey_id)'];
							
							
							
							/*----------> Total Site Visit <----------*/
							$sql_site_visit = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Site Visit' AND SUBSTRING(survey_id,3,5) IN (".$employee.") ".$survey_output_date_condition;
							$res_site_visit = mysqli_query($link,$sql_site_visit);
							$row_site_visit = mysqli_fetch_assoc($res_site_visit);
							$site_visit_count = $row_site_visit['COUNT(DISTINCT survey_id)'];
							
							
							
							/*----------> Total Market Feedback <----------*/
							$sql_market_feedback = "SELECT COUNT(DISTINCT market_feedback_id) FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) IN (".$employee.") ".$market_feedback_condition;
							$res_market_feedback = mysqli_query($link,$sql_market_feedback);
							$row_market_feedback = mysqli_fetch_assoc($res_market_feedback);
							$market_feedback_count = $row_market_feedback['COUNT(DISTINCT market_feedback_id)'];
									
							
							if($present == 0)
								$show_present = "-";
							else
								$show_present = "<a href=\"#\" onclick=\"attendance('".$val."','".$employee_arg."');\" style=\"color:blue;\">".$present."</a>";
								
							if($no_customer_visit == 0)						
								$show_no_customer_visit = "-";
							else
								$show_no_customer_visit = "<a href=\"#\" onclick=\"customer_visit('".$val."','".$employee_arg."');\" style=\"color:blue;\">".$no_customer_visit."</a>";
								
							if($total_order_qty == 0)
								$show_total_order_qty = "-";
							else
								$show_total_order_qty = "<a href=\"#\" style=\"color:blue;\" onclick=\"order_received('".$val."','".$employee_arg."');\">".round($total_order_qty,3)."</a>";
								
							if($collection_received == 0)
								$show_collection_received = "-";
							else
								$show_collection_received = "<a href=\"#\" style=\"color:blue;\" onclick=\"collection_received('".$val."','".$employee_arg."');\">".number_format($collection_received,2)."</a>";
								
							if($no_stk_audit == 0)
								$show_no_stk_audit = "-";
							else
								$show_no_stk_audit = "<a href=\"#\" style=\"color:blue;\" onclick=\"stock_audit('".$val."','".$employee_arg."');\">".round($no_stk_audit,3)."</a>";
								
							if($no_transaction == 0)
								$show_no_transaction = "-";
							else
								$show_no_transaction = "<a href=\"#\" style=\"color:blue;\" onclick=\"no_transaction('".$val."','".$employee_arg."');\">".$no_transaction."</a>";
								
							if($KYC_count == 0)
								$show_KYC = "-";
							else
								$show_KYC = "<a href=\"#\" style=\"color:blue;\" onclick=\"show_KYC('".$val."','".$employee_arg."');\">".$KYC_count."</a>";
								
							if($brand_count == 0)
								$show_brand = "-";
							else
								$show_brand = "<a href=\"#\" style=\"color:blue;\" onclick=\"show_brand('".$val."','".$employee_arg."');\">".$brand_count."</a>";
								
							if($technical_count == 0)
								$show_technical = "-";
							else
								$show_technical = "<a href=\"#\" style=\"color:blue;\" onclick=\"show_technical('".$val."','".$employee_arg."');\">".$technical_count."</a>";
								
							if($site_visit_count == 0)
								$show_site_visit = "-";
							else
								$show_site_visit = "<a href=\"#\" style=\"color:blue;\" onclick=\"show_site_visit('".$val."','".$employee_arg."');\">".$site_visit_count."</a>";
								
							if($market_feedback_count == 0)
								$show_market_feedback = "-";
							else
								$show_market_feedback = "<a href=\"#\" style=\"color:blue;\" onclick=\"show_market_feedback('".$val."','".$employee_arg."');\">".$market_feedback_count."</a>";	
							
							
							$total_activity = $no_customer_visit+$KYC_count+$brand_count+$technical_count+$site_visit_count+$market_feedback_count;
?>
							<tr> 
                                    <td valign="top" align="center" style="BORDER: #A92A61 1px solid; font-weight:bold;"><?php echo $sl_value?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $av_field_force; ?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_present; ?></td>
									<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_no_customer_visit ;?></td>
                                    <?php if(order == 'yes'){?>
                                     <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_total_order_qty; ?></td>
                                     <?php }
                                      if(collection == 'yes'){?>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_collection_received; ?></td>
                                    <?php }?>
                              		<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_no_transaction;?></td>
                                    <?php if(stk_audit=='yes'){?>
                                   <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_no_stk_audit; ?></td>
                                    <?php }?>
                                    <td align="right"  valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_KYC; ?></td>
                                    <td align="right"  valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_brand; ?></td>
                                    <td align="right"  valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_technical; ?></td>
                                    <td align="right"  valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_site_visit; ?></td>
                                    <td align="right"  valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $show_market_feedback; ?></td>
                                    <td align="right"  valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $total_activity; ?></td>
                              </tr>
                             <?php				
							}
							mysqli_close($link);
						?>
            </table>