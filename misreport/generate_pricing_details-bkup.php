<?php
ob_start();
	session_start();
	require("adminUtils.php");
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
    	<td align="center">Generate Pricing Details</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_basic_freight.php" name="pricing_details" onSubmit="return validation();" method="post">
<table cellpadding="4px">
    <tr>
        <td align="left">Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name">
        	<option value="" selected>Select</option>
            <?php 
			$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysqli_query($link,$sqlplant);
			while($rowplant=mysqli_fetch_assoc($rsplant))
			{
			?>
            <option value="<?php echo $rowplant['plant_name'];?>" <?php if($plant_name==$rowplant['plant_name']){?>selected<?php }?>><?php echo $rowplant['plant_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        
        Select Oil Group:
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code">
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
			$rsproductgroup=mysqli_query($link,$sqlproductgroup);
			while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
			{
			?>
            <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>selected<?php }?>><?php echo $rowproductgroup['product_group_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;

        </td>
    </tr>
    <tr>
    <?php $loose_rate=$_REQUEST['loose_rate'];
		  $packing_cost=$_REQUEST['packing_cost'];
	?>
        <td align="left">Loose rate(MT):<input type="text" name="loose_rate" id="loose_rate" style="height:20px;" value="<?php echo $loose_rate;?>"/><font color="#FF0000">*</font>&nbsp;
&nbsp;<!--Packing Cost:<input type="text" name="packing_cost" id="packing_cost" style="height:20px;" value="<?php //echo $packing_cost;?>"/>!--></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Submit"/><!--&nbsp;&nbsp;<input name="submit1" type="button" value="Generate Price List"/>!--></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function validation()
{
	if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}
	
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
	
	if(document.getElementById("loose_rate").value.search(/\S/) == -1)
	{
		alert('Input Loose Rate');
		return false;
	}
	return true;
}
/*function GetXmlHttpObject()
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
function previous_packing_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var plant_name = document.getElementById("plant_name").value;
	var product_group_code = document.getElementById("product_group_code").value;

	var url="returnpreviouspackcost.php?plant_name="+plant_name+"&product_group_code="+product_group_code;
	xmlHttp.onreadystatechange=previousPackcost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previousPackcost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("packing_cost").value=val;
		 }
	}
 }*/
</script>
<?php
}
?>