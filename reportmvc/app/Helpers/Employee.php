<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

class Employee {

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


}
