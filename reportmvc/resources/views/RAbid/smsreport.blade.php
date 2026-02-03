@extends('layouts.default')
@section('main_container')
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>SMS Log</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'smsreport','class'=>'form-horizontal form-label-left','id'=>'smsreportform')) }}
                    	<div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Choose Date:
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          {{ Form::text('start_date', null, array(
          								    'class' => 'form-control col-md-7 col-xs-12',
          								    'id' => 'start_date',
                                            'format' => 'DD-MM-YYYY',
          								    'placeholder' => 'Sms Date',
                                            'autocomplete' => 'off',
          								)) }}
                        </div>
                      </div>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          {{ Form::submit('Submit',array(
               								    'class' => 'btn btn-success',
               								    'id' => '',
               								    'placeholder' => '',
               								)) }}
                              <!--{{ Form::button('Export',array(
                                      'class' => 'btn btn-success bidbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} !-->
                        </div>

                      </div>
                    {{ Form::close() }}
                  </div>
                </div>
              </div>
              @php if($reoprtdata) { @endphp
              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                    	@if(!empty($successMsg))
                          <div class="alert alert-success"> {{ $successMsg }}</div>
                        @endif
                    <form name="update_bid_status" method="POST" action="/reportmvc/bidstatuschange">
                     {{ csrf_field() }}
                      <table class="table table-striped jambo_table bulk_action" id="display"  border="1">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Date </th>
                            <th class="column-title">Dns customer code</th>
                            <th class="column-title">Customer name</th>
                             <th class="column-title">Phone no</th>
                            <th class="column-title">Route</th>
                            <th class="column-title">State</th>
                            <th class="column-title">Sms Body </th>
                            <th class="column-title">Sms type</th>
                            <th class="column-title">Sms time</th>
                            <th class="column-title">Response</th>
                          </tr>
                        </thead>

                        <tbody>
                         <?php //echo "<pre>";print_r($reoprtdata);exit; ?>
                          @foreach ($reoprtdata as $key => $report)
                          <tr class="even pointer">
                            <td>{{$report->sms_date}}</td>
                            <td>{{$report->dns_customer_code}}</td>
                            <td>{{$report->customer_name}}</td>
                            <td>{{$report->phone_no}}</td>
                            <td>{{$report->route_name}}</td>
                            <td>{{$report->state}}</td>
                            <td>{{$report->sms_body}}</td>
                            <td align="right">{{$report->sms_type}}</td>
                            <td align="right">{{$report->sms_time}}</td>
                            <td align="right">{{$report->response}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <form>
                      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="center">
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success smsbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} 
                        </div>
                    </div>
                  </div>
                </div>
              </div>
                   
              @php } @endphp
    </div>
              <script type="text/javascript">
              $(".smsbtn").on("click", function(e){
                  e.preventDefault();
                  $('#smsreportform').attr('action', "{{ url('/smsreportexportcsv') }}").submit();
              });
              </script>

    @include('includes/footer')
@endsection
