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
  
  if($currenttime < $windowtimefrom)
	{
       $windowtimeclosedetails=Reverseauction::getWindowtimeclose($dbname);
       if(count($windowtimeclosedetails) >0)
 		{ 
        	foreach($windowtimeclosedetails as $key => $windowtimecloseval){
            	$windowtimeclosefrom=$windowtimecloseval->time_from;
                $windowtimeclosefrom=date('H:i:s',strtotime("$windowtimeclosefrom -30 minutes"));
               	$windowtimeclosefrom=$windowtimeclosefrom;
               }
        }
        else
        {
        	$windowtimeclosefrom='00:00:00';
        }
    }
    else
    {
    $windowtimeclosefrom='00:00:00';
    }
@endphp
  <!--link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /-->

    <!-- page content -->
    <div class="right_col" role="main">
    <form name="create_rverse_auction" method="post" action="/reportmvc/reverseauctionsubmit" onsubmit="javascript:return confirm('Are you ready to release rate?')" />
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
		<table width="95%"   id="maintable" border="1" style="border-collapse: collapse;" align="center" cellpadding="10">
            <thead>
              <tr>
                <td colspan="12" align="center"><b><font size="+2" >Reverse Auction Price Release</font></b></td>
              </tr>
              <tr  align="center" height="20">
                <td  width="9%" style="background:#FFFFCC;" align="center"><b>SKU Code</b></td>
                <td  width="17%" style="background:#FFFFCC;" align="center"><b>SKU Name</b></td>
                 @php
                	$td_with=44/count($plant_list);
                    $dbname=Session::get('DBNAME');
                 @endphp   
                 @foreach($plant_list as $value)
                    <td  width="{{ $td_with }}%" align="center"><b>{{ $value }}</b></td>
                 @endforeach
                 <td width="10%" align="center"><b>Rate Jump -IR<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(%)</b></td>
                <td width="10%" align="center"><b>Counter Bid Jump<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(%)</b></td>
                <td width="10%" align="center"><b>Counter Bid Limit<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(%)</b></td>
              </tr>
              @php $i=1; @endphp
              @foreach($prodlistsconversion as $prodvalconversion)
              	<tr height="30">
                	<td style="background:#FFFFCC; width: 11px;padding-left: 5px;">{{ $prodvalconversion->mapped_prod_code  }}</td><input type="hidden" name="mapped_prod_code_{{ $i }}" value="{{ $prodvalconversion->mapped_prod_code  }}" />
                	<td style="background:#FFFFCC;padding-left: 5px;" width="11%">{{ $prodvalconversion->mapped_prod_desc  }}</td><input type="hidden" 
                    name="mapped_prod_desc_{{ $i }}" value="{{ $prodvalconversion->mapped_prod_desc  }}" />
                    @foreach($plant_list as $value)
                    <td width="11%" align="center"><input type="text" name="{{ $value }}_{{ $i }}" value="{{ (new \App\Helpers\Reverseauction)->getlastreleasedrate($dbname,$value,$prodvalconversion->mapped_prod_code) }}" size="11"  autocomplete="off"/></td>
                    @endforeach
                    <td width="10%" align="center"><input type="text" name="rate_jump_{{ $i }}" value="{{ (new \App\Helpers\Reverseauction)->getlastreleasedratejumpir($dbname,$prodvalconversion->mapped_prod_code) }}" size="10"  autocomplete="off"/></td>
                    <td width="10%" align="center"><input type="text" name="counter_bid_jump_{{ $i }}" value="{{ (new \App\Helpers\Reverseauction)->getlastreleasedratecounterbidjump($dbname,$prodvalconversion->mapped_prod_code) }}" size="10"  autocomplete="off"/></td>
                    <td width="10%" align="center"><input type="text" name="counter_bid_limit_{{ $i }}" value="{{ (new \App\Helpers\Reverseauction)->getlastreleasedratecounterbidlimit($dbname,$prodvalconversion->mapped_prod_code) }}" size="10"  autocomplete="off"/></td>
                 </tr>  
                 @php $i++; @endphp
               @endforeach
               <input type="hidden" name="prodcount" value="{{ ($i-1) }}" />
               <tr height="40"><td colspan="12" align="center"><font size="+1" >GST (%):</font>&nbsp;&nbsp;
               <input type="text" name="GST_percent" id="GST_percent" value="5" onclick="javascript:blank_textbox();" size="4" maxlength="2"/>&nbsp;&nbsp;&nbsp;&nbsp;
               <font size="+1" >Upper Limit (%):</font>&nbsp;&nbsp;
               <input type="text" name="upper_limit_percent" id="upper_limit_percent" value="4" onclick="javascript:blank_textbox_limit();" size="4" maxlength="2"/>
               </td>
               </tr>
               <tr height="30"><td colspan="12" align="center"><input type="button" name="submit1" value=" Submit " onclick="javascript:checking_windowtime();"/></td></tr>
              </thead>
		</table>
        </form>
    </div>
    <!-- /page content -->
    <script language="javascript" type="text/javascript">
     function blank_textbox()
	 {
		 document.getElementById('GST_percent').value="";
	 }
	 function blank_textbox_limit()
	 {
		 document.getElementById('upper_limit_percent').value="";
	 }
	 function checking_windowtime()
	 {
		 var windowtimefrom="<?=$windowtimefrom;?>";
		  var windowtimeto="<?=$windowtimeto;?>";
		 var currenttime="<?=$currenttime;?>";
		 var windowtimeclosefrom="<?=$windowtimeclosefrom;?>";
		 if(currenttime >=windowtimefrom &&  currenttime <= windowtimeto) {
			  alert("Window time is open. Plesae try sometime later.");
		 }
		 else
		 {
			 document.create_rverse_auction.submit();
		 }
		 /*else if(currenttime > windowtimeclosefrom && windowtimeclosefrom!='00:00:00')
		 {
			  alert("Under 30 minutes locking period price release is impossible.");
		 }*/

	 }
    </script>
   
    @include('includes/footer')


@endsection
