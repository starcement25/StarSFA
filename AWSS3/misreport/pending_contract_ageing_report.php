<?php
ob_start();
session_start();
//require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	if(strtoupper($_SESSION['admin_login'])=='SFATS')
	{
		$admin_login_value='SFATS';
		$_SESSION['admin_login']='E0076';
	}
	else  $admin_login_value='';
		
	if($_SESSION['admin_login']=="admin")
	{
		//$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 AND EM.acedns!='N'";
		$customer_condition=" 1 ";
		$product_group_condition = " 1 ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N'";
		$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
		
		if(vertical_fields=='yes'){
		$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
		$rsempvertical=mysql_query($sqlempvertical);
		$rowempvertical=mysql_fetch_array($rsempvertical);
		$emp_vertical_value=$rowempvertical['vertical_value'];
		$emp_vertical_value_array=explode(',',$emp_vertical_value);
		//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
		$condition_one=" WHERE PGM.acedns='Y' AND (";
		$condition_three=" AND (";
		$condition_two='';
		foreach($emp_vertical_value_array as $emp_vertical_values)
		{
			$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PGM.vertical_value) OR";
		}
		$condition_two=substr($condition_two,0,-2);
		$condition_one.=$condition_two.")";
		$condition_three .= $condition_two.")";
		//$condition_three .= $condition_two.")";
		$emp_cond = ", employee_master EM $condition_one AND EM.emp_code = '".$_SESSION['admin_login']."' ";
		
		}
	}
?>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<body>
<center><br />
	<div>
    	<table cellpadding="4">
          <tr class="TDHEAD">
          	<td colspan="2" align="center" style="font-weight:bold;">Choose any one of the following:</td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">Employee:</td>
            <td>
            <select name="emp_name" id="emp_name" onChange="setblank('emp_name');">
                    <option value="" selected>Select</option>
                    <?php
                    $sql_select_emp = "SELECT DISTINCT EM.emp_code, EM.emp_name FROM employee_master EM, customer_master CM, pending_contract_ageing PCA WHERE ".$emp_hierarchy_condition." AND PCA.customer_code=CM.customer_code AND CM.emp_code=EM.emp_code ORDER BY EM.emp_name ASC";
                    $res_select_emp = mysql_query($sql_select_emp);
                    while($row_select_emp = mysql_fetch_array($res_select_emp)){
                        echo "<option value=\"'".$row_select_emp['emp_code']."'\">".$row_select_emp['emp_name']."</option>";
                        $emp_code_string .= "'".$row_select_emp['emp_code']."',";
                    }
                    $emp_code_string = rtrim($emp_code_string,",");
                    echo "<option value=\"".$emp_code_string."\">All</option>";
                    ?>
                </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">Customer:</td>
            <td>
            <select name="customer" id="customer" onChange="setblank('customer');">
        			<option value="" selected>Select</option>
        			<?php
					$sql_get_customer = "SELECT DISTINCT CM.customer_name, CM.customer_code FROM customer_master CM, pending_contract_ageing PCA WHERE ".$customer_condition." AND PCA.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
					$res_get_customer = mysql_query($sql_get_customer);
					while($row_get_customer = mysql_fetch_array($res_get_customer)){
						echo "<option value=\"'".$row_get_customer['customer_code']."'\">".$row_get_customer['customer_name']."</option>";
						$customer_code_string .= "'".$row_get_customer['customer_code']."',";						
					}
					$customer_code_string = rtrim($customer_code_string,",");
                    echo "<option value=\"".$customer_code_string."\">All</option>";
					?>
        		 </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">Product Group:</td>
            <td>
            <select name="product_group" id="product_group" onChange="setblank('product_group');">
                    <option value="" selected>Select</option>
                    <?php
                    $sql_prod_group = "SELECT PGM.product_group_code, PGM.product_group_name FROM product_group_master PGM".$emp_cond." ORDER BY PGM.product_group_name ASC";
                    $res_prod_group = mysql_query($sql_prod_group);
                    while($row_prod_group = mysql_fetch_array($res_prod_group)){
                        echo "<option value=\"'".$row_prod_group['product_group_code']."'\">".$row_prod_group['product_group_name']."</option>";
                        $getprodgroupcond .= "'".$row_prod_group['product_group_code']."',";
						$product_group_code_string .= "'".$row_prod_group['product_group_code']."',";
                    }
                    $product_group_code_string = rtrim($product_group_code_string,",");
                    echo "<option value=\"".$product_group_code_string."\">All</option>";
                    ?>
        		</select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">SKU:</td>
            <td>
            <?php 
				$getprodgroup = rtrim($getprodgroupcond,",");
				if($_SESSION['admin_login']!="admin"){
					$prod_condtion = " WHERE product_group_code IN(".$getprodgroup.") AND acedns='Y'";
				}
			?>
            <select name="product" id="product" onChange="setblank('product');">
            	<option value="" selected>Select</option>
                <?php
					$sql_product = "SELECT dns_prod_code,prod_desc FROM product_master".$prod_condtion." GROUP BY dns_prod_code ORDER BY prod_desc ASC";
					$res_product = mysql_query($sql_product);
					while($row_product = mysql_fetch_array($res_product)){
						echo "<option value=\"'".$row_product['dns_prod_code']."'\">".$row_product['prod_desc']."</option>";
						$product_code_string .= "'".$row_product['dns_prod_code']."',";
					}
					$product_code_string = rtrim($product_code_string,",");
                    echo "<option value=\"".$product_code_string."\">All</option>";
				?>
            </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">Depotwise</td>
            <td>
            <select name="depot" id="depot" onChange="setblank('depot');">
            	<option value="" selected>Select</option>
                <?php
				$branchnamecodearray = array();
				$plantnamearray = array();
				$sql_branch = "SELECT EM.branch_code FROM employee_master EM WHERE".$emp_hierarchy_condition;
				$res_branch = mysql_query($sql_branch);
				while($row_branch = mysql_fetch_array($res_branch)){
					$branch = $row_branch['branch_code'];
					$branch_array = explode(",",$branch);
					foreach($branch_array as $branchcode){
						$sql_branch_code = "SELECT branch_name, plant_name FROM branch_master WHERE branch_code='".$branchcode."'";
						$res_branch_code = mysql_query($sql_branch_code);
						$row_branch_code = mysql_fetch_array($res_branch_code);
						$branch_name = $row_branch_code['branch_name'];
						$branchnamecodearray[$branchcode] = $branch_name;
						$plant_name = $row_branch_code['plant_name'];
						if(!in_array($plant_name,$plantnamearray)){
							array_push($plantnamearray,$plant_name);
						}
					}
				}
				foreach($branchnamecodearray as $bcode=>$bname){
					echo "<option value=\"'".$bcode."'\">".$bname."</option>";
					$branch_code_string .= "'".$bcode."',";
				}
				$branch_code_string = rtrim($branch_code_string,",");
                  echo "<option value=\"".$branch_code_string."\">All</option>";
				?>
            </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">Plantwise</td>
            <td>
            <select name="plant" id="plant" onChange="setblank('plant');">
            	<option value="" selected>Select</option>
                <?php
				foreach($plantnamearray as $plant){
					echo "<option value=\"'".$plant."'\">".$plant."</option>";
					$plant_string .= "'".$plant."',";
				}
				$plant_string = rtrim($plant_string,",");
                  echo "<option value=\"".$plant_string."\">All</option>";
				?>
            </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td></td>
            <td><input name="submit" type="button" value="Submit" id="submit" onClick="submitdata();"></td>
          </tr>
        </table>       
         
    </div>
    <br />
    <div id="display" style="max-height: 400px; width:95%; overflow-y: scroll;" align="center"></div>
    <br />
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
</center>
</body>
<script>
function exporttocsv()
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Pending Contract' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
	
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Pending Contract', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Pending Contract</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function submitdata(){
	if(document.getElementById("emp_name").value.search(/\S/) == -1 && document.getElementById("customer").value.search(/\S/) == -1 && document.getElementById("product_group").value.search(/\S/) == -1 && document.getElementById("product").value.search(/\S/) == -1 && document.getElementById("depot").value.search(/\S/) == -1 && document.getElementById("plant").value.search(/\S/) == -1){
		alert('Please provide a selection');
		return false;
	}
	
	var emp_code = document.getElementById("emp_name").value;
	var customer_name = document.getElementById("customer").value;
	var product_group = document.getElementById("product_group").value;
	var product = document.getElementById("product").value;
	var depot = document.getElementById("depot").value;
	var plant = document.getElementById("plant").value;
		
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('pending_contract_ageing_data.php?emp_code='+emp_code+'&customer_name='+customer_name+'&product_group='+product_group+'&product='+product+'&depot='+depot+'&plant='+plant,'display',0);
}

