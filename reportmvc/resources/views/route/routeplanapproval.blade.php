@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Route;
use App\Helpers\Commonfunctions;
$dbname=Session::get('DBNAME');
@endphp

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>ATTRIBUTES SELECTION</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'routplanapproval','name'=>'frmSearch','class'=>'form-horizontal form-label-left')) }}

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Choose Employee:
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          {{ Form::select('emp_list', [null=>'Please Select'] +$emplist, null, ['class'=> 'form-control','onChange'=>'javascript:document.frmSearch.submit();']) }}
                        </div>
                      </div>
                    {{ Form::close() }}
                  </div>
                </div>
              </div>
              @php if($reoprtdatas) { @endphp
              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Sl no.</th>
                            <th class="column-title"></th>
                            <th class="column-title">Visit Date</th>
                            <th class="column-title">Route</th>
                          </tr>
                        </thead>
                        <?php
                        $visit_date_array=array();
                        $visit_date_codehints_array=array();
                        $sl_no_route_plan=0;
                        $route_visit_status_prev_array=array();
                        //print_r($reoprtdatas);exit;
                        $i=1;
                        foreach ($reoprtdatas as $key => $reoprtdata) {

                              $route_code=$reoprtdata->route_code;
                              $route_name=Commonfunctions::getNameINTable($dbname, 'route_master', 'route_name', 'route_code', $route_code);
                              $visit_date=$reoprtdata->visit_date;
                          ?>
                           <tr>
                             <td style="width:10%">{{$i}}</td>
                             <td style="width:10%"><input type="checkbox" name=""></td>
                             <td style="width:20%">{{$visit_date}}</td>
                             <td style="width:60%">{{$route_name}}</td>
                           </tr>

                        <?php
                        $i++;
                        }
                        ?>


                      </table>
                    </div>


                  </div>
                </div>
              </div>
              @php } @endphp
    </div>
    <!-- /page content -->
    @include('includes/footer')

@endsection
