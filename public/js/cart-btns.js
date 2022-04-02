function error() {
    alert('Oops, something is wrong!');
    location.reload();
}

// Реализация кнопки добавления продукта в корзину
$('#add-to-cart-btn').click(function () {
    $.ajax({
        url: $(this).data('href'),
        method: 'GET',
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
            if (!($('#product-' + product_id).length == 1)) {
                error();
            }

            if ($('.cart-product').length == 1) {
                $('.cart-products-table').remove();
                $('#cart-elements-count').remove();
                $('.cart-table-block').append('<h2 class="text-center">Cart is empty!</h2>');
            } else {
                $('#product-' + product_id).remove();
                $('#cart-elements-count').text($('#cart-elements-count').text() - 1);
            }
        },
        error: error
    });
});

