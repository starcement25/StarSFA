<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name']) == 'PRABHUJI' && $_SESSION['admin_login']=="admin" )
	{
		require("adminUtils.php");
	}
	else if(strtoupper($_SESSION['nick_name']) == 'ARCHITA' || strtoupper($_SESSION['nick_name']) == 'GOLDSTONE' )
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
	elseif($mode=="activate")		   		  			activate_customer($_REQUEST['row_id']);
	//elseif($mode =='access')							disphtml("access_add_edit($_REQUEST[row_id]);");
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
		//$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		//$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		if(strtoupper($_SESSION['nick_name']) == 'PRABHUJI')
		{
		$emp_hierarchy_condition=" AND CM.state_code IN(SELECT state_code FROM emp_datewise_state_allocation WHERE emp_code='".$_SESSION['admin_login']."' 
			AND acedns='yes')";
		}
		else
		{
			$emp_hierarchy_condition=" AND CM.state_code IN(SELECT state FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."' AND acedns='yes')";
		}
	}
	
	/*$sql_count = "SELECT COUNT(DISTINCT CM.customer_code) FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM 
			WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code=EM.emp_code AND CRR.acedns='N' AND CM.acedns='N' AND CM.customer_code LIKE 'N%' ".$emp_hierarchy_condition."";			
	$res = mysql_query($sql_count) or die(mysql_error()." Error in count: ".$sql_count); 
	$row = mysql_fetch_row($res);
	$count =  $row[0];

	if($_REQUEST[hold_page] > 0)   	$GLOBALS[start] = $_REQUEST[hold_page];
	if($count == $GLOBALS[start])   $GLOBALS[start] = $GLOBALS[start] - $GLOBALS[show];
	if($GLOBALS[start] < 0)		 $GLOBALS[start] = 0;*/
	$sql="SELECT CM.customer_code,CM.customer_name,EM.emp_name,CM.cust_type,CM.rds_tag,CM.phone_no,CRR.route_code,CM.state_code,CM.image,RM.route_name,(SELECT customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag,(SELECT COUNT(phone_no) FROM customer_master CMD WHERE CMD.phone_no = CM.phone_no) AS phone_no_count
	 		FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM,route_master RM
			WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code=EM.emp_code AND CM.acedns='N' AND CRR.route_code=RM.route_code 
			AND CM.customer_code LIKE 'N%' ".$emp_hierarchy_condition." 
			ORDER BY CM.phone_no ASC";		
	 /*$row=mysql_fetch_array(mysql_query("SELECT COUNT(DISTINCT CM.customer_code) FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM 
			WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code=EM.emp_code AND CM.acedns='N' AND CM.customer_code LIKE 'N%' ".$emp_hierarchy_condition));*/	
	//$count=$row[0];
	//echo $sql;
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
function ChangeStatus(ID,record_no)
{
	document.frm_opts.mode.value='change_status';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}

function activate_customer(ID,record_no)
{
	document.frm_opts.mode.value='activate';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}
