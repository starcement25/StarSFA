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
                                    <a href="#" class="list-group-item active">Order Details</a>
                                    <a href="#" class="list-group-item">Survey Form Details</a>
                                    <a href="#" class="list-group-item">Sauda Details</a>
                                    <a href="#" class="list-group-item">Route Plan Details</a>
                                    <a href="#" class="list-group-item">Market Feedback Details</a>
                                    <a href="#" class="list-group-item">Report Configaration</a>
                              </div>
                            </div>
                         </div>
                     </div>
                     <div class="col-md-9 col-sm-9 col-xs-10">
                      {{ Form::open(array('url' => 'store/editorderdetails/'.$storeorderdetails->user_id,'id'=>'createuser','class'=>'form-signin','files'=>'true','method'=>'patch')) }}

                              <div class="row setup-content" id="step-1">
                                  <div class="col-xs-12">
                                      <div class="col-md-12 well text-center">
                                          <h1>Order Details</h1>

                                            <!-- <form> -->

                                            <div class="container col-xs-12">
                                              <div class="row clearfix">
                          		                    <div class="col-md-12 column">
                          			                       <table class="table table-bordered table-hover" id="tab_logic">
                                                    				<tbody>

                                                              @foreach ($orderdetails as $key => $val)
                                                    					 <tr>
                                                                    @php
                                                                    $excludearraylist=array('0','1','2','57');
                                                                    if(in_array($key,$excludearraylist)) { continue;}
                                                                    $colname=$val->ColumnName;
                                                                    @endphp
                                                    						        <td>{{ucfirst(str_replace('_',' ',$val->ColumnName))}}</td>
                                                            						<td>
                                                                         @php
                                                                         if($val->DataType=='varchar' || $val->DataType=='int')
                                                                         {
                                                                          @endphp
                                                                              {{ Form::text($val->ColumnName, $storeorderdetails->$colname, array(
                                                                                  'class' => 'form-control',
                                                                                  'id' =>$val->ColumnName,
                                                                                  'placeholder' =>$val->ColumnName,
                                                                              )) }}
                                                                          @php
                                                                          }
                                                                          elseif($val->DataType=='enum')
                                                                          {
                                                                             if($key=='5' || $key=='21')
                                                                             {
                                                                          @endphp
                                                                             <input type="radio" name="{{$val->ColumnName}}" value="input" @php if($storeorderdetails->$colname=='input') { @endphp checked="" @php } @endphp> Input
                                                                             <input type="radio" name="{{$val->ColumnName}}" value="dropdown" @php if($storeorderdetails->$colname=='dropdown') { @endphp checked="" @php } @endphp> Dropdown
                                                                            @php
                                                                              }
                                                                              elseif($key=='8')
                                                                              {
                                                                             @endphp
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="sku wise" @php if($storeorderdetails->$colname=='sku wise') { @endphp checked="" @php } @endphp> Sku wise
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="order value wise" @php if($storeorderdetails->$colname=='order value wise') { @endphp checked="" @php } @endphp> Order value wise
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="customer wise" @php if($storeorderdetails->$colname=='customer wise') { @endphp checked="" @php } @endphp> Customer wise
                                                                             @php
                                                                              }
                                                                              elseif($key=='9')
                                                                              {
                                                                             @endphp
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="input" @php if($storeorderdetails->$colname=='input') { @endphp checked="" @php } @endphp> Input
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="dropdown" @php if($storeorderdetails->$colname=='dropdown') { @endphp checked="" @php } @endphp> Dropdown

                                                                             @php
                                                                              }
                                                                              elseif($key=='13')
                                                                              {
                                                                              @endphp
                                                                               <input type="radio" name="{{$val->ColumnName}}" value="UOM" @php if($storeorderdetails->$colname=='UOM') { @endphp checked="" @php } @endphp> UOM
                                                                               <input type="radio" name="{{$val->ColumnName}}" value="VALUE" @php if($storeorderdetails->$colname=='VALUE') { @endphp checked="" @php } @endphp> VALUE
                                                                              @php
                                                                              }
                                                                              elseif($key=='25')
                                                                              {
                                                                              @endphp
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="sku" @php if($storeorderdetails->$colname=='sku') { @endphp checked="" @php } @endphp> Sku
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="ordervalue" @php if($storeorderdetails->$colname=='ordervalue') { @endphp checked="" @php } @endphp> Ordervalue

                                                                              @php
                                                                              }
                                                                              elseif($key=='10' || $key=='23')
                                                                              {
                                                                              @endphp
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="amount" @php if($storeorderdetails->$colname=='amount') { @endphp checked="" @php } @endphp> Amount
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="percentage" @php if($storeorderdetails->$colname=='percentage') { @endphp checked="" @php } @endphp> Percentage

                                                                              @php
                                                                              }
                                                                               elseif($key=='31')
                                                                               {
                                                                                @endphp
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="trade" @php if($storeorderdetails->$colname=='trade') { @endphp checked="" @php } @endphp> Trade
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="nontrade" @php if($storeorderdetails->$colname=='nontrade') { @endphp checked="" @php } @endphp> Nontrade
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="both" @php if($storeorderdetails->$colname=='both') { @endphp checked="" @php } @endphp> Both

                                                                                 @php
                                                                               }
                                                                               elseif($key=='40')
                                                                               {
                                                                              @endphp
                                                                                   <input type="radio" name="{{$val->ColumnName}}" value="cash" @php if($storeorderdetails->$colname=='cash') { @endphp checked="" @php } @endphp> Cash
                                                                                   <input type="radio" name="{{$val->ColumnName}}" value="credit" @php if($storeorderdetails->$colname=='credit') { @endphp checked="" @php } @endphp> Credit
                                                                                   <input type="radio" name="{{$val->ColumnName}}" value="both" @php if($storeorderdetails->$colname=='both') { @endphp checked="" @php } @endphp> Both

                                                                               @php
                                                                               }
                                                                               elseif($key=='52')
                                                                                {
                                                                              @endphp
                                                                                     <input type="radio" name="{{$val->ColumnName}}" value="BLUETOOTH" @php if($storeorderdetails->$colname=='BLUETOOTH') { @endphp checked="" @php } @endphp> BLUETOOTH
                                                                                     <input type="radio" name="{{$val->ColumnName}}" value="WLAN" @php if($storeorderdetails->$colname=='WLAN') { @endphp checked="" @php } @endphp> WLAN
                                                                                     <input type="radio" name="{{$val->ColumnName}}" value="BOTH" @php if($storeorderdetails->$colname=='BOTH') { @endphp checked="" @php } @endphp> BOTH

                                                                               @php
                                                                                }
                                                                               else{
                                                                              @endphp
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="yes" @php if($storeorderdetails->$colname=='yes') { @endphp checked="" @php } @endphp> Yes
                                                                                 <input type="radio" name="{{$val->ColumnName}}" value="no" @php if($storeorderdetails->$colname=='no') { @endphp checked="" @php } @endphp> No
                                                                                @php
                                                                               }
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
