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
	if($_REQUEST['mode']=='packingfilter'){
	$product = $_REQUEST['product'];
	$plant = $_REQUEST['plant'];
	
	if($plant != ''){
		$plant_condition = " AND PC.plant_name IN(".$plant.") ";
	}
	else if($product != ''){
		$product_condition = " AND PC.dns_prod_code IN(".$product.")";
	}
}
else
{
	$plant_condition ='';
	$product_condition='';
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
 <style>
.datatable{
  width:98%;
  table-layout: fixed;
  }
.tbl-header{
  background-color: rgba(255,255,255,0.3);
 }
.tbl-content{
  height:500px;
  overflow-x:auto;
  margin-top: 0px;
  border: 1px solid rgba(255,255,255,0.3);
}
.datatable th{
  padding: 20px 15px;
  text-align: left;
  font-weight: 500;
  font-size: 12px;
  color: #fff;
  text-transform: uppercase;
}
.datatable td{
  padding: 15px;
  text-align: left;
  vertical-align:middle;
  font-weight: 300;
  font-size: 12px;
  color: #000000;
  border-bottom: solid 1px rgba(255,255,255,0.1);
}
/* demo styles */
/* for custom scrollbar for webkit browser*/
::-webkit-scrollbar {
    width: 6px;
} 
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
} 
::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
}
</style>
</head>
<body >
<center><br /><div id="display">
<form name="packing_filter" method="post" action="" onSubmit="javascript:return submitdata();">
<input type='hidden' name='mode' value="packingfilter" />
<table cellpadding="4">
          <tr class="TDHEAD">
          	<td colspan="2" align="center" style="font-weight:bold;">Choose any one of the following:</td>
          </tr>
           <tr class="TDHEAD_SUB">
          	<td align="right">Plantwise</td>
            <td>
          
            <select name="plant" id="plant" onChange="setblank('plant');">
            	<option value="" selected>Select</option>
                <?php
					$sql_plant = "SELECT DISTINCT plant_name FROM branch_master WHERE acedns='Y' ORDER BY plant_name ASC";
					$res_plant = mysqli_query($link,$sql_plant);
					while($row_plant = mysqli_fetch_assoc($res_plant)){
						if(str_replace("'","",$plant)==$row_plant['plant_name']) $selected="selected";
						else $selected="";
					echo "<option value=\"'".$row_plant['plant_name']."'\" $selected>".$row_plant['plant_name']."</option>";
					$plant_string .= "'".$row_plant['plant_name']."',";
				}
				$plant_string = rtrim($plant_string,",");
                  echo "<option value=\"".$plant_string."\">All</option>";
				?>
            </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">SKUwise:</td>
            <td>
            <select name="product" id="product" onChange="setblank('product');">
            	<option value="" selected>Select</option>
                <?php
					$sql_product = "SELECT dns_prod_code,prod_desc FROM product_master WHERE acedns='Y' 
								AND vertical_value='".$_SESSION['vertical_value']."' GROUP BY dns_prod_code ORDER BY prod_desc ASC";
					$res_product = mysqli_query($link,$sql_product);
					while($row_product = mysqli_fetch_assoc($res_product)){
						if(str_replace("'","",$product)==$row_product['dns_prod_code']) $selected="selected";
						else $selected="";
						echo "<option value=\"'".$row_product['dns_prod_code']."'\" $selected>".$row_product['prod_desc']."</option>";
						$product_code_string .= "'".$row_product['dns_prod_code']."',";
					}
					$product_code_string = rtrim($product_code_string,",");
                    echo "<option value=\"".$product_code_string."\">All</option>";
				?>
            </select>
            </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td></td>
            <td><input name="submit" type="submit" value="Submit" id="submit"></td>
          </tr>
        </table>
       </form> 
       <br /><br /><br />
<?php


/*$sqllatestuploaddate="SELECT DATE_FORMAT(datetime,'%d-%m-%Y') AS packing_upload_date FROM `packing_master` ORDER BY datetime DESC LIMIT 0,1";
$rslatestuploaddate=mysqli_query($link,$sqllatestuploaddate);
$rowlatestuploaddate=mysqli_fetch_assoc($rslatestuploaddate);
$packing_upload_date_latest=$rowlatestuploaddate['packing_upload_date'];*/

$sql_packing= "SELECT * FROM (SELECT PC.dns_prod_code, PC.prod_desc, PC.packing_cost, PC.no_pc_one_case,PC.plant_name,PC.packing_realization,
				PM.acedns,DATE_FORMAT(SUBSTRING(PC.datetime,1,10),'%d-%m-%Y') As last_updated_date,PM.product_group_code,PM.pack_size,PGM.product_group_name FROM
				packing_master PC,product_master PM,product_group_master PGM WHERE PC.dns_prod_code=PM.dns_prod_code AND PM.product_group_code=PGM.product_group_code AND PC.plant_name!='' AND 
				PM.vertical_value='".$_SESSION['vertical_value']."'".$plant_condition.$product_condition."  ORDER BY PC.datetime DESC) AS SAT GROUP BY 1,5 ORDER BY 5,9,1";