function deactivate_customer(ID,record_no)
{
	document.frm_opts.mode.value='deactivate';
	document.frm_opts.row_id.value=ID;
	document.frm_opts.hold_page.value = record_no*1;
	document.frm_opts.submit();
}
function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'New Customer Access', 'height=400,width=600');
	mywindow.document.write('<html><head><title>New Customer Access</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv(){
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var tab_text="<table border='2px' id='export_table'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<a[^>]*>|<\/a>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	//$('#export_table').find('a').contents().unwrap(); //converts hyperlinks to plain text
		
	var a = document.createElement('a');
	
	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Blocked new customer ' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
</script>
<div id="display_main" style="max-height: 400px; max-width:1200px; overflow-y: scroll;" align="center">
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0" id="display_table">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage New Customer Access</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
            
			<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border">
				<tr class="TDHEAD" > 
					<td colspan="11">Blocked Customer Information</td>
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
                    <td width="15%" align="left" style="padding-left:20px;">Customer name</td>
                    <td width="8%" align="left" style="padding-left:20px;">Phone no</td>
					<td width="12%" align="left" style="padding-left:20px;">Employee</td>
                    <td width="10%" align="left" style="padding-left:20px;">Route</td>
					<td width="10%" align="left" style="padding-left:20px;">State</td>
                    <td width="8%" align="left" style="padding-left:20px;">Cust type</td>
                    <td width="12%" align="left" style="padding-left:20px;">Mapped distributor</td>
                    <?php if(strpos(geo_fencing_menu,'add_customer')!=false){?>
                    <td width="8%" align="left" style="padding-left:20px;">Image</td>
                    <?php }?>
					<td align="center" width="10%" >Active</td>
                    <td align="center" width="10%" >Inactive</td>
				</tr>   
				<?
				$cnt=$GLOBALS[start]+1;
				$cutomer_route_state_array=array();
				while($rec=mysql_fetch_array($rs))
				{
					$customer_code=$rec['customer_code'];
					$customer_name=$rec['customer_name'];
					$emp_name=$rec['emp_name'];
					$cust_type=$rec['cust_type'];
					$rds_tag=$rec['rds_tag'];
					$phone_no=$rec['phone_no'];
					$route_code=$rec['route_code'];
					$state_code=$rec['state_code'];
					$image=$rec['image'];
					$customer_route_stateval=$phone_no;
					/*$sqlselphonecount="SELECT phone_no FROM customer_master WHERE phone_no='".$phone_no."' AND customer_code LIKE 'N%'";
					$rsselphonecount=mysql_query($sqlselphonecount);
					$cntselphonecount=mysql_num_rows($rsselphonecount);
					if($cntselphonecount > 1){
						$color="#f44242";
					}
					else
					{
						$color="";
					}*/
					if($rec['phone_no_count'] > 1){
						$color="#f44242";
					}
					else
					{
						$color="";
					}
					//array_push($cutomer_route_state_array,$customer_route_stateval);

					/*$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
					$rsroute=mysql_query($sqlroute);
					$rowroute=mysql_fetch_array($rsroute);*/
					$route_name=$rec['route_name'];
					
					/*$sqldistributor="SELECT customer_name FROM customer_master WHERE customer_code='".$rds_tag."'";
					$rsdistributor=mysql_query($sqldistributor);
					$rowdistributor=mysql_fetch_array($rsdistributor);*/
					$distributor_name=$rec['rds_tag'];
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=stripslashes($customer_name);?></td>
                     <td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=stripslashes($phone_no);?></td>
					<td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=stripslashes($emp_name);?></td>
                    <td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=stripslashes($route_name);?></td>
                    <td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=stripslashes($state_code);?></td>
                    <td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=$cust_type;?></td>
                    <td align="left" valign="top" style="padding-left:20px;background:<?=$color;?>;"><?=$distributor_name;?></td>
                     <?php if(strpos(geo_fencing_menu,'add_customer')!=false){
						$image_string = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a>"; 
					 ?>
                    <td align="left" style="padding-left:20px;"><?=$image_string;?></td>
                    <?php }?>
                <td align="center">
                <?php if($color==''){?>
                <a href="javascript:activate_customer('<?=$customer_code;?>','<?=$GLOBALS[start]?>');" title=" Activate " style="color: #F00;">Active</a>
                <?php } ?>
                </td>
   				<td align="center"><a href="javascript:deactivate_customer('<?=$customer_code;?>','<?=$GLOBALS[start]?>');" title=" Inactivate " style="color: #F00;">Inactive</a></td>
				</tr>
			<? 
				} // end of while loop

			} // end of page count

			?>
            </table>
			<? 
				if($count>0 && $count > $GLOBALS[show])	
				{
			?>
			<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2">
				
                <tr>
                <td align="right"><div style="width:95%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
            <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
        </div></td>
            </tr>
			</table>
			<?
				}
			?>
		</td>
	</tr>
    
</table>
	<br>
	<form name="frm_opts" action="adminNewCustomerUnblocked.php" method="post" >
		<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
		<input type="hidden" name="pageNo" value="<?=$_REQUEST[pageNo]?>">
		<input type="hidden" name="url" value="adminNewCustomerUnblocked.php">
		<input type="hidden" name="row_id" value="">
		<input type="hidden" name="hold_page" value="">
	</form>
</div>
</center>
<?
}//End of main()

