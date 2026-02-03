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
    	<td align="center">Plant Wise Load Capacity</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<form action="generate_plantwise_transport_mode_capacity.php" name="plant_wise_load_capacity"  method="post" onsubmit="return validation();">
<input type='hidden' name="mode" value="generate_plant_wise_load_capacity" />
<table cellpadding="4px">
	  <tr>
        <td align="right" width="25%"  valign="top">Select Plant:<font color="#FF0000">*</font>&nbsp;&nbsp;
		</td>
        <td align="left">
        <?php $plant_name=$_REQUEST['plant_name'];?>
        <table>
            <tr>
                <td align="left">
                     <input type="checkbox" name="all_checked_plant" id="all_checked_plant" value="allplant" onchange="javascript:checked_all_plant();"/>ALL
                </td>
             </tr>  
        <?php
        	$sqlplant="SELECT DISTINCT plant_name FROM branch_master WHERE acedns='Y' ORDER BY plant_name ASC";
			$rsplant=mysqli_query($link,$sqlplant);
			$cnt=0;
			while($rowplant=mysqli_fetch_assoc($rsplant))
			{
				$cnt++;
			?>
          		<tr>
                    <td align="left">
                        <input type="checkbox" name="plant_name[]" value="<?php echo $rowplant['plant_name'];?>"/><?php echo $rowplant['plant_name'];?>
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
        <td align="right" width="25%"  valign="top">Tranport Mode:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:200px; overflow-y: scroll;">
        <?php $transport_mode=$_REQUEST['transport_mode'];
			$transport_mode_input=$_REQUEST['transport_mode_input'];
		?>
        <table>
            <tr id="transportmodeselect" style="display:block">
                <td align="left">
                    <select name="transport_mode" id="transport_mode" >
                    <option value="">SELECT</option>
                    <?php
                    $sqltransportmode="SELECT DISTINCT transport_mode FROM transport_mode ORDER BY transport_mode ASC";
                    $rstransportmode=mysqli_query($link,$sqltransportmode);
                    while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
                    {		
                       echo "<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
                    }
					?>
                  </select>&nbsp;&nbsp;<input name="add_transport_mode" type="button" value="Add"  onclick="show_transport_mode_input();" />
                </td>
             </tr>
               <tr id="transportmodeinput" style="display:none">
                    <td align="left"> 
                    <input type="text" name="transport_mode_input" id="transport_mode_input" style="height:20px;" value="<?php echo $transport_mode_input;?>"/>
                      </td>
             	</tr>              
         </table>
         </div>
       </td>  
    </tr>
     <tr>
    	 <?php $load_capacity	=$_REQUEST['load_capacity'];?>
         <td align="right" width="25%"  valign="top">Load Capacity(MT):</td>
        <td align="left" ><input type="hidden" name="hidden_val" id="hidden_val" value=""><input type="text" name="load_capacity" id="load_capacity" style="height:20px;" value="<?php echo $load_capacity;?>"/></td>
    </tr>
    <tr>
    	<td align="right" width="25%"  valign="top"></td>
    	<td align="center" ><input name="submit" type="submit" value=" Generate Load Capacity "/></td>
    </tr>
</table>
</form>
</td></tr></table>
</center>
<script>
function show_transport_mode_input()
{
	document.getElementById('transportmodeselect').style.display='none';
	document.getElementById('transportmodeinput').style.display='';
	document.getElementById('hidden_val').value="INPUT";
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
function validation()
{
		var is_checked=false;
		for(i=0; i<document.plant_wise_load_capacity.elements.length; i++){
			if(document.plant_wise_load_capacity.elements[i].type=="checkbox" && document.plant_wise_load_capacity.elements[i].checked==true 
					&& document.plant_wise_load_capacity.elements[i].name=='plant_name[]'){
				is_checked=true;
				break;
			}
		}
		if(!is_checked){
			alert("Please check at least one plant");
			return false;
		}
		if(document.getElementById('hidden_val').value!='INPUT')
		{
			if(document.getElementById("transport_mode").value.search(/\S/) == -1)
			{
				alert('Select Transport Mode');
				return false;
			}
		}
		if(document.getElementById('hidden_val').value=='INPUT')
		{
			if(document.getElementById("transport_mode_input").value.search(/\S/) == -1)
			{
				alert('Please Input Transport Mode');
				return false;
			}
		}
		if(document.getElementById("load_capacity").value.search(/\S/) == -1)
			{
				alert('Please Input Load Capacity');
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
</script>
<?php
	if($_REQUEST['mode']=='generate_plant_wise_load_capacity')
	{
		$load_capacity=$_POST['load_capacity'];
		$transport_mode=$_POST['transport_mode'];
		$transport_mode_input=$_POST['transport_mode_input'];
		$plant_name=$_POST['plant_name'];
		if($transport_mode_input !='')
		{
			$sqlseltrasportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode_input."'";
			$rsselecttrasportmode=mysqli_query($link,$sqlseltrasportmode);
			$cntselecttrasportmode=mysqli_num_rows($rsselecttrasportmode);
			if($cntselecttrasportmode==0)
			{
				$sqlinserttransportmode="INSERT INTO transport_mode SET transport_mode='".$transport_mode_input."'";
				if(mysqli_query($link,$sqlinserttransportmode))
				{
					$transport_mode=$transport_mode_input;
				}
			}
			else  $transport_mode=$transport_mode_input;
		}
		//$plant_name="'".implode("','", $plant_name)."'";
			foreach($plant_name as $plant_name_val)
			{
				$sqlselectloadcapacity="SELECT load_capacity FROM plantwise_load_capacity WHERE 
										plant_name='".$plant_name_val."' AND transport_mode='".$transport_mode."' AND 
										load_capacity='".$load_capacity."'";
				$rsselectloadcapacity=mysqli_query($link,$sqlselectloadcapacity);
				$cntelectloadcapacity=mysqli_num_rows($rsselectloadcapacity);
				if($cntelectloadcapacity ==0){
					$sqlinsertloadcapacity="INSERT INTO plantwise_load_capacity SET 
											plant_name='".$plant_name_val."',
											transport_mode='".$transport_mode."',
											load_capacity='".$load_capacity."',
											ip_address='".$_SERVER['REMOTE_ADDR']."',
											 user_id='".$_SESSION['admin_login']."',
											 datetime=CURRENT_TIMESTAMP";
					mysqli_query($link,$sqlinsertloadcapacity);						
				}
			}
			$flag=1;
		if($flag==1)
		{ ?><script language="JavaScript" type="text/javascript">alert('Plant wise load capacity generated successfully.');window.location.href='generate_plantwise_transport_mode_capacity.php';</script>
		<?php }else{
			?><script language="JavaScript" type="text/javascript">alert('Plant wise load capacity generation unsuccessful.');window.location.href='generate_plantwise_transport_mode_capacity.php';</script>
		<?php
		}
	}
}
?>