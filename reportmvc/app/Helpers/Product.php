<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

class Product {

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


    // ------------- Product Group Management System Start -----------------
    /**
      *  @param  string  Database Name
      *
      * @return userlist object
     */

    public static function getAllProductGruopList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $productgrouplist = $CUTDB->table('product_group_master')
                ->paginate(50000000000000000);
        return $productgrouplist;

    }

    // ------------- Product Group Management System End -----------------

    //  ------------- Product Sub Group Management System Start ----------


    /**
     * [getAllProductSubGruopList Show all the information avilable in product_sub_group_master table]
     * @param  [type] $dbname [description]
     * @return [object]         [sub group list object]
     */
    public static function getAllProductSubGruopList($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productsubgrouplist = $CUTDB->table('product_sub_group_master')
                ->paginate(50000000000000000);
        return $productsubgrouplist;

    }

    //  ------------- Product Sub Group Management System End -----------------

    //  ------------- Product Brand Management System Start -----------------

        /**
         * [getAllProductBrandList Show all the information avilable in product_brand_master table]
         * @param  [type] $dbname [databse object]
         * @return [object]         [brand list object]
         */
        public static function getAllProductBrandList($dbname)
        {
            $CUTDB = self::dydb($dbname);
            $productbrandlists = $CUTDB->table('product_brand_master')
                    ->paginate(50000000000000000);
            return $productbrandlists;

        }

    //  ------------- Product Brand Management System End -----------------

    //  ------------- Product Management System Start -----------------

        public static function getAllProductList($dbname)
        {
            $CUTDB = self::dydb($dbname);
            $productlists = $CUTDB->table('product_master')
                    ->paginate(50000000000000000);
            return $productlists;

        }

    //  ------------- Product  Management System End -----------------

    //  ------------- Mrp  Management System Start -----------------

    public static function getAllMrpList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $mrplist = $CUTDB->table('mrp')
                ->paginate(50000000000000000);
        return $mrplist;

    }

    public static function getCusttypelist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $custyplist=$CUTDB->table('customertype_master')
                          ->where('acedns', 'Y')
                          ->get();
        return $custyplist;
    }

    //  ------------- Mrp  Management System End -----------------

    //---- All below function use in Product Group Sub group and Brand Managemnet Controller start ------


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

    public static function getProductSubGrouplist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productsubgrouplists = $CUTDB->table('product_sub_group_master')
                ->lists('product_sub_group_name','product_sub_group_code');
        return $productsubgrouplists;

    }

    public static function getProductGrouplist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productgrouplists = $CUTDB->table('product_group_master')
                ->where('acedns', 'Y')
                ->lists('product_group_name','product_group_code');
        return $productgrouplists;

    }

    public static function getProductBrandlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productbrandlists = $CUTDB->table('product_brand_master')
                ->lists('product_brand_name','product_brand_code');
        return $productbrandlists;

    }

    public static function getBranchlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productbranchlists = $CUTDB->table('branch_master')
                ->lists('branch_name','branch_code');
        return $productbranchlists;

    }

    public static function getProductlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $productlists = $CUTDB->table('product_master')
                ->lists('prod_desc','prod_code');
        return $productlists;
    }

    public static function getDestinationlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $destinationlists = $CUTDB->table('destination_master')
                ->lists('dns_destination_code','destination_code');
        return $destinationlists;
    }

    //---- All below function use in Product Group Sub group and Brand Managemnet Controller end ------



}
