@php
use App\Helpers\Commonfunctions;
use App\Helpers\Reverseauction;
$showmenu=Commonfunctions::getNameMenuDetails();
 $dbname=Session::get('DBNAME');
@endphp
<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;background: url(http://salesmpower.acedns.in/logo/{{ Session::get('LOGONAME')}}) 2% 
                    50% no-repeat; background-size: 70px 40px;">
            <!--a href="{{ url('/dashboard') }}" class="site_title"><img src="http://salesmpower.acedns.in/logo/{{ Session::get('LOGONAME')}}"></a-->
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
                <div class="profile_pic">

                </div>
                <div class="profile_info">
                  @if(Session::has('USERNAME'))
                        <span>Welcome,</span>
                        <h2>{{ Session::get('USERNAME')}}</h2>
                  @endif
                </div>
                <div class="clearfix"></div>
              </div>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
            	@if(strtoupper(Session::get('nick_name'))=='EMAMIT' || strtoupper(Session::get('nick_name'))=='EMAMI')
                	<ul class="nav side-menu">
                    <li><a href="{{ url('/homepage') }}">Home</a></li>
                    <li><a href="{{ url('/reverseauctionprice') }}">Release Rate</a></li>
                    <li><a href="{{ url('/windowtime') }}">Window Time</a></li>
                    <li><a href="{{ url('/closewindow') }}">Window Time Control</a></li>
                    <li><a href="{{ url('/bidcenterreport') }}">Bid center</a></li>
                    <!--li><a href="{{ url('/exportRAbid') }}">RA BID Download</a></li-->
                    <li><a href="{{ url('/releaseratereport') }}">Release Rate Report</a></li>
                    <li><a href="{{ url('/uploadfreight') }}">Upload Freight</a></li>
                    <li><a href="{{ url('/uploadmargin') }}">Upload Margin</a></li>
                    <li><a href="{{ url('/uploadconversion') }}">Upload Conversion</a></li>
                    <li><a href="{{ url('/RAfreightreportdownload') }}">RA Freight Report</a></li>
                    <li><a href="{{ url('/RAmarginreportdownload') }}">RA Margin Report</a></li>
                     <li><a href="{{ url('/smsreport') }}">SMS Report</a></li>
                     <li><a href="{{ url('/RAconversionreportdownload') }}">Conversion Report</a></li>
                    </ul>          
                @else
                <ul class="nav side-menu">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li class=""><a style="width:150px;">House Keeping <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" style="display: none;">
                      @if($showmenu->no_of_branches>0)
                      <li><a href="{{ url('/branch') }}">Branch Master</a></li>
                      @endif
                      @if($showmenu->vertical_fields=='yes')
                      <li><a href="{{ url('/vartical') }}">Vartical Master</a></li>
                      @endif
                      <li><a href="{{ url('/employee') }}">Employee Master</a></li>
                      <li><a href="{{ url('/route') }}">Route Master</a></li>
                      @if($showmenu->vertical_fields=='yes')
                      <li><a href="{{ url('/customertype') }}">Customer Type Master</a></li>
                      @endif
                      <li><a href="{{ url('/customer') }}">Customer Master</a></li>
                      @if(Session::get('isproductgroup')=='yes')
                      <li><a href="{{ url('/productgroup') }}">Product Group Master</a></li>
                      @endif
                      @if(Session::get('isproductsubgroup')=='yes')
                      <li><a href="{{ url('/productsubgroup') }}">Product Sub Group Master</a></li>
                      @endif
                      @if(Session::get('isproductbrand')=='yes')
                      <li><a href="{{ url('/productbrand') }}">Product Brand Master</a></li>
                      @endif
                      <li><a href="{{ url('/product') }}">Product Master</a></li>
                      @if($showmenu->uom_wise_mrp=='yes' || $showmenu->branch_wise_mrp=='yes' || $showmenu->state_wise_mrp=='yes')
                      <li><a href="{{ url('/mrp') }}">Price List Master</a></li>
                      @endif
                    </ul>
                  </li>
                    <li class=""><a style="width:150px;">Activity Analysis<span class="fa fa-chevron-down" style="margin-right: 0px;"></span></a>
                    <ul class="nav child_menu" style="display: none;">
                      @if($showmenu->attendance=='yes')
                      <li><a href="{{ url('/attendancecalview') }}">Attendance</a></li>
                      @endif
                      @if($showmenu->route_plan=='yes')
                      <li><a>Beat/Route/Tour Plan</a>
                         <ul class="nav child_menu" style="display: block;">
                                        <li class="sub_menu"><a href="{{ url('routplanreport') }}">Route Plan Report</a> </li>
                                        <!--<li><a href="{{ url('routplanapproval') }}">Route Plan Approval </a></li>-->

                          </ul>
                      </li>
                      @endif
                      <li><a href="{{url('visitanalysis')}}">Visit Analysis</a></li>
                      @if($showmenu->order=='yes')
                      <li><a>Sales Order Analysis</a>
                        <ul class="nav child_menu" style="display: block;">
                                       <li class="sub_menu"><a href="{{ url('dailyactivityanalysis') }}">Daily Activity Analysis</a> </li>
                                       <li class="sub_menu"><a href="{{ url('montlyactivityanalysis') }}">Monthly Activity Report</a> </li>
                                       <li class="sub_menu"><a href="{{ url('saleregister') }}">Sale Register</a> </li>

                         </ul>
                      </li>
                      @endif
                      @if($showmenu->collection=='yes')
                      <li><a href="{{ url('collectionanalysis') }}">Collection Analysis</a></li>
                      @endif
                      @if($showmenu->stk_audit=='yes')
                      <!--<li><a href="{{ url('/attendance') }}">Stock Audit Analysis</a></li>-->
                      @endif
                      @if($showmenu->add_customer=='yes')
                      <li><a href="{{ url('/newcustomer') }}">New Customer Addition</a></li>
                      @endif
                      @if($showmenu->target_achievement=='yes')
                      <li><a href="{{ url('/attendance') }}">Appraisal</a></li>
                      @endif
                    </ul>
                    </li>
                </ul>
                @endif
            </div>

        </div>
        <!-- /sidebar menu -->


    </div>
</div>
