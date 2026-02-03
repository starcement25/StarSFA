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
    	<td align="center">Generate Mergin Cost</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_margin_cost.php" name="margin_cost" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_margin_cost" />
<table cellpadding="4px">
    <tr>
        <td align="left">
         Select Oil Group:
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code" >
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
			$rsproductgroup=mysql_query($sqlproductgroup);
			while($rowproductgroup=mysql_fetch_array($rsproductgroup))
			{
			?>
            <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>selected<?php }?>><?php echo $rowproductgroup['product_group_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;

       Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" >
        	<option value="" selected>Select</option>
            <?php 
			$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysql_query($sqlplant);
			while($rowplant=mysql_fetch_array($rsplant))
			{
			?>
            <option value="<?php echo $rowplant['plant_name'];?>" <?php if($plant_name==$rowplant['plant_name']){?>selected<?php }?>><?php echo $rowplant['plant_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;

        </td>
    </tr>
     <!--tr>
        <td align="left">
        Select Plant:
        <?php /*$plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" onchange="javascript:select_depot();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysql_query($sqlplant);
			while($rowplant=mysql_fetch_array($rsplant))
			{
			?>
            <option value="<?php echo $rowplant['plant_name'];?>" <?php if($plant_name==$rowplant['plant_name']){?>selected<?php }?>><?php echo $rowplant['plant_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        Select Depot:
        <?php $branch_code=$_REQUEST['branch_code'];*/?>
        <span id="showbranchdetails"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr-->
     <tr>
    	 <?php $margin_cost	=$_REQUEST['margin_cost'];?>
        <td align="left">Mergin Cost:<input type="text" name="margin_cost" id="margin_cost" style="height:20px;" value="<?php echo $margin_cost;?>"/></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value=" Generate Mergin Cost "/></td>
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
	/*if(document.getElementById("prod_code").value.search(/\S/) == -1)
	{
		alert('Select SKU');
		return false;
	}*/
	if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}
	/*if(document.getElementById("branch_code").value.search(/\S/) == -1)
	{
		alert('Select Depot');
		return false;
	}*/
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
function previous_margin_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_code = document.getElementById("prod_code").value;
	var branch_code = document.getElementById("branch_code").value;

	var url="returnpreviousmargincost.php?prod_code="+prod_code+"&branch_code="+branch_code;
	xmlHttp.onreadystatechange=previousmargincost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previousmargincost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		document.getElementById("margin_cost").value=val;
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
function select_depot()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var plant_name = document.getElementById("plant_name").value;
	var url="returnbranchmargincost.php?plant_name="+plant_name;
	xmlHttp.onreadystatechange=depotdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function depotdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showbranchdetails").innerHTML=val;
		 }
	}
 }

</script>
<?php
	if($_REQUEST['mode']=='generate_margin_cost')
	{
		$product_group_code=$_POST['product_group_code'];
		$margin_cost=$_POST['margin_cost'];
		$plant_name=$_POST['plant_name'];
		
		$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."' 
									AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."')";
		$rsselectdistinctbranch=mysql_query($sqlselectdistinctbranch);
		while($rowselectdistinctbranch=mysql_fetch_array($rsselectdistinctbranch))
		{
			$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
										product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."')";
			$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
			{
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];

				$magin_cost_case_prodwise=round(($margin_cost/${conversion_factor_two.$distinct_dnsprod_code}),2);
				$magin_cost_case_prodwise=round(($magin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

				$sqlinsertmargincost="INSERT INTO margin_cost 
							SET dns_prod_code='".$distinct_dnsprod_code."',
							branch_code='".$distinct_branch_code."',
							margin_cost='".$magin_cost_case_prodwise."',
							datetime=CURRENT_TIMESTAMP";
				mysql_query($sqlinsertmargincost);
			}
		}
		$successval=1;
		if($successval==1)
		{ ?><script language="JavaScript" type="text/javascript">alert('Mergin cost generated successfully.');window.location.href='generate_margin_cost.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Mergin cost generation unsuccessful.');window.location.href='generate_margin_cost.php';</script>
		<?php
		}
	}
}
?>