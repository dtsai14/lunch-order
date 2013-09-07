function afterThePageLoads() {
    $('#registration-form').submit(function() {
        $.ajax({
            url: "./registerUser.php",
            type: 'POST',
            data: {"firstName": $("#firstName").val(),
            "lastName": $("#lastName").val(),
            "username": $("#username").val(),
            "password": $("#password").val(),
            "email": $("#email").val()},
            success: function(data) {
                data = JSON.parse(data);
                console.log(data);
                $('#alert').html(data);
            }
        })
        return false;
    })
}

$(afterThePageLoads);