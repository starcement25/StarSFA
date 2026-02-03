<?php
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

//$state = $_REQUEST['state'];
$emp_code=$_REQUEST['emp_code'];
$emp_code_array=explode(",",$emp_code);
$emp_code_string='';
foreach($emp_code_array as $emp_code_val)
{
	$emp_code_string=$emp_code_string."'".$emp_code_val."'".',';
}
$emp_code_string=substr($emp_code_string,0,-1);
$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month_year[0]));
$year = $month_year[1];
$monthabbreviation= date('F',strtotime($month_year[0])).'-'.date('y',strtotime($year));


$current_date = date('Y-m-d');
$datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";

$count = 1;
/*----> Total Site Visit <----*/
		/*$sql_site_visit = "SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,SM.address,SM.sub_area,SM.city,SM.site_reffered_by,SM.contact_person,SM.phone_no,
						SM.current_status,SM.product_info,SM.product_category,SM.expected_month_maturity,SM.site_id,SM.site_name
		 				FROM site_master SM INNER JOIN employee_master EM
						ON SM.emp_code=EM.emp_code AND SUBSTRING(SM.site_id,-14,4)='".$year."' AND 
						SUBSTRING(SM.site_id,-10,2)='".$monthvalue."' AND SM.state='".$state."'";*/
		$sql_site_visit = "SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,SM.address,SM.sub_area,SM.city,SM.site_reffered_by,SM.contact_person,SM.phone_no,
						SM.current_status,SM.product_info,SM.product_category,SM.expected_month_maturity,SM.site_id,SM.site_name
		 				FROM site_master SM INNER JOIN employee_master EM
						ON SM.emp_code=EM.emp_code AND SUBSTRING(SM.site_id,-14,4)='".$year."' AND 
						SUBSTRING(SM.site_id,-10,2)='".$monthvalue."' AND SM.emp_code IN(".$emp_code_string.")";				
		$res_site_visit = mysqli_query($link,$sql_site_visit);
		$total_rows = mysqli_num_rows($res_site_visit);
		if($total_rows>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
              <tr class="TDHEAD_SUB">
                <td width="4%">SL No</td>
                <td width="5%">Site Name</td>
                <td width="5%">Executive Name</td>
                 <td width="5%">Designation</td>
                  <td width="5%">Branch</td>
                <td width="5%">Month of Adding</td>
                <td width="5%">Site Address</td>
                <td width="5%">Sub Area</td>
                <td width="5%">Area</td>
                <td width="5%">Owner Name</td>
                <td width="5%">Owner No.</td>
                <td width="5%">Site Referred By</td>
                <td width="5%">Site Referred by Name</td>
                <td width="5%">Site Referred by Firm Name</td>
                <td width="5%">Site Status</td>
                <td width="5%">Brand Specification</td>
                <td colspan="5" width="16%" align="center">Product Category</td>
                <td width="5%">Expected Month of Maturity</td>
              </tr>
               <tr class="TDHEAD_SUB" >
                <td width="4%" ></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="2%">Category</td>
                <td width="2%">Qty</td>
                <td width="2%" >Value</td>
                <td width="8%" >Desc</td>
                <td width="2%">Species</td>
                <td width="5%"></td>
              </tr>
            <?php
			while($row_site_visit = mysqli_fetch_assoc($res_site_visit)){
				$emp_name = $row_site_visit['emp_name'];
				$designation = $row_site_visit['designation'];
				$emp_code = $row_site_visit['emp_code'];
				$branch_code = $row_site_visit['branch_code'];
				$site_name = $row_site_visit['site_name'];
				$address = str_replace('#','',$row_site_visit['address']);
				$site_id = $row_site_visit['site_id'];
				$sub_area=$row_site_visit['sub_area'];
				$city=$row_site_visit['city'];
				$site_reffered_by=$row_site_visit['site_reffered_by'];
				$site_reffered_by_array=explode(':',$site_reffered_by);
				$contact_person=$row_site_visit['contact_person'];
				$phone_no=$row_site_visit['phone_no'];
				$current_status=$row_site_visit['current_status'];
				$product_info=str_replace(';',',',$row_site_visit['product_info']);
				${product_category.$site_id}=$row_site_visit['product_category'];
				$expected_month_maturity=str_replace("/","-",$row_site_visit['expected_month_maturity']);
				if($expected_month_maturity!='')
				{
					$expected_month_maturity='01-'.$expected_month_maturity;
					$expected_month_maturity=date('F-y',strtotime($expected_month_maturity));
				}
				else
				{
					$expected_month_maturity='';
				}
				
				$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
				$rsbranch=mysqli_query($link,$sqlbranch);
				$rowbranch=mysqli_fetch_assoc($rsbranch);
				$branch_name=$rowbranch['branch_name'];
				
				if(strtoupper($site_reffered_by_array[0])=='ARCHITECT' || strtoupper($site_reffered_by_array[0])=='CONTRACTOR'){
					$site_reffered_by=explode(":",$site_reffered_by);
					$sqlfetchfacilitator="SELECT facilitator_name,firm_name FROM facilitator_master WHERE f_code='".$site_reffered_by[1]."'";
					$rsfetchfacilitator=mysqli_query($link,$sqlfetchfacilitator);
					$rowfetchfacilitator=mysqli_fetch_assoc($rsfetchfacilitator);
					$facilitator_name=$rowfetchfacilitator['facilitator_name'];
					$firm_name=$rowfetchfacilitator['firm_name'];
					$site_reffered_by_TD="<td align=\"left\" >".$site_reffered_by[0]."</td><td align=\"left\" >".$facilitator_name."</td><td align=\"left\" >".$firm_name."</td>";
				}
				else
				{
				  $site_reffered_by_TD="<td align=\"left\" >".$site_reffered_by."</td><td align=\"left\" ></td><td align=\"left\" ></td>";
				}
				if(${product_category.$site_id} !='')
				{
					$product_cat_type_TD='<td><table >';
					$product_cat_qty_TD='<td><table>';
					$product_cat_value_TD='<td><table >';
					$product_cat_desc_TD='<td><table>';
					$product_cat_species_TD='<td><table>';

					$product_cat_first_part=explode("$",${product_category.$site_id});
					$stylecount=1;
					foreach($product_cat_first_part as $product_cat_first_part_val)
					{
						$TD_count=1;
						if($stylecount=='1' && count($product_cat_first_part) > 1) $styleval="style='padding-top:20px;'";
						else 				  $styleval='';
						$product_cat_second_part=explode("#",$product_cat_first_part_val);
						$product_cat_second_part_sub_val=explode(":",$product_cat_second_part[0]);
						$product_cat_type_TD.="<tr><td valign='top' $styleval>".$product_cat_second_part_sub_val[0]."</td></tr><tr height='50px'><td></td></tr>";
						$product_cat_qty_TD.="<tr><td valign='top'>";
						$product_cat_value_TD.="<tr><td valign='top'>";
						$product_cat_desc_TD.="<tr><td valign='top'>";
						if($product_cat_second_part_sub_val[0]=='VENEER')
						{
						$product_cat_species_TD.="<tr><td valign='top'>";
						}
						else  $product_cat_species_TD.="<tr><td valign='top'>";

						foreach($product_cat_second_part as $product_cat_second_part_val)
						{
							if($TD_count==1){
							 $product_cat_second_part_sub_val=explode(":",$product_cat_second_part_val);
							 $product_cat_qty_TD.=$product_cat_second_part_sub_val[1]."</td></tr><tr height='50px'><td></td></tr>";
							}
							if($TD_count==2){
							$product_cat_value_TD.=$product_cat_second_part_val."</td></tr><tr height='50px'><td></td></tr>";
							}
							if($TD_count==3){
							$product_cat_desc_TD.=$product_cat_second_part_val."</td></tr><tr height='50px'><td></td></tr>";
							}
							if($TD_count==4){
							$product_cat_species_TD.=$product_cat_second_part_val."</td></tr><tr height='50px'><td></td></tr>";
							}
							$TD_count++;
						}
					  $stylecount++;	
					}
					$product_cat_type_TD.="</table></td>";
					$product_cat_qty_TD.="</table></td>";
					$product_cat_value_TD.="</table></td>";
					$product_cat_desc_TD.="</table></td>";
					$product_cat_species_TD.="</table></td>";
				}
				else
				{
					$product_cat_type_TD="<td></td>";
					$product_cat_qty_TD="<td></td>";
					$product_cat_value_TD="<td></td>";
					$product_cat_desc_TD="<td></td>";
					$product_cat_species_TD="<td></td>";
				}
				echo "<tr>
						<td>".$count."</td>
						<td>".$site_name."</td>
						<td>".$emp_name."</td>
						<td>".$designation."</td>
						<td>".$branch_name."</td>
						<td>".$monthabbreviation."</td>
						<td>".$address."</td>
						<td>".$sub_area."</td>
						<td >".$city."</td>
						<td >".$contact_person."</td>
						<td >".$phone_no."</td>".$site_reffered_by_TD."
						<td >".$current_status."</td>
						<td >".$product_info."</td>".$product_cat_type_TD.$product_cat_qty_TD.$product_cat_value_TD.$product_cat_desc_TD.$product_cat_species_TD."
						<td >".$expected_month_maturity."</td>
					  </tr>";
				$count++;
			}
			?>
            </table>
<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
       <?php		
		}
		else{
			echo "<div style=\"font-weight:bold; color:red;\">No Records Found</div>";
		}
		mysqli_close($link);
?>
