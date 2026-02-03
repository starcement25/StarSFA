<?php
ob_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_STORE");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
?>

<?php
if($_GET)
{
	get_data();
}
else if($_POST)
{
	post_data();
}
else
{
	main();
}

ob_end_flush();
function main()
{
?><head>
<title>Edit survey information</title>
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





<div id="main">
	<h4>Edit your own survey form</h4>
    <div class="my-form">
        <form role="form" method="post" onSubmit="return validate();">
        <input type="hidden" name="mode" value="survey_post">
            
            
        

<?php
$sql_survey_edit = "SELECT survey_input_value FROM survey_input_backup WHERE download_time = (SELECT max(download_time) FROM survey_input_backup)";
$res_survey_edit = mysqli_query($link,$sql_survey_edit);
$row_survey_edit = mysqli_fetch_assoc($res_survey_edit);

echo $survey_array = $row_survey_edit['survey_input_value'];

$survey_array = unserialize($survey_array);

/*echo "<pre>";
print_r($survey_array);
echo "</pre>";*/

$survey_array_chunk = array_chunk($survey_array,5);

echo "<pre>";
print_r($survey_array_chunk);
echo "</pre>";

$count = 1;
foreach($survey_array_chunk as $key=>$index)
{
	?>
       	<p class="text-box">
        <label for="box' + i + '">Info <span class="box-number">'<?php echo $count; ?>'</span></label> <input type="text" name="boxes[]"  id="box<?php echo $count; ?>" class="form-control" value="<?php echo $index[0];?>" placeholder="Display name" onblur="change_display_value(<?php echo $count; ?>,this.value);"/><br />
        <select name="boxes_input[]" class="form-control" id="box_input<?php echo $count; ?>" onchange="add_boolean_data(<?php echo $count; ?>,this.value);">
        <?php
			if($index[1] == 'text')
			{?>
        	<option value="text" selected>text</option>
            <option value="boolean">boolean</option>
            <option value="listview">listview</option>
        <?php } ?>
        <?php
			if($index[1] == 'boolean')
			{?>
        	<option value="text">text</option>
            <option value="boolean" selected>boolean</option>
            <option value="listview">listview</option>
        <?php } ?>
        <?php
			if($index[1] == 'listview')
			{?>
        	<option value="text">text</option>
            <option value="boolean">boolean</option>
            <option value="listview" selected>listview</option>
        <?php } ?>
        </select><br />
        <span id="listview_div<?php echo $count; ?>" <?php if($index[1] != 'listview') echo " hidden" ?>>
        	<select name="listview_option[]" class="form-control" id="listview_option<?php echo $count; ?>" >
            <?php if($index[1] == 'listview' && $index[4] == 'checkbox') {?>
            	<option value="checkbox" selected>checkbox</option>
                <option value="radio">radio</option>
            <? } else if($index[1] == 'listview' && $index[4] == 'radio'){ ?>
               	<option value="checkbox" >checkbox</option>
                <option value="radio" selected>radio</option>
            <? } else{ ?>
               	<option value="checkbox" selected>checkbox</option>
                <option value="radio" >radio</option>
            <? } ?>
            </select>
        </span><br />
        <input type="text" name="boxes_input_value[]" class="form-control" value="<?php echo $index[2]; ?>" id="box_input_value<?php echo $count; ?>" placeholder="Display value" disabled  />
        <input type="hidden" id="box_input_value_hidden<?php echo $count; ?>"  name="boxes_input_value_hidden[]" /><br />
        <select name="boxes_input_man[]" class="form-control" id="box_input_man<?php echo $count; ?>" >
        <?php if($index[3] == 'mandatory') {?>
        	<option value="mandatory" selected>mandatory</option>
            <option value="not_mandatory">not mandatory</option>
        <?php } ?>
        <?php if($index[3] == 'not_mandatory') {?>
        	<option value="mandatory" >mandatory</option>
            <option value="not_mandatory" selected>not mandatory</option>
        <?php } ?>
        </select><br />
        <a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a>
        </p>
        
        <?php
		$count++;
}

$row_count = 0;
$row_i = 1;
foreach($survey_array_chunk as $key=>$index)
{
		if($index[1] == 'layer')
		{
			$layout_name = $index[0];
			$type = $index[1] ;
		}
		
		$display_name = $index[0];
		
		if($index[3] == 'mandatory')
			$mandatory = 'Y';
		else 
			$mandatory = 'N';
		
		/*if($row_i == 1)
		{
			$row_id = "RA".$row_count;
		}*/
		
			$row_count = $key+1;
			$row_id = "RA".$row_count;
		
				
		
		if($index[1] == 'text')
			$type = $index[4];
		else if($index[1] == 'boolean')
			$type = 'Y:N';
		else if($index[1] == 'listview')
			$type = $index[4];
			
		echo $sql_insert_survey_input = "INSERT INTO survey_input SET row_id='".$row_id."', action_id='', layout_name='".$layout_name."', display_name='".$display_name."', type='".$type."', mandatory='".$mandatory."', `action`='', download_time=current_timestamp";
		$res_insert_survey_input = mysqli_query($link,$sql_insert_survey_input);
		
		//if($row_count%7 == 0)
		//$row_count++;
		//$row_i++;
}
	//header('location:survey_edit_backup.php?update=success');
?>
		<p><input type="submit" value="Submit" class="btn btn-success btn-xs"/>&nbsp;&nbsp;<input name="add_more" type="button" class="add_more" value="Add More" id="add_more" style="background:#5cb85c; border-radius:3px; color:#FFFFFF; font-size: 12px; border-color: #4cae4c; border:thick; height:24px;"></p>
		</form>
    </div>
</div>

<script>
jQuery(document).ready(function($){
	
	$('.my-form .add_more').click(function(){
		//var n=document.getElementById('box1').value;
		var i = $('span.box-number').length;
		i = i+1;
		var box_html = $('<p class="text-box"><label for="box' + i + '">Info <span class="box-number">' + i + '</span></label> <input type="text" name="boxes[]"  id="box' + i + '" class="form-control" value="" placeholder="Display name" onblur="change_display_value(' + i + ',this.value);"/><br /><select name="boxes_input[]" class="form-control" id="box_input' + i + '" onchange="add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="boolean">boolean</option><option value="listview">listview</option></select><br /><span id="listview_div' + i + '" hidden><select name="listview_option[]" class="form-control" id="listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><input type="text" name="boxes_input_value[]" class="form-control" value="" id="box_input_value' + i + '" placeholder="Display value" disabled  /><input type="hidden" id="box_input_value_hidden' + i + '"  name="boxes_input_value_hidden[]" /><br /><select name="boxes_input_man[]" class="form-control" id="box_input_man' + i + '" ><option value="mandatory" selected>mandatory</option><option value="not_mandatory">not mandatory</option></select><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
			//alert(box_html);
			box_html.hide();
			 $('.my-form p.text-box:last').after(box_html);
        		box_html.fadeIn('slow');
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

function add_boolean_data(k, select_option)
{
	if(select_option == 'boolean')
	{
		document.getElementById('box_input_value'+k+'').value = "Y;N";
		document.getElementById('box_input_value_hidden'+k+'').value = "Y;N";
		document.getElementById('listview_div'+k+'').hidden = true;
	}
	
	if(select_option == 'listview')
	{
		document.getElementById('box_input_value'+k+'').value = document.getElementById('box'+k+'').value;
		document.getElementById('box_input_value_hidden'+k+'').value =document.getElementById('box'+k+'').value;
		document.getElementById('listview_div'+k+'').hidden = false;
		
	}
	
	if(select_option == 'text')
	{
		document.getElementById('box_input_value'+k+'').value = '';
		document.getElementById('box_input_value_hidden'+k+'').value ='';
		document.getElementById('listview_div'+k+'').hidden = true;
	}
	
}

function change_display_value(j, display_text)
{
	if(document.getElementById('box_input'+j+'').value == 'listview')
	{
		document.getElementById('box_input_value'+j+'').value = display_text;
		document.getElementById('box_input_value_hidden'+j+'').value = display_text;
	}
}
</script>

<?php

}

function post_data()
{
	//if($_REQUEST['mode']=='survey_post')
	//{
	//print_r($_POST);
	/*$dataArray=array_merge($_POST['boxes'],$_POST['boxes_input'],$_POST['boxes_input_value'],$_POST['boxes_input_man'],$_POST['listview_option']);
	print_r($dataArray);*/
	//exit();
	/*$dataArray=array();
	$array_box=$_POST['boxes'];
	$array_boxes_input=$_POST['boxes_input'];
	$array_boxes_input_value=$_POST['boxes_input_value_hidden'];
	$array_boxes_input_man=$_POST['boxes_input_man'];
	$array_listview_option=$_POST['listview_option'];
	
	for($count=0;$count <count($array_box);$count++)
	{
		array_push($dataArray,$array_box[$count]);
		array_push($dataArray,$array_boxes_input[$count]);
		if($array_boxes_input[$count]=='text')
		{
			$array_boxes_input_value[$count]=' ';
		}
		array_push($dataArray,$array_boxes_input_value[$count]);
		array_push($dataArray,$array_boxes_input_man[$count]);
		if($array_boxes_input[$count]=='listview')
		{
			$value_listview_option=$array_listview_option[$count];
		}

		if($array_boxes_input[$count]=='text' || $array_boxes_input[$count]=='boolean')
		{
			$value_listview_option=' ';
		}
		array_push($dataArray,$value_listview_option);
	}
	$dataArrayFinal=serialize($dataArray);
	
	//@$survey_id = "SU".$_SESSION['nick_name'].date("YmdHis");
	@$survey_id = "SU"."ADMIN".date("YmdHis");
	
	
	$sql_survey_input = "INSERT INTO survey_input SET survey_input_id='".$survey_id."', survey_input_value = '".$dataArrayFinal."'";
	$res_survey_input = mysqli_query($link,$sql_survey_input);
	
	//echo "Data Updated";
	header('location:survey_edit.php?update=success');
	}*/
	
	$row_count = 1234567;
	foreach($survey_array_chunk as $key=>$index)
	{
		if($index[1] == 'layer')
		{
			$layout_name = $index[0];
			$type = ' ' ;
		}
		
		$display_name = $index[0];
		
		if($index[3] == 'manadatory')
			$mandatory = 'Y';
		else 
			$manadatory = 'N';
		
		$row_id = "RA".$row_count;
		
		if($index[1] == 'text')
			$type = $index[4];
		else if($index[1] == 'boolean')
			$type = $index[4];
		else if($index[1] == 'listview')
			$type = $index[4];
			
		$sql_insert_survey_input = "INSERT INTO survey_input_value SET $row_id='".$row_id."', action_id='', layout_name='".$layout_name."', diplay_name='".$display_name."', type='".$type."', manadatory='".$manadatory."', action='', download_time=current_timestamp";
		$res_insert_survey_input = mysqli_query($link,$sql_insert_survey_input);
		
		$count++;
	}
	header('location:survey_edit_backup.php?update=success');
}

function get_data()
{
	if($_GET['update'] == 'success')
		echo "<center><font color='green'><strong>Data Updated</strong></font></center>";
}
?>
