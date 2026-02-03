@extends('layouts.default')

@section('main_container')

@php
$cnt=explode('|',$attncnt);
$totalordercnt=explode('|',$totalorder);
$totalcusorderprice=explode('|',$totalcusorderpricecnt);
$today = date('Y-m-d');
$startdate = date('Y-m-d', strtotime('first day of this month', strtotime($today)));
$days = ((strtotime($today) - strtotime($startdate)) / (60 * 60 * 24)+1);
$totcust=(20*$days)*$cnt[1];
@endphp
<style>
#load{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background:url("http://salesmpower.acedns.in/ajax-loader.gif") no-repeat center center
    rgba(0,0,0,0.25)
}
</style>
    <!-- page content -->
    <div id="load"></div>
    <div class="right_col" role="main" id="contents">
      <div class="row tile_count">
            <div class="col-md-4 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total  Attandence MTD and Employee</span>
              <div class="count">{{$cnt[1]}}</div>
              <span class="count_bottom">{{$cnt[0]}}</span>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Productive Customer MTD</span>
              <div class="count">{{$totalordercnt[0]}}</div>
              <span class="count_bottom">{{$totalordercnt[1]}} ||</span>
              <span class="count_bottom">{{$totcust}}</span>

            </div>
            <div class="col-md-4 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Customer Volume and Value</span>
              <div class="count">{{$totalcusorderprice[0]}}</div>
              <span class="count_bottom">{{$totalcusorderprice[1]}} ||</span>
              <span class="count_bottom">{{round($totalcusorderprice[2])}}</span>
            </div>

      </div>
      <div class="row">
          <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                      <div class="x_content">
                        <div class="dashboard-widget-content text-center">
                          <input type="hidden" id="total_emp" name="total_emp" value="{{$cnt[0]}}">
                          <input type="hidden" id="attandence_user" name="attandence_user" value="{{$cnt[1]}}">
                          <div id="chart-container">A user attendence chat show here!</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <div class="x_panel">
                        <div class="x_content">
                          <div class="dashboard-widget-content text-center">
                            <input type="hidden" id="total_order_mnth" name="total_order_mnth" value="{{$totalordercnt[0]}}">
                            <input type="hidden" id="total_visted" name="total_visted" value="{{$totalordercnt[1]}}">
                            <input type="hidden" id="total_cal_cus" name="total_cal_cus" value="{{$totcust}}">
                            <div id="chart-container1">Productivity chat show here!</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="col-md-12 col-sm-12col-xs-12">
              <div class="row">
                      <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                          <div class="x_content">
                            <div class="dashboard-widget-content text-center">
                              <input type="hidden" id="total_cust" name="total_cust" value="{{$totalcusorderprice[0]}}">
                              <input type="hidden" id="total_qty" name="total_qty" value="{{$totalcusorderprice[1]}}">
                              <input type="hidden" id="total_price" name="total_price" value="{{$totalcusorderprice[2]}}">
                              <div id="chart-container2">Total Customer Order QTY And Cost Chart</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
              </div>

        </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')
    <script type="text/javascript">
      document.onreadystatechange = function () {
        var state = document.readyState
        if (state == 'interactive') {
            document.getElementById('contents').style.visibility="hidden";
        } else if (state == 'complete') {
           setTimeout(function(){
              document.getElementById('interactive');
              document.getElementById('load').style.visibility="hidden";
              document.getElementById('contents').style.visibility="visible";
           },1000);
        }
      }
    </script>

@endsection
