<?php
include_once '../authenticate.php';
include_once '../mysql.php';

$jsonTakenOrders = array();

foreach (sqlGetRestaurantsOpenedBy($_SESSION['user_id']) as $restaurant) {
    $orders = sqlGetOrdersForRestaurant($restaurant['restaurant_id']);
    if (!empty($orders)) {
        $ordersArray = array();
        foreach ($orders as $order) {
            $orderObject = array("first_name" => $order['first_name'], "last_name" => $order['last_name'], "text" => $order['text']);
            $ordersArray[] = $orderObject;
        }
        $jsonTakenOrder = array("id" => $restaurant['restaurant_id'], "name" => $restaurant['name'], "orders" => $ordersArray);
        $jsonTakenOrders[] = $jsonTakenOrder;
    }
};

echo json_encode(array("takenOrders" => $jsonTakenOrders));