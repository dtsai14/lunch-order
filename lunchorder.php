<?php
include 'authenticate.php';
include 'mysql.php';
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <title>Lunch Task Manager</title>
    <?php
    include 'bootstrapSources.php';
    ?>
</head>
<body>
<?php
date_default_timezone_set('America/Los_Angeles');
?>

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
                <li class="active"><a href="#">Lunch Order</a></li>
                <li><a href="/lunchorder">Admin Page</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <a class="btn btn-primary navbar-btn" href="/lunchorder/logout.php">Logout</a>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

    <div id="voting_container">
        <h3>Which restaurant would you like the office to order from today?</h3>
        <form id="voting_form" role="form">
            <table id="voting" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th style="width: 5%">Vote</th>
                    <th style="width: 20%">Restaurant Name</th>
                    <th>Current Votes</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach (getRestaurants() as $restaurant) {
                    $numVotes = getNumVotes($restaurant['id']);
                    $numUsers = getNumUsers();
                    $voteBar = $numVotes / $numUsers * 100;
                    ?>
                    <tr>
                        <td><div class='radio'><input type='radio' name='vote' id="<?= $restaurant['id'] ?>" value="<?= $restaurant['id'] ?>" required></td>
                        <td><a href="<?= $restaurant['menu_url'] ?>" target="_blank"> <?= $restaurant['name'] ?> </a></td>
                        <td><div><span class='badge pull-left'><?= $numVotes ?></span><div class='progress progress-striped'><div class='progress-bar progress-bar-info' role='progressbar'aria-valuenow="<?= $numVotes ?>"aria-valuemin='0' aria-valuemax='16' style='width: <?= $voteBar ?>%'></div></div></div></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <button id="send_vote" type="submit" class="btn btn-default">Vote</button>
            <span id="vote_alert"></span>
        </form>
    </div>

    <div id="ordering_container">
        <!--<div class="alert alert-dismissable alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Voting has closed for today!</strong> Please vote again tomorrow :)
        </div> -->
        <div id="restaurantOfDay">
            <h2>Today's menu is from </h2>
        </div>

        <form role="form">
            <div class="form-group">
                <label for="restaurant">Restaurant</label>
                <input type="text" class="form-control" id="restaurant" placeholder="Enter Restaurant">
            </div>
            <div class="form-group">
                <label for="order">Order</label>
                <textarea name="order" class="form-control" id="order" maxlength="1000" cols="20" rows="6"
                          required></textarea>
            </div>
            <button id="send_order" class="btn btn-default">Submit</button>
        </form>

        <h2>Orders <span class="pull-right"><small><?php echo date('l, F d, Y') ?></small></span></h2>
        <div id="flash"></div>
    </div>

 <!--   <button type="button" id="voting-button" class="btn btn-primary">Voting</button>
    <button type="button" id="ordering-button" class="btn btn-primary">Ordering</button> -->
</div>

<script type="text/javascript" src="lunchorder.js"></script>
</body>
</html>