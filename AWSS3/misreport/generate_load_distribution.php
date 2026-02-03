<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
disphtml("main();");

function main()
{
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
<input type='hidden' name="mode" value="generate_distribution" />
<!--input type='hidden' name="plant_name" value="<?php /*echo $plant_name;?>" />
<input type='hidden' name="loose_rate_ton" value="<?php echo $loose_rate_ton;?>" />
<input type='hidden' name="product_group_code" value="<?php echo $product_group_code;?>" />
<input type='hidden' name="branch_code" value="<?php echo $branch_code;?>" />
<input type='hidden' name="truck_load" value="<?php echo $truck_load;?>" />
<input type='hidden' name="hire_cost" value="<?php echo $hire_cost;*/?>" /!-->
<table cellpadding="4px">
    <tr>
        <td align="left">
         Select Oil Group:
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code" onchange="javascript:select_product();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
			$rsproductgroup=mysql_query($sqlproductgroup);
			while($rowproductgroup=mysql_fetch_array($rsproductgroup))
			{
			?>
            <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>selected<?php }?>><?php echo $rowproductgroup['product_group_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;

        Select SKU:
        <?php $prod_code=$_REQUEST['prod_code'];?>
        <span id="showproductdetails"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;
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
     <!--tr>
    	 <?php /*$packing_cost=$_REQUEST['packing_cost'];$depot_cost	=$_REQUEST['depot_cost'];?>
        <td align="left">Packing Cost:<input type="text" name="packing_cost" id="packing_cost" style="height:20px;" value="<?php echo $packing_cost;*/?>"/></td>
    </tr-->
    <tr>
    	<td align="center"><input name="submit" type="submit" value=" Generate Load Distribution "/></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function validation()
{
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
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
function previous_cost()
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
		//alert(val);
		if(val!="")
		 {
			 valArray=val.split('#');
			 document.getElementById("truck_load_distribution").value=valArray[1];
			 document.getElementById("qty_truck_load").value=valArray[0];
			 document.getElementById("packing_cost").value=valArray[2];
		 }
	}
 }
function select_product()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_group_code = document.getElementById("product_group_code").value;
	var url="returnproductdetails.php?prod_group_code="+prod_group_code;
	xmlHttp.onreadystatechange=productdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function productdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showproductdetails").innerHTML=val;
		 }
	}
 }

