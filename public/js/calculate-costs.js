const init = () => {
    let totalCost = 0;
    let subtotalCost = 0;
    let exchange = {};

    // Формирования массива с данными курса валют
    [...$('.exchange-rate')].forEach((exc) => {
        const excJQ = $(exc);

        exchange[excJQ.data('code')] = Number(excJQ.data('sale'));
    });

    // Проход по всем продуктам в корзине и расчёт итоговорой и подитоговой цены
    [...$('.cart-product')].forEach((product) => {
        const productJQ = $(product);
        const product_currency = productJQ.data('currency');
        const product_price = Number(productJQ.data('price')) * exchange[product_currency];

        totalCost += product_price * Number(productJQ.find('.product-count').val());

        discountPercent = ($('.coupon-percent')) ? Number($('.coupon-percent').text()) : 0;
        subtotalCost = totalCost - (totalCost / 100 * discountPercent);
    });

    $('.total-price').text(totalCost.toFixed(2));
    $('.subtotal-price').text(subtotalCost.toFixed(2));
};

init();
