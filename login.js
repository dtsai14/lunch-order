function afterThePageLoads() {
    $('form#login').submit(function() {
        $.ajax({
            url: "loginUser.php",
            type: 'POST',
            data: {'username': $('#username').val(),
            'password': $('#password').val()},
            success: function(data) {
                data = JSON.parse(data);
                console.log(data);
                if (data == "") {
                    window.location.href="./lunchorder.php";
                } else {
                    $('#alert').html(data);
                }
            }
        });
        return false;
    })
}

$(afterThePageLoads);