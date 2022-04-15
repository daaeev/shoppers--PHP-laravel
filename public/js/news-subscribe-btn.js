// Реализация кнопки подписки на рассылку
$('#news-sub-btn').click(function () {
    let button = $(this);
    let data_email = $('#email-subscribe-input').val();

    // Проверка на пустую строку
    if (!data_email) {
        if ($('.email-sub-input-error').length > 0) {
            $('.email-sub-input-error').remove();
        }

        $('.email-sub-block').append('<label class="text-danger email-sub-input-error">Field Email is required</label>');

        return;
    }

    // Проверка на то, что введенная строк - эл. почта
    let regExp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (!regExp.test(String(data_email).toLowerCase())) {
        if ($('.email-sub-input-error').length > 0) {
            $('.email-sub-input-error').remove();
        }

        $('.email-sub-block').append('<label class="text-danger email-sub-input-error">The field must be an email</label>');

        return;
    }

    $.ajax({
        url: button.data('href'),
        method: 'POST',
        data: {email: data_email},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            button.attr('disabled', true);
        },
        success: function(success_msg = null) {
            if ($('.email-sub-input-error').length > 0) {
                $('.email-sub-input-error').remove();
            }

            if ($('.email-sub-input-success').length > 0) {
                $('.email-sub-input-success').remove();
            }

            if (success_msg) {
                $('.email-sub-block').append('<label class="text-success email-sub-input-success">' + success_msg + '</label>');
            } else {
                $('.email-sub-block').append('<label class="text-success email-sub-input-success">You have subscribed to our news</label>');
            }
        },
        error: function (error) {
            if ($('.email-sub-input-error').length > 0) {
                $('.email-sub-input-error').remove();
            }

            if ($('.email-sub-input-success').length > 0) {
                $('.email-sub-input-success').remove();
            }

            if (error) {
                let msg = JSON.parse(error.responseText);

                $('.email-sub-block').append('<label class="text-danger email-sub-input-error">' + msg.message + '</label>');
            } else {
                $('.email-sub-block').append('<label class="text-danger email-sub-input-error">Something went wrong! <a href="/login">Log in</a> and try again</label>');

            }
        }
    });
});
