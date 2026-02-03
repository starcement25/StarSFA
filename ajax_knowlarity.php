<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<div class= "obutton feature2" data-id="<?php echo $bookID;?>">
    <button class="reserve-button">call</button>
</div>
<script>
$('.reserve-button').click(function(){
var form = new FormData();
form.append("auth_key", "bff82a44-bead-11e5-ab15-06b74f9e776_laranya");
form.append("agent_number", "+919836361358");
form.append("caller_number", "+919474335413");
//form.append("caller_number", "+919836389152");
$.ajax({
    url: "http://etsrds.kapps.in/webapi/laranya/api/laranya_c2c.py",
    data: form,
    type: 'POST',
    contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
    processData: false, // NEEDED, DON'T OMIT THIS
    success: function (data) {
		alert('success');
        console.info(data);
		//alert(data);
    },
	error: function(data) {
            alert("error");
			//alert(data);
        }
});
});
</script>