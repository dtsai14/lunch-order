function afterThePageLoads() {
    $('form#login').submit(function() {
        $.ajax({
            url: "./loginUser.php",
            type: 'POST',
            data: {'username': $('#username').val(),
            'password': $('#password').val()},
            success: function(data) {
                data = JSON.parse(data);
                if (data == "") {
                    window.location.href="../index.php";
                } else {
                    $('#alert').html(data);
                }
            }
        });
        return false;
    })
}

$(afterThePageLoads);