<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

use DB;

class Dashboard {

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
     * [attendence_count this function return total attandence count for current month till
     * today admin see all employee and other see total employee under him]
     * @param  [type] $dbname   [databse name]
     * @param  [type] $username [username]
     * @return [int]           [return number of days attande]
     */

    public static function attendence_count($dbname,$username)
    {
        $CUTDB = self::dydb($dbname);
        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime('first day of this month', strtotime($today)));
        $totalcount =$CUTDB->table('employee_master')
                      ->where('acedns', '=', 'Y')
                      ->count();
        if($username=='admin')
        {
          $attendencecnt = $CUTDB->table('location')
                  ->select('emp_code')
                  ->whereBetween('date', [$startdate, date('Y-m-d')])
                  ->where('trans_id', 'LIKE', 'A%')
                  ->distinct()
                  ->get();

                //print_r($attendencecnt);exit;
        }
        else{
          $emp_hierarchy=self::return_employee_hierarchy($dbname,$username);
          $attendencecnt = $CUTDB->select("SELECT count(*) from `location` where `date` between $startdate and $today and `trans_id` LIKE 'A%' and `emp_code` IN (".$emp_hierarchy.")");
        }
        /*$datediff = strtotime($today) - $startdate;
        $day = date('d',$datediff);*/
        $mtdattendencecnt = count($attendencecnt);

        return $totalcount.'|'.$mtdattendencecnt;
    }
    /**
     * [return_employee_hierarchy This function return emp code array]
     * @param  [type] $dbname   [database name]
     * @param  [type] $emp_code [user name]
     * @return [type]           [emp code array]
     */

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

    /**
     * [tot_customer_order_price This function use to display Total customer, Qty and Price]
     * @param  [type] $dbname [database name]
     * @return [type]         [Total customer, Qty and Price]
     */

    public static function tot_customer_order_price($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime('first day of this month', strtotime($today)));
        $totalcustomer =$CUTDB->table('customer_master')
                ->where('acedns', '=', 'Y')
                ->distinct()
                ->count();

        $totalqty=$CUTDB->table('location')
              ->join('order_details', 'location.trans_id', '=', 'order_details.order_no')
              ->whereBetween('location.date', [$startdate, date('Y-m-d')])
              ->sum('qty');

        $totalcost=$CUTDB->table('location')
              ->join('order_details', 'location.trans_id', '=', 'order_details.order_no')
              ->whereBetween('location.date', [$startdate, date('Y-m-d')])
              ->sum('amount');

        $alltotal=$totalcustomer.'|'.$totalqty.'|'.$totalcost;
        return $alltotal;
    }

    /**
     * [tot_order This function use to show count total customer Order qty and Price this month]
     * @param  [type] $dbname [database name]
     * @return [numeric]         [Count of customer Qty and Price]
     */

    public static function tot_order($dbname){

      $CUTDB = self::dydb($dbname);
      $today = date('Y-m-d');
      $startdate = date('Y-m-d', strtotime('first day of this month', strtotime($today)));
      $totorder = $CUTDB->table('location')
              ->select('trans_id')
              ->whereBetween('date', [$startdate, date('Y-m-d')])
              ->where('trans_id', 'LIKE', 'O%')
              ->distinct()
              ->get();
      $totprononproorder = $CUTDB->table('location')
                      ->select('trans_id')
                      ->whereBetween('date', [$startdate, date('Y-m-d')])
                      ->where('trans_id', 'NOT LIKE', 'A%')
                      ->distinct()
                      ->get();

      $nickname = substr($dbname,7);
      $menuavilable = DB::table('menu_details')
                      ->where('nick_name', '=' ,$nickname)
                      ->first();
      //echo "<pre>";print_r($menuavilable);exit;
      $order=array();
      if($menuavilable->order=='yes'){

      $order=$CUTDB->table('location')
            ->join('order_header', 'location.trans_id', '=', 'order_header.order_no')
            ->whereBetween('location.date', [$startdate, date('Y-m-d')])
            ->where(function ( $query )
            {
               $query->where('order_header.order_no', 'LIKE', 'O%')
               ->orWhere('order_header.order_no', 'LIKE', 'NO%');
            })
            ->select('order_header.customer_code')
            ->get();

      }
      $collection=array();
      if($menuavilable->collection=='yes'){

        $collection=$CUTDB->table('location')
              ->join('payment_header', 'location.trans_id', '=', 'payment_header.receipt_id')
              ->whereBetween('location.date', [$startdate, date('Y-m-d')])
              ->where(function ( $query )
              {
                 $query->where('payment_header.receipt_id', 'like', 'P%')
                 ->orWhere('payment_header.receipt_id', 'LIKE', 'NC%');
              })
              ->select('payment_header.customer_code')
              ->get();

      }
      $stkaudit=array();
      if($menuavilable->stk_audit=='yes'){
        $stkaudit=$CUTDB->table('location')
              ->join('stock_audit', 'location.trans_id', '=', 'stock_audit.transaction_id')
              ->whereBetween('location.date', [$startdate, date('Y-m-d')])
              ->where(function ( $query )
              {
                 $query->where('stock_audit.transaction_id', 'like', 'S%')
                 ->orWhere('stock_audit.transaction_id', 'LIKE', 'NS%');
              })
              ->select('stock_audit.customer_code')
              ->get();

      }
      $marketfeedback=array();
      if($menuavilable->market_feedback=='yes'){

         $marketfeedback=$CUTDB->table('location')
               ->join('market_feedback', 'location.trans_id', '=', 'market_feedback.market_feedback_id')
               ->where('market_feedback.market_feedback_id', 'like', 'MF%')
               ->whereBetween('location.date', [$startdate, date('Y-m-d')])
               ->select('market_feedback.customer_code')
               ->get();

      }
      $checkinout=array();
      if($menuavilable->check_in_out=='yes'){

        $checkinout=$CUTDB->table('location')
              ->join('check_in_out_details', 'location.trans_id', '=', 'check_in_out_details.trans_id')
              ->where('check_in_out_details.trans_id', 'like', 'CI%')
              ->whereBetween('location.date', [$startdate, date('Y-m-d')])
              ->select('check_in_out_details.customer_code')
              ->get();
      }
      $yellowcard=array();
      if($menuavilable->yellow_card=='yes'){

        $yellowcard=$CUTDB->table('location')
              ->join('yellow_card_details', 'location.trans_id', '=', 'yellow_card_details.yellow_card_no')
              ->where('yellow_card_details.yellow_card_no', 'like', 'Y%')
              ->whereBetween('location.date', [$startdate, date('Y-m-d')])
              ->select('yellow_card_details.customer_code')
              ->get();

      }

      $totprononproorder = array_merge_recursive($order,$collection,$stkaudit,$marketfeedback,$checkinout,$yellowcard);

      $totproandnonprocust =count($totorder).'|'.count($totprononproorder);
      return $totproandnonprocust;

    }




}
