<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
disphtml("main();");

function main()
{
?><head>
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>

<center>
<br /><br />
<table cellpadding="4px" width="60%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate Focus product</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_focus_product.php" name="focus_product" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_focus_product" />

<table cellpadding="4px">
<?php if(branch_wise_product=='yes'){
		if(no_of_filter > 1)  $onclickbranch = "sel_product_group(this.value);";
		else				  $onclickbranch = "select_product();";
	?>
  <tr>
  	<td align="right">Depot:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
    <td align="left">
    	<select name="depot" id="depot" onchange="<?php echo  $onclickbranch ;?>"
			<option value="">Select</option>
            <?php
			$sql_depot = "SELECT DISTINCT BM.branch_name, BM.branch_code FROM branch_master BM ORDER BY BM.branch_name ASC";
			$res_depot = mysql_query($sql_depot);
			while($row_depot = mysql_fetch_array($res_depot)){
				echo "<option value=\"'".$row_depot['branch_code']."'\">".$row_depot['branch_name']."</option>";
				$branch_code_string .= "'".$row_depot['branch_code']."',";
			}
			$branch_code_string = rtrim($branch_code_string,",");
			?>
            <option value="<?php echo $branch_code_string; ?>">All</option>
		 </select>
    </td>
  </tr>
  <?php }
  if(state_wise_mrp=='yes'){?>
  <tr>
  	<td align="right">State:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
    <td align="left">
    	<select name="state" id="state" >
			<option value="">Select</option>
            <?php
			$sql_state = "SELECT DISTINCT state_code,state_name FROM state_master ORDER BY state_name ASC";
			$res_state = mysql_query($sql_state);
			while($row_state = mysql_fetch_array($res_state)){
				echo "<option value=\"'".$row_state['state_code']."'\">".$row_state['state_name']."</option>";
				$state_code_string .= "'".$row_state['state_code']."',";
			}
			$state_code_string = rtrim($state_code_string,",");
			?>
            <option value="<?php echo $state_code_string; ?>">All</option>
		 </select>
    </td>
  </tr>
  <?php } if(no_of_filter > 1){
	  	if(no_of_filter > 2)  $onclick = "sel_product_subgroup(this.value);";
		else				  $onclick = "select_product();";		
	  ?>
     <tr>
        <td align="right" width="25%"  valign="top">Product Group:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left">
              <select name="prod_group" id="prod_group" onchange="<?php echo $onclick;?>">
                <option value="">Select</option>
                <?php
                $sql_product_group = "SELECT  PGM.product_group_code, PGM.product_group_name FROM product_group_master PGM 
									WHERE PGM.acedns='Y' ORDER BY PGM.product_group_name ASC";
                $res_product_group = mysql_query($sql_product_group);
                while($row_product_group = mysql_fetch_array($res_product_group)){
                    echo "<option value=\"'".$row_product_group['product_group_code']."'\">".$row_product_group['product_group_name']."</option>";
                    $prod_group_string .= "'".$row_product_group['product_group_code']."',";
                }
                $prod_group_string = rtrim($prod_group_string,",");
                ?>
                <option value="<?php echo $prod_group_string; ?>">All</option>
             </select>
        	</td>
      </tr> 
      <tr>
        <td align="right" width="25%"  valign="top">Select SKU:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
         <td align="left"><div style="max-height:500px; overflow-y: scroll;" > 
        <?php $prod_code=$_REQUEST['prod_code'];?>
             <table>
                <tr>
                    <td align="left">
                        <input type="checkbox" name="all_checked_prod" id="all_checked_prod" value="allprod" onchange="javascript:checked_all_prod();"/>ALL
                    </td>
                 </tr> 
                 <tr><td><table id="showproductdetails" ></table></td></tr>
              </table>
              </div>
         </td>
        </tr> 
      <?php }?>
        <tr>
            <td align="right" width="25%"  valign="top"></td>    
            <td align="left"><input name="submit" type="submit" value=" Generate Focus Product "/></td>
        </tr>    
    </table>
    </form>
    </td>
    </tr>
</table>
</center>
<script>
function sel_product_subgroup(zone){
	if(document.getElementById("zone").value.search(/\S/) == -1)
		return false;
	var zone = encodeURIComponent(zone);
	document.getElementById("state_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=state','state_select_div',0);
}
function checked_all_prod()
{
  checkboxesprod = document.getElementsByName('prod_code[]');
  if(document.getElementById("all_checked_prod").checked==true)
  {
	  for(var i in checkboxesprod)
	  checkboxesprod[i].checked = true;
  }
  else
  {
	 for(var i in checkboxesprod)
	 checkboxesprod[i].checked = false;
  }
}
function validation()
{
	
	if(document.getElementById("prod_group").value.search(/\S/) == -1)
	{
		alert('Select Product Group');
		return false;
	}
	var is_checked=false;
	for(i=0; i<document.focus_product.elements.length; i++){
		if(document.focus_product.elements[i].type=="checkbox" && document.focus_product.elements[i].checked==true 
				&& document.focus_product.elements[i].name=='prod_code[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one sku.");
		return false;
	}
	return true;
}
function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}

	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
function select_product()
{
	var prodgroup=document.getElementById("prod_group").value;
	if(document.getElementById("depot"))
	{
		var branch=document.getElementById("depot").value;
	}
	else
	{
		var branch='';
	}
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="get_product_related_data.php?prod_group_code="+prodgroup+"&branch="+branch+"&mode=productsel";
	xmlHttp.onreadystatechange=productdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function productdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showproductdetails").innerHTML=val;
		 }
	}
 }
