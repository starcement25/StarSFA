<?php 
session_start();
include "star_connection.php";
$customer_master_STAR = "customer_master_STAR";
$city_data_arr = array();
$server_url = "http://" . $_SERVER['SERVER_NAME']."/cust_location/";
$brand_image_url = $server_url."img/default.png";
$first_mall_latlong = array("lattitude"=>"","longitude"=>"");
$sql2 = "select `latitude`,`longitude` from $customer_master_STAR where `latitude`!='' and `longitude`!='' group by `dns_customer_code` order by `customer_name` asc limit 0,1";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
if($totres2>0){
	$row2=mysql_fetch_assoc($res2);
	$the_latitude = $row2["latitude"];
	$the_longitude = $row2["longitude"];
	$first_mall_latlong["lattitude"] = $the_latitude;
	$first_mall_latlong["longitude"] = $the_longitude;
}
?>
<!doctype html>
<html>
    
<head>
        <title>Customer locator</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- css -->
        <link rel="stylesheet" type="text/css" href="<?php echo $server_url1;?>assets/fonts/helveticaneue/">
        <link href="<?php echo $server_url1;?>assets/img/common/favicon.png" rel="shortcut icon" />
        <link href="<?php echo $server_url1;?>assets/css/normalize.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $server_url1;?>assets/css/styles.css" rel="stylesheet" type="text/css" />
		

        <!-- js -->
        <script src="<?php echo $server_url1;?>assets/js/jquery.min.js"></script>

        <!-- jPList core js and css  -->
        <link href="<?php echo $server_url1;?>dist/css/jplist.core.min.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo $server_url1;?>dist/js/jplist.core.min.js"></script>

        <!-- jPList Google Maps Bundle -->
        <script src="<?php echo $server_url1;?>dist/js/jplist.store-locator-bundle.min.js"></script>

		<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo $server_url1;?>assets/css/bootstrap.min.css">

<!-- Latest compiled JavaScript -->
<script src="<?php echo $server_url1;?>assets/js/bootstrap.min.js"></script>

    <!-- jPlist history bundle -->
    <link href="<?php echo $server_url1;?>dist/css/jplist.history-bundle.min.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $server_url1;?>dist/js/jplist.history-bundle.min.js"></script>

    <!-- jPList sort bundle -->
    <script src="<?php echo $server_url1;?>dist/js/jplist.sort-bundle.min.js"></script>

    <!-- jPList pagination bundle -->
    <link href="<?php echo $server_url1;?>dist/css/jplist.pagination-bundle.min.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $server_url1;?>dist/js/jplist.pagination-bundle.min.js"></script>

    <!-- jPlist toggle filters bundle -->
    <link href="<?php echo $server_url1;?>dist/css/jplist.filter-toggle-bundle.min.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $server_url1;?>dist/js/jplist.filter-toggle-bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBhHI9Y1QreJYx0ldZ1_sxVJBfY4R_0jI&libraries=places,geometry&region=IN"></script>
    <style type="text/css">
		    	@font-face {
		  font-family: 'HelveticaNeue';
		  src: url('<?php echo $server_url1;?>assets/fonts/helveticaneue/HelveticaNeue Light.ttf') format('truetype');
		  font-weight: normal;
		  font-style: normal;
		}

		body{
			font-family: HelveticaNeue;
		}
		
		@media screen and (max-width: 768px) {
		    
		    .powered{
		        text-align:center !important;
		        padding-top:0px;
		    }
		     .logo{
		        text-align:center !important;
		    }
		    
		    .allstores
		    {
		        padding-top:0px !important;
		    }
		    
		    
		}
		
		.powered
		{
		    text-align:right;
		    padding-top:30px;
		}
		
		.allstores
		{
		    text-align:center;
		    padding-top:15px;
		}
