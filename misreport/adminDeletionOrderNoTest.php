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
	echo $_SESSION['nick_name'];
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
		//Logic to delete the item
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
        	<form name ="frmDelete" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
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
		mysqli_query($link,"SET AUTOCOMMIT=0");
		mysqli_query($link,"START TRANSACTION");

		$order_no=$_REQUEST['order_no'];
		echo $sqlquerydeleteorder="DELETE location,order_header,order_details,goods_in_transit FROM location INNER JOIN 
						order_header INNER JOIN order_details INNER JOIN goods_in_transit ON location.trans_id=order_header.order_no 
						AND order_header.order_no=order_details.order_no OR 
						(order_header.order_no=goods_in_transit.grn_no OR order_header.order_no=goods_in_transit.order_no)
						WHERE location.trans_id ='".$order_no."'";
		if(mysqli_query($link,$sqlquerydeleteorder))
		{
			if(mysqli_affected_rows() <1)
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
				
		$sqlsaletype="SELECT sale_type FROM order_header WHERE order_no='".$order_no."'";
		$rssaletype=mysqli_query($link,$sqlsaletype);
		echo $cntsaletype=mysqli_num_rows($rssaletype);
		if($cntsaletype >0)
		{
			$rowsaletype=mysqli_fetch_assoc($rssaletype);
			$sale_type=$rowsaletype['sale_type'];
			if($sale_type=='CASH'){
				$receipt_id=str_replace('O','P',$order_no);
				
				echo$sqlquerydeletepayment="DELETE location,payment_header,payment_details FROM location INNER JOIN 
									payment_header INNER JOIN payment_details ON location.trans_id=payment_header.receipt_id 
									AND payment_header.receipt_id=payment_details.receipt_id 
									WHERE location.trans_id LIKE '%".$receipt_id."%'";
				if(mysqli_query($link,$sqlquerydeletepayment))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
			}
		}
		if($flag==0)
		{
			mysqli_query($link,"ROLLBACK");
			$GLOBALS['err_msg']="Transaction data of the refference no $order_no deletion unsuccessful";
		}
		else if($flag==1)
		{
			mysqli_query($link,"COMMIT");
			$GLOBALS['err_msg']="Transaction data of the refference no $order_no deletion successful";
		}
		else
		{
			$GLOBALS['err_msg']="No transaction data exists of the refference no $order_no";
		}
		disphtml("main();");
	}//End of main()
?>