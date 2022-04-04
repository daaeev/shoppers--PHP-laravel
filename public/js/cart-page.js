const init = () => {
    let totalCost = 0;
    let subtotalCost = 0;

    [...document.querySelectorAll('.cart-product')].forEach((product) => {
        totalCost += Number(product.querySelector('.product-price').textContent) * Number(product.querySelector('.product-count').value);
        discountPercent = 0; // TODO
        subtotalCost = totalCost - (totalCost / 100 * discountPercent);
    });

    document.querySelector('.total-price').textContent = totalCost.toFixed(2);
    document.querySelector('.subtotal-price').textContent = subtotalCost.toFixed(2);
};

init();
