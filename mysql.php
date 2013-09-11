<?php
date_default_timezone_set('America/Los_Angeles');

$dbhost = 'localhost';
$dbname = 'lunch_master';
$dbuser = 'root';
$dbpass = '';

$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);

function sqlGetRestaurants() {
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

function sqlSendVote($user_id, $restaurant_id) {
    global $pdo;
    try {
        $statement = $pdo->prepare("SELECT * FROM votes WHERE DATE(creation_time) = CURDATE() AND user_id = $user_id");
        $statement->execute();
        if ($statement->fetch()) {
            $type = 'warning';
            $text = "You've already voted once today! Please vote again tomorrow.";
        } else {
            $statement = $pdo->prepare("INSERT INTO votes(user_id,restaurant_id) VALUES (?,?)");
            $statement->execute(array($user_id, $restaurant_id));
            $type = 'success';
            $text = "Your vote has been recorded!";
        };
        return json_encode(array('type' => $type, 'text' => $text));
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return json_encode(array('error' => $error));
    }
}

function sqlGetNumVotes($restaurant_id) {
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

function sqlGetNumUsers() {
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

function sqlTakeOrder($restaurant_id) {
    global $pdo;
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO open_restaurants(restaurant_id,user_id) VALUES (?,?)");
        $stmt->execute(array($restaurant_id, $user_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

 /********************************* need to implement email ***********************************************/
function sqlSendEmail($restaurant_id) {
    global $pdo;
    $user_id = $_SESSION['user_id'];
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
/******************end of danger area******************************/

function sqlGetActiveRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT open_restaurants.*, restaurants.name, restaurants.menu_url, users.username FROM open_restaurants INNER JOIN restaurants ON open_restaurants.restaurant_id = restaurants.id
        INNER JOIN users ON open_restaurants.user_id = users.id  WHERE DATE(opening_time) = CURDATE() AND closing_time IS NULL ORDER BY opening_time");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

/**************************************don't need this anymore? **********************************/
function sqlGetActiveRestaurantIds() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT restaurant_id FROM open_restaurants WHERE DATE(opening_time) = CURDATE() AND closing_time IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlDeactivateRestaurant($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE open_restaurants SET closing_time = NOW() WHERE DATE(opening_time) = CURDATE() AND restaurant_id = $restaurant_id AND closing_time IS NULL");
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlGetClosedRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT restaurant_id, restaurants.name FROM open_restaurants INNER JOIN restaurants ON open_restaurants.restaurant_id = restaurants.id WHERE DATE(opening_time) = CURDATE() AND closing_time IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlGetRestaurantsOpenedBy($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT restaurant_id, restaurants.name FROM open_restaurants INNER JOIN restaurants ON open_restaurants.restaurant_id = restaurants.id WHERE DATE(opening_time) = CURDATE() AND user_id = $user_id AND closing_time IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlGetOrdersForRestaurant($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT orders.*, users.first_name, users.last_name FROM orders INNER JOIN users ON orders.user_id = users.id WHERE DATE(creation_date) = CURDATE() AND restaurant_id = $restaurant_id ORDER BY creation_date");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlGetRestaurantName($restaurant_id) {
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

function sqlUsernameExists($username) {
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

function sqlFetchOrderList() {
    global $pdo;
    try {
        $statement = $pdo->prepare("SELECT orders.*, users.username, restaurants.name FROM orders INNER JOIN users ON orders.user_id = users.id INNER JOIN restaurants ON orders.restaurant_id = restaurants.id WHERE DATE(creation_date) = CURDATE() ORDER BY creation_date DESC");
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

function sqlSendOrder($order, $user_id, $restaurant_id) {
    global $pdo;
    try {
        $statement = $pdo->prepare("INSERT INTO orders (text,user_id,restaurant_id) VALUES (?,?,?)");
        $statement->execute(array($order, $user_id, $restaurant_id));
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}