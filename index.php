<?php
include 'authenticate.php';
?>
<!DOCTYPE html>
<head lang="en">
    <?php
    include 'bootstrapSources.php';
    ?></head>
<body>

<div class="container">
    <div class="page-header">
        <h1>Good Morning, <?php echo $_SESSION['firstname'] ?></h1>
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

        <?php

        include 'mysql.php';

        try {
            $restaurants = $pdo->query("SELECT * FROM restaurants ORDER BY name");

            foreach ($restaurants->fetchAll() as $row) {
                echo "<tr id=" . $row['id'] . ">";
                echo "<td> <button id='delete' class=\"btn btn-danger btn-xs\" onclick=\"deleteItem(" . $row['id'] . ")\"><span class=\"glyphicon glyphicon-remove\"></span></button></td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['food_type'] . "</td>";
                echo "<td>" . ($row['menu_url'] ? "<button class=\"btn btn-info btn-xs\" onClick=\"window.open('" . $row['menu_url'] . "')\">View Menu</button>" :
                        "<button id='add_menu' class=\"btn btn-default btn-xs\" onClick=alert(\"add menu!\")>Add Menu</button>") .
                        " <button class=\"btn btn-success btn-xs\">Take Orders</button>" . "</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        ?>
        </tbody>
    </table>

    <a class="btn btn-default" href="addrestaurant.php">Add Restaurant</a>
    <br><br><br>
</div>

<script type="text/javascript">
    function deleteItem(restaurant_id) {
        $("#" + restaurant_id).hide();
        $.ajax({
            type: "POST",
            url: "deleteRestaurant.php",
            data: {'restaurant_id' : restaurant_id},
            success: function(data) {
                console.log("SUCCESS!");
            }
        })
    }
</script>
</body>