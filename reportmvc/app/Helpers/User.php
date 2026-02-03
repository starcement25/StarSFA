<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

class User {

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

    public static function userlist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $response = $CUTDB->table('employee_master')
                ->paginate(15);
        return $response;

    }


}
