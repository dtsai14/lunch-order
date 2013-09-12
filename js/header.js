function afterThePageLoads() {
    $('.alert').hide();
    updateGreeting();
    alertQuote();

    $('.logout-button').click(function() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'logout'},
            success: function() {
                window.location.reload(true);
            }
        })
    });

    setInterval(function () {
        updateGreeting();
    }, 3000);

    function updateGreeting() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'getGreeting'},
            success: function(data) {
                data = JSON.parse(data);
                $('#greeting').html(data['greeting']);
            },
            error: function(args) {
                console.log(args);
            }
        })
    }

    function alertQuote() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'getQuote'},
            success: function(data) {
                data = JSON.parse(data);
                if (data['display_quote']) {
                    $('#quote').html(data['quote']);
                    $('#person').html(data['person']);
                    $('.alert').show();
                }
            }
        })
    }

    $('#quote-alert').bind('close.bs.alert', function() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'closeAlert'},
            success: function(data) {
                console.log("display quote =");
                console.log(data);
            }
        })
    })
}

$(afterThePageLoads);