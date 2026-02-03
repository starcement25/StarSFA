<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
	disphtml("main();");
ob_end_flush();	
function main()
{
	if($_POST){
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		$emp_name = $_POST['emp_name'];
		$city = $_POST['city'];
		$survey_type = $_POST['survey_type'];
		
		$_SESSION['start_date'] = $start_date;
		$_SESSION['end_date'] = $end_date;
		$_SESSION['emp_name'] = $emp_name;
		$_SESSION['survey_type'] = $survey_type;
	}
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

<table width="100%" align="center" cellpadding="4" cellspacing="2" border="0">
<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Manage Survey</strong></td>
	</tr> 
    <tr>
    	<td align="center">
        	<form method="POST" action="survey_output_edit_test.php">
            <table cellpadding="4" style="border:2px solid #900;">
            <?php 
						$sql_check_surveytype = "SELECT survey_type, survey_type_details FROM acedns_acednsproduct.survey_form_details WHERE nick_name='".$_SESSION['nick_name']."'";
						$res_check_surveytype = mysqli_query($link,$sql_check_surveytype);
						$row_check_surveytype = mysqli_fetch_assoc($res_check_surveytype);
						$survey_type = $row_check_surveytype['survey_type'];
						$survey_type_details = $row_check_surveytype['survey_type_details'];
						$survey_type_details_array = explode(",",$survey_type_details);
						if($survey_type == 'yes'){?>
                        	<tr class="TDHEAD_SUB">
                            	<td align="right">Survey Type:</td>
                            	<td align="left">
                                <select name="survey_type">
                                <?php
									foreach($survey_type_details_array as $survey_type_value){
										if($survey_type_value == 'mall'){
											$name = 'Mall';
											$value = $survey_type_value;
										}
										else if($survey_type_value == 'hi-street'){
											$name = 'Hi Street';
											$value = $survey_type_value;
										}
										if($survey_type_value == $_POST['survey_type'] || $survey_type_value == $_SESSION['survey_type']){
											$selected = 'selected';
										}
										else{
											$selected = '';
										}
										echo "<option value=\"$value\" $selected>".$name."</option>";
									}
								?>
                                </select>
                                </td>
                            </tr>
                        <?php } ?>
                <tr class="TDHEAD_SUB">
                	<td align="right">Employee:</td>
                	<td align="left">
                    <select name="emp_name">
                    <option selected="selected" value="">Select</option>
					<?php
                    $sql_emp_name = "SELECT emp_code, emp_name FROM employee_master ORDER BY emp_name ASC";
                    $res_emp_name = mysqli_query($link,$sql_emp_name);
                    while($row_emp_name = mysqli_fetch_assoc($res_emp_name)){
                        if($row_emp_name['emp_code'] == $_POST['emp_name'] || $row_emp_name['emp_code'] == $_SESSION['emp_name'])
                        {
                            echo "<option value='$row_emp_name[emp_code]' selected>$row_emp_name[emp_name]</option>";
                        }
                        else
                        {
                            echo "<option value='$row_emp_name[emp_code]'>$row_emp_name[emp_name]</option>";
                        }
                    }
					
					?>
                    </select>
                    <!--Enter City:<input name="city" type="text" />--></td>
                </tr>
            	<tr class="TDHEAD_SUB">
                	<td align="right">Date Range:</td>
                	<td align="left">From:<input type="date" name="start_date" id="start_date" style="height:20px;" value="<?php if($_GET) echo $_SESSION['start_date']; else echo $_POST['start_date'];?>"
required />&nbsp;&nbsp;<font color="#FF0000">*</font>
To:<input type="date" name="end_date" id="end_date" style="height:20px;" value="<?php if($_GET) echo $_SESSION['end_date']; else echo $_POST['end_date'];?>" required /><font color="#FF0000">*</font></td>
                </tr>
                <tr class="TDHEAD_SUB">
                	<td></td>
                	<td align="left"><input name="submit" type="submit" value="Submit" /></td>
                </tr>
            </table>
            </form>
        </td>
</tr>
</table>

<?php
function display($date_condition,$emp_condition,$survey_type_condition){
?>
<div id="display" style="max-height:400px; overflow-y:scroll;">
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" >
            <table width="75%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" >
                <tr class="TDHEAD" > 
                    <td colspan="7" align="center">Survey Information</td>
                </tr>
              <tr class="TDHEAD_SUB">
                <td width="5%" align="center">SI</td>
                <td align="left" style="padding-left:20px;" width="20%">Date</td>
                <td align="left" style="padding-left:20px;" width="20%">Made By</td>
                <td align="left" style="padding-left:20px;" width="">Business Name</td>
                <td align="left" style="padding-left:20px;" width="">Image</td>
                <td align="left" style="padding-left:20px;" width="8%">Locate</td>
                <td  align="left" style="padding-left:20px;" width="8%">Edit</td>
              </tr>
            <?php
                $count = 1;
                $sql_select_survey = "SELECT date_format(substring(LO.date,1,10),'%d-%m-%Y') as survey_date, substring(LO.date,12,8) as survey_time, LO.trans_id, EM.emp_name, SO.value, LO.latt, LO.longi FROM location LO, employee_master EM, survey_input SI, survey_output SO WHERE LO.trans_id = SO.survey_id AND SO.row_id = SI.row_id AND LO.emp_code = EM.emp_code ".$date_condition.$emp_condition.$survey_type_condition." AND SI.type!='menu' AND SI.type != 'layer' GROUP BY SO.survey_id ORDER BY LO.date DESC";
                $res_select_survey = mysqli_query($link,$sql_select_survey);
                while($row_select_survey = mysqli_fetch_assoc($res_select_survey))
                {
					$latt = $row_select_survey['latt'];
					$longi = $row_select_survey['longi'];
					$emp_name = $row_select_survey['emp_name'];
					$survey_date = $row_select_survey['survey_date'];
					$survey_time = $row_select_survey['survey_time'];
					
					$imagelink = "<a href=\"survey_image.php?survey_id=$row_select_survey[trans_id]\" style=\"color:blue;\">Image</a>";
					
                    echo "<tr>
                            <td align=\"center\">$count</td>
                            <td align=\"left\" style=\"padding-left:20px;\">".$survey_date."</td>
                            <td align=\"left\" style=\"padding-left:20px;\">".$emp_name."</td>
                            <td align=\"left\" style=\"padding-left:20px;\">".$row_select_survey['value']."</td>
							<td>$imagelink</td>
                            <td align=\"left\" style=\"padding-left:20px;\"><a href=\"survey_locate.php?get_latt=$latt&get_longi=$longi&emp_name=$emp_name&locate_date=$survey_date&locate_time=$survey_time\" title=\" Locate \" style=\"color: #F00;\">Locate</a></td>
                            <td align=\"left\" style=\"padding-left:20px;\"><a href=\"output_edit_test.php?survey_id=$row_select_survey[trans_id]&survey_type=$_SESSION[survey_type]\"  title=\" Edit \" style=\"color: #5555FF;\">Edit</a></td>
                    </tr>";
                    $count++;
                }
            ?>
            </table>
        </td>
	</tr>
</table>
</div>
<?php } 
if($_POST['submit'] == 'Submit')
{
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$emp_name = $_POST['emp_name'];
	$city = $_POST['city'];
	$survey_type = $_POST['survey_type'];
	
	$_SESSION['start_date'] = $start_date;
	$_SESSION['end_date'] = $end_date;
	$_SESSION['emp_name'] = $emp_name;
	$_SESSION['survey_type'] = $survey_type;
	
	$date_condition = "AND SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."' ";
	
	if($survey_type != '')
		$survey_type_condition = " AND SI.survey_type = '".$survey_type."' ";
	else
		$survey_type_condition = "";
	
	if($emp_name != '')
		$emp_condition = " AND EM.emp_code LIKE '%".$emp_name."%' ";
	else
		$emp_condition = "";
	display($date_condition,$emp_condition,$survey_type_condition);
}

if($_GET['show'] == 'data')
{
	$date_condition = "AND SUBSTRING(LO.date,1,10) BETWEEN '".$_SESSION['start_date']."' AND '".$_SESSION['end_date']."' ";
	if($_SESSION['survey_type'] != '')
		$survey_type_condition = " AND SI.survey_type = '".$_SESSION['survey_type']."' ";
	else
		$survey_type_condition = "";
		
	if($_SESSION['emp_name'] != '')
		$emp_condition = " AND EM.emp_code LIKE '%".$_SESSION['emp_name']."%' ";
	else
		$emp_condition = "";
	display($date_condition,$emp_condition,$survey_type_condition);
}

if($_GET['update'] == 'success')
echo "<center><font color='green'><strong>Data updated successfully</strong></font></center>"; 
}?>
