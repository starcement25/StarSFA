<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Helpers\Reverseauction;
use App\Helpers\Route;
use App\Helpers\Commonfunctions;
use Session;

class RACustomerBidReportController extends Controller
{
    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $sdbname =$this->databasename();
        return view('route.index',compact('routes'));
    }

    /**
     * [customerbidreports This function return the customer and date wise bid details]
     * @return [type] [description]
     */
    public function customerbidreports(){
      $dbname =$this->databasename();
      $customerlist =Route::getCustomerlist($dbname);
	  //$customerlist ='';
      $reoprtdata='';
      return view('RAbid.RAcustomerbidreport',compact('customerlist','reoprtdata'));
    }
    public function showcustomerbidreports(){
      $dbname =$this->databasename();

      $empid=Input::get('emp_list');
      $newempid=implode("','",$empid);
      $startdate=date('Y-m-d',strtotime(Input::get('start_date')));
      $enddate=date('Y-m-d',strtotime(Input::get('end_date')));
      $emplist =Route::getEmplist($dbname);
      $reoprtdata=Route::getRouteReport($dbname,$newempid,$startdate,$enddate);
      return view('route.routeplanreport',compact('emplist','reoprtdata'));
    }
	public function bidcenterreports(){
      $dbname =$this->databasename();
      $reoprtdata='';
      return view('RAbid.bidcenterreport',compact('reoprtdata'));
    }
	public function showbidcenterreports(){
      $dbname =$this->databasename();
	  $startdate=date('Y-m-d',strtotime(Input::get('start_date')));
	  //$customerlist =Route::getCustomerlist($dbname);
      $reoprtdata=Reverseauction::getBidCenterReport($dbname,$startdate);
      return view('RAbid.bidcenterreport',compact('reoprtdata'));
    }
   public function bidcenterexportcsv(Request $request)
    {
        $dbname =$this->databasename();
      	$CUTDB = Reverseauction::dydb($dbname);

 		 $startdate=date('Y-m-d',strtotime(Input::get('start_date')));		
		 $reportlist=$CUTDB->select("SELECT DISTINCT RAD.*,PM.prod_desc,CM.customer_name,CM.dns_customer_code,DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%d-%m-%Y') AS bid_date,BM.branch_name,PM.conversion_factor,PM.conversion_factor_two,SM.state,EM.emp_name FROM `product_master` PM,RA_bid_rate_details RAD,customer_master CM,
		branch_master BM,state_master SM,employee_master EM WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code AND RAD.branch_code=BM.branch_code 
		 AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y-%m-%d')='".$startdate."' AND SM.dns_state_code=CM.state_code AND PM.acedns='Y' AND SUBSTRING(RAD.bid_id,3,5)=EM.emp_code ORDER BY RAD.bid_status ASC,CM.customer_name ASC");
        $data = "Date,Customer code,customer name,State,Prod Desc,Bid qty(CASE),Bid qty(MT),Bid rate,Base rate,Freight,GST,counter bid rate,status,Incoterms,Bid Done By"."\n";
        foreach($reportlist as $report) {
                $bid_date=$report->bid_date;
				$customer_name=$report->customer_name;
				$dns_customer_code=$report->dns_customer_code;
				$state=$report->state;
				$prod_desc=$report->prod_desc;
				$qty=$report->qty;
				$bid_rate=$report->bid_rate;
				$base_rate=$report->base_rate;
				$freight=($report->primary_freight+$report->secondary_freight+$report->depot_cost);
				$GST_value=$report->GST_value;
				$margin_cost=$report->margin_cost;
				$counter_bid_rate=$report->counter_bid_rate;
				$bid_status=$report->bid_status;
				if($bid_status =='') $bid_status='CB';
				else				  $bid_status=$bid_status;
				$final_base_rate=($base_rate +$margin_cost);
				$incoterms=$report->incoterms;
				$branch_name=$report->branch_name;
				$emp_name=$report->emp_name;
				$convert_qty_MT=round((($report->qty*$report->conversion_factor)/$report->conversion_factor_two),3);
				
    $data .=$bid_date.",".$dns_customer_code.",".$customer_name.",".$state.",".$prod_desc.",".$qty.",".$convert_qty_MT.",".$bid_rate.",".$final_base_rate.",".$freight.",".$GST_value.",".$counter_bid_rate.",".
				$bid_status.",".$incoterms." (".$branch_name.")".",".$emp_name."\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=Bidcenter.csv');
        echo $data;
        exit();
    }
	public function bidstatuschange(Request $request){
      $dbname =$this->databasename();
	  $CUTDB = Reverseauction::dydb($dbname);
	  
	  $startdate=date('Y-m-d',strtotime(Input::get('startdateval')));
	  $bid_id=$request->input('bid_id');
	  $prod_code=$request->input('prod_code');
	  $bidstatus=$request->input('bidstatus');
	  //$customerlist =Route::getCustomerlist($dbname);
	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	  $date=$year.'-'.$month.'-'.$date;
	  $latt=0;
	  $longi=0;

	  if(strtoupper($bidstatus)=='ACCEPT'){
		$sqlbiddetailsval=$CUTDB->select("SELECT primary_freight,secondary_freight,depot_cost,GST_percent,GST_value,branch_code,incoterms,vertical_value,margin_cost,honeycomb_cost,customer_code,
		bid_rate,qty,detention_cost FROM RA_bid_rate_details WHERE bid_id='".$bid_id."' AND prod_code='".$prod_code."'");
		if(count($sqlbiddetailsval) >0){
			foreach ($sqlbiddetailsval as $key => $sqlbiddetails) {
				$customer_code=$sqlbiddetails->customer_code;		
				$primary_freight=(float)$sqlbiddetails->primary_freight;
				$secondary_freight=(float)$sqlbiddetails->secondary_freight;
				$depot_cost=(float)$sqlbiddetails->depot_cost;
				$GST_percent=(float)$sqlbiddetails->GST_percent;
				$GST_value=(float)$sqlbiddetails->GST_value;
				if($GST_value=='')  $GST_value=0;  
				$branch_code=$sqlbiddetails->branch_code;
				$incoterms=$sqlbiddetails->incoterms;
				$vertical_value=$sqlbiddetails->vertical_value;
				$margin_cost_RA=(float)$sqlbiddetails->margin_cost;
				$honeycomb_cost_RA=(float)$sqlbiddetails->honeycomb_cost;
				$detention_cost_RA=(float)$sqlbiddetails->detention_cost;
				//$date=$sqlbiddetails->bid_date;
				$date=$date;
				$bid_rate=$sqlbiddetails->bid_rate;
				$qty=$sqlbiddetails->qty;
			};						
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
			$sqlstatedetails=$CUTDB->table('state_master')
							->select('state')
							->where('dns_state_code',$state_code)
							->first();
			$state_name=$sqlstatedetails->state;
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
				/*echo strtoupper($incoterms);
				echo $branch_code;
				echo $route_code;
				echo $transport_mode;
				echo $state_code;
				exit();*/
				
				if(strtoupper($incoterms)=='FOR DEPOT'){
					$sqlfreihgt=$CUTDB->table('branch_route_freight')
							->select('freight','capacity')
							->where('branch_code',$branch_code)
							->where('route_code',$route_code)
							->where('acedns','Y')
							->where('transport_mode',$transport_mode)
							->where('state_code',$state_code)
							->where('vertical_value','HBC:Rasoi:BIB')
							->first();
					$freight=$sqlfreihgt->freight;
					$loadability_ton=$sqlfreihgt->capacity;
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
							->where('vertical_value','HBC:Rasoi:BIB')
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
				else
				{
					$freight_charge=0;
				}
				//$freight_charge=$secondary_freight;
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
			//$total_amount=($qty*($bid_rate+$freight_charge));
			$total_amount=($qty*($bid_rate));
			if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='EX DEPOT' || strtoupper($incoterms)=='FOR PLANT')
			{
				if(strtoupper($incoterms)=='FOR DEPOT')  $FRC1=$freight_cost+$freight_charge+$depot_cost+$detention_cost_RA;
				if(strtoupper($incoterms)=='EX DEPOT')   $FRC1=$freight_cost+$depot_cost+$detention_cost_RA;
				if(strtoupper($incoterms)=='FOR PLANT')  $FRC1=$freight_charge+$detention_cost_RA;
				
				$PR00=$bid_rate-$FRC1-$GST_value;
			}
			else if(strtoupper($incoterms)=='EX PLANT'){
				$PR00=$bid_rate-$GST_value;
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
						$packing_realization=round($packingdetails->packing_realization,2);
					}
				}
			else {
				 $packing_cost=0;
				 $packing_realization=0;
				}				
			//$packing_cost=round($sqlpackingprodwise->packing_cost,2);
			if($packing_cost=='')   $packing_cost=0;
			if($packing_realization=='')
			{
				$packing_realization=0;
			}		
			/*$sqlmargincostprodwise=$CUTDB->table('margin_cost')
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
				}*/
			$margin_cost=0;				
			//$margin_cost=round($sqlmargincostprodwise->margin_cost,2);
			if($margin_cost=='')   $margin_cost=0;		
			$material_cost=$bid_rate-$packing_cost-$margin_cost_RA-$FRC1-$honeycomb_cost_RA-$GST_value;
			if($material_cost==0){
				$realization_per_case=0;
				$realization_per_MT=0;
			}
			else
			{
				$premium=0;
				$TD=0;
				$liquid_TD=0;
				
			$realization_per_case=$material_cost+$packing_cost+$margin_cost+$margin_cost_RA+$premium-$TD-$liquid_TD-$packing_realization;
			$realization_per_case=round($realization_per_case,2);
			$realization_per_MT=round((($realization_per_case*$conversion_factor_two)/$conversion_factor),2);
			}
			
			$sale_rate=$bid_rate-$GST_value;
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
				'updatetime'=>$datetime,
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
				 'sauda_type'  =>'RA',
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
				'sale_rate'=>$sale_rate,
				'VAT'	=>'',
				'freight_charge' =>$freight_charge,
				'primary_freight' =>$freight_cost,
				'depot_cost' 	=>$depot_cost,
				'honeycomb_cost' 	=>$honeycomb_cost_RA,
				'margin_cost' 	=>$margin_cost,
				'margin_cost_RA' 	=>$margin_cost_RA,
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
					   ->update(array('pending_qty'=>('pending_qty'+$convert_qty_two),'download_time'=>$datetime));
					   
					   $sqlupdatecustomer=$CUTDB->table('customer_master')
					   ->where('dns_customer_code', $dns_customer_code)
					   ->limit(1)
					   ->update(array('download_time'=>$datetime));
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
					'branch_code'=>$dns_branch_code,
					'branch_name'=>addslashes($branch_name),
					'state'=>addslashes($state_name),
					'material_cost'=>$material_cost,
					'primary_freight'=>$freight_cost,
					'packing_cost'=>$packing_cost,
					'honeycomb_cost'=>$honeycomb_cost_RA,
					'detention_charges'=>$detention_cost_RA,
					'brokerage_cost'=>'',
					'depot_cost'=>$depot_cost,
					'margin_cost'=>$margin_cost,
					'margin_cost_RA'=>$margin_cost_RA,
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
					'sale_rate'=>$sale_rate,
					'packing_realization'=>$packing_realization,
					'sauda_type'=>'RA',
					'download_time'=>$datetime,
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
		}
	 }
	//Sauda creation end------------
	  $CUTDB->table('RA_bid_rate_details')
					->where('bid_id', $bid_id)
					->where('prod_code', $prod_code)
					->update(array('bid_status' => $bidstatus,'download_time'  => $datetime));
     $reoprtdata=Reverseauction::getBidCenterReport($dbname,$startdate);
     return view('RAbid.bidcenterreport',compact('reoprtdata'))->with('successMsg','Updated Successfully!.');
    }
	public function smsreports(){
      $dbname =$this->databasename();
      $reoprtdata='';
      return view('RAbid.smsreport',compact('reoprtdata'));
    }
	public function showsmsreports(){
      $dbname =$this->databasename();
	  $startdate=date('Y-m-d',strtotime(Input::get('start_date')));
	  //$customerlist =Route::getCustomerlist($dbname);
      $reoprtdata=Reverseauction::getsmsReport($dbname,$startdate);
      return view('RAbid.smsreport',compact('reoprtdata'));
    }
   public function smsexportcsv(Request $request)
    {
        $dbname =$this->databasename();
      	$CUTDB = Reverseauction::dydb($dbname);

 		 $startdate=date('Y-m-d',strtotime(Input::get('start_date')));		
		 $reportlist=$CUTDB->select("SELECT DATE_FORMAT(SM.`sms_date`,'%d-%m-%Y') As sms_date,CM.dns_customer_code,CM.customer_name,RM.route_name,ST.state,SM.`sms_body`,SM.`sms_type`,SM.`sms_time`,SM.`response`,SM.phone_no FROM `sms_log` SM,customer_master CM,route_master RM,state_master ST WHERE SM.`customer_code`=CM.customer_code AND CM.acedns='Y' AND SM.`sms_date`='".$startdate."' AND CM.route_code=RM.route_code AND ST.dns_state_code=CM.state_code ORDER BY SM.sms_time DESC");

        $data = "Date\tcustomer code\tcustomer name\tPhone no\troute\tstate\tSms body\tSms type\tSms time\tResponse"."\n";
        foreach($reportlist as $report) {
                $sms_date=$report->sms_date;
				$dns_customer_code=$report->dns_customer_code;
				$customer_name=$report->customer_name;
				$phone_no=$report->phone_no;
				$route_name=$report->route_name;
				$state=$report->state;
				$sms_body=$report->sms_body;
				$sms_body=str_replace("\n",'',$sms_body);
				$sms_type=$report->sms_type;
				$sms_time=$report->sms_time;
				$response=$report->response;
				
    $data .=$sms_date."\t".$dns_customer_code."\t".$customer_name."\t".$phone_no."\t".$route_name."\t".$state."\t".$sms_body."\t".$sms_type."\t".$sms_time."\t".$response."\n";
        }
        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename=smslog.xls');
        echo $data;
        exit();
    }
}
