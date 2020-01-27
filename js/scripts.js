

$(document).ready(function() {

    var hash = location.hash;
    if (hash == '#auth') {
        $('#page_tasks').hide();
        $('#page_auth').show();
    } else {
        $('#page_tasks').show();
        $('#page_auth').hide();
    }

    Show_tasks();
    Show_auth();

    $('.authorization_form .login').click(function() {
        var formdata = new FormData();
        var login = $("#login").val();
        formdata.append("login", login);
        var password = $("#password").val();
        formdata.append("password", password);
        $.ajax({
            url: '/send.php?type=authorization',
            data: formdata,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if(data['success']) {
                    $('#auth_results').html(data['success']);
                    $('#auth_botton').html('<button type="button" class="btn btn-success logout" onclick="logout_auth();">Выйти</button>');
                } else if(data['error']) {
                    $('#auth_results').html('<font style="color: red;">' + data['error'] + '</font>');
                }
            }
        });
    });

    $('.add_form .btn-success').click(function() {
        var formdata = new FormData();
        var email = $("#inputEmail").val();
        formdata.append("email", email);
        var name = $("#name").val();
        formdata.append("name", name);
        var descr = $("#descr").val();
        formdata.append("descr", descr);
        $.ajax({
            url: '/send.php?type=add',
            data: formdata,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if(data['success']) {
                    $('#results').html(data['success']);
                    Show_tasks();
                } else if(data['error']) {
                    $('#results').html('<font style="color: red;">' + data['error'] + '</font>');
                }
            }
        });
    });

});

function Show_tasks() {
    var formdata = new FormData();
    var sort = $("#sort").val();
    formdata.append("sort", sort);
    var page = $("#page").val();
    formdata.append("page", page);
    $.ajax({
        url: '/send.php?type=list',
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            if(data['success']) {
                $('#tasks').html(data['success']);
            } else if(data['error']) {
                $('#tasks').html('Error');
            }
        }
    });
}

function Show_auth() {
    var formdata = new FormData();
    $.ajax({
        url: '/send.php?type=check_authorization',
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            if(data['success']) {
                $('#login').hide();
                $('#password').hide();
                $('#auth_results').html(data['success']);
                $('#auth_botton').html('<button type="button" class="btn btn-success logout" onclick="logout_auth();">Выйти</button>');
            }
        }
    });
}

function Edit_task(tid) {
    var formdata = new FormData();
    formdata.append("tid", tid);
    var descr = $('#descr_' + tid).val();
    formdata.append("descr", descr);
    var old_descr = $('#old_descr_' + tid).val();
    formdata.append("old_descr", old_descr);
    if ($("#status_" + tid).is(':checked')) formdata.append("status", "yes");
    else formdata.append("status", "no");
    $.ajax({
        url: '/send.php?type=edit',
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            if(data['success']) {
                Show_tasks();
            } else if(data['error']) {
                $('#tasks').html(data['error']);
            }
        }
    });
}

function Remove_task(tid) {
    var formdata = new FormData();
    formdata.append("tid", tid);
    $.ajax({
        url: '/send.php?type=delete',
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            if(data['success']) {
                Show_tasks();
            } else if(data['error']) {
                $('#tasks').html('Error');
            }
        }
    });
}

function Sort_list(sort) {
    $("#sort").val(sort);
    Show_tasks();
}

function go_links(page) {
    if (page == 'auth') {
        $('#page_tasks').hide();
        $('#page_auth').show();
    } else {
        $('#page_tasks').show();
        $('#page_auth').hide();
    }
}

function logout_auth() {
    var formdata = new FormData();
    $.ajax({
        url: '/send.php?type=logout_authorization',
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            if(data['success']) {
                $('#login').show();
                $('#password').show();
                $('#auth_results').html(data['success']);
                $('#auth_botton').html('<button type="button" class="btn btn-success login">Войти</button>');
            } else if(data['error']) {
                $('#auth_results').html('<font style="color: red;">' + data['error'] + '</font>');
            }
        }
    });
}
