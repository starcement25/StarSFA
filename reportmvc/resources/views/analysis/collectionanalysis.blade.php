@extends('layouts.default')

@section('main_container')
<script type="text/javascript">
function show_date_div(){
	document.getElementById("date_div").hidden = false;
}
function hide_date_div(){
	document.getElementById("date_div").hidden = true;
}
</script>
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Collection Analysis</h2>

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
												<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Choose Employee:
												</label>
												<div class="col-md-6 col-sm-6 col-xs-12">
													{{ Form::radio('duration', 'today',null,array('onClick'=>'hide_date_div()','id'=>'duration')) }}Today
													{{ Form::radio('duration', 'mtd',null,array('onClick'=>'hide_date_div()','id'=>'duration')) }}MTD
													{{ Form::radio('duration', 'custom',null,array('onClick'=>'show_date_div()','id'=>'duration')) }}Custom
												</div>
											</div>
											@php
											 if($type=='custom'){
												 $hide='';
											 }
											 else{
												 $hide='hidden';
											 }
											@endphp
											<div class="form-group" id="date_div" @php echo $hide; @endphp>
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
											var duration = $('input[name=duration]:checked', '#myForm').val();
											var start_date=$('#start_date').val();
											var end_date=$('#end_date').val();
											var image = "http://salesmpower.acedns.in/misreport/ajax-loader.gif";
                      $('#loading').html("<img src='"+image+"' />").show();
                      $.ajax({
                       url: '{{url("showcollectionanalysis")}}',
                       type: "post",
                         data: {
                            _token: CSRF_TOKEN,
                            employee :employee,
														duration :duration,
														start_date:start_date,
														end_date:end_date

                         },
                         success: function(resp) {
													 $('#loading').html("").hide();
                           $("#collection_analysis").html(resp);
                         }
                      });
                    }
										function zone_state(zone){
											$.ajax({
											 url: '{{url("statelisting")}}',
											 type: "GET",
												 data: {
														'type' :'state',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : zone

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function zone_district(district){
											$.ajax({
											 url: '{{url("districtlisting")}}',
											 type: "GET",
												 data: {
														'type' :'district',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : district

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function zone_hq(hq){
											$.ajax({
											 url: '{{url("hqlisting")}}',
											 type: "GET",
												 data: {
														'type' :'hq',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : hq

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function zone_designation(designation){
											$.ajax({
											 url: '{{url("designation")}}',
											 type: "GET",
												 data: {
														'type' :'designation',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : designation

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function zone_emp(zone){
											var state = $('#state').val();
											var district = $('#district').val();
											var designation = $('#designation').val();
											var hq = $('#hq').val();

											$.ajax({
											 url: '{{url("emplisting")}}',
											 type: "GET",
												 data: {
														'zone':zone,
														'state' : state,
														'hq':hq,
														'designation':designation,
														'district':district

												 },
												 success: function(resp) {
													 $("#emp_select_div").html(resp);
												 }
											});
										}
										function state_district(district){
											$.ajax({
											 url: '{{url("districtlisting")}}',
											 type: "GET",
												 data: {
														'type' :'district',
														'conditionfiledname':'state',
														'conditionfiledvalue' : district

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function state_hq(hq){
											$.ajax({
											 url: '{{url("hqlisting")}}',
											 type: "GET",
												 data: {
														'type' :'hq',
														'conditionfiledname':'state',
														'conditionfiledvalue' : hq

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function state_designation(designation){
											var ndesignation = encodeURIComponent(designation);
											$.ajax({
											 url: '{{url("designation")}}',
											 type: "GET",
												 data: {
														'type' :'designation',
														'conditionfiledname':'state',
														'conditionfiledvalue' : ndesignation

												 },
												 success: function(resp) {
													 $("#designation_select_div").html(resp);
												 }
											});
										}
										function state_emp(state){
											var zone = $('#zone').val();
											var district = $('#district').val();
											var designation = $('#designation').val();
											var hq = $('#hq').val();

											$.ajax({
											 url: '{{url("emplisting")}}',
											 type: "GET",
												 data: {
														'zone':zone,
														'state' : state,
														'hq':hq,
														'designation':designation,
														'district':district

												 },
												 success: function(resp) {
													 $("#emp_select_div").html(resp);
												 }
											});
										}
										function district_hq(hq){
											$.ajax({
											 url: '{{url("hqlisting")}}',
											 type: "GET",
												 data: {
														'type' :'hq',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : hq

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function district_designation(designation){
											$.ajax({
											 url: '{{url("designation")}}',
											 type: "GET",
												 data: {
														'type' :'designation',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : designation

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function district_emp(district){
											var zone = $('#zone').val();
											var state = $('#state').val();
											var designation = $('#designation').val();
											var hq = $('#hq').val();

											$.ajax({
											 url: '{{url("emplisting")}}',
											 type: "GET",
												 data: {
														'zone':zone,
														'state' : state,
														'hq':hq,
														'designation':designation,
														'district':district

												 },
												 success: function(resp) {
													 $("#emp_select_div").html(resp);
												 }
											});
										}
										function hq_designation(designation){
											$.ajax({
											 url: '{{url("designation")}}',
											 type: "GET",
												 data: {
														'type' :'district',
														'conditionfiledname':'zone',
														'conditionfiledvalue' : designation

												 },
												 success: function(resp) {
													 $("#state_select_div").html(resp);
												 }
											});
										}
										function hq_emp(hq){
											var zone = $('#zone').val();
											var state = $('#state').val();
											var designation = $('#designation').val();
											var district = $('#district').val();

											$.ajax({
											 url: '{{url("emplisting")}}',
											 type: "GET",
												 data: {
														'zone':zone,
														'state' : state,
														'hq':hq,
														'district':district,
														'designation':designation

												 },
												 success: function(resp) {
													 $("#emp_select_div").html(resp);
												 }
											});
										}
										function designation_emp(designation){
											var zone = $('#zone').val();
											var state = $('#state').val();
											var hq = $('#hq').val();
											var district = $('#district').val();

											$.ajax({
											 url: '{{url("emplisting")}}',
											 type: "GET",
												 data: {
														'zone':zone,
														'state' : state,
														'hq':hq,
														'district':district,
														'designation':designation

												 },
												 success: function(resp) {
													 $("#emp_select_div").html(resp);
													 $("#employee").multiselect({
										            includeSelectAllOption: true,
										            maxHeight: 200,
										            enableFiltering: true
										        });
												 }
											});
										}
										</script>


                  </div>
                </div>
              </div>

              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
											<div id="loading" style="display:none;padding-left:500px"></div>
                      <table class="table table-striped jambo_table bulk_action" id="collection_analysis">



                      </table>
                    </div>


                  </div>
                </div>
              </div>

    </div>
		<script type="text/javascript">
					function show_invoice(receipt_id,customer_name){
						$.ajax({
							 url: '{{url("invcollectionanalysis")}}',
							 type: "GET",
							 data: {
									 'receipt_id' : receipt_id,
									 'customer_name' :customer_name
							 },
							 success: function(resp) {
								 $("#getCode").html(resp);
	               $("#getCodeModal").modal('show');
							 }
					 });
         }

		</script>
		<div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		   <div class="modal-dialog modal-lg">
		      <div class="modal-content">
		       <div class="modal-header">
		         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		         <h4 class="modal-title" id="myModalLabel">Invoice Details </h4>
		       </div>
		       <div class="modal-body" id="getCode" style="overflow-x: scroll;">
		          //ajax success content here.
		       </div>
		    </div>
		   </div>
		 </div>
    <!-- /page content -->
    @include('includes/footer')

@endsection
