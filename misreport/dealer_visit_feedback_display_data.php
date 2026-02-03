<?php
ob_start();
session_start();
require("adminUtils.php");

	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
    <tr class='TDHEAD'><td colspan='5' align='center'>Dealer Visit Feedback</td></tr>
      <tr class="TDHEAD_SUB">
      	<td>Sl No</td>
      	<td>Feedback Category</td>
      	<td>Feedback Sub Category</td>
        <td>Status</td>
      </tr>
    <?
	$sql_feedback = "SELECT * FROM dealer_visit_feedback ORDER BY `order` ASC";
	$res_feedback = mysqli_query($link,$sql_feedback);
	$cnt_feedback=mysqli_num_rows($res_feedback);
	if($cnt_feedback >0){
		$count=1;
	while($row_feedback = mysqli_fetch_assoc($res_feedback)){
		$feedback_category = $row_feedback['feedback_category'];
		$feedback_sub_category = $row_feedback['feedback_sub_category'];
		$acedns = $row_feedback['acedns'];
		if($acedns=='Y') $status='ACTIVE';
		else			 $status='INACTIVE';
			echo "<tr>
				<td>".$count."</td>
				<td>".$feedback_category."</td>
				<td>".$feedback_sub_category."</td>
				<td>".$status."</td></tr>";
		$count++;		
	}
	}
	else{
	echo "<tr>
				<td colspan='5'>No Records</td></tr>";
}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
//}
mysqli_close($link);
?>