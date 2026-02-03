<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$route = $_REQUEST['route'];
$category = $_REQUEST['category'];
$month_data = $_REQUEST['month_data'];
$monthNum=substr($month_data,4,2);
$yearNum=substr($month_data,0,4);

if($employee == 'all'){
	$emp_condition = '';
}
else{
	$emp_condition = " AND SCW.emp_code IN('".$employee."') ";
}
if($route == 'all'){
	$route_condition = '';
}
else{
	$route_condition = " AND SCW.route_code IN('".$route."') ";
}
if($category == 'all'){
	$category_condition = '';
}
else{
	$category_condition = " AND SCW.product_category IN('".$category."') ";
}

				?>
                <form name ="frmApprove" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
                <input type="hidden" name="mode" value="approve" />
                <input type="hidden" name="emp_code" value="<?php echo $emp_code;?>" />
                <table class="border" width="100%" style="border-collapse:collapse;" border="1">
                    <tr class="TDHEAD" > 
                        <td colspan="11" align="center"><strong>Target vs Achv Report</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="7%" align="center">Sl no.</td>
                        <td width="10%" align="center">Employee Name</td>
                        <td width="10%" align="center">Area</td>
                        <td width="10%" align="left">Route Name</td>
                        <td width="9%">Route No.</td>
                        <td width="9%">DTS Target(Kg)</td>
                        <td width="9%">DTS Achv</td>
                        <td width="9%">DW Target(Kg)</td>
                        <td width="9%">DW Achv</td>
                        <td width="9%">Other Target(Kg)</td>
                        <td width="9%">Other Achv</td>
                    </tr> 
                    <?php
				$sql_target = "SELECT DISTINCT SCW.emp_code, SCW.emp_name,SCW.route_code, SCW.route_name,SCW.product_category, SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
			SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
			SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
			SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
			SCW.dec_31_achievement,RM.area,RM.route_no FROM self_appraisal_route_product_group_wise	SCW,route_master RM WHERE RM.route_code=SCW.route_code ".$emp_condition.$route_condition.$category_condition."";
				$res_target= mysqli_query($link,$sql_target);
				$total_target = mysqli_num_rows($res_target);
				//exit();
			if($total_target > 0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			$emp_route_array=array();
			$count=1;
		while($row_target = mysqli_fetch_assoc($res_target)){
				$emp_code=$row_target['emp_code'];
				$route_code=$row_target['route_code'];
				$area='';
				$route_no='';
				${'emp_name'.$emp_code.$route_code}=$row_target['emp_name'];
				${'route_name'.$emp_code.$route_code}=$row_target['route_name'];
				${'route_no'.$emp_code.$route_code}=$row_target['route_no'];
				${'area'.$emp_code.$route_code}=$row_target['area'];

				${'product_category'.$emp_code.$route_code}=$row_target['product_category'];
				
				$first_date='01'.'-'.$monthNum.'-'.$yearNum;
				$month_abrev=date('M',strtotime($first_date));
				$days = cal_days_in_month(CAL_GREGORIAN,$monthNum,$yearNum);
				if(${'product_category'.$emp_code.$route_code}=='DTS')
				{
					$DTS_target=strtolower($month_abrev).'_'.$days.'_target';
					$DTS_achievement=strtolower($month_abrev).'_'.$days.'_achievement';
					${'DTS_target'.$emp_code.$route_code}=$row_target[$DTS_target];
					${'DTS_achievement'.$emp_code.$route_code}=$row_target[$DTS_achievement];
				}
				if(${'product_category'.$emp_code.$route_code}=='DW')
				{
					$DW_target=strtolower($month_abrev).'_'.$days.'_target';
					$DW_achievement=strtolower($month_abrev).'_'.$days.'_achievement';
					${'DW_target'.$emp_code.$route_code}=$row_target[$DW_target];
					${'DW_achievement'.$emp_code.$route_code}=$row_target[$DW_achievement];
				}
				if(${'product_category'.$emp_code.$route_code}=='Other')
				{
					$Other_target=strtolower($month_abrev).'_'.$days.'_target';
					$Other_achievement=strtolower($month_abrev).'_'.$days.'_achievement';
					${'Other_target'.$emp_code.$route_code}=$row_target[$Other_target];
					${'Other_achievement'.$emp_code.$route_code}=$row_target[$Other_achievement];
				}
				$emp_route_string=$emp_code.'#'.$route_code;
				
				if(!in_array($emp_route_string,$emp_route_array))
				{
					array_push($emp_route_array,$emp_route_string);
				}
		}
			foreach($emp_route_array as $emp_route_val)
			{
			$emp_route_val_str=explode("#",$emp_route_val);
			$emp_val=$emp_route_val_str[0];
			$route_val=$emp_route_val_str[1];
			//$contents_target.=${'emp_name'.$emp_val.$route_val}.",".$area.",".${'route_name'.$emp_val.$route_val}.",".$route_no.",".${'DTS_target'.$emp_val.$route_val}.",".${'DW_target'.$emp_val.$route_val}.",".${'Other_target'.$emp_val.$route_val}."\n";
			?>
						<tr> 
                            <td  align="center"><?php echo $count;?></td>
                            <td align="center"><?php echo ${'emp_name'.$emp_val.$route_val};?></td>
                            <td width="15%" align="center"><?php echo ${'area'.$emp_val.$route_val};?></td>
                            <td align="left"><?php echo ${'route_name'.$emp_val.$route_val};?></td>
                            <td><?php echo ${'route_no'.$emp_val.$route_val};?></td>
                            <td><?php echo ${'DTS_target'.$emp_val.$route_val};?></td>
                            <td><?php echo ${'DTS_achievement'.$emp_val.$route_val};?></td>
                            <td><?php echo ${'DW_target'.$emp_val.$route_val};?></td>
                            <td ><?php echo ${'DW_achievement'.$emp_val.$route_val};?></td>
                             <td ><?php echo ${'Other_target'.$emp_val.$route_val};?></td>
                             <td ><?php echo ${'Other_achievement'.$emp_val.$route_val};?></td>
						
						</tr>
				<?php
						$count++;
					}
				}
			else
			{?>
				<tr> 
					<td valign="top" align="center"  colspan="4">No Target Achv founds for this search</td>
				 </tr>
			<?php 
			}
        ?>
            </table></form><br />
            </td>
	</tr>
</table>
<br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
<?php
mysqli_close($link);
?>