<?php
include_once 'authenticate.php';
include_once 'mysql.php';

$orderList = fetchOrderList();
if (is_string($orderList)) {
    echo $orderList;
} else {
    $htmlOrder = "";
    foreach ($orderList as $order) {
        $htmlOrder .= "<div class='panel panel-default'><div class='panel-heading'><h3 class='panel-title'>"
            . $order['username'] . "'s order from " . $order['restaurant_name'] . "<span class='badge pull-right'>" . $order['creation_time']
            . "</span></h3></div><div class='panel-body'>" . $order['text'] . "</div></div>";
    }
    echo $htmlOrder;
}

?>