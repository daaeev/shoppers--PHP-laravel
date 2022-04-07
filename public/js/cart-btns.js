const totalPriceDOM = $('.total-price')
const subTotalPriceDOM = $('.subtotal-price');

function error() {
    alert('Oops, something is wrong!');
    location.reload();
}

function nullifyTotal()
{
    totalPriceDOM.text('0.00');
}

function nullifySubtotal()
{
    subTotalPriceDOM.text('0.00');
}

function setTotal(totalCost)
{
    totalPriceDOM.text(totalCost);
}

function setSubtotal(totalCost, discountPercent)
{
    let cost = (totalCost - (totalCost / 100 * discountPercent)).toFixed(2);
    subTotalPriceDOM.text(cost);
}

// Реализация кнопки добавления продукта в корзину
$('#add-to-cart-btn').click(function () {
    let button = $(this);

    $.ajax({
        url: button.data('href'),
        method: 'GET',
        beforeSend: function() {
            button.attr('disabled', true);
        },
        success: function() {
            if ($('#add-to-cart-success').length == 0) {
                $('#button_add-to-card_block').append('<span id="add-to-cart-success" class="text-success ml-2">Product added to cart!</span>');
            }
        },
        error: error
    });
});

// Реализация кнопки удаления продукта из корзины
$('.remove-from-cart-btn').click(function () {
    $.ajax({
        url: $(this).data('href'),
        method: 'GET',
        success: function(product_id) {
            if ($('.cart-product').length == 1) {

                // Форматирование страницы
                $('.cart-products-table').remove();
                $('#cart-elements-count').remove();
                $('.block-update-cart-btn').remove();
                $('.cart-table-block').append('<h2 class="text-center">Cart is empty!</h2>');

                // Установка цен
                nullifyTotal();
                nullifySubtotal();
            } else {

                // Установка цен
                let totalCost = (Number(totalPriceDOM.text()) - Number($('#product-' + product_id + ' .product-price').text()) * Number($('#product-' + product_id + ' .product-count').val())).toFixed(2);
                let discountPercent = (document.querySelector('.coupon-percent')) ? Number(document.querySelector('.coupon-percent').textContent) : 0;

                setTotal(totalCost);
                setSubtotal(totalCost, discountPercent);

                // Форматирование страницы
                $('#product-' + product_id).remove();
                $('#cart-elements-count').text(Number(($('#cart-elements-count').text()) - 1));
            }
        },
        error: error
    });
});

// Реализация кнопки обновления корзины
$('.update-cart-btn').click(function () {
    $.ajax({
        url: $(this).data('href'),
        method: 'GET',
        success: function() {

            // Форматирование страницы
            $('.cart-products-table').remove();
            $('#cart-elements-count').remove();
            $('.block-update-cart-btn').remove();
            $('.cart-table-block').append('<h2 class="text-center">Cart is empty!</h2>');

            // Установка цен
            nullifyTotal();
            nullifySubtotal();
        },
        error: error
    });
});

// Реализация кнопки прибавления количества
$('.product-count-plus-btn').click(function () {
    const button = $(this);
    const product_id = button.data('product');
    const productDOM = $('#product-' + product_id);

    $.ajax({
        url: button.data('href'),
        method: 'GET',
        beforeSend: function () {
            button.attr('disabled', true);
            setTimeout(() => button.attr('disabled', false), 3000);
        },
        success: function(product_count) {

            // Если значение в куках не равно значению в инпуте
            if (product_count != productDOM.find('.product-count').val()) {
                error();
            }

            // Расчет цен
            init();
        },
        error: error
    });
});

// Реализация кнопки уменьшения количества
$('.product-count-minus-btn').click(function () {
    const button = $(this);
    const product_id = button.data('product');
    const productDOM = $('#product-' + product_id);

    $.ajax({
        url: button.data('href'),
        method: 'GET',
        beforeSend: function () {
            button.attr('disabled', true);
            setTimeout(() => button.attr('disabled', false), 3000);
        },
        success: function(product_count) {

            // Если значение в куках не равно значению в инпуте
            if (product_count != productDOM.find('.product-count').val()) {
                error();
            }

            // Расчет цен
            init();
        },
        error: error
    });
});

// Реализация кнопки использования купона
$('.apply-coupon-btn').click(function () {
    const coupon_token = $('#coupon-token-input').val();

    if (coupon_token.length == 0) {

        if ($('.coupon-input-error').length == 0) {
            $('.coupon-input-block').append('<label class="coupon-input-required text-danger">This field is required</label>');
        }

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
                <div class="user-coupon-block">
                    <div class="col-md-12">
                        <label class="text-black h4" htmlFor="coupon">Coupon</label>
                        <h3><span class="text-danger">` + coupon_token + `</span> <span
                            class="text-success coupon-percent">` + coupon_percent + `</span><span
                            class="text-success">%</span></h3>
                    </div>
                </div>`;

            // Блок с уведомлением о том, что купон успешно активирован
            let couponApplied = `
                <div class="user-coupon-block">
                    <div class="col-md-12">
                        <label class="text-black h4" htmlFor="coupon">Coupon</label>
                        <h3><span class="text-success">Coupon applied</span></h3>
                    </div>
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
            if ($('.coupon-input-error').length) {
                $('.coupon-input-error').remove();
            }

            $('.coupon-input-block').append('<label class="coupon-input-required text-danger">Coupon does not exist</label>');
        }
    });
});