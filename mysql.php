<?php
date_default_timezone_set('America/Los_Angeles');

$dbhost = 'localhost';
$dbname = 'lunch_master';
$dbuser = 'root';
$dbpass = '';

$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);

$stmt = $pdo->prepare("SET time_zone = 'America/Los_Angeles'");
$stmt->execute();

function sqlRegisterUser($username,$token,$first_name,$last_name,$email) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username,password,
        first_name,last_name,email) VALUES (?,?,?,?,?)");
        $stmt->execute(array($username,$token,$first_name,$last_name,$email));
        $_SESSION['username'] = $username;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['user_id'] = $pdo->lastInsertID();
        $_SESSION['display_quote'] = true;
        $_SESSION['display_pic'] = false;
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlLoginUser($username,$token) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username='$username'");
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user['password'] == $token) {
            $_SESSION['username'] = $username;
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['display_quote'] = true;
            if (sqlHasVoted($user['id'])) {
                $_SESSION['display_pic'] = pickPic();
            } else  {
                $_SESSION['display_pic'] = false;
            }
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function pickPic() {
    $sources = array("http://therufusway.files.wordpress.com/2013/05/cutest-little-corgi-ever.jpeg",
        "http://1.bp.blogspot.com/-0IKvqJctUT8/TV6FkkC2mmI/AAAAAAAAA7c/1SjSs2-Mhs0/s400/pig.jpg",
        "http://cutenfunny.com/thumbs/?src=photos/tumblr_mesgdzhrap1qew6kmo1_500.jpg&w=600&zc=1",
        "http://cdn.cutestpaw.com/wp-content/uploads/2011/11/cute-puppy-l1.jpg",
        "http://cutestuff.co/wp-content/uploads/2012/03/cute_cat_eyes.jpg",
        "http://www.aplacetolovedogs.com/wp-content/uploads/2012/10/cute-corgi-bum.jpg",
        "http://data.whicdn.com/images/9148878/cute,dog,puppy,corgi,puppies,baby-c47f30e1d63cc913cefb8204bd961193_h_large.jpg",
        "http://memberfiles.freewebs.com/94/63/70766394/photos/Puppies/cori%20puppy.jpg");

    $randomKey = array_rand($sources);
    return $sources[$randomKey];
}

function sqlHasVoted($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM votes WHERE DATE(creation_time) =
        CURDATE() AND user_id = $user_id");
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

/*************** Misc ********************/
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

function sqlGetRestaurantName($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT name FROM restaurants
        WHERE id = $restaurant_id LIMIT 1");
        $stmt->execute();
        $name = $stmt->fetchColumn(0);
        return $name;
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlGetActiveRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT open_restaurants.*, restaurants.name,
restaurants.menu_url, users.username FROM open_restaurants INNER JOIN restaurants
ON open_restaurants.restaurant_id = restaurants.id INNER JOIN users ON
open_restaurants.user_id = users.id  WHERE DATE(opening_time) = CURDATE() AND
closing_time IS NULL ORDER BY opening_time");
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
        $stmt = $pdo->prepare("SELECT restaurant_id FROM open_restaurants WHERE
        DATE(opening_time) = CURDATE() AND closing_time IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

/******************* index *********************/

/**************** voting table *****************/
function sqlGetNumVotes($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE DATE(creation_time) =
         CURDATE() AND restaurant_id = $restaurant_id");
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

function sqlSendVote($user_id, $restaurant_id) {
    global $pdo;
    try {
        if (sqlHasVoted($user_id)) {
            $type = 'warning';
            $text = "You've already voted once today! Please vote again tomorrow.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO votes(user_id,restaurant_id)
             VALUES (?,?)");
            $stmt->execute(array($user_id, $restaurant_id));
            $type = 'success';
            $text = "Your vote has been recorded!";
            $_SESSION['display_pic'] = pickPic(); // make header display pic
        };
        return array('type' => $type, 'text' => $text);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}



/******************* ordering ******************/
function sqlSendOrder($order, $user_id, $restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (text,user_id,restaurant_id)
        VALUES (?,?,?)");
        $stmt->execute(array($order, $user_id, $restaurant_id));
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function sqlFetchOrderList() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT orders.*, users.username, restaurants.name
        FROM orders INNER JOIN users ON orders.user_id = users.id INNER JOIN restaurants
        ON orders.restaurant_id = restaurants.id WHERE DATE(creation_date) = CURDATE()
        ORDER BY creation_date DESC");
        $stmt->execute();

        $orders = array();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $orders []= array('username' => $row['username'], 'restaurant_name' =>
            $row['name'], 'text' => $row['text'], 'creation_time' =>
            date("g:i a", strtotime($row['creation_date'])));
        }
        return $orders;
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

 /***************** need to implement email ******************/
function sqlSendEmail($restaurant_id) {
    global $pdo;
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users");
        $stmt->execute;
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            $email = $user['email'];
            mail($email, 'Orders open', 'Please visit the LunchMaster website
            to place your order!');
        }
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}
/******************end of danger area******************************/



/******************* admin *********************/
function sqlAddRestaurant($name, $type, $url) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO restaurants (name,food_type,menu_url) VALUES (?,?,?)");
        $stmt->execute(array($name, $type, $url));
    } catch (PDOException $e){
        $error = "PDO error :" . $e->getMessage() . "<br/>";
        echo $error;
    }
}

function sqlAddMenu($url, $restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE restaurants SET menu_url=? WHERE id=?");
        $stmt->execute(array($url, $restaurant_id));
    } catch (PDOException $e) {
        $error = "PDO error :" . $e->getMessage() . "<br/>";
        echo $error;
    }
}

function sqlDeleteRestaurant($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id=?");
        $stmt->execute(array($restaurant_id));
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        echo $error;
    }
}

/*******************taking orders***************/
function sqlTakeOrder($restaurant_id) {
    global $pdo;
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO open_restaurants(restaurant_id,user_id)
        VALUES (?,?)");
        $stmt->execute(array($restaurant_id, $user_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlDeactivateRestaurant($restaurant_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE open_restaurants SET closing_time = NOW()
        WHERE DATE(opening_time) = CURDATE() AND restaurant_id = $restaurant_id
        AND closing_time IS NULL");
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}

function sqlGetClosedRestaurants() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT restaurant_id, restaurants.name
        FROM open_restaurants INNER JOIN restaurants ON
        open_restaurants.restaurant_id = restaurants.id WHERE DATE(opening_time)
        = CURDATE() AND closing_time IS NOT NULL");
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
        $stmt = $pdo->prepare("SELECT DISTINCT restaurant_id, restaurants.name
        FROM open_restaurants INNER JOIN restaurants ON
        open_restaurants.restaurant_id = restaurants.id WHERE DATE(opening_time)
         = CURDATE() AND user_id = $user_id AND closing_time IS NOT NULL");
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
        $stmt = $pdo->prepare("SELECT orders.*, users.first_name, users.last_name
        FROM orders INNER JOIN users ON orders.user_id = users.id WHERE
        DATE(creation_date) = CURDATE() AND restaurant_id = $restaurant_id
        ORDER BY creation_date");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error!: " . $e->getMessage() . "<br/>";
        return $error;
    }
}




