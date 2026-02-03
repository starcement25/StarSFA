<?php
set_time_limit(1000);
ini_set('memory_limit', '-1');
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	
	if($_REQUEST['delete_mode']=='delete')
	{
		delete_transaction();
	}
	else
	{
		disphtml("main();");
	}
ob_end_flush();
function main()
{
	//echo $_SESSION['nick_name'];
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
function select_depot(branch_code_val)
	{
	  document.frmdepot.branch_code.value=branch_code_val;
	  document.frmdepot.submit();
	}
function check(form)
{
	if (document.frmDelete.branch_name.value==0) 
	{
		alert('Please select a branch.');
		document.frmDelete.branch_name.focus();
		return false;
	}
	if (document.frmDelete.rds_name.value==0) 
	{
		alert('Please select a depot.');
		document.frmDelete.rds_name.focus();
		return false;
	}
	if (document.frmDelete.from_date.value==0) 
	{
		alert('Please select a date for from date.');
		document.frmDelete.from_date.focus();
		return false;
	}
	if (document.frmDelete.to_date.value==0) 
	{
		alert('Please select a date for to date.');
		document.frmDelete.to_date.focus();
		return false;
	}
	return true;
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>DEPOT WISE TRANSACTION DATA DELETION</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name ="frmdepot" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="branch_code" value="">
            </form>
        	<form name ="frmDelete" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return check(this);">
			<input type="hidden" name="delete_mode" value="delete">
			<br><br>
			
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>DEPOT SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                            <td align="right" width="15%" colspan="2">Branch:</td>
                            <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php $branch_name=$_REQUEST['branch_name'];?>
                            	<select name="branch_name" id="branch_name" onChange="javascript:select_depot(this.value);">
                                <option value="0">SELECT</option>
                                 <?php 
								 $sqlquerybranch="SELECT branch_code,branch_name FROM branch_master WHERE 1 ORDER BY branch_name ASC";
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
                                <td align="right" width="15%" colspan="2">Depot:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php 
										$rds_name=$_REQUEST['rds_name'];
										$branch_code=$_REQUEST['branch_code'];
								?>	 
                                	<select name="rds_name" id="rds_name">
                                    <option value="0">SELECT</option>
                                     <?php 
									 $sqlqueryrds="SELECT RM.rds_code,RM.rds_name,RM.emp_code FROM rds_master RM,employee_master EM WHERE 
									 			RM.emp_code=EM.emp_code AND EM.branch_code='".$branch_code."'";
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
                                     <input type="hidden" name="rds_name_delete" id="rds_name_delete" value="<?php echo $rowrds['rds_name']?>">
                                </td>
                           </tr>
                            <tr>
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
                                        var cal5 = new calendar3(document.forms['frmDelete'].elements['from_date']);
                                        cal5.year_scroll = true;
                                        cal5.time_comp = false;
                                        //-->
                                    </script>
									 <?php 
                                     /*$month=$_REQUEST['month'];
                                     echo PopulateSelectMonthYear('month', "SELECT DATE_FORMAT(date,'%M') AS month,DATE_FORMAT(date,'%m') AS month_value FROM loyalty_location_transaction WHERE loyalty_location_transaction.emp_code!='C0007' GROUP BY DATE_FORMAT(date,'%m-%Y') ORDER BY DATE_FORMAT(date,'%m-%Y') ASC", 'month_value', 'month', $month,'','inplogin');*/?>					
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                                <td width="" style="vertical-align:top;">
                                    <?php 
                                    /*$year=$_REQUEST['year'];
                                    echo PopulateSelectMonthYear('year', "SELECT DATE_FORMAT(date,'%Y') AS year FROM loyalty_location_transaction WHERE loyalty_location_transaction.emp_code!='C0007' GROUP BY DATE_FORMAT(date,'%Y') ORDER BY DATE_FORMAT(date,'%Y') ASC", 'year', 'year', $year,'','inplogin');*/
									?>
                                    <?php $to_date=$_REQUEST['to_date'];?>
                                     <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="to_date"></input>&nbsp;
                                        <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
                                    </label>
                                    <script language="JavaScript" type="text/javascript">
                                        <!-- // create calendar object(s) just after form tag closed
                                         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                         // note: you can have as many calendar objects as you need for your application
                                        var cal6 = new calendar3(document.forms['frmDelete'].elements['to_date']);
                                        cal6.year_scroll = true;
                                        cal6.time_comp = false;
                                        //-->
                                    </script>
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
                     </table> 
                      </form>
               		</td>
              	</tr> 
            </table><br />
 <?php
}
 	//For deleting transaction details
	function delete_transaction()
	{
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		mysqli_query($link,"SET AUTOCOMMIT=1");
		//mysqli_query($link,"START TRANSACTION");
		$grn_no_array=array();
		$receiver_code_array=array();

		$rds_name_query=$_REQUEST['rds_name'];
		$sqlemp="SELECT emp_code,rds_name FROM rds_master WHERE rds_code='".$rds_name_query."'";
		$rsemp=mysqli_query($link,$sqlemp);
		$rowemp=mysqli_fetch_assoc($rsemp);
		$emp_code=$rowemp['emp_code'];
		$rds_name=$rowemp['rds_name'];

		$rds_name_delete=$rds_name;
		$branch_name=$_REQUEST['branch_name'];
		$from_date=$_REQUEST['from_date'];
		$from_date=date('Y-m-d',strtotime($from_date));
		$to_date=$_REQUEST['to_date'];
		$to_date=date('Y-m-d',strtotime($to_date));
		$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_name."'";
		$rsbranch=mysqli_query($link,$sqlbranch);
		$rowbranch=mysqli_fetch_assoc($rsbranch);
		$branch_name_mail=$rowbranch['branch_name'];

		$sqlinsertactivitylog="INSERT INTO activity_log SET 
							transaction_id='',
							branch_code='".$branch_name."',
							rds_code='".$rds_name_query."',
							start_date='".$from_date."',
							end_date='".$to_date."',
							request_datetime=CURRENT_TIMESTAMP(),
							updated_flag='0',
							operation_type='deletion ".$_SESSION['admin_login']." ".$_SERVER['REMOTE_ADDR']."'";
	 	if(mysqli_query($link,$sqlinsertactivitylog))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}	

		if($from_date!='' && $to_date!='')
		{
			$date_condition=" AND DATE_FORMAT(location.date,'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(location.date,'%Y-%m-%d') <='".$to_date."'";
			$date_condition_ST=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(LO.date,'%Y-%m-%d') <='".$to_date."'";
			$date_condition_mis=" AND DATE_FORMAT(trans_date,'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(trans_date,'%Y-%m-%d') <='".$to_date."'";
		}
		else
		{
			$date_condition='';
		}
		//FOR ST transaction type
		$sqlgrndetails="SELECT DISTINCT GIT.grn_no,GIT.receiver_code  FROM location LO,
						order_header OH,goods_in_transit GIT WHERE 
						LO.trans_id=OH.order_no AND OH.order_no=GIT.grn_no AND OH.transaction_type='ST'
					   AND LO.emp_code='".$emp_code."' ".$date_condition_ST."";
		$rsgrndetails=mysqli_query($link,$sqlgrndetails);
		while($rowgrndetails=mysqli_fetch_assoc($rsgrndetails))
		{
			$receiver_code=$rowgrndetails['receiver_code'];
			$grn_no=$rowgrndetails['grn_no'];
			/*$sqlselectBT="SELECT DISTINCT order_no FROM goods_in_transit WHERE grn_no='".$grn_no."' AND receiver_code='".$receiver_code."' 
						 AND transaction_type='BT'";
			$rsselectBT=mysqli_query($link,$sqlselectBT);
			$countselectBT=mysqli_num_rows($rsselectBT);
			if($countselectBT >0)
			{
				while($rowselectBT=mysqli_fetch_assoc($rsselectBT))
				{
					$order_no_BT=$rowselectBT['order_no'];
					$sqlgrnordervaluedeleteBT="DELETE FROM goods_in_transit WHERE order_no='".$order_no_BT."'";
					
					$sqlquerydeleteorderBT="DELETE location,order_header,order_details FROM location INNER JOIN 
									order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
									AND order_header.order_no=order_details.order_no WHERE location.trans_id ='".$order_no_BT."'";
					if(mysqli_query($link,$sqlquerydeleteorderBT) && mysqli_query($link,$sqlgrnordervaluedeleteBT))
					{
						$flag=1;
						$sqlinsertactivitylogBT="INSERT INTO activity_log SET 
												transaction_id='".$order_no_BT."',
												branch_code='',
												rds_code='',
												receiver_code='".$receiver_code."',
												start_date='0000-00-00',
												end_date='0000-00-00',
												request_datetime=CURRENT_TIMESTAMP(),
												updated_flag='0',
												operation_type='deletion ".$_SESSION['admin_login']." ".$_SERVER['REMOTE_ADDR']."'";
						if(mysqli_query($link,$sqlinsertactivitylogBT))
						{
							$flag=1;
							array_push($grn_no_array,$order_no_BT);
							array_push($receiver_code_array,$receiver_code);
						}
						else
						{
							$flag=0;
						}
					}
					else
					{
						$flag=0;
					}
				}
			}*/
		    $sqlinsertactivitylogST="INSERT INTO activity_log SET 
									transaction_id='".$grn_no."',
									branch_code='',
									rds_code='',
									receiver_code='".$receiver_code."',
									start_date='0000-00-00',
									end_date='0000-00-00',
									request_datetime=CURRENT_TIMESTAMP(),
									updated_flag='0',
									operation_type='deletion ".$_SESSION['admin_login']." ".$_SERVER['REMOTE_ADDR']."'";
			if(mysqli_query($link,$sqlinsertactivitylogST))
			{
				$flag=1;
				array_push($grn_no_array,$grn_no);
				array_push($receiver_code_array,$receiver_code);
			}
			else
			{
				$flag=0;
			}
		}
		//End for ST transaction type
		
		//For BT transaction type
		$sqlgrndetailsBT="SELECT GIT.grn_no,GIT.prod_code,GIT.receiver_code  FROM location LO,
						 order_header OH,goods_in_transit GIT WHERE 
						 LO.trans_id=OH.order_no AND OH.order_no=GIT.order_no AND OH.transaction_type='BT'
					     AND LO.emp_code='".$emp_code."' ".$date_condition_ST."";
		$rsgrndetailsBT=mysqli_query($link,$sqlgrndetailsBT);
		while($rowgrndetailsBT=mysqli_fetch_assoc($rsgrndetailsBT))
		{
			$grn_no_BT=$rowgrndetailsBT['grn_no'];
			$receiver_code_BT=$rowgrndetailsBT['receiver_code'];
			$prod_code=$rowgrndetailsBT['prod_code'];
			
			$sqlupdategrnST="UPDATE goods_in_transit SET status='0' WHERE grn_no='".$grn_no_BT."' AND 
							receiver_code='".$receiver_code_BT."' AND  prod_code='".$prod_code."' AND transaction_type='ST'";
			if(mysqli_query($link,$sqlupdategrnST))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}				
			
		}
		//End for BT transaction type
		
		$sqlgrndelete="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
						order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
					  AND order_header.order_no=order_details.order_no AND location.trans_id=goods_in_transit.grn_no  
					  WHERE location.emp_code='".$emp_code."' AND goods_in_transit.transaction_type='ST' ".$date_condition."";
		if(mysqli_query($link,$sqlgrndelete))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		//exit();
		$sqlgrnreceiverdelete="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
							order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
					  		AND order_header.order_no=order_details.order_no AND location.trans_id=goods_in_transit.order_no  
					  		WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		if(mysqli_query($link,$sqlgrnreceiverdelete))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		
		$sqlquerydeleteorder="DELETE location,order_header,order_details FROM location INNER JOIN 
							 order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
							 AND order_header.order_no=order_details.order_no WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		$sqlquerydeletepayment="DELETE location,payment_header,payment_details FROM location INNER JOIN 
								payment_header INNER JOIN payment_details ON location.trans_id=payment_header.receipt_id 
								AND payment_header.receipt_id=payment_details.receipt_id 
								WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		$sqlquerydeleteexpense="DELETE location,freight_expenses FROM location INNER JOIN 
								freight_expenses ON location.trans_id=freight_expenses.freight_exp_trans_id 
								WHERE location.emp_code='".$emp_code."' ".$date_condition."";
		$sqlquerydeletemis="DELETE FROM mis_transaction_log WHERE emp_code='".$emp_code."' ".$date_condition_mis."";
						
		if(mysqli_query($link,$sqlquerydeleteorder) && mysqli_query($link,$sqlquerydeletepayment) && mysqli_query($link,$sqlquerydeleteexpense) && mysqli_query($link,$sqlquerydeletemis))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		if($flag==1)
		{
			//mysqli_query($link,"COMMIT");
			$sqlupdateactivitylog="UPDATE activity_log SET 
								   updated_flag='1',
								   update_datetime=CURRENT_TIMESTAMP()
								   WHERE branch_code='".$branch_name."' AND rds_code='".$rds_name_query."' AND start_date='".$from_date."' AND end_date='".$to_date."'";
			mysqli_query($link,$sqlupdateactivitylog);
			
			for($cnt=0;$cnt<count($grn_no_array);$cnt++)
			{
				$sqlupdateactivitylogST="UPDATE activity_log SET 
									   updated_flag='1',
									   update_datetime=CURRENT_TIMESTAMP()
									   WHERE transaction_id='".$grn_no_array[$cnt]."' AND receiver_code='".$receiver_code_array[$cnt]."'";
				mysqli_query($link,$sqlupdateactivitylogST);
			}
			$GLOBALS['err_msg']="Transaction data of the $rds_name deletion successful";
			$emailsubj="$_SESSION[nick_name] transaction deletion confirmation on ".$date."-".$month."-".$year." @".$hour."-".$minute."-".$second.' hrs.';
			$emailbody = "<html><head><title>Deletion</title></head>
										<body><br /><table>
										<b>Branch: " .$branch_name_mail. "</b><br /><br />
										<b>Depot Name: " .$rds_name_delete. "</b><br /><br />
										<b>From date: " .$from_date. "</b><br /><br />
										<b>To date: " .$to_date. "</b><br /><br />
										<b>Deletion By: " .$_SESSION['admin_login']. "</b><br /><br />
										</table><br /><br />Powered By aceDNS</body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n".
						'X-Mailer: PHP/' . phpversion();
			$email_to='';				
			mail($email_to, $emailsubj, $emailbody, $headers,'-facedns@acedns.in');
		}
		else
		{
			//mysqli_query($link,"ROLLBACK");
			$GLOBALS['err_msg']="Transaction data of the $rds_name deletion unsuccessful";
		}
		disphtml("main();");
	}//End of main()
?>