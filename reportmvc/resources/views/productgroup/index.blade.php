@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Product Group List</h2>
                <a href="{{url('/productgroup/create')}}" class="btn btn-success pull-right">Add New Product Group</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action" id="emp_table">
                    <thead>
                      <tr class="headings">

                        <th class="column-title" style="display: table-cell;">Group Name </th>
                        <th class="column-title" style="display: table-cell;">Varetical Name </th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                  <tbody>
                      @foreach ($productgroups as $key => $productgroup)
                      <tr class="even pointer">

                        <td class=" ">{{ $productgroup->product_group_name	 }}</td>
                        <td class=" ">{{ $productgroup->vertical_value }}</td>
                        <td class="">
                          <a href="{{ url('/productgroup/'.$productgroup->product_group_code) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/productgroup/'.$productgroup->product_group_code.'/edit') }}"><i class="fa fa-edit"></i></a>
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
                      {{ $productgroups->links() }}
                    </div>
                  </div>
                </div>-->

              </div>
            </div>
          </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
