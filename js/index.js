$(afterThePageLoads);

function afterThePageLoads() {
    $('#voting-container').hide();
    $('#ordering-container').hide();
    var takingOrders;
    var voteData = {};
    var intervalId;
    var activeRestaurantsData = {};
    var ordersData = {};
    checkSwap();

    /* check to see if the ordering form needs to be swapped our for the voting
     * form or vice versa every 3 seconds */
    setInterval(function () {
        checkSwap();
    }, 3000);

    /* checks to see if restaurants have already been opened and closed today,
    * and displays an alert telling users if they have already been closed */
    function alertOrdersClosed() {
        $.ajax({
            url: "./api.php",
            type: 'POST',
            data: {'cmd': "getClosedRestaurants"},
            success: function (data) {
                data = JSON.parse(data);
                if (data['closedRestaurants'].length > 0) {
                    var source = $("#orders-closed-alert-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#orders-closed-alert').html(html);
                }
            }
        })
    }

    /* checks and updates vote table */
    function refreshVotes() {
        $.ajax({
            url: "./api.php",
            type: 'POST',
            data: {'cmd': 'getVotes'},
            success: function(data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,voteData)) {
                    var source = $("#vote-table-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    voteData = data;

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

    /* if user has not voted yet today, records user's vote; otherwise, doesn't
    * let user vote. Displays alert telling user if his vote was recorded or not */
    $("#voting-form").submit(function () {
        $.ajax({
            url: "./api.php",
            data: {'cmd': 'sendVote',
                'restaurant_id': $('input[name=vote]:checked').val()},
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

    /* checks for and updates restaurant dropdown and "Today" alerts with any
    changes in active restaurants */
    function refreshActiveRestaurants() {
        $.ajax({
            url: "./api.php",
            type: "POST",
            data: {'cmd': 'getActiveRestaurants'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,activeRestaurantsData)) {
                    var dropdownSource = $("#restaurant-dropdown-template").html();
                    var dropdownTemplate = Handlebars.compile(dropdownSource);
                    var dropdownHtml = dropdownTemplate(data);
                    var restaurantChoice = $('#restaurant-dropdown').val();
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

    /* when order form is submitted, order is sent to database, and order
    * list is refreshed */
    $("#order-form").submit(function () {
        $.ajax({
                url: "./api.php",
                type: "POST",
                data: {'cmd': 'sendOrder', 'restaurant_id': $('#restaurant-dropdown').val(),
                    'order': $('#order').val()},
                success: function () {
                    $('#order').val("");
                    refreshOrders();
                }
            });
        return false;
    });

    /* checks for and updates order list with any new orders*/
    function refreshOrders() {
        $.ajax({
            url: "./api.php",
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

    /* checks to see if any restaurants are currently active, and if the voting
    * form needs to be switched for ordering form, or vice versa; performs
     * needed switch */
    function checkSwap() {
        $.ajax({
            url: "./api.php",
            type: 'POST',
            data: {'cmd': "getActiveRestaurants"},
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










