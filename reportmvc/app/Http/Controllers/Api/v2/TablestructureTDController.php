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

class TablestructureTDController extends Controller
{

    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname)
    {
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }
    public function tablestructureTDdownload(Request $request){
        $nick_name=(($request->nickname)?Apicommonfunction::decrypt($request->input('nickname')):'');
        $emp_code=(($request->emp_code)?Apicommonfunction::decrypt($request->emp_code):'');
        $deviceid=(($request->deviceid)?Apicommonfunction::decrypt($request->deviceid):'');
		    $mode=(($request->mode)?Apicommonfunction::decrypt($request->mode):'');
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $counttable="";

		$sqlselectversion=$CUTDB->table('db_version')
                        ->select('db_version.version_code')
                        ->first();
		$versionCode=$sqlselectversion->version_code;

    if($mode=='INSTALL')
		{

      if(strtoupper($nick_name)=="EMAMIT" || strtoupper($nick_name)=="EMAMI"){
        $sqlquery=$CUTDB->table('table_structure_master_TD_validation')
  							->select('table_structure_master_TD_validation.t_structure_id','table_structure_master_TD_validation.table_name','table_structure_master_TD_validation.table_structure','table_structure_master_TD_validation.need_update','table_structure_master_TD_validation.is_transaction','table_structure_master_TD_validation.is_master')
  							->orderBy('table_structure_master_TD_validation.t_structure_id','asc')
  							->get();

      }
      if(strtoupper($nick_name)=="KARMA"){
        $sqlquery=$CUTDB->table('table_structure_master_price_validation')
                ->select('table_structure_master_price_validation.t_structure_id','table_structure_master_price_validation.table_name','table_structure_master_price_validation.table_structure','table_structure_master_price_validation.need_update','table_structure_master_price_validation.is_transaction','table_structure_master_price_validation.is_master')
                ->orderBy('table_structure_master_price_validation.t_structure_id','asc')
                ->get();

      }
      $counttable=count($sqlquery);

			if($emp_code!='')
			{
				$sqlselect=$CUTDB->table('table_structure_updation')
                        ->select('table_structure_updation.db_version_code')
						 ->where('table_structure_updation.device_id', '=' ,$deviceid)
						  ->where('table_structure_updation.emp_code', '=' ,$emp_code)
                          ->first();
			}
			else
			{
				$sqlselect=$CUTDB->table('table_structure_updation')
                         ->select('table_structure_updation.db_version_code')
						 ->where('table_structure_updation.device_id', '=' ,$deviceid)
						 ->where('table_structure_updation.emp_code', '=' ,'')
                         ->first();
			}

			if(count($sqlselect)<1)
			{
				$sqlInsert=$CUTDB->table('table_structure_updation')
                                ->insert(array('emp_code'=>$emp_code,
                                'db_version_code'=>$versionCode,
								'device_id'=>$deviceid,
                                'is_update'=>'0'
                                ));
			}
		}
		else
		{
			if($emp_code!='')
			{
				$sqlselect=$CUTDB->table('table_structure_updation')
                        ->select('table_structure_updation.db_version_code','table_structure_updation.is_update')
						->where('table_structure_updation.device_id', '=' ,$deviceid)
						->where('table_structure_updation.emp_code', '=' ,$emp_code)
                         ->first();
			}
			else
			{
				$sqlselect=$CUTDB->table('table_structure_updation')
                         ->select('table_structure_updation.db_version_code','table_structure_updation.is_update')
						 ->where('table_structure_updation.device_id', '=' ,$deviceid)
						 ->where('table_structure_updation.emp_code', '=' ,'')
                         ->first();
			}
			if(count($sqlselect)>0)
			{
				$is_update=$sqlselect->is_update;
				$user_db_version_code=$sqlselect->db_version_code;
				if($is_update==1){
          if($nick_name=="EMAMIT" || $nick_name=="EMAMI" ){
					$sqlquery=$CUTDB->table('table_structure_master_TD_validation')
							->select('table_structure_master_TD_validation.t_structure_id','table_structure_master_TD_validation.table_name','table_structure_master_TD_validation.table_structure','table_structure_master_TD_validation.need_update','table_structure_master_TD_validation.is_transaction','table_structure_master_TD_validation.is_master')
							 ->where('table_structure_master_TD_validation.need_update', '=' ,'Y')
							->orderBy('table_structure_master_TD_validation.t_structure_id','DESC')
							->get();
          }
          if($nick_name=="KARMA"){
            $sqlquery=$CUTDB->table('table_structure_master_price_validation')
  							->select('table_structure_master_price_validation.t_structure_id','table_structure_master_price_validation.table_name','table_structure_master_price_validation.table_structure','table_structure_master_price_validation.need_update','table_structure_master_price_validation.is_transaction','table_structure_master_price_validation.is_master')
  							 ->where('table_structure_master_price_validation.need_update', '=' ,'Y')
  							->orderBy('table_structure_master_price_validation.t_structure_id','DESC')
  							->get();
          }

					$counttable=count($sqlquery);
				}
				else
				{
					$counttable=0;
				}
		    }
		}
		if($counttable>0){
			$sqlquerybaseurl=DB::table('user_details')
	  					->select('user_details.previous_baseurl_app','user_details.current_baseurl_app')
                        ->where('nick_name',$nick_name)
                        ->first();
			$previous_baseurl_app=$sqlquerybaseurl->previous_baseurl_app;
			$current_baseurl_app=$sqlquerybaseurl->current_baseurl_app;
			if($previous_baseurl_app!=$current_baseurl_app || $mode=='INSTALL')
			{
				$baseurlchanged='Y';
			}
			else
			{
				$baseurlchanged='N';
			}
			$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
			 foreach ($sqlquery as $rowsdatadownload) {
				$contents.="<data>";
				$contents .='<table_name><![CDATA['.mb_convert_encoding($rowsdatadownload->table_name, 'UTF-8', 'UTF-8').']]></table_name>
								<table_structure><![CDATA['.mb_convert_encoding($rowsdatadownload->table_structure, 'UTF-8', 'UTF-8').']]></table_structure>
								<transaction><![CDATA['.mb_convert_encoding($rowsdatadownload->is_transaction, 'UTF-8', 'UTF-8').']]></transaction>
								<master><![CDATA['.mb_convert_encoding($rowsdatadownload->is_master, 'UTF-8', 'UTF-8').']]></master>
								<db_version><![CDATA['.mb_convert_encoding($versionCode, 'UTF-8', 'UTF-8').']]></db_version>
								<base_url_changed><![CDATA['.mb_convert_encoding($baseurlchanged, 'UTF-8', 'UTF-8').']]></base_url_changed>
								<current_baseurl_app><![CDATA['.mb_convert_encoding($current_baseurl_app, 'UTF-8', 'UTF-8').']]></current_baseurl_app>
								';
				$contents.="</data>";
			 }
			 $contents.="</recordset>";
       echo Apicommonfunction::encrypt($contents);
			//return $contents;
		}
		else
		{
			echo Apicommonfunction::encrypt('0');
		}
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v2/tablestructureTDvalidation?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&mode='.$mode);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
     }
}
