<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	if(strtoupper($_SESSION['admin_login'])=="ADMIN")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1";
		$customer_condition=" 1 ";
		
	}
	else
	{	
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" emp_code IN (".$emp_hierarchy.") AND acedns!='N' ";
		$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
	}
	
	/*------------------------------> Select of month (financial year)<-------------------------------*/
$current_month_year = date('M')."-".date('Y');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$current_year = date('Y');
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
}
else{
	$previous_year = date('Y');
	
	$current_year = date('Y', strtotime('+1 year'));
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
	
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='PARLE')
	{
			$months = array ('Jan-'.$previous_year.'','Feb-'.$previous_year.'','Mar-'.$previous_year.'','Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');

	}
}
//echo $sql_state = "SELECT DISTINCT state FROM site_master WHERE ".$emp_hierarchy_condition." ORDER BY state ASC";
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<script>
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}

function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Monthly Activity Report', 'height=400,width=600');
	mywindow.document.write('<html><head>');
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
	//var table_html = encodeURIComponent(table_div.outerHTML.replace(/ /g, '%20'));
	//alert(table_html);return false;
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Site Visit Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
</script>
<body>
<center>
<br>
<!--table width="30%" class="border" style="border-collapse:collapse;" border="1" cellpadding="4">
  <tr  class="TDHEAD">
  	<td colspan="2" align="center">Site Visit Report</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">State:</td>
    <td align="left">
    	<select name="state" id="state" >
			<option value="">Select</option>
            <!--option value="all">All</option-->
            <?php
			/*echo $sql_state = "SELECT DISTINCT state FROM site_master WHERE ".$emp_hierarchy_condition." ORDER BY state ASC";
			$res_state = mysqli_query($link,$sql_state); 
			while($row_state = mysqli_fetch_assoc($res_state)){
				echo "<option value=\"".$row_state['state']."\">".$row_state['state']."</option>";
			}*/
			?>
		 <!--/select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Month:</td>
    <td align="left">
    	<select name="month_select" id="month_select">
        <option value="">Select</option>
    	<?php
		/*foreach($months as $monthvalue){
			echo "<option>".$monthvalue."</option>";
			if($monthvalue == $current_month_year)
				break;
		}*/
		?>
        </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td>
  </tr>
</table-->
<table cellpadding="4px" width="50%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Site Visit Report</td>
    </tr>
    <tr><td align="center">