function sel_product_group()
{
	var branch=document.getElementById("depot").value;
	xmlHttpProduct=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="get_product_related_data.php?branch="+branch+"&mode=productgroupsel";
	xmlHttpProduct.onreadystatechange=productgroupdetails;
	xmlHttpProduct.open("GET",url,true);
	xmlHttpProduct.send(null);
}
function productgroupdetails()
 {
    if(xmlHttpProduct.readyState==4 || xmlHttpProduct.readyState=="complete")
	 {
		var val=xmlHttpProduct.responseText;
		if(val!="")
		 {
			 document.getElementById("showproductdetails").innerHTML=val;
		 }
	}
 }
 
function defocus_product(product_code)
{
	xmlHttpdefocus=GetXmlHttpObject()
	if (xmlHttpdefocus==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	if(confirm("Are you sure to defocus the product"))
	{
		var url="get_product_related_data.php?prod_code="+product_code+"&mode=defocusing";
		xmlHttpdefocus.onreadystatechange=defocusingproduct;
		xmlHttpdefocus.open("GET",url,true);
		xmlHttpdefocus.send(null);
	}
	else
	{
		document.getElementById("prod_id_"+product_code).checked = true;
	}

}
function defocusingproduct()
 {
    if(xmlHttpdefocus.readyState==4 || xmlHttpdefocus.readyState=="complete")
	 {
		var val=xmlHttpdefocus.responseText;
		alert("Defocused successfully");
		//alert(document.getElementById("prod_id_"+val).style.color);
		document.getElementById("prod_id_"+val).style.color = 'black';
	 }
}
/*function sel_product()
{
	checkboxes = document.getElementsByName('product_group_code[]');
	var valsoilgroup='';
	for(var i=0, n=checkboxes.length;i<n;i++) {
	  if (checkboxes[i].checked==true) 
	  {
	  	valsoilgroup += ","+checkboxes[i].value;
	  }
	}
	valsoilgroup=valsoilgroup.substr(1);
	if(valsoilgroup=='')
	{
		alert('Please select at least one Oil Group');
	}
  checkboxestype = document.getElementsByName('pack_size[]');
	var valstype='';
	for(var i=0, n=checkboxestype.length;i<n;i++) {
	  if (checkboxestype[i].checked==true) 
	  {
	  	valstype += ","+checkboxestype[i].value;
	  }
	}
	valstype=valstype.substr(1);
	if(valstype=='')
	{
		alert('Please select at least one Type');
	}
  checkboxesbranch = document.getElementsByName('branch_code[]');
	var valsbranch='';
	for(var i=0, n=checkboxesbranch.length;i<n;i++) {
	  if (checkboxesbranch[i].checked==true) 
	  {
	  	valsbranch += ","+checkboxesbranch[i].value;
	  }
	}
	valsbranch=valsbranch.substr(1);
	//alert(valsoilgroup);
	//alert(valstype);
	//alert(valsbranch);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="returnproductdetailsmargin.php?prod_group_code="+valsoilgroup+"&prodtype="+valstype+"&prodbranch="+valsbranch;
	xmlHttp.onreadystatechange=productdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function productdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("showproductdetails").innerHTML=val;
		 }
	}
 }
function select_depot()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var plant_name = document.getElementById("plant_name").value;
	var url="returnbranchmargincost.php?plant_name="+plant_name;
	xmlHttp.onreadystatechange=depotdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function depotdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showbranchdetails").innerHTML=val;
		 }
	}
 }*/

</script>
<?php
/*echo '<pre>';
print_r($_POST);
echo '</pre>';*/
	if($_REQUEST['mode']=='generate_focus_product')
	{
		$product_group_code=$_POST['product_group_code'];
		$product_group_code="'".implode("','", $product_group_code)."'";
		$branch_code=$_POST['branch_code'];
		$prod_code_array=$_POST['prod_code'];
		$prod_code="'".implode("','", $prod_code_array)."'";
		
		$sqlupdatefocus="UPDATE product_master SET focus='Y',download_time=CURRENT_TIMESTAMP() WHERE prod_code IN(".$prod_code.")";
		if(mysql_query($sqlupdatefocus))
		{
			foreach($prod_code_array as $prod_code_val)
			{
				$sqldnsprodcode="SELECT dns_prod_code FROM product_master WHERE prod_code='".$prod_code_val."'";
				$rsdnsprodcode=mysql_query($sqldnsprodcode);
				$rowdnsprodcode=mysql_fetch_array($rsdnsprodcode);
				$dns_prod_code=$rowdnsprodcode['dns_prod_code'];
				
				$sqlinsertlog="INSERT INTO  focus_product_log SET prod_code='".$prod_code_val."',
																	dns_prod_code='".$dns_prod_code."',
																	operation_date=CURDATE(),
																	operated_by='".$_SESSION['admin_login']."',
																	is_focus='Y'";
				mysql_query($sqlinsertlog);													
			}
		}
		$successval=1;
		if($successval==1)
		{ ?><script language="JavaScript" type="text/javascript">alert('Focus product generated successfully.');window.location.href='generate_focus_product.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Focus product generation unsuccessful.');window.location.href='generate_focus_product.php';</script>
		<?php
		}
	}
}
?>