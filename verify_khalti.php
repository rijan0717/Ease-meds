<?php
include 'config.php';

if (isset($_POST['token']) && isset($_POST['amount'])) {
    $token = $_POST['token'];
    $amount = $_POST['amount'];

    $args = http_build_query(array(
        'token' => $token,
        'amount' => $amount
    ));

    $url = "https://khalti.com/api/v2/payment/verify/";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $headers = ['Authorization: Key test_secret_key_6d63428d0526487e8e52e46b30c30a84'];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo $response;
}
?>
