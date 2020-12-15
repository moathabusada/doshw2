<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Origin: *");


include 'database.php';



$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );



////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// replicas managment

$balancer_db = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_order_balancer', '3306');

$query = $balancer_db->query("SELECT * FROM replicas WHERE `status` = 1 ORDER BY current_tasks ASC LIMIT 1");

if ($query->num_rows) {
    $destination_ip = $query->row['ip'];
    $destination_name = $query->row['name'];
    $balancer_db->query("UPDATE replicas SET current_tasks = (current_tasks + 1) WHERE `name` = '" . $destination_name . "'");
} else {
    header("HTTP/1.1 404 No Available Server");
    exit();
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// server operations

if ($uri[3] == 'buy') {
    
    $url = "http://" . $destination_ip . "/" . $destination_name . "/buy.php?book_id=" . $uri[4];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    echo $resp;

    $balancer_db->query("UPDATE replicas SET current_tasks = (current_tasks - 1) WHERE `name` = '" . $destination_name . "'");

    
    // All replicas DB sync
    syncOrderServers($destination_name, $uri[4]);

} else {

    header("HTTP/1.1 404 Not Found");
    exit();
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// internal syncing

function syncOrderServers($source, $book_id){

    $balancer_db2 = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_order_balancer', '3306');

    $query = $balancer_db2->query("SELECT * FROM replicas WHERE `status` = 1 AND name != '" . $source . "'");

    foreach ($query->rows as $result) {

        $replica_db = new DataBaseInstance('localhost', 'admin', '11223344', "bookstore_db_order_" . $result['name'], '3306');

        $sql = "INSERT INTO orders SET book_id = '" . $book_id . "', order_date = NOW()";

        $query = $replica_db->query($sql);

    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////