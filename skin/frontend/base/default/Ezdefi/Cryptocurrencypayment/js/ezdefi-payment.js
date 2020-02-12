var selectors = {
    selectCurrencyRadio: '.ezdefi__select-currency--checkbox',
    selectCurrencyLabel: '.ezdefi__select-currency--label',
    buttonCreatePayment: '.ezdefi__btn-create-payment',
    selectCurrencyBox  : '.ezdefi__select-currency-box',
    paymentBox         : '.ezdefi-payment-box',
    simpleMethodContent: '.ezdefi__simple-payment-content',
    ezdefiMethodContent: '.ezdefi__ezdefi-payment-content',
    simplePaymentBox   : '.simple-pay-box',
    ezdefiPaymentBox   : '.ezdefi-pay-box',
    btnChooseMethod    : '.choose-method-radio'
};

var $j = jQuery.noConflict();
$j( function() {
    var EzdefiPayment = Class.create();
    EzdefiPayment.prototype = {

        initialize: function () {
            this.enablePlaceOrder();
            $j(document).on('click', selectors.buttonCreatePayment, this.afterPlaceOrder.bind(this));
            $j(document).on('change', selectors.btnChooseMethod, this.checkPaymentToCreate.bind(this));
        },

        enablePlaceOrder: function () {

            $j(document).on('change', selectors.selectCurrencyRadio, function () {
                let inputId = $j(selectors.selectCurrencyRadio + ":checked").attr('id');
                $j(selectors.selectCurrencyLabel).css('border', '1px solid #d8d8d8').css('background', 'inherit');
                $j(selectors.selectCurrencyLabel + "[for='" + inputId + "']").css('border', '2px solid #54bdff').css('background', '#c0dcf9db');
                $j(selectors.buttonCreatePayment).prop('disabled', false);
            });
        },

        createPayment: function (paymentType) {
            $j(selectors.selectCurrencyBox).css('display', 'none');
            $j(selectors.paymentBox).css('display', 'block');


            var that = this;
            var currencyId = $j(selectors.selectCurrencyRadio+":checked").val();

            $j.ajax({
                url: '/ezdefi_frontend/payment/create',
                method: "GET",
                data: {
                    type: paymentType,
                    currency_id: currencyId
                },
                success: function(response) {
                    if(paymentType === 'simple') {
                        that.renderSimplePayment(response);
                    } else if (paymentType === 'ezdefi') {
                        that.renderEzdefiPayment(response);
                    }
                    that.createCountDownSimpleMethod();
                }
            });
        },

        createCountDownSimpleMethod: function () {

        },

        renderEzdefiPayment: function(html) {
            $j('.loader--ezdefi').css('display', 'none');
            $j(selectors.ezdefiMethodContent).html(html)
        },

        renderSimplePayment: function (html) {
            $j('.loader--simple').css('display', 'none');
            $j(selectors.simpleMethodContent).html(html)
        },

        afterPlaceOrder: function() {
            this.createSimplePayment();
        },

        checkPaymentToCreate: function() {
            var method = $j('input[name="choose-method-radio"]:checked').val();
            if(method === 'ezdefi') {
                this.createEzdefiPayment()
            } else if (method === 'simple') {
                this.createSimplePayment();
            }
        },

        createSimplePayment: function() {
            $j(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
            $j(".btn-show-payment--simple").addClass('ezdefi__check-showed-payment');
            $j(".payment-box").css('display', 'none');
            $j(".simple-pay-box").css('display', 'block');

            if(!$j(selectors.simpleMethodContent).html().trim()) {
                this.createPayment('simple');
            }
        },
        
        createEzdefiPayment: function () {
            $j(selectors.paymentBox).css('display', 'block');
            $j(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
            $j(".btn-show-payment--ezdefi").addClass('ezdefi__check-showed-payment');
            $j(".payment-box").css('display', 'none');
            $j(".ezdefi-pay-box").css('display', 'block');
            if(!$j(selectors.ezdefiMethodContent).html().trim()) {
                this.createPayment('ezdefi');
            }
        }

    };

    new EzdefiPayment();
});