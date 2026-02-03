@extends('layouts.default')

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Branch Details</h2>
                    <a href="{{url('/branch/')}}" class="btn btn-success pull-right">Back</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Code</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$branchdetails[0]->dns_branch_code}}
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$branchdetails[0]->branch_name}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Location</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$branchdetails[0]->branch_location}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch State</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$branchdetails[0]->branch_state}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Email ID</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$branchdetails[0]->branch_email_id}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Account Email Id</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                              {{$branchdetails[0]->branch_accounts_email_id}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Branch Allternative Email Id</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                                {{$branchdetails[0]->alternative_email_id}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Plant Name</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                                  {{$branchdetails[0]->plant_name}}
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Cost Center</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                                  {{$branchdetails[0]->costcenter}}
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
