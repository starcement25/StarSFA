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
$date_condition=" AND SUBSTRING(SO.survey_id,-14,4)='".$year."' AND SUBSTRING(SO.survey_id,-10,2)='".$monthvalue."'";

$monthabbreviation= date('F',strtotime($month_year[0])).'-'.date('y',strtotime($year));


$current_date = date('Y-m-d');
$datecondition = " AND SUBSTRING(SO.value,1,10)='".$current_date."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";

$count = 1;

	$sql_get_display_menunew="SELECT display_name,row_id,action,display_order FROM survey_input WHERE  type!='menu' AND menu_id='RA001'  AND acedns='Y'
					   ORDER BY display_order ASC";
	$res_get_display_menunew = mysql_query($sql_get_display_menunew);
	$count_display_menunew=mysql_num_rows($res_get_display_menunew);
	$menu_id_new_array=array();
	while($row_get_display_menunew = mysql_fetch_array($res_get_display_menunew))
	{
		array_push($menu_id_new_array,$row_get_display_menunew['row_id']);
		$display_no++;
	}
	$sql_get_display_menuexisting="SELECT display_name,row_id,action,display_order FROM survey_input WHERE  type!='menu' AND menu_id='RA002'  AND acedns='Y'
					   ORDER BY display_order ASC";
	$res_get_display_menuexisting = mysql_query($sql_get_display_menuexisting);
	$count_display_menuexisting=mysql_num_rows($res_get_display_menuexisting);
	$menu_id_existing_array=array();
	while($row_get_display_menuexisting = mysql_fetch_array($res_get_display_menuexisting))
	{
		array_push($menu_id_existing_array,$row_get_display_menuexisting['row_id']);
	}
	
	$survey_site_menu_id_array=array();
	/*$sql_survey_output="SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,SO.*,
						DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date,
						IF(SUBSTRING(SO.value,-26,3)='SE0', SUBSTRING(SO.value,-26,20), '') AS site_id
						 FROM survey_output SO,employee_master EM 
						WHERE SO.type='Site Visit' ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code AND 
						SUBSTRING(SO.survey_id,3,5) IN(".$emp_code_string.")  ORDER BY  site_id DESC,SO.survey_id DESC ";*/
	$sql_survey_output="SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,SO.*,
						DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date,
						IF(SUBSTRING(SO.value,-26,3)='SE0', SUBSTRING(SO.value,-26,20), '') AS site_id,
						IF(SUBSTRING(SO.value,-26,3)='SE0', SUBSTRING(SO.value,-(LENGTH(SO.value)), (LENGTH(SO.value)-27)),'zzzz') AS site_name
						 FROM survey_output SO,employee_master EM 
						WHERE SO.type='Site Visit' ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code AND 
						SUBSTRING(SO.survey_id,3,5) IN(".$emp_code_string.")  ORDER BY  EM.emp_name,site_name ASC,SO.survey_id ASC ";					
	  $rs_survey_output=mysql_query($sql_survey_output);
	  $output_no=1;
	  while($row_survey_output=mysql_fetch_array($rs_survey_output))
	  {
		 $survey_id=$row_survey_output['survey_id'];
		 $site_id=$row_survey_output['site_id'];
		 if($site_id!='' && substr($site_id,0,3)=='SE0')
		 {
		 ${survey_site_id.$survey_id}=$row_survey_output['site_id'];
		 }
		 $survey_row_id=$row_survey_output['row_id'];
		 $survey_date= date("d-M-Y",strtotime($row_survey_output['survey_date']));
		 $value=$row_survey_output['value'];
		 
		 $emp_name = $row_survey_output['emp_name'];
		 $designation = $row_survey_output['designation'];
		 $emp_code = $row_survey_output['emp_code'];
		 $branch_code = $row_survey_output['branch_code'];
		 $sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
		 $rsbranch=mysql_query($sqlbranch);
		 $rowbranch=mysql_fetch_array($rsbranch);
		 $branch_name=$rowbranch['branch_name'];
		 if(in_array($survey_row_id,$menu_id_new_array))
		 {
			 $menu_id='RA001';
			 if($survey_row_id=='RA003'){
			 $value_site_details_array=explode(';',$value);
			 $site_name_value=$value_site_details_array[0];
			 ${site_name.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $site_name_value;
			 }
			 if($survey_row_id=='RA004'){
			 ${address.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA005'){
			 ${subarea.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA007'){
			 ${city.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA010'){
			 ${contactperson.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			  ${contactperson.$survey_id.${survey_site_id.$survey_id}.($menu_id+1)}= $value;
			 }
			 if($survey_row_id=='RA011'){
			 ${contactpersonphone.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA084'){
			 ${siterefferedby.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 ${status.$survey_id.${survey_site_id.$survey_id}.$menu_id}='';
			 if($survey_row_id=='RA015'){
			 ${brandspec.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			  ${brandspec.$survey_id.${survey_site_id.$survey_id}.($menu_id+1)}= $value;
			 }
			 if($survey_row_id=='RA058'){
			 ${product_category.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA086'){
			 ${expected_month_maturity.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
		 }
		 if(in_array($survey_row_id,$menu_id_existing_array))
		 {
			 $menu_id='RA002';
			 if($survey_row_id=='RA024'){
			 $value_site_details_array=explode(';',$value);
			 $site_name_value=$value_site_details_array[0];
			 ${site_name.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $site_name_value;
			 }
			 if($survey_row_id=='RA025'){
			 ${address.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA026'){
			 ${subarea.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA028'){
			 ${city.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA107'){
			 ${contactpersonphone.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA085'){
			 ${siterefferedby.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA090'){
			 ${status.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA084'){
			 ${siterefferedby.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA059'){
			 ${product_category.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
			 if($survey_row_id=='RA099'){
			 ${expected_month_maturity.$survey_id.${survey_site_id.$survey_id}.$menu_id}= $value;
			 }
		 }
		  ${survey_date.$survey_id.${survey_site_id.$survey_id}.$menu_id}=$survey_date;
		  ${emp_name.$survey_id.${survey_site_id.$survey_id}.$menu_id}=$emp_name;
		  ${designation.$survey_id.${survey_site_id.$survey_id}.$menu_id}=$designation;
		  ${branch_name.$survey_id.${survey_site_id.$survey_id}.$menu_id}=$branch_name;
		 if(${survey_site_id.$survey_id}!='')
		 { 
		 $survey_site_menu_id_string=$survey_id.'#'.${survey_site_id.$survey_id}.'#'.$menu_id;
		 if(!in_array($survey_site_menu_id_string,$survey_site_menu_id_array))
		 {
			 array_push($survey_site_menu_id_array,$survey_site_menu_id_string);
		 }
		 }
	  }
		/*$sql_site_visit = "SELECT EM.emp_name,EM.designation,EM.emp_code,EM.branch_code,SM.address,SM.sub_area,SM.city,SM.site_reffered_by,SM.contact_person,SM.phone_no,
						SM.current_status,SM.product_info,SM.product_category,SM.expected_month_maturity,SM.site_id,SM.site_name
		 				FROM site_master SM INNER JOIN employee_master EM
						ON SM.emp_code=EM.emp_code AND SUBSTRING(SM.site_id,-14,4)='".$year."' AND 
						SUBSTRING(SM.site_id,-10,2)='".$monthvalue."' AND SM.emp_code IN(".$emp_code_string.")";				
		$res_site_visit = mysql_query($sql_site_visit);
		$total_rows = mysql_num_rows($res_site_visit);*/
		if(count($survey_site_menu_id_array)>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
              <tr class="TDHEAD_SUB">
                <td width="4%">SL No</td>
                 <td width="5%">Date</td>
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
                <td width="5%"></td>
                <td width="2%">Category</td>
                <td width="2%">Qty</td>
                <td width="2%" >Value</td>
                <td width="8%" >Desc</td>
                <td width="2%">Species</td>
                <td width="5%"></td>
              </tr>
            <?php
			//print_r($survey_site_menu_id_array);
			//echo 'wetwewwwwwwwwwwwyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy'.${product_category.'SUE010820210322224828'.'SE010820201215213905'.'RA002'};
			foreach($survey_site_menu_id_array as $survey_site_menu_id_val)
			{
				$survey_site_menu_id_val_array=explode('#',$survey_site_menu_id_val);
				$survey_id_fetched=$survey_site_menu_id_val_array[0];
				$site_id_fetched=$survey_site_menu_id_val_array[1];
				$menu_id_fetched=$survey_site_menu_id_val_array[2];
				
				$site_reffered_by= ${siterefferedby.$survey_id_fetched.$site_id_fetched.$menu_id_fetched};
				$site_reffered_by_array=explode(':',$site_reffered_by);
				$product_info=str_replace(';',',', ${brandspec.$survey_id_fetched.$site_id_fetched.$menu_id_fetched});
				${product_category.$site_id_fetched}=${product_category.$survey_id_fetched.$site_id_fetched.$menu_id_fetched};
				$expected_month_maturity=str_replace("/","-",${expected_month_maturity.$survey_id_fetched.$site_id_fetched.$menu_id_fetched});
				if($expected_month_maturity!='')
				{
					$expected_month_maturity='01-'.$expected_month_maturity;
					$expected_month_maturity=date('F-y',strtotime($expected_month_maturity));
				}
				else
				{
					$expected_month_maturity='';
				}
				if(strtoupper($site_reffered_by_array[0])=='ARCHITECT' || strtoupper($site_reffered_by_array[0])=='CONTRACTOR'){
					$site_reffered_by=explode(":",$site_reffered_by);
					$sqlfetchfacilitator="SELECT facilitator_name,firm_name FROM facilitator_master WHERE f_code='".$site_reffered_by[1]."'";
					$rsfetchfacilitator=mysql_query($sqlfetchfacilitator);
					$rowfetchfacilitator=mysql_fetch_array($rsfetchfacilitator);
					$facilitator_name=$rowfetchfacilitator['facilitator_name'];
					$firm_name=$rowfetchfacilitator['firm_name'];
					$site_reffered_by_TD="<td align=\"left\" >".$site_reffered_by[0]."</td><td align=\"left\" >".$facilitator_name."</td><td align=\"left\" >".$firm_name."</td>";
				}
				else
				{
				  $site_reffered_by_TD="<td align=\"left\" >".$site_reffered_by."</td><td align=\"left\" ></td><td align=\"left\" ></td>";
				}
				if(${product_category.$site_id_fetched} !='')
				{
					$product_cat_type_TD='<td><table >';
					$product_cat_qty_TD='<td><table>';
					$product_cat_value_TD='<td><table >';
					$product_cat_desc_TD='<td><table>';
					$product_cat_species_TD='<td><table>';

					$product_cat_first_part=explode("$",${product_category.$site_id_fetched});
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
						<td>".${survey_date.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td>".${site_name.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td>".${emp_name.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td>".${designation.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td>".${branch_name.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td>".$monthabbreviation."</td>
						<td>".${address.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td>".${subarea.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td >".${city.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td >".${contactperson.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
						<td >".${contactpersonphone.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>".$site_reffered_by_TD."
						<td >".${status.$survey_id_fetched.$site_id_fetched.$menu_id_fetched}."</td>
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
		mysql_close($link);
?>
