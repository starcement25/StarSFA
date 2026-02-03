<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
if(!$_GET)
{
	disphtml("main();");
}

function main()
{
		if(strtoupper($_SESSION['admin_login'])=="ADMIN" || strtoupper($_SESSION['admin_login'])=="SUPERVISOR" || strtoupper($_SESSION['admin_login'])=="SYSTEM" ){
			$condition='';
		}
		else if(strtoupper($_SESSION['admin_login'])=="PRICEHBC" || strtoupper($_SESSION['admin_login'])=="PRICESFATS" || strtoupper($_SESSION['admin_login'])=="HBC" || strtoupper($_SESSION['admin_login'])=="SFATS" || strtoupper($_SESSION['admin_login'])=="ED01")
		{
			$condition='WHERE PGM.vertical_value ="'.$_SESSION['vertical_value'].'"';
		}
		else
		{
			$condition='WHERE PGM.vertical_value ="'.$_SESSION['vertical_value'].'"';
		}
?>
	<script type="text/javascript" src="ajax1.js"></script>
	<?php
	?>
    <head>
     <style>
	.datatable{
	  width:100%;
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
	  text-align: left;
	  vertical-align:middle;
	  font-weight: 300;
	  font-size: 12px;
	  color: #000000;
	  overflow-wrap: break-word;
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
	<body>
	<center>
	<br /><br />
  <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
	<table border="1" width="40%" class="border" style="border-collapse:collapse;" cellpadding="5px;">
    	<tr class="TDHEAD_SUB">
    	<td align="center" colspan="2">Costing Report Destination Wise MCX</td>
       </tr>
	   <tr class="TDHEAD_SUB" >
            <td align="right" width="40%">Choose Date:</td>
            <td width="">
                    <?php $from_date=$_REQUEST['from_date'];?>
                  <!--input type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date" id="from_date"></input>&nbsp;
                    <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" 
                    width="20" height="18" ></a>
                </label>
                <script language="JavaScript" type="text/javascript">
                    <!-- // create calendar object(s) just after form tag closed
                     // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                     // note: you can have as many calendar objects as you need for your application
                    var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                    cal5.year_scroll = true;
                    cal5.time_comp = false;
                    //-->
                <!--/script-->&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!--input name="submit" type="button" value="Submit" onclick="return_result(from_date.value);" /-->
                 <select name="from_date" id="from_date" >
                <!--option value="" selected>Select</option-->
                <?php 
                    $sqlratecreatedate="SELECT DISTINCT DATE_FORMAT(SUBSTRING(create_date,1,10),'%d-%m-%Y') AS create_date  FROM 
                                        mcx_rate ORDER BY DATE_FORMAT(SUBSTRING(create_date,1,10),'%d-%m-%Y') DESC";
                    $rsratecreatedate=mysql_query($sqlratecreatedate);
                    while($rowratecreatedate=mysql_fetch_array($rsratecreatedate))
                    {
                    ?>
                    <option value="<?php echo $rowratecreatedate['create_date'];?>" ><?php echo $rowratecreatedate['create_date'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
       </tr> 
       <tr class="TDHEAD_SUB">
		 <td align="right" width="40%">Choose Oilgroup:</td>
         <td width="">
			<select name="product_group" id="product_group">
				<option selected value="">Choose Oilgroup</option>
				<?php
					$sql_select_product_group = "SELECT PGM.product_group_code,PGM.product_group_name FROM product_group_master PGM $condition
												ORDER BY PGM.product_group_name ASC";
					$res_select_product_group = mysql_query($sql_select_product_group);
					while($row_select_product_group = mysql_fetch_array($res_select_product_group))
					{
						$option_value_string.="<option value=\"'".$row_select_product_group['product_group_code']."'\">$row_select_product_group[product_group_name]</option>";
						$all .= "'".$row_select_product_group['product_group_code']."',";
					}
					$all = rtrim($all,",");
					 echo "<option value=\"".$all."\">All</option>";
					 echo $option_value_string;
				?>
			</select>
		</td>
	  </tr>
       <tr class="TDHEAD_SUB">
		<td align="right" width="40%">Choose State:</td>
        <td width="">
      <?php
	  	$onclick = "state_route(this.value);load_capacity();";
		$sql_state = "SELECT DISTINCT SM.dns_state_code,SM.state FROM state_master SM,customer_master CM WHERE SM.state!='' 
						AND CM.state_code=SM.state AND CM.acedns='Y' AND CM.cust_type='D' ORDER BY SM.state ASC";
		$state_select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
		$state_select_control .= "<option value=\"\">Choose State</option>";
		$res_state = mysql_query($sql_state);
		while($row_state = mysql_fetch_array($res_state)){
			$state = $row_state['state'];
			$dns_state_code = $row_state['dns_state_code'];
			$state_string .= "'".$dns_state_code."',";
			$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
		}
		$state_string = rtrim($state_string,",");
		//$state_select_control .= "<option value=\"".$state_string."\">All</option>";
		$state_select_control .= $state_select_control_option;
		$state_select_control .= "</select>";
		echo $table_data .= $state_select_control;
	  ?>
      </td>
	  </tr>
       <tr class="TDHEAD_SUB">
		<td align="right" width="40%">Choose Route:</td><td width=""><div id="route_select_div"></div></td>
	  </tr>
      <tr class="TDHEAD_SUB">
		<td align="center" colspan="2">Transport Mode:
        <span id="showtransportmode"></span>
         <?php 
			   /*$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
				$content.='<option value="">Choose Trasport Mode </option>';
				//$sqltransportmode="SELECT DISTINCT transport_mode FROM plantwise_load_capacity ORDER BY transport_mode ASC";
				$sqltransportmode="SELECT DISTINCT transport_mode FROM customer_master WHERE transport_mode!='' ORDER BY transport_mode ASC";
				$rstransportmode=mysql_query($sqltransportmode);
				while($rowtransportmode=mysql_fetch_array($rstransportmode))
				{		
					$content.="<option value='".$rowtransportmode['transport_mode']."'>".strtoupper($rowtransportmode['transport_mode'])."</option>";
				}
				echo $content.='</select>';*/
		?>&nbsp;&nbsp;&nbsp;&nbsp;Capacity: <span id="showloadcapacity"></span>
        <br /> <br />
			<input name="submit" type="button" value="Submit" onClick="return_result(product_group.value,from_date.value,state.value,route.value,transport_mode.value,truck_load_distribution.value);" />
        </td>
      </tr>  
      </table>
   </form>
	<br />
	<div id="display" >
	<img src="ajax-loader.gif" id="ajaxloader" hidden>
	</div>
    <br />
    <div style="width:60%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv(product_group.value,from_date.value,state.value,route.value,transport_mode.value,truck_load_distribution.value);" >
</div>
	</center>
	</body>
	<script>
	function state_route(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		
		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=stateroute','route_select_div',0);
	}
	function show_transportmode(route)
	{
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = document.getElementById("state").value;
		
		document.getElementById("showtransportmode").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&route='+route+'&type=stateroutetransport','showtransportmode',0);
	}
	function return_result(product_group_code,from_date,state,route,transport_mode,capacity)
	{
		//alert(branch_code+product_group_code);
		if(document.getElementById("from_date").value.search(/\S/) == -1)
		{
			alert("Please choose date");
		}
		else if(document.getElementById("product_group").value.search(/\S/) == -1)
		{
			alert("Please choose Oilgroup");
		}
		else if(document.getElementById("state").value.search(/\S/) == -1)
		{
			alert("Please choose state");
		}
		else if(document.getElementById("route").value.search(/\S/) == -1)
		{
			alert("Please choose route");
		}
		else if(document.getElementById("transport_mode").value.search(/\S/) == -1)
		{
			alert("Please choose transport mode");
		}
		else if(document.getElementById("truck_load_distribution").value.search(/\S/) == -1)
		{
			alert("Please choose capacity");
		}
		else
		{
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			
			GenericAjaxFunction('sale-rate-data-download-modified-FOR-mcx.php?from_date='+from_date+'&product_group_code='+product_group_code+'&state='+state+'&route='+route+'&transport_mode='+transport_mode+'&capacity='+capacity,'display',0);
			//document.getElementById('ndenotes').style.display='';
		}
	}
	
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Costing Destination Wise', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Costing Destination Wise</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv(product_group_code,from_date,state,route,transport_mode,capacity)
{
	//alert(divid);
        //getting values of current time for generating the file name
       /* var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		
		/*document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');*/
        //creating a temporary HTML link element (they support setting file names)*/
        /*var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('display');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Sale Rate' + postfix + '.xls';
        //triggering the function
		document.body.appendChild(a);
        a.click();
		document.body.appendChild(a);
        //just in case, prevent default behaviour
        e.preventDefault();*/
		//alert(from_date);
		//alert(product_group_code);
		//alert(capacity);
		window.open('sale-rate-data-download-modified-export-mcx.php?from_date='+from_date+'&product_group_code='+product_group_code+'&state='+state+'&route='+route+'&transport_mode='+transport_mode+'&capacity='+capacity,'mywindow')	;
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

function load_capacity()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	//var plant_name = document.getElementById("plant_name").value;
	var transport_mode = document.getElementById("transport_mode").value;
	var state=document.getElementById("state").value;
	var route=document.getElementById("route").value;
	var url="returntransportmodedetails.php?transport_mode="+transport_mode+"&state="+state+"&route="+route+"&type=loadcapacitystateroute";
	xmlHttp.onreadystatechange=loadcapacitydetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function loadcapacitydetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			 document.getElementById("showloadcapacity").innerHTML=val;
		 }
	}
 }
	</script>
	<?php
}
?>
