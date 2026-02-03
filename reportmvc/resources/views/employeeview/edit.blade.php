@extends('layouts.default')

@section('main_container')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit Employee</h2>
                      <a href="{{url('/employee/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'employee/'.$employeedetails[0]->emp_code,'class'=>'form-signin','method' => 'PUT')) }}

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

                          {{ Form::text('emp_code', $employeedetails[0]->dns_emp_code, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Employee Code',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('emp_name', $employeedetails[0]->emp_name, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Employee Name',
          								)) }}
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
                           @php $branch = explode(',', $employeedetails[0]->branch_code);@endphp
                           {{Form::select('emp_branch[]', $branch_list, $branch, ['class' => 'form-control js-example-placeholder-multiple','multiple'=>'multiple'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php }  @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Email ID</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('emp_emailid', $employeedetails[0]->email, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Employee Email ID',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Phone Number</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('emp_phnum', $employeedetails[0]->phone_no, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Employee Phone Number',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Report To</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            @php $empreport = explode(',', $employeedetails[0]->reporting_to);@endphp
                            {{Form::select('emp_reportto[]', $employee_list, $empreport, ['class' => 'form-control report-to-multiple','multiple'=>'multiple'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php
                      if(Session::get('isvartical')=="yes")
                      {
                      @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            @php $varticalval = explode(',', $employeedetails[0]->vertical_value);@endphp
                            {{Form::select('vartical[]', $vartical_list, $varticalval, ['class' => 'form-control vartical-multiple','multiple'=>'multiple'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php } @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Head Quarter</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('emp_hq', $employeedetails[0]->HQ, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Head Quarter',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sale Access</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('sale_access', $employeedetails[0]->sale_access, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Sale Access',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Designation</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('emp_designation', $employeedetails[0]->designation, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Designation',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">District</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('district', $employeedetails[0]->District, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'District',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">State</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('state', $employeedetails[0]->state, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'State',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Zone</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('zone', $employeedetails[0]->zone, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Zone',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          {{ Form::submit('Submit',array(
          								    'class' => 'btn btn-success',
          								    'id' => '',
          								    'placeholder' => '',
          								)) }}
                        </div>
                      </div>

                    {{ Form::close() }}
                  </div>
                </div>
              </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
