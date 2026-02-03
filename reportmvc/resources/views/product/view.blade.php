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
                      <h2>Product Details</h2>
                      <a href="{{url('/product/')}}" class="btn btn-success pull-right">Back</a>
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
                                         {{$productdetails[0]->branch_code}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                                         {{$productdetails[0]->dns_prod_code}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        @php
                        if(Session::get('isproductgroup')=="yes")
                        {
                        @endphp
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Group Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                            {{$productdetails[0]->product_group_code}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        @php
                         }
                         if(Session::get('isproductsubgroup')=="yes")
                         {
                        @endphp
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Sub Group Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                                         {{$productdetails[0]->product_sub_group_code}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        @php }
                        if(Session::get('isproductbrand')=="yes")
                        {
                        @endphp
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Brand Code</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                                         {{$productdetails[0]->product_brand_code}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        @php } @endphp
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical Name</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                                         {{$productdetails[0]->vertical_value}}

                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Description</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$productdetails[0]->prod_desc}}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Stock</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
                             {{$productdetails[0]->cl_stk}}
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Product UOM1</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                               {{$productdetails[0]->UOM1}}
                            </div>
                            <div class="clearfix"></div>
                          </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Product UOM2</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                {{$productdetails[0]->UOM2}}
                            </div>
                            <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Product UOM3</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                 {{$productdetails[0]->UOM3}}
                              </div>
                              <div class="clearfix"></div>
                            </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Conversion</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                               {{$productdetails[0]->conversion_factor}}
                            </div>
                            <div class="clearfix"></div>
                          </div>
                        <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12">Product Conversion 2</label>
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                 {{$productdetails[0]->conversion_factor_two}}
                              </div>
                              <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Is Product Blacklisted</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                   {{$productdetails[0]->black_list}}
                                </div>
                                <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">TD</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                   {{$productdetails[0]->TD}}
                                </div>
                                <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Is Focus</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                   {{$productdetails[0]->focus}}
                                </div>
                                <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Weightage</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                   {{$productdetails[0]->weightage}}
                                </div>
                                <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Vat</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                   {{$productdetails[0]->vat}}
                                </div>
                                <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Additional Vat</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                   {{$productdetails[0]->addl_vat}}
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
