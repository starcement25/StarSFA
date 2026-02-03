@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Daily Activity Analysis</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    @php
										use App\Helpers\Commonfunctions;
										$dbname=Session::get('DBNAME');
										$filteravilable=explode(',',$avialblefilter);
										@endphp

										{{ Form::open(array('url' => '','class'=>'form-horizontal form-label-left','id'=>'myForm')) }}

											@php
											 if(in_array('Area wise',$filteravilable)){
													if(in_array('State',$filteravilable))
														$onclick = "zone_state(this.value);";
													else if(in_array('District',$filteravilable))
														$onclick = "zone_district(this.value);";
													else if(in_array('HQ',$filteravilable))
														$onclick = "zone_hq(this.value);";
													else if(in_array('Designation',$filteravilable))
														$onclick = "zone_designation(this.value);";
													else
														$onclick = "zone_emp(this.value);";
											@endphp
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Area wise
												</label>
												<div class="col-md-6 col-sm-6 col-xs-12">
													{{ Form::select('zone', [null=>'Please Select'] + Commonfunctions::getListTable($dbname, 'employee_master', 'zone'), null, ['id'=>'zone','class'=> 'form-control','onchange'=>$onclick]) }}
												</div>
											</div>
											@php
											}
											if(in_array('State',$filteravilable))
											{
											@endphp
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">State:
												</label>
												@php
												 if(in_array('Area wise',$filteravilable)){
												@endphp
														<div class="col-md-6 col-sm-6 col-xs-12" id="state_select_div"></div>
												@php
												}
												else{
													if(in_array('District',$filteravilable))
														$onclick = "state_district(this.value);";
													else if(in_array('HQ',$filteravilable))
														$onclick = "state_hq(this.value);";
													else if(in_array('Designation',$filteravilable))
														$onclick = "state_designation(this.value);";
													else
														$onclick = "state_emp(this.value);";
												@endphp
														<div class="col-md-6 col-sm-6 col-xs-12">
															{{ Form::select('state', [null=>'Please Select'] + Commonfunctions::getListTable($dbname, 'employee_master', 'zone'), null, ['id'=>'state','class'=> 'form-control','onchange'=>$onclick]) }}
														</div>
													@php
													}
													@endphp
											</div>
											@php
											}
											if(in_array('District',$filteravilable))
											{
											@endphp
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">District
												</label>
											@php
													if(in_array('Area wise',$filteravilable) || in_array('State',$filteravilable)){
											@endphp
																 <div class="col-md-6 col-sm-6 col-xs-12" id="district_select_div"></div>
											@php
											 }
											else{
												if(in_array('HQ',$filteravilable))
													$onclick = "district_hq(this.value);";
												else if(in_array('Designation',$filteravilable))
													$onclick = "district_designation(this.value);";
												else
													$onclick = "district_emp(this.value);";
											@endphp
													<div class="col-md-6 col-sm-6 col-xs-12">
														{{ Form::select('district', [null=>'Please Select'] + Commonfunctions::getListTable($dbname, 'employee_master', 'District'), null, ['id'=>'district','class'=> 'form-control','onchange'=>$onclick]) }}
													</div>
											@php
												}
											@endphp
											</div>
											@php
											}
											if(in_array('HQ',$filteravilable))
											{
											@endphp
												<div class="form-group">
													<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">HQ
													</label>
													@php
													if(in_array('Area wise',$filteravilable) || in_array('State',$filteravilable) || in_array('District',$filteravilable)){
													@endphp
													<div class="col-md-6 col-sm-6 col-xs-12" id="hq_select_div"></div>
													@php
													 }
													else{
														if(in_array('Designation',$filteravilable))
															$onclick = "hq_designation(this.value);";
														else
															$onclick = "hq_emp(this.value);";
													@endphp
															<div class="col-md-6 col-sm-6 col-xs-12">
																{{ Form::select('hq', [null=>'Please Select'] + Commonfunctions::getListTable($dbname, 'employee_master', 'HQ'), null, ['id'=>'hq','class'=> 'form-control','onchange'=>$onclick]) }}
															</div>
													@php
														}
													@endphp
												</div>
											@php
											}
											if(in_array('Designation',$filteravilable))
											{
											@endphp
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Designation:
												</label>
												@php
												if(in_array('Area wise',$filteravilable) || in_array('State',$filteravilable) || in_array('District',$filteravilable) || in_array('HQ',$filteravilable)){
												@endphp
												<div class="col-md-6 col-sm-6 col-xs-12" id="designation_select_div"></div>
												@php
												 }
												else{
													$onclick = "designation_emp(this.value);";
												@endphp
												<div class="col-md-6 col-sm-6 col-xs-12">
													{{ Form::select('designation', [null=>'Please Select'] + Commonfunctions::getListTable($dbname, 'employee_master', 'designation'), null, ['id'=>'designation','class'=> 'form-control','onchange'=>$onclick]) }}
												</div>
												@php } @endphp
											</div>
											@php } @endphp

											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Choose Employee:
												</label>
												@php
												if(in_array('Area wise',$filteravilable) || in_array('State',$filteravilable) || in_array('District',$filteravilable) || in_array('HQ',$filteravilable) || in_array('Designation',$filteravilable) ){
												@endphp
												<div class="col-md-6 col-sm-6 col-xs-12" id="emp_select_div"></div>
													@php } else { @endphp
												<div class="col-md-6 col-sm-6 col-xs-12">
													{{ Form::select('emp', [null=>'Please Select','all'=>'All'] +$emplist, null, ['id'=>'emp','class'=> 'form-control']) }}
												</div>
													@php } @endphp
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
                          {{ Form::button('Submit',array(
               								    'class' => 'btn btn-success',
               								    'id' => '',
               								    'placeholder' => '',
                                  'onclick'=>'frmSub()',
               								)) }}
                        </div>
                      </div>

                    {{ Form::close() }}
                    <script type="text/javascript">
                    function frmSub(){
                      var CSRF_TOKEN = $('input[name=_token]').val();
                      var employee=$('#employee').val();
                      var startdate=$('#start_date').val();
                      var enddate=$('#end_date').val();
                      var image = "http://salesmpower.acedns.in/misreport/ajax-loader.gif";
                      $('#loading').html("<img src='"+image+"' />").show();
                      $.ajax({
                       url: '{{url("dailyactivityanalysis")}}',
                       type: "post",
                         data: {
                            _token: CSRF_TOKEN,
                            employee :employee,
                            startdate :startdate,
                            enddate   : enddate
                         },
                         success: function(resp) {
                           $('#loading').html("").hide();
                           $("#dailyactivityanalysislists").html(resp);
                         }
                      });
                    }
                    </script>
                    @include('includes/dynamicfilterscript')
                  </div>
                </div>
              </div>

              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                      <div id="loading" style="display:none;padding-left:500px"></div>
                      <table class="table table-striped jambo_table bulk_action" id="dailyactivityanalysislists">

                      </table>
                    </div>


                  </div>
                </div>
              </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')

@endsection
