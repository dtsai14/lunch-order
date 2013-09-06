<?php include_once 'authenticate.php';
?>
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-modal" data-dismiss="modal" data-target="#menuModal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Menu</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form" id="menu" enctype="multipart/form-data">
                    <fieldset>
                        <div class="form-group">
                            <input type="file" name="menuInputFile" id="menuInputFile">
                            <br>Or enter URL:<br>
                            <input type="url" name="menuInputURL" id="menuInputURL">
                        </div>
                    </fieldset>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default close-modal" data-dismiss="modal" data-target="#menuModal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->
