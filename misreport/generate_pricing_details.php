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
    	<td align="center">Generate Pricing Details</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_pricing_details.php" name="pricing_details" onSubmit="return validation();" method="post">
<input type="hidden" name="pricing_mode" value="create_pricing" />
<table cellpadding="4px">
    <tr>
        <td align="left">Select Plant:
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <select name="plant_name" id="plant_name">
        	<option value="" selected>Select</option>
            <?php 
			$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysqli_query($link,$sqlplant);
			while($rowplant=mysqli_fetch_assoc($rsplant))
			{
			?>
            <option value="<?php echo $rowplant['plant_name'];?>" <?php if($plant_name==$rowplant['plant_name']){?>selected<?php }?>><?php echo $rowplant['plant_name'];?></option>
            <?php
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        
        Select Oil Group:
        <?php $product_group_code=$_REQUEST['product_group_code'];?>
        <select name="product_group_code" id="product_group_code">
        	<option value="" selected>Select</option>
            <?php 
			$sqlproductgroup="SELECT product_group_name,product_group_code FROM product_group_master WHERE acedns='Y' ORDER BY product_group_name ASC";
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
    <?php $loose_rate=$_REQUEST['loose_rate'];
		  $packing_cost=$_REQUEST['packing_cost'];
	?>
        <td align="left">Loose rate(MT):<input type="text" name="loose_rate" id="loose_rate" style="height:20px;" value="<?php echo $loose_rate;?>"/><font color="#FF0000">*</font>&nbsp;
&nbsp;<!--Packing Cost:<input type="text" name="packing_cost" id="packing_cost" style="height:20px;" value="<?php //echo $packing_cost;?>"/>!--></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Generate Price List"/><!--&nbsp;&nbsp;<input name="submit1" type="button" value="Generate Price List"/>!--></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function validation()
{
	if(document.getElementById("plant_name").value.search(/\S/) == -1)
	{
		alert('Select Plant');
		return false;
	}
	
	if(document.getElementById("product_group_code").value.search(/\S/) == -1)
	{
		alert('Select Oil Group');
		return false;
	}
	
	if(document.getElementById("loose_rate").value.search(/\S/) == -1)
	{
		alert('Input Loose Rate');
		return false;
	}
	return true;
}
/*function GetXmlHttpObject()
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
function previous_packing_cost()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var plant_name = document.getElementById("plant_name").value;
	var product_group_code = document.getElementById("product_group_code").value;

	var url="returnpreviouspackcost.php?plant_name="+plant_name+"&product_group_code="+product_group_code;
	xmlHttp.onreadystatechange=previousPackcost;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function previousPackcost()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("packing_cost").value=val;
		 }
	}
 }*/
</script>
<?php
	if($_REQUEST['pricing_mode']=='create_pricing')
	{
		$plant_name=$_POST['plant_name'];
		$product_group_code=$_POST['product_group_code'];
		$loose_rate_ton=$_POST['loose_rate'];
		$folderName='EMAMI';
		$branch_code_array=array();
		
		/*$sqlcheckvalue="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$plant_name."' AND product_group_code='".$product_group_code."' 
					   AND loose_rate_ton='".$loose_rate_ton."'";
		$rscheckvalue=mysqli_query($link,$sqlcheckvalue);
		$countcheckvalue=mysqli_num_rows($rscheckvalue);
		if($countcheckvalue <1)
		{*/				
			$sqlinsertpricingdetails="INSERT INTO pricing_detials 
									  SET loose_rate_ton='".$loose_rate_ton."',
									  plant_name='".$plant_name."',
									  product_group_code='".$product_group_code."',
									  datetime=CURRENT_TIMESTAMP,
									  user_login='".$_SESSION['admin_login']."',
									  user_ip='".$_SERVER['REMOTE_ADDR']."'";
			$rsinsertpricingdetails=mysqli_query($link,$sqlinsertpricingdetails);	
		/*}
		else
		{
			$sqlupdatepricingdetails="UPDATE pricing_detials 
									  SET datetime=CURRENT_TIMESTAMP WHERE loose_rate_ton='".$loose_rate_ton."' AND plant_name='".$plant_name."' AND 
									  product_group_code='".$product_group_code."'";
			$rsupdatepricingdetails=mysqli_query($link,$sqlupdatepricingdetails);
		}*/
		 if($product_group_code !='BR1')
		 {	
			//For product group wise all product data mrp updation on the basis of Loose rate
			$sqlselectdistinctbranch="SELECT DISTINCT branch_code FROM product_master WHERE product_group_code='".$product_group_code."' 
									AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."')";
			$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
			while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
			{
				$distinct_branch_code=$rowselectdistinctbranch['branch_code'];
				/*$sqlfreight="SELECT hire_cost FROM basic_freight WHERE branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
				$rsfreight=mysqli_query($link,$sqlfreight);
				$rowfreight=mysqli_fetch_assoc($rsfreight);
				${hire_cost.$distinct_branch_code}=$rowfreight['hire_cost'];
				if(${hire_cost.$distinct_branch_code}=='')
				{
					${hire_cost.$distinct_branch_code}=0;
				}*/
				
				$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
											product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
											AND acedns='Y' AND black_list='N' AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."')";
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

					$sqlpackingprodwise="SELECT packing_pc,packing_cost FROM packing_master WHERE dns_prod_code='".$distinct_dnsprod_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
					$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
					${packing_pc.$distinct_dnsprod_code}=$rowpackingprodwise['packing_pc'];
					${packing_cost.$distinct_dnsprod_code}=$rowpackingprodwise['packing_cost'];
					
					$sqldepotfreightcostprodwise="SELECT depot_cost,freight FROM depot_freight_cost WHERE dns_prod_code='".$distinct_dnsprod_code."' 
												AND branch_code='".$distinct_branch_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rsdepotfreightcostprodwise=mysqli_query($link,$sqldepotfreightcostprodwise);
					$rowdepotfreightcostprodwise=mysqli_fetch_assoc($rsdepotfreightcostprodwise);
					${depot_cost.$distinct_dnsprod_code}=$rowdepotfreightcostprodwise['depot_cost'];
					${freight.$distinct_dnsprod_code}=$rowdepotfreightcostprodwise['freight'];
					if(${depot_cost.$distinct_dnsprod_code}=='')
					{
						${depot_cost.$distinct_dnsprod_code}=0;
					}

					/*$sqltruckloadqty="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$distinct_dnsprod_code."' ORDER BY datetime DESC LIMIT 0,1";
					$rstruckloadqty=mysqli_query($link,$sqltruckloadqty);
					$rowtruckloadqty=mysqli_fetch_assoc($rstruckloadqty);
					${qty_truck_load.$distinct_dnsprod_code}=$rowtruckloadqty['qty_truck_load'];
					if(${qty_truck_load.$distinct_dnsprod_code}=='')
					{
						${qty_truck_load.$distinct_dnsprod_code}=0;
					}*/
					
					$loose_rate_case_prodwise=round(($loose_rate_ton/${conversion_factor_two.$distinct_dnsprod_code}),2);
					$loose_rate_case_prodwise=round(($loose_rate_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
					//echo ${hire_cost.$distinct_branch_code};
					//echo ${qty_truck_load.$distinct_dnsprod_code};
					/*if(${qty_truck_load.$distinct_dnsprod_code}>0)
					{
						$final_freight_prodwise=${hire_cost.$distinct_branch_code}/${qty_truck_load.$distinct_dnsprod_code};
					}
					else
					{
						$final_freight_prodwise=0;
					}*/
					if(${freight.$distinct_dnsprod_code}=='')
					{
					    ${freight.$distinct_dnsprod_code}=0;
					}
					
					$mrp_prodwise=$loose_rate_case_prodwise+${freight.$distinct_dnsprod_code}+${packing_cost.$distinct_dnsprod_code}+${depot_cost.$distinct_dnsprod_code};
					$mrp_prodwise=round($mrp_prodwise,2);
					
					//exit();
					
					$sql_prod_code="SELECT prod_code,vertical_value FROM product_master WHERE branch_code='".$distinct_branch_code."' AND dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";
					$rs_prod_code=mysqli_query($link,$sql_prod_code);
					$cntprod_code=mysqli_num_rows($rs_prod_code);
					$row_prod_code=mysqli_fetch_assoc($rs_prod_code);
					$prod_code_master=$row_prod_code['prod_code'];
					$vertical_value_master=$row_prod_code['vertical_value'];
					
					if($cntprod_code >0)
					{
						$sqlbranchprodchk="SELECT product_code FROM sauda_mrp WHERE branch_code='".$distinct_branch_code."' AND 
											product_code='".$prod_code_master."'";
								
						$rsbranchprodchk=mysqli_query($link,$sqlbranchprodchk);
						$cntbranchprodchk=mysqli_num_rows($rsbranchprodchk);
						if($cntbranchprodchk >0){
							$sqlupdatemrpprodwise="UPDATE sauda_mrp SET mrp='".$mrp_prodwise."',sale_rate='".$mrp_prodwise."',download_time=CURRENT_TIMESTAMP() 
											WHERE branch_code='".$distinct_branch_code."' AND product_code='".$prod_code_master."'";
							mysqli_query($link,$sqlupdatemrpprodwise);
						}
						else
						{
							$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) 
											AS max_mrp_code from sauda_mrp";
							$rsmaxmrpcode=mysqli_query($link,$sqlmaxmrpcode);
							$rowmaxmrpcode=mysqli_fetch_assoc($rsmaxmrpcode);
							$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
							$max_mrp_code++;
							$max_mrp_code='z'.$max_mrp_code;

						   $sqlinsertmrpprodwise="INSERT INTO sauda_mrp SET mrp_code='".$max_mrp_code."',mrp='".$mrp_prodwise."',sale_rate='".$mrp_prodwise."',
						   							branch_code='".$distinct_branch_code."',product_code='".$prod_code_master."',vertical_value='".$vertical_value_master."',download_time=CURRENT_TIMESTAMP()";
						   mysqli_query($link,$sqlinsertmrpprodwise);
						   
						}
						if(!in_array($distinct_branch_code,$branch_code_array))
						{
							array_push($branch_code_array,$distinct_branch_code);
							$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET('".$distinct_branch_code."', branch_code)";
							$rsbranchwiseemp=mysqli_query($link,$sqlbranchwiseemp);
							while($rowbranchwiseemp=mysqli_fetch_assoc($rsbranchwiseemp))
							{
								$emp_code_branchwise=$rowbranchwiseemp['emp_code'];
								modifyempdatadownloadlog($emp_code_branchwise,strtoupper($folderName));
							}
						}
					}
					$flag=1;
				}
			}
			//exit();
			//End For product group wise all product data mrp updation on the basis of Loose rate
			if($flag==1){?>
			   <script language="JavaScript" type="text/javascript">alert('Price generated successfully.');window.location.href='product_groupwise_pricelist.php?product_group_code=<?php echo $product_group_code;?>&plant_name=<?php echo $plant_name;?>';</script>
			<?php }else{
                ?><script language="JavaScript" type="text/javascript">alert('Price generation unsuccessful.');window.location.href='generate_pricing_details.php';</script>
            <?php
            }
		  }
		  else
		  {
			  ?>
				 <script language="JavaScript" type="text/javascript">alert('Price calculation for MUSTARD is under progress.');window.location.href='generate_pricing_details.php';</script>
			  <?php 
		  }
	}
}
?>