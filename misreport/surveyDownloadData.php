<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$customer = $_REQUEST['customer'];
	$count = 1;
	$emp_array = array();
	$sql_customer = "SELECT address,phone_no,cust_type,customer_name FROM customer_master WHERE customer_code IN('".$customer."')";
	$res_customer = mysqli_query($link,$sql_customer);
	$total_customer = mysqli_num_rows($res_customer);
	if($total_customer>0){
		?>
        <table border="1"  style="border-collapse:collapse;display:'';" class="border" width="100%" cellpadding="4" align="center">
          <tr class="TDHEAD">
            <td colspan="4" align="center">Personal Info</td>
          </tr>
          <tr class="TDHEAD_SUB">
            <td align="center">Customer Name</td>
          	<td align="center">Address</td>
            <td align="center">Mobile no</td>
            <td align="center">Customer Category</td>
          </tr>
       <?php
		while($row_customer = mysqli_fetch_assoc($res_customer)){
		    $customer_name = $row_customer['customer_name'];
			$address = $row_customer['address'];
			$phone_no = $row_customer['phone_no'];
			$cust_type = $row_customer['cust_type'];
			if($cust_type=='D')  $cust_type='Dealer';
		    if($cust_type=='R')  $cust_type='Retailer';
			echo "<tr>
					<td>".$customer_name."</td>
					<td align=\"left\">".$address."</td>
					<td align=\"left\">".$phone_no."</td>
					<td align=\"left\">".$cust_type."</td>
				  </tr>";
		}
		?>
        </table>
        <br /><br />
        <?php
	}
	?>
    <table border="1"  style="border-collapse:collapse;" class="border" width="100%" align="center">
	<?php
	$sql_get_display="SELECT display_name,row_id,action,display_order FROM survey_input WHERE  acedns='Y' ORDER BY display_order ASC";
	$res_get_display = mysqli_query($link,$sql_get_display);
	$count_display=mysqli_num_rows($res_get_display);
	?>
     <tr class="TDHEAD">
        <td colspan="<?php echo ($count_display+2);?>" align="center">Survey Info of "<?php echo $customer_name;?>"</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center"><td>SI</td><td>Date</td>
		<?php
        while($row_get_display = mysqli_fetch_assoc($res_get_display))
        {
            if($row_get_display['display_order'] >1)
			{
				$display_name=$row_get_display['display_name'];
				$action=$row_get_display['action'];
				$row_id=$row_get_display['row_id'];
				echo "<td>".$display_name."</td>";
				$row_id_string.="'".$row_id."'".',';
			}
		}
		 $row_id_string=substr($row_id_string,0,-1);
		?>
      </tr>
      <?php
	  	$sqlsurveyid="SELECT survey_id,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM 
						survey_output WHERE row_id='RA001' AND value='".$customer_name."' AND SUBSTRING(survey_id,-14,8) 
							BETWEEN ".date('Ymd',strtotime($start_date))." AND ".date('Ymd',strtotime($end_date))."";
		$rssurveyid=mysqli_query($link,$sqlsurveyid);
	    $count=1;
	    while($rowsurveyid = mysqli_fetch_assoc($rssurveyid))
        {
			$survey_id=$rowsurveyid['survey_id'];
			$survey_date=$rowsurveyid['survey_date'];
	      	$sql_survey_output="SELECT SO.* 
		 					FROM survey_output SO,survey_input SI 
		 					WHERE SO.row_id=SI.row_id AND SO.row_id IN(".$row_id_string.") AND SO.survey_id IN('".$survey_id."') ORDER BY SI.display_order ASC";
			$rs_survey_output=mysqli_query($link,$sql_survey_output);
			 echo "<tr><td>".$count."</td><td>".$survey_date."</td>";
			 while($row_survey_output=mysqli_fetch_assoc($rs_survey_output))
			  {
				$survey_date=$row_survey_output['survey_date'];
				$row_survey_output['row_id'];
				if($row_survey_output['row_id']=='RA006')
				{
					 $sqlsamplephoto="SELECT sample_photo FROM sample_master WHERE reference_no='".$row_survey_output['value']."'";
 					 $rssamplephoto=mysqli_query($link,$sqlsamplephoto);
					 $rowsamplephoto=mysqli_fetch_assoc($rssamplephoto);
					 $sample_photo=$rowsamplephoto['sample_photo'];
					 $file_name="../sample/thumbnail/thumb_". $sample_photo;
					 $value="<img src='".$file_name."' border=0' /><b>".$row_survey_output['value']."</b>";
				}
				else $value=$row_survey_output['value'];
				
				echo "<td>".$value."</td>";
			 }
			 $count++;
			 echo "</tr>";
		}
		?>
     </table>
