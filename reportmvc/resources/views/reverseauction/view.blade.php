@extends('layouts.default')

@section('main_container')

  <!--link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /-->

    <!-- page content -->
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <div class="right_col" role="main">
       <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
               <div class="x_title">
                 <h2>Released Rate</h2>
                  <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    @php
                    use App\Helpers\Reverseauction;
                    $dbname=Session::get('DBNAME');
                    @endphp
                    {{ Form::open(array('url' => '','class'=>'form-horizontal form-label-left')) }}
                    @php
                        $onclick = "state_zone(this.value);";
                     @endphp
                     <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">State
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          {{ Form::select('state', [null=>'Please Select'] + Reverseauction::getcustomerstate($dbname), null, ['id'=>'state','class'=> 'form-control','onchange'=>$onclick]) }}
                        </div>
                      </div>
                        <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Zone
                        </label>
                            <div class="col-md-3 col-sm-3 col-xs-12" id="zone_select_div"></div>
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
                   </div>
                 </div>  
                 <script type="text/javascript">
				 function state_zone(state){
				  $.ajax({
				   url: 'http://salesmpower.acedns.in/reportmvc/zonelisting',
				   type: "GET",
					 data: {
						'type' :'zone',
						'conditionfiledname':'state',
						'conditionfiledvalue' : state
				
					 },
					 success: function(resp) {
					   $("#zone_select_div").html(resp);
					 }
				  });
				}
				  function frmSub(){
                      var CSRF_TOKEN = $('input[name=_token]').val();
                      var state=$('#state').val();
                      var zone=$('#zone').val();
                      var image = "http://salesmpower.acedns.in/misreport/ajax-loader.gif";

                      $('#loading').html("<img src='"+image+"' />").show();
                      $.ajax({
                       url: '{{url("showreleaseratereport")}}',
                       type: "post",
                         data: {
                            _token: CSRF_TOKEN,
                            state :state,
							zone :zone
                         },
                         success: function(resp) {
                           $('#loading').html("").hide();
                           $("#release_rate_report").html(resp);
						   $('#export_display').show();
                         }
                      });
                    }
					 
				 </script>   

                @if(session()->has('message'))
                    <div class="alert alert-success" align="center" style="font-weight:bold;">
                        {{ session()->get('message') }}
                    </div>
                @endif
                @if (count($errors) > 0)
                <div class="alert alert-danger" align="center" style="font-weight:bold;">
                    <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                    </ul>
                </div>
                @endif
              </div>  
             <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                      <div id="loading" style="display:none;padding-left:500px"></div>     
                <table width="90%" class="table table-striped jambo_table bulk_action" border="1"  align="center"  id="release_rate_report">
                </table>
                 <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="center" id="export_display" style="display:none;">
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success ratebtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} 
                        </div>
                </div>
                  </div>
                </div>
              </div>
              <script type="text/javascript">
              $(".ratebtn").on("click", function(e){
						var dt = new Date();
						var day = dt.getDate();
						var month = dt.getMonth() + 1;
						var year = dt.getFullYear();
						var hour = dt.getHours();
						var mins = dt.getMinutes();
						var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
						
						var a = document.createElement('a');
						//getting data from our div that contains the HTML table
						var data_type = 'data:application/vnd.ms-excel';
						var table_div = document.getElementById('release_rate_report');
						var table_html = table_div.outerHTML.replace(/ /g, '%20');
						a.href = data_type + ', ' + table_html;
						//setting the file name
						a.download = 'Zonewise rate data' + postfix + '.xls';
						//triggering the function
						a.click();
						//just in case, prevent default behaviour
						e.preventDefault();
					});
		   </script>			
        </form>
   </div> 
    <!-- /page content -->
    @include('includes/footer')
@endsection
