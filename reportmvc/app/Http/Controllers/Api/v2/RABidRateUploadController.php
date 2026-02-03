<?php
namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Database\DbOnTheFly;
use App\Helpers\Apicommonfunction;
use Session;
use DB;


class RABidRateUploadController extends Controller{

	/**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */

    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }
    public function operationdbuploadbidrate(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
		$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $flag='';
        //$device_id=Apicommonfunction::decrypt($request->deviceid);
        //$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
		 //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
        //if($isverify==1){
			//$body=file_get_contents('php://input');
					$body=Apicommonfunction::decrypt($request->xmldata);

					$date=gmdate('d',strtotime('+330 minute'));
					$month=gmdate('m',strtotime('+330 minute'));
					$year=gmdate('Y',strtotime('+330 minute'));

					$hour=gmdate('H',strtotime('+330 minute'));
					$minute=gmdate('i',strtotime('+330 minute'));
					$second=gmdate('s',strtotime('+330 minute'));
					$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
					$plant_name_array=array();
					$bid_id_array=array();
					$prod_code_array=array();
					$indicative_rate_array=array();
					$bid_rate_array=array();
					$sqlInsertxml=$CUTDB->table('xml_data')
											->insert(array('xml'=>$body,));
					$CUTDB->beginTransaction();
					$xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach ($xml as $RA_bid_rate) {
						foreach ($RA_bid_rate->location as $location) {
							$emp_code=$location->emp_code;
							$trans_id=$location->trans_id;
							$latt=$location->latt;
							$longi=$location->longi;
							$date=$location->date;

							$sqlselectlocation=$CUTDB->table('location')
									->select('location.trans_id')
									->where('location.trans_id', '=' ,$trans_id)
									->first();
						   if(count($sqlselectlocation) >0){
							   $sqlupdatelocation=$CUTDB->table('location')
												   ->where('trans_id', $trans_id)
												   ->limit(1)
												   ->update(array('emp_code'=>$emp_code,'latt'=>$latt,'longi'=>$longi));
						   }
						   else{
							   $sqlInsertlocation=$CUTDB->table('location')
											->insert(array('emp_code'=>$emp_code,
											'trans_id'=>$trans_id,
											'latt'=>$latt,
											'longi'=>$longi,
											'date'=>$date,
											'updatetime'=>$location_date,
											));
							  if($sqlInsertlocation){
									$flag=5;
							  }
							  else{
									$CUTDB->rollBack();
 								  echo $flag=Apicommonfunction::encrypt('fail');
 								  return;
							  }
						   }
						}
						$countbid=0;
						foreach ($RA_bid_rate->RA_bid_rate_data as $RA_bid_rate_data) {
							foreach($RA_bid_rate_data->RA_bid_rate_details as $RA_bid_rate_details){
									$bid_id=$RA_bid_rate_details->bid_id;
									$plant_name=$RA_bid_rate_details->plant_name;
									$prod_code=$RA_bid_rate_details->prod_code;
									$released_rate=(float)$RA_bid_rate_details->released_rate;
									$base_rate=(float)$RA_bid_rate_details->base_rate;
									$server_indicative_rate=(float)$RA_bid_rate_details->server_indicative_rate;
									$app_indicative_rate=(float)$RA_bid_rate_details->app_indicative_rate;
									$customer_code=$RA_bid_rate_details->customer_code;
									$qty=$RA_bid_rate_details->qty;
									$bid_rate=(float)$RA_bid_rate_details->bid_rate;
									$primary_freight=(float)$RA_bid_rate_details->primary_freight;
									$secondary_freight=(float)$RA_bid_rate_details->secondary_freight;
									$depot_cost=(float)$RA_bid_rate_details->depot_cost;
									$GST_percent=(float)$RA_bid_rate_details->GST_percent;
									$GST_value=(float)$RA_bid_rate_details->GST_value;
									if($GST_value=='')  $GST_value=0;  
									$branch_code=$RA_bid_rate_details->branch_code;
									$incoterms=$RA_bid_rate_details->incoterms;
									$vertical_value=$RA_bid_rate_details->vertical_value;
									
									$without_freightdepot_bid_rate=$bid_rate-$primary_freight-$secondary_freight-$depot_cost-$GST_value;
									$without_freightdepot_bid_rate=round($without_freightdepot_bid_rate,0);
									if($without_freightdepot_bid_rate >=$base_rate)
									{
										$counter_bid='N';
										$bid_status='ACCEPT';
										$counter_bid_rate='';
										$counter_bid_threshold='';
										
										//For sauda creation start-------
										$emp_code=substr($bid_id,2,5);
										$sqlempdetails=$CUTDB->table('employee_master')
														->select('emp_code','emp_name')
														->where('emp_code',$emp_code)
														->first();
										$emp_name=$sqlempdetails->emp_name;
										
										$sqlcustomerdetails=$CUTDB->table('customer_master')
														->select('dns_customer_code','route_code','customer_name','sauda_validity_period','transport_mode','loadability_ton','state_code')
														->where('customer_code',$customer_code)
														->first();
										$customer_name=$sqlcustomerdetails->customer_name;
										$sauda_validity_period=$sqlcustomerdetails->sauda_validity_period;
										$route_code=$sqlcustomerdetails->route_code;
										$transport_mode=$sqlcustomerdetails->transport_mode;
										$loadability_ton=$sqlcustomerdetails->loadability_ton;
										$state_code=$sqlcustomerdetails->state_code;
										$dns_customer_code=$sqlcustomerdetails->dns_customer_code;
										
										$sqlroutedetails=$CUTDB->table('route_master')
														->select('route_name')
														->where('route_code',$route_code)
														->first();
										$route_name=$sqlroutedetails->route_name;				
										
										$sqlbranchdetails=$CUTDB->table('branch_master')
														->select('branch_name','branch_code','branch_state','is_plant','plant_name','dns_branch_code')
														->where('branch_code',$branch_code)
														->first();
										$branch_name=$sqlbranchdetails->branch_name;
										$branch_code=$sqlbranchdetails->branch_code;
										$branch_state=$sqlbranchdetails->branch_state;
										$is_plant=$sqlbranchdetails->is_plant;
										$plant_name=$sqlbranchdetails->plant_name;
										$dns_branch_code=$sqlbranchdetails->dns_branch_code;
										
										$sqlselectproduct=$CUTDB->select("SELECT PGM.product_group_name,PGM.product_group_code,PM.prod_desc,PM.UOM1,PM.UOM2,PM.UOM3,
										PM.conversion_factor,PM.conversion_factor_two,
										PM.prod_code,PM.branch_code,PM.dns_prod_code,PGM.formulation FROM product_master PM,product_group_master PGM 
										WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$prod_code."' AND PM.acedns='Y' 
										AND PM.branch_code='".$branch_code."'");		
										if(count($sqlselectproduct) >0){
											foreach ($sqlselectproduct as $key => $productdetails) {
												$prod_code_db=$productdetails->prod_code;
												$prod_desc=$productdetails->prod_desc;
												$product_group_code=$productdetails->product_group_code;
												$product_group_name=$productdetails->product_group_name;
												$UOM1=$productdetails->UOM1;
												$UOM2=$productdetails->UOM2;
												$UOM3=$productdetails->UOM3;
												$conversion_factor=$productdetails->conversion_factor;
												$conversion_factor_two=$productdetails->conversion_factor_two;
												$dns_prod_code=$productdetails->dns_prod_code;
												$formulation=$productdetails->formulation;
												//$branch_code=$rowproductdetails['branch_code'];
												$convert_qty_one=round(($qty*$conversion_factor),3);
												$convert_qty_two=round((($qty*$conversion_factor)/$conversion_factor_two),3);
											}
										}
										$trans_id_sauda=str_replace('RB','FT',$bid_id);
										$saudadate=date('Y-m-d',strtotime(substr($date,0,10)));
										$saudadate_diff_format=date('d-m-Y',strtotime(substr($date,0,10)));
										$contract_valid_from=date('Y-m-d',strtotime($saudadate));
										$valid_upto = date('Y-m-d',strtotime("+$sauda_validity_period days,$contract_valid_from"));
										$sauda_time=substr($bid_id,-6,2).':'.substr($bid_id,-4,2).':'.substr($bid_id,-2,2);
										
										if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='FOR PLANT')
										{
											if(strtoupper($incoterms)=='FOR DEPOT'){
												$sqlfreihgt=$CUTDB->table('branch_route_freight')
														->select('freight')
														->where('branch_code',$branch_code)
														->where('route_code',$route_code)
														->where('acedns','Y')
														->where('transport_mode',$transport_mode)
														->where('state_code',$state_code)
														->first();
												$freight=$sqlfreihgt->freight;
											}
											if(strtoupper($incoterms)=='FOR PLANT'){
												$sqlfreihgt=$CUTDB->table('branch_route_freight')
														->select('freight')
														->where('branch_code',$branch_code)
														->where('route_code',$route_code)
														->where('acedns','Y')
														->where('transport_mode',$transport_mode)
														->where('capacity',$loadability_ton)
														->where('state_code',$state_code)
														->first();
												$freight=$sqlfreihgt->freight;
											}
											$sqlqtytruckload=$CUTDB->table('load_distribution')
														->select('qty_truck_load')
														->where('transport_mode',$transport_mode)
														->where('truck_load',$loadability_ton)
														->where('prod_code',$prod_code)
														->orderBy('datetime', 'DESC')
														->take(1)
														->get();
											if(count($sqlqtytruckload) >0)
											{		
												foreach ($sqlqtytruckload as $key => $truckloaddetails) {			
													$qty_truck_load=$truckloaddetails->qty_truck_load;
												}
											}
											else $qty_truck_load=0;
											if($qty_truck_load >0 && $freight >0)
											{
												$freight_charge=round(($freight/$qty_truck_load),2);
											}
											else $freight_charge=0;
										}
										else $freight_charge=0;
										if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='EX DEPOT')
										{
											$sqlfreightcostprodwise=$CUTDB->table('freight_cost')
														->select('freight_cost')
														->where('dns_prod_code',$prod_code)
														->where('branch_code',$branch_code)
														->where('transport_mode',$transport_mode)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
											if(count($sqlfreightcostprodwise) >0)
											{		
												foreach ($sqlfreightcostprodwise as $key => $freightcostdetails) {			
													$freight_cost=round($freightcostdetails->freight_cost,2);
												}
											}
											else $freight_cost=0;			
														
											//$freight_cost=round($sqlfreightcostprodwise->freight_cost,2);
										}
										else
										{
											$freight_cost=0;
										}
										if($freight_cost=='')      $freight_cost=0;
										if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='EX DEPOT')
										{
											$sqldepotcostprodwise=$CUTDB->table('depot_cost')
														->select('depot_cost')
														->where('dns_prod_code',$prod_code)
														->where('branch_code',$branch_code)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
											if(count($sqldepotcostprodwise) >0)
											{		
												foreach ($sqldepotcostprodwise as $key => $depotdetails) {			
													$depot_cost=round($depotdetails->depot_cost,2);
												}
											}
											else $depot_cost=0;			
											//$depot_cost=round($sqldepotcostprodwise->depot_cost,2);
										}
										else   $depot_cost=0;
										$sqldetentioncostprodwise=$CUTDB->table('detention_cost')
														->select('detention_cost')
														->where('prod_code',$prod_code)
														->where('branch_code',$branch_code)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
										if(count($sqldetentioncostprodwise) >0)
											{		
												foreach ($sqldetentioncostprodwise as $key => $detentiondetails) {			
													$detention_cost=round($detentiondetails->detention_cost,2);
												}
											}
										else $detention_cost=0;					
										//$detention_cost=round($sqldetentioncostprodwise->detention_cost,2);
										if($detention_cost=='')
										{
											$detention_cost=0;
										}
										$sqlhoneycombcostprodwise=$CUTDB->table('honeycomb_cost')
														->select('honeycomb_cost')
														->where('prod_code',$prod_code)
														->where('plant_name',$plant_name)
														->where('transport_mode',$transport_mode)
														->where('state_code',$state_code)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
										if(count($sqlhoneycombcostprodwise) >0)
											{		
												foreach ($sqlhoneycombcostprodwise as $key => $honeycombdetails) {			
													$honeycomb_cost=round($honeycombdetails->honeycomb_cost,2);
												}
											}
										else $honeycomb_cost=0;						
										//$honeycomb_cost=round($sqlhoneycombcostprodwise->honeycomb_cost,2);				
										if($honeycomb_cost=='')
										{
											$honeycomb_cost=0;
										}
										$total_amount=($qty*($bid_rate+$freight_charge));
										if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='EX DEPOT' || strtoupper($incoterms)=='FOR PLANT')
										{
											if(strtoupper($incoterms)=='FOR DEPOT')  $FRC1=$freight_cost+$freight_charge+$depot_cost+$detention_cost;
											if(strtoupper($incoterms)=='EX DEPOT')   $FRC1=$freight_cost+$depot_cost+$detention_cost;
											if(strtoupper($incoterms)=='FOR PLANT')  $FRC1=$freight_charge+$detention_cost;
											
											$PR00=$bid_rate-$FRC1;
										}
										else if(strtoupper($incoterms)=='EX PLANT'){
											$PR00=$bid_rate;
											$FRC1=0;
										}
										$sqllooserate=$CUTDB->table('pricing_detials')
														->select('loose_rate_ton')
														->where('product_group_code',$product_group_code)
														->where('plant_name',$plant_name)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
										if(count($sqllooserate) >0)
											{		
												foreach ($sqllooserate as $key => $looseratedetails) {			
													$loose_rate_ton=$looseratedetails->loose_rate_ton;
												}
											}
										else $loose_rate_ton=0;					
										//$loose_rate_ton=$sqlhoneycombcostprodwise->loose_rate_ton;
										
										$sqlpackingprodwise=$CUTDB->table('packing_master')
														->select('packing_cost','packing_realization')
														->where('dns_prod_code',$prod_code)
														->where('plant_name',$plant_name)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
										if(count($sqlpackingprodwise) >0)
											{		
												foreach ($sqlpackingprodwise as $key => $packingdetails) {			
													$packing_cost=round($packingdetails->packing_cost,2);
												}
											}
										else $packing_cost=0;				
										//$packing_cost=round($sqlpackingprodwise->packing_cost,2);
										if($packing_cost=='')   $packing_cost=0;		
										
										$sqlmargincostprodwise=$CUTDB->table('margin_cost')
														->select('margin_cost')
														->where('dns_prod_code',$prod_code)
														->where('state_code',$state_code)
														->orderBy('datetime', 'DESC')														
														->take(1)
														->get();
										if(count($sqlmargincostprodwise) >0)
											{		
												foreach ($sqlmargincostprodwise as $key => $margindetails) {			
													$margin_cost=round($margindetails->margin_cost,2);
												}
											}
										else $margin_cost=0;				
										//$margin_cost=round($sqlmargincostprodwise->margin_cost,2);
										if($margin_cost=='')   $margin_cost=0;		
										$material_cost=$bid_rate-$packing_cost-$margin_cost-$FRC1-$honeycomb_cost;
										if($material_cost==0){
											$realization_per_case=0;
											$realization_per_MT=0;
										}
										else
										{
											$premium=0;
											$TD=0;
											$liquid_TD=0;
											
											$realization_per_case=$material_cost+$packing_cost+$margin_cost+$premium-$TD-$liquid_TD-$packing_cost;
											$realization_per_case=round($realization_per_case,2);
											$realization_per_MT=round((($realization_per_case*$conversion_factor_two)/$conversion_factor),2);
										}
										
										//For Insert into the location table for new trans id regarding sauda
										$sqlselectlocationsauda=$CUTDB->table('location')
												->select('location.trans_id')
												->where('location.trans_id', '=' ,$trans_id_sauda)
												->first();
									   if(count($sqlselectlocationsauda)==0){
										$sqlInsertlocationsauda=$CUTDB->table('location')
											->insert(array('emp_code'=>$emp_code,
											'trans_id'=>$trans_id_sauda,
											'latt'=>$latt,
											'longi'=>$longi,
											'date'=>$date,
											'updatetime'=>$location_date,
											));
										//For Insert into the Sauda Header table for new trans id
										$sqlinsertsaudaheader=$CUTDB->table('sauda_header')
											->insert(array('sauda_no'=>$trans_id_sauda,
											'customer_code'=>$customer_code,
											'TD'=>'',
											'broker_id'=>'',
											'VAT'=>'',
											'transaction_type'=>'OB',
											 'sauda_valid_from'=>$contract_valid_from,
											 'sauda_type'  =>'RAA',
											));
										if($sqlInsertlocationsauda && $sqlinsertsaudaheader)
										{
											$flag=5;
										}
										else
										{
											$CUTDB->rollBack();
											echo $flag=Apicommonfunction::encrypt('fail');
											return;
										}
									   }
										$sqlinsertsaudadetails=$CUTDB->table('sauda_details')
											->insert(array('sauda_no'=>$trans_id_sauda,
											'sku_code'=>$prod_code_db,
											'qty'=>$qty,
											'convert_qty_one'=>$conversion_factor,
											'convert_qty_two'=>$conversion_factor_two,
											'TD'=>'',
											'premium'=>'',
											'sale_rate'=>$bid_rate,
											'VAT'	=>'',
											'freight_charge' =>$freight_charge,
											'primary_freight' =>$freight_cost,
											'depot_cost' 	=>$depot_cost,
											'honeycomb_cost' 	=>$honeycomb_cost,
											'brokerage_cost' 	=>0,
											'amount'		=>round($total_amount,2),
											'liquid_TD'	=>'',
											'mrp_code'	=>'',
											));
											if($sqlinsertsaudadetails)
											{
											  $flag=5;
											  if(strtoupper($vertical_value)=='HBC:RASOI:BIB'){
												  	$sqlupdatecustomersaudalimit=$CUTDB->table('customer_sauda_limit')
												   ->where('customer_code', $dns_customer_code)
												   ->limit(1)
												   ->update(array('pending_qty'=>('pending_qty'+$convert_qty_two),'download_time'=>$location_date));
												   
												   $sqlupdatecustomer=$CUTDB->table('customer_master')
												   ->where('dns_customer_code', $dns_customer_code)
												   ->limit(1)
												   ->update(array('download_time'=>$location_date));
												}
											}
											else
											{
												$CUTDB->rollBack();
												echo $flag=Apicommonfunction::encrypt('fail');
												return;
											}
											
											$sqlselsuada=$CUTDB->table('sauda_download_log')
														->select('sauda_no','prod_code')
														->where('sauda_no',$trans_id_sauda)
														->where('prod_code',$prod_code)
														->first();
											if(count($sqlselsuada) ==0)
											{
												
												$sqlinsertsaudadownloadlog=$CUTDB->table('sauda_download_log')
												->insert(array('customer_code'=>$dns_customer_code,
												'customer_name'=>$customer_name,
												'route_name'=>$route_name,
												'broker_id'=>'',
												'broker_name'=>'',
												'sauda_no'=>$trans_id_sauda,
												'sauda_date'=>$saudadate,
												'sauda_time'=>$sauda_time,
												'contract_valid_from'=>$contract_valid_from,
												'contract_valid_to'=>$valid_upto,
												'prod_code'=>$prod_code,
												'qty'=>$qty,
												'convert_qty_two'=>$convert_qty_two,
												'UOM'=>$UOM1,
												'product_group_code'=>$product_group_code,
												'product_group_name'=>$product_group_name,
												'prod_desc'=>addslashes($prod_desc),
												'branch_code'=>$branch_code,
												'branch_name'=>addslashes($branch_name),
												'state'=>addslashes($branch_state),
												'material_cost'=>$material_cost,
												'primary_freight'=>$freight_cost,
												'packing_cost'=>$packing_cost,
												'honeycomb_cost'=>$honeycomb_cost,
												'detention_charges'=>$detention_cost,
												'brokerage_cost'=>'',
												'depot_cost'=>$depot_cost,
												'margin_cost'=>$margin_cost,
												'freight_charge'=>$freight_charge,
												'TD'=>'',
												'liquid_TD'=>'',
												'premium'=>'',
												'PR00'=>$PR00,
												'FRC1'=>$FRC1,
												'amount'=>round($total_amount,2),
												'incoterms'=>$incoterms,
												'emp_name'=>addslashes($emp_name),
												'payment_due_on'=>'',
												'cm_credit_limit'=>'',
												'remarks'=>'',
												'vertical'=>$vertical_value,
												'realization_per_case'=>$realization_per_case,
												'realization_per_MT'=>$realization_per_MT,
												'sale_rate'=>$bid_rate,
												'packing_realization'=>$packing_cost,
												'sauda_type'=>'RAA',
												'download_time'=>$location_date,
												));
											if($sqlinsertsaudadownloadlog)
											{
											   $flag=5;
											}
											else
											{
												$CUTDB->rollBack();
												echo $flag=Apicommonfunction::encrypt('fail');
												return;
											}
										}
									   //Sauda creation end------------
									}
									else
									{
										$sqlselectcounterbiddetails=$CUTDB->select("SELECT counter_bid_jump,counter_bid_limit FROM plant_product_wise_RA_rate 
																WHERE plant_name= '".$plant_name."' AND 	prod_code='".$prod_code."' AND acedns='Y'");		
										if(count($sqlselectcounterbiddetails) >0){
											foreach ($sqlselectcounterbiddetails as $key => $counterbiddetails) {
												$counter_bid_jump=$counterbiddetails ->counter_bid_jump;
												$counter_bid_limit=$counterbiddetails ->counter_bid_limit;
											}
										}
										else
										{
											$counter_bid_jump=0;
											$counter_bid_limit=0;
										}
										//$without_freight_rate=(float)($indicative_rate-$freight_value);
										$counter_bid_threshold=($base_rate-($base_rate*$counter_bid_limit));
										$counter_bid_threshold=(float)round($counter_bid_threshold,0);
										//$counter_bid_threshold=(float)($without_freight_rate-(($without_freight_rate*$counter_bid_limit)/100));
										if($without_freightdepot_bid_rate >=$counter_bid_threshold)
										{
											$counter_bid='Y';
											$bid_status='';
											//$counter_bid_rate=$without_freightdepot_bid_rate+(($without_freightdepot_bid_rate*$counter_bid_jump)/100)+$primary_freight+$secondary_freight+$depot_cost;
											$counter_bid_rate=$base_rate+(($base_rate*$counter_bid_jump)/100)+$primary_freight+$secondary_freight+$depot_cost;
											if($GST_percent >0){
												$counter_bid_rate=$counter_bid_rate+(($counter_bid_rate*$GST_percent)/100);
											}
											//$counter_bid_rate=$counter_bid_rate+$primary_freight+$secondary_freight+$depot_cost;
											$counter_bid_rate=round($counter_bid_rate,0);
										}
										else
										{
											$counter_bid='N';
											$bid_status='REJECT';
											$counter_bid_rate='';
										}
									}
									//echo $counter_bid.'<br />';
									//echo $bid_status.'<br />';
									//echo $counter_bid_rate.'<br />';
									
									$sqlselectbidrate=$CUTDB->table('RA_bid_rate_details')
									  ->select('RA_bid_rate_details.bid_id')
									  ->where('RA_bid_rate_details.bid_id', '=' ,$bid_id)
									   ->where('RA_bid_rate_details.prod_code', '=' ,$prod_code)
									  ->first();
						   			if(count($sqlselectbidrate)==0){
										$sqlInsertbidrate=$CUTDB->table('RA_bid_rate_details')
											->insert(array('bid_id'=>$bid_id,
											'plant_name'=>$plant_name,
											'prod_code'=>$prod_code,
											'released_rate'=>$released_rate,
											'base_rate'=>$base_rate,
											'server_indicative_rate'=>$server_indicative_rate,
											'app_indicative_rate'=>$app_indicative_rate,
											'customer_code'=>$customer_code,
											'qty'=>$qty,
											'bid_rate'=>$bid_rate,
											'counter_bid'=>$counter_bid,
											'counter_bid_threshold'=>$counter_bid_threshold,
											'counter_bid_rate'=>$counter_bid_rate,
											'bid_status'=>$bid_status,
											'primary_freight'=>$primary_freight,
											'secondary_freight'=>$secondary_freight,
											'depot_cost'=>$depot_cost,
											'GST_percent'=>$GST_percent,
											'GST_value'=>$GST_value,
											'branch_code'=>$branch_code,
											'vertical_value'=>$vertical_value,
											'incoterms'=>$incoterms,
											'download_time'=>$location_date,
											));
									  if($sqlInsertbidrate){
											$flag=5;
											/*array_push($plant_name_array,$plant_name);
											array_push($bid_id_array,$bid_id);
											array_push($prod_code_array,$prod_code);
											array_push($indicative_rate_array,$indicative_rate);
											array_push($bid_rate_array,$bid_rate);*/
										}
									  else {
										$CUTDB->rollBack();
										echo $flag=Apicommonfunction::encrypt('fail');
										return;
									  }
									}
									$countbid++;
								}
						 	}
						}// End of foreach of xml
					if($flag==5){
						$CUTDB->commit();
						echo Apicommonfunction::encrypt('success');
					}
				/*}// End of verify if
				else{
					echo Apicommonfunction::encrypt('404');
				}*/
		    $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        	$url = url('/api/v1/RAbidrateupload?nick_name='.$nick_name.'&emp_code='.$emp_code);
        	Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
    }
}
