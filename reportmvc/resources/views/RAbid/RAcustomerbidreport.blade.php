@extends('layouts.default')
@section('main_container')
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>CUSTOMER BID REPORT</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'RAcustomerbidreport','class'=>'form-horizontal form-label-left','id'=>'customerbidreportform')) }}
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Choose Customer:</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          {{ Form::select('customer_list[]', $customerlist, null, ['class'=> 'form-control multiselect-ui','multiple'=>'multiple']) }}
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Choose Date:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          {{ Form::text('bid_date', null, array(
          								    'class' => 'form-control col-md-7 col-xs-12',
          								    'id' => 'bid_date',
          								    'placeholder' => 'Bid Date',
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
                                      'class' => 'btn btn-success pjpbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} -->
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
    @include('includes/footer')
@endsection
