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
{
	$cpo_rate=$_POST['cpo_rate'];
		$cpo_duty=$_POST['cpo_duty'];
		$cpo_rate_USD=$_POST['cpo_rate_USD'];
		$inr_exchange_rate=$_POST['inr_exchange_rate'];
		$mcx_open_rate=$_POST['mcx_open_rate'];
		$mcx_close_rate=$_POST['mcx_close_rate'];
	
	$sqlprevcrudval="SELECT cpo_rate,cpo_duty,cpo_rate_USD,exchange_rate_USD,mcx_open_rate,mcx_close_rate FROM crude_oil_rate  ORDER BY download_time DESC LIMIT 0,1";
	$rsprevcrudval=mysql_query($sqlprevcrudval); 
	$rowprevcrudval=mysql_fetch_array($rsprevcrudval);
	$cpo_rate_val=$rowprevcrudval['cpo_rate'];
	$cpo_duty_val=$rowprevcrudval['cpo_duty'];
	$cpo_rate_USD_val=$rowprevcrudval['cpo_rate_USD'];
	$inr_exchange_rate_val=$rowprevcrudval['inr_exchange_rate'];
	$mcx_open_rate_val=$rowprevcrudval['mcx_open_rate'];
	$mcx_close_rate_val=$rowprevcrudval['mcx_close_rate'];
	
	if($cpo_rate_val=='') $cpo_rate_val=0;
	if($cpo_duty_val=='') $cpo_duty_val=0;
	if($cpo_rate_USD_val=='') $cpo_rate_USD_val=0;
	if($inr_exchange_rate_val=='') $inr_exchange_rate_val=0;
	if($mcx_open_rate_val=='') $mcx_open_rate_val=0;
	if($mcx_close_rate_val=='') $mcx_close_rate_val=0;
	?>
<table cellpadding="4px" width="40%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate MCX Rate</td>
    </tr>
    <tr>
    <td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="genarate_crude_oil_procurement.php" name="procurement_create" onSubmit="return validation();" method="post">
<input type="hidden" name="pricing_mode" value="create_procurement" />
<table cellpadding="4px">
    <tr>
        <td>
            <table id="displayformulation" align="center">
            	 <tr><td align="left">CPO rate in USD:</td>
                	<td align="left"><input type="text" name="cpo_rate_USD"  id="cpo_rate_USD" style="height:20px;"  value="<?php echo $cpo_rate_USD_val;?>" /></td>
                 </tr>
				<tr><td align="left">CPO rate in INR:</td>
                	<td align="left"><input type="text" name="cpo_rate"  id="cpo_rate" style="height:20px;" value="<?php echo $cpo_rate_val;?>" /></td>
                 </tr>
                 <tr><td align="left">CPO Duty in INR:</td>
                	<td align="left"><input type="text" name="cpo_duty"  id="cpo_duty" style="height:20px;" value="<?php echo $cpo_duty_val;?>" /></td>
                 </tr>
                 <tr><td align="left">USD INR exchange Rate:</td>
                	<td align="left"><input type="text" name="inr_exchange_rate"  id="inr_exchange_rate" style="height:20px;" value="<?php echo $inr_exchange_rate_val;?>" /></td>
                 </tr>
				<tr><td align="left">MCX Open Rate:</td>
                	<td align="left"><input type="text" name="mcx_open_rate"  id="mcx_open_rate" style="height:20px;" value="<?php echo $mcx_open_rate_val;?>" /></td>
                </tr>
                <tr><td align="left">MCX Close Rate:</td>
                	<td align="left"><input type="text" name="mcx_close_rate"  id="mcx_close_rate" style="height:20px;" value="<?php echo $mcx_close_rate_val;?>" /></td>
                </tr>
			</table>
       </td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Generate MCX Rate"/><!--&nbsp;&nbsp;<input name="submit1" type="button" value="Generate Price List"/>!--></td>
    </tr>
</table>
</form>
</td></tr></table><br /><br /><br />
<?php }?>
</center>
<script>
function validation()
{
	if(document.getElementById("cpo_rate").value.search(/\S/) == -1)
	{
		alert('Please input CPO rate in INR');
		return false;
	}
	if(document.getElementById("cpo_duty").value.search(/\S/) == -1)
	{
		alert('Please input CPO Duty');
		return false;
	}
	if(document.getElementById("mcx_open_rate").value==0 && document.getElementById("mcx_close_rate").value==0)
	{
		alert('Please input MCX Open rate or MCX Close rate');
		return false;
	}
	return true;
}
</script>
<?php
	if($_REQUEST['pricing_mode']=='create_procurement')
	{
		$cpo_rate=$_POST['cpo_rate'];
		$cpo_duty=$_POST['cpo_duty'];
		$cpo_rate_USD=$_POST['cpo_rate_USD'];
		$inr_exchange_rate=$_POST['inr_exchange_rate'];
		$mcx_open_rate=$_POST['mcx_open_rate'];
		$mcx_close_rate=$_POST['mcx_close_rate'];
		
		$count=1;
		//$procurement_rate=(($cpo_rate*$cpo_duty)/100)+$cpo_rate;
		$procurement_rate=$cpo_rate+$cpo_duty;
		$tabledataval='';
		$tabledata='<form name="create_procurement" method="post" action="genarate_crude_oil_procurement.php">
						<input type="hidden" name="mode" value="submit_procurement">
						<input type="hidden" name="cpo_rate" value="'.$cpo_rate.'">
						<input type="hidden" name="cpo_duty" value="'.$cpo_duty.'">
						<input type="hidden" name="cpo_rate_USD" value="'.$cpo_rate_USD.'">
						<input type="hidden" name="inr_exchange_rate" value="'.$inr_exchange_rate.'">
						<input type="hidden" name="mcx_open_rate" value="'.$mcx_open_rate.'">
						<input type="hidden" name="mcx_close_rate" value="'.$mcx_close_rate.'">
						<table class="border" width="45%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
						  <tr>
							<td colspan="7" class="TDHEAD" align="left">Crude Oil Procurment Data </td>
						  </tr>
						  <tr class="TDHEAD_SUB">
						  	<td>CPO rate in USD</td>
							<td>CPO rate in INR</td>
							<td>CPO Duty in INR</td>
							<td>USD INR exchange Rate</td>
							<td>MCX Open Rate</td>
							<td>MCX Close Rate</td>
							<td>procurement Rate</td>
						  </tr>';
		$tabledataval.="<input type=\"hidden\" name=\"procurement_rate\" value=".$procurement_rate.">
						<tr id=\"tab\">
							<td align=\"right\">".number_format($cpo_rate_USD,2)."</td>
							<td align=\"right\">".number_format($cpo_rate,2)."</td>
							<td align=\"right\">".number_format($cpo_duty,2)."</td>
							<td align=\"right\">".number_format($inr_exchange_rate,2)."</td>
							<td align=\"right\">".number_format($mcx_open_rate,2)."</td>
							<td align=\"right\">".number_format($mcx_close_rate,2)."</td>
							<td align=\"right\">".number_format($procurement_rate,2)."</td>
						</tr>";
		echo $tabledata.=$tabledataval."<tr><td colspan='7' align='center'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit5\" value=\"Proced to Release\" />&nbsp;&nbsp;&nbsp;<input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/genarate_crude_oil_procurement.php'\"/></td></tr></table></form>";
	}
	if($_REQUEST['mode']=='submit_procurement')
	{
		$cpo_rate=$_POST['cpo_rate'];
		$cpo_duty=$_POST['cpo_duty'];
		$cpo_rate_USD=$_POST['cpo_rate_USD'];
		$inr_exchange_rate=$_POST['inr_exchange_rate'];
		$mcx_open_rate=$_POST['mcx_open_rate'];
		$mcx_close_rate=$_POST['mcx_close_rate'];
		
		$oils_val=array('mcx_open_rate','mcx_close_rate');
		$oils_val_string = "'" .implode("', '", $oils_val) . "'";
		$loose_rate_ton=array($mcx_open_rate,$mcx_close_rate);
		//print_r($loose_rate_ton);
		
		$sqlinsertcrudeoilrate="INSERT INTO crude_oil_rate 
								SET cpo_rate='".$cpo_rate."',
								 cpo_duty='".$cpo_duty."',
								 cpo_rate_USD='".$cpo_rate_USD."',
								 exchange_rate_USD='".$inr_exchange_rate."',
								 mcx_open_rate='".$mcx_open_rate."',
								 mcx_close_rate='".$mcx_close_rate."',
								 crude_oil_rate='".$procurement_rate."',
								 download_time=CURRENT_TIMESTAMP,
								 create_date=CURRENT_TIMESTAMP,
								 user_login='".$_SESSION['admin_login']."',
								 user_ip='".$_SERVER['REMOTE_ADDR']."'";
		$rsinsertcrudeoilrate=mysql_query($sqlinsertcrudeoilrate);
		
		$tabledata='<form name="create_price" method="post" action="genarate_crude_oil_procurement.php">
						<input type="hidden" name="mode" value="submit_pricing">
						<input type="hidden" name="loose_rate_ton" value="'.$loose_rate_ton.'">
						<table class="border" width="70%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
						  <tr>
							<td colspan="10" class="TDHEAD" align="left">MCX Base Product Sale Rate </td>
						  </tr>
						  <tr class="TDHEAD_SUB">
							<td>SI</td>
							<td>Product Code</td>
							<td>Product Description</td>
							<td>Material Cost Open (MCX)</td>
							<td>Material Cost Close (MCX)</td>
							<td>Process Cost</td>
							<td>Packing Realization</td>
							<td>Bargain Rate Open (MCX)</td>
							<td>Bargain Rate Close (MCX)</td>
						  </tr>';
			$formulation_prod_array=array();
			for($i=0;$i<count($oils_val);$i++ )
			{
			  if($oils_val[$i]=='mcx_open_rate'){
				$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
											WHERE  oils like 'MCX%' AND acedns='Y' AND prod_code 
											IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y') ORDER BY datetime DESC) AS SAT GROUP BY 1 ";																		
				$rsprodwiseformulation=mysql_query($sqlprodwiseformulation);
				while($rowprodwiseformulation=mysql_fetch_array($rsprodwiseformulation))
				{  		
					${loosrate_calc_val_open.$rowprodwiseformulation['prod_code']}=($rowprodwiseformulation['formulation']*$loose_rate_ton[$i])/100;
					${loosrate_final_val_open.$rowprodwiseformulation['prod_code']}=${loosrate_final_val_open.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val_open.$rowprodwiseformulation['prod_code']};
					if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
					{
						array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
					}
				}
			  }
			  if($oils_val[$i]=='mcx_close_rate'){
				$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
											WHERE oils like 'MCX%' AND acedns='Y' AND prod_code 
											IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y') ORDER BY datetime DESC) AS SAT GROUP BY 1 ";																		
				$rsprodwiseformulation=mysql_query($sqlprodwiseformulation);
				while($rowprodwiseformulation=mysql_fetch_array($rsprodwiseformulation))
				{  		
					${loosrate_calc_val_close.$rowprodwiseformulation['prod_code']}=($rowprodwiseformulation['formulation']*$loose_rate_ton[$i])/100;
					${loosrate_final_val_close.$rowprodwiseformulation['prod_code']}=${loosrate_final_val_close.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val_close.$rowprodwiseformulation['prod_code']};
					if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
					{
						array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
					}
				}
			  }
				$tabledataval.="<input type=\"hidden\" name=\"oils_val_array[]\" value=\"$oils_val[$i]\">
							<input type=\"hidden\" name=\"oils_rate_array[]\" value=\"$loose_rate_ton[$i]\">";
			}
			//echo ${loosrate_final_val.'DV15KT000108'};
			//echo '<br />';
			$count=1;
			foreach($formulation_prod_array as $formulation_prod_val)
			{
				//echo $formulation_prod_val.'<br />';
				//$formulation_prod_val='ASV15KB00007';
				/*$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type like 'MCX%' ORDER BY datetime DESC LIMIT 0,1";
				$rsprocesscost=mysql_query($sqlprocesscost);
				$rowprocesscost=mysql_fetch_array($rsprocesscost);
					
				${process_cost.$formulation_prod_val}=$rowprocesscost['process_cost'];*/
				$sqlformulationbaseoilN="SELECT * FROM (SELECT oils,formulation,percentile_calc FROM loose_oilrate_formulation 
										WHERE  acedns='Y' AND prod_code='".$formulation_prod_val."' 
										AND percentile_calc='N' ORDER BY datetime DESC) AS SAT GROUP BY 1 ";						
				$rsformulationbaseoilN=mysql_query($sqlformulationbaseoilN);
				while($rowformulationbaseoilN=mysql_fetch_array($rsformulationbaseoilN))
				{
					$oil_type=$rowformulationbaseoilN['oils'];
					$formulation=$rowformulationbaseoilN['formulation'];
					${percentile_calc.$formulation_prod_val}=$rowformulationbaseoilN['percentile_calc'];
					$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$oil_type."'  ORDER BY datetime DESC LIMIT 0,1";
					$rsprocesscost=mysql_query($sqlprocesscost);
					$rowprocesscost=mysql_fetch_array($rsprocesscost);
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
				$rsconversionfactor=mysql_query($sqlconversionfactor);
				$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
				${conversion_factor.$formulation_prod_val}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$formulation_prod_val}=$rowconversionfactor['conversion_factor_two'];
				${prod_desc.$formulation_prod_val}=$rowconversionfactor['prod_desc'];
				${UOM1.$formulation_prod_val}=$rowconversionfactor['UOM1'];
				${pack_size.$formulation_prod_val}=$rowconversionfactor['pack_size'];
				if(${prod_desc.$formulation_prod_val}!=''){
				$sqlpackingprodwise="SELECT packing_cost,labour_cost,extra_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$formulation_prod_val."' 
										AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
				$rspackingprodwise=mysql_query($sqlpackingprodwise);
				$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
				${labour_cost.$formulation_prod_val}=$rowpackingprodwise['labour_cost'];
				${packing_cost.$formulation_prod_val}=$rowpackingprodwise['packing_cost'];
				${extra_cost.$formulation_prod_val}=$rowpackingprodwise['extra_cost'];
				${packing_realization.$formulation_prod_val}=$rowconversionfactor['packing_realization'];
					
					
				$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$formulation_prod_val."'  ORDER BY datetime DESC LIMIT 0,1";
				$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
				$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
				${margin_cost.$formulation_prod_val}=$rowmargincostprodwise['margin_cost'];
				if(${margin_cost.$formulation_prod_val}=='')
				{
					${margin_cost.$formulation_prod_val}=0;
				}
				if(strtoupper(${UOM1.$formulation_prod_val})=='LOOSE')
				{
					${loose_rate_case_mcx_open_prodwise.$formulation_prod_val}=${loosrate_final_val_open.$formulation_prod_val};
					${loose_rate_case_mcx_close_prodwise.$formulation_prod_val}=${loosrate_final_val_close.$formulation_prod_val};
					${process_cost_case.$formulation_prod_val}=${process_cost.$formulation_prod_val};
				}
				else
				{
					//${loose_rate_case_prodwise.$formulation_prod_val}=round((${loosrate_final_val.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val}),2);
					//${process_cost_case.$formulation_prod_val}=round((${process_cost.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val}),2);
					${loose_rate_case_mcx_open_prodwise.$formulation_prod_val}=${loosrate_final_val_open.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val};
					${loose_rate_case_mcx_close_prodwise.$formulation_prod_val}=${loosrate_final_val_close.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val};
					${process_cost_case.$formulation_prod_val}=${process_cost.$formulation_prod_val}*${conversion_factor_two.$formulation_prod_val};
				}
				//${loose_rate_case_prodwise.$formulation_prod_val}=round((${loose_rate_case_prodwise.$formulation_prod_val}*${conversion_factor.$formulation_prod_val}),2);
				if(${loose_rate_case_mcx_open_prodwise.$formulation_prod_val} >0)
				{
					${basic_rate_mcx_open_prodwise.$formulation_prod_val}=${loose_rate_case_mcx_open_prodwise.$formulation_prod_val}+${process_cost_case.$formulation_prod_val}+${margin_cost.$formulation_prod_val}+${packing_realization.$formulation_prod_val};
				}
				else ${basic_rate_mcx_open_prodwise.$formulation_prod_val}=0;
				if(${loose_rate_case_mcx_close_prodwise.$formulation_prod_val} >0)
				{
					${basic_rate_mcx_close_prodwise.$formulation_prod_val}=${loose_rate_case_mcx_close_prodwise.$formulation_prod_val}+${process_cost_case.$formulation_prod_val}+${margin_cost.$formulation_prod_val}+${packing_realization.$formulation_prod_val};
				}
				else ${basic_rate_mcx_close_prodwise.$formulation_prod_val}=0;

				if(${pack_size.$formulation_prod_val}=='BP')
				{
				 	${basic_rate_mcx_open_prodwise.$formulation_prod_val}=round(${basic_rate_mcx_open_prodwise.$formulation_prod_val},0);
					${basic_rate_mcx_close_prodwise.$formulation_prod_val}=round(${basic_rate_mcx_close_prodwise.$formulation_prod_val},0);
				}
				if(${pack_size.$formulation_prod_val}=='CP')
				{
					${basic_rate_mcx_open_prodwise.$formulation_prod_val}=round(${basic_rate_mcx_open_prodwise.$formulation_prod_val},1);
					${basic_rate_mcx_close_prodwise.$formulation_prod_val}=round(${basic_rate_mcx_close_prodwise.$formulation_prod_val},1);
				}
				else
				{
					${basic_rate_mcx_open_prodwise.$formulation_prod_val}=${basic_rate_mcx_open_prodwise.$formulation_prod_val};
					${basic_rate_mcx_close_prodwise.$formulation_prod_val}=${basic_rate_mcx_close_prodwise.$formulation_prod_val};
				}
					
				if(${loose_rate_case_mcx_open_prodwise.$formulation_prod_val} >0 || ${loose_rate_case_mcx_close_prodwise.$formulation_prod_val} > 0){
					$tabledataval.="<input type=\"hidden\" name=\"prod_val[]\" value=".$formulation_prod_val.">
									<input type=\"hidden\" name=\"sale_rate_open[]\" value=".${basic_rate_mcx_open_prodwise.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"sale_rate_close[]\" value=".${basic_rate_mcx_close_prodwise.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"loose_rate_open[]\" value=".${loose_rate_case_mcx_open_prodwise.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"loose_rate_close[]\" value=".${loose_rate_case_mcx_close_prodwise.$formulation_prod_val}.">
									<input type=\"hidden\" name=\"process_cost[]\" value=".${process_cost_case.$formulation_prod_val}.">
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
										<td align=\"right\">".number_format(${loose_rate_case_mcx_open_prodwise.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${loose_rate_case_mcx_close_prodwise.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${process_cost_case.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${packing_realization.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${basic_rate_mcx_open_prodwise.$formulation_prod_val},2)."</td>
										<td align=\"right\">".number_format(${basic_rate_mcx_close_prodwise.$formulation_prod_val},2)."</td>
									</tr>";
						$count++;
					}
				  }
			 }
		echo $tabledata.=$tabledataval."<tr><td colspan='5' align='right'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit5\" value=\"Issued to Release\" /></td><td colspan='4' align='left'><input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/generate_pricing_formulationwise_modified.php'\"/></td></tr></table></form>";
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
		$sale_rate_open=$_POST['sale_rate_open'];
		$sale_rate_close=$_POST['sale_rate_close'];
		$loose_rate_open=$_POST['loose_rate_open'];
		$loose_rate_close=$_POST['loose_rate_close'];
		$process_cost=$_POST['process_cost'];
		$packing_cost=$_POST['packing_cost'];
		$labour_cost=$_POST['labour_cost'];
		$extra_cost=$_POST['extra_cost'];
		$margin_cost=$_POST['margin_cost'];
		$plant_name=$_POST['plant_name'];
		$conversion_factor=$_POST['conversion_factor'];
		$packing_realization=$_POST['packing_realization'];
			
		for($m=0;$m<count($prod_val);$m++){
			$sqlselprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_val[$m]."'";
			$rsselprodcode=mysql_query($sqlselprodcode);
			$rowselprodcode=mysql_fetch_array($rsselprodcode);
			$prod_code=$rowselprodcode['prod_code'];
			
			/*$sqlchkmrp="SELECT product_code FROM sauda_mrp WHERE product_code='".$prod_code."' AND acedns='Y'";
			$rschkmrp=mysql_query($sqlchkmrp);
			$cntchkmrp=mysql_num_rows($rschkmrp);
			if($cntchkmrp==0)
			{*/
				$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
								AS max_mrp_code from mcx_rate";
				$rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
				$rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
				$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
				$max_mrp_code++;
				$max_mrp_code='z'.$max_mrp_code;
				
				$sqlupdateparentMCX="UPDATE mcx_rate SET acedns='N' WHERE product_code='".$prod_code."'";
				$rsupdateparentMCX=mysql_query($sqlupdateparentMCX);
				$sqlinsertmrpprodwiseMCX="INSERT INTO mcx_rate SET mrp_code='".$max_mrp_code."',
										mrp='0',sale_rate_open='".$sale_rate_open[$m]."',
										sale_rate_close='".$sale_rate_close[$m]."',
										branch_code='',product_code='".$prod_code."',
										vertical_value='',
										basic_rate_open='".$loose_rate_open[$m]."',
										basic_rate_close='".$loose_rate_close[$m]."',
										process_cost='".$process_cost[$m]."',
										labour_cost='".$labour_cost[$m]."',
										packing_cost='".$packing_cost[$m]."',
										margin_cost='".$margin_cost[$m]."',
										extra_cost='".$extra_cost[$m]."',
										packing_realization='".$packing_realization[$m]."',
										acedns='Y',
										create_date=CURRENT_TIMESTAMP(),
										primary_freight	='0',depot_cost='0',download_time=CURRENT_TIMESTAMP()";
				if(mysql_query($sqlinsertmrpprodwiseMCX))
				{
				  $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
						  				  mapped_prod_code='".$prod_val[$m]."' AND acedns='Y' AND prod_code!=mapped_prod_code 
										  AND prod_code IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y')";
				  $rsfetchdependentprod=mysql_query($sqlfetchdependentprod);
				  while($rowfetchdependentprod=mysql_fetch_array($rsfetchdependentprod))
				  {
					$dependent_prod_val=$rowfetchdependentprod['prod_code'];
					//echo $sale_rate[$n];
					//echo '<br />';
					$sqlconversiondependent="SELECT conversion_factor,conversion_factor_two,prod_desc,prod_code,UOM1,pack_size,packing_realization FROM product_master 
									WHERE dns_prod_code='".$dependent_prod_val."'";
					$rsconversiondependent=mysql_query($sqlconversiondependent);
					$rowconversiondependent=mysql_fetch_array($rsconversiondependent);
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
								AND plant_name='BHIWADI' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingdependent=mysql_query($sqlpackingdependent);
					$rowpackingdependent=mysql_fetch_array($rspackingdependent);
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
					$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
					${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
					if(${freight_cost.$distinct_dnsprod_code}=='')
					{
						${freight_cost.$distinct_dnsprod_code}=0;
					}
					
					${honeycomb_cost.$distinct_dnsprod_code}=0;*/
			
					$sqlmargincostdependent="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dependent_prod_val."' ORDER BY 
										datetime DESC LIMIT 0,1";
					$rsmargincostdependent=mysql_query($sqlmargincostdependent);
					$rowmargincostdependent=mysql_fetch_array($rsmargincostdependent);
					${margin_cost.$dependent_prod_val}=$rowmargincostdependent['margin_cost'];
					if(${margin_cost.$dependent_prod_val}=='')
					{
						${margin_cost.$dependent_prod_val}=0;
					}
					if(strtoupper(${UOM1.$dependent_prod_val})=='LOOSE')
					{
						${loose_rate_open_dependent.$dependent_prod_val}=$loose_rate_open[$m];
						${loose_rate_close_dependent.$dependent_prod_val}=$loose_rate_close[$m];
						${process_cost.$dependent_prod_val}=$process_cost[$m];
					}
					else
					{
						${loose_rate_open_dependent.$dependent_prod_val}=($loose_rate_open[$m]/$conversion_factor[$m])*${conversion_factor_two.$dependent_prod_val};
						${loose_rate_close_dependent.$dependent_prod_val}=($loose_rate_close[$m]/$conversion_factor[$m])*${conversion_factor_two.$dependent_prod_val};
						${process_cost.$dependent_prod_val}=($process_cost[$m]/$conversion_factor[$m])*${conversion_factor_two.$dependent_prod_val};
					}
					if(${loose_rate_open_dependent.$dependent_prod_val} >0)
					{
						${basic_rate_open_prodwise.$dependent_prod_val}=${loose_rate_open_dependent.$dependent_prod_val}+${process_cost.$dependent_prod_val}+${margin_cost.$dependent_prod_val}+${packing_realization.$dependent_prod_val};
					}
					else ${basic_rate_open_prodwise.$dependent_prod_val}=0;
					if(${loose_rate_close_dependent.$dependent_prod_val} >0)
					{
						${basic_rate_close_prodwise.$dependent_prod_val}=${loose_rate_close_dependent.$dependent_prod_val}+${process_cost.$dependent_prod_val}+${margin_cost.$dependent_prod_val}+${packing_realization.$dependent_prod_val};
					}
					else ${basic_rate_close_prodwise.$formulation_prod_val}=0;
					//${basic_rate_open_prodwise.$dependent_prod_val}=${loose_rate_open_dependent.$dependent_prod_val}+${process_cost.$dependent_prod_val}+${margin_cost.$dependent_prod_val}+${packing_realization.$dependent_prod_val};
					//${basic_rate_close_prodwise.$dependent_prod_val}=${loose_rate_close_dependent.$dependent_prod_val}+${process_cost.$dependent_prod_val}+${margin_cost.$dependent_prod_val}+${packing_realization.$dependent_prod_val};
					//${basic_rate_prodwise.$dependent_prod_val}=round(${basic_rate_prodwise.$dependent_prod_val},2);
					
					if(${pack_size.$dependent_prod_val}=='BP')
					{
						${basic_rate_open_prodwise.$dependent_prod_val}=round(${basic_rate_open_prodwise.$dependent_prod_val},0);
						${basic_rate_close_prodwise.$dependent_prod_val}=round(${basic_rate_close_prodwise.$dependent_prod_val},0);
					}
					if(${pack_size.$dependent_prod_val}=='CP')
					{
						${basic_rate_open_prodwise.$dependent_prod_val}=round(${basic_rate_open_prodwise.$dependent_prod_val},1);
						${basic_rate_close_prodwise.$dependent_prod_val}=round(${basic_rate_close_prodwise.$dependent_prod_val},1);
					}
					else
					{
						${basic_rate_open_prodwise.$dependent_prod_val}=${basic_rate_open_prodwise.$dependent_prod_val};
						${basic_rate_close_prodwise.$dependent_prod_val}=${basic_rate_close_prodwise.$dependent_prod_val};
					}

					$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
							AS max_mrp_code from mcx_rate";
					$rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
					$rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
					$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
					$max_mrp_code++;
					$max_mrp_code='z'.$max_mrp_code;
					$sqlupdatechildMCX="UPDATE mcx_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
					$rsupdatechildMCX=mysql_query($sqlupdatechildMCX);
					$sqlinsertmrpprodwiseMCXdepen="INSERT INTO mcx_rate SET mrp_code='".$max_mrp_code."',
										mrp='0',sale_rate_open='".${basic_rate_open_prodwise.$dependent_prod_val}."',
										sale_rate_close='".${basic_rate_close_prodwise.$dependent_prod_val}."',
										branch_code='',product_code='".${prod_code.$dependent_prod_val}."',
										vertical_value='',
										basic_rate_open='".${loose_rate_open_dependent.$dependent_prod_val}."',
										basic_rate_close='".${loose_rate_close_dependent.$dependent_prod_val}."',
										process_cost='".${process_cost.$dependent_prod_val}."',
										labour_cost='".${labour_cost.$dependent_prod_val}."',
										packing_cost='".${packing_cost.$dependent_prod_val}."',
										margin_cost='".${margin_cost.$dependent_prod_val}."',
										extra_cost='".${extra_cost.$dependent_prod_val}."',
										packing_realization='".${packing_realization.$dependent_prod_val}."',
										acedns='Y',
										create_date=CURRENT_TIMESTAMP(),
										primary_freight	='0',depot_cost='0',download_time=CURRENT_TIMESTAMP()";					
					mysql_query($sqlinsertmrpprodwiseMCXdepen);
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
				mysql_query($sqlupdatemrpprodwise);
			}*/
		}
		$flag=1;
		//End For product group wise all product data mrp updation on the basis of Loose rate
		if($flag==1){?>
		   <script language="JavaScript" type="text/javascript">window.location.href='publiashed_rate_details_MCX.php';</script>
           <?php
		   		//For Dependent product
		   		/*$tabledatadependent='<form name="create_price" method="post" action="generate_pricing_formulationwise_modified.php">
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
						  $rsfetchdependentprod=mysql_query($sqlfetchdependentprod);
						  while($rowfetchdependentprod=mysql_fetch_array($rsfetchdependentprod))
						  {
						    $dependent_prod_val=$rowfetchdependentprod['prod_code'];
							//echo $sale_rate[$n];
							//echo '<br />';
							$sqlconversiondependent="SELECT conversion_factor,conversion_factor_two,prod_desc,prod_code,UOM1 FROM product_master 
											WHERE dns_prod_code='".$dependent_prod_val."'";
							$rsconversiondependent=mysql_query($sqlconversiondependent);
							$rowconversiondependent=mysql_fetch_array($rsconversiondependent);
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
										
							$rspackingdependent=mysql_query($sqlpackingdependent);
							$rowpackingdependent=mysql_fetch_array($rspackingdependent);
							${labour_cost.$dependent_prod_val}=$rowpackingdependent['labour_cost'];
							${packing_cost.$dependent_prod_val}=$rowpackingdependent['packing_cost'];
							${extra_cost.$dependent_prod_val}=$rowpackingdependent['extra_cost'];
							
							if(${packing_cost.$dependent_prod_val}=='') ${packing_cost.$dependent_prod_val}=0;
							if(${labour_cost.$dependent_prod_val}=='')  ${labour_cost.$dependent_prod_val}=0;
							if(${extra_cost.$dependent_prod_val}=='')   ${extra_cost.$dependent_prod_val}=0;
							/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
														AND branch_code='".$distinct_branch_code."' AND 
														vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
							$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
							$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
							${freight_cost.$distinct_dnsprod_code}=$rowfreightcostprodwise['freight_cost'];
							if(${freight_cost.$distinct_dnsprod_code}=='')
							{
								${freight_cost.$distinct_dnsprod_code}=0;
							}
							
							${honeycomb_cost.$distinct_dnsprod_code}=0;*/
					
							/*$sqlmargincostdependent="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dependent_prod_val."' ORDER BY 
												datetime DESC LIMIT 0,1";
							$rsmargincostdependent=mysql_query($sqlmargincostdependent);
							$rowmargincostdependent=mysql_fetch_array($rsmargincostdependent);
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
						echo $tabledatadependent.=$tabledatavaldependent."<tr><td colspan='9' align='center'>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit6\" value=\"Issued to Release\" />&nbsp;&nbsp;&nbsp;<input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/generate_pricing_formulationwise_modified.php'\"/></td></tr></table></form>";

		   			//End of dependent product rate creation*/
		   }else{
			?><script language="JavaScript" type="text/javascript">alert('MCX Base product rate release unsuccessful.');window.location.href='genarate_crude_oil_procurement.php';</script>
		<?php 
		}
	}
}
?>