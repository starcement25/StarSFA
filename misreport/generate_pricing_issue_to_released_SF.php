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
function main(){
$today = date('Y-m-d');
$condition = " SUBSTRING(PD.datetime,1,10)='".$today."' ";
function plant_sort($a, $b) {
    if($a==$b) return $a;
}
$count = 1;
$plantnamearray = array();
$plant_name_array=array();
$loose_rate_ton_array=array();
$product_group_name_array=array();
$product_group_code_array=array();
$pd_date_array=array();
$pd_time_array=array();
$pd_datetime_array=array();

$sql_check_record = "SELECT * FROM(SELECT PD.plant_name,PD.loose_rate_ton,DATE_FORMAT(SUBSTRING(PD.datetime,1,10),'%d-%m-%Y') as pd_date, SUBSTRING(PD.datetime,12) as pd_time,PD.datetime FROM pricing_detials PD WHERE ".$condition." AND PD.price_generated='no' 
AND PD.vertical_value='".$_SESSION['vertical_value']."' ORDER BY PD.datetime DESC) AS SAT GROUP BY 1 ORDER BY 1 ASC,8 DESC";
$res_check_record = mysqli_query($link,$sql_check_record);
$total_rows = mysqli_num_rows($res_check_record);
//if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center">
      <tr class="TDHEAD" align="center" id="head_main">
      	<td>SI</td>
        <td>Date</td>
        <td>Time</td>
        <td>Plant name</td>
        <td>Loose Rate</td>
        <td>Confirmation</td>
      </tr>
    <?php
	$res_check_record = mysqli_query($link,$sql_check_record);
	while($row_check_record = mysqli_fetch_assoc($res_check_record)){
		$plant_name = $row_check_record['plant_name'];
		$loose_rate_ton = $row_check_record['loose_rate_ton'];
		$pd_date = $row_check_record['pd_date'];
		$pd_time = $row_check_record['pd_time'];
		$pd_datetime_combined=$pd_date.' '.$pd_time;
		
		array_push($plant_name_array,$plant_name);
		array_push($loose_rate_ton_array,$loose_rate_ton);
		array_push($pd_date_array,$pd_date);
		array_push($pd_time_array,$pd_time);
		array_push($pd_datetime_array,$pd_datetime_combined);
	}
//}
/*else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}*/
$sql_check_formulation_record="SELECT PD.plant_name,group_concat(concat(PD.oils,':',PD.oils_rate) separator '#') AS loose_rate_ton,DATE_FORMAT(SUBSTRING(PD.datetime,1,10),'%d-%m-%Y') as pd_date, SUBSTRING(PD.datetime,12) as pd_time FROM pricing_detials_formulation PD WHERE ".$condition." AND PD.price_generated='no' AND PD.vertical_value='".$_SESSION['vertical_value']."' GROUP BY PD.plant_name,price_generation_id ORDER BY PD.plant_name ASC,SUBSTRING(PD.datetime,1,19) DESC";
$res_check_record_formulation = mysqli_query($link,$sql_check_formulation_record);
$total_rows_formulation = mysqli_num_rows($res_check_record_formulation);
while($row_check_record_formulation = mysqli_fetch_assoc($res_check_record_formulation)){
		$plant_name = $row_check_record_formulation['plant_name'];
		//$oils=$row_check_record_formulation['oils'];
		//$loose_rate_ton = $oils.' : '.$oils_rate.'#';
		$loose_rate_ton = $row_check_record_formulation['loose_rate_ton'];
		$pd_date = $row_check_record_formulation['pd_date'];
		$pd_time = $row_check_record_formulation['pd_time'];
		$pd_datetime_combined=$pd_date.' '.$pd_time;
		
		array_push($plant_name_array,$plant_name);
		array_push($loose_rate_ton_array,$loose_rate_ton);
		array_push($pd_date_array,$pd_date);
		array_push($pd_time_array,$pd_time);
		array_push($pd_datetime_array,$pd_datetime_combined);
	}
	//asort($plant_name_array);
	//print_r($plant_name_array);
	for($i=0;$i<count($plant_name_array);$i++)
	{
		if(strpos($loose_rate_ton_array[$i],'#')!=false){
			$loose_rate_ton_split=explode('#',$loose_rate_ton_array[$i]);
			$loose_rate_ton_final='';
			foreach($loose_rate_ton_split as $loose_rate_ton_val)
			{
				$loose_rate_ton_final=$loose_rate_ton_final.$loose_rate_ton_val.'<br />';
			}
		}
		else $loose_rate_ton_final=$loose_rate_ton_array[$i];
		
		/*if(!in_array($plant_name_array[$i],$plantnamearray)){
			array_push($plantnamearray,$plant_name_array[$i]);
			
			echo "<tr class=\"TDHEAD_SUB\"><td align=\"center\" colspan=\"5\">".$plant_name_array[$i]."</td></tr>";
		}*/
		echo "<tr id=\"tab".$count."\">
				<td>".$count."</td>
				<td>".$pd_date_array[$i]."</td>
				<td>".$pd_time_array[$i]."</td>
				<td>".$plant_name_array[$i]."</td>
				<td align=\"right\">".$loose_rate_ton_final."</td>
				<td ><input type=\"button\" name=\"butoon_$count\" value=\"CONFIRM\" onClick=\"javascript: price_confirmation(
				'".$plant_name_array[$i]."','".$loose_rate_ton_array[$i]."')\" ></td>
			  </tr>";
		$count++;
	}
	echo "</table>";
	?>
	<script language="javascript" type="text/javascript">
