@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Commonfunctions;
$dbname=Session::get('DBNAME');
@endphp

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Employee List</h2>
                <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">Upload Csv</button>
                <a href="{{url('/employee/create')}}" class="btn btn-success pull-right">Add New Employee</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action" id="emp_table">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Employee Code </th>
                        <th class="column-title" style="display: table-cell;">Employee Name </th>
                        @php
                        if(Session::get('isbranch')=="1")
                        {
                        @endphp
                        <th class="column-title" style="display: table-cell;">Employee Branch Name </th>
                        @php } @endphp
                        <th class="column-title" style="display: table-cell;">Email</th>
                        <th class="column-title" style="display: table-cell;">Phone number</th>
                        <th class="column-title" style="display: table-cell;">Reporting Person</th>
                        <th class="column-title" style="display: table-cell;">Status </th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                  <tbody>
                      @foreach ($employees as $key => $employee)
                      <tr class="even pointer">
                        <td class=" ">{{ $employee->emp_code }}</td>
                        <td class=" ">{{ $employee->emp_name }}</td>
                        @php
                        if(Session::get('isbranch')=="1")
                        {
                        @endphp
                        <td class=" ">
                            {{ Commonfunctions::getNameINTable($dbname, 'branch_master', 'branch_name', 'branch_code', $employee->branch_code) }}
                        </td>
                        @php } @endphp
                        <td class=" ">{{ $employee->email }}</td>
                        <td class=" ">{{ $employee->phone_no }}</td>
                        <td class=" ">
                            {{ Commonfunctions::getNameINTable($dbname, 'employee_master', 'emp_name', 'emp_code', $employee->reporting_to) }}
                        </td>
                        <td class=" ">
                          @if ($employee->acedns=="Y")
                            Active
                          @else
                            Inactive
                          @endif
                          </td>
                        <td class="">
                          <a href="{{ url('/employee/'.$employee->emp_code) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/employee/'.$employee->emp_code.'/edit') }}"><i class="fa fa-edit"></i></a>
                          {{-- <a href="#"><i class="fa fa-trash-o"></i></a> --}}
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <!--<div class="panel-footer">
                  <div class="row">
                    <div class="col col-xs-8">
                      {{ $employees->links() }}
                    </div>
                  </div>
                </div>-->

              </div>
            </div>
          </div>

    </div>
    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Upload Employee CSV File</h4>
          </div>
          <div class="modal-body">

           {{ Form::open(array('url' => 'uploademployeefile','id'=>'upload_form','class'=>'form-signin','files'=>'true')) }}
           <div class="form-group">
             <label class="control-label col-md-3 col-sm-3 col-xs-12">Upload CSV</label>
             <div class="col-md-9 col-sm-9 col-xs-12">
               {{ Form::file('employee_csv_file', null, array(
                   'class' => 'form-control',
                   'id' => 'uploaded_file',
               )) }}
             </div>
             <div class="clearfix"></div>
           </div>
           <div class="form-group">
             <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
               {{ Form::submit('Submit',array(
                   'class' => 'btn btn-success',
                   'id' => 'upload_employee_form1',
                   'placeholder' => '',
               )) }}
             </div>
           </div>
           {{ Form::close() }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <!-- /page content -->
    @include('includes/footer')


@endsection
