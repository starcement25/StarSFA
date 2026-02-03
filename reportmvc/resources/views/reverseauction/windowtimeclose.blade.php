@extends('layouts.default')
@section('main_container')
@php
 use App\Helpers\Reverseauction;
 $dbname=Session::get('DBNAME');
 $allwindowtime=Reverseauction::getallWindowtime($dbname);
 $hour=gmdate('H',strtotime('+330 minute'));
 $minute=gmdate('i',strtotime('+330 minute'));
 $second=gmdate('s',strtotime('+330 minute'));
 $currenttime=$hour.':'.$minute.':'.$second;
@endphp
    <!-- page content -->
    <div class="right_col" role="main" >
              <div class="col-md-9 col-sm-9 col-xs-9" >
                <div class="x_panel">
                	<div class="x_title">
                    <h2>Window Time Lists</h2>
                    <div class="clearfix"></div>
                        @if(!empty($successMsg))
                          <div class="alert alert-success"> {{ $successMsg }}</div>
                        @endif

                  </div>
                    @php if($allwindowtime) { @endphp
                  <div class="x_content">
                    <div class="table-responsive">
                    <form name="close_window_time" method="POST" action="/reportmvc/closewindowsubmit">
                     {{ csrf_field() }}
                      <table class="table table-striped jambo_table bulk_action" id="display"  border="1" width="60%">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Time From</th>
                            <th class="column-title">Time To</th>
                            <th class="column-title">Operation</th>
                          </tr>
                        </thead>

                        <tbody>
                         <?php //echo "<pre>";print_r($reoprtdata);exit; ?>
                          @foreach ($allwindowtime as $key => $windowtimeval)
                          
                          <tr class="even pointer" width="60%">
                            <td>{{$windowtimeval->time_from}}</td>
                            <td>{{$windowtimeval->time_to}}</td>
                            <td id="closelink{{$windowtimeval->sl_no}}" style="display:''" >
                            <a href="javascript:void(0);" onclick="javascript:close_windowtime('{{$windowtimeval->sl_no}}','{{$windowtimeval->time_to}}');">CLOSE</a></td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <input type="hidden" name="sl_no" id="sl_no" value="" />
                        <input type="hidden" name="time_to" id="time_to" value="" />
                      <form>
                      <!--div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="center">
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success bidbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} 
                        </div-->
                    </div>
                  </div>
                    @php }else{ @endphp
                    <table class="table table-striped jambo_table bulk_action" id="display"  border="1">
                        <tbody>
                           <tr><td align="center">No window time exists for today.</td>
                          </tr>
                        </tbody>
                  </table> 
                   @php }@endphp       
                </div>
              </div>
                   
    </div>
              <script type="text/javascript">
              $(".bidbtn").on("click", function(e){
                  e.preventDefault();
                  $('#bidreportform').attr('action', "{{ url('/bidcenterexportcsv') }}").submit();
              });
			  function close_windowtime(sl_no,time_to)
			  {
				  document.getElementById("sl_no").value= sl_no;
				   document.getElementById("time_to").value= time_to;
        		  //document.update_bid_status.action = "/reportmvc/bidstatuschange";
				  document.close_window_time.submit();
			  }
              </script>

    @include('includes/footer')
@endsection
