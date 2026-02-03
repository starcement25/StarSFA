<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

?>

<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?>

<?php

if($_POST)
{
	disphtml("view_post_data();");
	
}
else if($_GET)
{
	disphtml("view_get_data();");
	
}
else 
{
	disphtml("main();");
}

ob_end_flush();
	
function view_post_data()
{
echo "<center>";

echo "<div id=\"display\" style=\"max-height: 340px; width:80%; overflow-y: scroll;\">";
if($_POST['submit'] == "Submit")
{
	if($_POST['view'] == "all")
		$condition = "";
	else if($_POST['view'] == "by_card_no")
		$condition = " AND CT.loyalty_card_no='".$_POST['cardno']."'";
	
	$end_date = str_replace("-","",$_POST['end_date']);
	
	$sql_reward_point = "SELECT CT.loyalty_card_no, LCHM.loyalty_card_holder_name, sum(CT.scheme_value), LCHM.total_reward_point, LCHM.total_redeemed_point FROM card_transaction CT, loyalty_card_holder_master LCHM WHERE substring(CT.transaction_id,7,8)<='".$end_date."' AND CT.loyalty_card_no=LCHM.loyalty_card_no".$condition." GROUP BY loyalty_card_no";
	$res_reward_point = mysql_query($sql_reward_point);
	echo "<div style=\"position:fixed;  width:inherit;\">";
	echo "<div style=\"position:relative; width:100%;\">
			<table border=\"1\" style=\"border-collapse:collapse; width:100%;\" class=\"border\">
			<tr>
				<td class=\"TDHEAD\" align=\"center\">Reward Details</td>
			</tr>
			</table>
		  </div>";
	
	echo "<div style=\"position:relative;\">
			<table style=\"border-collapse:collapse; width:100%; \" class=\"border\">
			<tr class=\"TDHEAD_SUB\">
				<td style=\"width:20%\" align=\"center\"><b>Name</b></td>
				<td style=\"width:20%\" align=\"center\"><b>Card Number</b></td>
				<td style=\"width:20%\" align=\"center\"><b>Scheme Value</b></td>
				<td style=\"width:20%\" align=\"center\"><b>Accumulated Points</b></td>
				<td style=\"width:20%\" align=\"center\"><b>Redeemed Points</b></td>
			</tr>
			</table>
		  </div>";
	echo "</div>";
	echo "<br><br>";
		
	echo "<table border=\"1\" style=\"border-collapse:collapse; width:100%;\" class=\"border\">
			
			";
	while($row_reward_point = mysql_fetch_array($res_reward_point))
	{
		if($row_reward_point['loyalty_card_no'] == "")
		continue;
		
		$scheme_point = number_format($row_reward_point['sum(CT.scheme_value)'],2);
		echo "<tr>
				<td style=\"width:20%; height:20px;\"><a href=\"loyalty_reward_details.php?card_no=$row_reward_point[loyalty_card_no]&empl_name=$row_reward_point[loyalty_card_holder_name]\" style=\"color:blue;\">$row_reward_point[loyalty_card_holder_name]</a></td>
				<td style=\"width:20%\">$row_reward_point[loyalty_card_no]</td>
				<td style=\"width:20%\" align=\"right\">$scheme_point</td>
				<td style=\"width:20%\" align=\"right\">$row_reward_point[total_reward_point]</td>
				<td style=\"width:20%\" align=\"right\">$row_reward_point[total_redeemed_point]</td>
			  </tr>";
	}
	echo "</table>";
				
}

echo "</div>";

echo "</center>";
echo "<div><br>
	  <pre>
	     <b>Scheme Value Calculation Method:
	     Vertical		Purchase Value * Rate</b>
	     FMCG		Purchase Value * 0.00
	     HSD		Purchase Value * 0.0030
	     MS			Purchase Value * 0.0035
	     LUBE		Purchase Value * 0.00
	  </pre>
	  </div>";
	  
}

