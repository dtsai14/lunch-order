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
$firstname = $_POST['firstName'];
$lastname = $_POST['lastName'];
$username = $_POST['username'];
$password = $_POST['password'];
$token = md5("$salt1$password$salt2");

try {
    $statement = $pdo->prepare("INSERT INTO users (username,password,first_name,last_name,isadmin) VALUES (?,?,?,?,?)");
    $statement->execute(array($username,$token,$firstname,$lastname,false));
    $_SESSION['username'] = $username;
    $_SESSION['firstname'] = $firstname;
    $_SESSION['userid'] = $pdo->lastInsertID();
?>
<div class="jumbotron">
    <h1>Welcome, <?php echo $_SESSION['firstname'] ?>!</h1>
    <p>You have been added to the list of registered users in the PaperG lunch ordering system. Visit the LunchMaster homepage to vote on the restaurant of the day or place your order.</p>

    <p><a class="btn btn-primary btn-lg" href="lunchorder.php">LunchMaster</a></p>
</div>
<?php
} catch (PDOException $exception) {
    echo "PDO error :" . $exception->getMessage();
}
?>
</body>