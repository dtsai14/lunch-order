<?php
include '../authenticate.php';
include '../mysql.php';

if ($_POST['menuInputURL'] || $_FILES['menuInputFile']['name'] ){
    $restaurant_id = $_POST['restaurant_id'];
    if ($_POST['menuInputURL']) {
        $url = $_POST['menuInputURL'];
    } else { //($_FILES['menuInputFile']['name']) {
        $restaurant = $pdo->query("SELECT * FROM restaurants WHERE id= $restaurant_id");
        $name = $restaurant->fetch()['name'];
        $ext = pathinfo($_FILES['menuInputFile']['name'], PATHINFO_EXTENSION);
        $fileName = "../../Menus/$name" . "_menu.$ext";
        move_uploaded_file($_FILES['menuInputFile']["tmp_name"], $fileName);
        $url = "../Menus/$name" . "_menu.$ext";
    };

    try {
        $statement = $pdo->prepare("UPDATE restaurants SET menu_url=? WHERE id=?");
        $statement->execute(array($url, $restaurant_id));
    } catch (PDOException $e) {
        $error = "PDO error :" . $e->getMessage() . "<br/>";
        echo $error;
    }
}
