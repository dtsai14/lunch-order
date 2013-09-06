<?php

$dbhost = 'localhost';
$dbname = 'lunch_master';
$dbuser = 'root';
$dbpass = '';

$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);

function getRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM restaurants ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getNumVotes($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE DATE(creation_time) = CURDATE() AND restaurant_id = $restaurant_id");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getNumUsers() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function takeOrder($restaurant_id) {
    global $pdo;
    $user_id = $_SESSION['userid'];
    try {
        $stmt = $pdo->prepare("INSERT INTO open_restaurants(restaurant_id,user_id) VALUES (?,?)");
        $stmt->execute(array($restaurant_id, $user_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sendEmail($restaurant_id) {
    global $pdo;
    $user_id = $_SESSION['userid'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users");
        $stmt->execute;
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            $email = $user['email'];
            mail($email, 'Orders open', 'Please visit the LunchMaster website to place your order!');
        }
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getActiveRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT open_restaurants.*, restaurants.name, restaurants.menu_url, users.username FROM open_restaurants INNER JOIN restaurants ON open_restaurants.restaurant_id = restaurants.id INNER JOIN users ON open_restaurants.user_id = users.id  WHERE date(opening_time) = curdate() AND closing_time IS NULL ORDER BY opening_time");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getActiveRestaurantIds() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT restaurant_id FROM open_restaurants WHERE date(opening_time) = curdate() AND closing_time IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function deactivateRestaurant($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE open_restaurants SET closing_time = NOW() WHERE date(opening_time) = curdate() AND restaurant_id = $restaurant_id AND closing_time IS NULL");
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getClosedRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT restaurant_id, restaurants.name FROM open_restaurants INNER JOIN restaurants ON open_restaurants.restaurant_id = restaurants.id WHERE date(opening_time) = curdate() AND closing_time IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getRestaurantsOpenedBy($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT restaurant_id, restaurants.name FROM open_restaurants INNER JOIN restaurants ON open_restaurants.restaurant_id = restaurants.id WHERE date(opening_time) = curdate() AND user_id = $user_id AND closing_time IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getOrdersForRestaurant($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT orders.*, users.first_name, users.last_name FROM orders INNER JOIN users ON orders.user_id = users.id WHERE date(creation_date) = curdate() AND restaurant_id = $restaurant_id ORDER BY creation_date");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function getRestaurantName($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT name FROM restaurants WHERE id = $restaurant_id LIMIT 1");
        $stmt->execute();
        $name = $stmt->fetchColumn(0);
        return $name;
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function usernameExists($username) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute(array($username));
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function fetchOrderList() {
    global $pdo;
    try {
        $statement = $pdo->prepare("SELECT orders.*, users.username, restaurants.name FROM orders INNER JOIN users ON orders.user_id = users.id INNER JOIN restaurants ON orders.restaurant_id = restaurants.id WHERE date(creation_date) = curdate() ORDER BY creation_date DESC");
        $statement->execute();

        $orders = array();
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $orders []= array('username' => $row['username'], 'restaurant_name' => $row['name'], 'text' => $row['text'], 'creation_time' => date("g:i a", strtotime($row['creation_date'])));
        }
        return $orders;
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}
?>