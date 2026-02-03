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
<form action="generate_basic_freight.php" name="freight_generation" onSubmit="return validation();" method="post">
<input type="hidden" name="freight_mode" value="basic_freight" />
<!--input type="hidden" name="plant_name" value="<?php /*echo $plant_name;?>" />
<input type="hidden" name="loose_rate_ton" value="<?php echo $loose_rate_ton;?>" />
<input type="hidden" name="product_group_code" value="<?php echo $product_group_code;*/?>" /-->
<table cellpadding="4px">
    <tr>
        <td align="left">
        Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
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
        <?php $branch_code=$_REQUEST['branch_code'];?>
        <span id="showbranchdetails"></span>
        <font color="#FF0000">*</font>&nbsp;&nbsp;
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
		//alert(val);
		if(val!="")
		 {
			 valArray=val.split('#');
			 document.getElementById("truck_load").value=valArray[0];
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
	}
 }

</script>
<?php
	if($_POST['freight_mode']=='basic_freight')
	{
		$branch_code=$_POST['branch_code'];
		$truck_load=$_POST['truck_load'];
		$hire_cost=$_POST['hire_cost'];
		$current_date=date('Y-m-d');
		
		$sqlplant="SELECT plant_name FROM branch_master WHERE branch_code='".$branch_code."'";
		$rsplant=mysql_query($sqlplant);
		$rowplant=mysql_fetch_array($rsplant);
		$plant_name=$rowplant['plant_name'];
		
		/*$sqlcheckvalue="SELECT hire_cost FROM basic_freight WHERE plant_name='".$plant_name."' AND branch_code='".$branch_code."' 
					AND truck_load='".$truck_load."' AND hire_cost='".$hire_cost."'";
		$rscheckvalue=mysql_query($sqlcheckvalue);
		$countcheckvalue=mysql_num_rows($rscheckvalue);
		if($countcheckvalue <1)
		{*/				
			$sqlinsertbasicfreight="INSERT INTO basic_freight 
									  SET branch_code='".$branch_code."',
									  truck_load='".$truck_load."',
									  plant_name='".$plant_name."',
									  hire_cost='".$hire_cost."',
									  vertical_value='".$_SESSION['vertical_value']."',
									  ip_address='".$_SERVER['REMOTE_ADDR']."',
									  datetime=CURRENT_TIMESTAMP";
			if(mysql_query($sqlinsertbasicfreight)){
				//Freight calculation
				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE branch_code='".$branch_code."' AND prod_desc 
											NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N' AND product_group_code !='BR1'";
				$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
				while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
				{
					$dns_prod_code=$rowselectdistinctdnsprod['dns_prod_code'];
					$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
										WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$dns_prod_code."'";
					$rschkformulation=mysql_query($sqlchkformulation);
					$rowchkformulation=mysql_fetch_array($rschkformulation);
					$is_formulation=$rowchkformulation['formulation'];
					${product_group_code.$dns_prod_code}=$rowchkformulation['product_group_code'];

					$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$dns_prod_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rsqtytruckload=mysql_query($sqlqtytruckload);
					$countqtytruckload=mysql_num_rows($rsqtytruckload);
					if($countqtytruckload >0)
					{
						$rowqtytruckload=mysql_fetch_array($rsqtytruckload);
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
					$rsbranchprodchk=mysql_query($sqlbranchprodchk);
					$cntbranchprodchk=mysql_num_rows($rsbranchprodchk);
					if($cntbranchprodchk >0)	
					{
						$sqlupdatefreightcost="UPDATE freight_cost SET freight_cost='".${freight_cost.$dns_prod_code}."',datetime=CURRENT_TIMESTAMP() 
												WHERE branch_code='".$branch_code."' AND dns_prod_code='".$dns_prod_code."'";
						mysql_query($sqlupdatefreightcost);
					}
					else
					{*/
						$sqlinsertfreightcost="INSERT INTO freight_cost SET 
												freight_cost='".${freight_cost.$dns_prod_code}."',
												dns_prod_code='".$dns_prod_code."',
												branch_code='".$branch_code."',
												vertical_value='".$_SESSION['vertical_value']."',
												datetime=CURRENT_TIMESTAMP()";
						mysql_query($sqlinsertfreightcost);
					//}
					if($is_formulation=='yes')
					{
						$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$plant_name."' AND 	
												product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
												SUBSTRING(datetime,1,10)='".$current_date."'";
						$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
						$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
					}
					else
					{
						$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$plant_name."' AND 	
												product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
												SUBSTRING(datetime,1,10)='".$current_date."'";
						$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
						$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
					}
					if($cntchkpricegeneration > 0)
					{
						generate_price_details($dns_prod_code,$branch_code);
					}
				}
				?>
			   <script language="JavaScript" type="text/javascript">alert('Freight generated successfully.');window.location.href='generate_basic_freight.php';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Freight generation unsuccessful.');window.location.href='generate_basic_freight.php';</script>
            <?php
            }
		/*}
		else
		{
		    $sqlupdatebasicfreight="UPDATE basic_freight 
									SET datetime=CURRENT_TIMESTAMP WHERE branch_code='".$branch_code."' AND truck_load='".$truck_load."' 
									AND plant_name='".$plant_name."' AND hire_cost='".$hire_cost."'";
			mysql_query($sqlupdatebasicfreight);
		?>
            <script language="JavaScript" type="text/javascript">alert('Freight generated successfully.');window.location.href='generate_basic_freight.php';</script>
          <?php
		}*/
	}
}
?>