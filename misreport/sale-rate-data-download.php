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
	  width:98%;
	  table-layout: fixed;
	  }
	.tbl-header{
	  background-color: rgba(255,255,255,0.3);
	 }
	.tbl-content{
	  height:400px;
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
	  padding: 15px;
	  text-align: left;
	  vertical-align:middle;
	  font-weight: 300;
	  font-size: 12px;
	  color: #000000;
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
	   <tr class="TDHEAD_SUB" >
            <td align="center">Choose Date:
                    <?php $from_date=$_REQUEST['from_date'];?>
                  <input type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date" id="from_date"></input>&nbsp;
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
                </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!--input name="submit" type="button" value="Submit" onclick="return_result(from_date.value);" /-->
            </td>
       </tr> 
       <tr class="TDHEAD_SUB">
		<td align="center">
			<select name="product_group" id="product_group">
				<option selected value="">Choose Oilgroup</option>
				<?php
					$sql_select_product_group = "SELECT PGM.product_group_code,PGM.product_group_name FROM product_group_master PGM $condition
												ORDER BY PGM.product_group_name ASC";
					$res_select_product_group = mysqli_query($link,$sql_select_product_group);
					while($row_select_product_group = mysqli_fetch_assoc($res_select_product_group))
					{
						echo "<option value=\"'".$row_select_product_group['product_group_code']."'\">$row_select_product_group[product_group_name]</option>";
						$all .= "'".$row_select_product_group['product_group_code']."',";
					}
					$all = rtrim($all,",");
					 echo "<option value=\"".$all."\">All</option>";
				?>
			</select>
		&nbsp;&nbsp;
			<input name="submit" type="button" value="Submit" onclick="return_result(product_group.value,from_date.value);" />
		</td>
	  </tr>
   </table>
   </form>
	<br />
	<div id="display" style="max-height: 400px; max-width: 1800px;  overflow-y: scroll; overflow-x: scroll; " align="center">
	<img src="ajax-loader.gif" id="ajaxloader" hidden>
	</div>
    <br />
    <div style="width:60%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	</center>
    </body>
	
	<script>
	function return_result(product_group_code,from_date)
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
		else
		{
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			
			GenericAjaxFunction('sale-rate-data-download-modified.php?from_date='+from_date+'&product_group_code='+product_group_code,'display',0);
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
	var mywindow = window.open('', 'Depotwise Pricelist', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Depotwise Pricelist</title>');
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

function exporttocsv(divid)
{
	//alert(divid);
        //getting values of current time for generating the file name
        var dt = new Date();
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
        var a = document.createElement('a');
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
        e.preventDefault();
}
	</script>
	<?php
}
?>
