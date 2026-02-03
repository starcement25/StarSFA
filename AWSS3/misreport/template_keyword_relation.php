<?php
include "header.php";
$sql_menucard_name = "SELECT DISTINCT menucard_name FROM template_keyword_relation where company_id='".$_SESSION['company_id']."'
										AND active='yes' AND menucard_id='".$_REQUEST['menucard_id']."' ";
			$res_menucard_name = mysql_query($sql_menucard_name);
			$row_menucard_name=mysql_fetch_array($res_menucard_name);
$menucard_name=$row_menucard_name['menucard_name'];			
?>
<!-- -->
<div class="main-content">
  <section class="section">
  <div class="row">
<table style="border-collapse:collapse; font-size:10px;" align="center">
<tr>
    <td colspan="2" style="font-weight:bold; color:#6699CC; font-size:18px;text-align:center;" align="center"><?php echo $menucard_name;?></td>
  </tr>
<?php if($_GET['insert_success']==1){?>
  	<tr>
    <td colspan="2" style="font-weight:bold; color:green; font-size:18px;" align="center">Template Keyword mapping done</td>
  </tr>
  <?php }if($_GET['update_success']==1){?>
  <tr>
    <td colspan="2" style="font-weight:bold; color:green; font-size:18px;" align="center">Template Tagging Updated Successfully</td>
  </tr>
<?php }?>
 <?php if($_GET['update_success']==3){?>
  <tr>
    <td colspan="2" style="font-weight:bold; color:green; font-size:18px;" align="center">Template Keyword mapping Deleted Successfully</td>
  </tr>
<?php }?>
 </table> 
<?php
if($_REQUEST['mode']=='delete')
	{
		$menucard_id=$_REQUEST['menucard_id'];
		$template_id=$_REQUEST['template_id'];
		$sqldel="UPDATE template_keyword_relation SET active='no' where menucard_id='".$menucard_id."' AND template='".$template_id."' AND 
					company_id='".$_SESSION['company_id']."'";
		mysql_query($sqldel);
		header('location:template_keyword_relation.php?update_success=3');
	}
