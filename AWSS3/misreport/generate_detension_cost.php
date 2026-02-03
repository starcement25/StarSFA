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
    	<td align="center">Generate Detention Cost</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_detension_cost.php" name="detention_cost"  method="post" onsubmit="return validation();">
<input type='hidden' name="mode" value="generate_detention_cost" />
<table cellpadding="4px">
	  <!--tr>
        <td align="right" width="25%"  valign="top">Select Plant:<font color="#FF0000">*</font>&nbsp;&nbsp;
		</td>
        <td align="left">
        <?php /*$plant_name=$_REQUEST['plant_name'];?>
        <table>
            <!--tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_plant" id="all_checked_plant" value="allplant" onchange="javascript:checked_all_plant();select_depot_details();"/>ALL
                </td>
             </tr-->  
        <?php
        	$sqlplant="SELECT DISTINCT plant_name FROM branch_master ORDER BY plant_name ASC";
			$rsplant=mysql_query($sqlplant);
			$cnt=0;
			while($rowplant=mysql_fetch_array($rsplant))
			{
				$cnt++;
			?>
          		<!--tr>
                    <td align="left">
                        <input type="checkbox" name="plant_name[]" value="<?php echo $rowplant['plant_name'];?>" onchange="javascript:select_depot_details();"/><?php echo $rowplant['plant_name'];?>
                    </td>
                 </tr>   
            <?php
			}*/
		?>	
    </tr>
    </table>
    </td>
    </tr--->
     <tr>
        <td align="right" width="25%"  valign="top">Select Depot:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:200px; overflow-y: scroll;">
        <?php $branch_code=$_REQUEST['branch_code'];?>
        <table>
            <tr>
                <td align="left">
                    <input type="checkbox" name="all_checked_branch" id="all_checked_branch" value="allbranch" onchange="javascript:checked_all_branch();previous_detention_cost();"/>ALL
                </td>
             </tr>
              <?php 
				   $sqlbranch="SELECT DISTINCT branch_name,branch_code FROM branch_master WHERE acedns='Y' ORDER BY branch_name ASC";
                    $rsbranch=mysql_query($sqlbranch);
                    while($rowbranch=mysql_fetch_array($rsbranch))
                    {	 $branch_code=$rowbranch['branch_code'];
                         $branch_name=$rowbranch['branch_name'];
                          $content.="<tr>";
                          $content.="<td align='left'>";
                          $content.="<input type='checkbox' name='branch_code[]' value='".$branch_code."' />".$branch_name."";
                          $content.="</td>";
                          $content.= "</tr>"; 
                    }
					echo $content;
				?>  
        	<!--tr><td><table id="showbranchdetails" ></table></td></tr-->
         </table>
         </div>
       </td>  
    </tr>
     <tr>
    	 <?php $detention_cost	=$_REQUEST['detention_cost'];?>
         <td align="right" width="25%"  valign="top">Detention Cost(MT):</td>
        <td align="left" ><input type="text" name="detention_cost" id="detention_cost" style="height:20px;" value="<?php echo $detention_cost;?>"/></td>
    </tr>
    <tr>
    	<td align="right" width="25%"  valign="top"></td>
    	<td align="center" ><input name="submit" type="submit" value=" Generate Detention Cost "/></td>
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
		var is_checked=false;
		for(i=0; i<document.detention_cost.elements.length; i++){
			if(document.detention_cost.elements[i].type=="checkbox" && document.detention_cost.elements[i].checked==true 
					&& document.detention_cost.elements[i].name=='branch_code[]'){
				is_checked=true;
				break;
			}
		}
		if(!is_checked){
			alert("Please check at least one depot");
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
	var url="returndepotdetailsdetentioncheckbox.php?plant_name="+valsplant;
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
	if($_REQUEST['mode']=='generate_detention_cost')
	{
		$detention_cost=$_POST['detention_cost'];
		$plant_name=$_POST['plant_name'];
		$plant_name="'".implode("','", $plant_name)."'";
		$branch_code=$_POST['branch_code'];
		$branch_code_array=$_POST['branch_code'];
		$branch_code="'".implode("','", $branch_code)."'";
		$vertical_value=$_SESSION['vertical_value'];
		
			$sqlselectdistinctdnsprod="SELECT DISTINCT branch_code,dns_prod_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
										AND acedns='Y' AND black_list='N' AND branch_code IN(".$branch_code.") AND 
										vertical_value='".$vertical_value."'";
			$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
			{
				$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$distinct_branch_code=$rowselectdistinctdnsprod['branch_code'];
				$sqlplant="SELECT plant_name FROM branch_master WHERE branch_code='".$distinct_branch_code."'";
				$rsplant=mysql_query($sqlplant);
				$rowplant=mysql_fetch_array($rsplant);
				$distinct_plant_name=$rowplant['plant_name'];
				$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
									dns_prod_code='".$distinct_dnsprod_code."'";
				$rsconversionfactor=mysql_query($sqlconversionfactor);
				$rowconversionfactor=mysql_fetch_array($rsconversionfactor);

				${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
				${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];

				$detention_cost_case_prodwise=$detention_cost/${conversion_factor_two.$distinct_dnsprod_code};
				$detention_cost_case_prodwise=round(($detention_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
		
				$sqlinsertdetensioncost="INSERT INTO detention_cost SET
										prod_code='".$distinct_dnsprod_code."',
										branch_code='".$distinct_branch_code."',
										detention_cost	='".$detention_cost_case_prodwise."',
										detention_cost_ton	='".$detention_cost."',
									 	vertical_value='".$_SESSION['vertical_value']."',
										ip_address='".$_SERVER['REMOTE_ADDR']."',
										user_id='".$_SESSION['admin_login']."',
										datetime=CURRENT_TIMESTAMP";
				if(mysql_query($sqlinsertdetensioncost))
				{
					$flag=1;
				}
				else
				{
					$flag=0;
				}
			    //generate_price_details($distinct_dnsprod_code,$distinct_branch_code);					
			}
		if($flag==1)
		{ 
			 foreach($branch_code_array as $branch_code_val){
				 $sqldnsbranchcode="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code_val."'";
				 $rsbranchcode=mysql_query($sqldnsbranchcode);
				 $rowbranchcode=mysql_fetch_array($rsbranchcode);
				 $dns_branch_code=$rowbranchcode['dns_branch_code'];
			 
			  $sqlinsertdetentioncostlog="INSERT INTO detention_cost_log SET
											branch_code='".$dns_branch_code."',
										   detention_cost	='".$detention_cost."',
										   vertical_value='".$_SESSION['vertical_value']."',
										   ip_address='".$_SERVER['REMOTE_ADDR']."',
										   operation_type='GENERATE',
										   user_id='".$_SESSION['admin_login']."',
										   datetime=CURRENT_TIMESTAMP";
			  mysql_query($sqlinsertdetentioncostlog);
			}
		?><script language="JavaScript" type="text/javascript">alert('Detention cost generated successfully.');window.location.href='generate_detension_cost.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Detention cost generation unsuccessful.');window.location.href='generate_detension_cost.php';</script>
		<?php
		}
	}
}
?>