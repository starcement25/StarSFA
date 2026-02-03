@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Commonfunctions;
$dbname=Session::get('DBNAME');
@endphp

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Product Brand List</h2>
                <a href="{{url('/productbrand/create')}}" class="btn btn-success pull-right">Add New Product Brand</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Product Group Name </th>
                        <th class="column-title" style="display: table-cell;">Product Sub Group Name </th>
                        <th class="column-title" style="display: table-cell;">Product Brand Name </th>
                        <th class="column-title no-link last" style="display: table-cell;"><span class="nobr">Action</span>
                        </th>
                      </tr>
                    </thead>

                  <tbody>

                      @foreach ($productbands as $key => $productband)
                      <tr class="even pointer">
                        <td class=" ">{{ Commonfunctions::getNameTable($dbname, 'product_group_master', 'product_group_name', 'product_group_code', $productband->product_group_code) }}</td>
                        <td class=" ">{{ Commonfunctions::getNameTable($dbname, 'product_sub_group_master', 'product_sub_group_name', 'product_sub_group_code', $productband->product_sub_group_code) }}</td>
                        <td class=" ">{{ $productband->product_brand_name	 }}</td>
                        <td class="">
                          <a href="{{ url('/productbrand/'.$productband->product_brand_code) }}"><i class="fa fa-eye"></i></a>
                          <a href="{{ url('/productbrand/'.$productband->product_brand_code.'/edit') }}"><i class="fa fa-edit"></i></a>
                          {{-- <a href="#"><i class="fa fa-trash-o"></i></a> --}}
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="panel-footer">
                  <div class="row">
                    <div class="col col-xs-8">
                      {{ $productbands->links() }}
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
