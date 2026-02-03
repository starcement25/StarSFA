<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

class Customer {

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

    public static function getAllCustomerTypeList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $customertypelists = $CUTDB->table('customertype_master')
                ->paginate(500000000000000000000);
        return $customertypelists;

    }

    /**
      *  @param  string  $dbname
      *
      * @return userlist object
     */

    public static function getAllCustomerList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $customerlists = $CUTDB->table('customer_master')
                ->paginate(50);
        return $customerlists;

    }



    public static function getEmplist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('employee_master')
                ->where('acedns', 'Y')
                ->lists('emp_name','emp_code');
        return $emplists;

    }

    public static function getRoutelist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $routelists = $CUTDB->table('route_master')
                ->lists('route_name','route_code');
        return $routelists;

    }


    public static function getDistributorlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $distributorlists = $CUTDB->table('customer_master')
                ->where('acedns', 'Y')
                ->where('cust_type','Dealer')
                ->lists('customer_name','customer_code');
        return $distributorlists;

    }

    public static function getBranchlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productbranchlists = $CUTDB->table('branch_master')
                ->lists('branch_name','branch_code');
        return $productbranchlists;

    }

    public static function getCustomerTypelist($dbname,$id='')
    {
        $CUTDB = self::dydb($dbname);
        $cutlistlists = $CUTDB->table('customertype_master')
                ->where('acedns', 'Y')
                ->where('id','!=', $id)
                ->lists('name','id');
        return $cutlistlists;

    }


}
