<?php

include 'database.php';

$json = array();

$book_id = filter_input(INPUT_GET, 'book_id', FILTER_SANITIZE_STRING);


$url = "http://34.121.141.144/balancer_server/api.php/lookup/" . $book_id;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($curl);
curl_close($curl);


$result = json_decode($resp, true);


if ($result['status'] != "FALSE") {

    if ($result['quantity'] < 1) {
        $json['msg'] = "book out of stock";
        $json['status'] = "FALSE";
    } else {

        $db = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_order_rep_server_1', '3306');

        $sql = "INSERT INTO orders SET book_id = '" . $book_id . "', order_date = NOW()";

        $query = $db->query($sql);

        $order_id = $db->getLastId();

        // update quantity
        $url = "http://34.121.141.144/balancer_server/api.php/update_quantity/" . $book_id;
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        curl_close($curl);

        $json['msg'] = "Order was added with order_id #" . $order_id;
        $json['status'] = "TRUE";
    }
} else {
    $json['msg'] = "No book founded with sent book_id";
    $json['status'] = "FALSE";
}


header('Content-Type: application/json');
echo json_encode($json);
