<?php
include_once './authenticate.php';
include_once './mysql.php';
?>
<!DOCTYPE html>
<head lang="en">
    <?php include_once './sources.php';
    ?>
</head>
<body>

<div class="container">
    <?php include './header/header.php';
    ?>
    <table id="lunch-options" class="table table-hover table-striped">
        <thead>
        <tr>
            <th></th>
            <th>Restaurant</th>
            <th>Food Type</th>
            <th>Menu</th>
        </tr>
        </thead>
        <tbody id="admin-table">
        </tbody>
    </table>

    <!-- Button trigger modal -->
    <a data-toggle="modal" href="#add_restaurant" class="btn btn-primary btn-sm">Add Restaurant</a>
    <br><br>
    <div class="panel-group" id="taken-orders"></div>
    <br><br>
</div>


<?php include './admin/restaurantModal.php';
include './admin/menuModal.php';
?>

<script id="admin-table-template" type="text/x-handlebars-template">
    {{#restaurants}}
        <tr id='{{id}}'>
            <td>
                <button data-restaurant-id='{{id}}' class='delete-button btn btn-danger btn-xs'>
                    <span class='glyphicon glyphicon-remove'></span>
                </button>
            </td>
            <td>{{name}}</td>
            <td>{{food_type}}</td>
            <td class='restaurant-actions' data-taking-orders='{{taking_orders}}' data-auth-close='{{auth_close}}' data-menu-url='{{menu_url}}'>
            <button class='view-menu-button btn btn-info btn-xs' data-menu-url='{{menu_url}}'>View Menu</button>
            <button class='add-menu-button btn btn-default btn-xs' data-restaurant-id='{{id}}'>Add Menu</button>
            <button class='take-orders-button btn btn-success btn-xs' data-restaurant-id='{{id}}'>Take Orders</button>
            <button class='order-in-progress-button btn btn-success btn-xs' disabled=true>Taking Orders...</button>
            <button class='close-orders-button btn btn-danger btn-xs pull-right' data-restaurant-id='{{id}}'>Close Orders</button>
            </td>
        </tr>
    {{/restaurants}}
</script>

<script id="taken-orders-template" type="text/x-handlebars-template">
    <h3>Orders Taken By You Today</h3>
    {{#takenOrders}}
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <h4 class='panel-title'>
                    <a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion' href='#collapse{{id}}'>{{name}}
                    </a>
                </h4>
            </div>
            <div id='collapse{{id}}' class='panel-collapse collapse in'>
                <div class='panel-body'>
                    <dl class='dl-horizontal'>
                        {{#orders}}
                            <dt>{{first_name}} {{last_name}}</dt>
                            <dd>{{text}}</dd>
                        {{/orders}}
                    </dl>
                </div>
            </div>
        </div>
    {{/takenOrders}}
</script>

<script src="./js/admin.js"></script>
</body>