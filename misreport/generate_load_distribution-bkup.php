<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
disphtml("main();");

function main()
{
	$plant_name=$_POST['plant_name'];
	$product_group_code=$_POST['product_group_code'];
	$loose_rate_ton=$_POST['loose_rate_ton'];
	$branch_code=$_POST['branch_code'];
	$truck_load=$_POST['truck_load'];
	$hire_cost=$_POST['hire_cost'];
	
	if($_POST['freight_mode']=='basic_freight')
	{
		$sqlcheckvalue="SELECT hire_cost FROM basic_freight WHERE plant_name='".$plant_name."' AND branch_code='".$branch_code."' 
					AND truck_load='".$truck_load."' AND hire_cost='".$hire_cost."'";
		$rscheckvalue=mysqli_query($link,$sqlcheckvalue);
		$countcheckvalue=mysqli_num_rows($rscheckvalue);
		if($countcheckvalue <1)
		{				
			$sqlinsertbasicfreight="INSERT INTO basic_freight 
									  SET branch_code='".$branch_code."',
									  truck_load='".$truck_load."',
									  plant_name='".$plant_name."',
									  hire_cost='".$hire_cost."',
									  datetime=CURRENT_TIMESTAMP";
			$rsinsertinsertbasicfreight=mysqli_query($link,$sqlinsertbasicfreight);
		}
	}
?><head>
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>

<center>
<br /><br />
<table cellpadding="4px" width="70%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate Load Distribution</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_load_distribution.php" name="load_generation" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_price_list" />
<input type='hidden' name="plant_name" value="<?php echo $plant_name;?>" />
<input type='hidden' name="loose_rate_ton" value="<?php echo $loose_rate_ton;?>" />
<input type='hidden' name="product_group_code" value="<?php echo $product_group_code;?>" />
<input type='hidden' name="branch_code" value="<?php echo $branch_code;?>" />
<input type='hidden' name="truck_load" value="<?php echo $truck_load;?>" />
<input type='hidden' name="hire_cost" value="<?php echo $hire_cost;?>" />
<table cellpadding="4px">
    <tr>
        <td align="left">Select SKU:
        <?php $prod_code=$_REQUEST['prod_code'];?>
        <select name="prod_code" id="prod_code" onChange="javascript:previous_truckload_qty();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE product_group_code='".$product_group_code."' ORDER BY prod_desc ASC";
			$rsproddesc=mysqli_query($link,$sqlproddesc);
			while($rowproddesc=mysqli_fetch_assoc($rsproddesc))
			{
			?>
            <option value="<?php echo $rowproddesc['dns_prod_code'];?>" <?php if($prod_code==$rowproddesc['dns_prod_code']){?>selected<?php }?>><?php echo $rowproddesc['prod_desc'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr>
    <!--tr>
    	 <?php //$packing_cost=$_REQUEST['packing_cost'];?>
        <td align="left" colspan="2">Packing Cost:<input type="text" name="packing_cost" id="packing_cost" style="height:20px;" value="<?php //echo $packing_cost;?>"/></td>
    </tr-->
    <tr>
    	 <?php $truck_load_distribution=$_REQUEST['truck_load_distribution'];
		  $qty_truck_load	=$_REQUEST['qty_truck_load'];
		?>
        <td align="center">Truck load(MT):<input type="text" name="truck_load_distribution" id="truck_load_distribution" style="height:20px;" value="<?php echo $truck_load_distribution;?>"/>&nbsp;
&nbsp;Truck load qty(CASES):<input type="text" name="qty_truck_load" id="qty_truck_load" style="height:20px;" value="<?php echo $qty_truck_load;?>"/></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Generate Price List"/></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function validation()
{
	if(document.getElementById("prod_code").value.search(/\S/) == -1)
	{
		alert('Select SKU');
		return false;
	}
	return true;
}
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
function previous_truckload_qty()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_code = document.getElementById("prod_code").value;

	var url="returnprevioustruckloadqty.php?prod_code="+prod_code;
	xmlHttp.onreadystatechange=previoustruckloadqty;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previoustruckloadqty()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		alert(val);
		if(val!="")
		 {
			 valArray=val.split('#');
			 document.getElementById("truck_load_distribution").value=valArray[1];
			 document.getElementById("qty_truck_load").value=valArray[0];
			 //document.getElementById("packing_cost").value=valArray[2];
		 }
	}
 }

</script>
<?php
	if($_REQUEST['mode']=='generate_price_list')
	{
		$prod_code=$_POST['prod_code'];
		$truck_load_distribution=$_POST['truck_load_distribution'];
		$qty_truck_load=$_POST['qty_truck_load'];
		$plant_name=$_POST['plant_name'];
		$product_group_code=$_POST['product_group_code'];
		$loose_rate_ton=$_POST['loose_rate_ton'];
		$loose_rate_litre=$_POST['loose_rate_litre'];
		//$packing_cost=$_POST['packing_cost'];
		$branch_code=$_POST['branch_code'];
		$truck_load=$_POST['truck_load'];
		$hire_cost=$_POST['hire_cost'];
		
		$sqlcheckvalue="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$prod_code."' AND truck_load='".$truck_load_distribution."' 
					 AND qty_truck_load='".$qty_truck_load."'";
		$rscheckvalue=mysqli_query($link,$sqlcheckvalue);
		$countcheckvalue=mysqli_num_rows($rscheckvalue);
		if($countcheckvalue <1)
		{	
			$sqlinsertloaddistribution="INSERT INTO load_distribution 
									  SET truck_load='".$truck_load_distribution."',
									  prod_code='".$prod_code."',
									  qty_truck_load='".$qty_truck_load."',
									  datetime=CURRENT_TIMESTAMP";
			$rsinsertloaddistribution=mysqli_query($link,$sqlinsertloaddistribution);
		}
		 if($product_group_code !='BR1')
		 {	
			/*$final_freight=$hire_cost/$qty_truck_load;
			
		    $sqlpacking="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$prod_code."'";
			$rspacking=mysqli_query($link,$sqlpacking);
			$rowpacking=mysqli_fetch_assoc($rspacking);
			$packing_pc=$rowpacking['packing_pc'];
			$packing_cost=$rowpacking['packing_cost'];
			$loose_rate_case=($loose_rate_ton/1000)*$packing_pc;// 1000 for MT to liter conversion	
			
			$mrp=$loose_rate_case+$final_freight+$packing_cost;
			$sql_prod_code="SELECT prod_code FROM product_master WHERE branch_code='".$branch_code."' AND dns_prod_code='".$prod_code."'";
			$rs_prod_code=mysqli_query($link,$sql_prod_code);
			$cntprod_code=mysqli_num_rows($rs_prod_code);
			$row_prod_code=mysqli_fetch_assoc($rs_prod_code);
			$prod_code_master=$row_prod_code['prod_code'];
			if($cntprod_code >0)
			{
				$sqlgenerateprice="UPDATE mrp SET mrp='".$mrp."',sale_rate='".$mrp."',download_time=CURRENT_TIMESTAMP() WHERE  product_code='".$prod_code_master."'";
				mysqli_query($link,$sqlgenerateprice);
			}*/
			//For product group wise all product data mrp updation on the basis of Loose rate
			$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."'";
			$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
			while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
			{
				$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
				$sqlfreight="SELECT hire_cost FROM basic_freight WHERE branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
				$rsfreight=mysqli_query($link,$sqlfreight);
				$rowfreight=mysqli_fetch_assoc($rsfreight);
				${hire_cost.$distinct_branch_code}=$rowfreight['hire_cost'];
				if(${hire_cost.$distinct_branch_code}=='')
				{
					${hire_cost.$distinct_branch_code}=0;
				}
				
				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE product_group_code='".$product_group_code."'";
				$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
				while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
				{
					$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
					$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."'";
					$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
					$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
					${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
					${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
					
					$sqltruckloadqty="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$distinct_dnsprod_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rstruckloadqty=mysqli_query($link,$sqltruckloadqty);
					$rowtruckloadqty=mysqli_fetch_assoc($rstruckloadqty);
					${qty_truck_load.$distinct_dnsprod_code}=$rowtruckloadqty['qty_truck_load'];
					if(${qty_truck_load.$distinct_dnsprod_code}=='')
					{
						${qty_truck_load.$distinct_dnsprod_code}=0;
					}
					
					$loose_rate_case_prodwise=($loose_rate_ton/1000)*${packing_pc.$distinct_dnsprod_code};
					//echo ${hire_cost.$distinct_branch_code};
					//echo ${qty_truck_load.$distinct_dnsprod_code};
					if(${qty_truck_load.$distinct_dnsprod_code}>0)
					{
						$final_freight_prodwise=${hire_cost.$distinct_branch_code}/${qty_truck_load.$distinct_dnsprod_code};
					}
					else
					{
						$final_freight_prodwise=0;
					}
					
					$mrp_prodwise=$loose_rate_case_prodwise+$final_freight_prodwise+${packing_cost.$distinct_dnsprod_code};
					
					$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code."' AND dns_prod_code='".$distinct_dnsprod_code."'";
					$rs_prod_code=mysqli_query($link,$sql_prod_code);
					$cntprod_code=mysqli_num_rows($rs_prod_code);
					$row_prod_code=mysqli_fetch_assoc($rs_prod_code);
					$prod_code_master=$row_prod_code['prod_code'];
					$vertical_value_master=$row_prod_code['vertical_value'];
					
					if($cntprod_code >0)
					{
						$sqlbranchprodchk="SELECT product_code FROM mrp WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
						$rsbranchprodchk=mysqli_query($link,$sqlbranchprodchk);
						$cntbranchprodchk=mysqli_num_rows($rsbranchprodchk);
						if($cntbranchprodchk >0)
						{
							$sqlupdatemrpprodwise="UPDATE mrp SET mrp='".$mrp_prodwise."',sale_rate='".$mrp_prodwise."',download_time=CURRENT_TIMESTAMP() 
											WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
							mysqli_query($link,$sqlupdatemrpprodwise);
						}
						else
						{
							$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
							$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
							$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
							$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
							$max_mrp_code++;
							$max_mrp_code='z'.$max_mrp_code;

						   $sqlinsertmrpprodwise="INSERT INTO mrp SET mrp_code='".$max_mrp_code."',mrp='".$mrp_prodwise."',sale_rate='".$mrp_prodwise."',
						   							branch_code='".$distinct_branch_code."',product_code='".$prod_code_master."',vertical_value='".$vertical_value_master."',download_time=CURRENT_TIMESTAMP()";
							mysqli_query($link,$sqlinsertmrpprodwise);
						}
					}
					//exit();
					$flag=1;				
				}
			}
			//exit();
			//End For product group wise all product data mrp updation on the basis of Loose rate
			if($flag==1){?>
			   <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='product_groupwise_pricelist.php?product_group_code=<?php echo $product_group_code;?>';</script>
			<?php }else{
                //header("location: license-details.php?addlicense=failure");
                ?><script language="JavaScript" type="text/javascript">alert('Price generation unsuccessful.');window.location.href='generate_pricing_details.php';</script>
            <?php
            }
		  }
		  else
		  {
			  ?>
				 <script language="JavaScript" type="text/javascript">alert('Price calculation for MUSTARD is under progress.');window.location.href='generate_pricing_details.php';</script>
			  <?php 
		  }
		}
	 }
?>