function setblank(value){
	if(value == 'emp_name'){
		document.getElementById("customer").value = '';
		document.getElementById("product_group").value = '';
		document.getElementById("product").value = '';
		document.getElementById("depot").value = '';
		document.getElementById("plant").value = '';
	}
	
	if(value == 'customer'){
		document.getElementById("emp_name").value = '';
		document.getElementById("product_group").value = '';
		document.getElementById("product").value = '';
		document.getElementById("depot").value = '';
		document.getElementById("plant").value = '';
	}
	
	if(value == 'product_group'){
		document.getElementById("emp_name").value = '';
		document.getElementById("customer").value = '';
		document.getElementById("product").value = '';
		document.getElementById("depot").value = '';
		document.getElementById("plant").value = '';
	}
	
	if(value == 'product'){
		document.getElementById("emp_name").value = '';
		document.getElementById("customer").value = '';
		document.getElementById("product_group").value = '';
		document.getElementById("depot").value = '';
		document.getElementById("plant").value = '';
	}
	
	if(value == 'depot'){
		document.getElementById("emp_name").value = '';
		document.getElementById("customer").value = '';
		document.getElementById("product_group").value = '';
		document.getElementById("product").value = '';
		document.getElementById("plant").value = '';
	}
	
	if(value == 'plant'){
		document.getElementById("emp_name").value = '';
		document.getElementById("customer").value = '';
		document.getElementById("product_group").value = '';
		document.getElementById("product").value = '';
		document.getElementById("depot").value = '';
	}
}
</script>
<?php
  if($admin_login_value=='SFATS')
	{
		$_SESSION['admin_login']='SFATS';
	}
}
?>