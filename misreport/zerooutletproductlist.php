<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$customer_code = $_REQUEST['customer_code'];
	$customer_name = $_REQUEST['customer_name'];
	$ouletarr = $_REQUEST['ouletarr'];
	$ouletarrdecoded=json_decode($ouletarr);
	//print_r($ouletarrdecoded);
echo "<table class=\"border\" width=\"40%\" border=\"1\" style=\"border-collapse:collapse; padding:6px;\" align=\"center\">
<tr class=\"TDHEAD\" > 
					<td colspan=\"10\">Zero Outlet Product List for "."$customer_name"."</td>
				</tr>
    <tr class=\"TDHEAD_SUB\"> 
        <td>Sl</td>
        <td>Product</td>
    </tr>";
	foreach($ouletarrdecoded as $key =>$firstoccurrance)
	{
		if($key ==$customer_code)
		{
		 	 $prod_code_array=array();
			 $count=1;
			foreach($firstoccurrance as $finalval)
			{
				if(!in_array($finalval,$prod_code_array))
				{
					$sqlproddesc="SELECT prod_desc FROM product_master where prod_code='".$finalval."'";
					$rsproddesc=mysqli_query($link,$sqlproddesc);
					$rowproddesc=mysqli_fetch_assoc($rsproddesc);
					$prod_desc=$rowproddesc['prod_desc'];
					echo "<tr><td>$count</td><td>$prod_desc</td></tr>";
					array_push($prod_code_array,$finalval);
					$count++;
				}
			}
		}
	}
echo "</table>";	
mysqli_close($link);
?>
