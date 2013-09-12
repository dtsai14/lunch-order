<?php
include_once 'authenticate.php';
include_once 'mysql.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "take_orders") {
        $restaurant_id = $_POST['restaurant_id'];
        sqlTakeOrder($restaurant_id);
    } else if ($_POST['action'] == "get_active_restaurants"){
        $active_restaurants = sqlGetActiveRestaurants();
        echo json_encode(array('activeRestaurants'=>$active_restaurants));
    } else if ($_POST['action'] == "deactivate_restaurant"){
        /* deactivates restaurant and gathers all orders for the restaurant today,
        return a JSON array of */
        $restaurant_id = $_POST['restaurant_id'];
        sqlDeactivateRestaurant($restaurant_id);
        $orders = sqlGetOrdersForRestaurant($restaurant_id);

        if (empty($orders)) {
            $htmlTakenOrders = "";
        } else {
            $htmlTakenOrders = "<div class='panel panel-default'><div class='panel-heading'><h4 class='panel-title'><a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion' href='#collapse". $restaurant_id . "'>"
                . sqlGetRestaurantName($restaurant_id) . "</a></h4></div><div id='collapse" . $restaurant_id . "' class='panel-collapse collapse in'><div class='panel-body'><dl class='dl-horizontal'>";

            foreach ($orders as $order) {
                $htmlTakenOrders .= "<dt>" . $order['first_name'] . " " . $order['last_name'] . "</dt><dd>" . $order['text'] . "</dd>";
            }
            $htmlTakenOrders .= "</dl></div></div></div>";
        }
        echo json_encode(array('htmlTakenOrders' => $htmlTakenOrders));
    } else if ($_POST['action'] == "get_closed_restaurants") {
        $jsonClosedRestaurants = array();
        foreach (sqlGetClosedRestaurants() as $restaurant) {
            $jsonClosedRestaurants[] = $restaurant['name'];
        };
        echo json_encode(array('closedRestaurants' => $jsonClosedRestaurants));
    }
};
