require('./bootstrap');

/*
 * ITU Project 2019/2020
 * Flight Search (Team xjurig00, xlinka01, xpukan01)
 *
 * Author of this file: Dominik Juriga (xjurig00), Adam Linka (xlinka01),
 *                           Marian Pukancik (xpukan01)
 *
 * */

var pas_count = parseInt($("#pas_count").text());

$(function () {
    $('#datepicker_departure').datepicker({dateFormat: "dd/mm/yy"}).keyup(function (e) {
        if (e.keyCode == 8 || e.keyCode == 46) {
            $.datepicker._clearDate(this);
        }
    });
});

$(function () {
    $('#datepicker_arrival').datepicker({dateFormat: "dd/mm/yy"}).keyup(function (e) {
        if (e.keyCode == 8 || e.keyCode == 46) {
            $.datepicker._clearDate(this);
        }
    });
});

$("#flight_search").submit(function (e) {
    if ($('#adults').val() + $('#children').val() + $('#infants').val() == 0) {
        e.preventDefault();
        alert('You need to choose at least one passenger!');
        return false;
    }
    $(".full").css('display', 'block');
});

$("#feeling_lucky").click(function (e) {
    $("#search_form_to").val(_.sample(['Bratislava', 'Barcelona', 'Prague', 'Paris', 'Vancouver', 'Los Angeles']));
});

$(".flight_seat").click(function () {
    if ($(this).hasClass('newly_reserved')) {
        $(this).removeClass('newly_reserved');
        pas_count++;
    } else {
        if (pas_count > 0) {
            $(this).addClass('newly_reserved');
            pas_count--;
        } else {
            alert('You have reserved all your seats.');
        }
    }
});

$(".passenger_fill").click(function (e) {
    if ($(this).parent().siblings('.sel').children().val() == "-1") {
        alert('Please select a valid value')
        return false;
    }

    const token = $("meta[name=csrf-token]").attr("content")
    const that = $(this)
    const passenger = $(this).parent().siblings('.sel').children().val()

    $.ajax({
        url: 'passenger',
        type: 'GET',
        dataType: 'json',
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        data: {'passenger': passenger},
        ContentType: 'application/json;charset=UTF-8',
        success: function (res) {
            $("#passenger_name" + that.val()).val(res.name)
            $("#passenger_street" + that.val()).val(res.street)
            $("#passenger_city" + that.val()).val(res.city)
            $("#passenger_zip" + that.val()).val(res.zip)
            $("#passenger_state" + that.val()).val(res.state)
        }
    });
});

$(document).ready(function () {
    $("#credentials_button").click(function (e) {
        e.preventDefault()
        $("#credentials_modal").modal('show')
    })
})

$(document).ready(function () {
    $("#credentials_form").submit(function (e) {
        e.preventDefault()
        const url = window.location.pathname;
        const username = url.substring(url.lastIndexOf('/')+1);

        const token = $("meta[name=csrf-token]").attr("content")

        $.ajax({
            url: '/profile/' + username + '/edit',
            type: 'POST',
            dataType: 'json',
            beforeSend: function (request) {
                request.setRequestHeader('x-csrf-token', token);
            },
            data: {
                'name': $(this).find('[name=name]').val(),
                'email': $(this).find('[name=email]').val(),
                'phone_number': $(this).find('[name=phone_number]').val(),
                'preferred_airport': $(this).find('[name=preferred_airport]').val(),
            },
            ContentType: 'application/json;charset=UTF-8',
            success: function (res) {
                $("#user_name").text(res.name)
                $("#user_email").text(res.email)
                $("#user_phone").text(res.phone_number)
                $("#user_preferred").text(res.preferred_airport)
            },
            complete: function() {
                $("#credentials_modal").modal('hide')
                $(".modal-backdrop").remove()
            }
        });
    })
})

$(document).ready(function () {
    $("#address_form").submit(function (e) {
        e.preventDefault();
        const url = window.location.pathname;
        const username = url.substring(url.lastIndexOf('/')+1);

        const token = $("meta[name=csrf-token]").attr("content")

        $.ajax({
            url: '/profile/' + username + '/address',
            type: 'POST',
            dataType: 'json',
            beforeSend: function (request) {
                request.setRequestHeader('x-csrf-token', token);
            },
            data: {
                'street': $(this).find('[name=street]').val(),
                'city': $(this).find('[name=city]').val(),
                'zip': $(this).find('[name=zip]').val(),
                'state': $(this).find('[name=state]').val(),
            },
            ContentType: 'application/json;charset=UTF-8',
            success: function (res) {
                $("#user_street").text(res.street);
                $("#user_city").text(res.city);
                $("#user_zip").text(res.zip);
                $("#user_state").text(res.state);
            },

            complete: function() {
                $("#address_modal").modal('hide')
                $(".modal-backdrop").remove()
            }
        });
    })
})

$(".readonly").on('keydown', function (e) {
    e.preventDefault();
});


$(document).ready(function () {
    if ($('#sortResults').length && window.innerWidth < 992) {
        $('html, body').animate({
            scrollTop: $(".homepage_results").offset().top
        }, 2000);
    }
    ;
});
