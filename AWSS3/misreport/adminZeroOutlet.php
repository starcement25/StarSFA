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
	$zero_outlet_array=array();
	$phone_no_array=array();
	$customer_name_array=array();
	//$zero_outlet_details_array=array();
	$sqlsaledetails="SELECT CM.customer_code,CM.customer_name,CM.phone_no,CM.rds_tag,
					SUM(CASE WHEN CPB.stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS stock_out_qty,CPB.prod_code 
					FROM
				 	customer_product_billing CPB INNER JOIN customer_master CM 
				 	ON CPB.stock_out_customer_code=CM.customer_code 
				 	GROUP BY CM.customer_code,CPB.prod_code ORDER BY CM.customer_name ASC";
	$rssaledetails=mysql_query($sqlsaledetails);
	while($rowsaledetails=mysql_fetch_array($rssaledetails))
	{
		$customer_code=$rowsaledetails['customer_code'];
		$customer_name=$rowsaledetails['customer_name'];
		$prod_code=$rowsaledetails['prod_code'];
		$phone_no=$rowsaledetails['phone_no'];
		$phone_no=$rowsaledetails['phone_no'];
		$stock_out_qty=$rowsaledetails['stock_out_qty'];
		$rds_tag=$rowsaledetails['rds_tag'];
		
		if($rds_tag=='')
		{
			$sqlselbilling="SELECT COUNT(IMEI) as billed_qty,SUM(CASE WHEN stock_out_date='0000-00-00' AND activation_date!='0000-00-00 00:00:00' 
							THEN 1 ELSE 0 END) AS stock_out_qty_unregistered FROM customer_product_billing  
							WHERE customer_code='".$customer_code."' AND prod_code='".$prod_code."'";
			$rsselbilling=mysql_query($sqlselbilling);
			$rowselbilling=mysql_fetch_array($rsselbilling);
			$billed_qty=$rowselbilling['billed_qty'];
			$stock_out_qty_unregistered=$rowselbilling['stock_out_qty_unregistered'];
			$closing_stock=$billed_qty-($stock_out_qty+$stock_out_qty_unregistered);
			if($closing_stock==0)
			{
				$zero_outlet_details_array[$customer_code][]=$prod_code;
				$sqlselbilling="SELECT COUNT(IMEI) as billed_qty FROM customer_product_billing  
							WHERE customer_code='".$customer_code."' AND prod_code='".$prod_code."'";
				if(!in_array($customer_code,$zero_outlet_array))
				{
					array_push($zero_outlet_array,$customer_code);
					array_push($phone_no_array,$phone_no);
					array_push($customer_name_array,$customer_name);
				}
			}
		}
		else
		{
			$sqlselbilling="SELECT COUNT(IMEI) as consolidated_billed_qty,SUM(CASE WHEN stock_out_date='0000-00-00' 
							AND activation_date!='0000-00-00 00:00:00' THEN 1 ELSE 0 END) AS consolidated_stock_out_qty_unregistered FROM customer_product_billing 
							WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."'";
			$rsselbilling=mysql_query($sqlselbilling);
			$rowselbilling=mysql_fetch_array($rsselbilling);
			$consolidated_billed_qty=$rowselbilling['consolidated_billed_qty'];
			$consolidated_stock_out_qty_unregistered=$rowselbilling['consolidated_stock_out_qty_unregistered'];
			//echo '<br />';
			/*$sqlstockout="SELECT SUM(CASE WHEN IMEI!='' THEN 1 ELSE 0 END) AS consolidated_stock_out_qty  FROM stock_out_details  
							WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."'";*/
			$sqlstockout="SELECT SUM(CASE WHEN stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS consolidated_stock_out_qty  FROM 
							customer_product_billing  
							WHERE stock_out_customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."'";				
			$rsstockout=mysql_query($sqlstockout);
			$rowstockout=mysql_fetch_array($rsstockout);
			$consolidated_stock_out_qty=$rowstockout['consolidated_stock_out_qty'];
			//echo '<br />';
			$closing_stock=$consolidated_billed_qty-($consolidated_stock_out_qty+$consolidated_stock_out_qty_unregistered);
			//echo '<br />';
			if($closing_stock ==0)
			{
				$sqlselbranchoutlet="SELECT customer_code,customer_name,phone_no FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y'";
				$rsselbranchoutlet=mysql_query($sqlselbranchoutlet);
				while($rowselbranchoutlet=mysql_fetch_array($rsselbranchoutlet))
				{
					$zero_outlet_details_array[$rowselbranchoutlet['customer_code']][]=$prod_code;
					if(!in_array($rowselbranchoutlet['customer_code'],$zero_outlet_array))
					{
						array_push($zero_outlet_array,$rowselbranchoutlet['customer_code']);
						array_push($phone_no_array,$rowselbranchoutlet['phone_no']);
						array_push($customer_name_array,$rowselbranchoutlet['customer_name']);
					}
				}
			}
		}
		//$zero_outlet_details_string=//array_push($zero_outlet_details_array,
	}
	//print_r($zero_outlet_array);
	?>


    <table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Zero Outlet</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">		
			<table width="98%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR"><?php echo stripslashes($GLOBALS['err_msg']);?></td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
			</table>
			<table width="40%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 350px;overflow-y: scroll;display:block;">
				<tr class="TDHEAD" > 
					<td colspan="10">Zero Outlet</td>
				</tr>
			<?php 
			if(count($zero_outlet_array) == 0)
			{ 
			?>
				<tr> 
					<td align="center" colspan="8">No records found</td>
				</tr>
			<?php
			}
			else
			{
				//print_r($zero_outlet_details_array);
			?>
				<tr class="TDHEAD_SUB"> 
					<td width="10%" align="center">Sl</td>
                    <td width="60%" align="left" style="padding-left:20px;">Customer name</td>
                    <td width="30%" align="left" style="padding-left:20px;">Phone No</td>
				</tr>   
				<?php
				$cnt=$GLOBALS[start]+1;
				for($i=0;$i<count($zero_outlet_array);$i++)
				{
				?>
				<tr onMouseOver="this.bgColor='<?=SCROLL_COLOR;?>'" onMouseOut="this.bgColor=''" class="body"> 
					<td valign="top" align="center"><?=$cnt++ ?></td>
                    <td align="left" valign="top" style="padding-left:20px;"><a href="javascript:void(0);" style="color:blue;" onclick="javascript:product_details_zero_outlet('<?=$zero_outlet_array[$i]?>','<?=$customer_name_array[$i]?>');"><?=$customer_name_array[$i];?></a></td>
					<td align="left" valign="top" style="padding-left:20px;"><?=$phone_no_array[$i];?></td>
				</tr>
			<?php 
				}
			}
			?>
			</table>
            <br /> <br />
            <div id="productwisedisplay" style="display:none"></div>
<div id="loader" style="display:none">
                <br/>
               <center><img src="ajax-loader.gif" /></center>
               </div>
		</td>
	</tr>
    </td>
</table>
	    <script language="javascript" type="text/javascript">
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
	function product_details_zero_outlet(val1,val2)
	{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		var ouletarr='<?php echo json_encode($zero_outlet_details_array);?>';
		var url="zerooutletproductlist.php";
		xmlHttp.onreadystatechange=zerooutletprodlist;
		xmlHttp.open("POST",url,true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.send("customer_code="+val1+"&ouletarr="+ouletarr+"&customer_name="+val2);
	  }
	function zerooutletprodlist()
	 {
		if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		 {
			var val=xmlHttp.responseText;
			//alert(val);
			if(val!="")
			 {
				document.getElementById('productwisedisplay').style.display='';
				document.getElementById('productwisedisplay').innerHTML=val;
				document.getElementById('loader').style.display='none';
			 }
		}
		else
		{
			document.getElementById('loader').style.display='';
		}
	 }
    </script>

<?php
}//End of main()
?>