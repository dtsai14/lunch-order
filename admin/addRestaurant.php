<?php
include '../authenticate.php';
include '../mysql.php';

/* adds restaurant name, type, and menu (if given) to the database */

$name = $_POST['restaurantName'];
$type = $_POST['restaurantType'];
if (isset($_POST['phoneNumber'])) {
    $phone = $_POST['phoneNumber'];
} else {
    $phone = "";
}

if ($_POST['menuInputURL']) {
    $url = $_POST['menuInputURL'];
} else if ($_FILES['menuInputFile']['name']) {
    print_r($_FILES['menuInputFile']);
    $ext = pathinfo($_FILES['menuInputFile']['name'], PATHINFO_EXTENSION);
    $fileName = "../Menus/$name"."_menu.$ext";
    move_uploaded_file($_FILES['menuInputFile']["tmp_name"], $fileName);
    $url = "./Menus/$name"."_menu.$ext";
} else {
    $url = "";
}

sqlAddRestaurant($name, $type, $url, $phone);
