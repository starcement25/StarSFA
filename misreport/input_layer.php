<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if($_GET)
{
	disphtml("get_data();");
}
else if($_POST)
{
	disphtml("post_data();");
}
else
{
	disphtml("main();");
}
ob_end_flush();

function main()
{
?>
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
                <label for="box">No of Layers <span class="box-number"></span></label>
                <input type="text" name="boxes[]" id="box_total_layer"  value="" class="form-control"/><br />
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
		var n=document.getElementById('box_total_layer').value;
		for(i=1;i<=n;i++)
		{
       	 var box_html = $('<p class="text-box"><label for="box' + i + '">Layer <span class="box-number">' + i + '</span></label> <input type="text" name="layer_boxes[]"  id="layer_box' + i + '" class="form-control" value="" placeholder="Layer Name"/><br /><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
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
                $(this).text( n + 1 );
            });
        });
        return false;
    });
});
</script>
<?php
}

function post_data()
{
if($_REQUEST['mode']=='survey_post')
{
	//print_r($_POST);
	$dataArray=array_merge($_POST['layer_boxes']);
	/*echo "<pre>";
	print_r($dataArray);
	echo "</pre>";*/
	
	foreach($dataArray as $key)
	{
		$sql_insert_layer = "INSERT INTO survey_layer SET layer_name = '$key'";
		$res_insert_layer = mysqli_query($link,$sql_insert_layer);
	}
	
	header('location:survey_input.php');
}
}
?>
</body>
</html>