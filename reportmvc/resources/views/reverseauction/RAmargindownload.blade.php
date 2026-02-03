@extends('layouts.default')
@section('main_container')
@php
 use App\Helpers\Reverseauction;
 $dbname=Session::get('DBNAME');
 $RAmarginlist=Reverseauction::RAmargindownloadreport($dbname);
 $hour=gmdate('H',strtotime('+330 minute'));
 $minute=gmdate('i',strtotime('+330 minute'));
 $second=gmdate('s',strtotime('+330 minute'));
 $currenttime=$hour.':'.$minute.':'.$second;
@endphp
    <!-- page content -->
    <div class="right_col" role="main" >
              <div class="col-md-12 col-sm-12 col-xs-12" >
                <div class="x_panel">
                	<div class="x_title">
                    <h2>RA Zone wise margin</h2>
                    <div class="clearfix"></div>
                        @if(!empty($successMsg))
                          <div class="alert alert-success"> {{ $successMsg }}</div>
                        @endif
                  </div>
                    @php if($RAmarginlist) { @endphp
                  <div class="x_content">
                    <div class="table-responsive">
                    <form name="RAmargin" id="RAmargin" method="POST" action="">
                     {{ csrf_field() }}
                      <table class="table table-striped jambo_table bulk_action" id="display"  border="1" width="60%">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Date of Upload</th>
                            <th class="column-title">State code</th>
                            <th class="column-title">Product code</th>
                            <th class="column-title">Product Group</th>
                            <th class="column-title">Pack size(BP/CP)</th>
                            <th class="column-title">TOTAL/MT</th>
                            <th class="column-title">TOTAL/CASE</th>
                            <th class="column-title">Vertical Value</th>
                            <th class="column-title">Zone</th>
                          </tr>
                        </thead>
                        <tbody>
                         <?php //echo "<pre>";print_r($reoprtdata);exit; ?>
                          @foreach ($RAmarginlist as $key => $RAmarginval)
                           @php
                           if($RAmarginval->oil_group =='')
                            {
                           		$oil_group_string=Reverseauction::getProductGroupPack($dbname,$RAmarginval->dns_prod_code);
                                $oil_group_string_parts=explode("#",$oil_group_string);
                                $oil_group=$oil_group_string_parts[0];
                                $oil_type=$oil_group_string_parts[1];
                            }
                            else
                            {
                            	$oil_group=$RAmarginval->oil_group;
                                $oil_type=$RAmarginval->oil_type;
                            }
                           @endphp
                          <tr class="even pointer" width="60%">
                            <td>{{$RAmarginval->date_upload}}</td>
                            <td>{{$RAmarginval->state_code}}</td>
                            <td>{{$RAmarginval->dns_prod_code}}</td>
                            <td>{{$oil_group}}</td>
                            <td>{{$oil_type}}</td>
                            <td>{{$RAmarginval->margin_cost_ton}}</td>
                             <td>{{$RAmarginval->margin_cost}}</td>
                            <td>{{$RAmarginval->vertical_value}}</td>
                            <td>{{$RAmarginval->zone}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <form>
                      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="center">
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success marginbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} 
                        </div>
                    </div>
                  </div>
                    @php }else{ @endphp
                    <table class="table table-striped jambo_table bulk_action" id="display"  border="1">
                        <tbody>
                           <tr><td align="center">No records found.</td>
                          </tr>
                        </tbody>
                  </table> 
                   @php }@endphp       
                </div>
              </div>
                   
    </div>
              <script type="text/javascript">
			   $(".marginbtn").on("click", function(e){
                  e.preventDefault();
                  $('#RAmargin').attr('action', "{{ url('/RAmarginexportcsv') }}").submit();
              });

				/*$(".marginbtn").on("click", function(e){
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
					var table_div = document.getElementById('display');
					var table_html = table_div.outerHTML.replace(/ /g, '%20');
					a.href = data_type + ', ' + table_html;
					//setting the file name
					a.download = 'RA Zone wise margin data' + postfix + '.xls';
					//triggering the function
					a.click();
					//just in case, prevent default behaviour
					e.preventDefault();
				});*/

              </script>

    @include('includes/footer')
@endsection
