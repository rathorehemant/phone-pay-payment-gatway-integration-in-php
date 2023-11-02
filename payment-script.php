<?php 
require('config.php');
if($_POST){
    
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
     $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_STRING);
    $message = [];

    if(empty($name) ||empty($email) || empty($phone) || empty($amount) ){
        $message = ['error'=>true,'message'=>'All fields are requirement'];
    }else{
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = ['error'=>true,'message'=>'Please provide valid email'];
        }else{
            if (!ctype_digit($amount)) {
                $message = ['error' => true, 'message' => 'Amount should contain digits only'];
            }else {
                $amount = (int)$amount; // Convert to an integer
                if ($amount <= 0) {
                    $message = ['error' => true, 'message' => 'Amount should be greater than 1'];
                }else{

                    $description = 'Shillong Tours Packages payment' ;
                    
                  // prepared the payload data
                        $payloadData = array(
                        'merchantId' => merchant_id,
                        'merchantTransactionId' => "MT7850590068188104", // test transactionID
                        "merchantUserId"=>"MUID123",
                        'amount' => $amount*100,
                        'redirectUrl'=>return_url,
                        'redirectMode'=>"POST",
                        'callbackUrl'=>return_url,
                        "merchantOrderId"=>uniqid(),
                        "mobileNumber"=>$phone,
                        "message"=>$description,
                        "email"=>$email,
                        "shortName"=>$name,
                        "paymentInstrument"=> array(    
                            "type"=> "PAY_PAGE",
                    )
                    );
                    
                    $jsonencode = json_encode($payloadData);
                    $payloadMain = base64_encode($jsonencode);
                    $salt_index = salt_index; //key index 1
                    $payload = $payloadMain . "/pg/v1/pay" . Salt_Key;
                    $sha256 = hash("sha256", $payload);
                    $final_x_header = $sha256 . '###' . $salt_index;
                    $request = json_encode(array('request'=>$payloadMain));
                    
                    // $url = "https://api.phonepe.com/apis/hermes/pg/v1/pay"; <PRODUCTION URL>
                    
                    $curl = curl_init();
                curl_setopt_array($curl, [
                CURLOPT_URL => $api_url.'pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $request,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "X-VERIFY: " . $final_x_header,
                    "accept: application/json"
                ],
                ]);
                
                $response = curl_exec($curl);
                $err = curl_error($curl);
                
                curl_close($curl);
                
                if ($err) {
                echo "cURL Error #:" . $err;
                } else {
                $res = json_decode($response);
                if($res->success){
                    $message = ['error' => false, 'message' => $res->message,'payment_url' =>$res->data->instrumentResponse->redirectInfo->url ];
                }else{
                    echo '<pre>';
                            print_r( $res);
                            echo '</pre>';
                }       

                            

                }
                }
            }
        }
    }

    echo json_encode( $message);
}else{
    echo 'You are not allow to access';
}
?>