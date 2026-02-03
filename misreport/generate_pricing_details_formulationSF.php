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
     <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
</head>

<center>
<br /><br />
<table cellpadding="4px" width="70%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate Pricing Details</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_pricing_details_formulationSF.php" name="pricing_details" onSubmit="return validation();" method="post">
<input type="hidden" name="pricing_mode" value="create_pricing" />
<table cellpadding="4px">
    <tr>
        <td align="left">Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" onchange="javascript:check_formulation();">
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
        
        </td>
    </tr>
    
    <tr>
        <td>
            <table id="displayformulation" align="center"></table>
         </td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Generate Price List"/><!--&nbsp;&nbsp;<input name="submit1" type="button" value="Generate Price List"/>!--></td>
    </tr>
</table>
</form>
</td></tr></table><br /><br /><br />
</center>
   

<script>
function validation()
{
	if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}
	
	/*if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}*/
	
	/*if(document.getElementById("loose_rate").value.search(/\S/) == -1)
	{
		alert('Input Loose Rate');
		return false;
	}*/
	var x = document.getElementsByName("loose_rate[]");
	var i;
	var flag=0;
	for (i = 0; i < x.length; i++) {
		//alert(x[i].value);
		
	  if ((x[i].value.search(/\S/) == -1) || x[i].value <0) {
		 //alert(x[i].value);
		 alert("All Oils value should be 0 OR Greater than 0");
		 return false;
	  }
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
function check_formulation()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	if(document.getElementById("plant_name").value.search(/\S/) == -1){
		alert("Select Plant");
		return false;
	}
	var plant_name = document.getElementById("plant_name").value;
	
	var url="show_formulation_data_SF.php?plant_name="+plant_name;
	xmlHttp.onreadystatechange=showformulation;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function showformulation()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("displayformulation").innerHTML = val;
		 }
	}
 }
