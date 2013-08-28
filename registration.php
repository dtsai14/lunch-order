<?php
include 'redirectHome.php';
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <title>Registration</title>

    <meta charset="utf-8">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Latest compiled and minified JavaScript -->
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/js/bootstrap.min.js"></script>
    <!-- Latest Glyphicons minified CSS -->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css" rel="stylesheet">

</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1>Welcome!  <small>Please create an account here.</small></h1>
            <br>
            If you already have an account, log in <a href="/lunchorder/login.php">here</a>.
        </div>
        <form role="form" action="registerUser.php" method="post">
            <fieldset>
                <legend>Registration</legend>
                <div class="form-group">
                    <label for="firstName">Name</label>
                    <input type="text" class="input-small" name="firstName" id="firstName" placeholder="First" required>
                    <input type="text" class="input-small" name="lastName" id="lastName" placeholder="Last" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="input-small" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="input-small" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="input-small" name="email" id="email" required>
                </div>

                <button type="submit" class="btn btn-default">Register!</button>
            </fieldset>
        </form>
    </div>


</body>