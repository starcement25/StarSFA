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
<?php if($_REQUEST['pricing_mode']=='' && $_REQUEST['mode']=='')
{?>
<table cellpadding="4px" width="40%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate Pricing Details</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_pricing_formulationwise.php" name="pricing_details" onSubmit="return validation();" method="post">
<input type="hidden" name="pricing_mode" value="create_pricing" />
<table cellpadding="4px">
    <tr>
        <td align="left">Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name" >
        	<!--option value="" selected>Select</option-->
            <?php 
			$sqlplant="SELECT branch_name FROM branch_master WHERE is_plant='yes' ORDER BY branch_name ASC";
			$rsplant=mysqli_query($link,$sqlplant);
			while($rowplant=mysqli_fetch_assoc($rsplant))
			{
			?>
            <option value="<?php echo $rowplant['branch_name'];?>" <?php if($plant_name==$rowplant['branch_name']){?>selected<?php }?>><?php echo $rowplant['branch_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td>
            <table id="displayformulation" align="center">
    <?php
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$current_date=$year.'-'.$month.'-'.$date;
		$sqlselectformulation="SELECT * FROM (SELECT DISTINCT oils FROM loose_oilrate_formulation WHERE plant_name=
						(SELECT branch_name FROM branch_master WHERE is_plant='yes' AND acedns='Y') AND base_oil='Y' AND acedns='Y' 
						AND prod_code 
						IN(SELECT DISTINCT dns_prod_code FROM product_master) ORDER BY datetime DESC) AS SAT GROUP BY 1 ";						
		$rsselectformulation=mysqli_query($link,$sqlselectformulation);
		while($rowselectformulation=mysqli_fetch_assoc($rsselectformulation))
		{
			$oils=$rowselectformulation['oils'];
			/*if($oils=='CPO Declared')
			{
				$sqlcrudeoilrate="SELECT crude_oil_rate FROM crude_oil_rate WHERE SUBSTRING(create_date,1,10)='".$current_date."' 
									ORDER BY create_date  DESC LIMIT 0,1";
				$rscrudeoilrate=mysqli_query($link,$sqlcrudeoilrate);
				$rowcrudeoilrate=mysqli_fetch_assoc($rscrudeoilrate);
				
				$text_value=$rowcrudeoilrate['crude_oil_rate'];
			}
			else
			{
				$text_value='';
			}*/
			$sqlprevoilrate="SELECT oils_rate FROM pricing_detials_formulation WHERE oils='".$oils."' ORDER BY datetime DESC LIMIT 0,1";
			$rsprevoilrate=mysqli_query($link,$sqlprevoilrate);
			$rowrsprevoilrate=mysqli_fetch_assoc($rsprevoilrate);
			$text_value=$rowrsprevoilrate['oils_rate'];
				
			//$text_value=$rowcrudeoilrate['crude_oil_rate'];
			$tabledata.='<tr><td align="left">'.$oils.'(MT):</td><td align="left"><input type="text" name="loose_rate[]"  id="loose_rate_"'.$oils.'" style="height:20px;" value="'.$text_value.'"/><input type="hidden" name="oils_val[]" value="'.$oils.'" /></td></tr>';
		}
		echo $tabledata;
	?></table>
       </td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Generate Price List"/><!--&nbsp;&nbsp;<input name="submit1" type="button" value="Generate Price List"/>!--></td>
    </tr>
</table>
</form>
</td></tr></table><br /><br /><br />
<?php }?>
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
	
	var url="show_formulation_details.php?plant_name="+plant_name;
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
		$tabledata='<form name="create_price" method="post" action="generate_pricing_formulationwise.php">
						<input type="hidden" name="mode" value="submit_pricing">
						<input type="hidden" name="plant_name" value="'.$plant_name.'">
						<input type="hidden" name="loose_rate_ton" value="'.$loose_rate_ton.'">
						<table class="border" width="70%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
						  <tr>
							<td colspan="10" class="TDHEAD" align="left">Base Product Sale Rate </td>
						  </tr>
						  <tr class="TDHEAD_SUB">
							<td>SI</td>
							<td>Product Code</td>
							<td>Product Description</td>
							<td>Material Cost</td>
							<td>Process Cost</td>
							<td>(Packing + Labour) Cost</td>
							<td>Margin</td>
							<td>Bargain Rate</td>
						  </tr>';
		    $formulation_prod_array=array();
			for($i=0;$i<count($oils_val);$i++ )
			{
				/*$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
										WHERE  plant_name='".$plant_name."' 
										AND oils='".$oils_val[$i]."' AND acedns='Y'  AND base_oil='Y' AND prod_code='DV15KT000108') AS SAT GROUP BY 1 ";*/
				$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
										WHERE  plant_name='".$plant_name."' 
										AND oils='".$oils_val[$i]."' AND acedns='Y' AND base_oil='Y' AND prod_code 
										IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y') ORDER BY datetime DESC) AS SAT GROUP BY 1 ";												
				$rsprodwiseformulation=mysqli_query($link,$sqlprodwiseformulation);
				while($rowprodwiseformulation=mysqli_fetch_assoc($rsprodwiseformulation))
				{  /*echo 	$rowprodwiseformulation['prod_code'];
					echo '<br />';
					echo 'loose rate input-'.$loose_rate_ton[$i];
					echo '<br />';
					echo 'formulation-'.$rowprodwiseformulation['formulation'];
					echo '<br />';*/			
					${loosrate_calc_val.$rowprodwiseformulation['prod_code']}=($rowprodwiseformulation['formulation']*$loose_rate_ton[$i])/100;
					${loosrate_final_val.$rowprodwiseformulation['prod_code']}=${loosrate_final_val.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val.$rowprodwiseformulation['prod_code']};
					if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
					{
						array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
					}
				}
			$tabledataval.="<input type=\"hidden\" name=\"oils_val_array[]\" value=\"$oils_val[$i]\">
							<input type=\"hidden\" name=\"oils_rate_array[]\" value=\"$loose_rate_ton[$i]\">";
			}
			//echo ${loosrate_final_val.'DV15KT000108'};
			//echo '<br />';
			foreach($formulation_prod_array as $formulation_prod_val)
			{
				//echo $formulation_prod_val.'<br />';
				//$formulation_prod_val='ASV15KB00007';
				$sqlformulationbaseoilN="SELECT * FROM (SELECT oils,formulation,percentile_calc FROM loose_oilrate_formulation 
										WHERE  plant_name='".$plant_name."' AND acedns='Y' AND base_oil!='Y' AND prod_code='".$formulation_prod_val."' 
										ORDER BY datetime DESC) AS SAT GROUP BY 1 ";
				$rsformulationbaseoilN=mysqli_query($link,$sqlformulationbaseoilN);
				while($rowformulationbaseoilN=mysqli_fetch_assoc($rsformulationbaseoilN))
				{
					$oil_type=$rowformulationbaseoilN['oils'];
					$formulation=$rowformulationbaseoilN['formulation'];
					${percentile_calc.$formulation_prod_val}=$rowformulationbaseoilN['percentile_calc'];
					$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$oil_type."' 
								AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
					$rsprocesscost=mysqli_query($link,$sqlprocesscost);
					$rowprocesscost=mysqli_fetch_assoc($rsprocesscost);
					if(${percentile_calc.$formulation_prod_val}=='N')
					{
						${process_cost_sub_val.$formulation_prod_val}=$rowprocesscost['process_cost'];
					}
					else if(${percentile_calc.$formulation_prod_val}!='N')
					{
						${process_cost_sub_val.$formulation_prod_val}=($rowprocesscost['process_cost']*$formulation)/100;
					}
					${process_cost.$formulation_prod_val}=${process_cost.$formulation_prod_val}+${process_cost_sub_val.$formulation_prod_val};
				}
				if(${process_cost.$formulation_prod_val}=='') ${process_cost.$formulation_prod_val}=0;
				//echo 'final loose rate  - <br />';
				//${loosrate_total.$formulation_prod_val}=${loosrate_final_val.$formulation_prod_val}+${process_cost.$formulation_prod_val};
				/*echo ${process_cost.'DV15KT000108'};
				echo '<br />';
				echo ${loosrate_total.'DV15KT000108'};*/
				//${loosrate_total.$product_group_code}=${loosrate_total.$product_group_code}+${loosrate_total.$formulation_prod_val};
				//For product group wise all product data mrp updation on the basis of Loose rate
				
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,UOM1,pack_size,packing_realization FROM product_master WHERE dns_prod_code='".$formulation_prod_val."'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
				${conversion_factor.$formulation_prod_val}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$formulation_prod_val}=$rowconversionfactor['conversion_factor_two'];
				${prod_desc.$formulation_prod_val}=$rowconversionfactor['prod_desc'];
				${UOM1.$formulation_prod_val}=$rowconversionfactor['UOM1'];
				${pack_size.$formulation_prod_val}=$rowconversionfactor['pack_size'];
				if(${prod_desc.$formulation_prod_val}!=''){
				$sqlpackingprodwise="SELECT packing_cost,labour_cost,extra_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$formulation_prod_val."' 
										AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
				$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
				${labour_cost.$formulation_prod_val}=$rowpackingprodwise['labour_cost'];
				${packing_cost.$formulation_prod_val}=$rowpackingprodwise['packing_cost'];
				${extra_cost.$formulation_prod_val}=$rowpackingprodwise['extra_cost'];
				${packing_realization.$formulation_prod_val}=$rowconversionfactor['packing_realization'];
					
				/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
											AND branch_code='".$distinct_branch_code."' AND 
											vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
				$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
				$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
				${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
				if(${freight_cost.$distinct_dnsprod_code}=='')
				{
					${freight_cost.$distinct_dnsprod_code}=0;
				}
				
				${honeycomb_cost.$distinct_dnsprod_code}=0;*/
					
				$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$formulation_prod_val."'  ORDER BY datetime DESC LIMIT 0,1";
				$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
				$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
				${margin_cost.$formulation_prod_val}=$rowmargincostprodwise['margin_cost'];
				if(${margin_cost.$formulation_prod_val}=='')
				{
					${margin_cost.$formulation_prod_val}=0;
				}
				if(strtoupper(${UOM1.$formulation_prod_val})=='LOOSE')
				{
					${loose_rate_case_prodwise.$formulation_prod_val}=${loosrate_final_val.$formulation_prod_val};
					${process_cost_case.$formulation_prod_val}=${process_cost.$formulation_prod_val};
				}
				else
				{
					//${loose_rate_case_prodwise.$formulation_prod_val}=round((${loosrate_final_val.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val}),2);
					//${process_cost_case.$formulation_prod_val}=round((${process_cost.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val}),2);
					${loose_rate_case_prodwise.$formulation_prod_val}=${loosrate_final_val.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val};
					${process_cost_case.$formulation_prod_val}=${process_cost.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val};
				}
				//${loose_rate_case_prodwise.$formulation_prod_val}=round((${loose_rate_case_prodwise.$formulation_prod_val}*${conversion_factor.$formulation_prod_val}),2);
				${basic_rate_prodwise.$formulation_prod_val}=${loose_rate_case_prodwise.$formulation_prod_val}+${process_cost_case.$formulation_prod_val}+${margin_cost.$formulation_prod_val}+${packing_realization.$formulation_prod_val};
				if(${pack_size.$formulation_prod_val}=='BP')
				{
				 	${basic_rate_prodwise.$formulation_prod_val}=round(${basic_rate_prodwise.$formulation_prod_val},0);
				}
				if(${pack_size.$formulation_prod_val}=='CP')
				{
					${basic_rate_prodwise.$formulation_prod_val}=round(${basic_rate_prodwise.$formulation_prod_val},1);
				}
				else
				{
					${basic_rate_prodwise.$formulation_prod_val}=${basic_rate_prodwise.$formulation_prod_val};
				}
					
					if(${loose_rate_case_prodwise.$formulation_prod_val} >0){
					$tabledataval.="<input type=\"hidden\" name=\"prod_val[]\" value=".$formulation_prod_val.">
									<input type=\"hidden\" name=\"sale_rate[]\" value=".${basic_rate_prodwise.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"loose_rate[]\" value=".${loose_rate_case_prodwise.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"process_cost[]\" value=".${process_cost_case.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"plant_name[]\" value=".$plant_name.">
									<input type=\"hidden\" name=\"conversion_factor[]\" value=".${conversion_factor_two.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"packing_cost[]\" value=".${packing_cost.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"labour_cost[]\" value=".${labour_cost.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"packing_realization[]\" value=".${packing_realization.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"extra_cost[]\" value=".${extra_cost.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"margin_cost[]\" value=".${margin_cost.$formulation_prod_val}.">
									<tr id=\"tab\">
										<td>".$count."</td>
										<td>".$formulation_prod_val."</td>
										<td>".${prod_desc.$formulation_prod_val}."</td>
										<td align=\"right\">".number_format(${loose_rate_case_prodwise.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${process_cost_case.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${packing_realization.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${margin_cost.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${basic_rate_prodwise.$formulation_prod_val},2)."</td>
									</tr>";
						$count++;
					}
				  }
			 }
		echo $tabledata.=$tabledataval."<tr><td colspan='5' align='right'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit5\" value=\"Issued to Release\" /></td><td colspan='5' align='left'><input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/generate_pricing_formulationwise.php'\"/></td></tr></table></form>";
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
		
		$prod_val=$_POST['prod_val'];
		$sale_rate=$_POST['sale_rate'];
		$loose_rate=$_POST['loose_rate'];
		$process_cost=$_POST['process_cost'];
		$packing_cost=$_POST['packing_cost'];
		$labour_cost=$_POST['labour_cost'];
		$extra_cost=$_POST['extra_cost'];
		$margin_cost=$_POST['margin_cost'];
		$plant_name=$_POST['plant_name'];
		$conversion_factor=$_POST['conversion_factor'];
		$packing_realization=$_POST['packing_realization'];
			
		for($k=0;$k<count($oils_val);$k++){
			$sqlinsertpricingdetails="INSERT INTO pricing_detials_formulation 
									  SET plant_name='".$distinct_plant_name[$k]."',
									  product_group_code='',
									  price_generation_id='".$price_generation_id."',
									  oils='".$oils_val[$k]."',
									  oils_rate='".$oils_rate[$k]."',
									  datetime=CURRENT_TIMESTAMP,
									  vertical_value='',
									  user_login='".$_SESSION['admin_login']."',
									  user_ip='".$_SERVER['REMOTE_ADDR']."'";
			$rsinsertpricingdetails=mysqli_query($link,$sqlinsertpricingdetails);
		}
		for($m=0;$m<count($prod_val);$m++){
			$sqlselprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_val[$m]."'";
			$rsselprodcode=mysqli_query($link,$sqlselprodcode);
			$rowselprodcode=mysqli_fetch_assoc($rsselprodcode);
			$prod_code=$rowselprodcode['prod_code'];
			
			/*$sqlchkmrp="SELECT product_code FROM sauda_mrp WHERE product_code='".$prod_code."' AND acedns='Y'";
			$rschkmrp=mysqli_query($link,$sqlchkmrp);
			$cntchkmrp=mysqli_num_rows($rschkmrp);
			if($cntchkmrp==0)
			{*/
				$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
								AS max_mrp_code from sauda_mrp";
				$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
				$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
				$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
				$max_mrp_code++;
				$max_mrp_code='z'.$max_mrp_code;
				
				$sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',
										mrp='0',sale_rate='".$sale_rate[$m]."',
										branch_code='',product_code='".$prod_code."',
										vertical_value='',
										basic_rate='".$loose_rate[$m]."',
										process_cost='".$process_cost[$m]."',
										labour_cost='".$labour_cost[$m]."',
										packing_cost='".$packing_cost[$m]."',
										margin_cost='".$margin_cost[$m]."',
										extra_cost='".$extra_cost[$m]."',
										packing_realization='".$packing_realization[$m]."',
										create_date=CURRENT_TIMESTAMP(),
										primary_freight	='0',depot_cost='0',download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertmrpprodwise))
				{
				  $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
						  				  mapped_prod_code='".$prod_val[$m]."' AND acedns='Y' AND prod_code!=mapped_prod_code 
										  AND prod_code IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y')";
				  $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
				  while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
				  {
					$dependent_prod_val=$rowfetchdependentprod['prod_code'];
					//echo $sale_rate[$n];
					//echo '<br />';
					$sqlconversiondependent="SELECT conversion_factor,conversion_factor_two,prod_desc,prod_code,UOM1,pack_size,packing_realization FROM product_master 
									WHERE dns_prod_code='".$dependent_prod_val."'";
					$rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
					$rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
					${conversion_factor.$dependent_prod_val}=$rowconversiondependent['conversion_factor'];
					//echo '<br />';
					${conversion_factor_two.$dependent_prod_val}=$rowconversiondependent['conversion_factor_two'];
					${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];
					${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
					${UOM1.$dependent_prod_val}=$rowconversiondependent['UOM1'];
					${pack_size.$dependent_prod_val}=$rowconversiondependent['pack_size'];
					//echo '<br />';
					//echo $conversion_factor[$n];
					//echo '<br />';
					$sqlpackingdependent="SELECT packing_cost,labour_cost,extra_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$dependent_prod_val."' 
								AND plant_name='".$distinct_plant_name[$m]."' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingdependent=mysqli_query($link,$sqlpackingdependent);
					$rowpackingdependent=mysqli_fetch_assoc($rspackingdependent);
					${labour_cost.$dependent_prod_val}=$rowpackingdependent['labour_cost'];
					${packing_cost.$dependent_prod_val}=$rowpackingdependent['packing_cost'];
					${extra_cost.$dependent_prod_val}=$rowpackingdependent['extra_cost'];
					
					${packing_realization.$dependent_prod_val}=$rowconversiondependent['packing_realization'];
					
					if(${packing_cost.$dependent_prod_val}=='') ${packing_cost.$dependent_prod_val}=0;
					if(${labour_cost.$dependent_prod_val}=='')  ${labour_cost.$dependent_prod_val}=0;
					if(${extra_cost.$dependent_prod_val}=='')   ${extra_cost.$dependent_prod_val}=0;
					if(${packing_realization.$dependent_prod_val}=='')   ${packing_realization.$dependent_prod_val}=0;
					/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' AND 
												vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
					$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
					${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
					if(${freight_cost.$distinct_dnsprod_code}=='')
					{
						${freight_cost.$distinct_dnsprod_code}=0;
					}
					
					${honeycomb_cost.$distinct_dnsprod_code}=0;*/
			
					$sqlmargincostdependent="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dependent_prod_val."' ORDER BY 
										datetime DESC LIMIT 0,1";
					$rsmargincostdependent=mysqli_query($link,$sqlmargincostdependent);
					$rowmargincostdependent=mysqli_fetch_assoc($rsmargincostdependent);
					${margin_cost.$dependent_prod_val}=$rowmargincostdependent['margin_cost'];
					if(${margin_cost.$dependent_prod_val}=='')
					{
						${margin_cost.$dependent_prod_val}=0;
					}
					if(strtoupper(${UOM1.$dependent_prod_val})=='LOOSE')
					{
						${loose_rate_dependent.$dependent_prod_val}=$loose_rate[$m];
						${process_cost.$dependent_prod_val}=$process_cost[$m];
					}
					else
					{
						${loose_rate_dependent.$dependent_prod_val}=($loose_rate[$m]/$conversion_factor[$m])*${conversion_factor_two.$dependent_prod_val};
						${process_cost.$dependent_prod_val}=($process_cost[$m]/$conversion_factor[$m])*${conversion_factor_two.$dependent_prod_val};
					}
					${basic_rate_prodwise.$dependent_prod_val}=${loose_rate_dependent.$dependent_prod_val}+${process_cost.$dependent_prod_val}+${margin_cost.$dependent_prod_val}+${packing_realization.$dependent_prod_val};
					//${basic_rate_prodwise.$dependent_prod_val}=round(${basic_rate_prodwise.$dependent_prod_val},2);
					
					if(${pack_size.$dependent_prod_val}=='BP')
					{
						${basic_rate_prodwise.$dependent_prod_val}=round(${basic_rate_prodwise.$dependent_prod_val},0);
					}
					if(${pack_size.$dependent_prod_val}=='CP')
					{
						${basic_rate_prodwise.$dependent_prod_val}=round(${basic_rate_prodwise.$dependent_prod_val},1);
					}
					else
					{
						${basic_rate_prodwise.$dependent_prod_val}=${basic_rate_prodwise.$dependent_prod_val};
					}

					$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
							AS max_mrp_code from sauda_mrp";
					$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
					$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
					$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
					$max_mrp_code++;
					$max_mrp_code='z'.$max_mrp_code;
					
					$sqlinsertmrpprodwisedepen="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',
											mrp='0',sale_rate='".${basic_rate_prodwise.$dependent_prod_val}."',
											branch_code='',product_code='".${prod_code.$dependent_prod_val}."',
											vertical_value='',
											basic_rate='".${loose_rate_dependent.$dependent_prod_val}."',
											process_cost='".${process_cost.$dependent_prod_val}."',
											labour_cost='".${labour_cost.$dependent_prod_val}."',
											packing_cost='".${packing_cost.$dependent_prod_val}."',
											margin_cost='".${margin_cost.$dependent_prod_val}."',
											extra_cost='".${extra_cost.$dependent_prod_val}."',
											packing_realization='".${packing_realization.$dependent_prod_val}."',
											create_date=CURRENT_TIMESTAMP(),
											primary_freight	='0',depot_cost='0',download_time=CURRENT_TIMESTAMP()";
					mysqli_query($link,$sqlinsertmrpprodwisedepen);
				  }
				}
			/*}
			else
			{
				$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='0',
										sale_rate='".$sale_rate[$m]."',
										basic_rate='".$sale_rate[$m]."',primary_freight	='',
										depot_cost='',download_time=CURRENT_TIMESTAMP()
										WHERE product_code='".$prod_code."'";
				mysqli_query($link,$sqlupdatemrpprodwise);
			}*/
		}
		$flag=1;
		//End For product group wise all product data mrp updation on the basis of Loose rate
		if($flag==1){?>
		   <script language="JavaScript" type="text/javascript">alert('Base product rate release sucessfull.'); window.location.href='generated_price_release.php';</script>
           <?php
		   		//For Dependent product
		   		/*$tabledatadependent='<form name="create_price" method="post" action="generate_pricing_formulationwise.php">
						<input type="hidden" name="mode" value="submit_pricing_dependent">
						<table class="border" width="70%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
						  <tr>
							<td colspan="9" class="TDHEAD" align="left">Dependent Product Sale Rate </td>
						  </tr>
						  <tr class="TDHEAD_SUB">
							<td width="8%">SI</td>
							<td width="10%">Product Code</td>
							<td width="22%">Product Description</td>
							<td width="10%">Material Cost</td>
							<td width="10%">Packing Cost</td>
							<td width="10%">Labour Cost</td>
							<td width="10%">Extra Cost</td>
							<td width="10%">Margin</td>
							<td width="10%">Bargain Rate</td>
						  </tr>';
						  $countdependent=1;
						  for($n=0;$n<count($prod_val);$n++){
						  $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
						  						 mapped_prod_code='".$prod_val[$n]."' AND acedns='Y' AND prod_code!=mapped_prod_code";
						  $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
						  while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
						  {
						    $dependent_prod_val=$rowfetchdependentprod['prod_code'];
							//echo $sale_rate[$n];
							//echo '<br />';
							$sqlconversiondependent="SELECT conversion_factor,conversion_factor_two,prod_desc,prod_code,UOM1 FROM product_master 
											WHERE dns_prod_code='".$dependent_prod_val."'";
							$rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
							$rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
							${conversion_factor.$dependent_prod_val}=$rowconversiondependent['conversion_factor'];
							//echo '<br />';
							${conversion_factor_two.$dependent_prod_val}=$rowconversiondependent['conversion_factor_two'];
							${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];
							${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
							${UOM1.$dependent_prod_val}=$rowconversiondependent['UOM1'];
							//echo '<br />';
							//echo $conversion_factor[$n];
							//echo '<br />';
							$sqlpackingdependent="SELECT packing_cost,labour_cost,extra_cost FROM packing_master WHERE dns_prod_code='".$dependent_prod_val."' 
										AND plant_name='".$plant_name[$n]."' ORDER BY datetime DESC LIMIT 0,1";
										
							$rspackingdependent=mysqli_query($link,$sqlpackingdependent);
							$rowpackingdependent=mysqli_fetch_assoc($rspackingdependent);
							${labour_cost.$dependent_prod_val}=$rowpackingdependent['labour_cost'];
							${packing_cost.$dependent_prod_val}=$rowpackingdependent['packing_cost'];
							${extra_cost.$dependent_prod_val}=$rowpackingdependent['extra_cost'];
							
							if(${packing_cost.$dependent_prod_val}=='') ${packing_cost.$dependent_prod_val}=0;
							if(${labour_cost.$dependent_prod_val}=='')  ${labour_cost.$dependent_prod_val}=0;
							if(${extra_cost.$dependent_prod_val}=='')   ${extra_cost.$dependent_prod_val}=0;
							/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
														AND branch_code='".$distinct_branch_code."' AND 
														vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
							$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
							$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
							${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
							if(${freight_cost.$distinct_dnsprod_code}=='')
							{
								${freight_cost.$distinct_dnsprod_code}=0;
							}
							
							${honeycomb_cost.$distinct_dnsprod_code}=0;*/
					
							/*$sqlmargincostdependent="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dependent_prod_val."' ORDER BY 
												datetime DESC LIMIT 0,1";
							$rsmargincostdependent=mysqli_query($link,$sqlmargincostdependent);
							$rowmargincostdependent=mysqli_fetch_assoc($rsmargincostdependent);
							${margin_cost.$dependent_prod_val}=$rowmargincostdependent['margin_cost'];
							if(${margin_cost.$dependent_prod_val}=='')
							{
								${margin_cost.$dependent_prod_val}=0;
							}
							if(strtoupper(${UOM1.$dependent_prod_val})=='LOOSE')
							{
								${loose_rate_dependent.$dependent_prod_val}=$loose_rate[$n];
							}
							else
							{
						    	${loose_rate_dependent.$dependent_prod_val}=($loose_rate[$n]/$conversion_factor[$n])*${conversion_factor.$dependent_prod_val};
							}
						    ${basic_rate_prodwise.$dependent_prod_val}=${loose_rate_dependent.$dependent_prod_val}+${packing_cost.$dependent_prod_val}+${margin_cost.$dependent_prod_val}+${labour_cost.$dependent_prod_val}+${extra_cost.$dependent_prod_val};
					        ${basic_rate_prodwise.$dependent_prod_val}=round(${basic_rate_prodwise.$dependent_prod_val},2);
							//echo '<br />';
							//exit();
						    $tabledatavaldependent.="<input type=\"hidden\" name=\"dependent_prod_val[]\" value=".$dependent_prod_val.">
									<input type=\"hidden\" name=\"sale_rate_dependent[]\" value=".${basic_rate_prodwise.$dependent_prod_val}.">
									<input type=\"hidden\" name=\"prod_code_dependent[]\" value=".${prod_code.$dependent_prod_val}.">
									<input type=\"hidden\" name=\"loose_rate_dependent[]\" value=".${loose_rate_dependent.$dependent_prod_val}.">
									<input type=\"hidden\" name=\"packing_cost_dependent[]\" value=".${packing_cost.$dependent_prod_val}.">
									<input type=\"hidden\" name=\"labour_cost_dependent[]\" value=".${labour_cost.$dependent_prod_val}.">
									<input type=\"hidden\" name=\"extra_cost_dependent[]\" value=".${extra_cost.$dependent_prod_val}.">
									<input type=\"hidden\" name=\"margin_cost_dependent[]\" value=".${margin_cost.$dependent_prod_val}.">
									<tr id=\"tab\">
										<td>".$countdependent."</td>
										<td>".$dependent_prod_val."</td>
										<td>".${prod_desc.$dependent_prod_val}."</td>
										<td align=\"right\">".number_format(${loose_rate_dependent.$dependent_prod_val},2)."</td>
										<td align=\"right\">".number_format(${packing_cost.$dependent_prod_val},2)."</td>
										<td align=\"right\">".number_format(${labour_cost.$dependent_prod_val},2)."</td>
										<td align=\"right\">".number_format(${extra_cost.$dependent_prod_val},2)."</td>
										<td align=\"right\">".number_format(${margin_cost.$dependent_prod_val},2)."</td>
										<td align=\"right\">".number_format(${basic_rate_prodwise.$dependent_prod_val},2)."</td>
									</tr>";
							 $countdependent++;		
						  }
						 }
						echo $tabledatadependent.=$tabledatavaldependent."<tr><td colspan='9' align='center'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit6\" value=\"Issued to Release\" />&nbsp;&nbsp;&nbsp;<input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/generate_pricing_formulationwise.php'\"/></td></tr></table></form>";

		   			//End of dependent product rate creation*/
		   }else{
			?><script language="JavaScript" type="text/javascript">alert('Base product rate release unsuccessful.');window.location.href='generate_pricing_formulationwise.php';</script>
		<?php 
		}
	}
	/*if($_REQUEST['mode']=='submit_pricing_dependent')
	{
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$price_generation_id='PG'.$_SESSION['admin_login'].$year.$month.$date.$hour.$minute.$second;
		
		$dependent_prod_val=$_POST['dependent_prod_val'];
		$sale_rate_dependent=$_POST['sale_rate_dependent'];
		$prod_code_dependent=$_POST['prod_code_dependent'];
		$loose_rate_dependent=$_POST['loose_rate_dependent'];
		$packing_cost_dependent=$_POST['packing_cost_dependent'];
		$labour_cost_dependent=$_POST['labour_cost_dependent'];
		$extra_cost_dependent=$_POST['extra_cost_dependent'];
		$margin_cost_dependent=$_POST['margin_cost_dependent'];

		for($p=0;$p<count($dependent_prod_val);$p++){
			$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
							AS max_mrp_code from sauda_mrp";
			$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
			$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
			$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
			$max_mrp_code++;
			$max_mrp_code='z'.$max_mrp_code;
			
			$sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',
									mrp='0',sale_rate='".$sale_rate_dependent[$p]."',
									branch_code='',product_code='".$prod_code_dependent[$p]."',
									vertical_value='',
									basic_rate='".$loose_rate_dependent[$p]."',
									labour_cost='".$labour_cost_dependent[$p]."',
									packing_cost='".$packing_cost_dependent[$p]."',
									margin_cost='".$margin_cost_dependent[$p]."',
									extra_cost='".$extra_cost_dependent[$p]."',
									create_date=CURRENT_TIMESTAMP(),
									primary_freight	='0',depot_cost='0',download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sqlinsertmrpprodwise);
		}
		$flag=2;
		if($flag==2){?>
		   <script language="JavaScript" type="text/javascript">alert('Dependent product rate release sucessfull.');
		   window.location.href='generated_price_release.php';</script>
           <?php }else{?>
           <script language="JavaScript" type="text/javascript">alert('Dependent product rate release unsuccessful.');window.location.href='generate_pricing_formulationwise.php';</script>
           <?php
	}
  }*/
}
?>