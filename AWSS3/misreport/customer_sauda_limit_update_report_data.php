<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}

$from_date = $_REQUEST['from_date'];
$from_date_check=date('Y-m-d',strtotime($from_date));

$sql_customer_saudalimit = "SELECT CM.customer_name,CM.dns_customer_code,CSL.previous_sauda_limit,CSL.uploaded_sauda_limit,
							DATE_FORMAT(CSL.datetime,'%d-%m-%Y %H:%i:%s') AS datetime  FROM 
							customer_master CM,customer_sauda_limit_update_log CSL 
							WHERE CM.dns_customer_code=CSL.customer_code AND SUBSTRING(CSL.datetime,1,10)='".$from_date_check."'  
							ORDER BY CSL.datetime DESC,CM.customer_name ASC";
$res_customer_saudalimit = mysql_query($sql_customer_saudalimit);
$total_rows = mysql_num_rows($res_customer_saudalimit);
$count = 1;
if($total_rows>0){
	?>
    <div class="tbl-header"><table class="border datatable" width="100%" border="0" style="border-collapse:collapse;removed: fixed;"  align="center">
    <thead>
     <tr>
        <td colspan="6" class="TDHEAD" align="center">Customer sauda limit report on - <?php echo $from_date; ?></td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="10%">SI</td>
        <td width="20%">Customer code</td>
        <td width="20%">Customer name</td>
        <td width="20%">Previous Sauda Limit</td>
        <td width="20%">Uploaded Sauda limit</td>
        <td width="20%">Upload Datetime</td>
      </tr></thead></table></div><div class="tbl-content"><table class="BORDER datatable" cellpadding="0" cellspacing="0" border="1" style="removed: fixed;">
      <tbody>
    <?php
	while($row_customer_saudalimit = mysql_fetch_array($res_customer_saudalimit)){
		$customer_name = $row_customer_saudalimit['customer_name'];
		$dns_customer_code = $row_customer_saudalimit['dns_customer_code'];
		$previous_sauda_limit = $row_customer_saudalimit['previous_sauda_limit'];
		$uploaded_sauda_limit = $row_customer_saudalimit['uploaded_sauda_limit'];
		$datetime = $row_customer_saudalimit['datetime'];
		
		echo "<tr>
				<td width=\"10%\">".$count."</td>
				<td width=\"20%\">".$dns_customer_code."</td>
				<td width=\"20%\">".$customer_name."</td>
				<td  width=\"20%\" style=\"text-align:right\">".$previous_sauda_limit."</td>
				<td  width=\"20%\" style=\"text-align:right\">".$uploaded_sauda_limit."</td>
				<td width=\"20%\">".$datetime."</td>
			  </tr>"; 
		$count++;
	}
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
echo '</tbody></table></div>';
mysql_close($link);
?>