</script>
<?php
	if($_REQUEST['pricing_mode']=='create_pricing')
	{
		$plant_name=$_POST['plant_name'];
		$loose_rate_ton=$_POST['loose_rate'];
		$oils_val=$_POST['oils_val'];
		$count=1;
		$tabledataval='';
		$tabledata='<form name="create_price" method="post" action="generate_pricing_details_formulationSF.php"><input type="hidden" name="mode" value="submit_pricing"><input type="hidden" name="plant_name" value="'.$plant_name.'"><input type="hidden" name="loose_rate_ton" value="'.$loose_rate_ton.'"><table class="border" width="90%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
				  <tr>
					<td colspan="11" class="TDHEAD" align="left">Product Sale Rate - '.$product_group_name.'</td>
				  </tr>
				  <tr class="TDHEAD_SUB">
					<td>SI</td>
					<td>Depot</td>
					<td>Product Description</td>
					<td>Material Cost</td>
					<td>Freight</td>
					<td>Packing Cost</td>
					<td>Depot Cost</td>
					<td>Margin</td>
					<td>Honeycomb Cost</td>
					<td>Detention Cost</td>
					<td>Sale Rate</td>
				  </tr>';
				  $formulation_prod_array=array();
			for($i=0;$i<count($oils_val);$i++ )
			{
				$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
										WHERE  plant_name='".$plant_name."' 
										AND oils='".$oils_val[$i]."' AND acedns='Y' AND prod_code 
										IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y' AND 
										vertical_value='".$_SESSION['vertical_value']."')ORDER BY datetime DESC) AS SAT GROUP BY 1 ";
				$rsprodwiseformulation=mysqli_query($link,$sqlprodwiseformulation);
				while($rowprodwiseformulation=mysqli_fetch_assoc($rsprodwiseformulation))
				{	/*echo 	$rowprodwiseformulation['prod_code'];
					echo '<br />';
					echo 'loose rate input-'.$loose_rate_ton[$i];
					echo '<br />';
					echo 'formulation-'.$rowprodwiseformulation['formulation'];
					echo '<br />';*/			
					 ${loosrate_calc_val.$rowprodwiseformulation['prod_code']}=(substr($rowprodwiseformulation['formulation'],0,-1)
					*$loose_rate_ton[$i])/100;
					
					${loosrate_final_val.$rowprodwiseformulation['prod_code']}=${loosrate_final_val.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val.$rowprodwiseformulation['prod_code']};
					if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
					{
						array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
					}
				}
			$tabledataval.="<input type=\"hidden\" name=\"oils_val_array[]\" value=\"$oils_val[$i]\">
							<input type=\"hidden\" name=\"oils_rate_array[]\" value=\"$loose_rate_ton[$i]\">";
			}
			//echo 'process cost -<br />';
			foreach($formulation_prod_array as $formulation_prod_val)
			{
				//echo $formulation_prod_val.'<br />';
				$sqlprocesscost="SELECT process_cost FROM process_cost WHERE dns_prod_code='".$formulation_prod_val."' 
								AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rsprocesscost=mysqli_query($link,$sqlprocesscost);
				$rowprocesscost=mysqli_fetch_assoc($rsprocesscost);
				${process_cost.$formulation_prod_val}=$rowprocesscost['process_cost'];
				if(${process_cost.$formulation_prod_val}=='') ${process_cost.$formulation_prod_val}=0;
				${loosrate_final_val.$formulation_prod_val};
				//echo 'final loose rate  - <br />';
				${loosrate_total.$formulation_prod_val}=${loosrate_final_val.$formulation_prod_val}+${process_cost.$formulation_prod_val};
				//${loosrate_total.$product_group_code}=${loosrate_total.$product_group_code}+${loosrate_total.$formulation_prod_val};
			}
			//For product group wise all product data mrp updation on the basis of Loose rate
			$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE vertical_value='".$_SESSION['vertical_value']."' 
			AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
			$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
			while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
			{
				$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
				$sqlbranchname="SELECT branch_name,branch_state FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsbranchname=mysqli_query($link,$sqlbranchname);
				$rowbranchname=mysqli_fetch_assoc($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
				
				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
											    prod_desc NOT LIKE '%LUP%' 
											AND acedns='Y' AND black_list='N' AND vertical_value='".$_SESSION['vertical_value']."' 
											AND dns_prod_code IN(SELECT DISTINCT prod_code FROM loose_oilrate_formulation WHERE acedns='Y') AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
				$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
				while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
				{
					$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
					$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,branch_code FROM product_master WHERE 
										dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."' AND acedns='Y'";
					$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
					$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
					${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
					${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
					${prod_desc.$distinct_dnsprod_code}=$rowconversionfactor['prod_desc'];
					${distinct_branch_code.$distinct_dnsprod_code}=$rowconversionfactor['branch_code'];
					if(${prod_desc.$distinct_dnsprod_code}!=''){
					$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
					$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
					${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
					${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
					
					$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
					$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
					${depot_cost.$distinct_dnsprod_code}=$rowdepotcostprodwise['depot_cost'];
					if(${depot_cost.$distinct_dnsprod_code}=='')
					{
						${depot_cost.$distinct_dnsprod_code}=0;
					}
					
					${margin_cost.$distinct_dnsprod_code}=0;
					
					$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
					${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
					if(${freight_cost.$distinct_dnsprod_code}=='')
					{
						${freight_cost.$distinct_dnsprod_code}=0;
					}
					
					${honeycomb_cost.$distinct_dnsprod_code}=0;
					
					$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdetentioncostprodwise=mysqli_query($link,$sqldetentioncostprodwise);
					$rowdetentioncostprodwise=mysqli_fetch_assoc($rsdetentioncostprodwise);
					${detention_cost.$distinct_dnsprod_code}=$rowdetentioncostprodwise['detention_cost'];
					if(${detention_cost.$distinct_dnsprod_code}=='')
					{
						${detention_cost.$distinct_dnsprod_code}=0;
					}
					
					//echo ${loosrate_total.$distinct_dnsprod_code};
					${loose_rate_case_prodwise.$distinct_dnsprod_code}=round((${loosrate_total.$distinct_dnsprod_code}/${conversion_factor_two.$distinct_dnsprod_code}),2);
					${loose_rate_case_prodwise.$distinct_dnsprod_code}=round((${loose_rate_case_prodwise.$distinct_dnsprod_code}*${conversion_factor.$distinct_dnsprod_code}),2);
					//${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${honeycomb_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
					${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
					${mrp_prodwise.$distinct_dnsprod_code}=round(${mrp_prodwise.$distinct_dnsprod_code},2);
					
					//${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code};
					${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
					${basic_rate_prodwise.$distinct_dnsprod_code}=round(${basic_rate_prodwise.$distinct_dnsprod_code},2);
					$primary_freight=round(${freight_cost.$distinct_dnsprod_code},2);
					$depot_cost=${depot_cost.$distinct_dnsprod_code};
					//exit();
					if(${loose_rate_case_prodwise.$distinct_dnsprod_code} >0){
					$tabledataval.="<tr id=\"tab\">
										<td>".$count."</td>
										<td>".$branch_name."</td>
										<td>".${prod_desc.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".${loose_rate_case_prodwise.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".round(${freight_cost.$distinct_dnsprod_code},2)."</td>
										<td align=\"right\">".${packing_cost.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".${depot_cost.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".${margin_cost.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".${honeycomb_cost.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".${detention_cost.$distinct_dnsprod_code}."</td>
										<td align=\"right\">".${mrp_prodwise.$distinct_dnsprod_code}."</td>
									</tr>";
						$count++;
					 }
					}
				}
			}
		echo $tabledata.=$tabledataval."<tr><td colspan='6' align='right'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit5\" value=\"Issued to Release\" /></td><td colspan='5' align='left'><input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/generate_pricing_details_formulationSF.php'\"/></td></tr></table></form>";
	}
	if($_REQUEST['mode']=='submit_pricing')
	{
		$distinct_plant_name=$_POST['plant_name'];
		$loose_rate_ton=$_POST['loose_rate_ton'];
		$oils_val=$_POST['oils_val_array'];
	    $oils_rate=$_POST['oils_rate_array'];
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$price_generation_id='PG'.$_SESSION['admin_login'].$year.$month.$date.$hour.$minute.$second;
			
		for($k=0;$k<count($oils_val);$k++){
			$sqlinsertpricingdetails="INSERT INTO pricing_detials_formulation 
									  SET plant_name='".$distinct_plant_name."',
									  product_group_code='',
									  price_generation_id='".$price_generation_id."',
									  oils='".$oils_val[$k]."',
									  oils_rate='".$oils_rate[$k]."',
									  datetime=CURRENT_TIMESTAMP,
									  vertical_value='".$_SESSION['vertical_value']."',
									  user_login='".$_SESSION['admin_login']."',
									  user_ip='".$_SERVER['REMOTE_ADDR']."'";
			$rsinsertpricingdetails=mysqli_query($link,$sqlinsertpricingdetails);
		}
		$flag=1;
		//End For product group wise all product data mrp updation on the basis of Loose rate
		if($flag==1){?>
		   <script language="JavaScript" type="text/javascript">alert('Issued to release has been performed sucessfully.');window.location.href='depotwise_pricelist_report_verticalwise.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Issued to release unsuccessful.');window.location.href='generate_pricing_details_formulationSF.php';</script>
		<?php 
		}
	}
	if($_REQUEST['mode']=='setpricezero')
	{
		$sqlinsertpricingdetails="INSERT INTO pricing_detials 
								  SET loose_rate_ton='0',
								  plant_name='".$_REQUEST['plant_name']."',
								  product_group_code='',
								  datetime=CURRENT_TIMESTAMP,
								  price_generated='yes',
								   vertical_value='".$_SESSION['vertical_value']."'
								  user_login='".$_SESSION['admin_login']."',
								  user_ip='".$_SERVER['REMOTE_ADDR']."'";
		$rsinsertpricingdetails=mysqli_query($link,$sqlinsertpricingdetails);
		$sqlupdatemrp="UPDATE sauda_mrp  SET mrp='0',sale_rate='0',
									basic_rate='0',download_time=CURRENT_TIMESTAMP()
								WHERE product_code IN(SELECT prod_code FROM product_master WHERE 
								vertical_value='".$_SESSION['vertical_value']."' AND acedns='Y' AND branch_code 
								IN(SELECT branch_code FROM branch_master WHERE plant_name='".$_REQUEST['plant_name']."' AND acedns='Y')) ";
	  mysqli_query($link,$sqlupdatemrp);
	  ?>
      <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='product_groupwise_pricelist_new.php?product_group_code=''&plant_name=<?php echo $_REQUEST['plant_name'];?>&formulation=<?php echo $_REQUEST['formulation'];?>';</script>
      <?php							
	}
}
?>