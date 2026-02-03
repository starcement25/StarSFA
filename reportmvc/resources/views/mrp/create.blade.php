@extends('layouts.default')

@section('main_container')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
      <!-- page content -->
      <div class="right_col" role="main">

        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                    <div class="x_title">
                      <h2>Add New MRP</h2>
                        <a href="{{url('/mrp/')}}" class="btn btn-success pull-right">Back</a>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                          <br>
                          {{ Form::open(array('url' => 'mrp','class'=>'form-signin')) }}

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
                                  {{Form::select('brnach_code', array_merge(['' => 'Please Select Brnach'], $branch_lists), null, ['class' => 'form-control'])}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Code</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                              {{Form::select('product_code', array_merge(['' => 'Please Select Product'], $product_lists), null, ['class' => 'form-control'])}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <!--<div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Mrp Code</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{ Form::text('dns_mrp_code', null, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'Mrp Code',
                                )) }}

                              </div>
                              <div class="clearfix"></div>
                            </div>-->
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Destination Name</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{Form::select('destination_code', array_merge(['' => 'Please Select Destination'], $destinationlists), null, ['class' => 'form-control'])}}

                              </div>
                              <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Order Type </label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{ Form::text('order_type', null, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'Order Type',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">MRP</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                {{ Form::text('mrp', null, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'MRP',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            @php $i=0; @endphp
                            @foreach($custtypelists as $key => $custtypelist)
                            @php $i++; @endphp
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">{{ $custtypelist->name }}</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('price'.$i, null, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => '',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                            </div>

                            @endforeach
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
