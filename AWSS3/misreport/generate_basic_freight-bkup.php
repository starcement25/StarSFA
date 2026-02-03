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
	$loose_rate_ton=$_POST['loose_rate'];
	
	/*$sqlfilterwiseconversion="SELECT conversion_factor_two FROM product_master WHERE product_group_code='".$product_group_code."'";
	$rsfilterwiseconversion=mysql_query($sqlfilterwiseconversion);
	$rowfilterwiseconversion=mysql_fetch_array($rsfilterwiseconversion);
	$conversion_two=$rowfilterwiseconversion['conversion_factor_two'];
	$loose_rate_case=($loose_rate_ton/$conversion_two);			
	
	if($packing_cost=='')
	{
		$selectpreviouspackcost="SELECT packing_cost FROM pricing_detials WHERE plant_name='".$plant_name."' AND 
								product_group_code='".$product_group_code."' ORDER BY datetime DESC LIMIT 0,1";
		$rspreviouspackcost=mysql_query($selectpreviouspackcost);
		$countpreviouspackcost=mysql_num_rows($rspreviouspackcost);
		if($countpreviouspackcost >0)
		{
			$rowpreviouspackcost=mysql_fetch_array($rspreviouspackcost);
			$previos_packing_cost=$rowpreviouspackcost['packing_cost'];
			if($previos_packing_cost >0)  $packing_cost=$previos_packing_cost;
			else						  $packing_cost=0;
		}
		else
		{
			$packing_cost=0;
		}
	}*/
	$sqlcheckvalue="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$plant_name."' AND product_group_code='".$product_group_code."' 
					AND loose_rate_ton='".$loose_rate_ton."'";
	$rscheckvalue=mysql_query($sqlcheckvalue);
	$countcheckvalue=mysql_num_rows($rscheckvalue);
	if($countcheckvalue <1)
	{				
		$sqlinsertpricingdetails="INSERT INTO pricing_detials 
								  SET loose_rate_ton='".$loose_rate_ton."',
								  plant_name='".$plant_name."',
								  product_group_code='".$product_group_code."',
								  datetime=CURRENT_TIMESTAMP";
		$rsinsertpricingdetails=mysql_query($sqlinsertpricingdetails);	
	}
?><head>
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>

<center>
<br /><br />
<table cellpadding="4px" width="70%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate Basic Freight</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_load_distribution.php" name="freight_generation" onSubmit="return validation();" method="post">
<input type="hidden" name="freight_mode" value="basic_freight" />
<input type="hidden" name="plant_name" value="<?php echo $plant_name;?>" />
<input type="hidden" name="loose_rate_ton" value="<?php echo $loose_rate_ton;?>" />
<input type="hidden" name="product_group_code" value="<?php echo $product_group_code;?>" />
<table cellpadding="4px">
    <tr>
        <td align="left">Select Depot:
        <?php $branch_code=$_REQUEST['branch_code'];?>
        <select name="branch_code" id="branch_code" onChange="javascript:previous_truckload_hirecost();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlbranch="SELECT DISTINCT branch_name,branch_code FROM branch_master WHERE plant_name='".$plant_name."' ORDER BY branch_name ASC";
			$rsbranch=mysql_query($sqlbranch);
			while($rowbranch=mysql_fetch_array($rsbranch))
			{
			?>
            <option value="<?php echo $rowbranch['branch_code'];?>" <?php if($branch_code==$rowbranch['branch_code']){?>selected<?php }?>><?php echo $rowbranch['branch_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
    	 <?php $truck_load=$_REQUEST['truck_load'];
		  $hire_cost=$_REQUEST['hire_cost'];
		?>
        <td align="center">Truck load(MT):<input type="text" name="truck_load" id="truck_load" style="height:20px;" value="<?php echo $truck_load;?>"/>&nbsp;
&nbsp;Hire Cost:<input type="text" name="hire_cost" id="hire_cost" style="height:20px;" value="<?php echo $hire_cost;?>"/></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Submit"/></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function validation()
{
	if(document.getElementById("branch_code").value.search(/\S/) == -1)
	{
		alert('Select Depot');
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
function previous_truckload_hirecost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var branch_code = document.getElementById("branch_code").value;

	var url="returnprevioustruckload-hirecost.php?branch_code="+branch_code;
	xmlHttp.onreadystatechange=previoustruckhirecost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previoustruckhirecost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		alert(val);
		if(val!="")
		 {
			 valArray=val.split('#');
			 document.getElementById("truck_load").value=valArray[0];
			 document.getElementById("hire_cost").value=valArray[1];
		 }
	}
 }

</script>
<?php
}
?>