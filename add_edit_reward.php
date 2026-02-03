<link href="http://www.acedns.in/acednsproduct/css/adminStyle.css" rel="stylesheet" type="text/css" />
<?php
ob_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_RKBK");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
?>

<?php
if($_POST['mode']=="edit")
{
	edit_rewarddetails();
}
else if($_POST['mode']=="add")
{
	add_rewarddetails();
}
else 
{
	main();
}

function add_rewarddetails()
{
	$end_date = $_POST['end_date'];
	$next_date= date('Y-m-d', strtotime($end_date. ' + 365 days')); //Add 365 days to a given date
	
	$sql_reward = "INSERT INTO redeem_details SET scheme_expiry_date='$next_date', points='$_POST[points]',award='$_POST[award]'";
	$res_reward = mysqli_query($link,$sql_reward);
	header('location:add_edit_reward.php?insert=success');
}

function edit_rewarddetails()
{
	$end_date = $_POST['end_date'];
	$next_date= date('Y-m-d', strtotime($end_date. ' + 365 days')); //Add 365 days to a given date
	
	$sql_reward = "UPDATE redeem_details SET scheme_expiry_date='$next_date', points='$_POST[points]',award='$_POST[award]' WHERE sn='$_GET[serial_no]'";
	$res_reward = mysqli_query($link,$sql_reward);
	header('location:add_edit_reward.php?update=success');
}

function main()
{
	echo "<center>";
	echo "<div style=\"height:30px; width:90%; text-align:right;\"><input type=\"submit\" class=\"inplogin\" name=\"submit\" value=\"Add\" onclick=\"show_form();\"></div>";
	
	echo "<table border=\"1\" style=\"border-collapse:collapse; width:90%\" class=\"border\">
			<tr>
				<td class=\"TDHEAD\" colspan=\"5\" align=\"center\">Rewards</td>
			</tr>
			<tr class=\"TDHEAD_SUB\">
				<td><b>SI</b></td>
				<td><b>Date</b></td>
				<td><b>Points</b></td>
				<td><b>Award</b></td>
				<td><b>Action</b></td>
			</tr>";
	$sql_reward_details = "SELECT * FROM redeem_details";
	$res_reward_details = mysqli_query($link,$sql_reward_details);
	$count = 1;
	while($row_reward_details = mysqli_fetch_assoc($res_reward_details))
	{
	echo "<tr>
			<td>$count</td>
			<td>$row_reward_details[scheme_expiry_date]</td>
			<td align=\"right\">$row_reward_details[points]</td>
			<td>$row_reward_details[award]</td>
			<td><a href=\"add_edit_reward.php?serial_no=$row_reward_details[sn]\" style=\"color:blue;\">Edit</a></td>
		  </tr>";
	$count++;
	}
	echo "</table>";
	echo "</center>";
	echo "<br>";
	
	if($_GET['serial_no'])
	{
		$sql_edit_reward = "SELECT * FROM redeem_details WHERE sn='$_GET[serial_no]'";
		$res_edit_reward = mysqli_query($link,$sql_edit_reward);
		$row_edit_reward = mysqli_fetch_assoc($res_edit_reward);
	}
	
?>
<script>
function validate()
{
	if(document.add_reward.end_date.value.search(/\S/) == -1)
	{
		alert("Enter Date");
		return false;
	}
	
	if(document.add_reward.points.value.search(/\S/) == -1)
	{
		alert("Enter Points");
		return false;
	}
	
	if(document.add_reward.award.value.search(/\S/) == -1)
	{
		alert("Enter award name");
		return false;
	}
	
	if(document.getElementById("sn").value!="")
	{
		document.getElementById("mode").value="edit";
	}
	else
	{
		document.getElementById("mode").value="add";
	}
	
	return true;
}
</script>
<center>
<form name="add_reward" id="add_reward" method="POST" action="" onsubmit="return validate();" hidden>
<input type="hidden" name="sn" id="sn" value="<?php echo $row_edit_reward['sn']; ?>" />
<input type="hidden" name="mode" id="mode" value="" />
<table width="400px" class="border" style="border-collapse:collapse;">
<tr class="TDHEAD">
	<td colspan="2" align="center"><b>Enter Reward Details</b><div style="float:right;"><a href="add_edit_reward.php"><img src="close.png" width="25" height="25" /></a></div></td>
</tr>
<tr>
	<td>Date</td>
    <td><input type="date" class="INPUT" name="end_date" value="<?php echo $row_edit_reward['scheme_expiry_date'];?>" /></td>
</tr>
<tr>
	<td>Points</td>
    <td><input type="text" class="INPUT" name="points" value="<?php echo $row_edit_reward['points'];?>"/></td>
</tr>
<tr>
	<td>Award</td>
    <td><input type="text" class="INPUT" name="award" value="<?php echo $row_edit_reward['award']; ?>"/></td>
</tr>
<tr>
	<td></td>
	<td align="left"><input type="submit" class="inplogin" id="submit" name="submit" value="Submit" /></td>
</tr>
</table>
</form>

<?php
if($_GET['insert'] == "success")
echo "Added successfully";

if($_GET['update'] == "success")
echo "Updated successfully";
?>
</center>
<script>
function show_form()
{
	window.location.assign("add_edit_reward.php?show_form=show");
	document.getElementById("add_reward").hidden=false;
}

<?php
if($_GET['serial_no'])
{
echo "document.getElementById(\"add_reward\").hidden=false;";
echo "document.getElementById(\"submit\").value='Update'";
}
if($_GET['show_form'])
echo "document.getElementById(\"add_reward\").hidden=false;";
?>
</script>
<?php
}
?>
