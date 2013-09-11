<?php
include 'redirectHome.php';

$dbhost = 'localhost';
$dbname = 'lunch_master';
$dbuser = 'root';
$dbpass = '';

$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
$salt1 = "*qba";
$salt2 = "cl@&";

$username = $_POST['username'];
$password = $_POST['password'];
$token = md5("$salt1$password$salt2");

try {
    $statement = $pdo->prepare("SELECT * FROM users WHERE username='$username'");
    $statement->execute();
    $user = $statement->fetch();
    if ($user['password'] == $token) {
        $_SESSION['username'] = $username;
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['displayQuote'] = true;
        echo json_encode("");
    } else {
        $result = json_encode("<div class='alert alert-danger alert-dismissable'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>
        &times;</button>Invalid username/password combination. Please <a href='registration.php'
        class='alert-link'>register</a> or try logging in again</p>");
        echo $result;
    }
} catch (PDOException $exception) {
    echo "PDO error :" . $exception->getMessage();
}