function price_confirmation(plant_name,loose_rate_ton)
{
	var plant_name = encodeURIComponent(plant_name);
	var loose_rate_ton = encodeURIComponent(loose_rate_ton);
	window.location.href='generate_pricing_issue_to_released_SF.php?plant_name='+plant_name+'&loose_rate_ton='+loose_rate_ton+'&mode=generateprice';
}
</script>
<?php
if($_REQUEST['mode']=='generateprice'){
	$plant_name=$_REQUEST['plant_name'];
	$loose_rate_ton=$_REQUEST['loose_rate_ton'];
	$current_date=date('Y-m-d');
	//exit();
	//For formulation=yes
	if(strpos($loose_rate_ton,'#')!=false){
		$loose_rate_ton_split=explode('#',$loose_rate_ton);
		$formulation_prod_array=array();
		foreach($loose_rate_ton_split as $oils_val)
		{
			//$loose_rate_ton_final=$loose_rate_ton_final.$loose_rate_ton_val.'<br />';
			$oils_val_split=explode(":",$oils_val);
			$sqlprodwiseformulation="SELECT * FROM (SELECT prod_code,formulation FROM loose_oilrate_formulation 
									 WHERE  plant_name='".$plant_name."' AND oils='".$oils_val_split[0]."' AND acedns='Y' AND prod_code 
										IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y' AND 
										vertical_value='".$_SESSION['vertical_value']."') ORDER BY datetime DESC) AS SAT GROUP BY 1 ";
			$rsprodwiseformulation=mysqli_query($link,$sqlprodwiseformulation);
			while($rowprodwiseformulation=mysqli_fetch_assoc($rsprodwiseformulation))
			{	/*echo 	$rowprodwiseformulation['prod_code'];
				echo '<br />';
				echo 'loose rate input-'.$oils_val_split[1];
				echo '<br />';
				echo 'formulation-'.$rowprodwiseformulation['formulation'];
				echo '<br />';*/			
				 ${loosrate_calc_val.$rowprodwiseformulation['prod_code']}=(substr($rowprodwiseformulation['formulation'],0,-1)
				*$oils_val_split[1])/100;
				
				${loosrate_final_val.$rowprodwiseformulation['prod_code']}=${loosrate_final_val.$rowprodwiseformulation['prod_code']}+${loosrate_calc_val.$rowprodwiseformulation['prod_code']};
				if(!in_array($rowprodwiseformulation['prod_code'],$formulation_prod_array))
				{
					array_push($formulation_prod_array,$rowprodwiseformulation['prod_code']);
				}
			}
		}
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
			$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
			$rsbranchname=mysqli_query($link,$sqlbranchname);
			$rowbranchname=mysqli_fetch_assoc($rsbranchname);
			$branch_name=$rowbranchname['branch_name'];
			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 
										prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND vertical_value='".$_SESSION['vertical_value']."' 
										AND dns_prod_code IN(SELECT DISTINCT prod_code FROM loose_oilrate_formulation WHERE acedns='Y') AND 
										branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
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
				//exit();
				//${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${honeycomb_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${mrp_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${freight_cost.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${mrp_prodwise.$distinct_dnsprod_code}=round(${mrp_prodwise.$distinct_dnsprod_code},2);
				
				//${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${margin_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${basic_rate_prodwise.$distinct_dnsprod_code}=${loose_rate_case_prodwise.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${detention_cost.$distinct_dnsprod_code};
				${basic_rate_prodwise.$distinct_dnsprod_code}=round(${basic_rate_prodwise.$distinct_dnsprod_code},2);
				$primary_freight=round(${freight_cost.$distinct_dnsprod_code},2);
				$depot_cost=${depot_cost.$distinct_dnsprod_code};
				
				$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code."' AND 
								dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";
				$rs_prod_code=mysqli_query($link,$sql_prod_code);
				$cntprod_code=mysqli_num_rows($rs_prod_code);
				$row_prod_code=mysqli_fetch_assoc($rs_prod_code);
				$prod_code_master=$row_prod_code['prod_code'];
				$vertical_value_master=$row_prod_code['vertical_value'];
				if($cntprod_code >0)
				{
				  $sqlbranchprodchk="SELECT product_code FROM sauda_mrp WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
				  $rsbranchprodchk=mysqli_query($link,$sqlbranchprodchk);
				  $cntbranchprodchk=mysqli_num_rows($rsbranchprodchk);
				   if($cntbranchprodchk >0)						{
						$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='".${mrp_prodwise.$distinct_dnsprod_code}."',
											sale_rate='".${mrp_prodwise.$distinct_dnsprod_code}."',
											basic_rate='".${basic_rate_prodwise.$distinct_dnsprod_code}."',primary_freight	='".$primary_freight."',
											depot_cost='".$depot_cost."',download_time=CURRENT_TIMESTAMP()
										WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
						mysqli_query($link,$sqlupdatemrpprodwise);
					}
					else
						{
							$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
											AS max_mrp_code from sauda_mrp";
							$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
							$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
							$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
							$max_mrp_code++;
							$max_mrp_code='z'.$max_mrp_code;

						   $sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',
						   						mrp='".${mrp_prodwise.$distinct_dnsprod_code}."',sale_rate='".${mrp_prodwise.$distinct_dnsprod_code}."',
						   						branch_code='".$distinct_branch_code."',product_code='".$prod_code_master."',
												vertical_value='".$vertical_value_master."',
												basic_rate='".${basic_rate_prodwise.$distinct_dnsprod_code}."',
												primary_freight	='".$primary_freight."',depot_cost='".$depot_cost."',download_time=CURRENT_TIMESTAMP()";
						   mysqli_query($link,$sqlinsertmrpprodwise);
						}
					}
					$flag=1;
			}
		}
		if($flag==1){
				$sqlupdatepricegenflag="UPDATE pricing_detials_formulation SET price_generated='yes' WHERE product_group_code='' 
									AND plant_name='".$plant_name."' AND SUBSTRING(datetime,1,10)='".$current_date."' AND 
									vertical_value='".$_SESSION['vertical_value']."'";
				mysqli_query($link,$sqlupdatepricegenflag);						
			?>
			   <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='SF_pricelist.php?plant_name=<?php echo $plant_name;?>';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Price generation unsuccessful.');window.location.href='generate_pricing_issue_to_released_SF.php';</script>
            <?php
            }
	}// END of formulation=yes
}
mysqli_close($link);
}?>