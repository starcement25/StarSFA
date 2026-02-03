<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
?>
<html>
<header>
<script language="javascript" type="text/javascript">
window.onload = function() {
  document.getElementById("documentID").focus();
};
</script>
</header>
 <body>
 	<form action="read-barcode.php?val=success" method="post" name="barcodereader">
    <input type="hidden" name="mode" value="readbarcode" />
    <table border="0" width="40%" align="center">
    	<?php if($_REQUEST['val']=='success'){?>
        	<tr><td align="center"><font color="#009966"><b>Successfully Captured - <?php echo $_POST['documentID'];?> !!</b></font></td></tr>
          <?php }?>  
    </table>
 	<table width="40%" align="center" border="1">
    	 <tr>
                <td>
                    <table border="0" width="40%" align="center">
                        <tr>
                            <td align="center"><b>Capture Barcode</b></td>
                         </tr>
                        <tr>
                            <td align="center">
                                <input type="text" name="documentID" id="documentID" onBlur="javascript:document.barcodereader.submit();">
                            </td>
                         </tr> 
                         <tr>
                            <td align="center" >&nbsp;</td>
                         </tr> 
                    </table>
                </td>
           </tr>   
    </table>
    </form>
 </body>
</html>	
<?php
	if($_REQUEST['mode']=='readbarcode')
	{
		$barcodedata=$_POST['documentID'];
		if($barcodedata !=''){
			$sqlinsert="INSERT INTO barcode_details SET barcode='".$barcodedata."'";
			mysqli_query($link,$sqlinsert);
		}
		header("location:http://salesmpower.acedns.in/read-barcode.php?val=success");
		exit();
	}
?>	
