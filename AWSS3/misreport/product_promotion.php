<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
//if(!$_GET)
	disphtml("main();");
	
function main()
{
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

<?php
if($_GET['prospect_code'])
{
	$sql_prospect_details = "SELECT * FROM product_promotion WHERE prospect_code LIKE '%".$_GET['prospect_code']."%'";
	$res_prospect_details = mysql_query($sql_prospect_details);
	$row_prospect_details = mysql_fetch_array($res_prospect_details);
}
?>

<center>
<table width="60%" class="border" cellpadding="6px">
<tr class="TDHEAD">
	<td colspan="2" align="center">Product Promotion</td>
</tr>
<tr>
	<td align="right">Customer Name<font color="#FF0000">*</font></td>
    <td align="left"><input name="customer_name" type="text" id="customer_name" value="<?php echo $row_prospect_details['prospect_name']; ?>" /></td>
</tr>
<tr>
	<td align="right">PIN Code<font color="#FF0000">*</font></td>
    <td align="left">
    <input name="pincode" type="text" id="pincode" value="<?php echo $row_prospect_details['pin']; ?>" />
    </td>
</tr>
<tr>
	<td align="right">Road/Street Name<font color="#FF0000">*</font></td>
    <td align="left"><input name="streetname" type="text" id="streetname" value="<?php echo $row_prospect_details['street_name']; ?>" /></td>
</tr>
<tr>
	<td align="right">Road/Street No</td>
    <td align="left"><input name="streetno" type="text" id="streetno" value="<?php echo $row_prospect_details['street_no']; ?>" /></td>
</tr>
<tr>
	<td align="right">Building No:</td>
    <td align="left"><input name="buildingno" type="text" id="buildingno" value="<?php echo $row_prospect_details['building_no']; ?>" /></td>
</tr>
<tr>
	<td align="right">Complex / Apartment No</td>
    <td align="left"><input name="apartno" type="text" id="apartno" value="<?php echo $row_prospect_details['apartment_no']; ?>"/></td>
</tr>
<tr>
	<td align="right">Phone No<font color="#FF0000">*</font></td>
    <td align="left"><input name="phone" type="text" id="phone" value="<?php echo $row_prospect_details['phone_no'] ?>" /></td>
</tr>
<tr>
	<td align="right">OIL used</td>
    <td>
    <select name="oilused" id="oilused">
    	<option value="">Select</option>
    <?php
		$sql_oil_used = "SELECT oil_name FROM generic_oil_master";
		$res_oil_used = mysql_query($sql_oil_used);
		while($row_oil_used = mysql_fetch_array($res_oil_used))
		{
			//$product_group_code = $row_oil_used['product_group_code'];
			$product_name = $row_oil_used['oil_name'];
			if($row_prospect_details['oil_used'] == $product_name)
				echo "<option selected>".$product_name."</option>";
			else
				echo "<option>".$product_name."</option>";
		}
	?>
    </td>
</tr>
<tr>
	<td></td>
    <td align="left">
    <input type="hidden" name= "prospect_id" id="prospect_id" value="<?php echo $_GET['prospect_code']; ?>" />
    <input name="submit" id="submit" type="button" value="Submit" onclick="return submit_data();" />
    <input name="update" id="update" type="button" value="Update" onclick="return update_data();" hidden /></td>
</tr>
</table>
<?php
if($_GET['prospect_code']){?>
<br />
<a href="product_promotion_details.php" style="color:blue;">Back</a>
<?php }?>
</center>
<script>
function submit_data()
{
	var customer_name = document.getElementById("customer_name").value;
	if(document.getElementById("customer_name").value.search(/\S/) == -1)
	{
		alert('Customer Name Empty')
		return false;
	}
	
	var pincode = document.getElementById("pincode").value;
	if(document.getElementById("pincode").value.search(/\S/) == -1)
	{
		alert('Pincode Empty')
		return false;
	}
	
	var streetname = document.getElementById("streetname").value;
	if(document.getElementById("streetname").value.search(/\S/) == -1)
	{
		alert('Road/Street Name Empty')
		return false;
	}
	
	var phone = document.getElementById("phone").value;
	phone = phone.replace(/[^0-9]/g, '');
	if(phone.length <10 || phone.length>10)
	{
		alert('Phone Number should be 10 digit in length');
		return false;
	}
	
	var mobile_prefix = phone.substr(0,1);
	if(mobile_prefix != 9 && mobile_prefix != 8 && mobile_prefix != 7)
	{
		alert('Phone Number should begin with either 9 or 8 or 7');
		return false;
	}
	
	var buildingno = document.getElementById("buildingno").value;
	var apartno = document.getElementById("apartno").value;
	var oilused = document.getElementById("oilused").value;
	var streetno = document.getElementById("streetno").value;
	
	$.post("insert_product_promotion.php",
    {
		customer_name: customer_name,
		pincode: pincode,
		streetname: streetname,
		streetno: streetno,
		phone: phone,
		buildingno: buildingno,
		apartno: apartno,
		oilused: oilused
	},
    function(data, status){
        alert(data);
		var r = confirm("Would you like to continue?");
		if (r == true)
		{
			window.location.href = "product_promotion.php";
		} 
		else 
		{
			window.location.href = "sauda_report_main.php";
		}
	});
	//return true;
}
<?php
if($_GET['prospect_code'])
{
	echo "document.getElementById('submit').hidden = true;
	document.getElementById('update').hidden = false;";
}
?>

function update_data()
{
	//alert('Hello');
	var customer_name = document.getElementById("customer_name").value;
	if(document.getElementById("customer_name").value.search(/\S/) == -1)
	{
		alert('Customer Name Empty')
		return false;
	}
	
	var pincode = document.getElementById("pincode").value;
	if(document.getElementById("pincode").value.search(/\S/) == -1)
	{
		alert('Pincode Empty')
		return false;
	}
	
	var streetname = document.getElementById("streetname").value;
	if(document.getElementById("streetname").value.search(/\S/) == -1)
	{
		alert('Road/Street Name Empty')
		return false;
	}
	
	var phone = document.getElementById("phone").value;
	phone = phone.replace(/[^0-9]/g, '');
	if(phone.length <10 || phone.length>10)
	{
		alert('Phone Number should be 10 digit in length');
		return false;
	}
	
	var mobile_prefix = phone.substr(0,1);
	if(mobile_prefix != 9 && mobile_prefix != 8 && mobile_prefix != 7)
	{
		alert('Phone Number should begin with either 9 or 8 or 7');
		return false;
	}
	
	var buildingno = document.getElementById("buildingno").value;
	var apartno = document.getElementById("apartno").value;
	var oilused = document.getElementById("oilused").value;
	var streetno = document.getElementById("streetno").value;
	var prospect_id = document.getElementById("prospect_id").value;
	
	$.post("update_product_promotion.php",
    {
		prospect_id: prospect_id,
		customer_name: customer_name,
		pincode: pincode,
		streetname: streetname,
		streetno: streetno,
		phone: phone,
		buildingno: buildingno,
		apartno: apartno,
		oilused: oilused
	},
    function(data, status){
        alert(data);
		
	});
}
</script>
<?php
}
?>
	