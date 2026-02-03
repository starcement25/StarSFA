<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	if($mode =='access')								disphtml("access_add_edit($_REQUEST[row_id]);");
	else    											disphtml("main();");
ob_end_flush();

function main()
{
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
		
	if($_REQUEST['search_mode']=='search')
	{
	  $sql_condition.=" WHERE SUBSTRING(IMEI,-LENGTH(".$_REQUEST['IMEI']."))='".$_REQUEST['IMEI']."'";
	}
	else
	{
		$sql_condition="";
	}
	
?>
<script language="JavaScript">
function show_all()
{
	document.frmSearch.search_mode.value = "";	
	document.frmSearch.submit();	
}
</script>	

<script language="javascript">
function access_add_edit(ID,record_no)
{
	document.frm_opts.mode.value='access';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}
</script>
<script language="javascript">
function check()
{
	if (document.frmSearch.IMEI.value.search(/\S/)==-1) 
	{
		alert('Please Input maximum last six digits of IMEI.');
		document.frmSearch.IMEI.focus();
		return false;
	}
	return true;
}
</script>
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> IMEI Status</strong></td>
	</tr>
    <tr>
		<td valign="top" >
			<table width="40%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
				<tr class="TDHEAD" > 
					<td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
				</tr>
				<tr > 
					<td width="15%" colspan="7" align="center">
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  >
                        <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
                        <input type="hidden" name="search_mode" value="search">
                        	<tr>
                                <td align="right" width="25%">IMEI:</td>
                                <td align="left" width="" style="vertical-align:top;">
                               		 <input type="text" value="<?php echo $_REQUEST['IMEI'];?>" name="IMEI" id="IMEI" maxlength="6" size="8"></input>
                                     &nbsp;<br /><b><font color="#FF0000">[Maximum Last six digits of IMEI]</font></b>
                                </td>
                             </tr>
                        	<tr>
                            	<td align="right" width="25%">&nbsp;</td>
                                <td align="left" width="" >
                                <input type="submit" value="Submit" class="inplogin">
                                </td>
                        	</tr>
                        	</form>
                        </table> 
					</td>
				</tr>
			</table> 
		</td>
	</tr>
    <?php if($_REQUEST['search_mode']=='search')
	{
		$sql="SELECT DATE_FORMAT(invoice_date,'%d-%m-%Y') AS invoice_date,DATE_FORMAT(stock_out_date,'%d-%m-%Y') AS stock_out_date,
		(SELECT customer_name FROM customer_master WHERE customer_code=customer_product_billing.customer_code) AS billed_customer,(SELECT customer_name FROM customer_master WHERE customer_code=customer_product_billing.stock_out_customer_code) AS stock_out_customer,IMEI,
		DATE_FORMAT(activation_date,'%d-%m-%Y') AS activation_date,DATE_FORMAT(activation_date,'%H:%i:%s') AS activation_time
			FROM 
		 customer_product_billing ".$sql_condition;
		$rs=mysqli_query($link,$sql) or die(mysqli_error()." Error in main: ".$sql);
		$count=mysqli_num_rows($rs);
	?>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><?php echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
			<table width="80%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="10">IMEI Status</td>
				</tr>
			<?php 
			if($count == 0)
			{ 
			?>
				<tr> 
					<td align="center" colspan="8">No records found</td>
				</tr>
			<?php
			}
			else
			{
			?>
				<tr class="TDHEAD_SUB"> 
					<td width="5%" align="center">Sl</td>
                    <td width="10%" align="left" style="padding-left:20px;">IMEI</td>
					<td width="10%" align="left" style="padding-left:20px;">Bill date</td>
                    <td width="15%" align="left" style="padding-left:20px;">Billed To</td>
                    <td width="15%" align="left" style="padding-left:20px;">Stock Out Date</td>
                    <td width="10%" align="left" style="padding-left:20px;">Stock Out Time</td>
                    <td width="15%" align="left" style="padding-left:20px;">Stock Out Done By</td>
                    <td width="10%" align="left" style="padding-left:20px;">Activation Date</td>
                    <td width="10%" align="left" style="padding-left:20px;">Activation Time</td>
				</tr>   
				<?php
				$cnt=$GLOBALS[start]+1;
				while($rec=mysqli_fetch_assoc($rs))
				{
					$sqlstockoutdetails="SELECT DATE_FORMAT(SUBSTRING(stock_out_id,-14,8),'%d-%m-%Y') AS stock_out_date,
					(SELECT customer_name FROM customer_master WHERE customer_code=stock_out_details.customer_code) AS stock_out_customer,
					 DATE_FORMAT(SUBSTRING(stock_out_id,-14,14),'%H:%i:%s') AS stock_out_time
					FROM stock_out_details WHERE IMEI='".$rec['IMEI']."'";
			   		$rsstockoutdetails=mysqli_query($link,$sqlstockoutdetails);
			   		$rowstockoutdetails=mysqli_fetch_assoc($rsstockoutdetails);		
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=$rec['IMEI'];?></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=$rec['invoice_date'];?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rec['billed_customer']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=$rowstockoutdetails['stock_out_date'];?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=$rowstockoutdetails['stock_out_time'];?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=stripslashes($rowstockoutdetails['stock_out_customer']);?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=$rec['activation_date'];?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><?=$rec['activation_time'];?></td>
				</tr>
			<?php 
				 }  // end of while loop
			  }
			?>
			</table>
		</td>
	</tr>
    </td>
    <?php }?>
</table>
<?php
}//End of main()

