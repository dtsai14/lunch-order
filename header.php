<?php include_once 'authenticate.php';
?>

<div class="page-header">
    <h1>Good Morning, <?php echo $_SESSION['firstname'] ?>!</h1>
</div>

<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <a class="navbar-brand" href="#">LunchMaster</a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav nav-pills">
            <li id="lunchorder"><a href="/lunchorder/lunchorder.php">Lunch Order</a></li>
            <li id="admin"><a href="/lunchorder/index.php">Admin Page</a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <button class="btn btn-primary navbar-btn logout-button" type="submit">Logout</button>
        </ul>
    </div>
</nav>

<script src="header.js"></script>