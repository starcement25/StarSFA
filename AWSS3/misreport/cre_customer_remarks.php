<?php
ob_start();
session_start();
require("adminUtils.php");
$customer_code=$_GET['customer_code'];
$today=date("Ymd");
//$sqltransaction=mysql_fetch_array(mysql_query("SELECT * FROM CRM_transaction WHERE customer_code='".$customer_code."' AND SUBSTRING(call_id,-14,8)='".$today."'"));
$customername=mysql_fetch_array(mysql_query("SELECT customer_name FROM `customer_master` WHERE customer_code='".$customer_code."'"));
?>

<table border="1" width="600" style="border-collapse:collapse;" class="border" cellpadding="5px">
    <tr>
    <td>
    <table style="width:800px">
	<tr class="TDHEAD">
    	<td colspan="3" align="center"><b>Remarks for <?php echo $customername['customer_name']; ?></b></td>
    </tr>
    <tr><td>
    <input type="radio" name="restype" id="restype" value="Responded" onclick="show1();" />Responded
    <input type="radio" name="restype" id="restype" value="Phone Ringing" onclick="show2();" />Phone Ringing
    <input type="radio" name="restype" id="restype" value="No Not Available" onclick="show2();" />No. Not Available
    <input type="radio" name="restype" id="restype" value="Switch Off" onclick="show2();" />Switch Off
    <input type="radio" name="restype" id="restype" value="Wrong No" onclick="show2();" />Wrong No.
    <input type="radio" name="restype" id="restype" value="Call Back Later" onclick="show2();" />Call Back Later
    </td>
    </tr>
    </table>
    </td>
    </tr>
    
    <tr style="display:none;" id="ph_responce"><td>
    <?php
	$sql_ques="SELECT * FROM CRM_questioners WHERE status='1'";
	$res_ques=mysql_query($sql_ques);
	$cnt=mysql_num_rows($res_ques);
	?>
     <input type="hidden" name="customercode" id="customercode" value="<?php echo $_REQUEST['customer_code'];?>">
    <!--input type="hidden" name="call_id" id="call_id" value="<?php //echo $sqltransaction['call_id'];?>"-->
    <input type="hidden" name="total_ques" id="total_ques" value="<?php echo $cnt; ?>">
    <table style="width:800px">
	<tr class="TDHEAD">
    	<td colspan="3" align="center"><b>Remarks</b></td>
    </tr>
    
    <tr>
    	<td colspan="3" align="center">CRE Feedback</td>
    </tr>
    <?php
	$i=1;
	
	while($row_ques=mysql_fetch_array($res_ques))
	{
	?>
    <tr>
    <td><?php echo $i.'.'; echo $row_ques['questioners'];?></td>
    <td>
    <input type="hidden" name="ques[<?php echo $row_ques['id'] ?>]" id="ques[<?php echo $row_ques['id'] ?>]" value="<?php echo $row_ques['id'] ?>" />
    <input type="radio" name="ans[<?php echo $row_ques['id'] ?>]" id="ans<?php echo $i?>1" value="1" <?php if($row_ques['id']==8) { ?>onclick="hide_drop_box();"<?php } ?>/>Yes 
    <input type="radio" id="ans<?php echo $i?>0" name="ans[<?php echo $row_ques['id'] ?>]" value="0" <?php if($row_ques['id']==8) { ?>onclick="show_drop_box();"<?php } ?>/>No</td>
    <td <?php if($row_ques['id']==8) { ?>id="remarks_area" <?php } ?>><textarea name="remarks[<?php echo $i?>]" id="remarks<?php echo $row_ques['id'] ?>"></textarea></td>
    <?php if($row_ques['id']==8) { ?>
    <td id="remarks_drop" style="display:none;border:#496c05 1px solid;padding:2px;">
    <p><input type="checkbox" name="check_list[]" value="Product related issue" /> Product related issue</p>
    <p><input type="checkbox" name="check_list[]" value="Damage or expiry related issue" /> Damage or expiry related issue</p>
    <p><input type="checkbox" name="check_list[]" value="Scheme or claim related issue" /> Scheme or claim related issue</p>
    <p><input type="checkbox" name="check_list[]" value="Distributor related issue" /> Distributor related issue</p>
    <p><input type="checkbox" name="check_list[]" value="Others" id="others" onchange="showhide_other(this)"/> Others</p>
    <br /><textarea name="other_reason" id="other_reason" style="display:none" class="user-success"></textarea>
    </td>
    <?php } ?>
    </tr>
    <?php $i++; } ?>
	<tr>
		
		<td colspan="3" align="center">
        <!--<lable>Subject :</span><textarea name="sub_remarks" id="sub_remarks" style="display:block;" class="user-success"></textarea><br /><br />-->
        <input type="submit"  name="sub" value="Update" />
		</td>
	</tr>
</table>
</td>
</tr>
<tr style="display:none;" id="ph_notresponce" colspan="3">
<td colspan="3" align="center" width="800px">
    <input type="button"  name="sub" value="Update" onclick="return ajaxsub1();"/>
</td>
</tr>
</table>