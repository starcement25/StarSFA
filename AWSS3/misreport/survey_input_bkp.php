<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_ANYHOB");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$sql_get_layer = "SELECT * FROM layer";
$res_get_layer = mysql_query($sql_get_layer);
?>

<script>
function validate()
{
	var n = $('span.box-number').length;
	//alert(i);
	
	for(i=1;i<=n;i++)
	{
		if(document.getElementById('box'+i+'').value.search(/\S/) == -1 || (document.getElementById('box_input'+i+'').value == 'listview' && document.getElementById('box_input_value'+i+'').value == -1))
		{
			alert("Fill out all the Display Name/Display Value fields");
			return false;
		}
		
		
	}
	return true;
}
</script>
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
        <form role="form" method="post" onSubmit="return validate();">
        <input type="hidden" name="mode" value="survey_post">
            <p class="text-box" id="information">
                <label for="box1">No of information</label>
                <input type="text" name="no_of_information[]" id="no_of_information"  value="" class="form-control"/><br />
                <a class="btn btn-success btn-xs add-txt add-box" href="#">Add</a>
            </p>
            <p><input type="submit" value="Submit" class="btn btn-success btn-xs"/>&nbsp;&nbsp;<input name="add_more" type="button" class="add_more" value="Add More" id="add_more" style="background:#5cb85c; border-radius:3px; color:#FFFFFF; font-size: 12px; border-color: #4cae4c; border:thick; height:24px;" hidden></p>
            
        </form>
    </div>
