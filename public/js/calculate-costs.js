const init = () => {
    let totalCost = 0;
    let subtotalCost = 0;

    // TODO: API для полуения курса валют ... к гривне
    // заглушка
    const exchange = {
        'UAH': 1,
        'USD': 28,
        'EUR': 32,
    };

    [...$('.cart-product')].forEach((product) => {
        const product_currency = $(product).find('.product-currency').text();
        let product_price = Number($(product).find('.product-price').text()) * exchange[product_currency];

        totalCost += product_price * Number($(product).find('.product-count').val());

        discountPercent = ($('.coupon-percent')) ? Number($('.coupon-percent').text()) : 0;
        subtotalCost = totalCost - (totalCost / 100 * discountPercent);
    });

    $('.total-price').text(totalCost.toFixed(2));
    $('.subtotal-price').text(subtotalCost.toFixed(2));
};

init();
