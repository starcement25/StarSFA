@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Commonfunctions;
$dbname=Session::get('DBNAME');
@endphp
    <!-- page content -->
    <div class="right_col" role="main">

        <div class="col-md-12 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>Customer Details</h2>
                        <a href="{{url('/customer/')}}" class="btn btn-success pull-right">Back</a>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                      <br>

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
                              {{$custdetails[0]->dns_customer_code}}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Customer Name</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$custdetails[0]->customer_name}}
                        </div>
                        <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Address</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->address}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Pin</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->pin}}

                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number </label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->phone_no }}
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Landline Number</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->landline_no }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->email }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">State</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->state_code }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Visit Day</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->visit_day }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Route code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->route_code }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Credit Limit</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ $custdetails[0]->credit_limit }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Credit Days</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ $custdetails[0]->credit_days }}
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">TD</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ $custdetails[0]->TD }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Customer Type</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ $custdetails[0]->cust_type }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Tag Distributor</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->rds_tag }}


                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Owner Name</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->owner_name }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Owner Phone</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->owner_phone }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Weekly Closing Day</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->weekly_closing_day }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">GST</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->TIN }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">PAN</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->PAN }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">District</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->district }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->branch_code }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Minimum Stock</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->minimum_stock }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Bank Name</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->bank_name }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Bank Account Number</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $custdetails[0]->bank_account_number }}
                          </div>
                          <div class="clearfix"></div>
                        </div>

                    </div>
                  </div>
        </div>
    </div>

  </div>
    <!-- /page content -->
    @include('includes/footer')
@endsection
