<?php

ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS'){
		require("adminUtils.php");
	}
	else
	{
		//require("adminUtils_HBC_SFATS.php");
		require("adminUtils.php");
		
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");

require("include/config.php");
require("include/config-setup.php");

disphtml("main();");

function main()
{
	require("include/dbcon.php");
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
?><head>
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
<body>
<center>
<br /><br />
<table cellpadding="4px" width="50%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Select Attributes</td>
    </tr>
    <tr><td align="center">
<form action="employee_entity_wise_report.php" name="employee_entity"  method="post">
<input type='hidden' name="mode" value="generate_entity_report" />
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
            <td align="right" width="25%"  valign="top">Employee Access:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
             <td align="left"><div style="max-height:100px; overflow-y: scroll;display:none;" id="show_emp_access"> 
            <?php $emp_code=$_REQUEST['emp_code'];?>
                 <select name="emp_access" id="emp_access" onchange="javascript:select_emp_details();" >
                    <option value="">All</option>
                    <option value="Y">Active</option>
                    <option value="N">In Active</option>
                    </select>
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
                        <input type="checkbox" name="all_checked_emp" id="all_checked_emp" value="allemp" onChange="javascript:checked_all_emp();select_entity();"/>ALL
                    </td>
                 </tr> 
                 <tr><td><table id="showempdetails" ></table></td></tr>
              </table>
              </div>
         </td>
        </tr>
         <tr id="show_entity" style="display:none;">
        <td align="right" width="25%"  valign="top">Select Entity:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
         <td align="left"><div style="max-height:100px; overflow-y: scroll;"> 
        <?php $entity=$_REQUEST['entity'];?>
             <table>
                <tr>
                    <td align="left">
                        <input type="checkbox" name="all_checked_entity" id="all_checked_entity" value="allentity" onChange="javascript:checked_all_entity();"/>ALL
                    </td>
                 </tr> 
                <?php
                $sqlentity="SELECT value FROM table_view WHERE row_id='RA037'";
                $rsentity=mysqli_query($link,$sqlentity);
                $cnt=0;
                while($rowentity=mysqli_fetch_assoc($rsentity))
                {
					$entity_val=$rowentity['value'];
				}
					$entity_val_array=explode("/",$entity_val);
					array_push($entity_val_array,'Dealer');
					array_push($entity_val_array,'Sub Dealer');
					array_push($entity_val_array,'Sites');
					foreach($entity_val_array as $entity_val){
                ?>
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="entity[]" value="<?php echo $entity_val;?>"  /><?php echo $entity_val;?>
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
        <td align="right" width="25%"  valign="top">Month:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
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
  		</tr
        <tr>
            <td align="right" width="25%"  valign="top"></td>    
            <td align="left"><input name="submit" type="button" value=" Search " onClick="get_data();"></td>
        </tr>    
    </table>
    </form>
    </td>
    </tr>
</table>
<br /><br />
<div id="display" style="max-height: 500px; width:95%; overflow-y: scroll;" align="center"></div>
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
function checked_all_entity()
{
  checkboxesentity = document.getElementsByName('entity[]');
  if(document.getElementById("all_checked_entity").checked==true)
  {
	  for(var i in checkboxesentity)
	  checkboxesentity[i].checked = true;
  }
  else
  {
	 for(var i in checkboxesentity)
	 checkboxesentity[i].checked = false;
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
			 document.getElementById("show_emp_access").style.display='';
			 document.getElementById("showbranchdetails").innerHTML=val;
		 }
		 else
		 {
			 document.getElementById("showbranchdetails").innerHTML='';
			 document.getElementById("showempdetails").innerHTML='';
			 document.getElementById("show_branch").style.display='none';
			 document.getElementById("show_emp").style.display='none';
			 document.getElementById("show_emp_access").style.display='none';
			 document.getElementById("show_entity").style.display='none';
		 }
	}
 }
function select_emp_details()
{
	checkboxes = document.getElementsByName('branch_code[]');
	if(document.getElementById("emp_access"))
	{
		var emp_access=document.getElementById("emp_access").value;
	}
	else
	{
		var emp_access='';
	}
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
	var url="get_employee_entity_related_data.php?branch_code="+valsbranch+"&data_type=fetch_emp&emp_access="+emp_access;
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
			 document.getElementById("show_emp_access").style.display='none';
			 document.getElementById("show_entity").style.display='none';
		 }
	}
 }
 function select_entity()
 {
	checkboxes = document.getElementsByName('emp_code[]');
	var valsemp='';
	for(var i=0, n=checkboxes.length;i<n;i++) {
	  if (checkboxes[i].checked==true) 
	  {
	  	valsemp += ","+checkboxes[i].value;
	  }
	}
	if(valsemp!='')
	{
		document.getElementById("show_entity").style.display='';
	}
	else
	{
		 document.getElementById("show_entity").style.display='none';
	}
 }
 function get_data(){
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
	var is_checked_branch=false;
	for(i=0; i<document.employee_entity.elements.length; i++){
		if(document.employee_entity.elements[i].type=="checkbox" && document.employee_entity.elements[i].checked==true 
				&& document.employee_entity.elements[i].name=='branch_code[]'){
			is_checked_branch=true;
			break;
		}
	}
	if(!is_checked_branch){
		alert("Please check at least one branch");
		return false;
	}
	var is_checked_emp=false;
	for(i=0; i<document.employee_entity.elements.length; i++){
		if(document.employee_entity.elements[i].type=="checkbox" && document.employee_entity.elements[i].checked==true 
				&& document.employee_entity.elements[i].name=='emp_code[]'){
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
	var is_checked_entity=false;
	for(i=0; i<document.employee_entity.elements.length; i++){
		if(document.employee_entity.elements[i].type=="checkbox" && document.employee_entity.elements[i].checked==true 
				&& document.employee_entity.elements[i].name=='entity[]'){
			is_checked_entity=true;
			break;
		}
	}
	if(!is_checked_entity){
		alert("Please check at least one entity");
		return false;
	}
	else
	{
		checkboxesentity = document.getElementsByName('entity[]');
		var valsentity='';
		for(var i=0, n=checkboxesentity.length;i<n;i++) {
		  if (checkboxesentity[i].checked==true) 
		  {
			valsentity += ","+checkboxesentity[i].value;
		  }
		}
		valsentity=valsentity.substr(1);
	}


	var month = document.getElementById("month_select").value;
	if(document.getElementById("month_select").value.search(/\S/) == -1){
		alert('Provide month');
		return false;
	}
	/*if(document.getElementById('month_select').value==''){
		alert("Please select month");
		return false;
	}
	else
	{
		var month = document.getElementById("month_select").value;
	}*/
	//var nickname="<?php /*echo strtoupper($_SESSION['nick_name'])*/?>";
	//alert(nickname);
	//alert(emp_code+" "+month);
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
	GenericAjaxFunction('employee_entity_wise_report_data.php?emp_code='+valsemp+'&entity='+valsentity,'display',0);
}
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Entity wise Report', 'height=400,width=600');
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
	a.download = 'Entity Wise Visit Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}

</script>
<?php
}
?>