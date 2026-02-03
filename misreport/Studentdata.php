<HTML>
	<BODY BGCOLOR=#kk8844>

<?php

mysqli_connect("localhost","root","");
mysqli_select_db("studentdb");

?>


<?php

if($_POST[sub]==Insert)
{
	$name=$_POST[txtname];
	$address=$_POST[txtadd];
	$gender=$_POST[txtgnd];

	@mkdir('images');
	$link="images/".time()."-".$_FILES[file1][name];

	copy($_FILES[file1][tmp_name],$link);

	$sql="INSERT INTO `studentdata` VALUES (null,'$name','$address','$gender','$link')";
	$query=mysqli_query($link,$sql);
}

if($_GET[del])
{
	$sql="SELECT * FROM `studentdata` WHERE `Id`='$_GET[del]'";
	$query=mysqli_query($link,$sql);

	$row=mysqli_fetch_assoc($query);

	unlink($row[Photo]);

	$sql="DELETE FROM `studentdata` WHERE `Id`='$_GET[del]'";
	$query=mysqli_query($link,$sql);

}



if($_POST[updt]==Update)
{
	if(empty($_FILES[file2][name]))
	{
		$sql="SELECT * FROM `studentdata` WHERE `Id`='$_POST[hid_id]'";
		$query=mysqli_query($link,$sql);
		$row=mysqli_fetch_assoc($query);
		$link=$row[Photo];
		
	}
	else
	{
		$sql="SELECT * FROM `studentdata` WHERE `Id`='$_POST[hid_id]'";
		$query=mysqli_query($link,$sql);
		$row=mysqli_fetch_assoc($query);
		unlink($row[Photo]);

		$link="images/".time()."-".$_FILES[file2][name];
		copy($_FILES[file2][tmp_name],$link);
	}

	$sql="UPDATE `studentdata` SET `Name`='$_POST[txtnm]', `Address`='$_POST[txtad]', `Gender`='$_POST[txtgd]', `Photo`='$link' WHERE `Id`='$_POST[hid_id]'";
	$query=mysqli_query($link,$sql);
	header('location:Studentdata.php');
}



if($_POST[sub]==Show)
{
	echo"<TABLE BORDER=1 ALIGN=CENTER CELLPADDING=3 WIDTH=600>
			<TR>
				<TH>Id</TH>
				<TH>Name</TH>
				<TH>Address</TH>
				<TH>Gender</TH>
				<TH>Photo</TH>
				<TH>Action</TH>
			<TR>";

	$sql="SELECT * FROM `studentdata`";
	$query=mysqli_query($link,$sql);
	$c=1;

	while($row=mysqli_fetch_assoc($query))
	{
		echo"<TR>
				<TD>$c</TD>
				<TD>$row[Name]</TD>
				<TD>$row[Address]</TD>
				<TD>$row[Gender]</TD>
				<TD><img src='$row[Photo]' width=200 height=200></TD>
				<TD><a href='Studentdata.php?del=$row[Id]'>Delete</a>
					<a href='Studentdata.php?updt=$row[Id]'>Update</a></TD>
			 <TR>";
			 $c++;
	}
}


if($_GET[updt])
{
	echo"<TABLE BORDER=1 ALIGN=CENTER CELLPADDING=3 WIDTH=600>
			<TR>
				<TH>Id</TH>
				<TH>Name</TH>
				<TH>Address</TH>
				<TH>Gender</TH>
				<TH>Photo</TH>
				<TH>Action</TH>
			<TR>";

	$sql="SELECT * FROM `studentdata`";
	$query=mysqli_query($link,$sql);
	$c=1;

	while($row=mysqli_fetch_assoc($query))
	{
		if($row[Id]==$_GET[updt])
		{
			echo"<form method='post' action='' enctype='multipart/form-data'>
					<TR>
					<TD>$c<input type='hidden' name='hid_id' value='$row[Id]'></TD>
					<TD><input type='text' name='txtnm' value='$row[Name]'></TD>
					<TD><input type='text' name='txtad' value='$row[Address]'></TD>
					<TD>";
						if($row[Gender]==Male)
						{
						echo"Male<input type='radio' name='txtgd' value='Male' checked>
						Female<input type='radio' name='txtgd' value='Female'>";
						}
						else if($row[Gender]==Female)
						{
						echo"Male<input type='radio' name='txtgd' value='Male'>
						Female<input type='radio' name='txtgd' value='Female' checked>";
						}
					echo"</TD>
					<TD>
						<img src='$row[Photo]' width=200 height=200>
						<input type='file' name='file2'>
					</TD>
					<TD><input type='submit' name='updt' value='Update'></TD>
					</TR>
				 </form>
				 ";
		}
		else
		{
			echo"<TR>
				<TD>$c</TD>
				<TD>$row[Name]</TD>
				<TD>$row[Address]</TD>
				<TD>$row[Gender]</TD>
				<TD><img src='$row[Photo]' width=200 height=200></TD>
				<TD><a href='Studentdata.php?del=$row[Id]'>Delete</a>
					<a href='Studentdata.php?updt=$row[Id]'>Update</a></TD>
			    <TR>";
		}
		$c++;
	}
	echo"</TABLE>";

	
}
?>


<form method="post" action="" enctype=multipart/form-data>

	Name:<input type="text" name="txtname"><br>
	Address:<input type="text" name="txtadd"><br>
	Gender:Male<input type="radio" name="txtgnd" value="Male">Female<input type="radio" name="txtgnd" value="Female"><br>
	Photo:<input type="file" name="file1"><br>
	<input type="submit" name="sub" value="Insert"><br>
	<input type="submit" name="sub" value="Show">

</form>


	</BODY>
</HTML>