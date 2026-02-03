@extends('layouts.default')

@section('main_container')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
      <!-- page content -->
      <div class="right_col" role="main">

        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                    <div class="x_title">
                      <h2>Add New Customer</h2>
                      <a href="{{url('/customer/')}}" class="btn btn-success pull-right">Back</a>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                          <br>
                          {{ Form::open(array('url' => 'customer','class'=>'form-signin')) }}

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
                                  {{ Form::text('customer_code', null, array(
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
                                {{ Form::text('customer_name', null, array(
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
                                {{ Form::textarea('address', null, array(
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
                                {{ Form::text('pin', null, array(
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
                                {{ Form::text('phone_no', null, array(
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
                                {{ Form::text('landline_no', null, array(
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
                                {{ Form::text('email', null, array(
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
                                {{ Form::text('state', null, array(
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
                        				   ], null, ['class' => 'form-control']
                                 )}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Route code</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{Form::select('route_code', array_merge(['' => 'Please Select Route'], $route_lists), null, ['class' => 'form-control'])}}
                              </div>
                              <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Credit Limit</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('credit_limit', null, array(
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

                                {{ Form::text('credit_days', null, array(
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

                                {{ Form::text('td', null, array(
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
                                   'R' => 'Retailer'], null, ['class' => 'form-control']
                                ) }}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Distributor List</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{Form::select('rds_tag', array_merge(['' => 'Please Select Distributor'], $distributor_lists), null, ['class' => 'form-control'])}}


                              </div>
                              <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Owner Name</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{ Form::text('owner_name', null, array(
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
                                {{ Form::text('owner_phone', null, array(
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
                        				   ], null, ['class' => 'form-control']
                                 )}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">GST</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{ Form::text('TIN', null, array(
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
                                {{ Form::text('PAN', null, array(
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
                                {{ Form::text('district', null, array(
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
                                {{Form::select('branch_code', array_merge(['' => 'Please Select Brnach'], $branch_list), null, ['class' => 'form-control'])}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Minimum Stock</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{ Form::text('minimum_stock', null, array(
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
                                {{ Form::text('bank_name', null, array(
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
                                {{ Form::text('bank_account_number', null, array(
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
    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
