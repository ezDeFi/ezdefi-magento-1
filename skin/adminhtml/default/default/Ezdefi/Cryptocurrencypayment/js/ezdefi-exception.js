var $j = jQuery.noConflict();
$j( function() {
    
    $j('.ezdefi__exception-action').click(function (event) {
        console.log($j(this).data('confirm-content'));
        event.preventDefault();
        var confirmContent = $j(this).data('confirm-content');
        var confirmAssign = confirm(confirmContent);
        if (confirmAssign === true) {
            window.location.href = $j(this).prop('href');
        }
    })
    
    $j('.ezdefi__btn-assign-order').click(function () {
        var confirmAssign = confirm("Do you want to assign this order?");
        if (confirmAssign === true) {
            window.location.href = $j(this).data('url-assign') + '?order_id='+ $j(this).data('order-to-assign');
        }
    });

    $j('.ezdefi__select-pending-order').select2({
        ajax: {
            // url: url.build("/admin/exception/getorderpending"),
            url: $j('.ezdefi__select-pending-order').data('url-get-order'),
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
        placeholder: "Enter order"
    });
    $j(".ezdefi__select-pending-order").on('select2:select', selectOrderPendingListener);

    function selectOrderPendingListener(e) {
        var data = e.params.data;
        var buttonAssign = $j(this).parent().find('.ezdefi__btn-assign-order');
        buttonAssign.css('display', 'inline-block');
        buttonAssign.data('order-to-assign', data.id);

    }
    
    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }
        return `<div class='select2-result-repository clearfix' id="order-pending-${repo.id}" style="padding-bottom: 12px;">
                    <div class='select2-result-repository__meta'>
                        <div class='select2-result-repository__title text-justify'>
                            <table class="exception__list-order-pending--table">
                                <tbody>
                                    <tr>
                                        <td>Order id</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${parseInt(repo.increment_id)}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Email</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.customer_email}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Customer</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.customer_firstname + ' ' + repo.customer_lastname}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Price</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${parseFloat(repo.grand_total) +' ' + repo.order_currency_code}</td>
                                    </tr>
                                    <tr>
                                        <td class="exception-order-label-2">Create at</td>
                                        <td class="padding-left-md">:</td>
                                        <td class="exception__order-pending--data">${repo.created_at}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
    };

    function formatRepoSelection (repo) {
        return repo.id ? 'Order: ' + parseInt(repo.increment_id) : 'Choose order to assign';
    };
});