<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

disphtml("main();");

function main(){
	?>
    <script>
	function close_window(){
		window.close();
	}
	</script>
    <?
	$survey_id = $_REQUEST['survey_id'];
	$sql_survey_output = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_output = mysqli_fetch_assoc($res_survey_output)){
		$row_id = $row_survey_output['row_id'];
		$value = $row_survey_output['value'];
		
		if($row_id == 'RA004'){
			$md_name = $value;
		}
		else if($row_id == 'RA005'){
			$cntct_num = $value;
		}
		else if($row_id == 'RA006'){
			$email = $value;
		}
		else if($row_id == 'RA007'){
			$web_addr = $value;
		}
		else if($row_id == 'RA008'){
			$address = $value;
		}
		else if($row_id == 'RA009'){
			$VAT_nmbr = $value;
		}
		else if($row_id == 'RA010'){
			$servc_tax_nmbr = $value;
		}
		else if($row_id == 'RA011'){
			$contact_name = $value;
		}
		else if($row_id == 'RA012'){
			$contact_nmbr = $value;
		}
		else if($row_id == 'RA013'){
			$company_name = $value;
		}
		else if($row_id == 'RA014'){
			$landline = $value;
		}
		else if($row_id == 'RA015'){
			$owner = trim($value);
			$owner = rtrim($owner,";");
			$owner_array = explode(";",$owner);
			foreach($owner_array as $owner_val){
				if($owner_val != '' && $owner_val != ' '){
					$owner_link .= "<a href=\"../upload/CASHLESS/".$owner_val."\" target=\"_blank\" style=\"color:blue;\">View Image</a><br>";
				}
			}
		}
		else if($row_id == 'RA016'){
			$shop = trim($value);
			$shop = rtrim($shop,";");
			$shop_array = explode(";",$shop);
			foreach($shop_array as $shop_val){
				if($shop_val != '' && $shop_val != ' '){
					$shop_link .= "<a href=\"../upload/CASHLESS/".$shop_val."\" target=\"_blank\" style=\"color:blue;\">View Image</a><br>";
				}
			}
		}
		else if($row_id == 'RA017'){
			$rate_card = trim($value);
			$rate_card = rtrim($rate_card,";");
			$rate_card_array = explode(";",$rate_card);
			foreach($rate_card_array as $rate_card_val){
				if($rate_card_val != '' && $rate_card_val != ' '){
					$rate_card_link .= "<a href=\"../upload/CASHLESS/".$rate_card_val."\" target=\"_blank\" style=\"color:blue;\">View Image</a><br>";
				}
			}
		}
		else if($row_id == 'RA018'){
			$visiting_card = $value;
			$visiting_card = rtrim($visiting_card,";");
			$visiting_card_array = explode(";",$visiting_card);
			foreach($visiting_card_array as $visiting_card_val){
				if($visiting_card_val != '' && $visiting_card_val != ' '){
					$visiting_card_link .= "<a href=\"../upload/CASHLESS/".$visiting_card_val."\" target=\"_blank\" style=\"color:blue;\">View Image</a><br>";
				}
			}
		}
	}
	?>
    <center>
    <table class="border" width="60%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td>Header</td>
        <td>Value</td>
      </tr>
    <?php
      echo "<tr>
      	<td>M.D. name</td>
        <td>".$md_name."</td>
      </tr>
        <td>Contact Number</td>
        <td>".$cntct_num."</td>
      </tr>
        <td>Email Address</td>
        <td>".$email."</td>
      </tr>
        <td>Website address</td>
        <td>".$web_addr."</td>
      </tr>
        <td>Address</td>
        <td>".$address."</td>
      </tr>
        <td>VAT Number</td>
        <td>".$VAT_nmbr."</td>
      </tr>
        <td>Service Tax Number</td>
        <td>".$servc_tax_nmbr."</td>
      </tr>
        <td>Contact Person Name</td>
        <td>".$contact_name."</td>
      </tr>
        <td>Contact Person Number</td>
        <td>".$contact_nmbr."</td>
      </tr>
        <td>Company Name</td>
        <td>".$company_name."</td>
      </tr>
        <td>Landline no.</td>
        <td>".$landline."</td>
      </tr>
        <td>Owner</td>
        <td>".$owner_link."</td>
      </tr>
        <td>Shop</td>
        <td>".$shop_link."</td>
      </tr>
        <td>Rate card</td>
        <td>".$rate_card_link."</td>
      </tr>
        <td>Visiting card</td>
        <td>".$visiting_card_link."</td>
      </tr>";
    ?>
    </table>
    <br />
    <br />
    <div align="center" style="color:#000000;"><span onclick="close_window();" style="font-weight:bold; background:#999999; cursor:pointer;">CLOSE</span></div>
    <?php
}

?>