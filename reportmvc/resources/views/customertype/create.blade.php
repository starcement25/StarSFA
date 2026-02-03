@extends('layouts.default')

@section('main_container')


    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Add New Customer Type</h2>
                      <a href="{{url('/customertype/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'customertype','class'=>'form-signin')) }}

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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Prifix</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('prifix', null, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Prifix',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{ Form::text('name', null, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Name',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Depand On</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">

                          {{Form::select('depanden_to', array_merge(['0' => 'Parent'], $custtypelists), null, ['class' => 'form-control'])}}
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
