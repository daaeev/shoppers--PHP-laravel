// Реализация кнопки использования купона
$('.buy-page-apply-coupon-btn').click(function () {
    const coupon_token = $('#coupon-token-input').val();

    if (coupon_token.length == 0) {

        if ($('.coupon-input-error').length > 0) {
            $('.coupon-input-error').remove();
        }

        $('.coupon-input-block').append('<label class="coupon-input-error text-danger">This field is required</label>');

        return;
    }

    if (coupon_token.length > 30) {
        error();
    }

    const button = $(this);

    $.ajax({
        url: button.data('href'),
        method: 'POST',
        data: {token: coupon_token},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            button.attr('disabled', true);
            setTimeout(() => button.attr('disabled', false), 3000);
        },
        success: function(coupon_percent) {
            // Очищение блока
            $('.coupon-block').empty();

            // Блок с информацией о активированном купоне
            let couponInfo = `
            <h2 class="h3 mb-3 text-black">Coupon Code</h2>
              <div class="p-3 p-lg-5 border">
                  <h3>
                      <span class="text-danger">` + coupon_token + `</span>
                      <span class="text-success coupon-percent">` + coupon_percent + `</span><span class="text-success">%</span>
                  </h3>
              </div>`;

            // Блок с уведомлением о том, что купон успешно активирован
            let couponApplied = `
                <h2 class="h3 mb-3 text-black">Coupon Code</h2>
                <div class="p-3 p-lg-5 border">
                    <h3><span class="text-success">Coupon applied</span></h3>
                </div>`;

            $('.coupon-block').append(couponApplied);

            setTimeout(function () {
                $('.coupon-block').empty();
                $('.coupon-block').append(couponInfo);

                // Расчет цен
                init();
            }, 2000);
        },
        error: function () {
            if ($('.coupon-input-error').length > 0) {
                $('.coupon-input-error').remove();
            }

            $('.coupon-input-block').append('<label class="coupon-input-error text-danger">Coupon does not exist</label>');
        }
    });
});
