<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if(!$_GET)
{
	disphtml("main();");
}

function main()
{
?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
  webshims.setOptions('waitReady', false);
  webshims.setOptions('forms-ext', {types: 'date'});
  webshims.polyfill('forms forms-ext');
</script>
</head>



<body onLoad="load_mall_details();">
<center>
Search By: Mall Name:<input name="radiooption" id="radiooptionone" type="radio" value="mall">&nbsp;City Name:<input name="radiooption" id="radiooptiontwo" type="radio" value="city">&nbsp;<input type="text" name="search_mall" id="search_mall" placeholder="Enter Mall Name/City"> &nbsp;<input name="Search" type="button" value="Search" onClick="search_mall_details();" />&nbsp;&nbsp;&nbsp;&nbsp;<input name="add" type="button" value="Add" onClick="add_mall_details();" />
<br /><br />
<div id="list_mall_details" style="max-height:480px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;">
</div>
<br />
<br />
<div id="display" style="max-height: 400px; width:90%; overflow-y: scroll;" align="center"></div>

</center>
</body>
<script>
/*-------------------------> Display mall details of a particular mall <-----------------------------*/
function show_mall_data(mall_id)
{
	//alert(mall_id);
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('mall_edit_data.php?mall_id='+mall_id,'display',0);
	document.getElementById("list_mall_details").innerHTML = '';
}
/*-------------------------> Update mall data <-----------------------------*/
function update_mall_data()
{
	var mall_id = document.getElementById("mall_id").value;
	var mall_name = document.getElementById("mall_name").value;
	var street_number = document.getElementById("street_number").value;
	var address = document.getElementById("address").value;
	var landmark = document.getElementById("landmark").value;
	var area = document.getElementById("area").value;
	var city = document.getElementById("city").value;
	var pincode = document.getElementById("pincode").value;
	var state = document.getElementById("state").value;
	var country = document.getElementById("country").value;
	var stdcode = document.getElementById("std_code").value;
	var closed_on = document.getElementById("closed_on").value;
	var upcoming_event = document.getElementById("upcoming_event").value;
	
	if(document.getElementById("mall_name").value.search(/\S/) == -1){
		alert('Provide mall name');
		return false;
	}
	if(document.getElementById("street_number").value.search(/\S/) == -1){
		alert('Provide Street Number');
		return false;
	}
	if(document.getElementById("address").value.search(/\S/) == -1){
		alert('Provide Address');
		return false;
	}
	if(document.getElementById("landmark").value.search(/\S/) == -1){
		alert('Provide Landmark');
		return false;
	}
	if(document.getElementById("area").value.search(/\S/) == -1){
		alert('Provide area');
		return false;
	}
	if(document.getElementById("city").value.search(/\S/) == -1){
		alert('Provide City');
		return false;
	}
	if(document.getElementById("pincode").value.search(/\S/) == -1){
		alert('Provide pincode');
		return false;
	}
	if(document.getElementById("state").value.search(/\S/) == -1){
		alert('Provide state');
		return false;
	}
	if(document.getElementById("country").value.search(/\S/) == -1){
		alert('Provide country');
		return false;
	}
	if(document.getElementById("std_code").value.search(/\S/) == -1){
		alert('Provide std code');
		return false;
	}
	if(document.getElementById("closed_on").value.search(/\S/) == -1){
		alert('Provide closed on');
		return false;
	}
	if(document.getElementById("upcoming_event").value.search(/\S/) == -1){
		alert('Provide upcoming event');
		return false;
	}
	
	if (isNaN(pincode)) 
  	{
		alert("Pincode should be numeric");
		return false;
  	}
	
	if (isNaN(stdcode)) 
  	{
		alert("STD code should be numeric");
		return false;
  	}
	
	var empArray = new Array;
	$('.chk:checked').each(function() {
        empArray.push($(this).val());
    });
	
	//alert(JSON.stringify(empArray)); to alert array
	
	var generalfacilityArrayinput = new Array;
	$('.generalfacility_chk:checked').each(function() {
        generalfacilityArrayinput.push($(this).val());
    });
			
	$.post("update_mall_data.php",
    {
		mall_id: mall_id,
        mall_name: mall_name,
		street_number: street_number,
		address: address,
		landmark: landmark,
		area: area,
		city: city,
		pincode: pincode,
		state: state,
		country: country,
		stdcode: stdcode,
		emparray: empArray,
		closed_on: closed_on,
		upcoming_event: upcoming_event,
		general_facility: generalfacilityArrayinput
    },
    function(data, status){
        alert(data);
	document.getElementById("list_mall_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('display_mall_data.php','list_mall_details',0);
	document.getElementById("display").innerHTML = '';
    });
}
/*-------------------------> Display all mall details <-----------------------------*/
function load_mall_details()
{
	document.getElementById("list_mall_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('display_mall_data_test.php','list_mall_details',0);
}
/*-------------------------> Search mall details <-----------------------------*/
function search_mall_details()
{
	//alert('HEllo');
	document.getElementById("list_mall_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	var mall_name = document.getElementById("search_mall").value;
	
	if(document.getElementById("radiooptionone").checked == true)
	{
		var searchtype = 'mall';
	}
	else if(document.getElementById("radiooptiontwo").checked == true)
	{
		var searchtype = 'city';
	}
	//alert(searchtype);
	GenericAjaxFunction('display_mall_data.php?mall_name='+mall_name+'&searchtype='+searchtype,'list_mall_details',0);
}
/*-------------------------> Add new mall data <-----------------------------*/
function add_mall_details()
{
	document.getElementById("list_mall_details").innerHTML = '';
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('add_mall_details.php','display',0);
}
/*-------------------------> Insert mall data <-----------------------------*/
function insert_mall_data()
{
	var mall_name = document.getElementById("input_mall_name").value;
	var street_number = document.getElementById("input_street_number").value;
	var address = document.getElementById("input_address").value;
	var landmark = document.getElementById("input_landmark").value;
	var area = document.getElementById("input_area").value;
	var city = document.getElementById("input_city").value;
	var pincode = document.getElementById("input_pincode").value;
	var state = document.getElementById("input_state").value;
	var country = document.getElementById("input_country").value;
	var stdcode = document.getElementById("input_stdcode").value;
	var closed_on = document.getElementById("input_closed_on").value;
	var upcoming_event = document.getElementById("upcoming_event").value;
	
	if(document.getElementById("input_mall_name").value.search(/\S/) == -1){
		alert('Provide mall name');
		return false;
	}
	if(document.getElementById("input_street_number").value.search(/\S/) == -1){
		alert('Provide Street Number');
		return false;
	}
	if(document.getElementById("input_address").value.search(/\S/) == -1){
		alert('Provide Address');
		return false;
	}
	if(document.getElementById("input_landmark").value.search(/\S/) == -1){
		alert('Provide Landmark');
		return false;
	}
	if(document.getElementById("input_area").value.search(/\S/) == -1){
		alert('Provide area');
		return false;
	}
	if(document.getElementById("input_city").value.search(/\S/) == -1){
		alert('Provide City');
		return false;
	}
	if(document.getElementById("input_pincode").value.search(/\S/) == -1){
		alert('Provide pincode');
		return false;
	}
	if(document.getElementById("input_state").value.search(/\S/) == -1){
		alert('Provide state');
		return false;
	}
	if(document.getElementById("input_country").value.search(/\S/) == -1){
		alert('Provide country');
		return false;
	}
	if(document.getElementById("input_stdcode").value.search(/\S/) == -1){
		alert('Provide std code');
		return false;
	}
	if(document.getElementById("input_closed_on").value.search(/\S/) == -1){
		alert('Provide closed on');
		return false;
	}
	if(document.getElementById("upcoming_event").value.search(/\S/) == -1){
		alert('Provide upcoming event');
		return false;
	}
	
	
		
	if (isNaN(pincode)) 
  	{
		alert("Pincode should be numeric");
		return false;
  	}
	
	if (isNaN(stdcode)) 
  	{
		alert("STD code should be numeric");
		return false;
  	}
	
	var empArrayinput = new Array;
	$('.input_chk:checked').each(function() {
        empArrayinput.push($(this).val());
    });
	
	var generalfacilityArrayinput = new Array;
	$('.generalfacility_chk:checked').each(function() {
        generalfacilityArrayinput.push($(this).val());
    });
	
	//alert(JSON.stringify(empArrayinput)); //to alert array
	
	$.post("insert_mall_data.php",
    {
		mall_name: mall_name,
		street_number: street_number,
		address: address,
		landmark: landmark,
		area: area,
		city: city,
		pincode: pincode,
		state: state,
		country: country,
		stdcode: stdcode,
		emparray: empArrayinput,
		closed_on: closed_on,
		upcoming_event: upcoming_event,
		general_facility: generalfacilityArrayinput
    },
    function(data, status){
        alert(data);
	document.getElementById("list_mall_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('display_mall_data.php','list_mall_details',0);
	document.getElementById("display").innerHTML = '';
	});
}

function change_status(mall_id,set_status){
	$.post("change_mall_status.php",
    {
		mall_id: mall_id,
		set_status: set_status
	},
    function(data, status){
        alert(data);
	document.getElementById("list_mall_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('display_mall_data_test.php','list_mall_details',0);
	document.getElementById("display").innerHTML = '';
	});
}
</script>
<?php
}
?>
