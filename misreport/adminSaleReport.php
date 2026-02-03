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
		$emp_hierarchy_condition_one='';
		$emp_upper_hierarchy='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
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
/*function showRdswisesalesDeatails(val1,val2,val3,val4,val5)
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
	var url="selectRdsWiseSales.php?emp_code="+val2+"&rds_code="+val1+"&from_date="+val3+"&to_date="+val4+"&radio_type="+val5;
	xmlHttp.onreadystatechange=showDetailsRdsWiseSales;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }*/
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
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>SALES REPORT</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name ="frmdepot" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="branch_code" value="">
            </form>
            <form name ="frmsku" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="brand_code_val" value="">
                <input type="hidden" name="branch_code" value="">
                <input type="hidden" name="rds_name" value="">
            </form>
            </form>
            </form>
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="sales_search">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
			<br><br>
			</form>
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
					<!--input type="hidden" name="search_mode" value="sales_search"-->
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                                <td align="right" width="45%" colspan="2">Branch:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $branch_name=$_REQUEST['branch_name'];?>
                                    <select name="branch_name" id="branch_name" onChange="javascript:select_depot(this.value);">
                                    <option value="0">SELECT</option>
                                     <?php 
                                     $sqlquerybranch="SELECT DISTINCT BM.branch_code,BM.branch_name FROM branch_master BM,employee_master EM 
									 					WHERE BM.branch_code=EM.branch_code ".$emp_hierarchy_condition_one." ORDER BY BM.branch_name ASC";
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
                                <td align="right" width="45%" colspan="2">Depot:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php 
										$rds_name=$_REQUEST['rds_name'];
										$branch_code=$_REQUEST['branch_code'];
								?>	 
                                	<select name="rds_name" id="rds_name">
                                    <option value="0">SELECT</option>
                                     <?php 
									 $sqlqueryrds="SELECT RM.rds_code,RM.rds_name,RM.emp_code FROM rds_master RM,employee_master EM WHERE 
									 			RM.emp_code=EM.emp_code AND EM.branch_code='".$branch_code."' ".$emp_hierarchy_condition_one."";
									 $resultrds = mysqli_query($link,$sqlqueryrds);
									 $countrds=mysqli_num_rows($resultrds);
									 if($countrds>0){
									while($rowrds = mysqli_fetch_assoc($resultrds))
									{
										//echo $emp_code=$rowrds['emp_code'];
                                     ?>
                                         <option value=<?php echo $rowrds['rds_code'].'#'.$rowrds['emp_code']; ?> <?php if( $rds_name==$rowrds['rds_code'].'#'.$rowrds['emp_code']){echo 'selected';}?>>
                                         <?php echo $rowrds['rds_name']; ?>
                                         </option>
                                       <?php }?>
                                   <?php }?>   
                                     </select>
                                </td>
                       </tr>
                        <tr>
                            <td align="right" width="45%" colspan="2">Brand:</td>
                            <td align="left" width="" style="vertical-align:top;" colspan="2">
                             <?php  $brand_code=$_REQUEST['brand_code'];?>
                              	 <select name="brand_code" id="brand_code" onChange="javascript:select_sku(this.value);">
                                    <option value="all">ALL</option>
                                     <?php 
                                     $sqlproductgroup="SELECT product_group_code,product_group_name FROM product_group_master 
									 						ORDER BY product_group_name ASC";
                                     $resultproductgroup = mysqli_query($link,$sqlproductgroup);
                                     $countgroup=mysqli_num_rows($resultproductgroup);
                                        if($countgroup>0){
                                        while($rowproductgroup = mysqli_fetch_assoc($resultproductgroup))
                                        {
                                      ?>
                                         <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($brand_code==$rowproductgroup['product_group_code'] || $_REQUEST['brand_code_val']==$rowproductgroup['product_group_code']){echo  'selected';}?>><?php echo $rowproductgroup['product_group_name'];?></option>
                                       <?php
                                        }
                                      }
                                        ?>	
                                     </select>
                                </td>
                           </tr>
                           <tr>
                                <td align="right" width="45%" colspan="2">SKU:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php $sku_code=$_REQUEST['sku_code'];
									$brand_code=$_REQUEST['brand_code_val'];
								?>	 
                                	<select name="sku_code" id="sku_code">
                                    <option value="all">ALL</option>
                                     <?php 
									 if($brand_code!='all'){
									 $sqlqueryproduct="SELECT prod_code,prod_desc FROM product_master WHERE product_group_code='".$brand_code."'";
									 $resultproduct = mysqli_query($link,$sqlqueryproduct);
									 $countproduct=mysqli_num_rows($resultproduct);
									 if($countproduct>0){
									while($rowproduct = mysqli_fetch_assoc($resultproduct))
									{
										//echo $emp_code=$rowrds['emp_code'];
                                     ?>
                                         <option value=<?php echo $rowproduct['prod_code']; ?> <?php if( $sku_code==$rowproduct['prod_code']){echo 'selected';}?>>
                                         <?php echo $rowproduct['prod_desc']; ?>
                                         </option>
                                       <?php }
                                   		 }
									 }
								   ?>   
                                     </select>
                                </td>
                           </tr>
                           <tr> 
                            <td align="center" width="100%" colspan="4">
                                <input type="radio" value="today" name="radio_type" onClick="javascript:search_att('today');"
                                <?php if($_REQUEST['radio_type']=='today'){?>checked<?php }?>/>Today
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" value="monthly" name="radio_type" onClick="javascript:search_att('monthly');"  
								<?php if($_REQUEST['radio_type']=="monthly"){?>checked<?php }?>/>MTD
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" value="yourchoice" name="radio_type" onClick="javascript:search_att('yourchoice');" 
								<?php if($_REQUEST['radio_type']=="yourchoice"){?>checked<?php }?>/>Custom
                            </td>
                        </tr>
                            <tr id="datedropdown" style="display:<?php if($_REQUEST['radio_type']=="yourchoice"){?>''<?php }else{?>none<?php }?>">
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
                                        var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                                        cal5.year_scroll = true;
                                        cal5.time_comp = false;
                                        //-->
                                    </script>
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                                <td width="" style="vertical-align:top;">
                                    <?php $to_date=$_REQUEST['to_date'];?>
                                     <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="to_date"></input>&nbsp;
                                        <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
                                    </label>
                                    <script language="JavaScript" type="text/javascript">
                                        <!-- // create calendar object(s) just after form tag closed
                                         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                         // note: you can have as many calendar objects as you need for your application
                                        var cal6 = new calendar3(document.forms['frmSearch'].elements['to_date']);
                                        cal6.year_scroll = true;
                                        cal6.time_comp = false;
                                        //-->
                                    </script>
                                </td>
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Submit" class="inplogin" onclick="javascript:showRdswisesalesDeatails();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
                <!-----------------------------------------End of Table for first time page loading---------------------------------------------!-->
				 <?php 
				// Displaying the result of search 
				if($_REQUEST['search_mode']=='sales_search'){
					$radio_type=$_REQUEST['radio_type'];
					$branch_name=$_REQUEST['branch_name'];
					$from_date=$_REQUEST['from_date'];
					$from_date=date('Y-m-d',strtotime($from_date));
					$to_date=$_REQUEST['to_date'];
					$to_date=date('Y-m-d',strtotime($to_date));
					//For  Today
					if($radio_type=='today')
					{
						$date=date('Y-m-d');
						if($date!='')
						{
							$date_condition =" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') ='".$date."'";
							$date_condition_location=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') ='".$date."'";
						}
						$heading_val='Of '.date('d-m-Y');
					}
					//For MTD OR Month Today
					if($radio_type=='monthly')
					{
						$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
										AND MONTH(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
						$date_condition_location=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE())";
						$heading_val='Of '.date('M,Y');				
					}
					//For Custom
					if($radio_type=='yourchoice')
					{
					  $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$to_date."'";
					  $date_condition_location=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') >='".$from_date."' AND 
					  							DATE_FORMAT(LO.date,'%Y-%m-%d') <='".$to_date."'";
						$heading_val='From '.date('d-m-Y',strtotime($from_date)).' To '.date('d-m-Y',strtotime($to_date));							
					}
					$sqlquerybranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_name."'";
                      $resultbranchname = mysqli_query($link,$sqlquerybranchname);
                     $rowbranchname=mysqli_fetch_assoc($resultbranchname);
					 $branch_name_rds= $rowbranchname['branch_name'];
				?>
                <table width="75%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
                style="display: '';height: 300px;overflow-y: scroll;display:block;width: 75%;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="9" align="center"><strong>Sales Report RDS Wise <?php echo $heading_val;?> for the branch <?php echo $branch_name_rds;?>  </strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl no.</td>
                        <td width="15%" align="left" style="padding-left:20px;">RDS</td>
                        <td width="9%" align="left" style="padding:0px 20px 0px 20px;">Attendence</td>
                        <td width="12%" align="left" style="padding-left:20px;">Purchase</td>
                        <td width="12%" align="left" style="padding-left:20px;">Sale</td>
                        <td width="12%" align="left" style="padding-left:20px;">Stock Transfer</td>
                        <td width="11%" align="left" style="padding-left:20px;">Stock Received</td>
                        <td width="12%" align="left" style="padding-left:20px;">Expenses</td>
                        <td width="12%" align="left" style="padding-left:20px;">Leakage</td>
                    </tr> 
                    <?php
						$sqlrdsemployee="SELECT RM.rds_code,RM.rds_name,RM.emp_code FROM rds_master RM,employee_master EM WHERE 
										RM.emp_code=EM.emp_code AND EM.branch_code='".$branch_name."' ORDER BY rds_name ASC";
						$resrdsemployee=mysqli_query($link,$sqlrdsemployee) or die(mysqli_error()." Error in select rds employee: ".$sqlrdsemployee);
						$sl_no_rds_wise=1;
						while($rowrdsemployee=mysqli_fetch_assoc($resrdsemployee))
						{
							$rds_code=$rowrdsemployee['rds_code'];
							$emp_code=$rowrdsemployee['emp_code'];
							$rds_name=$rowrdsemployee['rds_name'];

							//select total attendance
							$sql_att="SELECT  COUNT(trans_id) AS total_att FROM location LO WHERE SUBSTRING(LO.trans_id,1,1)='A'
									  AND LO.emp_code='".$emp_code."' ".$date_condition_location."";
							$res_att=mysqli_query($link,$sql_att) or die(mysqli_error()." Error in select total attendence: ".$sql_att);
							$row_att=mysqli_fetch_assoc($res_att);
							$total_att=$row_att['total_att'];
							
							//select total purchase
							$sql_purchase="SELECT SUM(OD.qty) AS total_purchase_qty FROM order_header OH,order_details OD
											WHERE OH.order_no=OD.order_no AND SUBSTRING(OH.order_no,1,1)='O'
											AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='PB' ".$date_condition."";
							$res_purchase=mysqli_query($link,$sql_purchase) or die(mysqli_error()." Error in select purchase qty: ".$sql_purchase);
							$row_purchase=mysqli_fetch_assoc($res_purchase);
							$purchase_qty=$row_purchase['total_purchase_qty'];
								
							//select total sales
							$sql_sales="SELECT SUM(OD.qty) AS total_sale_qty FROM order_header OH,order_details OD
										WHERE OH.order_no=OD.order_no AND SUBSTRING(OH.order_no,1,1)='O'
										AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='SB' ".$date_condition."";
							$res_sales=mysqli_query($link,$sql_sales) or die(mysqli_error()." Error in select sales qty: ".$sql_sales);
							$row_sales=mysqli_fetch_assoc($res_sales);
							$sale_qty=$row_sales['total_sale_qty'];
							
							//select total Stock transfer
							$sql_ST="SELECT SUM(OD.qty) AS total_ST_qty FROM order_header OH,order_details OD
									WHERE OH.order_no=OD.order_no AND SUBSTRING(OH.order_no,1,1)='O'
									AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='ST' ".$date_condition."";
							$res_ST=mysqli_query($link,$sql_ST) or die(mysqli_error()." Error in select ST qty: ".$sql_ST);
							$row_ST=mysqli_fetch_assoc($res_ST);
							$ST_qty=$row_ST['total_ST_qty'];
							
							//select total Stock receive	
							$sql_BT="SELECT SUM(OD.qty) AS total_BT_qty FROM order_header OH,order_details OD
									WHERE OH.order_no=OD.order_no AND SUBSTRING(OH.order_no,1,1)='O'
									AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='BT' ".$date_condition."";
							$res_BT=mysqli_query($link,$sql_BT) or die(mysqli_error()." Error in select BT qty: ".$sql_BT);
							$row_BT=mysqli_fetch_assoc($res_BT);
							$BT_qty=$row_BT['total_BT_qty'];	
							
							//select total expenses	
							$sql_exp="SELECT COUNT(trans_id) AS total_exp FROM location LO WHERE SUBSTRING(LO.trans_id,1,2)='TR'
									  AND LO.emp_code='".$emp_code."' ".$date_condition_location."";
							$res_exp=mysqli_query($link,$sql_exp) or die(mysqli_error()." Error in select Expenses: ".$sql_exp);
							$row_exp=mysqli_fetch_assoc($res_exp);
							$total_exp=$row_exp['total_exp'];	
							
							//select total leakage
							$sql_leakage="SELECT SUM(OD.qty) AS total_leakage_qty FROM order_header OH,order_details OD
										  WHERE OH.order_no=OD.order_no AND SUBSTRING(OH.order_no,1,1)='O'
									     AND SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.transaction_type='SH' ".$date_condition."";
							$res_leakage=mysqli_query($link,$sql_leakage) or die(mysqli_error()." Error in select leakage: ".$sql_leakage);
							$row_leakage=mysqli_fetch_assoc($res_leakage);
							$total_leakage=$row_leakage['total_leakage_qty'];	
					?>
							<tr> 
                                    <td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><?php echo $sl_no_rds_wise;?></td>
                                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><a href="javascript:void(0)" 
					onClick="javascript:showRdswisesalesDeatails('<?php echo $rds_code;?>','<?php echo $emp_code;?>','<?php echo $from_date;?>','<?php echo $to_date;?>','<?php echo $radio_type;?>')"  style="color:#930;font-weight:bold;"><?php echo $rds_name;?></a></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $total_att;?></td>
									<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format($purchase_qty,2);?></td>
                                    <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format($sale_qty,2);?></td>
                              		<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format($ST_qty,2);?></td>
                                   <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo number_format($BT_qty,2);?></td>
                                   <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $total_exp;?></td>
                                   <td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $total_leakage;?></td>
                              </tr>
                             <?php
							 $sl_no_rds_wise++;
							}
						?>
            </table><br />
            <?php }?>
                <div id="rdswisesalesdisplay" style="display:none">
                </div><br /><br />
                <div id="productwisesalesdisplay" style="display:none">
                </div><br />
               <div id="loader" style="display:none">
                <br/>
               <center><img src="ajax-loader.gif" /></center>
               </div>
		</td>
	</tr>
</table>
<?php }//End of main()?>