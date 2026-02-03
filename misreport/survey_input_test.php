<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
	
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
	$sql_select_survey_type = "SELECT survey_type, survey_type_details FROM acedns_acednsproduct.survey_form_details WHERE nick_name LIKE '%".$_SESSION['nick_name']."%'";
	$res_select_survey_type = mysqli_query($link,$sql_select_survey_type);
	$row_select_survey_type = mysqli_fetch_assoc($res_select_survey_type);
	$survey_type = $row_select_survey_type['survey_type'];
	$survey_type_details = $row_select_survey_type['survey_type_details'];
	$survey_type_details_array = explode(",",$survey_type_details);
	
	if($survey_type == 'yes'){
		$hidden = '';
	}
	else{
		$hidden = 'hidden';
	}
	
	$type = $_REQUEST['type'];
	
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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
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
       	 var box_html = $('<p class="text-box"><label for="box' + i + '">Info <span class="box-number">' + i + '</span></label><br><input type="text" name="display_order[]" id="display_order' + i + '" value="" style="width:50px;"><br /><br /><input type="text" name="boxes[]"  id="box' + i + '" class="form-control" value="" placeholder="Display name" onblur="change_display_value(' + i + ',this.value);"/><br /><span id="survey_type_div' + i + '" <?php echo $hidden; ?>><select name="survey_type_input[]" class="form-control" id="survey_type_input' + i + '" onchange="get_menu(' + i + ',this.value);"><option value="" selected>Select</option><?php foreach($survey_type_details_array as $value){echo "<option>".$value."</option>";} ?></select></span><br /><select name="menu_input[]" class="form-control" id="menu_input' + i + '" onchange="get_layer(' + i + ',this.value);"><option value="" selected>Select</option></select><br /><select name="layer_input[]" class="form-control" id="layer_input' + i + '"><option>Select</option></select><br /><select name="boxes_input[]" class="form-control" id="box_input' + i + '" onchange="add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="boolean">boolean</option><option value="listview">listview</option><option value="numeric">numeric</option></select><br /><span id="listview_div' + i + '" hidden><select name="listview_option[]" class="form-control" id="listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><input type="text" name="boxes_input_value[]" class="form-control" value="" id="box_input_value' + i + '" placeholder="Display table name" onblur="add_value_hidden(' + i + ');" disabled  /><input type="hidden" id="box_input_value_hidden' + i + '"  name="boxes_input_value_hidden[]" /><br /><select name="boxes_input_man[]" class="form-control" id="box_input_man' + i + '" ><option value="mandatory" selected>mandatory</option><option value="not_mandatory">not mandatory</option></select><br><input type="text" name="boxes_validation[]"  id="box_validation' + i + '" class="form-control" value="" placeholder="Validation Length" /><span id="action_info' + i + '" style="margin-left:100px;" hidden><label for="action_box' + i + '">ACTION</label><input type="text" name="action_boxes[]"  id="action_box' + i + '" class="form-control" value="" placeholder="Action Display name" onblur="change_action_display_value(' + i + ',this.value);"/></span><br /><br /><span id="action_type' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input[]" class="form-control" id="action_box_input' + i + '" onchange="action_add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="listview">listview</option><option value="numeric">numeric</option></select></span><br /><br /><span id="action_listview_div' + i + '" style="margin-left:100px;" hidden><select name="action_listview_option[]" class="form-control" id="action_listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><br /><span id="action_display_value' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_input_value[]" class="form-control" value="" id="action_box_input_value' + i + '" placeholder="Action Display value" onblur="action_add_value_hidden(' + i + ');" disabled  /></span><input type="hidden" id="action_box_input_value_hidden' + i + '"  name="action_boxes_input_value_hidden[]" /><br /><br /><span id="action_mandatory' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input_man[]" class="form-control" id="action_box_input_man' + i + '" ><option value="Y" selected>mandatory</option><option value="N">not mandatory</option></select></span><br /><br /><span id="action_validation' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_validation[]"  id="action_box_validation' + i + '" class="form-control" value="" placeholder="Validation length" /></span><br /><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
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
		var box_html = $('<p class="text-box"><label for="box' + i + '">Info <span class="box-number">' + i + '</span></label> <input type="text" name="boxes[]"  id="box' + i + '" class="form-control" value="" placeholder="Display name" onblur="change_display_value(' + i + ',this.value);"/><br /><span id="survey_type_div' + i + '" <?php echo $hidden; ?>><select name="survey_type_input[]" class="form-control" id="survey_type_input' + i + '" onchange="get_menu(' + i + ',this.value);"><option value="" selected>Select</option><?php foreach($survey_type_details_array as $value){echo "<option>".$value."</option>";} ?></select></span><br /><select name="menu_input[]" class="form-control" id="menu_input' + i + '" onchange="get_layer(' + i + ',this.value);"><option value="" selected>Select</option></select><br /><select name="layer_input[]" class="form-control" id="layer_input' + i + '"><option>Select</option></select><br /><select name="boxes_input[]" class="form-control" id="box_input' + i + '" onchange="add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="boolean">boolean</option><option value="listview">listview</option><option value="numeric">numeric</option></select><br /><span id="listview_div' + i + '" hidden><select name="listview_option[]" class="form-control" id="listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><input type="text" name="boxes_input_value[]" class="form-control" value="" id="box_input_value' + i + '" placeholder="Display table name" onblur="add_value_hidden(' + i + ');" disabled /><input type="hidden" id="box_input_value_hidden' + i + '"  name="boxes_input_value_hidden[]" /><br /><select name="boxes_input_man[]" class="form-control" id="box_input_man' + i + '" ><option value="mandatory" selected>mandatory</option><option value="not_mandatory">not mandatory</option></select><br><br><input type="text" name="boxes_validation[]"  id="box_validation' + i + '" class="form-control" value="" placeholder="Validation Length" /><span id="action_info' + i + '" style="margin-left:100px;" hidden><label for="action_box' + i + '">ACTION</label><input type="text" name="action_boxes[]"  id="action_box' + i + '" class="form-control" value="" placeholder="Action Display name" onblur="change_action_display_value(' + i + ',this.value);"/></span><br /><br /><span id="action_type' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input[]" class="form-control" id="action_box_input' + i + '" onchange="action_add_boolean_data(' + i + ',this.value);"><option value="text">text</option><option value="listview">listview</option><option value="numeric">numeric</option></select></span><br /><br /><span id="action_listview_div' + i + '" style="margin-left:100px;" hidden><select name="action_listview_option[]" class="form-control" id="action_listview_option' + i + '" hidden ><option value="checkbox" selected>checkbox</option><option value="radio">radio</option></select></span><br /><br /><span id="action_display_value' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_input_value[]" class="form-control" value="" id="action_box_input_value' + i + '" placeholder="Action Display value" onblur="action_add_value_hidden(' + i + ');" disabled  /></span><input type="hidden" id="action_box_input_value_hidden' + i + '"  name="action_boxes_input_value_hidden[]" /><br /><br /><span id="action_mandatory' + i + '" style="margin-left:100px;" hidden><select name="action_boxes_input_man[]" class="form-control" id="action_box_input_man' + i + '" ><option value="Y" selected>mandatory</option><option value="N">not mandatory</option></select></span><br /><br /><span id="action_validation' + i + '" style="margin-left:100px;" hidden><input type="text" name="action_boxes_validation[]"  id="action_box_validation' + i + '" class="form-control" value="" placeholder="Validation length" /></span><br /><br /><a href="#" class="btn btn-danger btn-xs remove-txt remove-box">Remove</a></p>');
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
		document.getElementById('action_validation'+k+'').hidden = true;
		
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
		document.getElementById('action_validation'+k+'').hidden = true;
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
		document.getElementById('action_validation'+k+'').hidden = true;
		
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

function get_layer(id,menu){
	GenericAjaxFunction('get_survey_layer.php?menu='+menu,'layer_input'+id,0);
}

function get_menu(id,survey_type){
	GenericAjaxFunction('get_menu.php?survey_type='+survey_type,'menu_input'+id,0);
}
</script>
<?php

}

