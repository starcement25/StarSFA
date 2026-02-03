@extends('layouts.default')

@section('main_container')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit Product</h2>
                    <a href="{{url('/product/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'product/'.$productdetails[0]->prod_code,'class'=>'form-signin','method' => 'PUT')) }}

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
                            {{Form::select('brnach_code', array_merge(['' => 'Please Select Brnach'], $productbranch_lists), $productdetails[0]->branch_code, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('product_code', $productdetails[0]->dns_prod_code, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Product Code',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php
                      if(Session::get('isproductgroup')=="yes")
                      {
                      @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Group</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{Form::select('productgroup_lists', array_merge(['' => 'Please Select Group'], $productgroup_lists), $productdetails[0]->product_group_code, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php
                       }
                       if(Session::get('isproductsubgroup')=="yes")
                       {
                      @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Sub Group</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{Form::select('productsubgroup_lists', array_merge(['' => 'Please Select Sub Group'], $productsubgroup_lists), $productdetails[0]->product_sub_group_code, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php }
                      if(Session::get('isproductbrand')=="yes")
                      {
                      @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Brand </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{Form::select('productbrand_lists', array_merge(['' => 'Please Select Brand'], $productbrand_lists), $productdetails[0]->product_brand_code, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                       @php } @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          @php $varticalval = explode(',', $productdetails[0]->vertical_value);@endphp
                            {{Form::select('vartical[]', $vartical_list, $varticalval, ['class' => 'form-control vartical-multiple','multiple'=>'multiple'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Description</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('product_desc', $productdetails[0]->prod_desc, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Product Discription',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Stock</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('product_stock', $productdetails[0]->cl_stk, array(
                              'class' => 'form-control',
                              'id' => '',
                              'placeholder' => 'Product Stock',
                          )) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product UOM1</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ Form::text('product_uom1', $productdetails[0]->UOM1, array(
                                'class' => 'form-control',
                                'id' => '',
                                'placeholder' => 'Product UOM1',
                            )) }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product UOM2</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ Form::text('product_uom2', $productdetails[0]->UOM2, array(
                                'class' => 'form-control',
                                'id' => '',
                                'placeholder' => 'Product UOM2',
                            )) }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Product UOM3</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">

                              {{ Form::text('product_uom3', $productdetails[0]->UOM3, array(
                                  'class' => 'form-control',
                                  'id' => '',
                                  'placeholder' => 'Product UOM3',
                              )) }}
                            </div>
                            <div class="clearfix"></div>
                          </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Conversion</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">

                            {{ Form::text('product_conversion', $productdetails[0]->conversion_factor, array(
                                'class' => 'form-control',
                                'id' => '',
                                'placeholder' => 'Product Conversion',
                            )) }}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                      <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Conversion 2</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">

                              {{ Form::text('product_conversion2', $productdetails[0]->conversion_factor_two, array(
                                  'class' => 'form-control',
                                  'id' => '',
                                  'placeholder' => 'Product Conversion 2',
                              )) }}
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Is Product Blacklisted</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">


                                {{ Form::select('isblacklist', [
                                   'N' => 'No',
                                   'Y' => 'Yes'], $productdetails[0]->black_list, ['class' => 'form-control']
                                ) }}
                              </div>
                              <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">TD</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('td', $productdetails[0]->TD, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'TD',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Is Focus</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">


                                {{ Form::select('is_focus', [
                                   'N' => 'No',
                                   'Y' => 'Yes'], $productdetails[0]->focus, ['class' => 'form-control']
                                ) }}

                              </div>
                              <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Weightage</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('weightage', $productdetails[0]->weightage, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'Weightage',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Vat</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('vat', $productdetails[0]->vat, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'Vat',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Additional Vat</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('addl_vat', $productdetails[0]->addl_vat, array(
                                    'class' => 'form-control',
                                    'id' => '',
                                    'placeholder' => 'Additional vat',
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
