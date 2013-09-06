<?php
include_once 'authenticate.php';
include_once 'mysql.php';

$jsonOrders = array();

foreach (fetchOrderList() as $order) {
    $jsonOrder = array("username" => $order['username'], "restaurant_name" => $order['restaurant_name'], "creation_time" => $order['creation_time'], "text" => $order['text']);
    $jsonOrders[] = $jsonOrder;
}

echo json_encode(array("orders" => $jsonOrders));