/*$sql_packing= "SELECT PC.dns_prod_code, PC.prod_desc, PC.packing_cost, PC.no_pc_one_case,PC.plant_name,PC.packing_realization,PM.acedns FROM
				packing_master PC,product_master PM WHERE PC.dns_prod_code=PM.dns_prod_code AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' 
				AND DATE_FORMAT(SUBSTRING(PC.datetime,1,10),'%d-%m-%Y')='".$packing_upload_date_latest."' GROUP BY PM.dns_prod_code,PC.plant_name 
				ORDER BY PC.plant_name ASC";*/				
$res_packing = mysqli_query($link,$sql_packing);
$total_packing = mysqli_num_rows($res_packing);
$count = 1;
if($total_packing>0){
	?>
    <div class="tbl-header">
    <table width="90%" border="0" style="border-collapse:collapse;" class="BORDER datatable" cellpadding="4">
     <thead>
     <tr>
        <td colspan="12" class="TDHEAD" align="center" width="100%"><b>Packing Master</b></td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="5%">SI</td>
        <td width="10%">Date of Upload</td>
        <td width="6%">Plant Code</td>
        <td width="9%">Plant Name</td>
        <td width="9%">Product Group</td>
        <td width="8%">Material Code</td>
        <td width="13%">Material Description</td>
        <td width="7%">Pack size (BP/CP)</td>
        <td width="10%">Sale</td>
        <td width="10%">Actual Packing Cost <br />(Rs/case)</td>
        <td width="6%">Active/Inactive</td>
        <!--td width="4%">UOM</td-->
        <td width="7%">No. of pcs/case</td>
      </tr>
      </thead>
      </table>
      </div>
      <div class="tbl-content">
      <table class="BORDER datatable" cellpadding="0" cellspacing="0" border="1">
      <tbody>
    <?php
	while($row_packing = mysqli_fetch_assoc($res_packing)){
		$dns_prod_code = $row_packing['dns_prod_code'];
		$prod_desc = str_replace(',','',$row_packing['prod_desc']);
		$packing_cost = $row_packing['packing_cost'];
		$no_pc_one_case = $row_packing['no_pc_one_case'];
		$plant = $row_packing['plant_name'];
		$packing_realization = $row_packing['packing_realization'];
		$pack_size = $row_packing['pack_size'];
		$product_group_name = $row_packing['product_group_name'];
		$acedns = $row_packing['acedns'];
		$last_updated_date = $row_packing['last_updated_date'];
		/*$acedns = $row_packing['acedns'];
		if($acedns=='N'){
			$color="#f44242";
		}
		else $color="";
		echo "<tr>
				<td>".$count."</td>
				<td style=\"background:$color;\">".$plant."</td>
				<td style=\"background:$color;\">".$dns_prod_code."</td>
				<td style=\"background:$color;\">".$prod_desc."</td>
				<td align=\"right\">".$packing_cost."</td>
				<td align=\"right\">".$packing_realization."</td>
			  </tr>";*/
		if($acedns=='N'){
			$color="#f44242";
		}
		else $color="";	
		$sqlplantcode="SELECT plant_code FROM branch_master WHERE plant_name='".$plant."'";  
		$rsplantcode=mysqli_query($link,$sqlplantcode);
		$rowplantcode=mysqli_fetch_assoc($rsplantcode);
		$plant_code=$rowplantcode['plant_code'];
		echo "<tr>
				<td style=\"background:$color;\">".$count."</td>
				<td style=\"background:$color;\">".$last_updated_date."</td>
				<td style=\"background:$color;\">".$plant_code."</td>
				<td style=\"background:$color;\">".$plant."</td>
				<td style=\"background:$color;\">".$product_group_name."</td>
				<td style=\"background:$color;\">".$dns_prod_code."</td>
				<td style=\"background:$color;\">".$prod_desc."</td>
				<td style=\"background:$color;\">".$pack_size."</td>
				<td align=\"right\" style=\"background:$color;\">".number_format($packing_cost,2)."</td>
				<td align=\"right\" style=\"background:$color;\">".number_format($packing_realization,2)."</td>
				<td style=\"background:$color;\">".$acedns."</td>
				<td style=\"background:$color;\" align=\"right\">".$no_pc_one_case."</td>
			  </tr>";	  
		$count++;
	}
?>
</tbody>
</table>
</div>
</div><br />
  <!--div id="display" style="max-height: 400px; width:95%; overflow-y: scroll;" align="center"></div>
 <br /-->
   <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
    </center>
</body>
 <script>
 function setblank(value){
	if(value == 'product'){
		document.getElementById("plant").value = '';
	}
	if(value == 'plant'){
		document.getElementById("product").value = '';
	}
}

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
	a.download = 'Packing data' + postfix + '.xls';
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
	var mywindow = window.open('', 'Packing Data', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Packing Data</title>');
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
	if(document.getElementById("product").value.search(/\S/) == -1 && document.getElementById("plant").value.search(/\S/) == -1){
		alert('Please provide a selection');
		return false;
	}
	return true;
}

</script>
<?php	
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
}
mysqli_close($link);
?>
