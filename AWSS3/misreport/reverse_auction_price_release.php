<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
?>
<!--div style="position:absolute; width:inherit;"-->
<table width="90%"   id="maintable" border="1" style="border-collapse: collapse;" align="center">
<thead>
  <tr>
  	<th colspan="10" align="center"><b><font size="+2">Reverse Auction Price Release</font></b></th>
  </tr>
  <tr  align="center" height="20">
    <th  width="10%" style="background:#0033FF;" align="left"><b>Oil Group</b></th>
    <th  width="10%" style="background:#0033FF;" align="left"><b>UOM</b></th>
    <th  width="10%" style="background:#0033FF;" align="left"><b>Pack type</b></th>
    <?php
	$sql_count_plant = "SELECT distinct(plant_name) AS plant_name FROM branch_master WHERE is_plant='yes'";
	$res_count_plant = mysql_query($sql_count_plant);
	$count_plant=mysql_num_rows($res_count_plant);
	$td_with=40/$count_plant;
	while($row_count_plant = mysql_fetch_array($res_count_plant))
		echo "<th align=\"center\"  width=\"$td_width%\"><b>$row_count_plant[plant_name]</b></th>";
	?>
    <th width="10%" align="left">Counter Bid Jump</th>
    <th width="10%" align="left">Counter Bid Limit</th>
     <th width="10%" align="left">Rate Jump -IR</th>
  </tr>
  <!--tr class="TDHEAD_SUB" align="center">
    <?php
	/*for($i=1;$i<=$total_product;$i++)
		echo "<th width=\"$td_width%\" align=\"center\"><b>Alloted</b></th>";*/
	?>
  </tr-->
  </thead>
</table>
<?php }?>
