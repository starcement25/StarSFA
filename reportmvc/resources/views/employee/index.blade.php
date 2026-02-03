@extends('layouts.default')

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Employee Listing </h2>

                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content">
                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">

                            <th class="column-title" style="display: table-cell;">Emp Id </th>
                            <th class="column-title" style="display: table-cell;">Emp Name </th>
                            <th class="column-title" style="display: table-cell;">Email </th>
                            <th class="column-title" style="display: table-cell;">Phone </th>
                            <th class="column-title" style="display: table-cell;">HQ </th>
                            <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                            </th>
                            <th class="bulk-actions" colspan="7" style="display: none;">
                              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt">1 Records Selected</span> ) <i class="fa fa-chevron-down"></i></a>
                            </th>
                          </tr>
                        </thead>

                        <tbody>
                          @foreach ($users as $key => $user)
                          <tr class="even pointer">
                            <td class=" ">{{ $user->emp_code }}</td>
                            <td class=" ">{{ $user->emp_name }}</td>
                            <td class=" ">{{ $user->email }}</td>
                            <td class=" ">{{ $user->phone_no }}</td>
                            <td class=" ">{{ $user->HQ }}</td>
                            <td class=" last"><a href="#">View</a>
                            </td>
                          </tr>
                          @endforeach


                        </tbody>
                      </table>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col col-xs-8">
                          {{ $users->links() }}
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
