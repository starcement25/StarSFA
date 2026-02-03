<?php
mysqli_connect("localhost","acedns_dnsprod","dnsprod1234");
mysqli_select_db("acedns_EMAMI");
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
 <script type="text/javascript">
   // Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {'packages':['corechart']});
	</script>
	<script type="text/javascript"
		  src="https://www.google.com/jsapi?autoload={
			'modules':[{
			  'name':'visualization',
			  'version':'1',
			  'packages':['corechart']
			}]
		  }">
	</script>
<body onload="showdata();">
<div id="display" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center"></div>
</body>
<script>
function showdata(){
	GenericAjaxFunction('dashboard_emami.php','display',0);
}
</script>