<?php 
define('merchant_id','your merchant id');
define('Salt_Key','your salt key');
$environment = "sandbox";
$api_url = ($environment === "sandbox") ? "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/" : "https://api.phonepe.com/apis/hermes/pg/v1/";
define('return_url','your return url');
define('salt_index','your salt index');
define('callback_url','your callback url');

?>