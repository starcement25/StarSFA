<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;
use App\Helpers\Commonfunctions;
use Session;

class Route {

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

    public static function getAllRouteList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $routelists = $CUTDB->table('route_master')
                      ->select('route_master.route_code','route_master.route_name','customer_route_emp_relation.customer_code','employee_master.emp_name','employee_master.state','distributor_route_relation.distributor_code','customer_master.customer_name')
                      ->join('customer_route_emp_relation', 'route_master.route_code', '=', 'customer_route_emp_relation.route_code')
                      ->join('employee_master', 'customer_route_emp_relation.emp_code', '=', 'employee_master.emp_code')
                      ->join('distributor_route_relation', 'distributor_route_relation.route_code', '=', 'route_master.route_code')
                      ->join('customer_master', 'distributor_route_relation.distributor_code', '=', 'customer_master.customer_code')
                      ->groupBy('route_master.route_code')
                      ->paginate(500000000000000);
        return $routelists;

    }

    public static function getEmplist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('employee_master')
                ->where('acedns', 'Y')
                ->orderBy('emp_name','ASC')
                ->lists('emp_name','emp_code');
        return $emplists;

    }

    public static function getNormalEmpList($dbname){
       $CUTDB = self::dydb($dbname);
       $from_date=date('Y-m-d', strtotime('first day of this month', strtotime(date('Y-m-d'))));
       $emplist = $CUTDB->table('employee_master')
                     ->select('emp_code','emp_name')
                     ->where('acedns', 'Y')
                     ->orderBy('emp_name','ASC')
                     ->get();
       //print_r($emplist);exit;
       return $emplist;
    }
    public static function getNormalEmpListNew($dbname){
      $CUTDB = self::dydb($dbname);
      $from_date=date('Y-m-d', strtotime('first day of this month', strtotime(date('Y-m-d'))));
      $emplist=$CUTDB->table('location')
            ->select('location.emp_code','employee_master.emp_name')
            ->join('employee_master', 'location.emp_code', '=', 'employee_master.emp_code')
            ->whereBetween('location.date', [$from_date, date('Y-m-d')])
            ->get();
      return $emplist;

    }

    public static function getVarticallist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('vartical_master')
                ->where('acedns', 'Y')
                ->lists('vartical_name','vartical_name');
        return $emplists;

    }

    public static function getRouteReport($dbname,$empid,$startdate,$enddate)
    {
        $CUTDB = self::dydb($dbname);
        $reportlist=$CUTDB->select("SELECT rp.visit_date,em.emp_name,em.dns_emp_code,group_concat(rm.route_name separator ',') as route_name FROM `route_plan` as rp ,`employee_master` as em, route_master as rm WHERE em.emp_code=rp.emp_code and rp.route_code=rm.route_code and (rp.visit_date BETWEEN '$startdate' AND '$enddate') and rp.emp_code IN ('$empid') group by rp.visit_date,em.emp_code ORDER BY em.emp_name ASC,rp.visit_date ASC");

        return $reportlist;

    }

    public static function getRoutePlane($dbname,$empid){
      $CUTDB = self::dydb($dbname);
      $routplanlist=$CUTDB->select("SELECT group_concat(RP.route_code separator ',') as route_code,DATE_FORMAT(RP.visit_date,'%d-%m-%Y') AS visit_date,DATE_FORMAT(RP.visit_date,'%d%m%Y') AS visit_date_codehints FROM route_master RM,route_plan RP WHERE RM.route_code=RP.route_code AND RP.emp_code='".$empid."' AND MONTH(RP.visit_date)=MONTH(date_add(curdate(),interval 0 month)) AND YEAR(RP.visit_date)= YEAR(curdate()) GROUP BY RP.visit_date ORDER BY RP.visit_date ASC");
      return $routplanlist;
    }

    public static function planeStatus($dbname,$route_code,$visit_date){
      $CUTDB = self::dydb($dbname);
      $status=  $CUTDB->table('route_plan')
                  ->select('status')
                  ->where('route_code', '=', $route_code)
                  ->where('visit_date', '=', date('Y-m-d',strtotime($visit_date)))
                  ->orderBy('create_date', 'desc')
                  ->first();
      return $status;
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

    public static function getEmpHryWiseRoute($dbname,$empid){
      $CUTDB = self::dydb($dbname);
      $emp_hierarchy_choosed=Commonfunctions::return_employee_hierarchy($dbname,$empid);
      $sqlroute=$CUTDB->select("SELECT DISTINCT RM.route_code,RM.route_name FROM route_master RM,customer_route_emp_relation CRER WHERE CRER.route_code=RM.route_code AND
            CRER.emp_code IN(".$emp_hierarchy_choosed.") ORDER BY RM.route_name ASC");
      return $sqlroute;
    }

    public static function getAssignbeatperemp($dbname,$empid){
      $today = date('Y-m-d');
      $startdate=date('Y-m-d', strtotime('first day of this month', strtotime($today)));
      $CUTDB = self::dydb($dbname);
      $assignroute=  $CUTDB->table('route_plan')
                  ->where('emp_code','=',$empid)
                  ->whereBetween('visit_date',[$startdate, date('Y-m-d')])
                  ->distinct()
                  ->get();

      return $assignroute;

    }

    public static function getAssigncustperemp($dbname,$empid,$routecode){
      $CUTDB = self::dydb($dbname);

      $routcodearr=explode(',',$routecode);
      $assigncust=  $CUTDB->table('customer_route_emp_relation')
                  ->where('emp_code', '=', $empid)
                  ->whereIn('route_code',$routcodearr)
                  ->count();

      return $assigncust;
    }

    public static function getVistedcustperemp($dbname,$emp_code){
                  $customer_code_array=array();
                  $to_date = date('Y-m-d');
                  $from_date=date('Y-m-d', strtotime('first day of this month', strtotime($to_date)));
                  $CUTDB = self::dydb($dbname);
                  $sql_order_header_customers = $CUTDB->select("SELECT order_no,SUBSTRING(order_no,-19,5) AS emp_code, customer_code, SUBSTRING(order_no,-14,8) AS oh_date
                    FROM order_header WHERE ( SUBSTRING(order_no,-19,5)='".$emp_code."' OR  SUBSTRING(order_no,-19,5)='".$emp_code."')
                    AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' AND customer_code IN(SELECT customer_code FROM customer_master)
                    GROUP BY SUBSTRING(order_no,-19,5), customer_code, SUBSTRING(order_no,-14,8)
                    ORDER BY SUBSTRING(order_no,-14,8) DESC,SUBSTRING(customer_code,-14,8) DESC");

                    foreach ($sql_order_header_customers as $key => $sql_order_header_customer) {

                      $emp_code = $sql_order_header_customer->emp_code;
                      $order_header_customer_code = $sql_order_header_customer->customer_code;
                      $location_date = $sql_order_header_customer->oh_date;
                      $ordernumber=$sql_order_header_customer->order_no.'<br>';
                      $customer_concat_date = $emp_code."^".$order_header_customer_code."^".$location_date;


                      $customer_code_array[$customer_concat_date] = '1';
                    }
                    /*$sql_payment_header_customers = $CUTDB->select("SELECT receipt_id,SUBSTRING(receipt_id,-19,5) AS emp_code, customer_code, SUBSTRING(receipt_id,-14,8)
                                    AS ph_date FROM payment_header WHERE
                                    ( SUBSTRING(receipt_id,-19,5)='".$emp_code."' OR  SUBSTRING(receipt_id,-19,5)='".$emp_code."')
                                    AND DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' AND customer_code IN(SELECT customer_code FROM customer_master)
                                    GROUP BY SUBSTRING(receipt_id,-19,5), customer_code, SUBSTRING(receipt_id,-14,8)");

                    foreach($sql_payment_header_customers as $key=>$sql_payment_header_customer){
                      $emp_code = $sql_payment_header_customer->emp_code;
                      $payment_header_customer_code = $sql_payment_header_customer->customer_code;
                      $paymentordernumber=$sql_payment_header_customers->receipt_id.'<br>';
                      $location_date = $sql_payment_header_customer->ph_date;
                      $customer_concat_date = $emp_code."^".$payment_header_customer_code."^".$location_date;
                      $customer_code_array[$customer_concat_date] = '1';
                    }*/

           return count($customer_code_array);

      }
      /*public static function getOrderemp($dbname,$emp_code){

      }*/

      public static function getDailyActivityAnalysis($dbname,$start_date,$end_date,$emp_id){
        $empids=join("','",$emp_id);
        $CUTDB = self::dydb($dbname);
        $total_productive_call='';
        $total_non_productive_call='';
        $total_primary_quantity='';
        $total_secondary_quantity='';

        if($start_date!= '' && $end_date!= '')
        {

        	$table_columnname = 'No of days present';
        	$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
          $condition1="AND EM.emp_code IN ('$empids')";
        	$count_transid_condition = " ,COUNT(LO.trans_id) ";
        	$primary_secondary_quantity_condition = "AND  (DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')";
        	$group_by = " GROUP BY EM.emp_code ";
        	$attendance_condition = " COUNT(LO.trans_id) as attendance ";
        	if($start_date == $end_date){
        		$table_columnname = 'Attendance';
        		$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."'";
            $condition1="AND EM.emp_code IN ('$empids')";
        		$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
        		$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
        	}

        }
        else
        {
        	$start_date = date('Y-m-d');
        	$table_columnname = 'Attendance';
        	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."'";
          $condition1="AND EM.emp_code IN ('$empids')";
        	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
        	$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
        }
        $sales_type_array = array();
        $sql_get_custtype = $CUTDB->select("SELECT DISTINCT cust_type as sales_type FROM customer_master");
        foreach($sql_get_custtype as $key=>$val)
        {
        	$sale_type = $val->sales_type;
        	if($sale_type == '')
        		$sale_type = 'R';

        	if($sale_type == 'R')
        		$retailer = 'true';

        	if($sale_type == 'D')
        		$distributor = 'true';
        }

        if($retailer == 'true' && $distributor == 'true'){
        	$colspan = '7';
        	$table_column = "<td>Primary</td>
        					 <td>Secondary</td>";
        	$colspan_two = '2';
        }
        else if($retailer == 'true'){
        	$colspan = '6';
        	$table_column = "<td>Secondary</td>";
        }
        else if($distributor == 'true'){
        	$colspan = '6';
        	$table_column = "<td>Primary</td>";
        }

        $count = 1;
        $sql_get_empdetails = $CUTDB->select("SELECT LO.emp_code, EM.emp_name,".$attendance_condition."FROM location LO, employee_master EM WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code AND EM.acedns != 'N'".$condition.$condition1.$group_by."ORDER BY EM.emp_name ASC");

        if(count($sql_get_empdetails)>0){
        	$htmlval= "<tr class='headings'><td colspan='$colspan' align='center'>Daily Activity Analysis</td></tr>";
        	$htmlval.= "<tr class='headings_sub' align=\"center\">
        			<td>SI</td>
        			<td>Employee Name</td>
        			<td>".$table_columnname."</td>
        			<td>Productive Calls</td>
        			<td>Non Productive Calls</td>
        			<td colspan='$colspan_two'>Total</td>
        		  </tr>";
        	$htmlval.= "<tr class='TDHEAD_SUB'>
        			<td></td>
        			<td></td>
        			<td></td>
        			<td></td>
        			<td></td>
        			$table_column
        		  </tr>";

        	foreach($sql_get_empdetails as $key=>$val){
        		$emp_code = $val->emp_code;
        		$emp_name = $val->emp_name;
        		$attendance = $val->attendance;

        	/*---------------------------------> Count of productive call <--------------------------------*/
        		$sql_productive_call = $CUTDB->select("SELECT COUNT(LO.trans_id) as transid FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'S%' OR LO.trans_id LIKE 'P%') AND LO.trans_id NOT LIKE 'PA%' ".$condition)[0];
        		$productive_call = $sql_productive_call->transid;

        	/*---------------------------------> Count of non-productive call <--------------------------------*/
        		$sql_non_productive_call = $CUTDB->select("SELECT COUNT(LO.trans_id) as transid1 FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$condition)[0];

        		$non_productive_call = $sql_non_productive_call->transid1;

        		$total_productive_call += $productive_call;
        		$total_non_productive_call += $non_productive_call;

        		if($productive_call == 0 && $non_productive_call == 0){
        			$color_name = '#FF0000';
              $col_name='#fff';
            }
        		else{
        			$color_name = '';
              $col_name='';
            }

        		/*---------------------------------> Total primary sales <--------------------------------*/
        		$sql_total_primary = $CUTDB->select("SELECT SUM(OD.qty) as qtsum FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='D'")[0];
        		$primary_quantity = $sql_total_primary->qtsum;
        		$total_primary_quantity += $primary_quantity;

        		/*---------------------------------> Total secondary sales <--------------------------------*/
        		$sql_total_secondary = $CUTDB->select("SELECT SUM(OD.qty) as qtsum1 FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='R'")[0];
        		$secondary_quantity = $sql_total_secondary->qtsum1;
        		$total_secondary_quantity += $secondary_quantity;

        		if($retailer == 'true' && $distributor == 'true'){
        			$table_column_data = "<td align='right'>".number_format($primary_quantity,2)."</td>
        								  <td align='right'>".number_format($secondary_quantity,2)."</td>";
        		}
        		else if($retailer == 'true'){
        			$table_column_data = "<td align='right'>".number_format($secondary_quantity,2)."</td>";
        		}
        		else if($distributor == 'true'){
        			$table_column_data = "<td align='right'>".number_format($primary_quantity,2)."</td>";
        		}

        		$htmlval.="<tr>
        					<td>".$count."</td>
        					<td style='background:$color_name';color:$col_name>".$emp_name."</td>
        					<td align='right'>".$attendance."</td>
        					<td align='right'>".$productive_call."</td>
        					<td align='right'>".$non_productive_call."</td>
        					$table_column_data
        				  </tr>";

        		$count++;
        	}
        	if($retailer == 'true' && $distributor == 'true'){
        		$table_column_data = "<td align='right'>".number_format($total_primary_quantity,2)."</td>
        							  <td align='right'>".number_format($total_secondary_quantity,2)."</td>";
        	}
        	else if($retailer == 'true'){
        		$table_column_data = "<td align='right'>".number_format($total_secondary_quantity,2)."</td>";
        	}
        	else if($distributor == 'true'){
        		$table_column_data = "<td align='right'>".number_format($total_primary_quantity,2)."</td>";
        	}

        	$htmlval.="<tr style='font-weight:bold;'>
        			<td colspan='3' align='center'>Total</td>
        			<td align='right'>".$total_productive_call."</td>
        			<td align='right'>".$total_non_productive_call."</td>
        			$table_column_data
        		  </tr>";


        }
        else{
        	$htmlval="<font color='red'><strong>No records found</strong></font>";
        }

        return $htmlval;

    }

    public static function getMonthlist(){
          $current_month_year = date('M')."-".date('Y');
          $current_month = date('m');
          if($current_month == '01' || $current_month == '02' || $current_month == '03'){
          	$previous_year = date('Y', strtotime('-1 year'));
          	$current_year = date('Y');
          	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
          }
          else{
          	$previous_year = date('Y');
          	$current_year = date('Y', strtotime('+1 year'));
          	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
          }
          foreach ($months as $monthvalue) {
              $monthsnew[$monthvalue]=$monthvalue;
              if($monthvalue == $current_month_year)
              break;
          }


          return $monthsnew;
    }


    public static function getMonthlyAnalysisreport($dbname,$empids,$month_select){
      $CUTDB = self::dydb($dbname);
      $month = $month_select;
      $month_year = explode("-",$month);
      $monthvalue = date('m',strtotime($month_year[0]));
      $year = $month_year[1];
      $htmlnewval=" <thead><tr class=\"headings\">
                <td>SL No</td>
                <td>Emp Code</td>
                <td>Emp Name</td>
                <td>Designation</td>
                <td>State</td>
                <td>Days Present</td>
                <td>Total Calls</td>
                <td>Productive Calls</td>
                <td>Productive Calls %</td>
                <td>LPPC</td>
                <td>Value</td>
              </tr></thead>";

      /*if($emp_code == 'all'){
        $username=Session::get('USERNAME');
        if(strtoupper($username) != "ADMIN"){
          $emp_hierarchy = self::return_employee_hierarchy($dbname,$username);
          $order_check_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
          $employee_select_condition = " AND LO.emp_code IN (".$emp_hierarchy.") ";
        }
        else{
          $order_check_condition = "";
          $employee_select_condition = "";
        }
        $order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
      }
      else{


        $emp_code = $empid;
        $emp_hierarchy=self::return_employee_hierarchy($dbname,$emp_code);
        $emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
        $order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
        $order_check_condition = " AND SUBSTRING(OH.order_no,2,5)=EM.emp_code ";
        $employee_select_condition = " AND LO.emp_code = '".$emp_code."' ";
      }*/
      foreach ($empids as $empid) {

      $emp_code = $empid;
      $emp_hierarchy=self::return_employee_hierarchy($dbname,$emp_code);
      $emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
      $order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
      $order_check_condition = " AND SUBSTRING(OH.order_no,2,5)=EM.emp_code ";
      $employee_select_condition = " AND LO.emp_code = '".$emp_code."' ";

      $current_date = date('Y-m-d');
      $datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
      $primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";
      $count = 1;

          $sql_total_calls = $CUTDB->select("SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present FROM location LO WHERE SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."'".$employee_select_condition." GROUP BY LO.emp_code");
          //echo "<pre>";print_r($sql_total_calls);exit;
          if(count($sql_total_calls)>0){

             foreach($sql_total_calls as $key=>$row_total_calls){
               $sql_empname = $CUTDB->table('employee_master')
                                    ->select('emp_name','emp_code','state','designation')
                                    ->where('emp_code','=',$row_total_calls->emp_code)
                                    ->first();
                 //echo '--'.count($sql_empname);
                 if(count($sql_empname)>0)
                 {
                   $emp_name = (($sql_empname->emp_name)?$sql_empname->emp_name:'');
                   $emp_code = (($sql_empname->emp_code)?$sql_empname->emp_code:'');
                   $state = (($sql_empname->state)?$sql_empname->state:'');
                   $designation = (($sql_empname->designation)?$sql_empname->designation:'');
                   //$emp_code = $row_total_calls->emp_code;
                   $days_present = $row_total_calls->days_present;

                    $sql_prod_call = $CUTDB->select("SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls,COUNT(DISTINCT product_code) AS sku_count, SUM(amount) as amt FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'O%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'")[0];
                    $prod_call = (($sql_prod_call->productive_calls)?$sql_prod_call->productive_calls:'0');

                    $sql_nonprod_call = $CUTDB->select("SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS non_productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'NO%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'")[0];
                    $non_prod_call = (($sql_nonprod_call->non_productive_calls)?$sql_nonprod_call->non_productive_calls:'0');

                    //$sql_emp_name = $CUTDB->select("SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'")[0];
                    //$emp_name = $sql_emp_name->emp_name;

                    $total_calls = $prod_call + $non_prod_call;
                    if($prod_call>0)
                    {
                      $prod_call_percentage = ($prod_call/$total_calls)*100;
                    }
                    else{
                      $prod_call_percentage ='0';
                    }
                    $lppc_count = $sql_prod_call->sku_count;
                    $tot_amount = $sql_prod_call->amt;
                    if($lppc_count>0)
                    {
                     $lppc = number_format($lppc_count/$prod_call,2);
                    }
                    else{
                       $lppc ='0';
                    }


                    $htmlnewval.="<tbody><tr>
                        <td>".$count."</td>
                        <td>".$emp_code."</td>
                        <td>".$emp_name."</td>
                        <td>".$designation."</td>
                        <td>".$state."</td>
                        <td align=\"right\">".$days_present."</td>
                        <td align=\"right\">".$total_calls."</td>
                        <td align=\"right\">".$prod_call."</td>
                        <td align=\"right\">".number_format($prod_call_percentage,2)."</td>
                        <td align=\"right\">".$lppc."</td>
                        <td align=\"right\">".number_format($tot_amount,2)."</td>
                        </tr></tbody>";
                    $count++;
                  }
                  else{
                    continue;
                  }
            }


          }
          else{
            $htmlnewval="<tr><td><div style=\"font-weight:bold; color:red;\">No Records Found</div></td></tr>";
          }
      }

          return $htmlnewval;

     }
    public static function getStatelist($dbname){
      $CUTDB = self::dydb($dbname);
      if(strtoupper(Session::get('USERNAME')) == "ADMIN"){
      	$emp_hierarchy = "";
      	$emp_hierarchy_condition = "";
      }
      else{
      	$emp_hierarchy =self::return_employee_hierarchy(Session::get('USERNAME'));
      	$emp_hierarchy_condition = " emp_code IN (".$emp_hierarchy.") ";
      }
      //$statelists = $CUTDB->select("SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master WHERE state != ''".$emp_hierarchy_condition." ORDER BY state ASC")->lists();
      $statelists = $CUTDB->table('employee_master')
              ->select('state')
              ->where('state','!=',$emp_hierarchy_condition)
              ->orderBy('state','ASC')
              ->distinct()
              ->lists('state','state');


      return $statelists;
    }
    public static function productdetails($dbname,$p_code,$tablename,$column,$get_column){

      $CUTDB = self::dydb($dbname);
      $fildvalue=$CUTDB->table($tablename)
                  ->select($get_column)
                  ->where($column, '=', $p_code)
                  ->first();
      $val = $fildvalue->$get_column;
      return $val;
    }
    public static function getSalergister($dbname,$state,$saletype,$satrdate,$enddate){
          $CUTDB = self::dydb($dbname);
          if(strtoupper(Session::get('USERNAME')) == "ADMIN"){
          	$emp_hierarchy = "";
          	$emp_hierarchy_condition = "";
          }
          else{
          	$emp_hierarchy = self::return_employee_hierarchy(Session::get('USERNAME'));
          	$emp_hierarchy_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
          }


          $start_date = $satrdate;
          $end_date = $enddate;
          $cust_type = $saletype;
          $state = $state;

          if($cust_type == 'primary'){
          	$cust_type_condition = " (CM.cust_type = 'D' OR CM.cust_type = 'Dealer') ";
          	$cust_type_order = "";
          }
          else if($cust_type == 'secondary'){
          	$cust_type_condition = " (CM.cust_type = 'R' OR CM.cust_type = 'Sub-Dealer') ";
          	$cust_type_order = " CM.rds_tag, ";
          }

          $date_condition = " SUBSTRING(OH.order_no,-14,8) BETWEEN '".str_replace("-",'',$start_date)."' AND '".str_replace("-",'',$end_date)."' ";


          if(Session::get('nooffilter') == 1){
          	$table_header = "<td>Prod Desc</td>";
          	$colspan = '5';
          }
          if(Session::get('nooffilter') == 2){
          	$table_header = "<td>Prod Group</td>
          					<td>Prod Desc</td>";
          	$colspan = '6';
          }
          if(Session::get('nooffilter') == 3){
          	$table_header = "<td>Prod Group</td>
          					<td>Prod Sub Group</td>
          					<td>Prod Desc</td>";
          	$colspan = '7';
          }
          if(Session::get('nooffilter') == 4){
          	$table_header = "<td>Prod Group</td>
          					<td>Prod Sub Group</td>
          					<td>Brand</td>
          					<td>Prod Desc</td>";
          	$colspan = '8';
          }
          $count = 1;
          $emp_customer_date_array = array();
          $sql_order_header = $CUTDB->select("SELECT OH.order_no, SUBSTRING(OH.order_no,2,5) AS emp_code, OH.customer_code, DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.TD,EM.emp_name,EM.dns_emp_code,EM.state,CM.customer_name,CM.rds_tag FROM `order_header` OH, customer_master CM,employee_master EM WHERE
          OH.order_no LIKE 'O%' AND ".$date_condition." AND
          OH.customer_code = CM.customer_code
          AND ".$cust_type_condition.$emp_hierarchy_condition." AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND EM.state='$state'
          ORDER BY ".$cust_type_order." SUBSTRING(OH.order_no,2,5), OH.customer_code, SUBSTRING(OH.order_no,-14,8) ASC");

          if(count($sql_order_header)>0){
                $htmlreturn="<tr class='' align='center'>
                  <td>Date</td>";
                $htmlreturn.=$table_header;
                $htmlreturn.="<td>Quantity</td>
                  <td>Sale Rate</td>
                  <td>Amount</td>
                </tr>";

          	foreach($sql_order_header as $key=>$row_order_header){
          		$order_no = $row_order_header->order_no;
          		$emp_code = $row_order_header->emp_code;
          		$emp_name = $row_order_header->emp_name;
          		$dns_emp_code = $row_order_header->dns_emp_code;
          		$customer_code = $row_order_header->customer_code;
          		$order_date = $row_order_header->order_date;
          		$TD = $row_order_header->TD;
          		$customer_name = $row_order_header->customer_name;
          		$rds_tag = $row_order_header->rds_tag;
          		$state = $row_order_header->state;


          		$emp_customer_tag = $emp_code."^".$customer_code."^".$order_date;
          		if(!in_array($emp_customer_tag,$emp_customer_date_array)){
          			array_push($emp_customer_date_array,$emp_customer_tag);
          			if($cust_type == 'primary'){
          				$htmlreturn.= "<tr>
          						<td colspan=\"".$colspan."\" class=\"TDHEAD_SUB\" align=\"center\">
          						<table width=\"100%\">
          						  <tr>
          						  	<td align=\"center\">Distributor: $customer_name</td>
          							<td align=\"center\">Employee: $emp_name (".$emp_code.")</td>
          							<td align=\"center\">State: $state </td>
          						  </tr>
          						</table>
          						</td>
          					  </tr>";

          			}
          			else if($cust_type == 'secondary'){
          				$sql_rds_tag = $CUTDB->table('customer_master')
                                       ->select('customer_name')
                                       ->where('customer_code','=',$rds_tag)
                                       ->first();
                  //$CUTDB->select("SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'")[0];
                  if(count($sql_rds_tag)>0){
          				  $rds_name = $sql_rds_tag->customer_name;
                  }
                  else{
                    $rds_name ='';
                  }

          				if($rds_name != '')
          				$htmlreturn.= "<tr>
          						<td colspan=\"".$colspan."\" class=\"TDHEAD_SUB\" align=\"center\">
          						<table width=\"100%\">
          						  <tr>
          						  	<td align=\"center\">Distributor: $rds_name</td>
          							<td align=\"center\">Retailer: $customer_name</td>
          							<td align=\"center\">Employee: $emp_name (".$emp_code.")</td>
          							<td align=\"center\">State: $state </td>
          						  </tr>
          						</table>
          						</td>
          					  </tr>";
          			}
          		}

          		if($cust_type == 'secondary' && $rds_name == '')
          			continue;

          		$sql_order_details = $CUTDB->select("SELECT sku_code, qty, sale_rate, amount,mrp_code FROM order_details WHERE order_no = '".$order_no."'");
              if(count($sql_order_details)>0){
          		foreach($sql_order_details as $key=>$row_order_details){
          			$sku_code = $row_order_details->sku_code;
          			$qty = $row_order_details->qty;
          			$sale_rate = $row_order_details->sale_rate;
          			$mrp_code = $row_order_details->mrp_code;

          			if(Session::get('ismrp')=='yes'){
          			$sqlmrp=$CUTDB->select("SELECT mrp from mrp where mrp_code='".$mrp_code."'")[0];
          			$mrp=$sqlmrp->mrp;
          			$sale_rate=$mrp;
          			}
          			$amount=$qty*$sale_rate;
          			if($TD >0){
          				$amount=$amount-(($amount*$TD)/100);
          			}


          			$sql_prod_details = $CUTDB->table('product_master')
                            ->select('prod_desc', 'product_group_code', 'product_sub_group_code', 'product_brand_code')
                            ->where('prod_code', '=', $sku_code)
                            ->first();
                if(count($sql_prod_details)>0){
            			$prod_desc = $sql_prod_details->prod_desc;
            			$product_group_code = $sql_prod_details->product_group_code;
            			$product_sub_group_code = $sql_prod_details->product_sub_group_code;
            			$product_brand_code = $sql_prod_details->product_brand_code;
                }
                else{
                  $prod_desc ='';
                  $product_group_code ='';
                  $product_sub_group_code='';
                  $product_brand_code='';
                }


          			if(Session::get('nooffilter') == '1'){
          				$table_data = "<td>".$prod_desc."</td>";
          			}
          			else if(Session::get('nooffilter') == '2'){
          				$prod_group_name = productdetails($dbname,$product_group_code,'product_group_master','product_group_code','product_group_name');
          				$table_data = "<td>".$prod_group_name."</td>
          							   <td>".$prod_desc."</td>";
          			}
          			else if(Session::get('nooffilter') == '3'){
          				$prod_group_name = self::productdetails($dbname,$product_group_code,'product_group_master','product_group_code','product_group_name');
          				$prod_sub_group_name = self::productdetails($dbname,$product_sub_group_code,'product_sub_group_master','product_sub_group_code','product_sub_group_name');
          				$table_data = "<td>".$prod_group_name."</td>
          							   <td>".$prod_sub_group_name."</td>
          							   <td>".$prod_desc."</td>";
          			}
          			else if(Session::get('nooffilter') == '4'){
          				$prod_group_name = productdetails($dbname,$product_group_code,'product_group_master','product_group_code','product_group_name');
          				$prod_sub_group_name = productdetails($dbname,$product_sub_group_code,'product_sub_group_master','product_sub_group_code','product_sub_group_name');
          				$prod_brand_name = productdetails($dbname,$product_brand_code,'product_brand_master','product_brand_code','product_brand_name');
          				$table_data = "<td>".$prod_group_name."</td>
          							   <td>".$prod_sub_group_name."</td>
          							   <td>".$prod_brand_name."</td>
          							   <td>".$prod_desc."</td>";
          			}
          			$htmlreturn.= "<tr>
          					<td>".$order_date."</td>
          					".$table_data."
          					<td align=\"right\">".$qty."</td>
          					<td align=\"right\">".$sale_rate."</td>
          					<td align=\"right\">".$amount."</td>
          				  </tr>";

          			$count++;
          		}
             }
          	}
          }
          else{
            $htmlreturn= "<tr><td><span style=\"font-weight:bold; color:red;\">No Records Found!</span></td></tr>";
          }

          return $htmlreturn;
    }
   public static function getProductivDetails($dbname,$emp_code){
     $CUTDB = self::dydb($dbname);
     $to_date = date('Y-m-d');
     $from_date=date('Y-m-d', strtotime('first day of this month', strtotime($to_date)));
     $sql_prod_call =$CUTDB->select("SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls,COUNT(DISTINCT product_code) AS sku_count, SUM(amount) as amt,SUM(visit_qty) as qty,count(order_no) as OD FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') BETWEEN $to_date AND $from_date AND order_no LIKE 'O%'")[0];

     return $sql_prod_call;
   }


  public static function getNoOrderCntPerEmp($dbname,$emp_code){
    $CUTDB = self::dydb($dbname);
    $to_date = date('Y-m-d');
    $from_date=date('Y-m-d', strtotime('first day of this month', strtotime($to_date)));
    $noordercnt =$CUTDB->select("SELECT * FROM `location` where  DATE_FORMAT(SUBSTRING(trans_id,-14,8), '%Y-%m-%d') between '$from_date' AND '$to_date' and emp_code='".$emp_code."' and (trans_id like '%NO%' OR trans_id like '%NC%' OR trans_id like '%NS%')");

    return count($noordercnt);
  }
  public static function getVisitAnalysisReport($dbname,$empids){
    $CUTDB = self::dydb($dbname);
    $htmlview="<thead>
    <tr class=\"headings\">
      <td class=\"column-title\">Employee Name </td>
      <td class=\"column-title\" colspan=\"1\" align=\"center\">Beat </td>
      <td class=\"column-title\" colspan=\"1\" align=\"center\">Customer</td>
      <td class=\"column-title\" colspan=\"1\" align=\"center\">Productivity</td>
      <td class=\"column-title\" colspan=\"1\" align=\"center\">Visit Analysis</td>
      <td class=\"column-title\" colspan=\"1\">Per Capita Productivity/Day</td>
    </tr>
    <tr style=\"background-color: #fff;color: #000;\">
            <td class=\"column-title\"></td>
            <td class=\"column-title\">
                   <table>
                      <tr>
                         <td style=\"padding-right: 20px;\">Assigned</td>
                         <td>Covered</td>
                      </tr>
                   </table>
            </td>
            <td class=\"column-title\">
                   <table>
                      <tr>
                         <td style=\"padding-right: 20px;\">Avaialable</td>
                         <td>Vsited</td>
                      </tr>
                   </table>
            </td>
            <td class=\"column-title\">
                   <table>
                      <tr>
                         <td style=\"padding-right: 20px;\">Order</td>
                         <td>No Order</td>
                      </tr>
                   </table>
            </td>
            <td class=\"column-title\">
                   <table>
                      <tr>
                         <td style=\"padding-right: 20px;\">Av. Beat</td>
                         <td style=\"padding-right: 20px;\">Av. Customer Visit</td>
                         <td style=\"padding-right: 20px;\">LPPC</td>
                         <td>VPPC</td>
                      </tr>
                   </table>
            </td>
            <td class=\"column-title\">
                   <table>
                      <tr>
                         <td style=\"padding-right: 20px;\">Volume</td>
                         <td>Value</td>
                      </tr>
                   </table>
            </td>
    </tr>
  </thead>
  <tbody>";
  foreach ($empids as $empid) {
  $elements=array();
  $startdate=date('Y-m-d', strtotime('first day of this month', strtotime(date('Y-m-d'))));
  $today=date('Y-m-d');
  $days = (strtotime($today) - strtotime($startdate)) / (60 * 60 * 24);
  $name=Commonfunctions::getNameTable($dbname,'employee_master','emp_name','emp_code',$empid);
  $assignroute=  $CUTDB->table('customer_route_emp_relation')
                ->where('emp_code', '=', $empid)
                ->count();

  $coveredroute=$CUTDB->table('route_plan')
              ->where('emp_code','=',$empid)
              ->whereBetween('visit_date',[$startdate, date('Y-m-d')])
              ->distinct()
              ->get();
    foreach($coveredroute as $key=>$val){
      $elements[] =$val->route_code;
    }
    $routecode=implode(",",$elements);
    $routcodearr=explode(',',$routecode);
    $assigncustomer= $CUTDB->table('customer_route_emp_relation')
                  ->where('emp_code', '=', $empid)
                  ->whereIn('route_code',$routcodearr)
                  ->count();
    $visitedcust=$CUTDB->select("SELECT customer_code
                    FROM order_header WHERE SUBSTRING(order_no,-19,5)='".$empid."'
                    AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') BETWEEN '$startdate' AND '$today' ");

    $productivdetails=$CUTDB->select("SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls,COUNT(DISTINCT product_code) AS sku_count, SUM(amount) as amt,SUM(visit_qty) as qty,count(order_no) as OD FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$empid."' AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') BETWEEN '$startdate' AND '$today' AND order_no LIKE 'O%'")[0];
    $lppc_count = $productivdetails->sku_count;
    if($lppc_count>0)
    {
     $lppc = number_format($lppc_count/$productivdetails->productive_calls,2);
    }
    else{
       $lppc ='0';
    }
    if($productivdetails->amt>"0"){
      $vppc=number_format($productivdetails->amt/$productivdetails->productive_calls,2);
    }
    else{
      $vppc="0";
    }
    if($productivdetails->qty!=''){
      $qty=$productivdetails->qty;
    }
    else{
      $qty="0";
    }
    if($productivdetails->amt!=''){
      $amt=$productivdetails->amt;
    }
    else{
      $amt=0;
    }
    $noordercnt =$CUTDB->select("SELECT * FROM `location` where  DATE_FORMAT(SUBSTRING(trans_id,-14,8), '%Y-%m-%d') between '$startdate' AND '$today' and emp_code='".$empid."' and (trans_id like '%NO%' OR trans_id like '%NC%' OR trans_id like '%NS%')");
    $noorder=count($noordercnt);
  $htmlview.="<tr class=\"even pointer\">
          <td>".$name."</td>
          <td class=\"column-title\">
              <table>
                <tr>
                   <td style=\"width:78px;\">".$assignroute."</td>
                   <td>".count($coveredroute)."</td>
                </tr>
             </table>
         </td>
          <td class=\"column-title\">
            <table>
              <tr>
                 <td style=\"width:78px;\">".$assigncustomer."</td>
                 <td>".count($visitedcust)."</td>
              </tr>
           </table>

          </td>
          <td class=\"column-title\">
                 <table>
                    <tr>
                       <td style=\"width:78px;\">".$productivdetails->OD."</td>
                       <td>".$noorder."</td>
                    </tr>
                 </table>
          </td>
          <td class=\"column-title\">
                 <table>
                    <tr>
                       <td style=\"width:78px;\">".round(count($assignroute)/$days)."</td>
                       <td style=\"width:115px;\" >".round($assigncustomer/$days)."</td>
                       <td style=\"width:78px;\" >".$lppc."</td>
                       <td style=\"width:78px;\">".round($vppc)."</td>
                    </tr>
                 </table>
          </td>
          <td class=\"column-title\">
                 <table>
                    <tr>
                       <td style=\"width:78px;\">".$qty."</td>
                       <td>".$amt."</td>
                    </tr>
                 </table>
          </td>
        </tr>
      </tbody>";
  }

    return $htmlview;

  }
    public static function getCustomerlist($dbname)
    {
  		$CUTDB = self::dydb($dbname);
        $customerlists = $CUTDB->table('customer_master')
                ->where('acedns', 'Y')
				->where('sauda_type', 'RA')
                ->orderBy('customer_name','ASC')
                ->lists('customer_name','customer_code');
        return $customerlists;

    }
}
