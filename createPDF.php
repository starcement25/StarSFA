<?php
    include_once('phpToPDF.php') ;
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");	


    // Generate a PDF Invoice from PHP
    /*
       Store the contents of your report into a variable ($html) and the 
       phptopdf_html() function will turn the HTML into a PDF file. The PDF file 
       will be named 'my_pdf_filename.pdf' and stored in a 'pdf' folder.
    */

    $html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>HTML Invoice Template</title>
<style type="text/css">
<!--
body {
  font-family:Tahoma;
}

img {
  border:0;
}

#page {
  width:800px;
  margin:0 auto;
  padding:15px;

}

#logo {
  float:left;
  margin:0;
}

#address {
  height:181px;
  margin-left:250px; 
}

table {
  width:100%;
}

td {
padding:5px;
}

tr.odd {
  background:#e1ffe1;
}
-->
</style>
</head>
<body>
<div id="page">
  <div id="logo">
    <a href="http://www.danifer.com/"><img src="http://www.danifer.com/images/invoice_logo.jpg"></a>
  </div><!--end logo-->
  
  <div id="address">

    <p>Your company name<br />
    <a href="mailto:youremail@somewhere.com">youremail@somewhere.com</a>
    <br /><br />
    Transaction # xxx<br />
    Created on 2008-10-09<br />
    </p>

  </div><!--end address-->

  <div id="content">
    <p>
      <strong>Customer Details</strong><br />
      Name: Last, First<br />
      Email: customeremail@somewhere.com<br />
      Payment Type: MasterCard    </p>

    <hr>
    <table>
      <tr><td><strong>Description</strong></td><td><strong>Qty</strong></td><td><strong>Unit Price</strong></td><td><strong>Amount</strong></td></tr>
      <tr class="odd"><td>Product 1</td><td>1</td><td>4.95</td><td>4.95</td></tr><tr class="even"><td>Product 2</td><td>1</td><td>4.95</td><td>4.95</td></tr><tr class="odd"><td>Product 3</td><td>1</td><td>4.95</td><td>4.95</td></tr>              <tr><td>&nbsp;</td><td>&nbsp;</td><td><strong>Total</strong></td><td><strong>14.85</strong></td></tr>

    </table>
    <hr>
    <p>
      Thank you for your order!  This transaction will appear on your billing statement as "Your Company".<br />
      If you have any questions, please feel free to contact us at <a href="mailto:youremail@somewhere.com">youremail@somewhere.com</a>.
    </p>

    <hr>

    <p>
      <center><small>This communication is for the exclusive use of the addressee and may contain proprietary, confidential or privileged information. If you are not the intended recipient any use, copying, disclosure, dissemination or distribution is strictly prohibited.
      <br /><br />
      &copy; Your Company All Rights Reserved
      </small></center>
    </p>
  </div><!--end content-->
</div><!--end page-->
</body>

</html>';



phptopdf_html($html,'pdf/', 'my_pdf_filename.pdf');

$strSid = md5(uniqid(time()));
$email='dipankarc@coral.in';
$subj='Attched file testing';
$emailbody="PDF file is attached below.";
$headers='';
//$headers .= "Content-type: text/html; charset=UTF-8\n";
$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
			"Reply-To:".FROMEMAIL." \r\n" .
			'X-Mailer: PHP/' . phpversion();
$headers .= "MIME-Version: 1.0\r\n";			
$headers .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n\n";
$headers .= "This is a multi-part message in MIME format.\n";
$headers .= "--".$strSid."\n";
$headers .= "Content-type: text/html; charset=UTF-8\n"; // or UTF-8 //
$headers .= "Content-Transfer-Encoding: 7bit\n\n";
$headers .= $emailbody."\n\n";
$strContent1 = base64_encode(file_get_contents("pdf/my_pdf_filename.pdf"));
$headers .= "--".$strSid."\n";
$headers .= "Content-Type: application/octet-stream; name=\"testing.pdf\"\n";
$headers .= "Content-Transfer-Encoding: base64\n";
$headers .= "Content-Disposition: attachment; filename=\"testing.pdf\"\n\n";
$headers .= $strContent1."\n\n";			
//echo $surveyemailbody;
	
	if(@mail($email, $subj, null, $headers))
	{
		echo 'success';
	}

//echo "<a href='pdf/my_pdf_filename.pdf'>Download PDF</a>";
?> 