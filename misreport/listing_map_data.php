<?php
ob_start();
session_start();
require("adminUtils.php");

/*$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);*/
$curdate=date('Y-m-d');
disphtml("main();");
function main(){
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND emp_code IN(".$emp_hierarchy_value.") ";
	}
	
	if($_REQUEST['mode']=='delete_map_link')
	{
		$map_link=$_REQUEST['map_link'];
		if(count($map_link) > 0)
		{
			for($i=0;$i<=count($map_link);$i++)
			{
			  $map_link_val=$map_link[$i];
			  if($map_link_val!=''){
				$del_sql="DELETE FROM market_survey_map_link where map_id='".$map_link_val."'";
				mysqli_query($link,$del_sql) or die(mysqli_error()." Error in map link deletion.");
			  }
			  
			}
			?>
			 <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
					<tr> 
						<td align="center" class="ERR">Map link deleted successfully</td>
						<td align="right">&nbsp;</td>
						<td align="right" width="3%">&nbsp;</td>
					</tr>
					</table>
			<?php
		 }
		 else
		 {
		?>
         <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
					<tr> 
						<td align="center" class="ERR">Please select a Map link</td>
						<td align="right">&nbsp;</td>
						<td align="right" width="3%">&nbsp;</td>
					</tr>
					</table>
        <?php
		 }
	  }// end main

	
	$sqlcustomerdetails="SELECT map_id,create_date_time  FROM market_survey_map_link ORDER BY create_date_time DESC";					
	$rescustomerdetails = mysqli_query($link,$sqlcustomerdetails);
	$totalcustomerdetails = mysqli_num_rows($rescustomerdetails);
	if($totalcustomerdetails >0){
		$count = 1;
		?>
         <form action="listing_map_data.php" method="post">
    	<input type="hidden" name="mode" value="delete_map_link" />
		<table border="1" style="border-collapse:collapse;" class="border" width="80%" align="center">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Map Link Info</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="5%">SI</td>
            <td width="70%">Map Link</td>
            <td width="14%">Create Date Time</td>
            <td width="11%">Action</td>
		  </tr>
		<?php
		$dealercount=0;
		$nonstarcount=0;
		$subdealercount=0;
		$sever_url="http://".$_SERVER['SERVER_NAME']."/loadlinkedmap.php?mapid=";
		while($rowcustomerdetails = mysqli_fetch_assoc($rescustomerdetails)){
			//$emp_name = $rowcustomerdetails['emp_name'];
			$map_id = $rowcustomerdetails['map_id'];
			$create_date_time = $rowcustomerdetails['create_date_time'];
			echo "<tr>
					<td width=\"5%\">".$count."</td>
					<td width=\"70%\">".$sever_url.$map_id."</td>
					<td width=\"14%\">".date('d-m-Y H:i:s',strtotime($create_date_time))."</td>
					<td align=\"center\" width=\"12%\"><input type='checkbox' name='map_link[]' value=\"$map_id\" class=\"map_link_chk\"></td>
				  </tr>";
			$count++;
		}
		?>
        	<tr>
	  		<td colspan='3'></td>
			<td  align='center' height=\"30\"><input type='submit' name='SUBMIT1' value='DELETE' /></td>
			</tr>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
}
?>

