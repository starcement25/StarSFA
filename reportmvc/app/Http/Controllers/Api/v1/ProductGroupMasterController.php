<?php
namespace App\Http\Controllers\Api\v1;

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

class ProductGroupMasterController extends Controller
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

  /**
   * [productgroupmasterincremental This function download product group master data]
   * @param  Request $request [description]
   * @return [type]           [description]
   */

   public function productgroupmasterincremental(Request $request){
     $nick_name=$request->input('nickname');
     $emp_code=$request->emp_code;
     $last_update_time=$request->last_update_time;
     $last_update_time=str_replace('€',' ',$last_update_time);
     $incremental_download=$request->incremental_download;
     $data_download_time=$request->data_download_time;
     $data_download_time=str_replace('€',' ',$data_download_time);
     $verificationcode=$request->verificationcode;
     $db_name='acedns_'.strtoupper($request->input('nickname'));
     $dydb =$this->dydb($db_name);
     $CUTDB = $dydb->getConnection();
     $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
     $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
     if($isverify==1){
         if($vertical_fields=='yes'){
         	$rowempvertical=$CUTDB->table('employee_master')
                               ->select('vertical_value')
                               ->where('emp_code',$emp_code)
                               ->first();
         	$emp_vertical_value=$rowempvertical->vertical_value;
         	$emp_vertical_value_array=explode(',',$emp_vertical_value);
         	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
         	$condition_one=' AND PGM.vertical_value IN ('.$emp_vertical_value.')';
         }
         else{
         	$condition_one="";
         }
         if($incremental_download=='no'){
         	$login_condition='';
         }
         else{
         	$login_condition=" AND UNIX_TIMESTAMP(PGM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
         }
         $sqlbranches=$CUTDB->table('branch_master')
                            ->get();
         if(count($sqlbranches)>1){
           $sqlquery=$CUTDB->select("SELECT DISTINCT PGM.* FROM product_group_master PGM WHERE 1 ".$condition_one." ".$login_condition." ORDER BY PGM.product_group_name ASC");
         }
         else{
           $sqlquery=$CUTDB->select("SELECT DISTINCT PGM.* FROM product_group_master PGM WHERE 1 ".$condition_one." ".$login_condition." ORDER BY PGM.product_group_name ASC");
         }
         $count=count($sqlquery);
         $cnt=1;
       	 $contentsrowcolumn  =$count.'¥'.'2';
         if($count>0){
            $date=gmdate('d',strtotime('+329 minute'));
         		$month=gmdate('m',strtotime('+329 minute'));
         		$year=gmdate('Y',strtotime('+329 minute'));

         		$hour=gmdate('H',strtotime('+329 minute'));
         		$minute=gmdate('i',strtotime('+329 minute'));
         		$second=gmdate('s',strtotime('+329 minute'));
         		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
         		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
            foreach($sqlquery as $rowproductgroup){
              $contents  = (($rowproductgroup->product_group_code!='')?$rowproductgroup->product_group_code: ' ')."^";
        			$contents  .= (($rowproductgroup->product_group_name!='')?$rowproductgroup->product_group_name: ' ');
        			$linecontents  .= $contents."\n";
            }
            $datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
         }
         else{
           $last_update_time=str_replace('?','',$last_update_time);
       		 $data_download_time=str_replace('?','',$data_download_time);
           if(strtotime($data_download_time)>=strtotime($last_update_time)){
       			$datacontents = '0'.'¥'.'0';
       		 }
       		 else{
       			$datacontents = '0'.'¥'.'2';
       		 }
         }
         $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
         $url = url('/api/v1/productgroupmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
         Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
         header("Content-type: application/text");
       	 header("Content-Disposition: attachment; filename=product_group_master.txt");
       	 print "$datacontents";
     }
     else{
       return 404;
       $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
       $url = url('/api/v1/productgroupmaster');
       Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
     }
  }


}

?>
