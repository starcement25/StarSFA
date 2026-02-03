<?php
ob_start();
session_start();
require("adminUtils.php");
$yellowcardno = $_REQUEST['yellowcardno'];
if($yellowcardno){
	$sql=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`qty` FROM `yellow_card_details` WHERE `yellow_card_no`='".$yellowcardno."'"));
}

?>

<table border="1" width="600" style="border-collapse:collapse;" class="border" cellpadding="5px">
	<tr class="TDHEAD">
    	<td colspan="2" align="center"><b>Yellow Card Update</b></td>
    </tr>
    
    <tr>
    	<td>Quantity In Bags</td>
        <td >
            <input type="hidden" name="yellowcardnum" id="yellowcardnum" value="<?php echo $yellowcardno ?>" />
        	<input type="text" class="INPUT" name="qty" id="qty" value="<?php echo $sql['qty'] ?>">
        </td>
    </tr>
	<tr>
		
		<td colspan="2" align="center">
        <input type="button"  name="sub" value="Update" onclick="return ajaxsub();"/>
		</td>
	</tr>
    
</table>

