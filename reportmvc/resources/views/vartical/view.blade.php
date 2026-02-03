@extends('layouts.default')

@section('main_container')
    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Vartical Details</h2>
                    <a href="{{url('/vartical/')}}" class="btn btn-success pull-right">Back</a>
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                                       {{$varticaldetails[0]->vartical_name}}

                        </div>
                        <div class="clearfix"></div>
                      </div>


                  </div>
                </div>
              </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
