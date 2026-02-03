<?php
//$conn=mysqli_connect("localhost","root","","tree");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tree</title>
</head>

<body>
<form action="" method="post">
<input type="text" name="search" />
<input type="submit" name="submit"  />
</form>
<?php
/*function para($dt='NULL')
{
if($dt=='NULL')
{
	echo $dt;
$sql=mysqli_query($GLOBALS['conn'],"select * from para");
$row=mysqli_fetch_assoc($sql);
echo $row['detail'];
}
else
{
$sql=mysqli_query($GLOBALS['conn'],"select * from para");
if($row=mysqli_fetch_array($sql))
{
	$item=$row['detail'];
	$catg='';
	$c=0;
	$cats=explode($dt,$item);
	foreach($cats as $cat)
	{
	//$cat = trim($cat);
	echo $cat;
	
	echo '<b>'.$dt.'</b>';
	
	}
}
else
{
	echo "BLANK";
}

}
}
if(isset($_POST['submit']))
{
$d=$_POST['search'];
$df=' '.$d.' ';
print para($df);
}*/
function highlightkeyword($str, $search) {
    $highlightcolor = "#daa732";
    $occurrences = substr_count(strtolower($str), strtolower($search));
    $newstring = $str;
    $match = array();
 
    for ($i=0;$i<$occurrences;$i++) {
        $match[$i] = stripos($str, $search, $i);
        $match[$i] = substr($str, $match[$i], strlen($search));
        $newstring = str_replace($match[$i], '[#]'.$match[$i].'[@]', strip_tags($newstring));
		//$newstring = preg_replace("/\w*?$match[$i]\w*/i", '[#]'.$match[$i].'[@]', strip_tags($newstring));
    }
 
    $newstring = str_replace('[#]', '<span style="color: '.$highlightcolor.';">', $newstring);
    $newstring = str_replace('[@]', '</span>', $newstring);
    return $newstring;
 
}
if(isset($_POST['submit']))
{
	$string = "My cat school is very good having four storey building. It is like a temple where we go daily to study. First of all in the early morning, we pray to God for our better study and say good morning to our class teacher. Then category we start study accordin";
	$replaced=' '.$_POST['search'].' ';
	$keyword = str_replace($_POST['search'],$replaced,$_POST['search']);
	echo highlightkeyword($string,$keyword);
}
?>

</body>
</html>