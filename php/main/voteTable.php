<?php
include_once "../authenticate.php";
include_once "../mysql.php";

$jsonRestaurantVotes = array();

foreach (sqlGetRestaurants() as $restaurant) {
    $numVotes = sqlGetNumVotes($restaurant['id']);
    $numUsers = sqlGetNumUsers();
    $voteBar = $numVotes / $numUsers * 100;
    $jsonRestaurantVote = array("id" => $restaurant['id'], "name" => $restaurant['name'], "menu_url" => $restaurant['menu_url'], "num_votes" => $numVotes, "num_users" => $numUsers, "vote_bar" => $voteBar);
    $jsonRestaurantVotes[] = $jsonRestaurantVote;
};

echo json_encode(array("restaurantVotes" => $jsonRestaurantVotes));