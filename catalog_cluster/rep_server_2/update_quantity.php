<?php

include 'database.php';


$book_id = filter_input(INPUT_GET, 'book_id', FILTER_SANITIZE_STRING);

$db = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_catalog_rep_server_2', '3306');

$sql = "UPDATE books SET quantity = (quantity - 1) WHERE book_id = '" . (int)$book_id . "'";

$query = $db->query($sql);