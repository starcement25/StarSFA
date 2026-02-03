<style>
/* Paste this css to your style sheet file or under head tag */
/* This only works with JavaScript, 
if it's not present, don't show loader */
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url(Preloader_8.gif) center no-repeat #fff;
}

.category_div{
	font-family:'Courier New', Courier, monospace; 
	font-size:14px; 
	font-weight:bold; 
	color:#039;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
<script>
//paste this code under the head tag or in a separate js file.
	// Wait for window load
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});
</script>
<?php
	function get_designation($shortcode){
		if($shortcode == 'scale_one')
			$level = '1';
		if($shortcode == 'scale_two')
			$level = '2';
		if($shortcode == 'scale_three')
			$level = '3';
		if($shortcode == 'scale_four')
			$level = '4';
			
		$sql_desig = "SELECT DISTINCT designation FROM employee_master WHERE level = '".$level."' AND acedns = 'Y'";
		$res_desig = mysql_query($sql_desig);
		while($row_desig = mysql_fetch_array($res_desig)){
			$desig = $row_desig['designation'];
			
			if($desig == 'Executive Sales')
				$desig = 'ES';
			if($desig == 'Officer Sales')
				$desig = 'OS';
			if($desig == 'Supervisor Sales' || $desig == 'Sales Supervisor')
				$desig = 'SS';
			if($desig == 'Sales Demonstrator')
				$desig = 'SD';
			if($desig == 'Area Sales Executive')
				$desig = 'ASE';
			if($desig == 'Area Sales Manager')
				$desig = 'ASM';
			if($desig == 'Regional Sales Manager')
				$desig = 'RSM';
			if($desig == 'Vice President')
				$desig = 'VP';
			if($desig == 'General Manager Sales')
				$desig = 'GM Sales';
			/*if($desig == 'Sales Head')
				$desig = 'SH';*/
			
			$desig_string .= $desig.", ";
		}
		$desig_string = rtrim($desig_string,", ");
		return $desig_string;
	}
	function emamiguicat_func($category_val,$level_short){
		?>
        <div class="category_div" align="center" >
        <table style="padding:5%;" width="65%">
        	<tr>
            	<td><input type="radio" name="category" id="cat_home" value="HOME" onClick="get_category_data(this.value);" >HOME</td>
                <td><input type="radio" name="category" id="cat_emp" value="EMPLOYEE" onClick="get_category_data(this.value);">EMPLOYEE</td>
                <td><input type="radio" name="category" id="cat_state" value="STATE" onClick="get_category_data(this.value);">STATE</td>
                <td><input type="radio" name="category" id="cat_depot" value="DEPOT" onClick="get_category_data(this.value);">DEPOT</td>
                <td><input type="radio" name="category" id="cat_plant" value="PLANT" onClick="get_category_data(this.value);">PLANT</td>
                <td><input type="radio" name="category" id="cat_zone" value="ZONE" onClick="get_category_data(this.value);">ZONE</td>
            </tr>
            <?php
				if($category_val == 'EMPLOYEE'){
					$sql_desig = "SELECT DISTINCT EM.level FROM `employee_master` EM, sauda_transaction_log STL WHERE STL.emp_code = EM.emp_code ORDER BY EM.level";
					$res_desig = mysql_query($sql_desig);
					while($row_desig = mysql_fetch_array($res_desig)){
						$level = $row_desig['level'];
						
						if($level == '1'){
							$shortcode = 'scale_one';
							$get_desig = get_designation($shortcode);
						}
						if($level == '2'){
							$shortcode = 'scale_two';
							$get_desig = get_designation($shortcode);
						}
						if($level == '3'){
							$shortcode = 'scale_three';
							$get_desig = get_designation($shortcode);
						}
						if($level == '4'){
							$shortcode = 'scale_four';
							$get_desig = get_designation($shortcode);
						}
						
						//$get_desig = "<span style=\"font-size:9px;\">".$get_desig."</span>";
							
						if($shortcode == $level_short)
							$emp_option .= "<option value=\"".$shortcode."\" selected>$get_desig</option>";
						else
							$emp_option .= "<option value=\"".$shortcode."\">$get_desig</option>";
					}
					$select_scale = "<select name=\"emp_scale\" id=\"emp_scale\" onchange=\"emp_scale_wise(this.value);\">".$emp_option."</select>";
					echo "<tr>
							<td colspan=\"5\" >Select Designation: ".$select_scale."</td>
						  </tr>";
                    
				}
			?>
        </table>
        </div>
        <?php
		echo "<script>";
			if($category_val == 'HOME' || $category_val == '')
				echo "document.getElementById(\"cat_home\").checked = true;";
			else if($category_val == 'EMPLOYEE')
				echo "document.getElementById(\"cat_emp\").checked = true;";
			else if($category_val == 'STATE')
				echo "document.getElementById(\"cat_state\").checked = true;";
			else if($category_val == 'DEPOT')
				echo "document.getElementById(\"cat_depot\").checked = true;";
			else if($category_val == 'PLANT')
				echo "document.getElementById(\"cat_plant\").checked = true;";
			else if($category_val == 'ZONE')
				echo "document.getElementById(\"cat_zone\").checked = true;";
		echo "</script>";
	}
?>