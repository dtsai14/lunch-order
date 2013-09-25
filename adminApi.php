<?php
include_once './authenticate.php';
include_once './mysql.php';

/* returns JSON array containing information about each restaurant in the database,
including restaurant id, name, food type, menu url, taking orders status, and
whether or not user is authorized to close this restaurant */
function getTable() {
    $active_ids_and_users = array();
    foreach (sqlGetActiveRestaurants() as $restaurant) {
        $active_ids_and_users[$restaurant['restaurant_id']] = $restaurant['user_id'];
    }

    $json_restaurants = array();
    foreach (sqlGetRestaurants() as $restaurant) {
        if (array_key_exists($restaurant['id'], $active_ids_and_users)) {
            $taking_orders = "true";
            if ($active_ids_and_users[$restaurant['id']] == $_SESSION['user_id']) {
                $auth_close = "true";
            } else {
                $auth_close = "false";
            }
        } else {
            $taking_orders = "false";
            $auth_close = "false";
        }

        $json_restaurant = array("id" => $restaurant['id'],
            "name" => $restaurant['name'], "food_type" => $restaurant['food_type'],
            "menu_url" => $restaurant['menu_url'], "taking_orders" => $taking_orders,
            "auth_close" => $auth_close, "phone_num" => $restaurant['phone_num']);
        $json_restaurants[] = $json_restaurant;
    }
    return json_encode(array("restaurants" => $json_restaurants));
}

/* removes restaurant from mysql table*/
function deleteRestaurant() {
    $restaurant_id = $_POST['restaurant_id'];
    sqlDeleteRestaurant($restaurant_id);
}

function addPhone() {
    $restaurant_id = $_POST['restaurant_id'];
    $phone = $_POST['phone'];
    sqlAddPhone($restaurant_id, $phone);
}

/* NOT USING ANYMORE returns JSON array containing data about orders from restaurants this user
opened today, leaving out restaurants for which no one placed orders */
function exGetTakenOrders() {
    $json_taken_orders = array();
    foreach (sqlGetRestaurantsClosedBy($_SESSION['user_id']) as $restaurant) {
        $orders = sqlGetOrdersForRestaurant($restaurant['restaurant_id']);
        if (!empty($orders)) {
            $orders_array = array();
            foreach ($orders as $order) {
                $order_object = array("first_name" => $order['first_name'],
                    "last_name" => $order['last_name'], "text" => $order['text']);
                $orders_array[] = $order_object;
            }
            $json_taken_order = array("id" => $restaurant['restaurant_id'],
                "name" => $restaurant['name'], "orders" => $orders_array);
            $json_taken_orders[] = $json_taken_order;
        }
    };
    return json_encode(array("takenOrders" => $json_taken_orders));
}

function getTakenOrders() {
    $json_taken_orders = array();
    foreach (sqlGetSessionsOpenedBy($_SESSION['user_id']) as $open_session) {
        $orders = sqlGetOrdersForSession($open_session['id']);
        if (!empty($orders)) {
            $orders_array = array();
            foreach ($orders as $order) {
                $order_object = array("first_name" => $order['first_name'],
                    "last_name" => $order['last_name'], "text" => $order['text']);
                $orders_array[] = $order_object;
            }
            $json_taken_order = array("id" => $open_session['restaurant_id'],
                "name" => $open_session['restaurant_name'], "orders" => $orders_array);
            $json_taken_orders[] = $json_taken_order;
        }
    }
    return json_encode(array("takenOrders" => $json_taken_orders));
}

function takeOrders() {
    $restaurant_id = $_POST['restaurant_id'];
    sqlTakeOrder($restaurant_id);
}

function closeOrders() {
    $restaurant_id = $_POST['restaurant_id'];
    sqlDeactivateRestaurant($restaurant_id);
}



