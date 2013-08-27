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
                          "<!-- Button trigger modal -->
                          <a data-toggle=\"modal\" href=\"#myModal\" class=\"btn btn-default btn-xs\">Add Menu</a>

                          <!-- Modal -->
                          <div class=\"modal fade\" id=\"myModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
                            <div class=\"modal-dialog\">
                              <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                  <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
                                  <h4 class=\"modal-title\">Modal title</h4>
                                </div>
                                <div class=\"modal-body\">
                                  ...
                                </div>
                                <div class=\"modal-footer\">
                                  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
                                  <button type=\"button\" class=\"btn btn-primary\">Save changes</button>
                                </div>
                              </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                          </div><!-- /.modal -->") .
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
                    <form role="form" id="restaurant" action="insertRestaurant.php" method="post" enctype="multipart/form-data">
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

    $("form#restaurant").submit(function(){
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "insertRestaurant.php",
            type: 'POST',
            data: formData,
            async: false,
            success: function() {
                $('#add_restaurant').modal('hide');
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    });

    function afterThePageLoads(){
        var menuInputFile = $("#menuInputFile");
        var menuInputURL = $("#menuInputURL");
        menuInputFile.change(function(){
            if(menuInputFile.val()== "") {
                menuInputURL.attr('disabled', false);
            } else {
                menuInputURL.attr('disabled', true);
            }
        })
        menuInputURL.change(function(){
            if(menuInputURL.val()== "") {
                menuInputFile.attr('disabled', false);
            } else {
                menuInputFile.attr('disabled', true);
            }
        })
    }

    $(afterThePageLoads);

</script>
</body>