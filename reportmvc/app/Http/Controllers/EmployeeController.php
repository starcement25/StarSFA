<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;

use Session;
use App\Helpers\Employee;
use App\Helpers\Commonfunctions;


class EmployeeController extends Controller
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
        $employees=Employee::getAllEmployeeList($sdbname);
        return view('employeeview.index',compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $branch_list=Employee::getBrnlist($sdbname);
        $employee_list=Employee::getEmplist($sdbname);
        $vartical_list=Employee::getVarticallist($sdbname);
        return view('employeeview.create',compact('branch_list','employee_list','vartical_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Employee::dydb($dbname);
      $nickname = substr(Session::get('DBNAME'),7);
      $configaretion=Commonfunctions::getNameTableMainDb('configuration_filter','filter_value','nick_name',$nickname);
      $avilableconfig=explode(',',$configaretion);
      $this->validate($request, [
            'emp_name' => 'required',
            'emp_emailid' => 'required|email',
            'emp_phnum' => 'required|numeric',
            'emp_reportto' => 'required',
            'state' => 'required',
      ]);
      if(in_array('Area wise',$avilableconfig))
      {
        $this->validate($request, [
              'zone' => 'required',
        ]);
      }
      if(in_array('State',$avilableconfig))
      {
        $this->validate($request, [
              'state' => 'required',
        ]);
      }
      if(in_array('Designation',$avilableconfig))
      {
        $this->validate($request, [
              'designation' => 'required',
        ]);
      }
      if(in_array('Hq',$avilableconfig))
      {
        $this->validate($request, [
              'HQ' => 'required',
        ]);
      }


      $emp_branch='';
      $empmultipleValues = $request->input('emp_branch');
      if(count($empmultipleValues))
      {
          foreach($empmultipleValues as $value)
          {
              $emp_branch.=$value.',';
          }
          $emp_branch=substr($emp_branch,0,-1);
      }

      $emp_reportto='';
      $empmultiplereporttoValues = $request->input('emp_reportto');
      if(count($empmultiplereporttoValues))
      {
          foreach($empmultiplereporttoValues as $value)
          {
              $emp_reportto.=$value.',';
          }
          $emp_reportto=substr($emp_reportto,0,-1);
      }

      $emp_vartical='';
      $empmultiplevarticalValues = $request->input('vartical');
      if($empmultiplevarticalValues)
      {
          foreach($empmultiplevarticalValues as $value)
          {
              $emp_vartical.=$value.',';
          }
          $emp_vartical=substr($emp_vartical,0,-1);
      }

      $dnsbranchcode=$request->input('emp_code');
      $empname=$request->input('emp_name');
      $empbranch=$emp_branch;
      $empemail=$request->input('emp_emailid');
      $empphone=$request->input('emp_phnum');
      $empreportto=$emp_reportto;
      $emphq=$request->input('emp_hq');
      $empsaleaccess=$request->input('sale_access');
      $empdesignation=$request->input('emp_designation');
      $empdistrict=$request->input('district');
      $empstate=$request->input('state');
      $empzone=$request->input('zone');
      $vartical=$emp_vartical;

      $downloadtime=date('Y-m-d H:i:s');

      $empcode=$CUTDB->table('employee_master')->max('emp_code');
      if($empcode)
      $empcode++;
      else{
      $empcode='E0001';
      }


       $CUTDB->table('employee_master')->insert(array(
          'emp_code' => $empcode,
          'dns_emp_code' => $dnsbranchcode,
          'emp_name' => $empname,
          'acedns' => 'Y',
          'branch_code' => $empbranch,
          'reporting_to' => $empreportto,
          'vertical_value' => $vartical,
          'email' => $empemail,
          'phone_no' => $empphone,
          'HQ' => $emphq,
          'sale_access' => $empsaleaccess,
          'designation' => $empdesignation,
          'District' => $empdistrict,
          'state' => $empstate,
          'zone' => $empzone,
          'app_access' => 'Y',
          'download_time' =>$downloadtime
       ));
       $CUTDB->table('changepassword')->insert(array(
             'emp_code' => $empcode,
             'newpassword'=>'1234',
             'oldpassword'=>'1234',
             'status'=>'true',
             'is_licensed'=>'1'
      ));

      if($nickname=='STAR')
      {
         $CUTDB->table('mis_data_details')->insert(array(
                   'emp_code'=>$empcode,
                   'sale_access'=>$empsaleaccess,
                   'reporting_to'=>$empreportto
         ));
      }
      return redirect('/employee')->with('message', 'Success!');

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
        $tablename="employee_master";
        $fieldname="emp_code";
        $parameter=$id;
        $employeedetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('employeeview.view',compact('employeedetails'));
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
      $tablename="employee_master";
      $fieldname="emp_code";
      $parameter=$id;
      $employeedetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      $branch_list=Employee::getBrnlist($sdbname);
      $employee_list=Employee::getEmplist($sdbname);
      $vartical_list=Employee::getVarticallist($sdbname);

      return view('employeeview.edit',compact('employeedetails','branch_list','employee_list','vartical_list'));
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
        $CUTDB = Employee::dydb($dbname);

        $emp_branch='';
        $empmultipleValues = Input::get('emp_branch');
        if(count($empmultipleValues))
        {
            foreach($empmultipleValues as $value)
            {
                $emp_branch.=$value.',';
            }
            $emp_branch=substr($emp_branch,0,-1);
        }

        $emp_reportto='';
        $empmultiplereporttoValues = Input::get('emp_reportto');
        if(count($empmultiplereporttoValues))
        {
            foreach($empmultiplereporttoValues as $value)
            {
                $emp_reportto.=$value.',';
            }
            $emp_reportto=substr($emp_reportto,0,-1);
        }

        $emp_vartical='';
        $empmultiplevarticalValues = Input::get('vartical');
        if(count($empmultiplevarticalValues))
        {
            foreach($empmultiplevarticalValues as $value)
            {
                $emp_vartical.=$value.',';
            }
            $emp_vartical=substr($emp_vartical,0,-1);
        }

        $dnsbranchcode=Input::get('emp_code');
        $empname=Input::get('emp_name');
        $empbranch=$emp_branch;
        $empemail=Input::get('emp_emailid');
        $empphone=Input::get('emp_phnum');
        $empreportto=$emp_reportto;
        $emphq=Input::get('emp_hq');
        $empsaleaccess=Input::get('sale_access');
        $empdesignation=Input::get('emp_designation');
        $empdistrict=Input::get('district');
        $empstate=Input::get('state');
        $empzone=Input::get('zone');
        $vartical=$emp_vartical;

        if(Session::get('iscodeneed')=='yes')
        {

           $rules1[] = array(
              'emp_code' => 'required',
           );
        }
        $rules1[] = array(
          'emp_name' => 'required',
          'emp_emailid' => 'required|email',
          'emp_phnum' => 'required|numeric',
          'emp_reportto' => 'required',
          'state' => 'required',
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
             return Redirect::to('employee/'.$id.'/edit')->withErrors($validator);
        }
        else{
              $CUTDB->table('employee_master')
              ->where('emp_code', $id)
              ->limit(1)
              ->update(array('dns_emp_code' => $dnsbranchcode,
              'emp_name' => $empname,
              'branch_code' => $empbranch,
              'reporting_to' => $empreportto,
              'vertical_value' => $vartical,
              'email' => $empemail,
              'phone_no' => $empphone,
              'HQ' => $emphq,
              'sale_access' => $empsaleaccess,
              'designation' => $empdesignation,
              'District' => $empdistrict,
              'state' => $empstate,
              'zone' => $empzone
               ));

              return redirect('/employee')->with('message', 'Success!');
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

    public function employeeUploadFile(Request $request)
    {


          $dbname =$this->databasename();
          $CUTDB = Branch::dydb($dbname);

          $file = $request->file('brach_csv_file');

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
              $downloadtime=date('Y-m-d H:i:s');
        			$lines = file($filename);
        			$reporting_to_array=array();
        			foreach($lines as $line)
        			{
        				$i = 0;
        				$char = substr($line, $i, 1);
        				$value ="";
        				$data="";
        				$double_coute_found = false;

        				if($rec_count>=1)
        				{
        					$reporting_to_val='';
        					$branch_code='';

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


        					$dns_employee_code=trim($data[0]);
        					$employee_name=trim($data[1]);
        					$branch_code_name=trim($data[2]);
        					$vertical_value=trim($data[3]);
        					$reporting_to=trim($data[4]);
        					if(strpos($reporting_to,';')!=false)
        					 {
        						$reporting_to=str_replace(';',',',$reporting_to);
        					 }
        					if($reporting_to!='')
        					{
        						$reporting_to=str_replace(', ',',',$reporting_to);
        						$reporting_val_array=explode(',',$reporting_to);
        						foreach($reporting_val_array as $reporting_val)
        						{
        							if(Session::get('iscodeneed') == 'yes')
        							{

                        $sql_check_emp_code = $CUTDB->table('employee_master')
                                                    ->where('dns_emp_code', '=' ,ltrim($reporting_val))
                                                    ->first();
        							}
        							else
        							{

                         $sql_check_emp_code = $CUTDB->table('employee_master')
                                                     ->where('emp_name', '=' ,ltrim($reporting_val))
                                                     ->first();
        							}

        							$reporting_not_exists='';
        							if(count($sql_check_emp_code) == 0)
        							{
        								if(Session::get('iscodeneed') == 'yes')
        								{
        									$reporting_not_exists=$dns_employee_code.'#'.$reporting_val;
        									array_push($reporting_to_array,$reporting_not_exists);
        								}
        								else
        								{
        									$reporting_not_exists=$employee_name.'#'.$reporting_val;
        									array_push($reporting_to_array,$reporting_not_exists);
        								}
        							}
        						}
        				    }


        					$email=trim($data[5]);
        					$phone_no=trim($data[6]);
        					$sale_access=trim($data[7]);
        					$designation=trim($data[8]);
        					$HQ=trim($data[9]);
        					$state=trim($data[10]);
        					$zone=trim($data[11]);
        					$acedns=trim($data[12]);
        					if($acedns =='N')
        					{
        						$app_access='N';
        					}
        					else
        					{
        						$app_access='Y';
        					}

        					if(Session::get('iscodeneed')=='yes'){
        						$sqlbranchcode=$CUTDB->select("SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')");
        						$sqlreportingto=$CUTDB->select("SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code,'".$reporting_to."')");
        					}
        					else
        					{
        						$sqlbranchcode=$CUTDB->select("SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')");
        						$sqlreportingto=$CUTDB->select("SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')");
        					}


                  foreach ($sqlbranchcode as $key => $value) {
                     $branch_code=$branch_code.$value->branch_code.',';
                  }
        					$branch_code=substr($branch_code,0,-1);

        					foreach ($sqlreportingto as $key => $value) {
        						$reporting_to_val=$reporting_to_val.$value->emp_code.',';
        					}
        					$reporting_to_val=substr($reporting_to_val,0,-1);
        					//exit();
        					if(Session::get('iscodeneed')=='yes'){
        						$sqlempnamechk=$CUTDB->table('employee_master')
                                    ->where('dns_emp_code', '=' ,$dns_employee_code)
                                    ->first();
        					}
        					else{
                    $sqlempnamechk=$CUTDB->table('employee_master')
                                    ->where('emp_name', '=' ,$employee_name)
                                    ->first();
        					}
        					$csv_row_count=$rec_count+1;
        					if(count($sqlempnamechk)<1)
        					{

                      $empcode=$CUTDB->table('employee_master')->max('emp_code');
                      if($empcode)
                      $max_emp_code++;
                      else{
                      $max_emp_code='E0001';
                      }
                      $CUTDB->table('employee_master')->insert(array(
                          'emp_code' => $max_emp_code,
                          'dns_emp_code' => $dns_employee_code,
                          'emp_name' => $employee_name,
                          'acedns' => 'Y',
                          'branch_code' => $branch_code,
                          'reporting_to' => $reporting_to_val,
                          'email' => $email,
                          'phone_no' => $phone_no,
                          'HQ' => $HQ,
                          'sale_access' => $sale_access,
                          'designation' => $designation,
                          'state' => $state,
                          'zone' => $zone,
                          'app_access' => $app_access,
                          'download_time' =>$downloadtime
                      ));
                      $CUTDB->table('changepassword')->insert(array(
                             'emp_code' => $max_emp_code,
                             'newpassword'=>'1234',
                             'oldpassword'=>'1234',
                             'status'=>'true',
                             'is_licensed'=>'1'
                     ));
                      $nickname = substr(Session::get('DBNAME'),7);
                      if($nickname=='STAR')
                      {
                         $CUTDB->table('mis_data_details')->insert(array(
                                   'emp_code'=>$max_emp_code,
                                   'sale_access'=>$sale_access,
                                   'reporting_to'=>$reporting_to_val
                         ));
                      }
                  }
                  else {
                    $CUTDB->table('employee_master')
                    ->where('emp_code', $sqlempnamechk->emp_code)
                    ->limit(1)
                    ->update(array(  'dns_emp_code' => $dns_employee_code,
                      'emp_name' => $employee_name,
                      'branch_code' => $branch_code,
                      'reporting_to' => $reporting_to_val,
                      'email' => $email,
                      'phone_no' => $phone_no,
                      'HQ' => $HQ,
                      'sale_access' => $sale_access,
                      'designation' => $designation,
                      'state' => $state,
                      'zone' => $zone,
                      'app_access' => $app_access,
                     ));
                  }
                }
            }

         }
    }

}
