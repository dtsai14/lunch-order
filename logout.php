<?php
include 'authenticate.php';
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <title>Logout</title>

    <?php
    include 'bootstrapSources.php';
    ?>
</head>
<body>
<?php

$_SESSION = array();
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-2592000, '/');
}
session_destroy();
?>
<html>
<head>
    <title>Logout</title>
</head>
<body>

<div class="jumbotron">
    <div class="container">
        <h1>You have been logged out!</h1>
        <p>Click <a href="login.php">here</a> to log back in!</p>
    </div>
</div>

</body>

</html>