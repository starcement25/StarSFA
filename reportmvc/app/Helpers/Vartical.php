<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

class Vartical {

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

    public static function getAllVarticalList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $varticallists = $CUTDB->table('vartical_master')
                ->paginate(50);
        return $varticallists;

    }

    public static function getBrnchlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $brnlists = $CUTDB->table('branch_master')
                ->where('acedns', 'Y')
                ->lists('branch_name','branch_code');
        return $brnlists;

    }



}
