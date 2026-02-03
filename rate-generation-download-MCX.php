<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$loose_rate_ton=$_POST['loose_rate'];
$oils_val=$_POST['oils_val'];
//$loose_rate_ton='66400,57000,73500,78500,78000,80000,62700,40000,30000,71500,80000,78000,85000,80000,75000,90000,85000,99000';
//$oils_val='CPO Declared,Stearin,Palmolien (ex kandla),Soya (ex kandla),KGMO Alwar,KGMO,PALM FATTY ACID,LOOSE SOAP STOCK,SPENT FULLERS EARTH,SOYA DEGUM KANDLA,RPKO,RCSO,Maize Oil - ex kandla,Cotton Seed oil,Canola,Rice Bran Oil,Sunflower OIl,Groundnut oil';
//$loose_rate_ton='74680';
//$oils_val='MCX_close_rate';
if($oils_val=='MCX_close_rate')
{
	$loose_rate_ton='0,'.$loose_rate_ton;
	$oils_val='MCX_open_rate,'.$oils_val;
}

$loose_rate_ton_array=explode(",",$loose_rate_ton);
$oils_val_array=explode(",",$oils_val);
$oils_val_string .= "'" .implode("', '", $oils_val_array) . "'";
$plant_name='Bhiwadi';
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$price_create_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$price_generation_id='PG'.$emp_code.$year.$month.$date.$hour.$minute.$second;