</script>
<?php
	if($_REQUEST['mode']=='generate_distribution')
	{
		$prod_code=$_POST['prod_code'];
		$truck_load_distribution=$_POST['truck_load_distribution'];
		$qty_truck_load=$_POST['qty_truck_load'];
		$current_date=date('Y-m-d');
		//$packing_cost=$_POST['packing_cost'];
		//$depot_cost=$_POST['depot_cost'];
		
		/*$sqlpackingchk="SELECT packing_cost FROM packing_master WHERE dns_prod_code='".$prod_code."' AND packing_cost='".$packing_cost."'";
		$rspackingchk=mysql_query($sqlpackingchk);
		$countpackingchk=mysql_num_rows($rspackingchk);
		if($countpackingchk <1)
		{
			$sqlinsertpackingmaster="INSERT INTO packing_master
									  SET dns_prod_code='".$prod_code."',
									  packing_cost='".$packing_cost."',
									  datetime=CURRENT_TIMESTAMP";
		   	mysql_query($sqlinsertpackingmaster);						  
		}
		else
		{
			$sqlupdatepackingmaster="UPDATE packing_master
									 SET datetime=CURRENT_TIMESTAMP WHERE dns_prod_code='".$prod_code."' AND packing_cost='".$packing_cost."'";
		   	mysql_query($sqlupdatepackingmaster);
		}
		$sqlcheckvalue="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$prod_code."' AND truck_load='".$truck_load_distribution."' 
					 AND qty_truck_load='".$qty_truck_load."'";
		$rscheckvalue=mysql_query($sqlcheckvalue);
		$countcheckvalue=mysql_num_rows($rscheckvalue);
		if($countcheckvalue <1)
		{*/	
			$sqlinsertloaddistribution="INSERT INTO load_distribution 
									  SET truck_load='".$truck_load_distribution."',
									  prod_code='".$prod_code."',
									  qty_truck_load='".$qty_truck_load."',
									  datetime=CURRENT_TIMESTAMP";
			if(mysql_query($sqlinsertloaddistribution))
			{ 
				//Freight calculation
				$sqlselectdistinctbranchcode="SELECT DISTINCT branch_code FROM product_master WHERE dns_prod_code='".$prod_code."' AND prod_desc 
											NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
				$rsselectdistinctbranchcode=mysql_query($sqlselectdistinctbranchcode);
				while($rowselectdistinctbranchcode=mysql_fetch_array($rsselectdistinctbranchcode))
				{
					$branch_code=$rowselectdistinctbranchcode['branch_code'];
					$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					
					$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
										WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$prod_code."'";
					$rschkformulation=mysql_query($sqlchkformulation);
					$rowchkformulation=mysql_fetch_array($rschkformulation);
					$is_formulation=$rowchkformulation['formulation'];
					${product_group_code.$prod_code}=$rowchkformulation['product_group_code'];

					$sqlhirecost="SELECT hire_cost FROM basic_freight WHERE branch_code='".$branch_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rshirecost=mysql_query($sqlhirecost);
					$counthirecost=mysql_num_rows($rshirecost);
					if($counthirecost >0)
					{
						$rowhirecost=mysql_fetch_array($rshirecost);
						${hire_cost.$branch_code}=$rowhirecost['hire_cost'];
					}
					else{
						${hire_cost.$branch_code}=0;
					}
					if(${hire_cost.$branch_code}>0)
					{
						${freight_cost.$branch_code}=${hire_cost.$branch_code}/$qty_truck_load;
					}
					else
					{
						${freight_cost.$branch_code}=0;
					}
					
					$sqlbranchprodchk="SELECT dns_prod_code FROM freight_cost WHERE branch_code='".$branch_code."' AND dns_prod_code='".$prod_code."'";
					$rsbranchprodchk=mysql_query($sqlbranchprodchk);
					$cntbranchprodchk=mysql_num_rows($rsbranchprodchk);
					if($cntbranchprodchk >0)						{
						$sqlupdatefreightcost="UPDATE freight_cost SET freight_cost='".${freight_cost.$branch_code}."',datetime=CURRENT_TIMESTAMP() 
												WHERE branch_code='".$branch_code."' AND dns_prod_code='".$prod_code."'";
						mysql_query($sqlupdatefreightcost);
					}
					else
					{
						$sqlinsertfreightcost="INSERT INTO freight_cost SET 
												freight_cost='".${freight_cost.$branch_code}."',
												dns_prod_code='".$prod_code."',
												branch_code='".$branch_code."',
												datetime=CURRENT_TIMESTAMP()";
						mysql_query($sqlinsertfreightcost);
					}
					if($is_formulation=='yes')
					{
						$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
												product_group_code='".${product_group_code.$prod_code}."' AND 	
												SUBSTRING(datetime,1,10)='".$current_date."'";
						$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
						$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
					}
					else
					{
						$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
												product_group_code='".${product_group_code.$prod_code}."' AND 	
												SUBSTRING(datetime,1,10)='".$current_date."'";
						$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
						$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
					}
					if($cntchkpricegeneration > 0)
					{
						generate_price_details($prod_code,$branch_code);
					}
				}
			
			?><script language="JavaScript" type="text/javascript">alert('Load generated successfully.');window.location.href='generate_load_distribution.php';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Load generation unsuccessful.');window.location.href='generate_load_distribution.php';</script>
            <?php
			}
		/*}
	    else
	    {
		  	$sqlupdateloaddistribution="UPDATE load_distribution 
									  SET datetime=CURRENT_TIMESTAMP WHERE truck_load='".$truck_load_distribution."' AND prod_code='".$prod_code."' AND 
									  qty_truck_load='".$qty_truck_load."'";
		    mysql_query($sqlupdateloaddistribution);
		  ?>
		<script language="JavaScript" type="text/javascript">alert('Load generated successfully.');window.location.href='generate_load_distribution.php';</script>
		  <?php 
	     }*/
	 }
}
?>