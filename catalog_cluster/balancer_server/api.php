<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Origin: *");



include 'database.php';
include 'cache.php';


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// connect to cache server

$cache_server = new Cache();


////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// replicas managment

$balancer_db = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_catalog_balancer', '3306');


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

if ($uri[3] == 'search') {

    $url = "http://" . $destination_ip . "/" . $destination_name . "/search.php?topic=" . $uri[4];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    echo $resp;

    $balancer_db->query("UPDATE replicas SET current_tasks = (current_tasks - 1) WHERE `name` = '" . $destination_name . "'");

} elseif ($uri[3] == 'lookup') {

    // check the cache first

    $cache_result = $cache_server->get("lookup." . $uri[4]);

    if ($cache_result) {

        $cache_result['from_cache'] = true;  // just for testing
        echo json_encode($cache_result);

        $balancer_db->query("UPDATE replicas SET current_tasks = (current_tasks - 1) WHERE `name` = '" . $destination_name . "'");

    } else {

        // if the lookup not found on cache, read it from server

        $url = "http://" . $destination_ip . "/" . $destination_name . "/lookup.php?book_id=" . $uri[4];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        curl_close($curl);

        echo $resp;

        $balancer_db->query("UPDATE replicas SET current_tasks = (current_tasks - 1) WHERE `name` = '" . $destination_name . "'");


        // cache update (add lookup)
        $cache_server->set("lookup." . $uri[4], json_decode($resp, true));
    }

} elseif ($uri[3] == 'update_quantity') {

    $url = "http://" . $destination_ip . "/" . $destination_name . "/update_quantity.php?book_id=" . $uri[4];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    echo $resp;

    $balancer_db->query("UPDATE replicas SET current_tasks = (current_tasks - 1) WHERE `name` = '" . $destination_name . "'");


    // All replicas DB sync
    syncCatalogServers($destination_name, $uri[4]);

    // cache update (remove lookup)
    $cache_server->delete("lookup." . $uri[4]);
    
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// internal DBs syncing

function syncCatalogServers($source, $book_id)
{

    $replica_db = new DataBaseInstance('localhost', 'admin', '11223344', "bookstore_db_catalog_" . $source, '3306');

    $sql = "SELECT quantity FROM books WHERE book_id = '" . $book_id . "'";

    $query = $replica_db->query($sql);

    $quantity = $query->row['quantity'];


    $balancer_db2 = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_catalog_balancer', '3306');

    $query = $balancer_db2->query("SELECT * FROM replicas WHERE `status` = 1 AND name != '" . $source . "'");

    foreach ($query->rows as $result) {

        $replica_db2 = new DataBaseInstance('localhost', 'admin', '11223344', "bookstore_db_catalog_" . $result['name'], '3306');

        $sql = "UPDATE books SET quantity = '" . (int)$quantity . "' WHERE book_id = '" . (int)$book_id . "'";

        $query = $replica_db2->query($sql);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
