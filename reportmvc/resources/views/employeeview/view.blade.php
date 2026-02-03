@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Commonfunctions;
$dbname=Session::get('DBNAME');
@endphp
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Employee Details</h2>
                      <a href="{{url('/employee/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>

                      @if($errors->any())
                      <div class="alert alert-danger fade in">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                      </div>
                      @endif
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                                       {{$employeedetails[0]->dns_emp_code}}

                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$employeedetails[0]->emp_name}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php
                      if(Session::get('isbranch')=="1")
                      {
                      @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Branch</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Commonfunctions::getNameINTable($dbname, 'branch_master', 'branch_name', 'branch_code', $employeedetails[0]->branch_code) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php }  @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Email ID</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{$employeedetails[0]->email}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Phone Number</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{$employeedetails[0]->phone_no}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Report To</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ Commonfunctions::getNameINTable($dbname, 'employee_master', 'emp_name', 'emp_code', $employeedetails[0]->reporting_to) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{$employeedetails[0]->vertical_value}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Head Quarter</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$employeedetails[0]->HQ}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sale Access</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$employeedetails[0]->sale_access}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Designation</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$employeedetails[0]->designation}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">District</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$employeedetails[0]->District}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">State</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$employeedetails[0]->state}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Zone</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$employeedetails[0]->zone}}
                        </div>
                        <div class="clearfix"></div>
                      </div>

                  </div>
                </div>
              </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
