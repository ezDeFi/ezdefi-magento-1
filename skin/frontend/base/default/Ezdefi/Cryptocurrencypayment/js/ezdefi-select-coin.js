var selectors = {
    selectCurrencyRadio: '.ezdefi__select-currency--checkbox',
    selectCurrencyLabel: '.ezdefi__select-currency--label',
    btnPlaceOrder      : '.ezdefi__btn-place-order',
    btnCreatePayment   : '.ezdefi__btn-create-payment',
};

var $j = jQuery.noConflict();
$j( function() {
    var EzdefiPayment = Class.create();
    EzdefiPayment.prototype = {
        initialize: function () {
            $j(document).on('change', selectors.selectCurrencyRadio, this.enablePlaceOrder.bind(this))
        },

        enablePlaceOrder: function () {
            let inputId = $j(selectors.selectCurrencyRadio + ":checked").attr('id');
            $j(selectors.selectCurrencyLabel).css('border', '1px solid #d8d8d8').css('background', 'inherit');
            $j(selectors.selectCurrencyLabel + "[for='" + inputId + "']").css('border', '2px solid #54bdff').css('background', '#c0dcf9db');
            $j(selectors.btnPlaceOrder).css({background: 'cornflowerblue', cursor: 'pointer'});
            $j(selectors.btnCreatePayment).css({background: 'cornflowerblue', cursor: 'pointer'});
        },

    }
    new EzdefiPayment();
});