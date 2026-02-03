<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
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
    	<td align="center">Generate Depot Cost</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_depot_cost_modified.php" name="depot_cost"  method="post" onsubmit="return validation();">
<input type='hidden' name="mode" value="generate_depot_cost" />
<table cellpadding="4px">
	   <!--tr>
        <td align="right" width="25%"  valign="top">Select Oil Group:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left"-->
        <?php /*$product_group_code=$_REQUEST['product_group_code'];?>
        <!--select name="product_group_code" id="product_group_code" >
        	<option value="" selected>Select</option-->
            <table>
                <tr>
                    <td align="left">
                         <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                    </td>
                 </tr>  
                <?php 
                /*$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master ORDER BY product_group_name ASC";
                $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                {
                ?>
                <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>selected<?php }?>><?php echo $rowproductgroup['product_group_name'];?></option>
                <?php
                }*/
                /*$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master  
										WHERE vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
                $rsproductgroup=mysqli_query($link,$sqlproductgroup);
                while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
                {?>
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="product_group_code[]" value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>checked<?php }?>/><?php echo $rowproductgroup['product_group_name'];?>
                        </td>
                     </tr>   
                <?php
                }*/
            ?>
        <!--/select-->
        	<!--</table>
        </td>
      </tr-->  
	  <tr>
        <td align="right" width="25%"  valign="top">Select Plant:<font color="#FF0000">*</font>&nbsp;&nbsp;
		</td>
        <td align="left">
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <table>
            <tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_plant" id="all_checked_plant" value="allplant" onchange="javascript:checked_all_plant();select_depot_details();"/>ALL
                </td>
             </tr>  
        <?php
        	$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysqli_query($link,$sqlplant);
			$cnt=0;
			while($rowplant=mysqli_fetch_assoc($rsplant))
			{
				$cnt++;
			?>
          		<tr>
                    <td align="left">
                        <input type="checkbox" name="plant_name[]" value="<?php echo $rowplant['plant_name'];?>" onchange="javascript:select_depot_details();"/><?php echo $rowplant['plant_name'];?>
                    </td>
                 </tr>   
            <?php
			}
		?>	
    </tr>
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
                    <input type="checkbox" name="all_checked_branch" id="all_checked_branch" value="allbranch" onchange="javascript:checked_all_branch();"/>ALL
                </td>
             </tr>  
        	<tr><td><table id="showbranchdetails" ></table></td></tr>
         </table>
         </div>
       </td>  
    </tr>
     <tr>
    	 <?php $depot_cost	=$_REQUEST['depot_cost'];?>
         <td align="right" width="25%"  valign="top">Depot Cost(MT):</td>
        <td align="left" ><input type="text" name="depot_cost" id="depot_cost" style="height:20px;" value="<?php echo $depot_cost;?>"/></td>
    </tr>
    <tr>
    	<td align="right" width="25%"  valign="top"></td>
    	<td align="center" ><input name="submit" type="submit" value=" Generate Depot Cost "/></td>
    </tr>
</table>
</form>
</td></tr></table>
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
function checked_all_plant()
{
  checkboxesplant = document.getElementsByName('plant_name[]');
  if(document.getElementById("all_checked_plant").checked==true)
  {
	  for(var i in checkboxesplant)
	  checkboxesplant[i].checked = true;
  }
  else
  {
	 for(var i in checkboxesplant)
	 checkboxesplant[i].checked = false;
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

function validation()
{
	/*if(document.getElementById("branch_code").value.search(/\S/) == -1)
	{
		alert('Select Depot');
		return false;
	}*/
	/*var flag=false;
	var cbs = document.getElementsByTagName('input');
	  for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'checkbox') {
		  if(cbs[i].checked ==true)
		  {
			  var flag=true;
		  }
		}
	  }
	  if(flag==false)
	  {
		  alert("Please select at least one oil group");
		  return false;
	  }
	  var flag=false;
	  //var flagplant=false;

	  checkboxes = document.getElementsByName('product_group_code[]');
	  //checkboxesplant = document.getElementsByName('plant_name[]');

	   for(var i in checkboxes){
		   if(checkboxes[i].checked==true)
		   {
			   var flag=true;
		   }
	   }
	   if(flag==false)
		  {
			  alert("Please select at least one oil group");
			  return false;
		  }*/
	  /* for(var k in checkboxesplant){
		   if(checkboxesplant[k].checked==true)
		   {
			   var flagplant=true;
		   }
	   }
	   if(flagplant==false)
		  {
			  alert("Please select at least one plant");
			  return false;
		  }*/
		var is_checked=false;
		var is_checked_depot=false;
		for(i=0; i<document.depot_cost.elements.length; i++){
			if(document.depot_cost.elements[i].type=="checkbox" && document.depot_cost.elements[i].checked==true 
					&& document.depot_cost.elements[i].name=='plant_name[]'){
				is_checked=true;
				break;
			}
		}
		if(!is_checked){
			alert("Please check at least one Plant");
			return false;
		}
		for(i=0; i<document.depot_cost.elements.length; i++){
			if(document.depot_cost.elements[i].type=="checkbox" && document.depot_cost.elements[i].checked==true 
					&& document.depot_cost.elements[i].name=='branch_code[]'){
				is_checked_depot=true;
				break;
			}
		}
		if(!is_checked_depot){
			alert("Please check at least one Depot");
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
function previous_depot_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_code = document.getElementById("prod_code").value;
	var branch_code = document.getElementById("branch_code").value;

	var url="returnpreviousdepotcost.php?prod_code="+prod_code+"&branch_code="+branch_code;
	xmlHttp.onreadystatechange=previousdepotcost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previousdepotcost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		document.getElementById("depot_cost").value=val;
	 }
 }
function select_product()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var prod_group_code = document.getElementById("product_group_code").value;
	var branch_code = document.getElementById("branch_code").value;
	
	var url="returnproductdetailsdepotcost.php?prod_group_code="+prod_group_code+"&branch_code="+branch_code+"&plant_name="+plant_name;
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
function select_depot_details()
{
	  checkboxesplant = document.getElementsByName('plant_name[]');
		var valsplant='';
		for(var i=0, n=checkboxesplant.length;i<n;i++) {
		  if (checkboxesplant[i].checked==true) 
		  {
			valsplant += ","+checkboxesplant[i].value;
		  }
		}
		valsplant=valsplant.substr(1);
		if(valsplant=='')
		{
			alert('Please select at least one Plant');
		}
	//alert(valsplant);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	//var plant_name = document.getElementById("plant_name").value;
	var url="returndepotdetailscheckbox.php?plant_name="+valsplant;
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
			 //alert(val);
			 document.getElementById("showbranchdetails").innerHTML=val;
		 }
	}
 }

