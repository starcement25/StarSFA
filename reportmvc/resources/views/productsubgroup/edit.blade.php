@extends('layouts.default')

@section('main_container')

  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit Product Sub Group</h2>
                    <a href="{{url('/productsubgroup/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'productsubgroup/'.$productsubgroupdetails[0]->product_sub_group_code,'class'=>'form-signin','method' => 'PUT')) }}

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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Sub Group Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('productsubgroup_code', $productsubgroupdetails[0]->dns_product_sub_group_code, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Product Sub Group Code',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Group </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{Form::select('productgroup_lists', $productgroup_lists,NULL , ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Sub Group Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('productsubgroup_name', $productsubgroupdetails[0]->product_sub_group_name, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Product Sub Group Name',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          @php $varticalval = explode(',', $productsubgroupdetails[0]->vertical_value);@endphp
                          {{Form::select('vartical[]', $vartical_list, $varticalval, ['class' => 'form-control vartical-multiple','multiple'=>'multiple'])}}
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