$count = 1;
			/*$sql_menu_card = "SELECT DISTINCT menucard_id,menucard_name FROM template_keyword_relation where company_id='".$_SESSION['company_id']."' 
										AND active='yes' ";
			$res_menu_card= mysql_query($sql_menu_card);
			$countmenu_card=mysql_num_rows($res_menu_card);
			//echo "<table border='1'>";
			while($row_menu_card=mysql_fetch_array($res_menu_card))
			{
				if($_REQUEST['menu_card']==$row_menu_card['menucard_id']) $selected='selected';
				else														 $selected=''; 
				$option_string.="<option value=".$row_menu_card['menucard_id']." $selected>".$row_menu_card['menucard_name']."</option>";
			}*/
			if($_REQUEST['menucard_id']!='')
			{
				$menu_card_condition=" AND menucard_id='".$_REQUEST['menucard_id']."'";
			}
			else
			{
				$menu_card_condition="";
			}
			//echo "<div  style=\"position:fixed;\">";
			echo "<div class=\"table-responsive\"><table class=\"table table-bordered table-striped\"> <thead>";
			echo "<tr>
					<td width=\"8%\"><b>Sl no</b></td>
					<td width=\"20%\"><b>Menu Item</b></td>
					<td width=\"30%\"><b>Keyword</b></td>
					<td width=\"10%\"><b>Delete</b></td>
					<td width=\"30%\"><b>Show More</b></td>";
				  echo "</tr></thead>";
				 //echo "</table>";
			//echo "</div>";
			//echo "<br /><br />";
			$sql_template_keyword_details = "SELECT menucard_id,template,GROUP_CONCAT(keyword_id SEPARATOR ',') AS final_keyword FROM template_keyword_relation where company_id='".$_SESSION['company_id']."' ".$menu_card_condition."
										AND active='yes' GROUP BY  menucard_id,template ORDER BY menucard_id,template ASC";
			$res_template_keyword_details = mysql_query($sql_template_keyword_details);
			$counttemplatekeyword=mysql_num_rows($res_template_keyword_details);
			//echo "<table border='1'>";
			$templateid_menuid_array=array();
			while($row_template_keyword_details=mysql_fetch_array($res_template_keyword_details))
			{
				$final_keyword=$row_template_keyword_details['final_keyword'];
				$final_keyword_array=explode(",",$final_keyword);
				$template=$row_template_keyword_details['template'];
				$menu_card_id=$row_template_keyword_details['menucard_id'];
				//$templatename='Row'.$template;
				
				$sql_menu_keyword_details = "SELECT menu_id,keyword_id FROM menu_keyword_relation where company_id='".$_SESSION['company_id']."' 
										AND active='yes'   ORDER BY menu_id ASC ";
				$res_menu_keyword_details = mysql_query($sql_menu_keyword_details);
				//$countmenukeyword=mysql_num_rows($res_menu_keyword_details);
				//echo "<table border='1'>";
				while($row_menu_keyword_details=mysql_fetch_array($res_menu_keyword_details))
				{
					$menu_id=$row_menu_keyword_details['menu_id'];
					$menu_keyword_id=$row_menu_keyword_details['keyword_id'];
					if(in_array($menu_keyword_id,$final_keyword_array))
					{
						$sql_menu_details = "SELECT menu_name FROM menu_master where company_id='".$_SESSION['company_id']."' AND menu_id='".$menu_id."'";
						$res_menu_details = mysql_query($sql_menu_details);
						$row_menu_details=mysql_fetch_array($res_menu_details);
						$menu_name=$row_menu_details['menu_name'];
						${menu_name.$menu_id.$template.$menu_card_id}=$menu_name;
						${keyword_name_string_match.$menu_id.$template.$menu_card_id}=${keyword_name_string_match.$menu_id.$template.$menu_card_id}.$menu_keyword_id.',';
						$templateid_menuid_string=$template.'#'.$menu_id.'#'.$menu_card_id;
						${keyword_name_string_whole.$menu_id.$template.$menu_card_id}=$final_keyword;
					
					if(!in_array($templateid_menuid_string,$templateid_menuid_array))
					{
						array_push($templateid_menuid_array,$templateid_menuid_string);
					}
					break;	
					}
				}
			}
			//print_r($templateid_menuid_array);
				foreach($templateid_menuid_array as $templateid_menuid_val)
				{
					$keyword_final_string='';
					$templateid_menuid_parts=explode("#",$templateid_menuid_val);
					
						$sql_menu_card_name = "SELECT DISTINCT menucard_name FROM template_keyword_relation 
													where company_id='".$_SESSION['company_id']."' AND menucard_id='".$templateid_menuid_parts[2]."'";
						$res_menu_card_name= mysql_query($sql_menu_card_name);
						$row_menu_card_name=mysql_fetch_array($res_menu_card_name);
						$menu_card_name=$row_menu_card_name['menucard_name'];
					
					if(${menu_name.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]}!='' && ${keyword_name_string_whole.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]}!=''){
						${keyword_name_string_match_array.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]}=explode(",",${keyword_name_string_match.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]});
						${keyword_name_string_whole_array.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]}=explode(",",${keyword_name_string_whole.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]});
						$unmatched_keyword = array_diff(${keyword_name_string_whole_array.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]},${keyword_name_string_match_array.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]});       //equals (5,6,7,8)
						//print_r($unmatched_keyword);
						
						foreach(${keyword_name_string_match_array.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]} as $keywod_match_val){
							$sql_keyword_details = "SELECT keyword_name FROM keyword_master where company_id='".$_SESSION['company_id']."' 
												AND  keyword_id='".$keywod_match_val."'";
							$res_keyword_details = mysql_query($sql_keyword_details);
							$row_keyword_details=mysql_fetch_array($res_keyword_details);
							$keyword_name="<font color='#003300'>".$row_keyword_details['keyword_name']."</font>";
							$keyword_final_string=$keyword_final_string.$keyword_name.',';
						}
						$keyword_final_string=substr($keyword_final_string,0,-1);
						if(count($unmatched_keyword) > 0){
						foreach($unmatched_keyword as $keywod_unmatch_val){
							$sql_keyword_details = "SELECT keyword_name FROM keyword_master where company_id='".$_SESSION['company_id']."' 
												AND  keyword_id='".$keywod_unmatch_val."'";
							$res_keyword_details = mysql_query($sql_keyword_details);
							$row_keyword_details=mysql_fetch_array($res_keyword_details);
							$keyword_name="<font color='#FF0000'>".$row_keyword_details['keyword_name']."</font>";
							$keyword_final_string=$keyword_final_string.$keyword_name.',';
						}
						$keyword_final_string=substr($keyword_final_string,0,-1);
						}
						//$keyword_final_string=str_replace(",</font>","</font>",$keyword_final_string);
			$actiondelete = "<td class=\"action\"><a href=\"template_keyword_relation.php?menucard_id=$templateid_menuid_parts[2]&template_id=$templateid_menuid_parts[0]&mode=delete\" class=\"view\" id=\"view\"><b>DELETE</b></a></td>";
			
			
			
			//Start od drop down option population
			
			$sql_template_keyword_details_sub = "SELECT menucard_id,template,GROUP_CONCAT(keyword_id SEPARATOR ',') AS final_keyword FROM template_keyword_relation where company_id='".$_SESSION['company_id']."' AND menucard_id='".$templateid_menuid_parts[2]."' 
											AND template='".$templateid_menuid_parts[0]."'
										AND active='yes' GROUP BY  menucard_id,template ORDER BY menucard_id,template ASC";
			$res_template_keyword_details_sub = mysql_query($sql_template_keyword_details_sub);
			$counttemplatekeywordsub=mysql_num_rows($res_template_keyword_details_sub);
			//echo "<table border='1'>";
			$templateid_menuid_sub_array=array();
			while($row_template_keyword_details_sub=mysql_fetch_array($res_template_keyword_details_sub))
			{
				$final_keyword_sub=$row_template_keyword_details_sub['final_keyword'];
				$final_keyword_sub_array=explode(",",$final_keyword_sub);
				$template_sub=$row_template_keyword_details_sub['template'];
				$menu_card_id_sub=$row_template_keyword_details_sub['menucard_id'];
				//$templatename='Row'.$template;
				
				$sql_menu_keyword_details_sub = "SELECT menu_id,keyword_id FROM menu_keyword_relation where company_id='".$_SESSION['company_id']."' 
										AND active='yes'   ORDER BY menu_id ASC ";
				$res_menu_keyword_details_sub = mysql_query($sql_menu_keyword_details_sub);
				//$countmenukeyword=mysql_num_rows($res_menu_keyword_details);
				//echo "<table border='1'>";
				while($row_menu_keyword_details_sub=mysql_fetch_array($res_menu_keyword_details_sub))
				{
					$menu_id_sub=$row_menu_keyword_details_sub['menu_id'];
					$menu_keyword_id_sub=$row_menu_keyword_details_sub['keyword_id'];
					if(in_array($menu_keyword_id_sub,$final_keyword_sub_array) && $menu_id_sub!=$templateid_menuid_parts[1])
					{
						$sql_menu_details_sub = "SELECT menu_name FROM menu_master where company_id='".$_SESSION['company_id']."' AND menu_id='".$menu_id_sub."'";
						$res_menu_details_sub = mysql_query($sql_menu_details_sub);
						$row_menu_details_sub=mysql_fetch_array($res_menu_details_sub);
						$menu_name_sub=$row_menu_details_sub['menu_name'];
						/*$sql_keyword_details = "SELECT keyword_name FROM keyword_master where company_id='".$_SESSION['company_id']."' 
												AND  keyword_id='".$menu_keyword_id."'";
						$res_keyword_details = mysql_query($sql_keyword_details);
						$row_keyword_details=mysql_fetch_array($res_keyword_details);
						$keyword_name="<font color='#003300'>".$row_keyword_details['keyword_name']."</font>";*/
						${menu_name_sub.$menu_id_sub.$template_sub.$menu_card_id_sub}=$menu_name_sub;
						${keyword_name_string_match_sub.$menu_id_sub.$template_sub.$menu_card_id_sub}=${keyword_name_string_match_sub.$menu_id_sub.$template_sub.$menu_card_id_sub}.$menu_keyword_id_sub.',';
						$templateid_menuid_string_sub=$template_sub.'#'.$menu_id_sub.'#'.$menu_card_id_sub;
					}
					${keyword_name_string_whole_sub.$menu_id_sub.$template_sub.$menu_card_id_sub}=$final_keyword_sub;
					
					if(!in_array($templateid_menuid_string_sub,$templateid_menuid_sub_array))
					{
						array_push($templateid_menuid_sub_array,$templateid_menuid_string_sub);
					}
					
				}
			}
			//print_r($templateid_menuid_array);
				foreach($templateid_menuid_sub_array as $templateid_menuid_val_sub)
				{
					$keyword_final_string_sub='';
					$templateid_menuid_parts_sub=explode("#",$templateid_menuid_val_sub);
					
						$sql_menu_card_name_sub = "SELECT DISTINCT menucard_name FROM template_keyword_relation 
													where company_id='".$_SESSION['company_id']."' AND menucard_id='".$templateid_menuid_parts_sub[2]."'";
						$res_menu_card_name_sub= mysql_query($sql_menu_card_name_sub);
						$row_menu_card_name_sub=mysql_fetch_array($res_menu_card_name_sub);
						$menu_card_name_sub=$row_menu_card_name_sub['menucard_name'];
					
					if(${menu_name_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]}!='' && ${keyword_name_string_whole_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]}!=''){
						${keyword_name_string_match_array_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]}=explode(",",${keyword_name_string_match_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]});
						${keyword_name_string_whole_array_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]}=explode(",",${keyword_name_string_whole_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]});
						$unmatched_keyword_sub = array_diff(${keyword_name_string_whole_array_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]},${keyword_name_string_match_array_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]});       //equals (5,6,7,8)
						//print_r($unmatched_keyword);
						
						foreach(${keyword_name_string_match_array_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]} as $keywod_match_val_sub){
							$sql_keyword_details_sub = "SELECT keyword_name FROM keyword_master where company_id='".$_SESSION['company_id']."' 
												AND  keyword_id='".$keywod_match_val_sub."'";
							$res_keyword_details_sub = mysql_query($sql_keyword_details_sub);
							$row_keyword_details_sub=mysql_fetch_array($res_keyword_details_sub);
							$keyword_name_sub="".strtoupper($row_keyword_details_sub['keyword_name'])."";
							$keyword_final_string_sub=$keyword_final_string_sub.$keyword_name_sub.',';
						}
						$keyword_final_string_sub=substr($keyword_final_string_sub,0,-1);
						if(count($unmatched_keyword_sub) > 0){
						foreach($unmatched_keyword_sub as $keywod_unmatch_val_sub){
							$sql_keyword_details_sub = "SELECT keyword_name FROM keyword_master where company_id='".$_SESSION['company_id']."' 
												AND  keyword_id='".$keywod_unmatch_val_sub."'";
							$res_keyword_details_sub = mysql_query($sql_keyword_details_sub);
							$row_keyword_details_sub=mysql_fetch_array($res_keyword_details_sub);
							$keyword_name_sub="".strtolower($row_keyword_details_sub['keyword_name'])."";
							$keyword_final_string_sub=$keyword_final_string_sub.$keyword_name_sub.',';
						}
						$keyword_final_string_sub=substr($keyword_final_string_sub,0,-1);
						}
				${optionstring.$templateid_menuid_parts_sub[0]}.= "<option >".${menu_name_sub.$templateid_menuid_parts_sub[1].$templateid_menuid_parts_sub[0].$templateid_menuid_parts_sub[2]}." - ".$keyword_final_string_sub."</option>";
					}

				}
			
			//End of drop down option population
			$actionmore = "<td > <div class=\"col-12\">
  <div class=\"form-group\"><select id='showmorekey' name=\"showmorekey_$templateid_menuid_parts[0]\" style='height:5%;width:100%;' class=\"form-control keyword\" ><option value=''>SELECT MORE</option>".${optionstring.$templateid_menuid_parts[0]}."</select></div></div></td>";

				echo "<tbody><tr >
						<td>".$count."</td>
						<td>".${menu_name.$templateid_menuid_parts[1].$templateid_menuid_parts[0].$templateid_menuid_parts[2]}."</td>
						<td><b>".$keyword_final_string."</b></td>".$actiondelete.$actionmore;
					 echo "</tr></tbody>";
					 $count++;
					}

				}
			if(count($templateid_menuid_array)==0)
			{
				echo "<br /><br /><tr><td colspan=\"10\" align=\"center\"><b>No records found</b></td></tr>";
			}
			echo "</table></form></div>";

	?>
    </div>
</section>
</div>

<?php
include "footer.php";
?>