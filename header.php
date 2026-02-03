<?php
include "connection.php";
include "check.php";
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Broker Account Creation</title>

<!-- CSS -->
<link href="style/css/transdmin.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie7.css" /><![endif]-->

<!-- JavaScripts-->
<script type="text/javascript" src="style/js/jquery.js"></script>
<script type="text/javascript" src="style/js/jNice.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>

<?php
$access_type = explode(",",$_SESSION['access_type']);
$access = 'x';
foreach($access_type as $key)
{
	if($key == 'add')
		$access .= 'a';
	if($key == 'update')
		$access.= 'u';
	if($key == 'view')
		$access.= 'v';
}


echo "<div id=\"welcome\" style=\" background:url(divbkgrnd.jpeg); padding-top:5px; height:20px; text-align:right; padding-right:50px;\" ><span style=\"vertical-align:middle;\"><font style=\"vertical-align:bottom; font-weight:bold; font-size:14px; font-family:'Times New Roman', Times, serif;\">Hi <a href=\"user_access_details.php\">$_SESSION[user_name]</a></font></span></div>";

?>
<!--<div id="clickme" style="width:100%; height:10px;" onmouseover="show_greetings();"></div>-->

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="#"><span>ACEdns</span></a></h1>
                
        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <center>
        <ul id="mainNav">
        	<li><a href="add_employee.php">ADD EMPLOYEE</a></li>
           	<li><a href="add_customer.php">ADD CUSTOMER</a></li> <!-- Use the "active" class for the active menu item  -->
            <li class="logout"><a href="logout.php">LOGOUT</a></li>
        </ul>
        </center>        
        <!-- // #end mainNav -->
<div id="containerHolder">
			<div id="container">
        		<div id="sidebar">
                	<ul class="sideNav">
                        <li><a href="company_details.php">Home</a></li>
                         <li><a href="employee_details.php">Employee Details</a></li>
                        <li><a href="">Customer Details</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                    <!-- // .sideNav -->
                </div>
<script>
<?php
//if($access == 'xv')
if(strpos($_SESSION['access_type'],"view") === 0)
echo "document.getElementById(\"mainNav\").hidden=true;";
?>
</script>

<script>

/*$(document).ready(function(){
      $("#welcome").fadeToggle(4000);
    });


function show_greetings()
{
	$("#welcome").fadeToggle(2000);
}*/
	
</script>