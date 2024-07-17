<?php

$ch = curl_init();


curl_setopt_array($ch, array(
    CURLOPT_URL => "https://www.google.com",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.'$this->token'
    ),
));

$response = curl_exec($ch);


curl_close($ch);

echo $response;