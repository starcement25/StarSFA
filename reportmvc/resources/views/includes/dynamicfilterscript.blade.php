<script  type="text/javascript">
function zone_state(zone){
  $.ajax({
   url: '{{url("statelisting")}}',
   type: "GET",
     data: {
        'type' :'state',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : zone

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function zone_district(district){
  $.ajax({
   url: '{{url("districtlisting")}}',
   type: "GET",
     data: {
        'type' :'district',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : district

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function zone_hq(hq){
  $.ajax({
   url: '{{url("hqlisting")}}',
   type: "GET",
     data: {
        'type' :'hq',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : hq

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function zone_designation(designation){
  $.ajax({
   url: '{{url("designation")}}',
   type: "GET",
     data: {
        'type' :'designation',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : designation

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function zone_emp(zone){
  var state = $('#state').val();
  var district = $('#district').val();
  var designation = $('#designation').val();
  var hq = $('#hq').val();

  $.ajax({
   url: '{{url("emplisting")}}',
   type: "GET",
     data: {
        'zone':zone,
        'state' : state,
        'hq':hq,
        'designation':designation,
        'district':district

     },
     success: function(resp) {
       $("#emp_select_div").html(resp);
       $("#employee").multiselect({
            includeSelectAllOption: true,
            maxHeight: 200,
            enableFiltering: true
        });
     }
  });
}
function state_district(district){
  $.ajax({
   url: '{{url("districtlisting")}}',
   type: "GET",
     data: {
        'type' :'district',
        'conditionfiledname':'state',
        'conditionfiledvalue' : district

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function state_hq(hq){
  $.ajax({
   url: '{{url("hqlisting")}}',
   type: "GET",
     data: {
        'type' :'hq',
        'conditionfiledname':'state',
        'conditionfiledvalue' : hq

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function state_designation(designation){
  var ndesignation = encodeURIComponent(designation);
  $.ajax({
   url: '{{url("designation")}}',
   type: "GET",
     data: {
        'type' :'designation',
        'conditionfiledname':'state',
        'conditionfiledvalue' : ndesignation

     },
     success: function(resp) {
       $("#designation_select_div").html(resp);
     }
  });
}
function state_emp(state){
  var zone = $('#zone').val();
  var district = $('#district').val();
  var designation = $('#designation').val();
  var hq = $('#hq').val();

  $.ajax({
   url: '{{url("emplisting")}}',
   type: "GET",
     data: {
        'zone':zone,
        'state' : state,
        'hq':hq,
        'designation':designation,
        'district':district

     },
     success: function(resp) {
       $("#emp_select_div").html(resp);
       $("#employee").multiselect({
            includeSelectAllOption: true,
            maxHeight: 200,
            enableFiltering: true
        });
     }
  });
}
function district_hq(hq){
  $.ajax({
   url: '{{url("hqlisting")}}',
   type: "GET",
     data: {
        'type' :'hq',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : hq

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function district_designation(designation){
  $.ajax({
   url: '{{url("designation")}}',
   type: "GET",
     data: {
        'type' :'designation',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : designation

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function district_emp(district){
  var zone = $('#zone').val();
  var state = $('#state').val();
  var designation = $('#designation').val();
  var hq = $('#hq').val();

  $.ajax({
   url: '{{url("emplisting")}}',
   type: "GET",
     data: {
        'zone':zone,
        'state' : state,
        'hq':hq,
        'designation':designation,
        'district':district

     },
     success: function(resp) {
       $("#emp_select_div").html(resp);
       $("#employee").multiselect({
            includeSelectAllOption: true,
            maxHeight: 200,
            enableFiltering: true
        });
     }
  });
}
function hq_designation(designation){
  $.ajax({
   url: '{{url("designation")}}',
   type: "GET",
     data: {
        'type' :'district',
        'conditionfiledname':'zone',
        'conditionfiledvalue' : designation

     },
     success: function(resp) {
       $("#state_select_div").html(resp);
     }
  });
}
function hq_emp(hq){
  var zone = $('#zone').val();
  var state = $('#state').val();
  var designation = $('#designation').val();
  var district = $('#district').val();

  $.ajax({
   url: '{{url("emplisting")}}',
   type: "GET",
     data: {
        'zone':zone,
        'state' : state,
        'hq':hq,
        'district':district,
        'designation':designation

     },
     success: function(resp) {
       $("#emp_select_div").html(resp);
       $("#employee").multiselect({
            includeSelectAllOption: true,
            maxHeight: 200,
            enableFiltering: true
        });
     }
  });
}
function designation_emp(designation){
  var zone = $('#zone').val();
  var state = $('#state').val();
  var hq = $('#hq').val();
  var district = $('#district').val();

  $.ajax({
   url: '{{url("emplisting")}}',
   type: "GET",
     data: {
        'zone':zone,
        'state' : state,
        'hq':hq,
        'district':district,
        'designation':designation

     },
     success: function(resp) {
       $("#emp_select_div").html(resp);
       $("#employee").multiselect({
            includeSelectAllOption: true,
            maxHeight: 200,
            enableFiltering: true
        });
     }
  });
}
</script>
