var $j = jQuery.noConflict();
$j( function() {
    var selectors = {
        coinConfigTable         : '.ezdefi__coin-config-table',
        simplePaymentCheckbox   : '.ezdefi__simple-payment-checkbox',
        ezdefiPaymentCheckbox   : '.ezdefi__ezdefi-payment-checkbox',
        checkPaymentMethodInput : '.check-payment-method-input',
        currencySymbolInput     : '.ezdefi__currency-symbol-input',
        currencyOrderByInput    : '.ezdefi__currency-orderby-input',
        currencyNameInput       : '.ezdefi__currency-name-input',
        currencyIdInput         : '.ezdefi__currency-id-input',
        currencyDescriptionInput: '.ezdefi__currency-description-input',
        currencyLogoInput       : '.ezdefi__currency-logo-input',
        currencydiscountInput   : '.ezdefi__currency-discount-input',
        currencyLifetimeInput   : '.ezdefi__payment-lifetime-input',
        currencyDecimalInput    : '.ezdefi__currency-decimal-input',
        currencyMaxDecimalInput : '.ezdefi__currency-max-decimal-input',
        blockConfirmationInput  : '.ezdefi_block-confirmation-input',
        walletAddressInput      : '.ezdefi__wallet-address-input',
        btnCancelAddCurrency    : '.canel-add-currency-input',
        btnDeleteCurrency       : '.btn-delete-curency-config'
    };
    var tmp = 1;
    var validator = new Validation('config_edit_form');

    //-------------init------------------
    initCancelAddCurrency('.canel-add-currency');

    $j(document).on('focus', '.ezdefi__currency-decimal-input', function () {
        $j(this).parent().find('.ezdefi__warning_edit_decimal').css('display', 'block');
    })
    $j(document).on('focusout', '.ezdefi__currency-decimal-input', function () {
        $j(this).parent().find('.ezdefi__warning_edit_decimal').css('display', 'none');
    })

    // -----------------validate----------------------


    $j(document).on('keypress', '.only-float', function(eve) {
        if ((eve.which != 46 || $j(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $j(this).caret().start == 0) ) {
            eve.preventDefault();
        }
        $j(document).on('keyup', '.only-float', function(eve) {
            if($j(this).val().indexOf('.') == 0) {
                $j(this).val($j(this).val().substring(1));
            }
        });
    });
    $j(document).on('keypress', '.only-positive-integer', function (eve) {
        if (eve.which < 48 || eve.which > 57) {
            eve.preventDefault();
        }
    });

    $j(document).on("change", '.ezdefi__gateway-api-url-input', function () {
        var apiKey = $j('.ezdefi__api-key').val();
        $j('.ezdefi__api-key').val('x');
        validator.validate();
        $j('.ezdefi__api-key').val(apiKey);
        validator.validate();
    });

    $j(document).on("change", '.ezdefi__api-key', function () {
        validator.validate();
    });

    $j(document).on("change", selectors.ezdefiPaymentCheckbox, checkPaymentMethodRequire);
    $j(document).on("change", selectors.simplePaymentCheckbox, function () {
        checkPaymentMethodRequire();
        showAcceptedCurrency();
    });
    function showAcceptedCurrency() {
        if($j(selectors.simplePaymentCheckbox).is(':checked')) {
            $j('#row_payment_ezdefi_cryptocurrencypayment_acceptable_variation').css('display', 'table-row');
        } else {
            $j('#row_payment_ezdefi_cryptocurrencypayment_acceptable_variation').css('display', 'none');
        }
    }

    function checkPaymentMethodRequire() {
        if( !$j(selectors.simplePaymentCheckbox).is(':checked') && !$j(selectors.ezdefiPaymentCheckbox).is(':checked')) {
            $j(selectors.checkPaymentMethodInput).val('');
        } else {
            $j(selectors.checkPaymentMethodInput).val('1');
        }
    }

    //----------------------------sortable-------------------------

    // ------------------------coin config---------------------------
    $j(document).on("click", "#ezdefi-configuration-add-coin", function () {
        tmp += 1;
        var container = `<tr>
                <td class="ezdefi__currency-td">
                    <select class="ezdefi-select-coin" style="width: 130px" id="select-currency-${tmp}">
                        <option value=""></option>
                    </select>
                    <input type="hidden" class="${selectorToClass(selectors.currencySymbolInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyNameInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyIdInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyDescriptionInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyLogoInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyMaxDecimalInput)}">
                    <input type="hidden" class="${selectorToClass(selectors.currencyOrderByInput)}">
                </td>
                <td class="value">
                    <input type="text" class="ezdefi__config-input ezdefi__table-config-input ${selectorToClass(selectors.currencydiscountInput)} validate-not-negative-number only-float validate-min-max max-100"> 
                    <span>%</span>
                </td>
                <td class="value">
                    <input type="text" class="ezdefi__config-input ezdefi__table-config-input ${selectorToClass(selectors.currencyLifetimeInput)} validate-not-negative-number validate-digits only-positive-integer">
                </td>
                <td class="value"><input type="text" placeholder="Wallet address" class="ezdefi__config-input ezdefi__table-config-input ${selectorToClass(selectors.walletAddressInput)}"></td>
                <td class="value"><input type="text" class="ezdefi__config-input ezdefi__table-config-input ${selectorToClass(selectors.blockConfirmationInput)} validate-not-negative-number validate-digits only-positive-integer"></td>
                <td class="value"><input type="text" 
                    class="ezdefi__config-input ezdefi__table-config-input ${selectorToClass(selectors.currencyDecimalInput)} validate-not-negative-number validate-digits only-positive-integer validate-min-max min-2">
                </td>
                <td>
                    <button class="scalable delete ${selectorToClass(selectors.btnCancelAddCurrency)}" type="button" id="btn-cancel-${tmp}"><span>Delete</span></button>
                </td>
            </tr>`;
        $j("#ezdefi-configuration-coin-table").append(container);
        initCancelAddCurrency("#btn-cancel-"+tmp);
        initSelectCoinConfig("#select-currency-"+tmp);
        $j(".ezdefi-select-coin").on('select2:select', selectCoinListener);
    });

    $j(document).on("click", selectors.btnDeleteCurrency, function () {
        var currencyConfigElement = $j(this).parent().parent();
        var that = $j(this);

        var confirmRemove = confirm("Do you want to remove coin/token?");
        if (confirmRemove === true) {
            var currencyId = that.data('currency-id');
            $j(".ezdefi__list-currency-delete").append('<input type="hidden" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][ids_delete][]" value="'+currencyId+'">');
            currencyConfigElement.remove();
        }
    });

    function initCancelAddCurrency(btnCancel) {
        $j(document).on("click", btnCancel, function () {
            var currencyConfigElement = $j(this).parent().parent();

            var confirmRemove = confirm('Do you want to cancel add this coin?');
            if (confirmRemove === true) {
                currencyConfigElement.remove();
            }
        });
    }

    function initSelectCoinConfig(select) {
        var that = this;
        $j(select).select2({
            ajax: {
                url: '/ezdefi_frontend/gateway/getcoins',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        form_key: window.FORM_KEY
                    };
                },
                processResults: function (data, params) {
                    let response = data;
                    params.page = params.page || 1;
                    return {
                        results: response.data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1,
            templateResult: formatRepo,
            // templateSelection: formatRepoSelection,
            placeholder: "Enter name"
        });
    }

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }
        return `<div class='select2-result-repository clearfix select-coin-box' id="$j{repo.id}">
                <div class='select2-result-repository__meta'>
                    <div class="ezdefi__select-currency--item">
                        <span>
                            <img src="${repo.logo}" alt="" class="ezdefi__select-currency--logo">
                        </span>
                        <span class='select2-result-repository__title text-justify ezdefi__select-currency--name' style="text-transform: uppercase">${repo.symbol}</span>
                    </div>
                </div>
            </div>`;
    };

    function formatRepoSelection(repo) {
        return `<div class='select2-result-repository clearfix select-coin-box' id="$j{repo.id}">
                <div class='select2-result-repository__meta'>
                    <div class="ezdefi__select-currency--item">
                        <span>
                            <img src="$j{repo.logo}" alt="" class="ezdefi__select-currency--logo">
                        </span>
                        <span class='select2-result-repository__title text-justify ezdefi__select-currency--name'>${repo.symbol}</span>
                   </div>
                </div>
            </div>`;
    }

    function selectCoinListener(e) {
        var data = e.params.data;
        var duplicate = false;

        // check duplicate coin
        $j(selectors.currencyIdInput).each(function () {
            if($j(this).val() === data._id) {
                duplicate = true;
            }
        });

        if(!duplicate) {
            let rowElement = e.currentTarget.parentNode.parentNode;

            $j(rowElement).find('.ezdefi__currency-td').append('<p class="ezdefi__currency-symbol"><img src="'+data.logo+'"/><span>'+data.symbol+'</span></p>');
            $j(rowElement).find('.ezdefi-select-coin').remove();
            $j(rowElement).find('.select2').remove();

            let idInput           = $j(rowElement).find(selectors.currencyIdInput);
            let nameInput         = $j(rowElement).find(selectors.currencyNameInput);
            let symbolInput       = $j(rowElement).find(selectors.currencySymbolInput);
            let descriptionInput  = $j(rowElement).find(selectors.currencyDescriptionInput);
            let logoInput         = $j(rowElement).find(selectors.currencyLogoInput);
            let decimal           = $j(rowElement).find(selectors.currencyDecimalInput);
            let discount          = $j(rowElement).find(selectors.currencydiscountInput);
            let paymentLifetime   = $j(rowElement).find(selectors.currencyLifetimeInput);
            let blockConfirmation = $j(rowElement).find(selectors.blockConfirmationInput);
            let maxDecimal        = $j(rowElement).find(selectors.currencyMaxDecimalInput);
            let orderBy           = $j(rowElement).find(selectors.currencyOrderByInput);
            let walletAddress           = $j(rowElement).find(selectors.walletAddressInput);

            decimal.attr('data-validate', '{min:2, max:'+data.decimal+'}')

            idInput          .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][id]');
            nameInput        .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][name]');
            symbolInput      .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][symbol]');
            descriptionInput .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][description]');
            logoInput        .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][logo]');
            decimal          .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][decimal]');
            paymentLifetime  .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][lifetime]');
            blockConfirmation.attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][block_confirmation]');
            discount         .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][discount]');
            maxDecimal       .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][max_decimal]');
            orderBy          .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][order]');
            walletAddress    .attr('name', 'groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['+data._id+'][wallet_address]');

            walletAddress    .addClass('required-entry');
            decimal          .addClass('required-entry');


            idInput          .val(data._id);
            nameInput        .val(data.name);
            symbolInput      .val(data.symbol);
            descriptionInput .val(data.description);
            logoInput        .val(data.logo);
            decimal          .val(data.suggestedDecimal);
            discount         .val(0);
            paymentLifetime  .val(15);
            blockConfirmation.val(1);
            maxDecimal       .val(data.decimal);
            $j(selectors.currencyOrderByInput).each(function(order) {
                $j(this).val(order);
            })

        }
    }

    function selectorToClass(selector) {
        return selector.slice(1);
    }

});

