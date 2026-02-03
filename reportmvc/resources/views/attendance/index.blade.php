@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Attendence;
$dbname=Session::get('DBNAME');
@endphp

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE" type="text/javascript"></script>
    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Attendance Tracker</h2>

                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Sl</th>
                        <th class="column-title" style="display: table-cell;">Emp code</th>
                        <th class="column-title" style="display: table-cell;">Name </th>
                        <th class="column-title" style="display: table-cell;">HQ</th>
                        <th class="column-title" style="display: table-cell;">Days on the Field</th>
                        <th class="column-title" style="display: table-cell;">Details </th>

                      </tr>
                    </thead>

                    <tbody>
                      @php $i=0;@endphp
                      @foreach ($attendancelists as $key => $attendancelist)
                      @php $i++;@endphp
                      <tr class="even pointer">
                        <td class=" ">@php echo $i @endphp</td>
                        <td class=" ">{{ $attendancelist->emp_code }}</td>
                        <td class=" ">{{ $attendancelist->emp_name }}</td>
                        <td class=" ">{{ $attendancelist->HQ }}</td>
                        <td class=" ">{{ Attendence::getCheckouttime($dbname,$attendancelist->emp_code) }}</td>
                        <td class=" ">
                           <a href="{{url('/location/'.$attendancelist->trans_id.'/'.$attendancelist->emp_code)}}" >location</a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>



              </div>
            </div>
          </div>

    </div>
  
    <!-- /page content -->
    @include('includes/footer')


@endsection
