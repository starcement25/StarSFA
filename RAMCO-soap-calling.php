<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
set_time_limit(0);

echo $xmlPayload = file_get_contents("php://input");

			$soapUrl = "https://demoiris.ramcoerp.com:8246/services/Mnt_SaleOrder_API?wsdl"; // asmx URL of WSDL
			$user = "KarmaUser";  //  username
			$password = "K@rm@!123"; // password
			
			//$post_string =$test;   // data from the form, e.g. some ID number
			
			//exit();
			$headers = array(
			//"Content-type: text/xml;charset=\"utf-8\";SOAPAction: http://www.ramco.com/iris/EnterpriseServices/IMnt_SaleOrder_API/sOCreate_Authorize_OP;",
			//"SOAPAction=http://www.ramco.com/iris/EnterpriseServices/IMnt_SaleOrder_API/sOCreate_Authorize_OP",
			 //"Content-type: text/xml;charset=\"utf-8\"",
			 "Content-type: application/soap+xml;charset=\"utf-8\";SOAPAction= http://www.ramco.com/iris/EnterpriseServices/IMnt_SaleOrder_API/sOCreate_Authorize_OP;",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"Content-length: ".strlen($xmlPayload), 
		); //SOAPAction: your op URL*/

			$url = $soapUrl;
			$cookie_file_path = "cookie.txt"; 
			// PHP cURL  for https connection with auth
			$ch = curl_init();
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT);
			curl_setopt ($ch, CURLOPT_SSLVERSION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_USERPWD, $user.":".$password); // username and password - declared at the top of the doc
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_TIMEOUT, 400);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlPayload); // the SOAP request
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
			curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers); 
			$response = curl_exec($ch);
			//$info = curl_getinfo($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			//$err = curl_error($ch);  
			curl_close($ch);
			//print_r($info);
			echo $httpcode;
?>