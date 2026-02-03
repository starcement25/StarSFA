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
    	<td align="center">Generate Honeycomb Cost</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="genrate_honeycomb_cost_plantwise.php" name="honeycomb_cost" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_honeycomb" />
<table cellpadding="4px">
	<tr>
        <td align="left">
        Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" onchange="javascript:select_transport_mode();">
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
        
        <!--Select Depot:
        <?php /*$branch_code=$_REQUEST['branch_code'];*/?>
        <span id="showbranchdetails"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;!-->
         Select State:
        <?php $state_code=$_REQUEST['state_code'];?>
        <select name="state_code" id="state_code" >
        	<option value="" selected>Select</option>
            <?php 
			$sqlstate="SELECT dns_state_code FROM state_master ORDER BY dns_state_code ASC";
			$rsstate=mysqli_query($link,$sqlstate);
			while($rowstate=mysqli_fetch_assoc($rsstate))
			{
			?>
            <option value="<?php echo $rowstate['dns_state_code'];?>" <?php if($state_code==$rowstate['dns_state_code']){?>selected<?php }?>>
			<?php echo $rowstate['dns_state_code'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td align="left">
         Select Oil Group:
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code" onchange="javascript:select_product();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
			$rsproductgroup=mysqli_query($link,$sqlproductgroup);
			while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
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
    	 <?php 
		  $honeycomb_cost	=$_REQUEST['honeycomb_cost'];
		?>
        <td align="center">
        <!--State of Customer: 
        	<?php /*$state_code=$_REQUEST['state_code'];?>
        <select name="state_code" id="state_code" onChange="javascript:previous_honey_comb_cost();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlstate="SELECT DISTINCT SM.state,SM.dns_state_code FROM customer_master CM,state_master SM WHERE CM.state_code=SM.dns_state_code 
						ORDER BY SM.state ASC";
			$rsstate=mysqli_query($link,$sqlstate);
			while($rowstate=mysqli_fetch_assoc($rsstate))
			{
			?>
            <option value="<?php echo $rowstate['dns_state_code'];?>" <?php if($state_code==$rowstate['dns_state_code']){?>selected<?php }?>><?php echo $rowstate['state'];?></option>
            <?php
			}*/
			?>
        </select>!-->
        Select Transport Mode:
        <?php $transport_mode=$_REQUEST['transport_mode'];?>
        <span id="showtransportmode"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;

&nbsp;Honeycomb Cost:<input type="text" name="honeycomb_cost" id="honeycomb_cost" style="height:20px;" value="<?php echo $honeycomb_cost;?>"/></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value=" Generate Honeycomb Cost "/></td>
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
	if(document.getElementById("state_code").value.search(/\S/) == -1)
	{
		alert('Select State');
		return false;
	}
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
	if(document.getElementById("transport_mode").value.search(/\S/) == -1)
	{
		alert('Select Transport Mode');
		return false;
	}
	/*if(document.getElementById("state_code").value.search(/\S/) == -1)
	{
		alert('Select State of Customer');
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
function previous_honey_comb_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	}
	var plant_name = document.getElementById("plant_name").value;
	var transport_mode = document.getElementById("transport_mode").value;
 	//var customer_state= document.getElementById("state_code").value;
	var prod_code = document.getElementById("prod_code").value;
	var branch_code = document.getElementById("branch_code").value;

	var url="returnprevioushoneycombcost.php?prod_code="+prod_code+"&plant_name="+plant_name+"&transport_mode="+transport_mode+"&branch_code="+branch_code;
	xmlHttp.onreadystatechange=previoushoneycombcost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previoushoneycombcost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 //document.getElementById("truck_load_distribution").value=valArray[1];
			 document.getElementById("honeycomb_cost").value=val;
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
	var url="returnproductdetailsplantwise.php?prod_group_code="+prod_group_code+"&type=honeycombcost";
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
	var url="returntransportmodedetails.php?plant_name="+plant_name+"&type=honeycombcost";
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
 function select_depot()
	{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		var plant_name = document.getElementById("plant_name").value;
		var url="returnbranchdetails.php?plant_name="+plant_name+"&type=honeycombcost";
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
				 select_transport_mode();
			 }
		}
	 }
</script>
<?php
	if($_REQUEST['mode']=='generate_honeycomb')
	{
		$prod_code=$_POST['prod_code'];
		$state_code=$_POST['state_code'];
		$honeycomb_cost=$_POST['honeycomb_cost'];
		$plant_name=$_POST['plant_name'];
		$branch_code=$_POST['branch_code'];
		$transport_mode=$_POST['transport_mode'];
		$sqlselectdistinctbranchcode="SELECT DISTINCT conversion_factor,conversion_factor_two FROM 
									product_master WHERE dns_prod_code='".$prod_code."'  AND prod_desc 
									 NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
		$rsselectdistinctbranchcode=mysqli_query($link,$sqlselectdistinctbranchcode);
		while($rowselectdistinctbranchcode=mysqli_fetch_assoc($rsselectdistinctbranchcode))
		{
		  //$branch_code=$rowselectdistinctbranchcode['branch_code'];
		  $conversion_factor=$rowselectdistinctbranchcode['conversion_factor'];
		  $conversion_factor_two=$rowselectdistinctbranchcode['conversion_factor_two'];
		  
		  /*$sqldnsbranch="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code."'";
		  $rsdnsbranch=mysqli_query($link,$sqldnsbranch);
		  $rowdnsbranch=mysqli_fetch_assoc($rsdnsbranch);
		  $dns_branch_code=$rowdnsbranch['dns_branch_code'];*/
		  
		  //case conversion
		 $honeycomb_cost_case_prodwise=$honeycomb_cost/$conversion_factor_two;
		 $honeycomb_cost_case_prodwise=round(($honeycomb_cost_case_prodwise*$conversion_factor),2);
		  
		  $sqlinserthoneycombcost="INSERT INTO honeycomb_cost 
									  SET plant_name='".$plant_name."',
									  state_code='".$state_code."',
									  transport_mode='".$transport_mode."',
									  prod_code='".$prod_code."',
									  honeycomb_cost='".$honeycomb_cost_case_prodwise."',
									  honeycomb_cost_ton='".$honeycomb_cost."',
									  ip_address='".$_SERVER['REMOTE_ADDR']."',
									  vertical_value='".$_SESSION['vertical_value']."',
									  user_id='".$_SESSION['admin_login']."',
									  datetime=CURRENT_TIMESTAMP";
			if(mysqli_query($link,$sqlinserthoneycombcost))
			{
				 $sqlinserthoneycombcostlog="INSERT INTO honeycomb_cost_log 
									  SET plant_name='".$plant_name."',
									  state_code='".$state_code."',
									  transport_mode='".$transport_mode."',
									  prod_code='".$prod_code."',
									  honeycomb_cost='".$honeycomb_cost."',
									  ip_address='".$_SERVER['REMOTE_ADDR']."',
									   vertical_value='".$_SESSION['vertical_value']."',
									  user_id='".$_SESSION['admin_login']."',
									  operation_type='GENERATE',
									  datetime=CURRENT_TIMESTAMP";
				 mysqli_query($link,$sqlinserthoneycombcostlog);					  
				$flag=1;
			}
			else
			{
				$flag=0;
			}
		 }
		   if($flag==1){
			?><script language="JavaScript" type="text/javascript">alert('Honeycomb cost generated successfully.');window.location.href='genrate_honeycomb_cost_plantwise.php';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Honeycomb cost generation unsuccessful.');window.location.href='genrate_honeycomb_cost_plantwise.php';</script>
            <?php
			}
	 }
}
?>