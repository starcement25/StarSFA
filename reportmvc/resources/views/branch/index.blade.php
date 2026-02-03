@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Branch List</h2>
                <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">Upload Csv</button>
                <a href="{{url('/branch/create')}}" class="btn btn-success pull-right">Add New Branch</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Branch Code </th>
                        <th class="column-title" style="display: table-cell;">Branch Name </th>
                        <th class="column-title" style="display: table-cell;">Location </th>
                        <th class="column-title" style="display: table-cell;">State</th>
                        <th class="column-title" style="display: table-cell;">Email</th>
                        <th class="column-title" style="display: table-cell;">Status </th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach ($branchlistings as $key => $branch)
                      <tr class="even pointer">
                        <td class=" ">{{ $branch->dns_branch_code }}</td>
                        <td class=" ">{{ $branch->branch_name }}</td>
                        <td class=" ">{{ $branch->branch_location }}</td>
                        <td class=" ">{{ $branch->branch_state }}</td>
                        <td class=" ">{{ $branch->branch_email_id }}</td>
                        <td class=" ">
                          @if ($branch->acedns=="Y")
                            Active
                          @else
                            Inactive
                          @endif
                          </td>
                        <td class="">
                          <a href="{{ url('/branch/'.$branch->branch_code) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/branch/'.$branch->branch_code.'/edit') }}"><i class="fa fa-edit"></i></a>
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
                      {{ $branchlistings->links() }}
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

    </div>
    <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Upload Branch CSV File</h4>
        </div>
        <div class="modal-body">

         {{ Form::open(array('url' => 'uploadbranchfile','id'=>'upload_form','class'=>'form-signin','files'=>'true')) }}
         <div class="form-group">
           <label class="control-label col-md-3 col-sm-3 col-xs-12">Upload CSV</label>
           <div class="col-md-9 col-sm-9 col-xs-12">
             {{ Form::file('brach_csv_file', null, array(
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
                 'id' => 'upload_form1',
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
