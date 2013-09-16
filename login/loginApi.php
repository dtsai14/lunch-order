<?php
include_once 'redirectHome.php';
include_once '../mysql.php';

$salt1 = "*qba";
$salt2 = "cl@&";

/* checks if input username has already been taken, returning a JSON array
containing warning alert if so; otherwise, registers user with given username,
password, first name, last name, and email and returns a welcome alert */
function registerUser() {
    global $salt1;
    global $salt2;
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $token = md5("$salt1$password$salt2");

    if (sqlUsernameExists($username)) {
        $type = 'warning';
        $text = 'This username has already been taken. Please choose a different one.';
    } else {
        sqlRegisterUser($username,$token,$first_name,$last_name,$email);
        $type = 'success';
        $text = 'Welcome! You have been added to the list of registered users in the PaperG lunch ordering system.
            Visit the <a href="../index.php" class="alert-link">LunchMaster homepage</a> to vote on the restaurant of the day or place your order.';
    }
    $alert = array('type' => $type, 'text' => $text);
    return json_encode(array("alert" => $alert));
}

/* checks given password against password stored for given username. Returns
JSON array containing true if they match and false otherwise */
function loginUser() {
    global $salt1;
    global $salt2;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $token = md5("$salt1$password$salt2");

    $loggedIn = sqlLoginUser($username,$token);
    return json_encode(array('loggedIn' => $loggedIn));
}

$cmd = $_POST['cmd'];
if ($cmd == 'registerUser') {
    echo registerUser();
} else if ($cmd == 'loginUser') {
    echo loginUser();
}