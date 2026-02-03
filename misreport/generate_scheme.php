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
<table cellpadding="4px" width="70%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Generate Scheme</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_margin_cost.php" name="margin_cost" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generatescheme" />
<table cellpadding="4px">
	 <tr>
        <td align="left" width="" colspan="2">
          <div id="date_div" style="width:90%;">
            Start Date:<input type="date" name="start_date" id="start_date" style="height:20px;" />
            End Date:<input type="date" name="end_date" id="end_date" style="height:20px;" />
            </div>
          </td>
      </tr>      
	  <tr>
        <td align="right" width="25%"  valign="top">Scheme Type:</td>
        <td align="left">
            <input type="radio" name="scheme_type" id="scheme_type" value="quantity" onchange="javascript:select_scheme_details(this.value);"/>Quantity
            <input type="radio" name="scheme_type" id="scheme_type" value="valuewise" onchange="javascript:select_scheme_details(this.value);"/>Value
            <input type="radio" name="scheme_type" id="scheme_type" value="tob" onchange="javascript:select_scheme_details(this.value);"/>TOB
     	</td>
      </tr>
   	<tr id="quantity_div" style="display:none">
        <td align="center" width="100%"  valign="top" colspan="2">
            <table width="100%" align="center">
            	<tr>
                	<td align="right" width="30%"  valign="top">Scheme Filter:</td>
                    <td align="left">
                    	<select name="scheme_filter" id="scheme_filter" onchange="javscript:selectprod_details(this.value);">
                        	<option value="">SELECT</option>
                         	<option value="1">Product Wise</option>
                          	<option value="2">Product Group Wise</option>
                        </select>
                    </td>
                </tr>
            	<tr id="product_div" style="display:none">
                    <td align="right" width="25%"  valign="top">Select Product:</td>
                        <td align="left"><?php $prod_code=$_REQUEST['prod_code'];
                        $content='<select name="prod_code" id="prod_code" onchange="javascript:quantity_display();">';
                        $content.='<option value="">SELECT</option>';
                        $sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE 
                                    acedns='Y' AND black_list='N' ORDER BY prod_desc ASC";
                        $rsproddesc=mysqli_query($link,$sqlproddesc);
                        while($rowproddesc=mysqli_fetch_assoc($rsproddesc))
                        {
                            $content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
                        }
                         $content.='</select>';
                        echo $content;
                        ?>
                    </td>
               </tr>
               <tr id="product_group_div" style="display:none">
                    <td align="right" width="25%"  valign="top">Select Product Group:</td>
                        <td align="left"><?php $product_group_code=$_REQUEST['product_group_code'];
                        $content='<select name="product_group_code" id="product_group_code" onchange="javascript:quantity_display();">';
                        $content.='<option value="">SELECT</option>';
                        $sqlprodgroupdesc="SELECT product_group_code,product_group_name FROM product_group_master ORDER BY product_group_name ASC";
                        $rsprodgroupdesc=mysqli_query($link,$sqlprodgroupdesc);
                        while($rowprodgroupdesc=mysqli_fetch_assoc($rsprodgroupdesc))
                        {
                            $content.="<option value='".$rowprodgroupdesc['product_group_code']."'>".$rowprodgroupdesc['product_group_name']."</option>";
                        }
                         $content.='</select>';
                        echo $content;
                        ?>
                    </td>
               </tr>
               <tr id="scheme_qty_display" style="display:none">
                	<td align="right" width="25%"  valign="top">Scheme Quantity:</td>
                    <td align="left">
                    	<input type="text" name="scheme_qty" id="scheme_qty" />
                    </td>
                </tr> 
                <tr id="freebies_prod_display" style="display:none">
                	<td align="right" width="25%"  valign="top">Freebie Product Type:</td>
                    <td align="left">
                    	<input type="radio" name="product_type" id="product_type" value="own" onchange="javascript:freebies_prod_details(this.value);"/>Own
            			<input type="radio" name="product_type" id="product_type" value="outside" onchange="javascript:freebies_prod_details(this.value);"/>Outside
                    </td>
                </tr>
                <tr id="freebie_product_div" style="display:none">
                    <td align="right" width="25%"  valign="top">Freebie Product:</td>
                        <td align="left"><?php 
                        $content='<select name="freebie_prod_code" id="freebie_prod_code" onchange="javascript:freebie_quantity_display();">';
                        $content.='<option value="">SELECT</option>';
                        $sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE 
                                    acedns='Y' AND black_list='N' ORDER BY prod_desc ASC";
                        $rsproddesc=mysqli_query($link,$sqlproddesc);
                        while($rowproddesc=mysqli_fetch_assoc($rsproddesc))
                        {
                            $content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
                        }
                         $content.='</select>';
                        echo $content;
                        ?>
                    </td>
               </tr>
               <tr id="freebie_product_input_div" style="display:none">
                    <td align="right" width="25%"  valign="top">Freebie Product:</td>
                    <td align="left"><input type="text" name="freebie_prod_input" id="freebie_prod_input" /></td>
               </tr>
               <tr id="freebie_qty_display" style="display:none">
                	<td align="right" width="25%"  valign="top">Freebie Quantity:</td>
                    <td align="left">
                    	<input type="text" name="freebie_qty" id="freebie_qty" />
                    </td>
                </tr>
                 <tr id="freebie_UOM_display" style="display:none">
                	<td align="right" width="25%"  valign="top">Freebie UOM:</td>
                    <td align="left">
                    	<?php 
                        $content='<select name="freebie_UOM" id="freebie_UOM">';
                        $content.='<option value="">SELECT</option>';
                        $sqlproddesc="SELECT DISTINCT UOM1 AS UOM FROM product_master WHERE acedns='Y' AND black_list='N' UNION ALL  SELECT DISTINCT UOM2 AS UOM FROM product_master WHERE acedns='Y' AND black_list='N' UNION ALL  SELECT DISTINCT UOM3 AS UOM FROM product_master WHERE acedns='Y' AND black_list='N' ORDER BY UOM ASC";
                        $rsproddesc=mysqli_query($link,$sqlproddesc);
                        while($rowproddesc=mysqli_fetch_assoc($rsproddesc))
                        {
                            $content.="<option value='".$rowproddesc['UOM']."'>".$rowproddesc['UOM']."</option>";
                        }
                         $content.='</select>';
                        echo $content;
                        ?>
                    </td>
                </tr>
            </table>
       </td>  
    </tr>
        <tr>
            <td align="right" width="25%"  valign="top"></td>    
            <td align="left"><input name="submit" type="submit" value=" Generate Scheme "/></td>
        </tr>    
    </table>
    </form>
    </td>
    </tr>
