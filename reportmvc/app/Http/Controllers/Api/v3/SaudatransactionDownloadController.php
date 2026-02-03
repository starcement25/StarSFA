<?php
namespace App\Http\Controllers\Api\v3;

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

class SaudatransactionDownloadController extends Controller
{
    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }
    public function saudatransactiondownload(Request $request){
    $nick_name=Apicommonfunction::decrypt($request->nickname);
    $emp_code=Apicommonfunction::decrypt($request->emp_code);
    $last_update_time=Apicommonfunction::decrypt($request->last_update_time);
    $last_update_time=str_replace('€',' ',$last_update_time);
    $incremental_download=Apicommonfunction::decrypt($request->incremental_download);
    $data_download_time=Apicommonfunction::decrypt($request->data_download_time);
    $data_download_time=str_replace('€',' ',$data_download_time);
    $verificationcode=Apicommonfunction::decrypt($request->verificationcode);
    $db_name='acedns_'.strtoupper($nick_name);
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
	$linecontents='';
	$employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
    if($isverify==1){
      if($employeewise_hierarchy=='yes'){
             $employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
             $emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
        }
		else{
		 $emp_hierarchy_condition="emp_code='".$emp_code."'";
		}
		if($incremental_download=='no'){
			$login_condition="";
		 }
		 else{
			$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		 }
		 if(gmdate('m',strtotime('+330 minute')) >='4')
		{
			$financial_from_date=gmdate('Y',strtotime('+330 minute'))."-04-01";
			$financial_to_date=(gmdate('Y',strtotime('+330 minute'))+1)."-03-31";
		}
		else
		{
			$financial_from_date=(gmdate('Y',strtotime('+330 minute'))-1)."-04-01";
			$financial_to_date=gmdate('Y',strtotime('+330 minute'))."-03-31";
		}
		if($emp_code!='C0007'){
			$sqlquery=$CUTDB->select("SELECT * FROM sauda_transaction_log WHERE ".$emp_hierarchy_condition." AND 
						SUBSTRING(sauda_date,1,10)>='".$financial_from_date."' AND SUBSTRING(sauda_date,1,10)<='".$financial_to_date."' ".$login_condition."");			
		 }
		 else
		 {
		   	$sqlquery=$CUTDB->select("SELECT * FROM sauda_transaction_log WHERE SUBSTRING(sauda_date,1,10)>='".$financial_from_date."' 
   					AND SUBSTRING(sauda_date,1,10)<='".$financial_to_date."'  ".$login_condition."");			
		 }
        $count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn  =$count.'##'.'18';
      	if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));

      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      	  foreach($sqlquery as $rowsaudatransactionlog){
			$branch_code=$rowsaudatransactionlog->branch_code;
			$broker_id=$rowsaudatransactionlog->broker_id;
			$emp_code_transaction=$rowsaudatransactionlog->emp_code;
			$sauda_date=$rowsaudatransactionlog->sauda_date;
			$sauda_no=$rowsaudatransactionlog->sauda_no;
			$customer_code=$rowsaudatransactionlog->customer_code;
			$prod_code=$rowsaudatransactionlog->prod_code;
			$qty=$rowsaudatransactionlog->qty;
			$convert_qty_one=$rowsaudatransactionlog->convert_qty_one;
			$convert_qty_two=$rowsaudatransactionlog->convert_qty_two;
			$sale_rate=$rowsaudatransactionlog->sale_rate;
			$TD=$rowsaudatransactionlog->TD;
			$premium=$rowsaudatransactionlog->premium;
			$freight_charge=$rowsaudatransactionlog->freight_charge;
			$amount=$rowsaudatransactionlog->amount;
			
			$sqlstatezone=$CUTDB->table('employee_master')
                            ->select('state','zone')
                            ->where('emp_code',$emp_code_transaction)
                            ->first();
			$state=$sqlstatezone->state;
			$zone=$sqlstatezone->zone;
			$sqlplant=$CUTDB->table('branch_master')
                            ->select('plant_name')
                            ->where('branch_code',$branch_code)
                            ->first();
			$plant_name=$sqlplant->plant_name;
			
				$contents  = (($branch_code!='')?$branch_code: ' ')."^";
				$contents  .= (($broker_id!='')?$broker_id: ' ')."^";
				$contents  .= (($emp_code_transaction!='')?$emp_code_transaction: ' ')."^";
				$contents  .= (($sauda_date!='')?$sauda_date: ' ')."^";
				$contents  .= (($sauda_no!='')?$sauda_no: ' ')."^";
				$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($qty!='')?$qty: ' ')."^";
				$contents  .= (($convert_qty_one!='')?$convert_qty_one: ' ')."^";
				$contents  .= (($convert_qty_two!='')?$convert_qty_two: ' ')."^";
				$contents  .= (($sale_rate!='')?$sale_rate: ' ')."^";
				$contents  .= (($TD!='')?$TD: ' ')."^";
				$contents  .= (($premium!='')?$premium: ' ')."^";
				$contents  .= (($freight_charge!='')?$freight_charge: ' ')."^";
				$contents  .= (($amount!='')?$amount: ' ')."^";
				$contents  .= (($plant_name!='')?$plant_name: ' ')."^";
				$contents  .= (($state!='')?$state: ' ')."^";
				$contents  .= (($zone!='')?$zone: ' ');

      			$linecontents  .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
      	}
      	else{
      		$last_update_time=str_replace('?','',$last_update_time);
      		$data_download_time=str_replace('?','',$data_download_time);
      		if(strtotime($data_download_time)>=strtotime($last_update_time)){
      			$datacontents = '0'.'##'.'0';
      		}
      		else{
      			$datacontents = '0'.'##'.'18';
      		}
      	 }
		//$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		$datacontents = Apicommonfunction::encrypt($datacontents);
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url= url('/api/v3/saudatransactiondownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
		Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
		 header("Content-type: application/text");
		 header("Content-Disposition: attachment; filename=sauda_transaction_log.txt");
		 print "$datacontents";
		//return $datacontents;
    }
	else
	{
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v3/saudatransactiondownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
	}
  }
}
