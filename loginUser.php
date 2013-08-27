<?php
include 'redirectHome.php';
?>

<!DOCTYPE html>
<head lang="en">
    <?php
    include 'bootstrapSources.php';
    ?>
</head>
<body>
<?php
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
    $result = $statement->fetch();
    if ($result['password'] == $token) {
        $_SESSION['username'] = $username;
        $_SESSION['firstname'] = $result['first_name'];
        $_SESSION['userid'] = $result['id'];
        die("<div class='alert alert-success'>Welcome, $_SESSION[firstname]! You are now logged in as $username. <a href='lunchorder.php' class='alert-link'>Continue to the main page.</a></div>");

    } else {
        die("<div class='alert alert-warning'>Invalid username/password combination. Please <a href='registration.php' class='alert-link'>register</a> or try <a href='login.php' class='alert-link'>logging in</a> again</p>");
    }
} catch (PDOException $exception) {
    echo "PDO error :" . $exception->getMessage();
}
?>
</body>