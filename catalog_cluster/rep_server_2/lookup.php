<?php

include 'database.php';

$json = array();

$book_id = filter_input(INPUT_GET, 'book_id', FILTER_SANITIZE_STRING);



$db = new DataBaseInstance('localhost', 'admin', '11223344', 'bookstore_db_catalog_rep_server_2', '3306');

$sql = "SELECT * FROM books WHERE book_id = '" . $book_id . "'";

$query = $db->query($sql);

if($query->num_rows){
    $json = $query->row;
}else{
    $json['msg'] = "No book founded with sent book_id";
    $json['status'] = "FALSE";
}


echo json_encode($json);
