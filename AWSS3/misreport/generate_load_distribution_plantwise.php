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
<form action="generate_load_distribution_plantwise.php" name="load_generation" onSubmit="return validation();" method="post">
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
       <!-- Select Plant:
        <?php /*$plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" onchange="javascript:select_transport_mode();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysql_query($sqlplant);
			while($rowplant=mysql_fetch_array($rsplant))
			{
			?>
            <option value="<?php echo $rowplant['plant_name'];?>" <?php if($plant_name==$rowplant['plant_name']){?>selected<?php }?>><?php echo $rowplant['plant_name'];?></option>
            <?php
			}*/
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;!-->
        Select Transport Mode:
        <?php $transport_mode=$_REQUEST['transport_mode'];
			  $content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
				$content.='<option value="">SELECT</option>';
				$sqltransportmode="SELECT DISTINCT transport_mode FROM plantwise_load_capacity ORDER BY transport_mode ASC";
				$rstransportmode=mysql_query($sqltransportmode);
				while($rowtransportmode=mysql_fetch_array($rstransportmode))
				{		
					$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
				}
				echo $content.='</select>';

		   $truck_load_distribution=$_REQUEST['truck_load_distribution'];
		?>
        <!--span id="showtransportmode"></span>
        <font color="#FF0000">*</font-->&nbsp;&nbsp;&nbsp;&nbsp;
        Truck load(MT): <span id="showloadcapacity"></span>
        <font color="#FF0000">*</font>
        </td>
    </tr>
     <!--tr>
        <td align="left">
         Pack Type:
        <?php /*$pack_type=$_REQUEST['pack_type'];?>
        <select name="pack_type" id="pack_type" >
        	<option value="" selected>Select</option>
            <?php 
			$sqlpacktype="SELECT DISTINCT pack_type FROM product_master WHERE pack_type<> '' AND vertical_value='".$_SESSION['vertical_value']."' 
								ORDER BY pack_type ASC";
			$rspacktype=mysql_query($sqlpacktype);
			while($rowpacktype=mysql_fetch_array($rspacktype))
			{
			?>
            <option value="<?php echo $rowpacktype['pack_type'];?>" <?php if($pack_type==$rowpacktype['pack_type']){
				?>selected<?php }?>><?php echo $rowpacktype['pack_type'];?></option>
            <?php
			}*/
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
		</td>
	 </tr-->	
    <!--tr>
        <td align="left">
         Select Oil Group:
        <?php /*$product_group_code=$_REQUEST['product_group_code'];?>
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
        <?php $prod_code=$_REQUEST['prod_code'];*/?>
        <span id="showproductdetails"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr-->
    <!--tr>
    	 <?php //$packing_cost=$_REQUEST['packing_cost'];?>
        <td align="left" colspan="2">Packing Cost:<input type="text" name="packing_cost" id="packing_cost" style="height:20px;" value="<?php //echo $packing_cost;?>"/></td>
    </tr-->
    <tr>
        <td align="center"> Select SKU:<font color="#FF0000">*</font>
    	 <?php 
		    $qty_truck_load	=$_REQUEST['qty_truck_load'];
			$prod_code=$_REQUEST['prod_code'];
			$content='<select name="prod_code" id="prod_code" onChange="javascript:previous_cost();">';
			$content.='<option value="">SELECT</option>';
			$sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE 
						acedns='Y' AND black_list='N' AND vertical_value='".$_SESSION['vertical_value']."' ORDER BY prod_desc ASC";
			$rsproddesc=mysql_query($sqlproddesc);
			while($rowproddesc=mysql_fetch_array($rsproddesc))
			{
				$content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
			}
			 $content.='</select>';
		
			echo $content;
		?>&nbsp;
&nbsp;Truck load qty(CASES):<input type="text" name="qty_truck_load" id="qty_truck_load" style="height:20px;" value="<?php echo $qty_truck_load;?>"/></td>
    </tr>
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
	/*if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}*/
	if(document.getElementById("transport_mode").value.search(/\S/) == -1)
	{
		alert('Select Transport Mode');
		return false;
	}
	if(document.getElementById("truck_load_distribution").value.search(/\S/) == -1)
	{
		alert('Select Truck load(MT)');
		return false;
	}
	if(document.getElementById("prod_code").value.search(/\S/) == -1)
	{
		alert('Select SKU');
		return false;
	}
	/*if(document.getElementById("pack_type").value.search(/\S/) == -1)
	{
		alert('Select Pack Type');
		return false;
	}*/
	/*if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
	if(document.getElementById("prod_code").value.search(/\S/) == -1)
	{
		alert('Select SKU');
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
function previous_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	}
	//var plant_name = document.getElementById("plant_name").value;
	var transport_mode = document.getElementById("transport_mode").value;
 	var truck_load_distribution= document.getElementById("truck_load_distribution").value;
	var prod_code = document.getElementById("prod_code").value;

	var url="returnprevioustruckloadqtyplantwise.php?prod_code="+prod_code+"&transport_mode="+transport_mode+"&truck_load_distribution="+truck_load_distribution;
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
			 //document.getElementById("truck_load_distribution").value=valArray[1];
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
	var url="returnproductdetailsplantwise.php?prod_group_code="+prod_group_code;
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
function select_transport_mode()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var plant_name = document.getElementById("plant_name").value;
	var url="returntransportmodedetails.php?plant_name="+plant_name+"&type=transportmode";
	xmlHttp.onreadystatechange=transportmodedetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function transportmodedetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showtransportmode").innerHTML=val;
		 }
	}
 }
 function load_capacity()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	//var plant_name = document.getElementById("plant_name").value;
	var transport_mode = document.getElementById("transport_mode").value;
	var url="returntransportmodedetails.php?transport_mode="+transport_mode+"&type=loadcapacity";
	xmlHttp.onreadystatechange=loadcapacitydetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function loadcapacitydetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showloadcapacity").innerHTML=val;
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
		//$plant_name=$_POST['plant_name'];
		$transport_mode=$_POST['transport_mode'];
		//$pack_type=$_POST['pack_type'];
		//$packing_cost=$_POST['packing_cost'];
		//$depot_cost=$_POST['depot_cost'];
		
		$sqlinsertloaddistribution="INSERT INTO load_distribution 
									  SET transport_mode='".$transport_mode."',
									  truck_load='".$truck_load_distribution."',
									  qty_truck_load='".$qty_truck_load."',
									  prod_code='".$prod_code."',
									  ip_address='".$_SERVER['REMOTE_ADDR']."',
									  user_id='".$_SESSION['admin_login']."',
									  operation_type='GENERATE',
									  vertical_value='".$_SESSION['vertical_value']."',
									  datetime=CURRENT_TIMESTAMP";
		if(mysql_query($sqlinsertloaddistribution))
			{ 
				//Freight calculation
				/*$sqlselectdistinctbranchcode="SELECT DISTINCT branch_code FROM product_master WHERE dns_prod_code='".$prod_code."' AND prod_desc 
											NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
				$rsselectdistinctbranchcode=mysql_query($sqlselectdistinctbranchcode);
				while($rowselectdistinctbranchcode=mysql_fetch_array($rsselectdistinctbranchcode))
				{
					$branch_code=$rowselectdistinctbranchcode['branch_code'];
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
					generate_price_details($prod_code,$branch_code);
				}*/
			
			?><script language="JavaScript" type="text/javascript">alert('Load generated successfully.');window.location.href='generate_load_distribution_plantwise.php';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Load generation unsuccessful.');window.location.href='generate_load_distribution_plantwise.php';</script>
            <?php
			}
	 }
}
?>