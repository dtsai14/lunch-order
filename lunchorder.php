<?php
include_once 'authenticate.php';
include_once 'mysql.php';
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <title>Lunch Task Manager</title>
    <?php include_once 'bootstrapSources.php';
    ?>
</head>
<body>
<?php
date_default_timezone_set('America/Los_Angeles');
?>

<div class="container">
    <?php include "header.php";
    ?>

    <div id="voting_container">
        <span id="vote_alert"></span>
        <h3>Which restaurant would you like the office to order from today?</h3>
        <form id="voting_form" role="form">
            <table id="voting" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th style="width: 5%">Vote</th>
                    <th style="width: 20%">Restaurant Name</th>
                    <th>Current Votes</th>
                </tr>
                </thead>
                <tbody id="htmlTable">
                </tbody>
            </table>
            <button id="send_vote" type="submit" class="btn btn-default">Vote</button>
            <br><br>
        </form>
    </div>

    <div id="ordering_container">
        <!--<div class="alert alert-dismissable alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Voting has closed for today!</strong> Please vote again tomorrow :)
        </div> -->
        <div>
            <h2>Today</h2>
            <div id="restaurant-alerts"></div>
        </div>

        <br>
        <!--<div id="restaurantOfDay">
            <h2>Menu(s): </h2>
            <h3><ul class="list-inline" id="restaurant-list">
                    </ul>
                </h3>
        </div> -->
        <br>
        <form role="form" id="order_form">
            <div class="form-group">
                <label for="restaurant">Restaurant</label>
                <select id="restaurant-dropdown" class="form-control" required>
                </select>
            </div>
            <div class="form-group">
                <label for="order">Order</label>
                <textarea name="order" class="form-control" id="order" maxlength="1000" cols="20" rows="6"
                          required></textarea>
            </div>
            <button id="send_order" class="btn btn-primary">Submit</button>
        </form>

        <br><br>
        <h2>Orders <span class="pull-right"><small><?= date('l, F d, Y') ?></small></span></h2>
        <div id="flash"></div>
    </div>


</div>

<script src="lunchorder.js"></script>
</body>
</html>