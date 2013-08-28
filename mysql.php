<?php
$dbhost = 'localhost';
$dbname = 'lunch_master';
$dbuser = 'root';
$dbpass = '';

$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);

function getRestaurants(){
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM restaurants ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function getNumVotes($restaurant_id){
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE DATE(creation_time) = CURDATE() AND restaurant_id = $restaurant_id");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function getNumUsers() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}

?>