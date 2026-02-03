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
                      <h2>MRP Details</h2>
                        <a href="{{url('/mrp/')}}" class="btn btn-success pull-right">Back</a>
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
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$mrpdetails[0]->branch_code}}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                             {{$mrpdetails[0]->product_code}}
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Destination Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$mrpdetails[0]->destination_code}}

                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Order Type </label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$mrpdetails[0]->order_type}}
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">MRP</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$mrpdetails[0]->mrp}}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        @php $i=0; @endphp
                        @foreach($custtypelists as $key => $custtypelist)
                        @php $i++;
                         $prvval=$custtypelist->prefix;
                        @endphp
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">{{ $custtypelist->name }}</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{ $mrpdetails[0]->$prvval }}
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        @endforeach

                    </div>
                  </div>
        </div>
    </div>

  </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
