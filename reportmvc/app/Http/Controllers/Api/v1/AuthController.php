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
use Mail;

class AuthController extends Controller
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
     * [login This function use main application to login use using username and password]
     * @param  nickname,empcode,password
     * @return [type]           [xml or error code]
     */
    public function login(Request $request){
        $nick_name=$request->input('nickname');
        $emp_code=$request->emp_code;
        $newpassword=$request->newpassword;
        $deviceid=$request->deviceid;
        $db_name='acedns_'.strtoupper($request->input('nickname'));
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $verificationtoken='';
        $contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
        $sqlquery=$CUTDB->table('employee_master')
                        ->select('employee_master.emp_code','employee_master.emp_name','employee_master.sale_access','changepassword.newpassword',
                        		   'changepassword.deviceid','employee_master.acedns')
                        ->join('changepassword', 'employee_master.emp_code', '=', 'changepassword.emp_code')
                        ->where('changepassword.newpassword', '=' ,$newpassword)
                        ->where('changepassword.emp_code', '=' ,$emp_code)
                        ->first();

          if(count($sqlquery)>0){
                $device_id_database=$sqlquery->deviceid;
          			$acedns=$sqlquery->acedns;
                if(strtoupper($acedns)=='Y'){
          				if($deviceid==''){
          					echo '6';
          				}
                  else{
                      if($deviceid!=$device_id_database){ //Checking the posted deviceid and the database existed deviceid  is same or not
              						if($device_id_database==''){     // Checking that the database existed deviceid is blank or not
                            $sqlchkdeviceid=$CUTDB->table('employee_master')
                                            ->select('employee_master.emp_name')
                                            ->join('changepassword', 'employee_master.emp_code', '=', 'changepassword.emp_code')
                                            ->where('changepassword.deviceid', '=' ,$deviceid)
                                            ->first();
                              if(count($sqlchkdeviceid)>0){
                                            $emp_name=$sqlchkdeviceid->emp_name;
                           							   echo '4'.'/'.$emp_name;
                              }
                              else{
                                  $sqlUpdate=$CUTDB->table('changepassword')
                                                  ->where('emp_code', $emp_code)
                                                  ->limit(1)
                                                  ->update(array('deviceid'=>$deviceid));
                  								 if(count($sqlUpdate)>0){
                    									$sqlupdatetablestructure=$CUTDB->table('table_structure_updation')
                                                      ->where('device_id', $deviceid)
                                                      ->where('emp_code','')
                                                      ->limit(1)
                                                      ->update(array('emp_code'=>$emp_code));

                                      $apiauthcheck=$CUTDB->table('api_verification')
                                                      ->where('deviceid', '=' ,$deviceid)
                                                      ->first();
                                      if(count($apiauthcheck)>0){
                                        $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);
                                        $sqlupdateverficationtoken=$CUTDB->table('api_verification')
                                                        ->where('deviceid', $deviceid)
                                                        ->limit(1)
                                                        ->update(array('verificationcode'=>$verificationtoken));

                                      }
                                      else{
                                          $today=date('Y-m-d');
                                          $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);
                                          $CUTDB->table('api_verification')->insert(array(
                                              'nickname' => $nick_name,
                                              'deviceid' => $deviceid,
                                              'verificationcode' => $verificationtoken,
                                              'apicreatedate' =>$today
                                          ));
                                      }
                    									$contents.="<data>";
                    									$contents .='<emp_code><![CDATA['.mb_convert_encoding($sqlquery->emp_code, 'UTF-8', 'UTF-8').']]></emp_code>';
                                      $contents .='<emp_name><![CDATA['.mb_convert_encoding($sqlquery->emp_name, 'UTF-8', 'UTF-8').']]></emp_name>';
                                      $contents .='<sale_access><![CDATA['.mb_convert_encoding($sqlquery->sale_access, 'UTF-8', 'UTF-8').']]></sale_access>';
                                      $contents .='<newpassword><![CDATA['.mb_convert_encoding($sqlquery->newpassword, 'UTF-8', 'UTF-8').']]></newpassword>';
                                      $contents .='<deviceid><![CDATA['.mb_convert_encoding($sqlquery->deviceid, 'UTF-8', 'UTF-8').']]></deviceid>';
                                      $contents .='<acedns><![CDATA['.mb_convert_encoding($sqlquery->acedns, 'UTF-8', 'UTF-8').']]></acedns>';
                                      $contents .='<verificationtoken><![CDATA['.mb_convert_encoding($verificationtoken, 'UTF-8', 'UTF-8').']]></verificationtoken>';
                                      $contents.="</data>";
                    									$contents .= "</recordset>";
                    									echo $contents;
                                   }
                                   else{
                     									echo '0';
                     							 }

                            }
                            /*else{
                							echo '0';
                						}*/
                          }
                          else{
                            echo '0';
                          }
                     }
                     else{
                       $sqlupdatetablestructure=$CUTDB->table('table_structure_updation')
                                       ->where('device_id', $deviceid)
                                       ->where('emp_code','')
                                       ->limit(1)
                                       ->update(array('emp_code'=>$emp_code));
                                       $apiauthcheck=$CUTDB->table('api_verification')
                                                       ->where('deviceid', '=' ,$deviceid)
                                                       ->first();
                        if(count($apiauthcheck)>0){
                                         $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);
                                         $sqlupdateverficationtoken=$CUTDB->table('api_verification')
                                                         ->where('deviceid', $deviceid)
                                                         ->limit(1)
                                                         ->update(array('verificationcode'=>$verificationtoken));

                                       }
                                       else{
                                           $today=date('Y-m-d');
                                           $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);
                                           $CUTDB->table('api_verification')->insert(array(
                                               'nickname' => $nick_name,
                                               'deviceid' => $deviceid,
                                               'verificationcode' => $verificationtoken,
                                               'apicreatedate' =>$today
                                           ));
                          }
                       $contents.="<data>";
                       $contents .='<emp_code><![CDATA['.mb_convert_encoding($sqlquery->emp_code, 'UTF-8', 'UTF-8').']]></emp_code>';
                       $contents .='<emp_name><![CDATA['.mb_convert_encoding($sqlquery->emp_name, 'UTF-8', 'UTF-8').']]></emp_name>';
                       $contents .='<sale_access><![CDATA['.mb_convert_encoding($sqlquery->sale_access, 'UTF-8', 'UTF-8').']]></sale_access>';
                       $contents .='<newpassword><![CDATA['.mb_convert_encoding($sqlquery->newpassword, 'UTF-8', 'UTF-8').']]></newpassword>';
                       $contents .='<deviceid><![CDATA['.mb_convert_encoding($sqlquery->deviceid, 'UTF-8', 'UTF-8').']]></deviceid>';
                       $contents .='<acedns><![CDATA['.mb_convert_encoding($sqlquery->acedns, 'UTF-8', 'UTF-8').']]></acedns>';
                       $contents .='<verificationtoken><![CDATA['.mb_convert_encoding($verificationtoken, 'UTF-8', 'UTF-8').']]></verificationtoken>';
                       $contents.="</data>";
                       $contents .= "</recordset>";
                       echo $contents;
                    }
                 }
                }
                else{
          				echo 'NOT LICENSED USER';
          			}

          }
          else{
        	  echo 'NOT VALID USER';
        	}
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v1/employeelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&newpassword='.$newpassword);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      }

      /**
       * [tdrealtimelogin This is new app login function. Login using phonenumber ]
       * @param  nickname,phonenumber and deviceid
       * @return [type]           [xml or error code]
       */

      public function tdrealtimelogin(Request $request){
          $nick_name=Apicommonfunction::decrypt($request->input('nickname'));
          $phonenumber=Apicommonfunction::decrypt($request->phonenumber);
          $deviceid=Apicommonfunction::decrypt($request->deviceid);
          $db_name='acedns_'.strtoupper($nick_name);
          $dydb =$this->dydb($db_name);
          $CUTDB = $dydb->getConnection();
          $verificationtoken='';
          $emp_code='';
          $newpassword='';
          $checknickname=DB::table('user_details')
                            ->where('nick_name',$nick_name)
                            ->first();
          if(count($checknickname)>0){
          $contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
          $sqlquery=$CUTDB->table('employee_master')
                          ->select('employee_master.emp_code','employee_master.emp_name','employee_master.sale_access','changepassword.newpassword',
                          		   'changepassword.deviceid','employee_master.acedns','employee_master.phone_no')
                          ->join('changepassword', 'employee_master.emp_code', '=', 'changepassword.emp_code')
                          ->where('employee_master.phone_no', '=' ,$phonenumber)
                          ->first();
           //print_r($sqlquery);
          /*$sqlquery=$CUTDB->SELECT("SELECT employee_master.emp_code,employee_master.emp_name,employee_master.sale_access,changepassword.newpassword,
                                    changepassword.deviceid,employee_master.acedns,employee_master.phone_no FROM employee_master as employee_master,changepassword as changepassword  WHERE employee_master.emp_code=changepassword.emp_code AND employee_master.phone_no = $phonenumber AND changepassword.deviceid='$deviceid'")[0];*/
            if(count($sqlquery)>0){
                $emp_code=$sqlquery->emp_code;
                $device_id_database=$sqlquery->deviceid;
          			$acedns=$sqlquery->acedns;
                if(strtoupper($acedns)=='Y'){
          				if($deviceid==''){
          					return '6';
          				}
                  else{
                      if($deviceid!=$device_id_database){ //Checking the posted deviceid and the database existed deviceid  is same or not
              						if($device_id_database==''){     // Checking that the database existed deviceid is blank or not
                            $sqlchkdeviceid=$CUTDB->table('employee_master')
                                            ->select('employee_master.emp_name')
                                            ->join('changepassword', 'employee_master.emp_code', '=', 'changepassword.emp_code')
                                            ->where('changepassword.deviceid', '=' ,$deviceid)
                                            ->first();
                             if(count($sqlchkdeviceid)>0){
                                            $emp_name=$sqlchkdeviceid->emp_name;
                           							    return '4'.'/'.$emp_name;
                              }
                              else{
                                $date=gmdate('d',strtotime('+330 minute'));
                                $month=gmdate('m',strtotime('+330 minute'));
                                $year=gmdate('Y',strtotime('+330 minute'));

                                $hour=gmdate('H',strtotime('+330 minute'));
                                $minute=gmdate('i',strtotime('+330 minute'));
                                $second=gmdate('s',strtotime('+330 minute'));
                                $location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

                                $sqlUpdate=$CUTDB->table('changepassword')
                                                 ->where('emp_code', $emp_code)
                                                 ->limit(1)
                                                 ->update(array('deviceid'=>$deviceid,'loggedin_date_time'=>$location_date));
                  								 if(count($sqlUpdate)>0){
                                      $today=date("Y-m-d H:m:s");
                                      $sqlupdatetablestructurecheck=$CUTDB->table('table_structure_updation')
                                                      ->where('device_id', $deviceid)
                                                      ->where('emp_code',$emp_code)
                                                      ->first();
                                      if(count($sqlupdatetablestructurecheck)==0) {
                    									$sqlupdatetablestructure=$CUTDB->table('table_structure_updation')
                                                      ->where('device_id', $deviceid)
                                                      ->where('emp_code','')
                                                      ->limit(1)
                                                      ->update(array('emp_code'=>$emp_code));
                                      }
                                      $apiauthcheck=$CUTDB->table('api_verification')
                                                      ->where('deviceid', '=' ,$deviceid)
                                                      ->first();

                                      if(count($apiauthcheck)>0){
                                        $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);
                                      }
                                      else{
                                          $today=date('Y-m-d');
                                          $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);
                                      }

                    									$contents.="<data>";
                    									$contents .='<emp_code><![CDATA['.mb_convert_encoding($sqlquery->emp_code, 'UTF-8', 'UTF-8').']]></emp_code>';
                                      $contents .='<emp_name><![CDATA['.mb_convert_encoding($sqlquery->emp_name, 'UTF-8', 'UTF-8').']]></emp_name>';
                                      $contents .='<sale_access><![CDATA['.mb_convert_encoding($sqlquery->sale_access, 'UTF-8', 'UTF-8').']]></sale_access>';
                                      $contents .='<newpassword><![CDATA['.mb_convert_encoding($sqlquery->newpassword, 'UTF-8', 'UTF-8').']]></newpassword>';
                                      $contents .='<deviceid><![CDATA['.mb_convert_encoding($sqlquery->deviceid, 'UTF-8', 'UTF-8').']]></deviceid>';
                                      $contents .='<phonenumber><![CDATA['.mb_convert_encoding($sqlquery->phone_no, 'UTF-8', 'UTF-8').']]></phonenumber>';
                                      $contents .='<acedns><![CDATA['.mb_convert_encoding($sqlquery->acedns, 'UTF-8', 'UTF-8').']]></acedns>';
                                      $contents .='<verificationtoken><![CDATA['.mb_convert_encoding($verificationtoken, 'UTF-8', 'UTF-8').']]></verificationtoken>';
                                      $contents.="</data>";
                    									$contents .= "</recordset>";
                                      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                                      $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&newpassword='.$newpassword);
                                      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                    									return Apicommonfunction::encrypt($contents);
                                   }
                                   else{
                                     $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                                     $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&newpassword='.$newpassword);
                                     Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                     									return '0';
                     							 }

                            }

                          }
                          else{
                            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                            $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&newpassword='.$newpassword);
                            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                            return '0';
                          }
                     }
                     else{
                       $empblankcheck=$CUTDB->table('table_structure_updation')
                                       ->where('device_id', $deviceid)
                                       ->where('emp_code',$emp_code)
                                       ->first();
                       if(count($empblankcheck)==0){
                       $sqlupdatetablestructure=$CUTDB->table('table_structure_updation')
                                       ->where('device_id', $deviceid)
                                       ->where('emp_code',' ')
                                       ->limit(1)
                                       ->update(array('emp_code'=>$emp_code));
                       }
                                       $apiauthcheck=$CUTDB->table('api_verification')
                                                       ->where('deviceid', '=' ,$deviceid)
                                                       ->first();
                                     if(count($apiauthcheck)>0){
                                         $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);

                                       }
                                       else{
                                           $today=date('Y-m-d');
                                           $verificationtoken=Apicommonfunction::getVerificationCode($db_name,$nick_name,$deviceid);

                                       }
                                       $date=gmdate('d',strtotime('+330 minute'));
                                       $month=gmdate('m',strtotime('+330 minute'));
                                       $year=gmdate('Y',strtotime('+330 minute'));

                                       $hour=gmdate('H',strtotime('+330 minute'));
                                       $minute=gmdate('i',strtotime('+330 minute'));
                                       $second=gmdate('s',strtotime('+330 minute'));
                                       $location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

                                       $sqlUpdate=$CUTDB->table('changepassword')
                                                         ->where('emp_code', $emp_code)
                                                         ->limit(1)
                                                         ->update(array('loggedin_date_time'=>$location_date));

                               $contents.="<data>";
                               $contents .='<emp_code><![CDATA['.mb_convert_encoding($sqlquery->emp_code, 'UTF-8', 'UTF-8').']]></emp_code>';
                               $contents .='<emp_name><![CDATA['.mb_convert_encoding($sqlquery->emp_name, 'UTF-8', 'UTF-8').']]></emp_name>';
                               $contents .='<sale_access><![CDATA['.mb_convert_encoding($sqlquery->sale_access, 'UTF-8', 'UTF-8').']]></sale_access>';
                               $contents .='<newpassword><![CDATA['.mb_convert_encoding($sqlquery->newpassword, 'UTF-8', 'UTF-8').']]></newpassword>';
                               $contents .='<deviceid><![CDATA['.mb_convert_encoding($sqlquery->deviceid, 'UTF-8', 'UTF-8').']]></deviceid>';
                               $contents .='<acedns><![CDATA['.mb_convert_encoding($sqlquery->acedns, 'UTF-8', 'UTF-8').']]></acedns>';
                               $contents .='<verificationtoken><![CDATA['.mb_convert_encoding($verificationtoken, 'UTF-8', 'UTF-8').']]></verificationtoken>';
                               $contents.="</data>";
                               $contents .= "</recordset>";
                               $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                               $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&newpassword='.$newpassword);
                               Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                               return Apicommonfunction::encrypt($contents);
                        }
                  }
                }
                else{
                  $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                  $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&newpassword='.$newpassword);
                  Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          				return 'NOT LICENSED USER';
          			}

            }
              else{
                $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&phonenumer='.$phonenumber);
                Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            	  return 'NOT VALID USER';
            	}
           }
           else{
             $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
             $url = url('/api/v1/tdrealtimelogin?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceid='.$deviceid.'&phonenumber='.$phonenumber);
             Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
              return 'Invalid Nick Name';
           }

        }

        /**
         * [updateApp check any upadte avilable in app or not]
         * @param  nickname,deviceId,versionCode,emp_code,verificationcode need
         * @return [type]           [description]
         */

        public function updateApp(Request $request){
          $nick_name=$request->input('nickname');
          $db_name='acedns_'.strtoupper($request->input('nickname'));
          $deviceId=$request->deviceId;
          $versionCode=$request->versionCode;
          $emp_code=$request->emp_code;
          $verificationcode=$request->verificationcode;
          $db_name='acedns_'.strtoupper($request->input('nickname'));
          $dydb =$this->dydb($db_name);
          $CUTDB = $dydb->getConnection();
          $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
          if($isverify==1){
             $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
             $url = url('/api/v1/updateAppVersion?nick_name='.$nick_name.'&emp_code='.$emp_code.'&deviceId='.$deviceId.'&versionCode='.$versionCode);
             Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
             $sqlempname=$apiauthcheck=$CUTDB->table('employee_master')
                             ->select('emp_name')
                             ->where('emp_code', '=' ,$emp_code)
                             ->first();
             $emp_name=$sqlempname->emp_name;

             $sqlappversion=$apiauthcheck=$CUTDB->table('app_version')
                             ->first();
             $app_version_latest=$sqlappversion->version_code;
             $release_date=date('d/m/Y',strtotime($sqlappversion->date));

             $sqlselect=$apiauthcheck=$CUTDB->table('app_updation')
                             ->where('device_id', '=' ,$deviceId)
                             ->first();
              if(count($sqlselect)>0){
                  if($versionCode > $sqlselect->version_code ){
                    $sqlUpdate= $CUTDB->table('app_updation')
                      ->where('deviceid', $deviceId)
                      ->limit(1)
                      ->update(array('version_code'=>$versionCode,
                      'is_update'=>0
                       ));
              			if(count($sqlUpdate)>0){
              				return "2";
              			}
              			else{
              				return "3";
              			}
                    $date=gmdate('d',strtotime('+329 minute'));
              			$month=gmdate('m',strtotime('+329 minute'));
              			$year=gmdate('Y',strtotime('+329 minute'));

              			$hour=gmdate('H',strtotime('+329 minute'));
              			$minute=gmdate('i',strtotime('+329 minute'));
              			$second=gmdate('s',strtotime('+329 minute'));
              			$update_date=$date.'/'.$month.'/'.$year;
              			$update_time=$hour.':'.$minute.':'.$second;
                    $db_update_mail=Apicommonfunction::getNameTable($db_name,'company_master','admin_email_id','comp_name',$nick_name);
                    $mailsubj="ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name";
                    $bcc="acedns@coral.in";
                    $data = array('db_update_mail'=>$db_update_mail,'mailsubj'=>$mailsubj,'bcc'=>$bcc,'nick_name'=>strtoupper($nick_name),'versionCode'=>$versionCode,'typeversion'=>'App Version','release_date'=>$release_date,'emp_name'=>$emp_name,'emp_code'=>$emp_code,'update_date'=>$update_date,'update_time'=>$update_time);

                    Mail::send(['html'=>'mail'], $data, function($message) use ($data) {
                       $message->to($data['db_update_mail'])
                                ->from('acedns@coral.in','Acedns')
                                ->bcc($data['bcc'])
                               ->subject($data['mailsubj']);
                    });
                  }
                  elseif($sqlselect->version_code==$versionCode && $sqlselect->is_update==1){
                    return "1-".$app_version_latest;
                  }
                  else if($versionCode < $sqlselect->version_code){
              			return "1-".$app_version_latest;
              		}
              		else{
              			return "0";
              		}

              }
              else{
                $sqlInsert=$CUTDB->table('app_updation')
                                 ->insert(array('version_code'=>$versionCode,
                                 'device_id'=>$deviceId,
                                 'is_update'=>'0'
                                 ));
            		if(count($sqlInsert)>0){
            			return "4".'/'.$app_version_latest;
            		}
            		else{
            			return "3";
            		}
              }

          }
          else{
            return 404;
          }

      }

      /**
       * [updateFirstLoginDateTime description]
       * @param  Request $request [description]
       * @return [type]           [description]
       */

       public function updateFirstLoginDateTime(Request $request){
          $nick_name=$request->input('nickname');
          $emp_code=$request->emp_code;
          $device_id=$request->device_id;
          $db_version=$request->db_version;
          $verificationcode=$request->verificationcode;
          $db_name='acedns_'.strtoupper($request->input('nickname'));
          $dydb =$this->dydb($db_name);
          $CUTDB = $dydb->getConnection();
          $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
          if($isverify==1){
            $rowselectversion=$CUTDB->table('db_version')
                                    ->select('version_code','date')
                                    ->first();
            $versionCode=$rowselectversion->version_code;
            $release_date=date('d/m/Y',strtotime($rowselectversion->date));

            $rowselect=$CUTDB->table('table_structure_updation')
                            ->select('is_update','db_version_code')
                            ->where('device_id',$device_id)
                            ->where('emp_code',$emp_code)
                            ->first();
            if(count($rowselect)>0){
              $is_update=$rowselect->is_update;
              $existed_db_version_code=$rowselect->db_version_code;
                if($is_update==1){
                  if($versionCode==$db_version){
                      $sqlUpdatetablestructure=$CUTDB->table('table_structure_updation')
                                              ->where('device_id', $device_id)
                                              ->where('emp_code', $emp_code)
                                              ->limit(1)
                                              ->update(array('db_version_code'=>$versionCode,
                                              'is_update'=>'0'
                                            ));
                      $date=gmdate('d',strtotime('+329 minute'));
                      $month=gmdate('m',strtotime('+329 minute'));
                      $year=gmdate('Y',strtotime('+329 minute'));

                      $hour=gmdate('H',strtotime('+329 minute'));
                      $minute=gmdate('i',strtotime('+329 minute'));
                      $second=gmdate('s',strtotime('+329 minute'));
                      $update_date=$date.'/'.$month.'/'.$year;
                      $update_time=$hour.':'.$minute.':'.$second;
                      $emp_name=Apicommonfunction::getNameTable($db_name,'employee_master','emp_name','emp_code',$emp_code);
                      $db_update_mail=Apicommonfunction::getNameTable($db_name,'company_master','admin_email_id','comp_name',$nick_name);
                      $mailsubj="ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name";
                      $bcc="acedns@coral.in";
                      $data = array('db_update_mail'=>$db_update_mail,'mailsubj'=>$mailsubj,'bcc'=>$bcc,'nick_name'=>strtoupper($nick_name),'typeversion'=>'DB Version','versionCode'=>$versionCode,'release_date'=>$release_date,'emp_name'=>$emp_name,'emp_code'=>$emp_code,'update_date'=>$update_date,'update_time'=>$update_time);

                      Mail::send(['html'=>'mail'], $data, function($message) use ($data) {
                         $message->to($data['db_update_mail'])
                                  ->from('acedns@coral.in','Acedns')
                                  ->bcc($data['bcc'])
                                 ->subject($data['mailsubj']);
                      });
                }
              }
            }
            else{
              $sqlInsert=$CUTDB->table('table_structure_updation')
                               ->insert(array('emp_code'=>$emp_code,
                               'db_version_code'=>$versionCode,
                               'device_id'=> $device_id,
                               'is_update'=>'0'
                               ));
            }
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url = url('/api/v1/confirmationdownloadtd?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&db_version='.$db_version);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          }
          else{
            return 404;
          }

    }

}
