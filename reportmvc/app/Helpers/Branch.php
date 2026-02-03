<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

use DB;

class Branch {

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

   public static function getAllBracnchList($dbname){

          $CUTDB = self::dydb($dbname);
          $branchlists = $CUTDB->table('branch_master')
                  ->select('branch_code','dns_branch_code','branch_name','branch_location','branch_state','branch_email_id','acedns')
                  ->paginate(50);

          return $branchlists;
   }



}
