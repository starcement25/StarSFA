<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	
	if($_REQUEST['delete_mode']=='delete')
	{
		delete_transaction_orderno();
	}
	else
	{
		disphtml("main();");
	}
ob_end_flush();
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
function select_depot(branch_code_val)
	{
	  document.frmdepot.branch_code.value=branch_code_val;
	  document.frmdepot.submit();
	}
function check(form)
{
	if (document.frmDelete.order_no.value.search(/\S/)==-1) 
	{
		alert('Please enter refference no.');
		document.frmDelete.order_no.focus();
		return false;
	}
	var result = confirm("Want to delete the all transactions of this refference no?");
	if (result==true) {
		return true;
	}
	else
	{
		return false;
	}
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>REFFERENCE NO WISE TRANSACTION DATA DELETION</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name ="frmDelete" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return check(this);">
			<input type="hidden" name="delete_mode" value="delete">
			<br><br>
			
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!----------------------------------Start Table for first time page loading-----------------------------------------------------------------!-->
                <table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>Input Refference No</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                            <td align="right" width="25%" colspan="2">Refference No:</td>
                            <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php $branch_name=$_REQUEST['branch_name'];?>
                            	<input type="text" name="order_no" id="order_no" value="">
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
	function delete_transaction_orderno()
	{
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));

		//mysql_query("SET AUTOCOMMIT=0");
		//mysql_query("START TRANSACTION");
		$grn_no_array=array();
		$receiver_code_array=array();

		$order_no=$_REQUEST['order_no'];
		$order_type=substr($order_no,0,1);
		
		$sqlinsertactivitylog="INSERT INTO activity_log SET 
							transaction_id='".$order_no."',
							branch_code='',
							rds_code='',
							start_date='0000-00-00',
							end_date='0000-00-00',
							request_datetime=CURRENT_TIMESTAMP(),
							updated_flag='0',
							operation_type='deletion ".$_SESSION['admin_login']." ".$_SERVER['REMOTE_ADDR']."'";
	 	if(mysql_query($sqlinsertactivitylog))
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}		

		if($order_type=='O')
		{
			$sqlsaletype="SELECT sale_type,transaction_type FROM order_header WHERE order_no='".$order_no."'";
			$rssaletype=mysql_query($sqlsaletype);
			$cntsaletype=mysql_num_rows($rssaletype);
			if($cntsaletype >0)
			{
				$rowsaletype=mysql_fetch_array($rssaletype);
				$sale_type=$rowsaletype['sale_type'];
				$transaction_type=$rowsaletype['transaction_type'];
			}
			//Start of ST checking 
			if($transaction_type=='ST')
			{
				$sqlselectreceiver="SELECT receiver_code FROM goods_in_transit WHERE grn_no='".$order_no."'";
				$rsselectreceiver=mysql_query($sqlselectreceiver);
				$rowselectreceiver=mysql_fetch_array($rsselectreceiver);
				$receiver_code=$rowselectreceiver['receiver_code'];
				
				/*$sqlselectBT="SELECT DISTINCT order_no FROM goods_in_transit WHERE grn_no='".$order_no."' AND receiver_code='".$receiver_code."' 
							 AND transaction_type='BT'";
				$rsselectBT=mysql_query($sqlselectBT);
				$countselectBT=mysql_num_rows($rsselectBT);
				if($countselectBT >0)
				{
					while($rowselectBT=mysql_fetch_array($rsselectBT))
					{
						$order_no_BT=$rowselectBT['order_no'];
						$sqlgrnordervaluedeleteBT="DELETE FROM goods_in_transit WHERE order_no='".$order_no_BT."'";
						
						$sqlquerydeleteorderBT="DELETE location,order_header,order_details FROM location INNER JOIN 
										order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
										AND order_header.order_no=order_details.order_no WHERE location.trans_id ='".$order_no_BT."'";
						if(mysql_query($sqlquerydeleteorderBT) && mysql_query($sqlgrnordervaluedeleteBT))
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
							if(mysql_query($sqlinsertactivitylogBT))
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
									transaction_id='".$order_no."',
									branch_code='',
									rds_code='',
									receiver_code='".$receiver_code."',
									start_date='0000-00-00',
									end_date='0000-00-00',
									request_datetime=CURRENT_TIMESTAMP(),
									updated_flag='0',
									operation_type='deletion ".$_SESSION['admin_login']." ".$_SERVER['REMOTE_ADDR']."'";
	 			if(mysql_query($sqlinsertactivitylogST))
				{
					$flag=1;
					array_push($grn_no_array,$order_no);
					array_push($receiver_code_array,$receiver_code);
				}
				else
				{
					$flag=0;
				}		
	
			}
			//End of ST checking 
			//For BT checking
			if($transaction_type=='BT')
			{
				$sqlgrndetailsBT="SELECT GIT.grn_no,GIT.prod_code,GIT.receiver_code  FROM goods_in_transit GIT WHERE 
								 GIT.order_no='".$order_no."'";
				$rsgrndetailsBT=mysql_query($sqlgrndetailsBT);
				while($rowgrndetailsBT=mysql_fetch_array($rsgrndetailsBT))
				{
					$grn_no_BT=$rowgrndetailsBT['grn_no'];
					$receiver_code_BT=$rowgrndetailsBT['receiver_code'];
					$prod_code=$rowgrndetailsBT['prod_code'];
					
					$sqlupdategrnST="UPDATE goods_in_transit SET status='0' WHERE grn_no='".$grn_no_BT."' AND 
									receiver_code='".$receiver_code_BT."' AND  prod_code='".$prod_code."' AND transaction_type='ST'";
					if(mysql_query($sqlupdategrnST))
					{
						$flag=1;
					}
					else
					{
						$flag=0;
					}				
					
				}
			}
			//End of BT checking
			$sqlgrndelete="DELETE FROM goods_in_transit WHERE grn_no='".$order_no."' AND transaction_type='ST'";
			if(mysql_query($sqlgrndelete))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
			$sqlgrnordervaluedelete="DELETE FROM goods_in_transit WHERE order_no='".$order_no."'";
			if(mysql_query($sqlgrnordervaluedelete))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
			$sqlquerydeletemis="DELETE FROM mis_transaction_log WHERE trans_id ='".$order_no."'";
			if(mysql_query($sqlquerydeletemis))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
			if($sale_type=='CASH')
			{
				$receipt_id=str_replace('O','P',$order_no);
				
				$sqlquerydeletepayment="DELETE location,payment_header,payment_details FROM location INNER JOIN 
									payment_header INNER JOIN payment_details ON location.trans_id=payment_header.receipt_id 
									AND payment_header.receipt_id=payment_details.receipt_id 
									WHERE location.trans_id='".$receipt_id."'";
				if(mysql_query($sqlquerydeletepayment))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
		  }
			$sqlquerydeleteorder="DELETE location,order_header,order_details FROM location INNER JOIN 
							order_header INNER JOIN order_details ON location.trans_id=order_header.order_no 
							AND order_header.order_no=order_details.order_no WHERE location.trans_id ='".$order_no."'";
			if(mysql_query($sqlquerydeleteorder))
			{
				if(mysql_affected_rows() <1)
				{
					$flag=2;
				}
				else
				{
					$flag=1;
				}
			}
			else
			{
				$flag=0;
			}
		}
		else
		{
			$sqlquerydeleteexpense="DELETE location,freight_expenses FROM location INNER JOIN 
								freight_expenses ON location.trans_id=freight_expenses.freight_exp_trans_id 
								WHERE location.trans_id ='".$order_no."'";
			if(mysql_query($sqlquerydeleteexpense))
			{
				if(mysql_affected_rows() <1)
				{
					$flag=2;
				}
				else
				{
					$flag=1;
				}
			}
			else
			{
				$flag=0;
			}
		}
		//echo $flag;
		if($flag==0)
		{
			//mysql_query("ROLLBACK");
			$GLOBALS['err_msg']="Transaction data of the refference no $order_no deletion unsuccessful";
		}
		else if($flag==1)
		{
			//if(mysql_query("COMMIT"))
			//{
				$sqlupdateactivitylog="UPDATE activity_log SET 
									   updated_flag='1',
									   update_datetime=CURRENT_TIMESTAMP()
									   WHERE transaction_id='".$order_no."'";
				mysql_query($sqlupdateactivitylog);	
				for($cnt=0;$cnt<count($grn_no_array);$cnt++)
				{
					$sqlupdateactivitylogST="UPDATE activity_log SET 
										   updated_flag='1',
										   update_datetime=CURRENT_TIMESTAMP()
										   WHERE transaction_id='".$grn_no_array[$cnt]."' AND receiver_code='".$receiver_code_array[$cnt]."'";
					mysql_query($sqlupdateactivitylogST);
				}
	
				$emailsubj="$_SESSION[nick_name] transaction deletion confirmation on ".$date."-".$month."-".$year." @".$hour."-".$minute."-".$second.' hrs.';
				$emailbody = "<html><head><title>Deletion</title></head>
								<body><br /><table>
								<b>Transaction id: " .$order_no. "</b><br /><br />
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
				$GLOBALS['err_msg']="Transaction data of the refference no $order_no deletion successful";
			/*}
			else
			{
				$GLOBALS['err_msg']="Some technical snack exisis. Please perform the operation again.";
			}*/
		}
		else
		{
			$GLOBALS['err_msg']="No transaction data exists of the refference no $order_no";
		}
		disphtml("main();");
	}//End of main()
?>