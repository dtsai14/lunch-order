<div class="container">
<?php include 'header.php';
?>
<ul class="nav nav-pills">
    <li><a href="#admin" data-toggle="tab">Admin</a></li>
    <li><a href="#lunchorder" data-toggle="tab">Lunch Order</a></li>
</ul>
    <ul class="nav navbar-nav navbar-right">
        <button class="btn btn-primary navbar-btn logout-button" type="submit">Logout</button>
    </ul>

<div class="tab-content">
    <div class="tab-pane active" id="admin">
        <?php include 'index.php';
        ?>
    </div>
    <div class="tab-pane" id="lunchorder">
        <?php include 'lunchorder.php';
        ?>
    </div>
</div>
</div>