@extends('layouts.default')

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit Branch</h2>
                    <a href="{{url('/branch/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'branch/'.$branchdetails[0]->branch_code,'class'=>'form-signin','method' => 'PUT')) }}

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

                          {{ Form::text('brach_master_code', $branchdetails[0]->dns_branch_code, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Code',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_master_name', $branchdetails[0]->branch_name, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Name',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Location</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_location', $branchdetails[0]->branch_location, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Location',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch State</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_state', $branchdetails[0]->branch_state, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch State',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Email ID</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_emailid', $branchdetails[0]->branch_email_id, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Email ID',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Account Email ID</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_account_emailid', $branchdetails[0]->branch_accounts_email_id, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Account Email ID',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Alternative Email ID</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_alternative_emailid', $branchdetails[0]->alternative_email_id, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Alternative Email ID',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Plant Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('plamt_name', $branchdetails[0]->plant_name, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Plant Name',
          								)) }}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Costcenter</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          {{ Form::text('brach_costcenter', $branchdetails[0]->costcenter, array(
          								    'class' => 'form-control',
          								    'id' => '',
          								    'placeholder' => 'Branch Costcenter',
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
