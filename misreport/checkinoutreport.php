<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
?>
<div id="display">
<table border="1" id="display_table">
  <tr>
    <td>Emp Code</td>
    <td>Check In</td>
    <td>Check Out</td>
  </tr>
<?php
$sql_emp = "SELECT emp_code, date FROM location WHERE date LIKE '%2017-03-03%' AND trans_id LIKE 'A%'";
$res_emp = mysqli_query($link,$sql_emp);
while($row_emp = mysqli_fetch_assoc($res_emp)){
	$emp_code = $row_emp['emp_code'];
	$date = $row_emp['date'];
	
	$sql_chk = "SELECT date FROM location WHERE date LIKE '%2017-03-03%' AND trans_id LIKE 'CH%' AND emp_code = '".$emp_code."'";
	$res_chk = mysqli_query($link,$sql_chk);
	$row_chk = mysqli_fetch_assoc($res_chk);
	$chk_time = $row_chk['date'];
	
	echo "<tr>
			<td>".$emp_code."</td>
			<td>".$date."</td>
			<td>".$chk_time."</td>
		  </tr>";
}
?>
</table>
</div>
<br /><br />
<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
<script>
function exporttocsv(){
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
		var textRange; var j=0;
		tab = document.getElementById('display_table'); // id of table
		
		for(j = 0 ; j < tab.rows.length ; j++) 
		{     
			tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
		}
		
		tab_text=tab_text+"</table>";
		tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
		tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
		tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
			
		var a = document.createElement('a');
		
		a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
		a.download = 'Customer Visit Report' + postfix + '.xls';
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
}
</script>