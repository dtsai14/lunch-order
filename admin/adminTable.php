<?php
include '../authenticate.php';
include '../mysql.php';

$activeRestaurants = sqlGetActiveRestaurants();

$activeIdsAndUsers = array();
foreach ($activeRestaurants as $restaurant) {
    $activeIdsAndUsers[$restaurant['restaurant_id']] = $restaurant['user_id'];
}

$jsonRestaurants = array();

foreach (sqlGetRestaurants() as $restaurant) {
    if (array_key_exists($restaurant['id'], $activeIdsAndUsers)) {
        $takingOrders = "true";
        if ($activeIdsAndUsers[$restaurant['id']] == $_SESSION['user_id']) {
            $authClose = "true";
        } else {
            $authClose = "false";
        }
    } else {
        $takingOrders = "false";
        $authClose = "false";
    }

    $jsonRestaurant = array("id" => $restaurant['id'], "name" => $restaurant['name'], "food_type" => $restaurant['food_type'], "menu_url" => $restaurant['menu_url'], "taking_orders" => $takingOrders, "auth_close" => $authClose);
    $jsonRestaurants[] = $jsonRestaurant;
}

echo json_encode(array("restaurants" => $jsonRestaurants));


