@extends('layouts.default')

@section('main_container')
@php
use App\Helpers\Commonfunctions;
$dbname=Session::get('DBNAME');
@endphp
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- page content -->
    <div class="right_col" role="main" align="center">

      	 <img src="http://salesmpower.acedns.in/logo/{{ Session::get('LOGONAME')}}"  alt=""/>
            <!--a href="{{ url('/dashboard') }}" class="site_title"><img src="http://salesmpower.acedns.in/logo/{{ Session::get('LOGONAME')}}"></a-->
        </div>

    </div>
    <!-- /page content -->
    @include('includes/footer')
@endsection
