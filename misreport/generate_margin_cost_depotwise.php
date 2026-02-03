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
    	<td align="center">Generate Margin</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_margin_cost_depotwise.php" name="margin_cost" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_margin_cost" />
<table cellpadding="4px">
	 <!--tr>
        <td align="right" width="25%"  valign="top">Select Oil Group:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
          <td align="left">
        <?php /*$product_group_code=$_REQUEST['product_group_code'];?>
            <table>
                <tr>
                    <td align="left">
                         <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                    </td>
                 </tr>  
                <?php
                //$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
                $sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE 
				vertical_value='".$_SESSION['vertical_value']."' AND acedns='Y' ORDER BY product_group_name ASC";
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
			}*/
		?>	
        </table>
       </td>  
    </tr-->
    <tr>
    	<td align="right" width="25%"  valign="top">Select Oil Group:<font color="#FF0000">*</font></td>
        <td align="left">
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code" >
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE vertical_value='".$_SESSION['vertical_value']."' ORDER BY product_group_name ASC";
			$rsproductgroup=mysqli_query($link,$sqlproductgroup);
			while($rowproductgroup=mysqli_fetch_assoc($rsproductgroup))
			{
			?>
            <option value="<?php echo $rowproductgroup['product_group_code'];?>" <?php if($product_group_code==$rowproductgroup['product_group_code']){?>selected<?php }?>><?php echo $rowproductgroup['product_group_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
      </tr> 
      <tr>
      <td align="right" width="25%"  valign="top">Select Pack Type:<font color="#FF0000">*</font></td>
        <td align="left">
        <?php $pack_type=$_REQUEST['pack_type'];?>
        <select name="pack_type" id="pack_type" >
        	<option value="" selected>Select</option>
            <?php 
			$sqltype="SELECT DISTINCT pack_size FROM product_master ORDER BY pack_size ASC";
			$rstype=mysqli_query($link,$sqltype);
			$cnt=0;
			while($rowtype=mysqli_fetch_assoc($rstype))
			{
			?>
            <option value="<?php echo $rowtype['pack_size'];?>" <?php if($pack_type==$rowtype['pack_size']){?>selected<?php }?>><?php echo $rowtype['pack_size'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
      </tr>   
     <tr>
        <td align="right" width="25%"  valign="top">Select State:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:200px; overflow-y: scroll;">
        <?php $sate_code=$_REQUEST['sate_code'];?>
        <table>
            <tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_state" id="all_checked_state" value="allstate" onchange="javascript:checked_all_state();"/>ALL
                </td>
             </tr>  
				<?php
				$sqlstate="SELECT dns_state_code FROM state_master ORDER BY dns_state_code ASC";
				$rsstate=mysqli_query($link,$sqlstate);
				$cnt=0;
				while($rowstate=mysqli_fetch_assoc($rsstate)){
                    $cnt++;
                ?>
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="state_code[]" value="<?php echo $rowstate['dns_state_code'];?>" />
							<?php echo $rowstate['dns_state_code'];?>
                        </td>
                     </tr>   
                <?php
                }
                ?>	
         </table>
         </div>
       </td>  
    </tr>
    <!--tr>
        <td align="right" width="25%"  valign="top">Select Plant:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:200px; overflow-y: scroll;">
        <?php /*$plant_name=$_REQUEST['plant_name'];?>
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
                            <input type="checkbox" name="plant_name[]" value="<?php echo $rowplant['plant_name'];?>"  onchange="javascript:select_depot_details();"/><?php echo $rowplant['plant_name'];?>
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
        <td align="right" width="25%"  valign="top">Select Depot:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:200px; overflow-y: scroll;">
        <?php $branch_code=$_REQUEST['branch_code'];*/?>
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
    </tr-->
        <tr>
			 <?php $margin_cost	=$_REQUEST['margin_cost'];
			 		if($margin_cost=='') $margin_cost=0;
			 ?>         
             <td align="right" width="25%"  valign="top">Margin(MT):</td>
            <td align="left"><input type="text" name="margin_cost" id="margin_cost" style="height:20px;" value="<?php echo $margin_cost;?>"/></td>
    	</tr>
         <tr>
			 <?php $auth_one_limit	=$_REQUEST['auth_one_limit'];
			 		if($auth_one_limit=='') $auth_one_limit=0;
			 ?>         
             <td align="right" width="25%"  valign="top">Authorization one limit(MT):</td>
            <td align="left"><input type="text" name="auth_one_limit" id="auth_one_limit" style="height:20px;" value="<?php echo $auth_one_limit;?>"/></td>
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
function checked_all_state()
{
  checkboxesstate = document.getElementsByName('state_code[]');
  if(document.getElementById("all_checked_state").checked==true)
  {
	  for(var i in checkboxesstate)
	  checkboxesstate[i].checked = true;
  }
  else
  {
	 for(var i in checkboxesstate)
	 checkboxesstate[i].checked = false;
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

function validation()
{
	
	/*if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}*/
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
	if(document.getElementById("pack_type").value.search(/\S/) == -1)
	{
		alert('Select Pack type');
		return false;
	}
	var is_checked=false;
	for(i=0; i<document.margin_cost.elements.length; i++){
		if(document.margin_cost.elements[i].type=="checkbox" && document.margin_cost.elements[i].checked==true 
				&& document.margin_cost.elements[i].name=='state_code[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one state");
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
    /*checkboxesbranch = document.getElementsByName('branch_code[]');
	var valsbranch='';
	for(var i=0, n=checkboxesbranch.length;i<n;i++) {
	  if (checkboxesbranch[i].checked==true) 
	  {
	  	valsbranch += ","+checkboxesbranch[i].value;
	  }
	}
	valsbranch=valsbranch.substr(1);*/
	
	checkboxesplant = document.getElementsByName('plant_name[]');
	var valsplant='';
	for(var i=0, n=checkboxesplant.length;i<n;i++) {
	  if (checkboxesplant[i].checked==true) 
	  {
	  	valsplant += ","+checkboxesplant[i].value;
	  }
	}
	valsplant=valsplant.substr(1);
	
	//alert(valsoilgroup);
	//alert(valstype);
	//alert(valsbranch);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="returnproductdetailsmarginmodified.php?prod_group_code="+valsoilgroup+"&prodtype="+valstype+"&prodplant="+valsplant;
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
		/*$product_group_code_array=$_POST['product_group_code'];
		$product_group_code="'".implode("','", $product_group_code)."'";*/
		$pack_size=$_POST['pack_type'];
		/*$pack_size_array=$_POST['pack_size'];
		$pack_size="'".implode("','", $pack_size)."'";
		$branch_code=$_POST['branch_code'];
		$branch_code_array=$_POST['branch_code'];
		$branch_code="'".implode("','", $branch_code)."'";
		$plant_name=$_POST['plant_name'];
		$plant_name_array=$_POST['plant_name'];
		$plant_name="'".implode("','", $plant_name)."'";*/		
		//$prod_code=$_POST['prod_code'];
		//$prod_code="'".implode("','", $prod_code)."'";
		$state_code=$_POST['state_code'];
		$state_code_array=$_POST['state_code'];
		$state_code="'".implode("','", $state_code)."'";
		$margin_cost=$_POST['margin_cost'];
		$auth_one_limit=$_POST['auth_one_limit'];
		$current_date=date('Y-m-d');
		
	   /*$sqlselbranch="SELECT branch_code FROM branch_master WHERE acedns='Y' AND plant_name IN(".$plant_name.") ";
	   $rsselbranch=mysqli_query($link,$sqlselbranch);
	   while($rowselbranch=mysqli_fetch_assoc($rsselbranch))
	   {
		   $branch_code=$branch_code."'".$rowselbranch['branch_code']."'".',';
	   }
	   $branch_code=substr($branch_code,0,-1);*/

		/*$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code IN(".$product_group_code.") 
									AND branch_code IN(".$branch_code.") AND pack_size IN(".$pack_size.")  AND acedns='Y' AND black_list='N'";
		$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
		while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
		{
			$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
			$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
			$rsdistinctplant=mysqli_query($link,$sqldistinctplant);
			$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);
			$distinct_plant_name=$rowdistinctplant['plant_name'];*/
			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
										product_group_code ='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N'  AND pack_size='".$pack_size."'";
			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))
			{
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
									dns_prod_code='".$distinct_dnsprod_code."'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

				${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
				${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];
				
				$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
				$rschkformulation=mysqli_query($link,$sqlchkformulation);
				$rowchkformulation=mysqli_fetch_assoc($rschkformulation);
				$is_formulation=$rowchkformulation['formulation'];

				$magin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
				$magin_cost_case_prodwise=round(($magin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

				if($auth_one_limit >0){
				$auth_one_limit_case_prodwise=$auth_one_limit/${conversion_factor_two.$distinct_dnsprod_code};
				$auth_one_limit_case_prodwise=round(($auth_one_limit_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
				}
				else
				{
					$auth_one_limit_case_prodwise=0;
				}
				foreach($state_code_array as $state_code_val){
				$sqlinsertmargincost="INSERT INTO margin_cost 
							SET dns_prod_code='".$distinct_dnsprod_code."',
							state_code='".$state_code_val."',
							margin_cost='".$magin_cost_case_prodwise."',
							margin_cost_ton='".$margin_cost."',
							auth_one_limit='".$auth_one_limit_case_prodwise."',
							vertical_value='".$_SESSION['vertical_value']."',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertmargincost);
				}

				/*$sqlinsertmargincostlog="INSERT INTO margin_cost_log 
							SET dns_prod_code='".$distinct_dnsprod_code."',
							branch_code='".$distinct_branch_code."',
							margin_cost='".$margin_cost."',
							auth_one_limit='".$auth_one_limit_case_prodwise."',
							 vertical_value='".$_SESSION['vertical_value']."',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							datetime=CURRENT_TIMESTAMP";
				mysqli_query($link,$sqlinsertmargincostlog);*/
				
				/*if($is_formulation=='yes')
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
				}*/
			}
		//exit();
		$successval=1;
		
		if($successval==1)
		{ 
			/*foreach($branch_code_array as $branch_code_val){
					 $sqldnsbranchcode="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code_val."'";
					 $rsbranchcode=mysqli_query($link,$sqldnsbranchcode);
					 $rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
					 $dns_branch_code=$dns_branch_code.$rowbranchcode['dns_branch_code'].",";
			}
			$dns_branch_code=substr($dns_branch_code,0,-1);
			 
			foreach($product_group_code_array as $product_group_code_val){
				 $sqlprodgroupname="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$product_group_code_val."'";
				 $rsprodgroupname=mysqli_query($link,$sqlprodgroupname);
				 $rowprodgroupname=mysqli_fetch_assoc($rsprodgroupname);
				 $prodgroupname=$prodgroupname.$rowprodgroupname['product_group_name'].",";
			 }
			 $prodgroupname=substr($prodgroupname,0,-1);
			 foreach($pack_size_array as $pack_size_val){
				 $packsize=$packsize.$pack_size_val.",";
			 }
			 $packsize=substr($packsize,0,-1);
			 foreach($plant_name_array as $plant_name_val){
				 $plantname=$plantname.$plant_name_val.",";
			 }
			 $plantname=substr($plantname,0,-1);*/
			foreach($state_code_array as $state_code_val){
				 $dns_state_code=$dns_state_code.$state_code_val.",";
			}
			$dns_state_code=substr($dns_state_code,0,-1);

			$sqlinsertmargincostlog="INSERT INTO margin_cost_log 
							SET oil_group='".$product_group_code."',
							pack_size='".$pack_size."',
							margin_cost='".$margin_cost."',
							state_code='".$dns_state_code."',
							auth_one_limit=0,
							vertical_value='".$_SESSION['vertical_value']."',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							operation_type='GENERATE',
							datetime=CURRENT_TIMESTAMP";
			mysqli_query($link,$sqlinsertmargincostlog);
		?><script language="JavaScript" type="text/javascript">alert('Margin generated successfully.');window.location.href='generate_margin_cost_depotwise.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Margin generation unsuccessful.');window.location.href='generate_margin_cost_depotwise.php';</script>
		<?php
		}
	}
}
?>