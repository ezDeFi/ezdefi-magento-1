var selectors = {
    selectCurrencyRadio: '.ezdefi__select-currency--checkbox',
    selectCurrencyLabel: '.ezdefi__select-currency--label',
    btnPlaceOrder      : '.ezdefi__btn-place-order',
    selectCurrencyBox  : '.ezdefi__select-currency-box',
    paymentBox         : '.ezdefi-payment-box',
    simpleMethodContent: '.ezdefi__simple-payment-content',
    ezdefiMethodContent: '.ezdefi__ezdefi-payment-content',
    simplePaymentBox   : '.simple-pay-box',
    ezdefiPaymentBox   : '.ezdefi-pay-box',
    btnChooseMethod    : '.choose-method-radio',
    btnChangeCurrency  : '.ezdefi__btn-change-currency',
    btnCreatePayment   : '.ezdefi__btn-create-payment',
    currencyChosedLogo : '.ezdefi__payment-currency--logo',
    currencyChosedName : '.ezdefi__payment-currency--name',
};

var $j = jQuery.noConflict();
$j( function() {
    var EzdefiPayment = Class.create();
    EzdefiPayment.prototype = {
        countDownInterval: {
            simple : null,
            ezdefi : null
        },

        initialize: function () {
            this.enablePlaceOrder();
            $j(document).on('click', selectors.btnPlaceOrder, this.afterPlaceOrder.bind(this));
            $j(document).on('click', selectors.btnCreatePayment, this.checkDefaultPaymentToCreate.bind(this));
            $j(document).on('change', selectors.btnChooseMethod, this.checkPaymentToCreate.bind(this));
            $j(document).on('click', selectors.btnChangeCurrency, this.changeCurrency.bind(this));18
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
            $j('.loader--'+paymentType).css('display', 'flex');

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
                    that.createCountDownSimpleMethod(paymentType);
                }
            });
        },

        createCountDownSimpleMethod: function (type) {
            var that = this;
            var expiredTime = $j("#ezdefi__payment-expiration--" + type).val();
            this.countDownInterval[type] = setInterval(function () {
                var timestampCountdown = new Date(expiredTime) - new Date();
                var secondToCountdown = Math.floor(timestampCountdown / 1000);
                if (secondToCountdown >= 0) {
                    var hours = Math.floor(secondToCountdown / 3600);
                    secondToCountdown %= 3600;
                    var minutes = Math.floor(secondToCountdown / 60);
                    var seconds = secondToCountdown % 60;
                    if (hours > 0) {
                        $j("#ezdefi__countdown-label--" + type).html(hours + ':' + minutes + ':' + seconds);
                    } else {
                        $j("#ezdefi__countdown-label--" + type).html(minutes + ':' + seconds);
                    }
                } else {
                    $j("#ezdefi__countdown-label--" + type).html('0:0');
                    clearInterval(that.countDownInterval[type]);
                    that.showTimeoutMesage(type);
                }
            }, 1000);
        },

        showTimeoutMesage: function(type) {
            var that = this;
            var timeoutNotify = $j('.timeout-notification--' + type);
            var qrcodeImage = $j('.ezdefi-payment__qr-code--' + type);
            timeoutNotify.css('display', 'block');
            qrcodeImage.css('filter', 'blur(4px)');

            timeoutNotify.click(function () {
                if (type == 'simple') {
                    $j(selectors.simpleMethodContent).html('');
                } else {
                    $j(selectors.ezdefiMethodContent).html('');
                }
                that.createPayment(type);
            });
        },

        changeCurrency: function () {
            $j(selectors.selectCurrencyBox).css('display', 'block');
            $j(selectors.paymentBox).css('display', 'none');
            // $j("#ezdefi__select-currency--checkbox").prop('checked', false);
            clearInterval(this.countDownInterval.simple);
            clearInterval(this.countDownInterval.ezdefi);

            $j(selectors.ezdefiMethodContent).html('');
            $j(selectors.simpleMethodContent).html('');
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
            var that = this;
            ezdefiCustomPayment.save(function () {
                ezdefiReview.save(function () {
                    that.createSimplePayment();
                    that.checkDefaultPaymentToCreate();
                    that.checkOrderComplete();
                    $j(selectors.btnPlaceOrder).css('display', 'none');
                    $j(selectors.btnCreatePayment).css('display', 'block');
                });
            });
        },

        checkDefaultPaymentToCreate: function() {
            var currency = $j('input[name="currency-selected-to-order"]:checked');
            $j(selectors.currencyChosedLogo).prop('src', currency.data('logo'));
            $j(selectors.currencyChosedName).html(currency.data('symbol') +'/'+ currency.data('name'));

            let enableSimpleMethod = $j('#choose-simple-method-radio').length > 0;
            let enableEzdefiMethod = $j('#choose-ezdefi-method-radio').length > 0;
            if(enableSimpleMethod) {
                this.createSimplePayment();
                $j('#choose-simple-method-radio').prop('checked', true);
                $j(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
                $j(".btn-show-payment--simple").addClass('ezdefi__check-showed-payment');
                $j(".payment-box").css('display', 'none');
                $j(".simple-pay-box").css('display', 'block');
            } else if (enableEzdefiMethod) {
                this.createEzdefiPayment();
                $j(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
                $j(".btn-show-payment--ezdefi").addClass('ezdefi__check-showed-payment');
                $j(".payment-box").css('display', 'none');
                $j(".ezdefi-pay-box").css('display', 'block');
            }

        },

        checkOrderComplete: function () {
            var that = this;
            let urlCheckOrderComplete = '/ezdefi_frontend/payment/checkordercomplete';
            $j.ajax({
                url: urlCheckOrderComplete,
                method: "GET",
                data: {},
                dataType: 'json',
                success: function(response) {
                    let orderStatus = response.orderStatus;
                    if(orderStatus === 'processing') {
                        // clearInterval(checkOrderCompleteInterval);
                        alert('order complete');
                        window.location.href = '/';
                    } else {
                        setTimeout(function () {
                            that.checkOrderComplete();
                        }, 1000);
                    }
                }
            });
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