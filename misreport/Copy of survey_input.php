<!DOCTYPE html>
<html>
<head>
<title>Add survey information</title>
<script type="text/javascript" src="//code.jquery.com/jquery-latest.js"></script>
<link rel="stylesheet" href="bootstrap.min.css" />
<style type="text/css">
<!--
#main {
    max-width: 800px;
    margin: 0 auto;
}
-->
</style>
</head>
<body>
<div id="main">
    <h4>Design your own survey form</h4>
    <div class="my-form">
        <form role="form" method="post">
        <input type="hidden" name="mode" value="survey_post">
            <p class="text-box">
                <label for="box1">No of information <span class="box-number"></span></label>
                <input type="text" name="boxes[]" id="box1"  value="" class="form-control"/><br />
                <a class="btn btn-success btn-xs add-txt add-box" href="#">Add</a>
            </p>
            <p><input type="submit" value="Submit" class="btn btn-success btn-xs"/></p>
        </form>
    </div>
</div>
<script type="text/javascript">
var i;
jQuery(document).ready(function($){
    $('.my-form .add-box').click(function(){
       //alert($('.text-box').length);
	    //var n = $('.text-box').length + 10;
        /*if( 11 < n ) {
            alert('Stop it!');
            return false;
        }*/
		var n=document.getElementById('box1').value;
		for(i=1;i<=n;i++)
		{
       	 var box_html = $('<p class="text-box"><label for="box' + i + '">Info <span class="box-number">' + i + '</span></label> <input type="text" name="boxes[]"  id="box' + i + '" class="form-control" value="Display name"/><br /><select name="boxes_input[]" class="form-control" id="box_input' + i + '"><option value="text">text</option><option value="boolean">boolean</option><option value="listview"></option></select><br /><input type="text" name="boxes_input_value[]" class="form-control" value="" id="box_input_value' + i + '"  /><span style="color:red">[For boolean value should be (;) seperated]</span><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
			//alert(box_html);
			box_html.hide();
			 $('.my-form p.text-box:last').after(box_html);
        		box_html.fadeIn('slow');
		}
		return false;
    });
    $('.my-form').on('click', '.remove-box', function(){
        $(this).parent().css( 'background-color', '#FF6C6C' );
        $(this).parent().fadeOut("slow", function() {
            $(this).remove();
            $('.box-number').each(function(index){
                $(this).text( index + 1 );
            });
        });
        return false;
    });
});
</script>
<?php
if($_REQUEST['mode']=='survey_post')
{
	//print_r($_POST);
	$dataArray=array_merge($_POST['boxes'],$_POST['boxes_input'],$_POST['boxes_input_value']);
	print_r($dataArray);
	
	echo $dataArrayFinal=serialize($dataArray);
}
?>
</body>
</html>