<form action="site_visit_report.php" name="site_visit"  method="post">
<input type='hidden' name="mode" value="site_visit" />
<table cellpadding="4px">
    <tr>
        <td align="right" width="25%"  valign="top">Select Zone:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
        <td align="left"><div style="max-height:100px; overflow-y: scroll;">
        <?php $zone=$_REQUEST['zone'];?>
        <table>
            <tr>
                <!--td align="left">
                     <input type="checkbox" name="all_checked_zone" id="all_checked_zone" value="allzone" onChange="javascript:checked_all_zone();select_branch_details();"/>ALL
                </td-->
             </tr>  
				<?php
                $sqlzone="SELECT DISTINCT zone FROM employee_master WHERE zone!='' AND zone IS NOT NULL ORDER BY zone ASC";
                $rszone=mysqli_query($link,$sqlzone);
                $cnt=0;
                while($rowzone=mysqli_fetch_assoc($rszone))
                {
                    $cnt++;
                ?>
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="zone[]" value="<?php echo $rowzone['zone'];?>"  onchange="javascript:select_branch_details();"/><?php echo $rowzone['zone'];?>
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
        <td align="right" width="25%"  valign="top">Select Branch:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
         <td align="left"><div style="max-height:100px; overflow-y: scroll;display:none;" id="show_branch" > 
        <?php $branch_code=$_REQUEST['branch_code'];?>
             <table>
                <tr>
                    <!--td align="left">
                        <input type="checkbox" name="all_checked_branch" id="all_checked_branch" value="allbranch" onChange="javascript:checked_all_branch();select_emp_details();"/>ALL
                    </td-->
                 </tr> 
                 <tr><td><table id="showbranchdetails" ></table></td></tr>
              </table>
              </div>
         </td>
        </tr>
       <tr>
        <td align="right" width="25%"  valign="top">Select Employee:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
         <td align="left"><div style="max-height:100px; overflow-y: scroll;display:none;" id="show_emp"> 
        <?php $emp_code=$_REQUEST['emp_code'];?>
             <table>
                <tr>
                    <td align="left">
                        <input type="checkbox" name="all_checked_emp" id="all_checked_emp" value="allemp" onChange="javascript:checked_all_emp();"/>ALL
                    </td>
                 </tr> 
                 <tr><td><table id="showempdetails" ></table></td></tr>
              </table>
              </div>
         </td>
        </tr>
        <tr >
  		<td align="right" width="25%">Month:</td>
    	<td align="left">
    	<select name="month_select" id="month_select">
        <option value="">Select</option>
    	<?php
		foreach($months as $monthvalue){
			echo "<option>".$monthvalue."</option>";
			if($monthvalue == $current_month_year)
				break;
		}
		?>
        </select>
    </td>
  </tr>
  <tr >
  	<td></td>
    <td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td>
  </tr>
    </table>
    </form>
    </td>
    </tr>
</table>

<br />
<div id="display" style="max-height: 440px; width:1600px; overflow-y: scroll; margin-left:10px;" align="center">
</div>

</center>
</body>
<script>
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
function checked_all_zone()
{
  checkboxeszone = document.getElementsByName('zone[]');
  if(document.getElementById("all_checked_zone").checked==true)
  {
	  for(var i in checkboxeszone)
	  checkboxeszone[i].checked = true;
  }
  else
  {
	 for(var i in checkboxeszone)
	 checkboxeszone[i].checked = false;
  }
}
function checked_all_emp()
{
  checkboxesemp = document.getElementsByName('emp_code[]');
  if(document.getElementById("all_checked_emp").checked==true)
  {
	  for(var i in checkboxesemp)
	  checkboxesemp[i].checked = true;
  }
  else
  {
	 for(var i in checkboxesemp)
	 checkboxesemp[i].checked = false;
  }
}
function validation()
{
	var is_checked=false;
	for(i=0; i<document.employee_entity.elements.length; i++){
		if(document.employee_entity.elements[i].type=="checkbox" && document.employee_entity.elements[i].checked==true 
				&& document.employee_entity.elements[i].name=='zone[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one zone");
		return false;
	}
	
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
function select_branch_details()
{
	checkboxes = document.getElementsByName('zone[]');
	var valszone='';
	for(var i=0, n=checkboxes.length;i<n;i++) {
	  if (checkboxes[i].checked==true) 
	  {
	  	valszone += ","+checkboxes[i].value;
	  }
	}
	if(valszone!='')
	{
	valszone=valszone.substr(1);
	}
	/*if(valszone=='')
	{
		alert('Please select at least one zone');
	}*/
	//alert(valszone);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="get_employee_entity_related_data.php?zone="+valszone+"&data_type=fetch_branch";
	xmlHttp.onreadystatechange=branchdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function branchdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			 document.getElementById("show_branch").style.display='';
			 document.getElementById("showbranchdetails").innerHTML=val;
		 }
		 else
		 {
			 document.getElementById("showbranchdetails").innerHTML='';
			 document.getElementById("showempdetails").innerHTML='';
			 document.getElementById("show_branch").style.display='none';
			  document.getElementById("show_emp").style.display='none';
			 document.getElementById("show_entity").style.display='none';
		 }
	}
 }
function select_emp_details()
{
	checkboxes = document.getElementsByName('branch_code[]');
	var valsbranch='';
	for(var i=0, n=checkboxes.length;i<n;i++) {
	  if (checkboxes[i].checked==true) 
	  {
	  	valsbranch += ","+checkboxes[i].value;
	  }
	}
	if(valsbranch!='')
	{
		valsbranch=valsbranch.substr(1);
	}
	/*if(valszone=='')
	{
		alert('Please select at least one zone');
	}*/
	//alert(valszone);
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var url="get_employee_entity_related_data.php?branch_code="+valsbranch+"&data_type=fetch_emp";
	xmlHttp.onreadystatechange=empdetails;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function empdetails()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		//alert(val);
		if(val!="")
		 {
			  document.getElementById("show_emp").style.display='';
			 document.getElementById("showempdetails").innerHTML=val;
		 }
		 else
		 {
			 document.getElementById("showempdetails").innerHTML='';
			 document.getElementById("show_emp").style.display='none';
			 document.getElementById("show_entity").style.display='none';
		 }
	}
 }

function get_data(){
	/*var state = document.getElementById("state").value;
	if(document.getElementById("state").value.search(/\S/) == -1){
		alert('Provide state');
		return false;
	}*/
			var is_checked=false;
	for(i=0; i<document.site_visit.elements.length; i++){
		if(document.site_visit.elements[i].type=="checkbox" && document.site_visit.elements[i].checked==true 
				&& document.site_visit.elements[i].name=='zone[]'){
			is_checked=true;
			break;
		}
	}
	if(!is_checked){
		alert("Please check at least one zone");
		return false;
	}
	var is_checked_branch=false;
	for(i=0; i<document.site_visit.elements.length; i++){
		if(document.site_visit.elements[i].type=="checkbox" && document.site_visit.elements[i].checked==true 
				&& document.site_visit.elements[i].name=='branch_code[]'){
			is_checked_branch=true;
			break;
		}
	}
	if(!is_checked_branch){
		alert("Please check at least one branch");
		return false;
	}
	var is_checked_emp=false;
	for(i=0; i<document.site_visit.elements.length; i++){
		if(document.site_visit.elements[i].type=="checkbox" && document.site_visit.elements[i].checked==true 
				&& document.site_visit.elements[i].name=='emp_code[]'){
			is_checked_emp=true;
			break;
		}
	}
	if(!is_checked_emp){
		alert("Please check at least one employee");
		return false;
	}
	else
	{
		checkboxesemp = document.getElementsByName('emp_code[]');
		var valsemp='';
		for(var i=0, n=checkboxesemp.length;i<n;i++) {
		  if (checkboxesemp[i].checked==true) 
		  {
			valsemp += ","+checkboxesemp[i].value;
		  }
		}
		valsemp=valsemp.substr(1);
	}

	var month = document.getElementById("month_select").value;
	if(document.getElementById("month_select").value.search(/\S/) == -1){
		alert('Provide month');
		return false;
	}
	//var nickname="<?php /*echo strtoupper($_SESSION['nick_name'])*/?>";
	//alert(nickname);
	//alert(emp_code+" "+month);
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
	//GenericAjaxFunction('site_visit_report_data.php?state='+state+'&month='+month,'display',0);
	GenericAjaxFunction('site_visit_report_data.php?emp_code='+valsemp+'&month='+month,'display',0);
}
</script>
<?php } ?>