</div>
<script type="text/javascript">
var i;
jQuery(document).ready(function($){
	$('.my-form .add-box').click(function(){
		$('#information').hide();
		$('#add_more').show();
		
       //alert($('.text-box').length);
	    //var n = $('.text-box').length + 10;
        /*if( 11 < n ) {
            alert('Stop it!');
            return false;
        }*/
		var n=document.getElementById('no_of_information').value;
		for(i=1;i<=n;i++)
		{
       	 var box_html = $('<p class="text-box"><label for="box' + i + '">Info <span class="box-number">' + i + '</span></label><br><input type="text" name="display_order[]" id="display_order' + i + '" value="" style="width:50px;"><br /><br /><input type="text" name="boxes[]"  id="box' + i + '" class="form-control" value="" placeholder="Display name" onblur="change_display_value(' + i + ',this.value);"/><br /><select name="layer_input[]" class="form-control" id="layer_input' + i + '"><?php while($row_get_layer = mysql_fetch_array($res_get_layer)){echo "<option>$row_get_layer[layer_name]</option>";} ?></select><br /><select name="boxes_input[]" class="form-control" id="box_input' + i + '" onchange="add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="boolean">boolean</option><option value="listview">listview</option><option value="numeric">numeric</option></select><br /><span id="listview_div' + i + '" hidden><select name="listview_option[]" class="form-control" id="listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><input type="text" name="boxes_input_value[]" class="form-control" value="" id="box_input_value' + i + '" placeholder="Display table name" onblur="add_value_hidden(' + i + ');" disabled  /><input type="hidden" id="box_input_value_hidden' + i + '"  name="boxes_input_value_hidden[]" /><br /><select name="boxes_input_man[]" class="form-control" id="box_input_man' + i + '" ><option value="mandatory" selected>mandatory</option><option value="not_mandatory">not mandatory</option></select><br><input type="text" name="boxes_validation[]"  id="box_validation' + i + '" class="form-control" value="" placeholder="Validation Length" /><span id="action_info' + i + '" style="margin-left:100px;" hidden><label for="action_box' + i + '">ACTION</label><input type="text" name="action_boxes[]"  id="action_box' + i + '" class="form-control" value="" placeholder="Action Display name" onblur="change_action_display_value(' + i + ',this.value);"/></span><br /><br /><span id="action_type' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input[]" class="form-control" id="action_box_input' + i + '" onchange="action_add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="listview">listview</option><option value="numeric">numeric</option></select></span><br /><br /><span id="action_listview_div' + i + '" style="margin-left:100px;" hidden><select name="action_listview_option[]" class="form-control" id="action_listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><br /><span id="action_display_value' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_input_value[]" class="form-control" value="" id="action_box_input_value' + i + '" placeholder="Action Display value" onblur="action_add_value_hidden(' + i + ');" disabled  /></span><input type="hidden" id="action_box_input_value_hidden' + i + '"  name="action_boxes_input_value_hidden[]" /><br /><br /><span id="action_mandatory' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input_man[]" class="form-control" id="action_box_input_man' + i + '" ><option value="Y" selected>mandatory</option><option value="N">not mandatory</option></select></span><br /><br /><span id="action_validation' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_validation[]"  id="action_box_validation' + i + '" class="form-control" value="" placeholder="Validation length" /></span><br /><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
			//alert(box_html);
			box_html.hide();
			 $('.my-form p.text-box:last').after(box_html);
        		box_html.fadeIn('slow');
		}
		return false;
    });
	
		$('.my-form .add_more').click(function(){
		//var n=document.getElementById('box1').value;
		var i = $('span.box-number').length;
		i = i+1;
		var box_html = $('<p class="text-box"><label for="box' + i + '">Info <span class="box-number">' + i + '</span></label> <input type="text" name="boxes[]"  id="box' + i + '" class="form-control" value="" placeholder="Display name" onblur="change_display_value(' + i + ',this.value);"/><br /><select name="layer_input[]" class="form-control" id="layer_input' + i + '"><?php while($row_get_layer = mysql_fetch_array($res_get_layer)){echo "<option>$row_get_layer[layer_name]</option>";} ?></select><br /><select name="boxes_input[]" class="form-control" id="box_input' + i + '" onchange="add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="boolean">boolean</option><option value="listview">listview</option><option value="numeric">numeric</option></select><br /><span id="listview_div' + i + '" hidden><select name="listview_option[]" class="form-control" id="listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><input type="text" name="boxes_input_value[]" class="form-control" value="" id="box_input_value' + i + '" placeholder="Display table name" onblur="add_value_hidden(' + i + ');" disabled /><input type="hidden" id="box_input_value_hidden' + i + '"  name="boxes_input_value_hidden[]" /><br /><select name="boxes_input_man[]" class="form-control" id="box_input_man' + i + '" ><option value="mandatory" selected>mandatory</option><option value="not_mandatory">not mandatory</option></select><br><br><input type="text" name="boxes_validation[]"  id="box_validation' + i + '" class="form-control" value="" placeholder="Validation Length" /><span id="action_info' + i + '" style="margin-left:100px;" hidden><label for="action_box' + i + '">ACTION</label><input type="text" name="action_boxes[]"  id="action_box' + i + '" class="form-control" value="" placeholder="Action Display name" onblur="change_action_display_value(' + i + ',this.value);"/></span><br /><br /><span id="action_type' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input[]" class="form-control" id="action_box_input' + i + '" onchange="action_add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="listview">listview</option><option value="numeric">numeric</option></select></span><br /><br /><span id="action_listview_div' + i + '" style="margin-left:100px;" hidden><select name="action_listview_option[]" class="form-control" id="action_listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><br /><span id="action_display_value' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_input_value[]" class="form-control" value="" id="action_box_input_value' + i + '" placeholder="Action Display value" onblur="action_add_value_hidden(' + i + ');" disabled  /></span><input type="hidden" id="action_box_input_value_hidden' + i + '"  name="action_boxes_input_value_hidden[]" /><br /><br /><span id="action_mandatory' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input_man[]" class="form-control" id="action_box_input_man' + i + '" ><option value="Y" selected>mandatory</option><option value="N">not mandatory</option></select></span><br /><br /><span id="action_validation' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_validation[]"  id="action_box_validation' + i + '" class="form-control" value="" placeholder="Validation length" /></span><br /><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
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
		document.getElementById('box_input_value'+k+'').disabled = true;
		document.getElementById('box_input_value'+k+'').value = "Y;N";
		document.getElementById('box_input_value_hidden'+k+'').value = "Y;N";
		document.getElementById('listview_div'+k+'').hidden = true;
		
		
		/*-------------------------Action Data-------------------------*/
		document.getElementById('action_info'+k+'').hidden = false;
		document.getElementById('action_type'+k+'').hidden = false;
		document.getElementById('action_listview_div'+k+'').hidden = true;
		document.getElementById('action_display_value'+k+'').hidden = false;
		document.getElementById('action_mandatory'+k+'').hidden = false;
		document.getElementById('action_validation'+k+'').hidden = false;
				
	}
	
	if(select_option == 'listview')
	{
		//document.getElementById('box_input_value'+k+'').value = document.getElementById('box'+k+'').value;
		document.getElementById('box_input_value'+k+'').disabled = false;
		//document.getElementById('box_input_value_hidden'+k+'').value =document.getElementById('box'+k+'').value;
		document.getElementById('listview_div'+k+'').hidden = false;
		
		
		/*-------------------------Action Data-------------------------*/
		document.getElementById('action_info'+k+'').hidden = true;
		document.getElementById('action_type'+k+'').hidden = true;
		document.getElementById('action_listview_div'+k+'').hidden = true;
		document.getElementById('action_display_value'+k+'').hidden = true;
		document.getElementById('action_mandatory'+k+'').hidden = true;
		document.getElementById('action_validation'+k+'').hidden = false;
		
	}
	
	if(select_option == 'text')
	{
		document.getElementById('box_input_value'+k+'').disabled = true;
		document.getElementById('box_input_value'+k+'').value = '';
		document.getElementById('box_input_value_hidden'+k+'').value ='';
		document.getElementById('listview_div'+k+'').hidden = true;
		
		/*-------------------------Action Data-------------------------*/
		document.getElementById('action_info'+k+'').hidden = true;
		document.getElementById('action_type'+k+'').hidden = true;
		document.getElementById('action_listview_div'+k+'').hidden = true;
		document.getElementById('action_display_value'+k+'').hidden = true;
		document.getElementById('action_mandatory'+k+'').hidden = true;
		document.getElementById('action_validation'+k+'').hidden = false;
	}
	
	if(select_option == 'numeric')
	{
		document.getElementById('box_input_value'+k+'').disabled = true;
		document.getElementById('box_input_value'+k+'').value = '';
		document.getElementById('box_input_value_hidden'+k+'').value ='double';
		document.getElementById('listview_div'+k+'').hidden = true;
		
		/*-------------------------Action Data-------------------------*/
		document.getElementById('action_info'+k+'').hidden = true;
		document.getElementById('action_type'+k+'').hidden = true;
		document.getElementById('action_listview_div'+k+'').hidden = true;
		document.getElementById('action_display_value'+k+'').hidden = true;
		document.getElementById('action_mandatory'+k+'').hidden = true;
		document.getElementById('action_validation'+k+'').hidden = false;
		
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

function add_value_hidden(k)
{
	document.getElementById('box_input_value_hidden'+k+'').value =document.getElementById('box_input_value'+k+'').value;
}


function change_action_display_value(j, display_text)
{
	if(document.getElementById('action_box_input'+j+'').value == 'listview')
	{
		document.getElementById('action_box_input_value'+j+'').value = display_text;
		document.getElementById('action_box_input_value_hidden'+j+'').value = display_text;
	}
}

function action_add_boolean_data(k, select_option)
{
	if(select_option == 'listview')
	{
		document.getElementById('action_box_input_value'+k+'').disabled = false;
		document.getElementById('action_listview_div'+k+'').hidden = false;
	}
	
	if(select_option == 'text')
	{
		document.getElementById('action_box_input_value'+k+'').disabled = true;
		document.getElementById('action_box_input_value'+k+'').value = '';
		document.getElementById('action_box_input_value_hidden'+k+'').value ='';
		document.getElementById('action_listview_div'+k+'').hidden = true;
	}
	
	if(select_option == 'numeric')
	{
		document.getElementById('action_box_input_value'+k+'').disabled = true;
		document.getElementById('action_box_input_value'+k+'').value = '';
		document.getElementById('action_box_input_value_hidden'+k+'').value ='double';
		document.getElementById('action_listview_div'+k+'').hidden = true;
	}
}

function action_add_value_hidden(k)
{
	document.getElementById('action_box_input_value_hidden'+k+'').value =document.getElementById('action_box_input_value'+k+'').value;
}
</script>
<?php
if($_REQUEST['mode']=='survey_post')
{
	//print_r($_POST);
	/*$dataArray=array_merge($_POST['boxes'],$_POST['boxes_input'],$_POST['boxes_input_value'],$_POST['boxes_input_man'],$_POST['listview_option']);
	print_r($dataArray);*/
	//exit();
				  $dataArray = array();
		$array_display_order = $_POST['display_order'];
				  $array_box = $_POST['boxes'];
		  $array_boxes_input = $_POST['boxes_input'];
	$array_boxes_input_value = $_POST['boxes_input_value_hidden'];
	  $array_boxes_input_man = $_POST['boxes_input_man'];
	  $array_listview_option = $_POST['listview_option'];
		  $array_layer_input = $_POST['layer_input'];
	 $array_boxes_validation = $_POST['boxes_validation'];
	
	   $array_action_display_name = $_POST['action_boxes'];
			   $array_action_type = $_POST['action_boxes_input'];
	$array_action_listview_option = $_POST['action_listview_option'];
		  $array_action_mandatory = $_POST['action_boxes_input_man'];
		$array_action_input_value = $_POST['action_boxes_input_value_hidden'];
		 $array_action_validation = $_POST['action_boxes_validation'];
	
	/*echo "<pre>";
	print_r($array_action_display_name);
	print_r($array_action_type);
	print_r($array_action_listview_option);
	print_r($array_action_input_value);
	print_r($array_action_mandatory);
	echo "</pre>";*/
	
	for($count=0;$count <count($array_box);$count++)
	{
		array_push($dataArray,$array_box[$count]);
		array_push($dataArray,$array_layer_input[$count]);
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
		
		if($array_boxes_input[$count]=='text' || $array_boxes_input[$count]=='boolean' || $array_boxes_input[$count]=='numeric')
		{
			$value_listview_option=' ';
		}
		array_push($dataArray,$value_listview_option);
		array_push($dataArray,$array_boxes_validation[$count]);
		
		if($array_action_display_name[$count] == '')
		{
			$blank = ' ';
			
			array_push($dataArray,$blank);
			array_push($dataArray,$blank);
			array_push($dataArray,$blank);
			array_push($dataArray,$blank);
			array_push($dataArray,$blank);
			array_push($dataArray,$blank);
			
		}
		else
		{
			array_push($dataArray,$array_action_display_name[$count]);
			array_push($dataArray,$array_action_type[$count]);
			if($array_action_type[$count] == 'text' || $array_action_type[$count] == 'numeric')
			{
				$array_action_listview_option[$count] = ' ';
			}
			
			array_push($dataArray,$array_action_listview_option[$count]);
			array_push($dataArray,$array_action_input_value[$count]);
			array_push($dataArray,$array_action_mandatory[$count]);
			array_push($dataArray,$array_action_validation[$count]);
		}
		array_push($dataArray, $array_display_order[$count]);
	}
	
	
	$survey_array_chunk = array_chunk($dataArray,14);
	
	echo "<pre>";
	print_r($survey_array_chunk);
	echo "<pre>";
	
	foreach($survey_array_chunk as $key=>$index)
	{
			
		$display_name = $index[0];
		
		if($index[4] == 'mandatory')
			$mandatory = 'Y';
		else 
			$mandatory = 'N';
			
		$validation = $index[6];
		
				
		$row_count = $key+1;
		$row_id = "RA00".$row_count;
		
		$display_table_name = '';
		
		if($index[2] == 'text')
			$type = $index[5];
		else if($index[2] == 'boolean')
			$type = 'Y:N';
		else if($index[2] == 'listview')
		{
			$type = $index[5];
			$display_table_name = $index[3];
		}
		
		if($index[7] != ' ')
		{
			$action_id = "A00".$row_count;
			$action_display_name = $index[7];
			$action_type = $index[8];
			if($action_type == 'listview')
				$action_type_value = $index[9];
			else if($action_type == 'numeric')
				$action_type_value = $index[8];
			else
				$action_type_value = ' ';
			$action_mandatory = $index[11];
			$action_table_name = $index[10];
			$action_validation = $index[12];
			$action_value = $action_display_name."#".$action_type_value."#".$action_mandatory."#".$action_table_name."#".$action_validation;
		}
		else
		{
			$action_id = '';
			$action_value = '';
		}
			
		echo $sql_insert_survey_input = "INSERT INTO survey_input SET 
											row_id = '".$row_id."', 
										 action_id = '".$action_id."', 
									   layout_name = '".$index[1]."', 
									  display_name = '".$display_name."', 
									 display_order = '".$index[13].",
											  type = '".$type."', 
								display_table_name = '".$display_table_name."', 
										 mandatory = '".$mandatory."', 
										validation = '".$validation."', 
										  `action` = '".$action_value."', 
									 download_time = current_timestamp";
		//$res_insert_survey_input = mysql_query($sql_insert_survey_input);
	}
		
	echo "Data Inserted";
}
?>
</body>
</html>