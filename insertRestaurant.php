<?php
include 'authenticate.php';
?>
<!DOCTYPE html>
<head lang="en">
    <?php
    include 'bootstrapSources.php';
    ?>
</head>
<body>
<?php
include 'mysql.php';

$name = $_POST['restaurantName'];
$type = $_POST['restaurantType'];
//have to change path so is not absolute

if ($_POST['menuInputURL']) {
    $url = $_POST['menuInputURL'];
} else if ($_FILES['menuInputFile']['name']) {
    print_r($_FILES['menuInputFile']);
    $ext = pathinfo($_FILES['menuInputFile']['name'], PATHINFO_EXTENSION);
    $fileName = "C:\wamp\www\lunchorder\Menus\\$name"."_menu.$ext";
    move_uploaded_file($_FILES['menuInputFile']["tmp_name"], $fileName);
    $url = "/lunchorder/Menus/$name"."_menu.$ext";
} else {
    $url = "";
}

try {
    $statement = $pdo->prepare("INSERT INTO restaurants (name,food_type,menu_url) VALUES (?,?,?)");
    $statement->execute(array($name, $type, $url));

?>
<div class="jumbotron">
    <h1>Restaurant Added</h1>
    <p>Return to the homepage or add more restaurants</p>
    <p><a class="btn btn-primary btn-lg" href="addrestaurant.php">Add another restaurant</a>
        <a class="btn btn-primary btn-lg" href="index.php">Return to Homepage</a></p>
</div>
<?php
} catch (PDOException $exception){
    echo "PDO error :" . $exception->getMessage();
}
?>



</body>