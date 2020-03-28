var $j = jQuery.noConflict();
$j(function () {
    var validator = new Validation('config_edit_form');

    Validation.updateError = function (validationName, e, error) {
        var validation = Validation.get(validationName),
            advice = Validation.getAdvice(validationName, e);

        validation.error = error;
        if (advice) advice.innerHTML = error;
        return false;
    };

    Validation.add('validate-min-max', null, function (value, element) {
        let validationName = 'validate-min-max';
        let reMin = new RegExp(/^min-[0-9]+$/);
        let reMax = new RegExp(/^max-[0-9]+$/);
        let result = true;
        let message = '';
        let classNames = element.className.split(/\s+/);

        if (Validation.get('IsEmpty').test(value)) {
            return true;
        }

        for (let i = 0; i < classNames.length; i++) {
            if (classNames[i].match(reMin) && result) {
                let testValue = classNames[i].split('-')[1];
                message = 'Please enter a value greater than or equal to ' + testValue + '.';
                result = parseInt(value) >= testValue;
            }

            if (classNames[i].match(reMax) && result) {
                let testValue = classNames[i].split('-')[1];
                message = 'Please enter a value less than or equal to ' + testValue + '.';
                result = parseInt(value) <= testValue;
            }
        }
        ;
        if (!result) {
            return Validation.updateError(validationName, element, message);
        }
        return result;
    });

    Validation.add('validate-payment-method', 'Choose at least one payment method', function (value) {
        return !Validation.get('IsEmpty').test(value);
    });

    Validation.add('validate-api-key', null, function (value) {
        var apiKey = $j('.validate-api-key').val();
        var gatewayApiUrl = $j('.ezdefi__gateway-api-url-input').val();
        var url = '/ezdefi_frontend/gateway/checkApikey?api_key=' + apiKey + '&gateway_api_url=' + gatewayApiUrl;
        var ok = false;

        new Ajax.Request(url, {
            method: 'get',
            asynchronous: false,
            onSuccess: function (response) {
                let validateApiKeyMsg = "This Api Key is invalid";
                let status = eval('(' + response.responseText + ')');
                if (status === false) {
                    Validation.get('validate-api-key').error = validateApiKeyMsg;
                    ok = false;
                } else {
                    ok = true;
                }

            }
        });
        return ok;
    });

    Validation.add('validate-public-key', null, function (value) {
        var publicKey       = $j('.validate-public-key').val();
        var apiKey          = $j('.ezdefi__api-key').val();
        var gatewayApiUrl   = $j('.ezdefi__gateway-api-url-input').val();
        var url             = '/ezdefi_frontend/gateway/checkPublicKey?api_key=' + apiKey + '&gateway_api_url=' + gatewayApiUrl + '&public_key=' + publicKey;
        var ok              = false;

        new Ajax.Request(url, {
            method: 'get',
            asynchronous: false,
            onSuccess: function (response) {
                let validateSiteIdMsg = "This Site Id is invalid";
                let status = eval('(' + response.responseText + ')');
                if (status === false) {
                    Validation.get('validate-public-key').error = validateSiteIdMsg ;
                    ok = false;
                } else {
                    ok = true;
                }

            }
        });

        return ok;

    });
});