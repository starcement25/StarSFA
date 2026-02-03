@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Sale Register</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'saleregister','class'=>'form-horizontal form-label-left','id'=>'saleregister')) }}


                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">State:
                      </label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                          {{ Form::select('state', [null=>'Please Select State']+$statelists, null, ['class'=> 'form-control']) }}
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Sale Type:
                      </label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        {{ Form::select('cust_type', [
                           '' => '--Select--',
                           'primary' => 'Primary',
                           'secondary' => 'Secondary'],null,['class'=> 'form-control']
                        ) }}
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
                        <label class="col-md-3 col-sm-3 col-xs-12" for="last-name" style="width:3%">To:
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
                                          'class' => 'btn btn-success btnreport',
                                          'id' => '',
                                          'placeholder' => '',

                              )) }}
                        </div>
                      </div>

                    {{ Form::close() }}
                    <script type="text/javascript">
                    $(".btnreport").on("click", function(e){
                        e.preventDefault();
                        $('#saleregister').attr('action', "{{ url('/csvsaleregister') }}").submit();
                    });
                    </script>
                  </div>
                </div>
              </div>
              @php if($saleregister) { @endphp
              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        @php  echo $saleregister; @endphp
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
