<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
		|| strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS'){
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
    	<td align="center">Generate Basic Freight</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_basic_freight_plantwise.php" name="freight_generation" onSubmit="return validation();" method="post">
<input type="hidden" name="freight_mode" value="basic_freight" />
<!--input type="hidden" name="plant_name" value="<?php /*echo $plant_name;?>" />
<input type="hidden" name="loose_rate_ton" value="<?php echo $loose_rate_ton;?>" />
<input type="hidden" name="product_group_code" value="<?php echo $product_group_code;*/?>" /-->
<table cellpadding="4px">
    <tr>
        <td align="left">
        Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" onchange="javascript:return select_depot();">
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
        
        Select Depot:
        <?php $branch_code=$_REQUEST['branch_code'];?>
        <span id="showbranchdetails"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td align="left">
        Select Transport Mode:
        <?php $transport_mode=$_REQUEST['transport_mode'];
			$truck_load=$_REQUEST['truck_load'];
		?>
        <span id="showtransportmode"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;
        
        Truck load(MT):<span id="showloadcapacity"></span>
        <font color="#FF0000">*</font><!--input type="text" name="truck_load" id="truck_load" style="height:20px;" value="<?php //echo $truck_load;?>"/-->&nbsp;
&nbsp;
        </td>
    </tr>
    <tr>
    	 <?php $hire_cost=$_REQUEST['hire_cost'];?>
        <td align="center">Hire Cost:<input type="text" name="hire_cost" id="hire_cost" style="height:20px;" value="<?php echo $hire_cost;?>"/></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value=" Generate Freight "/></td>
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
	var plant_name = document.getElementById("plant_name").value;
	var transport_mode = document.getElementById("transport_mode").value;
 	var truck_load= document.getElementById("truck_load").value;


	var url="returnprevioustruckload-hirecostplantwise.php?branch_code="+branch_code+"&plant_name="+plant_name+"&transport_mode="+transport_mode+"&truck_load="+truck_load;
	xmlHttp.onreadystatechange=previoustruckhirecost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previoustruckhirecost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 valArray=val.split('#');
			// document.getElementById("truck_load").value=valArray[0];
			 document.getElementById("hire_cost").value=valArray[1];
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
	var url="returnbranchdetails.php?plant_name="+plant_name;
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
		 select_transport_mode();
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
	var plant_name = document.getElementById("plant_name").value;
	var transport_mode = document.getElementById("transport_mode").value;
	var url="returntransportmodedetails.php?plant_name="+plant_name+"&transport_mode="+transport_mode+"&type=loadcapacityfreight";
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
	if($_POST['freight_mode']=='basic_freight')
	{
		$current_date=date('Y-m-d');
		$branch_code=$_POST['branch_code'];
		$truck_load=$_POST['truck_load'];
		$hire_cost=$_POST['hire_cost'];
		$transport_mode=$_POST['transport_mode'];
		
		$sqlcheckloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
									AND truck_load='".$truck_load."' ";
		$rscheckloaddistribution=mysqli_query($link,$sqlcheckloaddistribution);
		$countcheckloaddistribution=mysqli_num_rows($rscheckloaddistribution);
		if($countcheckloaddistribution==0)
		{
		?>
        <script language="JavaScript" type="text/javascript">alert('Load distribution does not exists for the corresponding Transport Mode and Truck load.');window.location.href='generate_basic_freight_plantwise.php';</script>
        <?php
		}
		else
		{
		$sqlplant="SELECT plant_name,dns_branch_code FROM branch_master WHERE branch_code='".$branch_code."'";
		$rsplant=mysqli_query($link,$sqlplant);
		$rowplant=mysqli_fetch_assoc($rsplant);
		$plant_name=$rowplant['plant_name'];
		$dns_branch_code=$rowplant['dns_branch_code'];
		/*$sqlcheckvalue="SELECT hire_cost FROM basic_freight WHERE plant_name='".$plant_name."' AND branch_code='".$branch_code."' 
					AND truck_load='".$truck_load."' AND hire_cost='".$hire_cost."'";
		$rscheckvalue=mysqli_query($link,$sqlcheckvalue);
		$countcheckvalue=mysqli_num_rows($rscheckvalue);
		if($countcheckvalue <1)
		{*/				
			$sqlinsertbasicfreight="INSERT INTO basic_freight 
									  SET branch_code='".$dns_branch_code."',
									  truck_load='".$truck_load."',
									  plant_name='".$plant_name."',
									  hire_cost='".$hire_cost."',
									  transport_mode='".$transport_mode."',
									  vertical_value='".$_SESSION['vertical_value']."',
									  ip_address='".$_SERVER['REMOTE_ADDR']."',
									  user_id='".$_SESSION['admin_login']."',
									  operation_type='GENERATE',
									  datetime=CURRENT_TIMESTAMP";
			if(mysqli_query($link,$sqlinsertbasicfreight)){
				//Freight calculation
				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE branch_code='".$branch_code."' AND prod_desc 
											NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N' AND product_group_code !='BR1'";
				$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
				while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
				{
					$dns_prod_code=$rowselectdistinctdnsprod['dns_prod_code'];
					/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$dns_prod_code."' 
									AND  plant_name='".$plant_name."' 
									AND transport_mode='".$transport_mode."' AND truck_load='".$truck_load."' ORDER BY datetime DESC LIMIT 0,1";*/
					$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
								WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$dns_prod_code."'";
					$rschkformulation=mysqli_query($link,$sqlchkformulation);
					$rowchkformulation=mysqli_fetch_assoc($rschkformulation);
					$is_formulation=$rowchkformulation['formulation'];
					${product_group_code.$dns_prod_code}=$rowchkformulation['product_group_code'];	
								
					$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
								AND truck_load='".$truck_load."' AND prod_code='".$dns_prod_code."'  ORDER BY datetime DESC LIMIT 0,1";					
					$rsqtytruckload=mysqli_query($link,$sqlqtytruckload);
					$countqtytruckload=mysqli_num_rows($rsqtytruckload);
					if($countqtytruckload >0)
					{
						$rowqtytruckload=mysqli_fetch_assoc($rsqtytruckload);
						${qty_truck_load.$dns_prod_code}=$rowqtytruckload['qty_truck_load'];
					}
					else{
						${qty_truck_load.$dns_prod_code}=0;
					}
					if(${qty_truck_load.$dns_prod_code}>0)
					{
						${freight_cost.$dns_prod_code}=$hire_cost/${qty_truck_load.$dns_prod_code};
					}
					else
					{
						${freight_cost.$dns_prod_code}=0;
					}
					
					/*$sqlbranchprodchk="SELECT dns_prod_code FROM freight_cost WHERE branch_code='".$branch_code."' AND dns_prod_code='".$dns_prod_code."'";
					$rsbranchprodchk=mysqli_query($link,$sqlbranchprodchk);
					$cntbranchprodchk=mysqli_num_rows($rsbranchprodchk);
					if($cntbranchprodchk >0)	
					{
						$sqlupdatefreightcost="UPDATE freight_cost SET freight_cost='".${freight_cost.$dns_prod_code}."',datetime=CURRENT_TIMESTAMP() 
												WHERE branch_code='".$branch_code."' AND dns_prod_code='".$dns_prod_code."'";
						mysqli_query($link,$sqlupdatefreightcost);
					}
					else
					{*/
						$sqlinsertfreightcost="INSERT INTO freight_cost SET 
												freight_cost='".${freight_cost.$dns_prod_code}."',
												hire_cost='".$hire_cost."',
												transport_mode='".$transport_mode."',
												truck_load='".$truck_load."',
												dns_prod_code='".$dns_prod_code."',
												branch_code='".$branch_code."',
												vertical_value='".$_SESSION['vertical_value']."',
												ip_address='".$_SERVER['REMOTE_ADDR']."',
												user_id='".$_SESSION['admin_login']."',
												datetime=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertfreightcost);
					//}
					if($is_formulation=='yes')
					{
						$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$plant_name."' AND 	
												product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
												SUBSTRING(datetime,1,10)='".$current_date."'";
						$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
						$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
					}
					else
					{
						$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$plant_name."' AND 	
												product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
												SUBSTRING(datetime,1,10)='".$current_date."'";
						$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
						$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
					}
					if($cntchkpricegeneration > 0)
					{
						generate_price_details($dns_prod_code,$branch_code);
					}
				}
				?>
			   <script language="JavaScript" type="text/javascript">alert('Freight generated successfully.');window.location.href='generate_basic_freight_plantwise.php';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Freight generation unsuccessful.');window.location.href='generate_basic_freight_plantwise.php';</script>
            <?php
            }
		}
		/*}
		else
		{
		    $sqlupdatebasicfreight="UPDATE basic_freight 
									SET datetime=CURRENT_TIMESTAMP WHERE branch_code='".$branch_code."' AND truck_load='".$truck_load."' 
									AND plant_name='".$plant_name."' AND hire_cost='".$hire_cost."'";
			mysqli_query($link,$sqlupdatebasicfreight);
		?>
            <script language="JavaScript" type="text/javascript">alert('Freight generated successfully.');window.location.href='generate_basic_freight.php';</script>
          <?php
		}*/
	}
}
?>