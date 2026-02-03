<?php
error_reporting(0);
//$string = '<html><body><a href="http://xyz.com/src/abc.png"><img src="http://xyz.com/res/bca.png"></a><a href="http://xyz.com/src/hvc.gif"><img src="http://xyz.com/res/ncq.jpg"></a></body></html>';

$string = file_get_contents('https://incometaxindiaefiling.gov.in/e-Filing/Services/KnowYourJurisdiction.html');
//var_dump($string);
//Load/parse the (x)html document
$doc = new DOMDocument();
$doc->loadHTML($string);

//get all 'a' elements (links)
/*$elements = $doc->getElementsByTagName('a');

//Now check if we got results
if($elements->length >= 1)
{
   //We got results, check each result
   foreach($elements as $element)
   {
      //Check if this Link has an img child element
      $img = $element->getElementsByTagName('img');
      //You can validate if the src contains .jpg extension if you want
      //but for this example I'm skipping this
      if($img->length == 1)
      {
         //We got an link that has a img child element, store link
         $links[] = $element->getAttribute('href');
      }
   }

   //show all links
   echo '<pre>'."\r\n";
   print_r($links);
   echo '</pre>'."\r\n";

}*/
$elements = $doc->getElementsByTagName('img');
foreach($elements as $imagetag){
	//if($imagetag->getElementById('captchaImg') == true)
	$src = $imagetag->attributes->getNamedItem('src')->nodeValue;
	$imagearray[] = $src;
}
echo '<pre>'."\r\n";
print_r($imagearray);
echo '</pre>'."\r\n";
/*$url = 'http://abc.go.com/';
$crl = curl_init();
$timeout = 5;
curl_setopt ($crl, CURLOPT_URL,$url);
curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
$ret = curl_exec($crl);
curl_close($crl);

print_r($ret);*/
?>