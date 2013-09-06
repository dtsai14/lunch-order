<?php
include_once 'authenticate.php';
include_once 'mysql.php';

$jsonActiveRestaurants = array();

foreach (getActiveRestaurants() as $restaurant) {
    $jsonActiveRestaurant = array("id" => $restaurant['restaurant_id'], "name" => $restaurant['name'], "menu_url" => $restaurant['menu_url'], "username" => $restaurant['username']);
    $jsonActiveRestaurants[] = $jsonActiveRestaurant;
};

echo json_encode(array("activeRestaurants" => $jsonActiveRestaurants));
