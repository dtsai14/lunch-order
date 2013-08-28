<?php
include 'authenticate.php';
include 'mysql.php';
?>
<!DOCTYPE html>
<head lang="en">
    <?php
    include 'bootstrapSources.php';
    ?></head>
<body>

<div class="container">
    <div class="page-header">
        <h1>Good Morning, <?php echo $_SESSION['firstname'] ?>!</h1>
    </div>

    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">LunchMaster</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/lunchorder/lunchorder.php">Lunch Order</a></li>
                <li class="active"><a href="#">Admin Page</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <a class="btn btn-primary navbar-btn" href="/lunchorder/logout.php">Logout</a>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>
    <table id="lunch-options" class="table table-hover table-striped">
        <thead>
        <tr>
            <th></th>
            <th>Restaurant</th>
            <th>Food Type</th>
            <th>Menu</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach (getRestaurants() as $restaurant) { ?>
            <tr id="<?= $restaurant['id'] ?>">
            <td><button data-restaurant-id="<?= $restaurant['id']?>" class="delete-button btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button></td>
            <td><?= $restaurant['name'] ?></td>
            <td><?= $restaurant['food_type']?></td>
            <td class="restaurant-actions" data-menu-url="<?= $restaurant['menu_url'] ?>">
                <button class="view-menu-button btn btn-info btn-xs" data-menu-url="<?=  $restaurant['menu_url']?>">View Menu</button>
                <button class="add-menu-button btn btn-default btn-xs" data-restaurant-id="<?= $restaurant['id']?>">Add Menu</button>
                <button class="take-orders-button btn btn-success btn-xs">Take Orders</button>
            </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Button trigger modal -->
    <a data-toggle="modal" href="#add_restaurant" class="btn btn-primary btn-sm">Add Restaurant</a>

    <!-- Modal -->
    <div class="modal fade" id="add_restaurant" tabindex="-1" role="dialog" aria-labelledby="addRestaurant" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add Restaurant</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="restaurant" enctype="multipart/form-data">
                        <fieldset>
                            <div class="form-group">
                                <label for="restaurantName">Restaurant Name</label>
                                <input type="text" class="form-control" id="restaurantName" name="restaurantName" placeholder="Restaurant Name" required>
                            </div>
                            <div class="form-group">
                                <label for="restaurantType">Restaurant Type</label>
                                <input type="text" class="form-control" id="restaurantType" name="restaurantType" placeholder="Restaurant Type" required/>
                            </div>
                            <div class="form-group">
                                <label for="menuInput">Menu</label>
                                <input type="file" name="menuInputFile" id="menuInputFile">
                                <br>Or enter URL:<br>
                                <input type="url" name="menuInputURL" id="menuInputURL">
                            </div>
                        </fieldset>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <br><br><br>
</div>


<?php include "menuModal.php";
?>
<script type="text/javascript" src="index.js"></script>
</body>