require("./bootstrap");

var available = 8;
var elem = $("#ticket_seats");
var sent_items = false;

function fetch_seats() {
    var json = {"seats": []};
    $(".seat_btn").each(function () {
        if ($(this).hasClass("newly_reserved")) {
            json.seats.push($(this).val());
        }
    });
    elem.val(JSON.stringify(json));
}

$("#ticket_form").submit(function (e) {
    e.preventDefault();
    console.log("mrdka");
    if (available > 7) {
        alert("You need to choose at least one seat.");
        return false;
    }
    sent_items = true;
    fetch_seats();
    $(this).unbind('submit').submit();
});

$(".seat_btn").click(function () {
    if ($(this).hasClass("reserved")) {
        alert("This seat is already reserved");
        return;
    }

    if (available < 1 && !$(this).hasClass('newly_reserved')) {
        alert("You cannot reserve more than 8 seats.");
        return;
    }

    const seat_id = $(this).val();
    const instance_id = $("#instance_id").text();
    const token = $("meta[name=csrf-token]").attr("content");
    var time = new Date();
    const hours = time.getHours() < 10 ? "0" + time.getHours() : time.getHours();
    const minutes = time.getMinutes() < 10 ? "0" + time.getMinutes() : time.getMinutes();
    var timestamp = `${time.getDate()}.${time.getMonth()}.${time.getFullYear()} ${hours}:${minutes}`;
    console.log(timestamp);

    const that = $(this);

    $.ajax({
        url: "seat",
        type: "POST",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        data: {seat_id: seat_id, instance_id: instance_id, token: token, timestamp: timestamp},
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            if (that.hasClass('newly_reserved')) {
                that.removeClass('newly_reserved');
                available++;
            } else {
                that.addClass('newly_reserved');
                available--;
            }

            $("#ticket_amount").val(8 - available);
            $("#price_total").text(
                parseFloat($("#price_per_ticket").text()) *
                parseInt($("#ticket_amount").val())
            );
        },
        error: function (res) {
            alert(res.responseText);
        }
    });
});


$(".ticket_c").click(function () {
    const token = $("meta[name=csrf-token]").attr("content");
    const ticket_id = $(this)[0].value;
    const that = $(this);

    $.ajax({
        url: "confirm_ticket",
        type: "POST",
        dataType: "text",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        data: {ticket_id: ticket_id},
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            that.parent()
                .siblings(".ticket_paid")
                .text("Paid");
            that.text("Unavailable");
            that.removeClass();
            that.addClass('btn ticket_canceled');

        },
        error: function (res) {
            console.log(res.responseText)
        },
    });
});

$(".ticket_can").click(function () {

    const token = $("meta[name=csrf-token]").attr("content");
    const ticket_id = $(this).parent().siblings(".ticket_id").text();
    const that = $(this);

    $.ajax({
        url: window.location.pathname + "/cancel_ticket",
        type: "POST",
        dataType: "text",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        data: {ticket_id: ticket_id},
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            that.removeClass('ticket_can btn btn-primary')
                .text("Canceled");
        },
        error: function (res) {
            console.log(res.responseText)
        },
    });
});

$(function () {
    $('#datepicker').datepicker({dateFormat: "dd. mm. yy"}).keyup(function (e) {
        if (e.keyCode == 8 || e.keyCode == 46) {
            $.datepicker._clearDate(this);
        }
    });
});

$(function () {
    $('#datepicker_add').datepicker({dateFormat: "dd. mm. yy"}).keyup(function (e) {
        if (e.keyCode == 8 || e.keyCode == 46) {
            $.datepicker._clearDate(this);
        }
    });
});

$("#reset-date").click(function () {
    $('#datepicker').val("").datepicker("update");
});

$(window).on('beforeunload', function () {
    if (sent_items) {
        return;
    }
    fetch_seats();
    const token = $("meta[name=csrf-token]").attr("content");
    $.ajax({
        url: "cancel_seats",
        type: "POST",
        dataType: "text",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        data: {seats: elem.val()},
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            console.log(res);
        },
        error: function (res) {
            console.log(res.responseText)
        },
    });
});

$(".delete_user_admin").click(function () {
    $("#delete_user_modal_form").attr('action', '/user/' + $(this).val() + '/delete');
});

$(".update_user_admin").click(function () {
    const token = $("meta[name=csrf-token]").attr("content");


    $.ajax({
        url: "/user/" + $(this).val() + "/get",
        type: "GET",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            $("#update_user_modal_form").attr('action', "/user/" + res.username + "/update");
            $("#user_role").val(res.role);
            $("#firstName").val(res.firstName);
            $("#lastName").val(res.lastName);
            $("#phoneNumber").val(res.phoneNumber);
            $("#modal_username").text(res.username);
            $("#userRole").val(res.role);
            $("#manages").val(res.manages);


            if (res.role == "cashier") {
                $("#manages").attr("disabled", false);
            } else {
                $("#manages").attr("disabled", true);
            }
            console.log(res);
        },
        error: function (res) {
            alert("Cannot fetch user.");
        },
    });
});

$('.update_event_admin').click(function () {
    const token = $("meta[name=csrf-token]").attr("content");

    $.ajax({
        url: "/event/" + $(this).val() + "/get",
        type: "GET",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            $("#name").val(res.name);
            $("#description").val(res.description);
            $("#performers").val(res.performers);
            $("#type").val(res.type);
            $("#update_event_placeholder").text(res.name);
            $("#update_event_modal_form").attr('action', 'event/' + res.id + "/update");
        }
    });
});

