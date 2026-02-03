@extends('layouts.default')

@section('main_container')
@php
  $custcode=str_replace('/', '',$custdetails[0]->customer_code);
@endphp
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit Customer</h2>
                      <a href="{{url('/customer/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'customer/'.$custcode,'class'=>'form-signin','method' => 'PUT')) }}

                      @if($errors->any())
                      <div class="alert alert-danger fade in">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                      </div>
                      @endif
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Customer Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ Form::text('customer_code', $custdetails[0]->dns_customer_code, array(
                                'class' => 'form-control',
                                'id' => '',
                                'placeholder' => 'Customer Code',
                            )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Customer Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('customer_name', $custdetails[0]->customer_name, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Customer Name',
                          )) }}
                      </div>
                      <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Address</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::textarea('address', $custdetails[0]->address, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Address',
                          )) }}

                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Pin</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('pin', $custdetails[0]->pin, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Pin',
                          )) }}

                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('phone_no', $custdetails[0]->phone_no, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Phone Number',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Landline Number</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('landline_no', $custdetails[0]->landline_no, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Landline Number',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('email', $custdetails[0]->email, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Email',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">State</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('state', $custdetails[0]->state_code, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'State',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Visit Day</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::select('visit_day', [
                             ''      =>'Select Day',
                             'Sunday' => 'Sunday',
                             'Monday' => 'Monday',
                             'Tuesday'=>'Tuesday',
                             'Wednesday'=>'Wednesday',
                             'Thursday'=>'Thursday',
                             'Friday '=>'Friday',
                             'Saturday'=>'Saturday'
                           ], $custdetails[0]->visit_day, ['class' => 'form-control']
                           )}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Route code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{Form::select('route_code', array_merge(['' => 'Please Select Route'], $route_lists), $custdetails[0]->route_code, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>


                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Credit Limit</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('credit_limit', $custdetails[0]->credit_limit, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Credit Limit',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Credit Days</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('credit_days', $custdetails[0]->credit_days, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Credit Days',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">TD</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('td', $custdetails[0]->TD, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'TD',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Customer Type</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::select('cust_type', [
                             'D' => 'Distributor',
                             'R' => 'Retailer'], $custdetails[0]->cust_type, ['class' => 'form-control']
                          ) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Distributor List</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{Form::select('rds_tag', array_merge(['' => 'Please Select Distributor'], $distributor_lists), $custdetails[0]->rds_tag, ['class' => 'form-control'])}}


                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Owner Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('owner_name', $custdetails[0]->owner_name, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Owner Name',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Owner Phone</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('owner_phone', $custdetails[0]->owner_phone, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Owner Phone',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Weekly Closing Day</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::select('weekly_closing_day', [
                             ''      =>'Select Day',
                             'Sunday' => 'Sunday',
                             'Monday' => 'Monday',
                             'Tuesday'=>'Tuesday',
                             'Wednesday'=>'Wednesday',
                             'Thursday'=>'Thursday',
                             'Friday '=>'Friday',
                             'Saturday'=>'Saturday'
                           ], $custdetails[0]->weekly_closing_day, ['class' => 'form-control']
                           )}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">TIN</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('TIN', $custdetails[0]->TIN, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'TIN',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">PAN</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('PAN', $custdetails[0]->PAN, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'PAN',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">District</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('district', $custdetails[0]->district, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'District',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{Form::select('branch_code', array_merge(['' => 'Please Select Brnach'], $branch_list), $custdetails[0]->branch_code, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Minimum Stock</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('minimum_stock', $custdetails[0]->minimum_stock, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Minimum Stock',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Bank Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('bank_name', $custdetails[0]->bank_name, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Minimum Stock',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Bank Account Number</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('bank_account_number', $custdetails[0]->bank_account_number, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Bank Account Number',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          {{ Form::submit('Submit',array(
          								    'class' => 'btn btn-success',
          								    'id' => '',
          								    'placeholder' => '',
          								)) }}
                        </div>
                      </div>

                    {{ Form::close() }}
                  </div>
                </div>
              </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
