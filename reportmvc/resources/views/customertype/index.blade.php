@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Customer Type List</h2>
                <a href="{{url('/customertype/create')}}" class="btn btn-success pull-right">Add New Customer Type</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">ID </th>
                        <th class="column-title" style="display: table-cell;">Prefix </th>
                        <th class="column-title" style="display: table-cell;">Name </th>
                        <th class="column-title" style="display: table-cell;">Status</th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                  <tbody>
                      @foreach ($customertypelists as $key => $customertype)
                      <tr class="even pointer">
                        <td class=" ">{{ $customertype->id }}</td>
                        <td class=" ">{{ $customertype->prefix }}</td>
                        <td class=" ">{{ $customertype->name }}</td>
                        <td class=" ">
                          @if ($customertype->acedns=="Y")
                            Active
                          @else
                            Inactive
                          @endif
                          </td>
                        <td class="">
                          <a href="{{ url('/customertype/'.$customertype->id) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/customertype/'.$customertype->id.'/edit') }}"><i class="fa fa-edit"></i></a>
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
                      {{ $customertypelists->links() }}
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
