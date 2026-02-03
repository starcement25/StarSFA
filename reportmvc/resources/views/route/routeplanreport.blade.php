@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>PJP REPORT</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'routplanreport','class'=>'form-horizontal form-label-left','id'=>'pjpform')) }}

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Choose Employee:
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          {{ Form::select('emp_list[]', $emplist, null, ['class'=> 'form-control multiselect-ui','multiple'=>'multiple']) }}
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">From:
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">

                          {{ Form::text('start_date', null, array(
          								    'class' => 'form-control col-md-7 col-xs-12',
          								    'id' => 'start_date',
          								    'placeholder' => 'Start Date',
          								)) }}
                        </div>
                        <label class="col-md-3 col-sm-3 col-xs-12" for="last-name" style="width:10%">To:
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          {{ Form::text('end_date', null, array(
          								    'class' => 'form-control col-md-7 col-xs-12',
          								    'id' => 'end_date',
          								    'placeholder' => 'End Date',
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
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success pjpbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }}
                        </div>

                      </div>


                    {{ Form::close() }}
                  </div>
                </div>
              </div>
              <script type="text/javascript">
              $(".pjpbtn").on("click", function(e){
                  e.preventDefault();
                  $('#pjpform').attr('action', "{{ url('/pjpexportcsv') }}").submit();
              });

              </script>
              @php if($reoprtdata) { @endphp
              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action" id="display">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Employee Name </th>
                            <th class="column-title">Date </th>
                            <th class="column-title">Route Name</th>
                          </tr>
                        </thead>

                        <tbody>
                         <?php //echo "<pre>";print_r($reoprtdata);exit; ?>
                          @foreach ($reoprtdata as $key => $report)

                          <tr class="even pointer">
                            <td style="width:30%">{{$report->emp_name}}</td>
                            <td style="width:30%">{{date('d-m-Y',strtotime($report->visit_date))}}</td>
                            <td style="width:30%">{{$report->route_name}}</td>

                          </tr>
                          @endforeach

                        </tbody>
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
