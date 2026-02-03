<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;
use DB;
use Session;


class Apicommonfunction {

    /**
      *  @param  string  $dbname
      *
      * @return databse object
     */
     public static function dydb($dbname){
        $otf = new DbOnTheFly(['database' => $dbname]);
        $CUTDBOBJ = $otf->getConnection();
        return $CUTDBOBJ;
     }

      public static function insertapilog($dbname,$datetime,$emp_code='',$url){
         $CUTDB = self::dydb($dbname);
         $sqlinsertapilog=$CUTDB->table('apicalllog')
                          ->insert(array('date_time'=>$datetime,
                          'emp_code'=>$emp_code,
                          'url'=>$url
                          ));
          if(!$sqlinsertapilog){
            echo "error in logo insert";
          }
     	}

      public static function getVerificationCode($dbname,$nick_name,$deviceid){
          $CUTDB = self::dydb($dbname);
          $crntdatetime=date('Y-m-d h:i:s');
          $verificationcode=$nick_name.$crntdatetime;
          $newverificationcode=md5($verificationcode);
          $sqlcheck=$CUTDB->table('api_verification')
                    ->where('deviceid',$deviceid)
                    ->first();
          if(count($sqlcheck)>0){
            $CUTDB->table('api_verification')
            ->where('deviceid', $deviceid)
            ->limit(1)
            ->update(array('verificationcode'=>$newverificationcode,
            'apicreatedate'=>$crntdatetime
             ));
          }
          else{
            $apilog=$CUTDB->table('api_verification')
                             ->insert(array('nickname'=>$nick_name,
                             'verificationcode'=>$newverificationcode,
                             'deviceid'=> $deviceid,
                             'apicreatedate'=>$crntdatetime
                             ));

          }

          return $newverificationcode;

      }

      public static function verifyApikey($db_name,$verifactioncode){
	    $CUTDB = self::dydb($db_name);
        $apiauthcheck=$CUTDB->table('api_verification')
                        ->where('verificationcode', '=' ,$verifactioncode)
                        ->first();
        if(count($apiauthcheck)>0){
          return 1;
        }
        else{
          return 0;
        }

      }

      public static function return_employee_hierarchy($dbname,$emp_code) {
        $emphierarchy = array();
        $emphierarchystring='';
        $emphierarchy=self::employee_hierarchy_details($dbname,$emp_code,$emphierarchy);
         foreach($emphierarchy as $hierarchyval)
         {
             $emphierarchystring[]=$hierarchyval;
         }
          $emphierarchystring =implode(",",$emphierarchystring);
         //$emphierarchystring=substr($emphierarchystring,0,-1).','."'".$emp_code."'";
          return $emphierarchystring;
      }

      /**
       * [employee_hierarchy_details This function recursivly call to find emp id under one employee]
       * @param  [type] $dbname       [database name]
       * @param  [type] $emp_code     [emp code]
       * @param  [type] $emphierarchy []
       * @return [type]               [emp code array]
       */

      public static function employee_hierarchy_details($dbname,$emp_code,&$emphierarchy){

         $CUTDB = self::dydb($dbname);
         $sqlemphierarchy=$CUTDB->select("SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to)");
           if(count($sqlemphierarchy)>0)
           {
              foreach ($sqlemphierarchy as $value) {
               $emphierarchy[] = "'".$value->emp_code."'";
               self::employee_hierarchy_details($dbname,$value->emp_code,$emphierarchy);
             }
           }
          else
          {
             if(!in_array("'".$emp_code."'",$emphierarchy))
             {
               $emphierarchy[] ="'".$emp_code."'";
             }
           }
          return $emphierarchy;
      }
      public static function getNameTableMainDb($tablename='',$selectfiled,$fieldname='',$parameter){

             $fildvalue=DB::table($tablename)
                         ->select($selectfiled)
                         ->where($fieldname, '=',$parameter)
                         ->first();
             if(count($fildvalue)>0){
               $val = $fildvalue->$selectfiled;
             }
             else{
               $val ='';
             }
             return $val;

      }
      public static function getNameTable($dbname,$tablename='',$selectfiled,$fieldname='',$parameter){
             $CUTDB = self::dydb($dbname);
             $fildvalue=$CUTDB->table($tablename)
                         ->select($selectfiled)
                         ->where($fieldname, '=', $parameter)
                         ->first();
             //print_r($fildvalue);exit;
             if(count($fildvalue)>0){
               $val = ($fildvalue->$selectfiled)?$fildvalue->$selectfiled:'';
             }
             else{
               $val ='';
             }

             return $val;

      }


      public static function encrypt($str) {
        $iv = 'fedcba9876543210'; #Same as in JAVA
        $key = '0123456789abcdef'; #Same as in JAVA

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $key, $iv);
        $encrypted = mcrypt_generic($td, $str);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return bin2hex($encrypted);
      }

      public static function decrypt($code) {
        $iv = 'fedcba9876543210'; #Same as in JAVA
        $key = '0123456789abcdef'; #Same as in JAVA
        $code = self::hex2bin($code);

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $code);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return utf8_encode(trim($decrypted));
      }

      public static function hex2bin($hexdata) {
        $bindata = '';

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
              $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
      }



}
