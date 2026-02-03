<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MIS Report</title>

		<!-- Bootstrap -->
    <link href="{{ URL::asset('assets/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ URL::asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ URL::asset('assets/nprogress/nprogress.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ URL::asset('assets/custom.css') }}" rel="stylesheet">
  </head>

  <body class="login">
         <div class="row">
           <div class="top_nav">
              <div>
               <nav>
                 <ul class="nav navbar-left"><li><img src="http://salesmpower.acedns.in/storecreate/style/img/acednslogo.gif"></li></ul>
                 <ul class="nav navbar-right"><li><a href="{{ url('store/logout') }}">Log Out</a></li></ul>
               </nav>
              </div>
           </div>

               <div class="col-md-12 col-sm-12 col-xs-12">
                 <div class="x_panel">
                   <div class="col-md-3 col-sm-3 col-xs-2">
                      <div class="row form-group">
                            <div class="col-xs-12">
                              <div class="list-group list-cust">
                                    <a href="#" class="list-group-item">
                                      User Details
                                    </a>
                                    <a href="#" class="list-group-item text-primary">Menu Details</a>
                                    <a href="#" class="list-group-item">Product Details</a>
                                    <a href="#" class="list-group-item">Order Details</a>
                                    <a href="#" class="list-group-item">Survey Form Details</a>
                                    <a href="#" class="list-group-item">Sauda Details</a>
                                    <a href="#" class="list-group-item active">Route Plan Details</a>
                                    <a href="#" class="list-group-item">Market Feedback Details</a>
                                    <a href="#" class="list-group-item">Report Configaration</a>
                              </div>
                            </div>
                         </div>
                     </div>
                     <div class="col-md-9 col-sm-9 col-xs-10">
                      {{ Form::open(array('url' => 'store/editrouteplandetails/'.$storerouteplan->user_id,'id'=>'createuser','class'=>'form-signin','method'=>'patch')) }}

                              <div class="row setup-content" id="step-1">
                                  <div class="col-xs-12">
                                      <div class="col-md-12 well text-center">
                                          <h1>Route Plan Details</h1>

                                            <!-- <form> -->

                                            <div class="container col-xs-12">
                                              <div class="row clearfix">
                          		                    <div class="col-md-12 column">
                          			                       <table class="table table-bordered table-hover" id="tab_logic">
                                                    				<tbody>
                                                              <?php
                                                              //echo "<pre>";print_r($userdetails);
                                                              ?>
                                                              @foreach ($routeplanedetails as $key => $val)
                                                    					<tr>
                                                                @php
                                                                $excludearraylist=array('0','1','2','39');
                                                                if(in_array($key,$excludearraylist)) { continue;}
                                                                $colname=$val->ColumnName;
                                                                @endphp
                                                    						<td>{{ucfirst(str_replace('_',' ',$val->ColumnName))}}</td>
                                                    						<td>
                                                                 @php
                                                                 if($val->DataType=='varchar' || $val->DataType=='int')
                                                                 {

                                                                  @endphp
                                                                  {{ Form::text($val->ColumnName,  $storerouteplan->$colname, array(
                                                                      'class' => 'form-control',
                                                                      'id' =>$val->ColumnName,
                                                                      'placeholder' =>$val->ColumnName,
                                                                  )) }}
                                                                  @php

                                                                   }
                                                                   elseif($val->DataType=='enum')
                                                                   {
                                                                 @endphp
                                                                 <input type="radio" name="{{$val->ColumnName}}" value="yes" @php if($storerouteplan->$colname=='yes') { @endphp checked="" @php } @endphp> Yes
                                                                 <input type="radio" name="{{$val->ColumnName}}" value="no" @php if($storerouteplan->$colname=='no') { @endphp checked="" @php } @endphp> No
                                                                 @php
                                                                   }
                                                                   elseif($val->DataType=='text'){
                                                                 @endphp
                                                                    <textarea name="{{$val->ColumnName}}" class="form-control"></textarea>
                                                                 @php
                                                                   }

                                                                 @endphp
                                                                </td>
                                                    					</tr>
                                                              @endforeach;
                                                    				</tbody>
                          			                         </table>
                          		                     </div>
                          	                   </div>
                                             </div>

                                          <!-- </form> -->

                                          <button id="activate-step-2" class="btn btn-primary btn-md">Submit</button>
                                      </div>
                                  </div>
                              </div>
                      {{ Form::close() }}
                    </div>
                 </div>
               </div>
          </div>
        </div>
    </body>
</html>