function change_mapping()
{
	$customer_code_prefix=substr($_REQUEST['row_id'],0,1);
	if($customer_code_prefix=='C')
	{
		$customer_code_parts=substr($_REQUEST['row_id'],1,(strlen($_REQUEST['row_id'])-1));
		$customer_code=$customer_code_prefix.'/'.$customer_code_parts;
	}
	else
	{
		$customer_code=$_REQUEST['row_id'];
	}
	$new_emp_code=$_REQUEST['emp_code'];
	if(modified_customer_emp_route=='no'){
	$sqlexistingcustomer="SELECT customer_name,branch_code,current_balance,credit_limit,route_code,cust_type,rds_tag 
							FROM customer_master WHERE customer_code = '".$customer_code."'";
	$rsexistingcustomer=mysqli_query($link,$sqlexistingcustomer) or die(mysqli_error()." Error in select existing customer name: ".$sqlexistingcustomer);
	$rowexistingcustomer=mysqli_fetch_assoc($rsexistingcustomer);

	$new_customer_name = $rowexistingcustomer['customer_name'];
	$new_branch_code=$rowexistingcustomer['branch_code'];
	$new_current_balance=$rowexistingcustomer['current_balance'];
	$new_credit_limit=$rowexistingcustomer['credit_limit'];
	$new_cust_type=$rowexistingcustomer['cust_type'];
	$existing_route_code=$rowexistingcustomer['route_code'];
	$existing_rds_tag=$rowexistingcustomer['rds_tag'];
	
	//New employee data fetching
	$sqlroutename="SELECT route_name FROM route_master WHERE route_code = '".$existing_route_code."'";
	$rsroutename=mysqli_query($link,$sqlroutename) or die(mysqli_error()." Error in select route name: ".$sqlroutename);
	$rowroutename=mysqli_fetch_assoc($rsroutename);
	$route_name=$rowroutename['route_name'];
	if($existing_rds_tag!='')
	{
		$sqlrdsname="SELECT rds_name FROM rds_master WHERE rds_code = '".$existing_rds_tag."'";
		$rsrdsname=mysqli_query($link,$sqlrdsname) or die(mysqli_error()." Error in select rds name: ".$sqlrdsname);
		$rowrdsname=mysqli_fetch_assoc($rsrdsname);
		$rds_name=$rowrdsname['rds_name'];
	}
	//End of new employee data fetching
	
	//ADD new route
	$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$new_emp_code."'";
	$rsroutechk=mysqli_query($link,$sqlroutechk);
	$countroutechk=mysqli_num_rows($rsroutechk);
	if($countroutechk<1 && $route_name!='')
	{
		$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
		$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
		$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
		$new_route_code=$rowmaxroutecode['new_route_code'];
		
		if($new_route_code=='')
		{
			$max_route_code='RT/1';
		}
		else
		{
			$max_route_code='RT/'.($new_route_code+1);
			//$max_route_code++;
		}
	
		$sqlroute  = "insert into route_master ";
		$sqlroute .= " SET route_code='".$max_route_code."'";
		$sqlroute .= " ,dns_route_code=''";
		$sqlroute .= " ,route_name='".$route_name."'";
		$sqlroute .= " , emp_code='".$new_emp_code."'";
		$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sqlroute) or die(mysqli_error()." Error in route insertion.");
		$route_code=$max_route_code;
	}
	else
	{
		$rowroutechk=mysqli_fetch_assoc($rsroutechk);
		$route_code=$rowroutechk['route_code'];
	}
	//End of new route addition
	
	//ADD new rds
	if($existing_rds_tag!='')
	{
		$sqlrdsnamechk="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_name)."' AND emp_code='".$new_emp_code."'";
		$rsrdsnamechk=mysqli_query($link,$sqlrdsnamechk);
		$countrdsnamechk=mysqli_num_rows($rsrdsnamechk);
		if($countrdsnamechk<1)
		{
			$sqlmaxrdscode="SELECT MAX(rds_code) AS max_rds_code FROM  rds_master WHERE 1";
			$rsmaxrdscode=mysqli_query($link,$sqlmaxrdscode);
			$rowmaxrdscode=mysqli_fetch_assoc($rsmaxrdscode);
			$max_rds_code=$rowmaxrdscode['max_rds_code'];
			
			if($max_rds_code=='')
			{
				$max_rds_code='C/0000001';
			}
			else
			{
				$max_rds_code++;
			}
	
		
			$sqlrds  = "insert into rds_master ";
			$sqlrds .= " SET rds_code='".$max_rds_code."'";
			$sqlrds .= " ,rds_name='".$rds_name."'";
			$sqlrds .= " , emp_code='".$new_emp_code."'";
			$sqlrds .= " , rds_type='D'";
			$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
		
			mysqli_query($link,$sqlrds) or die(mysqli_error()." Error in rds insertion.");
			$rds_code=$max_rds_code;
		}
		else
		{
			$rowrdsnamechk=mysqli_fetch_assoc($rsrdsnamechk);
			$rds_code=$rowrdsnamechk['rds_code'];
		}
	}
	//End of new rds addition
	
	//New customer addition
	$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($new_customer_name)."' AND emp_code='".$new_emp_code."' 
						AND route_code='".$route_code."'";
	$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
	$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
	if($countcustomernamechk<1)
	{
		$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
		$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
		$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
		$max_customer_code=$rowmaxcustomercode['max_customer_code'];
		$max_customer_code++;
		
		$sql  = "insert into customer_master ";
		$sql .= " SET customer_code='".$max_customer_code."'";
		$sql .= " , dns_customer_code=''";
		$sql .= " , customer_name='".addslashes($new_customer_name)."'";
		$sql .= " , branch_code='".$new_branch_code."'";
		$sql .= " , phone_no=''";
		$sql .= " , route_code='".$route_code."'";
		$sql .= " , emp_code='".$new_emp_code."'";
		$sql .= " , current_balance	='".$new_current_balance."'";
		$sql .= " , credit_limit='".$new_credit_limit."'";
		$sql .= " , acedns='Y'";
		$sql .= " , black_list='N'";
		$sql .= " , TD=''";
		$sql .= " , rds_tag='".$rds_code."'";
		$sql .= " , cust_type='".$new_cust_type."'";
		$sql .= " , download_time=CURRENT_TIMESTAMP()";
		//exit();
		mysqli_query($link,$sql) or die(mysqli_error()." Error in customer addition.");
	}
	//End of new customer addition
	
	$upd_sql="UPDATE customer_master SET acedns ='N',
			 black_list='Y',
			 download_time=CURRENT_TIMESTAMP()
			 WHERE customer_code = '" .$customer_code."'";
	mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in customer updation.");
	
	$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlInsertdatarefresh);
	}
	else
	{
		$sqlselpreviousemp="SELECT emp_code FROM customer_route_emp_relation WHERE customer_code = '".$customer_code."' AND acedns='Y'";
		$rsselpreviousemp=mysqli_query($link,$sqlselpreviousemp);
		$rowselpreviousemp=mysqli_fetch_assoc($rsselpreviousemp);
		$previous_emp_code=$rowselpreviousemp['emp_code'];
		
		$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET emp_code='".$new_emp_code."',download_time=CURRENT_TIMESTAMP() 
								WHERE customer_code = '" .$customer_code."'";
		if(mysqli_query($link,$sqlupdatecustomerroute))
		{
			$sqlupddatetablestructure="UPDATE table_structure_master SET need_update='Y' 
						WHERE table_name  IN('customer_master','route_master','distributor_route_relation')";
		   if(mysqli_query($link,$sqlupddatetablestructure))
			{
				$sqldbupdateone="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$new_emp_code."'";
				mysqli_query($link,$sqldbupdateone);
				$sqldbupdatetwo="UPDATE table_structure_updation SET is_update='1' WHERE emp_code='".$previous_emp_code."'";
				mysqli_query($link,$sqldbupdatetwo);
			}
		}
	}

	$GLOBALS['err_msg']="Customer mapping has been changed successfully.";
	disphtml("main();");
}
?>