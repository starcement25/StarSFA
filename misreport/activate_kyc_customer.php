<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	disphtml("main();");	
	function main(){
		$survey_id = $_GET['survey_id'];
		
		$sql_survey_val = "SELECT value FROM survey_output WHERE survey_id = '".$survey_id."' and row_id='RA004'";
		$res_survey_val = mysqli_query($link,$sql_survey_val);
		$row_survey_val = mysqli_fetch_assoc($res_survey_val);
		$customer_name = $row_survey_val['value'];
		if($_POST['submit'] == 'Submit'){
			$dns_customer_code = $_POST['dns_customer_code'];
			$survey_id = $_POST['survey_id'];
				
			$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
			$res_survey_details = mysqli_query($link,$sql_survey_details);
			while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
				$survey_row_id = $row_survey_details['row_id'];
				$survey_value = $row_survey_details['value'];
				$emp_code=substr($survey_id,2,5);
					if($survey_row_id == 'RA004')
						$customer_name = $survey_value;
					else if($survey_row_id == 'RA005')
						$contact = $survey_value;
					else if($survey_row_id == 'RA006')
						$phone_no = $survey_value;
					else if($survey_row_id == 'RA007')
						$address = $survey_value;
					else if($survey_row_id == 'RA008')
						$pin = $survey_value;
					else if($survey_row_id == 'RA011')
						$category = $survey_value;
					else if($survey_row_id == 'RA012')
						$cus_type = $survey_value;
					else if($survey_row_id == 'RA013')
						$route_name = $survey_value;
					else if($survey_row_id == 'RA060')
						$linked_dealer = $survey_value;
					else if($survey_row_id == 'RA138')
						$branch_name = $survey_value;	
			}
			$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."'";
			$rsbranchcode=mysqli_query($link,$sqlbranchcode);
			$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
			$branch_code=$rowbranchcode['branch_code'];
			
			$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name)."'";
			$rsroutechk=mysqli_query($link,$sqlroutechk);
			$rowroutechk=mysqli_fetch_assoc($rsroutechk);
			$route_code=$rowroutechk['route_code'];
			
			$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($linked_dealer)."' AND acedns='Y' 
						AND cust_type IN('Dealer')";
			$rsrdscode=mysqli_query($link,$sqlrdscode);
			$rowrdscode=mysqli_fetch_assoc($rsrdscode);
			$rds_code=$rowrdscode['customer_code'];

			$sqlcustomernamechk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
			$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
			$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
			
			if($countcustomernamechk<1)
			{
			$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
			$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
			$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
			$max_customer_code=$rowmaxcustomercode['max_customer_code'];
			
			if($max_customer_code=='')
			{
				$max_customer_code='C/0000001';
			}
			else
			{
				$max_customer_code++;
			}
			$sql  = "insert into customer_master ";
			$sql .= " SET customer_code='".$max_customer_code."'";
			$sql .= " , dns_customer_code='".$dns_customer_code."'";
			$sql .= " , customer_name='".addslashes($customer_name)."'";
			$sql .= " , branch_code='".addslashes($branch_code)."'";
			$sql .= " , phone_no='".$phone_no."'";
			$sql .= " , route_code='".$route_code."'";
			$sql .= " , acedns='Y'";
			$sql .= " , black_list='N'";
			$sql .= " , rds_tag='".$rds_code."'";
			$sql .= " , cust_type='".$cus_type."'";
			$sql .= " , address='".$address."'";
			$sql .= " , pin='".addslashes($pin)."'";
			$sql .= " , download_time=CURRENT_TIMESTAMP()";
			mysqli_query($link,$sql);
			$sqlsurveyheaderupdate="UPDATE survey_header SET active='active' WHERE survey_id='".$survey_id."'";
			$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$max_customer_code."',
									 route_code='".$route_code."',
									 emp_code='".$emp_code."',
									 acedns='Y',
									download_time=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlinsertcustomerroute) && mysqli_query($link,$sqlsurveyheaderupdate)){
			?>
			<script language="JavaScript" type="text/javascript">alert('KYC activated successfully.');window.location.href='star_survey_report_modified.php';</script>
			<?php }else{
                ?> <script language="JavaScript" type="text/javascript">alert('KYC activation unsuccessful.');window.location.href='star_survey_report_modified.php';</script>
            <?php
			}
		}
		else{ 
		?>
        	<script language="JavaScript" type="text/javascript">alert('Customer Code Already Exists.');window.location.href='star_survey_report_modified.php';</script>
         <?php
		}
	}
?>
<script>
function validate(){
	if(document.getElementById("dns_customer_code").value.search(/\S/) == -1){
		alert('Please Provide Customer code');
		return false;
	}
}
function emplist_populate(){
	document.getElementById("emplist_div").hidden = false;
}
</script>
<center>
<br /><br />
<form method="POST" action="" onsubmit="return validate();">
<table class="border" width="40%" border="1" cellpadding="4" style="border-collapse:collapse;">
  <tr>
  	<td colspan="2" class="TDHEAD" align="center">Activate KYC of Firm "<?php echo $customer_name;?>"</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Customer Code:</td>
    <td align="left"><input type="text" name="dns_customer_code" id="dns_customer_code" value=""/>
    <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input type="submit" name="submit" value="Submit" /></td>
  </tr>
</table>
</form>
</center>
<?php
}
?>