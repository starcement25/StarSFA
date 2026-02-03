<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Helpers\Route;
use App\Helpers\Commonfunctions;
use Session;

class RouteController extends Controller
{
    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $sdbname =$this->databasename();
        $routes=Route::getAllRouteList($sdbname);
        return view('route.index',compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $employee_list=Route::getEmplist($sdbname);
        $vartical_list=Route::getVarticallist($sdbname);
        return view('route.create',compact('employee_list','vartical_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Route::dydb($dbname);

      $dnsroutecode=$request->input('route_code');
      $routename=$request->input('route_name');
      $empname=$request->input('emp_code');
      $varticalname=$request->input('vartical_name');
      $downloadtime=date('Y-m-d H:i:s');
      $routecode=$CUTDB->table('route_master')
                       ->where('route_code','NOT LIKE','N%')
                       ->max('route_code');
      if($routecode!='')
      {
        $routecode1=substr($routecode,3);
        $newcode=$routecode1+1;
        $routecode='RT/'.$newcode;
      }
      else {
        $routecode='RT/1';
      }


      $CUTDB->table('route_master')->insert(array(
          'route_code' => $routecode,
          'dns_route_code' => $dnsroutecode,
          'route_name' => $routename,
          'download_time' =>$downloadtime
      ));
      return redirect('/route')->with('message', 'Success!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
      $sdbname =$this->databasename();
      $tablename="route_master";
      $fieldname="route_code";
      $newparame=substr($id,0,2);
      $newparame1=substr($id,2);
      if($newparame=='RT')
        $parameter=$newparame.'/'.$newparame1;
      elseif($newparame=='NR')
      {
        $newparame2=substr($id,0,3);
        $newparame3=substr($id,3);
        $parameter=$newparame2.'/'.$newparame3;
      }
      $routedetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      $employee_list=Route::getEmplist($sdbname);
      $vartical_list=Route::getVarticallist($sdbname);
      return view('route.view',compact('routedetails','employee_list','vartical_list'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $sdbname =$this->databasename();
        $tablename="route_master";
        $fieldname="route_code";
        $newparame=substr($id,0,2);
        $newparame1=substr($id,2);
        if($newparame=='RT')
          $parameter=$newparame.'/'.$newparame1;
        elseif($newparame=='NR')
        {
          $newparame2=substr($id,0,3);
          $newparame3=substr($id,3);
          $parameter=$newparame2.'/'.$newparame3;
        }
        $routedetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        $employee_list=Route::getEmplist($sdbname);
        $vartical_list=Route::getVarticallist($sdbname);
        return view('route.edit',compact('routedetails','employee_list','vartical_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $dbname =$this->databasename();
        $CUTDB = Route::dydb($dbname);

        $dnsroutecode=Input::get('route_code');
        $routename=Input::get('route_name');
        $empname=Input::get('emp_code');
        $varticalname=Input::get('vartical_name');

        $newparame=substr($id,0,2);
        $newparame1=substr($id,2);
        if($newparame=='RT')
          $parameter=$newparame.'/'.$newparame1;
        elseif($newparame=='NR')
        {
          $newparame2=substr($id,0,3);
          $newparame3=substr($id,3);
          $parameter=$newparame2.'/'.$newparame3;
        }


        $CUTDB->table('route_master')
        ->where('route_code', $parameter)
        ->limit(1)
        ->update(array(
                'dns_route_code' => $dnsroutecode,
                'route_name' => $routename
         ));

        return redirect('/route')->with('message', 'Success!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function routeUploadFile(Request $request)
    {

      $dbname =$this->databasename();
      $CUTDB = Branch::dydb($dbname);

      $file = $request->file('route_csv_file');

      //Display File Name
      $name=$file->getClientOriginalName();
      $ext=$file->getClientOriginalExtension();

      if($ext=='csv')
      {
          $destinationPath = 'uploads/csv';
          $file->move($destinationPath,$file->getClientOriginalName());
          $rec_count = 0;
          $ins_count = 0;
          $err = "";
          $lines = file($filename);
          foreach($lines as $line)
          {
            $i = 0;
            $char = substr($line, $i, 1);
            $value ="";
            $data="";
            $double_coute_found = false;
            if($rec_count>=1)
            {
              while($char!="")
              {
                if($double_coute_found && $char=="\"")
                {
                  $double_coute_found = false;
                  $i++;
                  $char = substr($line, $i, 1);
                  continue;
                }

                if(!$double_coute_found && $char=="\"")
                {

                  $double_coute_found = true;
                  $i++;
                  $char = substr($line, $i, 1);
                  continue;
                }

                if($char=="," && !$double_coute_found)
                {
                  $data[]=$value;
                  $value = "";
                }
                else
                {
                $value .= $char;
                }
                $i++;
                $char = substr($line, $i, 1);
              } //end of while
               $data[]=$value;
              //print_r($data);
              //$routcode=trim($data[0]);
              $route_name	  =trim($data[0]);
              $emp_code_name   =trim($data[1]);




              $emp_code=$CUTDB->table('employee_master')
                              ->select('emp_code')
                              ->where('emp_name', '=' ,addslashes($emp_code_name))
                              ->first();



              $countroutechk=$CUTDB->table('route_master')
                              ->where('route_name', '=' ,addslashes($route_name))
                              ->where('emp_code','=',$emp_code)
                              ->first();
              if(count($countroutechk)<1)
              {
                $routecode=$CUTDB->table('route_master')
                                 ->where('route_code','NOT LIKE','N%')
                                 ->max('route_code');
                if($routecode!='')
                {
                  $routecode1=substr($routecode,3);
                  $newcode=$routecode1+1;
                  $routecode='RT/'.$newcode;
                }
                else {
                  $routecode='RT/1';
                }

                $CUTDB->table('route_master')->insert(array(
                    'route_code' => $max_route_code,
                    'route_name' => $route_name,
                    'emp_code' =>$emp_code
                ));
              }
            }
             $rec_count++;
          }
      }
    }

    /**
     * [routeplanreport This function return the employee and date wise route details]
     * @return [type] [description]
     */
    public function routeplanreports(){

      $dbname =$this->databasename();
      $emplist =Route::getEmplist($dbname);
      $reoprtdata='';
      return view('route.routeplanreport',compact('emplist','reoprtdata'));

    }

    public function showrouteplanreports(){
      $dbname =$this->databasename();

      $empid=Input::get('emp_list');
      $newempid=implode("','",$empid);
      $startdate=date('Y-m-d',strtotime(Input::get('start_date')));
      $enddate=date('Y-m-d',strtotime(Input::get('end_date')));
      $emplist =Route::getEmplist($dbname);
      $reoprtdata=Route::getRouteReport($dbname,$newempid,$startdate,$enddate);
      return view('route.routeplanreport',compact('emplist','reoprtdata'));
    }

    public function routeplanapproval(){

      $dbname =$this->databasename();
      $emplist =Route::getEmplist($dbname);
      $reoprtdatas='';
      return view('route.routeplanapproval',compact('emplist','reoprtdatas'));

    }

    public function showrouteplanapproval(){
      $dbname =$this->databasename();

      $empid=Input::get('emp_list');
      $emplist =Route::getEmplist($dbname);
      $reoprtdatas=Route::getRoutePlane($dbname,$empid);
      $emphierarchyroute=Route::getEmpHryWiseRoute($dbname,$empid);

      //echo "<pre>";print_r($reoprtdatas);exit;
      return view('route.routeplanapproval',compact('emplist','reoprtdatas'));
    }

    public function showvisitanalysis(){

       $dbname =$this->databasename();
       $emplists=Route::getNormalEmpList($dbname);
       $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
       $satrdate='';
       $enddate='';
       /*foreach ($emplists as $key => $emplist) {

           $assignbeat=Route::getAssignbeatperemp($dbname,$emplist->emp_code,$satrdate,$enddate);

       }*/

       return view('route.visitanalysis',compact('avialblefilter'));

    }
    public function show_visit_analysisreport(){
      $dbname =$this->databasename();
      $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
      $satrdate='';
      $enddate='';
      $empids=Input::get('employee');
      $visitanalysisreport=Route::getVisitAnalysisReport($dbname,$empids);

      return $visitanalysisreport;
    }

    public function dailyactivityanalysis(){
      $dailyactivityanalysislists='';
      $dbname =$this->databasename();
      $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
      return view('route.dailyactivityanalysis',compact('dailyactivityanalysislists','avialblefilter'));
    }

    public function showdailyactivityanalysis(Request $request){

      $dbname =$this->databasename();
      $satrdate=date('Y-m-d',strtotime($request->startdate));
      $enddate=date('Y-m-d',strtotime($request->enddate));
      $empid=$request->employee;
      $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
      $dailyactivityanalysislists=Route::getDailyActivityAnalysis($dbname,$satrdate,$enddate,$empid);

      return $dailyactivityanalysislists;
      //return view('route.dailyactivityanalysis',compact('dailyactivityanalysislists','avialblefilter'));
    }
    public function montlyactivityanalysis(){
      $dbname =$this->databasename();
      $montlyactivityanalysis='';
      $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
      $emplist =Route::getEmplist($dbname);
      $monthlist=Route::getMonthlist();

      return view('route.montlyactivityanalysis',compact('emplist','monthlist','montlyactivityanalysis','avialblefilter'));
    }
    public function showmontlyactivityanalysis(Request $request){
      $dbname =$this->databasename();
      $empids=$request->employee;
      $month_select=$request->month_select;
      //$emplist =Route::getEmplist($dbname);
      //$monthlist=Route::getMonthlist();
      $montlyactivityanalysis=Route::getMonthlyAnalysisreport($dbname,$empids,$month_select);

      //return view('route.montlyactivityanalysis',compact('emplist','monthlist','montlyactivityanalysis'));
      return $montlyactivityanalysis;
    }
    public function saleregister(){
      $dbname =$this->databasename();
      $saleregister='';
      $statelists =Route::getStatelist($dbname);

      return view('route.saleregister',compact('statelists','saleregister'));
    }
    public function showsaleregister(){
      $dbname =$this->databasename();
      $state=Input::get('state');
      $saletype=Input::get('cust_type');
      $satrdate=date('Y-m-d',strtotime(Input::get('start_date')));
      $enddate=date('Y-m-d',strtotime(Input::get('end_date')));
      $statelists =Route::getStatelist($dbname);
      $saleregister=Route::getSalergister($dbname,$state,$saletype,$satrdate,$enddate);
      return view('route.saleregister',compact('statelists','saleregister'));
    }
    public function pjpexport(Request $request)
    {
        $dbname =$this->databasename();

        $empid=$request->emp_list;
        $newempid=implode("','",$empid);
        $startdate=date('Y-m-d',strtotime($request->start_date));
        $enddate=date('Y-m-d',strtotime($request->end_date));
        $reoprtdata=Route::getRouteReport($dbname,$newempid,$startdate,$enddate);

        $data = "Employee Name,Date,Route Name"."\n";

        foreach($reoprtdata as $reoprt) {
                $date=date('d-m-Y',strtotime($reoprt->visit_date));
                $data .=$reoprt->emp_name.",".$date.",".$reoprt->route_name."\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=pjpreport.csv');
        echo $data;
        exit();


    }

    public function getMonthlyAnalysisReportCsv(Request $request){
      $dbname =$this->databasename();
      $CUTDB = Route::dydb($dbname);
      $month = $request->month_select;
      $month_year = explode("-",$month);
      $monthvalue = date('m',strtotime($month_year[0]));
      $year = $month_year[1];
      $empids=$request->employee;
      $data="SL No,Emp Code,Emp Name,Designation,State,Days Present,Total Calls,Productive Calls,Productive Calls %,LPPC,Value"."\n";
      foreach ($empids as $empid){
          $emp_code = $empid;
          $emp_hierarchy=Commonfunctions::return_employee_hierarchy($dbname,$emp_code);
          $emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
          $order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
          $order_check_condition = " AND SUBSTRING(OH.order_no,2,5)=EM.emp_code ";
          $employee_select_condition = " AND LO.emp_code = '".$emp_code."' ";

          $current_date = date('Y-m-d');
          $datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
          $primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";
          $count = 1;

          $sql_total_calls = $CUTDB->select("SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present FROM location LO WHERE SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."'".$employee_select_condition." GROUP BY LO.emp_code");
          if(count($sql_total_calls)>0){
             foreach($sql_total_calls as $key=>$row_total_calls){
                $sql_empname = $CUTDB->table('employee_master')
                                    ->select('emp_name','emp_code','state','designation')
                                    ->where('emp_code','=',$row_total_calls->emp_code)
                                    ->first();
                 if(count($sql_empname)>0){
                     $emp_name = (($sql_empname->emp_name)?$sql_empname->emp_name:'');
                     $emp_code = (($sql_empname->emp_code)?$sql_empname->emp_code:'');
                     $state = (($sql_empname->state)?$sql_empname->state:'');
                     $designation = (($sql_empname->designation)?$sql_empname->designation:'');
                     $days_present = $row_total_calls->days_present;

                     $sql_prod_call = $CUTDB->select("SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls,COUNT(DISTINCT product_code) AS sku_count, SUM(amount) as amt FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'O%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'")[0];
                     $prod_call = (($sql_prod_call->productive_calls)?$sql_prod_call->productive_calls:'0');

                     $sql_nonprod_call = $CUTDB->select("SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS non_productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'NO%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'")[0];
                     $non_prod_call = (($sql_nonprod_call->non_productive_calls)?$sql_nonprod_call->non_productive_calls:'0');

                     $total_calls = $prod_call + $non_prod_call;
                        if($prod_call>0){
                          $prod_call_percentage = ($prod_call/$total_calls)*100;
                        }
                        else{
                          $prod_call_percentage ='0';
                        }
                        $lppc_count = $sql_prod_call->sku_count;
                        $tot_amount = str_replace( ',', '',$sql_prod_call->amt);
                        if($lppc_count>0){
                         $lppc = number_format($lppc_count/$prod_call,2);
                        }
                        else{
                           $lppc ='0';
                        }
                        $data .=$count.",".$emp_code.",".$emp_name.",".$designation.",".$state.",".$days_present.",".$total_calls.",".$prod_call.",".number_format($prod_call_percentage,2).",".$lppc.",".number_format($tot_amount,2)."\n";
                        $count++;
                  }
                  else{
                    continue;
                  }
                }
              }
            }
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=MonthlyAnalysisReport.csv');
            echo $data;
            exit();
     }
     public function getDailyActivityAnalysisCsv(Request $request){
       $start_date=$request->start_date;
       $end_date=$request->end_date;
       $emp_id=$request->employee;
       $empids=join("','",$emp_id);
       $dbname =$this->databasename();
       $CUTDB = Route::dydb($dbname);
       $total_productive_call='';
       $total_non_productive_call='';
       $total_primary_quantity='';
       $total_secondary_quantity='';

       if($start_date!= '' && $end_date!= ''){
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
       else{
          $start_date = date('Y-m-d');
          $table_columnname = 'Attendance';
          $condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."'";
          $condition1="AND EM.emp_code IN ('$empids')";
          $attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
          $primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
       }
       $sales_type_array = array();
       $sql_get_custtype = $CUTDB->select("SELECT DISTINCT cust_type as sales_type FROM customer_master");
       foreach($sql_get_custtype as $key=>$val){
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
        $table_column = "Primary,Secondary";
        $colspan_two = '2';
       }
       else if($retailer == 'true'){
        $colspan = '6';
        $table_column = "Secondary";
       }
       else if($distributor == 'true'){
        $colspan = '6';
        $table_column = "Primary";
       }

       $count = 1;
       $sql_get_empdetails = $CUTDB->select("SELECT LO.emp_code, EM.emp_name,".$attendance_condition."FROM location LO, employee_master EM WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code AND EM.acedns != 'N'".$condition.$condition1.$group_by."ORDER BY EM.emp_name ASC");

       if(count($sql_get_empdetails)>0){
         $data="SI,Employee Name,".$table_columnname.",Productive Calls,Non Productive Calls,Total"."\n";
         $data.= "$table_column";

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
            $table_column_data = "number_format($primary_quantity,2),number_format($secondary_quantity,2)";
          }
          else if($retailer == 'true'){
            $table_column_data = "number_format($secondary_quantity,2)";
          }
          else if($distributor == 'true'){
            $table_column_data = "number_format($primary_quantity,2)";
          }

          $data.="$count,$emp_name,$attendance,$productive_call,$non_productive_call,$table_column_data";

          $count++;
        }
        if($retailer == 'true' && $distributor == 'true'){
          $table_column_data = "number_format($total_primary_quantity,2),number_format($total_secondary_quantity,2)";
        }
        else if($retailer == 'true'){
          $table_column_data = "number_format($total_secondary_quantity,2)";
        }
        else if($distributor == 'true'){
          $table_column_data = "number_format($total_primary_quantity,2)";
        }

        $data.="Total,$total_productive_call,$total_non_productive_call,$table_column_data";
       }
       header('Content-Type: application/csv');
       header('Content-Disposition: attachment; filename=DailyActivityAnalysisReport.csv');
       echo $data;
       exit();

   }

   public function csvdownloadsaleregister(Request $request){
     $dbname =$this->databasename();
     $CUTDB = Route::dydb($dbname);
     $table_data='';
     if(strtoupper(Session::get('USERNAME')) == "ADMIN"){
     	$emp_hierarchy = "";
     	$emp_hierarchy_condition = "";
     }
     else{
     	$emp_hierarchy = Commonfunctions::return_employee_hierarchy($_SESSION['admin_login']);
     	$emp_hierarchy_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
     }
     $start_date=date('Ymd',strtotime($request->start_date));
     $end_date=date('Ymd',strtotime($request->end_date));
     $cust_type = $request->cust_type;
     $state = $request->state;
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
     	$table_header = "Prod Desc";
     	$colspan = '5';
     }
     if(Session::get('nooffilter') == 2){
     	$table_header = "Prod Group"."\t"."Prod Desc";
     	$colspan = '6';
     }
     if(Session::get('nooffilter') == 3){
     	$table_header = "Prod Group"."\t"."Prod Sub Group"."\t"."Prod Desc";
     	$colspan = '7';
     }
     if(Session::get('nooffilter') == 4){
     	$table_header = "Prod Group"."\t"."Prod Sub Group"."\t"."Brand"."\t"."Prod Desc";
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

       $header = "Date"."\t"."Distributor"."\t"."Retailer"."\t"."Employee code"."\t"."Employee"."\t"."State"."\t".$table_header."\t"."Quantity"."\t"."Sale Rate"."\t"."Amount";


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

         			if($cust_type == 'primary'){

         				$rds_name = $customer_name;
         			}
         			else if($cust_type == 'secondary'){
                $sql_rds_tag = $CUTDB->table('customer_master')
                                     ->select('customer_name')
                                     ->where('customer_code','=',$rds_tag)
                                     ->first();
                  if(count($sql_rds_tag)>0){
                      $rds_name = $sql_rds_tag->customer_name;
                  }
                  else{
                      $rds_name ='';
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
                $sqlmrp=$CUTDB->table('mrp')
                            ->select('mrp')
                            ->where('mrp_code', '=', $mrp_code)
                            ->first();
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
         				$table_data_prod = $prod_desc;
         			}
         			else if(Session::get('nooffilter') == '2'){
         				$prod_group_name = Route::productdetails($dbname,$product_group_code,'product_group_master','product_group_code','product_group_name');
         				$table_data_prod = $prod_group_name."\t".$prod_desc;
         			}
         			else if(Session::get('nooffilter') == '3'){
         				$prod_group_name = Route::productdetails($dbname,$product_group_code,'product_group_master','product_group_code','product_group_name');
         				$prod_sub_group_name = Route::productdetails($dbname,$product_sub_group_code,'product_sub_group_master','product_sub_group_code','product_sub_group_name');
         				$table_data_prod = $prod_group_name."\t".$prod_sub_group_name."\t".$prod_desc;
         			}
         			else if(Session::get('nooffilter') == '4'){
         				$prod_group_name = Route::productdetails($dbname,$product_group_code,'product_group_master','product_group_code','product_group_name');
         				$prod_sub_group_name = Route::productdetails($dbname,$product_sub_group_code,'product_sub_group_master','product_sub_group_code','product_sub_group_name');
         				$prod_brand_name = Route::productdetails($dbname,$product_brand_code,'product_brand_master','product_brand_code','product_brand_name');
         				$table_data_prod = $prod_group_name."\t".$prod_sub_group_name."\t".$prod_brand_name."\t".$prod_desc;
         			}

         			$table_data .= $order_date."\t";

         			if($cust_type == 'secondary'){
         				$table_data .= $rds_name."\t".$customer_name."\t";
         			}
         			else if($cust_type == 'primary'){
         				$table_data .= $rds_name."\t"."--"."\t";
         			}
              $table_data .= $emp_code."\t".$emp_name."\t".$state."\t".$table_data_prod."\t".$qty."\t".$sale_rate."\t".$amount."\n";
        			$count++;
         		}
         	  }

         }
         header("Content-type: application/octet-stream");
         header("Content-Disposition: attachment; filename=Sale_Register.xls");
         header("Pragma: no-cache");
         header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header.
         echo ucwords($header)."\n".$table_data;exit;
     }

   }



}
