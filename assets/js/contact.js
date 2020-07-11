
function messageFadeOut(identifier)
{
    setTimeout(function () {
        $(identifier).fadeOut("slow");
    },5000)
}

function messageFadeIn(identifier)
{
    $(identifier).fadeIn("slow");
}

$(document).ready(function () {

    $("#send_spinner").hide(); //hide spinner
    var form = $("#contact_form"); //initalize contact form
    var formMessages = $(".form-message"); //initalize response holder

    $("#submit_contact_form").click(function () {

        $("#send_ico").hide(); //hide send icon on button
        $("#send_spinner").show(); //show spinner icon on button

        var formData = $(form).serialize();
        //Ajax request to send form data
        $.ajax({
            type: "POST",
            url: $("#contact_form").attr("action"),
            data: formData,

            success: function (response) {
                //Success response
                var output = response.split("|");
                if (output[0] == 1) {

                    $(formMessages).removeClass("alert alert-danger");
                    $(formMessages).addClass("alert alert-success");
                    $(formMessages).text(output[1]);
                    $("#contact_form input,#contact_form textarea").val("");


                } else if (output[0] == 0) {

                    $(formMessages).removeClass("alert alert-success");
                    $(formMessages).addClass("alert alert-danger");
                    $(formMessages).text(output[1]);


                } else {

                    $(formMessages).removeClass("alert alert-success");
                    $(formMessages).addClass("alert alert-danger");
                    $(formMessages).text(  "Oops! An error occured and your message could not be sent." );

                }
                messageFadeIn(formMessages);
                $(send_ico).show();
                $(send_spinner).hide();
            },

            error: function (response) {
                //If not Success
                console.log(response);
                $(send_ico).show();
                $(send_spinner).hide();
            },

        });

        messageFadeOut(formMessages);
    });


});