</script>
<?php
	if($_REQUEST['mode']=='generate_depot_cost')
	{
		//$product_group_code=$_POST['product_group_code'];
		//$product_group_code_array=$_POST['product_group_code'];
		//$product_group_code="'".implode("','", $product_group_code)."'";
		$depot_cost=$_POST['depot_cost'];
		$plant_name=$_POST['plant_name'];
		$plant_name="'".implode("','", $plant_name)."'";
		$branch_code=$_POST['branch_code'];
		$branch_code_array=$_POST['branch_code'];
		$branch_code="'".implode("','", $branch_code)."'";
		$current_date=date('Y-m-d');
		
		/*$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code IN(".$product_group_code.") 
									AND branch_code IN(SELECT branch_code FROM branch_master 
									WHERE plant_name IN(".$plant_name.") AND branch_code NOT IN ('B0003','B0007'))";*/
		
		//$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
			

			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,branch_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(".$branch_code.")";
			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
			{
				$distinct_branch_code=$rowselectdistinctdnsprod['branch_code'];
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
				$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
				$distinct_plant_name=$rowdistinctplant['plant_name'];
				
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
									dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

				${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
				${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];
				
				$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
				$rschkformulation=mysqli_query($link,$sqlchkformulation);
				$rowchkformulation=mysqli_fetch_assoc($rschkformulation);
				$is_formulation=$rowchkformulation['formulation'];

				$depot_cost_case_prodwise=$depot_cost/${conversion_factor_two.$distinct_dnsprod_code};
				$depot_cost_case_prodwise=round(($depot_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
		
				$sqlinsertdeptcost="INSERT INTO depot_cost 
									SET dns_prod_code='".$distinct_dnsprod_code."',
									branch_code='".$distinct_branch_code."',
									depot_cost='".$depot_cost_case_prodwise."',
									depot_cost_ton='".$depot_cost."',
									 vertical_value='".$_SESSION['vertical_value']."',
									 ip_address='".$_SERVER['REMOTE_ADDR']."',
									datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertdeptcost);
				/*$sqlinsertdeptcostlog="INSERT INTO depot_cost_log 
									SET dns_prod_code='".$distinct_dnsprod_code."',
									branch_code='".$distinct_branch_code."',
									depot_cost='".$depot_cost."',
									 vertical_value='".$_SESSION['vertical_value']."',
									 ip_address='".$_SERVER['REMOTE_ADDR']."',
									datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertdeptcostlog);*/
				if($is_formulation=='yes')
				{
					$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
											product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
					$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);
				}
				else
				{
					$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
											product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	
					$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					
				}
				if($cntchkpricegeneration > 0)
				{
					generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
				}
			}
		$successval=1;
		if($successval==1)
		{ 
			 foreach($branch_code_array as $branch_code_val){
				 $sqldnsbranchcode="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code_val."'";
				 $rsbranchcode=mysqli_query($link,$sqldnsbranchcode);
				 $rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
				 $dns_branch_code=$rowbranchcode['dns_branch_code'];
				 
				 $sqlinsertdeptcostlog="INSERT INTO depot_cost_log 
								SET branch_code='".$dns_branch_code."',
								depot_cost='".$depot_cost."',
								vertical_value='".$_SESSION['vertical_value']."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='GENERATE',
								datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertdeptcostlog);
			 }
		?><script language="JavaScript" type="text/javascript">alert('Depot cost generated successfully.');window.location.href='generate_depot_cost_modified.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Depot cost generation unsuccessful.');window.location.href='generate_depot_cost_modified.php';</script>
		<?php
		}
	}
}
?>