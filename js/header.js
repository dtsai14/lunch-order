function afterThePageLoads() {
    $('.alert').hide();
    updateGreeting();
    alertQuote();

    $('.logout-button').click(function() {
        $.ajax({
            url: "./header/logout.php",
            success: function() {
                window.location.reload(true);
            }
        })
    });

    setInterval(function () {
        updateGreeting();
    }, 3000);

    function alertQuote() {
        $.ajax({
            url: "./header/greeting.php",
            success: function(data) {
                data = JSON.parse(data);
                if (data['displayQuote']) {
                    $('#quote').html(data['quote']);
                    $('#person').html(data['person']);
                    $('.alert').show();
                }
            }
        })
    }

    function updateGreeting() {
        $.ajax({
            url: "./header/greeting.php",
            success: function(data) {
                data = JSON.parse(data);
                $('#greeting').html(data['greeting']);
            },
            error: function(args) {
                console.log(args);
            }
        })
    }

    $('#quote-alert').bind('close.bs.alert', function() {
        $.ajax({
            url: "./header/closeAlert.php",
            success: function(data) {
                console.log("display quote =");
                console.log(data);
                console.log("won't display quote anymore!");
            }
        })
    })
}

$(afterThePageLoads);