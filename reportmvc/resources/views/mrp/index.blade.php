@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Mrp List</h2>
                <a href="{{url('/mrp/create')}}" class="btn btn-success pull-right">Add New Mrp</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action" id="emp_table">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Mrp  Code </th>
                        <th class="column-title" style="display: table-cell;">Destination Code</th>
                        <th class="column-title" style="display: table-cell;">MRP</th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                  <tbody>

                      @foreach ($mrplists as $key => $mrp)
                      <tr class="even pointer">
                        <td class=" ">{{ $mrp->mrp_code }}</td>
                        <td class=" ">{{ $mrp->destination_code }}</td>
                        <td class=" ">{{ $mrp->mrp }}</td>
                        <td class="">
                          <a href="{{ url('/mrp/'.$mrp->mrp_code) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/mrp/'.$mrp->mrp_code.'/edit') }}"><i class="fa fa-edit"></i></a>
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
                      {{ $mrplists->links() }}
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
