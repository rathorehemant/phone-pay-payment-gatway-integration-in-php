<?php 
require('config.php');
session_start();
 $merchent_id =   $_POST['merchantId'];

$merchent_trans_id =  $_POST['transactionId'];




// call check status api

 $string = "/pg/v1/status/" .$merchent_id ."/" .$merchent_trans_id .Salt_Key ;

$sha256 = hash('sha256',$string);
$salt_index = salt_index;
$final_header = $sha256."###".$salt_index;



$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $api_url. 'status/' .$merchent_id. '/'. $merchent_trans_id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Content-Type: application/json',
    'X-VERIFY:' . $final_header,
    'X-MERCHANT-ID: ' . $merchent_id
  ),
));

$response = curl_exec($curl);

curl_close($curl);

  $res = json_decode($response);
//   echo '<pre>';
//   print_r($res);
//   echo '</pre>';
//   echo $res->success;
//   die();

  // check payment status if payment success then redirect to success page else redirect to error page
  if($res->success){
    header('location:your success url');
    $_SESSION['code'] = $res->code;
    $_SESSION['message'] = $res->message;
    $_SESSION['transection_id'] = $res->data->transactionId;
    $_SESSION['amount'] = $res->data->amount;
    $_SESSION['type'] = $res->data->paymentInstrument->type;
    


  }else{
    header('location:your error page url');
    $_SESSION['code'] = $res->code;
    $_SESSION['message'] = $res->message;
    $_SESSION['declined_description'] = $res->data->responseCodeDescription;
    
  }

  echo '<pre>';
  print_r($res);
  echo '</pre>';



?>