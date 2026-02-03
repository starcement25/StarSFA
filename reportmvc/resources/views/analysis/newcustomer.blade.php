@extends('layouts.default')

@section('main_container')
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12col-xs-12">
      <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="dashboard-widget-content text-center">
                      <input type="hidden" name="newcustjeson" id="newcustjeson" value="{{$newcust}}">
                      <div id="newcustomer">New Customer Chart</div>
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
