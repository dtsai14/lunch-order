<?php
include 'authenticate.php';
?>
<!DOCTYPE html>
<head lang="en">
    <meta charset="utf-8">
    <?php
    include 'bootstrapSources.php';
    ?>
    <script>
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
</head>
<body>

<div class="navbar">
    <div class="navbar-header">
        <a class="navbar-brand" href="index.php">PaperG Lunch Master</a>
    </div>
</div>

<div class="container">
    <form role="form" action="insertRestaurant.php" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Add New Restaurant</legend>
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

            <button type="submit" class="btn btn-default">Submit</button>
        </fieldset>
    </form>

</div>

</body>