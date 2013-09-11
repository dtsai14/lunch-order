<?php include_once './authenticate.php';
?>
<!-- Modal -->
<div class="modal fade" id="add_restaurant" tabindex="-1" role="dialog" aria-labelledby="addRestaurant" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="addRestaurant">Add Restaurant</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form" id="restaurant" enctype="multipart/form-data">
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
                        <button type="button" class="btn btn-default close-modal" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
