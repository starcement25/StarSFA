<!DOCTYPE html>
<html lang="en">
@php
 use App\Helpers\Reverseauction;
 $date=gmdate('d',strtotime('+330 minute'));
 $month=gmdate('m',strtotime('+330 minute'));
 $year=gmdate('Y',strtotime('+330 minute'));
 $currentdate= $month.'/'.$date.'/'.$year;
@endphp
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ADMIN PANEL </title>

    <!-- Bootstrap -->
    <link href="{{ URL::asset('assets/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ URL::asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ URL::asset('assets/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">


    <!-- Custom Theme Style -->
    <link href="{{ URL::asset('assets/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <script src="{{ URL::asset('assets/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/JavaScript">
		<!--
		function timedRefresh(timeoutPeriod) {
			//alert(document.getElementById("start_date").value);
			var currentdate="<?=$currentdate;?>";
			var selecteddate=document.getElementById("start_date").value;
			if(currentdate==selecteddate)
			{
				setTimeout("location.reload(true);",timeoutPeriod);
			}
		}
		//   -->
	</script>
  </head>
  <body class="nav-md" <?php if(Route::getCurrentRoute()->getPath()=='bidcenterreport'){?>onload="javascript:timedRefresh(30000);"<?php }?>>
          <div class="container body">
              <div class="main_container">
                  @include('includes/sidebar')

                  @include('includes/topbar')
                  
                  @yield('main_container')

              </div>
          </div>



<!-- jQuery -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.js"></script>
<!-- Bootstrap -->
<script src="{{ URL::asset('assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ URL::asset('assets/fastclick/lib/fastclick.js') }}"></script>
<!-- NProgress -->
<script src="{{ URL::asset('assets/nprogress/nprogress.js') }}"></script>

<script type="text/javascript" src="{{ URL::asset('assets/fusioncharts/js/fusioncharts.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/fusioncharts/js/themes/fusioncharts.theme.fint.js') }}"></script>

<!-- Custom Theme Scripts -->
<script src="{{ URL::asset('assets/custom.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<script type="text/javascript">
$(".js-example-placeholder-multiple").select2({
  placeholder: "Select a Branch"
});
$(".report-to-multiple").select2({
  placeholder: "Select a Report To"
});
$(".vartical-multiple").select2({
  placeholder: "Select a vartical"
});
</script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#emp_table').DataTable({
      "iDisplayLength": 50
    });
} );
</script>
</body>
</html>