if(count($oils_val_array) > 0)
{
	$formulation_prod_array=array();
	/*foreach($oils_val_array as $oils_value)
	{
		$oils_val_string .= "'" .implode("', '", $oils_value) . "'";
	}*/
	 $sqlinsertcrudeoilrate="INSERT INTO crude_oil_rate 
								SET 
								 mcx_open_rate='".$loose_rate_ton_array[0]."',
								 mcx_close_rate='".$loose_rate_ton_array[1]."',
								 download_time=CURRENT_TIMESTAMP,
								 create_date=CURRENT_TIMESTAMP,
								 user_login='".$emp_code."',
								 user_ip='APP'";
	$rsinsertcrudeoilrate=mysqli_query($link,$sqlinsertcrudeoilrate);
  for($i=0;$i<count($oils_val_array);$i++)
  {
	  if($oils_val_array[$i]=='MCX_open_rate'){
		$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
									WHERE  oils like 'MCX%' AND acedns='Y' AND prod_code 
									IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y') ORDER BY datetime DESC) AS SAT GROUP BY 1 ";																		
		$rsprodwiseformulation=mysqli_query($link,$sqlprodwiseformulation);
		while($rowprodwiseformulation=mysqli_fetch_assoc($rsprodwiseformulation))
		{  		
			${loosrate_calc_val_open.$rowprodwiseformulation['prod_code']}=($rowprodwiseformulation['formulation']*$loose_rate_ton_array[$i])/100;
			${loosrate_final_val_open.$rowprodwiseformulation['prod_code']}=${loosrate_final_val_open.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val_open.$rowprodwiseformulation['prod_code']};
			if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
			{
				array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
			}
		}
	  }
	  if($oils_val_array[$i]=='MCX_close_rate'){
		$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
									WHERE oils like 'MCX%' AND acedns='Y' AND prod_code 
									IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y') ORDER BY datetime DESC) AS SAT GROUP BY 1 ";																		
		$rsprodwiseformulation=mysqli_query($link,$sqlprodwiseformulation);
		while($rowprodwiseformulation=mysqli_fetch_assoc($rsprodwiseformulation))
		{  		
			${loosrate_calc_val_close.$rowprodwiseformulation['prod_code']}=($rowprodwiseformulation['formulation']*$loose_rate_ton_array[$i])/100;
			${loosrate_final_val_close.$rowprodwiseformulation['prod_code']}=${loosrate_final_val_close.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val_close.$rowprodwiseformulation['prod_code']};
			if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
			{
				array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
			}
		}
	  }
	}
//echo ${loosrate_final_val.'DV15KT000093'};
//echo '<br />';
//print_r($formulation_prod_array);
$cntprod=0;
foreach($formulation_prod_array as $formulation_prod_val)
{
	//echo $formulation_prod_val.'<br />';
	//$formulation_prod_val='ASV15KB00007';
	/*$sqlformulationbaseoilN="SELECT * FROM (SELECT oils,formulation,percentile_calc FROM loose_oilrate_formulation 
							WHERE  plant_name='".$plant_name."' AND acedns='Y' AND base_oil!='Y' AND prod_code='".$formulation_prod_val."' 
							ORDER BY datetime DESC) AS SAT GROUP BY 1 ";*/
	$sqlformulationbaseoilN="SELECT * FROM (SELECT oils,formulation,percentile_calc FROM loose_oilrate_formulation 
								WHERE  acedns='Y' AND prod_code='".$formulation_prod_val."' 
								AND percentile_calc='N' ORDER BY datetime DESC) AS SAT GROUP BY 1 ";						
	$rsformulationbaseoilN=mysqli_query($link,$sqlformulationbaseoilN);
	while($rowformulationbaseoilN=mysqli_fetch_assoc($rsformulationbaseoilN))
	{
		$oil_type=$rowformulationbaseoilN['oils'];
		$formulation=$rowformulationbaseoilN['formulation'];
		${percentile_calc.$formulation_prod_val}=$rowformulationbaseoilN['percentile_calc'];
		$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$oil_type."'  ORDER BY datetime DESC LIMIT 0,1";
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
			
			$sqlselprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$formulation_prod_val."'";
			$rsselprodcode=mysqli_query($link,$sqlselprodcode);
			$rowselprodcode=mysqli_fetch_assoc($rsselprodcode);
			$prod_code=$rowselprodcode['prod_code'];
			// For parent product rate insertion
			$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
								AS max_mrp_code from mcx_rate";
				$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
				$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
				$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
				$max_mrp_code++;
				$max_mrp_code='z'.$max_mrp_code;
				
				$sqlupdateparentMCX="UPDATE mcx_rate SET acedns='N' WHERE product_code='".$prod_code."'";
				$rsupdateparentMCX=mysqli_query($link,$sqlupdateparentMCX);
				$sqlinsertmrpprodwiseMCX="INSERT INTO mcx_rate SET mrp_code='".$max_mrp_code."',
										mrp='0',sale_rate_open='".${basic_rate_mcx_open_prodwise.$formulation_prod_val}."',
										sale_rate_close='".${basic_rate_mcx_close_prodwise.$formulation_prod_val}."',
										branch_code='',product_code='".$prod_code."',
										vertical_value='',
										basic_rate_open='".${loose_rate_case_mcx_open_prodwise.$formulation_prod_val}."',
										basic_rate_close='".${loose_rate_case_mcx_close_prodwise.$formulation_prod_val}."',
										process_cost='".${process_cost_case.$formulation_prod_val}."',
										labour_cost='".${labour_cost.$formulation_prod_val}."',
										packing_cost='".${packing_cost.$formulation_prod_val}."',
										margin_cost='".${margin_cost.$formulation_prod_val}."',
										extra_cost='".${extra_cost.$formulation_prod_val}."',
										packing_realization='".${packing_realization.$formulation_prod_val}."',
										acedns='Y',
										create_date=CURRENT_TIMESTAMP(),
										primary_freight	='0',depot_cost='0',download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertmrpprodwiseMCX))
				{
				  //For child product rate generation
				  $sqlfetchdependentprod="SELECT DISTINCT prod_code,add_subtract_val FROM product_unit_coversion_matrix WHERE 
										  mapped_prod_code='".$formulation_prod_val."' AND acedns='Y' AND prod_code!=mapped_prod_code 
										  AND prod_code IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y')";
				  $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
				  while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
				  {
					$dependent_prod_val=$rowfetchdependentprod['prod_code'];
					$add_subtract_val=$rowfetchdependentprod['add_subtract_val'];
					//echo $sale_rate[$n];
					//echo '<br />';
					$sqlconversiondependent="SELECT conversion_factor,conversion_factor_two,prod_desc,prod_code,UOM1,pack_size,packing_realization,UOM4,UOM5 
										FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
					$rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
					$rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
					${conversion_factor.$dependent_prod_val}=$rowconversiondependent['conversion_factor'];
					//echo '<br />';
					${conversion_factor_two.$dependent_prod_val}=$rowconversiondependent['conversion_factor_two'];
					${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];
					${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
					${UOM1.$dependent_prod_val}=$rowconversiondependent['UOM1'];
					${UOM4.$dependent_prod_val}=$rowconversiondependent['UOM4'];
					${UOM5.$dependent_prod_val}=$rowconversiondependent['UOM5'];
					${pack_size.$dependent_prod_val}=$rowconversiondependent['pack_size'];
					//echo '<br />';
					//echo $conversion_factor[$n];
					//echo '<br />';
					$sqlpackingdependent="SELECT packing_cost,labour_cost,extra_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$dependent_prod_val."' 
								AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
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
						${loose_rate_open_dependent.$dependent_prod_val}=${loose_rate_case_mcx_open_prodwise.$formulation_prod_val};
						${loose_rate_close_dependent.$dependent_prod_val}=${loose_rate_case_mcx_close_prodwise.$formulation_prod_val};
						${process_cost.$dependent_prod_val}=${process_cost_case.$formulation_prod_val};
					}
					else
					{
						${loose_rate_open_dependent.$dependent_prod_val}=(${loose_rate_case_mcx_open_prodwise.$formulation_prod_val}/${conversion_factor_two.$formulation_prod_val})*${conversion_factor_two.$dependent_prod_val};
						${loose_rate_close_dependent.$dependent_prod_val}=(${loose_rate_case_mcx_close_prodwise.$formulation_prod_val}/${conversion_factor_two.$formulation_prod_val})*${conversion_factor_two.$dependent_prod_val};
						${process_cost.$dependent_prod_val}=(${process_cost_case.$formulation_prod_val}/${conversion_factor_two.$formulation_prod_val})*${conversion_factor_two.$dependent_prod_val};
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
					$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
					$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
					$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
					$max_mrp_code++;
					$max_mrp_code='z'.$max_mrp_code;
					$sqlupdatechildMCX="UPDATE mcx_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
					$rsupdatechildMCX=mysqli_query($link,$sqlupdatechildMCX);
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
					mysqli_query($link,$sqlinsertmrpprodwiseMCXdepen);
	  		}
		 }

		/*$contents  = (($formulation_prod_val!='')?$formulation_prod_val: ' ')."^";
		$contents  .= ((${flashname.$formulation_prod_val}!='')?${flashname.$formulation_prod_val}: ' ')."^";
		$contents  .= ((${loose_rate_case_prodwise.$formulation_prod_val}!='')?${loose_rate_case_prodwise.$formulation_prod_val}: ' ')."^";
		$contents  .= ((${process_cost_case.$formulation_prod_val}!='')?${process_cost_case.$formulation_prod_val}: ' ')."^";
		$contents  .= ((${packing_realization.$formulation_prod_val}!='')?${packing_realization.$formulation_prod_val}: ' ')."^";
		$contents  .= ((${margin_cost.$formulation_prod_val}!='')?${margin_cost.$formulation_prod_val}: ' ')."^";
		$contents  .= ((${basic_rate_prodwise.$formulation_prod_val}!='')?${basic_rate_prodwise.$formulation_prod_val}: ' ');
		$linecontents  .= $contents."\n";*/
		
		$cntprod++;
		}
  	}
					
  }
	$sqlpublishedproddetails="SELECT PM.prod_code,PM.prod_desc,PM.dns_prod_code,SM.sale_rate_open,SM.sale_rate_close,SM.basic_rate_open,
	  							SM.basic_rate_close,SM.process_cost,SM.margin_cost,SM.packing_realization FROM product_master PM,mcx_rate SM 
							WHERE PM.prod_code=SM.product_code AND SM.acedns='Y' ORDER BY PM.prod_desc ASC";
	$rspublishedproddetails=mysqli_query($link,$sqlpublishedproddetails);
	$total_published_rows=mysqli_num_rows($rspublishedproddetails);
	if($total_published_rows> 0)
	{
		while($rowpublishedproddetails=mysqli_fetch_assoc($rspublishedproddetails))
		{
			$prod_code=$rowpublishedproddetails['dns_prod_code'];
		$prod_desc=$rowpublishedproddetails['prod_desc'];
		$sale_rate_open=$rowpublishedproddetails['sale_rate_open'];
		$sale_rate_close=$rowpublishedproddetails['sale_rate_close'];
		$basic_rate_open=$rowpublishedproddetails['basic_rate_open'];
		$basic_rate_close=$rowpublishedproddetails['basic_rate_close'];
		$process_cost=$rowpublishedproddetails['process_cost'];
		$margin_cost=$rowpublishedproddetails['margin_cost'];
		$packing_realization=$rowpublishedproddetails['packing_realization'];
		
		$contents  = (($prod_code!='')?$prod_code: ' ')."^";
		$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
		$contents  .= (($basic_rate_open!='')?$basic_rate_open: ' ')."^";
		$contents  .= (($basic_rate_close!='')?$basic_rate_close: ' ')."^";
		$contents  .= (($process_cost!='')?$process_cost: ' ')."^";
		$contents  .= (($packing_realization!='')?$packing_realization: ' ')."^";
		$contents  .= (($margin_cost!='')?$margin_cost: ' ')."^";
		$contents  .= (($sale_rate_open!='')?$sale_rate_open: ' ')."^";
		$contents  .= (($sale_rate_close!='')?$sale_rate_close: ' ');
		$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn  =$total_published_rows.'¥'.'9';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	 else
	 {
		 $datacontents = '0'.'¥'.'0';
	 }
 }
 else
 {
	 $datacontents = '0'.'¥'.'0';
 }

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url =APICALLLOGURL. "/rate-generation-download-MCX.php?nick_name=$nick_name&loose_rate_ton=$loose_rate_ton&oils_val=$oils_val";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=generated_rate_MCX.txt");
	print "$datacontents";	
	//mysqli_close($link);
?>
