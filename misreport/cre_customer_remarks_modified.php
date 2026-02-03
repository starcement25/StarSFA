<?php
ob_start();
session_start();
require("adminUtils_CRM_CRE.php");

$today=date("Ymd");
disphtml("main();");

function main(){

if(isset($_REQUEST['sub'])){
	$customercode=$_POST['customercode'];
	$route=$_POST['route'];
	$today=date("Ymd");
	sleep(10);
	$sqltransaction=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM CRM_transaction WHERE customer_code='".$customercode."' 
									AND SUBSTRING(call_id,-14,8)='".$today."' ORDER BY DATE_FORMAT('%Y-%m-%d %h:%i:%s',SUBSTRING(call_id,-14,14)) DESC LIMIT 0,1"));
	if($sqltransaction['call_id']!='')
	{
		$call_id=$sqltransaction['call_id'];
		$qcnt=$_POST['total_ques'];
		for($i=1;$i<=$qcnt;$i++){
			if($_POST['ques'][$i]==8 && $_POST['ans'][$i]==0){
				foreach($_POST['check_list'] as $selected){
				  if($selected=="Others"){ $selected=$_POST['other_reason']; }
				  $remark.=$selected.'#';
				}
				$remarks=substr($remark,0,-1);
			}
			else{
				$remarks=$_POST['remarks'][$i];
			}
			 $sql_ins="INSERT INTO CRM_customer_feedback SET call_id='".$call_id."',customer_code='".$customercode."',question_num='".$_POST['ques'][$i]."',ans='".$_POST['ans'][$i]."',remarks='".addslashes($remarks)."'";
			mysqli_query($link,$sql_ins);
		}
		$today=date("Ymd");
		$sqlup="UPDATE CRM_transaction SET remarks='".addslashes($_REQUEST['other_reason'])."',response_type='Responded',respond_date_time=NOW() WHERE customer_code='".$customercode."' AND SUBSTRING(call_id,-14,8)='".$today."'";
		if(mysqli_query($link,$sqlup)){
		// $GLOBALS['msg']="Transaction updated successfully";
			$flag=1;
		}
		else{
		 //$GLOBALS['msg']="Transaction not updated successfully";
		 $flag=0;
		}
	}
	else{
		//$GLOBALS['msg']="Call id has not genareted please check your data connectivity";
		$flag=2;
	}
	header("location:cre_verification_modified.php?route=$route&flag=$flag");
	exit();
}
if(isset($_REQUEST['sub1'])){
	$customercode=$_POST['customercode'];
	$route=$_POST['route'];
	$restype=$_POST['restype'];
	$today=date("Ymd");
	$sql=mysqli_query($link,"SELECT * FROM CRM_transaction WHERE customer_code='".$customercode."' AND SUBSTRING(call_id,-14,8)='".$today."'");
	if(mysqli_num_rows($sql)>0)
	{
		$sqlup="UPDATE CRM_transaction SET response_type='".$_REQUEST['restype']."',update_date_time=NOW() WHERE customer_code='".$customercode."' AND SUBSTRING(call_id,-14,8)='".$today."'";
		
		if(mysqli_query($link,$sqlup)){
			//echo "Remarks added sucessfully";
			$flag=3;
		}
		else{
		   //echo "Remarks not added ";
		   $flag=4;
		}
	}
	else{
		//echo "No record found";
		$flag=2;
	}
	header("location:cre_verification_modified.php?route=$route&flag=$flag");
	exit();
}
$customer_code=$_REQUEST['customer_code'];
$route=$_REQUEST['route'];
//$sqltransaction=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM CRM_transaction WHERE customer_code='".$customer_code."' AND SUBSTRING(call_id,-14,8)='".$today."'"));
$sqllatestorderid="SELECT order_no,DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') As orderdate  FROM order_header 
				WHERE customer_code='".$customer_code."' AND order_no LIKE 'O%' ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
$rslatestorderid=mysqli_query($link,$sqllatestorderid);
$rowlatestorderid=mysqli_fetch_assoc($rslatestorderid);
$latest_order_no=$rowlatestorderid['order_no'];
$latest_order_date =$rowlatestorderid['orderdate'];
$sqlorderdetails="SELECT PM.prod_desc,OD.qty FROM order_details OD,product_master PM,order_header OH
				WHERE PM.prod_code=OD.sku_code AND OD.order_no=OH.order_no AND 
				OH.customer_code='".$customer_code."' AND OH.order_no='".$latest_order_no."' ORDER BY PM.prod_desc ASC";
$rsorderdetails=mysqli_query($link,$sqlorderdetails);				

$customername=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name FROM `customer_master` WHERE customer_code='".$customer_code."'"));
?>
<table width="600" style="border-collapse:collapse;" class="border" cellpadding="5px" align="center">
	<?php if($latest_order_no !=''){?>
	<tr>
    <td>
    <table style="width:600px"  border="1" align="center">
        <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #ffff00;">
            <td colspan="3" align="center"><b>Last Order Details of  <?php echo $customername['customer_name']; ?> on <?php echo $latest_order_date;?></b></td>
        </tr>
       <tr class="TDHEAD_SUB"> 
             <td width="" align="center">Sl</td>
            <td width= align="left" style="padding-left:20px;">Sku</td>
            <td width= align="right" style="padding-left:20px;">Quantity</td>
    	</tr>
        <?php  
		$cnt=1;
		$countorderdetails=mysqli_num_rows($rsorderdetails);
		if($countorderdetails >0)
		{
			while($roworderdetails=mysqli_fetch_assoc($rsorderdetails))
			{
			?>
				<tr> 
					<td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><?php echo $cnt++;?></td>
					<td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $roworderdetails['prod_desc'];?></td>
					<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $roworderdetails['qty'];?></td>
				</tr>
			<?php
			}
		}
		else
		{?>
        	<tr> 
                <td valign="top" align="center" style="BORDER: #A92A61 1px solid;" colspan="3"> No Order Details Found.</td>
			</tr>
        <?php
		}
		?>	
    </table>
    </td>
    </tr>
    <?php
	}
	$sqllastCRMtrans="SELECT call_id,DATE_FORMAT(SUBSTRING(call_id,-14,8),'%d-%m-%Y') As calldate 
					FROM `CRM_transaction` WHERE customer_code='".$customer_code."' AND response_type='Responded' 
					ORDER BY respond_date_time DESC LIMIT 0,1";
	$resullastCRMtrans = mysqli_query($link,$sqllastCRMtrans);
	$rowlastCRMtrans=mysqli_fetch_assoc($resullastCRMtrans);
	$latest_call_id=$rowlastCRMtrans['call_id'];
	$latest_call_date=$rowlastCRMtrans['calldate'];
	
	if($latest_call_id !=''){
	$sql_remarks="SELECT CQ.id,CQ.questioners,CCF.remarks FROM CRM_questioners CQ,`CRM_customer_feedback` CCF 
				WHERE CQ.id=CCF.question_num AND CCF.call_id='".$latest_call_id."' ORDER BY CQ.id ASC";
	$rs_remarks=mysqli_query($link,$sql_remarks);			
	?>
    <tr>
        <td>
        <table style="width:800px" border="1" align="center">
            <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #00ffff;">
                <td colspan="3" align="center"><b>Last Responded Remarks of  <?php echo $customername['customer_name']; ?> on <?php echo $latest_call_date;?></b></td>
            </tr>
           <tr class="TDHEAD_SUB"> 
                 <td width="" align="center">Question No </td>
                <td width= align="left" style="padding-left:20px;">Question</td>
                <td width= align="right" style="padding-left:20px;">Remarks</td>
            </tr>
            <?php  
            while($row_remarks=mysqli_fetch_assoc($rs_remarks))
            {
            ?>
                <tr> 
                    <td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><?php echo $row_remarks['id'];?></td>
                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $row_remarks['questioners'];?></td>
                    <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $row_remarks['remarks'];?></td>
                </tr>
            <?php
            }
            ?>	
        </table>
        </td>
    </tr>
    <?php }
	$from_date=date('Y').'-'.'04'.'-'.'01';
	$to_date=(date('Y')+1).'-'.'03'.'-'.'31';
	$sqlCRMtrans="SELECT DATE_FORMAT(SUBSTRING(call_id,-14,8),'%d-%m-%Y') As calldate,call_duration,response_type,remarks
					FROM `CRM_transaction` WHERE customer_code='".$customer_code."' AND  
					DATE_FORMAT(SUBSTRING(call_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'
					ORDER BY update_date_time DESC ";
	$resultCRMtrans = mysqli_query($link,$sqlCRMtrans);
   ?>
	<tr>
    <td>
    <table style="width:800px"  border="1" align="center">
        <tr style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #00ff66;">
            <td colspan="5" align="center"><b>Call Details of  <?php echo $customername['customer_name']; ?> </b></td>
        </tr>
       <tr class="TDHEAD_SUB"> 
             <td width="5%" align="center">Sl</td>
            <td  align="left" style="padding-left:20px;" width="12%">Call Date</td>
            <td  align="left" style="padding-left:20px;" width="15%">Call Duration</td>
            <td  align="left" style="padding-left:20px;" width="20%">Response Type</td>
             <td  align="left" style="padding-left:20px;" width="">Remarks</td>
    	</tr>
        <?php  
		$cnt=1;
		$countCRMtrans=mysqli_num_rows($resultCRMtrans);
		if($countCRMtrans >0)
		{
			while($rowCRMtrans=mysqli_fetch_assoc($resultCRMtrans))
			{
				$call_duration=$rowCRMtrans['call_duration'];
				$calldate=$rowCRMtrans['calldate'];
				$response_type=$rowCRMtrans['response_type'];
				$remarks=$rowCRMtrans['remarks'];
			?>
				<tr> 
					<td valign="top" align="center" style="BORDER: #A92A61 1px solid;"><?php echo $cnt++;?></td>
					<td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $calldate;?></td>
					<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $call_duration;?></td>
                   <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $response_type;?></td>
                   <td align="left" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;"><?php echo $remarks;?></td>
				</tr>
			<?php
			}
		}
		else
		{?>
        	<tr> 
                <td valign="top" align="center" style="BORDER: #A92A61 1px solid;" colspan="3"> No Call History Found.</td>
			</tr>
        <?php
		}
		?>	
    </table>
    </td>
    </tr>
    <form method="post" action="" name="submitremarks">
     <input type="hidden" name="route" id="route" value="<?php echo $route;?>">
    <tr>
    <td>
    <table style="width:800px" border="1" align="center">
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
	$res_ques=mysqli_query($link,$sql_ques);
	$cnt=mysqli_num_rows($res_ques);
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
	while($row_ques=mysqli_fetch_assoc($res_ques))
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
    <input type="submit"  name="sub1" value="Update" />
</td>
</tr>
</form>
</table>
<script language="javascript" type="text/javascript">
	
	function show_drop_box(){
		
		document.getElementById("remarks_area").style.display="none";
		document.getElementById("remarks_drop").style.display="block";
	}
	function hide_drop_box(){
		
		document.getElementById("remarks_area").style.display="block";
		document.getElementById("remarks_drop").style.display="none";
	}
	function show1(){
		document.getElementById('ph_responce').style.display = 'block';
		document.getElementById('ph_notresponce').style.display = 'none';
	}
	
	function show2(){
		document.getElementById('ph_notresponce').style.display = 'block';
		document.getElementById('ph_responce').style.display ='none'
	}
	function showhide_other(checkboxElem) {
	  if (checkboxElem.checked) {
		document.getElementById('other_reason').style.display = 'block';
		//document.getElementById('subj').style.display = 'none';
	  } else {
		document.getElementById('other_reason').style.display = 'none';
		//document.getElementById('subj').style.display = 'block';
	  }
	} 
	function question_checked()
	{
		alert('kkk');
		if(document.getElementById("ans11").checked==false)
		{
			alert("Please choose at least one option for Question 1");
			return false;
		}
		return true;
	}
	function ajaxsub1(){
		   var customercode =document.getElementById("customercode").value;
		   var restype=document.querySelector('input[name="restype"]:checked').value;
		   GenericAjaxFunction('crmremarksupdate.php?customercode='+customercode+'&restype='+restype,'show_update',0);
	}

</script>
<?php }?>