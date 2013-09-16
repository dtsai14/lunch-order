<?php
include_once './authenticate.php';
include_once './mysql.php';

/* sends user's vote for given restaurant, return JSON object containing a vote
alert for the main page to display */
function sendVote() {
    $user_id = $_SESSION['user_id'];
    $restaurant_id = $_POST['restaurant_id'];
    $voteAlert = sqlSendVote($user_id, $restaurant_id);
    return json_encode(array("voteAlert" => $voteAlert));
}

/* returns JSON array containing data about restaurants which are currently open,
including restaurant id, name, menu url, and the username of user who opened it */
function getActiveRestaurants() {
    $json_active_restaurants = array();
    foreach (sqlGetActiveRestaurants() as $restaurant) {
        $json_active_restaurant = array("id" => $restaurant['restaurant_id'],
            "name" => $restaurant['name'], "menu_url" => $restaurant['menu_url'],
            "username" => $restaurant['username']);
        $json_active_restaurants[] = $json_active_restaurant;
    };
    return json_encode(array("activeRestaurants" => $json_active_restaurants));
};

function getClosedRestaurants() {
    $jsonClosedRestaurants = array();
    foreach (sqlGetClosedRestaurants() as $restaurant) {
        $jsonClosedRestaurants[] = $restaurant['name'];
    };
    return json_encode(array('closedRestaurants' => $jsonClosedRestaurants));
};

/* returns JSON array containing data about all orders sent today, including:
username, name of restaurant, time order was sent, and the text of the order */
function refreshOrders() {
    $json_orders = array();
    foreach (sqlFetchOrderList() as $order) {
        $json_order = array("username" => $order['username'], "restaurant_name" =>
        $order['restaurant_name'],
            "creation_time" => $order['creation_time'], "text" => $order['text']);
        $json_orders[] = $json_order;
    }
    return json_encode(array("orders" => $json_orders));

};

/* sends user's order */
function sendOrder() {
    $order = $_POST['order'];
    $restaurant_id = $_POST['restaurant_id'];
    sqlSendOrder($order, $_SESSION['user_id'], $restaurant_id);
};

/* returns JSON array containing data about votes for each restaurant today,
including: restaurant id, name, menu url, number of votes for this restaurant,
number of users currently registered in database, and percentage of current users
who voted for this restaurant */
function getVotes() {
    $json_restaurant_votes = array();
    foreach (sqlGetRestaurants() as $restaurant) {
        $num_votes = sqlGetNumVotes($restaurant['id']);
        $num_users = sqlGetNumUsers();
        $vote_bar = $num_votes / $num_users * 100;
        $json_restaurant_vote = array("id" => $restaurant['id'], "name" =>
        $restaurant['name'], "menu_url" => $restaurant['menu_url'],
            "num_votes" => $num_votes, "num_users" => $num_users,
            "vote_bar" => $vote_bar);
        $json_restaurant_votes[] = $json_restaurant_vote;
    };
    return json_encode(array("restaurantVotes" => $json_restaurant_votes));
}
