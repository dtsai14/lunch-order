<?php
include 'redirectHome.php';
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <title>Registration</title>
    <?php
    include 'sources.php';
    ?>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Welcome!  <small>Please create an account here.</small></h1>
            <br>
            If you already have an account, log in <a href="./login.php">here</a>.
        </div>
        <form role="form" id="registration-form" action="registerUser.php" method="post">
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
        <br>
        <div id="alert"></div>
    </div>
<script src="registration.js"></script>
</body>