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

    [...document.querySelectorAll('.cart-product')].forEach((product) => {
        const product_currency = product.querySelector('.product-currency').textContent;
        let product_price = Number(product.querySelector('.product-price').textContent) * exchange[product_currency];

        totalCost += product_price * Number(product.querySelector('.product-count').value);

        discountPercent = (document.querySelector('.coupon-percent')) ? Number(document.querySelector('.coupon-percent').textContent) : 0;
        subtotalCost = totalCost - (totalCost / 100 * discountPercent);
    });

    document.querySelector('.total-price').textContent = totalCost.toFixed(2);
    document.querySelector('.subtotal-price').textContent = subtotalCost.toFixed(2);
};

init();
