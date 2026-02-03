<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

use App\Http\Requests;

use Session;
use App\Helpers\Reverseauction;
use App\Helpers\Commonfunctions;

class ReverseauctionpriceController extends Controller
{
    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }
    /**
     * Show the Homepage.
     *
     * @return Response
     */
   public function homepageview()
	{
	   $sdbname =$this->databasename();
       return view('homepage.view');
	}
	/**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function reverseauctionpricegenerate()
    {
       $sdbname =$this->databasename();
	   $oil_group_list=Reverseauction::getOilgrouplist($sdbname);
	   $plant_list=Reverseauction::getPlantlist($sdbname);
	   $prodlistsconversion=Reverseauction::getproductlistconverion($sdbname);

       return view('reverseauction.create',compact('oil_group_list','plant_list','prodlistsconversion'));
		//echo 'Reverse auction';
    }
	/**
     * Show the list for stored resource.
     *
     * @return Response
     */
    public function showreleaserate()
    {
       $sdbname =$this->databasename();
	   //$plantwisereleasedrate=Reverseauction::getplantwisereleasedrate($sdbname);
       return view('reverseauction.view');
		//echo 'Reverse auction';
    }
	/**
     * Show the date time
     *
     * @return Response
     */
    public function windowtime()
    {
       $sdbname =$this->databasename();
	   $windowtimedetail=Reverseauction::getWindowtime($sdbname);
       return view('reverseauction.datetime',compact('windowtimedetail'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
	  $dbname =$this->databasename();
      $CUTDB = Reverseauction::dydb($dbname);	 
	 //echo 'testing process ongoing';
	  $prodcount=$request->input('prodcount');
	  $GST_percent=$request->input('GST_percent');
	  if($GST_percent=='') $GST_percent=5;
	  $upper_limit_percent=$request->input('upper_limit_percent');
	  if($upper_limit_percent=='') $upper_limit_percent=4;
	  $plant_list=Reverseauction::getPlantlist($dbname);
	  $downloadtime=date('Y-m-d');
	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  //$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	  $contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second."\n";
	  $biddingdate=$date.'/'.$month.'/'.$year;
	 //For valididation of Forms input
	 $validate_array[]='';
	 $plant_name_array=array();
	 $prod_code_array=array();
	 $release_rate_array=array();
	 for($x=1;$x<=$prodcount;$x++)
	  {
		  $counter_bid_jump_validate=$request->input("counter_bid_jump_$x");
		  $counter_bid_limit_validate=$request->input("counter_bid_limit_$x");
		  $rate_jump_validate=$request->input("rate_jump_$x");
		  if($counter_bid_jump_validate >0)
		  {
			$validate_array['counter_bid_jump_'. $x] = 'numeric|between:0,100';
		  }
		  if($counter_bid_limit_validate >0)
		  {
			  $validate_array['counter_bid_limit_'. $x] = 'numeric|between:0,100';
		  }
		  if($rate_jump_validate >0)
		  {
			  $validate_array['rate_jump_'. $x] = 'numeric|between:0,100';
		  }
	  }
	  if(count($validate_array) >0)
	  {
	 	 $this->validate($request, $validate_array );
	  }
	  /*if ( $this->validate->fails()) {
	  return redirect()->back()->withErrors('Ivalid Input');
	  }*/
	  foreach($plant_list as $plantvalinitialize)
		{
			${'prodstringrate'.$plantvalinitialize}='';
		}
	  $inserflag=0;
	  for($i=1;$i<=$prodcount;$i++)
	  {
		$prod_desc=$request->input("mapped_prod_desc_$i");
		$prod_code=$request->input("mapped_prod_code_$i");
		$counter_bid_jump=$request->input("counter_bid_jump_$i");
	    $counter_bid_limit=$request->input("counter_bid_limit_$i");
		$rate_jump_IR=$request->input("rate_jump_$i");

		/*$sqlprodgroupcode=$CUTDB->table('product_group_master')
				->select('product_group_code')
				->where('product_group_name', $request->input("oilgroup_$i"))
                ->first();
		$product_group_code=$sqlprodgroupcode->product_group_code;*/
		$sqlconversion=$CUTDB->select("SELECT conversion_one,conversion_two,addition,multiply,prod_code FROM product_unit_coversion_matrix WHERE 
						 mapped_prod_code= '".$prod_code."' AND acedns='Y'");		
		if(count($sqlconversion) >0){
			foreach ($sqlconversion as $key => $conversionvalue) {			
				$conversion_one=$conversionvalue->conversion_one;
				$conversion_two=$conversionvalue->conversion_two;
				$addition=$conversionvalue->addition;
				$multiply=$conversionvalue->multiply;
				$source_prod_code=$conversionvalue->prod_code;
				foreach($plant_list as $plantval)
				{
					$plant_name=$plantval;
					$release_rate=$request->input("$plant_name"."_"."$i");
					
					$CUTDB->table('plant_product_wise_RA_rate')
						  ->where('plant_name', $plant_name)
						   ->where('prod_code', $source_prod_code)
						    ->where('acedns', 'Y')
						  ->update(array('acedns' => 'N','download_time' =>$contentsdatetime));
					if($release_rate >0){
					    //$final_release_rate=$release_rate+(($release_rate*$rate_jump_IR)/100);
						if($source_prod_code==$prod_code) {
							$base_rate=$release_rate;
						}
						else
						{
							//$indicative_rate=$final_release_rate;
							$base_rate=$release_rate;
							if($multiply=='yes'){
								$base_rate=$release_rate*$conversion_two;
							}
							if($addition=='yes')
							{
								//$indicative_rate=$indicative_rate+$conversion_one;
								$base_rate=$base_rate+$conversion_one;
							}
						}
						$base_rate=round($base_rate,0);
						$indicative_rate=$base_rate+(($base_rate*$rate_jump_IR)/100);
						$indicative_rate=round($indicative_rate,0);
						if(!in_array($plant_name,$plant_name_array))
						{
							array_push($plant_name_array,$plant_name);
						}
						//array_push($prod_code_array,$source_prod_code);
						//array_push($release_rate_array,$release_rate);
						$sqlselectprodcode=$CUTDB->table('product_master')
									->select('product_master.prod_desc')
									->where('product_master.dns_prod_code', '=' ,$source_prod_code)
									->first();
						$prod_desc=$sqlselectprodcode->prod_desc;	
						${'prodstringrate'.$plant_name}.=$source_prod_code."###".$prod_desc."###".$indicative_rate."###".$GST_percent."|||";

					  $CUTDB->table('plant_product_wise_RA_rate')->insert(array(
						  'plant_name' => $plant_name,
						  'prod_code' => $source_prod_code,
						  'release_rate' => $release_rate,
						  'conversion_one' => $conversion_one,
						  'conversion_two' => $conversion_two,
						  'addition' => $addition,
						  'multiply' => $multiply,
						  'base_rate' => $base_rate,
						  'indicative_rate' => $indicative_rate,
						  'counter_bid_jump' => $counter_bid_jump,
						  'counter_bid_limit' => $counter_bid_limit,
						  'rate_jump_IR' => $rate_jump_IR,
						  'acedns' => 'Y',
						  'GST_percent' => $GST_percent,
						   'upper_limit' => $upper_limit_percent,
						  'download_time' =>$contentsdatetime
					   ));
					}
				}
		    }
		}
		$inserflag=1; 
	  }
	  if($inserflag==1){
		  /*$windowtimevalstring='';
		  $sqlwindowtime=$CUTDB->select("SELECT time_from,time_to FROM RA_windowtime WHERE rate_released_date= '".$downloadtime."'");	
		  foreach ($sqlwindowtime as $key => $windowtimeval) {
			  $windowtimefrom=$windowtimeval->time_from;
			  $windowtimeto=$windowtimeval->time_to;
			  $windowtimevalstring=$windowtimevalstring.($windowtimefrom.'-'.$windowtimeto).',';
		  }
		  $windowtimevalstring=substr($windowtimevalstring,0,-1); */
		  $username="emami";
		  $password="EAL2017";
		  $sender="EMAGRO";
		  $date=gmdate('d',strtotime('+330 minute'));
      	  $month=gmdate('m',strtotime('+330 minute'));
      	  $year=gmdate('Y',strtotime('+330 minute'));
      	  $hour=gmdate('H',strtotime('+330 minute'));
      	  $minute=gmdate('i',strtotime('+330 minute'));
      	  $second=gmdate('s',strtotime('+330 minute'));
		  $customer_code_array=array();

		  $msg="Rate released successfully";
		  
		  foreach($plant_name_array as $plantnameval)
		  {
			//$smsstringfinal=nl2br($smsstringfinal);
			//echo strlen($smsstringfinal);
			//exit();
			$prod_details_part=explode('|||',${'prodstringrate'.$plantnameval});
			$sqlselectCustphone=$CUTDB->select("SELECT DISTINCT CM.phone_no,CM.customer_code FROM customer_master CM,customer_branch_relation CBR 
			  					WHERE CM.customer_code=CBR.customer_code AND CM.sauda_type='RA' AND CM.acedns='Y' AND CBR.branch_code IN
								(SELECT branch_code FROM branch_master WHERE plant_name='".$plantnameval."')  AND CM.phone_no <> '' AND  
								CM.zone <> '' AND CM.state_code <> '' ORDER BY CM.customer_code ASC");
			if(count($sqlselectCustphone) >0)
			{ 
				foreach ($sqlselectCustphone as $key => $custphoneval) {
				  $customer_phone_no=$custphoneval->phone_no; 
				  $customer_code=$custphoneval->customer_code;
				  if(!in_array($customer_code,$customer_code_array))
					{
						array_push($customer_code_array,$customer_code);
					}
				  //$customer_phone_no='7347604544';
				  //$customer_phone_no='9874450813';
				   $sqlcustomerdetails=$CUTDB->table('customer_master')
						->select('dns_customer_code','route_code','transport_mode','loadability_ton','state_code','zone','incoterms')
						->where('customer_code',$customer_code)
						->first();
					$route_code=$sqlcustomerdetails->route_code;
					$transport_mode=$sqlcustomerdetails->transport_mode;
					$loadability_ton=$sqlcustomerdetails->loadability_ton;
					$state_code=$sqlcustomerdetails->state_code;
					$dns_customer_code=$sqlcustomerdetails->dns_customer_code;
					$zone=$sqlcustomerdetails->zone;
					$incoterms=$sqlcustomerdetails->incoterms;
					$incotermspart=explode(';',$incoterms);
					foreach($incotermspart as  $incotermsval)
					{	
					if(strtoupper($incotermsval)=='FOR PLANT' || strtoupper($incotermsval)=='EX PLANT'){
					  if(strtoupper($incotermsval)=='FOR PLANT')
					  { 
					   	$smsstring="Dear Customer,\nIndicative rates for today $biddingdate)\nFOR $plantnameval tax paid\nOil Type-Rate\n";
					  }
					  if(strtoupper($incotermsval)=='EX PLANT')
					  { 
					   	$smsstring="Dear Customer,\nIndicative rates for today $biddingdate)\nEX $plantnameval tax paid\nOil Type-Rate\n";
					  }
					$sqlcustomerbranchdetails=$CUTDB->select("SELECT CBR.branch_code FROM customer_branch_relation CBR,branch_master BM 
												WHERE CBR.branch_code=BM.branch_code AND CBR.acedns='Y' AND BM.plant_name='".$plantnameval."' 
											AND BM.is_plant='yes' AND CBR.customer_code='".$customer_code."'");
						foreach ($sqlcustomerbranchdetails as $key => $branchdetailsval) {
							${'prodstringrate'.$customer_code}='';
							$branch_code=$branchdetailsval->branch_code;								
							foreach($prod_details_part as $prod_details_value)
							{
								//echo $prod_details_value.'<br />';
								$final_indicative_rate=0;
								$final_indicative_rate_GST=0;
								$proddetailsval=explode('###',$prod_details_value);
								$sqlmargincostprodwise=$CUTDB->select("SELECT  margin_cost FROM margin_cost_RA
											WHERE dns_prod_code='".$proddetailsval[0]."' AND state_code='".$state_code."' AND zone='".$zone."' 
											ORDER BY datetime DESC LIMIT 0,1");
							   if(count($sqlmargincostprodwise) >0)
								{		
									foreach ($sqlmargincostprodwise as $key => $margindetails) {			
										$margin_cost=round($margindetails->margin_cost,2);
									}
								}
								else
								{
									$margin_cost=0;
								}
								if(strtoupper($incotermsval)=='FOR PLANT'){
									$sqlfreihgt=$CUTDB->table('RA_route_freight')
												->select('freight')
												->where('branch_code',$branch_code)
												->where('route_code',$route_code)
												->where('acedns','Y')
												->where('transport_mode',$transport_mode)
												->where('capacity',$loadability_ton)
												->where('vertical_value','HBC:Rasoi:BIB')
												->first();
									if(count($sqlfreihgt) >0)
									{
										$freight=$sqlfreihgt->freight;
									}
									else $freight=0;
			
									$sqlqtytruckload=$CUTDB->table('load_distribution')
												->select('qty_truck_load')
												->where('transport_mode',$transport_mode)
												->where('truck_load',$loadability_ton)
												->where('prod_code',$proddetailsval[0])
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
									if(count($sqlmargincostprodwise) >0 && $freight_charge >0)
									{
										$sqldetentioncostprodwise=$CUTDB->table('detention_cost')
														->select('detention_cost')
														->where('prod_code',$proddetailsval[0])
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
										$sqlhoneycombcostprodwise=$CUTDB->table('honeycomb_cost')
																		->select('honeycomb_cost')
																		->where('prod_code',$proddetailsval[0])
																		->where('plant_name',$plantnameval)
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
										
										if(isset($proddetailsval[2])){
										$indicativerateprodwise=(float)$proddetailsval[2];
										$final_indicative_rate=$final_indicative_rate+$indicativerateprodwise+$honeycomb_cost+$detention_cost+$margin_cost+$freight_charge;
										$final_indicative_rate_GST=$final_indicative_rate+(($final_indicative_rate*$proddetailsval[3])/100);
										$final_indicative_rate_GST=round($final_indicative_rate_GST,0);
										${'prodstringrate'.$customer_code}.=$proddetailsval[1]."-".$final_indicative_rate_GST."\n";
										}
									}
								}
								if(strtoupper($incotermsval)=='EX PLANT'){
									if(count($sqlmargincostprodwise) >0)
									{
										/*$sqldetentioncostprodwise=$CUTDB->table('detention_cost')
														->select('detention_cost')
														->where('prod_code',$proddetailsval[0])
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
										$sqlhoneycombcostprodwise=$CUTDB->table('honeycomb_cost')
																		->select('honeycomb_cost')
																		->where('prod_code',$proddetailsval[0])
																		->where('plant_name',$plantnameval)
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
										else $honeycomb_cost=0;	*/
										
										if(isset($proddetailsval[2])){
										$indicativerateprodwise=(float)$proddetailsval[2];
										$final_indicative_rate=$final_indicative_rate+$indicativerateprodwise+$margin_cost;
										$final_indicative_rate=round($final_indicative_rate,0);
										$final_indicative_rate_GST=$final_indicative_rate+(($final_indicative_rate*$proddetailsval[3])/100);
										$final_indicative_rate_GST=round($final_indicative_rate_GST,0);
										${'prodstringrate'.$customer_code}.=$proddetailsval[1]."-".$final_indicative_rate_GST."\n";
										//${'prodstringrate'.$customer_code}.=$proddetailsval[1]."-".$final_indicative_rate."\n";
										}
									}
								}
								//else $indicativerateprodwise=0;
								//echo $proddetailsval[1].'----'.$proddetailsval[2].' <br />';
							}// Product details block end
							if(${'prodstringrate'.$customer_code}!=''){
							$smsstringfinal=$smsstring.${'prodstringrate'.$customer_code}."\nCompany reserves the right to withdraw rates any time\nTeam HBC";
							/*if(strlen($smsstringfinal)> 160)
							{
								$smsstringfinal=wordwrap($smsstringfinal, 160, "<br />");
								$smsstringfinalarray=explode('<br />',$smsstringfinal);
								foreach($smsstringfinalarray as $smsstringval)
								{
								  $Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstringval);
								  $ch = curl_init();
								  curl_setopt($ch, CURLOPT_URL, $Url);
								  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
								  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								  $output = curl_exec($ch);
								  //print_r($output);
								  curl_close($ch);
								  sleep(3);
								  //exit();
								  /*$response = explode("|",$output);
								  
								  if(intval($response[0]) == 1701) {
									echo "Message: Success";
								  }
								  else {
									echo "Message: Fail (".$output.")";
								  }*/
								/*}
							}
							else
							{*/
								//$Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstringfinal);
							$Url="http://api.msg91.com/api/sendhttp.php?country=91&sender=EMAGRO&route=4&mobiles=".$customer_phone_no."&authkey=250054APHdwU5TNpm5c040582&message=".rawurlencode($smsstringfinal);
	
							  $ch = curl_init();
							  curl_setopt($ch, CURLOPT_URL, $Url);
							  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
							  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							  $output = curl_exec($ch);
							  //print_r($output);
							  curl_close($ch);
							//}
							//For insertion of sms log
							//$response = explode("|",$output);
								  
								 $sms_date=$year.'-'.$month.'-'.$date;
								 $sms_time=$hour.':'.$minute.':'.$second;
								$CUTDB->table('sms_log')->insert(array(
								  'sms_date' => $sms_date,
								  'customer_code' => $customer_code,
								  'sms_type' => 'RELEASE RATE',
								  'sms_time' => $sms_time,
								  'sms_body' => $smsstringfinal,
								  'phone_no' => $customer_phone_no,
								  'response' => $output,
							  ));
					}
		  		}// Branch details block end for FOR PLANT AND EX PLANT
			}// FOR PLANT AND EX PLANT block end
			if(strtoupper($incotermsval)=='FOR DEPOT' || strtoupper($incotermsval)=='EX DEPOT') //FOR DEPOT AND EX DEPOT block strat
			{
					  $sqlcustomerbranchdetails=$CUTDB->select("SELECT CBR.branch_code,BM.branch_name FROM customer_branch_relation CBR,branch_master BM 
												WHERE CBR.branch_code=BM.branch_code AND CBR.acedns='Y' AND BM.plant_name='".$plantnameval."' 
											AND BM.is_plant='no' AND CBR.customer_code='".$customer_code."'");
						foreach ($sqlcustomerbranchdetails as $key => $branchdetailsval) {
							$branch_code=$branchdetailsval->branch_code;
							$branch_name=$branchdetailsval->branch_name;
							${'prodstringrate'.$customer_code}='';
							if(strtoupper($incotermsval)=='FOR DEPOT')
							  { 
								$smsstring="Dear Customer,\nIndicative rates for today $biddingdate)\nFOR $branch_name tax paid\nOil Type-Rate\n";
							  }
							  if(strtoupper($incotermsval)=='EX DEPOT')
							  { 
								$smsstring="Dear Customer,\nIndicative rates for today $biddingdate)\nEX $branch_name tax paid\nOil Type-Rate\n";
							  }
								
							foreach($prod_details_part as $prod_details_value)
							{
								//echo $prod_details_value.'<br />';
								$final_indicative_rate=0;
								$final_indicative_rate_GST=0;
								$proddetailsval=explode('###',$prod_details_value);
								$sqlmargincostprodwise=$CUTDB->select("SELECT  margin_cost FROM margin_cost_RA
											WHERE dns_prod_code='".$proddetailsval[0]."' AND state_code='".$state_code."' AND zone='".$zone."' 
											ORDER BY datetime DESC LIMIT 0,1");
							   if(count($sqlmargincostprodwise) >0)
								{		
									foreach ($sqlmargincostprodwise as $key => $margindetails) {			
										$margin_cost=round($margindetails->margin_cost,2);
									}
								}
								else
								{
									$margin_cost=0;
								}
								if(strtoupper($incotermsval)=='FOR DEPOT'){
									$sqlfreight=$CUTDB->table('RA_route_freight')
												->select('freight','capacity')
												->where('branch_code',$branch_code)
												->where('route_code',$route_code)
												->where('acedns','Y')
												->where('transport_mode',$transport_mode)
												->where('vertical_value','HBC:Rasoi:BIB')
												->first();
									if(count($sqlfreight) >0)
									{
										$freight=$sqlfreight->freight;
										$capacity=$sqlfreight->capacity;
										//else $freight=0;
										$sqlqtytruckload=$CUTDB->table('load_distribution')
													->select('qty_truck_load')
													->where('transport_mode',$transport_mode)
													->where('truck_load',$capacity)
													->where('prod_code',$proddetailsval[0])
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
								  	}
									else  $freight_charge=0;
									$sqldepotcostprodwise=$CUTDB->select("SELECT  depot_cost FROM depot_cost
											WHERE dns_prod_code='".$proddetailsval[0]."' AND branch_code='".$branch_code."' ORDER BY datetime DESC LIMIT 0,1");
								   if(count($sqldepotcostprodwise) >0)
									{		
										foreach ($sqldepotcostprodwise as $key => $depotcostdetails) {			
											$depot_cost=round($depotcostdetails->depot_cost,2);
										}
									}
									else
									{
										$depot_cost=0;
									}
								   $sqlprimaryfreightprodwise=$CUTDB->select("SELECT  freight_cost FROM freight_cost
											WHERE dns_prod_code='".$proddetailsval[0]."' AND branch_code='".$branch_code."' 
											AND transport_mode='".$transport_mode."' ORDER BY datetime DESC LIMIT 0,1");
								   if(count($sqlprimaryfreightprodwise) >0)
									{		
										foreach ($sqlprimaryfreightprodwise as $key => $primaryfreightdetails) {			
											$freight_cost=round($primaryfreightdetails->freight_cost,2);
										}
									}
									else
									{
										$freight_cost=0;
									}
									if(count($sqlmargincostprodwise) >0 && $freight_charge >0 && $depot_cost > 0 && $freight_cost >0)
									{
										$sqldetentioncostprodwise=$CUTDB->table('detention_cost')
														->select('detention_cost')
														->where('prod_code',$proddetailsval[0])
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
										$sqlhoneycombcostprodwise=$CUTDB->table('honeycomb_cost')
																		->select('honeycomb_cost')
																		->where('prod_code',$proddetailsval[0])
																		->where('plant_name',$plantnameval)
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
										
										if(isset($proddetailsval[2])){
										$indicativerateprodwise=(float)$proddetailsval[2];
										$final_indicative_rate=$final_indicative_rate+$indicativerateprodwise+$detention_cost+$honeycomb_cost+$margin_cost+$freight_charge+$depot_cost+$freight_cost;
										$final_indicative_rate_GST=$final_indicative_rate+(($final_indicative_rate*$proddetailsval[3])/100);
										$final_indicative_rate_GST=round($final_indicative_rate_GST,0);
										${'prodstringrate'.$customer_code}.=$proddetailsval[1]."-".$final_indicative_rate_GST."\n";
										}
									}
								}// FOR DEPOT block end
								if(strtoupper($incotermsval)=='EX DEPOT'){
								   $sqldepotcostprodwise=$CUTDB->select("SELECT  depot_cost FROM depot_cost
											WHERE dns_prod_code='".$proddetailsval[0]."' AND branch_code='".$branch_code."' ORDER BY datetime DESC LIMIT 0,1");
								   if(count($sqldepotcostprodwise) >0)
									{		
										foreach ($sqldepotcostprodwise as $key => $depotcostdetails) {			
											$depot_cost=round($depotcostdetails->depot_cost,2);
										}
									}
									else
									{
										$depot_cost=0;
									}
								   $sqlprimaryfreightprodwise=$CUTDB->select("SELECT  freight_cost FROM freight_cost
											WHERE dns_prod_code='".$proddetailsval[0]."' AND branch_code='".$branch_code."' 
											AND transport_mode='".$transport_mode."' ORDER BY datetime DESC LIMIT 0,1");
								   if(count($sqlprimaryfreightprodwise) >0)
									{		
										foreach ($sqlprimaryfreightprodwise as $key => $primaryfreightdetails) {			
											$freight_cost=round($primaryfreightdetails->freight_cost,2);
										}
									}
									else
									{
										$freight_cost=0;
									}

									if(count($sqlmargincostprodwise) >0 && $depot_cost > 0 && $freight_cost >0)
									{
										$sqldetentioncostprodwise=$CUTDB->table('detention_cost')
														->select('detention_cost')
														->where('prod_code',$proddetailsval[0])
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
										$sqlhoneycombcostprodwise=$CUTDB->table('honeycomb_cost')
																		->select('honeycomb_cost')
																		->where('prod_code',$proddetailsval[0])
																		->where('plant_name',$plantnameval)
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
										
										if(isset($proddetailsval[2])){
										$indicativerateprodwise=(float)$proddetailsval[2];
										$final_indicative_rate=$final_indicative_rate+$indicativerateprodwise+$detention_cost+$honeycomb_cost+$margin_cost+$depot_cost+$freight_cost;
										$final_indicative_rate=round($final_indicative_rate,0);
										$final_indicative_rate_GST=$final_indicative_rate+(($final_indicative_rate*$proddetailsval[3])/100);
										$final_indicative_rate_GST=round($final_indicative_rate_GST,0);
										${'prodstringrate'.$customer_code}.=$proddetailsval[1]."-".$final_indicative_rate_GST."\n";
										//${'prodstringrate'.$customer_code}.=$proddetailsval[1]."-".$final_indicative_rate."\n";
										}
									}
								}//EX DEPOT  block end
								//else $indicativerateprodwise=0;
								//echo $proddetailsval[1].'----'.$proddetailsval[2].' <br />';
							}// Product details block end
							if(${'prodstringrate'.$customer_code}!=''){
								$smsstringfinal=$smsstring.${'prodstringrate'.$customer_code}."\nCompany reserves the right to withdraw rates any time\nTeam HBC";
								//$Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstringfinal);
							$Url="http://api.msg91.com/api/sendhttp.php?country=91&sender=EMAGRO&route=4&mobiles=".$customer_phone_no."&authkey=250054APHdwU5TNpm5c040582&message=".rawurlencode($smsstringfinal);
							  $ch = curl_init();
							  curl_setopt($ch, CURLOPT_URL, $Url);
							  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
							  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							  $output = curl_exec($ch);
							  //print_r($output);
							  curl_close($ch);
							//For insertion of sms log
							//$response = explode("|",$output);
								  
								 $sms_date=$year.'-'.$month.'-'.$date;
								 $sms_time=$hour.':'.$minute.':'.$second;
								$CUTDB->table('sms_log')->insert(array(
								  'sms_date' => $sms_date,
								  'customer_code' => $customer_code,
								  'sms_type' => 'RELEASE RATE',
								  'sms_time' => $sms_time,
								  'sms_body' => $smsstringfinal,
								  'phone_no' => $customer_phone_no,
								  'response' => $output,
							  ));
					}
		  		}// Branch details block end for FOR DEPOT AND EX DEPOT
			}//FOR DEPOT AND EX DEPOT block end
		  }//Incoterms block end
	    }//Customer phone block end
	  }//IF customer phone >0 block end
	}//Plant block end
   }//If insert flag=1 block end
   //exit();
	return redirect('/showreleasedrate')->with('message', 'Rate Released Successfully!');
  }
	 /**
     * Store a newly created window time in storage.
     *
     * @return Response
     */
    public function storewindowtime(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Reverseauction::dydb($dbname);
	  $previousdate=date('Y-m-d', strtotime(' -1 day'));
	  	 
	  $windowdate=$request->input('windowdate');
	  $windowdatefinal=date('Y-m-d',strtotime($windowdate));
	  $timefrom=$request->input('timefrom');
	  $timeto=$request->input('timeto');
	  $confirmval=$request->input('confirmval');

	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second."\n";
	  
	  $this->validate($request, [
            'windowdate' => 'required|date|after:'.$previousdate,
			'timefrom' => 'required',
			'timeto' => 'required|after:timefrom',
      ]);
	  if($confirmval=='yes'){
	  $CUTDB->table('RA_windowtime')
              ->where('last_window_time', $confirmval)
			  ->where('rate_released_date', $windowdatefinal)
              ->update(array('last_window_time' => 'no'));
	 }
	 if($confirmval=='no'){
	  $CUTDB->table('RA_windowtime')
              ->where('last_window_time', 'yes')
			  ->where('rate_released_date', $windowdatefinal)
              ->update(array('last_window_time' => 'no'));
	 }
	 //For valididation of Forms input
		$CUTDB->table('RA_windowtime')->insert(array(
		  'rate_released_date' => $windowdatefinal,
		  'time_from' => $timefrom,
		  'time_to' => $timeto,
		  'acedns' => 'Y',
		  'last_window_time' => $confirmval,
		  'user_id' => Session::get('USERNAME'),
		  'ip_address' => $_SERVER['REMOTE_ADDR'],
		  'download_time' =>$contentsdatetime
	   ));
	  return redirect('/windowtime')->with('message', 'Window time submitted successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $dbname =$this->databasename();
        $CUTDB = Employee::dydb($dbname);

        $emp_branch='';
        $empmultipleValues = Input::get('emp_branch');
        if(count($empmultipleValues))
        {
            foreach($empmultipleValues as $value)
            {
                $emp_branch.=$value.',';
            }
            $emp_branch=substr($emp_branch,0,-1);
        }

        $emp_reportto='';
        $empmultiplereporttoValues = Input::get('emp_reportto');
        if(count($empmultiplereporttoValues))
        {
            foreach($empmultiplereporttoValues as $value)
            {
                $emp_reportto.=$value.',';
            }
            $emp_reportto=substr($emp_reportto,0,-1);
        }

        $emp_vartical='';
        $empmultiplevarticalValues = Input::get('vartical');
        if(count($empmultiplevarticalValues))
        {
            foreach($empmultiplevarticalValues as $value)
            {
                $emp_vartical.=$value.',';
            }
            $emp_vartical=substr($emp_vartical,0,-1);
        }

        $dnsbranchcode=Input::get('emp_code');
        $empname=Input::get('emp_name');
        $empbranch=$emp_branch;
        $empemail=Input::get('emp_emailid');
        $empphone=Input::get('emp_phnum');
        $empreportto=$emp_reportto;
        $emphq=Input::get('emp_hq');
        $empsaleaccess=Input::get('sale_access');
        $empdesignation=Input::get('emp_designation');
        $empdistrict=Input::get('district');
        $empstate=Input::get('state');
        $empzone=Input::get('zone');
        $vartical=$emp_vartical;

        if(Session::get('iscodeneed')=='yes')
        {

           $rules1[] = array(
              'emp_code' => 'required',
           );
        }
        $rules1[] = array(
          'emp_name' => 'required',
          'emp_emailid' => 'required|email',
          'emp_phnum' => 'required|numeric',
          'emp_reportto' => 'required',
          'state' => 'required',
        );
        $rules = array();
        foreach($rules1 as $arr) {
             if(is_array($arr)) {
                 $rules = array_merge($rules, $arr);
             }
        }

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
             return Redirect::to('employee/'.$id.'/edit')->withErrors($validator);
        }
        else{
              $CUTDB->table('employee_master')
              ->where('emp_code', $id)
              ->limit(1)
              ->update(array('dns_emp_code' => $dnsbranchcode,
              'emp_name' => $empname,
              'branch_code' => $empbranch,
              'reporting_to' => $empreportto,
              'vertical_value' => $vartical,
              'email' => $empemail,
              'phone_no' => $empphone,
              'HQ' => $emphq,
              'sale_access' => $empsaleaccess,
              'designation' => $empdesignation,
              'District' => $empdistrict,
              'state' => $empstate,
              'zone' => $empzone
               ));

              return redirect('/employee')->with('message', 'Success!');
        }

    }
	public function pjpexportRAbid(Request $request)
    {
        $dbname =$this->databasename();
      	$CUTDB = Reverseauction::dydb($dbname);

        //$empid=$request->emp_list;
        //$newempid=implode("','",$empid);
        $date=gmdate('d',strtotime('+330 minute'));
	  	$month=gmdate('m',strtotime('+330 minute'));
	  	$year=gmdate('Y',strtotime('+330 minute'));
	    $contentsdatetime =$year.'-'.$month.'-'.$date;
		
		 $reportlist=$CUTDB->select("SELECT DISTINCT RAD.*,PM.prod_desc,CM.customer_name FROM `product_master` PM,RA_bid_rate_details RAD,
		 							customer_master CM 
		 							WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code ORDER BY RAD.`download_time` DESC");

        $data = "bid_id,plant_name,prod_code,prod_desc,released_rate,base_rate,indicative_rate,customer name,qty,bid_rate,primary_freight,secondary_freight,depot_cost,GST_value,counter_bid,counter_bid_threshold,counter_bid_rate,bid_status,counter_bid_id,date time"."\n";

        foreach($reportlist as $reoprt) {
                $datetime=date('d-m-Y H:i:s',strtotime($reoprt->download_time));
				$bid_id=$reoprt->bid_id;
				$plant_name=$reoprt->plant_name;
				$prod_code=$reoprt->prod_code;
				$prod_desc=$reoprt->prod_desc;
				$released_rate=$reoprt->released_rate;
				$base_rate=$reoprt->base_rate;
				$GST_value=$reoprt->GST_value;
				$indicative_rate=$reoprt->server_indicative_rate;
				$customer_name=$reoprt->customer_name;
				$qty=$reoprt->qty;
				$bid_rate=$reoprt->bid_rate;
				$primary_freight=$reoprt->primary_freight;
				$secondary_freight=$reoprt->secondary_freight;
				$depot_cost=$reoprt->depot_cost;
				$counter_bid=$reoprt->counter_bid;
				$counter_bid_threshold=$reoprt->counter_bid_threshold;
				$counter_bid_rate=$reoprt->counter_bid_rate;
				$counter_bid_id=$reoprt->counter_bid_id;
				$bid_status=$reoprt->bid_status;
				
                $data .=$bid_id.",".$plant_name.",".$prod_code.",".$prod_desc.",".$released_rate.",".$base_rate.",".$indicative_rate.",".$customer_name.",".$qty.",".
				$bid_rate.",".$primary_freight.",".$secondary_freight.",".$depot_cost.",".$GST_value.",".$counter_bid.",".$counter_bid_threshold.",".$counter_bid_rate.",".$bid_status.",".$counter_bid_id.",".$datetime."\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=RAbid.csv');
        echo $data;
        exit();
    }
	public function uploadfreight()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.uploadfreight');
    }
	public function RAfreightupload(Request $request)
    {
          $dbname =$this->databasename();
          $CUTDB = Reverseauction::dydb($dbname);

          $file = $request->file('freight_csv_file');

          //Display File Name
          $nickname=substr(Session::get('DBNAME'),7);
          //$name=$nickname.'_'.$file->getClientOriginalName();
		  $name=$file->getClientOriginalName();
          $ext=$file->getClientOriginalExtension();
		  
		 // $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

    //$destinationPath = public_path('/images');

    //$image->move($destinationPath, $input['imagename']);


    //$this->postImage->add($input);
          if(strtolower($name)=='ra_route_freight.csv')
          {
              $input['filename'] = $file->getClientOriginalName();
			  $destinationPath = public_path('csv');
              $file->move($destinationPath,$input['filename']);
			  //$this->postImage->add($input);
              $rec_count = 0;
          	  $ins_count = 0;
          	  $err = "";
              $date=gmdate('d',strtotime('+330 minute'));
			  $month=gmdate('m',strtotime('+330 minute'));
			  $year=gmdate('Y',strtotime('+330 minute'));
			  $hour=gmdate('H',strtotime('+330 minute'));
			  $minute=gmdate('i',strtotime('+330 minute'));
			  $second=gmdate('s',strtotime('+330 minute'));
			  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
        			//$lines = file($input['filename']);
					$lines = file($destinationPath.'/'.$input['filename']);
					//print_r($lines);
					$rec_count=0;
        			foreach($lines as $lineval)
        			{
        				$data=explode(',',$lineval);
						
						/*$i = 0;
        				$char = substr($line, $i, 1);
        				$value ="";
        				$data="";
        				$double_coute_found = false;

        				if($rec_count>=1)
        				{
        					$reporting_to_val='';
        					$branch_code='';

        					while($char!="")
        					{
        						if($double_coute_found && $char=="\"")
        						{
        							$double_coute_found = false;
        							$i++;
        							$char = substr($line, $i, 1);
        							continue;
        						}
        						if(!$double_coute_found && $char=="\"")
        						{

        							$double_coute_found = true;
        							$i++;
        							$char = substr($line, $i, 1);
        							continue;
        						}

        						if($char=="," && !$double_coute_found)
        						{
        							$data[]=$value;
        							$value = "";
        						}
        						else
        						{
        						$value .= $char;
        						}
        						$i++;
        						$char = substr($line, $i, 1);
        					} //end of while
        				  $data[]=$value;
						  
						  print_r($data);*/
						if($rec_count>=1)
        				{
						$dns_branch_code=trim($data[0]);
						$route_name=trim($data[1]);
						$zone=trim($data[2]);
						$transport_mode=trim($data[3]);
						$capacity=trim($data[4]);
						$freight=trim($data[5]);
						//$dns_customer_code=trim($data[6]);
						$sqlroutecode=$CUTDB->select("SELECT route_code FROM route_master WHERE route_name='".$route_name."'");
						$route_code='';
						 foreach ($sqlroutecode as $key => $value) {
							 $route_code=$value->route_code;
						  }
						$sqlbranchcode=$CUTDB->select("SELECT branch_code,is_plant FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'");
						$branch_code='';
						 foreach ($sqlbranchcode as $key => $branchvalue) {
							 $branch_code=$branchvalue->branch_code;
							 $is_plant=$branchvalue->is_plant;
						  }
				       if( $route_code!='' && $branch_code!=''){ 
							/*$CUTDB->table('RA_route_freight')
							->where('plant_name', $plant_name)
							->where('route_code', $route_code)
							->where('transport_mode', $transport_mode)
							->where('capacity', $capacity)
							->update(array('acedns' => 'N',
							'datetime' => $datetime
							 ));*/
							if($is_plant=='yes')
							{
							  $CUTDB->table('RA_route_freight')
								->where('branch_code', $branch_code)
								->where('route_code', $route_code)
								->where('transport_mode', $transport_mode)
								->where('vertical_value', 'HBC:Rasoi:BIB')
								->where('capacity', $capacity)
								->update(array('acedns' => 'N',
								'datetime' => $datetime
								 ));
							}
							else
							{
							 /*$CUTDB->table('RA_route_freight')
							->where('branch_code', $branch_code)
							->where('route_code', $route_code)
							->where('transport_mode', $transport_mode)
							->where('vertical_value', 'HBC:Rasoi:BIB')
							->update(array('acedns' => 'N',
							'datetime' => $datetime
							 ));*/
							 $CUTDB->table('RA_route_freight')
							->where('branch_code', $branch_code)
							->where('route_code', $route_code)
							->where('vertical_value', 'HBC:Rasoi:BIB')
							->update(array('acedns' => 'N',
							'datetime' => $datetime
							 ));
							}
		 
						  $CUTDB->table('RA_route_freight')->insert(array(
							  'branch_code' => $branch_code,
							  'route_code' => $route_code,
							  'zone' => $zone,
							  'transport_mode' => $transport_mode,
							  'capacity' => $capacity,
							  'freight' => $freight,
							  'acedns' => 'Y',
							   'vertical_value' => 'HBC:Rasoi:BIB',
							  'datetime' =>$datetime
						  ));
					   }
					}
					$rec_count++;
           		 }
				return redirect('/uploadfreight')->with('message', 'Freight uploaded successfully!');
         }
		 else
		 { 
		 	return redirect('/uploadfreight')->withErrors('File Extension will be .csv and file name will be RA_route_freight.csv.');
		 }
	}
	public function bidstatussendsms()
	{
		  $dbname =$this->databasename();
		  $CUTDB = Reverseauction::dydb($dbname);
		  $downloadtime=date('Y-m-d');
		  $date=gmdate('d',strtotime('+330 minute'));
		  $month=gmdate('m',strtotime('+330 minute'));
		  $year=gmdate('Y',strtotime('+330 minute'));
		  $hour=gmdate('H',strtotime('+330 minute'));
		  $minute=gmdate('i',strtotime('+330 minute'));
		  $second=gmdate('s',strtotime('+330 minute'));
		  $contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second."\n";
		  
		  $username="emami";
		  $password="EAL2017";
		  $sender="EMAGRO";
		  
		  $sqlwindowtime=$CUTDB->select("SELECT time_from,time_to FROM RA_windowtime WHERE rate_released_date= '".$downloadtime."' AND last_window_time='yes'");	
		  foreach ($sqlwindowtime as $key => $windowtimeval) {
			  $windowtimefrom=$windowtimeval->time_from;
			  $windowtimeto=$windowtimeval->time_to;
		  }
		  if(time() >strtotime($windowtimeto) && (time()-strtotime($windowtimeto)) > 15*60)
		  {
			$sqlbiddetails=$CUTDB->select("SELECT RAD.bid_id,RAD.bid_rate,RAD.qty,RAD.bid_status,RAD.counter_bid,PM.prod_desc,CM.customer_name,EM.emp_name,
											CM.phone_no FROM `product_master` PM,RA_bid_rate_details RAD,customer_master CM,employee_master EM
										WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code AND EM.emp_code=SUBSTRING(RAD.bid_id,2,5)
										AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y-%m-%d')='".$downloadtime."' 
										ORDER BY RAD.bid_status ASC,CM.customer_name ASC");
			foreach ($sqlbiddetails as $key => $biddetails) {
			  $bid_status=$biddetails->bid_status;
			  $bid_id=$biddetails->bid_id;
			  $bid_rate=$biddetails->bid_rate;
			  $qty=$biddetails->qty;
			  $prod_desc=$biddetails->prod_desc;
			  $customer_name=$biddetails->customer_name;
			  $counter_bid=$biddetails->counter_bid;
			  $emp_name=$biddetails->emp_name;
			  $bid_time=substr($bid_id,-5,2).':'.substr($bid_id,-3,2).':'.substr($bid_id,-1,2);
			  $customer_phone_no=$biddetails->phone_no;
	
			  if(strtoupper($bid_status)=='ACCEPT' &&  $counter_bid=='N')
			  {
				 $smsstring="Dear Customer,\n\nYour Bid @$bid_time of $prod_desc $qty $bid_rate is Accepted.\n\nRegards\nTeam Himani Best Choice";
			  }
			  if(strtoupper($bid_status)=='REJECT' &&  $counter_bid=='N')
			  {
				 $smsstring="Dear Customer,\n\nYour Bid @$bid_time of $prod_desc $qty $bid_rate is Rejected.\n\nRegards\nTeam Himani Best Choice";
			  }
			  if($bid_status=='' && $counter_bid=='Y')
			  {
				 $smsstring="Dear Customer,\n\nYour Bid @$bid_time of $prod_desc $qty $bid_rate is Counter Bid. 
							If Counter Bid Please contact your SO $emp_name for Counter Bid Rate.\n\nRegards\nTeam Himani Best Choice";
			  }
				//$Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstring);
				$Url="http://api.msg91.com/api/sendhttp.php?country=91&sender=EMAGRO&route=4&mobiles=".$customer_phone_no."&authkey=250054APHdwU5TNpm5c040582&message=".rawurlencode($smsstring);
				  $ch = curl_init();
				  curl_setopt($ch, CURLOPT_URL, $Url);
				  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
				  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				  //$output = curl_exec($ch);
				  //print_r($output);
				  curl_close($ch);
			}							
		}
	}
	public function uploadmargin()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.uploadmargin');
    }
	public function RAmarginupload(Request $request)
    {
          $dbname =$this->databasename();
          $CUTDB = Reverseauction::dydb($dbname);

          
		  $file = $request->file('margin_csv_file');
          //Display File Name
          $nickname=substr(Session::get('DBNAME'),7);
          //$name=$nickname.'_'.$file->getClientOriginalName();
		  if ($request->file('margin_csv_file')) {
			  $name=$file->getClientOriginalName();
			  $ext=$file->getClientOriginalExtension();
		  }
		  else
		  {
			  return redirect('/uploadmargin')->withErrors('File Extension will be .csv and file name will be margin cost-RA.csv OR margin cost-RA-rasoi.csv');
		  }
		 // $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

    	//$destinationPath = public_path('/images');

    	//$image->move($destinationPath, $input['imagename']);
         //$this->postImage->add($input);
          if(strtolower($name)=='margin cost-ra.csv' || strtolower($name)=='margin cost-ra-rasoi.csv')
          {
              if(strtolower($name)=='margin cost-ra.csv'){
			  $input['filename'] = $file->getClientOriginalName();
			  $destinationPath = public_path('csv');
              $file->move($destinationPath,$input['filename']);
			  //$this->postImage->add($input);
              $rec_count = 0;
          	  $ins_count = 0;
          	  $err = "";
              $date=gmdate('d',strtotime('+330 minute'));
			  $month=gmdate('m',strtotime('+330 minute'));
			  $year=gmdate('Y',strtotime('+330 minute'));
			  $hour=gmdate('H',strtotime('+330 minute'));
			  $minute=gmdate('i',strtotime('+330 minute'));
			  $second=gmdate('s',strtotime('+330 minute'));
			  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
        			//$lines = file($input['filename']);
					$lines = file($destinationPath.'/'.$input['filename']);
					$rec_count=0;
        			foreach($lines as $lineval)
        			{
        				$data=explode(',',$lineval);
						
						/*$i = 0;
        				$char = substr($line, $i, 1);
        				$value ="";
        				$data="";
        				$double_coute_found = false;

        				if($rec_count>=1)
        				{
        					$reporting_to_val='';
        					$branch_code='';

        					while($char!="")
        					{
        						if($double_coute_found && $char=="\"")
        						{
        							$double_coute_found = false;
        							$i++;
        							$char = substr($line, $i, 1);
        							continue;
        						}
        						if(!$double_coute_found && $char=="\"")
        						{

        							$double_coute_found = true;
        							$i++;
        							$char = substr($line, $i, 1);
        							continue;
        						}

        						if($char=="," && !$double_coute_found)
        						{
        							$data[]=$value;
        							$value = "";
        						}
        						else
        						{
        						$value .= $char;
        						}
        						$i++;
        						$char = substr($line, $i, 1);
        					} //end of while
        				  $data[]=$value;
						  
						  print_r($data);*/
						if($rec_count>=1)
        				{
						 $state_code=trim($data[0]);
						 $oil_group=trim($data[1]);
						 $oil_type=trim($data[2]);
						 $margin_cost_ton=trim($data[3]);
						 $vertical_value=trim($data[4]);
						 $zone=trim($data[5]);
						 $sqlproductgroupcode=$CUTDB->select("SELECT product_group_code FROM product_group_master WHERE product_group_name='".$oil_group."'");
						 $product_group_code='';
						 foreach ($sqlproductgroupcode as $key => $value) {
							 $product_group_code=$value->product_group_code;
						  }

						/*echo "SELECT DISTINCT conversion_factor,conversion_factor_two,dns_prod_code FROM product_master 
								WHERE acedns='Y' AND product_group_code='".$$product_group_code."' 
								AND prod_desc NOT LIKE '%LUP%' AND black_list='N' AND pack_size='".$oil_type."'";
						exit();	*/	
						$sqlconversion=$CUTDB->select("SELECT DISTINCT conversion_factor,conversion_factor_two,dns_prod_code FROM product_master 
												WHERE acedns='Y' AND product_group_code='".$product_group_code."' 
												AND prod_desc NOT LIKE '%LUP%' AND black_list='N' AND pack_size='".$oil_type."'");
						$conversion_factor='';
						$conversion_factor_two='';
						 foreach ($sqlconversion as $key => $prodvalue) {
							$distinct_dnsprod_code=$prodvalue->dns_prod_code;
							${'conversion_factor'.$distinct_dnsprod_code}=$prodvalue->conversion_factor;
							${'conversion_factor_two'.$distinct_dnsprod_code}=$prodvalue->conversion_factor_two;
						
						    $margin_cost_case_prodwise=$margin_cost_ton/${'conversion_factor_two'.$distinct_dnsprod_code};
						    $margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${'conversion_factor'.$distinct_dnsprod_code}),2);
							//$dns_customer_code=trim($data[6]);
							  $CUTDB->table('margin_cost_RA')->insert(array(
								  'dns_prod_code' => $distinct_dnsprod_code,
								  'state_code' => $state_code,
								  'zone' => $zone,
								  'oil_group' => $oil_group,
								  'oil_type' => $oil_type,
								  'margin_cost' => $margin_cost_case_prodwise,
								  'margin_cost_ton' => $margin_cost_ton,
								  'ip_address' => $_SERVER['REMOTE_ADDR'],
								   'vertical_value' => 'HBC:Rasoi:BIB',
								  'datetime' =>$datetime
							  ));
						  }
					   }
					$rec_count++;
           		 }
				return redirect('/uploadmargin')->with('message', 'Margin uploaded successfully!');
			 }
			 if(strtolower($name)=='margin cost-ra-rasoi.csv'){
			  $input['filename'] = $file->getClientOriginalName();
			  $destinationPath = public_path('csv');
              $file->move($destinationPath,$input['filename']);
			  //$this->postImage->add($input);
              $rec_count = 0;
          	  $ins_count = 0;
          	  $err = "";
              $date=gmdate('d',strtotime('+330 minute'));
			  $month=gmdate('m',strtotime('+330 minute'));
			  $year=gmdate('Y',strtotime('+330 minute'));
			  $hour=gmdate('H',strtotime('+330 minute'));
			  $minute=gmdate('i',strtotime('+330 minute'));
			  $second=gmdate('s',strtotime('+330 minute'));
			  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
        			//$lines = file($input['filename']);
					$lines = file($destinationPath.'/'.$input['filename']);
					$rec_count=0;
        			foreach($lines as $lineval)
        			{
        				$data=explode(',',$lineval);
						
						/*$i = 0;
        				$char = substr($line, $i, 1);
        				$value ="";
        				$data="";
        				$double_coute_found = false;

        				if($rec_count>=1)
        				{
        					$reporting_to_val='';
        					$branch_code='';

        					while($char!="")
        					{
        						if($double_coute_found && $char=="\"")
        						{
        							$double_coute_found = false;
        							$i++;
        							$char = substr($line, $i, 1);
        							continue;
        						}
        						if(!$double_coute_found && $char=="\"")
        						{

        							$double_coute_found = true;
        							$i++;
        							$char = substr($line, $i, 1);
        							continue;
        						}

        						if($char=="," && !$double_coute_found)
        						{
        							$data[]=$value;
        							$value = "";
        						}
        						else
        						{
        						$value .= $char;
        						}
        						$i++;
        						$char = substr($line, $i, 1);
        					} //end of while
        				  $data[]=$value;
						  
						  print_r($data);*/
						if($rec_count>=1)
        				{
						 $state_code=trim($data[0]);
						 $dns_prod_code=trim($data[1]);
						 $margin_cost_ton=trim($data[2]);
						 $vertical_value=trim($data[3]);
						 $zone=trim($data[4]);
						/*echo "SELECT DISTINCT conversion_factor,conversion_factor_two,dns_prod_code FROM product_master 
								WHERE acedns='Y' AND product_group_code='".$$product_group_code."' 
								AND prod_desc NOT LIKE '%LUP%' AND black_list='N' AND pack_size='".$oil_type."'";
						exit();	*/	
						$sqlconversion=$CUTDB->select("SELECT DISTINCT conversion_factor,conversion_factor_two,dns_prod_code FROM product_master 
												WHERE acedns='Y'  AND prod_desc NOT LIKE '%LUP%' AND black_list='N' AND dns_prod_code='".$dns_prod_code."'");
						$conversion_factor='';
						$conversion_factor_two='';
						 foreach ($sqlconversion as $key => $prodvalue) {
							$distinct_dnsprod_code=$prodvalue->dns_prod_code;
							${'conversion_factor'.$distinct_dnsprod_code}=$prodvalue->conversion_factor;
							${'conversion_factor_two'.$distinct_dnsprod_code}=$prodvalue->conversion_factor_two;
						
						    $margin_cost_case_prodwise=$margin_cost_ton/${'conversion_factor_two'.$distinct_dnsprod_code};
						    $margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${'conversion_factor'.$distinct_dnsprod_code}),2);
							//$dns_customer_code=trim($data[6]);
							  $CUTDB->table('margin_cost_RA')->insert(array(
								  'dns_prod_code' => $distinct_dnsprod_code,
								  'state_code' => $state_code,
								  'zone' => $zone,
								  'oil_group' => '',
								  'oil_type' => '',
								  'margin_cost' => $margin_cost_case_prodwise,
								  'margin_cost_ton' => $margin_cost_ton,
								  'ip_address' => $_SERVER['REMOTE_ADDR'],
								   'vertical_value' => 'HBC:Rasoi:BIB',
								  'datetime' =>$datetime
							  ));
						  }
					   }
					$rec_count++;
           		 }
				return redirect('/uploadmargin')->with('message', 'Rasoi Margin uploaded successfully!');
			 }
         }
		 else
		 { 
		 	return redirect('/uploadmargin')->withErrors('File Extension will be .csv and file name will be margin cost-RA.csv OR margin cost-RA-rasoi.csv');
		 }
	}
	public function show_zonelisting(){
      $dbname =$this->databasename();
      $conditionvalue=Input::get('conditionfiledvalue');
      $conditionfiledname=Input::get('conditionfiledname');

      $data = Reverseauction::show_zone_data($dbname,$conditionvalue,$conditionfiledname);

      return $data;
    }
	public function show_relaese_ratereport(){
      $dbname =$this->databasename();
      $state=Input::get('state');
	  $zone=Input::get('zone');
      $releaseratereport=Reverseauction::getReleaseRateReport($dbname,$state,$zone);

      return $releaseratereport;
    }
	public function close_windowtime()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.windowtimeclose');
    }
	public function close_windowtime_submit(Request $request){
      $dbname =$this->databasename();
	  $CUTDB = Reverseauction::dydb($dbname);
	  $windowtime_id=$request->input('sl_no');
	  $time_to=$request->input('time_to');
	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $currenttime=$hour.':'.$minute.':'.$second;
	  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	  if($windowtime_id!='')
	  {
		  $CUTDB->table('RA_windowtime')
					->where('sl_no', $windowtime_id)
					->update(array('time_to' => $currenttime,'forcefully_closed'  => 'yes','forcefully_closed_time'  =>  $datetime,
					'forcefully_closed_ip'  =>$_SERVER['REMOTE_ADDR'],'prev_time_to'  =>$time_to,'last_window_time'  =>'no' ));
		return view('reverseauction.windowtimeclose')->with('successMsg','Closed Successfully!.');			
	  }
	}
	public function RAfreightdownload()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.RAfreightdownload');
    }
	public function RAmargindownload()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.RAmargindownload');
    }
	public function RAmarginexportcsv(Request $request)
    {
        $dbname =$this->databasename();
      	$CUTDB = Reverseauction::dydb($dbname);

		 $reportlist=$CUTDB->select("SELECT * FROM (SELECT MC.dns_prod_code,DATE_FORMAT(MC.datetime,'%d-%m-%Y %H:%i:%s') As date_upload,
				MC.margin_cost,MC.state_code,MC.zone,MC.oil_group,MC.oil_type,MC.margin_cost_ton,MC.vertical_value FROM margin_cost_RA MC WHERE 1 ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,4,5 ORDER BY 2 DESC");

        $data = "Date of Upload,State code,Product code,Product Group,Pack size(BP/CP),TOTAL/MT,TOTAL/CASE,Vertical Value,Zone"."\n";
        foreach($reportlist as $report) {
                $dns_prod_code=$report->dns_prod_code;
				$date_upload=$report->date_upload;
				$margin_cost=$report->margin_cost;
				$state_code=$report->state_code;
				$zone=$report->zone;
				$oil_group=$report->oil_group;
				$oil_type=$report->oil_type;
				$margin_cost_ton=$report->margin_cost_ton;
				$vertical_value=$report->vertical_value;
				
				if($oil_group =='')
				{
					$oil_group_string=Reverseauction::getProductGroupPack($dbname,$dns_prod_code);
					$oil_group_string_parts=explode("#",$oil_group_string);
					$oil_group=$oil_group_string_parts[0];
					$oil_type=$oil_group_string_parts[1];
				}
				else
				{
					$oil_group=$oil_group;
					$oil_type=$oil_type;
				}

				
$data .=$date_upload.",".$state_code.",".$dns_prod_code.",".$oil_group.",".$oil_type.",".$margin_cost_ton.",".$margin_cost.",".$vertical_value.",".$zone."\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=RA Zone wise margin.csv');
        echo $data;
        exit();
    }
	public function uploadconversion()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.uploadconversion');
    }
	public function RAconversionupload(Request $request)
    {
          $dbname =$this->databasename();
          $CUTDB = Reverseauction::dydb($dbname);
		  $file = $request->file('conversion_csv_file');
          //Display File Name
          $nickname=substr(Session::get('DBNAME'),7);
          //$name=$nickname.'_'.$file->getClientOriginalName();
		  if ($request->file('conversion_csv_file')) {
			  $name=$file->getClientOriginalName();
			  $ext=$file->getClientOriginalExtension();
		  }
		  else
		  {
			  return redirect('/uploadconversion')->withErrors('File Extension will be .csv and file name will be conversion-RA.csv');
		  }
		 // $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

    	//$destinationPath = public_path('/images');

    	//$image->move($destinationPath, $input['imagename']);
         //$this->postImage->add($input);
          if(strtolower($name)=='conversion-ra.csv')
          {
			  $input['filename'] = $file->getClientOriginalName();
			  $destinationPath = public_path('csv');
              $file->move($destinationPath,$input['filename']);
			  //$this->postImage->add($input);
              $rec_count = 0;
          	  $ins_count = 0;
          	  $err = "";
              $date=gmdate('d',strtotime('+330 minute'));
			  $month=gmdate('m',strtotime('+330 minute'));
			  $year=gmdate('Y',strtotime('+330 minute'));
			  $hour=gmdate('H',strtotime('+330 minute'));
			  $minute=gmdate('i',strtotime('+330 minute'));
			  $second=gmdate('s',strtotime('+330 minute'));
			  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
        			//$lines = file($input['filename']);
					$lines = file($destinationPath.'/'.$input['filename']);
					$rec_count=0;
					$CUTDB->table('product_unit_coversion_matrix')
							->where('acedns', 'Y')
							->update(array('acedns' => 'N','download_time' => $datetime));
        			foreach($lines as $lineval)
        			{
        				$data=explode(',',$lineval);
						if($rec_count>=1)
        				{
						 $prod_code=trim($data[0]);
						 $description=trim($data[1]);
						 $mapped_prod_code=trim($data[2]);
						 $conversion_one=trim($data[3]);
						 $addition=trim($data[4]);
						 $conversion_two=trim($data[5]);
						 $multiply=trim($data[6]);
						 
						 $sqlmappedproddesc=$CUTDB->select("SELECT DISTINCT prod_desc FROM product_master WHERE acedns='Y' 
						 						AND dns_prod_code='".$mapped_prod_code."'");
						 $mapped_prod_desc='';
						 foreach ($sqlmappedproddesc as $key => $value) {
							 $mapped_prod_desc=$value->prod_desc;
						  }
						  /*$CUTDB->table('product_unit_coversion_matrix')
							->where('mapped_prod_code', $mapped_prod_code)
							->where('prod_code', $prod_code)
							->update(array('acedns' => 'N','download_time' => $datetime
							 ));*/
							 $CUTDB->table('product_unit_coversion_matrix')->insert(array(
							  'prod_desc' => $description,
							  'prod_code' => $prod_code,
							  'mapped_prod_code' => $mapped_prod_code,
							  'mapped_prod_desc' => $mapped_prod_desc,
							  'conversion_one' => $conversion_one,
							  'addition' => $addition,
							  'conversion_two' => $conversion_two,
							   'multiply' => $multiply,
							   'acedns' => 'Y',
							  'download_time' =>$datetime
						  ));
					   }
					$rec_count++;
           		 }
				return redirect('/uploadconversion')->with('message', 'Conversion uploaded successfully!');
			 }
		 else
		 { 
		 	return redirect('/uploadconversion')->withErrors('File Extension will be .csv and file name will be conversion-RA.csv');
		 }
	}
	public function RAconversiondownload()
    {
       $sdbname =$this->databasename();
       return view('reverseauction.RAconversiondownload');
    }
	public function RAconversionexportcsv(Request $request)
    {
        $dbname =$this->databasename();
      	$CUTDB = Reverseauction::dydb($dbname);

		 $reportlist=$CUTDB->select("SELECT *  FROM product_unit_coversion_matrix WHERE acedns='Y'");
        $data = "SKU Code,Description,Mapped SKU,Conversion1,Addition,Conversion2,Multiply"."\n";
        foreach($reportlist as $report) {
                $prod_code=$report->prod_code;
				$prod_desc=$report->prod_desc;
				$mapped_prod_code=$report->mapped_prod_code;
				$conversion_one=$report->conversion_one;
				$addition=$report->addition;
				$conversion_two=$report->conversion_two;
				$multiply=$report->multiply;
				
$data .=$prod_code.",".$prod_desc.",".$mapped_prod_code.",".$conversion_one.",".$addition.",".$conversion_two.",".$multiply."\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=conversion-ra.csv');
        echo $data;
        exit();
    }

}
