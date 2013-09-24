$(afterThePageLoads);

function afterThePageLoads() {
    $('#voting-container').hide();
    $('#ordering-container').hide();
    var takingOrders;
    var voteData = {};
    var intervalId;
    var activeRestaurantsData = {};
    var ordersData = {};
    var rejectionData = {};
    var rejectedOrderNotifications = {};
    var changedOrderNotifications = {};
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
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getClosedRestaurants'},
            success: function (data) {
                data = JSON.parse(data);
                if (data['closedRestaurants'].length > 0) {
                    var source = $('#orders-closed-alert-template').html();
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
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getVotes'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,voteData)) {
                    var source = $('#vote-table-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    voteData = data;

                    var selected = $('input[name=vote]:checked').val();
                    $('#vote-table').html(html);
                    $('.radio #' + selected).prop('checked', true);
                    console.log('votes refreshed!');
                } else {
                    console.log('no new votes to update');
                }
            }
        })
    }

    /* if user has not voted yet today, records user's vote; otherwise, doesn't
    * let user vote. Displays alert telling user if his vote was recorded or not */
    $('#voting-form').submit(function () {
        $.ajax({
            url: './api.php',
            data: {'cmd': 'sendVote',
                'restaurant_id': $('input[name=vote]:checked').val()},
            type: 'POST',
            success: function (data) {
                data = JSON.parse(data);
                if ('error' in data) {
                    console.log(data['error']);
                } else {
                    var source = $('#vote-alert-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#vote-alert').html(html);
                }
                $('input[name=vote]:checked').attr('checked', false);
                refreshVotes();
            },
            error: function () {
                console.log('ERROR', arguments);
            }
        });
        return false;
    });

    /* checks for and updates restaurant dropdown and 'Today' alerts with any
    changes in active restaurants */
    function refreshActiveRestaurants() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getActiveRestaurants'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,activeRestaurantsData)) {
                    var dropdownSource = $('#restaurant-dropdown-template').html();
                    var dropdownTemplate = Handlebars.compile(dropdownSource);
                    var dropdownHtml = dropdownTemplate(data);
                    var restaurantChoice = $('#restaurant-dropdown').val();
                    $('#restaurant-dropdown').html(dropdownHtml);
                    $('#restaurant-dropdown').val(restaurantChoice);

                    var alertsSource = $('#restaurant-alerts-template').html();
                    var alertsTemplate = Handlebars.compile(alertsSource);
                    var alertsHtml = alertsTemplate(data);
                    $('#restaurant-alerts').html(alertsHtml);
                    activeRestaurantsData = data;
                    console.log('active restaurants updated!')
                } else {
                    console.log('no changes in active restaurants!')
                }
            }
        })
    }

    /* when order form is submitted, order is sent to database, and order
    * list is refreshed */
    $('#order-form').submit(function () {
        $('#send-order').attr('disabled', true);

        $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'cmd': 'sendOrder', 'restaurant_id': $('#restaurant-dropdown').val(),
                    'order': $('#order').val()},
                success: function () {
                    refreshOrders();
                    $('#send-order').attr('disabled', false);
                    $('#order').val('');
                }
            });
        return false;
    });

    /* checks for and updates order list with any new orders*/
    function refreshOrders() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'refreshOrders'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,ordersData)) {
                    var source = $('#order-list-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#order-list').html(html);
                    ordersData = data;
                    $(afterOrdersLoad());
                    console.log("orders loaded!");
                }
            },
            error: function () {
                console.log('ERROR', arguments);
            }
        });
    }

    /* applies formatting to orders and checks for alerts for users */
    function afterOrdersLoad() {
        $(userEditing());
        $(adminRejecting());
        $(checkRejection());
        $(checkRejectedChanges());
    }

    function checkRejection() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getRejectedOrders'},
            success: function (data) {
                data = JSON.parse(data);
                console.log("just checked rejection!");
                console.log(rejectionData);
                console.log(data['rejectionIds']);
                if (!_.isEqual(data['rejectionIds'], _.keys(rejectedOrderNotifications))) {
                    var acceptedRejectionIds = _.difference(_.keys(rejectedOrderNotifications), data['rejectionIds']);
                    var newRejectionIds = _.difference(data['rejectionIds'], _.keys(rejectedOrderNotifications));
                    var rejectedOrders = data['rejectedOrders'];
                    acceptedRejectionIds.forEach(function (element, index, array) {
                        rejectedOrderNotifications[element].pnotify_remove();
                        delete rejectedOrderNotifications[element];
                    });
                    newRejectionIds.forEach(function (element, index, array) {
                        var rejection = rejectedOrders[element];
                        var notice = $.pnotify({
                            title: 'Notice',
                            text: "<strong>" + rejection['admin'] + "</strong> just sent back your order for <strong>" + rejection['restaurant_name']
                                + "</strong><br><strong>Your Order:</strong><br> " + rejection['text'] + "<br><strong>Message:</strong><br> " + rejection['message'],
                            type: 'error',
                            nonblock: true,
                            hide: false,
                            /*before_close: function (pnotify) {
                                pnotify.pnotify({
                                    title: 'Order Accepted!',
                                    text: "<strong>" + rejection['admin'] + "</strong> has accepted your order for <strong>" + rejection['restaurant_name'] + "</strong>!",
                                    type: 'success',
                                    before_close: null
                                });
                                pnotify.pnotify_queue_remove();
                                //pnotify.effect('bounce');
                                return false;
                            }*/
                        });
                        rejectedOrderNotifications[element] = notice;
                    });
                }
            }
        })
    }

    function checkRejectedChanges() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'checkRejectedChanges'},
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data['rejectionIds'], _.keys(changedOrderNotifications))) {
                    var acceptedChangeIds = _.difference(_.keys(changedOrderNotifications), data['rejectionIds']);
                    var newChangeIds = _.difference(data['rejectionIds'], _.keys(changedOrderNotifications));
                    var changedOrders = data['changedRejections'];
                    acceptedChangeIds.forEach(function (element, index, array) {
                        changedOrderNotifications[element].pnotify_remove();
                        delete changedOrderNotifications[element];
                    });
                    newChangeIds.forEach(function (element, index, array) {
                        var changedOrder = changedOrders[element];
                        var notice = $.pnotify({
                            title: 'Notice',
                            text: "<strong>" + changedOrder['username'] + "</strong> has made changes to his/her order for <strong>" + changedOrder['restaurant_name']
                                + "</strong><br>Please review the order and accept it",
                            nonblock: true,
                            hide: false,
                            closer: false,
                            sticker: false
                        });
                        changedOrderNotifications[element] = notice;
                    });
                }
            }
        })
    }

    /* applies formatting to allow admin to reject users' orders */
    function adminRejecting() {
        $('.reject-order-button').hide(); // hide all reject and accept order
        $('.accept-order-button').hide(); // buttons, and reject panels
        $('.reject-panel').hide();

        /* makes rejected order panels red, and shows either the accept order
        button or reject order button for those who are authorized to reject
        * orders (the admin who took the order for this restaurant) */
        $('.order-actions').each(function () {
            console.log($(this).data('rejection-id'));
            if ($(this).data('rejection-id')) {
                $(this).removeClass('panel-default');
                $(this).addClass('panel-warning');
                if ($(this).data('auth-reject')) {
                    $(this).find('.accept-order-button').show();
                }
            } else {
                if ($(this).data('auth-reject')) {
                    $(this).find('.reject-order-button').show();
                }
            }
        });

        /* on click, shows panel where admin can send user a reject message*/
        $('.reject-order-button').click(function () {
            var orderId = $(this).data('order-id');
            $('#' + orderId + ' .reject-panel').show();
            $(this).hide();
        });

        /* closes reject panel without sending reject message*/
        $('.cancel-reject-button').click(function () {
            var orderId = $(this).data('order-id');
            $('#' + orderId + ' .reject-panel').hide();
            $('#' + orderId + ' .reject-message').val("");
            $('#' + orderId + ' .reject-order-button').show();
        });

        /* submits reject message to database*/
        $('.reject-message-form').submit(function () {
            var orderId = $(this).data('order-id');
            var rejectMessage = $('#' + orderId + ' textarea.reject-message').val();
            $('#' + orderId + ' .reject-panel').hide();
            $('#' + orderId + ' .accept-order-button').show();
            $('#' + orderId).removeClass('panel-default');
            $('#' + orderId).addClass('panel-warning');
            console.log("changed color!");

            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'cmd': 'rejectOrder', 'order_id': orderId, 'reject_message': rejectMessage}
            });
            return false;
        });

        $('.accept-order-button').click(function () {
            var orderId = $(this).data('order-id');
            $(this).hide();
            $('#' + orderId + ' .reject-order-button').show();
            var rejectionId = $('#' + orderId).data('rejection-id');
            $('#' + orderId).removeClass('panel-warning');
            $('#' + orderId).addClass('panel-default');
            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'cmd': 'acceptOrder', 'rejection_id': rejectionId}
            })
        })
    }

    /* applies formatting to allow user to edit his/her own order */
    function userEditing() {
        $('.delete-order-button').hide(); // hide all delete order and edit
        $('.edit-order-button').hide();   // order buttons, and edit panels
        $('.edit-panel').hide();

        /* show delete and edit order buttons if user is authorized to edit
         * the order (the user sent this order) */
        $('.order-actions').each(function () {
            if ($(this).data('auth-edit')) {
                $(this).find('.delete-order-button').show();
                $(this).find('.edit-order-button').show();
            }
        });

        /* deletes order from database on click */
        $('.delete-order-button').click(function () {
            var orderId = $(this).data('order-id');
            $('#' + orderId).hide();

            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'cmd': 'deleteOrder', 'order_id': orderId}
            })
        });

        /* allows user to see the edit order panel */
        $('.edit-order-button').click(function () {
            var orderId = $(this).data('order-id');
            $('#' + orderId + ' .order-panel').hide();
            $('#' + orderId + ' .edit-panel').show();
        });

        /* user doesn't make any changes to order */
        $('.cancel-changes-button').click(function () {
            var orderId = $(this).data('order-id');
            var originalOrder = $('#' + orderId + ' .order-text').text();
            $('#' + orderId + ' .edit-panel').hide();
            $('#' + orderId + ' .order-panel').show();
            $('#' + orderId + ' .form-control').val(originalOrder);
        });

        /* any changes user has made to order are saved to database */
        $('.save-changes-button').click(function () {
            var orderId = $(this).data('order-id');
            var originalOrder = $('#' + orderId + ' .order-text').text();
            var editedOrder = $('#' + orderId + ' .form-control').val();
            var rejectionId = $('#' + orderId).data('rejection-id');
            $('#' + orderId + ' .order-text').text(editedOrder);
            $('#' + orderId + ' .edit-panel').hide();
            $('#' + orderId + ' .order-panel').show();
            if (editedOrder != originalOrder) {
                $.ajax({
                    url: './api.php',
                    type: 'POST',
                    data: {'cmd': 'changeOrder', 'order_id': orderId, 'edited_order': editedOrder, 'rejection_id': rejectionId},
                    success: function() {
                        console.log("order has been updated in database!");
                    }
                })
            }
        });
    }

    /* checks to see if any restaurants are currently active, and if the voting
    * form needs to be switched for ordering form, or vice versa; performs
     * needed switch */
    function checkSwap() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getActiveRestaurants'},
            success: function (data) {
                data = JSON.parse(data);
                var activeRestaurants = data['activeRestaurants'];
                var takingOrdersCheck = activeRestaurants.length > 0;
                if (takingOrdersCheck != takingOrders){
                    clearInterval(intervalId);
                    takingOrders = takingOrdersCheck;
                    if (takingOrders) {
                        console.log('changed to taking orders!');
                        refreshActiveRestaurants();
                        refreshOrders();
                        $('#ordering-container').show();
                        $('#voting-container').hide();

                        intervalId = setInterval(function () {
                            refreshActiveRestaurants();
                            refreshOrders();
                        }, 3000);
                    } else {
                        console.log('changed to voting!');
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
            error: function () {
                console.log('ERROR', arguments);
            }
        });
    }
}










