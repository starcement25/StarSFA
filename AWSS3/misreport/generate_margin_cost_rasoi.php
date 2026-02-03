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
<table cellpadding="4px" width="60%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center"><?php if(strtoupper($_SESSION['vertical_value'])=='SPECIALTY FATS'){?>Generate Margin SF<?php }else{?>Generate Margin Rasoi<?php }?></td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_margin_cost_rasoi.php" name="margin_cost" onSubmit="return validation();" method="post">
<input type='hidden' name="mode" value="generate_margin_cost_rasoi" />
<table cellpadding="4px">
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
                $rsplant=mysql_query($sqlplant);
                $cnt=0;
                while($rowplant=mysql_fetch_array($rsplant))
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
				$rsstate=mysql_query($sqlstate);
				$cnt=0;
				while($rowstate=mysql_fetch_array($rsstate)){
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
     <tr>
       <td align="right" width="25%"  valign="top">Select SKU:<font color="#FF0000">*</font></td>
    		<td align="left"><?php $prod_code=$_REQUEST['prod_code'];
			$content='<select name="prod_code" id="prod_code">';
			$content.='<option value="">SELECT</option>';
			$sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE 
						acedns='Y' AND black_list='N' AND vertical_value='".$_SESSION['vertical_value']."' 
						and product_group_code IN(SELECT product_group_code FROM product_group_master WHERE formulation='yes') ORDER BY prod_desc ASC";
			$rsproddesc=mysql_query($sqlproddesc);
			while($rowproddesc=mysql_fetch_array($rsproddesc))
			{
				$content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
			}
			 $content.='</select>';
		
			echo $content;
			?>
     	</td>
    </tr>           
    <tr>
         <?php $margin_cost	=$_REQUEST['margin_cost'];
                if($margin_cost=='') $margin_cost=0;
         ?>         
         <td align="right" width="25%"  valign="top">Margin(MT):</td>
        <td align="left"><input type="text" name="margin_cost" id="margin_cost" style="height:20px;" value="<?php echo $margin_cost;?>"/></td>
    </tr>
     <!--tr>
         <?php /*$auth_one_limit	=$_REQUEST['auth_one_limit'];
                if($auth_one_limit=='') $auth_one_limit=0;*/
         ?>         
         <td align="right" width="25%"  valign="top">Authorization one limit(MT):</td>
        <td align="left"><input type="text" name="auth_one_limit" id="auth_one_limit" style="height:20px;" value="<?php echo $auth_one_limit;?>"/></td>
    </tr-->
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
	var is_checked=false;
	for(i=0; i<document.margin_cost.elements.length; i++){
		if(document.margin_cost.elements[i].type=="checkbox" && document.margin_cost.elements[i].checked==true 
				&& document.margin_cost.elements[i].name=='state_code[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one State");
		return false;
	}
	/*var is_checked=false;
	for(i=0; i<document.margin_cost.elements.length; i++){
		if(document.margin_cost.elements[i].type=="checkbox" && document.margin_cost.elements[i].checked==true 
				&& document.margin_cost.elements[i].name=='plant_name[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one Plant");
		return false;
	}
	var is_checked_depot=false;
	for(i=0; i<document.margin_cost.elements.length; i++){
		if(document.margin_cost.elements[i].type=="checkbox" && document.margin_cost.elements[i].checked==true 
				&& document.margin_cost.elements[i].name=='branch_code[]'){
			is_checked_depot=true;
			break;
		}
	}
	if(!is_checked_depot){
		alert("Please check at least one Depot");
		return false;
	}*/
	if(document.getElementById("prod_code").value.search(/\S/) == -1)
	{
		alert('Select SKU');
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
	if($_REQUEST['mode']=='generate_margin_cost_rasoi')
	{
		/*$branch_code=$_POST['branch_code'];
		$branch_code_array=$_POST['branch_code'];
		$branch_code="'".implode("','", $branch_code)."'";*/
		
		$state_code=$_POST['state_code'];
		$state_code_array=$_POST['state_code'];
		$state_code="'".implode("','", $state_code)."'";
		/*$plant_name=$_POST['plant_name'];
		$plant_name_array=$_POST['plant_name'];
		$plant_name="'".implode("','", $plant_name)."'";*/		
		$prod_code=$_POST['prod_code'];
		//$prod_code="'".implode("','", $prod_code)."'";
		$margin_cost=$_POST['margin_cost'];
		$auth_one_limit=$_POST['auth_one_limit'];
		$current_date=date('Y-m-d');
		
			/*$sqlselectdistinctdnsprod="SELECT DISTINCT branch_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(".$branch_code.") AND dns_prod_code='".$prod_code."'";
			$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
			{
				$distinct_dnsprod_code=$prod_code;
				$distinct_branch_code=$rowselectdistinctdnsprod['branch_code'];
				$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsdistinctplant=mysql_query($sqldistinctplant);
				$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
				$distinct_plant_name=$rowdistinctplant['plant_name'];*/

				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
									dns_prod_code='".$prod_code."'";
				$rsconversionfactor=mysql_query($sqlconversionfactor);
				$rowconversionfactor=mysql_fetch_array($rsconversionfactor);

				${conversion_factor.$prod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$prod_code}=$rowconversionfactor['conversion_factor_two'];
				${product_group_code.$prod_code}=$rowconversionfactor['product_group_code'];
				
				/*$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$prod_code}."'";
				$rschkformulation=mysql_query($sqlchkformulation);
				$rowchkformulation=mysql_fetch_array($rschkformulation);
				$is_formulation=$rowchkformulation['formulation'];*/

				$magin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$prod_code};
				$magin_cost_case_prodwise=round(($magin_cost_case_prodwise*${conversion_factor.$prod_code}),2);

				/*if($auth_one_limit >0){
				$auth_one_limit_case_prodwise=$auth_one_limit/${conversion_factor_two.$distinct_dnsprod_code};
				$auth_one_limit_case_prodwise=round(($auth_one_limit_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
				}
				else
				{
					$auth_one_limit_case_prodwise=0;
				}*/
				foreach($state_code_array as $state_code_val){
				$sqlinsertmargincost="INSERT INTO margin_cost 
							SET dns_prod_code='".$prod_code."',
							state_code='".$state_code_val."',
							margin_cost='".$magin_cost_case_prodwise."',
							margin_cost_ton='".$margin_cost."',
							auth_one_limit='".$auth_one_limit_case_prodwise."',
							 vertical_value='".$_SESSION['vertical_value']."',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							datetime=CURRENT_TIMESTAMP";
				mysql_query($sqlinsertmargincost);
				$sqlinsertmargincostlog="INSERT INTO margin_cost_log 
							SET state_code='".$state_code_val."',
							margin_cost='".$margin_cost."',
							dns_prod_code='".$prod_code."',
							auth_one_limit=0,
							 vertical_value='".$_SESSION['vertical_value']."',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							operation_type='GENERATE',
							datetime=CURRENT_TIMESTAMP";
				mysql_query($sqlinsertmargincostlog);
				//exit();

				/*$sqlinsertmargincostlog="INSERT INTO margin_cost_log 
							SET dns_prod_code='".$distinct_dnsprod_code."',
							branch_code='".$distinct_branch_code."',
							margin_cost='".$margin_cost."',
							auth_one_limit='".$auth_one_limit_case_prodwise."',
							 vertical_value='".$_SESSION['vertical_value']."',
							ip_address='".$_SERVER['REMOTE_ADDR']."',
							datetime=CURRENT_TIMESTAMP";
				mysql_query($sqlinsertmargincostlog);*/
				
				/*if($is_formulation=='yes')
				{
					$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
											product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
					$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
				}
				else
				{
					$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
											product_group_code='".${product_group_code.$distinct_dnsprod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
					$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
				}
				if($cntchkpricegeneration > 0)
				{
					generate_price_details($distinct_dnsprod_code,$distinct_branch_code);
				}*/
				}
		$successval=1;
		
		if($successval==1)
		{ 
			 /*foreach($branch_code_array as $branch_code_val){
					 $sqldnsbranchcode="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code_val."'";
					 $rsbranchcode=mysql_query($sqldnsbranchcode);
					 $rowbranchcode=mysql_fetch_array($rsbranchcode);
					 $dns_branch_code=$dns_branch_code.$rowbranchcode['dns_branch_code'].",";
			}
			$dns_branch_code=substr($dns_branch_code,0,-1);
			 
			 foreach($plant_name_array as $plant_name_val){
				 $plantname=$plantname.$plant_name_val.",";
			 }
			 $plantname=substr($plantname,0,-1);*/
		?><script language="JavaScript" type="text/javascript">alert('Margin generated successfully.');window.location.href='generate_margin_cost_rasoi.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Margin generation unsuccessful.');window.location.href='generate_margin_cost_rasoi.php';</script>
		<?php
	}
 }
}
?>