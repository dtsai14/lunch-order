<?php
include_once './authenticate.php';
include_once './mysql.php';

/* sends user's vote for given restaurant, return JSON object containing a vote
alert for the main page to display */
function sendVote() {
    $user_id = $_SESSION['user_id'];
    $restaurant_id = $_POST['restaurant_id'];
    $voteAlert = sqlSendVote($user_id, $restaurant_id);
    return json_encode(array('voteAlert' => $voteAlert));
}

/* returns JSON array containing data about restaurants which are currently open,
including restaurant id, name, menu url, and the username of user who opened it */
function getActiveRestaurants() {
    $json_active_restaurants = array();
    foreach (sqlGetActiveRestaurants() as $restaurant) {
        $json_active_restaurant = array('id' => $restaurant['restaurant_id'],
            'name' => $restaurant['name'], 'menu_url' => $restaurant['menu_url'],
            'username' => $restaurant['username']);
        $json_active_restaurants[] = $json_active_restaurant;
    };
    return json_encode(array('activeRestaurants' => $json_active_restaurants));
};

function getClosedRestaurants() {
    $json_closed_restaurants = array();
    foreach (sqlGetClosedRestaurants() as $restaurant) {
        $json_closed_restaurants[] = $restaurant['name'];
    };
    return json_encode(array('closedRestaurants' => $json_closed_restaurants));
};

/* returns JSON array containing data about all orders sent today, including:
username, name of restaurant, time order was sent, and the text of the order */
function refreshOrders() {
    $json_orders = array();
    foreach (sqlFetchOrders() as $order) {
        if ($order['user_id'] == $_SESSION['user_id']) {
            $auth_edit = "true";
            $auth_reject = "false";
        } else {
            $auth_edit = "false";
            $auth_reject = "false";
            foreach (sqlGetRestaurantsOpenedBy($_SESSION['user_id']) as $restaurant) {
                if ($restaurant['restaurant_id'] == $order['restaurant_id']) {
                    $auth_reject = "true";
                }
            }
        }
        $json_orders[] = array('username' => $order['username'],
            'restaurant_name' => $order['name'], 'text' => $order['text'],
            'creation_time' => date("g:i a", strtotime($order['creation_date'])),
            'auth_edit' => $auth_edit, 'auth_reject' => $auth_reject,
            'order_id' => $order['id'], 'rejection_id' => $order['rejection_id']);
    }
    return json_encode(array('orders' => $json_orders));
};

function getRejectedOrders() {
    $user_id = $_SESSION['user_id'];
    $json_rejected_orders = array();
    foreach (sqlGetRejectedOrdersFor($user_id) as $rejectedOrder) {
        $json_rejected_orders[] = array('admin' => $rejectedOrder['username'],
            'restaurant_name' => $rejectedOrder['name'],
            'text' => $rejectedOrder['text'], 'message' => $rejectedOrder['reject_message']);
    };
    return json_encode(array('rejectedOrders' => $json_rejected_orders));
};

function checkRejectedChanges() {
    $user_id = $_SESSION['user_id'];
    $json_changed_rejections = array();
    foreach (sqlGetRejectedChanges($user_id) as $changedRejectedOrder) {
        $json_changed_rejections[] = array('username' => $changedRejectedOrder['username'],
            'restaurant_name' => $changedRejectedOrder['restaurant_name']);
    };
    return json_encode(array('changedRejections' => $json_changed_rejections));
}

/* sends user's order for given restaurant, with given text order */
function sendOrder() {
    $order = $_POST['order'];
    $restaurant_id = $_POST['restaurant_id'];
    sqlSendOrder($order, $_SESSION['user_id'], $restaurant_id);
};

/* delete order with given order id */
function deleteOrder() {
    $order_id = $_POST['order_id'];
    sqlDeleteOrder($order_id);
};

function changeOrder() {
    $order_id = $_POST['order_id'];
    $edited_order = $_POST['edited_order'];
    $rejection_id = $_POST['rejection_id'];
    sqlChangeOrder($order_id, $edited_order, $rejection_id);
};

function rejectOrder() {
    $order_id = $_POST['order_id'];
    $reject_message = $_POST['reject_message'];
    $rejector_id = $_SESSION['user_id'];
    sqlRejectOrder($order_id, $reject_message, $rejector_id);
}

function acceptOrder() {
    $rejection_id = $_POST['rejection_id'];
    sqlAcceptOrder($rejection_id);
}

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
        $json_restaurant_vote = array('id' => $restaurant['id'], 'name' =>
        $restaurant['name'], 'menu_url' => $restaurant['menu_url'],
            'num_votes' => $num_votes, 'num_users' => $num_users,
            'vote_bar' => $vote_bar);
        $json_restaurant_votes[] = $json_restaurant_vote;
    };
    return json_encode(array('restaurantVotes' => $json_restaurant_votes));
}
