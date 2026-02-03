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

class SaudaallocationDownloadController extends Controller
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
    public function saudaallocationdownload(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
       	$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		$linecontents='';
		if($isverify==1){
			$sqlempdetails=$CUTDB->table('employee_master')
                            ->select('reporting_to','vertical_value')
                            ->where('emp_code',$emp_code)
                            ->first();
			$reporting_to=$sqlempdetails->reporting_to;
			$vertical_value=$sqlempdetails->vertical_value;
			$sqlchkallocationaccess=$CUTDB->table('sauda_allocation_access')
                            ->select('get_allocation')
                            ->where('emp_code',$emp_code)
                            ->first();
			$allocation_flag=$sqlchkallocationaccess->get_allocation;
			if($allocation_flag=='yes')
			{
				$allocation_fetch_emp_code=$emp_code;
				$emp_hierarchy_condition="emp_code='".$allocation_fetch_emp_code."'";
			}
			else if($allocation_flag=='no')
			{
				if($reporting_to!='')
				{
					$allocation_fetch_emp_code=$reporting_to;
					$emp_hierarchy_condition="emp_code='".$allocation_fetch_emp_code."'";
				}
			}
			else if($allocation_flag=='yes' && $reporting_to=='')
			{
				$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
				$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
			}			
            $sqlqueryconversion=$CUTDB->select("SELECT conversion_factor_two,UOM3,product_group_code,vertical_value FROM product_master 
									GROUP BY product_group_code");
			foreach($sqlqueryconversion as $rowfilterwiseconversion){
				$val_fillter_code=$rowfilterwiseconversion->product_group_code;
				${'conversion_two'.$val_fillter_code}=$rowfilterwiseconversion->conversion_factor_two;
				${'vertical_value'.$val_fillter_code}=$rowfilterwiseconversion->vertical_value;
			}
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
			$sqlquery=$CUTDB->select("SELECT *,BAL FROM sauda_allocation WHERE ".$emp_hierarchy_condition." GROUP BY product_filter_code");
			if(count($sqlquery)>0)
			{
				$contentsrowcolumn=count($sqlquery).'##'.'4';
				foreach($sqlquery as $rowsauda){
					$product_filter_code	=$rowsauda->product_filter_code;
					$qty=round(($rowsauda->BAL*${'conversion_two'.$product_filter_code}),2);
					if(${'vertical_value'.$product_filter_code}=='HBC:Rasoi:BIB') $qty=0;
					$allot_qty=round(($rowsauda->allot_qty*${'conversion_two'.$product_filter_code}),2);
					
					$contents  = (($emp_code!='')?$emp_code: ' ')."^";
					$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
					$contents  .= (($qty!='')?$qty: 0)."^";
					$contents  .= (($allot_qty!='')?$allot_qty: 0);
					$linecontents  .= $contents."\n";
				}
				$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents));
				$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
				$url =url('/api/v3/saudaallocationdownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
				Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
				return $datacontents;
			}
			else
			{
				$sqlqueryproductfilter=$CUTDB->select("SELECT product_group_code FROM product_group_master WHERE vertical_value='".$vertical_value."' AND acedns='Y'");
				$contentsrowcolumn=count($sqlqueryproductfilter).'##'.'4';
				foreach($sqlqueryproductfilter as $rowproductfilter)
				{
					$product_filter_code	=$rowproductfilter->product_group_code;
					$qty=0;
					$allot_qty=0;
						$contents  = (($emp_code!='')?$emp_code: ' ')."^";
						$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
						$contents  .= $qty."^";
						$contents  .= $allot_qty;
						$linecontents  .= $contents."\n";
				}
				$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
				$datacontents = Apicommonfunction::encrypt($datacontents);
				$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
				$url= url('/api/v1/saudaallocationdownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
				Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
				return $datacontents;
			}
    }
	else
	{
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v3/saudaallocationaccess?nick_name='.$nick_name.'&emp_code='.$emp_code);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
	}
  }
}
