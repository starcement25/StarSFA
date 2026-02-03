<?php
namespace App\Helpers;
use App\Database\DbOnTheFly;
class Reverseauction {

   /**
     *  @param  string  $dbname
     *
     * @return databse object
    */
    public static function dydb($dbname)
    {
       $otf = new DbOnTheFly(['database' => $dbname]);
       $CUTDBOBJ = $otf->getConnection();
       return $CUTDBOBJ;
    }

    /**
      *  @param  string  $dbname
      *
      * @return userlist object
     */

    public static function getAllEmployeeList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $employeelists = $CUTDB->table('employee_master')
                               ->select('dns_emp_code','emp_name','branch_code','email','phone_no','reporting_to','acedns','emp_code')
                               ->paginate(500000000000000);
        return $employeelists;

    }

    public static function getBrnlist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $brnlists = $CUTDB->table('branch_master')
                ->where('acedns', 'Y')
                ->lists('branch_name','branch_code');
        return $brnlists;

    }

    public static function getEmplist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('employee_master')
                ->where('acedns', 'Y')
                ->lists('emp_name','emp_code');
        return $emplists;

    }

    public static function getVarticallist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('vartical_master')
                ->where('acedns', 'Y')
                ->lists('vartical_name','vartical_name');
        return $emplists;

    }
	public static function getOilgrouplist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $oilgrouplists = $CUTDB->table('product_group_master')
                ->where('acedns', 'Y')
                ->lists('product_group_code','product_group_name');
        return $oilgrouplists;

    }
	public static function getPlantlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $plantlists = $CUTDB->table('branch_master')
				->distinct()
                ->where('acedns', 'Y')
                ->lists('plant_name');
        return $plantlists;
    }
	public static function getproductlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $prodlists = $CUTDB->table('product_master')
				->distinct()
                ->where('acedns', 'Y')
                ->lists('prod_desc');
        return $prodlists;
    }
	public static function getproductlistconverion($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $prodlistsconversion = $CUTDB->table('product_unit_coversion_matrix')
				->distinct()
				->select('mapped_prod_code','mapped_prod_desc')
				->orderBy('mapped_prod_desc', 'asc')
				->paginate(500000000000000);
        return $prodlistsconversion;
    }
	public static function getplantwisereleasedrate($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $plantwisereleasedrate = $CUTDB->select("SELECT DISTINCT PR.plant_name,PR.prod_code,PR.release_rate,PR.counter_bid_limit,PM.prod_desc,PR.base_rate,PR.indicative_rate,PR.addition,PR.multiply,PR.conversion_one,PR.conversion_two
							FROM plant_product_wise_RA_rate PR INNER JOIN product_master PM ON PM.dns_prod_code=PR.prod_code AND PR.acedns='Y' AND PM.acedns='Y' ORDER BY PR.plant_name ASC,PM.product_group_code ASC");
							
        return $plantwisereleasedrate;
    }
	public static function getBidCenterReport($dbname,$startdate)
    {
        $CUTDB = self::dydb($dbname);
		/*echo "SELECT DISTINCT RAD.*,PM.prod_desc,CM.customer_name,CM.dns_customer_code,DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%d-%m-%Y') AS bid_date,BM.branch_name,PM.conversion_factor,PM.conversion_factor_two,SM.state,EM.emp_name FROM `product_master` PM,RA_bid_rate_details RAD,
		customer_master CM,
		branch_master BM,state_master SM,employee_master EM 
		WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code AND RAD.branch_code=BM.branch_code 
		 AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y-%m-%d')='".$startdate."' AND SM.dns_state_code=CM.state_code AND PM.acedns='Y' 
		 AND SUBSTRING(RAD.bid_id,3,5)=EM.emp_code ORDER BY RAD.bid_status ASC,CM.customer_name ASC";*/
        $reportlist=$CUTDB->select("SELECT DISTINCT RAD.*,PM.prod_desc,CM.customer_name,CM.dns_customer_code,DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%d-%m-%Y') AS bid_date,BM.branch_name,PM.conversion_factor,PM.conversion_factor_two,SM.state,EM.emp_name FROM `product_master` PM,RA_bid_rate_details RAD,
		customer_master CM,
		branch_master BM,state_master SM,employee_master EM 
		WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code AND RAD.branch_code=BM.branch_code 
		 AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y-%m-%d')='".$startdate."' AND SM.dns_state_code=CM.state_code AND PM.acedns='Y' 
		 AND SUBSTRING(RAD.bid_id,3,5)=EM.emp_code AND RAD.branch_code=PM.branch_code ORDER BY RAD.bid_status ASC,CM.customer_name ASC");
        return $reportlist;
    }
	public static function getlastreleasedrate($dbname,$plant_name,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$plantwisereleasedrate=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.release_rate')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->where('plant_product_wise_RA_rate.plant_name', '=' ,$plant_name)
			->first();
			 if(count($plantwisereleasedrate)>0){
				$lastreleasedrate=$plantwisereleasedrate->release_rate;
			 }
			 else $lastreleasedrate='';
        return $lastreleasedrate;
    }
	public static function getlastreleasedratecounterbidjump($dbname,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$plantwisereleasedratecounterbidjump=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.counter_bid_jump')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->first();
			 if(count($plantwisereleasedratecounterbidjump)>0){
				$counter_bid_jump=$plantwisereleasedratecounterbidjump->counter_bid_jump;
			 }
			 else {
				 $counter_bid_jump='';
			 }
        return $counter_bid_jump;
    }
	public static function getlastreleasedratecounterbidlimit($dbname,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$lastreleasedratecounterbidlimit=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.counter_bid_limit')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->first();
			 if(count($lastreleasedratecounterbidlimit)>0){
				$counter_bid_limit=$lastreleasedratecounterbidlimit->counter_bid_limit;
			 }
			 else {
				 $counter_bid_limit='';
			 }
        return $counter_bid_limit;
    }
	public static function getlastreleasedratejumpir($dbname,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$lastreleasedratejumpir=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.rate_jump_IR')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->first();
			 if(count($lastreleasedratejumpir)>0){
				$rate_jump_IR=$lastreleasedratejumpir->rate_jump_IR;
			 }
			 else {
				 $rate_jump_IR='';
			 }
        return $rate_jump_IR;
    }
	public static function getLastWindowtime($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$downloadtime=date('Y-m-d');
		$sqlwindowtimelast=$CUTDB->table('RA_windowtime')
						->select('time_from','time_to')
						->where('rate_released_date',$downloadtime)
						->where('last_window_time','yes')
						->orderBy('time_to', 'DESC')
						->take(1)
						->get();
		if(count($sqlwindowtimelast) >0)
		{
			foreach ($sqlwindowtimelast as $key => $windowtimelastval) {			
			  $lastwindowtime=$windowtimelastval->time_to;
			}
		}
		else
		{
			$lastwindowtime='00:00:00';
		}
		return 	$lastwindowtime;						
	}
	public static function getWindowtimeDetails($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$downloadtime=date('Y-m-d');
		$sqlwindowtimedetails=$CUTDB->table('RA_windowtime')
						->select('time_from','time_to')
						->where('rate_released_date',$downloadtime)
						->orderBy('time_to', 'DESC')
					    ->take(1)
						->get();
		return  $sqlwindowtimedetails;					
	}
	public static function getReleaseRatetime($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$sqlreleaseratedetails=$CUTDB->table('plant_product_wise_RA_rate')
						->select('download_time')
						->where('acedns','Y')
						->orderBy('download_time', 'DESC')
					    ->take(1)
						->get();
		foreach ($sqlreleaseratedetails as $key => $ratedetails) {			
			  $download_time=$ratedetails->download_time;
			}				
		return  $download_time;					
	}
	public static function getWindowtime($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$downloadtime=date('Y-m-d');
		$currenttime=date('H:i:s');
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$currenttime=$hour.':'.$minute.':'.$second;
		/*echo "SELECT time_to,time_from FROM RA_windowtime WHERE rate_released_date='".$downloadtime."' AND 
										UNIX_TIMESTAMP(time_to) > UNIX_TIMESTAMP('".$currenttime."') ORDER BY time_to ASC LIMIT 0,1";*/
		$sqlwindowtime=$CUTDB->select("SELECT time_to,time_from FROM RA_windowtime WHERE rate_released_date='".$downloadtime."' AND 
										time_to > '".$currenttime."' ORDER BY time_to ASC LIMIT 0,1");
						/*->select('time_to','time_from')
						->where('rate_released_date',$downloadtime)
						->where('UNIX_TMESTAMP(time_to)','>',"UNIX_TMESTAMP($currenttime)")
						->orderBy('time_to', 'ASC')														
						->take(1)
						->get();*/
	  /*if(count($sqlwindowtime) >0)
		{		
			foreach ($sqlwindowtime as $key => $windowtimeval) {			
				$windowtime=$windowtimeval->time_to;
			}
		}
		else
		{
			$windowtime='00:00:00';
		}
		return  $windowtime;*/
		return $sqlwindowtime;				
	}
	public static function getWindowtimeclose($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$downloadtime=date('Y-m-d');
		$currenttime=date('H:i:s');
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$currenttime=$hour.':'.$minute.':'.$second;
		$sqlwindowtimeclose=$CUTDB->select("SELECT time_to,time_from FROM RA_windowtime WHERE rate_released_date='".$downloadtime."' AND 
										time_from > '".$currenttime."' ORDER BY time_to ASC LIMIT 0,1");
						/*->select('time_to','time_from')
						->where('rate_released_date',$downloadtime)
						->where('UNIX_TMESTAMP(time_to)','>',"UNIX_TMESTAMP($currenttime)")
						->orderBy('time_to', 'ASC')														
						->take(1)
						->get();*/
	  /*if(count($sqlwindowtime) >0)
		{		
			foreach ($sqlwindowtime as $key => $windowtimeval) {			
				$windowtime=$windowtimeval->time_to;
			}
		}
		else
		{
			$windowtime='00:00:00';
		}
		return  $windowtime;*/
		return $sqlwindowtimeclose;				
	}
	public static function getallWindowtime($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$downloadtime=date('Y-m-d');
		$currenttime=date('H:i:s');
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$currenttime=$hour.':'.$minute.':'.$second;
		$sqlallwindowtime=$CUTDB->select("SELECT time_to,time_from,sl_no FROM RA_windowtime WHERE rate_released_date='".$downloadtime."' AND 
										time_to > '".$currenttime."' ORDER BY time_to ASC LIMIT 0,1");
		return $sqlallwindowtime;				
	}
	public static function getWindowtimecombo($dbname)
	{
		$CUTDB = self::dydb($dbname);
		$downloadtime=date('Y-m-d');
		$currenttime=date('H:i:s');
		$sqlwindowtime=$CUTDB->table('RA_windowtime')
						->select('time_to','time_from')
						->where('rate_released_date',$downloadtime)
						->where('UNIX_TMESTAMP(time_to)','>',"UNIX_TMESTAMP($currenttime)")
						->orderBy('time_to', 'ASC')														
						->take(1)
						->get();
	  if(count($sqlwindowtime) >0)
		{		
			foreach ($sqlwindowtime as $key => $windowtimeval) {			
				$windowtime=$windowtimeval->time_to;
			}
		}
		else
		{
			$windowtime='00:00:00';
		}
		return  $windowtime;					
	}
	public static function getcustomerstate($dbname)
    {
        $CUTDB = self::dydb($dbname);
		$fildvalue=$CUTDB->table('customer_master')
						->join('state_master', 'state_master.dns_state_code', '=', 'customer_master.state_code')
                      ->select('state_master.state','customer_master.state_code')
                      ->where('customer_master.state_code', '!=','')
					  ->where('customer_master.acedns', '=','Y')
                      ->distinct()
					  ->orderBy('state_master.state', 'ASC')
                      ->lists('state_master.state','customer_master.state_code');						
        return $fildvalue;
    }
	public static function show_zone_data($dbname,$conditionvalue,$conditionfiledname)
    {
		$CUTDB = self::dydb($dbname);
		$select_control = "<select name=\"zone\" id=\"zone\" class=\"form-control\">";
		$select_control .= "<option value=\"\">Select</option>";
		$cutomerzonelist = $CUTDB->select("SELECT DISTINCT CM.zone FROM customer_master CM WHERE CM.acedns='Y' AND CM.zone <> '' 
									AND CM.state_code='".$conditionvalue."' ORDER BY CM.zone ASC");
	   if(count($cutomerzonelist) >0)
		{		
			foreach ($cutomerzonelist as $key => $cutomerzoneval) {			
			  $zone=$cutomerzoneval->zone;
			  $select_control.= "<option value=\"".$zone."\">".$zone."</option>";
			}
		}
		$select_control.= "</select>";
		return  $select_control;
	}
	public static function getReleaseRateReport($dbname,$state,$zone)
	{
		   $CUTDB = self::dydb($dbname);
		   $htmlview=' 
			<thead>
              <tr>
                <td colspan="12" align="center"><b><font size="+2" >Zonewise Reverse Auction Price Release - '.$state.' - '.$zone.'</font></b></td>
              </tr>
              <tr  align="center" height="20">
                <td  width="5%" align="center"><b>Sl</b></td>
                 <td  width="9%" align="center"><b>SKU Code</b></td>
                <td  width="17%"  align="center"><b>SKU Name</b></td>
                <td width="10%" align="center"><b>Release Rate</b></td>
                <td width="12%" align="center" colspan="2"><b>Conversion</b></td>
				<td width="9%" align="center" ><b>Margin</b></td>
                <td width="9%" align="center"><b>Base Rate</b></td>
                <td width="9%" align="center"><b>Indicative Rate</b></td>
                <td width="10%" align="center"><b>Counter Bid Limit<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(%)</b></td>
                 <td width="10%" align="center"><b>Counter Bid Rate</b></td>
              </tr>
              <tr align="center">
                <td   align="center"></td>
                <td  align="center"></td>
                <td   align="center"></td>
                <td  align="center"></td>
                <td  align="center"><b>Operand</b></td>
                <td  align="center"><b>Value</b></td>
				<td  align="center"></td>
                <td  align="center"></td>
                <td  align="center"></td>
                <td  align="center"></td>
                 <td align="center"></td>
              </tr></thead>';
              $i=1; $plant_array=array(); 
			  $plantwisereleasedrate=self::getplantwisereleasedrate($dbname);
             foreach($plantwisereleasedrate as $releasedrate){
				 echo "SELECT  margin_cost FROM margin_cost_RA
		 							WHERE dns_prod_code='".$releasedrate->prod_code."' AND state_code='".$state."' AND zone='".$zone."' 
									ORDER BY datetime DESC LIMIT 0,1";
				//exit();					
				 	$sqlmargincostprodwise=$CUTDB->select("SELECT  margin_cost FROM margin_cost_RA
		 							WHERE dns_prod_code='".$releasedrate->prod_code."' AND state_code='".$state."' AND zone='".$zone."' 
									ORDER BY datetime DESC LIMIT 0,1");
					
					/*$CUTDB->table('margin_cost_RA')
													->select('margin_cost')
													->where('dns_prod_code',$releasedrate->prod_code)
													->where('state_code',$state)
													->where('zone',$zone)
													->orderBy('datetime', 'DESC')														
													->take(1)
													->get();*/
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
                 	$release_rate=$releasedrate->release_rate;
                    $base_rate=($releasedrate->base_rate+$margin_cost);
                 	$indicative_rate=($releasedrate->indicative_rate+$margin_cost);
                 	$counter_bid_limit=$releasedrate->counter_bid_limit;
                    $counter_bid_rate=$base_rate-($base_rate * $counter_bid_limit);
                    $addition=$releasedrate->addition;
                    $multiply=$releasedrate->multiply;
                    $conversion_type_multi='';
                    $conversion_val_multi='';
                    $conversion_type_add='';
                    $conversion_val_add='';
                    
                    if($multiply=='yes')
                    {
                    	$conversion_type_multi='(X)';
                        $conversion_val_multi=$releasedrate->conversion_two;
                    }
                    if($addition=='yes')
                    {
                    	$conversion_type_add='(+)';
                        $conversion_val_add=$releasedrate->conversion_one;
                    }
                   
                  if(!in_array($releasedrate->plant_name,$plant_array))
                    {
              		$htmlview .="<tr><td colspan=\"11\" style=\"background:#330033;font-weight:bold;\" align=\"center\">$releasedrate->plant_name</td></tr>";
                       array_push($plant_array,$releasedrate->plant_name);
                    }
              	$htmlview.="<tr height=\"30\">
                	<td  align=\"right\" style=\"padding-right:2px\">$i</td>
                    <td  align=\"right\" style=\"padding-right:2px\">$releasedrate->prod_code</td>
                	<td style=\"padding-left:5px;\" >$releasedrate->prod_desc</td>
                    <td align=\"right\" style=\"padding-right:2px\">$release_rate</td>
                     <td align=\"center\" style=\"padding-left:2px\">$conversion_type_multi"."<br />".$conversion_type_add."</td>
                      <td align=\"right\" style=\"padding-right:2px\">$conversion_val_multi"."<br />".$conversion_val_add."</td>
					  <td align=\"right\" style=\"padding-right:2px\">$margin_cost</td>
                    <td align=\"right\" style=\"padding-right:2px\">$base_rate</td>
                    <td  align=\"right\" style=\"padding-right:2px\">$indicative_rate</td>
                    <td  align=\"right\" style=\"padding-right:2px\">$counter_bid_limit</td>
                    <td  align=\"right\" style=\"padding-right:2px\">".round($counter_bid_rate,0)."</td>
                 </tr>";  
                 $i++; 
				}
			  return $htmlview;
		}
	public static function RAfreightdownloadreport($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $reportlistfreight=$CUTDB->select("SELECT RAF.*,DATE_FORMAT(RAF.datetime,'%d-%m-%Y %H:%i:%s') AS date_upload,BM.branch_name,BM.dns_branch_code,RM.route_name FROM RA_route_freight RAF,branch_master BM,
										   route_master RM  WHERE RAF.branch_code=BM.branch_code AND RAF.route_code=RM.route_code AND RAF.acedns='Y' 
										AND RAF.vertical_value='HBC:Rasoi:BIB' ORDER BY RAF.datetime DESC");
        return $reportlistfreight;
    }
	public static function RAmargindownloadreport($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $reportlistmargin=$CUTDB->select("SELECT * FROM (SELECT MC.dns_prod_code,DATE_FORMAT(MC.datetime,'%d-%m-%Y %H:%i:%s') As date_upload,
				MC.margin_cost,MC.state_code,MC.zone,MC.oil_group,MC.oil_type,MC.margin_cost_ton,MC.vertical_value FROM margin_cost_RA MC WHERE 1 ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,4,5 ORDER BY 2 DESC");
        return $reportlistmargin;
    }
	public static function getsmsReport($dbname,$startdate)
    {
        $CUTDB = self::dydb($dbname);
        $reportlist=$CUTDB->select("SELECT DATE_FORMAT(SM.`sms_date`,'%d-%m-%Y') As sms_date,CM.dns_customer_code,CM.customer_name,RM.route_name,ST.state,SM.`sms_body`,SM.`sms_type`,SM.`sms_time`,SM.`response`,SM.phone_no FROM `sms_log` 	SM,customer_master CM,route_master RM,state_master ST WHERE SM.`customer_code`=CM.customer_code AND CM.acedns='Y' AND SM.`sms_date`='".$startdate."' AND CM.route_code=RM.route_code AND ST.dns_state_code=CM.state_code ORDER BY SM.sms_time DESC");
        return $reportlist;
    }
	public static function getProductGroupPack($dbname,$dns_prod_code)
	{
		$CUTDB = self::dydb($dbname);
		$sqlproductgrouppack=$CUTDB->select("SELECT  PGM.product_group_name,PM.pack_type FROM product_master PM,product_group_master PGM
						WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$dns_prod_code."' AND PM.acedns='Y'");
	   if(count($sqlproductgrouppack) >0)
		{		
			foreach ($sqlproductgrouppack as $key => $productgrouppack) {			
				$product_group_name=$productgrouppack->product_group_name;
				$pack_type=$productgrouppack->pack_type;
			}
			$grouppackval=$product_group_name.'#'.$pack_type;
		}
		else
		{
			$product_group_name='';
			$pack_type='';
			$grouppackval='';
		}
		return $grouppackval;
	}
	public static function RAconversiondownloadreport($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $reportlistconversion=$CUTDB->select("SELECT * FROM product_unit_coversion_matrix WHERE acedns='Y'");
        return $reportlistconversion;
    }

}
