@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Customer List</h2>
                <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">Upload Csv</button>
                <a href="{{url('/customer/create')}}" class="btn btn-success pull-right">Add New Customer</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Customer  Code </th>
                        <th class="column-title" style="display: table-cell;">Customer Name</th>
                        <th class="column-title" style="display: table-cell;">Customer Phone Number</th>
                        <th class="column-title" style="display: table-cell;">Assign Employee</th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                  <tbody>

                      @foreach ($customerlists as $key => $customerlist)
                      @php
                        $custcode=str_replace('/', '',$customerlist->customer_code);
                      @endphp
                      <tr class="even pointer">
                        <td class=" ">{{ $customerlist->dns_customer_code }}</td>
                        <td class=" ">{{ $customerlist->customer_name }}</td>
                        <td class=" ">{{ $customerlist->phone_no }}</td>
                        <td class=" ">{{ $customerlist->emp_code }}</td>
                        <td class="">
                          <a href="{{ url('/customer/'.$custcode) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/customer/'.$custcode.'/edit') }}"><i class="fa fa-edit"></i></a>
                          {{-- <a href="#"><i class="fa fa-trash-o"></i></a> --}}
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="panel-footer">
                  <div class="row">
                    <div class="col col-xs-8">
                      {{ $customerlists->links() }}
                    </div>
                  </div>
                </div>

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
            <h4 class="modal-title">Upload Customer CSV File</h4>
          </div>
          <div class="modal-body">

           {{ Form::open(array('url' => 'uploadcustomerfile','id'=>'upload_form','class'=>'form-signin','files'=>'true')) }}
           <div class="form-group">
             <label class="control-label col-md-3 col-sm-3 col-xs-12">Upload CSV</label>
             <div class="col-md-9 col-sm-9 col-xs-12">
               {{ Form::file('customer_csv_file', null, array(
                   'class' => 'form-control',
                   'id' => 'uploaded_cus_file',
               )) }}
             </div>
             <div class="clearfix"></div>
           </div>
           <div class="form-group">
             <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
               {{ Form::submit('Submit',array(
                   'class' => 'btn btn-success',
                   'id' => 'upload_customer_form1',
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
