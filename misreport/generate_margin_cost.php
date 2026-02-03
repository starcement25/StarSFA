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
    	<td align="center">Generate Margin</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_margin_cost.php" name="margin_cost" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_margin_cost" />
<table cellpadding="4px">
	 <tr>
        <td align="right" width="25%"  valign="top">Select Oil Group:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left">
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
            <table>
                <tr>
                    <td align="left">
                         <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                    </td>
                 </tr>  
                <?php 
                $sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
                $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                {?>
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>checked<?php }?>/><?php echo $rowproductgroup['product_group_name'];?>
                        </td>
                     </tr>   
                <?php
                }
            ?>
        	</table>
        </td>
      </tr>  
	  <tr>
        <td align="right" width="25%"  valign="top">Select Type:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left">
        <?php $pack_size=$_REQUEST['pack_size'];?>
        <table>
            <tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_type" id="all_checked_type" value="alltype" onchange="javascript:checked_all_type();"/>ALL
                </td>
             </tr>  
        	<?php
        	$sqltype="SELECT DISTINCT pack_size FROM product_master ORDER BY pack_size ASC";
			$rstype=mysqli_query($link,$sqltype);
			$cnt=0;
			while($rowtype=mysqli_fetch_assoc($rstype))
			{
				$cnt++;
			?>
          		<tr>
                    <td align="left">
                        <input type="checkbox" name="pack_size[]" value="<?php echo $rowtype['pack_size'];?>" /><?php echo $rowtype['pack_size'];?>
                    </td>
                 </tr>   
            <?php
			}
		?>	
        </table>
       </td>  
    </tr>
   	<tr>
        <td align="right" width="25%"  valign="top">Select Depot:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:200px; overflow-y: scroll;">
        <?php $branch_code=$_REQUEST['branch_code'];?>
        <table>
            <tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_branch" id="all_checked_branch" value="allbranch" onchange="javascript:checked_all_branch();select_prod_details();"/>ALL
                </td>
             </tr>  
        	<?php
        	$sqlbranch="SELECT branch_code,branch_name FROM branch_master WHERE acedns='Y' ORDER BY branch_name ASC";
			$rsbranch=mysqli_query($link,$sqlbranch);
			$cnt=0;
			while($rowbranch=mysqli_fetch_assoc($rsbranch))
			{
				$cnt++;
			?>
          		<tr>
                    <td align="left">
                        <input type="checkbox" name="branch_code[]" value="<?php echo $rowbranch['branch_code'];?>"  onchange="javascript:select_prod_details();"/><?php echo $rowbranch['branch_name'];?>
                    </td>
                 </tr>   
            <?php
			}
		?>	
        
         </table>
         </div>
       </td>  
    </tr>
     <tr>
        <td align="right" width="25%"  valign="top">Select SKU:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
         <td align="left"><div style="max-height:200px; overflow-y: scroll;"> 
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
        <tr>
			 <?php $margin_cost	=$_REQUEST['margin_cost'];
			 		if($margin_cost=='') $margin_cost=0;
			 ?>         
             <td align="right" width="25%"  valign="top">Margin(MT):</td>
            <td align="left"><input type="text" name="margin_cost" id="margin_cost" style="height:20px;" value="<?php echo $margin_cost;?>"/></td>
    	</tr>
        <tr>
            <td align="right" width="25%"  valign="top"></td>    
            <td align="left"><input name="submit" type="submit" value=" Generate Margin "/></td>
        </tr>    
    </table>
    </form>
    </td>
    </tr>
</table>
</center>
<script>
function checked_all()
{
  checkboxes = document.getElementsByName('product_group_code[]');
  if(document.getElementById("all_checked").checked==true)
  {
	  for(var i in checkboxes)
	  checkboxes[i].checked = true;
  }
  else
  {
	   for(var i in checkboxes)
	  checkboxes[i].checked = false;
  }
}
function checked_all_type()
{
  checkboxestype = document.getElementsByName('pack_size[]');
  if(document.getElementById("all_checked_type").checked==true)
  {
	  for(var i in checkboxestype)
	  checkboxestype[i].checked = true;
  }
  else
  {
	 for(var i in checkboxestype)
	 checkboxestype[i].checked = false;
  }
}
function checked_all_branch()
{
  checkboxesbranch = document.getElementsByName('branch_code[]');
  if(document.getElementById("all_checked_branch").checked==true)
  {
	  for(var i in checkboxesbranch)
	  checkboxesbranch[i].checked = true;
  }
  else
  {
	 for(var i in checkboxesbranch)
	 checkboxesbranch[i].checked = false;
  }
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