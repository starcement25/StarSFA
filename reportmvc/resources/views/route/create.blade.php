@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Add New Route</h2>
                    <a href="{{url('/route/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'route','class'=>'form-signin')) }}

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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Route Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('route_code', null, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Route Code',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Route Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('route_name', null, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Route Name',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <!--<div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                            {{Form::select('emp_code', $employee_list, null, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Vartical Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                        {{Form::select('vartical_name', $vartical_list, null, ['class' => 'form-control'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    -->
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
