<?php
include './indexApi.php';
include './adminApi.php';

$cmd = $_POST['cmd'];
if ($cmd == 'getVotes') { // from indexApi.php
    echo getVotes();
} else if ($cmd == 'sendVote') {
    echo sendVote();
} else if ($cmd == 'getActiveRestaurants') {
    echo getActiveRestaurants();
} else if ($cmd == 'getClosedRestaurants') {
    echo getClosedRestaurants();
} else if ($cmd == 'refreshOrders') {
    echo refreshOrders();
} else if ($cmd == 'sendOrder') {
    sendOrder();
} else if ($cmd == 'getTable') { // from adminApi.php
    echo getTable();
} else if ($cmd == 'deleteRestaurant') {
    deleteRestaurant();
} else if ($cmd == 'getTakenOrders') {
    echo getTakenOrders();
} else if ($cmd == 'takeOrders') {
    takeOrders();
} else if ($cmd == 'closeOrders') {
    closeOrders();
};