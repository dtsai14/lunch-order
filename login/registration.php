<?php
include 'redirectHome.php';
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <title>Registration</title>
    <?php
    include '../sources.php';
    ?>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Welcome!  <small>Please create an account here.</small></h1>
            <br>
            If you already have an account, log in <a href="./login.php">here</a>.
        </div>
        <form role="form" id="registration-form">
            <fieldset>
                <legend>Registration</legend>
                <div class="form-group">
                    <label for="first-name">Name</label>
                    <input type="text" class="input-small" name="first-name" id="first-name" placeholder="First" required>
                    <input type="text" class="input-small" name="last-name" id="last-name" placeholder="Last" required>
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
                    <label for="password">Confirm Password</label>
                    <input type="password" class="input-small" name="password-confirm" id="password-confirm" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="input-small" name="email" id="email" required>
                </div>
                <button type="submit" class="btn btn-default" id="register-button">Register!</button>
            </fieldset>
        </form>
        <br>
        <div id="register-alert"></div>
    </div>

<script id="register-alert-template" type="text/x-handlebars-template">
    {{#alert}}
    <div class='alert alert-{{type}}' alert-dismissable'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        {{{text}}}
        </div>
    {{/alert}}
</script>

<script src="../js/registration.js"></script>
</body>