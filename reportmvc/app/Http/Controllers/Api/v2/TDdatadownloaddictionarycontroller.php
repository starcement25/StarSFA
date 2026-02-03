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

class TDdatadownloaddictionarycontroller extends Controller{

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
       * [TDdatadownloaddictionary ]
       * @param Request $request [description]
       */
      public function TDdatadownloaddictionary(Request $request){
           $nick_name=Apicommonfunction::decrypt($request->input('nickname'));
           $emp_code=Apicommonfunction::decrypt($request->emp_code);
           $device_id=Apicommonfunction::decrypt($request->deviceid);
           $incremental_download=Apicommonfunction::decrypt($request->incremental_download);
           $last_update_time=Apicommonfunction::decrypt($request->last_update_time);
           $last_update_time=str_replace('@@',' ',$last_update_time);
           $verificationcode=Apicommonfunction::decrypt($request->verificationcode);

          $db_name='acedns_'.strtoupper($nick_name);
          $dydb =$this->dydb($db_name);
          $CUTDB = $dydb->getConnection();
          $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
            if($isverify==1){
                $date=gmdate('d',strtotime('+330 minute'));
                $month=gmdate('m',strtotime('+330 minute'));
                $year=gmdate('Y',strtotime('+330 minute'));

                $hour=gmdate('H',strtotime('+330 minute'));
                $minute=gmdate('i',strtotime('+330 minute'));
                $second=gmdate('s',strtotime('+330 minute'));
                //$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
                $query_validate_datetime=$year.$month.$date;
                $contents='';
                $contents =$year.'-'.$month.'-'.$date.'@@'.$hour.':'.$minute.':'.$second."\n";

                $sqlselectversion=$CUTDB->table('db_version')
                                        ->select('version_code')
                                        ->first();
                $versionCodecurrent=$sqlselectversion->version_code;
                $server_current_date=$year.'-'.$month.'-'.$date;

                $emp_val_rds=" AND emp_code='".$emp_code."'";
                //$emp_val_rds.=')';
                $currenttimestamp=date('Y-m-d H:m:s');
                $sqlselect=$CUTDB->table('emp_data_update_log')
                                        ->select('emp_code')
                                        ->where('emp_code',$emp_code)
                                        ->first();

                  if(count($sqlselect)>0){
                		  $sqlUpdate=$CUTDB->table('emp_data_update_log')
                                  ->where('emp_code', $emp_code)
                                  ->limit(1)
                                  ->update(array('update_time'=>$currenttimestamp));
                  		if(count($sqlUpdate)>0){
                  			$successval="1";
                  		}
                  		else{
                  			$successval="0";
                  		}
                	}
                	else{
                    $sqlInsert=$CUTDB->table('emp_data_update_log')->insert(array(
                      'emp_code'=>$emp_code,
                      'update_time'=>$currenttimestamp
                    ));
                		if(count($sqlInsert)>0){
                			$successval="1";
                		}
                		else{
                			$successval="0";
                		}
                	}
                  $sqlselectuserdbversion=$CUTDB->table('table_structure_updation')
                                          ->select('is_update','db_version_code')
                                          ->where('device_id',$device_id)
                                          ->where('emp_code',$emp_code)
                                          ->first();

                  if(count($sqlselectuserdbversion)>0){
                  	$is_update=$sqlselectuserdbversion->is_update;
                  	$user_db_version_code=$sqlselectuserdbversion->db_version_code;
                  	if($is_update==1 && $incremental_download=='yes'){
                  			$need_download_table_array=array();
                  			$sqlquery=$CUTDB->table('table_structure_master_TD_validation')
                                                ->select('table_name')
                                                ->where('need_update','Y')
                                                ->orderBy('t_structure_id','asc')
                                                ->get();
                         foreach($sqlquery as $rowstructuredetails){
                  				array_push($need_download_table_array,$rowstructuredetails->table_name);
                  			 }

                    		if(in_array('menu_details',$need_download_table_array)){
                    			$contents  .= 'menu_details'."\n";
                    		}
                    		if(in_array('emp_master',$need_download_table_array)){
                    				$contents  .= 'emp_master'."\n";
                    		}
                    		if(in_array('TD_realtime_validation_details',$need_download_table_array)){
                    				$contents  .= 'TD_realtime_validation_details'."\n";
                    		}
                        if(in_array('price_validation_details',$need_download_table_array)){
                    				$contents  .= 'price_validation_details'."\n";
                    		}
                        $contents= Apicommonfunction::encrypt($contents);


                    		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                    		$url = url('/api/v2/TDdatadownloaddictionary?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
                    		Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                    		/*header("Content-type: application/text");
                    		header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");*/
                    		return $contents;
                    		exit();
                  	 }
                    }

                    if($successval=="1"){
                  	if($incremental_download=='no'){
                  		$contents  .= 'menu_details'."\n";
                  		$sqlqueryemp=$CUTDB->table('employee_master')
                                         ->select('emp_code')
                                         ->where('emp_code',$emp_code)
                                         ->get();
                  		if(count($sqlqueryemp) >0){
                  			$contents  .= 'emp_master'."\n";
                  		}
                      if(strtoupper($nick_name)=='EMAMI' || strtoupper($nick_name)=='EMAMIT'){
                        $sqlqueryrelatimevalidation=$CUTDB->table('TD_realtime_validation_details')
                                           ->select('sauda_no')
                                           ->where('authorized_emp_code',$emp_code)
                                           ->get();
                    		if(count($sqlqueryrelatimevalidation)>0){
                    			$contents  .= 'TD_realtime_validation_details'."\n";
                    		}
                      }
                      if(strtoupper($nick_name)=='KARMA'){
                        $sqlquerypricevalidation=$CUTDB->table('price_validation_details')
                                           ->where('authorized_emp_code',$emp_code)
                                           ->get();
                    		if(count($sqlquerypricevalidation)>0){
                    			$contents  .= 'price_validation_details'."\n";
                    		}
                     }

                  	}
                  	else{
                      $sqlmenudetails=DB::select("SELECT menu_id FROM menu_details
                  			WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
                        		if(count($sqlmenudetails) >0 ){
                        			$menu_details_download="yes";
                        		}
                        		else{
                        			$menu_details_download="no";
                        		}

                    			if($menu_details_download=='yes'){
                    				$contents  .= 'menu_details'."\n";
                    			}

                          $sqlqueryemp=$CUTDB->select("SELECT emp_code FROM employee_master WHERE
                  								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') $emp_val_rds");
                  				if(count($sqlqueryemp) >0){
                  					$contents  .= 'emp_master'."\n";
                  				}
                          if(strtoupper($nick_name)=='EMAMI' || strtoupper($nick_name)=='EMAMIT'){
                            $sqlqueryrealtime=$CUTDB->select("SELECT sauda_no FROM TD_realtime_validation_details WHERE
                    								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') and authorized_emp_code='$emp_code'");

                    				if(count($sqlqueryrealtime)>0){
                    					$contents  .= 'TD_realtime_validation_details'."\n";
                    				}
                          }
                          if(strtoupper($nick_name)=='KARMA'){
                              $sqlquerypricevalidation=$CUTDB->select("SELECT * FROM price_validation_details WHERE
                      								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') and authorized_emp_code='$emp_code'");
                          		if(count($sqlquerypricevalidation)>0){
                          			$contents  .= 'price_validation_details'."\n";
                          		}
                          }
                  	}
                    $contents= Apicommonfunction::encrypt($contents);
                  	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                    $url = url('/api/v2/TDdatadownloaddictionary?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
                    Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                    /*header("Content-type: application/text");
                    header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");*/
                    return $contents;
                  }
                  else{
                  	echo Apicommonfunction::encrypt('0');
                  }
            }
            else{
              return Apicommonfunction::encrypt('404');
            }
      }


}
