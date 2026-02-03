<?php
require("adminUtils.php");
echo "<b>Zip file extracted Successfully. Backend process is going on. It will take few minutes. You will receive the confirmation mail.</b>";
?>
<br />
<a href="javascript:void(0);" style="color: #e40000" onclick="javascript:window.location='adminCsvReadIncremental.php'" title="Back to main">
<img src="images/back.png" alt="back" /></a>
<?php
$url="http://salesmpower.acedns.in/misreport/uploadskuRUPACURL.php?nick_name=RUPA";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_exec($ch);
?>