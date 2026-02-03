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
<form action="generate_pricing_details_formulationmodified.php" name="pricing_details" onSubmit="return validation();" method="post">
<input type="hidden" name="pricing_mode" value="create_pricing" />
<table cellpadding="4px">
    <tr>
        <td align="left">Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name">
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
        
        Select Oil Group:
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code" onchange="javascript:check_formulation();">
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE is_upload='no' 
								AND vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
			$rsproductgroup=mysql_query($sqlproductgroup);
			while($rowproductgroup=mysql_fetch_array($rsproductgroup))
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
	
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
	
	/*if(document.getElementById("loose_rate").value.search(/\S/) == -1)
	{
		alert('Input Loose Rate');
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
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
	var plant_name = document.getElementById("plant_name").value;
	var product_group_code = document.getElementById("product_group_code").value;
	
	var url="show_formulation_data.php?plant_name="+plant_name+"&product_group_code="+product_group_code;
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
		$product_group_code=$_POST['product_group_code'];
		$loose_rate_ton=$_POST['loose_rate'];
		$formulation=$_POST['formulation'];
		$formulation_percent=$_POST['formulation_percent'];
		$oils_val=$_POST['oils_val'];
		if($loose_rate_ton==0)
		{
		?>	
			 <script language="JavaScript" type="text/javascript">alert('Price will be zero for this oil group');window.location.href='generate_pricing_details_formulationmodified.php?product_group_code=<?php echo $product_group_code;?>&plant_name=<?php echo $plant_name;?>&formulation=<?php echo $formulation;?>&mode=setpricezero';</script>
        <?php     
		}
		//print_r($loose_rate_ton);
		//Starting for Formulation=yes
		$current_date=date('Y-m-d');
		$sqlproductgroupname="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$product_group_code."'";
		$rsproductgroupname=mysql_query($sqlproductgroupname);
		$rowproductgroupname=mysql_fetch_array($rsproductgroupname);
		$product_group_name=$rowproductgroupname['product_group_name'];
		$count=1;
		$tabledataval='';
		$tabledata='<form name="create_price" method="post" action="generate_pricing_details_formulationmodified.php"><input type="hidden" name="mode" value="submit_pricing"><input type="hidden" name="plant_name" value="'.$plant_name.'"><input type="hidden" name="product_group_code" 
		value="'.$product_group_code.'"><input type="hidden" name="loose_rate_ton" value="'.$loose_rate_ton.'"><input type="hidden" name="formulation" value="'.$formulation.'"><table class="border" width="90%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
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
		if($formulation=='yes'){
			$formulation_prod_array=array();
			$formulation_percent=substr($formulation_percent,0,-1);
			$formulation_percent_array=explode(',',$formulation_percent);
			for($i=0;$i<count($oils_val);$i++ )
			{
				$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
										WHERE product_group_code='".$product_group_code."' AND plant_name='".$plant_name."' 
										AND oils='".$oils_val[$i]."' ORDER BY datetime DESC) AS SAT GROUP BY 1 ";
				$rsprodwiseformulation=mysql_query($sqlprodwiseformulation);
				while($rowprodwiseformulation=mysql_fetch_array($rsprodwiseformulation))
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
			/*$sqlinsertpricingdetailsformulation="INSERT INTO pricing_detials_formulation 
										  SET plant_name='".$plant_name."',
										  product_group_code='".$product_group_code."',
										  oils='".$oils_val[$i]."',
										  oils_rate='".$loose_rate_ton[$i]."',
										  datetime=CURRENT_TIMESTAMP,
										  user_login='".$_SESSION['admin_login']."',
										  user_ip='".$_SERVER['REMOTE_ADDR']."'";
			$rsinsertpricingdetailsformulation=mysql_query($sqlinsertpricingdetailsformulation);*/
			$tabledataval.="<input type=\"hidden\" name=\"oils_val_array[]\" value=\"$oils_val[$i]\">
							<input type=\"hidden\" name=\"oils_rate_array[]\" value=\"$loose_rate_ton[$i]\">";
			}
			//echo 'process cost -<br />';
			foreach($formulation_prod_array as $formulation_prod_val)
			{
				//echo $formulation_prod_val.'<br />';
				$sqlprocesscost="SELECT process_cost FROM process_cost WHERE dns_prod_code='".$formulation_prod_val."' 
								AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rsprocesscost=mysql_query($sqlprocesscost);
				$rowprocesscost=mysql_fetch_array($rsprocesscost);
				${process_cost.$formulation_prod_val}=$rowprocesscost['process_cost'];
				if(${process_cost.$formulation_prod_val}=='') ${process_cost.$formulation_prod_val}=0;
				${loosrate_final_val.$formulation_prod_val};
				//echo 'final loose rate  - <br />';
				${loosrate_total.$formulation_prod_val}=${loosrate_final_val.$formulation_prod_val}+${process_cost.$formulation_prod_val};
				//${loosrate_total.$product_group_code}=${loosrate_total.$product_group_code}+${loosrate_total.$formulation_prod_val};
			}

			//For product group wise all product data mrp updation on the basis of Loose rate
			$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."' 
									AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
			$rsselectdistinctbranch=mysql_query($sqlselectdistinctbranch);
			while($rowselectdistinctbranch=mysql_fetch_array($rsselectdistinctbranch))
			{
				$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
				$sqlbranchname="SELECT branch_name,branch_state FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
				
				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
											product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
											AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
				$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
				while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
				{
					$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
					$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,branch_code FROM product_master WHERE 
										dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."' 
										AND product_group_code='".$product_group_code."' AND acedns='Y'";
					$rsconversionfactor=mysql_query($sqlconversionfactor);
					$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
					${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
					${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
					${prod_desc.$distinct_dnsprod_code}=$rowconversionfactor['prod_desc'];
					${distinct_branch_code.$distinct_dnsprod_code}=$rowconversionfactor['branch_code'];
					if(${prod_desc.$distinct_dnsprod_code}!=''){
					$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingprodwise=mysql_query($sqlpackingprodwise);
					$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
					${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
					${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
					
					$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
					$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
					${depot_cost.$distinct_dnsprod_code}=$rowdepotcostprodwise['depot_cost'];
					if(${depot_cost.$distinct_dnsprod_code}=='')
					{
						${depot_cost.$distinct_dnsprod_code}=0;
					}
					
					/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
					$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
					${margin_cost.$distinct_dnsprod_code}=$rowmargincostprodwise['margin_cost'];
					if(${margin_cost.$distinct_dnsprod_code}=='')
					{
						${margin_cost.$distinct_dnsprod_code}=0;
					}*/
					${margin_cost.$distinct_dnsprod_code}=0;
					
					$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
					${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
					if(${freight_cost.$distinct_dnsprod_code}=='')
					{
						${freight_cost.$distinct_dnsprod_code}=0;
					}
					
					/*$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rshoneycombcostprodwise=mysql_query($sqlhoneycombcostprodwise);
					$rowhoneycombcostprodwise=mysql_fetch_array($rshoneycombcostprodwise);
					${honeycomb_cost.$distinct_dnsprod_code}=$rowhoneycombcostprodwise['honeycomb_cost'];
					if(${honeycomb_cost.$distinct_dnsprod_code}=='')
					{
						${honeycomb_cost.$distinct_dnsprod_code}=0;
					}*/
					${honeycomb_cost.$distinct_dnsprod_code}=0;
					
					$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdetentioncostprodwise=mysql_query($sqldetentioncostprodwise);
					$rowdetentioncostprodwise=mysql_fetch_array($rsdetentioncostprodwise);
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
					/*$tabledataval.="<input type=\"hidden\" name=\"branch_code_array[]\" value=\"${distinct_branch_code.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
									<input type=\"hidden\" name=\"prod_desc_array[]\" value=\"${prod_desc.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"material_cost_array[]\" value=\"${loose_rate_case_prodwise.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"freight_cost_array[]\" value=\"${freight_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"packing_cost_array[]\" value=\"${packing_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"depot_cost_array[]\" value=\"${depot_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"margin_cost_array[]\" value=\"${margin_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"honeycomb_cost_array[]\" value=\"${honeycomb_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"detention_cost_array[]\" value=\"${detention_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"basic_rate_array[]\" value=\"${basic_rate_prodwise.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"sale_rate_array[]\" value=\"${mrp_prodwise.$distinct_dnsprod_code}\">";*/
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
		}//End of  Formulation=yes
		else // starting for Formulation=no
		{
		 if($product_group_code !='BR1')
		 {	
			//For product group wise all product data mrp updation on the basis of Loose rate
			$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."' 
									AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
			$rsselectdistinctbranch=mysql_query($sqlselectdistinctbranch);
			while($rowselectdistinctbranch=mysql_fetch_array($rsselectdistinctbranch))
			{
				$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
				//$tabledataval.="<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">";
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsbranchname=mysql_query($sqlbranchname);
				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];

				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
											product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
											AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
				/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
											product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
											AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."'";	*/						
				$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
				while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
				{
					$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
					$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,branch_code FROM product_master WHERE 
										dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."' 
										AND product_group_code='".$product_group_code."' AND acedns='Y'";
					$rsconversionfactor=mysql_query($sqlconversionfactor);
					$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
					${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
					${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
					${prod_desc.$distinct_dnsprod_code}=$rowconversionfactor['prod_desc'];
					${branch_code.$distinct_dnsprod_code}=$rowconversionfactor['branch_code'];

					$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' 
										AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingprodwise=mysql_query($sqlpackingprodwise);
					$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
					${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
					${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
					
					$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
					$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
					${depot_cost.$distinct_dnsprod_code}=$rowdepotcostprodwise['depot_cost'];
					if(${depot_cost.$distinct_dnsprod_code}=='')
					{
						${depot_cost.$distinct_dnsprod_code}=0;
					}
					
					/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
					$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
					${margin_cost.$distinct_dnsprod_code}=$rowmargincostprodwise['margin_cost'];
					if(${margin_cost.$distinct_dnsprod_code}=='')
					{
						${margin_cost.$distinct_dnsprod_code}=0;
					}*/
					${margin_cost.$distinct_dnsprod_code}=0;
					
					$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
					${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
					if(${freight_cost.$distinct_dnsprod_code}=='')
					{
						${freight_cost.$distinct_dnsprod_code}=0;
					}
					
					/*$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rshoneycombcostprodwise=mysql_query($sqlhoneycombcostprodwise);
					$rowhoneycombcostprodwise=mysql_fetch_array($rshoneycombcostprodwise);
					${honeycomb_cost.$distinct_dnsprod_code}=$rowhoneycombcostprodwise['honeycomb_cost'];
					if(${honeycomb_cost.$distinct_dnsprod_code}=='')
					{
						${honeycomb_cost.$distinct_dnsprod_code}=0;
					}*/
					${honeycomb_cost.$distinct_dnsprod_code}=0;
					
					$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdetentioncostprodwise=mysql_query($sqldetentioncostprodwise);
					$rowdetentioncostprodwise=mysql_fetch_array($rsdetentioncostprodwise);
					${detention_cost.$distinct_dnsprod_code}=$rowdetentioncostprodwise['detention_cost'];
					if(${detention_cost.$distinct_dnsprod_code}=='')
					{
						${detention_cost.$distinct_dnsprod_code}=0;
					}
					${loose_rate_case_prodwise.$distinct_dnsprod_code}=round(($loose_rate_ton/${conversion_factor_two.$distinct_dnsprod_code}),2);
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
					/*$tabledataval.="<input type=\"hidden\" name=\"branch_code_array[]\" value=\"${branch_code.$distinct_dnsprod_code}\">";
					$tabledataval.="<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
									<input type=\"hidden\" name=\"prod_desc_array[]\" value=\"${prod_desc.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"material_cost_array[]\" value=\"${loose_rate_case_prodwise.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"freight_cost_array[]\" value=\"${freight_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"packing_cost_array[]\" value=\"${packing_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"depot_cost_array[]\" value=\"${depot_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"margin_cost_array[]\" value=\"${margin_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"honeycomb_cost_array[]\" value=\"${honeycomb_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"detention_cost_array[]\" value=\"${detention_cost.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"basic_rate_array[]\" value=\"${basic_rate_prodwise.$distinct_dnsprod_code}\">
									<input type=\"hidden\" name=\"sale_rate_array[]\" value=\"${mrp_prodwise.$distinct_dnsprod_code}\">";*/
					if(${prod_desc.$distinct_dnsprod_code}!=''){				
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
		  else
		  {
			  ?>
				 <script language="JavaScript" type="text/javascript">alert('Price calculation for MUSTARD is under progress.');window.location.href='generate_pricing_details_formulation.php';</script>
			  <?php 
		  }
		}
		echo $tabledata.=$tabledataval."<tr><td colspan='6' align='right'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit5\" value=\"Issued to Release\" /></td><td colspan='5' align='left'><input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/generate_pricing_details_formulationmodified.php'\"/></td></tr></table></form>";
	}
	if($_REQUEST['mode']=='submit_pricing')
	{
		//print_r($_POST);
		//exit();
		$distinct_plant_name=$_POST['plant_name'];
		$product_group_code=$_POST['product_group_code'];
		$loose_rate_ton=$_POST['loose_rate_ton'];
		$formulation=$_POST['formulation'];
		/*$distinct_branch_code=$_POST['branch_code_array'];
		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		$prod_desc=$_POST['prod_desc_array'];
		$material_cost=$_POST['material_cost_array'];
		$freight_cost=$_POST['freight_cost_array'];
		$packing_cost=$_POST['packing_cost_array'];
		$depot_cost=$_POST['depot_cost_array'];
		$margin_cost=$_POST['margin_cost_array'];
		$honeycomb_cost=$_POST['honeycomb_cost_array'];
		$detention_cost=$_POST['detention_cost_array'];
		$basic_rate=$_POST['basic_rate_array'];
		$sale_rate=$_POST['sale_rate_array'];*/
		
		//print_r($distinct_branch_code);
		//print_r($distinct_dnsprod_code);
		//exit();
		if($formulation=='yes'){
			$oils_val=$_POST['oils_val_array'];
			$oils_rate=$_POST['oils_rate_array'];
			for($k=0;$k<count($oils_val);$k++){
				$sqlinsertpricingdetails="INSERT INTO pricing_detials_formulation 
										  SET plant_name='".$distinct_plant_name."',
										  product_group_code='".$product_group_code."',
										  oils='".$oils_val[$k]."',
										  oils_rate='".$oils_rate[$k]."',
										  datetime=CURRENT_TIMESTAMP,
										  user_login='".$_SESSION['admin_login']."',
										  vertical_value='".$_SESSION['vertical_value']."',
										  user_ip='".$_SERVER['REMOTE_ADDR']."'";
				$rsinsertpricingdetails=mysql_query($sqlinsertpricingdetails);
			}
		}
		else
		{
			$sqlinsertpricingdetails="INSERT INTO pricing_detials 
								  SET loose_rate_ton='".$loose_rate_ton."',
								  plant_name='".$distinct_plant_name."',
								  product_group_code='".$product_group_code."',
								  datetime=CURRENT_TIMESTAMP,
								  user_login='".$_SESSION['admin_login']."',
								  user_ip='".$_SERVER['REMOTE_ADDR']."'";
			$rsinsertpricingdetails=mysql_query($sqlinsertpricingdetails);
		}
		$flag=1;
		/*for($i=0;$i<count($distinct_dnsprod_code);$i++)
		{	
		$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code[$i]."' AND dns_prod_code='".$distinct_dnsprod_code[$i]."' AND acedns='Y' AND black_list='N'";
		$rs_prod_code=mysql_query($sql_prod_code);
		$cntprod_code=mysql_num_rows($rs_prod_code);
		$row_prod_code=mysql_fetch_array($rs_prod_code);
		$prod_code_master=$row_prod_code['prod_code'];
		$vertical_value_master=$row_prod_code['vertical_value'];
		if($cntprod_code >0)
		{
			$sqlbranchprodchk="SELECT product_code FROM sauda_mrp WHERE branch_code='".$distinct_branch_code[$i]."' AND 
								product_code='".$prod_code_master."'";
			$rsbranchprodchk=mysql_query($sqlbranchprodchk);
			$cntbranchprodchk=mysql_num_rows($rsbranchprodchk);
			if($cntbranchprodchk >0)						{
				$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='".$sale_rate[$i]."',sale_rate='".$sale_rate[$i]."',
									basic_rate='".$basic_rate[$i]."',primary_freight	='".$freight_cost[$i]."',
									depot_cost='".$depot_cost[$i]."',download_time=CURRENT_TIMESTAMP()
								WHERE branch_code='".$distinct_branch_code[$i]."' AND product_code='".$prod_code_master."'";
				mysql_query($sqlupdatemrpprodwise);
			}
			else
			{
				$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from sauda_mrp";
				$rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
				$rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
				$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
				$max_mrp_code++;
				$max_mrp_code='z'.$max_mrp_code;

			  $sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',mrp='".$sale_rate[$i]."',sale_rate='".$sale_rate[$i]."',
										branch_code='".$distinct_branch_code[$i]."',product_code='".$prod_code_master."',
										vertical_value='".$vertical_value_master."',basic_rate='".$basic_rate[$i]."',primary_freight	='".$freight_cost[$i]."',depot_cost='".$depot_cost[$i]."',download_time=CURRENT_TIMESTAMP()";
			   mysql_query($sqlinsertmrpprodwise);
			}
		}
		$flag=1;
		}*/
		//End For product group wise all product data mrp updation on the basis of Loose rate
		if($flag==1){?>
		   <script language="JavaScript" type="text/javascript">alert('Issued to release has been performed sucessfully.');window.location.href='depotwise_pricelist_report_verticalwise.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Issued to release unsuccessful.');window.
			location.href='generate_pricing_details_formulationmodified.php';</script>
		<?php
		}
	}
	if($_REQUEST['mode']=='setpricezero')
	{
		$sqlinsertpricingdetails="INSERT INTO pricing_detials 
								  SET loose_rate_ton='0',
								  plant_name='".$_REQUEST['plant_name']."',
								  product_group_code='".$_REQUEST['product_group_code']."',
								  datetime=CURRENT_TIMESTAMP,
								  price_generated='yes',
								  user_login='".$_SESSION['admin_login']."',
								  user_ip='".$_SERVER['REMOTE_ADDR']."'";
		$rsinsertpricingdetails=mysql_query($sqlinsertpricingdetails);
		$sqlupdatemrp="UPDATE sauda_mrp  SET mrp='0',sale_rate='0',
									basic_rate='0',download_time=CURRENT_TIMESTAMP()
								WHERE product_code IN(SELECT prod_code FROM product_master WHERE 
								product_group_code='".$_REQUEST['product_group_code']."' AND acedns='Y' AND branch_code 
								IN(SELECT branch_code FROM branch_master WHERE plant_name='".$_REQUEST['plant_name']."' AND acedns='Y')) ";
	  mysql_query($sqlupdatemrp);
	  ?>
      <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='product_groupwise_pricelist_new.php?product_group_code=<?php echo $_REQUEST['product_group_code'];?>&plant_name=<?php echo $_REQUEST['plant_name'];?>&formulation=<?php echo $_REQUEST['formulation'];?>';</script>
      <?php							
	}
}
?>