function activate_customer($row_id = '')
{
	/*$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
	$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
	$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
	$max_customer_code=$rowmaxcustomercode['max_customer_code'];
	
	if($max_customer_code=='')
	{
		$max_customer_code='C/0000001';
	}
	else
	{
		$max_customer_code++;
	}*/
	$sqlupdatecustomer="UPDATE customer_master SET activated='yes',acedns='Y',activated_customer_code='".$row_id."',activated_datetime=CURRENT_TIMESTAMP(),
							download_time=CURRENT_TIMESTAMP(),activated_by= '".$_SESSION['admin_login']."' 
							WHERE customer_code='".$row_id."'";
	mysql_query($sqlupdatecustomer) or die(mysql_error()." Error in customer activation.");
	$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() 
							WHERE customer_code='".$row_id."'";
	mysql_query($sqlupdatecustomerroute) or die(mysql_error()." Error in customer route emp activation.");
	
	/*$sqlupdateorder="UPDATE order_header SET customer_code='".$max_customer_code."' WHERE customer_code='".$row_id."'";
	mysql_query($sqlupdateorder);
	$sqlupdatestock="UPDATE stock_audit SET customer_code='".$max_customer_code."' WHERE customer_code='".$row_id."'";
	mysql_query($sqlupdatestock);*/

	$GLOBALS['err_msg']="Customer activated Successfully.";
	disphtml("main();");					
}
function deactivate_customer($row_id = '')
{
   $sqlcustomerdetails="SELECT * FROM customer_master WHERE customer_code='".$row_id."'";
   $rscustomerdetails=mysql_query($sqlcustomerdetails);
   $rowcustomerdetails=mysql_fetch_array($rscustomerdetails);
   $emp_code=substr($rowcustomerdetails['customer_code'],1,5);
		$sql  = "insert into customer_master_inactive ";
		$sql .= " SET customer_code='".$rowcustomerdetails['customer_code']."'";
		$sql .= " , dns_customer_code='".$rowcustomerdetails['dns_customer_code']."'";
		$sql .= " , customer_name='".addslashes($rowcustomerdetails['customer_name'])."'";
		$sql .= " , emp_code='".addslashes($emp_code)."'";
		$sql .= " , branch_code='".addslashes($rowcustomerdetails['branch_code'])."'";
		$sql .= " , phone_no='".$rowcustomerdetails['phone_no']."'";
		$sql .= " , route_code='".$rowcustomerdetails['route_code']."'";
		$sql .= " , current_balance	='".$rowcustomerdetails['current_balance']."'";
		$sql .= " , credit_limit='".$rowcustomerdetails['credit_limit']."'";
		$sql .= " , credit_days='".$rowcustomerdetails['credit_days']."'";
		$sql .= " , acedns='".$rowcustomerdetails['acedns']."'";
		$sql .= " , black_list='".$rowcustomerdetails['black_list']."'";
		$sql .= " , TD='".$rowcustomerdetails['TD']."'";
		$sql .= " , rds_tag='".$rowcustomerdetails['rds_tag']."'";
		$sql .= " , cust_type='".$rowcustomerdetails['cust_type']."'";
		$sql .= " , sauda_validity_period='".$rowcustomerdetails['sauda_validity_period']."'";
		$sql .= " , address='".addslashes($rowcustomerdetails['address'])."'";
		$sql .= " , owner_name='".addslashes($rowcustomerdetails['owner_name'])."'";
		$sql .= " , owner_phone='".$rowcustomerdetails['owner_phone']."'";
		$sql .= " , cust_class='".$rowcustomerdetails['cust_class']."'";
		$sql .= " , weekly_closing_day='".$rowcustomerdetails['weekly_closing_day']."'";
		$sql .= " , TIN='".$rowcustomerdetails['TIN']."'";
		$sql .= " , PAN='".$rowcustomerdetails['PAN']."'";
		$sql .= " , district='".$rowcustomerdetails['district']."'";
		$sql .= " , landline_no='".$rowcustomerdetails['landline_no']."'";
		$sql .= " , minimum_stock='".$rowcustomerdetails['minimum_stock']."'";
		$sql .= " , bank_name='".addslashes($rowcustomerdetails['bank_name'])."'";
		$sql .= " , bank_account_number='".$rowcustomerdetails['bank_account_number']."'";
		$sql .= " , email='".$rowcustomerdetails['email']."'";
		$sql .= " , visit_day='".addslashes($rowcustomerdetails['visit_day'])."'";
		$sql .= " , state_code='".addslashes(strtoupper($rowcustomerdetails['state_code']))."'";
		$sql .= " , monthly_potential='".addslashes($rowcustomerdetails['monthly_potential'])."'";
		$sql .= " , coverage_type='".addslashes($rowcustomerdetails['coverage_type'])."'";
		$sql .= " , incoterms='".addslashes($rowcustomerdetails['incoterms'])."'";
		$sql .= " , loadability_ton='".addslashes($rowcustomerdetails['loadability_ton'])."'";
		$sql .= " , transport_mode='".addslashes($rowcustomerdetails['transport_mode'])."'";
		$sql .= " , sauda_type='".addslashes($rowcustomerdetails['sauda_type'])."'";
		$sql .= " , zone='".addslashes($rowcustomerdetails['zone'])."'";
		$sql .= " , visit_sequence='".addslashes($rowcustomerdetails['visit_sequence'])."'";
		$sql .= " , inactivated_by='".$_SESSION['admin_login']."'";
		$sql .= " , inactive_datetime=CURRENT_TIMESTAMP()";
		if(mysql_query($sql)){
		$sqldeletecustomer="DELETE FROM customer_master WHERE customer_code='".$row_id."'";
		mysql_query($sqldeletecustomer) or die(mysql_error()." Error in customer deactivation.");
		$sqldeletecustomerroute="DELETE FROM customer_route_emp_relation WHERE customer_code='".$row_id."'";
		mysql_query($sqldeletecustomerroute) or die(mysql_error()." Error in customer route emp deactivation.");

		$GLOBALS['err_msg']="Customer deactivated Successfully.";	
		disphtml("main();");
	}
}
function clear_allocation()
{
	$emp_code = $_REQUEST['row_id'];
	$upd_sql="UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '" .$emp_code."'";
    mysql_query($upd_sql) or die(mysql_error()." Error in device allocation updation.");

	$GLOBALS['err_msg']="Employee device allocation has been cleared Successfully.";
	disphtml("main();");
}
?>