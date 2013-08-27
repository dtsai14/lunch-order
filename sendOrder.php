<?php
include 'authenticate.php';

if (isset($_POST['order']) && !empty($_POST['order'])) {
    include 'mysql.php';

    $order = $_POST['order'];

    date_default_timezone_set('America/Los_Angeles');

    try {
        $statement = $pdo->prepare("INSERT INTO orders (text,user_id,restaurant_id) VALUES (?,?,?)");
        $statement->execute(array($order, $_SESSION['userid'], 1));
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}
?>