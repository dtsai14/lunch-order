<?php
include 'authenticate.php';
include 'mysql.php';

$restaurant_id = $_POST['restaurant_id'];
try {
    $statement = $pdo->prepare("DELETE FROM restaurants WHERE id = $restaurant_id");
    $statement->execute();
} catch (PDOException $e) {
    $error = "Error!: " . $e->getMessage() . "<br/>";
    echo json_encode($error);
}

?>

