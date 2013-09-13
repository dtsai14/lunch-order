<?php
include '../authenticate.php';
include '../mysql.php';

/* takes the uploaded file or menu url and adds it to database */
if ($_POST['menuInputURL'] || $_FILES['menuInputFile']['name'] ){
    $restaurant_id = $_POST['restaurant_id'];
    if ($_POST['menuInputURL']) {
        $url = $_POST['menuInputURL'];
    } else { //($_FILES['menuInputFile']['name']) {
        $name = sqlGetRestaurantName($restaurant_id);
        $ext = pathinfo($_FILES['menuInputFile']['name'], PATHINFO_EXTENSION);
        $fileName = "../Menus/$name" . "_menu.$ext";
        move_uploaded_file($_FILES['menuInputFile']["tmp_name"], $fileName);
        $url = "./Menus/$name" . "_menu.$ext";
    };

    sqlAddMenu($url, $restaurant_id);
}