function view_get_data()
{
	echo "<center><div id=\"display_reward_details\" style=\"max-height: 500px; width:80%; overflow-y: scroll; \" >";
	echo "<table border=\"1\" style=\"border-collapse:collapse; width:100%;\" class=\"border\">
			<tr>
				<td class=\"TDHEAD\" colspan=\"4\" align=\"center\">Reward Details '$_GET[empl_name]' Card No.: '$_GET[card_no]'</td>
			</tr>";
	echo "<tr class=\"TDHEAD_SUB\">
			<td><b>Date</b></td>
			<td><b>Transaction Type</b></td>
			<td><b>Purchase Value</b></td>
			<td><b>Points</b></td>
		  </tr>";
	$sql_reward_details = "SELECT substring(transaction_id,7,8) as date, purchase_value, trans_type, scheme_value FROM card_transaction WHERE loyalty_card_no='$_GET[card_no]'";
	$res_reward_details = mysql_query($sql_reward_details);
	while($row_reward_details = mysql_fetch_array($res_reward_details))
	{
		$d = strtotime("$row_reward_details[date]");
		$date = date("d-m-Y", $d);
		$points = number_format($row_reward_details['scheme_value'],2);
		echo "<tr>
				<td>$date</td>
				<td>$row_reward_details[trans_type]</td>
				<td align=\"right\">$row_reward_details[purchase_value]</td>
				<td align=\"right\">$points</td>
			  </tr>";
	}
			  
	echo "</table>";
	echo "</div>";
	echo "<br>";
	
	echo "</center>";
}

function main()
{
?>
<script>
function validate()
{
	var dd = '<?php echo date("Y-m-d"); ?>';
	var end_date = document.reward_details.end_date.value;
	
	if(document.reward_details.end_date.value.search(/\S/) == -1 || end_date>dd)
	{
	alert("Date cannot be blank/Entered date cannot exceed todays date");
	return false;
	}
	
	return true;
}
</script>

<center>
<?php
$today = date("Ymd");
$sql_reward_point = "SELECT CT.loyalty_card_no, LCHM.loyalty_card_holder_name, sum(CT.scheme_value) FROM card_transaction CT, loyalty_card_holder_master LCHM WHERE substring(CT.transaction_id,7,8)<='".$today."' AND CT.loyalty_card_no=LCHM.loyalty_card_no GROUP BY loyalty_card_no";
$res_reward_point = mysql_query($sql_reward_point);
?>


<form method="POST" name="reward_details" action="" onsubmit="return validate();" >
<table  width="400px" style="border-collapse:collapse;" class="border" cellpadding="5px">
<tr class="TDHEAD">
	<td colspan="2" align="center"><b>Loyalty Reward Point</b></td>
</tr>
<tr>
	<td>Date</td>
    <td><input type="date" class="INPUT" name="end_date" value="<?php if($_POST) echo $_POST['end_date'];?>" /></td>
</tr>
<tr>
	<td>View</td>
    <td>
    <?php
	if($_POST['view'] == "all")
    	echo "All<input type=\"radio\" name=\"view\" value=\"all\" onclick=\"hide_cardno();\" checked />
		  By card number<input type=\"radio\" name=\"view\" id=\"bycardno\" value=\"by_card_no\" onclick=\"show_cardno();\" />";
	else if($_POST['view'] == "by_card_no")
		echo "All<input type=\"radio\" name=\"view\" value=\"all\" onclick=\"hide_cardno();\"  />
		  By card number<input type=\"radio\" name=\"view\" id=\"bycardno\" value=\"by_card_no\" onclick=\"show_cardno();\" checked />";
	else
		echo "All<input type=\"radio\" name=\"view\" value=\"all\" onclick=\"hide_cardno();\" checked />
		  By card number<input type=\"radio\" name=\"view\" id=\"bycardno\" value=\"by_card_no\" onclick=\"show_cardno();\"  />";
    ?>
    </td>
</tr>
<tr  id="cardno" hidden>
	<td>Card No</td>
    <td>
    <select name="cardno" id="SELECT">
       	<option selected>Select</option>
            <?php
			while($row_reward_point = mysql_fetch_array($res_reward_point))
			{
				if($row_reward_point['loyalty_card_no'] == '')
					continue;
					
				if($_POST['cardno'] == $row_reward_point['loyalty_card_no'])
					echo "<option value=\"$row_reward_point[loyalty_card_no]\" selected>$row_reward_point[loyalty_card_no]</option>";
				else
					echo "<option value=\"$row_reward_point[loyalty_card_no]\">$row_reward_point[loyalty_card_no]</option>";
			}
			?>
    </select>
    </td>
</tr>
<tr>
	<td></td>
	<td align="left"><input type="submit" name="submit" value="Submit" class="inplogin" /></td>
    
</tr>
</table>
</form>

<br />

</center>

<script>
function show_cardno()
{
	document.getElementById("cardno").hidden = false;
}
function hide_cardno()
{
	document.getElementById("cardno").hidden = true;
}

if(document.getElementById("bycardno").checked == true)
	document.getElementById("cardno").hidden = false;

</script>
<?php
}
?>