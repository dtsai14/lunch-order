<?php
include_once 'authenticate.php';
include_once 'mysql.php';
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <title>Lunch Task Manager</title>
    <?php include_once 'sources.php';
    ?>
</head>
<body>
<?php
date_default_timezone_set('America/Los_Angeles');
?>

<div class="container">
    <?php include "header.php";
    ?>
    <div id="orders-closed-alert"></div>
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
                <tbody id="vote-table">
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
        <div id="order-list"></div>
    </div>


</div>

<script id="vote-table-template" type="text/x-handlebars-template">
    {{#restaurantVotes}}
        <tr>
            <td>
                <div class='radio'>
                    <input type='radio' name='vote' id='{{id}}' value='{{id}}' required>
                </div>
            </td>
            <td>
                <a href='{{menu_url}}' target='_blank'>{{name}}</a>
            </td>
            <td>
                <div>
                    <span class='badge pull-left'>{{num_votes}}</span>
                    <div class='progress progress-striped'>
                        <div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='{{num_votes}}' aria-valuemin='0' aria-valuemax='{{num_users}}' style='width:{{vote_bar}}%'></div>
                    </div>
                </div>
            </td>
        </tr>
    {{/restaurantVotes}}
</script>

<script id="restaurant-dropdown-template" type="text/x-handlebars-template">
    {{#activeRestaurants}}
        <option value="{{id}}">{{name}}</option>
    {{/activeRestaurants}}
</script>

<script id="restaurant-alerts-template" type="text/x-handlebars-template">
    {{#activeRestaurants}}
    <div class='alert alert-warning'>
        <strong>{{username}}</strong> is currently taking orders for {{name}}.
        {{#if menu_url}}
            View menu <a href='{{menu_url}}' target='_blank' class='alert-link'>here</a>.
        {{else}}
            No menu has been uploaded; please see <strong>{{username}}</strong> for menu.
        {{/if}}
    </div>
    {{/activeRestaurants}}
</script>

<script id="order-list-template" type="text/x-handlebars-template">
    {{#orders}}
       <div class='panel panel-default'>
            <div class='panel-heading'>
                <h3 class='panel-title'>{{username}}'s order from {{restaurant_name}}
                    <span class='badge pull-right'>{{creation_time}}</span>
                </h3>
            </div>
            <div class='panel-body'>{{text}}
            </div>
        </div>
    {{/orders}}
</script>

<script id="orders-closed-alert-template" type="text/x-handlebars-template">
    <div class='alert alert-danger'>
        Orders have already been taken and closed today for:
        <ul>
        {{#each closedRestaurants}}
            <li>{{this}}</li>
        {{/each}}
        </ul>
        Please check with your admin to see if orders will be opened again today.
    </div>
</script>

<script src="./lunchorder.js"></script>
</body>
</html>