@extends('layouts.default')
@section('main_container')
@php
 use App\Helpers\Reverseauction;
 $dbname=Session::get('DBNAME');
 $RAfreightlist=Reverseauction::RAfreightdownloadreport($dbname);
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
                    <h2>RA freight lists</h2>
                    <div class="clearfix"></div>
                        @if(!empty($successMsg))
                          <div class="alert alert-success"> {{ $successMsg }}</div>
                        @endif
                  </div>
                    @php if($RAfreightlist) { @endphp
                  <div class="x_content">
                    <div class="table-responsive">
                    <form name="close_window_time" method="POST" action="/reportmvc/closewindowsubmit">
                     {{ csrf_field() }}
                      <table class="table table-striped jambo_table bulk_action" id="display"  border="1" width="60%">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Date of Upload</th>
                            <th class="column-title">Branch code</th>
                            <th class="column-title">Route</th>
                            <th class="column-title">Zone</th>
                            <th class="column-title">Transport mode</th>
                            <th class="column-title">Truck size</th>
                            <th class="column-title">Freight</th>
                          </tr>
                        </thead>
                        <tbody>
                         <?php //echo "<pre>";print_r($reoprtdata);exit; ?>
                          @foreach ($RAfreightlist as $key => $RAfreightval)
                          
                          <tr class="even pointer" width="60%">
                            <td>{{$RAfreightval->date_upload}}</td>
                            <td>{{$RAfreightval->dns_branch_code}}</td>
                            <td>{{$RAfreightval->route_name}}</td>
                            <td>{{$RAfreightval->zone}}</td>
                            <td>{{$RAfreightval->transport_mode}}</td>
                            <td>{{$RAfreightval->capacity}}</td>
                            <td>{{$RAfreightval->freight}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <form>
                      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="center">
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success freightbtn',
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
				$(".freightbtn").on("click", function(e){
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
					a.download = 'RA freight data' + postfix + '.xls';
					//triggering the function
					a.click();
					//just in case, prevent default behaviour
					e.preventDefault();
				});

              </script>

    @include('includes/footer')
@endsection
