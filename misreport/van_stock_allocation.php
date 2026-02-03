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
<table cellpadding="4px" width="40%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Van Stock Allocation</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="van_stock_allocation.php" name="van_stock"  method="post" onsubmit="return validation();">
<input type='hidden' name="mode" value="generate_van_stock" />
<table cellpadding="4px">
		<tr>
        <td align="right" width="25%"  valign="top">Select Van:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left">
        <?php $emp_code=$_REQUEST['emp_code'];?>
        <select name="emp_code" id="emp_code" >
        	<option value="" selected>Select</option>
                <?php 
               $sqlemployee="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ORDER BY emp_name ASC";
               $rsemployee=mysqli_query($link,$sqlemployee);
                while($rowemployee=mysqli_fetch_assoc($rsemployee))
                {
                ?>
                <option value="<?php echo $rowemployee['emp_code'];?>" <?php if($emp_code==$rowemployee['emp_code']){?>selected<?php }?>><?php echo $rowemployee['emp_name'];?></option>
                <?php
				}
            ?>
        </select>
        </td>
      </tr>  
	   <tr>
        <td align="right" width="25%"  valign="top">Select Brand:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left">
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <!--select name="product_group_code" id="product_group_code" onchange="javascript:select_product_subgroup();"-->
        <select name="product_group_code" id="product_group_code" onchange="javascript:select_product();">
        	<option value="" selected>Select</option>
                <?php 
               $sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
                $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                {
                ?>
                <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>selected<?php }?>><?php echo $rowproductgroup['product_group_name'];?></option>
                <?php
        
                /*$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master  
										WHERE vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
                $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                {?>
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>checked<?php }?>/><?php echo $rowproductgroup['product_group_name'];?>
                        </td>
                     </tr>   
                <?php
                }*/
				}
            ?>
        </select>
        </td>
      </tr>  
      <!--tr>
        <td align="right" width="25%"  valign="top">Select Sub Brand:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left" id="showproductsubgroupdetails">
        </td>
      </tr--> 
      <tr>
        <td align="right" width="25%"  valign="top"><b>Input Qty:</b><font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left" width=""><table id="showproductdetails" align="left" class="border" border="1"></table></td>
      </tr>  
    <tr>
    	<td align="center" colspan="2"><input name="submit" type="submit" value=" Allocate "/></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function validation()
{
	if(document.getElementById("emp_code").value.search(/\S/) == -1)
	{
		alert('Select Van');
		return false;
	}
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Brand');
		return false;
	}
	/*if(document.getElementById("product_sub_group_code").value.search(/\S/) == -1)
	{
		alert('Select Sub Brand');
		return false;
	}*/
	var flag=false;
	var cbs = document.getElementsByTagName('input');
	  for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'text') {
		  if(cbs[i].value.search(/\S/) == -1)
		  {
			  var flag=true;
		  }
		}
	  }
	  if(flag==true)
	  {
		  alert("Please Input valid Qty");
		  return false;
	  }
	/*var flag=false;
	var cbs = document.getElementsByTagName('input');
	  for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'checkbox') {
		  if(cbs[i].checked ==true)
		  {
			  var flag=true;
		  }
		}
	  }
	  if(flag==false)
	  {
		  alert("Please select at least one oil group");
		  return false;
	  }
	  var flag=false;
	  //var flagplant=false;

	  checkboxes = document.getElementsByName('product_group_code[]');
	  //checkboxesplant = document.getElementsByName('plant_name[]');

	   for(var i in checkboxes){
		   if(checkboxes[i].checked==true)
		   {
			   var flag=true;
		   }
	   }
	   if(flag==false)
		  {
			  alert("Please select at least one oil group");
			  return false;
		  }*/
	  /* for(var k in checkboxesplant){
		   if(checkboxesplant[k].checked==true)
		   {
			   var flagplant=true;
		   }
	   }
	   if(flagplant==false)
		  {
			  alert("Please select at least one plant");
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
function previous_depot_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_code = document.getElementById("prod_code").value;
	var branch_code = document.getElementById("branch_code").value;

	var url="returnpreviousdepotcost.php?prod_code="+prod_code+"&branch_code="+branch_code;
	xmlHttp.onreadystatechange=previousdepotcost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previousdepotcost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		document.getElementById("depot_cost").value=val;
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
	//var product_sub_group_code = document.getElementById("product_sub_group_code").value;
	
	//var url="returnproductdetailsvan.php?prod_group_code="+prod_group_code+"&product_sub_group_code="+product_sub_group_code;
	var url="returnproductdetailsvan.php?prod_group_code="+prod_group_code;
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
function select_product_subgroup()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_group_code = document.getElementById("product_group_code").value;
	var url="returnproductsubgroupdetails.php?prod_group_code="+prod_group_code;
	xmlHttp.onreadystatechange=productsubgroupdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function productsubgroupdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showproductsubgroupdetails").innerHTML=val;
		 }
	}
 }
</script>
<?php
	if($_REQUEST['mode']=='generate_van_stock')
	{
		//$product_group_code=$_POST['product_group_code'];
		//$product_group_code_array=$_POST['product_group_code'];
		//$product_group_code="'".implode("','", $product_group_code)."'";
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$allocation_date =$year.'-'.$month.'-'.$date;
		$prod_code=$_POST['prod_code'];
		$emp_code=$_POST['emp_code'];
		$allocated_qty=$_POST['allocated_qty'];
		for($i=0;$i<count($prod_code);$i++)
		{		
		$sqlinsertvanstock="INSERT INTO van_stock_allocation 
							SET emp_code='".$emp_code."',
							prod_code='".$prod_code[$i]."',
							allocation_date='".$allocation_date."',
							allocated_qty='".$allocated_qty[$i]."',
							 balance_qty='".$allocated_qty[$i]."',
							 acedns='Y',
							download_time=CURRENT_TIMESTAMP";
		mysqli_query($link,$sqlinsertvanstock);
		}
			/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,branch_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(".$branch_code.")";
			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
			{
				$distinct_branch_code=$rowselectdistinctdnsprod['branch_code'];
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
				$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
				$distinct_plant_name=$rowdistinctplant['plant_name'];
				
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
									dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

				${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
				${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];
				
				$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
				$rschkformulation=mysqli_query($link,$sqlchkformulation);
				$rowchkformulation=mysqli_fetch_assoc($rschkformulation);
				$is_formulation=$rowchkformulation['formulation'];

				$depot_cost_case_prodwise=$depot_cost/${conversion_factor_two.$distinct_dnsprod_code};
				$depot_cost_case_prodwise=round(($depot_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
		
				$sqlinsertdeptcost="INSERT INTO depot_cost 
									SET dns_prod_code='".$distinct_dnsprod_code."',
									branch_code='".$distinct_branch_code."',
									depot_cost='".$depot_cost_case_prodwise."',
									depot_cost_ton='".$depot_cost."',
									 vertical_value='".$_SESSION['vertical_value']."',
									 ip_address='".$_SERVER['REMOTE_ADDR']."',
									datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertdeptcost);
				/*$sqlinsertdeptcostlog="INSERT INTO depot_cost_log 
									SET dns_prod_code='".$distinct_dnsprod_code."',
									branch_code='".$distinct_branch_code."',
									depot_cost='".$depot_cost."',
									 vertical_value='".$_SESSION['vertical_value']."',
									 ip_address='".$_SERVER['REMOTE_ADDR']."',
									datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertdeptcostlog);*/
				/*if($is_formulation=='yes')
				{
					$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
											product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
					$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
				}
				else
				{
					$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
											product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
					$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
				}
				if($cntchkpricegeneration > 0)
				{
					generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
				}
			}*/
		$successval=1;
		if($successval==1)
		{ 
		?><script language="JavaScript" type="text/javascript">alert('Van stock allocation successful.');window.location.href='van_stock_allocation.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Van stock allocation unsuccessful.');window.location.href='van_stock_allocation.php';</script>
		<?php
		}
	}
}
?>