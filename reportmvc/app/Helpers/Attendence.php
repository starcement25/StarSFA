<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

use DB;

class Attendence {

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

   public static function gettodayattendence($dbname){

          $CUTDB = self::dydb($dbname);
          //$date=date('d-m-Y');
          $date='01-09-2017';
          if($date!='')
          {
                  $date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
          }
          else
          {
                  $date_condition='';
          }
          $attendancelists = $CUTDB->select("SELECT EM.emp_name,EM.emp_code,EM.state,EM.designation,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM
                                        		location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%'
                                        		AND SUBSTRING(EM.emp_code,1,1)!='C' ".$date_condition."
                                        		ORDER BY EM.emp_code ASC");

          return $attendancelists;
   }

   public static function getCheckouttime($dbname,$emp_code){

     $CUTDB = self::dydb($dbname);
     $date=date('d-m-Y');
     if($date!='')
     {
             $date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
     }
     else
     {
             $date_condition='';
     }
     $fildvalue=$CUTDB->select("SELECT SUBSTRING(LO.date,12) AS checkout_time FROM location LO WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'CH%'".$date_condition);
     if(empty($fildvalue->checkout_time))
     {
       $val="--";
     }
     else {
        $val=$fildvalue->checkout_time;
     }
     return $val;
   }

   public static function getEmpList($dbname){
      $CUTDB = self::dydb($dbname);
      $emplist=$CUTDB->table('employee_master')
                    ->select('emp_code','emp_name','state','designation')
                    ->where('acedns', 'Y')
                    ->get();
      return $emplist;
   }

   public static function getAttendenceList($dbname,$month='',$year=''){
      $CUTDB = self::dydb($dbname);

      if($month=='' && $year==''){
         $startdate="'2017-08-01'";
         $enddate="'2017-08-31'";
         //$enddate = date('Y-m-d');
         //$startdate = date('Y-m-d', strtotime('first day of this month', strtotime($enddate)));
      }
      else{
        function lastday($month = '', $year = '') {
           if (empty($month)) {
              $month = date('m');
           }
           if (empty($year)) {
              $year = date('Y');
           }
           $result = strtotime("{$year}-{$month}-01");
           $result = strtotime('-1 second', strtotime('+1 month', $result));
           return date('Y-m-d', $result);
        }

        $startdate="'$year-$month-01'";
        //echo lastday($month,$year);
        $endday=date('t',strtotime($startdate));
        $enddate1=lastday($month,$year);
        $enddate="'$enddate1'";
      }

      $attendencelists=$CUTDB->select("SELECT EM.emp_code,EM.state,EM.designation,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time,DATE_FORMAT(LO.date,'%d%m%Y') AS date FROM location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND SUBSTRING(EM.emp_code,1,1)!='C' AND DATE(LO.date) BETWEEN $startdate and $enddate ORDER BY EM.emp_code,LO.date ASC");
      foreach ($attendencelists as $key => $value) {
        $empiddate[]=$value->emp_code.$value->date;
        $empdetails[]=$value->state.$value->designation.$value->time;
      }
      $presentlist=implode(',', $empiddate);
      $empdetails=implode(',', $empdetails);
      $prefinallist=$presentlist.'@'.$startdate.'@'.$enddate.'@'.$empdetails;
      return $prefinallist;

   }


}
