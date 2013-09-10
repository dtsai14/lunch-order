<?php include_once 'authenticate.php';
?>

<div class="page-header">
    <h1 id="greeting"></h1>
</div>

<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <a class="navbar-brand" href="#">LunchMaster</a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav nav-pills">
            <li id="lunchorder"><a href="./lunchorder.php">Lunch Order</a></li>
            <li id="admin"><a href="./index.php">Admin Page</a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <!--<button class="btn btn-info navbar-btn add-quote-button" type="submit">Add a Quote!</button> -->
            <button class="btn btn-primary navbar-btn logout-button" type="submit">Logout</button>
        </ul>
    </div>
</nav>
<div class="alert alert-success alert-dismissable" id="quote-alert">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h3>Quote of the Day:</h3> <blockquote><p><strong><span id="quote"></span></strong> <em><span id="person"></span></em>
        </p>
    </blockquote>
</div>

<script src="header.js"></script>