<?php
include 'authenticate.php';
include 'mysql.php';

$statement = $pdo->prepare("SELECT orders.*, users.username FROM orders INNER JOIN users ON orders.user_id = users.id WHERE date(creation_date) = curdate() ORDER BY creation_date DESC");
$statement->execute();

$orders = array();
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $orders []= array('username' => $row['username'], 'text' => $row['text'], 'creation_time' => date("g:i a", strtotime($row['creation_date'])));
}

//print_r($orders);

echo json_encode(array('orders'=>$orders));
//echo '{"orders": []}';
?>