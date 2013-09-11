<?php
include '../authenticate.php';
include '../mysql.php';

$user_id = $_SESSION['user_id'];

try {
    $statement = $pdo->prepare("SELECT * FROM votes WHERE DATE(creation_time) = CURDATE( ) AND user_id = $user_id");
    $statement->execute();
    if ($statement->fetch()) {
        echo json_encode(array('data_added' => false));
    } else {
        $restaurant_id = $_POST['restaurant_id'];
        $statement = $pdo->prepare("INSERT INTO votes(user_id,restaurant_id) VALUES (?,?)");
        $statement->execute(array($user_id, $restaurant_id));
        echo json_encode(array('data_added' => true));
    }
} catch (PDOException $e) {
    $error = "Error!: " . $e->getMessage() . "<br/>";
    echo json_encode($error);
}
