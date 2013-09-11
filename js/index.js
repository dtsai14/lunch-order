$(afterThePageLoads);

function afterThePageLoads() {
    $('#voting-container').hide();
    $('#ordering-container').hide();
    var takingOrders;
    var voteTableData = {};
    var intervalId;
    var activeRestaurantsData = {};
    var ordersData = {};
    checkSwap();

    setInterval(function () {
        checkSwap();
    }, 3000);

    $("#voting-form").submit(function () {
        $.ajax({
            url: "./main/mainApi.php",
            data: {'cmd': 'sendVote', 'restaurant_id': $('input[name=vote]:checked').val()},
            type: "POST",
            success: function (data) {
                data = JSON.parse(data);
                if ("error" in data) {
                    console.log(data['error']);
                } else {
                    var source = $("#vote-alert-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#vote-alert').html(html);
                }
                $('input[name=vote]:checked').attr('checked', false);
                refreshVotes();
            },
            error: function () {
                console.log("ERROR", arguments);
            }
        });
        return false;
    });

    function alertOrdersClosed() {
        $.ajax({
            url: "./orderInProgress.php",
            type: 'POST',
            data: {'action': "get_closed_restaurants"},
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                if (data['closedRestaurants'].length > 0) {
                    var source = $("#orders-closed-alert-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#orders-closed-alert').html(html);
                }
            }
        })
    }

    function refreshVotes() {
        $.ajax({
            url: "./main/voteTable.php",
            success: function(data) {
                data = JSON.parse(data);
                console.log(data);
                if (!_.isEqual(data,voteTableData)) {
                    var source = $("#vote-table-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    voteTableData = data;

                    var selected = $('input[name=vote]:checked').val();
                    $("#vote-table").html(html);
                    $('.radio #' + selected).prop('checked', true);
                    console.log("votes refreshed!");
                } else {
                    console.log("no new votes to update");
                }
            }
        })
    }

    // adds the order to the database and refreshes the div to include new orders
    $("#order-form").submit(function () {
        var order = $('#order').val();
        $.ajax({
                url: "./main/mainApi.php",
                type: "POST",
                data: {'cmd': 'sendOrder', 'restaurant_id': $('#restaurant-dropdown').val(),
                    'order': order},
                success: function () {
                    $('#order').val("");
                    refreshOrders();
                }
            });
        return false;
    });

    function refreshActiveRestaurants() {
        var restaurantChoice = $('#restaurant-dropdown').val();
        $.ajax({
            url: "./main/mainApi.php",
            type: "POST",
            data: {'cmd': 'getActiveRestaurants'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,activeRestaurantsData)) {
                    var dropdownSource = $("#restaurant-dropdown-template").html();
                    var dropdownTemplate = Handlebars.compile(dropdownSource);
                    var dropdownHtml = dropdownTemplate(data);
                    $("#restaurant-dropdown").html(dropdownHtml);
                    $("#restaurant-dropdown").val(restaurantChoice);

                    var alertsSource = $('#restaurant-alerts-template').html();
                    var alertsTemplate = Handlebars.compile(alertsSource);
                    var alertsHtml = alertsTemplate(data);
                    $("#restaurant-alerts").html(alertsHtml);
                    activeRestaurantsData = data;
                    console.log("active restaurants updated!")
                } else {
                    console.log("no changes in active restaurants!")
                }
            }
        })
    }

    // accesses database to update div with all current orders
    function refreshOrders() {
        $.ajax({
            url: "./main/mainApi.php",
            type: "POST",
            data: {'cmd': 'refreshOrders'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,ordersData)) {
                    var source = $('#order-list-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $("#order-list").html(html);
                    ordersData = data;
                }
            },
            error: function () {
                console.log("ERROR", arguments);
            }
        });
    }

    function checkSwap() {
        $.ajax({
            url: "./orderInProgress.php",
            type: 'POST',
            data: {'action': "get_active_restaurants"},
            success: function(data) {
                data = JSON.parse(data);
                var activeRestaurants = data['activeRestaurants'];
                var takingOrdersCheck = activeRestaurants.length > 0;
                if (takingOrdersCheck != takingOrders){
                    clearInterval(intervalId);
                    takingOrders = takingOrdersCheck;
                    if (takingOrders) {
                        console.log("changed to taking orders!");
                        refreshActiveRestaurants();
                        refreshOrders();
                        $('#ordering-container').show();
                        $('#voting-container').hide();

                        intervalId = setInterval(function () {
                            refreshActiveRestaurants();
                            refreshOrders();
                        }, 3000);
                    } else {
                        console.log("changed to voting!");
                        alertOrdersClosed();
                        refreshVotes();
                        $('#voting-container').show();
                        $('#ordering-container').hide();

                        intervalId = setInterval(function () {
                            refreshVotes();
                        }, 3000);
                    }
                }
            },
            error: function() {
                console.log("ERROR", arguments);
            }
        });
    }
}










