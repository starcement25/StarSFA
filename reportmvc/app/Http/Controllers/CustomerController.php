<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Session;
use App\Helpers\Customer;
use App\Helpers\Commonfunctions;


class CustomerController extends Controller
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
        $customerlists=Customer::getAllCustomerList($sdbname);
        return view('customer.index',compact('customerlists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $employee_lists=Customer::getEmplist($sdbname);
        $route_lists=Customer::getRoutelist($sdbname);
        $distributor_lists=Customer::getDistributorlist($sdbname);
        $branch_list=Customer::getBranchlist($sdbname);
        return view('customer.create',compact('employee_lists','route_lists','distributor_lists','branch_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Customer::dydb($dbname);

      $dns_customer_code=(($request->input('customer_code'))?$request->input('customer_code'):'');
      $customer_name=(($request->input('customer_name'))?$request->input('customer_name'):'');
      $address=(($request->input('address'))?$request->input('address'):'');
      $pin=(($request->input('pin'))?$request->input('pin'):'');
      $phone_no=(($request->input('phone_no'))?$request->input('phone_no'):'');
      $landline_no=(($request->input('landline_no'))?$request->input('landline_no'):'');
      $route_code=(($request->input('route_code'))?$request->input('route_code'):'');
      $emp_code=(($request->input('emp_code'))?$request->input('emp_code'):'');
      $current_balance=(($request->input('current_balance'))?$request->input('current_balance'):'');
      $credit_limit=(($request->input('credit_limit'))?$request->input('credit_limit'):'');
      $credit_days=(($request->input('credit_days'))?$request->input('credit_days'):'');
      $acedns='Y';
      $black_list=(($request->input('black_list'))?$request->input('black_list'):'');
      $TD=(($request->input('TD'))?$request->input('TD'):'');
      $cust_type=(($request->input('cust_type'))?$request->input('cust_type'):'');
      $rds_tag=(($request->input('rds_tag'))?$request->input('rds_tag'):'');
      $sauda_validity_period=(($request->input('sauda_validity_period'))?$request->input('sauda_validity_period'):'');
      $owner_name=(($request->input('owner_name'))?$request->input('owner_name'):'');
      $owner_phone=(($request->input('owner_phone'))?$request->input('owner_phone'):'');
      $cust_class=(($request->input('cust_class'))?$request->input('cust_class'):'');
      $weekly_closing_day=(($request->input('weekly_closing_day'))?:'');
      $TIN=(($request->input('TIN'))?$request->input('TIN'):'');
      $PAN=(($request->input('PAN'))?:'');
      $district=(($request->input('district'))?$request->input('district'):'');
      $branch_code=(($request->input('branch_code'))?$request->input('branch_code'):'');
      $minimum_stock=(($request->input('minimum_stock'))?$request->input('minimum_stock'):'');
      $bank_name=(($request->input('bank_name'))?$request->input('bank_name'):'');
      $bank_account_number=(($request->input('bank_account_number'))?$request->input('bank_account_number'):'');
      $email=(($request->input('email'))?$request->input('email'):'');
      $visit_day=(($request->input('visit_day'))?$request->input('visit_day'):'');
      $state=(($request->input('state'))?$request->input('state'):'');
      $downloadtime=date('Y-m-d H:i:s');
      $custcode=$CUTDB->table('customer_master')
                      ->where('customer_code','NOT LIKE','N%')
                      ->max('customer_code');

      if($custcode!='')
      {
        $custcode++;
      }
      else {
        $custcode='C/0000001';
      }

        if(Session::get('iscodeneed')=='yes')
        {

           $rules1[] = array(
              'customer_code' => 'required',
           );
        }

        $rules1[] = array(
          'customer_name' => 'required',
          'phone_no' => 'required',
          'cust_type' => 'required',
          'route_code' => 'required',
          'branch_code' => 'required',
      );
      $rules = array();
      foreach($rules1 as $arr) {
           if(is_array($arr)) {
               $rules = array_merge($rules, $arr);
           }
      }
      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails())
      {
           return Redirect::to('customer/create')->withErrors($validator);
      }
      else{
      $CUTDB->table('customer_master')->insert(array(
          'customer_code' => $custcode,
          'dns_customer_code' => $dns_customer_code,
          'customer_name'=>$customer_name,
          'address' => $address,
          'pin'=>$pin,
          'phone_no'=>$phone_no,
          'landline_no' => $landline_no,
          'route_code' => $route_code,
          'credit_limit' => $credit_limit,
          'credit_days' => $credit_days,
          'acedns' => $acedns,
          'TD' => $TD,
          'cust_type' => $cust_type,
          'rds_tag' => $rds_tag,
          'owner_name' => $owner_name,
          'owner_phone' => $owner_phone,
          'weekly_closing_day' => $weekly_closing_day,
          'TIN' => $TIN,
          'PAN' => $PAN,
          'district' => $district,
          'branch_code' => $branch_code,
          'minimum_stock' => $minimum_stock,
          'bank_name' => $bank_name,
          'bank_account_number' => $bank_account_number,
          'email' => $email,
          'visit_day' => $visit_day,
          'state_code'=>$state,
          'download_time' =>$downloadtime
      ));
      return redirect('/customer')->with('message', 'Success!');
     }
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
      $tablename="customer_master";
      $fieldname="customer_code";
      $newparame=substr($id,0,1);
      $newparame1=substr($id,1);
      if($newparame=='C')
        $parameter=$newparame.'/'.$newparame1;
      else
        $parameter=$id;

      $employee_lists=Customer::getEmplist($sdbname);
      $route_lists=Customer::getRoutelist($sdbname);
      $distributor_lists=Customer::getDistributorlist($sdbname);
      $branch_list=Customer::getBranchlist($sdbname);
      $custdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      return view('customer.view',compact('employee_lists','route_lists','distributor_lists','branch_list','custdetails'));
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
      $tablename="customer_master";
      $fieldname="customer_code";
      $newparame=substr($id,0,1);
      $newparame1=substr($id,1);
      if($newparame=='C')
        $parameter=$newparame.'/'.$newparame1;
      else
        $parameter=$id;

      $employee_lists=Customer::getEmplist($sdbname);
      $route_lists=Customer::getRoutelist($sdbname);
      $distributor_lists=Customer::getDistributorlist($sdbname);
      $branch_list=Customer::getBranchlist($sdbname);
      $custdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      return view('customer.edit',compact('employee_lists','route_lists','distributor_lists','branch_list','custdetails'));
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
      $CUTDB = Customer::dydb($dbname);

      $newparame=substr($id,0,1);
      $newparame1=substr($id,1);
      if($newparame=='C')
        $parameter=$newparame.'/'.$newparame1;
      else
        $parameter=$id;

        if(Session::get('iscodeneed')=='yes')
        {

           $rules1[] = array(
              'customer_code' => 'required',
           );
        }

        $rules1[] = array(
          'customer_name' => 'required',
          'phone_no' => 'required',
          'cust_type' => 'required',
          'route_code' => 'required',
          'branch_code' => 'required',
      );
      $rules = array();
      foreach($rules1 as $arr) {
           if(is_array($arr)) {
               $rules = array_merge($rules, $arr);
           }
      }
      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails())
      {
           return Redirect::to('customer/'.$id.'/edit')->withErrors($validator);
      }
      else{
      $dns_customer_code=((Input::get('customer_code'))?Input::get('customer_code'):'');
      $customer_name=((Input::get('customer_name'))?Input::get('customer_name'):'');
      $address=((Input::get('address'))?Input::get('address'):'');
      $pin=((Input::get('pin'))?Input::get('pin'):'');
      $phone_no=((Input::get('phone_no'))?Input::get('phone_no'):'');
      $landline_no=((Input::get('landline_no'))?Input::get('landline_no'):'');
      $route_code=((Input::get('route_code'))?Input::get('route_code'):'');
      $emp_code=((Input::get('emp_code'))?Input::get('emp_code'):'');
      $current_balance=((Input::get('current_balance'))?Input::get('current_balance'):'');
      $credit_limit=((Input::get('credit_limit'))?Input::get('credit_limit'):'');
      $credit_days=((Input::get('credit_days'))?Input::get('credit_days'):'');
      $black_list=((Input::get('black_list'))?Input::get('black_list'):'');
      $TD=((Input::get('TD'))?Input::get('TD'):'');
      $cust_type=((Input::get('cust_type'))?Input::get('cust_type'):'');
      $rds_tag=((Input::get('rds_tag'))?Input::get('rds_tag'):'');
      $sauda_validity_period=((Input::get('sauda_validity_period'))?Input::get('sauda_validity_period'):'');
      $owner_name=((Input::get('owner_name'))?Input::get('owner_name'):'');
      $owner_phone=((Input::get('owner_phone'))?Input::get('owner_phone'):'');
      $weekly_closing_day=((Input::get('weekly_closing_day'))?Input::get('weekly_closing_day'):'');
      $TIN=((Input::get('TIN'))?Input::get('TIN'):'');
      $PAN=((Input::get('PAN'))?Input::get('PAN'):'');
      $district=Input::get('district');
      $branch_code=((Input::get('branch_code'))?Input::get('branch_code'):'');
      $minimum_stock=((Input::get('minimum_stock'))?Input::get('minimum_stock'):'');
      $bank_name=((Input::get('bank_name'))?Input::get('bank_name'):'');
      $bank_account_number=((Input::get('bank_account_number'))?Input::get('bank_account_number'):'');
      $email=((Input::get('email'))?Input::get('email'):'');
      $visit_day=((Input::get('visit_day'))?Input::get('visit_day'):'');
      $state=((Input::get('state'))?Input::get('state'):'');


        $CUTDB->table('customer_master')
        ->where('customer_code', $parameter)
        ->limit(1)
        ->update(array('dns_customer_code' => $dns_customer_code,
        'customer_name'=>$customer_name,
        'address' => $address,
        'pin'=>$pin,
        'phone_no'=>$phone_no,
        'landline_no' => $landline_no,
        'route_code' => $route_code,
        'credit_limit' => $credit_limit,
        'credit_days' => $credit_days,
        'TD' => $TD,
        'cust_type' => $cust_type,
        'rds_tag' => $rds_tag,
        'owner_name' => $owner_name,
        'owner_phone' => $owner_phone,
        'weekly_closing_day' => $weekly_closing_day,
        'TIN' => $TIN,
        'PAN' => $PAN,
        'district' => $district,
        'branch_code' => $branch_code,
        'minimum_stock' => $minimum_stock,
        'bank_name' => $bank_name,
        'bank_account_number' => $bank_account_number,
        'email' => $email,
        'visit_day' => $visit_day,
        'state_code' => $state
         ));

        return redirect('/customer')->with('message', 'Success!');
      }
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

      public function customerUploadFile(Request $request)
      {
                        $dbname =$this->databasename();
                        $CUTDB = Branch::dydb($dbname);

                        $file = $request->file('customer_csv_file');

                        //Display File Name
                        $nickname=substr(Session::get('DBNAME'),7);
                        $name=$nickname.'_'.$file->getClientOriginalName();
                        $ext=$file->getClientOriginalExtension();

                        if($ext=='csv')
                        {

                              $destinationPath = 'uploads/csv';
                              $file->move($destinationPath,$name);
                              $rec_count = 0;
                              $ins_count = 0;
                              $err = "";

                              $lines = file($filename);
                              $countroute=0;

                              if(Session::get('iserproute')=='no' && $nickname!='ABDOS')
                              {
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
                                              $value .=$char;
                                              }
                                              $i++;
                                              $char = substr($line, $i, 1);
                                            } //end of while
                                             $data[]=$value;
                                            //print_r($data);

                                            $csv_row_count=$rec_count+1;
                                            $dns_customer_code =trim($data[0]);
                                            $customer_name    =trim($data[1]);
                                            $phone_no        =trim($data[2]);
                                            $dns_route_code      =trim($data[3]);
                                            $route_name      =trim($data[4]);
                                            $emp_code_name        =trim($data[5]);
                                            if($folderName=='MAITHAN')
                                            {
                                                $sqlemparray=$CUTDB->table('employee_master')
                                                                     ->select('emp_name')
                                                                     ->where('HQ', '=' ,$route_name)
                                                                     ->get();

                                                $emp_code_name_array=array();
                                                foreach ($sqlemparray as $key => $value) {
                                                  array_push($emp_code_name_array,$value->emp_name);
                                                }
                                            }
                                            else
                                            {
                                               if(strpos($emp_code_name,';')!=false)
                                               {
                                                $emp_code_name=str_replace(';',',',$emp_code_name);
                                               }
                                               $emp_code_name_array=explode(',',$emp_code_name);
                                            }
                                            //print_r($emp_code_name_array);

                                            if(Session::get('iscodeneed') == 'yes')
                                            {
                                              foreach($emp_code_name_array as $emp_code_name_values)
                                              {
                                                $sql_check_dns_code = $CUTDB->table('employee_master')
                                                                            ->select('emp_name')
                                                                            ->where('dns_emp_code', '=' ,$emp_code_name_values)
                                                                            ->first();

                                                if(count($sql_check_dns_code) == 0)
                                                {
                                                  echo "Please provide proper DNS Employee Code at row ".($csv_row_count+1);
                                                  die;
                                                }
                                              }
                                              if($route_name != '' && $dns_route_code == '')
                                              {
                                                echo "Please provide dns route code at row ".($csv_row_count+1);
                                              }
                                            }
                                            else
                                            {
                                              foreach($emp_code_name_array as $emp_code_name_values)
                                              {
                                                $sql_check_emp = $CUTDB->table('employee_master')
                                                                            ->select('emp_name')
                                                                            ->where('emp_name', '=' ,$emp_code_name_values)
                                                                            ->first();

                                                if(count($sql_check_emp) == 0)
                                                {
                                                  echo "Please provide proper employee at row ".($csv_row_count+1);
                                                  die;
                                                }
                                              }
                                            }
                                            //For VIPL employee only
                                            /*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_code)."'";
                                            $rsempnamechk=mysql_query($sqlempnamechk);
                                            $rowempnamechk=mysql_fetch_array($rsempnamechk);
                                            $emp_code=$rowempnamechk['emp_code'];*/
                                            $acedns          =trim($data[6]);
                                            if($acedns =='')
                                            {
                                              echo "Please provide value for Acedns at row ".($csv_row_count+1);
                                              die;
                                            }

                                            $credit_limit    =trim($data[7]);
                                            $credit_days     =trim($data[8]);
                                            $current_balance =trim($data[9]);
                                            $black_list      =trim($data[10]);
                                            if($black_list=='')
                                            {
                                              echo "Please provide value for Blacklist at row ".($csv_row_count+1);
                                              die;
                                            }

                                            $TD                =trim($data[11]);
                                            $branch_code_name =trim($data[12]);
                                            $customer_type   =trim($data[13]);
                                            $rds_tag   =trim($data[14]);
                                            $sauda_validity_period  =trim($data[15]);
                                            $address  =trim($data[16]);
                                            $landline_no  =trim($data[17]);
                                            $owner_name  =trim($data[18]);
                                            $owner_phone  =trim($data[19]);
                                            $cust_class  =trim($data[20]);
                                            $weekly_closing_day  =trim($data[21]);
                                            $coverage_type  =trim($data[22]);
                                            $TIN  =trim($data[23]);
                                            $PAN  =trim($data[24]);
                                            $district  =trim($data[25]);
                                            $minimum_stock  =trim($data[26]);
                                            $bank_name  =trim($data[27]);
                                            $bank_account_number  =trim($data[28]);
                                            $email  =trim($data[29]);
                                            $visit_day  =trim($data[30]);

                                            foreach($emp_code_name_array as $emp_code_name_value_next)
                                            {
                                              if(Session::get('iscodeneed')=='yes'){
                                                $sqlempcode=$CUTDB->table('employee_master')
                                                                            ->select('emp_code','branch_code')
                                                                            ->where('dns_emp_code', '=' ,$emp_code_name_value_next)
                                                                            ->first();

                                                $emp_code=$sqlempcode->emp_code;
                                                $branch_code=$sqlempcode->branch_code;
                                              }
                                              else
                                              {
                                                $sqlempcode=$CUTDB->table('employee_master')
                                                                            ->select('emp_code','branch_code')
                                                                            ->where('emp_name', '=' ,$emp_code_name_value_next)
                                                                            ->first();
                                                $emp_code=$sqlempcode->emp_code;
                                                //exit();
                                                $branch_code=$sqlempcode->branch_code;
                                              }
                                             //$emp_code=$emp_code_name;
                                              if(Session::get('iscodeneed')=='yes'){
                                                $sqlrdscode=$CUTDB->table('customer_master')
                                                                            ->select('customer_code')
                                                                            ->where('dns_customer_code', '=' ,$rds_tag)
                                                                            ->first();


                                              }
                                              else
                                              {
                                                $sqlrdscode=$CUTDB->table('customer_master')
                                                                                  ->select('customer_code')
                                                                                  ->where('customer_name', '=' ,$rds_tag)
                                                                                  ->where('emp_code', '=' ,$emp_code)
                                                                                  ->first();
                                              }

                                              $rds_code=$sqlrdscode->customer_code;

                                              if(Session::get('iscodeneed')=='yes'){
                                                $sqlroutechk=$CUTDB->table('route_master')
                                                                                  ->where('dns_route_code', '=' ,$dns_route_code)
                                                                                  ->where('emp_code', '=' ,$emp_code)
                                                                                  ->first();



                                              }
                                              else
                                              {
                                                $sqlroutechk=$CUTDB->table('route_master')
                                                                                  ->where('route_name', '=' ,$route_name)
                                                                                  ->where('emp_code', '=' ,$emp_code)
                                                                                  ->first();


                                              }

                                              if(count($sqlroutechk)<1 && $route_name!='')
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

                                                $max_route_code=$routecode;


                                                $CUTDB->table('route_master')->insert(array(
                                                    'route_code' => $max_route_code,
                                                    'dns_route_code' => $dns_route_code,
                                                    'route_name' => $route_name,
                                                    'emp_code'=>$emp_code,
                                                    'download_time' =>$downloadtime
                                                ));
                                                $route_code=$max_route_code;
                                              }
                                              else
                                              {

                                                $route_code=$sqlroutechk->route_code;
                                              }

                                              if(Session::get('iscodeneed')=='yes'){
                                                 $sqlcustomernamechk=$CUTDB->table('customer_master')
                                                                                   ->where('dns_customer_code', '=' ,$dns_customer_code)
                                                                                   ->where('emp_code', '=' ,$emp_code)
                                                                                   ->where('route_code', '=' ,$route_code)
                                                                                   ->first();


                                              }
                                              else
                                              {
                                              $sqlcustomernamechk=$CUTDB->table('customer_master')
                                                                                ->where('customer_name', '=' ,$customer_name)
                                                                                ->where('emp_code', '=' ,$emp_code)
                                                                                ->where('route_code', '=' ,$route_code)
                                                                                ->first();

                                              }


                                            if(count($sqlcustomernamechk)<1)
                                            {
                                              $custcode=$CUTDB->table('customer_master')
                                                              ->where('customer_code','NOT LIKE','N%')
                                                              ->max('customer_code');

                                              if($custcode!='')
                                              {
                                                $custcode++;
                                              }
                                              else {
                                                $custcode='C/0000001';
                                              }
                                              $downloadtime=date('Y-m-d H:i:s');
                                              $CUTDB->table('customer_master')->insert(array(
                                                  'customer_code' => $max_customer_code,
                                                  'dns_customer_code' => $dns_customer_code,
                                                  'customer_name'=>addslashes($customer_name),
                                                  'address' => $address,
                                                  'pin'=>$pin,
                                                  'phone_no'=>$phone_no,
                                                  'emp_code'=>$emp_code,
                                                  'current_balance'=>$current_balance,
                                                  'landline_no' => $landline_no,
                                                  'route_code' => $route_code,
                                                  'credit_limit' => $credit_limit,
                                                  'credit_days' => $credit_days,
                                                  'acedns' => $acedns,
                                                  'black_list'=>$black_list,
                                                  'TD' => $TD,
                                                  'cust_type' => $customer_type,
                                                  'rds_tag' => $rds_code,
                                                  'sauda_validity_period'=>$sauda_validity_period,
                                                  'owner_name' => $owner_name,
                                                  'owner_phone' => $owner_phone,
                                                  'cust_class'=>$cust_class,
                                                  'weekly_closing_day' => $weekly_closing_day,
                                                  'TIN' => $TIN,
                                                  'PAN' => $PAN,
                                                  'district' => $district,
                                                  'branch_code' => addslashes($branch_code),
                                                  'minimum_stock' => $minimum_stock,
                                                  'bank_name' => $bank_name,
                                                  'bank_account_number' => $bank_account_number,
                                                  'email' => $email,
                                                  'coverage_type'=>$coverage_type,
                                                  'visit_day' => $visit_day,
                                                  'state_code'=>$state,
                                                  'download_time' =>$downloadtime
                                              ));


                                              $customer_code=$max_customer_code;
                                            }
                                            else
                                            {

                                              $customer_code_db=$sqlcustomernamechk->customer_code;
                                              $route_code_db=$sqlcustomernamechk->route_code;
                                              $emp_code_db=$sqlcustomernamechk->emp_code;
                                              $current_balance_db=$sqlcustomernamechk->current_balance;
                                              $credit_limit_db=$sqlcustomernamechk->credit_limit;
                                              $credit_days_db=$sqlcustomernamechk->credit_days;
                                              $acedns_db=$sqlcustomernamechk->acedns;
                                              $black_list_db=$sqlcustomernamechk->black_list;
                                              $TD_db=$sqlcustomernamechk->TD;
                                              $customer_type_db=$sqlcustomernamechk->cust_type;
                                              $rds_tag_db=$sqlcustomernamechk->rds_tag;
                                              $branch_code_db=$sqlcustomernamechk->branch_code;
                                              $sauda_validity_period_db=$sqlcustomernamechk->sauda_validity_period;
                                              $customer_name_db=$sqlcustomernamechk->customer_name;
                                              $dns_customer_code_db=$sqlcustomernamechk->dns_customer_code;
                                              $address_db=$sqlcustomernamechk->address;
                                              $owner_name_db=$sqlcustomernamechk->owner_name;
                                              $owner_phone_db=$sqlcustomernamechk->owner_phone;
                                              $cust_class_db=$sqlcustomernamechk->cust_class;
                                              $weekly_closing_day_db=$sqlcustomernamechk->weekly_closing_day;
                                              $TIN_db=$sqlcustomernamechk->TIN;
                                              $PAN_db=$sqlcustomernamechk->PAN;
                                              $district_db=$sqlcustomernamechk->district;
                                              $landline_no_db=$sqlcustomernamechk->landline_no;
                                              $minimum_stock_db=$sqlcustomernamechk->minimum_stock;
                                              $bank_name_db=$sqlcustomernamechk->bank_name;
                                              $bank_account_number_db=$sqlcustomernamechk->bank_account_number;
                                              $email_db=$sqlcustomernamechk->email;
                                              $visit_day_db=$sqlcustomernamechk->visit_day;
                                              $coverage_type_db  =$sqlcustomernamechk->coverage_type;

                                              if(Session::get('iscodeneed')=='yes'){
                                                $update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";
                                              }
                                              else
                                              {
                                                $update_condition=" customer_name='".addslashes($customer_name)."'";
                                              }
                                              if($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD || $customer_type_db!=$customer_type || $rds_tag_db!=$rds_code
                                              || $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days
                                              || $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no || $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class || $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district || $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name || $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day || $coverage_type_db!=$coverage_type)
                                              {
                                                /*$sqlupdated  = "update customer_master ";
                                                $sqlupdated .= " SET route_code='".$route_code."'";
                                                $sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";
                                                $sqlupdated .= " , customer_name='".addslashes($customer_name)."'";
                                                $sqlupdated .= " , current_balance    ='".$current_balance."'";
                                                $sqlupdated .= " , acedns='".$acedns."'";
                                                $sqlupdated .= " , branch_code='".$branch_code."'";
                                                $sqlupdated .= " , TD='".$TD."'";
                                                $sqlupdated .= " , cust_type='".$customer_type."'";
                                                  $sqlupdated .= " , credit_days='".$credit_days."'";
                                                $sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";
                                                $sqlupdated .= " , address='".$address."'";
                                                $sqlupdated .= " , owner_name='".$owner_name."'";
                                                $sqlupdated .= " , owner_phone='".$owner_phone."'";
                                                $sqlupdated .= " , cust_class='".$cust_class."'";
                                                $sqlupdated .= " , weekly_closing_day='".$weekly_closing_day."'";
                                                $sqlupdated .= " , TIN='".$TIN."'";
                                                $sqlupdated .= " , PAN='".$PAN."'";
                                                $sqlupdated .= " , district='".$district."'";
                                                $sqlupdated .= " , landline_no='".$landline_no."'";
                                                $sqlupdated .= " , minimum_stock='".$minimum_stock."'";
                                                $sqlupdated .= " , bank_name='".$bank_name."'";
                                                $sqlupdated .= " , bank_account_number='".$bank_account_number."'";
                                                $sqlupdated .= " , rds_tag='".$rds_code."',visit_day='".addslashes($visit_day)."',coverage_type='".addslashes($coverage_type)."', download_time=CURRENT_TIMESTAMP()
                                                         WHERE  ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."' ";
                                                mysql_query($sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
                                                modifyempdatadownloadlog($emp_code,strtoupper($folderName));*/
                                                $downloadtime=date('Y-m-d H:i:s');
                                                $CUTDB->table('customer_master')
                                                ->where($update_condition)
                                                ->where('emp_code', $emp_code)
                                                ->where('route_code', $route_code)
                                                ->limit(1)
                                                ->update(array('dns_customer_code' => $dns_customer_code,
                                                'customer_name'=>$customer_name,
                                                'current_balance'=>$current_balance,
                                                'acedns'=>$acedns,
                                                'address' => $address,
                                                'pin'=>$pin,
                                                'phone_no'=>$phone_no,
                                                'landline_no' => $landline_no,
                                                'route_code' => $route_code,
                                                'credit_limit' => $credit_limit,
                                                'credit_days' => $credit_days,
                                                'TD' => $TD,
                                                'cust_type' => $customer_type,
                                                'sauda_validity_period'=>$sauda_validity_period,
                                                'rds_tag' => $rds_code,
                                                'owner_name' => $owner_name,
                                                'owner_phone' => $owner_phone,
                                                'cust_class'=>$cust_class,
                                                'weekly_closing_day' => $weekly_closing_day,
                                                'TIN' => $TIN,
                                                'PAN' => $PAN,
                                                'district' => $district,
                                                'branch_code' => $branch_code,
                                                'minimum_stock' => $minimum_stock,
                                                'bank_name' => $bank_name,
                                                'bank_account_number' => $bank_account_number,
                                                'visit_day' => $visit_day,
                                                'coverage_type'=>$coverage_type,
                                                'download_time' => $downloadtime
                                                 ));
                                              }
                                              if(($credit_limit_db!=$credit_limit))
                                              {
                                                $sqlupdatedcredit  = "update customer_master ";
                                                $sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";
                                                $sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP()
                                                         WHERE ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."'";
                                                mysql_query($sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
                                                modifyempdatadownloadlog($emp_code,strtoupper($folderName));
                                              }
                                              $customer_code=$customer_code_db;
                                            }//End of else
                                            //For Distributor route creation
                                              if(Session::get('distributorrouteplanning')=='yes')
                                               {
                                                 $sqlchkdistributorroute= $CUTDB->table('distributor_route_relation')
                                                                                   ->select('distributor_code')
                                                                                   ->where('distributor_code', '=' ,$customer_code)
                                                                                   ->where('route_code', '=' ,$route_code)
                                                                                   ->where('emp_code', '=' ,$emp_code)
                                                                                   ->first();

                                                 if(count($sqlchkdistributorroute)==0)
                                                 {
                                                   $downloadtime=date('Y-m-d H:i:s');
                                                   $sqlinsertdistributorroute=  $CUTDB->table('distributor_route_relation')->insert(array(
                                                                                   'distributor_code'=>$customer_code,
                                                                                   'route_code'=>$route_code,
                                                                                   'emp_code'=>$emp_code,
                                                                                   'download_time'=>$downloadtime
                                                                                ));

                                                 }
                                               }
                                            //End of Distributor route creation
                                          }//End of emp code name foreach
                                          //exit();
                                         }//End of IF
                                           $rec_count++;
                                        }//End of main foreach
                                        //exit();
                                        //Emp code checking start
                                        $sqlempcoderetail=$CUSTDB->SELECT("SELECT emp_code FROM customer_master WHERE emp_code NOT IN(SELECT emp_code FROM employee_master)");

                                        if(count($sqlempcoderetail)>0)
                                        {
                                          $empcoderetail='';

                                          foreach ($sqlempcoderetail as $key => $value) {

                                            $empcoderetail=$empcoderetail.$value->emp_code.',';
                                          }
                                          $empcoderetail=substr($empcoderetail,0,-1);
                                          $errorempcoderetail=$empcoderetail.' exists in customer_master but not exists in employee_master.';
                                          array_push($error_array,$errorempcoderetail);
                                        }
                                      //Emp code checking end
                                      //Route code checking start
                                        $sqlroutecoderetail=$CUTDB->SELECT("SELECT route_code FROM customer_master WHERE route_code NOT IN(SELECT route_code FROM route_master)");

                                        if(count($sqlroutecoderetail)>0)
                                        {
                                          $routecoderetail='';

                                           foreach ($sqlroutecoderetail as $key => $value) {

                                            $routecoderetail=$routecoderetail.$value->route_code.',';
                                          }
                                          $routecoderetail=substr($routecoderetail,0,-1);
                                          $errorroutecoderetail=$routecoderetail.' exists in customer_master but not exists in route_master.';
                                          array_push($error_array,$errorroutecoderetail);
                                        }
                                      //Route code checking end
                                      }
                                      else
                                      {
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
                                          $dns_customer_code =trim($data[0]);
                                          $customer_name    =trim($data[1]);
                                          $phone_no        =trim($data[2]);
                                          $dns_route_code      =trim($data[3]);
                                          $route_name      =trim($data[4]);
                                          $emp_code_name        =trim($data[5]);
                                          $emp_code_name_array=explode(';',$emp_code_name);
                                          $emp_code_name=$emp_code_name_array[0];
                                          $acedns          =trim($data[6]);
                                          $credit_limit    =trim($data[7]);
                                          $credit_days     =trim($data[8]);
                                          $current_balance =trim($data[9]);
                                          $black_list      =trim($data[10]);
                                          $TD                =trim($data[11]);
                                          $branch_code_name =trim($data[12]);
                                          $customer_type   =trim($data[13]);
                                          $rds_tag   =trim($data[14]);
                                          $sauda_validity_period  =trim($data[15]);
                                          $address  =trim($data[16]);
                                          $landline_no  =trim($data[17]);
                                          $owner_name  =trim($data[18]);
                                          $owner_phone  =trim($data[19]);
                                          $cust_class  =trim($data[20]);
                                          $weekly_closing_day  =trim($data[21]);
                                          $coverage_type  =trim($data[22]);
                                          $TIN  =trim($data[23]);
                                          $PAN  =trim($data[24]);
                                          $district  =trim($data[25]);
                                          $minimum_stock  =trim($data[26]);
                                          $bank_name  =trim($data[27]);
                                          $bank_account_number  =trim($data[28]);
                                          $email  =trim($data[29]);
                                          $visit_day  =trim($data[30]);

                                          //For employee code and branch code
                                          if(Session::get('iscodeneed')=='yes'){
                                            $sqlempcode=$CUTDB->table('employee_master')
                                                              ->select('emp_code','branch_code')
                                                              ->where('dns_emp_code', '=' ,addslashes($emp_code_name))
                                                              ->first();

                                            $emp_code=$sqlempcode->emp_code;
                                            $branch_code=$sqlempcode->branch_code;
                                          }
                                          else
                                          {
                                            $sqlempcode=$CUTDB->table('employee_master')
                                                              ->select('emp_code','branch_code')
                                                              ->where('emp_name', '=' ,addslashes($emp_code_name))
                                                              ->first();

                                            $emp_code=$sqlempcode->emp_code;
                                            $branch_code=$sqlempcode->branch_code;
                                          }
                                          //For distributor tagged
                                         if(Session::get('iscodeneed')=='yes'){
                                            $sqlrdscode=$CUTDB->table('customer_master')
                                                              ->select('customer_code')
                                                              ->where('dns_customer_code', '=' ,addslashes($rds_tag))
                                                              ->first();
                                          }
                                          else
                                          {

                                            $sqlrdscode=$CUTDB->table('customer_master')
                                                              ->select('customer_code')
                                                              ->where('customer_name', '=' ,addslashes($rds_tag))
                                                              ->first();
                                          }

                                          $rds_code=$sqlrdscode->customer_code;

                                          //For route
                                           if(Session::get('iscodeneed')=='yes'){

                                            $sqlroutechk=$CUTDB->table('route_master')
                                                              ->where('dns_route_code', '=' ,addslashes($dns_route_code))
                                                              ->first();
                                          }
                                          else
                                          {

                                            $sqlroutechk=$CUTDB->table('route_master')
                                                              ->where('route_name', '=' ,addslashes($route_name))
                                                              ->first();
                                          }

                                          if(count($sqlroutechk)<1 && $route_name!='')
                                          {


                                            $routecode=$CUTDB->table('route_master')
                                                             ->where('route_code','NOT LIKE','N%')
                                                             ->max('route_code');
                                            if($routecode!='')
                                            {
                                              $routecode1=substr($routecode,3);
                                              $newcode=$routecode1+1;
                                              $max_route_code='RT/'.$newcode;
                                            }
                                            else {
                                              $max_route_code='RT/1';
                                            }
                                            $downloadtime=date('Y-m-d H:i:s');
                                            $CUTDB->table('route_master')->insert(array(
                                                'route_code' => $max_route_code,
                                                'dns_route_code' => $dns_route_code,
                                                'route_name' => $route_name,
                                                'download_time' =>$downloadtime
                                            ));

                                            $route_code=$max_route_code;
                                          }
                                          else
                                          {

                                            $route_code=$sqlroutechk->route_code;
                                          }
                                          //For customer
                                          if(Session::get('iscodeneed')=='yes'){
                                             $sqlcustomernamechk=$CUTDB->table('customer_master')
                                                               ->where('dns_customer_code', '=' ,addslashes($dns_customer_code))
                                                               ->where('route_code', '=' ,$route_code)
                                                               ->get();

                                          }
                                          else
                                          {
                                           $sqlcustomernamechk=$CUTDB->table('customer_master')
                                                             ->where('customer_name', '=' ,addslashes($customer_name))
                                                             ->where('route_code', '=' ,$route_code)
                                                             ->get();


                                          }


                                          $csv_row_count=$rec_count+1;
                                          if(count($sqlcustomernamechk)<1)
                                          {

                                            $custcode=$CUTDB->table('customer_master')
                                                            ->where('customer_code','NOT LIKE','N%')
                                                            ->max('customer_code');

                                            if($custcode!='')
                                            {
                                              $custcode++;
                                            }
                                            else {
                                              $custcode='C/0000001';
                                            }
                                            $max_customer_code = $custcode;
                                            $downloadtime=date('Y-m-d H:i:s');
                                            $CUTDB->table('customer_master')->insert(array(
                                                'customer_code' => $max_customer_code,
                                                'dns_customer_code' => $dns_customer_code,
                                                'customer_name'=>addslashes($customer_name),
                                                'address' => $address,
                                                'pin'=>$pin,
                                                'phone_no'=>$phone_no,
                                                'emp_code'=>$emp_code,
                                                'current_balance'=>$current_balance,
                                                'landline_no' => $landline_no,
                                                'route_code' => $route_code,
                                                'credit_limit' => $credit_limit,
                                                'credit_days' => $credit_days,
                                                'acedns' => 'Y',
                                                'black_list'=>'N',
                                                'TD' => $TD,
                                                'cust_type' => $customer_type,
                                                'rds_tag' => $rds_code,
                                                'sauda_validity_period'=>$sauda_validity_period,
                                                'owner_name' => $owner_name,
                                                'owner_phone' => $owner_phone,
                                                'cust_class'=>$cust_class,
                                                'weekly_closing_day' => $weekly_closing_day,
                                                'TIN' => $TIN,
                                                'PAN' => $PAN,
                                                'district' => $district,
                                                'branch_code' => addslashes($branch_code),
                                                'minimum_stock' => $minimum_stock,
                                                'bank_name' => $bank_name,
                                                'bank_account_number' => $bank_account_number,
                                                'email' => $email,
                                                'coverage_type'=>$coverage_type,
                                                'visit_day' => $visit_day,
                                                'state_code'=>$state,
                                                'download_time' =>$downloadtime
                                            ));


                                            $customer_code=$max_customer_code;
                                          }
                                          else
                                          {

                                            $customer_code_db=$sqlcustomernamechk->customer_code;
                                            $route_code_db=$sqlcustomernamechk->route_code;
                                            $current_balance_db=$sqlcustomernamechk->current_balance;
                                            $phone_no_db=$sqlcustomernamechk->phone_no;
                                            $credit_limit_db=$sqlcustomernamechk->credit_limit;
                                            $credit_days_db=$sqlcustomernamechk->credit_days;
                                            $acedns_db=$sqlcustomernamechk->acedns;
                                            $black_list_db=$sqlcustomernamechk->black_list;
                                            $TD_db=$sqlcustomernamechk->TD;
                                            $customer_type_db=$sqlcustomernamechk->cust_type;
                                            $rds_tag_db=$sqlcustomernamechk->rds_tag;
                                            $branch_code_db=$sqlcustomernamechk->branch_code;
                                            $sauda_validity_period_db=$sqlcustomernamechk->sauda_validity_period;
                                            $customer_name_db=$sqlcustomernamechk->customer_name;
                                            $dns_customer_code_db=$sqlcustomernamechk->dns_customer_code;
                                            $address_db=$sqlcustomernamechk->address;
                                            $owner_name_db=$sqlcustomernamechk->owner_name;
                                            $owner_phone_db=$sqlcustomernamechk->owner_phone;
                                            $cust_class_db=$sqlcustomernamechk->cust_class;
                                            $weekly_closing_day_db=$sqlcustomernamechk->weekly_closing_day;
                                            $TIN_db=$sqlcustomernamechk->TIN;
                                            $PAN_db=$sqlcustomernamechk->PAN;
                                            $district_db=$sqlcustomernamechk->district;
                                            $zone_db=$sqlcustomernamechk->zone;
                                            $landline_no_db=$sqlcustomernamechk->landline_no;
                                            $minimum_stock_db=$sqlcustomernamechk->minimum_stock;
                                            $bank_name_db=$sqlcustomernamechk->bank_name;
                                            $bank_account_number_db=$sqlcustomernamechk->bank_account_number;
                                            $email_db=$sqlcustomernamechk->email;
                                            $visit_day_db=$sqlcustomernamechk->visit_day;
                                            $coverage_type_db=$sqlcustomernamechk->coverage_type;


                                            if(Session::get('iscodeneed')=='yes'){
                                              $update_condition=("'dns_customer_code',$dns_customer_code");

                                            }
                                            else
                                            {
                                              $update_condition=("'customer_name',$customer_name");
                                            }

                                            if($route_code_db!=$route_code || $current_balance_db!=$current_balance || $TD_db!=$TD || $customer_type_db!=$customer_type
                                            || $rds_tag_db!=$rds_code
                                            || $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days
                                            || $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no || $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class || $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district || $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name || $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day || $coverage_type_db!=$coverage_type)
                                            {
                                              /*$sqlupdated  = "update customer_master ";
                                              $sqlupdated .= " SET route_code='".$route_code."'";
                                              $sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";
                                              $sqlupdated .= " , customer_name='".addslashes($customer_name)."'";
                                              $sqlupdated .= " , current_balance    ='".$current_balance."'";
                                              $sqlupdated .= " , branch_code='".$branch_code."'";
                                              $sqlupdated .= " , TD='".$TD."'";
                                              $sqlupdated .= " , cust_type='".$customer_type."'";
                                              $sqlupdated .= " , phone_no='".$phone_no."'";
                                                $sqlupdated .= " , credit_days='".$credit_days."'";
                                              $sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";
                                              $sqlupdated .= " , address='".$address."'";
                                              $sqlupdated .= " , owner_name='".$owner_name."'";
                                              $sqlupdated .= " , owner_phone='".$owner_phone."'";
                                              $sqlupdated .= " , cust_class='".$cust_class."'";
                                              $sqlupdated .= " , weekly_closing_day='".$weekly_closing_day."'";
                                              $sqlupdated .= " , TIN='".$TIN."'";
                                              $sqlupdated .= " , PAN='".$PAN."'";
                                              $sqlupdated .= " , district='".$district."'";
                                              $sqlupdated .= " , landline_no='".$landline_no."'";
                                              $sqlupdated .= " , minimum_stock='".$minimum_stock."'";
                                              $sqlupdated .= " , bank_name='".$bank_name."'";
                                              $sqlupdated .= " , bank_account_number='".$bank_account_number."'";
                                              $sqlupdated .= " , rds_tag='".$rds_code."',visit_day='".addslashes($visit_day)."',
                                                        coverage_type='".addslashes($coverage_type)."',download_time=CURRENT_TIMESTAMP()
                                                       WHERE  ".$update_condition." AND route_code='".$route_code."' ";
                                              mysql_query($sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");*/
                                              //modifyempdatadownloadlog($emp_code,strtoupper($folderName));


                                              $CUTDB->table('customer_master')
                                              ->where($update_condition)
                                              ->where('route_code', $route_code)
                                              ->limit(1)
                                              ->update(array('dns_customer_code' => $dns_customer_code,
                                              'customer_name'=>$customer_name,
                                              'current_balance'=>$current_balance,
                                              'acedns'=>$acedns,
                                              'address' => $address,
                                              'pin'=>$pin,
                                              'phone_no'=>$phone_no,
                                              'landline_no' => $landline_no,
                                              'route_code' => $route_code,
                                              'credit_limit' => $credit_limit,
                                              'credit_days' => $credit_days,
                                              'TD' => $TD,
                                              'cust_type' => $customer_type,
                                              'sauda_validity_period'=>$sauda_validity_period,
                                              'rds_tag' => $rds_code,
                                              'owner_name' => $owner_name,
                                              'owner_phone' => $owner_phone,
                                              'cust_class'=>$cust_class,
                                              'weekly_closing_day' => $weekly_closing_day,
                                              'TIN' => $TIN,
                                              'PAN' => $PAN,
                                              'district' => $district,
                                              'branch_code' => $branch_code,
                                              'minimum_stock' => $minimum_stock,
                                              'bank_name' => $bank_name,
                                              'bank_account_number' => $bank_account_number,
                                              'visit_day' => $visit_day,
                                              'coverage_type'=>$coverage_type
                                               ));
                                            }
                                            if(($credit_limit_db!=$credit_limit))
                                            {
                                             $downloadtime=date('Y-m-d H:i:s');
                                             $CUTDB->table('customer_master')
                                             ->where($update_condition)
                                             ->where('route_code',$route_code)
                                             ->limit(1)
                                             ->update(array(
                                                     'credit_limit' => $credit_limit,
                                                     'download_time_credit_limit' => $downloadtime
                                              ));

                                            }
                                            $customer_code=$customer_code_db;
                                          }
                                          //For Distributor route creation
                                            if(Session::get('distributorrouteplanning')=='yes')
                                             {
                                               $downloadtime=date('Y-m-d H:i:s');
                                               $sqlinsertdistributorroute=  $CUTDB->table('distributor_route_relation')->insert(array(
                                                                                'distributor_code'=>$customer_code,
                                                                                'route_code'=>$route_code,
                                                                                'emp_code'=>$emp_code,
                                                                                'download_time'=>$downloadtime
                                                                            ));



                                             }
                                          //End of Distributor route creation
                                          //For customer route relation
                                          $sqlselcustomerroute=$CUTDB->table('customer_route_emp_relation')
                                                            ->select('customer_code','route_code','emp_code')
                                                            ->where('customer_code', '=' ,$customer_code)
                                                            ->where('route_code', '=' ,$route_code)
                                                            ->where('emp_code', '=' ,$emp_code)
                                                            ->get();

                                          if(count($sqlselcustomerroute)==0)
                                          {
                                              $downloadtime=date('Y-m-d H:i:s');
                                              $sqlinsertcustomerroute=  $CUTDB->table('customer_route_emp_relation')->insert(array(
                                                    'customer_code' => $customer_code,
                                                    'route_code' => $route_code,
                                                    'emp_code' => $emp_code,
                                                    'acedns'=>$acedns,
                                                    'download_time' =>$downloadtime
                                                ));
                                          }
                                          else
                                          {
                                            $downloadtime=date('Y-m-d H:i:s');
                                            $sqlupdatecustomerroute=  $CUTDB->table('customer_route_emp_relation')
                                              ->where('customer_code', $customer_code)
                                              ->where('route_code', $route_code)
                                              ->where('emp_code', $emp_code)
                                              ->limit(1)
                                              ->update(array(
                                                      'acedns' => $acedns,
                                                      'download_time' => $downloadtime
                                               ));

                                          }
                                          //exit();
                                          //End for customer route relation
                                          }
                                          $rec_count++;
                                        }//End of for loop
                                      }//End of else
                                      $successval=1;
                  }
      }


}