.stores .store .arrow_on_st_dtls{
    position: absolute;
    right: 9px;
    top: 33%;
	width:16px;
}
.jplist-map-panel .jplist-marker-popup .arrow_on_st_dtls{
	display:none;
}
		/*.onethird
		{
			width: 33%;
			float:left;
			background-color:#e2e2e2;
		}*/
    </style>

    </head>
    <body>

        <div class="fcuk-header container-fluid" style="background-color:#eeeeee;">
        	<div class="row">
            	<div class="col-md-4 col-sm-12 logo" style="height:85px;" >
    		        <img src="<?php echo $brand_image_url;?>" style="height:75px;" >
    		    </div>
    		    <div class="col-md-4 col-sm-12" >
    		        <h3 class="allstores" > </h3>
    		    </div>
    		    <div class="col-md-4 col-sm-12 powered" style="">
    		    			
    		    </div>
		   </div>	
        </div>

        <div class="wrapper">

        <!-- <div class="lead" ></div> -->	

    <!--- DEMO START -->
    <div id="demo" class="jplist-store-locator-bundle">

        <!-- controls panel -->
      
<div class="row">
<div class="col-sm-12">
<div class="form-group">
<label for="Filter by city" style="margin-right:10px;">Customer Details</label>
<span class="loader_msg" style="display:inline-block;"></span>
</div>
</div>
</div>
        <div class="jplist-google-maps-row">

            <!-- stores box -->
<div class="stores-box">

    <div class="stores" id="fcuk">

    </div>
    <!-- no results found -->
    <div class="jplist-no-results">
        <p>Loading....</p>
    </div>

</div>

            <!-- map panel -->
            <div class="jplist-map-panel">

                <div
                        data-control-type="google-maps-control"
                        data-control-name="google-maps-control"
                        data-control-action="google-maps-control"
                        data-latitude="<?php echo $first_mall_latlong["lattitude"];?>"
                        data-longitude="<?php echo $first_mall_latlong["longitude"];?>"
                        data-map-type="ROADMAP"
                        data-initial-zoom="8"
                        data-store-zoom="17"
                        data-geolocation="false"
                        data-open-popup-store-click="true"
                        data-styles="mapStyles"
                        class="jplist-map">
                </div>

            </div>
        </div>

    </div>
    <!--- DEMO END -->
        </div>

        <!-- footer -->
        <div id="footer" >
            <p style="padding: 10px 0 !important; margin:0px">
            <a href="https://www.qoie.in/" target="_blank"><img src="<?php echo $server_url1;?>assets/img/common/logo.png" alt="QOIE" style="height:65px;" ></a>
            </p>
        </div>
    </body>
    <!-- jPList start -->
    <script>
        var mapp = function(){
            /**
             * user defined functions
             */
            jQuery.fn.jplist.settings = {

                mapStyles: [
                    {
                        featureType: 'all',
                        stylers: [
                            { saturation: -80 }
                        ]
                    },{
                        featureType: 'road.arterial',
                        elementType: 'geometry',
                        stylers: [
                            { hue: '#00ffee' },
                            { saturation: 50 }
                        ]
                    },{
                        featureType: 'poi.business',
                        elementType: 'labels',
                        stylers: [
                            { visibility: 'off' }
                        ]
                    }
                ]
            };

            $('#demo').jplist({
                itemsBox: '.stores'
                ,itemPath: '.store'
                ,panelPath: '.jplist-panel, .jplist-map-panel'
            });

        };

    </script>
    <script>
         $('document').ready(function(){ 
           // mapp();
		load_stores_by_city();
		
			
	function load_stores_by_city(){
		var l_img_url = "<?php echo $server_url1."img/";?>ajax-loader.gif";
		var img_data = '<img src="'+l_img_url+'">';
		jQuery(".loader_msg").html(img_data);
	jQuery.ajax({
	type: "POST",
	url: "<?php echo $server_url1;?>ajax_load_customer_data.php",
	data: "",
	dataType:"JSON",
	success: function(data){
	if(data.process_status=="YES"){
		jQuery(".loader_msg").html("");
		
		jQuery("#fcuk").html(data.store_data_wrt_brand);
	mapp();
	
	}else{
		jQuery(".loader_msg").html("");
	alert(data.process_msg);
	}
	}
	});
	}
        });
    </script>
    <!-- google maps -->
    
</html>


<?php

?>