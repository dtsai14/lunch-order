<?php
include_once '../authenticate.php';
include_once '../mysql.php';

function sendVote() {
    $user_id = $_SESSION['user_id'];
    $restaurant_id = $_POST['restaurant_id'];
    return sqlSendVote($user_id, $restaurant_id);

}

function getActiveRestaurants() {
    $jsonActiveRestaurants = array();
    foreach (sqlGetActiveRestaurants() as $restaurant) {
        $jsonActiveRestaurant = array("id" => $restaurant['restaurant_id'],
            "name" => $restaurant['name'], "menu_url" => $restaurant['menu_url'],
            "username" => $restaurant['username']);
        $jsonActiveRestaurants[] = $jsonActiveRestaurant;
    };
    return json_encode(array("activeRestaurants" => $jsonActiveRestaurants));
};

function refreshOrders() {
    $jsonOrders = array();
    foreach (sqlFetchOrderList() as $order) {
        $jsonOrder = array("username" => $order['username'], "restaurant_name" => $order['restaurant_name'],
            "creation_time" => $order['creation_time'], "text" => $order['text']);
        $jsonOrders[] = $jsonOrder;
    }
    return json_encode(array("orders" => $jsonOrders));

};

function sendOrder() {
    $order = $_POST['order'];
    $restaurant_id = $_POST['restaurant_id'];
    sqlSendOrder($order, $_SESSION['user_id'], $restaurant_id);
};

$cmd = $_POST['cmd'];
if ($cmd == 'sendVote') {
    echo sendVote();
} else if ($cmd == 'getActiveRestaurants') {
    echo getActiveRestaurants();
} else if ($cmd == 'refreshOrders') {
    echo refreshOrders();
} else if ($cmd == 'sendOrder') {
    sendOrder();
};