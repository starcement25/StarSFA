<?php
session_start();
if($_REQUEST['mode']=='delete-event'){
	 deleteEvent();
 }
 else if($_REQUEST['mode']=='log-out')
 {
	 logout();
 }
 else
 {
	 main();
 }
 function  main(){

	// Zend library include path
	set_include_path("../ZendGdata-1.12.0/library");

	include_once("../Google_Spreadsheet.php");

	$u = "apps@forcepower.in";
	$p = "Forcepower12#";
	$spreadsheetname='CSCLUB';
	$sheetname='Event';

	$ss = @new Google_Spreadsheet($u,$p);
	$ss->useSpreadsheet("$spreadsheetname");
	$ss->useWorksheet("$sheetname");

	$rows = $ss->getRows();
?>
<script language="javascript" type="text/javascript">
function delete_event(ID)
{
	var UserResp = window.confirm("Are you sure to remove this Event?");
	if( UserResp == true )
	{
		document.frm_opts.mode.value='delete-event';
		document.frm_opts.row_id.value=ID;
				document.frm_opts.submit();
	}
}
function logout()
{
		document.frm_opts.mode.value='log-out';
		document.frm_opts.submit();
}
</script>
<!DOCTYPE html>
<html>
    <!-- This code is only meant for previewing your Reflow design. -->
    <head>
	<link rel="stylesheet" href="css/boilerplate.css" />
	<link rel="stylesheet" href="css/style.css" />
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0" />
    <style>
#customers
{
font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
width:100%;
font-size:12px;
border-collapse:collapse;
}
#customers td, #customers th
{
font-size:1em;
border:1px solid #98bf21;
padding:3px 7px 2px 7px;
}
#customers th
{
font-size:1.1em;
text-align:left;
padding-top:5px;
padding-bottom:4px;
background-color:#961114;
color:#ffffff;
}
#customers tr.alt td
{
color:#000000;
background-color:#EAF2D3;
}

	</style>
    </head>
    <body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="header" class="clearfix">
            <img id="image" src="img/csc%20logo.png" class="image" />
            <div id="box" class="clearfix">
                <p id="text">
                Welcome <?php echo strtoupper($_SESSION['user_name']);?>
                </p>
            </div>
        </div>
        <div id="menu" class="clearfix">
            <!--input id="input" type="button" value="Event"></input-->
            <input id="input1" type="button" value="Send Messages" onClick="javascript:window.location='send-message.php'"></input>
            <input id="input2" type="button" value="View Messages" onClick="javascript:window.location='messageDetails.php'"></input>
            <input id="input3" type="button" value="Member details" onClick="javascript:window.location='memberDetails.php'"></input>
            <input id="input4" type="button" value="Logout" onClick="javascript:logout();"></input>
        </div>
        <input id="input5" type="button" value="Add Event" onClick="javascript:window.location='add-event.php'"></input>
        <div id="tablebox" class="clearfix">
        <div id="heading" class="clearfix"><h2>Event Details</h2></div>
            <table id="customers">
<tr>
  <th>ID</th>
  <th>Category</th>
  <th>Name</th>
  <th>Date</th>
  <th>Start Time</th>
  <th>End Time</th>
  <th>Venue</th>
  <th>Description</th>
  <th>Fees</th>
  <th>Contact person's name</th>
  <th>Contact person's no</th>
  <th>Contact person's Email</th>
  <th>Image</th>
  <th>Action</th>
 </tr>
<?php
    if ($rows)
    {
      //print_r($rows);
     foreach ($rows as $z => $values) {
    ?>
<tr>
<?php
		$count=12;
		foreach ($values as $key => $value) {?>
<td> <?php echo $value;?></td>
<?php }?>
<td>
<input  name="input1-<?php echo $values['eventid'];?>" type="button" value="Edit" onClick="javascript:window.location='edit-event.php?event_id=<?php echo $values['eventid']; ?>';">
                </input><br/>
                <input  type="button" value="Delete" onClick="javascript:delete_event('<?php echo $values['eventid'];?>');"></input>
</td>
</tr>
<?php }
 }
?>
</table>
        </div>
    </div>
<form name="frm_opts" action="eventDetails.php" method="post" >
    <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
    <input type="hidden" name="row_id" value="">
</form>
<footer>
<p id="foottext">Copyright &copy; <?=date('Y');?> - <?=(date('Y')+1);?>  - All Rights Reserved</p>
</footer>
    </body>
</html>
<?php }

function logout()
{
	if($_SESSION['user_name'] != "")
	{
		 $_SESSION['user_name'] == "";
		 $_SESSION['password'] == "";

		 unset($_SESSION['admin_id']);
		 unset($_SESSION['admin_login']);
		 session_destroy();
	}
	header('location: index.php');
}
function deleteEvent(){
	$event_id=$_REQUEST['row_id'];
// Zend library include path
set_include_path("../ZendGdata-1.12.0/library");
	include_once("../Google_Spreadsheet.php");

$u = "apps@forcepower.in";
$p = "apps123456";
$spreadsheetname='CSCLUB';
$sheetname='Event';

	$ss = new Google_Spreadsheet($u,$p);
	$ss->useSpreadsheet("$spreadsheetname");
	$ss->useWorksheet("$sheetname");

	$versionupdate = new Google_Spreadsheet($u,$p);
	$versionupdate->useSpreadsheet("CSCLUB");
	$versionupdate->useWorksheet("VERSION-UPDATE");
	$rowsval = $versionupdate->getRows();
	foreach ($rowsval as $z => $updateval) {
		$event_version=$updateval['event'];
		$member_version=$updateval['member'];
		$info_version=$updateval['info'];
		$feedback_version=$updateval['feedback'];
	}
if ($ss->deleteRow("eventid=".$event_id))
	{
		$event_version_latest=$event_version+1;
		$versionupdateval = array("event" => $event_version_latest, "member" => $member_version, "info" => $info_version, "feedback" => $feedback_version);
		$versionupdate->updateRow($versionupdateval,'event="'.$event_version.'"');

		$GLOBALS["msg"]="Event information deleted successfully.";
		 main();
	}
}
?>
