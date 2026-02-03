@extends('layouts.default')
@section('main_container')
@php
 use App\Helpers\Reverseauction;
 $dbname=Session::get('DBNAME');
 $windowtimelatest=Reverseauction::getWindowtime($dbname);
 if(count($windowtimelatest) >0)
 {
 	foreach($windowtimelatest as $key => $windowtimeval){
 		$windowtime_from=$windowtimeval->time_from;
        $windowtime_to=$windowtimeval->time_to;
       } 
 }
 else
 {
 	$windowtime_from='00:00:00';
 	$windowtime_to='00:00:00';
 }

 $windowtimedetails=Reverseauction::getWindowtimeDetails($dbname);
 if(count($windowtimedetails) >0)
 {
  foreach ($windowtimedetails as $key => $windowtimeval){
       $windowtimefrom=$windowtimeval->time_from;
      $windowtimeto=$windowtimeval->time_to;
      $windowtimetimestamp=$windowtimeto;
    } 
  }
  else
  {
  	$windowtimefrom='00:00:00';
    $windowtimeto='00:00:00';
    $windowtimetimestamp='00:00:00';
  }  
    $releaseratetime=Reverseauction::getReleaseRatetime($dbname);
    $releaseratetimeafterthirtym= date('Y-m-d H:i:s',strtotime("$releaseratetime +1 minute"));
    $releaseratetimefinal=substr($releaseratetimeafterthirtym,11,8);	
    $calldone=1;
 @endphp
  <!--link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"-->
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
		position:inherit;
		padding: 5px;
		margin-top:100px;
	}
	#jqDialog_box {
	background: #f5f5f5;
	position: absolute;
	width: 450px;
	height: 130px;
	font-family: Arial;
	
	border-width: 1px 3px 3px 1px;
	border-style: solid;
	border-color: #ccc;

	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	-khtml-border-radius: 6px;
	border-radius: 6px;
	
	-moz-box-shadow: 0 0 30px #e6e6e6;
	}
	#jqDialog_content {
		margin: 10px;
		font-weight: bold;
		font-size: 12px;
		height: 70px;
		overflow: hidden;
	}

	#jqDialog_options {
		margin: 10px;
		text-align: center;
	}
	#jqDialog_options button {
		font-family: Arial;
		margin-right: 5px;
		background: #000;
		border: 0px;
	
		font-size: 1.5em;
		color: #fff;
		width: auto;
		
		cursor: pointer;
		
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		-khtml-border-radius: 3px;
		border-radius: 3px;
	}

	#jqDialog_input {
		padding: 4px;
		width: 250px;
	}
	#jqDialog_close {
		background: none;
		border: none;
		float: right;
		font-weight: bold;
		font-size: 10px;
		color: #ff0000;
		cursor: pointer;
	}
</style>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>  
  <script src="{{ URL::asset('assets/jqdialog.min.js') }}"></script>

<div class="right_col" role="main">
  <form name="window_time" method="post" action="/reportmvc/windowtimesubmit"  />
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
   		 <h2 align="center"><u>Window Time</u></h2>
          @php
          $hour=gmdate('H',strtotime('+330 minute'));
          $minute=gmdate('i',strtotime('+330 minute'));
          $second=gmdate('s',strtotime('+330 minute'));

          $currenttime=$hour.':'.$minute.':'.$second;
          //$windowtimedetail;
		  if($currenttime >=$windowtime_from  &&  $currenttime <=$windowtime_to)
		  {
          	//echo 'a';
          	//$imgsrc=public_path('images');
            $imgsrc='open.png';
          }
          else
          {
          	//echo 'b';
          	//$imgsrc=public_path('images');
            $imgsrc='close.png';
          }
          @endphp
         <div style="position: relative"><img src="http://salesmpower.acedns.in/reportmvc/public/images/{{ $imgsrc }}" alt="" /></div>
      <div class="datediv" style="position: relative">
        <p><strong>Date:</strong><input type="text" name="windowdate" class="datepicker form-control"/></p>
    </div>
    <div class="row" style="position: relative">
     <div class="column" >
        <p><strong>From:</strong><input type="text" name="timefrom" id="timefrom" class="timepicker form-control" /></p>
      </div>
      <div class="column" >
       <p><strong>To:</strong><input type="text" name="timeto" class="timepicker form-control" id="timeto" /></p>
      </div>
    </div>
    	<div class="datediv">
        <p><input type="hidden" name="confirmval" id="confirmval" value=""/><input type="button" name="timesubmit" value=" Submit " id="bt-confirm" /></p>
    </div>
   </div> 
   </form>
    <!--div style="position: relative">
      <strong>From:</strong>
      <input class="timepicker form-control" type="text" name="from" size="11">
    </div-->
</div>
<script type="text/javascript">
	//var calldone = false;
	function check_previouswindow(calldone)
	{
		var timefromval=document.getElementById("timefrom").value;
		var timefromvalparse=Date.parse(timefromval);
		var windowtimeto="<?=$windowtimeto;?>";
		var windowtimestamp="<?=$windowtimetimestamp;?>";
		var releaseratetimefinal="<?=$releaseratetimefinal;?>";
		//var windowtimetoparse=Date.parse(windowtimeto);
		//var difference=timefromvalparse-windowtimestamp;
		//alert(difference);
		if((timefromval < releaseratetimefinal) && calldone==1)
		{
			alert("Window time From should be after release rate time - "+releaseratetimefinal);
			calldone=0;
		}
		if((timefromval < windowtimeto) && calldone==1)
		{
			alert("Window time From should be after "+windowtimeto);
			document.getElementById("timefrom").value="00:00:00";
			document.getElementById("timeto").value="00:00:00";
			calldone=0;
		}
	}

    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    }); 
	 $('.datepicker').datetimepicker({
        format: 'DD-MM-YYYY'
    });
	/*function windowconfirm()
	{*/
	//var timefromvalparse=Date.parse(timefromval);
	var windowtimeto="<?=$windowtimeto;?>";
	var windowtimestamp="<?=$windowtimetimestamp;?>";
	var releaseratetimefinal="<?=$releaseratetimefinal;?>";

	
		$('#bt-confirm').click( function() {
			var timefromval=document.getElementById("timefrom").value;
			if(timefromval < releaseratetimefinal)
			{
				alert("Window time From should be after release rate time - "+releaseratetimefinal);
			}
			else if(timefromval < windowtimeto)
			{
				alert("Window time From should be after "+windowtimeto);
			}
			else
			{
				$.jqDialog.confirm("Is this last window time?",
				function() { // callback function for 'YES' button
					document.getElementById("confirmval").value='yes';
					document.window_time.action = "/reportmvc/windowtimesubmit"; 
					document.window_time.submit();
				 },		
				function() {  // callback function for 'NO' button
					document.getElementById("confirmval").value='no';
					document.window_time.action = "/reportmvc/windowtimesubmit";
					document.window_time.submit();
				}		
				);
			}
		});
		
		/*if(confirm("Is this last window time?"))
        {
			document.getElementById("confirmval").value='yes';
        	document.window_time.action = "/reportmvc/windowtimesubmit";
        }
        else
        {
			document.getElementById("confirmval").value='no';
      		document.window_time.action = "/reportmvc/windowtimesubmit";
        }*/
	//}
</script>  
@include('includes/footer')


@endsection