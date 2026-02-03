@extends('layouts.default')
@section('main_container')
@php
 use App\Helpers\Reverseauction;
 $dbname=Session::get('DBNAME');
 $windowtimelatest=Reverseauction::getWindowtime($dbname);
 if(count($windowtimelatest) >0)
 {
 	foreach($windowtimelatest as $key => $windowtimeval){
 		$windowtimefrom=$windowtimeval->time_from;
        $windowtimeto=$windowtimeval->time_to;
       } 
 }
 else
 {
 	$windowtimefrom='00:00:00';
 	$windowtimeto='00:00:00';
 }
  $hour=gmdate('H',strtotime('+330 minute'));
  $minute=gmdate('i',strtotime('+330 minute'));
  $second=gmdate('s',strtotime('+330 minute'));
  $currenttime=$hour.':'.$minute.':'.$second;
  $currenttime=$currenttime;
@endphp  
 <style>
	.row {
		display: flex;
		width: 70%;
		margin: 0 auto;
	}
/* Create two equal columns that sits next to each other */
	.column {
		flex: 50%;
		padding: 10px;
	}
	.datediv {
		flex: 50%;
		width: 50%;
		margin: 0 auto;
		text-align: center;
	}
	.main-container {
		width: 70%;
		border: 1px solid;
		margin: 0 auto;
		text-align: center;
		position: inherit;
		height: 150px;
		padding: 5px;
		margin-top:100px;
	}
</style>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>  
<div class="right_col" role="main">
  <form name="upload_margin" method="post" action="/reportmvc/uploadconversionsubmit" enctype="multipart/form-data" onsubmit="return checking_windowtime();"/>
    {{ csrf_field() }}
    @if(session()->has('message'))
            <div class="alert alert-success" align="center" style="font-weight:bold;">
                {{ session()->get('message') }}
            </div>
        @endif
        @if (count($errors) > 0)
        <div class="alert alert-danger" align="center" style="font-weight:bold;">
            <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
            </ul>
        </div>
        @endif
   <div class="main-container">
   		 <h3 align="center"><u>Upload Conversion Csv File</u></h3>
         <div class="clearfix" style="height:10px;"></div>
         <div class="form-group" align="center">
          <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
           <div class="col-md-9 col-sm-9 col-xs-12" >
             {{ Form::file('conversion_csv_file', null, array(
                 'class' => 'form-control',
                 'id' => 'uploaded_file',
             )) }}
           </div>
           <div class="clearfix"></div>
         </div>
         <div class="form-group" align="center">
             <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3" >
             {{ Form::submit('Submit',array(
                 'class' => 'btn btn-success',
                 'id' => 'upload_form1',
                 'placeholder' => '',
             )) }}
           </div>
         </div>  

   </div> 
   </form>
    <!--div style="position: relative">
      <strong>From:</strong>
      <input class="timepicker form-control" type="text" name="from" size="11">
    </div-->
   <script language="javascript" type="text/javascript">
    function checking_windowtime()
	 {
		 var windowtimefrom="<?=$windowtimefrom;?>";
		  var windowtimeto="<?=$windowtimeto;?>";
		 var currenttime="<?=$currenttime;?>";
		 if(currenttime >=windowtimefrom &&  currenttime <= windowtimeto) {
			  alert("Window time is open. Plesae try sometime later.");
			  return false;
		 }
		 else
		 {
			 return true;	 
		 }
		 /*else if(currenttime > windowtimeclosefrom && windowtimeclosefrom!='00:00:00')
		 {
			  alert("Under 30 minutes locking period price release is impossible.");
		 }*/
	 }
	 </script>
</div>
@include('includes/footer')
@endsection