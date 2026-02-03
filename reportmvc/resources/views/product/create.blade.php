@extends('layouts.default')

@section('main_container')
<script type="text/javascript">
function checkvalue(val)
{
    if(val==="othergroup")
       document.getElementById('product_other_group').style.display='block';
    else
       document.getElementById('product_other_group').style.display='none';
}
function checkvalue1(val)
{
    if(val==="othersubgroup")
       document.getElementById('product_other_subgroup').style.display='block';
    else
       document.getElementById('product_other_subgroup').style.display='none';
}
function checkvalue2(val)
{
    if(val==="otherbrand")
       document.getElementById('product_other_brand').style.display='block';
    else
       document.getElementById('product_other_brand').style.display='none';
}
</script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
      <!-- page content -->
      <div class="right_col" role="main">

        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                    <div class="x_title">
                      <h2>Add New Product</h2>
                        <a href="{{url('/product/')}}" class="btn btn-success pull-right">Back</a>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                          <br>
                          {{ Form::open(array('url' => 'product','class'=>'form-signin')) }}

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
                                  {{Form::select('brnach_code', array_merge(['' => 'Please Select Brnach'], $productbranch_lists), null, ['class' => 'form-control'])}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Code</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('product_code', null, array(
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

                                {{Form::select('productgroup_lists', array_merge(['' => 'Please Select Group'],$productgroup_lists,['othergroup' => 'Other Group']), null, ['class' => 'form-control','onchange'=>'checkvalue(this.value)'])}}

                                {{ Form::text('productgroup_name', null, array(
                								    'class' => 'form-control',
                								    'id' => 'product_other_group',
                								    'placeholder' => 'Product Group',
                                    'style'=>'display:none',
                								)) }}
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
                                  {{Form::select('productsubgroup_lists', array_merge(['' => 'Please Select Sub Group'], $productsubgroup_lists,['othersubgroup' => 'Other Sub Group']), null, ['class' => 'form-control','onchange'=>'checkvalue1(this.value)'])}}

                                  {{ Form::text('productsubgroup_name', null, array(
                  								    'class' => 'form-control',
                  								    'id' => 'product_other_subgroup',
                  								    'placeholder' => 'Product Sub Group',
                                      'style'=>'display:none',
                  								)) }}
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
                                {{Form::select('productbrand_lists', array_merge(['' => 'Please Select Brand'], $productbrand_lists,['otherbrand'=>'Other Brand']), null, ['class' => 'form-control','onchange'=>'checkvalue2(this.value)'])}}

                                {{ Form::text('product_brand_name', null, array(
                                    'class' => 'form-control',
                                    'id' => 'product_other_brand',
                                    'placeholder' => 'Product Brand',
                                    'style'=>'display:none',
                                )) }}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            @php } @endphp
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical Code</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                  {{Form::select('vartical[]', $vartical_list, null, ['class' => 'form-control vartical-multiple','multiple'=>'multiple'])}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Description</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">

                                {{ Form::text('product_desc', null, array(
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

                                {{ Form::text('product_stock', null, array(
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

                                  {{ Form::text('product_uom1', null, array(
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

                                  {{ Form::text('product_uom2', null, array(
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

                                    {{ Form::text('product_uom3', null, array(
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

                                  {{ Form::text('product_conversion', null, array(
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

                                    {{ Form::text('product_conversion2', null, array(
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
                                         'Y' => 'Yes'], null, ['class' => 'form-control']
                                      ) }}
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
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Is Focus</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">

                                      {{ Form::select('is_focus', [
                                         'N' => 'No',
                                         'Y' => 'Yes'], null, ['class' => 'form-control']
                                      ) }}
                                    </div>
                                    <div class="clearfix"></div>
                              </div>
                              <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Weightage</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">

                                      {{ Form::text('weightage', null, array(
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

                                      {{ Form::text('vat', null, array(
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

                                      {{ Form::text('addl_vat', null, array(
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
    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
