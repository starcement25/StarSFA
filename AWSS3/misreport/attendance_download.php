<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
	function main(){
		$current_date = date('Y-m-d');
		$current_date_array = explode("-",$current_date);
		$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
		
		//print_r($months);
		//echo $get_month = date("F", gmmktime(0,0,0,10,3,2015));
		?>
        <center>
        <form method="POST" action="attendance_download_data.php">
        <table width="30%" cellpadding="5px">
        	<tr class="TDHEAD">
            	<td colspan="2" align="center">Select Attributes</td>
            </tr>
        	<tr class="TDHEAD_SUB">
            	<td>Month:<select name="month_name">
               	<?php 
				foreach($months as $month_index=>$month_value){
					if($month_index == $current_date_array[1])
						echo "<option selected>".$month_value."</option>";
					else
						echo "<option>".$month_value."</option>";
				}
				?>
                </select>
                </td>
                <td>Year:<input type="text" value="<?php echo $current_date_array[0]; ?>" size="6" name="year" readonly /></td>
            </tr>
            <tr class="TDHEAD_SUB">
            	<td colspan="2" align="center"><input type="submit" name="submit" value="Submit" /></td>
            </tr>
        </table>
        </form>
        </center>
        <?php
	}
?>