$('.update_room_admin').click(function () {
    const token = $("meta[name=csrf-token]").attr("content");

    $.ajax({
        url: "/room/" + $(this).val() + "/get",
        type: "GET",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            $("#name").val(res.name);
            $("#rows").val(res.rows);
            $("#columns").val(res.columns);
            $("#update_room_placeholder").text(res.name);
            $("#update_room_modal_form").attr('action', 'room/' + res.id + "/update");
        }
    });
});



$('.update_instance_admin').click(function () {
    const token = $("meta[name=csrf-token]").attr("content");

    $.ajax({
        url: "/event/" + $(this).val() + "/getInstance",
        type: "GET",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            $("#update_instance_form").attr('action', 'event/' + res.id + '/updateInstance');
            $("#event_id").val(res.event_id);
            $("#room_id").val(res.room_id);
            $("#datepicker").val(res.date);
            $("#time").val(res.time);
            $("#price").val(res.price);
        }
    });
});


$(".delete_event_admin").click(function () {
    $("#delete_event_modal_form").attr('action', '/event/' + $(this).val() + '/delete');
});

$(".delete_room_admin").click(function () {
    $("#delete_event_modal_form").attr('action', '/room/' + $(this).val() + '/delete');
});

/*
$('.delete_event_admin').click(function () {
    $("#delete_event_modal_form").attr('action', '/event/' + $(this).val() + '/delete');
    const token = $("meta[name=csrf-token]").attr("content");
    $.ajax({
        url: "/event/" + $(this).val() + "/delete",
        type: "POST",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            location.reload();
        }
    });

});
*/


$(".delete_instance_admin").click(function () {
    $("#delete_instance_modal_form").attr('action', '/event/' + $(this).val() + '/deleteInstance');
});

/*
$('.delete_instance_admin').click(function () {

    const token = $("meta[name=csrf-token]").attr("content");
    $.ajax({
        url: "/event/" + $(this).val() + "/deleteInstance",
        type: "POST",
        dataType: "json",
        beforeSend: function (request) {
            request.setRequestHeader('x-csrf-token', token);
        },
        ContentType: "application/json;charset=UTF-8",
        success: function (res) {
            location.reload();
        }
    });
});
*/

$("#role").change(function () {
    if ($("#role").val() == "cashier") {
        $("#manages").attr("disabled", false);
    } else {
        $("#manages").attr("disabled", true);
    }
});

$("#user_role").change(function () {
    if ($("#user_role").val() == "cashier") {
        $("#manages").attr("disabled", false);
    } else {
        $("#manages").attr("disabled", true);
    }
});
$('.custom-file input').change(function() {
    var $el = $(this)
    files = $el[0].files
    if (files.length = 1) {
        label = "1 picture"
    }
    if (files.length > 1) {
        label = String(files.length) + " pictures"
    }
    $el.next('.custom-file-label').html(label);
});


let modalId = $('#image-gallery');

$(document)
  .ready(function () {

    loadGallery(true, 'a.thumbnail');

    //This function disables buttons when needed
    function disableButtons(counter_max, counter_current) {
      $('#show-previous-image, #show-next-image')
        .show();
      if (counter_max === counter_current) {
        $('#show-next-image')
          .hide();
      } else if (counter_current === 1) {
        $('#show-previous-image')
          .hide();
      }
    }

    /**
     *
     * @param setIDs        Sets IDs when DOM is loaded. If using a PHP counter, set to false.
     * @param setClickAttr  Sets the attribute for the click handler.
     */

    function loadGallery(setIDs, setClickAttr) {
      let current_image,
        selector,
        counter = 0;

      $('#show-next-image, #show-previous-image')
        .click(function () {
          if ($(this)
            .attr('id') === 'show-previous-image') {
            current_image--;
          } else {
            current_image++;
          }

          selector = $('[data-image-id="' + current_image + '"]');
          updateGallery(selector);
        });

      function updateGallery(selector) {
        let $sel = selector;
        current_image = $sel.data('image-id');
        $('#image-gallery-title')
          .text($sel.data('title'));
        $('#image-gallery-image')
          .attr('src', $sel.data('image'));
        disableButtons(counter, $sel.data('image-id'));
      }

      if (setIDs == true) {
        $('[data-image-id]')
          .each(function () {
            counter++;
            $(this)
              .attr('data-image-id', counter);
          });
      }
      $(setClickAttr)
        .on('click', function () {
          updateGallery($(this));
        });
    }
  });

// build key actions
$(document)
  .keydown(function (e) {
    switch (e.which) {
      case 37: // left
        if ((modalId.data('bs.modal') || {})._isShown && $('#show-previous-image').is(":visible")) {
          $('#show-previous-image')
            .click();
        }
        break;

      case 39: // right
        if ((modalId.data('bs.modal') || {})._isShown && $('#show-next-image').is(":visible")) {
          $('#show-next-image')
            .click();
        }
        break;

      default:
        return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
  });

  document.getElementById('event_cover_del').onchange = function() {
    document.getElementById('inputGroupFile01').disabled = this.checked;
    document.getElementById('inputGroupFile01').value = '';
};

document.getElementById('event_picture_del').onchange = function() {
    document.getElementById('inputGroupFile02').disabled = this.checked;
    document.getElementById('inputGroupFile02').value = '';
};
