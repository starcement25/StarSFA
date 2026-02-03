<?php

/*$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://vj1w9p.api.infobip.com/settings/1/accounts/8F0792F86035A9F4290821F1EE6BC06A/api-keys/573711510E1C002E29679B12C7CB48AE',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{"allowedIPs":["string"],"enabled":true,"name":"string","permissions":["ALL"],"validFrom":"2021-06-09T12:41:38Z","validTo":"2021-06-09T12:41:38Z"}',
    CURLOPT_HTTPHEADER => array(
        'Authorization: {authorization}',
        'Content-Type: application/json',
        'Accept: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;*/
//https://vj1w9p.api.infobip.com
//Template message
$encodedval=base64_encode('star_pushnotification:forcePower2021@#');
$finalval='Basic '.$encodedval;
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://zwyv2.api.infobip.com/whatsapp/1/message/template',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{"messages":[{"from":"917595080005","to":"919474335413","messageId":"a211198-d2342","content":{"templateName":"notif_13","templateData":{"body":{"placeholders":["Placeholder Value 1","Placeholder Value 5"]}},"language":"en"},"callbackData":"Callback data"}]}',
    CURLOPT_HTTPHEADER => array(
        "Authorization: $finalval",
        'Content-Type: application/json',
        'Accept: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>