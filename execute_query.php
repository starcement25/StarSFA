<?php
set_time_limit(0);

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_acednsproduct");

//mysqli_connect(SERVER,USER,PASSWORD);
//mysqli_select_db(DB);

$mysqli = new mysqli(SERVER,USER,PASSWORD,DB);

if($_POST['submit'] == "Submit")
{
	if(strtolower($_POST['user_name']) == "dsc2006" && $_POST['password'] == "admin")
	{
		if($mysqli->connect_errno)
			echo "Error connecting to database".$mysqli->connect_error;
		$sql_db = "SELECT nick_name FROM user_details";
		$res_db = $mysqli->query($sql_db);
		while($row_db = $res_db->fetch_assoc())
		{
			$db_name_array[] = "acedns_".$row_db['nick_name'];
		}
		
		$query = rtrim($_POST['query'], ";");
		$query = explode(";", $query);
		/*echo "<pre>";
		print_r($query);
		print_r($db_name_array);
		echo "</pre>";*/
		
			foreach($db_name_array as $key)
			{
				if($mysqli->select_db($key))
				{
					foreach($query as $exec_query)
					{
					$exec_query = trim($exec_query);
					$sql_execute = $exec_query;
					//$res_execute = $mysqli->query($sql_execute);
					if($mysqli->query($sql_execute))
						$flag=1;
					else
						$flag=0;
						
					}
				}
			}
			header('location:execute_query.php?query=executed');
			//echo "Tables altered";
	}
	else if($_POST['user_name'] != "dsc2006" && $_POST['password'] != "admin")
	header('location:execute_query.php?login=wrong');
}

?>

<script>
function validate()
{
	if(query_data.user_name.value.search(/\S/) == -1)
	{
		alert("Enter username");
		return false;
	}
	
	if(query_data.password.value.search(/\S/) == -1)
	{
		alert("Enter Password");
		return false;
	}
	
	if(query_data.query.value.search(/\S/) == -1)
	{
	alert("Enter Query First");
	return false;
	}
	return true;
}
</script>

<body>
<center>
<br /><br /><br /><br />
<form method="POST" action="" name="query_data" id="query_data" onSubmit="return validate();" >
<table style="border-collapse:collapse; background:#DDDDDD;" cellpadding="5px" >
<tr>
	<td colspan="2" align="center" style="font-size:16px; font-weight:bold; background:#BDB69C;">Execute Query</td>
</tr>
<tr>
	<td>User Name</td>
    <td><input type="text" name="user_name" id="user_name" /></td>
</tr>
<tr>
	<td>Password</td>
    <td><input type="password" name="password" id="password" /></td>
</tr>
<tr>
	<td>Type Query</td>
    <td><input type="text" name="query" style="width:600px; height:50px; font-family:'Courier New', Courier, monospace; color:#0066FF;" placeholder="Type your query/Use ';' separator for multiple queries"/></td>
</tr>
<tr>
	<td></td>
	<td align="left" ><input type="submit" name="submit" value="Submit" /></td>
</tr>
</table>
</form>
<?php
if($_GET['login'] == "wrong")
echo "Wrong username/password";

if($_GET['query'] == "executed")
echo "Query executed successfully";
?>
</center>
</body>