function post_data()
{
	if($_REQUEST['mode']=='survey_post')
	{
					  $dataArray = array();
			$array_display_order = $_POST['display_order'];
					  $array_box = $_POST['boxes'];
			  $array_boxes_input = $_POST['boxes_input'];
		$array_boxes_input_value = $_POST['boxes_input_value_hidden'];
		  $array_boxes_input_man = $_POST['boxes_input_man'];
		  $array_listview_option = $_POST['listview_option'];
			  $array_layer_input = $_POST['layer_input'];
		 $array_boxes_validation = $_POST['boxes_validation'];
			  $array_survey_type = $_POST['survey_type_input'];
					 $array_menu = $_POST['menu_input'];
		
		   $array_action_display_name = $_POST['action_boxes'];
				   $array_action_type = $_POST['action_boxes_input'];
		$array_action_listview_option = $_POST['action_listview_option'];
			  $array_action_mandatory = $_POST['action_boxes_input_man'];
			$array_action_input_value = $_POST['action_boxes_input_value_hidden'];
			 $array_action_validation = $_POST['action_boxes_validation'];
		
		$sql_row_id="SELECT max(row_id) AS row_id FROM survey_input";
		$rs_row_id=mysqli_query($link,$sql_row_id);
		$rec_row_id=mysqli_fetch_assoc($rs_row_id);
		$max_row_id=$rec_row_id['row_id'];	 
		
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
			if($array_survey_type[$count] == ''){
				$blank = ' ';
				array_push($dataArray, $blank);
			}
			else{
				array_push($dataArray, $array_survey_type[$count]);
			}
			array_push($dataArray, $array_menu[$count]);
		}
		$survey_array_chunk = array_chunk($dataArray,16);
		/*echo "<pre>";
		print_r($survey_array_chunk);
		echo "<pre>";*/
		
		foreach($survey_array_chunk as $key=>$index)
		{
			$display_name = $index[0];
			
			if($index[4] == 'mandatory')
				$mandatory = 'Y';
			else 
				$mandatory = 'N';
				
			$validation = $index[6];
			$survey_type = $index[14];
			$survey_menu = $index[15];
			
					
			$row_count = $key+1;
			/*$row_id = "RA00".$row_count;*/
			
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
			else if($index[2] == 'numeric')
				$type = 'double';
			
			if($index[7] != ' ')
			{
				if($max_row_id!=''){
					$max_row_id_temp = $max_row_id;
					$max_row_id_temp++;
					$max_action_id = substr($max_row_id_temp,2);
					$action_id = "A".$max_action_id;
				}
				else{
					$max_action_id = substr($row_id,2);
					$action_id = "A00".$row_count;
				}
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
			if($max_row_id == ''){
				$max_row_id = "RA001";
			}
			else{
				$max_row_id++;
			}
			
			$sql_insert_survey_input = "INSERT INTO survey_input SET 
												row_id = '".$max_row_id."', 
											 action_id = '".$action_id."',
										   survey_type = '".$survey_type."',
											   menu_id = '".$survey_menu."', 
										   layout_name = '".addslashes($index[1])."', 
										  display_name = '".addslashes($display_name)."', 
										 display_order = '".$index[13]."',
												  type = '".$type."', 
									display_table_name = '".$display_table_name."', 
											 mandatory = '".$mandatory."', 
											validation = '".$validation."', 
											  `action` = '".$action_value."', 
										 download_time = current_timestamp"; 
			//exit;							 
			$res_insert_survey_input = mysqli_query($link,$sql_insert_survey_input);
		}
		header('location:survey_input.php?insert=success');	
	}
}
function get_data(){
	if($_GET['insert'] == 'success')
		echo "<center><font color='green'><strong>Data Inserted</strong></font></center>";
}
?>
</body>
</html>