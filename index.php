<?php
include_once './authenticate.php';
include_once './mysql.php';
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <title>Lunch Task Manager</title>
    <?php include_once './sources.php';
    ?>
</head>
<body>
<div class="container">
    <?php include "./header/header.php";
    ?>
    <div id="voting-container">
        <div id="orders-closed-alert"></div>
        <span id="vote-alert"></span>
        <h3>Which restaurant would you like the office to order from today?</h3>
        <form id="voting-form" role="form">
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
            <button id="send-vote" type="submit" class="btn btn-default">Vote</button>
            <br><br>
        </form>
    </div>

    <div id="ordering-container">
        <!--<div class="alert alert-dismissable alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Voting has closed for today!</strong> Please vote again tomorrow :)
            </div> -->
        <div>
            <h2>Today</h2>

            <div id="restaurant-alerts"></div>
        </div>
        <br>

        <h2>Place your order:</h2>
        <form role="form" id="order-form">
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
            <button id="send-order" class="btn btn-primary">Submit</button>
        </form>

        <br><br>

        <h2>Orders <span class="pull-right"><small><?= date('l, F d, Y') ?></small></span></h2>
        <div id="order-list"></div>
    </div>
</div>


<script id="vote-alert-template" type="text/x-handlebars-template">
    {{#voteAlert}}
    <div class='alert alert-{{type}} alert-dismissable'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        {{text}}
    </div>
    {{/voteAlert}}
</script>

<script id="vote-table-template" type="text/x-handlebars-template">
    {{#restaurantVotes}}
        <tr>
            <td>
                <div class='radio'>
                    <input type='radio' name='vote' id='{{id}}' value='{{id}}' required>
                </div>
            </td>
            <td>
                {{#if menu_url}}
                <a href='{{menu_url}}' target='_blank'>{{name}}</a>
                {{else}}
                {{name}}
                {{/if}}
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
       <div class='panel panel-default order-actions' data-auth-edit='{{auth_edit}}' data-auth-reject='{{auth_reject}}' data-rejection-id='{{rejection_id}}' id="{{order_id}}">
            <div class='panel-heading'>
                <button class='delete-order-button btn btn-danger btn-xs' data-order-id='{{order_id}}'>
                    <span class='glyphicon glyphicon-remove'></span>
                </button>
                <h3 style="display:inline" class='panel-title'>{{username}}'s order from {{restaurant_name}}
                    <span class='badge pull-right'>{{creation_time}}</span>
                </h3>
            </div>
            <div class='panel-body order-panel'><span class="order-text">{{text}}</span>
                <button class='reject-order-button btn btn-danger btn-xs pull-right' data-order-id='{{order_id}}'>Reject</button>
                <button class='accept-order-button btn btn-info btn-xs pull-right' data-order-id='{{order_id}}'>Accept</button>
                <button class='edit-order-button btn btn-primary btn-xs pull-right' data-order-id='{{order_id}}'>Edit Order</button>
            </div>
           <div class='panel-body edit-panel'>
               <textarea class="form-control" rows="2">{{text}}</textarea>
               <br>
               <button class='save-changes-button btn btn-info btn-xs' data-order-id='{{order_id}}'>Save Changes</button>
               <button class='cancel-changes-button btn btn-default btn-xs' data-order-id='{{order_id}}'>Cancel</button>
               <br>
           </div>
           <div class='panel-footer reject-panel'>
               <form role="form" class="reject-message-form" data-order-id='{{order_id}}'>
                   <div class="form-group">
                       <label for="reject-message">Reject {{username}}'s order:</label>
                       <textarea class="form-control reject-message" rows="2" placeholder="Please enter a message to the user." required></textarea>
                   </div>
                   <button type="submit" class='save-changes-button btn btn-info btn-xs' data-order-id='{{order_id}}'>Send</button>
                   <button class='cancel-reject-button btn btn-default btn-xs' data-order-id='{{order_id}}'>Cancel</button>
               </form>
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



<script src="./js/index.js"></script>
</body>
</html>