</table>
</center>
<script language="javascript">
function quantity_display()
{
	document.getElementById("scheme_qty_display").style.display='';
	document.getElementById("freebies_prod_display").style.display='';
}
function selectprod_details(attval)
{
	if(attval=='1')
	{
		document.getElementById("product_div").style.display='';
		document.getElementById("product_group_div").style.display='none';
	}
	if(attval=='2')
	{
		document.getElementById("product_div").style.display='none';
		document.getElementById("product_group_div").style.display='';
	}
}
function select_scheme_details(typeval)
{
	if(typeval=='quantity')
	{
		document.getElementById("quantity_div").style.display='';
	}
}
function freebies_prod_details(prodtype)
{
	if(prodtype=='own'){
		document.getElementById("freebie_product_div").style.display='';
		document.getElementById("freebie_product_input_div").style.display='none';
		document.getElementById("freebie_qty_display").style.display='';
		document.getElementById("freebie_UOM_display").style.display='';
		document.getElementById("freebieheading").style.display='';
	}
	else
	{
		document.getElementById("freebie_product_input_div").style.display='';
		document.getElementById("freebie_product_div").style.display='none';
		document.getElementById("freebie_qty_display").style.display='';
		document.getElementById("freebie_UOM_display").style.display='';
		document.getElementById("freebieheading").style.display='';
		document.getElementById("freebieheading").style.display='';
	}
}

function validation()
{
	/*if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}*/
	var is_checked=false;
	for(i=0; i<document.margin_cost.elements.length; i++){
		if(document.margin_cost.elements[i].type=="checkbox" && document.margin_cost.elements[i].checked==true 
				&& document.margin_cost.elements[i].name=='product_group_code[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one oil group");
		return false;
	}
	if(document.getElementById("margin_cost").value < 0)
	{
		if(document.getElementById("margin_cost").value <0 && document.getElementById("margin_cost").value >=-100)
		{
			alert('You are at loss');
		}
		else if(document.getElementById("margin_cost").value < -100)
		{
			alert('Exceed Limit');
			return false;
		}
	}
	/*if(document.getElementById("branch_code").value.search(/\S/) == -1)
	{
		alert('Select Depot');
		return false;
	}*/
	//return true;
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
function previous_margin_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_code = document.getElementById("prod_code").value;
	var branch_code = document.getElementById("branch_code").value;

	var url="returnpreviousmargincost.php?prod_code="+prod_code+"&branch_code="+branch_code;
	xmlHttp.onreadystatechange=previousmargincost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previousmargincost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		document.getElementById("margin_cost").value=val;
	 }
 }
function select_prod_details()
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
</script>
<?php
	if($_REQUEST['mode']=='generate_margin_cost')
	{
		$product_group_code=$_POST['product_group_code'];
		$product_group_code="'".implode("','", $product_group_code)."'";
		$pack_size=$_POST['pack_size'];
		$pack_size="'".implode("','", $pack_size)."'";
		$branch_code=$_POST['branch_code'];
		$branch_code="'".implode("','", $branch_code)."'";		
		$prod_code=$_POST['prod_code'];
		$prod_code="'".implode("','", $prod_code)."'";
		$margin_cost=$_POST['margin_cost'];
		
		$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code IN(".$product_group_code.") 
									AND branch_code IN(".$branch_code.") AND pack_size IN(".$pack_size.") AND dns_prod_code IN(".$prod_code.") 
									AND acedns='Y' AND black_list='N'";
		$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
		while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
		{
			$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
										product_group_code IN(".$product_group_code.") AND prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(".$branch_code.") AND pack_size IN(".$pack_size.") 
										AND dns_prod_code IN(".$prod_code.")";
			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
			{
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
									dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

				${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];

				$magin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
				$magin_cost_case_prodwise=round(($magin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

				$sqlinsertmargincost="INSERT INTO margin_cost 
							SET dns_prod_code='".$distinct_dnsprod_code."',
							branch_code='".$distinct_branch_code."',
							margin_cost='".$magin_cost_case_prodwise."',
							datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertmargincost);
				generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
			}
		}
		//exit();
		$successval=1;
		if($successval==1)
		{ ?><script language="JavaScript" type="text/javascript">alert('Margin generated successfully.');window.location.href='generate_margin_cost.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Margin generation unsuccessful.');window.location.href='generate_margin_cost.php';</script>
		<?php
		}
	}
}
?>