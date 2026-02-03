@extends('layouts.default')

@section('main_container')

  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit Vartical</h2>
                    <a href="{{url('/vartical/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'vartical/'.$varticaldetails[0]->vartical_code,'class'=>'form-signin','method' => 'PUT')) }}

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

                          {{ Form::text('vartical_name', $varticaldetails[0]->vartical_name, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Vartical Name',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php
                      if(Session::get('isbranch')=="1" && Session::get('isvarticalbranch')=="yes")
                      {
                      @endphp
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{Form::select('branch[]', $branch_list, null, ['class' => 'form-control vartical-multiple','multiple'=>'multiple'])}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      @php } @endphp

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
